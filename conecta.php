<?php
$host = 'localhost';
$dbname = 'alozadasolis_soporte';
$username = 'alozadasolis_soporte_user';
$password = 'Cem30dit01.';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}
?>