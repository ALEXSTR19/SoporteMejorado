<?php require_once("seguridad.php");?>
<?php
require_once("conexion.php");
		$sqlid = "SELECT MAX(id) FROM soportes";
		$res = mysqli_query($conecta,$sqlid);
		while ($idsol=mysqli_fetch_array($res)) {
	     $idfinal=$idsol[0]+1;
		 $hoy = date('dmy');
         $fusion=$hoy . $idfinal;
		 $token = str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789".uniqid()); 
         }
         $utoken_query="SELECT * FROM soportes ORDER BY id ASC LIMIT 1";
		 $utokenres = mysqli_query($conecta,$utoken_query);
         while ($var_token=mysqli_fetch_array($utokenres)) {
		 $utoken=$var_token[15];
		 }
				
//if($utoken!=$_POST['token']){
if (isset($_REQUEST['guardar'])) 
{ 
    // Generar folio único al momento de guardar (no al cargar la página)
    $hoy_guardar = date('dmy');
    $max_query = "SELECT MAX(id) FROM soportes";
    $max_res = mysqli_query($conecta, $max_query);
    $max_row = mysqli_fetch_array($max_res);
    $nuevo_id = $max_row[0] + 1;
    $folio_generado = $hoy_guardar . $nuevo_id;

    // Verificar que el folio no exista, si existe agregar sufijo incremental
    $check = mysqli_query($conecta, "SELECT COUNT(*) as total FROM soportes WHERE folio = '$folio_generado'");
    $existe = mysqli_fetch_assoc($check)['total'];
    if ($existe > 0) {
        $folio_generado = $hoy_guardar . $nuevo_id . rand(10,99);
        $check2 = mysqli_query($conecta, "SELECT COUNT(*) as total FROM soportes WHERE folio = '$folio_generado'");
        while (mysqli_fetch_assoc($check2)['total'] > 0) {
            $folio_generado = $hoy_guardar . $nuevo_id . rand(100,999);
            $check2 = mysqli_query($conecta, "SELECT COUNT(*) as total FROM soportes WHERE folio = '$folio_generado'");
        }
    }

    $fusion = $folio_generado;
    $token_guardar = str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789".uniqid());

    $fechareg = mysqli_real_escape_string($conecta, $_POST['fechareg']);
    $hora = mysqli_real_escape_string($conecta, $_POST['hora']);
    $usuario = mysqli_real_escape_string($conecta, $_POST['usuario']);
    $tipo_equipo = mysqli_real_escape_string($conecta, $_POST['tipo_equipo']);
    $marca = mysqli_real_escape_string($conecta, $_POST['marca']);
    $modelo = mysqli_real_escape_string($conecta, $_POST['modelo']);
    $serie = mysqli_real_escape_string($conecta, $_POST['serie']);
    $n_inventario = mysqli_real_escape_string($conecta, $_POST['n_inventario']);
    $area = mysqli_real_escape_string($conecta, $_POST['area']);
    $tipo_mantto = mysqli_real_escape_string($conecta, $_POST['tipo_mantto']);
    $usuario_equipo = mysqli_real_escape_string($conecta, $_POST['usuario_equipo']);
    $usuario_reporte = mysqli_real_escape_string($conecta, $_POST['usuario_reporte']);
    $movil = mysqli_real_escape_string($conecta, $_POST['movil']);
    $falla = mysqli_real_escape_string($conecta, $_POST['falla']);
    $usuarioa = mysqli_real_escape_string($conecta, $_POST['usuarioa']);

    $sql_query = "insert into soportes(folio,fecha,hora,usuario,tipo_equipo,marca,modelo,serie,n_inventario,area,tipo_mantto,usuario_equipo,usuario_reporte,contacto,falla,asignado,token,estado) values ('$folio_generado','$fechareg','$hora','$usuario','$tipo_equipo','$marca','$modelo','$serie','$n_inventario','$area','$tipo_mantto','$usuario_equipo','$usuario_reporte','$movil','$falla','$usuarioa','$token_guardar','1')";
   if (mysqli_query($conecta,$sql_query)){
	$msg="El registro se agrego correctamente";
	 
    }
  else
     {
	$msg="Error no se agregaron los datos".mysqli_error($conecta);
 }
   }//Final del if del botón Guardar.
//} //Final del if del token.
?>		

