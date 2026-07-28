<?php
require_once("seguridad_ambos.php");
require_once("conexion.php");
?>
<!doctype html>
<html lang="es" data-bs-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard de Productividad</title>
    <link rel="icon" href="images/icono.png">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/dashboard.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        .bordo { padding: 30px; margin-top: 5px; border: 3px solid #882e41; border-radius: 15px; background: #fff; }
        .kpi-card { border-radius: 10px; padding: 20px; color: white; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.2s;}
        .kpi-card:hover { transform: translateY(-5px); }
        .kpi-primary { background: linear-gradient(45deg, #882e41, #b48e5d); }
        .kpi-success { background: linear-gradient(45deg, #28a745, #20c997); }
        .kpi-warning { background: linear-gradient(45deg, #ffc107, #fd7e14); }
        .kpi-info { background: linear-gradient(45deg, #17a2b8, #007bff); }
        .kpi-value { font-size: 2.5rem; font-weight: bold; }
        .kpi-title { font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; }
        .chart-container { background: #fff; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; border: 1px solid #eee; }
        .btn-busq{ background-color: #882e41; color: #EAECF9; border: 1px solid #ffc107; }
        .btn-busq:hover{ background-color: #b48e5d; color: #000; border-color: #d39e00; }
        thead th { background-color: #882e41; color: white; }
    </style>
</head>
<body class="bg-light">

<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
  <!-- Same SVGs used in other pages... -->
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
  <symbol id="search" viewBox="0 0 16 16">
    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
  </symbol>
  <symbol id="file-earmark" viewBox="0 0 16 16">
    <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
  </symbol>
  <symbol id="clipboard2" viewBox="0 0 16 16">
    <path d="M3.5 2a.5.5 0 0 0-.5.5v12a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5v-12a.5.5 0 0 0-.5-.5H12a.5.5 0 0 1 0-1h.5A1.5 1.5 0 0 1 14 2.5v12a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 14.5v-12A1.5 1.5 0 0 1 3.5 1H4a.5.5 0 0 1 0 1z"/>
    <path d="M10 .5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5.5.5 0 0 1-.5.5.5.5 0 0 0-.5.5V2a.5.5 0 0 0 .5.5h5A.5.5 0 0 0 11 2v-.5a.5.5 0 0 0-.5-.5.5.5 0 0 1-.5-.5"/>
  </symbol>
  <symbol id="door-closed" viewBox="0 0 16 16">
    <path d="M3 2a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v13h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V2zm1 13h8V2H4v13z"/>
    <path d="M9 9a1 1 0 1 0 2 0 1 1 0 0 0-2 0z"/>
  </symbol>
  <symbol id="graph-up" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M0 0h1v15h15v1H0V0Zm14.817 3.113a.5.5 0 0 1 .07.704l-4.5 5.5a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61 4.15-5.073a.5.5 0 0 1 .704-.07Z"/>
  </symbol>
  <symbol id="pencil-square" viewBox="0 0 16 16">
	<path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
	<path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
  </symbol>
  <symbol id="file-earmark-text" viewBox="0 0 16 16">
    <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>
    <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
  </symbol>
  <symbol id="clipboard-check" viewBox="0 0 16 16">
	<path fill-rule="evenodd" d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
	<path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1z"/>
	<path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0z"/>
  </symbol>
</svg>

<header class="navbar sticky-top bg-dark flex-md-nowrap p-0 shadow" data-bs-theme="dark">
  <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6 text-white" href="#"><?php echo $_SESSION['usuarioactual'];?></a>
  <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6 text-white" href="#">SOPORTICS V.1.0 R. 1.1</a>
  <ul class="navbar-nav flex-row d-md-none">
    <li class="nav-item text-nowrap">
      <button class="nav-link px-3 text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
        <svg class="bi"><use xlink:href="#list"/></svg>
      </button>
    </li>
  </ul>
</header>

<div class="container-fluid">
  <div class="row">
    <div class="sidebar border border-right col-md-3 col-lg-2 p-0 bg-body-tertiary">
      <div class="offcanvas-md offcanvas-end bg-body-tertiary" tabindex="-1" id="sidebarMenu">
        <div class="offcanvas-body d-md-flex flex-column p-0 pt-lg-3 overflow-y-auto">
          <ul class="nav flex-column">
<?php if ($esMaster): ?>
            <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="principal.php"><svg class="bi"><use xlink:href="#house-fill"/></svg>Principal</a></li>
            <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="dashboard.php"><svg class="bi"><use xlink:href="#graph-up"/></svg>Dashboard</a></li>
            <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="altas.php"><svg class="bi"><use xlink:href="#file-earmark"/></svg>Registro Mttos.</a></li>
            <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="listado.php"><svg class="bi"><use xlink:href="#list"/></svg>Listado Mttos.</a></li>
            <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#modalBuscar"><svg class="bi"><use xlink:href="#search"/></svg>Buscar</a></li>
            <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#modalRU"><svg class="bi"><use xlink:href="#people"/></svg>Registro Usuarios</a></li>
            <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#modalReporte"><svg class="bi"><use xlink:href="#file-earmark"/></svg>Reporte Mttos.</a></li>
            <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#modalKardex"><svg class="bi"><use xlink:href="#clipboard-check"/></svg>Kardex Equipo</a></li>
            <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="soportesU.php"><svg class="bi"><use xlink:href="#file-earmark-text"/></svg>Soportes Asignados</a></li>
            <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2 active" href="dashboardU.php"><svg class="bi"><use xlink:href="#graph-up"/></svg>Dashboard Personal</a></li>
            <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="seguimientosU.php"><svg class="bi"><use xlink:href="#clipboard-check"/></svg>Reporte Seguim.</a></li>
<?php else: ?>
            <li class="nav-item">
              <a class="nav-link d-flex align-items-center gap-2" aria-current="page" href="principalU.php">
                <svg class="bi"><use xlink:href="#house-fill"/></svg>
                Principal
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link d-flex align-items-center gap-2 active" href="dashboardU.php">
                <svg class="bi"><use xlink:href="#graph-up"/></svg>
                Dashboard Personal
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link d-flex align-items-center gap-2 " href="usuarioaltas.php">
                <svg class="bi"><use xlink:href="#pencil-square"/></svg>
                Registro Mttos.
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link d-flex align-items-center gap-2" href="soportesU.php">
                <svg class="bi"><use xlink:href="#file-earmark-text"/></svg>
                Soportes Asignados.
              </a>
            </li>
			<li class="nav-item">
              <a class="nav-link d-flex align-items-center gap-2" href="seguimientosU.php">
                <svg class="bi"><use xlink:href="#clipboard-check"/></svg>
                Reporte Seguim.
              </a>
            </li>
<?php endif; ?>
            </ul>
          <hr class="my-3">
          <ul class="nav flex-column mb-auto">
            <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="salir.php"><svg class="bi"><use xlink:href="#door-closed"/></svg>Salir</a></li>
          </ul>
        </div>
      </div>
    </div>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pb-4 bg-light">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3 mb-4 border-bottom">
        <h1 class="h3 fw-bold text-dark"><i class="bi bi-graph-up-arrow" style="color: #882e41;"></i> Dashboard de Productividad</h1>
        <div class="btn-toolbar mb-2 mb-md-0 shadow-sm rounded">
          <div class="input-group">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar3 text-muted"></i></span>
            <select class="form-select border-start-0 fw-semibold" id="periodoSelector" onchange="cargarDashboard()" style="color: #882e41; cursor: pointer;">
              <option value="semana">Últimos 7 días</option>
              <option value="mes" selected>Este Mes</option>
              <option value="trimestre">Último Trimestre</option>
              <option value="semestre">Último Semestre</option>
              <option value="anio">Este Año</option>
              <option value="todo">Histórico Completo</option>
            </select>
          </div>
        </div>
      </div>

      <!-- KPIs ROW -->
      <div class="row g-4 mb-4">
        <div class="col-md-3">
          <div class="kpi-card kpi-info h-100 position-relative overflow-hidden" onclick="verDetalles('total')" style="cursor: pointer;" title="Ver folios">
            <div class="position-absolute opacity-25" style="right: -10px; top: -10px; font-size: 5rem;"><i class="bi bi-ticket-detailed"></i></div>
            <div class="kpi-title mb-2"><i class="bi bi-ticket-fill"></i> Total Soportes</div>
            <div class="kpi-value mb-1" id="kpiTotal">0</div>
            <div class="small bg-white bg-opacity-25 rounded px-2 py-1 d-inline-block" id="kpiVariacion">
                <i class="bi bi-dash-circle"></i> 0%
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="kpi-card kpi-success h-100 position-relative overflow-hidden" onclick="verDetalles('resueltos')" style="cursor: pointer;" title="Ver folios">
            <div class="position-absolute opacity-25" style="right: -10px; top: -10px; font-size: 5rem;"><i class="bi bi-check-circle"></i></div>
            <div class="kpi-title mb-2"><i class="bi bi-check2-square"></i> Resueltos</div>
            <div class="kpi-value mb-1" id="kpiResueltos">0</div>
            <div class="small bg-white bg-opacity-25 rounded px-2 py-1 d-inline-block" id="kpiTasa">
                0% Resolución
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="kpi-card kpi-warning h-100 position-relative overflow-hidden" onclick="verDetalles('proceso')" style="cursor: pointer;" title="Ver folios">
            <div class="position-absolute opacity-25" style="right: -10px; top: -10px; font-size: 5rem;"><i class="bi bi-clock-history"></i></div>
            <div class="kpi-title mb-2"><i class="bi bi-hammer"></i> En Proceso</div>
            <div class="kpi-value mb-1" id="kpiProceso">0</div>
            <div class="small bg-white bg-opacity-25 rounded px-2 py-1 d-inline-block">Atención continua</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="kpi-card kpi-primary h-100 position-relative overflow-hidden" onclick="verDetalles('pendientes')" style="cursor: pointer;" title="Ver folios">
            <div class="position-absolute opacity-25" style="right: -10px; top: -10px; font-size: 5rem;"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="kpi-title mb-2"><i class="bi bi-hourglass-split"></i> Pendientes</div>
            <div class="kpi-value mb-1" id="kpiPendientes">0</div>
            <div class="small bg-white bg-opacity-25 rounded px-2 py-1 d-inline-block">Sin asignar o nuevos</div>
          </div>
        </div>
      </div>

      <!-- CHARTS ROW 1 -->
      <div class="row g-4 mb-4">
        <div class="col-lg-8">
          <div class="chart-container h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="m-0 fw-bold text-secondary text-uppercase"><i class="bi bi-graph-up"></i> Tendencia de Soportes</h6>
            </div>
            <div style="position: relative; height:240px; width:100%">
                <canvas id="tendenciaChart"></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="chart-container h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="m-0 fw-bold text-secondary text-uppercase"><i class="bi bi-pie-chart"></i> Estado Actual</h6>
            </div>
            <div style="position: relative; height:220px; width:100%">
                <canvas id="estadoChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- CHARTS ROW 2 -->
      <div class="row g-4 mb-4">
        <div class="col-lg-6">
          <div class="chart-container h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="m-0 fw-bold text-secondary text-uppercase"><i class="bi bi-person-badge"></i> Mi Productividad</h6>
            </div>
            <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
              <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="bg-light sticky-top" style="z-index: 1;">
                  <tr>
                    <th class="border-0 bg-light text-muted">Usuario</th>
                    <th class="border-0 bg-light text-center text-muted">Total</th>
                    <th class="border-0 bg-light text-center text-muted">Resueltos</th>
                    <th class="border-0 bg-light text-center text-muted">Tasa</th>
                    <th class="border-0 bg-light text-muted" style="min-width: 100px;">Progreso</th>
                  </tr>
                </thead>
                <tbody id="tablaUsuarios">
                  <!-- Filled via JS -->
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="chart-container h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="m-0 fw-bold text-secondary text-uppercase"><i class="bi bi-bar-chart-steps"></i> Top 5 Áreas</h6>
            </div>
            <div style="position: relative; height:220px; width:100%">
                <canvas id="areasChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- CHARTS ROW 3 -->
      <div class="row g-4">
        <div class="col-md-4">
          <div class="chart-container h-100">
            <h6 class="m-0 mb-3 fw-bold text-secondary text-uppercase"><i class="bi bi-laptop"></i> Equipos</h6>
            <div style="position: relative; height:200px; width:100%">
                <canvas id="equiposChart"></canvas>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="chart-container h-100">
            <h6 class="m-0 mb-3 fw-bold text-secondary text-uppercase"><i class="bi bi-calendar-day"></i> Por Día de la Semana</h6>
            <div style="position: relative; height:200px; width:100%">
                <canvas id="diasChart"></canvas>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="chart-container h-100">
            <h6 class="m-0 mb-3 fw-bold text-secondary text-uppercase"><i class="bi bi-clock"></i> Por Hora</h6>
            <div style="position: relative; height:200px; width:100%">
                <canvas id="horasChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Modals (Re-used for side menu) -->
<?php
// We can simply include empty modals to prevent errors or paste the modals from principal.php here
?>
<div class="modal fade" id="modalBuscar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Buscar Soporte</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <form action="buscar.php" method="GET">
        <div class="modal-body"><input type="text" class="form-control" name="q" placeholder="Folio, Área..." required></div>
        <div class="modal-footer"><button type="submit" class="btn btn-busq">Buscar</button></div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="modalKardex" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Kardex Equipo</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <form action="kardex.php" method="GET">
        <div class="modal-body"><input type="text" class="form-control" name="ni" placeholder="No. Inventario..." required></div>
        <div class="modal-footer"><button type="submit" class="btn btn-busq">Buscar</button></div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="modalRU" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Alta de Usuarios</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <form action="altasU.php" method="GET">
        <div class="modal-body">
            <input type="text" class="form-control mb-2" name="id" placeholder="Usuario" required>
            <input type="text" class="form-control mb-2" name="nombre" placeholder="Nombre" required>
            <input type="password" class="form-control mb-2" name="clave" placeholder="Contraseña" required>
            <input type="radio" name="estado" value="1" required> Activado <input type="radio" name="estado" value="0" required> Desactivado
        </div>
        <div class="modal-footer"><button type="submit" class="btn btn-busq" name="registrar">Registrar</button></div>
      </form>
    </div>
  </div>
</div>
<?php $area_query = mysqli_query($conecta, "select * from areas order by area asc"); ?>
<div class="modal fade" id="modalReporte" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Reporte Mttos</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <form action="Reportar.php" method="GET">
        <div class="modal-body">
            <input type="date" class="form-control mb-2" name="fi" required>
            <input type="date" class="form-control mb-2" name="ff" required>
            <select class="form-control" name="area" required>
                <?php while ($area = mysqli_fetch_array($area_query)) { echo "<option value='$area[1]'>$area[1]</option>"; } ?>
                <option value="Todas">Todas</option>
            </select>
        </div>
        <div class="modal-footer"><button type="submit" class="btn btn-busq" name="reporte">Generar</button></div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalDetalles" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content shadow">
      <div class="modal-header" style="background-color: #882e41; color: white;">
        <h5 class="modal-title fw-bold" id="modalDetallesLabel"><i class="bi bi-list-ul"></i> Detalle de Folios</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
            <thead class="bg-light sticky-top" style="z-index: 1;">
              <tr>
                <th class="border-0 text-muted ps-3">Folio</th>
                <th class="border-0 text-muted">Fecha</th>
                <th class="border-0 text-muted">Área</th>
                <th class="border-0 text-muted">Asignado</th>
                <th class="border-0 text-muted">Falla</th>
                <th class="border-0 text-muted text-center pr-3">Ver</th>
              </tr>
            </thead>
            <tbody id="tablaDetalles">
              <!-- JS Loading -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
let charts = {};

// Paleta de colores Premium
const colors = {
    primary: '#882e41', secondary: '#b48e5d', success: '#198754', warning: '#ffc107',
    danger: '#dc3545', info: '#0dcaf0', light: '#f8f9fa', dark: '#212529',
    palette: ['#882e41', '#b48e5d', '#002C53', '#007A33', '#F2A900', '#D32F2F', '#1976D2', '#388E3C']
};

document.addEventListener('DOMContentLoaded', cargarDashboard);

function initChart(id, type, data, options) {
    const ctx = document.getElementById(id).getContext('2d');
    if (charts[id]) { charts[id].destroy(); }
    charts[id] = new Chart(ctx, { type, data, options });
}

function animateValue(id, start, end, duration) {
    const obj = document.getElementById(id);
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        obj.innerHTML = Math.floor(progress * (end - start) + start);
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
}

function cargarDashboard() {
    const periodo = document.getElementById('periodoSelector').value;
    fetch(`dashboardU_data.php?periodo=${periodo}`)
        .then(res => res.json())
        .then(data => renderizarDashboard(data))
        .catch(err => console.error("Error cargando dashboard:", err));
}

function verDetalles(estado) {
    const nombres = {
        'total': 'Todos los Soportes',
        'resueltos': 'Soportes Resueltos',
        'proceso': 'Soportes En Proceso',
        'pendientes': 'Soportes Pendientes'
    };
    
    document.getElementById('modalDetallesLabel').innerHTML = `<i class="bi bi-list-ul"></i> ${nombres[estado]} (Cargando...)`;
    document.getElementById('tablaDetalles').innerHTML = `<tr><td colspan="6" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm mr-2" role="status"></div> Cargando datos...</td></tr>`;
    
    const modal = new bootstrap.Modal(document.getElementById('modalDetalles'));
    modal.show();

    const periodo = document.getElementById('periodoSelector').value;
    
    fetch(`dashboardU_data.php?action=detalles&estado=${estado}&periodo=${periodo}`)
        .then(res => res.json())
        .then(resp => {
            if (resp.success) {
                const badgeMap = {
                    0: '<span class="badge bg-danger">Pendiente</span>',
                    1: '<span class="badge bg-warning text-dark">En Proceso</span>',
                    2: '<span class="badge bg-success">Finalizado</span>',
                    3: '<span class="badge bg-primary">Cerrado</span>'
                };
                document.getElementById('modalDetallesLabel').innerHTML = `<i class="bi bi-list-ul"></i> ${nombres[estado]} <span class="badge bg-secondary ms-2">${resp.data.length} folios</span>`;
                
                let html = '';
                if(resp.data.length === 0) {
                    html = '<tr><td colspan="6" class="text-center py-4 text-muted">No se encontraron registros para este periodo y estado.</td></tr>';
                } else {
                    resp.data.forEach(row => {
                        let shortFalla = row.falla && row.falla.length > 35 ? row.falla.substring(0,35) + '...' : (row.falla || '');
                        html += `
                        <tr>
                            <td class="fw-bold ps-3 text-dark">${row.folio}</td>
                            <td>${row.fecha}</td>
                            <td><span class="text-truncate d-inline-block" style="max-width: 120px;" title="${row.area}">${row.area}</span></td>
                            <td><span class="text-truncate d-inline-block" style="max-width: 100px;">${row.asignado || '<i class="text-muted">Sin asignar</i>'}</span></td>
                            <td class="text-muted small" title="${row.falla}">${shortFalla}</td>
                            <td class="text-center pe-3">
                                <a href="editarU.php?ref=${row.id}" target="_blank" class="btn btn-sm" style="background-color: #882e41; color: white; padding: 2px 6px;" title="Ver/Editar"><i class="bi bi-box-arrow-up-right"></i></a>
                            </td>
                        </tr>`;
                    });
                }
                document.getElementById('tablaDetalles').innerHTML = html;
            } else {
                document.getElementById('tablaDetalles').innerHTML = `<tr><td colspan="6" class="text-center text-danger">Error al cargar datos.</td></tr>`;
            }
        })
        .catch(err => {
            console.error(err);
            document.getElementById('tablaDetalles').innerHTML = `<tr><td colspan="6" class="text-center text-danger">Error de red.</td></tr>`;
        });
}

function renderizarDashboard(data) {
    // 1. KPIs
    animateValue('kpiTotal', 0, data.total_soportes, 1000);
    animateValue('kpiResueltos', 0, data.finalizados + data.cerrados, 1000);
    animateValue('kpiProceso', 0, data.en_proceso, 1000);
    animateValue('kpiPendientes', 0, data.pendientes, 1000);
    
    document.getElementById('kpiTasa').innerText = `${data.tasa_resolucion}% Tasa de Resolución`;
    
    const variacionEl = document.getElementById('kpiVariacion');
    if(data.variacion > 0) {
        variacionEl.innerHTML = `<i class="bi bi-arrow-up-right-circle text-white"></i> +${data.variacion}% vs periodo anterior`;
    } else if(data.variacion < 0) {
        variacionEl.innerHTML = `<i class="bi bi-arrow-down-right-circle text-warning"></i> ${data.variacion}% vs periodo anterior`;
    } else {
        variacionEl.innerHTML = `<i class="bi bi-dash-circle text-white"></i> Sin cambio vs anterior`;
    }

    // 2. Tabla Usuarios
    let tablaHTML = '';
    data.productividad_usuarios.forEach(u => {
        let barColor = u.tasa_resolucion >= 80 ? 'bg-success' : (u.tasa_resolucion >= 50 ? 'bg-primary' : 'bg-warning');
        tablaHTML += `
            <tr>
                <td class="fw-bold"><i class="bi bi-person-circle text-secondary"></i> ${u.usuario}</td>
                <td class="text-center">${u.total}</td>
                <td class="text-center"><span class="badge bg-success">${u.finalizados + u.cerrados}</span></td>
                <td class="text-center fw-bold text-muted">${u.tasa_resolucion}%</td>
                <td>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar ${barColor}" role="progressbar" style="width: ${u.tasa_resolucion}%" aria-valuenow="${u.tasa_resolucion}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </td>
            </tr>`;
    });
    document.getElementById('tablaUsuarios').innerHTML = tablaHTML || '<tr><td colspan="5" class="text-center">No hay datos</td></tr>';

    // 3. Gráficas
    
    // Tendencia Line Chart
    initChart('tendenciaChart', 'line', {
        labels: data.tendencia.map(t => t.periodo),
        datasets: [{
            label: 'Total Soportes',
            data: data.tendencia.map(t => t.total),
            borderColor: colors.primary,
            backgroundColor: colors.primary + '20',
            fill: true,
            tension: 0.4
        }, {
            label: 'Resueltos',
            data: data.tendencia.map(t => t.resueltos),
            borderColor: colors.success,
            backgroundColor: colors.success + '20',
            fill: true,
            tension: 0.4
        }]
    }, { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } });

    // Estado Doughnut Chart
    initChart('estadoChart', 'doughnut', {
        labels: ['Pendientes', 'En Proceso', 'Finalizados', 'Cerrados'],
        datasets: [{
            data: [data.pendientes, data.en_proceso, data.finalizados, data.cerrados],
            backgroundColor: [colors.danger, colors.warning, colors.success, colors.primary],
            borderWidth: 0
        }]
    }, { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom' } } });

    // Áreas Bar Chart (Horizontal)
    let areas = data.por_area.slice(0, 5); // To 5
    initChart('areasChart', 'bar', {
        labels: areas.map(a => a.area.length > 20 ? a.area.substring(0,20)+'...' : a.area),
        datasets: [{
            label: 'Soportes solicitados',
            data: areas.map(a => a.total),
            backgroundColor: colors.secondary,
            borderRadius: 6
        }]
    }, { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } });

    // Equipos Doughnut Chart
    initChart('equiposChart', 'pie', {
        labels: data.por_tipo_equipo.map(t => t.tipo.length > 15 ? t.tipo.substring(0,15)+'...' : t.tipo),
        datasets: [{
            data: data.por_tipo_equipo.map(t => t.total),
            backgroundColor: colors.palette,
            borderWidth: 2,
            borderColor: '#fff'
        }]
    }, { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } });

    // Días Bar Chart
    initChart('diasChart', 'bar', {
        labels: data.por_dia_semana.map(d => d.dia.substring(0,3)),
        datasets: [{
            label: 'Soportes',
            data: data.por_dia_semana.map(d => d.total),
            backgroundColor: colors.info,
            borderRadius: 4
        }]
    }, { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } });

    // Horas Line Chart
    initChart('horasChart', 'bar', {
        labels: data.por_hora.map(h => h.hora.substring(0,2)+'h'),
        datasets: [{
            label: 'Carga horaria',
            data: data.por_hora.map(h => h.total),
            backgroundColor: colors.palette[2],
            borderRadius: 4
        }]
    }, { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } });
}
</script>
</body>
</html>
