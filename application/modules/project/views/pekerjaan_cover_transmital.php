<?php
require('./assets_tambahan/fpdf/fpdf.php');
require('./assets_tambahan/fpdi_default/src/autoload.php');
require('./assets_tambahan/fpdi/src/autoload.php');

use \setasign\Fpdi\Fpdi;

$pdf = new FPDI();
$halaman = $pdf->setSourceFile('document/' . $dokumen['pekerjaan_dokumen_file']);
$data_dokumen = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen_transmital WHERE pekerjaan_dokumen_id = '" . $dokumen['pekerjaan_dokumen_id'] . "'")->result_array();
?>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Cover</title>
  <style type="text/css">
    @page {
      size: A4 Portrait;
      margin: 0.5cm 0cm 0cm 0cm;
    }

    @media print {
      @page {
        margin: 0cm;
      }

      body {
        margin: 1cm;
      }
    }

    .isiText {
      font-family: Arial;
      font-size: 15pt;
      font-weight: bold;
    }

    .table {
      border: 1px solid black;
      border-collapse: collapse;
    }

    #qrcode {
      position: relative;
    }

    #qrcode img {
      position: absolute;
      top: 0px;
      right: 30px;
      width: 70px;
    }
  </style>

  <div id="qrcode">

  </div>

</head>

<body>
  <table border="0" width="100%" cellspacing="0" cellpadding="0" style="padding: 0 5 0 ;">
    <tr>
      <td>
        <table class="table" width="100%" border="1" cellspacing="0" cellpadding="0">
          <tr>
            <td align="center" rowspan="3" width="15%">
              <img src="https://storage.googleapis.com/pkg-portal-bucket/images/template/logo-PG-agro-trans-small.png" style="background-repeat: no-repeat;height:60px;margin-top:-0.5cm" alt="">
            </td>
            <?php
            $width_bidang = "32%";
            ?>
            <td align="center" rowspan="3" width="<?= $width_bidang ?>">
              <span>bidang</span>
              <br>
              <span class="isiText"><?= (!empty($dokumen)) ? $dokumen['bagian_nama'] : '' ?></span>
            </td>
            <td style="padding-left: 5px;font-size: 10pt;">
              No. Pekerjaan :
              <br>
              <center>
                <b style="font-size: 10px;"><?= (!empty($pekerjaan)) ? $pekerjaan['pekerjaan_nomor'] : '' ?></b>
              </center>
            </td>
            <!-- <td rowspan="2" style="text-align:center;">Revisi : <br>
              <center>
                <b>
                  <?php $cek_approve_vp = $this->db->query("SELECT * FROM dec.dec_pekerjaan_dokumen WHERE id_pekerjaan='" . $dokumen['id_pekerjaan'] . "' and is_lama ='n' and pekerjaan_dokumen_awal='n' AND (pekerjaan_dokumen_status = '4' OR pekerjaan_dokumen_status = '11' ) AND pekerjaan_dokumen_nomor = '" . $dokumen['pekerjaan_dokumen_nomor'] . "'")->num_rows();
                  ?>
                  <?= ($dokumen['pekerjaan_dokumen_revisi'] && $cek_approve_vp > 0) ? $dokumen['pekerjaan_dokumen_revisi'] : '0'   ?>
                </b>
              </center>
            </td> -->
            <td align="center" rowspan="3" width="80px" valign="top">
              &nbsp;
            </td>
          </tr>
          <tr>
            <td colspan="1" style="padding-left: 5px;font-size: 10pt;">
              No Dokumen :
              <br>
              <center>
                <b style="font-size: 10px;">
                  <?= (!empty($dokumen)) ? $dokumen['pekerjaan_dokumen_nomor'] : ''   ?>
                </b>
              </center>
            </td>
          </tr>
          <tr>
            <td colspan="1" style="padding-left: 5px;font-size: 10pt;">
              Halaman :
              <br>
              <center>
                <b style="font-size: 10px;">
                  <?= $halaman;  ?>
                </b>
              </center>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td colspan="2" height="600px" align="center">
        <span><i>Nama Pekerjaan/Proyek : </i></span>
        <p class="text-muted isiText"><?= (!empty($pekerjaan)) ? $pekerjaan['pekerjaan_judul'] : '' ?></p>

        <span><i>Kontraktor : </i></span>
        <p class="text-muted isiText"><?= (!empty($pekerjaan)) ? $pekerjaan['pekerjaan_kontraktor_nama'] : '' ?></p>

        <span><i>Location : </i></span>
        <p class="text-muted isiText">Gresik</p>
      </td>
    </tr>
    <tr>
      <td>
        <table border="1" width="80%" align="center" class="table">
          <?php
          $data_dokumen = array_combine(array_reverse(array_keys($data_dokumen)), $data_dokumen);
          foreach ($data_dokumen as $value_dokumen) {
            if ($value_dokumen['pekerjaan_dokumen_status'] == '6') {
              $status = 'Approve';
            } else {
              $status = 'Review';
            }
          ?>
            <tr>
              <td><?= $status ?></td>
              <td><?= date('Y-m-d', strtotime($value_dokumen['pekerjaan_dokumen_waktu_input'])) ?></td>
            </tr>
          <?php
          }
          ?>
          <tr>
            <td>Uraian</td>
            <td>Tanggal</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td style="padding:10px;text-align:center">
    <tr>
      <td colspan="6">
        <center><span style="bottom:0;font-size:8pt;text-align:center"><i>Engineering Department PT. Petrokimia Gresik</i></span></center>
      </td>
    </tr>
    </td>
    </tr>
  </table>
</body>

</html>