<!doctype html>
    <html lang="es" data-bs-theme="auto">
    <head><script src="js/color-modes.js"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="A.L.S">
    <meta name="generator" content="">
    <title>Altas</title>
    
    <link href="css/dashboard.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/icono.png"/>
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

      .b-example-divider {
        width: 100%;
        height: 3rem;
        background-color: rgba(0, 0, 0, .1);
        border: solid rgba(0, 0, 0, .15);
        border-width: 1px 0;
        box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
      }

      .b-example-vr {
        flex-shrink: 0;
        width: 1.5rem;
        height: 100vh;
      }

      .bi {
        vertical-align: -.125em;
        fill: currentColor;
      }

      .nav-scroller {
        position: relative;
        z-index: 2;
        height: 2.75rem;
        overflow-y: hidden;
      }

      .nav-scroller .nav {
        display: flex;
        flex-wrap: nowrap;
        padding-bottom: 1rem;
        margin-top: -1px;
        overflow-x: auto;
        text-align: center;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
      }

      .btn-bd-primary {
        --bd-violet-bg: #712cf9;
        --bd-violet-rgb: 112.520718, 44.062154, 249.437846;

        --bs-btn-font-weight: 600;
        --bs-btn-color: var(--bs-white);
        --bs-btn-bg: var(--bd-violet-bg);
        --bs-btn-border-color: var(--bd-violet-bg);
        --bs-btn-hover-color: var(--bs-white);
        --bs-btn-hover-bg: #6528e0;
        --bs-btn-hover-border-color: #6528e0;
        --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
        --bs-btn-active-color: var(--bs-btn-hover-color);
        --bs-btn-active-bg: #5a23c8;
        --bs-btn-active-border-color: #5a23c8;
      }
      .bd-mode-toggle {
        z-index: 1500;
      }
	  .btn-busq{
      background-color: #882e41;
      color: #EAECF9;
      border: 1px solid #ffc107;
      }
       .btn-busq:hover{
       background-color: #b48e5d; 
       color: #000;
       border-color: #d39e00;
      }
    </style>

    
    <!-- Custom styles for this template -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom styles for this template -->
   <style>
   .letra {
    font-weight: bold;
    }
   .select-with-add {
    display: flex;
    align-items: center;
    gap: 6px;
   }
   .select-with-add select {
    flex: 1;
   }
   .btn-add-option {
    background: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    color: #882e41;
    cursor: pointer;
    padding: 4px 7px;
    font-size: 14px;
    line-height: 1;
    transition: all 0.2s ease;
    flex-shrink: 0;
   }
   .btn-add-option:hover {
    background-color: #882e41;
    color: #fff;
    border-color: #882e41;
   }
   /* Autocomplete */
   .autocomplete-wrapper {
    position: relative;
   }
   .autocomplete-list {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #fff;
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 6px 6px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1050;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    display: none;
   }
   .autocomplete-list .ac-item {
    padding: 8px 12px;
    cursor: pointer;
    font-size: 0.9rem;
    border-bottom: 1px solid #f1f3f5;
   }
   .autocomplete-list .ac-item:hover,
   .autocomplete-list .ac-item.active {
    background-color: #882e41;
    color: #fff;
   }
   .autocomplete-list .ac-item:last-child {
    border-bottom: none;
   }
  </style>
  </head>
  <body>
    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
      <symbol id="check2" viewBox="0 0 16 16">
        <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
      </symbol>
      <symbol id="circle-half" viewBox="0 0 16 16">
        <path d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/>
      </symbol>
      <symbol id="moon-stars-fill" viewBox="0 0 16 16">
        <path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"/>
        <path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z"/>
      </symbol>
      <symbol id="sun-fill" viewBox="0 0 16 16">
        <path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/>
      </symbol>
    </svg>

    
