<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pekerjaan_usulan extends MX_Controller
{
  public function __construct()
  {
    parent::__construct();

    $this->load->model('M_pekerjaan');
    $this->load->model('master/M_user');
    $this->load->library('mailer');
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
    $param['pekerjaan_id'] = $this->input->get('pekerjaan_id');

    $data["pekerjaan"] = $this->M_pekerjaan->getPekerjaan($param);

    $this->load->view('project/detail_pekerjaan_usulan', $data);
  }
  /* Index Pekerjaan Detail */
  /* INDEX */

  /* PEKERJAAN USULAN */
  /* GET */
  /* Get Pekerjaan Ususlan */
  public function getPekerjaanUsulan()
  {
    $param['pekerjaan_id'] = $this->input->get('pekerjaan_id');
    $data = array();
    $session = $this->session->userdata();

    if ($param['pekerjaan_id'] != null) {
      $data = $this->M_pekerjaan->getPekerjaan($param);
    } else {
      foreach ($this->M_pekerjaan->getPekerjaan($param) as $value) {
        foreach ($value as $key => $val) {
          $isi[$key] = $val;
        }
        $sql_total = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '" . $value['pekerjaan_status'] . "' AND id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND  id_user = '" . $session['pegawai_nik'] . "'");
        $isi_total = $sql_total->row_array();

        $isi['milik'] = ($isi_total['total'] > 0 || ($value['pic'] == $session['pegawai_nik'] && ($value['pekerjaan_status'] == '0' || $value['pekerjaan_status'] == '-'))) ? 'y' : 'n';

        array_push($data, $isi);
      }
    }


    echo json_encode($data);
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
    $data = $this->M_pekerjaan->getTemplatePekerjaan();
    echo json_encode($data);
  }
  /* Get Pekerjaan Dokumen */
  /* GET */

  /* PROSES */
  /* Pekerjaan Dreft */
  public function insertPekerjaan()
  {
    $isi = $this->session->userdata();
    $pekerjaan_status = '0';

    $data['pekerjaan_id'] = $this->input->post('pekerjaan_id');
    $data['pekerjaan_judul'] = $this->input->post('pekerjaan_judul');
    $data['pekerjaan_waktu'] = $this->input->post('pekerjaan_waktu');
    $data['pekerjaan_waktu_akhir'] = $this->input->post('pekerjaan_waktu_akhir');
    $data['id_klasifikasi_pekerjaan'] = $this->input->post('id_klasifikasi_pekerjaan');
    $data['pekerjaan_deskripsi'] = $this->input->post('pekerjaan_deskripsi');
    $data['pic'] = $isi['pegawai_nik'];
    $data['pic_no_telp'] = $this->input->post('pic_no_telp');
    $data['pekerjaan_status'] = $pekerjaan_status;
    $data['id_pekerjaan_disposisi'] = $this->input->post('id_pekerjaan_disposisi');

    $this->M_pekerjaan->insertPekerjaan($data);

    // Email


    dblog('I',  $data['pekerjaan_id'], 'Pekerjaan Tersimpan di Draft');
  }
  /* Pekerjaan Dreft */

  /* Pekerjaan Send */
  public function insertPekerjaanSend()
  {
    $isi = $this->session->userdata();

    if ($this->input->post('jabatan_temp') == '2') $pekerjaan_status = '3';
    elseif ($this->input->post('jabatan_temp') == '3') $pekerjaan_status = '2';
    else $pekerjaan_status = '1';

    $pekerjaan_status_temp = $this->input->post('pekerjaan_status');
    $pekerjaan_id = $this->input->post('pekerjaan_id');

    if ($pekerjaan_status_temp == '1') { // Ketika ada dreft
      $data['pekerjaan_judul'] = $this->input->post('pekerjaan_judul');
      $data['pekerjaan_waktu'] = $this->input->post('pekerjaan_waktu');
      $data['pekerjaan_waktu_akhir'] = $this->input->post('pekerjaan_waktu_akhir');
      $data['id_klasifikasi_pekerjaan'] = $this->input->post('id_klasifikasi_pekerjaan');
      $data['pekerjaan_deskripsi'] = $this->input->post('pekerjaan_deskripsi');
      $data['pic'] = $this->input->post('pic');
      $data['pic_no_telp'] = $this->input->post('pic_no_telp');
      $data['id_pekerjaan_disposisi'] = $this->input->post('id_pekerjaan_disposisi');
      $data['tipe_pekerjaan'] = $this->input->post('tipe_pekerjaan');
      $data['pekerjaan_status'] = $pekerjaan_status;

      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
    } else { // Ketika langsung send
      $data['pekerjaan_id'] = $this->input->post('pekerjaan_id');
      $data['pekerjaan_judul'] = $this->input->post('pekerjaan_judul');
      $data['pekerjaan_waktu'] = $this->input->post('pekerjaan_waktu');
      $data['pekerjaan_waktu_akhir'] = $this->input->post('pekerjaan_waktu_akhir');
      $data['id_klasifikasi_pekerjaan'] = $this->input->post('id_klasifikasi_pekerjaan');
      $data['pekerjaan_deskripsi'] = $this->input->post('pekerjaan_deskripsi');
      $data['pic'] = $isi['pegawai_nik'];
      $data['pic_no_telp'] = $this->input->post('pic_no_telp');
      $data['pekerjaan_status'] = $pekerjaan_status;
      $data['id_pekerjaan_disposisi'] = $this->input->post('id_pekerjaan_disposisi');

      $this->M_pekerjaan->insertPekerjaan($data);
    }

    dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Send ke AVP Customer');

    /* User */
    $data_user['pegawai_poscode'] = ($pekerjaan_status == '3') ? 'E53000000' : $isi['pegawai_direct_superior'];

    $user = $this->M_user->getUser($data_user);
    /* User */

    /* Disposisi */
    $data_disposisi['pekerjaan_disposisi_id'] = create_id();
    $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
    $data_disposisi['id_user'] = $user['pegawai_nik'];
    $data_disposisi['id_pekerjaan'] = $pekerjaan_id;
    $data_disposisi['pekerjaan_disposisi_status'] = $pekerjaan_status;

    $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

    $email_penerima = $user['email_pegawai'];
    $subjek = $this->input->get_post('pekerjaan_judul');
    $pesan = $this->input->get_post('pekerjaan_deskripsi');
    $sendmail = array(
      'email_penerima' => $email_penerima,
      'subjek' => $subjek,
      'content' => $pesan,
    );
    $send = $this->mailer->send($sendmail); // Panggil fungsi send yang ada di librari Mailer
    echo "<b>" . $send['status'] . "</b><br />";
    echo $send['message'];
    // INSERT KE DB EMAIL
    $param_email['email_id'] = create_id();
    $param_email['id_penerima'] = $user['pegawai_nik'];
    $param_email['id_pengirim'] = $isi['pegawai_nik'];
    $param_email['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
    // $param_email['pic'] 
    $param_email['id_pekerjaan_disposisi'] = $data_disposisi['pekerjaan_disposisi_id'];
    // $param_email['id_pekerjaan_dokumen'] 
    $param_email['email_subject'] = $subjek;
    $param_email['email_content'] = $pesan;
    $param_email['when_created'] = date('Y-m-d H:i:s');
    $param_email['who_created'] = $isi['pegawai_nama'];

    $this->M_pekerjaan->insertEmail($param_email);
    // $param_email['email_attach'] 

    /* Disposisi */
  }
  /* Pekerjaan Send */

  /* Pekerjaan Edit */
  public function updatePekerjaan()
  {
    $pekerjaan_id = $this->input->post('pekerjaan_id');
    if ($pekerjaan_id) {
      $data['pekerjaan_judul'] = $this->input->post('pekerjaan_judul');
      $data['pekerjaan_waktu'] = $this->input->post('pekerjaan_waktu');
      $data['pekerjaan_waktu_akhir'] = $this->input->post('pekerjaan_waktu_akhir');
      $data['id_klasifikasi_pekerjaan'] = $this->input->post('id_klasifikasi_pekerjaan');
      $data['pekerjaan_deskripsi'] = $this->input->post('pekerjaan_deskripsi');
      $data['pic'] = $this->input->post('pic');
      $data['pic_no_telp'] = $this->input->post('pic_no_telp');
      $data['id_pekerjaan_disposisi'] = $this->input->post('id_pekerjaan_disposisi');
      $data['tipe_pekerjaan'] = $this->input->post('tipe_pekerjaan');
      $data['pekerjaan_status'] = '0';

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

      if (!empty($tmpFile)) {
        $random = rand(11111111, 99999999);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $fileName = pathinfo($fileName, PATHINFO_FILENAME);
        $newFileName = str_replace(' ', '', $_POST['id_pekerjaan'] . '_' . date('ymdhis') . '_' . $random . '.' . $fileExt);

        move_uploaded_file($tmpFile, $directory . $newFileName);
        echo $newFileName;
      }
    }
  }
  /* Insert File Pekerjaan Dokumen */

  /* Insert Pekerjaan Dokumen */
  public function insertPekerjaanDokumenUsulan()
  {
    $user = $this->session->userdata();

    $data['pekerjaan_dokumen_id'] = create_id();
    $data['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
    $data['pekerjaan_dokumen_nama'] = $this->input->get_post('pekerjaan_dokumen_nama');
    $data['pekerjaan_dokumen_file'] = $this->input->get_post('savedFileName');
    $data['pekerjaan_dokumen_status'] = '1';
    $data['who_create'] = $user['pegawai_nama'];
    $data['is_lama'] = 'n';
    $data['pekerjaan_dokumen_awal'] = 'y';

    $this->M_pekerjaan->insertPekerjaanDokumen($data);
  }
  /* Insert Pekerjaan Dokumen */

  /* Update Pekerjaan Dokumen */
  public function updatePekerjaanDokumen()
  {
    $id = $this->input->post('pekerjaan_dokumen_id');
    $data = array(
      'id_pekerjaan' => $this->input->post('id_pekerjaan'),
      'pekerjaan_dokumen_nama' => $this->input->post('pekerjaan_dokumen_nama'),
      'pekerjaan_dokumen_file' => $this->input->post('savedFileName'),
      'pekerjaan_dokumen_status' => '1',
    );

    $this->M_pekerjaan->updatePekerjaanDokumen($data, $id);
  }
  /* Update Pekerjaan Dokumen */

  /* Proses Send VP */
  public function prosesSendVP()
  {
    $isi = $this->session->userdata();
    $pekerjaan_id = $this->input->get_post('id_pekerjaan_send_vp');
    $id_tanggung_jawab = null;
    $pekerjaan_status_send_vp = '8';
    // $pekerjaan_status = '9';


    if ($this->input->get_post('id_user_send_vp')) {

      $this->M_pekerjaan->deletePekerjaanDisposisi($pekerjaan_id, null, $id_tanggung_jawab, $pekerjaan_status_send_vp);

      $user = $this->input->get_post('id_user_send_vp');
      foreach ($user as $key => $value) {
        $data_disposisi_vp['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_vp['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
        $data_disposisi_vp['id_user'] = $value;
        $data_disposisi_vp['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_vp['pekerjaan_disposisi_status'] = '8';
        $data_disposisi_vp['id_penanggung_jawab'] = 'n';

        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_vp);
      }
    }
    /* Pekerjaan */
    $pekerjaan_status = $this->input->get_post('pekerjaan_status') + 1;
    // $pekerjaan_status = '9';

    $pekerjaan_id = $this->input->get_post('id_pekerjaan_send_vp');
    if ($pekerjaan_id) {
      $data['pekerjaan_status'] = $pekerjaan_status;
      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
      // print_r($this->db->last_query());

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
    $data_disposisi['id_pekerjaan'] = $pekerjaan_id;
    $data_disposisi['pekerjaan_disposisi_status'] = $pekerjaan_status;
    // print_r($data_disposisi);
    $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
    /* Disposisi */
  }


  /* Proses Send VP */

  public function prosesApproveVP()
  {
    $isi = $this->session->userdata();
    $pekerjaan_id = $this->input->get_post('id_pekerjaan_approve_vp');
    $id_tanggung_jawab = null;
    $pekerjaan_status_approve_vp = '8';
    // $pekerjaan_status = '9';


    if ($this->input->get_post('id_user_approve_vp')) {

      $this->M_pekerjaan->deletePekerjaanDisposisi($pekerjaan_id, null, $id_tanggung_jawab, $pekerjaan_status_approve_vp);

      $user = $this->input->get_post('id_user_approve_vp');
      foreach ($user as $key => $value) {
        $data_disposisi_vp['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_vp['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
        $data_disposisi_vp['id_user'] = $value;
        $data_disposisi_vp['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi_vp['pekerjaan_disposisi_status'] = '8';
        $data_disposisi_vp['id_penanggung_jawab'] = 'n';

        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_vp);
      }
    }
    /* Pekerjaan */
    $pekerjaan_status = $this->input->get_post('pekerjaan_status') + 1;
    // $pekerjaan_status = '9';

    $pekerjaan_id = $this->input->get_post('id_pekerjaan_approve_vp');
    $param['pekerjaan_id'] = $this->input->get_post('id_pekerjaan_approve_vp');
    $data_pekerjaan = $this->M_pekerjaan->getPekerjaan($param);
    // print_r($data_pekerjaan);
    if ($pekerjaan_id) {
      if ($data_pekerjaan['id_klasifikasi_pekerjaan'] == '616b79fa38c26380f49f3b84f088b8f86f9cd176') {
        $data['pekerjaan_status'] = '15';
        $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
      } else {
        $data['pekerjaan_status'] = $pekerjaan_status;
        $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
        // print_r($this->db->last_query());
      }
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
    $data_disposisi['id_pekerjaan'] = $pekerjaan_id;
    $data_disposisi['pekerjaan_disposisi_status'] = $pekerjaan_status;
    // print_r($data_disposisi);
    $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
    /* Disposisi */
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

    foreach ($this->M_pekerjaan->getPekerjaan() as $key => $value) {
      // print_r($value);
      $sql_total = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '5' AND id_pekerjaan = '" . $value['pekerjaan_id'] . "' ");
      $isi_total = $sql_total->row_array();

      $sql = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '" . $value['pekerjaan_status'] . "' AND id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND  id_user = '" . $session['pegawai_nik'] . "'");
      $dataMilik = $sql->row_array();
      // print_r($isi_total);


      $isi['milik'] = ($dataMilik['total'] > 0 || ($value['pic'] == $session['pegawai_nik'] && ($value['pekerjaan_status'] == '0' || $value['pekerjaan_status'] == '-'))) ? 'y' : 'n';

      $isi['pekerjaan_id'] = $value['pekerjaan_id'];
      $isi['pekerjaan_nomor'] = $value['pekerjaan_nomor'];
      $isi['pekerjaan_judul'] = $value['pekerjaan_judul'];
      $isi['pekerjaan_status'] = $value['pekerjaan_status'];
      $isi['pegawai_nama'] = $value['pegawai_nama'];
      $isi['pekerjaan_progress'] = $value['pekerjaan_progress'];
      $isi['total'] = $isi_total['total'];
      $isi['tanggal_akhir'] = $value['tanggal_akhir'];
      $isi['pekerjaan_status'] = $value['pekerjaan_status'];

      array_push($data, $isi);
    }
    echo json_encode($data);
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
  public function prosesApprove()
  {
    $isi = $this->session->userdata();

    /* Pekerjaan */
    $pekerjaan_status = $this->input->get('pekerjaan_status') + 1;

    $pekerjaan_id = $this->input->get('pekerjaan_id');
    if ($pekerjaan_id) {
      $data['pekerjaan_status'] = $pekerjaan_status;

      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
    }

    dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Approve');
    /* Pekerjaan */

    /* User */
    $data_user['pegawai_poscode'] = ($pekerjaan_status == '3') ? 'E53000000' : $isi['pegawai_direct_superior'];

    $user = $this->M_user->getUser($data_user);
    /* User */

    /* Disposisi */
    $data_disposisi['pekerjaan_disposisi_id'] = create_id();
    $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
    $data_disposisi['id_user'] = $user['pegawai_nik'];
    $data_disposisi['id_pekerjaan'] = $pekerjaan_id;
    $data_disposisi['pekerjaan_disposisi_status'] = $pekerjaan_status;

    $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
    /* Disposisi */
  }
  /* Approve */

  /* Reject */
  public function prosesReject()
  {
    $isi = $this->session->userdata();

    /* Pekerjaan */
    $pekerjaan_id = $this->input->get('pekerjaan_id');
    if ($pekerjaan_id) {
      $data['pekerjaan_status'] = '-';
      $data['pekerjaan_note'] = $this->input->get_post('note_reject');

      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reject');
    }
    /* Pekerjaan */

    /* Disposisi */
    $pekerjaan_id = $this->input->get('pekerjaan_id');
    if ($pekerjaan_id) {
      $data_disposisi['is_aktif'] = 'n';

      $this->M_pekerjaan->updatePekerjaanDisposisi($data_disposisi, $pekerjaan_id);
    }
    /* Disposisi */
  }
  /* Reject */

  /*Reject AVP */
  public function prosesRejectAVP()
  {
    $user = $this->session->userdata();

    /* Disposisi */
    $pekerjaan_id = $this->input->get('pekerjaan_id');
    if ($pekerjaan_id) {
      $data_disposisi['is_aktif'] = 'n';
      $user_id = $user['pegawai_nik'];
      $this->M_pekerjaan->updatePekerjaanDisposisi($data_disposisi, $pekerjaan_id, $user_id);
    }
    /* Disposisi */

    // cek apakah semua disposisi dari bukan penanggung jawab (vp) sudah di reject 
    $data['pekerjaan_id'] = $pekerjaan_id;
    $data['id_penanggung_jawab'] = 'n';
    $data['is_aktif'] = 'y';
    $cek_disposisi = $this->M_pekerjaan->getPekerjaanDisposisi($data);

    // jika pekerjaan dari vp direject oleh semua avp ,ubah status pekerjaan ke -1 dari sebelumnya
    if (($cek_disposisi['jumlah'] == '0')) {
      $data_pekerjaan['pekerjaan_status'] = '3';
      $data['pekerjaan_note'] = $this->input->get_post('note_reject');

      $this->M_pekerjaan->updatePekerjaan($data_pekerjaan, $pekerjaan_id);
      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reject');
    }
  }
  /*Reject AVP */

  /*Reject Staf */
  public function prosesRejectStaf()
  {
    $user = $this->session->userdata();
    // print_r($user);
    // cek avp dari staf tersebut;
    $data_user['pegawai_nik'] = $user['pegawai_nik'];
    $cek_avp = $this->M_user->getUser($data_user);

    // direct superior = poscode dari avp tersebut
    $data_avp['pegawai_poscode'] = $cek_avp[0]['pegawai_direct_superior'];
    $data_avp = $this->M_user->getUser($data_avp);
    print_r($data_avp);


    /* Disposisi */
    $pekerjaan_id = $this->input->get('pekerjaan_id');
    print_r($pekerjaan_id);
    // ubah disposisi dari staf dan avp dari staf ke status n 
    if ($pekerjaan_id) {
      $data_disposisi['is_aktif'] = 'n';
      $user_id = $user['pegawai_nik'];
      $this->M_pekerjaan->updatePekerjaanDisposisi($data_disposisi, $pekerjaan_id, $user_id);

      $user_avp = $data_avp['pegawai_nik'];
      print_r($user_avp);
      $this->M_pekerjaan->updatePekerjaanDisposisi($data_disposisi, $pekerjaan_id, $user_avp);
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
      $data['pekerjaan_note'] = $this->input->get_post('note_reject');

      $this->M_pekerjaan->updatePekerjaan($data_pekerjaan, $pekerjaan_id);
      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reject');
    }
    /* Pekerjaan */
  }
  /*Reject Staf */

  /* Disposisi VP */
  public function disposisiVP()
  {
    $isi = $this->session->userdata();

    /* Pekerjaan */
    $pekerjaan_status = '4';

    $pekerjaan_id = $this->input->post('id_pekerjaan_vp');
    if ($pekerjaan_id) {
      $data['pekerjaan_status'] = $pekerjaan_status;

      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Approve VP Cangun');
    }
    /* Pekerjaan */

    /* Disposisi */
    $data_disposisi['pekerjaan_disposisi_id'] = create_id();
    $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
    $data_disposisi['id_user'] = $this->input->post('id_tanggung_jawab_vp');
    $data_disposisi['id_pekerjaan'] = $pekerjaan_id;
    $data_disposisi['pekerjaan_disposisi_status'] = $pekerjaan_status;
    $data_disposisi['id_penanggung_jawab'] = 'y';

    $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);


    // Kirim Email
    $data_user['pegawai_nik'] = $this->input->post('id_tanggung_jawab_vp');
    $data_pekerjaan['pekerjaan_id'] = $pekerjaan_id;

    $user = $this->M_user->getUser($data_user);
    $pekerjaan = $this->M_pekerjaan->getPekerjaan($data_pekerjaan);

    $email_penerima = $user['email_pegawai'];
    $subjek = $pekerjaan['pekerjaan_judul'];
    $pesan = $pekerjaan['pekerjaan_deskripsi'];
    $sendmail = array(
      'email_penerima' => $email_penerima,
      'subjek' => $subjek,
      'content' => $pesan,
    );
    $send = $this->mailer->send($sendmail); // Panggil fungsi send yang ada di librari Mailer
    echo $send['message'];

    // INSERT KE DB EMAIL

    $param_email['email_id'] = create_id();
    $param_email['id_penerima'] = $user['pegawai_nik'];
    $param_email['id_pengirim'] = $isi['pegawai_nik'];
    $param_email['id_pekerjaan'] = $pekerjaan_id;
    // $param_email['pic'] 
    $param_email['id_pekerjaan_disposisi'] = $data_disposisi['pekerjaan_disposisi_id'];
    // $param_email['id_pekerjaan_dokumen'] 
    $param_email['email_subject'] = $subjek;
    $param_email['email_content'] = $pesan;
    $param_email['when_created'] = date('Y-m-d H:i:s');
    $param_email['who_created'] = $isi['pegawai_nama'];

    $this->M_pekerjaan->insertEmail($param_email);

    if ($this->input->post('id_user_vp')) {
      $User = $this->input->post('id_user_vp');
      foreach ($User as $key => $id_user) {
        $data_disposisi['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
        $data_disposisi['id_user'] = $id_user;
        $data_disposisi['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi['pekerjaan_disposisi_status'] = $pekerjaan_status;
        $data_disposisi['id_penanggung_jawab'] = 'n';

        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

        $data_user['pegawai_nik'] = $id_user;
        $data_pekerjaan['pekerjaan_id'] = $pekerjaan_id;

        $user = $this->M_user->getUser($data_user);
        $pekerjaan = $this->M_pekerjaan->getPekerjaan($data_pekerjaan);

        $email_penerima = $user['email_pegawai'];
        $subjek = $pekerjaan['pekerjaan_judul'];
        $pesan = $pekerjaan['pekerjaan_deskripsi'];
        $sendmail = array(
          'email_penerima' => $email_penerima,
          'subjek' => $subjek,
          'content' => $pesan,
        );
        $send = $this->mailer->send($sendmail); // Panggil fungsi send yang ada di librari Mailer
        echo $send['message'];

        // INSERT KE DB EMAIL

        $param_email['email_id'] = create_id();
        $param_email['id_penerima'] = $user['pegawai_nik'];
        $param_email['id_pengirim'] = $isi['pegawai_nik'];
        $param_email['id_pekerjaan'] = $pekerjaan_id;
        // $param_email['pic'] 
        $param_email['id_pekerjaan_disposisi'] = $data_disposisi['pekerjaan_disposisi_id'];
        // $param_email['id_pekerjaan_dokumen'] 
        $param_email['email_subject'] = $subjek;
        $param_email['email_content'] = $pesan;
        $param_email['when_created'] = date('Y-m-d H:i:s');
        $param_email['who_created'] = $isi['pegawai_nama'];

        $this->M_pekerjaan->insertEmail($param_email);
      }
    }
    /* Disposisi */
  }
  /* Disposisi VP */

  /* Disposisi AVP */
  public function disposisiAVP()
  {
    $isi = $this->session->userdata();

    $pekerjaan_status_vp_avp = '4';
    // delete disposisi vp
    $pekerjaan_id = $this->input->post('id_pekerjaan_avp');
    $id_tanggung_jawab = 'n';

    $this->M_pekerjaan->deletePekerjaanDisposisi($pekerjaan_id, null, $id_tanggung_jawab, $pekerjaan_status_vp_avp);

    // insert disposisi vp
    if ($this->input->post('id_user_vp_avp')) {
      $User = $this->input->post('id_user_vp_avp');
      foreach ($User as $key => $id_user) {
        $data_disposisi['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
        $data_disposisi['id_user'] = $id_user;
        $data_disposisi['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi['pekerjaan_disposisi_status'] = $pekerjaan_status_vp_avp;
        $data_disposisi['id_penanggung_jawab'] = 'n';

        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

        $data_user['pegawai_nik'] = $id_user;
        $data_pekerjaan['pekerjaan_id'] = $pekerjaan_id;

        $user = $this->M_user->getUser($data_user);
        $pekerjaan = $this->M_pekerjaan->getPekerjaan($data_pekerjaan);

        $email_penerima = $user['email_pegawai'];
        $subjek = $pekerjaan['pekerjaan_judul'];
        $pesan = $pekerjaan['pekerjaan_deskripsi'];
        $sendmail = array(
          'email_penerima' => $email_penerima,
          'subjek' => $subjek,
          'content' => $pesan,
        );
        $send = $this->mailer->send($sendmail); // Panggil fungsi send yang ada di librari Mailer
        echo $send['message'];

        // INSERT KE DB EMAIL

        $param_email['email_id'] = create_id();
        $param_email['id_penerima'] = $user['pegawai_nik'];
        $param_email['id_pengirim'] = $isi['pegawai_nik'];
        $param_email['id_pekerjaan'] = $pekerjaan_id;
        // $param_email['pic'] 
        $param_email['id_pekerjaan_disposisi'] = $data_disposisi['pekerjaan_disposisi_id'];
        // $param_email['id_pekerjaan_dokumen'] 
        $param_email['email_subject'] = $subjek;
        $param_email['email_content'] = $pesan;
        $param_email['when_created'] = date('Y-m-d H:i:s');
        $param_email['who_created'] = $isi['pegawai_nama'];

        $this->M_pekerjaan->insertEmail($param_email);
      }
    }

    /* Pekerjaan */
    $pekerjaan_status = '5';

    $pekerjaan_id = $this->input->post('id_pekerjaan_avp');
    if ($pekerjaan_id) {
      if ($this->input->post('id_klasifikasi_pekerjaan_avp')) {
        // if ($this->input->get_post('id_klasifikasi_pekerjaan_avp') == '616b79fa38c26380f49f3b84f088b8f86f9cd176') {
        // $param['pekerjaan_status'] = '15';
        // $this->M_pekerjaan->updatePekerjaan($param, $pekerjaan_id);
        // } else {
        // echo "tesnon";
        $sql_klasifikasi = $this->db->query("SELECT klasifikasi_pekerjaan_nama FROM global.global_klasifikasi_pekerjaan WHERE klasifikasi_pekerjaan_id = '" . $this->input->post('id_klasifikasi_pekerjaan_avp') . "'");
        $isi_klasifikasi = $sql_klasifikasi->row_array();

        $sql_nomor = $this->db->query("SELECT pekerjaan_nomor FROM dec.dec_pekerjaan WHERE pekerjaan_nomor LIKE '%" . date('Y') . "%'");
        // echo $this->db->last_query();
        $isi_nomor = $sql_nomor->row_array();
        $nomor = explode('-', $isi_nomor['pekerjaan_nomor']);

        $data['pekerjaan_nomor'] = ($nomor[0] + 1) . '-' . $isi_klasifikasi['klasifikasi_pekerjaan_nama'] . '-' . date('Y');
        $data['pekerjaan_status'] = $pekerjaan_status;
        $data['id_klasifikasi_pekerjaan'] = $this->input->post('id_klasifikasi_pekerjaan_avp');
        $data['pekerjaan_waktu_akhir'] = $this->input->post('pekerjaan_waktu_akhir_avp');
        $data['pekerjaan_judul'] = $this->input->post('pekerjaan_judul');

        $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
        echo $this->db->last_query();

        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Disposisikan Oleh AVP Cangun');
      }
      // }
    } else {
      $data['pekerjaan_status'] = $pekerjaan_status;
      // $data['id_klasifikasi_pekerjaan'] = $this->input->post('id_klasifikasi_pekerjaan_avp');
      // $data['pekerjaan_waktu_akhir'] = $this->input->post('pekerjaan_waktu_akhir_avp');

      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Disposisikan Oleh AVP Cangun');
    }
    /* Pekerjaan */

    /* Disposisi */

    if ($this->input->post('id_user_avp')) {
      $data_disposisi['pekerjaan_disposisi_id'] = create_id();
      $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
      $data_disposisi['id_user'] = $this->input->post('id_user_avp');
      $data_disposisi['id_pekerjaan'] = $pekerjaan_id;
      $data_disposisi['pekerjaan_disposisi_status'] = $pekerjaan_status;

      $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

      $data_user['pegawai_nik'] = $data_disposisi['id_user'];
      $data_pekerjaan['pekerjaan_id'] = $pekerjaan_id;

      $user = $this->M_user->getUser($data_user);
      $pekerjaan = $this->M_pekerjaan->getPekerjaan($data_pekerjaan);

      $email_penerima = $user['email_pegawai'];
      $subjek = $pekerjaan['pekerjaan_judul'];
      $pesan = $pekerjaan['pekerjaan_deskripsi'];
      $sendmail = array(
        'email_penerima' => $email_penerima,
        'subjek' => $subjek,
        'content' => $pesan,
      );
      $send = $this->mailer->send($sendmail); // Panggil fungsi send yang ada di librari Mailer
      echo $send['message'];

      // INSERT KE DB EMAIL

      $param_email['email_id'] = create_id();
      $param_email['id_penerima'] = $user['pegawai_nik'];
      $param_email['id_pengirim'] = $isi['pegawai_nik'];
      $param_email['id_pekerjaan'] = $pekerjaan_id;
      // $param_email['pic'] 
      $param_email['id_pekerjaan_disposisi'] = $data_disposisi['pekerjaan_disposisi_id'];
      // $param_email['id_pekerjaan_dokumen'] 
      $param_email['email_subject'] = $subjek;
      $param_email['email_content'] = $pesan;
      $param_email['when_created'] = date('Y-m-d H:i:s');
      $param_email['who_created'] = $isi['pegawai_nama'];

      $this->M_pekerjaan->insertEmail($param_email);
    }
    if ($this->input->post('id_user_avp_2')) {
      $data_disposisi['pekerjaan_disposisi_id'] = create_id();
      $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
      $data_disposisi['id_user'] = $this->input->post('id_user_avp_2');
      $data_disposisi['id_pekerjaan'] = $pekerjaan_id;
      $data_disposisi['pekerjaan_disposisi_status'] = $pekerjaan_status;

      $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
    }
    /* Disposisi */
  }
  /* Disposisi AVP */

  /* Progress Pekerjaan */
  public function updateProgressPekerjaan()
  {
    $pekerjaan_id = $this->input->post('id_pekerjaan_progress');
    if ($pekerjaan_id) {
      $data['pekerjaan_progress'] = $this->input->post('pekerjaan_progress');
      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Isi Progress Oleh Staf Cangun');
    }
  }
  /* Progress Pekerjaan */

  /* Approve Pekerjaan Berjalan */
  public function prosesApproveBerjalan()
  {
    $isi = $this->session->userdata();

    /* isi disposisi */
    if ($this->input->get_post('id_user_staf')) {
      $user = $this->input->get_post('id_user_staf');
      // print_r($user);
      foreach ($user as $key => $value) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = $value;
        $data_disposisi_doc['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
        $data_disposisi_doc['pekerjaan_disposisi_status'] = '8';
        $data_disposisi_doc['id_penanggung_jawab'] = 'n';

        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);

        $data_user['pegawai_nik'] = $data_disposisi_doc['id_user'];
        $data_pekerjaan['pekerjaan_id'] = $data_disposisi_doc['id_pekerjaan'];

        $user = $this->M_user->getUser($data_user);
        $pekerjaan = $this->M_pekerjaan->getPekerjaan($data_pekerjaan);

        $email_penerima = $user['email_pegawai'];
        $subjek = $pekerjaan['pekerjaan_judul'];
        $pesan = $pekerjaan['pekerjaan_deskripsi'];
        $sendmail = array(
          'email_penerima' => $email_penerima,
          'subjek' => $subjek,
          'content' => $pesan,
        );
        $send = $this->mailer->send($sendmail); // Panggil fungsi send yang ada di librari Mailer
        echo $send['message'];

        // INSERT KE DB EMAIL

        $param_email['email_id'] = create_id();
        $param_email['id_penerima'] = $user['pegawai_nik'];
        $param_email['id_pengirim'] = $isi['pegawai_nik'];
        $param_email['id_pekerjaan'] = $data_disposisi_doc['id_pekerjaan'];
        // $param_email['pic'] 
        $param_email['id_pekerjaan_disposisi'] = $data_disposisi_doc['pekerjaan_disposisi_id'];
        // $param_email['id_pekerjaan_dokumen'] 
        $param_email['email_subject'] = $subjek;
        $param_email['email_content'] = $pesan;
        $param_email['when_created'] = date('Y-m-d H:i:s');
        $param_email['who_created'] = $isi['pegawai_nama'];

        $this->M_pekerjaan->insertEmail($param_email);
        // print_r($data_disposisi_doc);
      }
    }
    /* isi disposisi */

    /* Pekerjaan */
    $pekerjaan_status = $this->input->get('pekerjaan_status') + 1;
    // $pekerjaan_status = '9';

    $pekerjaan_id = $this->input->get('pekerjaan_id');
    if ($pekerjaan_id) {
      $data['pekerjaan_status'] = $pekerjaan_status;

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
    $data_disposisi['id_pekerjaan'] = $pekerjaan_id;
    $data_disposisi['pekerjaan_disposisi_status'] = $pekerjaan_status;

    $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

    $data_user['pegawai_nik'] = $data_disposisi['id_user'];
    $data_pekerjaan['pekerjaan_id'] = $pekerjaan_id;

    $user = $this->M_user->getUser($data_user);
    $pekerjaan = $this->M_pekerjaan->getPekerjaan($data_pekerjaan);

    $email_penerima = $user['email_pegawai'];
    $subjek = $pekerjaan['pekerjaan_judul'];
    $pesan = $pekerjaan['pekerjaan_deskripsi'];
    $sendmail = array(
      'email_penerima' => $email_penerima,
      'subjek' => $subjek,
      'content' => $pesan,
    );
    $send = $this->mailer->send($sendmail); // Panggil fungsi send yang ada di librari Mailer
    echo $send['message'];

    // INSERT KE DB EMAIL

    $param_email['email_id'] = create_id();
    $param_email['id_penerima'] = $user['pegawai_nik'];
    $param_email['id_pengirim'] = $isi['pegawai_nik'];
    $param_email['id_pekerjaan'] = $pekerjaan_id;
    // $param_email['pic'] 
    $param_email['id_pekerjaan_disposisi'] = $data_disposisi['pekerjaan_disposisi_id'];
    // $param_email['id_pekerjaan_dokumen'] 
    $param_email['email_subject'] = $subjek;
    $param_email['email_content'] = $pesan;
    $param_email['when_created'] = date('Y-m-d H:i:s');
    $param_email['who_created'] = $isi['pegawai_nama'];

    $this->M_pekerjaan->insertEmail($param_email);
    /* Disposisi */
  }
  /* Approve Pekerjaan Berjalan */

  /* Approve Pekerjaan Berjalan Revisi  */
  public function prosesApproveBerjalanRevisi()
  {
    $isi = $this->session->userdata();

    /* isi disposisi */
    if ($this->input->get_post('id_user_staf')) {
      $user = $this->input->get_post('id_user_staf');
      // print_r($user);
      foreach ($user as $key => $value) {
        $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
        $data_disposisi_doc['id_user'] = $value;
        $data_disposisi_doc['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
        $data_disposisi_doc['pekerjaan_disposisi_status'] = '8';
        $data_disposisi_doc['id_penanggung_jawab'] = 'n';

        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
        // print_r($data_disposisi_doc);

        $data_user['pegawai_nik'] = $data_disposisi_doc['id_user'];
        $data_pekerjaan['pekerjaan_id'] = $data_disposisi_doc['id_pekerjaan'];

        $user = $this->M_user->getUser($data_user);
        $pekerjaan = $this->M_pekerjaan->getPekerjaan($data_pekerjaan);

        $email_penerima = $user['email_pegawai'];
        $subjek = $pekerjaan['pekerjaan_judul'];
        $pesan = $pekerjaan['pekerjaan_deskripsi'];
        $sendmail = array(
          'email_penerima' => $email_penerima,
          'subjek' => $subjek,
          'content' => $pesan,
        );
        $send = $this->mailer->send($sendmail); // Panggil fungsi send yang ada di librari Mailer
        echo $send['message'];

        // INSERT KE DB EMAIL

        $param_email['email_id'] = create_id();
        $param_email['id_penerima'] = $user['pegawai_nik'];
        $param_email['id_pengirim'] = $isi['pegawai_nik'];
        $param_email['id_pekerjaan'] = $data_disposisi_doc['id_pekerjaan'];
        // $param_email['pic'] 
        $param_email['id_pekerjaan_disposisi'] = $data_disposisi_doc['pekerjaan_disposisi_id'];
        // $param_email['id_pekerjaan_dokumen'] 
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
    $data_disposisi['id_pekerjaan'] = $pekerjaan_id;
    $data_disposisi['pekerjaan_disposisi_status'] = '9';

    $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

    $data_user['pegawai_nik'] = $data_disposisi['id_user'];
    $data_pekerjaan['pekerjaan_id'] = $pekerjaan_id;

    $user = $this->M_user->getUser($data_user);
    $pekerjaan = $this->M_pekerjaan->getPekerjaan($data_pekerjaan);

    $email_penerima = $user['email_pegawai'];
    $subjek = $pekerjaan['pekerjaan_judul'];
    $pesan = $pekerjaan['pekerjaan_deskripsi'];
    $sendmail = array(
      'email_penerima' => $email_penerima,
      'subjek' => $subjek,
      'content' => $pesan,
    );
    $send = $this->mailer->send($sendmail); // Panggil fungsi send yang ada di librari Mailer
    echo $send['message'];

    // INSERT KE DB EMAIL

    $param_email['email_id'] = create_id();
    $param_email['id_penerima'] = $user['pegawai_nik'];
    $param_email['id_pengirim'] = $isi['pegawai_nik'];
    $param_email['id_pekerjaan'] = $pekerjaan_id;
    // $param_email['pic'] 
    $param_email['id_pekerjaan_disposisi'] = $data_disposisi['pekerjaan_disposisi_id'];
    // $param_email['id_pekerjaan_dokumen'] 
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
    $isi = $this->session->userdata();

    /* Pekerjaan */
    $pekerjaan_id = $this->input->get('pekerjaan_id');
    if ($pekerjaan_id) {
      $data['pekerjaan_status'] = '5';

      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reject Oleh Cangun');
    }
    /* Pekerjaan */
  }
  /* Reject Pekerjaan Berjalan */

  /* Approve Pekerjaan IFA */
  public function prosesApproveIFA()
  {
    $isi = $this->session->userdata();

    /* Pekerjaan */
    $pekerjaan_status = $this->input->get('pekerjaan_status') + 1;

    $pekerjaan_id = $this->input->get('pekerjaan_id');
    if ($pekerjaan_id) {
      $data['pekerjaan_status'] = $pekerjaan_status;

      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Approve Oleh PIC');
    }
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
    foreach ($isi_disposisi as $key => $value) {
      $data_disposisi['pekerjaan_disposisi_id'] = create_id();
      $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
      $data_disposisi['id_user'] = $value['id_user'];
      $data_disposisi['id_pekerjaan'] = $pekerjaan_id;;
      $data_disposisi['pekerjaan_disposisi_status'] = $pekerjaan_status;

      $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

      $data_user['pegawai_nik'] = $data_disposisi['id_user'];
      $data_pekerjaan['pekerjaan_id'] = $pekerjaan_id;

      $user = $this->M_user->getUser($data_user);
      $pekerjaan = $this->M_pekerjaan->getPekerjaan($data_pekerjaan);

      $email_penerima = $user['email_pegawai'];
      $subjek = $pekerjaan['pekerjaan_judul'];
      $pesan = $pekerjaan['pekerjaan_deskripsi'];
      $sendmail = array(
        'email_penerima' => $email_penerima,
        'subjek' => $subjek,
        'content' => $pesan,
      );
      $send = $this->mailer->send($sendmail); // Panggil fungsi send yang ada di librari Mailer
      echo $send['message'];

      // INSERT KE DB EMAIL

      $param_email['email_id'] = create_id();
      $param_email['id_penerima'] = $user['pegawai_nik'];
      $param_email['id_pengirim'] = $isi['pegawai_nik'];
      $param_email['id_pekerjaan'] = $pekerjaan_id;
      // $param_email['pic'] 
      $param_email['id_pekerjaan_disposisi'] = $data_disposisi['pekerjaan_disposisi_id'];
      // $param_email['id_pekerjaan_dokumen'] 
      $param_email['email_subject'] = $subjek;
      $param_email['email_content'] = $pesan;
      $param_email['when_created'] = date('Y-m-d H:i:s');
      $param_email['who_created'] = $isi['pegawai_nama'];

      $this->M_pekerjaan->insertEmail($param_email);
    }
    /* Disposisi */
  }
  /* Approve Pekerjaan IFA */

  /* Reject Pekerjaan IFA */
  public function prosesRejectIFA()
  {
    $isi = $this->session->userdata();

    /* Pekerjaan */
    $pekerjaan_id = $this->input->get('pekerjaan_id');
    if ($pekerjaan_id) {
      $data['pekerjaan_status'] = '5';

      $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reject Oleh PIC');
    }
    /* Pekerjaan */
  }
  /* Reject Pekerjaan IFA */

  /* Aksi Approve / Reject Dokumen */
  public function simpanAksi()
  {
    $isi = $this->session->userdata();

    if (isset($_FILES['pekerjaan_dokumen_file'])) {
      $temp = "./document/";
      if (!file_exists($temp)) mkdir($temp);

      $fileupload      = $_FILES['pekerjaan_dokumen_file']['tmp_name'];
      $ImageName       = $_FILES['pekerjaan_dokumen_file']['name'];
      $ImageType       = $_FILES['pekerjaan_dokumen_file']['type'];

      if (!empty($fileupload)) {
        $acak           = rand(11111111, 99999999);
        $ImageExt       = substr($ImageName, strrpos($ImageName, '.'));
        $ImageExt       = str_replace('.', '', $ImageExt); // Extension
        $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
        $NewImageName   = str_replace(' ', '', $_POST['pekerjaan_dokumen_id'] . '_' . date('ymdhis') . '_' . $acak . '.' . $ImageExt);

        move_uploaded_file($_FILES["pekerjaan_dokumen_file"]["tmp_name"], $temp . $NewImageName); // Menyimpan file

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

    $status_dokumen = ($this->input->get_post('pekerjaan_dokumen_status') == 'n') ? '0' : '3';
    // print_r($status_dokumen);

    /* Insert */
    if ($NewImageName == null) {
      $data['pekerjaan_dokumen_id_temp'] = $this->input->post('pekerjaan_dokumen_id');
      $data['pekerjaan_dokumen_id'] = create_id();
      $data['pekerjaan_dokumen_status'] = $status_dokumen;
      $data['pekerjaan_dokumen_keterangan'] = $this->input->post('pekerjaan_dokumen_keterangan');

      $this->M_pekerjaan->simpanAksiSama($data);
    } else {
      $data['pekerjaan_dokumen_id_temp'] = $this->input->post('pekerjaan_dokumen_id');
      $data['pekerjaan_dokumen_id'] = create_id();
      $data['pekerjaan_dokumen_file'] = $NewImageName;
      $data['pekerjaan_dokumen_status'] = $status_dokumen;
      $data['pekerjaan_dokumen_keterangan'] = $this->input->post('pekerjaan_dokumen_keterangan');

      $this->M_pekerjaan->simpanAksi($data);
    }
    // print_r($data);
    /* Insert */

    if ($data['pekerjaan_dokumen_id_temp']) {
      $data_edit['is_lama'] = 'y';
      $this->M_pekerjaan->editAksi($data_edit, $data['pekerjaan_dokumen_id_temp']);
    }
  }
  /* Aksi Approve / Reject Dokumen */

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
        $acak           = rand(11111111, 99999999);
        $ImageExt       = substr($ImageName, strrpos($ImageName, '.'));
        $ImageExt       = str_replace('.', '', $ImageExt); // Extension
        $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
        $NewImageName   = str_replace(' ', '', $_POST['pekerjaan_dokumen_id'] . '_' . date('ymdhis') . '_' . $acak . '.' . $ImageExt);

        move_uploaded_file($_FILES["pekerjaan_dokumen_file"]["tmp_name"], $temp . $NewImageName); // Menyimpan file

        $note = "Data Berhasil Disimpan";
      } else {
        $note = "Data Gagal Disimpan";
      }
      echo $note;
    } else {
      $NewImageName = null;
    }

    // cek dokumen statusnya dari input status

    $dokumen_status = ($this->input->get_post('pekerjaan_dokumen_status') == 'n') ? '0' : '4';

    /* Insert */
    if ($NewImageName == null) {
      $data['pekerjaan_dokumen_id_temp'] = $this->input->post('pekerjaan_dokumen_id');
      $data['pekerjaan_dokumen_id'] = create_id();
      // $data['pekerjaan_dokumen_status'] = $this->input->post('pekerjaan_dokumen_status');
      $data['pekerjaan_dokumen_status'] = $dokumen_status;
      $data['pekerjaan_dokumen_keterangan'] = $this->input->post('pekerjaan_dokumen_keterangan');

      $this->M_pekerjaan->simpanAksiSama($data);
    } else {
      $data['pekerjaan_dokumen_id_temp'] = $this->input->post('pekerjaan_dokumen_id');
      $data['pekerjaan_dokumen_id'] = create_id();
      $data['pekerjaan_dokumen_file'] = $NewImageName;
      $data['pekerjaan_dokumen_status'] = $dokumen_status;
      // $data['pekerjaan_dokumen_status'] = $this->input->post('pekerjaan_dokumen_status');
      $data['pekerjaan_dokumen_keterangan'] = $this->input->post('pekerjaan_dokumen_keterangan');

      $this->M_pekerjaan->simpanAksi($data);
    }
    /* Insert */

    // cek apakah direvisi 
    if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
      // ambil urutan revisi sebelumnya dari dokumen di pekerjaan tersebut
      $data_revisi = $this->M_pekerjaan->getRevisiNomor($data);
      print_r($this->db->last_query());
      print_r($data_revisi);

      $data_revisi_isi['pekerjaan_dokumen_revisi'] = $data_revisi['pekerjaan_dokumen_revisi'] + 1;

      print_r($data_revisi_isi);
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
        $acak           = rand(11111111, 99999999);
        $ImageExt       = substr($ImageName, strrpos($ImageName, '.'));
        $ImageExt       = str_replace('.', '', $ImageExt); // Extension
        $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
        $NewImageName   = str_replace(' ', '', $_POST['pekerjaan_dokumen_id'] . '_' . date('ymdhis') . '_' . $acak . '.' . $ImageExt);

        move_uploaded_file($_FILES["pekerjaan_dokumen_file"]["tmp_name"], $temp . $NewImageName); // Menyimpan file

        $note = "Data Berhasil Disimpan";
      } else {
        $note = "Data Gagal Disimpan";
      }
      echo $note;
    } else {
      $NewImageName = null;
    }

    // cek dokumen statusnya dari input status

    $dokumen_status = ($this->input->get_post('pekerjaan_dokumen_status') == 'n') ? '0' : '5';

    /* Insert */
    if ($NewImageName == null) {
      $data['pekerjaan_dokumen_id_temp'] = $this->input->post('pekerjaan_dokumen_id');
      $data['pekerjaan_dokumen_id'] = create_id();
      // $data['pekerjaan_dokumen_status'] = $this->input->post('pekerjaan_dokumen_status');
      $data['pekerjaan_dokumen_status'] = $dokumen_status;
      $data['pekerjaan_dokumen_keterangan'] = $this->input->post('pekerjaan_dokumen_keterangan');

      $this->M_pekerjaan->simpanAksiSama($data);
    } else {
      $data['pekerjaan_dokumen_id_temp'] = $this->input->post('pekerjaan_dokumen_id');
      $data['pekerjaan_dokumen_id'] = create_id();
      $data['pekerjaan_dokumen_file'] = $NewImageName;
      $data['pekerjaan_dokumen_status'] = $dokumen_status;
      // $data['pekerjaan_dokumen_status'] = $this->input->post('pekerjaan_dokumen_status');
      $data['pekerjaan_dokumen_keterangan'] = $this->input->post('pekerjaan_dokumen_keterangan');

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

    $param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');

    $dokumen = explode('~', $this->input->get_post('pekerjaan_dokumen_file'));
    $format  = explode('.', $dokumen[0]);

    $data['pekerjaan'] = $this->M_pekerjaan->getPekerjaan($param);

    $sql_template = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON a.id_pekerjaan_template = b.pekerjaan_template_id WHERE pekerjaan_dokumen_id = '" . $dokumen[1] . "'");
    $isi_template = $sql_template->row_array();
    $data['template'] = $isi_template;

    $html =    $this->load->view('project/pekerjaan_cover', $data, true);
    $filename = 'cover_' . $dokumen[0];

    // $cover = $this->pdfgenerator->generate($html, $filename, true, 'A4', 'portrait');
    $cover = $this->pdfgenerator->save($html, $filename, 'A4', 'portrait');


    $cover_download = base_url() . 'document/cover_' . $dokumen[0];
    $data_download = base_url() . 'document/' . $dokumen[0];

    $data1['cover_download'] = 'cover_' . $dokumen[0];
    $data1['data_download'] = $dokumen[0];

    $this->load->view('project/combine', $data1);
  }

  /* DOWNLOAD */







  public function getUserList()
  {
    $isi = $this->session->userdata();
    // print_r($isi);

    $list['results'] = array();

    $param['pegawai_nama'] = $this->input->get('pegawai_nama');
    $param['pegawai_poscode'] = $isi['pegawai_poscode'];
    // $this->M_pekerjaan->getUserList($param);
    // echo  $this->db->last_query();
    foreach ($this->M_pekerjaan->getUserList($param) as $key => $value) {
      array_push($list['results'], [
        'id' => $value['pegawai_nik'],
        'text' => $value['pegawai_nama'] . ' - ' . $value['pegawai_postitle'],
      ]);
    }

    echo json_encode($list);
  }

  /* user list vp */
  public function getUserListVP()
  {
    $isi = $this->session->userdata();

    $list['results'] = array();

    $param['pegawai_nama'] = $this->input->get('pegawai_nama');
    $param['pegawai_poscode'] = $isi['pegawai_poscode'];
    foreach ($this->M_pekerjaan->getUserListVP($param) as $key => $value) {
      // echo $this->db->last_query();
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
    // print_r($isi);

    $list['results'] = array();

    $param['pegawai_nama'] = $this->input->get('pegawai_nama');
    $param['pegawai_poscode'] = $isi['pegawai_poscode'];
    $param['bagian_nama'] = $isi['pegawai_nama_bag'];
    // print_r($param);
    // $this->M_pekerjaan->getUserListAVP($param);
    // echo $this->db->last_query();
    // $param['pegawai_nik'] = $isi['pegawai_nik'];/
    foreach ($this->M_pekerjaan->getUserListAVP($param) as $key => $value) {

      array_push($list['results'], [
        'id' => $value['pegawai_nik'],
        'text' => $value['pegawai_nama'] . ' - ' . $value['pegawai_postitle'],
      ]);
    }

    // echo $this->db->last_query();
    echo json_encode($list);
  }
  /* user list vp */

  /* get user staf */
  public function getUserStaf()
  {
    $list['results'] = array();

    $param['pegawai_nama'] = $this->input->get('pegawai_nama');
    // $param['pegawai_poscode'] = $isi['pegawai_poscode'];
    // $param['pegawai_nik'] = $isi['pegawai_nik'];/
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
    // $param['pegawai_nik'] = $user['pegawai_nik'];
    $param['id_tanggung_jawab'] = 'n';
    $data = $this->M_pekerjaan->getVPAVP($param);
    // echo $this->db->last_query();

    echo json_encode($data);
  }
  /*GET VP AVP */

  public function getUserStafVP()
  {
    $user = $this->session->userdata();

    $param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
    // $param['pegawai_nik'] = $user['pegawai_nik'];
    // $param['id_tanggung_jawab'] = 'n';
    $data = $this->M_pekerjaan->getUserStafVP($param);
    // echo $this->db->last_query();

    echo json_encode($data);
  }

  /*GET VP AVP Penanggung Jawab */
  public function getVPAVPTJ()
  {
    $user = $this->session->userdata();

    $param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
    $param['pegawai_nik'] = $user['pegawai_nik'];
    // print_r($param);
    // $param['id_tanggung_jawab'] = 'n';
    $data = $this->M_pekerjaan->getVPAVP($param);
    // echo $this->db->last_query();

    echo json_encode($data);
  }
  /*GET VP AVP Penanggung Jawab TJ*/




  public function getPekerjaan()
  {
    $isi = $this->session->userdata();

    $param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
    $param['pegawai_nik'] = $isi['pegawai_nik'];

    echo json_encode($this->M_pekerjaan->getPekerjaanDetail($param));
    // print_r($this->db->last_query());
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
    // $param['pekerjaan_dokumen_status'] = $this->input->get_post('pekerjaan_dokumen_status');
    $param['pekerjaan_dokumen_status!='] = 'y';
    $param['is_lama!='] = 'y';
    $data = $this->M_pekerjaan->getAsetDocumentUsulanBaru($param);
    // print_r($param);
    // echo $this->db->last_query();
    echo json_encode($data);
  }

  public function getAsetDocument()
  {
    $param = array();

    if ($this->input->get('id_pekerjaan')) $param['id_pekerjaan'] = $this->input->get('id_pekerjaan');
    // $param['pekerjaan_dokumen_status'] = $this->input->get_post('pekerjaan_dokumen_status');
    $param['pekerjaan_dokumen_status!='] = 'y';
    $param['is_lama!='] = 'y';
    $data = $this->M_pekerjaan->getAsetDocument($param);
    // print_r($param);
    // echo $this->db->last_query();
    echo json_encode($data);
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
    // $param['']
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

    $user = $this->session->userdata();

    $data['pekerjaan_dokumen_id'] = create_id();
    $data['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
    $data['pekerjaan_dokumen_nama'] = $this->input->get_post('pekerjaan_dokumen_nama');
    $data['id_pekerjaan_template'] = $this->input->get_post('pekerjaan_template_nama');
    $data['pekerjaan_dokumen_file'] = $this->input->get_post('savedFileName');
    // if($this->input->get_post('pekerjaan_dokumen_status')){
    // $data['pekerjaan_dokumen_status'] = $this->input->get_post('pekerjaan_input_status');
    // }else{
    $data['pekerjaan_dokumen_status'] = '2';
    // }
    $data['who_create'] = $user['pegawai_nama'];
    $data['is_lama'] = 'n';
    $data['pekerjaan_dokumen_awal'] = 'n';

    $this->M_pekerjaan->insertPekerjaanDokumen($data);
  }

  public function insertAsetDocumentIFC()
  {

    $user = $this->session->userdata();

    $data['pekerjaan_dokumen_id'] = create_id();
    $data['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
    $data['pekerjaan_dokumen_nama'] = $this->input->get_post('pekerjaan_dokumen_nama');
    $data['id_pekerjaan_template'] = $this->input->get_post('pekerjaan_template_nama');
    $data['pekerjaan_dokumen_file'] = $this->input->get_post('savedFileName');
    // if($this->input->get_post('pekerjaan_dokumen_status')){
    // $data['pekerjaan_dokumen_status'] = $this->input->get_post('pekerjaan_input_status');
    // }else{
    $data['pekerjaan_dokumen_status'] = '5';
    // }
    $data['who_create'] = $user['pegawai_nama'];
    $data['is_lama'] = 'n';
    $data['pekerjaan_dokumen_awal'] = 'n';

    $this->M_pekerjaan->insertPekerjaanDokumen($data);
  }

  public function updateAsetDocument()
  {

    $user = $this->session->userdata();

    $id = $this->input->get_post('pekerjaan_dokumen_id');
    // $data['pekerjaan_dokumen_id'] = create_id();
    $data['id_pekerjaan'] = $this->input->get_post('id_pekerjaan');
    $data['pekerjaan_dokumen_nama'] = $this->input->get_post('pekerjaan_dokumen_nama');
    $data['id_pekerjaan_template'] = $this->input->get_post('pekerjaan_template_nama');
    if ($this->input->get_post('savedFileName') != '') {
      $data['pekerjaan_dokumen_file'] = $this->input->get_post('savedFileName');
    }
    // if($this->input->get_post('pekerjaan_dokumen_status')){
    // $data['pekerjaan_dokumen_status'] = $this->input->get_post('pekerjaan_input_status');
    // }else{
    $data['pekerjaan_dokumen_status'] = '2';
    // }
    $data['who_create'] = $user['pegawai_nama'];
    $data['is_lama'] = 'n';
    $data['pekerjaan_dokumen_awal'] = 'n';
    print_r($data);

    $this->M_pekerjaan->updatePekerjaanDokumen($data, $id);
  }

  public function updateAsetDocumentIFC()
  {

    $user = $this->session->userdata();

    $id = $this->input->get_post('pekerjaan_dokumen_id');
    // $data['pekerjaan_dokumen_id'] = create_id();
    $data['id_pekerjaan'] = $this->input->get_post('id_pekerjaan');
    $data['pekerjaan_dokumen_nama'] = $this->input->get_post('pekerjaan_dokumen_nama');
    $data['id_pekerjaan_template'] = $this->input->get_post('pekerjaan_template_nama');
    if ($this->input->get_post('savedFileName') != '') {
      $data['pekerjaan_dokumen_file'] = $this->input->get_post('savedFileName');
    }
    // if($this->input->get_post('pekerjaan_dokumen_status')){
    // $data['pekerjaan_dokumen_status'] = $this->input->get_post('pekerjaan_input_status');
    // }else{
    $data['pekerjaan_dokumen_status'] = '5';
    // }
    $data['who_create'] = $user['pegawai_nama'];
    $data['is_lama'] = 'n';
    $data['pekerjaan_dokumen_awal'] = 'n';
    print_r($data);

    $this->M_pekerjaan->updatePekerjaanDokumen($data, $id);
  }


  public function insertAsetDocumentDetail()
  {
    $data['pekerjaan_dokumen_id'] = create_id();
    $data['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
    $data['pekerjaan_dokumen_nama'] = $this->input->get_post('pekerjaan_dokumen_nama');
    $data['pekerjaan_dokumen_file'] = $this->input->get_post('savedFileName');

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
        $data['id_user'] = $id_user;
        $data['id_pekerjaan'] = $this->input->get_post('id_pekerjaan_approve_avp');
        $data['pekerjaan_disposisi_status'] = 'AVP';
        // $data['id_penanggung_jawab'] = 
        // print_r($data);
        $this->M_pekerjaan->insertPekerjaanDisposisi($data);
      }

      $id = $this->input->get_post('id_pekerjaan_approve_avp');
      // $
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
        $param['id_user'] = $id_usr;
        $param['id_pekerjaan'] = $this->input->get_post('id_pekerjaan');
        $param['pekerjaan_disposisi_status'] = 'AVP';
        $param['id_penanggung_jawab'] = $this->input->get_post('usr_id_pj');

        $this->M_pekerjaan->insertPekerjaanDisposisi($param, 'AVP');
      }

      $param1['pekerjaan_dokumen_id'] = create_id();
      $param1['id_pekerjaan'] = $this->input->get_post('id_pekerjaan');
      $param1['id_pekerjaan_disposisi'] = $param['pekerjaan_disposisi_id'];
      $param1['pekerjaan_dokumen_nama'] = $this->input->get_post('pekerjaan_dokumen_nama');
      $param1['pekerjaan_dokumen_keterangan'] = $this->input->get_post('pekerjaan_dokumen_keterangan');
      $param1['pekerjaan_dokumen_status'] = $this->input->get_post('pekerjaan_dokumen_status_nama');
      $param1['pekerjaan_dokumen_file'] = $this->input->get_post('savedFileName');
      $param1['who_create'] = $user['pegawai_nama'];

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
    $param['id_pekerjaan'] = $this->input->get_post('id_pekerjaan');
    $param['pekerjaan_dokumen_nama'] = $this->input->get_post('pekerjaan_dokumen_nama');
    // $param['pekerjaan_dokumen_departemen'] = $this->input->get_post('pekerjaan_dokumen_departemen');
    $param['pekerjaan_dokumen_keterangan'] = $this->input->get_post('pekerjaan_dokumen_keterangan');
    $param['pekerjaan_dokumen_status'] = $this->input->get_post('pekerjaan_dokumen_status_nama');
    $param['pekerjaan_dokumen_file'] = $this->input->get_post('savedFileName');
    // $param['id_pekerjaan_disposisi'] = $this->input->get_post('id_pekerjaan_disposisi');
    // $param['id_penanggung_jawab'] = $this->input->get_post('id_penanggung_jawab');
    $param['who_create'] = $user['pegawai_nama'];
    // print_r($param);

    $this->M_pekerjaan->insertPekerjaanDokumen($param);
    // echo $this->db->last_query();


    // update dokumen lama 
    $id = $this->input->get_post('pekerjaan_dokumen_id');
    $param1['is_lama'] = 'y';
    // print_r($param1);

    $this->M_pekerjaan->updatePekerjaanDokumen($param1, $id);
    // echo $this->db->last_query();
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
    $param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
    $data = $this->M_pekerjaan->cekRevisi($param);
    echo json_encode($data);
  }
  /* LAIN */
}
