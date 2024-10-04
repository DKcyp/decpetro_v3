<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tools_Bantuan extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Tools_Bantuan_Model', 'M_Tools');
	}

	public function index()
	{
		$this->load->view('tools_bantuan/tools_bantuan');
	}

	public function tools_pekerjaan()
	{
		$data = array();

		$data = array();

		$param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
		$param['is_hps'] = $this->input->get_post('is_hps');
		$param['is_lama'] = $this->input->get_post('is_lama');
		$param['pekerjaan_nomor'] = $this->input->get_post('pekerjaan_nomor');
		$param['pekerjaan_jenis'] = $this->input->get_post('pekerjaan_jenis');
		$param['pekerjaan_judul'] = $this->input->get_post('pekerjaan_judul');

		if (!empty($this->input->get_post('pekerjaan_status'))) {
			$split = explode(',', $this->input->get_post('pekerjaan_status'));
			$param['pekerjaan_status'] = $split;
		}

		$data['pekerjaan'] = $this->M_Tools->getPekerjaan($param);

		// echo $this->db->last_query();

		$data['disposisi'] = $this->M_Tools->getDisposisi($param);

		$this->load->view('tools_bantuan/tools_pekerjaan', $data, FALSE);
	}

	public function tools_dokumen()
	{
		$data = array();

		$param['pekerjaan_id'] = $this->input->get_post('pekerjaan_id');
		$param['is_hps'] = $this->input->get_post('is_hps');
		$param['is_lama'] = $this->input->get_post('is_lama');
		$param['pekerjaan_nomor'] = $this->input->get_post('pekerjaan_nomor');
		$param['pekerjaan_jenis'] = $this->input->get_post('pekerjaan_jenis');
		$param['pekerjaan_judul'] = $this->input->get_post('pekerjaan_judul');

		if (!empty($this->input->get_post('pekerjaan_status'))) {
			$split = explode(',', $this->input->get_post('pekerjaan_status'));
			$param['pekerjaan_status'] = $split;
		}

		$data['pekerjaan'] = $this->M_Tools->getPekerjaan($param);

		echo $this->db->last_query();

		$data['dokumen'] = $this->M_Tools->getDokumen($param);

		$this->load->view('tools_bantuan/tools_dokumen', $data, FALSE);
	}

	public function tools_dokumen_ifa()
	{
		$data = array();

		$pekerjaan = $this->db->query("SELECT pekerjaan_id,pekerjaan_status,id_klasifikasi_pekerjaan FROM dec.dec_pekerjaan WHERE (pekerjaan_status = '15') AND (id_klasifikasi_pekerjaan = '1' OR id_klasifikasi_pekerjaan = '2') ")->result_array();

		foreach ($pekerjaan as $key_pekerjaan => $val_pekerjaan) {
			$dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $val_pekerjaan['pekerjaan_id'] . "' AND (is_lama != 'y' OR is_lama is null) AND (pekerjaan_dokumen_status='4' OR pekerjaan_dokumen_status = '5') ORDER BY cast(pekerjaan_dokumen_status as int) ASC")->result_array();

			foreach ($dokumen as $key_dokumen => $val_dokumen) {

				$dokumen_ifc = $this->db->query("SELECT pekerjaan_dokumen_nama,id_pekerjaan_template FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_status = '9' AND id_pekerjaan = '" . $val_pekerjaan['pekerjaan_id'] . "' AND pekerjaan_dokumen_nama = '" . $val_dokumen['pekerjaan_dokumen_nama'] . "' AND id_pekerjaan_template = '" . $val_dokumen['id_pekerjaan_template'] . "' ")->row_array();


				if (!empty($dokumen_ifc) && ($val_dokumen['pekerjaan_dokumen_nama'] == $dokumen_ifc['pekerjaan_dokumen_nama'] and $val_dokumen['id_pekerjaan_template'] == $dokumen_ifc['id_pekerjaan_template'])) {
					echo "<pre>";
					print_r('skip');
					echo "</pre>";
				} else {
					$param['pekerjaan_dokumen_id'] = create_id();
					$param['id_pekerjaan'] = $val_dokumen['id_pekerjaan'];
					$param['pekerjaan_dokumen_file'] = $val_dokumen['pekerjaan_dokumen_file'];
					// $param['id_pekerjaan_disposisi'] =
					$param['pekerjaan_dokumen_nama'] = $val_dokumen['pekerjaan_dokumen_nama'];
					$param['pekerjaan_dokumen_awal'] = 'n';
					$param['pekerjaan_dokumen_status'] = '9';
					$param['pekerjaan_dokumen_keterangan'] = $val_dokumen['pekerjaan_dokumen_keterangan'];
					$param['who_create'] = $val_dokumen['who_create'];
					$param['is_lama'] = 'n';
					$param['id_pekerjaan_template'] = $val_dokumen['id_pekerjaan_template'];
					$param['pekerjaan_dokumen_revisi'] = $val_dokumen['pekerjaan_dokumen_revisi'];
					$param['pekerjaan_dokumen_status_review'] = $val_dokumen['pekerjaan_dokumen_status_review'];
					$param['id_create'] = $val_dokumen['id_create'];
					$param['is_hps'] = $val_dokumen['is_hps'];
					$param['is_proses'] = $val_dokumen['is_proses'];
					$param['id_create_awal'] = $val_dokumen['id_create_awal'];
					$param['pekerjaan_dokumen_nomor'] = $val_dokumen['pekerjaan_dokumen_nomor'];
					// $param['pekerjaan_dokumen_cc'] =
					$param['is_review'] = $val_dokumen['is_review'];
					$param['pekerjaan_dokumen_jumlah'] = $val_dokumen['pekerjaan_dokumen_jumlah'];
					$param['pekerjaan_dokumen_jenis'] = $val_dokumen['pekerjaan_dokumen_jenis'];
					$param['id_bidang'] = $val_dokumen['id_bidang'];
					$param['id_urutan_proyek'] = $val_dokumen['id_urutan_proyek'];
					$param['id_section_area'] = $val_dokumen['id_section_area'];
					$param['pekerjaan_dokumen_waktu'] = date('Y-m-d H:i:s');
					$param['is_reject'] = $val_dokumen['is_reject'];
					$param['is_update_ifa'] = $val_dokumen['is_update_ifa'];

					$cek = $this->db->insert('dec.dec_pekerjaan_dokumen', $param);
					if ($cek) {
						echo 'berhasil';
					} else {
						echo 'gagal';
					}
				}
			}
		}
	}

	public function delete_cc_usulan()
	{
		$pekerjaan = $this->db->query("SELECT * FROM dec.dec_pekerjaan WHERE pekerjaan_status IN('-','','1','2','3','4')")->result_array();
		foreach ($pekerjaan as $value) {
			$data_cc = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc IS NOT NULL")->result_array();
			foreach ($data_cc as $value1) {
				// hapus cc yang ada tapi masih usulan
				$hapus = $this->db->query("DELETE FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $value['pekerjaan_id'] . "' AND pekerjaan_disposisi_id = '" . $value1['pekerjaan_disposisi_id'] . "'");
				echo $this->db->last_query();
				if ($hapus) {
					echo 'berhasil';
				}
			}
		}
	}

	public function delete_cc_double()
	{
		// cari data duplikat nya

		$query = "DELETE FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_id IN (SELECT MAX(pekerjaan_disposisi_id) FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '8' AND is_cc IS NOT NULL GROUP BY id_pekerjaan, id_user, is_cc HAVING COUNT(*) > 1)";

		$hapus = $this->db->query($query);

		if ($hapus) {
			echo "Data duplikat telah dihapus.";
		} else {
			echo "Gagal menghapus data duplikat.";
		}
	}

	public function delete_disposisi()
	{
		$this->db->where('pekerjaan_disposisi_id', $this->input->get_post('pekerjaan_disposisi_id'));
		$this->db->delete('dec.dec_pekerjaan_disposisi');
	}

	public function update_status()
	{
		$param['is_lama'] = $this->input->get_post('is_lama');
		$id = $this->input->get_post('pekerjaan_dokumen_id');

		$this->db->where('pekerjaan_dokumen_id', $id);
		$this->db->update('dec.dec_pekerjaan_dokumen', $param);
	}

	public function update_waktu_selesai_kosong()
	{
		$pekerjaan = $this->db->query("SELECT * FROM dec.dec_pekerjaan a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_pekerjaan = a.pekerjaan_id WHERE pekerjaan_disposisi_status >= '12' AND pekerjaan_disposisi_status <='15'")->result_array();
		foreach ($pekerjaan as $val) :
			// if($val['pekerjaan_waktu_selesai']=='' && $val['pekerjaan_status']>='12' && $val['pekerjaan_status']<='15'){
			$this->db->query("UPDATE dec.dec_pekerjaan SET pekerjaan_waktu_selesai = '" . $val['pekerjaan_disposisi_waktu'] . "' WHERE pekerjaan_id = '" . $val['pekerjaan_id'] . "'");
			// }
			echo $this->db->last_query();
		endforeach;
	}


	public function update_halaman_dokumen()
	{
		$data = [];
		$this->load->view('halaman_dokumen', $data, FALSE);
	}

	public function penomoran_dokumen()
	{
		$pekerjaan  = $this->db->get('dec.dec_pekerjaan')->result_array();
		foreach ($pekerjaan as $pk) {
			$dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan = '" . $pk['pekerjaan_id'] . "' AND pekerjaan_dokumen_nomor IS NOT NULL AND pekerjaan_dokumen_nomor !='' AND is_lama = 'n'")->result_array();
			print_r($dokumen);
			// foreach($dokumen as $dk){
			// 	$template = $this->db->query("SELECT a.*,b.pekerjaan_dokumen_nomor,pekerjaan_dokumen_waktu,pekerjaan_dokumen_id FROM dec.dec_pekerjaan_template a LEFT JOIN dec.dec_pekerjaan_dokumen b ON b.id_pekerjaan_template = a.pekerjaan_template_id WHERE a.pekerjaan_template_id = '".$dk['id_pekerjaan_template']."'")->num_rows();
			// 	echo "<pre>";
			// 	print_r ($template);
			// 	echo "</pre>";
			// }
		}
	}

	public function update_ukuran_kertas()
	{
		$dokumen  = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan_template IN(SELECT pekerjaan_template_id FROM dec.dec_pekerjaan_template WHERE pekerjaan_template_kode IN('11','12','16','17','18','34','32','33','35','36','3','31','37','38')) AND pekerjaan_dokumen_waktu <= '2023-07-31' AND pekerjaan_dokumen_kertas is null")->result_array();
		foreach ($dokumen as $dok) {
			$data['pekerjaan_dokumen_kertas'] = 'A3';
			$data['pekerjaan_dokumen_orientasi'] = 'Landscape';
			$id = $dok['pekerjaan_dokumen_id'];
			$this->db->where('pekerjaan_dokumen_id', $id);
			$this->db->update('dec.dec_pekerjaan_dokumen', $data);

			echo "<pre>";
			print_r(
				$this->db->last_query()
			);
			echo "</pre>";
		}
	}

	public function nomor_dokumen(){
		$sql = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen  WHERE is_update_ifa = 'y' and is_lama = 'n'  and id_bidang is null order by id_pekerjaan,pekerjaan_dokumen_nomor asc")->result_array();
		foreach($sql as $value){
			$sql2 = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen  WHERE id_pekerjaan = '".$value['id_pekerjaan']."' and is_lama = 'n' and cast(pekerjaan_dokumen_status as int) >= '11' and pekerjaan_dokumen_nama = '".$value['pekerjaan_dokumen_nama']."' and pekerjaan_dokumen_nomor!='".$value['pekerjaan_dokumen_nomor']."' order by id_pekerjaan,pekerjaan_dokumen_nomor asc")->result_array();
			foreach($sql2 as $value2){
				
			}
		}

	}

}

/* End of file Tools_Bantuan.php */
/* Location: ./application/controllers/Tools_Bantuan.php */