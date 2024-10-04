<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();

    if (empty($this->session->userdata('pegawai_nik'))) redirect(base_url('login'));

    $this->load->model('M_Admin');
  }

  public function index()
  {
    $data = array();
    $this->template->template_master('master/admin', $data);
  }

  public function getAdmin()
  {
    $param['admin_id'] = $this->input->get_post('admin_id');
    $param['admin_nik'] = $this->input->get_post('admin_nik');
    $data = $this->M_Admin->getAdmin($param);

    echo json_encode($data);
  }

  public function getUser()
  {
    $session = $this->session->userdata();
    $list['results'] = array();
    $param['pegawai_nama'] = $this->input->get_post('pegawai_nama');

    foreach ($this->M_Admin->getUser($param) as $key => $value) {
      array_push($list['results'], [
        'id' => $value['pegawai_nik'],
        'text' => $value['pegawai_nik'] . ' - ' . $value['pegawai_nama'] . ' - ' . $value['pegawai_postitle'],
      ]);
    }
    echo json_encode($list);
  }

  public function insertAdmin()
  {
    $param = [
      'admin_id' => create_id(),
      'admin_nik' => $this->input->post('admin_nik'),
    ];
    $this->M_Admin->insertAdmin($param);
  }

  public function deleteAdmin()
  {
    $id = $this->input->get_post('admin_id');
    $this->M_Admin->deleteAdmin($id);
  }
}
