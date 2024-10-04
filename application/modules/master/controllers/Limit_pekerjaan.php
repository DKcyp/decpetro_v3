<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Limit_pekerjaan extends MX_Controller {
  public function __construct() {
    parent::__construct();

    if (empty($this->session->userdata('pegawai_nik'))) redirect(base_url('login'));

    $this->load->model('M_limit_pekerjaan');
  }

  public function index() {
    $data = array();
    $this->template->template_master('master/limit_pekerjaan',$data);
  }

  public function getLimitPekerjaan() {
    $param['limit_pekerjaan_id'] = $this->input->get('limit_pekerjaan_id');
    $data = $this->M_limit_pekerjaan->getlimitPekerjaan($param);
    echo json_encode($data);
  }

  public function insertlimitPekerjaan() {
    $p['limit_pekerjaan_id']  = anti_inject(uniqid());
    $p['bagian_kode'] = anti_inject($this->input->get_post('bagian_kode'));
    $p['bagian_nama'] = anti_inject($this->input->get_post('bagian_nama'));
    $p['limit_pekerjaan_total'] = anti_inject($this->input->get_post('limit_pekerjaan_total'));

    $this->M_limit_pekerjaan->insertLimitPekerjaan($p);
  }

  public function updatelimitPekerjaan() {
    $id = anti_inject($this->input->get_post('limit_pekerjaan_id'));
    $p['bagian_kode'] = anti_inject($this->input->get_post('bagian_kode'));
    $p['bagian_nama'] = anti_inject($this->input->get_post('bagian_nama'));
    $p['limit_pekerjaan_total'] = anti_inject($this->input->get_post('limit_pekerjaan_total'));
    $this->M_limit_pekerjaan->updateLimitPekerjaan($id, $p);
  }

  public function deleteLimitPekerjaan() {
    $id = $this->input->get_post('limit_pekerjaan_id');
    $this->M_limit_pekerjaan->deleteLimitPekerjaan($id);
  }
}