<?php
error_reporting(E_ALL & ~E_NOTICE);

require_once('./assets_tambahan/fpdf/fpdf.php');
require_once('./assets_tambahan/fpdi/src/autoload.php');

use setasign\Fpdi\Fpdi;

$pdf = new FPDI();

$pekerjaan = $this->db->get('dec.dec_pekerjaan')->result_array();
foreach ($pekerjaan as $pek) {
  $dokumen = $this->db->get_where('dec.dec_pekerjaan_dokumen', array('is_lama' => 'n', 'id_pekerjaan' => $pek['pekerjaan_id']))->result_array();
  foreach ($dokumen as $dok) {
    $id = $dok['pekerjaan_dokumen_id'];
    $doc['pekerjaan_dokumen_nama'] = $dok['pekerjaan_dokumen_nama'];
    $doc['pekerjaan_dokumen_file'] = $dok['pekerjaan_dokumen_file'];
    $doc['pekerjaan_dokumen_jumlah'] = $dok['pekerjaan_dokumen_jumlah'];

    $path = FCPATH.'document/'.$doc['pekerjaan_dokumen_file'];

    $pdftext = file_get_contents($path);
    $num = preg_match_all('/\/Page\W/', $pdftext, $dummy);

    $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_jumlah = '".$num."' WHERE pekerjaan_dokumen_id = '".$id."'");
    echo "<pre>";
    print_r ($this->db->last_query());
    echo "</pre>";
  }
}
