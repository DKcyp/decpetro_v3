<link rel="stylesheet" type="text/css" href="<?= base_url('assets_tambahan/') ?>easyui/themes/default/easyui.css">
<link rel="stylesheet" type="text/css" href="<?= base_url('assets_tambahan/') ?>easyui/themes/icon.css">
<style>
  .description .input-group-append span {
    color: #cc5555 !important;
    margin-left: 11px;
    cursor: pointer;
  }

  #drawingList img {
    cursor: pointer;
  }
</style>

<?php
$jml = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $_GET['pekerjaan_id'] . "' AND (pekerjaan_disposisi_status = '8' OR pekerjaan_disposisi_status = '5')");
$isi_jml = $jml->row_array();

$is_koor = $this->db->get_where('dec.dec_pekerjaan_disposisi', ['id_pekerjaan' => $this->input->get('pekerjaan_id'), 'pekerjaan_disposisi_status' => $this->input->get('status'), 'id_user' => $this->session->userdata()['pegawai_nik']])->row_array();

$data_pic = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE is_cc is not null and id_user in(select pic from dec.dec_pekerjaan where pekerjaan_id = '" . $this->input->get('pekerjaan_id') . "') AND id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' ")->result_array();

?>

<style>
  .tree ul {
    padding-top: 20px;
    position: relative;

    transition: all 0.5s;
    -webkit-transition: all 0.5s;
    -moz-transition: all 0.5s;
  }

  .tree li {
    float: left;
    text-align: center;
    list-style-type: none;
    position: relative;
    padding: 20px 5px 0 5px;

    transition: all 0.5s;
    -webkit-transition: all 0.5s;
    -moz-transition: all 0.5s;
  }

  .tree li::before,
  .tree li::after {
    content: '';
    position: absolute;
    top: 0;
    right: 50%;
    border-top: 1px solid #ccc;
    width: 50%;
    height: 20px;
  }

  .tree li::after {
    right: auto;
    left: 50%;
    border-left: 1px solid #ccc;
  }

  .tree li:only-child::after,
  .tree li:only-child::before {
    display: none;
  }

  .tree li:first-child::before,
  .tree li:last-child::after {
    border: 0 none;
  }

  .tree li:last-child::before {
    border-right: 1px solid #ccc;
    border-radius: 0 5px 0 0;
    -webkit-border-radius: 0 5px 0 0;
    -moz-border-radius: 0 5px 0 0;
  }

  .tree li:first-child::after {
    border-radius: 5px 0 0 0;
    -webkit-border-radius: 5px 0 0 0;
    -moz-border-radius: 5px 0 0 0;
  }

  .tree ul ul::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    border-left: 1px solid #ccc;
    width: 0;
    height: 20px;
  }

  .tree li a {
    border: 1px solid #ccc;
    padding: 5px 10px;
    text-decoration: none;
    color: #666;
    font-family: arial, verdana, tahoma;
    font-size: 9.5px;
    display: inline-block;

    border-radius: 5px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;

    transition: all 0.5s;
    -webkit-transition: all 0.5s;
    -moz-transition: all 0.5s;
  }

  .tree li a:hover,
  .tree li a:hover+ul li a {
    background: #c8e4f8;
    color: #000;
    border: 1px solid #94a0b4;
  }

  .tree li a:hover+ul li::after,
  .tree li a:hover+ul li::before,
  .tree li a:hover+ul::before,
  .tree li a:hover+ul ul::before {
    border-color: #94a0b4;
  }

  #div_hirarki {
    display: flex;
    /* displays flex-items (children) inline */
    overflow-x: auto;
  }

  #div_hirarki .scroll {
    min-width: 1600px;
    width: <?= $isi_jml['total'] * 500 ?>px;
    overflow: hidden;
  }
</style>

<?php
$data_session = $this->session->userdata();

$sql_pekerjaan = $this->db->query("SELECT * FROM dec.dec_pekerjaan a LEFT JOIN global.global_pegawai b ON a.pic = b.pegawai_nik LEFT JOIN global.global_klasifikasi_pekerjaan c ON a.id_klasifikasi_pekerjaan = c.klasifikasi_pekerjaan_id WHERE pekerjaan_id = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) . "'");
$isi_pekerjaan = $sql_pekerjaan->row_array();

$sql_bagian = $this->db->query("SELECT * FROM global.global_bagian a LEFT JOIN global.global_bagian_detail b ON a.bagian_id = b.id_bagian LEFT JOIN global.global_pegawai c ON c.pegawai_nik = b.id_pegawai WHERE b.id_pegawai = '" . $data_session['pegawai_nik'] . "'");
$data_bagian = $sql_bagian->row_array();

$status_disposisi = $this->db->query("SELECT max(pekerjaan_disposisi_status)  as status_disposisi FROM dec.dec_pekerjaan_disposisi a WHERE id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' AND id_user = '" . $data_session['pegawai_nik'] . "' ")->row_array();

$sql_disposisi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user = '" . $data_session['pegawai_nik'] . "' AND id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '" . $status_disposisi['status_disposisi'] . "'");
$data_disposisi = $sql_disposisi->row_array();
// echo "<pre>";
// print_r ($data_disposisi);
// echo "</pre>";
// die();

?>

<div class="page-content">
  <div class="container-fluid">
    <!-- start page title -->
    <div class="row">
      <div class="col-12">
        <?php if ($this->input->get('rkap') == 0) : ?>
          <a href="<?= base_url('project/Non_RKAP') ?>" class="btn btn-success"><u><i class="fa fa-arrow-left"></i> Kembali</u></a>
        <?php else : ?>
          <a href="<?= base_url('project/RKAP') ?>" class="btn btn-success"><u><i class="fa fa-arrow-left"></i> Kembali</u></a>
        <?php endif ?>
        <h4 class="card-title mb-4 text-center">Pekerjaan</h4>
      </div>
    </div>
    <!-- end page title -->

    <!-- Tab -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <ul class="nav nav-pills">
              <li class="nav-item">
                <a class="nav-link active bg-secondary bg-gradient bg-secondary bg-gradient" href="javascript:;" onclick="fun_div_home()" id="link_div_home">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="javascript:;" onclick="fun_div_history()" id="link_div_history">History</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="javascript:;" onclick="fun_div_hirarki()" id="link_div_hirarki">Hirarki</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <!-- Tab -->

    <!-- Div Home -->
    <div class="row" id="div_home">
      <!-- Div Atas -->
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="d-flex">
              <div class="flex-grow-1 overflow-hidden">
                <input hidden type="text" name="session_poscode" id="session_poscode" value="<?= $data_session['pegawai_poscode'] ?>">
                <input hidden type="text" name="session_direct_superior" id="session_direct_superior" value="<?= $data_session['pegawai_direct_superior'] ?>">
                <input hidden type="text" name="session_user" id="session_user" value="<?= $data_session['pegawai_nik'] ?>">
                <input hidden type="text" name="session_bagian" id="session_bagian" value="<?php echo (!empty($data_bagian['bagian_id'])) ? $data_bagian['bagian_id'] : '' ?>">
                <input hidden type="text" name="pekerjaan_status" id="pekerjaan_status" style="display:none">
                <input type="text" name="is_rkap" id="is_rkap" value="<?= $_GET['rkap']; ?>" hidden>
                <input type="text" name="rkap" id="rkap" value="<?php echo ($this->input->get('rkap') == '1') ? 'y' : 'n' ?>" style="display:none">
                <h5 class="text-truncate font-size-15">Pekerjaan</h5>
                <p class="text-muted"><?= (!empty($pekerjaan)) ? $pekerjaan['pekerjaan_judul'] : '-'  ?></p>
              </div>
            </div>
            <div class="d-flex">
              <div class="flex-grow-1 overflow-hidden">
                <h5 class="text-truncate font-size-15">No Pekerjaan</h5>
                <p class="text-muted"><?= (!empty($pekerjaan['pekerjaan_nomor'])) ? $pekerjaan['pekerjaan_nomor'] : '-'; ?></p>
              </div>
            </div>
            <div class="d-flex">
              <div class="flex-grow-1 overflow-hidden">
                <h5 class="text-truncate font-size-15">Klasifikasi Pekerjaan</h5>
                <p class="text-muted"><?= (!empty($pekerjaan)) ? $pekerjaan['klasifikasi_pekerjaan_nama'] : '-'; ?></p>
              </div>
            </div>
            <div class="d-flex">
              <div class="flex-grow-1 overflow-hidden">
                <h5 class="text-truncate font-size-15">PIC</h5>
                <p class="text-muted"><?= (!empty($pekerjaan)) ? $pekerjaan['pegawai_nama'] : '-' ?> - <u><?= (!empty($pekerjaan)) ? $pekerjaan['pegawai_nama_dep'] : '-' ?></u></p>
              </div>
            </div>
            <div class="d-flex">
              <div class="flex-grow-1 overflow-hidden">
                <h5 class="text-truncate font-size-15">No Telp.</h5>
                <p class="text-muted"><?= (!empty($pekerjaan)) ? $pekerjaan['pic_no_telp'] : '-' ?></p>
              </div>
            </div>
            <div class="d-flex">
              <div class="flex-grow-1 overflow-hidden">
                <h5 class="text-truncate font-size-15">Detail Pekerjaan</h5>
                <span class="text-muted" style="word-wrap: break-word;overflow:auto;">
                  <?= (!empty($pekerjaan)) ? ($pekerjaan['pekerjaan_deskripsi']) : '-' ?>
                </span>
              </div>
            </div>
            <div class="d-flex">
              <div class="flex-grow-1 overflow-hidden">
                <h5 class="text-truncate font-size-15">Catatan Pekerjaan</h5>
                <p class="text-muted"><?= (!empty($isi_pekerjaan['pekerjaan_note'])) ? ($isi_pekerjaan['pekerjaan_note']) : '-' ?></p>
              </div>
            </div>
            <div class="d-flex">
              <div class="flex-grow-1 overflow-hidden">
                <h5 class="text-truncate font-size-15">Catatan Disposisi</h5>
                <p class="text-muted"><?= (!empty($data_disposisi['pekerjaan_disposisi_catatan'])) ? ($data_disposisi['pekerjaan_disposisi_catatan']) : '-' ?></p>
              </div>
            </div>
            <?php if ($this->input->get('aksi') == 'berjalan') : ?>
              <div class="d-flex">
                <div class="flex-grow-1 overflow-hidden">
                  <h5 class="text-truncate font-size-15">Prioritas Pekerjaan</h5>
                  <p class="text-muted">
                    <?php
                    switch ($data_disposisi['pekerjaan_disposisi_prioritas']) {
                      case '1':
                      echo 'Normal';
                      break;
                      case '2';
                      echo 'Priority';
                      default:
                        // echo ;
                      break;
                    }
                    ?>
                  </p>
                </div>
              </div>
            <?php endif; ?>
            <?php if ($data_disposisi) { ?>
              <?php if ($data_disposisi['pekerjaan_disposisi_status'] == '5' || $data_disposisi['pekerjaan_disposisi_status'] == '6') : ?>
                <div class="d-flex">
                  <div class="flex-grow-1 overflow-hidden">
                    <h5 class="text-truncate font-size-15">Kategori Pekerjaan</h5>
                    <p class="text-muted">
                      <?php
                      switch ($data_disposisi['pekerjaan_disposisi_kategori']) {
                        case '1':
                        echo 'Mudah';
                        break;
                        case '2';
                        echo 'Sedang';
                        break;
                        case '3';
                        echo 'Sulit';
                        break;
                        default:
                          // echo '';
                        break;
                      }
                      ?>
                    </p>
                  </div>
                </div>
              <?php endif; ?>
            <?php } ?>

            <div class="d-flex">
              <div class="flex-grow-1 overflow-hidden">
                <h5 class="text-truncate font-size-15"><i class="bx bx-calendar me-1 text-primary"></i> Tanggal Pengajuan</h5>
                <p class="text-muted">
                  <?= (!empty($pekerjaan)) ? date("d-m-Y", strtotime($pekerjaan['pekerjaan_waktu'])) : '-' ?>
                </p>
              </div>
            </div>

            <div class="row">
              <!-- Tombol -->
              <div class="row">
                <?php if ($this->input->get('pekerjaan_status') >= 5 || $this->input->get('pekerjaan_status') != '-') : ?>
                <div class="col-sm-4 col-md-3" style="display: block; margin-right: -50px;">
                  <div class="mt-4">
                    <button type="button" class="btn btn-primary col-10" onclick="fun_cc_ifa('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>')" style="display: block; margin-right: -50px;">CC Pekerjaan</button>
                  </div>
                </div>

                <?php if ($this->input->get('aksi') != 'usulan' && $this->input->get('aksi') != 'ifa') : ?>
                <div class="col-sm-4 col-md-3" style="display: block; margin-right: -50px;">
                  <div class="mt-4">
                    <button type="button" class="btn btn-success col-10" onclick="fun_cc_hps_ifa('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>')" style="display: block; margin-right: -50px;">CC HPS</button>
                  </div>
                </div>
              <?php endif; ?>
            <?php endif; ?>
          </div>

          <?php if ($this->input->get('aksi') == 'usulan') : ?>
            <div id="btn_approve" class="col-sm-4 col-md-3" style="display: none;">
              <div class="mt-4">
                <button type="button" class="btn btn-primary col-10" onclick="fun_approve('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>', 'Apakah Anda Yakin Approved?')">Approve</button>
              </div>
            </div>
            <div id="btn_reviewed" class="col-sm-4 col-md-3" style="display: none;">
              <div class="mt-4">
                <button type="button" class="btn btn-primary col-10" onclick="fun_review('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>', 'Apakah Anda Yakin Reviewed?')">Reviewed</button>
              </div>
            </div>
            <div id="btn_disposisi_vp" class="col-sm-4 col-md-3" style="display: none;">
              <div class="mt-4">
                <button type="button" class="btn btn-primary col-10" onclick="fun_disposisi_vp()">Disposisi ke AVP</button>
              </div>
            </div>
            <div id="btn_disposisi_avp" class="col-sm-4 col-md-3" style="display: none;">
              <div class="mt-4">
                <button type="button" class="btn btn-primary col-10" onclick="fun_disposisi_avp('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>');fun_disposisi_avp_check_tj('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>')">Reviewed AVP</button>
              </div>
            </div>
            <div id="btn_ganti_koor" class="col-sm-4 col-md-3" style="display: none;">
              <div class="mt-4">
                <button type="button" class="btn btn-warning col-10" onclick="fun_ganti_koor('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>')">Ganti Koordinator</button>
              </div>
            </div>
            <div id="btn_reject" class="col-sm-4 col-md-3" style="display: none;">
              <div class="mt-4">
                <button type="button" class="btn btn-danger col-10" onclick="fun_reject('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>')">Reject</button>
              </div>
            </div>
            <div id="btn_reject_avp" class="col-sm-4 col-md-3" style="display: none;">
              <div class="mt-4">
                <button type="button" class="btn btn-danger col-10" onclick="fun_reject_avp('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>')">Reject AVP</button>
              </div>
            </div>
          <?php elseif ($this->input->get('aksi') == 'berjalan') : ?>
            <div class="col-12" id="div_penomoran_dokumen" style="display:none;margin-right:-50px">
              <div class="mt-4">
                <label class="alert alert-warning">
                  Penomoran Harus diisi untuk memproses dokumen
                </label>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-4 col-md-3" id="btn_penomoran" style="display: none;margin-right:-50px">
                <div class="mt-4">
                  <button type="button" class="btn btn-warning col-10" onclick="fun_penomoran('<?= $this->input->get('pekerjaan_id') ?>')">Input Urutan Proyek dan Section</button>
                </div>
              </div>
              <div class="col-sm-4 col-md-3" id="btn_send_ifa" style="display: none;margin-right:-50px">
                <div class="mt-4">
                  <button type="button" class="btn btn-primary col-10" onclick="fun_approve_berjalan('<?= $this->input->get('pekerjaan_id') ?>', 'Apakah Anda Yakin Send?')">Send IFA</button>
                </div>
              </div>
            </div>
            <div class="row">
              <div id="btn_ganti_perencana" class="col-sm-4 col-md-3" style="display: none;margin-right:-50px">
                <div class="mt-4">
                  <button type="button" class="btn btn-warning col-10" onclick="fun_ganti_perencana('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>')">Ganti Perencana</button>
                </div>
              </div>
              <div id="btn_ganti_koor" class="col-sm-4 col-md-3" style="display: none;margin-right:-50px">
                <div class="mt-4">
                  <button type="button" class="btn btn-warning col-10" onclick="fun_ganti_koor('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>')">Ganti Koordinator</button>
                </div>
              </div>
              <div id="btn_reject_staf" class="col-sm-4 col-md-3" style="display: none;margin-right:-50px">
                <div class="mt-4">
                  <button type="button" class="btn btn-danger col-10" onclick="fun_reject_staf('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>')">Reject Bagian</button>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-4 col-md-3" id="btn_upload_hps" style="display:none;margin-right:-50px">
                <div class="mt-4">
                  <button type="button" class="btn btn-success col-10" onclick="fun_upload_hps('<?= $this->input->get('pekerjaan_id') ?>','<?= $_GET['status'] ?>')">Upload HPS</button>
                </div>
              </div>
              <div class="col-sm-4 col-md-3" id="btn_upload" style="display: none;margin-right:-50px">
                <div class="mt-4">
                  <button type="button" class="btn btn-success col-10" onclick="fun_upload('<?= $this->input->get('pekerjaan_id') ?>','<?= $_GET['status'] ?>')">Upload Dokumen</button>
                </div>
              </div>
              <div class="col-sm-4 col-md-3" id="btn_progress" style="display: none;margin-right:-50px">
                <div class="mt-4">
                  <button type="button" class="btn btn-success col-10" onclick="fun_progress('<?= $this->input->get('pekerjaan_id') ?>')">Input Progress</button>
                </div>
              </div>
              <div class="col-sm-4 col-md-3" id="btn_nilai_hps" style="display: none;margin-right:-50px">
                <div class="mt-4">
                  <button type="button" class="btn btn-success col-10" onclick="fun_nilai_hps('<?= $this->input->get('pekerjaan_id') ?>')">Input Nilai Total HPS</button>
                </div>
              </div>
            </div>
            <!-- Berjalan -->
          <?php elseif ($this->input->get('aksi') == 'ifa') : ?>
            <!-- IFA -->
            <div class="col-sm-4 col-md-3" id="btn_upload_hps" style="display:none;margin-right:-50px">
              <div class="mt-4">
                <button type="button" class="btn btn-success col-10" onclick="fun_upload_hps('<?= $this->input->get('pekerjaan_id') ?>','<?= $_GET['status'] ?>')">Upload Dokumen HPS</button>
              </div>
            </div>
            <div class="col-sm-4 col-md-3" id="btn_upload" style="display: none;margin-right:-50px">
              <div class="mt-4">
                <button type="button" class="btn btn-success col-10" onclick="fun_upload('<?= $this->input->get('pekerjaan_id') ?>','<?= $_GET['status'] ?>')">Upload Dokumen Non HPS</button>
              </div>
            </div>
            <div class="col-sm-4 col-md-3" id="btn_progress" style="display: none;margin-right:-50px">
              <div class="mt-4">
                <button type="button" class="btn btn-success col-10" onclick="fun_progress('<?= $this->input->get('pekerjaan_id') ?>')">Progress</button>
              </div>
            </div>

            <div id="btn_ganti_perencana" class="col-sm-4 col-md-3" style="display: none;margin-right:-50px">
              <div class="mt-4">
                <button type="button" class="btn btn-warning col-10" onclick="fun_ganti_perencana('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>')">Ganti Perencana</button>
              </div>
            </div>

            <div id="btn_reject_staf" class="col-sm-4 col-md-3" style="display: none;margin-right:-50px">
              <div class="mt-4">
                <button type="button" class="btn btn-danger col-10" onclick="fun_reject_staf('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>')">Reject Bagian</button>
              </div>
            </div>
            <!-- IFA -->
          <?php elseif ($this->input->get('aksi') == 'ifc') : ?>
            <!-- IFC -->
            <div class="row">
              <div class="col-sm-4 col-md-3" id="btn_upload_ifc_hps" style="display: none;margin-right: -50px;">
                <div class="mt-4">
                  <button type="button" class="btn btn-success col-10" onclick="fun_upload_ifc_hps('<?= $this->input->get('pekerjaan_id') ?>','<?= $_GET['status'] ?>')" style="margin-right: -50px;">Upload Dokumen HPS</button>
                </div>
              </div>
              <div class="col-sm-4 col-md-3" id="btn_upload_ifc" style="display: none;margin-right: -50px;">
                <div class="mt-4">
                  <button type="button" class="btn btn-success col-10" onclick="fun_upload_ifc('<?= $this->input->get('pekerjaan_id') ?>','<?= $_GET['status'] ?>')" style="margin-right: -50px;">Upload Dokumen Non HPS</button>
                </div>
              </div>
              <div class="col-sm-4 col-md-3" id="btn_progress" style="display: none;">
                <div class="mt-4">
                  <button type="button" class="btn btn-success col-10" onclick="fun_progress_ifc('<?= $this->input->get('pekerjaan_id') ?>')">Progress</button>
                </div>
              </div>
              <div id="btn_reject_staf" class="col-sm-4 col-md-3" style="display: none;">
                <div class="mt-4">
                  <button type="button" class="btn btn-danger col-10" onclick="fun_reject_staf_ifc('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>')">Reject Staf</button>
                </div>
              </div>
            </div>
            <!-- IFC -->
          <?php endif ?>
          <!-- Tombol -->
        </div>
      </div>
    </div>
  </div>
  <!-- Div Atas -->
  <!-- Div Bawah -->
  <?php if ($_GET['aksi'] == 'usulan') : ?>
    <!-- Detail Usulan -->
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title mb-4">Dokumen</h4>
          <h4>
            <button class="btn btn-success" id="pdf_dokumen_usulan" name="pdf_dokumen_usulan" onClick="pdf_dokumen_usulan()">PDF</button>
          </h4>
          <table class="table table-bordered table-striped" id="table_dokumen_usulan">
            <thead>
              <tr>
                <th>Pilih</th>
                <th>No</th>
                <th>Nama File</th>
                <th style="text-align: center;">Lihat</th>
                <th style="text-align: center;">Download</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
    <!-- Detail Usulan -->
  <?php elseif ($_GET['aksi'] == 'berjalan') : ?>
    <!-- Detail Pekerjaan -->

    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <?php
          $hasil = '';
          $sql = $this->db->query("SELECT * FROM global.global_bagian a LEFT JOIN global.global_bagian_detail b ON b.id_bagian = a.bagian_id  LEFT JOIN global.global_klasifikasi_dokumen c ON c.id_pegawai = b.id_pegawai WHERE b.id_pegawai IN(SELECT id_user FROM dec.dec_pekerjaan_disposisi WHERE is_proses IS NULL AND id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' AND pekerjaan_disposisi_status IN('5','6') AND id_penanggung_jawab != 'y')");
            if ($sql->num_rows() > 1) {
              foreach ($sql->result_array() as $value) {
                $hasil .= $value['bagian_nama'] . '(' . $value['klasifikasi_dokumen_inisial'] . ')' . ', ';
              }
            } else if ($sql->num_rows() == 1) {
              $hasil = $sql->row_array()['bagian_nama'] . '(' . $sql->row_array()['klasifikasi_dokumen_inisial'] . ')';
            } else {
              $hasil = '';
            }
            ?>
            <?php if ($sql->num_rows() > '0') : ?>
              <div id="div_cek_proses_berjalan">
                <span style="float: right;" class="alert alert-warning"><?= $hasil ?> Belum Melakukan Proses Pada Pekerjaan Ini</span>
              </div>
              <div class="clearfix"></div>
            <?php endif ?>
            <!-- Tombol -->
            <button type="button" class="btn btn-danger col-2 float-end" id="btn_revisi" onclick="fun_reject_berjalan_ifa('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>')" style="display:none;margin-left: 0.5cm;">Revisi</button>
            <button type="button" class="btn btn-info col-2 float-end" onclick="funcModalSendVP(<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>)" id="btn_send_avp_koor" style="display:none;margin-left: 0.5cm;">
              Send AVP Koor
            </button>
            <button type="button" class="btn btn-success col-2 float-end" onclick="funcModalApproveVP(<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>)" id="btn_approve_vp" style="display:none;margin-left: 0.5cm;">
              Send User
            </button>
            <?php if ($sql->num_rows() == 0) : ?>
              <button type="button" class="btn btn-success col-2 float-end" onclick="funcModalSendVPKoor(<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>)" id="btn_send_avp" style="display:none;margin-left: 0.5cm;">
                Send VP
              </button>
            <?php endif ?>
            <button type="button" class="btn btn-info col-2 float-end" onclick="funcApproveDokumenAVP(<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>)" id="btn_approve_dokumen_avp" style="display:none;margin-left: 0.5cm;">
              Approve All Dokumen
            </button>

            <!-- Tombol -->
          </div>
        </div>
      </div>

      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <ul class="nav nav-tabs">
              <li class="nav-item">
                <a class="nav-link active bg-secondary bg-gradient" href="javascript:;" onclick="div_doc_usulan()" id="link_div_doc_usulan">Usulan</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="javascript:;" onclick="div_doc_ifa()" id="link_div_doc_ifa">IFA</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="javascript:;" onclick="div_doc_ifc()" id="link_div_doc_ifc">IFC</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="javascript:;" onclick="div_doc_ifa_hps()" id="link_div_doc_ifa_hps">HPS</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="javascript:;" onclick="div_doc_ifc_hps()" id="link_div_doc_ifc_hps" style="display: none;">IFC (Dok Internal)</a>
              </li>
            </ul>
          </div>

          <div id="div_doc_usulan">
            <div class="card-body">
              <h4 class="card-title mb-4">Dokumen Usulan</h4>
              <h4>
                <button class="btn btn-success" id="pdf_dokumen_usulan" name="pdf_dokumen_usulan" onClick="pdf_dokumen_usulan()">PDF</button>
              </h4>
              <table class="table table-bordered table-striped" id="table_dokumen_usulan" width="100%">
                <thead>
                  <tr>
                    <th>Pilih</th>
                    <th>No</th>
                    <th>Nama File</th>
                    <th style="text-align: center;">Lihat</th>
                    <th style="text-align: center;">Download</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>

          <div id="div_doc_ifa" style="display:none">
            <div class="card-body">
              <h4 class="card-title mb-4">Dokumen</h4>
              <h4>
                <button class="btn btn-success" id="pdf_dokumen" name="pdf_dokumen" onClick="pdf_dokumen()">PDF</button>
              </h4>
              <table class="table table-striped table-bordered align-middle mb-0" id="table_dokumen" width="100%">
                <thead>
                  <tr>
                    <th>Pilih</th>
                    <th>No</th>
                    <th>Nama File</th>
                    <th>Bagian</th>
                    <th>Status</th>
                    <th>Diupload Oleh</th>
                    <th>Keterangan</th>
                    <th style="text-align: center;">Lihat</th>
                    <th style="text-align: center;">Download</th>
                    <th style="text-align: center;">History</th>
                    <th style="text-align: center;" id="aksi_upload">Aksi</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>

          <div id="div_doc_ifa_hps" style="display:none">
            <div class="card-body">
              <h4 class="card-title mb-4">Dokumen Internal</h4>
              <h4>
                <button class="btn btn-success" id="pdf_dokumen" name="pdf_dokumen" onClick="pdf_dokumen()">PDF</button>
              </h4>
              <table class="table table-bordered table-striped" id="table_dokumen_hps" width="100%">
                <thead>
                  <tr>
                    <th>Pilih</th>
                    <th>No</th>
                    <th>Nama File</th>
                    <th>Bagian</th>
                    <th>Status</th>
                    <th>Diupload Oleh</th>
                    <th>Keterangan</th>
                    <th style="text-align: center;">Lihat</th>
                    <th style="text-align: center;">Download</th>
                    <th style="text-align: center;">History</th>
                    <th style="text-align: center;" id="aksi_upload">Aksi</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>


        <!-- Detail Pekerjaan -->
      <?php elseif ($_GET['aksi'] == 'ifa') : ?>
        <!-- Detail Pekerjaan -->
        <?php
        $is_cc = $this->db->get_where('dec.dec_pekerjaan_disposisi', ['id_pekerjaan' => $this->input->get('pekerjaan_id'), 'pekerjaan_disposisi_status' => $this->input->get('status'), 'id_user' => $this->session->userdata()['pegawai_nik']])->row_array();
          // $is_cc = $this->db->query("SELECT COUNT(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan ='" . $this->input->get_post('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '8' AND id_user IN (SELECT pic FROM dec.dec_pekerjaan WHERE pekerjaan_id = '" . $this->input->get_post('pekerjaan_id') . "' AND pic = '" . $this->session->userdata()['pegawai_nik'] . "') AND id_user = '" . $this->session->userdata()['pegawai_nik'] . "'")->row_array();
        ?>
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <!-- Tombol -->

              <?php if (($is_cc) && $is_cc['is_cc'] != 'y' && $this->input->get('status') == '8') : ?>
              <button type="button" class="btn btn-success col-2 float-end" id="btn_approve_ifa" onclick="fun_approve_ifa('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>', 'Apakah Anda Yakin Approved?')" style="display: none;margin-left: 0.5cm;">
                Approve IFA
              </button>
            <?php endif ?>
            <button type="button" class="btn btn-success col-2 float-end" id="btn_approve_ifa_avp" onclick="fun_approve_ifa_avp('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>', 'Apakah Anda Yakin Approved?')" style="display: none;margin-left: 0.5cm;">
              Approve IFA AVP
            </button>

            <button type="button" class="btn btn-success col-2 float-end" id="btn_approve_ifa_vp" onclick="fun_approve_ifa_vp('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>', 'Apakah Anda Yakin Approved?')" style="display: none;margin-left: 0.5cm;">
              Approve IFA VP
            </button>

            <button type="button" class="btn btn-danger col-2 float-end" id="btn_revisi_ifa" onclick="fun_reject_ifa('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>')" style="display: none;margin-left: 0.5cm;">Revisi IFA</button>

            <?php if (($is_cc) && $is_cc['is_cc'] != 'y' && $this->input->get('status') == '8') : ?>
            <button type="button" class="btn btn-primary col-2 float-end" id="btn_cc_ifa" onclick="fun_cc_ifa('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>')" style="display: none;margin-left: 0.5cm;">CC Dokumen
            </button>
          <?php endif ?>
          <button type="button" class="btn btn-primary col-2 float-end" id="btn_cc_hps_ifa" onclick="fun_cc_hps_ifa('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>')" style="display: none;margin-left: 0.5cm;">CC HPS</button>

          <!-- Tombol -->
        </div>
      </div>
    </div>

    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">

          <ul class="nav nav-tabs">
            <li class="nav-item">
              <a class="nav-link active bg-secondary bg-gradient" href="javascript:;" onclick="div_doc_usulan()" id="link_div_doc_usulan">Usulan</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="javascript:;" onclick="div_doc_ifa()" id="link_div_doc_ifa">IFA</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="javascript:;" onclick="div_doc_ifc()" id="link_div_doc_ifc">IFC</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="javascript:;" onclick="div_doc_ifa_hps()" id="link_div_doc_ifa_hps">HPS</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="javascript:;" onclick="div_doc_ifc_hps()" id="link_div_doc_ifc_hps" style="display: none;">IFC (Dok Internal)</a>
            </li>
          </ul>
        </div>

        <div id="div_doc_usulan">
          <div class="card-body">
            <h4 class="card-title mb-4">Dokumen Usulan</h4>
            <h4>
              <button class="btn btn-success" id="pdf_dokumen_usulan" name="pdf_dokumen_usulan" onClick="pdf_dokumen_usulan()">PDF</button>
            </h4>
            <table class="table table-bordered table-striped" id="table_dokumen_usulan" width="100%">
              <thead>
                <tr>
                  <th>Pilih</th>
                  <th>No</th>
                  <th>Nama File</th>
                  <th style="text-align: center;">Lihat</th>
                  <th style="text-align: center;">Download</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>

        <div id="div_doc_ifa" style="display:none">
          <div class="card-body">
            <h4 class="card-title mb-4">Dokumen IFA</h4>
            <h4>
              <button class="btn btn-success" id="pdf_dokumen" name="pdf_dokumen" onClick="pdf_dokumen()">PDF</button>
            </h4>
            <table class="table table-striped align-middle mb-0" id="table_dokumen_ifa" width="100%">
              <thead>
                <tr>
                  <th>Pilih</th>
                  <th>No</th>
                  <th>Nama File</th>
                  <th>Bagian</th>
                  <th>Status</th>
                  <th>Diupload Oleh</th>
                  <th>Keterangan</th>
                  <th style="text-align: center;">Lihat</th>
                  <th style="text-align: center;">Download</th>
                  <th style="text-align: center;">History</th>
                  <th style="text-align: center;">Aksi</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>

        <div id="div_doc_ifa_hps" style="display:none">
          <div class="card-body">
            <h4 class="card-title mb-4">Dokumen IFA Internal</h4>
            <h4>
              <button class="btn btn-success" id="pdf_dokumen" name="pdf_dokumen" onClick="pdf_dokumen()">PDF</button>
            </h4>
            <table class="table table-bordered table-striped" id="table_dokumen_ifa_hps" width="100%">
              <thead>
                <tr>
                  <th>Pilih</th>
                  <th>No</th>
                  <th>Nama File</th>
                  <th>Bagian</th>
                  <th>Status</th>
                  <th>Diupload Oleh</th>
                  <th>Keterangan</th>
                  <th style="text-align: center;">Lihat</th>
                  <th style="text-align: center;">Download</th>
                  <th style="text-align: center;">History</th>
                  <th style="text-align: center;" id="aksi_upload">Aksi</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>

      </div>
    </div>
    <!-- Detail Pekerjaan -->
  <?php elseif ($_GET['aksi'] == 'ifc') : ?>
    <!-- Detail Pekerjaan -->
    <div class="col-lg-12">
      <div class="card">
        <?php
        $hasil = '';
        $sql = $this->db->query("SELECT * FROM global.global_bagian a LEFT JOIN global.global_bagian_detail b ON b.id_bagian = a.bagian_id WHERE id_pegawai IN(SELECT id_user FROM dec.dec_pekerjaan_disposisi WHERE is_proses IS NULL AND id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' AND pekerjaan_disposisi_status IN('11','12') AND id_penanggung_jawab != 'y')");
          if ($sql->num_rows() > 1) {
            foreach ($sql->result_array() as $value) {
              $hasil .= $value['bagian_nama'] . ', ';
            }
          } else if ($sql->num_rows() == 1) {
            $hasil = $sql->row_array()['bagian_nama'];
          } else {
            $hasil = '';
          }
          ?>
          <div class="card-body">
            <?php if ($sql->num_rows() > '0') : ?>
              <div id="div_cek_proses">
                <span style="float: right;" class="alert alert-warning"><?= $hasil ?> belum melakukan upload dokumen</span>
              </div>
              <div class="clearfix"></div>
            <?php endif ?>
            <!-- Tombol -->
            <button type="button" class="btn btn-danger col-2 float-end" id="btn_revisi" onclick="fun_reject_berjalan_ifc('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>')" style="display:none;margin-left: 0.5cm;">
              Revisi
            </button>
            <?php if ($sql->num_rows() == '0') : ?>
              <button type="button" class="btn btn-success col-2 float-end" onclick="funcModalSendVPIFC(<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>)" id="btn_send_avp_ifc" style="display:none;margin-left: 0.5cm;">
                Send VP
              </button>
            <?php endif ?>
            <button type="button" class="btn btn-info col-2 float-end" onclick="funcModalSendAVPIFC(<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>)" id="btn_send_avp_ifc_koor" style="display:none;margin-left: 0.5cm;">
              Send AVP Koor
            </button>
            <button type="button" class="btn btn-success col-2 float-end" onclick="funcModalApproveVP(<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>)" id="btn_approve_vp" style="display:none;margin-left: 0.5cm;">
              Approve VP
            </button>

            <!-- Tombol -->
          </div>
        </div>
      </div>

      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <ul class="nav nav-tabs">
              <li class="nav-item">
                <a class="nav-link active bg-secondary bg-gradient" href="javascript:;" onclick="div_doc_usulan()" id="link_div_doc_usulan">Usulan</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="javascript:;" onclick="div_doc_ifa()" id="link_div_doc_ifa">IFA</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="javascript:;" onclick="div_doc_ifc()" id="link_div_doc_ifc">IFC</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="javascript:;" onclick="div_doc_ifa_hps()" id="link_div_doc_ifa_hps">HPS</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="javascript:;" onclick="div_doc_ifc_hps()" id="link_div_doc_ifc_hps" style="display: none;">IFC (Dok Internal)</a>
              </li>
            </ul>
          </div>

          <div id="div_doc_usulan">
            <div class="card-body">
              <h4 class="card-title mb-4">Dokumen Usulan</h4>
              <h4>
                <button class="btn btn-success" id="pdf_dokumen_usulan" name="pdf_dokumen_usulan" onClick="pdf_dokumen_usulan()">PDF</button>
              </h4>
              <table class="table table-bordered table-striped" id="table_dokumen_usulan" width="100%">
                <thead>
                  <tr>
                    <th>Pilih</th>
                    <th>No</th>
                    <th>Nama File</th>
                    <th style="text-align: center;">Lihat</th>
                    <th style="text-align: center;">Download</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>

          <div id="div_doc_ifa" style="display:none">
            <div class="card-body">
              <h4 class="card-title mb-4">Dokumen IFA</h4>
              <h4>
                <button class="btn btn-success" id="pdf_dokumen" name="pdf_dokumen" onClick="pdf_dokumen()">PDF</button>
              </h4>
              <table class="table table-striped align-middle mb-0" id="table_dokumen_ifa" width="100%">
                <thead>
                  <tr>
                    <th>Pilih</th>
                    <th>No</th>
                    <th>Nama File</th>
                    <th>Bagian</th>
                    <th>Status</th>
                    <th>Diupload Oleh</th>
                    <th>Keterangan</th>
                    <th style="text-align: center;">Lihat</th>
                    <th style="text-align: center;">Download</th>
                    <th style="text-align: center;">History</th>
                    <th style="text-align: center;">Aksi</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>

          <div id="div_doc_ifa_hps" style="display:none">
            <div class="card-body">
              <h4 class="card-title mb-4">Dokumen IFA Internal</h4>
              <h4>
                <button class="btn btn-success" id="pdf_dokumen" name="pdf_dokumen" onClick="pdf_dokumen()">PDF</button>
              </h4>
              <table class="table table-bordered table-striped" id="table_dokumen_ifa_hps" width="100%">
                <thead>
                  <tr>
                    <th>Pilih</th>
                    <th>No</th>
                    <th>Nama File</th>
                    <th>Bagian</th>
                    <th>Status</th>
                    <th>Diupload Oleh</th>
                    <th>Keterangan</th>
                    <th style="text-align: center;">Lihat</th>
                    <th style="text-align: center;">Download</th>
                    <th style="text-align: center;">History</th>
                    <th style="text-align: center;" id="aksi_upload">Aksi</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>

          <div id="div_doc_ifc" style="display:none">
            <div class="card-body">
              <h4 class="card-title mb-4">Dokumen IFC</h4>
              <h4>
                <button class="btn btn-success" id="pdf_dokumen" name="pdf_dokumen" onClick="pdf_dokumen()">PDF</button>
              </h4>
              <table class="table table-striped align-middle mb-0" id="table_dokumen_ifc" width="100%">
                <thead>
                  <tr>
                    <th>Pilih</th>
                    <th>No</th>
                    <th>Nama File</th>
                    <th>Bagian</th>
                    <th>Status</th>
                    <th>Diupload Oleh</th>
                    <th>Keterangan</th>
                    <th style="text-align: center;">Lihat</th>
                    <th style="text-align: center;">Download</th>
                    <th style="text-align: center;">History</th>
                    <th style="text-align: center;">Aksi</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>

          <div id="div_doc_ifc_hps" style="display:none">
            <div class="card-body">
              <h4 class="card-title mb-4">Dokumen IFC Internal</h4>
              <h4>
                <button class="btn btn-success" id="pdf_dokumen" name="pdf_dokumen" onClick="pdf_dokumen()">PDF</button>
              </h4>
              <table class="table table-striped align-middle mb-0" id="table_dokumen_ifc_hps" width="100%">
                <thead>
                  <tr>
                    <th>Pilih</th>
                    <th>No</th>
                    <th>Nama File</th>
                    <th>Bagian</th>
                    <th>Status</th>
                    <th>Diupload Oleh</th>
                    <th>Keterangan</th>
                    <th style="text-align: center;">Lihat</th>
                    <th style="text-align: center;">Download</th>
                    <th style="text-align: center;">History</th>
                    <th style="text-align: center;">Aksi</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>

        </div>
      </div>

    <?php elseif ($_GET['aksi'] == 'selesai') :  ?>
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <!-- Tombol -->
            <button type="button" class="btn btn-success col-2 float-end" onclick="funcModalSendVP(<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>)" id="btn_send_avp" style="display:none">
              Send VP
            </button>
            <button type="button" class="btn btn-info col-2 float-end" onclick="funcModalSendVP(<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>)" id="btn_send_avp_koor" style="display:none">
              Send AVP Koor
            </button>
            <button type="button" class="btn btn-success col-2 float-end" onclick="funcModalApproveVP(<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>)" id="btn_approve_vp" style="display:none">
              Approve VP
            </button>
            <button type="button" class="btn btn-danger col-1 float-end" id="btn_revisi" onclick="fun_reject_berjalan_ifa('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>')" style="display: none;">Revisi</button>
            <!-- Tombol -->
            <h4 class="card-title mb-4">Dokumen</h4>
          </div>
        </div>
      </div>

      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <ul class="nav nav-tabs">
              <li class="nav-item">
                <li class="nav-item">
                  <a class="nav-link active bg-secondary bg-gradient" href="javascript:;" onclick="div_doc_ifa_selesai()" id="link_div_doc_ifa_selesai">IFA</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="javascript:;" onclick="div_doc_ifc_selesai()" id="link_div_doc_ifc_selesai">IFC</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="javascript:;" onclick="div_doc_hps_selesai()" id="link_div_doc_hps_selesai" style=>HPS</a>
                </li>
              </ul>
            </div>

            <div id="div_doc_ifa_selesai">
              <div class="card-body">
                <h4 class="card-title mb-4">Dokumen IFA</h4>
                <h4>
                  <button class="btn btn-success" id="pdf_dokumen" name="pdf_dokumen" onClick="pdf_dokumen()">PDF</button>
                </h4>
                <table class="table table-striped align-middle mb-0" id="table_dokumen_selesai_ifa" width="100%">
                  <thead>
                    <tr>
                      <th>Pilih</th>
                      <th>No</th>
                      <th>Nama File</th>
                      <th>Bagian</th>
                      <th>Status</th>
                      <th>Diupload Oleh</th>
                      <th>Keterangan</th>
                      <th style="text-align: center;">Lihat</th>
                      <th style="text-align: center;">Download</th>
                      <th style="text-align: center;">History</th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
            <div id="div_doc_ifc_selesai" style="display:none">
              <div class="card-body">
                <h4 class="card-title mb-4">Dokumen IFC</h4>
                <h4>
                  <button class="btn btn-success" id="pdf_dokumen" name="pdf_dokumen" onClick="pdf_dokumen()">PDF</button>
                </h4>
                <table class="table table-striped align-middle mb-0" id="table_dokumen_selesai" width="100%">
                  <thead>
                    <tr>
                      <th>Pilih</th>
                      <th>No</th>
                      <th>Nama File</th>
                      <th>Bagian</th>
                      <th>Status</th>
                      <th>Diupload Oleh</th>
                      <th>Keterangan</th>
                      <th style="text-align: center;">Lihat</th>
                      <th style="text-align: center;">Download</th>
                      <th style="text-align: center;">History</th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>

            <div id="div_doc_hps_selesai" style="display:none ;">
              <div class="card-body">
                <h4 class="card-title mb-4">Dokumen HPS</h4>
                <h4>
                  <button class="btn btn-success" id="pdf_dokumen" name="pdf_dokumen" onClick="pdf_dokumen()">PDF</button>
                </h4>
                <table class="table table-striped align-middle mb-0" id="table_dokumen_selesai_hps" width="100%">
                  <thead>
                    <tr>
                      <th>Pilih</th>
                      <th>No</th>
                      <th>Nama File</th>
                      <th>Bagian</th>
                      <th>Status</th>
                      <th>Diupload Oleh</th>
                      <th>Keterangan</th>
                      <th style="text-align: center;">Lihat</th>
                      <th style="text-align: center;">Download</th>
                      <th style="text-align: center;">History</th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
          </div>
        </div>
        <!-- Detail Pekerjaan -->
      <?php endif ?>
      <!-- Div Bawah -->
    </div>
    <!-- Div Home -->
  </div>

  <!-- Div History -->
  <div class="row" id="div_history" style="display: none;">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title mb-4">History</h4>
          <table class="table table-bordered table-striped" id="table_history" width="100%">
            <thead>
              <tr>
                <th>No</th>
                <th>Waktu</th>
                <th>Aksi</th>
                <!-- <th>Keterangan</th> -->
                <th>Dilakukan Oleh</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
  <!-- Div History -->

  <!-- Div Hirarki -->
  <!-- Div Hirarki -->
  <div class="row" id="div_hirarki" style="display:none">
    <div class="col-lg-12">
      <div class="card scroll">
        <div class="card-body">
          <div class="tree">
            <?php
            $sql_jumlah = $this->db->query("SELECT pekerjaan_disposisi_status FROM dec.dec_pekerjaan_disposisi a LEFT JOIN dec.dec_pekerjaan b ON a.id_pekerjaan = b.pekerjaan_id WHERE is_aktif = 'y' AND a.id_pekerjaan = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) . "' AND  pekerjaan_disposisi_status <= '6' GROUP BY pekerjaan_disposisi_status ORDER BY cast(pekerjaan_disposisi_status as integer) ASC");
            $dataJumlah = $sql_jumlah->result_array();

            $sql_pekerjaan = $this->db->query("SELECT * FROM dec.dec_pekerjaan a LEFT JOIN global.global_pegawai b ON a.pic = b.pegawai_nik WHERE a.pekerjaan_id = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) . "'");
            $dataPekerjaan = $sql_pekerjaan->row_array();

            $sql_cc = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik WHERE id_pekerjaan ='" . $_GET['pekerjaan_id'] . "' AND is_cc IS NOT NULL AND is_aktif = 'y'");
            $data_cc = $sql_cc->result_array();
            $ada_cc = $this->db->query("SELECT COUNT(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $_GET['pekerjaan_id'] . "' AND pekerjaan_disposisi_status = '8' AND is_cc IS NOT NULL")->row_array();
            $total = ($ada_cc['total'] > 7) ? 150 : 100;
              // CC
            if (isset($dataJumlah[0]['pekerjaan_disposisi_status']) && $dataJumlah[0]['pekerjaan_disposisi_status'] == '3') {
              $total = (COUNT($data_cc) != NULL) ? 30 : 100;
              $totalCC = (COUNT($data_cc) != NULL) ? 35 / COUNT($data_cc) : 0;
            }
              // CC
            ?>
            <!-- PERTAMA -->
            <ul>
              <?php if (isset($dataJumlah)) : ?>
                <!-- CC -->
                <?php if (isset($dataJumlah[0]['pekerjaan_disposisi_status']) && $dataJumlah[0]['pekerjaan_disposisi_status'] == '3') : ?>
                  <?php foreach ($data_cc as $value_cc) : ?>
                    <li style="width:<?= $totalCC ?>%">
                      <a href="javascript:0;"><?= $value_cc['pegawai_nama'] ?></a>
                    </li>
                  <?php endforeach ?>
                <?php endif ?>
                <!-- CC -->
                <li style="width: <?= $total ?>%">
                  <a href="javascript:0;"><?= $dataPekerjaan['pegawai_nama'] ?></a>
                  <?php if (isset($dataJumlah[0]['pekerjaan_disposisi_status'])) : ?>
                    <?php
                    $sql_1 = ($dataJumlah[0]['pekerjaan_disposisi_status'] <= 3) ? $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik WHERE is_aktif = 'y' AND id_pekerjaan = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) . "' AND pekerjaan_disposisi_status = '" . $dataJumlah[0]['pekerjaan_disposisi_status'] . "'") : $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik LEFT JOIN global.global_bagian_detail c ON c.id_pegawai = a.id_user LEFT JOIN global.global_bagian d ON d.bagian_id = c.id_bagian  WHERE is_aktif = 'y' AND id_pekerjaan = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) . "' AND pekerjaan_disposisi_status = '" . $dataJumlah[0]['pekerjaan_disposisi_status'] . "' AND b.pegawai_direct_superior = '" . $dataPekerjaan['pegawai_poscode'] . "'");
                    $data1 = $sql_1->result_array();
                    $total1 = (COUNT($data1) != NULL) ? 100 / COUNT($data1) : 0;
                      // CC
                    if ($dataJumlah[0]['pekerjaan_disposisi_status'] == '2') {
                      $total1 = (COUNT($data_cc) != NULL) ? 30 : 100;
                      $totalCC1 = (COUNT($data_cc) != NULL) ? 35 / COUNT($data_cc) : 0;
                    }
                      // CC
                    ?>
                    <?php if (isset($data1)) : ?>
                      <!-- KEDUA -->
                      <ul>
                        <!-- CC -->
                        <?php if ($dataJumlah[0]['pekerjaan_disposisi_status'] == '2') : ?>
                          <?php foreach ($data_cc as $value_cc) : ?>
                            <li style="width:<?= $totalCC1 ?>%">
                              <a href="javascript:0;"><?= $value_cc['pegawai_nama'] ?></a>
                            </li>
                          <?php endforeach ?>
                        <?php endif ?>
                        <!-- CC -->
                        <?php foreach ($data1 as $value1) : ?>
                          <li style="width: <?= $total1; ?>%">
                            <a href="javascript:0;"><?= ($value1['id_penanggung_jawab'] == 'y') ? '<b><u>' . $value1['pegawai_nama'] . '</u></b>' : $value1['pegawai_nama'] ?></a>
                            <?php if (isset($dataJumlah[1]['pekerjaan_disposisi_status'])) : ?>
                              <?php
                              $sql_2 = ($dataJumlah[1]['pekerjaan_disposisi_status'] <= 4) ? $this->db->query("SELECT pegawai_nik,id_penanggung_jawab,pegawai_nama,bagian_id FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik LEFT JOIN global.global_bagian_detail c ON c.id_pegawai = a.id_user LEFT JOIN global.global_bagian d ON d.bagian_id = c.id_bagian WHERE is_aktif = 'y' AND id_pekerjaan = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) . "' AND pekerjaan_disposisi_status = '" . $dataJumlah[1]['pekerjaan_disposisi_status'] . "' GROUP BY pegawai_nik,id_penanggung_jawab,pegawai_nama,bagian_id") : $this->db->query("SELECT pegawai_nik,id_penanggung_jawab,pegawai_nama,bagian_id FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik LEFT JOIN global.global_bagian_detail c ON c.id_pegawai = a.id_user LEFT JOIN global.global_bagian d ON d.bagian_id = c.id_bagian WHERE is_aktif = 'y' AND id_pekerjaan = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) . "' AND pekerjaan_disposisi_status = '" . $dataJumlah[1]['pekerjaan_disposisi_status'] . "' AND b.pegawai_direct_superior = '" . $value1['pegawai_poscode'] . "' GROUP BY pegawai_nik,id_penanggung_jawab,pegawai_nama,bagian_id");
                              $data2 = $sql_2->result_array();
                              $total2 = (COUNT($data2) != NULL) ? 100 / COUNT($data2) : 0;
                                // CC
                              if ($dataJumlah[0]['pekerjaan_disposisi_status'] == '1') {
                                $total2 = (COUNT($data_cc) != NULL) ? 30 : 100;
                                $totalCC2 = (COUNT($data_cc) != NULL) ? 35 / COUNT($data_cc) : 0;
                              }
                                // CC
                              ?>
                              <?php if (isset($data2)) : ?>
                                <!-- KETIGA -->
                                <ul>
                                  <!-- CC -->
                                  <?php if ($dataJumlah[0]['pekerjaan_disposisi_status'] == '1') : ?>
                                    <?php foreach ($data_cc as $value_cc) : ?>
                                      <li style="width:<?= $totalCC2 ?>%">
                                        <a href="javascript:0;"><?= $value_cc['pegawai_nama'] ?></a>
                                      </li>
                                    <?php endforeach ?>
                                  <?php endif ?>
                                  <!-- CC -->
                                  <?php foreach ($data2 as $value2) : ?>
                                    <li style="width:<?= $total2; ?>%">
                                      <a href="javascript:0;"><?= ($value2['id_penanggung_jawab'] == 'y') ? '<b><u>' . $value2['pegawai_nama'] . '</u></b>' : $value2['pegawai_nama'] ?></a>
                                      <?php if (isset($dataJumlah[2]['pekerjaan_disposisi_status'])) : ?>
                                        <?php
                                        $sql_3 = ($dataJumlah[2]['pekerjaan_disposisi_status'] <= 4) ? $this->db->query("SELECT pegawai_nik,id_penanggung_jawab,pegawai_nama,bagian_id FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik LEFT JOIN global.global_bagian_detail c ON c.id_pegawai = a.id_user LEFT JOIN global.global_bagian d ON d.bagian_id = c.id_bagian WHERE is_aktif = 'y' AND id_pekerjaan = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) . "' AND pekerjaan_disposisi_status = '" . $dataJumlah[2]['pekerjaan_disposisi_status'] . "' GROUP BY pegawai_nik,id_penanggung_jawab,pegawai_nama,bagian_id") : $this->db->query("SELECT pegawai_nik,id_penanggung_jawab,pegawai_nama,bagian_id FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik LEFT JOIN global.global_bagian_detail c ON c.id_pegawai = a.id_user LEFT JOIN global.global_bagian d ON d.bagian_id = c.id_bagian WHERE is_aktif = 'y' AND id_pekerjaan = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) . "' AND pekerjaan_disposisi_status = '" . $dataJumlah[2]['pekerjaan_disposisi_status'] . "' AND d.bagian_id = '" . $value2['bagian_id'] . "' GROUP BY pegawai_nik,id_penanggung_jawab,pegawai_nama,bagian_id");
                                        $data3 = $sql_3->result_array();

                                        $total3 = (COUNT($data3) != NULL) ? 100 / COUNT($data3) : 0;
                                        ?>
                                      <?php endif ?>
                                      <?php if (isset($data3)) : ?>
                                        <!-- KEEMPAT -->
                                        <ul>
                                          <?php foreach ($data3 as $value3) : ?>
                                            <li style="width: <?= $total3; ?>%">
                                              <a href="javascript:0;"><?= ($value3['id_penanggung_jawab'] == 'y') ? '<b><u>' . $value3['pegawai_nama'] . '</u></b>' : $value3['pegawai_nama'] ?></a>
                                              <?php if (isset($dataJumlah[3]['pekerjaan_disposisi_status']) && $dataJumlah[3]['pekerjaan_disposisi_status'] != 6) : ?>
                                                <?php
                                                $sql_4 = ($dataJumlah[3]['pekerjaan_disposisi_status'] <= 4) ? $this->db->query("SELECT pegawai_nik,id_penanggung_jawab,pegawai_nama,bagian_id FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik LEFT JOIN global.global_bagian_detail c ON c.id_pegawai = a.id_user LEFT JOIN global.global_bagian d ON d.bagian_id = c.id_bagian WHERE is_aktif = 'y' AND id_pekerjaan = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) . "' AND pekerjaan_disposisi_status = '" . $dataJumlah[3]['pekerjaan_disposisi_status'] . "' GROUP BY pegawai_nik,id_penanggung_jawab,pegawai_nama,bagian_id ORDER BY id_penanggung_jawab DESC") : $this->db->query("SELECT pegawai_nik,id_penanggung_jawab,pegawai_nama,bagian_id FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik LEFT JOIN global.global_bagian_detail c ON c.id_pegawai = a.id_user LEFT JOIN global.global_bagian d ON d.bagian_id = c.id_bagian WHERE is_aktif = 'y' AND id_pekerjaan = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) . "' AND pekerjaan_disposisi_status = '" . $dataJumlah[3]['pekerjaan_disposisi_status'] . "' AND d.bagian_id = '" . $value3['bagian_id'] . "' GROUP BY pegawai_nik,id_penanggung_jawab,pegawai_nama,bagian_id ORDER BY id_penanggung_jawab DESC");
                                                $data4 = $sql_4->result_array();

                                                $cust_data4 = array();
                                                foreach ($data4 as $k4 => $cust_value4) {
                                                  $nik = (isset($data4[$k4 - 1]['pegawai_nik'])) ? $data4[$k4 - 1]['pegawai_nik'] : 0;
                                                  if ($cust_value4['pegawai_nik'] != $nik) array_push($cust_data4, $cust_value4);
                                                }

                                                $total4 = (COUNT($cust_data4) != NULL) ? 100 / COUNT($cust_data4) : 0;
                                                ?>

                                                <?php if (isset($data4)) : ?>
                                                  <!-- KELIMA -->
                                                  <ul>
                                                    <?php foreach ($cust_data4 as $key4 => $value4) : ?>
                                                      <?php  ?>
                                                      <li style="width: <?= $total4; ?>%">
                                                        <a href="javascript:0;"><?= ($value4['id_penanggung_jawab'] == 'y') ? '<b><u>' . $value4['pegawai_nama'] . '</u></b>' : $value4['pegawai_nama'] ?></a>
                                                        <?php if (isset($dataJumlah[4]['pekerjaan_disposisi_status']) && $dataJumlah[4]['pekerjaan_disposisi_status'] != 6) : ?>
                                                          <?php
                                                          $sql_5 = ($dataJumlah[4]['pekerjaan_disposisi_status'] <= 3) ? $this->db->query("SELECT id_penanggung_jawab,pegawai_nik,pegawai_nama FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik LEFT JOIN global.global_bagian_detail c ON c.id_pegawai = a.id_user LEFT JOIN global.global_bagian d ON d.bagian_id = c.id_bagian WHERE is_aktif = 'y' AND a.id_pekerjaan = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) . "' AND pekerjaan_disposisi_status = '" . $dataJumlah[4]['pekerjaan_disposisi_status'] . "' AND d.bagian_id = '" . $value4['bagian_id'] . "' GROUP BY pegawai_nik,id_penanggung_jawab,pegawai_nama") : $this->db->query("SELECT id_penanggung_jawab,pegawai_nik,pegawai_nama FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik LEFT JOIN global.global_bagian_detail c ON c.id_pegawai = a.id_user LEFT JOIN global.global_bagian d ON d.bagian_id = c.id_bagian WHERE is_aktif = 'y' AND a.id_pekerjaan = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) . "' AND pekerjaan_disposisi_status = '" . $dataJumlah[4]['pekerjaan_disposisi_status'] . "'  AND d.bagian_id = '" . $value4['bagian_id'] . "' GROUP BY pegawai_nik,id_penanggung_jawab,pegawai_nama");
                                                          $data5 = $sql_5->result_array();

                                                          $total5 = (COUNT($data5) != NULL) ? 100 / COUNT($data5) : 0;
                                                          ?>
                                                          <?php if (isset($data5)) : ?>
                                                            <ul>
                                                              <?php foreach ($data5 as $value5) : ?>
                                                                <li style="width: <?= ($total5); ?>%">
                                                                  <a href="javascript:0;"><?= ($value5['id_penanggung_jawab'] == 'y') ? '<b><u>' . $value5['pegawai_nama'] . '</u></b>' : $value5['pegawai_nama'] ?></a>
                                                                </li>
                                                              <?php endforeach ?>
                                                            </ul>
                                                          <?php endif ?>
                                                        <?php endif ?>
                                                      </li>
                                                    <?php endforeach ?>
                                                  </ul>
                                                  <!-- KELIMA -->
                                                <?php endif ?>
                                              <?php endif ?>
                                            </li>
                                          <?php endforeach ?>
                                        </ul>
                                        <!-- KEEMPAT -->
                                      <?php endif ?>
                                    </li>
                                  <?php endforeach ?>
                                </ul>
                                <!-- KETIGA -->
                              <?php endif ?>
                            <?php endif ?>
                          </li>
                        <?php endforeach ?>
                      </ul>
                      <!-- KEDUA -->
                    <?php endif ?>
                  <?php endif ?>
                </li>
              <?php endif ?>
            </ul>
            <!-- PERTAMA -->
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Div Hirarki -->
  <!-- Div Hirarki -->
</div>

<!-- MODAL -->
<!-- MODAL DISPOSISI VP -->
<div class="modal fade" id="modal_vp" data-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Disposisi ke AVP</h4>
      </div>
      <div class="modal-body">
        <form id="form_modal_vp">
          <input type="text" name="id_pekerjaan_vp" id="id_pekerjaan_vp" value="<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>" style="display: none">
          <div class="card-body">
            <div class="form-group row col-md-12 mb-3">
              <label class="col-md-4 form-label">Prioritas Pekerjaan</label>
              <select name="prioritas_pekerjaan_vp" id="prioritas_pekerjaan_vp" class="form-control col-md-8 select2" style="width: 100%">
                <option value="">-Pilih Prioritas Pekerjaan-</option>
                <option value="1">Normal</option>
                <option value="2">Priority</option>
              </select>
            </div>
            <div class="form-group row col-md-12 mb-3">
              <label class="col-md-4 form-label">Koordinator Pekerjaan</label>
              <select name="id_tanggung_jawab_vp" id="id_tanggung_jawab_vp" class="form-control col-md-8 select2" style="width: 100%"></select>
              <label style="color:red;display:none" id="id_tanggung_jawab_vp_alert">Koordinator Tidak Boleh Kosong</label>
            </div>
            <div class="form-group row col-md-12 mb-3" id="div_pekerjaan_disposisi_catatan">
              <label class="col-md-4 form-label">Catatan Disposisi Koordinator</label>
              <textarea name="pekerjaan_disposisi_catatan_koordinator" id="pekerjaan_disposisi_catatan_koordinator" class="form-control col-md-8" placeholder="Catatan Disposisi"></textarea>
            </div>
            <div class="form-group row col-md-12 mb-3">
              <label class="col-md-4 form-label">AVP Terkait</label>
              <select name="id_user_vp[]" id="id_user_vp" class="form-control select2" style="width: 100%" multiple></select>
              <label style="color:red;display:none" id="id_user_vp_alert">AVP Terkait Tidak Boleh Kosong</label>
            </div>
            <div class="form-group row col-md-12 mb-3" id="div_pekerjaan_disposisi_catatan">
              <label class="col-md-4 form-label">Catatan Disposisi Terkait</label>
              <textarea name="pekerjaan_disposisi_catatan_terkait" id="pekerjaan_disposisi_catatan_terkait" class="form-control col-md-8" placeholder="Catatan Disposisi"></textarea>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" id="close_vp" class="btn btn-default" data-dismiss="modal" onclick="fun_close_vp()">Close</button>
            <input type="submit" class="btn btn-success pull-right" id="simpan_vp" value="Simpan">
            <button class="btn btn-primary" type="button" id="loading_form_vp" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- MODAL DISPOSISI VP -->

<!-- MODAL DISPOSISI AVP -->
<div class="modal fade" id="modal_avp" data-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Reviewed AVP</h4>
      </div>
      <div class="modal-body">
        <form id="form_modal_avp">
          <input type="text" name="id_pekerjaan_avp" id="id_pekerjaan_avp" value="<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>" style="display: none;">
          <input type="text" name="id_klasifikasi_pekerjaan_avp_rkap" id="id_klasifikasi_pekerjaan_avp_rkap" value="<?php echo (!empty($isi_pekerjaan)) ? $isi_pekerjaan['id_klasifikasi_pekerjaan'] : '-' ?>" style="display:none">
          <div class="card-body">
            <div class="form-group row col-md-12 mb-3">
              <label class="col-md-4 form-label">Kategori Pekerjaan</label>
              <select name="kategori_pekerjaan_avp" id="kategori_pekerjaan_avp" class="form-control col-md-8 select2" style="width: 100%">

              </select>
            </div>
            <div class="form-group row col-md-12 mb-3" id="div_id_user_vp_avp" style="display:none;">
              <label class="col-md-4 form-label">AVP Terkait</label>
              <select name="id_user_vp_avp[]" id="id_user_vp_avp" class="form-control col-md-8 select2" style="width: 100%" multiple></select>
            </div>
            <div class="form-group row col-md-12 mb-3" id="div_pekerjaan_judul_avp" style="display:none">
              <label class="col-md-4 form-label">Nama Pekerjaan</label>
              <input type="text" name="pekerjaan_judul" id="pekerjaan_judul" value="<?= (!empty($isi_pekerjaan)) ? $isi_pekerjaan['pekerjaan_judul'] : '-' ?>" class="form-control col-md-8">
            </div>
            <div class="form-group row col-md-12 mb-3" style="display:none">
              <!-- <div class="form-group row col-md-12 mb-3" id="div_pekerjaan_waktu_akhir_avp" style="display:none"> -->
                <label class="col-md-4 form-label">Estimasi Target Pekerjaan Selesai</label>
                <input type="text" name="pekerjaan_waktu_akhir_avp" id="pekerjaan_waktu_akhir_avp" readonly>
                <!-- <input type="date" name="pekerjaan_waktu_akhir_avp" id="pekerjaan_waktu_akhir_avp" style="background-color: pink;" class="form-control col-md-8" value="<?= (!empty($isi_pekerjaan['pekerjaan_waktu_akhir'])) ? date("Y-m-d", strtotime($isi_pekerjaan['pekerjaan_waktu_akhir'])) : date('Y-m-d') ?>"> -->
              </div>
              <div class="form-group row col-md-12 mb-3" id="div_id_klasifikasi_pekerjaan_avp" style="display:none">
                <label class="col-md-4 form-label">Klasifikasi Pekerjaan</label>
                <select name="id_klasifikasi_pekerjaan_avp" id="id_klasifikasi_pekerjaan_avp" class="form-control col-md-8 select2" style="width: 100%"></select>
              </div>
              <div class="form-group row col-md-12 mb-3" id="div_id_user_avp" style="display:none">
                <label class="col-md-4 form-label">Disposisi</label>
                <select name="id_user_avp" id="id_user_avp" class="form-control col-md-8 select2" style="width: 100%"></select>
              </div>
              <div class="form-group row col-md-12 mb-3" id="div_id_user_avp_listrik" style="display:none">
                <label class="col-md-4 form-label">Disposisi Listrik</label>
                <select name="id_user_avp_listrik" id="id_user_avp_listrik" class="form-control col-md-8 select2" style="width: 100%"></select>
              </div>
              <div class="form-group row col-md-12 mb-3" id="div_id_user_avp_instrumen" style="display:none">
                <label class="col-md-4 form-label">Disposisi Instrumen</label>
                <select name="id_user_avp_instrumen" id="id_user_avp_instrumen" class="form-control col-md-8 select2" style="width: 100%"></select>
              </div>
              <div class="form-group row col-md-12 mb-3" id="div_pekerjaan_disposisi_catatan">
                <label class="col-md-4 form-label">Catatan Disposisi</label>
                <textarea name="pekerjaan_disposisi_catatan" id="pekerjaan_disposisi_catatan" class="form-control col-md-8" placeholder="Catatan Pekerjaan"></textarea>
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" id="close_avp" class="btn btn-default" data-dismiss="modal" onclick="fun_close_avp()">Close</button>
              <input type="submit" class="btn btn-success pull-right" id="simpan_avp" value="Simpan">
              <button class="btn btn-primary" type="button" id="loading_form_avp" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- MODAL DISPOSISI AVP -->

  <!-- MODAL PROGRESS PEKERJAAN -->
  <div class="modal fade" id="modal_progress" data-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Progress Pekerjaan</h4>
        </div>
        <div class="modal-body">
          <form id="form_modal_progress">
            <input type="text" name="id_pekerjaan_progress" id="id_pekerjaan_progress" value="<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>" style="display: none">
            <input type="text" name="progress_id" id="progress_id" style="display:none">
            <div class="card-body row">
              <div class="form-group row col-md-12">
                <label class="col-md-4">Progress Pekerjaan</label>
                <input type="number" name="pekerjaan_progress" id="pekerjaan_progress" class="form-control col-md-8" max="91">
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" id="close_progress" class="btn btn-default" data-dismiss="modal" onclick="fun_close_progress()">Close</button>
              <input type="button" class="btn btn-success pull-right" id="simpan_progress" value="Simpan">
              <input type="button" class="btn btn-primary pull-right" id="edit_progress" value="Edit" style="display: none;">
              <button class="btn btn-primary" type="button" id="loading_form_progress" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- MODAL PROGRESS PEKERJAAN -->

  <!-- MODAL FORMAT PENOMORAN -->
  <div class="modal fade" id="modal_penomoran" data-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Penomoran Dokumen</h4>
        </div>
        <div class="modal-body">
          <form id="form_modal_penomoran">
            <input type="text" name="id_pekerjaan_penomoran" id="id_pekerjaan_penomoran" style="display: none;">
            <input type="text" name="penomoran_id" id="penomoran_id" style="display: none;">
            <div class="card-body row">
              <div class="form-group row col-md-12">
                <label class="col-md-4">Urutan Proyek</label>
                <select name="urutan_proyek_penomoran" id="urutan_proyek_penomoran" class="form-control col-md-8 select2" onchange="fun_ganti_section_area(this.value)"></select>
              </div>
            </div>
            <div class="card-body row">
              <div class="form-group row col-md-12">
                <label class="col-md-4">Section Area</label>
                <select name="section_area_penomoran" id="section_area_penomoran" class="form-control col-md-8 select2"></select>
              </div>
            </div>

            <div class="modal-footer justify-content-between">
              <button type="button" id="close_penomoran" class="btn btn-default" data-dismiss="modal" onclick="fun_close_penomoran()">Close</button>
              <input type="submit" class="btn btn-success pull-right" id="simpan_penomoran" value="Simpan">
              <input type="button" class="btn btn-primary pull-right" id="edit_penomoran" value="Edit" style="display: none;">
              <button class="btn btn-primary" type="button" id="loading_form_penomoran" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- MODAL FORMAT PENOMORAN -->

  <!-- MODAL NILAI HPS -->
  <div class="modal fade" id="modal_nilai_hps" data-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Nilai HPS</h4>
        </div>
        <div class="modal-body">
          <form id="form_modal_nilai_hps">
            <input type="text" id="is_nilai_hps_old" name="is_nilai_hps_old" style="display:none">
            <input type="text" name="id_pekerjaan_nilai_hps" id="id_pekerjaan_nilai_hps" value="<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>" style="display: none">
            <?php
            $data_bagian = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_bagian_detail b ON b.id_pegawai = a.id_user LEFT JOIN global.global_bagian c ON c.bagian_id = b.id_bagian WHERE id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '4' ")->result_array();
            ?>
            <?php foreach ($data_bagian as $key_bagian => $value_bagian) : ?>
              <div class="card-body row">
                <div class="form-group row col-md-12">
                  <label class="col-md-4">Nilai HPS - <?= $value_bagian['bagian_nama'] ?></label>
                  <input type="text" name="nilai_hps_id[]" id="nilai_hps_id_<?= $key_bagian ?>" style="display:none">
                  <input type="text" name="id_bagian_nilai_hps[]" value="<?= $value_bagian['bagian_id'] ?>" style="display: none;">
                  <input type="number" name="pekerjaan_nilai_hps[]" id="pekerjaan_nilai_hps_<?= $value_bagian['bagian_id'] ?>" class="pekerjaan_nilai_hps form-control col-md-8" onkeyup="funSumHPS(`<?= $value_bagian['bagian_id'] ?>`)">
                </div>
              </div>
            <?php endforeach; ?>

            <div class="card-body row">
              <div class="form-group row col-md-12">
                <label class="col-md-4">Nilai HPS Total</label>
                <input type="number" name="pekerjaan_nilai_hps_total" id="pekerjaan_nilai_hps_total" class="form-control col-md-8" disabled>
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" id="close_nilai_hps" class="btn btn-default" data-dismiss="modal" onclick="fun_close_nilai_hps()">Close</button>
              <input type="button" class="btn btn-success pull-right" id="simpan_nilai_hps" value="Simpan">
              <input type="button" class="btn btn-primary pull-right" id="edit_nilai_hps" value="Edit" style="display: none;">
              <button class="btn btn-primary" type="button" id="loading_form_nilai_hps" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- MODAL NILAI HPS -->

  <!-- MODAL GANTI BAGIAN -->
  <div class="modal fade" id="modal_ganti_perencana" data-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Ganti Perencana</h4>
        </div>
        <div class="modal-body">
          <form id="form_modal_ganti_perencana">
            <input type="text" name="id_pekerjaan_ganti_perencana" id="id_pekerjaan_ganti_perencana" value="<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>" style="display: none">
            <input type="text" name="pekerjaan_status_ganti_perencana" id="pekerjaan_status_ganti_perencana" value="<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['status']) ?>" hidden>
            <div class="card-body row">
              <div class="form-group row col-md-12">
                <label class="col-md-4">Pilih Perencana</label>
                <select class="form-control select2" id="id_perencana_baru" name="id_perencana_baru"></select>
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" id="close_ganti_perencana" class="btn btn-default" data-dismiss="modal" onclick="fun_close_ganti_perencana()">Close</button>
              <input type="submit" class="btn btn-success pull-right" id="simpan_ganti_perencana" value="Simpan">
              <input type="submit" class="btn btn-primary pull-right" id="edit_ganti_perencana" value="Edit" style="display: none;">
              <button class="btn btn-primary" type="button" id="loading_form_ganti_perencana" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- MODAL GANTI BAGIAN -->

  <!-- MODAL GANTI KOOR -->
  <div class="modal fade" id="modal_ganti_koor" data-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Ganti Koor</h4>
        </div>
        <div class="modal-body">
          <form id="form_modal_ganti_koor">
            <input type="text" name="id_pekerjaan_ganti_koor" id="id_pekerjaan_ganti_koor" value="<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) ?>" style="display: none">
            <input type="text" name="pekerjaan_status_ganti_koor" id="pekerjaan_status_ganti_koor" value="<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('status')) ?>" style="display: none;">
            <div class="card-body row">
              <div class="form-group row col-md-12">
                <label class="col-md-4">Pilih Koor</label>
                <select class="form-control select2" id="id_koor_baru" name="id_koor_baru"></select>
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" id="close_ganti_koor" class="btn btn-default" data-dismiss="modal" onclick="fun_close_ganti_koor()">Close</button>
              <input type="submit" class="btn btn-success pull-right" id="simpan_ganti_koor" value="Simpan">
              <input type="submit" class="btn btn-primary pull-right" id="edit_ganti_koor" value="Edit" style="display: none;">
              <button class="btn btn-primary" type="button" id="loading_form_ganti_koor" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- MODAL GANTI KOOR -->

  <!-- MODAL UPLOAD DOKUMEN -->
  <div class="modal fade" id="modal_upload" data-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Upload Document</h4>
        </div>
        <div class="modal-body">
          <form id="form_upload_dokumen">
            <div class="form-group row col-md-12">
              <input type="text" name="id_pekerjaan" id="id_pekerjaan" style="display:none">
              <input type="hidden" name="doc_nama" id="doc_nama">
              <table id="dg_document" title="Document" style="width:100%" toolbar="#toolbar" pagination="true" idField="id" rownumbers="true" fitColumns="true" singleSelect="true">
                <thead>
                  <tr>
                    <th field="pekerjaan_dokumen_nama" width="50" editor="{type:'label'}">Nama</th>
                    <th field="pekerjaan_dokumen_file" width="50" editor="{type:'label'}">File</th>
                  </tr>
                </thead>
              </table>
              <div id="toolbar">
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="fun_tambah_document()">New</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="fun_hapus_document()">Delete</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-save" plain="true" onclick="fun_simpan_document()">Save</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-undo" plain="true" onclick="javascript:$('#dg_document').edatagrid('cancelRow')">Cancel</a>
              </div>
            </div>
            <div class="form-group row col-md-12 mt-4">
              <label class="col-md-4">CC Dokumen</label>
              <select class="form-control col-md-12" id="id_user_staf" name="id_user_staf[]" multiple></select>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" id="close_dokumen" class="btn btn-default border border-dark" data-dismiss="modal" onclick="fun_close_dokumen()">Close</button>
              <button type="button" id="draft_dokumen" class="btn btn-primary" onclick="fun_draft_dokumen()">Draft</button>
              <input type="button" class="btn btn-success pull-right" id="simpan_dokumen_ifc_revisi" value="Send IFC" style="display:none">
              <?php if ($_GET['aksi'] == 'ifc') : ?>
                <input type="submit" class="btn btn-success pull-right" id="simpan_dokumen_ifc" value="Send IFC">
              <?php elseif ($_GET['aksi'] == 'berjalan') : ?>
                <input type="submit" class="btn btn-success pull-right" id="simpan_dokumen" value="Send IFA" style="display: none;">
              <?php else : ?>
                <input type="button" class="btn btn-success pull-right" id="simpan_dokumen_rev_ifa" value="Send IFA" onclick="fun_send_ifa_rev()">
              <?php endif; ?>
              <input type="submit" class="btn btn-primary pull-right" id="edit_dokumen" value="Edit" style="display: none;">
              <button class="btn btn-primary" type="button" id="loading_form_dokumen" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- MODAL UPLOAD DOKUMEN -->

  <!-- MODAL HPS DOKUMEN -->
  <div class="modal fade" id="modal_upload_hps" data-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Upload HPS</h4>
        </div>
        <div class="modal-body">
          <form id="form_upload_dokumen_hps">
            <div class="form-group row col-md-12">
              <input type="text" name="id_pekerjaan_hps" id="id_pekerjaan_hps" style="display:none">
              <input type="hidden" name="doc_nama_hps" id="doc_nama_hps">
              <table id="dg_document_hps" title="Document" style="width:100%" toolbar="#toolbar_hps" pagination="true" idField="id" rownumbers="true" fitColumns="true" singleSelect="true">
                <thead>
                  <tr>
                    <th field="pekerjaan_dokumen_nama_hps" width="50" editor="{type:'label'}">Nama</th>
                    <th field="pekerjaan_dokumen_file_hps" width="50" editor="{type:'label'}">File</th>
                  </tr>
                </thead>
              </table>
              <div id="toolbar_hps">
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="fun_tambah_document_hps()">New</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="fun_hapus_document_hps()">Delete</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-save" plain="true" onclick="fun_simpan_document_hps()">Save</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-undo" plain="true" onclick="javascript:$('#dg_document_hps').edatagrid('cancelRow')">Cancel</a>
              </div>
            </div>
            <div class="form-group row col-md-12 mt-4">
              <label class="col-md-4">CC Dokumen HPS</label>
              <select class="form-control col-md-12" id="id_user_staf_hps" name="id_user_staf_hps[]" multiple></select>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" id="close_dokumen_hps" class="btn btn-default border border-dark" data-dismiss="modal" onclick="fun_close_dokumen_hps()">Close</button>
              <button type="button" id="draft_dokumen_hps" class="btn btn-primary" onclick="fun_draft_dokumen_hps()">Draft</button>
              <input type="button" class="btn btn-success pull-right" id="simpan_dokumen_ifc_revisi_hps" value="Send IFC" style="display:none">
              <?php if ($_GET['aksi'] == 'ifc') : ?>
                <input type="submit" class="btn btn-success pull-right" id="simpan_dokumen_ifc_hps" value="Send IFC">
              <?php elseif ($_GET['aksi'] == 'berjalan') : ?>
                <input type="button" class="btn btn-success pull-right" onclick="fun_simpan_dokumen_hps()" value="Send IFA" style="display: none;">
              <?php else : ?>
                <input type="button" class="btn btn-success pull-right" onclick="fun_draft_dokumen_hps()" value="Simpan">
              <?php endif; ?>
              <input type="submit" class="btn btn-primary pull-right" id="edit_dokumen_hps" value="Edit" style="display: none;">
              <button class="btn btn-primary" type="button" id="loading_form_dokumen_hps" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- MODAL HPS DOKUMEN -->


  <!-- MODAL UPLOAD DOKUMEN IFC -->
  <div class="modal fade" id="modal_upload_ifc" data-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Upload Document</h4>
          <button type="button" class="btn-close" aria-label="Close" onclick="return fun_cancel_dokumen_ifc()"></button>
        </div>
        <div class="modal-body">
          <form id="form_upload_dokumen_ifc">
            <div class="form-group row col-md-12">
              <input type="hidden" name="id_pekerjaan_ifc" id="id_pekerjaan_ifc">
              <input type="hidden" name="doc_nama_ifc" id="doc_nama_ifc">
              <table id="dg_document_ifc" title="Document" style="width:100%" toolbar="#toolbar_ifc" pagination="true" idField="id" rownumbers="true" fitColumns="true" singleSelect="true">
                <thead>
                  <tr>
                    <th field="pekerjaan_dokumen_nama_ifc" width="50" editor="{type:'label'}">Nama</th>
                    <th field="pekerjaan_dokumen_file_ifc" width="50" editor="{type:'label'}">File</th>
                  </tr>
                </thead>
              </table>
              <div id="toolbar_ifc">
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="fun_tambah_document_ifc()">New</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="fun_hapus_document_ifc()">Delete</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-save" plain="true" onclick="fun_simpan_document_ifc()">Save</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-undo" plain="true" onclick="javascript:$('#dg_document_ifc').edatagrid('cancelRow')">Cancel</a>
              </div>
            </div>
            <div class="form-group row col-md-12 mt-4">
              <label class="col-md-4">CC Dokumen</label>
              <select class="form-control col-md-12" id="id_user_staf_ifc" name="id_user_staf_ifc[]" multiple></select>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" id="close_dokumen_ifc" class="btn btn-default border border-dark" data-dismiss="modal" onclick="fun_close_dokumen_ifc()">Close</button>
              <button type="button" id="close_dokumen_ifc" class="btn btn-primary" onclick="fun_draft_dokumen_ifc()">Draft</button>
              <input type="button" class="btn btn-success pull-right" id="simpan_dokumen_ifc_revisi_ifc" value="Send IFC" style="display:none">
              <?php if ($_GET['aksi'] == 'ifc') : ?>
                <input type="submit" class="btn btn-success pull-right" id="simpan_dokumen_ifc_ifc" value="Send IFC">
              <?php else : ?>
                <input type="submit" class="btn btn-success pull-right" id="simpan_dokumen_ifc" value="Send IFA" style="display: none;">
              <?php endif; ?>
              <input type="submit" class="btn btn-primary pull-right" id="edit_dokumen_ifc" value="Edit" style="display: none;">
              <button class="btn btn-primary" type="button" id="loading_form_dokumen_ifc" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- MODAL UPLOAD DOKUMEN IFC -->

  <!-- MODAL UPLOAD DOKUMEN IFC -->
  <div class="modal fade" id="modal_upload_ifc_hps" data-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Upload Document Internal</h4>
          <!-- <button type="button" class="btn-close" aria-label="Close" onclick="return fun_cancel_dokumen_ifc()"></button> -->
        </div>
        <div class="modal-body">
          <form id="form_upload_dokumen_ifc_hps">
            <div class="form-group row col-md-12">
              <input type="hidden" name="id_pekerjaan_ifc_hps" id="id_pekerjaan_ifc_hps">
              <input type="hidden" name="doc_nama_ifc_hps" id="doc_nama_ifc_hps">
              <table id="dg_document_ifc_hps" title="Document" style="width:100%" toolbar="#toolbar_ifc_hps" pagination="true" idField="id" rownumbers="true" fitColumns="true" singleSelect="true">
                <thead>
                  <tr>
                    <th field="pekerjaan_dokumen_nama_ifc_hps" width="50" editor="{type:'label'}">Nama</th>
                    <th field="pekerjaan_dokumen_file_ifc_hps" width="50" editor="{type:'label'}">File</th>
                  </tr>
                </thead>
              </table>
              <div id="toolbar_ifc_hps">
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="fun_tambah_document_ifc_hps()">New</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="fun_hapus_document_ifc_hps()">Delete</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-save" plain="true" onclick="fun_simpan_document_ifc_hps()">Save</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-undo" plain="true" onclick="javascript:$('#dg_document_ifc_hps').edatagrid('cancelRow')">Cancel</a>
              </div>
            </div>
            <div class="form-group row col-md-12 mt-4">
              <label class="col-md-4">CC Dokumen HPS</label>
              <select class="form-control col-md-12" id="id_user_staf_ifc_hps" name="id_user_staf_ifc_hps[]" multiple></select>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" id="close_dokumen_ifc_hps" class="btn btn-default border border-dark" data-dismiss="modal" onclick="fun_close_dokumen_ifc_hps()">Close</button>
              <button type="button" id="draft_dokumen_ifc_hps" class="btn btn-primary" onclick="fun_draft_dokumen_ifc_hps()">Draft</button>
              <input type="button" class="btn btn-success pull-right" id="simpan_dokumen_ifc_revisi_ifc_hps" value="Send IFC" style="display:none">
              <?php if ($_GET['aksi'] == 'ifc') : ?>
                <input type="button" class="btn btn-success pull-right" onclick="fun_draft_dokumen_ifc_hps()" value="Simpan">
              <?php else : ?>
                <input type="submit" class="btn btn-success pull-right" id="simpan_dokumen_ifc_hps" value="Send IFA" style="display: none;">
              <?php endif; ?>
              <input type="submit" class="btn btn-primary pull-right" id="edit_dokumen_ifc_hps" value="Edit" style="display: none;">
              <button class="btn btn-primary" type="button" id="loading_form_dokumen_ifc_hps" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- MODAL UPLOAD DOKUMEN IFC -->

  <!-- MODAL AKSI DOKUMEN -->
  <div class="modal fade" id="modal_aksi" data-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Aksi</h4>
        </div>
        <div class="modal-body">
          <form id="form_modal_aksi">
            <input type="text" name="pekerjaan_status_dokumen" id="pekerjaan_status_dokumen" hidden>
            <input type="text" name="pekerjaan_dokumen_id_temp" id="pekerjaan_dokumen_id_temp" style="display: none">
            <div class="card-body row">
              <div class="form-group row col-md-12 div_aksi_toolbar">
              </div>
              <div class="form-group row col-md-12" id="div_aksi_dokumen">
                <div id="aksi_dokumen"></div>
              </div>
              <div class="form-group row col-md-12">
                <label class="col-md-4">Nama File</label>
                <input type="text" name="aksi_nama" id="aksi_nama" class="form-control" readonly>
              </div>
              <div class="form-group row col-md-12">
                <label class="col-md-4">Status</label>
                <select name="aksi_status" id="aksi_status" class="form-control select2" style="width: 100%" onchange="cekStatus(this.value);">
                  <option value="y">Approved</option>
                  <option value="n">Revisi</option>
                </select>
              </div>
              <div class="form-group row col-md-12">
                <label class="col-md-4">File</label>
                <input type="file" name="aksi_file" id="aksi_file" class="form-control">
              </div>
              <div class="form-group row col-md-12" id="div_keterangan" style="display:none">
                <label class="col-md-4">Keterangan</label>
                <input type="text" name="aksi_keterangan" id="aksi_keterangan" class="form-control">
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" id="close_aksi" class="btn btn-default" data-dismiss="modal" onclick="fun_close_aksi()">Close</button>
              <input type="submit" class="btn btn-success pull-right" id="simpan_aksi" value="Simpan">
              <button class="btn btn-primary" type="button" id="loading_form_aksi" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- MODAL AKSI DOKUMEN -->

  <!-- MODAL AKSI DOKUMEN -->
  <div class="modal fade" id="modal_aksi_staf" data-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Aksi</h4>
        </div>
        <div class="modal-body">
          <form id="form_modal_aksi_staf">
            <input type="text" name="pekerjaan_dokumen_id_temp_staf" id="pekerjaan_dokumen_id_temp_staf" style="display: none">
            <div class="card-body row">
              <div class="form-group row col-md-12">
                <div class="form-group row col-md-12">
                  <label class="col-md-4">Nama File</label>
                  <input type="text" name="aksi_nama_staf" id="aksi_nama_staf" class="form-control" readonly>
                </div>
                <!-- <label class="col-md-4">Status</label>
                <select name="aksi_status" id="aksi_status_" class="form-control select2" style="width: 100%" onchange="cekStatus(this.value);">
                  <option value="y">Approved</option>
                  <option value="n">Revisi</option>
                </select> -->
              </div>
              <!-- <div class="form-group row col-md-12" id="div_keterangan" style="display:none"> -->
                <!-- <label class="col-md-4">Keterangan</label> -->
                <input type="text" name="aksi_status_staf" id="aksi_status_staf" class="form-control" value="y" hidden>
                <!-- </div> -->
                <div class="form-group row col-md-12">
                  <label class="col-md-4">File</label>
                  <input type="file" name="aksi_file_staf" id="aksi_file_staf" class="form-control">
                </div>
                <div class="form-group row col-md-12" id="div_keterangan" style="display:none">
                  <label class="col-md-4">Keterangan</label>
                  <input type="text" name="aksi_keterangan_staf" id="aksi_keterangan_staf" class="form-control">
                </div>
                <div class="modal-footer justify-content-between">
                  <button type="button" id="close_aksi_staf" class="btn btn-default" data-dismiss="modal" onclick="fun_close_aksi_staf()">Close</button>
                  <input type="submit" class="btn btn-success pull-right" id="simpan_aksi_staf" value="Simpan">
                  <button class="btn btn-primary" type="button" id="loading_form_aksi_staf" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- MODAL AKSI DOKUMEN -->

    <!-- MODAL AKSI DOKUMEN CC -->
    <div class="modal fade" id="modal_aksi_cc" data-backdrop="static">
      <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Aksi IFA</h4>
          </div>
          <div class="modal-body">
            <form id="form_modal_aksi_cc">
              <input type="text" name="pekerjaan_dokumen_id_temp_cc" id="pekerjaan_dokumen_id_temp_cc" style="display: none">
              <div class="card-body row">
                <div class="form-group row col-md-12">
                  <div class="form-group row col-md-12">
                    <label class="col-md-4">Nama File</label>
                    <input type="text" name="aksi_nama_cc" id="aksi_nama_cc" class="form-control" readonly>
                  </div>
                  <label class="col-md-4">Status</label>
                  <select name="aksi_status_cc" id="aksi_status_cc" class="form-control select2" style="width: 100%" onclick="cekStatusIFACC(this.value)">
                    <option value="y">Approved</option>
                    <option value="n">Revisi</option>
                  </select>
                </div>
                <div class="form-group row col-md-12">
                  <label class="col-md-4">File</label>
                  <input type="file" name="aksi_file_cc" id="aksi_file_cc" class="form-control">
                </div>
                <div class="form-group row col-md-12" id="div_keterangan_cc" style="display:none">
                  <label class="col-md-4">Keterangan</label>
                  <input type="text" name="aksi_keterangan_cc" id="aksi_keterangan_cc" class="form-control">
                </di>
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" id="close_aksi_cc" class="btn btn-default" data-dismiss="modal" onclick="fun_close_aksi_cc()">Close</button>
              <input type="submit" class="btn btn-success pull-right" id="simpan_aksi_cc" value="Simpan">
              <button class="btn btn-primary" type="button" id="loading_form_aksi_cc" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- MODAL AKSI DOKUMEN CC -->

  <!-- MODAL AKSI DOKUMEN IFA -->
  <div class="modal fade" id="modal_aksi_ifa" data-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Aksi IFA</h4>
        </div>
        <div class="modal-body">
          <form id="form_modal_aksi_ifa">
            <input type="text" name="pekerjaan_dokumen_status_ifa" id="pekerjaan_dokumen_status_ifa" style="display:none">
            <input type="text" name="pekerjaan_dokumen_id_temp_ifa" id="pekerjaan_dokumen_id_temp_ifa" style="display: none">
            <div class="card-body row">
              <div class="form-group row col-md-12">
                <div class="form-group row col-md-12">
                  <label class="col-md-4">Nama File</label>
                  <input type="text" name="aksi_nama_ifa" id="aksi_nama_ifa" class="form-control" readonly>
                </div>
                <label class="col-md-4">Status</label>
                <select name="aksi_status_ifa" id="aksi_status_ifa" class="form-control select2" style="width: 100%" onclick="cekStatusIFA(this.value)">
                  <option value="y">Approved</option>
                  <option value="n">Revisi</option>
                </select>
              </div>
              <div class="form-group row col-md-12">
                <label class="col-md-4">File</label>
                <input type="file" name="aksi_file_ifa" id="aksi_file_ifa" class="form-control">
              </div>
              <div class="form-group row col-md-12" id="div_keterangan_ifa" style="display:none">
                <label class="col-md-4">Keterangan</label>
                <input type="text" name="aksi_keterangan_ifa" id="aksi_keterangan_ifa" class="form-control">
              </di>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" id="close_aksi_ifa" class="btn btn-default" data-dismiss="modal" onclick="fun_close_aksi_ifa()">Close</button>
            <input type="submit" class="btn btn-success pull-right" id="simpan_aksi_ifa" value="Simpan">
            <button class="btn btn-primary" type="button" id="loading_form_aksi_ifa" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- MODAL AKSI DOKUMEN IFA -->

<!-- MODAL AKSI DOKUMEN IFA CC -->
<div class="modal fade" id="modal_aksi_ifa_cc" data-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Aksi IFA</h4>
      </div>
      <div class="modal-body">
        <form id="form_modal_aksi_ifa_cc">
          <input type="text" name="pekerjaan_dokumen_id_temp_ifa_cc" id="pekerjaan_dokumen_id_temp_ifa_cc" style="display: none">
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <div class="form-group row col-md-12">
                <label class="col-md-4">Nama File</label>
                <input type="text" name="aksi_nama_ifa_cc" id="aksi_nama_ifa_cc" class="form-control" readonly>
              </div>
              <label class="col-md-4">Status</label>
              <select name="aksi_status_ifa_cc" id="aksi_status_ifa_cc" class="form-control select2" style="width: 100%" onclick="cekStatusIFACC(this.value)">
                <option value="y">Approved</option>
                <option value="n">Revisi</option>
              </select>
            </div>
            <div class="form-group row col-md-12">
              <label class="col-md-4">File</label>
              <input type="file" name="aksi_file_ifa_cc" id="aksi_file_ifa_cc" class="form-control">
            </div>
            <div class="form-group row col-md-12" id="div_keterangan_ifa_cc" style="display:none">
              <label class="col-md-4">Keterangan</label>
              <input type="text" name="aksi_keterangan_ifa_cc" id="aksi_keterangan_ifa_cc" class="form-control">
            </di>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" id="close_aksi_ifa_cc" class="btn btn-default" data-dismiss="modal" onclick="fun_close_aksi_ifa_cc()">Close</button>
          <input type="submit" class="btn btn-success pull-right" id="simpan_aksi_ifa_cc" value="Simpan">
          <button class="btn btn-primary" type="button" id="loading_form_aksi_ifa_cc" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
        </div>
      </form>
    </div>
  </div>
</div>
</div>
<!-- MODAL AKSI DOKUMEN IFA CC -->

<!-- MODAL AKSI DOKUMEN IFC -->
<div class="modal fade" id="modal_aksi_ifc" data-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Aksi IFC</h4>
      </div>
      <div class="modal-body">
        <form id="form_modal_aksi_ifc">
          <input type="text" name="pekerjaan_dokumen_id_temp_ifc" id="pekerjaan_dokumen_id_temp_ifc" style="display: none">
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <div class="form-group row col-md-12">
                <label class="col-md-4">Nama File</label>
                <input type="text" name="aksi_nama_ifc" id="aksi_nama_ifc" class="form-control" readonly>
              </div>
              <label class="col-md-4">Status</label>
              <select name="aksi_status_ifc" id="aksi_status_ifc" class="form-control select2" style="width: 100%" onchange="cekStatusIFC(this.value);">
                <option value="y">Approved</option>
                <option value="n">Revisi</option>
              </select>
            </div>
            <div class="form-group row col-md-12">
              <label class="col-md-4">File</label>
              <input type="file" name="aksi_file_ifc" id="aksi_file_ifc" class="form-control">
            </div>
            <div class="form-group row col-md-12" id="div_keterangan_ifc" style="display:none">
              <label class="col-md-4">Keterangan</label>
              <input type="text" name="aksi_keterangan_ifc" id="aksi_keterangan_ifc" class="form-control">
            </di>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" id="close_aksi_ifc" class="btn btn-default" data-dismiss="modal" onclick="fun_close_aksi_ifc()">Close</button>
            <input type="submit" class="btn btn-success pull-right" id="simpan_aksi_ifc" value="Simpan">
            <button class="btn btn-primary" type="button" id="loading_form_aksi_ifc" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</div>
<!-- MODAL AKSI DOKUMEN IFC -->

<!-- MODAL LIHAT DOKUMEN -->
<div class="modal fade" id="modal_lihat" data-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">View</h4>
      </div>
      <input type="hidden" id="jadwal_id" name="jadwal_id" value="">
      <div class="modal-body">
        <div class="card-body row" id="div_document" style="height: 400px;">
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="close" onclick="fun_close_lihat()" class="btn btn-outline-dark waves-effect waves-light" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- MODAL LIHAT DOKUMEN -->

<!-- MODAL LIHAT DOKUMEN -->
<div class="modal fade" id="modal_edit_dokumen" data-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Aksi</h4>
      </div>
      <div class="modal-body">
        <form id="form_modal_aksi">
          <div class="card-body row">
            <div class="form-group row col-md-12" id="pdf-container" style="height: 400px;">
            </div>
            <div class="form-group row col-md-12">
              <div class="form-group row col-md-12">
                <label class="col-md-4">Nama File</label>
                <input type="text" name="aksi_nama" id="aksi_nama" class="form-control" readonly>
              </div>
              <label class="col-md-4">Status</label>
              <select name="aksi_status" id="aksi_status" class="form-control select2" style="width: 100%" onchange="cekStatus(this.value);">
                <option value="y">Approved</option>
                <option value="n">Revisi</option>
              </select>
            </div>
            <div class="form-group row col-md-12">
              <label class="col-md-4">File</label>
              <input type="file" name="aksi_file" id="aksi_file" class="form-control">
            </div>
            <div class="form-group row col-md-12" id="div_keterangan" style="display:none">
              <label class="col-md-4">Keterangan</label>
              <input type="text" name="aksi_keterangan" id="aksi_keterangan" class="form-control">
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" id="close_aksi" class="btn btn-default" data-dismiss="modal" onclick="fun_close_aksi()">Close</button>
            <input type="submit" class="btn btn-success pull-right" id="simpan_aksi" value="Simpan">
            <button class="btn btn-primary" type="button" id="loading_form_aksi" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- MODAL LIHAT DOKUMEN -->

<!-- MODAL HISTORY DOKUMEN -->
<div class="modal fade" id="modal_history" data-backdrop="static">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">History Dokumen</h4>
      </div>
      <input type="hidden" id="jadwal_id" name="jadwal_id" value="">
      <div class="modal-body">
        <table class="table table-bordered table-striped" id="table_dokumen_history" width="100%">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama File</th>
              <th>Aksi</th>
              <th>Dilakukan Oleh</th>
              <th>Keterangan</th>
              <th>Lihat</th>
              <th>Download</th>
            </tr>
          </thead>
        </table>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="close" onclick="fun_close_history()" class="btn btn-outline-dark waves-effect waves-light" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- MODAL HISTORY DOKUMEN -->


<!-- MODAL SEND VP -->
<div class="modal fade" id="modal_send_vp" data-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Send VP</h4>
      </div>
      <div class="modal-body">
        <form id="form_modal_send_vp">
          <input type="text" name="id_pekerjaan_send_vp" id="id_pekerjaan_send_vp" value="<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>" style="display: none">
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <label class="col-md-4">CC Dokumen</label>
              <select class="form-control col-md-12" id="id_user_send_vp" name="id_user_send_vp[]" multiple></select>
            </div>
          </div>
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <label class="col-md-4">CC Dokumen HPS</label>
              <select class="form-control col-md-12" id="id_user_send_vp_hps" name="id_user_send_vp_hps[]" multiple></select>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" id="close_send_vp" class="btn btn-default" data-dismiss="modal" onclick="fun_close_send_vp()">Close</button>
            <input type="submit" class="btn btn-success pull-right" id="simpan_send_vp" value="Simpan">
            <input type="submit" class="btn btn-primary pull-right" id="edit_send_vp" value="Edit" style="display: none;">
            <button class="btn btn-primary" type="button" id="loading_form_send_vp" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- MODAL SEND VP -->
<div class="modal fade" id="modal_send_vp_koor" data-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Send VP</h4>
      </div>
      <div class="modal-body">
        <form id="form_modal_send_vp_koor">
          <input type="text" name="id_pekerjaan_send_vp_koor" id="id_pekerjaan_send_vp_koor" value="<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>" style="display: none">
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <label class="col-md-4">CC Dokumen</label>
              <select class="form-control col-md-12" id="id_user_send_vp_koor" name="id_user_send_vp_koor[]" multiple></select>
            </div>
          </div>
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <label class="col-md-4">CC Dokumen HPS</label>
              <select class="form-control col-md-12" id="id_user_send_vp_koor_hps" name="id_user_send_vp_koor_hps[]" multiple></select>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" id="close_send_vp_koor" class="btn btn-default" data-dismiss="modal" onclick="fun_close_send_vp_koor()">Close</button>
            <input type="submit" class="btn btn-success pull-right" id="simpan_send_vp_koor" value="Simpan">
            <input type="submit" class="btn btn-primary pull-right" id="edit_send_vp_koor" value="Edit" style="display: none;">
            <button class="btn btn-primary" type="button" id="loading_form_send_vp_koor" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- MODAL SEND AVP -->
<div class="modal fade" id="modal_send_avp_ifc" data-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Send AVP Koor</h4>
      </div>
      <div class="modal-body">
        <form id="form_modal_send_avp_ifc">
          <input type="text" name="id_pekerjaan_send_avp_ifc" id="id_pekerjaan_send_avp_ifc" value="<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>" style="display: none">
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <label class="col-md-4">CC Dokumen</label>
              <select class="form-control col-md-12" id="id_user_send_avp_ifc" name="id_user_send_avp_ifc[]" multiple></select>
            </div>
          </div>
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <label class="col-md-4">CC Dokumen HPS</label>
              <select class="form-control col-md-12" id="id_user_send_avp_ifc_hps" name="id_user_send_avp_ifc_hps[]" multiple></select>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" id="close_send_avp_ifc" class="btn btn-default" data-dismiss="modal" onclick="fun_close_send_vp_ifc()">Close</button>
            <input type="submit" class="btn btn-success pull-right" id="simpan_send_avp_ifc" value="Simpan">
            <input type="submit" class="btn btn-primary pull-right" id="edit_send_avp_ifc" value="Edit" style="display: none;">
            <button class="btn btn-primary" type="button" id="loading_form_send_avp_ifc" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- MODAL SEND VP -->
<div class="modal fade" id="modal_send_vp_ifc" data-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Send VP</h4>
      </div>
      <div class="modal-body">
        <form id="form_modal_send_vp_ifc">
          <input type="text" name="id_pekerjaan_send_vp_ifc" id="id_pekerjaan_send_vp_ifc" value="<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>" style="display: none">
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <label class="col-md-4">CC Dokumen</label>
              <select class="form-control col-md-12" id="id_user_send_vp_ifc" name="id_user_send_vp_ifc[]" multiple></select>
            </div>
          </div>
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <label class="col-md-4">CC Dokumen HPS</label>
              <select class="form-control col-md-12" id="id_user_send_vp_ifc_hps" name="id_user_send_vp_ifc_hps[]" multiple></select>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" id="close_send_vp_ifc" class="btn btn-default" data-dismiss="modal" onclick="fun_close_send_vp_ifc()">Close</button>
            <input type="submit" class="btn btn-success pull-right" id="simpan_send_vp_ifc" value="Simpan">
            <input type="submit" class="btn btn-primary pull-right" id="edit_send_vp_ifc" value="Edit" style="display: none;">
            <button class="btn btn-primary" type="button" id="loading_form_send_vp_ifc" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- MODAL SEND VP -->
<div class="modal fade" id="modal_approve_vp" data-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Approve VP</h4>
      </div>
      <div class="modal-body">
        <form id="form_modal_approve_vp">
          <input type="text" name="id_pekerjaan_approve_vp" id="id_pekerjaan_approve_vp" value="<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>" style="display: none">
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <label class="col-md-4">CC Dokumen</label>
              <select class="form-control col-md-12" id="id_user_approve_vp" name="id_user_approve_vp[]" multiple></select>
            </div>
          </div>
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <label class="col-md-4">CC Dokumen HPS</label>
              <select class="form-control col-md-12" id="id_user_approve_vp_hps" name="id_user_approve_vp_hps[]" multiple></select>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" id="close_approve_vp" class="btn btn-default" data-dismiss="modal" onclick="fun_close_approve_vp()">Close</button>
            <input type="submit" class="btn btn-success pull-right" id="simpan_approve_vp" value="Simpan">
            <input type="submit" class="btn btn-primary pull-right" id="edit_approve_vp" value="Edit" style="display: none;">
            <button class="btn btn-primary" type="button" id="loading_form_approve_vp" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- MODAL -->

<!-- MODAL SEND VP -->
<div class="modal fade" id="modal_approve_ifa" data-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Approve IFA</h4>
      </div>
      <form id="form_modal_approve_ifa">
        <div class="modal-body">
          <input type="text" name="id_pekerjaan_approve_ifa_pic" id="id_pekerjaan_approve_ifa_pic" value="<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>" style="display: none">
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <label class="col-md-4">CC Dokumen</label>
              <select class="form-control col-md-12" id="cc_approve_ifa" name="cc_approve_ifa[]" multiple></select>
            </div>
          </div>
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <label class="col-md-4">CC Dokumen HPS</label>
              <select class="form-control col-md-12" id="cc_approve_ifa_hps" name="cc_approve_ifa_hps[]" multiple></select>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" id="close_approve_ifa_pic" class="btn btn-default" data-dismiss="modal" onclick="fun_close_approve_ifa()">Close</button>
          <input type="button" class="btn btn-success pull-right" id="simpan_approve_ifa_pic" value="Approve" style="display: none;">
          <input type="button" class="btn btn-success pull-right" id="simpan_approve_ifa_avp" value="Approve AVP" style="display: none;">
          <input type="button" class="btn btn-success pull-right" id="simpan_approve_ifa_vp" value="Approve VP" style="display: none;">
          <input type="button" class="btn btn-primary pull-right" id="edit_approve_ifa_pic" value="Edit" style="display: none;">
          <button class="btn btn-primary" type="button" id="loading_form_approve_ifa" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- MODAL -->

<!-- MODAL CC IFA -->
<div class="modal fade" id="modal_cc_ifa" data-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">CC IFA</h4>
      </div>
      <form id="form_modal_cc_ifa">
        <div class="modal-body">
          <input type="text" name="id_pekerjaan_cc_ifa" id="id_pekerjaan_cc_ifa" value="<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>" style="display: none">
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <label class="col-md-4">CC Dokumen</label>
              <select class="form-control col-md-12" id="cc_ifa" name="cc_ifa[]" multiple></select>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" id="close_cc_ifa" class="btn btn-default" data-dismiss="modal" onclick="fun_close_cc_ifa()">Close</button>
          <input type="button" class="btn btn-success pull-right" id="simpan_cc_ifa" value="Simpan">
          <button class="btn btn-primary" type="button" id="loading_form_cc_ifa" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- MODAL -->

<!-- MODAL CC IFA -->
<div class="modal fade" id="modal_cc_hps_ifa" data-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">CC IFA HPS</h4>
      </div>
      <form id="form_modal_cc_hps_ifa">
        <div class="modal-body">
          <input type="text" name="id_pekerjaan_cc_hps_ifa" id="id_pekerjaan_cc_hps_ifa" value="<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>" style="display: none">
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <label class="col-md-4">CC Dokumen</label>
              <select class="form-control col-md-12" id="cc_hps_ifa" name="cc_hps_ifa[]" multiple></select>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" id="close_cc_hps_ifa" class="btn btn-default" data-dismiss="modal" onclick="fun_close_cc_hps_ifa()">Close</button>
          <input type="button" class="btn btn-success pull-right" id="simpan_cc_hps_ifa" value="Simpan">
          <button class="btn btn-primary" type="button" id="loading_form_cc_hps_ifa" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- MODAL -->

<script type="text/javascript" src="<?= base_url('assets_tambahan/') ?>easyui/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?= base_url('assets_tambahan/') ?>easyui/jquery.edatagrid.js"></script>
<script type="text/javascript" src="<?= base_url('assets_tambahan/') ?>orgchart/orgchart.js"></script>

<script type="text/javascript">
  function cekRevisi() {
    $.getJSON("<?= base_url() ?>project/pekerjaan_usulan/cekRevisi", {
      role_id: role_id
    })
    .done(function(data) {

    })
    .fail(() => toastr.error('Gagal mengambil data!'));
  }

  // div tab dokumen
  setTimeout(function() {
    $.getJSON('<?= base_url('project/pekerjaan_usulan/getPekerjaan') ?>', {
      pekerjaan_id: "<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) ?>",
      pekerjaan_status: "<?= $this->input->get('status') ?>",
    }, function(json) {
      if (json.pekerjaan_disposisi_status == '5') {
        fun_cek_penomoran('<?= $this->input->get('pekerjaan_id') ?>');
      } else if (json.pekerjaan_disposisi_status == '8' || json.pekerjaan_disposisi_status == '9' || json.pekerjaan_disposisi_status == '10') {
        fun_cek_dokumen_ifa('<?= $this->input->get('pekerjaan_id') ?>', json.pekerjaan_disposisi_status);
      }
    }, 500);
  })

  function div_doc_usulan() {
    $('#div_doc_usulan').show();
    $('#div_doc_ifa').hide();
    $('#div_doc_ifa_hps').hide();
    $('#div_doc_ifc').hide();
    $('#div_doc_ifc_hps').hide();
    $('#div_doc_hps_selesai').hide();
    $('#div_doc_ifa_selesai').hide();
    $('#div_doc_ifc_selesai').hide();

    $('#link_div_doc_usulan').addClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifa').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifa_hps').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifc').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifc_hps').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifa_selesai').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifc_selesai').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_hps_selesai').removeClass('active bg-secondary bg-gradient');
  }

  function div_doc_ifa() {
    $('#div_doc_usulan').hide();
    $('#div_doc_ifa').show();
    $('#div_doc_ifa_hps').hide();
    $('#div_doc_ifc').hide();
    $('#div_doc_ifc_hps').hide();
    $('#div_doc_ifa_selesai').hide();
    $('#div_doc_ifc_selesai').hide();
    $('#div_doc_hps_selesai').hide();

    $('#link_div_doc_usulan').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifa').addClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifa_hps').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifc').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifc_hps').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifa_selesai').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifc_selesai').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_hps_selesai').removeClass('active bg-secondary bg-gradient');

  }

  function div_doc_ifa_hps() {
    $('#div_doc_usulan').hide();
    $('#div_doc_ifa').hide();
    $('#div_doc_ifa_hps').show();
    $('#div_doc_ifc').hide();
    $('#div_doc_ifc_hps').hide();
    $('#div_doc_ifa_selesai').hide();
    $('#div_doc_ifc_selesai').hide();
    $('#div_doc_hps_selesai').hide();

    $('#link_div_doc_usulan').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifa').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifa_hps').addClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifc').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifc_hps').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifa_selesai').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifc_selesai').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_hps_selesai').removeClass('active bg-secondary bg-gradient');
  }

  function div_doc_ifc() {
    $('#div_doc_usulan').hide();
    $('#div_doc_ifa').hide();
    $('#div_doc_ifa_hps').hide();
    $('#div_doc_ifc').show();
    $('#div_doc_ifc_hps').hide();
    $('#div_doc_ifa_selesai').hide();
    $('#div_doc_ifc_selesai').hide();
    $('#div_doc_hps_selesai').hide();

    $('#link_div_doc_usulan').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifa').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifa_hps').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifc').addClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifc_hps').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifa_selesai').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifc_selesai').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_hps_selesai').removeClass('active bg-secondary bg-gradient');
  }

  function div_doc_ifc_hps() {
    $('#div_doc_usulan').hide();
    $('#div_doc_ifa').hide();
    $('#div_doc_ifa_hps').hide();
    $('#div_doc_ifc').hide();
    $('#div_doc_ifc_hps').show();
    $('#div_doc_ifa_selesai').hide();
    $('#div_doc_ifc_selesai').hide();
    $('#div_doc_hps_selesai').hide();

    $('#link_div_doc_usulan').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifa').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifa_hps').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifc').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifc_hps').addClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifa_selesai').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifc_selesai').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_hps_selesai').removeClass('active bg-secondary bg-gradient');
  }

  function div_doc_ifa_selesai() {
    $('#div_doc_usulan').hide();
    $('#div_doc_ifa').hide();
    $('#div_doc_ifa_hps').hide();
    $('#div_doc_ifc').hide();
    $('#div_doc_ifc_hps').hide();
    $('#div_doc_ifa_selesai').show();
    $('#div_doc_ifc_selesai').hide();
    $('#div_doc_hps_selesai').hide();

    $('#link_div_doc_usulan').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifa').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifa_hps').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifc').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifc_hps').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifa_selesai').addClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifc_selesai').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_hps_selesai').removeClass('active bg-secondary bg-gradient');
  }

  function div_doc_ifc_selesai() {
    $('#div_doc_usulan').hide();
    $('#div_doc_ifa').hide();
    $('#div_doc_ifa_hps').hide();
    $('#div_doc_ifc').hide();
    $('#div_doc_ifc_hps').hide();
    $('#div_doc_ifa_selesai').hide();
    $('#div_doc_ifc_selesai').show();
    $('#div_doc_hps_selesai').hide();

    $('#link_div_doc_usulan').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifa').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifa_hps').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifc').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifc_hps').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifa_selesai').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifc_selesai').addClass('active bg-secondary bg-gradient');
    $('#link_div_doc_hps_selesai').removeClass('active bg-secondary bg-gradient');
  }


  function div_doc_hps_selesai() {
    $('#div_doc_usulan').hide();
    $('#div_doc_ifa').hide();
    $('#div_doc_ifa_hps').hide();
    $('#div_doc_ifc').hide();
    $('#div_doc_ifc_hps').hide();
    $('#div_doc_ifa_selesai').hide();
    $('#div_doc_ifc_selesai').hide();
    $('#div_doc_hps_selesai').show();

    $('#link_div_doc_usulan').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifa').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifa_hps').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifc').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifc_hps').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifa_selesai').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_ifc_selesai').removeClass('active bg-secondary bg-gradient');
    $('#link_div_doc_hps_selesai').addClass('active bg-secondary bg-gradient');
  }
  // div tab dokumen

  /* TAB */
  /* Klik Tab Home */
  function fun_div_home() {
    $('#div_home').show();
    $('#div_history').hide();
    $('#div_hirarki').hide();
    $('#link_div_home').addClass('active bg-secondary bg-gradient');
    $('#link_div_history').removeClass('active bg-secondary bg-gradient');
    $('#link_div_hirarki').removeClass('active bg-secondary bg-gradient');
  }
  /* Klik Tab Home */

  /* Klik Tab History */
  function fun_div_history() {
    $('#div_home').hide();
    $('#div_history').show();
    $('#div_hirarki').hide();
    $('#link_div_home').removeClass('active bg-secondary bg-gradient');
    $('#link_div_history').addClass('active bg-secondary bg-gradient');
    $('#link_div_hirarki').removeClass('active bg-secondary bg-gradient');
    $('#table_history').DataTable().ajax.reload();
  }
  /* Klik Tab History */

  /* Klik Tab Hirarki */
  function fun_div_hirarki() {
    $('#div_home').hide();
    $('#div_history').hide();
    $('#div_hirarki').show();
    $('#link_div_home').removeClass('active bg-secondary bg-gradient');
    $('#link_div_history').removeClass('active bg-secondary bg-gradient');
    $('#link_div_hirarki').addClass('active bg-secondary bg-gradient');
  }
  /* Klik Tab Hirarki */
  /* TAB */


  $(function() {
    // $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserSession') ?>', function(json) {
    // $('#session_user').val(json.pegawai_nik);
    // })

    // $.getJSON('<?= base_url('project/pekerjaan_usulan/getBagianSession') ?>', function(json) {
    // $('#session_bagian').val(json.bagian_id);
    // })

    $.getJSON('<?= base_url('project/pekerjaan_usulan/getPekerjaan') ?>', {
      pekerjaan_id: "<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>",
      pekerjaan_status: "<?= $_GET['status'] ?>"
    }, function(json) {
      if ('<?= $this->input->get('aksi') ?>' == 'usulan') {
        switch (json.pekerjaan_status) {
        case '1':
          $('#btn_reviewed, #btn_reject').show();
          $('#btn_approve, #btn_disposisi_vp, #btn_disposisi_avp, #btn_reject_avp, btn_ganti_koor').hide();
          break;
        case '2':
          $('#btn_reject, #btn_approve').show();
          $('#btn_reviewed, #btn_disposisi_vp, #btn_disposisi_avp, #btn_reject_avp, btn_ganti_koor').hide();
          break;
        case '3':
          $('#btn_disposisi_vp, #btn_reject').show();
          $('#btn_disposisi_avp, #btn_reviewed, #btn_approve, #btn_reject_avp, btn_ganti_koor').hide();
          break;
        case '4':
          if (json.is_proses != 'y') {
            if (json.id_penanggung_jawab == 'y') {
              $('#btn_disposisi_avp, #btn_reject_avp, #btn_ganti_koor').show();
              $('#btn_reviewed, #btn_approve, #btn_disposisi_vp, #btn_reject').hide();
            } else {
              $('#btn_disposisi_avp, #btn_reject_avp').show();
              $('#btn_reviewed, #btn_approve, #btn_disposisi_vp, #btn_reject, #btn_ganti_koor').hide();
            }
          } else {
            if (json.id_koor_baru != '' && json.id_penanggung_jawab == 'y') {
              $('#btn_ganti_koor').show();
              $('#btn_reviewed, #btn_approve, #btn_disposisi_vp, #btn_disposisi_avp, #btn_reject, #btn_reject_avp').hide();
            } else {
              $('#btn_reviewed, #btn_approve, #btn_disposisi_vp, #btn_disposisi_avp, #btn_reject, #btn_reject_avp, #btn_ganti_koor').hide();
            }
          }
          break;
        default:
          $('#btn_reviewed, #btn_approve, #btn_disposisi_vp, #btn_disposisi_avp, #btn_reject, #btn_reject_avp, #btn_ganti_koor').hide();
        }
      } else {
        switch (json.pekerjaan_status) {
        case '5':
          if ((json.pekerjaan_disposisi_status == '6' || json.disposisi_status_sekarang == '6')) {
            if (json.id_penanggung_jawab == 'y' || json.status_tanggung_jawab_sekarang == 'y') {
              if (json.is_proses == 'y') {
                  /* Tombol Atas */
                $('#btn_upload, #btn_upload_hps, #btn_progress, #btn_penomoran, #btn_nilai_hps, #btn_send_ifa, #btn_reject_staf, #btn_ganti_perencana, #btn_ganti_koor').hide();
                  /* Tombol Atas */

                  /* Tombol Bawah */
                $('#btn_send_avp, #btn_revisi, #btn_approve_dokumen_avp').show();
                $('#btn_send_koor, #btn_approve_vp').hide();
                  /* Tombol Bawah */
              } else {
                $('#btn_send_avp, #btn_revisi').show();
                $('#btn_upload, #btn_upload_hps, #btn_progress, #btn_penomoran, #btn_nilai_hps, #btn_send_ifa, #btn_send_koor, #btn_approve_vp, #btn_approve_dokumen_avp, #btn_reject_staf, #btn_ganti_perencana, #btn_ganti_koor').hide();
              }
            } else {
              if (json.is_proses == 'y') {
                  /* Tombol Atas */
                $('#btn_upload, #btn_upload_hps, #btn_progress, #btn_penomoran, #btn_nilai_hps, #btn_send_ifa, #btn_reject_staf, #btn_ganti_perencana, #btn_ganti_koor').hide();
                  /* Tombol Atas */

                  /* Tombol Bawah */
                $('#btn_send_avp_koor, #btn_revisi, #btn_send_avp, #btn_approve_vp, #btn_approve_dokumen_avp, #div_cek_proses_berjalan').hide();
                  /* Tombol Bawah */
              } else {
                  /* Tombol Atas */
                $('#btn_upload, #btn_upload_hps, #btn_progress, #btn_penomoran, #btn_nilai_hps, #btn_send_ifa, #btn_reject_staf, #btn_ganti_perencana, #btn_ganti_koor').hide();
                  /* Tombol Atas */

                  /* Tombol Bawah */
                $('#btn_send_avp_koor, #btn_revisi').show();
                $('#btn_send_avp, #btn_approve_vp, #btn_approve_dokumen_avp, #div_cek_proses_berjalan').hide();
                  /* Tombol Bawah */
              }
            }
          } else {
            if (json.is_proses != 'y') {
              if (json.id_penanggung_jawab == 'y') {
                  /* Tombol Atas */
                $('#btn_upload, #btn_upload_hps, #btn_progress, #btn_penomoran, #btn_nilai_hps, #btn_send_ifa, #btn_reject_staf, #btn_ganti_perencana, #btn_ganti_koor').show();
                  /* Tombol Atas */

                  /* Tombol Bawah */
                  /* Tombol Bawah */
                $('#btn_send_avp, #btn_send_avp_koor, #btn_approve_vp, #btn_revisi, #btn_approve_dokumen_avp, #div_cek_proses_berjalan').hide();
                  /* Tombol Bawah */
              } else {
                  /* Tombol Atas */
                $('#btn_upload, #btn_upload_hps, #btn_progress, #btn_send_ifa, #btn_reject_staf, #btn_ganti_perencana').show();
                $('#btn_penomoran, #btn_nilai_hps, #btn_ganti_koor').hide();
                  /* Tombol Atas */

                  /* Tombol Bawah */
                $('#btn_send_avp, #btn_send_avp_koor, #btn_approve_vp, #btn_revisi, #btn_approve_dokumen_avp, #div_cek_proses_berjalan').hide();
                  /* Tombol Bawah */
              }
            } else {
              $('#btn_upload, #btn_upload_hps, #btn_progress, #btn_penomoran, #btn_nilai_hps, #btn_send_ifa, #btn_send_avp, #btn_send_avp_koor, #btn_approve_vp, #btn_revisi, #btn_approve_dokumen_avp, #btn_reject_staf, #btn_ganti_perencana, #btn_ganti_koor, #div_cek_proses_berjalan').hide();
            }
          }
          break;
        case '6':
          if (json.is_proses != 'y' || json.is_proses != 'r') {
            if (json.id_penanggung_jawab == 'y') {
              $('#btn_send_avp, #btn_revisi, #btn_approve_dokumen_avp').show();
              $('#btn_upload, #btn_upload_hps, #btn_progress, #btn_penomoran, #btn_nilai_hps, #btn_send_ifa, #btn_send_avp_koor, #btn_approve_vp, #btn_reject_staf, #btn_ganti_perencana, #btn_ganti_koor').hide();
            } else {
              $('#btn_send_avp_koor, #btn_revisi').css('display', 'block');
              $('#btn_upload, #btn_upload_hps, #btn_progress, #btn_penomoran, #btn_nilai_hps, #btn_send_ifa, #btn_send_avp, #btn_approve_vp, #btn_approve_dokumen_avp, #btn_reject_staf, #btn_ganti_perencana, #btn_ganti_koor').hide();
            }
          } else {
            $('#btn_upload, #btn_upload_hps, #btn_progress, #btn_penomoran, #btn_nilai_hps, #btn_send_ifa, #btn_send_avp, #btn_send_avp_koor, #btn_approve_vp, #btn_revisi, #btn_approve_dokumen_avp, #btn_reject_staf, #btn_ganti_perencana, #btn_ganti_koor, #div_cek_proses_berjalan').hide();
          }
          break;
        case '7':
          $('#btn_approve_vp, #btn_revisi').css('display', 'block');
          $('#btn_upload, #btn_upload_hps, #btn_progress, #btn_penomoran, #btn_nilai_hps, #btn_send_ifa, #btn_send_avp, #btn_send_avp_koor, #btn_approve_dokumen_avp, #btn_reject_staf, #btn_ganti_perencana, #btn_ganti_koor').hide();
          break;
        case '8':
          if (json.id_user == '<?= $data_session['pegawai_nik'] ?>') {
            if (json.is_pic == 'y') {
              $('#btn_upload, #btn_upload_hps, #btn_progress, #btn_penomoran, #btn_nilai_hps, #btn_send_ifa, #btn_approve_ifa, #btn_revisi_ifa, #btn_reject_staf, #btn_ganti_perencana').hide();
            } else {
              $('#btn_cc_ifa').show();
              $('#btn_upload, #btn_upload_hps, #btn_progress, #btn_penomoran, #btn_nilai_hps, #btn_send_ifa, #btn_approve_ifa, #btn_revisi_ifa, #btn_reject_staf, #btn_ganti_perencana').hide();
            }
          } else {
            $('#btn_upload, #btn_upload_hps, #btn_progress, #btn_penomoran, #btn_nilai_hps, #btn_send_ifa, #btn_send_avp, #btn_send_avp_koor, #btn_approve_vp, #btn_revisi, #btn_approve_dokumen_avp, #btn_reject_staf, #btn_ganti_perencana, #btn_ganti_koor, #div_cek_proses_berjalan').hide();
          }
          break;
        case '9':
          $('#btn_upload, #btn_upload_hps, #btn_progress, #btn_penomoran, #btn_nilai_hps, #btn_send_ifa, #btn_send_avp, #btn_send_avp_koor, #btn_approve_vp, #btn_revisi, #btn_approve_dokumen_avp, #btn_reject_staf, #btn_ganti_perencana, #btn_ganti_koor, #div_cek_proses_berjalan').hide();
          break;
        case '10':
          $('#btn_upload, #btn_upload_hps, #btn_progress, #btn_penomoran, #btn_nilai_hps, #btn_send_ifa, #btn_send_avp, #btn_send_avp_koor, #btn_approve_vp, #btn_revisi, #btn_approve_dokumen_avp, #btn_reject_staf, #btn_ganti_perencana, #btn_ganti_koor, #div_cek_proses_berjalan').hide();
          break;
        case '11':
          if (json.is_proses != 'y') {
            if (json.pekerjaan_disposisi_status == '12' || json.disposisi_status_sekarang == '12') {
              if (json.id_penanggung_jawab == 'y' || json.status_tanggung_jawab_sekarang == 'y') {
                $('#btn_revisi, #btn_send_avp_ifc').show();
                $('#btn_upload_ifc, #btn_progress, #btn_send_ifa, #btn_approve_vp, #btn_reject_staf, #btn_ganti_perencana').hide();
              } else {
                $('#btn_send_avp_ifc_koor, #btn_revisi').show();
                $('#btn_upload_ifc, #btn_progress, #btn_send_ifa, #btn_approve_vp, #btn_reject_staf, #btn_ganti_perencana, #div_cek_proses').hide();
              }
            } else {
              $('#btn_upload_ifc, #btn_upload_ifc_hps').show();
              $('#btn_progress, #btn_send_ifa, #btn_send_avp, #btn_send_avp_koor, #btn_approve_vp, #btn_revisi, #btn_reject_staf, #btn_ganti_perencana, #div_cek_proses').hide();
            }
          } else {
            $('#btn_upload, #btn_upload_hps, #btn_progress, #btn_penomoran, #btn_nilai_hps, #btn_send_ifa, #btn_send_avp, #btn_send_avp_koor, #btn_approve_vp, #btn_revisi, #btn_approve_dokumen_avp, #btn_reject_staf, #btn_ganti_perencana, #btn_ganti_koor, #div_cek_proses_berjalan').hide();
          }
          break;
        case '12':
          if (json.is_proses != 'y') {
            if (json.id_penanggung_jawab == 'y' || json.status_tanggung_jawab_sekarang == 'y') {
              $('#btn_send_avp_ifc, #btn_revisi, #div_cek_proses').show();
              $('#btn_upload, #btn_progress, #btn_send_ifa, #btn_send_avp_ifc_koor, #btn_approve_vp, #btn_reject_staf, #btn_ganti_perencana').hide();
            } else {
              $('#btn_send_avp_ifc_koor, #btn_revisi').css('display', 'block');
              $('#btn_upload, #btn_progress, #btn_send_ifa, #btn_send_avp_ifc, #btn_approve_vp, #btn_reject_staf, #btn_ganti_perencana, #div_cek_proses').hide();
            }
          } else {
            $('#btn_upload, #btn_progress, #btn_send_ifa, #btn_send_avp_ifc, #btn_approve_vp, #btn_revisi, #btn_reject_staf, #btn_ganti_perencana').hide();
          }
          break;
        case '13':
          $('#btn_approve_vp, #btn_revisi').show();
          $('#btn_upload, #btn_progress, #btn_send_ifa, #btn_send_avp, #btn_send_avp_koor, #btn_reject_staf, #btn_ganti_perencana').hide();
          break;
        default:
          $('#btn_upload, #btn_upload_hps, #btn_progress, #btn_penomoran, #btn_nilai_hps, #btn_send_ifa, #btn_send_avp, #btn_send_avp_koor, #btn_approve_vp, #btn_revisi, #btn_approve_dokumen_avp, #btn_reject_staf, #btn_ganti_perencana, #btn_ganti_koor, #div_cek_proses_berjalan').hide();
        }
      }

      $('#pekerjaan_status').val(json.pekerjaan_status);
    });

    /* tombol approve all */
setTimeout(function() {
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getDokumenApprove') ?>', {
    pekerjaan_id: "<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) ?>",
    pekerjaan_status: "<?= $this->input->get('status') ?>",
    dokumen_status: '2'
  }, function(json) {
        // if (json.dokumen_belum_approve > 0 && json.penanggung_jawab == 'y') {
    if (json.dokumen_belum_approve > 0) {
      $('#btn_approve_dokumen_avp').show();
    } else {
      $('#btn_approve_dokumen_avp').hide();
    }
  }, 1000);
})

    /* Table Dokumen Pekerjaan Usulan */
$('#table_dokumen_usulan thead tr').clone(true).addClass('filters_table_dokumen_usulan').appendTo('#table_dokumen_usulan thead');
$('#table_dokumen_usulan').DataTable({
  orderCellsTop: true,
  initComplete: function() {
    var api = this.api();
    api.columns().eq(0).each(function(colIdx) {
      var cell = $('.filters_table_dokumen_usulan th').eq(
        $(api.column(colIdx).header()).index()
        );
      var title = $(cell).text();
      $(cell).html('<input type="text" class="form-control" style="width:100%" placeholder="' + title + '" />');

      $('input', $('.filters_table_dokumen_usulan th').eq($(api.column(colIdx).header()).index())).off('keyup change').on('keyup change', function(e) {
        e.stopPropagation();
        $(this).attr('title', $(this).val());
        var regexr = '({search})';
        var cursorPosition = this.selectionStart;

        api.column(colIdx).search(this.value != '' ? regexr.replace('{search}', '(((' + this.value + ')))') : '', this.value != '', this.value == '').draw();

        $(this).focus()[0].setSelectionRange(cursorPosition, cursorPosition);
      });
    });
  },
      // "dom": 'Bfrtip',
      // buttons: [{
      //   extend: 'copy',
      //   exportOptions: {
      //     columns: [0, 1]
      //   },
      // },
      // {
      //   extend: 'excel',
      //   exportOptions: {
      //     columns: [0, 1]
      //   },
      // },
      // {
      //   extend: 'csv',
      //   exportOptions: {
      //     columns: [0, 1]
      //   },
      // },
      // {
      //   extend: 'pdf',
      //   exportOptions: {
      //     columns: [0, 1]
      //   },
      // },
      // {
      //   extend: 'print',
      //   exportOptions: {
      //     columns: [0, 1]
      //   },
      // },
      // ],
      // select: {
      //   style: 'multi',
      // },
  "ajax": {
    "url": "<?= base_url('project/pekerjaan_usulan/') ?>getPekerjaanDokumen?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>",
    "dataSrc": ""
  },
  "columns": [
        // {
        //   data: null,
        //   defaultContent: '',
        //   orderable: false,
        //   className: 'select-checkbox',
        //   checkboxes: {
        //     'selectRow': true
        //   }
        // },
  {
    render: function(data, type, full, meta) {
      return '<center><input type="checkbox" id="cb_dokumen_usulan" class="cb_dokumen_usulan" name="cb_dokumen_usulan[]" value="' + full.pekerjaan_dokumen_id + '"></center>'
    }
  },
  {
    render: function(data, type, full, meta) {
      return meta.row + meta.settings._iDisplayStart + 1;
    }
  },
  {
    "data": "pekerjaan_dokumen_nama"
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_file + '" name="' + full.pekerjaan_dokumen_id + '" title="Lihat" onclick="fun_lihat(this.id, this.name)"><i class="bx bx-book m-0"></i></a></center>';
      }
    }
  }, {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_file + '~' + full.pekerjaan_dokumen_id + '" title="Download" onclick="fun_download_usulan(this.id,this.name)"><i class="fa fa-download"></i></a></center>';
      }
    }
  },
  ]
}).columns.adjust().draw();
    /* Table Dokumen Pekerjaan Usulan */

    /* Table Dokumen Pekerjaan Berjalan */
$('#table_dokumen thead tr').clone(true).addClass('filters_table_dokumen').appendTo('#table_dokumen thead');
$('#table_dokumen').DataTable({
  orderCellsTop: true,
  initComplete: function() {
    var api = this.api();
    api.columns().eq(0).each(function(colIdx) {
      var cell = $('.filters_table_dokumen th').eq(
        $(api.column(colIdx).header()).index()
        );
      var title = $(cell).text();
      $(cell).html('<input type="text" class="form-control" style="width:100%" placeholder="' + title + '" />');

      $('input', $('.filters_table_dokumen th').eq($(api.column(colIdx).header()).index())).off('keyup change').on('keyup change', function(e) {
        e.stopPropagation();
        $(this).attr('title', $(this).val());
        var regexr = '({search})';
        var cursorPosition = this.selectionStart;

        api.column(colIdx).search(this.value != '' ? regexr.replace('{search}', '(((' + this.value + ')))') : '', this.value != '', this.value == '').draw();

        $(this).focus()[0].setSelectionRange(cursorPosition, cursorPosition);
      });
    });
  },
      // "dom": 'Bfrtip',
      // buttons: [{
      //     extend: 'copy',
      //     exportOptions: {
      //       columns: [0, 1, 2, 3, 4, 5]
      //     },
      //   },
      //   {
      //     extend: 'excel',
      //     exportOptions: {
      //       columns: [0, 1, 2, 3, 4, 5]
      //     },
      //   },
      //   {
      //     extend: 'csv',
      //     exportOptions: {
      //       columns: [0, 1, 2, 3, 4, 5]
      //     },
      //   },
      //   {
      //     extend: 'pdf',
      //     exportOptions: {
      //       columns: [0, 1, 2, 3, 4, 5]
      //     },
      //   },
      //   {
      //     extend: 'print',
      //     exportOptions: {
      //       columns: [0, 1, 2, 3, 4, 5]
      //     },
      //   },
      // ],
      // 'select': {
      //   'style': 'multi'
      // },

  "ajax": {
    "url": "<?= base_url('project/pekerjaan_usulan/') ?>getDokumenBerjalan?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>&pekerjaan_status=<?= $_GET['status'] ?>&is_hps=n",
    "dataSrc": ""
  },


  "columns": [
        // {
        // data: null,
        // defaultContent: '',
        // orderable: false,
        // className: 'select-checkbox',
        // checkboxes: {
        // 'selectRow': true
        // },
        // },
  {
    render: function(data, type, full, meta) {
      return '<center><input type="checkbox" id="cb_dokumen" class="cb_dokumen" name="cb_dokumen[]" value="' + full.pekerjaan_dokumen_id + '"></center>'
    }
  },
  {
    render: function(data, type, full, meta) {
      return meta.row + 1;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_template_nama + ' - ' + full.pekerjaan_dokumen_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.bagian_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      var data = '';
      if (full.pekerjaan_dokumen_status == '0' && full.revisi_ifc == 'y' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Revisi';
      } else if (full.pekerjaan_dokumen_status == '0' && (full.revisi_ifc != 'y' && full.revisi_ifc == null) && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Revisi';
      } else if (full.pekerjaan_dokumen_status == '0') {
        var data = 'Revisi';
      } else if (full.pekerjaan_dokumen_status == '1' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Draft';
      } else if (full.pekerjaan_dokumen_status == '1') {
        var data = 'Draft';
      } else if (full.pekerjaan_dokumen_status == '2' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - IFA – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '2') {
        var data = 'IFA – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '3' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Menunggu Approve VP';
      } else if (full.pekerjaan_dokumen_status == '3' && full.avp_belum_review == 'y') {
        var data = 'IFA – Review By AVP';
      } else if (full.pekerjaan_dokumen_status == '3') {
        var data = 'IFA - Menunggu Approve VP';
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pic == 'y' || full.picavp == 'y' || full.picvp == 'y') && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Send User';
      } else if (full.pekerjaan_dokumen_status == '4' && full.vp_belum_approve == 'y') {
        var data = 'IFA – Approve By VP';
      } else if (full.pekerjaan_dokumen_status == '4') {
        var data = 'IFA - Send User';
      } else if (full.pekerjaan_dokumen_status == '5' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '5') {
        var data = 'IFA – Menunggu Review AVP User'
      } else if (full.pekerjaan_dokumen_status == '6' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Menunggu Approve VP User';
      } else if (full.pekerjaan_dokumen_status == '6') {
        var data = 'IFA - Menunggu Approve VP User'
      } else if (full.pekerjaan_dokumen_status == '7' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Approve VP User';
      } else if (full.pekerjaan_dokumen_status == '7') {
        var data = 'IFA - Approve VP User'
      } else if (full.pekerjaan_dokumen_status == '8') {
        var data = 'Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '9') {
        var data = 'IFC – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '10') {
        var data = 'IFC – Menunggu Approve VP'
      } else if (full.pekerjaan_dokumen_status == '11') {
        var data = 'IFC - Approved VP';
      } else if (full.pekerjaan_dokumen_status_review == '2') {
        var data = 'Review CC';
      } else {
        var data = '';
      }
      return data;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.who_create;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_dokumen_keterangan;
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_file + '" name="' + full.pekerjaan_dokumen_id + '" title="Lihat" onclick="fun_lihat(this.id, this.name)"><i class="bx bx-book m-0"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_file + '~' + full.pekerjaan_dokumen_id + '" title="Download" onclick="fun_download(this.id,this.name)"><i class="fa fa-download"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_nama + '" title="History" onclick="fun_history(this.id,this.name,`' + full.id_pekerjaan_template + '`,`' + full.is_hps + '`,`' + full.id_dokumen_awal + '`,`7`)"><i class="fa fa-history"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      var aksi = '';
      if (full.id_bagian == $('#session_bagian').val() && full.avp == 'y' && full.is_proses != 'y' && full.vp != 'y' && full.pekerjaan_dokumen_status == '2') {
        aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi(this.id)"><i class="fa fa-share"></i></a></center>';
      } else if (full.is_proses == 'y' && full.vp == 'y' && full.pekerjaan_dokumen_status != '0' && full.pekerjaan_dokumen_status == '3' && full.pekerjaan_status == '7') {
        aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi(this.id)"><i class="fa fa-share"></i></a></center>';
      } else if (full.vp == 'n' && full.cc == 'y' && full.is_review == null) {
        aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi_cc(this.id)"><i class="fa fa-share"></i></a></center>';
      } else {
        aksi = '<center> - </center>';
      }
      return aksi;
    }
  },
  ]
});

    /* Table Dokumen Pekerjaan Berjalan */

    /* Table Dokumen Pekerjaan Berjalan */
$('#table_dokumen_hps thead tr').clone(true).addClass('filters_table_dokumen_hps').appendTo('#table_dokumen_hps thead');
$('#table_dokumen_hps').DataTable({
  orderCellsTop: true,
  initComplete: function() {
    var api = this.api();
    api.columns().eq(0).each(function(colIdx) {
      var cell = $('.filters_table_dokumen_hps th').eq(
        $(api.column(colIdx).header()).index()
        );
      var title = $(cell).text();
      $(cell).html('<input type="text" class="form-control" style="width:100%" placeholder="' + title + '" />');

      $('input', $('.filters_table_dokumen_hps th').eq($(api.column(colIdx).header()).index())).off('keyup change').on('keyup change', function(e) {
        e.stopPropagation();
        $(this).attr('title', $(this).val());
        var regexr = '({search})';
        var cursorPosition = this.selectionStart;

        api.column(colIdx).search(this.value != '' ? regexr.replace('{search}', '(((' + this.value + ')))') : '', this.value != '', this.value == '').draw();

        $(this).focus()[0].setSelectionRange(cursorPosition, cursorPosition);
      });
    });
  },
      // "dom": 'Bfrtip',
      // buttons: [{
      //     extend: 'copy',
      //     exportOptions: {
      //       columns: [0, 1, 2, 3, 4, 5]
      //     },
      //   },
      //   {
      //     extend: 'excel',
      //     exportOptions: {
      //       columns: [0, 1, 2, 3, 4, 5]
      //     },
      //   },
      //   {
      //     extend: 'csv',
      //     exportOptions: {
      //       columns: [0, 1, 2, 3, 4, 5]
      //     },
      //   },
      //   {
      //     extend: 'pdf',
      //     exportOptions: {
      //       columns: [0, 1, 2, 3, 4, 5]
      //     },
      //   },
      //   {
      //     extend: 'print',
      //     exportOptions: {
      //       columns: [0, 1, 2, 3, 4, 5]
      //     },
      //   },
      // ],
      // select: {
      //   style: 'multi',
      // },
  "ajax": {
    "url": "<?= base_url('project/pekerjaan_usulan/') ?>getDokumenBerjalan?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>&pekerjaan_status=<?= $_GET['status'] ?>&is_hps=y",
    "dataSrc": ""
  },
  "columns": [
        // {
        //   data: null,
        //   defaultContent: '',
        //   orderable: false,
        //   className: 'select-checkbox',
        //   checkboxes: {
        //     'selectRow': true
        //   }
        // },
  {
    render: function(data, type, full, meta) {
      return '<center><input type="checkbox" id="cb_dokumen" class="cb_dokumen" name="cb_dokumen[]" value="' + full.pekerjaan_dokumen_id + '"></center>'
    }
  },
  {
    render: function(data, type, full, meta) {
      return meta.row + meta.settings._iDisplayStart + 1;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_template_nama + ' - ' + full.pekerjaan_dokumen_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.bagian_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      var data = '';
      if (full.pekerjaan_dokumen_status == '0' && full.revisi_ifc == 'y' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Revisi';
      } else if (full.pekerjaan_dokumen_status == '0' && (full.revisi_ifc != 'y' && full.revisi_ifc == null) && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Revisi';
      } else if (full.pekerjaan_dokumen_status == '0') {
        var data = 'Revisi';
      } else if (full.pekerjaan_dokumen_status == '1' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Draft';
      } else if (full.pekerjaan_dokumen_status == '1') {
        var data = 'Draft';
      } else if (full.pekerjaan_dokumen_status == '2' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - IFA – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '2') {
        var data = 'IFA – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '3' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Menunggu Approve VP';
      } else if (full.pekerjaan_dokumen_status == '3') {
        var data = 'IFA - Menunggu Approve VP';
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pic == 'y' || full.picavp == 'y' || full.picvp == 'y') && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Send User';
      } else if (full.pekerjaan_dokumen_status == '4') {
        var data = 'IFA - Send User';
      } else if (full.pekerjaan_dokumen_status == '5' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '5') {
        var data = 'IFA'
      } else if (full.pekerjaan_dokumen_status == '6' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Menunggu Approve VP User';
      } else if (full.pekerjaan_dokumen_status == '6') {
        var data = 'IFA - Menunggu Approve VP User'
      } else if (full.pekerjaan_dokumen_status == '7' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Approve VP User';
      } else if (full.pekerjaan_dokumen_status == '7') {
        var data = 'IFA - Approve VP User'
      } else if (full.pekerjaan_dokumen_status == '8') {
        var data = 'Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '9') {
        var data = 'IFC – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '10') {
        var data = 'IFC – Menunggu Approve VP'
      } else if (full.pekerjaan_dokumen_status == '11') {
        var data = 'IFC - Approved VP';
      } else if (full.pekerjaan_dokumen_status_review == '2') {
        var data = 'Review CC';
      } else {
        var data = '';
      }
      return data;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.who_create;
    }
  },
        // {
        // render: function(data, type, full, meta) {
        // return full.pegawai_nama;
        // }
        // },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_dokumen_keterangan;
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_file + '" name="' + full.pekerjaan_dokumen_id + '" title="Lihat" onclick="fun_lihat(this.id, this.name)"><i class="bx bx-book m-0"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_file + '~' + full.pekerjaan_dokumen_id + '" title="Download" onclick="fun_download(this.id,this.name)"><i class="fa fa-download"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_nama + '" title="History" onclick="fun_history(this.id,this.name,`' + full.id_pekerjaan_template + '`,`' + full.is_hps + '`,`' + full.id_dokumen_awal + '`,`7`)"><i class="fa fa-history"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      var aksi = '';
      if (full.id_bagian == $('#session_bagian').val() && full.avp == 'y' && full.is_proses != 'y' && full.vp != 'y' && full.pekerjaan_dokumen_status == '2') {
        aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi(this.id)"><i class="fa fa-share"></i></a></center>';
      } else if (full.is_proses == 'y' && full.vp == 'y' && full.pekerjaan_status == '7') {
        aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi(this.id)"><i class="fa fa-share"></i></a></center>';
      } else {
        aksi = '<center> - </center>';
      }
      return aksi;
    }
  },
  ]
});
    /* Table Dokumen Pekerjaan Berjalan */



    /* Table Dokumen IFA */
$('#table_dokumen_ifa thead tr').clone(true).addClass('filters_table_dokumen_ifa').appendTo('#table_dokumen_ifa thead');
$('#table_dokumen_ifa').DataTable({
  orderCellsTop: true,
  initComplete: function() {
    var api = this.api();
    api.columns().eq(0).each(function(colIdx) {
      var cell = $('.filters_table_dokumen_ifa th').eq(
        $(api.column(colIdx).header()).index()
        );
      var title = $(cell).text();
      $(cell).html('<input type="text" class="form-control" style="width:100%" placeholder="' + title + '" />');

      $('input', $('.filters_table_dokumen_ifa th').eq($(api.column(colIdx).header()).index())).off('keyup change').on('keyup change', function(e) {
        e.stopPropagation();
        $(this).attr('title', $(this).val());
        var regexr = '({search})';
        var cursorPosition = this.selectionStart;

        api.column(colIdx).search(this.value != '' ? regexr.replace('{search}', '(((' + this.value + ')))') : '', this.value != '', this.value == '').draw();

        $(this).focus()[0].setSelectionRange(cursorPosition, cursorPosition);
      });
    });
  },
      // "dom": 'Bfrtip',
      // buttons: [{
      //   extend: 'copy',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'excel',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'csv',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'pdf',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'print',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      // ],
      // select: {
      //  style: 'multi',
      // },
  "ajax": {
    "url": "<?= base_url('project/pekerjaan_usulan/') ?>getDokumenIFA?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>&pekerjaan_status=<?= $_GET['status'] ?>&is_hps=n",
    "dataSrc": ""
  },
  "columns": [
        // {
        //  data: null,
        //  defaultContent: '',
        //  orderable: false,
        //  className: 'select-checkbox',
        //  checkboxes: {
        //   'selectRow': true
        //  }
        // },
  {
    render: function(data, type, full, meta) {
      return '<center><input type="checkbox" id="cb_dokumen" class="cb_dokumen" name="cb_dokumen[]" value="' + full.pekerjaan_dokumen_id + '"></center>'
    }
  },
  {
    render: function(data, type, full, meta) {
      return meta.row + meta.settings._iDisplayStart + 1;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_template_nama + ' - ' + full.pekerjaan_dokumen_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.bagian_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      var data = '';
      if (full.pekerjaan_dokumen_status == '0' && full.revisi_ifc == 'y' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Revisi';
      } else if (full.pekerjaan_dokumen_status == '0' && (full.revisi_ifc != 'y' && full.revisi_ifc == null) && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Revisi';
      } else if (full.pekerjaan_dokumen_status == '0') {
        var data = 'Revisi';
      } else if (full.pekerjaan_dokumen_status == '1' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Draft';
      } else if (full.pekerjaan_dokumen_status == '1') {
        var data = 'Draft';
      } else if (full.pekerjaan_dokumen_status == '2' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - IFA – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '2') {
        var data = 'IFA – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '3' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Menunggu Approve VP';
      } else if (full.pekerjaan_dokumen_status == '3') {
        var data = 'IFA - Menunggu Approve VP';
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pic == 'y' || full.picavp == 'y' || full.picvp == 'y') && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Send User';
      } else if (full.pekerjaan_dokumen_status == '4') {
        var data = 'IFA - Send User';
      } else if (full.pekerjaan_dokumen_status == '5' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '5') {
        var data = 'IFA - Menunggu Review AVP User'
      } else if (full.pekerjaan_dokumen_status == '6' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Menunggu Approve VP User';
      } else if (full.pekerjaan_dokumen_status == '6') {
        var data = 'IFA - Menunggu Approve VP User'
      } else if (full.pekerjaan_dokumen_status == '7' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Approve VP User';
      } else if (full.pekerjaan_dokumen_status == '7') {
        var data = 'IFA - Approve VP User'
      } else if (full.pekerjaan_dokumen_status == '8') {
        var data = 'Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '9') {
        var data = 'IFC – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '10') {
        var data = 'IFC – Menunggu Approve VP'
      } else if (full.pekerjaan_dokumen_status == '11') {
        var data = 'IFC - Approved VP';
      } else if (full.pekerjaan_dokumen_status_review == '2') {
        var data = 'Review CC';
      } else {
        var data = '';
      }
      return data;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.who_create;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_dokumen_keterangan;
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_file + '" name="' + full.pekerjaan_dokumen_id + '" title="Lihat" onclick="fun_lihat(this.id, this.name)"><i class="bx bx-book m-0"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_file + '~' + full.pekerjaan_dokumen_id + '" title="Download" onclick="fun_download(this.id,this.name)"><i class="fa fa-download"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_nama + '" title="History" onclick="fun_history(this.id,this.name,`' + full.id_pekerjaan_template + '`,`' + full.is_hps + '`,`' + full.id_dokumen_awal + '`,`7`)"><i class="fa fa-history"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      var aksi = '';
      if (full.vp == 'n' && full.cc == 'y' && full.pic!='y') {
        aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi_cc(this.id)"><i class="fa fa-share"></i></a></center>';
      } else if (full.id_bagian == $('#session_bagian').val() && full.avp == 'y' && full.is_proses != 'y' && full.vp != 'y' && full.is_update_ifa == null) {
        aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi_ifa(this.id)"><i class="fa fa-share"></i></a></center>';
      } else if (full.is_proses == 'a' && full.vp == 'n' && full.is_hps == 'n') {
        aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi_ifa(this.id)"><i class="fa fa-share"></i></a></center>';
      } else if (full.is_proses == 'ifaavp' && full.is_hps == 'n' && full.picavp == 'y') {
        aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi_ifa(this.id)"><i class="fa fa-share"></i></a></center>';
      } else if (full.is_proses == 'ifavp' && full.is_hps == 'n' && full.picvp == 'y') {
        aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi_ifa(this.id)"><i class="fa fa-share"></i></a></center>';

      } else {
        aksi = '<center> - </center>';
      }
      return aksi;
    }
  },
  ]
});
    /* Table Dokumen IFA */

    /* Table Dokumen IFA HPS*/
$('#table_dokumen_ifa_hps thead tr').clone(true).addClass('filters_table_dokumen_ifa_hps').appendTo('#table_dokumen_ifa_hps thead');
$('#table_dokumen_ifa_hps').DataTable({
  orderCellsTop: true,
  initComplete: function() {
    var api = this.api();
    api.columns().eq(0).each(function(colIdx) {
      var cell = $('.filters_table_dokumen_ifa_hps th').eq(
        $(api.column(colIdx).header()).index()
        );
      var title = $(cell).text();
      $(cell).html('<input type="text" class="form-control" style="width:100%" placeholder="' + title + '" />');

      $('input', $('.filters_table_dokumen_ifa_hps th').eq($(api.column(colIdx).header()).index())).off('keyup change').on('keyup change', function(e) {
        e.stopPropagation();
        $(this).attr('title', $(this).val());
        var regexr = '({search})';
        var cursorPosition = this.selectionStart;

        api.column(colIdx).search(this.value != '' ? regexr.replace('{search}', '(((' + this.value + ')))') : '', this.value != '', this.value == '').draw();

        $(this).focus()[0].setSelectionRange(cursorPosition, cursorPosition);
      });
    });
  },
      // "dom": 'Bfrtip',
      // buttons: [{
      //   extend: 'copy',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'excel',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'csv',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'pdf',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'print',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      // ],
      // select: {
      //  style: 'multi',
      // },
  "ajax": {
    "url": "<?= base_url('project/pekerjaan_usulan/') ?>getDokumenIFAHPS?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>&pekerjaan_status=<?= $_GET['status'] ?>&is_hps=y",
    "dataSrc": ""
  },
  "columns": [
        // {
        //  data: null,
        //  defaultContent: '',
        //  orderable: false,
        //  className: 'select-checkbox',
        //  checkboxes: {
        //   'selectRow': true
        //  }
        // },
  {
    render: function(data, type, full, meta) {
      return '<center><input type="checkbox" id="cb_dokumen" class="cb_dokumen" name="cb_dokumen[]" value="' + full.pekerjaan_dokumen_id + '"></center>'
    }
  },
  {
    render: function(data, type, full, meta) {
      return meta.row + meta.settings._iDisplayStart + 1;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_template_nama + ' - ' + full.pekerjaan_dokumen_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.bagian_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      var data = '';
      if (full.pekerjaan_dokumen_status == '0' && full.revisi_ifc == 'y' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Revisi';
      } else if (full.pekerjaan_dokumen_status == '0' && (full.revisi_ifc != 'y' && full.revisi_ifc == null) && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Revisi';
      } else if (full.pekerjaan_dokumen_status == '0') {
        var data = 'Revisi';
      } else if (full.pekerjaan_dokumen_status == '1' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Draft';
      } else if (full.pekerjaan_dokumen_status == '1') {
        var data = 'Draft';
      } else if (full.pekerjaan_dokumen_status == '2' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - IFA – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '2') {
        var data = 'IFA – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '3' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Menunggu Approve VP';
      } else if (full.pekerjaan_dokumen_status == '3') {
        var data = 'IFA - Menunggu Approve VP';
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pic == 'y' || full.picavp == 'y' || full.picvp == 'y') && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Send User';
      } else if (full.pekerjaan_dokumen_status == '4') {
        var data = 'IFA - Send User';
      } else if (full.pekerjaan_dokumen_status == '5' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '5') {
        var data = 'IFA'
      } else if (full.pekerjaan_dokumen_status == '6' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Menunggu Approve VP User';
      } else if (full.pekerjaan_dokumen_status == '6') {
        var data = 'IFA - Menunggu Approve VP User'
      } else if (full.pekerjaan_dokumen_status == '7' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Approve VP User';
      } else if (full.pekerjaan_dokumen_status == '7') {
        var data = 'IFA - Approve VP User'
      } else if (full.pekerjaan_dokumen_status == '8') {
        var data = 'Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '9') {
        var data = 'IFC – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '10') {
        var data = 'IFC – Menunggu Approve VP'
      } else if (full.pekerjaan_dokumen_status == '11') {
        var data = 'IFC - Approved VP';
      } else if (full.pekerjaan_dokumen_status_review == '2') {
        var data = 'Review CC';
      } else {
        var data = '';
      }
      return data;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.who_create;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_dokumen_keterangan;
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_file + '" name="' + full.pekerjaan_dokumen_id + '" title="Lihat" onclick="fun_lihat(this.id, this.name)"><i class="bx bx-book m-0"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_file + '~' + full.pekerjaan_dokumen_id + '" title="Download" onclick="fun_download(this.id,this.name)"><i class="fa fa-download"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '')
        return '<center>-</center>'
      else if (parseInt(full.pekerjaan_dokumen_status) <= '7')
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_nama + '" title="History" onclick="fun_history(this.id,this.name,`' + full.id_pekerjaan_template + '`,`' + full.is_hps + '`,`' + full.id_dokumen_awal + '`,`7`)"><i class="fa fa-history"></i></a></center>'
      else if (parseInt(full.pekerjaan_dokumen_status) >= '8')
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_nama + '" title="History" onclick="fun_history(this.id,this.name,`' + full.id_pekerjaan_template + '`,`' + full.is_hps + '`,`' + full.id_dokumen_awal + '`,`11`)"><i class="fa fa-history"></i></a></center>'
      else
        return '<center>-</cemter>'

    }
  },
  {
    "render": function(data, type, full, meta) {
      var aksi = '';
      if (full.id_bagian == $('#session_bagian').val() && full.avp == 'y' && full.is_proses != 'y' && full.vp != 'y')
        aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi_ifc(this.id)"><i class="fa fa-share"></i></a></center>'
      else if (full.is_proses == 'y' && full.vp == 'y' && full.avp == 'n' && full.pekerjaan_dokumen_status == 10)
        aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi_ifc(this.id)"><i class="fa fa-share"></i></a></center>'
      else
        aksi = '<center> - </center>';

      return aksi;
    }
  },
  ]
});
    /* Table Dokumen IFA HPS*/

    /* Table Dokumen IFC */
$('#table_dokumen_ifc thead tr').clone(true).addClass('filters_table_dokumen_ifc').appendTo('#table_dokumen_ifc thead');
$('#table_dokumen_ifc').DataTable({
  orderCellsTop: true,
  initComplete: function() {
    var api = this.api();
    api.columns().eq(0).each(function(colIdx) {
      var cell = $('.filters_table_dokumen_ifc th').eq(
        $(api.column(colIdx).header()).index()
        );
      var title = $(cell).text();
      $(cell).html('<input type="text" class="form-control" style="width:100%" placeholder="' + title + '" />');

      $('input', $('.filters_table_dokumen_ifc th').eq($(api.column(colIdx).header()).index())).off('keyup change').on('keyup change', function(e) {
        e.stopPropagation();
        $(this).attr('title', $(this).val());
        var regexr = '({search})';
        var cursorPosition = this.selectionStart;

        api.column(colIdx).search(this.value != '' ? regexr.replace('{search}', '(((' + this.value + ')))') : '', this.value != '', this.value == '').draw();

        $(this).focus()[0].setSelectionRange(cursorPosition, cursorPosition);
      });
    });
  },
      // "dom": 'Bfrtip',
      // buttons: [{
      //   extend: 'copy',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'excel',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'csv',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'pdf',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'print',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      // ],
      // select: {
      //  style: 'multi',
      // },
  "ajax": {
    "url": "<?= base_url('project/pekerjaan_usulan/') ?>getDokumenIFC?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>&pekerjaan_status=<?= $_GET['status'] ?>&is_hps=n",
    "dataSrc": ""
  },
  "columns": [
        // {
        //  data: null,
        //  defaultContent: '',
        //  orderable: false,
        //  className: 'select-checkbox',
        //  checkboxes: {
        //   'selectRow': true
        //  }
        // },
  {
    render: function(data, type, full, meta) {
      return '<center><input type="checkbox" id="cb_dokumen" class="cb_dokumen" name="cb_dokumen[]" value="' + full.pekerjaan_dokumen_id + '"></center>'
    }
  },
  {
    render: function(data, type, full, meta) {
      return meta.row + meta.settings._iDisplayStart + 1;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_template_nama + ' - ' + full.pekerjaan_dokumen_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.bagian_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      var data = '';
      if (full.pekerjaan_dokumen_status == '0' && full.revisi_ifc == 'y' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Revisi';
      } else if (full.pekerjaan_dokumen_status == '0' && (full.revisi_ifc != 'y' && full.revisi_ifc == null) && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Revisi';
      } else if (full.pekerjaan_dokumen_status == '0') {
        var data = 'Revisi';
      } else if (full.pekerjaan_dokumen_status == '1' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Draft';
      } else if (full.pekerjaan_dokumen_status == '1') {
        var data = 'Draft';
      } else if (full.pekerjaan_dokumen_status == '2' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - IFA – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '2') {
        var data = 'IFA – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '3' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Menunggu Approve VP';
      } else if (full.pekerjaan_dokumen_status == '3') {
        var data = 'IFA - Menunggu Approve VP';
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pic == 'y' || full.picavp == 'y' || full.picvp == 'y') && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Send User';
      } else if (full.pekerjaan_dokumen_status == '4') {
        var data = 'IFA - Send User';
      } else if (full.pekerjaan_dokumen_status == '5' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '5') {
        var data = 'IFA – Menunggu Review AVP User'
      } else if (full.pekerjaan_dokumen_status == '6' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Menunggu Approve VP User';
      } else if (full.pekerjaan_dokumen_status == '6') {
        var data = 'IFA - Menunggu Approve VP User'
      } else if (full.pekerjaan_dokumen_status == '7' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Approve VP User';
      } else if (full.pekerjaan_dokumen_status == '7') {
        var data = 'IFA - Approve VP User'
      } else if (full.pekerjaan_dokumen_status == '8') {
        var data = 'Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '9') {
        var data = 'IFC – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '10') {
        var data = 'IFC – Menunggu Approve VP'
      } else if (full.pekerjaan_dokumen_status == '11') {
        var data = 'IFC - Approved VP';
      } else if (full.pekerjaan_dokumen_status_review == '2') {
        var data = 'Review CC';
      } else {
        var data = '';
      }
      return data;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.who_create;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_dokumen_keterangan;
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_file + '" name="' + full.pekerjaan_dokumen_id + '" title="Lihat" onclick="fun_lihat(this.id, this.name)"><i class="bx bx-book m-0"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_file + '~' + full.pekerjaan_dokumen_id + '" title="Download" onclick="fun_download(this.id,this.name)"><i class="fa fa-download"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_nama + '" title="History" onclick="fun_history(this.id,this.name,`' + full.id_pekerjaan_template + '`,`' + full.is_hps + '`,`' + full.id_dokumen_awal + '`,`11`)"><i class="fa fa-history"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      var aksi = '';
      if (full.id_bagian == $('#session_bagian').val() && full.avp == 'y' && full.is_proses != 'y' && full.vp != 'y') {
        aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi_ifc(this.id)"><i class="fa fa-share"></i></a></center>';
      } else if (full.is_proses == 'y' && full.vp == 'y' && full.pekerjaan_dokumen_status >= 7 && full.pekerjaan_dokumen_status < 11) {
        aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi_ifc(this.id)"><i class="fa fa-share"></i></a></center>';

              // } else if (full.vp == 'n' && full.cc == 'y' && full.is_review == null) {
              //   aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi_cc(this.id)"><i class="fa fa-share"></i></a></center>';

      } else {
        aksi = '<center> - </center>';
      }
      return aksi;
    }
  },
  ]
});
    /* Table Dokumen IFC */

    /* Table Dokumen IFC HPS */
$('#table_dokumen_ifc_hps').DataTable({
      // "dom": 'Bfrtip',
      // buttons: [{
      //   extend: 'copy',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'excel',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'csv',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'pdf',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'print',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      // ],
      // select: {
      //  style: 'multi',
      // },
  "ajax": {
    "url": "<?= base_url('project/pekerjaan_usulan/') ?>getDokumenIFC?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>&pekerjaan_status=<?= $_GET['status'] ?>&is_hps=y",
    "dataSrc": ""
  },
  "columns": [
        // {
        //  data: null,
        //  defaultContent: '',
        //  orderable: false,
        //  className: 'select-checkbox',
        //  checkboxes: {
        //   'selectRow': true
        //  }
        // },
  {
    render: function(data, type, full, meta) {
      return '<center><input type="checkbox" id="cb_dokumen" class="cb_dokumen" name="cb_dokumen[]" value="' + full.pekerjaan_dokumen_id + '"></center>'
    }
  },
  {
    render: function(data, type, full, meta) {
      return meta.row + meta.settings._iDisplayStart + 1;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_template_nama + ' - ' + full.pekerjaan_dokumen_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.bagian_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      var data = '';
      if (full.pekerjaan_dokumen_status == '0' && full.revisi_ifc == 'y' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Revisi';
      } else if (full.pekerjaan_dokumen_status == '0' && (full.revisi_ifc != 'y' && full.revisi_ifc == null) && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Revisi';
      } else if (full.pekerjaan_dokumen_status == '0') {
        var data = 'Revisi';
      } else if (full.pekerjaan_dokumen_status == '1' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Draft';
      } else if (full.pekerjaan_dokumen_status == '1') {
        var data = 'Draft';
      } else if (full.pekerjaan_dokumen_status == '2' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - IFA – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '2') {
        var data = 'IFA – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '3' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Menunggu Approve VP';
      } else if (full.pekerjaan_dokumen_status == '3') {
        var data = 'IFA - Menunggu Approve VP';
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pic == 'y' || full.picavp == 'y' || full.picvp == 'y') && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Send User';
      } else if (full.pekerjaan_dokumen_status == '4') {
        var data = 'IFA - Send User';
      } else if (full.pekerjaan_dokumen_status == '5' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '5') {
        var data = 'IFA'
      } else if (full.pekerjaan_dokumen_status == '6' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Menunggu Approve VP User';
      } else if (full.pekerjaan_dokumen_status == '6') {
        var data = 'IFA - Menunggu Approve VP User'
      } else if (full.pekerjaan_dokumen_status == '7' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Approve VP User';
      } else if (full.pekerjaan_dokumen_status == '7') {
        var data = 'IFA - Approve VP User'
      } else if (full.pekerjaan_dokumen_status == '8') {
        var data = 'Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '9') {
        var data = 'IFC – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '10') {
        var data = 'IFC – Menunggu Approve VP'
      } else if (full.pekerjaan_dokumen_status == '11') {
        var data = 'IFC - Approved VP';
      } else if (full.pekerjaan_dokumen_status_review == '2') {
        var data = 'Review CC';
      } else {
        var data = '';
      }
      return data;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.who_create;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_dokumen_keterangan;
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_file + '" name="' + full.pekerjaan_dokumen_id + '" title="Lihat" onclick="fun_lihat(this.id, this.name)"><i class="bx bx-book m-0"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_file + '~' + full.pekerjaan_dokumen_id + '" title="Download" onclick="fun_download(this.id,this.name)"><i class="fa fa-download"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_nama + '" title="History" onclick="fun_history(this.id,this.name,`' + full.id_pekerjaan_template + '`,`' + full.is_hps + '`,`' + full.id_dokumen_awal + '`,`11`)"><i class="fa fa-history"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      var aksi = '';
      if (full.id_bagian == $('#session_bagian').val() && full.avp == 'y' && full.is_proses != 'y' && full.vp != 'y') {
        aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi(this.id)"><i class="fa fa-share"></i></a></center>';
      } else if (full.is_proses == 'y' && full.vp == 'y' &&
        full.pekerjaan_dokumen_status >= 7) {
        aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi(this.id)"><i class="fa fa-share"></i></a></center>';
      } else {
        aksi = '<center> - </center>';
      }
      return aksi;
    }
  },
  ]
});
    /* Table Dokumen IFC HPS */

$('#table_dokumen_selesai_ifa').DataTable({
      // select: {
      //  style: 'multi',
      // },
      // "dom": 'Bfrtip',
      // buttons: [{
      //   extend: 'copy',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'excel',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'csv',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'pdf',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'print',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      // ],
  "ajax": {
    "url": "<?= base_url('project/pekerjaan_usulan/') ?>getDokumenSelesaiIFA?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>&pekerjaan_status=<?= $_GET['status'] ?>&is_hps=n",
    "dataSrc": ""
  },
  "columns": [
        // {
        //  data: null,
        //  defaultContent: '',
        //  orderable: false,
        //  className: 'select-checkbox',
        //  checkboxes: {
        //   'selectRow': true
        //  }
        // },
  {
    render: function(data, type, full, meta) {
      return '<center><input type="checkbox" id="cb_dokumen" class="cb_dokumen" name="cb_dokumen[]" value="' + full.pekerjaan_dokumen_id + '"></center>'
    }
  },
  {
    render: function(data, type, full, meta) {
      return meta.row + meta.settings._iDisplayStart + 1;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_template_nama + ' - ' + full.pekerjaan_dokumen_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.bagian_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      var data = '';
      if (full.pekerjaan_dokumen_status == '0' && full.revisi_ifc == 'y' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Revisi';
      } else if (full.pekerjaan_dokumen_status == '0' && (full.revisi_ifc != 'y' && full.revisi_ifc == null) && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Revisi';
      } else if (full.pekerjaan_dokumen_status == '0') {
        var data = 'Revisi';
      } else if (full.pekerjaan_dokumen_status == '1' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Draft';
      } else if (full.pekerjaan_dokumen_status == '1') {
        var data = 'Draft';
      } else if (full.pekerjaan_dokumen_status == '2' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - IFA – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '2') {
        var data = 'IFA – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '3' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Menunggu Approve VP';
      } else if (full.pekerjaan_dokumen_status == '3') {
        var data = 'IFA - Menunggu Approve VP';
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pic == 'y' || full.picavp == 'y' || full.picvp == 'y') && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Send User';
      } else if (full.pekerjaan_dokumen_status == '4') {
        var data = 'IFA - Send User';
      } else if (full.pekerjaan_dokumen_status == '5' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '5') {
        var data = 'IFA – Menunggu Review AVP User'
      } else if (full.pekerjaan_dokumen_status == '6' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Menunggu Approve VP User';
      } else if (full.pekerjaan_dokumen_status == '6') {
        var data = 'IFA - Menunggu Approve VP User'
      } else if (full.pekerjaan_dokumen_status == '7' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Approve VP User';
      } else if (full.pekerjaan_dokumen_status == '7') {
        var data = 'IFA - Approve VP User'
      } else if (full.pekerjaan_dokumen_status == '8') {
        var data = 'Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '9') {
        var data = 'IFC – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '10') {
        var data = 'IFC – Menunggu Approve VP'
      } else if (full.pekerjaan_dokumen_status == '11') {
        var data = 'IFC - Approved VP';
      } else if (full.pekerjaan_dokumen_status_review == '2') {
        var data = 'Review CC';
      } else {
        var data = '';
      }
      return data;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.who_create;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_dokumen_keterangan;
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_file + '" name="' + full.pekerjaan_dokumen_id + '" title="Lihat" onclick="fun_lihat(this.id, this.name)"><i class="bx bx-book m-0"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_file + '~' + full.pekerjaan_dokumen_id + '" title="Download" onclick="fun_download(this.id,this.name)"><i class="fa fa-download"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_nama + '" title="History" onclick="fun_history(this.id,this.name,`' + full.id_pekerjaan_template + '`,`' + full.is_hps + '`,`' + full.id_dokumen_awal + '`,`7`)"><i class="fa fa-history"></i></a></center>';
      }
    }
  },

  ]
});
    /* Table Dokumen Pekerjaan Selesai */

    /* Table Dokumen Pekerjaan Selesai */
$('#table_dokumen_selesai').DataTable({
      // select: {
      //  style: 'multi',
      // },
      // "dom": 'Bfrtip',
      // buttons: [{
      //   extend: 'copy',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'excel',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'csv',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'pdf',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'print',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      // ],
  "ajax": {
    "url": "<?= base_url('project/pekerjaan_usulan/') ?>getDokumenSelesai?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>&pekerjaan_status=<?= $_GET['status'] ?>&is_hps=n",
    "dataSrc": ""
  },
  "columns": [
        // {
        //  data: null,
        //  defaultContent: '',
        //  orderable: false,
        //  className: 'select-checkbox',
        //  checkboxes: {
        //   'selectRow': true
        //  }
        // },
  {
    render: function(data, type, full, meta) {
      return '<center><input type="checkbox" id="cb_dokumen" class="cb_dokumen" name="cb_dokumen[]" value="' + full.pekerjaan_dokumen_id + '"></center>'
    }
  },
  {
    render: function(data, type, full, meta) {
      return meta.row + meta.settings._iDisplayStart + 1;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_template_nama + ' - ' + full.pekerjaan_dokumen_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.bagian_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      var data = '';
      if (full.pekerjaan_dokumen_status == '0' && full.revisi_ifc == 'y' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Revisi';
      } else if (full.pekerjaan_dokumen_status == '0' && (full.revisi_ifc != 'y' && full.revisi_ifc == null) && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Revisi';
      } else if (full.pekerjaan_dokumen_status == '0') {
        var data = 'Revisi';
      } else if (full.pekerjaan_dokumen_status == '1' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Draft';
      } else if (full.pekerjaan_dokumen_status == '1') {
        var data = 'Draft';
      } else if (full.pekerjaan_dokumen_status == '2' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - IFA – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '2') {
        var data = 'IFA – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '3' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Menunggu Approve VP';
      } else if (full.pekerjaan_dokumen_status == '3') {
        var data = 'IFA - Menunggu Approve VP';
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pic == 'y' || full.picavp == 'y' || full.picvp == 'y') && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Send User';
      } else if (full.pekerjaan_dokumen_status == '4') {
        var data = 'IFA - Send User';
      } else if (full.pekerjaan_dokumen_status == '5' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '5') {
        var data = 'IFA – Menunggu Review AVP User'
      } else if (full.pekerjaan_dokumen_status == '6' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Menunggu Approve VP User';
      } else if (full.pekerjaan_dokumen_status == '6') {
        var data = 'IFA - Menunggu Approve VP User'
      } else if (full.pekerjaan_dokumen_status == '7' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Approve VP User';
      } else if (full.pekerjaan_dokumen_status == '7') {
        var data = 'IFA - Approve VP User'
      } else if (full.pekerjaan_dokumen_status == '8') {
        var data = 'Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '9') {
        var data = 'IFC – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '10') {
        var data = 'IFC – Menunggu Approve VP'
      } else if (full.pekerjaan_dokumen_status == '11') {
        var data = 'IFC - Approved VP';
      } else if (full.pekerjaan_dokumen_status_review == '2') {
        var data = 'Review CC';
      } else {
        var data = '';
      }
      return data;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.who_create;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_dokumen_keterangan;
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_file + '" name="' + full.pekerjaan_dokumen_id + '" title="Lihat" onclick="fun_lihat(this.id, this.name)"><i class="bx bx-book m-0"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_file + '~' + full.pekerjaan_dokumen_id + '" title="Download" onclick="fun_download(this.id,this.name)"><i class="fa fa-download"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_nama + '" title="History" onclick="fun_history(this.id,this.name,`' + full.id_pekerjaan_template + '`,`' + full.is_hps + '`,`' + full.id_dokumen_awal + '`,`11`)"><i class="fa fa-history"></i></a></center>';
      }
    }
  },

  ]
});
    /* Table Dokumen Pekerjaan Selesai */

    /* Table Dokumen Pekerjaan Selesai */
$('#table_dokumen_selesai_hps').DataTable({
      // select: {
      //  style: 'multi',
      // },
      // "dom": 'Bfrtip',
      // buttons: [{
      //   extend: 'copy',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'excel',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'csv',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'pdf',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      //  {
      //   extend: 'print',
      //   exportOptions: {
      //    columns: [0, 1, 2, 3, 4, 5]
      //   },
      //  },
      // ],
  "ajax": {
    "url": "<?= base_url('project/pekerjaan_usulan/') ?>getDokumenSelesaiHPS?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>&pekerjaan_status=<?= $_GET['status'] ?>&is_hps=y",
    "dataSrc": ""
  },
  "columns": [
        // {
        //  data: null,
        //  defaultContent: '',
        //  orderable: false,
        //  className: 'select-checkbox',
        //  checkboxes: {
        //   'selectRow': true
        //  }
        // },
  {
    render: function(data, type, full, meta) {
      return '<center><input type="checkbox" id="cb_dokumen" class="cb_dokumen" name="cb_dokumen[]" value="' + full.pekerjaan_dokumen_id + '"></center>'
    }
  },
  {
    render: function(data, type, full, meta) {
      return meta.row + meta.settings._iDisplayStart + 1;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_template_nama + ' - ' + full.pekerjaan_dokumen_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.bagian_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      var data = '';
      if (full.pekerjaan_dokumen_status == '0' && full.revisi_ifc == 'y' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Revisi';
      } else if (full.pekerjaan_dokumen_status == '0' && (full.revisi_ifc != 'y' && full.revisi_ifc == null) && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Revisi';
      } else if (full.pekerjaan_dokumen_status == '0') {
        var data = 'Revisi';
      } else if (full.pekerjaan_dokumen_status == '1' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Draft';
      } else if (full.pekerjaan_dokumen_status == '1') {
        var data = 'Draft';
      } else if (full.pekerjaan_dokumen_status == '2' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - IFA – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '2') {
        var data = 'IFA – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '3' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Menunggu Approve VP';
      } else if (full.pekerjaan_dokumen_status == '3') {
        var data = 'IFA - Menunggu Approve VP';
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pic == 'y' || full.picavp == 'y' || full.picvp == 'y') && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Send User';
      } else if (full.pekerjaan_dokumen_status == '4') {
        var data = 'IFA - Send User';
      } else if (full.pekerjaan_dokumen_status == '5' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '5') {
        var data = 'IFA'
      } else if (full.pekerjaan_dokumen_status == '6' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Menunggu Approve VP User';
      } else if (full.pekerjaan_dokumen_status == '6') {
        var data = 'IFA - Menunggu Approve VP User'
      } else if (full.pekerjaan_dokumen_status == '7' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Approve VP User';
      } else if (full.pekerjaan_dokumen_status == '7') {
        var data = 'IFA - Approve VP User'
      } else if (full.pekerjaan_dokumen_status == '8') {
        var data = 'Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '9') {
        var data = 'IFC – Menunggu Review AVP';
      } else if (full.pekerjaan_dokumen_status == '10') {
        var data = 'IFC – Menunggu Approve VP'
      } else if (full.pekerjaan_dokumen_status == '11') {
        var data = 'IFC - Approved VP';
      } else if (full.pekerjaan_dokumen_status_review == '2') {
        var data = 'Review CC';
      } else {
        var data = '';
      }
      return data;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.who_create;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_dokumen_keterangan;
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_file + '" name="' + full.pekerjaan_dokumen_id + '" title="Lihat" onclick="fun_lihat(this.id, this.name)"><i class="bx bx-book m-0"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_file + '~' + full.pekerjaan_dokumen_id + '" title="Download" onclick="fun_download(this.id,this.name)"><i class="fa fa-download"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_nama + '" title="History" onclick="fun_history(this.id,this.name,`' + full.id_pekerjaan_template + '`,`' + full.is_hps + '`,`' + full.id_dokumen_awal + '`,`11`)"><i class="fa fa-history"></i></a></center>';
      }
    }
  },

  ]
});
    /* Table Dokumen Pekerjaan Selesai */

    /*Table History Dokumen*/
$('#table_dokumen_history').DataTable({
  "ajax": {
    "url": "<?= base_url('project/pekerjaan_usulan/') ?>getAsetDocumentHistory?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>&pekerjaan_dokumen_nama=0&id_pekerjaan_template=0&pekerjaan_dokumen_status=0",
    "dataSrc": ""
  },
  "columns": [{
    render: function(data, type, full, meta) {
      return meta.row + meta.settings._iDisplayStart + 1;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_template_nama + ' - ' + full.pekerjaan_dokumen_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      var data = '';
      if (full.is_review == 'y') {
        data = 'Checked BY CC';
      } else {
        if (full.pekerjaan_dokumen_status == '0' && full.revisi_ifc == 'y' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
          var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Revisi';
        } else if (full.pekerjaan_dokumen_status == '0' && (full.revisi_ifc != 'y' && full.revisi_ifc == null) && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
          var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Revisi';
        } else if (full.pekerjaan_dokumen_status == '0') {
          var data = 'Revisi';
        } else if (full.pekerjaan_dokumen_status == '1' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
          var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Draft';
        } else if (full.pekerjaan_dokumen_status == '1') {
          var data = 'Draft';
        } else if (full.pekerjaan_dokumen_status == '2' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
          var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - IFA – Menunggu Review AVP';
        } else if (full.pekerjaan_dokumen_status == '2') {
          var data = 'IFA – Menunggu Review AVP';
        } else if (full.pekerjaan_dokumen_status == '3' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
          var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Menunggu Approve VP';
        } else if (full.pekerjaan_dokumen_status == '3') {
          var data = 'IFA - Menunggu Approve VP';
        } else if (full.pekerjaan_dokumen_status == '4' && (full.pic == 'y' || full.picavp == 'y' || full.picvp == 'y') && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
          var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
        } else if (full.pekerjaan_dokumen_status == '4' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
          var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Send User';
        } else if (full.pekerjaan_dokumen_status == '4') {
          var data = 'IFA - Send User';
        } else if (full.pekerjaan_dokumen_status == '5' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
          var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
        } else if (full.pekerjaan_dokumen_status == '5') {
          var data = 'IFA – Menunggu Review AVP User'
        } else if (full.pekerjaan_dokumen_status == '6' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
          var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Menunggu Approve VP User';
        } else if (full.pekerjaan_dokumen_status == '6') {
          var data = 'IFA - Menunggu Approve VP User'
        } else if (full.pekerjaan_dokumen_status == '7' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
          var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Approve VP User';
        } else if (full.pekerjaan_dokumen_status == '7') {
          var data = 'IFA - Approve VP User'
        } else if (full.pekerjaan_dokumen_status == '8') {
          var data = 'Draft IFC';
        } else if (full.pekerjaan_dokumen_status == '9') {
          var data = 'IFC – Menunggu Review AVP';
        } else if (full.pekerjaan_dokumen_status == '10') {
          var data = 'IFC – Menunggu Approve VP'
        } else if (full.pekerjaan_dokumen_status == '11') {
          var data = 'IFC - Approved VP';
        } else if (full.pekerjaan_dokumen_status_review == '2') {
          var data = 'Review CC';
        } else {
          var data = '';
        }
      }
      return data;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.pegawai_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_dokumen_keterangan;
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_file + '" name="' + full.pekerjaan_dokumen_id + '" title="Lihat" onclick="fun_lihat(this.id, this.name)"><i class="bx bx-book m-0"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_file + '~' + full.pekerjaan_dokumen_id + '" title="Download" onclick="fun_download(this.id,this.name)"><i class="fa fa-download"></i></a></center>';
      }
    }
  },
  ]
});
    /* Table Dokumen History */


    /* Table History */
$('#table_history').DataTable({
  "ordering": false,
  "ajax": {
    "url": "<?= base_url('project/pekerjaan_usulan/') ?>getHistory?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>",
    "dataSrc": ""
  },
  "columns": [{
    render: function(data, type, full, meta) {
      return meta.row + meta.settings._iDisplayStart + 1;
    }
  },
  {
    "data": "log_when"
  },
  {
    "data": "text"
  },
  {
    "data": "log_who"
  },
  ]
});
    /* Table History */

    /* SELECT2 */
    /* Disposisi Koordinator Pekerjaan */
$('#id_tanggung_jawab_vp').select2({
  dropdownParent: $('#modal_vp'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getListUser?pegawai_direct_superior=E53000000&pegawai_jabatan=3') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        q: params.term
      }

      return queryParameters;
    },
  }
});
    /* Disposisi Koordinator Pekerjaan */

    /* Reviewed AVP Terkait */
$('#id_user_vp').select2({
  dropdownParent: $('#modal_vp'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getListUser?pegawai_direct_superior=E53000000&pegawai_jabatan=3') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        q: params.term
      }

      return queryParameters;
    },
  }
});
    /* Reviewed AVP Terkait */

    /*kategori_pekerjaan*/
$('#kategori_pekerjaan_avp').select2({
  dropdownParent: $('#modal_avp'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getListKategoriPekerjaan') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        q: params.term
      }

      return queryParameters;
    },
  }
});
    /*kategori_pekerjaan*/

    /* Disposisi VP AVP Terkait */
$('#id_user_vp_avp').select2({
  dropdownParent: $('#modal_avp'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getListUser?pegawai_direct_superior=E53000000&pegawai_jabatan=3') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        q: params.term
      }

      return queryParameters;
    },
  }
});
    /* Disposisi VP AVP Terkait */

    /* Klasifikasi Pekerjaan */
$('#id_klasifikasi_pekerjaan_avp').select2({
  dropdownParent: $('#modal_avp'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getListKlasifikasiPekerjaan?rkap=') ?>' + $('#rkap').val(),
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        q: params.term
      }

      return queryParameters;
    },
    cache: true
  }
});
    /* Klasifikasi Pekerjaan */

    /* Disposisi Staf */
$('#id_user_avp').select2({
  dropdownParent: $('#modal_avp'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getListUser?perencana=y') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        q: params.term
      }
      return queryParameters;
    },
  }
});

$('#id_user_avp_instrumen').select2({
  dropdownParent: $('#modal_avp'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getListUser?perencana=y') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        q: params.term
      }
      return queryParameters;
    },
  }
});

$('#id_user_avp_listrik').select2({
  dropdownParent: $('#modal_avp'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getListUser?perencana=y') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        q: params.term
      }
      return queryParameters;
    },
  }
});
    /* Disposisi Staf */

    /* Disposisi dari upload document staf*/
$('#id_user_staf').select2({
  dropdownParent: $('#modal_upload'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUserStaf') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        pegawai_nama: params.term
      }

      return queryParameters;
    },
  }
})
    /* Disposisi dari upload document staf*/

    /* Disposisi dari upload document staf hps*/
$('#id_user_staf_hps').select2({
  dropdownParent: $('#modal_upload_hps'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUserStaf') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        pegawai_nama: params.term
      }

      return queryParameters;
    },
  }
})
    /* Disposisi dari upload document staf hps*/

    /* Disposisi dari upload document staf ifc*/
$('#id_user_staf_ifc').select2({
  dropdownParent: $('#modal_upload_ifc'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUserStaf') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        pegawai_nama: params.term
      }

      return queryParameters;
    },
  }
})
    /* Disposisi dari upload document staf ifc*/

    /* Disposisi dari upload document staf ifc hps*/
$('#id_user_staf_ifc_hps').select2({
  dropdownParent: $('#modal_upload_ifc_hps'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUserStaf') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        pegawai_nama: params.term
      }
      return queryParameters;
    },
  }
})
    /* Disposisi dari upload document staf ifc hps*/

    /* Disposisi dari upload document staf*/
$('#id_user_send_vp').select2({
  dropdownParent: $('#modal_send_vp'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUserStaf') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        pegawai_nama: params.term
      }

      return queryParameters;
    },
  }
})
    /* Disposisi dari upload document staf*/

    /* Disposisi dari upload document staf*/
$('#id_user_send_vp_hps').select2({
  dropdownParent: $('#modal_send_vp'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUserStaf') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        pegawai_nama: params.term
      }

      return queryParameters;
    },
  }
})
    /* Disposisi dari upload document staf*/

    /* Send VP khusus Koordinator*/
$('#id_user_send_vp_koor').select2({
  dropdownParent: $('#modal_send_vp_koor'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUserStaf') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        pegawai_nama: params.term
      }

      return queryParameters;
    },
  }
})
    /* Send VP khusus Koordinator*/

    /* Send VP khusus Koordinator*/
$('#id_user_send_vp_koor_hps').select2({
  dropdownParent: $('#modal_send_vp_koor'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUserStaf') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        pegawai_nama: params.term
      }

      return queryParameters;
    },
  }
})
    /* Send VP khusus Koordinator*/

    /* Disposisi dari upload document staf*/
$('#id_user_send_avp_ifc').select2({
  dropdownParent: $('#modal_send_avp_ifc'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUserStaf') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        pegawai_nama: params.term
      }

      return queryParameters;
    },
  }
})
    /* Disposisi dari upload document staf*/

    /* Disposisi dari upload document staf*/
$('#id_user_send_avp_ifc_hps').select2({
  dropdownParent: $('#modal_send_avp_ifc'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUserStaf') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        pegawai_nama: params.term
      }

      return queryParameters;
    },
  }
})
    /* Disposisi dari upload document staf*/

    /* Disposisi dari upload document staf*/
$('#id_user_send_vp_ifc').select2({
  dropdownParent: $('#modal_send_vp_ifc'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUserStaf') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        pegawai_nama: params.term
      }

      return queryParameters;
    },
  }
})
    /* Disposisi dari upload document staf*/

    /* Disposisi dari upload document staf*/
$('#id_user_send_vp_ifc_hps').select2({
  dropdownParent: $('#modal_send_vp_ifc'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUserStaf') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        pegawai_nama: params.term
      }

      return queryParameters;
    },
  }
})
    /* Disposisi dari upload document staf*/

$('#id_user_approve_vp').select2({
  dropdownParent: $('#modal_approve_vp'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUserStaf') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        pegawai_nama: params.term
      }

      return queryParameters;
    },
  }
})

$('#id_user_approve_vp_hps').select2({
  dropdownParent: $('#modal_approve_vp'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUserStaf') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        pegawai_nama: params.term
      }

      return queryParameters;
    },
  }
})

$('#cc_approve_ifa').select2({
  dropdownParent: $('#modal_approve_ifa'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUserStaf') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        pegawai_nama: params.term
      }

      return queryParameters;
    },
  }
})

$('#cc_approve_ifa_hps').select2({
  dropdownParent: $('#modal_approve_ifa'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUserStaf') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        pegawai_nama: params.term
      }

      return queryParameters;
    },
  }
})

$('#cc_ifa').select2({
  dropdownParent: $('#modal_cc_ifa'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUserStaf') ?>?id_pekerjaan=<?= $this->input->get('pekerjaan_id') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        pegawai_nama: params.term
      }

      return queryParameters;
    },
  }
});

$('#cc_ifa').on('select2:unselecting', function(e) {
  var title = e.params.args.data.title;

  if (title == 'y') e.preventDefault();
});

$('#cc_hps_ifa').select2({
  dropdownParent: $('#modal_cc_hps_ifa'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUserStaf') ?>?id_pekerjaan=<?= $this->input->get('pekerjaan_id') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        pegawai_nama: params.term
      }

      return queryParameters;
    },
  }
})

$('#cc_hps_ifa').on('select2:unselecting', function(e) {
  var title = e.params.args.data.title;

  if (title == 'y') e.preventDefault();
});

$('#urutan_proyek_penomoran').select2({
  dropdownParent: $('#modal_penomoran'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUrutanProyekList') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        q: params.term
      }

      return queryParameters;
    },
  }
})

$('#urutan_proyek_penomoran').select2({
  dropdownParent: $('#modal_penomoran'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUrutanProyekList') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        q: params.term
      }

      return queryParameters;
    },
  }
})

$('#section_area_penomoran').select2({
  dropdownParent: $('#modal_penomoran'),
  placeholder: 'Pilih Urutan Proyek Dahulu',
      // ajax: {
      //   delay: 250,
      //   url: '<?= base_url('project/pekerjaan_usulan/getSectionAreaList') ?>',
      //   dataType: 'json',
      //   type: 'GET',
      //   data: function(params) {
      //     var queryParameters = {
      //       q: params.term
      //     }

      //     return queryParameters;
      //   },
      // }
})


$('#id_perencana_baru').select2({
  dropdownParent: $('#modal_ganti_perencana'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUserPengganti') ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        pegawai_nama: params.term
      }

      return queryParameters;
    },
  }
})

$('#id_koor_baru').select2({
  dropdownParent: $('#modal_ganti_koor'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUserKoorPengganti?id_pekerjaan=' . $this->input->get('pekerjaan_id')) ?>',
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        pegawai_nama: params.term
      }

      return queryParameters;
    },
  }
})

$('.select2-selection').css({
  height: 'auto',
  margin: '0px -10px 0px -10px'
});
$('.select2').css('width', '100%');
    /* SELECT2 */

    /* Maksimal Progress Pekerjaan */
$('#pekerjaan_progress').on('input', function(ev) {
  var value = $(this).val();
  if ((value !== '') && (value.indexOf('.') === -1)) $(this).val(Math.max(Math.min(value, 91), 0));
});
    /* Maksimal Progress Pekerjaan */

fun_loading();


fun_cekRevisiIFA('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>');

});

function fun_ganti_section_area(id) {
  $('#section_area_penomoran').select2({
    dropdownParent: $('#modal_penomoran'),
    placeholder: 'Pilih',
    ajax: {
      delay: 250,
      url: '<?= base_url('project/pekerjaan_usulan/getSectionAreaList?id_urutan_proyek=') ?>' + id,
      dataType: 'json',
      type: 'GET',
      data: function(params) {
        var queryParameters = {
          q: params.term
        }

        return queryParameters;
      },
    }
  })

  $('.select2-selection').css({
    height: 'auto',
    margin: '0px -10px 0px -10px'
  });
  $('.select2').css('width', '100%');

}

  /* KLIK */
  /* Klik Approve Customer */
function fun_approve(id, text) {
  Swal.fire({
    title: text,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya"
  }).then(function(result) {
    if (result.value) {
      $.getJSON('<?= base_url('project/pekerjaan_usulan/prosesApprove') ?>', {
        pekerjaan_id: id,
        pekerjaan_status: $('#pekerjaan_status').val()
      }, function(json) {
        console.log('ok');
      });
      setTimeout(() => {
        if ($('#is_rkap').val() == '1') {
          window.location.replace('<?= base_url('project/RKAP#berjalan') ?>');
        } else {
          window.location.replace('<?= base_url('project/Non_RKAP#berjalan') ?>');
        }
      }, "1000");
    }
  });
}

function fun_review(id, text) {
  Swal.fire({
    title: text,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya"
  }).then(function(result) {
    if (result.value) {
      $.getJSON('<?= base_url('project/pekerjaan_usulan/prosesReview') ?>', {
        pekerjaan_id: id,
        pekerjaan_status: $('#pekerjaan_status').val()
      }, function(json) {
        console.log('ok');
      });
      setTimeout(() => {
        if ($('#is_rkap').val() == '1') {
          window.location.replace('<?= base_url('project/RKAP') ?>')
        } else {
          window.location.replace('<?= base_url('project/Non_RKAP') ?>');
        }
      }, "1000");
    }
  });
}
  /* Klik Approve Customer */

  /* Klik Reject Customer */
function fun_reject(id) {
  Swal.fire({
    title: "Anda Yakin Reject?",
    html: `<input type="text" id="note_reject" class="swal2-input" placeholder="Note">`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya",
    focusConfirm: false,
    preConfirm: () => {
      const note_reject = Swal.getPopup().querySelector('#note_reject').value
      if (!note_reject) {
        Swal.showValidationMessage(`Note Tidak Boleh Kosong`)
      }
      return {
        note_reject: note_reject
      }
    }
  }).then(function(result) {
    if (result.value) {
      var note_reject = result.value.note_reject;

      $.getJSON('<?= base_url('project/pekerjaan_usulan/prosesReject') ?>', {
        pekerjaan_id: id,
        note_reject: note_reject,
        pekerjaan_status: $('#pekerjaan_status').val(),

      }, function(json) {});
      setTimeout(() => {
        if ($('#is_rkap').val() == '1') {
          window.location.replace('<?= base_url('project/RKAP') ?>')
        } else {
          window.location.replace('<?= base_url('project/Non_RKAP') ?>');
        }
      }, "1000");
    }
  });
}
  /* Klik Reject Customer */

  /* Klik Reject AVP */
function fun_reject_avp(id) {
  <?php
  $is_koor = $this->db->get_where('dec.dec_pekerjaan_disposisi', ['id_pekerjaan' => $this->input->get('pekerjaan_id'), 'pekerjaan_disposisi_status' => $this->input->get('status'), 'id_user' => $this->session->userdata()['pegawai_nik']])->row_array();
  ?>
  <?php if (($is_koor) && $is_koor['id_penanggung_jawab'] != 'y') : ?>
    Swal.fire({
      title: "Anda Yakin Reject?",
      html: `<input type="text" id="note_reject" class="swal2-input" placeholder="Note">`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#34c38f",
      cancelButtonColor: "#f46a6a",
      confirmButtonText: "Iya",
      focusConfirm: false,
      preConfirm: () => {
        const note_reject = Swal.getPopup().querySelector('#note_reject').value
        if (!note_reject) {
          Swal.showValidationMessage(`Note Tidak Boleh Kosong`)
        }
        return {
          note_reject: note_reject
        }
      }
    }).then(function(result) {
      if (result.value) {
        var note_reject = result.value.note_reject;
        $.getJSON('<?= base_url('project/pekerjaan_usulan/prosesRejectAVP') ?>', {
          pekerjaan_id: id,
          note_reject: note_reject
        }, function(json) {
          console.log('ok');
        });
        setTimeout(() => {
          if ($('#is_rkap').val() == '1') {
            window.location.replace('<?= base_url('project/RKAP') ?>')
          } else {
            window.location.replace('<?= base_url('project/Non_RKAP') ?>');
          }
        }, "1000");
      }
    });
  <?php else : ?>
    Swal.fire({
      title: "Ganti Koor Dahulu",
      icon: "warning",
      cancelButtonColor: "#f46a6a",
      focusConfirm: false,
      preConfirm: () => {}
    }).then(function(result) {});
  <?php endif ?>
}
  /* Klik Reject AVP */

  /* Klik Reject Staf */
function fun_reject_staf(id) {
  <?php if (($is_koor) && $is_koor['id_penanggung_jawab'] != 'y') : ?>
    Swal.fire({
      title: "Anda Yakin Reject?",
      html: `<input type="text" id="note_reject" class="swal2-input" placeholder="Note">`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#34c38f",
      cancelButtonColor: "#f46a6a",
      confirmButtonText: "Iya",
      focusConfirm: false,
      preConfirm: () => {
        const note_reject = Swal.getPopup().querySelector('#note_reject').value
        if (!note_reject) {
          Swal.showValidationMessage(`Note Tidak Boleh Kosong`)
        }
        return {
          note_reject: note_reject
        }
      }
    }).then(function(result) {
      if (result.value) {
        var note_reject = result.value.note_reject;
        $.getJSON('<?= base_url('project/pekerjaan_usulan/prosesRejectStaf') ?>', {
          pekerjaan_id: id,
          note_reject: note_reject
        }, function(json) {
          console.log('ok');
        });
        setTimeout(() => {
          if ($('#is_rkap').val() == '1') {
            window.location.replace('<?= base_url('project/RKAP') ?>')
          } else {
            window.location.replace('<?= base_url('project/Non_RKAP') ?>');
          }
        }, "1000");
      }
    });
  <?php else : ?>
    Swal.fire({
      title: "Ganti Koor Dahulu",
      icon: "warning",
      cancelButtonColor: "#f46a6a",
      focusConfirm: false,
      preConfirm: () => {}
    }).then(function(result) {});
  <?php endif ?>
}
  /* Klik Reject Staf */




  /* Klik Disposisi VP */
function fun_disposisi_vp() {
  $('#modal_vp').modal('show');
  $('#simpan_vp').css('display', 'block');
}
  /* Klik Disposisi VP */

  /* Klik Reviewed AVP */
function fun_disposisi_avp(id) {
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getVPAVP') ?>', {
    pekerjaan_id: id
  }, function(json) {
    console.log(json);
    $.each(json, function(index, val) {
      $('#' + index).val(val);
      $('#id_user_vp_avp').append('<option selected value="' + val.id_user + '">' + val.pegawai_nama + ' - ' + val.pegawai_postitle + '</option>');

        // $('#id_user_vp_avp').select2('data', {id:val.id_user, text:val.pegawai_nama});
        // $('#id_user_vp_avp').trigger('change');
    });
  });
  $('#modal_avp').modal('show');
  $('#simpan_avp').css('display', 'block');

}
  /* Klik Reviewed AVP */

  /*Untuk Blok"an nya */
function fun_disposisi_avp_check_tj(id) {
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getVPAVPTJ') ?>', {
    pekerjaan_id: id
  }, function(json) {

    $.each(json, function(index, val) {
      $('#' + index).val(val);
      console.log(val);
        // $('#id_tanggung_jawab').val(val.id_penanggung_jawab);
        // LISTIN AVP TERKAIT
      if (val.id_penanggung_jawab == 'n' && val.pegawai_unitkerja == 'E53300') {
        $('#div_id_user_vp_avp').hide();
        $('#id_user_vp_avp').prop('disabled', true);
        $('#div_pekerjaan_waktu_akhir_avp').hide();
        $('#div_pekerjaan_judul_avp').hide();
        $('#div_id_klasifikasi_pekerjaan_avp').hide();
          // $('#div_id_user_avp').show();
        $('#div_id_user_avp_instrumen').show();
        $('#div_id_user_avp_listrik').show();
          // NON RKAP LISTIN KOORDINATOR
          // } else if (val.id_penanggung_jawab == 'y' && val.pegawai_unitkerja == 'E53300' && $('#is_rkap').val() != '1') {
      } else if (val.id_penanggung_jawab == 'y' && val.pegawai_unitkerja == 'E53300') {
        $('#div_id_user_vp_avp').show();
        $('#id_user_vp_avp').prop('enable', true);
        $('#div_pekerjaan_waktu_akhir_avp').show();
        $('#div_pekerjaan_judul_avp').show();
        $('#div_id_klasifikasi_pekerjaan_avp').show();
        $('#id_klasifikasi_pekerjaan_avp').append('<option selected value="' + val.id_klasifikasi_pekerjaan + '">' + val.klasifikasi_pekerjaan_nama + '</option>');
          // $('#div_id_user_avp').show();
        $('#div_id_user_avp_instrumen').show();
        $('#div_id_user_avp_listrik').show();
          // RKAP NON LISTIN KOORDINATOR
      } else if (val.id_penanggung_jawab == 'y' && $('#is_rkap').val() == '1') {
        $('#div_id_user_vp_avp').show();
        $('#id_user_vp_avp').prop('enable', true);
        $('#div_pekerjaan_waktu_akhir_avp').show();
        $('#div_pekerjaan_judul_avp').show();
        $('#div_id_klasifikasi_pekerjaan_avp').show();
        $('#id_klasifikasi_pekerjaan_avp').append('<option selected value="' + val.id_klasifikasi_pekerjaan + '">' + val.klasifikasi_pekerjaan_nama + '</option>');
        $('#div_id_user_avp').show();
          // $('#div_id_user_avp_instrumen').show();
          // $('#div_id_user_avp_listrik').show();
          // NON RKAP LISTIN NON KOORDINATOR
      } else if (val.id_penanggung_jawab == 'n') {
        $('#div_id_user_vp_avp').hide();
        $('#id_user_vp_avp').prop('disabled', true);
        $('#div_pekerjaan_waktu_akhir_avp').hide();
        $('#div_pekerjaan_judul_avp').hide();
        $('#div_id_klasifikasi_pekerjaan_avp').hide();
        $('#div_id_user_avp').show();
          // NON RKAP NON LISTIN KOORDINATOR
      } else if (val.id_penanggung_jawab == 'y' && $('#is_rkap').val() != 1) {
        $('#div_id_user_vp_avp').show();
        $('#id_user_vp_avp').prop('enable', true);
        $('#div_pekerjaan_waktu_akhir_avp').show();
        $('#div_pekerjaan_judul_avp').show();
        $('#div_id_klasifikasi_pekerjaan_avp').show();
        $('#id_klasifikasi_pekerjaan_avp').append('<option selected value="' + val.id_klasifikasi_pekerjaan + '">' + val.klasifikasi_pekerjaan_nama + '</option>');
        $('#div_id_user_avp').show();
      }
        // select2
    });
  });
    // $('#modal_avp').modal('show');
    // $('#simpan_avp').css('display', 'block');

}
  /*Untuk Blok"an nya */

  /* Klik Progress Pekerjaan Staf */
function fun_progress(id) {
  $('#modal_progress').modal('show');
  $.getJSON('<?= base_url('project/pekerjaan_usulan/') ?>getProgressPekerjaan?pekerjaan_id=' + id, function(json) {
    $('#progress_id').val(json.progress_id);
    $('#id_pekerjaan_progress').val(json.id_pekerjaan);
    $('#pekerjaan_progress').val(json.progress_jumlah);
    if (json.progress_jumlah != null) {
      $('#edit_progress').css('display', 'block');
      $('#simpan_progress').css('display', 'none');
    } else {
      $('#edit_progress').css('display', 'none');
      $('#simpan_progress').css('display', 'block');
    }
  });
}
  /* Klik Progress Pekerjaan Staf */

  /*klik penomoran*/
function fun_penomoran(id) {
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getPenomoranDokumen') ?>', {
    id_pekerjaan: id
  }, function(json, textStatus) {
    if (json != '') {
      $.each(json, function(index, val) {
        $('#' + index).val(val);
        $('#id_pekerjaan_penomoran').val(val.id_pekerjaan);
        $('#penomoran_id').val(val.pekerjaan_dokumen_penomoran_id);
        $('#urutan_proyek_penomoran').append('<option selected value="' + val.urutan_proyek_id + '">' + val.urutan_proyek_nama + '</option>');
        $('#section_area_penomoran').append('<option selected value="' + val.section_area_id + '">' + val.section_area_nama + '</option>');
      });
    } else {
      $('#id_pekerjaan_penomoran').val(id);
    }
  });
  $("#modal_penomoran").modal('show');
}
  /*klik penomoran*/

  /*klik nilai HPS*/
function fun_nilai_hps(id) {
  $("#modal_nilai_hps").modal('show');
  $.getJSON('<?= base_url('project/pekerjaan_usulan/nilaiHPS') ?>', {
    id_pekerjaan: id
  }, function(json) {
    if (json != '') {
      $('#is_nilai_hps_old').val('y');
      $.each(json, function(index, val) {
        console.log(index);
        $('#' + index).val(val)
        $('#nilai_hps_id_' + index).val(val.pekerjaan_nilai_hps_id);
        $('#pekerjaan_nilai_hps_' + val.id_bagian).val(val.pekerjaan_nilai_hps_jumlah);
        funSumHPS(val.id_bagian)
      });
    }
  });
}
  /*klik nilai HPS*/

  // klik ganti perencana
function fun_ganti_perencana(id) {
  $('#modal_ganti_perencana').modal('show');
}
  // klik ganti perencana

  /*klik ganti Koor*/
function fun_ganti_koor(id) {
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getDataKoorBaru') ?>', {
    id_pekerjaan: id
  }, function(json, textStatus) {
    if (json != '') {
      $.each(json, function(index, val) {
        $('#' + index).val(val);
        $('#id_koor_baru').append('<option selected value="' + val.id_koor_baru + '">' + val.pegawai_nama + '</option>');
      });
    }
  });
  $('#modal_ganti_koor').modal('show');
}
  /*klik ganti Koor*/

  /* Klik Upload Dokumen */
function fun_upload(id, val) {
  $('#id_pekerjaan').val(id);
  $('#modal_upload').modal('show');
  $('#id_user_staf').empty();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserStafVP') ?>', {
    pekerjaan_id: id,
    is_cc: 'y',
  }, function(json) {
    $.each(json, function(index, val) {
      $('#' + index).val(val);
      $('#id_user_staf').append('<option selected value="' + val.id_user + '">' + val.pegawai_nama + '</option>');
    });
  });

  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserKoor') ?>', {
    pekerjaan_id: id,
    status: '<?= $_GET['status'] ?>'
  }, function(json, result) {
    if (json.id_penanggung_jawab == 'n') {
      $('#id_user_staf').prop('disabled', true);
    } else if (json.id_penanggung_jawab == 'y') {
      $('#id_user_staf').prop('disabled', false);
    }
  });

  $.getJSON('<?= base_url('project/pekerjaan_usulan/cekRevisiIFA') ?>', {
    pekerjaan_id: id
  }, function(json, result) {
    console.log(json);
    if (json && json.jumlah_revisi > 0) {
        // $('#simpan_dokumen_ifc_revisi').show();
    } else {
        // $('#simpan_dokumen_ifc_revisi').hide();
    }
  })

    /* EASYUI */
  setTimeout(function() {
    $('#dg_document').edatagrid({
      url: '<?= base_url("project/pekerjaan_usulan/getAsetDocument?pekerjaan_dokumen_status=n&id_pekerjaan=") ?>' + id + '&pekerjaan_status=' + val + '&id_create=' + $('#session_user').val() + '&is_hps=n',
      saveUrl: '<?= base_url('project/pekerjaan_usulan/insertAsetDocument') ?>',
      updateUrl: '<?= base_url('project/pekerjaan_usulan/updateAsetDocument') ?>',
      rowStyler: function(index, row) {
        if (row.pekerjaan_dokumen_status == '0') {
          return 'background-color:#FF0000;font-weight:bold;';
        }
      },

      onAdd: function(index, row) {
        var up = $(this).datagrid('getEditor', {
          index: index,
          field: 'urutan_proyek_nama'
        });
        $(up.target).textbox('setValue', row.urutan_proyek_nama);

        var sa = $(this).datagrid('getEditor', {
          index: index,
          field: 'section_area_nama'
        });
        $(sa.target).textbox('setValue', row.section_area_nama);
      },

      onBeginEdit: function(index, row) {
          /* Combobox */
          /*template*/
        var cb = $(this).datagrid('getEditor', {
          index: index,
          field: 'pekerjaan_template_nama'
        });
        $(cb.target).combobox('setValue', row.pekerjaan_template_id);
          /*template*/

        var up = $(this).datagrid('getEditor', {
          index: index,
          field: 'urutan_proyek_nama'
        });
        $(up.target).textbox('setValue', row.urutan_proyek_nama);

        var sa = $(this).datagrid('getEditor', {
          index: index,
          field: 'section_area_nama'
        });
        $(sa.target).textbox('setValue', row.section_area_nama);

          /*bidang*/
        var bd = $(this).datagrid('getEditor', {
          index: index,
          field: 'bidang_nama'
        });
        $(bd.target).combobox('setValue', row.bidang_id);
          /*bidang*/
          /* File */
        var ed = $(this).datagrid('getEditor', {
          index: index,
          field: 'pekerjaan_dokumen_file'
        });
        row.fileName = row.fileName || row.pekerjaan_dokumen_file;
        $(ed.target).filebox('setText', row.fileName);
          /* Combobox */
          /* File */
      },
      onEndEdit: function(index, row) {
        var e = $(this).datagrid('getEditor', {
          index: index,
          field: 'pekerjaan_dokumen_file'
        });
        var files = $(e.target).filebox('files');
        if (files.length) row.savedFileName = e.target.filebox('getText');
      },
      columns: [
        [{
          field: 'tanggal_dokumen_input',
          title: 'Waktu Input',
          width: '20%',
          editor: {
            type: 'label',
          },
        },
        {
          field: 'pekerjaan_template_nama',
          title: 'Kategori',
          width: '20%',
          editor: {
            type: 'combobox',
            options: {
              required: true,
              idField: 'pekerjaan_template_id',
              textField: 'pekerjaan_template_nama',
              valueField: 'pekerjaan_template_id',
              url: '<?= base_url() ?>project/pekerjaan_usulan/getTemplatePekerjaan',
              mode: 'remote',
              fitColumns: true,
              columns: [
                [{
                  field: 'pekerjaan_template_nama',
                  title: 'Template Dokumen',
                  width: 400
                }, ]
                ],
              panelHeight: 135
            },
          },
        },
        {
          field: 'pekerjaan_dokumen_nama',
          title: 'Sub Kategori',
          width: '20%',
          editor: {
            type: 'textbox',
            options: {
              required: true,
              onchange: function(value) {
                $("#doc_nama").val(value);
              }
            }
          },
        },
        {
          field: 'pekerjaan_dokumen_jenis',
          title: 'Gambar / Dokumen',
          width: '20%',
          editor: {
            type: 'combobox',
            options: {
              required: true,
              idField: 'pekerjaan_dokumen_jenis',
              textField: 'pekerjaan_dokumen_jenis',
              valueField: 'pekerjaan_dokumen_jenis',
              url: '<?= base_url() ?>project/pekerjaan_usulan/getPekerjaanDokumenJenis',
              mode: 'remote',
              fitColumns: true,
              columns: [
                [{
                  field: 'pekerjaan_dokumen_jenis',
                  title: 'Gambar / Dokumen',
                  width: 400
                }, ]
                ],
              panelHeight: 135
            },
          },
        },
        {
          field: 'bidang_nama',
          title: 'Bidang',
          width: '20%',
          editor: {
            type: 'combobox',
            options: {
              required: true,
              idField: 'bidang_id',
              textField: 'bidang_nama',
              valueField: 'bidang_id',
              url: '<?= base_url() ?>project/pekerjaan_usulan/getBidang',
              mode: 'remote',
              fitColumns: true,
              columns: [
                [{
                  field: 'bidang_nama',
                  title: 'Bidang',
                  width: 400
                }, ]
                ],
              panelHeight: 135
            },
          },
        },
        {
          field: 'urutan_proyek_nama',
          title: 'Urutan Proyek',
          width: '20%',
          editor: {
            type: 'textbox',
            options: {
              required: false,
              readonly: true,
            }
          },
        },
        {
          field: 'section_area_nama',
          title: 'Section Area',
          width: '20%',
          editor: {
            type: 'textbox',
            options: {
              required: false,
              readonly: true,
            }
          },
        },
        {
          field: 'pekerjaan_dokumen_nomor',
          title: 'No Dokumen',
          width: '20%',
          editor: {
            type: 'label',
            options: {
              required: false,
            }
          },
        },
            // {
            //   field: 'pekerjaan_dokumen_jumlah',
            //   title: 'Jumlah Halaman',
            //   width: '20%',
            //   editor: {
            //     type: 'numberbox',
            //     options: {
            //       required: true,
            //     }
            //   },
            // },
        {
          field: 'pekerjaan_dokumen_kertas',
          title: 'Ukuran Kertas',
          width: '20%',
          editor: {
            type: 'combobox',
            options: {
              required: true,
              idField: 'pekerjaan_dokumen_kertas',
              textField: 'pekerjaan_dokumen_kertas',
              valueField: 'pekerjaan_dokumen_kertas',
              url: '<?= base_url() ?>project/pekerjaan_usulan/getPekerjaanDokumenKertas',
              mode: 'remote',
              fitColumns: true,
              columns: [
                [{
                  field: 'pekerjaan_dokumen_kertas',
                  title: 'Ukuran Kertas',
                  width: 400
                }, ]
                ],
              panelHeight: 135
            },
          },
        },
        {
          field: 'pekerjaan_dokumen_orientasi',
          title: 'Orientasi Kertas',
          width: '20%',
          editor: {
            type: 'combobox',
            options: {
              required: true,
              idField: 'pekerjaan_dokumen_orientasi',
              textField: 'pekerjaan_dokumen_orientasi',
              valueField: 'pekerjaan_dokumen_orientasi',
              url: '<?= base_url() ?>project/pekerjaan_usulan/getPekerjaanDokumenOrientasi',
              mode: 'remote',
              fitColumns: true,
              columns: [
                [{
                  field: 'pekerjaan_dokumen_orientasi',
                  title: 'Orientasi Kertas',
                  width: 400
                }, ]
                ],
              panelHeight: 135
            },
          },
        },
        {
          field: 'pekerjaan_dokumen_file',
          title: 'File',
          width: '20%',
          formatter: (value, row) => row.fileName || value,
          editor: {
            type: 'filebox',
            options: {
              required: true,
              accept: 'application/pdf,.xls,.xlsx',
              buttonText: '...',
              onChange: function() {
                var self = $(this);
                var files = self.filebox('files');
                var formData = new FormData();
                var nama = $("#doc_nama").val();
                self.filebox('setText', 'Menyimpan...');

                formData.append('id_pekerjaan', $('#id_pekerjaan').val());

                for (var i = 0; i < files.length; i++) {
                  var file = files[i];
                  var extension = file.name.split('.').pop().toLowerCase();

                  if (extension !== 'pdf' && extension !== 'xls' && extension !== 'xlsx') {
                    Swal.fire({
                      icon: 'error',
                      title: 'Oops...',
                      text: 'Please select a PDF file.',
                    });
                    self.filebox('clear');
                    return;
                  } else {
                    formData.append('file', file, file.name);
                  }
                }

                $.ajax({
                  url: '<?= base_url('project/pekerjaan_usulan/insertFilePekerjaanDokumen') ?>',
                  type: 'post',
                  data: formData,
                  contentType: false,
                  processData: false,
                  beforeSend: function() {
                    $.messager.progress({
                      title: 'Uploading',
                      msg: 'Uploading file...',
                      interval: 1000
                    });
                  },
                  complete: function() {
                    $.messager.progress('close');
                  },
                  success: function(data) {
                    self.filebox('setText', data);
                  }
                })
              }
            },
          },
        },
        {
          field: 'pekerjaan_dokumen_id',
          title: 'Lihat',
          width: '20%',
          formatter: function(value, row, index) {
            if (row.pekerjaan_dokumen_file) {
              return '<a href="#" onclick="viewFile(\'' + row.pekerjaan_dokumen_file + '\')">Lihat File</a>';
            } else {
              return '-';
            }
          },

        },

        ],
],
});
}, 500);
}
  /* EASYUI */
  /* Klik Upload Dokumen */

  /* Klik HPS Dokumen */
function fun_upload_hps(id, val) {
  $('#id_pekerjaan_hps').val(id);
  $('#modal_upload_hps').modal('show');
  $('#id_user_staf_hps').empty();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserStafVP') ?>', {
    pekerjaan_id: id,
    is_cc: 'h',
  }, function(json) {
    $.each(json, function(index, val) {
      $('#' + index).val(val);
      $('#id_user_staf_hps').append('<option selected value="' + val.id_user + '">' + val.pegawai_nama + '</option>');
    });
  });

  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserKoor') ?>', {
    pekerjaan_id: id,
    status: '<?= $_GET['status'] ?>'
  }, function(json, result) {
    if (json.id_penanggung_jawab == 'n') {
      $('#id_user_staf_hps').prop('disabled', true);
      $('#id_user_staf_hps').attr('readonly', true);

    } else if (json.id_penanggung_jawab == 'y') {
      $('#id_user_staf_hps').prop('disabled', false);
      $('#id_user_staf_hps').attr('readonly', false);
    }
  });
  $.getJSON('<?= base_url('project/pekerjaan_usulan/cekRevisiIFA') ?>', {
    pekerjaan_id: id
  }, function(json, result) {
    console.log(json);
    if (json && json.jumlah_revisi > 0) {
      $('#simpan_dokumen_ifc_revisi_hps').show();
    } else {
      $('#simpan_dokumen_ifc_revisi_hps').hide();
    }
  })

    /* EASYUI */
    /*Upload Dokumen HPS*/
  setTimeout(function() {
    $('#dg_document_hps').edatagrid({
      url: '<?= base_url("project/pekerjaan_usulan/getAsetDocument?pekerjaan_dokumen_status=n&id_pekerjaan=") ?>' + id + '&pekerjaan_status=' + val + '&id_create=' + $('#session_user').val() + '&is_hps=y',
      saveUrl: '<?= base_url('project/pekerjaan_usulan/insertAsetDocument') ?>',
      updateUrl: '<?= base_url('project/pekerjaan_usulan/updateAsetDocument') ?>',
      queryParams: {
          // is_hps: 'y'
      },
      rowStyler: function(index, row) {
        if (row.pekerjaan_dokumen_status == '0') {
          return 'background-color:#FF0000;font-weight:bold;';
        }
      },
      onAdd: function(index, row) {
        var up = $(this).datagrid('getEditor', {
          index: index,
          field: 'urutan_proyek_nama'
        });
        $(up.target).textbox('setValue', row.urutan_proyek_nama);

        var sa = $(this).datagrid('getEditor', {
          index: index,
          field: 'section_area_nama'
        });
        $(sa.target).textbox('setValue', row.section_area_nama);

        var kertas = $(this).datagrid('getEditor', {
          index: index,
          field: 'pekerjaan_dokumen_kertas'
        });
        $(kertas.target).textbox('setValue', 'A4');
      },
      onBeginEdit: function(index, row) {
        console.log(row);
          /* Combobox */
          /*template*/
        var cb = $(this).datagrid('getEditor', {
          index: index,
          field: 'pekerjaan_template_nama'
        });
        $(cb.target).combobox('setValue', row.pekerjaan_template_id);
          /*template*/
          /*bidang*/
        var bd = $(this).datagrid('getEditor', {
          index: index,
          field: 'bidang_nama'
        });
        $(bd.target).combobox('setValue', row.bidang_id);

        var up = $(this).datagrid('getEditor', {
          index: index,
          field: 'urutan_proyek_nama'
        });
        $(up.target).textbox('setValue', row.urutan_proyek_nama);

        var sa = $(this).datagrid('getEditor', {
          index: index,
          field: 'section_area_nama'
        });
        $(sa.target).textbox('setValue', row.section_area_nama);
          /* Combobox */

          /* File */
        var ed = $(this).datagrid('getEditor', {
          index: index,
          field: 'pekerjaan_dokumen_file'
        });
        row.fileName = row.fileName || row.pekerjaan_dokumen_file;
        $(ed.target).filebox('setText', row.fileName);
          /* File */
      },
      onEndEdit: function(index, row) {
        var e = $(this).datagrid('getEditor', {
          index: index,
          field: 'pekerjaan_dokumen_file'
        });
        var files = $(e.target).filebox('files');
        if (files.length) row.savedFileName = e.target.filebox('getText');
      },
      columns: [
        [{
          field: 'tanggal_dokumen_input',
          title: 'Waktu Input',
          width: '20%',
          editor: {
            type: 'label',
          },
        },
            // {
            //   field: 'pekerjaan_template_nama',
            //   title: 'Kategori',
            //   width: '20%',
            //   editor: {
            //     type: 'combobox',
            //     options: {
            //       required: true,
            //       idField: 'pekerjaan_template_id',
            //       textField: 'pekerjaan_template_nama',
            //       valueField: 'pekerjaan_template_id',
            //       url: '<?= base_url() ?>project/pekerjaan_usulan/getTemplatePekerjaan?is_hps=true',
            //       mode: 'remote',
            //       fitColumns: true,
            //       columns: [
            //         [{
            //           field: 'pekerjaan_template_nama',
            //           title: 'Template Dokumen',
            //           width: 400
            //         }, ]
            //         ],
            //       panelHeight: 135,
            //       onLoadSuccess: function(data) {
            //         if (data.length > 0) {
            //           $(this).combobox('setValue', data[0].pekerjaan_template_id);
            //         }
            //       }
            //     },
            //   },
            // },
        {
          field: 'pekerjaan_dokumen_nama',
          title: 'Sub Kategori',
          width: '20%',
          editor: {
            type: 'textbox',
            options: {
              required: true,
              onchange: function(value) {
                $("#doc_nama").val(value);
              }
            }
          },
        },
            // {
            //   field: 'pekerjaan_dokumen_jenis',
            //   title: 'Gambar / Dokumen',
            //   width: '20%',
            //   editor: {
            //     type: 'combobox',
            //     options: {
            //       required: true,
            //       idField: 'pekerjaan_dokumen_jenis',
            //       textField: 'pekerjaan_dokumen_jenis',
            //       valueField: 'pekerjaan_dokumen_jenis',
            //       url: '<?= base_url() ?>project/pekerjaan_usulan/getPekerjaanDokumenJenis?is_hps=true',
            //       mode: 'remote',
            //       fitColumns: true,
            //       columns: [
            //         [{
            //           field: 'pekerjaan_dokumen_jenis',
            //           title: 'Gambar / Dokumen',
            //           width: 400
            //         }, ]
            //         ],
            //       panelHeight: 135,
            //       onLoadSuccess: function(data) {
            //         if (data.length > 0) {
            //           $(this).combobox('setValue', data[0].pekerjaan_dokumen_jenis);
            //         }
            //       }
            //     },
            //   },
            // },
        {
          field: 'bidang_nama',
          title: 'Bidang',
          width: '20%',
          editor: {
            type: 'combobox',
            options: {
              required: true,
              idField: 'bidang_id',
              textField: 'bidang_nama',
              valueField: 'bidang_id',
              url: '<?= base_url() ?>project/pekerjaan_usulan/getBidang',
              mode: 'remote',
              fitColumns: true,
              columns: [
                [{
                  field: 'bidang_nama',
                  title: 'Bidang',
                  width: 400
                }, ]
                ],
              panelHeight: 135
            },
          },
        },
        {
          field: 'urutan_proyek_nama',
          title: 'Urutan Proyek',
          width: '20%',
          editor: {
            type: 'textbox',
            options: {
              required: false,
              readonly: true,
            }
          },
        },
        {
          field: 'section_area_nama',
          title: 'Section Area',
          width: '20%',
          editor: {
            type: 'textbox',
            options: {
              required: false,
              readonly: true,
            }
          },
        },
        {
          field: 'pekerjaan_dokumen_nomor',
          title: 'No Dokumen',
          width: '20%',
          editor: {
            type: 'label',
            options: {
              required: false,
            }
          },
        },
            // {
            //   field: 'pekerjaan_dokumen_jumlah',
            //   title: 'Jumlah Halaman',
            //   width: '20%',
            //   editor: {
            //     type: 'numberbox',
            //     options: {
            //       required: true,
            //     }
            //   },
            // },
            // {
            //   field: 'pekerjaan_dokumen_kertas',
            //   title: 'Ukuran Kertas',
            //   width: '20%',
            //   editor: {
            //     type: 'combobox',
            //     options: {
            //       required: true,
            //       idField: 'pekerjaan_dokumen_kertas',
            //       textField: 'pekerjaan_dokumen_kertas',
            //       valueField: 'pekerjaan_dokumen_kertas',
            //       url: '<?= base_url() ?>project/pekerjaan_usulan/getPekerjaanDokumenKertas?is_hps=true',
            //       mode: 'remote',
            //       fitColumns: true,
            //       columns: [
            //         [{
            //           field: 'pekerjaan_dokumen_kertas',
            //           title: 'Ukuran Kertas',
            //           width: 400
            //         }, ]
            //         ],
            //       panelHeight: 135
            //     },
            //   },
            // },
            // {
            //   field: 'pekerjaan_dokumen_orientasi',
            //   title: 'Orientasi Kertas',
            //   width: '20%',
            //   editor: {
            //     type: 'combobox',
            //     options: {
            //       required: true,
            //       idField: 'pekerjaan_dokumen_orientasi',
            //       textField: 'pekerjaan_dokumen_orientasi',
            //       valueField: 'pekerjaan_dokumen_orientasi',
            //       url: '<?= base_url() ?>project/pekerjaan_usulan/getPekerjaanDokumenOrientasi?is_hps=true',
            //       mode: 'remote',
            //       fitColumns: true,
            //       columns: [
            //         [{
            //           field: 'pekerjaan_dokumen_orientasi',
            //           title: 'Orientasi Kertas',
            //           width: 400
            //         }, ]
            //         ],
            //       panelHeight: 135,
            //       onLoadSuccess: function(data) {
            //         if (data.length > 0) {
            //           $(this).combobox('setValue', data[0].pekerjaan_dokumen_orientasi);
            //         }
            //       }
            //     },
            //   },
            // },
        {
          field: 'pekerjaan_dokumen_file',
          title: 'File',
          width: '20%',
          formatter: (value, row) => row.fileName || value,
          editor: {
            type: 'filebox',
            options: {
              required: true,
              accept: 'application/pdf,.xls,.xlsx',
              buttonText: '...',
              onChange: function() {
                var self = $(this);
                var files = self.filebox('files');
                var formData = new FormData();
                var nama = $("#doc_nama_hps").val();
                self.filebox('setText', 'Menyimpan...');

                formData.append('id_pekerjaan', $('#id_pekerjaan_hps').val());

                for (var i = 0; i < files.length; i++) {
                  var file = files[i];
                  formData.append('file', file, file.name);
                }

                $.ajax({
                  url: '<?= base_url('project/pekerjaan_usulan/insertFilePekerjaanDokumen') ?>',
                  type: 'post',
                  data: formData,
                  contentType: false,
                  processData: false,
                  beforeSend: function() {
                    $.messager.progress({
                      title: 'Uploading',
                      msg: 'Uploading file...',
                      interval: 1000
                    });
                  },
                  complete: function() {
                    $.messager.progress('close');
                  },
                  success: function(data) {
                    self.filebox('setText', data);
                  }
                })
              }
            },
          },
        },
        {
          field: 'pekerjaan_dokumen_id',
          title: 'Lihat',
          width: '20%',
          formatter: function(value, row, index) {
            if (row.pekerjaan_dokumen_file) {
              return '<a href="#" onclick="viewFile(\'' + row.pekerjaan_dokumen_file + '\')">Lihat File</a>';
            } else {
              return '-';
            }
          },
        },

        ],
],
});
}, 500);
}
  /* Klik HPS Dokumen */
  /* EASYUI */

  /* Klik Upload Dokumen IFC */
function fun_upload_ifc(id, val) {
  $('#id_pekerjaan_ifc').val(id);
  $('#modal_upload_ifc').modal('show');
  $('#id_user_staf_ifc').empty();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserStafVP') ?>', {
    pekerjaan_id: id,
    is_cc: 'y',
  }, function(json) {
    $.each(json, function(index, val) {
      $('#' + index).val(val);
      $('#id_user_staf_ifc').append('<option selected value="' + val.id_user + '">' + val.pegawai_nama + '</option>');
    });
  });

  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserKoorIFC') ?>', {
    pekerjaan_id: id,
    status: '<?= $_GET['status'] ?>'
  }, function(json, result) {
    if (json) {
      if (json.id_penanggung_jawab == 'n') {
        $('#id_user_staf_ifc').prop('disabled', true);
      } else if (json.id_penanggung_jawab == 'y' && json.id_user != $('#session_user').val()) {
        $('#id_user_staf_ifc').prop('disabled', false);
      } else if (json.id_penanggung_jawab == 'y' && json.id_user == $('#session_user').val()) {
        $('#id_user_staf_ifc').prop('disabled', false);
      }
    }
  });

    /* EASYUI */
    /*IFC NON HPS*/
  setTimeout(function() {
    $('#dg_document_ifc').edatagrid({
      url: '<?= base_url("project/pekerjaan_usulan/getAsetDocumentIFC?pekerjaan_dokumen_status=n&id_pekerjaan=") ?>' + id + '&pekerjaan_status=' + val + '&id_create=' + $('#session_user').val() + '&is_hps=n',
      saveUrl: '<?= base_url('project/pekerjaan_usulan/insertAsetDocumentIFC') ?>',
      updateUrl: '<?= base_url('project/pekerjaan_usulan/updateAsetDocumentIFC') ?>',
      rowStyler: function(index, row) {
        if (row.pekerjaan_dokumen_status == '0') {
          return 'background-color:#FF0000;font-weight:bold;';
        }
      },
      onAdd: function(index, row) {
        var up = $(this).datagrid('getEditor', {
          index: index,
          field: 'urutan_proyek_nama'
        });
        $(up.target).textbox('setValue', row.urutan_proyek_nama);

        var sa = $(this).datagrid('getEditor', {
          index: index,
          field: 'section_area_nama'
        });
        $(sa.target).textbox('setValue', row.section_area_nama);
      },
      onBeginEdit: function(index, row) {
          /* Combobox */
          /*template*/
        var cb = $(this).datagrid('getEditor', {
          index: index,
          field: 'pekerjaan_template_nama'
        });
        $(cb.target).combobox('setValue', row.pekerjaan_template_id);
          /*template*/
          /*bidang*/
        var bd = $(this).datagrid('getEditor', {
          index: index,
          field: 'bidang_nama'
        });
        $(bd.target).combobox('setValue', row.bidang_id);
          /*bidang*/
        var up = $(this).datagrid('getEditor', {
          index: index,
          field: 'urutan_proyek_nama'
        });
        $(up.target).textbox('setValue', row.urutan_proyek_nama);

        var sa = $(this).datagrid('getEditor', {
          index: index,
          field: 'section_area_nama'
        });
        $(sa.target).textbox('setValue', row.section_area_nama);
          /* Combobox */

          /* File */
        var ed = $(this).datagrid('getEditor', {
          index: index,
          field: 'pekerjaan_dokumen_file'
        });
        row.fileName = row.fileName || row.pekerjaan_dokumen_file;
        $(ed.target).filebox('setText', row.fileName);
          /* File */
      },
      onEndEdit: function(index, row) {
        var e = $(this).datagrid('getEditor', {
          index: index,
          field: 'pekerjaan_dokumen_file'
        });
        var files = $(e.target).filebox('files');
        if (files.length) row.savedFileName = e.target.filebox('getText');
      },
      columns: [
        [{
          field: 'tanggal_dokumen_input',
          title: 'Waktu Input',
          width: '20%',
          editor: {
            type: 'label',
          },
        },
        {
          field: 'pekerjaan_template_nama',
          title: 'Kategori',
          width: '20%',
          editor: {
            type: 'combobox',
            options: {
              required: true,
              idField: 'pekerjaan_template_id',
              textField: 'pekerjaan_template_nama',
              valueField: 'pekerjaan_template_id',
              url: '<?= base_url() ?>project/pekerjaan_usulan/getTemplatePekerjaan',
              mode: 'remote',
              fitColumns: true,
              columns: [
                [{
                  field: 'pekerjaan_template_nama',
                  title: 'Template Dokumen',
                  width: 400
                }, ]
                ],
              panelHeight: 135
            },
          },
        },
        {
          field: 'pekerjaan_dokumen_nama',
          title: 'Sub Kategori',
          width: '20%',
          editor: {
            type: 'textbox',
            options: {
              required: true,
              onchange: function(value) {
                $("#doc_nama").val(value);
              }
            }
          },
        },
        {
          field: 'pekerjaan_dokumen_jenis',
          title: 'Gambar / Dokumen',
          width: '20%',
          editor: {
            type: 'combobox',
            options: {
              required: true,
              idField: 'pekerjaan_dokumen_jenis',
              textField: 'pekerjaan_dokumen_jenis',
              valueField: 'pekerjaan_dokumen_jenis',
              url: '<?= base_url() ?>project/pekerjaan_usulan/getPekerjaanDokumenJenis',
              mode: 'remote',
              fitColumns: true,
              columns: [
                [{
                  field: 'pekerjaan_dokumen_jenis',
                  title: 'Gambar / Dokumen',
                  width: 400
                }, ]
                ],
              panelHeight: 135
            },
          },
        },
        {
          field: 'bidang_nama',
          title: 'Bidang',
          width: '20%',
          editor: {
            type: 'combobox',
            options: {
              required: true,
              idField: 'bidang_id',
              textField: 'bidang_nama',
              valueField: 'bidang_id',
              url: '<?= base_url() ?>project/pekerjaan_usulan/getBidang',
              mode: 'remote',
              fitColumns: true,
              columns: [
                [{
                  field: 'bidang_nama',
                  title: 'Bidang',
                  width: 400
                }, ]
                ],
              panelHeight: 135
            },
          },
        },
        {
          field: 'urutan_proyek_nama',
          title: 'Urutan Proyek',
          width: '20%',
          editor: {
            type: 'textbox',
            options: {
              required: false,
              readonly: true,
            }
          },
        },
        {
          field: 'section_area_nama',
          title: 'Section Area',
          width: '20%',
          editor: {
            type: 'textbox',
            options: {
              required: false,
              readonly: true,
            }
          },
        },
        {
          field: 'pekerjaan_dokumen_nomor',
          title: 'No Dokumen',
          width: '20%',
          editor: {
            type: 'label',
            options: {
              required: false,
            }
          },
        },
            // {
            //   field: 'pekerjaan_dokumen_jumlah',
            //   title: 'Jumlah Halaman',
            //   width: '20%',
            //   editor: {
            //     type: 'numberbox',
            //     options: {
            //       required: true,
            //     }
            //   },
            // },
        {
          field: 'pekerjaan_dokumen_kertas',
          title: 'Ukuran Kertas',
          width: '20%',
          editor: {
            type: 'combobox',
            options: {
              required: true,
              idField: 'pekerjaan_dokumen_kertas',
              textField: 'pekerjaan_dokumen_kertas',
              valueField: 'pekerjaan_dokumen_kertas',
              url: '<?= base_url() ?>project/pekerjaan_usulan/getPekerjaanDokumenKertas',
              mode: 'remote',
              fitColumns: true,
              columns: [
                [{
                  field: 'pekerjaan_dokumen_kertas',
                  title: 'Ukuran Kertas',
                  width: 400
                }, ]
                ],
              panelHeight: 135
            },
          },
        },
        {
          field: 'pekerjaan_dokumen_orientasi',
          title: 'Orientasi Kertas',
          width: '20%',
          editor: {
            type: 'combobox',
            options: {
              required: true,
              idField: 'pekerjaan_dokumen_orientasi',
              textField: 'pekerjaan_dokumen_orientasi',
              valueField: 'pekerjaan_dokumen_orientasi',
              url: '<?= base_url() ?>project/pekerjaan_usulan/getPekerjaanDokumenOrientasi',
              mode: 'remote',
              fitColumns: true,
              columns: [
                [{
                  field: 'pekerjaan_dokumen_orientasi',
                  title: 'Orientasi Kertas',
                  width: 400
                }, ]
                ],
              panelHeight: 135
            },
          },
        },
        {
          field: 'pekerjaan_dokumen_file',
          title: 'File',
          width: '20%',
          formatter: (value, row) => row.fileName || value,
          editor: {
            type: 'filebox',
            options: {
              required: true,
              accept: 'application/pdf,.xls,.xlsx',
              buttonText: '...',
              onChange: function() {
                var self = $(this);
                var files = self.filebox('files');
                var formData = new FormData();
                var nama = $("#doc_nama_ifc").val();
                self.filebox('setText', 'Menyimpan...');

                formData.append('id_pekerjaan', $('#id_pekerjaan_ifc').val());

                for (var i = 0; i < files.length; i++) {
                  var file = files[i];
                  formData.append('file', file, file.name);
                }

                $.ajax({
                  url: '<?= base_url('project/pekerjaan_usulan/insertFilePekerjaanDokumen') ?>',
                  type: 'post',
                  data: formData,
                  contentType: false,
                  processData: false,
                  beforeSend: function() {
                    $.messager.progress({
                      title: 'Uploading',
                      msg: 'Uploading file...',
                      interval: 1000
                    });
                  },
                  complete: function() {
                    $.messager.progress('close');
                  },
                  success: function(data) {
                    self.filebox('setText', data);
                  }
                })
              }
            },
          },
        },
        {
          field: 'pekerjaan_dokumen_id',
          title: 'Lihat',
          width: '20%',
          formatter: function(value, row, index) {
            if (row.pekerjaan_dokumen_file) {
              return '<a href="#" onclick="viewFile(\'' + row.pekerjaan_dokumen_file + '\')">Lihat File</a>';
            } else {
              return '-';
            }
          },

        },

        ],
],
});
}, 500);
}
  /*IFC NON HPS*/
  /* EASYUI */
  /* Klik Upload Dokumen IFC */

  /* Klik Upload Dokumen IFC */
function fun_upload_ifc_hps(id, val) {
  $('#id_pekerjaan_ifc_hps').val(id);
  $('#modal_upload_ifc_hps').modal('show');
  $('#id_user_staf_ifc_hps').empty();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserStafVP') ?>', {
    pekerjaan_id: id,
    is_cc: 'h',
  }, function(json) {
    $.each(json, function(index, val) {
      $('#' + index).val(val);
      $('#id_user_staf_ifc_hps').append('<option selected value="' + val.id_user + '">' + val.pegawai_nama + '</option>');
    });
  });

  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserKoorIFC') ?>', {
    pekerjaan_id: id,
    status: '<?= $_GET['status'] ?>'
  }, function(json, result) {
    if (json) {
      if (json.id_penanggung_jawab == 'n') {
        $('#id_user_staf_ifc_hps').prop('disabled', true);
      } else if (json.id_penanggung_jawab == 'y' && json.id_user != $('#session_user').val()) {
        $('#id_user_staf_ifc_hps').prop('disabled', false);
      } else if (json.id_penanggung_jawab == 'y' && json.id_user == $('#session_user').val()) {
        $('#id_user_staf_ifc_hps').prop('disabled', false);
      }
    }
  });

    /* EASYUI */
    /*IFC HPS*/
  setTimeout(function() {
    $('#dg_document_ifc_hps').edatagrid({
      url: '<?= base_url("project/pekerjaan_usulan/getAsetDocumentIFC?pekerjaan_dokumen_status=n&id_pekerjaan=") ?>' + id + '&pekerjaan_status=' + val + '&id_create=' + $('#session_user').val() + '&is_hps=y',
      saveUrl: '<?= base_url('project/pekerjaan_usulan/insertAsetDocumentIFC') ?>',
      updateUrl: '<?= base_url('project/pekerjaan_usulan/updateAsetDocumentIFC') ?>',
      rowStyler: function(index, row) {
        if (row.pekerjaan_dokumen_status == '0') {
          return 'background-color:#FF0000;font-weight:bold;';
        }
      },
      onAdd: function(index, row) {
        var up = $(this).datagrid('getEditor', {
          index: index,
          field: 'urutan_proyek_nama'
        });
        $(up.target).textbox('setValue', row.urutan_proyek_nama);

        var sa = $(this).datagrid('getEditor', {
          index: index,
          field: 'section_area_nama'
        });
        $(sa.target).textbox('setValue', row.section_area_nama);
      },
      onBeginEdit: function(index, row) {
          /* Combobox */
          /*template*/
        var cb = $(this).datagrid('getEditor', {
          index: index,
          field: 'pekerjaan_template_nama'
        });
        $(cb.target).combobox('setValue', row.pekerjaan_template_id);
          /*template*/
          /*bidang*/
        var bd = $(this).datagrid('getEditor', {
          index: index,
          field: 'bidang_nama'
        });
        $(bd.target).combobox('setValue', row.bidang_id);
          /*bidang*/
        var up = $(this).datagrid('getEditor', {
          index: index,
          field: 'urutan_proyek_nama'
        });
        $(up.target).textbox('setValue', row.urutan_proyek_nama);

        var sa = $(this).datagrid('getEditor', {
          index: index,
          field: 'section_area_nama'
        });
        $(sa.target).textbox('setValue', row.section_area_nama);
          /* Combobox */

          /* File */
        var ed = $(this).datagrid('getEditor', {
          index: index,
          field: 'pekerjaan_dokumen_file'
        });
        row.fileName = row.fileName || row.pekerjaan_dokumen_file;
        $(ed.target).filebox('setText', row.fileName);
          /* File */
      },
      onEndEdit: function(index, row) {
        var e = $(this).datagrid('getEditor', {
          index: index,
          field: 'pekerjaan_dokumen_file'
        });
        var files = $(e.target).filebox('files');
        if (files.length) row.savedFileName = e.target.filebox('getText');
      },
      columns: [
        [{
          field: 'tanggal_dokumen_input',
          title: 'Waktu Input',
          width: '20%',
          editor: {
            type: 'label',
          },
        },
            // {
            //   field: 'pekerjaan_template_nama',
            //   title: 'Kategori',
            //   width: '20%',
            //   editor: {
            //     type: 'combobox',
            //     options: {
            //       required: true,
            //       idField: 'pekerjaan_template_id',
            //       textField: 'pekerjaan_template_nama',
            //       valueField: 'pekerjaan_template_id',
            //       url: '<?= base_url() ?>project/pekerjaan_usulan/getTemplatePekerjaan?is_hps=true',
            //       mode: 'remote',
            //       fitColumns: true,
            //       columns: [
            //         [{
            //           field: 'pekerjaan_template_nama',
            //           title: 'Template Dokumen',
            //           width: 400
            //         }, ]
            //         ],
            //       panelHeight: 135,
            //       onLoadSuccess: function(data) {
            //         if (data.length > 0) {
            //           $(this).combobox('setValue', data[0].pekerjaan_template_id);
            //         }
            //       }
            //     },
            //   },
            // },
        {
          field: 'pekerjaan_dokumen_nama',
          title: 'Sub Kategori',
          width: '20%',
          editor: {
            type: 'textbox',
            options: {
              required: true,
              onchange: function(value) {
                $("#doc_nama").val(value);
              }
            }
          },
        },
            // {
            //   field: 'pekerjaan_dokumen_jenis',
            //   title: 'Gambar / Dokumen',
            //   width: '20%',
            //   editor: {
            //     type: 'combobox',
            //     options: {
            //       required: true,
            //       idField: 'pekerjaan_dokumen_jenis',
            //       textField: 'pekerjaan_dokumen_jenis',
            //       valueField: 'pekerjaan_dokumen_jenis',
            //       url: '<?= base_url() ?>project/pekerjaan_usulan/getPekerjaanDokumenJenis?is_hps=true',
            //       mode: 'remote',
            //       fitColumns: true,
            //       columns: [
            //         [{
            //           field: 'pekerjaan_dokumen_jenis',
            //           title: 'Gambar / Dokumen',
            //           width: 400
            //         }, ]
            //         ],
            //       panelHeight: 135,
            //       onLoadSuccess: function(data) {
            //         if (data.length > 0) {
            //           $(this).combobox('setValue', data[0].pekerjaan_dokumen_jenis);
            //         }
            //       }
            //     },
            //   },
            // },
        {
          field: 'bidang_nama',
          title: 'Bidang',
          width: '20%',
          editor: {
            type: 'combobox',
            options: {
              required: true,
              idField: 'bidang_id',
              textField: 'bidang_nama',
              valueField: 'bidang_id',
              url: '<?= base_url() ?>project/pekerjaan_usulan/getBidang',
              mode: 'remote',
              fitColumns: true,
              columns: [
                [{
                  field: 'bidang_nama',
                  title: 'Bidang',
                  width: 400
                }, ]
                ],
              panelHeight: 135
            },
          },
        },

        {
          field: 'urutan_proyek_nama',
          title: 'Urutan Proyek',
          width: '20%',
          editor: {
            type: 'textbox',
            options: {
              required: false,
              readonly: true,
            }
          },
        },
        {
          field: 'section_area_nama',
          title: 'Section Area',
          width: '20%',
          editor: {
            type: 'textbox',
            options: {
              required: false,
              readonly: true,
            }
          },
        },
        {
          field: 'pekerjaan_dokumen_nomor',
          title: 'No Dokumen',
          width: '20%',
          editor: {
            type: 'label',
            options: {
              required: false,
            }
          },
        },
            // {
            //   field: 'pekerjaan_dokumen_kertas',
            //   title: 'Ukuran Kertas',
            //   width: '20%',
            //   editor: {
            //     type: 'combobox',
            //     options: {
            //       required: true,
            //       idField: 'pekerjaan_dokumen_kertas',
            //       textField: 'pekerjaan_dokumen_kertas',
            //       valueField: 'pekerjaan_dokumen_kertas',
            //       url: '<?= base_url() ?>project/pekerjaan_usulan/getPekerjaanDokumenKertas?is_hps=true',
            //       mode: 'remote',
            //       fitColumns: true,
            //       columns: [
            //         [{
            //           field: 'pekerjaan_dokumen_kertas',
            //           title: 'Ukuran Kertas',
            //           width: 400
            //         }, ]
            //         ],
            //       panelHeight: 135
            //     },
            //   },
            // },
            // {
            //   field: 'pekerjaan_dokumen_orientasi',
            //   title: 'Orientasi Kertas',
            //   width: '20%',
            //   editor: {
            //     type: 'combobox',
            //     options: {
            //       required: true,
            //       idField: 'pekerjaan_dokumen_orientasi',
            //       textField: 'pekerjaan_dokumen_orientasi',
            //       valueField: 'pekerjaan_dokumen_orientasi',
            //       url: '<?= base_url() ?>project/pekerjaan_usulan/getPekerjaanDokumenOrientasi?is_hps=true',
            //       mode: 'remote',
            //       fitColumns: true,
            //       columns: [
            //         [{
            //           field: 'pekerjaan_dokumen_orientasi',
            //           title: 'Orientasi Kertas',
            //           width: 400
            //         }, ]
            //         ],
            //       panelHeight: 135,
            //       onLoadSuccess: function(data) {
            //         if (data.length > 0) {
            //           $(this).combobox('setValue', data[0].pekerjaan_dokumen_orientasi);
            //         }
            //       }
            //     },
            //   },
            // },
        {
          field: 'pekerjaan_dokumen_file',
          title: 'File',
          width: '20%',
          formatter: (value, row) => row.fileName || value,
          editor: {
            type: 'filebox',
            options: {
              required: true,
              accept: 'application/pdf,.xls,.xlsx',
              buttonText: '...',
              onChange: function() {
                var self = $(this);
                var files = self.filebox('files');
                var formData = new FormData();
                var nama = $("#doc_nama_ifc").val();
                self.filebox('setText', 'Menyimpan...');

                formData.append('id_pekerjaan', $('#id_pekerjaan_ifc').val());

                for (var i = 0; i < files.length; i++) {
                  var file = files[i];
                  formData.append('file', file, file.name);
                }

                $.ajax({
                  url: '<?= base_url('project/pekerjaan_usulan/insertFilePekerjaanDokumen') ?>',
                  type: 'post',
                  data: formData,
                  contentType: false,
                  processData: false,
                  beforeSend: function() {
                    $.messager.progress({
                      title: 'Uploading',
                      msg: 'Uploading file...',
                      interval: 1000
                    });
                  },
                  complete: function() {
                    $.messager.progress('close');
                  },
                  success: function(data) {
                    self.filebox('setText', data);
                  }
                })
              }
            },
          },
        },
        {
          field: 'pekerjaan_dokumen_id',
          title: 'Lihat',
          width: '20%',
          formatter: function(value, row, index) {
            if (row.pekerjaan_dokumen_file) {
              return '<a href="#" onclick="viewFile(\'' + row.pekerjaan_dokumen_file + '\')">Lihat File</a>';
            } else {
              return '-';
            }
          },

        },
        ],
],
});
}, 500);
}
  /*IFC HPS*/
  /* EASYUI */
  /* Klik Upload Dokumen IFC */

  /* Klik Approve Berjalan */
function fun_approve_berjalan(id, text) {
  Swal.fire({
    title: text,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya"
  }).then(function(result) {
    if (result.value) {
      $.getJSON('<?= base_url('project/pekerjaan_usulan/prosesApproveBerjalan') ?>', {
        pekerjaan_id: id,
        pekerjaan_status: $('#pekerjaan_status').val(),
        id_user_staf: $('#id_user_staf').val()
      }, function(json) {
        console.log('ok');
      });
      setTimeout(() => {
        if ($('#is_rkap').val() == '1') {
          window.location.replace('<?= base_url('project/RKAP#berjalan') ?>')
        } else {
          window.location.replace('<?= base_url('project/Non_RKAP#berjalan') ?>')
        }
      }, "1000");
    }
  });
}
  /* Klik Approve Berjalan */

  /* Klik Approve Berjalan */
function fun_approve_berjalan_ifa_rev(id, text) {
  Swal.fire({
    title: text,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya"
  }).then(function(result) {
    if (result.value) {
        // call_ajax_page('project/pekerjaan_berjalan');
      setTimeout(() => {
        if ($('#is_rkap').val() == '1') {
          window.location.replace('<?= base_url('project/RKAP#berjalan') ?>')
        } else {
          window.location.replace('<?= base_url('project/Non_RKAP#berjalan') ?>')
        }
      }, "1000");
      $.getJSON('<?= base_url('project/pekerjaan_usulan/prosesApproveBerjalanIFARev') ?>', {
        pekerjaan_id: id,
        pekerjaan_status: $('#pekerjaan_status').val(),
        id_user_staf: $('#id_user_staf').val()
      }, function(json) {
        console.log('ok');
      });
    }
  });
}
  /* Klik Approve Berjalan */

function fun_approve_berjalan_ifc(id, text) {
  Swal.fire({
    title: text,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya"
  }).then(function(result) {
    if (result.value) {
      $.getJSON('<?= base_url('project/pekerjaan_usulan/prosesApproveBerjalanIFC') ?>', {
        pekerjaan_id: id,
        pekerjaan_status: $('#pekerjaan_status').val(),
        id_user_staf: $('#id_user_staf_ifc').val()
      }, function(json) {
        console.log('ok');
      });
      setTimeout(() => {
        if ($('#is_rkap').val() == '1') {
          window.location.replace('<?= base_url('project/RKAP#berjalan') ?>')
        } else {
          window.location.replace('<?= base_url('project/Non_RKAP#berjalan') ?>')
        }
      }, "2500");
    }
  });
}

  /* Klik Approve Berjalan */
function fun_approve_berjalan_hps(id, text) {
  Swal.fire({
    title: text,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya"
  }).then(function(result) {
    if (result.value) {
        // call_ajax_page('project/pekerjaan_berjalan');
      setTimeout(() => {
        if ($('#is_rkap').val() == '1') {
          window.location.replace('<?= base_url('project/RKAP#berjalan') ?>')
        } else {
          window.location.replace('<?= base_url('project/Non_RKAP#berjalan') ?>')
        }
      }, "1000");
      $.getJSON('<?= base_url('project/pekerjaan_usulan/prosesApproveBerjalanHPS') ?>', {
        pekerjaan_id: id,
        pekerjaan_status: $('#pekerjaan_status').val(),
        id_user_staf: $('#id_user_staf_hps').val()
      }, function(json) {
        console.log('ok');
      });
    }
  });
}
  /* Klik Approve Berjalan */

  /*Ketika reject di bagian IFC*/
function fun_reject_berjalan_ifc(id) {
  Swal.fire({
    title: "Anda Yakin Reject?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya"
  }).then(function(result) {
    if (result.value) {
      $.getJSON('<?= base_url('project/pekerjaan_usulan/prosesRejectBerjalanIFC') ?>', {
        pekerjaan_id: id,
        pekerjaan_status: '<?= $_GET['status'] ?>',
      }, function(json) {
        console.log('ok');
      });
      setTimeout(() => {
        if ($('#is_rkap').val() == '1') {
          window.location.replace('<?= base_url('project/RKAP#berjalan') ?>')
        } else {
          window.location.replace('<?= base_url('project/Non_RKAP#berjalan') ?>')
        }
      }, "1000");
    }
  });
}
  /*Ketika reject di bagian IFC*/

  /* Klik Reject Berjalan */
function fun_reject_berjalan_ifa(id) {
  Swal.fire({
    title: "Anda Yakin Reject?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya"
  }).then(function(result) {
    if (result.value) {
      $.getJSON('<?= base_url('project/pekerjaan_usulan/prosesRejectBerjalanIFA') ?>', {
        pekerjaan_id: id,
        status: '<?= $_GET['status'] ?>'
      }, function(json) {
        console.log('ok');
      });
      setTimeout(() => {
        if ($('#is_rkap').val() == '1') {
          window.location.replace('<?= base_url('project/RKAP#berjalan') ?>')
        } else {
          window.location.replace('<?= base_url('project/Non_RKAP#berjalan') ?>')
        }
      }, "1000");
    }
  });
}

function fun_reject_berjalan(id) {
  Swal.fire({
    title: "Anda Yakin Reject?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya"
  }).then(function(result) {
    if (result.value) {
        // call_ajax_page('project/pekerjaan_berjalan');
      setTimeout(() => {
        if ($('#is_rkap').val() == '1') {
          window.location.replace('<?= base_url('project/RKAP#berjalan') ?>')
        } else {
          window.location.replace('<?= base_url('project/Non_RKAP#berjalan') ?>')
        }
      }, "1000");
      $.getJSON('<?= base_url('project/pekerjaan_usulan/prosesRejectBerjalan') ?>', {
        pekerjaan_id: id
      }, function(json) {
        console.log('ok');
      });
    }
  });
}
  /* Klik Reject Berjalan */

  /* Klik Approve IFA pic */
function fun_approve_ifa(id, text) {
  $('#cc_approve_ifa').empty();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserStafVP') ?>', {
    pekerjaan_id: id,
    is_cc: 'y',
  }, function(json) {
    $.each(json, function(index, val) {
      $('#' + index).val(val);
      $('#cc_approve_ifa').append('<option selected value="' + val.id_user + '">' + val.pegawai_nama + '</option>');
    });
  });

  $('#cc_approve_ifa_hps').empty();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserStafVP') ?>', {
    pekerjaan_id: id,
    is_cc: 'h',
  }, function(json) {
    $.each(json, function(index, val) {
      $('#' + index).val(val);
      $('#cc_approve_ifa_hps').append('<option selected value="' + val.id_user + '">' + val.pegawai_nama + '</option>');
    });
  });
  $('#modal_approve_ifa').modal('show');
  $('#simpan_approve_ifa_pic').css('display', 'inline-block');
  $('#simpan_approve_ifa_avp').css('display', 'none');
  $('#simpan_approve_ifa_vp').css('display', 'none');
}
  /* Klik Approve IFA pic */

  /*SUBMIT APPROVE IFA PIC*/
$('#simpan_approve_ifa_pic').on('click', function() {
  Swal.fire({
    title: 'Apakah Anda Yakin Approved?',
    html: `<input type="text" id="pekerjaan_note" class="swal2-input" placeholder="Note">`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya",
    focusConfirm: false,
    preConfirm: () => {
      const pekerjaan_note = Swal.getPopup().querySelector('#pekerjaan_note').value
        // if (!pekerjaan_note) {
        //   Swal.showValidationMessage(`Note Tidak Boleh Kosong`)
        // }
      return {
        pekerjaan_note: pekerjaan_note
      }
    }
  }).then(function(result) {
    if (result.value) {
      var pekerjaan_note = result.value.pekerjaan_note;
      $.post('<?= base_url('project/pekerjaan_usulan/prosesApproveIFA') ?>', {
        pekerjaan_id: '<?= $this->input->get('pekerjaan_id'); ?>',
        pekerjaan_status: $('#pekerjaan_status').val(),
        cc: $('#cc_approve_ifa').val(),
        cc_hps: $('#cc_approve_ifa_hps').val(),
        pekerjaan_note: pekerjaan_note,

      }, function(json) {
        $('#loading_form_approve_ifa').show();
        $('#simpan_approve_ifa_pic').hide();
      });
      setTimeout(() => {
        if ($('#is_rkap').val() == '1') {
          window.location.replace('<?= base_url('project/RKAP#ifa') ?>')
        } else {
          window.location.replace('<?= base_url('project/Non_RKAP#ifa') ?>')
        }
      }, "1000");
    }
  });
})
  /*SUBMIT APPROVE IFA PIC*/

  /* Klik Approve IFA AVP*/
function fun_approve_ifa_avp(id, text) {
  $('#cc_approve_ifa').empty();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserStafVP') ?>', {
    pekerjaan_id: id,
    is_cc: 'y',
  }, function(json) {
    $.each(json, function(index, val) {
      $('#' + index).val(val);
      $('#cc_approve_ifa').append('<option selected value="' + val.id_user + '">' + val.pegawai_nama + '</option>');
    });
  });

  $('#cc_approve_ifa_hps').empty();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserStafVP') ?>', {
    pekerjaan_id: id,
    is_cc: 'h',
  }, function(json) {
    $.each(json, function(index, val) {
      $('#' + index).val(val);
      $('#cc_approve_ifa_hps').append('<option selected value="' + val.id_user + '">' + val.pegawai_nama + '</option>');
    });
  });
  $('#modal_approve_ifa').modal('show');
  $('#simpan_approve_ifa_pic').css('display', 'none');
  $('#simpan_approve_ifa_avp').css('display', 'inline-block');
  $('#simpan_approve_ifa_vp').css('display', 'none');
}

  /* Klik Approve IFA AVP*/

  /*Submit Approve IFA AVP*/
$('#simpan_approve_ifa_avp').on('click', function() {
  Swal.fire({
    title: 'Apakah Anda Yakin Approved?',
    html: `<input type="text" id="pekerjaan_note" class="swal2-input" placeholder="Note">`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya",
    preConfirm: () => {
      const pekerjaan_note = Swal.getPopup().querySelector('#pekerjaan_note').value
        // if (!pekerjaan_note) {
        //   Swal.showValidationMessage(`Note Tidak Boleh Kosong`)
        // }
      return {
        pekerjaan_note: pekerjaan_note
      }
    }
  }).then(function(result) {
    if (result.value) {
      var pekerjaan_note = result.value.pekerjaan_note;
      $.post('<?= base_url('project/pekerjaan_usulan/prosesApproveIFAAVP') ?>', {
        pekerjaan_id: '<?= $this->input->get('pekerjaan_id'); ?>',
        pekerjaan_status: $('#pekerjaan_status').val(),
        cc: $('#cc_approve_ifa').val(),
        cc_hps: $('#cc_approve_ifa_hps').val(),
        pekerjaan_note: pekerjaan_note,
      }, function(json) {
        $('#loading_form_approve_ifa').show();
        $('#simpan_approve_ifa_avp').hide();
      });
      setTimeout(() => {
        if ($('#is_rkap').val() == '1') {
          window.location.replace('<?= base_url('project/RKAP#ifa') ?>')
        } else {
          window.location.replace('<?= base_url('project/Non_RKAP#ifa') ?>')
        }
      }, "1000");
    }
  });
})
  /*Submit Approve IFA AVP*/

  /* Klik Approve IFA VP*/
function fun_approve_ifa_vp(id, text) {
  $('#cc_approve_ifa').empty();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserStafVP') ?>', {
    pekerjaan_id: id,
    is_cc: 'y',
  }, function(json) {
    $.each(json, function(index, val) {
      $('#' + index).val(val);
      $('#cc_approve_ifa').append('<option selected value="' + val.id_user + '">' + val.pegawai_nama + '</option>');
    });
  });

  $('#cc_approve_ifa_hps').empty();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserStafVP') ?>', {
    pekerjaan_id: id,
    is_cc: 'h',
  }, function(json) {
    $.each(json, function(index, val) {
      $('#' + index).val(val);
      $('#cc_approve_ifa_hps').append('<option selected value="' + val.id_user + '">' + val.pegawai_nama + '</option>');
    });
  });
  $('#modal_approve_ifa').modal('show');
  $('#simpan_approve_ifa_pic').css('display', 'none');
  $('#simpan_approve_ifa_avp').css('display', 'none');
  $('#simpan_approve_ifa_vp').css('display', 'inline-block');
}
  /* Klik Approve IFA VP*/

  /*Submit Approve IFA VP*/
$('#simpan_approve_ifa_vp').on('click', function() {
  Swal.fire({
    title: 'Apakah Anda Yakin Approved?',
    html: `<input type="text" id="pekerjaan_note" class="swal2-input" placeholder="Note">`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya",
    preConfirm: () => {
      const pekerjaan_note = Swal.getPopup().querySelector('#pekerjaan_note').value
        // if (!pekerjaan_note) {
        //   Swal.showValidationMessage(`Note Tidak Boleh Kosong`)
        // }
      return {
        pekerjaan_note: pekerjaan_note
      }
    }
  }).then(function(result) {
    if (result.value) {
      var pekerjaan_note = result.value.pekerjaan_note;
      $.post('<?= base_url('project/pekerjaan_usulan/prosesApproveIFAVP') ?>', {
        pekerjaan_id: '<?= $this->input->get('pekerjaan_id'); ?>',
        pekerjaan_status: $('#pekerjaan_status').val(),
        cc: $('#cc_approve_ifa').val(),
        cc_hps: $('#cc_approve_ifa_hps').val(),
        pekerjaan_note: pekerjaan_note,
      }, function(json) {
        $('#loading_form_approve_ifa').show();
        $('#simpan_approve_ifa_vp').hide();
        console.log('ok');
      });
      setTimeout(() => {
        if ($('#is_rkap').val() == '1') {
          window.location.replace('<?= base_url('project/RKAP#ifa') ?>')
        } else {
          window.location.replace('<?= base_url('project/Non_RKAP#ifa') ?>')
        }
      }, "1000");
    }
  });
})
  /*Submit Approve IFA VP*/

  /* Klik Reject IFA */
function fun_reject_ifa(id) {
  Swal.fire({
    title: "Anda Yakin Reject?",
    html: `<input type="text" id="pekerjaan_note" class="swal2-input" placeholder="Note">`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya",
    preConfirm: () => {
      const pekerjaan_note = Swal.getPopup().querySelector('#pekerjaan_note').value
      if (!pekerjaan_note) {
        Swal.showValidationMessage(`Note Tidak Boleh Kosong`)
      }
      return {
        pekerjaan_note: pekerjaan_note
      }
    }
  }).then(function(result) {
    if (result.value) {
      var pekerjaan_note = result.value.pekerjaan_note;
      $.getJSON('<?= base_url('project/pekerjaan_usulan/prosesRejectIFA') ?>', {
        pekerjaan_id: id,
        pekerjaan_note: pekerjaan_note,
      }, function(json) {});
      setTimeout(() => {
        if ($('#is_rkap').val() == '1') {
          window.location.replace('<?= base_url('project/RKAP#ifa') ?>')
        } else {
          window.location.replace('<?= base_url('project/Non_RKAP#ifa') ?>')
        }
      }, "1000");
    }
  });
}
  /* Klik Reject IFA */

  /* Klik Aksi Dokumen Pekerjaan */
function fun_aksi(id) {
  window.location.href = '<?= base_url('project/pekerjaan_usulan/dokumenAksi?pekerjaan_dokumen_id=') ?>' + id + '&aksi=usulan';

    // $('#modal_aksi').modal('show');
    // $('#div_keterangan').hide();
    // $.getJSON('<?= base_url('project/pekerjaan_usulan/') ?>getDokumenAksi?pekerjaan_dokumen_id=' + id, function(json) {
    // $('#pekerjaan_dokumen_id_temp').val(id);
    // $('#aksi_nama').val(json.pekerjaan_dokumen_nama);
    // $('#pekerjaan_status_dokumen').val('<?= $_GET['status'] ?>');
    // renderToolPDF();
    // loadPDFAksi(json.pekerjaan_dokumen_file);
    // });
}
  /* Klik Aksi Dokumen Pekerjaan */



  /* Klik Aksi Dokumen Pekerjaan IFA (CC) */
function fun_aksi_cc(id) {
  window.location.href = '<?= base_url('project/pekerjaan_usulan/dokumenAksi?pekerjaan_dokumen_id=') ?>' + id + '&aksi=cc';
    // $('#modal_aksi_cc').modal('show');
    // $('#div_keterangan_cc').hide();
    // $.getJSON('<?= base_url('project/pekerjaan_usulan/') ?>getDokumenAksi?pekerjaan_dokumen_id=' + id, function(json) {
    //   $('#pekerjaan_dokumen_id_temp_cc').val(id);
    //   $('#aksi_nama_cc').val(json.pekerjaan_dokumen_nama);
    // });
}
  /* Klik Aksi Dokumen Pekerjaan IFA (CC) */

  /* Klik Aksi Dokumen Pekerjaan Staf */
function fun_aksi_staf(id) {
  $('#modal_aksi_staf').modal('show');
  $('#div_keterangan_staf').hide();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/') ?>getDokumenAksi?pekerjaan_dokumen_id=' + id, function(json) {
    $('#pekerjaan_dokumen_id_temp_staf').val(id);
    $('#aksi_nama_staf').val(json.pekerjaan_dokumen_nama);
  });
}
  /* Klik Aksi Dokumen Pekerjaan Staf */

  /* Klik Aksi Dokumen Pekerjaan IFA */
function fun_aksi_ifa(id) {
  window.location.href = '<?= base_url('project/pekerjaan_usulan/dokumenAksi?pekerjaan_dokumen_id=') ?>' + id + '&aksi=ifa';
    // $('#modal_aksi_ifa').modal('show');
    // $('#div_keterangan_ifa').hide();
    // $.getJSON('<?= base_url('project/pekerjaan_usulan/') ?>getDokumenAksi?pekerjaan_dokumen_id=' + id, function(json) {
    //   $('#pekerjaan_dokumen_id_temp_ifa').val(id);
    //   $('#aksi_nama_ifa').val(json.pekerjaan_dokumen_nama);
    //   $('#pekerjaan_dokumen_status_ifa').val(json.pekerjaan_dokumen_status);
    // });
}
  /* Klik Aksi Dokumen Pekerjaan IFA */

  /* Klik Aksi Dokumen Pekerjaan IFA (CC) */
function fun_aksi_ifa_cc(id) {
  $('#modal_aksi_ifa_cc').modal('show');
  $('#div_keterangan_ifa_cc').hide();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/') ?>getDokumenAksi?pekerjaan_dokumen_id=' + id, function(json) {
    $('#pekerjaan_dokumen_id_temp_ifa_cc').val(id);
    $('#aksi_nama_ifa_cc').val(json.pekerjaan_dokumen_nama);
    $('#pekerjaan_dokumen_status_ifa_cc').val(json.pekerjaan_dokumen_status);
  });
}
  /* Klik Aksi Dokumen Pekerjaan IFA (CC) */

  /* Klik Aksi Dokumen Pekerjaan IFC*/
function fun_aksi_ifc(id) {
  window.location.href = '<?= base_url('project/pekerjaan_usulan/dokumenAksi?pekerjaan_dokumen_id=') ?>' + id + '&aksi=ifc';
    // $('#modal_aksi_ifc').modal('show');
    // $('#div_keterangan').hide();
    // $.getJSON('<?= base_url('project/pekerjaan_usulan/') ?>getDokumenAksi?pekerjaan_dokumen_id=' + id, function(json) {
    // $('#pekerjaan_dokumen_id_temp_ifc').val(id);
    // $('#aksi_nama_ifc').val(json.pekerjaan_dokumen_nama);
    // });
}
  /* Klik Aksi Dokumen Pekerjaan IFC*/
  /* KLIK */

  /* PROSES */
  /* Simpan Disposisi VP */
$("#form_modal_vp").on("submit", function(e) {
  var url = '<?= base_url('project/pekerjaan_usulan/disposisiVP') ?>';
  if ($('#id_tanggung_jawab_vp').val() == null) {
    $('#id_tanggung_jawab_vp_alert').show();
  } else {
    $('#id_tanggung_jawab_vp_alert').hide();
  }
  if ($('#id_user_vp').val() == '') {
    $('#id_user_vp_alert').show();
  } else {
    $('#id_user_vp_alert').hide();
  }
  e.preventDefault();
  if ($('#id_tanggung_jawab_vp').val() != null && $('#id_user_vp').val() != null) {
    $.ajax({
      url: url,
      data: $('#form_modal_vp').serialize(),
      type: 'POST',
      dataType: 'html',
      beforeSend: function() {
        $('#loading_form_vp').css('display', 'block');
        $('#simpan_vp').css('display', 'none');
      },
      complete: function() {
        $('#loading_form_vp').hide();
        $('#simpan_vp').show();
      },
      success: function(isi) {
        $('#close_vp').click();
        setTimeout(() => {
          if ($('#is_rkap').val() == '1') {
            window.location.replace('<?= base_url('project/RKAP') ?>')
          } else {
            window.location.replace('<?= base_url('project/Non_RKAP') ?>');
          }
        }, "1000");
        toastr.success('Berhasil');
      }
    });
  }
});
  /* Simpan Disposisi VP */

  /* Simpan Reviewed AVP */
$("#form_modal_avp").on("submit", function(e) {
  var url = '<?= base_url('project/pekerjaan_usulan/disposisiAVP') ?>';
  e.preventDefault();
  Swal.fire({
    title: 'Pastikan Sudah Terisi Dengan Benar!',
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya"
  }).then(function(result) {
    if (result.value) {
      $.ajax({
        url: url,
        data: $('#form_modal_avp').serialize(),
        type: 'POST',
        dataType: 'html',
        beforeSend: function() {
          $('#loading_form_avp').css('display', 'block');
          $('#simpan_avp').css('display', 'none');
        },
        complete: function() {
          $('#loading_form_avp').hide();
          $('#simpan_avp').show();
        },
        success: function(isi) {
          $('#close_avp').click();
          setTimeout(() => {
            if ($('#is_rkap').val() == '1') {
              window.location.replace('<?= base_url('project/RKAP') ?>');
            } else {
              window.location.replace('<?= base_url('project/Non_RKAP') ?>');
            }
          }, "1000");
          toastr.success('Berhasil');
        }
      });
    }
  })
});
  /* Simpan Reviewed AVP */

  // ganti perencana aksi
$('#form_modal_ganti_perencana').on('submit', function(e) {
  var data = new FormData();
  data.append('id_pekerjaan', $('#id_pekerjaan_ganti_perencana').val());
  data.append('pekerjaan_status', $('#pekerjaan_status_ganti_perencana').val());
  data.append('id_user_perencana', $('#id_perencana_baru').val());

  e.preventDefault();
  $.ajax({
    url: '<?= base_url('project/pekerjaan_usulan/gantiPerencana') ?>',
    data: data,
    dataType: 'HTML',
    type: 'POST',
    processData: false,
    contentType: false,
    beforeSend: function() {
      $('#loading_form_ganti_perencana').css('display', 'block');
      $('#simpan_ganti_perencana').css('display', 'none');
    },
    complete: function() {
      $('#loading_form_ganti_perencana').hide();
      $('#simpan_ganti_perencana').show();
    },
    success: function(isi) {
      $('#close_ganti_perencana').click();
        // call_ajax_page('project/pekerjaan_usulan');
      setTimeout(() => {
        if ($('#is_rkap').val() == '1') {
          window.location.replace('<?= base_url('project/RKAP') ?>')
        } else {
          window.location.replace('<?= base_url('project/Non_RKAP') ?>');
        }
      }, "1000");
      toastr.success('Berhasil');
    }
  })
})
  // ganti perencana aksi

  /*ganti koor aksi*/
$('#form_modal_ganti_koor').on('submit', function(e) {
  var data = new FormData();
  data.append('id_pekerjaan', $('#id_pekerjaan_ganti_koor').val());
  data.append('pekerjaan_status', $('#pekerjaan_status_ganti_koor').val());
  data.append('id_koor', $('#id_koor_baru').val());

  if ($('#pekerjaan_status_ganti_koor').val() == '4') {
    var url = '<?= base_url('project/pekerjaan_usulan/gantiKoor') ?>'
  } else if ($('#pekerjaan_status_ganti_koor').val() == '5') {
    var url = '<?= base_url('project/pekerjaan_usulan/gantiKoorPerencana') ?>'
  }

  e.preventDefault();
  $.ajax({
    url: url,
    data: data,
    dataType: 'HTML',
    type: 'POST',
    processData: false,
    contentType: false,
    beforeSend: function() {
      $('#loading_form_ganti_koor').css('display', 'block');
      $('#simpan_ganti_koor').css('display', 'none');
    },
    complete: function() {
      $('#loading_form_ganti_koor').hide();
      $('#simpan_ganti_koor').show();
    },
    success: function(isi) {
      $('#close_ganti_koor').click();
        // call_ajax_page('project/pekerjaan_usulan');
      setTimeout(() => {
        if ($('#is_rkap').val() == '1') {
          window.location.replace('<?= base_url('project/RKAP') ?>')
        } else {
          window.location.replace('<?= base_url('project/Non_RKAP') ?>');
        }
      }, "1000");
      toastr.success('Berhasil');
    }
  })
})
  // ganti perencana aksi

  /*submit penomoran*/
$('#form_modal_penomoran').on('submit', function(e) {
  e.preventDefault();
  var data = new FormData($('#form_modal_penomoran')[0]);
  var url = '<?= base_url('project/pekerjaan_usulan/insertPenomoranDokumen') ?>'
  $.ajax({
    url: url,
    type: 'POST',
    dataType: 'HTML',
    data: data,
    contentType: false,
    processData: false,
    cache: false,
    success: function() {
      $('#close_penomoran').click();
    }
  })

})
  /*submit penomoran*/

  /* Simpan Progress Pekerjaan Staf */
  // $("#form_modal_progress").on("submit", function(e) {
  //   var url = '<?= base_url('project/pekerjaan_usulan/updateProgressPekerjaan') ?>';
  //   e.preventDefault();

  //   var data = $('#form_modal_progress').serialize();
  //   $.ajax({
  //     url: url,
  //     data: data,
  //     type: 'POST',
  //     dataType: 'html',
  //     beforeSend: function() {
  //       $('#loading_form_progress').css('display', 'block');
  //       $('#simpan_progress').css('display', 'none');
  //       $('#edit_progress').css('display', 'none');
  //     },
  //     complete: function() {
  //       $('#loading_form_progress').hide();
  //       $('#simpan_progress').show();
  //     },
  //     success: function(isi) {
  //       $('#close_progress').click();
  //       toastr.success('Berhasil');
  //     }
  //   });
  // });
$('#simpan_progress').on('click', function(e) {
  var url = '<?= base_url('project/pekerjaan_usulan/insertProgressPekerjaan') ?>';
  e.preventDefault();

  var data = $('#form_modal_progress').serialize();
  $.ajax({
    url: url,
    data: data,
    type: 'POST',
    dataType: 'html',
    beforeSend: function() {
      $('#loading_form_progress').css('display', 'block');
      $('#simpan_progress').css('display', 'none');
      $('#edit_progress').css('display', 'none');
    },
    complete: function() {
      $('#loading_form_progress').hide();
      $('#simpan_progress').show();
    },
    success: function(isi) {
      $('#close_progress').click();
      toastr.success('Berhasil');
    }
  });
})
  /* Simpan Progress Pekerjaan Staf */

$('#edit_progress').on('click', function(e) {
  var url = '<?= base_url('project/pekerjaan_usulan/updateProgressPekerjaan') ?>';
  e.preventDefault();

  var data = $('#form_modal_progress').serialize();
  $.ajax({
    url: url,
    data: data,
    type: 'POST',
    dataType: 'html',
    beforeSend: function() {
      $('#loading_form_progress').css('display', 'block');
      $('#simpan_progress').css('display', 'none');
      $('#edit_progress').css('display', 'none');
    },
    complete: function() {
      $('#loading_form_progress').hide();
      $('#simpan_progress').show();
    },
    success: function(isi) {
      $('#close_progress').click();
      toastr.success('Berhasil');
    }
  });
})



  /* Send IFA */
function fun_send_ifa_rev() {
    // e.preventDefault();

  fun_approve_berjalan_ifa_rev('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>', 'Apakah Anda Yakin Send?');
  $('#close_dokumen').click();
};
  /* Send IFA */

  /* Send IFA */
$('#form_upload_dokumen').on('submit', function(e) {
  e.preventDefault();
  var isi_awal = $('#dg_document').data('datagrid').data.rows[0];
  if ($('#dg_document').data('datagrid').data.total > 0 && 'isNewRecord' in isi_awal) {
    $.messager.alert('Peringatan', 'Data Dokumen Masih Ada Yang Belum Tersimpan');
  } else {
    fun_approve_berjalan('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>', 'Apakah Anda Yakin Send?');
    $('#close_dokumen').click();
  }
});
  /* Send IFA */

  /* Send IFA HPS */
$('#form_upload_dokumen_hps').on('submit', function(e) {
  e.preventDefault();

  fun_approve_berjalan_hps('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>', 'Apakah Anda Yakin Send?');
  $('#close_dokumen_hps').click();
});
  /* Send IFA HPS*/

  /* Send IFA IFC*/
$('#form_upload_dokumen_ifc').on('submit', function(e) {
  e.preventDefault();
  var isi_awal = $('#dg_document_ifc').data('datagrid').data.rows[0];
  if ($('#dg_document_ifc').data('datagrid').data.total > 0 && 'isNewRecord' in isi_awal) {
    $.messager.alert('Peringatan', 'Data Dokumen Masih Ada Yang Belum Tersimpan');
  } else {
    fun_approve_berjalan_ifc('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>', 'Apakah Anda Yakin Send?');
    $('#close_dokumen_ifc').click();
  }
});
  /* Send IFA IFC*/

  /* Simpan Aksi Dokumen Pekerjaan */
$("#form_modal_aksi").on("submit", function(e) {
  var url = '<?= base_url('project/pekerjaan_usulan/simpanAksi') ?>';

  var aksi_file = $('#aksi_file').prop('files')[0];
  var data = new FormData();

  data.append('pekerjaan_dokumen_file', aksi_file);
  data.append('pekerjaan_status', $('#pekerjaan_status_dokumen').val());
  data.append('pekerjaan_dokumen_id', $('#pekerjaan_dokumen_id_temp').val());
  data.append('pekerjaan_dokumen_status', $('#aksi_status').val());
  data.append('pekerjaan_dokumen_keterangan', $('#aksi_keterangan').val());

  e.preventDefault();
  $.ajax({
    url: url,
    data: data,
    type: 'POST',
    dataType: 'html',
    processData: false,
    contentType: false,
    beforeSend: function() {
      $('#loading_form_aksi').css('display', 'block');
      $('#simpan_aksi').css('display', 'none');
    },
    complete: function() {
      $('#loading_form_aksi').hide();
      $('#simpan_aksi').show();
    },
    success: function(isi) {
      $('#close_aksi').click();
      $('#table_dokumen').DataTable().ajax.reload(null, false);
      $('#table_dokumen_hps').DataTable().ajax.reload(null, false);
      $('#table_dokumen_ifc').DataTable().ajax.reload(null, false);
      fun_cekRevisi('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>');
    }
  });
});
  /* Simpan Aksi Dokumen Pekerjaan */

  /* Simpan Aksi Dokumen Pekerjaan IFA */
$("#form_modal_aksi_cc").on("submit", function(e) {
  var url = '<?= base_url('project/pekerjaan_usulan/simpanAksiCC') ?>';

  var aksi_file = $('#aksi_file_cc').prop('files')[0];
  var data = new FormData();

  data.append('pekerjaan_dokumen_file_cc', aksi_file);
  data.append('pekerjaan_dokumen_id', $('#pekerjaan_dokumen_id_temp_cc').val());
  data.append('pekerjaan_dokumen_status', $('#aksi_status_cc').val());
  data.append('pekerjaan_dokumen_keterangan', $('#aksi_keterangan_cc').val());

  e.preventDefault();
  $.ajax({
    url: url,
    data: data,
    type: 'POST',
    dataType: 'html',
    processData: false,
    contentType: false,
    beforeSend: function() {
      $('#loading_form_aksi_cc').css('display', 'block');
      $('#simpan_aksi_cc').css('display', 'none');
    },
    complete: function() {
      $('#loading_form_aksi_cc').hide();
      $('#simpan_aksi_cc').show();
    },
    success: function(isi) {
      $('#close_aksi_cc').click();
      $('#table_dokumen_ifa').DataTable().ajax.reload(null, false);
      $('#table_dokumen_hps').DataTable().ajax.reload(null, false);
      fun_cekRevisiIFA('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>');
    }
  });
});
  /* Simpan Aksi Dokumen Pekerjaan IFA */

  /* Simpan Aksi Dokumen Pekerjaan Staf */
$("#form_modal_aksi_staf").on("submit", function(e) {
  var url = '<?= base_url('project/pekerjaan_usulan/simpanAksiStaf') ?>';

  var aksi_file = $('#aksi_file_staf').prop('files')[0];
  var data = new FormData();

  data.append('pekerjaan_dokumen_file', aksi_file);
  data.append('pekerjaan_dokumen_id', $('#pekerjaan_dokumen_id_temp_staf').val());
  data.append('pekerjaan_dokumen_status', $('#aksi_status_staf').val());
  data.append('pekerjaan_dokumen_keterangan', $('#aksi_keterangan_staf').val());

  e.preventDefault();
  $.ajax({
    url: url,
    data: data,
    type: 'POST',
    dataType: 'html',
    processData: false,
    contentType: false,
    beforeSend: function() {
      $('#loading_form_aksi_staf').css('display', 'block');
      $('#simpan_aksi_staf').css('display', 'none');
    },
    complete: function() {
      $('#loading_form_aksi_staf').hide();
      $('#simpan_aksi_staf').show();
    },
    success: function(isi) {
      $('#close_aksi_staf').click();
      $('#table_dokumen').DataTable().ajax.reload(null, false);
      $('#table_dokumen_hps').DataTable().ajax.reload(null, false);
      $('#table_dokumen_ifc').DataTable().ajax.reload(null, false);
    }
  });
});
  /* Simpan Aksi Dokumen Pekerjaan Staf */

  /* Simpan Aksi Dokumen Pekerjaan IFA */
$("#form_modal_aksi_ifa").on("submit", function(e) {
  var url = '<?= base_url('project/pekerjaan_usulan/simpanAksiIFA') ?>';

  var aksi_file = $('#aksi_file_ifa').prop('files')[0];
  var data = new FormData();

  data.append('pekerjaan_dokumen_file', aksi_file);
  data.append('pekerjaan_dokumen_id', $('#pekerjaan_dokumen_id_temp_ifa').val());
  data.append('pekerjaan_dokumen_status', $('#aksi_status_ifa').val());
  data.append('pekerjaan_dokumen_keterangan', $('#aksi_keterangan_ifa').val());
  data.append('pekerjaan_dokumen_status_nomor', $('#pekerjaan_dokumen_status_ifa').val());

  e.preventDefault();
  $.ajax({
    url: url,
    data: data,
    type: 'POST',
    dataType: 'html',
    processData: false,
    contentType: false,
    beforeSend: function() {
      $('#loading_form_aksi_ifa').css('display', 'block');
      $('#simpan_aksi_ifa').css('display', 'none');
    },
    complete: function() {
      $('#loading_form_aksi_ifa').hide();
      $('#simpan_aksi_ifa').show();
    },
    success: function(isi) {
      $('#close_aksi_ifa').click();
      $('#table_dokumen_ifa').DataTable().ajax.reload(null, false);
      $('#table_dokumen_ifa_hps').DataTable().ajax.reload(null, false);
      fun_cekRevisiIFA('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>');
    }
  });
});
  /* Simpan Aksi Dokumen Pekerjaan IFA */

  /* Simpan Aksi Dokumen Pekerjaan IFA */
$("#form_modal_aksi_ifa_cc").on("submit", function(e) {
  var url = '<?= base_url('project/pekerjaan_usulan/simpanAksiIFACC') ?>';

  var aksi_file = $('#aksi_file_ifa_cc').prop('files')[0];
  var data = new FormData();

  data.append('pekerjaan_dokumen_file_cc', aksi_file);
  data.append('pekerjaan_dokumen_id', $('#pekerjaan_dokumen_id_temp_ifa_cc').val());
  data.append('pekerjaan_dokumen_status', $('#aksi_status_ifa_cc').val());
  data.append('pekerjaan_dokumen_keterangan', $('#aksi_keterangan_ifa_cc').val());

  e.preventDefault();
  $.ajax({
    url: url,
    data: data,
    type: 'POST',
    dataType: 'html',
    processData: false,
    contentType: false,
    beforeSend: function() {
      $('#loading_form_aksi_ifa_cc').css('display', 'block');
      $('#simpan_aksi_ifa_cc').css('display', 'none');
    },
    complete: function() {
      $('#loading_form_aksi_ifa_cc').hide();
      $('#simpan_aksi_ifa_cc').show();
    },
    success: function(isi) {
      $('#close_aksi_ifa_cc').click();
      $('#table_dokumen_ifa').DataTable().ajax.reload(null, false);
      $('#table_dokumen_ifa_hps').DataTable().ajax.reload(null, false);
      fun_cekRevisiIFA('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>');
    }
  });
});
  /* Simpan Aksi Dokumen Pekerjaan IFA */

  /* Simpan Aksi Dokumen Pekerjaan IFC */
$("#form_modal_aksi_ifc").on("submit", function(e) {
  var url = '<?= base_url('project/pekerjaan_usulan/simpanAksiIFC') ?>';

  var aksi_file = $('#aksi_file_ifc').prop('files')[0];
  var data = new FormData();

  data.append('pekerjaan_dokumen_file', aksi_file);
  data.append('pekerjaan_dokumen_id', $('#pekerjaan_dokumen_id_temp_ifc').val());
  data.append('pekerjaan_dokumen_status', $('#aksi_status_ifc').val());
  data.append('pekerjaan_dokumen_keterangan', $('#aksi_keterangan_ifc').val());

  e.preventDefault();
  $.ajax({
    url: url,
    data: data,
    type: 'POST',
    dataType: 'html',
    processData: false,
    contentType: false,
    beforeSend: function() {
      $('#loading_form_aksi_ifc').css('display', 'block');
      $('#simpan_aksi_ifc').css('display', 'none');
    },
    complete: function() {
      $('#loading_form_aksi_ifc').hide();
      $('#simpan_aksi_ifc').show();
    },
    success: function(isi) {
      $('#close_aksi_ifc').click();
      $('#table_dokumen_ifc').DataTable().ajax.reload(null, false);
      $('#table_dokumen_ifc_hps').DataTable().ajax.reload(null, false);
      fun_cekRevisiIFA('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>');
    }
  });
});
  /* Simpan Aksi Dokumen Pekerjaan IFC */

  /*Proses Send VP */
$('#form_modal_send_vp').on('submit', function(e) {
  e.preventDefault();
  var url = '<?= base_url('project/pekerjaan_usulan/prosesSendVP') ?>';
  $.ajax({
    url: url,
    data: {
      id_pekerjaan_send_vp: $('#id_pekerjaan_send_vp').val(),
      pekerjaan_status: $('#pekerjaan_status').val(),
      id_user_send_vp: $('#id_user_send_vp').val(),
      id_user_send_vp_hps: $('#id_user_send_vp_hps').val(),
    },
    type: 'POST',
    dataType: 'HTML',
      // processData: false,
      // contentType: false,
    beforeSend: function() {
      $('#loading_form_send_vp').css('display', 'block');
      $('#simpan_send_vp').css('display', 'none');
    },
    complete: function() {
      $('#loading_form_send_vp').hide();
      $('#simpan_send_vp').show();
    },
    success: function(isi) {
      $('#close_send_vp').click();
      setTimeout(() => {
        if ($('#is_rkap').val() == '1') {
          // window.location.replace('<?= base_url('project/RKAP') ?>')
        } else {
          // window.location.replace('<?= base_url('project/Non_RKAP') ?>');
        }
      }, "1000");
    }
  })
    // }
    // });
})
  /*Proses Send VP */

  /*Proses Send AVP Koor*/
$('#form_modal_send_vp_koor').on('submit', function(e) {
  e.preventDefault();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getStatusKoor') ?>', {
    pekerjaan_id: $('#id_pekerjaan_send_vp').val()
  }, function(json) {
    $.each(json, function(index, val) {
      if (val.total > 0 && val.total != val.total_koor) {
        Swal.fire(
          'Peringatan!',
          'Masih Ada ' + val.total + ' Dokumen Yang Belum Siap!',
          'warning'
          );
      } else {
        var url = '<?= base_url('project/pekerjaan_usulan/prosesSendVP') ?>';
        $.ajax({
          url: url,
            // data: $('#form_modal_send_vp').serialize(),
          data: {
            id_pekerjaan_send_vp: $('#id_pekerjaan_send_vp_koor').val(),
            pekerjaan_status: $('#pekerjaan_status').val(),
            id_user_send_vp: $('#id_user_send_vp_koor').val(),
            id_user_send_vp_hps: $('#id_user_send_vp_koor_hps').val(),
          },
          type: 'POST',
          dataType: 'HTML',
            // processData: false,
            // contentType: false,
          beforeSend: function() {
            $('#loading_form_send_vp_koor').css('display', 'block');
            $('#simpan_send_vp_koor').css('display', 'none');
          },
          complete: function() {
            $('#loading_form_send_vp_koor').hide();
            $('#simpan_send_vp_koor').show();
          },
          success: function(isi) {
            $('#close_send_vp_koor').click();
              // call_ajax_page('project/pekerjaan_usulan');
            setTimeout(() => {
              if ($('#is_rkap').val() == '1') {
                window.location.replace('<?= base_url('project/RKAP') ?>')
              } else {
                window.location.replace('<?= base_url('project/Non_RKAP') ?>');
              }
            }, "2500");
              // $('#table_dokumen_ifa').DataTable().ajax.reload(null, false);
          }
        })
      }
    })
  });
})
  /*Proses Send AVP Koor*/

  /*Proses Send VP */
$('#form_modal_send_avp_ifc').on('submit', function(e) {
  e.preventDefault();
  var url = '<?= base_url('project/pekerjaan_usulan/prosesSendAVPIFC') ?>';
  $.ajax({
    url: url,
      // data: $('#form_modal_send_vp').serialize(),
    data: {
      id_pekerjaan: $('#id_pekerjaan_send_avp_ifc').val(),
      pekerjaan_status: $('#pekerjaan_status').val(),
      id_user_cc: $('#id_user_send_avp_ifc').val(),
      id_user_cc_hps: $('#id_user_send_avp_ifc_hps').val(),
    },
    type: 'POST',
    dataType: 'HTML',
      // processData: false,
      // contentType: false,
    beforeSend: function() {
      $('#loading_form_send_avp_ifc').css('display', 'block');
      $('#simpan_send_avp_ifc').css('display', 'none');
    },
    complete: function() {
      $('#loading_form_send_avp_ifc').hide();
      $('#simpan_send_avp_ifc').show();
    },
    success: function(isi) {
      $('#close_send_avp_ifc').click();
      setTimeout(() => {
        if ($('#is_rkap').val() == '1') {
          window.location.replace('<?= base_url('project/RKAP') ?>')
        } else {
          window.location.replace('<?= base_url('project/Non_RKAP') ?>');
        }
      }, "1000");
    }
  })
})
  /*Proses Send VP */

  /*Proses Send VP */
$('#form_modal_send_vp_ifc').on('submit', function(e) {
  e.preventDefault();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getStatusKoorIFC') ?>', {
    pekerjaan_id: $('#id_pekerjaan_send_vp_ifc').val()
  }, function(json) {
    $.each(json, function(index, val) {
      if (val.total > 0 && val.total != val.total_koor) {
        Swal.fire(
          'Peringatan!',
          'Masih Ada ' + val.total + ' Dokumen Yang Belum Siap!',
          'warning'
          );
      } else {
        var url = '<?= base_url('project/pekerjaan_usulan/prosesSendVPIFC') ?>';
        $.ajax({
          url: url,
          data: {
            id_pekerjaan_send_vp: $('#id_pekerjaan_send_vp_ifc').val(),
            pekerjaan_status: $('#pekerjaan_status').val(),
            id_user_cc: $('#id_user_send_vp_ifc').val(),
            id_user_cc_hps: $('#id_user_send_vp_ifc_hps').val(),
          },
          type: 'POST',
          dataType: 'HTML',
          beforeSend: function() {
            $('#loading_form_send_vp_ifc').css('display', 'block');
            $('#simpan_send_vp_ifc').css('display', 'none');
          },
          complete: function() {
            $('#loading_form_send_vp_ifc').hide();
            $('#simpan_send_vp_ifc').show();
          },
          success: function(isi) {
            $('#close_send_vp_ifc').click();
            setTimeout(() => {
              if ($('#is_rkap').val() == '1') {
                window.location.replace('<?= base_url('project/RKAP') ?>')
              } else {
                window.location.replace('<?= base_url('project/Non_RKAP') ?>');
              }
            }, "1000");
          }
        })
      }
    });

  })
})
  /*Proses Send VP */

$('#form_modal_approve_vp').on('submit', function(e) {
  e.preventDefault();
  var url = '<?= base_url('project/pekerjaan_usulan/prosesApproveVP') ?>';
  $.ajax({
    url: url,
    data: {
      id_pekerjaan_approve_vp: $('#id_pekerjaan_approve_vp').val(),
      pekerjaan_status: $('#pekerjaan_status').val(),
      id_user_cc: $('#id_user_approve_vp').val(),
      id_user_cc_hps: $('#id_user_approve_vp_hps').val(),

    },
    type: 'POST',
    dataType: 'HTML',
      // processData: false,
      // contentType: false,
    beforeSend: function() {
      $('#loading_form_approve_vp').css('display', 'block');
      $('#simpan_approve_vp').css('display', 'none');
    },
    complete: function() {
      $('#loading_form_approve_vp').hide();
      $('#simpan_approve_vp').show();
    },
    success: function(isi) {
      $('#close_approve_vp').click();
        // call_ajax_page('project/pekerjaan_usulan');
      setTimeout(() => {
        if ($('#is_rkap').val() == '1') {
          window.location.replace('<?= base_url('project/RKAP') ?>')
        } else {
          window.location.replace('<?= base_url('project/Non_RKAP') ?>');
        }
      }, "1000");
        // $('#table_dokumen_ifa').DataTable().ajax.reload(null, false);
    }
  })
})
  /* PROSES */





  /* CLOSE */
  /* Close Modal Disposisi VP */
$('#modal_vp').on('hidden.bs.modal', function(e) {
  fun_close_vp();
});

function fun_close_vp() {
  $('#form_modal_vp')[0].reset();
  $('#modal_vp').modal('hide');
  $('#id_tanggung_jawab_vp_alert').hide();
  $('#id_user_vp_alert').hide();
}
  /* Close Modal Disposisi VP */

  /* Close Modal Reviewed AVP */
$('#modal_avp').on('hidden.bs.modal', function(e) {
  fun_close_avp();
});

function fun_close_avp() {
  $('#form_modal_avp')[0].reset();
  $('#modal_avp').modal('hide');
  $("#id_user_vp_avp").val('');
}
  /* Close Modal Reviewed AVP */

  /* Close Modal Progress Pekerjaan Staf */
$('#modal_progress').on('hidden.bs.modal', function(e) {
  fun_close_progress();
});

function fun_close_progress() {
  $('#form_modal_progress')[0].reset();
  $('#modal_progress').modal('hide');
}
  /* Close Modal Progress Pekerjaan Staf */

  /* Close Modal Progress Pekerjaan Staf */
$('#modal_ganti_perencana').on('hidden.bs.modal', function(e) {
  fun_close_ganti_perencana();
});

function fun_close_ganti_perencana() {
  $('#form_modal_ganti_perencana')[0].reset();
  $('#modal_ganti_perencana').modal('hide');
}
  /* Close Modal Progress Pekerjaan Staf */

  /* Close Modal Progress Pekerjaan Staf */
$('#modal_ganti_koor').on('hidden.bs.modal', function(e) {
  fun_close_ganti_koor();
});

function fun_close_ganti_koor() {
  $('#form_modal_ganti_koor')[0].reset();
  $('#modal_ganti_koor').modal('hide');
}
  /* Close Modal Progress Pekerjaan Staf */

  /* Close Modal Penomoran*/
$('#modal_penomoran').on('hidden.bs.modal', function(e) {
  fun_close_penomoran();
});

function fun_close_penomoran() {
  $('#modal_penomoran').modal('hide');
  $('#form_modal_penomoran')[0].reset();
  $('#urutan_proyek_penomoran').empty();
  $('#section_area_penomoran').empty();
  fun_cek_penomoran('<?= $this->input->get('pekerjaan_id') ?>');
  location.reload();
}
  /* Close Modal Penomoran*/


  /* Close Modal Upload Dokumen*/
$('#modal_upload').on('hidden.bs.modal', function(e) {
  fun_close_dokumen();
});

function fun_close_dokumen() {
  $('#modal_upload').modal('hide');
  $('#table_dokumen').DataTable().ajax.reload();
  $('#table_dokumen_hps').DataTable().ajax.reload();
  $('#table_dokumen_ifc').DataTable().ajax.reload(null, false);
  $('#table_dokumen_ifc_hps').DataTable().ajax.reload(null, false);
}
  /* Close Modal Upload Dokumen*/

  /* Close Modal Upload Dokumen HPS */
$('#modal_upload_hps').on('hidden.bs.modal', function(e) {
  fun_close_dokumen_hps();
});

function fun_close_dokumen_hps() {
  $('#modal_upload_hps').modal('hide');
  $('#table_dokumen').DataTable().ajax.reload();
  $('#table_dokumen_hps').DataTable().ajax.reload();
}
  /* Close Modal Upload Dokumen HPS */


  /* Close Modal Upload Dokumen IFC */
$('#modal_upload_ifc').on('hidden.bs.modal', function(e) {
  fun_close_dokumen_ifc();
});

function fun_close_dokumen_ifc() {
  $('#modal_upload_ifc').modal('hide');
  $('#table_dokumen_ifc').DataTable().ajax.reload();
}
  /* Close Modal Upload Dokumen IFC */

  /* Close Modal Upload Dokumen IFC */
$('#modal_upload_ifc_hps').on('hidden.bs.modal', function(e) {
  fun_close_dokumen_ifc_hps();
});

function fun_close_dokumen_ifc_hps() {
  $('#modal_upload_ifc_hps').modal('hide');
  $('#table_dokumen_ifc_hps').DataTable().ajax.reload();
  $('#table_dokumen_ifa_hps').DataTable().ajax.reload();
}
  /* Close Modal Upload Dokumen IFC */

  /* Close Modal Aksi Dokumen Pekerjaan */
$('#modal_aksi').on('hidden.bs.modal', function(e) {
  fun_close_aksi();
});

function fun_close_aksi() {
  $('#form_modal_aksi')[0].reset();
  $('#modal_aksi').modal('hide');
  $('#table_dokumen').DataTable().ajax.reload(null, false);
  $('#table_dokumen_hps').DataTable().ajax.reload();
}
  /* Close Modal Aksi Dokumen Pekerjaan */

  /* Close Modal Aksi Dokumen Pekerjaan CC */
$('#modal_aksi_cc').on('hidden.bs.modal', function(e) {
  fun_close_aksi_cc();
});

function fun_close_aksi_cc() {
  $('#form_modal_aksi_cc')[0].reset();
  $('#modal_aksi_cc').modal('hide');
  $('#table_dokumen').DataTable().ajax.reload(null, false);
  $('#table_dokumen_hps').DataTable().ajax.reload(null, false);
}
  /* Close Modal Aksi Dokumen Pekerjaan CC */

  /* Close Modal Aksi Dokumen Pekerjaan Staf */
$('#modal_aksi_staf').on('hidden.bs.modal', function(e) {
  fun_close_aksi_staf();
});



function fun_close_aksi_staf() {
  $('#form_modal_aksi_staf')[0].reset();
  $('#modal_aksi_staf').modal('hide');
  $('#table_dokumen').DataTable().ajax.reload(null, false);
  $('#table_dokumen_hps').DataTable().ajax.reload();
}
  /* Close Modal Aksi Dokumen Pekerjaan Staf */

  /* Close Modal Aksi Dokumen Pekerjaan IFA */
$('#modal_aksi_ifa').on('hidden.bs.modal', function(e) {
  fun_close_aksi_ifa();
});

function fun_close_aksi_ifa() {
  $('#form_modal_aksi_ifa')[0].reset();
  $('#modal_aksi_ifa').modal('hide');
  $('#table_dokumen_ifa').DataTable().ajax.reload(null, false);
  $('#table_dokumen_hps').DataTable().ajax.reload(null, false);
}
  /* Close Modal Aksi Dokumen Pekerjaan IFA */

  /* Close Modal Aksi Dokumen Pekerjaan IFA CC */
$('#modal_aksi_ifa_cc').on('hidden.bs.modal', function(e) {
  fun_close_aksi_ifa_cc();
});

function fun_close_aksi_ifa_cc() {
  $('#form_modal_aksi_ifa_cc')[0].reset();
  $('#modal_aksi_ifa_cc').modal('hide');
  $('#table_dokumen_ifa').DataTable().ajax.reload(null, false);
  $('#table_dokumen_hps').DataTable().ajax.reload(null, false);
}
  /* Close Modal Aksi Dokumen Pekerjaan IFA CC */

  /* Close Modal Aksi Dokumen Pekerjaan IFC */
$('#modal_aksi_ifc').on('hidden.bs.modal', function(e) {
  fun_close_aksi_ifc();
})

function fun_close_aksi_ifc() {
  $('#form_modal_aksi_ifc')[0].reset();
  $('#modal_aksi_ifc').modal('hide');
  $('#table_dokumen_ifc').DataTable().ajax.reload(null, false);

}

  /* Close Modal Aksi Dokumen Pekerjaan IFC */

  /* Close Modal Lihat Dokumen */
$('#modal_lihat').on('hidden.bs.modal', function(e) {
  fun_close_lihat();
});

function fun_close_lihat() {
  $('#modal_lihat').modal('hide');
}
  /* Close Modal Lihat Dokumen */

  /* Close Modal Lihat Dokumen */
$('#modal_history').on('hidden.bs.modal', function(e) {
  fun_close_history();
});

function fun_close_history() {
  $('#modal_history').modal('hide');
}
  /* Close Modal Lihat Dokumen */

$('#modal_send_vp').on('hidden.bs.modal', function(e) {
  fun_close_send_vp();
})

function fun_close_send_vp() {
  $('#modal_send_vp').modal('hide');
}

$('#modal_send_vp_koor').on('hidden.bs.modal', function(e) {
  fun_close_send_vp_koor();
})

function fun_close_send_vp_koor() {
  $('#modal_send_vp_koor').modal('hide');
}

$('#modal_send_avp_ifc').on('hidden.bs.modal', function(e) {
  fun_close_send_avp_ifc();
})

function fun_close_send_avp_ifc() {
  $('#modal_send_avp_ifc').modal('hide');
}


$('#modal_send_vp_ifc').on('hidden.bs.modal', function(e) {
  fun_close_send_vp_ifc();
})

function fun_close_send_vp_ifc() {
  $('#modal_send_vp_ifc').modal('hide');
}

$('#modal_approve_vp').on('hidden.bs.modal', function(e) {
  fun_close_approve_vp();
})

function fun_close_approve_vp() {
  $('#modal_approve_vp').modal('hide');
}

$('#modal_nilai_hps').on('hidden.bs.modal', function(e) {
  fun_close_nilai_hps();
})

function fun_close_nilai_hps() {
  $('#modal_nilai_hps').modal('hide');
  $('#form_modal_nilai_hps')[0].reset();
}
  /* CLOSE */

  /* EASYUI */
  /* Dokumen Pekerjaan */
  /* Fun Tambah */
function fun_tambah_document() {
  var isi_awal = $('#dg_document').data('datagrid').data.rows[0];
  if ($('#dg_document').data('datagrid').data.total > 0 && 'isNewRecord' in isi_awal) {
    $.messager.alert('Peringatan', 'Data Dokumen Masih Ada Yang Belum Tersimpan');
  } else {
    var id = '<?= $this->input->get('pekerjaan_id') ?>';
    $.getJSON('<?= base_url('project/pekerjaan_usulan/getPenomoranDokumen') ?>', {
      id_pekerjaan: id,
      single: 'y'
    }, function(json, textStatus) {
      $('#dg_document').edatagrid('addRow', {
        index: 0,
        row: {
          pekerjaan_id: id,
          is_hps: 'n',
          urutan_proyek_nama: json.urutan_proyek_nama,
          section_area_nama: json.section_area_nama
        }
      });
    });
  }
}
  /* Fun Tambah */

  /* Fun Simpan */
function fun_simpan_document() {
  $('#dg_document').edatagrid('saveRow');
  setTimeout(() => {
    $('#dg_document').datagrid('reload');
  }, 1000);
}
  /* Fun Simpan */

  /* Fun Hapus */
function fun_hapus_document() {
  var row = $('#dg_document').datagrid('getSelected');
  $.post('<?= base_url('/project/pekerjaan_usulan/deletePekerjaanDokumen') ?>', {
    pekerjaan_dokumen_id: row.pekerjaan_dokumen_id
  }, function(data, textStatus, xhr) {
    $('#dg_document').datagrid('reload');
  });
}
  /* Fun Hapus */
  /* Dokumen Pekerjaan */
  /* EASYUI */

  /* EASYUI HPS*/
  /* Dokumen Pekerjaan */
  /* Fun Tambah */
function fun_tambah_document_hps() {
  var isi_awal = $('#dg_document_hps').data('datagrid').data.rows[0];
  if ($('#dg_document_hps').data('datagrid').data.total > 0 && 'isNewRecord' in isi_awal) {
    $.messager.alert('Peringatan', 'Data Dokumen Masih Ada Yang Belum Tersimpan');
  } else {
    var id = '<?= $this->input->get('pekerjaan_id') ?>';
    $.getJSON('<?= base_url('project/pekerjaan_usulan/getPenomoranDokumen') ?>', {
      id_pekerjaan: id,
      single: 'y'
    }, function(json, textStatus) {
      $('#dg_document_hps').edatagrid('addRow', {
        index: 0,
        row: {
          pekerjaan_id: id,
          is_hps: 'y',
          urutan_proyek_nama: json.urutan_proyek_nama,
          section_area_nama: json.section_area_nama,
        }
      });
    });
  }
}
  /* Fun Tambah */

  /* Fun Simpan */
function fun_simpan_document_hps() {
  $('#dg_document_hps').edatagrid('saveRow');
  setTimeout(() => {
    $('#dg_document_hps').datagrid('reload');
  }, 1000);
}
  /* Fun Simpan */

  /* Fun Hapus */
function fun_hapus_document_hps() {
  var row = $('#dg_document_hps').datagrid('getSelected');
  $.post('<?= base_url('/project/pekerjaan_usulan/deletePekerjaanDokumen') ?>', {
    pekerjaan_dokumen_id: row.pekerjaan_dokumen_id
  }, function(data, textStatus, xhr) {
    $('#dg_document_hps').datagrid('reload');
  });
}
  /* Fun Hapus */
  /* Dokumen Pekerjaan */
  /* EASYUI HPS*/

  /* EASYUI IFC*/
  /* Dokumen Pekerjaan */
  /* Fun Tambah */
function fun_tambah_document_ifc() {
  var isi_awal = $('#dg_document_ifc').data('datagrid').data.rows[0];
  if ($('#dg_document_ifc').data('datagrid').data.total > 0 && 'isNewRecord' in isi_awal) {
    $.messager.alert('Peringatan', 'Data Dokumen Masih Ada Yang Belum Tersimpan');
  } else {
    var id = '<?= $this->input->get('pekerjaan_id') ?>';
    $.getJSON('<?= base_url('project/pekerjaan_usulan/getPenomoranDokumen') ?>', {
      id_pekerjaan: id,
      single: 'y'
    }, function(json, textStatus) {
      $('#dg_document_ifc').edatagrid('addRow', {
        index: 0,
        row: {
          pekerjaan_id: id,
          is_hps: 'n',
          urutan_proyek_nama: json.urutan_proyek_nama,
          section_area_nama: json.section_area_nama,
        }
      })
    });
  }
}
  /* Fun Tambah */

  /* Fun Simpan */
function fun_simpan_document_ifc() {
  $('#dg_document_ifc').edatagrid('saveRow');
  setTimeout(() => {
    $('#dg_document_ifc').datagrid('reload');
  }, 1000);
}
  /* Fun Simpan */

  /* Fun Hapus */
function fun_hapus_document_ifc() {
  var row = $('#dg_document_ifc').datagrid('getSelected');
  console.log(row);
  $.post('<?= base_url('/project/pekerjaan_usulan/deletePekerjaanDokumen') ?>', {
    pekerjaan_dokumen_id: row.pekerjaan_dokumen_id
  }, function(data, textStatus, xhr) {
    $('#dg_document_ifc').datagrid('reload');
  });
}
  /* Fun Hapus */
  /* Dokumen Pekerjaan */

  /* Dokumen Pekerjaan */
  /* Fun Tambah */
function fun_tambah_document_ifc_hps() {
  var isi_awal = $('#dg_document_ifc_hps').data('datagrid').data.rows[0];
  if ($('#dg_document_ifc_hps').data('datagrid').data.total > 0 && 'isNewRecord' in isi_awal) {
    $.messager.alert('Peringatan', 'Data Dokumen Masih Ada Yang Belum Tersimpan');
  } else {
    var id = '<?= $this->input->get('pekerjaan_id') ?>';
    $.getJSON('<?= base_url('project/pekerjaan_usulan/getPenomoranDokumen') ?>', {
      id_pekerjaan: id,
      single: 'y'
    }, function(json, textStatus) {
      $('#dg_document_ifc_hps').edatagrid('addRow', {
        index: 0,
        row: {
          pekerjaan_id: id,
          is_hps: 'y',
          urutan_proyek_nama: json.urutan_proyek_nama,
          section_area_nama: json.section_area_nama,
        }
      });
    });
  }
}
  /* Fun Tambah */

  /* Fun Simpan */
function fun_simpan_document_ifc_hps() {
  $('#dg_document_ifc_hps').edatagrid('saveRow');
  setTimeout(() => {
    $('#dg_document_ifc_hps').datagrid('reload');
  }, 1000);
}
  /* Fun Simpan */

  /* Fun Hapus */
function fun_hapus_document_ifc_hps() {
  var row = $('#dg_document_ifc_hps').datagrid('getSelected');
  console.log(row);
  $.post('<?= base_url('/project/pekerjaan_usulan/deletePekerjaanDokumen') ?>', {
    pekerjaan_dokumen_id: row.pekerjaan_dokumen_id
  }, function(data, textStatus, xhr) {
    $('#dg_document_ifc_hps').datagrid('reload');
  });
}
  /* Fun Hapus */
  /* Dokumen Pekerjaan */
  /* EASYUI IFC*/

  /* Fun Lihat Dokumen Pekerjaan */
function fun_lihat(file, id) {
  $.getJSON('<?= base_url('project/pekerjaan_usulan/lihatDokumen') ?>', {
    id: id
  }, function(json) {
    window.open('<?= base_url('document/') ?>' + file);
  });
}

  /* Fun Lihat Dokumen Pekerjaan */

  /* Fun Loading */
function fun_loading() {
  var simplebar = new Nanobar();
  simplebar.go(100);
}
  /* Fun Loading */

  /* Fun Download */
function fun_download(isi, name) {
  window.open('<?= base_url('project/pekerjaan_usulan/downloadDokumen') ?>?pekerjaan_id=' + isi + '&pekerjaan_dokumen_file=' + name);
}

function fun_history(id_pekerjaan, pekerjaan_dokumen_nama, id_pekerjaan_template, is_hps, id_dokumen_awal, status) {
  $('#modal_history').modal('show');
  $('#table_dokumen_history').DataTable().ajax.url('<?= base_url('project/pekerjaan_usulan/getAsetDocumentHistory') ?>?id_pekerjaan=' + id_pekerjaan + '&pekerjaan_dokumen_nama=' + pekerjaan_dokumen_nama + '&id_pekerjaan_template=' + id_pekerjaan_template + '&is_hps=' + is_hps + '&id_dokumen_awal=' + id_dokumen_awal + '&pekerjaan_dokumen_status=' + status).load();
}

function fun_download_usulan(isi, name) {
  window.open('<?= base_url('project/pekerjaan_usulan/downloadDokumenUsulan') ?>?pekerjaan_id=' + isi + '&pekerjaan_dokumen_file=' + name);
}

function funcModalSendVP(id) {
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserKoorVP') ?>', {
    pekerjaan_id: id,
    status: '<?= $_GET['status'] ?>'
  }, function(json, result) {
    if (json.id_penanggung_jawab == 'n') {
      $('#id_user_send_vp').prop('disabled', true);
      $('#id_user_send_vp_hps').prop('disabled', true);
    } else if (json.id_penanggung_jawab == 'y') {
      $('#id_user_send_vp').prop('disabled', false);
      $('#id_user_send_vp_hps').prop('disabled', false);
    }
  });

  $('#id_user_send_vp').empty();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserStafVP') ?>', {
    pekerjaan_id: id,
    is_cc: 'y',
  }, function(json) {
    $.each(json, function(index, val) {
      $('#' + index).val(val);
      $('#id_user_send_vp').append('<option selected value="' + val.id_user + '">' + val.pegawai_nama + '</option>');
    });
  });

  $('#id_user_send_vp_hps').empty();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserStafVP') ?>', {
    pekerjaan_id: id,
    is_cc: 'h',
  }, function(json) {
    $.each(json, function(index, val) {
      $('#' + index).val(val);
      $('#id_user_send_vp_hps').append('<option selected value="' + val.id_user + '">' + val.pegawai_nama + '</option>');
    });
  });
  $('#modal_send_vp').modal('show');
}

function funcModalSendVPKoor(id) {
  $('#id_user_send_vp_koor').empty();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserStafVP') ?>', {
    pekerjaan_id: id,
    is_cc: 'y',
  }, function(json) {
    $.each(json, function(index, val) {
      $('#' + index).val(val);
      $('#id_user_send_vp_koor').append('<option selected value="' + val.id_user + '">' + val.pegawai_nama + '</option>');
    });
  });

  $('#id_user_send_vp_koor_hps').empty();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserStafVP') ?>', {
    pekerjaan_id: id,
    is_cc: 'h',
  }, function(json) {
    $.each(json, function(index, val) {
      $('#' + index).val(val);
      $('#id_user_send_vp_koor_hps').append('<option selected value="' + val.id_user + '">' + val.pegawai_nama + '</option>');
    });
  });
  $('#modal_send_vp_koor').modal('show');
}

function funcModalSendVPIFC(id) {
  $('#id_user_send_vp_ifc').empty();
  $('#id_user_send_vp_ifc_hps').empty();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserKoorVPIFC') ?>', {
    pekerjaan_id: id,
    status: '<?= $_GET['status'] ?>'
  }, function(json, result) {
    if (json.id_penanggung_jawab == 'n') {
      $('#id_user_send_vp_ifc').prop('disabled', true);
      $('#id_user_send_vp_ifc_hps').prop('disabled', true);
    } else if (json.id_penanggung_jawab == 'y') {
      $('#id_user_send_vp_ifc').prop('disabled', false);
      $('#id_user_send_vp_ifc_hps').prop('disabled', false);
    }
  });

  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserStafVP') ?>', {
    pekerjaan_id: id,
    is_cc: 'y',
  }, function(json) {
    $.each(json, function(index, val) {
      $('#' + index).val(val);
      $('#id_user_send_vp_ifc').append('<option selected value="' + val.id_user + '">' + val.pegawai_nama + '</option>');
    });
  });
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserStafVP') ?>', {
    pekerjaan_id: id,
    is_cc: 'h',
  }, function(json) {
    $.each(json, function(index, val) {
      $('#' + index).val(val);
      $('#id_user_send_vp_ifc_hps').append('<option selected value="' + val.id_user + '">' + val.pegawai_nama + '</option>');
    });
  });
  $('#modal_send_vp_ifc').modal('show');
}

function funcModalSendAVPIFC(id) {
  $('#id_user_send_avp_ifc').empty();
  $('#id_user_send_avp_ifc_hps').empty();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserKoorVPIFC') ?>', {
    pekerjaan_id: id,
    status: '<?= $_GET['status'] ?>'
  }, function(json, result) {
    if (json.id_penanggung_jawab == 'n') {
      $('#id_user_send_avp_ifc').prop('disabled', true);
      $('#id_user_send_avp_ifc_hps').prop('disabled', true);
    } else if (json.id_penanggung_jawab == 'y') {
      $('#id_user_send_avp_ifc').prop('disabled', false);
      $('#id_user_send_avp_ifc_hps').prop('disabled', false);
    }
  });

  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserStafVP') ?>', {
    pekerjaan_id: id,
    is_cc: 'y',
  }, function(json) {
    $.each(json, function(index, val) {
      $('#' + index).val(val);
      $('#id_user_send_avp_ifc').append('<option selected value="' + val.id_user + '">' + val.pegawai_nama + '</option>');
    });
  });
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserStafVP') ?>', {
    pekerjaan_id: id,
    is_cc: 'h',
  }, function(json) {
    $.each(json, function(index, val) {
      $('#' + index).val(val);
      $('#id_user_send_avp_ifc_hps').append('<option selected value="' + val.id_user + '">' + val.pegawai_nama + '</option>');
    });
  });
  $('#modal_send_avp_ifc').modal('show');
}

function funcModalApproveVP(id) {
  $('#id_user_approve_vp').empty();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserStafVP') ?>', {
    pekerjaan_id: id,
    is_cc: 'y',
  }, function(json) {
    $.each(json, function(index, val) {
      $('#' + index).val(val);
      $('#id_user_approve_vp').append('<option selected value="' + val.id_user + '">' + val.pegawai_nama + '</option>');
    });
  });

  $('#id_user_approve_vp_hps').empty();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserStafVP') ?>', {
    pekerjaan_id: id,
    is_cc: 'h',
  }, function(json) {
    $.each(json, function(index, val) {
      $('#' + index).val(val);
      $('#id_user_approve_vp_hps').append('<option selected value="' + val.id_user + '">' + val.pegawai_nama + '</option>');
    });
  });
  $('#modal_approve_vp').modal('show');
}
  /* Fun Download */

  /* SEND IFC dari Revisi Staf */
$('#simpan_dokumen_ifc_revisi').on('click', function() {
  var id = '<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>';
  Swal.fire({
    title: 'Apakah Anda Yakin Send ?',
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya"
  }).then(function(result) {
    if (result.value) {
        // call_ajax_page('project/pekerjaan_berjalan');
      setTimeout(() => {
        if ($('#is_rkap').val() == '1') {
          window.location.replace('<?= base_url('project/RKAP#berjalan') ?>')
        } else {
          window.location.replace('<?= base_url('project/Non_RKAP#berjalan') ?>')
        }
      }, "1000");
      $.getJSON('<?= base_url('project/pekerjaan_usulan/prosesApproveBerjalanRevisi') ?>', {
        pekerjaan_id: id,
        pekerjaan_status: $('#pekerjaan_status').val(),
        id_user_staf: $('#id_user_staf').val()
      }, function(json) {
        console.log('ok');
        $('#close_dokumen').click();
      });
    }
  });
})
  /* SEND IFC dari Revisi Staf */



  /* FUN LAIN" */
function cekStatus(data) {
  if (data == 'n') {
    $("#div_keterangan").show();
  } else {
    $('#div_keterangan').hide();
  }
}

function cekStatusIFA(data) {
  if (data == 'n') {
    $("#div_keterangan_ifa").show();
  } else {
    $('#div_keterangan_ifa').hide();
  }
}

function cekStatusIFC(data) {
  if (data == 'n') {
    $('#div_keterangan_ifc').show();
  } else {
    $('#div_keterangan_ifc').hide();
  }
}

function fun_cekRevisi(id) {
  $.getJSON('<?= base_url('project/pekerjaan_usulan/cekRevisiIFA') ?>', {
    pekerjaan_id: id
  }, function(json, result) {
      // console.log(json);
      // console.log(result);
    if (json && json.jumlah_revisi > 0) {
        // $('#btn_revisi').show();
        // $('#btn_send_avp').hide();
    } else {
        // $('#btn_send_avp').show();
        // $('#btn_revisi').hide();
    }
  })
}

function fun_cekRevisiIFA(id) {
  $.getJSON('<?= base_url('project/pekerjaan_usulan/cekRevisiIFA') ?>', {
    pekerjaan_id: id
  }, function(json, result) {
    if (json && json.jumlah_revisi == 0 && $('#pekerjaan_status').val() == '8') {
      $('#btn_approve_ifa').show();
      $('#btn_revisi_ifa').hide();
    } else if (json && json.jumlah_revisi > 0) {
      $('#btn_revisi_ifa').show();
      $('#btn_approve_ifa').hide();
    }
  })
}

function cekAVPSama(id) {
  var id = id;
}

$('#id_user_vp').on('change', function(e) {
  koordinator = $('#id_tanggung_jawab_vp').val();
  terkait = $('#id_user_vp').val();

  if (terkait.includes(koordinator)) {
    Swal.fire({
      title: "Peringatan !",
      html: "Data Tidak Boleh Sama",
      icon: "warning",
    });
    data = terkait.pop();
    $("#id_user_vp option[value='" + koordinator + "']").remove();
  }
})

$('#id_tanggung_jawab_vp').on('change', function(e) {
  koordinator = $('#id_tanggung_jawab_vp').val();
  terkait = $('#id_user_vp').val();

  if (terkait.includes(koordinator)) {
    Swal.fire({
      title: "Peringatan !",
      html: "Data Tidak Boleh Sama",
      icon: "warning",
    });
    data = terkait.pop();
    $("#id_user_vp option[value='" + koordinator + "']").remove();
  }
})

$('#id_user_vp_avp').on('change', function(e) {
  koordinator = '<?= $this->session->userdata('pegawai_nik') ?>';
  terkait = $('#id_user_vp_avp').val();

  if (terkait.includes(koordinator)) {
    Swal.fire({
      title: "Peringatan !",
      html: "Tidak Boleh Disposisi ke Diri Sendiri",
      icon: "warning",
    });
    data = terkait.pop();
    $("#id_user_vp_avp option[value='" + koordinator + "']").remove();
  }
})

function cekAVPDispoSama(id) {
  var dispo = id;
  var diri = $('#session_user').val();

  if (dispo.includes(diri)) {
    Swal.fire({
      title: "Peringatan !",
      html: "Tidak Boleh Disposisi Ke Diri Sendiri",
      icon: "warning",
    });
      // data = terkait.pop();
    $("#id_user_avp option[value='" + dispo + "']").remove();
    $("#id_user_avp_listrik option[value='" + dispo + "']").remove();
    $("#id_user_avp_instrumen option[value='" + dispo + "']").remove();
  }
}
  /* FUN LAIN" */

  // START CANCEL UPLOAD
function fun_cancel_dokumen() {
  Swal.fire({
    title: "Batal",
    html: "Batalkan Upload Dokumen (Semua Dokumen Akan Terhapus) ?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya",
    focusConfirm: false,
  }).then(function(result) {
    if (result.value) {
      var rows = $('#dg_document').edatagrid('getRows');
      for (var i = 0; i < rows.length; i++) {
        var data = rows[i];
        console.log(data);
        $.ajax({
          url: '<?= base_url('project/pekerjaan_usulan/cancelDokumen') ?>',
          data: data,
          type: 'POST',
          dataType: 'HTML',
          success: function(json) {
            $('#close_dokumen').click();
          }
        })
      }
    }
  })
}

function fun_cancel_dokumen_hps() {
  Swal.fire({
    title: "Batal",
    html: "Batalkan Upload Dokumen (Semua Dokumen Akan Terhapus) ?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya",
    focusConfirm: false,
  }).then(function(result) {
    if (result.value) {
      var rows = $('#dg_document').edatagrid('getRows');
      for (var i = 0; i < rows.length; i++) {
        var data = rows[i];
        console.log(data);
        $.ajax({
          url: '<?= base_url('project/pekerjaan_usulan/cancelDokumen') ?>',
          data: data,
          type: 'POST',
          dataType: 'HTML',
          success: function(json) {
            $('#close_dokumen_hps').click();
          }
        })
      }
    }
  })
}

function fun_cancel_dokumen_ifc() {
  Swal.fire({
    title: "Batal",
    html: "Batalkan Upload Dokumen (Semua Dokumen Akan Terhapus) ?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya",
    focusConfirm: false,
  }).then(function(result) {
    if (result.value) {
      var rows = $('#dg_document').edatagrid('getRows');
      for (var i = 0; i < rows.length; i++) {
        var data = rows[i];
        console.log(data);
        $.ajax({
          url: '<?= base_url('project/pekerjaan_usulan/cancelDokumen') ?>',
          data: data,
          type: 'POST',
          dataType: 'HTML',
          success: function(json) {
            $('#close_dokumen_ifc').click();
          }
        })
      }
    }
  })
}

function funcApproveDokumenAVP() {
  Swal.fire({
    title: "Approve",
    html: "Apakah Anda Akan Approve Dokumen Bagian Anda ?",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya",
    focusConfirm: false,
  }).then(function(result) {
    if (result.value) {
      $.get('<?= base_url('project/pekerjaan_usulan/approveDokumenAVP') ?>', {
        pekerjaan_id: '<?= $this->input->get('pekerjaan_id'); ?>'
      }, function(json, textStatus) {
        $('#table_dokumen').DataTable().ajax.reload();
        $('#table_dokumen_hps').DataTable().ajax.reload();
        $('#btn_approve_dokumen_avp').hide();
      });
    }
  })
}

function fun_draft_dokumen(id) {
  var isi_awal = $('#dg_document').data('datagrid').data.rows[0];
  if ($('#dg_document').data('datagrid').data.total > 0 && 'isNewRecord' in isi_awal) {
    $.messager.alert('Peringatan', 'Data Dokumen Masih Ada Yang Belum Tersimpan');
  } else {
      // insert CC status non aktif
    $.ajax({
      url: '<?= base_url('project/pekerjaan_usulan/insertCCDraft') ?>',
      data: $('#form_upload_dokumen').serialize(),
      dataType: 'HTML',
      type: 'POST',
      success: function(result) {
        $('#close_dokumen').click();
      }
    })
  }
}

function fun_draft_dokumen_hps(id) {
  var isi_awal = $('#dg_document_hps').data('datagrid').data.rows[0];
  if ($('#dg_document_hps').data('datagrid').data.total > 0 && 'isNewRecord' in isi_awal) {
    $.messager.alert('Peringatan', 'Data Dokumen Masih Ada Yang Belum Tersimpan');
  } else {
    $.ajax({
      url: '<?= base_url('project/pekerjaan_usulan/insertCCDraftHPS') ?>',
      data: $('#form_upload_dokumen_hps').serialize(),
      dataType: 'HTML',
      type: 'POST',
      success: function(result) {

        $('#close_dokumen_hps').click();
      }
    })
  }
}

function fun_simpan_dokumen_hps() {
  var isi_awal = $('#dg_document_hps').data('datagrid').data.rows[0];
  if ($('#dg_document_hps').data('datagrid').data.total > 0 && 'isNewRecord' in isi_awal) {
    $.messager.alert('Peringatan', 'Data Dokumen Masih Ada Yang Belum Tersimpan');
  } else {
    fun_approve_berjalan_ifc('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>', 'Apakah Anda Yakin Send?');
    $('#close_dokumen_hps').click();
  }
}

function fun_draft_dokumen_ifc(id) {
  var isi_awal = $('#dg_document_ifc').data('datagrid').data.rows[0];
  if ($('#dg_document_ifc').data('datagrid').data.total > 0 && 'isNewRecord' in isi_awal) {
    $.messager.alert('Peringatan', 'Data Dokumen Masih Ada Yang Belum Tersimpan');
  } else {
    $.ajax({
      url: '<?= base_url('project/pekerjaan_usulan/insertCCDraftIFC') ?>',
      data: $('#form_upload_dokumen_ifc').serialize(),
      dataType: 'HTML',
      type: 'POST',
      success: function(result) {
        $('#close_dokumen_ifc').click();
      }
    })
  }
}

function fun_draft_dokumen_ifc_hps(id) {
  var isi_awal = $('#dg_document_ifc_hps').data('datagrid').data.rows[0];
  if ($('#dg_document_ifc_hps').data('datagrid').data.total > 0 && 'isNewRecord' in isi_awal) {
    $.messager.alert('Peringatan', 'Data Dokumen Masih Ada Yang Belum Tersimpan');
  } else {
    $.ajax({
      url: '<?= base_url('project/pekerjaan_usulan/insertCCDraftIFCHPS') ?>',
      data: {
        id_pekerjaan_ifc_hps: $('#id_pekerjaan_ifc_hps').val(),
          // pekerjaan_status: $('#pekerjaan_status').val(),
        id_user_staf_ifc_hps: $('#id_user_staf_ifc_hps').val(),
          // id_user_send_vp_hps: $('#id_user_send_vp_hps').val(),
      },
      dataType: 'HTML',
      type: 'POST',
      success: function(result) {
        $('#close_dokumen_ifc_hps').click();
      }
    })
  }
}
  // FINISH CANCEL UPLOAD

  /*sum hps*/
function funSumHPS(id) {
  var hasil = 0;
  $(".pekerjaan_nilai_hps").each(function() {
    hasil += parseFloat($(this).val());
    $("#pekerjaan_nilai_hps_total").val(hasil);
  });
}
  /*sum hps*/

function fun_cek_penomoran(id) {
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getPenomoranDokumen') ?>', {
    id_pekerjaan: id
  }, function(json, textStatus) {
    if (json != '') {
      $.each(json, function(index, val) {
        if (json.perencana == 'y') {
          $.each(json, function(index, val) {
            $('#' + index).val(val);
            $('#btn_upload_hps, #btn_upload, #btn_progress, #btn_send_ifa').show();
            $('#div_penomoran_dokumen').hide();
          });
        } else {
            // $('#' + index).val(val);
            // $('#btn_upload_hps').show();
            // $('#btn_upload').show();
            // $('#btn_progress').show();
            // $('#btn_send_ifa').show();
        }
      });
    } else {
      $('#div_penomoran_dokumen').show();
      $('#btn_upload_hps, #btn_upload, #btn_progress, #btn_send_ifa').hide();
    }
  });
}

  /*cek dokumen ifa*/
function fun_cek_dokumen_ifa(id, status) {
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getStatusDokumenIFA') ?>', {
    pekerjaan_id: id,
    pekerjaan_status: status
  },
  function(json, textStatus) {
    if (json.dokumen_revisi > 0) {
      $('#btn_approve_ifa').hide();
      $('#btn_approve_ifa_avp').hide();
      $('#btn_approve_ifa_vp').hide();
      $('#btn_revisi_ifa').show();
    } else {
      if (json.dokumen_ifa == '0') {
        $('#btn_approve_ifa').show();
      } else if (json.dokumen_ifa_avp == '0') {
        $('#btn_approve_ifa_avp').show();
      } else if (json.dokumen_ifa_vp == '0') {
        $('#btn_approve_ifa_vp').show();
      }
    }

  });
}
  /*cek dokumen ifa*/


  // $(function() {
  //  $('#mySelect2').select2({
  //    tags: true,
  //    placeholder: 'Select an option',
  //    templateSelection : function (tag, container){
  //     var $option = $('#mySelect2 option[value="'+tag.id+'"]');
  //     if ($option.attr('locked')){
  //      $(container).addClass('locked-tag');
  //      tag.locked = true;
  //    }
  //    return tag.text;
  //  },
  // })
  //  .on('select2:unselecting', function(e){
  //    if ($(e.params.args.data.element).attr('locked')) {
  //      e.select2.pleaseStop();
  //    }
  //  });
  // });

function fun_cc_ifa(id) {
  $('.select2-selection__choice .select2-selection__choice__remove').hide();
  $('#cc_ifa').empty();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserStafVP') ?>', {
    pekerjaan_id: id,
    is_cc: 'y',
  }, function(data, textStatus, xhr) {
    $.each(data, function(index, val) {
      $('#' + index).val();
      if ('<?= $this->session->userdata('pegawai_unit_id'); ?>' == 'E53000' || (val.pegawai_unit_id == '<?= $this->session->userdata('pegawai_unit_id'); ?>' && val.id_user == '<?= $this->session->userdata('pegawai_nik'); ?>')) {
        $('#cc_ifa').append('<option selected title="n" value="' + val.id_user + '">' + val.pegawai_nama + ' - ' + val.pegawai_postitle + '</option>');
      } else {
        $('#cc_ifa').append('<option selected title="y" value="' + val.id_user + '">' + val.pegawai_nama + ' - ' + val.pegawai_postitle + '</option>');
      }

    });
  });
  $('#modal_cc_ifa').modal('show');
}

function fun_cc_hps_ifa(id) {
  $('#cc_hps_ifa').empty();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserStafVP') ?>', {
    pekerjaan_id: id,
    is_cc: 'h',
  }, function(data, textStatus, xhr) {
    $.each(data, function(index, val) {
      $('#' + index).val();
      if ('<?= $this->session->userdata('pegawai_unit_id'); ?>' == 'E53000' || (val.pegawai_unit_id == '<?= $this->session->userdata('pegawai_unit_id'); ?>' && val.id_user == '<?= $this->session->userdata('pegawai_nik'); ?>')) {
        $('#cc_ifa').append('<option selected title="n" value="' + val.id_user + '">' + val.pegawai_nama + ' - ' + val.pegawai_postitle + '</option>');
      } else {
        $('#cc_ifa').append('<option selected title="y" value="' + val.id_user + '">' + val.pegawai_nama + ' - ' + val.pegawai_postitle + '</option>');
      }
    });
  });
  $('#modal_cc_hps_ifa').modal('show');
}

$('#simpan_cc_ifa').on('click', function() {
  var url = '';
  var data = '';
  $.ajax({
    url: '<?= base_url('project/pekerjaan_usulan/insertCC') ?>',
    type: 'POST',
    dataType: 'HTML',
    data: {
      pekerjaan_id: $('#id_pekerjaan_cc_ifa').val(),
      cc_id: $('#cc_ifa').val(),
      cc_tipe: 'y',
    },
    success: function(result) {
      $('#modal_cc_ifa').modal('hide');
      $('#close_cc_ifa').click();
    }
  })
})

$('#simpan_cc_hps_ifa').on('click', function() {
  var url = '';
  var data = '';
  $.ajax({
    url: '<?= base_url('project/pekerjaan_usulan/insertCCHPS') ?>',
    type: 'POST',
    dataType: 'HTML',
    data: {
      pekerjaan_id: $('#id_pekerjaan_cc_hps_ifa').val(),
      cc_id: $('#cc_hps_ifa').val(),
      cc_tipe: 'h',
    },
    success: function(result) {
      $('#modal_cc_hps_ifa').modal('hide');
      $('#close_cc_hps_ifa').click();
    }
  })
})

$('#simpan_nilai_hps').on('click', function() {
  var url = '<?= base_url('project/pekerjaan_usulan/insertNilaiHPS') ?>';
  var data = new FormData($('#form_modal_nilai_hps')[0]);
  data.append('pekerjaan_id', $('#id_pekerjaan_nilai_hps').val());
  $.ajax({
    url: url,
    type: 'POST',
    dataType: 'HTML',
    data: data,
    processData: false,
    contentType: false,
    cache: false,
    success: function(result) {
      $('#close_nilai_hps').click();
    }
  })
})

function viewFile(fileName) {
  window.open('<?= base_url('document') ?>/' + fileName, '_blank');
}

function padTo2Digits(num) {
  return num.toString().padStart(2, '0');
}

function fun_ganti_kategori(hari) {
    var date = new Date(); // Now
    date.setDate(date.getDate() + parseInt(hari)); // Set now + 30 days as the new date
    var akhir = date.toLocaleString('en-GB')
    $('#pekerjaan_waktu_akhir_avp').val(akhir);
  }

  function pdf_dokumen_usulan() {
    var cb = document.getElementsByName('cb_dokumen_usulan[]');
    var cek = [];
    for (var i = 0; i < cb.length; i++) {
      if (cb[i].checked) {
        cek.push(cb[i].value);
      }
    }
    window.open('<?= base_url() ?>project/pekerjaan_usulan/downloadDokumenList?idp=<?= $this->input->get_post('pekerjaan_id'); ?>&idd=' + cek + '&status=usulan');
  }

  function pdf_dokumen() {
    var checkboxes = document.getElementsByName('cb_dokumen[]');
    var selected = [];
    for (var i = 0; i < checkboxes.length; i++) {
      if (checkboxes[i].checked) {
        selected.push(checkboxes[i].value);
      }
    }
    window.open('<?= base_url() ?>project/pekerjaan_usulan/downloadDokumenList?idp=<?= $this->input->get_post('pekerjaan_id'); ?>&idd=' + selected);
  }
</script>
