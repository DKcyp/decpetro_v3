<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pekerjaan_langsung extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('template');
        $sesi = $this->session->userdata();
        if (empty($sesi['pegawai_nik'])) {
            redirect('tampilan');
        }
        $this->load->model('M_pekerjaan');
        $this->load->model('master/M_user');
    }

    public function index()
    {
        $data = $this->session->userdata();

        // $this->load->view('pekerjaan_langsung', $data);
        $this->template->template_master('pekerjaan_langsung', $data);
    }

    /* Index Pekerjaan Detail */
    public function detailPekerjaan($var = null)
    {
        // $param['pekerjaan_id'] = $this->input->get('pekerjaan_id');

        // $data["pekerjaan"] = $this->M_pekerjaan->getPekerjaan($param);

        // // $this->load->view('project/detail_pekerjaan_langsung', $data);
        // $this->template->template_master('project/detail_pekerjaan_langsung', $data);

        $param['pekerjaan_id'] = preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get_post('pekerjaan_id'));
        $data = array();
        $data = $this->session->userdata();
        $data['pekerjaan'] = $this->M_pekerjaan->getPekerjaan($param);
        // $this->template->template_master('project/detail_pekerjaan_usulan', $data);
        $this->load->view('tampilan/header', $data, FALSE);
        $this->load->view('tampilan/sidebar', $data, FALSE);
        $this->load->view('project/detail_pekerjaan_langsung', $data);
        $this->load->view('tampilan/footer', $data, FALSE);
    }
    /* Index Pekerjaan Detail */
    /* INDEX */

    /* PEKERJAAN USULAN */
    /* GET */
    /* Get Pekerjaan Usulan */
    public function getPekerjaanLangsung()
    {
        $param['pekerjaan_id'] = $this->input->get('pekerjaan_id');
        // $param['pekerjaan_status_not_inpro'] = '5';
        $data = array();
        $session = $this->session->userdata();
        // $data = $this->M_pekerjaan->getPekerjaan($param);

        if ($param['pekerjaan_id'] != null) {
            $data = $this->M_pekerjaan->getPekerjaan($param);
        } else {
            foreach ($this->M_pekerjaan->getPekerjaan($param) as $value) {
                // echo $this->db->last_query();
                // die();
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
    /* Get Pekerjaan Usulan */

    /* Get Pekerjaan Usulan Aksi */
    public function getPekerjaanLangsungAksi()
    {
        $param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
        $data = $this->M_pekerjaan->getPekerjaanLangsungAksi($param);
        echo json_encode($data);
    }
    /* Get Pekerjaan Usulan Aksi */

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
        $sql = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->post('pic') . "'");
        $isi = $sql->row_array();
        $pekerjaan_status = '0';

        $data['pekerjaan_id'] = $this->input->post('pekerjaan_id');
        $data['pekerjaan_judul'] = $this->input->post('pekerjaan_judul');
        $data['pekerjaan_waktu'] = date('Y-m-d', strtotime($this->input->post('pekerjaan_waktu')));
        $data['pekerjaan_waktu_akhir'] = date('Y-m-d', strtotime($this->input->post('pekerjaan_waktu_akhir')));
        $data['id_klasifikasi_pekerjaan'] = $this->input->post('id_klasifikasi_pekerjaan');
        $data['pic'] = $this->input->get_post('pic');
        $data['pic_no_telp'] = $this->input->post('pic_no_telp');
        $data['pekerjaan_status'] = $pekerjaan_status;
        $data['id_pekerjaan_disposisi'] = $this->input->post('id_pekerjaan_disposisi');
        $data['pekerjaan_tahun'] = $this->input->post('pekerjaan_tahun');
        $data['pekerjaan_reviewer'] = $this->input->post('reviewer');
        $data['pekerjaan_approver'] = $this->input->post('approver');
        $data['pekerjaan_deskripsi'] = $this->input->post('pekerjaan_deskripsi');

        $this->M_pekerjaan->insertPekerjaan($data);

        dblog('I',  $data['pekerjaan_id'], 'Pekerjaan Tersimpan di Draft', $this->input->get_post('pic'));
    }
    /* Pekerjaan Dreft */

    /* Pekerjaan Send */
    public function insertPekerjaanSend()
    {
        $sql = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->post('pic') . "'");
        $isi = $sql->row_array();

        if ($this->input->post('jabatan_temp') == '2') $pekerjaan_status = '3';
        elseif ($this->input->post('jabatan_temp') == '3') $pekerjaan_status = '2';
        else $pekerjaan_status = '1';

        // $pekerjaan_status = '1';

        $pekerjaan_status_temp = $this->input->post('pekerjaan_status');
        $pekerjaan_id = $this->input->post('pekerjaan_id');

        if ($pekerjaan_status_temp == '1') {
            /* Draft Dulu */
            $data['pekerjaan_judul'] = $this->input->post('pekerjaan_judul');
            $data['pekerjaan_waktu'] = date('Y-m-d', strtotime($this->input->post('pekerjaan_waktu')));
            $data['pekerjaan_waktu_akhir'] = date('Y-m-d', strtotime($this->input->post('pekerjaan_waktu_akhir')));
            $data['id_klasifikasi_pekerjaan'] = $this->input->post('id_klasifikasi_pekerjaan');
            $data['pic'] = $this->input->post('pic');
            $data['pic_no_telp'] = $this->input->post('pic_no_telp');
            $data['id_pekerjaan_disposisi'] = $this->input->post('id_pekerjaan_disposisi');
            $data['tipe_pekerjaan'] = $this->input->post('tipe_pekerjaan');
            $data['pekerjaan_status'] = $pekerjaan_status;
            $data['pekerjaan_tahun'] = $this->input->post('pekerjaan_tahun');
            $data['pekerjaan_reviewer'] = $this->input->post('reviewer');
            $data['pekerjaan_approver'] = $this->input->post('approver');
            $data['pekerjaan_deskripsi'] = $this->input->post('pekerjaan_deskripsi');

            $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
            /* Draft Dulu */
        } else {
            /* Langsung Send */
            $data['pekerjaan_id'] = $this->input->post('pekerjaan_id');
            $data['pekerjaan_judul'] = $this->input->post('pekerjaan_judul');
            $data['pekerjaan_waktu'] = date('Y-m-d', strtotime($this->input->post('pekerjaan_waktu')));
            $data['pekerjaan_waktu_akhir'] = date('Y-m-d', strtotime($this->input->post('pekerjaan_waktu_akhir')));
            $data['id_klasifikasi_pekerjaan'] = $this->input->post('id_klasifikasi_pekerjaan');
            $data['pic'] = $this->input->get_post('pic');
            $data['pic_no_telp'] = $this->input->post('pic_no_telp');
            $data['pekerjaan_status'] = $pekerjaan_status;
            $data['id_pekerjaan_disposisi'] = $this->input->post('id_pekerjaan_disposisi');
            $data['pekerjaan_tahun'] = $this->input->post('pekerjaan_tahun');
            $data['pekerjaan_reviewer'] = $this->input->post('reviewer');
            $data['pekerjaan_approver'] = $this->input->post('approver');
            $data['pekerjaan_deskripsi'] = $this->input->post('pekerjaan_deskripsi');

            $this->M_pekerjaan->insertPekerjaan($data);
            /* Langsung Send */
        }

        $data_users['pegawai_poscode'] = ($pekerjaan_status == '3') ? 'E53000000' : $isi['pegawai_direct_superior'];
        $users = $this->M_user->getUser($data_users);

        $dari = $isi['pegawai_nik'];
        $tujuan = $users['pegawai_nik'];
        $tujuan_nama = $users['pegawai_nama'];
        $text = "Mohon untuk melakukan REVIEW pada pekerjaan ini";
        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
        sendNotif($pekerjaan_id, $dari, $tujuan, $text);

        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Send ke AVP Customer', $this->input->get_post('pic'));

        /* User */
        $data_user['pegawai_poscode'] = ($pekerjaan_status == '3') ? 'E53000000' : $isi['pegawai_direct_superior'];
        $user = $this->M_user->getUser($data_user);
        /* User */

        /* Disposisi */
        /* AVP */
        if ($this->input->post('reviewer') != '' || $this->input->post('reviewer') != null) {
            $param['pegawai_nik'] = $this->input->post('reviewer');
            $userReviewer = $this->M_pekerjaan->getUserListRevApp2($param);

            $data_disposisi['pekerjaan_disposisi_id'] = create_id();
            $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
            $data_disposisi['id_user'] = htmlentities($userReviewer['pegawai_nik']);
            $data_disposisi['id_pekerjaan'] = htmlentities($pekerjaan_id);
            $data_disposisi['pekerjaan_disposisi_status'] = htmlentities($pekerjaan_status);

            $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
        }
        /* AVP */

        /* VP */
        if ($this->input->post('approver') != '' || $this->input->post('approver') != null) {
            $param['pegawai_nik'] = $this->input->post('approver');
            $userApprover = $this->M_pekerjaan->getUserListRevApp2($param);

            $data_disposisi2['pekerjaan_disposisi_id'] = create_id();
            $data_disposisi2['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
            $data_disposisi2['id_user'] = htmlentities($userApprover['pegawai_nik']);
            $data_disposisi2['id_pekerjaan'] = htmlentities($pekerjaan_id);
            $data_disposisi2['pekerjaan_disposisi_status'] = '2';

            $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi2);
        }
        /* VP */

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

      /* Disposisi */

      /*CC NON HPS*/
      if ($this->input->get_post('ref_unit_kerja')) {
        $user = $this->input->get_post('ref_unit_kerja');
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

  /*Insert CC*/

  /* INSERT PIC ke CC HPS */
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
  dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC HPS', $isi['pegawai_nik']);
  $tujuan = $data_cc['pegawai_nik'];
  $tujuan_nama = $data_cc['pegawai_nama'];
  $kalimat = "Pekerjaan telah di CC kepada anda";
  sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
  sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
  /* INSERT PIC ke CC HPS */

  /* Buat Notifikasi */
  $dari = $isi['pegawai_nik'];
  $tujuan = $user['pegawai_nik'];
  $tujuan_nama = $user['pegawai_nama'];
  $text = "Mohon untuk melakukan REVIEW pada pekerjaan ini";
  dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Send ke AVP Customer', $isi['pegawai_nik']);
  sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $text);
  sendNotif($pekerjaan_id, $dari, $tujuan, $text);
  /* Buat Notifikasi */
}
/* Pekerjaan Send */

