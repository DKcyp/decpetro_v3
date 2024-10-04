<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_urutan_proyek extends CI_Model {
  public function getUrutanProyek($data = null) {
    $this->db->select('*');
    $this->db->from('global.global_urutan_proyek');
    if (isset($data['urutan_proyek_id'])) $this->db->where('urutan_proyek_id', $data['urutan_proyek_id']);

    $sql = $this->db->get();
    return (isset($data['urutan_proyek_id'])) ? $sql->row_array() : $sql->result_array();
  }

  public function insertUrutanProyek($data = null) {
    $this->db->insert('global.global_urutan_proyek', $data);

    return   $this->db->affected_rows();
  }

  public function updateUrutanProyek($data = null, $id = null) {
    $this->db->where('urutan_proyek_id', $id);
    $this->db->update('global.global_urutan_proyek', $data);

    return  $this->db->affected_rows();
  }

  public function deleteUrutanProyek($id = null) {
    $this->db->where('urutan_proyek_id', $id);
    $this->db->delete('global.global_urutan_proyek');

    return  $this->db->affected_rows();
  }

  public function getSectionArea($data = null) {
    $this->db->select('*');
    $this->db->from('global.global_section_area a');
    $this->db->join('global.global_urutan_proyek b', 'a.id_urutan_proyek = b.urutan_proyek_id', 'left');
    if (isset($data['id_urutan_proyek'])) $this->db->where('b.urutan_proyek_id', $data['id_urutan_proyek']);
    if (isset($data['section_area_id'])) $this->db->where('a.section_area_id', $data['section_area_id']);

    $sql = $this->db->get();
    return (isset($data['section_area_id'])) ? $sql->row_array() : $sql->result_array();
  }

  public function insertSectionArea($data = null) {
    $this->db->insert('global.global_section_area', $data);

    return   $this->db->affected_rows();
  }

  public function updateSectionArea($data = null, $id = null) {
    $this->db->where('section_area_id', $id);
    $this->db->update('global.global_section_area', $data);

    return  $this->db->affected_rows();
  }

  public function deleteSectionArea($id = null) {
    $this->db->where('section_area_id', $id);
    $this->db->delete('global.global_section_area');

    return  $this->db->affected_rows();
  }
}