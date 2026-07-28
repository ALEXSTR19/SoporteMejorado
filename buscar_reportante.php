<?php
session_start();
setcookie('PHPSESSID', $_COOKIE['PHPSESSID'], time()+86400);
if($_SESSION['autentica'] != "SIP" && $_SESSION['autentica'] != "UTI"){
    echo json_encode([]);
    exit;
}
require_once("conexion.php");

header('Content-Type: application/json');

$termino = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($termino) < 1) {
    // Sin término, devolver los últimos 15 reportantes distintos
    $sql = "SELECT DISTINCT usuario_reporte FROM soportes WHERE usuario_reporte != '' ORDER BY usuario_reporte ASC LIMIT 15";
} else {
    $termino = mysqli_real_escape_string($conecta, $termino);
    $sql = "SELECT DISTINCT usuario_reporte FROM soportes WHERE usuario_reporte LIKE '%$termino%' ORDER BY usuario_reporte ASC LIMIT 15";
}

$result = mysqli_query($conecta, $sql);
$datos = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $datos[] = $row['usuario_reporte'];
    }
}

echo json_encode($datos);
mysqli_close($conecta);
?>
