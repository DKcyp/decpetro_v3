<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dokumen_IFA extends MX_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->library('template');
    $sesi = $this->session->userdata();
    if (empty($sesi['pegawai_nik'])) {
      redirect('tampilan');
    }

    $this->load->model('project/M_Pekerjaan');
  }

  public function index()
  {
    $data = array();
    $this->template->template_master('project/ifa',$data);
  }

  public function getIdDisposisi()
  {
    $user = $this->session->userdata();
    $param_disposisi['id_user'] = htmlentities($user['pegawai_nik']);
    $param_disposisi['id_pekerjaan'] = htmlentities($this->input->get_post('id_pekerjaan'));
    $param_disposisi['pekerjaan_status'] = htmlentities($this->input->get_post('pekerjaan_disposisi_status'));

    $data = $this->M_Pekerjaan->getIdDisposisi($param_disposisi);
    return $data;
  }

  public function getExtend()
  {
    $param['pekerjaan_id'] = htmlentities($this->input->get_post('id_pekerjaan'));
    $param['pekerjaan_status'] = htmlentities($this->input->get_post('pekerjaan_disposisi_status'));
    // $param['extend_status'] = htmlentities($this->input->get_post('extend_status'));

    $data = $this->M_Pekerjaan->getExtend($param);
    // echo $this->db->last_query();
    echo json_encode($data);
  }

  public function insertAjuanExtend()
  {
    $user = $this->session->userdata();
    // get id disposisi nya
    $data_disposisi = $this->getIdDisposisi();
    //ambil pekerjaan waktu dari pekerjaan waktu
    $param_pekerjaan['pekerjaan_id'] = htmlentities($this->input->get_post('id_pekerjaan'));
    $data_pekerjaan = $this->M_Pekerjaan->getPekerjaan($param_pekerjaan);
    $pekerjaan_waktu = $data_pekerjaan['pekerjaan_waktu'];

    // insert ke tb_extend
    $param_extend['extend_id'] = create_id();
    $param_extend['id_pekerjaan'] = htmlentities($this->input->get_post('id_pekerjaan'));
    $param_extend['id_user'] = htmlentities($user['pegawai_nik']);
    $param_extend['extend_hari'] = htmlentities($this->input->get_post('extend_hari'));
    $param_extend['extend_status'] = htmlentities($this->input->get_post('extend_status'));
    $param_extend['extend_tanggal'] = date('Y-m-d', strtotime(date('Y-m-d') . '+' . $this->input->get_post('extend_hari') . ' days'));
    $this->M_Pekerjaan->insertExtend($param_extend);
  }

  public function updateAJuanExtend()
  {
    $user = $this->session->userdata();
    // get id disposisi nya
    $data_disposisi = $this->getIdDisposisi();
    //ambil pekerjaan waktu dari pekerjaan waktu
    $param_pekerjaan['pekerjaan_id'] = htmlentities($this->input->get_post('id_pekerjaan'));
    $data_pekerjaan = $this->M_Pekerjaan->getPekerjaan($param_pekerjaan);
    $pekerjaan_waktu = $data_pekerjaan['pekerjaan_waktu'];
    // insert ke tb_extend
    $id_extend = htmlentities($this->input->get_post('extend_id'));
    $param_extend['id_pekerjaan'] = htmlentities($this->input->get_post('id_pekerjaan'));
    // $param_extend['id_pekerjaan_disposisi'] = htmlentities($data_disposisi['pekerjaan_disposisi_id']);
    $param_extend['id_user'] = htmlentities($user['pegawai_nik']);
    $param_extend['extend_hari'] = htmlentities($this->input->get_post('extend_hari'));
    $param_extend['extend_status'] = htmlentities($this->input->get_post('extend_status'));
    $param_extend['extend_tanggal'] = date('Y-m-d', strtotime(date('Y-m-d') . '+' . $this->input->get_post('extend_hari') . ' days'));

    $this->M_Pekerjaan->updateExtendBaru($id_extend, $param_extend);
  }
}