<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
  <symbol id="calendar3" viewBox="0 0 16 16">
    <path d="M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zM1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857V3.857z"/>
    <path d="M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
  </symbol>
  <symbol id="cart" viewBox="0 0 16 16">
    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
  </symbol>
  <symbol id="chevron-right" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
  </symbol>
  <symbol id="door-closed" viewBox="0 0 16 16">
    <path d="M3 2a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v13h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V2zm1 13h8V2H4v13z"/>
    <path d="M9 9a1 1 0 1 0 2 0 1 1 0 0 0-2 0z"/>
  </symbol>
  <symbol id="file-earmark" viewBox="0 0 16 16">
    <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
  </symbol>
  <symbol id="file-earmark-text" viewBox="0 0 16 16">
    <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>
    <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
  </symbol>
  <symbol id="gear-wide-connected" viewBox="0 0 16 16">
    <path d="M7.068.727c.243-.97 1.62-.97 1.864 0l.071.286a.96.96 0 0 0 1.622.434l.205-.211c.695-.719 1.888-.03 1.613.931l-.08.284a.96.96 0 0 0 1.187 1.187l.283-.081c.96-.275 1.65.918.931 1.613l-.211.205a.96.96 0 0 0 .434 1.622l.286.071c.97.243.97 1.62 0 1.864l-.286.071a.96.96 0 0 0-.434 1.622l.211.205c.719.695.03 1.888-.931 1.613l-.284-.08a.96.96 0 0 0-1.187 1.187l.081.283c.275.96-.918 1.65-1.613.931l-.205-.211a.96.96 0 0 0-1.622.434l-.071.286c-.243.97-1.62.97-1.864 0l-.071-.286a.96.96 0 0 0-1.622-.434l-.205.211c-.695.719-1.888.03-1.613-.931l.08-.284a.96.96 0 0 0-1.186-1.187l-.284.081c-.96.275-1.65-.918-.931-1.613l.211-.205a.96.96 0 0 0-.434-1.622l-.286-.071c-.97-.243-.97-1.62 0-1.864l.286-.071a.96.96 0 0 0 .434-1.622l-.211-.205c-.719-.695-.03-1.888.931-1.613l.284.08a.96.96 0 0 0 1.187-1.186l-.081-.284c-.275-.96.918-1.65 1.613-.931l.205.211a.96.96 0 0 0 1.622-.434l.071-.286zM12.973 8.5H8.25l-2.834 3.779A4.998 4.998 0 0 0 12.973 8.5zm0-1a4.998 4.998 0 0 0-7.557-3.779l2.834 3.78h4.723zM5.048 3.967c-.03.021-.058.043-.087.065l.087-.065zm-.431.355A4.984 4.984 0 0 0 3.002 8c0 1.455.622 2.765 1.615 3.678L7.375 8 4.617 4.322zm.344 7.646.087.065-.087-.065z"/>
  </symbol>
  <symbol id="graph-up" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M0 0h1v15h15v1H0V0Zm14.817 3.113a.5.5 0 0 1 .07.704l-4.5 5.5a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61 4.15-5.073a.5.5 0 0 1 .704-.07Z"/>
  </symbol>
  <symbol id="house-fill" viewBox="0 0 16 16">
    <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.707 1.5Z"/>
    <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293l6-6Z"/>
  </symbol>
  <symbol id="list" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
  </symbol>
  <symbol id="people" viewBox="0 0 16 16">
    <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8Zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022ZM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816ZM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0Zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/>
  </symbol>
  <symbol id="plus-circle" viewBox="0 0 16 16">
    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
  </symbol>
  <symbol id="puzzle" viewBox="0 0 16 16">
    <path d="M3.112 3.645A1.5 1.5 0 0 1 4.605 2H7a.5.5 0 0 1 .5.5v.382c0 .696-.497 1.182-.872 1.469a.459.459 0 0 0-.115.118.113.113 0 0 0-.012.025L6.5 4.5v.003l.003.01c.004.01.014.028.036.053a.86.86 0 0 0 .27.194C7.09 4.9 7.51 5 8 5c.492 0 .912-.1 1.19-.24a.86.86 0 0 0 .271-.194.213.213 0 0 0 .039-.063v-.009a.112.112 0 0 0-.012-.025.459.459 0 0 0-.115-.118c-.375-.287-.872-.773-.872-1.469V2.5A.5.5 0 0 1 9 2h2.395a1.5 1.5 0 0 1 1.493 1.645L12.645 6.5h.237c.195 0 .42-.147.675-.48.21-.274.528-.52.943-.52.568 0 .947.447 1.154.862C15.877 6.807 16 7.387 16 8s-.123 1.193-.346 1.638c-.207.415-.586.862-1.154.862-.415 0-.733-.246-.943-.52-.255-.333-.48-.48-.675-.48h-.237l.243 2.855A1.5 1.5 0 0 1 11.395 14H9a.5.5 0 0 1-.5-.5v-.382c0-.696.497-1.182.872-1.469a.459.459 0 0 0 .115-.118.113.113 0 0 0 .012-.025L9.5 11.5v-.003a.214.214 0 0 0-.039-.064.859.859 0 0 0-.27-.193C8.91 11.1 8.49 11 8 11c-.491 0-.912.1-1.19.24a.859.859 0 0 0-.271.194.214.214 0 0 0-.039.063v.003l.001.006a.113.113 0 0 0 .012.025c.016.027.05.068.115.118.375.287.872.773.872 1.469v.382a.5.5 0 0 1-.5.5H4.605a1.5 1.5 0 0 1-1.493-1.645L3.356 9.5h-.238c-.195 0-.42.147-.675.48-.21.274-.528.52-.943.52-.568 0-.947-.447-1.154-.862C.123 9.193 0 8.613 0 8s.123-1.193.346-1.638C.553 5.947.932 5.5 1.5 5.5c.415 0 .733.246.943.52.255.333.48.48.675.48h.238l-.244-2.855zM4.605 3a.5.5 0 0 0-.498.55l.001.007.29 3.4A.5.5 0 0 1 3.9 7.5h-.782c-.696 0-1.182-.497-1.469-.872a.459.459 0 0 0-.118-.115.112.112 0 0 0-.025-.012L1.5 6.5h-.003a.213.213 0 0 0-.064.039.86.86 0 0 0-.193.27C1.1 7.09 1 7.51 1 8c0 .491.1.912.24 1.19.07.14.14.225.194.271a.213.213 0 0 0 .063.039H1.5l.006-.001a.112.112 0 0 0 .025-.012.459.459 0 0 0 .118-.115c.287-.375.773-.872 1.469-.872H3.9a.5.5 0 0 1 .498.542l-.29 3.408a.5.5 0 0 0 .497.55h1.878c-.048-.166-.195-.352-.463-.557-.274-.21-.52-.528-.52-.943 0-.568.447-.947.862-1.154C6.807 10.123 7.387 10 8 10s1.193.123 1.638.346c.415.207.862.586.862 1.154 0 .415-.246.733-.52.943-.268.205-.415.39-.463.557h1.878a.5.5 0 0 0 .498-.55l-.001-.007-.29-3.4A.5.5 0 0 1 12.1 8.5h.782c.696 0 1.182.497 1.469.872.05.065.091.099.118.115.013.008.021.01.025.012a.02.02 0 0 0 .006.001h.003a.214.214 0 0 0 .064-.039.86.86 0 0 0 .193-.27c.14-.28.24-.7.24-1.191 0-.492-.1-.912-.24-1.19a.86.86 0 0 0-.194-.271.215.215 0 0 0-.063-.039H14.5l-.006.001a.113.113 0 0 0-.025.012.459.459 0 0 0-.118.115c-.287.375-.773.872-1.469.872H12.1a.5.5 0 0 1-.498-.543l.29-3.407a.5.5 0 0 0-.497-.55H9.517c.048.166.195.352.463.557.274.21.52.528.52.943 0 .568-.447.947-.862 1.154C9.193 5.877 8.613 6 8 6s-1.193-.123-1.638-.346C5.947 5.447 5.5 5.068 5.5 4.5c0-.415.246-.733.52-.943.268-.205.415-.39.463-.557H4.605z"/>
  </symbol>
  <symbol id="search" viewBox="0 0 16 16">
    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
  </symbol>
  <symbol id="clipboard2" viewBox="0 0 16 16">
    <path d="M3.5 2a.5.5 0 0 0-.5.5v12a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5v-12a.5.5 0 0 0-.5-.5H12a.5.5 0 0 1 0-1h.5A1.5 1.5 0 0 1 14 2.5v12a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 14.5v-12A1.5 1.5 0 0 1 3.5 1H4a.5.5 0 0 1 0 1z"/>
	<path d="M10 .5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5.5.5 0 0 1-.5.5.5.5 0 0 0-.5.5V2a.5.5 0 0 0 .5.5h5A.5.5 0 0 0 11 2v-.5a.5.5 0 0 0-.5-.5.5.5 0 0 1-.5-.5"/>
  </symbol>
