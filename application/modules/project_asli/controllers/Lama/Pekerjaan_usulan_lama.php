<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pekerjaan_usulan extends MX_Controller
{
  public function __construct()
  {
    parent::__construct();
    $sesi = $this->session->userdata();
    if (empty($sesi['pegawai_nik'])) {
      redirect('tampilan');
    }

    $this->load->model('M_pekerjaan');
    $this->load->model('master/M_user');
    $this->load->library('mailer');
    $this->load->library('mailer_api');
  }

  /* INDEX */
  /* Index Pekerjaan Usulan */
  public function index()
  {
    $data = $this->session->userdata();

    $this->load->view('project/pekerjaan_usulan', $data);
  }
  /* Index Pekerjaan Usulan */

  /* Index Pekerjaan Detail */
  public function detailPekerjaan($var = null)
  {
    $data = $this->session->userdata();
    $param['pekerjaan_id'] = preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get_post('pekerjaan_id'));
    $data["pekerjaan"] = $this->M_pekerjaan->getPekerjaan($param);
    // print_r($this->db->last_query());

    $this->load->view('tampilan/header', $data);
    $this->load->view('tampilan/sidebar');
    $this->load->view('project/detail_pekerjaan_usulan', $data);
    $this->load->view('tampilan/footer');
  }
  /* Index Pekerjaan Detail */
  /* INDEX */

  /* PEKERJAAN USULAN */
  /* GET */
  /* Get Pekerjaan Ususlan */
  public function getPekerjaanUsulanLama()
  {
    $param['pekerjaan_id'] = $this->input->get('pekerjaan_id');
    $param['id_user'] = $this->input->get_post('id_user_cari');
    // $param['pekerjaan_status_not_inpro'] = '5';
    $data = array();
    $session = $this->session->userdata();

    if ($param['pekerjaan_id'] != null) {
      $data = $this->M_pekerjaan->getPekerjaan($param);
      echo json_encode($data);
    } else {
      if (empty($param['id_user'])) {
        foreach ($this->M_pekerjaan->getPekerjaan($param) as $value) {
          foreach ($value as $key => $val) {
            $isi[$key] = $val;
          }
          $sql_total = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '" . $value['pekerjaan_status'] . "' AND id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND  id_user = '" . $session['pegawai_nik'] . "'");
          $isi_total = $sql_total->row_array();

          $isi['milik'] = ($isi_total['total'] > 0 || ($value['pic'] == $session['pegawai_nik'] && ($value['pekerjaan_status'] == '0' || $value['pekerjaan_status'] == '-'))) ? 'y' : 'n';
          array_push($data, $isi);
        }
        echo json_encode($data);
      } else {
        foreach ($this->M_pekerjaan->getPekerjaanDispo($param) as $value) {
          foreach ($value as $key => $val) {
            $isi[$key] = $val;
          }
          $sql_total = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '" . $value['pekerjaan_status'] . "' AND id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND  id_user = '" . $session['pegawai_nik'] . "'");
          $isi_total = $sql_total->row_array();

          $isi['milik'] = ($isi_total['total'] > 0 || ($value['pic'] == $session['pegawai_nik'] && ($value['pekerjaan_status'] == '0' || $value['pekerjaan_status'] == '-'))) ? 'y' : 'n';
          array_push($data, $isi);
        }
        echo json_encode($data);
      }
    }
  }

  /* Get Pekerjaan Ususlan */

  /* Get Pekerjaan Ususlan */
  public function getPekerjaanUsulan()
  {
    $session = $this->session->userdata();

    $param_detail['pekerjaan_id'] = $this->input->get('pekerjaan_id');
    $param['pekerjaan_id'] = $this->input->get('pekerjaan_id');
    $param['id_user'] = $this->input->get_post('id_user_cari');
    $param['klasifikasi_pekerjaan_id'] = $this->input->get_post('klasifikasi_pekerjaan_id');
    $param['klasifikasi_pekerjaan_id_non_rkap'] = $this->input->get_post('klasifikasi_pekerjaan_id_non_rkap');

    $split = explode(',', $this->input->get_post('pekerjaan_status'));

    $param['pekerjaan_status'] = $split;

    $sql_pic = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan WHERE pic='" . $session['pegawai_nik'] . "'");
    $data_pic = $sql_pic->row_array();


    if ($session['pegawai_nik'] != '2190626') {
      if ($data_pic['total'] > 0) {
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
        foreach ($this->M_pekerjaan->getPekerjaan($param) as $value) {
          foreach ($value as $key => $val) {
            $isi[$key] = $val;
          }
          $sql_total = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '" . $value['pekerjaan_status'] . "' AND id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND  id_user = '" . $session['pegawai_nik'] . "' and is_aktif = 'y' ");
          $isi_total = $sql_total->row_array();

          $sql_proses = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '$value[pekerjaan_status]' AND id_pekerjaan = '$value[pekerjaan_id]' AND  id_user = '$session[pegawai_nik]' ORDER BY pekerjaan_disposisi_status DESC");

          $data_proses = $sql_proses->row_array();

          // print_r($data_proses);
          /* Tambahan */
          $sql_allow = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND  id_user = '" . $session['pegawai_nik'] . "' and is_aktif = 'y' ");
          $isi_allow = $sql_allow->row_array();

          $sql_ajuan_extend = $this->db->query("SELECT * FROM dec.dec_pekerjaan_extend WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND extend_status='0'");
          $isi_ajuan_extend = $sql_ajuan_extend->row_array();

          $sql_extend = $this->db->query("SELECT * FROM dec.dec_pekerjaan_extend WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND extend_status='1'");
          $isi_extend = $sql_extend->row_array();

          $isi['extend_ajuan_tanggal'] = (!empty($isi_ajuan_extend)) ? $isi_ajuan_extend['extend_tanggal'] : '';
          $isi['extend_ajuan_status'] = (!empty($isi_ajuan_extend)) ? $isi_ajuan_extend['extend_status'] : '';

          $isi['extend_tanggal'] = (!empty($isi_extend)) ? $isi_extend['extend_tanggal'] : '';
          $isi['extend_status'] = (!empty($isi_extend)) ? $isi_extend['extend_status'] : '';

          $isi['is_allow'] = ($isi_allow['total'] > 0 || $session['pegawai_nik'] == '2190626') ? 'y' : 'n';
          /* Tambahan */

          // $sql_disposisi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND id_user = '" . $session['pegawai_nik'] . "' AND is_aktif = 'y' AND pekerjaan_disposisi_status='" . $this->input->get_post('pekerjaan_disposisi_status')          . "'");

          // $data_disposisi = $sql_disposisi->row_array();

          $isi['milik'] = ($isi_total['total'] > 0 || ($value['pic'] == $session['pegawai_nik'] && ($value['pekerjaan_status'] == '0' || $value['pekerjaan_status'] == '-'))) ? 'y' : 'n';

          $isi['is_proses'] = ($isi_total['total'] > 0 && $data_proses['is_proses']) ? $data_proses['is_proses'] : null;
          $isi['is_disposisi_aktif'] = ($isi_total['total'] > 0 && $data_proses['is_aktif'] == 'y') ? 'y' : 'n';
          array_push($data, $isi);
        }
        echo json_encode($data);
      } else {
        foreach ($this->M_pekerjaan->getPekerjaanDispo($param) as $value) {
          foreach ($value as $key => $val) {
            $isi[$key] = $val;
          }
          $sql_total = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '" . $value['pekerjaan_status'] . "' AND id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND  id_user = '" . $session['pegawai_nik'] . "' and  is_aktif = 'y' ");
          $isi_total = $sql_total->row_array();

          $sql_proses = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '$value[pekerjaan_status]' AND id_pekerjaan = '$value[pekerjaan_id]' AND  id_user = '$session[pegawai_nik]' and is_aktif = 'y' ORDER BY pekerjaan_disposisi_status DESC");

          $data_proses = $sql_proses->row_array();

          /* Tambahan */
          $sql_allow = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND  id_user = '" . $session['pegawai_nik'] . "' and is_aktif = 'y' ");
          $isi_allow = $sql_allow->row_array();

          $sql_ajuan_extend = $this->db->query("SELECT * FROM dec.dec_pekerjaan_extend WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND extend_status='0'");
          $isi_ajuan_extend = $sql_ajuan_extend->row_array();

          $sql_extend = $this->db->query("SELECT * FROM dec.dec_pekerjaan_extend WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND extend_status='1'");
          $isi_extend = $sql_extend->row_array();

          $isi['extend_ajuan_tanggal'] = (!empty($isi_ajuan_extend)) ? $isi_ajuan_extend['extend_tanggal'] : '';
          $isi['extend_ajuan_status'] = (!empty($isi_ajuan_extend)) ? $isi_ajuan_extend['extend_status'] : '';

          $isi['extend_tanggal'] = (!empty($isi_extend)) ? $isi_extend['extend_tanggal'] : '';
          $isi['extend_status'] = (!empty($isi_extend)) ? $isi_extend['extend_status'] : '';

          $isi['is_allow'] = ($isi_allow['total'] > 0) ? 'y' : 'n';
          /* Tambahan */

          $isi['milik'] = ($isi_total['total'] > 0 || ($value['pic'] == $session['pegawai_nik'] && ($value['pekerjaan_status'] == '0' || $value['pekerjaan_status'] == '-'))) ? 'y' : 'n';
          $isi['is_proses'] = ($data_proses['is_proses'] == 'y') ? 'y' : 'n';
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
  /* GET */

  // CC Didokumen 
  public function getDokumenCC()
  {
    $param['pegawai_nama'] = $this->input->get_post('q');
    echo json_encode($this->M_pekerjaan->getUserStaf($param));
  }
  // CC Didokumen

  /* PROSES */
  /* Pekerjaan Dreft */
  public function insertPekerjaan()
  {
    $isi = $this->session->userdata();
    $pekerjaan_status = '0';

    $data['pekerjaan_id'] = anti_inject($this->input->post('pekerjaan_id'));
    $data['pekerjaan_waktu'] = anti_inject($this->input->post('pekerjaan_waktu') . " " . date('H:i:s'));
    $data['pekerjaan_waktu_akhir'] = anti_inject($this->input->post('pekerjaan_waktu_akhir'));
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


    $sql_pekerjaan = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan WHERE pekerjaan_id = '" . anti_inject($this->input->get_post('pekerjaan_id')) . "'");
    $data_pekerjaan = $sql_pekerjaan->row_array();
    if ($data_pekerjaan['total'] < 1) {
      $this->M_pekerjaan->insertPekerjaan($data);
    }

    // Email


    dblog('I',  $data['pekerjaan_id'], 'Pekerjaan Tersimpan di Draft');
  }
  /* Pekerjaan Dreft */

  /* Pekerjaan Send */
  public function insertPekerjaanSend()
  {
    $isi = $this->session->userdata();

    if (anti_inject($this->input->post('jabatan_temp') == '2')) $pekerjaan_status = '3';
    elseif (anti_inject($this->input->post('jabatan_temp') == '3')) $pekerjaan_status = '2';
    else $pekerjaan_status = '1';

    $pekerjaan_status_temp = anti_inject($this->input->post('pekerjaan_status'));
    $pekerjaan_id = anti_inject($this->input->post('pekerjaan_id'));

    if ($pekerjaan_status_temp == '1') { // Ketika ada dreft
      $data['pekerjaan_judul'] = anti_inject($this->input->post('pekerjaan_judul'));
      $data['pekerjaan_waktu'] = $this->input->post('pekerjaan_waktu') . " " . date('H:i:s');
      $data['pekerjaan_waktu_akhir'] = $this->input->post('pekerjaan_waktu_akhir');
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

      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
    } else { // Ketika langsung send
      $data['pekerjaan_id'] = anti_inject($this->input->post('pekerjaan_id'));
      $data['pekerjaan_judul'] = anti_inject($this->input->post('pekerjaan_judul'));
      $data['pekerjaan_waktu'] = $this->input->post('pekerjaan_waktu') . " " . date('H:i:s');
      $data['pekerjaan_waktu_akhir'] = $this->input->post('pekerjaan_waktu_akhir');
      $data['id_klasifikasi_pekerjaan'] = anti_inject($this->input->post('id_klasifikasi_pekerjaan'));
      $data['pekerjaan_deskripsi'] = $this->input->post('pekerjaan_deskripsi');
      $data['pic'] = anti_inject($isi['pegawai_nik']);
      $data['pic_no_telp'] = anti_inject($this->input->post('pic_no_telp'));
      $data['pekerjaan_status'] = anti_inject($pekerjaan_status);
      $data['id_pekerjaan_disposisi'] = anti_inject($this->input->post('id_pekerjaan_disposisi'));
      $data['pekerjaan_tahun'] = $this->input->post('pekerjaan_tahun');
      $data['pekerjaan_reviewer'] = $this->input->post('reviewer');
      $data['pekerjaan_approver'] = $this->input->post('approver');

      // cek apakah sudah ada pekerjaan
      $sql_pekerjaan = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan WHERE pekerjaan_id = '" . anti_inject($this->input->get_post('pekerjaan_id')) . "'");
      $data_pekerjaan = $sql_pekerjaan->row_array();
      if ($data_pekerjaan['total'] < 1) {
        $this->M_pekerjaan->insertPekerjaan($data);
      }
    }

    dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Send ke AVP Customer');

    /* User */
    // if ($pekerjaan_status == '3') {
    //   $data_user['pegawai_poscode'] = 'E53000000';
    //   $user = $this->M_user->getUserBantuan($data_user);
    // } else {
    //   $data_user['pegawai_poscode'] = $isi['pegawai_direct_superior'];
    // }
    $data_user['pegawai_poscode'] = ($pekerjaan_status == '3') ? 'E53000000' : $isi['pegawai_direct_superior'];
    $user = $this->M_user->getUser($data_user);

    /* User */

    /* Disposisi */
    if ($this->input->post('reviewer') != '' || $this->input->post('reviewer') != null) {
      $param['pegawai_nik'] = $this->input->post('reviewer');
      $userReviewer = $this->M_pekerjaan->getUserListRevApp2($param);

      $data_disposisi['pekerjaan_disposisi_id'] = create_id();
      $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
      $data_disposisi['id_user'] = htmlentities($userReviewer['pegawai_nik']);
      $data_disposisi['id_pekerjaan'] = htmlentities($pekerjaan_id);
      $data_disposisi['pekerjaan_disposisi_status'] = htmlentities($pekerjaan_status);

      $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

      $email_penerima = htmlentities($userReviewer['email_pegawai']);
      $subjek = htmlentities($this->input->get_post('pekerjaan_judul'));
      $pesan = ($this->input->get_post('pekerjaan_deskripsi'));
      $sendmail = array(
        'email_penerima' => $email_penerima,
        'subjek' => $subjek,
        'content' => $pesan,
      );      // INSERT KE DB EMAIL
      $param_email['email_id'] = create_id();
      $param_email['id_penerima'] = htmlentities($userReviewer['pegawai_nik']);
      $param_email['id_pengirim'] = htmlentities($isi['pegawai_nik']);
      $param_email['id_pekerjaan'] = htmlentities($this->input->get_post('pekerjaan_id'));
      $param_email['id_pekerjaan_disposisi'] = htmlentities($data_disposisi['pekerjaan_disposisi_id']);
      $param_email['email_subject'] = htmlentities($subjek);
      $param_email['email_content'] = htmlentities($pesan);
      $param_email['when_created'] = date('Y-m-d H:i:s');
      $param_email['who_created'] = htmlentities($isi['pegawai_nama']);

      $this->M_pekerjaan->insertEmail($param_email);
    }

    if ($this->input->post('approver') != '' || $this->input->post('approver') != null) {
      $param['pegawai_nik'] = $this->input->post('approver');
      $userApprover = $this->M_pekerjaan->getUserListRevApp2($param);

      $data_disposisi2['pekerjaan_disposisi_id'] = create_id();
      $data_disposisi2['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
      $data_disposisi2['id_user'] = htmlentities($userApprover['pegawai_nik']);
      $data_disposisi2['id_pekerjaan'] = htmlentities($pekerjaan_id);
      $data_disposisi2['pekerjaan_disposisi_status'] = '2';

      $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi2);

      $email_penerima = htmlentities($userApprover['email_pegawai']);
      $subjek = htmlentities($this->input->get_post('pekerjaan_judul'));
      $pesan = ($this->input->get_post('pekerjaan_deskripsi'));
      $sendmail = array(
        'email_penerima' => $email_penerima,
        'subjek' => $subjek,
        'content' => $pesan,
      );      // INSERT KE DB EMAIL
      $param_email2['email_id'] = create_id();
      $param_email2['id_penerima'] = htmlentities($userApprover['pegawai_nik']);
      $param_email2['id_pengirim'] = htmlentities($isi['pegawai_nik']);
      $param_email2['id_pekerjaan'] = htmlentities($this->input->get_post('pekerjaan_id'));
      $param_email2['id_pekerjaan_disposisi'] = htmlentities($data_disposisi['pekerjaan_disposisi_id']);
      $param_email2['email_subject'] = htmlentities($subjek);
      $param_email2['email_content'] = htmlentities($pesan);
      $param_email2['when_created'] = date('Y-m-d H:i:s');
      $param_email2['who_created'] = htmlentities($isi['pegawai_nama']);

      $this->M_pekerjaan->insertEmail($param_email2);
    }
    /* Disposisi */
  }
  /* Pekerjaan Send */

  /* Pekerjaan Edit */
  public function updatePekerjaan()
  {
    $pekerjaan_id = $this->input->post('pekerjaan_id');
    if ($pekerjaan_id) {
      $data['pekerjaan_judul'] = htmlentities($this->input->post('pekerjaan_judul'));
      $data['pekerjaan_waktu'] = $this->input->post('pekerjaan_waktu') . " " . date('H:i:s');
      $data['pekerjaan_waktu_akhir'] = $this->input->post('pekerjaan_waktu_akhir');
      $data['id_klasifikasi_pekerjaan'] = htmlentities($this->input->post('id_klasifikasi_pekerjaan'));
      $data['pekerjaan_deskripsi'] = ($this->input->post('pekerjaan_deskripsi'));
      $data['pic'] = htmlentities($this->input->post('pic'));
      $data['pic_no_telp'] = htmlentities($this->input->post('pic_no_telp'));
      $data['id_pekerjaan_disposisi'] = htmlentities($this->input->post('id_pekerjaan_disposisi'));
      $data['tipe_pekerjaan'] = htmlentities($this->input->post('tipe_pekerjaan'));
      $data['pekerjaan_status'] = htmlentities('0');
      $data['pekerjaan_tahun'] = $this->input->post('pekerjaan_tahun');
      $data['pekerjaan_reviewer'] = $this->input->post('reviewer');
      $data['pekerjaan_approver'] = $this->input->post('approver');


      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

      dblog('E', $pekerjaan_id, 'Pekerjaan Telah di Edit');
    }
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
        $fileExt       = str_replace('.', '', $fileExt); // Extension
        $fileName      = preg_replace("/\.[^.\s]{3,4}$/", "", $fileName);
        // $newFileName   = str_replace(' ', '', $random . '_' . date('ymdhis') . '.' . $fileExt);
        $newFileName = str_replace(' ', '', $_POST['id_pekerjaan'] . '_' . date('ymdhis') . '_' . $random . '.' . $fileExt);

        // $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        // $fileName = pathinfo($fileName, PATHINFO_FILENAME);

        if (in_array(strtolower($fileExt), $Extension)) {
          move_uploaded_file($tmpFile, $directory . $newFileName);
          echo $newFileName;
        }
      }
    }
  }




  /* Insert Pekerjaan Dokumen */
  public function insertPekerjaanDokumenUsulan()
  {
    $user = $this->session->userdata();

    $data['pekerjaan_dokumen_id'] = htmlentities(create_id());
    $data['id_pekerjaan'] = htmlentities($this->input->get_post('pekerjaan_id'));
    $data['pekerjaan_dokumen_nama'] = htmlentities($this->input->get_post('pekerjaan_dokumen_nama'));
    $data['pekerjaan_dokumen_file'] = htmlentities($this->input->get_post('savedFileName'));
    $data['pekerjaan_dokumen_status'] = htmlentities('1');
    $data['who_create'] = htmlentities($user['pegawai_nama']);
    $data['id_create'] = htmlentities($user['pegawai_nik']);
    $data['is_lama'] = htmlentities('n');
    $data['pekerjaan_dokumen_awal'] = htmlentities('y');

    $this->M_pekerjaan->insertPekerjaanDokumen($data);
  }
  /* Insert Pekerjaan Dokumen */

  /* Update Pekerjaan Dokumen */
  public function updatePekerjaanDokumen()
  {
    $id = $this->input->post('pekerjaan_dokumen_id');
    $data = array(
      'id_pekerjaan' => htmlentities($this->input->post('id_pekerjaan')),
      'pekerjaan_dokumen_nama' => htmlentities($this->input->post('pekerjaan_dokumen_nama')),
      'pekerjaan_dokumen_file' => htmlentities($this->input->post('savedFileName')),
      'pekerjaan_dokumen_status' => htmlentities('1'),
    );

    $this->M_pekerjaan->updatePekerjaanDokumen($data, $id);
  }
  /* Update Pekerjaan Dokumen */

  /* Proses Send VP */
  public function prosesSendVP()
  {
    if (isset($_GET['id_user'])) {
      $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '".$_GET['id_user']."'");
      $isi = $sql_isi->row_array();
    } else {
      $isi = $this->session->userdata();      
    }

    $pekerjaan_id = htmlentities($this->input->get_post('id_pekerjaan_send_vp'));
    $id_tanggung_jawab = null;
    $pekerjaan_status_send_vp = htmlentities('8');
    // CC BIASA
    $is_cc = 'y';

    if ($this->input->get_post('id_user_send_vp')) {
      $this->M_pekerjaan->deletePekerjaanDisposisi($pekerjaan_id, null, $id_tanggung_jawab, $pekerjaan_status_send_vp, $is_cc);

      $user = $this->input->get_post('id_user_send_vp');
      foreach ($user as $key => $value) {
        $data_disposisi_vp['pekerjaan_disposisi_id'] = htmlentities(create_id());
        $data_disposisi_vp['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
        $data_disposisi_vp['id_user'] = $value;
        $data_disposisi_vp['id_pekerjaan'] = htmlentities($pekerjaan_id);
        $data_disposisi_vp['pekerjaan_disposisi_status'] = htmlentities('8');
        $data_disposisi_vp['id_penanggung_jawab'] = htmlentities('n');
        $data_disposisi_vp['is_cc'] = 'y';

        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_vp);
      }
    }
    // CC BIASA

    //  CC HPS
    $is_cc_hps = 'h';
    if ($this->input->get_post('id_user_send_vp_hps')) {
      $this->M_pekerjaan->deletePekerjaanDisposisi($pekerjaan_id, null, $id_tanggung_jawab, $pekerjaan_status_send_vp, $is_cc_hps);
      $user = $this->input->get_post('id_user_send_vp_hps');
      foreach ($user as $key => $value) {
        $data_disposisi_vp['pekerjaan_disposisi_id'] = htmlentities(create_id());
        $data_disposisi_vp['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
        $data_disposisi_vp['id_user'] = $value;
        $data_disposisi_vp['id_pekerjaan'] = htmlentities($pekerjaan_id);
        $data_disposisi_vp['pekerjaan_disposisi_status'] = htmlentities('8');
        $data_disposisi_vp['id_penanggung_jawab'] = htmlentities('n');
        $data_disposisi_vp['is_cc'] = 'h';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_vp);
      }
    }
    //  CC HPS


    $where_id_user = ($isi['pegawai_nik']);
    $where_id_pekerjaan = (($pekerjaan_id));
    $where_disposisi_status = '6';
    $param_staf['is_proses'] = 'y';
    $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);

    $pekerjaan_status = '7';

    // cek apakah koordinator atau bukan
    $sql_koordinator = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . anti_inject($this->input->get_post('id_pekerjaan_send_vp')) . "' AND id_penanggung_jawab = '" . anti_inject('y') . "' AND pekerjaan_disposisi_status = '" . anti_inject('4') . "' AND id_user = '" . $isi['pegawai_nik'] . "'");

    $data_koordinator = $sql_koordinator->row_array();
    // echo $this->db->last_query();

    // print_r($data_koordinator);
    // cek apakah koordinator atau bukan

    $pekerjaan_id = $this->input->get_post('id_pekerjaan_send_vp');
    if ($pekerjaan_id) {
      if ($data_koordinator['total'] > '0') {
        $data['pekerjaan_status'] = htmlentities($pekerjaan_status);
        $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Review Oleh Cangun');

        /* User */
        $data_user['pegawai_poscode'] = $isi['pegawai_direct_superior'];

        $user = $this->M_user->getUser($data_user);
        /* User */

        // cek vp
        // $sql_vp =  
        // cek vp

        /* Get Pekerjaan */
        $sql_pekerjaan = $this->db->query("SELECT pic FROM dec.dec_pekerjaan WHERE pekerjaan_id = '" . $pekerjaan_id . "'");
        $isi_pekerjaan = $sql_pekerjaan->row_array();
        /* Get Pekerjaan */

        /* Disposisi */
        $data_disposisi['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
        $data_disposisi['id_user'] = ($pekerjaan_status == '8') ? $isi_pekerjaan['pic'] : $user['pegawai_nik'];
        $data_disposisi['id_pekerjaan'] = htmlentities($pekerjaan_id);
        $data_disposisi['pekerjaan_disposisi_status'] = htmlentities($pekerjaan_status);
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
      }
    }
    /* Pekerjaan */

    /* Dokumen */
      $sql_bagian = $this->db->query("SELECT id_bagian FROM global.global_bagian_detail WHERE id_pegawai = '".$isi['pegawai_nik']."'");
      $data_bagian = $sql_bagian->row_array();

      $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_status = '3', is_proses = 'y' WHERE id_pekerjaan = '".$pekerjaan_id."' AND id_create IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian = '".$data_bagian['id_bagian']."')");
    /* Dokumen */
  }
  /* Proses Send VP */

  /* Proses Send VP  Koor*/
  public function prosesSendVPKoor()
  {
    $isi = $this->session->userdata();
    $pekerjaan_id = htmlentities($this->input->get_post('id_pekerjaan_send_vp'));
    $id_tanggung_jawab = null;
    $pekerjaan_status_send_vp = htmlentities('8');
    // CC Biasa
    $is_cc = 'y';
    if ($this->input->get_post('id_user_send_vp')) {
      $this->M_pekerjaan->deletePekerjaanDisposisi($pekerjaan_id, null, $id_tanggung_jawab, $pekerjaan_status_send_vp, $is_cc);
      $user = $this->input->get_post('id_user_send_vp');
      foreach ($user as $key => $value) {
        $data_disposisi_vp['pekerjaan_disposisi_id'] = htmlentities(create_id());
        $data_disposisi_vp['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
        $data_disposisi_vp['id_user'] = $value;
        $data_disposisi_vp['id_pekerjaan'] = htmlentities($pekerjaan_id);
        $data_disposisi_vp['pekerjaan_disposisi_status'] = htmlentities('8');
        $data_disposisi_vp['id_penanggung_jawab'] = htmlentities('n');
        $data_disposisi_vp['is_cc'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_vp);
      }
    }
    // CC Biasa

    //  CC HPS
    $is_cc_hps = 'h';
    if ($this->input->get_post('id_user_send_vp_hps')) {
      $this->M_pekerjaan->deletePekerjaanDisposisi($pekerjaan_id, null, $id_tanggung_jawab, $pekerjaan_status_send_vp, $is_cc_hps);
      $user = $this->input->get_post('id_user_send_vp_hps');
      foreach ($user as $key => $value) {
        $data_disposisi_vp['pekerjaan_disposisi_id'] = htmlentities(create_id());
        $data_disposisi_vp['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
        $data_disposisi_vp['id_user'] = $value;
        $data_disposisi_vp['id_pekerjaan'] = htmlentities($pekerjaan_id);
        $data_disposisi_vp['pekerjaan_disposisi_status'] = htmlentities('8');
        $data_disposisi_vp['id_penanggung_jawab'] = htmlentities('n');
        $data_disposisi_vp['is_cc'] = 'h';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_vp);
      }
    }
    //  CC HPS


    $where_id_user = ($isi['pegawai_nik']);
    $where_id_pekerjaan = (($pekerjaan_id));
    $where_disposisi_status = '6';
    $param_staf['is_proses'] = 'y';
    $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);

    $pekerjaan_status = '7';

    // cek apakah koordinator atau bukan
    $sql_koordinator = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . anti_inject($this->input->get_post('id_pekerjaan_send_vp')) . "' AND id_penanggung_jawab = '" . anti_inject('y') . "' AND pekerjaan_disposisi_status = '" . anti_inject('6') . "' AND id_user = '" . $isi['pegawai_nik'] . "'");

    $data_koordinator = $sql_koordinator->row_array();
    // echo $this->db->last_query();

    // print_r($data_koordinator);
    // cek apakah koordinator atau bukan

    // $pekerjaan_id = $this->input->get_post('id_pekerjaan_send_vp');
    // if ($pekerjaan_id) {
    //   if ($data_koordinator['total'] > '0') {
    //     $data['pekerjaan_status'] = htmlentities($pekerjaan_status);
    //     $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
    //     dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Review Oleh Cangun');

    //     /* User */
    //     $data_user['pegawai_poscode'] = $isi['pegawai_direct_superior'];

    //     $user = $this->M_user->getUser($data_user);
    //     /* User */

    //     /* Get Pekerjaan */
    //     $sql_pekerjaan = $this->db->query("SELECT pic FROM dec.dec_pekerjaan WHERE pekerjaan_id = '" . $pekerjaan_id . "'");
    //     $isi_pekerjaan = $sql_pekerjaan->row_array();
    //     /* Get Pekerjaan */

    //     /* Disposisi */
    //     $data_disposisi['pekerjaan_disposisi_id'] = create_id();
    //     $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
    //     $data_disposisi['id_user'] = ($pekerjaan_status == '8') ? $isi_pekerjaan['pic'] : $user['pegawai_nik'];
    //     $data_disposisi['id_pekerjaan'] = htmlentities($pekerjaan_id);
    //     $data_disposisi['pekerjaan_disposisi_status'] = htmlentities($pekerjaan_status);
    //     $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
    //   }
    // }

    /* Pekerjaan */



    /* User */
    // $data_user['pegawai_poscode'] = $isi['pegawai_direct_superior'];

    // $user = $this->M_user->getUser($data_user);
    // /* User */

    // /* Get Pekerjaan */
    // $sql_pekerjaan = $this->db->query("SELECT pic FROM dec.dec_pekerjaan WHERE pekerjaan_id = '" . $pekerjaan_id . "'");
    // $isi_pekerjaan = $sql_pekerjaan->row_array();
    // /* Get Pekerjaan */

    // /* Disposisi */
    // $data_disposisi['pekerjaan_disposisi_id'] = create_id();
    // $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
    // $data_disposisi['id_user'] = ($pekerjaan_status == '8') ? $isi_pekerjaan['pic'] : $user['pegawai_nik'];
    // $data_disposisi['id_pekerjaan'] = htmlentities($pekerjaan_id);
    // $data_disposisi['pekerjaan_disposisi_status'] = htmlentities($pekerjaan_status);
    // $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
    /* Disposisi */
  }
  /* Proses Send VP  Koor*/


  /* Proses Send VP */
  public function prosesSendVPIFC()
  {
    $isi = $this->session->userdata();
    $pekerjaan_id = htmlentities($this->input->get_post('id_pekerjaan_send_vp'));
    $id_tanggung_jawab = null;
    $pekerjaan_status_send_vp = htmlentities('8');
    $is_cc = 'y';

    if ($this->input->get_post('id_user_send_vp')) {

      $this->M_pekerjaan->deletePekerjaanDisposisi($pekerjaan_id, null, $id_tanggung_jawab, $pekerjaan_status_send_vp, $is_cc);

      $user = $this->input->get_post('id_user_send_vp');
      foreach ($user as $key => $value) {
        $data_disposisi_vp['pekerjaan_disposisi_id'] = htmlentities(create_id());
        $data_disposisi_vp['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
        $data_disposisi_vp['id_user'] = $value;
        $data_disposisi_vp['id_pekerjaan'] = htmlentities($pekerjaan_id);
        $data_disposisi_vp['pekerjaan_disposisi_status'] = htmlentities('8');
        $data_disposisi_vp['id_penanggung_jawab'] = htmlentities('n');
        $data_disposisi_vp['is_cc'] = 'y';
        $data_disposisi_vp['is_aktif'] = 'y';

        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_vp);
      }
    }

    $is_cc_hps = 'h';

    if ($this->input->get_post('id_user_send_vp_hps')) {

      $this->M_pekerjaan->deletePekerjaanDisposisi($pekerjaan_id, null, $id_tanggung_jawab, $pekerjaan_status_send_vp, $is_cc_hps);

      $user = $this->input->get_post('id_user_send_vp');
      foreach ($user as $key => $value) {
        $data_disposisi_vp['pekerjaan_disposisi_id'] = htmlentities(create_id());
        $data_disposisi_vp['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
        $data_disposisi_vp['id_user'] = $value;
        $data_disposisi_vp['id_pekerjaan'] = htmlentities($pekerjaan_id);
        $data_disposisi_vp['pekerjaan_disposisi_status'] = htmlentities('8');
        $data_disposisi_vp['id_penanggung_jawab'] = htmlentities('n');
        $data_disposisi_vp['is_cc'] = 'h';
        $data_disposisi_vp['is_aktif'] = 'y';

        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_vp);
      }
    }



    $where_id_user = ($isi['pegawai_nik']);
    $where_id_pekerjaan = (($pekerjaan_id));
    $where_disposisi_status = '10';
    $param_staf['is_proses'] = 'y';
    $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);

    $pekerjaan_status = '11';

    // cek apakah koordinator atau bukan
    $sql_koordinator = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . anti_inject($this->input->get_post('id_pekerjaan_send_vp')) . "' AND id_penanggung_jawab = '" . anti_inject('y') . "' AND pekerjaan_disposisi_status = '" . anti_inject('10') . "' AND id_user = '" . $isi['pegawai_nik'] . "'");

    $data_koordinator = $sql_koordinator->row_array();

    // cek apakah koordinator atau bukan

    $pekerjaan_id = $this->input->get_post('id_pekerjaan_send_vp');
    if ($pekerjaan_id) {
      if ($data_koordinator['total'] > '0') {
        $data['pekerjaan_status'] = htmlentities($pekerjaan_status);
        $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Review Oleh Cangun');

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
        $data_disposisi['id_pekerjaan'] = htmlentities($pekerjaan_id);
        $data_disposisi['pekerjaan_disposisi_status'] = htmlentities($pekerjaan_status);
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
      }
    }
    /* Pekerjaan */



    /* User */
    // $data_user['pegawai_poscode'] = $isi['pegawai_direct_superior'];

    // $user = $this->M_user->getUser($data_user);
    // /* User */

    // /* Get Pekerjaan */
    // $sql_pekerjaan = $this->db->query("SELECT pic FROM dec.dec_pekerjaan WHERE pekerjaan_id = '" . $pekerjaan_id . "'");
    // $isi_pekerjaan = $sql_pekerjaan->row_array();
    // /* Get Pekerjaan */

    // /* Disposisi */
    // $data_disposisi['pekerjaan_disposisi_id'] = create_id();
    // $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
    // $data_disposisi['id_user'] = ($pekerjaan_status == '8') ? $isi_pekerjaan['pic'] : $user['pegawai_nik'];
    // $data_disposisi['id_pekerjaan'] = htmlentities($pekerjaan_id);
    // $data_disposisi['pekerjaan_disposisi_status'] = htmlentities($pekerjaan_status);
    // $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
    /* Disposisi */
  }


  /* Proses Send VP */

  public function prosesApproveVP()
  {
    $isi = $this->session->userdata();
    $pekerjaan_id = htmlentities($this->input->get_post('id_pekerjaan_approve_vp'));
    $id_tanggung_jawab = null;
    $pekerjaan_status_approve_vp = htmlentities('8');
    // CC BIASA
    $is_cc = 'y';
    // $pekerjaan_status = '9';
    if ($this->input->get_post('id_user_approve_vp')) {
      $this->M_pekerjaan->deletePekerjaanDisposisi($pekerjaan_id, null, $id_tanggung_jawab, $pekerjaan_status_approve_vp, $is_cc);
      $user = $this->input->get_post('id_user_approve_vp');
      foreach ($user as $key => $value) {
        $data_disposisi_vp['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_vp['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
        $data_disposisi_vp['id_user'] = htmlentities($value);
        $data_disposisi_vp['id_pekerjaan'] = htmlentities($pekerjaan_id);
        $data_disposisi_vp['pekerjaan_disposisi_status'] = htmlentities('8');
        $data_disposisi_vp['id_penanggung_jawab'] = htmlentities('n');
        $data_disposisi_vp['is_cc'] = 'y';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_vp);
      }
    }
    // CC BIASA

    //  CC HPS
    $is_cc_hps = 'h';
    if ($this->input->get_post('id_user_approve_vp_hps')) {
      $this->M_pekerjaan->deletePekerjaanDisposisi($pekerjaan_id, null, $id_tanggung_jawab, $pekerjaan_status_approve_vp, $is_cc_hps);
      $user = $this->input->get_post('id_user_approve_vp_hps');
      foreach ($user as $key => $value) {
        $data_disposisi_vp['pekerjaan_disposisi_id'] = htmlentities(create_id());
        $data_disposisi_vp['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
        $data_disposisi_vp['id_user'] = $value;
        $data_disposisi_vp['id_pekerjaan'] = htmlentities($pekerjaan_id);
        $data_disposisi_vp['pekerjaan_disposisi_status'] = htmlentities('8');
        $data_disposisi_vp['id_penanggung_jawab'] = htmlentities('n');
        $data_disposisi_vp['is_cc'] = 'h';
        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_vp);
      }
    }
    //  CC HPS

    /* Pekerjaan */
    $pekerjaan_status = $this->input->get_post('pekerjaan_status') + 1;
    // $pekerjaan_status = '9';

    $pekerjaan_id = htmlentities($this->input->get_post('id_pekerjaan_approve_vp'));
    $param['pekerjaan_id'] = htmlentities($this->input->get_post('id_pekerjaan_approve_vp'));
    $data_pekerjaan = $this->M_pekerjaan->getPekerjaan($param);
    // print_r($data_pekerjaan);
    if ($pekerjaan_id) {
      if ($data_pekerjaan['id_klasifikasi_pekerjaan'] == '616b79fa38c26380f49f3b84f088b8f86f9cd176') {
        $data['pekerjaan_status'] = htmlentities('15');
        $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

        $this->db->query("UPDATE dec.dec_pekerjaan_progress SET progress_jumlah = '100' WHERE id_pekerjaan = '" . $pekerjaan_id . "'");
      } else {
        $data['pekerjaan_status'] = htmlentities($pekerjaan_status);
        $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
        // print_r($this->db->last_query());
      }
      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Approve Oleh  Cangun');
    }
    /* Pekerjaan */

    /* User */
    $data_user['pegawai_poscode'] = $isi['pegawai_direct_superior'];

    $user = $this->M_user->getUser($data_user);
    // print_r($this->db->last_query());
    /* User */

    /* Get Pekerjaan */
    $sql_pekerjaan = $this->db->query("SELECT pic FROM dec.dec_pekerjaan WHERE pekerjaan_id = '" . $pekerjaan_id . "'");
    $isi_pekerjaan = $sql_pekerjaan->row_array();
    /* Get Pekerjaan */

    /* Disposisi */
    $data_disposisi['pekerjaan_disposisi_id'] = create_id();
    $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
    $data_disposisi['id_user'] = ($pekerjaan_status == '8') ? $isi_pekerjaan['pic'] : $user['pegawai_nik'];
    $data_disposisi['id_pekerjaan'] = htmlentities($pekerjaan_id);
    $data_disposisi['pekerjaan_disposisi_status'] = htmlentities($pekerjaan_status);
    // print_r($data_disposisi);
    $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
    /* Disposisi */

    // update status proses ke y
    $where_id_user = ($isi['pegawai_nik']);
    $where_id_pekerjaan = (($pekerjaan_id));
    $where_disposisi_status = $this->input->get_post('pekerjaan_status');
    $param_staf['is_proses'] = 'y';
    $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);
    // update status proses ke y

    /* Dokumen */
      $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_status = '4', is_proses = 'a' WHERE is_lama = 'n' AND pekerjaan_dokumen_awal != 'y' AND id_pekerjaan = '".$pekerjaan_id."'");
    /* Dokumen */
  }

  /* PROSES */

  /* DELETE */
  /* Delete Pekerjaan */
  public function deletePekerjaan()
  {
    $this->M_pekerjaan->deletePekerjaan($this->input->get('pekerjaan_id'));
  }
  /* Delete Pekerjaan */

  /* Delete Pekerjaan Dokumen */
  public function deletePekerjaanDokumen()
  {
    $this->M_pekerjaan->deletePekerjaanDokumen($this->input->post('pekerjaan_dokumen_id'));
    // print_r($this->db->last_query());
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

    $sql_pic = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan WHERE pic='" . $session['pegawai_nik'] . "'");
    $data_pic = $sql_pic->row_array();

    if ($session['pegawai_nik'] != '2190626') {
      if ($data_pic['total'] > 0) {
        $param['user_pic'] = $session['pegawai_nik'];
      } else {
        $param['user_disposisi'] = $session['pegawai_nik'];
      }
    }


    if (empty($param['id_user'])) {
      foreach ($this->M_pekerjaan->getPekerjaan($param) as $key => $value) {
        // echo $this->db->last_query();
        // print_r($value);
        $sql_total = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '5' AND id_pekerjaan = '" . $value['pekerjaan_id'] . "' ");
        $isi_total = $sql_total->row_array();

        $sql = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '" . $value['pekerjaan_status'] . "' AND id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND  id_user = '" . $session['pegawai_nik'] . "' ");
        $dataMilik = $sql->row_array();

        // data per progress
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


        $sql_listrik = $this->db->query("SELECT bagian_id,id_bagian,progress_jumlah FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian LEFT JOIN dec.dec_pekerjaan_disposisi c ON c.id_user = a.id_user   WHERE a.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND is_listin = 'L'");
        $data_listrik = $sql_listrik->row_array();

        $sql_jml_listrik = $this->db->query("SELECT COUNT(*) as total FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian LEFT JOIN dec.dec_pekerjaan_disposisi c ON c.id_user = a.id_user   WHERE a.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND is_listin = 'L'");
        $data_jml_listrik = $sql_jml_listrik->row_array();

        $sql_user_listrik = $this->db->query("SELECT klasifikasi_dokumen_inisial FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '5' AND c.id_bagian = 'f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND b.is_listin = 'L'");
        $data_user_listrik = $sql_user_listrik->row_array();

        $sql_user_listrik_koor = $this->db->query("SELECT count(*) AS total FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '4' AND id_penanggung_jawab = 'y' AND c.id_bagian = 'f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND b.is_listin = 'L'");
        $data_user_listrik_koor = $sql_user_listrik_koor->row_array();


        $sql_instrumen = $this->db->query("SELECT bagian_id,id_bagian,progress_jumlah FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian LEFT JOIN dec.dec_pekerjaan_disposisi c ON c.id_user = a.id_user  WHERE a.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND is_listin='I'");
        $data_instrumen = $sql_instrumen->row_array();

        $sql_jml_instrumen = $this->db->query("SELECT COUNT(*) as total FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian LEFT JOIN dec.dec_pekerjaan_disposisi c ON c.id_user = a.id_user  WHERE a.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND is_listin='I'");
        $data_jml_instrumen = $sql_jml_instrumen->row_array();

        $sql_user_instrumen = $this->db->query("SELECT klasifikasi_dokumen_inisial FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '5' AND c.id_bagian = 'f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND b.is_listin = 'I'");
        $data_user_instrumen = $sql_user_instrumen->row_array();

        $sql_user_instrumen_koor = $this->db->query("SELECT count(*) AS total FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '4' AND id_penanggung_jawab = 'y' AND c.id_bagian = 'f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND b.is_listin = 'I'");
        $data_user_instrumen_koor = $sql_user_instrumen_koor->row_array();
        // print_r($data_instrumen);
        // data per progress

        $sql_progress = $this->db->query("select pekerjaan_id,bagian_id,bagian_nama,progress_jumlah,id_pekerjaan from global.global_bagian a left join global.global_bagian_detail b on b.id_bagian = a.bagian_id left join dec.dec_pekerjaan_progress c on c.id_user = b.id_pegawai right join dec.dec_pekerjaan d on d.pekerjaan_id = c.id_pekerjaan where pekerjaan_id ='" . $value['pekerjaan_id'] . "' order by progress_jumlah desc");

        $isi_progress = $sql_progress->result_array();
        // var_dump($isi_progress);/


        $sql_jumlah_progress = $this->db->query("select count(*) as total from global.global_bagian a left join global.global_bagian_detail b on b.id_bagian = a.bagian_id left join dec.dec_pekerjaan_progress c on c.id_user = b.id_pegawai right join dec.dec_pekerjaan d on d.pekerjaan_id = c.id_pekerjaan where pekerjaan_id ='" . $value['pekerjaan_id'] . "' ");
        $isi_jumlah_proses = $sql_jumlah_progress->row_array();


        $isi['milik'] = ($dataMilik['total'] > 0 || ($value['pic'] == $session['pegawai_nik'] && ($value['pekerjaan_status'] == '0' || $value['pekerjaan_status'] == '-')) || $session['pegawai_nik'] == '2190626') ? 'y' : 'n';


        if (!empty($data_sipil['id_bagian']) && $data_sipil['id_bagian'] == '1483c0882e75988626fee21c5926cc63727734a0') {
          $isi['pekerjaan_sipil'] =  $data_sipil['progress_jumlah'];
          $isi['pekerjaan_isi_sipil'] =  (isset($data_user_sipil['klasifikasi_dokumen_inisial'])) ? $data_user_sipil['klasifikasi_dokumen_inisial'].'('.$data_sipil['progress_jumlah'].'%)' : '';
        } else {
          $isi['pekerjaan_sipil'] = 0;
          $isi['pekerjaan_isi_sipil'] = (isset($data_user_sipil['klasifikasi_dokumen_inisial'])) ? $data_user_sipil['klasifikasi_dokumen_inisial'].'(0%)' : '';
        }
        $isi['pekerjaan_isi_sipil'] = ($data_user_sipil_koor['total'] == 0) ? $isi['pekerjaan_isi_sipil'] : '<b>'.$isi['pekerjaan_isi_sipil'].'</b>';

        if (!empty($data_proses['bagian_id']) && $data_proses['bagian_id'] == 'c21f86a03fdf9f7420764ac49d664415cfc942eb') {
          $isi['pekerjaan_proses'] =  $data_proses['progress_jumlah'];
          $isi['pekerjaan_isi_proses'] =  (isset($data_user_proses['klasifikasi_dokumen_inisial'])) ? $data_user_proses['klasifikasi_dokumen_inisial'].'('.$data_proses['progress_jumlah'].'%)' : '';
        } else {
          $isi['pekerjaan_proses'] = 0;
          $isi['pekerjaan_isi_proses'] = (isset($data_user_proses['klasifikasi_dokumen_inisial'])) ? $data_user_proses['klasifikasi_dokumen_inisial'].'(0%)' : '';
        }
        $isi['pekerjaan_isi_proses'] = ($data_user_proses_koor['total'] == 0) ? $isi['pekerjaan_isi_proses'] : '<b>'.$isi['pekerjaan_isi_proses'].'</b>';

        if ((!empty($data_mesin['id_bagian'])) && $data_mesin['id_bagian'] == 'fd2aa961b30ede7622a57d42267edc5d5eae3e1b') {
          $isi['pekerjaan_mesin'] =  $data_mesin['progress_jumlah'];
          $isi['pekerjaan_isi_mesin'] =  (isset($data_user_mesin['klasifikasi_dokumen_inisial'])) ? $data_user_mesin['klasifikasi_dokumen_inisial'].'('.$data_mesin['progress_jumlah'].'%)' : '';
        } else {
          $isi['pekerjaan_mesin'] = 0;
          $isi['pekerjaan_isi_mesin'] = (isset($data_user_mesin['klasifikasi_dokumen_inisial'])) ? $data_user_mesin['klasifikasi_dokumen_inisial'].'(0%)' : '';
        }
        $isi['pekerjaan_isi_mesin'] = ($data_user_mesin_koor['total'] == 0) ? $isi['pekerjaan_isi_mesin'] : '<b>'.$isi['pekerjaan_isi_mesin'].'</b>';

        if ((!empty($data_listrik['bagian_id'])) && $data_listrik['bagian_id'] == 'f683cbbca693d1a08fc010fd861b7350efa3e8d2') {

          $isi['pekerjaan_listrik'] =  $data_listrik['progress_jumlah'];
          $isi['pekerjaan_isi_listrik'] =  (isset($data_user_listrik['klasifikasi_dokumen_inisial'])) ? $data_user_listrik['klasifikasi_dokumen_inisial'].'('.$data_listrik['progress_jumlah'].'%)' : '';
        } else {
          $isi['pekerjaan_listrik'] = 0;
          $isi['pekerjaan_isi_listrik'] = (isset($data_user_listrik['klasifikasi_dokumen_inisial'])) ? $data_user_listrik['klasifikasi_dokumen_inisial'].'(0%)' : '';
        }
        $isi['pekerjaan_isi_listrik'] = ($data_user_listrik_koor['total'] == 0) ? $isi['pekerjaan_isi_listrik'] : '<b>'.$isi['pekerjaan_isi_listrik'].'</b>';

        if ((!empty($data_instrumen['bagian_id'])) && $data_instrumen['bagian_id'] == 'f683cbbca693d1a08fc010fd861b7350efa3e8d2') {

          $isi['pekerjaan_instrumen'] =  $data_instrumen['progress_jumlah'];
          $isi['pekerjaan_isi_instrumen'] =  (isset($data_user_instrumen['klasifikasi_dokumen_inisial'])) ? $data_user_instrumen['klasifikasi_dokumen_inisial'].'('.$data_instrumen['progress_jumlah'].'%)' : '';
        } else {
          $isi['pekerjaan_instrumen'] = 0;
          $isi['pekerjaan_isi_instrumen'] = (isset($data_user_instrumen['klasifikasi_dokumen_inisial'])) ? $data_user_instrumen['klasifikasi_dokumen_inisial'].'(0%)' : '';
        }
        $isi['pekerjaan_isi_instrumen'] = ($data_user_instrumen_koor['total'] == 0) ? $isi['pekerjaan_isi_instrumen'] : '<b>'.$isi['pekerjaan_isi_instrumen'].'</b>';



        // foreach ($isi_progress as $key => $value_progress) {

        if ($data_jml_sipil['total'] > 0 && $data_sipil['bagian_id'] == '1483c0882e75988626fee21c5926cc63727734a0') {
          $isi['pekerjaan_jumlah_sipil'] = ($data_jml_sipil['total'] > 0) ? $data_jml_sipil['total'] : '0';
        } else {
          $isi['pekerjaan_jumlah_sipil'] = '0';
        }

        if ($data_jml_proses['total'] > 0 && $data_proses['bagian_id'] == 'c21f86a03fdf9f7420764ac49d664415cfc942eb') {
          $isi['pekerjaan_jumlah_proses'] = ($data_jml_proses['total'] > 0) ? $data_jml_proses['total'] : '0';
        } else {
          $isi['pekerjaan_jumlah_proses'] = '0';
        }

        if ($data_jml_mesin['total'] > 0 && $data_mesin['bagian_id'] == 'fd2aa961b30ede7622a57d42267edc5d5eae3e1b') {
          $isi['pekerjaan_jumlah_mesin'] = ($data_jml_mesin['total'] > 0) ? $data_jml_mesin['total'] : '0';
        } else {
          $isi['pekerjaan_jumlah_mesin'] = '0';
        }

        if ($data_jml_listrik['total'] > 0 && $data_listrik['bagian_id'] == 'f683cbbca693d1a08fc010fd861b7350efa3e8d2') {
          $isi['pekerjaan_jumlah_listrik'] = ($data_jml_listrik['total'] > 0) ? $data_jml_listrik['total'] : '0';
        } else {
          $isi['pekerjaan_jumlah_listrik'] = '0';
        }

        if ($data_jml_instrumen['total'] > 0 && $data_instrumen['bagian_id'] == 'f683cbbca693d1a08fc010fd861b7350efa3e8d2') {
          $isi['pekerjaan_jumlah_instrumen'] = ($data_jml_instrumen['total'] > 0) ? $data_jml_instrumen['total'] : '0';
        } else {
          $isi['pekerjaan_jumlah_instrumen'] = '0';
        }
        // }

        if (($isi['pekerjaan_proses'] + $isi['pekerjaan_mesin'] + $isi['pekerjaan_listrik'] + $isi['pekerjaan_instrumen'] + $isi['pekerjaan_sipil'] > 0) && ($isi['pekerjaan_jumlah_proses'] + $isi['pekerjaan_jumlah_mesin'] + $isi['pekerjaan_jumlah_listrik'] + $isi['pekerjaan_jumlah_instrumen'] + $isi['pekerjaan_jumlah_sipil'] > 0)) {
          $isi_progressnya = ($isi['pekerjaan_proses'] + $isi['pekerjaan_mesin'] + $isi['pekerjaan_listrik'] + $isi['pekerjaan_instrumen'] + $isi['pekerjaan_sipil']) / ($isi_total['total']);
        } else {
          $isi_progressnya = 0;
        }

        $sql_tgl_start = $this->db->query("SELECT pekerjaan_disposisi_waktu FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND pekerjaan_disposisi_status ='4'");
        $data_tgl_start = $sql_tgl_start->row_array();

        $sql_avp_review = $this->db->query("SELECT count(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND pekerjaan_disposisi_status ='6' AND id_user = '".$session['pegawai_nik']."' AND is_aktif = 'y'");
        $data_avp_review = $sql_avp_review->row_array();

        $status_avp = ($value['pekerjaan_status'] == '5' && $data_avp_review['total'] >= '1') ? '1' : '0';

        $isi['pekerjaan_progress'] = round($isi_progressnya,2);
        $isi['pekerjaan_id'] = $value['pekerjaan_id'];
        $isi['pekerjaan_nomor'] = $value['pekerjaan_nomor'];
        $isi['pekerjaan_judul'] = $value['pekerjaan_judul'];
        $isi['pekerjaan_status'] = $value['pekerjaan_status'];
        $isi['pegawai_nama'] = $value['pegawai_nama'];
        // $isi['pekerjaan_progress'] = $value['pekerjaan_progress'];
        $isi['total'] = $isi_total['total'];
        $isi['tanggal_akhir'] =  date("Y-m-d", strtotime($value['tanggal_akhir']));
        $isi['tanggal_start'] = ($data_tgl_start['pekerjaan_disposisi_waktu'] != '') ? date("Y-m-d", strtotime($data_tgl_start['pekerjaan_disposisi_waktu'])) : '-';
        $isi['pekerjaan_status'] = $value['pekerjaan_status'];
        $isi['status_avp'] = $status_avp;

        array_push($data, $isi);
      }
      echo json_encode($data);
    } else {
      foreach ($this->M_pekerjaan->getPekerjaanDispo($param) as $key => $value) {
        $sql_total = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '5' AND id_pekerjaan = '" . $value['pekerjaan_id'] . "' ");
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


        $sql_listrik = $this->db->query("SELECT bagian_id,id_bagian,progress_jumlah FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian LEFT JOIN dec.dec_pekerjaan_disposisi c ON c.id_user = a.id_user   WHERE a.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND is_listin = 'L'");
        $data_listrik = $sql_listrik->row_array();

        $sql_jml_listrik = $this->db->query("SELECT COUNT(*) as total FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian LEFT JOIN dec.dec_pekerjaan_disposisi c ON c.id_user = a.id_user   WHERE a.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND is_listin = 'L'");
        $data_jml_listrik = $sql_jml_listrik->row_array(); 

        $sql_user_listrik = $this->db->query("SELECT klasifikasi_dokumen_inisial FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '5' AND c.id_bagian = 'f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND b.is_listin = 'L'");
        $data_user_listrik = $sql_user_listrik->row_array();

        $sql_user_listrik_koor = $this->db->query("SELECT count(*) AS total FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '4' AND id_penanggung_jawab = 'y' AND c.id_bagian = 'f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND b.is_listin = 'L'");
        $data_user_listrik_koor = $sql_user_listrik_koor->row_array();


        $sql_instrumen = $this->db->query("SELECT bagian_id,id_bagian,progress_jumlah FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian LEFT JOIN dec.dec_pekerjaan_disposisi c ON c.id_user = a.id_user  WHERE a.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND is_listin='I'");
        $data_instrumen = $sql_instrumen->row_array();

        $sql_jml_instrumen = $this->db->query("SELECT COUNT(*) as total FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian LEFT JOIN dec.dec_pekerjaan_disposisi c ON c.id_user = a.id_user  WHERE a.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND is_listin='I'");
        $data_jml_instrumen = $sql_jml_instrumen->row_array(); 

        $sql_user_instrumen = $this->db->query("SELECT klasifikasi_dokumen_inisial FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '5' AND c.id_bagian = 'f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND b.is_listin = 'I'");
        $data_user_instrumen = $sql_user_instrumen->row_array();

        $sql_user_instrumen_koor = $this->db->query("SELECT count(*) AS total FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '4' AND id_penanggung_jawab = 'y' AND c.id_bagian = 'f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND b.is_listin = 'I'");
        $data_user_instrumen_koor = $sql_user_instrumen_koor->row_array();
        // print_r($data_instrumen);
        // data per progress

        $sql_progress = $this->db->query("select pekerjaan_id,bagian_id,bagian_nama,progress_jumlah,id_pekerjaan from global.global_bagian a left join global.global_bagian_detail b on b.id_bagian = a.bagian_id left join dec.dec_pekerjaan_progress c on c.id_user = b.id_pegawai right join dec.dec_pekerjaan d on d.pekerjaan_id = c.id_pekerjaan where pekerjaan_id ='" . $value['pekerjaan_id'] . "' order by progress_jumlah desc");

        $isi_progress = $sql_progress->result_array();
        // var_dump($isi_progress);/


        $sql_jumlah_progress = $this->db->query("select count(*) as total from global.global_bagian a left join global.global_bagian_detail b on b.id_bagian = a.bagian_id left join dec.dec_pekerjaan_progress c on c.id_user = b.id_pegawai right join dec.dec_pekerjaan d on d.pekerjaan_id = c.id_pekerjaan where pekerjaan_id ='" . $value['pekerjaan_id'] . "' ");
        $isi_jumlah_proses = $sql_jumlah_progress->row_array();


        $isi['milik'] = ($dataMilik['total'] > 0 || ($value['pic'] == $session['pegawai_nik'] && ($value['pekerjaan_status'] == '0' || $value['pekerjaan_status'] == '-'))) ? 'y' : 'n';


        if (!empty($data_sipil['id_bagian']) && $data_sipil['id_bagian'] == '1483c0882e75988626fee21c5926cc63727734a0') {
          $isi['pekerjaan_sipil'] =  $data_sipil['progress_jumlah'];
          $isi['pekerjaan_isi_sipil'] =  (isset($data_user_sipil['klasifikasi_dokumen_inisial'])) ? $data_user_sipil['klasifikasi_dokumen_inisial'].'('.$data_sipil['progress_jumlah'].'%)' : '';
        } else {
          $isi['pekerjaan_sipil'] = 0;
          $isi['pekerjaan_isi_sipil'] = (isset($data_user_sipil['klasifikasi_dokumen_inisial'])) ? $data_user_sipil['klasifikasi_dokumen_inisial'].'(0%)' : '';
        }
        $isi['pekerjaan_isi_sipil'] = ($data_user_sipil_koor['total'] == 0) ? $isi['pekerjaan_isi_sipil'] : '<b>'.$isi['pekerjaan_isi_sipil'].'</b>';

        if (!empty($data_proses['bagian_id']) && $data_proses['bagian_id'] == 'c21f86a03fdf9f7420764ac49d664415cfc942eb') {
          $isi['pekerjaan_proses'] =  $data_proses['progress_jumlah'];
          $isi['pekerjaan_isi_proses'] =  (isset($data_user_proses['klasifikasi_dokumen_inisial'])) ? $data_user_proses['klasifikasi_dokumen_inisial'].'('.$data_proses['progress_jumlah'].'%)' : '';
        } else {
          $isi['pekerjaan_proses'] = 0;
          $isi['pekerjaan_isi_proses'] = (isset($data_user_proses['klasifikasi_dokumen_inisial'])) ? $data_user_proses['klasifikasi_dokumen_inisial'].'(0%)' : '';
        }
        $isi['pekerjaan_isi_proses'] = ($data_user_proses_koor['total'] == 0) ? $isi['pekerjaan_isi_proses'] : '<b>'.$isi['pekerjaan_isi_proses'].'</b>';

        if ((!empty($data_mesin['id_bagian'])) && $data_mesin['id_bagian'] == 'fd2aa961b30ede7622a57d42267edc5d5eae3e1b') {
          $isi['pekerjaan_mesin'] =  $data_mesin['progress_jumlah'];
          $isi['pekerjaan_isi_mesin'] =  (isset($data_user_mesin['klasifikasi_dokumen_inisial'])) ? $data_user_mesin['klasifikasi_dokumen_inisial'].'('.$data_mesin['progress_jumlah'].'%)' : '';
        } else {
          $isi['pekerjaan_mesin'] = 0;
          $isi['pekerjaan_isi_mesin'] = (isset($data_user_mesin['klasifikasi_dokumen_inisial'])) ? $data_user_mesin['klasifikasi_dokumen_inisial'].'(0%)' : '';
        }
        $isi['pekerjaan_isi_mesin'] = ($data_user_mesin_koor['total'] == 0) ? $isi['pekerjaan_isi_mesin'] : '<b>'.$isi['pekerjaan_isi_mesin'].'</b>';

        if ((!empty($data_listrik['bagian_id'])) && $data_listrik['bagian_id'] == 'f683cbbca693d1a08fc010fd861b7350efa3e8d2') {
          $isi['pekerjaan_listrik'] =  $data_listrik['progress_jumlah'];
          $isi['pekerjaan_isi_listrik'] =  (isset($data_user_listrik['klasifikasi_dokumen_inisial'])) ? $data_user_listrik['klasifikasi_dokumen_inisial'].'('.$data_listrik['progress_jumlah'].'%)' : '';
        } else {
          $isi['pekerjaan_listrik'] = 0;
          $isi['pekerjaan_isi_listrik'] = (isset($data_user_listrik['klasifikasi_dokumen_inisial'])) ? $data_user_listrik['klasifikasi_dokumen_inisial'].'(0%)' : '';
        }
        $isi['pekerjaan_isi_listrik'] = ($data_user_listrik_koor['total'] == 0) ? $isi['pekerjaan_isi_listrik'] : '<b>'.$isi['pekerjaan_isi_listrik'].'</b>';

        if ((!empty($data_instrumen['bagian_id'])) && $data_instrumen['bagian_id'] == 'f683cbbca693d1a08fc010fd861b7350efa3e8d2') {
          $isi['pekerjaan_instrumen'] =  $data_instrumen['progress_jumlah'];
          $isi['pekerjaan_isi_instrumen'] =  (isset($data_user_instrumen['klasifikasi_dokumen_inisial'])) ? $data_user_instrumen['klasifikasi_dokumen_inisial'].'('.$data_instrumen['progress_jumlah'].'%)' : '';
        } else {
          $isi['pekerjaan_instrumen'] = 0;
          $isi['pekerjaan_isi_instrumen'] = (isset($data_user_instrumen['klasifikasi_dokumen_inisial'])) ? $data_user_instrumen['klasifikasi_dokumen_inisial'].'(0%)' : '';
        }
        $isi['pekerjaan_isi_instrumen'] = ($data_user_instrumen_koor['total'] == 0) ? $isi['pekerjaan_isi_instrumen'] : '<b>'.$isi['pekerjaan_isi_instrumen'].'</b>';



        // foreach ($isi_progress as $key => $value_progress) {

        if ($data_jml_sipil['total'] > 0 && $data_sipil['bagian_id'] == '1483c0882e75988626fee21c5926cc63727734a0') {
          $isi['pekerjaan_jumlah_sipil'] = ($data_jml_sipil['total'] > 0) ? $data_jml_sipil['total'] : '0';
        } else {
          $isi['pekerjaan_jumlah_sipil'] = '0';
        }

        if ($data_jml_proses['total'] > 0 && $data_proses['bagian_id'] == 'c21f86a03fdf9f7420764ac49d664415cfc942eb') {
          $isi['pekerjaan_jumlah_proses'] = ($data_jml_proses['total'] > 0) ? $data_jml_proses['total'] : '0';
        } else {
          $isi['pekerjaan_jumlah_proses'] = '0';
        }

        if ($data_jml_mesin['total'] > 0 && $data_mesin['bagian_id'] == 'fd2aa961b30ede7622a57d42267edc5d5eae3e1b') {
          $isi['pekerjaan_jumlah_mesin'] = ($data_jml_mesin['total'] > 0) ? $data_jml_mesin['total'] : '0';
        } else {
          $isi['pekerjaan_jumlah_mesin'] = '0';
        }

        if ($data_jml_listrik['total'] > 0 && $data_listrik['bagian_id'] == 'f683cbbca693d1a08fc010fd861b7350efa3e8d2') {
          $isi['pekerjaan_jumlah_listrik'] = ($data_jml_listrik['total'] > 0) ? $data_jml_listrik['total'] : '0';
        } else {
          $isi['pekerjaan_jumlah_listrik'] = '0';
        }

        if ($data_jml_instrumen['total'] > 0 && $data_instrumen['bagian_id'] == 'f683cbbca693d1a08fc010fd861b7350efa3e8d2') {
          $isi['pekerjaan_jumlah_instrumen'] = ($data_jml_instrumen['total'] > 0) ? $data_jml_instrumen['total'] : '0';
        } else {
          $isi['pekerjaan_jumlah_instrumen'] = '0';
        }
        // }

        if (($isi['pekerjaan_proses'] + $isi['pekerjaan_mesin'] + $isi['pekerjaan_listrik'] + $isi['pekerjaan_instrumen'] + $isi['pekerjaan_sipil'] > 0) && ($isi['pekerjaan_jumlah_proses'] + $isi['pekerjaan_jumlah_mesin'] + $isi['pekerjaan_jumlah_listrik'] + $isi['pekerjaan_jumlah_instrumen'] + $isi['pekerjaan_jumlah_sipil'] > 0)) {
          $isi_progressnya = ($isi['pekerjaan_proses'] + $isi['pekerjaan_mesin'] + $isi['pekerjaan_listrik'] + $isi['pekerjaan_instrumen'] + $isi['pekerjaan_sipil']) / (($isi_total['total']));
        } else {
          $isi_progressnya = 0;
        }

        $sql_tgl_start = $this->db->query("SELECT pekerjaan_disposisi_waktu FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND pekerjaan_disposisi_status ='4' AND is_aktif = 'y'");
        $data_tgl_start = $sql_tgl_start->row_array();

        $sql_avp_review = $this->db->query("SELECT count(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND pekerjaan_disposisi_status ='6' AND id_user = '".$session['pegawai_nik']."' AND is_aktif = 'y'");
        $data_avp_review = $sql_avp_review->row_array();

        $status_avp = ($value['pekerjaan_status'] == '5' && $data_avp_review['total'] >= '1') ? '1' : '0';

        $isi['pekerjaan_progress'] = round($isi_progressnya,2);
        $isi['pekerjaan_id'] = $value['pekerjaan_id'];
        $isi['pekerjaan_nomor'] = $value['pekerjaan_nomor'];
        $isi['pekerjaan_judul'] = $value['pekerjaan_judul'];
        $isi['pekerjaan_status'] = $value['pekerjaan_status'];
        $isi['pegawai_nama'] = $value['pegawai_nama'];
        // $isi['pekerjaan_progress'] = $value['pekerjaan_progress'];
        $isi['total'] = $isi_total['total'];
        $isi['tanggal_akhir'] =  date("Y-m-d", strtotime($value['tanggal_akhir']));
        $isi['tanggal_start'] = ($data_tgl_start['pekerjaan_disposisi_waktu'] != '') ? date("Y-m-d", strtotime($data_tgl_start['pekerjaan_disposisi_waktu'])) : '-';
        $isi['pekerjaan_status'] = $value['pekerjaan_status'];
        $isi['status_avp'] = $status_avp;

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
    $isi = $this->session->userdata();

    /* Pekerjaan */
    $pekerjaan_status = $this->input->get('pekerjaan_status') + 1;

    $pekerjaan_id = $this->input->get('pekerjaan_id');
    if ($pekerjaan_id) {
      $data['pekerjaan_status'] = htmlentities($pekerjaan_status);

      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
    }

    dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reviewed', $isi['pegawai_nik']);
    /* Pekerjaan */

    $where_id_user = ($isi['pegawai_nik']);
    $where_id_pekerjaan = (($pekerjaan_id));
    $where_disposisi_status = '1';
    $param_staf['is_proses'] = 'y';
    $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);

    // $data_user['pegawai_poscode'] = ($pekerjaan_status == '3') ? 'E53000000' : $isi['pegawai_direct_superior'];

    // $user = $this->M_user->getUser($data_user);
    /* User */

    /* Disposisi */
    // $data_disposisi['pekerjaan_disposisi_id'] = create_id();
    // $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
    // $data_disposisi['id_user'] = htmlentities($user['pegawai_nik']);
    // $data_disposisi['id_pekerjaan'] = htmlentities($pekerjaan_id);
    // $data_disposisi['pekerjaan_disposisi_status'] = htmlentities($pekerjaan_status);

    // $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

    // update status 

    // update status 
    /* Disposisi */
  }
  /* Approve */

  /* Approve */
  public function prosesApprove()
  {
    $isi = $this->session->userdata();

    /* Pekerjaan */
    $pekerjaan_status = $this->input->get('pekerjaan_status') + 1;

    $pekerjaan_id = $this->input->get('pekerjaan_id');
    if ($pekerjaan_id) {
      $data['pekerjaan_status'] = htmlentities($pekerjaan_status);

      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
    }

    dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Approve', $_GET['id_user']);
    /* Pekerjaan */

    $where_id_user = ($isi['pegawai_nik']);
    $where_id_pekerjaan = (($pekerjaan_id));
    $where_disposisi_status = '2';
    $param_staf['is_proses'] = 'y';
    $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);


    /* User */
    // if ($pekerjaan_status == '3') {
    //   $data_user['pegawai_poscode'] = 'E53000000';
    //   $user = $this->M_user->getUserBantuan($data_user);
    // } else {
    //   $data_user['pegawai_poscode'] = $isi['pegawai_direct_superior'];
    //   $user = $this->M_user->getUser($data_user);
    // }
    $data_user['pegawai_poscode'] = ($pekerjaan_status == '3') ? 'E53000000' : $isi['pegawai_direct_superior'];

    $user = $this->M_user->getUser($data_user);
    /* User */

    /* Disposisi */
    $data_disposisi['pekerjaan_disposisi_id'] = create_id();
    $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
    $data_disposisi['id_user'] = htmlentities($user['pegawai_nik']);
    $data_disposisi['id_pekerjaan'] = htmlentities($pekerjaan_id);
    $data_disposisi['pekerjaan_disposisi_status'] = htmlentities($pekerjaan_status);

    $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
    /* Disposisi */
  }
  /* Approve */



  /* Reject */
  public function prosesReject()
  {
    $user = $this->session->userdata();

    /* Pekerjaan */
    $pekerjaan_id = $this->input->get('pekerjaan_id');
    if ($pekerjaan_id) {
      $data['pekerjaan_status'] = htmlentities('-');
      $data['pekerjaan_note'] = htmlentities($this->input->get_post('note_reject'));

      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reject, Alasan ('.$data['pekerjaan_note'].')', $_GET['id_user']);
    }
    /* Pekerjaan */
    // deaktivasi
    $pekerjaan_id = $this->input->get('pekerjaan_id');
    if ($pekerjaan_id) {
      $data_disposisi['is_aktif'] = ('n');
      // $user_id = ($user['pegawai_nik']);
      $this->M_pekerjaan->updatePekerjaanDisposisiReject($data_disposisi, $pekerjaan_id, $user_id = null, $penanggung_jawab = null, $disposisi_status = null, $bagian = null);
    }
    // deaktivasi

    // unset proses
    $param_proses['is_proses'] = null;
    $where_id_pekerjaan = $pekerjaan_id;
    $this->M_pekerjaan->updateStatusProses($where_id_user = null, $where_id_pekerjaan, $where_disposisi_status = null, $param_proses);
    // echo $this->db->last_query();
    // unset proses



    /* Disposisi */
  }
  /* Reject */


  /*Reject AVP */
  public function prosesRejectAVP()
  {
    if (isset($_GET['id_user'])) {
      $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '".$_GET['id_user']."'");
      $user = $sql_user->row_array();
    } else {
      $user = $this->session->userdata();
    }
    // $user = $this->session->userdata();

    // cek pekerjaan penanggung jawab
    $pekerjaan_id = $this->input->get('pekerjaan_id');

    $sql_pekerjaan = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '4' AND id_user = '" . $user['pegawai_nik'] . "'");
    $data_pekerjaan = $sql_pekerjaan->row_array();

    // jika koordinator
    if ($data_pekerjaan['id_penanggung_jawab'] == 'y') {
      $pekerjaan_id = $data_pekerjaan['id_pekerjaan'];
      $disposisi_status = '4';
      $this->M_pekerjaan->deletePekerjaanDisposisiReject($pekerjaan_id, $disposisi_status, $user_id = null);
      // unset aktif vp cangun
      $where_id_pekerjaan = (($pekerjaan_id));
      $where_disposisi_status = ('3');
      $param_staf['is_proses'] = null;
      $this->M_pekerjaan->updateStatusProses($where_id_user = null, $where_id_pekerjaan, $where_disposisi_status, $param_staf);
      // dan kembali ke VP
      $param_disposisi['pekerjaan_status'] = '3';
      $param_disposisi['pekerjaan_note'] = $this->input->get_post('note_reject');

      $this->M_pekerjaan->updatePekerjaan($param_disposisi, $pekerjaan_id);
      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reject AVP Koordinator', $_GET['id_user']);
      // jika terkait
      // reject terkait sadja
    } else {
      $pekerjaan_id = $data_pekerjaan['id_pekerjaan'];
      $disposisi_status = '4';
      $user_id = $user['pegawai_nik'];
      $this->M_pekerjaan->deletePekerjaanDisposisiReject($pekerjaan_id, $disposisi_status, $user_id);
      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reject AVP Terkait', $_GET['id_user']);
    }
  }
  /*Reject AVP */

  /*Reject Staf */
  public function prosesRejectStaf()
  {
    if (isset($_GET['id_user'])) {
      $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '".$_GET['id_user']."'");
      $user = $sql_user->row_array();
    } else {
      $user = $this->session->userdata();
    }
    // $user = $this->session->userdata();
    $pekerjaan_id = $this->input->get('pekerjaan_id');
    // cek bagian 
    $sql_bagian = $this->db->query("SELECT * FROM global.global_bagian_detail WHERE id_pegawai = '" . $user['pegawai_nik'] . "'");
    $data_bagian = $sql_bagian->row_array();

    $sql_avp_bagian = $this->db->query("SELECT * FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $data_bagian['id_bagian'] . "' AND pekerjaan_disposisi_status = '4' AND id_pekerjaan = '" . $pekerjaan_id . "'");
    $data_avp_bagian = $sql_avp_bagian->row_array();

    if ($data_avp_bagian['id_penanggung_jawab'] == 'y') {
      $pekerjaan_id = $pekerjaan_id;
      $disposisi_status = '5';
      // $user_id = $user['pegawai_nik'];
      $this->M_pekerjaan->deletePekerjaanDisposisiReject($pekerjaan_id, $disposisi_status, $user_id = null);

      $pekerjaan_id = $pekerjaan_id;
      $disposisi_status = '4';
      // $user_id = $data_avp_bagian['id_user'];
      $this->M_pekerjaan->deletePekerjaanDisposisiReject($pekerjaan_id, $disposisi_status, $user_id = null);

      $where_id_pekerjaan = (($pekerjaan_id));
      $where_disposisi_status = ('3');
      $param_staf['is_proses'] = null;
      $this->M_pekerjaan->updateStatusProses($where_id_user = null, $where_id_pekerjaan, $where_disposisi_status, $param_staf);

      $param_disposisi['pekerjaan_status'] = '3';
      $param_disposisi['pekerjaan_note'] = $this->input->get_post('note_reject');

      $this->M_pekerjaan->updatePekerjaan($param_disposisi, $pekerjaan_id);

      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reject Staf', $_GET['id_user']);
    } else if ($data_avp_bagian['id_penanggung_jawab'] == 'n') {
      $pekerjaan_id = $pekerjaan_id;
      $disposisi_status = '5';
      $user_id = $user['pegawai_nik'];
      $this->M_pekerjaan->deletePekerjaanDisposisiReject($pekerjaan_id, $disposisi_status, $user_id);

      // // unset proses avp
      // $where_id_user = ($data_avp_bagian['id_pegawai']);
      // $where_id_pekerjaan = (($pekerjaan_id));
      // $where_disposisi_status = ('4');
      // $param_staf['is_proses'] = null;
      // $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);

      $pekerjaan_id = $pekerjaan_id;
      $disposisi_status = '4';
      $user_id = $data_avp_bagian['id_pegawai'];
      $this->M_pekerjaan->deletePekerjaanDisposisiReject($pekerjaan_id, $disposisi_status, $user_id);

      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reject Staf', $_GET['id_user']);
    }

    // print_r($data_avp_bagian);


    // cek avp dari staf tersebut;
    $data_user['pegawai_nik'] = $user['pegawai_nik'];
    $cek_avp = $this->M_user->getUser($data_user);

    // direct superior = poscode dari avp tersebut
    $data_avp['pegawai_poscode'] = $cek_avp['pegawai_direct_superior'];
    $data_avp = $this->M_user->getUser($data_avp);


    /* Disposisi */
    // ubah disposisi dari staf dan avp dari staf ke status n 
    if ($pekerjaan_id) {
      $data_disposisi['is_aktif'] = htmlentities('n');
      $user_id = htmlentities($user['pegawai_nik']);
      // $this->M_pekerjaan->updatePekerjaanDisposisi($data_disposisi, $pekerjaan_id, $user_id);

      $user_avp = $data_avp['pegawai_nik'];
      // $this->M_pekerjaan->updatePekerjaanDisposisi($data_disposisi, $pekerjaan_id, $user_avp);
    }
    /* Disposisi */

    /* Pekerjaan */
    $data['pekerjaan_id'] = $pekerjaan_id;
    $data['id_penanggung_jawab'] = 'n';
    $data['is_aktif'] = 'y';
    $cek_disposisi = $this->M_pekerjaan->getPekerjaanDisposisi($data);

    // jika pekerjaan dari vp direject oleh semua avp ,ubah status pekerjaan ke -1 dari sebelumnya
    if (($cek_disposisi['jumlah'] == '0')) {
      $data_pekerjaan['pekerjaan_status'] = '3';
      $data['pekerjaan_note'] = htmlentities($this->input->get_post('note_reject'));

      // $this->M_pekerjaan->updatePekerjaan($data_pekerjaan, $pekerjaan_id);
      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reject');
    }
    /* Pekerjaan */
  }
  /*Reject Staf */

  /* Disposisi VP */
  public function disposisiVP()
  {
    if (isset($_GET['id_user'])) {
      $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '".$_GET['id_user']."'");
      $isi = $sql_user->row_array();
    } else {
      $isi = $this->session->userdata();
    }

    /* Pekerjaan */
    $pekerjaan_status = '4';

    $pekerjaan_id = $this->input->post('id_pekerjaan_vp');
    if ($pekerjaan_id) {
      $data['pekerjaan_status'] = htmlentities($pekerjaan_status);

      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

      // dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Approve VP Cangun', $_GET['id_user']);
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
    $data_disposisi['id_user'] = htmlentities($this->input->post('id_tanggung_jawab_vp'));
    $data_disposisi['id_pekerjaan'] = htmlentities($pekerjaan_id);
    $data_disposisi['pekerjaan_disposisi_status'] = htmlentities($pekerjaan_status);
    $data_disposisi['id_penanggung_jawab'] = htmlentities('y');
    $data_disposisi['pekerjaan_disposisi_catatan'] = htmlentities($this->input->post('pekerjaan_disposisi_catatan_koordinator'));

    $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

    $param_cari_user['pegawai_nik'] = $data_disposisi['id_user'];
    $cari_user = $this->M_user->getUser($param_cari_user);
    dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Disposisikan ke '.$cari_user['pegawai_nama'].' Sebagai AVP Koordinator', $_GET['id_user']);


    if ($this->input->post('id_user_vp')) {
      $User = $this->input->post('id_user_vp');
      foreach ($User as $key => $id_user) {
        $data_disposisi['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
        $data_disposisi['id_user'] = htmlentities($id_user);
        $data_disposisi['id_pekerjaan'] = htmlentities($pekerjaan_id);
        $data_disposisi['pekerjaan_disposisi_status'] = htmlentities($pekerjaan_status);
        $data_disposisi['id_penanggung_jawab'] = htmlentities('n');
        $data_disposisi['pekerjaan_disposisi_catatan'] = htmlentities($this->input->post('pekerjaan_disposisi_catatan_terkait'));

        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

        $param_cari_user['pegawai_nik'] = $data_disposisi['id_user'];
        $cari_user = $this->M_user->getUser($param_cari_user);
        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Disposisikan ke '.$cari_user['pegawai_nama'].' Sebagai AVP Terkait', $_GET['id_user']);
      }
    }
    /* Disposisi */
  }
  /* Disposisi VP */

  /* Disposisi AVP */
  public function disposisiAVP()
  {
    // SESI USER
    if (isset($_GET['id_user'])) {
      $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '".$_GET['id_user']."'");
      $isi = $sql_user->row_array();
    } else {
      $isi = $this->session->userdata();
    }

    // INISIALIASI DATA

    $pekerjaan_status_vp_avp = '4';
    $pekerjaan_id = $this->input->post('id_pekerjaan_avp');
    $id_tanggung_jawab = 'n';
    $id_proses = 'y';

    // ISI AVP TERKAIT
    if ($this->input->post('id_user_vp_avp')) {


      // HAPUS DISPOSISI
      $this->M_pekerjaan->deletePekerjaanDisposisiDisposisi($pekerjaan_id, null, $id_tanggung_jawab, $pekerjaan_status_vp_avp);

      $User = $this->input->post('id_user_vp_avp');

      foreach ($User as $key => $id_user) {
        $sql_ada_dispo = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan='" . $pekerjaan_id . "' AND id_penanggung_jawab='" . $id_tanggung_jawab . "' AND pekerjaan_disposisi_status = '" . $pekerjaan_status_vp_avp . "' AND is_proses='" . $id_proses . "' AND id_user='" . $id_user . "'");
        $data_ada_dispo = $sql_ada_dispo->row_array();

        $data_disposisi['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
        $data_disposisi['id_user'] = htmlentities($id_user);
        $data_disposisi['id_pekerjaan'] = htmlentities($pekerjaan_id);
        $data_disposisi['pekerjaan_disposisi_status'] = htmlentities($pekerjaan_status_vp_avp);
        $data_disposisi['id_penanggung_jawab'] = htmlentities('n');

        if (isset($data_ada_dispo['is_proses']) != 'y') {
          $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
        }
      }
    }

    /* Pekerjaan */
    $pekerjaan_status = '5';
    // INSERT UNTUK KOORDINATOR
    $pekerjaan_id = $this->input->post('id_pekerjaan_avp');
    if ($pekerjaan_id) {
      if ($this->input->post('id_klasifikasi_pekerjaan_avp') && $this->input->get_post('pekerjaan_waktu_akhir_avp')) {
        $klasifikasi_baru = ($this->input->post('id_klasifikasi_pekerjaan_avp') == '1') ? 1 : 2;

        $sql_klasifikasi = $this->db->query("SELECT klasifikasi_pekerjaan_nama FROM global.global_klasifikasi_pekerjaan WHERE klasifikasi_pekerjaan_id = '" . $klasifikasi_baru . "'");
        $isi_klasifikasi = $sql_klasifikasi->row_array();

        $where = ($this->input->post('id_klasifikasi_pekerjaan_avp') == '1') ? " AND id_klasifikasi_pekerjaan = '1'" : " AND id_klasifikasi_pekerjaan != '1'";

        $sql_nomor = $this->db->query("SELECT pekerjaan_nomor FROM dec.dec_pekerjaan WHERE pekerjaan_nomor LIKE '%" . date('Y') . "%' AND pekerjaan_nomor IS NOT NULL ".$where." ORDER BY pekerjaan_nomor DESC");        $isi_nomor = $sql_nomor->row_array();
        $nomor = explode('-', $isi_nomor['pekerjaan_nomor']);

        $sql_pekerjaan = $this->db->query("SELECT pekerjaan_nomor FROM dec.dec_pekerjaan WHERE pekerjaan_id = '".$pekerjaan_id."'");
        $isi_pekerjaan = $sql_pekerjaan->row_array();

        $data['pekerjaan_nomor'] = ($isi_pekerjaan['pekerjaan_nomor'] == null) ? ($nomor[0] + 1) . '-' . $isi_klasifikasi['klasifikasi_pekerjaan_nama'] . '-' . date('Y') : $isi_pekerjaan['pekerjaan_nomor'];
        $data['pekerjaan_status'] = htmlentities($pekerjaan_status_vp_avp);
        $data['id_klasifikasi_pekerjaan'] = htmlentities($this->input->post('id_klasifikasi_pekerjaan_avp'));
        $data['pekerjaan_waktu_akhir'] = htmlentities($this->input->post('pekerjaan_waktu_akhir_avp'));
        $data['pekerjaan_judul'] = htmlentities($this->input->post('pekerjaan_judul'));

        $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

        // dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Disposisikan Oleh AVP Cangun');
      }
      // }
    } else {
      $data['pekerjaan_status'] = $pekerjaan_status_vp_avp;
      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);


      // dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Disposisikan Oleh AVP Cangun');
    }
    /* Pekerjaan */

    /* Disposisi */
    if ($this->input->post('id_user_avp')) {
      $data_disposisi['pekerjaan_disposisi_id'] = create_id();
      $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
      $data_disposisi['id_user'] = htmlentities($this->input->post('id_user_avp'));
      $data_disposisi['pekerjaan_disposisi_catatan'] = htmlentities($this->input->post('pekerjaan_disposisi_catatan'));
      $data_disposisi['id_pekerjaan'] = htmlentities($pekerjaan_id);
      $data_disposisi['pekerjaan_disposisi_status'] = htmlentities($pekerjaan_status);
      if ($isi['pegawai_nik'] == '2115260') {
        $data_disposisi['is_listin'] = 'l';
      }

      $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
    }
    if ($this->input->post('id_user_avp_instrumen')) {
      $data_disposisi['pekerjaan_disposisi_id'] = create_id();
      $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
      $data_disposisi['id_user'] = htmlentities($this->input->post('id_user_avp_instrumen'));
      $data_disposisi['pekerjaan_disposisi_catatan'] = htmlentities($this->input->post('pekerjaan_disposisi_catatan'));
      $data_disposisi['id_pekerjaan'] = htmlentities($pekerjaan_id);
      $data_disposisi['pekerjaan_disposisi_status'] = htmlentities($pekerjaan_status);
      $data_disposisi['is_listin'] = 'I';

      $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Disposisikan Oleh AVP Cangun', $_GET['id_user']);
    }
    if ($this->input->post('id_user_avp_listrik')) {
      $data_disposisi['pekerjaan_disposisi_id'] = create_id();
      $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
      $data_disposisi['id_user'] = htmlentities($this->input->post('id_user_avp_listrik'));
      $data_disposisi['pekerjaan_disposisi_catatan'] = htmlentities($this->input->post('pekerjaan_disposisi_catatan'));
      $data_disposisi['id_pekerjaan'] = htmlentities($pekerjaan_id);
      $data_disposisi['pekerjaan_disposisi_status'] = htmlentities($pekerjaan_status);
      $data_disposisi['is_listin'] = 'L';

      $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
    }
    /* Disposisi */

    // UPDATE STATUS PROSES NYA
    // $id_pekerjaan_disposisi = $pekerjaan_id;
    // $nik_disposisi = $isi['pegawai_nik'];
    // $pekerjaan_disposisi_status = $pekerjaan_status_vp_avp;
    // $data_pekerjaan_disposisi['is_proses'] = 'y';

    $user_avp_admin = (isset($_GET['user_id'])) ? $_GET['user_id'] : null;

    // $this->M_pekerjaan->updatePekerjaanDisposisi($data_pekerjaan_disposisi, $id_pekerjaan_disposisi, $nik_disposisi, $id_tanggung_jawab = null, $pekerjaan_disposisi_status);
    // print_r($this->db->last_query());
    $where_id_user = ($isi['pegawai_nik'] == '2190626') ? $user_avp_admin : $isi['pegawai_nik'];
    $where_id_pekerjaan = (($pekerjaan_id));
    $where_disposisi_status = ($pekerjaan_status_vp_avp);
    $param_staf['is_proses'] = 'y';
    $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);

    // CEK IS PROSES != 0 
    $sql_proses = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '" . $pekerjaan_status_vp_avp . "' AND (is_proses != 'y' OR is_proses is null)");
    $data_proses = $sql_proses->row_array();

    // JIKA SEMUA SUDAH DIPROSES
    if ($data_proses['total'] == '0') {
      $data['pekerjaan_status'] = $pekerjaan_status;
      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
    }

    $param_cari_user['pegawai_nik'] = $data_disposisi['id_user'];
    $cari_user = $this->M_user->getUser($param_cari_user);
    dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Disposisikan Ke '.$cari_user['pegawai_nama'].' Sebagai Perencana', $_GET['id_user']);
  }
  /* Disposisi AVP */

  /* Progress Pekerjaan */
  public function getProgressPekerjaan()
  {
    $user = $this->session->userdata();

    $param['id_pekerjaan'] =  $this->input->get_post('pekerjaan_id');
    $param['id_user'] = $user['pegawai_nik'];

    $data = $this->M_pekerjaan->getProgressPekerjaan($param);
    echo json_encode($data);
  }

  public function insertProgressPekerjaan()
  {
    if (isset($_GET['id_user'])) {
      $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '".$_GET['id_user']."'");
      $user = $sql_user->row_array();
    } else {
      $user = $this->session->userdata();
    }

    $param['progress_id'] = create_id();
    $param['id_pekerjaan'] = htmlentities($this->input->get_post('id_pekerjaan_progress'));
    $param['id_user'] = htmlentities($user['pegawai_nik']);
    $param['progress_jumlah'] = htmlentities($this->input->get_post('pekerjaan_progress'));
    // get id bagian
    $sql_bagian = $this->db->query("SELECT id_bagian FROM global.global_bagian_detail WHERE id_pegawai = '" . $user['pegawai_nik'] . "'");
    $data_bagian = $sql_bagian->row_array();
    $param['id_bagian'] = htmlentities($data_bagian['id_bagian']);

    $this->M_pekerjaan->insertProgress($param);
  }


  public function updateProgressPekerjaan()
  {
    if (isset($_GET['id_user'])) {
      $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '".$_GET['id_user']."'");
      $user = $sql_user->row_array();
    } else {
      $user = $this->session->userdata();
    }

    $id = htmlentities($this->input->get_post('progress_id'));
    $param['id_pekerjaan'] = htmlentities($this->input->get_post('id_pekerjaan_progress'));
    $param['id_user'] = htmlentities($user['pegawai_nik']);
    $param['progress_jumlah'] = htmlentities($this->input->get_post('pekerjaan_progress'));
    // get id bagian
    $sql_bagian = $this->db->query("SELECT id_bagian FROM global.global_bagian_detail WHERE id_pegawai = '" . $user['pegawai_nik'] . "'");
    $data_bagian = $sql_bagian->row_array();
    $param['id_bagian'] = htmlentities($data_bagian['id_bagian']);

    $this->M_pekerjaan->updateProgress($id, $param);
  }
  /* Progress Pekerjaan */

  /* Approve Pekerjaan Berjalan */
  public function prosesApproveBerjalan()
  {
    if (isset($_GET['id_user'])) {
      $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '".$_GET['id_user']."'");
      $isi = $sql_user->row_array();
    } else {
      $isi = $this->session->userdata();
    }
    $pekerjaan_id = htmlentities($this->input->get_post('pekerjaan_id'));
    $id_tanggung_jawab = null;
    $pekerjaan_status = htmlentities('8');

    $is_cc = 'y';
    /* isi disposisi */
    if ($this->input->get_post('id_user_staf')) {
      $this->M_pekerjaan->deletePekerjaanDisposisi($pekerjaan_id, null, $id_tanggung_jawab, $pekerjaan_status, $is_cc);
      $user = $this->input->get_post('id_user_staf');
      foreach ($user as $key => $value) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = htmlentities($value);
        $data_disposisi_doc['id_pekerjaan'] = htmlentities($this->input->get_post('pekerjaan_id'));
        $data_disposisi_doc['pekerjaan_disposisi_status'] = htmlentities('8');
        $data_disposisi_doc['id_penanggung_jawab'] = htmlentities('n');
        $data_disposisi_doc['is_cc'] = htmlentities('y');

        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
      }
    }

    if ($this->input->get_post('id_user_staf_hps')) {
      $this->M_pekerjaan->deletePekerjaanDisposisi($pekerjaan_id, null, $id_tanggung_jawab, $pekerjaan_status, $is_cc);
      $user_hps = $this->input->get_post('id_user_staf_hps');
      foreach ($user_hps as $key_hps => $value_hps) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = htmlentities($value_hps);
        $data_disposisi_doc['id_pekerjaan'] = htmlentities($this->input->get_post('pekerjaan_id'));
        $data_disposisi_doc['pekerjaan_disposisi_status'] = htmlentities('8');
        $data_disposisi_doc['id_penanggung_jawab'] = htmlentities('n');
        $data_disposisi_doc['is_cc'] = htmlentities('h');

        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
      }
    }
    // CC
    /* isi disposisi */

    /* Ubah Status Dari Staf */

    $where_id_user = htmlentities($isi['pegawai_nik']);
    $where_id_pekerjaan = htmlentities($this->input->get_post('pekerjaan_id'));
    $where_disposisi_status = htmlentities($this->input->get_post('pekerjaan_status'));
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
    // echo $this->db->last_query();
    // print_r($data_jumlah_proses);

    if ($pekerjaan_id) {
      if ($data_jumlah_proses['total'] == '0') {

        $data['pekerjaan_status'] = htmlentities($pekerjaan_status);

        // $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
        // echo $this->db->last_query();

        // dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reviewed Oleh Cangun');
      }
    }
    /* Pekerjaan */

    /* select pekerjaan disposisi dengan pekerjaan dan status sama */
    $sql_disposisi_sama = $this->db->query("SELECT id_user,id_pekerjaan FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $where_disposisi['id_pekerjaan'] . "' AND pekerjaan_disposisi_status = '" . $where_disposisi['pekerjaan_disposisi_status'] . "'");

    $data_disposisi_sama = $sql_disposisi_sama->result_array();
    /* select pekerjaan disposisi dengan pekerjaan dan status sama */

    // cek jika status koordinator
    $sql_koordinator = $this->db->query("SELECT id_user FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = " . $this->db->escape($pekerjaan_id) . " AND id_penanggung_jawab = " . $this->db->escape('y') . "AND pekerjaan_disposisi_status = " . $this->db->escape('4'));

    $data_koordinator = $sql_koordinator->row_array();
    // cek jika status koordinator

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
      $data_disposisi['id_pekerjaan'] = htmlentities($pekerjaan_id);
      $data_disposisi['pekerjaan_disposisi_status'] = htmlentities($pekerjaan_status);
      $data_disposisi['id_penanggung_jawab'] = ($data_koordinator['id_user'] == $user['id_pegawai']) ? 'y' : 'n';
      $data_disposisi['is_proses'] = null;

      $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
    }

    // update status avp (6) agar null
    $where_id_user = htmlentities($id_user_disposisi);
    $where_id_pekerjaan = htmlentities($pekerjaan_id);
    $where_disposisi_status = htmlentities($pekerjaan_status);
    $param_staf['is_proses'] = null;
    $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);
    // echo $this->db->last_query();
    // update status avp (6) agar null

    /* Disposisi */
    // }

    // ubah status dokumen ke SEND 
    $where_id_pekerjaan_dokumen = htmlentities($this->input->get_post('pekerjaan_id'));
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

      // echo $this->db->last_query();
    } else {

      $where_id_pekerjaan_progress = $this->input->get_post('pekerjaan_id');
      $where_id_user_progress = $isi['pegawai_nik'];
      $param_user_progress['progress_jumlah'] = '92';

      $this->M_pekerjaan->updateProgressIFA($where_id_pekerjaan_progress, $where_id_user_progress, $param_user_progress);

      // echo $this->db->last_query();
    }

    dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Diinput Oleh Staf', $_GET['id_user']);

    /* Dokumen */
      $sql_bagian = $this->db->query("SELECT id_bagian FROM global.global_bagian_detail WHERE id_pegawai = '".$isi['pegawai_nik']."'");
      $data_bagian = $sql_bagian->row_array();

      $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_status = '2', is_proses = 'a', pekerjaan_dokumen_keterangan = NULL WHERE pekerjaan_dokumen_status < '2' AND id_pekerjaan = '".$pekerjaan_id."' AND id_create IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian = '".$data_bagian['id_bagian']."')");
    /* Dokumen */
  }

  /* Approve Pekerjaan Berjalan */
  public function prosesApproveBerjalanIFARev()
  {
    $isi = $this->session->userdata();
    $pekerjaan_id = htmlentities($this->input->get_post('pekerjaan_id'));
    $id_tanggung_jawab = null;
    $pekerjaan_status = htmlentities('8');
    $is_cc = 'y';
    /* isi disposisi */
    if ($this->input->get_post('id_user_staf')) {
      $this->M_pekerjaan->deletePekerjaanDisposisi($pekerjaan_id, null, $id_tanggung_jawab, $pekerjaan_status, $is_cc);
      $user = $this->input->get_post('id_user_staf');
      foreach ($user as $key => $value) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = htmlentities($value);
        $data_disposisi_doc['id_pekerjaan'] = htmlentities($this->input->get_post('pekerjaan_id'));
        $data_disposisi_doc['pekerjaan_disposisi_status'] = htmlentities('8');
        $data_disposisi_doc['id_penanggung_jawab'] = htmlentities('n');
        $data_disposisi_doc['is_cc'] = htmlentities('y');

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

        // INSERT KE DB EMAIL
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
    // CC
    /* isi disposisi */

    /* Ubah Status Dari Staf */

    $where_id_user = htmlentities($isi['pegawai_nik']);
    $where_id_pekerjaan = htmlentities($this->input->get_post('pekerjaan_id'));
    $where_disposisi_status = htmlentities($this->input->get_post('pekerjaan_status') - 3);
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
    // echo $this->db->last_query();
    // print_r($data_jumlah_proses);

    if ($pekerjaan_id) {
      if ($data_jumlah_proses['total'] == '0') {

        $data['pekerjaan_status'] = htmlentities($pekerjaan_status);

        // $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
        // echo $this->db->last_query();

        // dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reviewed Oleh Cangun');
      }
    }
    /* Pekerjaan */

    /* select pekerjaan disposisi dengan pekerjaan dan status sama */
    $sql_disposisi_sama = $this->db->query("SELECT id_user,id_pekerjaan FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $where_disposisi['id_pekerjaan'] . "' AND pekerjaan_disposisi_status = '" . $where_disposisi['pekerjaan_disposisi_status'] . "'");

    $data_disposisi_sama = $sql_disposisi_sama->result_array();
    /* select pekerjaan disposisi dengan pekerjaan dan status sama */

    // cek jika status koordinator
    $sql_koordinator = $this->db->query("SELECT id_user FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = " . $this->db->escape($pekerjaan_id) . " AND id_penanggung_jawab = " . $this->db->escape('y') . "AND pekerjaan_disposisi_status = " . $this->db->escape('4'));

    $data_koordinator = $sql_koordinator->row_array();
    // cek jika status koordinator

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
      $data_disposisi['id_pekerjaan'] = htmlentities($pekerjaan_id);
      $data_disposisi['pekerjaan_disposisi_status'] = htmlentities($pekerjaan_status);
      $data_disposisi['id_penanggung_jawab'] = ($data_koordinator['id_user'] == $user['id_pegawai']) ? 'y' : 'n';
      $data_disposisi['is_proses'] = null;

      $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

      // update agar set null proses avpnya

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
      // INSERT KE DB EMAIL
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

    // update status avp (6) agar null
    $where_id_user = htmlentities($id_user_disposisi);
    $where_id_pekerjaan = htmlentities($pekerjaan_id);
    $where_disposisi_status = htmlentities($pekerjaan_status);
    $param_staf['is_proses'] = null;
    $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);
    // echo $this->db->last_query();
    // update status avp (6) agar null

    /* Disposisi */
    // }

    // ubah status dokumen ke SEND 
    $where_id_pekerjaan_dokumen = htmlentities($this->input->get_post('pekerjaan_id'));
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

      // echo $this->db->last_query();
    } else {

      $where_id_pekerjaan_progress = $this->input->get_post('pekerjaan_id');
      $where_id_user_progress = $isi['pegawai_nik'];
      $param_user_progress['progress_jumlah'] = '92';

      $this->M_pekerjaan->updateProgressIFA($where_id_pekerjaan_progress, $where_id_user_progress, $param_user_progress);

      // echo $this->db->last_query();
    }




    dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Diinput Oleh Staf');
  }

  public function prosesApproveBerjalanHPS()
  {
    $isi = $this->session->userdata();
    $pekerjaan_id = htmlentities($this->input->get_post('id_pekerjaan'));
    $id_tanggung_jawab = null;
    $pekerjaan_status = htmlentities('8');
    $is_cc = 'y';
    // CC




    /* isi disposisi */
    // CC
    if ($this->input->get_post('id_user_staf')) {
      $this->M_pekerjaan->deletePekerjaanDisposisi($pekerjaan_id, null, $id_tanggung_jawab, $pekerjaan_status, $is_cc);
      $user = $this->input->get_post('id_user_staf');
      foreach ($user as $key => $value) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = htmlentities($value);
        $data_disposisi_doc['id_pekerjaan'] = htmlentities($this->input->get_post('pekerjaan_id'));
        $data_disposisi_doc['pekerjaan_disposisi_status'] = htmlentities('8');
        $data_disposisi_doc['id_penanggung_jawab'] = htmlentities('n');
        $data_disposisi_doc['is_cc'] = htmlentities('y');

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

        // INSERT KE DB EMAIL
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
    // CC
    /* isi disposisi */

    /* Ubah Status Dari Staf */

    $where_id_user = htmlentities($isi['pegawai_nik']);
    $where_id_pekerjaan = htmlentities($this->input->get_post('pekerjaan_id'));
    $where_disposisi_status = htmlentities($this->input->get_post('pekerjaan_status'));
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

        // $data['pekerjaan_status'] = htmlentities($pekerjaan_status);

        // $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

        // dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reviewed Oleh Cangun');
      }
    }
    /* Pekerjaan */

    /* select pekerjaan disposisi dengan pekerjaan dan status sama */
    $sql_disposisi_sama = $this->db->query("SELECT id_user,id_pekerjaan FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $where_disposisi['id_pekerjaan'] . "' AND pekerjaan_disposisi_status = '" . $where_disposisi['pekerjaan_disposisi_status'] . "'");

    $data_disposisi_sama = $sql_disposisi_sama->result_array();
    /* select pekerjaan disposisi dengan pekerjaan dan status sama */

    // cek jika status koordinator
    $sql_koordinator = $this->db->query("SELECT id_user FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = " . $this->db->escape($pekerjaan_id) . " AND id_penanggung_jawab = " . $this->db->escape('y') . "AND pekerjaan_disposisi_status = " . $this->db->escape('4'));

    $data_koordinator = $sql_koordinator->row_array();
    // cek jika status koordinator

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
      // $data_disposisi['id_user'] = ($pekerjaan_status == '8') ? $isi_pekerjaan['pic'] : $user['pegawai_nik'];
      $data_disposisi['id_user'] = ($pekerjaan_status == '8') ? $isi_pekerjaan['pic'] : $user['id_pegawai'];
      $data_disposisi['id_pekerjaan'] = htmlentities($pekerjaan_id);
      $data_disposisi['pekerjaan_disposisi_status'] = htmlentities($pekerjaan_status);
      $data_disposisi['id_penanggung_jawab'] = ($data_koordinator['id_user'] == $user['id_pegawai']) ? 'y' : 'n';
      // echo json_encode($data_disposisi);

      $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
      // print_r($this->db->last_query());

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
      // INSERT KE DB EMAIL
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
    // }

    // ubah status dokumen ke SEND 
    $where_id_pekerjaan_dokumen = htmlentities($this->input->get_post('pekerjaan_id'));
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

      // echo $this->db->last_query();
    } else {

      $where_id_pekerjaan_progress = $this->input->get_post('pekerjaan_id');
      $where_id_user_progress = $isi['pegawai_nik'];
      $param_user_progress['progress_jumlah'] = '92';

      $this->M_pekerjaan->updateProgressIFA($where_id_pekerjaan_progress, $where_id_user_progress, $param_user_progress);

      // echo $this->db->last_query();
    }




    dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Diinput Oleh Staf', $_GET['id_user']);
  }

  public function prosesApproveBerjalanIFC()
  {
    if (isset($_GET['id_user'])) {
      $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '".$_GET['id_user']."'");
      $isi = $sql_user->row_array();
    } else {
      $isi = $this->session->userdata();
    }

    $pekerjaan_id = htmlentities($this->input->get_post('pekerjaan_id'));
    $id_tanggung_jawab = null;
    $pekerjaan_status = htmlentities('8');
    $is_cc = 'y';

    /* isi disposisi */
    if ($this->input->get_post('id_user_staf')) {
      $this->M_pekerjaan->deletePekerjaanDisposisi($pekerjaan_id, null, $id_tanggung_jawab, $pekerjaan_status, $is_cc);
      $user = $this->input->get_post('id_user_staf');
      foreach ($user as $key => $value) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = htmlentities($value);
        $data_disposisi_doc['id_pekerjaan'] = htmlentities($this->input->get_post('pekerjaan_id'));
        $data_disposisi_doc['pekerjaan_disposisi_status'] = htmlentities('8');
        $data_disposisi_doc['id_penanggung_jawab'] = htmlentities('n');
        $data_disposisi_doc['is_cc'] = htmlentities('y');

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

        // INSERT KE DB EMAIL
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
    // CC

    /* Ubah Status Dari Staf */

    $where_id_user = htmlentities($isi['pegawai_nik']);
    $where_id_pekerjaan = htmlentities($this->input->get_post('pekerjaan_id'));
    $where_disposisi_status = htmlentities($this->input->get_post('pekerjaan_status'));
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

        $data['pekerjaan_status'] = htmlentities($pekerjaan_status);

        $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

        // dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reviewed Oleh Cangun');
      }
    }
    /* Pekerjaan */

    /* select pekerjaan disposisi dengan pekerjaan dan status sama */
    $sql_disposisi_sama = $this->db->query("SELECT id_user,id_pekerjaan FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $where_disposisi['id_pekerjaan'] . "' AND pekerjaan_disposisi_status = '" . $where_disposisi['pekerjaan_disposisi_status'] . "'");

    $data_disposisi_sama = $sql_disposisi_sama->result_array();
    /* select pekerjaan disposisi dengan pekerjaan dan status sama */

    // cek jika status koordinator
    $sql_koordinator = $this->db->query("SELECT id_user FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = " . $this->db->escape($pekerjaan_id) . " AND id_penanggung_jawab = " . $this->db->escape('y') . "AND pekerjaan_disposisi_status = " . $this->db->escape('4'));

    $data_koordinator = $sql_koordinator->row_array();
    // cek jika status koordinator

    $data_user['pegawai_poscode'] = $isi['pegawai_direct_superior'];

    $sql_user_bagian = $this->db->query("SELECT * FROM global.global_bagian_detail WHERE id_pegawai = '" . $isi['pegawai_nik'] . "'");
    $data_user_bagian = $sql_user_bagian->row_array();

    $pekerjaan_disposisi_status = '4';

    $sql_avp_bagian = $this->db->query("SELECT * FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai WHERE id_bagian = '" . $data_user_bagian['id_bagian'] . "' AND pekerjaan_disposisi_status = '" . $pekerjaan_disposisi_status . "' AND id_pekerjaan = '" . $pekerjaan_id . "'");

    $data_avp_bagian = $sql_avp_bagian->row_array();

    $user = $data_avp_bagian;

    $sql_cek_vp  = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user='" . $data_avp_bagian['id_pegawai'] . "' AND pekerjaan_disposisi_status = '" . ($pekerjaan_disposisi_status + 6) . "' AND id_pekerjaan = '" . $pekerjaan_id . "'");

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
      // $data_disposisi['id_user'] = ($pekerjaan_status == '8') ? $isi_pekerjaan['pic'] : $user['id_pegawai'];
      $data_disposisi['id_user'] = ($id_user_disposisi);
      $data_disposisi['id_pekerjaan'] = htmlentities($pekerjaan_id);
      $data_disposisi['pekerjaan_disposisi_status'] = htmlentities($pekerjaan_status);
      $data_disposisi['id_penanggung_jawab'] = ($data_koordinator['id_user'] == $user['id_pegawai']) ? 'y' : 'n';

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
      // INSERT KE DB EMAIL
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
    // }

    // ubah status dokumen ke SEND 
    $where_id_pekerjaan_dokumen = htmlentities($this->input->get_post('pekerjaan_id'));
    $where_status_dokumen = '6';
    $where_id_user_dokumen = $isi['pegawai_nik'];
    $param_user_dokumen['pekerjaan_dokumen_status'] = '7';

    $this->M_pekerjaan->updateStatusDokumenIFC($where_id_pekerjaan_dokumen, $where_id_user_dokumen, $where_status_dokumen, $param_user_dokumen);

    $sql_progress = $this->db->query("SELECT * FROM dec.dec_pekerjaan_progress WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_user = '" . $isi['pegawai_nik'] . "'");
    $data_progress = $sql_progress->row_array();

    if (empty($data_progress)) {
      $param_user_progress['progress_id'] = create_id();
      $param_user_progress['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
      $param_user_progress['id_user'] = $isi['pegawai_nik'];
      $param_user_progress['progress_jumlah'] = '92';
      $param_user_progress['id_bagian'] = $data_user_bagian['id_bagian'];

      $this->M_pekerjaan->insertProgressIFA($param_user_progress);

      // echo $this->db->last_query();
    } else {

      $where_id_pekerjaan_progress = $this->input->get_post('pekerjaan_id');
      $where_id_user_progress = $isi['pegawai_nik'];
      $param_user_progress['progress_jumlah'] = '92';

      $this->M_pekerjaan->updateProgressIFA($where_id_pekerjaan_progress, $where_id_user_progress, $param_user_progress);

      // echo $this->db->last_query();
    }

    dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Diinput Oleh Staf', $_GET['id_user']);
  }
  /* Approve Pekerjaan Berjalan */




  /* Approve Pekerjaan Berjalan Revisi  */
  public function prosesApproveBerjalanRevisi()
  {
    $isi = $this->session->userdata();

    /* isi disposisi */
    if ($this->input->get_post('id_user_staf')) {
      $user = $this->input->get_post('id_user_staf');
      foreach ($user as $key => $value) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = htmlentities($value);
        $data_disposisi_doc['id_pekerjaan'] = htmlentities($this->input->get_post('pekerjaan_id'));
        $data_disposisi_doc['pekerjaan_disposisi_status'] = htmlentities('8');
        $data_disposisi_doc['id_penanggung_jawab'] = htmlentities('n');

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

        // INSERT KE DB EMAIL
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
    // $pekerjaan_status = '9';

    $pekerjaan_id = $this->input->get('pekerjaan_id');
    if ($pekerjaan_id) {
      $data['pekerjaan_status'] = '9';

      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Approve Oleh Cangun');
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
    $data_disposisi['id_pekerjaan'] = htmlentities($pekerjaan_id);
    $data_disposisi['pekerjaan_disposisi_status'] = htmlentities('9');

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
    // INSERT KE DB EMAIL
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
      $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '".$_GET['id_user']."'");
      $isi = $sql_user->row_array();
    } else {
      $isi = $this->session->userdata();
    }

    /* Pekerjaan */
    $pekerjaan_id = $this->input->get('pekerjaan_id');
    if ($pekerjaan_id) {
      $data['pekerjaan_status'] = htmlentities('5');

      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reject Oleh Cangun', $_GET['id_user']);
    }
    /* Pekerjaan */
  }


  public function prosesRejectBerjalanIFA()
  {
    if (isset($_GET['id_user'])) {
      $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '".$_GET['id_user']."'");
      $isi = $sql_user->row_array();
    } else {
      $isi = $this->session->userdata();
    }

    /* Pekerjaan */
    $pekerjaan_id = $this->input->get('pekerjaan_id');
    if ($pekerjaan_id) {
      $data['pekerjaan_status'] = htmlentities('5');

      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Revisi Oleh Cangun', $_GET['id_user']);
    }

    $where_id_user = htmlentities($isi['pegawai_nik']);
    $where_id_pekerjaan = htmlentities($this->input->get_post('pekerjaan_id'));
    $where_disposisi_status = '6';
    $param_staf['is_proses'] = NULL;

    $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);
    /* Pekerjaan */

    /* Staf */
      $sql_staf = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '5' AND is_aktif = 'y' AND id_pekerjaan = '".$this->input->get_post('pekerjaan_id')."' AND id_user IN (SELECT id_pegawai FROM global.global_bagian_detail WHERE id_bagian IN (SELECT bagian_id FROM global.global_bagian a LEFT JOIN global.global_bagian_detail b ON a.bagian_id = b.id_bagian WHERE b.id_pegawai = '".$isi['pegawai_nik']."'))");
      $dataStaf = $sql_staf->row_array();

      $param_cangung['is_proses'] = NULL;


      $this->db->query("UPDATE dec.dec_pekerjaan_disposisi SET is_proses = null WHERE is_aktif = 'y' AND id_pekerjaan = '".$this->input->get_post('pekerjaan_id')."' AND id_user = '".$dataStaf['id_user']."'");
    /* Staf */
  }
  /* Reject Pekerjaan Berjalan */

  /* Approve Pekerjaan IFA */
  public function prosesApproveIFA()
  {
    if (isset($_GET['id_user'])) {
      $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '".$_GET['id_user']."'");
      $isi = $sql_user->row_array();
    } else {
      $isi = $this->session->userdata();
    }

    $where_id_user = ($isi['pegawai_nik']);
    $where_id_pekerjaan = $this->input->get_post('pekerjaan_id');
    $where_disposisi_status = '8';
    $param_staf['is_proses'] = 'y';
    $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);

    /* Pekerjaan */

    $pekerjaan_status = 9;
    $pekerjaan_id = $this->input->get('pekerjaan_id');

    $sql_proses = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $pekerjaan_id . "' AND pekerjaan_disposisi_status = '8' AND (is_proses != 'y' OR is_proses is null)");

    $data_proses = $sql_proses->row_array();

    // if ($pekerjaan_id && $data_proses['total'] == '0') {
      $data['pekerjaan_status'] = htmlentities($pekerjaan_status);

      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reviewed Oleh PIC', $_GET['id_user']);
    // }
    /* Pekerjaan */

    /* User */
    $data_user['pegawai_poscode'] = $isi['pegawai_direct_superior'];

    $user = $this->M_user->getUser($data_user);
    /* User */

    /* Staf Cangun */
    $sql_disposisi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '5' AND id_pekerjaan = '" . $pekerjaan_id . "'");
    $isi_disposisi = $sql_disposisi->result_array();
    /* Staf Cangun */

    /* Disposisi */
    // if ($data_proses['total'] == '0') {
      foreach ($isi_disposisi as $key => $value) {
        $data_disposisi['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
        $data_disposisi['id_user'] = htmlentities($value['id_user']);
        $data_disposisi['id_pekerjaan'] = htmlentities($pekerjaan_id);
        $data_disposisi['pekerjaan_disposisi_status'] = htmlentities($pekerjaan_status);

        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
      }
    // }



    /* Disposisi */
  }
  /* Approve Pekerjaan IFA */

  /* Reject Pekerjaan IFA */
  public function prosesRejectIFA()
  {
    if (isset($_GET['id_user'])) {
      $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '".$_GET['id_user']."'");
      $isi = $sql_user->row_array();
    } else {
      $isi = $this->session->userdata();
    }

    $sql_cek_pekerjaan = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND pekerjaan_disposisi_status= '5'");
    $data_cek_pekerjaan = $sql_cek_pekerjaan->result_array();

    foreach ($data_cek_pekerjaan as $key => $value) {
      $where_id_user = ($value['id_user']);
      $where_id_pekerjaan = $this->input->get_post('pekerjaan_id');
      $where_disposisi_status = '5';
      $param_staf['is_proses'] = null;
      $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);
    }

    $sql_cek_avp = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan='" . $this->input->get_post('pekerjaan_id') . "' AND pekerjaan_disposisi_status='6'");
    $data_cek_avp = $sql_cek_avp->result_array();

    foreach ($data_cek_avp as $key => $value) {
      $where_id_user = ($value['id_user']);
      $where_id_pekerjaan = $this->input->get_post('pekerjaan_id');
      $where_disposisi_status = '6';
      $param_staf['is_proses'] = null;
      $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);
    }

    /* Pekerjaan */
    $pekerjaan_id = $this->input->get('pekerjaan_id');
    if ($pekerjaan_id) {
      // $data['pekerjaan_status'] = htmlentities('5');

      // $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

      $where_id_user = ($isi['pegawai_nik']);
      $where_id_pekerjaan = $this->input->get_post('pekerjaan_id');
      $where_disposisi_status = '8';
      $param_staf['is_proses'] = 'r';
      $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);

      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reject Oleh PIC', $_GET['id_user']);
    }
    /* Pekerjaan */
  }
  /* Reject Pekerjaan IFA */

  /* Aksi Approve / Reject Dokumen */
  public function simpanAksi()
  {
    if (isset($_GET['id_user'])) {
      $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '".$_GET['id_user']."'");
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
        $ImageExt       = str_replace('.', '', $ImageExt); // Extension
        $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
        $NewImageName   = str_replace(' ', '', $this->input->get_post('pekerjaan_dokumen_id') . '_' . date('ymdhis') . '_' . $acak . '.' . $ImageExt);

        if (in_array($ImageExt, $Extension)) {
          move_uploaded_file($_FILES["pekerjaan_dokumen_file"]["tmp_name"], $temp . $NewImageName); // Menyimpan file
        }

        // move_uploaded_file($_FILES["pekerjaan_dokumen_file"]["tmp_name"], $temp . $NewImageName); // Menyimpan file

        $note = "Data Berhasil Disimpan";
      } else {
        $note = "Data Gagal Disimpan";
      }
    } else {
      $NewImageName = null;
    }

    // cek dokumen statusnya dari input status
    $dataku['pekerjaan_dokumen_status'] = $this->input->post('pekerjaan_dokumen_status');

    $sql_status = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->get_post('pekerjaan_dokumen_id') . "'");
    $data_status = $sql_status->row_array();

    $status_dokumen = ($this->input->get_post('pekerjaan_dokumen_status') == 'n') ? '0' : $data_status['pekerjaan_dokumen_status'] + 1;

    // if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
    //   $status_dokumen_revisi = $data_status['pekerjaan_dokumen_revisi'] + 1;
    // } else {
      $status_dokumen_revisi = null;
    // }



    // ketika dokumen reject
    if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
      $where_id_user = $data_status['id_create_awal'];
      $where_id_pekerjaan = $data_status['id_pekerjaan'];
      $where_disposisi_status = $this->input->get_post('pekerjaan_status');
      $where_disposisi_status = '5';
      $param_staf['is_proses'] = null;
      $this->M_pekerjaan->updateStatusProses($where_id_user, $where_id_pekerjaan, $where_disposisi_status, $param_staf);


      // $param_disposisi['pekerjaan_status'] = '5';
      $param_disposisi['pekerjaan_note'] = $this->input->get_post('note_reject');
      // 
      $this->M_pekerjaan->updatePekerjaan($param_disposisi, $data_status['id_pekerjaan']);
    }



    if (($data_status['is_proses'] == null || $data_status['is_proses'] == '') && $this->input->get_post('pekerjaan_dokumen_status') == 'y') {
      $proses = 'y';
    } else if ($data_status['is_proses'] == 'y') {
      $proses = 'a';
    } else {
      $proses = 'y';
    }

    /* Insert */
    if ($NewImageName == null) {
      $data['pekerjaan_dokumen_id_temp'] = htmlentities($this->input->post('pekerjaan_dokumen_id'));
      $data['pekerjaan_dokumen_id'] = htmlentities(create_id());
      $data['pekerjaan_dokumen_status'] = htmlentities($status_dokumen);
      $data['pekerjaan_dokumen_revisi'] = htmlentities($status_dokumen_revisi);
      $data['pekerjaan_dokumen_keterangan'] = htmlentities($this->input->post('pekerjaan_dokumen_keterangan'));
      $data['id_create'] = $isi['pegawai_nik'];
      $data['is_proses'] = $proses;
      $data['id_create_awal'] = $data_status['id_create_awal'];
      $data['pekerjaan_dokumen_jumlah'] = $data_status['pekerjaan_dokumen_jumlah'];
      $this->M_pekerjaan->simpanAksiSama($data);
    } else {
      $data['pekerjaan_dokumen_id_temp'] = htmlentities($this->input->post('pekerjaan_dokumen_id'));
      $data['pekerjaan_dokumen_id'] = htmlentities(create_id());
      $data['pekerjaan_dokumen_file'] = $NewImageName;
      $data['pekerjaan_dokumen_status'] = htmlentities($status_dokumen);
      $data['pekerjaan_dokumen_revisi'] = htmlentities($status_dokumen_revisi);
      $data['pekerjaan_dokumen_keterangan'] = htmlentities($this->input->post('pekerjaan_dokumen_keterangan'));
      $data['id_create'] = $isi['pegawai_nik'];
      $data['is_proses'] = $proses;
      $data['id_create_awal'] = $data_status['id_create_awal'];
      $data['pekerjaan_dokumen_jumlah'] = $data_status['pekerjaan_dokumen_jumlah'];
      $this->M_pekerjaan->simpanAksi($data);
    }
    /* Insert */

    if ($data['pekerjaan_dokumen_id_temp']) {
      $data_edit['is_lama'] = 'y';
      $this->M_pekerjaan->editAksi($data_edit, $data['pekerjaan_dokumen_id_temp']);
    }
  }
  /* Aksi Approve / Reject Dokumen */

  /* Aksi Approve / Reject Dokumen CC*/
  public function simpanAksiCC()
  {
    if (isset($_GET['id_user'])) {
      $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '".$_GET['id_user']."'");
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
        $ImageExt       = str_replace('.', '', $ImageExt); // Extension
        $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
        $NewImageName   = str_replace(' ', '', $this->input->get_post('pekerjaan_dokumen_id') . '_' . date('ymdhis') . '_' . $acak . '.' . $ImageExt);

        if (in_array($ImageExt, $Extension)) {
          move_uploaded_file($_FILES["pekerjaan_dokumen_file"]["tmp_name"], $temp . $NewImageName); // Menyimpan file
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
    // print_r($status_dokumen_cc);




    if ($data_status['is_review'] == null || $data_status['is_review'] == '') {
      $review = 'y';
    }

    // cek dokumen statusnya dari input status
    $dokumen_status = ($this->input->get_post('pekerjaan_dokumen_status') == 'n') ? '0' : '4';

    /* Insert */
    if ($NewImageName == null) {
      $data['pekerjaan_dokumen_id_temp'] = htmlentities($this->input->post('pekerjaan_dokumen_id'));
      $data['pekerjaan_dokumen_id'] = create_id();
      $data['pekerjaan_dokumen_status_review'] = htmlentities($status_dokumen_cc);
      $data['pekerjaan_dokumen_keterangan'] = htmlentities($this->input->post('pekerjaan_dokumen_keterangan'));
      $data['id_create'] = $isi['pegawai_nik'];
      $data['is_review'] = $review;
      // $data['is_proses'] = 'y';
      $data['id_create_awal'] = $data_status['id_create_awal'];
      $this->M_pekerjaan->simpanAksiSamaCC($data);
    } else {
      $data['pekerjaan_dokumen_id_temp'] = htmlentities($this->input->post('pekerjaan_dokumen_id'));
      $data['pekerjaan_dokumen_id'] = create_id();
      $data['pekerjaan_dokumen_file'] = $NewImageName;

      $data['pekerjaan_dokumen_status_review'] = htmlentities($status_dokumen_cc);
      $data['pekerjaan_dokumen_keterangan'] = htmlentities($this->input->post('pekerjaan_dokumen_keterangan'));
      $data['id_create'] = $isi['pegawai_nik'];
      $data['is_review'] = $review;
      // $data['is_proses'] = 'y';
      $data['id_create_awal'] = $data_status['id_create_awal'];
      $this->M_pekerjaan->simpanAksiCC($data);
    }
    /* Insert */
    // cek apakah direvisi 
    if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
      // ambil urutan revisi sebelumnya dari dokumen di pekerjaan tersebut
      $data_revisi = $this->M_pekerjaan->getRevisiNomor($data);
      $data_revisi_isi['pekerjaan_dokumen_revisi'] = $data_revisi['pekerjaan_dokumen_revisi'] + 1;
      // revisi nomor ke doc yang direvisikan
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
    $isi = $this->session->userdata();

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
        $ImageExt       = str_replace('.', '', $ImageExt); // Extension
        $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
        $NewImageName   = str_replace(' ', '', $this->input->get_post('pekerjaan_dokumen_id') . '_' . date('ymdhis') . '_' . $acak . '.' . $ImageExt);

        if (in_array($ImageExt, $Extension)) {
          move_uploaded_file($_FILES["pekerjaan_dokumen_file"]["tmp_name"], $temp . $NewImageName); // Menyimpan file
        }

        // move_uploaded_file($_FILES["pekerjaan_dokumen_file"]["tmp_name"], $temp . $NewImageName); // Menyimpan file

        $note = "Data Berhasil Disimpan";
      } else {
        $note = "Data Gagal Disimpan";
      }
      echo $note;
    } else {
      $NewImageName = null;
    }

    // cek dokumen statusnya dari input status
    $dataku['pekerjaan_dokumen_status'] = $this->input->post('pekerjaan_dokumen_status');

    $sql_status = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->get_post('pekerjaan_dokumen_id') . "'");
    $data_status = $sql_status->row_array();

    $status_dokumen = ($this->input->get_post('pekerjaan_dokumen_status') == 'n') ? '0' : $data_status['pekerjaan_dokumen_status'] + 2;

    if ($data_status['is_proses'] == null || $data_status['is_proses'] == '') {
      $proses = 'y';
    } else if ($data_status['is_proses'] == 'y') {
      $proses = 'a';
    } else {
      $proses = 'y';
    }

    /* Insert */
    if ($NewImageName == null) {
      $data['pekerjaan_dokumen_id_temp'] = htmlentities($this->input->post('pekerjaan_dokumen_id'));
      $data['pekerjaan_dokumen_id'] = htmlentities(create_id());
      $data['pekerjaan_dokumen_status'] = htmlentities($status_dokumen);
      $data['pekerjaan_dokumen_keterangan'] = htmlentities($this->input->post('pekerjaan_dokumen_keterangan'));
      $data['id_create'] = $isi['pegawai_nik'];
      $data['is_proses'] = $proses;
      $data['id_create_awal'] = $data_status['id_create_awal'];
      $this->M_pekerjaan->simpanAksiSama($data);
    } else {
      $data['pekerjaan_dokumen_id_temp'] = htmlentities($this->input->post('pekerjaan_dokumen_id'));
      $data['pekerjaan_dokumen_id'] = htmlentities(create_id());
      $data['pekerjaan_dokumen_file'] = $NewImageName;
      $data['pekerjaan_dokumen_status'] = htmlentities($status_dokumen);
      $data['pekerjaan_dokumen_keterangan'] = htmlentities($this->input->post('pekerjaan_dokumen_keterangan'));
      $data['id_create'] = $isi['pegawai_nik'];
      $data['is_proses'] = $proses;
      $data['id_create_awal'] = $data_status['id_create_awal'];
      $this->M_pekerjaan->simpanAksi($data);
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
    $isi = $this->session->userdata();

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
        $ImageExt       = str_replace('.', '', $ImageExt); // Extension
        $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
        $NewImageName   = str_replace(' ', '', $this->input->get_post('pekerjaan_dokumen_id') . '_' . date('ymdhis') . '_' . $acak . '.' . $ImageExt);

        if (in_array($ImageExt, $Extension)) {
          move_uploaded_file($_FILES["pekerjaan_dokumen_file"]["tmp_name"], $temp . $NewImageName); // Menyimpan file
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

    // cek dokumen statusnya dari input status
    $dokumen_status = ($this->input->get_post('pekerjaan_dokumen_status') == 'n') ? '0' : '4';

    /* Insert */
    if ($NewImageName == null) {
      $data['pekerjaan_dokumen_id_temp'] = htmlentities($this->input->post('pekerjaan_dokumen_id'));
      $data['pekerjaan_dokumen_id'] = create_id();
      $data['pekerjaan_dokumen_status'] = htmlentities($status_dokumen);
      $data['pekerjaan_dokumen_revisi'] = htmlentities($status_dokumen_revisi);
      $data['pekerjaan_dokumen_keterangan'] = htmlentities($this->input->post('pekerjaan_dokumen_keterangan'));
      $data['id_create'] = $isi['pegawai_nik'];
      $data['is_proses'] = 'y';
      $data['id_create_awal'] = $data_status['id_create_awal'];
      $this->M_pekerjaan->simpanAksiSama($data);
    } else {
      $data['pekerjaan_dokumen_id_temp'] = htmlentities($this->input->post('pekerjaan_dokumen_id'));
      $data['pekerjaan_dokumen_id'] = create_id();
      $data['pekerjaan_dokumen_file'] = $NewImageName;
      $data['pekerjaan_dokumen_status'] = htmlentities($status_dokumen);
      $data['pekerjaan_dokumen_revisi'] = htmlentities($status_dokumen_revisi);
      $data['pekerjaan_dokumen_keterangan'] = htmlentities($this->input->post('pekerjaan_dokumen_keterangan'));
      $data['id_create'] = $isi['pegawai_nik'];
      $data['is_proses'] = 'y';
      $data['id_create_awal'] = $data_status['id_create_awal'];
      $this->M_pekerjaan->simpanAksi($data);
    }
    /* Insert */
    // cek apakah direvisi 
    if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
      // ambil urutan revisi sebelumnya dari dokumen di pekerjaan tersebut
      $data_revisi = $this->M_pekerjaan->getRevisiNomor($data);
      $data_revisi_isi['pekerjaan_dokumen_revisi'] = $data_revisi['pekerjaan_dokumen_revisi'] + 1;
      // revisi nomor ke doc yang direvisikan
      $this->M_pekerjaan->updateRevisiNomor($data_revisi_isi, $data['pekerjaan_dokumen_id']);
    }

    if ($data['pekerjaan_dokumen_id_temp']) {
      $data_edit['is_lama'] = 'y';
      $this->M_pekerjaan->editAksi($data_edit, $data['pekerjaan_dokumen_id_temp']);
    }
  }
  /* Aksi Approve / Reject Dokumen IFA */

  /* Aksi Approve / Reject Dokumen IFA CC*/
  public function simpanAksiIFACC()
  {
    $isi = $this->session->userdata();

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
        $ImageExt       = str_replace('.', '', $ImageExt); // Extension
        $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
        $NewImageName   = str_replace(' ', '', $this->input->get_post('pekerjaan_dokumen_id') . '_' . date('ymdhis') . '_' . $acak . '.' . $ImageExt);

        if (in_array($ImageExt, $Extension)) {
          move_uploaded_file($_FILES["pekerjaan_dokumen_file"]["tmp_name"], $temp . $NewImageName); // Menyimpan file
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

    // cek dokumen statusnya dari input status
    $dokumen_status = ($this->input->get_post('pekerjaan_dokumen_status') == 'n') ? '0' : '4';

    /* Insert */
    if ($NewImageName == null) {
      $data['pekerjaan_dokumen_id_temp'] = htmlentities($this->input->post('pekerjaan_dokumen_id'));
      $data['pekerjaan_dokumen_id'] = create_id();
      $data['pekerjaan_dokumen_status'] = htmlentities($status_dokumen);
      $data['pekerjaan_dokumen_status_review'] = htmlentities($status_dokumen_cc);
      $data['pekerjaan_dokumen_keterangan'] = htmlentities($this->input->post('pekerjaan_dokumen_keterangan'));
      $data['id_create'] = $isi['pegawai_nik'];
      $data['is_review'] = $review;
      // $data['is_proses'] = 'y';
      $data['id_create_awal'] = $data_status['id_create_awal'];
      $this->M_pekerjaan->simpanAksiSamaCC($data);
    } else {
      $data['pekerjaan_dokumen_id_temp'] = htmlentities($this->input->post('pekerjaan_dokumen_id'));
      $data['pekerjaan_dokumen_id'] = create_id();
      $data['pekerjaan_dokumen_file'] = $NewImageName;
      $data['pekerjaan_dokumen_status'] = htmlentities($status_dokumen);
      $data['pekerjaan_dokumen_status_review'] = htmlentities($status_dokumen_cc);
      $data['pekerjaan_dokumen_keterangan'] = htmlentities($this->input->post('pekerjaan_dokumen_keterangan'));
      $data['id_create'] = $isi['pegawai_nik'];
      $data['is_review'] = $review;
      // $data['is_proses'] = 'y';
      $data['id_create_awal'] = $data_status['id_create_awal'];
      $this->M_pekerjaan->simpanAksiCC($data);
    }
    /* Insert */
    // cek apakah direvisi 
    if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
      // ambil urutan revisi sebelumnya dari dokumen di pekerjaan tersebut
      $data_revisi = $this->M_pekerjaan->getRevisiNomor($data);
      $data_revisi_isi['pekerjaan_dokumen_revisi'] = $data_revisi['pekerjaan_dokumen_revisi'] + 1;
      // revisi nomor ke doc yang direvisikan
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
    $isi = $this->session->userdata();

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
        $ImageExt       = str_replace('.', '', $ImageExt); // Extension
        $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
        $NewImageName   = str_replace(' ', '', $this->input->get_post('pekerjaan_dokumen_id') . '_' . date('ymdhis') . '_' . $acak . '.' . $ImageExt);

        if (in_array($ImageExt, $Extension)) {
          move_uploaded_file($_FILES["pekerjaan_dokumen_file"]["tmp_name"], $temp . $NewImageName); // Menyimpan file
        }
        $note = "Data Berhasil Disimpan";
      } else {
        $note = "Data Gagal Disimpan";
      }
      echo $note;
    } else {
      $NewImageName = null;
    }

    // cek dokumen statusnya dari input status

    $dataku['pekerjaan_dokumen_status'] = $this->input->post('pekerjaan_dokumen_status');

    $sql_status = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $this->input->get_post('pekerjaan_dokumen_id') . "'");
    $data_status = $sql_status->row_array();

    $status_dokumen = ($this->input->get_post('pekerjaan_dokumen_status') == 'n') ? '0' : $data_status['pekerjaan_dokumen_status'] + 1;

    if ($data_status['is_proses'] == null || $data_status['is_proses'] == '') {
      $proses = 'y';
    } else if ($data_status['is_proses'] == 'y') {
      $proses = 'a';
    }

    $dokumen_status = ($this->input->get_post('pekerjaan_dokumen_status') == 'n') ? '0' : '5';

    /* Insert */
    if ($NewImageName == null) {
      $data['pekerjaan_dokumen_id_temp'] = htmlentities($this->input->post('pekerjaan_dokumen_id'));
      $data['pekerjaan_dokumen_id'] = create_id();
      $data['pekerjaan_dokumen_status'] = htmlentities($dokumen_status);
      $data['pekerjaan_dokumen_keterangan'] = htmlentities($this->input->post('pekerjaan_dokumen_keterangan'));
      $data['id_create'] = $isi['pegawai_nik'];
      $data['is_proses'] = 'y';
      $data['id_create_awal'] = $data_status['id_create_awal'];
      $this->M_pekerjaan->simpanAksiSama($data);
    } else {
      $data['pekerjaan_dokumen_id_temp'] = htmlentities($this->input->post('pekerjaan_dokumen_id'));
      $data['pekerjaan_dokumen_id'] = create_id();
      $data['pekerjaan_dokumen_file'] = $NewImageName;
      $data['pekerjaan_dokumen_status'] = htmlentities($dokumen_status);
      $data['pekerjaan_dokumen_keterangan'] = htmlentities($this->input->post('pekerjaan_dokumen_keterangan'));
      $data['id_create'] = $isi['pegawai_nik'];
      $data['is_proses'] = 'y';
      $data['id_create_awal'] = $data_status['id_create_awal'];
      $this->M_pekerjaan->simpanAksi($data);
    }

    /* Insert */

    // cek apakah direvisi 
    if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
      // ambil urutan revisi sebelumnya dari dokumen di pekerjaan tersebut
      $data_revisi = $this->M_pekerjaan->getRevisiNomor($data);
      $data_revisi_isi['pekerjaan_dokumen_revisi'] = $data_revisi['pekerjaan_dokumen_revisi'] + 1;

      // revisi nomor ke doc yang direvisikan
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

    $this->load->library('PdfGenerator');

    $dokumen = explode('~', $this->input->get_post('pekerjaan_dokumen_file'));
    $format  = explode('.', $dokumen[0]);

    $id_dokumen = preg_replace("/[^0-9^a-z^A-Z]/", "", $dokumen[1]);
    $param['pekerjaan_id'] =  preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get_post('pekerjaan_id'));
    $param_dokumen['pekerjaan_dokumen_id'] = preg_replace("/[^0-9^a-z^A-Z^_.]/", "", $dokumen[0]);

    $data['pekerjaan'] = $this->M_pekerjaan->getPekerjaan($param);
    // $data['bagian'] = $sql_avp_bagian_nama = $this->db->query("SELECT bagian_nama FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai LEFT JOIN global.global_bagian c ON c.bagian_id = a.id_bagian WHERE a.id_pegawai = '" . $value['id_create_awal'] . "'");

    $data['bagian'] = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN global.global_bagian_detail b ON b.id_pegawai = a.id_create_awal LEFT JOIN global.global_bagian c ON c.bagian_id = b.id_bagian WHERE pekerjaan_dokumen_id = '" . $dokumen[1] . "'  ")->row_array();
    // print_r($data['bagian']);

    $sql_template = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON a.id_pekerjaan_template = b.pekerjaan_template_id WHERE pekerjaan_dokumen_id = '" . $id_dokumen . "'");
    $isi_template = $sql_template->row_array();
    $data['template'] = $isi_template;

    $sql_dokumen = $this->db->query("SELECT pekerjaan_dokumen_file FROM dec.dec_pekerjaan_dokumen WHERE  pekerjaan_dokumen_file !=''");
    $data_dokumen = $sql_dokumen->result_array();

    foreach ($data_dokumen as $key => $value) {
      if ($value['pekerjaan_dokumen_file'] == $dokumen[0]) {
        dblog('V', $this->input->get_post('pekerjaan_id'), 'Dokumen '.$isi_template['pekerjaan_template_nama'].' - '.$isi_template['pekerjaan_dokumen_nama'].' Telah Didownload');

        $html =    $this->load->view('project/pekerjaan_cover', $data, true);
        $filename = 'cover_' . preg_replace("/[^0-9^a-z^A-Z^_.]/", "", $dokumen[0]);

        $this->pdfgenerator->save($html, $filename, 'A4', 'portrait');

        $data1['cover_download'] = 'cover_' . preg_replace("/[^0-9^a-z^A-Z^_.]/", "", $dokumen[0]);
        $data1['data_download'] = preg_replace("/[^0-9^a-z^A-Z^_.]/", "", $dokumen[0]);
        $data1['judul'] = $isi_template['pekerjaan_dokumen_nama'].' - '.$isi_template['pekerjaan_dokumen_nomor'];

        $this->load->view('project/combine', $data1);
      }
    }
  }

  public function downloadDokumenUsulan()
  {

    $this->load->library('PdfGenerator');
    $this->load->helper(array('url','download'));

    $dokumen = explode('~', $this->input->get_post('pekerjaan_dokumen_file'));
    $format  = explode('.', $dokumen[0]);

    $id_dokumen = preg_replace("/[^0-9^a-z^A-Z]/", "", $dokumen[1]);
    $param['pekerjaan_id'] =  preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get_post('pekerjaan_id'));
    $param_dokumen['pekerjaan_dokumen_id'] = preg_replace("/[^0-9^a-z^A-Z^_.]/", "", $dokumen[0]);

    $data['pekerjaan'] = $this->M_pekerjaan->getPekerjaan($param);
    // $data['bagian'] = $sql_avp_bagian_nama = $this->db->query("SELECT bagian_nama FROM global.global_bagian_detail a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_user = a.id_pegawai LEFT JOIN global.global_bagian c ON c.bagian_id = a.id_bagian WHERE a.id_pegawai = '" . $value['id_create_awal'] . "'");

    $data['bagian'] = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN global.global_bagian_detail b ON b.id_pegawai = a.id_create_awal LEFT JOIN global.global_bagian c ON c.bagian_id = b.id_bagian WHERE pekerjaan_dokumen_id = '" . $dokumen[1] . "'  ")->row_array();
    // print_r($data['bagian']);

    $sql_template = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON a.id_pekerjaan_template = b.pekerjaan_template_id WHERE pekerjaan_dokumen_id = '" . $id_dokumen . "'");
    $isi_template = $sql_template->row_array();
    $data['template'] = $isi_template;

    $sql_dokumen = $this->db->query("SELECT pekerjaan_dokumen_file FROM dec.dec_pekerjaan_dokumen WHERE  pekerjaan_dokumen_file !=''");
    $data_dokumen = $sql_dokumen->result_array();

    dblog('V', $this->input->get_post('pekerjaan_id'), 'Dokumen '.$isi_template['pekerjaan_dokumen_nama'].' Telah Didownload');

    force_download('./document/'.$isi_template['pekerjaan_dokumen_file'],NULL);
  }
  /* DOWNLOAD */






  // USER LIST
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
  // USER LIST

  /* user list vp */
  public function getUserListVP()
  {
    $isi = $this->session->userdata();

    $list['results'] = array();

    $param['pegawai_nama'] = $this->input->get('pegawai_nama');
    $param['pegawai_poscode'] = $isi['pegawai_poscode'];
    foreach ($this->M_pekerjaan->getUserListVP($param) as $key => $value) {
      array_push($list['results'], [
        'id' => $value['pegawai_nik'],
        'text' => $value['pegawai_nama'] . ' - ' . $value['pegawai_postitle'],
      ]);
    }

    echo json_encode($list);
  }
  /* user list vp */

  /* user list vp */
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
  /* user list vp */

  /* user list vp */
  public function getUserPengganti()
  {
    if (isset($_GET['id_user'])) {
      $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '".$_GET['id_user']."'");
      $isi = $sql_user->row_array();
    } else {
      $isi = $this->session->userdata();
    }

    $sql_bagian = $this->db->query("SELECT * FROM global.global_bagian_detail WHERE id_pegawai='" . $isi['pegawai_nik'] . "'");
    $data_bagian = $sql_bagian->row_array();

    $list['results'] = array();
    $param['pegawai_nama'] = $this->input->get('pegawai_nama');
    // $param['pegawai_poscode'] = $isi['pegawai_poscode'];
    // $param['bagian_nama'] = $isi['pegawai_nama_bag'];
    $param['bagian_id'] = $data_bagian['id_bagian'];

    // $this->M_pekerjaan->getUserListAVP($param);
    // print_r($this->db->last_query());

    foreach ($this->M_pekerjaan->getUserListAVP($param) as $key => $value) {
      array_push($list['results'], [
        'id' => $value['pegawai_nik'],
        'text' => $value['pegawai_nama'] . ' - ' . $value['pegawai_postitle'],
      ]);
    }

    echo json_encode($list);
  }
  /* user list vp */

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


  // GET USER KOOR
  public function getUserKoor()
  {
    $user = $this->session->userdata();

    if ($user['pegawai_poscode'] == 'E53600031A' || $user['pegawai_poscode'] == 'E53500031B') {
      $param['id_user'] = $user['pegawai_nik'];
      $param['pekerjaan_disposisi_status'] = htmlentities($this->input->get_post('status') - 1);
      $data = $this->M_pekerjaan->getUserKoorKhusus($param);
      echo json_encode($data);
    } else {
      $param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
      $param['pekerjaan_disposisi_status'] = $this->input->get_post('status');
      $param['id_user'] = $user['pegawai_nik'];

      $data = $this->M_pekerjaan->getUserKoor($param);

      $param_koor['bagian_id'] = $data['bagian_id'];
      $param_koor['pekerjaan_disposisi_status'] = htmlentities($this->input->get_post('status') - 1);
      $param_koor['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');

      $datanya = $this->M_pekerjaan->getUserKoor($param_koor);
      // echo $this->db->last_query();
      echo json_encode($datanya);
    }
  }
  // GET USER KOOR

  // GET USER KOOR
  public function getUserKoorIFC()
  {
    $user = $this->session->userdata();

    if ($user['pegawai_poscode'] == 'E53600031A' || $user['pegawai_poscode'] == 'E53500031B') {
      $param['id_user'] = $user['pegawai_nik'];
      $param['pekerjaan_disposisi_status'] = htmlentities($this->input->get_post('status') - 3);
      $data = $this->M_pekerjaan->getUserKoorKhusus($param);
      echo json_encode($data);
    } else {
      $param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
      $param['pekerjaan_disposisi_status'] = $this->input->get_post('status');
      $param['id_user'] = $user['pegawai_nik'];

      $data = $this->M_pekerjaan->getUserKoor($param);

      $param_koor['bagian_id'] = $data['bagian_id'];
      $param_koor['pekerjaan_disposisi_status'] = htmlentities($this->input->get_post('status') - 3);

      $datanya = $this->M_pekerjaan->getUserKoor($param_koor);
      echo json_encode($datanya);
    }
  }
  // GET USER KOOR

  public function getUserKoorVP()
  {

    $user = $this->session->userdata();

    if ($user['pegawai_poscode'] == 'E53600031A' || $user['pegawai_poscode'] == 'E53500031B') {
      $param['id_user'] = $user['pegawai_nik'];
      $param['pekerjaan_disposisi_status'] = htmlentities($this->input->get_post('status') - 1);
      $data = $this->M_pekerjaan->getUserKoorKhusus($param);
      // echo $this->db->last_query();
      echo json_encode($data);
    } else {
      $param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
      $param['pekerjaan_disposisi_status'] = htmlentities($this->input->get_post('status') - 1);
      $param['id_user'] = $user['pegawai_nik'];
      $data = $this->M_pekerjaan->getUserKoor($param);
      echo json_encode($data);
    }
  }

  public function getUserKoorVPIFC()
  {

    $user = $this->session->userdata();

    if ($user['pegawai_poscode'] == 'E53600031A' || $user['pegawai_poscode'] == 'E53500031B') {
      $param['id_user'] = $user['pegawai_nik'];
      $param['pekerjaan_disposisi_status'] = htmlentities($this->input->get_post('status'));
      $data = $this->M_pekerjaan->getUserKoorKhusus($param);
      // echo $this->db->last_query();
      echo json_encode($data);
    } else {
      $param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
      $param['pekerjaan_disposisi_status'] = htmlentities($this->input->get_post('status'));
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

    // echo $this->db->last_query();

    $param['pekerjaan_id'] = htmlentities($this->input->get_post('pekerjaan_id'));
    $param['pegawai_nik'] = htmlentities($isi['pegawai_nik']);
    $param['pekerjaan_status'] = htmlentities($this->input->get('pekerjaan_status'));
    // $param['pekerjaan_disposisi_status'] = htmlentities($this->input->get('pekerjaan_status') + 1);

    $param['pekerjaan_disposisi_status'] =  ($data_disposisi_status['total'] > 0) ? htmlentities($this->input->get('pekerjaan_status')) : htmlentities($this->input->get('pekerjaan_status') + 1);
    // $param['pekerjaan_disposisi_status_rev_ifa'] = ($data_disposisi_ifa_rev['total'] > 0) ? htmlentities($this->input->get('pekerjaan_status') - 3) : htmlentities($this->input->get('pekerjaan_status'));


    if ($data_disposisi_ifa_rev['total'] > 0 && $data_is_avp['total'] > 0) {
      $param['pekerjaan_disposisi_status_rev_ifa'] = htmlentities($this->input->get('pekerjaan_status') - 2);
    } else if ($data_disposisi_ifa_rev['total'] > 0 && $data_is_avp['total'] == 0) {
      $param['pekerjaan_disposisi_status_rev_ifa'] = htmlentities($this->input->get('pekerjaan_status') - 3);
    } else {
      $param['pekerjaan_disposisi_status_rev_ifa'] = htmlentities($this->input->get('pekerjaan_status'));
    }

    // print_r($param);

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
      // echo '1';
      $data = $this->M_pekerjaan->getPekerjaanDetail($param);
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
      $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '".$_GET['id_user']."'");
      $user = $sql_user->row_array();
    } else {
      $user = $this->session->userdata();
    }
    $param = array();

    $sql_pekerjaan = $this->db->query("SELECT pekerjaan_status FROM dec.dec_pekerjaan WHERE pekerjaan_id = '" . $this->input->get_post('id_pekerjaan') . "'");
    $data_pekerjaan = $sql_pekerjaan->row_array();

    $sqlCC = $this->db->query("SELECT is_cc FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' AND id_user = '".$user['pegawai_nik']."'");
    $dataCC = $sqlCC->row_array();

    $is_hps = (isset($_GET['is_hps'])) ? $_GET['is_hps'] : 'n';
    $is_cc = (isset($dataCC['is_cc'])) ? $dataCC['is_cc'] : 'n';

    $status_cc = ($is_cc == 'h' && $is_hps == 'n') ? "1=0" : "1=1";

    $sql_cek = ($data_pekerjaan['pekerjaan_status'] == 5) ? $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi a LEFT JOIN dec.dec_pekerjaan b ON b.pekerjaan_id=a.id_pekerjaan WHERE ".$status_cc." AND (pekerjaan_disposisi_status='5' OR pekerjaan_disposisi_status='6') and a.id_user='" . $user['pegawai_nik'] . "'") : $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi a LEFT JOIN dec.dec_pekerjaan b ON b.pekerjaan_id=a.id_pekerjaan WHERE ".$status_cc." AND pekerjaan_disposisi_status <= '".$data_pekerjaan['pekerjaan_status']."' and a.id_user='" . $user['pegawai_nik'] . "'");
    $data_cek = $sql_cek->row_array();
    // echo $this->db->last_query();

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
      foreach ($this->M_pekerjaan->getAsetDocument($param) as $value) {
        // echo $this->db->last_query();
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

        if ($data_cek['total'] >= '1' ||  $user['pegawai_nik'] == '2190626') array_push($data, $isi);
      }
      echo json_encode($data);
    }
  }

  public function getAsetDocumentIFC()
  {
    if (isset($_GET['id_user'])) {
      $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '".$_GET['id_user']."'");
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
    $param['pekerjaan_dokumen_status_min'] = '6';
    $param['id_user'] = $user['pegawai_nik'];

    // if ($this->input->get_post('is_hps') == 'y' && $this->input->get_post('pekerjaan_status') >= '8') {
    // $param['is_cc_hps'] = 'y';
    // }

    if ($data_dokumen_cc['total'] > 0) {
      $param['pekerjaan_dokumen_cc'] = $user['pegawai_nik'];
    }

    $data = array();
    if ($this->input->get_post('id_create') != null) {
      $data = $this->M_pekerjaan->getAsetDocumentUpload($param);
      echo json_encode($data);
    } else {
      foreach ($this->M_pekerjaan->getAsetDocument($param) as $value) {
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
      $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '".$_GET['id_user']."'");
      $user = $sql_user->row_array();
    } else {
      $user = $this->session->userdata();      
    }

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
    $data['pekerjaan_dokumen_nomor'] = $this->input->get_post('pekerjaan_dokumen_nomor');
    $data['pekerjaan_dokumen_jumlah'] = $this->input->get_post('pekerjaan_dokumen_jumlah');
    $data['pekerjaan_dokumen_cc'] = $this->input->get_post('pegawai_nama');
    $data['is_proses'] = null;


    $this->M_pekerjaan->insertPekerjaanDokumen($data);
  }

  public function insertAsetDocumentIFC()
  {
    if (isset($_GET['id_user'])) {
      $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '".$_GET['id_user']."'");
      $user = $sql_user->row_array();
    } else {
      $user = $this->session->userdata();      
    }

    $data['pekerjaan_dokumen_id'] = create_id();
    $data['id_pekerjaan'] = htmlentities($this->input->get_post('pekerjaan_id'));
    $data['pekerjaan_dokumen_nama'] = htmlentities($this->input->get_post('pekerjaan_dokumen_nama'));
    $data['id_pekerjaan_template'] = htmlentities($this->input->get_post('pekerjaan_template_nama'));
    $data['pekerjaan_dokumen_file'] = htmlentities($this->input->get_post('savedFileName'));
    $data['pekerjaan_dokumen_status'] = htmlentities('6');
    $data['who_create'] = htmlentities($user['pegawai_nama']);
    $data['id_create'] = htmlentities($user['pegawai_nik']);
    $data['is_lama'] = htmlentities('n');
    $data['pekerjaan_dokumen_awal'] = htmlentities('n');
    $data['id_create_awal'] = htmlentities($user['pegawai_nik']);
    $data['pekerjaan_dokumen_nomor'] = $this->input->get_post('pekerjaan_dokumen_nomor');
    $data['pekerjaan_dokumen_jumlah'] = $this->input->get_post('pekerjaan_dokumen_jumlah');
    if (($this->input->get_post('is_hps'))) {
      $data['is_hps'] = htmlentities($this->input->get_post('is_hps'));
    }

    $this->M_pekerjaan->insertPekerjaanDokumen($data);
  }

  public function updateAsetDocument()
  {
    if (isset($_GET['id_user'])) {
      $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '".$_GET['id_user']."'");
      $user = $sql_user->row_array();
    } else {
      $user = $this->session->userdata();      
    }

    // cek apakah dokumen revisi atau bukan
    $sql_dokumen_revisi = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id ='" . $this->input->get_post('pekerjaan_dokumen_id') . "' AND pekerjaan_dokumen_status='0'");

    $data_dokumen_revisi = $sql_dokumen_revisi->row_array();

    $sql_jumlah_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as maks FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id ='" . $this->input->get_post('pekerjaan_dokumen_id') . "' AND pekerjaan_dokumen_status='0'");

    $data_jumlah_revisi = $sql_jumlah_revisi->row_array();


    if ($data_dokumen_revisi['total'] > 0) {
      if ($this->input->get_post('savedFileName') == '') {
        $data['pekerjaan_dokumen_id_temp'] = htmlentities($this->input->post('pekerjaan_dokumen_id'));
        $data['pekerjaan_dokumen_id'] = htmlentities(create_id());
        $data['pekerjaan_dokumen_nama'] = htmlentities($this->input->post('pekerjaan_dokumen_nama'));
        $data['pekerjaan_dokumen_jumlah'] = htmlentities($this->input->post('pekerjaan_dokumen_jumlah'));
        $data['id_pekerjaan_template'] = htmlentities($this->input->get_post('pekerjaan_template_nama'));
        $data['pekerjaan_dokumen_status'] = htmlentities('1');
        $data['pekerjaan_dokumen_revisi'] = htmlentities($data_jumlah_revisi['maks'] + 1);
        $data['pekerjaan_dokumen_keterangan'] = htmlentities($this->input->post('pekerjaan_dokumen_keterangan'));
        $data['id_create'] = $user['pegawai_nik'];
        $data['is_proses'] = null;
        $data['id_create_awal'] = $user['pegawai_nik'];
        $this->M_pekerjaan->simpanAksiSamaRevisi($data);
      } else {
        $data['pekerjaan_dokumen_id_temp'] = htmlentities($this->input->post('pekerjaan_dokumen_id'));
        $data['pekerjaan_dokumen_id'] = htmlentities(create_id());
        $data['pekerjaan_dokumen_nama'] = htmlentities($this->input->post('pekerjaan_dokumen_nama'));
        $data['id_pekerjaan_template'] = htmlentities($this->input->get_post('pekerjaan_template_nama'));
        $data['pekerjaan_dokumen_jumlah'] = htmlentities($this->input->post('pekerjaan_dokumen_jumlah'));
        $data['pekerjaan_dokumen_file'] = $this->input->get_post('savedFileName');
        $data['pekerjaan_dokumen_status'] = htmlentities('1');
        // $data['pekerjaan_dokumen_revisi'] = htmlentities($data_jumlah_revisi['maks'] + 1);
        // $data['pekerjaan_dokumen_keterangan'] = htmlentities($this->input->post('pekerjaan_dokumen_keterangan'));
        $data['id_create'] = $user['pegawai_nik'];
        $data['is_proses'] = null;
        $data['id_create_awal'] = $user['pegawai_nik'];
        $this->M_pekerjaan->simpanAksiRevisi($data);
      }
      $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET is_lama = 'y' WHERE pekerjaan_dokumen_id ='" . $this->input->get_post('pekerjaan_dokumen_id') . "' AND pekerjaan_dokumen_status='0'");
    } else {
      $id = htmlentities($this->input->get_post('pekerjaan_dokumen_id'));
      $data['id_pekerjaan'] = htmlentities($this->input->get_post('id_pekerjaan'));
      $data['pekerjaan_dokumen_nama'] = htmlentities($this->input->get_post('pekerjaan_dokumen_nama'));
      $data['pekerjaan_dokumen_jumlah'] = htmlentities($this->input->post('pekerjaan_dokumen_jumlah'));
      $data['id_pekerjaan_template'] = htmlentities($this->input->get_post('pekerjaan_template_nama'));
      if ($this->input->get_post('savedFileName') != '') {
        $data['pekerjaan_dokumen_file'] = htmlentities($this->input->get_post('savedFileName'));
      }
      $data['pekerjaan_dokumen_status'] = htmlentities('1');
      $data['pekerjaan_dokumen_status_review'] = htmlentities('1');
      $data['who_create'] = htmlentities($user['pegawai_nama']);
      $data['id_create'] = htmlentities($user['pegawai_nik']);
      $data['is_lama'] = htmlentities('n');
      $data['pekerjaan_dokumen_awal'] = htmlentities('n');
      if (($this->input->get_post('is_hps'))) {
        $data['is_hps'] = htmlentities($this->input->get_post('is_hps'));
      }
      $data['id_create_awal'] = htmlentities($user['pegawai_nik']);
      $data['pekerjaan_dokumen_nomor'] = $this->input->get_post('pekerjaan_dokumen_nomor');
      $data['pekerjaan_dokumen_cc'] = $this->input->get_post('pegawai_nama');
      $data['is_proses'] = null;

      $this->M_pekerjaan->updatePekerjaanDokumen($data, $id);
    }
  }

  public function updateAsetDocumentIFC()
  {
    if (isset($_GET['id_user'])) {
      $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '".$_GET['id_user']."'");
      $user = $sql_user->row_array();
    } else {
      $user = $this->session->userdata();      
    }

    $id = htmlentities($this->input->get_post('pekerjaan_dokumen_id'));
    $data['id_pekerjaan'] = htmlentities($this->input->get_post('id_pekerjaan'));
    $data['pekerjaan_dokumen_nama'] = htmlentities($this->input->get_post('pekerjaan_dokumen_nama'));
    $data['id_pekerjaan_template'] = htmlentities($this->input->get_post('pekerjaan_template_nama'));
    if ($this->input->get_post('savedFileName') != '') {
      $data['pekerjaan_dokumen_file'] = htmlentities($this->input->get_post('savedFileName'));
    }
    $data['pekerjaan_dokumen_status'] = '6';
    $data['who_create'] = htmlentities($user['pegawai_nama']);
    $data['id_create'] = htmlentities($user['pegawai_nik']);
    $data['is_lama'] = htmlentities('n');
    $data['pekerjaan_dokumen_awal'] = htmlentities('n');
    $data['id_create_awal'] = htmlentities($user['pegawai_nik']);
    $data['pekerjaan_dokumen_nomor'] = $this->input->get_post('pekerjaan_dokumen_nomor');
    if (($this->input->get_post('is_hps'))) {
      $data['is_hps'] = htmlentities($this->input->get_post('is_hps'));
    }


    $this->M_pekerjaan->updatePekerjaanDokumen($data, $id);
  }


  public function insertAsetDocumentDetail()
  {
    $data['pekerjaan_dokumen_id'] = create_id();
    $data['id_pekerjaan'] = htmlentities($this->input->get_post('pekerjaan_id'));
    $data['pekerjaan_dokumen_nama'] = htmlentities($this->input->get_post('pekerjaan_dokumen_nama'));
    $data['pekerjaan_dokumen_file'] = htmlentities($this->input->get_post('savedFileName'));

    $this->M_pekerjaan->insertPekerjaanDokumen($data);
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
        $data['id_user'] = htmlentities($id_user);
        $data['id_pekerjaan'] = htmlentities($this->input->get_post('id_pekerjaan_approve_avp'));
        $data['pekerjaan_disposisi_status'] = htmlentities('AVP');
        $this->M_pekerjaan->insertPekerjaanDisposisi($data);
      }
      $id = $this->input->get_post('id_pekerjaan_approve_avp');
    }
  }

  public function updateAsetDocumentApproveAVP()
  {

    $user = $this->session->userdata();


    if ($this->input->get_post('usr_id')) {
      $id_user = (explode(',', $this->input->get_post('usr_id')));
      foreach ($id_user as $key => $id_usr) {
        // insert dokumen baru dan ubah status dokumen lama ke non aktif agar dokumen baru yang ditampilkan
        $param['pekerjaan_disposisi_id'] = create_id();
        $param['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $param['id_user'] = htmlentities($id_usr);
        $param['id_pekerjaan'] = htmlentities($this->input->get_post('id_pekerjaan'));
        $param['pekerjaan_disposisi_status'] = htmlentities('AVP');
        $param['id_penanggung_jawab'] = htmlentities($this->input->get_post('usr_id_pj'));

        $this->M_pekerjaan->insertPekerjaanDisposisi($param, 'AVP');
      }

      $param1['pekerjaan_dokumen_id'] = create_id();
      $param1['id_pekerjaan'] = htmlentities($this->input->get_post('id_pekerjaan'));
      $param1['id_pekerjaan_disposisi'] = htmlentities($param['pekerjaan_disposisi_id']);
      $param1['pekerjaan_dokumen_nama'] = htmlentities($this->input->get_post('pekerjaan_dokumen_nama'));
      $param1['pekerjaan_dokumen_keterangan'] = htmlentities($this->input->get_post('pekerjaan_dokumen_keterangan'));
      $param1['pekerjaan_dokumen_status'] = htmlentities($this->input->get_post('pekerjaan_dokumen_status_nama'));
      $param1['pekerjaan_dokumen_file'] = $this->input->get_post('savedFileName');
      $param1['who_create'] = htmlentities($user['pegawai_nama']);
      $param1['who_create'] = htmlentities($user['pegawai_nik']);

      $this->M_pekerjaan->insertPekerjaanDokumen($param1);

      // update dokumen lama 
      $id = $this->input->get_post('pekerjaan_dokumen_id');
      $param1['is_lama'] = 'y';

      $this->M_pekerjaan->updatePekerjaanDokumen($param1, $id);
    }
  }

  public function updateAsetDocumentApproveVP($data = null)
  {
    $user = $this->session->userdata();
    // foreach($id_disposisi as $key=>$id_dis)
    // insert dokumen baru dan ubah status dokumen lama ke non aktif agar dokumen baru yang ditampilkan
    $param['pekerjaan_dokumen_id'] = create_id();
    $param['id_pekerjaan'] = htmlentities($this->input->get_post('id_pekerjaan'));
    $param['pekerjaan_dokumen_nama'] = htmlentities($this->input->get_post('pekerjaan_dokumen_nama'));
    // $param['pekerjaan_dokumen_departemen'] = $this->input->get_post('pekerjaan_dokumen_departemen');
    $param['pekerjaan_dokumen_keterangan'] = htmlentities($this->input->get_post('pekerjaan_dokumen_keterangan'));
    $param['pekerjaan_dokumen_status'] = htmlentities($this->input->get_post('pekerjaan_dokumen_status_nama'));
    $param['pekerjaan_dokumen_file'] = htmlentities($this->input->get_post('savedFileName'));
    // $param['id_pekerjaan_disposisi'] = $this->input->get_post('id_pekerjaan_disposisi');
    // $param['id_penanggung_jawab'] = $this->input->get_post('id_penanggung_jawab');
    $param['who_create'] = htmlentities($user['pegawai_nama']);
    $param['id_create'] = htmlentities($user['pegawai_nik']);

    $this->M_pekerjaan->insertPekerjaanDokumen($param);


    // update dokumen lama 
    $id = $this->input->get_post('pekerjaan_dokumen_id');
    $param1['is_lama'] = 'y';

    $this->M_pekerjaan->updatePekerjaanDokumen($param1, $id);
  }
  /* UPDATE */

  /* DELETE */
  public function deleteAsetDocument2()
  {
    $this->M_pekerjaan->deleteAsetDocument2($this->input->get('id_pekerjaan'));
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

  // REMINDER
  public function reminder()
  {
    $user = $this->session->userdata();
    $param['id_user'] = $user['pegawai_nik'];

    $data = $this->M_pekerjaan->getReminder($param);

    foreach ($data as $key => $val) {
      $tanggal_extend = $val['extend_tanggal'];
      $tanggal_extend_reminder = date('Y-m-d', strtotime($tanggal_extend . '- 2 days'));
      $tanggal_sekarang = date('Y-m-d');
      // jika reminder nya hari sekarang
      if ($tanggal_extend_reminder == $tanggal_sekarang) {
        $email_penerima = htmlentities($user['email_pegawai']);
        $subjek = htmlentities('Reminder');
        $pesan = ('Anda Memiliki Pekerjaan Yang Harus Diselesaikan Sebelum Tanggal ' . $tanggal_extend . ' Klik <a href=' . base_url() . ' target="_blank">Tautan Berikut</a> Untuk Detailnya');
        print_r($pesan);
        $sendmail = array(
          'email_penerima' => $email_penerima,
          'subjek' => $subjek,
          'content' => $pesan,
        );        echo $send; // Panggil fungsi send yang ada di librari Mailer      }
      }
      // cek pekerjaan 
    }
    // REMINDER
  }

  // AUTO UPDATE STATUS
  public function autoUpdateIFA()
  {
    $user = $this->session->userdata();
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
          $data['pekerjaan_status'] = htmlentities($pekerjaan_status);


          $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

          dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reviewed Oleh PIC');
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
          $data_disposisi['id_user'] = htmlentities($value['id_user']);
          $data_disposisi['id_pekerjaan'] = htmlentities($pekerjaan_id);
          $data_disposisi['pekerjaan_disposisi_status'] = htmlentities($pekerjaan_status);


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
          );          echo $send; // Panggil fungsi send yang ada di librari Mailer

          // INSERT KE DB EMAIL
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
  // AUTO UPDATE STATUS

  public function cancelDokumen()
  {
    // $user = $this->session->userdata();
    $pekerjaan_dokumen_id = $this->input->post('pekerjaan_dokumen_id');

    $this->M_pekerjaan->deletePekerjaanDokumen($pekerjaan_dokumen_id);
  }

  public function insertCCDraft()
  {


    $isi = $this->session->userdata();
    $pekerjaan_id = htmlentities($this->input->get_post('id_pekerjaan'));
    $id_tanggung_jawab = null;
    $pekerjaan_status = htmlentities('8');
    $is_cc = 'y';
    // CC


    if ($this->input->get_post('id_user_staf')) {
      $this->M_pekerjaan->deletePekerjaanDisposisi($pekerjaan_id, null, $id_tanggung_jawab, $pekerjaan_status, $is_cc);
      // print_r($this->db->last_query());
      $user = $this->input->get_post('id_user_staf');
      foreach ($user as $key => $value) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = htmlentities($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = htmlentities('8');
        $data_disposisi_doc['id_penanggung_jawab'] = htmlentities('n');
        $data_disposisi_doc['is_cc'] = htmlentities('y');
        $data_disposisi_doc['is_aktif'] = 'y';

        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
      }
    }
    // CC


  }

  public function insertCCDraftHPS()
  {


    $isi = $this->session->userdata();
    $pekerjaan_id = htmlentities($this->input->get_post('id_pekerjaan_hps'));
    $id_tanggung_jawab = null;
    $pekerjaan_status = htmlentities('8');
    $is_cc = 'h';
    // CC


    if ($this->input->get_post('id_user_staf_hps')) {
      $this->M_pekerjaan->deletePekerjaanDisposisi($pekerjaan_id, null, $id_tanggung_jawab, $pekerjaan_status, $is_cc);
      // print_r($this->db->last_query());
      $user = $this->input->get_post('id_user_staf_hps');
      foreach ($user as $key => $value) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = htmlentities($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = htmlentities('8');
        $data_disposisi_doc['id_penanggung_jawab'] = htmlentities('n');
        $data_disposisi_doc['is_cc'] = htmlentities('h');
        $data_disposisi_doc['is_aktif'] = 'y';

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

        // INSERT KE DB EMAIL
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
    // CC


  }

  public function insertCCDraftIFC()
  {


    $isi = $this->session->userdata();
    $pekerjaan_id = htmlentities($this->input->get_post('id_pekerjaan_ifc'));
    $id_tanggung_jawab = null;
    $pekerjaan_status = htmlentities('8');
    $is_cc = 'y';
    // CC


    if ($this->input->get_post('id_user_staf_ifc')) {
      $this->M_pekerjaan->deletePekerjaanDisposisi($pekerjaan_id, null, $id_tanggung_jawab, $pekerjaan_status, $is_cc);
      // print_r($this->db->last_query());
      $user = $this->input->get_post('id_user_staf_ifc');
      foreach ($user as $key => $value) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = htmlentities($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = htmlentities('8');
        $data_disposisi_doc['id_penanggung_jawab'] = htmlentities('n');
        $data_disposisi_doc['is_cc'] = htmlentities('y');
        $data_disposisi_doc['is_aktif'] = 'y';

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

        // INSERT KE DB EMAIL
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
    // CC
  }

  public function insertCCDraftIFCHPS()
  {


    $isi = $this->session->userdata();
    $pekerjaan_id = htmlentities($this->input->get_post('id_pekerjaan_ifc_hps'));
    $id_tanggung_jawab = null;
    $pekerjaan_status = htmlentities('8');
    $is_cc = 'y';
    // CC


    if ($this->input->get_post('id_user_staf_ifc_hps')) {
      $this->M_pekerjaan->deletePekerjaanDisposisi($pekerjaan_id, null, $id_tanggung_jawab, $pekerjaan_status, $is_cc);
      // print_r($this->db->last_query());
      $user = $this->input->get_post('id_user_staf_ifc_hps');
      foreach ($user as $key => $value) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = htmlentities($value);
        $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_doc['pekerjaan_disposisi_status'] = htmlentities('8');
        $data_disposisi_doc['id_penanggung_jawab'] = htmlentities('n');
        $data_disposisi_doc['is_cc'] = htmlentities('h');
        $data_disposisi_doc['is_aktif'] = 'y';

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

        // INSERT KE DB EMAIL
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
    // CC
  }


  // Ganti Perencana
  public function gantiPerencana()
  {
    if (isset($_GET['id_user'])) {
      $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '".$_GET['id_user']."'");
      $user = $sql_user->row_array();
    } else {
      $user = $this->session->userdata();
    }

    $pekerjaan_id = $this->input->post('id_pekerjaan');
    $disposisi_status = $this->input->post('pekerjaan_status');
    $user_id = $user['pegawai_nik'];
    $this->M_pekerjaan->deletePekerjaanDisposisiReject($pekerjaan_id, $disposisi_status, $user_id);

    $param['pekerjaan_disposisi_id'] = create_id();
    $param['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
    $param['id_user'] = $this->input->post('id_user');
    $param['id_pekerjaan'] = $this->input->post('id_pekerjaan');
    $param['pekerjaan_disposisi_status'] = $this->input->post('pekerjaan_status');
    $param['is_aktif'] = 'y';

    $this->M_pekerjaan->insertPekerjaanDisposisi($param);

    // is_listin
  }
  // Ganti Perencana

  /* Lihat Dokumen */
    public function lihatDokumen() {      
      $sql_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON a.id_pekerjaan_template = b.pekerjaan_template_id WHERE pekerjaan_dokumen_id = '".$_GET['id']."'");
      $dokumen = $sql_dokumen->row_array();

      $judul = ($dokumen['pekerjaan_dokumen_awal'] == 'y') ? $dokumen['pekerjaan_dokumen_nama'] : $dokumen['pekerjaan_template_nama'].' - '.$dokumen['pekerjaan_dokumen_nama'];

      dblog('V', $dokumen['id_pekerjaan'], 'Dokumen '.$judul.' Telah Dilihat');

      echo json_encode('1');
    }
  /* Lihat Dokumen */

  /* Cek Status Dokumen AVP Koor */
    public function getStatusKoor() {
      if (isset($_GET['id_user'])) {
        $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '".$_GET['id_user']."'");
        $user = $sql_user->row_array();
      } else {
        $user = $this->session->userdata();
      }

      $sql_total = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE id_penanggung_jawab = 'y' AND id_pekerjaan = '" . $_GET['pekerjaan_id'] . "' AND  id_user = '" . $user['pegawai_nik'] . "'");
      $isi_total = $sql_total->row_array();

      if ($isi_total['total'] > 0) {
        $sql_dokumen = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_awal = 'n' AND is_lama = 'n' AND pekerjaan_dokumen_status < '3' AND id_pekerjaan = '".$_GET['pekerjaan_id']."'");
        $dokumen = $sql_dokumen->row_array();
      } else {
        $sql_dokumen = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_dokumen WHERE 1=3 AND pekerjaan_dokumen_awal = 'n' AND is_lama = 'n' AND pekerjaan_dokumen_status < '3' AND id_pekerjaan = '".$_GET['pekerjaan_id']."'");
        $dokumen = $sql_dokumen->row_array();
      }

      echo json_encode($dokumen);
    }
  /* Cek Status Dokumen AVP Koor */

  /* Get Dokumen Selesai */
    public function getDokumenSelesai() {
      $user = $this->session->userdata();

      $sqlCC = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE is_cc = 'h' AND id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' AND id_user = '".$user['pegawai_nik']."'");
      $dataCC = $sqlCC->row_array();

      if ($user['pegawai_id_dep'] == 'E53000' || $dataCC['total'] == 0) {
        $sql = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON a.id_pekerjaan_template = b.pekerjaan_template_id LEFT JOIN dec.dec_pekerjaan c ON c.pekerjaan_id = a.id_pekerjaan LEFT JOIN global.global_bagian_detail d ON d.id_pegawai = a.id_create_awal LEFT JOIN global.global_bagian e ON e.bagian_id = d.id_bagian LEFT JOIN global.global_pegawai f ON f.pegawai_nik = d.id_pegawai LEFT JOIN global.global_pegawai h ON a.pekerjaan_dokumen_cc = h.pegawai_nik LEFT JOIN global.global_bidang j ON a.id_bidang = j.bidang_id LEFT JOIN global.global_urutan_proyek k ON a.id_urutan_proyek = k.urutan_proyek_id LEFT JOIN global.global_section_area l ON a.id_section_area = l.section_area_id WHERE pekerjaan_dokumen_awal = 'n' AND (is_lama != 'y' or is_lama is null) AND pekerjaan_dokumen_status <= '7' AND a.id_pekerjaan = '".$_GET['id_pekerjaan']."' AND is_hps = '".$_GET['is_hps']."'");
        $data = $sql->result_array();
      } else {
        $data = [];
      }

      echo json_encode($data);
    }

  /* Get Dokumen Selesai */
    public function getDokumenSelesaiHPS() {
      $user = $this->session->userdata();

      $sqlCC = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE is_cc = 'h' AND id_pekerjaan = '" . $this->input->get_post('id_pekerjaan') . "' AND id_user = '".$user['pegawai_nik']."'");
      $dataCC = $sqlCC->row_array();

      if ($user['pegawai_id_dep'] == 'E53000' || $dataCC['total'] > 0) {
        $sql = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON a.id_pekerjaan_template = b.pekerjaan_template_id LEFT JOIN dec.dec_pekerjaan c ON c.pekerjaan_id = a.id_pekerjaan LEFT JOIN global.global_bagian_detail d ON d.id_pegawai = a.id_create_awal LEFT JOIN global.global_bagian e ON e.bagian_id = d.id_bagian LEFT JOIN global.global_pegawai f ON f.pegawai_nik = d.id_pegawai LEFT JOIN global.global_pegawai h ON a.pekerjaan_dokumen_cc = h.pegawai_nik LEFT JOIN global.global_bidang j ON a.id_bidang = j.bidang_id LEFT JOIN global.global_urutan_proyek k ON a.id_urutan_proyek = k.urutan_proyek_id LEFT JOIN global.global_section_area l ON a.id_section_area = l.section_area_id WHERE pekerjaan_dokumen_awal = 'n' AND (is_lama != 'y' or is_lama is null) AND pekerjaan_dokumen_status <= '7' AND a.id_pekerjaan = '".$_GET['id_pekerjaan']."' AND is_hps = '".$_GET['is_hps']."'");
        $data = $sql->result_array();
      } else {
        $data = [];
      }

      echo json_encode($data);
    }
  /* Get Dokumen Selesai */
}
