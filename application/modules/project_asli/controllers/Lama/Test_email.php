<?php


defined('BASEPATH') or exit('No direct script access allowed');

class Test_email extends MX_Controller
{

    public function index()
    {
        $this->load->view('project/test_email');
    }
    public function send()
    {
        $this->load->library('mailer');

        $email_penerima = $this->input->get_post('email_penerima');
        $subjek = $this->input->get_post('subjek');
        $pesan = $this->input->get_post('pesan');
        $attachment = $_FILES['attachment'];
        $content = $this->load->view('project/test_email_content', array('pesan' => $pesan), true); // Ambil isi file content.php dan masukan ke variabel $content
        $sendmail = array(
            'email_penerima' => $email_penerima,
            'subjek' => $subjek,
            'content' => $content,
            'attachment' => $attachment
        );
        print_r($sendmail);
        if (empty($attachment['name'])) { // Jika tanpa attachment
            // $this->M_pekerjaan->insertEmail($sendmail);  
            $send = $this->mailer->send($sendmail); // Panggil fungsi send yang ada di librari Mailer
        } else { // Jika dengan attachment
            $send = $this->mailer->send_with_attachment($sendmail); // Panggil fungsi send_with_attachment yang ada di librari Mailer
        }
        echo "<b>" . $send['status'] . "</b><br />";
        echo $send['message'];
        echo "<br /><a href='" . base_url("project/test_email") . "'>Kembali ke Form</a>";
    }
}

/* End of file Test_email.php */
