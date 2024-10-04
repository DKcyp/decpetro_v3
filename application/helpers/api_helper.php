<?php
function api($data)
{
	$url = "http://localhost/api/" . $data;

	return $url;
}

function create_id()
{
	/* ID OTOMATIS */
	$r = rand();
	$u = uniqid(getmypid() . $r . (float)microtime() * 1000000, true);
	$id = sha1(session_id() . $u);

	return $id;
}

function dblog($tipe, $id_pekerjaan = null, $text = null, $id_user = null)
{
	$CI = &get_instance();
	if ($id_user != null) {
		$sql_user = $CI->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $id_user . "'");
		$isi = $sql_user->row_array();
	} else {
		$isi = $CI->session->userdata();
	}

	$data['log_id'] = create_id();
	$data['log_data'] = addslashes($CI->db->last_query());
	$data['log_tipe'] = strtoupper($tipe);
	$data['log_ip'] = ($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'UNKNOWN IP';
	$data['pekerjaan_id'] = $id_pekerjaan;
	$data['text'] = $text;
	$data['log_when'] = date('Y-m-d H:i:s');
	$data['log_who'] = $isi['pegawai_nama'];

	$CI->db->insert('global.global_dblog', $data);

	$CI->db->affected_rows();
}

function tasklog($pekerjaan_id = null, $status = null, $user_id=null, $text = null, $action = null){
	$CI = &get_instance();
	if ($user_id != null) {
		$user = $CI->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_nik = '" . $user_id . "'")->row_array();
	} else {
		$user = $CI->session->userdata();
	}

	$data['task_id'] = create_id();
	$data['id_pekerjaan'] = $pekerjaan_id;
	$data['status'] = $status;
	$data['user_ip'] = ($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'UNKNOWN IP';
	$data['user_id'] = $user['pegawai_nik'];
	$data['task_name'] = $text; 
	$data['task_date'] = date('Y-m-d H:i:s');
	$data['user_action'] = $action;

	$CI->db->insert('global.global_tasklog',$data);
	$CI->db->affected_rows();
}

function loginlog($username, $password, $status)
{
	$CI = &get_instance();

	$user = $CI->db->query("SELECT pegawai_nama FROM global.global_pegawai WHERE pegawai_nik = '" . $username . "'")->row_array();

	$data['login_log_id'] = create_id();
	$data['login_log_data'] = ($CI->db->last_query());
	$data['login_log_ip'] =  ($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '-';
	$data['login_log_username'] = $username;
	$data['login_log_password'] = $password;
	$data['login_log_time'] = date('Y-m-d H:i:s');
	$data['login_log_status'] = $status;
	$data['login_log_who'] = $user['pegawai_nama'];

	$CI->db->insert('global.global_login_log', $data);

	$CI->db->affected_rows();
}

function sendWA($pekerjaan_id, $tujuan = null, $tujuan_nama = null, $text = null)
{
	$CI = &get_instance();
	$sql = $CI->db->query("SELECT * FROM dec.dec_pekerjaan WHERE pekerjaan_id = '" . $pekerjaan_id . "'");
	$data = $sql->row_array();

	$tujuan = ($tujuan != null) ? $tujuan : '-';
	$tujuan_nama = ($tujuan_nama != null) ? $tujuan_nama : '-';
	$text = ($text != null) ? $text : '-';
	$no = ($data['pekerjaan_nomor'] != null) ? $data['pekerjaan_nomor'] : '-';
	$nama = ($data['pekerjaan_judul'] != null) ? $data['pekerjaan_judul'] : '-';
	$waktu = ($data['pekerjaan_waktu'] != null) ? date("d-m-Y", strtotime($data['pekerjaan_waktu'])) : '-';

	$pekerjaan_id = $data['pekerjaan_id'];
	$status = $data['pekerjaan_status'];
	$rkap = ($data['id_klasifikasi_pekerjaan'] == '1') ? '1' : '0';
	$id_user = $tujuan;

	if ($data['pekerjaan_status'] >= '0' && $data['pekerjaan_status'] <= '4') {
		$aksi = 'usulan';
	} else if ($data['pekerjaan_status'] >= '5' && $data['pekerjaan_status'] <= '7') {
		$aksi = "berjalan";
	} else if ($data['pekerjaan_status'] >= '8' && $data['pekerjaan_status'] <= '10') {
		$aksi = "ifa";
	} else if ($data['pekerjaan_status'] >= '11' && $data['pekerjaan_status'] <= '13') {
		$aksi = "ifc";
	} else if ($data['pekerjaan_status'] >= '14' && $data['pekerjaan_status'] <= '16') {
		$aksi = "selesai";
	} else {
		$aksi = "usulan";
	}
	$target = "6285600554235";

	$tujuan = explode('-', $target);

	$link = (base_url() . "login?aksi=" . $aksi . "&pekerjaan_id=" . $pekerjaan_id . "&status=" . $status . "&rkap=" . $rkap . "&id_user=" . $id_user);
	$text = '{
		"schema": "NUMBER",
		"receiver": '.$tujuan[0].',
		"message": {
			"text": "DEC Notification : \n\n' . $tujuan_nama . '\n' . $text . ' \nNomor Pekerjaan : ' . $no . '\nNama Pekerjaan : ' . $nama . '\nTanggal : ' . $waktu . '\n\n",
			"footer": "DEC",
			"viewOnce": true,
			"templateButtons": [
			{
				"index": 1,
				"urlButton": {
					"displayText": "buka pekerjaan disini!",
					"url": "' . $link . '"
				}
			}
			]
		}
	}';

	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://34.101.96.239/api/message/text',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => $text,
		CURLOPT_HTTPHEADER => array(
			'X-APP-KEY:ecef2-66439-474cc-b69dc-59dd3',
			'X-APP-TOKEN:ab5f3-e94c8-4cc7c-ac53c-4c968',
			'Content-Type: application/json',
			'User-Agent: PostmanRuntime/7.31.3',
		),
	));

	$response = curl_exec($curl);

	curl_close($curl);

	print_r($response);
}

