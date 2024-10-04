<?php
error_reporting(E_ALL & ~E_NOTICE);

require_once ('./assets_tambahan/PDFMerger/PDFMerger.php');

use PDFMerger\PDFMerger;
$pdf = new PDFMerger;

$pdf->addPDF('./document/' . $cover_download,);
$pdf->addPDF('./document/' . $data_download,);
$pdf->merge('download', $judul .'.pdf');