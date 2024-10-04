<?php
error_reporting(E_ALL & ~E_NOTICE);

require('./assets_tambahan/fpdf/fpdf.php');
// require('./assets_tambahan/fpdf/alphapdf.php');
require('./assets_tambahan/fpdi_default/src/autoload.php');
require('./assets_tambahan/fpdi/src/autoload.php');

use \setasign\Fpdi\Fpdi;

$pdf = new FPDI();

function addWatermarkCell($x, $y, $text, $position, $pdf)
{
	$pdf->SetAlpha(0.3);
	$pdf->SetFont('Times', 'B', 16);
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetDrawColor(0, 0, 0);
	$pdf->SetLineWidth(0.75);
	$pdf->Cell($x, $y, $text, 1, 1, $position);
	$pdf->SetAlpha(1);
}

$orientation = substr($orientasi, 0, 1);

if ($kertas != '' && $orientasi != '') {
	$pdf->AddPage($orientation, $kertas);
	/*qr code*/
	if ($kertas == 'A3' && $orientation == 'L') {
		$x=390;
		$y=18;
	} else if ($kertas == 'A3' && $orientation == 'P') {
		$x=265;
		$y=18;
	} else if ($kertas == 'A4' && $orientation == 'L') {
		$x = 265;
		$y = 18;
	} else if ($kertas == 'A4' && $orientation == 'P') {
		$x = 178;
		$y = 19;
	}
	$pdf->Image(FCPATH .('document/qrcode/') . $qr_code, $x, $y, 15, 0, 'PNG');
	/*watermark*/
	$pdf->SetFont('Times', 'B', 40);
	$pdf->SetTextColor(140, 180, 205);

	if($status_dokumen>='4' && $status_dokumen<='7'  && $klasifikasi_pekerjaan_kode != 'ifi'  && $klasifikasi_pekerjaan_kode != 'ift'){
		$watermarkText = 'FOR APPROVAL'; /*IFA*/
	} else if($status_dokumen>='4' && $status_dokumen<='7' && $klasifikasi_pekerjaan_kode == 'ift'){
		$watermarkText = 'FOR TENDER';/*IFT*/
	} else if(($status_dokumen>='4' && $status_dokumen<='7') && ($klasifikasi_pekerjaan_kode == 'ifi')){
		$watermarkText = 'FOR INFORMATION';/*IFI*/
	} else if($status_dokumen>='8' && $status_dokumen<='11'){
		$watermarkText = 'FOR CONSTRUCTION';/*IFC*/
	}

	if ($kertas == 'A3' && $orientation == 'L') {
		$x=230;
		$y=20;
	} else if ($kertas == 'A3' && $orientation == 'P') {
		$x=130;
		$y=20;
	} else if ($kertas == 'A4' && $orientation == 'L') {
		$x=130;
		$y=20;
	} else if ($kertas == 'A4' && $orientation == 'P') {
		$x=30;
		$y=20;
	}

	$pdf->SetXY(40,40);
	addWatermarkCell($x, $y, $watermarkText, 'C', $pdf);


	$pdf->setSourceFile('./document/' . $cover_download);
	$tplIdxA = $pdf->importPage(1);
	$pdf->useTemplate($tplIdxA, null, null, null);

} else {
	$pdf->AddPage();
	$pdf->Image(FCPATH .('/document/qrcode/') . $qr_code, 173, 20, 15, 0, 'PNG');

	$pdf->SetFont('Times', 'B', 40);
	$pdf->SetTextColor(140, 180, 205);
	
	if($status_dokumen>='4' && $status_dokumen<='7'  && $klasifikasi_pekerjaan_kode != 'ifi'  && $klasifikasi_pekerjaan_kode != 'ift'){
		$watermarkText = 'FOR APPROVAL'; /*IFA*/
	} else if($status_dokumen>='4' && $status_dokumen<='7' && $klasifikasi_pekerjaan_kode == 'ift'){
		$watermarkText = 'FOR TENDER';/*IFT*/
	} else if(($status_dokumen>='4' && $status_dokumen<='7') && ($klasifikasi_pekerjaan_kode == 'ifi')){
		$watermarkText = 'FOR INFORMATION';/*IFI*/
	} else if($status_dokumen>='8' && $status_dokumen<='11'){
		$watermarkText = 'FOR CONSTRUCTION';/*IFC*/
	}

	if ($kertas == 'A3' && $orientation == 'L') {
		$x=230;
		$y=20;
	} else if ($kertas == 'A3' && $orientation == 'P') {
		$x=130;
		$y=20;
	} else if ($kertas == 'A4' && $orientation == 'L') {
		$x=130;
		$y=20;
	} else if ($kertas == 'A4' && $orientation == 'P') {
		$x=30;
		$y=20;
	}

	$pdf->SetXY(40,40);
	addWatermarkCell($x, $y, $watermarkText, 'C', $pdf);
	$pdf->setSourceFile('./document/' . $cover_download);
	$tplIdxA = $pdf->importPage(1);
	$pdf->useTemplate($tplIdxA, null, null, null);
}

