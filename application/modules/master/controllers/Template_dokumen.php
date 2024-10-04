<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Template_dokumen extends MX_Controller {
  public function __construct() {
    parent::__construct();

    if (empty($this->session->userdata('pegawai_nik'))) redirect(base_url('login'));

    $this->load->model('M_template_dokumen');
  }

  public function index() {
    $data['data_admin'] = $this->db->query("SELECT * FROM global.global_admin WHERE admin_nik = '" . $this->session->userdata('pegawai_nik') . "'")->num_rows();
    $this->template->template_master('master/template_dokumen', $data);
  }

  public function get_template_data() {
    $param['id_template_dokumen'] = $this->input->get('id_template_dokumen');
    $data = $this->M_template_dokumen->getTemplateData($param);

    echo json_encode($data);
  }

  public function down_template() {
    $this->load->helper('download');
    $template_dokumen_file = $this->input->get_post('template_dokumen_file');
    $file_path = FCPATH.'/document/template/'.$template_dokumen_file;

    force_download($file_path, NULL);
  }

  public function store_template() {
    $param['id_template_dokumen'] = ($this->input->get_post('id_template_dokumen') == '') ? create_id() : anti_inject($this->input->get_post('id_template_dokumen'));
    $param['template_nama']  = anti_inject($this->input->get_post('template_nama'));

    $upload_path = FCPATH.'/document/template/';
    if (!empty($_FILES['template_dokumen_file']['name'])) {
      $tmpName = $_FILES['template_dokumen_file']['tmp_name'];
      $fileName = $_FILES['template_dokumen_file']['name'];
      $fileType = $_FILES['template_dokumen_file']['type'];

      $fileExt = substr($fileName, strpos($fileName, '.'));
      $fileExt = str_replace('.', '', $fileExt); // Extension
      $fileName = preg_replace("/\.[^.\s]{3,4}$/", "", $fileName);
      $newFileName = str_replace(' ', '', $param['id_template_dokumen'] . '_' . date('YmdHis') . '.' . $fileExt);
      move_uploaded_file($tmpName, $upload_path . $newFileName);

      $param['template_dokumen_file'] = $newFileName;
    }

    ($this->input->get_post('id_template_dokumen') == '') ? $this->M_template_dokumen->storeTemplate($param) : $this->M_template_dokumen->updateTemplate($param, $param['id_template_dokumen']);
  }

  public function del_template() {
    $id = $this->input->get_post('id_template_dokumen');
    $this->M_template_dokumen->delTemplate($id);
  }
}