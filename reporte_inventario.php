<?php
require_once('seguridad_ambos.php');
require_once('conexion.php');
require_once('inventario_schema.php');
require_once('MultiCell.php');

$errorInventario = prepararInventario($conecta);
$tipoReporte = $_GET['tipo'] ?? 'existencias';
if ($errorInventario || !in_array($tipoReporte, array('existencias', 'salidas'), true)) {
    http_response_code(400);
    exit('No fue posible generar el reporte de inventario solicitado.');
}

function textoPdf($valor) { return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', (string)$valor); }
function cantidadPdf($valor) { return number_format((float)$valor, 2, '.', ','); }

class ReporteInventarioPDF extends PDF_MC_Table
{
    public $tipoReporte = 'existencias';

    function Header()
    {
        $this->Image(__DIR__ . '/log2.png', 8, 7, 27);
        $this->Image(__DIR__ . '/log1.png', 244, 7, 27);
        $this->SetTextColor(136, 46, 65);
        $this->SetFont('Arial', 'B', 17);
        $this->Cell(0, 7, textoPdf('H. Ayuntamiento de Tuxpan, Veracruz'), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 6, textoPdf('Coordinación de Tecnologías de la Información'), 0, 1, 'C');
        $this->SetTextColor(0);
        $this->SetFont('Arial', 'B', 14);
        $titulo = $this->tipoReporte === 'salidas' ? 'Reporte de salidas de inventario' : 'Reporte general de existencias';
        $this->Cell(0, 7, textoPdf($titulo), 0, 1, 'C');
        $this->SetFont('Arial', '', 9);
        $usuario = $_SESSION['usuarioactual'] ?? 'sistema';
        $this->Cell(0, 5, textoPdf('Generado el ' . date('d/m/Y H:i') . ' por ' . $usuario), 0, 1, 'C');
        $this->SetDrawColor(180, 142, 93);
        $this->SetLineWidth(0.8);
        $this->Line(8, $this->GetY() + 1, 271, $this->GetY() + 1);
        $this->Ln(4);
        $this->encabezadoTabla();
    }

    function encabezadoTabla()
    {
        $this->SetFillColor(136, 46, 65);
        $this->SetTextColor(255);
        $this->SetDrawColor(255);
        $this->SetLineWidth(0.2);
        $this->SetFont('Arial', 'B', 8);
        if ($this->tipoReporte === 'salidas') {
            $encabezados = array('Folio / fecha', 'Destino / trabajo', 'Responsable', 'Artículos', 'Unidades', 'Estado');
            $anchos = array(42, 89, 52, 24, 24, 32);
        } else {
            $encabezados = array('Código', 'Artículo / categoría', 'Modelo', 'Ubicación', 'Estado', 'Asignado a', 'Existencia', 'Mínimo');
            $anchos = array(27, 61, 30, 38, 27, 35, 24, 21);
        }
        foreach ($encabezados as $i => $encabezado) {
            $ultimo = $i === count($encabezados) - 1;
            $this->Cell($anchos[$i], 7, textoPdf($encabezado), 1, $ultimo ? 1 : 0, 'C', true);
        }
        $this->SetWidths($anchos);
        $this->SetTextColor(0);
        $this->SetDrawColor(190);
        $this->SetFont('Arial', '', 8);
    }

    function Footer()
    {
        $this->SetY(-14);
        $this->SetDrawColor(180, 142, 93);
        $this->Line(8, $this->GetY(), 271, $this->GetY());
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(90);
        $this->Cell(220, 6, textoPdf('Av. Juárez 20, Col. Centro, Tuxpan, Veracruz · Tel. 783 835 0118'), 0, 0, 'L');
        $this->Cell(43, 6, textoPdf('Página ' . $this->PageNo() . ' de {nb}'), 0, 0, 'R');
    }
}

$pdf = new ReporteInventarioPDF('L', 'mm', 'Letter');
$pdf->tipoReporte = $tipoReporte;
$pdf->SetMargins(8, 8, 8);
$pdf->SetAutoPageBreak(true, 18);
$pdf->AliasNbPages();
$pdf->AddPage();

if ($tipoReporte === 'salidas') {
    $consulta = mysqli_query($conecta, "SELECT s.folio,s.fecha,s.hora,s.destino,s.motivo,s.responsable,s.estado,COUNT(d.id) articulos,COALESCE(SUM(d.cantidad),0) unidades FROM inventario_salidas s LEFT JOIN inventario_salida_detalle d ON d.salida_id=s.id GROUP BY s.id ORDER BY s.id DESC");
    if (!$consulta) exit('No fue posible consultar las salidas de inventario.');
    while ($fila = mysqli_fetch_assoc($consulta)) {
        $pdf->Row(array($fila['folio'] . "\n" . date('d/m/Y', strtotime($fila['fecha'])) . ' ' . substr($fila['hora'], 0, 5), $fila['destino'] . "\n" . $fila['motivo'], $fila['responsable'], (string)(int)$fila['articulos'], cantidadPdf($fila['unidades']), $fila['estado']));
    }
    $nombreArchivo = 'Reporte_Salidas_Inventario_' . date('Y-m-d') . '.pdf';
} else {
    $consulta = mysqli_query($conecta, 'SELECT codigo,nombre,categoria,modelo,ubicacion,estado,usuario_actual,existencia,minimo,unidad FROM inventario_articulos WHERE activo=1 ORDER BY nombre');
    if (!$consulta) exit('No fue posible consultar las existencias de inventario.');
    while ($fila = mysqli_fetch_assoc($consulta)) {
        $pdf->Row(array($fila['codigo'], $fila['nombre'] . "\n" . $fila['categoria'], $fila['modelo'], $fila['ubicacion'] ?: 'Sin asignar', $fila['estado'], $fila['usuario_actual'] ?: 'Sin asignar', cantidadPdf($fila['existencia']) . ' ' . $fila['unidad'], cantidadPdf($fila['minimo'])));
    }
    $nombreArchivo = 'Reporte_Existencias_Inventario_' . date('Y-m-d') . '.pdf';
}

$pdf->Output('I', $nombreArchivo);
