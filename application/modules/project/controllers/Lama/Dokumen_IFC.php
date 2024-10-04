<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dokumen_IFC extends MX_Controller
{
  public function __construct()
  {
    parent::__construct();
    $sesi = $this->session->userdata();
    if (empty($sesi['pegawai_nik'])) {
      redirect('tampilan');
    }
  }

  public function index()
  {
    $this->load->view('project/ifc');
  }
}
