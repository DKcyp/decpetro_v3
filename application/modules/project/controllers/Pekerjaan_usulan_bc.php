<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(FCPATH . 'assets_tambahan/ghostscript/vendor/autoload.php');

use Xthiago\PDFVersionConverter\Guesser\RegexGuesser;
use Symfony\Component\Filesystem\Filesystem;
use Xthiago\PDFVersionConverter\Converter\GhostscriptConverterCommand;
use Xthiago\PDFVersionConverter\Converter\GhostscriptConverter;

class Pekerjaan_usulan extends MX_Controller
{
 public function __construct()
 {
   parent::__construct();
   $this->load->library('template');
   $CI = &get_instance();
   $sesi = $this->session->userdata();
   /* admin sistem */
   $this->admin_sistem = $this->db->query("SELECT * FROM global.global_admin WHERE admin_nik = '" . $sesi['pegawai_nik'] . "'")->row_array();
   if (!empty($this->admin_sistem)) {
    $this->admin_sistemnya = $this->admin_sistem['admin_nik'];
  } else {
    $this->admin_sistemnya = '0';
  }
  /* admin sistem */
  /* admin bagian */
  $this->admin_bagian = $this->db->query("SELECT * FROM global.global_admin_bagian WHERE admin_bagian_nik = '" . $sesi['pegawai_nik'] . "'")->row_array();
  if (!empty($this->admin_bagian)) {
    $this->admin_bagiannya = $this->admin_bagian['admin_bagian_nik'];
    $this->id_bagiannya = $this->admin_bagian['id_bagian'];
  } else {
    $this->admin_bagiannya = '0';
    $this->id_bagiannya = '0';
  }
  /* admin bagian */
  if (empty($sesi['pegawai_nik'])) {
    redirect('tampilan');
  }
  $this->load->model('M_pekerjaan');
  $this->load->model('master/M_user');
  $this->load->model('master/M_klasifikasi_pekerjaan');
  $this->load->model('master/M_kategori_pekerjaan');
  $this->load->model('master/M_departemen');
  $this->load->library('mailer');
  $this->load->library('mailer_api');
}

/* INDEX */
/* Index Pekerjaan Usulan */
public function index()
{
	$data = $this->session->userdata();
	$this->template->template_master('project/pekerjaan_usulan', $data);
}
/* Index Pekerjaan Usulan */


/* Index Pekerjaan Detail */
public function detailPekerjaan()
{
	$param['pekerjaan_id'] = preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get_post('pekerjaan_id'));
	$data = array();
	$data = $this->session->userdata();
	$data['pekerjaan'] = $this->M_pekerjaan->getPekerjaan($param);
	$this->load->view('tampilan/header', $data, FALSE);
	$this->load->view('tampilan/sidebar', $data, FALSE);
	$this->load->view('project/detail_pekerjaan_usulan', $data);
	$this->load->view('tampilan/footer', $data, FALSE);
}
/* Index Pekerjaan Detail */

/*Index Dokumen Aksi*/
public function dokumenAksi()
{
	$param['pekerjaan_dokumen_id'] = $this->input->get('pekerjaan_dokumen_id');
	$data = array();
	$data = $this->session->userdata();
	if ($this->input->get('transmital') == 'y') {
    $data['dokumen'] = $this->M_pekerjaan->getDokumenAksiTransmital($param);
  } else {
    $data['dokumen'] = $this->M_pekerjaan->getDokumenAksi($param);
  }
  $this->load->view('tampilan/header', $data, FALSE);
  $this->load->view('tampilan/sidebar', $data, FALSE);
  $this->load->view('project/dokumen_aksi', $data);
  $this->load->view('tampilan/footer', $data, FALSE);
}
/*Index Dokumen Aksi*/
/* INDEX */


/* PEKERJAAN USULAN */
/* GET */
/* Get Pekerjaan Ususlan */
public function getPekerjaanUsulan()
{
  if($this->input->get('id_user')){
    $session = $this->db->get_where('global.global_pegawai',['pegawai_nik'=>$this->input->get('id_user')])->row_array();
  }else{
   $session = $this->session->userdata();
 }

 $param_detail['pekerjaan_id'] = $this->input->get('pekerjaan_id');
 $split = explode(',', $this->input->get('pekerjaan_status'));
 $param['pekerjaan_status'] = $split;
 $param['pekerjaan_id'] = $this->input->get('pekerjaan_id');
 $param['id_user'] = $this->input->get_post('id_user_cari');
 $param['klasifikasi_pekerjaan_id'] = $this->input->get_post('klasifikasi_pekerjaan_id');
 $param['klasifikasi_pekerjaan_id_non_rkap'] = $this->input->get_post('klasifikasi_pekerjaan_id_non_rkap');
 $param['tahun'] = $this->input->get_post('tahun');
 if (($split[0]>=8 && $split[0]<=10 && $this->input->get('pekerjaan_jenis')=='IFI') || ($split[0] >= 11 && $split[0] <= 13)) {
  $param['pekerjaan_jenis'] = strtolower($this->input->get('pekerjaan_jenis'));
}else if($split[0]>=8 && $split[0]<='10' && $this->input->get('pekerjaan_jenis')=='IFA'){
 $param['pekerjaan_jenis_ifa'] = '1';
}


$sql_pic = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan WHERE pic='" . $session['pegawai_nik'] . "'");
$data_pic = $sql_pic->row_array();

if ($session['pegawai_nik'] != $this->admin_sistemnya) {
 if ($this->id_bagiannya != '0') {
   $param['id_bagian'] = $this->id_bagiannya;
   $param['pic_bagian'] = $session['pegawai_nik'];
 } else if ($data_pic['total'] > 0) {
   $param['user_pic'] = $session['pegawai_nik'];
 } else {
   $param['user_disposisi'] = $session['pegawai_nik'];
 }
}

$data = array();

if ($param_detail['pekerjaan_id'] != null) {
 $data = $this->M_pekerjaan->getPekerjaan($param_detail);
 echo json_encode($data);
} else {
 if (empty($param['id_user'])) {
   $sql_pekerjaan = $this->M_pekerjaan->getPekerjaan($param);

   foreach ($sql_pekerjaan as $value) {
    foreach ($value as $key => $val) {
      $isi[$key] = $val;
    }
    $sql_total = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '" . $value['pekerjaan_status'] . "' AND id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND  id_user = '" . $session['pegawai_nik'] . "' and is_aktif = 'y' ");
    $isi_total = $sql_total->row_array();

    $sql_proses = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '$value[pekerjaan_status]' AND id_pekerjaan = '$value[pekerjaan_id]' AND  id_user = '$session[pegawai_nik]' ORDER BY pekerjaan_disposisi_status DESC");
    $data_proses = $sql_proses->row_array();

    $proses_perencana_ifc = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '11' AND id_user='" . $session['pegawai_nik'] . "' AND id_pekerjaan='" . $value['pekerjaan_id'] . "' AND is_proses='y'")->num_rows();
    $proses_avp = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '4' AND id_pekerjaan='" . $value['pekerjaan_id'] . "' AND id_user = '" . $session['pegawai_nik'] . "' AND is_proses = 'y'")->num_rows();
    $proses_avp_belum = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '4' AND id_pekerjaan='" . $value['pekerjaan_id'] . "' AND is_proses is  null")->num_rows();
    $koor_baru = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '4' AND id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND id_koor_baru is not null")->num_rows();

    if ($proses_avp_belum == '1') {
      $data_belum = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE is_proses is null AND pekerjaan_disposisi_status = '4' AND id_pekerjaan='" . $value['pekerjaan_id'] . "'")->row_array();
      $bagian = $this->db->query("SELECT * FROM global.global_bagian a LEFT JOIN global.global_bagian_detail b ON b.id_bagian  = a.bagian_id WHERE id_pegawai = '" . $data_belum['id_user'] . "'")->row_array();
    }

    $sql_allow = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND  id_user = '" . $session['pegawai_nik'] . "' and is_aktif = 'y' ");
    $isi_allow = $sql_allow->row_array();

    $sql_ajuan_extend = $this->db->query("SELECT * FROM dec.dec_pekerjaan_extend WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND extend_status='0'");
    $isi_ajuan_extend = $sql_ajuan_extend->row_array();

    $sql_extend = $this->db->query("SELECT * FROM dec.dec_pekerjaan_extend WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND extend_status='1'");
    $isi_extend = $sql_extend->row_array();

    $isi_vp = $this->db->get_where('dec.dec_pekerjaan_disposisi',['id_pekerjaan'=>$value['pekerjaan_id'],'pekerjaan_disposisi_status'=>'3','id_user'=>$session['pegawai_nik']])->num_rows();

    $isi_avp = $this->db->get_where('dec.dec_pekerjaan_disposisi',['id_pekerjaan'=>$value['pekerjaan_id'],'pekerjaan_disposisi_status'=>'4','id_user'=>$session['pegawai_nik']])->num_rows();
    /* Tambahan */

    $isi['extend_ajuan_tanggal'] = (!empty($isi_ajuan_extend)) ? $isi_ajuan_extend['extend_tanggal'] : '';
    $isi['extend_ajuan_status'] = (!empty($isi_ajuan_extend)) ? $isi_ajuan_extend['extend_status'] : '';

    $isi['extend_tanggal'] = (!empty($isi_extend)) ? $isi_extend['extend_tanggal'] : '';
    $isi['extend_status'] = (!empty($isi_extend)) ? $isi_extend['extend_status'] : '';

    $isi['is_allow'] = ($isi_allow['total'] > 0 || $session['pegawai_nik'] == $this->admin_sistemnya) ? 'y' : 'n';

    $isi['milik'] = ($isi_total['total'] > 0 || ($value['pic'] == $session['pegawai_nik'] && ($value['pekerjaan_status'] == '0' || $value['pekerjaan_status'] == '-'))) ? 'y' : 'n';

    $isi['is_proses'] = ($isi_total['total'] > 0 && $data_proses['is_proses']) ? $data_proses['is_proses'] : null;
    $isi['is_disposisi_aktif'] = ($isi_total['total'] > 0 && $data_proses['is_aktif'] == 'y') ? 'y' : 'n';

    $isi['proses_perencana_ifc'] = $proses_perencana_ifc;
    $isi['proses_avp'] = $proses_avp;
    $isi['proses_avp_belum'] = $proses_avp_belum;
    $isi['bagian_nama'] = ($proses_avp_belum == '1') ? $bagian['bagian_nama'] : '';
    $isi['koor_baru'] = $koor_baru;
    $isi['is_vp'] = ($isi_vp>0) ? 'y' : 'n';
    $isi['is_avp'] = ($isi_avp>0) ? 'y' : 'n';

    /* tambahan */

    array_push($data, $isi);
  }
  echo json_encode($data);
} else {
 foreach ($this->M_pekerjaan->getPekerjaanDispo($param) as $value) {
  foreach ($value as $key => $val) {
    $isi[$key] = $val;
  }
  $sql_total = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '" . $value['pekerjaan_status'] . "' AND id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND  id_user = '" . $session['pegawai_nik'] . "' and is_aktif = 'y' ");
  $isi_total = $sql_total->row_array();

  $sql_proses = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '$value[pekerjaan_status]' AND id_pekerjaan = '$value[pekerjaan_id]' AND  id_user = '$session[pegawai_nik]' ORDER BY pekerjaan_disposisi_status DESC");
  $data_proses = $sql_proses->row_array();

  $proses_perencana_ifc = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '11' AND id_user='" . $session['pegawai_nik'] . "' AND id_pekerjaan='" . $value['pekerjaan_id'] . "' AND is_proses='y'")->num_rows();
  $proses_avp = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '4' AND id_pekerjaan='" . $value['pekerjaan_id'] . "' AND id_user = '" . $session['pegawai_nik'] . "' AND is_proses = 'y'")->num_rows();
  $proses_avp_belum = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '4' AND id_pekerjaan='" . $value['pekerjaan_id'] . "' AND is_proses is  null")->num_rows();
  $koor_baru = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '4' AND id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND id_koor_baru is not null")->num_rows();

  if ($proses_avp_belum == '1') {
    $data_belum = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE is_proses is null AND pekerjaan_disposisi_status = '4' AND id_pekerjaan='" . $value['pekerjaan_id'] . "'")->row_array();
    $bagian = $this->db->query("SELECT * FROM global.global_bagian a LEFT JOIN global.global_bagian_detail b ON b.id_bagian  = a.bagian_id WHERE id_pegawai = '" . $data_belum['id_user'] . "'")->row_array();
  }

  $sql_allow = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND  id_user = '" . $session['pegawai_nik'] . "' and is_aktif = 'y' ");
  $isi_allow = $sql_allow->row_array();

  $sql_ajuan_extend = $this->db->query("SELECT * FROM dec.dec_pekerjaan_extend WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND extend_status='0'");
  $isi_ajuan_extend = $sql_ajuan_extend->row_array();

  $sql_extend = $this->db->query("SELECT * FROM dec.dec_pekerjaan_extend WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND extend_status='1'");
  $isi_extend = $sql_extend->row_array();

  $isi_vp = $this->db->get_where('dec.dec_pekerjaan_disposisi',['id_pekerjaan'=>$value['pekerjaan_id'],'pekerjaan_disposisi_status'=>'3','id_user'=>$session['pegawai_nik']])->num_rows();

  $isi_avp = $this->db->get_where('dec.dec_pekerjaan_disposisi',['id_pekerjaan'=>$value['pekerjaan_id'],'pekerjaan_disposisi_status'=>'4','id_user'=>$session['pegawai_nik']])->num_rows();
  /* Tambahan */

  $isi['extend_ajuan_tanggal'] = (!empty($isi_ajuan_extend)) ? $isi_ajuan_extend['extend_tanggal'] : '';
  $isi['extend_ajuan_status'] = (!empty($isi_ajuan_extend)) ? $isi_ajuan_extend['extend_status'] : '';

  $isi['extend_tanggal'] = (!empty($isi_extend)) ? $isi_extend['extend_tanggal'] : '';
  $isi['extend_status'] = (!empty($isi_extend)) ? $isi_extend['extend_status'] : '';

  $isi['is_allow'] = ($isi_allow['total'] > 0 || $session['pegawai_nik'] == $this->admin_sistemnya) ? 'y' : 'n';

  $isi['milik'] = ($isi_total['total'] > 0 || ($value['pic'] == $session['pegawai_nik'] && ($value['pekerjaan_status'] == '0' || $value['pekerjaan_status'] == '-'))) ? 'y' : 'n';

  $isi['is_proses'] = ($isi_total['total'] > 0 && $data_proses['is_proses']) ? $data_proses['is_proses'] : null;
  $isi['is_disposisi_aktif'] = ($isi_total['total'] > 0 && $data_proses['is_aktif'] == 'y') ? 'y' : 'n';

  $isi['proses_perencana_ifc'] = $proses_perencana_ifc;
  $isi['proses_avp'] = $proses_avp;
  $isi['proses_avp_belum'] = $proses_avp_belum;
  $isi['bagian_nama'] = ($proses_avp_belum == '1') ? $bagian['bagian_nama'] : '';
  $isi['koor_baru'] = $koor_baru;
  $isi['is_vp'] = ($isi_vp>0) ? 'y' : 'n';
  $isi['is_avp'] = ($isi_avp>0) ? 'y' : 'n';
  /* tambahan */

  array_push($data, $isi);
}
echo json_encode($data);
}
}
}
/* Get Pekerjaan Ususlan */


/* Get Pekerjaan Dokumen */
public function getPekerjaanDokumen()
{
	$param = array();

	if ($this->input->get('id_pekerjaan')) $param['id_pekerjaan'] = $this->input->get('id_pekerjaan');
	$param['pekerjaan_dokumen_awal'] = 'y';

	$data = $this->M_pekerjaan->getPekerjaanDokumen($param);

	echo json_encode($data);
}
/* Get Pekerjaan Dokumen */

/* Get Pekerjaan Dokumen */
public function getTemplatePekerjaan()
{
	$param = array();
	if ($this->input->post('q')) $param['nama'] = $this->input->post('q');

	$data = $this->M_pekerjaan->getTemplatePekerjaan($param);
	echo json_encode($data);
}
/* Get Pekerjaan Dokumen */

/* Get Pekerjaan Dokumen Jenis */
public function getPekerjaanDokumenJenis()
{
	$jenis[0]['pekerjaan_dokumen_jenis'] = 'Gambar';
	$jenis[1]['pekerjaan_dokumen_jenis'] = 'Dokumen';

	echo json_encode($jenis);
}
/* Get Pekerjaan Dokumen Jenis */

/* Get Pekerjaan Dokumen Kertas */
public function getPekerjaanDokumenKertas()
{
	$kertas[0]['pekerjaan_dokumen_kertas'] = 'A3';
	$kertas[1]['pekerjaan_dokumen_kertas'] = 'A4';

	echo json_encode($kertas);
}

/* Get Pekerjaan Dokumen Kertas */
public function getPekerjaanDokumenOrientasi()
{
	$orientasi[0]['pekerjaan_dokumen_orientasi'] = 'Potrait';
	$orientasi[1]['pekerjaan_dokumen_orientasi'] = 'Landscape';
	echo json_encode($orientasi);
}
/* Get Pekerjaan Dokumen Kertas */

/* Get Bidang */
public function getBidang()
{
	$param = array();
	if ($this->input->post('q')) $param['nama'] = $this->input->post('q');

	$data = $this->M_pekerjaan->getBidang($param);

	echo json_encode($data);
}
/* Get Bidang */

/* Get Urutan Proyek */
public function getUrutanProyek()
{
	$param = array();
	if ($this->input->post('q')) $param['nama'] = $this->input->post('q');

	$data = $this->M_pekerjaan->getUrutanProyek($param);
	echo json_encode($data);
}
/* Get Urutan Proyek */

/* Get Section Are */
public function getSectionArea()
{
	$param = array();
	if ($this->input->post('q')) $param['nama'] = $this->input->post('q');

	$data = $this->M_pekerjaan->getSectionArea($param);
	echo json_encode($data);
}
/* Get Section Are */
/* GET */


/*CC*/
public function getDokumenCC()
{
	$param['pegawai_nama'] = $this->input->get_post('q');
	echo json_encode($this->M_pekerjaan->getUserStaf($param));
}
/*  CC*/


/* PROSES */
/* Pekerjaan Dreft */
public function insertPekerjaan()
{
	if ($this->input->get('id_user')) {
    $isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'")->row_array();
  } else {
    $isi = $this->session->userdata();
  }

  $pekerjaan_status = '0';

  $data['pekerjaan_id'] = anti_inject($this->input->post('pekerjaan_id'));
  $data['pekerjaan_waktu'] = date('Y-m-d', strtotime($this->input->post('pekerjaan_waktu')));
  $data['pekerjaan_waktu_akhir'] = date('Y-m-d', strtotime($this->input->post('pekerjaan_waktu_akhir')));
  $data['pekerjaan_judul'] = anti_inject($this->input->post('pekerjaan_judul'));
  $data['id_klasifikasi_pekerjaan'] = anti_inject($this->input->post('id_klasifikasi_pekerjaan'));
  $data['pekerjaan_deskripsi'] = $this->input->post('pekerjaan_deskripsi');
  $data['pic'] = anti_inject($isi['pegawai_nik']);
  $data['pic_no_telp'] = anti_inject($this->input->post('pic_no_telp'));
  $data['pekerjaan_status'] = anti_inject($pekerjaan_status);
  $data['id_pekerjaan_disposisi'] = anti_inject($this->input->post('id_pekerjaan_disposisi'));
  $data['pekerjaan_tahun'] = $this->input->post('pekerjaan_tahun');
  $data['pekerjaan_reviewer'] = $this->input->post('reviewer');
  $data['pekerjaan_approver'] = $this->input->post('approver');
  $data['pekerjaan_referensi_unit_kerja'] = $this->input->get_post('ref_unit_kerja');

  $sql_pekerjaan = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan WHERE pekerjaan_id = '" . anti_inject($this->input->get_post('pekerjaan_id')) . "'");
  $data_pekerjaan = $sql_pekerjaan->row_array();
  if ($data_pekerjaan['total'] < 1) {
    $this->M_pekerjaan->insertPekerjaan($data);
  }

  dblog('I',  $data['pekerjaan_id'], 'Pekerjaan Tersimpan di Draft', $isi['pegawai_nik']);

  if ($this->input->get_post('cc_id')) {
    $user = $this->input->get_post('cc_id');
    $user_implode = implode("','", $user);
    $cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'y' ")->result_array();
     foreach ($cc_not_in as $value_not_in) {
       $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
       dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC Non HPS', $isi['pegawai_nik']);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'y'");
     }
     foreach ($user as $key => $value) {
       $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='y'")->row_array();
       if ($ada_cc['total'] == 0) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = anti_inject($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
        $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi_doc['is_cc'] = anti_inject('y');
        $data_disposisi_doc['is_aktif'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC Non HPS', $isi['pegawai_nik']);
        $tujuan = $data_cc['pegawai_nik'];
        $tujuan_nama = $data_cc['pegawai_nama'];
        $kalimat = "Pekerjaan telah di CC kepada anda";
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
        sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
      }
    }
  }
}
/* Pekerjaan Dreft */


/* Pekerjaan Send */
public function insertPekerjaanSend()
{

	if ($this->input->get('id_user')) {
    $isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'")->row_array();
  } else {
    $isi = $this->session->userdata();
  }

  if (anti_inject($this->input->post('jabatan_temp') == '2')) $pekerjaan_status = '3';
  elseif (anti_inject($this->input->post('jabatan_temp') == '3')) $pekerjaan_status = '2';
  else $pekerjaan_status = '1';

  $pekerjaan_status_temp = anti_inject($this->input->post('pekerjaan_status'));
  $pekerjaan_id = anti_inject($this->input->post('pekerjaan_id'));

  if ($pekerjaan_status_temp == '1') { /* Ketika ada dreft*/
    $data['pekerjaan_judul'] = anti_inject($this->input->post('pekerjaan_judul'));
    $data['pekerjaan_waktu'] = date('Y-m-d', strtotime($this->input->post('pekerjaan_waktu')));
    $data['pekerjaan_waktu_akhir'] = date('Y-m-d', strtotime($this->input->post('pekerjaan_waktu_akhir')));
    $data['id_klasifikasi_pekerjaan'] = anti_inject($this->input->post('id_klasifikasi_pekerjaan'));
    $data['pekerjaan_deskripsi'] = $this->input->post('pekerjaan_deskripsi');
    $data['pic'] = anti_inject($this->input->post('pic'));
    $data['pic_no_telp'] = anti_inject($this->input->post('pic_no_telp'));
    $data['id_pekerjaan_disposisi'] = anti_inject($this->input->post('id_pekerjaan_disposisi'));
    $data['tipe_pekerjaan'] = anti_inject($this->input->post('tipe_pekerjaan'));
    $data['pekerjaan_status'] = anti_inject($pekerjaan_status);
    $data['pekerjaan_tahun'] = $this->input->post('pekerjaan_tahun');
    $data['pekerjaan_reviewer'] = $this->input->post('reviewer');
    $data['pekerjaan_approver'] = $this->input->post('approver');
    $data['pekerjaan_referensi_unit_kerja'] = $this->input->get_post('ref_unit_kerja');

    $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
  } else { /* Ketika langsung send*/
    $data['pekerjaan_id'] = anti_inject($this->input->post('pekerjaan_id'));
    $data['pekerjaan_judul'] = anti_inject($this->input->post('pekerjaan_judul'));
    $data['pekerjaan_waktu'] = date('Y-m-d', strtotime($this->input->post('pekerjaan_waktu')));
    $data['pekerjaan_waktu_akhir'] = date('Y-m-d', strtotime($this->input->post('pekerjaan_waktu_akhir')));
    $data['id_klasifikasi_pekerjaan'] = anti_inject($this->input->post('id_klasifikasi_pekerjaan'));
    $data['pekerjaan_deskripsi'] = $this->input->post('pekerjaan_deskripsi');
    $data['pic'] = anti_inject($isi['pegawai_nik']);
    $data['pic_no_telp'] = anti_inject($this->input->post('pic_no_telp'));
    $data['pekerjaan_status'] = anti_inject($pekerjaan_status);
    $data['id_pekerjaan_disposisi'] = anti_inject($this->input->post('id_pekerjaan_disposisi'));
    $data['pekerjaan_tahun'] = $this->input->post('pekerjaan_tahun');
    $data['pekerjaan_reviewer'] = $this->input->post('reviewer');
    $data['pekerjaan_approver'] = $this->input->post('approver');
    $data['pekerjaan_referensi_unit_kerja'] = $this->input->get_post('ref_unit_kerja');

    /*cek apakah sudah ada pekerjaan*/
    $sql_pekerjaan = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan WHERE pekerjaan_id = '" . anti_inject($this->input->get_post('pekerjaan_id')) . "'");
    $data_pekerjaan = $sql_pekerjaan->row_array();

    if ($data_pekerjaan['total'] < 1) {
      $this->M_pekerjaan->insertPekerjaan($data);
    }
  }

  $data_user['pegawai_poscode'] = ($pekerjaan_status == '3') ? 'E53000000' : $isi['pegawai_direct_superior'];
  $user = $this->M_user->getUser($data_user);
  /* User */
  /* Disposisi */
  if ($this->input->post('reviewer') != '' || $this->input->post('reviewer') != null) {
   $param['pegawai_nik'] = $this->input->post('reviewer');
   $userReviewer = $this->M_pekerjaan->getUserListRevApp2($param);

   $data_disposisi['pekerjaan_disposisi_id'] = create_id();
   $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
   $data_disposisi['id_user'] = anti_inject($userReviewer['pegawai_nik']);
   $data_disposisi['id_pekerjaan'] = anti_inject($pekerjaan_id);
   $data_disposisi['pekerjaan_disposisi_status'] = anti_inject($pekerjaan_status);

   $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
 }

 if ($this->input->post('approver') != '' || $this->input->post('approver') != null) {
   $param['pegawai_nik'] = $this->input->post('approver');
   $userApprover = $this->M_pekerjaan->getUserListRevApp2($param);

   $data_disposisi2['pekerjaan_disposisi_id'] = create_id();
   $data_disposisi2['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
   $data_disposisi2['id_user'] = anti_inject($userApprover['pegawai_nik']);
   $data_disposisi2['id_pekerjaan'] = anti_inject($pekerjaan_id);
   $data_disposisi2['pekerjaan_disposisi_status'] = '2';

   $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi2);
 }
 /* Disposisi */

 /*    jika langsung dari VP*/
 if (anti_inject($this->input->post('jabatan_temp') == '2')) {
   $data_user['pegawai_poscode'] = ($pekerjaan_status == '3') ? 'E53000000' : $isi['pegawai_direct_superior'];
   $user = $this->M_user->getUser($data_user);
   /* User */

   /* Disposisi */
   $data_disposisi['pekerjaan_disposisi_id'] = create_id();
   $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
   $data_disposisi['id_user'] = anti_inject($user['pegawai_nik']);
   $data_disposisi['id_pekerjaan'] = anti_inject($pekerjaan_id);
   $data_disposisi['pekerjaan_disposisi_status'] = anti_inject($pekerjaan_status);

   $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
   /*Disposisi*/
 }

 /*Insert CC*/
 /*CC NON HPS*/
 if ($this->input->get_post('cc_id')) {
   $user = $this->input->get_post('cc_id');
   $user_implode = implode("','", $user);
   $cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'y' ")->result_array();
    foreach ($cc_not_in as $value_not_in) {
      $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
      dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC Non HPS', $isi['pegawai_nik']);
      $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'y'");
    }
    foreach ($user as $key => $value) {
      $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='y'")->row_array();
      if ($ada_cc['total'] == 0) {
       $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
       $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
       $data_disposisi_doc['id_user'] = anti_inject($value);
       $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
       $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
       $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
       $data_disposisi_doc['is_cc'] = anti_inject('y');
       $data_disposisi_doc['is_aktif'] = 'y';
       $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
       $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
       $tujuan = $data_cc['pegawai_nik'];
       $tujuan_nama = $data_cc['pegawai_nama'];
       $kalimat = "Pekerjaan telah di CC kepada anda";
       dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC Non HPS', $isi['pegawai_nik']);
       tasklog($pekerjaan_id, $data_disposisi_doc['pekerjaan_disposisi_status'], $value, $kalimat, 'n');
       sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
       sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
     }
   }
 }
 /*CC NON HPS*/

 /* INSERT PIC ke CC HPS */
 $jml_cc = $this->db->get_where('dec.dec_pekerjaan_disposisi',['id_pekerjaan'=>$pekerjaan_id,'pekerjaan_disposisi_status'=>'8','is_cc'=>'h','id_user'=>$isi['pegawai_nik']])->num_rows();

 if($jml_cc==0){
   $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
   $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
   $data_disposisi_doc['id_user'] = anti_inject($isi['pegawai_nik']);
   $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
   $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
   $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
   $data_disposisi_doc['is_cc'] = anti_inject('h');
   $data_disposisi_doc['is_aktif'] = 'y';

   $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
   $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $this->input->post('pic')])->row_array();
   $tujuan = $data_cc['pegawai_nik'];
   $tujuan_nama = $data_cc['pegawai_nama'];
   $kalimat = "Pekerjaan telah di CC kepada anda";
   dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC HPS', $isi['pegawai_nik']);
   tasklog($pekerjaan_id, $data_disposisi_doc['pekerjaan_disposisi_status'], $data_disposisi_doc['id_user'], $kalimat, 'n');
   sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
   sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
 }
 /* INSERT PIC ke CC HPS */

 /* Buat Notifikasi */
 $dari = $isi['pegawai_nik'];
 $tujuan = $user['pegawai_nik'];
 $tujuan_nama = $user['pegawai_nama'];
 $text = "Mohon untuk melakukan REVIEW pada pekerjaan ini";
 dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Send ke AVP Customer', $isi['pegawai_nik']);
 tasklog($pekerjaan_id, $data_disposisi['pekerjaan_disposisi_status'], $user['pegawai_nik'], $text, 'n');
 sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
 sendNotif($pekerjaan_id, $dari, $tujuan, $text);
 /* Buat Notifikasi */
}
/* Pekerjaan Send */


/* Pekerjaan Edit */
public function updatePekerjaan()
{
	if (isset($_GET['id_user'])) {
    $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $isi = $sql_isi->row_array();
  } else {
    $isi = $this->session->userdata();
  }

  $pekerjaan_id = $this->input->post('pekerjaan_id');
  if ($pekerjaan_id) {
    $data['pekerjaan_judul'] = anti_inject($this->input->post('pekerjaan_judul'));
    $data['pekerjaan_waktu'] = date('Y-m-d', strtotime($this->input->post('pekerjaan_waktu')));
    $data['pekerjaan_waktu_akhir'] = date('Y-m-d', strtotime($this->input->post('pekerjaan_waktu_akhir')));
    $data['id_klasifikasi_pekerjaan'] = anti_inject($this->input->post('id_klasifikasi_pekerjaan'));
    $data['pekerjaan_deskripsi'] = ($this->input->post('pekerjaan_deskripsi'));
    $data['pic'] = anti_inject($this->input->post('pic'));
    $data['pic_no_telp'] = anti_inject($this->input->post('pic_no_telp'));
    $data['id_pekerjaan_disposisi'] = anti_inject($this->input->post('id_pekerjaan_disposisi'));
    $data['tipe_pekerjaan'] = anti_inject($this->input->post('tipe_pekerjaan'));
    $data['pekerjaan_status'] = anti_inject('0');
    $data['pekerjaan_tahun'] = $this->input->post('pekerjaan_tahun');
    $data['pekerjaan_reviewer'] = $this->input->post('reviewer');
    $data['pekerjaan_approver'] = $this->input->post('approver');


    $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

    dblog('E', $pekerjaan_id, 'Pekerjaan Telah di Edit', $isi['pegawai_nik']);
  }

  /*CC NON HPS*/
  if ($this->input->get_post('cc_id')) {
    $user = $this->input->get_post('cc_id');
    $user_implode = implode("','", $user);
    $cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'y' ")->result_array();
     foreach ($cc_not_in as $value_not_in) {
       $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
       dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC Non HPS', $isi['pegawai_nik']);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'y'");
     }
     foreach ($user as $key => $value) {
       $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='y'")->row_array();
       if ($ada_cc['total'] == 0) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = anti_inject($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
        $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi_doc['is_cc'] = anti_inject('y');
        $data_disposisi_doc['is_aktif'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC Non HPS', $isi['pegawai_nik']);
        $tujuan = $data_cc['pegawai_nik'];
        $tujuan_nama = $data_cc['pegawai_nama'];
        $kalimat = "Pekerjaan telah di CC kepada anda";
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
        sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
      }
    }
  }
  /*CC NON HPS*/
}
/* Pekerjaan Edit */


/* Insert File Pekerjaan Dokumen */
public function insertFilePekerjaanDokumen()
{
	if (isset($_FILES['file'])) {
    $directory = './document/';
    if (!file_exists($directory)) mkdir($directory);

    $tmpFile = $_FILES['file']['tmp_name'];
    $fileName = $_FILES['file']['name'];
    $fIleType = $_FILES['file']['type'];

    if (!empty($tmpFile)) {
      $Extension    = array("jpeg", "jpg", "png", "bmp", "gif", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf");

      $random = rand(11111111, 99999999);

      $fileExt       = substr($fileName, strrpos($fileName, '.'));
      $fileExt       = str_replace('.', '', $fileExt); /* Extension*/
      $fileName      = preg_replace("/\.[^.\s]{3,4}$/", "", $fileName);
      $newFileName = str_replace(' ', '', $_POST['id_pekerjaan'] . '_' . date('ymdhis') . '_' . $random . '.' . $fileExt);

      if (in_array(strtolower($fileExt), $Extension)) {
       move_uploaded_file($tmpFile, $directory . $newFileName);
       echo $newFileName;
     }
   }
 }
}
/*Insert File Pekerjaan Dokumen*/


/* Insert Pekerjaan Dokumen */
public function insertPekerjaanDokumenUsulan()
{
	$user = (isset($_GET['id_user'])) ? $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $_GET['id_user']))->row_array() : $this->session->userdata();

	$data['pekerjaan_dokumen_id'] = anti_inject(create_id());
	$data['id_pekerjaan'] = anti_inject($this->input->get_post('pekerjaan_id'));
	$data['pekerjaan_dokumen_nama'] = anti_inject($this->input->get_post('pekerjaan_dokumen_nama'));
	$data['pekerjaan_dokumen_file'] = anti_inject($this->input->get_post('savedFileName'));
	$data['pekerjaan_dokumen_status'] = anti_inject('1');
	$data['who_create'] = anti_inject($user['pegawai_nama']);
	$data['id_create'] = anti_inject($user['pegawai_nik']);
	$data['is_lama'] = anti_inject('n');
	$data['pekerjaan_dokumen_awal'] = anti_inject('y');

	$this->M_pekerjaan->insertPekerjaanDokumen($data);

	dblog('I', $data['id_pekerjaan'], 'Dokumen ' . $this->input->get_post('pekerjaan_dokumen_nama') . ' Telah Diupload', $user['pegawai_nik']);
}
/* Insert Pekerjaan Dokumen */


/* Update Pekerjaan Dokumen */
public function updatePekerjaanDokumen()
{
	$user = (isset($_GET['id_user'])) ? $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $_GET['id_user']))->row_array() : $this->session->userdata();

	$id = $this->input->post('pekerjaan_dokumen_id');
	$data = array(
    'id_pekerjaan' => anti_inject($this->input->post('id_pekerjaan')),
    'pekerjaan_dokumen_nama' => anti_inject($this->input->post('pekerjaan_dokumen_nama')),
    'pekerjaan_dokumen_file' => anti_inject($this->input->post('savedFileName')),
    'pekerjaan_dokumen_status' => anti_inject('1'),
  );

	$this->M_pekerjaan->updatePekerjaanDokumen($data, $id);

	dblog('U', $data['id_pekerjaan'], 'Dokumen ' . $this->input->get_post('pekerjaan_dokumen_nama') . ' Telah Diedit', $user['pegawai_nik']);
}
/* Update Pekerjaan Dokumen */


/* Proses Send VP */
public function prosesSendVP()
{
	if (isset($_GET['id_user'])) {
    $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $isi = $sql_isi->row_array();
  } else {
    $isi = $this->session->userdata();
  }
  $pekerjaan_id = anti_inject($this->input->get_post('id_pekerjaan_send_vp'));
  $id_tanggung_jawab = null;
  $pekerjaan_status_send_vp = anti_inject('8');
  /*CC NON HPS*/
  $is_cc = 'y';
  if ($this->input->get_post('id_user_send_vp')) {
    $user = $this->input->get_post('id_user_send_vp');
    $user_implode = implode("','", $user);
    $cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'y' ")->result_array();
     foreach ($cc_not_in as $value_not_in) {
       $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
       dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC Non HPS', $isi['pegawai_nik']);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'y'");
     }
     foreach ($user as $key => $value) {
       $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='y'")->row_array();
       if ($ada_cc['total'] == 0) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = anti_inject($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
        $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi_doc['is_cc'] = anti_inject('y');
        $data_disposisi_doc['is_aktif'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
        /*notifikasi*/
        $tujuan = $data_cc['pegawai_nik'];
        $tujuan_nama = $data_cc['pegawai_nama'];
        $kalimat = "Pekerjaan telah di CC kepada anda";
        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC Non HPS', $isi['pegawai_nik']);
        tasklog($pekerjaan_id, $data_disposisi_doc['pekerjaan_disposisi_status'], $data_cc['pegawai_nik'], $kalimat, 'n');
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
        sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);

        /*notifikasi*/
      }
    }
  }
  /*    CC NON HPS*/
  /* CC HPS*/
  $is_cc_hps = 'h';
  if ($this->input->get_post('id_user_send_vp_hps')) {
    $user = $this->input->get_post('id_user_send_vp_hps');
    $user_implode = implode("','", $user);
    $cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'h' ")->result_array();
     foreach ($cc_not_in as $value_not_in) {
       $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
       dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC HPS', $isi['pegawai_nik']);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'h'");
     }
     foreach ($user as $key => $value) {
       $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='h'")->row_array();
       if ($ada_cc['total'] == 0) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = anti_inject($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
        $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi_doc['is_cc'] = anti_inject('h');
        $data_disposisi_doc['is_aktif'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
        /*notifikasi*/
        $tujuan = $data_cc['pegawai_nik'];
        $tujuan_nama = $data_cc['pegawai_nama'];
        $kalimat = "Pekerjaan telah di CC kepada anda";
        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC HPS', $isi['pegawai_nik']);
        tasklog($pekerjaan_id, $data_disposisi_doc['pekerjaan_disposisi_status'], $data_cc['pegawai_nik'], $kalimat, 'n');
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
        sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);

        /*notifikasi*/
      }
    }
  }
  /* CC HPS*/

  $where_id_user = ($isi['pegawai_nik']);
  $where_id_pekerjaan = (($pekerjaan_id));
  $where_disposisi_status = '6';
  $param_staf['is_proses'] = 'y';
  $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);

  $pekerjaan_status = '7';

  /* cek apakah koordinator atau bukan*/
  $sql_koordinator = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . anti_inject($this->input->get_post('id_pekerjaan_send_vp')) . "' AND id_penanggung_jawab = '" . anti_inject('y') . "' AND pekerjaan_disposisi_status = '" . anti_inject('4') . "' AND id_user = '" . $isi['pegawai_nik'] . "'");
  $data_koordinator = $sql_koordinator->row_array();

  $sql_proses = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('id_pekerjaan_send_vp') . "' AND pekerjaan_disposisi_status = '6' AND is_proses is null");
  $jumlah_proses = $sql_proses->num_rows();

  $sql_proses_koor = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('id_pekerjaan_send_vp') . "' AND pekerjaan_disposisi_status = '6' AND is_proses ='y' AND id_penanggung_jawab = 'y'");
  $jumlah_proses_koor = $sql_proses_koor->num_rows();

  $pekerjaan_id = $this->input->get_post('id_pekerjaan_send_vp');
  if ($pekerjaan_id) {
    if ($data_koordinator['total'] > '0' || ($jumlah_proses == '0' && $jumlah_proses_koor > '0')) {
      $data['pekerjaan_status'] = anti_inject($pekerjaan_status);
      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
      /* User */
      $data_user['pegawai_poscode'] = $isi['pegawai_direct_superior'];
      $user = $this->M_user->getUser($data_user);
      /* User */

      /* Get Pekerjaan */
      $sql_pekerjaan = $this->db->query("SELECT pic FROM dec.dec_pekerjaan WHERE pekerjaan_id = '" . $pekerjaan_id . "'");
      $isi_pekerjaan = $sql_pekerjaan->row_array();
      /* Get Pekerjaan */

      /* Disposisi */
      $data_disposisi['pekerjaan_disposisi_id'] = create_id();
      $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
      $data_disposisi['id_user'] = ($pekerjaan_status == '8') ? $isi_pekerjaan['pic'] : $user['pegawai_nik'];
      $data_disposisi['id_pekerjaan'] = anti_inject($pekerjaan_id);
      $data_disposisi['pekerjaan_disposisi_status'] = anti_inject($pekerjaan_status);
      $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
      /* Buat Notifikasi */
      $dari = $isi['pegawai_nik'];
      $tujuan = $user['pegawai_nik'];
      $tujuan_nama = $user['pegawai_nama'];
      $text = "Mohon untuk melakukan APPROVE pada pekerjaan ini";
      dblog('I',  $pekerjaan_id, 'Pekerjaan IFA Telah di Review AVP Koordinator', $isi['pegawai_nik']);
      tasklog($pekerjaan_id, $data_disposisi['pekerjaan_disposisi_status'], $user['pegawai_nik'], $text, 'n');
      sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
      sendNotif($pekerjaan_id, $dari, $tujuan, $text);
      /* Buat Notifikasi */
    } else {
      /* Buat Notifikasi */
      $user_koor = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi dpd LEFT JOIN global.global_pegawai gp ON gp.pegawai_nik = dpd.id_user WHERE id_pekerjaan = '" . anti_inject($this->input->get_post('id_pekerjaan_send_vp')) . "' AND id_penanggung_jawab = '" . anti_inject('y') . "' AND pekerjaan_disposisi_status = '" . anti_inject('4') . "' ")->row_array();
      $dari = $isi['pegawai_nik'];
      $tujuan = $user_koor['pegawai_nik'];
      $tujuan_nama = $user_koor['pegawai_nama'];
      $text = "Pekerjaan Telah di Review IFA AVP Terkait";
      dblog('I',  $pekerjaan_id, 'Pekerjaan IFA Telah di Review AVP Terkait', $isi['pegawai_nik']);
      /* Buat Notifikasi */
    }
  }
  /* Pekerjaan */

  /* Dokumen */
  $sql_bagian = $this->db->query("SELECT id_bagian FROM global.global_bagian_detail WHERE id_pegawai = '" . $isi['pegawai_nik'] . "'");
  $data_bagian = $sql_bagian->row_array();

  $data_pegawai = $this->db->query("SELECT id_user,id_pekerjaan,pekerjaan_disposisi_status FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $isi['pegawai_nik'] . "' AND pekerjaan_disposisi_status = '6'")->row_array();

  /*    ubah status dokumen ke IFC*/
  $data_dokumen_send = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_create_awal IN (SELECT id_pegawai FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $data_bagian['id_bagian'] . "' AND id_pekerjaan = '" . $pekerjaan_id . "' ) AND pekerjaan_dokumen_status <= '2' AND (is_lama !='y' OR is_lama is null) AND pekerjaan_dokumen_awal != 'y' AND is_hps = 'n' AND (is_update_ifa !='y' OR is_update_ifa is NULL)")->result_array();

  foreach ($data_dokumen_send as $val_dokumen) {
   $dokumen_ada = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_create_awal = '" . $val_dokumen['id_create_awal'] . "' AND pekerjaan_dokumen_status = '3' AND pekerjaan_dokumen_nama = '" . $val_dokumen['pekerjaan_dokumen_nama'] . "' AND id_pekerjaan_template = '" . $val_dokumen['id_pekerjaan_template'] . "'  AND is_hps = 'n' AND pekerjaan_dokumen_file = '" . $val_dokumen['pekerjaan_dokumen_file'] . "'")->row_array();
   $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
   $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];
   if (!empty($dokumen_ada) && ($dokumen_ada['pekerjaan_dokumen_nama'] == $val_dokumen['pekerjaan_dokumen_nama'] && $dokumen_ada['id_pekerjaan_template'] == $val_dokumen['id_pekerjaan_template'] && $dokumen_ada['id_create_awal'] == $val_dokumen['id_create_awal'])) {
     /*        skip*/
   } else {
     $data['pekerjaan_dokumen_id_temp'] = anti_inject($val_dokumen['pekerjaan_dokumen_id']);
     $data['pekerjaan_dokumen_id'] = anti_inject(create_id());
     $data['pekerjaan_dokumen_status'] = '3';
     $data['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
     $data['pekerjaan_dokumen_keterangan'] = $val_dokumen['pekerjaan_dokumen_keterangan'];
     $data['id_create'] = $isi['pegawai_nik'];
     $data['is_proses'] = 'y';
     $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
     $this->M_pekerjaan->simpanAksiSama($data);

     $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();

     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");

     dblog('I', $this->input->get_post('pekerjaan_id'), 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
   }
 }
 $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET is_lama = 'y' WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_create_awal IN (SELECT id_pegawai FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $data_bagian['id_bagian'] . "' AND id_pekerjaan = '" . $pekerjaan_id . "' ) AND (pekerjaan_dokumen_status = '0' OR pekerjaan_dokumen_status = '2') AND (is_lama !='y' OR is_lama is null) AND pekerjaan_dokumen_awal != 'y' AND is_hps = 'n'");

 $data_dokumen_send_hps =   $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_create_awal IN (SELECT id_pegawai FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $data_bagian['id_bagian'] . "' AND id_pekerjaan = '" . $pekerjaan_id . "' ) AND pekerjaan_dokumen_status <= '2' AND (is_lama !='y' OR is_lama is null) AND pekerjaan_dokumen_awal != 'y' AND is_hps = 'y' AND (is_update_ifa !='y' OR is_update_ifa is NULL)")->result_array();

 foreach ($data_dokumen_send_hps as $val_dokumen) {
   $dokumen_ada = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_create_awal = '" . $val_dokumen['id_create_awal'] . "' AND pekerjaan_dokumen_status ='3' AND pekerjaan_dokumen_nama = '" . $val_dokumen['pekerjaan_dokumen_nama'] . "' AND id_pekerjaan_template = '" . $val_dokumen['id_pekerjaan_template'] . "'  AND is_hps = 'y' AND pekerjaan_dokumen_file = '" . $val_dokumen['pekerjaan_dokumen_file'] . "'")->row_array();
   $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
   $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];
   if (!empty($dokumen_ada) && ($dokumen_ada['pekerjaan_dokumen_nama'] == $val_dokumen['pekerjaan_dokumen_nama'] && $dokumen_ada['id_pekerjaan_template'] == $val_dokumen['id_pekerjaan_template'] && $dokumen_ada['id_create_awal'] == $val_dokumen['id_create_awal'])) {
     /*skip*/
   } else {
     $data['pekerjaan_dokumen_id_temp'] = anti_inject($val_dokumen['pekerjaan_dokumen_id']);
     $data['pekerjaan_dokumen_id'] = anti_inject(create_id());
     $data['pekerjaan_dokumen_status'] = '3';
     $data['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
     $data['pekerjaan_dokumen_keterangan'] = $val_dokumen['pekerjaan_dokumen_keterangan'];
     $data['id_create'] = $isi['pegawai_nik'];
     $data['is_proses'] = 'y';
     $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');

     $this->M_pekerjaan->simpanAksiSama($data);
     $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();

     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");

     dblog('I', $this->input->get_post('pekerjaan_id'), 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
   }
 }
 $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET is_lama = 'y' WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_create_awal IN (SELECT id_pegawai FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $data_bagian['id_bagian'] . "' AND id_pekerjaan = '" . $pekerjaan_id . "' ) AND (pekerjaan_dokumen_status = '0' OR pekerjaan_dokumen_status = '2') AND (is_lama !='y' OR is_lama is null) AND pekerjaan_dokumen_awal != 'y' AND is_hps = 'y'");
 /* Dokumen */
}
/* Proses Send VP */


/* Proses Send VP  Koor*/
public function prosesSendVPKoor()
{
	if (isset($_GET['id_user'])) {
    $isi = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $_GET['id_user']))->row_array();
  } else {
    $isi = $this->session->userdata();
  }

  $pekerjaan_id = anti_inject($this->input->get_post('id_pekerjaan_send_vp'));
  $id_tanggung_jawab = null;
  $pekerjaan_status_send_vp = anti_inject('8');
  /*CC NON HPS*/
  if ($this->input->get_post('id_user_send_vp')) {
    $user = $this->input->get_post('id_user_send_vp');
    $user_implode = implode("','", $user);
    $cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'y' ")->result_array();
     foreach ($cc_not_in as $value_not_in) {
       $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
       dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC Non HPS', $isi['pegawai_nik']);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'y'");
     }
     foreach ($user as $key => $value) {
       $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='y'")->row_array();
       if ($ada_cc['total'] == 0) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = anti_inject($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
        $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi_doc['is_cc'] = anti_inject('y');
        $data_disposisi_doc['is_aktif'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
        $tujuan = $data_cc['pegawai_nik'];
        $tujuan_nama = $data_cc['pegawai_nama'];
        $kalimat = "Pekerjaan telah di CC kepada anda";
        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC Non HPS', $isi['pegawai_nik']);
        tasklog($pekerjaan_id, $data_disposisi_doc['pekerjaan_disposisi_status'], $data_cc['pegawai_nik'], $kalimat, 'n');
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
        sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
      }
    }
  }
  /*CC NON HPS*/

  /* CC HPS*/
  if ($this->input->get_post('id_user_send_vp_hps')) {
    $user_implode = implode("','", $user);
    $cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'h' ")->result_array();
     foreach ($cc_not_in as $value_not_in) {
       $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
       dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC HPS', $isi['pegawai_nik']);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'h'");
     }
     foreach ($user as $key => $value) {
       $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='h'")->row_array();
       if ($ada_cc['total'] == 0) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = anti_inject($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
        $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi_doc['is_cc'] = anti_inject('h');
        $data_disposisi_doc['is_aktif'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
        $tujuan = $data_cc['pegawai_nik'];
        $tujuan_nama = $data_cc['pegawai_nama'];
        $kalimat = "Pekerjaan telah di CC kepada anda";
        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC HPS', $isi['pegawai_nik']);
        tasklog($pekerjaan_id, $data_disposisi_doc['pekerjaan_disposisi_status'], $data_cc['pegawai_nik'], $kalimat, 'n');
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
        sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
      }
    }
  }
  /*     CC HPS*/

  $where_id_user = ($isi['pegawai_nik']);
  $where_id_pekerjaan = (($pekerjaan_id));
  $where_disposisi_status = '6';
  $param_staf['is_proses'] = 'y';
  $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);

  $data_user['pegawai_poscode'] = $isi['pegawai_direct_superior'];
  $user = $this->M_user->getUser($data_user);
  /* Buat Notifikasi */
  $dari = $isi['pegawai_nik'];
  $tujuan = $user['pegawai_nik'];
  $tujuan_nama = $user['pegawai_nama'];
  $text = "Mohon untuk melakukan REVIEW pada pekerjaan ini";
  dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Send ke AVP Customer', $isi['pegawai_nik']);
  sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
  sendNotif($pekerjaan_id, $dari, $tujuan, $text);
  /* Buat Notifikasi */

  $pekerjaan_status = '7';

  /*    cek apakah koordinator atau bukan*/
  $sql_koordinator = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . anti_inject($this->input->get_post('id_pekerjaan_send_vp')) . "' AND id_penanggung_jawab = '" . anti_inject('y') . "' AND pekerjaan_disposisi_status = '" . anti_inject('6') . "' AND id_user = '" . $isi['pegawai_nik'] . "'");

  $data_koordinator = $sql_koordinator->row_array();
  /* cek apakah koordinator atau bukan*/
}
/* Proses Send VP  Koor*/

public function prosesSendAVPIFC()
{
	if ($this->input->get('id_user')) {
    $session = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $this->input->get('id_user')))->row_array();
  } else {
    $session = $this->session->userdata();
  }

  $pekerjaan_id = $this->input->get_post('id_pekerjaan');
  /*    CC NON HPS*/
  if ($this->input->get_post('id_user_cc')) {
    $user = $this->input->get_post('id_user_cc');
    $user_implode = implode("','", $user);
    $cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'y' ")->result_array();
     foreach ($cc_not_in as $value_not_in) {
       $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
       dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC Non HPS', $session['pegawai_nik']);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'y'");
     }
     foreach ($user as $key => $value) {
       $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='y'")->row_array();
       if ($ada_cc['total'] == 0) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = anti_inject($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
        $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi_doc['is_cc'] = anti_inject('y');
        $data_disposisi_doc['is_aktif'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
        $tujuan = $data_cc['pegawai_nik'];
        $tujuan_nama = $data_cc['pegawai_nama'];
        $kalimat = "Pekerjaan telah di CC kepada anda";
        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC Non HPS', $session['pegawai_nik']);
        tasklog($pekerjaan_id, $data_disposisi_doc['pekerjaan_disposisi_status'], $data_cc['pegawai_nik'], $kalimat, 'n');
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
        sendNotif($pekerjaan_id, $session['pegawai_nik'], $tujuan, $kalimat);
      }
    }
  }
  /*CC NON HPS*/

  /*    CC HPS*/
  if ($this->input->get_post('id_user_cc_hps')) {
    $user = $this->input->get_post('id_user_cc_hps');
    $user_implode = implode("','", $user);
    $cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'h' ")->result_array();
     foreach ($cc_not_in as $value_not_in) {
       $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
       dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC HPS', $session['pegawai_nik']);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'h'");
     }
     foreach ($user as $key => $value) {
       $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='h'")->row_array();
       if ($ada_cc['total'] == 0) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = anti_inject($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
        $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi_doc['is_cc'] = anti_inject('h');
        $data_disposisi_doc['is_aktif'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
        $tujuan = $data_cc['pegawai_nik'];
        $tujuan_nama = $data_cc['pegawai_nama'];
        $kalimat = "Pekerjaan telah di CC kepada anda";
        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC HPS', $session['pegawai_nik']);
        tasklog($pekerjaan_id, $data_disposisi_doc['pekerjaan_disposisi_status'], $data_cc['pegawai_nik'], $kalimat, 'n');
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
        sendNotif($pekerjaan_id, $session['pegawai_nik'], $tujuan, $kalimat);
      }
    }
  }

  /*UPDATE STATUS KE 'Y'*/
  $param_status['id_pekerjaan'] = anti_inject($this->input->get_post('id_pekerjaan'));
  $param_status['disposisi_status']  = anti_inject($this->input->get_post('pekerjaan_status'));
  $param_status['id_user'] = anti_inject($session['pegawai_nik']);
  $data_status['is_proses'] = anti_inject('y');
  $this->M_pekerjaan->updateStatus($param_status, $data_status);

  $pekerjaan_status = '13';

  /*cek apakah koordinator atau bukan*/
  $sql_proses = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' AND pekerjaan_disposisi_status = '12' AND is_proses is null");
  $jumlah_proses = $sql_proses->num_rows();

  $sql_proses_koor = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('id_pekerjaan_send_vp') . "' AND pekerjaan_disposisi_status = '6' AND is_proses ='y' AND id_penanggung_jawab = 'y'");
  $jumlah_proses_koor = $sql_proses_koor->num_rows();

  /*cek apakah koordinator atau bukan*/

  $pekerjaan_id = $this->input->get_post('id_pekerjaan');
  if ($pekerjaan_id) {
    if ($jumlah_proses == '0' && $jumlah_proses_koor > '0') {
      $data['pekerjaan_status'] = anti_inject($pekerjaan_status);
      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);


      /* Get Pekerjaan */
      $sql_pekerjaan = $this->db->query("SELECT pic FROM dec.dec_pekerjaan WHERE pekerjaan_id = '" . $pekerjaan_id . "'");
      $isi_pekerjaan = $sql_pekerjaan->row_array();
      /* Get Pekerjaan */

      /* Disposisi */
      $data_disposisi['pekerjaan_disposisi_id'] = create_id();
      $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
      $data_disposisi['id_user'] = ($pekerjaan_status == '8') ? $isi_pekerjaan['pic'] : $user['pegawai_nik'];
      $data_disposisi['id_pekerjaan'] = anti_inject($pekerjaan_id);
      $data_disposisi['pekerjaan_disposisi_status'] = anti_inject($pekerjaan_status);
      $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
      /*Disposisi*/

      /* User */
      $data_user['pegawai_poscode'] = $session['pegawai_direct_superior'];
      $user = $this->M_user->getUser($data_user);
      /* User */
      /*notif*/
      $tujuan = $user['pegawai_nik'];
      $tujuan_nama = $user['pegawai_nama'];
      $kalimat = "Mohon untuk melakukan APPROVE IFC pada pekerjaan ini";
      dblog('I',  $pekerjaan_id, 'Pekerjaan IFC Telah di Review AVP Koordinator', $session['pegawai_nik']);
      tasklog($pekerjaan_id, $data_disposisi['pekerjaan_disposisi_status'], $user['pegawai_nik'], $kalimat, 'n');
      sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
      sendNotif($pekerjaan_id, $session['pegawai_nik'], $tujuan, $kalimat);
      /*notif*/
    }
  }
  /* Pekerjaan */

  /*    UPDATE DOKUMEN*/
  $sql_bagian = $this->db->query("SELECT id_bagian FROM global.global_bagian_detail WHERE id_pegawai = '" . $session['pegawai_nik'] . "'");
  $data_bagian = $sql_bagian->row_array();

  $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' AND is_lama = 'n' and pekerjaan_dokumen_status >= '8' AND pekerjaan_dokumen_status <= '9' AND is_hps='n' AND id_create_awal IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian = '" . $data_bagian['id_bagian'] . "')  ")->result_array();

  foreach ($data_dokumen as $val_dokumen) {
   $sql_status = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'");
   $data_status = $sql_status->row_array();
   $status_dokumen = '10';
   $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
   if ($data_status['is_proses'] == null || $data_status['is_proses'] == '') {
     $proses = 'y';
   } else if ($data_status['is_proses'] == 'y') {
     $proses = 'a';
   } else if ($data_status['is_proses'] == 'a') {
     $proses = 'i';
   }
   if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
     $status_dokumen_revisi = $data_status['pekerjaan_dokumen_revisi'] + 1;
   } else {
     $status_dokumen_revisi = null;
   }
   $data['pekerjaan_dokumen_id_temp'] = anti_inject($val_dokumen['pekerjaan_dokumen_id']);
   $data['pekerjaan_dokumen_id'] = create_id();
   $data['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
   $data['pekerjaan_dokumen_keterangan'] = anti_inject($this->input->post('pekerjaan_dokumen_keterangan'));
   $data['id_create'] = $session['pegawai_nik'];
   $data['is_proses'] = 'y';
   $data['id_create_awal'] = $data_status['id_create_awal'];
   $data['pekerjaan_dokumen_waktu'] = date('Y-m-d H:i:s');
   $data['pekerjaan_dokumen_revisi'] = $statut_dokumen_revisi;
   $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
   $this->M_pekerjaan->simpanAksiIFASama($data);
   if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data['pekerjaan_dokumen_keterangan'] . '</span>', $session['pegawai_nik']);
   } else {
     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $session['pegawai_nik']);
   }
   $param_lama['is_lama'] = 'y';
   $param_id = $val_dokumen['pekerjaan_dokumen_id'];
   $this->M_pekerjaan->editAksi($param_lama, $param_id);
 }

 $data_dokumen_hps = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' AND is_lama = 'n' and pekerjaan_dokumen_status >= '8' AND pekerjaan_dokumen_status <= '9' AND is_hps='y' AND id_create_awal IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian = '" . $data_bagian['id_bagian'] . "')  ")->result_array();

 foreach ($data_dokumen_hps as $val_dokumen) {
   $sql_status = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'");
   $data_status = $sql_status->row_array();
   $status_dokumen = '10';
   $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
   if ($data_status['is_proses'] == null || $data_status['is_proses'] == '') {
     $proses = 'y';
   } else if ($data_status['is_proses'] == 'y') {
     $proses = 'a';
   } else if ($data_status['is_proses'] == 'a') {
     $proses = 'i';
   }
   if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
     $status_dokumen_revisi = $data_status['pekerjaan_dokumen_revisi'] + 1;
   } else {
     $status_dokumen_revisi = null;
   }
   $data['pekerjaan_dokumen_id_temp'] = anti_inject($val_dokumen['pekerjaan_dokumen_id']);
   $data['pekerjaan_dokumen_id'] = create_id();
   $data['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
   $data['pekerjaan_dokumen_keterangan'] = anti_inject($this->input->post('pekerjaan_dokumen_keterangan'));
   $data['id_create'] = $session['pegawai_nik'];
   $data['is_proses'] = 'y';
   $data['id_create_awal'] = $data_status['id_create_awal'];
   $data['pekerjaan_dokumen_waktu'] = date('Y-m-d H:i:s');
   $data['pekerjaan_dokumen_revisi'] = $statut_dokumen_revisi;
   $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
   $this->M_pekerjaan->simpanAksiIFASama($data);
   if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data['pekerjaan_dokumen_keterangan'] . '</span>', $session['pegawai_nik']);
   } else {
     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $session['pegawai_nik']);
   }
   $param_lama['is_lama'] = 'y';
   $param_id = $val_dokumen['pekerjaan_dokumen_id'];
   $this->M_pekerjaan->editAksi($param_lama, $param_id);
 }
 dblog('I',  $pekerjaan_id, 'Pekerjaan IFC Telah di Review AVP Terkait', $session['pegawai_nik']);
}


/* Proses Send VP */
public function prosesSendVPIFC()
{
	if (isset($_GET['id_user'])) {
    $isi = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $_GET['id_user']))->row_array();
  } else {
    $isi = $this->session->userdata();
  }

  $pekerjaan_id = anti_inject($this->input->get_post('id_pekerjaan_send_vp'));
  $id_tanggung_jawab = null;
  $pekerjaan_status_send_vp = anti_inject('8');
  $is_cc = 'y';

  if ($this->input->get_post('id_user_cc')) {
    $user = $this->input->get_post('id_user_cc');
    $user_implode = implode("','", $user);
    $cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'y' ")->result_array();
     foreach ($cc_not_in as $value_not_in) {
       $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
       dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC Non HPS', $isi['pegawai_nik']);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'y'");
     }
     foreach ($user as $key => $value) {
       $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='y'")->row_array();
       if ($ada_cc['total'] == 0) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = anti_inject($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
        $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi_doc['is_cc'] = anti_inject('y');
        $data_disposisi_doc['is_aktif'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
        $tujuan = $data_cc['pegawai_nik'];
        $tujuan_nama = $data_cc['pegawai_nama'];
        $kalimat = "Pekerjaan telah di CC kepada anda";
        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC Non HPS', $isi['pegawai_nik']);
        tasklog($pekerjaan_id, $data_disposisi_doc['pekerjaan_disposisi_status'], $data_cc['pegawai_nik'], $kalimat, 'n');
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
        sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
      }
    }
  }

  $is_cc_hps = 'h';

  if ($this->input->get_post('id_user_cc_hps')) {
    $user = $this->input->get_post('id_user_cc_hps');
    $user_implode = implode("','", $user);
    $cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'h' ")->result_array();
     foreach ($cc_not_in as $value_not_in) {
       $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
       dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC HPS', $isi['pegawai_nik']);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'h'");
     }
     foreach ($user as $key => $value) {
       $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='h'")->row_array();
       if ($ada_cc['total'] == 0) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = anti_inject($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
        $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi_doc['is_cc'] = anti_inject('h');
        $data_disposisi_doc['is_aktif'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
        $tujuan = $data_cc['pegawai_nik'];
        $tujuan_nama = $data_cc['pegawai_nama'];
        $kalimat = "Pekerjaan telah di CC kepada anda";
        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC HPS', $isi['pegawai_nik']);
        tasklog($pekerjaan_id, $data_disposisi_doc['pekerjaan_disposisi_status'], $data_cc['pegawai_nik'], $kalimat, 'n');
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
        sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
      }
    }
  }

  $where_id_user = ($isi['pegawai_nik']);
  $where_id_pekerjaan = (($pekerjaan_id));
  $where_disposisi_status = '12';
  $param_staf['is_proses'] = 'y';
  $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);

  $pekerjaan_status = '13';

  /*cek apakah koordinator atau bukan*/
  $sql_koordinator = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . anti_inject($this->input->get_post('id_pekerjaan_send_vp')) . "' AND id_penanggung_jawab = '" . anti_inject('y') . "' AND pekerjaan_disposisi_status = '" . anti_inject('12') . "' AND id_user = '" . $isi['pegawai_nik'] . "'");

  $data_koordinator = $sql_koordinator->row_array();

  /*cek apakah koordinator atau bukan*/

  $pekerjaan_id = $this->input->get_post('id_pekerjaan_send_vp');
  if ($pekerjaan_id) {
    if ($data_koordinator['total'] > '0') {
      $data['pekerjaan_status'] = anti_inject($pekerjaan_status);
      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

      /* User */
      $data_user['pegawai_poscode'] = $isi['pegawai_direct_superior'];
      $user = $this->M_user->getUser($data_user);


      /* Get Pekerjaan */
      $sql_pekerjaan = $this->db->query("SELECT pic FROM dec.dec_pekerjaan WHERE pekerjaan_id = '" . $pekerjaan_id . "'");
      $isi_pekerjaan = $sql_pekerjaan->row_array();
      /* Get Pekerjaan */

      /* Disposisi */
      $data_disposisi['pekerjaan_disposisi_id'] = create_id();
      $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
      $data_disposisi['id_user'] = ($pekerjaan_status == '8') ? $isi_pekerjaan['pic'] : $user['pegawai_nik'];
      $data_disposisi['id_pekerjaan'] = anti_inject($pekerjaan_id);
      $data_disposisi['pekerjaan_disposisi_status'] = anti_inject($pekerjaan_status);
      $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

      /* User */
      $tujuan = $user['pegawai_nik'];
      $tujuan_nama = $user['pegawai_nama'];
      $kalimat = "Mohon untuk melakukan APPROVE IFC pada pekerjaan ini";

      dblog('I',  $pekerjaan_id, 'Pekerjaan IFC Telah di Review AVP Koordinator', $isi['pegawai_nik']);
      tasklog($pekerjaan_id, $data_disposisi['pekerjaan_disposisi_status'], $user['pegawai_nik'], $kalimat, 'n');
      sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
      sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
    }
  }
  /* Pekerjaan */


  /*UPDATE DOKUMEN BIASA*/
  $sql_bagian = $this->db->query("SELECT id_bagian FROM global.global_bagian_detail WHERE id_pegawai = '" . $isi['pegawai_nik'] . "'");
  $data_bagian = $sql_bagian->row_array();

  $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND is_lama = 'n' AND pekerjaan_dokumen_status IN('8','9','0') AND is_hps='n' AND id_create_awal IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian = '" . $data_bagian['id_bagian'] . "')  ")->result_array();
  foreach ($data_dokumen as $val_dokumen) {
   $sql_status = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'");
   $data_status = $sql_status->row_array();
   $status_dokumen = '10';
   $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
   if ($data_status['is_proses'] == null || $data_status['is_proses'] == '') {
     $proses = 'y';
   } else if ($data_status['is_proses'] == 'y') {
     $proses = 'a';
   } else if ($data_status['is_proses'] == 'a') {
     $proses = 'i';
   }

   $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
   $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];

   $data['pekerjaan_dokumen_id_temp'] = anti_inject($val_dokumen['pekerjaan_dokumen_id']);
   $data['pekerjaan_dokumen_id'] = create_id();
   $data['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
   $data['pekerjaan_dokumen_revisi'] = anti_inject($nomor_revisi_baru);
   $data['pekerjaan_dokumen_keterangan'] = anti_inject($val_dokumen['pekerjaan_dokumen_keterangan']);
   $data['id_create'] = $isi['pegawai_nik'];
   $data['is_proses'] = 'y';
   $data['id_create_awal'] = $data_status['id_create_awal'];
   $data['pekerjaan_dokumen_waktu'] = date('Y-m-d H:i:s');
   $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
   $this->M_pekerjaan->simpanAksiIFASama($data);
   echo $this->db->last_query();

   if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
   } else {
     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '6'");
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
   }

   $param_lama['is_lama'] = 'y';
   $param_id = $val_dokumen['pekerjaan_dokumen_id'];
   $this->M_pekerjaan->editAksi($param_lama, $param_id);
 }

 $data_dokumen_hps = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND is_lama = 'n' AND pekerjaan_dokumen_status IN('8','9','0') AND is_hps='y' AND id_create_awal IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian = '" . $data_bagian['id_bagian'] . "')  ")->result_array();
 foreach ($data_dokumen_hps as $val_dokumen) {
   $sql_status = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'");
   $data_status = $sql_status->row_array();
   $status_dokumen = '10';
   $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
   if ($data_status['is_proses'] == null || $data_status['is_proses'] == '') {
     $proses = 'y';
   } else if ($data_status['is_proses'] == 'y') {
     $proses = 'a';
   } else if ($data_status['is_proses'] == 'a') {
     $proses = 'i';
   }

   $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
   $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];

   $data['pekerjaan_dokumen_id_temp'] = anti_inject($val_dokumen['pekerjaan_dokumen_id']);
   $data['pekerjaan_dokumen_id'] = create_id();
   $data['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
   $data['pekerjaan_dokumen_revisi'] = anti_inject($nomor_revisi_baru);
   $data['pekerjaan_dokumen_keterangan'] = anti_inject($val_dokumen['pekerjaan_dokumen_keterangan']);
   $data['id_create'] = $isi['pegawai_nik'];
   $data['is_proses'] = 'y';
   $data['id_create_awal'] = $data_status['id_create_awal'];
   $data['pekerjaan_dokumen_waktu'] = date('Y-m-d H:i:s');
   $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
   $this->M_pekerjaan->simpanAksiIFASama($data);
   echo $this->db->last_query();

   if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
   } else {
     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '6'");
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
   }

   $param_lama['is_lama'] = 'y';
   $param_id = $val_dokumen['pekerjaan_dokumen_id'];
   $this->M_pekerjaan->editAksi($param_lama, $param_id);
 }
}


/* Proses Send VP */

public function prosesApproveVP()
{
	/*cek apakah user yang memproses otomatis atau manual*/
	if ($this->input->get('id_user')) {
    $isi = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $this->input->get('id_user')))->row_array();
  } else {
    $isi = $this->session->userdata();
  }

  $pekerjaan_id = anti_inject($this->input->get_post('id_pekerjaan_approve_vp'));
  $id_tanggung_jawab = null;
  $pekerjaan_status_approve_vp = anti_inject('8');
  /*CC BIASA*/
  $is_cc = 'y';
  /*    $pekerjaan_status = '9';*/
  if ($this->input->get_post('id_user_cc')) {
    $user = $this->input->get_post('id_user_cc');
    $user_implode = implode("','", $user);
    $cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'y' ")->result_array();
     /*insert history hapus cc*/
     foreach ($cc_not_in as $value_not_in) {
       $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
       dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC Non HPS', $isi['pegawai_nik']);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'y'");
     }
     foreach ($user as $key => $value) {
       /* cek apakah cc sudah pernah diinsertkan*/
       $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='y'")->row_array();
       if ($ada_cc['total'] == 0) {
        /* ika belum diinsert*/
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = anti_inject($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
        $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi_doc['is_cc'] = anti_inject('y');
        $data_disposisi_doc['is_aktif'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
        /*notifikasi*/
        $tujuan = $data_cc['pegawai_nik'];
        $tujuan_nama = $data_cc['pegawai_nama'];
        $kalimat = "Pekerjaan telah di CC kepada anda";
        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC Non HPS', $isi['pegawai_nik']);
        tasklog($pekerjaan_id, $data_disposisi_doc['pekerjaan_disposisi_status'], $data_cc['pegawai_nik'], $kalimat, 'n');
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
        sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);

        /*notifikasi*/
      }
    }
  }
  /*CC BIASA*/
  /* CC HPS*/
  $is_cc_hps = 'h';
  if ($this->input->get_post('id_user_cc_hps')) {
    $user = $this->input->get_post('id_user_cc_hps');
    $user_implode = implode("','", $user);
    $cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'h' ")->result_array();
     /*insert history hapus cc*/
     foreach ($cc_not_in as $value_not_in) {
       $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
       dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC HPS', $isi['pegawai_nik']);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'h'");
     }
     foreach ($user as $key => $value) {
       /*        cek apakah cc sudah pernah diinsertkan*/
       $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='h'")->row_array();
       if ($ada_cc['total'] == 0) {
        /*          jika belum diinsert*/
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = anti_inject($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
        $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi_doc['is_cc'] = anti_inject('h');
        $data_disposisi_doc['is_aktif'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
        $tujuan = $data_cc['pegawai_nik'];
        $tujuan_nama = $data_cc['pegawai_nama'];
        $kalimat = "Pekerjaan telah di CC kepada anda";
        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC HPS', $isi['pegawai_nik']);
        tasklog($pekerjaan_id, $data_disposisi_doc['pekerjaan_disposisi_status'], $data_cc['pegawai_nik'], $kalimat, 'n');
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
        sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
      }
    }
  }
  /* CC HPS*/
  /* Pekerjaan */
  $pekerjaan_status = $this->input->get_post('pekerjaan_status') + 1;
  /*$pekerjaan_status = '9';*/
  $pekerjaan_id = anti_inject($this->input->get_post('id_pekerjaan_approve_vp'));
  $param['pekerjaan_id'] = anti_inject($this->input->get_post('id_pekerjaan_approve_vp'));
  $data_pekerjaan = $this->M_pekerjaan->getPekerjaan($param);
  /*    print_r($data_pekerjaan);*/
  if ($pekerjaan_id) {
    if ($data_pekerjaan['id_klasifikasi_pekerjaan'] == '616b79fa38c26380f49f3b84f088b8f86f9cd176') {
      $data['pekerjaan_status'] = anti_inject('15');
      $data['pekerjaan_waktu_selesai'] = date('Y-m-d H:i:s');
      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
      $this->db->query("UPDATE dec.dec_pekerjaan_progress SET progress_jumlah = '100' WHERE id_pekerjaan = '" . $pekerjaan_id . "'");
    } else if ($this->input->get_post('pekerjaan_status') == '13') {
      $data['pekerjaan_status'] = anti_inject($pekerjaan_status);
      $data['pekerjaan_waktu_selesai'] = date('Y-m-d H:i:s');
      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
    } else {
      $data['pekerjaan_status'] = anti_inject($pekerjaan_status);
      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
    }
  }
  /* Pekerjaan */

  /* User */
  $data_user['pegawai_poscode'] = $isi['pegawai_direct_superior'];
  $user = $this->M_user->getUser($data_user);
  /* User */

  /* Get Pekerjaan */
  $sql_pekerjaan = $this->db->query("SELECT pic FROM dec.dec_pekerjaan WHERE pekerjaan_id = '" . $pekerjaan_id . "'");
  $isi_pekerjaan = $sql_pekerjaan->row_array();
  /* Get Pekerjaan */

  /* Disposisi */
  if ($this->input->get_post('pekerjaan_status') != '13') {
   $data_disposisi['pekerjaan_disposisi_id'] = create_id();
   $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
   $data_disposisi['id_user'] = ($pekerjaan_status == '8') ? $isi_pekerjaan['pic'] : $user['pegawai_nik'];
   $data_disposisi['id_pekerjaan'] = anti_inject($pekerjaan_id);
   $data_disposisi['pekerjaan_disposisi_status'] = anti_inject($pekerjaan_status);
   $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
 } else if ($this->input->get_post('pekerjaan_status') == '13') {
   $data_disposisi['pekerjaan_disposisi_id'] = create_id();
   $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
   $data_disposisi['id_pekerjaan'] = anti_inject($pekerjaan_id);
   $data_disposisi['pekerjaan_disposisi_status'] = anti_inject($pekerjaan_status);
   $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
 }
 /* Disposisi */
 if ($pekerjaan_status == '8') {
   $data_users['pegawai_nik'] = $isi_pekerjaan['pic'];
   $users = $this->M_user->getUser($data_users);
   $tujuan = $users['pegawai_nik'];
   $tujuan_nama = $users['pegawai_nama'];
   $kalimat = "Mohon untuk melakukan APPROVE pada pekerjaan ini";
   dblog('I',  $pekerjaan_id, 'Pekerjaan IFA Telah di Approve VP', $isi['pegawai_nik']);
   tasklog($pekerjaan_id, $data_disposisi['pekerjaan_disposisi_status'], $users['pegawai_nik'], $kalimat, 'n');
   sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
   sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
 } else {
   $data_users['pegawai_nik'] = $isi_pekerjaan['pic'];
   $users = $this->M_user->getUser($data_users);
   $tujuan = $users['pegawai_nik'];
   $tujuan_nama = $users['pegawai_nama'];
   $kalimat = "Pekerjaan Telah Diselesaikan";
   dblog('I',  $pekerjaan_id, 'Pekerjaan IFC Telah DiApprove VP', $isi['pegawai_nik']);
   sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
   sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
 }

 /*update status proses ke y*/
 $where_id_user = ($isi['pegawai_nik']);
 $where_id_pekerjaan = (($pekerjaan_id));
 $where_disposisi_status = $this->input->get_post('pekerjaan_status');
 $param_staf['is_proses'] = 'y';
 $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);
 /*    update status proses ke y*/

 /* Dokumen */
 $sql_bagian = $this->db->query("SELECT id_bagian FROM global.global_bagian_detail WHERE id_pegawai = '" . $isi['pegawai_nik'] . "'");
 $data_bagian = $sql_bagian->row_array();

 $data_pegawai = $this->db->query("SELECT id_user,id_pekerjaan,pekerjaan_disposisi_status FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $isi['pegawai_nik'] . "' AND pekerjaan_disposisi_status = '6'")->row_array();

 /*ubah status dokumen BIASA*/
 $data_dokumen_send = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND  pekerjaan_dokumen_status <= '3' AND (is_lama !='y' OR is_lama is null) AND pekerjaan_dokumen_awal != 'y' AND is_hps = 'n' AND (is_update_ifa !='y' OR is_update_ifa is NULL)")->result_array();

 if ($this->input->get_post('pekerjaan_status') != '13') {
   foreach ($data_dokumen_send as $val_dokumen) {
     $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
     $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];
     $dokumen_ada = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_create_awal = '" . $val_dokumen['id_create_awal'] . "' AND pekerjaan_dokumen_status ='4' AND pekerjaan_dokumen_nama = '" . $val_dokumen['pekerjaan_dokumen_nama'] . "' AND id_pekerjaan_template = '" . $val_dokumen['id_pekerjaan_template'] . "'  AND is_hps = 'n' AND pekerjaan_dokumen_file = '" . $val_dokumen['pekerjaan_dokumen_file'] . "'")->row_array();
     if (!empty($dokumen_ada) && ($dokumen_ada['pekerjaan_dokumen_nama'] == $val_dokumen['pekerjaan_dokumen_nama'] && $dokumen_ada['id_pekerjaan_template'] == $val_dokumen['id_pekerjaan_template'] && $dokumen_ada['id_create_awal'] == $val_dokumen['id_create_awal'])) {
      /*skip*/
    } else {
      $data['pekerjaan_dokumen_id_temp'] = anti_inject($val_dokumen['pekerjaan_dokumen_id']);
      $data['pekerjaan_dokumen_id'] = anti_inject(create_id());
      $data['pekerjaan_dokumen_status'] = '4';
      $data['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
      $data['pekerjaan_dokumen_keterangan'] = $val_dokumen['pekerjaan_dokumen_keterangan'];
      $data['id_create'] = $isi['pegawai_nik'];
      $data['is_proses'] = 'a';
      $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
      $this->M_pekerjaan->simpanAksiSama($data);
      $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();

      $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");

      dblog('I', $this->input->get_post('pekerjaan_id'), 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
    }
  }
  $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET is_lama = 'y' WHERE id_pekerjaan = '" . $pekerjaan_id . "'  AND (pekerjaan_dokumen_status = '0' OR pekerjaan_dokumen_status = '3') AND (is_lama !='y' OR is_lama is null) AND pekerjaan_dokumen_awal != 'y' AND is_hps = 'n'");
  /*ubah status dokumen BIASA*/

  /*ubah status dokumen HPS*/
  $data_dokumen_send_hps =   $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_dokumen_status <= '3' AND (is_lama !='y' OR is_lama is null) AND pekerjaan_dokumen_awal != 'y' AND is_hps = 'y' AND (is_update_ifa !='y' OR is_update_ifa is NULL)")->result_array();

  foreach ($data_dokumen_send_hps as $val_dokumen) {
   $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
   $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];
   $dokumen_ada = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_create_awal = '" . $val_dokumen['id_create_awal'] . "' AND pekerjaan_dokumen_status ='4' AND pekerjaan_dokumen_nama = '" . $val_dokumen['pekerjaan_dokumen_nama'] . "' AND id_pekerjaan_template = '" . $val_dokumen['id_pekerjaan_template'] . "'  AND is_hps = 'y' AND pekerjaan_dokumen_file = '" . $val_dokumen['pekerjaan_dokumen_file'] . "'")->row_array();

   if (!empty($dokumen_ada) && ($dokumen_ada['pekerjaan_dokumen_nama'] == $val_dokumen['pekerjaan_dokumen_nama'] && $dokumen_ada['id_pekerjaan_template'] == $val_dokumen['id_pekerjaan_template'] && $dokumen_ada['id_create_awal'] == $val_dokumen['id_create_awal'])) {
    /*skip*/
  } else {
    $data['pekerjaan_dokumen_id_temp'] = anti_inject($val_dokumen['pekerjaan_dokumen_id']);
    $data['pekerjaan_dokumen_id'] = anti_inject(create_id());
    $data['pekerjaan_dokumen_status'] = '4';
    $data['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
    $data['pekerjaan_dokumen_keterangan'] = $val_dokumen['pekerjaan_dokumen_keterangan'];
    $data['id_create'] = $isi['pegawai_nik'];
    $data['is_proses'] = 'a';
    $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
    $this->M_pekerjaan->simpanAksiSama($data);
    $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();

    $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");
    dblog('I', $this->input->get_post('pekerjaan_id'), 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
  }
}
$this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET is_lama = 'y' WHERE id_pekerjaan = '" . $pekerjaan_id . "'  AND (pekerjaan_dokumen_status = '0' OR pekerjaan_dokumen_status = '3') AND (is_lama !='y' OR is_lama is null) AND pekerjaan_dokumen_awal != 'y' AND is_hps = 'y'");
/*ubah status dokumen HPS*/
}

if ($this->input->get_post('pekerjaan_status') == '13') {
 /*ubah status dokumen IFC*/
 $data_dokumen_ifc = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND  pekerjaan_dokumen_status = '10' AND (is_lama !='y' OR is_lama is null) AND pekerjaan_dokumen_awal != 'y' AND is_hps = 'n' AND (is_update_ifa !='y' OR is_update_ifa is NULL)")->result_array();

 if (!empty($data_dokumen_ifc)) {
   foreach ($data_dokumen_ifc as $val_dokumen) {
    $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
    $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];

    $dokumen_ada = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_create_awal = '" . $val_dokumen['id_create_awal'] . "' AND pekerjaan_dokumen_status ='10' AND pekerjaan_dokumen_nama = '" . $val_dokumen['pekerjaan_dokumen_nama'] . "' AND id_pekerjaan_template = '" . $val_dokumen['id_pekerjaan_template'] . "'  AND is_hps = 'n' AND pekerjaan_dokumen_file = '" . $val_dokumen['pekerjaan_dokumen_file'] . "'")->row_array();
    if (!empty($dokumen_ada) && ($dokumen_ada['pekerjaan_dokumen_nama'] == $val_dokumen['pekerjaan_dokumen_nama'] && $dokumen_ada['id_pekerjaan_template'] == $val_dokumen['id_pekerjaan_template'] && $dokumen_ada['id_create_awal'] == $val_dokumen['id_create_awal'])) {
      /*skip*/
    } else {
      $data['pekerjaan_dokumen_id_temp'] = anti_inject($val_dokumen['pekerjaan_dokumen_id']);
      $data['pekerjaan_dokumen_id'] = anti_inject(create_id());
      $data['pekerjaan_dokumen_status'] = '11';
      $data['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
      $data['pekerjaan_dokumen_keterangan'] = $val_dokumen['pekerjaan_dokumen_keterangan'];
      $data['id_create'] = $isi['pegawai_nik'];
      $data['is_proses'] = 'a';
      $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
      $this->M_pekerjaan->simpanAksiSama($data);
      $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();

      $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '6'");

      dblog('I', $this->input->get_post('pekerjaan_id'), 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
    }
  }
  $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET is_lama = 'y' WHERE id_pekerjaan = '" . $pekerjaan_id . "'  AND (pekerjaan_dokumen_status = '0' OR pekerjaan_dokumen_status = '10') AND (is_lama !='y' OR is_lama is null) AND pekerjaan_dokumen_awal != 'y' AND is_hps = 'n'");
}

$data_dokumen_ifc_hps = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND  pekerjaan_dokumen_status = '10' AND (is_lama !='y' OR is_lama is null) AND pekerjaan_dokumen_awal != 'y' AND is_hps = 'y' AND (is_update_ifa !='y' OR is_update_ifa is NULL)")->result_array();

if (!empty($data_dokumen_ifc_hps)) {
	foreach ($data_dokumen_ifc_hps as $val_dokumen) {
    $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
    $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];

    $dokumen_ada = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_create_awal = '" . $val_dokumen['id_create_awal'] . "' AND pekerjaan_dokumen_status ='10' AND pekerjaan_dokumen_nama = '" . $val_dokumen['pekerjaan_dokumen_nama'] . "' AND id_pekerjaan_template = '" . $val_dokumen['id_pekerjaan_template'] . "'  AND is_hps = 'y' AND pekerjaan_dokumen_file = '" . $val_dokumen['pekerjaan_dokumen_file'] . "'")->row_array();
    if (!empty($dokumen_ada) && ($dokumen_ada['pekerjaan_dokumen_nama'] == $val_dokumen['pekerjaan_dokumen_nama'] && $dokumen_ada['id_pekerjaan_template'] == $val_dokumen['id_pekerjaan_template'] && $dokumen_ada['id_create_awal'] == $val_dokumen['id_create_awal'])) {
      /*skip*/
    } else {
      $data['pekerjaan_dokumen_id_temp'] = anti_inject($val_dokumen['pekerjaan_dokumen_id']);
      $data['pekerjaan_dokumen_id'] = anti_inject(create_id());
      $data['pekerjaan_dokumen_status'] = '11';
      $data['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
      $data['pekerjaan_dokumen_keterangan'] = $val_dokumen['pekerjaan_dokumen_keterangan'];
      $data['id_create'] = $isi['pegawai_nik'];
      $data['is_proses'] = 'a';
      $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
      $this->M_pekerjaan->simpanAksiSama($data);
      $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();

      $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '6'");

      dblog('I', $this->input->get_post('pekerjaan_id'), 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
    }
  }
  $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET is_lama = 'y' WHERE id_pekerjaan = '" . $pekerjaan_id . "'  AND (pekerjaan_dokumen_status = '0' OR pekerjaan_dokumen_status = '10') AND (is_lama !='y' OR is_lama is null) AND pekerjaan_dokumen_awal != 'y' AND is_hps = 'y'");
}
}
/*ubah status dokumen IFC*/
}

/* PROSES */

/* DELETE */
/* Delete Pekerjaan */
public function deletePekerjaan()
{
	if (isset($_GET['id_user'])) {
    $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $isi = $sql_isi->row_array();
  } else {
    $isi = $this->session->userdata();
  }
  $this->M_pekerjaan->deletePekerjaan($this->input->get('pekerjaan_id'));
  dblog(
    'I',
    $this->input->get_post('pekerjaan_id'),
    'Pekerjaan Telah Dihapus',
    $isi['pegawai_nik'],
  );
}
/* Delete Pekerjaan */

/* Delete Pekerjaan Dokumen */
public function deletePekerjaanDokumen()
{
	if (isset($_GET['id_user'])) {
    $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $isi = $sql_isi->row_array();
  } else {
    $isi = $this->session->userdata();
  }
  $this->M_pekerjaan->deletePekerjaanDokumen($this->input->get_post('pekerjaan_dokumen_id'));
  dblog('D', $this->input->get_post('pekerjaan_id'), 'Dokumen ' . $this->input->get_post('pekerjaan_dokumen_nama') . ' Telah Dihapus', $isi['pegawai_nik']);
}
/* Delete Pekerjaan Dokumen */
/* DELETE */
/* PEKERJAAN USULAN */

/* PEKERJAAN BERJALAN */
/* Get Pekerjaan Berjalan */
public function getPekerjaanBerjalan()
{
	$session = $this->session->userdata();
	$data = array();
	$param['id_user'] = $this->input->get_post('id_user_cari');
	$param['klasifikasi_pekerjaan_id'] = $this->input->get_post('klasifikasi_pekerjaan_id');
	$param['klasifikasi_pekerjaan_id_non_rkap'] = $this->input->get_post('klasifikasi_pekerjaan_id_non_rkap');
	$split = explode(',', $this->input->get_post('pekerjaan_status'));
	$param['pekerjaan_status'] = $split;
	$param['tahun'] = $this->input->get_post('tahun');

	$sql_pic = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan WHERE pic='" . $session['pegawai_nik'] . "'");
	$data_pic = $sql_pic->row_array();

	if ($session['pegawai_nik'] != $this->admin_sistemnya) {
    if ($this->id_bagiannya != 0) {
      $param['id_bagian'] = $this->id_bagiannya;
      $param['pic_bagian'] = $session['pegawai_nik'];
    } else if ($data_pic['total'] > 0) {
      $param['user_pic'] = $session['pegawai_nik'];
    } else {
      $param['user_disposisi'] = $session['pegawai_nik'];
    }
  }

  if (empty($param['id_user'])) {
    $sql_pekerjaan = $this->M_pekerjaan->getPekerjaan($param);
    foreach ($sql_pekerjaan as $key => $value) {
      $sql_total = $this->db->query("SELECT  count(distinct id_user) as total  FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '5' AND id_pekerjaan = '" . $value['pekerjaan_id'] . "' ");
      $isi_total = $sql_total->row_array();

      $sql = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '" . $value['pekerjaan_status'] . "' AND id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND  id_user = '" . $session['pegawai_nik'] . "' ");
      $dataMilik = $sql->row_array();

      /*data per progress*/
      /*sipil*/
      $sql_sipil = $this->db->query("SELECT bagian_id,id_bagian,progress_jumlah FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='1483c0882e75988626fee21c5926cc63727734a0'");
      $data_sipil = $sql_sipil->row_array();
      $jml_sipil = $sql_sipil->num_rows();

      $sql_user_sipil = $this->db->query("SELECT klasifikasi_dokumen_inisial FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '5' AND c.id_bagian = '1483c0882e75988626fee21c5926cc63727734a0'");
      $data_user_sipil = $sql_user_sipil->row_array();

      $sql_user_sipil_koor = $this->db->query("SELECT count(*) AS total FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '4' AND id_penanggung_jawab = 'y' AND c.id_bagian = '1483c0882e75988626fee21c5926cc63727734a0'");
      $data_user_sipil_koor = $sql_user_sipil_koor->row_array();
      /*sipil*/

      /*proses*/
      $sql_proses = $this->db->query("SELECT bagian_id,id_bagian,progress_jumlah FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='c21f86a03fdf9f7420764ac49d664415cfc942eb'");
      $data_proses = $sql_proses->row_array();
      $jml_proses = $sql_proses->num_rows();

      $sql_user_proses = $this->db->query("SELECT klasifikasi_dokumen_inisial FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '5' AND c.id_bagian = 'c21f86a03fdf9f7420764ac49d664415cfc942eb'");
      $data_user_proses = $sql_user_proses->row_array();

      $sql_user_proses_koor = $this->db->query("SELECT count(*) AS total FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '4' AND id_penanggung_jawab = 'y' AND c.id_bagian = 'c21f86a03fdf9f7420764ac49d664415cfc942eb'");
      $data_user_proses_koor = $sql_user_proses_koor->row_array();
      /*proses*/

      /*mesin*/
      $sql_mesin = $this->db->query("SELECT bagian_id,id_bagian,progress_jumlah FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='fd2aa961b30ede7622a57d42267edc5d5eae3e1b'");
      $data_mesin = $sql_mesin->row_array();
      $jml_mesin = $sql_mesin->num_rows();

      $sql_user_mesin = $this->db->query("SELECT klasifikasi_dokumen_inisial FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '5' AND c.id_bagian = 'fd2aa961b30ede7622a57d42267edc5d5eae3e1b'");
      $data_user_mesin = $sql_user_mesin->row_array();

      $sql_user_mesin_koor = $this->db->query("SELECT count(*) AS total FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '4' AND id_penanggung_jawab = 'y' AND c.id_bagian = 'fd2aa961b30ede7622a57d42267edc5d5eae3e1b'");
      $data_user_mesin_koor = $sql_user_mesin_koor->row_array();
      /*mesin*/

      /*listrik*/
      $sql_listrik = $this->db->query("SELECT bagian_id,id_bagian,progress_jumlah,is_listin FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian LEFT JOIN dec.dec_pekerjaan_disposisi c ON c.id_user = a.id_user AND a.id_pekerjaan = c.id_pekerjaan  WHERE c.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND is_listin = 'L' and pekerjaan_disposisi_status = '5'");
      $data_listrik = $sql_listrik->row_array();
      $jml_listrik = $sql_listrik->num_rows();

      $sql_user_listrik = $this->db->query("SELECT klasifikasi_dokumen_inisial FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '5' AND c.id_bagian = 'f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND b.is_listin = 'L'");
      $data_user_listrik = $sql_user_listrik->row_array();

      $sql_user_listrik_koor = $this->db->query("SELECT count(*) AS total FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND (b.pekerjaan_disposisi_status = '4' OR pekerjaan_disposisi_status = '5') AND id_penanggung_jawab = 'y' AND c.id_bagian = 'f683cbbca693d1a08fc010fd861b7350efa3e8d2'");
      $data_user_listrik_koor = $sql_user_listrik_koor->row_array();
      /*listrik*/

      /*instrumen*/
      $sql_instrumen = $this->db->query("SELECT bagian_id,id_bagian,progress_jumlah,is_listin FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian LEFT JOIN dec.dec_pekerjaan_disposisi c ON c.id_user = a.id_user AND a.id_pekerjaan = c.id_pekerjaan WHERE c.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND is_listin='I' and pekerjaan_disposisi_status = '5' ");
      $data_instrumen = $sql_instrumen->row_array();
      $jml_instrumen = $sql_instrumen->num_rows();

      $sql_user_instrumen = $this->db->query("SELECT klasifikasi_dokumen_inisial FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '5' AND c.id_bagian = 'f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND b.is_listin = 'I'");
      $data_user_instrumen = $sql_user_instrumen->row_array();

      $sql_user_instrumen_koor = $this->db->query("SELECT count(*) AS total FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '4' AND id_penanggung_jawab = 'y' AND c.id_bagian = 'f683cbbca693d1a08fc010fd861b7350efa3e8d2'");
      $data_user_instrumen_koor = $sql_user_instrumen_koor->row_array();
      /*instrumen*/
      /*data per progress*/


      if (!empty($data_sipil['id_bagian']) && $data_sipil['id_bagian'] == '1483c0882e75988626fee21c5926cc63727734a0') {
        $isi['pekerjaan_sipil'] =  $data_sipil['progress_jumlah'];
        $isi['pekerjaan_isi_sipil'] =  (isset($data_user_sipil['klasifikasi_dokumen_inisial'])) ? $data_user_sipil['klasifikasi_dokumen_inisial'] . '(' . $data_sipil['progress_jumlah'] . '%)' : '';
      } else {
        $isi['pekerjaan_sipil'] = 0;
        $isi['pekerjaan_isi_sipil'] = (isset($data_user_sipil['klasifikasi_dokumen_inisial'])) ? $data_user_sipil['klasifikasi_dokumen_inisial'] . '(0%)' : '';
      }
      $isi['pekerjaan_isi_sipil'] = ($data_user_sipil_koor['total'] == 0) ? $isi['pekerjaan_isi_sipil'] : '<b>' . $isi['pekerjaan_isi_sipil'] . '</b>';

      if (!empty($data_proses['bagian_id']) && $data_proses['bagian_id'] == 'c21f86a03fdf9f7420764ac49d664415cfc942eb') {
        $isi['pekerjaan_proses'] =  $data_proses['progress_jumlah'];
        $isi['pekerjaan_isi_proses'] =  (isset($data_user_proses['klasifikasi_dokumen_inisial'])) ? $data_user_proses['klasifikasi_dokumen_inisial'] . '(' . $data_proses['progress_jumlah'] . '%)' : '';
      } else {
        $isi['pekerjaan_proses'] = 0;
        $isi['pekerjaan_isi_proses'] = (isset($data_user_proses['klasifikasi_dokumen_inisial'])) ? $data_user_proses['klasifikasi_dokumen_inisial'] . '(0%)' : '';
      }
      $isi['pekerjaan_isi_proses'] = ($data_user_proses_koor['total'] == 0) ? $isi['pekerjaan_isi_proses'] : '<b>' . $isi['pekerjaan_isi_proses'] . '</b>';

      if ((!empty($data_mesin['id_bagian'])) && $data_mesin['id_bagian'] == 'fd2aa961b30ede7622a57d42267edc5d5eae3e1b') {
        $isi['pekerjaan_mesin'] =  $data_mesin['progress_jumlah'];
        $isi['pekerjaan_isi_mesin'] =  (isset($data_user_mesin['klasifikasi_dokumen_inisial'])) ? $data_user_mesin['klasifikasi_dokumen_inisial'] . '(' . $data_mesin['progress_jumlah'] . '%)' : '';
      } else {
        $isi['pekerjaan_mesin'] = 0;
        $isi['pekerjaan_isi_mesin'] = (isset($data_user_mesin['klasifikasi_dokumen_inisial'])) ? $data_user_mesin['klasifikasi_dokumen_inisial'] . '(0%)' : '';
      }
      $isi['pekerjaan_isi_mesin'] = ($data_user_mesin_koor['total'] == 0) ? $isi['pekerjaan_isi_mesin'] : '<b>' . $isi['pekerjaan_isi_mesin'] . '</b>';

      if ((!empty($data_listrik['bagian_id'])) && $data_listrik['bagian_id'] == 'f683cbbca693d1a08fc010fd861b7350efa3e8d2' && $data_listrik['is_listin'] == 'L') {

        $isi['pekerjaan_listrik'] =  $data_listrik['progress_jumlah'];
        $isi['pekerjaan_isi_listrik'] =  (isset($data_user_listrik['klasifikasi_dokumen_inisial'])) ? $data_user_listrik['klasifikasi_dokumen_inisial'] . '(' . $data_listrik['progress_jumlah'] . '%)' : '';
      } else {
        $isi['pekerjaan_listrik'] = 0;
        $isi['pekerjaan_isi_listrik'] = (isset($data_user_listrik['klasifikasi_dokumen_inisial'])) ? $data_user_listrik['klasifikasi_dokumen_inisial'] . '(0%)' : '';
      }
      $isi['pekerjaan_isi_listrik'] = ($data_user_listrik_koor['total'] == 0) ? $isi['pekerjaan_isi_listrik'] : '<b>' . $isi['pekerjaan_isi_listrik'] . '</b>';

      if ((!empty($data_instrumen['bagian_id'])) && $data_instrumen['bagian_id'] == 'f683cbbca693d1a08fc010fd861b7350efa3e8d2' && $data_instrumen['is_listin'] == 'I') {

        $isi['pekerjaan_instrumen'] =  $data_instrumen['progress_jumlah'];
        $isi['pekerjaan_isi_instrumen'] =  (isset($data_user_instrumen['klasifikasi_dokumen_inisial'])) ? $data_user_instrumen['klasifikasi_dokumen_inisial'] . '(' . $data_instrumen['progress_jumlah'] . '%)' : '';
      } else {
        $isi['pekerjaan_instrumen'] = 0;
        $isi['pekerjaan_isi_instrumen'] = (isset($data_user_instrumen['klasifikasi_dokumen_inisial'])) ? $data_user_instrumen['klasifikasi_dokumen_inisial'] . '(0%)' : '';
      }
      $isi['pekerjaan_isi_instrumen'] = ($data_user_instrumen_koor['total'] == 0) ? $isi['pekerjaan_isi_instrumen'] : '<b>' . $isi['pekerjaan_isi_instrumen'] . '</b>';

      if ($jml_sipil > 0 && $data_sipil['bagian_id'] == '1483c0882e75988626fee21c5926cc63727734a0') {
        $isi['pekerjaan_jumlah_sipil'] = ($jml_sipil['total'] > 0) ? $jml_sipil['total'] : 0;
      } else {
        $isi['pekerjaan_jumlah_sipil'] = 0;
      }

      if ($jml_proses > 0 && $data_proses['bagian_id'] == 'c21f86a03fdf9f7420764ac49d664415cfc942eb') {
        $isi['pekerjaan_jumlah_proses'] = ($jml_proses > 0) ? $jml_proses : 0;
      } else {
        $isi['pekerjaan_jumlah_proses'] = 0;
      }

      if ($jml_mesin > 0 && $data_mesin['bagian_id'] == 'fd2aa961b30ede7622a57d42267edc5d5eae3e1b') {
        $isi['pekerjaan_jumlah_mesin'] = ($jml_mesin > 0) ? $jml_mesin : 0;
      } else {
        $isi['pekerjaan_jumlah_mesin'] = 0;
      }

      if ((!empty($data_listrik)) && $jml_listrik > 0 && $data_listrik['bagian_id'] == 'f683cbbca693d1a08fc010fd861b7350efa3e8d2') {
        $isi['pekerjaan_jumlah_listrik'] = ($jml_listrik > 0) ? $jml_listrik : 0;
      } else {
        $isi['pekerjaan_jumlah_listrik'] = 0;
      }

      if ((!empty($data_instrumen)) && $jml_instrumen > 0 && $data_instrumen['bagian_id'] == 'f683cbbca693d1a08fc010fd861b7350efa3e8d2') {
        $isi['pekerjaan_jumlah_instrumen'] = ($jml_instrumen > 0) ? $jml_instrumen : 0;
      } else {
        $isi['pekerjaan_jumlah_instrumen'] = 0;
      }

      if (($isi['pekerjaan_proses'] + $isi['pekerjaan_mesin'] + $isi['pekerjaan_listrik'] + $isi['pekerjaan_instrumen'] + $isi['pekerjaan_sipil'] > 0) && ($isi['pekerjaan_jumlah_proses'] + $isi['pekerjaan_jumlah_mesin'] + $isi['pekerjaan_jumlah_listrik'] + $isi['pekerjaan_jumlah_instrumen'] + $isi['pekerjaan_jumlah_sipil'] > 0)) {
        $isi_progressnya = ($isi['pekerjaan_proses'] + $isi['pekerjaan_mesin'] + $isi['pekerjaan_listrik'] + $isi['pekerjaan_instrumen'] + $isi['pekerjaan_sipil']) / ($isi_total['total']);
      } else {
        $isi_progressnya = 0;
      }

      $sql_tgl_start = $this->db->query("SELECT pekerjaan_disposisi_waktu FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND pekerjaan_disposisi_status ='4'");
      $data_tgl_start = $sql_tgl_start->row_array();

      $sql_avp_review = $this->db->query("SELECT count(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND pekerjaan_disposisi_status ='6' AND id_user = '" . $session['pegawai_nik'] . "' AND is_aktif = 'y'");
      $data_avp_review = $sql_avp_review->row_array();

      $status_avp = ($value['pekerjaan_status'] == '5' && $data_avp_review['total'] >= '1') ? '1' : 0;

      $data_bagian = $this->db->query("SELECT * FROM global.global_bagian_detail WHERE id_pegawai = '" . $session['pegawai_nik'] . "'")->row_array();


      if (!empty($data_bagian)) {
        $dokumen_revisi = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "'  AND is_lama!='y' AND (pekerjaan_dokumen_revisi !='' OR pekerjaan_dokumen_revisi != null) ")->row_array();
      } else {
        $dokumen_revisi = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND is_lama!='y' AND (pekerjaan_dokumen_revisi !='' OR pekerjaan_dokumen_revisi != null) ")->row_array();
      }

      $revisi_dokumen = ($dokumen_revisi['total'] > 0) ? '1' : 0;

      /* tambahan */
      $proses_perencana = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status='5' AND id_pekerjaan='" . $value['pekerjaan_id'] . "' AND id_user='" . $session['pegawai_nik'] . "' AND is_proses='y'")->num_rows();
      $avp_koor = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status='6' AND id_pekerjaan='" . $value['pekerjaan_id'] . "' AND id_user='" . $session['pegawai_nik'] . "' AND id_penanggung_jawab = 'y'")->num_rows();
      $bidang = $this->db->query("SELECT * FROM global.global_bagian a LEFT jOIN global.global_bagian_detail b ON b.id_bagian = a.bagian_id WHERE id_pegawai = '" . $session['pegawai_nik'] . "'")->row_array();
      $avp_terkait_proses = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status='6' AND id_pekerjaan='" . $value['pekerjaan_id'] . "' AND id_user='" . $session['pegawai_nik'] . "' AND id_penanggung_jawab = 'n' AND is_proses = 'y'")->num_rows();

      $isi['milik'] = ($dataMilik['total'] > 0 || ($value['pic'] == $session['pegawai_nik'] && ($value['pekerjaan_status'] == 0 || $value['pekerjaan_status'] == '-')) || $session['pegawai_nik'] == $this->admin_sistemnya) ? 'y' : 'n';
      $isi['pekerjaan_progress'] = round($isi_progressnya, 2);
      $isi['pekerjaan_id'] = $value['pekerjaan_id'];
      $isi['pekerjaan_nomor'] = $value['pekerjaan_nomor'];
      $isi['pekerjaan_judul'] = $value['pekerjaan_judul'];
      $isi['pekerjaan_status'] = $value['pekerjaan_status'];
      $isi['pegawai_nama'] = $value['pegawai_nama'];
      $isi['total'] = $isi_total['total'];
      $isi['tanggal_akhir'] =  date("Y-m-d", strtotime($value['tanggal_akhir']));
      $isi['tanggal_start'] = ($data_tgl_start['pekerjaan_disposisi_waktu'] != '') ? date("Y-m-d", strtotime($data_tgl_start['pekerjaan_disposisi_waktu'])) : '-';
      $isi['pekerjaan_status'] = $value['pekerjaan_status'];
      $isi['status_avp'] = $status_avp;
      $isi['revisi_dokumen'] = $revisi_dokumen;
      $isi['perencana_proses'] = $proses_perencana;
      $isi['avp_koor'] = $avp_koor;
      $isi['avp_terkait_proses'] = $avp_terkait_proses;
      $isi['bagian_nama'] = ($bidang) ? $bidang['bagian_nama'] : '';


      /* tambahan */

      array_push($data, $isi);
    }
    echo json_encode($data);
  } else {
   foreach ($this->M_pekerjaan->getPekerjaanDispo($param) as $key => $value) {
     $sql_total = $this->db->query("SELECT  count(distinct id_user) as total  FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '5' AND id_pekerjaan = '" . $value['pekerjaan_id'] . "' ");
     $isi_total = $sql_total->row_array();

     $sql = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '" . $value['pekerjaan_status'] . "' AND id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND  id_user = '" . $session['pegawai_nik'] . "'  ");
     $dataMilik = $sql->row_array();

     $sql_sipil = $this->db->query("SELECT bagian_id,id_bagian,progress_jumlah FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='1483c0882e75988626fee21c5926cc63727734a0'");
     $data_sipil = $sql_sipil->row_array();

     $sql_jml_sipil = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='1483c0882e75988626fee21c5926cc63727734a0'");
     $data_jml_sipil = $sql_jml_sipil->row_array();

     $sql_user_sipil = $this->db->query("SELECT klasifikasi_dokumen_inisial FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '5' AND c.id_bagian = '1483c0882e75988626fee21c5926cc63727734a0'");
     $data_user_sipil = $sql_user_sipil->row_array();

     $sql_user_sipil_koor = $this->db->query("SELECT count(*) AS total FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '4' AND id_penanggung_jawab = 'y' AND c.id_bagian = '1483c0882e75988626fee21c5926cc63727734a0'");
     $data_user_sipil_koor = $sql_user_sipil_koor->row_array();


     $sql_proses = $this->db->query("SELECT bagian_id,id_bagian,progress_jumlah FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='c21f86a03fdf9f7420764ac49d664415cfc942eb'");
     $data_proses = $sql_proses->row_array();

     $sql_jml_proses = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='c21f86a03fdf9f7420764ac49d664415cfc942eb'");
     $data_jml_proses = $sql_jml_proses->row_array();

     $sql_user_proses = $this->db->query("SELECT klasifikasi_dokumen_inisial FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '5' AND c.id_bagian = 'c21f86a03fdf9f7420764ac49d664415cfc942eb'");
     $data_user_proses = $sql_user_proses->row_array();

     $sql_user_proses_koor = $this->db->query("SELECT count(*) AS total FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '4' AND id_penanggung_jawab = 'y' AND c.id_bagian = 'c21f86a03fdf9f7420764ac49d664415cfc942eb'");
     $data_user_proses_koor = $sql_user_proses_koor->row_array();


     $sql_mesin = $this->db->query("SELECT bagian_id,id_bagian,progress_jumlah FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='fd2aa961b30ede7622a57d42267edc5d5eae3e1b'");
     $data_mesin = $sql_mesin->row_array();

     $sql_jml_mesin = $this->db->query("SELECT COUNT(*) as total FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='fd2aa961b30ede7622a57d42267edc5d5eae3e1b'");
     $data_jml_mesin = $sql_jml_mesin->row_array();

     $sql_user_mesin = $this->db->query("SELECT klasifikasi_dokumen_inisial FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '5' AND c.id_bagian = 'fd2aa961b30ede7622a57d42267edc5d5eae3e1b'");
     $data_user_mesin = $sql_user_mesin->row_array();

     $sql_user_mesin_koor = $this->db->query("SELECT count(*) AS total FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '4' AND id_penanggung_jawab = 'y' AND c.id_bagian = 'fd2aa961b30ede7622a57d42267edc5d5eae3e1b'");
     $data_user_mesin_koor = $sql_user_mesin_koor->row_array();


     $sql_listrik = $this->db->query("SELECT bagian_id,id_bagian,progress_jumlah FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian LEFT JOIN dec.dec_pekerjaan_disposisi c ON c.id_user = a.id_user  AND a.id_pekerjaan = c.id_pekerjaan WHERE c.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND is_listin = 'L'");
     $data_listrik = $sql_listrik->row_array();

     $sql_jml_listrik = $this->db->query("SELECT COUNT(*) as total FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian LEFT JOIN dec.dec_pekerjaan_disposisi c ON c.id_user = a.id_user AND a.id_pekerjaan = c.id_pekerjaan AND a.id_pekerjaan = c.id_pekerjaan WHERE c.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND is_listin = 'L'");
     $data_jml_listrik = $sql_jml_listrik->row_array();

     $sql_user_listrik = $this->db->query("SELECT klasifikasi_dokumen_inisial FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '5' AND c.id_bagian = 'f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND b.is_listin = 'L'");
     $data_user_listrik = $sql_user_listrik->row_array();

     $sql_user_listrik_koor = $this->db->query("SELECT count(*) AS total FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '4' AND id_penanggung_jawab = 'y' AND c.id_bagian = 'f683cbbca693d1a08fc010fd861b7350efa3e8d2'");
     $data_user_listrik_koor = $sql_user_listrik_koor->row_array();


     $sql_instrumen = $this->db->query("SELECT bagian_id,id_bagian,progress_jumlah FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian LEFT JOIN dec.dec_pekerjaan_disposisi c ON c.id_user = a.id_user AND a.id_pekerjaan = c.id_pekerjaan WHERE c.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND is_listin='I'");
     $data_instrumen = $sql_instrumen->row_array();

     $sql_jml_instrumen = $this->db->query("SELECT COUNT(*) as total FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian LEFT JOIN dec.dec_pekerjaan_disposisi c ON c.id_user = a.id_user  AND a.id_pekerjaan = c.id_pekerjaan WHERE c.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND is_listin='I'");
     $data_jml_instrumen = $sql_jml_instrumen->row_array();

     $sql_user_instrumen = $this->db->query("SELECT klasifikasi_dokumen_inisial FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '5' AND c.id_bagian = 'f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND b.is_listin = 'I'");
     $data_user_instrumen = $sql_user_instrumen->row_array();

     $sql_user_instrumen_koor = $this->db->query("SELECT count(*) AS total FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '4' AND id_penanggung_jawab = 'y' AND c.id_bagian = 'f683cbbca693d1a08fc010fd861b7350efa3e8d2'");
     $data_user_instrumen_koor = $sql_user_instrumen_koor->row_array();

     /*data per progress*/

     $sql_progress = $this->db->query("select pekerjaan_id,bagian_id,bagian_nama,progress_jumlah,id_pekerjaan from global.global_bagian a left join global.global_bagian_detail b on b.id_bagian = a.bagian_id left join dec.dec_pekerjaan_progress c on c.id_user = b.id_pegawai right join dec.dec_pekerjaan d on d.pekerjaan_id = c.id_pekerjaan where pekerjaan_id ='" . $value['pekerjaan_id'] . "' order by progress_jumlah desc");

     $isi_progress = $sql_progress->result_array();



     $sql_jumlah_progress = $this->db->query("select count(*) as total from global.global_bagian a left join global.global_bagian_detail b on b.id_bagian = a.bagian_id left join dec.dec_pekerjaan_progress c on c.id_user = b.id_pegawai right join dec.dec_pekerjaan d on d.pekerjaan_id = c.id_pekerjaan where pekerjaan_id ='" . $value['pekerjaan_id'] . "' ");
     $isi_jumlah_proses = $sql_jumlah_progress->row_array();


     $isi['milik'] = ($dataMilik['total'] > 0 || ($value['pic'] == $session['pegawai_nik'] && ($value['pekerjaan_status'] == 0 || $value['pekerjaan_status'] == '-'))) ? 'y' : 'n';


     if (!empty($data_sipil['id_bagian']) && $data_sipil['id_bagian'] == '1483c0882e75988626fee21c5926cc63727734a0') {
      $isi['pekerjaan_sipil'] =  $data_sipil['progress_jumlah'];
      $isi['pekerjaan_isi_sipil'] =  (isset($data_user_sipil['klasifikasi_dokumen_inisial'])) ? $data_user_sipil['klasifikasi_dokumen_inisial'] . '(' . $data_sipil['progress_jumlah'] . '%)' : '';
    } else {
      $isi['pekerjaan_sipil'] = 0;
      $isi['pekerjaan_isi_sipil'] = (isset($data_user_sipil['klasifikasi_dokumen_inisial'])) ? $data_user_sipil['klasifikasi_dokumen_inisial'] . '(0%)' : '';
    }
    
    $isi['pekerjaan_isi_sipil'] = ($data_user_sipil_koor['total'] == 0) ? $isi['pekerjaan_isi_sipil'] : '<b>' . $isi['pekerjaan_isi_sipil'] . '</b>';

    if (!empty($data_proses['bagian_id']) && $data_proses['bagian_id'] == 'c21f86a03fdf9f7420764ac49d664415cfc942eb') {
      $isi['pekerjaan_proses'] =  $data_proses['progress_jumlah'];
      $isi['pekerjaan_isi_proses'] =  (isset($data_user_proses['klasifikasi_dokumen_inisial'])) ? $data_user_proses['klasifikasi_dokumen_inisial'] . '(' . $data_proses['progress_jumlah'] . '%)' : '';
    } else {
      $isi['pekerjaan_proses'] = 0;
      $isi['pekerjaan_isi_proses'] = (isset($data_user_proses['klasifikasi_dokumen_inisial'])) ? $data_user_proses['klasifikasi_dokumen_inisial'] . '(0%)' : '';
    }
    $isi['pekerjaan_isi_proses'] = ($data_user_proses_koor['total'] == 0) ? $isi['pekerjaan_isi_proses'] : '<b>' . $isi['pekerjaan_isi_proses'] . '</b>';

    if ((!empty($data_mesin['id_bagian'])) && $data_mesin['id_bagian'] == 'fd2aa961b30ede7622a57d42267edc5d5eae3e1b') {
      $isi['pekerjaan_mesin'] =  $data_mesin['progress_jumlah'];
      $isi['pekerjaan_isi_mesin'] =  (isset($data_user_mesin['klasifikasi_dokumen_inisial'])) ? $data_user_mesin['klasifikasi_dokumen_inisial'] . '(' . $data_mesin['progress_jumlah'] . '%)' : '';
    } else {
      $isi['pekerjaan_mesin'] = 0;
      $isi['pekerjaan_isi_mesin'] = (isset($data_user_mesin['klasifikasi_dokumen_inisial'])) ? $data_user_mesin['klasifikasi_dokumen_inisial'] . '(0%)' : '';
    }
    $isi['pekerjaan_isi_mesin'] = ($data_user_mesin_koor['total'] == 0) ? $isi['pekerjaan_isi_mesin'] : '<b>' . $isi['pekerjaan_isi_mesin'] . '</b>';

    if ((!empty($data_listrik['bagian_id'])) && $data_listrik['bagian_id'] == 'f683cbbca693d1a08fc010fd861b7350efa3e8d2') {
      $isi['pekerjaan_listrik'] =  $data_listrik['progress_jumlah'];
      $isi['pekerjaan_isi_listrik'] =  (isset($data_user_listrik['klasifikasi_dokumen_inisial'])) ? $data_user_listrik['klasifikasi_dokumen_inisial'] . '(' . $data_listrik['progress_jumlah'] . '%)' : '';
    } else {
      $isi['pekerjaan_listrik'] = 0;
      $isi['pekerjaan_isi_listrik'] = (isset($data_user_listrik['klasifikasi_dokumen_inisial'])) ? $data_user_listrik['klasifikasi_dokumen_inisial'] . '(0%)' : '';
    }
    $isi['pekerjaan_isi_listrik'] = ($data_user_listrik_koor['total'] == 0) ? $isi['pekerjaan_isi_listrik'] : '<b>' . $isi['pekerjaan_isi_listrik'] . '</b>';

    if ((!empty($data_instrumen['bagian_id'])) && $data_instrumen['bagian_id'] == 'f683cbbca693d1a08fc010fd861b7350efa3e8d2') {
      $isi['pekerjaan_instrumen'] =  $data_instrumen['progress_jumlah'];
      $isi['pekerjaan_isi_instrumen'] =  (isset($data_user_instrumen['klasifikasi_dokumen_inisial'])) ? $data_user_instrumen['klasifikasi_dokumen_inisial'] . '(' . $data_instrumen['progress_jumlah'] . '%)' : '';
    } else {
      $isi['pekerjaan_instrumen'] = 0;
      $isi['pekerjaan_isi_instrumen'] = (isset($data_user_instrumen['klasifikasi_dokumen_inisial'])) ? $data_user_instrumen['klasifikasi_dokumen_inisial'] . '(0%)' : '';
    }
    $isi['pekerjaan_isi_instrumen'] = ($data_user_instrumen_koor['total'] == 0) ? $isi['pekerjaan_isi_instrumen'] : '<b>' . $isi['pekerjaan_isi_instrumen'] . '</b>';



    /*foreach ($isi_progress as $key => $value_progress) {*/

     if ($data_jml_sipil['total'] > 0 && $data_sipil['bagian_id'] == '1483c0882e75988626fee21c5926cc63727734a0') {
      $isi['pekerjaan_jumlah_sipil'] = ($data_jml_sipil['total'] > 0) ? $data_jml_sipil['total'] : 0;
    } else {
      $isi['pekerjaan_jumlah_sipil'] = 0;
    }

    if ($data_jml_proses['total'] > 0 && $data_proses['bagian_id'] == 'c21f86a03fdf9f7420764ac49d664415cfc942eb') {
      $isi['pekerjaan_jumlah_proses'] = ($data_jml_proses['total'] > 0) ? $data_jml_proses['total'] : 0;
    } else {
      $isi['pekerjaan_jumlah_proses'] = 0;
    }

    if ($data_jml_mesin['total'] > 0 && $data_mesin['bagian_id'] == 'fd2aa961b30ede7622a57d42267edc5d5eae3e1b') {
      $isi['pekerjaan_jumlah_mesin'] = ($data_jml_mesin['total'] > 0) ? $data_jml_mesin['total'] : 0;
    } else {
      $isi['pekerjaan_jumlah_mesin'] = 0;
    }

    if ($data_jml_listrik['total'] > 0 && $data_listrik['bagian_id'] == 'f683cbbca693d1a08fc010fd861b7350efa3e8d2') {
      $isi['pekerjaan_jumlah_listrik'] = ($data_jml_listrik['total'] > 0) ? $data_jml_listrik['total'] : 0;
    } else {
      $isi['pekerjaan_jumlah_listrik'] = 0;
    }

    if ($data_jml_instrumen['total'] > 0 && $data_instrumen['bagian_id'] == 'f683cbbca693d1a08fc010fd861b7350efa3e8d2') {
      $isi['pekerjaan_jumlah_instrumen'] = ($data_jml_instrumen['total'] > 0) ? $data_jml_instrumen['total'] : 0;
    } else {
      $isi['pekerjaan_jumlah_instrumen'] = 0;
    }
    /*}*/

    if (($isi['pekerjaan_proses'] + $isi['pekerjaan_mesin'] + $isi['pekerjaan_listrik'] + $isi['pekerjaan_instrumen'] + $isi['pekerjaan_sipil'] > 0) && ($isi['pekerjaan_jumlah_proses'] + $isi['pekerjaan_jumlah_mesin'] + $isi['pekerjaan_jumlah_listrik'] + $isi['pekerjaan_jumlah_instrumen'] + $isi['pekerjaan_jumlah_sipil'] > 0)) {
      $isi_progressnya = ($isi['pekerjaan_proses'] + $isi['pekerjaan_mesin'] + $isi['pekerjaan_listrik'] + $isi['pekerjaan_instrumen'] + $isi['pekerjaan_sipil']) / (($isi_total['total']));
    } else {
      $isi_progressnya = 0;
    }

    $sql_tgl_start = $this->db->query("SELECT pekerjaan_disposisi_waktu FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND pekerjaan_disposisi_status ='4' AND is_aktif = 'y'");
    $data_tgl_start = $sql_tgl_start->row_array();

    $sql_avp_review = $this->db->query("SELECT count(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND pekerjaan_disposisi_status ='6' AND id_user = '" . $session['pegawai_nik'] . "' AND is_aktif = 'y'");
    $data_avp_review = $sql_avp_review->row_array();

    $status_avp = ($value['pekerjaan_status'] == '5' && $data_avp_review['total'] >= '1') ? '1' : 0;

    $data_bagian = $this->db->query("SELECT * FROM global.global_bagian_detail WHERE id_pegawai = '" . $session['pegawai_nik'] . "'")->row_array();


    if (!empty($data_bagian)) {
      $dokumen_revisi = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "'  AND is_lama!='y' AND (pekerjaan_dokumen_revisi !='' OR pekerjaan_dokumen_revisi != null) ")->row_array();
    } else {
      $dokumen_revisi = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND is_lama!='y' AND (pekerjaan_dokumen_revisi !='' OR pekerjaan_dokumen_revisi != null) ")->row_array();
    }

    $revisi_dokumen = ($dokumen_revisi['total'] > 0) ? '1' : 0;

    $isi['pekerjaan_progress'] = round($isi_progressnya, 2);
    $isi['pekerjaan_id'] = $value['pekerjaan_id'];
    $isi['pekerjaan_nomor'] = $value['pekerjaan_nomor'];
    $isi['pekerjaan_judul'] = $value['pekerjaan_judul'];
    $isi['pekerjaan_status'] = $value['pekerjaan_status'];
    $isi['pegawai_nama'] = $value['pegawai_nama'];
    /*        $isi['pekerjaan_progress'] = $value['pekerjaan_progress'];*/
    $isi['total'] = $isi_total['total'];
    $isi['tanggal_akhir'] =  date("Y-m-d", strtotime($value['tanggal_akhir']));
    $isi['tanggal_start'] = ($data_tgl_start['pekerjaan_disposisi_waktu'] != '') ? date("Y-m-d", strtotime($data_tgl_start['pekerjaan_disposisi_waktu'])) : '-';
    $isi['pekerjaan_status'] = $value['pekerjaan_status'];
    $isi['status_avp'] = $status_avp;
    $isi['revisi_dokumen'] = $revisi_dokumen;

    /* tambahan */
    $proses_perencana = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status='5' AND id_pekerjaan='" . $value['pekerjaan_id'] . "' AND id_user='" . $session['pegawai_nik'] . "' AND is_proses='y'")->num_rows();
    $avp_koor = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status='6' AND id_pekerjaan='" . $value['pekerjaan_id'] . "' AND id_user='" . $session['pegawai_nik'] . "' AND id_penanggung_jawab = 'y'")->num_rows();
    $bidang = $this->db->query("SELECT * FROM global.global_bagian a LEFT jOIN global.global_bagian_detail b ON b.id_bagian = a.bagian_id WHERE id_pegawai = '" . $session['pegawai_nik'] . "'")->row_array();
    $avp_terkait_proses = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status='6' AND id_pekerjaan='" . $value['pekerjaan_id'] . "' AND id_user='" . $session['pegawai_nik'] . "' AND id_penanggung_jawab = 'n' AND is_proses = 'y'")->num_rows();
    $isi['perencana_proses'] = $proses_perencana;
    $isi['avp_koor'] = $avp_koor;
    $isi['avp_terkait_proses'] = $avp_terkait_proses;
    $isi['bagian_nama'] = $bidang['bagian_nama'];


    /* tambahan */

    array_push($data, $isi);
  }
  echo json_encode($data);
}
}
/* Get Pekerjaan Berjalan */
/* PEKERJAAN BERJALAN */

/* DETAIL PEKERJAAN */
/* Get History */
public function getHistory()
{
	$param = array();

	if ($this->input->get('id_pekerjaan')) $param['id_pekerjaan'] = $this->input->get('id_pekerjaan');

	$data = $this->M_pekerjaan->getHistory($param);






	echo json_encode($data);
}
/* Get History */

/* Approve */
public function prosesReview()
{
	if ($this->input->get('id_user')) {
    $isi = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $this->input->get('id_user')))->row_array();
  } else {
    $isi = $this->session->userdata();
  }

  /* Pekerjaan */
  $pekerjaan_status = $this->input->get('pekerjaan_status') + 1;

  $pekerjaan_id = $this->input->get('pekerjaan_id');
  if ($pekerjaan_id) {
    $data['pekerjaan_status'] = anti_inject($pekerjaan_status);
    $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
  }

  $data_user['pegawai_poscode'] = ($pekerjaan_status == '3') ? 'E53000000' : $isi['pegawai_direct_superior'];
  $user = $this->M_user->getUser($data_user);
  /* Notikikasi */
  $tujuan = $user['pegawai_nik'];
  $tujuan_nama = $user['pegawai_nama'];
  $text = "Mohon untuk melakukan APPROVE pada pekerjaan ini";

  dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reviewed', $isi['pegawai_nik']);
  tasklog($pekerjaan_id, $pekerjaan_status, $user['pegawai_nik'], $text, 'n');
  sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
  sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $text);
  /* Notikikasi */
  /* Pekerjaan */

  $where_id_user = ($isi['pegawai_nik']);
  $where_id_pekerjaan = (($pekerjaan_id));
  $where_disposisi_status = '1';
  $param_staf['is_proses'] = 'y';
  $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);
}
/* Approve */

/* Approve */
public function prosesApprove()
{

	if ($this->input->get('id_user')) {
    $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'");
    $isi = $sql_isi->row_array();
  } else {
    $isi = $this->session->userdata();
  }
  /* Pekerjaan */
  $pekerjaan_status = $this->input->get('pekerjaan_status') + 1;

  $pekerjaan_id = $this->input->get('pekerjaan_id');
  if ($pekerjaan_id) {
    $data['pekerjaan_status'] = anti_inject($pekerjaan_status);
    $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
  }

  $data_user['pegawai_poscode'] = ($pekerjaan_status == '3') ? 'E53000000' : $isi['pegawai_direct_superior'];
  $user = $this->M_user->getUser($data_user);

  $tujuan = $user['pegawai_nik'];
  $tujuan_nama = $user['pegawai_nama'];
  $text = "Mohon untuk melakukan APPROVE dan DISPOSISI pada pekerjaan ini";
  dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Approve', $isi['pegawai_nik']);
  tasklog($pekerjaan_id, $pekerjaan_status, $user['pegawai_nik'], $text, 'n');
  sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
  sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $text);
  /* Pekerjaan */

  $where_id_user = ($isi['pegawai_nik']);
  $where_id_pekerjaan = (($pekerjaan_id));
  $where_disposisi_status = '2';
  $param_staf['is_proses'] = 'y';
  $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);


  /* User */
  $data_user['pegawai_poscode'] = ($pekerjaan_status == '3') ? 'E53000000' : $isi['pegawai_direct_superior'];
  $user = $this->M_user->getUser($data_user);
  /* User */

  /* Disposisi */
  $data_disposisi['pekerjaan_disposisi_id'] = create_id();
  $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
  $data_disposisi['id_user'] = anti_inject($user['pegawai_nik']);
  $data_disposisi['id_pekerjaan'] = anti_inject($pekerjaan_id);
  $data_disposisi['pekerjaan_disposisi_status'] = anti_inject($pekerjaan_status);

  $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
  /* Disposisi */
}
/* Approve */



/* Reject */
public function prosesReject()
{
	$user = (isset($_GET['id_user'])) ? $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $_GET['id_user']))->row_array() : $this->session->userdata();

	/* Pekerjaan */
	$pekerjaan_id = $this->input->get('pekerjaan_id');
	if ($pekerjaan_id) {
    $data['pekerjaan_status'] = anti_inject('-');
    $data['pekerjaan_note'] = anti_inject($this->input->get_post('note_reject'));

    $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

    $pic = $this->db->query("SELECT a.pic as pegawai_nik,b.pegawai_nama FROM dec.dec_pekerjaan a LEFT JOIN global.global_pegawai b ON b.pegawai_nik = a.pic WHERE pekerjaan_id = '" . $pekerjaan_id . "'")->row_array();

    $dari = $user['pegawai_nik'];
    $tujuan = $pic['pegawai_nik'];
    $tujuan_nama = $pic['pegawai_nama'];
    $text = "Pekerjaan anda telah di REJECT dengan alasan : " . $data['pekerjaan_note'];
    sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
    sendNotif($pekerjaan_id, $dari, $tujuan, $text);

    if($this->input->get('pekerjaan_status')=='1'):
      $text_log = 'Pekerjaan Usulan Telah Direvisi AVP Customer, Alasan <span style="color:red">' . $data['pekerjaan_note'] . '</span>';
    elseif($this->input->get('pekerjaan_status')=='2'):
      $text_log = 'Pekerjaan Usulan Telah Direvisi VP Customer, Alasan <span style="color:red">' . $data['pekerjaan_note'] . '</span>';
    elseif($this->input->get('pekerjaan_status')=='3'):
      $text_log = 'Pekerjaan Usulan Telah Direvisi VP Rancang Bangun, Alasan <span style="color:red">' . $data['pekerjaan_note'] . '</span>';
    endif;

    dblog('I',  $pekerjaan_id, $text_log, $user['pegawai_nik']);
  }
  /* Pekerjaan */

  /*delete disposisi*/
  $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status <= '3' ");
}
/* Reject */


/*Reject AVP */
public function prosesRejectAVP()
{
	if (isset($_GET['id_user'])) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $user = $sql_user->row_array();
  } else {
    $user = $this->session->userdata();
  }

  /*cek pekerjaan penanggung jawab*/
  $pekerjaan_id = $this->input->get('pekerjaan_id');

  $sql_pekerjaan = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '4' AND id_user = '" . $user['pegawai_nik'] . "'");
  $data_pekerjaan = $sql_pekerjaan->row_array();

  /*    jika koordinator*/
  if ($data_pekerjaan['id_penanggung_jawab'] == 'y') {
    $pekerjaan_id = $data_pekerjaan['id_pekerjaan'];
    $disposisi_status = '4';
    $this->M_pekerjaan->deletePekerjaanDisposisiReject($pekerjaan_id, $disposisi_status, $user_id = null);
    /*unset aktif vp cangun*/
    $where_id_pekerjaan = (($pekerjaan_id));
    $where_disposisi_status = ('3');
    $param_staf['is_proses'] = null;
    $this->M_pekerjaan->updateStatusProses($where_id_user = null, $where_id_pekerjaan, $where_disposisi_status, $param_staf);
    /*      dan kembali ke VP*/
    $param_disposisi['pekerjaan_status'] = '3';
    $param_disposisi['pekerjaan_note'] = $this->input->get_post('note_reject');

    $this->M_pekerjaan->updatePekerjaan($param_disposisi, $pekerjaan_id);
    dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reject AVP Koordinator, Alasan : ' . $this->input->get_post('note_reject'), $user['pegawai_nik']);

    /* Notifikasi DOF */
    $data_user['pegawai_poscode'] = $user['pegawai_direct_superior'];
    $users = $this->M_user->getUser($data_user);
    $dari = $user['pegawai_nik'];
    $tujuan = $users['pegawai_nik'];
    $tujuan_nama = $users['pegawai_nama'];
    $text = "Pekerjaan Telah di Reject AVP Koordinator, Alasan : " . $this->input->get_post('note_reject');
    sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
    sendNotif($pekerjaan_id, $dari, $tujuan, $text);
    /* Notifikasi DOF */

    /*      reject terkait sadja*/
  } else {
    $pekerjaan_id = $data_pekerjaan['id_pekerjaan'];
    $disposisi_status = '4';
    $user_id = $user['pegawai_nik'];
    $this->M_pekerjaan->deletePekerjaanDisposisiReject($pekerjaan_id, $disposisi_status, $user_id);
    dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reject AVP Terkait, Alasan : ' . $this->input->get_post('note_reject'), $user['pegawai_nik']);

    /* Notifikasi DOF */
    $data_user['pegawai_poscode'] = $user['pegawai_direct_superior'];
    $users = $this->M_user->getUser($data_user);
    $dari = $user['pegawai_nik'];
    $tujuan = $users['pegawai_nik'];
    $tujuan_nama = $users['pegawai_nama'];
    $text = "Pekerjaan Telah di Reject AVP Terkait, Alasan : " . $this->input->get_post('note_reject');
    sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
    sendNotif($pekerjaan_id, $dari, $tujuan, $text);
    /* Notifikasi DOF */

    $sql_proses = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '4' AND (is_proses != 'y' OR is_proses is null)");
    $data_proses = $sql_proses->row_array();

    /* JIKA SEMUA SUDAH DIPROSES*/
    if ($data_proses['total'] == 0) {
      $data['pekerjaan_status'] = '5';
      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
    }
  }
}
/*Reject AVP */

/*Reject Staf */
public function prosesRejectStaf()
{
	if (isset($_GET['id_user'])) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $user = $sql_user->row_array();
  } else {
    $user = $this->session->userdata();
  }
  $pekerjaan_id = $this->input->get('pekerjaan_id');
  /*cek bagian*/
  $sql_bagian = $this->db->query("SELECT * FROM global.global_bagian_detail WHERE id_pegawai = '" . $user['pegawai_nik'] . "'");
  $data_bagian = $sql_bagian->row_array();

  $sql_avp_bagian = $this->db->query("SELECT * FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $data_bagian['id_bagian'] . "' AND pekerjaan_disposisi_status = '4' AND id_pekerjaan = '" . $pekerjaan_id . "'");
  $data_avp_bagian = $sql_avp_bagian->row_array();
  /* Reject Staf Koordinator*/
  if ($data_avp_bagian['id_penanggung_jawab'] == 'y') {
    $pekerjaan_id = $pekerjaan_id;
    $disposisi_status = '5';
    $user_id = $user['pegawai_nik'];
    $this->M_pekerjaan->deletePekerjaanDisposisiReject($pekerjaan_id, $disposisi_status, $user_id);

    $data_proses = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '5' AND id_pekerjaan = '" . $pekerjaan_id . "' AND id_user IN(SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian = '" . $data_avp_bagian['id_bagian'] . "') ")->row_array();

    if ($data_proses['total'] == '0') {
      $pekerjaan_id = $pekerjaan_id;
      $disposisi_status = '4';
      $this->M_pekerjaan->deletePekerjaanDisposisiReject($pekerjaan_id, $disposisi_status, $user_id = null);

      $where_id_pekerjaan = (($pekerjaan_id));
      $where_disposisi_status = ('3');
      $param_staf['is_proses'] = null;
      $this->M_pekerjaan->updateStatusProses($where_id_user = null, $where_id_pekerjaan, $where_disposisi_status, $param_staf);

      $param_disposisi['pekerjaan_status'] = '3';
      $param_disposisi['pekerjaan_note'] = $this->input->get_post('note_reject');

      $this->M_pekerjaan->updatePekerjaan($param_disposisi, $pekerjaan_id);
    }

    /* Notifikasi DOF */
    $data_user['pegawai_poscode'] = $user['pegawai_direct_superior'];
    $users = $this->M_user->getUser($data_user);
    $dari = $user['pegawai_nik'];
    $tujuan = $users['pegawai_nik'];
    $tujuan_nama = $users['pegawai_nama'];
    $text = "Pekerjaan Telah di Reject Staf Koordinator, Alasan :" . $this->input->get_post('note_reject');
    sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
    sendNotif($pekerjaan_id, $dari, $tujuan, $text);
    /* Notifikasi DOF */

    dblog(
      'I',
      $pekerjaan_id,
      'Pekerjaan Telah di Reject Staf Koordinator , Alasan ' . $this->input->get_post('note_reject'),
      $user['pegawai_nik']
    );
    /* Reject Staf Terkait */
  } else if ($data_avp_bagian['id_penanggung_jawab'] == 'n') {
   $pekerjaan_id = $pekerjaan_id;
   $disposisi_status = '5';
   $user_id = $user['pegawai_nik'];
   $this->M_pekerjaan->deletePekerjaanDisposisiReject($pekerjaan_id, $disposisi_status, $user_id);

   $data_proses = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '5' AND id_pekerjaan = '" . $pekerjaan_id . "' AND id_user IN(SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian = '" . $data_avp_bagian['id_bagian'] . "') ")->row_array();

   if ($data_proses['total'] == '0') {
     $pekerjaan_id = $pekerjaan_id;
     $disposisi_status = '4';
     $user_id = $data_avp_bagian['id_pegawai'];
     $this->M_pekerjaan->deletePekerjaanDisposisiReject($pekerjaan_id, $disposisi_status, $user_id);
   }

   /* Notifikasi DOF */
   $data_user['pegawai_poscode'] = $user['pegawai_direct_superior'];
   $users = $this->M_user->getUser($data_user);
   $dari = $user['pegawai_nik'];
   $tujuan = $users['pegawai_nik'];
   $tujuan_nama = $users['pegawai_nama'];
   $text = "Pekerjaan Telah di Reject Staf Terkait, Alasan :" . $this->input->get_post('note_reject');
   sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
   sendNotif($pekerjaan_id, $dari, $tujuan, $text);
   /* Notifikasi DOF */

   dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reject Staf Terkait, Alasan ' . $this->input->get_post('note_reject'), $user['pegawai_nik']);
 }
}

/*Reject Staf */

/* Disposisi VP */
public function disposisiVP()
{
	if ($this->input->get('id_user')) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'");
    $isi = $sql_user->row_array();
  } else {
    $isi = $this->session->userdata();
  }

  /* Pekerjaan */
  $pekerjaan_status = '4';

  $pekerjaan_id = $this->input->post('id_pekerjaan_vp');
  if ($pekerjaan_id) {
    $data['pekerjaan_status'] = anti_inject($pekerjaan_status);
    $data['pekerjaan_prioritas'] = $this->input->post('prioritas_pekerjaan_vp');
    $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
  }
  /* Pekerjaan */
  $where_id_user = ($isi['pegawai_nik']);
  $where_id_pekerjaan = (($pekerjaan_id));
  $where_disposisi_status = ('3');
  $param_staf['is_proses'] = 'y';
  $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);

  /* Disposisi */
  $data_disposisi['pekerjaan_disposisi_id'] = create_id();
  $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
  $data_disposisi['id_user'] = anti_inject($this->input->post('id_tanggung_jawab_vp'));
  $data_disposisi['id_pekerjaan'] = anti_inject($pekerjaan_id);
  $data_disposisi['pekerjaan_disposisi_status'] = anti_inject($pekerjaan_status);
  $data_disposisi['id_penanggung_jawab'] = anti_inject('y');
  $data_disposisi['pekerjaan_disposisi_catatan'] = anti_inject($this->input->post('pekerjaan_disposisi_catatan_koordinator'));
  $data_disposisi['pekerjaan_disposisi_prioritas'] = anti_inject($this->input->post('prioritas_pekerjaan_vp'));

  $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

  $param_cari_user['pegawai_nik'] = $data_disposisi['id_user'];
  $cari_user = $this->M_user->getUser($param_cari_user);
  /*Notifikasi*/
  $tujuan = $cari_user['pegawai_nik'];
  $tujuan_nama = $cari_user['pegawai_nama'];
  $text = "Mohon untuk melakukan REVIEW dan DISPOSISI pada pekerjaan ini";
  dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Disposisikan ke ' . $cari_user['pegawai_nama'] . ' Sebagai AVP Koordinator', $isi['pegawai_nik']);
  tasklog($pekerjaan_id, $data_disposisi['pekerjaan_disposisi_status'], $cari_user['pegawai_nik'], $text, 'n');
  sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
  sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $text);
  /*Notifikasi*/

  if ($this->input->post('id_user_vp')) {
    $User = $this->input->post('id_user_vp');
    foreach ($User as $key => $id_user) {
      $data_disposisi['pekerjaan_disposisi_id'] = create_id();
      $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
      $data_disposisi['id_user'] = anti_inject($id_user);
      $data_disposisi['id_pekerjaan'] = anti_inject($pekerjaan_id);
      $data_disposisi['pekerjaan_disposisi_status'] = anti_inject($pekerjaan_status);
      $data_disposisi['id_penanggung_jawab'] = anti_inject('n');
      $data_disposisi['pekerjaan_disposisi_catatan'] = anti_inject($this->input->post('pekerjaan_disposisi_catatan_terkait'));
      $data_disposisi['pekerjaan_disposisi_prioritas'] = anti_inject($this->input->post('prioritas_pekerjaan_vp'));

      $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

      $param_cari_user['pegawai_nik'] = $data_disposisi['id_user'];
      $cari_user = $this->M_user->getUser($param_cari_user);
      /*Notikasi*/
      $tujuan = $cari_user['pegawai_nik'];
      $tujuan_nama = $cari_user['pegawai_nama'];
      $text = "Mohon untuk melakukan REVIEW dan DISPOSISI pada pekerjaan ini";
      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Disposisikan ke ' . $cari_user['pegawai_nama'] . ' Sebagai AVP Terkait', $isi['pegawai_nik']);
      tasklog($pekerjaan_id, $data_disposisi['pekerjaan_disposisi_status'], $cari_user['pegawai_nik'], $text, 'n');
      sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
      sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $text);
      /*Notikasi*/
    }
  }
  /* Disposisi */
}
/* Disposisi VP */

/* Disposisi AVP */
public function disposisiAVP()
{
	if ($this->input->get('id_user')) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'");
    $isi = $sql_user->row_array();
  } else {
    $isi = $this->session->userdata();
  }

  $pekerjaan_status_vp_avp = '4';
  $pekerjaan_id = $this->input->post('id_pekerjaan_avp');
  $id_tanggung_jawab = 'n';
  $is_proses = 'y';

  if ($this->input->get_post('id_user_vp_avp')) {
    $user = $this->input->get_post('id_user_vp_avp');
    $user_implode = implode("','", $user);
    $dispo = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '4' AND id_penanggung_jawab = '" . $id_tanggung_jawab . "' ")->result_array();
     foreach ($dispo as $values) {
       $data_hapus = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $values['id_user']))->row_array();
       $tujuan = $data_hapus['pegawai_nik'];
       $tujuan_nama = $data_hapus['pegawai_nama'];
       $text = "Anda Telah Dihapus Dari Pekerjaan Sebagai AVP Terkait";
       dblog('I',  $pekerjaan_id, '' . $data_hapus['pegawai_nama'] . ' Telah Dihapus dari AVP Terkait', $isi['pegawai_nik']);
       tasklog($pekerjaan_id, '4', $data_hapus['pegawai_nik'], $text, 'n');
       sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
       sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $text);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $values['id_user'] . "' AND pekerjaan_disposisi_status = '4' AND id_penanggung_jawab = '" . $id_tanggung_jawab . "'");
     }
     foreach ($user as $key => $value) {
       $ada_dispo = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '4' AND id_user = '" . $value . "' AND id_penanggung_jawab = '" . $id_tanggung_jawab . "'")->row_array();
       if ($ada_dispo['total'] == 0) {
        $data_prioritas = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status ='4' AND id_user = '" . $isi['pegawai_nik'] . "' AND id_penanggung_jawab = 'y'")->row_array();
        $data_disposisi['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
        $data_disposisi['id_user'] = anti_inject($value);
        $data_disposisi['id_pekerjaan'] = anti_inject($pekerjaan_id);
        $data_disposisi['pekerjaan_disposisi_status'] = anti_inject($pekerjaan_status_vp_avp);
        $data_disposisi['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi['pekerjaan_disposisi_prioritas'] = anti_inject($data_prioritas['pekerjaan_disposisi_prioritas']);
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
        $data_dispo = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();

        $tujuan = $data_dispo['pegawai_nik'];
        $tujuan_nama = $data_dispo['pegawai_nama'];
        $text = "Mohon untuk melakukan REVIEW dan DISPOSISI pada pekerjaan ini";
        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Disposisikan ke ' . $data_dispo['pegawai_nama'] . ' Sebagai AVP Terkait', $isi['pegawai_nik']);
        tasklog($pekerjaan_id, $data_disposisi['pekerjaan_disposisi_status'], $data_dispo['pegawai_nik'], $text, 'n');
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
        sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $text);
      }
    }
  }

  /* Pekerjaan */
  $pekerjaan_status = '5';
  /*    INSERT UNTUK KOORDINATOR*/
  $pekerjaan_id = $this->input->post('id_pekerjaan_avp');
  if ($pekerjaan_id) {
    if ($this->input->post('id_klasifikasi_pekerjaan_avp')) {
      $klasifikasi_baru = $this->input->post('id_klasifikasi_pekerjaan_avp');

      $sql_klasifikasi = $this->db->query("SELECT klasifikasi_pekerjaan_rkap,klasifikasi_pekerjaan_nama FROM global.global_klasifikasi_pekerjaan WHERE klasifikasi_pekerjaan_id = '" . $this->input->post('id_klasifikasi_pekerjaan_avp') . "'");
      $isi_klasifikasi = $sql_klasifikasi->row_array();

      if ($isi_klasifikasi['klasifikasi_pekerjaan_rkap'] == 'y') {
       $jenis_klasifikasi = 'RKAP';
     } else {
       $jenis_klasifikasi = 'Non RKAP';
     }

     $where = ($isi_klasifikasi['klasifikasi_pekerjaan_rkap'] == 'y') ? " AND klasifikasi_pekerjaan_rkap = 'y'" : " AND klasifikasi_pekerjaan_rkap = 'n'";

     $tahun = $this->db->query("SELECT pekerjaan_tahun as tahun FROM dec.dec_pekerjaan WHERE pekerjaan_id = '" . $pekerjaan_id . "'")->row_array();

     $sql_nomor = $this->db->query("SELECT SPLIT_PART(pekerjaan_nomor,'-',1) as pekerjaan_nomornya FROM dec.dec_pekerjaan a LEFT JOIN global.global_klasifikasi_pekerjaan b ON b.klasifikasi_pekerjaan_id = a.id_klasifikasi_pekerjaan WHERE SPLIT_PART(pekerjaan_nomor,'-',3) = '" . $tahun['tahun'] . "' AND pekerjaan_nomor IS NOT NULL " . $where . " ORDER BY CAST(SPLIT_PART(pekerjaan_nomor, '-', 1) as FLOAT) DESC");

     $isi_nomor = $sql_nomor->row_array();
     $nomor = $isi_nomor['pekerjaan_nomornya'];
     $nomor_baru = sprintf("%03d", $nomor + 1);

     $sql_pekerjaan = $this->db->query("SELECT pekerjaan_nomor FROM dec.dec_pekerjaan WHERE pekerjaan_id = '" . $pekerjaan_id . "'");
     $isi_pekerjaan = $sql_pekerjaan->row_array();

     /*buat nomor otomatis*/
     $data['pekerjaan_nomor'] = ($isi_pekerjaan['pekerjaan_nomor'] == null) ? $nomor_baru . '-' . $jenis_klasifikasi . '-' . $tahun['tahun']  : $isi_pekerjaan['pekerjaan_nomor'];
     $data['pekerjaan_status'] = anti_inject($pekerjaan_status_vp_avp);
     $data['id_klasifikasi_pekerjaan'] = anti_inject($this->input->post('id_klasifikasi_pekerjaan_avp'));
     // $data['pekerjaan_waktu_akhir'] = anti_inject($this->input->post('pekerjaan_waktu_akhir_avp'));
     $data['pekerjaan_judul'] = anti_inject($this->input->post('pekerjaan_judul'));
     $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
   }
 } else {
   $data['pekerjaan_status'] = $pekerjaan_status_vp_avp;
   $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
 }
 /* Pekerjaan */

 /*    cek apakah vp pj atau biasa (untuk penentuan biar staf ke depan setingnya lebih enak)*/
 $avp_pj = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $isi['pegawai_nik'] . "' AND pekerjaan_disposisi_status = '4' ")->row_array();

 $kategori_pekerjaan = $this->db->get_where('global.global_kategori_pekerjaan', ['kategori_pekerjaan_id' => $this->input->post('kategori_pekerjaan_avp')])->row_array();

 /* Disposisi */
 if ($this->input->post('id_user_avp')) {
   $data_disposisi['pekerjaan_disposisi_id'] = create_id();
   $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
   $data_disposisi['id_user'] = anti_inject($this->input->post('id_user_avp'));
   $data_disposisi['pekerjaan_disposisi_catatan'] = anti_inject($this->input->post('pekerjaan_disposisi_catatan'));
   $data_disposisi['id_pekerjaan'] = anti_inject($pekerjaan_id);
   $data_disposisi['pekerjaan_disposisi_status'] = anti_inject($pekerjaan_status);
   $data_disposisi['id_penanggung_jawab'] = anti_inject($avp_pj['id_penanggung_jawab']);
   $data_disposisi['pekerjaan_disposisi_prioritas'] = anti_inject($avp_pj['pekerjaan_disposisi_prioritas']);
   $data_disposisi['pekerjaan_disposisi_kategori'] = ($this->input->post('kategori_pekerjaan_avp'));
   $data_disposisi['pekerjaan_disposisi_durasi'] = $kategori_pekerjaan['kategori_pekerjaan_estimasi'];

   $lastfinish = $this->db->query("SELECT max(pekerjaan_disposisi_waktu_finish) as akhir FROM dec.dec_pekerjaan_disposisi a WHERE true AND id_user = '" . $this->input->post('id_user_avp') . "' AND pekerjaan_disposisi_status = '5' ")->row_array();

   if ($lastfinish['akhir'] != '') :
     $start = date('Y-m-d H:i:s', strtotime($lastfinish['akhir'] . '+' . (1) . ' days'));
   else :
     $start = date('Y-m-d H:i:s', strtotime($data_disposisi['pekerjaan_disposisi_waktu']));
   endif;

   if ($lastfinish['akhir'] != '') :
     $finish = date('Y-m-d H:i:s', strtotime($lastfinish['akhir'] . '+' . ($kategori_pekerjaan['kategori_pekerjaan_estimasi']) . ' days'));
   else :
     $finish = date('Y-m-d H:i:s', strtotime($data_disposisi['pekerjaan_disposisi_waktu'] . '+' . ($kategori_pekerjaan['kategori_pekerjaan_estimasi'] - 1) . ' days'));
   endif;

   $data_disposisi['pekerjaan_disposisi_waktu_start'] = $start;
   $data_disposisi['pekerjaan_disposisi_waktu_finish'] = $finish;



   $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

   /*cek apakah dia dispo terakhirnya */
   $param_cari_user['pegawai_nik'] = $data_disposisi['id_user'];
   $cari_user = $this->M_user->getUser($param_cari_user);
   /*Notifikas*/
   $tujuan = $cari_user['pegawai_nik'];
   $tujuan_nama = $cari_user['pegawai_nama'];
   $text = "Mohon untuk melakukan PROSES pada pekerjaan ini";
   dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Disposisikan Ke ' . $cari_user['pegawai_nama'] . ' Sebagai Perencana', $isi['pegawai_nik']);
   tasklog($pekerjaan_id, $data_disposisi['pekerjaan_disposisi_status'], $cari_user['pegawai_nik'], $text, 'n');
   sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
   sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $text);
   /*Notifikas*/
 }
 if ($this->input->post('id_user_avp_instrumen')) {
   $data_disposisi['pekerjaan_disposisi_id'] = create_id();
   $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
   $data_disposisi['id_user'] = anti_inject($this->input->post('id_user_avp_instrumen'));
   $data_disposisi['pekerjaan_disposisi_catatan'] = anti_inject($this->input->post('pekerjaan_disposisi_catatan'));
   $data_disposisi['id_pekerjaan'] = anti_inject($pekerjaan_id);
   $data_disposisi['pekerjaan_disposisi_status'] = anti_inject($pekerjaan_status);
   $data_disposisi['is_listin'] = 'I';
   $data_disposisi['id_penanggung_jawab'] = anti_inject($avp_pj['id_penanggung_jawab']);
   $data_disposisi['pekerjaan_disposisi_prioritas'] = anti_inject($avp_pj['pekerjaan_disposisi_prioritas']);
   $data_disposisi['pekerjaan_disposisi_kategori'] = ($this->input->post('kategori_pekerjaan_avp'));
   $data_disposisi['pekerjaan_disposisi_durasi'] = $kategori_pekerjaan['kategori_pekerjaan_estimasi'];

   $lastfinish = $this->db->query("SELECT max(pekerjaan_disposisi_waktu_finish) as akhir FROM dec.dec_pekerjaan_disposisi a WHERE true AND id_user = '" . $this->input->post('id_user_avp_instrumen') . "' AND pekerjaan_disposisi_status = '5' ")->row_array();

   if ($lastfinish['akhir'] != '') :
     $start = date('Y-m-d H:i:s', strtotime($lastfinish['akhir'] . '+' . (1) . ' days'));
   else :
     $start = date('Y-m-d H:i:s', strtotime($data_disposisi['pekerjaan_disposisi_waktu']));
   endif;

   if ($lastfinish['akhir'] != '') :
     $finish = date('Y-m-d H:i:s', strtotime($lastfinish['akhir'] . '+' . ($kategori_pekerjaan['kategori_pekerjaan_estimasi']) . ' days'));
   else :
     $finish = date('Y-m-d H:i:s', strtotime($data_disposisi['pekerjaan_disposisi_waktu'] . '+' . ($kategori_pekerjaan['kategori_pekerjaan_estimasi'] - 1) . ' days'));
   endif;

   $data_disposisi['pekerjaan_disposisi_waktu_start'] = $start;
   $data_disposisi['pekerjaan_disposisi_waktu_finish'] = $finish;

   $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

   $param_cari_user['pegawai_nik'] = $data_disposisi['id_user'];
   $cari_user = $this->M_user->getUser($param_cari_user);
   /*Notifikasi*/
   $tujuan = $cari_user['pegawai_nik'];
   $tujuan_nama = $cari_user['pegawai_nama'];
   $text = "Mohon untuk melakukan PROSES pada pekerjaan ini";
   dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Disposisikan Ke ' . $cari_user['pegawai_nama'] . ' Sebagai Perencana', $isi['pegawai_nik']);
   tasklog($pekerjaan_id, $data_disposisi['pekerjaan_disposisi_status'], $cari_user['pegawai_nik'], $text, 'n');
   sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
   sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $text);
   /*Notifikasi*/
 }
 if ($this->input->post('id_user_avp_listrik')) {
   $data_disposisi['pekerjaan_disposisi_id'] = create_id();
   $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
   $data_disposisi['id_user'] = anti_inject($this->input->post('id_user_avp_listrik'));
   $data_disposisi['pekerjaan_disposisi_catatan'] = anti_inject($this->input->post('pekerjaan_disposisi_catatan'));
   $data_disposisi['id_pekerjaan'] = anti_inject($pekerjaan_id);
   $data_disposisi['pekerjaan_disposisi_status'] = anti_inject($pekerjaan_status);
   $data_disposisi['is_listin'] = 'L';
   $data_disposisi['id_penanggung_jawab'] = anti_inject($avp_pj['id_penanggung_jawab']);
   $data_disposisi['pekerjaan_disposisi_prioritas'] = anti_inject($avp_pj['pekerjaan_disposisi_prioritas']);
   $data_disposisi['pekerjaan_disposisi_kategori'] = ($this->input->post('kategori_pekerjaan_avp'));
   $data_disposisi['pekerjaan_disposisi_durasi'] = $kategori_pekerjaan['kategori_pekerjaan_estimasi'];

   $lastfinish = $this->db->query("SELECT max(pekerjaan_disposisi_waktu_finish) as akhir FROM dec.dec_pekerjaan_disposisi a WHERE true AND id_user = '" . $this->input->post('id_user_avp_listrik') . "' AND pekerjaan_disposisi_status = '5' ")->row_array();

   if ($lastfinish['akhir'] != '') :
     $start = date('Y-m-d H:i:s', strtotime($lastfinish['akhir'] . '+' . (1) . ' days'));
   else :
     $start = date('Y-m-d H:i:s', strtotime($data_disposisi['pekerjaan_disposisi_waktu']));
   endif;

   if ($lastfinish['akhir'] != '') :
     $finish = date('Y-m-d H:i:s', strtotime($lastfinish['akhir'] . '+' . ($kategori_pekerjaan['kategori_pekerjaan_estimasi']) . ' days'));
   else :
     $finish = date('Y-m-d H:i:s', strtotime($data_disposisi['pekerjaan_disposisi_waktu'] . '+' . ($kategori_pekerjaan['kategori_pekerjaan_estimasi'] - 1) . ' days'));
   endif;

   $data_disposisi['pekerjaan_disposisi_waktu_start'] = $start;
   $data_disposisi['pekerjaan_disposisi_waktu_finish'] = $finish;

   $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

   $param_cari_user['pegawai_nik'] = $data_disposisi['id_user'];
   $cari_user = $this->M_user->getUser($param_cari_user);
   /*NOtifikasi*/
   $tujuan = $cari_user['pegawai_nik'];
   $tujuan_nama = $cari_user['pegawai_nama'];
   $text = "Mohon untuk melakukan PROSES pada pekerjaan ini";
   dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Disposisikan Ke ' . $cari_user['pegawai_nama'] . ' Sebagai Perencana', $isi['pegawai_nik']);
   tasklog($pekerjaan_id, $data_disposisi['pekerjaan_disposisi_status'], $cari_user['pegawai_nik'], $text, 'n');
   sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
   sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $text);
   /*NOtifikasi*/
 }
 /* Disposisi */
 $user_avp_admin = (isset($_GET['user_id'])) ? $_GET['user_id'] : null;

 $where_id_user = ($isi['pegawai_nik'] == $this->admin_sistemnya) ? $user_avp_admin : $isi['pegawai_nik'];
 $where_id_pekerjaan = (($pekerjaan_id));
 $where_disposisi_status = ($pekerjaan_status_vp_avp);
 $param_staf['is_proses'] = 'y';
 $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);

 /*update pekerjaan*/
 $first_dispo = $this->db->query("SELECT min(pekerjaan_disposisi_waktu_start) as dispo_mulai FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '5'")->row_array();

 $last_dispo = $this->db->query("SELECT max(pekerjaan_disposisi_waktu_finish) as dispo_selesai FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '5'")->row_array();

 $this->db->query("UPDATE dec.dec_pekerjaan SET pekerjaan_estimasi_mulai = '" . $first_dispo['dispo_mulai'] . "', pekerjaan_estimasi_selesai = '" . $last_dispo['dispo_selesai'] . "' WHERE pekerjaan_id = '" . $pekerjaan_id . "'");

 /*    CEK IS PROSES != 0*/
 $sql_proses = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '" . $pekerjaan_status_vp_avp . "' AND (is_proses != 'y' OR is_proses is null)");
 $data_proses = $sql_proses->row_array();

 /*    JIKA SEMUA SUDAH DIPROSES*/
 if ($data_proses['total'] == '0') {
   $data['pekerjaan_status'] = $pekerjaan_status;
   $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
 }
}
/* Disposisi AVP */

/* Progress Pekerjaan */
public function getProgressPekerjaan()
{

	if ($this->input->get('id_user')) {
    $user = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $this->input->get('id_user')])->row_array();
  } else {
    $user = $this->session->userdata();
  }

  $param['id_pekerjaan'] =  $this->input->get_post('pekerjaan_id');
  $param['id_user'] = $user['pegawai_nik'];

  $data = $this->M_pekerjaan->getProgressPekerjaan($param);
  echo json_encode($data);
}

public function insertProgressPekerjaan()
{
	if (isset($_GET['id_user'])) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $user = $sql_user->row_array();
  } else {
    $user = $this->session->userdata();
  }

  $param['progress_id'] = create_id();
  $param['id_pekerjaan'] = anti_inject($this->input->get_post('id_pekerjaan_progress'));
  $param['id_user'] = anti_inject($user['pegawai_nik']);
  $param['progress_jumlah'] = anti_inject($this->input->get_post('pekerjaan_progress'));

  $sql_bagian = $this->db->query("SELECT id_bagian FROM global.global_bagian_detail WHERE id_pegawai = '" . $user['pegawai_nik'] . "'");
  $data_bagian = $sql_bagian->row_array();
  $param['id_bagian'] = anti_inject($data_bagian['id_bagian']);

  $this->M_pekerjaan->insertProgress($param);
  dblog('I',  $param['id_pekerjaan'], 'Petugas Telah Upload Progress ' . $param['progress_jumlah'], $user['pegawai_nik']);
}


public function updateProgressPekerjaan()
{
	if ($this->input->get('id_user')) {
    $user = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $this->input->get('id_user')])->row_array();
  } else {
    $user = $this->session->userdata();
  }

  $id = anti_inject($this->input->get_post('progress_id'));
  $param['id_pekerjaan'] = anti_inject($this->input->get_post('id_pekerjaan_progress'));
  $param['id_user'] = anti_inject($user['pegawai_nik']);
  $param['progress_jumlah'] = anti_inject($this->input->get_post('pekerjaan_progress'));

  $sql_bagian = $this->db->query("SELECT id_bagian FROM global.global_bagian_detail WHERE id_pegawai = '" . $user['pegawai_nik'] . "'");
  $data_bagian = $sql_bagian->row_array();
  $param['id_bagian'] = anti_inject($data_bagian['id_bagian']);

  $this->M_pekerjaan->updateProgress($id, $param);

  dblog('I', $param['id_pekerjaan'], 'Petugas Telah Mengedit Progress ' . $param['progress_jumlah'], $user['pegawai_nik']);
}
/* Progress Pekerjaan */

/* Approve Pekerjaan Berjalan */
public function prosesApproveBerjalan()
{
	if ($this->input->get('id_user')) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'");
    $isi = $sql_user->row_array();
  } else {
    $isi = $this->session->userdata();
  }
  $pekerjaan_id = anti_inject($this->input->get_post('pekerjaan_id'));
  $id_tanggung_jawab = null;
  $pekerjaan_status = anti_inject('8');

  $is_cc = 'y';
  /* isi disposisi */
  if ($this->input->get_post('id_user_staf')) {
    $user = $this->input->get_post('id_user_staf');
    $user_implode = implode("','", $user);
    $cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'y' ")->result_array();

     foreach ($cc_not_in as $value_not_in) {
       $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
       dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC Non HPS', $isi['pegawai_nik']);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'y'");
     }
     foreach ($user as $key => $value) {
       $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='y'")->row_array();
       if ($ada_cc['total'] == 0) {

        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = anti_inject($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
        $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi_doc['is_cc'] = anti_inject('y');
        $data_disposisi_doc['is_aktif'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
        $tujuan = $data_cc['pegawai_nik'];
        $tujuan_nama = $data_cc['pegawai_nama'];
        $kalimat = "Pekerjaan telah di CC kepada anda";
        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC Non HPS', $isi['pegawai_nik']);
        tasklog($pekerjaan_id, $data_disposisi_doc['pekerjaan_disposisi_status'], $data_cc['pegawai_nik'], $kalimat, 'n');
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
        sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
      }
    }
  }

  if ($this->input->get_post('id_user_staf_hps')) {
    $user  = $this->input->get_post('id_user_staf_hps');
    $user_implode = implode("','", $user);
    $cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'y' ")->result_array();

     foreach ($cc_not_in as $value_not_in) {
       $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
       dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC Non HPS', $isi['pegawai_nik']);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'y'");
     }
     foreach ($user as $key => $value) {
       $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='y'")->row_array();
       if ($ada_cc['total'] == 0) {

        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = anti_inject($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
        $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi_doc['is_cc'] = anti_inject('y');
        $data_disposisi_doc['is_aktif'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
        $tujuan = $data_cc['pegawai_nik'];
        $tujuan_nama = $data_cc['pegawai_nama'];
        $kalimat = "Pekerjaan telah di CC kepada anda";
        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC Non HPS', $isi['pegawai_nik']);
        tasklog($pekerjaan_id, $data_disposisi_doc['pekerjaan_disposisi_status'], $data_cc['pegawai_nik'], $kalimat, 'n');
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
        sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
      }
    }
  }
  /*CC*/
  /* isi disposisi */

  /* Ubah Status Dari Staf */
  $where_id_user = anti_inject($isi['pegawai_nik']);
  $where_id_pekerjaan = anti_inject($this->input->get_post('pekerjaan_id'));
  $where_disposisi_status = anti_inject($this->input->get_post('pekerjaan_status'));
  $param_staf['is_proses'] = 'y';
  $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);
  /* Ubah Status Dari Staf */

  /* Pekerjaan */
  $pekerjaan_status = $this->input->get('pekerjaan_status') + 1;
  $pekerjaan_id = $this->input->get('pekerjaan_id');
  /* cek apakah sudah diproses staf semua */
  $where_disposisi['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
  $where_disposisi['pekerjaan_disposisi_status'] = $this->input->get_post('pekerjaan_status');

  $sql_jumlah_proses = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $where_disposisi['id_pekerjaan'] . "' AND pekerjaan_disposisi_status = '" . $where_disposisi['pekerjaan_disposisi_status'] . "' AND is_proses is null");
  $data_jumlah_proses = $sql_jumlah_proses->row_array();

  if ($pekerjaan_id) {
    if ($data_jumlah_proses['total'] == '0') {
      $data['pekerjaan_status'] = anti_inject($pekerjaan_status);
    }
  }
  /* Pekerjaan */

  /* select pekerjaan disposisi dengan pekerjaan dan status sama */
  $sql_disposisi_sama = $this->db->query("SELECT id_user,id_pekerjaan FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $where_disposisi['id_pekerjaan'] . "' AND pekerjaan_disposisi_status = '" . $where_disposisi['pekerjaan_disposisi_status'] . "'");
  $data_disposisi_sama = $sql_disposisi_sama->result_array();
  /* select pekerjaan disposisi dengan pekerjaan dan status sama */

  /*cek jika status koordinator*/
  $sql_koordinator = $this->db->query("SELECT id_user FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = " . $this->db->escape($pekerjaan_id) . " AND id_penanggung_jawab = " . $this->db->escape('y') . "AND pekerjaan_disposisi_status = " . $this->db->escape('4'));
  $data_koordinator = $sql_koordinator->row_array();
  /*cek jika status koordinator*/

  $data_user['pegawai_poscode'] = $isi['pegawai_direct_superior'];
  $sql_user_bagian = $this->db->query("SELECT * FROM global.global_bagian_detail WHERE id_pegawai = '" . $isi['pegawai_nik'] . "'");
  $data_user_bagian = $sql_user_bagian->row_array();

  $pekerjaan_disposisi_status = '4';

  $sql_avp_bagian = $this->db->query("SELECT * FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $data_user_bagian['id_bagian'] . "' AND pekerjaan_disposisi_status = '" . $pekerjaan_disposisi_status . "' AND id_pekerjaan = '" . $pekerjaan_id . "'");
  $data_avp_bagian = $sql_avp_bagian->row_array();

  $user = $data_avp_bagian;
  $sql_cek_vp  = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user='" . $data_avp_bagian['id_pegawai'] . "' AND pekerjaan_disposisi_status = '" . ($pekerjaan_disposisi_status + 2) . "' AND id_pekerjaan = '" . $pekerjaan_id . "'");
  $data_cek_vp = $sql_cek_vp->row_array();

  /*cek apakah semua staf sudah proses*/
  $data_proses = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '5' AND id_pekerjaan = '" . $pekerjaan_id . "' AND is_proses IS NULL AND id_user IN(SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian = '" . $data_avp_bagian['id_bagian'] . "') ")->row_array();

  /* Get Pekerjaan */
  $sql_pekerjaan = $this->db->query("SELECT pic FROM dec.dec_pekerjaan WHERE pekerjaan_id = " . $this->db->escape($pekerjaan_id));
  $isi_pekerjaan = $sql_pekerjaan->row_array();
  /* Get Pekerjaan */

  if ($pekerjaan_status == '8') {
   $id_user_disposisi = $isi_pekerjaan['pic'];
 } else if ($isi['pegawai_poscode'] == 'E53600031A' || $isi['pegawai_poscode'] == 'E53500031B') {
   $id_user_disposisi = $isi['pegawai_nik'];
 } else {
   $id_user_disposisi = $user['id_pegawai'];
 }

 /* Disposisi */
 if (empty($data_cek_vp)) {
   $data_perencana = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi a WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $isi['pegawai_nik'] . "' AND pekerjaan_disposisi_status = '5'")->row_array();

   $data_disposisi['pekerjaan_disposisi_id'] = create_id();
   $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
   $data_disposisi['id_user'] = $id_user_disposisi;
   $data_disposisi['id_pekerjaan'] = anti_inject($pekerjaan_id);
   $data_disposisi['pekerjaan_disposisi_status'] = anti_inject($pekerjaan_status);
   $data_disposisi['id_penanggung_jawab'] = ($data_koordinator['id_user'] == $user['id_pegawai']) ? 'y' : 'n';
   $data_disposisi['is_proses'] = null;
   $data_disposisi['pekerjaan_disposisi_prioritas'] = $data_perencana['pekerjaan_disposisi_prioritas'];
   $data_disposisi['pekerjaan_disposisi_kategori'] = $data_perencana['pekerjaan_disposisi_kategori'];
   $data_disposisi['pekerjaan_disposisi_durasi'] = $data_perencana['pekerjaan_disposisi_durasi'];
   $data_disposisi['pekerjaan_disposisi_waktu_finish'] = $data_perencana['pekerjaan_disposisi_waktu_finish'];
   $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
 }

 /*update status avp (6) agar null*/
 $where_id_user = anti_inject($id_user_disposisi);
 $where_id_pekerjaan = anti_inject($pekerjaan_id);
 $where_disposisi_status = anti_inject($pekerjaan_status);
 $param_staf['is_proses'] = null;
 $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);


 /*ubah status dokumen ke SEND*/
 $where_id_pekerjaan_dokumen = anti_inject($this->input->get_post('pekerjaan_id'));
 $where_id_user_dokumen = $isi['pegawai_nik'];
 $where_dokumen_status = '0';
 $param_user_dokumen['pekerjaan_dokumen_status'] = '2';

 /* dokumen */
 $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_create_awal = '" . $isi['pegawai_nik'] . "' AND pekerjaan_dokumen_status < '2' AND is_lama !='y' AND pekerjaan_dokumen_awal !='y' AND is_hps = 'n' ")->result_array();

 foreach ($data_dokumen as $val_dokumen) {

   $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();

   $data['pekerjaan_dokumen_id_temp'] = $val_dokumen['pekerjaan_dokumen_id'];
   $data['pekerjaan_dokumen_id'] = anti_inject(create_id());
   $data['pekerjaan_dokumen_status'] = '2';
   $data['pekerjaan_dokumen_revisi'] = $nomor_revisi['nomor_revisi'];
   $data['pekerjaan_dokumen_keterangan'] = anti_inject($this->input->post('pekerjaan_dokumen_keterangan'));
   $data['id_create'] = $isi['pegawai_nik'];
   $data['is_proses'] = 'a';
   $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
   $this->M_pekerjaan->simpanAksiSama($data);
   $data_dokumen_isi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
   dblog('I', $this->input->get_post('pekerjaan_id'), 'Dokumen ' . $data_dokumen_isi['pekerjaan_template_nama'] . ' - ' . $data_dokumen_isi['pekerjaan_dokumen_nama'] . ' Telah  DiSend ', $isi['pegawai_nik']);
 }

 $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET is_lama = 'y' WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_create_awal ='" . $isi['pegawai_nik'] . "' AND pekerjaan_dokumen_status < '2' AND (is_lama !='y' OR is_lama is null) AND pekerjaan_dokumen_awal != 'y' AND is_hps = 'n'");
 /* dokumen */

 $data_dokumen_hps = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_create_awal = '" . $isi['pegawai_nik'] . "' AND pekerjaan_dokumen_status < '2' AND is_lama !='y' AND pekerjaan_dokumen_awal !='y' AND is_hps = 'y' ")->result_array();

 foreach ($data_dokumen_hps as $val_dokumen) {

   $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();

   $data['pekerjaan_dokumen_id_temp'] = $val_dokumen['pekerjaan_dokumen_id'];
   $data['pekerjaan_dokumen_id'] = anti_inject(create_id());
   $data['pekerjaan_dokumen_status'] = '2';
   $data['pekerjaan_dokumen_revisi'] = $nomor_revisi['nomor_revisi'];
   $data['pekerjaan_dokumen_keterangan'] = anti_inject($this->input->post('pekerjaan_dokumen_keterangan'));
   $data['id_create'] = $isi['pegawai_nik'];
   $data['is_proses'] = 'a';
   $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
   $this->M_pekerjaan->simpanAksiSama($data);
   $data_dokumen_hps_isi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
   dblog('I', $this->input->get_post('pekerjaan_id'), 'Dokumen ' . $data_dokumen_hps_isi['pekerjaan_template_nama'] . ' - ' . $data_dokumen_hps_isi['pekerjaan_dokumen_nama'] . ' Telah  DiSend ', $isi['pegawai_nik']);
 }

 $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET is_lama = 'y' WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_create_awal ='" . $isi['pegawai_nik'] . "' AND pekerjaan_dokumen_status < '2' AND (is_lama !='y' OR is_lama is null) AND pekerjaan_dokumen_awal != 'y' AND is_hps = 'y'");
 /* dokumen */

					// die();

 $sql_progress = $this->db->query("SELECT * FROM dec.dec_pekerjaan_progress WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user = '" . $isi['pegawai_nik'] . "'");
 $data_progress = $sql_progress->row_array();

 if (empty($data_progress)) {
   $param_user_progress['progress_id'] = create_id();
   $param_user_progress['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
   $param_user_progress['id_user'] = $isi['pegawai_nik'];
   $param_user_progress['progress_jumlah'] = '92';
   $param_user_progress['id_bagian'] = $data_user_bagian['id_bagian'];
   $this->M_pekerjaan->insertProgressIFA($param_user_progress);
 } else {
   $where_id_pekerjaan_progress = $this->input->get_post('pekerjaan_id');
   $where_id_user_progress = $isi['pegawai_nik'];
   $param_user_progress['progress_jumlah'] = '92';
   $this->M_pekerjaan->updateProgressIFA($where_id_pekerjaan_progress, $where_id_user_progress, $param_user_progress);
 }

 $data_users['pegawai_nik'] = $id_user_disposisi;
 $users = $this->M_user->getUser($data_users);
 /*Notifikasi*/
 $tujuan = $users['pegawai_nik'];
 $tujuan_nama = $users['pegawai_nama'];
 $text = "Mohon untuk melakukan REVIEW pada pekerjaan ini";
 dblog('I',  $pekerjaan_id, 'Pekerjaan telah di Send IFA', $isi['pegawai_nik']);
 tasklog($pekerjaan_id, $data_disposisi['pekerjaan_disposisi_status'], $users['pegawai_nik'], $text, 'n');
 sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
 sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $text);
 /*Notifikasi*/
}

/* Approve Pekerjaan Berjalan */
public function prosesApproveBerjalanIFARev()
{

	if (isset($_GET['id_user'])) {
    $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $isi = $sql_isi->row_array();
  } else {
    $isi = $this->session->userdata();
  }

  $pekerjaan_id = anti_inject($this->input->get_post('pekerjaan_id'));
  $id_tanggung_jawab = null;
  $pekerjaan_status = anti_inject('8');
  $is_cc = 'y';
  /* isi disposisi */
  if ($this->input->get_post('id_user_staf')) {
    $this->M_pekerjaan->deletePekerjaanDisposisi($pekerjaan_id, null, $id_tanggung_jawab, $pekerjaan_status, $is_cc);
    $user = $this->input->get_post('id_user_staf');
    foreach ($user as $key => $value) {
      $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
      $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
      $data_disposisi_doc['id_user'] = anti_inject($value);
      $data_disposisi_doc['id_pekerjaan'] = anti_inject($this->input->get_post('pekerjaan_id'));
      $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
      $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
      $data_disposisi_doc['is_cc'] = anti_inject('y');

      $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);

      $data_user['pegawai_nik'] = $data_disposisi_doc['id_user'];
      $user = $this->M_user->getUser($data_user);

      $data_pekerjaan['pekerjaan_id'] = $data_disposisi_doc['id_pekerjaan'];
      $pekerjaan = $this->M_pekerjaan->getPekerjaan($data_pekerjaan);

      $email_penerima = $user['email_pegawai'];
      $subjek = $pekerjaan['pekerjaan_judul'];
      $pesan = $pekerjaan['pekerjaan_deskripsi'];
      $sendmail = array(
       'email_penerima' => $email_penerima,
       'subjek' => $subjek,
       'content' => $pesan,
     );

      /*INSERT KE DB EMAIL*/
      $param_email['email_id'] = create_id();
      $param_email['id_penerima'] = $user['pegawai_nik'];
      $param_email['id_pengirim'] = $isi['pegawai_nik'];
      $param_email['id_pekerjaan'] = $data_disposisi_doc['id_pekerjaan'];
      $param_email['id_pekerjaan_disposisi'] = $data_disposisi_doc['pekerjaan_disposisi_id'];
      $param_email['email_subject'] = $subjek;
      $param_email['email_content'] = $pesan;
      $param_email['when_created'] = date('Y-m-d H:i:s');
      $param_email['who_created'] = $isi['pegawai_nama'];

      $this->M_pekerjaan->insertEmail($param_email);
    }
  }
  /*CC*/
  /* isi disposisi */

  /* Ubah Status Dari Staf */

  $where_id_user = anti_inject($isi['pegawai_nik']);
  $where_id_pekerjaan = anti_inject($this->input->get_post('pekerjaan_id'));
  $where_disposisi_status = anti_inject($this->input->get_post('pekerjaan_status') - 3);
  $param_staf['is_proses'] = 'y';
  $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);

  /* Ubah Status Dari Staf */

  /* Pekerjaan */
  $pekerjaan_status = $this->input->get('pekerjaan_status') + 1;
  $pekerjaan_id = $this->input->get('pekerjaan_id');
  /* cek apakah sudah diproses staf semua */
  $where_disposisi['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
  $where_disposisi['pekerjaan_disposisi_status'] = $this->input->get_post('pekerjaan_status');

  $sql_jumlah_proses = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $where_disposisi['id_pekerjaan'] . "' AND pekerjaan_disposisi_status = '" . $where_disposisi['pekerjaan_disposisi_status'] . "' AND is_proses is null");

  $data_jumlah_proses = $sql_jumlah_proses->row_array();



  if ($pekerjaan_id) {
   if ($data_jumlah_proses['total'] == '0') {

     $data['pekerjaan_status'] = anti_inject($pekerjaan_status);

     /*$this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);*/


     /*dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reviewed Oleh Cangun');*/
   }
 }
 /* Pekerjaan */

 /* select pekerjaan disposisi dengan pekerjaan dan status sama */
 $sql_disposisi_sama = $this->db->query("SELECT id_user,id_pekerjaan FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $where_disposisi['id_pekerjaan'] . "' AND pekerjaan_disposisi_status = '" . $where_disposisi['pekerjaan_disposisi_status'] . "'");

 $data_disposisi_sama = $sql_disposisi_sama->result_array();
 /* select pekerjaan disposisi dengan pekerjaan dan status sama */

 /*cek jika status koordinator*/
 $sql_koordinator = $this->db->query("SELECT id_user FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = " . $this->db->escape($pekerjaan_id) . " AND id_penanggung_jawab = " . $this->db->escape('y') . "AND pekerjaan_disposisi_status = " . $this->db->escape('4'));

 $data_koordinator = $sql_koordinator->row_array();
 /*cek jika status koordinator*/

 $data_user['pegawai_poscode'] = $isi['pegawai_direct_superior'];

 $sql_user_bagian = $this->db->query("SELECT * FROM global.global_bagian_detail WHERE id_pegawai = '" . $isi['pegawai_nik'] . "'");
 $data_user_bagian = $sql_user_bagian->row_array();

 $pekerjaan_disposisi_status = '4';

 $sql_avp_bagian = $this->db->query("SELECT * FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $data_user_bagian['id_bagian'] . "' AND pekerjaan_disposisi_status = '" . $pekerjaan_disposisi_status . "' AND id_pekerjaan = '" . $pekerjaan_id . "'");

 $data_avp_bagian = $sql_avp_bagian->row_array();

 $user = $data_avp_bagian;

 $sql_cek_vp  = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user='" . $data_avp_bagian['id_pegawai'] . "' AND pekerjaan_disposisi_status = '" . ($pekerjaan_disposisi_status + 2) . "' AND id_pekerjaan = '" . $pekerjaan_id . "'");

 $data_cek_vp = $sql_cek_vp->row_array();



 /* Get Pekerjaan */
 $sql_pekerjaan = $this->db->query("SELECT pic FROM dec.dec_pekerjaan WHERE pekerjaan_id = " . $this->db->escape($pekerjaan_id));
 $isi_pekerjaan = $sql_pekerjaan->row_array();
 /* Get Pekerjaan */

 if ($pekerjaan_status == '8') {
   $id_user_disposisi = $isi_pekerjaan['pic'];
 } else if ($isi['pegawai_poscode'] == 'E53600031A' || $isi['pegawai_poscode'] == 'E53500031B') {
   $id_user_disposisi = $isi['pegawai_nik'];
 } else {
   $id_user_disposisi = $user['id_pegawai'];
 }

 /* Disposisi */
 if (empty($data_cek_vp)) {
   $data_disposisi['pekerjaan_disposisi_id'] = create_id();
   $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
   $data_disposisi['id_user'] = $id_user_disposisi;
   $data_disposisi['id_pekerjaan'] = anti_inject($pekerjaan_id);
   $data_disposisi['pekerjaan_disposisi_status'] = anti_inject($pekerjaan_status);
   $data_disposisi['id_penanggung_jawab'] = ($data_koordinator['id_user'] == $user['id_pegawai']) ? 'y' : 'n';
   $data_disposisi['is_proses'] = null;

   $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

   /*update agar set null proses avpnya*/

   $data_user['pegawai_nik'] = $data_disposisi['id_user'];
   $user_email = $this->M_user->getUser($data_user);

   $data_pekerjaan['pekerjaan_id'] = $pekerjaan_id;
   $pekerjaan = $this->M_pekerjaan->getPekerjaan($data_pekerjaan);

   $email_penerima = $user_email['email_pegawai'];
   $subjek = $pekerjaan['pekerjaan_judul'];
   $pesan = $pekerjaan['pekerjaan_deskripsi'];
   $sendmail = array(
     'email_penerima' => $email_penerima,
     'subjek' => $subjek,
     'content' => $pesan,
   );
   /*INSERT KE DB EMAIL*/
   $param_email['email_id'] = create_id();
   $param_email['id_penerima'] = $user_email['pegawai_nik'];
   $param_email['id_pengirim'] = $isi['pegawai_nik'];
   $param_email['id_pekerjaan'] = $pekerjaan_id;
   $param_email['id_pekerjaan_disposisi'] = $data_disposisi['pekerjaan_disposisi_id'];
   $param_email['email_subject'] = $subjek;
   $param_email['email_content'] = $pesan;
   $param_email['when_created'] = date('Y-m-d H:i:s');
   $param_email['who_created'] = $isi['pegawai_nama'];

   $this->M_pekerjaan->insertEmail($param_email);
 }

 /*update status avp (6) agar null*/
 $where_id_user = anti_inject($id_user_disposisi);
 $where_id_pekerjaan = anti_inject($pekerjaan_id);
 $where_disposisi_status = anti_inject($pekerjaan_status);
 $param_staf['is_proses'] = null;
 $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);

 /*update status avp (6) agar null*/

 /* Disposisi */
 /*}*/

 /*ubah status dokumen ke SEND*/
 $where_id_pekerjaan_dokumen = anti_inject($this->input->get_post('pekerjaan_id'));
 $where_id_user_dokumen = $isi['pegawai_nik'];
 $where_dokumen_status = '0';
 $param_user_dokumen['pekerjaan_dokumen_status'] = '2';

 $this->M_pekerjaan->updateStatusDokumen($where_id_pekerjaan_dokumen, $where_id_user_dokumen, $where_dokumen_status, $param_user_dokumen);

 $sql_progress = $this->db->query("SELECT * FROM dec.dec_pekerjaan_progress WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user = '" . $isi['pegawai_nik'] . "'");
 $data_progress = $sql_progress->row_array();

 if (empty($data_progress)) {
   $param_user_progress['progress_id'] = create_id();
   $param_user_progress['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
   $param_user_progress['id_user'] = $isi['pegawai_nik'];
   $param_user_progress['progress_jumlah'] = '92';
   $param_user_progress['id_bagian'] = $data_user_bagian['id_bagian'];

   $this->M_pekerjaan->insertProgressIFA($param_user_progress);
 } else {

   $where_id_pekerjaan_progress = $this->input->get_post('pekerjaan_id');
   $where_id_user_progress = $isi['pegawai_nik'];
   $param_user_progress['progress_jumlah'] = '92';

   $this->M_pekerjaan->updateProgressIFA($where_id_pekerjaan_progress, $where_id_user_progress, $param_user_progress);
 }

 dblog('I',  $pekerjaan_id, 'Pekerjaan telah di Send IFA', $isi['pegawai_nik']);
}

public function prosesApproveBerjalanHPS()
{
	if (isset($_GET['id_user'])) {
    $isi = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $_GET['id_user']))->row_array();
  } else {
    $isi = $this->session->userdata();
  }
  $pekerjaan_id = anti_inject($this->input->get_post('id_pekerjaan'));
  $id_tanggung_jawab = null;
  $pekerjaan_status = anti_inject('8');
  $is_cc = 'y';
  /*CC*/
  /* isi disposisi */
  /*CC*/
  if ($this->input->get_post('id_user_staf')) {
    $user = $this->input->get_post('id_user_staf');
    $user_implode = implode("','", $user);
    $cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'y' ")->result_array();

     foreach ($cc_not_in as $value_not_in) {
       $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
       dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC Non HPS', $isi['pegawai_nik']);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'y'");
     }
     foreach ($user as $key => $value) {

       $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='y'")->row_array();
       if ($ada_cc['total'] == 0) {

        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = anti_inject($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
        $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi_doc['is_cc'] = anti_inject('y');
        $data_disposisi_doc['is_aktif'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
        $tujuan = $data_cc['pegawai_nik'];
        $tujuan_nama = $data_cc['pegawai_nama'];
        $kalimat = "Pekerjaan telah di CC kepada anda";
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
        sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);

        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC Non HPS', $isi['pegawai_nik']);
      }
    }
  }
  /*CC*/
  /* isi disposisi */

  /* Ubah Status Dari Staf */

  $where_id_user = anti_inject($isi['pegawai_nik']);
  $where_id_pekerjaan = anti_inject($this->input->get_post('pekerjaan_id'));
  $where_disposisi_status = anti_inject($this->input->get_post('pekerjaan_status'));
  $param_staf['is_proses'] = 'y';
  $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);

  /* Ubah Status Dari Staf */

  /* Pekerjaan */
  $pekerjaan_status = $this->input->get('pekerjaan_status') + 1;
  $pekerjaan_id = $this->input->get('pekerjaan_id');
  /* cek apakah sudah diproses staf semua */
  $where_disposisi['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
  $where_disposisi['pekerjaan_disposisi_status'] = $this->input->get_post('pekerjaan_status');

  $sql_jumlah_proses = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $where_disposisi['id_pekerjaan'] . "' AND pekerjaan_disposisi_status = '" . $where_disposisi['pekerjaan_disposisi_status'] . "' AND is_proses is null");

  $data_jumlah_proses = $sql_jumlah_proses->row_array();
  if ($pekerjaan_id) {
    if ($data_jumlah_proses['total'] == '0') {

      /*$data['pekerjaan_status'] = anti_inject($pekerjaan_status);*/

      /*$this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);*/

      /*dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reviewed Oleh Cangun');*/
    }
  }
  /* Pekerjaan */

  /* select pekerjaan disposisi dengan pekerjaan dan status sama */
  $sql_disposisi_sama = $this->db->query("SELECT id_user,id_pekerjaan FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $where_disposisi['id_pekerjaan'] . "' AND pekerjaan_disposisi_status = '" . $where_disposisi['pekerjaan_disposisi_status'] . "'");

  $data_disposisi_sama = $sql_disposisi_sama->result_array();
  /* select pekerjaan disposisi dengan pekerjaan dan status sama */

  /*cek jika status koordinator*/
  $sql_koordinator = $this->db->query("SELECT id_user FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = " . $this->db->escape($pekerjaan_id) . " AND id_penanggung_jawab = " . $this->db->escape('y') . "AND pekerjaan_disposisi_status = " . $this->db->escape('4'));

  $data_koordinator = $sql_koordinator->row_array();
  /*cek jika status koordinator*/

  $data_user['pegawai_poscode'] = $isi['pegawai_direct_superior'];

  $sql_user_bagian = $this->db->query("SELECT * FROM global.global_bagian_detail WHERE id_pegawai = '" . $isi['pegawai_nik'] . "'");
  $data_user_bagian = $sql_user_bagian->row_array();

  $pekerjaan_disposisi_status = '4';

  $sql_avp_bagian = $this->db->query("SELECT * FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $data_user_bagian['id_bagian'] . "' AND pekerjaan_disposisi_status = '" . $pekerjaan_disposisi_status . "' AND id_pekerjaan = '" . $pekerjaan_id . "'");

  $data_avp_bagian = $sql_avp_bagian->row_array();

  $user = $data_avp_bagian;

  $sql_cek_vp  = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user='" . $data_avp_bagian['id_pegawai'] . "' AND pekerjaan_disposisi_status = '" . ($pekerjaan_disposisi_status + 2) . "' AND id_pekerjaan = '" . $pekerjaan_id . "'");

  $data_cek_vp = $sql_cek_vp->row_array();


  /* Get Pekerjaan */
  $sql_pekerjaan = $this->db->query("SELECT pic FROM dec.dec_pekerjaan WHERE pekerjaan_id = " . $this->db->escape($pekerjaan_id));
  $isi_pekerjaan = $sql_pekerjaan->row_array();
  /* Get Pekerjaan */

  /* Disposisi */
  if (empty($data_cek_vp)) {
   $data_disposisi['pekerjaan_disposisi_id'] = create_id();
   $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
   /*$data_disposisi['id_user'] = ($pekerjaan_status == '8') ? $isi_pekerjaan['pic'] : $user['pegawai_nik'];*/
   $data_disposisi['id_user'] = ($pekerjaan_status == '8') ? $isi_pekerjaan['pic'] : $user['id_pegawai'];
   $data_disposisi['id_pekerjaan'] = anti_inject($pekerjaan_id);
   $data_disposisi['pekerjaan_disposisi_status'] = anti_inject($pekerjaan_status);
   $data_disposisi['id_penanggung_jawab'] = ($data_koordinator['id_user'] == $user['id_pegawai']) ? 'y' : 'n';
   /*echo json_encode($data_disposisi);*/

   $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);


   $data_user['pegawai_nik'] = $data_disposisi['id_user'];
   $user_email = $this->M_user->getUser($data_user);

   $data_pekerjaan['pekerjaan_id'] = $pekerjaan_id;
   $pekerjaan = $this->M_pekerjaan->getPekerjaan($data_pekerjaan);

   $email_penerima = $user_email['email_pegawai'];
   $subjek = $pekerjaan['pekerjaan_judul'];
   $pesan = $pekerjaan['pekerjaan_deskripsi'];
   $sendmail = array(
     'email_penerima' => $email_penerima,
     'subjek' => $subjek,
     'content' => $pesan,
   );
   /*INSERT KE DB EMAIL*/
   $param_email['email_id'] = create_id();
   $param_email['id_penerima'] = $user_email['pegawai_nik'];
   $param_email['id_pengirim'] = $isi['pegawai_nik'];
   $param_email['id_pekerjaan'] = $pekerjaan_id;
   $param_email['id_pekerjaan_disposisi'] = $data_disposisi['pekerjaan_disposisi_id'];
   $param_email['email_subject'] = $subjek;
   $param_email['email_content'] = $pesan;
   $param_email['when_created'] = date('Y-m-d H:i:s');
   $param_email['who_created'] = $isi['pegawai_nama'];

   $this->M_pekerjaan->insertEmail($param_email);
 }
 /* Disposisi */
 /*}*/

 /*ubah status dokumen ke SEND*/
 $where_id_pekerjaan_dokumen = anti_inject($this->input->get_post('pekerjaan_id'));
 $where_id_user_dokumen = $isi['pegawai_nik'];
 $where_dokumen_status = '0';
 $param_user_dokumen['pekerjaan_dokumen_status'] = '2';

 $this->M_pekerjaan->updateStatusDokumen($where_id_pekerjaan_dokumen, $where_id_user_dokumen, $where_dokumen_status, $param_user_dokumen);

 $sql_progress = $this->db->query("SELECT * FROM dec.dec_pekerjaan_progress WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user = '" . $isi['pegawai_nik'] . "'");
 $data_progress = $sql_progress->row_array();

 if (empty($data_progress)) {
   $param_user_progress['progress_id'] = create_id();
   $param_user_progress['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
   $param_user_progress['id_user'] = $isi['pegawai_nik'];
   $param_user_progress['progress_jumlah'] = '92';
   $param_user_progress['id_bagian'] = $data_user_bagian['id_bagian'];

   $this->M_pekerjaan->insertProgressIFA($param_user_progress);
 } else {

   $where_id_pekerjaan_progress = $this->input->get_post('pekerjaan_id');
   $where_id_user_progress = $isi['pegawai_nik'];
   $param_user_progress['progress_jumlah'] = '92';

   $this->M_pekerjaan->updateProgressIFA($where_id_pekerjaan_progress, $where_id_user_progress, $param_user_progress);
 }

 dblog('I',  $pekerjaan_id, 'Pekerjaan telah di Send IFA', $isi['pegawai_nik']);
}

public function prosesApproveBerjalanIFC()
{
	if (isset($_GET['id_user'])) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $isi = $sql_user->row_array();
  } else {
    $isi = $this->session->userdata();
  }

  /*    deklarasi awal*/
  $pekerjaan_id = anti_inject($this->input->get_post('pekerjaan_id'));
  $pekerjaan_status = anti_inject('8');
  /*deklarasi awal*/

  /* Data CC  */
  if ($this->input->get_post('id_user_staf')) {
    $user = $this->input->get_post('id_user_staf');
    $user_implode = implode("','", $user);
    $cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'y' ")->result_array();
     /*insert history hapus cc*/
     foreach ($cc_not_in as $value_not_in) {
       $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
       dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC Non HPS', $isi['pegawai_nik']);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'y'");
     }
     foreach ($user as $key => $value) {
       /*cek apakah cc sudah pernah diinsertkan*/
       $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='y'")->row_array();
       if ($ada_cc['total'] == 0) {
        /*          jika belum diinsert*/
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = anti_inject($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
        $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi_doc['is_cc'] = anti_inject('y');
        $data_disposisi_doc['is_aktif'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
        $tujuan = $data_cc['pegawai_nik'];
        $tujuan_nama = $data_cc['pegawai_nama'];
        $kalimat = "Pekerjaan telah di CC kepada anda";
        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC Non HPS', $isi['pegawai_nik']);
        tasklog($pekerjaan_id, $data_disposisi_doc['pekerjaan_disposisi_status'], $data_cc['pegawai_nik'], $kalimat, 'n');
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
        sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
      }
    }
  }
  /*    Data CC*/

  /* Ubah Status Dari Staf */
  $where_status['id_user'] = anti_inject($isi['pegawai_nik']);
  $where_status['id_pekerjaan'] = anti_inject($this->input->get_post('pekerjaan_id'));
  $where_status['disposisi_status'] = anti_inject($this->input->get_post('pekerjaan_status'));
  $param_status['is_proses'] = anti_inject('y');
  $this->M_pekerjaan->updateStatus($where_status, $param_status);
  /* Ubah Status Dari Staf */

  /* Pekerjaan */
  $pekerjaan_status = $this->input->get('pekerjaan_status') + 1;
  $pekerjaan_id = $this->input->get('pekerjaan_id');

  $sql_jumlah_proses = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '" . $this->input->get_post('pekerjaan_status') . "' AND is_proses is null");
  $data_jumlah_proses = $sql_jumlah_proses->row_array();

					// if ($pekerjaan_id) {
  if ($data_jumlah_proses['total'] == '0') {
    $data['pekerjaan_status'] = anti_inject($pekerjaan_status);
    $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
  }
					// }
  /* Pekerjaan */

  $pekerjaan_disposisi_status = '4';
  $data_user['pegawai_poscode'] = $isi['pegawai_direct_superior'];

  /*    cek jika status koordinator*/
  $sql_koordinator = $this->db->query("SELECT id_user FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = " . $this->db->escape($pekerjaan_id) . " AND id_penanggung_jawab = " . $this->db->escape('y') . "AND pekerjaan_disposisi_status = " . $this->db->escape('4'));
  $data_koordinator = $sql_koordinator->row_array();

  $sql_user_bagian = $this->db->query("SELECT * FROM global.global_bagian_detail WHERE id_pegawai = '" . $isi['pegawai_nik'] . "'");
  $data_user_bagian = $sql_user_bagian->row_array();

  $sql_proses_bagian = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_bagian_detail b ON b.id_pegawai = a.id_user WHERE a.id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '" . $this->input->get_post('pekerjaan_status') . "' AND id_bagian = '" . $data_user_bagian['id_bagian'] . "' AND is_proses != 'y' ");
  $data_proses_bagian = $sql_proses_bagian->row_array();

  $sql_avp_bagian = $this->db->query("SELECT * FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $data_user_bagian['id_bagian'] . "' AND pekerjaan_disposisi_status = '" . $pekerjaan_disposisi_status . "' AND id_pekerjaan = '" . $pekerjaan_id . "'");
  $data_avp_bagian = $sql_avp_bagian->row_array();
  $user = $data_avp_bagian;

  $sql_cek_vp  = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user='" . $data_avp_bagian['id_pegawai'] . "' AND pekerjaan_disposisi_status = '" . ($pekerjaan_disposisi_status + 9) . "' AND id_pekerjaan = '" . $pekerjaan_id . "'");
  $data_cek_vp = $sql_cek_vp->row_array();

  /*cek apakah semua staf sudah proses*/
  $data_proses = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '11' AND id_pekerjaan = '" . $pekerjaan_id . "' AND is_proses IS NULL AND id_user IN(SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian = '" . $data_avp_bagian['id_bagian'] . "') ")->row_array();

  /* Get Pekerjaan */
  $sql_pekerjaan = $this->db->query("SELECT pic FROM dec.dec_pekerjaan WHERE pekerjaan_id = " . $this->db->escape($pekerjaan_id));
  $isi_pekerjaan = $sql_pekerjaan->row_array();
  /* Get Pekerjaan */

  if ($pekerjaan_status == '8') {
    $id_user_disposisi = $isi_pekerjaan['pic'];
  } else if ($isi['pegawai_poscode'] == 'E53600031A' || $isi['pegawai_poscode'] == 'E53500031B') {
    $id_user_disposisi = $isi['pegawai_nik'];
  } else {
    $id_user_disposisi = $user['id_pegawai'];
  }

  /* Disposisi */
  if (empty($data_cek_vp)) {
    $data_disposisi['pekerjaan_disposisi_id'] = create_id();
    $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
    /*$data_disposisi['id_user'] = ($pekerjaan_status == '8') ? $isi_pekerjaan['pic'] : $user['id_pegawai'];*/
    $data_disposisi['id_user'] = ($id_user_disposisi);
    $data_disposisi['id_pekerjaan'] = anti_inject($pekerjaan_id);
    $data_disposisi['pekerjaan_disposisi_status'] = anti_inject($pekerjaan_status);
    $data_disposisi['id_penanggung_jawab'] = ($data_koordinator['id_user'] == $user['id_pegawai']) ? 'y' : 'n';

    $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
  }
  /* Disposisi */

  /* Dokumen Non HPS*/
  $sql_bagian = $this->db->query("SELECT id_bagian FROM global.global_bagian_detail WHERE id_pegawai = '" . $isi['pegawai_nik'] . "'");
  $data_bagian = $sql_bagian->row_array();

  $data_pegawai = $this->db->query("SELECT id_user,id_pekerjaan,pekerjaan_disposisi_status FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $isi['pegawai_nik'] . "' AND pekerjaan_disposisi_status = '11'")->row_array();

  /*    ubah status dokumen ke IFC*/
  $data_dokumen_send = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_create_awal IN (SELECT id_pegawai FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $data_bagian['id_bagian'] . "' AND id_user = '" . $data_pegawai['id_user'] . "' AND id_pekerjaan = '" . $data_pegawai['id_pekerjaan'] . "' AND pekerjaan_disposisi_status='11') AND pekerjaan_dokumen_status <= '8' AND (is_lama !='y' OR is_lama is null) AND pekerjaan_dokumen_awal != 'y' AND is_hps = 'n' AND (is_update_ifa !='y' OR is_update_ifa is NULL)")->result_array();
  foreach ($data_dokumen_send as $val_dokumen) {
    /*cek apakah sudah ada dokumen di ifc nya*/
    $dokumen_ada = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_create_awal = '" . $val_dokumen['id_create_awal'] . "' AND pekerjaan_dokumen_status ='9' AND pekerjaan_dokumen_nama = '" . $val_dokumen['pekerjaan_dokumen_nama'] . "' AND id_pekerjaan_template = '" . $val_dokumen['id_pekerjaan_template'] . "'  AND is_hps = 'n' AND pekerjaan_dokumen_file = '" . $val_dokumen['pekerjaan_dokumen_file'] . "' AND pekerjaan_dokumen_file = '" . $val_dokumen['pekerjaan_dokumen_file'] . "'")->row_array();
    if (!empty($dokumen_ada) && ($dokumen_ada['pekerjaan_dokumen_nama'] == $val_dokumen['pekerjaan_dokumen_nama'] && $dokumen_ada['id_pekerjaan_template'] == $val_dokumen['id_pekerjaan_template'] && $dokumen_ada['id_create_awal'] == $val_dokumen['id_create_awal'])) {
      /* skip*/
    } else {
      $where['id_pekerjaan'] = $val_dokumen['id_pekerjaan'];
      $where['pekerjaan_dokumen_id'] = $val_dokumen['pekerjaan_dokumen_id'];
      $param['pekerjaan_dokumen_id'] = create_id();
      $param['pekerjaan_dokumen_status'] = '9';
      $param['pekerjaan_dokumen_waktu'] = date('Y-m-d H:i:s');
      $param['id_create'] = $isi['pegawai_nik'];
      $param['is_hps'] = $val_dokumen['is_hps'];
      $param['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
      $update = $this->M_pekerjaan->updateStatusDokumenIFCAll($where, $param);

      $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();

      $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "', pekerjaan_dokumen_revisi = '' WHERE pekerjaan_dokumen_id = '" . $param['pekerjaan_dokumen_id'] . "' AND (revisi_ifc !='y' OR revisi_ifc is NULL)");

      $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET is_update_ifa = 'y' WHERE pekerjaan_dokumen_id = '" . $where['pekerjaan_dokumen_id'] . "'");

      dblog('I', $this->input->get_post('pekerjaan_id'), 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
    }
  }
  $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET is_lama = 'y' WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_create_awal IN (SELECT id_pegawai FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $data_bagian['id_bagian'] . "' AND id_user = '" . $data_pegawai['id_user'] . "' AND id_pekerjaan = '" . $data_pegawai['id_pekerjaan'] . "' AND pekerjaan_disposisi_status='11') AND (pekerjaan_dokumen_status = '0' OR pekerjaan_dokumen_status = '8') AND (is_lama !='y' OR is_lama is null) AND pekerjaan_dokumen_awal != 'y' AND is_hps = 'n'");

  $data_dokumen_send_hps =   $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_create_awal IN (SELECT id_pegawai FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $data_bagian['id_bagian'] . "' AND id_user = '" . $data_pegawai['id_user'] . "' AND id_pekerjaan = '" . $data_pegawai['id_pekerjaan'] . "' AND pekerjaan_disposisi_status='11') AND pekerjaan_dokumen_status <= '8' AND (is_lama !='y' OR is_lama is null) AND pekerjaan_dokumen_awal != 'y' AND is_hps = 'y' AND (is_update_ifa !='y' OR is_update_ifa is NULL)")->result_array();

  foreach ($data_dokumen_send_hps as $val_dokumen) {
   $dokumen_ada = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_create_awal = '" . $val_dokumen['id_create_awal'] . "' AND pekerjaan_dokumen_status ='9' AND pekerjaan_dokumen_nama = '" . $val_dokumen['pekerjaan_dokumen_nama'] . "' AND id_pekerjaan_template = '" . $val_dokumen['id_pekerjaan_template'] . "'  AND is_hps = 'y' AND is_lama = 'y' AND pekerjaan_dokumen_file = '" . $val_dokumen['pekerjaan_dokumen_file'] . "'")->row_array();
   if (!empty($dokumen_ada) && ($dokumen_ada['pekerjaan_dokumen_nama'] == $val_dokumen['pekerjaan_dokumen_nama'] && $dokumen_ada['id_pekerjaan_template'] == $val_dokumen['id_pekerjaan_template'] && $dokumen_ada['id_create_awal'] == $val_dokumen['id_create_awal'])) {
     /*skip*/
   } else {
     $where['id_pekerjaan'] = $val_dokumen['id_pekerjaan'];
     $where['pekerjaan_dokumen_id'] = $val_dokumen['pekerjaan_dokumen_id'];
     $param['pekerjaan_dokumen_id'] = create_id();
     $param['pekerjaan_dokumen_status'] = '9';
     $param['pekerjaan_dokumen_waktu'] = date('Y-m-d H:i:s');
     $param['id_create'] = $isi['pegawai_nik'];
     $param['is_hps'] = $val_dokumen['is_hps'];
     $param['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
     $update = $this->M_pekerjaan->updateStatusDokumenIFCAll($where, $param);

     $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();

     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "', pekerjaan_dokumen_revisi = '' WHERE pekerjaan_dokumen_id = '" . $param['pekerjaan_dokumen_id'] . "' AND (revisi_ifc !='y' OR revisi_ifc is NULL)");

     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET is_update_ifa = 'y' WHERE
      pekerjaan_dokumen_id = '" . $where['pekerjaan_dokumen_id'] . "'");

										// $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $param['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '6'");

     dblog('I', $this->input->get_post('pekerjaan_id'), 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
   }
 }
 $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET is_lama = 'y' WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_create_awal IN (SELECT id_pegawai FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $data_bagian['id_bagian'] . "' AND id_user = '" . $data_pegawai['id_user'] . "' AND id_pekerjaan = '" . $data_pegawai['id_pekerjaan'] . "' AND pekerjaan_disposisi_status='11') AND (pekerjaan_dokumen_status = '0' OR pekerjaan_dokumen_status = '8') AND (is_lama !='y' OR is_lama is null) AND pekerjaan_dokumen_awal != 'y' AND is_hps = 'y'");

 $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_status = '7' WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_create_awal = '" . $isi['pegawai_nik'] . "' AND pekerjaan_dokumen_status = '6' AND (is_lama !='y' OR is_lama is null) AND pekerjaan_dokumen_awal != 'y' AND is_hps = 'y'");

 $sql_progress = $this->db->query("SELECT * FROM dec.dec_pekerjaan_progress WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user = '" . $isi['pegawai_nik'] . "'");
 $data_progress = $sql_progress->row_array();
 if (empty($data_progress)) {
   $param_user_progress['progress_id'] = create_id();
   $param_user_progress['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
   $param_user_progress['id_user'] = $isi['pegawai_nik'];
   $param_user_progress['progress_jumlah'] = '92';
   $param_user_progress['id_bagian'] = $data_user_bagian['id_bagian'];

   $this->M_pekerjaan->insertProgressIFA($param_user_progress);
 } else {
   $where_id_pekerjaan_progress = $this->input->get_post('pekerjaan_id');
   $where_id_user_progress = $isi['pegawai_nik'];
   $param_user_progress['progress_jumlah'] = '92';

   $this->M_pekerjaan->updateProgressIFA($where_id_pekerjaan_progress, $where_id_user_progress, $param_user_progress);
 }

 $data_users['pegawai_nik'] = $id_user_disposisi;
 $users = $this->M_user->getUser($data_users);
 $tujuan = $users['pegawai_nik'];
 $tujuan_nama = $users['pegawai_nama'];
 $kalimat = "Mohon untuk melakukan REVIEW IFC pada pekerjaan ini";
 dblog('I',  $pekerjaan_id, 'Pekerjaan telah di Send IFC', $isi['pegawai_nik']);
 tasklog($pekerjaan_id, $data_disposisi['pekerjaan_disposisi_status'], $users['pegawai_nik'], $kalimat, 'n');
 sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
 sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
}
/* Approve Pekerjaan Berjalan */


/* Approve Pekerjaan Berjalan Revisi  */
public function prosesApproveBerjalanRevisi()
{
	if (isset($_GET['id_user'])) {
    $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $isi = $sql_isi->row_array();
  } else {
    $isi = $this->session->userdata();
  }

  /* isi disposisi */
  if ($this->input->get_post('id_user_staf')) {
    $user = $this->input->get_post('id_user_staf');
    foreach ($user as $key => $value) {
      $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
      $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
      $data_disposisi_doc['id_user'] = anti_inject($value);
      $data_disposisi_doc['id_pekerjaan'] = anti_inject($this->input->get_post('pekerjaan_id'));
      $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
      $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');

      $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
      dblog('I',  $this->input->get_post('pekerjaan_id'), 'Pekerjaan Telah di Disposisikan', $isi['pegawai_nik']);

      $data_user['pegawai_nik'] = $data_disposisi_doc['id_user'];
      $user = $this->M_user->getUser($data_user);

      $data_pekerjaan['pekerjaan_id'] = $data_disposisi_doc['id_pekerjaan'];
      $pekerjaan = $this->M_pekerjaan->getPekerjaan($data_pekerjaan);

      $email_penerima = $user['email_pegawai'];
      $subjek = $pekerjaan['pekerjaan_judul'];
      $pesan = $pekerjaan['pekerjaan_deskripsi'];
      $sendmail = array(
       'email_penerima' => $email_penerima,
       'subjek' => $subjek,
       'content' => $pesan,
     );

      /*INSERT KE DB EMAIL*/
      $param_email['email_id'] = create_id();
      $param_email['id_penerima'] = $user['pegawai_nik'];
      $param_email['id_pengirim'] = $isi['pegawai_nik'];
      $param_email['id_pekerjaan'] = $data_disposisi_doc['id_pekerjaan'];
      $param_email['id_pekerjaan_disposisi'] = $data_disposisi_doc['pekerjaan_disposisi_id'];
      $param_email['email_subject'] = $subjek;
      $param_email['email_content'] = $pesan;
      $param_email['when_created'] = date('Y-m-d H:i:s');
      $param_email['who_created'] = $isi['pegawai_nama'];

      $this->M_pekerjaan->insertEmail($param_email);
    }
  }
  /* isi disposisi */

  /* Pekerjaan */
  $pekerjaan_status = $this->input->get('pekerjaan_status') + 1;
  /*$pekerjaan_status = '9';*/

  $pekerjaan_id = $this->input->get('pekerjaan_id');
  if ($pekerjaan_id) {
   $data['pekerjaan_status'] = '9';

   $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

   dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Approve Oleh Cangun', $isi['pegawai_nik']);
 }
 /* Pekerjaan */

 /* User */
 $data_user['pegawai_poscode'] = $isi['pegawai_direct_superior'];

 $user = $this->M_user->getUser($data_user);
 /* User */

 /* Get Pekerjaan */
 $sql_pekerjaan = $this->db->query("SELECT pic FROM dec.dec_pekerjaan WHERE pekerjaan_id = '" . $pekerjaan_id . "'");
 $isi_pekerjaan = $sql_pekerjaan->row_array();
 /* Get Pekerjaan */

 /* Disposisi */
 $data_disposisi['pekerjaan_disposisi_id'] = create_id();
 $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
 $data_disposisi['id_user'] = ($pekerjaan_status == '8') ? $isi_pekerjaan['pic'] : $user['pegawai_nik'];
 $data_disposisi['id_pekerjaan'] = anti_inject($pekerjaan_id);
 $data_disposisi['pekerjaan_disposisi_status'] = anti_inject('9');

 $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

 $data_user['pegawai_nik'] = $data_disposisi['id_user'];
 $user = $this->M_user->getUser($data_user);

 $data_pekerjaan['pekerjaan_id'] = $pekerjaan_id;
 $pekerjaan = $this->M_pekerjaan->getPekerjaan($data_pekerjaan);

 $email_penerima = $user['email_pegawai'];
 $subjek = $pekerjaan['pekerjaan_judul'];
 $pesan = $pekerjaan['pekerjaan_deskripsi'];
 $sendmail = array(
   'email_penerima' => $email_penerima,
   'subjek' => $subjek,
   'content' => $pesan,
 );
 /*INSERT KE DB EMAIL*/
 $param_email['email_id'] = create_id();
 $param_email['id_penerima'] = $user['pegawai_nik'];
 $param_email['id_pengirim'] = $isi['pegawai_nik'];
 $param_email['id_pekerjaan'] = $pekerjaan_id;
 $param_email['id_pekerjaan_disposisi'] = $data_disposisi['pekerjaan_disposisi_id'];
 $param_email['email_subject'] = $subjek;
 $param_email['email_content'] = $pesan;
 $param_email['when_created'] = date('Y-m-d H:i:s');
 $param_email['who_created'] = $isi['pegawai_nama'];

 $this->M_pekerjaan->insertEmail($param_email);
 /* Disposisi */
}
/* Approve Pekerjaan Berjalan Revisi */

/* Reject Pekerjaan Berjalan */
public function prosesRejectBerjalan()
{
	if (isset($_GET['id_user'])) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $isi = $sql_user->row_array();
  } else {
    $isi = $this->session->userdata();
  }
  /* Pekerjaan */
  $pekerjaan_id = $this->input->get('pekerjaan_id');
  if ($pekerjaan_id) {
    $data['pekerjaan_status'] = anti_inject('5');
    $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
    dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reject Oleh Cangun', $isi['pegawai_nik']);
  }
  /* Pekerjaan */
}


public function prosesRejectBerjalanIFA()
{
  if ($this->input->get('id_user')) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'");
    $isi = $sql_user->row_array();
  } else {
    $isi = $this->session->userdata();
  }
  /* Pekerjaan */
  $pekerjaan_id = $this->input->get('pekerjaan_id');
  if ($pekerjaan_id) {
    $data['pekerjaan_status'] = anti_inject('5');
    $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
  }
  $where_id_user = anti_inject($isi['pegawai_nik']);
  $where_id_pekerjaan = anti_inject($this->input->get_post('pekerjaan_id'));
  $where_disposisi_status = '6';
  $param_staf['is_proses'] = NULL;
  /*jika vp klik*/
  if ($this->input->get_post('status') == '7') {
    /*cek revisi*/
    $sql_revisi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN global.global_bagian_detail b ON b.id_pegawai = a.id_create_awal WHERE id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' AND pekerjaan_dokumen_status = '0' AND is_lama = 'n' AND pekerjaan_dokumen_awal = 'n'");
    $data_revisi = $sql_revisi->result_array();
    $jumlah_revisi = count($data_revisi);
    /*jika ada yang direvisi*/
    if ($jumlah_revisi > 0) {
      /*set avp penanggung jawab yang kena ke null*/
      $this->db->query("UPDATE dec.dec_pekerjaan_disposisi SET is_proses = null WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '6' AND id_penanggung_jawab = 'y'");
      foreach ($data_revisi as $key => $value) {
        /*seleksi avp dari perencana yang kena revisi*/
        $disposisi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $where_id_pekerjaan . "' AND pekerjaan_disposisi_status = '6' AND id_user IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian = '" . $value['id_bagian'] . "') ")->result_array();
        foreach ($disposisi as $value2) {
          /*seleksi perencana yang kena revisi */
          $sql_staf = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi dpd LEFT JOIN global.global_pegawai gp ON gp.pegawai_nik = dpd.id_user WHERE pekerjaan_disposisi_status = '5' AND is_aktif = 'y' AND id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian IN (SELECT bagian_id FROM global.global_bagian a LEFT JOIN global.global_bagian_detail b ON a.bagian_id = b.id_bagian WHERE b.id_pegawai = '" . $value2['id_user'] . "')) AND id_user IN('".$value['id_create_awal']."') ");
          $dataStaf = $sql_staf->result_array();
          $param_cangung['is_proses'] = NULL;
          foreach ($dataStaf as $valueStaf) :
            /*update perencana yang kena revisi ke null*/
            $this->db->query("UPDATE dec.dec_pekerjaan_disposisi SET is_proses = null WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user = '" . $valueStaf['id_user'] . "' AND pekerjaan_disposisi_status = '5'");
            $dari = $isi['pegawai_nik'];
            $tujuan = $valueStaf['pegawai_nik'];
            $tujuan_nama = $valueStaf['pegawai_nama'];
            $text = "Pekerjaan IFA anda telah di REJECT VP";
            /*notif ke dof*/
            sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
            sendNotif($pekerjaan_id, $dari, $tujuan, $text);
          endforeach;
        }
        /*hapus avp yang kena revisi*/
        $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $where_id_pekerjaan . "' AND pekerjaan_disposisi_status = '6' AND id_user IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian = '" . $value['id_bagian'] . "') ");

      }
      /*jika ga ada yang kena revisi*/
    } else {
      /*cek apv dari perencana*/
      $disposisi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $where_id_pekerjaan . "' AND pekerjaan_disposisi_status = '6' ")->result_array();
      foreach ($disposisi as $value) {
        /*cek perencana*/
        $sql_staf = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi dpd LEFT JOIN global.global_pegawai gp ON gp.pegawai_nik = dpd.id_user WHERE pekerjaan_disposisi_status = '5' AND is_aktif = 'y' AND id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian IN (SELECT bagian_id FROM global.global_bagian a LEFT JOIN global.global_bagian_detail b ON a.bagian_id = b.id_bagian WHERE b.id_pegawai = '" . $value['id_user'] . "')) ");
        $dataStaf = $sql_staf->result_array();
        $param_cangung['is_proses'] = NULL;
        foreach ($dataStaf as $valueStaf) :
          /*set perencana ke null*/
          $this->db->query("UPDATE dec.dec_pekerjaan_disposisi SET is_proses = null WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user = '" . $valueStaf['id_user'] . "' AND pekerjaan_disposisi_status = '5'");
          $dari = $isi['pegawai_nik'];
          $tujuan = $valueStaf['pegawai_nik'];
          $tujuan_nama = $valueStaf['pegawai_nama'];
          $text = "Pekerjaan IFA anda telah di REJECT VP";
          /* Notif ke dof */
          sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
          sendNotif($pekerjaan_id, $dari, $tujuan, $text);
        endforeach;
        /*hapus avp*/
        $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $where_id_pekerjaan . "' AND pekerjaan_disposisi_status = '6' ");
      }
    }
    /*hapus vp*/
    $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $where_id_pekerjaan . "' AND id_user = '" . $isi['pegawai_nik'] . "' AND pekerjaan_disposisi_status = '7'");
    /*set status pekerjaan ke 5*/
    $this->db->query("UPDATE dec.dec_pekerjaan SET pekerjaan_status ='5' WHERE pekerjaan_id = '" . $where_id_pekerjaan . "'");
    /*history*/ 
    dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reject Oleh Cangun', $isi['pegawai_nik']);
    /*jika avp klik*/
  } else {
    /*bagian avp*/
    $data_bagian = $this->db->get_where('global.global_bagian_detail',['id_pegawai'=>$isi['pegawai_nik']])->row_array();
    /*cek revisi*/

    $sql_revisi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN global.global_bagian_detail b ON b.id_pegawai = a.id_create_awal WHERE id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' AND pekerjaan_dokumen_status = '0' AND is_lama = 'n' AND pekerjaan_dokumen_awal = 'n' AND id_create_awal IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian = '".$data_bagian['id_bagian']."')");
    $data_revisi = $sql_revisi->result_array();
    $jumlah_revisi = count($data_revisi);
    /*jika ada yang direvisi*/
    if ($jumlah_revisi > 0) {
      /*set avp penanggung jawab yang kena ke null*/
      $this->db->query("UPDATE dec.dec_pekerjaan_disposisi SET is_proses = null WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '6' AND id_penanggung_jawab = 'y'");
      foreach ($data_revisi as $key => $value) {
        /*seleksi avp dari perencana yang kena revisi*/
        $disposisi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $where_id_pekerjaan . "' AND pekerjaan_disposisi_status = '6' AND id_user IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian = '" . $value['id_bagian'] . "') ")->result_array();
        foreach ($disposisi as $value2) {
          /*seleksi perencana yang kena revisi */
          $sql_staf = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi dpd LEFT JOIN global.global_pegawai gp ON gp.pegawai_nik = dpd.id_user WHERE pekerjaan_disposisi_status = '5' AND is_aktif = 'y' AND id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian IN (SELECT bagian_id FROM global.global_bagian a LEFT JOIN global.global_bagian_detail b ON a.bagian_id = b.id_bagian WHERE b.id_pegawai = '" . $value2['id_user'] . "')) AND id_user IN('".$value['id_create_awal']."') ");
          $dataStaf = $sql_staf->result_array();
          $param_cangung['is_proses'] = NULL;
          foreach ($dataStaf as $valueStaf) :
            /*update perencana yang kena revisi ke null*/
            $this->db->query("UPDATE dec.dec_pekerjaan_disposisi SET is_proses = null WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user = '" . $valueStaf['id_user'] . "' AND pekerjaan_disposisi_status = '5'");
            $dari = $isi['pegawai_nik'];
            $tujuan = $valueStaf['pegawai_nik'];
            $tujuan_nama = $valueStaf['pegawai_nama'];
            $text = "Pekerjaan IFA anda telah di REJECT VP";
            /*notif ke dof*/
            sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
            sendNotif($pekerjaan_id, $dari, $tujuan, $text);
          endforeach;
        }
        /*hapus avp yang kena revisi*/
                  // $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $where_id_pekerjaan . "' AND pekerjaan_disposisi_status = '6' AND id_user IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian = '" . $value['id_bagian'] . "') AND id_user = '".$isi['id_user']."'");
        $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user = '" . $isi['pegawai_nik'] . "' AND pekerjaan_disposisi_status = '6'");

      }
      /*jika ga ada yang kena revisi*/
    }else{

      $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user = '" . $isi['pegawai_nik'] . "' AND pekerjaan_disposisi_status = '6'");
      /* Pekerjaan */
      /* Staf */
      $sql_staf = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi dpd LEFT JOIN global.global_pegawai gp ON gp.pegawai_nik = dpd.id_user WHERE pekerjaan_disposisi_status = '5' AND is_aktif = 'y' AND id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian IN (SELECT bagian_id FROM global.global_bagian a LEFT JOIN global.global_bagian_detail b ON a.bagian_id = b.id_bagian WHERE b.id_pegawai = '" . $isi['pegawai_nik'] . "'))");
      $dataStaf = $sql_staf->result_array();
      $param_cangung['is_proses'] = NULL;
      foreach ($dataStaf as $valueStaf) :
        $this->db->query("UPDATE dec.dec_pekerjaan_disposisi SET is_proses = null WHERE is_aktif = 'y' AND id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user = '" . $valueStaf['id_user'] . "' AND pekerjaan_disposisi_status = '5'");
        /* Staf */
        /* Notif DOF */
        $dari = $isi['pegawai_nik'];
        $tujuan = $valueStaf['pegawai_nik'];
        $tujuan_nama = $valueStaf['pegawai_nama'];
        $text = "Pekerjaan IFA anda telah di REJECT AVP";
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
        sendNotif($pekerjaan_id, $dari, $tujuan, $text);
      endforeach;
      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reject Oleh Cangun', $isi['pegawai_nik']);
      /* Notif DOF */
    }
  }
}
/* Reject Pekerjaan Berjalan */

public function prosesRejectBerjalanIFC()
{
  if (isset($_GET['id_user'])) {
    $session = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'")->row_array();
  } else {
    $session = $this->session->userdata();
  }

  $pekerjaan_id = $this->input->get_post('pekerjaan_id');

  /*ambil data apakah dari avp atau dari vp nya yang melakukan reject*/
  $data_avp_reject = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user = '" . $session['pegawai_nik'] . "' AND pekerjaan_disposisi_status = '12'")->row_array();

  /*jika dari avp*/
  if ($data_avp_reject) {
    $data_staf = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '11' AND is_aktif = 'y' AND id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian IN (SELECT bagian_id FROM global.global_bagian a LEFT JOIN global.global_bagian_detail b ON a.bagian_id = b.id_bagian WHERE b.id_pegawai = '" . $data_avp_reject['id_user'] . "'))")->result_array();

    /*      update status staf ke null*/
    foreach ($data_staf as $val_staf) {
      $this->db->query("UPDATE dec.dec_pekerjaan_disposisi SET is_proses = NULL WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND is_aktif = 'y' AND id_user = '" . $val_staf['id_user'] . "' AND pekerjaan_disposisi_status = '11' ");

      $data_users = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $val_staf['id_user']))->row_array();

      $dari = $session['pegawai_nik'];
      $tujuan = $data_users['pegawai_nik'];
      $tujuan_nama = $data_users['pegawai_nama'];
      $text = "Pekerjaan IFC anda telah di REJECT AVP";
      sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
      sendNotif($pekerjaan_id, $dari, $tujuan, $text);
    }

    /*hapus data avp agar tidak terdoble*/
    $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND is_aktif = 'y' AND id_user = '" . $data_avp_reject['id_user'] . "' AND pekerjaan_disposisi_status = '12' ");
    /*kalau bukan avp (vpnya)*/
  } else {

    $sql_revisi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN global.global_bagian_detail b ON b.id_pegawai = a.id_create_awal WHERE id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' AND pekerjaan_dokumen_status = '0' AND is_lama = 'n' AND pekerjaan_dokumen_awal = 'n'");
    $data_revisi = $sql_revisi->result_array();
    $jumlah_revisi = count($data_revisi);

    if ($jumlah_revisi > 0) {
      foreach ($data_revisi as $key => $value) {
        $disposisi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '12' AND id_user IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian = '" . $value['id_bagian'] . "') ")->result_array();
        foreach ($disposisi as $value2) {
          $sql_staf = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi dpd LEFT JOIN global.global_pegawai gp ON gp.pegawai_nik = dpd.id_user WHERE pekerjaan_disposisi_status = '5' AND is_aktif = 'y' AND id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian IN (SELECT bagian_id FROM global.global_bagian a LEFT JOIN global.global_bagian_detail b ON a.bagian_id = b.id_bagian WHERE b.id_pegawai = '" . $value2['id_user'] . "'))");
          $dataStaf = $sql_staf->result_array();
          $param_cangung['is_proses'] = NULL;
          foreach ($dataStaf as $valueStaf) :
            $this->db->query("UPDATE dec.dec_pekerjaan_disposisi SET is_proses = null WHERE is_aktif = 'y' AND id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user = '" . $valueStaf['id_user'] . "' AND pekerjaan_disposisi_status = '11'");
            /* Staf */
            /* Notif DOF */
            $dari = $isi['pegawai_nik'];
            $tujuan = $valueStaf['pegawai_nik'];
            $tujuan_nama = $valueStaf['pegawai_nama'];
            $text = "Pekerjaan IFC anda telah di REJECT VP";
            sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
            sendNotif($pekerjaan_id, $dari, $tujuan, $text);
          endforeach;
        }
        $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '12' AND id_user IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian = '" . $value['id_bagian'] . "') ");
      }
    } else {
      $disposisi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '12' ")->result_array();
      foreach ($disposisi as $value) {
        $sql_staf = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi dpd LEFT JOIN global.global_pegawai gp ON gp.pegawai_nik = dpd.id_user WHERE pekerjaan_disposisi_status = '5' AND is_aktif = 'y' AND id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian IN (SELECT bagian_id FROM global.global_bagian a LEFT JOIN global.global_bagian_detail b ON a.bagian_id = b.id_bagian WHERE b.id_pegawai = '" . $value['id_user'] . "'))");
        $dataStaf = $sql_staf->result_array();
        $param_cangung['is_proses'] = NULL;
        foreach ($dataStaf as $valueStaf) :
          $this->db->query("UPDATE dec.dec_pekerjaan_disposisi SET is_proses = null WHERE is_aktif = 'y' AND id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user = '" . $valueStaf['id_user'] . "' AND pekerjaan_disposisi_status = '11'");
          /* Staf */
          /* Notif DOF */
          $dari = $isi['pegawai_nik'];
          $tujuan = $valueStaf['pegawai_nik'];
          $tujuan_nama = $valueStaf['pegawai_nama'];
          $text = "Pekerjaan IFC anda telah di REJECT VP";
          sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
          sendNotif($pekerjaan_id, $dari, $tujuan, $text);
        endforeach;
        $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '12' ");
      }
    }
      // $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' AND id_user = '" . $isi['pegawai_nik'] . "' AND pekerjaan_disposisi_status = '7'");
    $this->db->query("UPDATE dec.dec_pekerjaan SET pekerjaan_status ='11' WHERE pekerjaan_id = '" . $this->input->get('pekerjaan_id') . "'");
    /*hapus data vp nya juga*/
    $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND is_aktif = 'y' AND id_user = '" . $session['pegawai_nik'] . "' AND pekerjaan_disposisi_status = '13' ");
  }

  /*    ubah status jadi 9*/
  $pekerjaan_id = $this->input->get_post('pekerjaan_id');
  $data_status['pekerjaan_status'] = '11';
  $this->M_pekerjaan->updatePekerjaan($data_status, $pekerjaan_id);
  dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reject Oleh Cangun', $session['pegawai_nik']);
}


/* Approve Pekerjaan IFA */
public function prosesApproveIFA()
{
	if ($this->input->get('id_user')) {
		$sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'");
		$isi = $sql_user->row_array();
	} else {
		$isi = $this->session->userdata();
	}

	$pekerjaan_id = $this->input->get_post('pekerjaan_id');

	/*CC*/
	if ($this->input->get_post('cc')) {
		$user = $this->input->get_post('cc');
		$user_implode = implode("','", $user);
		$cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'y' ")->result_array();
      foreach ($cc_not_in as $value_not_in) {
        $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
        dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC Non HPS', $isi['pegawai_nik']);
        $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'y'");
      }
      foreach ($user as $key => $value) {
        $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='y'")->row_array();
        if ($ada_cc['total'] == 0) {
          $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
          $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
          $data_disposisi_doc['id_user'] = anti_inject($value);
          $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
          $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
          $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
          $data_disposisi_doc['is_cc'] = anti_inject('y');
          $data_disposisi_doc['is_aktif'] = 'y';
          $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
          $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
          $tujuan = $data_cc['pegawai_nik'];
          $tujuan_nama = $data_cc['pegawai_nama'];
          $kalimat = "Pekerjaan telah di CC kepada anda";
          dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC Non HPS', $isi['pegawai_nik']);
          tasklog($pekerjaan_id, $data_disposisi_doc['pekerjaan_disposisi_status'], $data_cc['pegawai_nik'], $kalimat, 'n');
          sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
          sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
        }
      }
    }
    /*CC*/

    /*CC HPS*/
    if ($this->input->get_post('cc_hps')) {
      $user = $this->input->get_post('cc_hps');
      $user_implode = implode("','", $user);
      $cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'h' ")->result_array();
        foreach ($cc_not_in as $value_not_in) {
          $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
          dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC HPS', $isi['pegawai_nik']);
          $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'h'");
        }
        foreach ($user as $key => $value) {
          $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='h'")->row_array();
          if ($ada_cc['total'] == 0) {
            $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
            $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
            $data_disposisi_doc['id_user'] = anti_inject($value);
            $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
            $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
            $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
            $data_disposisi_doc['is_cc'] = anti_inject('h');
            $data_disposisi_doc['is_aktif'] = 'y';
            $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
            $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
            $tujuan = $data_cc['pegawai_nik'];
            $tujuan_nama = $data_cc['pegawai_nama'];
            $kalimat = "Pekerjaan telah di CC kepada anda";
            dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC HPS', $isi['pegawai_nik']);
            tasklog($pekerjaan_id, $data_disposisi_doc['pekerjaan_disposisi_status'], $data_cc['pegawai_nik'], $kalimat, 'n');
            sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
            sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
          }
        }
      }
      /*CC HPS*/

      /*STATUS PEKERJAAN*/
      $where_id_user = ($isi['pegawai_nik']);
      $where_id_pekerjaan = $this->input->get_post('pekerjaan_id');
      $where_disposisi_status = '8';
      $param_staf['is_proses'] = 'y';
      $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);

      $klasifikasi_pekerjaan = $this->db->query("SELECT * FROM dec.dec_pekerjaan a LEFT JOIN global.global_klasifikasi_pekerjaan b ON b.klasifikasi_pekerjaan_id = a.id_klasifikasi_pekerjaan where pekerjaan_id = '".$this->input->post('pekerjaan_id')."'")->row_array();

      if($klasifikasi_pekerjaan['klasifikasi_pekerjaan_kode']=='ifi'):
       $data['pekerjaan_status'] = '14';
       $data['pekerjaan_waktu_selesai'] = date('Y-m-d H:i:s');
       $data['pekerjaan_note'] = $this->input->post('pekerjaan_note');
       $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
       dblog('I',  $pekerjaan_id, 'Pekerjaan IFI Telah di Approve PIC Peminta Jasa dengan Note <span style="color:green">' . $this->input->get_post('pekerjaan_note') . '</span>', $isi['pegawai_nik']);
     else:
       $pekerjaan_status = 9;
       $sql_proses = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND (is_proses != 'y' OR is_proses is null)");
       $data_proses = $sql_proses->row_array();
       $data['pekerjaan_status'] = anti_inject($pekerjaan_status);
       $data['pekerjaan_note'] = $this->input->post('pekerjaan_note');
       $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
       dblog('I',  $pekerjaan_id, 'Pekerjaan IFA Telah di Approve PIC Peminta Jasa dengan Note <span style="color:green">' . $this->input->get_post('pekerjaan_note') . '</span>', $isi['pegawai_nik']);
       /* STATUS PEKEERJAAN */

       /* User */
       $data_user['pegawai_poscode'] = $isi['pegawai_direct_superior'];
       $user = $this->M_user->getUser($data_user);
       /* User */

       /* AVP */
       $sql_avp = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '1' AND id_pekerjaan = '" . $pekerjaan_id . "'");
       $data_avp = $sql_avp->row_array();
       /* AVP */

       /* Disposisi */
       $data_disposisi = [
        'pekerjaan_disposisi_id' => create_id(),
        'pekerjaan_disposisi_waktu' => date("Y-m-d H:i:s"),
        'id_user' => $data_avp['id_user'],
        'id_pekerjaan' => $pekerjaan_id,
        'pekerjaan_disposisi_status' => $pekerjaan_status,
        'id_penanggung_jawab' => '',
      ];
      $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

      $user_disposisi = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $data_avp['id_user']))->row_array();
      $tujuan = $user_disposisi['pegawai_nik'];
      $tujuan_nama = $user_disposisi['pegawai_nama'];
      $kalimat = "Mohon untuk melakukan PROSES pada pekerjaan ini";
      tasklog($pekerjaan_id, $data_disposisi['pekerjaan_disposisi_status'], $user_disposisi['pegawai_nik'], $kalimat, 'n');
      sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
      sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
      /* Disposisi */
    endif;

    /*DOKUMEN NON HPS*/
    $data_dokumen_non = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND is_lama = 'n' and pekerjaan_dokumen_status = '4' AND is_hps='n' ")->result_array();
    foreach ($data_dokumen_non as $val_dokumen) {
     $status_dokumen = '5';
     $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
     $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
     $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];

     $data_dok['pekerjaan_dokumen_id_temp'] = anti_inject($val_dokumen['pekerjaan_dokumen_id']);
     $data_dok['pekerjaan_dokumen_id'] = create_id();
     $data_dok['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
     $data_dok['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
     $data_dok['pekerjaan_dokumen_keterangan'] = $val_dokumen['pekerjaan_dokumen_keterangan'];
     $data_dok['id_create'] = $isi['pegawai_nik'];
     $data_dok['is_proses'] = 'ifaavp';
     $data_dok['id_create_awal'] = $data_dokumen['id_create_awal'];
     $data_dok['pekerjaan_dokumen_waktu'] = date('Y-m-d H:i:s');
     $data_dok['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
     $this->M_pekerjaan->simpanAksiIFASama($data_dok);
     if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
       dblog('I', $pekerjaan_id, 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data_dok['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
     } else {
       $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data_dok['pekerjaan_dokumen_id'] . "'");
       dblog('I', $pekerjaan_id, 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
     }
     $param_lama['is_lama'] = 'y';
     $param_id = $val_dokumen['pekerjaan_dokumen_id'];
     $this->M_pekerjaan->editAksi($param_lama, $param_id);
   }
   /*DOKUMEN NON HPS*/

   /*Dokumen HPS*/
   $data_dokumen_hps = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND is_lama = 'n' and pekerjaan_dokumen_status = '4' AND is_hps='y' ")->result_array();
   foreach ($data_dokumen_hps as $val_dokumen) {
     $status_dokumen = '5';
     $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template WHERE a.pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
     $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
     $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];

     $data_dok['pekerjaan_dokumen_id_temp'] = anti_inject($val_dokumen['pekerjaan_dokumen_id']);
     $data_dok['pekerjaan_dokumen_id'] = create_id();
     $data_dok['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
     $data_dok['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
     $data_dok['pekerjaan_dokumen_keterangan'] = $val_dokumen['pekerjaan_dokumen_keterangan'];
     $data_dok['id_create'] = $isi['pegawai_nik'];
     $data_dok['is_proses'] = 'ifaavp';
     $data_dok['id_create_awal'] = $data_dokumen['id_create_awal'];
     $data_dok['pekerjaan_dokumen_waktu'] = date('Y-m-d H:i:s');
     $data_dok['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
     $this->M_pekerjaan->simpanAksiIFASama($data_dok);

     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data_dok['pekerjaan_dokumen_id'] . "'");
     if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
       dblog('I', $pekerjaan_id, 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data_dok['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
     } else {
       $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '".date('Y-m-d H:i:s')."' WHERE pekerjaan_dokumen_id = '".$data['pekerjaan_dokumen_id']."' AND pekerjaan_dokumen_status >='4'");
       dblog('I', $pekerjaan_id, 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
     }

     $param_lama['is_lama'] = 'y';
     $param_id = $val_dokumen['pekerjaan_dokumen_id'];
     $this->M_pekerjaan->editAksi($param_lama, $param_id);
   }
   /*Dokumen HPS*/
 }
 /* Approve Pekerjaan IFA */


 /* Approve Pekerjaan IFA AVP */
 public function prosesApproveIFAAVP()
 {
   if ($this->input->get('id_user')) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $isi = $sql_user->row_array();
  } else {
    $isi = $this->session->userdata();
  }
  $pekerjaan_id = $this->input->get_post('pekerjaan_id');

  /*CC*/
  if ($this->input->get_post('cc')) {
    $user = $this->input->get_post('cc');
    $user_implode = implode("','", $user);
    $cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'y' ")->result_array();
     echo $this->db->last_query();
     foreach ($cc_not_in as $value_not_in) {
       $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
       dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC Non HPS', $isi['pegawai_nik']);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'y'");
     }
     foreach ($user as $key => $value) {
       $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='y'")->row_array();
       if ($ada_cc['total'] == 0) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = anti_inject($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
        $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi_doc['is_cc'] = anti_inject('y');
        $data_disposisi_doc['is_aktif'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
        $tujuan = $data_cc['pegawai_nik'];
        $tujuan_nama = $data_cc['pegawai_nama'];
        $kalimat = "Pekerjaan telah di CC kepada anda";
        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC Non HPS', $isi['pegawai_nik']);
        tasklog($pekerjaan_id, $data_disposisi_doc['pekerjaan_disposisi_status'], $data_cc['pegawai_nik'], $kalimat, 'n');
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
        sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
      }
    }
  }
  /*CC*/

  /*CC HPS*/
  if ($this->input->get_post('cc_hps')) {
    $user = $this->input->get_post('cc_hps');
    $user_implode = implode("','", $user);
    $cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'h' ")->result_array();
     foreach ($cc_not_in as $value_not_in) {
       $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
       dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC HPS', $isi['pegawai_nik']);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'h'");
     }
     foreach ($user as $key => $value) {
       $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='h'")->row_array();
       if ($ada_cc['total'] == 0) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = anti_inject($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
        $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi_doc['is_cc'] = anti_inject('h');
        $data_disposisi_doc['is_aktif'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
        $tujuan = $data_cc['pegawai_nik'];
        $tujuan_nama = $data_cc['pegawai_nama'];
        $kalimat = "Pekerjaan telah di CC kepada anda";
        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC HPS', $isi['pegawai_nik']);
        tasklog($pekerjaan_id, $data_disposisi_doc['pekerjaan_disposisi_status'], $data_cc['pegawai_nik'], $kalimat, 'n');
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
        sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
      }
    }
  }
  /*CC HPS*/
  $where_id_user = ($isi['pegawai_nik']);
  $where_id_pekerjaan = $this->input->get_post('pekerjaan_id');
  $where_disposisi_status = '9';
  $param_staf['is_proses'] = 'y';
  $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);

  /* Pekerjaan */
  $pekerjaan_status = $this->input->get_post('pekerjaan_status') + 1;

  $sql_proses = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '9' AND (is_proses != 'y' OR is_proses is null)");
  $data_proses = $sql_proses->row_array();
  $data['pekerjaan_status'] = anti_inject($pekerjaan_status);
  $data['pekerjaan_note'] = $this->input->get_post('pekerjaan_note');

  $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

  dblog('I',  $pekerjaan_id, 'Pekerjaan IFA Telah di Reviewed Oleh AVP Peminta Jasa dengan Note <span style="color:green">' . $this->input->get_post('pekerjaan_note') . '</span>', $isi['pegawai_nik']);
  /* Pekerjaan */

  /* User */
  $data_user['pegawai_poscode'] = $isi['pegawai_direct_superior'];
  $user = $this->M_user->getUser($data_user);
  /* User */

  /* AVP */
  $sql_avp = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '2' AND id_pekerjaan = '" . $pekerjaan_id . "'");
  $data_avp = $sql_avp->row_array();
  /* AVP */

  /* Disposisi */
  $data_disposisi = [
    'pekerjaan_disposisi_id' => create_id(),
    'pekerjaan_disposisi_waktu' => date("Y-m-d H:i:s"),
    'id_user' => $data_avp['id_user'],
    'id_pekerjaan' => $pekerjaan_id,
    'pekerjaan_disposisi_status' => $pekerjaan_status,
    'id_penanggung_jawab' => '',
  ];
  $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

  $user_disposisi = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $data_avp['id_user']))->row_array();
  $tujuan = $user_disposisi['pegawai_nik'];
  $tujuan_nama = $user_disposisi['pegawai_nama'];
  $kalimat = "Mohon untuk melakukan PROSES pada pekerjaan ini";
  tasklog($pekerjaan_id, $data_disposisi['pekerjaan_disposisi_status'], $user_disposisi['pegawai_nik'], $kalimat, 'n');
  sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
  sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
  /* Disposisi */

  /*    otomatisasi dari pada surat nya*/
  /*    cek dulu nih suratnya*/
  /*DOKUMEN NON HPS*/
  $data_dokumen_non = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND is_lama = 'n' and pekerjaan_dokumen_status = '5' AND is_hps='n' ")->result_array();

  foreach ($data_dokumen_non as $val_dokumen) {
    $status_dokumen = '6';

    $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();

    $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();

    $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
    $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];

    $data_dok['pekerjaan_dokumen_id_temp'] = anti_inject($val_dokumen['pekerjaan_dokumen_id']);
    $data_dok['pekerjaan_dokumen_id'] = create_id();
    $data_dok['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
    $data_dok['pekerjaan_dokumen_revisi'] = null;
    $data_dok['pekerjaan_dokumen_keterangan'] = $val_dokumen['pekerjaan_dokumen_keterangan'];
    $data_dok['id_create'] = $isi['pegawai_nik'];
    $data_dok['is_proses'] = 'ifavp';
    $data_dok['id_create_awal'] = $data_dokumen['id_create_awal'];
    $data_dok['pekerjaan_dokumen_waktu'] = date('Y-m-d H:i:s');
    $data_dok['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
    $this->M_pekerjaan->simpanAksiIFASama($data_dok);

    if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
      dblog('I', $pekerjaan_id, 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data_dok['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
    } else {
      $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data_dok['pekerjaan_dokumen_id'] . "'");
      dblog('I', $pekerjaan_id, 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
    }

    $param_lama['is_lama'] = 'y';
    $param_id = $val_dokumen['pekerjaan_dokumen_id'];
    $this->M_pekerjaan->editAksi($param_lama, $param_id);
  }
  /*DOKUMEN NON HPS*/

  /*Dokumen HPS*/
  $data_dokumen_hps = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND is_lama = 'n' and pekerjaan_dokumen_status = '5' AND is_hps='y' ")->result_array();

  foreach ($data_dokumen_hps as $val_dokumen) {
   $status_dokumen = '6';
   $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template WHERE a.pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();

   $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
   $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];

   $data_dok['pekerjaan_dokumen_id_temp'] = anti_inject($val_dokumen['pekerjaan_dokumen_id']);
   $data_dok['pekerjaan_dokumen_id'] = create_id();
   $data_dok['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
   $data_dok['pekerjaan_dokumen_revisi'] = null;
   $data_dok['pekerjaan_dokumen_keterangan'] = $val_dokumen['pekerjaan_dokumen_keterangan'];
   $data_dok['id_create'] = $isi['pegawai_nik'];
   $data_dok['is_proses'] = 'ifavp';
   $data_dok['id_create_awal'] = $data_dokumen['id_create_awal'];
   $data_dok['pekerjaan_dokumen_waktu'] = date('Y-m-d H:i:s');
   $data_dok['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
   $this->M_pekerjaan->simpanAksiIFASama($data_dok);

   $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data_dok['pekerjaan_dokumen_id'] . "'");
							 /*if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
								dblog('I', $pekerjaan_id, 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data_dok['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
							} else {
								$this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '".date('Y-m-d H:i:s')."' WHERE pekerjaan_dokumen_id = '".$data['pekerjaan_dokumen_id']."' AND pekerjaan_dokumen_status >='4'");
								dblog('I', $pekerjaan_id, 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
							}*/

							$param_lama['is_lama'] = 'y';
							$param_id = $val_dokumen['pekerjaan_dokumen_id'];
							$this->M_pekerjaan->editAksi($param_lama, $param_id);
						}
						/*Dokumen HPS*/
					}
					/* Approve Pekerjaan IFA AVP */


					/* Approve Pekerjaan IFA VP */
					public function prosesApproveIFAVP()
					{
						if ($this->input->get('id_user')) {
             $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
             $isi = $sql_user->row_array();
           } else {
             $isi = $this->session->userdata();
           }

           $pekerjaan_id = $this->input->get_post('pekerjaan_id');

           /*CC*/
           if ($this->input->get_post('cc')) {
             $user = $this->input->get_post('cc');
             $user_implode = implode("','", $user);
             $cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'y' ")->result_array();
              foreach ($cc_not_in as $value_not_in) {
                $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
                dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC Non HPS', $isi['pegawai_nik']);
                $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'y'");
              }
              foreach ($user as $key => $value) {
                $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='y'")->row_array();
                if ($ada_cc['total'] == 0) {
                 $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
                 $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
                 $data_disposisi_doc['id_user'] = anti_inject($value);
                 $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
                 $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
                 $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
                 $data_disposisi_doc['is_cc'] = anti_inject('y');
                 $data_disposisi_doc['is_aktif'] = 'y';
                 $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
                 $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
                 $tujuan = $data_cc['pegawai_nik'];
                 $tujuan_nama = $data_cc['pegawai_nama'];
                 $kalimat = "Pekerjaan telah di CC kepada anda";
                 dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC Non HPS', $isi['pegawai_nik']);
                 tasklog($pekerjaan_id, $data_disposisi_doc['pekerjaan_disposisi_status'], $data_cc['pegawai_nik'], $kalimat, 'n');
                 sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
                 sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
               }
             }
           }
           /*CC*/

           /*CC HPS*/
           if ($this->input->get_post('cc_hps')) {
             $user = $this->input->get_post('cc_hps');
             $user_implode = implode("','", $user);
             $cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'h' ")->result_array();
              foreach ($cc_not_in as $value_not_in) {
                $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
                dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC HPS', $isi['pegawai_nik']);
                $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'h'");
              }
              foreach ($user as $key => $value) {
                $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='h'")->row_array();
                if ($ada_cc['total'] == 0) {
                 $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
                 $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
                 $data_disposisi_doc['id_user'] = anti_inject($value);
                 $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
                 $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
                 $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
                 $data_disposisi_doc['is_cc'] = anti_inject('h');
                 $data_disposisi_doc['is_aktif'] = 'y';
                 $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
                 $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
                 $tujuan = $data_cc['pegawai_nik'];
                 $tujuan_nama = $data_cc['pegawai_nama'];
                 $kalimat = "Pekerjaan telah di CC kepada anda";
                 dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC HPS', $isi['pegawai_nik']);
                 tasklog($pekerjaan_id, $data_disposisi_doc['pekerjaan_disposisi_status'], $data_cc['pegawai_nik'], $kalimat, 'n');
                 sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
                 sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
               }
             }
           }
           /*CC HPS*/

           $where_id_user = ($isi['pegawai_nik']);
           $where_id_pekerjaan = $this->input->get_post('pekerjaan_id');
           $where_disposisi_status = '10';
           $param_staf['is_proses'] = 'y';
           $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);

           /* Pekerjaan */
           $pekerjaan_status = $this->input->get_post('pekerjaan_status') + 1;

           $sql_proses = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '10' AND (is_proses != 'y' OR is_proses is null)");
           $data_proses = $sql_proses->row_array();
           $data['pekerjaan_status'] = anti_inject($pekerjaan_status);
           $data['pekerjaan_note'] = $this->input->get_post('pekerjaan_note');

           $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

           dblog('I',  $pekerjaan_id, 'Pekerjaan IFA Telah di Approved Oleh VP Peminta Jasa dengan Note <span style="color:green">' . $this->input->get_post('pekerjaan_note') . '</span>', $isi['pegawai_nik']);
           /* Pekerjaan */

           /* User */
           $data_user['pegawai_poscode'] = $isi['pegawai_direct_superior'];
           $user = $this->M_user->getUser($data_user);
           /* User */

           /* AVP */
           $sql_disposisi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '5' AND id_pekerjaan = '" . $pekerjaan_id . "'");
           $isi_disposisi = $sql_disposisi->result_array();

           /* AVP */

           /* Disposisi */
           foreach ($isi_disposisi as $key => $value) {
             $data_disposisi = [
               'pekerjaan_disposisi_id' => create_id(),
               'pekerjaan_disposisi_waktu' => date("Y-m-d H:i:s"),
               'id_user' => $value['id_user'],
               'id_pekerjaan' => $pekerjaan_id,
               'pekerjaan_disposisi_status' => $pekerjaan_status,
               'id_penanggung_jawab' => $value['id_penanggung_jawab'],
             ];
             $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

             $user_disposisi = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value['id_user']))->row_array();
             $tujuan = $user_disposisi['pegawai_nik'];
             $tujuan_nama = $user_disposisi['pegawai_nama'];
             $kalimat = "Mohon untuk melakukan PROSES pada pekerjaan ini";
             tasklog($pekerjaan_id, $data_disposisi['pekerjaan_disposisi_status'], $user_disposisi['pegawai_nik'], $kalimat, 'n');
             sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
             sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
           }
           /* Disposisi */

           /*DOKUMEN NON HPS*/
           $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND is_lama = 'n' and pekerjaan_dokumen_status = '6' AND is_hps='n' ")->result_array();

           foreach ($data_dokumen as $val_dokumen) {
            $status_dokumen = '7';

            $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();

            $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();

            $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
            $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];

            $data_dok['pekerjaan_dokumen_id_temp'] = anti_inject($val_dokumen['pekerjaan_dokumen_id']);
            $data_dok['pekerjaan_dokumen_id'] = create_id();
            $data_dok['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
            $data_dok['pekerjaan_dokumen_revisi'] = null;
            $data_dok['pekerjaan_dokumen_keterangan'] = anti_inject($val_dokumen['pekerjaan_dokumen_keterangan']);
            $data_dok['id_create'] = $isi['pegawai_nik'];
            $data_dok['is_proses'] = 'y';
            $data_dok['id_create_awal'] = $data_dokumen['id_create_awal'];
            $data_dok['pekerjaan_dokumen_waktu'] = date('Y-m-d H:i:s');
            $data_dok['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
            $this->M_pekerjaan->simpanAksiIFASama($data_dok);

            if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
              dblog('I', $pekerjaan_id, 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data_dok['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
            } else {
              $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data_dok['pekerjaan_dokumen_id'] . "'");
              dblog('I', $pekerjaan_id, 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
            }

            $param_lama['is_lama'] = 'y';
            $param_id = $val_dokumen['pekerjaan_dokumen_id'];
            $this->M_pekerjaan->editAksi($param_lama, $param_id);
          }
          /*DOKUMEN NON HPS*/

          /*Dokumen HPS*/
          $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND is_lama = 'n' and pekerjaan_dokumen_status = '6' AND is_hps='y' ")->result_array();

          foreach ($data_dokumen as $val_dokumen) {
           $status_dokumen = '7';
           $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template WHERE a.pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();

           $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
           $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];

           $data_dok['pekerjaan_dokumen_id_temp'] = anti_inject($val_dokumen['pekerjaan_dokumen_id']);
           $data_dok['pekerjaan_dokumen_id'] = create_id();
           $data_dok['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
           $data_dok['pekerjaan_dokumen_revisi'] = null;
           $data_dok['pekerjaan_dokumen_keterangan'] = anti_inject($val_dokumen['pekerjaan_dokumen_keterangan']);
           $data_dok['id_create'] = $isi['pegawai_nik'];
           $data_dok['is_proses'] = 'y';
           $data_dok['id_create_awal'] = $data_dokumen['id_create_awal'];
           $data_dok['pekerjaan_dokumen_waktu'] = date('Y-m-d H:i:s');
           $data_dok['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
           $this->M_pekerjaan->simpanAksiIFASama($data_dok);

           $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data_dok['pekerjaan_dokumen_id'] . "'");
							 /*if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
								dblog('I', $pekerjaan_id, 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data_dok['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
							} else {
								$this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '".date('Y-m-d H:i:s')."' WHERE pekerjaan_dokumen_id = '".$data['pekerjaan_dokumen_id']."' AND pekerjaan_dokumen_status >='4'");
								dblog('I', $pekerjaan_id, 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
							}*/

							$param_lama['is_lama'] = 'y';
							$param_id = $val_dokumen['pekerjaan_dokumen_id'];
							$this->M_pekerjaan->editAksi($param_lama, $param_id);
						}
						/*Dokumen HPS*/
					}
					/* Approve Pekerjaan IFA VP */

					/* Reject Pekerjaan IFA */
					public function prosesRejectIFA()
					{
						if ($this->input->get('id_user')) {
             $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'");
             $isi = $sql_user->row_array();
           } else {
             $isi = $this->session->userdata();
           }

           $pekerjaan_id = $this->input->get_post('pekerjaan_id');

           $data_cek_pekerjaan = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND pekerjaan_disposisi_status= '5'")->result_array();

           /*new*/
           $sql_revisi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN global.global_bagian_detail b ON b.id_pegawai = a.id_create_awal WHERE id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' AND pekerjaan_dokumen_status = '0' AND is_lama = 'n' AND pekerjaan_dokumen_awal = 'n'");
           $data_revisi = $sql_revisi->result_array();
           $jumlah_revisi = $sql_revisi->num_rows();

           if ($jumlah_revisi > 0) {
             $this->db->query("UPDATE dec.dec_pekerjaan_disposisi SET is_proses = null WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '6' AND id_penanggung_jawab = 'y'");
             foreach ($data_revisi as $key => $value) {
               $disposisi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '6' AND id_user IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian = '" . $value['id_bagian'] . "') ")->result_array();
               foreach ($disposisi as $value) {
                $sql_staf = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi dpd LEFT JOIN global.global_pegawai gp ON gp.pegawai_nik = dpd.id_user WHERE pekerjaan_disposisi_status = '5' AND id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian IN (SELECT bagian_id FROM global.global_bagian a LEFT JOIN global.global_bagian_detail b ON a.bagian_id = b.id_bagian WHERE b.id_pegawai = '" . $value['id_user'] . "'))");
                $dataStaf = $sql_staf->result_array();
                $param_cangung['is_proses'] = NULL;
                foreach ($dataStaf as $valueStaf) :
                  $this->db->query("UPDATE dec.dec_pekerjaan_disposisi SET is_proses = null WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user = '" . $valueStaf['id_user'] . "' AND pekerjaan_disposisi_status = '5'");
                  /* Staf */
                  /* Notif DOF */
                  $dari = $isi['pegawai_nik'];
                  $tujuan = $valueStaf['pegawai_nik'];
                  $tujuan_nama = $valueStaf['pegawai_nama'];
                  $text = "Pekerjaan anda telah di REJECT PIC Peminta Jasa";
                  sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
                  sendNotif($pekerjaan_id, $dari, $tujuan, $text);

                  $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '6' AND id_user IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_pegawai = '" . $value['id_user'] . "') ");
                endforeach;
              }
            }
          } else {
           $disposisi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '6' ")->result_array();
           foreach ($disposisi as $value) {
             $sql_staf = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi dpd LEFT JOIN global.global_pegawai gp ON gp.pegawai_nik = dpd.id_user WHERE pekerjaan_disposisi_status = '5' AND id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian IN (SELECT bagian_id FROM global.global_bagian a LEFT JOIN global.global_bagian_detail b ON a.bagian_id = b.id_bagian WHERE b.id_pegawai = '" . $value['id_user'] . "'))");
             $dataStaf = $sql_staf->result_array();
             $param_cangung['is_proses'] = NULL;
             foreach ($dataStaf as $valueStaf) :
              $this->db->query("UPDATE dec.dec_pekerjaan_disposisi SET is_proses = null WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user = '" . $valueStaf['id_user'] . "' AND pekerjaan_disposisi_status = '5'");
              /* Staf */
              /* Notif DOF */
              $dari = $isi['pegawai_nik'];
              $tujuan = $valueStaf['pegawai_nik'];
              $tujuan_nama = $valueStaf['pegawai_nama'];
              $text = "Pekerjaan anda telah di REJECT PIC Peminta Jasa";
              sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
              sendNotif($pekerjaan_id, $dari, $tujuan, $text);

              $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '6' ");

              $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status IN ('8', '9', '10') AND id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND is_cc is null");
            endforeach;
          }
        }

        $this->db->query("UPDATE dec.dec_pekerjaan SET pekerjaan_status ='5',pekerjaan_note = '".$this->input->get_post('pekerjaan_note')."' WHERE pekerjaan_id = '" . $this->input->get('pekerjaan_id') . "'");
        /* Pekerjaan */

        /*new*/

        $data_vp = $this->db->select('*')->from('dec.dec_pekerjaan_disposisi a')->where(array('id_pekerjaan' => $this->input->get_post('pekerjaan_id'), 'pekerjaan_disposisi_status' => '7'))->get()->row_array();

        /*delete disposisi vp*/
        $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '7' AND id_user = '" . $data_vp['id_user'] . "' AND id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "'");

        /* Pekerjaan */
        /*delete disposisi pic*/
        $pekerjaan_id = $this->input->get_post('pekerjaan_id');
        if ($pekerjaan_id) {
          $param['pekerjaan_status'] = '5';
          $this->M_pekerjaan->updatePekerjaan($param, $pekerjaan_id);

          $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '8' AND id_user = '" . $isi['pegawai_nik'] . "' AND id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND is_cc is null");

          dblog('I',  $pekerjaan_id, 'Pekerjaan telah diajukan revisi oleh User dengan Alasan <span style="color:red">' . $this->input->get_post('pekerjaan_note') . '</span>', $isi['pegawai_nik']);
        }
        /* Pekerjaan */
      }
      /* Reject Pekerjaan IFA */

      /* Aksi Approve / Reject Dokumen */
      public function simpanAksi()
      {
        if ($this->input->get('id_user')) {
         $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'");
         $isi = $sql_user->row_array();
       } else {
         $isi = $this->session->userdata();
       }

       $dokumen_status = $this->db->query("SELECT pekerjaan_dokumen_status FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->post('pekerjaan_dokumen_id') . "' ")->row_array();
       $dokumen_statusnya = $dokumen_status['pekerjaan_dokumen_status'] + 1;

       if (isset($_FILES['pekerjaan_dokumen_file'])) {
         $temp = "./document/";
         if (!file_exists($temp)) mkdir($temp);
         $fileupload      = $_FILES['pekerjaan_dokumen_file']['tmp_name'];
         $ImageName       = $_FILES['pekerjaan_dokumen_file']['name'];
         $ImageType       = $_FILES['pekerjaan_dokumen_file']['type'];
         if (!empty($fileupload)) {
           $Extension    = array("jpeg", "jpg", "png", "bmp", "gif", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf");
           $acak           = rand(11111111, 99999999);
           $ImageExt       = substr($ImageName, strrpos($ImageName, '.'));
           $ImageExt       = str_replace('.', '', $ImageExt);
           $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
           $NewImageName   = str_replace(' ', '', $this->input->get_post('pekerjaan_dokumen_id') . '_' . date('ymdhis') . '_' . $acak . '.' . $ImageExt);

           if (in_array($ImageExt, $Extension)) {
            move_uploaded_file($_FILES["pekerjaan_dokumen_file"]["tmp_name"], $temp . $NewImageName); /* Menyimpan file*/
          }
        }
      } else {
        $dokumen_edit = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->get_post('pekerjaan_dokumen_id') . "'")->row_array();
        if ($this->input->post('is_change') == 'y') {
          $NewImageName = $dokumen_statusnya . '_' . $dokumen_edit['pekerjaan_dokumen_file'];
        } else {
          $NewImageName = null;
        }
      }

      /*cek dokumen statusnya dari input status*/

      $data_status = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->get_post('pekerjaan_dokumen_id') . "'")->row_array();

      $data_ifa = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_dokumen WHERE id_dokumen_awal = '" . $data_status['id_dokumen_awal'] . "' AND pekerjaan_dokumen_status ='7' AND is_update_ifa = 'y' ")->row_array();

      $status_dokumen = ($this->input->get_post('pekerjaan_dokumen_status') == 'n') ? '0' : $data_status['pekerjaan_dokumen_status'] + 1;
      $status_dokumen_revisi = null;

      /*    ketika dokumen reject*/
      $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->post('pekerjaan_dokumen_id') . "'")->row_array();


      if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
       $data_customer = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status IN('8','9','10') AND id_user = '" . $isi['pegawai_nik'] . "' AND id_pekerjaan = '" . $data_status['id_pekerjaan'] . "'")->num_rows();
       if ($data_customer > 0) {
         if ($nomor_revisi['nomor_revisi'] == '') {
          $nomor_revisinya = 0;
        } else {
          $nomor_revisinya = $nomor_revisi['nomor_revisi'];
        }
        $nomor_revisi_baru = ($nomor_revisinya + 1);
      } else {
       $nomor_revisi_baru = null;
     }
   } else {
     $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];
   }

   $data_dokumen = $this->db->select('*')->from('dec.dec_pekerjaan_dokumen a')->join('dec.dec_pekerjaan_template b', 'b.pekerjaan_template_id=a.id_pekerjaan_template', 'left')->where(array('pekerjaan_dokumen_id' => $this->input->post('pekerjaan_dokumen_id')))->get()->row_array();

   if (($data_status['is_proses'] == null || $data_status['is_proses'] == '') && $this->input->get_post('pekerjaan_dokumen_status') == 'y') {
     $proses = 'y';
   } else if ($data_status['is_proses'] == 'y') {
     $proses = 'a';
   } else {
     $proses = 'y';
   }
   /* Insert */
   if ($NewImageName == null) {
     $data['pekerjaan_dokumen_id_temp'] = anti_inject($this->input->post('pekerjaan_dokumen_id'));
     $data['pekerjaan_dokumen_id'] = anti_inject(create_id());
     $data['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
     $data['pekerjaan_dokumen_revisi'] = ($nomor_revisi_baru);
     $data['pekerjaan_dokumen_keterangan'] = ($this->input->post('pekerjaan_dokumen_keterangan') != '') ? $this->input->post('pekerjaan_dokumen_keterangan') : $data_dokumen['pekerjaan_dokumen_keterangan'];
     $data['id_create'] = $isi['pegawai_nik'];
     $data['is_proses'] = $proses;
     $data['id_create_awal'] = $data_status['id_create_awal'];
     $data['pekerjaan_dokumen_jumlah'] = $data_status['pekerjaan_dokumen_jumlah'];
     $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
     $this->M_pekerjaan->simpanAksiSama($data);

     if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
       if ($data_status['pekerjaan_dokumen_status'] == '9') {
        $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET revisi_ifc = 'y' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "'");
      }
      dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
    } else {
     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
   }
 } else {
   $data['pekerjaan_dokumen_id_temp'] = anti_inject($this->input->post('pekerjaan_dokumen_id'));
   $data['pekerjaan_dokumen_id'] = anti_inject(create_id());
   $data['pekerjaan_dokumen_file'] = $NewImageName;
   $data['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
   $data['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
   $data['pekerjaan_dokumen_keterangan'] = ($this->input->post('pekerjaan_dokumen_keterangan') != '') ? $this->input->post('pekerjaan_dokumen_keterangan') : $data_dokumen['pekerjaan_dokumen_keterangan'];
   $data['id_create'] = $isi['pegawai_nik'];
   $data['is_proses'] = $proses;
   $data['id_create_awal'] = $data_status['id_create_awal'];
   $data['pekerjaan_dokumen_jumlah'] = $data_status['pekerjaan_dokumen_jumlah'];
   $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
   $this->M_pekerjaan->simpanAksi($data);

   if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
     if ($data_status['pekerjaan_dokumen_status'] == '9') {
      $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET revisi_ifc = 'y' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "'");
    }
    dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
  } else {
   $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");
   dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
 }
}
/* Insert */

if ($this->input->post('pekerjaan_dokumen_id')) {
 $data_edit['is_lama'] = 'y';
 $this->M_pekerjaan->editAksi($data_edit, $this->input->post('pekerjaan_dokumen_id'));
}
}
/* Aksi Approve / Reject Dokumen */

/* Aksi Approve / Reject Dokumen CC*/
public function simpanAksiCC()
{
	if (isset($_GET['id_user'])) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $isi = $sql_user->row_array();
  } else {
    $isi = $this->session->userdata();
  }

  if (isset($_FILES['pekerjaan_dokumen_file'])) {
    $temp = "./document/";
    if (!file_exists($temp)) mkdir($temp);

    $fileupload      = $_FILES['pekerjaan_dokumen_file']['tmp_name'];
    $ImageName       = $_FILES['pekerjaan_dokumen_file']['name'];
    $ImageType       = $_FILES['pekerjaan_dokumen_file']['type'];

    if (!empty($fileupload)) {
      $Extension    = array("jpeg", "jpg", "png", "bmp", "gif", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf");
      $acak           = rand(11111111, 99999999);
      $ImageExt       = substr($ImageName, strrpos($ImageName, '.'));
      $ImageExt       = str_replace('.', '', $ImageExt);
      $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
      $NewImageName   = str_replace(' ', '', $this->input->get_post('pekerjaan_dokumen_id') . '_' . date('ymdhis') . '_' . $acak . '.' . $ImageExt);

      if (in_array($ImageExt, $Extension)) {
       move_uploaded_file($_FILES["pekerjaan_dokumen_file"]["tmp_name"], $temp . $NewImageName); /* Menyimpan file*/
     }
     $note = "Data Berhasil Disimpan";
   } else {
     $note = "Data Gagal Disimpan";
   }
   echo $note;
 } else {
   $NewImageName = null;
 }

 $dataku['pekerjaan_dokumen_status'] = $this->input->post('pekerjaan_dokumen_status');

 $sql_status = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->get_post('pekerjaan_dokumen_id') . "'");
 $data_status = $sql_status->row_array();

 $status_dokumen_cc = ($this->input->get_post('pekerjaan_dokumen_status') == 'n') ? '0' : $data_status['pekerjaan_dokumen_status_review'] + 1;
 /*    print_r($status_dokumen_cc);*/

 $data_dokumen = $this->db->select('*')->from('dec.dec_pekerjaan_dokumen a')->join('dec.dec_pekerjaan_template b', 'b.pekerjaan_template_id=a.id_pekerjaan_template', 'left')->where(array('pekerjaan_dokumen_id' => $this->input->post('pekerjaan_dokumen_id')))->get()->row_array();


 if ($data_status['is_review'] == null || $data_status['is_review'] == '') {
   $review = 'y';
 }

 /*cek dokumen statusnya dari input status*/
 $dokumen_status = ($this->input->get_post('pekerjaan_dokumen_status') == 'n') ? '0' : '4';

 /* Insert */
 if ($NewImageName == null) {
   $data['pekerjaan_dokumen_id_temp'] = anti_inject($this->input->post('pekerjaan_dokumen_id'));
   $data['pekerjaan_dokumen_id'] = create_id();
   $data['pekerjaan_dokumen_status_review'] = anti_inject($status_dokumen_cc);
   $data['pekerjaan_dokumen_keterangan'] = anti_inject($this->input->post('pekerjaan_dokumen_keterangan'));
   $data['id_create'] = $isi['pegawai_nik'];
   $data['is_review'] = $review;
   /*      $data['is_proses'] = 'y';*/
   $data['id_create_awal'] = $data_status['id_create_awal'];
   $this->M_pekerjaan->simpanAksiSamaCC($data);

   if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
   } else {
     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
   }
 } else {
   $data['pekerjaan_dokumen_id_temp'] = anti_inject($this->input->post('pekerjaan_dokumen_id'));
   $data['pekerjaan_dokumen_id'] = create_id();
   $data['pekerjaan_dokumen_file'] = $NewImageName;

   $data['pekerjaan_dokumen_status_review'] = anti_inject($status_dokumen_cc);
   $data['pekerjaan_dokumen_keterangan'] = anti_inject($this->input->post('pekerjaan_dokumen_keterangan'));
   $data['id_create'] = $isi['pegawai_nik'];
   $data['is_review'] = $review;
   /*      $data['is_proses'] = 'y';*/
   $data['id_create_awal'] = $data_status['id_create_awal'];
   $this->M_pekerjaan->simpanAksiCC($data);

   if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
   } else {
     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
   }
 }
 /* Insert */
 /*cek apakah direvisi*/
 if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
   /*ambil urutan revisi sebelumnya dari dokumen di pekerjaan tersebut*/
   $data_revisi = $this->M_pekerjaan->getRevisiNomor($data);
   $data_revisi_isi['pekerjaan_dokumen_revisi'] = $data_revisi['pekerjaan_dokumen_revisi'] + 1;
   /*revisi nomor ke doc yang direvisikan*/
   $this->M_pekerjaan->updateRevisiNomor($data_revisi_isi, $data['pekerjaan_dokumen_id']);
 }

 if ($data['pekerjaan_dokumen_id_temp']) {
   $data_edit['is_lama'] = 'y';
   $this->M_pekerjaan->editAksi($data_edit, $data['pekerjaan_dokumen_id_temp']);
 }
}
/* Aksi Approve / Reject Dokumen CC*/

/* Aksi Send Ulang Staf */
public function simpanAksiStaf()
{
	if (isset($_GET['id_user'])) {
    $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $isi = $sql_isi->row_array();
  } else {
    $isi = $this->session->userdata();
  }

  if (isset($_FILES['pekerjaan_dokumen_file'])) {
    $temp = "./document/";
    if (!file_exists($temp)) mkdir($temp);

    $fileupload      = $_FILES['pekerjaan_dokumen_file']['tmp_name'];
    $ImageName       = $_FILES['pekerjaan_dokumen_file']['name'];
    $ImageType       = $_FILES['pekerjaan_dokumen_file']['type'];

    if (!empty($fileupload)) {
      $Extension    = array("jpeg", "jpg", "png", "bmp", "gif", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf");
      $acak           = rand(11111111, 99999999);
      $ImageExt       = substr($ImageName, strrpos($ImageName, '.'));
      $ImageExt       = str_replace('.', '', $ImageExt);
      $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
      $NewImageName   = str_replace(' ', '', $this->input->get_post('pekerjaan_dokumen_id') . '_' . date('ymdhis') . '_' . $acak . '.' . $ImageExt);

      if (in_array($ImageExt, $Extension)) {
       move_uploaded_file($_FILES["pekerjaan_dokumen_file"]["tmp_name"], $temp . $NewImageName); /* Menyimpan file*/
     }

     move_uploaded_file($_FILES["pekerjaan_dokumen_file"]["tmp_name"], $temp . $NewImageName);  /*Menyimpan file*/

     $note = "Data Berhasil Disimpan";
   } else {
     $note = "Data Gagal Disimpan";
   }
   echo $note;
 } else {
   $NewImageName = null;
 }

 /*cek dokumen statusnya dari input status*/
 $dataku['pekerjaan_dokumen_status'] = $this->input->post('pekerjaan_dokumen_status');

 $sql_status = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->get_post('pekerjaan_dokumen_id') . "'");
 $data_status = $sql_status->row_array();

 $status_dokumen = ($this->input->get_post('pekerjaan_dokumen_status') == 'n') ? '0' : $data_status['pekerjaan_dokumen_status'] + 2;

 $data_dokumen = $this->db->select('*')->from('dec.dec_pekerjaan_dokumen a')->join('dec.dec_pekerjaan_template b', 'b.pekerjaan_template_id=a.id_pekerjaan_template', 'left')->where(array('pekerjaan_dokumen_id' => $this->input->post('pekerjaan_dokumen_id')))->get()->row_array();

 if ($data_status['is_proses'] == null || $data_status['is_proses'] == '') {
   $proses = 'y';
 } else if ($data_status['is_proses'] == 'y') {
   $proses = 'a';
 } else {
   $proses = 'y';
 }

 /* Insert */
 if ($NewImageName == null) {
   $data['pekerjaan_dokumen_id_temp'] = anti_inject($this->input->post('pekerjaan_dokumen_id'));
   $data['pekerjaan_dokumen_id'] = anti_inject(create_id());
   $data['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
   $data['pekerjaan_dokumen_keterangan'] = anti_inject($this->input->post('pekerjaan_dokumen_keterangan'));
   $data['id_create'] = $isi['pegawai_nik'];
   $data['is_proses'] = $proses;
   $data['id_create_awal'] = $data_status['id_create_awal'];
   $this->M_pekerjaan->simpanAksiSama($data);
   if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
   } else {
     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
   }
 } else {
   $data['pekerjaan_dokumen_id_temp'] = anti_inject($this->input->post('pekerjaan_dokumen_id'));
   $data['pekerjaan_dokumen_id'] = anti_inject(create_id());
   $data['pekerjaan_dokumen_file'] = $NewImageName;
   $data['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
   $data['pekerjaan_dokumen_keterangan'] = anti_inject($this->input->post('pekerjaan_dokumen_keterangan'));
   $data['id_create'] = $isi['pegawai_nik'];
   $data['is_proses'] = $proses;
   $data['id_create_awal'] = $data_status['id_create_awal'];
   $this->M_pekerjaan->simpanAksi($data);
   if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
   } else {
     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
   }
 }
 /* Insert */

 if ($data['pekerjaan_dokumen_id_temp']) {
   $data_edit['is_lama'] = 'y';
   $this->M_pekerjaan->editAksi($data_edit, $data['pekerjaan_dokumen_id_temp']);
 }
}
/* Aksi Send Ulang Staf */

/* Aksi Approve / Reject Dokumen IFA */
public function simpanAksiIFA()
{
	/*cek apakah ada id user*/
	if ($this->input->get('id_user')) {
    $isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'")->row_array();
  } else {
    $isi = $this->session->userdata();
  }

  $path = "./document/";
  if (!file_exists($path)) mkdir($path);
  /*jika ada file diupload*/
  if (isset($_FILES['pekerjaan_dokumen_file'])) {
    $fileupload      = $_FILES['pekerjaan_dokumen_file']['tmp_name'];
    $ImageName       = $_FILES['pekerjaan_dokumen_file']['name'];
    $ImageType       = $_FILES['pekerjaan_dokumen_file']['type'];
    if (!empty($fileupload)) {
      $Extension    = array("jpeg", "jpg", "png", "bmp", "gif", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf");
      $acak           = rand(11111111, 99999999);
      $ImageExt       = substr($ImageName, strrpos($ImageName, '.'));
      $ImageExt       = str_replace('.', '', $ImageExt);
      $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
      $NewImageName   = str_replace(' ', '', $this->input->get_post('pekerjaan_dokumen_id') . '_' . date('ymdhis') . '_' . $acak . '.' . $ImageExt);
      if (in_array($ImageExt, $Extension)) {
       move_uploaded_file($_FILES["pekerjaan_dokumen_file"]["tmp_name"], $path . $NewImageName); /* Menyimpan file*/
     }
   }
   /*jika ga ada file diupload*/
 } else {
   $dokumen_status = $this->db->query("SELECT pekerjaan_dokumen_status FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->post('pekerjaan_dokumen_id') . "' ")->row_array();
   $dokumen_statusnya = $dokumen_status['pekerjaan_dokumen_status'] + 1;

   $dokumen_edit = $this->db->query("SELECT pekerjaan_dokumen_file FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->get_post('pekerjaan_dokumen_id') . "'")->row_array();
   /*jika ada view dokumen yang dikasih sesuatu*/
   if ($this->input->post('is_change') == 'y') {
     $NewImageName = $dokumen_statusnya . '_' . $dokumen_edit['pekerjaan_dokumen_file'];
   } else {
     $NewImageName = null;
   }
 }


 /*status approve /reject*/
 $sql_status = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->get_post('pekerjaan_dokumen_id') . "'");
 $data_status = $sql_status->row_array();
 $status_dokumen = ($this->input->get_post('pekerjaan_dokumen_status') == 'n') ? '0' : $data_status['pekerjaan_dokumen_status'] + 1;

 $data_dokumen = $this->db->select('*')->from('dec.dec_pekerjaan_dokumen a')->join('dec.dec_pekerjaan_template b', 'b.pekerjaan_template_id=a.id_pekerjaan_template', 'left')->where(array('pekerjaan_dokumen_id' => $this->input->post('pekerjaan_dokumen_id')))->get()->row_array();
 if ($this->input->get_post('pekerjaan_dokumen_status') != 'n') {
   if ($this->input->get_post('pekerjaan_dokumen_status_nomor') == '4') {
     $proses = 'ifaavp';
   } else if ($this->input->get_post('pekerjaan_dokumen_status_nomor') == '5') {
     $proses = 'ifavp';
   } else {
     $proses = '-';
   }
 } else {
   $proses = '';
 }

 /*penomoran revisi*/
 $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->post('pekerjaan_dokumen_id') . "'")->row_array();

 if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
   if ($nomor_revisi['nomor_revisi'] == '') {
     $nomor_revisinya = 0;
   } else {
     $nomor_revisinya = $nomor_revisi['nomor_revisi'];
   }
   $nomor_revisi_baru = ($nomor_revisinya + 1);
 } else {
   $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];
 }

 /* Insert */
 if ($NewImageName == null) {
   $data['pekerjaan_dokumen_id_temp'] = anti_inject($this->input->post('pekerjaan_dokumen_id'));
   $data['pekerjaan_dokumen_id'] = create_id();
   $data['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
   $data['pekerjaan_dokumen_revisi'] = anti_inject($nomor_revisi_baru);
   $data['pekerjaan_dokumen_keterangan'] = ($this->input->post('pekerjaan_dokumen_keterangan') != '') ? anti_inject($this->input->post('pekerjaan_dokumen_keterangan')) : $data_dokumen['pekerjaan_dokumen_keterangan'];
   $data['id_create'] = $isi['pegawai_nik'];
   $data['is_proses'] = $proses;
   $data['id_create_awal'] = $data_status['id_create_awal'];
   $data['pekerjaan_dokumen_waktu'] = date('Y-m-d H:i:s');
   $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
   $this->M_pekerjaan->simpanAksiIFASama($data);

   if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
   } else {
     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
   }
 } else {
   $data['pekerjaan_dokumen_id_temp'] = anti_inject($this->input->post('pekerjaan_dokumen_id'));
   $data['pekerjaan_dokumen_id'] = create_id();
   $data['pekerjaan_dokumen_file'] = $NewImageName;
   $data['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
   $data['pekerjaan_dokumen_revisi'] = anti_inject($nomor_revisi_baru);
   $data['pekerjaan_dokumen_keterangan'] = ($this->input->post('pekerjaan_dokumen_keterangan') != '') ? anti_inject($this->input->post('pekerjaan_dokumen_keterangan')) : $data_dokumen['pekerjaan_dokumen_keterangan'];
   $data['id_create'] = $isi['pegawai_nik'];
   $data['is_proses'] = $proses;
   $data['id_create_awal'] = $data_status['id_create_awal'];
   $data['pekerjaan_dokumen_waktu'] = date('Y-m-d H:i:s');
   $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
   $this->M_pekerjaan->simpanAksiIFA($data);

   if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
   } else {
     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
   }
 }
 /* Insert */
 /*update status dokumen lama*/
 if ($data['pekerjaan_dokumen_id_temp']) {
   $data_lama['is_lama'] = 'y';
   $this->M_pekerjaan->editAksi($data_lama, $data['pekerjaan_dokumen_id_temp']);
 }
}
/* Aksi Approve / Reject Dokumen IFA */

/* Aksi Approve / Reject Dokumen IFA CC*/
public function simpanAksiIFACC()
{
	if (isset($_GET['id_user'])) {
    $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $isi = $sql_isi->row_array();
  } else {
    $isi = $this->session->userdata();
  }

  if (isset($_FILES['pekerjaan_dokumen_file'])) {
    $temp = "./document/";
    if (!file_exists($temp)) mkdir($temp);

    $fileupload      = $_FILES['pekerjaan_dokumen_file']['tmp_name'];
    $ImageName       = $_FILES['pekerjaan_dokumen_file']['name'];
    $ImageType       = $_FILES['pekerjaan_dokumen_file']['type'];

    if (!empty($fileupload)) {
      $Extension    = array("jpeg", "jpg", "png", "bmp", "gif", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf");
      $acak           = rand(11111111, 99999999);
      $ImageExt       = substr($ImageName, strrpos($ImageName, '.'));
      $ImageExt       = str_replace('.', '', $ImageExt);
      $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
      $NewImageName   = str_replace(' ', '', $this->input->get_post('pekerjaan_dokumen_id') . '_' . date('ymdhis') . '_' . $acak . '.' . $ImageExt);

      if (in_array($ImageExt, $Extension)) {
       move_uploaded_file($_FILES["pekerjaan_dokumen_file"]["tmp_name"], $temp . $NewImageName); /* Menyimpan file*/
     }
     $note = "Data Berhasil Disimpan";
   } else {
     $note = "Data Gagal Disimpan";
   }
   echo $note;
 } else {
   $NewImageName = null;
 }

 $dataku['pekerjaan_dokumen_status'] = $this->input->post('pekerjaan_dokumen_status');

 $sql_status = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->get_post('pekerjaan_dokumen_id') . "'");
 $data_status = $sql_status->row_array();

 $status_dokumen = ($this->input->get_post('pekerjaan_dokumen_status') == 'n') ? '0' : $data_status['pekerjaan_dokumen_status'] + 1;

 $status_dokumen_cc = ($this->input->get_post('pekerjaan_dokumen_status') == 'n') ? '0' : $data_status['pekerjaan_dokumen_status_review'] + 1;

 $data_dokumen = $this->db->select('*')->from('dec.dec_pekerjaan_dokumen a')->join('dec.dec_pekerjaan_template b', 'b.pekerjaan_template_id=a.id_pekerjaan_template', 'left')->where(array('pekerjaan_dokumen_id' => $this->input->post('pekerjaan_dokumen_id')))->get()->row_array();

 if ($data_status['is_proses'] == null || $data_status['is_proses'] == '') {
   $proses = 'y';
 } else if ($data_status['is_proses'] == 'y') {
   $proses = 'a';
 } else if ($data_status['is_proses'] == 'a') {
   $proses = 'i';
 }

 if ($data_status['is_review'] == null || $data_status['is_review'] == '') {
   $review = 'y';
 }

 /*cek dokumen statusnya dari input status*/
 $dokumen_status = ($this->input->get_post('pekerjaan_dokumen_status') == 'n') ? '0' : '4';

 /* Insert */
 if ($NewImageName == null) {
   $data['pekerjaan_dokumen_id_temp'] = anti_inject($this->input->post('pekerjaan_dokumen_id'));
   $data['pekerjaan_dokumen_id'] = create_id();
   $data['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
   $data['pekerjaan_dokumen_status_review'] = anti_inject($status_dokumen_cc);
   $data['pekerjaan_dokumen_keterangan'] = anti_inject($this->input->post('pekerjaan_dokumen_keterangan'));
   $data['id_create'] = $isi['pegawai_nik'];
   $data['is_review'] = $review;
   /*      $data['is_proses'] = 'y';*/
   $data['id_create_awal'] = $data_status['id_create_awal'];
   $this->M_pekerjaan->simpanAksiSamaCC($data);
   if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
   } else {
     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
   }
 } else {
   $data['pekerjaan_dokumen_id_temp'] = anti_inject($this->input->post('pekerjaan_dokumen_id'));
   $data['pekerjaan_dokumen_id'] = create_id();
   $data['pekerjaan_dokumen_file'] = $NewImageName;
   $data['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
   $data['pekerjaan_dokumen_status_review'] = anti_inject($status_dokumen_cc);
   $data['pekerjaan_dokumen_keterangan'] = anti_inject($this->input->post('pekerjaan_dokumen_keterangan'));
   $data['id_create'] = $isi['pegawai_nik'];
   $data['is_review'] = $review;
   /*      $data['is_proses'] = 'y';*/
   $data['id_create_awal'] = $data_status['id_create_awal'];
   $this->M_pekerjaan->simpanAksiCC($data);
   if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawa_nik']);
   } else {
     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
   }
 }
 /* Insert */
 /*cek apakah direvisi*/
 if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
   /*ambil urutan revisi sebelumnya dari dokumen di pekerjaan tersebut*/
   $data_revisi = $this->M_pekerjaan->getRevisiNomor($data);
   $data_revisi_isi['pekerjaan_dokumen_revisi'] = $data_revisi['pekerjaan_dokumen_revisi'] + 1;
   /*revisi nomor ke doc yang direvisikan*/
   $this->M_pekerjaan->updateRevisiNomor($data_revisi_isi, $data['pekerjaan_dokumen_id']);
 }

 if ($data['pekerjaan_dokumen_id_temp']) {
   $data_edit['is_lama'] = 'y';
   $this->M_pekerjaan->editAksi($data_edit, $data['pekerjaan_dokumen_id_temp']);
 }
}
/* Aksi Approve / Reject Dokumen IFA */

/* Aksi Approve / Reject Dokumen IFC */
public function simpanAksiIFC()
{
	if ($this->input->get('id_user')) {
    $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'");
    $isi = $sql_isi->row_array();
  } else {
    $isi = $this->session->userdata();
  }

  $path = "./document/";
  if (!file_exists($path)) mkdir($path);
  /*jika ada file diupload*/
  if (isset($_FILES['pekerjaan_dokumen_file'])) {
    $fileupload      = $_FILES['pekerjaan_dokumen_file']['tmp_name'];
    $ImageName       = $_FILES['pekerjaan_dokumen_file']['name'];
    $ImageType       = $_FILES['pekerjaan_dokumen_file']['type'];
    if (!empty($fileupload)) {
      $Extension    = array("jpeg", "jpg", "png", "bmp", "gif", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf");
      $acak           = rand(11111111, 99999999);
      $ImageExt       = substr($ImageName, strrpos($ImageName, '.'));
      $ImageExt       = str_replace('.', '', $ImageExt);
      $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
      $NewImageName   = str_replace(' ', '', $this->input->get_post('pekerjaan_dokumen_id') . '_' . date('ymdhis') . '_' . $acak . '.' . $ImageExt);
      if (in_array($ImageExt, $Extension)) {
       move_uploaded_file($_FILES["pekerjaan_dokumen_file"]["tmp_name"], $path . $NewImageName); /* Menyimpan file*/
     }
   }
   /*jika ga ada file diupload*/
 } else {
   $dokumen_status = $this->db->query("SELECT pekerjaan_dokumen_status FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->post('pekerjaan_dokumen_id') . "' ")->row_array();
   $dokumen_statusnya = $dokumen_status['pekerjaan_dokumen_status'] + 1;

   $dokumen_edit = $this->db->query("SELECT pekerjaan_dokumen_file FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->get_post('pekerjaan_dokumen_id') . "'")->row_array();
   /*jika ada view dokumen yang dikasih sesuatu*/
   if ($this->input->post('is_change') == 'y') {
     $NewImageName = $dokumen_statusnya . '_' . $dokumen_edit['pekerjaan_dokumen_file'];
   } else {
     $NewImageName = null;
   }
 }

 /*penomoran revisi*/
 $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->post('pekerjaan_dokumen_id') . "'")->row_array();

 if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
   if ($nomor_revisi['nomor_revisi'] == '') {
     $nomor_revisinya = 0;
   } else {
     $nomor_revisinya = $nomor_revisi['nomor_revisi'];
   }
   $nomor_revisi_baru = ($nomor_revisinya + 1);
 } else {
   $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];
 }

 /*cek dokumen statusnya dari input status*/
 $data_status = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->get_post('pekerjaan_dokumen_id') . "'")->row_array();

 $status_dokumen = ($this->input->get_post('pekerjaan_dokumen_status') == 'n') ? '0' : $data_status['pekerjaan_dokumen_status'] + 1;

 $data_dokumen = $this->db->select('*')->from('dec.dec_pekerjaan_dokumen a')->join('dec.dec_pekerjaan_template b', 'b.pekerjaan_template_id=a.id_pekerjaan_template', 'left')->where(array('pekerjaan_dokumen_id' => $this->input->post('pekerjaan_dokumen_id')))->get()->row_array();

 if ($data_status['is_proses'] == null || $data_status['is_proses'] == '') {
   $proses = 'y';
 } else if ($data_status['is_proses'] == 'y') {
   $proses = 'a';
 }

 /* Insert */
 if ($NewImageName == null) {
   $data['pekerjaan_dokumen_id_temp'] = anti_inject($this->input->post('pekerjaan_dokumen_id'));
   $data['pekerjaan_dokumen_id'] = create_id();
   $data['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
   $data['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
   $data['pekerjaan_dokumen_keterangan'] = anti_inject($this->input->post('pekerjaan_dokumen_keterangan'));
   $data['id_create'] = $isi['pegawai_nik'];
   $data['is_proses'] = 'y';
   $data['id_create_awal'] = $data_status['id_create_awal'];
   $data['pekerjaan_dokumen_jumlah'] = $data_status['pekerjaan_dokumen_jumlah'];
   $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
   $this->M_pekerjaan->simpanAksiSama($data);
   if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
   } else {
     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
   }
 } else {
   $data['pekerjaan_dokumen_id_temp'] = anti_inject($this->input->post('pekerjaan_dokumen_id'));
   $data['pekerjaan_dokumen_id'] = create_id();
   $data['pekerjaan_dokumen_file'] = $NewImageName;
   $data['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
   $data['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
   $data['pekerjaan_dokumen_keterangan'] = anti_inject($this->input->post('pekerjaan_dokumen_keterangan'));
   $data['id_create'] = $isi['pegawai_nik'];
   $data['is_proses'] = 'y';
   $data['id_create_awal'] = $data_status['id_create_awal'];
   $data['pekerjaan_dokumen_jumlah'] = $data_status['pekerjaan_dokumen_jumlah'];
   $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
   $this->M_pekerjaan->simpanAksi($data);
   if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
   } else {
     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
   }
 }
 /* Insert */


 /*cek apakah direvisi*/
 if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
   /*ambil urutan revisi sebelumnya dari dokumen di pekerjaan tersebut*/
   $nomor_revisi = $this->M_pekerjaan->getRevisiNomor($data);
   if ($nomor_revisi['max'] == '') {
     $nomor_revisinya = 0;
   } else {
     $nomor_revisinya = $nomor_revisi['max'];
   }

   $data_revisi_isi['pekerjaan_dokumen_revisi'] = $nomor_revisinya + 1;

   /*revisi nomor ke doc yang direvisikan*/
   $this->M_pekerjaan->updateRevisiNomor($data_revisi_isi, $data['pekerjaan_dokumen_id']);
 }

 if ($data['pekerjaan_dokumen_id_temp']) {
   $data_edit['is_lama'] = 'y';
   $this->M_pekerjaan->editAksi($data_edit, $data['pekerjaan_dokumen_id_temp']);
 }
}
/* Aksi Approve / Reject Dokumen IFC */

/* Aksi Approve / Reject Dokumen IFC */
public function simpanAksiTransmital()
{
	if ($this->input->get('id_user')) {
    $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'");
    $isi = $sql_isi->row_array();
  } else {
    $isi = $this->session->userdata();
  }

  $path = "./document/";
  if (!file_exists($path)) mkdir($path);
  /*jika ada file diupload*/
  if (isset($_FILES['pekerjaan_dokumen_file'])) {
    $fileupload      = $_FILES['pekerjaan_dokumen_file']['tmp_name'];
    $ImageName       = $_FILES['pekerjaan_dokumen_file']['name'];
    $ImageType       = $_FILES['pekerjaan_dokumen_file']['type'];
    if (!empty($fileupload)) {
      $Extension    = array("jpeg", "jpg", "png", "bmp", "gif", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf");
      $acak           = rand(11111111, 99999999);
      $ImageExt       = substr($ImageName, strrpos($ImageName, '.'));
      $ImageExt       = str_replace('.', '', $ImageExt);
      $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
      $NewImageName   = str_replace(' ', '', $this->input->get_post('pekerjaan_dokumen_id') . '_' . date('ymdhis') . '_' . $acak . '.' . $ImageExt);
      if (in_array($ImageExt, $Extension)) {
       move_uploaded_file($_FILES["pekerjaan_dokumen_file"]["tmp_name"], $path . $NewImageName); /* Menyimpan file*/
     }
   }
   /*jika ga ada file diupload*/
 } else {
   $dokumen_status = $this->db->query("SELECT pekerjaan_dokumen_status FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->post('pekerjaan_dokumen_id') . "' ")->row_array();
   $dokumen_statusnya = $dokumen_status['pekerjaan_dokumen_status'] + 1;

   $dokumen_edit = $this->db->query("SELECT pekerjaan_dokumen_file FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->get_post('pekerjaan_dokumen_id') . "'")->row_array();
   /*jika ada view dokumen yang dikasih sesuatu*/
   if ($this->input->post('is_change') == 'y') {
     $NewImageName = $dokumen_statusnya . '_' . $dokumen_edit['pekerjaan_dokumen_file'];
   } else {
     $NewImageName = null;
   }
 }

 /*penomoran revisi*/
 $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->post('pekerjaan_dokumen_id') . "'")->row_array();

 if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
   if ($nomor_revisi['nomor_revisi'] == '') {
     $nomor_revisinya = 0;
   } else {
     $nomor_revisinya = $nomor_revisi['nomor_revisi'];
   }
   $nomor_revisi_baru = ($nomor_revisinya + 1);
 } else {
   $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];
 }

 /*cek dokumen statusnya dari input status*/
 $data_status = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->get_post('pekerjaan_dokumen_id') . "'")->row_array();

 $status_dokumen = ($this->input->get_post('pekerjaan_dokumen_status') == 'n') ? '0' : $data_status['pekerjaan_dokumen_status'] + 1;

 $data_dokumen = $this->db->select('*')->from('dec.dec_pekerjaan_dokumen a')->join('dec.dec_pekerjaan_template b', 'b.pekerjaan_template_id=a.id_pekerjaan_template', 'left')->where(array('pekerjaan_dokumen_id' => $this->input->post('pekerjaan_dokumen_id')))->get()->row_array();

 if ($data_status['is_proses'] == null || $data_status['is_proses'] == '') {
   $proses = 'y';
 } else if ($data_status['is_proses'] == 'y') {
   $proses = 'a';
 }

 /* Insert */
 if ($NewImageName == null) {
   $data['pekerjaan_dokumen_id_temp'] = anti_inject($this->input->post('pekerjaan_dokumen_id'));
   $data['pekerjaan_dokumen_id'] = create_id();
   $data['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
   $data['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
   $data['pekerjaan_dokumen_keterangan'] = anti_inject($this->input->post('pekerjaan_dokumen_keterangan'));
   $data['id_create'] = $isi['pegawai_nik'];
   $data['is_proses'] = 'dta';
   $data['id_create_awal'] = $data_status['id_create_awal'];
   $data['pekerjaan_dokumen_jumlah'] = $data_status['pekerjaan_dokumen_jumlah'];
   $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
   $this->M_pekerjaan->simpanAksiSama($data);
   if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
   } else {
     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
   }
 } else {
   $data['pekerjaan_dokumen_id_temp'] = anti_inject($this->input->post('pekerjaan_dokumen_id'));
   $data['pekerjaan_dokumen_id'] = create_id();
   $data['pekerjaan_dokumen_file'] = $NewImageName;
   $data['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
   $data['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
   $data['pekerjaan_dokumen_keterangan'] = anti_inject($this->input->post('pekerjaan_dokumen_keterangan'));
   $data['id_create'] = $isi['pegawai_nik'];
   $data['is_proses'] = 'dta';
   $data['id_create_awal'] = $data_status['id_create_awal'];
   $data['pekerjaan_dokumen_jumlah'] = $data_status['pekerjaan_dokumen_jumlah'];
   $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
   $this->M_pekerjaan->simpanAksi($data);
   if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
   } else {
     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
   }
 }
 /* Insert */


 /*cek apakah direvisi*/
 if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
   /*ambil urutan revisi sebelumnya dari dokumen di pekerjaan tersebut*/
   $nomor_revisi = $this->M_pekerjaan->getRevisiNomor($data);
   if ($nomor_revisi['max'] == '') {
     $nomor_revisinya = 0;
   } else {
     $nomor_revisinya = $nomor_revisi['max'];
   }

   $data_revisi_isi['pekerjaan_dokumen_revisi'] = $nomor_revisinya + 1;

   /*revisi nomor ke doc yang direvisikan*/
   $this->M_pekerjaan->updateRevisiNomor($data_revisi_isi, $data['pekerjaan_dokumen_id']);
 }

 if ($data['pekerjaan_dokumen_id_temp']) {
   $data_edit['is_lama'] = 'y';
   $this->M_pekerjaan->editAksi($data_edit, $data['pekerjaan_dokumen_id_temp']);
 }
}
/* Aksi Approve / Reject Dokumen IFC */

/* Aksi Approve / Reject Dokumen IFC */
public function simpanAksiTransmitalCangun()
{
	if ($this->input->get('id_user')) {
    $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'");
    $isi = $sql_isi->row_array();
  } else {
    $isi = $this->session->userdata();
  }

  $path = "./document/";
  if (!file_exists($path)) mkdir($path);
  /*jika ada file diupload*/
  if (isset($_FILES['pekerjaan_dokumen_file'])) {
    $fileupload      = $_FILES['pekerjaan_dokumen_file']['tmp_name'];
    $ImageName       = $_FILES['pekerjaan_dokumen_file']['name'];
    $ImageType       = $_FILES['pekerjaan_dokumen_file']['type'];
    if (!empty($fileupload)) {
      $Extension    = array("jpeg", "jpg", "png", "bmp", "gif", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf");
      $acak           = rand(11111111, 99999999);
      $ImageExt       = substr($ImageName, strrpos($ImageName, '.'));
      $ImageExt       = str_replace('.', '', $ImageExt);
      $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
      $NewImageName   = str_replace(' ', '', $this->input->get_post('pekerjaan_dokumen_id') . '_' . date('ymdhis') . '_' . $acak . '.' . $ImageExt);
      if (in_array($ImageExt, $Extension)) {
       move_uploaded_file($_FILES["pekerjaan_dokumen_file"]["tmp_name"], $path . $NewImageName); /* Menyimpan file*/
     }
   }
   /*jika ga ada file diupload*/
 } else {
   $dokumen_status = $this->db->query("SELECT pekerjaan_dokumen_status FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->post('pekerjaan_dokumen_id') . "' ")->row_array();
   $dokumen_statusnya = $dokumen_status['pekerjaan_dokumen_status'] + 1;

   $dokumen_edit = $this->db->query("SELECT pekerjaan_dokumen_file FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->get_post('pekerjaan_dokumen_id') . "'")->row_array();
   /*jika ada view dokumen yang dikasih sesuatu*/
   if ($this->input->post('is_change') == 'y') {
     $NewImageName = $dokumen_statusnya . '_' . $dokumen_edit['pekerjaan_dokumen_file'];
   } else {
     $NewImageName = null;
   }
 }

 /*penomoran revisi*/
 $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->post('pekerjaan_dokumen_id') . "'")->row_array();

 if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
   if ($nomor_revisi['nomor_revisi'] == '') {
     $nomor_revisinya = 0;
   } else {
     $nomor_revisinya = $nomor_revisi['nomor_revisi'];
   }
   $nomor_revisi_baru = ($nomor_revisinya + 1);
 } else {
   $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];
 }

 /*cek dokumen statusnya dari input status*/
 $data_status = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->get_post('pekerjaan_dokumen_id') . "'")->row_array();

 $status_dokumen = ($this->input->get_post('pekerjaan_dokumen_status') == 'n') ? '0' : $data_status['pekerjaan_dokumen_status'] + 1;

 $data_dokumen = $this->db->select('*')->from('dec.dec_pekerjaan_dokumen a')->join('dec.dec_pekerjaan_template b', 'b.pekerjaan_template_id=a.id_pekerjaan_template', 'left')->where(array('pekerjaan_dokumen_id' => $this->input->post('pekerjaan_dokumen_id')))->get()->row_array();

 if ($data_status['is_proses'] == null || $data_status['is_proses'] == '') {
   $proses = 'y';
 } else if ($data_status['is_proses'] == 'y') {
   $proses = 'a';
 }

 /* Insert */
 if ($NewImageName == null) {
   $data['pekerjaan_dokumen_id_temp'] = anti_inject($this->input->post('pekerjaan_dokumen_id'));
   $data['pekerjaan_dokumen_id'] = create_id();
   $data['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
   $data['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
   $data['pekerjaan_dokumen_keterangan'] = anti_inject($this->input->post('pekerjaan_dokumen_keterangan'));
   $data['id_create'] = $isi['pegawai_nik'];
   $data['is_proses'] = 'dtc';
   $data['id_create_awal'] = $data_status['id_create_awal'];
   $data['pekerjaan_dokumen_jumlah'] = $data_status['pekerjaan_dokumen_jumlah'];
   $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
   $this->M_pekerjaan->simpanAksiSama($data);
   if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
   } else {
     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
   }
 } else {
   $data['pekerjaan_dokumen_id_temp'] = anti_inject($this->input->post('pekerjaan_dokumen_id'));
   $data['pekerjaan_dokumen_id'] = create_id();
   $data['pekerjaan_dokumen_file'] = $NewImageName;
   $data['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
   $data['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
   $data['pekerjaan_dokumen_keterangan'] = anti_inject($this->input->post('pekerjaan_dokumen_keterangan'));
   $data['id_create'] = $isi['pegawai_nik'];
   $data['is_proses'] = 'dtc';
   $data['id_create_awal'] = $data_status['id_create_awal'];
   $data['pekerjaan_dokumen_jumlah'] = $data_status['pekerjaan_dokumen_jumlah'];
   $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
   $this->M_pekerjaan->simpanAksi($data);
   if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
   } else {
     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
   }
 }
 /* Insert */


 /*cek apakah direvisi*/
 if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
   /*ambil urutan revisi sebelumnya dari dokumen di pekerjaan tersebut*/
   $nomor_revisi = $this->M_pekerjaan->getRevisiNomor($data);
   if ($nomor_revisi['max'] == '') {
     $nomor_revisinya = 0;
   } else {
     $nomor_revisinya = $nomor_revisi['max'];
   }

   $data_revisi_isi['pekerjaan_dokumen_revisi'] = $nomor_revisinya + 1;

   /*revisi nomor ke doc yang direvisikan*/
   $this->M_pekerjaan->updateRevisiNomor($data_revisi_isi, $data['pekerjaan_dokumen_id']);
 }

 if ($data['pekerjaan_dokumen_id_temp']) {
   $data_edit['is_lama'] = 'y';
   $this->M_pekerjaan->editAksi($data_edit, $data['pekerjaan_dokumen_id_temp']);
 }
}
/* Aksi Approve / Reject Dokumen IFC */


/* Aksi Approve / Reject Dokumen IFC */
public function simpanAksiTransmitalCangunAVP()
{
	if ($this->input->get('id_user')) {
    $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'");
    $isi = $sql_isi->row_array();
  } else {
    $isi = $this->session->userdata();
  }

  $path = "./document/";
  if (!file_exists($path)) mkdir($path);
  /*jika ada file diupload*/
  if (isset($_FILES['pekerjaan_dokumen_file'])) {
    $fileupload      = $_FILES['pekerjaan_dokumen_file']['tmp_name'];
    $ImageName       = $_FILES['pekerjaan_dokumen_file']['name'];
    $ImageType       = $_FILES['pekerjaan_dokumen_file']['type'];
    if (!empty($fileupload)) {
      $Extension    = array("jpeg", "jpg", "png", "bmp", "gif", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf");
      $acak           = rand(11111111, 99999999);
      $ImageExt       = substr($ImageName, strrpos($ImageName, '.'));
      $ImageExt       = str_replace('.', '', $ImageExt);
      $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
      $NewImageName   = str_replace(' ', '', $this->input->get_post('pekerjaan_dokumen_id') . '_' . date('ymdhis') . '_' . $acak . '.' . $ImageExt);
      if (in_array($ImageExt, $Extension)) {
       move_uploaded_file($_FILES["pekerjaan_dokumen_file"]["tmp_name"], $path . $NewImageName); /* Menyimpan file*/
     }
   }
   /*jika ga ada file diupload*/
 } else {
   $dokumen_status = $this->db->query("SELECT pekerjaan_dokumen_status FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->post('pekerjaan_dokumen_id') . "' ")->row_array();
   $dokumen_statusnya = $dokumen_status['pekerjaan_dokumen_status'] + 1;

   $dokumen_edit = $this->db->query("SELECT pekerjaan_dokumen_file FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->get_post('pekerjaan_dokumen_id') . "'")->row_array();
   /*jika ada view dokumen yang dikasih sesuatu*/
   if ($this->input->post('is_change') == 'y') {
     $NewImageName = $dokumen_statusnya . '_' . $dokumen_edit['pekerjaan_dokumen_file'];
   } else {
     $NewImageName = null;
   }
 }

 /*penomoran revisi*/
 $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->post('pekerjaan_dokumen_id') . "'")->row_array();

 if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
   if ($nomor_revisi['nomor_revisi'] == '') {
     $nomor_revisinya = 0;
   } else {
     $nomor_revisinya = $nomor_revisi['nomor_revisi'];
   }
   $nomor_revisi_baru = ($nomor_revisinya + 1);
 } else {
   $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];
 }

 /*cek dokumen statusnya dari input status*/
 $data_status = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->get_post('pekerjaan_dokumen_id') . "'")->row_array();

 $status_dokumen = ($this->input->get_post('pekerjaan_dokumen_status') == 'n') ? '0' : $data_status['pekerjaan_dokumen_status'] + 1;

 $data_dokumen = $this->db->select('*')->from('dec.dec_pekerjaan_dokumen a')->join('dec.dec_pekerjaan_template b', 'b.pekerjaan_template_id=a.id_pekerjaan_template', 'left')->where(array('pekerjaan_dokumen_id' => $this->input->post('pekerjaan_dokumen_id')))->get()->row_array();

 if ($data_status['is_proses'] == null || $data_status['is_proses'] == '') {
   $proses = 'y';
 } else if ($data_status['is_proses'] == 'y') {
   $proses = 'a';
 }

 /* Insert */
 if ($NewImageName == null) {
   $data['pekerjaan_dokumen_id_temp'] = anti_inject($this->input->post('pekerjaan_dokumen_id'));
   $data['pekerjaan_dokumen_id'] = create_id();
   $data['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
   $data['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
   $data['pekerjaan_dokumen_keterangan'] = anti_inject($this->input->post('pekerjaan_dokumen_keterangan'));
   $data['id_create'] = $isi['pegawai_nik'];
   $data['is_proses'] = 'dtca';
   $data['id_create_awal'] = $data_status['id_create_awal'];
   $data['pekerjaan_dokumen_jumlah'] = $data_status['pekerjaan_dokumen_jumlah'];
   $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
   $this->M_pekerjaan->simpanAksiSama($data);
   if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
   } else {
     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
   }
 } else {
   $data['pekerjaan_dokumen_id_temp'] = anti_inject($this->input->post('pekerjaan_dokumen_id'));
   $data['pekerjaan_dokumen_id'] = create_id();
   $data['pekerjaan_dokumen_file'] = $NewImageName;
   $data['pekerjaan_dokumen_status'] = anti_inject($status_dokumen);
   $data['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
   $data['pekerjaan_dokumen_keterangan'] = anti_inject($this->input->post('pekerjaan_dokumen_keterangan'));
   $data['id_create'] = $isi['pegawai_nik'];
   $data['is_proses'] = 'dtca';
   $data['id_create_awal'] = $data_status['id_create_awal'];
   $data['pekerjaan_dokumen_jumlah'] = $data_status['pekerjaan_dokumen_jumlah'];
   $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
   $this->M_pekerjaan->simpanAksi($data);
   if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  Direject dengan Alasan <span style="color:red">' . $data['pekerjaan_dokumen_keterangan'] . '</span>', $isi['pegawai_nik']);
   } else {
     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");
     dblog('I', $data_status['id_pekerjaan'], 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
   }
 }
 /* Insert */


 /*cek apakah direvisi*/
 if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
   /*ambil urutan revisi sebelumnya dari dokumen di pekerjaan tersebut*/
   $nomor_revisi = $this->M_pekerjaan->getRevisiNomor($data);
   if ($nomor_revisi['max'] == '') {
     $nomor_revisinya = 0;
   } else {
     $nomor_revisinya = $nomor_revisi['max'];
   }

   $data_revisi_isi['pekerjaan_dokumen_revisi'] = $nomor_revisinya + 1;

   /*revisi nomor ke doc yang direvisikan*/
   $this->M_pekerjaan->updateRevisiNomor($data_revisi_isi, $data['pekerjaan_dokumen_id']);
 }

 if ($data['pekerjaan_dokumen_id_temp']) {
   $data_edit['is_lama'] = 'y';
   $this->M_pekerjaan->editAksi($data_edit, $data['pekerjaan_dokumen_id_temp']);
 }
}
/* Aksi Approve / Reject Dokumen IFC */



/* DETAIL PEKERJAAN */

/* DOWNLOAD */
public function downloadDokumen()
{
	$this->load->library('ciqrcode'); //pemanggilan library QR CODE
  $this->load->library('PdfGenerator');
  $this->load->helper(array('url', 'download'));

  $dokumen = explode('~', $this->input->get_post('pekerjaan_dokumen_file'));
  $format  = explode('.', $dokumen[0]);

  $id_dokumen = preg_replace("/[^0-9^a-z^A-Z]/", "", $dokumen[1]);
  $param['pekerjaan_id'] =  preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get_post('pekerjaan_id'));
  $param_dokumen['pekerjaan_dokumen_id'] = preg_replace("/[^0-9^a-z^A-Z^_.]/", "", $dokumen[0]);

  /* QRCODE */
	$config['cacheable']    = true; //boolean, the default is true
	$config['cachedir']     = './application/cache/'; //string, the default is application/cache/
	$config['errorlog']     = './application/logs/'; //string, the default is application/logs/
	$config['imagedir']     = './document/qrcode/'; //direktori penyimpanan qr code
	$config['quality']      = true; //boolean, the default is true
	$config['size']         = '1024'; //interger, the default is 1024
  $config['black']        = array(224, 255, 255); // array, default is array(255,255,255)
  $config['white']        = array(70, 130, 180); // array, default is array(0,0,0)
  $this->ciqrcode->initialize($config);

  $judul = 'qrcode_' . $format[0];
  $url = base_url('project/direct/downloadDokumen?pekerjaan_id=') . $this->input->get_post('pekerjaan_id') . '&pekerjaan_dokumen_file=' . $this->input->get_post('pekerjaan_dokumen_file');

	$image_name = $judul . '.PNG'; //buat name dari qr code sesuai dengan nim
					$params['data'] = $url; //data yang akan di jadikan QR CODE
					$params['level'] = 'M'; //H=High
					$params['size'] = 10;
					$params['savename'] = FCPATH . $config['imagedir'] . $image_name; //simpan image QR CODE ke folder assets/images/
					$this->ciqrcode->generate($params);
					$this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_qrcode = '" . $image_name . "' WHERE pekerjaan_dokumen_id = '" . $dokumen[1] . "'");
					/* QRCODE */

					$data['pekerjaan'] = $this->M_pekerjaan->getPekerjaan($param);
					$data['bagian'] = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN global.global_bagian_detail b ON b.id_pegawai = a.id_create_awal LEFT JOIN global.global_bagian c ON c.bagian_id = b.id_bagian WHERE pekerjaan_dokumen_id = '" . $dokumen[1] . "'  ")->row_array();

					$sql_template = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON a.id_pekerjaan_template = b.pekerjaan_template_id WHERE pekerjaan_dokumen_id = '" . $id_dokumen . "'");
					$isi_template = $sql_template->row_array();
					$data['template'] = $isi_template;

					$sql_dokumen = $this->db->query("SELECT pekerjaan_dokumen_file FROM dec.dec_pekerjaan_dokumen WHERE  pekerjaan_dokumen_file !='' AND pekerjaan_dokumen_id = '" . $id_dokumen . "'");
					$data_dokumen = $sql_dokumen->row_array();

					/* foreach ($data_dokumen as $key => $value) {*/
						if ($data_dokumen['pekerjaan_dokumen_file'] != '') {
             dblog('V', $this->input->get_post('pekerjaan_id'), 'Dokumen ' . $isi_template['pekerjaan_template_nama'] . ' - ' . $isi_template['pekerjaan_dokumen_nama'] . ' Telah Didownload');

             if ($data['pekerjaan']['klasifikasi_pekerjaan_rkap'] == 'n') {
               $html =    $this->load->view('project/pekerjaan_cover_non_rkap', $data, true);
             } else {
               $html =    $this->load->view('project/pekerjaan_cover_rkap', $data, true);
             }

             $filename = 'cover_' . preg_replace("/[^0-9^a-z^A-Z^_.]/", "", $dokumen[0]);

             if ($isi_template['pekerjaan_dokumen_kertas'] != '' && $isi_template['pekerjaan_dokumen_orientasi'] != '') {
               $this->pdfgenerator->save($html, $filename, $isi_template['pekerjaan_dokumen_kertas'], $isi_template['pekerjaan_dokumen_orientasi']);
             } else {
               $this->pdfgenerator->save($html, $filename, 'A4', 'portrait');
             }

             $judul = $isi_template['pekerjaan_template_nama'] . ' - ' . $isi_template['pekerjaan_dokumen_nama'] . ' - ' . $isi_template['pekerjaan_dokumen_nomor'];
             $data1['judul'] = $judul;
             $data1['direktori'] = FCPATH.'document_baru/'.$judul;

             $data1['cover_download'] = 'cover_' . preg_replace("/[^0-9^a-z^A-Z^_.]/", "", $dokumen[0]);
             $data1['data_download'] = preg_replace("/[^0-9^a-z^A-Z^_.]/", "", $dokumen[0]);
             $data1['qrcode'] = preg_replace("/[^0-9^a-z^A-Z^_.]/", "", $image_name);
             $data1['halaman'] = $isi_template['pekerjaan_dokumen_jumlah'];
             $data1['kertas'] = $isi_template['pekerjaan_dokumen_kertas'];
             $data1['orientasi'] = $isi_template['pekerjaan_dokumen_orientasi'];
             $data1['qr_code'] = $isi_template['pekerjaan_dokumen_qrcode'];
             $data1['status_dokumen'] = $isi_template['pekerjaan_dokumen_status'];
             $data1['klasifikasi_pekerjaan_kode'] = $data['pekerjaan']['klasifikasi_pekerjaan_kode'];
             $this->load->view('project/combine', $data1);
             /* }*/
           }
         }

         public function downloadDokumenUsulan()
         {
           if (isset($_GET['id_user'])) {
            $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
            $isi = $sql_isi->row_array();
          } else {
            $isi = $this->session->userdata();
          }
          $this->load->library('PdfGenerator');
          $this->load->helper(array('url', 'download'));

          $dokumen = explode('~', $this->input->get_post('pekerjaan_dokumen_file'));
          $format  = explode('.', $dokumen[0]);

          $id_dokumen = preg_replace("/[^0-9^a-z^A-Z]/", "", $dokumen[1]);
          $param['pekerjaan_id'] =  preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get_post('pekerjaan_id'));
          $param_dokumen['pekerjaan_dokumen_id'] = preg_replace("/[^0-9^a-z^A-Z^_.]/", "", $dokumen[0]);

          $data['pekerjaan'] = $this->M_pekerjaan->getPekerjaan($param);
          /*$data['bagian'] = $sql_avp_bagian_nama = $this->db->query("SELECT bagian_nama FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai LEFT JOIN global.global_bagian c ON c.bagian_id = a.id_bagian WHERE a.id_pegawai = '" . $value['id_create_awal'] . "'");*/

          $data['bagian'] = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN global.global_bagian_detail b ON b.id_pegawai = a.id_create_awal LEFT JOIN global.global_bagian c ON c.bagian_id = b.id_bagian WHERE pekerjaan_dokumen_id = '" . $dokumen[1] . "'  ")->row_array();
          /*print_r($data['bagian']);*/

          $sql_template = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON a.id_pekerjaan_template = b.pekerjaan_template_id WHERE pekerjaan_dokumen_id = '" . $id_dokumen . "'");
          $isi_template = $sql_template->row_array();
          $data['template'] = $isi_template;

          $sql_dokumen = $this->db->query("SELECT pekerjaan_dokumen_file FROM dec.dec_pekerjaan_dokumen WHERE  pekerjaan_dokumen_file !=''");
          $data_dokumen = $sql_dokumen->result_array();

          dblog('V', $this->input->get_post('pekerjaan_id'), 'Dokumen ' . $isi_template['pekerjaan_dokumen_nama'] . ' Telah Didownload', $isi['pegawai_nik']);

          /*check version*/
					// $guesser = new RegexGuesser();
					// echo $guesser->guess(FCPATH.'document/'.$isi_template['pekerjaan_dokumen_file']);
          /*check version*/

					// $command = new GhostscriptConverterCommand();
					// $filesystem = new Filesystem();

					// $converter = new GhostscriptConverter($command, $filesystem);
					// $converter->convert(FCPATH . 'document/' . $isi_template['pekerjaan_dokumen_file'], '1.4');

          force_download('./document/' . $isi_template['pekerjaan_dokumen_file'], NULL);
        }
        /* DOWNLOAD */

        function downloadDokumenList()
        {

					$this->load->library('ciqrcode'); //pemanggilan library QR CODE
					$this->load->library('PdfGenerator');
					$this->load->helper(array('url', 'download'));

					if ($this->input->get('id_user')) {
            $session = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $this->input->get('id_user')])->row_array();
          } else {
            $session = $this->session->userdata();
          }

          $param['pekerjaan_id'] = $this->input->get('idp');

          $idd = $this->input->get('idd');
          $eidd = explode(',', $idd);
          $iidd = implode("','", $eidd);

          /*generate qr code*/
          $config['cacheable']    = true;
          $config['cachedir']     = './application/cache/';
          $config['errorlog']     = './application/logs/';
          $config['imagedir']     = './document/qrcode/';
          $config['quality']      = true;
          $config['size']         = '1024';
          $config['black']        = array(224, 255, 255);
          $config['white']        = array(70, 130, 180);
          $this->ciqrcode->initialize($config);

          $judul = 'qrcodelist_' . $this->input->get('idp');
          $url = base_url('project/direct/downloadDokumenList?idp=') . $this->input->get_post('idp') . '&=idd' . $this->input->get_post('idd');

          $image_name = $judul . '.PNG';
          $params['data'] = $url;
          $params['level'] = 'L';
          $params['size'] = 10;
          $params['savename'] = FCPATH . $config['imagedir'] . $image_name;


					// echo '<img src="' . base_url() . '/document/qrcode/' . $image_name . '">';

					// die();

          $this->ciqrcode->generate($params);
          $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_qrcode_list = '" . $image_name . "' WHERE pekerjaan_dokumen_id IN('" . $iidd . "')");
          /*generate qr code*/


          $data['pekerjaan'] = $this->M_pekerjaan->getPekerjaan($param);
          $data['dokumen'] = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template  LEFT JOIN global.global_bagian_detail c ON c.id_pegawai = a.id_create_awal LEFT JOIN global.global_bagian d ON d.bagian_id = c.id_bagian WHERE id_pekerjaan = '" . $this->input->get('idp') . "' AND pekerjaan_dokumen_id IN('" . $iidd . "')")->result_array();

          $cover = $this->load->view('project/pekerjaan_cover_dokumen', $data, true);

          $cover1 = 'coverlist_' . preg_replace("/[^0-9^a-z^A-Z^_.]/", "", $this->input->get('idp'));

          $this->pdfgenerator->save($cover, $cover1, 'A4', 'portrait');

          $list = $this->load->view('project/pekerjaan_list_dokumen', $data, true);

          $list1 = 'dokumenlist_' . preg_replace("/[^0-9^a-z^A-Z^_.]/", "", $this->input->get('idp'));

          $this->pdfgenerator->save($list, $list1, 'A4', 'portrait');


          $data1['cover_download'] = $cover1;
          $data1['list_download'] = $list1;
          $data1['judul'] = $data['pekerjaan']['pekerjaan_judul'] . ' - ' . $data['pekerjaan']['pekerjaan_nomor'];
          $data1['dokumen'] = $data['dokumen'];
          $data1['qr_code'] = $image_name;


          $this->load->view('project/combine_dokumen', $data1);
        }


        /*departemen list*/
        public function getListDepartemen()
        {
          $list['results'] = array();
          $param['q'] = $this->input->get('q');
          $data = $this->M_departemen->getDepartemen($param);
          foreach ($data as $key => $value) {
           array_push($list['results'], [
             'id' => $value['pegawai_dep_id'],
             'text'=> $value['pegawai_dep_nama'],
           ]);
         }
         echo json_encode($list);
       }
       /*departemen list*/


       /*klasifikasi pekerjaan list*/
       public function getListKlasifikasiPekerjaan()
       {
        $list['results'] = array();

        $param['klasifikasi_pekerjaan_rkap'] = $this->input->get('rkap');
        $param['q'] = $this->input->get('q');

        $data = $this->M_klasifikasi_pekerjaan->getKlasifikasiPekerjaan($param);

        foreach ($data as $key => $value) {
         array_push($list['results'], [
           'id' => $value['klasifikasi_pekerjaan_id'],
           'text' => ($value['klasifikasi_pekerjaan_rkap']=='y') ? 'RKAP'.' - '. $value['klasifikasi_pekerjaan_nama'] : 'Non RKAP'.' - '. $value['klasifikasi_pekerjaan_nama'],
         ]);
       }
       echo json_encode($list);
     }
     /*klasifikasi pekerjaan list*/

     /*kategori pekerjaan list*/
     public function getListKategoriPekerjaan()
     {
       $list['results'] = array();

       $param['q'] = $this->input->get('q');

       $data = $this->M_kategori_pekerjaan->getKategoriPekerjaan($param);

       foreach ($data as $key => $value) {
        array_push($list['results'], [
          'id' => $value['kategori_pekerjaan_id'],
          'text' => $value['kategori_pekerjaan_nama'],
        ]);
      }
      echo json_encode($list);
    }
    /*kategori pekerjaan list*/


    /*USER LIST*/
    public function getUserList()
    {
      $isi = $this->session->userdata();
      $list['results'] = array();

      $param['pegawai_nama'] = $this->input->get('pegawai_nama');
      $param['pegawai_poscode'] = $isi['pegawai_poscode'];

      foreach ($this->M_pekerjaan->getUserList($param) as $key => $value) {
       array_push($list['results'], [
         'id' => $value['pegawai_nik'],
         'text' => $value['pegawai_nama'] . ' - ' . $value['pegawai_postitle'],
       ]);
     }
     echo json_encode($list);
   }
   /*USER LIST*/

   /* user list vp all dep */
   public function getUserlistVPAllDep()
   {
     $isi = $this->session->userdata();

     $list['results'] = array();

     $param['pegawai_nama'] = $this->input->get('pegawai_nama');
     $param['pegawai_poscode'] = $isi['pegawai_poscode'];
     $param['pegawai_id_dep'] = $this->input->get('pegawai_id_dep');
     $data = $this->M_pekerjaan->getUserListVPAllDep($param);

     foreach ($data as $key => $value) {
      array_push($list['results'], [
        'id' => $value['pegawai_nik'],
        'text' => $value['pegawai_nama'] . ' - ' . $value['pegawai_postitle'],
      ]);
    }
    echo json_encode($list);
  }
  /* user list vp all dep */

  /* user list vp */
  public function getUserListVP()
  {
   $isi = $this->session->userdata();

   $list['results'] = array();

   $param['pegawai_nama'] = $this->input->get('pegawai_nama');
   $param['pegawai_poscode'] = $isi['pegawai_poscode'];
   $data = $this->M_pekerjaan->getUserListVP($param);
   foreach ($data as $key => $value) {
    array_push($list['results'], [
      'id' => $value['pegawai_nik'],
      'text' => $value['pegawai_nama'] . ' - ' . $value['pegawai_postitle'],
    ]);
  }

  echo json_encode($list);
}
/* user list vp */

/* user list avp */
public function getUserListAVP()
{
 $isi = $this->session->userdata();
 $list['results'] = array();
 $param['pegawai_nama'] = $this->input->get('pegawai_nama');
 $param['pegawai_poscode'] = $isi['pegawai_poscode'];
 $param['bagian_nama'] = $isi['pegawai_nama_bag'];


 if ($isi['pegawai_poscode'] == 'E53600031A' || $isi['pegawai_poscode'] == 'E53600060B') {
  foreach ($this->M_pekerjaan->getUserListAVPKhusus($param) as $key => $value) {
    array_push($list['results'], [
     'id' => $value['pegawai_nik'],
     'text' => $value['pegawai_nama'] . ' - ' . $value['pegawai_postitle'],
   ]);
  }
} else {
 foreach ($this->M_pekerjaan->getUserListAVP($param) as $key => $value) {
   array_push($list['results'], [
    'id' => $value['pegawai_nik'],
    'text' => $value['pegawai_nama'] . ' - ' . $value['pegawai_postitle'],
  ]);
 }
}

echo json_encode($list);
}
/* user list avp */

/* user list vp */
public function getUserPengganti()
{
	if (isset($_GET['id_user'])) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $isi = $sql_user->row_array();
  } else {
    $isi = $this->session->userdata();
  }

  $sql_bagian = $this->db->query("SELECT * FROM global.global_bagian_detail WHERE id_pegawai='" . $isi['pegawai_nik'] . "'");
  $data_bagian = $sql_bagian->row_array();

  $list['results'] = array();
  $param['pegawai_nama'] = $this->input->get('pegawai_nama');
  /*$param['pegawai_poscode'] = $isi['pegawai_poscode'];*/
  /*$param['bagian_nama'] = $isi['pegawai_nama_bag'];*/
  $param['bagian_id'] = $data_bagian['id_bagian'];

  /*$this->M_pekerjaan->getUserListAVP($param);*/


  foreach ($this->M_pekerjaan->getUserListAVP($param) as $key => $value) {
    array_push($list['results'], [
      'id' => $value['pegawai_nik'],
      'text' => $value['pegawai_nama'] . ' - ' . $value['pegawai_postitle'],
    ]);
  }

  echo json_encode($list);
}
/* user list vp */

/* user koor pengganti */
public function getUserKoorPengganti()
{
	if (isset($_GET['id_user'])) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $isi = $sql_user->row_array();
  } else {
    $isi = $this->session->userdata();
  }

  $list['results'] = array();
  $param['pegawai_nama'] = $this->input->get('pegawai_nama');
  $param['id_pekerjaan'] = $this->input->get('id_pekerjaan');

  $data = $this->M_pekerjaan->getUserKoorPengganti($param);

  foreach ($data as $key => $value) {
    array_push($list['results'], [
      'id' => $value['pegawai_nik'],
      'text' => $value['pegawai_nama'] . ' - ' . $value['pegawai_postitle'],
    ]);
  }

  echo json_encode($list);
}
/* user koor pengganti */

/* get user staf */
public function getUserStaf()
{
	$list['results'] = array();

	$param['pegawai_nama'] = $this->input->get('pegawai_nama');
	foreach ($this->M_pekerjaan->getUserStaf($param) as $key => $value) {

    array_push($list['results'], [
      'id' => $value['pegawai_nik'],
      'text' => $value['pegawai_nama'] . ' - ' . $value['pegawai_postitle'],
    ]);
  }
  echo json_encode($list);
}
/* get user staf */

/*get urutan proyek list*/
public function getUrutanProyekList()
{
	$list['results'] = array();

	$param['urutan_proyek_nama'] = $this->input->get('q');
	$data = $this->M_pekerjaan->getUrutanProyek($param);
	foreach ($data as $key => $value) {
    array_push($list['results'], [
      'id' => $value['urutan_proyek_id'],
      'text' => $value['urutan_proyek_nama'],
    ]);
  }
  echo json_encode($list);
}
/*get urutan proyek list*/

/*get section area list*/
public function getSectionAreaList()
{
	$list['results'] = array();

	$param['section_area_nama'] = $this->input->get('q');
					// $param['id_urutan_proyek'] = $this->input->get('id_urutan_proyek');
	$data = $this->M_pekerjaan->getSectionArea($param);
	foreach ($data as $key => $value) {
    array_push($list['results'], [
      'id' => $value['section_area_id'],
      'text' => $value['section_area_nama'],
    ]);
  }
  echo json_encode($list);
}
/*get section area list*/

/*GET VP AVP */
public function getVPAVP()
{
	$user = $this->session->userdata();

	$param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
	$param['id_tanggung_jawab'] = 'n';
	$data = $this->M_pekerjaan->getVPAVP($param);

	echo json_encode($data);
}
/*GET VP AVP */

public function getUserStafVP()
{
	$user = $this->session->userdata();

	$param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
	$param['is_cc'] = $this->input->get_post('is_cc');
	$data = $this->M_pekerjaan->getUserStafVP($param);

	echo json_encode($data);
}

/*GET VP AVP Penanggung Jawab */
public function getVPAVPTJ()
{
	$user = $this->session->userdata();

	$param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
	$param['pegawai_nik'] = $user['pegawai_nik'];
	$data = $this->M_pekerjaan->getVPAVP($param);

	echo json_encode($data);
}
/*GET VP AVP Penanggung Jawab TJ*/


/*GET USER KOOR*/
public function getUserKoor()
{

	$user = (isset($_GET['id_user'])) ? $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $_GET['id_user']))->row_array() : $this->session->userdata();

	$status = anti_inject($this->input->get_post('status'));

	/*cek apakah staf atau bukan*/
	$user_staf = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_user = '" . $user['pegawai_nik'] . "' AND id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '5'")->row_array();

	$staf = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user = '" . $user['pegawai_nik'] . "' AND id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '5'")->row_array();

	$isi_staf = array();

	if ($user_staf['total'] > 0) {
    $data_staf['id_penanggung_jawab'] = $staf['id_penanggung_jawab'];
    array_push($isi_staf, $data_staf);
    echo json_encode($data_staf);
  } else if ($user['pegawai_poscode'] == 'E53600031A' || $user['pegawai_poscode'] == 'E53500031B') {
    $param['id_user'] = $user['pegawai_nik'];
    $param['pekerjaan_disposisi_status'] = anti_inject($this->input->get_post('status') - 1);
    $data = $this->M_pekerjaan->getUserKoorKhusus($param);
    echo json_encode($data);
  } else {
    $param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
    $param['pekerjaan_disposisi_status'] = $this->input->get_post('status');
    $param['id_user'] = $user['pegawai_nik'];
    $data = $this->M_pekerjaan->getUserKoor($param);
    $param_koor['bagian_id'] = $data['bagian_id'];
    $param_koor['pekerjaan_disposisi_status'] = anti_inject($this->input->get_post('status') - 1);
    $param_koor['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
    $datanya = $this->M_pekerjaan->getUserKoor($param_koor);

    echo json_encode($datanya);
  }
}
/*GET USER KOOR*/

/*GET USER KOOR*/
public function getUserKoorIFC()
{

	if (isset($_GET['id_user'])) {
    $user = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $_GET['id_user']))->row_array();
  } else {
    $user = $this->session->userdata();
  }

  if ($user['pegawai_poscode'] == 'E53600031A' || $user['pegawai_poscode'] == 'E53500031B') {
    $param['id_user'] = $user['pegawai_nik'];
    $param['pekerjaan_disposisi_status'] = anti_inject($this->input->get_post('status') - 3);
    $data = $this->M_pekerjaan->getUserKoorKhusus($param);
    echo json_encode($data);
  } else {
    $param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
    $param['pekerjaan_disposisi_status'] = $this->input->get_post('status');
    $param['id_user'] = $user['pegawai_nik'];
    $data = $this->M_pekerjaan->getUserKoor($param);

    $param_koor['bagian_id'] = $data['bagian_id'];
    $param_koor['pekerjaan_disposisi_status'] = anti_inject($this->input->get_post('status') - 3);
    $param_koor['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');

    $datanya = $this->M_pekerjaan->getUserKoor($param_koor);

    echo json_encode($datanya);
  }
}
/*GET USER KOOR*/

public function getUserKoorVP()
{

	if (isset($_GET['id_user'])) {
    $user = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $_GET['id_user']))->row_array();
  } else {
    $user = $this->session->userdata();
  }

  if ($user['pegawai_poscode'] == 'E53600031A' || $user['pegawai_poscode'] == 'E53500031B') {
    $param['id_user'] = $user['pegawai_nik'];
    $param['pekerjaan_disposisi_status'] = ($this->input->get_post('status') == '5') ? anti_inject($this->input->get_post('status') + 1) :  $this->input->get_post('status');
    $data = $this->M_pekerjaan->getUserKoorKhusus($param);

    echo json_encode($data);
  } else {
    $param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
    $param['pekerjaan_disposisi_status'] = ($this->input->get_post('status') == '5') ? anti_inject($this->input->get_post('status') + 1) :  $this->input->get_post('status');
    $param['id_user'] = $user['pegawai_nik'];
    $data = $this->M_pekerjaan->getUserKoor($param);

    echo json_encode($data);
  }
}

public function getUserKoorVPIFC()
{

	if (isset($_GET['id_user'])) {
    $user = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $_GET['id_user']))->row_array();
  } else {
    $user = $this->session->userdata();
  }

  if ($user['pegawai_poscode'] == 'E53600031A' || $user['pegawai_poscode'] == 'E53500031B') {
    $param['id_user'] = $user['pegawai_nik'];
    $param['pekerjaan_disposisi_status'] = anti_inject($this->input->get_post('status'));
    $data = $this->M_pekerjaan->getUserKoorKhusus($param);

    echo json_encode($data);
  } else {
    $param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
    $param['pekerjaan_disposisi_status'] = anti_inject($this->input->get_post('status'));
    $param['id_user'] = $user['pegawai_nik'];
    $data = $this->M_pekerjaan->getUserKoor($param);
    echo json_encode($data);
  }
}



public function getPekerjaan()
{
	$isi = $this->session->userdata();

	$sql_disposisi_status = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_user='" . $isi['pegawai_nik'] . "' AND id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND pekerjaan_disposisi_status IN('5','9') AND is_aktif='y' AND is_proses is null");
	$data_disposisi_status = $sql_disposisi_status->row_array();

	$sql_disposisi_ifa_rev = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND pekerjaan_disposisi_status IN('8') AND is_aktif='y' AND is_proses='r' AND is_cc is not null");
	$data_disposisi_ifa_rev = $sql_disposisi_ifa_rev->row_array();

	$sql_is_avp = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan='" . $this->input->get_post('pekerjaan_id') . "' AND id_user ='" . $isi['pegawai_nik'] . "' AND pekerjaan_disposisi_status='6'");
	$data_is_avp = $sql_is_avp->row_array();

	$data_is_pic = $this->db->query("SELECT COUNT(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan ='" . $this->input->get_post('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '8' AND id_user IN (SELECT pic FROM dec.dec_pekerjaan WHERE pekerjaan_id = '" . $this->input->get_post('pekerjaan_id') . "' AND pic = '" . $isi['pegawai_nik'] . "') AND id_user = '" . $isi['pegawai_nik'] . "'")->row_array();

	$sql_disposisi_saat_ini = $this->db->query("SELECT pekerjaan_disposisi_status,id_penanggung_jawab FROM dec.dec_pekerjaan_disposisi WHERE id_user = '" . $isi['pegawai_nik'] . "' AND id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' AND is_proses IS NULL")->row_array();

	$param['pekerjaan_id'] = anti_inject($this->input->get_post('pekerjaan_id'));
	$param['pegawai_nik'] = anti_inject($isi['pegawai_nik']);
	$param['pekerjaan_status'] = anti_inject($this->input->get('pekerjaan_status'));
	/*$param['pekerjaan_disposisi_status'] = anti_inject($this->input->get('pekerjaan_status') + 1);*/

	$param['pekerjaan_disposisi_status'] =  ($data_disposisi_status['total'] > 0) ? anti_inject($this->input->get('pekerjaan_status')) : anti_inject($this->input->get('pekerjaan_status') + 1);
	/*$param['pekerjaan_disposisi_status_rev_ifa'] = ($data_disposisi_ifa_rev['total'] > 0) ? anti_inject($this->input->get('pekerjaan_status') - 3) : anti_inject($this->input->get('pekerjaan_status'));*/

	if ($data_disposisi_ifa_rev['total'] > 0 && $data_is_avp['total'] > 0) {
    $param['pekerjaan_disposisi_status_rev_ifa'] = anti_inject($this->input->get('pekerjaan_status') - 2);
  } else if ($data_disposisi_ifa_rev['total'] > 0 && $data_is_avp['total'] == 0) {
    $param['pekerjaan_disposisi_status_rev_ifa'] = anti_inject($this->input->get('pekerjaan_status') - 3);
  } else {
    $param['pekerjaan_disposisi_status_rev_ifa'] = anti_inject($this->input->get('pekerjaan_status'));
  }

  if (
    $param['pekerjaan_status'] == '5' &&
    ($isi['pegawai_poscode'] == 'E53300000' ||
      $isi['pegawai_poscode'] == 'E53400000' ||
      $isi['pegawai_poscode'] == 'E53100000' ||
      $isi['pegawai_poscode'] == 'E53200000' ||
      $isi['pegawai_poscode'] == 'E53600031A' ||
      $isi['pegawai_poscode'] == 'E53500031B')
  ) {
    $data = $this->M_pekerjaan->getPekerjaanDetailBerjalan($param);
  } else if (
    $param['pekerjaan_status'] == '9' &&
    ($isi['pegawai_poscode'] == 'E53300000' ||
      $isi['pegawai_poscode'] == 'E53400000' ||
      $isi['pegawai_poscode'] == 'E53100000' ||
      $isi['pegawai_poscode'] == 'E53200000' ||
      $isi['pegawai_poscode'] == 'E53600031A' ||
      $isi['pegawai_poscode'] == 'E53500031B')
  ) {
    $data = $this->M_pekerjaan->getPekerjaanDetailBerjalan($param);
  } else if ($param['pekerjaan_status'] == '8' && ($param['pekerjaan_disposisi_status_rev_ifa'] == '5' || $param['pekerjaan_disposisi_status_rev_ifa'] == '6')) {
    $data = $this->M_pekerjaan->getPekerjaanDetailIFARev($param);
  } else {
    $data = $this->M_pekerjaan->getPekerjaanDetail($param);
    $data['is_pic'] = ($data_is_pic['total'] > 0) ? 'y' : 'n';
							 // $data['disposisi_status_sekarang'] = $sql_disposisi_saat_ini['pekerjaan_disposisi_status'];
							 // $data['status_tanggung_jawab_sekarang'] = $sql_disposisi_saat_ini['id_penanggung_jawab'];
    $data['disposisi_status_sekarang'] = ($sql_disposisi_saat_ini) ? $sql_disposisi_saat_ini['pekerjaan_disposisi_status'] : '';
    $data['status_tanggung_jawab_sekarang'] = ($sql_disposisi_saat_ini) ? $sql_disposisi_saat_ini['id_penanggung_jawab'] : '';
  }

  echo json_encode($data);
}

public function getPekerjaanStatusAksi()
{
	$isi = $this->session->userdata();

	$param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
	$param['pegawai_nik'] = $isi['pegawai_nik'];

	echo json_encode($this->M_pekerjaan->getPekerjaan($param));
}

public function getUserSession()
{
	$user = $this->session->userdata();
	echo json_encode($user);
}

public function getBagianSession()
{
	$user = $this->session->userdata();
	$param['id_user'] = $user['pegawai_nik'];
	$param['pegawai_direct_superior'] = $user['pegawai_direct_superior'];

	$data = $this->M_pekerjaan->getBagianSession($param);
	echo json_encode($data);
}

public function getDokumenAksi()
{
	$param['pekerjaan_dokumen_id'] = $this->input->get('pekerjaan_dokumen_id');

	echo json_encode($this->M_pekerjaan->getDokumenAksi($param));
}

public function disposisiView()
{
	$data['id_pekerjaan'] = $this->input->get_post('id_pekerjaan');

	$page = 'project/disposisi-view';
					# code...
	$this->load->view($page, $data, FALSE);
}
/* GET */

public function getUser()
{
	$param['usr_id'] = $this->input->get_post('usr_id');

	$data = $this->M_pekerjaan->getUser($param);

	echo json_encode($data);
}

public function getAsetDocumentUsulanBaru()
{
	$param = array();

	if ($this->input->get('id_pekerjaan')) $param['id_pekerjaan'] = $this->input->get('id_pekerjaan');
	$param['pekerjaan_dokumen_status!='] = 'y';
	$param['is_lama!='] = 'y';
	$data = $this->M_pekerjaan->getAsetDocumentUsulanBaru($param);
	echo json_encode($data);
}

public function getAsetDocument()
{
	if (isset($_GET['id_user'])) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $user = $sql_user->row_array();
  } else {
    $user = $this->session->userdata();
  }
  $param = array();

  $sql_pekerjaan = $this->db->query("SELECT pekerjaan_status FROM dec.dec_pekerjaan WHERE pekerjaan_id = '" . $this->input->get_post('id_pekerjaan') . "'");
  $data_pekerjaan = $sql_pekerjaan->row_array();

  $sqlCC = $this->db->query("SELECT is_cc FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' AND id_user = '" . $user['pegawai_nik'] . "'");
  $dataCC = $sqlCC->row_array();

  $is_hps = (isset($_GET['is_hps'])) ? $_GET['is_hps'] : 'n';
  $is_cc = (isset($dataCC['is_cc'])) ? $dataCC['is_cc'] : 'n';

  $status_cc = ($is_cc == 'h' && $is_hps == 'n') ? "1=0" : "1=1";

  $sql_cek = ($data_pekerjaan['pekerjaan_status'] == 5) ? $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi a LEFT JOIN dec.dec_pekerjaan b ON b.pekerjaan_id=a.id_pekerjaan WHERE " . $status_cc . " AND (pekerjaan_disposisi_status='5' OR pekerjaan_disposisi_status='6') and a.id_user='" . $user['pegawai_nik'] . "'") : $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi a LEFT JOIN dec.dec_pekerjaan b ON b.pekerjaan_id=a.id_pekerjaan WHERE " . $status_cc . " AND pekerjaan_disposisi_status <= '" . $data_pekerjaan['pekerjaan_status'] . "' and a.id_user='" . $user['pegawai_nik'] . "'");
  $data_cek = $sql_cek->row_array();

  $sql_cc = $this->db->query("SELECT count(id_user) as total FROM dec.dec_pekerjaan_disposisi WHERE id_user = '" . $user['pegawai_nik'] . "' AND id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' AND is_cc = 'y' ");
  $data_cc = $sql_cc->row_array();

  $sql_dokumen_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_cc = '" . $user['pegawai_nik'] . "' AND id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' ");
  $data_dokumen_cc = $sql_dokumen_cc->row_array();

  $sql_disposisi = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get('id_pekerjaan') . "' AND id_user = '" . $user['pegawai_nik'] . "' AND pekerjaan_disposisi_status = '5'");
  $data_disposisi = $sql_disposisi->row_array();

  $param['pekerjaan_dokumen_status'] = '0';

  if ($this->input->get_post('id_pekerjaan')) $param['id_pekerjaan'] = $this->input->get_post('id_pekerjaan');
  $param['pekerjaan_dokumen_status!='] = 'y';
  $param['is_lama!='] = 'y';
  $param['id_create_awal'] = $user['pegawai_nik'];
  $param['is_hps'] = $this->input->get_post('is_hps');
  $param['pekerjaan_dokumen_status_max'] = '5';
  $param['id_user'] = $user['pegawai_nik'];
  $param['id_dep'] = $user['pegawai_id_dep'];

  if ($this->input->get_post('is_hps') == 'y' && $this->input->get_post('pekerjaan_status') >= '8' && $user['pegawai_unit_id'] != 'E53000') {
    $param['is_cc_hps'] = 'y';
  }

  if ($data_dokumen_cc['total'] > 0) {
    $param['pekerjaan_dokumen_cc'] = $user['pegawai_nik'];
  }

  $data = array();
  if ($this->input->get_post('id_create') != null) {
    $data = $this->M_pekerjaan->getAsetDocumentUpload($param);
    echo json_encode($data);
  } else {
    $data_dokumen = $this->M_pekerjaan->getAsetDocument($param);

    foreach ($data_dokumen as $value) {


      foreach ($value as $key => $val) {
       $isi[$key] = $val;
     }

     $sql_avp_bagian_nama = $this->db->query("SELECT bagian_nama FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai LEFT JOIN global.global_bagian c ON c.bagian_id = a.id_bagian WHERE a.id_pegawai = '" . $value['id_create_awal'] . "'");

     $data_avp_bagian_nama = $sql_avp_bagian_nama->row_array();

     $sql_cc = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $user['pegawai_nik'] . "'");
     $data_cc = $sql_cc->row_array();

     if ($user['pegawai_poscode'] == 'E53600031A' || $user['pegawai_poscode'] == 'E53500031B') {
       $sql_avp_bagian = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_user ='" . $user['pegawai_nik'] . "' AND pekerjaan_disposisi_status='4'");
       $data_avp_bagian = $sql_avp_bagian->row_array();
     } else {
       $sql_avp_bagian = $this->db->query("SELECT count(*) as total FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $value['id_bagian'] . "' AND pekerjaan_disposisi_status = '4' AND id_pekerjaan = '" . $_GET['id_pekerjaan'] . "' AND b.id_user = '" . $user['pegawai_nik'] . "'");
       $data_avp_bagian = $sql_avp_bagian->row_array();
     }

     $sql_pic = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi a LEFT JOIN dec.dec_pekerjaan b ON b.pic=a.id_user WHERE a.id_pekerjaan = '" . $this->input->get('id_pekerjaan') . "' AND pekerjaan_disposisi_status='8' and is_aktif='y' AND is_cc is null AND pic='" . $user['pegawai_nik'] . "'");
     $data_pic = $sql_pic->row_array();

     $sql_vp = $this->db->query("SELECT count(*) as total FROM global.global_pegawai WHERE pegawai_poscode = 'E53000000' AND pegawai_nik = '" . $user['pegawai_nik'] . "'");
     $data_vp = $sql_vp->row_array();

     $sql_staf = $this->db->query("SELECT count(*) as total FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $value['id_bagian'] . "' AND pekerjaan_disposisi_status = '5' AND id_pekerjaan = '" . $_GET['id_pekerjaan'] . "' AND b.id_user = '" . $user['pegawai_nik'] . "'");
     $data_staf = $sql_staf->row_array();

     $isi['pic'] = ($data_pic['total'] > 0) ? 'y' : 'n';
     $isi['avp'] = ($data_avp_bagian['total'] > 0) ? 'y' : 'n';
     $isi['vp'] = ($data_vp['total'] > 0) ? 'y' : 'n';
     $isi['bagian'] = (!empty($data_avp_bagian_nama['bagian_nama'])) ? $data_avp_bagian_nama['bagian_nama'] : '';
     $isi['cc'] = (!empty($data_cc) && $isi['pekerjaan_status'] > '5') ? $data_cc['is_cc'] : 'n';
     $isi['staf'] = ($data_staf['total'] > 0) ? 'y' : 'n';

     if ($data_cek['total'] >= '2' ||  $user['pegawai_nik'] == $this->admin_sistemnya) array_push($data, $isi);
   }
   echo json_encode($data);
 }
}

public function getAsetDocumentIFC()
{
	if (isset($_GET['id_user'])) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $user = $sql_user->row_array();
  } else {
    $user = $this->session->userdata();
  }

  $param = array();

  $sql_cc = $this->db->query("SELECT count(id_user) as total FROM dec.dec_pekerjaan_disposisi WHERE id_user = '" . $user['pegawai_nik'] . "' AND id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' AND is_cc = 'y' ");
  $data_cc = $sql_cc->row_array();

  $sql_dokumen_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_cc = '" . $user['pegawai_nik'] . "' AND id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' ");
  $data_dokumen_cc = $sql_dokumen_cc->row_array();

  $sql_disposisi = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get('id_pekerjaan') . "' AND id_user = '" . $user['pegawai_nik'] . "' AND pekerjaan_disposisi_status = '5'");
  $data_disposisi = $sql_disposisi->row_array();

  $param['pekerjaan_dokumen_status'] = '0';

  if ($this->input->get_post('id_pekerjaan')) $param['id_pekerjaan'] = $this->input->get_post('id_pekerjaan');
  $param['pekerjaan_dokumen_status!='] = 'y';
  $param['is_lama!='] = 'y';
  $param['id_create_awal'] = $user['pegawai_nik'];
  $param['is_hps'] = $this->input->get_post('is_hps');
  $param['pekerjaan_dokumen_status_min'] = '0';
  $param['pekerjaan_dokumen_status_max'] = '9';
  $param['id_user'] = $user['pegawai_nik'];

  $ifc_staf = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' AND pekerjaan_disposisi_status = '9' AND id_user='" . $user['pegawai_nik'] . "'")->row_array();


  if ($ifc_staf['total'] > 0) {
    $param['is_proses'] = 'y';
  }

  if ($data_dokumen_cc['total'] > 0) {
    $param['pekerjaan_dokumen_cc'] = $user['pegawai_nik'];
  }

  $data = array();
  $cust_data4 = array();
  if ($this->input->get_post('id_create') != null) {
    $data4 = $this->M_pekerjaan->getAsetDocumentUploadIFC($param);
							 // echo $this->db->last_query();
							 // die();
    foreach ($data4 as $k4 => $cust_value4) {
      $template = (isset($data4[$k4 - 1]['id_pekerjaan_template'])) ? $data4[$k4 - 1]['id_pekerjaan_template'] : 0;
      $nama = (isset($data4[$k4 - 1]['pekerjaan_dokumen_nama'])) ? $data4[$k4 - 1]['pekerjaan_dokumen_nama'] : 0;
      $nomor = (isset($data4[$k4 - 1]['pekerjaan_dokumen_nomor'])) ? $data4[$k4 - 1]['pekerjaan_dokumen_nomor'] : 0;
      $create = (isset($data4[$k4 - 1]['id_create_awal'])) ? $data4[$k4 - 1]['id_create_awal'] : 0;
										// if ( $cust_value4['pekerjaan_dokumen_nomor'] != $nomor  && $cust_value4['id_pekerjaan_template']!=$template) array_push($cust_data4, $cust_value4);
    }
							 // if(!empty($cust_data4)){
							 // echo json_encode($cust_data4);
							 // }else{
    echo json_encode($data4);
							 // }
  } else {
   $dokumen = $this->M_pekerjaan->getAsetDocument($param);
   foreach ($dokumen as $value) {
     foreach ($value as $key => $val) {
      $isi[$key] = $val;
    }

    $sql_avp_bagian_nama = $this->db->query("SELECT bagian_nama FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai LEFT JOIN global.global_bagian c ON c.bagian_id = a.id_bagian WHERE a.id_pegawai = '" . $value['id_create_awal'] . "'");

    $data_avp_bagian_nama = $sql_avp_bagian_nama->row_array();

    $sql_cc = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $user['pegawai_nik'] . "'");
    $data_cc = $sql_cc->row_array();

    if ($user['pegawai_poscode'] == 'E53600031A' || $user['pegawai_poscode'] == 'E53500031B') {
      $sql_avp_bagian = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_user ='" . $user['pegawai_nik'] . "' AND pekerjaan_disposisi_status='4'");
      $data_avp_bagian = $sql_avp_bagian->row_array();
    } else {
      $sql_avp_bagian = $this->db->query("SELECT count(*) as total FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $value['id_bagian'] . "' AND pekerjaan_disposisi_status = '4' AND id_pekerjaan = '" . $_GET['id_pekerjaan'] . "' AND b.id_user = '" . $user['pegawai_nik'] . "'");
      $data_avp_bagian = $sql_avp_bagian->row_array();
    }

    $sql_pic = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi a LEFT JOIN dec.dec_pekerjaan b ON b.pic=a.id_user WHERE pekerjaan_disposisi_status='8' and is_aktif='y' AND is_cc is null AND pic='" . $user['pegawai_nik'] . "'");

    $data_pic = $sql_pic->row_array();

    $sql_vp = $this->db->query("SELECT count(*) as total FROM global.global_pegawai WHERE pegawai_poscode = 'E53000000' AND pegawai_nik = '" . $user['pegawai_nik'] . "'");
    $data_vp = $sql_vp->row_array();

    $sql_staf = $this->db->query("SELECT count(*) as total FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $value['id_bagian'] . "' AND pekerjaan_disposisi_status = '5' AND id_pekerjaan = '" . $_GET['id_pekerjaan'] . "' AND b.id_user = '" . $user['pegawai_nik'] . "'");
    $data_staf = $sql_staf->row_array();

    $isi['pic'] = ($data_pic['total'] > 0) ? 'y' : 'n';
    $isi['avp'] = ($data_avp_bagian['total'] > 0) ? 'y' : 'n';
    $isi['vp'] = ($data_vp['total'] > 0) ? 'y' : 'n';
    $isi['bagian'] = (!empty($data_avp_bagian_nama['bagian_nama'])) ? $data_avp_bagian_nama['bagian_nama'] : '';
    $isi['cc'] = (!empty($data_cc)) ? $data_cc['is_cc'] : 'n';
    $isi['staf'] = ($data_staf['total'] > 0) ? 'y' : 'n';

    array_push($data, $isi);
  }
  echo json_encode($data);
}
}

public function getAsetDocumentApproveAVP()
{
	$param['pekerjaan_disposisi_status'] = $this->input->get_post('pekerjaan_disposisi_status');
	$param['id_pekerjaan'] = $this->input->get_post('id_pekerjaan');

	$data = $this->M_pekerjaan->getAsetDocumentApproveAVP($param);
	echo json_encode($data);
}

public function getAsetDocumentApproveVP()
{
	$param['id_pekerjaan'] = $this->input->get_post('id_pekerjaan');

	$data = $this->M_pekerjaan->getAsetDocumentApproveVP($param);
	echo json_encode($data);
}


public function getDisposisi()
{
	$param = array();

	$param['id_pekerjaan'] = $this->input->get('pekerjaan_id');

	$data = $this->M_pekerjaan->getDisposisi($param);

	echo json_encode($data);
}

public function getUnitDepartemen()
{
	$data = $this->M_pekerjaan->getUnitDepartemen();
	echo json_encode($data);
}

public function getApproveVP()
{
	$param['pekerjaan_dokumen_id'] = $this->input->get_post('pekerjaan_dokumen_id');

	$data = $this->M_pekerjaan->getApproveVP($param);
	echo json_encode($data);
}
/* GET */

/* INSERT */
public function approveVP()
{
	$pekerjaan_id = $this->input->post('id_pekerjaan');
	if ($pekerjaan_id) {
    $data['pekerjaan_status'] = 'pim';
    $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
  }
}

public function approveAVP()
{
	$pekerjaan_id = $this->input->post('id_pekerjaan');
	if ($pekerjaan_id) {
    $data['pekerjaan_status'] = 'man';
    $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
  }
}

public function insertAsetDocument()
{
	if (isset($_GET['id_user'])) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $user = $sql_user->row_array();
  } else {
    $user = $this->session->userdata();
  }

  /* Dokumen Penomoran */
  $sql_penomoran = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen_penomoran WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "'");
  $penomoran = $sql_penomoran->row_array();
  /* Dokumen Penomoran */

  /* Template */
  $sql_template = $this->db->query("SELECT * FROM dec.dec_pekerjaan_template WHERE pekerjaan_template_id = '" . $this->input->get_post('pekerjaan_template_nama') . "'");
  $template = $sql_template->row_array();
  /* Template */

  /* Bidang */
  $sql_bidang = $this->db->query("SELECT * FROM global.global_bidang WHERE bidang_id = '" . $this->input->get_post('bidang_nama') . "'");
  $bidang = $sql_bidang->row_array();
  /* Bidang */

  /* Urutan Proyek */
  $sql_urutan_proyek = $this->db->query("SELECT * FROM global.global_urutan_proyek WHERE urutan_proyek_id = '" . $penomoran['urutan_proyek_default'] . "'");
  $urutan_proyek = $sql_urutan_proyek->row_array();
  /* Urutan Proyek */

  /* Sectiona Area */
  $sql_section_area = $this->db->query("SELECT * FROM global.global_section_area WHERE section_area_id = '" . $penomoran['section_area_default'] . "'");
  $section_area = $sql_section_area->row_array();
  /* Sectiona Area */

  if ($this->input->get_post('pekerjaan_dokumen_jenis') == 'Gambar') $nomor = '34-' . $bidang['bidang_kode'] . '-' . $template['pekerjaan_template_kode'] . '-' . $urutan_proyek['urutan_proyek_kode'] . '-' . $section_area['section_area_kode'];
  else $nomor = '34-J' . $bidang['bidang_kode'] . '-' . $template['pekerjaan_template_kode'] . '-' . $urutan_proyek['urutan_proyek_kode'] . '-' . $section_area['section_area_kode'];

  $sql_nomor = $this->db->query("SELECT max(cast(split_part(pekerjaan_dokumen_nomor, '-', 6) as INT)) as terakhir FROM dec.dec_pekerjaan_dokumen WHERE 1=1 AND UPPER(pekerjaan_dokumen_nomor) LIKE '%" . strtoupper($nomor) . "%'");
  $dataNomor = $sql_nomor->row_array();
  $pekerjaan_dokumen_nomor = $nomor . '-' . sprintf("%02d", $dataNomor['terakhir'] + 1);

  $data['pekerjaan_dokumen_id'] = create_id();
  $data['id_pekerjaan'] = htmlentities($this->input->get_post('pekerjaan_id'));
  $data['pekerjaan_dokumen_nama'] = htmlentities($this->input->get_post('pekerjaan_dokumen_nama'));
  $data['id_pekerjaan_template'] = htmlentities($this->input->get_post('pekerjaan_template_nama'));
  $data['pekerjaan_dokumen_file'] = htmlentities($this->input->get_post('savedFileName'));
  $data['pekerjaan_dokumen_status'] = htmlentities('1');
  $data['pekerjaan_dokumen_status_review'] = htmlentities('1');
  $data['who_create'] = htmlentities($user['pegawai_nama']);
  $data['id_create'] = htmlentities($user['pegawai_nik']);
  $data['is_lama'] = htmlentities('n');
  $data['pekerjaan_dokumen_awal'] = 'n';
  if (($this->input->get_post('is_hps'))) {
    $data['is_hps'] = htmlentities($this->input->get_post('is_hps'));
  }
  $data['id_create_awal'] = htmlentities($user['pegawai_nik']);
					// $data['pekerjaan_dokumen_nomor'] = $this->input->post('pekerjaan_dokumen_nomor');
  $data['pekerjaan_dokumen_nomor'] = $pekerjaan_dokumen_nomor;
  $data['pekerjaan_dokumen_jumlah'] = $this->input->get_post('pekerjaan_dokumen_jumlah');
  $data['pekerjaan_dokumen_cc'] = $this->input->get_post('pegawai_nama');
  $data['is_proses'] = null;
  $data['pekerjaan_dokumen_jenis'] = htmlentities($this->input->get_post('pekerjaan_dokumen_jenis'));
  $data['pekerjaan_dokumen_kertas'] = htmlentities($this->input->get_post('pekerjaan_dokumen_kertas'));
  $data['pekerjaan_dokumen_orientasi'] = htmlentities($this->input->get_post('pekerjaan_dokumen_orientasi'));
  $data['id_bidang'] = htmlentities($this->input->get_post('bidang_nama'));
  $data['id_urutan_proyek'] = htmlentities($penomoran['urutan_proyek_default']);
  $data['id_section_area'] = htmlentities($penomoran['section_area_default']);
  $data['pekerjaan_dokumen_waktu_input'] = date('Y-m-d H:i:s');
  $data['id_dokumen_awal'] = bin2hex(random_bytes(16));
  $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');

  $this->M_pekerjaan->insertPekerjaanDokumen($data);

  $data_template = $this->db->get_where('dec.dec_pekerjaan_template', array('pekerjaan_template_id' => $this->input->get_post('pekerjaan_template_nama')))->row_array();

  dblog('I', $data['id_pekerjaan'], 'Dokumen ' . $data_template['pekerjaan_template_nama'] . ' - ' . $data['pekerjaan_dokumen_nama'] . ' Telah Diupload');
}

public function insertAsetDocumentIFC()
{
	if (isset($_GET['id_user'])) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $user = $sql_user->row_array();
  } else {
    $user = $this->session->userdata();
  }

  /* Template */
  $sql_template = $this->db->query("SELECT * FROM dec.dec_pekerjaan_template WHERE pekerjaan_template_id = '" . $this->input->get_post('pekerjaan_template_nama') . "'");
  $template = $sql_template->row_array();
  /* Template */

  /* Bidang */
  $sql_bidang = $this->db->query("SELECT * FROM global.global_bidang WHERE bidang_id = '" . $this->input->get_post('bidang_nama') . "'");
  $bidang = $sql_bidang->row_array();
  /* Bidang */

  /* Urutan Proyek */
  $sql_urutan_proyek = $this->db->query("SELECT * FROM global.global_urutan_proyek WHERE urutan_proyek_id = '" . $this->input->get_post('urutan_proyek_nama') . "'");
  $urutan_proyek = $sql_urutan_proyek->row_array();
  /* Urutan Proyek */

  /* Sectiona Area */
  $sql_section_area = $this->db->query("SELECT * FROM global.global_section_area WHERE section_area_id = '" . $this->input->get_post('section_area_nama') . "'");
  $section_area = $sql_section_area->row_array();
  /* Sectiona Area */

  if ($this->input->get_post('pekerjaan_dokumen_jenis') == 'Gambar') $nomor = '34-' . $bidang['bidang_kode'] . '-' . $template['pekerjaan_template_kode'] . '-' . $urutan_proyek['urutan_proyek_kode'] . '-' . $section_area['section_area_kode'];
  else $nomor = '34-J' . $bidang['bidang_kode'] . '-' . $template['pekerjaan_template_kode'] . '-' . $urutan_proyek['urutan_proyek_kode'] . '-' . $section_area['section_area_kode'];

  $sql_nomor = $this->db->query("SELECT max(cast(split_part(pekerjaan_dokumen_nomor, '-', 6) as INT)) as terakhir FROM dec.dec_pekerjaan_dokumen WHERE 1=1 AND UPPER(pekerjaan_dokumen_nomor) LIKE '%" . strtoupper($nomor) . "%'");
  $dataNomor = $sql_nomor->row_array();
  $pekerjaan_dokumen_nomor = $nomor . '-' . sprintf("%02d", $dataNomor['terakhir'] + 1);

  $data['pekerjaan_dokumen_id'] = create_id();
  $data['id_pekerjaan'] = anti_inject($this->input->get_post('pekerjaan_id'));
  $data['pekerjaan_dokumen_nama'] = anti_inject($this->input->get_post('pekerjaan_dokumen_nama'));
  $data['id_pekerjaan_template'] = anti_inject($this->input->get_post('pekerjaan_template_nama'));
  $data['pekerjaan_dokumen_file'] = anti_inject($this->input->get_post('savedFileName'));
  $data['pekerjaan_dokumen_status'] = anti_inject('8');
  $data['who_create'] = anti_inject($user['pegawai_nama']);
  $data['id_create'] = anti_inject($user['pegawai_nik']);
  $data['is_lama'] = anti_inject('n');
  $data['pekerjaan_dokumen_awal'] = anti_inject('n');
  $data['id_create_awal'] = anti_inject($user['pegawai_nik']);
  $data['pekerjaan_dokumen_nomor'] = $pekerjaan_dokumen_nomor;
					// $data['pekerjaan_dokumen_nomor'] = $this->input->post('pekerjaan_dokumen_nomor');
  $data['pekerjaan_dokumen_jumlah'] = $this->input->get_post('pekerjaan_dokumen_jumlah');
  if (($this->input->get_post('is_hps'))) {
    $data['is_hps'] = anti_inject($this->input->get_post('is_hps'));
  }
  $data['pekerjaan_dokumen_jenis'] = $this->input->get_post('pekerjaan_dokumen_jenis');
  $data['id_bidang'] = $this->input->get_post('bidang_nama');
  $data['id_urutan_proyek'] = $this->input->get_post('urutan_proyek_nama');
  $data['id_section_area'] = $this->input->get_post('urutan_proyek_nama');
  $data['pekerjaan_dokumen_kertas'] = $this->input->get_post('pekerjaan_dokumen_kertas');
  $data['pekerjaan_dokumen_orientasi'] = $this->input->get_post('pekerjaan_dokumen_orientasi');
  $data['pekerjaan_dokumen_waktu_input'] = date('Y-m-d H:i:s');
  $data['id_dokumen_awal'] = bin2hex(random_bytes(16));
  $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
  $this->M_pekerjaan->insertPekerjaanDokumen($data);

  $data_template = $this->db->get_where('dec.dec_pekerjaan_template', array('pekerjaan_template_id' => $this->input->get_post('pekerjaan_template_nama')))->row_array();

  dblog('I', $data['id_pekerjaan'], 'Dokumen ' . $data_template['pekerjaan_template_nama'] . ' - ' . $data['pekerjaan_dokumen_nama'] . ' Telah Diupload', $user['pegawai_nik']);
}

public function updateAsetDocument()
{
	if (isset($_GET['id_user'])) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $user = $sql_user->row_array();
  } else {
    $user = $this->session->userdata();
  }

  /*nomor dokumen*/
  /* Dokumen Penomoran */
  $sql_penomoran = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen_penomoran WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "'");
  $penomoran = $sql_penomoran->row_array();
  /* Dokumen Penomoran */

  /* Template */
  $sql_template = $this->db->query("SELECT * FROM dec.dec_pekerjaan_template WHERE pekerjaan_template_id = '" . $this->input->get_post('pekerjaan_template_nama') . "'");
  $template = $sql_template->row_array();
  /* Template */

  /* Bidang */
  $sql_bidang = $this->db->query("SELECT * FROM global.global_bidang WHERE bidang_id = '" . $this->input->get_post('bidang_nama') . "'");
  $bidang = $sql_bidang->row_array();
  /* Bidang */

  /* Urutan Proyek */
  $sql_urutan_proyek = $this->db->query("SELECT * FROM global.global_urutan_proyek WHERE urutan_proyek_id = '" . $penomoran['urutan_proyek_default'] . "'");
  $urutan_proyek = $sql_urutan_proyek->row_array();
  /* Urutan Proyek */

  /* Sectiona Area */
  $sql_section_area = $this->db->query("SELECT * FROM global.global_section_area WHERE section_area_id = '" . $penomoran['section_area_default'] . "'");
  $section_area = $sql_section_area->row_array();
  /* Sectiona Area */

  if ($this->input->get_post('pekerjaan_dokumen_jenis') == 'Gambar') $nomor = '34-' . $bidang['bidang_kode'] . '-' . $template['pekerjaan_template_kode'] . '-' . $urutan_proyek['urutan_proyek_kode'] . '-' . $section_area['section_area_kode'];
  else $nomor = '34-J' . $bidang['bidang_kode'] . '-' . $template['pekerjaan_template_kode'] . '-' . $urutan_proyek['urutan_proyek_kode'] . '-' . $section_area['section_area_kode'];

  $sql_nomor = $this->db->query("SELECT max(cast(split_part(pekerjaan_dokumen_nomor, '-', 6) as INT)) as terakhir FROM dec.dec_pekerjaan_dokumen WHERE 1=1 AND UPPER(pekerjaan_dokumen_nomor) LIKE '%" . strtoupper($nomor) . "%'");
  $dataNomor = $sql_nomor->row_array();

  $dataDokumen = $this->db->get_where('dec.dec_pekerjaan_dokumen', ['pekerjaan_dokumen_id' => $this->input->post('pekerjaan_dokumen_id')])->row_array();

  $dataNomorIni = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_dokumen WHERE UPPER(pekerjaan_dokumen_nomor) LIKE '%" . strtoupper($nomor) . "%' AND pekerjaan_dokumen_id = '" . $this->input->post('pekerjaan_dokumen_id') . "'")->row_array();
					// print_r($dataNomorIni);

  $pekerjaan_dokumen_nomor = $nomor . '-' . sprintf("%02d", $dataNomor['terakhir'] + 1);
					// print_r($nomor);
  /*nomor dokumen*/

  /*    cek apakah dokumen revisi atau bukan*/
  $data_dokumen_revisi = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id ='" . $this->input->get_post('pekerjaan_dokumen_id') . "' AND pekerjaan_dokumen_status='0'")->row_array();
  $data_id_pekerjaan = $this->db->query("SELECT id_pekerjaan FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->get_post('pekerjaan_dokumen_id') . "'")->row_array();
  if ($data_dokumen_revisi['total'] > 0) {
    $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as max_rev FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->post('pekerjaan_dokumen_id') . "' AND pekerjaan_dokumen_status = '0'")->row_array();
    $nomor_revisi_baru = $nomor_revisi['max_rev'];
    if ($this->input->get_post('savedFileName') == '') {
      $data['pekerjaan_dokumen_id_temp'] = anti_inject($this->input->post('pekerjaan_dokumen_id'));
      $data['pekerjaan_dokumen_id'] = anti_inject(create_id());
      $data['pekerjaan_dokumen_nama'] = anti_inject($this->input->post('pekerjaan_dokumen_nama'));
      $data['pekerjaan_dokumen_jumlah'] = anti_inject($this->input->post('pekerjaan_dokumen_jumlah'));
      $data['id_pekerjaan_template'] = anti_inject($this->input->get_post('pekerjaan_template_nama'));
      $data['pekerjaan_dokumen_status'] = anti_inject('1');
      $data['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
      $data['pekerjaan_dokumen_keterangan'] = anti_inject($this->input->post('pekerjaan_dokumen_keterangan'));
      $data['is_proses'] = null;
      $data['id_create'] = $user['pegawai_nik'];
      $data['pekerjaan_dokumen_waktu'] = date('Y-m-d H:i:s');
      $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
      $this->M_pekerjaan->simpanAksiSamaRevisi($data);
      $data_template = $this->db->get_where('dec.dec_pekerjaan_template', array('pekerjaan_template_id' => $this->input->get_post('pekerjaan_template_nama')))->row_array();
      dblog('I', $data_id_pekerjaan['id_pekerjaan'], 'Dokumen ' . $data_template['pekerjaan_template_nama'] . ' - ' . $data['pekerjaan_dokumen_nama'] . ' Telah Diedit', $user['pegawai_nik']);
    } else {
      $data['pekerjaan_dokumen_id_temp'] = anti_inject($this->input->post('pekerjaan_dokumen_id'));
      $data['pekerjaan_dokumen_id'] = anti_inject(create_id());
      $data['pekerjaan_dokumen_nama'] = anti_inject($this->input->post('pekerjaan_dokumen_nama'));
      $data['id_pekerjaan_template'] = anti_inject($this->input->get_post('pekerjaan_template_nama'));
      $data['pekerjaan_dokumen_jumlah'] = anti_inject($this->input->post('pekerjaan_dokumen_jumlah'));
      $data['pekerjaan_dokumen_file'] = $this->input->get_post('savedFileName');
      $data['pekerjaan_dokumen_status'] = anti_inject('1');
      $data['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
      $data['is_proses'] = null;
      $data['id_create'] = $user['pegawai_nik'];
      $data['pekerjaan_dokumen_waktu'] = date('Y-m-d H:i:s');
      $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
      $this->M_pekerjaan->simpanAksiRevisi($data);

      $data_template = $this->db->get_where('dec.dec_pekerjaan_template', array('pekerjaan_template_id' => $this->input->get_post('pekerjaan_template_nama')))->row_array();

      dblog('U', $data_id_pekerjaan['id_pekerjaan'], 'Dokumen ' . $data_template['pekerjaan_template_nama'] . ' - ' . $data['pekerjaan_dokumen_nama'] . ' Telah Diedit', $user['pegawai_nik']);
    }
    $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET is_lama = 'y' WHERE pekerjaan_dokumen_id ='" . $this->input->get_post('pekerjaan_dokumen_id') . "' AND pekerjaan_dokumen_status='0'");
  } else {
   $id = anti_inject($this->input->get_post('pekerjaan_dokumen_id'));
   $data['id_pekerjaan'] = anti_inject($this->input->get_post('id_pekerjaan'));
   $data['pekerjaan_dokumen_nama'] = anti_inject($this->input->get_post('pekerjaan_dokumen_nama'));
   $data['pekerjaan_dokumen_jumlah'] = anti_inject($this->input->post('pekerjaan_dokumen_jumlah'));
   $data['id_pekerjaan_template'] = anti_inject($this->input->get_post('pekerjaan_template_nama'));
   if ($this->input->get_post('savedFileName') != '') {
     $data['pekerjaan_dokumen_file'] = anti_inject($this->input->get_post('savedFileName'));
   }
   $data['pekerjaan_dokumen_status'] = anti_inject('1');
   $data['pekerjaan_dokumen_status_review'] = anti_inject('1');
   $data['who_create'] = anti_inject($user['pegawai_nama']);
   $data['id_create'] = anti_inject($user['pegawai_nik']);
   $data['is_lama'] = anti_inject('n');
   $data['pekerjaan_dokumen_awal'] = anti_inject('n');
   if (($this->input->get_post('is_hps'))) {
     $data['is_hps'] = anti_inject($this->input->get_post('is_hps'));
   }
   $data['id_create_awal'] = anti_inject($user['pegawai_nik']);
   if ($dataNomorIni['total'] == 0) {
     $data['pekerjaan_dokumen_nomor'] = $pekerjaan_dokumen_nomor;
   } else {
     $data['pekerjaan_dokumen_nomor'] = $dataDokumen['pekerjaan_dokumen_nomor'];
   }
							 // $data['pekerjaan_dokumen_nomor'] = $this->input->get_post('pekerjaan_dokumen_nomor');
   $data['pekerjaan_dokumen_cc'] = $this->input->get_post('pegawai_nama');
   $data['is_proses'] = null;
							 // $data['pekerjaan_dokumen_waktu_input'] = date('Y-m-d H:i:s');
   $data['id_bidang'] = htmlentities($this->input->get_post('bidang_nama'));
   $data['id_urutan_proyek'] = htmlentities($penomoran['urutan_proyek_default']);
   $data['id_section_area'] = htmlentities($penomoran['section_area_default']);
   $data['pekerjaan_dokumen_jenis'] = htmlentities($this->input->get_post('pekerjaan_dokumen_jenis'));
   $data['pekerjaan_dokumen_kertas'] = htmlentities($this->input->get_post('pekerjaan_dokumen_kertas'));
   $data['pekerjaan_dokumen_orientasi'] = htmlentities($this->input->get_post('pekerjaan_dokumen_orientasi'));
   $this->M_pekerjaan->updatePekerjaanDokumen($data, $id);
							 // echo $this->db->last_query();
   $data_template = $this->db->get_where('dec.dec_pekerjaan_template', array('pekerjaan_template_id' => $this->input->get_post('pekerjaan_template_nama')))->row_array();

   dblog('U', $data['id_pekerjaan'], 'Dokumen ' . $data_template['pekerjaan_template_nama'] . ' - ' . $data['pekerjaan_dokumen_nama'] . ' Telah Diedit', $user['pegawai_nik']);
 }
}

public function updateAsetDocumentLangsung()
{
	if (isset($_GET['id_user'])) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $user = $sql_user->row_array();
  } else {
    $user = $this->session->userdata();
  }

  /* Template */
  $sql_template = $this->db->query("SELECT * FROM dec.dec_pekerjaan_template WHERE pekerjaan_template_id = '" . $this->input->get_post('pekerjaan_template_nama') . "'");
  $template = $sql_template->row_array();
  /* Template */

  /* Bidang */
  $sql_bidang = $this->db->query("SELECT * FROM global.global_bidang WHERE bidang_id = '" . $this->input->get_post('bidang_nama') . "'");
  $bidang = $sql_bidang->row_array();
  /* Bidang */

  /* Urutan Proyek */
  $sql_urutan_proyek = $this->db->query("SELECT * FROM global.global_urutan_proyek WHERE urutan_proyek_id = '" . $this->input->get_post('urutan_proyek_nama') . "'");
  $urutan_proyek = $sql_urutan_proyek->row_array();
  /* Urutan Proyek */

  /* Sectiona Area */
  $sql_section_area = $this->db->query("SELECT * FROM global.global_section_area WHERE section_area_id = '" . $this->input->get_post('section_area_nama') . "'");
  $section_area = $sql_section_area->row_array();
  /* Sectiona Area */

  if ($this->input->get_post('pekerjaan_dokumen_jenis') == 'Gambar') $nomor = '34-' . $bidang['bidang_kode'] . '-' . $template['pekerjaan_template_kode'] . '-' . $urutan_proyek['urutan_proyek_kode'] . '-' . $section_area['section_area_kode'];
  else $nomor = '34-J' . $bidang['bidang_kode'] . '-' . $template['pekerjaan_template_kode'] . '-' . $urutan_proyek['urutan_proyek_kode'] . '-' . $section_area['section_area_kode'];

  $sql_nomor = $this->db->query("SELECT max(cast(split_part(pekerjaan_dokumen_nomor, '-', 6) as INT)) as terakhir FROM dec.dec_pekerjaan_dokumen WHERE 1=1 AND UPPER(pekerjaan_dokumen_nomor) LIKE '%" . strtoupper($nomor) . "%'");
  $dataNomor = $sql_nomor->row_array();

  $dataNomorIni = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_dokumen WHERE UPPER(pekerjaan_dokumen_nomor) LIKE '%" . strtoupper($nomor) . "%' AND pekerjaan_dokumen_id = '" . $this->input->post('pekerjaan_dokumen_id') . "'")->row_array();

  $pekerjaan_dokumen_nomor = $nomor . '-' . sprintf("%02d", $dataNomor['terakhir'] + 1);

  /*cek apakah dokumen revisi atau bukan*/
  $sql_dokumen_revisi = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id ='" . $this->input->get_post('pekerjaan_dokumen_id') . "' AND pekerjaan_dokumen_status='0'");

  $data_dokumen_revisi = $sql_dokumen_revisi->row_array();

  $sql_jumlah_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as maks FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id ='" . $this->input->get_post('pekerjaan_dokumen_id') . "' AND pekerjaan_dokumen_status='0'");

  $data_jumlah_revisi = $sql_jumlah_revisi->row_array();

  $data_id_pekerjaan = $this->db->query("SELECT id_pekerjaan FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->get_post('pekerjaan_dokumen_id') . "'")->row_array();


  if ($data_dokumen_revisi['total'] > 0) {
    if ($this->input->get_post('savedFileName') == '') {
      $data['pekerjaan_dokumen_id_temp'] = anti_inject($this->input->post('pekerjaan_dokumen_id'));
      $data['pekerjaan_dokumen_id'] = anti_inject(create_id());
      $data['pekerjaan_dokumen_nama'] = anti_inject($this->input->post('pekerjaan_dokumen_nama'));
      $data['pekerjaan_dokumen_jumlah'] = anti_inject($this->input->post('pekerjaan_dokumen_jumlah'));
      $data['id_pekerjaan_template'] = anti_inject($this->input->get_post('pekerjaan_template_nama'));
      $data['pekerjaan_dokumen_keterangan'] = anti_inject($this->input->post('pekerjaan_dokumen_keterangan'));
      $data['pekerjaan_dokumen_status'] = anti_inject($this->input->post('pekerjaan_dokumen_status'));
      $data['pekerjaan_dokumen_revisi'] = anti_inject($this->input->post('pekerjaan_dokumen_revisi'));
      $data['pekerjaan_dokumen_waktu_update'] = anti_inject($this->input->post('pekerjaan_dokumen_waktu_update'));
      $data['id_create'] = anti_inject($this->input->post('id_create'));
      $data['is_proses'] = null;
      $data['pekerjaan_dokumen_waktu'] = date('Y-m-d H:i:s');
      $this->M_pekerjaan->simpanAksiSamaRevisi($data);

      $data_template = $this->db->get_where('dec.dec_pekerjaan_template', array('pekerjaan_template_id' => $this->input->get_post('pekerjaan_template_nama')))->row_array();

      dblog('I', $data_id_pekerjaan['id_pekerjaan'], 'Dokumen ' . $data_template['pekerjaan_template_nama'] . ' - ' . $data['pekerjaan_dokumen_nama'] . ' Telah Diedit', $user['pegawai_nik']);
    } else {
      $data['pekerjaan_dokumen_id_temp'] = anti_inject($this->input->post('pekerjaan_dokumen_id'));
      $data['pekerjaan_dokumen_id'] = anti_inject(create_id());
      $data['pekerjaan_dokumen_nama'] = anti_inject($this->input->post('pekerjaan_dokumen_nama'));
      $data['id_pekerjaan_template'] = anti_inject($this->input->get_post('pekerjaan_template_nama'));
      $data['pekerjaan_dokumen_jumlah'] = anti_inject($this->input->post('pekerjaan_dokumen_jumlah'));
      $data['pekerjaan_dokumen_file'] = $this->input->get_post('savedFileName');
      $data['pekerjaan_dokumen_keterangan'] = anti_inject($this->input->post('pekerjaan_dokumen_keterangan'));
      $data['pekerjaan_dokumen_status'] = anti_inject($this->input->post('pekerjaan_dokumen_status'));
      $data['pekerjaan_dokumen_revisi'] = anti_inject($this->input->post('pekerjaan_dokumen_revisi'));
      $data['pekerjaan_dokumen_waktu_update'] = anti_inject($this->input->post('pekerjaan_dokumen_waktu_update'));
      $data['id_create'] = anti_inject($this->input->post('id_create'));
      $data['is_proses'] = null;
      $data['pekerjaan_dokumen_waktu'] = date('Y-m-d H:i:s');
      $this->M_pekerjaan->simpanAksiRevisi($data);

      $data_template = $this->db->get_where('dec.dec_pekerjaan_template', array('pekerjaan_template_id' => $this->input->get_post('pekerjaan_template_nama')))->row_array();

      dblog('U', $data_id_pekerjaan['id_pekerjaan'], 'Dokumen ' . $data_template['pekerjaan_template_nama'] . ' - ' . $data['pekerjaan_dokumen_nama'] . ' Telah Diedit', $user['pegawai_nik']);
    }
    $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET is_lama = 'y' WHERE pekerjaan_dokumen_id ='" . $this->input->get_post('pekerjaan_dokumen_id') . "' AND pekerjaan_dokumen_status='0'");
  } else {
   $id = anti_inject($this->input->get_post('pekerjaan_dokumen_id'));
   $data['id_pekerjaan'] = anti_inject($this->input->get_post('id_pekerjaan'));
   $data['pekerjaan_dokumen_nama'] = anti_inject($this->input->get_post('pekerjaan_dokumen_nama'));
   $data['pekerjaan_dokumen_jumlah'] = anti_inject($this->input->post('pekerjaan_dokumen_jumlah'));
   $data['id_pekerjaan_template'] = anti_inject($this->input->get_post('pekerjaan_template_nama'));
   if ($this->input->get_post('savedFileName') != '') {
     $data['pekerjaan_dokumen_file'] = anti_inject($this->input->get_post('savedFileName'));
   }
							 /*      $data['pekerjaan_dokumen_status'] = anti_inject('1');
			$data['pekerjaan_dokumen_status_review'] = anti_inject('1');
			$data['who_create'] = anti_inject($user['pegawai_nama']);
			$data['id_create'] = anti_inject($user['pegawai_nik']);*/
			$data['is_lama'] = anti_inject('n');
			$data['pekerjaan_dokumen_awal'] = anti_inject('n');
			if (($this->input->get_post('is_hps'))) {
				$data['is_hps'] = anti_inject($this->input->get_post('is_hps'));
			}
			/*      $data['id_create_awal'] = anti_inject($user['pegawai_nik']);*/
							 // $data['pekerjaan_dokumen_nomor'] = $pekerjaan_dokumen_nomor;
							 // $data['pekerjaan_dokumen_nomor'] = $this->input->get_post('pekerjaan_dokumen_nomor');
			$data['pekerjaan_dokumen_cc'] = $this->input->get_post('pegawai_nama');
			$data['is_proses'] = null;
			/*$data['pekerjaan_dokumen_waktu'] = date('Y-m-d H:i:s');*/

							 // if ($dataNomorIni['total'] == 0) {
							 // $data['pekerjaan_dokumen_nomor'] = $pekerjaan_dokumen_nomor;
							 // }
							 // $data['pekerjaan_dokumen_nomor'] = $this->input->get_post('pekerjaan_dokumen_nomor');
							 // $data['pekerjaan_dokumen_waktu_input'] = date('Y-m-d H:i:s');
			$data['id_bidang'] = htmlentities($this->input->get_post('bidang_nama'));
			$data['id_urutan_proyek'] = htmlentities($this->input->get_post('urutan_proyek_nama'));
			$data['id_section_area'] = htmlentities($this->input->get_post('section_area_nama'));
			$data['pekerjaan_dokumen_jenis'] = htmlentities($this->input->get_post('pekerjaan_dokumen_jenis'));
			$data['pekerjaan_dokumen_kertas'] = htmlentities($this->input->get_post('pekerjaan_dokumen_kertas'));
			$data['pekerjaan_dokumen_orientasi'] = htmlentities($this->input->get_post('pekerjaan_dokumen_orientasi'));

			$this->M_pekerjaan->updatePekerjaanDokumen($data, $id);
			$data_template = $this->db->get_where('dec.dec_pekerjaan_template', array('pekerjaan_template_id' => $this->input->get_post('pekerjaan_template_nama')))->row_array();

			dblog('U', $data['id_pekerjaan'], 'Dokumen ' . $data_template['pekerjaan_template_nama'] . ' - ' . $data['pekerjaan_dokumen_nama'] . ' Telah Diedit', $user['pegawai_nik']);
		}
	}

	public function updateAsetDocumentIFC()
	{
		if (isset($_GET['id_user'])) {
     $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
     $user = $sql_user->row_array();
   } else {
     $user = $this->session->userdata();
   }

   /* Template */
   $sql_template = $this->db->query("SELECT * FROM dec.dec_pekerjaan_template WHERE pekerjaan_template_id = '" . $this->input->get_post('pekerjaan_template_nama') . "'");
   $template = $sql_template->row_array();
   /* Template */

   /* Bidang */
   $sql_bidang = $this->db->query("SELECT * FROM global.global_bidang WHERE bidang_id = '" . $this->input->get_post('bidang_nama') . "'");
   $bidang = $sql_bidang->row_array();
   /* Bidang */

   /* Urutan Proyek */
   $sql_urutan_proyek = $this->db->query("SELECT * FROM global.global_urutan_proyek WHERE urutan_proyek_id = '" . $this->input->get_post('urutan_proyek_nama') . "'");
   $urutan_proyek = $sql_urutan_proyek->row_array();
   /* Urutan Proyek */

   /* Sectiona Area */
   $sql_section_area = $this->db->query("SELECT * FROM global.global_section_area WHERE section_area_id = '" . $this->input->get_post('section_area_nama') . "'");
   $section_area = $sql_section_area->row_array();
   /* Sectiona Area */

   if ($this->input->get_post('pekerjaan_dokumen_jenis') == 'Gambar') $nomor = '34-' . $bidang['bidang_kode'] . '-' . $template['pekerjaan_template_kode'] . '-' . $urutan_proyek['urutan_proyek_kode'] . '-' . $section_area['section_area_kode'];
   else $nomor = '34-J' . $bidang['bidang_kode'] . '-' . $template['pekerjaan_template_kode'] . '-' . $urutan_proyek['urutan_proyek_kode'] . '-' . $section_area['section_area_kode'];

   $dataDokumen = $this->db->get_where('dec.dec_pekerjaan_dokumen', ['pekerjaan_dokumen_id' => $this->input->post('pekerjaan_dokumen_id')])->row_array();

   $dataNomorIni = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_dokumen WHERE UPPER(pekerjaan_dokumen_nomor) LIKE '%" . strtoupper($nomor) . "%' AND pekerjaan_dokumen_id = '" . $this->input->post('pekerjaan_dokumen_id') . "'")->row_array();


   $sql_nomor = $this->db->query("SELECT max(cast(split_part(pekerjaan_dokumen_nomor, '-', 6) as INT)) as terakhir FROM dec.dec_pekerjaan_dokumen WHERE 1=1 AND UPPER(pekerjaan_dokumen_nomor) LIKE '%" . strtoupper($nomor) . "%'");
   $dataNomor = $sql_nomor->row_array();
   $pekerjaan_dokumen_nomor = $nomor . '-' . sprintf("%02d", $dataNomor['terakhir'] + 1);

   /*jika dokumen dengan status 5 maka insert baru*/
   if ($this->input->get_post('pekerjaan_dokumen_status') == '5' || $this->input->get_post('pekerjaan_dokumen_status') == '6' || $this->input->get_post('pekerjaan_dokumen_status') == '7' || $this->input->get_post('pekerjaan_dokumen_status') == '0') {
     $data['pekerjaan_dokumen_id'] = create_id();
     $data['id_pekerjaan'] = anti_inject($this->input->get_post('id_pekerjaan'));
     $data['pekerjaan_dokumen_nama'] = anti_inject($this->input->get_post('pekerjaan_dokumen_nama'));
     $data['id_pekerjaan_template'] = anti_inject($this->input->get_post('pekerjaan_template_nama'));
     if ($this->input->get_post('savedFileName') != '') {
       $data['pekerjaan_dokumen_file'] = anti_inject($this->input->get_post('savedFileName'));
     } else {
       $data['pekerjaan_dokumen_file'] = $this->input->get_post('fileName');
     }
     $data['pekerjaan_dokumen_status'] = '8';
     $data['who_create'] = anti_inject($user['pegawai_nama']);
     $data['id_create'] = anti_inject($user['pegawai_nik']);
     $data['pekerjaan_dokumen_awal'] = anti_inject('n');
     if ($dataNomorIni['total'] == 0) {
       $data['pekerjaan_dokumen_nomor'] = $pekerjaan_dokumen_nomor;
     } else {
       $data['pekerjaan_dokumen_nomor'] = $dataDokumen['pekerjaan_dokumen_nomor'];
     }
							 // $data['pekerjaan_dokumen_nomor'] = $pekerjaan_dokumen_nomor;
							 // $data['pekerjaan_dokumen_nomor'] = $this->input->post('pekerjaan_dokumen_nomor');
							 // $data['pekerjaan_dokumen_nomor'] = $pekerjaan_dokumen_nomor;
     if (($this->input->get_post('is_hps'))) {
       $data['is_hps'] = anti_inject($this->input->get_post('is_hps'));
     }
     $data['pekerjaan_dokumen_jumlah'] = ($this->input->get_post('pekerjaan_dokumen_jumlah') != '') ? $this->input->get_post('pekerjaan_dokumen_jumlah') : '0';
     $data['pekerjaan_dokumen_jenis'] = htmlentities($this->input->get_post('pekerjaan_dokumen_jenis'));
     $data['pekerjaan_dokumen_kertas'] = htmlentities($this->input->get_post('pekerjaan_dokumen_kertas'));
     $data['pekerjaan_dokumen_orientasi'] = htmlentities($this->input->get_post('pekerjaan_dokumen_orientasi'));
     $data['id_bidang'] = htmlentities($this->input->get_post('bidang_nama'));
     $data['id_urutan_proyek'] = htmlentities($this->input->get_post('urutan_proyek_nama'));
     $data['id_section_area'] = htmlentities($this->input->get_post('section_area_nama'));
     $data['pekerjaan_dokumen_waktu_input'] = date('Y-m-d H:i:s');
     $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
     $where['pekerjaan_dokumen_id'] = $this->input->get_post('pekerjaan_dokumen_id');

     $this->M_pekerjaan->simpanAksiIFC($data, $where);

     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET is_update_ifa = 'y' WHERE pekerjaan_dokumen_id = '" . $this->input->get_post('pekerjaan_dokumen_id') . "'");
     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET is_lama ='y' WHERE pekerjaan_dokumen_id = '" . $where['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status = '0'");

     $data_template = $this->db->get_where('dec.dec_pekerjaan_template', array('pekerjaan_template_id' => $this->input->get_post('pekerjaan_template_nama')))->row_array();

     dblog('U', $data['id_pekerjaan'], 'Dokumen ' . $data_template['pekerjaan_template_nama'] . ' - ' . $data['pekerjaan_dokumen_nama'] . ' Telah Diedit', $user['pegawai_nik']);
   } else {
    $id = anti_inject($this->input->get_post('pekerjaan_dokumen_id'));
    $data['id_pekerjaan'] = anti_inject($this->input->get_post('id_pekerjaan'));
    $data['pekerjaan_dokumen_nama'] = anti_inject($this->input->get_post('pekerjaan_dokumen_nama'));
    $data['id_pekerjaan_template'] = anti_inject($this->input->get_post('pekerjaan_template_nama'));
    if ($this->input->get_post('savedFileName') != '') {
      $data['pekerjaan_dokumen_file'] = anti_inject($this->input->get_post('savedFileName'));
    }
    $data['pekerjaan_dokumen_status'] = '8';
    $data['who_create'] = anti_inject($user['pegawai_nama']);
    $data['id_create'] = anti_inject($user['pegawai_nik']);
    $data['pekerjaan_dokumen_awal'] = anti_inject('n');
							 // $data['pekerjaan_dokumen_nomor'] = $this->input->post('pekerjaan_dokumen_nomor');
							 // $data['pekerjaan_dokumen_nomor'] = $this->input->post('pekerjaan_dokumen_nomor');
							 // $data['pekerjaan_dokumen_nomor'] = $pekerjaan_dokumen_nomor;
    if ($dataNomorIni['total'] == 0) {
      $data['pekerjaan_dokumen_nomor'] = $pekerjaan_dokumen_nomor;
    } else {
      $data['pekerjaan_dokumen_nomor'] = $dataDokumen['pekerjaan_dokumen_nomor'];
    }
    if (($this->input->get_post('is_hps'))) {
      $data['is_hps'] = anti_inject($this->input->get_post('is_hps'));
    }
    $data['pekerjaan_dokumen_jumlah'] = ($this->input->get_post('pekerjaan_dokumen_jumlah') != '') ? $this->input->get_post('pekerjaan_dokumen_jumlah') : '0';
    $data['pekerjaan_dokumen_jenis'] = htmlentities($this->input->get_post('pekerjaan_dokumen_jenis'));
    $data['pekerjaan_dokumen_kertas'] = htmlentities($this->input->get_post('pekerjaan_dokumen_kertas'));
    $data['pekerjaan_dokumen_orientasi'] = htmlentities($this->input->get_post('pekerjaan_dokumen_orientasi'));
    $data['id_bidang'] = htmlentities($this->input->get_post('bidang_nama'));
    $data['id_urutan_proyek'] = htmlentities($this->input->get_post('urutan_proyek_nama'));
    $data['id_section_area'] = htmlentities($this->input->get_post('section_area_nama'));
    $data['pekerjaan_dokumen_waktu_input'] = date('Y-m-d H:i:s');

    $this->M_pekerjaan->updatePekerjaanDokumen($data, $id);
    $data_template = $this->db->get_where('dec.dec_pekerjaan_template', array('pekerjaan_template_id' => $this->input->get_post('pekerjaan_template_nama')))->row_array();

    dblog('U', $data['id_pekerjaan'], 'Dokumen ' . $data_template['pekerjaan_template_nama'] . ' - ' . $data['pekerjaan_dokumen_nama'] . ' Telah Diedit', $user['pegawai_nik']);
  }
}


public function insertAsetDocumentDetail()
{
	if (isset($_GET['id_user'])) {
    $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $isi = $sql_isi->row_array();
  } else {
    $isi = $this->session->userdata();
  }
  $data['pekerjaan_dokumen_id'] = create_id();
  $data['id_pekerjaan'] = anti_inject($this->input->get_post('pekerjaan_id'));
  $data['pekerjaan_dokumen_nama'] = anti_inject($this->input->get_post('pekerjaan_dokumen_nama'));
  $data['pekerjaan_dokumen_file'] = anti_inject($this->input->get_post('savedFileName'));
  /*$data['pekerjaan_dokumen_waktu'] = date('Y-m-d H:i:s');*/

  $this->M_pekerjaan->insertPekerjaanDokumen($data);
  dblog('I', $data['id_pekerjaan'], 'Dokumen ' . $data['pekerjaan_dokumen_nama'] . ' Telah Diupload', $isi['pegawai_nik']);
}
/* INSERT */

/* UPDATE */
public function updatePekerjaanApprove()
{
	if ($this->input->get_post('id_user_avp')) {
    $user = $this->input->get_post('id_user_avp');
    foreach ($user as $key => $id_user) {
      $data['pekerjaan_disposisi_id'] = create_id();
      $data['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
      $data['id_user'] = anti_inject($id_user);
      $data['id_pekerjaan'] = anti_inject($this->input->get_post('id_pekerjaan_approve_avp'));
      $data['pekerjaan_disposisi_status'] = anti_inject('AVP');
      $this->M_pekerjaan->insertPekerjaanDisposisi($data);
    }
    $id = $this->input->get_post('id_pekerjaan_approve_avp');
  }
}

public function updateAsetDocumentApproveAVP()
{

	$user = (isset($_GET['id_user'])) ? $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $_GET['id_user']))->row_array() : $this->session->userdata();


	if ($this->input->get_post('usr_id')) {
    $id_user = (explode(',', $this->input->get_post('usr_id')));
    foreach ($id_user as $key => $id_usr) {
      /*insert dokumen baru dan ubah status dokumen lama ke non aktif agar dokumen baru yang ditampilkan*/
      $param['pekerjaan_disposisi_id'] = create_id();
      $param['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
      $param['id_user'] = anti_inject($id_usr);
      $param['id_pekerjaan'] = anti_inject($this->input->get_post('id_pekerjaan'));
      $param['pekerjaan_disposisi_status'] = anti_inject('AVP');
      $param['id_penanggung_jawab'] = anti_inject($this->input->get_post('usr_id_pj'));

      $this->M_pekerjaan->insertPekerjaanDisposisi($param, 'AVP');
    }

    $param1['pekerjaan_dokumen_id'] = create_id();
    $param1['id_pekerjaan'] = anti_inject($this->input->get_post('id_pekerjaan'));
    $param1['id_pekerjaan_disposisi'] = anti_inject($param['pekerjaan_disposisi_id']);
    $param1['pekerjaan_dokumen_nama'] = anti_inject($this->input->get_post('pekerjaan_dokumen_nama'));
    $param1['pekerjaan_dokumen_keterangan'] = anti_inject($this->input->get_post('pekerjaan_dokumen_keterangan'));
    $param1['pekerjaan_dokumen_status'] = anti_inject($this->input->get_post('pekerjaan_dokumen_status_nama'));
    $param1['pekerjaan_dokumen_file'] = $this->input->get_post('savedFileName');
    $param1['who_create'] = anti_inject($user['pegawai_nama']);
    $param1['who_create'] = anti_inject($user['pegawai_nik']);
    /*      $param1['pekerjaan_dokumen_waktu'] = date('Y-m-d H:i:s');*/

    $this->M_pekerjaan->insertPekerjaanDokumen($param1);
    dblog('U', $this->input->get_post('id_pekerjaan'), 'Dokumen ' . $this->input->get_post('pekerjaan_dokumen_nama') . ' Diedit', $user['pegawai_nik']);


    /*update dokumen lama*/
    $id = $this->input->get_post('pekerjaan_dokumen_id');
    $param1['is_lama'] = 'y';

    $this->M_pekerjaan->updatePekerjaanDokumen($param1, $id);
  }
}

public function updateAsetDocumentApproveVP($data = null)
{
	$user = (isset($_GET['id_user'])) ? $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $_GET['id_user']))->row_array() : $this->session->userdata();
	/*foreach($id_disposisi as $key=>$id_dis)*/
	/*insert dokumen baru dan ubah status dokumen lama ke non aktif agar dokumen baru yang ditampilkan*/
	$param['pekerjaan_dokumen_id'] = create_id();
	$param['id_pekerjaan'] = anti_inject($this->input->get_post('id_pekerjaan'));
	$param['pekerjaan_dokumen_nama'] = anti_inject($this->input->get_post('pekerjaan_dokumen_nama'));
	/*$param['pekerjaan_dokumen_departemen'] = $this->input->get_post('pekerjaan_dokumen_departemen');*/
	$param['pekerjaan_dokumen_keterangan'] = anti_inject($this->input->get_post('pekerjaan_dokumen_keterangan'));
	$param['pekerjaan_dokumen_status'] = anti_inject($this->input->get_post('pekerjaan_dokumen_status_nama'));
	$param['pekerjaan_dokumen_file'] = anti_inject($this->input->get_post('savedFileName'));
	/*    $param['id_pekerjaan_disposisi'] = $this->input->get_post('id_pekerjaan_disposisi');*/
	/*    $param['id_penanggung_jawab'] = $this->input->get_post('id_penanggung_jawab');*/
	$param['who_create'] = anti_inject($user['pegawai_nama']);
	$param['id_create'] = anti_inject($user['pegawai_nik']);
	/*    $param['pekerjaan_dokumen_waktu'] = date('Y-m-d H:i:s');*/

	$this->M_pekerjaan->insertPekerjaanDokumen($param);
	dblog('U', $this->input->get_post('id_pekerjaan'), 'Dokumen ' . $this->input->get_post('pekerjaan_dokumen_nama') . ' Diedit', $user['pegawai_nik']);

	/*update dokumen lama*/
	$id = $this->input->get_post('pekerjaan_dokumen_id');
	$param1['is_lama'] = 'y';

	$this->M_pekerjaan->updatePekerjaanDokumen($param1, $id);
}
/* UPDATE */

/* DELETE */
public function deleteAsetDocument2()
{
	if (isset($_GET['id_user'])) {
    $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $isi = $sql_isi->row_array();
  } else {
    $isi = $this->session->userdata();
  }
  $this->M_pekerjaan->deleteAsetDocument2($this->input->get('id_pekerjaan'));
  dblog('I', $this->input->get('id_pekerjaan'), 'Dokumen Telah Dihapus', $isi['pegawai_nik']);
}
/* DELETE */

/* LAIN */
public function cekRevisiIFA()
{
	$user = $this->session->userdata();

	$param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
	$param['pic'] = $user['pegawai_nik'];
	$data = $this->M_pekerjaan->cekRevisi($param);


	echo json_encode($data);
}
/* LAIN */

/*REMINDER*/
public function reminder()
{
	$user = $this->session->userdata();
	$param['id_user'] = $user['pegawai_nik'];

	$data = $this->M_pekerjaan->getReminder($param);

	foreach ($data as $key => $val) {
    $tanggal_extend = $val['extend_tanggal'];
    $tanggal_extend_reminder = date('Y-m-d', strtotime($tanggal_extend . '- 2 days'));
    $tanggal_sekarang = date('Y-m-d');
    /*      jika reminder nya hari sekarang*/
    if ($tanggal_extend_reminder == $tanggal_sekarang) {
      $email_penerima = anti_inject($user['email_pegawai']);
      $subjek = anti_inject('Reminder');
      $pesan = ('Anda Memiliki Pekerjaan Yang Harus Diselesaikan Sebelum Tanggal ' . $tanggal_extend . ' Klik <a href=' . base_url() . ' target="_blank">Tautan Berikut</a> Untuk Detailnya');
      print_r($pesan);
      $sendmail = array(
       'email_penerima' => $email_penerima,
       'subjek' => $subjek,
       'content' => $pesan,
     );
      echo $sendmail;
    }
    /*      cek pekerjaan*/
  }
  /*REMINDER*/
}

/*  AUTO UPDATE STATUS*/
public function autoUpdateIFA()
{
	$user = (isset($_GET['id_user'])) ? $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $_GET['id_user']))->row_array() : $this->session->userdata();
	$param['id_user'] = $user['pegawai_nik'];

	$data_reminder = $this->M_pekerjaan->getReminder($param);

	foreach ($data_reminder as $key => $val) {
    $tanggal_extend = $val['extend_tanggal'];
    $tanggal_extend_reminder = date('Y-m-d', strtotime($tanggal_extend));
    $tanggal_sekarang = date('Y-m-d');

    if ($tanggal_extend_reminder == $tanggal_sekarang && $val['pekerjaan_status'] = '8') {

      /* Pekerjaan */
      $pekerjaan_status = '9';

      $pekerjaan_id = $val['pekerjaan_id'];
      if ($pekerjaan_id) {
       $data['pekerjaan_status'] = anti_inject($pekerjaan_status);
       $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
       dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reviewed Oleh PIC', $user['pegawai_nik']);
     }
     /* Pekerjaan */

     /* User */
     $param_user['pegawai_poscode'] = $user['pegawai_direct_superior'];

     $data_user = $this->M_user->getUser($param_user);
     /* User */

     /* Staf Cangun */
     $sql_disposisi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '5' AND id_pekerjaan = '" . $pekerjaan_id . "'");
     $isi_disposisi = $sql_disposisi->result_array();
     /* Staf Cangun */

     /* Disposisi */
     foreach ($isi_disposisi as $key => $value) {
       $data_disposisi['pekerjaan_disposisi_id'] = create_id();
       $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
       $data_disposisi['id_user'] = anti_inject($value['id_user']);
       $data_disposisi['id_pekerjaan'] = anti_inject($pekerjaan_id);
       $data_disposisi['pekerjaan_disposisi_status'] = anti_inject($pekerjaan_status);


       $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

       $param_user_disposisi['pegawai_nik'] = $data_disposisi['id_user'];
       $data_user_disposisi = $this->M_user->getUser($param_user_disposisi);

       $param_pekerjaan['pekerjaan_id'] = $pekerjaan_id;
       $data_pekerjaan = $this->M_pekerjaan->getPekerjaan($param_pekerjaan);

       $email_penerima = $data_user_disposisi['email_pegawai'];
       $subjek = $data_pekerjaan['pekerjaan_judul'];
       $pesan = $data_pekerjaan['pekerjaan_deskripsi'];
       $sendmail = array(
         'email_penerima' => $email_penerima,
         'subjek' => $subjek,
         'content' => $pesan,
       );
       echo $sendmail;

       /*INSERT KE DB EMAIL*/
       $param_email['email_id'] = create_id();
       $param_email['id_penerima'] = $data_user_disposisi['pegawai_nik'];
       $param_email['id_pengirim'] = $user['pegawai_nik'];
       $param_email['id_pekerjaan'] = $pekerjaan_id;
       $param_email['id_pekerjaan_disposisi'] = $data_disposisi['pekerjaan_disposisi_id'];
       $param_email['email_subject'] = $subjek;
       $param_email['email_content'] = $pesan;
       $param_email['when_created'] = date('Y-m-d H:i:s');
       $param_email['who_created'] = $user['pegawai_nama'];

       $this->M_pekerjaan->insertEmail($param_email);
     }
     /* Disposisi */
   }
 }
}
/*  AUTO UPDATE STATUS*/


public function cancelDokumen()
{
	$pekerjaan_dokumen_id = $this->input->post('pekerjaan_dokumen_id');
	$this->M_pekerjaan->deletePekerjaanDokumen($pekerjaan_dokumen_id);
}


public function insertCCDraft()
{
	if (isset($_GET['id_user'])) {
    $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $isi = $sql_isi->row_array();
  } else {
    $isi = $this->session->userdata();
  }
  $pekerjaan_id = anti_inject($this->input->get_post('id_pekerjaan'));
  $id_tanggung_jawab = null;
  $pekerjaan_status = anti_inject('8');
  /*cek jumlah dokumen yang ada*/
  $sql_jml_dokumen  = $this->db->query("SELECT COUNT(*) as total FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND is_lama = 'n' and pekerjaan_dokumen_awal='n' AND id_create_awal = '" . $isi['pegawai_nik'] . "' AND is_hps='n' ")->row_array();
  /*cek jumlah dokumen yang ada*/
  if ($sql_jml_dokumen['total'] > 0) {
    /*insert progres*/
    $param['progress_id'] = create_id();
    $param['id_pekerjaan'] = anti_inject($pekerjaan_id);
    $param['id_user'] = anti_inject($isi['pegawai_nik']);
    $param['progress_jumlah'] = '50';

    $sql_bagian = $this->db->query("SELECT id_bagian FROM global.global_bagian_detail WHERE id_pegawai = '" . $isi['pegawai_nik'] . "'");
    $data_bagian = $sql_bagian->row_array();
    $param['id_bagian'] = anti_inject($data_bagian['id_bagian']);

    $this->M_pekerjaan->insertProgress($param);
    dblog('I',  $param['id_pekerjaan'], 'Perencana Telah Draft Dokumen', $isi['pegawai_nik']);
    /*insert progres*/
  }
  /*cc*/
  $is_cc = 'y';
  if ($this->input->get_post('id_user_staf')) {
    $user = $this->input->get_post('id_user_staf');
    $user_implode = implode("','", $user);
    $cc_non = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'y' ")->result_array();
     foreach ($cc_non as $value_non) {
       $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_non['id_user']))->row_array();
       dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC Non HPS', $isi['pegawai_nik']);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_non['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'y'");
     }
     foreach ($user as $key => $value) {
       $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='y'")->row_array();
       if ($ada_cc['total'] == 0) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = anti_inject($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
        $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi_doc['is_cc'] = anti_inject('y');
        $data_disposisi_doc['is_aktif'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
        $tujuan = $data_cc['pegawai_nik'];
        $tujuan_nama = $data_cc['pegawai_nama'];
        $kalimat = "Pekerjaan telah di CC kepada anda";
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
        sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);

        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC Non HPS', $isi['pegawai_nik']);
      }
    }
  }
  /*cc*/
}


public function insertCCDraftHPS()
{
	if (isset($_GET['id_user'])) {
    $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $isi = $sql_isi->row_array();
  } else {
    $isi = $this->session->userdata();
  }
  $pekerjaan_id = anti_inject($this->input->get_post('id_pekerjaan_hps'));
  $id_tanggung_jawab = null;
  $pekerjaan_status = anti_inject('8');
  /*cek jumlah dokumen yang ada*/
  $sql_jml_dokumen  = $this->db->query("SELECT COUNT(*) as total FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND is_lama = 'n' and pekerjaan_dokumen_awal='n' AND id_create_awal = '" . $isi['pegawai_nik'] . "' AND is_hps='y' ")->row_array();
  /*cek jumlah dokumen yang ada*/
  if ($sql_jml_dokumen['total'] > 0) {
    /*insert progres*/
    $param['progress_id'] = create_id();
    $param['id_pekerjaan'] = anti_inject($pekerjaan_id);
    $param['id_user'] = anti_inject($isi['pegawai_nik']);
    $param['progress_jumlah'] = '50';

    $sql_bagian = $this->db->query("SELECT id_bagian FROM global.global_bagian_detail WHERE id_pegawai = '" . $isi['pegawai_nik'] . "'");
    $data_bagian = $sql_bagian->row_array();
    $param['id_bagian'] = anti_inject($data_bagian['id_bagian']);

    $this->M_pekerjaan->insertProgress($param);
    echo $this->db->last_query();
    dblog('I',  $param['id_pekerjaan'], 'Perencana Telah Draft Dokumen', $isi['pegawai_nik']);
    /*insert progres*/
  }
  $is_cc = 'h';
  if ($this->input->get_post('id_user_staf_hps')) {

    $user = $this->input->get_post('id_user_staf_hps');
    $user_implode = implode("','", $user);

    $cc_hps = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'h' ")->result_array();

     foreach ($cc_hps as $value_hps) {
       $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_hps['id_user']))->row_array();
       dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC HPS', $isi['pegawai_nik']);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_hps['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'h'");
     }
     foreach ($user as $key => $value) {

       $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='h'")->row_array();
       if ($ada_cc['total'] == 0) {

        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = anti_inject($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
        $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi_doc['is_cc'] = anti_inject('h');
        $data_disposisi_doc['is_aktif'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
        $tujuan = $data_cc['pegawai_nik'];
        $tujuan_nama = $data_cc['pegawai_nama'];
        $kalimat = "Pekerjaan telah di CC kepada anda";
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
        sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);

        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC HPS', $isi['pegawai_nik']);
      }
    }
  }
}

public function insertCCDraftIFC()
{
	if (isset($_GET['id_user'])) {
    $isi = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $_GET['id_user']))->row_array();
  } else {
    $isi = $this->session->userdata();
  }
  $pekerjaan_id = anti_inject($this->input->get_post('id_pekerjaan_ifc'));
  $id_tanggung_jawab = null;
  $pekerjaan_status = anti_inject('8');
  $is_cc = 'y';
  /*CC*/
  if ($this->input->get_post('id_user_staf_ifc')) {
    $user = $this->input->get_post('id_user_staf_ifc');
    $user_implode = implode("','", $user);
    $cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'y' ")->result_array();

     foreach ($cc_not_in as $value_not_in) {
       $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
       dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC Non HPS', $isi['pegawai_nik']);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'y'");
     }
     foreach ($user as $key => $value) {

       $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='y'")->row_array();
       if ($ada_cc['total'] == 0) {

        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = anti_inject($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
        $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi_doc['is_cc'] = anti_inject('y');
        $data_disposisi_doc['is_aktif'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
        $tujuan = $data_cc['pegawai_nik'];
        $tujuan_nama = $data_cc['pegawai_nama'];
        $kalimat = "Pekerjaan telah di CC kepada anda";
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
        sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);

        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC Non HPS', $isi['pegawai_nik']);
      }
    }
    /*CC*/
  }
}

public function insertCCDraftIFCHPS()
{
	if (isset($_GET['id_user'])) {
    $isi = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $_GET['id_user']))->row_array();
  } else {
    $isi = $this->session->userdata();
  }
  $pekerjaan_id = anti_inject($this->input->get_post('id_pekerjaan_ifc_hps'));
  $id_tanggung_jawab = null;
  $pekerjaan_status = anti_inject('8');
  $is_cc = 'y';
  /*CC*/
  if ($this->input->get_post('id_user_staf_ifc_hps')) {
    $user = $this->input->get_post('id_user_staf_ifc_hps');
    $user_implode = implode("','", $user);
    $cc_not_in = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'h' ")->result_array();

     foreach ($cc_not_in as $value_not_in) {
       $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_not_in['id_user']))->row_array();
       dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC HPS', $isi['pegawai_nik']);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_not_in['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'h'");
     }
     foreach ($user as $key => $value) {

       $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='h'")->row_array();
       if ($ada_cc['total'] == 0) {

        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = anti_inject($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
        $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi_doc['is_cc'] = anti_inject('h');
        $data_disposisi_doc['is_aktif'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
        $tujuan = $data_cc['pegawai_nik'];
        $tujuan_nama = $data_cc['pegawai_nama'];
        $kalimat = "Pekerjaan telah di CC kepada anda";
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
        sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);

        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC HPS', $isi['pegawai_nik']);
      }
    }
  }
  /*CC*/
}


/*  Ganti Perencana*/
public function gantiPerencana()
{
	if (isset($_GET['id_user'])) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $user = $sql_user->row_array();
  } else {
    $user = $this->session->userdata();
  }

  $data_penanggung_jawab = $this->db->query("SELECT id_penanggung_jawab FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' AND id_user = '" . $user['pegawai_nik'] . "' AND pekerjaan_disposisi_status = '5'")->row_array();

  $listin = $this->db->query("SELECT  is_listin FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' AND id_user = '" . $user['pegawai_nik'] . "' AND pekerjaan_disposisi_status = '5'")->row_array();


  $param['pekerjaan_disposisi_id'] = create_id();
  $param['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
  $param['id_user'] = $this->input->post('id_user');
  $param['id_pekerjaan'] = $this->input->post('id_pekerjaan');
  $param['pekerjaan_disposisi_status'] = $this->input->post('pekerjaan_status');
  $param['is_aktif'] = 'y';
  $param['id_penanggung_jawab'] = $data_penanggung_jawab['id_penanggung_jawab'];
  $param['is_listin'] = $listin['is_listin'];

  $this->M_pekerjaan->insertPekerjaanDisposisi($param);

  $pekerjaan_id = $this->input->post('id_pekerjaan');
  $disposisi_status = $this->input->post('pekerjaan_status');
  $user_id = $user['pegawai_nik'];
  $this->M_pekerjaan->deletePekerjaanDisposisiReject($pekerjaan_id, $disposisi_status, $user_id);

  $data_user = $this->db->get_where('global.global_pegawai', array(
    'pegawai_nik' => $this->input->get_post('id_user_perencana')
  ))->row_array();

  /* Notifikasi DOF */
  $dari = $user['pegawai_nik'];
  $tujuan = $data_user['pegawai_nik'];
  $tujuan_nama = $data_user['pegawai_nama'];
  $text = "Mohon untuk melakukan PROSES pada pekerjaan ini";
  sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
  sendNotif($pekerjaan_id, $dari, $tujuan, $text);
  /* Notifikasi DOF */

  dblog('I', $this->input->get_post('id_pekerjaan'), 'Pekerjaan Telah Diganti Perencana ke ' . $data_user['pegawai_nama'], $user['pegawai_nik']);

  /*is_listin*/
}
/*  Ganti Perencana*/

public function gantiKoor()
{
	if ($this->input->get('id_user')) {
    $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $isi = $sql_isi->row_array();
  } else {
    $isi = $this->session->userdata();
  }

  $pekerjaan_id = $this->input->get_post('id_pekerjaan');

  /*ambil data yang koor lama*/
  $bagian_detail_lama = $this->db->query("SELECT * FROM global.global_bagian_detail WHERE id_pegawai = '" . $isi['pegawai_nik'] . "'")->row_array();

  $param_lama['id_pekerjaan'] = $this->input->get_post('id_pekerjaan');
  $param_lama['id_bagian'] = $bagian_detail_lama['id_bagian'];
  $param_lama['id_penanggung_jawab'] = 'y';

  $koor_lama = $this->M_pekerjaan->getDataKoor($param_lama);

  foreach ($koor_lama as $Key_lama => $value_lama) :
    $id_pekerjaan_disposisi = $value_lama['pekerjaan_disposisi_id'];
    $data_pekerjaan_disposisi['id_penanggung_jawab'] = 'n';

    $this->db->where('pekerjaan_disposisi_id', $id_pekerjaan_disposisi);
    $this->db->update('dec.dec_pekerjaan_disposisi', $data_pekerjaan_disposisi);

  endforeach;
  /*ambil data yang koor lama*/

  /*ambil data koor baru*/
  $bagian_detail_baru = $this->db->query("SELECT * FROM global.global_bagian_detail WHERE id_pegawai = '" . $this->input->get_post('id_koor') . "'")->row_array();

  $param_baru['id_pekerjaan'] = $this->input->get_post('id_pekerjaan');
  $param_baru['id_bagian'] = $bagian_detail_baru['id_bagian'];
  $param_baru['id_penanggung_jawab'] = 'n';

  $koor_baru = $this->M_pekerjaan->getDataKoor($param_baru);

  foreach ($koor_baru as $Key_baru => $value_baru) :
    $id_pekerjaan_disposisi = $value_baru['pekerjaan_disposisi_id'];
    $data_pekerjaan_disposisi['id_penanggung_jawab'] = 'y';

    $this->db->where('pekerjaan_disposisi_id', $id_pekerjaan_disposisi);
    $this->db->update('dec.dec_pekerjaan_disposisi', $data_pekerjaan_disposisi);

  endforeach;
  /*ambil data koor baru*/

  /*cek apakah suda proses (jika sudah langsung ubah ke status 5)*/
  $data_belum_proses = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND (is_proses != 'y' OR is_proses is null)  AND pekerjaan_disposisi_status = '4'")->num_rows();
  if ($data_belum_proses == 0) {
    $data_pekerjaan['pekerjaan_status'] = '5';
    $this->M_pekerjaan->updatePekerjaan($data_pekerjaan, $pekerjaan_id);
  }
  /*cek apakah suda proses (jika sudah langsung ubah ke status 5)*/


  /* Notifikasi DOF */
  $data_user_lama = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON b.pegawai_nik = a.id_user WHERE id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' AND pekerjaan_disposisi_status = '4' AND id_penanggung_jawab = 'y'")->row_array();

  $data_user = $this->db->get_where(
    'global.global_pegawai',
    array(
      'pegawai_nik' => $this->input->get_post('id_koor')
    )
  )->row_array();

  $dari = $data_user_lama['pegawai_nik'];
  $tujuan = $data_user['pegawai_nik'];
  $tujuan_nama = $data_user['pegawai_nama'];
  $text = "Mohon untuk melakukan PROSES pada pekerjaan ini";
  sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
  sendNotif($pekerjaan_id, $dari, $tujuan, $text);
  /* Notifikasi DOF */

  dblog('I', $this->input->get_post('id_pekerjaan'), 'AVP Koordinator Telah Diganti dari ' . $isi['pegawai_nama'] . ' Ke ' . $data_user['pegawai_nama'], $isi['pegawai_nik']);
}

public function gantiKoorPerencana()
{
	if ($this->input->get('id_user')) {
    $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $isi = $sql_isi->row_array();
  } else {
    $isi = $this->session->userdata();
  }

  $pekerjaan_id = $this->input->get_post('id_pekerjaan');

  /*kirim permintaan ke avp buat ubah koor*/
  $bagian_detail = $this->db->query("SELECT * FROM global.global_bagian_detail WHERE id_pegawai = '" . $isi['pegawai_nik'] . "'")->row_array();

  $param['id_pekerjaan'] = $this->input->get_post('id_pekerjaan');
  $param['id_bagian'] = $bagian_detail['id_bagian'];
  $param['id_penanggung_jawab'] = 'y';

  $koor = $this->M_pekerjaan->getDataKoorAVP($param);

  foreach ($koor as $Key => $value) :
    $id_disposisi_pekerjaan = $value['pekerjaan_disposisi_id'];
    $data_disposisi_pekerjaan['id_koor_baru'] = $this->input->post('id_koor');

    $this->db->where('pekerjaan_disposisi_id', $id_disposisi_pekerjaan);
    $this->db->update('dec.dec_pekerjaan_disposisi', $data_disposisi_pekerjaan);
  endforeach;

  $data_pekerjaan['pekerjaan_status'] = '4';
  $this->M_pekerjaan->updatePekerjaan($data_pekerjaan, $pekerjaan_id);
  /*kirim permintaan ke avp buat ubah koor*/

  /* Notifikasi DOF */
  $data_user_lama = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON b.pegawai_nik = a.id_user WHERE id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' AND pekerjaan_disposisi_status = '4' AND id_penanggung_jawab = 'y'")->row_array();

  $data_user = $this->db->get_where(
    'global.global_pegawai',
    array(
      'pegawai_nik' => $this->input->get_post('id_koor')
    )
  )->row_array();

  $dari = $data_user_lama['pegawai_nik'];
  $tujuan = $data_user['pegawai_nik'];
  $tujuan_nama = $data_user['pegawai_nama'];
  $text = "Mohon untuk melakukan PROSES pada pekerjaan ini";
  sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
  sendNotif($pekerjaan_id, $dari, $tujuan, $text);
  /* Notifikasi DOF */

  dblog('I', $this->input->get_post('id_pekerjaan'), 'Perencana Meminta Mengubah AVP Koor Ke ' . $data_user['pegawai_nama'], $isi['pegawai_nik']);
}

/* Lihat Dokumen */
public function lihatDokumen()
{
	if (isset($_GET['id_user'])) {
    $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $isi = $sql_isi->row_array();
  } else {
    $isi = $this->session->userdata();
  }
  $sql_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON a.id_pekerjaan_template = b.pekerjaan_template_id WHERE pekerjaan_dokumen_id = '" . $_GET['id'] . "'");
  $dokumen = $sql_dokumen->row_array();

  $judul = ($dokumen['pekerjaan_dokumen_awal'] == 'y') ? $dokumen['pekerjaan_dokumen_nama'] : $dokumen['pekerjaan_template_nama'] . ' - ' . $dokumen['pekerjaan_dokumen_nama'];

  dblog('V', $dokumen['id_pekerjaan'], 'Dokumen ' . $judul . ' Telah Dilihat', $isi['pegawai_nik']);

  echo json_encode('1');
}
/* Lihat Dokumen */

/* Cek Status Dokumen AVP Koor */
public function getStatusKoorIFC()
{
	if (isset($_GET['id_user'])) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $user = $sql_user->row_array();
  } else {
    $user = $this->session->userdata();
  }

  $dokumennya = array();

  $sql_total = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE id_penanggung_jawab = 'y' AND id_pekerjaan = '" . $_GET['pekerjaan_id'] . "' AND  id_user = '" . $user['pegawai_nik'] . "'");
  $isi_total = $sql_total->row_array();

  $data_koordinator = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_penanggung_jawab ='y' AND id_pekerjaan = '" . $_GET['pekerjaan_id'] . "' AND id_user = '" . $user['pegawai_nik'] . "'")->row_array();

  if (!empty($data_koordinator)) {
    $data_bagian_koor = $this->db->query("SELECT * FROM global.global_bagian_detail WHERE id_pegawai = '" . $data_koordinator['id_user'] . "'")->row_array();
  }

  if ($isi_total['total'] > 0) {
    $dokumen = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_awal = 'n' AND is_lama = 'n' AND (pekerjaan_dokumen_status >= '8' AND pekerjaan_dokumen_status <= '9') AND id_pekerjaan = '" . $_GET['pekerjaan_id'] . "' AND is_hps = 'n' ")->row_array();


    $data_dokumen = $this->db->query("SELECT count(*) AS total FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_awal = 'n' AND is_lama = 'n' AND (pekerjaan_dokumen_status >= '8' AND pekerjaan_dokumen_status <= '9') AND id_pekerjaan = '" . $_GET['pekerjaan_id'] . "' AND id_create_awal IN(SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian = '" . $data_bagian_koor['id_bagian'] . "') AND is_hps = 'n' ")->row_array();

    $isi['total'] = $dokumen['total'];
    $isi['total_koor'] = $data_dokumen['total'];

    array_push($dokumennya, $isi);
  } else {
    $isi['total'] = '0';
    $isi['total_koor'] = '0';
    array_push($dokumennya, $isi);
  }

  echo json_encode($dokumennya);
}
/* Cek Status Dokumen AVP Koor */

/* Cek Status Dokumen AVP Koor */
public function getStatusKoor()
{
	if (isset($_GET['id_user'])) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $user = $sql_user->row_array();
  } else {
    $user = $this->session->userdata();
  }

  $dokumennya = array();

  $sql_total = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE id_penanggung_jawab = 'y' AND id_pekerjaan = '" . $_GET['pekerjaan_id'] . "' AND  id_user = '" . $user['pegawai_nik'] . "'");
  $isi_total = $sql_total->row_array();

  $data_koordinator = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_penanggung_jawab ='y' AND id_pekerjaan = '" . $_GET['pekerjaan_id'] . "' AND id_user = '" . $user['pegawai_nik'] . "'")->row_array();

  $data_bagian_koor = $this->db->query("SELECT * FROM global.global_bagian_detail WHERE id_pegawai = '" . $data_koordinator['id_user'] . "'")->row_array();

  if ($isi_total['total'] > 0) {
    $sql_dokumen = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_awal = 'n' AND is_lama = 'n' AND pekerjaan_dokumen_status < '3' AND id_pekerjaan = '" . $_GET['pekerjaan_id'] . "'");
    $dokumen = $sql_dokumen->row_array();

    $data_dokumen = $this->db->query("SELECT count(*) AS total FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_awal = 'n' AND is_lama = 'n' AND pekerjaan_dokumen_status < '3' AND id_pekerjaan = '" . $_GET['pekerjaan_id'] . "' AND id_create_awal IN(SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian = '" . $data_bagian_koor['id_bagian'] . "')")->row_array();

    $isi['total'] = $dokumen['total'];
    $isi['total_koor'] = $data_dokumen['total'];

    array_push($dokumennya, $isi);
  } else {
    $sql_dokumen = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_dokumen WHERE 1=3 AND pekerjaan_dokumen_awal = 'n' AND is_lama = 'n' AND pekerjaan_dokumen_status < '3' AND id_pekerjaan = '" . $_GET['pekerjaan_id'] . "'");
    $dokumen = $sql_dokumen->row_array();

    $data_dokumen = $this->db->query("SELECT count(*) AS total FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_awal = 'n' AND is_lama = 'n' AND pekerjaan_dokumen_status < '3' AND id_pekerjaan = '" . $_GET['pekerjaan_id'] . "' AND id_create_awal IN(SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian = '" . $data_bagian_koor['id_bagian'] . "')")->row_array();

    $isi['total'] = $dokumen['total'];
    $isi['total_koor'] = $data_dokumen['total'];

    array_push($dokumennya, $isi);
  }

  echo json_encode($dokumennya);
}
/* Cek Status Dokumen AVP Koor */

/* Cek Status Dokumen AVP Koor */
public function getStatus()
{
	if (isset($_GET['id_user'])) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $user = $sql_user->row_array();
  } else {
    $user = $this->session->userdata();
  }

  $data_bantuan = $this->db->get_where('dec.dec_pekerjaan_disposisi', (array('id_pekerjaan' => $_GET['pekerjaan_id'], 'pekerjaan_disposisi_status' => '5', 'id_penanggung_jawab' => null)))->result_array();

  /*    $data_bantuan = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi a JOIN global.global_pegawai b ON b.pegawai_nik = a.id_user WHERE a.id_pekerjaan = '".$_GET['pekerjaan_id']."' AND pekerjaan_disposisi_status = '5' AND id_penanggung_jawab IS NULL")->result_array();*/

  $sql_total = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE 1=1 AND id_pekerjaan = '" . $_GET['pekerjaan_id'] . "' AND  id_user = '" . $user['pegawai_nik'] . "'");
  $isi_total = $sql_total->row_array();



  foreach ($data_bantuan as $val_bantuan) {
    if ($isi_total['total'] > 0) {

      /* $sql_dokumen = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_dokumen a JOIN global.global_pegawai b ON b.pegawai_nik = a.id_create_awal WHERE pekerjaan_dokumen_awal = 'n' AND is_lama = 'n' AND pekerjaan_dokumen_status < '3' AND id_pekerjaan = '".$_GET['pekerjaan_id']."' AND b.pegawai_id_bag = '".$val_bantuan['pegawai_id_bag']."'");*/

      $sql_dokumen = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_dokumen WHERE 1=3 AND pekerjaan_dokumen_awal = 'n' AND is_lama = 'n' AND pekerjaan_dokumen_status < '3' AND id_pekerjaan = '" . $_GET['pekerjaan_id'] . "' AND id_create = '" . $val_bantuan['id_user'] . "'");
      $dokumen = $sql_dokumen->row_array();
    } else {

      /*$sql_dokumen = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_dokumen a JOIN global.global_pegawai b ON b.pegawai_nik = a.id_create_awal WHERE pekerjaan_dokumen_awal = 'n' AND is_lama = 'n' AND pekerjaan_dokumen_status < '3' AND id_pekerjaan = '".$_GET['pekerjaan_id']."' AND b.pegawai_id_bag = '".$val_bantuan['pegawai_id_bag']."'");*/

      $sql_dokumen = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_dokumen WHERE 1=3 AND pekerjaan_dokumen_awal = 'n' AND is_lama = 'n' AND pekerjaan_dokumen_status < '3' AND id_pekerjaan = '" . $_GET['pekerjaan_id'] . "' AND id_create = '" . $val_bantuan['id_user'] . "'");
      $dokumen = $sql_dokumen->row_array();
    }
  }
  echo json_encode($dokumen);
}
/* Cek Status Dokumen AVP Koor */

/* Cek Status Dokumen AVP Koor */
public function getStatusIFA()
{
	if (isset($_GET['id_user'])) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $user = $sql_user->row_array();
  } else {
    $user = $this->session->userdata();
  }

  $sql_total = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE 1=1 AND id_pekerjaan = '" . $_GET['pekerjaan_id'] . "' AND  id_user = '" . $user['pegawai_nik'] . "'");
  $isi_total = $sql_total->row_array();

  if ($isi_total['total'] > 0) {
    $sql_dokumen = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_awal = 'n' AND is_lama = 'n' AND pekerjaan_dokumen_status <= '4' AND id_pekerjaan = '" . $_GET['pekerjaan_id'] . "' AND is_hps = 'n' ");
    $dokumen = $sql_dokumen->row_array();
  } else {
    $sql_dokumen = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_dokumen WHERE 1=3 AND pekerjaan_dokumen_awal = 'n' AND is_lama = 'n' AND pekerjaan_dokumen_status <= '4' AND id_pekerjaan = '" . $_GET['pekerjaan_id'] . "' AND is_hps = 'n' ");
    $dokumen = $sql_dokumen->row_array();
  }

  echo json_encode($dokumen);
}
/* Cek Status Dokumen AVP Koor */

public function getStatusIFCKoor()
{
	if (isset($_GET['id_user'])) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $user = $sql_user->row_array();
  } else {
    $user = $this->session->userdata();
  }

  $sql_total = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE 1=1 AND id_pekerjaan = '" . $_GET['pekerjaan_id'] . "' AND  id_user = '" . $user['pegawai_nik'] . "' AND id_penanggung_jawab = 'y' ");
  $isi_total = $sql_total->row_array();

  if ($isi_total['total'] > 0) {
    $sql_dokumen = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_awal = 'n' AND is_lama = 'n' AND pekerjaan_dokumen_status > '4' AND pekerjaan_dokumen_status < '8' AND id_pekerjaan = '" . $_GET['pekerjaan_id'] . "' AND is_hps = 'n' ");
    $dokumen = $sql_dokumen->row_array();
  } else {
    $sql_dokumen = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_dokumen WHERE 1=3 AND pekerjaan_dokumen_awal = 'n' AND is_lama = 'n' AND pekerjaan_dokumen_status > '4' AND pekerjaan_dokumen_status < '8' AND id_pekerjaan = '" . $_GET['pekerjaan_id'] . "' AND is_hps = 'n' ");
    $dokumen = $sql_dokumen->row_array();
  }



  echo json_encode($dokumen);
}

/* Get Dokumen History */
public function getAsetDocumentHistory()
{
	if ($this->input->get('pekerjaan_dokumen_status') == '7') {
    $sql = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON a.id_pekerjaan_template = b.pekerjaan_template_id LEFT JOIN global.global_pegawai c ON c.pegawai_nik = a.id_create WHERE a.id_pekerjaan = '" . $this->input->get('id_pekerjaan') . "' AND is_hps = '" . $this->input->get('is_hps') . "' AND id_dokumen_awal = '" . $this->input->get('id_dokumen_awal') . "' AND CAST(pekerjaan_dokumen_status as FLOAT) <='" . $this->input->get('pekerjaan_dokumen_status') . "' AND (revisi_ifc!='y' OR revisi_ifc is null) ORDER BY pekerjaan_dokumen_waktu_update DESC");
  } else {
    $sql = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON a.id_pekerjaan_template = b.pekerjaan_template_id LEFT JOIN global.global_pegawai c ON c.pegawai_nik = a.id_create WHERE a.id_pekerjaan = '" . $this->input->get('id_pekerjaan') . "' AND is_hps = '" . $this->input->get('is_hps') . "' AND id_dokumen_awal = '" . $this->input->get('id_dokumen_awal') . "' AND CAST(pekerjaan_dokumen_status as FLOAT) <='" . $this->input->get('pekerjaan_dokumen_status') . "' ORDER BY pekerjaan_dokumen_waktu_update DESC");
  }
  $data = $sql->result_array();

  echo json_encode($data);
}
/* Get Dokumen History */

/* Get Dokumen Berjalan */
public function getDokumenBerjalan()
{
	if (isset($_GET['id_user'])) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $user = $sql_user->row_array();
  } else {
    $user = $this->session->userdata();
  }

  $dataUser = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi a LEFT JOIN dec.dec_pekerjaan b ON b.pekerjaan_id=a.id_pekerjaan WHERE (pekerjaan_disposisi_status = '5' OR pekerjaan_disposisi_status = '6' OR pekerjaan_disposisi_status = '7') and a.id_user='" . $user['pegawai_nik'] . "'")->row_array();

  if ($dataUser['total'] > 0 || $user['pegawai_nik'] == $this->admin_sistemnya || $this->id_bagiannya != '0') {
    $isi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON a.id_pekerjaan_template = b.pekerjaan_template_id LEFT JOIN dec.dec_pekerjaan c ON c.pekerjaan_id = a.id_pekerjaan LEFT JOIN global.global_bagian_detail d ON d.id_pegawai = a.id_create_awal LEFT JOIN global.global_bagian e ON e.bagian_id = d.id_bagian LEFT JOIN global.global_pegawai f ON f.pegawai_nik = d.id_pegawai LEFT JOIN global.global_pegawai h ON a.pekerjaan_dokumen_cc = h.pegawai_nik LEFT JOIN global.global_bidang j ON a.id_bidang = j.bidang_id LEFT JOIN global.global_urutan_proyek k ON a.id_urutan_proyek = k.urutan_proyek_id LEFT JOIN global.global_section_area l ON a.id_section_area = l.section_area_id WHERE pekerjaan_dokumen_awal = 'n' AND (is_lama != 'y' or is_lama is null) AND CAST(pekerjaan_dokumen_status AS INT) <= '7' AND a.id_pekerjaan = '" . $_GET['id_pekerjaan'] . "' AND is_hps = '" . $_GET['is_hps'] . "'")->result_array();

    $data = array();
    foreach ($isi as $value) {
      $dataAVP = $this->db->query("SELECT count(*) as total FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $value['id_bagian'] . "' AND pekerjaan_disposisi_status = '4' AND id_pekerjaan = '" . $_GET['id_pekerjaan'] . "' AND b.id_user = '" . $user['pegawai_nik'] . "'")->row_array();

      $dataVP = $this->db->query("SELECT count(*) as total FROM global.global_pegawai WHERE pegawai_poscode = 'E53000000' AND pegawai_nik = '" . $user['pegawai_nik'] . "'")->row_array();

      $value['avp'] = ($dataAVP['total'] > 0) ? 'y' : 'n';
      $value['vp'] = ($dataVP['total'] > 0) ? 'y' : 'n';

      array_push($data, $value);
    }
  } else {
   $data = [];
 }

 echo json_encode($data);
}
/* Get Dokumen Berjalan */

/* Get Dokumen IFA */
public function getDokumenIFA()
{
	if (isset($_GET['id_user'])) {
    $user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'")->row_array();
    /*  $user = $sql_user->row_array();*/
  } else {
    $user = $this->session->userdata();
  }

  $dataUser = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi a LEFT JOIN dec.dec_pekerjaan b ON b.pekerjaan_id=a.id_pekerjaan WHERE pekerjaan_disposisi_status IN ('8','9','10') and a.id_user='" . $user['pegawai_nik'] . "'  AND is_cc is null AND id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "'")->row_array();

  if ($this->input->get('is_hps') == 'y') {
    $dataUserHPS = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi a LEFT JOIN dec.dec_pekerjaan b ON b.pekerjaan_id=a.id_pekerjaan WHERE (pekerjaan_disposisi_status = '8') and a.id_user='" . $user['pegawai_nik'] . "' AND is_cc = 'h' AND id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "'")->row_array();
  }

  if ($this->input->get('is_hps') == 'n') {
    $dataUserCC = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi a LEFT JOIN dec.dec_pekerjaan b ON b.pekerjaan_id=a.id_pekerjaan WHERE (pekerjaan_disposisi_status = '8') and a.id_user='" . $user['pegawai_nik'] . "' AND is_cc = 'y' AND id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "'")->row_array();
  }

  if ($dataUser['total'] > 0 || $user['pegawai_nik'] == $this->admin_sistemnya || $user['pegawai_unit_id'] == 'E53000' || isset($dataUserHPS) && $dataUserHPS['total'] > 0 || isset($dataUserCC) && $dataUserCC['total'] > 0) {
    $isi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON a.id_pekerjaan_template = b.pekerjaan_template_id LEFT JOIN dec.dec_pekerjaan c ON c.pekerjaan_id = a.id_pekerjaan LEFT JOIN global.global_bagian_detail d ON d.id_pegawai = a.id_create_awal LEFT JOIN global.global_bagian e ON e.bagian_id = d.id_bagian LEFT JOIN global.global_pegawai f ON f.pegawai_nik = d.id_pegawai LEFT JOIN global.global_pegawai h ON a.pekerjaan_dokumen_cc = h.pegawai_nik LEFT JOIN global.global_bidang j ON a.id_bidang = j.bidang_id LEFT JOIN global.global_urutan_proyek k ON a.id_urutan_proyek = k.urutan_proyek_id LEFT JOIN global.global_section_area l ON a.id_section_area = l.section_area_id WHERE pekerjaan_dokumen_awal = 'n' AND (is_lama != 'y' or is_lama is null) AND CAST(pekerjaan_dokumen_status as INT) <= '7' AND a.id_pekerjaan = '" . $_GET['id_pekerjaan'] . "' AND is_hps = '" . $_GET['is_hps'] . "' AND (revisi_ifc!='y' OR revisi_ifc is null)")->result_array();

    $data = array();
    foreach ($isi as $value) {

      $dataAVP = $this->db->query("SELECT count(*) as total FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $value['id_bagian'] . "' AND pekerjaan_disposisi_status = '4' AND id_pekerjaan = '" . $_GET['id_pekerjaan'] . "' AND b.id_user = '" . $user['pegawai_nik'] . "'")->row_array();

      $dataVP = $this->db->query("SELECT count(*) as total FROM global.global_pegawai WHERE pegawai_poscode = 'E53000000' AND pegawai_nik = '" . $user['pegawai_nik'] . "'")->row_array();

      $dataPIC = $this->db->query("SELECT count(*) as total FROM  dec.dec_pekerjaan_disposisi b WHERE pekerjaan_disposisi_status = '8' AND id_pekerjaan = '" . $this->input->get('id_pekerjaan') . "' AND b.id_user = '" . $user['pegawai_nik'] . "' AND is_cc is null")->row_array();

      $dataPICAVP = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '9' AND id_pekerjaan = '" . $this->input->get('id_pekerjaan') . "' AND id_user = '" . $user['pegawai_nik'] . "' ")->row_array();

      $dataPICVP = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '10' AND id_pekerjaan = '" . $this->input->get('id_pekerjaan') . "' AND id_user = '" . $user['pegawai_nik'] . "' ")->row_array();

      $value['pic'] = ($dataPIC['total'] > 0) ? 'y' : 'n';
      $value['picavp'] = ($dataPICAVP['total'] > 0) ? 'y' : 'n';
      $value['picvp'] = ($dataPICVP['total'] > 0) ? 'y' : 'n';
      $value['avp'] = ($dataAVP['total'] > 0) ? 'y' : 'n';
      $value['vp'] = ($dataVP['total'] > 0) ? 'y' : 'n';

      array_push($data, $value);
    }
  } else {
   $data = [];
 }

 echo json_encode($data);
}
/* Get Dokumen IFA */

/* Get Dokumen IFA */
public function getDokumenIFAHPS()
{
	if (isset($_GET['id_user'])) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $user = $sql_user->row_array();
  } else {
    $user = $this->session->userdata();
  }


  $dataUser = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi a LEFT JOIN dec.dec_pekerjaan b ON b.pekerjaan_id=a.id_pekerjaan WHERE pekerjaan_disposisi_status IN ('8','9','10') and a.id_user='" . $user['pegawai_nik'] . "'  AND is_cc is null AND id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "'")->row_array();

  if ($this->input->get('is_hps') == 'y') {
    $dataUserHPS = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi a LEFT JOIN dec.dec_pekerjaan b ON b.pekerjaan_id=a.id_pekerjaan WHERE (pekerjaan_disposisi_status = '8') and a.id_user='" . $user['pegawai_nik'] . "' AND is_cc = 'h' AND id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "'")->row_array();
  }

  if ($this->input->get('is_hps') == 'n') {
    $dataUserCC = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi a LEFT JOIN dec.dec_pekerjaan b ON b.pekerjaan_id=a.id_pekerjaan WHERE (pekerjaan_disposisi_status = '8') and a.id_user='" . $user['pegawai_nik'] . "' AND is_cc = 'y' AND id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "'")->row_array();
  }

  if ($dataUser['total'] > 0 && $dataUserHPS['total'] > 0 || $user['pegawai_nik'] == $this->admin_sistemnya || $user['pegawai_unit_id'] == 'E53000' || isset($dataUserHPS) && $dataUserHPS['total'] > 0 || isset($dataUserCC) && $dataUserCC['total'] > 0) {

    $isi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON a.id_pekerjaan_template = b.pekerjaan_template_id LEFT JOIN dec.dec_pekerjaan c ON c.pekerjaan_id = a.id_pekerjaan LEFT JOIN global.global_bagian_detail d ON d.id_pegawai = a.id_create_awal LEFT JOIN global.global_bagian e ON e.bagian_id = d.id_bagian LEFT JOIN global.global_pegawai f ON f.pegawai_nik = d.id_pegawai LEFT JOIN global.global_pegawai h ON a.pekerjaan_dokumen_cc = h.pegawai_nik LEFT JOIN global.global_bidang j ON a.id_bidang = j.bidang_id LEFT JOIN global.global_urutan_proyek k ON a.id_urutan_proyek = k.urutan_proyek_id LEFT JOIN global.global_section_area l ON a.id_section_area = l.section_area_id WHERE pekerjaan_dokumen_awal = 'n' AND (is_lama != 'y' or is_lama is null)  AND a.id_pekerjaan = '" . $_GET['id_pekerjaan'] . "' AND is_hps = '" . $_GET['is_hps'] . "' ")->result_array();



    $data = array();
    foreach ($isi as $value) {
      $dataAVP = $this->db->query("SELECT count(*) as total FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $value['id_bagian'] . "' AND pekerjaan_disposisi_status = '4' AND id_pekerjaan = '" . $_GET['id_pekerjaan'] . "' AND b.id_user = '" . $user['pegawai_nik'] . "'")->row_array();

      $dataVP = $this->db->query("SELECT count(*) as total FROM global.global_pegawai WHERE pegawai_poscode = 'E53000000' AND pegawai_nik = '" . $user['pegawai_nik'] . "'")->row_array();

      $dataPIC = $this->db->query("SELECT count(*) as total FROM  dec.dec_pekerjaan_disposisi b WHERE pekerjaan_disposisi_status = '8' AND id_pekerjaan = '" . $this->input->get('id_pekerjaan') . "' AND b.id_user = '" . $user['pegawai_nik'] . "' AND is_cc is null")->row_array();


      $value['pic'] = ($dataPIC['total'] > 0) ? 'y' : 'n';
      $value['avp'] = ($dataAVP['total'] > 0) ? 'y' : 'n';
      $value['vp'] = ($dataVP['total'] > 0) ? 'y' : 'n';

      array_push($data, $value);
    }
  } else {
   $data = [];
 }



 echo json_encode($data);
}
/* Get Dokumen IFA */

/* Get Dokumen IFC */
public function getDokumenIFC()
{
	if (isset($_GET['id_user'])) {
    $user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'")->row_array();
  } else {
    $user = $this->session->userdata();
  }

  $dataUser = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi a LEFT JOIN dec.dec_pekerjaan b ON b.pekerjaan_id=a.id_pekerjaan WHERE (pekerjaan_disposisi_status = '8' ) and a.id_user='" . $user['pegawai_nik'] . "'  AND is_cc is null AND id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "'")->row_array();

  if ($this->input->get('is_hps') == 'y') {
    $dataUserHPS = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi a LEFT JOIN dec.dec_pekerjaan b ON b.pekerjaan_id=a.id_pekerjaan WHERE (pekerjaan_disposisi_status = '8') and a.id_user='" . $user['pegawai_nik'] . "' AND is_cc = 'h' AND id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "'")->row_array();
  }

  if ($this->input->get('is_hps') == 'n') {
    $dataUserCC = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi a LEFT JOIN dec.dec_pekerjaan b ON b.pekerjaan_id=a.id_pekerjaan WHERE (pekerjaan_disposisi_status = '8') and a.id_user='" . $user['pegawai_nik'] . "' AND is_cc = 'y' AND id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "'")->row_array();
  }

  if ($user['pegawai_nik'] == $this->admin_sistemnya || $user['pegawai_unit_id'] == 'E53000') {
    $isi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON a.id_pekerjaan_template = b.pekerjaan_template_id LEFT JOIN dec.dec_pekerjaan c ON c.pekerjaan_id = a.id_pekerjaan LEFT JOIN global.global_bagian_detail d ON d.id_pegawai = a.id_create_awal LEFT JOIN global.global_bagian e ON e.bagian_id = d.id_bagian LEFT JOIN global.global_pegawai f ON f.pegawai_nik = d.id_pegawai LEFT JOIN global.global_pegawai h ON a.pekerjaan_dokumen_cc = h.pegawai_nik LEFT JOIN global.global_bidang j ON a.id_bidang = j.bidang_id LEFT JOIN global.global_urutan_proyek k ON a.id_urutan_proyek = k.urutan_proyek_id LEFT JOIN global.global_section_area l ON a.id_section_area = l.section_area_id WHERE pekerjaan_dokumen_awal = 'n' AND (is_lama != 'y' or is_lama is null) AND (CAST(pekerjaan_dokumen_status AS INT) >= '8' OR pekerjaan_dokumen_status = '0') AND a.id_pekerjaan = '" . $_GET['id_pekerjaan'] . "' AND is_hps = '" . $_GET['is_hps'] . "'")->result_array();
  } else if ($dataUser['total'] > 0 || $user['pegawai_nik'] == $this->admin_sistemnya || $user['pegawai_unit_id'] == 'E53000' || isset($dataUserHPS) && $dataUserHPS['total'] > 0 || isset($dataUserCC) && $dataUserCC['total'] > 0) {
    $isi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON a.id_pekerjaan_template = b.pekerjaan_template_id LEFT JOIN dec.dec_pekerjaan c ON c.pekerjaan_id = a.id_pekerjaan LEFT JOIN global.global_bagian_detail d ON d.id_pegawai = a.id_create_awal LEFT JOIN global.global_bagian e ON e.bagian_id = d.id_bagian LEFT JOIN global.global_pegawai f ON f.pegawai_nik = d.id_pegawai LEFT JOIN global.global_pegawai h ON a.pekerjaan_dokumen_cc = h.pegawai_nik LEFT JOIN global.global_bidang j ON a.id_bidang = j.bidang_id LEFT JOIN global.global_urutan_proyek k ON a.id_urutan_proyek = k.urutan_proyek_id LEFT JOIN global.global_section_area l ON a.id_section_area = l.section_area_id WHERE pekerjaan_dokumen_awal = 'n' AND (is_lama != 'y' or is_lama is null) AND (CAST(pekerjaan_dokumen_status AS INT) >= '11' OR pekerjaan_dokumen_status = '0') AND a.id_pekerjaan = '" . $_GET['id_pekerjaan'] . "' AND is_hps = '" . $_GET['is_hps'] . "'")->result_array();
  } else {
    $isi = [];
  }

  $data = array();
  foreach ($isi as $value) {
    $dataAVP = $this->db->query("SELECT count(*) as total FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $value['id_bagian'] . "' AND pekerjaan_disposisi_status = '4' AND id_pekerjaan = '" . $_GET['id_pekerjaan'] . "' AND b.id_user = '" . $user['pegawai_nik'] . "'")->row_array();

    $dataVP = $this->db->query("SELECT count(*) as total FROM global.global_pegawai WHERE pegawai_poscode = 'E53000000' AND pegawai_nik = '" . $user['pegawai_nik'] . "'")->row_array();

    $dataPIC = $this->db->query("SELECT count(*) as total FROM  dec.dec_pekerjaan_disposisi b WHERE pekerjaan_disposisi_status = '8' AND id_pekerjaan = '" . $this->input->get('id_pekerjaan') . "' AND b.id_user = '" . $user['pegawai_nik'] . "' AND is_cc is null")->row_array();

    $dataVPProses = $this->db->query("SELECT COUNT(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' AND pekerjaan_disposisi_status = '9' AND is_proses = 'y'")->row_array();


    $value['pic'] = ($dataPIC['total'] > 0) ? 'y' : 'n';
    $value['avp'] = ($dataAVP['total'] > 0) ? 'y' : 'n';
    $value['vp'] = ($dataVP['total'] > 0) ? 'y' : 'n';
    $value['vp_proses'] = ($dataVPProses['total'] > 0) ? 'y' : 'n';

    array_push($data, $value);
  }
					// } else {
					// $data = [];
					// }

  echo json_encode($data);
}
/* Get Dokumen IFC */

/* Get Dokumen Selesai */
public function getDokumenSelesai()
{
	if ($this->input->get_post('id_user_cc') != '') {
    $user = $this->db->query("SELECT * FROM global.global_pegawai a WHERE a.pegawai_nik = '" . $this->input->get_post('id_user_cc') . "'")->row_array();
  } else if ($this->input->get_post('id_user') != '') {
    $user = $this->db->query("SELECT * FROM global.global_pegawai a WHERE a.pegawai_nik = '" . $this->input->get_post('id_user') . "'")->row_array();
  } else {
    $user = $this->session->userdata();
  }

  if ($this->input->get_post('id_user_cc')) {
    $sqlCCSelesai = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE (is_cc = 'y' OR is_cc IS NULL) AND id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' AND id_user = '" . $this->input->get_post('id_user_cc') . "'");
    $dataCCSelesai = $sqlCCSelesai->row_array();
  } else {
    $sqlCC = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE (is_cc = 'y' OR is_CC IS NULL) AND id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' AND id_user = '" . $user['pegawai_nik'] . "'");
    $dataCC = $sqlCC->row_array();
  }

  $pekerjaan = $this->db->get_where('dec.dec_pekerjaan', array('pekerjaan_id' => $this->input->get('id_pekerjaan')))->row_array();

  if ($user['pegawai_unit_id'] == 'E53000' || (!empty($dataCC)) && $dataCC['total'] > 0 || !empty($dataCCSelesai)  && $dataCCSelesai['total'] > 0) {
    /*jika non tender*/
    if (!empty($pekerjaan) && $pekerjaan['id_klasifikasi_pekerjaan'] == '616b79fa38c26380f49f3b84f088b8f86f9cd176') {
      $sql = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON a.id_pekerjaan_template = b.pekerjaan_template_id LEFT JOIN dec.dec_pekerjaan c ON c.pekerjaan_id = a.id_pekerjaan LEFT JOIN global.global_bagian_detail d ON d.id_pegawai = a.id_create_awal LEFT JOIN global.global_bagian e ON e.bagian_id = d.id_bagian LEFT JOIN global.global_pegawai f ON f.pegawai_nik = d.id_pegawai LEFT JOIN global.global_pegawai h ON a.pekerjaan_dokumen_cc = h.pegawai_nik LEFT JOIN global.global_bidang j ON a.id_bidang = j.bidang_id LEFT JOIN global.global_urutan_proyek k ON a.id_urutan_proyek = k.urutan_proyek_id LEFT JOIN global.global_section_area l ON a.id_section_area = l.section_area_id WHERE pekerjaan_dokumen_awal = 'n' AND (is_lama != 'y' or is_lama is null) AND CAST(pekerjaan_dokumen_status as INT) >= '6' AND CAST(pekerjaan_dokumen_status as INT) <= '7' AND a.id_pekerjaan = '" . $_GET['id_pekerjaan'] . "' AND is_hps = '" . $_GET['is_hps'] . "'");
      $data = $sql->result_array();
    } else {
      $sql = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON a.id_pekerjaan_template = b.pekerjaan_template_id LEFT JOIN dec.dec_pekerjaan c ON c.pekerjaan_id = a.id_pekerjaan LEFT JOIN global.global_bagian_detail d ON d.id_pegawai = a.id_create_awal LEFT JOIN global.global_bagian e ON e.bagian_id = d.id_bagian LEFT JOIN global.global_pegawai f ON f.pegawai_nik = d.id_pegawai LEFT JOIN global.global_pegawai h ON a.pekerjaan_dokumen_cc = h.pegawai_nik LEFT JOIN global.global_bidang j ON a.id_bidang = j.bidang_id LEFT JOIN global.global_urutan_proyek k ON a.id_urutan_proyek = k.urutan_proyek_id LEFT JOIN global.global_section_area l ON a.id_section_area = l.section_area_id WHERE pekerjaan_dokumen_awal = 'n' AND (is_lama != 'y' or is_lama is null) AND CAST(pekerjaan_dokumen_status as INT) >= '8' AND CAST(pekerjaan_dokumen_status as INT) <= '11' AND a.id_pekerjaan = '" . $_GET['id_pekerjaan'] . "' AND is_hps = '" . $_GET['is_hps'] . "'");
      $data = $sql->result_array();
    }
  } else {
   $data = [];
 }

 echo json_encode($data);
}

public function getDokumenSelesaiIFA()
{
	if ($this->input->get_post('id_user_cc') != '') {
    $user = $this->db->query("SELECT * FROM global.global_pegawai a WHERE a.pegawai_nik = '" . $this->input->get_post('id_user_cc') . "'")->row_array();
  } else if ($this->input->get_post('id_user') != '') {
    $user = $this->db->query("SELECT * FROM global.global_pegawai a WHERE a.pegawai_nik = '" . $this->input->get_post('id_user') . "'")->row_array();
  } else {
    $user = $this->session->userdata();
  }

  if ($this->input->get_post('id_user_cc')) {
    $sqlCCSelesai = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE (is_cc = 'y') AND id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' AND id_user = '" . $this->input->get_post('id_user_cc') . "'");
    $dataCCSelesai = $sqlCCSelesai->row_array();
  } else {
    $sqlCC = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE (is_cc = 'y' OR is_CC IS NULL) AND id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' AND id_user = '" . $user['pegawai_nik'] . "'");
    $dataCC = $sqlCC->row_array();
  }

  if ($user['pegawai_unit_id'] == 'E53000' || (!empty($dataCC)) && $dataCC['total'] > 0 || !empty($dataCCSelesai)  && $dataCCSelesai['total'] > 0) {
    $sql = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON a.id_pekerjaan_template = b.pekerjaan_template_id LEFT JOIN dec.dec_pekerjaan c ON c.pekerjaan_id = a.id_pekerjaan LEFT JOIN global.global_bagian_detail d ON d.id_pegawai = a.id_create_awal LEFT JOIN global.global_bagian e ON e.bagian_id = d.id_bagian LEFT JOIN global.global_pegawai f ON f.pegawai_nik = d.id_pegawai LEFT JOIN global.global_pegawai h ON a.pekerjaan_dokumen_cc = h.pegawai_nik LEFT JOIN global.global_bidang j ON a.id_bidang = j.bidang_id LEFT JOIN global.global_urutan_proyek k ON a.id_urutan_proyek = k.urutan_proyek_id LEFT JOIN global.global_section_area l ON a.id_section_area = l.section_area_id WHERE pekerjaan_dokumen_awal = 'n' AND (is_lama != 'y' or is_lama is null) AND  CAST(pekerjaan_dokumen_status as INT) <= '7' AND a.id_pekerjaan = '" . $_GET['id_pekerjaan'] . "' AND is_hps = '" . $_GET['is_hps'] . "'");
    $data = $sql->result_array();
  } else {
    $data = [];
  }

  echo json_encode($data);
}

/* Get Dokumen Selesai */
public function getDokumenSelesaiHPS()
{

	if ($this->input->get_post('id_user_cc') != '') {
    $user = $this->db->query("SELECT * FROM global.global_pegawai a WHERE a.pegawai_nik = '" . $this->input->get_post('id_user_cc') . "'")->row_array();
  } else if ($this->input->get_post('id_user') != '') {
    $user = $this->db->query("SELECT * FROM global.global_pegawai a WHERE a.pegawai_nik = '" . $this->input->get_post('id_user') . "'")->row_array();
  } else {
    $user = $this->session->userdata();
  }

  $dataUser = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi a LEFT JOIN dec.dec_pekerjaan b ON b.pekerjaan_id=a.id_pekerjaan WHERE (pekerjaan_disposisi_status = '8' ) and a.id_user='" . $user['pegawai_nik'] . "'  AND is_cc is null AND id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "'")->row_array();

  if ($this->input->get_post('id_user_cc')) {
    $sqlCCSelesai = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE (is_cc = 'h') AND id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' AND id_user = '" . $this->input->get_post('id_user_cc') . "'");
    $dataCCSelesai = $sqlCCSelesai->row_array();
  } else {
    $sqlCC = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE (is_cc = 'h') AND id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' AND id_user = '" . $user['pegawai_nik'] . "'");
    $dataCC = $sqlCC->row_array();
  }

  if (!empty($dataUser) && $dataUser['total'] > 0 && !empty($dataCC) && $dataCC['total'] > 0 || $user['pegawai_unit_id'] == 'E53000' || (!empty($dataCC)) && $dataCC['total'] > 0 || !empty($dataCCSelesai)  && $dataCCSelesai['total'] > 0) {
    $sql = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON a.id_pekerjaan_template = b.pekerjaan_template_id LEFT JOIN dec.dec_pekerjaan c ON c.pekerjaan_id = a.id_pekerjaan LEFT JOIN global.global_bagian_detail d ON d.id_pegawai = a.id_create_awal LEFT JOIN global.global_bagian e ON e.bagian_id = d.id_bagian LEFT JOIN global.global_pegawai f ON f.pegawai_nik = d.id_pegawai LEFT JOIN global.global_pegawai h ON a.pekerjaan_dokumen_cc = h.pegawai_nik LEFT JOIN global.global_bidang j ON a.id_bidang = j.bidang_id LEFT JOIN global.global_urutan_proyek k ON a.id_urutan_proyek = k.urutan_proyek_id LEFT JOIN global.global_section_area l ON a.id_section_area = l.section_area_id WHERE pekerjaan_dokumen_awal = 'n' AND (is_lama != 'y' or is_lama is null) AND CAST(pekerjaan_dokumen_status as INT) <= '11' AND is_lama = 'n' AND a.id_pekerjaan = '" . $_GET['id_pekerjaan'] . "' AND is_hps = '" . $_GET['is_hps'] . "'");
    $data = $sql->result_array();
  } else {
    $data = [];
  }

  echo json_encode($data);
}
/* Get Dokumen Selesai */

/* get dokumen approve */
public function getDokumenApprove()
{

	if ($this->input->get('id_user')) {
    $user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'")->row_array();
  } else {
    $user = $this->session->userdata();
  }

  $data_bagian = $this->db->query("SELECT * FROM global.global_bagian_detail a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian WHERE id_pegawai = '" . $user['pegawai_nik'] . "'")->row_array();

  $data = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' AND is_lama = 'n' AND pekerjaan_dokumen_awal = 'n' AND pekerjaan_dokumen_status = '" . $this->input->get('dokumen_status') . "' AND id_create_awal IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian = '" . $data_bagian['bagian_id'] . "')")->num_rows();

  $penanggung_jawab = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user = '" . $user['pegawai_nik'] . "' AND id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '6' ")->row_array();


  $datanya['dokumen_belum_approve'] = $data;
  $datanya['penanggung_jawab'] = $penanggung_jawab['id_penanggung_jawab'];
  echo json_encode($datanya);
}
/* get dokumen approve */


public function getIdDisposisi()
{
	$user = $this->session->userdata();
	$param_disposisi['id_user'] = htmlentities($user['pegawai_nik']);
	$param_disposisi['id_pekerjaan'] = htmlentities($this->input->get_post('id_pekerjaan'));
	$param_disposisi['pekerjaan_status'] = htmlentities($this->input->get_post('pekerjaan_disposisi_status'));

	$data = $this->M_pekerjaan->getIdDisposisi($param_disposisi);
	return $data;
}

public function getExtend()
{
	$param['pekerjaan_id'] = htmlentities($this->input->get_post('id_pekerjaan'));
	$param['pekerjaan_status'] = htmlentities($this->input->get_post('pekerjaan_disposisi_status'));
					// $param['extend_status'] = htmlentities($this->input->get_post('extend_status'));

	$data = $this->M_pekerjaan->getExtend($param);
					// echo $this->db->last_query();
	echo json_encode($data);
}

public function insertAjuanExtend()
{
	$user = $this->session->userdata();
					// get id disposisi nya
	$data_disposisi = $this->getIdDisposisi();
					//ambil pekerjaan waktu dari pekerjaan waktu
	$param_pekerjaan['pekerjaan_id'] = htmlentities($this->input->get_post('id_pekerjaan'));
	$data_pekerjaan = $this->M_pekerjaan->getPekerjaan($param_pekerjaan);
	$pekerjaan_waktu = $data_pekerjaan['pekerjaan_waktu'];

					// insert ke tb_extend
	$param_extend['extend_id'] = create_id();
	$param_extend['id_pekerjaan'] = htmlentities($this->input->get_post('id_pekerjaan'));
	$param_extend['id_user'] = htmlentities($user['pegawai_nik']);
	$param_extend['extend_hari'] = htmlentities($this->input->get_post('extend_hari'));
	$param_extend['extend_status'] = htmlentities($this->input->get_post('extend_status'));
	$param_extend['extend_tanggal'] = date('Y-m-d', strtotime(date('Y-m-d') . '+' . $this->input->get_post('extend_hari') . ' days'));
	$this->M_pekerjaan->insertExtend($param_extend);
}

public function updateAJuanExtend()
{
	$user = $this->session->userdata();
					// get id disposisi nya
	$data_disposisi = $this->getIdDisposisi();
					//ambil pekerjaan waktu dari pekerjaan waktu
	$param_pekerjaan['pekerjaan_id'] = htmlentities($this->input->get_post('id_pekerjaan'));
	$data_pekerjaan = $this->M_pekerjaan->getPekerjaan($param_pekerjaan);
	$pekerjaan_waktu = $data_pekerjaan['pekerjaan_waktu'];
					// insert ke tb_extend
	$id_extend = htmlentities($this->input->get_post('extend_id'));
	$param_extend['id_pekerjaan'] = htmlentities($this->input->get_post('id_pekerjaan'));
					// $param_extend['id_pekerjaan_disposisi'] = htmlentities($data_disposisi['pekerjaan_disposisi_id']);
	$param_extend['id_user'] = htmlentities($user['pegawai_nik']);
	$param_extend['extend_hari'] = htmlentities($this->input->get_post('extend_hari'));
	$param_extend['extend_status'] = htmlentities($this->input->get_post('extend_status'));
	$param_extend['extend_tanggal'] = date('Y-m-d', strtotime(date('Y-m-d') . '+' . $this->input->get_post('extend_hari') . ' days'));

	$this->M_pekerjaan->updateExtendBaru($id_extend, $param_extend);
}

public function editor_dokumen()
{
	$data = $this->session->userdata();
	$konten['dokumen'] = $this->uri->segment(4);
	$this->load->view('tampilan/header', $data, FALSE);
	$this->load->view('tampilan/sidebar', $data, FALSE);
	$this->load->view('project/editor_dokumen', $konten, FALSE);
	$this->load->view('tampilan/footer', $data, FALSE);
}

public function getMaxNomor()
{
	$tahun = $this->input->get('tahun');
	$id_pekerjaan = $this->input->get_post('id_pekerjaan');
	$where = ($this->input->get('id_klasifikasi_pekerjaan') == '1') ? " AND id_klasifikasi_pekerjaan = '1'" : " AND id_klasifikasi_pekerjaan != '1'";

	$sql_klasifikasi = $this->db->query("SELECT klasifikasi_pekerjaan_nama FROM global.global_klasifikasi_pekerjaan WHERE klasifikasi_pekerjaan_id = '" . $this->input->get('id_klasifikasi_pekerjaan') . "'");
	$isi_klasifikasi = $sql_klasifikasi->row_array();

	$sql_nomor = $this->db->query("SELECT SPLIT_PART(pekerjaan_nomor,'-',1) as pekerjaan_nomornya FROM dec.dec_pekerjaan WHERE SPLIT_PART(pekerjaan_nomor,'-',3) = '" . $tahun . "' AND pekerjaan_nomor IS NOT NULL " . $where . " ORDER BY CAST(SPLIT_PART(pekerjaan_nomor, '-', 1) as FLOAT) DESC");

	$isi_nomor = $sql_nomor->row_array();
	$nomor = $isi_nomor['pekerjaan_nomornya'];
	$nomor_baru = sprintf("%03d", $nomor + 1);

	$nomor_baru_nya = $nomor_baru . '-' . $isi_klasifikasi['klasifikasi_pekerjaan_nama'] . '-' . $tahun;

	echo json_encode($nomor_baru_nya);
}

public function uploadDokumen()
{
	/*status*/
	$dokumen_status = $this->db->query("SELECT pekerjaan_dokumen_status FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->post('pekerjaan_dokumen_id') . "' ")->row_array();
	$dokumen_statusnya = $dokumen_status['pekerjaan_dokumen_status'] + 1;

	$upload_path = FCPATH . './document/';
	if (!file_exists($upload_path)) mkdir($upload_path);
	$filename = $dokumen_statusnya . '_' . $this->input->post('filename');

	if (!empty($_FILES['file']['name'])) {
    $tmpName = $_FILES['file']['tmp_name'];
    $fileName = $_FILES['file']['name'];
    $fileType = $_FILES['file']['type'];

    $acak = rand(11111111, 99999999);
    $fileExt = substr($fileName, strpos($fileName, '.'));
    $fileExt = str_replace('.', '', $fileExt);
    $fileName = preg_replace("/\.[^.\s]{3,4}$/", "", $fileName);
    $newFileName = $filename;
    move_uploaded_file($tmpName, $upload_path . $newFileName);
    $newCheckFile = $newFileName;
  } else {
    $newCheckFile = null;
  }
  echo $newCheckFile;
}

public function insertPenomoranDokumen()
{
	if ($this->input->post('penomoran_id') == '') {
    $param['pekerjaan_dokumen_penomoran_id'] = create_id();
    $param['id_pekerjaan'] = $this->input->post('id_pekerjaan_penomoran');
    $param['urutan_proyek_default'] = $this->input->post('urutan_proyek_penomoran');
    $param['section_area_default'] = $this->input->post('section_area_penomoran');
    $this->M_pekerjaan->insertPenomoranDokumen($param);
  } else {
    $id = $this->input->post('penomoran_id');
    $param['id_pekerjaan'] = $this->input->post('id_pekerjaan_penomoran');
    $param['urutan_proyek_default'] = $this->input->post('urutan_proyek_penomoran');
    $param['section_area_default'] = $this->input->post('section_area_penomoran');
    $this->M_pekerjaan->updatePenomoranDokumen($id, $param);
  }
}

public function getPenomoranDokumen()
{
	$param['id_pekerjaan'] = $this->input->get('id_pekerjaan');
	$param['single'] = $this->input->get('single');
	$data =  $this->M_pekerjaan->getPenomoranDokumen($param);
	echo json_encode($data);
}

public function getDataKoorBaru()
{
	$data = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON b.pegawai_nik = a.id_koor_baru WHERE id_pekerjaan = '" . $this->input->get('id_pekerjaan') . "' AND id_koor_baru is not null")->result_array();
	echo json_encode($data);
}

public function approveDokumenAVP()
{
	if ($this->input->get('id_user')) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'");
    $isi = $sql_user->row_array();
  } else {
    $isi = $this->session->userdata();
  }
  $pekerjaan_id = $this->input->get_post('pekerjaan_id');

  /* Dokumen */
  $sql_bagian = $this->db->query("SELECT id_bagian FROM global.global_bagian_detail WHERE id_pegawai = '" . $isi['pegawai_nik'] . "'");
  $data_bagian = $sql_bagian->row_array();

  $data_pegawai = $this->db->query("SELECT id_user,id_pekerjaan,pekerjaan_disposisi_status FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $isi['pegawai_nik'] . "' AND pekerjaan_disposisi_status = '6'")->row_array();
  /*    ubah status dokumen ke IFC*/
  $data_dokumen_send = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_create_awal IN (SELECT id_pegawai FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $data_bagian['id_bagian'] . "' AND id_pekerjaan = '" . $pekerjaan_id . "' ) AND pekerjaan_dokumen_status <= '2' AND (is_lama !='y' OR is_lama is null) AND pekerjaan_dokumen_awal != 'y' AND is_hps = 'n' AND (is_update_ifa !='y' OR is_update_ifa is NULL)")->result_array();

  foreach ($data_dokumen_send as $val_dokumen) {
    $dokumen_ada = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_create_awal = '" . $val_dokumen['id_create_awal'] . "' AND pekerjaan_dokumen_status = '3' AND pekerjaan_dokumen_nama = '" . $val_dokumen['pekerjaan_dokumen_nama'] . "' AND id_pekerjaan_template = '" . $val_dokumen['id_pekerjaan_template'] . "'  AND is_hps = 'n' AND pekerjaan_dokumen_file = '" . $val_dokumen['pekerjaan_dokumen_file'] . "'")->row_array();
    $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
    $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];
    if (!empty($dokumen_ada) && ($dokumen_ada['pekerjaan_dokumen_nama'] == $val_dokumen['pekerjaan_dokumen_nama'] && $dokumen_ada['id_pekerjaan_template'] == $val_dokumen['id_pekerjaan_template'] && $dokumen_ada['id_create_awal'] == $val_dokumen['id_create_awal'])) {
      /*        skip*/
    } else {
      $data['pekerjaan_dokumen_id_temp'] = anti_inject($val_dokumen['pekerjaan_dokumen_id']);
      $data['pekerjaan_dokumen_id'] = anti_inject(create_id());
      $data['pekerjaan_dokumen_status'] = '3';
      $data['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
      $data['pekerjaan_dokumen_keterangan'] = $val_dokumen['pekerjaan_dokumen_keterangan'];
      $data['id_create'] = $isi['pegawai_nik'];
      $data['is_proses'] = 'y';
      $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
      $this->M_pekerjaan->simpanAksiSama($data);

      $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
      $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");
      dblog('I', $this->input->get_post('pekerjaan_id'), 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
    }
  }
  $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET is_lama = 'y' WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_create_awal IN (SELECT id_pegawai FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $data_bagian['id_bagian'] . "' AND id_pekerjaan = '" . $pekerjaan_id . "' ) AND (pekerjaan_dokumen_status = '0' OR pekerjaan_dokumen_status = '2') AND (is_lama !='y' OR is_lama is null) AND pekerjaan_dokumen_awal != 'y' AND is_hps = 'n'");

  $data_dokumen_send_hps =   $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_create_awal IN (SELECT id_pegawai FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $data_bagian['id_bagian'] . "' AND id_pekerjaan = '" . $pekerjaan_id . "' ) AND pekerjaan_dokumen_status <= '2' AND (is_lama !='y' OR is_lama is null) AND pekerjaan_dokumen_awal != 'y' AND is_hps = 'y' AND (is_update_ifa !='y' OR is_update_ifa is NULL)")->result_array();

  foreach ($data_dokumen_send_hps as $val_dokumen) {
   $dokumen_ada = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_create_awal = '" . $val_dokumen['id_create_awal'] . "' AND pekerjaan_dokumen_status ='3' AND pekerjaan_dokumen_nama = '" . $val_dokumen['pekerjaan_dokumen_nama'] . "' AND id_pekerjaan_template = '" . $val_dokumen['id_pekerjaan_template'] . "'  AND is_hps = 'y' AND pekerjaan_dokumen_file = '" . $val_dokumen['pekerjaan_dokumen_file'] . "'")->row_array();
   $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
   $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];
   if (!empty($dokumen_ada) && ($dokumen_ada['pekerjaan_dokumen_nama'] == $val_dokumen['pekerjaan_dokumen_nama'] && $dokumen_ada['id_pekerjaan_template'] == $val_dokumen['id_pekerjaan_template'] && $dokumen_ada['id_create_awal'] == $val_dokumen['id_create_awal'])) {
     /*skip*/
   } else {
     $data['pekerjaan_dokumen_id_temp'] = anti_inject($val_dokumen['pekerjaan_dokumen_id']);
     $data['pekerjaan_dokumen_id'] = anti_inject(create_id());
     $data['pekerjaan_dokumen_status'] = '3';
     $data['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
     $data['pekerjaan_dokumen_keterangan'] = $val_dokumen['pekerjaan_dokumen_keterangan'];
     $data['id_create'] = $isi['pegawai_nik'];
     $data['is_proses'] = 'y';
     $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
     $this->M_pekerjaan->simpanAksiSama($data);
     $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON b.pekerjaan_template_id = a.id_pekerjaan_template WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
     $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_waktu = '" . date('Y-m-d H:i:s') . "' WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status >= '4'");
     dblog('I', $this->input->get_post('pekerjaan_id'), 'Dokumen ' . $data_dokumen['pekerjaan_template_nama'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah  DiApprove', $isi['pegawai_nik']);
   }
 }
 $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET is_lama = 'y' WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_create_awal IN (SELECT id_pegawai FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $data_bagian['id_bagian'] . "' AND id_pekerjaan = '" . $pekerjaan_id . "' ) AND (pekerjaan_dokumen_status = '0' OR pekerjaan_dokumen_status = '2') AND (is_lama !='y' OR is_lama is null) AND pekerjaan_dokumen_awal != 'y' AND is_hps = 'y'");
 /* Dokumen */
}

public function insertCC()
{
	if (isset($_GET['id_user'])) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $isi = $sql_user->row_array();
  } else {
    $isi = $this->session->userdata();
  }
  $pekerjaan_id = $this->input->get_post('pekerjaan_id');
  $cc_tipe = $this->input->post('cc_tipe');

  if ($this->input->get_post('cc_id')) {
    $user = $this->input->get_post('cc_id');
    $user_implode = implode("','", $user);
    $cc_non = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc= 'y' ")->result_array();
     foreach ($cc_non as $value_non) {
       $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_non['id_user']))->row_array();
       dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC Non HPS', $isi['pegawai_nik']);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_non['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'y'");
     }
     foreach ($user as $key => $value) {
       $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='y'")->row_array();
       if ($ada_cc['total'] == 0) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = anti_inject($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
        $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi_doc['is_cc'] = anti_inject('y');
        $data_disposisi_doc['is_aktif'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
        $tujuan = $data_cc['pegawai_nik'];
        $tujuan_nama = $data_cc['pegawai_nama'];
        $kalimat = "Pekerjaan telah di CC kepada anda";
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
        sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);

        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC Non HPS', $isi['pegawai_nik']);
      }
    }
  }
}


public function insertCCHPS()
{
	if (isset($_GET['id_user'])) {
    $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
    $isi = $sql_user->row_array();
  } else {
    $isi = $this->session->userdata();
  }
  $pekerjaan_id = $this->input->get_post('pekerjaan_id');
  $cc_tipe = $this->input->post('cc_tipe');

  if ($this->input->get_post('cc_id')) {
    $user = $this->input->get_post('cc_id');
    $user_implode = implode("','", $user);
    $cc_non = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user NOT IN ('" . $user_implode . "') AND id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'h' ")->result_array();
     foreach ($cc_non as $value_non) {
       $data_cc = $this->db->get_where('global.global_pegawai', array('pegawai_nik' => $value_non['id_user']))->row_array();
       dblog('I',  $pekerjaan_id, '' . $data_cc['pegawai_nama'] . ' Telah Dihapus Dari CC HPS', $isi['pegawai_nik']);
       $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND id_user = '" . $value_non['id_user'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc = 'h'");
     }
     foreach ($user as $key => $value) {
       $ada_cc = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $value . "' AND is_cc ='h'")->row_array();
       if ($ada_cc['total'] == 0) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = anti_inject($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
        $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
        $data_disposisi_doc['is_cc'] = anti_inject('h');
        $data_disposisi_doc['is_aktif'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $value])->row_array();
        $tujuan = $data_cc['pegawai_nik'];
        $tujuan_nama = $data_cc['pegawai_nama'];
        $kalimat = "Pekerjaan telah di CC kepada anda";
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
        sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);

        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC HPS', $isi['pegawai_nik']);
      }
    }
  }
}

public function nilaiHPS()
{
	$this->db->select("*");
	$this->db->from('dec.dec_pekerjaan_nilai_hps');
	$this->db->where("id_pekerjaan = '" . $this->input->get('id_pekerjaan') . "'");
	$sql = $this->db->get();
	echo json_encode($sql->result_array());
}

public function insertNilaiHPS()
{
	if ($this->input->post('is_nilai_hps_old') == '') {
    foreach ($this->input->post('id_bagian_nilai_hps') as $key => $id_bagian) {
      $d['pekerjaan_nilai_hps_id'] = create_id();
      $d['id_pekerjaan'] = $this->input->post('pekerjaan_id');
      $d['id_bagian'] = $id_bagian;
      $d['pekerjaan_nilai_hps_jumlah'] = $this->input->post('pekerjaan_nilai_hps')[$key];
      $d['pekerjaan_nilai_hps_total'] = $this->input->post('pekerjaan_nilai_hps_total');
      $this->db->insert('dec.dec_pekerjaan_nilai_hps', $d);
    }
  } else {
   foreach ($this->input->post('id_bagian_nilai_hps') as $key => $id_bagian) {
     $data = array(
      'pekerjaan_nilai_hps_jumlah' => $this->input->post('pekerjaan_nilai_hps')[$key],
      'pekerjaan_nilai_hps_total' => $this->input->post('pekerjaan_nilai_hps_total'),
    );
     $this->db->where('pekerjaan_nilai_hps_id', $this->input->post('nilai_hps_id')[$key]);
     $this->db->where('id_bagian', $id_bagian);
     $result = $this->db->update('dec.dec_pekerjaan_nilai_hps', $data);
   }
 }
}
}
