<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Cover</title>
  <style type="text/css">
    @page {
      size: A4 Portrait;
    }
    margin: 0;
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
  <!-- <img src="<?= base_url('document/qrcode/') . $dokumen['pekerjaan_dokumen_qrcode'] ?>" width="20px" class="ribbon" alt="" /> -->
</div>

</head>

<body>
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td>
        <table class="table" width="100%" border="1" cellspacing="0" cellpadding="0">
          <tr>
            <td align="center" rowspan="2" width="25%">
              <img src="https://storage.googleapis.com/pkg-portal-bucket/images/template/logo-PG-agro-trans-small.png" style="background-repeat: no-repeat;height:70px;margin:0.3cm" alt="">
            </td>
            <td align="center" rowspan="2" width="32%">
              <span class="isiText"><?php echo (!empty($dokumen)) ? $dokumen[0]['pekerjaan_template_nama'] : '' ?></span>
            </td>
            <td style="padding-left: 5px;">
              <span style="font-size: 10pt;">
                No. Pekerjaan :    
                <b style="font-size: 10px;"><?= (!empty($pekerjaan)) ? $pekerjaan['pekerjaan_nomor'] : '' ?></b>
              </span>
            </td>
            <td align="center" rowspan="2" width="80px" valign="top">
              &nbsp;
            </td>
          </tr>
          <tr>
            <td style="padding-left: 5px;">Jumlah Lembar : <b>-</b>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td colspan="2" height="450px" align="center">
        <span><i>Nama Pekerjaan/Proyek : </i></span>
        <p class="text-muted isiText"><?= (!empty($pekerjaan)) ? $pekerjaan['pekerjaan_judul'] : '' ?></p>

        <span><i>Client : </i></span>
        <p class="text-muted isiText"><?= (!empty($pekerjaan)) ? $pekerjaan['pegawai_nama_dep'] : '' ?></p>

        <span><i>Location : </i></span>
        <p class="text-muted isiText">Gresik</p>
      </td>
    </tr>
    <tr>
      <td>
        <table border="1" width="80%" align="center" class="table">
          <?php

          $sql_dispoisisi = $this->db->query("SELECT * FROM dec.dec_pekerjaan a LEFT JOIN dec.dec_pekerjaan_disposisi b ON b.id_pekerjaan = a.pekerjaan_id AND a.pekerjaan_status = b.pekerjaan_disposisi_status LEFT JOIN global.global_klasifikasi_dokumen c ON c.id_pegawai = b.id_user WHERE a.pekerjaan_id = '".$pekerjaan['pekerjaan_id']."' AND pekerjaan_disposisi_status IN ('7','13')");
          $data_disposisi = $sql_dispoisisi->result_array();
          $jumlah_disposisi = $sql_dispoisisi->num_rows();
          $data_disposisi = array_combine(array_reverse(array_keys($data_disposisi)), $data_disposisi);
          ?>
          <tr>    
            <?php
            if($jumlah_disposisi>0):
              foreach($data_disposisi as $value_disposisi):
               ?>
               <td><?=$value_disposisi['pekerjaan_disposisi_waktu']?></td>
               <td><?=$value_disposisi['klasifikasi_dokumen_inisial']?></td>
               <?php
             endforeach;
           else:
            ?>
            <td><?=date('Y-m-d')?></td>
            <td>-</td>
            <?php
          endif;
          ?>
        </tr>
        <tr>
          <td>Tanggal</td>
          <td>Disetujui</td>
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