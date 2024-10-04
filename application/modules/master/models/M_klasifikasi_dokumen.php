<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_klasifikasi_dokumen extends CI_Model {
  public function getKlasifikasiDokumen($data = null) {
    $this->db->select('*');
    $this->db->from('global.global_klasifikasi_dokumen a');
    $this->db->join('global.global_pegawai b', 'a.id_pegawai = b.pegawai_nik', 'left');
    if (isset($data['klasifikasi_dokumen_id'])) $this->db->where('klasifikasi_dokumen_id', $data['klasifikasi_dokumen_id']);
    if (isset($data['id_pegawai'])) $this->db->where('id_pegawai', $data['id_pegawai']);

    $sql = $this->db->get();
    return (isset($data['klasifikasi_dokumen_id']) || isset($data['id_pegawai'])) ? $sql->row_array() : $sql->result_array();
  }

  public function insertKlasifikasiDokumen($data = null) {
    $this->db->insert('global.global_klasifikasi_dokumen', $data);
    return $this->db->affected_rows();
  }

  public function updateKlasifikasiDokumen($data = null, $id) {
    $this->db->where('klasifikasi_dokumen_id', $id);
    $this->db->update('global.global_klasifikasi_dokumen', $data);
    return $this->db->affected_rows();
  }

  public function deleteKlasifikasiDokumen($id) {
    $this->db->where('klasifikasi_dokumen_id', $id);
    $this->db->delete('global.global_klasifikasi_dokumen');
  }
}

/* End of file ModelName.php */
