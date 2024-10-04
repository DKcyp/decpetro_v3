<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Cover</title>

  <style type="text/css">
    @page {
      size: A4;
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

    body {
      orientation: potrait;
      margin: 1cm;
      width: 210mm;
      /* height: 297mm; */
    }

    .isiText {
      font-family: Arial;
      font-size: 18pt;
    }

    .table {
      border: 0px solid black;
      border-collapse: collapse
    }
  </style>

</head>

<body>
  <table class="table" width="95%" border="0">
    <!-- <tr> -->
    <!-- <th width="25%"><img style="background-repeat: no-repeat;height:90px;margin:0.3cm"  src="<?= base_url('gambar/img/logo/logo_PG_Solusi_Agroindustri.png') ?>"></th> -->
    <!-- <th width="75%" style="font-family: Arial, Helvetica, sans-serif;"><h2>PT. PETROKIMIA GRESIK</h2></th> -->
    <!-- </tr> -->
    <tr>
      <th class="isiText" colspan="2" height="815px">
        <div class="d-flex">
          <div class="flex-grow-1 overflow-hidden">
            <h5 class="text-truncate font-size-15">No Pekerjaan</h5>
            <p class="text-muted"><?= ($pekerjaan['pekerjaan_nomor'] != null) ? $pekerjaan['pekerjaan_nomor'] : '-'; ?></p>
          </div>
        </div>
        <div class="d-flex">
          <div class="flex-grow-1 overflow-hidden">
            <h5 class="text-truncate font-size-15">PIC</h5>
            <p class="text-muted"><?= $pekerjaan['pegawai_nama'] ?></p>
          </div>
        </div>
        <div class="d-flex">
          <div class="flex-grow-1 overflow-hidden">
            <h5 class="text-truncate font-size-15">Detail Pekerjaan</h5>
            <p class="text-muted"><?= $pekerjaan['pekerjaan_deskripsi'] ?></p>
          </div>
        </div>
        <div class="row task-dates">
          <div class="col-sm-4 col-6">
            <div class="mt-4">
              <h5 class="font-size-14"><i class="bx bx-calendar me-1 text-primary"></i> Tanggal Pengajuan
              </h5>
              <p class="text-muted mb-0"><?php echo date("d-m-Y", strtotime($pekerjaan['pekerjaan_waktu'])) ?></p>
            </div>
          </div>

        </div>
        </div>
      </th>
    </tr>
  </table>
</body>

</html>
<script>
  // window.print();
</script>