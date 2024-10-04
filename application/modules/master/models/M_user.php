<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_user extends CI_Model
{
	public function getUserTable($data = null)
	{
		$this->db->select('*');
		$this->db->from('global.global_pegawai a');
		$this->db->join('global.global_klasifikasi_dokumen b', 'b.id_pegawai = a.pegawai_nik', 'left');
		$this->db->join('global.global_auth_user_bantuan c', 'c.usr_id = a.pegawai_nik', 'left');
		/* Searching */
		if ($data['search'] != '') {
			$this->db->group_start();
			$this->db->where("pegawai_nama iLIKE '%" . $data['search'] . "%'");
			$this->db->or_where("pegawai_nik iLIKE '%" . $data['search'] . "%'");
			$this->db->or_where("pegawai_postitle iLIKE '%" . $data['search'] . "%'");
			$this->db->group_end();
		}
		/* Searching */
		$this->db->limit($data['length'], $data['start']);
		$this->db->order_by($data['order_col'], $data['order_dir']);
		$sql = $this->db->get();

		return $sql->result_array();
	}

	public function getUserTableTotal($data = null)
	{
		$this->db->select('count(*) as total');
		$this->db->from('global.global_pegawai a');
		$this->db->join('global.global_klasifikasi_dokumen b', 'b.id_pegawai = a.pegawai_nik', 'left');
		$this->db->join('global.global_auth_user_bantuan c', 'c.usr_id = a.pegawai_nik', 'left');
		/* Searching */
		if ($data['search'] != '') {
			$this->db->group_start();
			$this->db->where("pegawai_nama iLIKE '%" . $data['search'] . "%'");
			$this->db->or_where("pegawai_nik iLIKE '%" . $data['search'] . "%'");
			$this->db->or_where("pegawai_postitle iLIKE '%" . $data['search'] . "%'");
			$this->db->group_end();
		}
		/* Searching */
		$sql = $this->db->get();

		return $sql->row_array();
	}

	public function getUser($data = null)
	{
		$this->db->select('*');
		$this->db->from('global.global_pegawai a');
		$this->db->join('global.global_pegawai_pgs b', "a.pegawai_nik = b.pegawai_pgs_pemberi_tugas_nik AND NOW() BETWEEN pegawai_pgs_awal_cuti AND pegawai_pgs_akhir_cuti", 'left');
		if (isset($data['pegawai_nik'])) $this->db->where('pegawai_nik', $data['pegawai_nik']);
		if (isset($data['pegawai_poscode'])) $this->db->where('pegawai_poscode', $data['pegawai_poscode']);
		if (isset($data['usr_name'])) $this->db->where("upper(usr_name) LIKE '%" . strtoupper($data['usr_name']) . "%'");
		if (isset($data['pegawai_jabatan'])) $this->db->like('UPPER(pegawai_jabatan)', strtoupper($data['pegawai_jabatan']), 'BOTH');
		if (isset($data['q'])) {
			$this->db->where("(upper(pegawai_nama) LIKE '%" . strtoupper($data['q']) . "%' OR upper(pegawai_postitle) LIKE '%" . strtoupper($data['q']) . "%' OR upper(pegawai_pgs_nama) LIKE '%" . strtoupper($data['q']) . "%')");
		}
		$this->db->order_by('pegawai_nik', 'asc');
		$sql = $this->db->get();

		return (isset($data['pegawai_nik']) || isset($data['pegawai_poscode'])) ? $sql->row_array() : $sql->result_array();
	}

	public function getUserSelect2($data = null)
	{
		$this->db->select('*');
		$this->db->from('global.global_pegawai a');
		$this->db->join('global.global_pegawai_pgs b', "a.pegawai_nik = b.pegawai_pgs_pemberi_tugas_nik AND NOW() BETWEEN pegawai_pgs_awal_cuti AND pegawai_pgs_akhir_cuti", 'left');
		$this->db->join('global.global_bagian_detail c', 'c.id_pegawai = a.pegawai_nik', 'left');
		$this->db->join('global.global_bagian d', 'd.bagian_id = c.id_bagian', 'left');

		if (isset($data['pegawai_nik'])) $this->db->where('pegawai_nik', $data['pegawai_nik']);
		if (isset($data['pegawai_poscode'])) $this->db->where('pegawai_poscode', $data['pegawai_poscode']);
		if (isset($data['pegawai_direct_superior'])) $this->db->where('pegawai_direct_superior', $data['pegawai_direct_superior']);
		if (isset($data['usr_name'])) $this->db->where("upper(usr_name) LIKE '%" . strtoupper($data['usr_name']) . "%'");
		if (isset($data['pegawai_jabatan'])) $this->db->like('UPPER(pegawai_jabatan)', strtoupper($data['pegawai_jabatan']), 'BOTH');
		if (isset($data['q'])) {
			$this->db->where("(upper(pegawai_nama) LIKE '%" . strtoupper($data['q']) . "%' OR upper(pegawai_postitle) LIKE '%" . strtoupper($data['q']) . "%' OR upper(pegawai_pgs_nama) LIKE '%" . strtoupper($data['q']) . "%')");
		}
		if (isset($data['pegawai_nama'])) $this->db->where("upper(pegawai_nama) LIKE '%" . strtoupper($data['pegawai_nama']) . "%'");
		if (isset($data['bagian_nama'])) $this->db->like('upper(bagian_nama)', str_replace('BAG ', '', strtoupper($data['bagian_nama'])));
		if (isset($data['perencana'])) {
			$this->db->where("(LEFT(pegawai_jabatan,1) != '3' OR pegawai_poscode = '$data[pegawai_poscode_perencana]') ");
			// $this->db->where("(LEFT(pegawai_jabatan,1) != '3')");
		}

		$this->db->order_by('pegawai_nama', 'asc');
		$this->db->limit('50');

		$sql = $this->db->get();
		return $sql->result_array();
	}

	public function insertUserBantuan($data)
	{
		$this->db->insert('global.global_auth_user_bantuan', $data);

		return $this->db->affected_rows();
	}

	public function insertPegawai($data)
	{
		$this->db->insert('global.global_pegawai', $data);

		return $this->db->affected_rows();
	}

	public function deleteMenuRole($id)
	{
		$this->db->where('id_role', $id);
		$this->db->delete('global.global_menu_role');

		return $this->db->affected_rows();
	}
}
