<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_limit_pekerjaan extends CI_Model {
  public function getLimitPekerjaan($d = '') {
    $this->db->select('*');
    $this->db->from('global.global_limit_pekerjaan a');
    if(isset($d['limit_pekerjaan_id'])) $this->db->where('limit_pekerjaan_id', $d['limit_pekerjaan_id']);
    $this->db->order_by('bagian_nama', 'asc');
    $q = $this->db->get();
    return (isset($d['limit_pekerjaan_id'])) ? $q->row_array() : $q->result_array();
  }

  public function insertLimitPekerjaan($d = '') {
    $this->db->insert('global.global_limit_pekerjaan', $d);
    return $this->db->affected_rows();
  }

  public function updateLimitPekerjaan($id,$d='') {
    $this->db->where('limit_pekerjaan_id', $id);
    $this->db->update('global.global_limit_pekerjaan', $d);
    return $this->db->affected_rows();
  }

  public function deleteLimitPekerjaan($id) {
    $this->db->where('limit_pekerjaan_id', $id);
    $this->db->delete('global.global_limit_pekerjaan');
    return $this->db->affected_rows();
  }
}