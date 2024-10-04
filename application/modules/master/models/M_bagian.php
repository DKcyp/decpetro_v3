<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_bagian extends CI_Model {
  public function getBagian($data = null) {
    $this->db->select('*');
    $this->db->from('global.global_bagian');
    if (isset($data['bagian_id'])) $this->db->where('bagian_id', $data['bagian_id']);

    $sql = $this->db->get();
    return (isset($data['bagian_id'])) ? $sql->row_array() : $sql->result_array();
  }
  
  public function insertBagian($data = null) {
    $this->db->insert('global.global_bagian', $data);
    return   $this->db->affected_rows();
  }

  public function updateBagian($data = null, $id = null) {
    $this->db->where('bagian_id', $id);
    $this->db->update('global.global_bagian', $data);
    return  $this->db->affected_rows();
  }

  public function deleteBagian($id = null) {
    $this->db->where('bagian_id', $id);
    $this->db->delete('global.global_bagian');
    return  $this->db->affected_rows();
  }

  public function getBagianAdmin($data = null) {
    $this->db->select('*');
    $this->db->from('global.global_bagian a');
    $this->db->join('global.global_bagian_detail b', 'b.id_bagian = a.bagian_id');
    $this->db->join('global.global_pegawai c', 'c.pegawai_nik = b.id_pegawai', 'left');
    $this->db->join('global.global_admin_bagian d', 'd.id_bagian = a.bagian_id AND d.admin_bagian_nik = c.pegawai_nik', 'left');
    if (isset($data['bagian_id'])) $this->db->where('d.id_bagian', $data['bagian_id']);

    $sql = $this->db->get();
    return (isset($data['bagian_detail_id'])) ? $sql->row_array() : $sql->result_array();
  }

  public function getBagianPegawai($data = null) {
    $this->db->select('*');
    $this->db->from('global.global_bagian a');
    $this->db->join('global.global_bagian_detail b', 'b.id_bagian = a.bagian_id');
    $this->db->join('global.global_pegawai c', 'c.pegawai_nik = b.id_pegawai', 'left');
    if (isset($data['id_bagian'])) $this->db->where('a.bagian_id', $data['id_bagian']);
    if (isset($data['bagian_detail_id'])) $this->db->where('b.bagian_detail_id', $data['bagian_detail_id']);
    if (!empty($data['pegawai_nama'])) $this->db->like('UPPER(pegawai_nama)', strtoupper($data['pegawai_nama']), 'BOTH');

    $sql = $this->db->get();
    return (isset($data['bagian_detail_id'])) ? $sql->row_array() : $sql->result_array();
  }

  public function insertBagianAdmin($data = null) {
    $this->db->insert('global.global_admin_bagian', $data);
    return  $this->db->affected_rows();
  }

  public function updateBagianPegawai($data = null, $id = null) {
    $this->db->where('bagian_detail_id', $id);
    $this->db->update('global.global_bagian_detail', $data);
    return  $this->db->affected_rows();
  }

  public function getUserStaf($data = null) {
    $this->db->select('*');
    $this->db->from('global.global_pegawai');
    if (isset($data['pegawai_nik'])) $this->db->where('b.id_user', $data['pegawai_nik']);
    if (isset($data['pegawai_nama'])) $this->db->where("upper(pegawai_nama) LIKE '%" . strtoupper($data['pegawai_nama']) . "%'");
    $this->db->limit('50');
    $this->db->order_by('pegawai_nama', 'asc');

    $sql = $this->db->get();
    return $sql->result_array();
  }

  public function insertBagianPegawai($data = null) {
    $this->db->insert('global.global_bagian_detail', $data);
    return   $this->db->affected_rows();
  }

  public function deleteBagianPegawai($id = null) {
    $this->db->where('bagian_detail_id', $id);
    $this->db->delete('global.global_bagian_detail');
    return  $this->db->affected_rows();
  }
}