</svg>

<header class="navbar sticky-top bg-dark flex-md-nowrap p-0 shadow" data-bs-theme="dark">
  <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6 text-white" href="#"><?php echo $_SESSION['usuarioactual'];?></a>
  <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6 text-white" href="#">SOPORTICS V.1.0 R. 1.1</a>
  <ul class="navbar-nav flex-row d-md-none">
  
    <li class="nav-item text-nowrap">
	
      <button class="nav-link px-3 text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <svg class="bi"><use xlink:href="#list"/></svg>
      </button>
    </li>
  </ul>
</header>

<div class="container-fluid">
  <div class="row">
    <div class="sidebar border border-right col-md-3 col-lg-2 p-0 bg-body-tertiary">
      <div class="offcanvas-md offcanvas-end bg-body-tertiary" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="sidebarMenuLabel">Municipio de Tuxpan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-md-flex flex-column p-0 pt-lg-3 overflow-y-auto">
          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link d-flex align-items-center gap-2" aria-current="page" href="principal.php">
                <svg class="bi"><use xlink:href="#house-fill"/></svg>
                Principal
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link d-flex align-items-center gap-2" href="dashboard.php">
                <svg class="bi"><use xlink:href="#graph-up"/></svg>
                Dashboard
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link d-flex align-items-center gap-2 active" href="altas.php">
                <svg class="bi"><use xlink:href="#file-earmark"/></svg>
                Registro Mttos.
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link d-flex align-items-center gap-2" href="listado.php">
                <svg class="bi"><use xlink:href="#list"/></svg>
                Listado Mttos.
              </a>
            </li>
			<li class="nav-item">
				<a class="nav-link d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#modalBuscar">
				<svg class="bi"><use xlink:href="#search"/></svg>
				Buscar
				</a>
			</li>
			<li class="nav-item">
              <a class="nav-link d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#modalRU">
                <svg class="bi"><use xlink:href="#people"/></svg>
                Registro Usuarios
              </a>
            </li>
			<li class="nav-item">
              <a class="nav-link d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#modalReporte">
                <svg class="bi"><use xlink:href="#file-earmark"/></svg>
                Reporte Mttos.
              </a>
            </li>
			<li class="nav-item">
              <a class="nav-link d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#modalKardex">
                <svg class="bi"><use xlink:href="#clipboard2"/></svg>
                Kardex Equipo
              </a>
            </li>
			<li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="soportesU.php"><svg class="bi"><use xlink:href="#file-earmark-text"/></svg>Soportes Asignados</a></li>
			<li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="dashboardU.php"><svg class="bi"><use xlink:href="#graph-up"/></svg>Dashboard Personal</a></li>
			<li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="seguimientosU.php"><svg class="bi"><use xlink:href="#clipboard2"/></svg>Reporte Seguim.</a></li>
          </ul>
          <hr class="my-3">
          <ul class="nav flex-column mb-auto">
            <li class="nav-item">
              <a class="nav-link d-flex align-items-center gap-2" href="salir.php">
                <svg class="bi"><use xlink:href="#door-closed"/></svg>
                Salir
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 letra">
	
	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
		<h1 class="h2">Registro de Mantenimientos del Departamento de TI</h1>
    </div>
	
 <form class="row g-3" action="altas.php" method="POST" autocomplete="off">
    	  <?php
    	       	if (isset ($msg))
	            {echo "<h4 align='center'>$msg con el folio: $fusion</h4>";}
			    if (isset ($msg2))
	            {echo "<h4 align='center'>$msg2</h4>";}
	          ?> 
     
   <div class="col-md-2">
    <label class="form-label letra">Folio</label>
    <input type="text" value="<?php echo $fusion;?>" class="form-control" name="folio" readonly>
  </div>
  <div class="col-md-2">
    <label class="form-label">Fecha de Registro</label>
    <input type="date" value="<?php echo date("Y-m-d");?>" class="form-control" name="fecha" disabled>
    <input type="hidden" value="<?php echo date("Y-m-d");?>" class="form-control" name="fechareg">
  </div>
   <div class="col-md-2">
    <label class="form-label">Hora de Registro</label>
    <input type="time" value="<?php echo date("H:i:s");?>" class="form-control" name="hora" readonly>
  </div>  
    <div class="col-md-6">
    <label class="form-label">Nombre de Usuario</label>
    <input type="text" value="<?php echo $_SESSION['nombreuser'];?>" class="form-control" name="usuario" readonly>
  </div> 
  <?php
   
	$seleccionado = "";
	if($_SERVER['REQUEST_METHOD']=='POST')
	{
	$seleccionado = $_POST['tipo_equipo'];
	}
	$sql_query = "SELECT * FROM tipos_equipos ORDER BY tipo ASC";
	$result = mysqli_query($conecta,$sql_query);
	?>
  <div class="col-md-3">
     <label class="form-label">Tipo de Equipo</label>
     <div class="select-with-add">
       <select class="form-control select" name="tipo_equipo" id="select_tipo_equipo">
		<option value="">(Tipo Equipo)</option>
		 <?php 
		 while($row = mysqli_fetch_array($result))
			{
			echo"<option value='$row[1]'>$row[1]</option>";
			}
			?>
	   </select>
	   <button type="button" class="btn-add-option" onclick="abrirModalAgregar('tipo_equipo','Tipo de Equipo','select_tipo_equipo')" title="Agregar tipo de equipo"><i class="bi bi-plus-lg"></i></button>
	 </div>
  </div>
 <?php
	$selecciona = "";
	if($_SERVER['REQUEST_METHOD']=='POST')
	{
	$selecciona = $_POST['marca'];
	}
	$sql_marca = "SELECT * FROM marcas ORDER BY marca ASC";
	$resulta = mysqli_query($conecta,$sql_marca);
	?>
  <div class="col-md-3">
     <label class="form-label">Marca</label>
     <div class="select-with-add">
       <select class="form-control select" name="marca" id="select_marca">
		<option value="">(Marca)</option>
		 <?php 
		 while($mar = mysqli_fetch_array($resulta))
			{
			echo"<option value='$mar[1]'>$mar[1]</option>";
			}
			?>
	   </select>
	   <button type="button" class="btn-add-option" onclick="abrirModalAgregar('marca','Marca','select_marca')" title="Agregar marca"><i class="bi bi-plus-lg"></i></button>
	 </div>
  </div>
   <div class="col-md-2">
    <label class="form-label">Modelo</label>
    <input type="text" class="form-control" name="modelo">
  </div>
  <div class="col-md-2">
    <label class="form-label">Serie</label>
    <input type="text" class="form-control" name="serie" >
  </div>
   <div class="col-md-2">
    <label class="form-label">No. Inventario</label>
    <input type="text" class="form-control" name="n_inventario" >
  </div>
  <?php
	$seleccion = "";
	if($_SERVER['REQUEST_METHOD']=='POST')
	{
	$seleccion = $_POST['area'];
	}
	$sql_area = "SELECT * FROM areas ORDER BY area ASC";
	$resultado = mysqli_query($conecta,$sql_area);
	?>
  <div class="col-md-2">
     <label class="form-label">Área</label>
     <div class="select-with-add">
       <select class="form-control select" name="area" id="select_area" required>
		<option value="">(Área)</option>
		 <?php 
		 while($area = mysqli_fetch_array($resultado))
			{
			echo"<option value='$area[1]'>$area[1]</option>";
			}
			 
			?>
	   </select>
	   <button type="button" class="btn-add-option" onclick="abrirModalAgregar('area','Área','select_area')" title="Agregar área"><i class="bi bi-plus-lg"></i></button>
	 </div>
  </div>
   <div class="col-md-2">
     <label class="form-label">Tipo Mantto.</label>
       <select class="form-control select" name="tipo_mantto" required>
		<option value="">(Tipo Mantto.)</option>
		 <option value="Preventivo">Preventivo</option>
         <option value="Correctivo">Correctivo</option>
         <option value="Perfectivo">Perfectivo</option>
		 <option value="Soporte Técnico">Soporte Técnico</option>
	   </select>
  </div>
   <div class="col-md-3">
    <label class="form-label">Usuario de Equipo</label>
    <input type="text" class="form-control" name="usuario_equipo" required>
  </div>
   <div class="col-md-3">
    <label class="form-label">Quien reporta</label>
    <div class="autocomplete-wrapper">
      <input type="text" class="form-control" name="usuario_reporte" id="inputReportante" autocomplete="off" required>
      <div class="autocomplete-list" id="listaReportantes"></div>
    </div>
  </div>
  <div class="col-md-2">
    <label class="form-label">Móvil de Contacto</label>
    <input type="text" class="form-control" name="movil" required>
    </div>
  <div class="col-md-10">
    <label class="form-label">Descripción de la falla</label>
  <textarea name="falla" class="form-control"  rows="4" required></textarea>
  <input type="hidden" name="token"  class="form-control" value="<?php echo $token;?>">
  </div>
      <?php
	$sele = "";
	if($_SERVER['REQUEST_METHOD']=='POST')
	{
	$sele = $_POST['usuarioa'];
	}
	$sql_usuarios = "SELECT * FROM usuarios ORDER BY nombre ASC";
	$resultau = mysqli_query($conecta,$sql_usuarios);
	?>
  <div class="col-md-2">
     <label class="form-label">Se asigna a:</label>
       <select class="form-control select" name="usuarioa" required>
		<option value=""><?php echo $_SESSION['nombreuser'];?></option>
		 <?php 
		 while($usera = mysqli_fetch_array($resultau))
			{
			echo"<option value='$usera[1]'>$usera[1]</option>";
			}
			 
			?>
	   </select>
  </div>
  <div class="col-md-12">
    <button type="submit" class="btn btn-primary" name="guardar" >Guardar</button>
    
  </div>
