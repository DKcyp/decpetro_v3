<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pekerjaan_berjalan extends MX_Controller
{


  public function __construct()
  {
    parent::__construct();
    //Do your magic here
    $sesi = $this->session->userdata();
    if (empty($sesi['pegawai_nik'])) {
      redirect('tampilan');
    }
  }


  public function index()
  {
    $this->load->view('project/pekerjaan_berjalan');
  }
}
