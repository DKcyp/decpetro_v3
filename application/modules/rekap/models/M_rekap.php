<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_rekap extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		//Do your magic here
		$this->usr_id = $this->session->userdata('usr_id');
		$this->id_dep = $this->session->userdata('id_dep');
		$this->usr_name = $this->session->userdata('nama');
		$this->lokasi = base_url('gambar/img/');
		$this->reallokasi = realpath(APPPATH . '/gambar/img/');
		$this->icon 	= base_url('assets/icon/');
		$this->realicon = realpath(APPPATH . '/assets2/icon/');

	}

	/* GET */
		public function getPekerjaan($data = null)
		{
			$this->db->select("pekerjaan_nomor, to_char(pekerjaan_waktu, 'DD-MM-YYYY') as pekerjaan_waktu, pekerjaan_judul, klasifikasi_pekerjaan_nama");
			$this->db->from('dec.dec_pekerjaan a');
			$this->db->join('global.global_klasifikasi_pekerjaan b', 'b.klasifikasi_pekerjaan_id = a.id_klasifikasi_pekerjaan', 'left');
			if (isset($data['klasifikasi_pekerjaan_nama'])) $this->db->where('klasifikasi_pekerjaan_nama', $data['klasifikasi_pekerjaan_nama']); 
			$this->db->order_by('pekerjaan_waktu', 'asc');

			$sql = $this->db->get();

			return (isset($data['pekerjaan_id'])) ? $sql->row_array() : $sql->result_array();
		}

		public function getDokumen($data = null)
		{
			$this->db->select("pekerjaan_dokumen_file, pekerjaan_judul");
			$this->db->from('dec.dec_pekerjaan_dokumen a');
			$this->db->join('dec.dec_pekerjaan c', 'c.pekerjaan_id = a.id_pekerjaan', 'left');
			$this->db->order_by('pekerjaan_id, pekerjaan_waktu', 'asc');

			$sql = $this->db->get();

			return (isset($data['dokumen_pekerjaan_id'])) ? $sql->row_array() : $sql->result_array();
		}

		public function getDisposisi($data = null)
		{
			$this->db->select("pekerjaan_nomor, to_char(pekerjaan_waktu, 'DD-MM-YYYY') as pekerjaan_waktu, pekerjaan_judul, klasifikasi_pekerjaan_nama, pekerjaan_disposisi_status");
			$this->db->from('dec.dec_pekerjaan_disposisi a');
			$this->db->join('dec.dec_pekerjaan b', 'b.pekerjaan_id = a.id_pekerjaan', 'left');
			$this->db->join('global.global_klasifikasi_pekerjaan c', 'c.klasifikasi_pekerjaan_id = b.id_klasifikasi_pekerjaan', 'left');
			$this->db->order_by('pekerjaan_waktu, pekerjaan_disposisi_waktu', 'asc');

			$sql = $this->db->get();
			
			return (isset($data['pekerjaan_id'])) ? $sql->row_array() : $sql->result_array();
		}

		public function getPekerjaanGrafik($field, $where)
		{
			$this->db->select($field);
			$this->db->join('global.global_klasifikasi_pekerjaan b', 'b.klasifikasi_pekerjaan_id = a.id_klasifikasi_pekerjaan', 'left');
			if (!empty($where)) $this->db->where($where);
			
			return $this->db->get('dec.dec_pekerjaan a');
		}

		public function getDokumenGrafik($field, $where)
		{
			$this->db->select($field);
			if (!empty($where)) $this->db->where($where);
			$this->db->join('dec.dec_pekerjaan b', 'b.pekerjaan_id = a.id_pekerjaan', 'left');
			
			return $this->db->get('dec.dec_pekerjaan_dokumen a');
		}
	/* GET */

}