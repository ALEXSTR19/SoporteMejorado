<?php
include_once("fpdf.php");
require_once("conexion.php");
require_once("MultiCell.php");
require_once("report_branding.php");

$GLOBALS['fi'] = $_POST['finicio'];
$GLOBALS['ff'] = $_POST['ffin'];
$GLOBALS['di'] = $_POST['di'];
$GLOBALS['df'] = $_POST['df'];
$GLOBALS['m'] = $_POST['m'];
$GLOBALS['an'] = $_POST['a'];
$query="select folio,fecha,tipo_equipo,n_inventario,area,serie,asignado,falla from soportes where fecha between '$fi' and '$ff'";
$query1="select folio from soportes where fecha between '$fi' and '$ff'";

class PDF extends PDF_MC_TABLE
{
function Footer(){
		municipalReportFooter($this);
	    }
function Header(){
	municipalReportHeader($this, 'Reporte de Soportes del '.$GLOBALS['di'].' al '.$GLOBALS['df'].' de '.$GLOBALS['m'].' de '.$GLOBALS['an'].' · Todas las Áreas');
}	
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

$ancho_total_actual = 263;
$ancho_folio_original = 18;

$diferencia = $ancho_folio_final - $ancho_folio_original;
$ancho_fallas_nuevo = 100 - $diferencia;//Cambiar el ancho de toda la tabla
$ANCHO_FALLAS_MIN = 90;
if ($ancho_fallas_nuevo < $ANCHO_FALLAS_MIN) {
    $ancho_folio_final = $ancho_folio_original + (118 - $ANCHO_FALLAS_MIN);
    $ancho_fallas_nuevo = $ANCHO_FALLAS_MIN;
}

$pdf->Ln(6);
municipalTableHeader($pdf);
$pdf->SetFont('Arial','B',10);
$pdf->Cell($ancho_folio_final,6,'Folio',1,0,'C',1);$pdf->Cell(21,6,'Fecha Reg.',1,0,'C',1);$pdf->Cell(24,6,utf8_decode('Equipo'),1,0,'C',1);
$pdf->Cell(29,6,utf8_decode('N. Serie'),1,0,'C',1);$pdf->Cell(29,6,utf8_decode('N. Inventario'),1,0,'C',1);
$pdf->Cell(22,6,utf8_decode('Área'),1,0,'C',1);$pdf->Cell(20,6,utf8_decode('Asignado'),1,0,'C',1);
$pdf->Cell($ancho_fallas_nuevo,6,utf8_decode('Falla'),1,1,'C',1);

$pdf->SetTextColor(false);//Color del texto
$pdf->SetFont('Arial','',10);

$registros=mysqli_query($conecta,$query);
$pdf->SetWidths(array($ancho_folio_final,21,24,29,29,22,20,$ancho_fallas_nuevo));
while ($dato=mysqli_fetch_array($registros))
{
	
	$pdf->Row(array($dato['folio'],$dato['fecha'],$dato['tipo_equipo'],$dato['serie'],
					$dato['n_inventario'],$dato['area'],$dato['asignado'],$dato['falla']));
}

$nombre="Reporte_".$GLOBALS['m']."_".$GLOBALS['di']."_".$GLOBALS['df']."_".$GLOBALS['an'].".pdf";
$pdf->Output('I',$nombre);
?>