for ($i = 1; $i <= $halaman; ++$i) {
	if ($kertas != '' && $orientasi != '') {
		if ($kertas == 'A3' && $orientation == 'L') {
			$x=390;
			$y=18;
		} else if ($kertas == 'A3' && $orientation == 'P') {
			$x=265;
			$y=18;
		} else if ($kertas == 'A4' && $orientation == 'L') {
			$x = 265;
			$y = 18;
		} else if ($kertas == 'A4' && $orientation == 'P') {
			$x = 178;
			$y = 19;
		}
		$pdf->Image(FCPATH .('document/qrcode/') . $qr_code, $x, $y, 15, 0, 'PNG');
		/*watermark*/
		$pdf->SetFont('Times', 'B', 40);
		$pdf->SetTextColor(140, 180, 205);

		if($status_dokumen>='4' && $status_dokumen<='7'  && $klasifikasi_pekerjaan_kode != 'ifi'  && $klasifikasi_pekerjaan_kode != 'ift'){
			$watermarkText = 'FOR APPROVAL'; /*IFA*/
		} else if($status_dokumen>='4' && $status_dokumen<='7' && $klasifikasi_pekerjaan_kode == 'ift'){
			$watermarkText = 'FOR TENDER';/*IFT*/
		} else if(($status_dokumen>='4' && $status_dokumen<='7') && ($klasifikasi_pekerjaan_kode == 'ifi')){
			$watermarkText = 'FOR INFORMATION';/*IFI*/
		} else if($status_dokumen>='8' && $status_dokumen<='11'){
			$watermarkText = 'FOR CONSTRUCTION';/*IFC*/
		}

		if ($kertas == 'A3' && $orientation == 'L') {
			$x=230;
			$y=20;
		} else if ($kertas == 'A3' && $orientation == 'P') {
			$x=130;
			$y=20;
		} else if ($kertas == 'A4' && $orientation == 'L') {
			$x=130;
			$y=20;
		} else if ($kertas == 'A4' && $orientation == 'P') {
			$x=30;
			$y=20;
		}

		$pdf->SetXY(40,40);
		addWatermarkCell($x, $y, $watermarkText, 'C', $pdf);
	} else {
		$pdf->AddPage();
		$pdf->setSourceFile('./document/' . $data_download);
		$tplIdx = $pdf->importPage($i);
		$pdf->useTemplate($tplIdx, null, null, null);
		$pdf->Image(FCPATH .('/document/qrcode/') . $qr_code, 173, 20, 15, 0, 'PNG');

		$pdf->SetFont('Times', 'B', 40);
		$pdf->SetTextColor(140, 180, 205);
		if($status_dokumen>='4' && $status_dokumen<='7'  && $klasifikasi_pekerjaan_kode != 'ifi'  && $klasifikasi_pekerjaan_kode != 'ift'){
			$watermarkText = 'FOR APPROVAL'; /*IFA*/
		} else if($status_dokumen>='4' && $status_dokumen<='7' && $klasifikasi_pekerjaan_kode == 'ift'){
			$watermarkText = 'FOR TENDER';/*IFT*/
		} else if(($status_dokumen>='4' && $status_dokumen<='7') && ($klasifikasi_pekerjaan_kode == 'ifi')){
			$watermarkText = 'FOR INFORMATION';/*IFI*/
		} else if($status_dokumen>='8' && $status_dokumen<='11'){
			$watermarkText = 'FOR CONSTRUCTION';/*IFC*/
		}

		if ($kertas == 'A3' && $orientation == 'L') {
			$x=230;
			$y=20;
		} else if ($kertas == 'A3' && $orientation == 'P') {
			$x=130;
			$y=20;
		} else if ($kertas == 'A4' && $orientation == 'L') {
			$x=130;
			$y=20;
		} else if ($kertas == 'A4' && $orientation == 'P') {
			$x=30;
			$y=20;
		}

		$pdf->SetXY(40,40);
		addWatermarkCell($x, $y, $watermarkText, 'C', $pdf);
	}
}

$pdf->Output($judul . '.pdf', 'I');
