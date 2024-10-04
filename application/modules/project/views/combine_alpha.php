<?php
require('./assets_tambahan/fpdf/alphapdf.php');

$pdf = new AlphaPDF();

$pdf->AddPage();

$pdf->setSourceFile(FCPATH.'/document_baru/'.$judul.'.pdf');

$pdf->SetLineWidth(1.5);

// draw opaque red square
$pdf->SetFillColor(255,0,0);
$pdf->Rect(10,10,40,40,'DF');

// set alpha to semi-transparency
$pdf->SetAlpha(0.5);

// draw green square
$pdf->SetFillColor(0,255,0);
$pdf->Rect(20,20,40,40,'DF');

// print name
$pdf->SetFont('Arial', '', 12);
$pdf->Text(46,68,'Lena');

$pdf->Output();
?>