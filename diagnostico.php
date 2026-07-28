<?php
include_once("fpdf.php");
require_once("conexion.php");
require_once("MultiCell.php");

$GLOBALS['folio']=$_GET['ref'];

$query1="select * from soportes where folio = '$GLOBALS[folio]'";
$res=mysqli_query($conecta,$query1);
while ($da=mysqli_fetch_array($res))
{
	
	$GLOBALS['equipo']=$da['tipo_equipo'];
	$GLOBALS['asig']=$da['asignado'];
	$GLOBALS['zona']=$da['area'];
	$GLOBALS['mantenimiento']=$da['tipo_mantto'];
}

class PDF extends PDF_MC_TABLE
{
function Footer(){
        $this->SetFont('Arial','',11);
		$this->SetXY(78,260);
		$this->Cell(60,5,utf8_decode('Av. Juárez 20 Col. Centro Tuxpan, Ver. Tel.7838350118'),0,0,'C');
	    }
function Header(){
	$this->Image('log2.png',5,6,20,0);
	$this->Image('log1.png',185,6,25,0);

	$this->SetFont('Arial','B',18);
	$this->Cell(0,6,utf8_decode('Coordinación de Tecnologías de la Información'),0,1,'C');
	$this->Ln(4);
	$this->SetFont('Arial','B',14);
	$this->Cell(0,6,utf8_decode('Reporte de Evidencias del Equipo con el Folio: '.$GLOBALS['folio']),0,0,'C');
	$this->Ln(10);
}
function NbLines($w,$txt)
{
    $cw = &$this->CurrentFont['cw'];

    if($w==0)
        $w = $this->w - $this->rMargin - $this->x;

    $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;

    $s = str_replace("\r",'',$txt);
    $nb = strlen($s);

    if($nb>0 && $s[$nb-1]=="\n")
        $nb--;

    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $nl = 1;

    while($i<$nb)
    {
        $c = $s[$i];

        if($c=="\n")
        {
            $i++;
            $sep = -1;
            $j = $i;
            $l = 0;
            $nl++;
            continue;
        }

        if($c==' ')
            $sep = $i;

        $l += $cw[$c];

        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i = $sep+1;

            $sep = -1;
            $j = $i;
            $l = 0;
            $nl++;
        }
        else
            $i++;
    }

    return $nl;
}
function EstimarAlturaEvidencia($imagen,$nota,$anchoImagen)
{
    $altoMaximo = 120;
    $alturaImagen = 0;

    if(!empty($imagen) && file_exists($imagen))
    {
        list($w,$h) = getimagesize($imagen);

        if($w>0 && $h>0)
        {
            $alturaImagen = ($h/$w)*$anchoImagen;

            if($alturaImagen > $altoMaximo)
            {
                $alturaImagen = $altoMaximo;
            }
        }
    }

    $this->SetFont('Arial','',12);

    $numLineas = $this->NbLines(0,utf8_decode($nota));

    $alturaTexto = $numLineas * 6;

    return $alturaImagen + $alturaTexto + 12;
}
function VerificarSaltoPagina($altura)
{
    $limite = $this->GetPageHeight() - 10;

    if($this->GetY() + $altura > $limite)
    {
        $this->AddPage('portrait','letter');
    }
}
}

$pdf=new PDF();
$pdf->AddPage('portrait','letter');
$pdf->AliasNbPages();

$pdf->SetFont('Arial','',12);
$pdf->Cell(62,6,utf8_decode('Equipo: '.$GLOBALS['equipo']),0,0,'J');$pdf->Cell(62,6,utf8_decode('Área: '.$GLOBALS['zona']),0,0,'J');
$pdf->Cell(72,6,utf8_decode('Mantenimiento realizado: '.$GLOBALS['mantenimiento']),0,1,'J');
$pdf->Cell(0,6,utf8_decode('Soporte realizado por: '.$GLOBALS['asig']),0,1,'J');
$pdf->Ln(6);

$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,6,utf8_decode('Evidencias'),0,1,'C');
$pdf->Ln(4);

$fotos = [];
$qFotos = mysqli_query($conecta, "SELECT foto FROM fotos WHERE folio = '$GLOBALS[folio]' ORDER BY id ASC");
while ($row = mysqli_fetch_assoc($qFotos)) {
    $fotos[] = $row['foto'];
}

$notas = [];
$qNotas = mysqli_query($conecta, "SELECT nota FROM evidencias WHERE folio = '$GLOBALS[folio]' ORDER BY id ASC");
while ($row = mysqli_fetch_assoc($qNotas)) {
    $notas[] = $row['nota'];
}

$anchoImagen = 160;
$maxElementos = max(count($fotos), count($notas));

for ($i = 0; $i < $maxElementos; $i++) {
    $foto = isset($fotos[$i]) ? $fotos[$i] : '';
    $nota = isset($notas[$i]) ? $notas[$i] : '';

    $alturaEvidencia = $pdf->EstimarAlturaEvidencia($foto, $nota, $anchoImagen);

    $pdf->VerificarSaltoPagina($alturaEvidencia);

    $yActual = $pdf->GetY();

    if (!empty($foto) && file_exists($foto)) {
        list($width, $height) = getimagesize($foto);
        
        $altoMaximo = 120;
        $anchoImg = $anchoImagen;
        
        if ($width > 0 && $height > 0) {
            $altoImagen = ($height / $width) * $anchoImg;
            
            if ($altoImagen > $altoMaximo) {
                $altoImagen = $altoMaximo;
                $anchoImg = ($width / $height) * $altoImagen;
            }

            $x = ($pdf->GetPageWidth() - $anchoImg) / 2;
            $pdf->Image($foto, $x, $yActual, $anchoImg, $altoImagen);
            $pdf->SetY($yActual + $altoImagen + 4);
        }
    } else {
        $pdf->SetY($yActual + 4);
    }

    if (!empty($nota)) {
        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 6, utf8_decode($nota), 0, 'J');
    }

    $pdf->Ln(6);
}

$nombre="Reporte_Evidencias_".$GLOBALS['folio'].".pdf";
$pdf->Output('I',$nombre);
?>