<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends MX_Controller {
	public function __construct() {
		parent::__construct();

		$this->load->model('M_login');
	}

	public function index() {
		if (!empty($this->session->userdata('pegawai_nik'))) redirect(base_url('home'));

		if (isset($_GET['pekerjaan_id'])) {
			$url = base_url() . 'login?aksi=' . $this->input->get('aksi') . '&pekerjaan_id=' . $this->input->get('pekerjaan_id') . '&status=' . $this->input->get('status') . '&rkap=' . $this->input->get('rkap') . '&id_user=' . $this->input->get('id_user');
			$to = $this->input->get('id_user');
			readNotif($url, $to);
		}

		$this->load->view('login/login');
	}

	public function auth() {
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$id_user = $this->input->get('id_user');

		$data = $this->M_login->dataUserBantuan($username, $password);

		if ($data) {
			$this->session->set_userdata($data);
			redirect(base_url('home'));
		} else {
			$client_id = "";
			$client_secret = "";
			$tokenUrl = "https://sso.petrokimia-gresik.net/dev/api/User/Login";
			// $tokenUrl = "https://sso.petrokimia-gresik.net/api/User/Login";
			$tokenContent = "grant_type=password&username=" . $username . "&password=" . $password;
			$authorization = base64_encode("$client_id:$client_secret");
			$tokenHeaders = array("Authorization: Basic {$authorization}", "Content-Type: application/x-www-form-urlencoded");

			$token = curl_init();
			curl_setopt($token, CURLOPT_URL, $tokenUrl);
			curl_setopt($token, CURLOPT_HTTPHEADER, $tokenHeaders);
			curl_setopt($token, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($token, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($token, CURLOPT_POST, true);
			curl_setopt($token, CURLOPT_POSTFIELDS, $tokenContent);
			$item = curl_exec($token);
			curl_close($token);
			$item = json_decode($item);

			if ($item->status) {
				$isi['pegawai_nik'] = $item->nikSap;
				$data = $this->M_login->dataUser($isi);

				$this->session->set_userdata($data);

				if ($this->session->userdata('pegawai_nik') == $id_user) {
					$url = base_url() . 'Project/Pekerjaan_usulan/detailPekerjaan?aksi=' . $this->input->get('aksi') . '&pekerjaan_id=' . $this->input->get('pekerjaan_id') . '&status=' . $this->input->get('status') . '&rkap=' . $this->input->get('rkap') . '&id_user=' . $this->input->get('id_user');
					redirect($url, 'refresh');
				} else {
					redirect(base_url('home'), 'refresh');
				}
			} else {
				$this->session->set_flashdata('pesan', 'Username dan password Salah');
				redirect(base_url('login'));
			}
		}
	}

	public function logout() {
		$this->session->sess_destroy();

		redirect(base_url('login'));
	}

	/*tambahan*/
	public function fk_pegawai()
	{
		$sql_bantuan = $this->db->get('global.global_pegawai_bantuan')->result_array();
		foreach ($sql_bantuan as $value) {
			$sql_pegawai = $this->db->get("global.global_pegawai WHERE pegawai_nik = '" . $value['pegawai_nik'] . "'")->row_array();

			$sql_pegawai_bantuan = $this->db->get("global.global_pegawai_bantuan WHERE pegawai_nik = '" . $value['pegawai_nik'] . "'")->row_array();

			if ($sql_pegawai) {
				$id = $sql_pegawai['pegawai_nik'];
				$param["pegawai_nik_lama"] =  $sql_pegawai['pegawai_nik_lama'];
				$param["pegawai_nama"] =  $sql_pegawai['pegawai_nama'];
				$param["pegawai_unitkerja"] = $sql_pegawai['pegawai_unitkerja'];
				$param["pegawai_nama_unit_kerja"] =  $sql_pegawai["pegawai_nama_unit_kerja"];
				$param["pegawai_poscode"] = $sql_pegawai['pegawai_poscode'];
				$param["pegawai_postitle"] = $sql_pegawai['pegawai_postitle'];
				$param["pegawai_direct_superior"] = $sql_pegawai['pegawai_direct_superior'];
				$param["pegawai_unit_id"] = $sql_pegawai['pegawai_unit_id'];
				$param["pegawai_unit_name"] = $sql_pegawai['pegawai_unit_name'];
				$param["pegawai_id_bag"] = $sql_pegawai['pegawai_id_bag'];
				$param["pegawai_nama_bag"] = $sql_pegawai['pegawai_nama_bag'];
				$param["pegawai_id_dep"] = $sql_pegawai['pegawai_id_dep'];
				$param["pegawai_nama_dep"] = $sql_pegawai['pegawai_nama_dep'];
				$param["pegawai_jabatan"] = $sql_pegawai['pegawai_jabatan'];
				$param["pegawai_updatedon"] = $sql_pegawai['pegawai_updatedon'];

				$this->M_login->updatePegawaiBantuan($id, $param);
			} else {
				$param["pegawai_nik"] = $sql_pegawai_bantuan['pegawai_nik'];
				$param["pegawai_nik_lama"] =  $sql_pegawai_bantuan['pegawai_nik_lama'];
				$param["pegawai_nama"] =  $sql_pegawai_bantuan['pegawai_nama'];
				$param["pegawai_unitkerja"] = $sql_pegawai_bantuan['pegawai_unitkerja'];
				$param["pegawai_nama_unit_kerja"] =  $sql_pegawai_bantuan["pegawai_nama_unit_kerja"];
				$param["pegawai_poscode"] = $sql_pegawai_bantuan['pegawai_poscode'];
				$param["pegawai_postitle"] = $sql_pegawai_bantuan['pegawai_postitle'];
				$param["pegawai_direct_superior"] = $sql_pegawai_bantuan['pegawai_direct_superior'];
				$param["pegawai_unit_id"] = $sql_pegawai_bantuan['pegawai_unit_id'];
				$param["pegawai_unit_name"] = $sql_pegawai_bantuan['pegawai_unit_name'];
				$param["pegawai_id_bag"] = $sql_pegawai_bantuan['pegawai_id_bag'];
				$param["pegawai_nama_bag"] = $sql_pegawai_bantuan['pegawai_nama_bag'];
				$param["pegawai_id_dep"] = $sql_pegawai_bantuan['pegawai_id_dep'];
				$param["pegawai_nama_dep"] = $sql_pegawai_bantuan['pegawai_nama_dep'];
				$param["pegawai_jabatan"] = $sql_pegawai_bantuan['pegawai_jabatan'];
				$param["pegawai_updatedon"] = $sql_pegawai_bantuan['pegawai_updatedon'];

				$this->M_login->insertPegawaiBantuan($param);
			}
		}
		redirect(base_url('login'));
	}

	public function fk_pegawai_bantuan()
	{
		$this->db->query("DELETE FROM global.global_pegawai WHERE pegawai_nik IN (SELECT usr_id FROM global.global_auth_user_bantuan)");

		$this->db->query("INSERT INTO global.global_pegawai SELECT usr_id, usr_id, usr_name, 'E53600', 'Staf Dep Rancang Bangun', 'E53600060B', 'Pl. Dep Rancang Bangun', 'E53400000', 'E53000', 'Dep Rancang Bangun', 'E53400', 'Bag Sipil', 'E53000', 'Dep Rancang Bangun', '41A', NULL FROM global.global_auth_user_bantuan WHERE usr_loginname NOT LIKE '%-AVP'");

		$this->db->query("INSERT INTO global.global_pegawai SELECT usr_id,usr_id,usr_name,'E53400','Bag Proses','E53400000','AVP Sipil','E53000000','E53000','Dep Rancang Bangun','E53400','Bag Sipil','E53000','Dep Rancang Bangun','30A','2022-09-30 15:51:25.963' FROM global.global_auth_user_bantuan WHERE usr_loginname LIKE '%-AVP'");
	}
	/*tambahan*/

	/*fk pgs*/
	public function fk_pgs()
	{
		/*get data login*/
		$client_id = "";
		$client_secret = "";
		$tokenUrl = "http://devsso.petrokimia-gresik.com/api/User/Login";
		$tokenContent = "grant_type=password&username=2190626&password=Petrokimia1";
		$authorization = base64_encode("$client_id:$client_secret");
		$tokenHeaders = array("Authorization: Basic {$authorization}", "Content-Type: application/x-www-form-urlencoded");
		$token = curl_init();
		curl_setopt($token, CURLOPT_URL, $tokenUrl);
		curl_setopt($token, CURLOPT_HTTPHEADER, $tokenHeaders);
		curl_setopt($token, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($token, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($token, CURLOPT_POST, true);
		curl_setopt($token, CURLOPT_POSTFIELDS, $tokenContent);
		$item = curl_exec($token);
		curl_close($token);
		$item = json_decode($item);
		/*get data login*/

		if($item->status){
			/*get data pgs*/
			$api_key = "41FE98BA-2967-40E2-816A-9E93A7F7291A";
			$token = $item->access_token;
			$url = "http://devsso.petrokimia-gresik.com/api/User/GetPtsPgs?apikey={$api_key}";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Authorization: Bearer ' . $token,
				'User-Agent: PostmanRuntime/7.30.0',
			));
			$response = curl_exec($ch);
			if (curl_errno($ch)) {
				echo 'Error: ' . curl_error($ch);
			} else {
				$data = json_decode($response, true);
				for ($i = 0; $i <count($data) ; ++$i) {

					/*cek data pgs*/
					$countDataByID = $this->db->get_where('global.global_pegawai_pgs',['pegawai_pgs_id'=>$data[$i]['userId']])->num_rows();
					/*hpus data pgs*/
					$this->db->query("delete from global.global_pegawai_pgs where pegawai_pgs_id = '".$data[$i]['userId']."'");
					if($countDataByID==0):
						/*insert data pgs*/
						$value['pegawai_pgs_id'] = $data[$i]['userId'];
						$value['pegawai_pgs_nik'] = $data[$i]['nikSap'];
						$value['pegawai_pgs_nama'] = $data[$i]['name'];
						$value['pegawai_pgs_unit_id'] = $data[$i]['unitId'];
						$value['pegawai_pgs_unit_nama'] = $data[$i]['unitName'];
						$value['pegawai_pgs_pemberi_tugas_id'] = $data[$i]['pemberiTugasId'];
						$value['pegawai_pgs_pemberi_tugas_nik'] = $data[$i]['nikSapPemberiTugas'];
						$value['pegawai_pgs_pemberi_tugas_nama'] = $data[$i]['pemberiTugasName'];
						$value['pegawai_pgs_company_code'] = $data[$i]['companyCode'];
						$value['pegawai_pgs_awal_cuti'] = $data[$i]['validFrom'];
						$value['pegawai_pgs_akhir_cuti'] = $data[$i]['validTo'];
						$value['pegawai_pgs_pemberi_tugas_poscode'] = $data[$i]['posCodePemberiTugas'];
						$this->db->insert('global.global_pegawai_pgs',$value);
					else:
						/*update data pgs*/
						$id = $data[$i]['userId'];
						$value['pegawai_pgs_nik'] = $data[$i]['nikSap'];
						$value['pegawai_pgs_nama'] = $data[$i]['name'];
						$value['pegawai_pgs_unit_id'] = $data[$i]['unitId'];
						$value['pegawai_pgs_unit_nama'] = $data[$i]['unitName'];
						$value['pegawai_pgs_pemberi_tugas_id'] = $data[$i]['pemberiTugasId'];
						$value['pegawai_pgs_pemberi_tugas_nik'] = $data[$i]['nikSapPemberiTugas'];
						$value['pegawai_pgs_pemberi_tugas_nama'] = $data[$i]['pemberiTugasName'];
						$value['pegawai_pgs_company_code'] = $data[$i]['companyCode'];
						$value['pegawai_pgs_awal_cuti'] = $data[$i]['validFrom'];
						$value['pegawai_pgs_akhir_cuti'] = $data[$i]['validTo'];
						$value['pegawai_pgs_pemberi_tugas_poscode'] = $data[$i]['posCodePemberiTugas'];
						$this->db->where('pegawai_pgs_id',$id);
						$this->db->update('global.global_pegawai_pgs', $value);
					endif;
				}
			}
			curl_close($ch);
		}

		/*ambil data PGS per hari ini*/
		$dataPGSHariIni = $this->db->get_where('global.global_pegawai_pgs',
			[
				'DATE(pegawai_pgs_awal_cuti)<='=>date('Y-m-d'),
				'DATE(pegawai_pgs_akhir_cuti)>='=>date('Y-m-d'),
			]
		)->result_array();

		foreach($dataPGSHariIni as $keyPGSHariIni => $valPGSHariIni):
			/*ambil data vp yang ada didisposisi*/
			$dataDisposisi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE TRUE AND id_user = '".$valPGSHariIni['pegawai_pgs_pemberi_tugas_nik']."' AND pekerjaan_disposisi_status NOT IN ('5','11')")->result_array();
			/*rubah data ke PGS*/
			foreach($dataDisposisi as $keyDisposisi => $valDisposisi){
				$idDisposisi = $valPGSHariIni['pegawai_pgs_pemberi_tugas_nik'];
				$valueDisposisi['id_user'] = $valPGSHariIni['pegawai_pgs_nik'];
				$valueDisposisi['id_user_asli'] = $valPGSHariIni['pegawai_pgs_pemberi_tugas_nik'];
				$valueDisposisi['is_pgs'] = 'y';

				$this->db->where('id_user',$idDisposisi);
				$this->db->update('dec.dec_pekerjaan_disposisi',$valueDisposisi);
			}
		endforeach;

		/*cek untuk cuti habis*/


	}
	/*fk pgs*/
}
