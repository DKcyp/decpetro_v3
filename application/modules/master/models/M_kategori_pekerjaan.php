<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_kategori_pekerjaan extends CI_Model {
  public function getKategoriPekerjaan($data = null) {
    $this->db->select('*');
    $this->db->from('global.global_kategori_pekerjaan');

    if (isset($data['kategori_pekerjaan_id'])) $this->db->where('kategori_pekerjaan_id', $data['kategori_pekerjaan_id']);
    if(isset($data['q'])) $this->db->like('UPPER(kategori_pekerjaan_nama)', strtoupper($data['q']),'BOTH');

    $this->db->order_by('kategori_pekerjaan_estimasi', 'asc');

    $sql = $this->db->get();
    return (isset($data['kategori_pekerjaan_id'])) ? $sql->row_array() : $sql->result_array();
  }
}