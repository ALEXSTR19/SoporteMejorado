<?php
session_start();
setcookie('PHPSESSID', $_COOKIE['PHPSESSID'], time()+86400);
if($_SESSION['autentica'] != "SIP" && $_SESSION['autentica'] != "UTI"){
	header("Location: index.php");
	exit();
}
require_once("conexion.php");

header('Content-Type: application/json');

$accion = isset($_POST['accion']) ? $_POST['accion'] : (isset($_GET['accion']) ? $_GET['accion'] : '');

switch ($accion) {

    // Subir una o más evidencias (imágenes)
    case 'subir':
        if (!isset($_FILES['evidencias']) || !isset($_POST['folio'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit;
        }

        $folio = mysqli_real_escape_string($conecta, $_POST['folio']);
        $carpeta = "evidencias/";
        $subidas = 0;
        $errores = 0;
        $archivos_subidos = [];

        // Manejar múltiples archivos
        $total = count($_FILES['evidencias']['name']);
        for ($i = 0; $i < $total; $i++) {
            $nombre_original = $_FILES['evidencias']['name'][$i];
            $tipo = $_FILES['evidencias']['type'][$i];
            $ruta_temp = $_FILES['evidencias']['tmp_name'][$i];
            $error = $_FILES['evidencias']['error'][$i];

            if ($error !== UPLOAD_ERR_OK) {
                $errores++;
                continue;
            }

            // Validar que sea imagen
            $tipos_permitidos = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array(strtolower($tipo), $tipos_permitidos)) {
                $errores++;
                continue;
            }

            // Generar nombre único para evitar colisiones
            $extension = pathinfo($nombre_original, PATHINFO_EXTENSION);
            $nombre_unico = uniqid($folio . '_') . '.' . $extension;
            $ruta_destino = $carpeta . $nombre_unico;

            if (move_uploaded_file($ruta_temp, $ruta_destino)) {
                $ruta_db = "evidencias/" . $nombre_unico;
                $sql = "INSERT INTO fotos (folio, foto) VALUES ('$folio', '$ruta_db')";
                if (mysqli_query($conecta, $sql)) {
                    $subidas++;
                    $archivos_subidos[] = [
                        'id' => mysqli_insert_id($conecta),
                        'foto' => $ruta_db,
                        'nombre' => $nombre_original
                    ];
                } else {
                    $errores++;
                    @unlink($ruta_destino);
                }
            } else {
                $errores++;
            }
        }

        // Actualizar estado del soporte a 1 (En proceso) si estaba en 0 (Pendiente)
        // La foto ya no marca el soporte como Realizado (estado 2), la nota es obligatoria.
        if ($subidas > 0) {
            mysqli_query($conecta, "UPDATE soportes SET estado = 1 WHERE folio = '$folio' AND estado = 0");
        }

        $message = $subidas . " imagen(es) subida(s) correctamente";
        if ($errores > 0) {
            $message .= ", $errores con error";
        }

        echo json_encode([
            'success' => $subidas > 0,
            'message' => $message,
            'archivos' => $archivos_subidos,
            'subidas' => $subidas,
            'errores' => $errores
        ]);
        break;

    // Obtener evidencias existentes de un folio
    case 'listar':
        $folio = isset($_GET['folio']) ? mysqli_real_escape_string($conecta, $_GET['folio']) : '';
        if (empty($folio)) {
            echo json_encode(['success' => false, 'message' => 'Folio no proporcionado']);
            exit;
        }

        $sql = "SELECT id, folio, foto FROM fotos WHERE folio = '$folio' ORDER BY id ASC";
        $result = mysqli_query($conecta, $sql);
        $fotos = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $fotos[] = $row;
        }

        // Obtener notas también
        $sql_notas = "SELECT id, folio, nota FROM evidencias WHERE folio = '$folio' ORDER BY id ASC";
        $result_notas = mysqli_query($conecta, $sql_notas);
        $notas = [];
        while ($row = mysqli_fetch_assoc($result_notas)) {
            $notas[] = $row;
        }

        echo json_encode([
            'success' => true,
            'fotos' => $fotos,
            'notas' => $notas
        ]);
        break;

    // Eliminar una evidencia
    case 'eliminar':
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID no válido']);
            exit;
        }

        // Obtener ruta del archivo antes de eliminar
        $sql = "SELECT foto, folio FROM fotos WHERE id = $id";
        $result = mysqli_query($conecta, $sql);
        if ($row = mysqli_fetch_assoc($result)) {
            $ruta = $row['foto'];
            $folio = $row['folio'];

            // Eliminar archivo físico
            if (file_exists($ruta)) {
                @unlink($ruta);
            }

            // Eliminar registro de la BD
            $sql_del = "DELETE FROM fotos WHERE id = $id";
            if (mysqli_query($conecta, $sql_del)) {
                // Verificar si quedan fotos para este folio
                $check = mysqli_query($conecta, "SELECT COUNT(*) as total FROM fotos WHERE folio = '$folio'");
                $count = mysqli_fetch_assoc($check)['total'];

                // Ya no ajustamos el estado a 2 o 1 basado en fotos al eliminar, 
                // ya que las fotos no determinan el estado Finalizado, solo las notas lo hacen.
                // Sin embargo, si eliminamos una foto y no quedan notas, y estaba en 2, debería volver a 1.
                // Pero como borrar foto no afecta a las notas, no es necesario cambiar el estado a 1 aquí,
                // excepto si queremos que al no haber fotos vuelva a 1 (pero la nota es lo que lo hace 2).
                // Así que lo dejamos tal cual o verificamos que las notas mantengan el estado 2.
                $check_notas = mysqli_query($conecta, "SELECT COUNT(*) as total FROM evidencias WHERE folio = '$folio'");
                $count_notas = mysqli_fetch_assoc($check_notas)['total'];
                if ($count_notas == 0) {
                    // Si no hay notas, no puede estar Finalizado
                    mysqli_query($conecta, "UPDATE soportes SET estado = 1 WHERE folio = '$folio' AND estado = 2");
                }

                echo json_encode(['success' => true, 'message' => 'Evidencia eliminada']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Evidencia no encontrada']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}

mysqli_close($conecta);
?>
