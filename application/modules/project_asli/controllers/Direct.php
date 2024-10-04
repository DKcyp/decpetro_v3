<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Direct extends MX_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->library('template');
    $CI = &get_instance();
    $sesi = $this->session->userdata();


    $this->load->model('M_pekerjaan');
    $this->load->model('master/M_user');
    $this->load->library('mailer');
    $this->load->library('mailer_api');

  }

  public function downloadDokumen()
  {
    $this->load->library('ciqrcode'); //pemanggilan library QR CODE
    $this->load->library('PdfGenerator');
    $this->load->helper(array('url', 'download'));

    $dokumen = explode('~', $this->input->get_post('pekerjaan_dokumen_file'));
    $format  = explode('.', $dokumen[0]);

    $id_dokumen = preg_replace("/[^0-9^a-z^A-Z]/", "", $dokumen[1]);
    $param['pekerjaan_id'] =  preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get_post('pekerjaan_id'));
    $param_dokumen['pekerjaan_dokumen_id'] = preg_replace("/[^0-9^a-z^A-Z^_.]/", "", $dokumen[0]);

    /* QRCODE */
    $config['cacheable']    = true; //boolean, the default is true
    $config['cachedir']     = './application/cache/'; //string, the default is application/cache/
    $config['errorlog']     = './application/logs/'; //string, the default is application/logs/
    $config['imagedir']     = './document/qrcode/'; //direktori penyimpanan qr code
    $config['quality']      = true; //boolean, the default is true
    $config['size']         = '1024'; //interger, the default is 1024
    $config['black']        = array(224, 255, 255); // array, default is array(255,255,255)
    $config['white']        = array(70, 130, 180); // array, default is array(0,0,0)
    $this->ciqrcode->initialize($config);

    $judul = 'qrcode_' . $format[0];
    // $url = base_url('document/') . $dokumen[0];
    $url = base_url('project/direct/downloadDokumen?pekerjaan_id=') . $this->input->get_post('pekerjaan_id').'&pekerjaan_dokumen_file='.$this->input->get_post('pekerjaan_dokumen_file');

    $image_name = $judul . '.PNG'; //buat name dari qr code sesuai dengan nim
    $params['data'] = $url; //data yang akan di jadikan QR CODE
    $params['level'] = 'H'; //H=High
    $params['size'] = 10;
    $params['savename'] = FCPATH . $config['imagedir'] . $image_name; //simpan image QR CODE ke folder assets/images/
    $this->ciqrcode->generate($params);
    $this->db->query("UPDATE dec.dec_pekerjaan_dokumen SET pekerjaan_dokumen_qrcode = '" . $image_name . "' WHERE pekerjaan_dokumen_id = '" . $dokumen[1] . "'");
    /* QRCODE */

    $data['pekerjaan'] = $this->M_pekerjaan->getPekerjaan($param);
    $data['bagian'] = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN global.global_bagian_detail b ON b.id_pegawai = a.id_create_awal LEFT JOIN global.global_bagian c ON c.bagian_id = b.id_bagian WHERE pekerjaan_dokumen_id = '" . $dokumen[1] . "'  ")->row_array();

    $sql_template = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen a LEFT JOIN dec.dec_pekerjaan_template b ON a.id_pekerjaan_template = b.pekerjaan_template_id WHERE pekerjaan_dokumen_id = '" . $id_dokumen . "'");
    $isi_template = $sql_template->row_array();
    $data['template'] = $isi_template;

    $sql_dokumen = $this->db->query("SELECT pekerjaan_dokumen_file FROM dec.dec_pekerjaan_dokumen WHERE  pekerjaan_dokumen_file !='' AND pekerjaan_dokumen_id = '" . $id_dokumen . "'");
    $data_dokumen = $sql_dokumen->row_array();

    /* foreach ($data_dokumen as $key => $value) {*/
      if ($data_dokumen['pekerjaan_dokumen_file'] != '') {
        dblog('V', $this->input->get_post('pekerjaan_id'), 'Dokumen ' . $isi_template['pekerjaan_template_nama'] . ' - ' . $isi_template['pekerjaan_dokumen_nama'] . ' Telah Didownload');

        $html =    $this->load->view('project/pekerjaan_cover', $data, true);
        $filename = 'cover_' . preg_replace("/[^0-9^a-z^A-Z^_.]/", "", $dokumen[0]);

        if ($isi_template['pekerjaan_dokumen_kertas'] != '' && $isi_template['pekerjaan_dokumen_orientasi'] != '') {
          $this->pdfgenerator->save($html, $filename, $isi_template['pekerjaan_dokumen_kertas'], $isi_template['pekerjaan_dokumen_orientasi']);
        } else {
          $this->pdfgenerator->save($html, $filename, 'A4', 'portrait');
        }

        $data1['cover_download'] = 'cover_' . preg_replace("/[^0-9^a-z^A-Z^_.]/", "", $dokumen[0]);
        $data1['data_download'] = preg_replace("/[^0-9^a-z^A-Z^_.]/", "", $dokumen[0]);
        $data1['qrcode'] = preg_replace("/[^0-9^a-z^A-Z^_.]/", "", $image_name);
        $data1['judul'] = $isi_template['pekerjaan_template_nama'] . ' - ' . $isi_template['pekerjaan_dokumen_nama'] . ' - ' . $isi_template['pekerjaan_dokumen_nomor'];
        $data1['halaman'] = $isi_template['pekerjaan_dokumen_jumlah'];
        $data1['kertas'] = $isi_template['pekerjaan_dokumen_kertas'];
        $data1['orientasi'] = $isi_template['pekerjaan_dokumen_orientasi'];
        $data1['qr_code'] = $isi_template['pekerjaan_dokumen_qrcode'];
        print_r($data1);

        $this->load->view('project/combine', $data1);
        /* }*/
      }
    }

  }

?>