/* Pekerjaan Edit */
public function updatePekerjaan()
{
    $pekerjaan_id = $this->input->post('pekerjaan_id');
    if ($pekerjaan_id) {
        $data['pekerjaan_judul'] = $this->input->post('pekerjaan_judul');
        $data['pekerjaan_waktu'] = date('Y-m-d', strtotime($this->input->post('pekerjaan_waktu')));
        $data['pekerjaan_waktu_akhir'] = date('Y-m-d', strtotime($this->input->post('pekerjaan_waktu_akhir')));
        $data['id_klasifikasi_pekerjaan'] = $this->input->post('id_klasifikasi_pekerjaan');
        $data['pic'] = $this->input->post('pic');
        $data['pic_no_telp'] = $this->input->post('pic_no_telp');
        $data['id_pekerjaan_disposisi'] = $this->input->post('id_pekerjaan_disposisi');
        $data['tipe_pekerjaan'] = $this->input->post('tipe_pekerjaan');
            // $data['pekerjaan_status'] = '0';
        $data['pekerjaan_tahun'] = $this->input->post('pekerjaan_tahun');
        $data['pekerjaan_reviewer'] = $this->input->post('reviewer');
        $data['pekerjaan_approver'] = $this->input->post('approver');
        $data['pekerjaan_deskripsi'] = $this->input->post('pekerjaan_deskripsi');

        $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
            // echo $this->db->last_query();

        dblog('E', $pekerjaan_id, 'Pekerjaan Telah di Edit', $this->input->get_post('pic'));
    }
}
/* Pekerjaan Edit */

