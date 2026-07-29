<?php
/** Identidad visual compartida para los documentos imprimibles del Ayuntamiento. */
const MUNICIPAL_BURGUNDY = [149, 47, 87];
const MUNICIPAL_GOLD = [190, 153, 72];

function municipalReportHeader($pdf, $title)
{
    $pageWidth = $pdf->GetPageWidth();

    $pdf->SetFillColor(...MUNICIPAL_BURGUNDY);
    $pdf->Rect(0, 0, $pageWidth, 4, 'F');
    $pdf->Image('log2.png', 8, 8, 24, 0);
    $pdf->Image('log1.png', $pageWidth - 32, 8, 24, 0);

    $pdf->SetY(8);
    $pdf->SetTextColor(...MUNICIPAL_BURGUNDY);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(0, 5, utf8_decode('H. AYUNTAMIENTO DE TUXPAN, VERACRUZ'), 0, 1, 'C');
    $pdf->SetTextColor(45, 45, 45);
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 6, utf8_decode('Coordinación de Tecnologías de la Información'), 0, 1, 'C');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 6, utf8_decode($title), 0, 1, 'C');

    $pdf->SetDrawColor(...MUNICIPAL_GOLD);
    $pdf->SetLineWidth(0.8);
    $pdf->Line(10, 31, $pageWidth - 10, 31);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->SetLineWidth(0.2);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetY(35);
}

function municipalTableHeader($pdf)
{
    $pdf->SetFillColor(...MUNICIPAL_BURGUNDY);
    $pdf->SetTextColor(255, 255, 255);
}

function municipalReportFooter($pdf)
{
    $pageWidth = $pdf->GetPageWidth();
    $pdf->SetY(-18);
    $pdf->SetDrawColor(...MUNICIPAL_GOLD);
    $pdf->Line(10, $pdf->GetY(), $pageWidth - 10, $pdf->GetY());
    $pdf->Ln(2);
    $pdf->SetTextColor(90, 90, 90);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(0, 4, utf8_decode('Av. Juárez No. 20, Col. Centro, Tuxpan, Veracruz  |  Tel. 783 835 0118'), 0, 1, 'C');
    $pdf->Cell(0, 4, utf8_decode('Documento oficial  •  Página ').$pdf->PageNo().'/{nb}', 0, 0, 'C');
    $pdf->SetTextColor(0, 0, 0);
}
