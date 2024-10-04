<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Import extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
	//Do your magic here
	}

	public function index()
	{
		
	}

	public function import_dokumen_view()
	{
		$this->load->view('tools_bantuan/import_dokumen_view');
	}

	public function import_dokumen_upload()
	{
		// ini_set('display_errors', 1);
		error_reporting(0);
		$data_session = $this->session->userdata();

		$config = array(
			'upload_path'   => FCPATH . 'document/',
			'allowed_types' => 'xls|csv|xlsx'
		);

		$this->load->library('upload', $config);
		if ($this->upload->do_upload('file')) {
			$data = $this->upload->data();
			@chmod($data['full_path'], 0777);

			$this->load->library('Spreadsheet_Excel_Reader');
			$this->spreadsheet_excel_reader->setOutputEncoding('CP1251');
			$this->db->db_set_charset('latin1', 'latin1_swedish_ci');

			$this->spreadsheet_excel_reader->read($data['full_path']);
			$sheets = $this->spreadsheet_excel_reader->sheets[0];
			$data_excel = array();
			$id = create_id();
			for ($i = 2; $i <= $sheets['numRows']; $i++) {

				$kode_dep = $sheets['cells'][$i][4];

				if(strlen($sheets['cells'][$i][5])=='2'){
					$bidang_kode = substr($sheets['cells'][$i][5],1);
				}else{
					$bidang_kode = ($sheets['cells'][$i][5]);
				}

				if($this->input->post('jenis')=='Dokumen'){
					$kode_awal= "J";
				}else{
					$kode_awal = "";
				}

				$template = $this->db->query("SELECT * FROM dec.dec_pekerjaan_template WHERE pekerjaan_template_kode = '".$sheets['cells'][$i][6]."'")->row_array();
				$bidang = $this->db->query("SELECT * FROM global.global_bidang WHERE bidang_kode = '".$bidang_kode."'")->row_array();
				$urutan_proyek = $this->db->query("SELECT * FROM global.global_urutan_proyek WHERE urutan_proyek_kode = '".$sheets['cells'][$i][7]."'")->row_array();
				$section_area = $this->db->query("SELECT * FROM global.global_section_area WHERE section_area_kode = '".$sheets['cells'][$i][8]."'")->row_array();
				


				$data_excel[$i-1]['import_kode'] = $id;
				$data_excel[$i-1]['pekerjaan_dokumen_nama'] = $sheets['cells'][$i][3];
				$data_excel[$i-1]['kode_departemen'] = $sheets['cells'][$i][4];
				$data_excel[$i-1]['id_bidang'] = (!empty($bidang)) ? $bidang['bidang_id'] : 'excel_'.$bidang_kode;
				$data_excel[$i-1]['id_pekerjaan_template'] = (!empty($template)) ? $template['pekerjaan_template_id'] : 'excel_'.$sheets['cells'][$i][6];
				$data_excel[$i-1]['id_urutan_proyek']=(!empty($urutan_proyek)) ? $urutan_proyek['urutan_proyek_id'] : 'excel_'.$sheets['cells'][$i][7];
				$data_excel[$i-1]['id_section_area']=(!empty($section_area)) ? $section_area['section_area_id'] : 'excel_'.$sheets['cells'][$i][8];
				$data_excel[$i-1]['id_urutan_dokumen']=$sheets['cells'][$i]['9'];
				$data_excel[$i-1]['nomor_dokumen']= $sheets['cells'][$i][4].'-'.$sheets['cells'][$i][5].'-'.$sheets['cells'][$i][6].'-'.$sheets['cells'][$i][7].'-'.$sheets['cells'][$i][8].'-'.$sheets['cells'][$i][9];
				$var = $sheets['cells'][$i][11];
				$date = str_replace('/', '-', $var);
				$data_excel[$i-1]['pekerjaan_dokumen_waktu']=($sheets['cells'][$i][11]) ? date("Y-m-d",strtotime($date)) : date('Y-m-d');
				// $data_excel[$i-1]['pekerjaan_dokumen_waktu']=$sheets['cells'][$i][11];
			}
			
			$this->db->insert_batch('import.import_nomor_dokumen', $data_excel);

			header("Location: " . base_url('tools_bantuan/import/import_dokumen_view?import_kode=' . $id));
		}
	}


	public function import_dokumen_insert()
	{
		// insert pekerjaan bayangan
		$pekerjaan_id = '99999999_lawas';

		$param = array();


		$param['pekerjaan_id']=$pekerjaan_id;
		$param['pekerjaan_judul']='Pekerjaan Lawas';
		$param['pekerjaan_waktu']=date('Y-m-d');
		$param['pekerjaan_status'] = '20';
		$param['pekerjaan_waktu_akhir']  = date('Y-m-d');
		$param['pekerjaan_waktu_selesai'] = date('Y-m-d');

		$cek_pekerjaan = $this->db->query("SELECT * FROM dec.dec_pekerjaan WHERE pekerjaan_id = '".$pekerjaan_id."'")->row_array();
		if(empty($cek_pekerjaan)){
			$this->db->insert('dec.dec_pekerjaan', $param);
		}

		$dokumen_import = $this->db->query("SELECT * FROM import.import_nomor_dokumen WHERE import_kode = '".$_GET['import_kode']."'")->result_array();
		foreach($dokumen_import as $val){
			$param_dokumen = [
				'pekerjaan_dokumen_id'=>bin2hex(uniqid()),
				'id_pekerjaan'=> $pekerjaan_id,
				'pekerjaan_dokumen_nama'=> '-',
				'id_bidang'=> $val['id_bidang'],
				'id_pekerjaan_template'=> $val['id_pekerjaan_template'],
				'id_urutan_proyek'=> $val['id_urutan_proyek'],
				'id_section_area'=> $val['id_section_area'],
				'pekerjaan_dokumen_nomor'=>$val['nomor_dokumen'],
				'pekerjaan_dokumen_waktu'=>$val['pekerjaan_dokumen_waktu'],
			];
			$this->db->insert('dec.dec_pekerjaan_dokumen', $param_dokumen);
		}
		$this->db->query("DELETE  FROM import.import_nomor_dokumen WHERE import_kode ='".$_GET['import_kode']."'");
		header("Location: " . base_url('tools_bantuan/import/import_dokumen_view'));
	}

		// insert dokumen 

	public function import_dokumen_update()
	{
		$dokumen_import = $this->db->query("SELECT * FROM import.import_nomor_dokumen WHERE import_kode = '".$_GET['import_kode']."'")->result_array();
		foreach($dokumen_import as $val){
			$id = $val['nomor_dokumen'];
			$param = [
				'id_bidang'=>$val['id_bidang'],
				'id_pekerjaan_template'=>$val['id_pekerjaan_template'],
				'id_urutan_proyek'=>$val['id_urutan_proyek'],
				'id_section_area'=>$val['id_section_area'],
			];
			$this->db->where('pekerjaan_dokumen_nomor',$id);
			$this->db->update('dec.dec_pekerjaan_dokumen', $param);
			header("Location: " . base_url('tools_bantuan/import/import_dokumen_view?import_kode=' . $_GET['import_kode']));
		}
	}

	public function import_dokumen_delete()
	{
		$this->db->query("DELETE  FROM import.import_nomor_dokumen WHERE import_kode ='".$_GET['import_kode']."'");
		header("Location: " . base_url('tools_bantuan/import/import_dokumen_view'));
	}

}

/* End of file Import.php */
/* Location: ./application/modules/tools_bantuan/controllers/Import.php */