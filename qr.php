<!DOCTYPE html>
<html>
  <head>
    <title>Dirección de Juventud y Recreación</title>
    <link rel="icon" type="image/png" href="images/icono.png"/>
    <style>
body {
  margin: auto;
  width: 600px; 
  padding: 50px;
  font-family: 'Arial', sans-serif; 
  color: #33475b;    
}

/* Centrar Imagen */

 .center {
  height: 600px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 3px solid #dbe4ed; /* Color de bordo */  

}
</style>    
    
  </head>
  <body>
  <?php
  $qrvalor=$_GET['ref'];
  $enlace="https://juventud.tuxpanveracruz.gob.mx/consulta.php?var=";
  ?>
 <div class="center">
 
  	<img src= "https://quickchart.io/qr?size=560&dark=9D2449&text=<?php echo $enlace.$qrvalor?>&centerImageSizeRatio= 0.50&centerImageUrl=https://juventud.tuxpanveracruz.gob.mx/images/logo1.png"/>
  	
  	</div>
  	
  	<center><p><h3>Copyright © ¿Qúe debo poner aquí? </h3></p></center>
  	
  </body>
</html>


