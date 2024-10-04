<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_Task extends CI_Model
{
	/*GET*/
	public function getTaskLama($p=''){
		if(isset($p['user_id'])) $this->db->where("b.id_user = '".$p['user_id']."' AND a.user_id = '".$p['user_id']."'");
		if(isset($p['tahun'])) $this->db->where('tahun',$p['tahun']);
		// $this->db->where("is_cc is null");
		$this->db->select('a.*,b.is_proses,c.*');
		$this->db->distinct();
		$this->db->from('global.global_tasklog a');
		$this->db->join('dec.dec_pekerjaan_disposisi b', 'b.id_pekerjaan = a.id_pekerjaan', 'left');
		$this->db->join('dec.dec_pekerjaan c','c.pekerjaan_id = a.id_pekerjaan','left');
		$this->db->order_by('task_date', 'desc');
		$this->db->order_by('user_action', 'asc');
		$this->db->order_by('is_proses', 'desc');

		$q = $this->db->get();

		if($q){
			return $q->result_array();
		}else{
			return false;
		}
	}

		public function getTask($p=''){
		if(isset($p['user_id'])) $this->db->where("a.user_id = '".$p['user_id']."'");
		if(isset($p['tahun'])) $this->db->where('tahun',$p['tahun']);
		// $this->db->where("is_cc is null");
		$this->db->select('*');
		$this->db->from('global.global_tasklog a');
		$this->db->join('dec.dec_pekerjaan c','c.pekerjaan_id = a.id_pekerjaan','left');
		$this->db->order_by('task_date', 'desc');
		$this->db->order_by('user_action', 'asc');
		$q = $this->db->get();

		if($q){
			return $q->result_array();
		}else{
			return false;
		}
	}

	public function getTaskTotal($p=''){
		if(isset($p['user_id'])) $this->db->where("a.user_id = '".$p['user_id']."'");
		if(isset($p['tahun'])) $this->db->where('tahun',$p['tahun']);
		$this->db->where("is_proses is null");
		$this->db->select('a.*');
		$this->db->from('global.global_tasklog a');
		$this->db->join('dec.dec_pekerjaan c','c.pekerjaan_id = a.id_pekerjaan','left');
		$this->db->join('dec.dec_pekerjaan_disposisi b','b.id_pekerjaan = a.id_pekerjaan AND b.id_user = a.user_id AND b.pekerjaan_disposisi_status = a.status');
		$this->db->order_by('task_date', 'desc');
		$this->db->order_by('user_action', 'asc');
		$q = $this->db->get();

		if($q){
			return $q->result_array();
		}else{
			return false;
		}
	}
	/*GET*/
}

/* End of file M_Admin.php */
