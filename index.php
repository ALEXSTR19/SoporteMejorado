<!DOCTYPE html>
<html lang="es">
<head>
	<title>Inicio de Sesión</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/png" href="images/icono.png"/>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">

</head>
<body>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<form class="login100-form validate-form"   action="control.php" method="POST" autocomplete="off" >
					<span class="login100-form-title p-b-26">
						Inicio de Sesión 
					</span>
					<span class="login100-form-title p-b-48">
						<i class="zmdi"><div ><img src="images\logo2.png" width="87" height="122" align="left"></div></i>
					</span>

					<div class="wrap-input100 validate-input">
						<input class="input100" type="text" name="usuario">
						<span class="focus-input100" data-placeholder="Usuario"></span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Enter password">
						<span class="btn-show-pass">
							<i class="zmdi zmdi-eye"></i>
						</span>
						<input class="input100" type="password" name="contrasena">
						<span class="focus-input100" data-placeholder="Contraseña"></span>
					</div>

					<div class="container-login100-form-btn">
						<div class="wrap-login100-form-btn">
							<div class="login100-form-bgbtn"></div>
							<button class="login100-form-btn">
								Entrar
							</button>
						</div>
					</div>

					<div class="text-center p-t-115">
						<span class="txt1">
							Copyright © Todos los Derechos Reservados
						</span>
                            
						<a class="txt2" href="https://tuxpanveracruz.gob.mx">
							Tuxpan, Veracruz.
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
    <script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/main.js"></script>
</body>
</html>