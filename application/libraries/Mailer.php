<?php defined('BASEPATH') or exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    protected $_ci;
    // protected $session = $_ci->session->userdata();
    // protected $email_pengirim = 'triaji20@gmail.com'; // Isikan dengan email pengirim
    // protected $nama_pengirim = 'aji'; // Isikan dengan nama pengirim
    // protected $password = 'azhc hejn jnfo ajqt'; // Isikan dengan password email pengirim

    public function __construct()
    {
        $this->_ci = &get_instance(); // Set variabel _ci dengan Fungsi2-fungsi dari Codeigniter

        require_once(APPPATH . 'third_party/phpmailer/Exception.php');
        require_once(APPPATH . 'third_party/phpmailer/PHPMailer.php');
        require_once(APPPATH . 'third_party/phpmailer/SMTP.php');
        $session = $this->_ci->session->userdata();
    }

    public function send($data)
    {

        $session = $this->_ci->session->userdata();
        // print_r($session);
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
        // $mail->AddEmbeddedImage('assets/gambar/pg_logo_header.png', 'logo_mynotescode', 'logo.png'); // Aktifkan jika ingin menampilkan gambar dalam email


        $send = $mail->send();

        if ($send) { // Jika Email berhasil dikirim
            $response = array('status' => 'Sukses', 'message' => 'Email berhasil dikirim');
        } else { // Jika Email Gagal dikirim
            $response = array('status' => 'Gagal', 'message' => 'Email gagal dikirim');
        }

        return $response;
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
