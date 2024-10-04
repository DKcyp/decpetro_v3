<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Template {
  protected $ci;

  public function __construct() {
    $this->ci = &get_instance();
  }

  public function template_master($konten, $isi) {
    $data = $this->ci->session->userdata();

    $field['header'] = $this->ci->load->view('tampilan/header', $data);
    $field['sidebar'] = $this->ci->load->view('tampilan/sidebar', $data);
    $field['konten'] = $this->ci->load->view($konten, $isi);
    $field['footer'] = $this->ci->load->view('tampilan/footer', $data);

    return $field;
  }
}