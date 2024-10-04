<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_role extends CI_Model {
	public function getRole($data = null) {
		$this->db->select('*');
		$this->db->from('global.global_auth_role');
		if (isset($data['rol_name'])) $this->db->where("upper(rol_name) LIKE '%" . strtoupper($data['rol_name']) . "%'");
		if (isset($data['rol_id'])) $this->db->where('rol_id', $data['rol_id']);
		$this->db->order_by('UPPER(rol_name)', 'asc');
		$sql = $this->db->get();

		return (isset($data['rol_id'])) ? $sql->row_array() : $sql->result_array();
	}

	public function getMenu() {
		$this->db->select('*');
		$this->db->from('global.global_menu');
		$this->db->order_by('menu_urut', 'asc');
		$sql = $this->db->get();

		return $sql->result_array();
	}

	public function getMenuRole($data = null) {
		$this->db->select('*');
		$this->db->from('global.global_menu a');
		$this->db->join('global.global_menu_role b', 'a.menu_id = b.id_menu', 'left');
		if (isset($data['rol_id'])) $this->db->where('b.id_role', $data['rol_id']);
		$this->db->order_by('menu_urut', 'asc');
		$sql = $this->db->get();

		return $sql->result_array();
	}

	public function insertRole($data) {
		$this->db->insert('global.global_auth_role', $data);

		return $this->db->affected_rows();
	}

	public function insertMenuRole($data) {
		$this->db->insert('global.global_menu_role', $data);

		return $this->db->affected_rows();
	}

	public function updateRole($data, $id) {
		$this->db->set($data);
		$this->db->where('rol_id', $id);
		$this->db->update('global.global_auth_role');

		return $this->db->affected_rows();
	}

	public function deleteRole($id) {
		$this->db->where('rol_id', $id);
		$this->db->delete('global.global_auth_role');

		return $this->db->affected_rows();
	}

	public function deleteMenuRole($id) {
		$this->db->where('id_role', $id);
		$this->db->delete('global.global_menu_role');

		return $this->db->affected_rows();
	}
}