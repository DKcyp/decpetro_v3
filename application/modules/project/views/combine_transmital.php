<?php
error_reporting(E_ALL & ~E_NOTICE);

// require('./assets_tambahan/fpdf/fpdf.php');
// require('./assets_tambahan/fpdi_default/src/autoload.php');
// require('./assets_tambahan/fpdi/src/autoload.php');

use \setasign\Fpdi\Fpdi;

$pdf = new FPDI();

function addWatermarkCell($x, $y, $text, $position, $pdf, $long = 0)
{
	$pdf->SetAlpha(0.7);
	$pdf->SetTextColor(250, 0, 0);
	$pdf->SetDrawColor(250, 0, 0);
	$pdf->SetLineWidth(0.50);
	if ($long == 1) {
		$pdf->SetFont('Times', 'B', 5);
		$pdf->MultiCell($x, $y, $text, 1, 'J', 0);
	} else {
		$pdf->SetFont('Times', 'B', 8);
		$pdf->MultiCell($x, $y, $text, 1, $position, 0);
	}
	$pdf->SetAlpha(1);
}

if ($pekerjaan_dokumen_status_doc == 'y') {
	$aproved = 'V';
} else if ($pekerjaan_dokumen_status_doc == 'yc') {
	$aprovedwithnote = 'V';
} else if ($pekerjaan_dokumen_status_doc == 'nc') {
	$revisedwithnote = 'V';
} else if ($pekerjaan_dokumen_status_doc == 'n') {
	$revised = 'V';
}

/*cover*/
$pdf->AddPage('P', 'A4');
$pdf->setSourceFile('./document/' . $cover_download);
$tplIdxA = $pdf->importPage(1);
$pdf->useTemplate($tplIdxA, null, null, null);
/*qr code*/
$pdf->Image(FCPATH . ('document/qrcode/') . $qr_code, 190, 10, 15, 0, 'PNG');
/*watermark*/
$pdf->SetXY(125, 30);
addWatermarkCell(80, 5, 'APPROVAL', 'C', $pdf);

$pdf->SetXY(125, 35);
addWatermarkCell(10, 5, $aproved, 'C', $pdf);
$pdf->SetXY(135, 35);
addWatermarkCell(70, 5, 'APPROVED', 'L', $pdf);

$pdf->SetXY(125, 40);
addWatermarkCell(10, 5, $aprovedwithnote, 'C', $pdf);
$pdf->SetXY(135, 40);
addWatermarkCell(70, 5, 'APPROVED WITH MINOR COMMENTS', 'L', $pdf);

$pdf->SetXY(125, 45);
addWatermarkCell(10, 5, $revisedwithnote, 'C', $pdf);
$pdf->SetXY(135, 45);
addWatermarkCell(70, 5, 'TO BE REVISED AS COMMENTS AND RESUBMIT', 'L', $pdf);

$pdf->SetXY(125, 50);
addWatermarkCell(10, 5, $revised, 'C', $pdf);
$pdf->SetXY(135, 50);
addWatermarkCell(70, 5, 'REJECTED', 'L', $pdf);

$pdf->SetXY(125, 55);
addWatermarkCell(80, 2.5, 'SUBMITTAL WAS REVIEWED FOR DESIGN CONFORMITY AND GENERAL CONFORMANCE TO CONTRACT DOCUMENTS ONLY. THE CONTRACTOR IS RESPONSIBLE FOR CONFORMING AND CORRELATING DIMENSIONS AT JOBSITE FOR TOLERANCE, CLEARANCE, QUANTITIES, FABRICATION PROCESSES AND TECHNIQUES OF CONSTRUCTION, COORDINATION OF THEIR WORK WITH OTHER TRADES AND FULL COMPLIANCE WITH CONTRACT DOCUMENTS', 'L', $pdf, 1);

$pdf->SetXY(125, 70);
addWatermarkCell(80, 5, 'BY : ' . $pekerjaan['id_user'] . '  SIGN:' . $pekerjaan['klasifikasi_dokumen_inisial'] . '  DATE:' . date('d/m/Y', strtotime($pekerjaan['pekerjaan_disposisi_transmital_waktu'])) . '', 'C', $pdf);
/*cover*/
/*isi*/
$page = $pdf->setSourceFile('./document/' . $data_download);
if ($pekerjaan_dokumen_jenis == 'Gambar') {
	// /*isi*/
	for ($i = 1; $i <= $page; ++$i) {
		$pdf->AddPage();
		$pdf->setSourceFile('./document/' . $data_download);
		$tplIdx = $pdf->importPage($i);
		$pdf->useTemplate($tplIdx, null, null, null);
		$pdf->Image(FCPATH . ('document/qrcode/') . $qr_code, 190, 10, 15, 0, 'PNG');

		$pdf->SetXY(125, 30);
		addWatermarkCell(80, 5, 'APPROVAL', 'C', $pdf);

		$pdf->SetXY(125, 35);
		addWatermarkCell(10, 5, $aproved, 'C', $pdf);
		$pdf->SetXY(135, 35);
		addWatermarkCell(70, 5, 'APPROVED', 'L', $pdf);

		$pdf->SetXY(125, 40);
		addWatermarkCell(10, 5, $aprovedwithnote, 'C', $pdf);
		$pdf->SetXY(135, 40);
		addWatermarkCell(70, 5, 'APPROVED WITH MINOR COMMENTS', 'L', $pdf);

		$pdf->SetXY(125, 45);
		addWatermarkCell(10, 5, $revisedwithnote, 'C', $pdf);
		$pdf->SetXY(135, 45);
		addWatermarkCell(70, 5, 'TO BE REVISED AS COMMENTS AND RESUBMIT', 'L', $pdf);

		$pdf->SetXY(125, 50);
		addWatermarkCell(10, 5, $revised, 'C', $pdf);
		$pdf->SetXY(135, 50);
		addWatermarkCell(70, 5, 'REJECTED', 'L', $pdf);

		$pdf->SetXY(125, 55);
		addWatermarkCell(80, 2.5, 'SUBMITTAL WAS REVIEWED FOR DESIGN CONFORMITY AND GENERAL CONFORMANCE TO CONTRACT DOCUMENTS ONLY. THE CONTRACTOR IS RESPONSIBLE FOR CONFORMING AND CORRELATING DIMENSIONS AT JOBSITE FOR TOLERANCE, CLEARANCE, QUANTITIES, FABRICATION PROCESSES AND TECHNIQUES OF CONSTRUCTION, COORDINATION OF THEIR WORK WITH OTHER TRADES AND FULL COMPLIANCE WITH CONTRACT DOCUMENTS', 'L', $pdf, 1);

		$pdf->SetXY(125, 70);
		addWatermarkCell(80, 5, 'BY : ' . $pekerjaan['id_user'] . '  SIGN:' . $pekerjaan['klasifikasi_dokumen_inisial'] . '  DATE:' . date('d/m/Y', strtotime($pekerjaan['pekerjaan_disposisi_transmital_waktu'])) . '', 'C', $pdf);
	}
} else {
	for ($i = 1; $i <= $page; ++$i) {
		$pdf->AddPage();
		$pdf->setSourceFile('./document/' . $data_download);
		$tplIdx = $pdf->importPage($i);
		$pdf->useTemplate($tplIdx, null, null, null);
		$pdf->Image(FCPATH . ('document/qrcode/') . $qr_code, 190, 10, 15, 0, 'PNG');
	}
}


$pdf->Output($judul . '.pdf', 'I');
