<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

</body>
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
            font-weight: bold;
        }

        .table {
            border: 0px solid black;
            border-collapse: collapse
        }
    </style>

</head>
<!-- <?php print_r($pekerjaan); ?> -->

<body>
    <table border="1" width="100%" cellspacing="" class="table">
        <tr>
            <td>
                <table class="table" width="100%" border="1" cellspacing="0" cellpadding="0">
                    <tr>
                        <td align="center" rowspan="3" width="20%">
                            <img src="https://storage.googleapis.com/pkg-portal-bucket/pg_logo_header.png" style="background-repeat: no-repeat;height:70px;margin:0.3cm" alt="">
                        </td>
                        <td align="center" rowspan="3" width="40%">
                            <span class="isiText">Template Pekerjaan</span>
                            <br>
                            <span>bidang</span>
                            <br>
                            <span class="isiText">bidang nama</span>
                        </td>
                        <td>No. Pekerjaan :
                            <br>
                            <center>
                                <b><?= $pekerjaan['pekerjaan_nomor'] ?></b>
                            </center>
                        </td>
                        <td rowspan="2" style="text-align:center;">Revisi</td>
                    </tr>
                    <tr>
                        <td colspan="1">No Dokumen
                            <br>
                            <center>
                                <b></b>
                            </center>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">Jumlah Lembar :
                            <br>
                            <center>
                                <!-- <b><?= $pekerjaan['pekerjaan_nomor'] ?></b> -->
                            </center>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr style="border-bottom:none">
            <td colspan="2" height="815px" align="center">

                <span><i>Nama Pekerjaan/Proyek : </i></span>
                <p class="text-muted isiText"><?= $pekerjaan['pekerjaan_judul'] ?></p>

                <span><i>Client : </i></span>
                <p class="text-muted isiText"><?= $pekerjaan['pegawai_nama_unit_kerja'] ?></p>

                <span><i>Location : </i></span>
                <p class="text-muted isiText">Gresik</p>
            </td>
        </tr>
        <tr style="border-bottom:none;">
            <td>
                <table border="1" margin="20px" width="80%" align="center" class="table">
                    <?php //foreach ($pekerjaan as $key => $value) : 
                    ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php //endforeach; 
                    ?>
                    <tr>
                        <td>Rev</td>
                        <td>Uraian</td>
                        <td>Tanggal</td>
                        <td>Disiapkan</td>
                        <td>Diperiksa</td>
                        <td>Disetujui</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr style="border-bottom:none;border-top:none">
            <td style="padding:20px;text-align:center">
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
<script>
    // window.print();
</script>

</html>