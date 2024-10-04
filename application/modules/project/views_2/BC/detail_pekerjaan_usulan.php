<link rel="stylesheet" type="text/css" href="<?= base_url('assets_tambahan/') ?>easyui/themes/default/easyui.css">
<link rel="stylesheet" type="text/css" href="<?= base_url('assets_tambahan/') ?>easyui/themes/icon.css">
<?php
$jml = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $_GET['pekerjaan_id'] . "' AND (pekerjaan_disposisi_status = '8' OR pekerjaan_disposisi_status = '5')");
$isi_jml = $jml->row_array();
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


$sql_disposisi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi WHERE id_user = '" . $data_session['pegawai_nik'] . "' AND id_pekerjaan = '" . $_GET['pekerjaan_id'] . "' AND pekerjaan_disposisi_status = '" . $_GET['status'] . "'");
$data_disposisi = $sql_disposisi->row_array();
?>

<div class="page-content">
  <div class="container-fluid">
    <!-- start page title -->
    <div class="row">
      <div class="col-12">
        <?php if ($_GET['rkap'] == 0) : ?>
          <a href="<?= base_url('project/Non_RKAP') ?>" class="btn btn-success"><u><i class="fa fa-arrow-left"></i> Kembali</u></a>
        <?php else : ?>
          <a href="<?= base_url('project/RKAP') ?>" class="btn btn-success"><u><i class="fa fa-arrow-left"></i> Kembali</u></a>
        <?php endif ?>
        <center>
          <h4 class="card-title mb-4">Pekerjaan</h4>
        </center>
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
                <a class="nav-link active" href="javascript:;" onclick="fun_div_home()" id="link_div_home">Home</a>
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
                <h5 class="text-truncate font-size-15">Pekerjaan</h5>
                <input hidden type="text" name="session_poscode" id="session_poscode" value="<?= $data_session['pegawai_poscode'] ?>">
                <input hidden type="text" name="session_direct_superior" id="session_direct_superior" value="<?= $data_session['pegawai_direct_superior'] ?>">
                <input hidden type="text" name="session_user" id="session_user" value="<?= $data_session['pegawai_nik'] ?>">
                <input hidden type="text" name="session_bagian" id="session_bagian" value="<?php echo (!empty($data_bagian['bagian_id'])) ? $data_bagian['bagian_id'] : '' ?>">
                <input hidden type="text" name="pekerjaan_status" id="pekerjaan_status" style="display:none">
                <input type="text" name="is_rkap" id="is_rkap" value="<?= $_GET['rkap']; ?>" hidden>
                <p class="text-muted"><?= (!empty($pekerjaan)) ? $pekerjaan['pekerjaan_judul'] : '-'  ?></p>
              </div>
            </div>
            <div class="d-flex">
              <div class="flex-grow-1 overflow-hidden">
                <h5 class="text-truncate font-size-15">No Pekerjaan</h5>
                <p class="text-muted"><?= (!empty($pekerjaan)) ? $pekerjaan['pekerjaan_nomor'] : '-'; ?></p>
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
                <p class="text-muted"><?= (!empty($pekerjaan)) ? $pekerjaan['pegawai_nama'] : '-' ?></p>
                <p class="text-muted"><u><?= (!empty($pekerjaan)) ? $pekerjaan['pegawai_nama_dep'] : '-' ?></u></p>
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
                <p class="text-muted"><?= (!empty($pekerjaan)) ? ($pekerjaan['pekerjaan_deskripsi']) : '-' ?></p>
              </div>
            </div>
            <div class="d-flex">
              <div class="flex-grow-1 overflow-hidden">
                <h5 class="text-truncate font-size-15">Catatan Disposisi</h5>
                <p class="text-muted"><?= (!empty($data_disposisi['pekerjaan_disposisi_catatan'])) ? ($data_disposisi['pekerjaan_disposisi_catatan']) : '-' ?></p>
              </div>
            </div>
            <div class="row task-dates">
              <div class="col col-sm-4 col-4">
                <div class="mt-4">
                  <h5 class="font-size-14"><i class="bx bx-calendar me-1 text-primary"></i> Tanggal Pengajuan
                  </h5>
                  <p class="text-muted mb-0"><?= (!empty($pekerjaan)) ? date("d-m-Y", strtotime($pekerjaan['pekerjaan_waktu'])) : '-' ?></p>
                </div>
              </div>
              <?php if ($_GET['aksi'] != 'usulan') : ?>
                <div class="col col-sm-4 col-4">
                  <div class="mt-4">
                    <h5 class="font-size-14"><i class="bx bx-calendar-check me-1 text-primary"></i> Tanggal Akhir Pekerjaan</h5>
                    <p class="text-muted mb-0" id="tanggal_akhir"><?= (!empty($pekerjaan)) ? date("d-m-Y", strtotime($pekerjaan['pekerjaan_waktu_akhir'])) : '-' ?></p>
                  </div>
                </div>
              <?php endif ?>
              <!-- Tombol -->
              <div class="col-12"></div>
              <?php if ($_GET['aksi'] == 'usulan') : ?>
                <!-- Usulan -->
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
                <!-- Usulan -->
              <?php elseif ($_GET['aksi'] == 'berjalan') : ?>
                <!-- Berjalan -->
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
                    <button type="button" class="btn btn-success col-10" onclick="fun_progress('<?= $this->input->get('pekerjaan_id') ?>')">Progress</button>
                  </div>
                </div>
                <div class="col-sm-4 col-md-3" id="btn_send_ifa" style="display: none;margin-right:-50px">
                  <div class="mt-4">
                    <button type="button" class="btn btn-primary col-10" onclick="fun_approve_berjalan('<?= $this->input->get('pekerjaan_id') ?>', 'Apakah Anda Yakin Send?')">Send IFA</button>
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
                <!-- Berjalan -->
              <?php elseif ($_GET['aksi'] == 'ifa') : ?>
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
              <?php elseif ($_GET['aksi'] == 'ifc') : ?>
                <!-- IFC -->
                <div class="col-sm-4 col-md-3" id="btn_upload_ifc_hps" style="display: none;">
                  <div class="mt-4">
                    <button type="button" class="btn btn-success col-10" onclick="fun_upload_ifc_hps('<?= $this->input->get('pekerjaan_id') ?>','<?= $_GET['status'] ?>')">Upload Dokumen HPS</button>
                  </div>
                </div>
                <div class="col-sm-4 col-md-3" id="btn_upload_ifc" style="display: none;">
                  <div class="mt-4">
                    <button type="button" class="btn btn-success col-10" onclick="fun_upload_ifc('<?= $this->input->get('pekerjaan_id') ?>','<?= $_GET['status'] ?>')">Upload Dokumen Non HPS</button>
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
              <table class="table table-bordered table-striped" id="table_dokumen_usulan">
                <thead>
                  <tr>
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
              <!-- Tombol -->
              <button type="button" class="btn btn-success col-2 float-end" onclick="funcModalSendVPKoor(<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>)" id="btn_send_avp" style="display:none">
                Send VP
              </button>
              <button type="button" class="btn btn-info col-2 float-end" onclick="funcModalSendVP(<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>)" id="btn_send_avp_koor" style="display:none">
                Send AVP Koor
              </button>
              <button type="button" class="btn btn-success col-2 float-end" onclick="funcModalApproveVP(<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>)" id="btn_approve_vp" style="display:none">
                Send User
              </button>
              <button type="button" class="btn btn-danger col-1 float-end" id="btn_revisi" onclick="fun_reject_berjalan_ifa('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>')" style="display: none;">Revisi</button>
              <!-- Tombol -->
            </div>
          </div>
        </div>

        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">


              <ul class="nav nav-tabs">
                <li class="nav-item">
                  <a class="nav-link active" href="javascript:;" onclick="div_doc_usulan()" id="link_div_doc_usulan">Usulan</a>
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
                <table class="table table-bordered table-striped" id="table_dokumen_usulan" width="100%">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Nama File</th>
                      <th style="text-align: center;">Lihat</th>
                      <th style="text-align: center;">Download</th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>


            <!-- </div>
        </div>

        <div class="col-lg-12">
          <div class="card"> -->
            <div id="div_doc_ifa" style="display:none">
              <div class="card-body">

                <h4 class="card-title mb-4">Dokumen</h4>
                <!-- <div id="div_dokumen_send_vp" style="display:none"> -->
                  <table class="table table-striped align-middle mb-0" id="table_dokumen" width="100%">
                    <thead>
                      <tr>
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
                  <!-- </div> -->
                </div>
              </div>

              <!-- </div> -->
              <!-- </div> -->

            <!-- <div class="col-lg-12">
              <div class="card"> -->
                <div id="div_doc_ifa_hps" style="display:none">
                  <div class="card-body">
                    <h4 class="card-title mb-4">Dokumen Internal</h4>
                    <table class="table table-bordered table-striped" id="table_dokumen_hps" width="100%">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Nama File</th>
                          <th>Bagian</th>
                          <th>Status</th>
                          <th>Diupload Oleh</th>
                          <!-- <th>CC</th> -->
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

              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <!-- Tombol -->
                <!-- <button type="button" class="btn btn-success col-1 float-end" onclick="funcModalSendVPIFC(<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>)" id="btn_send_avp_ifc" style="display:none">
                  Send VP
                </button>
                <button type="button" class="btn btn-danger col-1 float-end" id="btn_revisi" onclick="fun_reject_berjalan('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>')" style="display: none;">
                  Revisi
                </button> -->
                <button type="button" class="btn btn-success col-2 float-end" id="btn_approve_ifa" onclick="fun_approve_ifa('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>', 'Apakah Anda Yakin Approved?')" style="display: none;">
                  Approve IFA
                </button>

                <button type="button" class="btn btn-success col-2 float-end" id="btn_approve_ifa_avp" onclick="fun_approve_ifa_avp('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>', 'Apakah Anda Yakin Approved?')" style="display: none;">
                  Approve IFA AVP
                </button>

                <button type="button" class="btn btn-success col-2 float-end" id="btn_approve_ifa_vp" onclick="fun_approve_ifa_vp('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>', 'Apakah Anda Yakin Approved?')" style="display: none;">
                  Approve IFA VP
                </button>

                <button type="button" class="btn btn-danger col-2 float-end" id="btn_revisi_ifa" onclick="fun_reject_ifa('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>')" style="display: none;">Revisi IFA</button>

                <!-- <button type="button" class="btn btn-success col-2 float-end" onclick="funcModalSendVP(<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>)" id="btn_send_avp" style="display:none">
                  Send VP
                </button>
                <button type="button" class="btn btn-info col-2 float-end" onclick="funcModalSendVP(<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>)" id="btn_send_avp_koor" style="display:none">
                  Send AVP Koor
                </button>
                <button type="button" class="btn btn-success col-2 float-end" onclick="funcModalApproveVP(<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>)" id="btn_approve_vp" style="display:none">
                  Approve VP
                </button>
                <button type="button" class="btn btn-danger col-1 float-end" id="btn_revisi" onclick="fun_reject_berjalan_ifa('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>')" style="display: none;">Revisi</button> -->
                <!-- Tombol -->
              </div>
            </div>
          </div>

          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">

                <ul class="nav nav-tabs">
                  <li class="nav-item">
                    <a class="nav-link active" href="javascript:;" onclick="div_doc_usulan()" id="link_div_doc_usulan">Usulan</a>
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
                  <table class="table table-bordered table-striped" id="table_dokumen_usulan" width="100%">
                    <thead>
                      <tr>
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
                  <table class="table table-striped align-middle mb-0" id="table_dokumen_ifa" width="100%">
                    <thead>
                      <tr>
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

              <!-- </div>
          </div>

          <div class="col-lg-12">
            <div class="card"> -->
              <div id="div_doc_ifa_hps" style="display:none">
                <div class="card-body">
                  <h4 class="card-title mb-4">Dokumen IFA Internal</h4>
                  <table class="table table-bordered table-striped" id="table_dokumen_ifa_hps" width="100%">
                    <thead>
                      <tr>
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
              <div class="card-body">
                <!-- Tombol -->
                <button type="button" class="btn btn-success col-2 float-end" onclick="funcModalSendVPIFC(<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>)" id="btn_send_avp_ifc" style="display:none">
                  Send VP
                </button>
                <button type="button" class="btn btn-info col-2 float-end" onclick="funcModalSendAVPIFC(<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>)" id="btn_send_avp_ifc_koor" style="display:none">
                  Send AVP Koor
                </button>
                <button type="button" class="btn btn-success col-2 float-end" onclick="funcModalApproveVP(<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>)" id="btn_approve_vp" style="display:none">
                  Approve VP
                </button>
                <button type="button" class="btn btn-danger col-1 float-end" id="btn_revisi" onclick="fun_reject_berjalan_ifc('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>')" style="display: none;">Revisi</button>
                <!-- Tombol -->
              </div>
            </div>
          </div>

          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                <ul class="nav nav-tabs">
                  <li class="nav-item">
                    <a class="nav-link active" href="javascript:;" onclick="div_doc_usulan()" id="link_div_doc_usulan">Usulan</a>
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
                  <table class="table table-bordered table-striped" id="table_dokumen_usulan" width="100%">
                    <thead>
                      <tr>
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
                  <table class="table table-striped align-middle mb-0" id="table_dokumen_ifa" width="100%">
                    <thead>
                      <tr>
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

              <!-- </div>
          </div>

          <div class="col-lg-12">
            <div class="card"> -->
              <div id="div_doc_ifa_hps" style="display:none">
                <div class="card-body">
                  <h4 class="card-title mb-4">Dokumen IFA Internal</h4>
                  <table class="table table-bordered table-striped" id="table_dokumen_ifa_hps" width="100%">
                    <thead>
                      <tr>
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
                  <table class="table table-striped align-middle mb-0" id="table_dokumen_ifc" width="100%">
                    <thead>
                      <tr>
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
                  <table class="table table-striped align-middle mb-0" id="table_dokumen_ifc_hps" width="100%">
                    <thead>
                      <tr>
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
                <!-- <button type="button" class="btn btn-primary col-1 float-end" id="btn_send_avp" onclick="fun_approve_berjalan('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>', 'Apakah Anda Yakin Reviewed?')" style="display: none;">Send VP</button> -->
                <!-- <button type="button" class="btn btn-success col-1 float-end" data-bs-toggle="modal" data-target="#modal_send_vp" style="display:none" id="btn_send_avp">Send VP</button> -->
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
                <!-- <div id="div_dokumen_send_vp" style="display:none"> -->
                <!-- <table class="table table-striped align-middle mb-0" id="table_dokumen_selesai" width="100%">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Nama File</th>
                        <th>Bagian</th>
                        <th>Status</th>
                        <th>Diupload Oleh</th>
                        <th>Keterangan</th>
                        <th style="text-align: center;">Lihat</th>
                        <th style="text-align: center;">Download</th>
                      </tr>
                    </thead>
                  </table> -->
                  <!-- </div> -->
                </div>
              </div>
            </div>

          <!-- <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title mb-4">Dokumen Internal</h4>
                  <table class="table table-bordered table-striped" id="table_dokumen_selesai_hps" width="100%">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Nama File</th>
                        <th>Bagian</th>
                        <th>Status</th>
                        <th>Diupload Oleh</th>
                        <th>Keterangan</th>
                        <th style="text-align: center;">Lihat</th>
                        <th style="text-align: center;">Download</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>
            </div> -->
            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <ul class="nav nav-tabs">
                    <li class="nav-item">
                      <li class="nav-item">
                        <a class="nav-link active" href="javascript:;" onclick="div_doc_ifa_selesai()" id="link_div_doc_ifa_selesai">IFA</a>
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
                      <table class="table table-striped align-middle mb-0" id="table_dokumen_selesai_ifa" width="100%">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Nama File</th>
                            <th>Bagian</th>
                            <th>Status</th>
                            <th>Diupload Oleh</th>
                            <th>Keterangan</th>
                            <th style="text-align: center;">Lihat</th>
                            <th style="text-align: center;">Download</th>
                            <th style="text-align: center;">History</th>
                            <!-- <th style="text-align: center;">Aksi</th> -->
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>
                  <div id="div_doc_ifc_selesai" style="display:none">
                    <div class="card-body">
                      <h4 class="card-title mb-4">Dokumen IFC</h4>
                      <table class="table table-striped align-middle mb-0" id="table_dokumen_selesai" width="100%">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Nama File</th>
                            <th>Bagian</th>
                            <th>Status</th>
                            <th>Diupload Oleh</th>
                            <th>Keterangan</th>
                            <th style="text-align: center;">Lihat</th>
                            <th style="text-align: center;">Download</th>
                            <th style="text-align: center;">History</th>
                            <!-- <th style="text-align: center;">Aksi</th> -->
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>

                  <div id="div_doc_hps_selesai" style="display:none ;">
                    <div class="card-body">
                      <h4 class="card-title mb-4">Dokumen HPS</h4>
                      <table class="table table-striped align-middle mb-0" id="table_dokumen_selesai_hps" width="100%">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Nama File</th>
                            <th>Bagian</th>
                            <th>Status</th>
                            <th>Diupload Oleh</th>
                            <th>Keterangan</th>
                            <th style="text-align: center;">Lihat</th>
                            <th style="text-align: center;">Download</th>
                            <th style="text-align: center;">History</th>
                            <!-- <th style="text-align: center;">Aksi</th> -->
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>

                </div>
              </div>

          <!-- <div class="col-lg-12">
            <div class="card">
              <div id="div_doc_ifa_hps" style="display:none">
                <div class="card-body">
                  <h4 class="card-title mb-4">Dokumen IFA Internal</h4>
                  <table class="table table-bordered table-striped" id="table_dokumen_ifa_hps" width="100%">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Nama File</th>
                        <th>Bagian</th>
                        <th>Status</th>
                        <th>Diupload Oleh</th>
                        <th>Keterangan</th>
                        <th style="text-align: center;">Lihat</th>
                        <th style="text-align: center;">Download</th>
                        <th style="text-align: center;" id="aksi_upload">Aksi</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>


              <div id="div_doc_ifc" style="display:none">
                <div class="card-body">
                  <h4 class="card-title mb-4">Dokumen IFC</h4>
                  <table class="table table-striped align-middle mb-0" id="table_dokumen_ifc" width="100%">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Nama File</th>
                        <th>Bagian</th>
                        <th>Status</th>
                        <th>Diupload Oleh</th>
                        <th>Keterangan</th>
                        <th style="text-align: center;">Lihat</th>
                        <th style="text-align: center;">Download</th>
                        <th style="text-align: center;">Aksi</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>

              <div id="div_doc_ifc_hps" style="display:none">
                <div class="card-body">
                  <h4 class="card-title mb-4">Dokumen IFC Internal</h4>
                  <table class="table table-striped align-middle mb-0" id="table_dokumen_ifc_hps" width="100%">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Nama File</th>
                        <th>Bagian</th>
                        <th>Status</th>
                        <th>Diupload Oleh</th>
                        <th>Keterangan</th>
                        <th style="text-align: center;">Lihat</th>
                        <th style="text-align: center;">Download</th>
                        <th style="text-align: center;">Aksi</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div> -->

          <!-- </div>
          </div> -->

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
  <div class="modal fade" id="modal_vp" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Disposisi ke AVP</h4>
        </div>
        <div class="modal-body">
          <form id="form_modal_vp">
            <input type="text" name="id_pekerjaan_vp" id="id_pekerjaan_vp" value="<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>" style="display: none">
            <div class="card-body row">
              <div class="form-group row col-md-12">
                <label class="col-md-4">Koordinator Pekerjaan</label>
                <select name="id_tanggung_jawab_vp" id="id_tanggung_jawab_vp" class="form-control col-md-8 select2" style="width: 100%"></select>
                <label style="color:red;display:none" id="id_tanggung_jawab_vp_alert">Koordinator Tidak Boleh Kosong</label>
              </div>
              <div class="form-group row col-md-12" id="div_pekerjaan_disposisi_catatan">
                <label class="col-md-4">Catatan Disposisi Koordinator</label>
                <textarea name="pekerjaan_disposisi_catatan_koordinator" id="pekerjaan_disposisi_catatan_koordinator" class="form-control col-md-8" placeholder="Catatan Disposisi"></textarea>
              </div>
              <div class="form-group row col-md-12">
                <label class="col-md-4">AVP Terkait</label>
                <select name="id_user_vp[]" id="id_user_vp" class="form-control select2" style="width: 100%" multiple></select>
                <label style="color:red;display:none" id="id_user_vp_alert">AVP Terkait Tidak Boleh Kosong</label>
              </div>
              <div class="form-group row col-md-12" id="div_pekerjaan_disposisi_catatan">
                <label class="col-md-4">Catatan Disposisi Terkait</label>
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
  <div class="modal fade" id="modal_avp" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Reviewed AVP</h4>
        </div>
        <div class="modal-body">
          <form id="form_modal_avp">
            <input type="text" name="id_pekerjaan_avp" id="id_pekerjaan_avp" value="<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>" style="display: none;">
            <input type="text" name="id_klasifikasi_pekerjaan_avp_rkap" id="id_klasifikasi_pekerjaan_avp_rkap" value="<?php echo (!empty($isi_pekerjaan)) ? $isi_pekerjaan['id_klasifikasi_pekerjaan'] : '-' ?>" hidden>
            <div class="card-body row">
              <div class="form-group row col-md-12" id="div_id_user_vp_avp" style="display:none;">
                <label class="col-md-4">Reviewed AVP</label>
                <select name="id_user_vp_avp[]" id="id_user_vp_avp" class="form-control col-md-8 select2" style="width: 100%" multiple></select>
              </div>
              <div class="form-group row col-md-12" id="div_pekerjaan_judul_avp" style="display:none">
                <label class="col-md-4">Nama Pekerjaan</label>
                <input type="text" name="pekerjaan_judul" id="pekerjaan_judul" value="<?= (!empty($isi_pekerjaan)) ? $isi_pekerjaan['pekerjaan_judul'] : '-' ?>" class="form-control col-md-8">
              </div>
              <div class="form-group row col-md-12" id="div_pekerjaan_waktu_akhir_avp" style="display:none">
                <label class="col-md-4">Estimasi Target Pekerjaan Selesai</label>
                <input type="date" name="pekerjaan_waktu_akhir_avp" id="pekerjaan_waktu_akhir_avp" style="background-color: pink;" class="form-control col-md-8" value="<?= (!empty($isi_pekerjaan['pekerjaan_waktu_akhir'])) ? date("Y-m-d", strtotime($isi_pekerjaan['pekerjaan_waktu_akhir'])) : date('Y-m-d') ?>">
              </div>
              <div class="form-group row col-md-12" id="div_id_klasifikasi_pekerjaan_avp" style="display:none">
                <label class="col-md-4">Klasifikasi Pekerjaan</label>
                <select name="id_klasifikasi_pekerjaan_avp" id="id_klasifikasi_pekerjaan_avp" class="form-control col-md-8 select2" style="width: 100%"></select>
              </div>
              <div class="form-group row col-md-12" id="div_id_user_avp" style="display:none">
                <label class="col-md-4">Disposisi</label>
                <select name="id_user_avp" id="id_user_avp" class="form-control col-md-8 select2" style="width: 100%"></select>
              </div>
              <div class="form-group row col-md-12" id="div_id_user_avp_listrik" style="display:none">
                <label class="col-md-4">Disposisi Listrik</label>
                <select name="id_user_avp_listrik" id="id_user_avp_listrik" class="form-control col-md-8 select2" style="width: 100%"></select>
              </div>
              <div class="form-group row col-md-12" id="div_id_user_avp_instrumen" style="display:none">
                <label class="col-md-4">Disposisi Instrumen</label>
                <select name="id_user_avp_instrumen" id="id_user_avp_instrumen" class="form-control col-md-8 select2" style="width: 100%"></select>
              </div>
              <div class="form-group row col-md-12" id="div_pekerjaan_disposisi_catatan">
                <label class="col-md-4">Catatan Disposisi</label>
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
  <div class="modal fade" id="modal_progress" data-bs-backdrop="static">
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

  <!-- MODAL GANTI BAGIAN -->
  <div class="modal fade" id="modal_ganti_perencana" data-bs-backdrop="static">
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

  <!-- MODAL UPLOAD DOKUMEN -->
  <div class="modal fade" id="modal_upload" data-bs-backdrop="static">
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
  <div class="modal fade" id="modal_upload_hps" data-bs-backdrop="static">
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
  <div class="modal fade" id="modal_upload_ifc" data-bs-backdrop="static">
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
  <div class="modal fade" id="modal_upload_ifc_hps" data-bs-backdrop="static">
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
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-undo" plain="true" onclick="javascript:$('#dg_document_ifc').edatagrid('cancelRow')">Cancel</a>
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
  <div class="modal fade" id="modal_aksi" data-bs-backdrop="static">
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
              </di>
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
</div>
<!-- MODAL AKSI DOKUMEN -->

