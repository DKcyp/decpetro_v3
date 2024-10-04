<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Calendar extends MX_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->library('template');
    $sesi = $this->session->userdata();
    if (empty($sesi['pegawai_nik'])) {
      redirect('tampilan');
    }
    $this->load->model('project/M_pekerjaan','Pekerjaan');
    $this->usr_id = $this->session->userdata('usr_id');
    $this->usr_name = $this->session->userdata('nama');
  }

  public function index()
  {
    $data = array();
    $this->template->template_master('calendar/calendar', $data);
  }

  public function show_awal()
  {
    if(!empty($this->input->get('user'))){
      $sesion = $this->db->get_where('global.global_pegawai',array('pegawai_nik'=>$this->input->get('user')))->row_array();
    }else{
      $sesion = $this->session->userdata();
    }


    $events = array();
    $usr_id = $sesion['pegawai_nik'];

    $this->db->select("CASE WHEN pegawai_jabatan = '30A   ' THEN '1' WHEN pegawai_jabatan = '20F' THEN '2' ELSE pegawai_jabatan END AS jabatan");
    $this->db->from('global.global_pegawai');
    $this->db->where('pegawai_nik', $usr_id);
    $sql = $this->db->get();
    $jabatan = $sql->row_array();

    if ($jabatan['jabatan'] == '1' || $jabatan['jabatan'] == '2') {
      if ($jabatan['jabatan'] == '2') {
        $sql = "SELECT d.*,a.id_pekerjaan,a.id_user,a.pekerjaan_disposisi_id,b.pekerjaan_judul,b.pekerjaan_estimasi_mulai as pekerjaan_disposisi_waktu,b.pekerjaan_estimasi_selesai as pekerjaan_waktu_akhir,c.pegawai_nama,b.pekerjaan_prioritas
        FROM dec.dec_pekerjaan_disposisi a
        INNER JOIN dec.dec_pekerjaan b ON a.id_pekerjaan = b.pekerjaan_id
        LEFT JOIN global.global_pegawai c ON a.id_user = c.pegawai_nik
        LEFT JOIN global.global_bagian_detail d ON d.id_pegawai = a.id_user
        WHERE a.pekerjaan_disposisi_status = '3'
        ";
        $query = $this->db->query($sql)->result_array();

        foreach ($query as $value) {
          $waktu_akhir = ($value['pekerjaan_waktu_akhir'] != null)? $value['pekerjaan_waktu_akhir'] : $value['pekerjaan_disposisi_waktu'];
          $judul = ($value['pekerjaan_judul'] != null)? $value['pekerjaan_judul'].'('.$value['pegawai_nama'].')' : '-';
          $color = ($value['pekerjaan_prioritas'] == '2')? 'yellow' : 'green' ;

          $events[] = [
            'id' => $value['pekerjaan_disposisi_id'],
            'title' => $judul,
            'start' => $value['pekerjaan_disposisi_waktu'],
            'end' => $waktu_akhir,
            'color' => $color,
          ];
        }
      }
      $sql = "SELECT d.*,a.id_pekerjaan,a.id_user,a.pekerjaan_disposisi_id,b.pekerjaan_judul,b.pekerjaan_estimasi_mulai as pekerjaan_disposisi_waktu,b.pekerjaan_estimasi_selesai as pekerjaan_waktu_akhir,c.pegawai_nama,b.pekerjaan_prioritas
      FROM dec.dec_pekerjaan_disposisi a
      INNER JOIN dec.dec_pekerjaan b ON a.id_pekerjaan = b.pekerjaan_id
      LEFT JOIN global.global_pegawai c ON a.id_user = c.pegawai_nik
      LEFT JOIN global.global_bagian_detail d ON d.id_pegawai = a.id_user
      WHERE a.pekerjaan_disposisi_status = '4'
      ";
      $query = $this->db->query($sql)->result_array();

      foreach ($query as $value) {
        $sql = "SELECT a.id_pekerjaan,a.id_user,a.pekerjaan_disposisi_id,d.pekerjaan_judul,d.pekerjaan_estimasi_mulai as pekerjaan_disposisi_waktu,d.pekerjaan_estimasi_selesai as pekerjaan_waktu_akhir,c.pegawai_nama,d.pekerjaan_prioritas from dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_bagian_detail b ON b.id_pegawai = a.id_user LEFT JOIN global.global_pegawai c ON a.id_user = c.pegawai_nik INNER JOIN dec.dec_pekerjaan d ON a.id_pekerjaan = d.pekerjaan_id where pekerjaan_disposisi_status = '5' AND id_pekerjaan = '".$value['id_pekerjaan']."' AND id_bagian = '".$value['id_bagian']."'
        ";
        $query2 = $this->db->query($sql)->row_array();
        $waktu_akhir = ($query2['pekerjaan_waktu_akhir'] != null)? $query2['pekerjaan_waktu_akhir'] : $query2['pekerjaan_disposisi_waktu'];
        $judul = ($query2['pekerjaan_judul'] != null)? $query2['pekerjaan_judul'].'('.$query2['pegawai_nama'].')' : '-';
        $color = ($query2['pekerjaan_prioritas'] == '2')? 'yellow' : 'green' ;

        $events[] = [
          'id' => $query2['pekerjaan_disposisi_id'],
          'title' => $judul,
          'start' => $query2['pekerjaan_disposisi_waktu'],
          'end' => $waktu_akhir,
          'color' => $color,
        ];
      }
      echo json_encode($events);
    } else {
      $this->db->select("a.pekerjaan_disposisi_id,b.pekerjaan_judul,a.pekerjaan_disposisi_waktu,a.pekerjaan_disposisi_waktu_finish as pekerjaan_waktu_akhir,c.pegawai_nama,b.pekerjaan_prioritas");
      $this->db->from('dec.dec_pekerjaan_disposisi a');
      $this->db->join('dec.dec_pekerjaan b', 'a.id_pekerjaan = b.pekerjaan_id', 'inner');
      $this->db->join('global.global_pegawai c', 'a.id_user = c.pegawai_nik', 'left');
      $this->db->where('a.pekerjaan_disposisi_status', '5');
      $this->db->where('a.id_user', $usr_id);
      $this->db->order_by('a.id_user', 'asc');
      $sql = $this->db->get();
      $booking = $sql->result_array();
      foreach ($booking as $value) {
        $waktu_akhir = ($value['pekerjaan_waktu_akhir'] != null)? $value['pekerjaan_waktu_akhir'] : $value['pekerjaan_disposisi_waktu'];
        $judul = ($value['pekerjaan_judul'] != null)? $value['pekerjaan_judul'].'('.$value['pegawai_nama'].')' : '-';
        $color = ($value['pekerjaan_prioritas'] == '2')? 'yellow' : 'green' ;

        $events[] = [
          'id' => $value['pekerjaan_disposisi_id'],
          'title' => $judul,
          'start' => $value['pekerjaan_disposisi_waktu'],
          'end' => $waktu_akhir,
          'color' => $color,
        ];
      }
      echo json_encode($events);
    }
  }

  public function show_table()
  {
    if(!empty($this->input->get('user'))){
      $sesion = $this->db->get_where('global.global_pegawai',array('pegawai_nik'=>$this->input->get('user')))->row_array();
    }else{
      $sesion = $this->session->userdata();
    }

    $bln = $this->input->get('bln');
    $thn = $this->input->get('thn');

    $usr_id = $sesion['pegawai_nik'];
    $events = array();

    $this->db->select("CASE WHEN pegawai_jabatan = '30A   ' THEN '1' WHEN pegawai_jabatan = '20F' THEN '2' ELSE pegawai_jabatan END AS jabatan");
    $this->db->from('global.global_pegawai');
    $this->db->where('pegawai_nik', $usr_id);
    $sql = $this->db->get();
    $jabatan = $sql->row_array();

    if ($jabatan['jabatan'] == '1' || $jabatan['jabatan'] == '2') {
      if ($jabatan['jabatan'] == '2') {
        $sql = "SELECT d.*,a.id_pekerjaan,a.id_user,a.pekerjaan_disposisi_id,b.pekerjaan_judul,b.pekerjaan_estimasi_mulai as pekerjaan_disposisi_waktu,b.pekerjaan_estimasi_selesai as pekerjaan_waktu_akhir,c.pegawai_nama,b.pekerjaan_prioritas
        FROM dec.dec_pekerjaan_disposisi a
        INNER JOIN dec.dec_pekerjaan b ON a.id_pekerjaan = b.pekerjaan_id
        LEFT JOIN global.global_pegawai c ON a.id_user = c.pegawai_nik
        LEFT JOIN global.global_bagian_detail d ON d.id_pegawai = a.id_user
        WHERE a.pekerjaan_disposisi_status = '3' AND (EXTRACT(MONTH FROM b.pekerjaan_estimasi_mulai) ='".$bln."' OR EXTRACT(MONTH FROM b.pekerjaan_estimasi_selesai) ='".$bln."')AND (EXTRACT(YEAR FROM b.pekerjaan_estimasi_mulai) ='".$thn."' OR EXTRACT(YEAR FROM b.pekerjaan_estimasi_selesai) ='".$thn."') ORDER BY b.pekerjaan_prioritas DESC NULLS LAST
          ";
          $query = $this->db->query($sql)->result_array();
          foreach ($query as $value) {
           $resultArray[] = $value;
         }
       }
       $sql = "SELECT d.*,a.id_pekerjaan,a.id_user,a.pekerjaan_disposisi_id,b.pekerjaan_judul,b.pekerjaan_estimasi_mulai as pekerjaan_disposisi_waktu,b.pekerjaan_estimasi_selesai as pekerjaan_waktu_akhir,c.pegawai_nama,b.pekerjaan_prioritas
       FROM dec.dec_pekerjaan_disposisi a
       INNER JOIN dec.dec_pekerjaan b ON a.id_pekerjaan = b.pekerjaan_id
       LEFT JOIN global.global_pegawai c ON a.id_user = c.pegawai_nik
       LEFT JOIN global.global_bagian_detail d ON d.id_pegawai = a.id_user
       WHERE a.pekerjaan_disposisi_status = '4' AND (EXTRACT(MONTH FROM b.pekerjaan_estimasi_mulai) ='".$bln."' OR EXTRACT(MONTH FROM b.pekerjaan_estimasi_selesai) ='".$bln."')AND (EXTRACT(YEAR FROM b.pekerjaan_estimasi_mulai) ='".$thn."' OR EXTRACT(YEAR FROM b.pekerjaan_estimasi_selesai) ='".$thn."') ORDER BY b.pekerjaan_prioritas DESC NULLS LAST
        ";
        $query = $this->db->query($sql)->result_array();

        foreach ($query as $value) {
          $sql = "SELECT a.id_pekerjaan,a.id_user,a.pekerjaan_disposisi_id,d.pekerjaan_judul,d.pekerjaan_estimasi_mulai as pekerjaan_disposisi_waktu,d.pekerjaan_estimasi_selesai as pekerjaan_waktu_akhir,c.pegawai_nama,d.pekerjaan_prioritas from dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_bagian_detail b ON b.id_pegawai = a.id_user LEFT JOIN global.global_pegawai c ON a.id_user = c.pegawai_nik LEFT JOIN dec.dec_pekerjaan d ON a.id_pekerjaan = d.pekerjaan_id where pekerjaan_disposisi_status = '5' AND id_pekerjaan = '".$value['id_pekerjaan']."' AND id_bagian = '".$value['id_bagian']."' ORDER BY d.pekerjaan_prioritas DESC NULLS LAST";
          $query2 = $this->db->query($sql)->row_array();
          if ($query2 != null) {
            $resultArray[] = $query2;
          }
        }
        echo json_encode($resultArray);
      } else {

        $this->db->select("a.pekerjaan_disposisi_id,b.pekerjaan_judul,a.pekerjaan_disposisi_waktu,a.pekerjaan_disposisi_waktu_finish as pekerjaan_waktu_akhir,c.pegawai_nama,b.pekerjaan_prioritas");
        $this->db->from('dec.dec_pekerjaan_disposisi a');
        $this->db->join('dec.dec_pekerjaan b', 'a.id_pekerjaan = b.pekerjaan_id', 'inner');
        $this->db->join('global.global_pegawai c', 'a.id_user = c.pegawai_nik', 'left');
        $this->db->where('a.pekerjaan_disposisi_status', '5');
        $this->db->where('a.id_user', $usr_id);

        $this->db->group_start();
        $this->db->where('(EXTRACT(MONTH FROM a.pekerjaan_disposisi_waktu) = '.$bln.' AND EXTRACT(MONTH FROM a.pekerjaan_disposisi_waktu_finish) = '.$bln.')', null, false);
        $this->db->where('(EXTRACT(YEAR FROM a.pekerjaan_disposisi_waktu) = '.$thn.' AND EXTRACT(YEAR FROM a.pekerjaan_disposisi_waktu_finish) = '.$thn.')', null, false);
        $this->db->or_where("'".$thn."-".$bln."-01' BETWEEN a.pekerjaan_disposisi_waktu AND a.pekerjaan_disposisi_waktu_finish", null, false);
        $this->db->group_end();

        $this->db->order_by('a.id_user', 'asc');
        $this->db->order_by('b.pekerjaan_prioritas', 'DESC');
        // $this->db->order_by('b.pekerjaan_prioritas NULLS LAST');
        $sql = $this->db->get();
        echo json_encode($sql->result_array());
      }
    }

    public function get_user_list(){
      $isi = $this->session->userdata();

      $list['results'] = array();

      $param['pegawai_nama'] = $this->input->get('pegawai_nama');
      $data = $this->Pekerjaan->getUserCangunList($param);
      foreach ($data as $key => $value) {
        array_push($list['results'], [
          'id' => $value['pegawai_nik'],
          'text' => $value['pegawai_nama'] . ' - ' . $value['pegawai_postitle'],
        ]);
      }
      echo json_encode($list);
    }
  }