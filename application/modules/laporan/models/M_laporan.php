<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_laporan extends CI_Model
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
	public function getPekerjaanGrafik($field, $where, $param = null)
	{
		$this->db->select($field);
		$this->db->join('global.global_klasifikasi_pekerjaan b', 'b.klasifikasi_pekerjaan_id = a.id_klasifikasi_pekerjaan', 'left');
		if (!empty($where)) $this->db->where($where);
		if (isset($param['pekerjaan_status'])) $this->db->where_in('pekerjaan_status', $param['pekerjaan_status']);


		return $this->db->get('dec.dec_pekerjaan a');
	}

	public function getDokumenGrafik($field, $where, $param = null)
	{
		$this->db->select($field);
		if (!empty($where)) $this->db->where($where);
		if (isset($param['pekerjaan_dokumen_status'])) $this->db->where_in('pekerjaan_dokumen_status', $param['pekerjaan_dokumen_status']);

		$this->db->join('dec.dec_pekerjaan b', 'b.pekerjaan_id = a.id_pekerjaan', 'left');

		return $this->db->get('dec.dec_pekerjaan_dokumen a');
	}

	public function getPekerjaanDispo($data = null)
	{
		$this->db->select("	pekerjaan_id,pekerjaan_status,pekerjaan_nomor,pekerjaan_judul,pegawai_nama,c.pegawai_unit_name,pekerjaan_progress,pic,pekerjaan_waktu,
		to_char( pekerjaan_waktu_akhir, 'DD-MM-YYYY' ) AS tanggal_akhir,
		to_char( pekerjaan_waktu, 'DD-MM-YYYY' ) AS tanggal_awal,
		to_char( pekerjaan_waktu, 'yyyy-MM-dd' ) AS pekerjaan_waktunya,
		to_char( pekerjaan_waktu_akhir, 'yyyy-MM-dd' ) AS pekerjaan_akhirnya ");
		$this->db->distinct();
		$this->db->from('dec.dec_pekerjaan a');
		$this->db->join('global.global_auth_user b', 'b.usr_id = a.pic', 'left');
		$this->db->join('global.global_pegawai c', 'c.pegawai_nik = b.usr_id');
		$this->db->join('global.global_klasifikasi_pekerjaan d', 'a.id_klasifikasi_pekerjaan=d.klasifikasi_pekerjaan_id', 'left');
		$this->db->join('dec.dec_pekerjaan_disposisi e', 'e.id_pekerjaan = a.pekerjaan_id', 'left');
		if (isset($data['pekerjaan_id'])) $this->db->where('pekerjaan_id', $data['pekerjaan_id']);
		if (isset($data['pic'])) $this->db->where('a.pic', $data['pic']);
		if (isset($data['id_user'])) $this->db->where('e.id_user', $data['id_user']);
		if (isset($data['klasifikasi_pekerjaan_id'])) $this->db->where('d.klasifikasi_pekerjaan_id', $data['klasifikasi_pekerjaan_id']);
		if (isset($data['klasifikasi_pekerjaan_id_non_rkap'])) $this->db->where('d.klasifikasi_pekerjaan_id !=', $data['klasifikasi_pekerjaan_id_non_rkap']);
		if (isset($data['pekerjaan_status'])) $this->db->where_in('pekerjaan_disposisi_status', $data['pekerjaan_status']);
		if (isset($data['pekerjaan_status_not_inpro'])) $this->db->where("pekerjaan_status != '" . $data['pekerjaan_status_not_inpro'] . "' ");
		// $this->db->group_by('pekerjaan_id,pekerjaan_status,pegawai_nama,c.pegawai_unit_name,pekerjaan_nomor');
		$this->db->order_by('pekerjaan_id', 'DESC');
		$this->db->order_by('pekerjaan_waktu', 'DESC');

		$sql = $this->db->get();

		return (isset($data['pekerjaan_id'])) ? $sql->row_array() : $sql->result_array();
	}
	/* GET */
}