</form>
    </main>
	
	<div class="modal fade" id="modalBuscar" tabindex="-1" aria-labelledby="modalBuscarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalBuscarLabel">Buscar Soporte</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="buscar.php" method="GET">
        <div class="modal-body">
          <div class="mb-3">
            <label for="termino" class="form-label">Término de búsqueda (Folio, Área o Tipo de Equipo)</label>
            <input type="text" class="form-control" id="termino" name="q" placeholder="Escribe aquí..." required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-busq">Realizar Búsqueda</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalKardex" tabindex="-1" aria-labelledby="modalKardexLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalBuscarLabel">Kardex de Equipo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="kardex.php" method="GET">
        <div class="modal-body">
          <div class="mb-3">
            <label for="termino" class="form-label">Ingresar Num. de Inventario</label>
            <input type="text" class="form-control" id="termino" name="ni" placeholder="Escribe aquí..." required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-busq">Realizar Búsqueda</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalRU" tabindex="-1" aria-labelledby="modalRULabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalRULabel">Alta de Usuarios</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="altasU.php" method="GET">
        <div class="modal-body">
        <div class="mb-3">
			<label class="form-label">Usuario</label>
			<input type="text" class="form-control" name="id" required>
		</div>
		<div class="mb-3">
			<label class="form-label">Nombre</label>
			<input type="text" class="form-control" name="nombre" required>
		</div>
		<div class="mb-3">
			<label class="form-label">Contraseña</label>
			<input type="password" class="form-control" name="clave" required>
		</div>
  
		<div class="mb-3">
			<label class="form-label">Estado del usuario</label>
		<br>
		<div class="form-control">
			<input type="radio" name="estado" value="1" required>Activado
			<br>
			<input type="radio" name="estado" value="0" required>Desactivado
		</div>
		</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-busq" name="registrar">Registrar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalReporte" tabindex="-1" aria-labelledby="modalReporteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalReporteLabel">Reporte de Registro de Soportes</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="Reportar.php" method="GET">
        <div class="modal-body">
		<div class="mb-3">
			<label class="form-label">Inicio del Periodo</label>
			<input type="date" value="" class="form-control" name="fi" required>
		</div>
		<div class="mb-3">
			<label class="form-label">Fin del Periodo</label>
			<input type="date" value="" class="form-control" name="ff" required>
		</div>
		<?php
		$area_query = "select * from areas order by area asc";
		$resultado = mysqli_query($conecta,$area_query);
		?>
		<div class="mb-3">
			<label class="form-label">Área</label>
			<select class="form-control" name="area" required>
		<?php
		while ($area = mysqli_fetch_array($resultado))
		{
			echo "<option value='$area[1]'>$area[1]</option>";
		}
		?>
			<option value="Todas">Todas</option>
			</select>
		</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-busq" name="reporte">Generar Reporte</button>
        </div>
      </form>
    </div>
  </div>
