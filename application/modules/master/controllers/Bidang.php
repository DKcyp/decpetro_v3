<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Bidang extends MX_Controller {
  public function __construct() {
    parent::__construct();

    if (empty($this->session->userdata('pegawai_nik'))) redirect(base_url('login'));

    $this->load->model('M_bidang');
  }

  public function index() {
    $data = array();
    $this->template->template_master('master/bidang',$data);
  }

  public function getBidang() {
    $param['bidang_id'] = $this->input->get('bidang_id');
    $data = $this->M_bidang->getBidang($param);
    echo json_encode($data);
  }

  public function insertBidang() {
    $param['bidang_id']  = anti_inject(create_id());
    $param['bidang_nama'] = anti_inject($this->input->get_post('bidang_nama'));
    $param['bidang_kode'] = anti_inject($this->input->get_post('bidang_kode'));

    $this->M_bidang->insertBidang($param);
  }

  public function updateBidang() {
    $id = anti_inject($this->input->get_post('bidang_id'));
    $param['bidang_nama'] = anti_inject($this->input->get_post('bidang_nama'));
    $param['bidang_kode'] = $this->input->get_post('bidang_kode');

    $this->M_bidang->updateBidang($param, $id);
  }

  public function deleteBidang() {
    $id = $this->input->get_post('bidang_id');
    $this->M_bidang->deleteBidang($id);
  }
}