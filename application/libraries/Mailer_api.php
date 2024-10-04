<?php defined('BASEPATH') or exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer_api
{
    protected $_ci;
    public function __construct()
    {
        $this->_ci = &get_instance(); // Set variabel _ci dengan Fungsi2-fungsi dari Codeigniter

        require_once(APPPATH . 'third_party/phpmailer/Exception.php');
        require_once(APPPATH . 'third_party/phpmailer/PHPMailer.php');
        require_once(APPPATH . 'third_party/phpmailer/SMTP.php');
        $session = $this->_ci->session->userdata();
    }

    public function send_email($data)
    {

        $session = $this->_ci->session->userdata();
        // print_r($session);
        $mail = new PHPMailer;
        $mail->isSMTP();

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

        if ($item) {

            $from       = $session['email_pegawai'] . ", " . $session['pegawai_nama'];
            $to         = $data['email_penerima'];
            $subject    = $data['subjek'];
            $body       = $data['content'];


            $client_id = "";
            $client_secret = "";
            $tokenUrl = "http://mailservices.petrokimia-gresik.com/api/Mail/SendData";
            $tokenContent = "From=" . $from . "&To=" . $to . "&Subject=" . $subject . "&Body=" . $body;
            $authorization = base64_encode("$client_id:$client_secret");
            $tokenHeaders = array(
                // "Authorization : Basic {$authorization}",
                "Authorization : Bearer " . $item->access_token,
                "Content-Type : application/x-www-form-urlencoded"
            );


            $token = curl_init($tokenUrl);
            curl_setopt($token, CURLOPT_URL, $tokenUrl);
            curl_setopt($token, CURLOPT_HTTPHEADER, $tokenHeaders);
            curl_setopt($token, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($token, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($token, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($token, CURLOPT_POST, true);
            curl_setopt($token, CURLOPT_POSTFIELDS, $tokenContent);
            $item = curl_exec($token);
            curl_close($token);
            $item = json_decode($item);

            print_r('sukses kirim email');
        } else {
            print_r('token is invalid');
        }


        // $mail->Host = 'smtp.gmail.com';
        // $mail->Username = $session['email_pegawai']; // Email Pengirim
        // $mail->Password = $session['password_aplikasi_pegawai']; // Isikan dengan Password email pengirim
        // $mail->Port = 465;
        // $mail->SMTPAuth = true;
        // $mail->SMTPSecure = 'ssl';
        // // $mail->SMTPDebug = 2; // Aktifkan untuk melakukan debugging

        // $mail->setFrom($session['email_pegawai'], $session['pegawai_nama']);
        // $mail->addAddress($data['email_penerima'], '');
        // $mail->isHTML(true); // Aktifkan jika isi emailnya berupa html

        // $mail->Subject = $data['subjek'];
        // $mail->Body = $data['content'];
        // // $mail->AddEmbeddedImage('assets/gambar/pg_logo_header.png', 'logo_mynotescode', 'logo.png'); // Aktifkan jika ingin menampilkan gambar dalam email


        // $send = $mail->send();

        // if ($send) { // Jika Email berhasil dikirim
        //     $response = array('status' => 'Sukses', 'message' => 'Email berhasil dikirim');
        // } else { // Jika Email Gagal dikirim
        //     $response = array('status' => 'Gagal', 'message' => 'Email gagal dikirim');
        // }

        // return $response;
    }

    public function send_with_attachment($data)
    {
        $session = $this->_ci->session->userdata();
        $mail = new PHPMailer;
        $mail->isSMTP();

        $mail->Host = 'smtp.gmail.com';
        $mail->Username = $session['email_pegawai']; // Email Pengirim
        $mail->Password = $session['password_aplikasi_pegawai']; // Isikan dengan Password email pengirim
        $mail->Port = 465;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        // $mail->SMTPDebug = 2; // Aktifkan untuk melakukan debugging

        $mail->setFrom($session['email_pegawai'], $session['pegawai_nama']);
        $mail->addAddress($data['email_penerima'], '');
        $mail->isHTML(true); // Aktifkan jika isi emailnya berupa html

        $mail->Subject = $data['subjek'];
        $mail->Body = $data['content'];
        // $mail->AddEmbeddedImage('image/logo.png', 'logo_mynotescode', 'logo.png'); // Aktifkan jika ingin menampilkan gambar dalam email

        if ($data['attachment']['size'] <= 25000000) { // Jika ukuran file <= 25 MB (25.000.000 bytes)
            $mail->addAttachment($data['attachment']['tmp_name'], $data['attachment']['name']);

            $send = $mail->send();

            if ($send) { // Jika Email berhasil dikirim
                $response = array('status' => 'Sukses', 'message' => 'Email berhasil dikirim');
            } else { // Jika Email Gagal dikirim
                $response = array('status' => 'Gagal', 'message' => 'Email gagal dikirim');
            }
        } else { // Jika Ukuran file lebih dari 25 MB
            $response = array('status' => 'Gagal', 'message' => 'Ukuran file attachment maksimal 25 MB');
        }

        return $response;
    }
}
