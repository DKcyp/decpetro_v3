<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends MX_Controller {
  public function __construct() {
    parent::__construct();

    if (empty($this->session->userdata('pegawai_nik'))) redirect(base_url('login'));

    $this->load->model('M_user');
  }

  public function index() {
    $data = array();
    $this->template->template_master('master/user', $data);
  }

  public function getUserTable() {
    $kolom_filter = array('pegawai_nama', 'pegawai_nama', 'pegawai_nik', 'pegawai_nama', 'pegawai_nik', 'pegawai_postitle', 'pegawai_nama');
    $output=array();

    $param['length'] = $_REQUEST['length'];
    $param['start']  = $_REQUEST['start'];
    $param['search'] = $_REQUEST['search']["value"];
    $param['order_col']  = $kolom_filter[$_REQUEST['order'][0]['column']];
    $param['order_dir']  = $_REQUEST['order'][0]['dir'];

    $data = $this->M_user->getUserTable($param);
    $total = $this->M_user->getUserTableTotal($param);

    $output['draw']         = $_REQUEST['draw'];
    $output['recordsTotal'] = $output['recordsFiltered'] = $total['total'];
    $output['data']         = array();

    if ($data) {
      foreach ($data as $key => $value) {
        $isi = array();
        $isi[] = $param['start']+$key+1;
        $isi[] = $value['pegawai_nama'];
        $isi[] = ($value['usr_id'] != '') ? $value['usr_loginname'] : $value['pegawai_nik'];
        $isi[] = '*********';
        $isi[] = $value['pegawai_nik'];
        $isi[] = $value['pegawai_postitle'];
        $isi[] = '<center><a href="javascript:void(0)" id="'.$value['pegawai_nik'].'" onclick="fun_sync(this.id)"><i class="fa fa-retweet"></i></a></center>';

        $output['data'][] = $isi;
      }
    }

    echo json_encode($output);
  }

  public function insertUser() {
    $data_user_bantuan['usr_id'] = $_POST['pegawai_nik'];
    $data_user_bantuan['usr_name'] = $_POST['pegawai_nama'];
    $data_user_bantuan['usr_loginname'] = $_POST['usr_loginname'];
    $data_user_bantuan['usr_password'] = md5($_POST['usr_password']);
    $data_user_bantuan['usr_status'] = 'y';
    $this->M_user->insertUserBantuan($data_user_bantuan);

    $data_pegawai['pegawai_nik'] = $_POST['pegawai_nik'];
    $data_pegawai['pegawai_nik_lama'] = $_POST['pegawai_nik'];
    $data_pegawai['pegawai_nama'] = $_POST['pegawai_nama'];
    $data_pegawai['pegawai_unitkerja'] = 'E53600';
    $data_pegawai['pegawai_nama_unit_kerja'] = 'Staf Dep Rancang Bangun';
    $data_pegawai['pegawai_poscode'] = 'E53600060B';
    $data_pegawai['pegawai_postitle'] = 'Pl. Dep Rancang Bangun';
    $data_pegawai['pegawai_direct_superior'] = 'E53000000';
    $data_pegawai['pegawai_unit_id'] = 'E53000';
    $data_pegawai['pegawai_unit_name'] = 'Dep Rancang Bangun';
    $data_pegawai['pegawai_id_bag'] = 'E53600';
    $data_pegawai['pegawai_nama_bag'] = 'Staf Dep Rancang Bangun';
    $data_pegawai['pegawai_id_dep'] = 'E53000';
    $data_pegawai['pegawai_nama_dep'] = 'Dep Rancang Bangun';
    $data_pegawai['pegawai_jabatan'] = '41A';
    $data_pegawai['pegawai_updatedon'] = date('Y-m-d');
    $this->M_user->insertPegawai($data_pegawai);
  }

  public function syncMenu() {
    $param['pegawai_nik'] = $this->input->get('nik');
    $dataUser = $this->M_user->getUser($param);

    $this->M_user->deleteMenuRole($dataUser['pegawai_poscode']);

    if ($dataUser['pegawai_unit_id'] == 'E53000') {
      $data = array(
        array('menu_role_id' => create_id(), 'id_role' => $dataUser['pegawai_poscode'], 'id_menu' => '01'),
        array('menu_role_id' => create_id(), 'id_role' => $dataUser['pegawai_poscode'], 'id_menu' => '02'),
        array('menu_role_id' => create_id(), 'id_role' => $dataUser['pegawai_poscode'], 'id_menu' => '03'),
        array('menu_role_id' => create_id(), 'id_role' => $dataUser['pegawai_poscode'], 'id_menu' => '06'),
        array('menu_role_id' => create_id(), 'id_role' => $dataUser['pegawai_poscode'], 'id_menu' => '07'),
        array('menu_role_id' => create_id(), 'id_role' => $dataUser['pegawai_poscode'], 'id_menu' => '09')
      );
    } else {
      $data = array(
        array('menu_role_id' => create_id(), 'id_role' => $dataUser['pegawai_poscode'], 'id_menu' => '01'),
        array('menu_role_id' => create_id(), 'id_role' => $dataUser['pegawai_poscode'], 'id_menu' => '02'),
        array('menu_role_id' => create_id(), 'id_role' => $dataUser['pegawai_poscode'], 'id_menu' => '03'),
        array('menu_role_id' => create_id(), 'id_role' => $dataUser['pegawai_poscode'], 'id_menu' => '06'),
        array('menu_role_id' => create_id(), 'id_role' => $dataUser['pegawai_poscode'], 'id_menu' => '07')
      );
    }

    $this->db->insert_batch('global.global_menu_role', $data);
  }
}
