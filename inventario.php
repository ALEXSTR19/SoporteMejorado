<?php
require_once('seguridad_ambos.php');
require_once('conexion.php');
require_once('inventario_schema.php');

$error = prepararInventario($conecta);
$mensaje = '';
if (empty($_SESSION['inventario_csrf'])) {
    $_SESSION['inventario_csrf'] = bin2hex(random_bytes(24));
}

function e($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); }
function numero($valor) { return is_numeric($valor) ? round((float)$valor, 2) : 0; }
function ejecutar($db, $sql, $tipos, $valores) {
    $stmt = mysqli_prepare($db, $sql);
    if (!$stmt) return false;
    if ($tipos !== '') mysqli_stmt_bind_param($stmt, $tipos, ...$valores);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}
function registrarMovimiento($db, $articuloId, $tipo, $cantidad, $referencia, $detalle, $usuario) {
    return ejecutar($db, 'INSERT INTO inventario_movimientos (articulo_id,tipo,cantidad,referencia,detalle,usuario) VALUES (?,?,?,?,?,?)', 'isdsss', array($articuloId,$tipo,$cantidad,$referencia,$detalle,$usuario));
}
function detalleEdicionArticulo($anterior, $nuevo, $cambioFoto) {
    $etiquetas = array('nombre'=>'Nombre','modelo'=>'Modelo','categoria'=>'Categoria','unidad'=>'Unidad','existencia'=>'Existencia','minimo'=>'Stock minimo','ubicacion'=>'Ubicacion','descripcion'=>'Descripcion','estado'=>'Estado','observaciones'=>'Observaciones','usuario_anterior'=>'Usuario anterior','usuario_actual'=>'Usuario actual','reutilizable'=>'Retornable');
    $cambios = array();
    foreach ($etiquetas as $campo => $etiqueta) {
        $antes = (string)($anterior[$campo] ?? ''); $despues = (string)($nuevo[$campo] ?? '');
        if (in_array($campo, array('existencia','minimo'), true)) { $antes = number_format((float)$antes, 2, '.', ''); $despues = number_format((float)$despues, 2, '.', ''); }
        if ($antes !== $despues) $cambios[] = $etiqueta . ': "' . ($antes === '' ? 'vacio' : $antes) . '" -> "' . ($despues === '' ? 'vacio' : $despues) . '"';
    }
    if ($cambioFoto) $cambios[] = 'Foto: reemplazada';
    return $cambios ? implode('; ', $cambios) : 'Edicion guardada sin cambios en los datos';
}
function segmentoCodigo($valor, $limite, $soloNumeros = false) {
    $valor = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', (string)$valor);
    $patron = $soloNumeros ? '/[^0-9]/' : '/[^A-Z0-9]/';
    return substr(preg_replace($patron, '', strtoupper($valor)), 0, $limite);
}
function generarCodigoInventario($db, $nombre, $modelo) {
    $prefijo = segmentoCodigo($nombre, 3);
    $numeros = segmentoCodigo($modelo, 4, true);
    if ($prefijo === '') $prefijo = 'ART';
    if ($numeros === '') $numeros = '0000';
    $base = $prefijo . '-' . $numeros;
    $codigo = $base; $consecutivo = 2;
    $stmt = mysqli_prepare($db, 'SELECT 1 FROM inventario_articulos WHERE codigo=? LIMIT 1');
    if (!$stmt) return $codigo;
    do {
        mysqli_stmt_bind_param($stmt, 's', $codigo);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $existe = mysqli_stmt_num_rows($stmt) > 0;
        if ($existe) $codigo = $base . '-' . $consecutivo++;
    } while ($existe);
    mysqli_stmt_close($stmt);
    return $codigo;
}
function guardarFotoInventario($archivo, &$error) {
    if (!isset($archivo['error']) || $archivo['error'] === UPLOAD_ERR_NO_FILE) return '';
    if ($archivo['error'] !== UPLOAD_ERR_OK || $archivo['size'] > 5 * 1024 * 1024) {
        $error = 'La foto no pudo cargarse o supera el limite de 5 MB.'; return '';
    }
    $mime = function_exists('mime_content_type') ? mime_content_type($archivo['tmp_name']) : '';
    $extensiones = array('image/jpeg'=>'jpg', 'image/png'=>'png', 'image/webp'=>'webp');
    if (!isset($extensiones[$mime])) { $error = 'La foto debe ser JPG, PNG o WEBP.'; return ''; }
    $directorio = __DIR__ . '/uploads/inventario';
    if (!is_dir($directorio) && !mkdir($directorio, 0755, true)) { $error = 'No fue posible preparar la carpeta de fotos.'; return ''; }
    $nombre = bin2hex(random_bytes(16)) . '.' . $extensiones[$mime];
    if (!move_uploaded_file($archivo['tmp_name'], $directorio . '/' . $nombre)) { $error = 'No fue posible guardar la foto.'; return ''; }
    return 'uploads/inventario/' . $nombre;
}
function datosArticuloFormulario() {
    return array(
        'nombre' => trim($_POST['nombre'] ?? ''), 'modelo' => trim($_POST['modelo'] ?? ''),
        'categoria' => $_POST['categoria'] ?? 'Material', 'unidad' => trim($_POST['unidad'] ?? 'pieza'),
        'existencia' => numero($_POST['existencia'] ?? 0), 'minimo' => numero($_POST['minimo'] ?? 0),
        'ubicacion' => trim($_POST['ubicacion'] ?? ''), 'descripcion' => trim($_POST['descripcion'] ?? ''),
        'estado' => trim($_POST['estado'] ?? 'Nueva'), 'observaciones' => trim($_POST['observaciones_articulo'] ?? ''),
        'usuario_anterior' => trim($_POST['usuario_anterior'] ?? ''), 'usuario_actual' => trim($_POST['usuario_actual'] ?? ''),
        'reutilizable' => isset($_POST['reutilizable']) ? 1 : 0
    );
}
function datosArticuloValidos($datos) {
    return $datos['nombre'] !== '' && $datos['modelo'] !== '' && $datos['unidad'] !== '' && $datos['existencia'] >= 0 && $datos['minimo'] >= 0
        && in_array($datos['categoria'], array('Herramienta','Material','Refaccion','Equipo','Otro'), true);
}

