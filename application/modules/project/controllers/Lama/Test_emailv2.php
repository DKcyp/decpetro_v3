<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Test_emailv2 extends MX_Controller
{

    public function index()
    {
        $data['title'] = 'Kirim Email - Admin Panel';
        // $data['email'] = $this->Settings_model->getEmailAccount();
        // $this->load->view('templates/header_admin', $data);
        $this->load->view('project/test_emailv2', $data);
        // $this->load->view('templates/footer_admin');

    }

    public function send_mail()
    {
        $to = $this->input->post('sendMailTo');
        $subjet = $this->input->post('subject');
        $message = $this->input->post('description');
        $data = [
            'mail_to' => $to,
            'subject' => $subjet,
            'message' => $message
        ];
        $this->db->insert('email_send', $data);

        if ($to == 0) {
            $data = $this->db->get('subscriber');
            foreach ($data->result_array() as $d) {
                $this->load->library('email');
                $config['charset'] = 'utf-8';
                $config['useragent'] = $this->Settings_model->general()["app_name"];
                $config['smtp_crypto'] = $this->Settings_model->general()["crypto_smtp"];
                $config['protocol'] = 'smtp';
                $config['mailtype'] = 'html';
                $config['smtp_host'] = $this->Settings_model->general()["host_mail"];
                $config['smtp_port'] = $this->Settings_model->general()["port_mail"];
                $config['smtp_timeout'] = '5';
                $config['smtp_user'] = $this->Settings_model->general()["account_gmail"];
                $config['smtp_pass'] = $this->Settings_model->general()["pass_gmail"];
                $config['crlf'] = "\r\n";
                $config['newline'] = "\r\n";
                $config['wordwrap'] = TRUE;

                $message .= '<br/><br/><a href="' . base_url() . 'unsubscribe-email?email=' . $d['email'] . '&code=' . $d['code'] . '">Berhenti berlangganan</a>';

                $this->email->initialize($config);
                $this->email->from($this->Settings_model->general()["account_gmail"], $this->Settings_model->general()["app_name"]);
                $this->email->to($d['email']);
                $this->email->subject($subjet);
                $this->email->message(nl2br($message));
                $this->email->send();
            }
        } else {
            $this->load->library('email');
            $config['charset'] = 'utf-8';
            $config['useragent'] = $this->Settings_model->general()["app_name"];
            $config['smtp_crypto'] = $this->Settings_model->general()["crypto_smtp"];
            $config['protocol'] = 'smtp';
            $config['mailtype'] = 'html';
            $config['smtp_host'] = $this->Settings_model->general()["host_mail"];
            $config['smtp_port'] = $this->Settings_model->general()["port_mail"];
            $config['smtp_timeout'] = '5';
            $config['smtp_user'] = $this->Settings_model->general()["account_gmail"];
            $config['smtp_pass'] = $this->Settings_model->general()["pass_gmail"];
            $config['crlf'] = "\r\n";
            $config['newline'] = "\r\n";
            $config['wordwrap'] = TRUE;

            $dataEmail = $this->db->get_where('subscriber', ['id' => $to])->row_array();
            $message .= '<br/><br/><a href="' . base_url() . 'unsubscribe-email?email=' . $dataEmail['email'] . '&code=' . $dataEmail['code'] . '">Berhenti berlangganan</a>';
            $this->email->initialize($config);
            $this->email->from($this->Settings_model->general()["account_gmail"], $this->Settings_model->general()["app_name"]);
            $this->email->to($dataEmail['email']);
            $this->email->subject($subjet);
            $this->email->message(nl2br($message));
            $this->email->send();
        }
        $this->session->set_flashdata('upload', "<script>
                swal({
                text: 'Email berhasil dikirim',
                icon: 'success'
                });
            </script>");
        redirect(base_url() . 'administrator/email');
    }
}

/* End of file Controllername.php */
