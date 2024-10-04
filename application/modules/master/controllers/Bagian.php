<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Bagian extends MX_Controller {
  public function __construct() {
    parent::__construct();

    if (empty($this->session->userdata('pegawai_nik'))) redirect(base_url('login'));

    $this->load->model('M_Bagian');
  }

  public function index() {
    $data = array();
    $this->template->template_master('master/bagian', $data);
  }

  public function getBagian() {
    $param['bagian_id'] = $this->input->get_post('bagian_id');
    $data = $this->M_Bagian->getBagian($param);

    echo json_encode($data);
  }

  public function insertBagian() {
    $param['bagian_id'] = anti_inject(create_id());
    $param['bagian_nama'] = anti_inject($this->input->get_post('bagian_nama'));

    $this->M_Bagian->insertBagian($param);
  }

  public function updateBagian() {
    $id = anti_inject($this->input->get_post('bagian_id'));
    $param['bagian_nama'] = anti_inject($this->input->get_post('bagian_nama'));

    $this->M_Bagian->updateBagian($param, $id);
  }

  public function deleteBagian() {
    $id = $this->input->get_post('bagian_id');
    $this->M_Bagian->deleteBagian($id);
  }

  public function getBagianAdmin() {
    $param['bagian_id'] = $this->input->get_post('bagian_id');
    $data = $this->M_Bagian->getBagianAdmin($param);

    echo json_encode($data);
  }

  public function getBagianUser() {
    $list['results'] = array();
    $param['pegawai_nama'] = $this->input->get_post('pegawai_nama');
    $param['id_bagian'] = $this->input->get_post('bagian_id');

    foreach ($this->M_Bagian->getBagianPegawai($param) as $key => $value) {
      array_push($list['results'], [
        'id' => $value['pegawai_nik'],
        'text' => $value['pegawai_nik'] . ' - ' . $value['pegawai_nama'] . ' - ' . $value['pegawai_postitle'],
      ]);
    }
    echo json_encode($list);
  }

  public function insertBagianAdmin() {
    $param = [
      'admin_bagian_id' => create_id(),
      'id_bagian' => $this->input->post('admin_bagian_id'),
      'admin_bagian_nik' => $this->input->post('admin_nik'),
    ];
    $this->M_Bagian->insertBagianAdmin($param);
  }

  public function updateBagianAdmin() {
    $id = $this->input->post('admin_bagian_id');
    $param['admin_bagian_nik'] = $this->input->post('admin_nik');
    $this->M_Bagian->updateBagianAdmin($param, $id);
  }

  public function getBagianPegawai() {
    $param['id_bagian'] = $this->input->get_post('id_bagian');
    $param['bagian_detail_id'] = $this->input->get_post('bagian_detail_id');
    $data = $this->M_Bagian->getBagianPegawai($param);

    echo json_encode($data);
  }

  public function getUserStaf() {
    $list['results'] = array();
    $param['pegawai_nama'] = $this->input->get('pegawai_nama');

    foreach ($this->M_Bagian->getUserStaf($param) as $key => $value) {
      array_push($list['results'], [
        'id' => $value['pegawai_nik'],
        'text' => $value['pegawai_nama'] . ' - ' . $value['pegawai_postitle'],
      ]);
    }
    echo json_encode($list);
  }

  public function insertBagianPegawai() {
    $param['bagian_detail_id'] = anti_inject(create_id());
    $param['id_bagian'] = anti_inject($this->input->get_post('temp_id_bagian'));
    $param['id_pegawai'] = anti_inject($this->input->get_post('pegawai_nik'));

    $this->M_Bagian->insertBagianPegawai($param);
  }

  public function deleteBagianPegawai() {
    $id = $this->input->get_post('bagian_detail_id');
    $this->M_Bagian->deleteBagianPegawai($id);
  }
}
