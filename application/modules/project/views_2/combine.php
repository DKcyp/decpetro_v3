<?php
error_reporting(E_ALL & ~E_NOTICE);

require_once('./assets_tambahan/fpdf/fpdf.php');
require_once('./assets_tambahan/fpdi/src/autoload.php');

use setasign\Fpdi\Fpdi;

$pdf = new FPDI();


$orientation = substr($orientasi, 0, 1);

if ($kertas != '' && $orientasi != '') {
	$pdf->AddPage($orientation, $kertas);
	$pdf->setSourceFile('./document/' . $cover_download);
	$tplIdxA = $pdf->importPage(1);
	$pdf->useTemplate($tplIdxA, null, null, null);
	if ($kertas == 'A3' && $orientation == 'L') {
		$pdf->Image(FCPATH .('document/qrcode/') . $qr_code, 390, 18, 15, 0, 'PNG');
	} else if ($kertas == 'A3' && $orientation == 'P') {
		$pdf->Image(FCPATH .('document/qrcode/') . $qr_code, 265, 18, 15, 0, 'PNG');
	} else if ($kertas == 'A4' && $orientation == 'L') {
		$pdf->Image(FCPATH .('document/qrcode/') . $qr_code, 265, 18, 15, 0, 'PNG');
	} else if ($kertas == 'A4' && $orientation == 'P') {
		$pdf->Image(FCPATH .('document/qrcode/') . $qr_code, 178, 19, 15, 0, 'PNG');
	} else {
		$pdf->Image(FCPATH .('document/qrcode/') . $qr_code, 160, 20, 15, 0, 'PNG');
	}
} else {
	$pdf->AddPage();
	$pdf->setSourceFile('./document/' . $cover_download);
	$tplIdxA = $pdf->importPage(1);
	$pdf->useTemplate($tplIdxA, null, null, null);
	$pdf->Image(FCPATH .('/document/qrcode/') . $qr_code, 173, 20, 15, 0, 'PNG');
}

for ($i = 1; $i <= $halaman; ++$i) {
	if ($kertas != '' && $orientasi != '') {
		$pdf->AddPage($orientation, $kertas);
		$pdf->setSourceFile('./document/' . $data_download);
		$tplIdx = $pdf->importPage($i);
		$pdf->useTemplate($tplIdx, null, null, null);
		if ($kertas == 'A3' && $orientation == 'L') {
			$pdf->Image(FCPATH .('document/qrcode/') . $qr_code, 390, 18, 15, 0, 'PNG');
		} else if ($kertas == 'A3' && $orientation == 'P') {
			$pdf->Image(FCPATH .('document/qrcode/') . $qr_code, 265, 18, 15, 0, 'PNG');
		} else if ($kertas == 'A4' && $orientation == 'L') {
			$pdf->Image(FCPATH .('document/qrcode/') . $qr_code, 265, 18, 15, 0, 'PNG');
		} else if ($kertas == 'A4' && $orientation == 'P') {
			// $pdf->Image(FCPATH .('document/qrcode/') . $qr_code, 178, 25, 15, 0, 'PNG');
			$pdf->Image(FCPATH .('document/qrcode/') . $qr_code, 175.5, 22, 15, 0, 'PNG');
		} else {
			$pdf->Image(FCPATH .('document/qrcode/') . $qr_code, 160, 20, 15, 0, 'PNG');
		}
	} else {
		$pdf->AddPage();
		$pdf->setSourceFile('./document/' . $data_download);
		$tplIdx = $pdf->importPage($i);
		$pdf->useTemplate($tplIdx, null, null, null);
		$pdf->Image(FCPATH .('/document/qrcode/') . $qr_code, 173, 20, 15, 0, 'PNG');
	}
}

// $pdf->Output($judul . '.pdf', 'I');
$pdf->Output($judul . '.pdf', 'D');
