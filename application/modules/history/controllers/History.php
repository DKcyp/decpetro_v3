<?php
defined('BASEPATH') or exit('No direct script access allowed');

class History extends MX_Controller {
  public function __construct() {
    parent::__construct();

    if (empty($this->session->userdata('pegawai_nik'))) redirect(base_url('login'));

    $this->load->model('M_history');
  }

  public function index() {
    $data = array();
    $this->template->template_master('history/history', $data);
  }

  public function getHistory() {
    $param['pegawai_nik'] = $this->session->userdata('pegawai_nik');
    $param['pegawai_id_dep'] = $this->session->userdata('pegawai_id_dep');
    $param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
    $data = $this->M_history->getHistory($param);

    echo json_encode($data);
  }
}