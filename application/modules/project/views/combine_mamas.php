<?php
error_reporting(E_ALL & ~E_NOTICE);

require('./assets_tambahan/fpdf/fpdf.php');
// require('./assets_tambahan/fpdf/alphapdf.php');
require('./assets_tambahan/fpdi_default/src/autoload.php');
require('./assets_tambahan/fpdi/src/autoload.php');

use \setasign\Fpdi\Fpdi;

$pdf = new FPDI();

function addWatermark($x, $y, $watermarkText, $angle, $pdf, $kertas = null, $orientation = null)
{
	$pdf->SetAlpha(0.40);
	if ($kertas == 'A4') {
		$pdf->SetFont('Arial', 'B', 20);
	} else if ($kertas == 'A3' && $orientation == 'P') {
		$pdf->SetFont('Arial', 'B', 20);
	} else if ($kertas == 'A3' && $orientation == 'L') {
		$pdf->SetFont('Arial', 'B', 30);
	} else {
		$pdf->SetFont('Arial', 'B', 26);
	}
	$angle = $angle * M_PI / 180;
	$c = cos($angle);
	$s = sin($angle);
	$cx = $x * 1;
	$cy = (200 - $y) * 1;
	// Apply rotation and translation
	$pdf->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));


	// Set the border color
	$pdf->SetDrawColor(250, 0, 0);

	// Set the border width
	$borderWidth = 1; // You can adjust this value
	$pdf->SetLineWidth($borderWidth);

	// Calculate the width and height of the watermark text
	$textWidth = $pdf->GetStringWidth($watermarkText) + 4;
	$textHeight = 14; // Adjust this value to change the height of the border
	$marginBottom = 4; // Adjust this value to set the margin at the bottom

	// Draw a rectangle around the text as a border
	$pdf->Rect($x - 1, $y - $textHeight + $marginBottom, $textWidth, $textHeight, 'D');

	// Reset transformation matrix
	$pdf->_out('Q');

	// Set the text color
	$pdf->SetTextColor(250, 0, 0);


	// Add the watermark text
	$pdf->Text($x,  $y, $watermarkText);

	$pdf->SetAlpha(1);
}


$orientation = substr($orientasi, 0, 1);