function sendNotif($pekerjaan_id, $dari = null, $ke = null, $text = null)
{
	$CI = &get_instance();
	$sql = $CI->db->query("SELECT * FROM dec.dec_pekerjaan WHERE pekerjaan_id = '" . $pekerjaan_id . "'");
	$data = $sql->row_array();

	$dari = ($dari != null) ? $dari : '-';
	$ke = ($ke != null) ? $ke : '-';
	$judul = $data['pekerjaan_judul'];
	$nomor = ($data['pekerjaan_nomor'] != null) ? '(' . $data['pekerjaan_nomor'] . ')' : '';
	$isi = $data['pekerjaan_deskripsi'];
	// $link = ($link!=null) ? $link : '-';
	$pekerjaan_id = $data['pekerjaan_id'];
	$status = $data['pekerjaan_status'];
	$rkap = ($data['id_klasifikasi_pekerjaan'] == '1') ? '1' : '0';
	$id_user = $ke;

	if ($data['pekerjaan_status'] >= '0' && $data['pekerjaan_status'] <= '4') {
		$aksi = 'usulan';
	} else if ($data['pekerjaan_status'] >= '5' && $data['pekerjaan_status'] <= '7') {
		$aksi = "berjalan";
	} else if ($data['pekerjaan_status'] >= '8' && $data['pekerjaan_status'] <= '10') {
		$aksi = "ifa";
	} else if ($data['pekerjaan_status'] >= '11' && $data['pekerjaan_status'] <= '13') {
		$aksi = "ifc";
	} else if ($data['pekerjaan_status'] >= '14' && $data['pekerjaan_status'] <= '16') {
		$aksi = "selesai";
	} else {
		$aksi = "usulan";
	}

	$link = urlencode(base_url() . "login?aksi=" . $aksi . "&pekerjaan_id=" . $pekerjaan_id . "&status=" . $status . "&rkap=" . $rkap . "&id_user=" . $id_user);

	// inisialiasi untuk dapat tokennya
	$client_id = "";
	$client_secret = "";
	$tokenUrl = "https://newdevdof.petrokimia-gresik.com/api/v2/Account/Login";
	$tokenContent = "grant_type=password&username=dec_apps&password=dec12345";
	$authorization = base64_encode("$client_id:$client_secret");
	$tokenHeaders = array("Authorization: Basic {$authorization}", "Content-Type: application/x-www-form-urlencoded", "User-Agent:PostmanRuntime/7.30.0",);

	$token = curl_init();
	curl_setopt($token, CURLOPT_URL, $tokenUrl);
	curl_setopt($token, CURLOPT_HTTPHEADER, $tokenHeaders);
	curl_setopt($token, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($token, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($token, CURLOPT_POST, true);
	curl_setopt($token, CURLOPT_POSTFIELDS, $tokenContent);
	$item = curl_exec($token);
	curl_close($token);
	$item = (array)json_decode($item);
	// kirim datanya ke dof

	$tujuan = explode('-',$ke);
	$asal = explode('-',$dari);

	$dof_tokenBearer = $item['access_token'];
	$dof_tokenUrl = "https://newdevdof.petrokimia-gresik.com/api/WebNotification/SendNotificationV2";
	$dof_tokenContentArray = array(
		"From" => $asal[0],
		"To" => $tujuan[0],
		"Title" => $judul . $nomor,
		"Body" => $text,
		"Url" => $link,
	);


	$dof_tokenContent = urldecode(http_build_query($dof_tokenContentArray));

	$dof_tokenHeaders = array(
		"User-Agent:PostmanRuntime/7.30.0",
		"Authorization:  Bearer " . $dof_tokenBearer,
		"Content-Type: application/x-www-form-urlencoded",
		"apiKey:DEAFF0D2-CC32-46E8-8107-8276A5A2B214",
		"systemId:DEC",
	);

	$dof_token = curl_init();
	curl_setopt($dof_token, CURLOPT_URL, $dof_tokenUrl);
	curl_setopt($dof_token, CURLOPT_HTTPHEADER, $dof_tokenHeaders);
	curl_setopt($dof_token, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($dof_token, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($dof_token, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($dof_token, CURLOPT_MAXREDIRS, 10);
	curl_setopt($dof_token, CURLOPT_TIMEOUT, 0);
	curl_setopt($dof_token, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	curl_setopt($dof_token, CURLOPT_POST, true);
	curl_setopt($dof_token, CURLOPT_POSTFIELDS, $dof_tokenContent);

	$dof_item = curl_exec($dof_token);
	curl_close($dof_token);
	$dof_item = (array)json_decode($dof_item);
	$object = json_decode(json_encode($dof_item), FALSE);
	print_r($object);
}

function readNotif($url = null, $to = null)
{

	$url = ($url != null) ? $url : '';
	$to  = ($to != null) ? $to : '';

	// inisialiasi untuk dapat tokennya
	$client_id = "";
	$client_secret = "";
	$tokenUrl = "https://newdevdof.petrokimia-gresik.com/api/v2/Account/Login";
	$tokenContent = "grant_type=password&username=dec_apps&password=dec12345";
	$authorization = base64_encode("$client_id:$client_secret");
	$tokenHeaders = array("Authorization: Basic {$authorization}", "Content-Type: application/x-www-form-urlencoded", "User-Agent:PostmanRuntime/7.30.0",);

	$token = curl_init();
	curl_setopt($token, CURLOPT_URL, $tokenUrl);
	curl_setopt($token, CURLOPT_HTTPHEADER, $tokenHeaders);
	curl_setopt($token, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($token, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($token, CURLOPT_POST, true);
	curl_setopt($token, CURLOPT_POSTFIELDS, $tokenContent);
	$item = curl_exec($token);
	curl_close($token);
	$item = (array)json_decode($item);

	$tujuan = explode('-',$ke);

	$dof_tokenBearer = $item['access_token'];
	$dof_tokenUrl = "https://newdevdof.petrokimia-gresik.com/api/WebNotification/SetNotificationToReadV2";
	$dof_tokenContentArray = array(
		"Url" => urlencode($url),
		"IsRead" => true,
		"To" => $tujuan[0],
	);

	$dof_tokenContent = urldecode(http_build_query($dof_tokenContentArray));

	$dof_tokenHeaders = array(
		"User-Agent:PostmanRuntime/7.30.0",
		"Authorization:  Bearer " . $dof_tokenBearer,
		"Content-Type: application/x-www-form-urlencoded",
		"apiKey:DEAFF0D2-CC32-46E8-8107-8276A5A2B214",
		"systemId:DEC",
	);

	$dof_token = curl_init();
	curl_setopt($dof_token, CURLOPT_URL, $dof_tokenUrl);
	curl_setopt($dof_token, CURLOPT_HTTPHEADER, $dof_tokenHeaders);
	curl_setopt($dof_token, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($dof_token, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($dof_token, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($dof_token, CURLOPT_MAXREDIRS, 10);
	curl_setopt($dof_token, CURLOPT_TIMEOUT, 0);
	curl_setopt($dof_token, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	curl_setopt($dof_token, CURLOPT_POST, true);
	curl_setopt($dof_token, CURLOPT_POSTFIELDS, $dof_tokenContent);

	$dof_item = curl_exec($dof_token);
	curl_close($dof_token);
	$dof_item = (array)json_decode($dof_item);
	// $object = json_decode(json_encode($dof_item), FALSE);
	// echo json_encode($dof_item);

}

function anti_inject($kata)
{
	$filter = addslashes(stripslashes(stripcslashes(strip_tags(htmlspecialchars($kata, ENT_QUOTES)))));
	return $filter;
}

function anti_inject_js($kata)
{
	$filter = htmlentities(addslashes(stripslashes(stripcslashes(strip_tags(htmlspecialchars($kata, ENT_QUOTES))))));
	return $filter;
}