</div>
	
  </div>
</div>
<?php mysqli_close($conecta);?>
<!-- Modal para agregar nueva opción -->
<div class="modal fade" id="modalAgregarOpcion" tabindex="-1" aria-labelledby="modalAgregarOpcionLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#882e41;color:#fff;">
        <h6 class="modal-title" id="modalAgregarOpcionLabel">Agregar</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="agregar_tipo" value="">
        <input type="hidden" id="agregar_select_id" value="">
        <div class="mb-2">
          <label class="form-label" id="agregar_label">Valor</label>
          <input type="text" class="form-control" id="agregar_valor" placeholder="Escribe el nuevo valor..." required>
        </div>
        <div id="agregar_msg" class="small mt-1"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-sm" style="background-color:#882e41;color:#fff;" onclick="guardarOpcion()">Agregar</button>
      </div>
    </div>
  </div>
</div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.2.1/dist/chart.umd.min.js" integrity="sha384-gdQErvCNWvHQZj6XZM0dNsAoY4v+j5P1XDpNkcM3HJG1Yx04ecqIHk7+4VBOCHOG" crossorigin="anonymous"></script><script src="dashboard.js"></script>
<script>
function abrirModalAgregar(tipo, etiqueta, selectId) {
    document.getElementById('agregar_tipo').value = tipo;
    document.getElementById('agregar_select_id').value = selectId;
    document.getElementById('agregar_label').textContent = 'Nuevo(a) ' + etiqueta;
    document.getElementById('agregar_valor').value = '';
    document.getElementById('agregar_msg').innerHTML = '';
    var modal = new bootstrap.Modal(document.getElementById('modalAgregarOpcion'));
    modal.show();
    setTimeout(function(){ document.getElementById('agregar_valor').focus(); }, 500);
}

