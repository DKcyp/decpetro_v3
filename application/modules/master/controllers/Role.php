<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Role extends MX_Controller {
  public function __construct() {
    parent::__construct();

    if (empty($this->session->userdata('pegawai_nik'))) redirect(base_url('login'));

    $this->load->model('M_role');
  }

  public function index() {
    $data = array();
    $this->template->template_master('master/role', $data);
  }

  public function getRole() {
    $param['rol_id'] = ($this->input->get('rol_id'));

    $data = $this->M_role->getRole($param);
    echo json_encode($data);
  }

  public function getMenuRole() {
    $param['rol_id'] = ($this->input->get('rol_id'));

    $data = $this->M_role->getMenuRole($param);
    echo json_encode($data);
  }

  public function insertRole() {
    $isi = $this->session->userdata();

    $data['rol_id'] = anti_inject(create_id());
    $data['rol_name'] = anti_inject($this->input->post('rol_name'));

    $this->M_role->insertRole($data);
  }

  public function insertMenuRole() {
    $this->M_role->deleteMenuRole($this->input->post('role_id_temp'));
    foreach ($this->input->post('menu') as $value) {
      $data['menu_role_id'] = anti_inject(create_id());
      $data['id_menu'] = anti_inject($value);
      $data['id_role'] = anti_inject($this->input->post('role_id_temp'));

      $this->M_role->insertMenuRole($data);
    }
  }

  public function updateRole() {
    $isi = $this->session->userdata();

    $id = anti_inject($this->input->post('rol_id'));
    $data = array(
      'rol_name' => anti_inject($this->input->post('rol_name')),
    );

    $this->M_role->updateRole($data, $id);
  }

  public function deleteRole() {
    $this->M_role->deleteRole($this->input->get('rol_id'));
  }
}
