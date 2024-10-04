<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_login extends CI_Model {
	public function dataUser($data = null) {
		$this->db->select("*");
		$this->db->from('global.global_pegawai a');
		$this->db->join('global.global_klasifikasi_dokumen b', 'b.id_pegawai = a.pegawai_nik', 'left');

		if (isset($data['pegawai_nik'])) $this->db->where('pegawai_nik', $data['pegawai_nik']);

		$sql = $this->db->get();

		return (isset($data['pegawai_nik'])) ? $sql->row_array() : $sql->result_array();
	}

	public function dataUserBantuan($username = null, $password = null) {
		$this->db->select("*");
		$this->db->from('global.global_auth_user_bantuan a');
		$this->db->join('global.global_pegawai b', 'b.pegawai_nik = a.usr_id', 'left');
		if (isset($username)) $this->db->where('usr_loginname', $username);
		if (isset($password)) $this->db->where('usr_password', md5($password));

		$sql = $this->db->get();

		return (isset($username) && isset($password)) ? $sql->row_array() : $sql->result_array();
	}

	public function updatePegawaiBantuan($id = null, $data = null) {
		$this->db->where('pegawai_nik', $id);
		$this->db->update('global.global_pegawai', $data);
		return $this->db->affected_rows();
	}

	public function insertPegawaiBantuan($data = null) {
		$this->db->insert('global.global_pegawai', $data);
		return $this->db->affected_rows();
	}
}