if (!$error && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['inventario_csrf'], isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
        $error = 'La sesion del formulario vencio. Recargue la pagina e intente nuevamente.';
    } else {
        $accion = isset($_POST['accion']) ? $_POST['accion'] : '';
        $usuario = isset($_SESSION['usuarioactual']) ? $_SESSION['usuarioactual'] : 'sistema';
        if ($accion === 'articulo') {
            $d = datosArticuloFormulario(); extract($d); $usuarioAnterior=$usuario_anterior; $usuarioActual=$usuario_actual;
            if (!datosArticuloValidos($d)) {
                $error = 'Capture nombre, modelo y cantidades validas.';
            } else {
                $codigo = generarCodigoInventario($conecta, $nombre, $modelo);
                $foto = guardarFotoInventario($_FILES['foto'] ?? array(), $error);
            }
            if (!$error) mysqli_begin_transaction($conecta);
            if (!$error && ejecutar($conecta, 'INSERT INTO inventario_articulos (codigo,nombre,modelo,foto,categoria,unidad,existencia,minimo,ubicacion,descripcion,estado,observaciones,usuario_anterior,usuario_actual,reutilizable,creado_por) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', 'ssssssddssssssis', array($codigo,$nombre,$modelo,$foto,$categoria,$unidad,$existencia,$minimo,$ubicacion,$descripcion,$estado,$observaciones,$usuarioAnterior,$usuarioActual,$reutilizable,$usuario))) {
                $id = mysqli_insert_id($conecta);
                if (registrarMovimiento($conecta,$id,'Alta',$existencia,'INICIAL','Alta inicial del articulo',$usuario)) { mysqli_commit($conecta); $mensaje = "Articulo $codigo registrado correctamente."; }
                else { mysqli_rollback($conecta); if ($foto && is_file(__DIR__ . '/' . $foto)) unlink(__DIR__ . '/' . $foto); $error = 'No se pudo registrar el alta en el kardex.'; }
            } elseif (!$error) { mysqli_rollback($conecta); if ($foto && is_file(__DIR__ . '/' . $foto)) unlink(__DIR__ . '/' . $foto); $error = 'No se pudo registrar el articulo.'; }
        } elseif ($accion === 'editar_articulo') {
            $id = (int)($_POST['articulo_id'] ?? 0); $d = datosArticuloFormulario();
            mysqli_begin_transaction($conecta);
            $res = $id ? mysqli_query($conecta, "SELECT * FROM inventario_articulos WHERE id=$id AND activo=1 FOR UPDATE") : false;
            $anterior = $res ? mysqli_fetch_assoc($res) : null;
            if (!$anterior || !datosArticuloValidos($d)) $error = 'Los datos del articulo no son validos.';
            $fotoNueva = '';
            if (!$error) $fotoNueva = guardarFotoInventario($_FILES['foto'] ?? array(), $error);
            if (!$error) {
                $foto = $fotoNueva ?: $anterior['foto'];
                $ok = ejecutar($conecta, 'UPDATE inventario_articulos SET nombre=?,modelo=?,foto=?,categoria=?,unidad=?,existencia=?,minimo=?,ubicacion=?,descripcion=?,estado=?,observaciones=?,usuario_anterior=?,usuario_actual=?,reutilizable=? WHERE id=? AND activo=1', 'sssssddssssssii', array($d['nombre'],$d['modelo'],$foto,$d['categoria'],$d['unidad'],$d['existencia'],$d['minimo'],$d['ubicacion'],$d['descripcion'],$d['estado'],$d['observaciones'],$d['usuario_anterior'],$d['usuario_actual'],$d['reutilizable'],$id));
                $diferencia = $d['existencia'] - (float)$anterior['existencia'];
                $detalle = detalleEdicionArticulo($anterior, $d, $fotoNueva !== '');
                $tipo = abs($diferencia) > 0.00001 ? 'Ajuste' : 'Edicion';
                if ($ok) $ok = registrarMovimiento($conecta,$id,$tipo,$diferencia,'EDICION DE ARTICULO',$detalle,$usuario);
                if ($ok) { mysqli_commit($conecta); if ($fotoNueva && $anterior['foto'] && is_file(__DIR__.'/'.$anterior['foto'])) unlink(__DIR__.'/'.$anterior['foto']); $mensaje = 'Articulo actualizado correctamente.'; }
                else { mysqli_rollback($conecta); if ($fotoNueva && is_file(__DIR__.'/'.$fotoNueva)) unlink(__DIR__.'/'.$fotoNueva); $error = 'No se pudo actualizar el articulo.'; }
            } else {
                mysqli_rollback($conecta);
            }
        } elseif ($accion === 'eliminar_articulo') {
            $id = (int)($_POST['articulo_id'] ?? 0);
            $uso = mysqli_query($conecta, "SELECT 1 FROM inventario_salida_detalle d JOIN inventario_salidas s ON s.id=d.salida_id WHERE d.articulo_id=$id AND s.estado='En curso' AND d.cantidad>d.devuelto LIMIT 1");
            if ($id < 1 || ($uso && mysqli_num_rows($uso))) $error = 'No se puede eliminar un articulo que esta en uso.';
            else {
                mysqli_begin_transaction($conecta);
                $articulo = mysqli_query($conecta, "SELECT existencia FROM inventario_articulos WHERE id=$id AND activo=1 FOR UPDATE");
                $actual = $articulo ? mysqli_fetch_assoc($articulo) : null;
                $ok = $actual && ejecutar($conecta, 'UPDATE inventario_articulos SET activo=0 WHERE id=? AND activo=1', 'i', array($id));
                if ($ok) $ok = registrarMovimiento($conecta,$id,'Baja',0,'BAJA DE ARTICULO','Articulo retirado del inventario; existencia al momento de la baja: '.number_format((float)$actual['existencia'],2,'.',''),$usuario);
                if ($ok) { mysqli_commit($conecta); $mensaje = 'Articulo eliminado del inventario y baja registrada en el kardex.'; }
                else { mysqli_rollback($conecta); $error = 'No se pudo eliminar el articulo.'; }
            }
        } elseif ($accion === 'ajuste') {
            $id = (int)($_POST['articulo_id'] ?? 0); $nueva = numero($_POST['nueva_existencia'] ?? -1);
            mysqli_begin_transaction($conecta);
            $res = mysqli_query($conecta, "SELECT existencia FROM inventario_articulos WHERE id=$id FOR UPDATE");
            $actual = $res ? mysqli_fetch_assoc($res) : null;
            if (!$actual || $nueva < 0) { mysqli_rollback($conecta); $error = 'El ajuste solicitado no es valido.'; }
            else {
                $diferencia = $nueva - (float)$actual['existencia'];
                $ok = ejecutar($conecta, 'UPDATE inventario_articulos SET existencia=? WHERE id=?', 'di', array($nueva,$id));
                $ok = $ok && registrarMovimiento($conecta,$id,'Ajuste',$diferencia,'AJUSTE MANUAL','Existencia: '.number_format((float)$actual['existencia'],2,'.','').' -> '.number_format($nueva,2,'.',''),$usuario);
                if ($ok) { mysqli_commit($conecta); $mensaje = 'Existencia actualizada.'; } else { mysqli_rollback($conecta); $error = 'No se pudo guardar el ajuste.'; }
            }
        } elseif ($accion === 'salida') {
            $fecha = $_POST['fecha'] ?? ''; $hora = $_POST['hora'] ?? ''; $destino = trim($_POST['destino'] ?? '');
            $motivo = trim($_POST['motivo'] ?? ''); $responsable = trim($_POST['responsable'] ?? ''); $obs = trim($_POST['observaciones'] ?? '');
            $ids = $_POST['articulo'] ?? array(); $cantidades = $_POST['cantidad'] ?? array();
            if (!$fecha || !$hora || !$destino || !$motivo || !$responsable || !is_array($ids) || count($ids) < 1) $error = 'Complete los datos de la salida y agregue al menos un articulo.';
            else {
                mysqli_begin_transaction($conecta); $ok = true; $lineas = array();
                foreach ($ids as $i => $articuloId) {
                    $articuloId = (int)$articuloId; $cantidad = numero($cantidades[$i] ?? 0);
                    if ($articuloId < 1 || $cantidad <= 0 || isset($lineas[$articuloId])) { $ok = false; break; }
                    $res = mysqli_query($conecta, "SELECT existencia FROM inventario_articulos WHERE id=$articuloId AND activo=1 FOR UPDATE");
                    $art = $res ? mysqli_fetch_assoc($res) : null;
                    if (!$art || (float)$art['existencia'] < $cantidad) { $ok = false; break; }
                    $lineas[$articuloId] = $cantidad;
                }
                $folio = 'ST-' . date('Ymd-His') . '-' . random_int(10,99);
                if ($ok) $ok = ejecutar($conecta, 'INSERT INTO inventario_salidas (folio,fecha,hora,destino,motivo,responsable,observaciones,creado_por) VALUES (?,?,?,?,?,?,?,?)', 'ssssssss', array($folio,$fecha,$hora,$destino,$motivo,$responsable,$obs,$usuario));
                $salidaId = mysqli_insert_id($conecta);
                foreach ($lineas as $articuloId => $cantidad) {
                    $ok = $ok && ejecutar($conecta, 'INSERT INTO inventario_salida_detalle (salida_id,articulo_id,cantidad) VALUES (?,?,?)', 'iid', array($salidaId,$articuloId,$cantidad));
                    $ok = $ok && ejecutar($conecta, 'UPDATE inventario_articulos SET existencia=existencia-? WHERE id=?', 'di', array($cantidad,$articuloId));
                    $negativa = -$cantidad;
                    $ok = $ok && registrarMovimiento($conecta,$articuloId,'Salida',$negativa,$folio,'Salida a '.$destino.'; responsable: '.$responsable.'; trabajo: '.$motivo,$usuario);
                }
                if ($ok) { mysqli_commit($conecta); $mensaje = "Salida $folio registrada y existencias descontadas."; }
                else { mysqli_rollback($conecta); $error = 'No se registro la salida: revise articulos repetidos, cantidades y existencias disponibles.'; }
            }
        } elseif ($accion === 'cerrar') {
            $salidaId = (int)($_POST['salida_id'] ?? 0); mysqli_begin_transaction($conecta); $ok = true;
            $salidaRes = mysqli_query($conecta, "SELECT folio FROM inventario_salidas WHERE id=$salidaId AND estado='En curso' FOR UPDATE");
            $salida = $salidaRes ? mysqli_fetch_assoc($salidaRes) : null;
            if (!$salida) $ok = false;
            $detalles = $ok ? mysqli_query($conecta, "SELECT d.id,d.articulo_id,d.cantidad,d.devuelto FROM inventario_salida_detalle d JOIN inventario_articulos a ON a.id=d.articulo_id WHERE d.salida_id=$salidaId AND a.reutilizable=1 FOR UPDATE") : false;
            while ($detalles && ($d = mysqli_fetch_assoc($detalles))) {
                $devuelve = max(0, (float)$d['cantidad'] - (float)$d['devuelto']);
                if ($devuelve > 0) {
                    $ok = $ok && ejecutar($conecta, 'UPDATE inventario_articulos SET existencia=existencia+? WHERE id=?', 'di', array($devuelve,$d['articulo_id']));
                    $ok = $ok && ejecutar($conecta, 'UPDATE inventario_salida_detalle SET devuelto=cantidad WHERE id=?', 'i', array($d['id']));
                    $ok = $ok && registrarMovimiento($conecta,$d['articulo_id'],'Devolucion',$devuelve,$salida['folio'],'Devolucion al cerrar la salida de trabajo',$usuario);
                }
            }
            $ok = $ok && ejecutar($conecta, "UPDATE inventario_salidas SET estado='Cerrada' WHERE id=?", 'i', array($salidaId));
            if ($ok) { mysqli_commit($conecta); $mensaje = 'Salida cerrada; las herramientas reutilizables regresaron a existencia.'; } else { mysqli_rollback($conecta); $error = 'No fue posible cerrar la salida.'; }
        }
    }
}

