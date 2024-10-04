<?php
error_reporting(E_ALL & ~E_NOTICE);

require_once('./assets_tambahan/fpdi/src/autoload.php');
require_once('./assets_tambahan/fpdf/fpdf.php');

use setasign\Fpdi\Fpdi;
$pdf = new FPDI();

if ($kertas == 'A3') {
	$pdf->AddPage('L', 'A3');
	$pdf->setSourceFile('./document/' . $cover_download);
	$tplIdxA = $pdf->importPage(1);
	$pdf->useTemplate($tplIdxA, null, null, null);
	$pdf->Image(base_url('document/').$qrcode, 375, 18, 15, 0, 'PNG');
} else {
	$pdf->AddPage();
	$pdf->setSourceFile('./document/' . $cover_download);
	$tplIdxA = $pdf->importPage(1);
	$pdf->useTemplate($tplIdxA, 10, 10, 200);
	$pdf->Image(base_url('document/').$qrcode, 177, 20, 15, 0, 'PNG');
}

for ($i = 1; $i <= $halaman; ++$i) {
	if ($kertas == 'A3') {
		$pdf->AddPage('L', 'A3');
		$pdf->setSourceFile('./document/' . $data_download);
		$tplIdx = $pdf->importPage($i);
		$pdf->useTemplate($tplIdx, null, null, null);
		$pdf->Image(base_url('document/').$qrcode, 400, 6, 15, 0, 'PNG');
	} else {
		$pdf->AddPage();
		$pdf->setSourceFile('./document/' . $data_download);
		$tplIdx = $pdf->importPage($i);
		$pdf->useTemplate($tplIdx, 10, 10, 200);
		$pdf->Image(base_url('document/').$qrcode, 173, 33, 15, 0, 'PNG');
	}
}

$pdf->Output($judul.'.pdf', 'I');
// $pdf->Output($judul.'.pdf', 'D');