function guardarOpcion() {
    var tipo = document.getElementById('agregar_tipo').value;
    var valor = document.getElementById('agregar_valor').value.trim();
    var selectId = document.getElementById('agregar_select_id').value;
    var msgDiv = document.getElementById('agregar_msg');

    if (valor === '') {
        msgDiv.innerHTML = '<span class="text-danger">Escribe un valor</span>';
        return;
    }

    msgDiv.innerHTML = '<span class="text-muted">Guardando...</span>';

    var formData = new FormData();
    formData.append('tipo', tipo);
    formData.append('valor', valor);

    fetch('agregar_opcion.php', {
        method: 'POST',
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            var selectElement = document.getElementById(selectId);
            var newOption = document.createElement('option');
            newOption.value = data.valor;
            newOption.textContent = data.valor;
            selectElement.appendChild(newOption);
            selectElement.value = data.valor;

            msgDiv.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> ' + data.message + '</span>';
            setTimeout(function(){
                var modalEl = document.getElementById('modalAgregarOpcion');
                var modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();
            }, 1000);
        } else {
            msgDiv.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-circle"></i> ' + data.message + '</span>';
        }
    })
    .catch(function(error) {
        msgDiv.innerHTML = '<span class="text-danger">Error de conexión</span>';
    });
}

