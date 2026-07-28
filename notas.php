<?php
require_once("conexion.php");

if (isset($_POST['observacion']))
{
	$origen = isset($_POST['origen']) ? $_POST['origen'] : '';
	$redirect_page = ($origen === 'listado') ? 'listado.php' : 'soportesU.php';
	
	$sql_query = "insert into evidencias(folio,nota) values ('$_POST[folio]','$_POST[observacion]')";
	if(mysqli_query($conecta,$sql_query))
	{
		mysqli_query($conecta,"UPDATE soportes SET estado = 2 WHERE folio = '$_POST[folio]'");
		$msg="La nota se agregó correctamente.";
		header("Location: ".$redirect_page."?msg=".urlencode($msg)."&tipo=success");

	}
	else
	{
		$msg="Error, no se agregó la nota.";
		header("Location: ".$redirect_page."?msg=".urlencode($msg)."&tipo=danger");
	}
}
?>