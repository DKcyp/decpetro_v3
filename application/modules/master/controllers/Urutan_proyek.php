<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Urutan_proyek extends MX_Controller {
  public function __construct() {
    parent::__construct();

    if (empty($this->session->userdata('pegawai_nik'))) redirect(base_url('login'));

    $this->load->model('M_urutan_proyek');
  }

  public function index() {
    $data = array();
    $this->template->template_master('master/urutan_proyek',$data);
  }

  public function getUrutanProyek() {
    $param['urutan_proyek_id'] = $this->input->get_post('urutan_proyek_id');

    $data = $this->M_urutan_proyek->getUrutanProyek($param);
    echo json_encode($data);
  }

  public function insertUrutanProyek() {
    $param['urutan_proyek_id'] = anti_inject(create_id());
    $param['urutan_proyek_nama'] = anti_inject($this->input->get_post('urutan_proyek_nama'));
    $param['urutan_proyek_kode'] = anti_inject($this->input->get_post('urutan_proyek_kode'));

    $this->M_urutan_proyek->insertUrutanProyek($param);
  }

  public function updateUrutanProyek() {
    $id = anti_inject($this->input->get_post('urutan_proyek_id'));
    $param['urutan_proyek_nama'] = anti_inject($this->input->get_post('urutan_proyek_nama'));
    $param['urutan_proyek_kode'] = anti_inject($this->input->get_post('urutan_proyek_kode'));

    $this->M_urutan_proyek->updateUrutanProyek($param, $id);
  }

  public function deleteUrutanProyek() {
    $id = $this->input->get_post('urutan_proyek_id');
    $this->M_urutan_proyek->deleteUrutanProyek($id);
  }

  public function getSectionArea() {
    $param['id_urutan_proyek'] = $this->input->get_post('id_urutan_proyek');
    $param['section_area_id'] = $this->input->get_post('section_area_id');

    $data = $this->M_urutan_proyek->getSectionArea($param);
    echo json_encode($data);
  }

  public function insertSectionArea() {
    $param['section_area_id'] = anti_inject(create_id());
    $param['id_urutan_proyek'] = anti_inject($this->input->get_post('temp_id_urutan_proyek'));
    $param['section_area_nama'] = anti_inject($this->input->get_post('section_area_nama'));
    $param['section_area_kode'] = anti_inject($this->input->get_post('section_area_kode'));

    $this->M_urutan_proyek->insertSectionArea($param);
  }

  public function updateSectionArea() {
    $id = anti_inject($this->input->get_post('section_area_id'));
    $param['section_area_nama'] = anti_inject($this->input->get_post('section_area_nama'));
    $param['section_area_kode'] = anti_inject($this->input->get_post('section_area_kode'));

    $this->M_urutan_proyek->updateSectionArea($param, $id);
  }

  public function deleteSectionArea() {
    $id = $this->input->get_post('section_area_id');
    $this->M_urutan_proyek->deleteSectionArea($id);
  }
}
