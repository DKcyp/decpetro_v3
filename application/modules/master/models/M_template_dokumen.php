<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Template_dokumen extends CI_Model {
  public function getTemplateData($data = null) {
    $this->db->select('*');
    $this->db->from('global.global_template_dokumen a');
    if (isset($data['id_template_dokumen'])) $this->db->where('id_template_dokumen', $data['id_template_dokumen']);

    $sql = $this->db->get();
    return (isset($data['id_template_dokumen'])) ? $sql->row_array() : $sql->result_array();
  }

  public function storeTemplate($data = null) {
    $this->db->insert('global.global_template_dokumen', $data);
    return $this->db->affected_rows();
  }

  public function updateTemplate($data = null, $id) {
    $this->db->where('id_template_dokumen', $id);
    $this->db->update('global.global_template_dokumen', $data);
    return $this->db->affected_rows();
  }

  public function delTemplate($id) {
    $this->db->where('id_template_dokumen', $id);
    $this->db->delete('global.global_template_dokumen');
  }
}