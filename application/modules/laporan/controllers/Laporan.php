<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Laporan extends MX_Controller
{

  public function __construct()
  {
    parent::__construct();
    #auth
    // isLogin();
    #conf dept
    $this->load->library('template');
    $sesi = $this->session->userdata();
    if (empty($sesi['pegawai_nik'])) {
      redirect('tampilan');
    }
    $this->load->model('M_laporan');
    #session data
    $this->usr_id = $this->session->userdata('usr_id');
    $this->usr_name = $this->session->userdata('nama');
  }

  public function index()
  {
    $data = array();
    $this->template->template_master('laporan/laporan',$data);
    // $this->load->view('laporan/laporan');
  }

  /* GET */
  public function getRencanaDanRealisasiPekerjaan()
  {
    $bulan = array();
    $isi = array();

    $bulan[0]['id'] = '01';
    $bulan[1]['id'] = '02';
    $bulan[2]['id'] = '03';
    $bulan[3]['id'] = '04';
    $bulan[4]['id'] = '05';
    $bulan[5]['id'] = '06';
    $bulan[6]['id'] = '07';
    $bulan[7]['id'] = '08';
    $bulan[8]['id'] = '09';
    $bulan[9]['id'] = '10';
    $bulan[10]['id'] = '11';
    $bulan[11]['id'] = '12';
    $bulan[0]['nama'] = 'Januari';
    $bulan[1]['nama'] = 'Februari';
    $bulan[2]['nama'] = 'Maret';
    $bulan[3]['nama'] = 'April';
    $bulan[4]['nama'] = 'Mei';
    $bulan[5]['nama'] = 'Juni';
    $bulan[6]['nama'] = 'Juli';
    $bulan[7]['nama'] = 'Agustus';
    $bulan[8]['nama'] = 'September';
    $bulan[9]['nama'] = 'Oktober';
    $bulan[10]['nama'] = 'November';
    $bulan[11]['nama'] = 'Desember';

    $tahun = $this->input->get('tahun');

    foreach ($bulan as $value) {
      $select = array('count(pekerjaan_id) as total');
      $where = array();
      // $param = array();
      $firstDate = $tahun . '-' . $value['id'] . '-01';
      $lastDate = date("Y-m-t", strtotime($firstDate));
      $where['pekerjaan_waktu >= '] = $firstDate;
      $where['pekerjaan_waktu <= '] = $lastDate;
      $where['klasifikasi_pekerjaan_nama = '] = 'RKAP';
      // $where['pekerjaan_status != '] = 'y';
      $param_status = '1,2,3,4,5,6,7,8,9,10,11';
      $split = explode(',', $param_status);
      $param['pekerjaan_status'] = $split;
      $rencanaRKAP = $this->M_laporan->getPekerjaanGrafik($select, $where, $param = null)->row();

      $select = array('count(pekerjaan_id) as total');
      $where = array();
      // $param = array();
      $firstDate = $tahun . '-' . $value['id'] . '-01';
      $lastDate = date("Y-m-t", strtotime($firstDate));
      $where['pekerjaan_waktu >= '] = $firstDate;
      $where['pekerjaan_waktu <= '] = $lastDate;
      $where['klasifikasi_pekerjaan_nama = '] = 'RKAP';
      // $where['pekerjaan_status = '] = 'y';
      $param_status = '12,13,14,15';
      $split = explode(',', $param_status);
      $param['pekerjaan_status'] = $split;
      $realisasiRKAP = $this->M_laporan->getPekerjaanGrafik($select, $where, $param)->row();

      $select = array('count(pekerjaan_id) as total');
      $where = array();
      // $param = array();
      $firstDate = $tahun . '-' . $value['id'] . '-01';
      $lastDate = date("Y-m-t", strtotime($firstDate));
      $where['pekerjaan_waktu >= '] = $firstDate;
      $where['pekerjaan_waktu <= '] = $lastDate;
      $where['klasifikasi_pekerjaan_nama = '] = 'Non RKAP';
      // $where['pekerjaan_status != '] = 'y';
      $param_status = '1,2,3,4,5,6,7,8,9,10,11';
      $split = explode(',', $param_status);
      $param['pekerjaan_status'] = $split;
      $rencanaNonRKAP = $this->M_laporan->getPekerjaanGrafik($select, $where, $param)->row();

      $select = array('count(pekerjaan_id) as total');
      $where = array();
      // $param = array();
      $firstDate = $tahun . '-' . $value['id'] . '-01';
      $lastDate = date("Y-m-t", strtotime($firstDate));
      $where['pekerjaan_waktu >= '] = $firstDate;
      $where['pekerjaan_waktu <= '] = $lastDate;
      $where['klasifikasi_pekerjaan_nama = '] = 'Non RKAP';
      // $where['pekerjaan_status = '] = 'y';
      $param_status = '12,13,14,15';
      $split = explode(',', $param_status);
      $param['pekerjaan_status'] = $split;
      $realisasiNonRKAP = $this->M_laporan->getPekerjaanGrafik($select, $where, $param)->row();

      $select = array('count(pekerjaan_id) as total');
      $where = array();
      // $param = array();
      $firstDate = $tahun . '-' . $value['id'] . '-01';
      $lastDate = date("Y-m-t", strtotime($firstDate));
      $where['pekerjaan_waktu >= '] = $firstDate;
      $where['pekerjaan_waktu <= '] = $lastDate;
      $where['klasifikasi_pekerjaan_nama = '] = 'Jasa Engineering';
      // $where['pekerjaan_status != '] = 'y';
      $param_status = '1,2,3,4,5,6,7,8,9,10,11';
      $split = explode(',', $param_status);
      $param['pekerjaan_status'] = $split;
      $rencanaJasaEngineering = $this->M_laporan->getPekerjaanGrafik($select, $where, $param = null)->row();

      $select = array('count(pekerjaan_id) as total');
      $where = array();
      // $param = array();
      $firstDate = $tahun . '-' . $value['id'] . '-01';
      $lastDate = date("Y-m-t", strtotime($firstDate));
      $where['pekerjaan_waktu >= '] = $firstDate;
      $where['pekerjaan_waktu <= '] = $lastDate;
      $where['klasifikasi_pekerjaan_nama = '] = 'Jasa Engineering';
      // $where['pekerjaan_status = '] = 'y';
      $param_status = '12,13,14,15';
      $split = explode(',', $param_status);
      $param['pekerjaan_status'] = $split;
      $realisasiJasaEngineering = $this->M_laporan->getPekerjaanGrafik($select, $where, $param = null)->row();

      $val['bulan'] = $value['nama'];
      $val['rencanaRKAP'] = $rencanaRKAP->total;
      $val['realisasiRKAP'] = $realisasiRKAP->total;
      $val['rencanaNonRKAP'] = $rencanaNonRKAP->total;
      $val['realisasiNonRKAP'] = $realisasiNonRKAP->total;
      $val['rencanaJasaEngineering'] = $rencanaJasaEngineering->total;
      $val['realisasiJasaEngineering'] = $realisasiJasaEngineering->total;

      array_push($isi, $val);
    }
    echo json_encode($isi);
  }

  public function getStatusPekerjaan()
  {
    $isi = array();

    $select = array('count(pekerjaan_id) as total');
    $where = array();
    $param_status = '1';
    $split = explode(',', $param_status);
    $param['pekerjaan_status'] = $split;
    $baru = $this->M_laporan->getPekerjaanGrafik($select, $where, $param)->row();


    $select = array('count(pekerjaan_id) as total');
    $where = array();
    $param_status = '2,3,4,5,6,7,8,9,10,11';
    $split = explode(',', $param_status);
    $param['pekerjaan_status'] = $split;
    $proses = $this->M_laporan->getPekerjaanGrafik($select, $where, $param)->row();

    $select = array('count(pekerjaan_id) as total');
    $where = array();
    $param_status = '12,13,14,15';
    $split = explode(',', $param_status);
    $param['pekerjaan_status'] = $split;
    $selesai = $this->M_laporan->getPekerjaanGrafik($select, $where, $param)->row();

    $val['baru'] = $baru->total;
    $val['proses'] = $proses->total;
    $val['selesai'] = $selesai->total;

    array_push($isi, $val);

    echo json_encode($isi);
  }

  public function getStatusDokumen()
  {
    $isi = array();

    $select = array('count(pekerjaan_dokumen_id) as total');
    $where = array();
    // $where['pekerjaan_dokumen_status != '] = 'c';
    // $param_status = '2,3,4,5';
    // $split = explode(',', $param_status);
    // $param['pekerjaan_dokumen_status'] = $split;
    $where['pekerjaan_status >= '] = '0';
    $where['pekerjaan_status <= '] = '9';
    $where['pekerjaan_dokumen_awal != '] = 'y';
    $where['is_lama != '] = 'y';
    $IFA = $this->M_laporan->getDokumenGrafik($select, $where, $param = null)->row();

    $select = array('count(pekerjaan_dokumen_id) as total');
    $where = array();
    // $where['pekerjaan_dokumen_status = '] = 'c';
    // $param_status = '6,7,8,9';
    // $split = explode(',', $param_status);
    // $param['pekerjaan_dokumen_status'] = $split;
    $where['pekerjaan_status >= '] = '10';
    $where['pekerjaan_status <= '] = '12';
    $where['pekerjaan_dokumen_awal != '] = 'y';
    $where['is_lama != '] = 'y';
    $IFC = $this->M_laporan->getDokumenGrafik($select, $where, $param = null)->row();

    $val['ifa'] = $IFA->total;
    $val['ifc'] = $IFC->total;

    array_push($isi, $val);

    echo json_encode($isi);
  }

  public function getPekerjaanBerjalan()
  {
    $session = $this->session->userdata();
    $data = array();
    $param['id_user'] = $this->input->get_post('id_user_cari');
    $param['klasifikasi_pekerjaan_id'] = $this->input->get_post('klasifikasi_pekerjaan_id');
    $param['klasifikasi_pekerjaan_id_non_rkap'] = $this->input->get_post('klasifikasi_pekerjaan_id_non_rkap');
    $split = explode(',', $this->input->get_post('pekerjaan_status'));
    $param['pekerjaan_status'] = $split;


    foreach ($this->M_laporan->getPekerjaanDispo($param) as $key => $value) {
      // print_r($value);
      $sql_total = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '5' AND id_pekerjaan = '" . $value['pekerjaan_id'] . "' ");
        $isi_total = $sql_total->row_array();

        $sql = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '" . $value['pekerjaan_status'] . "' AND id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND  id_user = '" . $session['pegawai_nik'] . "'  ");
        $dataMilik = $sql->row_array();

        $sql_sipil = $this->db->query("SELECT bagian_id,id_bagian,progress_jumlah FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='1483c0882e75988626fee21c5926cc63727734a0'");
        $data_sipil = $sql_sipil->row_array();

        $sql_jml_sipil = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='1483c0882e75988626fee21c5926cc63727734a0'");
        $data_jml_sipil = $sql_jml_sipil->row_array();

        $sql_user_sipil = $this->db->query("SELECT klasifikasi_dokumen_inisial FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '5' AND c.id_bagian = '1483c0882e75988626fee21c5926cc63727734a0'");
        $data_user_sipil = $sql_user_sipil->row_array();

        $sql_user_sipil_koor = $this->db->query("SELECT count(*) AS total FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '4' AND id_penanggung_jawab = 'y' AND c.id_bagian = '1483c0882e75988626fee21c5926cc63727734a0'");
        $data_user_sipil_koor = $sql_user_sipil_koor->row_array();


        $sql_proses = $this->db->query("SELECT bagian_id,id_bagian,progress_jumlah FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='c21f86a03fdf9f7420764ac49d664415cfc942eb'");
        $data_proses = $sql_proses->row_array();

        $sql_jml_proses = $this->db->query("SELECT count(*) as total FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='c21f86a03fdf9f7420764ac49d664415cfc942eb'");
        $data_jml_proses = $sql_jml_proses->row_array();

        $sql_user_proses = $this->db->query("SELECT klasifikasi_dokumen_inisial FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '5' AND c.id_bagian = 'c21f86a03fdf9f7420764ac49d664415cfc942eb'");
        $data_user_proses = $sql_user_proses->row_array();

        $sql_user_proses_koor = $this->db->query("SELECT count(*) AS total FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '4' AND id_penanggung_jawab = 'y' AND c.id_bagian = 'c21f86a03fdf9f7420764ac49d664415cfc942eb'");
        $data_user_proses_koor = $sql_user_proses_koor->row_array();


        $sql_mesin = $this->db->query("SELECT bagian_id,id_bagian,progress_jumlah FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='fd2aa961b30ede7622a57d42267edc5d5eae3e1b'");
        $data_mesin = $sql_mesin->row_array();

        $sql_jml_mesin = $this->db->query("SELECT COUNT(*) as total FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='fd2aa961b30ede7622a57d42267edc5d5eae3e1b'");
        $data_jml_mesin = $sql_jml_mesin->row_array();

        $sql_user_mesin = $this->db->query("SELECT klasifikasi_dokumen_inisial FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '5' AND c.id_bagian = 'fd2aa961b30ede7622a57d42267edc5d5eae3e1b'");
        $data_user_mesin = $sql_user_mesin->row_array();

        $sql_user_mesin_koor = $this->db->query("SELECT count(*) AS total FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '4' AND id_penanggung_jawab = 'y' AND c.id_bagian = 'fd2aa961b30ede7622a57d42267edc5d5eae3e1b'");
        $data_user_mesin_koor = $sql_user_mesin_koor->row_array();


        $sql_listrik = $this->db->query("SELECT bagian_id,id_bagian,progress_jumlah FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian LEFT JOIN dec.dec_pekerjaan_disposisi c ON c.id_user = a.id_user   WHERE a.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND is_listin = 'L'");
        $data_listrik = $sql_listrik->row_array();

        $sql_jml_listrik = $this->db->query("SELECT COUNT(*) as total FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian LEFT JOIN dec.dec_pekerjaan_disposisi c ON c.id_user = a.id_user   WHERE a.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND is_listin = 'L'");
        $data_jml_listrik = $sql_jml_listrik->row_array();

        $sql_user_listrik = $this->db->query("SELECT klasifikasi_dokumen_inisial FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '5' AND c.id_bagian = 'f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND b.is_listin = 'L'");
        $data_user_listrik = $sql_user_listrik->row_array();

        $sql_user_listrik_koor = $this->db->query("SELECT count(*) AS total FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '4' AND id_penanggung_jawab = 'y' AND c.id_bagian = 'f683cbbca693d1a08fc010fd861b7350efa3e8d2'");
        $data_user_listrik_koor = $sql_user_listrik_koor->row_array();


        $sql_instrumen = $this->db->query("SELECT bagian_id,id_bagian,progress_jumlah FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian LEFT JOIN dec.dec_pekerjaan_disposisi c ON c.id_user = a.id_user  WHERE a.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND is_listin='I'");
        $data_instrumen = $sql_instrumen->row_array();

        $sql_jml_instrumen = $this->db->query("SELECT COUNT(*) as total FROM dec.dec_pekerjaan_progress a LEFT JOIN global.global_bagian b ON b.bagian_id = a.id_bagian LEFT JOIN dec.dec_pekerjaan_disposisi c ON c.id_user = a.id_user  WHERE a.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND bagian_id ='f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND is_listin='I'");
        $data_jml_instrumen = $sql_jml_instrumen->row_array();

        $sql_user_instrumen = $this->db->query("SELECT klasifikasi_dokumen_inisial FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '5' AND c.id_bagian = 'f683cbbca693d1a08fc010fd861b7350efa3e8d2' AND b.is_listin = 'I'");
        $data_user_instrumen = $sql_user_instrumen->row_array();

        $sql_user_instrumen_koor = $this->db->query("SELECT count(*) AS total FROM global.global_klasifikasi_dokumen a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.id_pegawai = b.id_user LEFT JOIN global.global_bagian_detail c ON b.id_user = c.id_pegawai WHERE b.id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND b.pekerjaan_disposisi_status = '4' AND id_penanggung_jawab = 'y' AND c.id_bagian = 'f683cbbca693d1a08fc010fd861b7350efa3e8d2'");
        $data_user_instrumen_koor = $sql_user_instrumen_koor->row_array();
        // print_r($data_instrumen);
        // data per progress

        $sql_progress = $this->db->query("select pekerjaan_id,bagian_id,bagian_nama,progress_jumlah,id_pekerjaan from global.global_bagian a left join global.global_bagian_detail b on b.id_bagian = a.bagian_id left join dec.dec_pekerjaan_progress c on c.id_user = b.id_pegawai right join dec.dec_pekerjaan d on d.pekerjaan_id = c.id_pekerjaan where pekerjaan_id ='" . $value['pekerjaan_id'] . "' order by progress_jumlah desc");

        $isi_progress = $sql_progress->result_array();
        // var_dump($isi_progress);/


        $sql_jumlah_progress = $this->db->query("select count(*) as total from global.global_bagian a left join global.global_bagian_detail b on b.id_bagian = a.bagian_id left join dec.dec_pekerjaan_progress c on c.id_user = b.id_pegawai right join dec.dec_pekerjaan d on d.pekerjaan_id = c.id_pekerjaan where pekerjaan_id ='" . $value['pekerjaan_id'] . "' ");
        $isi_jumlah_proses = $sql_jumlah_progress->row_array();


        $isi['milik'] = ($dataMilik['total'] > 0 || ($value['pic'] == $session['pegawai_nik'] && ($value['pekerjaan_status'] == '0' || $value['pekerjaan_status'] == '-'))) ? 'y' : 'n';


        if (!empty($data_sipil['id_bagian']) && $data_sipil['id_bagian'] == '1483c0882e75988626fee21c5926cc63727734a0') {
          $isi['pekerjaan_sipil'] =  $data_sipil['progress_jumlah'];
          $isi['pekerjaan_isi_sipil'] =  (isset($data_user_sipil['klasifikasi_dokumen_inisial'])) ? $data_user_sipil['klasifikasi_dokumen_inisial'] . '(' . $data_sipil['progress_jumlah'] . '%)' : '';
        } else {
          $isi['pekerjaan_sipil'] = 0;
          $isi['pekerjaan_isi_sipil'] = (isset($data_user_sipil['klasifikasi_dokumen_inisial'])) ? $data_user_sipil['klasifikasi_dokumen_inisial'] . '(0%)' : '';
        }
        $isi['pekerjaan_isi_sipil'] = ($data_user_sipil_koor['total'] == 0) ? $isi['pekerjaan_isi_sipil'] : '<b>' . $isi['pekerjaan_isi_sipil'] . '</b>';

        if (!empty($data_proses['bagian_id']) && $data_proses['bagian_id'] == 'c21f86a03fdf9f7420764ac49d664415cfc942eb') {
          $isi['pekerjaan_proses'] =  $data_proses['progress_jumlah'];
          $isi['pekerjaan_isi_proses'] =  (isset($data_user_proses['klasifikasi_dokumen_inisial'])) ? $data_user_proses['klasifikasi_dokumen_inisial'] . '(' . $data_proses['progress_jumlah'] . '%)' : '';
        } else {
          $isi['pekerjaan_proses'] = 0;
          $isi['pekerjaan_isi_proses'] = (isset($data_user_proses['klasifikasi_dokumen_inisial'])) ? $data_user_proses['klasifikasi_dokumen_inisial'] . '(0%)' : '';
        }
        $isi['pekerjaan_isi_proses'] = ($data_user_proses_koor['total'] == 0) ? $isi['pekerjaan_isi_proses'] : '<b>' . $isi['pekerjaan_isi_proses'] . '</b>';

        if ((!empty($data_mesin['id_bagian'])) && $data_mesin['id_bagian'] == 'fd2aa961b30ede7622a57d42267edc5d5eae3e1b') {
          $isi['pekerjaan_mesin'] =  $data_mesin['progress_jumlah'];
          $isi['pekerjaan_isi_mesin'] =  (isset($data_user_mesin['klasifikasi_dokumen_inisial'])) ? $data_user_mesin['klasifikasi_dokumen_inisial'] . '(' . $data_mesin['progress_jumlah'] . '%)' : '';
        } else {
          $isi['pekerjaan_mesin'] = 0;
          $isi['pekerjaan_isi_mesin'] = (isset($data_user_mesin['klasifikasi_dokumen_inisial'])) ? $data_user_mesin['klasifikasi_dokumen_inisial'] . '(0%)' : '';
        }
        $isi['pekerjaan_isi_mesin'] = ($data_user_mesin_koor['total'] == 0) ? $isi['pekerjaan_isi_mesin'] : '<b>' . $isi['pekerjaan_isi_mesin'] . '</b>';

        if ((!empty($data_listrik['bagian_id'])) && $data_listrik['bagian_id'] == 'f683cbbca693d1a08fc010fd861b7350efa3e8d2') {
          $isi['pekerjaan_listrik'] =  $data_listrik['progress_jumlah'];
          $isi['pekerjaan_isi_listrik'] =  (isset($data_user_listrik['klasifikasi_dokumen_inisial'])) ? $data_user_listrik['klasifikasi_dokumen_inisial'] . '(' . $data_listrik['progress_jumlah'] . '%)' : '';
        } else {
          $isi['pekerjaan_listrik'] = 0;
          $isi['pekerjaan_isi_listrik'] = (isset($data_user_listrik['klasifikasi_dokumen_inisial'])) ? $data_user_listrik['klasifikasi_dokumen_inisial'] . '(0%)' : '';
        }
        $isi['pekerjaan_isi_listrik'] = ($data_user_listrik_koor['total'] == 0) ? $isi['pekerjaan_isi_listrik'] : '<b>' . $isi['pekerjaan_isi_listrik'] . '</b>';

        if ((!empty($data_instrumen['bagian_id'])) && $data_instrumen['bagian_id'] == 'f683cbbca693d1a08fc010fd861b7350efa3e8d2') {
          $isi['pekerjaan_instrumen'] =  $data_instrumen['progress_jumlah'];
          $isi['pekerjaan_isi_instrumen'] =  (isset($data_user_instrumen['klasifikasi_dokumen_inisial'])) ? $data_user_instrumen['klasifikasi_dokumen_inisial'] . '(' . $data_instrumen['progress_jumlah'] . '%)' : '';
        } else {
          $isi['pekerjaan_instrumen'] = 0;
          $isi['pekerjaan_isi_instrumen'] = (isset($data_user_instrumen['klasifikasi_dokumen_inisial'])) ? $data_user_instrumen['klasifikasi_dokumen_inisial'] . '(0%)' : '';
        }
        $isi['pekerjaan_isi_instrumen'] = ($data_user_instrumen_koor['total'] == 0) ? $isi['pekerjaan_isi_instrumen'] : '<b>' . $isi['pekerjaan_isi_instrumen'] . '</b>';



        // foreach ($isi_progress as $key => $value_progress) {

        if ($data_jml_sipil['total'] > 0 && $data_sipil['bagian_id'] == '1483c0882e75988626fee21c5926cc63727734a0') {
          $isi['pekerjaan_jumlah_sipil'] = ($data_jml_sipil['total'] > 0) ? $data_jml_sipil['total'] : '0';
        } else {
          $isi['pekerjaan_jumlah_sipil'] = '0';
        }

        if ($data_jml_proses['total'] > 0 && $data_proses['bagian_id'] == 'c21f86a03fdf9f7420764ac49d664415cfc942eb') {
          $isi['pekerjaan_jumlah_proses'] = ($data_jml_proses['total'] > 0) ? $data_jml_proses['total'] : '0';
        } else {
          $isi['pekerjaan_jumlah_proses'] = '0';
        }

        if ($data_jml_mesin['total'] > 0 && $data_mesin['bagian_id'] == 'fd2aa961b30ede7622a57d42267edc5d5eae3e1b') {
          $isi['pekerjaan_jumlah_mesin'] = ($data_jml_mesin['total'] > 0) ? $data_jml_mesin['total'] : '0';
        } else {
          $isi['pekerjaan_jumlah_mesin'] = '0';
        }

        if ($data_jml_listrik['total'] > 0 && $data_listrik['bagian_id'] == 'f683cbbca693d1a08fc010fd861b7350efa3e8d2') {
          $isi['pekerjaan_jumlah_listrik'] = ($data_jml_listrik['total'] > 0) ? $data_jml_listrik['total'] : '0';
        } else {
          $isi['pekerjaan_jumlah_listrik'] = '0';
        }

        if ($data_jml_instrumen['total'] > 0 && $data_instrumen['bagian_id'] == 'f683cbbca693d1a08fc010fd861b7350efa3e8d2') {
          $isi['pekerjaan_jumlah_instrumen'] = ($data_jml_instrumen['total'] > 0) ? $data_jml_instrumen['total'] : '0';
        } else {
          $isi['pekerjaan_jumlah_instrumen'] = '0';
        }
        // }

        if (($isi['pekerjaan_proses'] + $isi['pekerjaan_mesin'] + $isi['pekerjaan_listrik'] + $isi['pekerjaan_instrumen'] + $isi['pekerjaan_sipil'] > 0) && ($isi['pekerjaan_jumlah_proses'] + $isi['pekerjaan_jumlah_mesin'] + $isi['pekerjaan_jumlah_listrik'] + $isi['pekerjaan_jumlah_instrumen'] + $isi['pekerjaan_jumlah_sipil'] > 0)) {
          $isi_progressnya = ($isi['pekerjaan_proses'] + $isi['pekerjaan_mesin'] + $isi['pekerjaan_listrik'] + $isi['pekerjaan_instrumen'] + $isi['pekerjaan_sipil']) / (($isi_total['total']));
        } else {
          $isi_progressnya = 0;
        }

        $sql_tgl_start = $this->db->query("SELECT pekerjaan_disposisi_waktu FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND pekerjaan_disposisi_status ='4' AND is_aktif = 'y'");
        $data_tgl_start = $sql_tgl_start->row_array();

        $sql_avp_review = $this->db->query("SELECT count(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND pekerjaan_disposisi_status ='6' AND id_user = '" . $session['pegawai_nik'] . "' AND is_aktif = 'y'");
        $data_avp_review = $sql_avp_review->row_array();

        $status_avp = ($value['pekerjaan_status'] == '5' && $data_avp_review['total'] >= '1') ? '1' : '0';

        $isi['pekerjaan_progress'] = round($isi_progressnya, 2);
        $isi['pekerjaan_id'] = $value['pekerjaan_id'];
        $isi['pekerjaan_nomor'] = $value['pekerjaan_nomor'];
        $isi['pekerjaan_judul'] = $value['pekerjaan_judul'];
        $isi['pekerjaan_status'] = $value['pekerjaan_status'];
        $isi['pegawai_nama'] = $value['pegawai_nama'];
        $isi['pegawai_nama_dep'] = $value['pegawai_unit_name'];
        // $isi['pekerjaan_progress'] = $value['pekerjaan_progress'];
        $isi['total'] = $isi_total['total'];
        $isi['tanggal_akhir'] =  date("Y-m-d", strtotime($value['tanggal_akhir']));
        $isi['tanggal_start'] = ($data_tgl_start['pekerjaan_disposisi_waktu'] != '') ? date("Y-m-d", strtotime($data_tgl_start['pekerjaan_disposisi_waktu'])) : '-';
        $isi['pekerjaan_status'] = $value['pekerjaan_status'];
        $isi['status_avp'] = $status_avp;

        array_push($data, $isi);
    }
    echo json_encode($data);
  }
  /* Get Pekerjaan Berjalan */
  /* GET */
}
