<?php
include_once("fpdf.php");
require_once("conexion.php");
require_once("MultiCell.php");
require_once("report_branding.php");

$a = $_POST['ar'];
$fi = $_POST['finicio'];
$ff = $_POST['ffin'];
$di = $_POST['di'];
$df = $_POST['df'];
$m = $_POST['m'];
$an = $_POST['a'];
$GLOBALS['reportTitle'] = 'Reporte de Soportes del '.$di.' al '.$df.' de '.$m.' de '.$an.' · Área: '.$a;
$query="select folio,fecha,tipo_equipo,n_inventario,area,serie,asignado,falla from soportes where area = '$a' and fecha between '$fi' and '$ff'";
$query1="select folio from soportes where area = '$a' and fecha between '$fi' and '$ff'";

class PDF extends PDF_MC_Table
{
    function Header() { municipalReportHeader($this, $GLOBALS['reportTitle']); }
    function Footer() { municipalReportFooter($this); }
}

$pdf=new PDF();
$pdf->AddPage('landscape','letter');
$pdf->AliasNbPages();

$pdf->SetFont('Arial','',10);

$folios = [];
$re = mysqli_query($conecta, $query1);

while ($row = mysqli_fetch_assoc($re))
{
    $folios[] = $row['folio'];
}

$ancho_folio_max = 0;
$margen = 3;
foreach ($folios as $folio) {
    $ancho_texto = $pdf->GetStringWidth($folio);
    if ($ancho_texto > $ancho_folio_max) {
        $ancho_folio_max = $ancho_texto;
    }
}
$ancho_folio_deseado = $ancho_folio_max + $margen;
$ANCHO_FOLIO_MIN = 18;
$ANCHO_FOLIO_MAX = 30;
$ancho_folio_final = max($ANCHO_FOLIO_MIN, min($ANCHO_FOLIO_MAX, $ancho_folio_deseado));

$ancho_total_actual = 260;
$ancho_folio_original = 18;

$diferencia = $ancho_folio_final - $ancho_folio_original;
$ancho_fallas_nuevo = 120 - $diferencia;//Cambiar el ancho de toda la tabla
$ANCHO_FALLAS_MIN = 110;
if ($ancho_fallas_nuevo < $ANCHO_FALLAS_MIN) {
    $ancho_folio_final = $ancho_folio_original + (118 - $ANCHO_FALLAS_MIN);
    $ancho_fallas_nuevo = $ANCHO_FALLAS_MIN;
}

$pdf->Ln(6);
municipalTableHeader($pdf);
$pdf->SetFont('Arial','B',10);
$pdf->Cell($ancho_folio_final,6,'Folio',1,0,'C',1);$pdf->Cell(21,6,'Fecha Reg.',1,0,'C',1);$pdf->Cell(24,6,utf8_decode('Equipo'),1,0,'C',1);
$pdf->Cell(29,6,utf8_decode('N. Serie'),1,0,'C',1);$pdf->Cell(29,6,utf8_decode('N. Inventario'),1,0,'C',1);
$pdf->Cell(20,6,utf8_decode('Asignado'),1,0,'C',1);$pdf->Cell($ancho_fallas_nuevo,6,utf8_decode('Falla'),1,1,'C',1);

$pdf->SetTextColor(false);//Color del texto
$pdf->SetFont('Arial','',10);

$registros=mysqli_query($conecta,$query);
$pdf->SetWidths(array($ancho_folio_final,21,24,29,29,20,$ancho_fallas_nuevo));
while ($dato=mysqli_fetch_array($registros))
{
	
	$pdf->Row(array($dato['folio'],$dato['fecha'],$dato['tipo_equipo'],$dato['serie'],
					$dato['n_inventario'],$dato['asignado'],$dato['falla']));
}

$nombre="Reporte_".$a."_".$m."_".$di."_".$df."_".$an.".pdf";
$pdf->Output('I',$nombre);
?>
