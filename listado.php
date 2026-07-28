<?php
require_once("seguridad.php");
require_once("conecta.php");
require_once("conexion.php");
//session_start(); // Agregar sesión si no está iniciada

$mensaje = "";
$tipo = "info";
if (isset($_GET['msg']) && !empty($_GET['msg'])) {
    $mensaje = htmlspecialchars($_GET['msg']);
    $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'info';
}

// Parámetros de paginación
$porPagina = 10;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) { 
    $pagina = 1; 
}
$inicio = ($pagina - 1) * $porPagina;

// ------------------------------
// CONTAR TOTAL DE REGISTROS
// ------------------------------
$sql_count = "SELECT COUNT(*) as total FROM soportes";
$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute();
$total_registros = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];

// Calcular total de páginas
$totalPaginas = ceil($total_registros / $porPagina);

// Ajustar página si es mayor al total
if ($pagina > $totalPaginas && $totalPaginas > 0) {
    $pagina = $totalPaginas;
}

// ------------------------------
// OBTENER REGISTROS PAGINADOS
// ------------------------------
$sql = "SELECT * FROM soportes ORDER BY id DESC LIMIT :inicio, :porPagina";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':inicio', (int)$inicio, PDO::PARAM_INT);
$stmt->bindValue(':porPagina', (int)$porPagina, PDO::PARAM_INT);
$stmt->execute();

$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="es" data-bs-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Principal - Listado de Soportes</title>
    <link rel="icon" href="images/icono.png">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/dashboard.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
       /* Color fijo para el botón editar */
.btn-editar {
    background-color: #882e41;
    color: #EAECF9;
    border: 1px solid #ffc107;
}

/* Evitar que desaparezca el fondo al pasar el mouse */
.btn-editar:hover {
    background-color: #b48e5d; 
    color: #000;
    border-color: #d39e00;
}

 /* Color fijo para el botón editar */
.btn-imprimir {
    background-color: #882e41;
    color: #EAECF9;
    border: 1px solid #ffc107;
}

