<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rekap extends MX_Controller
{

  public function __construct()
  {
    parent::__construct();
    #auth
    // isLogin();
    #conf dept
    $sesi = $this->session->userdata();
    if (empty($sesi['pegawai_nik'])) {
      redirect('tampilan');
    }
    $this->load->model('M_rekap');
    #session data
    $this->usr_id = $this->session->userdata('usr_id');
    $this->usr_name = $this->session->userdata('nama');
  }

  public function index()
  {
    $this->load->view('rekap/rekap');
  }

  /* GET */
  public function getRKAP()
  {
    $param['klasifikasi_pekerjaan_nama'] = 'RKAP';

    $data = $this->M_rekap->getPekerjaan($param);
    echo json_encode($data);
  }

  public function getNonRKAP()
  {
    $param['klasifikasi_pekerjaan_nama'] = 'Non RKAP';

    $data = $this->M_rekap->getPekerjaan($param);
    echo json_encode($data);
  }

  public function getJasaEngineering()
  {
    $param['klasifikasi_pekerjaan_nama'] = 'Jasa Engineering';

    $data = $this->M_rekap->getPekerjaan($param);
    echo json_encode($data);
  }

  public function getApprovalDokumen()
  {
    $data = $this->M_rekap->getDokumen();
    echo json_encode($data);
  }

  public function getRiwayatDisposisi()
  {
    $data = $this->M_rekap->getDisposisi();
    echo json_encode($data);
  }

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
      $firstDate = $tahun . '-' . $value['id'] . '-01';
      $lastDate = date("Y-m-t", strtotime($firstDate));
      $where['pekerjaan_waktu >= '] = $firstDate;
      $where['pekerjaan_waktu <= '] = $lastDate;
      $where['klasifikasi_pekerjaan_nama = '] = 'RKAP';
      $where['pekerjaan_status != '] = 'y';
      $rencanaRKAP = $this->M_rekap->getPekerjaanGrafik($select, $where)->row();

      $select = array('count(pekerjaan_id) as total');
      $where = array();
      $firstDate = $tahun . '-' . $value['id'] . '-01';
      $lastDate = date("Y-m-t", strtotime($firstDate));
      $where['pekerjaan_waktu >= '] = $firstDate;
      $where['pekerjaan_waktu <= '] = $lastDate;
      $where['klasifikasi_pekerjaan_nama = '] = 'RKAP';
      $where['pekerjaan_status = '] = 'y';
      $realisasiRKAP = $this->M_rekap->getPekerjaanGrafik($select, $where)->row();

      $select = array('count(pekerjaan_id) as total');
      $where = array();
      $firstDate = $tahun . '-' . $value['id'] . '-01';
      $lastDate = date("Y-m-t", strtotime($firstDate));
      $where['pekerjaan_waktu >= '] = $firstDate;
      $where['pekerjaan_waktu <= '] = $lastDate;
      $where['klasifikasi_pekerjaan_nama = '] = 'Non RKAP';
      $where['pekerjaan_status != '] = 'y';
      $rencanaNonRKAP = $this->M_rekap->getPekerjaanGrafik($select, $where)->row();

      $select = array('count(pekerjaan_id) as total');
      $where = array();
      $firstDate = $tahun . '-' . $value['id'] . '-01';
      $lastDate = date("Y-m-t", strtotime($firstDate));
      $where['pekerjaan_waktu >= '] = $firstDate;
      $where['pekerjaan_waktu <= '] = $lastDate;
      $where['klasifikasi_pekerjaan_nama = '] = 'Non RKAP';
      $where['pekerjaan_status = '] = 'y';
      $realisasiNonRKAP = $this->M_rekap->getPekerjaanGrafik($select, $where)->row();

      $select = array('count(pekerjaan_id) as total');
      $where = array();
      $firstDate = $tahun . '-' . $value['id'] . '-01';
      $lastDate = date("Y-m-t", strtotime($firstDate));
      $where['pekerjaan_waktu >= '] = $firstDate;
      $where['pekerjaan_waktu <= '] = $lastDate;
      $where['klasifikasi_pekerjaan_nama = '] = 'Jasa Engineering';
      $where['pekerjaan_status != '] = 'y';
      $rencanaJasaEngineering = $this->M_rekap->getPekerjaanGrafik($select, $where)->row();

      $select = array('count(pekerjaan_id) as total');
      $where = array();
      $firstDate = $tahun . '-' . $value['id'] . '-01';
      $lastDate = date("Y-m-t", strtotime($firstDate));
      $where['pekerjaan_waktu >= '] = $firstDate;
      $where['pekerjaan_waktu <= '] = $lastDate;
      $where['klasifikasi_pekerjaan_nama = '] = 'Jasa Engineering';
      $where['pekerjaan_status = '] = 'y';
      $realisasiJasaEngineering = $this->M_rekap->getPekerjaanGrafik($select, $where)->row();

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
    $where['pekerjaan_status = '] = 'n';
    $baru = $this->M_rekap->getPekerjaanGrafik($select, $where)->row();

    $select = array('count(pekerjaan_id) as total');
    $where = array();
    $where['pekerjaan_status = '] = 'o';
    $proses = $this->M_rekap->getPekerjaanGrafik($select, $where)->row();

    $select = array('count(pekerjaan_id) as total');
    $where = array();
    $where['pekerjaan_status = '] = 'y';
    $selesai = $this->M_rekap->getPekerjaanGrafik($select, $where)->row();

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
    $where['pekerjaan_dokumen_status != '] = 'c';
    $IFA = $this->M_rekap->getDokumenGrafik($select, $where)->row();

    $select = array('count(pekerjaan_dokumen_id) as total');
    $where = array();
    $where['pekerjaan_dokumen_status = '] = 'c';
    $IFC = $this->M_rekap->getDokumenGrafik($select, $where)->row();

    $val['ifa'] = $IFA->total;
    $val['ifc'] = $IFC->total;

    array_push($isi, $val);

    echo json_encode($isi);
  }
  /* GET */
}
