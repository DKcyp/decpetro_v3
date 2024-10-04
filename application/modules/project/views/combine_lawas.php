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
    $pdf->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, - $s, $c, $cx, $cy, - $cx, - $cy));
    
    // Set the border color
    $pdf->SetDrawColor(255, 89, 89);
    
    // Set the border width
    $borderWidth = 1; // You can adjust this value
    $pdf->SetLineWidth($borderWidth);

    // Calculate the width and height of the watermark text
    $textWidth = $pdf->GetStringWidth($watermarkText) + 2;
    $textHeight = 14; // Adjust this value to change the height of the border
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

$orientation = substr($orientasi, 0, 1);

if ($kertas != '' && $orientasi != '') {
	$pdf->AddPage($orientation, $kertas);
	/*qr code*/
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
	/*watermark*/
	$pdf->SetFont('Times', 'B', 40);
	$pdf->SetTextColor(140, 180, 205);
	if($status_dokumen>='4' && $status_dokumen<='7'  && $klasifikasi_pekerjaan_kode != 'ifi'  && $klasifikasi_pekerjaan_kode != 'ift'){
		$watermarkText = 'FOR APPROVAL'; /*IFA*/
		if ($kertas == 'A3' && $orientation == 'L') {
			addWatermark(230, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A3' && $orientation == 'P') {
			addWatermark(130, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A4' && $orientation == 'L') {
			addWatermark(130, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A4' && $orientation == 'P') {
			addWatermark(30, 52, $watermarkText, 0.2, $pdf);
		} else {
		}
		$pdf->SetXY(25, 25);
	} else if($status_dokumen>='4' && $status_dokumen<='7' && $klasifikasi_pekerjaan_kode == 'ift'){
		$watermarkText = 'FOR TENDER';/*IFT*/
		if ($kertas == 'A3' && $orientation == 'L') {
			addWatermark(230, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A3' && $orientation == 'P') {
			addWatermark(130, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A4' && $orientation == 'L') {
			addWatermark(130, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A4' && $orientation == 'P') {
			addWatermark(30, 52, $watermarkText, 0.2, $pdf);
		} else {
		}
		$pdf->SetXY(25, 25);
	} else if(($status_dokumen>='4' && $status_dokumen<='7') && ($klasifikasi_pekerjaan_kode == 'ifi')){
		$watermarkText = 'FOR INFORMATION';/*IFI*/
		if ($kertas == 'A3' && $orientation == 'L') {
			addWatermark(230, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A3' && $orientation == 'P') {
			addWatermark(130, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A4' && $orientation == 'L') {
			addWatermark(130, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A4' && $orientation == 'P') {
			addWatermark(30, 52, $watermarkText, 0.2, $pdf);
		} else {
		}
		$pdf->SetXY(25, 25);
	} else if($status_dokumen>='8' && $status_dokumen<='11'){
		$watermarkText = 'FOR CONSTRUCTION';/*IFC*/
		if ($kertas == 'A3' && $orientation == 'L') {
			addWatermark(230, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A3' && $orientation == 'P') {
			addWatermark(130, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A4' && $orientation == 'L') {
			addWatermark(130, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A4' && $orientation == 'P') {
			addWatermark(30, 52, $watermarkText, 0.2, $pdf);
		} else {
		}
		$pdf->SetXY(25, 25);
	}else{
		// $watermarkText = '';
		// if ($kertas == 'A3' && $orientation == 'L') {
		// 	addWatermark(230, 52, $watermarkText, 0.2, $pdf);
		// } else if ($kertas == 'A3' && $orientation == 'P') {
		// 		addWatermark(130, 52, $watermarkText, 0.2, $pdf);
		// } else if ($kertas == 'A4' && $orientation == 'L') {
		// 		addWatermark(130, 52, $watermarkText, 0.2, $pdf);
		// } else if ($kertas == 'A4' && $orientation == 'P') {
		// 		addWatermark(30, 52, $watermarkText, 0.2, $pdf);
		// } else {
		// }
		$pdf->SetXY(25, 25);
	}

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
		if ($kertas == 'A3' && $orientation == 'L') {
			addWatermark(230, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A3' && $orientation == 'P') {
			addWatermark(130, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A4' && $orientation == 'L') {
			addWatermark(130, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A4' && $orientation == 'P') {
			addWatermark(30, 52, $watermarkText, 0.2, $pdf);
		} else {
		}
		$pdf->SetXY(25, 25);
	} else if($status_dokumen>='4' && $status_dokumen<='7' && $klasifikasi_pekerjaan_kode == 'ift'){
		$watermarkText = 'FOR TENDER';/*IFT*/
		if ($kertas == 'A3' && $orientation == 'L') {
			addWatermark(230, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A3' && $orientation == 'P') {
			addWatermark(130, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A4' && $orientation == 'L') {
			addWatermark(130, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A4' && $orientation == 'P') {
			addWatermark(30, 52, $watermarkText, 0.2, $pdf);
		} else {
		}
		$pdf->SetXY(25, 25);
	} else if(($status_dokumen>='4' && $status_dokumen<='7') && ($klasifikasi_pekerjaan_kode == 'ifi')){
		$watermarkText = 'FOR INFORMATION';/*IFI*/
		if ($kertas == 'A3' && $orientation == 'L') {
			addWatermark(230, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A3' && $orientation == 'P') {
			addWatermark(130, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A4' && $orientation == 'L') {
			addWatermark(130, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A4' && $orientation == 'P') {
			addWatermark(30, 52, $watermarkText, 0.2, $pdf);
		} else {
		}
		$pdf->SetXY(25, 25);
	} else if($status_dokumen>='8' && $status_dokumen<='11'){
		$watermarkText = 'FOR CONSTRUCTION';/*IFC*/
		if ($kertas == 'A3' && $orientation == 'L') {
			addWatermark(230, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A3' && $orientation == 'P') {
			addWatermark(130, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A4' && $orientation == 'L') {
			addWatermark(130, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A4' && $orientation == 'P') {
			addWatermark(30, 52, $watermarkText, 0.2, $pdf);
		} else {
		}
		$pdf->SetXY(25, 25);
	}else{
		// $watermarkText = '';
		// if ($kertas == 'A3' && $orientation == 'L') {
		// 	addWatermark(230, 52, $watermarkText, 0.2, $pdf);
		// } else if ($kertas == 'A3' && $orientation == 'P') {
		// 		addWatermark(130, 52, $watermarkText, 0.2, $pdf);
		// } else if ($kertas == 'A4' && $orientation == 'L') {
		// 		addWatermark(130, 52, $watermarkText, 0.2, $pdf);
		// } else if ($kertas == 'A4' && $orientation == 'P') {
		// 		addWatermark(30, 52, $watermarkText, 0.2, $pdf);
		// } else {
		// }
		$pdf->SetXY(25, 25);
	}
	$pdf->setSourceFile('./document/' . $cover_download);
	$tplIdxA = $pdf->importPage(1);
	$pdf->useTemplate($tplIdxA, null, null, null);
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
			$pdf->Image(FCPATH .('document/qrcode/') . $qr_code, 175.5, 22, 15, 0, 'PNG');
		} else {
			$pdf->Image(FCPATH .('document/qrcode/') . $qr_code, 160, 20, 15, 0, 'PNG');
		}

		$pdf->SetFont('Times', 'B', 40);
		$pdf->SetTextColor(140, 180, 205);
		if($status_dokumen>='4' && $status_dokumen<='7'  && $klasifikasi_pekerjaan_kode != 'ifi'  && $klasifikasi_pekerjaan_kode != 'ift'){
			$watermarkText = 'FOR APPROVAL'; /*IFA*/
			if ($kertas == 'A3' && $orientation == 'L') {
				addWatermark(230,52, $watermarkText, 0.2, $pdf);
			} else if ($kertas == 'A3' && $orientation == 'P') {
				addWatermark(130, 52, $watermarkText, 0.2, $pdf);
			} else if ($kertas == 'A4' && $orientation == 'L') {
				addWatermark(130, 52, $watermarkText, 0.2, $pdf);
			} else if ($kertas == 'A4' && $orientation == 'P') {
				addWatermark(30, 52, $watermarkText, 0.2, $pdf);
			} else {
			}
			$pdf->SetXY(25, 25);
		} else if($status_dokumen>='4' && $status_dokumen<='7' && $klasifikasi_pekerjaan_kode == 'ift'){
			$watermarkText = 'FOR TENDER';/*IFT*/
		if ($kertas == 'A3' && $orientation == 'L') {
			addWatermark(230, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A3' && $orientation == 'P') {
			addWatermark(130, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A4' && $orientation == 'L') {
			addWatermark(130, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A4' && $orientation == 'P') {
			addWatermark(30, 52, $watermarkText, 0.2, $pdf);
		} else {
		}
		$pdf->SetXY(25, 25);
		} else if(($status_dokumen>='4' && $status_dokumen<='7') && ($klasifikasi_pekerjaan_kode == 'ifi')){
			$watermarkText = 'FOR INFORMATION';/*IFI*/
			if ($kertas == 'A3' && $orientation == 'L') {
				addWatermark(230, 52, $watermarkText, 0.2, $pdf);
			} else if ($kertas == 'A3' && $orientation == 'P') {
				addWatermark(130, 52, $watermarkText, 0.2, $pdf);
			} else if ($kertas == 'A4' && $orientation == 'L') {
				addWatermark(130, 52, $watermarkText, 0.2, $pdf);
			} else if ($kertas == 'A4' && $orientation == 'P') {
				addWatermark(30, 52, $watermarkText, 0.2, $pdf);
			} else {
			}
			$pdf->SetXY(25, 25);
		} else if($status_dokumen>='8' && $status_dokumen<='11'){
			$watermarkText = 'FOR CONSTRUCTION';/*IFC*/
			if ($kertas == 'A3' && $orientation == 'L') {
				addWatermark(230,52, $watermarkText, 0.2, $pdf);
			} else if ($kertas == 'A3' && $orientation == 'P') {
				addWatermark(130, 52, $watermarkText, 0.2, $pdf);
			} else if ($kertas == 'A4' && $orientation == 'L') {
				addWatermark(130, 52, $watermarkText, 0.2, $pdf);
			} else if ($kertas == 'A4' && $orientation == 'P') {
				addWatermark(30, 52, $watermarkText, 0.2, $pdf);
			} else {
			}
			$pdf->SetXY(25, 25);
		}else{
			// $watermarkText = '';/*IFC*/
			// if ($kertas == 'A3' && $orientation == 'L') {
			// 	addWatermark(230,52, $watermarkText, 0.2, $pdf);
			// } else if ($kertas == 'A3' && $orientation == 'P') {
			// 	addWatermark(130, 52, $watermarkText, 0.2, $pdf);
			// } else if ($kertas == 'A4' && $orientation == 'L') {
			// 	addWatermark(130, 52, $watermarkText, 0.2, $pdf);
			// } else if ($kertas == 'A4' && $orientation == 'P') {
			// 	addWatermark(30, 52, $watermarkText, 0.2, $pdf);
			// } else {
			// }
			$pdf->SetXY(25, 25);
		}

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
			if ($kertas == 'A3' && $orientation == 'L') {
				addWatermark(230,52, $watermarkText, 0.2, $pdf);
			} else if ($kertas == 'A3' && $orientation == 'P') {
				addWatermark(130, 52, $watermarkText, 0.2, $pdf);
			} else if ($kertas == 'A4' && $orientation == 'L') {
				addWatermark(130, 52, $watermarkText, 0.2, $pdf);
			} else if ($kertas == 'A4' && $orientation == 'P') {
				addWatermark(30, 52, $watermarkText, 0.2, $pdf);
			} else {
			}
			$pdf->SetXY(25, 25);
		} else if($status_dokumen>='4' && $status_dokumen<='7' && $klasifikasi_pekerjaan_kode == 'ift'){
			$watermarkText = 'FOR TENDER';/*IFT*/
		if ($kertas == 'A3' && $orientation == 'L') {
			addWatermark(230, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A3' && $orientation == 'P') {
			addWatermark(130, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A4' && $orientation == 'L') {
			addWatermark(130, 52, $watermarkText, 0.2, $pdf);
		} else if ($kertas == 'A4' && $orientation == 'P') {
			addWatermark(30, 52, $watermarkText, 0.2, $pdf);
		} else {
		}
		$pdf->SetXY(25, 25);
		} else if(($status_dokumen>='4' && $status_dokumen<='7') && ($klasifikasi_pekerjaan_kode == 'ifi')){
			$watermarkText = 'FOR INFORMATION';/*IFI*/
			if ($kertas == 'A3' && $orientation == 'L') {
				addWatermark(230, 52, $watermarkText, 0.2, $pdf);
			} else if ($kertas == 'A3' && $orientation == 'P') {
				addWatermark(130, 52, $watermarkText, 0.2, $pdf);
			} else if ($kertas == 'A4' && $orientation == 'L') {
				addWatermark(130, 52, $watermarkText, 0.2, $pdf);
			} else if ($kertas == 'A4' && $orientation == 'P') {
				addWatermark(30, 52, $watermarkText, 0.2, $pdf);
			} else {
			}
		$pdf->SetXY(25, 25);
		} else if($status_dokumen>='8' && $status_dokumen<='11'){
			$watermarkText = 'FOR CONSTRUCTION';/*IFC*/
			if ($kertas == 'A3' && $orientation == 'L') {
				addWatermark(230,52, $watermarkText, 0.2, $pdf);
			} else if ($kertas == 'A3' && $orientation == 'P') {
				addWatermark(130, 52, $watermarkText, 0.2, $pdf);
			} else if ($kertas == 'A4' && $orientation == 'L') {
				addWatermark(130, 52, $watermarkText, 0.2, $pdf);
			} else if ($kertas == 'A4' && $orientation == 'P') {
				addWatermark(30, 52, $watermarkText, 0.2, $pdf);
			} else {
			}
			$pdf->SetXY(25, 25);
		}else{
			// $watermarkText = '';
			// if ($kertas == 'A3' && $orientation == 'L') {
			// 	addWatermark(230, 52, $watermarkText, 0.2, $pdf);
			// } else if ($kertas == 'A3' && $orientation == 'P') {
			// 	addWatermark(130, 52, $watermarkText, 0.2, $pdf);
			// } else if ($kertas == 'A4' && $orientation == 'L') {
			// 	addWatermark(130, 52, $watermarkText, 0.2, $pdf);
			// } else if ($kertas == 'A4' && $orientation == 'P') {
			// 	addWatermark(30, 52, $watermarkText, 0.2, $pdf);
			// } else {
			// }
			$pdf->SetXY(25, 25);
		}
	}
}

$pdf->Output($judul . '.pdf', 'D');
