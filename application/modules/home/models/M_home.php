<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_home extends CI_Model {
	public function getPekerjaanTotal($data = null) {
		$this->db->select("count(*) as total");
    $this->db->from('dec.dec_pekerjaan');
    $this->db->where_in('pekerjaan_status', $data['status']);
    if ($data['bulan'] != '') $this->db->where("EXTRACT(MONTH FROM pekerjaan_waktu) = '".$data['bulan']."'"); 
    if ($data['tahun'] != '') $this->db->where("EXTRACT(YEAR FROM pekerjaan_waktu) = '".$data['tahun']."'");

    $sql = $this->db->get();
    return $sql->row_array();
	}

	public function getPekerjaanStatus($data = null) {
		$this->db->select("count(pekerjaan_id) as total, CASE WHEN klasifikasi_pekerjaan_id = '1' THEN 'RKAP' ELSE 'Non RKAP'END AS nama");
    $this->db->from('global.global_klasifikasi_pekerjaan a');
    $this->db->join('dec.dec_pekerjaan b', 'b.id_klasifikasi_pekerjaan = a.klasifikasi_pekerjaan_id', 'left');
    $this->db->where_in('pekerjaan_status', $data['status']);
    if ($data['bulan'] != '') $this->db->where("EXTRACT(MONTH FROM pekerjaan_waktu) = '".$data['bulan']."'"); 
    if ($data['tahun'] != '') $this->db->where("EXTRACT(YEAR FROM pekerjaan_waktu) = '".$data['tahun']."'");
    $this->db->group_by('nama');

    $sql = $this->db->get();
    return $sql->result_array();
	}

	public function getPekerjaanBulan($data = null) {
		$this->db->select("COUNT(*) AS total, to_char(pekerjaan_waktu, 'Month') as bulan, EXTRACT(MONTH FROM TO_DATE(to_char(pekerjaan_waktu, 'Month'), 'Month')) AS bln");
    $this->db->from('dec.dec_pekerjaan');
    $this->db->where_in('pekerjaan_status', $data['status']);
    if ($data['bulan'] != '') $this->db->where("EXTRACT(MONTH FROM pekerjaan_waktu) = '".$data['bulan']."'"); 
    if ($data['tahun'] != '') $this->db->where("EXTRACT(YEAR FROM pekerjaan_waktu) = '".$data['tahun']."'");
    $this->db->group_by("to_char(pekerjaan_waktu, 'Month')");
    $this->db->order_by('bln', 'asc');

    $sql = $this->db->get();
    return $sql->result_array();
	}

	public function getDokumenTotal($data = null) {
		$this->db->select("COUNT(*) AS total");
    $this->db->from('dec.dec_pekerjaan_dokumen');
    $this->db->where("(is_lama = 'n' OR is_lama is NULL)");
    $this->db->where('pekerjaan_dokumen_awal', 'n');
    if ($data['bulan'] != '') $this->db->where("EXTRACT(MONTH FROM pekerjaan_dokumen_waktu) = '".$data['bulan']."'"); 
    if ($data['tahun'] != '') $this->db->where("EXTRACT(YEAR FROM pekerjaan_dokumen_waktu) = '".$data['tahun']."'");

    $sql = $this->db->get();
    return $sql->row_array();
	}

	public function getDokumenStatus($data = null) {
		$this->db->select("COUNT(id_pekerjaan), CASE WHEN pekerjaan_dokumen_status <= '3' THEN 'In Progress' WHEN pekerjaan_dokumen_status >= '4' AND pekerjaan_dokumen_status <= '6' THEN 'IFA' WHEN pekerjaan_dokumen_status > '6' THEN 'IFC' END AS dokumen_status ");
    $this->db->from('dec.dec_pekerjaan_dokumen');
    $this->db->where("(is_lama = 'n' OR is_lama is NULL)");
    $this->db->where('pekerjaan_dokumen_awal', 'n');
    if ($data['bulan'] != '') $this->db->where("EXTRACT(MONTH FROM pekerjaan_dokumen_waktu) = '".$data['bulan']."'"); 
    if ($data['tahun'] != '') $this->db->where("EXTRACT(YEAR FROM pekerjaan_dokumen_waktu) = '".$data['tahun']."'");
    $this->db->group_by("dokumen_status");
    $this->db->order_by('dokumen_status', 'desc');

    $sql = $this->db->get();
    return $sql->result_array();
	}

	public function getTransmitalTotal($data = null) {
		$this->db->select("COUNT(*) AS total");
    $this->db->distinct();
    $this->db->from('dec.dec_pekerjaan a');
    $this->db->join('global.global_auth_user b', 'b.usr_id = a.pic', 'left');
    $this->db->join('global.global_pegawai c', 'c.pegawai_nik = b.usr_id', 'left');
    $this->db->join('global.global_klasifikasi_pekerjaan d', 'a.id_klasifikasi_pekerjaan = d.klasifikasi_pekerjaan_id', 'left');
    $this->db->join('dec.dec_pekerjaan_disposisi_transmital e', 'e.id_pekerjaan = a.pekerjaan_id', 'left');
    $this->db->join('global.global_bagian f', 'f.bagian_id = e.id_bagian', 'left');
    $this->db->join('dec.dec_pekerjaan_dokumen_transmital g', 'a.pekerjaan_id = g.id_pekerjaan', 'left');
    $this->db->join('global.global_bagian h', 'h.bagian_id = g.id_bagian', 'left');
    $this->db->where_in('pekerjaan_disposisi_transmital_status', $data['status']);
    $this->db->where('is_lama', 'n');
    if ($data['bulan'] != '') $this->db->where("EXTRACT(MONTH FROM pekerjaan_dokumen_waktu) = '".$data['bulan']."'"); 
    if ($data['tahun'] != '') $this->db->where("EXTRACT(YEAR FROM pekerjaan_dokumen_waktu) = '".$data['tahun']."'");

    $sql = $this->db->get();
    return $sql->row_array();
	}

	public function getTransmitalStatus($data = null) {
		$this->db->select("COUNT(g.id_pekerjaan) as count, CASE WHEN pekerjaan_disposisi_transmital_status = '5' THEN 'Selesai' ELSE CASE WHEN pekerjaan_dokumen_status = '6' THEN 'Cangun' ELSE 'Was Pro' END END AS dokumen_status");
    $this->db->distinct();
    $this->db->from('dec.dec_pekerjaan a');
    $this->db->join('global.global_auth_user b', 'b.usr_id = a.pic', 'left');
    $this->db->join('global.global_pegawai c', 'c.pegawai_nik = b.usr_id', 'left');
    $this->db->join('global.global_klasifikasi_pekerjaan d', 'a.id_klasifikasi_pekerjaan = d.klasifikasi_pekerjaan_id', 'left');
    $this->db->join('dec.dec_pekerjaan_disposisi_transmital e', 'e.id_pekerjaan = a.pekerjaan_id', 'left');
    $this->db->join('global.global_bagian f', 'f.bagian_id = e.id_bagian', 'left');
    $this->db->join('dec.dec_pekerjaan_dokumen_transmital g', 'a.pekerjaan_id = g.id_pekerjaan', 'left');
    $this->db->join('global.global_bagian h', 'h.bagian_id = g.id_bagian', 'left');
    $this->db->where_in('pekerjaan_disposisi_transmital_status', $data['status']);
    $this->db->where('is_lama', 'n');
    if ($data['bulan'] != '') $this->db->where("EXTRACT(MONTH FROM pekerjaan_dokumen_waktu) = '".$data['bulan']."'"); 
    if ($data['tahun'] != '') $this->db->where("EXTRACT(YEAR FROM pekerjaan_dokumen_waktu) = '".$data['tahun']."'");
    $this->db->group_by("dokumen_status");
    $this->db->order_by('dokumen_status', 'desc');

    $sql = $this->db->get();
    return $sql->row_array();
	}

	public function getPegawai($data = null) {
		$sql = "SELECT";
    	$sql .= "(SELECT COUNT(*) FROM dec.dec_pekerjaan_disposisi b WHERE b.id_user = a.pegawai_nik AND pekerjaan_disposisi_status = '5'";
	    	if ($data['bulan'] != '') $sql .= " AND EXTRACT(MONTH FROM pekerjaan_disposisi_waktu) = ".$data['bulan'];
		    if ($data['tahun'] != '') $sql .= " AND EXTRACT(YEAR FROM pekerjaan_disposisi_waktu) = ".$data['tahun'];
	    $sql .= ") AS totalpekerjaan, ";
	    $sql .= "(SELECT COUNT(*) FROM dec.dec_pekerjaan_dokumen c WHERE c.id_create_awal = a.pegawai_nik AND c.is_lama = 'n' AND c.pekerjaan_dokumen_awal = 'n' ";
		    if ($data['bulan'] != '') $sql .= " AND EXTRACT(MONTH FROM pekerjaan_dokumen_waktu) = ".$data['bulan'];
		    if ($data['tahun'] != '') $sql .= " AND EXTRACT(YEAR FROM pekerjaan_dokumen_waktu) = ".$data['tahun'];
	    $sql .= ") AS totaldokumen,pegawai_nik, pegawai_nama ";
    $sql.=" FROM global.global_pegawai a GROUP BY pegawai_nik, pegawai_nama HAVING ((SELECT COUNT(*) FROM dec.dec_pekerjaan_disposisi b WHERE b.id_user = a.pegawai_nik AND pekerjaan_disposisi_status = '5') <> '0') AND ((SELECT COUNT(*) FROM dec.dec_pekerjaan_dokumen c WHERE c.id_create_awal = a.pegawai_nik AND c.is_lama = 'n' AND c.pekerjaan_dokumen_awal = 'n') <> '0')";
    $sql .= ($data['filter'] != '' && $data['filter'] == '2') ? " ORDER BY totaldokumen DESC, totalpekerjaan DESC" : " ORDER BY totalpekerjaan DESC, totaldokumen DESC";

    return $this->db->query($sql)->result_array();
	}
}