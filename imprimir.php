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
		$this->line(14,265,100,265);
        $this->SetXY(25,266);
        $this->SetFont('Arial','',11);
        $this->Cell(60,5,utf8_decode('Entregó (Nombre,Fecha,firma)'),0,0,'C');
        $this->line(198,265,110,265);
        $this->SetXY(120,266);
        $this->SetFont('Arial','',11);
        $this->Cell(60,5,utf8_decode('Recibió (Nombre,Fecha,firma)'),0,0,'C');
		$this->SetXY(78,274);
		$this->Cell(60,5,utf8_decode('Av. Juárez 20 Col. Centro Tuxpan, Ver. Tel.7838350118'),0,0,'C');
	    }
 function Header(){
         
        $this->SetXY(10,45);
		$this->SetFont('Arial','B',12);
        $this->Cell(20,15,'',0,0,'C', $this->Image('logo2.png',12,8,16));
        $this->Cell(20,15,'',0,0,'C',$this->Image('logo1.png', 170, 8, 30));
		
		$this->SetXY(82,15);
		$this->Cell(40,5,utf8_decode('Coordinación de Tecnologías de Información y Comunicaciones'),0,0,'C');
		
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
        $pdf->Cell(20,15,'',0,0,'C', $pdf->Image('logo2.png',12,150,16));
        $pdf->Cell(20,15,'',0,0,'C',$pdf->Image('logo1.png', 170, 150, 30));
		
		$pdf->SetXY(82,157);
		$pdf->Cell(40,5,utf8_decode('Coordinación de Tecnologías de Información y Comunicaciones'),0,0,'C');
		
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
$pdf->SetXY(13,40);
$pdf->Cell(20,5,utf8_decode('Tipo de Equipo:'),0,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(46, 40);
$pdf->Cell(55,5,utf8_decode($GLOBALS['tipo_equipo']),0, 0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(105,40);
$pdf->Cell(10,5,utf8_decode('Marca:'),0,0,'R');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(114,40);
$pdf->Cell(34,5,utf8_decode($GLOBALS['marca']),0,0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(164,40);
$pdf->Cell(10,5,utf8_decode('No. Inv.:'),0,0,'R');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(173,40);
$pdf->Cell(25,5,utf8_decode($GLOBALS['n_inventario']),0,0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(13,47);
$pdf->Cell(20,5,utf8_decode('Modelo:'),0,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(30, 47);
$pdf->Cell(30,5,utf8_decode($GLOBALS['modelo']),0, 0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(67,47);
$pdf->Cell(10,5,utf8_decode('Serie:'),0,0,'R');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(76,47);
$pdf->Cell(30,5,utf8_decode($GLOBALS['serie']),0,0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(32,54);
$pdf->Cell(10,5,utf8_decode('Tipo Mantto.:'),0,0,'R');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(41,54);
$pdf->Cell(25,5,utf8_decode($GLOBALS['tipo_mantto']),0,0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(75,54);
$pdf->Cell(10,5,utf8_decode('Contacto:'),0,0,'R');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(83,54);
$pdf->Cell(25,5,utf8_decode($GLOBALS['contacto']),0,0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(120,47);
$pdf->Cell(10,5,utf8_decode('Área:'),0,0,'R');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(130,47);
$pdf->Multicell(50,5,utf8_decode($GLOBALS['area']),0,'J');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(13,61);
$pdf->Cell(32,5,utf8_decode('Usuario de Eq.:'),0,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(45,61);
$pdf->Cell(65,5,utf8_decode($GLOBALS['usuario_equipo']),0,0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(110,61);
$pdf->Cell(20,5,utf8_decode('Reportó:'),0,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(130,61);
$pdf->Cell(67,5,utf8_decode($GLOBALS['usuario_reporte']),0,0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(13,68);
$pdf->Cell(48,5,utf8_decode('Descripción de la falla:'),0,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(13,75);
$pdf->Multicell(185,6,utf8_decode($GLOBALS['falla']),1,'J');





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
$pdf->SetXY(13,181);
$pdf->Cell(20,5,utf8_decode('Tipo de Equipo:'),0,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(46, 181);
$pdf->Cell(55,5,utf8_decode($GLOBALS['tipo_equipo']),0, 0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(105,181);
$pdf->Cell(10,5,utf8_decode('Marca:'),0,0,'R');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(114,181);
$pdf->Cell(34,5,utf8_decode($GLOBALS['marca']),0,0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(164,181);
$pdf->Cell(10,5,utf8_decode('No. Inv.:'),0,0,'R');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(173,181);
$pdf->Cell(25,5,utf8_decode($GLOBALS['n_inventario']),0,0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(13,188);
$pdf->Cell(20,5,utf8_decode('Modelo:'),0,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(30, 188);
$pdf->Cell(30,5,utf8_decode($GLOBALS['modelo']),0, 0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(67,188);
$pdf->Cell(10,5,utf8_decode('Serie:'),0,0,'R');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(76,188);
$pdf->Cell(30,5,utf8_decode($GLOBALS['serie']),0,0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(32,195);
$pdf->Cell(10,5,utf8_decode('Tipo Mantto.:'),0,0,'R');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(41,195);
$pdf->Cell(25,5,utf8_decode($GLOBALS['tipo_mantto']),0,0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(75,195);
$pdf->Cell(10,5,utf8_decode('Contacto:'),0,0,'R');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(83,195);
$pdf->Cell(25,5,utf8_decode($GLOBALS['contacto']),0,0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(120,188);
$pdf->Cell(10,5,utf8_decode('Área:'),0,0,'R');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(130,188);
$pdf->Multicell(50,5,utf8_decode($GLOBALS['area']),0,'J');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(13,202);
$pdf->Cell(32,5,utf8_decode('Usuario de Eq.:'),0,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(45,202);
$pdf->Cell(65,5,utf8_decode($GLOBALS['usuario_equipo']),0,0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(110,202);
$pdf->Cell(20,5,utf8_decode('Reportó:'),0,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(130,202);
$pdf->Cell(67,5,utf8_decode($GLOBALS['usuario_reporte']),0,0,'L');
$pdf->SetFont('Arial','B', 12);
$pdf->SetXY(13,209);
$pdf->Cell(48,5,utf8_decode('Descripción de la falla:'),0,0,'L');
$pdf->SetFont('Arial','', 12);
$pdf->SetXY(13,216);
$pdf->Multicell(185,6,utf8_decode($GLOBALS['falla']),1,'J');


$pdf->line(14,125,100,125);
$pdf->SetXY(25,126);
$pdf->SetFont('Arial','',11);
$pdf->Cell(60,5,utf8_decode('Entregó (Nombre,Fecha,firma)'),0,0,'C');

$pdf->line(198,125,110,125);
$pdf->SetXY(120,126);
$pdf->SetFont('Arial','',11);
$pdf->Cell(60,5,utf8_decode('Recibió (Nombre,Fecha,firma)'),0,0,'C');


$pdf->SetXY(78,135);
$pdf->SetFont('Arial','',11);
$pdf->Cell(60,5,utf8_decode('Av. Juárez 20 Col. Centro Tuxpan, Ver. Tel.7838350118'),0,0,'C');
$nombre="Soporte_".$GLOBALS['clave'].".pdf";
$pdf->Output('D',$nombre); //Salida al navegador
 
?>
