<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_history extends CI_Model {
  public function getHistory($data = null) {
    $this->db->from('dec.dec_pekerjaan a');
    $this->db->join('global.global_pegawai b', 'b.pegawai_nik = a.pic', 'left');
    if (isset($data['pekerjaan_id'])) $this->db->where('pekerjaan_id', $data['pekerjaan_id']);
    if ($data['pegawai_id_dep'] != 'E53000') $this->db->where("'".$data['pegawai_nik']."' IN (SELECT id_user FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = a.pekerjaan_id)");
    $this->db->where("pekerjaan_status BETWEEN '12' AND '16'");
    $this->db->order_by('pekerjaan_waktu', "desc");
    $sql = $this->db->get();

    return (isset($data['pekerjaan_id'])) ? $sql->row_array() : $sql->result_array();
  }
}
