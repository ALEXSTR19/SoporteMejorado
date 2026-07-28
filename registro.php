<?php
$GLOBALS['folio']=$_GET['ref'];
date_default_timezone_set('America/Mexico_City');
setlocale(LC_ALL,"es_ES");
include_once("fpdf.php");
include_once("conexion.php");

       $soporte = "SELECT * FROM soportes WHERE id = '$GLOBALS[folio]'";
	   $res = mysqli_query($conecta,$soporte);
	   while ($exp=mysqli_fetch_array($res)) {
		  $GLOBALS['clave']=$exp['folio']; 
		  $GLOBALS['fecha']=$exp['fecha'];
		  $GLOBALS['hora']=$exp['hora'];
		  $GLOBALS['usuario']=$exp['usuario'];
		  $GLOBALS['tipo_equipo']=$exp['tipo_equipo'];
		  $GLOBALS['marca']=$exp['marca'];
		  $GLOBALS['modelo']=$exp['modelo'];
		  $GLOBALS['serie']=$exp['serie'];
		  $GLOBALS['n_inventario']=$exp['n_inventario'];
		  $GLOBALS['area']=$exp['area'];
		  $GLOBALS['tipo_mantto']=$exp['tipo_mantto'];
		  $GLOBALS['usuario_equipo']=$exp['usuario_equipo'];
		  $GLOBALS['usuario_reporte']=$exp['usuario_reporte'];
		  $GLOBALS['contacto']=$exp['contacto'];
		  $GLOBALS['falla']=$exp['falla'];
	   }

	    mysqli_close($conecta);
class PDF_MC_Table extends FPDF
{
function Footer(){
        $this->SetFont('Arial','',11);
		$this->line(14,261,100,261);
        $this->SetXY(25,262);
        $this->SetFont('Arial','',11);
        $this->Cell(60,5,utf8_decode('Entregó (Nombre,Fecha,firma)'),0,0,'C');
        $this->line(198,261,110,261);
        $this->SetXY(120,262);
        $this->SetFont('Arial','',11);
        $this->Cell(60,5,utf8_decode('Recibió (Nombre,Fecha,firma)'),0,0,'C');
		$this->SetXY(78,270);
		$this->Cell(60,5,utf8_decode('Av. Juárez 20 Col. Centro Tuxpan, Ver. Tel.7838350118'),0,0,'C');
	    }
 function Header(){
         
        $this->SetXY(10,45);
		$this->SetFont('Arial','B',12);
        $this->Cell(20,15,'',0,0,'C', $this->Image('log2.png',10,6,26));
        $this->Cell(20,15,'',0,0,'C',$this->Image('log1.png', 170,4,26));
		
		$this->SetXY(82,15);
		$this->Cell(40,5,utf8_decode('Coordinación de Tecnologías de la Información'),0,0,'C');
		
		$this->SetFont('Arial','B',12);
		$this->SetXY(164,28);
		$this->Cell(16,5,'Folio:',0,0,'R');
		$this->SetXY(178,28);
		$this->Cell(30,5,$GLOBALS['clave'],0,0,'L');
		$this->SetFont('Arial','B',14);
		$this->SetXY(72,37);
		$this->Cell(50,5,utf8_decode(''),0,0,'C');
		$this->Ln(22);
	
    }	
}

$pdf = new PDF_MC_Table();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial','B', 12);

//Segundo Encabezado

        $pdf->SetXY(10,180);
		$pdf->SetFont('Arial','B',12);
        $pdf->Cell(20,15,'',0,0,'C', $pdf->Image('log2.png',10,147,26));
        $pdf->Cell(20,15,'',0,0,'C',$pdf->Image('log1.png', 170,146,26));
		
		$pdf->SetXY(82,157);
		$pdf->Cell(40,5,utf8_decode('Coordinación de Tecnologías de la Información'),0,0,'C');
		
		$pdf->SetFont('Arial','B',12);
		$pdf->SetXY(164,169);
		$pdf->Cell(16,5,'Folio:',0,0,'R');
		$pdf->SetXY(178,169);
		$pdf->Cell(30,5,$GLOBALS['clave'],0,0,'L');
		$pdf->SetFont('Arial','B',14);
		$pdf->SetXY(72,37);
		$pdf->Cell(50,5,utf8_decode(''),0,0,'C');

