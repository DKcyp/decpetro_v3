<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_klasifikasi_pekerjaan extends CI_Model {
	public function getKlasifikasiPekerjaan($data = null) {
		$this->db->select("klasifikasi_pekerjaan_id, klasifikasi_pekerjaan_kode, klasifikasi_pekerjaan_nama, klasifikasi_pekerjaan_rkap");
		$this->db->from('global.global_klasifikasi_pekerjaan');
		if (isset($data['klasifikasi_pekerjaan_id'])) $this->db->where('klasifikasi_pekerjaan_id', $data['klasifikasi_pekerjaan_id']);
		if(isset($data['klasifikasi_pekerjaan_rkap'])) $this->db->where('klasifikasi_pekerjaan_rkap', $data['klasifikasi_pekerjaan_rkap']);
		if(isset($data['q'])) $this->db->like('UPPER(klasifikasi_pekerjaan_nama)', strtoupper($data['q']), 'BOTH');
		$this->db->order_by('klasifikasi_pekerjaan_rkap', 'desc');
		$this->db->order_by('klasifikasi_pekerjaan_nama', 'asc');

		$sql = $this->db->get();
		return (isset($data['klasifikasi_pekerjaan_id'])) ? $sql->row_array() : $sql->result_array();
	}

	public function insertKlasifikasiPekerjaan($data) {
		$this->db->insert('global.global_klasifikasi_pekerjaan', $data);
		return $this->db->affected_rows();
	}

	public function updateKlasifikasiPekerjaan($data, $id) {
		$this->db->set($data);
		$this->db->where('klasifikasi_pekerjaan_id', $id);
		$this->db->update('global.global_klasifikasi_pekerjaan');

		return $this->db->affected_rows();
	}

	public function deleteKlasifikasiPekerjaan($klasifikasi_pekerjaan_id) {
		$this->db->where('klasifikasi_pekerjaan_id', $klasifikasi_pekerjaan_id);
		$this->db->delete('global.global_klasifikasi_pekerjaan');

		return $this->db->affected_rows();
	}
}
