<?php
session_start();
setcookie('PHPSESSID', $_COOKIE['PHPSESSID'], time()+86400);
// Permite acceso tanto al Master (SIP) como a usuarios normales (UTI)
if($_SESSION['autentica'] != "SIP" && $_SESSION['autentica'] != "UTI"){
	header("Location: index.php");
	exit();
}
// Variable auxiliar para saber si es Master
$esMaster = ($_SESSION['autentica'] == "SIP");
?>