/* Pekerjaan Edit */
public function updatePekerjaanEdit()
{
    if (isset($_GET['id_user'])) {
        $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
        $isi = $sql_isi->row_array();
    } else {
        $isi = $this->session->userdata();
    }

    $pekerjaan_id = $this->input->post('pekerjaan_id_edit');
    if ($this->input->get_post('cc_non_hps')) {
        $user = $this->input->get_post('cc_non_hps');
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
                        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC HPS', $isi['pegawai_nik']);
                        $tujuan = $data_cc['pegawai_nik'];
                        $tujuan_nama = $data_cc['pegawai_nama'];
                        $kalimat = "Pekerjaan telah di CC kepada anda";
                        sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
                        sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);
                    }
                }
            }
            /*CC HPS*/

            if ($pekerjaan_id) {
                $data['pekerjaan_judul'] = $this->input->post('pekerjaan_judul_edit');
                $data['pekerjaan_waktu'] = date('Y-m-d', strtotime($this->input->post('pekerjaan_waktu_edit')));
                $data['pekerjaan_waktu_akhir'] = date('Y-m-d', strtotime($this->input->post('pekerjaan_waktu_akhir_edit')));
                $data['id_klasifikasi_pekerjaan'] = $this->input->post('id_klasifikasi_pekerjaan_edit');
                $data['pic_no_telp'] = $this->input->post('pic_no_telp_edit');
                $data['pekerjaan_tahun'] = $this->input->post('pekerjaan_tahun_edit');
                if (!empty($this->input->post('pekerjaan_nomor_edit'))) {
                    $data['pekerjaan_nomor'] = $this->input->post('pekerjaan_nomor_edit');
                }
                $data['pekerjaan_deskripsi'] = $this->input->post('pekerjaan_deskripsi_edit');

                $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);
            // echo $this->db->last_query();

                dblog('E', $pekerjaan_id, 'Pekerjaan Telah di Edit', $this->input->get_post('pic'));
            }
        }
        /* Pekerjaan Edit */

        /* Pekerjaan Edit */
        public function updatePekerjaanLangsungAksi()
        {
            if (isset($_GET['id_user'])) {
                $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
                $isi = $sql_isi->row_array();
            } else {
                $isi = $this->session->userdata();
            }

            $pekerjaan_id = $this->input->get_post('pekerjaan_id_aksi');
            $data['pekerjaan_judul'] = $this->input->get_post('pekerjaan_judul_aksi');
            $data['pekerjaan_waktu'] = date('Y-m-d', strtotime($this->input->post('pekerjaan_waktu_aksi')));
            $data['pekerjaan_waktu_akhir'] = date('Y-m-d', strtotime($this->input->post('pekerjaan_waktu_akhir_aksi')));
            $data['id_klasifikasi_pekerjaan'] = $this->input->get_post('id_klasifikasi_pekerjaan_aksi');
            $data['id_user'] = $this->input->get_post('id_user_aksi');
            $data['pekerjaan_status'] = $this->input->get_post('pekerjaan_status_edit');
        // $data['id_pekerjaan_disposisi'] = $this->input->get_post('id_pekerjaan_disposisi_aksi'); //
        // $data['tipe_pekerjaan'] = $this->input->get_post('tipe_pekerjaan_aksi'); //
            if (!empty($this->input->post('pekerjaan_nomor_aksi'))) {
                $data['pekerjaan_nomor'] = $this->input->get_post('pekerjaan_nomor_aksi');
            }
            $data['pekerjaan_nilai_hps'] = ($this->input->get_post('pekerjaan_nilai_hps_aksi') != '') ? preg_replace('/[^0-9]/', '', $this->input->get_post('pekerjaan_nilai_hps_aksi')) : '0';
            $data['pekerjaan_nilai_kontrak'] = ($this->input->get_post('pekerjaan_nilai_hps_aksi') != '') ? preg_replace('/[^0-9]/', '', $this->input->get_post('pekerjaan_nilai_kontrak_aksi')) : '0';
            $data['pekerjaan_vendor'] = $this->input->get_post('pekerjaan_vendor_aksi');
        // $data['pekerjaan_durasi'] = $this->input->get_post('pekerjaan_durasi_aksi');
            $data['pekerjaan_status'] = '15';
        $data['pekerjaan_deskripsi'] = $this->input->get_post('pekerjaan_deskripsi_aksi'); //

        // print_r($data);

        $this->M_pekerjaan->updatePekerjaan($data, $pekerjaan_id);

        dblog('E', $pekerjaan_id, 'Pekerjaan Telah di Selesaikan');

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

            /*CC NON HPS*/
            if ($this->input->get_post('cc_hps_id')) {
                $user = $this->input->get_post('cc_hps_id');
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
                            dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC ke ' . $data_cc['pegawai_nama'] . ' Sebagai CC HPS', $isi['pegawai_nik']);
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

                    if (!empty($tmpFile)) {
                        $Extension    = array("jpeg", "jpg", "png", "bmp", "gif", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf");

                        $random = rand(11111111, 99999999);

                        $fileExt       = substr($fileName, strrpos($fileName, '.'));
                $fileExt       = str_replace('.', '', $fileExt); // Extension
                $fileName      = preg_replace("/\.[^.\s]{3,4}$/", "", $fileName);
                $newFileName   = str_replace(' ', '', $random . '_' . date('ymdhis') . '.' . $fileExt);

                // $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
                // $fileName = pathinfo($fileName, PATHINFO_FILENAME);
                // $newFileName = str_replace(' ', '', $_POST['id_pekerjaan'] . '_' . date('ymdhis') . '_' . $random . '.' . $fileExt);

                if (in_array($fileExt, $Extension)) {
                    move_uploaded_file($tmpFile, $directory . $newFileName);
                }
                echo $newFileName;
            }
        }
    }
    /* Insert File Pekerjaan Dokumen */

    /* Insert Pekerjaan Dokumen */
    public function insertPekerjaanDokumenUsulan()
    {
        $sesi = $this->session->userdata();
        $data['pekerjaan_dokumen_id'] = create_id();
        $data['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
        $data['pekerjaan_dokumen_nama'] = $this->input->get_post('pekerjaan_dokumen_nama');
        $data['pekerjaan_dokumen_file'] = $this->input->get_post('savedFileName');
        $data['pekerjaan_dokumen_status'] = '1';
        $data['who_create'] = $sesi['pegawai_nama'];
        $data['id_create'] = $sesi['pegawai_nik'];
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
        $pekerjaan_status_send_vp = '9';
        // $pekerjaan_status = '9';


        if ($this->input->get_post('id_user_send_vp')) {

            $this->M_pekerjaan->deletePekerjaanDisposisi($pekerjaan_id, null, $id_tanggung_jawab, $pekerjaan_status_send_vp);

            $user = $this->input->get_post('id_user_send_vp');
            foreach ($user as $key => $value) {
                $data_disposisi_vp['pekerjaan_disposisi_id'] = create_id();
                $data_disposisi_vp['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
                $data_disposisi_vp['id_user'] = $value;
                $data_disposisi_vp['id_pekerjaan'] = $pekerjaan_id;
                $data_disposisi_vp['pekerjaan_disposisi_status'] = '9';
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
        $pekerjaan_status_approve_vp = '9';
        // $pekerjaan_status = '9';


        if ($this->input->get_post('id_user_approve_vp')) {

            // $this->M_pekerjaan->deletePekerjaanDisposisi($pekerjaan_id, null, $id_tanggung_jawab, $pekerjaan_status_approve_vp);

            $user = $this->input->get_post('id_user_approve_vp');
            foreach ($user as $key => $value) {
                $data_disposisi_vp['pekerjaan_disposisi_id'] = create_id();
                $data_disposisi_vp['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
                $data_disposisi_vp['id_user'] = $value;
                $data_disposisi_vp['id_pekerjaan'] = $pekerjaan_id;
                $data_disposisi_vp['pekerjaan_disposisi_status'] = '9';
                $data_disposisi_vp['id_penanggung_jawab'] = 'n';

                // $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_vp);
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
            dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reviewed Oleh Cangun');
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
        // $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
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

        $data = array();

        foreach ($this->M_pekerjaan->getPekerjaan() as $key => $value) {
            $sql_total = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '5' AND id_pekerjaan = '" . $value['pekerjaan_id'] . "' ");
            $isi_total = $sql_total->row_array();

            $isi['pekerjaan_id'] = $value['pekerjaan_id'];
            $isi['pekerjaan_nomor'] = $value['pekerjaan_nomor'];
            $isi['pekerjaan_judul'] = $value['pekerjaan_judul'];
            $isi['pegawai_nama'] = $value['pegawai_nama'];
            $isi['pekerjaan_progress'] = $value['pekerjaan_progress'];
            $isi['total'] = $isi_total['total'];
            $isi['tanggal_akhir'] = $value['tanggal_akhir'];

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

        dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reviewed');
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

            dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reviewed VP Cangun');
        }
        /* Pekerjaan */

        /* Disposisi */
        $data_disposisi['pekerjaan_disposisi_id'] = create_id();
        $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
        $data_disposisi['id_user'] = $this->input->post('id_tanggung_jawab_vp');
        $data_disposisi['id_pekerjaan'] = $pekerjaan_id;
        $data_disposisi['pekerjaan_disposisi_status'] = $pekerjaan_status;
        $data_disposisi['id_penanggung_jawab'] = 'y';
        $data_disposisi['pekerjaan_disposisi_prioritas'] = anti_inject($this->input->post('prioritas_pekerjaan_vp'));

        $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);

        if ($this->input->post('id_user_vp')) {
            $User = $this->input->post('id_user_vp');
            foreach ($User as $key => $id_user) {
                $data_disposisi['pekerjaan_disposisi_id'] = create_id();
                $data_disposisi['pekerjaan_disposisi_waktu'] = date("Y-m-d H:i:s");
                $data_disposisi['id_user'] = $id_user;
                $data_disposisi['id_pekerjaan'] = $pekerjaan_id;
                $data_disposisi['pekerjaan_disposisi_status'] = $pekerjaan_status;
                $data_disposisi['id_penanggung_jawab'] = 'n';
                $data_disposisi['pekerjaan_disposisi_prioritas'] = anti_inject($this->input->post('prioritas_pekerjaan_vp'));

                $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
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
                $data_disposisi['pekerjaan_disposisi_kategori'] = anti_inject($this->input->post('kategori_pekerjaan_avp'));

                if ($this->input->post('kategori_pekerjaan_avp') == '1') {
                    $hari = '3';
                } else if ($this->input->post('kategori_pekerjaan_avp') == '2') {
                    $hari = '5';
                } else if ($this->input->post('kategori_pekerjaan_avp') == '3') {
                    $hari = '7';
                }
                $data_disposisi['pekerjaan_disposisi_durasi'] = $hari;

                $finish = date('Y-m-d H:i:s', strtotime($data_disposisi['pekerjaan_disposisi_waktu'] . '+' . ($hari - 1) . ' days'));

                $data_disposisi['pekerjaan_disposisi_waktu_finish'] = $finish;


                $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi);
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
                // echo $this->db->last_query();

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
            $data_disposisi['pekerjaan_disposisi_kategori'] = anti_inject($this->input->post('kategori_pekerjaan_avp'));

            if ($this->input->post('kategori_pekerjaan_avp') == '1') {
                $hari = '3';
            } else if ($this->input->post('kategori_pekerjaan_avp') == '2') {
                $hari = '5';
            } else if ($this->input->post('kategori_pekerjaan_avp') == '3') {
                $hari = '7';
            }
            $data_disposisi['pekerjaan_disposisi_durasi'] = $hari;

            $finish = date('Y-m-d H:i:s', strtotime($data_disposisi['pekerjaan_disposisi_waktu'] . '+' . ($hari - 1) . ' days'));

            $data_disposisi['pekerjaan_disposisi_waktu_finish'] = $finish;

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
                $data_disposisi_doc['pekerjaan_disposisi_status'] = '9';
                $data_disposisi_doc['id_penanggung_jawab'] = 'n';

                $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);
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

            dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reviewed Oleh Cangun');
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

            dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reviewed Oleh Cangun');
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

            dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di Reviewed Oleh PIC');
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
                $Extension    = array("jpeg", "jpg", "png", "bmp", "gif", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf");
                $acak           = rand(11111111, 99999999);
                $ImageExt       = substr($ImageName, strrpos($ImageName, '.'));
                $ImageExt       = str_replace('.', '', $ImageExt); // Extension
                $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
                $NewImageName   = str_replace(' ', '', $_POST['pekerjaan_dokumen_id'] . '_' . date('ymdhis') . '_' . $acak . '.' . $ImageExt);

                if (in_array($ImageExt, $Extension)) {
                    move_uploaded_file($_FILES["transaksi_detail_file"]["tmp_name"], $temp . $NewImageName); // Menyimpan file
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

        $status_dokumen = ($this->input->get_post('pekerjaan_dokumen_status') == 'n') ? '0' : '3';

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
                $Extension    = array("jpeg", "jpg", "png", "bmp", "gif", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf");
                $acak           = rand(11111111, 99999999);
                $ImageExt       = substr($ImageName, strrpos($ImageName, '.'));
                $ImageExt       = str_replace('.', '', $ImageExt); // Extension
                $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
                $NewImageName   = str_replace(' ', '', $_POST['pekerjaan_dokumen_id'] . '_' . date('ymdhis') . '_' . $acak . '.' . $ImageExt);

                if (in_array($ImageExt, $Extension)) {
                    move_uploaded_file($_FILES["transaksi_detail_file"]["tmp_name"], $temp . $NewImageName); // Menyimpan file
                }
                $note = "Data Berhasil Disimpan";
            } else {
                $note = "Data Gagal Disimpan";
            }
            echo $note;
        } else {
            $NewImageName = null;
        }

        $dokumen_status = ($this->input->get_post('pekerjaan_dokumen_status') == 'n') ? '0' : '4';

        /* Insert */
        if ($NewImageName == null) {
            $data['pekerjaan_dokumen_id_temp'] = $this->input->post('pekerjaan_dokumen_id');
            $data['pekerjaan_dokumen_id'] = create_id();
            $data['pekerjaan_dokumen_status'] = $dokumen_status;
            $data['pekerjaan_dokumen_keterangan'] = $this->input->post('pekerjaan_dokumen_keterangan');

            $this->M_pekerjaan->simpanAksiSama($data);
        } else {
            $data['pekerjaan_dokumen_id_temp'] = $this->input->post('pekerjaan_dokumen_id');
            $data['pekerjaan_dokumen_id'] = create_id();
            $data['pekerjaan_dokumen_file'] = $NewImageName;
            $data['pekerjaan_dokumen_status'] = $dokumen_status;
            $data['pekerjaan_dokumen_keterangan'] = $this->input->post('pekerjaan_dokumen_keterangan');

            $this->M_pekerjaan->simpanAksi($data);
        }
        /* Insert */

        // cek apakah direvisi
        if ($this->input->get_post('pekerjaan_dokumen_status') == 'n') {
            // ambil urutan revisi sebelumnya dari dokumen di pekerjaan tersebut
            $data_revisi = $this->M_pekerjaan->getRevisiNomor($data);
            // print_r($this->db->last_query());
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
    /* Aksi Approve / Reject Dokumen */
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
        $isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik='" . $this->input->get_post('user_id') . "'")->row_array();

        $list['results'] = array();
        $param['pegawai_nama'] = $this->input->get('pegawai_nama');
        $param['pegawai_poscode'] = $isi['pegawai_poscode'];
        $param['bagian_nama'] = $isi['pegawai_nama_bag'];


        if ($isi['pegawai_poscode'] == 'E53600031A' || $isi['pegawai_poscode'] == 'E53500031B') {
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

    /* get user staf cc*/
    public function getUserCC()
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
    /* get user staf cc*/

    /*GET VP Penanggung Jawab*/
    public function getVPPJ()
    {
        $user = $this->session->userdata();

        $param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
        $param['pegawai_nik'] = $this->input->get_post('user_id');
        $param['id_tanggung_jawab'] = 'y';
        $data = $this->M_pekerjaan->getVP($param);
        // echo $this->db->last_query();

        echo json_encode($data);
    }
    /*GET VP Penanggung Jawab*/

    /*GET VP Terkait*/
    public function getVPTerkait()
    {
        $user = $this->session->userdata();

        $param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
        $param['pegawai_nik'] = $this->input->get_post('user_id');
        $param['id_tanggung_jawab'] = 'n';
        $data = $this->M_pekerjaan->getVP($param);
        // echo $this->db->last_query();

        echo json_encode($data);
    }
    /*GET VP Terkait*/

    /*GET VP AVP */
    public function getVPAVP()
    {
        $user = $this->session->userdata();

        $param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
        $param['pekerjaan_disposisi_status'] = $this->input->get_post('pekerjaan_disposisi_status');
        $param['id_tanggung_jawab'] = $this->input->get_post('id_tanggung_jawab');
        // $param['pegawai_nik'] = $this->input->get_post('user_id');
        // $param['id_tanggung_jawab'] = 'n';
        $data = $this->M_pekerjaan->getVPAVPLangsung($param);

        // echo $this->db->last_query();

        echo json_encode($data);
    }
    /*GET VP AVP */

    /*GET AVP */
    public function getAVP()
    {
        $isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik='" . $this->input->get_post('user_id') . "'")->row_array();

        $param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
        $param['pegawai_nama'] = $this->input->get('pegawai_nama');
        $param['pegawai_poscode'] = $isi['pegawai_poscode'];
        $param['bagian_nama'] = $isi['pegawai_nama_bag'];
        $param['pekerjaan_disposisi_status'] = '5';

        // if ($isi['pegawai_poscode'] == 'E53600031A' || $isi['pegawai_poscode'] == 'E53500031B') {
        //     $data = $this->M_pekerjaan->getUserListAVPKhusus($param);
        //     echo json_encode($data);
        // } else {
        $data = $this->M_pekerjaan->getAVP($param);
        echo json_encode($data);
        // }
    }
    /*GET AVP */

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
        $param['pegawai_nik'] = $this->input->get_post('user_id');
        $param['id_tanggung_jawab'] = $this->input->get_post('id_tanggung_jawab');
        // print_r($param);
        // $param['id_tanggung_jawab'] = 'n';
        $data = $this->M_pekerjaan->getVPAVPLangsung($param);
        // echo $this->db->last_query();

        echo json_encode($data);
    }
    /*GET VP AVP Penanggung Jawab TJ*/




    public function getPekerjaan()
    {
        if (isset($_GET['id_user'])) {
            $sql_isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $_GET['id_user'] . "'");
            $isi = $sql_isi->row_array();
        } else {
            $isi = $this->session->userdata();
        }

        $sql_disposisi_status = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_user='" . $this->input->get_post('pekerjaan_user') . "' AND id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND pekerjaan_disposisi_status IN('5','9') AND is_aktif='y'");
        $data_disposisi_status = $sql_disposisi_status->row_array();

        $sql_pic = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '".$this->input->get('pekerjaan_id')."' AND id_user = '".$isi['pegawai_nik']."' AND pekerjaan_disposisi_status = '8' AND (is_cc is null OR is_cc ='') ");
        $jml_pic = $sql_pic->num_rows();

        $param['pekerjaan_id'] = htmlentities($this->input->get_post('pekerjaan_id'));
        $param['pegawai_nik'] = $isi['pegawai_nik'];
        $param['pekerjaan_status'] = htmlentities($this->input->get('pekerjaan_status'));
        // $param['pekerjaan_disposisi_status'] =  ($data_disposisi_status['total'] > 0) ? htmlentities($this->input->get('pekerjaan_status')) : htmlentities($this->input->get('pekerjaan_status') + 1);
        $param['pekerjaan_disposisi_status'] = htmlentities($this->input->get('pekerjaan_status'));

        $data = $this->M_pekerjaan->getPekerjaanDetailLangsung($param);
        $data['is_pic'] = ($jml_pic>0) ? 'y':'n'; 

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
        $param['pekerjaan_disposisi_status'] = $this->input->get_post('pekerjaan_status');
        $data = $this->M_pekerjaan->getAsetDocument($param);
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
        $sesi = $this->session->userdata();

        $data['pekerjaan_dokumen_id'] = create_id();
        $data['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
        $data['pekerjaan_dokumen_nama'] = $this->input->get_post('pekerjaan_dokumen_nama');
        $data['id_pekerjaan_template'] = $this->input->get_post('pekerjaan_template_nama');
        $data['pekerjaan_dokumen_file'] = $this->input->get_post('savedFileName');
        // if($this->input->get_post('pekerjaan_dokumen_status')){
        // $data['pekerjaan_dokumen_status'] = $this->input->get_post('pekerjaan_input_status');
        // }else{
        $data['pekerjaan_dokumen_status'] = 'b';
        // }
        $data['who_create'] = $sesi['pegawai_nama'];
        $data['id_create'] = $sesi['pegawai_nik'];
        $data['is_lama'] = 'n';
        $data['pekerjaan_dokumen_awal'] = 'n';


        $this->M_pekerjaan->insertPekerjaanDokumen($data);
        // print_r($this->db->last_query());
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

            $sesi = $this->session->userdata();

            $param1['pekerjaan_dokumen_id'] = create_id();
            $param1['id_pekerjaan'] = $this->input->get_post('id_pekerjaan');
            $param1['id_pekerjaan_disposisi'] = $param['pekerjaan_disposisi_id'];
            $param1['pekerjaan_dokumen_nama'] = $this->input->get_post('pekerjaan_dokumen_nama');
            $param1['pekerjaan_dokumen_keterangan'] = $this->input->get_post('pekerjaan_dokumen_keterangan');
            $param1['pekerjaan_dokumen_status'] = $this->input->get_post('pekerjaan_dokumen_status_nama');
            $param1['pekerjaan_dokumen_file'] = $this->input->get_post('savedFileName');
            $param1['who_create'] = $sesi['pegawai_nama'];
            $param1['who_create'] = $sesi['pegawai_nik'];

            $this->M_pekerjaan->insertPekerjaanDokumen($param1);

            // update dokumen lama
            $id = $this->input->get_post('pekerjaan_dokumen_id');
            $param1['is_lama'] = 'y';

            $this->M_pekerjaan->updatePekerjaanDokumen($param1, $id);
        }
    }

    public function updateAsetDocumentApproveVP($data = null)
    {
        // foreach($id_disposisi as $key=>$id_dis)
        // insert dokumen baru dan ubah status dokumen lama ke non aktif agar dokumen baru yang ditampilkan
        $sesi = $this->session->userdata();
        $param['pekerjaan_dokumen_id'] = create_id();
        $param['id_pekerjaan'] = $this->input->get_post('id_pekerjaan');
        $param['pekerjaan_dokumen_nama'] = $this->input->get_post('pekerjaan_dokumen_nama');
        // $param['pekerjaan_dokumen_departemen'] = $this->input->get_post('pekerjaan_dokumen_departemen');
        $param['pekerjaan_dokumen_keterangan'] = $this->input->get_post('pekerjaan_dokumen_keterangan');
        $param['pekerjaan_dokumen_status'] = $this->input->get_post('pekerjaan_dokumen_status_nama');
        $param['pekerjaan_dokumen_file'] = $this->input->get_post('savedFileName');
        // $param['id_pekerjaan_disposisi'] = $this->input->get_post('id_pekerjaan_disposisi');
        // $param['id_penanggung_jawab'] = $this->input->get_post('id_penanggung_jawab');
        $param['who_create'] = $sesi['pegawai_nama'];
        $param['id_create'] = $sesi['pegawai_nik'];
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

    /* LAIN */
    public function cekRevisiIFA()
    {
        $param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
        $data = $this->M_pekerjaan->cekRevisi($param);
        echo json_encode($data);
    }
    /* LAIN */
}



/* End of file Controllername.php */
