<?php
require_once('conexion.php');

$soportes = mysqli_query($conecta, "
    SELECT s.folio, s.fecha, s.hora, s.falla, s.asignado, u.correo
    FROM soportes s
    INNER JOIN usuarios u ON u.nombre = s.asignado
    WHERE s.estado = 1
      AND u.estado = 1
      AND u.correo <> ''
");

if (!$soportes) {
    echo "Error en consulta: " . mysqli_error($conecta) . PHP_EOL;
    exit(1);
}

$enviados = 0;
$errores = 0;

while ($row = mysqli_fetch_assoc($soportes)) {
    $to = $row['correo'];
    $subject = 'Recordatorio de soporte en proceso: ' . $row['folio'];
    $message = "Hola " . $row['asignado'] . ",\n\n"
        . "Este es un recordatorio automático de que el soporte " . $row['folio'] . " sigue en estado EN PROCESO.\n"
        . "Fecha: " . $row['fecha'] . " " . $row['hora'] . "\n"
        . "Falla reportada: " . $row['falla'] . "\n\n"
        . "Por favor finalízalo en cuanto concluyas la atención.\n\n"
        . "Sistema de Soporte TIC";

    $headers = "From: no-reply@soporte.local\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if (mail($to, $subject, $message, $headers)) {
        $enviados++;
    } else {
        $errores++;
    }
}

echo "Recordatorios enviados: {$enviados}. Errores: {$errores}." . PHP_EOL;
?>
