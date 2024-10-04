<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tools_Bantuan_Model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	public function getPekerjaan($param = null)
	{
		if (!empty($param['pekerjaan_status'])) $this->db->where_in('pekerjaan_status', $param['pekerjaan_status']);
		if (!empty($param['pekerjaan_nomor'])) $this->db->like('UPPER(pekerjaan_nomor)', strtoupper($param['pekerjaan_nomor']));
		if (!empty($param['pekerjaan_judul'])) $this->db->like('UPPER(pekerjaan_judul)', strtoupper($param['pekerjaan_judul']));
		if (isset($param['pekerjaan_jenis'])) {
			if ($param['pekerjaan_jenis'] == '1') {
				$this->db->where("klasifikasi_pekerjaan_rkap = 'y'");
			} else {
				$this->db->where("id_klasifikasi_pekerjaan!='1'");
			}
		}

		$this->db->select('*');
		$this->db->from('dec.dec_pekerjaan a');
		$this->db->join('global.global_klasifikasi_pekerjaan b', 'b.klasifikasi_pekerjaan_id = a.id_klasifikasi_pekerjaan', 'left');

		$this->db->order_by("pekerjaan_nomor ASC,pekerjaan_status ASC");

		$sql = $this->db->get();

		return $sql->result_array();
	}

	public function getDisposisi($param = null)
	{
		if (isset($param['pekerjaan_id'])) $this->db->where('id_pekerjaan', $param['pekerjaan_id']);
		$this->db->select('*');
		$this->db->from('dec.dec_pekerjaan_disposisi a');
		// $this->db->join('dec.dec_pekerjaan b', 'b.pekerjaan_id = a.id_pekerjaan', 'left');
		$this->db->join('global.global_pegawai c', 'c.pegawai_nik = a.id_user', 'left');

		$this->db->order_by("CAST(pekerjaan_disposisi_status as INT),pegawai_nama,is_cc ASC");

		$sql = $this->db->get();

		if (isset($param['pekerjaan_id'])) {
			return $sql->result_array();
		} else {
			return false;
		}
	}

	public function getDokumen($param = null)
	{
		if (isset($param['pekerjaan_id'])) $this->db->where('id_pekerjaan', $param['pekerjaan_id']);
		if (isset($param['is_hps'])) $this->db->where('is_hps', $param['is_hps']);
		if (isset($param['is_lama'])) $this->db->where('is_lama', $param['is_lama']);

		$this->db->select('*');

		$this->db->from('dec.dec_pekerjaan_dokumen');

		$this->db->order_by('pekerjaan_dokumen_nomor', 'ASC');
		$this->db->order_by('pekerjaan_dokumen_status', 'ASC');

		$sql = $this->db->get();

		if (isset($param['pekerjaan_id'])) {
			return $sql->result_array();
		} else {
			return false;
		}

		// $this->db->select('');
	}
}

/* End of file Tools_Bantuan_Model.php */
/* Location: ./application/models/Tools_Bantuan_Model.php */