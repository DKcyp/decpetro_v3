<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Template_pekerjaan extends MX_Controller {
  public function __construct() {
    parent::__construct();

    if (empty($this->session->userdata('pegawai_nik'))) redirect(base_url('login'));

    $this->load->model('M_template_pekerjaan');
  }

  public function index() {
    $data = array();
    $this->template->template_master('master/template_pekerjaan',$data);
  }

  public function getTemplatePekerjaan() {
    $param['pekerjaan_template_id'] = $this->input->get('pekerjaan_template_id');
    $data = $this->M_template_pekerjaan->getTemplatePekerjaan($param);

    echo json_encode($data);
  }

  public function insertTemplatePekerjaan() {
    $param['pekerjaan_template_id']  = anti_inject(create_id());
    $param['pekerjaan_template_nama'] = anti_inject($this->input->get_post('pekerjaan_template_nama'));
    $param['pekerjaan_template_kode'] = anti_inject($this->input->get_post('pekerjaan_template_kode'));
    $param['pekerjaan_template_file'] = 'pekerjaan_cover';

    $this->M_template_pekerjaan->insertTemplatePekerjaan($param);
  }

  public function updateTemplatePekerjaan() {
    $id = anti_inject($this->input->get_post('pekerjaan_template_id'));
    $param['pekerjaan_template_nama'] = anti_inject($this->input->get_post('pekerjaan_template_nama'));
    $param['pekerjaan_template_kode'] = $this->input->get_post('pekerjaan_template_kode');

    $this->M_template_pekerjaan->updateTemplatePekerjaan($param, $id);
  }

  public function deleteTemplatePekerjaan() {
    $id = $this->input->get_post('pekerjaan_template_id');
    $this->M_template_pekerjaan->deleteTemplatePekerjaan($id);
  }
}