/* Evitar que desaparezca el fondo al pasar el mouse */
.btn-imprimir:hover {
    background-color: #b48e5d; 
    color: #000;
    border-color: #d39e00;
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

.btn-agre{
  background-color: #38761d;
  color: #EAECF9;
  border: 1px solid #ffc107;
}
.btn-agre:hover{
  background-color: #2986cc;
  color: #000;
  border-color: #d39e00;
}
.btn-can{
  background-color: #882e41;
  color: #EAECF9;
  border: 1px solid #ffc107;
}
.btn-can:hover{
  background-color: #b48e5d;
  color: #000;
  border-color: #d39e00;
}
.icon-success { color: green; }
.icon-warning { color: yellow; }
.icon-danger { color: red; }
.evidencias-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; margin-bottom: 15px; }
.evidencia-item { position: relative; border-radius: 6px; overflow: hidden; border: 1px solid #dee2e6; aspect-ratio: 1; }
.evidencia-item img { width: 100%; height: 100%; object-fit: cover; cursor: pointer; transition: transform 0.2s; }
.evidencia-item img:hover { transform: scale(1.05); }
.evidencia-item .btn-eliminar-evi { position: absolute; top: 4px; right: 4px; background: rgba(220,53,69,0.85); color: #fff; border: none; border-radius: 50%; width: 22px; height: 22px; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0; }
.drop-zone { border: 2px dashed #adb5bd; border-radius: 8px; padding: 25px; text-align: center; cursor: pointer; transition: all 0.3s; background: #f8f9fa; color: #6c757d; }
.drop-zone:hover, .drop-zone.dragover { border-color: #882e41; background: #fdf0f3; color: #882e41; }
.drop-zone i { font-size: 2rem; display: block; margin-bottom: 8px; }
.preview-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 8px; margin-top: 10px; }
.preview-item { position: relative; border-radius: 4px; overflow: hidden; border: 1px solid #dee2e6; aspect-ratio: 1; }
.preview-item img { width: 100%; height: 100%; object-fit: cover; }
.preview-item .btn-quitar { position: absolute; top: 2px; right: 2px; background: rgba(0,0,0,0.6); color: #fff; border: none; border-radius: 50%; width: 18px; height: 18px; font-size: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0; }
.notas-list { max-height: 200px; overflow-y: auto; }
.nota-item { background: #f1f3f5; border-radius: 6px; padding: 8px 12px; margin-bottom: 6px; font-size: 0.9rem; border-left: 3px solid #882e41; }
.lightbox-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 9999; display: flex; align-items: center; justify-content: center; cursor: pointer; }
.lightbox-overlay img { max-width: 90%; max-height: 90%; border-radius: 8px; box-shadow: 0 4px 30px rgba(0,0,0,0.5); }
    
      
    
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

        .bordo {
            padding: 60px 30px;
            margin-top: 5px;
            border: 3px solid brown;
            border-radius: 15px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            text-align: left;
            padding: 12px;
            vertical-align: middle;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        thead th {
            background-color: #882e41;
            color: white;
            position: sticky;
            top: 0;
        }
        th{
            background-color:#882e41;
        }

        .letra {
            font-weight: bold;
        }

        .scroll {
            width: 100%;
            overflow-x: auto;
        }
        
        .pagination-container {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        
        .table-responsive {
            min-height: 400px;
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

    <!-- Header y Sidebar (mantener tu código existente) -->
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
            <!-- Sidebar (mantener tu código existente) -->
            <div class="sidebar border border-right col-md-3 col-lg-2 p-0 bg-body-tertiary">
                <!-- ... tu sidebar actual ... -->
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
              <a class="nav-link d-flex align-items-center gap-2" href="altas.php">
                <svg class="bi"><use xlink:href="#file-earmark"/></svg>
                Registro Mttos.
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link d-flex align-items-center gap-2 active" href="listado.php">
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

            <!-- Contenido principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Listado de Soportes del Departamento de TI</h1>
                </div>
				
				<?php if (!empty($mensaje)): ?>
				<div class="alert alert-<?php echo $tipo; ?> alert-dismissible fade show" role="alert">
					<center><?php echo $mensaje; ?></center>
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
				<?php endif; ?>
				
				<div class="d-flex gap-3 justify-content-start flex-wrap mb-3">
					<div class="col-md-2">
						<input type="text" id="busquedaf" class="form-control" placeholder="Buscar por Folio...">
					</div>
					<div class="col-md-3">
						<input type="text" id="busquedaa" class="form-control" placeholder="Buscar por Área...">
					</div>
					<div class="col-md-3">
						<input type="text" id="busquedan" class="form-control" placeholder="Buscar por Nombre...">
					</div>
					<div class="col-md-3">
						<input type="text" id="busquedas" class="form-control" placeholder="Buscar por Asignado...">
					</div>
				</div>

                <!-- Tabla de registros -->
                <div class="table-responsive scroll">
                    <table class="table table-hover table-striped">
                        <thead class="">
                            <tr class="letra">
                                <th>Folio</th>
                                <th>Fecha</th>
                                <th>Tipo de Eq.</th>
                                <th>Num. Inv.</th>
                                <th>Área</th>
								<th>Reportó</th>
								<th>Revisó</th>
								<th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="resultado-tabla">
                            <?php if (count($registros) > 0): ?>
                                <?php foreach ($registros as $dato): ?>
                                    <tr data-folio="<?php echo htmlspecialchars($dato['folio'] ?? $dato['id']); ?>" style="cursor:pointer;">
                                        <td><?php echo htmlspecialchars($dato['folio'] ?? $dato['id']); ?></td>
                                        <td><?php echo htmlspecialchars($dato['fecha'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($dato['tipo_equipo'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($dato['n_inventario'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($dato['area'] ?? ''); ?></td>
										<td><?php echo htmlspecialchars($dato['usuario_reporte'] ?? ''); ?></td>
										<td><?php echo htmlspecialchars($dato['asignado'] ?? ''); ?></td>
										<td><?php
										switch($dato['estado'] ?? 0) {
											case 0: echo '<center><i class="bi bi-exclamation-triangle-fill icon-danger"></i><br>Pendiente</center>'; break;
											case 1: echo '<center><i class="bi bi-exclamation-circle-fill icon-warning"></i><br>En proceso</center>'; break;
											case 2: echo '<center><i class="bi bi-check-square-fill icon-success"></i><br>Finalizado</center>'; break;
											case 3: echo '<center><i class="bi bi-lock-fill"></i><br>Cerrado</center>'; break;
										}
										?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="editar.php?ref=<?php echo htmlspecialchars($dato['id']); ?>" 
                                                   class="btn btn-editar">
                                                    Editar
                                                </a>
                                                <a href="registro.php?ref=<?php echo htmlspecialchars($dato['id']); ?>" 
                                                   class="btn btn-sm btn-imprimir" target="_blank">
                                                    Imprimir
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <div class="alert alert-info mb-0">
                                            No hay registros de soportes disponibles.
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Información de registros -->
                <div class="row mt-3" id="info-registros">
                    <div class="col-md-6">
                        <p class="text-muted">
                            Mostrando registros del 
                            <?php echo ($total_registros === 0) ? 0 : (($pagina - 1) * $porPagina) + 1; ?> 
                            al <?php echo min($pagina * $porPagina, $total_registros); ?> 
                            de un total de <?php echo $total_registros; ?> registros.
                        </p>
                    </div>
                </div>

                <!-- Paginación Bootstrap -->
                <?php if ($totalPaginas > 1): ?>
                <div class="pagination-container" id="paginacion-registros">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <!-- Botón Anterior -->
                            <li class="page-item <?php echo ($pagina <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" 
                                   href="?pagina=<?php echo $pagina - 1; ?>" 
                                   aria-label="Anterior">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            <!-- Números de página -->
                            <?php 
                            // Mostrar páginas cercanas a la actual
                            $inicioPag = max(1, $pagina - 2);
                            $finPag = min($totalPaginas, $pagina + 2);
                            
                            for ($i = $inicioPag; $i <= $finPag; $i++): 
                            ?>
                                <li class="page-item <?php echo ($i == $pagina) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?pagina=<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <!-- Botón Siguiente -->
                            <li class="page-item <?php echo ($pagina >= $totalPaginas) ? 'disabled' : ''; ?>">
                                <a class="page-link" 
                                   href="?pagina=<?php echo $pagina + 1; ?>" 
                                   aria-label="Siguiente">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    
                    <!-- Selector de página -->
                    <div class="row justify-content-center mt-2">
                        
                        </div>
                    </div>
                </div>
                <?php endif; ?>

				<!-- Sección de acciones (inicialmente oculta) -->
				<div id="accionesFila" class="mt-4 d-none">
				<h6 class="mb-3">Seguimiento del equipo con el folio: <strong id="folioSeleccionado">-</strong></h6>
				<div class="alert alert-warning py-2 shadow-sm" role="alert" style="font-size: 0.9rem; border-left: 4px solid #ffc107;">
					<i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2"></i>
					<strong>Importante:</strong> Es <strong>obligatorio</strong> agregar al menos una nota de seguimiento para poder marcar este soporte como Finalizado. La evidencia fotográfica es opcional.
				</div>
				<div class="d-flex gap-3 justify-content-start flex-wrap">
				<button type="button" class="btn btn-primary" id="btnAgregarNota">
				<i class="bi bi-pencil-square"></i> Agregar Notas
				</button>
				<button type="button" class="btn btn-primary" id="btnSubirEvidencia">
				<i class="bi bi-images"></i> Subir Evidencia
				</button>
				</div>
				</div>

            </main>
        </div>
    </div>

<script src="js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación de paginación
    const pageInput = document.querySelector('input[name="pagina"]');
    if (pageInput) {
        pageInput.addEventListener('change', function() {
            if (this.value < 1) this.value = 1;
            if (this.value > <?php echo $totalPaginas; ?>) this.value = <?php echo $totalPaginas; ?>;
        });
    }

    const inputFolio = document.getElementById('busquedaf');
    const inputArea = document.getElementById('busquedaa');
    const inputNombre = document.getElementById('busquedan');
    const inputAsignado = document.getElementById('busquedas');
    const btnLimpiar = document.getElementById('limpiarBusqueda');
    const resultadoTabla = document.getElementById('resultado-tabla');
    const infoRegistros = document.getElementById('info-registros');
    const paginacion = document.getElementById('paginacion-registros');

    const contenidoOriginalTabla = resultadoTabla.innerHTML;

    let temporizador;

    function hayBusquedaActiva() {
        return inputFolio.value.trim() !== '' ||
               inputArea.value.trim() !== '' ||
               inputNombre.value.trim() !== '' ||
               inputAsignado.value.trim() !== '';
    }

    function mostrarModoBusqueda(activo) {
        if (infoRegistros) {
            infoRegistros.style.display = activo ? 'none' : 'flex';
        }

        if (paginacion) {
            paginacion.style.display = activo ? 'none' : 'block';
        }
    }

    function buscarRegistros() {
        const folio = inputFolio.value.trim();
        const area = inputArea.value.trim();
        const nombre = inputNombre.value.trim();
        const asignado = inputAsignado.value.trim();

        if (folio === '' && area === '' && nombre === '' && asignado === '') {
            resultadoTabla.innerHTML = contenidoOriginalTabla;
            mostrarModoBusqueda(false);
            return;
        }

        const formData = new FormData();
        formData.append('folio', folio);
        formData.append('area', area);
        formData.append('nombre', nombre);
        formData.append('asignado', asignado);

        fetch('buscar_listado_ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            resultadoTabla.innerHTML = data;
            mostrarModoBusqueda(true);
        })
        .catch(error => {
            console.error('Error en la búsqueda:', error);
        });
    }

    function buscarConRetraso() {
        clearTimeout(temporizador);
        temporizador = setTimeout(buscarRegistros, 300);
    }

    inputFolio.addEventListener('input', buscarConRetraso);
    inputArea.addEventListener('input', buscarConRetraso);
    inputNombre.addEventListener('input', buscarConRetraso);
    inputAsignado.addEventListener('input', buscarConRetraso);

    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function() {
            inputFolio.value = '';
            inputArea.value = '';
            inputNombre.value = '';
            inputAsignado.value = '';
            resultadoTabla.innerHTML = contenidoOriginalTabla;
            mostrarModoBusqueda(false);
        });
    }
});
</script>
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

<!-- Modal Nota -->
<div class="modal fade" id="modalNota" tabindex="-1" aria-labelledby="modalNotaLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
	<div class="modal-header" style="background-color:#882e41;color:#fff;">
		<h5 class="modal-title" id="modalNotaLabel">Seguimiento del Soporte</h5>
		<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
	</div>
    <form action="notas.php" method="POST">
	<div class="modal-body">
		<input type="hidden" name="folio" id="modalNotaFolio" value="">
		<input type="hidden" name="origen" value="listado">
			<div class="mb-3">
				<label for="observacion" class="form-label">Agregar observaciones</label>
				<textarea class="form-control" id="observacion" name="observacion" rows="4" 
					placeholder="Escribe las observaciones o seguimiento..." required></textarea>
			</div>
	</div>
		<div class="modal-footer">
		<button type="submit" class="btn btn-agre">Agregar</button>
		<button type="button" class="btn btn-can" data-bs-dismiss="modal">Cancelar</button>
	</div>
    </form>
    </div>
</div>
</div>

<!-- Modal Evidencia -->
<div class="modal fade" id="modalEvidencia" tabindex="-1" aria-labelledby="modalEvidenciaLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header" style="background-color:#882e41;color:#fff;">
			<h5 class="modal-title" id="modalEvidenciaLabel"><i class="bi bi-images"></i> Evidencias del Soporte</h5>
		<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
		</div>
		<div class="modal-body">
		<input type="hidden" id="modalEvidenciaFolio" value="">
		<h6 class="mb-2"><i class="bi bi-folder2-open"></i> Evidencias guardadas</h6>
		<div id="evidenciasExistentes" class="evidencias-grid">
			<p class="text-muted small">Selecciona un soporte para ver sus evidencias.</p>
		</div>
		<div id="sinEvidencias" class="text-center text-muted small py-2 d-none">
			<i class="bi bi-inbox"></i> No hay evidencias guardadas para este folio.
		</div>
		<hr>
		<h6 class="mb-2"><i class="bi bi-cloud-arrow-up"></i> Subir nuevas evidencias</h6>
		<div class="drop-zone" id="dropZone">
			<i class="bi bi-cloud-arrow-up"></i>
			<p class="mb-1">Arrastra imágenes aquí o haz clic para seleccionar</p>
			<small>JPG, PNG, GIF, WebP (máx. 5 archivos)</small>
			<input type="file" id="inputEvidencias" accept="image/*" multiple style="display:none;">
		</div>
		<div id="previewContainer" class="preview-grid"></div>
		<div id="evidenciaMsgSubir" class="small mt-2"></div>
		</div>
		<div class="modal-footer">
		<button type="button" class="btn btn-agre" id="btnSubirArchivos" disabled>
			<i class="bi bi-upload"></i> Subir Evidencias
		</button>
		<button type="button" class="btn btn-can" data-bs-dismiss="modal">Cerrar</button>
		</div>
	</div>
	</div>
</div>

<script>
// ---- Row click interaction for notes/evidence ----
document.addEventListener('DOMContentLoaded', function () {
    var tabla = document.getElementById('resultado-tabla');
    var seccionAcciones = document.getElementById('accionesFila');
    var spanFolio = document.getElementById('folioSeleccionado');
    var modalNota = document.getElementById('modalNota');
    var modalEvidencia = document.getElementById('modalEvidencia');
    var modalNotaFolio = document.getElementById('modalNotaFolio');
    var modalEvidenciaFolio = document.getElementById('modalEvidenciaFolio');
    var bsModalNota = modalNota ? new bootstrap.Modal(modalNota) : null;
    var bsModalEvidencia = modalEvidencia ? new bootstrap.Modal(modalEvidencia) : null;

    tabla.addEventListener('click', function(event) {
        var fila = event.target.closest('tr');
        if (!fila || event.target.closest('a, button, input')) return;
        var folio = fila.getAttribute('data-folio');
        if (!folio) return;

        seccionAcciones.classList.remove('d-none');
        spanFolio.textContent = folio;
        if (modalNotaFolio) modalNotaFolio.value = folio;
        if (modalEvidenciaFolio) modalEvidenciaFolio.value = folio;

        if (fila.classList.contains('selected')) {
            seccionAcciones.classList.add('d-none');
            document.querySelectorAll('#resultado-tabla tr.selected').forEach(function(r) { r.classList.remove('selected'); });
            return;
        }
        document.querySelectorAll('#resultado-tabla tr.selected').forEach(function(r) { r.classList.remove('selected'); });
        fila.classList.add('selected');
    });

    document.getElementById('btnAgregarNota')?.addEventListener('click', function () {
        if (bsModalNota) bsModalNota.show();
    });
    document.getElementById('btnSubirEvidencia')?.addEventListener('click', function () {
        if (bsModalEvidencia) {
            var folio = modalEvidenciaFolio.value;
            if (folio) cargarEvidencias(folio);
            bsModalEvidencia.show();
        }
    });
});

// ---- Evidence functions ----
var archivosParaSubir = [];

function cargarEvidencias(folio) {
    var container = document.getElementById('evidenciasExistentes');
    var sinEvi = document.getElementById('sinEvidencias');
    container.innerHTML = '<p class="text-muted small">Cargando...</p>';
    sinEvi.classList.add('d-none');
    fetch('gestionar_evidencias.php?accion=listar&folio=' + encodeURIComponent(folio))
    .then(function(r){ return r.json(); })
    .then(function(data) {
        container.innerHTML = '';
        if (data.success && data.fotos.length > 0) {
            data.fotos.forEach(function(foto) {
                var div = document.createElement('div');
                div.className = 'evidencia-item';
                div.innerHTML = '<img src="' + foto.foto + '" alt="Evidencia" onclick="verImagen(\'' + foto.foto + '\')">' +
                    '<button type="button" class="btn-eliminar-evi" onclick="eliminarEvidencia(' + foto.id + ', \'' + folio + '\')" title="Eliminar"><i class="bi bi-x"></i></button>';
                container.appendChild(div);
            });
        } else {
            sinEvi.classList.remove('d-none');
        }
        if (data.notas && data.notas.length > 0) {
            var notasHtml = '<h6 class="mt-3 mb-2"><i class="bi bi-journal-text"></i> Notas/Diagnóstico</h6><div class="notas-list">';
            data.notas.forEach(function(n) { notasHtml += '<div class="nota-item">' + n.nota.replace(/\n/g, '<br>') + '</div>'; });
            notasHtml += '</div>';
            var notasDiv = document.getElementById('notasExistentesEvi');
            if (!notasDiv) { notasDiv = document.createElement('div'); notasDiv.id = 'notasExistentesEvi'; container.parentNode.insertBefore(notasDiv, container.nextSibling); }
            notasDiv.innerHTML = notasHtml;
        }
    })
    .catch(function() { container.innerHTML = '<p class="text-danger small">Error al cargar evidencias.</p>'; });
}

function eliminarEvidencia(id, folio) {
    if (!confirm('¿Eliminar esta evidencia?')) return;
    var formData = new FormData();
    formData.append('accion', 'eliminar');
    formData.append('id', id);
    fetch('gestionar_evidencias.php', { method: 'POST', body: formData })
    .then(function(r){ return r.json(); })
    .then(function(data) { if (data.success) cargarEvidencias(folio); else alert(data.message); });
}

function verImagen(src) {
    var overlay = document.createElement('div');
    overlay.className = 'lightbox-overlay';
    overlay.innerHTML = '<img src="' + src + '">';
    overlay.addEventListener('click', function() { document.body.removeChild(overlay); });
    document.body.appendChild(overlay);
}

document.addEventListener('DOMContentLoaded', function() {
    var dropZone = document.getElementById('dropZone');
    var inputFile = document.getElementById('inputEvidencias');
    var previewContainer = document.getElementById('previewContainer');
    var btnSubir = document.getElementById('btnSubirArchivos');

    dropZone.addEventListener('click', function() { inputFile.click(); });
    dropZone.addEventListener('dragover', function(e) { e.preventDefault(); dropZone.classList.add('dragover'); });
    dropZone.addEventListener('dragleave', function(e) { e.preventDefault(); dropZone.classList.remove('dragover'); });
    dropZone.addEventListener('drop', function(e) { e.preventDefault(); dropZone.classList.remove('dragover'); agregarArchivos(e.dataTransfer.files); });
    inputFile.addEventListener('change', function() { agregarArchivos(this.files); this.value = ''; });

    function agregarArchivos(files) {
        for (var i = 0; i < files.length; i++) {
            if (archivosParaSubir.length >= 5) { alert('Máximo 5 archivos'); break; }
            if (files[i].size > 15 * 1024 * 1024) { alert('El archivo ' + files[i].name + ' excede 15MB'); continue; }
            if (files[i].type.startsWith('image/')) archivosParaSubir.push(files[i]);
        }
        actualizarPreview();
    }

    function actualizarPreview() {
        previewContainer.innerHTML = '';
        archivosParaSubir.forEach(function(file, index) {
            var div = document.createElement('div'); div.className = 'preview-item';
            var img = document.createElement('img'); img.src = URL.createObjectURL(file); div.appendChild(img);
            var btn = document.createElement('button'); btn.type = 'button'; btn.className = 'btn-quitar'; btn.innerHTML = '<i class="bi bi-x"></i>';
            btn.setAttribute('data-index', index);
            btn.addEventListener('click', function(e) { e.stopPropagation(); archivosParaSubir.splice(parseInt(this.getAttribute('data-index')), 1); actualizarPreview(); });
            div.appendChild(btn); previewContainer.appendChild(div);
        });
        btnSubir.disabled = archivosParaSubir.length === 0;
    }

    btnSubir.addEventListener('click', function() {
        if (archivosParaSubir.length === 0) return;
        var folio = document.getElementById('modalEvidenciaFolio').value;
        var msgDiv = document.getElementById('evidenciaMsgSubir');
        msgDiv.innerHTML = '<span class="text-muted"><i class="bi bi-hourglass-split"></i> Subiendo...</span>';
        btnSubir.disabled = true;
        var formData = new FormData();
        formData.append('accion', 'subir');
        formData.append('folio', folio);
        archivosParaSubir.forEach(function(f) { formData.append('evidencias[]', f); });
        fetch('gestionar_evidencias.php', { method: 'POST', body: formData })
        .then(function(r){ return r.json(); })
        .then(function(data) {
            if (data.success) {
                msgDiv.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> ' + data.message + '</span>';
                archivosParaSubir = []; previewContainer.innerHTML = ''; btnSubir.disabled = true;
                cargarEvidencias(folio);
            } else { msgDiv.innerHTML = '<span class="text-danger">' + data.message + '</span>'; btnSubir.disabled = false; }
        })
        .catch(function() { msgDiv.innerHTML = '<span class="text-danger">Error de conexión</span>'; btnSubir.disabled = false; });
    });
});
</script>

</body>
</html>