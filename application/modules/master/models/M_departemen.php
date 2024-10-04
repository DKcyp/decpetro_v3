<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_departemen extends CI_Model {
  public function getDepartemen($data = null) {
    $this->db->select('*');
    $this->db->from('global.global_pegawai_departemen');

    if (isset($data['pegawai_dep_id'])) $this->db->where('pegawai_dep_id', $data['pegawai_dep_id']);
    if(isset($data['q'])) $this->db->like('UPPER(pegawai_dep_nama)', strtoupper($data['q']),'BOTH');

    $this->db->order_by('pegawai_dep_nama', 'asc');

    $sql = $this->db->get();
    return (isset($data['pegawai_dep_id'])) ? $sql->row_array() : $sql->result_array();
  }
}