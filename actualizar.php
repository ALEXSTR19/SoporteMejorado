<?php
require_once("conexion.php");
if (isset($_REQUEST['actualizar']))
{ 
    $sql_query = "update soportes set tipo_equipo = '$_POST[tipo_equipo]',marca = '$_POST[marca]',
	modelo = '$_POST[modelo]',serie = '$_POST[serie]',n_inventario = '$_POST[n_inventario]',
	area = '$_POST[area]',tipo_mantto = '$_POST[tipo_mantto]',
	usuario_equipo = '$_POST[usuario_equipo]',usuario_reporte = '$_POST[usuario_reporte]',
	contacto = '$_POST[movil]',falla = '$_POST[falla]',asignado = '$_POST[usuarioa]' 
	where id = '$_POST[edit]'";
   if (mysqli_query($conecta,$sql_query)){
	$msg="El registro se actualizo correctamente";
	header("Location: editar.php?ref='$_POST[edit]'");
    }
  else
     {
	$msg="Error no se agregaron los datos".mysqli_error($conecta);
 }
}
?>