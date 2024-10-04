<?php

defined('BASEPATH') or exit('No direct script access allowed');

class RKAP extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('template');
        $sesi = $this->session->userdata();
        if (empty($sesi['pegawai_nik'])) {
            redirect('tampilan');
        }

        $this->load->model('M_pekerjaan');
        $this->load->model('master/M_user');
        $this->load->library('mailer');
        $this->load->library('mailer_api');
    }

    /* INDEX */
    /* Index Pekerjaan Usulan */
    public function index()
    {
        $data = $this->session->userdata();

        $this->template->template_master('project/RKAP', $data);
        // $this->load->view('project/RKAP', $data);
    }
    /* Index Pekerjaan Usulan */

    /* Index Pekerjaan Detail */
    public function detailPekerjaan($var = null)
    {
        $param['pekerjaan_id'] = $this->input->get('pekerjaan_id');

        $data["pekerjaan"] = $this->M_pekerjaan->getPekerjaan($param);

        $this->load->view('project/detail_pekerjaan_usulan', $data);
    }
    /* Index Pekerjaan Detail */
    /* INDEX */

    public function getUserListRevApp() {
      $list['results'] = array();

      $param['pegawai_nama'] = $this->input->get('pegawai_nama');

      $data_pegawai = $this->M_pekerjaan->getUserListRevApp($param);
      foreach ($data_pegawai as $key => $value) {
        array_push($list['results'], [
          'id' => $value['pegawai_nik'],
          'text' => $value['pegawai_nama'] . ' - ' . $value['pegawai_postitle'],
        ]);
      }

      echo json_encode($list);
    }

    public function getUserListRevApp2() {
      $list['results'] = array();

      $param['pegawai_poscode'] = $this->input->get('param1');
      $param['pegawai_nik'] = $this->input->get('pegawai_nik');

      $data_pegawai = $this->M_pekerjaan->getUserListRevApp2($param);
      $data['id'] = $data_pegawai['pegawai_nik'];
      $data['text'] = $data_pegawai['pegawai_nama'] . ' - ' . $data_pegawai['pegawai_postitle'];

      echo json_encode($data);
    }

    public function getUserListRevApp3() {
      $list['results'] = array();

      $param['pegawai_poscode'] = $this->input->get('param1');
      $param['pegawai_nik'] = $this->input->get('pegawai_nik');

      $data_pegawai = $this->M_pekerjaan->getUserListRevApp2($param);

      echo json_encode($data_pegawai);
    }
}

/* End of file RKAP.php */
