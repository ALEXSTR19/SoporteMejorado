<?php
session_start();
setcookie('PHPSESSID', $_COOKIE['PHPSESSID'], time()+86400);
if($_SESSION['autentica'] != "SIP"){
	header("Location: index.php");
	exit();
}
?>