$unidades = array(
    'pieza' => 'Pieza',
    'caja' => 'Caja',
    'paquete' => 'Paquete',
    'juego' => 'Juego',
    'metro' => 'Metro',
    'kilogramo' => 'Kilogramo',
    'litro' => 'Litro',
    'rollo' => 'Rollo'
);
$usuarios = array();
$consultaUsuarios = mysqli_query($conecta, "SELECT nombre FROM usuarios WHERE nombre IS NOT NULL AND nombre <> '' ORDER BY nombre");
if ($consultaUsuarios) while ($u = mysqli_fetch_assoc($consultaUsuarios)) $usuarios[] = $u['nombre'];

$articulos = $error ? false : mysqli_query($conecta, 'SELECT * FROM inventario_articulos WHERE activo=1 ORDER BY nombre');
$catalogo = array(); if ($articulos) while ($a = mysqli_fetch_assoc($articulos)) $catalogo[] = $a;
$usos = array();
$consultaUsos = $error ? false : mysqli_query($conecta, "SELECT d.articulo_id,GROUP_CONCAT(DISTINCT s.responsable ORDER BY s.responsable SEPARATOR ', ') responsables,SUM(d.cantidad-d.devuelto) cantidad FROM inventario_salida_detalle d JOIN inventario_salidas s ON s.id=d.salida_id WHERE s.estado='En curso' AND d.cantidad>d.devuelto GROUP BY d.articulo_id");
if ($consultaUsos) while ($u=mysqli_fetch_assoc($consultaUsos)) $usos[(int)$u['articulo_id']]=$u;
$salidas = $error ? false : mysqli_query($conecta, "SELECT s.*,COUNT(d.id) articulos,SUM(d.cantidad) unidades FROM inventario_salidas s LEFT JOIN inventario_salida_detalle d ON d.salida_id=s.id GROUP BY s.id ORDER BY s.id DESC LIMIT 50");
$total = count($catalogo); $bajo = 0; $disponibles = 0;
foreach ($catalogo as $a) { $disponibles += (float)$a['existencia']; if ((float)$a['existencia'] <= (float)$a['minimo']) $bajo++; }
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Control e inventario | SOPORTICS</title><link rel="icon" href="images/icono.png"><link href="css/bootstrap.min.css" rel="stylesheet"><link href="css/dashboard.css" rel="stylesheet">
<style>
body{background:#f4f7fb}.inventory-hero{background:linear-gradient(125deg,#123c69,#176b87);color:#fff;border-radius:1rem;padding:1.6rem}.metric{border:0;border-radius:1rem;box-shadow:0 5px 20px rgba(21,49,78,.08)}.metric .number{font-size:1.8rem;font-weight:700}.panel{border:0;border-radius:1rem;box-shadow:0 5px 20px rgba(21,49,78,.08)}.stock-low{background:#fff3cd!important}.table td,.table th{vertical-align:middle}.brand-dot{width:10px;height:10px;background:#43d9a3;border-radius:50%;display:inline-block}.nav-pills .nav-link.active{background:#176b87}.item-row{border:1px solid #dee2e6;border-radius:.7rem;padding:.75rem;margin-bottom:.6rem;background:#fff}@media print{.no-print{display:none!important}body{background:#fff}.panel{box-shadow:none}}
</style></head><body>
<header class="navbar bg-dark px-3 py-2 sticky-top"><a class="navbar-brand text-white" href="<?php echo $esMaster?'principal.php':'principalU.php'; ?>"><span class="brand-dot me-2"></span>SOPORTICS</a><div class="text-white"><span class="me-3"><?php echo e($_SESSION['usuarioactual']); ?></span><a href="salir.php" class="btn btn-outline-light btn-sm">Salir</a></div></header>
<main class="container-fluid px-lg-4 py-4">
<div class="inventory-hero mb-4 d-md-flex justify-content-between align-items-center"><div><div class="text-uppercase small opacity-75">Tecnologias de la informacion</div><h1 class="h2 mb-1">Control e inventario</h1><p class="mb-0 opacity-75">Existencias, herramientas y salidas de trabajo en un solo lugar.</p></div><a class="btn btn-light mt-3 mt-md-0 no-print" href="<?php echo $esMaster?'principal.php':'principalU.php'; ?>">&larr; Menu principal</a></div>
<?php if($mensaje): ?><div class="alert alert-success alert-dismissible fade show"><?php echo e($mensaje); ?><button class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if($error): ?><div class="alert alert-danger"><?php echo e($error); ?></div><?php endif; ?>
<div class="row g-3 mb-4"><div class="col-md-4"><div class="card metric"><div class="card-body"><span class="text-secondary">Articulos activos</span><div class="number"><?php echo $total; ?></div></div></div></div><div class="col-md-4"><div class="card metric"><div class="card-body"><span class="text-secondary">Unidades disponibles</span><div class="number"><?php echo number_format($disponibles,2); ?></div></div></div></div><div class="col-md-4"><div class="card metric"><div class="card-body"><span class="text-secondary">En minimo o agotados</span><div class="number text-<?php echo $bajo?'danger':'success'; ?>"><?php echo $bajo; ?></div></div></div></div></div>
<ul class="nav nav-pills gap-2 mb-3 no-print" role="tablist"><li><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#existencias">Existencias</button></li><li><button class="nav-link" data-bs-toggle="pill" data-bs-target="#alta">Nuevo articulo</button></li><li><button class="nav-link" data-bs-toggle="pill" data-bs-target="#nueva-salida">Registrar salida</button></li><li><button class="nav-link" data-bs-toggle="pill" data-bs-target="#historial">Salidas de trabajo</button></li></ul>
<div class="tab-content">
<section class="tab-pane fade show active" id="existencias"><div class="card panel"><div class="card-header bg-white py-3 d-flex justify-content-between"><h2 class="h5 mb-0">Inventario actual</h2><input id="buscar" class="form-control form-control-sm no-print" style="max-width:280px" placeholder="Buscar codigo, modelo, articulo o responsable"></div><div class="table-responsive"><table class="table table-hover mb-0" id="tablaInventario"><thead><tr><th>Foto</th><th>Codigo / articulo</th><th>Modelo</th><th>Ubicacion / estado</th><th>Uso actual</th><th>Disponible</th><th class="no-print">Acciones</th></tr></thead><tbody><?php foreach($catalogo as $a): $uso=$usos[(int)$a['id']]??null; ?><tr class="<?php echo (float)$a['existencia'] <= (float)$a['minimo']?'stock-low':''; ?>"><td><?php if($a['foto']): ?><img src="<?php echo e($a['foto']); ?>" alt="Foto de <?php echo e($a['nombre']); ?>" width="64" height="64" style="object-fit:cover;border-radius:.5rem"><?php else: ?><span class="text-secondary small">Sin foto</span><?php endif; ?></td><td><strong><?php echo e($a['codigo']); ?></strong><br><span><?php echo e($a['nombre']); ?></span><?php if($a['reutilizable']): ?><span class="badge bg-info text-dark ms-1">Retornable</span><?php endif; ?><br><small class="text-secondary"><?php echo e($a['categoria']); ?></small></td><td><?php echo e($a['modelo']); ?></td><td><?php echo e($a['ubicacion'] ?: 'Sin asignar'); ?><br><span class="badge bg-secondary"><?php echo e($a['estado']); ?></span><?php if($a['observaciones']): ?><br><small><?php echo e($a['observaciones']); ?></small><?php endif; ?></td><td><?php if($uso): ?><span class="badge bg-warning text-dark">En uso</span><br><small>Por: <?php echo e($uso['responsables']); ?><br><?php echo number_format($uso['cantidad'],2).' '.e($a['unidad']); ?></small><?php else: ?><span class="badge bg-success">Disponible</span><br><small>Asignado: <?php echo e($a['usuario_actual'] ?: 'nadie'); ?></small><?php endif; ?></td><td><strong><?php echo number_format($a['existencia'],2); ?></strong> <?php echo e($a['unidad']); ?><br><small>Min. <?php echo number_format($a['minimo'],2); ?></small></td><td class="no-print"><div class="d-flex flex-wrap gap-1"><a class="btn btn-outline-dark btn-sm" href="inventario_kardex.php?articulo_id=<?php echo (int)$a['id']; ?>">Kardex</a><button type="button" class="btn btn-outline-primary btn-sm editarArticulo" data-bs-toggle="modal" data-bs-target="#modalEditar" data-articulo="<?php echo e(json_encode($a, JSON_UNESCAPED_UNICODE)); ?>">Editar</button><form method="post" onsubmit="return confirm('¿Eliminar este articulo del inventario?')"><input type="hidden" name="csrf" value="<?php echo e($_SESSION['inventario_csrf']); ?>"><input type="hidden" name="accion" value="eliminar_articulo"><input type="hidden" name="articulo_id" value="<?php echo (int)$a['id']; ?>"><button class="btn btn-outline-danger btn-sm" <?php echo $uso?'disabled title="El articulo esta en uso"':''; ?>>Eliminar</button></form></div></td></tr><?php endforeach; ?><?php if(!$catalogo): ?><tr><td colspan="7" class="text-center py-5 text-secondary">Aun no hay articulos. Use “Nuevo articulo” para comenzar.</td></tr><?php endif; ?></tbody></table></div></div></section>
<section class="tab-pane fade" id="alta"><div class="card panel"><div class="card-body p-lg-4"><h2 class="h4 mb-1">Registrar herramienta o material</h2><p class="text-secondary">El codigo se crea automaticamente con 3 caracteres del nombre y los primeros 4 numeros del modelo.</p><form method="post" enctype="multipart/form-data" class="row g-3"><input type="hidden" name="csrf" value="<?php echo e($_SESSION['inventario_csrf']); ?>"><input type="hidden" name="accion" value="articulo"><div class="col-md-3"><label class="form-label">Codigo automatico</label><input id="codigoVista" class="form-control" value="ART-0000" readonly><div class="form-text">Se agrega un consecutivo si ya existe.</div></div><div class="col-md-6"><label class="form-label">Herramienta / nombre *</label><input id="nombreArticulo" name="nombre" class="form-control" maxlength="150" required></div><div class="col-md-3"><label class="form-label">Modelo *</label><input id="modeloArticulo" name="modelo" class="form-control" maxlength="100" required></div><div class="col-md-3"><label class="form-label">Foto</label><input name="foto" type="file" accept="image/jpeg,image/png,image/webp" class="form-control"><div class="form-text">JPG, PNG o WEBP; maximo 5 MB.</div></div><div class="col-md-3"><label class="form-label">Categoria</label><select name="categoria" class="form-select"><option selected>Herramienta</option><option>Material</option><option>Refaccion</option><option>Equipo</option><option>Otro</option></select></div><div class="col-md-3"><label class="form-label">Estado</label><select name="estado" class="form-select"><option>Nueva</option><option>Buena</option><option>En uso</option><option>En reparacion</option><option>Dañada</option><option>Baja</option></select></div><div class="col-md-3"><label class="form-label">Ubicacion</label><input name="ubicacion" class="form-control" maxlength="120" placeholder="Almacen, estante..."></div><div class="col-md-3"><label class="form-label">Existencia inicial</label><input name="existencia" type="number" min="0" step=".01" value="1" class="form-control" required></div><div class="col-md-3"><label class="form-label">Stock minimo</label><input name="minimo" type="number" min="0" step=".01" value="0" class="form-control" required></div><div class="col-md-3"><label class="form-label">Unidad</label><select name="unidad" class="form-select" required><?php foreach($unidades as $valor=>$etiqueta): ?><option value="<?php echo e($valor); ?>" <?php echo $valor==='pieza'?'selected':''; ?>><?php echo e($etiqueta); ?></option><?php endforeach; ?></select></div><div class="col-md-6"><label class="form-label">Usuario anterior</label><select name="usuario_anterior" class="form-select"><option value="">Sin usuario</option><?php foreach($usuarios as $nombreUsuario): ?><option value="<?php echo e($nombreUsuario); ?>"><?php echo e($nombreUsuario); ?></option><?php endforeach; ?></select></div><div class="col-md-6"><label class="form-label">Usuario actual</label><select name="usuario_actual" class="form-select"><option value="">Sin asignar</option><?php foreach($usuarios as $nombreUsuario): ?><option value="<?php echo e($nombreUsuario); ?>"><?php echo e($nombreUsuario); ?></option><?php endforeach; ?></select></div><div class="col-md-6"><label class="form-label">Descripcion</label><textarea name="descripcion" class="form-control" maxlength="255" rows="2"></textarea></div><div class="col-md-6"><label class="form-label">Observaciones</label><textarea name="observaciones_articulo" class="form-control" maxlength="500" rows="2"></textarea></div><div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="reutilizable" id="retornable" checked><label class="form-check-label" for="retornable">Es herramienta/equipo retornable (regresa al cerrar la salida)</label></div></div><div class="col-12"><button class="btn btn-primary px-4">Guardar articulo</button></div></form></div></div></section>
<section class="tab-pane fade" id="nueva-salida"><div class="card panel"><div class="card-body p-lg-4"><h2 class="h4">Nueva salida de trabajo</h2><p class="text-secondary">Las cantidades se descuentan al registrar. Los articulos retornables vuelven al inventario al cerrar la salida.</p><form method="post" id="formSalida" class="row g-3"><input type="hidden" name="csrf" value="<?php echo e($_SESSION['inventario_csrf']); ?>"><input type="hidden" name="accion" value="salida"><div class="col-md-3"><label class="form-label">Fecha *</label><input type="date" name="fecha" value="<?php echo date('Y-m-d'); ?>" class="form-control" required></div><div class="col-md-3"><label class="form-label">Hora *</label><input type="time" name="hora" value="<?php echo date('H:i'); ?>" class="form-control" required></div><div class="col-md-6"><label class="form-label">Responsable *</label><input name="responsable" class="form-control" maxlength="150" required></div><div class="col-md-6"><label class="form-label">Destino / area *</label><input name="destino" class="form-control" maxlength="180" required></div><div class="col-md-6"><label class="form-label">Trabajo a realizar *</label><input name="motivo" class="form-control" maxlength="255" required></div><div class="col-12"><label class="form-label fw-bold">Materiales y herramientas</label><div id="lineas"></div><button type="button" id="agregarLinea" class="btn btn-outline-primary btn-sm">+ Agregar articulo</button></div><div class="col-12"><label class="form-label">Observaciones</label><textarea name="observaciones" class="form-control" rows="2"></textarea></div><div class="col-12"><button class="btn btn-primary px-4" <?php echo !$catalogo?'disabled':''; ?>>Registrar salida</button></div></form></div></div></section>
<section class="tab-pane fade" id="historial"><div class="card panel"><div class="card-header bg-white py-3"><h2 class="h5 mb-0">Ultimas salidas</h2></div><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Folio / fecha</th><th>Destino y trabajo</th><th>Responsable</th><th>Articulos</th><th>Estado</th><th class="no-print">Accion</th></tr></thead><tbody><?php if($salidas) while($s=mysqli_fetch_assoc($salidas)): ?><tr><td><strong><?php echo e($s['folio']); ?></strong><br><?php echo e($s['fecha'].' '.substr($s['hora'],0,5)); ?></td><td><?php echo e($s['destino']); ?><br><small class="text-secondary"><?php echo e($s['motivo']); ?></small></td><td><?php echo e($s['responsable']); ?></td><td><?php echo (int)$s['articulos']; ?> tipos / <?php echo number_format((float)$s['unidades'],2); ?> uds.</td><td><span class="badge bg-<?php echo $s['estado']==='En curso'?'warning text-dark':'success'; ?>"><?php echo e($s['estado']); ?></span></td><td class="no-print"><?php if($s['estado']==='En curso'): ?><form method="post" onsubmit="return confirm('¿Cerrar salida y devolver las herramientas retornables?')"><input type="hidden" name="csrf" value="<?php echo e($_SESSION['inventario_csrf']); ?>"><input type="hidden" name="accion" value="cerrar"><input type="hidden" name="salida_id" value="<?php echo (int)$s['id']; ?>"><button class="btn btn-outline-success btn-sm">Cerrar y devolver</button></form><?php endif; ?></td></tr><?php endwhile; ?></tbody></table></div></div></section>
</div></main>
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="tituloEditar" aria-hidden="true"><div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content"><form method="post" enctype="multipart/form-data" id="formEditar"><div class="modal-header"><div><h2 class="modal-title h5" id="tituloEditar">Editar articulo</h2><small class="text-secondary" id="codigoEditar"></small></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div><div class="modal-body"><input type="hidden" name="csrf" value="<?php echo e($_SESSION['inventario_csrf']); ?>"><input type="hidden" name="accion" value="editar_articulo"><input type="hidden" name="articulo_id"><div class="row g-3"><div class="col-md-6"><label class="form-label">Nombre *</label><input name="nombre" class="form-control" maxlength="150" required></div><div class="col-md-3"><label class="form-label">Modelo *</label><input name="modelo" class="form-control" maxlength="100" required></div><div class="col-md-3"><label class="form-label">Nueva foto</label><input name="foto" type="file" accept="image/jpeg,image/png,image/webp" class="form-control"><div class="form-text">Dejar vacio para conservarla.</div></div><div class="col-md-3"><label class="form-label">Categoria</label><select name="categoria" class="form-select"><option>Herramienta</option><option>Material</option><option>Refaccion</option><option>Equipo</option><option>Otro</option></select></div><div class="col-md-3"><label class="form-label">Estado fisico</label><select name="estado" class="form-select"><option>Nueva</option><option>Buena</option><option>En uso</option><option>En reparacion</option><option>Dañada</option><option>Baja</option></select></div><div class="col-md-3"><label class="form-label">Ubicacion</label><input name="ubicacion" class="form-control" maxlength="120"></div><div class="col-md-3"><label class="form-label">Unidad</label><select name="unidad" class="form-select" required><?php foreach($unidades as $valor=>$etiqueta): ?><option value="<?php echo e($valor); ?>"><?php echo e($etiqueta); ?></option><?php endforeach; ?></select></div><div class="col-md-3"><label class="form-label">Existencia</label><input name="existencia" type="number" min="0" step=".01" class="form-control" required></div><div class="col-md-3"><label class="form-label">Stock minimo</label><input name="minimo" type="number" min="0" step=".01" class="form-control" required></div><div class="col-md-3"><label class="form-label">Usuario anterior</label><select name="usuario_anterior" class="form-select"><option value="">Sin usuario</option><?php foreach($usuarios as $nombreUsuario): ?><option value="<?php echo e($nombreUsuario); ?>"><?php echo e($nombreUsuario); ?></option><?php endforeach; ?></select></div><div class="col-md-3"><label class="form-label">Usuario asignado</label><select name="usuario_actual" class="form-select"><option value="">Sin asignar</option><?php foreach($usuarios as $nombreUsuario): ?><option value="<?php echo e($nombreUsuario); ?>"><?php echo e($nombreUsuario); ?></option><?php endforeach; ?></select></div><div class="col-md-6"><label class="form-label">Descripcion</label><textarea name="descripcion" class="form-control" maxlength="255" rows="2"></textarea></div><div class="col-md-6"><label class="form-label">Observaciones</label><textarea name="observaciones_articulo" class="form-control" maxlength="500" rows="2"></textarea></div><div class="col-12"><div class="form-check"><input name="reutilizable" class="form-check-input" type="checkbox" id="editarRetornable"><label class="form-check-label" for="editarRetornable">Es retornable</label></div></div></div></div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-primary">Guardar cambios</button></div></form></div></div></div>
<template id="plantillaLinea"><div class="item-row row g-2 align-items-end"><div class="col-md-8"><label class="form-label small">Articulo disponible</label><select name="articulo[]" class="form-select articulo" required><option value="">Seleccione...</option><?php foreach($catalogo as $a): if((float)$a['existencia']<=0) continue; ?><option value="<?php echo (int)$a['id']; ?>" data-stock="<?php echo e($a['existencia']); ?>" data-unidad="<?php echo e($a['unidad']); ?>"><?php echo e($a['codigo'].' · '.$a['nombre'].' ('.$a['existencia'].' '.$a['unidad'].')'); ?></option><?php endforeach; ?></select></div><div class="col-md-3"><label class="form-label small">Cantidad <span class="stockAyuda"></span></label><input name="cantidad[]" type="number" min=".01" step=".01" class="form-control cantidad" required></div><div class="col-md-1"><button type="button" class="btn btn-outline-danger quitar" aria-label="Quitar">&times;</button></div></div></template>
<script src="js/bootstrap.bundle.min.js"></script><script>
const lineas=document.getElementById('lineas'),tpl=document.getElementById('plantillaLinea');
function agregar(){lineas.appendChild(tpl.content.cloneNode(true));}
document.getElementById('agregarLinea').addEventListener('click',agregar); if(tpl.content.querySelectorAll('option').length>1) agregar();
lineas.addEventListener('click',e=>{if(e.target.classList.contains('quitar')&&lineas.children.length>1)e.target.closest('.item-row').remove()});
lineas.addEventListener('change',e=>{if(e.target.classList.contains('articulo')){const o=e.target.selectedOptions[0],fila=e.target.closest('.item-row');fila.querySelector('.cantidad').max=o.dataset.stock||'';fila.querySelector('.stockAyuda').textContent=o.dataset.stock?'(max. '+o.dataset.stock+' '+o.dataset.unidad+')':'';}});
document.getElementById('buscar').addEventListener('input',e=>{const q=e.target.value.toLowerCase();document.querySelectorAll('#tablaInventario tbody tr').forEach(r=>r.hidden=!r.textContent.toLowerCase().includes(q));});
const nombreArticulo=document.getElementById('nombreArticulo'),modeloArticulo=document.getElementById('modeloArticulo'),codigoVista=document.getElementById('codigoVista');
function vistaCodigo(){const letras=(nombreArticulo.value.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toUpperCase().replace(/[^A-Z0-9]/g,'').slice(0,3)||'ART'),numeros=(modeloArticulo.value.replace(/\D/g,'').slice(0,4)||'0000');codigoVista.value=letras+'-'+numeros;}
nombreArticulo.addEventListener('input',vistaCodigo);modeloArticulo.addEventListener('input',vistaCodigo);
document.querySelectorAll('.editarArticulo').forEach(b=>b.addEventListener('click',()=>{const a=JSON.parse(b.dataset.articulo),f=document.getElementById('formEditar');document.getElementById('codigoEditar').textContent=a.codigo;f.elements.articulo_id.value=a.id;['nombre','modelo','categoria','estado','ubicacion','unidad','existencia','minimo','usuario_anterior','usuario_actual','descripcion'].forEach(n=>f.elements[n].value=a[n]??'');f.elements.observaciones_articulo.value=a.observaciones??'';f.elements.reutilizable.checked=Number(a.reutilizable)===1;}));
</script></body></html>
