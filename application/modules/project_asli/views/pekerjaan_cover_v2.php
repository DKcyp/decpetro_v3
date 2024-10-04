<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Cover</title>
    <style type="text/css">
        @page {
            size: <?php
            if ($template['pekerjaan_dokumen_kertas'] != null || $template['pekerjaan_dokumen_kertas'] != '' && $template['pekerjaan_dokumen_orientasi'] != null || $template['pekerjaan_dokumen_orientasi'] != '') {
                echo "size: " . $template['pekerjaan_dokumen_kertas'] . ' ' . $template['pekerjaan_dokumen_orientasi'];
            } else {
                echo "size: A4 Portrait;";
            }
            ?> margin: 0;
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
            <?php if ($template['pekerjaan_dokumen_kertas'] == 'A4' && $template['pekerjaan_dokumen_orientasi'] == 'Potrait') {
                echo 'font-size: 15pt';
            } else {
                echo 'font-size: 18pt';
            }
            ?>font-weight: bold;
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
        <!-- <img src="<?= base_url('document/qrcode/') . $template['pekerjaan_dokumen_qrcode'] ?>" width="20px" class="ribbon" alt="" /> -->
    </div>

</head>

<body>
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <table class="table" width="100%" border="1" cellspacing="0" cellpadding="0">
                    <tr>
                        <td align="center" rowspan="3" width="15%">
                            <!-- <img src="<?= base_url('assets/gambar/pg_logo_header.png') ?>" style="background-repeat: no-repeat;height:60px;margin:0.3cm" alt=""> -->
                            <img src="https://storage.googleapis.com/pkg-portal-bucket/images/template/logo-PG-agro-trans-small.png" style="background-repeat: no-repeat;height:70px;margin:0.3cm" alt="">
                        </td>
                        <td align="center" rowspan="3" width="<?php echo ($template['pekerjaan_dokumen_kertas'] == 'A4' && $template['pekerjaan_dokumen_orientasi'] == 'Potrait') ? '32%' : '40%'  ?>">
                            <span class="isiText"><?php echo (!empty($template)) ? $template['pekerjaan_template_nama'] : '' ?></span>
                            <br>
                            <span>bidang</span>
                            <br>
                            <span class="isiText"><?= (!empty($bagian)) ? $bagian['bagian_nama'] : '' ?></span>
                        </td>
                        <td style="padding-left: 5px;">
                            <span style="font-size: 10pt;">
                                No. Pekerjaan :    
                            </span>
                            <br>
                            <center>
                                <b style="font-size: 10px;"><?= (!empty($pekerjaan)) ? $pekerjaan['pekerjaan_nomor'] : '' ?></b>
                            </center>
                        </td>
                        <td rowspan="2" style="text-align:center;">Revisi : <br>
                            <center>
                                <b>
                                    <?= ($template['pekerjaan_dokumen_revisi']) ? $template['pekerjaan_dokumen_revisi'] : '0'   ?>
                                </b>
                            </center>
                        </td>
                        <td align="center" rowspan="3" width="<?php echo ($template['pekerjaan_dokumen_kertas'] == 'A4' && $template['pekerjaan_dokumen_orientasi'] == 'Potrait') ? '80px' : '90px' ?> " valign="top">
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td colspan="1" style="padding-left: 5px;font-size: 10pt;">No Dokumen
                            <br>
                            <center>
                                <b style="font-size: 10px;">
                                    <?= (!empty($template)) ? $template['pekerjaan_dokumen_nomor'] : ''   ?>
                                </b>
                            </center>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding-left: 5px;">Jumlah Lembar : <b><?= (!empty($template)) ? $template['pekerjaan_dokumen_jumlah'] : '' ?></b>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" height="<?php echo ($template['pekerjaan_dokumen_kertas'] == 'A4' && $template['pekerjaan_dokumen_orientasi'] == 'Landscape') ? '450px' : '600px' ?>" align="center">
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
                    $dokumen_id = explode('~', $_GET['pekerjaan_dokumen_file']);
                    /* Disetujui */
                    $sql_disetujui = $this->db->query("SELECT * FROM dec.dec_pekerjaan a LEFT JOIN dec.dec_pekerjaan_dokumen b on a.pekerjaan_id = b.id_pekerjaan LEFT JOIN dec.dec_pekerjaan_disposisi c ON c.id_pekerjaan = a.pekerjaan_id LEFT JOIN global.global_pegawai d ON d.pegawai_nik = c.id_user LEFT JOIN global.global_klasifikasi_dokumen e ON e.id_pegawai = d.pegawai_nik where pekerjaan_id = '" . preg_replace("/[^0-9^a-z^A-Z^_]/", "", $_GET['pekerjaan_id']) . "' AND (pekerjaan_disposisi_status = '3') AND pekerjaan_dokumen_id = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $dokumen_id[1]) . "' ");
                    $data_disetujui  = $sql_disetujui->row_array();
                    $jumlah_disetujui = $sql_disetujui->num_rows();
                    /* Disetujui */
                    /* Diperiksa */
                    $sql_diperiksa = $this->db->query("SELECT * FROM dec.dec_pekerjaan a LEFT JOIN dec.dec_pekerjaan_dokumen b on a.pekerjaan_id = b.id_pekerjaan LEFT JOIN dec.dec_pekerjaan_disposisi c ON c.id_pekerjaan = a.pekerjaan_id LEFT JOIN global.global_pegawai d ON d.pegawai_nik = c.id_user LEFT JOIN global.global_klasifikasi_dokumen e ON e.id_pegawai = d.pegawai_nik where pekerjaan_id = '" . preg_replace("/[^0-9^a-z^A-Z^_]/", "", $_GET['pekerjaan_id']) . "' AND pekerjaan_disposisi_status = '4' AND pekerjaan_dokumen_id = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $dokumen_id[1]) . "' ");
                    $data_diperiksa  = $sql_diperiksa->row_array();
                    $jumlah_diperiksa = $sql_diperiksa->num_rows();
                    $sql_avp = $this->db->query("SELECT * FROM global.global_klasifikasi_dokumen a LEFT JOIN global.global_bagian_detail b ON a.id_pegawai = b.id_pegawai LEFT JOIN global.global_pegawai c ON a.id_pegawai = c.pegawai_nik WHERE b.id_bagian ='" . $bagian['bagian_id'] . "' AND c.pegawai_jabatan LIKE '30%'");
                    $dataAVP = $sql_avp->row_array();
                    /* Diperiksa */
                    /* Disiapkan */
                    $sql_disiapkan = $this->db->query("SELECT * FROM dec.dec_pekerjaan a LEFT JOIN dec.dec_pekerjaan_dokumen b on a.pekerjaan_id = b.id_pekerjaan LEFT JOIN global.global_pegawai d ON d.pegawai_nik = b.id_create_awal LEFT JOIN global.global_klasifikasi_dokumen e ON e.id_pegawai = d.pegawai_nik where pekerjaan_id = '" . preg_replace("/[^0-9^a-z^A-Z^_]/", "", $_GET['pekerjaan_id']) . "'  AND pekerjaan_dokumen_id = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $dokumen_id[1]) . "' ");
                    $data_disiapkan  = $sql_disiapkan->row_array();
                    $jumlah_disiapkan = $sql_disiapkan->num_rows();
                    /* Disiapkan */
                    /*Status DOkumen*/

                    if ($template['is_hps'] == 'n') {
                        if ($template['pekerjaan_dokumen_status'] >= '6' && $template['pekerjaan_dokumen_status'] <= '10') {
                            $sql_dokumen = $this->db->query("SELECT id_pekerjaan_template,pekerjaan_template_nama, to_char(pekerjaan_waktu,'dd-mm-yyyy') as pekerjaan_waktunya,to_char(pekerjaan_dokumen_waktu,'dd-mm-yyyy') as pekerjaan_dokumen_waktunya,pekerjaan_dokumen_revisi,pekerjaan_dokumen_status,pekerjaan_id FROM dec.dec_pekerjaan a LEFT JOIN dec.dec_pekerjaan_dokumen b ON b.id_pekerjaan = a.pekerjaan_id LEFT JOIN dec.dec_pekerjaan_template C ON c.pekerjaan_template_id = b.id_pekerjaan_template WHERE pekerjaan_id = '" . $_GET['pekerjaan_id'] . "' AND is_hps = '" . $template['is_hps'] . "' AND id_create_awal = '" . $template['id_create_awal'] . "' AND id_pekerjaan_template = '" . $template['id_pekerjaan_template'] . "' AND pekerjaan_dokumen_status IN('4') AND pekerjaan_dokumen_waktu IS NOT NULL  GROUP BY to_char(pekerjaan_waktu,'dd-mm-yyyy'),to_char(pekerjaan_dokumen_waktu,'dd-mm-yyyy'),pekerjaan_dokumen_revisi,pekerjaan_dokumen_status,pekerjaan_id,id_pekerjaan_template,pekerjaan_template_nama ORDER BY CAST(pekerjaan_dokumen_status as INT) DESC,pekerjaan_dokumen_revisi DESC");
                        } else if ($template['pekerjaan_dokumen_status'] >= '11') {
                            $sql_dokumen = $this->db->query("SELECT id_pekerjaan_template,pekerjaan_template_nama, to_char(pekerjaan_waktu,'dd-mm-yyyy') as pekerjaan_waktunya,to_char(pekerjaan_dokumen_waktu,'dd-mm-yyyy') as pekerjaan_dokumen_waktunya,pekerjaan_dokumen_revisi,pekerjaan_dokumen_status,pekerjaan_id FROM dec.dec_pekerjaan a LEFT JOIN dec.dec_pekerjaan_dokumen b ON b.id_pekerjaan = a.pekerjaan_id LEFT JOIN dec.dec_pekerjaan_template C ON c.pekerjaan_template_id = b.id_pekerjaan_template WHERE pekerjaan_id = '" . $_GET['pekerjaan_id'] . "' AND is_hps = '" . $template['is_hps'] . "' AND id_create_awal = '" . $template['id_create_awal'] . "' AND id_pekerjaan_template = '" . $template['id_pekerjaan_template'] . "' AND pekerjaan_dokumen_status IN ('4','11') AND pekerjaan_dokumen_waktu IS NOT NULL  GROUP BY to_char(pekerjaan_waktu,'dd-mm-yyyy'),to_char(pekerjaan_dokumen_waktu,'dd-mm-yyyy'),pekerjaan_dokumen_revisi,pekerjaan_dokumen_status,pekerjaan_id,id_pekerjaan_template,pekerjaan_template_nama ORDER BY CAST(pekerjaan_dokumen_status as INT) DESC,pekerjaan_dokumen_revisi DESC");
                        } else {
                            $sql_dokumen = $this->db->query("SELECT id_pekerjaan_template,pekerjaan_template_nama, to_char(pekerjaan_waktu,'dd-mm-yyyy') as pekerjaan_waktunya,to_char(pekerjaan_dokumen_waktu,'dd-mm-yyyy') as pekerjaan_dokumen_waktunya,pekerjaan_dokumen_revisi,pekerjaan_dokumen_status,pekerjaan_id FROM dec.dec_pekerjaan a LEFT JOIN dec.dec_pekerjaan_dokumen b ON b.id_pekerjaan = a.pekerjaan_id LEFT JOIN dec.dec_pekerjaan_template C ON c.pekerjaan_template_id = b.id_pekerjaan_template WHERE pekerjaan_id = '" . $_GET['pekerjaan_id'] . "' AND is_hps = '" . $template['is_hps'] . "' AND id_create_awal = '" . $template['id_create_awal'] . "' AND id_pekerjaan_template = '" . $template['id_pekerjaan_template'] . "' AND pekerjaan_dokumen_status >= '4' AND pekerjaan_dokumen_status <='7' AND pekerjaan_dokumen_waktu IS NOT NULL  GROUP BY to_char(pekerjaan_waktu,'dd-mm-yyyy'),to_char(pekerjaan_dokumen_waktu,'dd-mm-yyyy'),pekerjaan_dokumen_revisi,pekerjaan_dokumen_status,pekerjaan_id,id_pekerjaan_template,pekerjaan_template_nama ORDER BY CAST(pekerjaan_dokumen_status as INT) DESC,pekerjaan_dokumen_revisi DESC");
                        }
                    } else if ($template['is_hps'] == 'y') {
                        if ($template['pekerjaan_dokumen_status'] >= '5' && $template['pekerjaan_dokumen_status'] <= '7') {
                            $sql_dokumen = $this->db->query("SELECT id_pekerjaan_template,pekerjaan_template_nama, to_char(pekerjaan_waktu,'dd-mm-yyyy') as pekerjaan_waktunya,to_char(pekerjaan_dokumen_waktu,'dd-mm-yyyy') as pekerjaan_dokumen_waktunya,pekerjaan_dokumen_revisi,pekerjaan_dokumen_status,pekerjaan_id FROM dec.dec_pekerjaan a LEFT JOIN dec.dec_pekerjaan_dokumen b ON b.id_pekerjaan = a.pekerjaan_id LEFT JOIN dec.dec_pekerjaan_template C ON c.pekerjaan_template_id = b.id_pekerjaan_template WHERE pekerjaan_id = '" . $_GET['pekerjaan_id'] . "' AND is_hps = '" . $template['is_hps'] . "' AND id_create_awal = '" . $template['id_create_awal'] . "' AND id_pekerjaan_template = '" . $template['id_pekerjaan_template'] . "' AND pekerjaan_dokumen_status IN ('7') AND pekerjaan_dokumen_waktu IS NOT NULL  GROUP BY to_char(pekerjaan_waktu,'dd-mm-yyyy'),to_char(pekerjaan_dokumen_waktu,'dd-mm-yyyy'),pekerjaan_dokumen_revisi,pekerjaan_dokumen_status,pekerjaan_id,id_pekerjaan_template,pekerjaan_template_nama ORDER BY CAST(pekerjaan_dokumen_status as INT) DESC,pekerjaan_dokumen_revisi DESC");
                        } else if ($template['pekerjaan_dokumen_status'] >= '8') {
                            $sql_dokumen = $this->db->query("SELECT id_pekerjaan_template,pekerjaan_template_nama, to_char(pekerjaan_waktu,'dd-mm-yyyy') as pekerjaan_waktunya,to_char(pekerjaan_dokumen_waktu,'dd-mm-yyyy') as pekerjaan_dokumen_waktunya,pekerjaan_dokumen_revisi,pekerjaan_dokumen_status,pekerjaan_id FROM dec.dec_pekerjaan a LEFT JOIN dec.dec_pekerjaan_dokumen b ON b.id_pekerjaan = a.pekerjaan_id LEFT JOIN dec.dec_pekerjaan_template C ON c.pekerjaan_template_id = b.id_pekerjaan_template WHERE pekerjaan_id = '" . $_GET['pekerjaan_id'] . "' AND is_hps = '" . $template['is_hps'] . "' AND id_create_awal = '" . $template['id_create_awal'] . "' AND id_pekerjaan_template = '" . $template['id_pekerjaan_template'] . "' AND pekerjaan_dokumen_status IN ('7','9') AND pekerjaan_dokumen_waktu IS NOT NULL  GROUP BY to_char(pekerjaan_waktu,'dd-mm-yyyy'),to_char(pekerjaan_dokumen_waktu,'dd-mm-yyyy'),pekerjaan_dokumen_revisi,pekerjaan_dokumen_status,pekerjaan_id,id_pekerjaan_template,pekerjaan_template_nama ORDER BY CAST(pekerjaan_dokumen_status as INT) DESC,pekerjaan_dokumen_revisi DESC");
                        } else {
                            $sql_dokumen = $this->db->query("SELECT id_pekerjaan_template,pekerjaan_template_nama, to_char(pekerjaan_waktu,'dd-mm-yyyy') as pekerjaan_waktunya,to_char(pekerjaan_dokumen_waktu,'dd-mm-yyyy') as pekerjaan_dokumen_waktunya,pekerjaan_dokumen_revisi,pekerjaan_dokumen_status,pekerjaan_id FROM dec.dec_pekerjaan a LEFT JOIN dec.dec_pekerjaan_dokumen b ON b.id_pekerjaan = a.pekerjaan_id LEFT JOIN dec.dec_pekerjaan_template C ON c.pekerjaan_template_id = b.id_pekerjaan_template WHERE pekerjaan_id = '" . $_GET['pekerjaan_id'] . "' AND is_hps = '" . $template['is_hps'] . "' AND id_create_awal = '" . $template['id_create_awal'] . "' AND id_pekerjaan_template = '" . $template['id_pekerjaan_template'] . "' AND pekerjaan_dokumen_status >='4' AND pekerjaan_dokumen_status <='6' AND pekerjaan_dokumen_waktu IS NOT NULL  GROUP BY to_char(pekerjaan_waktu,'dd-mm-yyyy'),to_char(pekerjaan_dokumen_waktu,'dd-mm-yyyy'),pekerjaan_dokumen_revisi,pekerjaan_dokumen_status,pekerjaan_id,id_pekerjaan_template,pekerjaan_template_nama ORDER BY CAST(pekerjaan_dokumen_status as INT) DESC,pekerjaan_dokumen_revisi DESC");
                        }
                    }
                    /*Status DOkumen*/
                    /* Dokumen */

                    $data_dokumen = $sql_dokumen->result_array();

                    $data_dokumen = array_combine(array_reverse(array_keys($data_dokumen)), $data_dokumen);

                    /* Dokumen */
                    foreach ($data_dokumen as $key => $value) :
                        // print_r($value);
                        $status = $value['pekerjaan_dokumen_status'];
                        $tgl_dokumen = $this->db->query("SELECT to_char(pekerjaan_dokumen_waktu ,'dd-mm-yyyy') as waktu FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_status = '" . $status . "' AND id_pekerjaan = '" . preg_replace("/[^0-9^a-z^A-Z^_]/", "", $_GET['pekerjaan_id']) . "' ORDER BY pekerjaan_dokumen_waktu DESC")->row_array();
                        /* status dokumen */
                        if ($value['pekerjaan_dokumen_status'] == '4' && (($value['pekerjaan_dokumen_revisi'] != null && $value['pekerjaan_dokumen_revisi'] != ''))) {
                            $status = 'IFA';
                        } else if ($value['pekerjaan_dokumen_status'] == '4') {
                            $status = 'IFA';
                        } else if ($value['pekerjaan_dokumen_status'] == '6') {
                            $status = 'IFA';
                        } else if ($value['pekerjaan_dokumen_status'] == '7') {
                            $status = 'IFC';
                        } else if ($value['pekerjaan_dokumen_status'] == '9') {
                            $status = 'IFC';
                        } else {
                            $status = '';
                        }
                        /* status dokumen */
                        ?>
                        <tr>
                            <td>
                                <?php
                                echo ($value['pekerjaan_dokumen_revisi'] == '' || $value['pekerjaan_dokumen_revisi'] == null) ? '0' : $value['pekerjaan_dokumen_revisi'];
                                ?>
                            </td>
                            <td>
                                <?= $status; ?>
                            </td>
                            <td>
                                <?php
                                if (!empty($tgl_dokumen['waktu'])) {
                                    echo $tgl_dokumen['waktu'];
                                } else {
                                    echo $value['pekerjaan_dokumen_waktunya'];
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($jumlah_disiapkan > 0) {
                                    echo $data_disiapkan['klasifikasi_dokumen_inisial'];
                                } else {
                                    echo '-';
                                } ?>
                            </td>
                            <td>
                                <?php if ($jumlah_diperiksa > 0) {
                                    echo $dataAVP['klasifikasi_dokumen_inisial'];
                                } else {
                                    echo '-';
                                } ?>
                            </td>
                            <td>
                                <?php if ($jumlah_disetujui > 0) {
                                    echo $data_disetujui['klasifikasi_dokumen_inisial'];
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
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