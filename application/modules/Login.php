<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends MX_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->model('M_login');
	}

	public function index()
	{
		$this->load->view('login/login');
	}

	/* Login */
	public function auth()
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');

		$data = $this->M_login->dataUserBantuan($username, $password);
		// echo $this->db->last_query();
		if ($data) {
			$this->session->set_userdata($data);
			// print_r($data);
			redirect(base_url('tampilan'));
		} else {
			$client_id = "";
			$client_secret = "";
			$tokenUrl = "https://sso.petrokimia-gresik.net/dev/api/User/Login";
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

				redirect(base_url('tampilan'));
			} else {
				$this->session->set_flashdata('pesan', 'Username dan password Salah');

				redirect(base_url('login'));
			}
		}
	}
	/* Login */

	/* Logout */
	public function logout()
	{
		$this->session->sess_destroy();

		redirect(base_url('login'));
	}
	/* Logout */

	// tambahan 
	// fk pegawai bantuan -> pegawai
	public function fk_pegawai()
	{
		$sql_bantuan = $this->db->get('global.global_pegawai_bantuan')->result_array();
		// echo json_encode($sql_bantuan);
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

			redirect(base_url('login/'));
		}
	}
	// tambahan
}
