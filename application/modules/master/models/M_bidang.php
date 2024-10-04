<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_bidang extends CI_Model {
  public function getBidang($data = null) {
    $this->db->select('*');
    $this->db->from('global.global_bidang');
    if (isset($data['bidang_id'])) $this->db->where('bidang_id', $data['bidang_id']);

    $sql = $this->db->get();
    return (isset($data['bidang_id'])) ? $sql->row_array() : $sql->result_array();
  }

  public function insertBidang($data = null) {
    $this->db->insert('global.global_bidang', $data);
    return $this->db->affected_rows();
  }

  public function updateBidang($data = null, $id = null) {
    $this->db->where('bidang_id', $id);
    $this->db->update('global.global_bidang', $data);
    return $this->db->affected_rows();
  }

  public function deleteBidang($id) {
    $this->db->where('bidang_id', $id);
    $this->db->delete('global.global_bidang');
  }
}