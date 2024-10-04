<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Klasifikasi_dokumen extends MX_Controller {
  public function __construct() {
    parent::__construct();

    if (empty($this->session->userdata('pegawai_nik'))) redirect(base_url('login'));

    $this->load->model('M_klasifikasi_dokumen');
    $this->load->model('M_user');
  }

  public function index() {
    $data = array();
    $this->template->template_master('master/klasifikasi_dokumen', $data);
  }

  public function getKlasifikasiDokumen() {
    $param['klasifikasi_dokumen_id'] = $this->input->get('klasifikasi_dokumen_id');
    $data = $this->M_klasifikasi_dokumen->getKlasifikasiDokumen($param);

    echo json_encode($data);
  }

  public function getUserListRB() {
    $isi = $this->session->userdata();
    $list['results'] = array();

    $param['pegawai_nama'] = $this->input->get('pegawai_nama');
    $param['pegawai_poscode'] = $isi['pegawai_poscode'];
    foreach ($this->M_user->getUserSelect2($param) as $key => $value) {
      array_push($list['results'], [
        'id' => $value['pegawai_nik'],
        'text' => $value['pegawai_nama'] . ' - ' . $value['pegawai_postitle'],
      ]);
    }
    echo json_encode($list);
  }

  public function insertKlasifikasiDokumen() {
    $cek['id_pegawai'] = anti_inject($this->input->get_post('pegawai_nik'));
    $data = $this->M_klasifikasi_dokumen->getKlasifikasiDokumen($cek);

    if ($data['klasifikasi_dokumen_id'] != '') {
      $id = anti_inject($data['klasifikasi_dokumen_id']);
      $param['id_pegawai']  = anti_inject($this->input->get_post('pegawai_nik'));
      $param['klasifikasi_dokumen_inisial'] = anti_inject($this->input->get_post('klasifikasi_dokumen_inisial'));

      $upload_path = FCPATH.'/document/signature/';
      if (!empty($_FILES['signature_pegawai']['name'])) {
        $tmpName = $_FILES['signature_pegawai']['tmp_name'];
        $fileName = $_FILES['signature_pegawai']['name'];
        $fileType = $_FILES['signature_pegawai']['type'];

        $fileExt = substr($fileName, strpos($fileName, '.'));
        $fileExt = str_replace('.', '', $fileExt); // Extension
        $fileName = preg_replace("/\.[^.\s]{3,4}$/", "", $fileName);
        $newFileName = str_replace(' ', '', $this->input->get_post('pegawai_nik') . '_' . date('YmdHis') . '.' . $fileExt);
        move_uploaded_file($tmpName, $upload_path . $newFileName);

        $param['signature_pegawai'] = $newFileName;
      }

      $this->M_klasifikasi_dokumen->updateKlasifikasiDokumen($param, $id);
    } else {
      $param['klasifikasi_dokumen_id'] = anti_inject(create_id());
      $param['id_pegawai']  = anti_inject($this->input->get_post('pegawai_nik'));
      $param['klasifikasi_dokumen_inisial'] = anti_inject($this->input->get_post('klasifikasi_dokumen_inisial'));

      $upload_path = FCPATH.'/document/signature/';
      if (!empty($_FILES['signature_pegawai']['name'])) {
        $tmpName = $_FILES['signature_pegawai']['tmp_name'];
        $fileName = $_FILES['signature_pegawai']['name'];
        $fileType = $_FILES['signature_pegawai']['type'];

        $fileExt = substr($fileName, strpos($fileName, '.'));
        $fileExt = str_replace('.', '', $fileExt); // Extension
        $fileName = preg_replace("/\.[^.\s]{3,4}$/", "", $fileName);
        $newFileName = str_replace(' ', '', $this->input->get_post('pegawai_nik') . '_' . date('YmdHis') . '.' . $fileExt);
        move_uploaded_file($tmpName, $upload_path . $newFileName);

        $param['signature_pegawai'] = $newFileName;
      }

      $this->M_klasifikasi_dokumen->insertKlasifikasiDokumen($param);
    }
  }

  public function updateKlasifikasiDokumen() {
    $id = anti_inject($this->input->get_post('klasifikasi_dokumen_id'));
    $param['id_pegawai']  = anti_inject($this->input->get_post('pegawai_nik'));
    $param['klasifikasi_dokumen_inisial'] = anti_inject($this->input->get_post('klasifikasi_dokumen_inisial'));

    $upload_path = FCPATH.'/document/signature/';
    if (!empty($_FILES['signature_pegawai']['name'])) {
      $tmpName = $_FILES['signature_pegawai']['tmp_name'];
      $fileName = $_FILES['signature_pegawai']['name'];
      $fileType = $_FILES['signature_pegawai']['type'];

      $fileExt = substr($fileName, strpos($fileName, '.'));
      $fileExt = str_replace('.', '', $fileExt); // Extension
      $fileName = preg_replace("/\.[^.\s]{3,4}$/", "", $fileName);
      $newFileName = str_replace(' ', '', $this->input->get_post('pegawai_nik') . '_' . date('YmdHis') . '.' . $fileExt);
      move_uploaded_file($tmpName, $upload_path . $newFileName);

      $param['signature_pegawai'] = $newFileName;
    }

    $this->M_klasifikasi_dokumen->updateKlasifikasiDokumen($param, $id);
  }

  public function deleteKlasifikasiDokumen() {
    $id = $this->input->get_post('klasifikasi_dokumen_id');
    $this->M_klasifikasi_dokumen->deleteKlasifikasiDokumen($id);
  }
}
