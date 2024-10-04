<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends MX_Controller
{
  public function __construct()
  {
    parent::__construct();

    if (empty($this->session->userdata('pegawai_nik'))) redirect(base_url('login'));

    $this->load->model('M_home');
  }

  public function index()
  {
    $data['dataTahun'] = $this->db->query("SELECT pekerjaan_tahun as tahun FROM dec.dec_pekerjaan WHERE pekerjaan_tahun IS NOT NULL GROUP BY pekerjaan_tahun ORDER BY pekerjaan_tahun ASC")->result_array();
    $data['bulan'] = array(1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember');

    $this->template->template_master('home/home', $data);
  }

  public function getTotalPekerjaan()
  {
    $param['bulan'] = $this->input->get('bulan');
    $param['tahun'] = $this->input->get('tahun');

    $param['status'] = array('5', '6', '7', '8', '9', '10', '11', '12', '13');
    $data['dataBerjalanTotal'] = $this->M_home->getPekerjaanTotal($param);
    $data['berjalanPerStatus'] = $this->M_home->getPekerjaanStatus($param);
    $data['berjalanPerBulan'] = $this->M_home->getPekerjaanBulan($param);

    $param['status'] = array('14', '15', '16');
    $data['dataselesaiTotal'] = $this->M_home->getPekerjaanTotal($param);
    $data['selesaiPerStatus'] = $this->M_home->getPekerjaanStatus($param);
    $data['selesaiPerBulan'] = $this->M_home->getPekerjaanBulan($param);

    echo json_encode($data);
  }

  public function dokumenTotal()
  {
    $param['bulan'] = $this->input->get('bulan');
    $param['tahun'] = $this->input->get('tahun');

    $data['dokumenTotal'] = $this->M_home->getDokumenTotal($param);
    $data['dokumenPerstatus'] = $this->M_home->getDokumenStatus($param);

    echo json_encode($data);
  }

  public function getEmploye()
  {
    $param['bulan'] = $this->input->get('bulan');
    $param['tahun'] = $this->input->get('tahun');
    $param['filter'] = $this->input->get('filter');
    $data = $this->M_home->getPegawai($param);

    echo json_encode($data);
  }

  public function dokumenTransmitalTotal()
  {
    $param['bulan'] = $this->input->get('bulan');
    $param['tahun'] = $this->input->get('tahun');
    $param['status'] = array('0', '1', '2', '3', '4', '5');

    $data['dokumenTotal'] = $this->M_home->getTransmitalTotal($param);
    $data['dokumenPerstatus'] = $this->M_home->getTransmitalStatus($param);

    echo json_encode($data);
  }
}