<!-- MODAL AKSI DOKUMEN -->
<div class="modal fade" id="modal_aksi_staf" data-bs-backdrop="static">
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
    <div class="modal fade" id="modal_aksi_cc" data-bs-backdrop="static">
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
  <div class="modal fade" id="modal_aksi_ifa" data-bs-backdrop="static">
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
<div class="modal fade" id="modal_aksi_ifa_cc" data-bs-backdrop="static">
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
<div class="modal fade" id="modal_aksi_ifc" data-bs-backdrop="static">
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
<div class="modal fade" id="modal_lihat" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">View</h4>
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"> -->
          <!-- <span aria-hidden="true">&times;</span> -->
          <!-- </button> -->
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

  <!-- MODAL HISTORY DOKUMEN -->
  <div class="modal fade" id="modal_history" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">History Dokumen</h4>
          <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"> -->
            <!-- <span aria-hidden="true">&times;</span> -->
            <!-- </button> -->
          </div>
          <input type="hidden" id="jadwal_id" name="jadwal_id" value="">
          <div class="modal-body">
            <table class="table table-bordered table-striped nowrap" id="table_dokumen_history" width="100%">
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
    <div class="modal fade" id="modal_send_vp" data-bs-backdrop="static">
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
    <div class="modal fade" id="modal_send_vp_koor" data-bs-backdrop="static">
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
    <div class="modal fade" id="modal_send_avp_ifc" data-bs-backdrop="static">
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
    <div class="modal fade" id="modal_send_vp_ifc" data-bs-backdrop="static">
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
    <div class="modal fade" id="modal_approve_vp" data-bs-backdrop="static">
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

    <script type="text/javascript" src="<?= base_url('assets_tambahan/') ?>easyui/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?= base_url('assets_tambahan/') ?>easyui/jquery.edatagrid.js"></script>
    <script type="text/javascript" src="<?= base_url('assets_tambahan/') ?>orgchart/orgchart.js"></script>

    <script type="text/javascript">
  // div tab dokumen

      function div_doc_usulan() {
        $('#div_doc_usulan').show();
        $('#div_doc_ifa').hide();
        $('#div_doc_ifa_hps').hide();
        $('#div_doc_ifc').hide();
        $('#div_doc_ifc_hps').hide();
        $('#div_doc_hps_selesai').hide();
        $('#div_doc_ifa_selesai').hide();
        $('#div_doc_ifc_selesai').hide();

        $('#link_div_doc_usulan').addClass('active');
        $('#link_div_doc_ifa').removeClass('active');
        $('#link_div_doc_ifa_hps').removeClass('active');
        $('#link_div_doc_ifc').removeClass('active');
        $('#link_div_doc_ifc_hps').removeClass('active');
        $('#link_div_doc_ifa_selesai').removeClass('active');
        $('#link_div_doc_ifc_selesai').removeClass('active');
        $('#link_div_doc_hps_selesai').removeClass('active');
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

        $('#link_div_doc_usulan').removeClass('active');
        $('#link_div_doc_ifa').addClass('active');
        $('#link_div_doc_ifa_hps').removeClass('active');
        $('#link_div_doc_ifc').removeClass('active');
        $('#link_div_doc_ifc_hps').removeClass('active');
        $('#link_div_doc_ifa_selesai').removeClass('active');
        $('#link_div_doc_ifc_selesai').removeClass('active');
        $('#link_div_doc_hps_selesai').removeClass('active');

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

        $('#link_div_doc_usulan').removeClass('active');
        $('#link_div_doc_ifa').removeClass('active');
        $('#link_div_doc_ifa_hps').addClass('active');
        $('#link_div_doc_ifc').removeClass('active');
        $('#link_div_doc_ifc_hps').removeClass('active');
        $('#link_div_doc_ifa_selesai').removeClass('active');
        $('#link_div_doc_ifc_selesai').removeClass('active');
        $('#link_div_doc_hps_selesai').removeClass('active');
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

        $('#link_div_doc_usulan').removeClass('active');
        $('#link_div_doc_ifa').removeClass('active');
        $('#link_div_doc_ifa_hps').removeClass('active');
        $('#link_div_doc_ifc').addClass('active');
        $('#link_div_doc_ifc_hps').removeClass('active');
        $('#link_div_doc_ifa_selesai').removeClass('active');
        $('#link_div_doc_ifc_selesai').removeClass('active');
        $('#link_div_doc_hps_selesai').removeClass('active');
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

        $('#link_div_doc_usulan').removeClass('active');
        $('#link_div_doc_ifa').removeClass('active');
        $('#link_div_doc_ifa_hps').removeClass('active');
        $('#link_div_doc_ifc').removeClass('active');
        $('#link_div_doc_ifc_hps').addClass('active');
        $('#link_div_doc_ifa_selesai').removeClass('active');
        $('#link_div_doc_ifc_selesai').removeClass('active');
        $('#link_div_doc_hps_selesai').removeClass('active');
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

        $('#link_div_doc_usulan').removeClass('active');
        $('#link_div_doc_ifa').removeClass('active');
        $('#link_div_doc_ifa_hps').removeClass('active');
        $('#link_div_doc_ifc').removeClass('active');
        $('#link_div_doc_ifc_hps').removeClass('active');
        $('#link_div_doc_ifa_selesai').addClass('active');
        $('#link_div_doc_ifc_selesai').removeClass('active');
        $('#link_div_doc_hps_selesai').removeClass('active');
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

        $('#link_div_doc_usulan').removeClass('active');
        $('#link_div_doc_ifa').removeClass('active');
        $('#link_div_doc_ifa_hps').removeClass('active');
        $('#link_div_doc_ifc').removeClass('active');
        $('#link_div_doc_ifc_hps').removeClass('active');
        $('#link_div_doc_ifa_selesai').removeClass('active');
        $('#link_div_doc_ifc_selesai').addClass('active');
        $('#link_div_doc_hps_selesai').removeClass('active');
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

        $('#link_div_doc_usulan').removeClass('active');
        $('#link_div_doc_ifa').removeClass('active');
        $('#link_div_doc_ifa_hps').removeClass('active');
        $('#link_div_doc_ifc').removeClass('active');
        $('#link_div_doc_ifc_hps').removeClass('active');
        $('#link_div_doc_ifa_selesai').removeClass('active');
        $('#link_div_doc_ifc_selesai').removeClass('active');
        $('#link_div_doc_hps_selesai').addClass('active');
      }





  // div tab dokumen

  /* TAB */
  /* Klik Tab Home */
      function fun_div_home() {
        $('#div_home').show();
        $('#div_history').hide();
        $('#div_hirarki').hide();
        $('#link_div_home').addClass('active');
        $('#link_div_history').removeClass('active');
        $('#link_div_hirarki').removeClass('active');
      }
  /* Klik Tab Home */

  /* Klik Tab History */
      function fun_div_history() {
        $('#div_home').hide();
        $('#div_history').show();
        $('#div_hirarki').hide();
        $('#link_div_home').removeClass('active');
        $('#link_div_history').addClass('active');
        $('#link_div_hirarki').removeClass('active');
        $('#table_history').DataTable().ajax.reload();
      }
  /* Klik Tab History */

  /* Klik Tab Hirarki */
      function fun_div_hirarki() {
        $('#div_home').hide();
        $('#div_history').hide();
        $('#div_hirarki').show();
        $('#link_div_home').removeClass('active');
        $('#link_div_history').removeClass('active');
        $('#link_div_hirarki').addClass('active');
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
          console.log(json);
          if ('<?= $_GET['aksi'] ?>' == 'usulan') {
        /* Usualan */
            if (json.pekerjaan_status == '1') {
          /* Reviewed AVP Customer */
              $('#btn_reviewed').css('display', 'block');
              $('#btn_approve').css('display', 'none');
              $('#btn_disposisi_vp').css('display', 'none');
              $('#btn_disposisi_avp').css('display', 'none');
              $('#btn_reject').css('display', 'block');
              $('#btn_reject_avp').css('display', 'none');
          /* Reviewed AVP Customer */
            } else if (json.pekerjaan_status == '2') {
          /* Approve VP Customer */
              $('#btn_reviewed').css('display', 'none');
              $('#btn_approve').css('display', 'block');
              $('#btn_disposisi_vp').css('display', 'none');
              $('#btn_disposisi_avp').css('display', 'none');
              $('#btn_reject').css('display', 'block');
              $('#btn_reject_avp').css('display', 'none');
          /* Approve VP Customer */
          /* Approved VP Cangun */
            } else if (json.pekerjaan_status == '3') {
          /* Approved VP Cangun */
              $('#btn_reviewed').css('display', 'none');
              $('#btn_approve').css('display', 'none');
              $('#btn_disposisi_vp').css('display', 'block');
              $('#btn_disposisi_avp').css('display', 'none');
              $('#btn_reject').css('display', 'block');
              $('#btn_reject_avp').css('display', 'none');
            } else if (json.pekerjaan_status == '4' && json.is_proses == 'y') {
          /* Approved VP Cangun sudah diproses*/
              $('#btn_reviewed').css('display', 'none');
              $('#btn_approve').css('display', 'none');
              $('#btn_disposisi_vp').css('display', 'none');
              $('#btn_disposisi_avp').css('display', 'none');
              $('#btn_reject').css('display', 'none');
              $('#btn_reject_avp').css('display', 'none');
          /* Approved VP Cangun */
            } else if (json.pekerjaan_status == '4') {
          /* Reviewed AVP Cangun */
              $('#btn_reviewed').css('display', 'none');
              $('#btn_approve').css('display', 'none');
              $('#btn_disposisi_vp').css('display', 'none');
              $('#btn_disposisi_avp').css('display', 'block');
              $('#btn_reject').css('display', 'none');
              $('#btn_reject_avp').css('display', 'block');
          /* Reviewed AVP Cangun */
            } else {
          /* Kecuali */
              $('#btn_approve').css('display', 'none');
              $('#btn_reviewed').css('display', 'none');
              $('#btn_disposisi_vp').css('display', 'none');
              $('#btn_disposisi_avp').css('display', 'none');
              $('#btn_reject').css('display', 'none');
              $('#btn_reject_avp').css('display', 'none');
          /* Kecuali */
            }
        /* Usualan */
          } else {
        /* Berjalan */
            if (json.pekerjaan_status == '5' && json.pekerjaan_disposisi_status == '6' && json.is_proses == 'y' && json.id_penanggung_jawab == 'y') {
          /* Reviewed AVP Cangun */
              $('#btn_upload').css('display', 'none');
              $('#btn_upload_hps').css('display', 'none');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_send_avp').css('display', 'block');
              $('#btn_send_koor').css('display', 'none');
              $('#btn_approve_vp').css('display', 'none');
              $('#btn_revisi').css('display', 'none');
              $('#btn_reject_staf').hide();
              $('#btn_ganti_perencana').hide();
            } else if (json.pekerjaan_status == '5' && json.pekerjaan_disposisi_status == '6' && json.is_proses == 'y') {
          /* Reviewed AVP Cangun */
              $('#btn_upload').css('display', 'none');
              $('#btn_upload_hps').css('display', 'none');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_send_avp').css('display', 'none');
              $('#btn_send_koor').css('display', 'none');
              $('#btn_approve_vp').css('display', 'none');
              $('#btn_revisi').css('display', 'none');
              $('#btn_reject_staf').hide();
              $('#btn_ganti_perencana').hide();
            } else if (json.pekerjaan_status == '5' && json.pekerjaan_disposisi_status == '6' && json.is_proses == 'r') {
          /* Reviewed AVP Cangun */
              $('#btn_upload').css('display', 'none');
              $('#btn_upload_hps').css('display', 'none');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_send_avp').css('display', 'none');
              $('#btn_send_avp_koor').css('display', 'none');
              $('#btn_approve_vp').css('display', 'none');
              $('#btn_revisi').css('display', 'none');
              $('#btn_reject_staf').hide();
              $('#btn_ganti_perencana').hide();
          /* Reviewed AVP Cangun */
          // } else if (json.pekerjaan_status == '5' && json.pekerjaan_disposisi_status == '6' && json.is_proses == null) {
          //   /* Reviewed AVP Cangun */
          //   $('#btn_upload').css('display', 'none');
          //   $('#btn_upload_hps').css('display', 'none');
          //   $('#btn_progress').css('display', 'none');
          //   $('#btn_send_avp').css('display', 'block');
          //   $('#btn_send_koor').css('display', 'none');
          //   $('#btn_approve_vp').css('display', 'none');
          //   $('#btn_revisi').css('display', 'block');
          //   $('#btn_reject_staf').hide();
          //   $('#btn_ganti_perencana').hide();
            } else if (json.pekerjaan_status == '5' && json.pekerjaan_disposisi_status == '6' && json.id_penanggung_jawab == 'y') {
          /* Reviewed AVP Cangun */
              $('#btn_upload').css('display', 'none');
              $('#btn_upload_hps').css('display', 'none');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_send_avp').css('display', 'block');
              $('#btn_send_avp_koor').css('display', 'none');
              $('#btn_approve_vp').css('display', 'none');
              $('#btn_revisi').css('display', 'block');
              $('#btn_reject_staf').hide();
              $('#btn_ganti_perencana').hide();
            } else if (json.pekerjaan_status == '5' && json.pekerjaan_disposisi_status == '6' && json.id_penanggung_jawab == 'n') {
          /* Reviewed AVP Cangun */
              $('#btn_upload').css('display', 'none');
              $('#btn_upload_hps').css('display', 'none');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_send_avp').css('display', 'none');
              $('#btn_send_avp_koor').css('display', 'block');
              $('#btn_approve_vp').css('display', 'none');
              $('#btn_revisi').css('display', 'block');
              $('#btn_reject_staf').hide();
              $('#btn_ganti_perencana').hide();
          // Upload File Satf Cangun dan Sudah Diproses
            } else if (json.pekerjaan_status == '5' && json.is_proses == 'y') {
              $('#btn_upload').css('display', 'none');
              $('#btn_upload_hps').css('display', 'none');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_send_avp').css('display', 'none');
              $('#btn_send_avp_koor').css('display', 'none');
              $('#btn_approve_vp').css('display', 'none');
              $('#btn_revisi').css('display', 'home');
              $('#btn_reject_staf').hide();
              $('#btn_ganti_perencana').hide();
          // Upload File Satf Cangun dan Sudah Diproses
            } else if (json.pekerjaan_status == '5') {
          /* Upload File Staf Cangun */
              $('#btn_upload').css('display', 'block');
              $('#btn_upload_hps').css('display', 'block');
              $('#btn_progress').css('display', 'block');
              $('#btn_send_ifa').css('display', 'block');
              $('#btn_send_avp').css('display', 'none');
              $('#btn_send_avp_koor').css('display', 'none');
              $('#btn_approve_vp').css('display', 'none');
              $('#btn_revisi').css('display', 'none');
              $('#btn_reject_staf').show();
              $('#btn_ganti_perencana').show();
          /* Upload File Staf Cangun */
            } else if (json.pekerjaan_status == '6' && json.is_proses == 'y' && json.id_penanggung_jawab == 'y') {
          /* Reviewed AVP Cangun */
              $('#btn_upload').css('display', 'none');
              $('#btn_upload_hps').css('display', 'none');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_send_avp').css('display', 'block');
              $('#btn_send_koor').css('display', 'none');
              $('#btn_approve_vp').css('display', 'none');
              $('#btn_revisi').css('display', 'none');
              $('#btn_reject_staf').hide();
              $('#btn_ganti_perencana').hide();
            } else if (json.pekerjaan_status == '6' && json.is_proses == 'y') {
          /* Reviewed AVP Cangun */
              $('#btn_upload').css('display', 'none');
              $('#btn_upload_hps').css('display', 'none');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_send_avp').css('display', 'none');
              $('#btn_send_koor').css('display', 'none');
              $('#btn_approve_vp').css('display', 'none');
              $('#btn_revisi').css('display', 'none');
              $('#btn_reject_staf').hide();
              $('#btn_ganti_perencana').hide();
            } else if (json.pekerjaan_status == '6' && json.is_proses == 'r') {
          /* Reviewed AVP Cangun */
              $('#btn_upload').css('display', 'none');
              $('#btn_upload_hps').css('display', 'none');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_send_avp').css('display', 'none');
              $('#btn_send_avp_koor').css('display', 'none');
              $('#btn_approve_vp').css('display', 'none');
              $('#btn_revisi').css('display', 'none');
              $('#btn_reject_staf').hide();
              $('#btn_ganti_perencana').hide();
          /* Reviewed AVP Cangun */
            } else if (json.pekerjaan_status == '6' && json.id_penanggung_jawab == 'y') {
          /* Reviewed AVP Cangun */
              $('#btn_upload').css('display', 'none');
              $('#btn_upload_hps').css('display', 'none');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_send_avp').css('display', 'block');
              $('#btn_send_avp_koor').css('display', 'none');
              $('#btn_approve_vp').css('display', 'none');
              $('#btn_revisi').css('display', 'block');
              $('#btn_reject_staf').hide();
              $('#btn_ganti_perencana').hide();
            } else if (json.pekerjaan_status == '6' && json.id_penanggung_jawab == 'n') {
          /* Reviewed AVP Cangun */
              $('#btn_upload').css('display', 'none');
              $('#btn_upload_hps').css('display', 'none');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_send_avp').css('display', 'none');
              $('#btn_send_avp_koor').css('display', 'block');
              $('#btn_approve_vp').css('display', 'none');
              $('#btn_revisi').css('display', 'block');
              $('#btn_reject_staf').hide();
              $('#btn_ganti_perencana').hide();

            } else if (json.pekerjaan_status == '7') {
          /* Approve VP Cangun */
              $('#btn_upload').css('display', 'none');
              $('#btn_upload_hps').css('display', 'none');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_send_avp').css('display', 'none');
              $('#btn_send_avp_koor').css('display', 'none');
              $('#btn_approve_vp').css('display', 'block');
              $('#btn_revisi').css('display', 'block');
              $('#btn_reject_staf').hide();
              $('#btn_ganti_perencana').hide();
          /* Approve VP Cangun */
          // Upload File Staf Cangun Rev IFA
            } else if (json.pekerjaan_status == '8' && json.pekerjaan_disposisi_status == '5') {
          /* Upload File Staf Cangun */
              $('#btn_upload').css('display', 'block');
              $('#btn_upload_hps').css('display', 'block');
              $('#btn_progress').css('display', 'block');
              $('#btn_send_ifa').css('display', 'block');
              $('#btn_send_avp').css('display', 'none');
              $('#btn_send_avp_koor').css('display', 'none');
              $('#btn_approve_vp').css('display', 'none');
              $('#btn_revisi').css('display', 'none');
              $('#btn_reject_staf').show();
              $('#btn_ganti_perencana').show();
          /* Upload File Staf Cangun Rev IFA*/
            } else if (json.pekerjaan_status == '8' && json.pekerjaan_disposisi_status == '6' && json.id_penanggung_jawab == 'y') {
          /* Reviewed AVP Cangun */
              $('#btn_upload').css('display', 'none');
              $('#btn_upload_hps').css('display', 'none');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_send_avp').css('display', 'block');
              $('#btn_send_avp_koor').css('display', 'none');
              $('#btn_approve_vp').css('display', 'none');
              $('#btn_revisi').css('display', 'block');
              $('#btn_reject_staf').hide();
              $('#btn_ganti_perencana').hide();
            } else if (json.pekerjaan_status == '8' && json.pekerjaan_disposisi_status == '6' && json.id_penanggung_jawab == 'n') {
          /* Reviewed AVP Cangun */
              $('#btn_upload').css('display', 'none');
              $('#btn_upload_hps').css('display', 'none');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_send_avp').css('display', 'block');
              $('#btn_send_avp_koor').css('display', 'none');
              $('#btn_approve_vp').css('display', 'none');
              $('#btn_revisi').css('display', 'block');
              $('#btn_reject_staf').hide();
              $('#btn_ganti_perencana').hide();
            } else if (json.pekerjaan_status == '8' && json.is_proses == 'y') {
          /* Approve PIC */
              $('#btn_upload').css('display', 'none');
              $('#btn_upload_hps').css('display', 'none');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_approve_ifa').css('display', 'none');
              $('#btn_revisi_ifa').css('display', 'none');
              $('#btn_reject_staf').hide();
              $('#btn_ganti_perencana').hide();


            } else if (json.pekerjaan_status == '8' && json.id_user == '<?= $data_session['pegawai_nik'] ?>' && json.is_pic == 'y') {
          /* Approve PIC */
              $('#btn_upload').css('display', 'none');
              $('#btn_upload_hps').css('display', 'none');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_approve_ifa').css('display', 'block');
              $('#btn_revisi_ifa').css('display', 'block');
              $('#btn_reject_staf').hide();
              $('#btn_ganti_perencana').hide();
          /* Approve PIC */
            } else if (json.pekerjaan_status == '9' && json.pekerjaan_disposisi_status == '9') {
              $('#btn_approve_ifa_avp').show();
              $('#btn_revisi_ifa').show();
            } else if (json.pekerjaan_status == '10' && json.pekerjaan_disposisi_status == '10') {
              $('#btn_approve_ifa_vp').show();
              $('#btn_revisi_ifa').show();
            } else if (json.pekerjaan_status == '11' && json.pekerjaan_disposisi_status == '11' && json.is_proses == 'y') {
          /* Upload File Staf Cangun */
              $('#btn_upload_ifc').css('display', 'none');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_send_avp_ifc').hide();
              $('#btn_approve_vp').css('display', 'none');
              $('#btn_revisi').css('display', 'none');
              $('#btn_reject_staf').hide();
            } else if (json.pekerjaan_status == '11' && json.pekerjaan_disposisi_status == '12' && json.is_proses == 'y') {
          /* Upload File Staf Cangun */
              $('#btn_upload_ifc').css('display', 'none');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_send_avp_ifc').hide();
              $('#btn_approve_vp').css('display', 'none');
              $('#btn_revisi').css('display', 'none');
              $('#btn_reject_staf').hide();
              $('#btn_ganti_perencana').hide();
            } else if (json.pekerjaan_status == '11' && json.pekerjaan_disposisi_status == '12') {
          /* Upload File Staf Cangun */
              $('#btn_upload_ifc').css('display', 'none');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_send_avp_ifc').show();
              $('#btn_approve_vp').css('display', 'none');
              $('#btn_revisi').css('display', 'block');
              $('#btn_reject_staf').hide();
              $('#btn_ganti_perencana').hide();
            } else if (json.pekerjaan_status == '11') {
          /* Upload File Staf Cangun */
              $('#btn_upload_ifc').css('display', 'block');
              $('#btn_upload_ifc_hps').css('display', 'block');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_send_avp').css('display', 'none');
              $('#btn_send_avp_koor').css('display', 'none');
              $('#btn_approve_vp').css('display', 'none');
              $('#btn_revisi').css('display', 'none');
              $('#btn_reject_staf').hide();
              $('#btn_ganti_perencana').hide();
          /* Upload File Staf Cangun */
            } else if (json.pekerjaan_status == '12' && json.is_proses == 'y') {
          /* Reviewed AVP Cangun */
              $('#btn_upload').css('display', 'none');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_send_avp_ifc').css('display', 'none');
              $('#btn_approve_vp').css('display', 'none');
              $('#btn_revisi').css('display', 'none');
              $('#btn_reject_staf').hide();
              $('#btn_ganti_perencana').hide();
          /* Reviewed AVP Cangun */
            } else if (json.pekerjaan_status == '12' && json.id_penanggung_jawab == 'y') {
          /* Reviewed AVP Cangun */
              $('#btn_upload').css('display', 'none');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_send_avp_ifc').css('display', 'block');
              $('#btn_send_avp_ifc_koor').css('display', 'none');
              $('#btn_approve_vp').css('display', 'none');
              $('#btn_revisi').css('display', 'block');
              $('#btn_reject_staf').hide();
              $('#btn_ganti_perencana').hide();
          /* Reviewed AVP Cangun */
            } else if (json.pekerjaan_status == '12' && json.id_penanggung_jawab == 'n') {
          /* Reviewed AVP Cangun */
              $('#btn_upload').css('display', 'none');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_send_avp_ifc').css('display', 'none');
              $('#btn_send_avp_ifc_koor').css('display', 'block');
              $('#btn_approve_vp').css('display', 'none');
              $('#btn_revisi').css('display', 'block');
              $('#btn_reject_staf').hide();
              $('#btn_ganti_perencana').hide();
          /* Reviewed AVP Cangun */

            } else if (json.pekerjaan_status == '13') {
          /* Approve VP Cangun */
              $('#btn_upload').css('display', 'none');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_send_avp').css('display', 'none');
              $('#btn_send_avp_koor').css('display', 'none');
              $('#btn_approve_vp').css('display', 'block');
              $('#btn_revisi').css('display', 'block');
              $('#btn_reject_staf').hide();
              $('#btn_ganti_perencana').hide();
          /* Approve VP Cangun */
            } else {
          /* Kecuali */
              $('#btn_upload').css('display', 'none');
              $('#btn_progress').css('display', 'none');
              $('#btn_send_ifa').css('display', 'none');
              $('#btn_send_avp').css('display', 'none');
              $('#btn_send_avp_koor').css('display', 'none');
              $('#btn_approve_vp').css('display', 'none');
              $('#btn_revisi').css('display', 'none');
              $('#btn_approve_ifa').css('display', 'none');
              $('#btn_revisi_ifa').css('display', 'none');
              $('#btn_reject_staf').hide();
              $('#btn_ganti_perencana').hide();
          /* Kecuali */
            }
        /* Berjalan */
          }

          $('#pekerjaan_status').val(json.pekerjaan_status);
        });



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
  "dom": 'Bfrtip',
  "ajax": {
    "url": "<?= base_url('project/pekerjaan_usulan/') ?>getPekerjaanDokumen?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>",
    "dataSrc": ""
  },
  "columns": [{
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
  "dom": 'Bfrtip',
  "ajax": {
    "url": "<?= base_url('project/pekerjaan_usulan/') ?>getDokumenBerjalan?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>&pekerjaan_status=<?= $_GET['status'] ?>&is_hps=n",
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
      return full.bagian_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      var data = '';
      if (full.pekerjaan_dokumen_status == '0' && full.revisi_ifc == 'y' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' -Revisi';
      }else if(full.pekerjaan_dokumen_status == '0' && (full.revisi_ifc!='y' && full.revisi_ifc == null) && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi !='')){
        var data = 'IFA Rev '+full.pekerjaan_dokumen_revisi+' -Revisi';
      } else if (full.pekerjaan_dokumen_status == '0') {
        var data = 'Revisi';
      } else if (full.pekerjaan_dokumen_status == '1' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Draft';
      } else if (full.pekerjaan_dokumen_status == '1') {
        var data = 'Draft';
      } else if (full.pekerjaan_dokumen_status == '2' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Send IFA';
      } else if (full.pekerjaan_dokumen_status == '2') {
        var data = 'Send IFA';
      } else if (full.pekerjaan_dokumen_status == '3' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Review IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '3') {
        var data = 'Review IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pic=='y' || full.picavp=='y' || full.picvp=='y') && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi ;
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Approve IFA VP';
      } else if (full.pekerjaan_dokumen_status == '4') {
        var data = 'Approve IFA VP';
      } else if (full.pekerjaan_dokumen_status == '5' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '5') {
        var data = 'IFA'
      } else if (full.pekerjaan_dokumen_status == '6' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '6') {
        var data = 'IFA AVP'
      } else if (full.pekerjaan_dokumen_status == '7' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - IFA VP';
      } else if (full.pekerjaan_dokumen_status == '7') {
        var data = 'IFA VP'
      } else if (full.pekerjaan_dokumen_status == '8' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '8') {
        var data = 'Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '9' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - Send IFC';
      } else if (full.pekerjaan_dokumen_status == '9') {
        var data = 'Send IFC';
      } else if (full.pekerjaan_dokumen_status == '10' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+ ' - reviewed IFC AVP';
      } else if (full.pekerjaan_dokumen_status == '10') {
        var data = 'reviewed IFC AVP'
      } else if (full.pekerjaan_dokumen_status == '11' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+ ' - Approved VP IFC';
      } else if (full.pekerjaan_dokumen_status == '11') {
        var data = 'Approved IFC VP';
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
            // console.log(full);
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
  "dom": 'Bfrtip',
  "ajax": {
    "url": "<?= base_url('project/pekerjaan_usulan/') ?>getDokumenBerjalan?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>&pekerjaan_status=<?= $_GET['status'] ?>&is_hps=y",
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
      return full.bagian_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      var data = '';
      if (full.pekerjaan_dokumen_status == '0' && full.revisi_ifc == 'y' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' -Revisi';
      }else if(full.pekerjaan_dokumen_status == '0' && (full.revisi_ifc!='y' && full.revisi_ifc == null) && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi !='')){
        var data = 'IFA Rev '+full.pekerjaan_dokumen_revisi+' -Revisi';
      } else if (full.pekerjaan_dokumen_status == '0') {
        var data = 'Revisi';
      } else if (full.pekerjaan_dokumen_status == '1' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Draft';
      } else if (full.pekerjaan_dokumen_status == '1') {
        var data = 'Draft';
      } else if (full.pekerjaan_dokumen_status == '2' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Send IFA';
      } else if (full.pekerjaan_dokumen_status == '2') {
        var data = 'Send IFA';
      } else if (full.pekerjaan_dokumen_status == '3' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Review IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '3') {
        var data = 'Review IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pic=='y' || full.picavp=='y' || full.picvp=='y') && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi ;
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Approve IFA VP';
      } else if (full.pekerjaan_dokumen_status == '4') {
        var data = 'Approve IFA VP';
      } else if (full.pekerjaan_dokumen_status == '5' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '5') {
        var data = 'IFA'
      } else if (full.pekerjaan_dokumen_status == '6' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '6') {
        var data = 'IFA AVP'
      } else if (full.pekerjaan_dokumen_status == '7' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - IFA VP';
      } else if (full.pekerjaan_dokumen_status == '7') {
        var data = 'IFA VP'
      } else if (full.pekerjaan_dokumen_status == '8' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '8') {
        var data = 'Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '9' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - Send IFC';
      } else if (full.pekerjaan_dokumen_status == '9') {
        var data = 'Send IFC';
      } else if (full.pekerjaan_dokumen_status == '10' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+ ' - reviewed IFC AVP';
      } else if (full.pekerjaan_dokumen_status == '10') {
        var data = 'reviewed IFC AVP'
      } else if (full.pekerjaan_dokumen_status == '11' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+ ' - Approved VP IFC';
      } else if (full.pekerjaan_dokumen_status == '11') {
        var data = 'Approved IFC VP';
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
            // console.log(full);
      var aksi = '';
      if (full.id_bagian == $('#session_bagian').val() && full.avp == 'y' && full.is_proses != 'y' && full.vp != 'y' && full.pekerjaan_dokumen_status == '2') {
        aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi(this.id)"><i class="fa fa-share"></i></a></center>';
      } else if (full.is_proses == 'y' && full.vp == 'y' && full.pekerjaan_status == '7') {
        aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi(this.id)"><i class="fa fa-share"></i></a></center>';
      } else {
        aksi = '<center> - </center>';
      }
      return aksi;
            // if (full.pekerjaan_status == '5' && full.id_bagian == $('#session_bagian').val()) {
            // } else if (full.pekerjaan_status == '6' && full.id_bagian == $('#session_bagian').val()) {
            //   aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi(this.id)"><i class="fa fa-share"></i></a></center>';
            // } else if (full.pekerjaan_status == '7' && full.id_bagian == $('#session_bagian').val()) {
            //   aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi(this.id)"><i class="fa fa-share"></i></a></center>';
            // } else {
            //   aksi = '-';
            // }

            // return (full.pekerjaan_status <= 6) ? '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi(this.id)"><i class="fa fa-share"></i></a></center>' : '-'
            // return '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi(this.id)"><i class="fa fa-share"></i></a></center>';
    }
  },
  ]
});
    /* Table Dokumen Pekerjaan Berjalan */


    /* Table Dokumen Pekerjaan Berjalan */
    // $('#table_dokumen_approve_vp').DataTable({
    //   "ajax": {
    //     "url": "<?= base_url('project/pekerjaan_usulan/') ?>getAsetDocument?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>&pekerjaan_",
    //     "dataSrc": ""
    //   },
    //   "columns": [{
    //       render: function(data, type, full, meta) {
    //         return meta.row + meta.settings._iDisplayStart + 1;
    //       }
    //     },
    //     {
    //       "data": "pekerjaan_dokumen_nama"
    //     },
    //     {
    //       "data": "pekerjaan_dokumen_status_nama"
    //     },
    //     {
    //       render: function(data, type, full, meta) {
    //         return '-';
    //       }
    //     },
    //     {
    //       render: function(data, type, full, meta) {
    //         return '-';
    //       }
    //     },
    //     {
    //       render: function(data, type, full, meta) {
    //         return full.pekerjaan_dokumen_keterangan;
    //       }
    //     },
    //     {
    //       "render": function(data, type, full, meta) {
    //         return '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_file + '" title="Lihat" onclick="fun_lihat(this.id)" ><i class="bx bx-book m-0"  data-bs-toggle="modal" data-bs-target="#modal_lihat"></i></a></center>';
    //       }
    //     }, {
    //       "render": function(data, type, full, meta) {
    //         return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_file + '~' + full.pekerjaan_dokumen_id + '" title="Download" onclick="fun_download(this.id,this.name)"><i class="fa fa-download"></i></a></center>';
    //       }
    //     }, {
    //       "render": function(data, type, full, meta) {
    //         return (full.pekerjaan_status == '5') ? '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi(this.id)"><i class="fa fa-share"></i></a></center>' : '-';
    //         // return '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi(this.id)"><i class="fa fa-share"></i></a></center>';
    //       }
    //     },
    //   ]
    // });
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
  "dom": 'Bfrtip',
  "ajax": {
    "url": "<?= base_url('project/pekerjaan_usulan/') ?>getDokumenIFA?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>&pekerjaan_status=<?= $_GET['status'] ?>&is_hps=n",
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
      return full.bagian_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      var data = '';
      if (full.pekerjaan_dokumen_status == '0' && full.revisi_ifc == 'y' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' -Revisi';
      }else if(full.pekerjaan_dokumen_status == '0' && (full.revisi_ifc!='y' && full.revisi_ifc == null) && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi !='')){
        var data = 'IFA Rev '+full.pekerjaan_dokumen_revisi+' -Revisi';
      } else if (full.pekerjaan_dokumen_status == '0') {
        var data = 'Revisi';
      } else if (full.pekerjaan_dokumen_status == '1' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Draft';
      } else if (full.pekerjaan_dokumen_status == '1') {
        var data = 'Draft';
      } else if (full.pekerjaan_dokumen_status == '2' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Send IFA';
      } else if (full.pekerjaan_dokumen_status == '2') {
        var data = 'Send IFA';
      } else if (full.pekerjaan_dokumen_status == '3' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Review IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '3') {
        var data = 'Review IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pic=='y' || full.picavp=='y' || full.picvp=='y') && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi ;
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Approve IFA VP';
      } else if (full.pekerjaan_dokumen_status == '4') {
        var data = 'Approve IFA VP';
      } else if (full.pekerjaan_dokumen_status == '5' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '5') {
        var data = 'IFA'
      } else if (full.pekerjaan_dokumen_status == '6' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '6') {
        var data = 'IFA AVP'
      } else if (full.pekerjaan_dokumen_status == '7' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - IFA VP';
      } else if (full.pekerjaan_dokumen_status == '7') {
        var data = 'IFA VP'
      } else if (full.pekerjaan_dokumen_status == '8' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '8') {
        var data = 'Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '9' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - Send IFC';
      } else if (full.pekerjaan_dokumen_status == '9') {
        var data = 'Send IFC';
      } else if (full.pekerjaan_dokumen_status == '10' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+ ' - reviewed IFC AVP';
      } else if (full.pekerjaan_dokumen_status == '10') {
        var data = 'reviewed IFC AVP'
      } else if (full.pekerjaan_dokumen_status == '11' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+ ' - Approved VP IFC';
      } else if (full.pekerjaan_dokumen_status == '11') {
        var data = 'Approved IFC VP';
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
      if (full.id_bagian == $('#session_bagian').val() && full.avp == 'y' && full.is_proses != 'y' && full.vp != 'y') {
        aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi_ifa(this.id)"><i class="fa fa-share"></i></a></center>';
      } else if (full.is_proses == 'a' && full.vp == 'n' && full.is_hps == 'n' && full.pic == 'y') {
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
  "dom": 'Bfrtip',
  "ajax": {
    "url": "<?= base_url('project/pekerjaan_usulan/') ?>getDokumenIFAHPS?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>&pekerjaan_status=<?= $_GET['status'] ?>&is_hps=y",
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
      return full.bagian_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      var data = '';
      if (full.pekerjaan_dokumen_status == '0' && full.revisi_ifc == 'y' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' -Revisi';
      }else if(full.pekerjaan_dokumen_status == '0' && (full.revisi_ifc!='y' && full.revisi_ifc == null) && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi !='')){
        var data = 'IFA Rev '+full.pekerjaan_dokumen_revisi+' -Revisi';
      } else if (full.pekerjaan_dokumen_status == '0') {
        var data = 'Revisi';
      } else if (full.pekerjaan_dokumen_status == '1' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Draft';
      } else if (full.pekerjaan_dokumen_status == '1') {
        var data = 'Draft';
      } else if (full.pekerjaan_dokumen_status == '2' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Send IFA';
      } else if (full.pekerjaan_dokumen_status == '2') {
        var data = 'Send IFA';
      } else if (full.pekerjaan_dokumen_status == '3' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Review IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '3') {
        var data = 'Review IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pic=='y' || full.picavp=='y' || full.picvp=='y') && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi ;
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Approve IFA VP';
      } else if (full.pekerjaan_dokumen_status == '4') {
        var data = 'Approve IFA VP';
      } else if (full.pekerjaan_dokumen_status == '5' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '5') {
        var data = 'IFA'
      } else if (full.pekerjaan_dokumen_status == '6' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '6') {
        var data = 'IFA AVP'
      } else if (full.pekerjaan_dokumen_status == '7' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - IFA VP';
      } else if (full.pekerjaan_dokumen_status == '7') {
        var data = 'IFA VP'
      } else if (full.pekerjaan_dokumen_status == '8' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '8') {
        var data = 'Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '9' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - Send IFC';
      } else if (full.pekerjaan_dokumen_status == '9') {
        var data = 'Send IFC';
      } else if (full.pekerjaan_dokumen_status == '10' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+ ' - reviewed IFC AVP';
      } else if (full.pekerjaan_dokumen_status == '10') {
        var data = 'reviewed IFC AVP'
      } else if (full.pekerjaan_dokumen_status == '11' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+ ' - Approved VP IFC';
      } else if (full.pekerjaan_dokumen_status == '11') {
        var data = 'Approved IFC VP';
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
            // if (full.id_bagian == $('#session_bagian').val() && full.avp == 'y' && full.is_proses != 'y' && full.vp != 'y') {
            //   aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi_ifa(this.id)"><i class="fa fa-share"></i></a></center>';
            // } else if (full.is_proses == 'a' && full.vp == 'n' && full.is_hps == 'y' && full.cc == 'h') {
            //   aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi_ifa(this.id)"><i class="fa fa-share"></i></a></center>';
            // } else if (full.vp == 'n' && full.cc == 'h') {
            //   aksi = '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_id + '" title="Aksi" onclick="fun_aksi_ifa(this.id)"><i class="fa fa-share"></i></a></center>';

            // } else {
      aksi = '<center> - </center>';
            // }
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
  "dom": 'Bfrtip',
  "ajax": {
    "url": "<?= base_url('project/pekerjaan_usulan/') ?>getDokumenIFC?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>&pekerjaan_status=<?= $_GET['status'] ?>&is_hps=n",
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
      return full.bagian_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      var data = '';
      if (full.pekerjaan_dokumen_status == '0' && full.revisi_ifc == 'y' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' -Revisi';
      }else if(full.pekerjaan_dokumen_status == '0' && (full.revisi_ifc!='y' && full.revisi_ifc == null) && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi !='')){
        var data = 'IFA Rev '+full.pekerjaan_dokumen_revisi+' -Revisi';
      } else if (full.pekerjaan_dokumen_status == '0') {
        var data = 'Revisi';
      } else if (full.pekerjaan_dokumen_status == '1' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Draft';
      } else if (full.pekerjaan_dokumen_status == '1') {
        var data = 'Draft';
      } else if (full.pekerjaan_dokumen_status == '2' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Send IFA';
      } else if (full.pekerjaan_dokumen_status == '2') {
        var data = 'Send IFA';
      } else if (full.pekerjaan_dokumen_status == '3' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Review IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '3') {
        var data = 'Review IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pic=='y' || full.picavp=='y' || full.picvp=='y') && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi ;
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Approve IFA VP';
      } else if (full.pekerjaan_dokumen_status == '4') {
        var data = 'Approve IFA VP';
      } else if (full.pekerjaan_dokumen_status == '5' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '5') {
        var data = 'IFA'
      } else if (full.pekerjaan_dokumen_status == '6' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '6') {
        var data = 'IFA AVP'
      } else if (full.pekerjaan_dokumen_status == '7' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - IFA VP';
      } else if (full.pekerjaan_dokumen_status == '7') {
        var data = 'IFA VP'
      } else if (full.pekerjaan_dokumen_status == '8' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '8') {
        var data = 'Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '9' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - Send IFC';
      } else if (full.pekerjaan_dokumen_status == '9') {
        var data = 'Send IFC';
      } else if (full.pekerjaan_dokumen_status == '10' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+ ' - reviewed IFC AVP';
      } else if (full.pekerjaan_dokumen_status == '10') {
        var data = 'reviewed IFC AVP'
      } else if (full.pekerjaan_dokumen_status == '11' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+ ' - Approved VP IFC';
      } else if (full.pekerjaan_dokumen_status == '11') {
        var data = 'Approved IFC VP';
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
    /* Table Dokumen IFC */

    /* Table Dokumen IFC HPS */
$('#table_dokumen_ifc_hps').DataTable({
  "dom": 'Bfrtip',
  "ajax": {
    "url": "<?= base_url('project/pekerjaan_usulan/') ?>getDokumenIFC?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>&pekerjaan_status=<?= $_GET['status'] ?>&is_hps=y",
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
      return full.bagian_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      var data = '';
      if (full.pekerjaan_dokumen_status == '0' && full.revisi_ifc == 'y' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' -Revisi';
      }else if(full.pekerjaan_dokumen_status == '0' && (full.revisi_ifc!='y' && full.revisi_ifc == null) && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi !='')){
        var data = 'IFA Rev '+full.pekerjaan_dokumen_revisi+' -Revisi';
      } else if (full.pekerjaan_dokumen_status == '0') {
        var data = 'Revisi';
      } else if (full.pekerjaan_dokumen_status == '1' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Draft';
      } else if (full.pekerjaan_dokumen_status == '1') {
        var data = 'Draft';
      } else if (full.pekerjaan_dokumen_status == '2' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Send IFA';
      } else if (full.pekerjaan_dokumen_status == '2') {
        var data = 'Send IFA';
      } else if (full.pekerjaan_dokumen_status == '3' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Review IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '3') {
        var data = 'Review IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pic=='y' || full.picavp=='y' || full.picvp=='y') && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi ;
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Approve IFA VP';
      } else if (full.pekerjaan_dokumen_status == '4') {
        var data = 'Approve IFA VP';
      } else if (full.pekerjaan_dokumen_status == '5' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '5') {
        var data = 'IFA'
      } else if (full.pekerjaan_dokumen_status == '6' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '6') {
        var data = 'IFA AVP'
      } else if (full.pekerjaan_dokumen_status == '7' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - IFA VP';
      } else if (full.pekerjaan_dokumen_status == '7') {
        var data = 'IFA VP'
      } else if (full.pekerjaan_dokumen_status == '8' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '8') {
        var data = 'Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '9' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - Send IFC';
      } else if (full.pekerjaan_dokumen_status == '9') {
        var data = 'Send IFC';
      } else if (full.pekerjaan_dokumen_status == '10' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+ ' - reviewed IFC AVP';
      } else if (full.pekerjaan_dokumen_status == '10') {
        var data = 'reviewed IFC AVP'
      } else if (full.pekerjaan_dokumen_status == '11' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+ ' - Approved VP IFC';
      } else if (full.pekerjaan_dokumen_status == '11') {
        var data = 'Approved IFC VP';
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
  "dom": 'Bfrtip',
  "ajax": {
    "url": "<?= base_url('project/pekerjaan_usulan/') ?>getDokumenSelesaiIFA?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>&pekerjaan_status=<?= $_GET['status'] ?>&is_hps=n",
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
      return full.bagian_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      var data = '';
      if (full.pekerjaan_dokumen_status == '0' && full.revisi_ifc == 'y' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' -Revisi';
      }else if(full.pekerjaan_dokumen_status == '0' && (full.revisi_ifc!='y' && full.revisi_ifc == null) && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi !='')){
        var data = 'IFA Rev '+full.pekerjaan_dokumen_revisi+' -Revisi';
      } else if (full.pekerjaan_dokumen_status == '0') {
        var data = 'Revisi';
      } else if (full.pekerjaan_dokumen_status == '1' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Draft';
      } else if (full.pekerjaan_dokumen_status == '1') {
        var data = 'Draft';
      } else if (full.pekerjaan_dokumen_status == '2' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Send IFA';
      } else if (full.pekerjaan_dokumen_status == '2') {
        var data = 'Send IFA';
      } else if (full.pekerjaan_dokumen_status == '3' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Review IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '3') {
        var data = 'Review IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pic=='y' || full.picavp=='y' || full.picvp=='y') && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi ;
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Approve IFA VP';
      } else if (full.pekerjaan_dokumen_status == '4') {
        var data = 'Approve IFA VP';
      } else if (full.pekerjaan_dokumen_status == '5' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '5') {
        var data = 'IFA'
      } else if (full.pekerjaan_dokumen_status == '6' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '6') {
        var data = 'IFA AVP'
      } else if (full.pekerjaan_dokumen_status == '7' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - IFA VP';
      } else if (full.pekerjaan_dokumen_status == '7') {
        var data = 'IFA VP'
      } else if (full.pekerjaan_dokumen_status == '8' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '8') {
        var data = 'Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '9' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - Send IFC';
      } else if (full.pekerjaan_dokumen_status == '9') {
        var data = 'Send IFC';
      } else if (full.pekerjaan_dokumen_status == '10' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+ ' - reviewed IFC AVP';
      } else if (full.pekerjaan_dokumen_status == '10') {
        var data = 'reviewed IFC AVP'
      } else if (full.pekerjaan_dokumen_status == '11' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+ ' - Approved VP IFC';
      } else if (full.pekerjaan_dokumen_status == '11') {
        var data = 'Approved IFC VP';
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
  "dom": 'Bfrtip',
  "ajax": {
    "url": "<?= base_url('project/pekerjaan_usulan/') ?>getDokumenSelesai?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>&pekerjaan_status=<?= $_GET['status'] ?>&is_hps=n",
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
      return full.bagian_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      var data = '';
      if (full.pekerjaan_dokumen_status == '0' && full.revisi_ifc == 'y' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' -Revisi';
      }else if(full.pekerjaan_dokumen_status == '0' && (full.revisi_ifc!='y' && full.revisi_ifc == null) && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi !='')){
        var data = 'IFA Rev '+full.pekerjaan_dokumen_revisi+' -Revisi';
      } else if (full.pekerjaan_dokumen_status == '0') {
        var data = 'Revisi';
      } else if (full.pekerjaan_dokumen_status == '1' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Draft';
      } else if (full.pekerjaan_dokumen_status == '1') {
        var data = 'Draft';
      } else if (full.pekerjaan_dokumen_status == '2' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Send IFA';
      } else if (full.pekerjaan_dokumen_status == '2') {
        var data = 'Send IFA';
      } else if (full.pekerjaan_dokumen_status == '3' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Review IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '3') {
        var data = 'Review IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pic=='y' || full.picavp=='y' || full.picvp=='y') && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi ;
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Approve IFA VP';
      } else if (full.pekerjaan_dokumen_status == '4') {
        var data = 'Approve IFA VP';
      } else if (full.pekerjaan_dokumen_status == '5' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '5') {
        var data = 'IFA'
      } else if (full.pekerjaan_dokumen_status == '6' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '6') {
        var data = 'IFA AVP'
      } else if (full.pekerjaan_dokumen_status == '7' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - IFA VP';
      } else if (full.pekerjaan_dokumen_status == '7') {
        var data = 'IFA VP'
      } else if (full.pekerjaan_dokumen_status == '8' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '8') {
        var data = 'Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '9' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - Send IFC';
      } else if (full.pekerjaan_dokumen_status == '9') {
        var data = 'Send IFC';
      } else if (full.pekerjaan_dokumen_status == '10' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+ ' - reviewed IFC AVP';
      } else if (full.pekerjaan_dokumen_status == '10') {
        var data = 'reviewed IFC AVP'
      } else if (full.pekerjaan_dokumen_status == '11' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+ ' - Approved VP IFC';
      } else if (full.pekerjaan_dokumen_status == '11') {
        var data = 'Approved IFC VP';
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
  "dom": 'Bfrtip',
  "ajax": {
    "url": "<?= base_url('project/pekerjaan_usulan/') ?>getDokumenSelesaiHPS?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $_GET['pekerjaan_id']) ?>&pekerjaan_status=<?= $_GET['status'] ?>&is_hps=y",
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
      return full.bagian_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      var data = '';
      if (full.pekerjaan_dokumen_status == '0' && full.revisi_ifc == 'y' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' -Revisi';
      }else if(full.pekerjaan_dokumen_status == '0' && (full.revisi_ifc!='y' && full.revisi_ifc == null) && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi !='')){
        var data = 'IFA Rev '+full.pekerjaan_dokumen_revisi+' -Revisi';
      } else if (full.pekerjaan_dokumen_status == '0') {
        var data = 'Revisi';
      } else if (full.pekerjaan_dokumen_status == '1' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Draft';
      } else if (full.pekerjaan_dokumen_status == '1') {
        var data = 'Draft';
      } else if (full.pekerjaan_dokumen_status == '2' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Send IFA';
      } else if (full.pekerjaan_dokumen_status == '2') {
        var data = 'Send IFA';
      } else if (full.pekerjaan_dokumen_status == '3' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Review IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '3') {
        var data = 'Review IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pic=='y' || full.picavp=='y' || full.picvp=='y') && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi ;
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Approve IFA VP';
      } else if (full.pekerjaan_dokumen_status == '4') {
        var data = 'Approve IFA VP';
      } else if (full.pekerjaan_dokumen_status == '5' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '5') {
        var data = 'IFA'
      } else if (full.pekerjaan_dokumen_status == '6' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '6') {
        var data = 'IFA AVP'
      } else if (full.pekerjaan_dokumen_status == '7' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - IFA VP';
      } else if (full.pekerjaan_dokumen_status == '7') {
        var data = 'IFA VP'
      } else if (full.pekerjaan_dokumen_status == '8' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '8') {
        var data = 'Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '9' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - Send IFC';
      } else if (full.pekerjaan_dokumen_status == '9') {
        var data = 'Send IFC';
      } else if (full.pekerjaan_dokumen_status == '10' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+ ' - reviewed IFC AVP';
      } else if (full.pekerjaan_dokumen_status == '10') {
        var data = 'reviewed IFC AVP'
      } else if (full.pekerjaan_dokumen_status == '11' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+ ' - Approved VP IFC';
      } else if (full.pekerjaan_dokumen_status == '11') {
        var data = 'Approved IFC VP';
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
      if (full.pekerjaan_dokumen_status == '0' && full.revisi_ifc == 'y' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' -Revisi';
      }else if(full.pekerjaan_dokumen_status == '0' && (full.revisi_ifc!='y' && full.revisi_ifc == null) && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi !='')){
        var data = 'IFA Rev '+full.pekerjaan_dokumen_revisi+' -Revisi';
      } else if (full.pekerjaan_dokumen_status == '0') {
        var data = 'Revisi';
      } else if (full.pekerjaan_dokumen_status == '1' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Draft';
      } else if (full.pekerjaan_dokumen_status == '1') {
        var data = 'Draft';
      } else if (full.pekerjaan_dokumen_status == '2' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Send IFA';
      } else if (full.pekerjaan_dokumen_status == '2') {
        var data = 'Send IFA';
      } else if (full.pekerjaan_dokumen_status == '3' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Review IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '3') {
        var data = 'Review IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pic=='y' || full.picavp=='y' || full.picvp=='y') && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi ;
      } else if (full.pekerjaan_dokumen_status == '4' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Approve IFA VP';
      } else if (full.pekerjaan_dokumen_status == '4') {
        var data = 'Approve IFA VP';
      } else if (full.pekerjaan_dokumen_status == '5' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
      } else if (full.pekerjaan_dokumen_status == '5') {
        var data = 'IFA'
      } else if (full.pekerjaan_dokumen_status == '6' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - IFA AVP';
      } else if (full.pekerjaan_dokumen_status == '6') {
        var data = 'IFA AVP'
      } else if (full.pekerjaan_dokumen_status == '7' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - IFA VP';
      } else if (full.pekerjaan_dokumen_status == '7') {
        var data = 'IFA VP'
      } else if (full.pekerjaan_dokumen_status == '8' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '8') {
        var data = 'Draft IFC';
      } else if (full.pekerjaan_dokumen_status == '9' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+' - Send IFC';
      } else if (full.pekerjaan_dokumen_status == '9') {
        var data = 'Send IFC';
      } else if (full.pekerjaan_dokumen_status == '10' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+ ' - reviewed IFC AVP';
      } else if (full.pekerjaan_dokumen_status == '10') {
        var data = 'reviewed IFC AVP'
      } else if (full.pekerjaan_dokumen_status == '11' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
        var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi+ ' - Approved VP IFC';
      } else if (full.pekerjaan_dokumen_status == '11') {
        var data = 'Approved IFC VP';
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
        // {
        // "data": "pekerjaan_dokumen_keterangan"
        // },
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
    url: '<?= base_url('project/pekerjaan_usulan/getUserListVP') ?>',
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
    /* Disposisi Koordinator Pekerjaan */

    /* Reviewed AVP Terkait */
$('#id_user_vp').select2({
  dropdownParent: $('#modal_vp'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUserListVP') ?>',
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
    /* Reviewed AVP Terkait */

    /* Disposisi VP AVP Terkait */
$('#id_user_vp_avp').select2({
  dropdownParent: $('#modal_avp'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUserListVP') ?>',
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
    /* Disposisi VP AVP Terkait */

    /* Klasifikasi Pekerjaan */
$('#id_klasifikasi_pekerjaan_avp').select2({
  dropdownParent: $('#modal_avp'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('history/history/getKlasifikasiPekerjaan?is_rkap=') ?>' + $('#is_rkap').val(),
    dataType: 'json',
    type: 'GET',
    data: function(params) {
      var queryParameters = {
        klasifikasi_pekerjaan_nama: params.term
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
    url: '<?= base_url('project/pekerjaan_usulan/getUserListAVP') ?>',
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

$('#id_user_avp_instrumen').select2({
  dropdownParent: $('#modal_avp'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUserListAVP') ?>',
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

$('#id_user_avp_listrik').select2({
  dropdownParent: $('#modal_avp'),
  placeholder: 'Pilih',
  ajax: {
    delay: 250,
    url: '<?= base_url('project/pekerjaan_usulan/getUserListAVP') ?>',
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
        // console.log(note_reject);
        // call_ajax_page('project/pekerjaan_usulan');
      setTimeout(() => {
        if ($('#is_rkap').val() == '1') {
          window.location.replace('<?= base_url('project/RKAP') ?>')
        } else {
          window.location.replace('<?= base_url('project/Non_RKAP') ?>');
        }
      }, "1000");
      $.getJSON('<?= base_url('project/pekerjaan_usulan/prosesReject') ?>', {
        pekerjaan_id: id,
        note_reject: note_reject
      }, function(json) {
        console.log('ok');
      });
    }
  });
}
  /* Klik Reject Customer */

  /* Klik Reject AVP */
function fun_reject_avp(id) {
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
}
  /* Klik Reject AVP */

  /* Klik Reject Staf */
function fun_reject_staf(id) {
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

  // klik ganti perencana
function fun_ganti_perencana(id) {
  $('#modal_ganti_perencana').modal('show');

}
  // klik ganti perencana

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
    if (json.jumlah_revisi > 0) {
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
      onBeginEdit: function(index, row) {
          /* Combobox */
          /*template*/
        var cb = $(this).datagrid('getEditor', {
          index: index,
          field: 'pekerjaan_template_nama'
        });
        $(cb.target).combobox('setValue', row.id_pekerjaan_template);
          /*template*/
          /*bidang*/
        var bd = $(this).datagrid('getEditor', {
          index: index,
          field: 'bidang_nama'
        });
        $(bd.target).combobox('setValue', row.id_bidang);
          /*bidang*/
          /*urutan*/
        var up = $(this).datagrid('getEditor', {
          index: index,
          field: 'urutan_proyek_nama'
        });
        $(up.target).combobox('setValue', row.id_urutan_proyek);
          /*urutan*/
          /*section area*/
        var sa = $(this).datagrid('getEditor', {
          index: index,
          field: 'section_area_nama'
        });
        $(sa.target).combobox('setValue', row.id_section_area);
          /*section area*/
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
            type: 'combobox',
            options: {
              required: true,
              idField: 'urutan_proyek_id',
              textField: 'urutan_proyek_nama',
              valueField: 'urutan_proyek_id',
              url: '<?= base_url() ?>project/pekerjaan_usulan/getUrutanProyek',
              mode: 'remote',
              fitColumns: true,
              columns: [
                [{
                  field: 'urutan_proyek_nama',
                  title: 'Urutan Proyek',
                  width: 400
                }, ]
                ],
              panelHeight: 135
            },
          },
        },
        {
          field: 'section_area_nama',
          title: 'Section Area',
          width: '20%',
          editor: {
            type: 'combobox',
            options: {
              required: true,
              idField: 'section_area_id',
              textField: 'section_area_nama',
              valueField: 'section_area_id',
              url: '<?= base_url() ?>project/pekerjaan_usulan/getSectionArea',
              mode: 'remote',
              fitColumns: true,
              columns: [
                [{
                  field: 'section_area_nama',
                  title: 'Urutan Proyek',
                  width: 400
                }, ]
                ],
              panelHeight: 135
            },
          },
        },
        {
          field: 'pekerjaan_dokumen_nomor',
          title: 'No Dokumen',
          width: '20%',
          editor: {
            type: 'textbox',
            options: {
              required: true,
            }
          },
        },
        {
          field: 'pekerjaan_dokumen_jumlah',
          title: 'Jumlah Halaman',
          width: '20%',
          editor: {
            type: 'numberbox',
            options: {
              required: true,
            }
          },
        },
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
              accept: 'application/pdf',
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
                  formData.append('file', file, file.name);
                }

                $.ajax({
                  url: '<?= base_url('project/pekerjaan_usulan/insertFilePekerjaanDokumen') ?>',
                  type: 'post',
                  data: formData,
                  contentType: false,
                  processData: false,
                  success: function(data) {
                    self.filebox('setText', data);
                  }
                })
              }
            },
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
    if (json.jumlah_revisi > 0) {
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
      rowStyler: function(index, row) {
        if (row.pekerjaan_dokumen_status == '0') {
          return 'background-color:#FF0000;font-weight:bold;';
        }
      },
      onBeginEdit: function(index, row) {
          /* Combobox */
          /*template*/
        var cb = $(this).datagrid('getEditor', {
          index: index,
          field: 'pekerjaan_template_nama'
        });
        $(cb.target).combobox('setValue', row.id_pekerjaan_template);
          /*template*/
          /*bidang*/
        var bd = $(this).datagrid('getEditor', {
          index: index,
          field: 'bidang_nama'
        });
        $(bd.target).combobox('setValue', row.id_bidang);
          /*bidang*/
          /*urutan*/
        var up = $(this).datagrid('getEditor', {
          index: index,
          field: 'urutan_proyek_nama'
        });
        $(up.target).combobox('setValue', row.id_urutan_proyek);
          /*urutan*/
          /*section area*/
        var sa = $(this).datagrid('getEditor', {
          index: index,
          field: 'section_area_nama'
        });
        $(sa.target).combobox('setValue', row.id_section_area);
          /*section area*/
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
            type: 'combobox',
            options: {
              required: true,
              idField: 'urutan_proyek_id',
              textField: 'urutan_proyek_nama',
              valueField: 'urutan_proyek_id',
              url: '<?= base_url() ?>project/pekerjaan_usulan/getUrutanProyek',
              mode: 'remote',
              fitColumns: true,
              columns: [
                [{
                  field: 'urutan_proyek_nama',
                  title: 'Urutan Proyek',
                  width: 400
                }, ]
                ],
              panelHeight: 135
            },
          },
        },
        {
          field: 'section_area_nama',
          title: 'Section Area',
          width: '20%',
          editor: {
            type: 'combobox',
            options: {
              required: true,
              idField: 'section_area_id',
              textField: 'section_area_nama',
              valueField: 'section_area_id',
              url: '<?= base_url() ?>project/pekerjaan_usulan/getSectionArea',
              mode: 'remote',
              fitColumns: true,
              columns: [
                [{
                  field: 'section_area_nama',
                  title: 'Urutan Proyek',
                  width: 400
                }, ]
                ],
              panelHeight: 135
            },
          },
        },
        {
          field: 'pekerjaan_dokumen_nomor',
          title: 'No Dokumen',
          width: '20%',
          editor: {
            type: 'textbox',
            options:{
              required: true,
            }
          },
        },
        {
          field: 'pekerjaan_dokumen_jumlah',
          title: 'Jumlah Halaman',
          width: '20%',
          editor: {
            type: 'numberbox',
            options: {
              required: true,
            }
          },
        },
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
              accept: 'application/pdf',
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
                  success: function(data) {
                    self.filebox('setText', data);
                  }
                })
              }
            },
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
    if (json.id_penanggung_jawab == 'n') {
      $('#id_user_staf_ifc').prop('disabled', true);
    } else if (json.id_penanggung_jawab == 'y' && json.id_user != $('#session_user').val()) {
      $('#id_user_staf_ifc').prop('disabled', false);
    } else if (json.id_penanggung_jawab == 'y' && json.id_user == $('#session_user').val()) {
      $('#id_user_staf_ifc').prop('disabled', false);
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
      onBeginEdit: function(index, row) {
          /* Combobox */
          /*template*/
        var cb = $(this).datagrid('getEditor', {
          index: index,
          field: 'pekerjaan_template_nama'
        });
        $(cb.target).combobox('setValue', row.id_pekerjaan_template);
          /*template*/
          /*bidang*/
        var bd = $(this).datagrid('getEditor', {
          index: index,
          field: 'bidang_nama'
        });
        $(bd.target).combobox('setValue', row.id_bidang);
          /*bidang*/
          /*urutan*/
        var up = $(this).datagrid('getEditor', {
          index: index,
          field: 'urutan_proyek_nama'
        });
        $(up.target).combobox('setValue', row.id_urutan_proyek);
          /*urutan*/
          /*section area*/
        var sa = $(this).datagrid('getEditor', {
          index: index,
          field: 'section_area_nama'
        });
        $(sa.target).combobox('setValue', row.id_section_area);
          /*section area*/
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
            type: 'combobox',
            options: {
              required: true,
              idField: 'urutan_proyek_id',
              textField: 'urutan_proyek_nama',
              valueField: 'urutan_proyek_id',
              url: '<?= base_url() ?>project/pekerjaan_usulan/getUrutanProyek',
              mode: 'remote',
              fitColumns: true,
              columns: [
                [{
                  field: 'urutan_proyek_nama',
                  title: 'Urutan Proyek',
                  width: 400
                }, ]
                ],
              panelHeight: 135
            },
          },
        },
        {
          field: 'section_area_nama',
          title: 'Section Area',
          width: '20%',
          editor: {
            type: 'combobox',
            options: {
              required: true,
              idField: 'section_area_id',
              textField: 'section_area_nama',
              valueField: 'section_area_id',
              url: '<?= base_url() ?>project/pekerjaan_usulan/getSectionArea',
              mode: 'remote',
              fitColumns: true,
              columns: [
                [{
                  field: 'section_area_nama',
                  title: 'Urutan Proyek',
                  width: 400
                }, ]
                ],
              panelHeight: 135
            },
          },
        },
        {
          field: 'pekerjaan_dokumen_nomor',
          title: 'No Dokumen',
          width: '20%',
          editor: {
            type: 'textbox',
            options:{
              required: true,
            }
          },
        },
        {
          field: 'pekerjaan_dokumen_jumlah',
          title: 'Jumlah Halaman',
          width: '20%',
          editor: {
            type: 'numberbox',
            options: {
              required: true,
            }
          },
        },
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
              accept: 'application/pdf',
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
                  success: function(data) {
                    self.filebox('setText', data);
                  }
                })
              }
            },
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
    if (json.id_penanggung_jawab == 'n') {
      $('#id_user_staf_ifc_hps').prop('disabled', true);
    } else if (json.id_penanggung_jawab == 'y' && json.id_user != $('#session_user').val()) {
      $('#id_user_staf_ifc_hps').prop('disabled', false);
    } else if (json.id_penanggung_jawab == 'y' && json.id_user == $('#session_user').val()) {
      $('#id_user_staf_ifc_hps').prop('disabled', false);
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
      onBeginEdit: function(index, row) {
          /* Combobox */
          /*template*/
        var cb = $(this).datagrid('getEditor', {
          index: index,
          field: 'pekerjaan_template_nama'
        });
        $(cb.target).combobox('setValue', row.id_pekerjaan_template);
          /*template*/
          /*bidang*/
        var bd = $(this).datagrid('getEditor', {
          index: index,
          field: 'bidang_nama'
        });
        $(bd.target).combobox('setValue', row.id_bidang);
          /*bidang*/
          /*urutan*/
        var up = $(this).datagrid('getEditor', {
          index: index,
          field: 'urutan_proyek_nama'
        });
        $(up.target).combobox('setValue', row.id_urutan_proyek);
          /*urutan*/
          /*section area*/
        var sa = $(this).datagrid('getEditor', {
          index: index,
          field: 'section_area_nama'
        });
        $(sa.target).combobox('setValue', row.id_section_area);
          /*section area*/
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
            type: 'combobox',
            options: {
              required: true,
              idField: 'urutan_proyek_id',
              textField: 'urutan_proyek_nama',
              valueField: 'urutan_proyek_id',
              url: '<?= base_url() ?>project/pekerjaan_usulan/getUrutanProyek',
              mode: 'remote',
              fitColumns: true,
              columns: [
                [{
                  field: 'urutan_proyek_nama',
                  title: 'Urutan Proyek',
                  width: 400
                }, ]
                ],
              panelHeight: 135
            },
          },
        },
        {
          field: 'section_area_nama',
          title: 'Section Area',
          width: '20%',
          editor: {
            type: 'combobox',
            options: {
              required: true,
              idField: 'section_area_id',
              textField: 'section_area_nama',
              valueField: 'section_area_id',
              url: '<?= base_url() ?>project/pekerjaan_usulan/getSectionArea',
              mode: 'remote',
              fitColumns: true,
              columns: [
                [{
                  field: 'section_area_nama',
                  title: 'Urutan Proyek',
                  width: 400
                }, ]
                ],
              panelHeight: 135
            },
          },
        },
        {
          field: 'pekerjaan_dokumen_nomor',
          title: 'No Dokumen',
          width: '20%',
          editor: {
            type: 'textbox',
            options:{
              required: true,
            }
          },
        },
        {
          field: 'pekerjaan_dokumen_jumlah',
          title: 'Jumlah Halaman',
          width: '20%',
          editor: {
            type: 'numberbox',
            options: {
              required: true,
            }
          },
        },
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
              accept: 'application/pdf',
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
                  success: function(data) {
                    self.filebox('setText', data);
                  }
                })
              }
            },
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

  /* Klik Approve IFA */
function fun_approve_ifa(id, text) {
  Swal.fire({
    title: text,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya"
  }).then(function(result) {
    if (result.value) {
      $.getJSON('<?= base_url('project/pekerjaan_usulan/prosesApproveIFA') ?>', {
        pekerjaan_id: id,
        pekerjaan_status: $('#pekerjaan_status').val()
      }, function(json) {
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
    // }
    // })
}
  /* Klik Approve IFA */

  /* Klik Approve IFA */
function fun_approve_ifa_avp(id, text) {
  Swal.fire({
    title: text,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya"
  }).then(function(result) {
    if (result.value) {
      $.getJSON('<?= base_url('project/pekerjaan_usulan/prosesApproveIFAAVP') ?>', {
        pekerjaan_id: id,
        pekerjaan_status: $('#pekerjaan_status').val()
      }, function(json) {
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
    // }
    // })
}
  /* Klik Approve IFA */

  /* Klik Approve IFA */
function fun_approve_ifa_vp(id, text) {
  Swal.fire({
    title: text,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya"
  }).then(function(result) {
    if (result.value) {
      $.getJSON('<?= base_url('project/pekerjaan_usulan/prosesApproveIFAVP') ?>', {
        pekerjaan_id: id,
        pekerjaan_status: $('#pekerjaan_status').val()
      }, function(json) {
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
    // }
    // })
}
  /* Klik Approve IFA */

  /* Klik Reject IFA */
function fun_reject_ifa(id) {
  Swal.fire({
    title: "Anda Yakin Reject?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya"
  }).then(function(result) {
    if (result.value) {
      $.getJSON('<?= base_url('project/pekerjaan_usulan/prosesRejectIFA') ?>', {
        pekerjaan_id: id
      }, function(json) {
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
}
  /* Klik Reject IFA */

  /* Klik Aksi Dokumen Pekerjaan */
function fun_aksi(id) {
  $('#modal_aksi').modal('show');
  $('#div_keterangan').hide();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/') ?>getDokumenAksi?pekerjaan_dokumen_id=' + id, function(json) {
    $('#pekerjaan_dokumen_id_temp').val(id);
    $('#aksi_nama').val(json.pekerjaan_dokumen_nama);
    $('#pekerjaan_status_dokumen').val('<?= $_GET['status'] ?>');
  });
}
  /* Klik Aksi Dokumen Pekerjaan */

  /* Klik Aksi Dokumen Pekerjaan IFA (CC) */
function fun_aksi_cc(id) {
  $('#modal_aksi_cc').modal('show');
  $('#div_keterangan_cc').hide();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/') ?>getDokumenAksi?pekerjaan_dokumen_id=' + id, function(json) {
    $('#pekerjaan_dokumen_id_temp_cc').val(id);
    $('#aksi_nama_cc').val(json.pekerjaan_dokumen_nama);
  });
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
  $('#modal_aksi_ifa').modal('show');
  $('#div_keterangan_ifa').hide();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/') ?>getDokumenAksi?pekerjaan_dokumen_id=' + id, function(json) {
    $('#pekerjaan_dokumen_id_temp_ifa').val(id);
    $('#aksi_nama_ifa').val(json.pekerjaan_dokumen_nama);
    $('#pekerjaan_dokumen_status_ifa').val(json.pekerjaan_dokumen_status);
  });
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
  $('#modal_aksi_ifc').modal('show');
  $('#div_keterangan').hide();
  $.getJSON('<?= base_url('project/pekerjaan_usulan/') ?>getDokumenAksi?pekerjaan_dokumen_id=' + id, function(json) {
    $('#pekerjaan_dokumen_id_temp_ifc').val(id);
    $('#aksi_nama_ifc').val(json.pekerjaan_dokumen_nama);
  });
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
  data.append('id_user', $('#id_perencana_baru').val());

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
          window.location.replace('<?= base_url('project/RKAP') ?>')
        } else {
          window.location.replace('<?= base_url('project/Non_RKAP') ?>');
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
            }, "1000");
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
    $('#dg_document').edatagrid('addRow', {
      index: 0,
      row: {
        pekerjaan_id: id,
        is_hps: 'n',
      }
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
    $('#dg_document_hps').edatagrid('addRow', {
      index: 0,
      row: {
        pekerjaan_id: id,
        is_hps: 'y',
      }
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
    $('#dg_document_ifc').edatagrid('addRow', {
      index: 0,
      row: {
        pekerjaan_id: id,
        is_hps: 'n',
      }
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
    $('#dg_document_ifc_hps').edatagrid('addRow', {
      index: 0,
      row: {
        pekerjaan_id: id,
        is_hps: 'y',
      }
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
    // $('#modal_lihat').modal('show');
    // $('#document').remove();
    // $('#div_document').append('<iframe src="https://docs.google.com/viewer?url=<?= base_url('document/') ?>' + data + '&embedded=true" frameborder="0" id="document" width="100%" height="350px"></iframe>');
    // $('#div_document').append('<embed src="<?= base_url('document/') ?>' + data + '#toolbar=0" frameborder="0" id="document" width="100%">');
    // $('#div_document').append('<embed src="<?= base_url('document/') ?>' + data + '#toolbar=0&navpanes=0&scrollbar=0" type="application/pdf" frameBorder="0" scrolling="auto" height="100%" width="100%" id="document"></embed>');

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
    if (json.jumlah_revisi > 0) {
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
      // console.log(json);
      // console.log(result);
      // console.log(re/sult);
    if (json.jumlah_revisi > 0) {
      $('#btn_revisi_ifa').show();
      $('#btn_approve_ifa').hide();
    } else {
      $('#btn_approve_ifa').show();
      $('#btn_revisi_ifa').hide();
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
</script>