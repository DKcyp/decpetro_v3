<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Task extends MX_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->library('template');
    $this->load->model('M_task');
    $sesi = $this->session->userdata();
    if (empty($sesi['pegawai_nik'])) {
      redirect('tampilan');
    }
  }

  public function index()
  {
    $data = array();
    $this->template->template_master('task/task', $data);
  }

  /*GET*/
public function getTask() {
    // Initialize variables and retrieve session data
    $session = $this->session->userdata();
    
    // Retrieve parameters
    $param['user_id'] = $this->input->get('id_user_cari') ? $this->input->get('id_user_cari') : $session['pegawai_nik'];
    $param['tahun'] = $this->input->post('tahun');
    
    // Fetch tasks from the model
    $datas = $this->M_task->getTask($param);
    $data = [];
    $condition = []; // Track processing status for each task
    
    // Process each task
    foreach ($datas as $value) {
        $isi = []; // Initialize temporary storage for task attributes
        
        // Copy task attributes
        foreach ($value as $key => $val) {
            $isi[$key] = $val;
        }
        
        // Check processing status
        $this->db->select('*');
        $this->db->from('dec.dec_pekerjaan_disposisi');
        $this->db->where('id_pekerjaan', $value['pekerjaan_id']);
        $this->db->where('id_user', $value['user_id']);
        $this->db->where('pekerjaan_disposisi_status', $value['status']);
        $this->db->where('is_proses IS NOT NULL', null, false); // Correct usage of `IS NOT NULL`
        $sql_perencana = $this->db->get()->row_array();

        // Determine 'is_proses' status
        $isi['is_proses'] = ($sql_perencana) ? 'n' : 'y';
        
        if (isset($condition[$value['pekerjaan_id']]) && $condition[$value['pekerjaan_id']] == 'y') {
            $isi['is_proses'] = 'n';
        } else {
            $condition[$value['pekerjaan_id']] = $isi['is_proses'];
        }
        
        // Append processed task to the result array
        array_push($data, $isi);
    }

    // Output the result as JSON
    echo json_encode($data);
}

 public function getTaskTotal(){
  $session = $this->session->userdata();
  $param['user_id'] = $session['pegawai_nik'];
  $param['tahun'] = $this->input->post('tahun');
  $data = $this->M_task->getTaskTotal($param);
  echo json_encode(count($data));
}
/*GET*/

/*Update Task*/
public function updateTask(){
  $id = $this->input->get('id');
  $data = array(
    'user_action' => 'y',
  );
  $this->db->where('task_id', $id);
  $this->db->update('global.global_tasklog', $data);
}
/*Update Task*/
}