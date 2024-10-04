<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_tampilan extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
	}

	public function dataUser($data = null)
	{
		$this->db->select("pegawai_poscode, pegawai_postitle");
		$this->db->from('global.global_pegawai');
		$this->db->where('pegawai_postitle IS NOT NULL');
		$this->db->order_by('pegawai_poscode', 'asc');
		$this->db->group_by('pegawai_poscode, pegawai_postitle');

		$sql = $this->db->get();

		return $sql->result_array();
	}

	public function deleteRole()
	{
		$this->db->where('1 = 1');
		$this->db->delete('global.global_auth_role');

		return $this->db->affected_rows();
	}


	public function getNotifBaru1($data = null)
	{
		$this->db->select('count(b.pekerjaan_disposisi_id) as total');
		$this->db->from('dec.dec_pekerjaan a');
		$this->db->join('dec.dec_pekerjaan_disposisi b', 'b.id_pekerjaan = a.pekerjaan_id', 'left');
		if (isset($data['pekerjaan_status'])) $this->db->where_in('pekerjaan_disposisi_status', $data['pekerjaan_status']);
		if (isset($data['rkap'])) $this->db->where("id_klasifikasi_pekerjaan = '1'");
		if (isset($data['non_rkap'])) $this->db->where("id_klasifikasi_pekerjaan != '1'");
		if (isset($data['user_id'])) $this->db->where('b.id_user', $data['user_id']);
		$this->db->where("(is_proses is null) ");
		$this->db->where('is_aktif', 'y');
		$sql = $this->db->get();
		return $sql->row_array();
	}

	public function getNotifBaru($data = null)
	{
		$this->db->select("count(distinct pekerjaan_id) as total");
		$this->db->from('dec.dec_pekerjaan a');
		$this->db->join('global.global_auth_user b', 'b.usr_id = a.pic', 'left');
		$this->db->join('global.global_pegawai c', 'c.pegawai_nik = b.usr_id');
		$this->db->join('dec.dec_pekerjaan_disposisi e', 'e.id_pekerjaan = a.pekerjaan_id AND e.pekerjaan_disposisi_status = a.pekerjaan_status', 'left');

		if (isset($data['pekerjaan_status'])) $this->db->where_in('pekerjaan_disposisi_status', $data['pekerjaan_status']);
		if (isset($data['rkap'])) $this->db->where("id_klasifikasi_pekerjaan = '1'");
		if (isset($data['non_rkap'])) $this->db->where("id_klasifikasi_pekerjaan != '1'");
		if (isset($data['user_id'])) $this->db->where("(e.id_user = '".$data['user_id']."')");
		
		$this->db->where("(is_proses is null) ");
		$this->db->where('is_aktif', 'y');
		
		$sql = $this->db->get();

		return $sql->row_array();
	}

	public function getNotifBaruPIC($data = null)
	{
		$this->db->select("count(distinct pekerjaan_id) as total");
		$this->db->from('dec.dec_pekerjaan a');
		$this->db->join('global.global_auth_user b', 'b.usr_id = a.pic', 'left');
		$this->db->join('global.global_pegawai c', 'c.pegawai_nik = b.usr_id');
		$this->db->join('dec.dec_pekerjaan_disposisi e', 'e.id_pekerjaan = a.pekerjaan_id AND e.pekerjaan_disposisi_status = a.pekerjaan_status', 'left');

		if (isset($data['pekerjaan_status'])) $this->db->where_in('pekerjaan_disposisi_status', $data['pekerjaan_status']);
		if (isset($data['rkap'])) $this->db->where("id_klasifikasi_pekerjaan = '1'");
		if (isset($data['non_rkap'])) $this->db->where("id_klasifikasi_pekerjaan != '1'");
		if (isset($data['user_id'])) $this->db->where("(e.id_user = '".$data['user_id']."' OR a.pic='".$data['user_id']."')");
		
		$this->db->where("(is_proses is null) ");
		$this->db->where('is_aktif', 'y');
		
		$sql = $this->db->get();

		return $sql->row_array();
	}

	public function getNotifBaruReject($data = null)
	{
		$this->db->select('count(a.pekerjaan_id) as total');
		$this->db->from('dec.dec_pekerjaan a');

		if (isset($data['pekerjaan_status'])) $this->db->where_in('pekerjaan_status', $data['pekerjaan_status']);
		if (isset($data['rkap'])) $this->db->where("id_klasifikasi_pekerjaan = '1'");
		if (isset($data['non_rkap'])) $this->db->where("id_klasifikasi_pekerjaan != '1'");
		if (isset($data['user_id'])) $this->db->where('a.pic', $data['user_id']);
		// $this->db->where("(is_proses != 'y' OR is_proses is null) ");

		// $this->db->where('is_aktif', 'y');

		$query = $this->db->get();
		return $query->row_array();
	}

	public function getNotif($data = null)
	{
		$this->db->select("klasifikasi_pekerjaan_id,klasifikasi_pekerjaan_nama,count(distinct pekerjaan_id) as total");
		$this->db->distinct();
		$this->db->from('dec.dec_pekerjaan a');
		$this->db->join('global.global_auth_user b', 'b.usr_id = a.pic', 'left');
		$this->db->join('global.global_pegawai c', 'c.pegawai_nik = b.usr_id');
		$this->db->join('dec.dec_pekerjaan_disposisi e', 'e.id_pekerjaan = a.pekerjaan_id AND e.pekerjaan_disposisi_status = a.pekerjaan_status', 'left');
		$this->db->join('global.global_klasifikasi_pekerjaan f','f.klasifikasi_pekerjaan_id = a.id_klasifikasi_pekerjaan','left');
		if (isset($data['pekerjaan_status'])) $this->db->where_in('pekerjaan_disposisi_status', $data['pekerjaan_status']);
		if (isset($data['user_id'])) $this->db->where("(e.id_user = '".$data['user_id']."')");
		$this->db->where("(is_proses is null) ");
		$this->db->where('is_aktif', 'y');
		if(isset($data['rkap'])) $this->db->where('klasifikasi_pekerjaan_rkap',$data['rkap']);
		$this->db->group_by('klasifikasi_pekerjaan_id,klasifikasi_pekerjaan_nama');
		
		$sql = $this->db->get();

		return $sql->row_array();
	}
}

/* End of file MLoket.php */
/* Location: ./application/modules/login/models/Login_model.php */