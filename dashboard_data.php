<?php
require_once("seguridad.php");
require_once("conexion.php");

header('Content-Type: application/json; charset=utf-8');

// Obtener filtro de período
$periodo = isset($_GET['periodo']) ? $_GET['periodo'] : 'mes';
$fecha_inicio = '';
$fecha_fin = date('Y-m-d');

switch ($periodo) {
    case 'semana':
        $fecha_inicio = date('Y-m-d', strtotime('-7 days'));
        break;
    case 'mes':
        $fecha_inicio = date('Y-m-01');
        break;
    case 'trimestre':
        $fecha_inicio = date('Y-m-d', strtotime('-3 months'));
        break;
    case 'semestre':
        $fecha_inicio = date('Y-m-d', strtotime('-6 months'));
        break;
    case 'anio':
        $fecha_inicio = date('Y-01-01');
        break;
    case 'todo':
        $fecha_inicio = '2000-01-01';
        break;
    default:
        $fecha_inicio = date('Y-m-01');
}

// Si se envían fechas personalizadas
if (isset($_GET['fi']) && isset($_GET['ff'])) {
    $fecha_inicio = mysqli_real_escape_string($conecta, $_GET['fi']);
    $fecha_fin = mysqli_real_escape_string($conecta, $_GET['ff']);
} else {
    $fecha_inicio = mysqli_real_escape_string($conecta, $fecha_inicio);
    $fecha_fin = mysqli_real_escape_string($conecta, $fecha_fin);
}

$action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

if ($action === 'detalles') {
    $estado_filtro = isset($_GET['estado']) ? $_GET['estado'] : '';
    $where_estado = '';
    
    if ($estado_filtro === 'resueltos') {
        $where_estado = "AND estado IN (2, 3)";
    } elseif ($estado_filtro === 'proceso') {
        $where_estado = "AND estado = 1";
    } elseif ($estado_filtro === 'pendientes') {
        $where_estado = "AND estado = 0";
    } elseif ($estado_filtro === 'total') {
        $where_estado = "";
    }

    $usuario_filtro = isset($_GET['usuario']) ? mysqli_real_escape_string($conecta, $_GET['usuario']) : '';
    if (!empty($usuario_filtro)) {
        $where_estado .= " AND asignado = '$usuario_filtro'";
    }
    
    $q_detalles = "SELECT id, folio, fecha, area, asignado, falla, estado FROM soportes WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin' $where_estado ORDER BY id DESC LIMIT 500";
    $res_detalles = mysqli_query($conecta, $q_detalles);
    
    $detalles = [];
    while ($row = mysqli_fetch_assoc($res_detalles)) {
        $detalles[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $detalles], JSON_UNESCAPED_UNICODE);
    exit;
}

$data = [];

// ============================================================
// 1. KPIs GENERALES
// ============================================================
// Total soportes en período
$q = mysqli_query($conecta, "SELECT COUNT(*) as total FROM soportes WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'");
$r = mysqli_fetch_assoc($q);
$data['total_soportes'] = (int)$r['total'];

// Por estado
$q = mysqli_query($conecta, "SELECT estado, COUNT(*) as total FROM soportes WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin' GROUP BY estado");
$estados = [0 => 0, 1 => 0, 2 => 0, 3 => 0];
while ($r = mysqli_fetch_assoc($q)) {
    $estados[(int)$r['estado']] = (int)$r['total'];
}
$data['pendientes'] = $estados[0];
$data['en_proceso'] = $estados[1];
$data['finalizados'] = $estados[2];
$data['cerrados'] = $estados[3];

// Tasa de resolución
$data['tasa_resolucion'] = $data['total_soportes'] > 0 
    ? round(($data['finalizados'] + $data['cerrados']) / $data['total_soportes'] * 100, 1) 
    : 0;

// Total soportes histórico (para comparación)
$periodo_anterior_ini = date('Y-m-d', strtotime($fecha_inicio . ' -' . (strtotime($fecha_fin) - strtotime($fecha_inicio)) / 86400 . ' days'));
$periodo_anterior_fin = date('Y-m-d', strtotime($fecha_inicio . ' -1 day'));

$q = mysqli_query($conecta, "SELECT COUNT(*) as total FROM soportes WHERE fecha BETWEEN '$periodo_anterior_ini' AND '$periodo_anterior_fin'");
$r = mysqli_fetch_assoc($q);
$data['total_anterior'] = (int)$r['total'];

// Variación porcentual
$data['variacion'] = $data['total_anterior'] > 0 
    ? round(($data['total_soportes'] - $data['total_anterior']) / $data['total_anterior'] * 100, 1) 
    : 0;

