<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_template_pekerjaan extends CI_Model {
  public function getTemplatePekerjaan($data = null) {
    $this->db->select('*');
    $this->db->from('dec.dec_pekerjaan_template');
    if (isset($data['pekerjaan_template_id'])) $this->db->where('pekerjaan_template_id', $data['pekerjaan_template_id']);

    $sql = $this->db->get();
    return (isset($data['pekerjaan_template_id'])) ? $sql->row_array() : $sql->result_array();
  }

  public function insertTemplatePekerjaan($data = null) {
    $this->db->insert('dec.dec_pekerjaan_template', $data);
    return $this->db->affected_rows();
  }

  public function updateTemplatePekerjaan($data = null, $id = null) {
    $this->db->where('pekerjaan_template_id', $id);
    $this->db->update('dec.dec_pekerjaan_template', $data);
    return $this->db->affected_rows();
  }

  public function deleteTemplatePekerjaan($id) {
    $this->db->where('pekerjaan_template_id', $id);
    $this->db->delete('dec.dec_pekerjaan_template');
  }
}