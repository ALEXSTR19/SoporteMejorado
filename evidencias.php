<?php
require_once("conexion.php");

if (isset($_FILES['evidencia']))
{
	$file = $_FILES['evidencia'];
	$nombre = $file['name'];
	$tipo = $file['type'];
	$ruta_provisional = $file['tmp_name'];
	$carpeta = "evidencias/";
	
	if ($tipo !='image/jpg' && $tipo !='image/JPG' && $tipo !='image/jpeg' && $tipo !='image/png' && $tipo !='image/gif')
	{
		$msg = "Error, el archivo no es una imagen.";
	}
	else
	{
		$src = $carpeta.$nombre;
		move_uploaded_file($ruta_provisional, $src);
		$imagen="evidencias/".$nombre;
	}
	
	$sql_query = "insert into fotos(folio,foto) values ('$_POST[folio]','$imagen')";
	if(mysqli_query($conecta,$sql_query))
	{
		mysqli_query($conecta,"UPDATE soportes SET estado = 2 WHERE folio = '$_POST[folio]'");
		$msg="La imagen se agrego correctamente.";
		header("Location: soportesU.php?msg=".urlencode($msg)."&tipo=success");
	}
	else
	{
		$msg="Error, no se agrego la imagen.";
		header("Location: soportesU.php?msg=".urlencode($msg)."&tipo=danger");
	}
}
?>