// ============================================================
// 2. PRODUCTIVIDAD POR USUARIO (asignado)
// ============================================================
$q = mysqli_query($conecta, "
    SELECT 
        asignado,
        COUNT(*) as total,
        SUM(CASE WHEN estado = 0 THEN 1 ELSE 0 END) as pendientes,
        SUM(CASE WHEN estado = 1 THEN 1 ELSE 0 END) as en_proceso,
        SUM(CASE WHEN estado = 2 THEN 1 ELSE 0 END) as finalizados,
        SUM(CASE WHEN estado = 3 THEN 1 ELSE 0 END) as cerrados
    FROM soportes 
    WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
    AND asignado != ''
    GROUP BY asignado
    ORDER BY total DESC
");
$data['productividad_usuarios'] = [];
while ($r = mysqli_fetch_assoc($q)) {
    $total_u = (int)$r['total'];
    $fin_u = (int)$r['finalizados'] + (int)$r['cerrados'];
    $data['productividad_usuarios'][] = [
        'usuario' => $r['asignado'],
        'total' => $total_u,
        'pendientes' => (int)$r['pendientes'],
        'en_proceso' => (int)$r['en_proceso'],
        'finalizados' => (int)$r['finalizados'],
        'cerrados' => (int)$r['cerrados'],
        'tasa_resolucion' => $total_u > 0 ? round($fin_u / $total_u * 100, 1) : 0,
    ];
}

// ============================================================
// 3. SOPORTES POR ÁREA (Top 10)
// ============================================================
$q = mysqli_query($conecta, "
    SELECT area, COUNT(*) as total
    FROM soportes 
    WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
    GROUP BY area
    ORDER BY total DESC
    LIMIT 10
");
$data['por_area'] = [];
while ($r = mysqli_fetch_assoc($q)) {
    $data['por_area'][] = [
        'area' => $r['area'],
        'total' => (int)$r['total']
    ];
}

// ============================================================
// 4. SOPORTES POR TIPO DE EQUIPO
// ============================================================
$q = mysqli_query($conecta, "
    SELECT tipo_equipo, COUNT(*) as total
    FROM soportes 
    WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
    GROUP BY tipo_equipo
    ORDER BY total DESC
    LIMIT 10
");
$data['por_tipo_equipo'] = [];
while ($r = mysqli_fetch_assoc($q)) {
    $data['por_tipo_equipo'][] = [
        'tipo' => $r['tipo_equipo'],
        'total' => (int)$r['total']
    ];
}

// ============================================================
// 5. SOPORTES POR TIPO DE MANTENIMIENTO
// ============================================================
$q = mysqli_query($conecta, "
    SELECT tipo_mantto, COUNT(*) as total
    FROM soportes 
    WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
    GROUP BY tipo_mantto
    ORDER BY total DESC
");
$data['por_tipo_mantto'] = [];
while ($r = mysqli_fetch_assoc($q)) {
    $data['por_tipo_mantto'][] = [
        'tipo' => $r['tipo_mantto'],
        'total' => (int)$r['total']
    ];
}

// ============================================================
// 6. TENDENCIA DIARIA / MENSUAL (últimos 30 días o meses según período)
// ============================================================
if ($periodo === 'anio' || $periodo === 'semestre' || $periodo === 'todo' || 
    ($periodo === 'personalizado' && (strtotime($fecha_fin) - strtotime($fecha_inicio)) > 90 * 86400)) {
    // Agrupar por mes para rangos amplios
    $q = mysqli_query($conecta, "
        SELECT DATE_FORMAT(fecha, '%Y-%m') as periodo, COUNT(*) as total,
            SUM(CASE WHEN estado IN (2,3) THEN 1 ELSE 0 END) as resueltos
        FROM soportes 
        WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
        GROUP BY DATE_FORMAT(fecha, '%Y-%m')
        ORDER BY periodo ASC
    ");
} else {
    // Agrupar por día
    $q = mysqli_query($conecta, "
        SELECT fecha as periodo, COUNT(*) as total,
            SUM(CASE WHEN estado IN (2,3) THEN 1 ELSE 0 END) as resueltos
        FROM soportes 
        WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
        GROUP BY fecha
        ORDER BY fecha ASC
    ");
}
$data['tendencia'] = [];
while ($r = mysqli_fetch_assoc($q)) {
    $data['tendencia'][] = [
        'periodo' => $r['periodo'],
        'total' => (int)$r['total'],
        'resueltos' => (int)$r['resueltos']
    ];
}

// ============================================================
// 7. SOPORTES POR MARCA (Top 10)
// ============================================================
$q = mysqli_query($conecta, "
    SELECT marca, COUNT(*) as total
    FROM soportes 
    WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
    GROUP BY marca
    ORDER BY total DESC
    LIMIT 10
");
$data['por_marca'] = [];
while ($r = mysqli_fetch_assoc($q)) {
    $data['por_marca'][] = [
        'marca' => $r['marca'],
        'total' => (int)$r['total']
    ];
}

// ============================================================
// 8. DISTRIBUCIÓN POR DÍA DE LA SEMANA
// ============================================================
$q = mysqli_query($conecta, "
    SELECT DAYOFWEEK(fecha) as dia, COUNT(*) as total
    FROM soportes 
    WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
    GROUP BY DAYOFWEEK(fecha)
    ORDER BY dia
");
$dias_semana = ['', 'Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
$data['por_dia_semana'] = [];
while ($r = mysqli_fetch_assoc($q)) {
    $data['por_dia_semana'][] = [
        'dia' => $dias_semana[(int)$r['dia']],
        'total' => (int)$r['total']
    ];
}

// ============================================================
// 9. DISTRIBUCIÓN POR HORA DEL DÍA
// ============================================================
$q = mysqli_query($conecta, "
    SELECT HOUR(hora) as hr, COUNT(*) as total
    FROM soportes 
    WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
    GROUP BY HOUR(hora)
    ORDER BY hr
");
$data['por_hora'] = [];
while ($r = mysqli_fetch_assoc($q)) {
    $data['por_hora'][] = [
        'hora' => str_pad($r['hr'], 2, '0', STR_PAD_LEFT) . ':00',
        'total' => (int)$r['total']
    ];
}

// ============================================================
// 10. ÚLTIMOS 5 SOPORTES
// ============================================================
$q = mysqli_query($conecta, "
    SELECT folio, fecha, hora, area, tipo_equipo, asignado, estado, falla
    FROM soportes 
    ORDER BY id DESC
    LIMIT 5
");
$data['recientes'] = [];
while ($r = mysqli_fetch_assoc($q)) {
    $data['recientes'][] = $r;
}

// ============================================================
// 11. INFO DE PERÍODO
// ============================================================
$data['fecha_inicio'] = $fecha_inicio;
$data['fecha_fin'] = $fecha_fin;
$data['periodo'] = $periodo;

echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>