document.getElementById('agregar_valor').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        guardarOpcion();
    }
});

// ---- Autocomplete Quien reporta ----
(function() {
    var input = document.getElementById('inputReportante');
    var lista = document.getElementById('listaReportantes');
    var indiceActivo = -1;
    var timer = null;

    input.addEventListener('input', function() {
        clearTimeout(timer);
        var val = this.value.trim();
        timer = setTimeout(function() {
            if (val.length < 1) {
                lista.style.display = 'none';
                return;
            }
            fetch('buscar_reportante.php?q=' + encodeURIComponent(val))
            .then(function(r){ return r.json(); })
            .then(function(datos) {
                lista.innerHTML = '';
                indiceActivo = -1;
                if (datos.length === 0) {
                    lista.style.display = 'none';
                    return;
                }
                datos.forEach(function(nombre) {
                    var div = document.createElement('div');
                    div.className = 'ac-item';
                    var regex = new RegExp('(' + val.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
                    div.innerHTML = nombre.replace(regex, '<strong>$1</strong>');
                    div.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                        input.value = nombre;
                        lista.style.display = 'none';
                    });
                    lista.appendChild(div);
                });
                lista.style.display = 'block';
            });
        }, 250);
    });

    input.addEventListener('keydown', function(e) {
        var items = lista.querySelectorAll('.ac-item');
        if (items.length === 0) return;
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            indiceActivo = Math.min(indiceActivo + 1, items.length - 1);
            actualizarActivo(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            indiceActivo = Math.max(indiceActivo - 1, 0);
            actualizarActivo(items);
        } else if (e.key === 'Enter' && indiceActivo >= 0) {
            e.preventDefault();
            input.value = items[indiceActivo].textContent;
            lista.style.display = 'none';
            indiceActivo = -1;
        } else if (e.key === 'Escape') {
            lista.style.display = 'none';
        }
    });

    function actualizarActivo(items) {
        items.forEach(function(it, i) {
            it.classList.toggle('active', i === indiceActivo);
        });
        if (items[indiceActivo]) {
            items[indiceActivo].scrollIntoView({ block: 'nearest' });
        }
    }

    input.addEventListener('blur', function() {
        setTimeout(function() { lista.style.display = 'none'; }, 200);
    });

    input.addEventListener('focus', function() {
        if (this.value.trim().length >= 1) {
            this.dispatchEvent(new Event('input'));
        }
    });
})();
</script>
</body>
</html>
