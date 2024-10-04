<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Admin extends CI_Model {
  public function getAdmin($val = null) {
    $this->db->select('*');
    $this->db->from('global.global_admin a');
    $this->db->join('global.global_pegawai b', 'b.pegawai_nik = a.admin_nik', 'left');
    if (!empty($val['admin_id'])) $this->db->where('admin_id', $val['admin_id']);
    if (!empty($val['admin_nik'])) $this->db->where('admin_nik', $val['admin_nik']);

    $sql = $this->db->get();
    return (!empty($val['admin_id'])) ? $sql->row_array() : $sql->result_array();
  }

  public function getUser($val = null) {
    $this->db->select('*');
    $this->db->from('global.global_pegawai');
    if (!empty($val['pegawai_nama'])) $this->db->like('UPPER(pegawai_nama)', strtoupper($val['pegawai_nama']), 'BOTH');
    $this->db->order_by('pegawai_nama', 'asc');
    $this->db->limit('50');

    $sql = $this->db->get();
    return (!empty($val['admin_id'])) ? $sql->row_array() : $sql->result_array();
  }

  public function insertAdmin($val = null) {
    $this->db->insert('global.global_admin', $val);
    return $this->db->affected_rows();
  }

  public function deleteAdmin($id = null) {
    $this->db->where('admin_id', $id);
    $this->db->delete('global.global_admin');
    return $this->db->affected_rows();
  }
}