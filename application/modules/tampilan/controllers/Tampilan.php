<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tampilan extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$sesi = $this->session->userdata();
		// if (empty($sesi['pegawai_nik'])) {
		// 	redirect('tampilan');
		// }
		$this->load->model('tampilan/M_tampilan');
		$this->load->model('master/M_role');

		if (empty($sesi['pegawai_nik'])) {
			redirect(base_url('login/fk_pegawai'));
		}
	}

	/* INDEX */
	public function index()
	{
		$data = $this->session->userdata();

		$this->load->view('tampilan/header', $data);
		$this->load->view('tampilan/sidebar');
		$this->load->view('tampilan/konten');
		$this->load->view('tampilan/footer');
	}
	/* INDEX */

	/* FK */
	public function fk_role()
	{
		$this->M_tampilan->deleteRole();
		foreach ($this->M_tampilan->dataUser() as $value) {
			$data['rol_id'] = $value['pegawai_poscode'];
			$data['rol_name'] = $value['pegawai_postitle'];

			$sql_isi = $this->db->query("SELECT count(*)  as total FROM global.global_auth_role WHERE rol_id = '".$value['pegawai_poscode']."'");
			$data_isi = $sql_isi->row_array();

			if ($data_isi['total'] == 0) $this->M_role->insertRole($data);
		}
	}

	public function fk_menu_role()
	{
		foreach ($this->M_role->getRole() as $value) {

			$this->M_role->deleteMenuRole($value['rol_id']);

			foreach ($this->M_role->getMenu() as $val) {
				$data['menu_role_id'] = create_id();
				$data['id_menu'] = $val['menu_id'];
				$data['id_role'] = $value['rol_id'];

				$this->M_role->insertMenuRole($data);
			}
		}
	}

	public function fk_menu_role_baru()
	{
		$sql = $this->db->query("SELECT * FROM global.global_pegawai ORDER BY pegawai_id_dep ASC, pegawai_nama ASC");
		$data = $sql->result_array();
		foreach ($data as $value) {
			if ($value['pegawai_id_dep'] == 'E53000') {
				if ($value['pegawai_poscode'] != 'E53600060B') $sql = $this->db->query("DELETE FROM global.global_menu_role WHERE id_role = '" . $value['pegawai_poscode'] . "' AND id_menu >= '10' AND id_menu <= '18'");
			} else {
				$sql = $this->db->query("DELETE FROM global.global_menu_role WHERE id_role = '" . $value['pegawai_poscode'] . "' AND id_menu != '01' AND id_menu != '02'  AND id_menu != '03'  AND id_menu != '04' AND id_menu != '06' AND id_menu != '07'");
			}
		}
	}
	/* FK */


	/* notif */



	// notif baru
	public function notif_baru()
	{
		$user = $this->session->userdata();

		if ($this->input->get_post('is_rkap') == 'y') {
			$param['rkap'] = '1';
		} else {
			$param['non_rkap'] = '1';
		}
		if (!empty($this->input->get_post('status'))) {
			$split = explode(',', $this->input->get_post('status'));
			$param['pekerjaan_status'] = $split;
		}

		$param['user_id'] = $user['pegawai_nik'];

		$sql_khusus = $this->db->query("SELECT count(*)  as total FROM dec.dec_pekerjaan_disposisi a LEFT JOIN dec.dec_pekerjaan b ON a.id_pekerjaan = b.pekerjaan_id 	WHERE pekerjaan_status = '5' AND pekerjaan_disposisi_status = '6' AND is_aktif = 'y' AND a.id_user = '" . $user['pegawai_nik'] . "' ");
		$data_khusus = $sql_khusus->row_array();

		$data = array();

		if (!empty($this->input->get_post('status')) && ($param['pekerjaan_status'][0] == '-' || $param['pekerjaan_status'][0] == '0')) {
			$data = $this->M_tampilan->getNotifBaruReject($param);
		} else if (!empty($this->input->get_post('status')) && ($param['pekerjaan_status'][0] == '1' || $param['pekerjaan_status'][0] == '2' || $param['pekerjaan_status'][0] == '3' || $param['pekerjaan_status'][0] == '4')){
			$data = $this->M_tampilan->getNotifBaruPIC($param);
		} else if (!empty($this->input->get_post('status')) && ($param['pekerjaan_status'][0] != '-' && $param['pekerjaan_status'][0] != '0' || $param['pekerjaan_status'][0] != '1' || $param['pekerjaan_status'][0] != '2' || $param['pekerjaan_status'][0] != '3' || $param['pekerjaan_status'][0] != '4')) {
			$data = $this->M_tampilan->getNotifBaru($param);

		} else if (empty($this->input->get_post('status'))) {
			$data = $this->M_tampilan->getNotifBaru($param);
		}		
		// echo $this->db->last_query();
		echo json_encode($data);
	}
	// notif baru

	public function reset_pekerjaan()
	{
		$data = array();

		if($this->input->get_post('pekerjaan_status')){
			$data['pekerjaan'] = $this->db->query("SELECT * FROM dec.dec_pekerjaan WHERE pekerjaan_status = '".$this->input->get_post('pekerjaan_status')."' ORDER BY pekerjaan_nomor ASC")->result_array();
		}

		if($this->input->get_post('pekerjaan_id')){
			$data['disposisi'] = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON b.pegawai_nik = a.id_user WHERE id_pekerjaan = '".$this->input->get_post('pekerjaan_id')."' ORDER BY CAST(pekerjaan_disposisi_status as INT) ASC")->result_array();
		}

		$this->load->view('tampilan/reset_pekerjaan', $data, FALSE);
	}

	public function get_notif()
	{
		$user = $this->session->userdata();

		$param['user_id'] = $user['pegawai_nik'];
		$param['rkap'] = $this->input->get_post('is_rkap');

		$data = [];

		$notif_reject = $this->db->query("SELECT pekerjaan_id FROM dec.dec_pekerjaan a LEFT JOIN global.global_klasifikasi_pekerjaan b ON b.klasifikasi_pekerjaan_id = a.id_klasifikasi_pekerjaan WHERE b.klasifikasi_pekerjaan_rkap = '".$param['rkap']."' AND a.pic = '".$param['user_id']."' AND pekerjaan_status IN('-','0')")->num_rows();
		$notif_usulan = $this->db->query("SELECT distinct(pekerjaan_id) FROM dec.dec_pekerjaan a LEFT JOIN global.global_klasifikasi_pekerjaan b ON b.klasifikasi_pekerjaan_id = a.id_klasifikasi_pekerjaan LEFT JOIN global.global_auth_user c ON c.usr_id = a.pic LEFT JOIN global.global_pegawai d ON d.pegawai_nik = c.usr_id LEFT JOIN dec.dec_pekerjaan_disposisi e ON e.id_pekerjaan = a.pekerjaan_id AND e.pekerjaan_disposisi_status = a.pekerjaan_status WHERE b.klasifikasi_pekerjaan_rkap = '".$param['rkap']."' AND a.pic = '".$param['user_id']."' AND pekerjaan_status IN('1','2','3','4')")->num_rows();
		$notif_berjalan = $this->db->query("SELECT distinct(pekerjaan_id) FROM dec.dec_pekerjaan a LEFT JOIN global.global_klasifikasi_pekerjaan b ON b.klasifikasi_pekerjaan_id = a.id_klasifikasi_pekerjaan LEFT JOIN global.global_auth_user c ON c.usr_id = a.pic LEFT JOIN global.global_pegawai d ON d.pegawai_nik = c.usr_id LEFT JOIN dec.dec_pekerjaan_disposisi e ON e.id_pekerjaan = a.pekerjaan_id AND e.pekerjaan_disposisi_status = a.pekerjaan_status WHERE b.klasifikasi_pekerjaan_rkap = '".$param['rkap']."' AND e.id_user = '".$param['user_id']."' AND pekerjaan_status IN('5','6','7')")->num_rows();
		$notif_ifi = $this->db->query("SELECT distinct(pekerjaan_id) FROM dec.dec_pekerjaan a LEFT JOIN global.global_klasifikasi_pekerjaan b ON b.klasifikasi_pekerjaan_id = a.id_klasifikasi_pekerjaan LEFT JOIN global.global_auth_user c ON c.usr_id = a.pic LEFT JOIN global.global_pegawai d ON d.pegawai_nik = c.usr_id LEFT JOIN dec.dec_pekerjaan_disposisi e ON e.id_pekerjaan = a.pekerjaan_id AND e.pekerjaan_disposisi_status = a.pekerjaan_status WHERE b.klasifikasi_pekerjaan_rkap = '".$param['rkap']."' AND e.id_user = '".$param['user_id']."' AND pekerjaan_status IN('8','9','10')")->num_rows();
		$notif_ifa = $this->db->query("SELECT distinct(pekerjaan_id) FROM dec.dec_pekerjaan a LEFT JOIN global.global_klasifikasi_pekerjaan b ON b.klasifikasi_pekerjaan_id = a.id_klasifikasi_pekerjaan LEFT JOIN global.global_auth_user c ON c.usr_id = a.pic LEFT JOIN global.global_pegawai d ON d.pegawai_nik = c.usr_id LEFT JOIN dec.dec_pekerjaan_disposisi e ON e.id_pekerjaan = a.pekerjaan_id AND e.pekerjaan_disposisi_status = a.pekerjaan_status WHERE b.klasifikasi_pekerjaan_rkap = '".$param['rkap']."' AND e.id_user = '".$param['user_id']."' AND pekerjaan_status IN('8','9','10')")->num_rows();
		$notif_ifc = $this->db->query("SELECT distinct(pekerjaan_id) FROM dec.dec_pekerjaan a LEFT JOIN global.global_klasifikasi_pekerjaan b ON b.klasifikasi_pekerjaan_id = a.id_klasifikasi_pekerjaan LEFT JOIN global.global_auth_user c ON c.usr_id = a.pic LEFT JOIN global.global_pegawai d ON d.pegawai_nik = c.usr_id LEFT JOIN dec.dec_pekerjaan_disposisi e ON e.id_pekerjaan = a.pekerjaan_id AND e.pekerjaan_disposisi_status = a.pekerjaan_status WHERE b.klasifikasi_pekerjaan_rkap = '".$param['rkap']."' AND e.id_user = '".$param['user_id']."' AND pekerjaan_status IN('11','12','13')")->num_rows();
		$notif_ift = $this->db->query("SELECT distinct(pekerjaan_id) FROM dec.dec_pekerjaan a LEFT JOIN global.global_klasifikasi_pekerjaan b ON b.klasifikasi_pekerjaan_id = a.id_klasifikasi_pekerjaan LEFT JOIN global.global_auth_user c ON c.usr_id = a.pic LEFT JOIN global.global_pegawai d ON d.pegawai_nik = c.usr_id LEFT JOIN dec.dec_pekerjaan_disposisi e ON e.id_pekerjaan = a.pekerjaan_id AND e.pekerjaan_disposisi_status = a.pekerjaan_status WHERE b.klasifikasi_pekerjaan_rkap = '".$param['rkap']."' AND e.id_user = '".$param['user_id']."' AND pekerjaan_status IN('11','12','13')")->num_rows();
		$notif_ifr = $this->db->query("SELECT distinct(pekerjaan_id) FROM dec.dec_pekerjaan a LEFT JOIN global.global_klasifikasi_pekerjaan b ON b.klasifikasi_pekerjaan_id = a.id_klasifikasi_pekerjaan LEFT JOIN global.global_auth_user c ON c.usr_id = a.pic LEFT JOIN global.global_pegawai d ON d.pegawai_nik = c.usr_id LEFT JOIN dec.dec_pekerjaan_disposisi e ON e.id_pekerjaan = a.pekerjaan_id AND e.pekerjaan_disposisi_status = a.pekerjaan_status WHERE b.klasifikasi_pekerjaan_rkap = '".$param['rkap']."' AND e.id_user = '".$param['user_id']."' AND pekerjaan_status IN('11','12','13')")->num_rows();
		$notif_selesai = $this->db->query("SELECT distinct(pekerjaan_id) FROM dec.dec_pekerjaan a LEFT JOIN global.global_klasifikasi_pekerjaan b ON b.klasifikasi_pekerjaan_id = a.id_klasifikasi_pekerjaan LEFT JOIN global.global_auth_user c ON c.usr_id = a.pic LEFT JOIN global.global_pegawai d ON d.pegawai_nik = c.usr_id LEFT JOIN dec.dec_pekerjaan_disposisi e ON e.id_pekerjaan = a.pekerjaan_id AND e.pekerjaan_disposisi_status = a.pekerjaan_status WHERE b.klasifikasi_pekerjaan_rkap = '".$param['rkap']."' AND e.id_user = '".$param['user_id']."' AND pekerjaan_status IN('14','15')")->num_rows();

		$data['notif_reject'] = $notif_reject;
		$data['notif_usulan'] = $notif_usulan;
		$data['notif_berjalan'] = $notif_berjalan;
		$data['notif_ifi'] = $notif_ifi;
		$data['notif_ifa'] = $notif_ifa;
		$data['notif_ifc'] = $notif_ifc;
		$data['notif_ift'] = $notif_ift;
		$data['notif_ifr'] = $notif_ifr;
		$data['notif_selesai'] = $notif_selesai;

		echo json_encode($data);

	}	

}
