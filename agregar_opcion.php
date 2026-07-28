<?php
session_start();
setcookie('PHPSESSID', $_COOKIE['PHPSESSID'], time()+86400);
if($_SESSION['autentica'] != "SIP" && $_SESSION['autentica'] != "UTI"){
	header("Location: index.php");
	exit();
}
require_once("conexion.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
$valor = isset($_POST['valor']) ? trim($_POST['valor']) : '';

if (empty($tipo) || empty($valor)) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$valor = mysqli_real_escape_string($conecta, $valor);

switch ($tipo) {
    case 'tipo_equipo':
        // Verificar si ya existe
        $check = mysqli_query($conecta, "SELECT * FROM tipos_equipos WHERE tipo = '$valor'");
        if (mysqli_num_rows($check) > 0) {
            echo json_encode(['success' => false, 'message' => 'Este tipo de equipo ya existe']);
            exit;
        }
        $sql = "INSERT INTO tipos_equipos (tipo) VALUES ('$valor')";
        break;
    case 'marca':
        $check = mysqli_query($conecta, "SELECT * FROM marcas WHERE marca = '$valor'");
        if (mysqli_num_rows($check) > 0) {
            echo json_encode(['success' => false, 'message' => 'Esta marca ya existe']);
            exit;
        }
        $sql = "INSERT INTO marcas (marca) VALUES ('$valor')";
        break;
    case 'area':
        $check = mysqli_query($conecta, "SELECT * FROM areas WHERE area = '$valor'");
        if (mysqli_num_rows($check) > 0) {
            echo json_encode(['success' => false, 'message' => 'Esta área ya existe']);
            exit;
        }
        $sql = "INSERT INTO areas (area) VALUES ('$valor')";
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Tipo no válido']);
        exit;
}

if (mysqli_query($conecta, $sql)) {
    echo json_encode(['success' => true, 'message' => 'Opción agregada correctamente', 'valor' => $valor]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al agregar: ' . mysqli_error($conecta)]);
}

mysqli_close($conecta);
?>
