<?php
require_once("seguridad.php");
require_once("conecta.php");
require_once("conexion.php");

$folio    = isset($_POST['folio']) ? trim($_POST['folio']) : '';
$area     = isset($_POST['area']) ? trim($_POST['area']) : '';
$nombre   = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$asignado = isset($_POST['asignado']) ? trim($_POST['asignado']) : '';

$sql = "SELECT * FROM soportes WHERE 1=1";
$params = [];

if ($folio !== '') {
    $sql .= " AND folio LIKE :folio";
    $params[':folio'] = "%{$folio}%";
}

if ($area !== '') {
    $sql .= " AND area LIKE :area";
    $params[':area'] = "%{$area}%";
}

if ($nombre !== '') {
    $sql .= " AND usuario_reporte LIKE :nombre";
    $params[':nombre'] = "%{$nombre}%";
}

if ($asignado !== '') {
    $sql .= " AND asignado LIKE :asignado";
    $params[':asignado'] = "%{$asignado}%";
}

$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($registros) > 0) {
    foreach ($registros as $dato) {
        echo '<tr data-folio="' . htmlspecialchars($dato['folio'] ?? $dato['id']) . '" style="cursor:pointer;">';
        echo '<td>' . htmlspecialchars($dato['folio'] ?? $dato['id']) . '</td>';
        echo '<td>' . htmlspecialchars($dato['fecha'] ?? '') . '</td>';
        echo '<td>' . htmlspecialchars($dato['tipo_equipo'] ?? '') . '</td>';
        echo '<td>' . htmlspecialchars($dato['n_inventario'] ?? '') . '</td>';
        echo '<td>' . htmlspecialchars($dato['area'] ?? '') . '</td>';
        echo '<td>' . htmlspecialchars($dato['usuario_reporte'] ?? '') . '</td>';
        echo '<td>' . htmlspecialchars($dato['asignado'] ?? '') . '</td>';
        echo '<td>';
        switch($dato['estado'] ?? 0) {
            case 0: echo '<center><i class="bi bi-exclamation-triangle-fill icon-danger"></i><br>Pendiente</center>'; break;
            case 1: echo '<center><i class="bi bi-exclamation-circle-fill icon-warning"></i><br>En proceso</center>'; break;
            case 2: echo '<center><i class="bi bi-check-square-fill icon-success"></i><br>Finalizado</center>'; break;
            case 3: echo '<center><i class="bi bi-lock-fill"></i><br>Cerrado</center>'; break;
        }
        echo '</td>';
        echo '<td>
                <div class="btn-group" role="group">
                    <a href="editar.php?ref=' . htmlspecialchars($dato['id']) . '" class="btn btn-editar">
                        Editar
                    </a>
                    <a href="registro.php?ref=' . htmlspecialchars($dato['id']) . '" class="btn btn-sm btn-imprimir" target="_blank">
                        Imprimir
                    </a>
                </div>
              </td>';
        echo '</tr>';
    }
} else {
    echo '<tr>
            <td colspan="10" class="text-center py-4">
                <div class="alert alert-info mb-0">
                    No se encontraron registros.
                </div>
            </td>
          </tr>';
}
?>