$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(13,33);
$pdf->Cell(26,5,'Fecha Reg.:',1,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(39,33);
$pdf->Cell(25,5,utf8_decode($GLOBALS['fecha']),1,0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(64,33);
$pdf->Cell(24,5,utf8_decode('Hora Reg.:'),1,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(88,33);
$pdf->Cell(20,5,utf8_decode($GLOBALS['hora']),1,0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(108,33);
$pdf->Cell(20,5,utf8_decode('Registró:'),1,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(128,33);
$pdf->Cell(70,5,utf8_decode($GLOBALS['usuario']),1,0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(13,44);
$pdf->Cell(20,5,utf8_decode('Tipo de Equipo:'),0,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(46, 44);
$pdf->MultiCell(55,4,utf8_decode($GLOBALS['tipo_equipo']),0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(103,44);
$pdf->Cell(10,5,utf8_decode('Marca:'),0,0,'R');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(114,44);
$pdf->MultiCell(38,4,utf8_decode($GLOBALS['marca']),0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(154,44);
$pdf->Cell(18,5,utf8_decode('No. Inv.:'),0,0,'R');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(173,44);
$pdf->MultiCell(25,4,utf8_decode($GLOBALS['n_inventario']),0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(13,55);
$pdf->Cell(20,5,utf8_decode('Modelo:'),0,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(30, 55);
$pdf->MultiCell(35,4,utf8_decode($GLOBALS['modelo']),0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(65,55);
$pdf->Cell(15,5,utf8_decode('Serie:'),0,0,'R');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(81,55);
$pdf->MultiCell(35,4,utf8_decode($GLOBALS['serie']),0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(118,55);
$pdf->Cell(10,5,utf8_decode('Área:'),0,0,'R');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(129,55);
$pdf->MultiCell(65,4,utf8_decode($GLOBALS['area']),0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(13,66);
$pdf->Cell(30,5,utf8_decode('Tipo Mantto.:'),0,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(44,66);
$pdf->MultiCell(30,4,utf8_decode($GLOBALS['tipo_mantto']),0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(74,66);
$pdf->Cell(20,5,utf8_decode('Contacto:'),0,0,'R');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(95,66);
$pdf->MultiCell(35,4,utf8_decode($GLOBALS['contacto']),0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(13,77);
$pdf->Cell(32,5,utf8_decode('Usuario de Eq.:'),0,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(46,77);
$pdf->MultiCell(60,4,utf8_decode($GLOBALS['usuario_equipo']),0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(108,77);
$pdf->Cell(20,5,utf8_decode('Reportó:'),0,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(129,77);
$pdf->MultiCell(65,4,utf8_decode($GLOBALS['usuario_reporte']),0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(13,88);
$pdf->Cell(48,5,utf8_decode('Descripción de la falla:'),0,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(13,94);
$pdf->Multicell(185,5,utf8_decode($GLOBALS['falla']),1,'J');





$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(13,174);
$pdf->Cell(26,5,'Fecha Reg.:',1,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(39,174);
$pdf->Cell(25,5,utf8_decode($GLOBALS['fecha']),1,0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(64,174);
$pdf->Cell(24,5,utf8_decode('Hora Reg.:'),1,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(88,174);
$pdf->Cell(20,5,utf8_decode($GLOBALS['hora']),1,0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(108,174);
$pdf->Cell(20,5,utf8_decode('Registró:'),1,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(128,174);
$pdf->Cell(70,5,utf8_decode($GLOBALS['usuario']),1,0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(13,185);
$pdf->Cell(20,5,utf8_decode('Tipo de Equipo:'),0,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(46, 185);
$pdf->MultiCell(55,4,utf8_decode($GLOBALS['tipo_equipo']),0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(103,185);
$pdf->Cell(10,5,utf8_decode('Marca:'),0,0,'R');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(114,185);
$pdf->MultiCell(38,4,utf8_decode($GLOBALS['marca']),0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(154,185);
$pdf->Cell(18,5,utf8_decode('No. Inv.:'),0,0,'R');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(173,185);
$pdf->MultiCell(25,4,utf8_decode($GLOBALS['n_inventario']),0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(13,196);
$pdf->Cell(20,5,utf8_decode('Modelo:'),0,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(30, 196);
$pdf->MultiCell(35,4,utf8_decode($GLOBALS['modelo']),0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(65,196);
$pdf->Cell(15,5,utf8_decode('Serie:'),0,0,'R');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(81,196);
$pdf->MultiCell(35,4,utf8_decode($GLOBALS['serie']),0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(118,196);
$pdf->Cell(10,5,utf8_decode('Área:'),0,0,'R');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(129,196);
$pdf->MultiCell(65,4,utf8_decode($GLOBALS['area']),0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(13,207);
$pdf->Cell(30,5,utf8_decode('Tipo Mantto.:'),0,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(44,207);
$pdf->MultiCell(30,4,utf8_decode($GLOBALS['tipo_mantto']),0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(74,207);
$pdf->Cell(20,5,utf8_decode('Contacto:'),0,0,'R');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(95,207);
$pdf->MultiCell(35,4,utf8_decode($GLOBALS['contacto']),0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(13,218);
$pdf->Cell(32,5,utf8_decode('Usuario de Eq.:'),0,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(46,218);
$pdf->MultiCell(60,4,utf8_decode($GLOBALS['usuario_equipo']),0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(108,218);
$pdf->Cell(20,5,utf8_decode('Reportó:'),0,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(129,218);
$pdf->MultiCell(65,4,utf8_decode($GLOBALS['usuario_reporte']),0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(13,229);
$pdf->Cell(48,5,utf8_decode('Descripción de la falla:'),0,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(13,235);
$pdf->Multicell(185,5,utf8_decode($GLOBALS['falla']),1,'J');


$pdf->line(14,121,100,121);
$pdf->SetXY(25,122);
$pdf->SetFont('Arial','',11);
$pdf->Cell(60,5,utf8_decode('Entregó (Nombre,Fecha,firma)'),0,0,'C');

$pdf->line(198,121,110,121);
$pdf->SetXY(120,122);
$pdf->SetFont('Arial','',11);
$pdf->Cell(60,5,utf8_decode('Recibió (Nombre,Fecha,firma)'),0,0,'C');


$pdf->SetXY(78,131);
$pdf->SetFont('Arial','',11);
$pdf->Cell(60,5,utf8_decode('Av. Juárez 20 Col. Centro Tuxpan, Ver. Tel.7838350118'),0,0,'C');
$pdf->SetXY(0,138);
$pdf->Cell(60,5,'-----------------------------------------------------------------------------------------------------------------------------------------------------------------',0,0,'L');
$nombre="Soporte_".$GLOBALS['clave'].".pdf";
$pdf->Output('I',$nombre); //Salida al navegador
 
?>
