<?php  
date_default_timezone_set('America/Mexico_City'); mysqli_report(MYSQLI_REPORT_OFF);
$conecta =  mysqli_connect('localhost','alozadasolis_soporte_user','Cem30dit01.','alozadasolis_soporte');
if (!mysqli_set_charset($conecta,'utf8')){
die('No pudo conectarse: ' . mysqli_connect_error());
}
?>