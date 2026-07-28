<?php
$token=$_GET['var'];
require_once("conexion.php");
		$consulta = "SELECT nombre,apaterno,amaterno,token FROM juventud WHERE token='$token'";
		$resultado = mysqli_query($conecta,$consulta);
		while ($datos=mysqli_fetch_array($resultado)) {
	      $nombre=$datos['nombre']; 
		  $apaterno=$datos['apaterno'];
		  $amaterno=$datos['amaterno'];
	    }
mysqli_close($conecta);
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="A.L.S" content="">
    <link rel="icon" href="icono.png">

    <title>Identificación</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="bootstrap.min.css" >
<style>
   .letra {
    font-weight: bold;
    }
</style>
</head>
<body>
    <div class="container">
    <div id="signupbox" style="margin-top:50px" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
	<div class="panel panel-info">
		<div class="panel-heading">
		<div class="panel-title">Datos de la persona: <?php echo $nombre." ".$apaterno." ".$amaterno;?></div>
		</div>  
	 <div class="panel-body" >
		<form id="signupform" class="form-horizontal" role="form" action="index.php" method="POST" autocomplete="off">
			<div class="form-group">
			  <?php
	         	if (isset ($msg))
	            {echo "<h4 align='center'>$msg con el folio: $fusion</h4>";}
			    if (isset ($msg2))
	            {echo "<h4 align='center'>$msg2</h4>";}
	          ?>
			<label for="nombre" class="col-md-3 control-label">Nombre:</label>
			<div class="col-md-9">
			<input type="text" class="form-control" name="nombre">
			</div>
			</div>
			<div class="form-group">
			<label for="telefono" class="col-md-3 control-label">Teléfono Móvil</label>
			<div class="col-md-9">
			<input type="tel" class="form-control" name="telefono">
			</div>
			</div>
			<div class="form-group">
			<label for="email" class="col-md-3 control-label">Email</label>
			<div class="col-md-9">
			<input type="email" class="form-control" name="email">
			</div>
			</div>
		   	</form>
	 </div>
	</div>
	</div>     
    </div> <!-- /container -->
  </body>
</html>