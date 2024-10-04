<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Transmital extends MX_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->library('template');
    $sesi = $this->session->userdata();
    if (empty($sesi['pegawai_nik'])) {
      redirect('tampilan');
    }
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
    $this->template->template_master('project/transmital', $data);
  }
  /* Index Pekerjaan Usulan */

  public function detail()
  {
    $param['pekerjaan_id'] = preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get_post('pekerjaan_id'));
    $data = array();
    $data = $this->session->userdata();
    $data['pekerjaan'] = $this->M_pekerjaan->getPekerjaan($param);
    $this->load->view('tampilan/header', $data, FALSE);
    $this->load->view('tampilan/sidebar', $data, FALSE);
    $this->load->view('project/detail_transmital', $data);
    $this->load->view('tampilan/footer', $data, FALSE);
  }

  /* INDEX */


  /* GET */
  /* transmital */
  public function getPekerjaan()
  {

    $session = $this->session->userdata();

    $param['pekerjaan_id'] = $this->input->get('pekerjaan_id');
    $param['pekerjaan_status'] = $this->input->get('pekerjaan_status');
    $param['pegawai_nik'] = $session['pegawai_nik'];

    $data = array();

    if ($param['pekerjaan_status'] == '0' && $this->input->get('aksi') == 'usulan') {
      $data = $this->db->query("SELECT a.*,b.*,c.*,c.is_proses as is_proses_transmital FROM dec.dec_pekerjaan a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.pekerjaan_id = b.id_pekerjaan left JOIN dec.dec_pekerjaan_disposisi_transmital c ON c.id_pekerjaan = a.pekerjaan_id WHERE a.pekerjaan_id = '" . $this->input->get('pekerjaan_id') . "' AND b.id_user ='" . $session['pegawai_nik'] . "' AND is_cc is not null AND b.is_aktif = 'y' AND pekerjaan_disposisi_status = '8'")->row_array();
      $data['pekerjaan_disposisi_transmital_status'] = '0';
    } else {
      $data = $this->M_pekerjaan->getPekerjaanTransmitalWaspro($param);
    }

    echo json_encode($data);
  }
  /* transmital */

  /*getPekerjaan waspro usulan*/
  public function getPekerjaanWasproUsulan()
  {
    $sesi = $this->session->userdata();
    $split = explode(',', $this->input->get_post('pekerjaan_status'));
    // $split1 = "'" . implode("','", $split) . "'";
    $param['pekerjaan_status'] = $split;
    $param['id_user'] = $sesi['pegawai_nik'];

    $transmital = $this->M_pekerjaan->getPekerjaanTransmitalUsulan($param);

    echo json_encode($transmital);
  }
  /*getPekerjaan waspro usulan*/

  /* get pekerjaan waspro */
  public function getPekerjaanWaspro()
  {
    $session = $this->session->userdata();
    $param['pekerjaan_id'] = $this->input->get('pekerjaan_id');
    $param['id_user'] = $this->input->get('id_user_cari');
    $param['klasifikasi_pekerjaan_id'] = $this->input->get('klasifikasi_pekerjaan_id');

    if ($this->input->get('pekerjaan_status')) {
      $split = explode(',', $this->input->get('pekerjaan_status'));
      $split1 = "'" . implode("','", $split) . "'";
      $param['pekerjaan_status'] = $split1;
    }

    if ($this->input->get('pekerjaan_transmital_status')) {
      $split_transmital = explode(',', $this->input->get('pekerjaan_transmital_status'));
      $split_transmital1 = "'" . implode("','", $split_transmital) . "'";
      $param['pekerjaan_disposisi_transmital_status'] = $split_transmital1;
    }

    if ($this->input->get('pekerjaan_transmital_status_cangun')) {
      $split_transmital_cangun = explode(',', $this->input->get('pekerjaan_transmital_status_cangun'));
      $param['pekerjaan_disposisi_transmital_status_cangun'] = $split_transmital_cangun;
    }
    $param['is_transmital'] = '1';
    $param['pekerjaan_is_selesai'] = ($this->input->get('pekerjaan_is_selesai')) ? 'y' : 'n';
    $param['user_disposisi'] = $session['pegawai_nik'];
    $data = array();

    $ada_transmital = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi_transmital WHERE id_pekerjaan IN (SELECT pekerjaan_id FROM dec.dec_pekerjaan WHERE pekerjaan_status IN('14','15')) AND id_user = '" . $session['pegawai_nik'] . "' ")->num_rows();

    $transmital = $this->M_pekerjaan->getPekerjaanTransmital($param);

    foreach ($transmital as $value) {
      foreach ($value as $key => $val) {
        $isi[$key] = $val;
      }
      $sql_milik = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi a LEFT JOIN dec.dec_pekerjaan_disposisi_transmital b ON a.id_pekerjaan = b.id_pekerjaan WHERE a.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND (a.id_user = '" . $session['pegawai_nik'] . "' OR b.id_user = '" . $session['pegawai_nik'] . "') AND pekerjaan_disposisi_status = '8' AND is_cc = 'y' AND a.is_aktif ='y' ");
      $jml_milik = $sql_milik->num_rows();

      $sql_transmital = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi_transmital WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND id_user = '" . $session['pegawai_nik'] . "' AND is_aktif = 'y' ORDER BY CAST(pekerjaan_disposisi_transmital_status AS FLOAT) DESC");
      $jml_transmital = $sql_transmital->num_rows();
      $data_transmital = $sql_transmital->row_array();

      $sql_proses = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi_transmital WHERE pekerjaan_disposisi_transmital_status = '$value[pekerjaan_status_transmital]' AND id_pekerjaan = '$value[pekerjaan_id]' AND  id_user = '$session[pegawai_nik]' ORDER BY pekerjaan_disposisi_transmital_status DESC");
      $data_proses = $sql_proses->row_array();

      $data_bagian = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi_transmital a LEFT JOIN global.global_bagian_detail b ON b.id_pegawai = a.id_user LEFT JOIN global.global_bagian c ON c.bagian_id = b.id_bagian WHERE a.id_user = '" . $session['pegawai_nik'] . "'")->row_array();

      /*bagian*/

      $isi['milik'] = ($jml_milik > 0) ? 'y' : 'n';
      $isi['proses'] = ($data_proses && $jml_milik > '0') ? $data_proses['is_proses'] : null;
      $isi['pekerjaan_transmital_status'] = ($jml_transmital > '0') ? $data_transmital['pekerjaan_disposisi_transmital_status'] : '0';
      $isi['bagian_id'] = $data_bagian['bagian_id'];
      array_push($data, $isi);
    }
    echo json_encode($data);
  }
  /* get pekerjaan waspro */

  /* get list pekerjaan selesai */
  public function getPekerjaanList()
  {
    $session = $this->session->userdata();
    $list['results'] = array();

    $param['pekerjaan_judul'] = $this->input->get('q');
    $param['klasifikasi_pekerjaan_id'] = $this->input->get_post('klasifikasi_pekerjaan_id');
    $split = explode(',', $this->input->get_post('pekerjaan_status'));
    $param['pekerjaan_status'] = $split;
    $data = $this->M_pekerjaan->getPekerjaan($param);
    foreach ($data as $key => $value) {
      array_push($list['results'], [
        'id' => $value['pekerjaan_id'],
        'text' => $value['pekerjaan_nomor'] . ' - ' . $value['pekerjaan_judul'],
      ]);
    }
    echo json_encode($list);
  }
  /* get list pekerjaan selesai */

  /*get user list*/
  public function getUserListWaspro()
  {

    $session = $this->session->userdata();

    $list['results'] = [];

    $param['pegawai_id_bag'] = $session['pegawai_id_bag'];
    if ($this->input->get('is_avp') == 'n') {
      $param['pegawai_unitkerja'] = 'E50110';
    } else {
      $param['pegawai_unitkerja'] = 'E50100';
    }
    $param['pegawai_nama'] = $this->input->get('pegawai_nama');

    $data = $this->M_pekerjaan->getUserList($param);

    foreach ($data as $key => $value) {
      array_push($list['results'], [
        'id' => $value['pegawai_nik'],
        'text' => $value['pegawai_nama'] . ' - ' . $value['pegawai_postitle'],
      ]);
    }
    echo json_encode($list);
  }
  /*get user list*/

  /*get list dokumen*/
  public function getDokumenTransmital()
  {
    if ($this->input->get('id_user')) {
      $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'");
      $user = $sql_user->row_array();
    } else {
      $user = $this->session->userdata();
    }

    $dataUser = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_disposisi a LEFT JOIN dec.dec_pekerjaan b ON b.pekerjaan_id=a.id_pekerjaan WHERE (pekerjaan_disposisi_status = '5' OR pekerjaan_disposisi_status = '6' OR pekerjaan_disposisi_status = '7') and a.id_user='" . $user['pegawai_nik'] . "'")->row_array();

    $dataPIC = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get('id_pekerjaan') . "' AND pekerjaan_disposisi_status = '8' AND id_user = '" . $user['pegawai_nik'] . "'")->num_rows();

    // if ($dataUser['total'] > 0) {
    $isi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON a.id_pekerjaan_template = b.pekerjaan_template_id LEFT JOIN dec.dec_pekerjaan c ON c.pekerjaan_id = a.id_pekerjaan LEFT JOIN global.global_bagian_detail d ON d.id_pegawai = a.id_create_awal LEFT JOIN global.global_bagian e ON e.bagian_id = d.id_bagian LEFT JOIN global.global_pegawai f ON f.pegawai_nik = d.id_pegawai LEFT JOIN global.global_pegawai h ON a.pekerjaan_dokumen_cc = h.pegawai_nik LEFT JOIN global.global_bidang j ON a.id_bidang = j.bidang_id LEFT JOIN global.global_urutan_proyek k ON a.id_urutan_proyek = k.urutan_proyek_id LEFT JOIN global.global_section_area l ON a.id_section_area = l.section_area_id WHERE pekerjaan_dokumen_awal = 'n' AND (is_lama != 'y' or is_lama is null) AND CAST(pekerjaan_dokumen_status AS INT) >= '11' AND a.id_pekerjaan = '" . $this->input->get('id_pekerjaan') . "' AND (is_hps = '" . $this->input->get('is_hps') . "' OR is_transmital = 'y') AND bagian_id = '" . $this->input->get('bagian_id') . "'")->result_array();
    // } else if($user['pegawai_nik'] == $this->admin_sistemnya || $this->id_bagiannya != '0' || $dataPIC>0){
    // $isi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON a.id_pekerjaan_template = b.pekerjaan_template_id LEFT JOIN dec.dec_pekerjaan c ON c.pekerjaan_id = a.id_pekerjaan LEFT JOIN global.global_bagian_detail d ON d.id_pegawai = a.id_create_awal LEFT JOIN global.global_bagian e ON e.bagian_id = d.id_bagian LEFT JOIN global.global_pegawai f ON f.pegawai_nik = d.id_pegawai  LEFT JOIN global.global_bidang j ON a.id_bidang = j.bidang_id LEFT JOIN global.global_urutan_proyek k ON a.id_urutan_proyek = k.urutan_proyek_id LEFT JOIN global.global_section_area l ON a.id_section_area = l.section_area_id WHERE pekerjaan_dokumen_awal = 'n' AND (is_lama != 'y' or is_lama is null) AND CAST(pekerjaan_dokumen_status AS INT) >= '11' AND a.id_pekerjaan = '" . $this->input->get('id_pekerjaan') . "' AND (is_hps = '" . $this->input->get('is_hps') . "' OR is_transmital = 'y') AND bagian_id = '".$this->input->get('bagian_id')."'")->result_array();
    // }

    $data = array();
    foreach ($isi as $value) {
      array_push($data, $value);
    }

    echo json_encode($data);
  }
  /*get list dokumen*/

  /* list dokumen kontraktor */
  public function getDokumenKontraktor()
  {
    $session = $this->session->userdata();
    $param['id_pekerjaan'] = $this->input->get('id_pekerjaan');
    $param['id_bagian'] = $this->input->get('id_bagian');

    $datanya = [];
    $data = $this->M_pekerjaan->getDokumenTransmital($param);
    foreach ($data as $value) :
      $avp_pic = $this->db->get_where('dec.dec_pekerjaan_disposisi_transmital', [
        'id_pekerjaan' => $value['id_pekerjaan'],
        'id_user' => $session['pegawai_nik'],
        'pekerjaan_disposisi_transmital_status' => '1',
      ])->num_rows();

      $perencana_cangun = $this->db->get_where('dec.dec_pekerjaan_disposisi_transmital', [
        'id_pekerjaan' => $value['id_pekerjaan'],
        'id_user' => $session['pegawai_nik'],
        'pekerjaan_disposisi_transmital_status' => '2',
        'id_bagian' => $this->input->get('id_bagian'),
      ])->num_rows();

      $avp_cangun = $this->db->get_where('dec.dec_pekerjaan_disposisi_transmital', [
        'id_pekerjaan' => $value['id_pekerjaan'],
        'id_user' => $session['pegawai_nik'],
        'pekerjaan_disposisi_transmital_status' => '3',
        'id_bagian' => $this->input->get('id_bagian'),
      ])->num_rows();

      $vp_cangun = $this->db->get_where('dec.dec_pekerjaan_disposisi_transmital', [
        'id_pekerjaan' => $value['id_pekerjaan'],
        'id_user' => $session['pegawai_nik'],
        'pekerjaan_disposisi_transmital_status' => '4',
      ])->num_rows();

      $value['avp_pic'] = ($avp_pic > 0) ? 'y' : 'n';
      $value['perencana_cangun'] = ($perencana_cangun > 0) ? 'y' : 'n';
      $value['avp_cangun'] = ($avp_cangun > 0) ? 'y' : 'n';
      $value['vp_cangun'] = ($vp_cangun > 0) ? 'y' : 'n';

      array_push($datanya, $value);
    endforeach;

    echo json_encode($datanya);
  }
  /* list dokumen kontraktor */

  /* list dokumen cangun */
  public function getDokumenCangun()
  {
    if ($this->input->get('id_user')) {
      $session = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'")->row_array();
    } else {
      $session = $this->session->userdata();
    }


    $data = $this->db->query("SELECT * FROM DEC.dec_pekerjaan_dokumen a
      LEFT JOIN DEC.dec_pekerjaan_template b ON a.id_pekerjaan_template = b.pekerjaan_template_id
      LEFT JOIN DEC.dec_pekerjaan C ON C.pekerjaan_id = a.id_pekerjaan
      LEFT JOIN GLOBAL.global_bagian_detail d ON d.id_pegawai = a.id_create_awal
      LEFT JOIN GLOBAL.global_bagian e ON e.bagian_id = d.id_bagian
      LEFT JOIN GLOBAL.global_pegawai f ON f.pegawai_nik = d.id_pegawai
      LEFT JOIN GLOBAL.global_pegawai h ON a.pekerjaan_dokumen_cc = h.pegawai_nik
      LEFT JOIN GLOBAL.global_bidang j ON a.id_bidang = j.bidang_id
      LEFT JOIN GLOBAL.global_urutan_proyek K ON a.id_urutan_proyek = K.urutan_proyek_id
      LEFT JOIN GLOBAL.global_section_area l ON a.id_section_area = l.section_area_id
      WHERE
      pekerjaan_dokumen_awal = 'n'
      AND ( is_lama != 'y' OR is_lama IS NULL )
      AND CAST ( pekerjaan_dokumen_status AS INT ) >= '11'
      AND a.id_pekerjaan = '" . $this->input->get('id_pekerjaan') . "'
      AND ( is_hps = 'n' )
      AND bagian_id = '" . $this->input->get('id_bagian') . "'")->result_array();

    echo json_encode($data);
  }

  public function getDokumenHistory()
  {
    $param['id_pekerjaan'] = $this->input->get('id_pekerjaan');
    $param['id_dokumen_awal'] = $this->input->get('id_dokumen_awal');
    $data = $this->M_pekerjaan->getDokumenTransmital($param);
    echo json_encode($data);
  }

  public function downloadDokumen()
  {
    if ($this->input->get('id_user')) {
      $isi = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'")->row_array();
    } else {
      $isi = $this->session->userdata();
    }
    $this->load->library('PdfGenerator');
    $this->load->helper(array('url', 'download'));
    $this->load->library('ciqrcode');

    $dokumen = explode('~', $this->input->get_post('pekerjaan_dokumen_file'));
    $format  = explode('.', $dokumen[0]);

    $file = preg_replace("/[^0-9^a-z^A-Z^_.]/", "", $dokumen[0]);
    $id = preg_replace("/[^0-9^a-z^A-Z]/", "", $dokumen[1]);

    /* QRCODE */
    $config['cacheable']    = true;
    $config['cachedir']     = './application/cache/';
    $config['errorlog']     = './application/logs/';
    $config['imagedir']     = './document/qrcode/';
    $config['quality']      = true;
    $config['size']         = '1024';
    $config['black']        = array(224, 255, 255);
    $config['white']        = array(70, 130, 180);
    $this->ciqrcode->initialize($config);


    $judul = 'qrcode_' . $format[0];
    $url = base_url('project/direct/downloadDokumenTransmital?pekerjaan_id=') . $this->input->get_post('pekerjaan_id') . '&pekerjaan_dokumen_file=' . $this->input->get_post('pekerjaan_dokumen_file');

    $image_name = $judul . '.PNG';
    $params['data'] = $url;
    $params['level'] = 'M';
    $params['size'] = 10;
    $params['savename'] = FCPATH . $config['imagedir'] . $image_name;
    $this->ciqrcode->generate($params);
    $this->db->query("UPDATE dec.dec_pekerjaan_dokumen_transmital SET pekerjaan_dokumen_qrcode = '" . $image_name . "' WHERE pekerjaan_dokumen_id = '" . $id . "'");
    /* QRCODE */

    $param['pekerjaan_id'] =  preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get_post('pekerjaan_id'));
    $param_dokumen['pekerjaan_dokumen_id'] = $id;

    $data['pekerjaan'] = $this->M_pekerjaan->getPekerjaan($param);
    $data['dokumen'] = $this->M_pekerjaan->getDokumenTransmital($param_dokumen);

    $html_cover = $this->load->view('project/pekerjaan_cover_transmital', $data, true);
    $file_cover = 'cover_' . $dokumen[0];
    $this->pdfgenerator->save($html_cover, $file_cover, 'A4', 'portrait');

    $judul = $data['dokumen']['pekerjaan_dokumen_nama'] . '-' . $data['dokumen']['pekerjaan_dokumen_nomor'];
    $data1['judul'] = $judul;
    $data1['qr_code'] = $data['dokumen']['pekerjaan_dokumen_qrcode'];
    $data1['cover_download'] = 'cover_' . preg_replace("/[^0-9^a-z^A-Z^_.]/", "", $dokumen[0]);
    $data1['data_download'] = preg_replace("/[^0-9^a-z^A-Z^_.]/", "", $dokumen[0]);
    $data1['is_change'] = $data['dokumen']['is_change'];
    $data1['pekerjaan_dokumen_status_doc'] = $data['dokumen']['pekerjaan_dokumen_status_doc'];
    $data1['pekerjaan_dokumen_jenis'] = $data['dokumen']['pekerjaan_dokumen_jenis'];
    $data1['pekerjaan_dokumen_kertas'] = $data['dokumen']['pekerjaan_dokumen_kertas'];
    $data1['pekerjaan_dokumen_orientasi'] = $data['dokumen']['pekerjaan_dokumen_orientasi'];
    $data1['pekerjaan'] = $this->db->query("SELECT a.*, b.klasifikasi_dokumen_inisial FROM dec.dec_pekerjaan_disposisi_transmital a LEFT JOIN global.global_klasifikasi_dokumen b ON a.id_user = b.id_pegawai WHERE a.id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "'")->row_array();
    $this->load->view('project/combine_transmital', $data1);

    $dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen_transmital a WHERE pekerjaan_dokumen_id = '" . $id . "'")->row_array();

    dblog('V', $this->input->get_post('pekerjaan_id'), 'Dokumen Transmital - ' . $dokumen['pekerjaan_dokumen_nama'] . ' Telah Didownload', $isi['pegawai_nik']);
  }

  public function downloadDokumenCangun()
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

    $image_name = $judul . '.PNG';
    $params['data'] = $url;
    $params['level'] = 'M';
    $params['size'] = 10;
    $params['savename'] = FCPATH . $config['imagedir'] . $image_name;
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
      $data1['direktori'] = FCPATH . 'document_baru/' . $judul;

      $data1['cover_download'] = 'cover_' . preg_replace("/[^0-9^a-z^A-Z^_.]/", "", $dokumen[0]);
      $data1['data_download'] = preg_replace("/[^0-9^a-z^A-Z^_.]/", "", $dokumen[0]);
      $data1['qrcode'] = preg_replace("/[^0-9^a-z^A-Z^_.]/", "", $image_name);
      $data1['halaman'] = $isi_template['pekerjaan_dokumen_jumlah'];
      $data1['kertas'] = $isi_template['pekerjaan_dokumen_kertas'];
      $data1['orientasi'] = $isi_template['pekerjaan_dokumen_orientasi'];
      $data1['qr_code'] = $isi_template['pekerjaan_dokumen_qrcode'];
      $data1['status_dokumen'] = $isi_template['pekerjaan_dokumen_status'];
      $data1['klasifikasi_pekerjaan_kode'] = $data['pekerjaan']['klasifikasi_pekerjaan_kode'];
      $this->load->view('project/combine_transmital_cangun', $data1);
      /* }*/
    }
  }

  /* GET */

  /* INSERT */
  public function insertPekerjaan()
  {
    if ($this->input->get('id_user')) {
      $sql_user = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'");
      $isi = $sql_user->row_array();
    } else {
      $isi = $this->session->userdata();
    }
    $pekerjaan_id = $this->input->get_post('pekerjaan_judul_list');
    $user = $this->input->post('user');
    if ($user) {
      $data_disposisi_doc['pekerjaan_disposisi_id'] = create_id();
      $data_disposisi_doc['pekerjaan_disposisi_waktu'] = date('Y-m-d H:i:s');
      $data_disposisi_doc['id_user'] = anti_inject($user);
      $data_disposisi_doc['id_pekerjaan'] = $pekerjaan_id;
      $data_disposisi_doc['pekerjaan_disposisi_status'] = anti_inject('8');
      $data_disposisi_doc['id_penanggung_jawab'] = anti_inject('n');
      $data_disposisi_doc['is_cc'] = anti_inject('y');
      $data_disposisi_doc['is_aktif'] = 'y';
      $this->M_pekerjaan->insertPekerjaanDisposisi($data_disposisi_doc);

      $data_cc = $this->db->get_where('global.global_pegawai', ['pegawai_nik' => $user])->row_array();
      $tujuan = $data_cc['pegawai_nik'];
      $tujuan_nama = $data_cc['pegawai_nama'];
      $kalimat = "Pekerjaan telah di CC kepada anda";
      sendWA($pekerjaan_id, $tujuan, $tujuan_nama, $kalimat);
      sendNotif($pekerjaan_id, $isi['pegawai_nik'], $tujuan, $kalimat);

      dblog('I',  $pekerjaan_id, 'Pekerjaan Telah di CC Kepada Anda ', $isi['pegawai_nik']);
    }
  }

  /*insert disposisi*/
  public function insertDisposisi()
  {
    if ($this->input->get('id_user')) {
      $session = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'")->row_array();
    } else {
      $session = $this->session->userdata();
    }

    $data['pekerjaan_kontraktor_nama'] = $this->input->post('pekerjaan_kontraktor_nama');
    $data['pekerjaan_status_transmital'] = '0';
    $this->M_pekerjaan->updatePekerjaan($data, $this->input->post('pekerjaan_id'));

    foreach ($this->input->post('pic_bagian') as $key => $value) :

      $data_disposisi['pekerjaan_disposisi_transmital_id'] = uniqid();
      $data_disposisi['pekerjaan_disposisi_transmital_waktu'] = date('Y-m-d H:i:s');
      $data_disposisi['id_user'] = $value;
      $data_disposisi['id_pekerjaan'] = $this->input->post('pekerjaan_id');
      $data_disposisi['pekerjaan_disposisi_transmital_status'] = 0;
      $data_disposisi['is_aktif'] = 'y';
      $data_disposisi['id_bagian'] = $key;

      $this->M_pekerjaan->insertSendTransmital($data_disposisi);

    endforeach;
  }
  /*insert disposisi*/

  /* insert send */
  public function insertSend()
  {
    if ($this->input->get('id_user')) {
      $session = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'")->row_array();
    } else {
      $session = $this->session->userdata();
    }

    $bagian = $this->db->query("SELECT id_bagian FROM dec.dec_pekerjaan_disposisi_transmital WHERE id_user ='" . $session['pegawai_nik'] . "'")->result_array();

    $paramAtasan['pegawai_poscode'] = $session['pegawai_direct_superior'];
    $dataAtasan = $this->M_user->getUser($paramAtasan);

    /* update status disposisi*/
    $where_status['id_user'] = $session['pegawai_nik'];
    $where_status['id_pekerjaan'] = $this->input->post('pekerjaan_id');
    $where_status['disposisi_status'] = $this->input->post('pekerjaan_status');
    $param_status['is_proses'] = 'y';
    $this->M_pekerjaan->updateStatusTransmital($where_status, $param_status);
    /* update status disposisi*/

    $pic_jml = $this->db->get_where('dec.dec_pekerjaan_disposisi_transmital', [
      'id_pekerjaan' => $this->input->post('pekerjaan_id'),
      'is_proses' => null,
      'pekerjaan_disposisi_transmital_status' => '0',
    ])->num_rows();

    $avp_pic_jml = $this->db->get_where(
      'dec.dec_pekerjaan_disposisi_transmital',
      [
        'id_pekerjaan' => $this->input->post('pekerjaan_id'),
        'pekerjaan_disposisi_transmital_status' => '1',
      ]
    )->num_rows();

    if ($pic_jml == 0 && $avp_pic_jml == 0) {
      $data_pekerjaan['pekerjaan_status_transmital'] = '1';
      $this->M_pekerjaan->updatePekerjaan($data_pekerjaan, $this->input->post('pekerjaan_id'));
      /* insert atasan */
      $data2['pekerjaan_disposisi_transmital_id'] = uniqid();
      $data2['pekerjaan_disposisi_transmital_waktu'] = date('Y-m-d H:i:s');
      $data2['id_user'] = $dataAtasan['pegawai_nik'];
      $data2['id_pekerjaan'] = $this->input->post('pekerjaan_id');
      $data2['pekerjaan_disposisi_transmital_status'] = '1';
      $data2['is_aktif'] = 'y';
      $this->M_pekerjaan->insertSendTransmital($data2);
      dblog('I', $this->input->post('pekerjaan_id'), 'Pekerjaan Transmital Telah Disend ', $session['pegawai_nik']);
    }

    if ($avp_pic_jml > 0) {
      $data_perencana = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->post('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '5' AND is_aktif = 'y' AND id_user NOT IN (SELECT id_user FROM dec.dec_pekerjaan_disposisi_transmital WHERE pekerjaan_disposisi_transmital_status = '2' AND id_pekerjaan = '" . $this->input->post('pekerjaan_id') . "')")->result_array();

      foreach ($data_perencana as $key_perencana => $value_perencana) {
        $bagian = $this->db->get_where('global.global_bagian_detail', array('id_pegawai' => $value_perencana['id_user']))->row_array();

        $data['pekerjaan_disposisi_transmital_id'] = uniqid();
        $data['pekerjaan_disposisi_transmital_waktu'] = date('Y-m-d H:i:s');
        $data['id_user'] = $value_perencana['id_user'];
        $data['id_pekerjaan'] = $this->input->post('pekerjaan_id');
        $data['pekerjaan_disposisi_transmital_status'] = '2';
        $data['is_aktif'] = 'y';
        $data['id_bagian'] = $bagian['id_bagian'];

        $this->M_pekerjaan->insertSendTransmital($data);
      }

      dblog('I', $this->input->post('pekerjaan_id'), 'Pekerjaan Transmital Telah diSend ke Perencana ', $session['pegawai_nik']);
    }

    /*perbarui status dokumen*/
    foreach ($bagian as $val_bagian) {
      $dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen_transmital WHERE id_pekerjaan = '" . $this->input->post('pekerjaan_id') . "' AND pekerjaan_dokumen_awal = 'n' AND is_lama = 'n' AND pekerjaan_dokumen_status <='1' AND id_bagian ='" . $val_bagian['id_bagian'] . "'")->result_array();

      foreach ($dokumen as $val_dokumen) {
        $dokumen_ada = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen_transmital WHERE id_pekerjaan = '" . $this->input->post('pekerjaan_id') . "' AND id_create_awal = '" . $val_dokumen['id_create_awal'] . "' AND pekerjaan_dokumen_status = '2' AND pekerjaan_dokumen_nama = '" . $val_dokumen['pekerjaan_dokumen_nama'] . "' AND pekerjaan_dokumen_nomor = '" . $val_dokumen['pekerjaan_dokumen_nomor'] . "'  AND id_bagian = '" . $val_dokumen['id_bagian'] . "'")->row_array();

        $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen_transmital WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
        $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];

        if (!empty($dokumen_ada) && ($dokumen_ada['pekerjaan_dokumen_nama'] == $val_dokumen['pekerjaan_dokumen_nama'] && $dokumen_ada['pekerjaan_dokumen_nomor'] == $val_dokumen['pekerjaan_dokumen_nomor'] && $dokumen_ada['id_bagian'] == $val_dokumen['id_bagian'])) {
          /*skip*/
        } else {
          $data['pekerjaan_dokumen_id_temp'] = $val_dokumen['pekerjaan_dokumen_id'];
          $data['pekerjaan_dokumen_id'] = uniqid();
          if ($avp_pic_jml > 0) {
            $data['pekerjaan_dokumen_status'] = '3';
            $data['is_proses'] = 'wc';
          } else {
            $data['pekerjaan_dokumen_status'] = '2';
            $data['is_proses'] = 'wa';
          }
          $data['pekerjaan_dokumen_keterangan'] = $val_dokumen['pekerjaan_dokumen_keterangan'];
          $data['pekerjaan_dokumen_status_doc'] = $val_dokumen['pekerjaan_dokumen_status_doc'];
          $data['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
          $data['id_create'] = $session['pegawai_nik'];
          $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
          $this->M_pekerjaan->simpanAksiDokumenKontraktorSama($data);

          $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen_transmital a WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();

          dblog('I', $this->input->post('pekerjaan_id'), 'Dokumen ' . $data_dokumen['pekerjaan_dokumen_nomor'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah Send', $session['pegawai_nik']);
        }

        $this->db->query("UPDATE dec.dec_pekerjaan_dokumen_transmital SET is_lama = 'y' WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status <= '1' AND id_bagian = '" . $val_dokumen['id_bagian'] . "'");
      }
    }
  }
  /* insert send */

  /* insert send cangun */
  public function insertSendCangun()
  {
    if ($this->input->get('id_user')) {
      $session = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'")->row_array();
    } else {
      $session = $this->session->userdata();
    }

    $data_pekerjaan['pekerjaan_status_transmital'] = '2';
    $this->M_pekerjaan->updatePekerjaan($data_pekerjaan, $this->input->post('pekerjaan_id'));

    $where_status['id_user'] = $session['pegawai_nik'];
    $where_status['id_pekerjaan'] = $this->input->post('pekerjaan_id');
    $where_status['disposisi_status'] = $this->input->post('pekerjaan_status');
    $param_status['is_proses'] = 'y';

    $this->M_pekerjaan->updateStatusTransmital($where_status, $param_status);

    $avp_bagian = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_bagian_detail b ON b.id_pegawai = a.id_user WHERE id_bagian IN(SELECT id_bagian FROM global.global_bagian_detail WHERE id_pegawai = '" . $session['pegawai_nik'] . "') AND id_pekerjaan = '" . $this->input->post('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '4'")->row_array();

    $data['pekerjaan_disposisi_transmital_id'] = uniqid();
    $data['pekerjaan_disposisi_transmital_waktu'] = date('Y-m-d H:i:s');
    $data['id_user'] = $avp_bagian['id_user'];
    $data['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
    $data['pekerjaan_disposisi_transmital_status'] = '3';
    $data['is_aktif'] = 'y';
    $data['id_bagian'] = $avp_bagian['id_bagian'];

    $this->M_pekerjaan->insertSendTransmital($data);

    dblog('I', $this->input->get_post('pekerjaan_id'), 'Pekerjaan Transmital Telah Disend Perencana ', $session['pegawai_nik']);

    /*perbarui status dokumen*/
    $dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen_transmital WHERE id_pekerjaan = '" . $this->input->post('pekerjaan_id') . "' AND pekerjaan_dokumen_awal = 'n' AND is_lama = 'n' AND pekerjaan_dokumen_status <='3' AND id_bagian = '" . $avp_bagian['id_bagian'] . "'")->result_array();

    foreach ($dokumen as $val_dokumen) {
      $dokumen_ada = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen_transmital WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_create_awal = '" . $val_dokumen['id_create_awal'] . "' AND pekerjaan_dokumen_status = '4' AND pekerjaan_dokumen_nama = '" . $val_dokumen['pekerjaan_dokumen_nama'] . "' AND pekerjaan_dokumen_nomor = '" . $val_dokumen['pekerjaan_dokumen_nomor'] . "'  AND id_bagian = '" . $val_dokumen['id_bagian'] . "'")->row_array();

      $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen_transmital WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
      $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];
      if (!empty($dokumen_ada) && ($dokumen_ada['pekerjaan_dokumen_nama'] == $val_dokumen['pekerjaan_dokumen_nama'] && $dokumen_ada['pekerjaan_dokumen_nomor'] == $val_dokumen['pekerjaan_dokumen_nomor'] && $dokumen_ada['id_bagian'] == $val_dokumen['id_bagian'])) {
        /*skip*/
      } else {
        $data['pekerjaan_dokumen_id_temp'] = $val_dokumen['pekerjaan_dokumen_id'];
        $data['pekerjaan_dokumen_id'] = uniqid();
        $data['pekerjaan_dokumen_status'] = '4';
        $data['pekerjaan_dokumen_keterangan'] = $val_dokumen['pekerjaan_dokumen_keterangan'];
        $data['pekerjaan_dokumen_status_doc'] = $val_dokumen['pekerjaan_dokumen_status_doc'];
        $data['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
        $data['id_create'] = $session['pegawai_nik'];
        $data['is_proses'] = 'wca';
        $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
        /*  */
        $this->M_pekerjaan->simpanAksiDokumenKontraktorSama($data);

        $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen_transmital a WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();

        dblog('I', $this->input->get_post('pekerjaan_id'), 'Dokumen ' . $data_dokumen['pekerjaan_dokumen_nomor'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah Send', $session['pegawai_nik']);
      }

      $this->db->query("UPDATE dec.dec_pekerjaan_dokumen_transmital SET is_lama = 'y' WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status<='3'");
    }

    // $this->db->query("UPDATE dec.dec_pekerjaan_dokumen_transmital SET is_lama = 'y' WHERE id_pekerjaan = '" . $this->input->post('pekerjaan_id') . "' AND pekerjaan_dokumen_status <= '3' AND id_bagian='" . $val_dokumen['id_bagian'] . "' ");

    /*perbarui status dokumen*/
  }
  /* insert send cangun */


  /* insert reviewed */
  public function insertReviewed()
  {
    if ($this->input->get('id_user')) {
      $session = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'")->row_array();
    } else {
      $session = $this->session->userdata();
    }

    /* set status ke y dahulu */
    $param_update['pekerjaan_disposisi_transmital_status'] = $this->input->get_post('pekerjaan_status');
    $param_update['id_user'] = $session['pegawai_nik'];
    $param_update['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
    $data_update['is_proses'] = 'y';
    $this->M_pekerjaan->updateDisposisiTransmital($param_update, $data_update);

    $data_pekerjaan['pekerjaan_status_transmital'] = '2';
    $this->M_pekerjaan->updatePekerjaan($data_pekerjaan, $this->input->post('pekerjaan_id'));

    /* ambil pekerjaan disposisi perencana */
    // $data_perencana = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '5' AND is_aktif = 'y'")->result_array();
    $data_perencana = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '5' AND is_aktif = 'y' AND id_user NOT IN (SELECT id_user FROM dec.dec_pekerjaan_disposisi_transmital WHERE pekerjaan_disposisi_transmital_status = '2' AND id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "')")->result_array();

    foreach ($data_perencana as $key_perencana => $value_perencana) :
      $bagian = $this->db->get_where('global.global_bagian_detail', array('id_pegawai' => $value_perencana['id_user']))->row_array();

      $data['pekerjaan_disposisi_transmital_id'] = uniqid();
      $data['pekerjaan_disposisi_transmital_waktu'] = date('Y-m-d H:i:s');
      $data['id_user'] = $value_perencana['id_user'];
      $data['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
      $data['pekerjaan_disposisi_transmital_status'] = '2';
      $data['is_aktif'] = 'y';
      $data['id_bagian'] = $bagian['id_bagian'];

      $this->M_pekerjaan->insertSendTransmital($data);
    endforeach;

    dblog('I', $this->input->get_post('pekerjaan_id'), 'Pekerjaan Transmital Telah diSend ke Perencana ', $session['pegawai_nik']);

    $dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen_transmital WHERE id_pekerjaan = '" . $this->input->post('pekerjaan_id') . "' AND pekerjaan_dokumen_awal = 'n' AND is_lama = 'n' AND pekerjaan_dokumen_status <='2'")->result_array();

    foreach ($dokumen as $val_dokumen) {
      $dokumen_ada = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen_transmital WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_create_awal = '" . $val_dokumen['id_create_awal'] . "' AND pekerjaan_dokumen_status = '3' AND pekerjaan_dokumen_nama = '" . $val_dokumen['pekerjaan_dokumen_nama'] . "' AND pekerjaan_dokumen_nomor = '" . $val_dokumen['pekerjaan_dokumen_nomor'] . "'  AND id_bagian = '" . $val_dokumen['id_bagian'] . "'")->row_array();

      $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen_transmital WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
      $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];
      if (!empty($dokumen_ada) && ($dokumen_ada['pekerjaan_dokumen_nama'] == $val_dokumen['pekerjaan_dokumen_nama'] && $dokumen_ada['pekerjaan_dokumen_nomor'] == $val_dokumen['pekerjaan_dokumen_nomor'] && $dokumen_ada['id_bagian'] == $val_dokumen['id_bagian'])) {
        /*skip*/
      } else {
        $data['pekerjaan_dokumen_id_temp'] = $val_dokumen['pekerjaan_dokumen_id'];
        $data['pekerjaan_dokumen_id'] = uniqid();
        $data['pekerjaan_dokumen_status'] = '3';
        $data['pekerjaan_dokumen_keterangan'] = $val_dokumen['pekerjaan_dokumen_keterangan'];
        $data['pekerjaan_dokumen_status_doc'] = $val_dokumen['pekerjaan_dokumen_status_doc'];
        $data['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
        $data['id_create'] = $session['pegawai_nik'];
        $data['is_proses'] = 'wc';
        $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
        /*  */
        $this->M_pekerjaan->simpanAksiDokumenKontraktorSama($data);

        $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen_transmital a WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();

        dblog('I', $this->input->get_post('pekerjaan_id'), 'Dokumen ' . $data_dokumen['pekerjaan_dokumen_nomor'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah Approve AVP', $session['pegawai_nik']);
      }

      $this->db->query("UPDATE dec.dec_pekerjaan_dokumen_transmital SET is_lama = 'y' WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status<='2'");
    }

    // $this->db->query("UPDATE dec.dec_pekerjaan_dokumen_transmital SET is_lama = 'y' WHERE id_pekerjaan = '" . $this->input->post('pekerjaan_id') . "' AND pekerjaan_dokumen_status <= '2'");
  }
  /* insert reviewed */

  /* insert reject */
  public function insertReject()
  {
    if ($this->input->get('id_user')) {
      $session = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'")->row_array();
    } else {
      $session = $this->session->userdata();
    }

    $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi_transmital WHERE id_pekerjaan = '" . $this->input->post('pekerjaan_id') . "'");

    dblog('I', $this->input->get_post('pekerjaan_id'), 'Pekerjaan Transmital Telah diReject PIC Waspro ', $session['pegawai_nik']);
  }
  /* insert reject */

  /* insert reviewed avp cagun*/
  public function insertReviewedCangun()
  {
    if ($this->input->get('id_user')) {
      $session = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'")->row_array();
    } else {
      $session = $this->session->userdata();
    }

    /* set status ke y dahulu */
    $param_update['pekerjaan_disposisi_transmital_status'] = $this->input->get_post('pekerjaan_status');
    $param_update['id_user'] = $session['pegawai_nik'];
    $param_update['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
    $data_update['is_proses'] = 'y';
    $this->M_pekerjaan->updateDisposisiTransmital($param_update, $data_update);

    $data_pekerjaan['pekerjaan_status_transmital'] = '4';
    $this->M_pekerjaan->updatePekerjaan($data_pekerjaan, $this->input->post('pekerjaan_id'));

    /* ambil data vp cangun */
    $vp = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '3' AND is_aktif = 'y'")->row_array();

    $avp_jml = $this->db->get_where('dec.dec_pekerjaan_disposisi_transmital', [
      'id_pekerjaan' => $this->input->post('pekerjaan_id'),
      'is_proses' => null,
      'pekerjaan_disposisi_transmital_status' => '3'
    ])->num_rows();

    $avp_bagian = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi_transmital WHERE id_pekerjaan = '" . $this->input->post('pekerjaan_id') . "' AND pekerjaan_disposisi_transmital_status = '3' AND id_user = '" . $session['pegawai_nik'] . "'")->row_array();

    if ($avp_jml == '0') {
      $data['pekerjaan_disposisi_transmital_id'] = uniqid();
      $data['pekerjaan_disposisi_transmital_waktu'] = date('Y-m-d H:i:s');
      $data['id_user'] = $vp['id_user'];
      $data['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
      $data['pekerjaan_disposisi_transmital_status'] = '4';
      $data['is_aktif'] = 'y';
      $this->M_pekerjaan->insertSendTransmital($data);

      dblog('I', $this->input->get_post('pekerjaan_id'), 'Pekerjaan Transmital Telah diReview AVP Cangun ', $session['pegawai_nik']);
    }

    $dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen_transmital WHERE id_pekerjaan = '" . $this->input->post('pekerjaan_id') . "' AND pekerjaan_dokumen_awal = 'n' AND is_lama = 'n' AND pekerjaan_dokumen_status <='4' AND id_bagian = '" . $avp_bagian['id_bagian'] . "'")->result_array();

    foreach ($dokumen as $val_dokumen) {
      $dokumen_ada = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen_transmital WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_create_awal = '" . $val_dokumen['id_create_awal'] . "' AND pekerjaan_dokumen_status = '5' AND pekerjaan_dokumen_nama = '" . $val_dokumen['pekerjaan_dokumen_nama'] . "' AND pekerjaan_dokumen_nomor = '" . $val_dokumen['pekerjaan_dokumen_nomor'] . "'  AND id_bagian = '" . $val_dokumen['id_bagian'] . "'")->row_array();

      echo $this->db->last_query();


      $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen_transmital WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
      $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];
      if (!empty($dokumen_ada) && ($dokumen_ada['pekerjaan_dokumen_nama'] == $val_dokumen['pekerjaan_dokumen_nama'] && $dokumen_ada['pekerjaan_dokumen_nomor'] == $val_dokumen['pekerjaan_dokumen_nomor'] && $dokumen_ada['id_bagian'] == $val_dokumen['id_bagian'])) {
        /*skip*/
      } else {
        $data['pekerjaan_dokumen_id_temp'] = $val_dokumen['pekerjaan_dokumen_id'];
        $data['pekerjaan_dokumen_id'] = uniqid();
        $data['pekerjaan_dokumen_status'] = '5';
        $data['pekerjaan_dokumen_keterangan'] = $val_dokumen['pekerjaan_dokumen_keterangan'];
        $data['pekerjaan_dokumen_status_doc'] = $val_dokumen['pekerjaan_dokumen_status_doc'];
        $data['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
        $data['id_create'] = $session['pegawai_nik'];
        $data['is_proses'] = 'wcv';
        $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
        /*  */
        $this->M_pekerjaan->simpanAksiDokumenKontraktorSama($data);

        $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen_transmital a WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();

        dblog('I', $this->input->get_post('pekerjaan_id'), 'Dokumen ' . $data_dokumen['pekerjaan_dokumen_nomor'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah Approve AVP Cangun', $session['pegawai_nik']);
      }
      $this->db->query("UPDATE dec.dec_pekerjaan_dokumen_transmital SET is_lama = 'y' WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status<='4'");
    }

    // $this->db->query("UPDATE dec.dec_pekerjaan_dokumen_transmital SET is_lama = 'y' WHERE id_pekerjaan = '" . $this->input->post('pekerjaan_id') . "' AND pekerjaan_dokumen_status <= '4' AND id_bagian = '" . $avp_bagian['id_bagian'] . "'");
  }
  /* insert reviewed avp cangun*/


  /* inset approve cangun */
  public function insertApprovedCangun()
  {
    if ($this->input->get('id_user')) {
      $session = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'")->row_array();
    } else {
      $session = $this->session->userdata();
    }

    /* set status ke y dahulu */
    $param_update['pekerjaan_disposisi_transmital_status'] = $this->input->get_post('pekerjaan_status');
    $param_update['id_user'] = $session['pegawai_nik'];
    $param_update['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
    $data_update['is_proses'] = 'y';
    $this->M_pekerjaan->updateDisposisiTransmital($param_update, $data_update);

    $data_pekerjaan['pekerjaan_status_transmital'] = '5';
    $this->M_pekerjaan->updatePekerjaan($data_pekerjaan, $this->input->post('pekerjaan_id'));

    /* ambil data avp waspro */
    $pic = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi_transmital WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND pekerjaan_disposisi_transmital_status = '0' AND is_aktif = 'y' AND id_user NOT IN (SELECT id_user FROM dec.dec_pekerjaan_disposisi_transmital where pekerjaan_disposisi_transmital_status = '5' AND id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "')")->result_array();

    foreach ($pic as $key => $value) :
      $data['pekerjaan_disposisi_transmital_id'] = uniqid();
      $data['pekerjaan_disposisi_transmital_waktu'] = date('Y-m-d H:i:s');
      $data['id_user'] = $value['id_user'];
      $data['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
      $data['pekerjaan_disposisi_transmital_status'] = '5';
      $data['is_aktif'] = 'y';
      $data['id_bagian'] = $value['id_bagian'];
      $this->M_pekerjaan->insertSendTransmital($data);
    endforeach;

    dblog('I', $this->input->get_post('pekerjaan_id'), 'Pekerjaan Transmital Telah Approve VP Cangun ', $session['pegawai_nik']);

    $dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen_transmital WHERE id_pekerjaan = '" . $this->input->post('pekerjaan_id') . "' AND pekerjaan_dokumen_awal = 'n' AND is_lama = 'n' AND pekerjaan_dokumen_status <='5'")->result_array();

    foreach ($dokumen as $val_dokumen) {
      $dokumen_ada = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen_transmital WHERE id_pekerjaan = '" . $this->input->get_post('pekerjaan_id') . "' AND id_create_awal = '" . $val_dokumen['id_create_awal'] . "' AND pekerjaan_dokumen_status = '6' AND pekerjaan_dokumen_nama = '" . $val_dokumen['pekerjaan_dokumen_nama'] . "' AND pekerjaan_dokumen_nomor = '" . $val_dokumen['pekerjaan_dokumen_nomor'] . "'  ")->row_array();

      $nomor_revisi = $this->db->query("SELECT max(pekerjaan_dokumen_revisi) as nomor_revisi FROM dec.dec_pekerjaan_dokumen_transmital WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();
      $nomor_revisi_baru = $nomor_revisi['nomor_revisi'];
      if (!empty($dokumen_ada) && ($dokumen_ada['pekerjaan_dokumen_nama'] == $val_dokumen['pekerjaan_dokumen_nama'] && $dokumen_ada['pekerjaan_dokumen_nomor'] == $val_dokumen['pekerjaan_dokumen_nomor'])) {
        /*skip*/
      } else {
        $data['pekerjaan_dokumen_id_temp'] = $val_dokumen['pekerjaan_dokumen_id'];
        $data['pekerjaan_dokumen_id'] = uniqid();
        $data['pekerjaan_dokumen_status'] = '6';
        $data['pekerjaan_dokumen_keterangan'] = $val_dokumen['pekerjaan_dokumen_keterangan'];
        $data['pekerjaan_dokumen_status_doc'] = $val_dokumen['pekerjaan_dokumen_status_doc'];
        $data['pekerjaan_dokumen_revisi'] = $nomor_revisi_baru;
        $data['id_create'] = $session['pegawai_nik'];
        $data['is_proses'] = 'ws';
        $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
        /*  */
        $this->M_pekerjaan->simpanAksiDokumenKontraktorSama($data);

        $data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen_transmital a WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "'")->row_array();

        dblog('I', $this->input->get_post('pekerjaan_id'), 'Dokumen ' . $data_dokumen['pekerjaan_dokumen_nomor'] . ' - ' . $data_dokumen['pekerjaan_dokumen_nama'] . ' Telah Approve AVP Cangun', $session['pegawai_nik']);
      }
      $this->db->query("UPDATE dec.dec_pekerjaan_dokumen_transmital SET is_lama = 'y' WHERE pekerjaan_dokumen_id = '" . $val_dokumen['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status<='5'");
    }
  }
  /* inset approve cangun */

  /* insert reject cangun */
  public function insertRejectCangun()
  {
    if ($this->input->get('id_user')) {
      $session = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'")->row_array();
    } else {
      $session = $this->session->userdata();
    }

    if ($this->input->post('pekerjaan_status') == '3') :
      /* set perencana null */
      $perencana = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi_transmital WHERE id_pekerjaan = '" . $this->input->post('pekerjaan_id') . "' AND pekerjaan_disposisi_transmital_status ='2' AND id_bagian IN(SELECT id_bagian FROM dec.dec_pekerjaan_disposisi_transmital WHERE id_pekerjaan = '" . $this->input->post('pekerjaan_id') . "' AND id_user = '" . $session['pegawai_nik'] . "')")->result_array();
      foreach ($perencana as $key => $value) :
        $param_update['pekerjaan_disposisi_transmital_status'] = '2';
        $param_update['id_user'] = $value['id_user'];
        $param_update['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
        $data_update['is_proses'] = null;
        $data_update['pekerjaan_disposisi_transmital_catatan'] = $this->input->post('note_reject');
        $this->M_pekerjaan->updateDisposisiTransmital($param_update, $data_update);
      endforeach;

      /* hapus data avp */
      $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi_transmital WHERE id_pekerjaan = '" . $this->input->post('pekerjaan_id') . "' AND id_user='" . $session['pegawai_nik'] . "' AND pekerjaan_disposisi_transmital_status ='" . $this->input->post('pekerjaan_status') . "'");

      dblog('I', $this->input->get_post('pekerjaan_id'), 'Pekerjaan Telah Direject AVP Cangun', $session['pegawai_nik']);

    elseif ($this->input->post('pekerjaan_status') == '4') :

      $avp = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi_transmital WHERE id_pekerjaan ='" . $this->input->post('pekerjaan_id') . "' AND pekerjaan_disposisi_transmital_status ='3' ")->result_array();

      foreach ($avp as $key => $value) :
        $perencana = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi_transmital WHERE id_pekerjaan = '" . $this->input->post('pekerjaan_id') . "' AND pekerjaan_disposisi_transmital_status ='2' AND id_bagian IN(SELECT id_bagian FROM dec.dec_pekerjaan_disposisi_transmital WHERE id_pekerjaan = '" . $this->input->post('pekerjaan_id') . "' AND id_user = '" . $value['id_user'] . "')")->result_array();
        foreach ($perencana as $key => $value2) :
          $param_update['pekerjaan_disposisi_transmital_status'] = '2';
          $param_update['id_user'] = $value2['id_user'];
          $param_update['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
          $data_update['is_proses'] = null;
          $data_update['pekerjaan_disposisi_transmital_catatan'] = $this->input->post('note_reject');
          $this->M_pekerjaan->updateDisposisiTransmital($param_update, $data_update);
        endforeach;
        $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi_transmital WHERE id_pekerjaan = '" . $this->input->post('pekerjaan_id') . "' AND id_user ='" . $value['id_user'] . "' AND pekerjaan_disposisi_transmital_status = '3'");
      endforeach;

      $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi_transmital WHERE id_pekerjaan = '" . $this->input->post('pekerjaan_id') . "' AND id_user ='" . $session['pegawai_nik'] . "' AND pekerjaan_disposisi_transmital_status = '4'");

      dblog('I', $this->input->get_post('pekerjaan_id'), 'Pekerjaan Telah Direject VP Cangun', $session['pegawai_nik']);

    endif;
  }
  /* insert reject cangun */

  public function insertKembaliUpload()
  {
    try {
      if ($this->input->get('id_user')) {
        $session = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'")->row_array();
      } else {
        $session = $this->session->userdata();
      }

      $disposisi = $this->db->get_where(
        'dec.dec_pekerjaan_disposisi_transmital',
        [
          'id_pekerjaan' => $this->input->post('pekerjaan_id'),
          'id_user' => $session['pegawai_nik'],
          'pekerjaan_disposisi_transmital_status' => '5',
        ]
      )->result_array();

      $this->db->query("UPDATE dec.dec_pekerjaan SET pekerjaan_is_selesai = 'n' WHERE pekerjaan_id = '" . $this->input->post('pekerjaan_id') . "'");

      // $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi_transmital WHERE pekerjaan_disposisi_transmital_status = '1' AND id_pekerjaan = '".$this->input->post('pekerjaan_id')."'");
      // $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi_transmital WHERE pekerjaan_disposisi_transmital_status = '4' AND id_pekerjaan = '".$this->input->post('pekerjaan_id')."'");

      $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi_transmital WHERE id_pekerjaan = '" . $this->input->post('pekerjaan_id') . "' AND CAST(pekerjaan_disposisi_transmital_status AS INT) > 0");
      $this->db->query("UPDATE dec.dec_pekerjaan_disposisi_transmital SET is_proses = null, pekerjaan_disposisi_transmital_catatan = '" . $this->input->post('note_reject') . "' WHERE id_pekerjaan = '" . $this->input->post('pekerjaan_id') . "' AND CAST(pekerjaan_disposisi_transmital_status AS INT) = 0");
      // foreach ($disposisi as $value) {

      // }
        dblog('I', $this->input->post('pekerjaan_id'), 'Pekerjaan Transmital Telah Dikembalikan Untuk Upload Ulang ', $session['pegawai_nik']);
      echo json_encode(['success' => true, 'message' => 'Berhasil']);
    } catch (Exception $e) {
      echo json_encode(['success' => false, 'message' => 'Error ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine()]);
    }
  }

  /* insert reviewed */
  public function insertApproveSelesai()
  {
    if ($this->input->get('id_user')) {
      $session = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'")->row_array();
    } else {
      $session = $this->session->userdata();
    }

    /* set status ke y dahulu */
    $param_update['pekerjaan_disposisi_transmital_status'] = $this->input->get_post('pekerjaan_status');
    $param_update['id_user'] = $session['pegawai_nik'];
    $param_update['id_pekerjaan'] = $this->input->get_post('pekerjaan_id');
    $data_update['is_proses'] = 'y';

    $this->M_pekerjaan->updateDisposisiTransmital($param_update, $data_update);

    /*update status dokumen*/
    $dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $this->input->post('pekerjaan_id') . "' AND is_lama = 'n' AND pekerjaan_dokumen_awal = 'n' AND CAST(pekerjaan_dokumen_status AS INT) = '13'")->result_array();
    foreach ($dokumen as $key => $dok) {
      $data_dokumen['pekerjaan_dokumen_id_temp'] = anti_inject($dok['pekerjaan_dokumen_id']);
      $data_dokumen['pekerjaan_dokumen_id'] = anti_inject(uniqid());
      $data_dokumen['pekerjaan_dokumen_status'] = anti_inject('14');
      $data_dokumen['pekerjaan_dokumen_revisi'] = $dok['pekerjaan_dokumen_revisi'];
      $data_dokumen['pekerjaan_dokumen_status_doc'] = $dok['pekerjaan_dokumen_status_doc'];
      $data_dokumen['pekerjaan_dokumen_keterangan'] = ($this->input->post('pekerjaan_dokumen_keterangan') != '') ? $this->input->post('pekerjaan_dokumen_keterangan') : $dok['pekerjaan_dokumen_keterangan'];
      $data_dokumen['id_create'] = $user['pegawai_nik'];
      $data_dokumen['is_proses'] = 'dt';
      $data_dokumen['id_create_awal'] = $dok['id_create_awal'];
      $data_dokumen['pekerjaan_dokumen_jumlah'] = $dok['pekerjaan_dokumen_jumlah'];
      $data_dokumen['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
      $this->M_pekerjaan->simpanAksiSama($data_dokumen);

      $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET is_lama = 'y' WHERE pekerjaan_dokumen_id = '" . $dok['pekerjaan_dokumen_id'] . "'");
    }
    dblog('I', $this->input->get_post('pekerjaan_id'), 'Pekerjaan Transmital Telah Approved ', $session['pegawai_nik']);
  }
  /* insert reviewed */


  /* upload file dokumen transmital */
  public function aksiFileDokumenTransmital()
  {
    if (isset($_FILES['file'])) {
      $directory = './document/';
      if (!file_exists($directory)) mkdir($directory);

      $tmpFile = $_FILES['file']['tmp_name'];
      $fileName = $_FILES['file']['name'];
      $fIleType = $_FILES['file']['type'];

      if (!empty($tmpFile)) {
        $Extension    = array("jpeg", "jpg", "png", "bmp", "gif", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf");
        $fileExt      = substr($fileName, strrpos($fileName, '.'));
        $fileExt      = str_replace('.', '', $fileExt); /* Extension*/
        $fileName     = preg_replace("/\.[^.\s]{3,4}$/", "", $fileName);
        $newFileName  = str_replace(' ', '', $this->input->post('id_pekerjaan') . '_' . date('ymdhis') . '_' . uniqid() . '.' . $fileExt);

        if (in_array(strtolower($fileExt), $Extension)) {
          move_uploaded_file($tmpFile, $directory . $newFileName);
          echo $newFileName;
        }
      }
    }
  }
  /* upload file dokumen transmital */

  /* crud aksi dokumen kontraktor */
  public function aksiDokumenKontraktor()
  {
    if ($this->input->get('id_user')) {
      $session = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'")->row_array();
    } else {
      $session = $this->session->userdata();
    }

    if ($this->input->post('tanggal_dokumen_input')) {
      $tanggal = date('Y-m-d H:i:s', strtotime($this->input->post('tanggal_dokumen_input')));
    } else {
      $tanggal = date('Y-m-d H:i:s');
    }

    if ($this->input->get('opsi') == 'baru') {
      $data['pekerjaan_dokumen_id'] = uniqid();
      $data['id_pekerjaan'] = $this->input->get('id_pekerjaan');
      $data['pekerjaan_dokumen_file'] = $this->input->post('savedFileName');
      $data['pekerjaan_dokumen_nama'] = $this->input->post('pekerjaan_dokumen_nama');
      $data['pekerjaan_dokumen_kertas'] = $this->input->post('pekerjaan_dokumen_kertas');
      $data['pekerjaan_dokumen_orientasi'] = $this->input->post('pekerjaan_dokumen_orientasi');
      $data['pekerjaan_dokumen_jenis'] = $this->input->post('pekerjaan_dokumen_jenis');
      $data['pekerjaan_dokumen_awal'] = 'n';
      $data['pekerjaan_dokumen_status'] = '1';
      $data['who_create'] = $session['pegawai_nama'];
      $data['is_lama'] = 'n';
      $data['id_create'] = $session['pegawai_nik'];
      $data['id_create_awal'] = $session['pegawai_nik'];
      $data['pekerjaan_dokumen_nomor'] = $this->input->post('pekerjaan_dokumen_nomor');
      $data['pekerjaan_dokumen_waktu'] = $tanggal;
      $data['pekerjaan_dokumen_waktu_input'] = $tanggal;
      $data['id_dokumen_awal'] = uniqid();
      $data['id_bagian'] = $this->input->get('id_bagian');
      $data['is_change'] = null;
      $this->M_pekerjaan->insertDokumenTransmital($data);

      dblog('I', $this->input->get_post('id_pekerjaan'), 'Dokumen Transmital - ' . $this->input->get_post('pekerjaan_dokumen_nama') . ' Telah Diupload', $session['pegawai_nik']);
    } else if ($this->input->get('opsi') == 'edit') {
      $id = $this->input->post('pekerjaan_dokumen_id');
      $data['id_pekerjaan'] = $this->input->get('id_pekerjaan');
      if ($this->input->post('savedFileName')) {
        $data['pekerjaan_dokumen_file'] = $this->input->post('savedFileName');
      }
      $data['pekerjaan_dokumen_nama'] = $this->input->post('pekerjaan_dokumen_nama');
      $data['pekerjaan_dokumen_kertas'] = $this->input->post('pekerjaan_dokumen_kertas');
      $data['pekerjaan_dokumen_orientasi'] = $this->input->post('pekerjaan_dokumen_orientasi');
      $data['pekerjaan_dokumen_jenis'] = $this->input->post('pekerjaan_dokumen_jenis');
      $data['pekerjaan_dokumen_awal'] = 'n';
      $data['pekerjaan_dokumen_status'] = '1';
      $data['who_create'] = $session['pegawai_nama'];
      $data['is_lama'] = 'n';
      $data['id_create'] = $session['pegawai_nik'];
      $data['id_create_awal'] = $session['pegawai_nik'];
      $data['pekerjaan_dokumen_nomor'] = $this->input->post('pekerjaan_dokumen_nomor');
      $data['pekerjaan_dokumen_waktu'] = $tanggal;
      $data['pekerjaan_dokumen_waktu_input'] = $tanggal;
      $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
      $data['id_bagian'] = $this->input->get('id_bagian');
      $data['is_change'] = null;
      $this->M_pekerjaan->updateDokumenTransmital($id, $data);

      dblog('U', $this->input->get_post('id_pekerjaan'), 'Dokumen Transmital - ' . $this->input->get_post('pekerjaan_dokumen_nama') . ' Telah Diedit', $session['pegawai_nik']);
    } else if ($this->input->get('opsi') == 'hapus') {
      $dokumen = $this->db->get_where('dec.dec_pekerjaan_dokumen_transmital', ['pekerjaan_dokumen_id' => $this->input->get_post('pekerjaan_dokumen_id')])->row_array();

      dblog('D', $this->input->get_post('id_pekerjaan'), 'Dokumen Transmital - ' . $dokumen['pekerjaan_dokumen_nama'] . ' Telah Dihapus', $session['pegawai_nik']);

      $this->M_pekerjaan->deleteDokumenTransmital($this->input->post('pekerjaan_dokumen_id'));
    }
    /* crud aksi dokumen kontraktor */
  }

  /*approval aksi dokumem kontraktor */
  public function simpanAksiDokumenKontraktor()
  {
    if ($this->input->get('id_user')) {
      $session = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'")->row_array();
    } else {
      $session = $this->session->userdata();
    }

    $dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen_transmital WHERE pekerjaan_dokumen_id = '" . $this->input->post('pekerjaan_dokumen_id') . "' ")->row_array();
    $dokumen_statusnya = $dokumen['pekerjaan_dokumen_status'] + 1;

    if ($this->input->post('pekerjaan_dokumen_status') == 'n') {
      if ($dokumen['pekerjaan_dokumen_revisi'] != null) {
        $dokumen_revisinya = $dokumen['pekerjaan_dokumen_revisi'] + 1;
      } else {
        $dokumen_revisinya = 1;
      }
    } else {
      $dokumen_revisinya = null;
    }

    if (isset($_FILES['pekerjaan_dokumen_file'])) {
      $temp = "./document/";
      if (!file_exists($temp)) mkdir($temp);
      $fileupload      = $_FILES['pekerjaan_dokumen_file']['tmp_name'];
      $ImageName       = $_FILES['pekerjaan_dokumen_file']['name'];
      $ImageType       = $_FILES['pekerjaan_dokumen_file']['type'];
      if (!empty($fileupload)) {
        $Extension    = array("jpeg", "jpg", "png", "bmp", "gif", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf");
        $acak           = uniqid();
        $ImageExt       = substr($ImageName, strrpos($ImageName, '.'));
        $ImageExt       = str_replace('.', '', $ImageExt);
        $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
        $NewImageName   = str_replace(' ', '', $this->input->get_post('pekerjaan_dokumen_id') . '_' . date('ymdhis') . '_' . $acak . '.' . $ImageExt);

        if (in_array($ImageExt, $Extension)) {
          move_uploaded_file($_FILES["pekerjaan_dokumen_file"]["tmp_name"], $temp . $NewImageName); /* Menyimpan file*/
        }
      }
    } else {
      $dokumen_edit = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen_transmital WHERE pekerjaan_dokumen_id = '" . $this->input->get_post('pekerjaan_dokumen_id') . "'")->row_array();
      if ($this->input->post('is_change') == 'y') {
        $this->db->query("UPDATE dec.dec_pekerjaan_dokumen_transmital SET is_change = 'y' WHERE pekerjaan_dokumen_id = '" . $this->input->get_post('pekerjaan_dokumen_id') . "'");
        $NewImageName = $dokumen_statusnya . '_' . $dokumen_edit['pekerjaan_dokumen_file'];
      } else {
        $NewImageName = null;
      }
    }

    /* cek apakah direvisi atau tidak */
    // if ($this->input->post('pekerjaan_dokumen_status') == 'y') {
    $status_dokumen = $dokumen_statusnya;
    // } else {
    //   $status_dokumen = 0;
    // }

    if ($this->input->get('opsi') == 'waspro_avp') {
      $proses = 'wc';
    } else if ($this->input->get('opsi') == 'waspro_cangun') {
      $proses = 'wca';
    } else if ($this->input->get('opsi') == 'waspro_cangun_avp') {
      $proses = 'wcv';
    } else if ($this->input->get('opsi') == 'waspro_cangun_vp') {
      $proses = 'ws';
    }

    /* insert dokumen dengan data sama dengan yang sebelumnya */
    /* jika tidak melakukan perubahan file */
    if ($NewImageName == null) {
      $data['pekerjaan_dokumen_id_temp'] = $dokumen['pekerjaan_dokumen_id'];
      $data['pekerjaan_dokumen_id'] = uniqid();
      $data['id_pekerjaan'] = $dokumen['id_pekerjaan'];
      $data['pekerjaan_dokumen_status'] = $status_dokumen;
      $data['pekerjaan_dokumen_keterangan'] = $this->input->post('pekerjaan_dokumen_keterangan');
      $data['is_lama'] = 'n';
      $data['pekerjaan_dokumen_revisi'] = $dokumen_revisinya;
      $data['pekerjaan_dokumen_status_doc'] = $this->input->post('pekerjaan_dokumen_status');
      $data['pekerjaan_dokumen_waktu'] = $this->input->post('pekerjaan_dokumen_waktu');
      $data['id_create'] = $session['pegawai_nik'];
      $data['is_proses'] = $proses;
      $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
      $data['id_bagian'] = $dokumen['id_bagian'];

      $this->M_pekerjaan->simpanAksiDokumenKontraktorSama($data);
    } else {
      /* jika melakukan perubahan file */
      $data['pekerjaan_dokumen_id_temp'] = $dokumen['pekerjaan_dokumen_id'];
      $data['pekerjaan_dokumen_id'] = uniqid();
      $data['id_pekerjaan'] = $dokumen['id_pekerjaan'];
      $data['pekerjaan_dokumen_file'] = $NewImageName;
      $data['pekerjaan_dokumen_status'] = $status_dokumen;
      $data['pekerjaan_dokumen_keterangan'] = $this->input->post('pekerjaan_dokumen_keterangan');
      $data['pekerjaan_dokumen_status_doc'] = $this->input->post('pekerjaan_dokumen_status');
      $data['pekerjaan_dokumen_waktu'] = $this->input->post('pekerjaan_dokumen_waktu');
      $data['is_lama'] = 'n';
      $data['pekerjaan_dokumen_revisi'] = $dokumen_revisinya;
      $data['id_create'] = $session['pegawai_nik'];
      $data['is_proses'] = $proses;
      $data['pekerjaan_dokumen_waktu_update'] = date('Y-m-d H:i:s');
      $data['id_bagian'] = $dokumen['id_bagian'];

      $this->M_pekerjaan->simpanAksiDokumenKontraktor($data);
    }

    if ($this->input->get('opsi') == 'waspro_avp') {
      dblog('I', $this->input->get_post('pekerjaan_id'), 'Dokumen ' . $dokumen['pekerjaan_dokumen_nomor'] . ' - ' . $dokumen['pekerjaan_dokumen_nama'] . ' Telah Approve AVP', $session['pegawai_nik']);
    } else if ($this->input->get('opsi') == 'waspro_cangun') {
      dblog('I', $this->input->get_post('pekerjaan_id'), 'Dokumen ' . $dokumen['pekerjaan_dokumen_nomor'] . ' - ' . $dokumen['pekerjaan_dokumen_nama'] . ' Telah Reviewed Perencana Cangun', $session['pegawai_nik']);
    } else if ($this->input->get('opsi') == 'waspro_cangun_avp') {
      dblog('I', $this->input->get_post('pekerjaan_id'), 'Dokumen ' . $dokumen['pekerjaan_dokumen_nomor'] . ' - ' . $dokumen['pekerjaan_dokumen_nama'] . ' Telah Reviewed AVP Cangun', $session['pegawai_nik']);
    } else if ($this->input->get('opsi') == 'waspro_cangun_vp') {
      dblog('I', $this->input->get_post('pekerjaan_id'), 'Dokumen ' . $dokumen['pekerjaan_dokumen_nomor'] . ' - ' . $dokumen['pekerjaan_dokumen_nama'] . ' Telah Approve VP Cangun', $session['pegawai_nik']);
    }

    $this->db->query("UPDATE dec.dec_pekerjaan_dokumen_transmital SET is_lama = 'y' WHERE pekerjaan_dokumen_id = '" . $dokumen['pekerjaan_dokumen_id'] . "' AND pekerjaan_dokumen_status <= '" . $dokumen['pekerjaan_dokumen_status'] . "'");
    /* approval aksi dokumen */
  }

  public function uploadDokumen()
  {
    /*status*/
    $dokumen_status = $this->db->query("SELECT pekerjaan_dokumen_status FROM dec.dec_pekerjaan_dokumen_transmital WHERE pekerjaan_dokumen_id = '" . $this->input->post('pekerjaan_dokumen_id') . "' ")->row_array();
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

  public function insertClose()
  {
    if ($this->input->get('id_user')) {
      $session = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $this->input->get('id_user') . "'")->row_array();
    } else {
      $session = $this->session->userdata();
    }

    $data_pekerjaan['pekerjaan_status_transmital'] = '5';
    $data_pekerjaan['pekerjaan_is_selesai'] = 'y';
    $this->M_pekerjaan->updatePekerjaan($data_pekerjaan, $this->input->post('pekerjaan_id'));

    dblog('I', $this->input->get_post('pekerjaan_id'), 'Pekerjaan Transmital Telah Close ', $session['pegawai_nik']);
  }
}



/* End of file RKAP.php */
