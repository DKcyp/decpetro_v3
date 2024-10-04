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
            /* height: 277mm; */
        }

        .isiText {
            font-family: Arial;
            font-size: 18pt;
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
            top: -30px;
            right: 45px;
            width: 50px;
        }
    </style>

<div id="qrcode">
    <!-- <img src="https://www.kibrispdr.org/data/824/qr-code-from-image-3.png" width="10px" class="ribbon" alt="" /> -->
</div>

</head>

<body>
    <?php //print_r($bagian); 
    ?>
    <table border="0" width="90%" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <table class="table" width="100%" border="1" cellspacing="0" cellpadding="0">
                    <tr>
                        <td align="center" rowspan="3" width="20%">
                            <img src="<?= base_url('assets/gambar/pg_logo_header.png') ?>" style="background-repeat: no-repeat;height:70px;margin:0.3cm" alt="">
                        </td>
                        <td align="center" rowspan="3" width="40%">
                            <span class="isiText"><?php echo (!empty($template)) ? $template['pekerjaan_template_nama'] : '' ?></span>
                            <br>
                            <span>bidang</span>
                            <br>
                            <span class="isiText"><?= (!empty($bagian)) ? $bagian['bagian_nama'] : '' ?></span>
                        </td>
                        <td>No. Pekerjaan :
                            <br>
                            <center>
                                <b style="font-size: 13px;"><?= (!empty($pekerjaan)) ? $pekerjaan['pekerjaan_nomor'] : '' ?></b>
                            </center>
                        </td>
                        <td rowspan="2" style="text-align:center;">Revisi : <br>
                            <center>
                                <b>
                                    <?= ($template['pekerjaan_dokumen_revisi']) ? $template['pekerjaan_dokumen_revisi'] : '0'   ?>
                                </b>
                            </center>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="1">No Dokumen
                            <br>
                            <center>
                                <b style="font-size: 13px;">
                                    <?= (!empty($template)) ? $template['pekerjaan_dokumen_nomor'] : ''   ?>
                                </b>
                            </center>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">Jumlah Lembar :<b><?= (!empty($template)) ? $template['pekerjaan_dokumen_jumlah'] : '' ?></b>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr style="">
            <td colspan="2" height="600px" align="center">

                <span><i>Nama Pekerjaan/Proyek : </i></span>
                <p class="text-muted isiText"><?= (!empty($pekerjaan)) ? $pekerjaan['pekerjaan_judul'] : '' ?></p>

                <span><i>Client : </i></span>
                <p class="text-muted isiText"><?= (!empty($pekerjaan)) ? $pekerjaan['pegawai_nama_dep'] : '' ?></p>

                <span><i>Location : </i></span>
                <p class="text-muted isiText">Gresik</p>
            </td>
        </tr>
        <tr style="">
            <td>
                <table border="1" margin="20px" width="80%" align="center" class="table">
                    <?php $dokumen_id = explode('~', $_GET['pekerjaan_dokumen_file']); ?>
                    <?php $query1 = $this->db->query("SELECT * FROM dec.dec_pekerjaan a LEFT JOIN dec.dec_pekerjaan_dokumen b on a.pekerjaan_id = b.id_pekerjaan LEFT JOIN dec.dec_pekerjaan_disposisi c ON c.id_pekerjaan = a.pekerjaan_id LEFT JOIN global.global_pegawai d ON d.pegawai_nik = c.id_user LEFT JOIN global.global_klasifikasi_dokumen e ON e.id_pegawai = d.pegawai_nik where pekerjaan_id = '" . preg_replace("/[^0-9^a-z^A-Z^_]/", "", $_GET['pekerjaan_id']) . "' AND (pekerjaan_disposisi_status = '3') AND pekerjaan_dokumen_id = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $dokumen_id[1]) . "' ");
                    ?>
                    <?php $data1  = $query1->row_array(); ?>

                    <?php $row1 = $query1->num_rows(); ?>

                    <?php $query2 = $this->db->query("SELECT * FROM dec.dec_pekerjaan a LEFT JOIN dec.dec_pekerjaan_dokumen b on a.pekerjaan_id = b.id_pekerjaan LEFT JOIN dec.dec_pekerjaan_disposisi c ON c.id_pekerjaan = a.pekerjaan_id LEFT JOIN global.global_pegawai d ON d.pegawai_nik = c.id_user LEFT JOIN global.global_klasifikasi_dokumen e ON e.id_pegawai = d.pegawai_nik where pekerjaan_id = '" . preg_replace("/[^0-9^a-z^A-Z^_]/", "", $_GET['pekerjaan_id']) . "' AND pekerjaan_disposisi_status = '4' AND pekerjaan_dokumen_id = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $dokumen_id[1]) . "' ");
                    ?>
                    <?php $data2  = $query2->row_array(); ?>
                    <?php $row2 = $query2->num_rows(); ?>

                    <?php
                      $sql_avp = $this->db->query("SELECT * FROM global.global_klasifikasi_dokumen a LEFT JOIN global.global_bagian_detail b ON a.id_pegawai = b.id_pegawai LEFT JOIN global.global_pegawai c ON a.id_pegawai = c.pegawai_nik WHERE b.id_bagian ='".$bagian['bagian_id']."' AND c.pegawai_jabatan LIKE '30%'");
                      $dataAVP = $sql_avp->row_array();
                    ?>


                    <?php 
                    // $query3 = $this->db->query("SELECT * FROM dec.dec_pekerjaan a LEFT JOIN dec.dec_pekerjaan_dokumen b on a.pekerjaan_id = b.id_pekerjaan LEFT JOIN dec.dec_pekerjaan_disposisi c ON c.id_pekerjaan = a.pekerjaan_id LEFT JOIN global.global_pegawai d ON d.pegawai_nik = c.id_user LEFT JOIN global.global_klasifikasi_dokumen e ON e.id_pegawai = d.pegawai_nik where pekerjaan_id = '" . preg_replace("/[^0-9^a-z^A-Z^_]/", "", $_GET['pekerjaan_id']) . "' AND (pekerjaan_disposisi_status = '5') AND pekerjaan_dokumen_id = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $dokumen_id[1]) . "' pekerjaan_dokumen_keterangan IS NOT NULL ");
                    ?>

                    <?php $query3 = $this->db->query("SELECT * FROM dec.dec_pekerjaan a LEFT JOIN dec.dec_pekerjaan_dokumen b on a.pekerjaan_id = b.id_pekerjaan LEFT JOIN global.global_pegawai d ON d.pegawai_nik = b.id_create_awal LEFT JOIN global.global_klasifikasi_dokumen e ON e.id_pegawai = d.pegawai_nik where pekerjaan_id = '" . preg_replace("/[^0-9^a-z^A-Z^_]/", "", $_GET['pekerjaan_id']) . "'  AND pekerjaan_dokumen_id = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $dokumen_id[1]) . "' ");
                    ?>

                    <?php $data3  = $query3->row_array(); ?>
                   
                    <?php $row3 = $query3->num_rows(); ?>


                    <?php $query_dokumen = $this->db->query("SELECT to_char(pekerjaan_waktu ,'dd-mm-yyyy') as pekerjaan_waktunya, pekerjaan_disposisi_status,pekerjaan_dokumen_revisi FROM dec.dec_pekerjaan a LEFT JOIN dec.dec_pekerjaan_disposisi b ON a.pekerjaan_id = b.id_pekerjaan left join dec.dec_pekerjaan_dokumen c on a.pekerjaan_id = c.id_pekerjaan  WHERE a.pekerjaan_id = '" . preg_replace("/[^0-9^a-z^A-Z^_]/", "", $_GET['pekerjaan_id']) . "'  AND pekerjaan_disposisi_status NOT IN ('1','2','3','4','5','6','7','9','10') AND is_hps='" . $template['is_hps'] . "' AND id_create_awal='" . $template['id_create_awal'] . "' AND pekerjaan_dokumen_revisi is not null  GROUP BY pekerjaan_disposisi_status,to_char(pekerjaan_waktu ,'dd-mm-yyyy'),pekerjaan_dokumen_revisi ORDER BY cast(pekerjaan_disposisi_status as int) DESC  "); ?>

                    <?php $data_dokumen = $query_dokumen->result_array();
                    // echo $this->db->last_query();

                    // print_r($template);

                    $data_dokumen = array_combine(array_reverse(array_keys($data_dokumen)), $data_dokumen);

                    ?>
                    <?php foreach ($data_dokumen as $key => $value) :  ?>
                        <?php
                            if ($value['pekerjaan_disposisi_status'] == '8') $status = '8';
                            else if ($value['pekerjaan_disposisi_status'] == '9') $status = '9';
                            else if ($value['pekerjaan_disposisi_status'] == '12') $status = '12';

                            $tgl_dokumen = $this->db->query("SELECT to_char(pekerjaan_disposisi_waktu ,'dd-mm-yyyy') as waktu FROM dec.dec_pekerjaan_disposisi WHERE pekerjaan_disposisi_status = '".$status."' AND id_pekerjaan = '" . preg_replace("/[^0-9^a-z^A-Z^_]/", "", $_GET['pekerjaan_id']) . "' ORDER BY pekerjaan_disposisi_waktu ASC")->row_array();
                        ?>

                        <tr>
                            <td>
                                <?php
                                echo ($value['pekerjaan_dokumen_revisi'] == '' || $value['pekerjaan_dokumen_revisi'] == null) ? '0' : $value['pekerjaan_dokumen_revisi'];
                                ?>
                            </td>
                            <td>
                                <?php $value['pekerjaan_disposisi_status'] ?>
                                <?php if ($value['pekerjaan_disposisi_status'] == '0') {
                                    echo 'Revisi';
                                } else if ($value['pekerjaan_disposisi_status'] == '8') {
                                    echo 'IFA';
                                } else if ($value['pekerjaan_disposisi_status'] == '9' || $value['pekerjaan_disposisi_status'] == '10' || $value['pekerjaan_disposisi_status'] == '11') {
                                    echo 'IFC';
                                } else if ($value['pekerjaan_disposisi_status'] == '12' || $value['pekerjaan_disposisi_status'] == '15') {
                                    echo 'Selesai';
                                } else if ($value['pekerjaan_disposisi_status'] == '-') {
                                    echo 'reject';
                                } ?>
                            </td>
                            <td><?php
                                // $waktu = explode(' ', $data1['pekerjaan_waktu']);
                                echo $tgl_dokumen['waktu']

                                ?></td>
                            <td>
                                <?php if ($row3 > 0) {
                                    echo $data3['klasifikasi_dokumen_inisial'];
                                } else {
                                    echo '-';
                                } ?>
                            </td>
                            <td>
                                <?php if ($row2 > 0) {
                                    echo $dataAVP['klasifikasi_dokumen_inisial'];
                                } else {
                                    echo '-';
                                } ?>
                            </td>
                            <td>
                                <?php if ($row1 > 0) {
                                    echo $data1['klasifikasi_dokumen_inisial'];
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach;
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
        <tr style="">
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