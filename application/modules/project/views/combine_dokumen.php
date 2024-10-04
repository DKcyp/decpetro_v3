<?php
error_reporting(E_ALL & ~E_NOTICE);

require_once('./assets_tambahan/fpdf/fpdf.php');
require_once('./assets_tambahan/fpdi_default/src/autoload.php');
require_once('./assets_tambahan/fpdi/src/autoload.php');



function addWatermark($x, $y, $watermarkText, $angle, $pdf)
{
    $angle = $angle * M_PI / 180;
    $c = cos($angle);
    $s = sin($angle);
    $cx = $x * 1;
    $cy = (300 - $y) * 1;

    // Apply rotation and translation
    $pdf->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));

    // Set the border color
    $pdf->SetDrawColor(255, 89, 89);

    // Set the border width
    $borderWidth = 1; // You can adjust this value
    $pdf->SetLineWidth($borderWidth);

    // Calculate the width and height of the watermark text
    $textWidth = $pdf->GetStringWidth($watermarkText) + 2;
    $textHeight = 8; // Adjust this value to change the height of the border
    $marginBottom = 3; // Adjust this value to set the margin at the bottom

    // Draw a rectangle around the text as a border
    $pdf->Rect($x, $y - $textHeight + $marginBottom, $textWidth, $textHeight, 'D');

    // Reset transformation matrix
    $pdf->_out('Q');

    // Set the text color
    $pdf->SetTextColor(255, 89, 89);

    // Add the watermark text
    $pdf->Text($x, $y, $watermarkText);
}


use \setasign\Fpdi\Fpdi;

$pdf = new FPDI();

$pdf->AddPage();
$pdf->Image(FCPATH . ('document/qrcode/') . $qr_code, 170, 25, 20, 0, 'PNG');
$pdf->SetFont('Times', 'B', 40);
$pdf->SetTextColor(140, 180, 205);
$pdf->setSourceFile('./document/' . $cover_download);
$tplIdxA = $pdf->importPage(1);
$pdf->useTemplate($tplIdxA, null, null, null);

$pdf->AddPage();
$pdf->Image(FCPATH . ('document/qrcode/') . $qr_code, 178, 19, 15, 0, 'PNG');
$pdf->SetFont('Times', 'B', 40);
$pdf->SetTextColor(140, 180, 205);
$pdf->setSourceFile('./document/' . $list_download);
$tplIdxA = $pdf->importPage(1);
$pdf->useTemplate($tplIdxA, null, null, null);

$pdf->Output($judul . '.pdf', 'D');
