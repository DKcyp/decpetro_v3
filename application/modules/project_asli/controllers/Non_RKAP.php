<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Non_RKAP extends MX_Controller
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

        $this->template->template_master('project/Non_RKAP',$data);

        // $this->load->view('project/Non_RKAP', $data);
    }
    /* Index Pekerjaan Usulan */

    /* Index Pekerjaan Detail */
    public function detailPekerjaan($var = null)
    {
        $param['pekerjaan_id'] = $this->input->get('pekerjaan_id');

        $data["pekerjaan"] = $this->M_pekerjaan->getPekerjaan($param);

        $this->template->template_master('project/detail_pekerjaan_usulan',$data);

        // $this->load->view('project/detail_pekerjaan_usulan', $data);
    }
    /* Index Pekerjaan Detail */
    /* INDEX */
}

/* End of file RKAP.php */
