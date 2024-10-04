<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Klasifikasi_pekerjaan extends MX_Controller {
  public function __construct() {
    parent::__construct();

    if (empty($this->session->userdata('pegawai_nik'))) redirect(base_url('login'));

    $this->load->model('M_klasifikasi_pekerjaan');
  }

  public function index() {
    $data = array();
    $this->template->template_master('master/klasifikasi_pekerjaan', $data);
  }

  public function getKlasifikasiPekerjaan()  {
    $param['klasifikasi_pekerjaan_id'] = $this->input->get('klasifikasi_pekerjaan_id');

    $data = $this->M_klasifikasi_pekerjaan->getKlasifikasiPekerjaan($param);
    echo json_encode($data);
  }

  public function insertKlasifikasiPekerjaan() {
    $data['klasifikasi_pekerjaan_id'] = anti_inject(create_id());
    $data['klasifikasi_pekerjaan_nama'] = anti_inject($this->input->post('klasifikasi_pekerjaan_nama'));
    $data['klasifikasi_pekerjaan_kode'] = anti_inject(strtolower($this->input->post('klasifikasi_pekerjaan_nama')));
    $data['klasifikasi_pekerjaan_rkap'] = anti_inject($this->input->post('klasifikasi_pekerjaan_rkap'));

    $this->M_klasifikasi_pekerjaan->insertKlasifikasiPekerjaan($data);
  }

  public function updateKlasifikasiPekerjaan() {
    $klasifikasi_pekerjaan_id = anti_inject($this->input->post('klasifikasi_pekerjaan_id'));
    if ($klasifikasi_pekerjaan_id) {
      $data = array(
        'klasifikasi_pekerjaan_nama' => anti_inject($this->input->post('klasifikasi_pekerjaan_nama')),
        'klasifikasi_pekerjaan_kode' => anti_inject(strtolower($this->input->post('klasifikasi_pekerjaan_nama'))),
        'klasifikasi_pekerjaan_rkap' => anti_inject($this->input->post('klasifikasi_pekerjaan_rkap')),
      );

      $this->M_klasifikasi_pekerjaan->updateKlasifikasiPekerjaan($data, $klasifikasi_pekerjaan_id);
    }
  }

  public function deleteKlasifikasiPekerjaan() {
    $this->M_klasifikasi_pekerjaan->deleteKlasifikasiPekerjaan($this->input->get('klasifikasi_pekerjaan_id'));
    echo json_encode(0);
  }
}