if ($kertas != '' && $orientasi != '') {
	$pdf->AddPage($orientation, $kertas);

	/*qr code*/
	if ($kertas == 'A3' && $orientation == 'L') {
		$pdf->Image(FCPATH . ('document/qrcode/') . $qr_code, 390, 18, 15, 0, 'PNG');
	} else if ($kertas == 'A3' && $orientation == 'P') {
		$pdf->Image(FCPATH . ('document/qrcode/') . $qr_code, 265, 18, 15, 0, 'PNG');
	} else if ($kertas == 'A4' && $orientation == 'L') {
		$pdf->Image(FCPATH . ('document/qrcode/') . $qr_code, 265, 18, 15, 0, 'PNG');
	} else if ($kertas == 'A4' && $orientation == 'P') {
		$pdf->Image(FCPATH . ('document/qrcode/') . $qr_code, 178, 19, 15, 0, 'PNG');
	} else {
		$pdf->Image(FCPATH . ('document/qrcode/') . $qr_code, 160, 20, 15, 0, 'PNG');
	}
	/*watermark*/
	$pdf->SetFont('Times', 'B', 40);
	$pdf->SetTextColor(250, 0, 0);

	if ($status_dokumen >= '4' && $status_dokumen <= '7'  && $klasifikasi_pekerjaan_kode != 'ifi'  && $klasifikasi_pekerjaan_kode != 'ift') {
		$watermarkText = 'FOR APPROVAL'; /*IFA*/
		if ($kertas == 'A3' && $orientation == 'L') {
			addWatermark(320, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else if ($kertas == 'A3' && $orientation == 'P') {
			addWatermark(225, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else if ($kertas == 'A4' && $orientation == 'L') {
			addWatermark(217, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else if ($kertas == 'A4' && $orientation == 'P') {
			addWatermark(129, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		}
		$pdf->SetXY(25, 25);
	} else if ($status_dokumen >= '4' && $status_dokumen <= '7' && $klasifikasi_pekerjaan_kode == 'ift') {
		$watermarkText = 'FOR TENDER';/*IFT*/
		if ($kertas == 'A3' && $orientation == 'L') {
			addWatermark(320, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else if ($kertas == 'A3' && $orientation == 'P') {
			addWatermark(225, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else if ($kertas == 'A4' && $orientation == 'L') {
			addWatermark(205, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else if ($kertas == 'A4' && $orientation == 'P') {
			addWatermark(129, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else {
		}
		$pdf->SetXY(25, 25);
	} else if (($status_dokumen >= '4' && $status_dokumen <= '7') && ($klasifikasi_pekerjaan_kode == 'ifi')) {
		$watermarkText = 'FOR INFORMATION';/*IFI*/
		if ($kertas == 'A3' && $orientation == 'L') {
			addWatermark(320, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else if ($kertas == 'A3' && $orientation == 'P') {
			addWatermark(225, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else if ($kertas == 'A4' && $orientation == 'L') {
			addWatermark(205, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else if ($kertas == 'A4' && $orientation == 'P') {
			addWatermark(129, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else {
		}
		$pdf->SetXY(25, 25);
	} else if ($status_dokumen >= '8' && $status_dokumen <= '11') {
		$watermarkText = 'FOR CONSTRUCTION';/*IFC*/
		if ($kertas == 'A3' && $orientation == 'L') {
			addWatermark(320, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else if ($kertas == 'A3' && $orientation == 'P') {
			addWatermark(225, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else if ($kertas == 'A4' && $orientation == 'L') {
			addWatermark(205, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else if ($kertas == 'A4' && $orientation == 'P') {
			addWatermark(129, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else {
		}
		$pdf->SetXY(25, 25);
	} else {
		// $watermarkText = '';
		// if ($kertas == 'A3' && $orientation == 'L') {
		// 	addWatermark(2308 52, $watermarkText, 0.2, $pdf);, $kertas
		// } else if ($kertas == 'A3' && $orientation == 'P') {
		// 		addWatermark(209, 52, $watermarkText, 0.2, $pdf, $kertas);
		// } else if ($kertas == 'A4' && $orientation == 'L') {
		// 		addWatermar18230,152, $watermarkText, 0.2, $pdf);
		// } else if ($kertas == 'A4' && $orientation == 'P') {
		// 		addWatermark(129, 51, $watermarkText, 0.2, $pdf, $kertas);
		// } else {
		// }
		$pdf->SetXY(25, 25);
	}

	$pdf->setSourceFile('./document/' . $cover_download);
	$tplIdxA = $pdf->importPage(1);
	$pdf->useTemplate($tplIdxA, null, null, null);
} else {
	$pdf->AddPage();
	$pdf->Image(FCPATH . ('/document/qrcode/') . $qr_code, 173, 20, 15, 0, 'PNG');

	$pdf->SetFont('Times', 'B', 40);
	$pdf->SetTextColor(250, 0, 0);
	if ($status_dokumen >= '4' && $status_dokumen <= '7'  && $klasifikasi_pekerjaan_kode != 'ifi'  && $klasifikasi_pekerjaan_kode != 'ift') {
		$watermarkText = 'FOR APPROVAL'; /*IFA*/
		if ($kertas == 'A3' && $orientation == 'L') {
			addWatermark(320, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else if ($kertas == 'A3' && $orientation == 'P') {
			addWatermark(225, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else if ($kertas == 'A4' && $orientation == 'L') {
			addWatermark(217, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else if ($kertas == 'A4' && $orientation == 'P') {
			addWatermark(129, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		}
		$pdf->SetXY(25, 25);
	} else if ($status_dokumen >= '4' && $status_dokumen <= '7' && $klasifikasi_pekerjaan_kode == 'ift') {
		$watermarkText = 'FOR TENDER';/*IFT*/
		if ($kertas == 'A3' && $orientation == 'L') {
			addWatermark(320, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else if ($kertas == 'A3' && $orientation == 'P') {
			addWatermark(225, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else if ($kertas == 'A4' && $orientation == 'L') {
			addWatermark(205, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else if ($kertas == 'A4' && $orientation == 'P') {
			addWatermark(129, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else {
		}
		$pdf->SetXY(25, 25);
	} else if (($status_dokumen >= '4' && $status_dokumen <= '7') && ($klasifikasi_pekerjaan_kode == 'ifi')) {
		$watermarkText = 'FOR INFORMATION';/*IFI*/
		if ($kertas == 'A3' && $orientation == 'L') {
			addWatermark(320, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else if ($kertas == 'A3' && $orientation == 'P') {
			addWatermark(225, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else if ($kertas == 'A4' && $orientation == 'L') {
			addWatermark(205, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else if ($kertas == 'A4' && $orientation == 'P') {
			addWatermark(129, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else {
		}
		$pdf->SetXY(25, 25);
	} else if ($status_dokumen >= '8' && $status_dokumen <= '11') {
		$watermarkText = 'FOR CONSTRUCTION';/*IFC*/
		if ($kertas == 'A3' && $orientation == 'L') {
			addWatermark(320, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else if ($kertas == 'A3' && $orientation == 'P') {
			addWatermark(225, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else if ($kertas == 'A4' && $orientation == 'L') {
			addWatermark(205, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else if ($kertas == 'A4' && $orientation == 'P') {
			addWatermark(129, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
		} else {
		}
		$pdf->SetXY(25, 25);
	} else {
		// $watermarkText = '';
		// if ($kertas == 'A3' && $orientation == 'L') {
		// 	addWatermark(2308 52, $watermarkText, 0.2, $pdf);, $kertas
		// } else if ($kertas == 'A3' && $orientation == 'P') {
		// 		addWatermark(209, 52, $watermarkText, 0.2, $pdf, $kertas);
		// } else if ($kertas == 'A4' && $orientation == 'L') {
		// 		addWatermar18230,152, $watermarkText, 0.2, $pdf);
		// } else if ($kertas == 'A4' && $orientation == 'P') {
		// 		addWatermark(129, 51, $watermarkText, 0.2, $pdf, $kertas);
		// } else {
		// }
		$pdf->SetXY(25, 25);
	}
	$pdf->setSourceFile('./document/' . $cover_download);
	$tplIdxA = $pdf->importPage(1);
	$pdf->useTemplate($tplIdxA, null, null, null);
}

$halaman = $pdf->setSourceFile('./document/' . $data_download);
for ($i = 1; $i <= $halaman; ++$i) {
	if ($kertas != '' && $orientasi != '') {
		$pdf->AddPage($orientation, $kertas);
		$pdf->setSourceFile('./document/' . $data_download);
		$tplIdx = $pdf->importPage($i);
		$pdf->useTemplate($tplIdx, null, null, null);

		if ($kertas == 'A3' && $orientation == 'L') {
			$pdf->Image(FCPATH . ('document/qrcode/') . $qr_code, 390, 18, 15, 0, 'PNG');
		} else if ($kertas == 'A3' && $orientation == 'P') {
			$pdf->Image(FCPATH . ('document/qrcode/') . $qr_code, 265, 18, 15, 0, 'PNG');
		} else if ($kertas == 'A4' && $orientation == 'L') {
			$pdf->Image(FCPATH . ('document/qrcode/') . $qr_code, 265, 18, 15, 0, 'PNG');
		} else if ($kertas == 'A4' && $orientation == 'P') {
			$pdf->Image(FCPATH . ('document/qrcode/') . $qr_code, 175.5, 22, 15, 0, 'PNG');
		} else {
			$pdf->Image(FCPATH . ('document/qrcode/') . $qr_code, 160, 20, 15, 0, 'PNG');
		}

		$pdf->SetFont('Times', 'B', 40);
		$pdf->SetTextColor(250, 0, 0);
		if ($status_dokumen >= '4' && $status_dokumen <= '7'  && $klasifikasi_pekerjaan_kode != 'ifi'  && $klasifikasi_pekerjaan_kode != 'ift') {
			$watermarkText = 'FOR APPROVAL'; /*IFA*/
			if ($kertas == 'A3' && $orientation == 'L') {
				addWatermark(320, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else if ($kertas == 'A3' && $orientation == 'P') {
				addWatermark(225, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else if ($kertas == 'A4' && $orientation == 'L') {
				addWatermark(217, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else if ($kertas == 'A4' && $orientation == 'P') {
				addWatermark(129, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			}
			$pdf->SetXY(25, 25);
		} else if ($status_dokumen >= '4' && $status_dokumen <= '7' && $klasifikasi_pekerjaan_kode == 'ift') {
			$watermarkText = 'FOR TENDER';/*IFT*/
			if ($kertas == 'A3' && $orientation == 'L') {
				addWatermark(320, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else if ($kertas == 'A3' && $orientation == 'P') {
				addWatermark(225, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else if ($kertas == 'A4' && $orientation == 'L') {
				addWatermark(205, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else if ($kertas == 'A4' && $orientation == 'P') {
				addWatermark(129, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else {
			}
			$pdf->SetXY(25, 25);
		} else if (($status_dokumen >= '4' && $status_dokumen <= '7') && ($klasifikasi_pekerjaan_kode == 'ifi')) {
			$watermarkText = 'FOR INFORMATION';/*IFI*/
			if ($kertas == 'A3' && $orientation == 'L') {
				addWatermark(320, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else if ($kertas == 'A3' && $orientation == 'P') {
				addWatermark(225, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else if ($kertas == 'A4' && $orientation == 'L') {
				addWatermark(205, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else if ($kertas == 'A4' && $orientation == 'P') {
				addWatermark(129, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else {
			}
			$pdf->SetXY(25, 25);
		} else if ($status_dokumen >= '8' && $status_dokumen <= '11') {
			$watermarkText = 'FOR CONSTRUCTION';/*IFC*/
			if ($kertas == 'A3' && $orientation == 'L') {
				addWatermark(320, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else if ($kertas == 'A3' && $orientation == 'P') {
				addWatermark(225, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else if ($kertas == 'A4' && $orientation == 'L') {
				addWatermark(205, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else if ($kertas == 'A4' && $orientation == 'P') {
				addWatermark(129, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else {
			}
			$pdf->SetXY(25, 25);
		} else {
			// $watermarkText = '';/*IFC*/
			// if ($kertas == 'A3' && $orientation == 'L') {
			// 	addWatermark(280,52, $watermarkText, 0.2, $pdf);, $kertas
			// } else if ($kertas == 'A3' && $orientation == 'P') {
			// 	addWatermark(209, 52, $watermarkText, 0.2, $pdf, $kertas);
			// } else if ($kertas == 'A4' && $orientation == 'L') {
			// 	addWatermark(290, 52, $watermarkText, 0.2, $pdf, $kertas);
			// } else if ($kertas == 'A4' && $orientation == 'P') {
			// 	addWatermark(129, 51, $watermarkText, 0.2, $pdf, $kertas);
			// } else {
			// }
			$pdf->SetXY(25, 25);
		}
	} else {
		$pdf->AddPage();
		$pdf->setSourceFile('./document/' . $data_download);
		$tplIdx = $pdf->importPage($i);
		$pdf->useTemplate($tplIdx, null, null, null);
		$pdf->Image(FCPATH . ('/document/qrcode/') . $qr_code, 173, 20, 15, 0, 'PNG');

		$pdf->SetFont('Times', 'B', 40);
		$pdf->SetTextColor(250, 0, 0);
		if ($status_dokumen >= '4' && $status_dokumen <= '7'  && $klasifikasi_pekerjaan_kode != 'ifi'  && $klasifikasi_pekerjaan_kode != 'ift') {
			$watermarkText = 'FOR APPROVAL'; /*IFA*/
			if ($kertas == 'A3' && $orientation == 'L') {
				addWatermark(320, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else if ($kertas == 'A3' && $orientation == 'P') {
				addWatermark(225, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else if ($kertas == 'A4' && $orientation == 'L') {
				addWatermark(217, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else if ($kertas == 'A4' && $orientation == 'P') {
				addWatermark(129, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			}
			$pdf->SetXY(25, 25);
		} else if ($status_dokumen >= '4' && $status_dokumen <= '7' && $klasifikasi_pekerjaan_kode == 'ift') {
			$watermarkText = 'FOR TENDER';/*IFT*/
			if ($kertas == 'A3' && $orientation == 'L') {
				addWatermark(320, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else if ($kertas == 'A3' && $orientation == 'P') {
				addWatermark(225, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else if ($kertas == 'A4' && $orientation == 'L') {
				addWatermark(205, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else if ($kertas == 'A4' && $orientation == 'P') {
				addWatermark(129, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else {
			}
			$pdf->SetXY(25, 25);
		} else if (($status_dokumen >= '4' && $status_dokumen <= '7') && ($klasifikasi_pekerjaan_kode == 'ifi')) {
			$watermarkText = 'FOR INFORMATION';/*IFI*/
			if ($kertas == 'A3' && $orientation == 'L') {
				addWatermark(320, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else if ($kertas == 'A3' && $orientation == 'P') {
				addWatermark(225, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else if ($kertas == 'A4' && $orientation == 'L') {
				addWatermark(205, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else if ($kertas == 'A4' && $orientation == 'P') {
				addWatermark(129, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else {
			}
			$pdf->SetXY(25, 25);
		} else if ($status_dokumen >= '8' && $status_dokumen <= '11') {
			$watermarkText = 'FOR CONSTRUCTION';/*IFC*/
			if ($kertas == 'A3' && $orientation == 'L') {
				addWatermark(320, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else if ($kertas == 'A3' && $orientation == 'P') {
				addWatermark(225, 52, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else if ($kertas == 'A4' && $orientation == 'L') {
				addWatermark(205, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else if ($kertas == 'A4' && $orientation == 'P') {
				addWatermark(129, 51, $watermarkText, 0.2, $pdf, $kertas, $orientation);
			} else {
			}
			$pdf->SetXY(25, 25);
		} else {
			// $watermarkText = '';
			// if ($kertas == 'A3' && $orientation == 'L') {
			// 	addWatermark(290, 52, $watermarkText, 0.2, $pdf, $kertas);
			// } else if ($kertas == 'A3' && $orientation == 'P') {
			// 	addWatermark(209, 52, $watermarkText, 0.2, $pdf, $kertas);
			// } else if ($kertas == 'A4' && $orientation == 'L') {
			// 	addWatermark(195, 51, $watermarkText, 0.2, $pdf, $kertas);
			// } else if ($kertas == 'A4' && $orientation == 'P') {
			// 	addWatermark(129, 51, $watermarkText, 0.2, $pdf, $kertas);
			// } else {
			// }
			$pdf->SetXY(25, 25);
		}
	}
}

$pdf->Output($judul . '.pdf', 'I');
