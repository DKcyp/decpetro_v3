<?php


defined('BASEPATH') or exit('No direct script access allowed');

class Email extends MX_Controller
{

    public function index()
    {
    }

    public function send($data = null)
    {
        $sesi = $this->session->userdata();
        var_dump($sesi);
        // INIT TOKEN 

        $client_id = "";
        $client_secret = "";
        $tokenUrl = "http://mailservices.petrokimia-gresik.com/token";
        // $tokenContent = "grant_type=password&username=" . $username . "&password=" . $password;
        $tokenContent = "grant_type=password&username=mailservices&password=mail@P3trokmi@2019";
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


        if ($item->access_token) {

            // $from       = $this->input->get_post('');
            // $to         = $this->input->get_post('');
            // $subject    = $this->input->get_post('');
            // $body       = $this->input->get_post('');

            $from       = $sesi['email_pegawai'];
            $to         = $data['email_penerima'];
            $subject    = $data['subjek'];
            $body       = $data['content'];


            $client_id = "";
            $client_secret = "";
            $tokenUrl = "http://mailservices.petrokimia-gresik.com/api/Mail/SendData";
            // $tokenContent = "grant_type=password&username=" . $username . "&password=" . $password;
            // $tokenContent = "From=triaji20@gmail.com&To=triaji20@gmail.com&Subject=test&Body=test";
            $tokenContent = "From=" . $from . "&To=" . $to . "&Subject=" . $subject . "&Body=" . $body;
            $authorization = base64_encode("$client_id:$client_secret");
            $tokenHeaders = array(
                "Authorization : Basic {$authorization}",
                "Authorization : Token {$item->access_token}",
                "Content-Type : application/x-www-form-urlencoded"
            );

            print_r($tokenContent);

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
            print_r('sukses');
        } else {
            print_r('token is invalid');
        }
    }
}
