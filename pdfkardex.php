<?php
include_once("fpdf.php");
require_once("conexion.php");
require_once("MultiCell.php");

$GLOBALS['inventario'] = $_POST['in'];

$soporte = "SELECT * FROM soportes WHERE n_inventario = '$GLOBALS[inventario]'";
$res = mysqli_query($conecta,$soporte);
while ($exp=mysqli_fetch_array($res))
{
	$GLOBALS['equipo']=$exp['tipo_equipo'];
}

$query = "SELECT s.fecha, s.tipo_equipo, s.falla, e.nota FROM soportes s LEFT JOIN ( SELECT *, ROW_NUMBER() OVER (PARTITION BY folio ORDER BY id DESC) AS rn FROM evidencias ) e ON s.folio = e.folio AND e.rn = 1 WHERE s.n_inventario = '$GLOBALS[inventario]' ORDER BY s.fecha DESC";

class PDF extends PDF_MC_Table
{
function Footer()
{
	$this->SetFont('Arial','',11);
	$this->SetXY(78,280);
	$this->Cell(60,5,utf8_decode('Av. Juárez 20 Col. Centro Tuxpan, Ver. Tel.7838350118'),0,0,'C');
}
function Header()
{
	$this->Image('log2.png',5,6,25,0);
	$this->Image('log1.png',180,6,25,0);
	
	$this->SetFont('Arial','B',18);
	$this->Cell(0,5,utf8_decode('Coordinación de Tecnologías de la Información'),0,0,'C');
	$this->Ln(8);
	$this->SetFont('Arial','B',14);
	$this->Cell(0,5,'Kardex del equipo: '.$GLOBALS['equipo'],0,1,'C');
	$this->Cell(0,5,utf8_decode('Numero de inventario del equipo: '.$GLOBALS['inventario']),0,1,'C');
}	
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->Ln(4);
$pdf->SetFillColor(149,47,87);//Color de fondo
$pdf->SetTextColor(255);//Color del texto
$pdf->SetFont('Arial','B', 12);
$pdf->Cell(24,5,'Fecha',1,0,'C',1);$pdf->Cell(83,5,'Falla',1,0,'C',1);$pdf->Cell(83,5,'Diagnostico',1,1,'C',1);
$pdf->SetFont('Arial','', 11);
$pdf->SetTextColor(false);//Color del texto

$registros=mysqli_query($conecta,$query);
$pdf->SetWidths(array(24,83,83));
while ($dato=mysqli_fetch_array($registros))
{
	
	$pdf->Row(array($dato['fecha'],$dato['falla'],$dato['nota']));
}

$pdf->Output(); //Salida al navegador
?>