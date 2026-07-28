<?php
require_once('seguridad_ambos.php');
require_once('conexion.php');
require_once('inventario_schema.php');

function e($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); }
function cantidadKardex($valor) { return number_format((float)$valor, 2); }

$error = prepararInventario($conecta);
$articuloId = filter_input(INPUT_GET, 'articulo_id', FILTER_VALIDATE_INT);
$desde = isset($_GET['desde']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['desde']) ? $_GET['desde'] : '';
$hasta = isset($_GET['hasta']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['hasta']) ? $_GET['hasta'] : '';
$articulo = null;
$movimientos = array();
$saldoCalculado = 0;

if (!$error && $articuloId) {
    $stmt = mysqli_prepare($conecta, 'SELECT * FROM inventario_articulos WHERE id=? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'i', $articuloId);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $articulo = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);
}
if (!$error && !$articulo) $error = 'El articulo solicitado no existe.';

if (!$error) {
    $sql = "SELECT m.*,s.fecha fecha_salida,s.hora,s.destino,s.motivo,s.responsable,
                   s.observaciones observaciones_salida,s.estado estado_salida
            FROM inventario_movimientos m
            LEFT JOIN inventario_salidas s ON s.folio=m.referencia
            WHERE m.articulo_id=? ORDER BY m.creado_en ASC,m.id ASC";
    $stmt = mysqli_prepare($conecta, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $articuloId);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    while ($movimiento = mysqli_fetch_assoc($resultado)) {
        $saldoCalculado += (float)$movimiento['cantidad'];
        $movimiento['saldo'] = $saldoCalculado;
        $fechaMovimiento = substr($movimiento['creado_en'], 0, 10);
        if (($desde && $fechaMovimiento < $desde) || ($hasta && $fechaMovimiento > $hasta)) continue;
        $movimientos[] = $movimiento;
    }
    mysqli_stmt_close($stmt);
}

$diferencia = $articulo ? (float)$articulo['existencia'] - $saldoCalculado : 0;
?>
<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Kardex de articulo | SOPORTICS</title><link rel="icon" href="images/icono.png"><link href="css/bootstrap.min.css" rel="stylesheet"><link href="css/dashboard.css" rel="stylesheet">
<style>
body{background:#f4f7fb}.hero{background:linear-gradient(125deg,#123c69,#176b87);color:#fff;border-radius:1rem}.panel{border:0;border-radius:1rem;box-shadow:0 5px 20px rgba(21,49,78,.08)}.metric{border-left:4px solid #176b87}.table td,.table th{vertical-align:middle}.detail{min-width:260px}@media print{.no-print{display:none!important}body{background:#fff}.hero{color:#000;background:#fff;padding:0!important}.panel{box-shadow:none}.container-fluid{padding:0!important}}
</style></head><body>
<main class="container-fluid px-lg-4 py-4">
<section class="hero p-4 mb-4 d-md-flex justify-content-between align-items-center"><div><div class="text-uppercase small opacity-75">Seguimiento total de inventario</div><h1 class="h2 mb-1">Kardex del articulo</h1><p class="mb-0 opacity-75">Entradas, salidas, devoluciones y ajustes en orden cronologico.</p></div><div class="no-print mt-3 mt-md-0"><button class="btn btn-light me-2" onclick="window.print()">Imprimir</button><a class="btn btn-outline-light" href="inventario.php">&larr; Inventario</a></div></section>
<?php if ($error): ?><div class="alert alert-danger"><?php echo e($error); ?></div><?php else: ?>
<section class="card panel mb-4"><div class="card-body p-lg-4"><div class="row g-3 align-items-center"><div class="col-md-2"><?php if($articulo['foto']): ?><img src="<?php echo e($articulo['foto']); ?>" alt="Foto del articulo" class="img-fluid rounded" style="max-height:130px"><?php endif; ?></div><div class="col-md-5"><span class="badge bg-secondary mb-2"><?php echo e($articulo['categoria']); ?></span><h2 class="h3 mb-1"><?php echo e($articulo['codigo'].' · '.$articulo['nombre']); ?></h2><div class="text-secondary">Modelo: <?php echo e($articulo['modelo']); ?> · Ubicacion: <?php echo e($articulo['ubicacion'] ?: 'Sin asignar'); ?></div><div class="mt-2">Estado: <strong><?php echo e($articulo['estado']); ?></strong> · Asignado: <strong><?php echo e($articulo['usuario_actual'] ?: 'nadie'); ?></strong></div></div><div class="col-md-5"><div class="row g-2"><div class="col-6"><div class="metric bg-light p-3 rounded"><small class="text-secondary">Existencia actual</small><div class="h4 mb-0"><?php echo cantidadKardex($articulo['existencia']).' '.e($articulo['unidad']); ?></div></div></div><div class="col-6"><div class="metric bg-light p-3 rounded"><small class="text-secondary">Movimientos mostrados</small><div class="h4 mb-0"><?php echo count($movimientos); ?></div></div></div></div></div></div></div></section>
<?php if (abs($diferencia) > 0.00001): ?><div class="alert alert-warning"><strong>Advertencia de conciliacion:</strong> el saldo historico es <?php echo cantidadKardex($saldoCalculado).' '.e($articulo['unidad']); ?> y difiere de la existencia actual por <?php echo cantidadKardex($diferencia); ?>. Esto puede corresponder a ediciones realizadas antes de habilitar el seguimiento total.</div><?php endif; ?>
<form class="card panel mb-4 no-print" method="get"><div class="card-body row g-3 align-items-end"><input type="hidden" name="articulo_id" value="<?php echo (int)$articuloId; ?>"><div class="col-md-4"><label class="form-label">Desde</label><input class="form-control" type="date" name="desde" value="<?php echo e($desde); ?>"></div><div class="col-md-4"><label class="form-label">Hasta</label><input class="form-control" type="date" name="hasta" value="<?php echo e($hasta); ?>"></div><div class="col-md-4"><button class="btn btn-primary">Filtrar periodo</button><a href="inventario_kardex.php?articulo_id=<?php echo (int)$articuloId; ?>" class="btn btn-light">Ver todo</a></div></div></form>
<section class="card panel"><div class="card-header bg-white py-3"><h2 class="h5 mb-0">Historial completo de movimientos</h2></div><div class="table-responsive"><table class="table table-striped table-hover mb-0"><thead><tr><th>Fecha y hora</th><th>Movimiento</th><th>Referencia</th><th>Entrada</th><th>Salida</th><th>Saldo</th><th>Detalle / seguimiento</th><th>Registrado por</th></tr></thead><tbody>
<?php foreach ($movimientos as $m): $entrada=(float)$m['cantidad']>0?(float)$m['cantidad']:0; $salida=(float)$m['cantidad']<0?abs((float)$m['cantidad']):0; ?><tr><td class="text-nowrap"><?php echo e(date('d/m/Y H:i', strtotime($m['creado_en']))); ?></td><td><span class="badge bg-<?php echo $salida?'danger':($m['tipo']==='Ajuste'?'warning text-dark':(in_array($m['tipo'],array('Edicion','Baja'),true)?'secondary':'success')); ?>"><?php echo e($m['tipo']); ?></span></td><td><?php echo e($m['referencia'] ?: 'Sin referencia'); ?></td><td class="text-success fw-bold"><?php echo $entrada?'+'.cantidadKardex($entrada):'—'; ?></td><td class="text-danger fw-bold"><?php echo $salida?'-'.cantidadKardex($salida):'—'; ?></td><td class="fw-bold"><?php echo cantidadKardex($m['saldo']); ?></td><td class="detail"><?php if(!empty($m['detalle'])): ?><div><?php echo e($m['detalle']); ?></div><?php endif; ?><?php if($m['destino']): ?><strong><?php echo e($m['destino']); ?></strong> · <?php echo e($m['motivo']); ?><br><small>Responsable: <?php echo e($m['responsable']); ?> · Estado: <?php echo e($m['estado_salida']); ?><?php if($m['observaciones_salida']): ?><br>Observaciones: <?php echo e($m['observaciones_salida']); ?><?php endif; ?></small><?php elseif(empty($m['detalle'])): ?><span class="text-secondary"><?php echo $m['tipo']==='Alta'?'Alta inicial del articulo':'Movimiento interno de inventario'; ?></span><?php endif; ?></td><td><?php echo e($m['usuario']); ?></td></tr><?php endforeach; ?>
<?php if(!$movimientos): ?><tr><td colspan="8" class="text-center py-5 text-secondary">No hay movimientos en el periodo seleccionado.</td></tr><?php endif; ?></tbody></table></div></section>
<?php endif; ?></main></body></html>
