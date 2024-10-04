<link rel="stylesheet" type="text/css" href="<?= base_url('assets_tambahan/') ?>easyui/themes/default/easyui.css">
<link rel="stylesheet" type="text/css" href="<?= base_url('assets_tambahan/') ?>easyui/themes/icon.css">

<?php
$jml = $this->db->query("SELECT COUNT(*) AS total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' AND (pekerjaan_disposisi_status = '8' OR pekerjaan_disposisi_status = '5')");
$isi_jml = $jml->row_array();

$data_session = $this->session->userdata();

$sql_pekerjaan = $this->db->query("SELECT * FROM dec.dec_pekerjaan a LEFT JOIN global.global_pegawai b ON a.pic = b.pegawai_nik LEFT JOIN global.global_klasifikasi_pekerjaan c ON a.id_klasifikasi_pekerjaan = c.klasifikasi_pekerjaan_id WHERE pekerjaan_id = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) . "'");
$isi_pekerjaan = $sql_pekerjaan->row_array();

$sql_disposisi = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi_transmital WHERE id_user = '" . $data_session['pegawai_nik'] . "' AND id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' AND pekerjaan_disposisi_transmital_status = '" . $this->input->get('status') . "'");
$data_disposisi = $sql_disposisi->row_array();

// $sql_bagian = $this->db->query("SELECT * FROM global.global_bagian a LEFT JOIN global.global_bagian_detail b ON a.bagian_id = b.id_bagian LEFT JOIN global.global_pegawai c ON c.pegawai_nik = b.id_pegawai WHERE b.id_pegawai = '" . $data_session['pegawai_nik'] . "'");
if($data_disposisi){
  $sql_bagian = $this->db->query("SELECT * FROM global.global_bagian a LEFT JOIN global.global_bagian_detail b ON a.bagian_id = b.id_bagian LEFT JOIN global.global_pegawai c ON c.pegawai_nik = b.id_pegawai WHERE a.bagian_id = '" . $data_disposisi['id_bagian'] . "'");
}else{
  $sql_bagian = $this->db->query("SELECT * FROM global.global_bagian a LEFT JOIN global.global_bagian_detail b ON a.bagian_id = b.id_bagian LEFT JOIN global.global_pegawai c ON c.pegawai_nik = b.id_pegawai WHERE c.pegawai_nik= '" . $data_session['pegawai_nik'] . "'");  
}

$data_bagian = $sql_bagian->row_array();

// $bagian = $this->db->query("SELECT bagian_id,bagian_nama FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_bagian_detail b ON b.id_pegawai = a.id_user LEFT JOIN global.global_bagian c ON c.bagian_id = b.id_bagian WHERE id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '5'")->result_array();

$pic_bagian = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi_transmital WHERE id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' AND id_user = '" . $data_session['pegawai_nik'] . "' AND pekerjaan_disposisi_transmital_status ='0'")->result_array();

$bagian = $this->db->get_where('global.global_bagian', ['bagian_disposisi' => 'y'])->result_array();

if ($this->input->get('status') === '0') {
  $where = ['id_user' => $data_session['pegawai_nik']];
} else {
  $where = ['1' => '1', 'pekerjaan_disposisi_transmital_status <' => '1'];
}
$dokumen_bagian = $this->db->select('*')
->from('dec.dec_pekerjaan_disposisi_transmital a')
->where('id_pekerjaan', $this->input->get('pekerjaan_id'))
->where($where)
->join('global.global_bagian c', 'c.bagian_id = a.id_bagian', 'left')
->get()
->result_array();

?>

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


<div class="page-content">
  <div class="container-fluid">
    <!-- start page title -->
    <div class="row">
      <div class="col-12">
        <a href="<?= base_url('project/transmital') ?>" class="btn btn-success"><u><i class="fa fa-arrow-left"></i> Kembali</u></a>
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
                <a class="nav-link active bg-secondary bg-gradient" href="javascript:;" onclick="fun_div_home()" id="link_div_home">Home</a>
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
                <input type="text" name="pekerjaan_id" id="pekerjaan_id" value="<?= $this->input->get('pekerjaan_id') ?>" style="display:none">
                <input type="text" name="bagian_id" id="bagian_id" style="display: none;">
                <input hidden type="text" name="session_poscode" id="session_poscode" value="<?= $data_session['pegawai_poscode'] ?>">
                <input hidden type="text" name="session_direct_superior" id="session_direct_superior" value="<?= $data_session['pegawai_direct_superior'] ?>">
                <input hidden type="text" name="session_user" id="session_user" value="<?= $data_session['pegawai_nik'] ?>">
                <input hidden type="text" name="session_bagian" id="session_bagian" value="<?php echo (!empty($data_bagian['bagian_id'])) ? $data_bagian['bagian_id'] : '' ?>" style="display: none;">
                <input hidden type="text" name="pekerjaan_status" id="pekerjaan_status" style="display:none">
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
                <div style="overflow:auto;">
                  <p class="text-muted" style="word-wrap: break-word;">
                    <?= (!empty($pekerjaan)) ? ($pekerjaan['pekerjaan_deskripsi']) : '-' ?>
                  </p>
                </div>
              </div>
            </div>
            <div class="d-flex">
              <div class="flex-grow-1 overflow-hidden">
                <h5 class="text-truncate font-size-15">Catatan Disposisi</h5>
                <p class="text-muted"><?= (!empty($data_disposisi['pekerjaan_disposisi_transmital_catatan'])) ? ($data_disposisi['pekerjaan_disposisi_transmital_catatan']) : '-' ?></p>
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
              <!-- Tombol -->
              <div class="col-12"></div>
              <?php if ($this->input->get('aksi') == 'usulan') : ?>
                <!-- usulan -->
                <div id="btn_disposisi" class="col-sm-4 col-md-3" style="display: none;">
                  <div class="mt-4">
                    <button type="button" class="btn btn-primary col-10" onclick="fun_disposisi('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) ?>', 'Apakah Anda Yakin Kirim AVP?')">Disposisi</button>
                  </div>
                </div>
                <!-- usulan -->
              <?php elseif ($this->input->get('aksi') == 'waspro') : ?>
                <!-- waspro -->
                <div id="btn_send" class="col-sm-4 col-md-3" style="display: none;">
                  <div class="mt-4">
                    <button type="button" class="btn btn-primary col-10" onclick="fun_send('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) ?>', 'Apakah Anda Yakin Send AVP?')">Send AVP</button>
                  </div>
                </div>
                <div id="btn_reviewed" class="col-sm-4 col-md-3" style="display: none;">
                  <div class="mt-4">
                    <button type="button" class="btn btn-primary col-10" onclick="fun_reviewed('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) ?>', 'Apakah Anda Yakin Review?')">Review AVP</button>
                  </div>
                </div>
                <div id="btn_reject" class="col-sm-4 col-md-3" style="display: none;">
                  <div class="mt-4">
                    <button type="button" class="btn btn-danger col-10" onclick="fun_reject('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) ?>', 'Apakah Anda Yakin Reject?')">Reject</button>
                  </div>
                </div>
                <!-- waspro -->
              <?php elseif ($this->input->get('aksi') == 'cangun') : ?>
                <!-- cangun -->
                <div id="btn_cangun_send" class="col-sm-4 col-md-3" style="display: none;">
                  <div class="mt-4">
                    <button type="button" class="btn btn-primary col-10" onclick="fun_send_cangun('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) ?>', 'Apakah Anda Yakin Kirim AVP?')">Send AVP</button>
                  </div>
                </div>
                <div id="btn_cangun_reviewed" class="col-sm-4 col-md-3" style="display: none;">
                  <div class="mt-4">
                    <button type="button" class="btn btn-success col-10" onclick="fun_reviewed_cangun('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) ?>', 'Apakah Anda Yakin Reviewed?')">Reviewed AVP</button>
                  </div>
                </div>
                <div id="btn_cangun_approved" class="col-sm-4 col-md-3" style="display: none;">
                  <div class="mt-4">
                    <button type="button" class="btn btn-success col-10" onclick="fun_approved_cangun('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) ?>', 'Apakah Anda Yakin Approve?')">Approved VP</button>
                  </div>
                </div>
                <div id="btn_cangun_reject" class="col-sm-4 col-md-3" style="display: none;">
                  <div class="mt-4">
                    <button type="button" class="btn btn-danger col-10" onclick="fun_reject_cangun('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) ?>', 'Apakah Anda Yakin Reject?')">Reject</button>
                  </div>
                </div>
                <!-- cangun -->
              <?php elseif ($this->input->get('aksi') == 'selesai') : ?>
                <div id="btn_kembali_inprogress" class="col-sm-4 col-md-3" style="display: none;">
                  <div class="mt-4">
                    <button type="button" class="btn btn-primary col-10" onclick="fun_kembali_upload('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) ?>', 'Apakah Anda Akan Kembali Upload Ulang?')">Upload Ulang</button>
                  </div>
                </div>
              <?php endif ?>
              <!-- Tombol -->
            </div>
          </div>
        </div>
      </div>
      <!-- Div Atas -->

      <!-- Div Bawah -->
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <?php if ($pic_bagian && $this->input->get('status') == '5' && $this->input->get('aksi') == 'waspro') : ?>
            <div id="btn_close" class="col-sm-4 col-md-3">
              <button type="button" class="btn btn-warning col-10" onclick="fun_close_transmital('<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) ?>', 'Apakah Anda Yakin Close Pekerjaan Ini?')">Close Pekerjaan</button>
            </div>
          <?php endif ?>
        </div>
      </div>
    </div>
    <div class="col-lg-12">
      <div class="card">
        <!-- link navigasi -->
        <div class="card-body">
          <ul class="nav nav-tabs">
            <?php
            foreach ($dokumen_bagian as $key => $val_bagian) :
              if ($this->input->get('aksi') == 'usulan') :
                ?>
                <li class="nav-item">
                  <a class="nav-link link_div_dokumen" href="javascript:void(0);" id="link_div_dokumen_<?= $val_bagian['bagian_id'] ?>" onClick="fun_div_dokumen_usulan('<?= $val_bagian['bagian_id'] ?>')">
                    <?= $val_bagian['bagian_nama'] ?>
                  </a>
                </li>
              <?php else : ?>
                <li class="nav-item">
                  <a class="nav-link link_div_dokumen " href="javascript:void(0);" id="link_div_dokumen_<?= $val_bagian['bagian_id'] ?>" onClick="fun_div_dokumen_waspro('<?= $val_bagian['bagian_id'] ?>')">
                    <?= $val_bagian['bagian_nama'] ?>
                  </a>
                  <ul class="nav nav-tabs">
                    <li id="nav_waspro_kontraktor_<?= $val_bagian['bagian_id'] ?>" class="nav-item nav_waspro" style="display: none;">
                      <a class="nav-link link_div_dokumen_kontraktor" href="javascript:void(0);" id="link_div_dokumen_kontraktor_<?= $val_bagian['bagian_id'] ?>" onClick="fun_div_dokumen_kontraktor('<?= $val_bagian['bagian_id'] ?>')">
                        Doc Kontraktor
                      </a>
                    </li>
                    <li id="nav_waspro_cangun_<?= $val_bagian['bagian_id'] ?>" class="nav-item nav_waspro" style="display: none;">
                      <a class="nav-link link_div_dokumen_cangun" href="javascript:void(0);" id="link_div_dokumen_cangun_<?= $val_bagian['bagian_id'] ?>" onClick="fun_div_dokumen_cangun('<?= $val_bagian['bagian_id'] ?>')">
                        Doc Cangun
                      </a>
                    </li>
                  </ul>
                </li>
              <?php endif; ?>
            <?php endforeach; ?>
          </ul>
        </div>
        <!-- link navigasi -->

        <!-- dokumen usulan -->
        <?php foreach ($dokumen_bagian as $key => $val_bagian) : ?>
          <div class="div_dokumen" id="div_dokumen_<?= $val_bagian['bagian_id'] ?>" style="display: none;">
            <div class="card-body">
              <h4 class="card-title mb-4">Dokumen <?= $val_bagian['bagian_nama'] ?>
            </h4>
            <table class="table table-striped table-bordered align-middle mb-0 table_dokumen" id="table_dokumen_<?= $val_bagian['bagian_id'] ?>" style="display: none;width: 100%;">
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
        <!-- dokumen usulan -->

        <!-- dokumen kontraktor -->
        <div class="div_dokumen_kontraktor" id="div_dokumen_kontraktor_<?= $val_bagian['bagian_id'] ?>" style="display: none;">
          <div class="card-body">
            <h4 class="card-title mb-4">Dokumen <?= $val_bagian['bagian_nama'] ?> - Kontraktor
              <?php if (!empty($pic_bagian)) {
                foreach ($pic_bagian as $key => $val_pic) {
                  if ($val_pic['id_bagian'] == $val_bagian['bagian_id']) {
                    ?>
                    <?php if ($this->input->get('aksi') == 'waspro') : ?>
                      <button type="button" class="btn btn-info col-2 float-end btn_tambah_dokumen_kontraktor" onclick="fun_upload_dokumen_kontraktor('<?= $this->input->get('pekerjaan_id') ?>','<?= $val_bagian['bagian_id'] ?>')" id="btn_tambah_dokumen_kontraktor_<?= $val_bagian['bagian_id'] ?>">
                        Upload
                      </button>
                    <?php endif; ?>
                    <?php
                  }
                }
              }
              ?>
            </h4>
            <table class="table table-striped table-bordered align-middle mb-0 table_dokumen_kontraktor" id="table_dokumen_kontraktor_<?= $val_bagian['bagian_id'] ?>" style="display: none;width: 100%;">
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
        <!-- /* dokumen kontraktor */ -->

        <!-- dokumen cangun -->
        <div class="div_dokumen_cangun" id="div_dokumen_cangun_<?= $val_bagian['bagian_id'] ?>" style="display: none;">
          <div class="card-body">
            <h4 class="card-title mb-4">Dokumen <?= $val_bagian['bagian_nama'] ?> - Cangun</h4>
            <table class="table table-striped table-bordered align-middle mb-0 table_dokumen_cangun" id="table_dokumen_cangun_<?= $val_bagian['bagian_id'] ?>" style="display: none;width: 100%;">
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
        <!-- dokumen cangun -->
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Div History -->
<div class="row" id="div_history" style="display:none">
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
<div class="row" id="div_hirarki" style="display:none">
  <div class="col-lg-12">
    <div class="card scroll">
      <div class="card-body">
        <div class="tree">
          <?php
          $sql_jumlah = $this->db->query("SELECT pekerjaan_disposisi_status FROM dec.dec_pekerjaan_disposisi a LEFT JOIN dec.dec_pekerjaan b ON a.id_pekerjaan = b.pekerjaan_id WHERE is_aktif = 'y' AND a.id_pekerjaan = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) . "' AND  pekerjaan_disposisi_status <= '6' GROUP BY pekerjaan_disposisi_status ORDER BY cast(pekerjaan_disposisi_status as integer) ASC");
          $dataJumlah = $sql_jumlah->result_array();

          $sql_pekerjaan = $this->db->query("SELECT * FROM dec.dec_pekerjaan a LEFT JOIN global.global_pegawai b ON a.pic = b.pegawai_nik WHERE a.pekerjaan_id = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) . "'");
          $dataPekerjaan = $sql_pekerjaan->row_array();

          $sql_cc = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik WHERE id_pekerjaan ='" . $this->input->get('pekerjaan_id') . "' AND is_cc IS NOT NULL AND is_aktif = 'y'");
          $data_cc = $sql_cc->result_array();
          $ada_cc = $this->db->query("SELECT COUNT(*) as total FROM dec.dec_pekerjaan_disposisi WHERE id_pekerjaan = '" . $this->input->get('pekerjaan_id') . "' AND pekerjaan_disposisi_status = '8' AND is_cc IS NOT NULL")->row_array();
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
                  $sql_1 = ($dataJumlah[0]['pekerjaan_disposisi_status'] <= 3) ? $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik WHERE is_aktif = 'y' AND id_pekerjaan = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) . "' AND pekerjaan_disposisi_status = '" . $dataJumlah[0]['pekerjaan_disposisi_status'] . "'") : $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik LEFT JOIN global.global_bagian_detail c ON c.id_pegawai = a.id_user LEFT JOIN global.global_bagian d ON d.bagian_id = c.id_bagian  WHERE is_aktif = 'y' AND id_pekerjaan = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) . "' AND pekerjaan_disposisi_status = '" . $dataJumlah[0]['pekerjaan_disposisi_status'] . "' AND b.pegawai_direct_superior = '" . $dataPekerjaan['pegawai_poscode'] . "'");
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
                            $sql_2 = ($dataJumlah[1]['pekerjaan_disposisi_status'] <= 4) ? $this->db->query("SELECT pegawai_nik,id_penanggung_jawab,pegawai_nama,bagian_id FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik LEFT JOIN global.global_bagian_detail c ON c.id_pegawai = a.id_user LEFT JOIN global.global_bagian d ON d.bagian_id = c.id_bagian WHERE is_aktif = 'y' AND id_pekerjaan = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) . "' AND pekerjaan_disposisi_status = '" . $dataJumlah[1]['pekerjaan_disposisi_status'] . "' GROUP BY pegawai_nik,id_penanggung_jawab,pegawai_nama,bagian_id") : $this->db->query("SELECT pegawai_nik,id_penanggung_jawab,pegawai_nama,bagian_id FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik LEFT JOIN global.global_bagian_detail c ON c.id_pegawai = a.id_user LEFT JOIN global.global_bagian d ON d.bagian_id = c.id_bagian WHERE is_aktif = 'y' AND id_pekerjaan = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) . "' AND pekerjaan_disposisi_status = '" . $dataJumlah[1]['pekerjaan_disposisi_status'] . "' AND b.pegawai_direct_superior = '" . $value1['pegawai_poscode'] . "' GROUP BY pegawai_nik,id_penanggung_jawab,pegawai_nama,bagian_id");
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
                                      $sql_3 = ($dataJumlah[2]['pekerjaan_disposisi_status'] <= 4) ? $this->db->query("SELECT pegawai_nik,id_penanggung_jawab,pegawai_nama,bagian_id FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik LEFT JOIN global.global_bagian_detail c ON c.id_pegawai = a.id_user LEFT JOIN global.global_bagian d ON d.bagian_id = c.id_bagian WHERE is_aktif = 'y' AND id_pekerjaan = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) . "' AND pekerjaan_disposisi_status = '" . $dataJumlah[2]['pekerjaan_disposisi_status'] . "' GROUP BY pegawai_nik,id_penanggung_jawab,pegawai_nama,bagian_id") : $this->db->query("SELECT pegawai_nik,id_penanggung_jawab,pegawai_nama,bagian_id FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik LEFT JOIN global.global_bagian_detail c ON c.id_pegawai = a.id_user LEFT JOIN global.global_bagian d ON d.bagian_id = c.id_bagian WHERE is_aktif = 'y' AND id_pekerjaan = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) . "' AND pekerjaan_disposisi_status = '" . $dataJumlah[2]['pekerjaan_disposisi_status'] . "' AND d.bagian_id = '" . $value2['bagian_id'] . "' GROUP BY pegawai_nik,id_penanggung_jawab,pegawai_nama,bagian_id");
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
                                              $sql_4 = ($dataJumlah[3]['pekerjaan_disposisi_status'] <= 4) ? $this->db->query("SELECT pegawai_nik,id_penanggung_jawab,pegawai_nama,bagian_id FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik LEFT JOIN global.global_bagian_detail c ON c.id_pegawai = a.id_user LEFT JOIN global.global_bagian d ON d.bagian_id = c.id_bagian WHERE is_aktif = 'y' AND id_pekerjaan = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) . "' AND pekerjaan_disposisi_status = '" . $dataJumlah[3]['pekerjaan_disposisi_status'] . "' GROUP BY pegawai_nik,id_penanggung_jawab,pegawai_nama,bagian_id ORDER BY id_penanggung_jawab DESC") : $this->db->query("SELECT pegawai_nik,id_penanggung_jawab,pegawai_nama,bagian_id FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik LEFT JOIN global.global_bagian_detail c ON c.id_pegawai = a.id_user LEFT JOIN global.global_bagian d ON d.bagian_id = c.id_bagian WHERE is_aktif = 'y' AND id_pekerjaan = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) . "' AND pekerjaan_disposisi_status = '" . $dataJumlah[3]['pekerjaan_disposisi_status'] . "' AND d.bagian_id = '" . $value3['bagian_id'] . "' GROUP BY pegawai_nik,id_penanggung_jawab,pegawai_nama,bagian_id ORDER BY id_penanggung_jawab DESC");
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
                                                        $sql_5 = ($dataJumlah[4]['pekerjaan_disposisi_status'] <= 3) ? $this->db->query("SELECT id_penanggung_jawab,pegawai_nik,pegawai_nama FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik LEFT JOIN global.global_bagian_detail c ON c.id_pegawai = a.id_user LEFT JOIN global.global_bagian d ON d.bagian_id = c.id_bagian WHERE is_aktif = 'y' AND a.id_pekerjaan = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) . "' AND pekerjaan_disposisi_status = '" . $dataJumlah[4]['pekerjaan_disposisi_status'] . "' AND d.bagian_id = '" . $value4['bagian_id'] . "' GROUP BY pegawai_nik,id_penanggung_jawab,pegawai_nama") : $this->db->query("SELECT id_penanggung_jawab,pegawai_nik,pegawai_nama FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik LEFT JOIN global.global_bagian_detail c ON c.id_pegawai = a.id_user LEFT JOIN global.global_bagian d ON d.bagian_id = c.id_bagian WHERE is_aktif = 'y' AND a.id_pekerjaan = '" . preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) . "' AND pekerjaan_disposisi_status = '" . $dataJumlah[4]['pekerjaan_disposisi_status'] . "'  AND d.bagian_id = '" . $value4['bagian_id'] . "' GROUP BY pegawai_nik,id_penanggung_jawab,pegawai_nama");
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

<!-- MODAL DISPOSISI -->
<div class="modal fade" id="modal_disposisi" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-xl modal-dialog-scrollable ">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Disposisi AVP</h4>
      </div>
      <div class="modal-body">
        <form id="form_modal_disposisi">
          <div class="card-body">
            <input type="text" name="pekerjaan_id_disposisi" id="pekerjaan_id_disposisi" style="display:none">
            <div class="form-group row col-md-12 mb-3">
              <label class="col-md-4 form-label">Nama Kontraktor</label>
              <input type="text" name="pekerjaan_kontraktor_nama" id="pekerjaan_kontraktor_nama" class="form-control">
            </div>
            <?php
            foreach ($bagian as $key => $val_bagian) :
              ?>
              <div class="form-group row col-md-12 mb-3">
                <label class="col-md-4 form-label">PIC - <?= $val_bagian['bagian_nama'] ?></label>
                <select name="pic_bagian[<?= $val_bagian['bagian_id'] ?>]" id="pic_bagian_<?= $val_bagian['bagian_id'] ?>" class="form-control col-md-8 select2 pic_bagian" style="width: 100%">
                </select>
              </div>
              <?php
            endforeach;
            ?>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" id="close_disposisi" class="btn btn-default border border-dark" data-dismiss="modal" onclick="fun_close_disposisi()">Close</button>
            <button type="button" id="proses_disposisi" class="btn btn-primary" onclick="fun_proses_disposisi()">Disposisi</button>
            <button class="btn btn-primary" type="button" id="loading_form_dokumen" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- MODAL DISPOSISI -->

<!-- MODAL UPLOAD DOKUMEN KONTRAKTOR -->
<div class="modal fade" id="modal_upload_dokumen_kontraktor" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Upload Dokumen Kontraktor</h4>
      </div>
      <div class="modal-body">
        <form id="form_upload_dokumen_kontraktor">
          <div class="form-group row col-md-12">
            <table id="dg_dokumen_kontraktor" title="Document" style="width:100%" toolbar="#toolbar_dokumen_kontraktor" pagination="true" idField="id" rownumbers="true" fitColumns="true" singleSelect="true">
            </table>
            <div id="toolbar_dokumen_kontraktor">
              <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="fun_tambah_dokumen_kontraktor()">New</a>
              <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="
              fun_hapus_dokumen_kontraktor()">Delete</a>
              <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-save" plain="true" onclick="fun_simpan_dokumen_kontraktor()">Save</a>
              <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-undo" plain="true" onclick="javascript:$('#dg_dokumen_kontraktor').edatagrid('cancelRow')">Cancel</a>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" id="close_dokumen_kontraktor" class="btn btn-default border border-dark" data-dismiss="modal" onclick="fun_close_dokumen_kontraktor()">Close</button>
            <button type="button" id="draft_dokumen_kontraktor" class="btn btn-primary" onclick="fun_draft_dokumen_kontraktor()">Draft</button>
            <button class="btn btn-primary" type="button" id="loading_form_dokumen_dokumen" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- MODAL UPLOAD DOKUMEN KONTRAKTOR -->

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

<div class="modal fade" id="modal_history_transmital" data-backdrop="static">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">History Dokumen</h4>
      </div>
      <input type="hidden" id="jadwal_id" name="jadwal_id" value="">
      <div class="modal-body">
        <table class="table table-bordered table-striped" id="table_dokumen_history_transmital" width="100%">
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

<script type="text/javascript" src="<?= base_url('assets_tambahan/') ?>easyui/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?= base_url('assets_tambahan/') ?>easyui/jquery.edatagrid.js"></script>
<script type="text/javascript" src="<?= base_url('assets_tambahan/') ?>orgchart/orgchart.js"></script>

<script type="text/javascript">
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

    /* tab dokumen usulan */
  function fun_div_dokumen_usulan(id) {
    $('.link_div_dokumen').removeClass('active bg-secondary bg-gradient');
    $('#link_div_dokumen_' + id).addClass('active bg-secondary bg-gradient');
    $('.div_dokumen').hide();
    $('.table_dokumen').hide();
    $('#div_dokumen_' + id).show();
    $('#table_dokumen_' + id).show();
    $('#table_dokumen_' + id).DataTable().ajax.reload();
  }
    /* tab dokumen usulan */

    /*tab dokumen waspro*/
  function fun_div_dokumen_waspro(id) {
    $('.link_div_dokumen').removeClass('active bg-secondary bg-gradient');
    $('.link_div_dokumen_cangun').removeClass('active bg-info bg-gradient');
    $('.link_div_dokumen_kontraktor').removeClass('active bg-success bg-gradient');
    $('#link_div_dokumen_' + id).addClass('active bg-secondary bg-gradient');
    $('.nav_waspro').hide();
    $('#nav_waspro_kontraktor_' + id).show();
    $('#nav_waspro_cangun_' + id).show();
  }

    /*tab dokumen cangun*/
  function fun_div_dokumen_cangun(id) {
    hide_div_dokumen();
    $('.link_div_dokumen_cangun').removeClass('active bg-info bg-gradient');
    $('.link_div_dokumen_kontraktor').removeClass('active bg-success bg-gradient');
    $('#link_div_dokumen_cangun_' + id).addClass('active bg-info bg-gradient');
    $('#div_dokumen_cangun_' + id).show();
    $('#table_dokumen_cangun_' + id).show();
    $('#table_dokumen_cangun_' + id).DataTable().ajax.reload();
  }
    /*tab dokumen cangun*/

    /*tab dokumen kontraktor*/
  function fun_div_dokumen_kontraktor(id) {
    hide_div_dokumen();
    $('.link_div_dokumen_cangun').removeClass('active bg-info bg-gradient');
    $('.link_div_dokumen_kontraktor').removeClass('active bg-success bg-gradient');
    $('#link_div_dokumen_kontraktor_' + id).addClass('active bg-success bg-gradient');
    $('#div_dokumen_kontraktor_' + id).show();
    $('#table_dokumen_kontraktor_' + id).show();
    $('#table_dokumen_kontraktor_' + id).DataTable().ajax.reload();
  }
    /*tab dokumen kontraktor*/

    /*hidden tab doc*/
  function hide_div_dokumen() {
    $('.div_dokumen_kontraktor').hide();
    $('.table_dokumen_kontraktor').hide();
    $('.div_dokumen_cangun').hide();
    $('.table_dokumen_cangun').hide();
      // $('.btn_tambah_dokumen_kontraktor').hide();
  }
    /*hidden tab doc*/

    /**/
    /*tab dokumen waspro*/

    /* TAB */

  $(function() {
      /*get user transmital*/
    $('.pic_bagian').select2({
      dropdownParent: $('#modal_disposisi'),
      allowClear: true,
      placeholder: 'Pilih',
      ajax: {
        delay: 250,
        url: '<?= base_url('project/transmital/getUserListWaspro?is_avp=n') ?>',
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
      /*get user transmital*/

    $('.select2-selection').css({
      height: 'auto',
      margin: '0px -10px 0px -10px'
    });
    $('.select2').css('width', '100%');


  })



  $(function() {
    $.getJSON('<?= base_url('project/transmital/getPekerjaan') ?>', {
      pekerjaan_id: "<?= preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) ?>",
      pekerjaan_status: "<?= $this->input->get('status') ?>",
      aksi: '<?= $this->input->get('aksi') ?>',
    }, function(json) {
      if ('<?= $this->input->get('aksi') ?>' == 'usulan') {
        if ((json.pekerjaan_status == '14' || json.pekerjaan_status == '15') && json.pekerjaan_disposisi_transmital_status == '0' && json.is_proses_transmital == 'y') {
          $('#btn_disposisi').css('display', 'none');
        } else if ((json.pekerjaan_status == '14' || json.pekerjaan_status == '15') && json.pekerjaan_disposisi_transmital_status == '0') {
          $('#btn_disposisi').css('display', 'block');
        }
      } else if ('<?= $this->input->get('aksi') ?>' == 'waspro') {
        if ((json.pekerjaan_status == '14' || json.pekerjaan_status == '15') && json.pekerjaan_disposisi_transmital_status == '0' && json.is_proses == 'y') {
          $('#btn_send').hide();
          $('#btn_reviewed').hide();
          $('#btn_reject').hide();
        } else if ((json.pekerjaan_status == '14' || json.pekerjaan_status == '15') && (json.pekerjaan_disposisi_transmital_status == '0' || json.pekerjaan_disposisi_transmital_status == '5')) {
          $('#btn_send').show();
          $('#btn_reviewed').hide();
          $('#btn_reject').show();
        } else if ((json.pekerjaan_status == '14' || json.pekerjaan_status == '15') && json.pekerjaan_disposisi_transmital_status == '1' && json.is_proses == 'y') {
          $('#btn_send').hide();
          $('#btn_reviewed').hide();
          $('#btn_reject').hide();
        } else if ((json.pekerjaan_status == '14' || json.pekerjaan_status == '15') && json.pekerjaan_disposisi_transmital_status == '1') {
          $('#btn_send').hide();
          $('#btn_reviewed').show();
          $('#btn_reject').show();
        }
      } else if ('<?= $this->input->get('aksi') ?>' == 'cangun') {
        if ((json.pekerjaan_status == '14' || json.pekerjaan_status == '15') && json.pekerjaan_disposisi_transmital_status == '2' && json.is_proses == 'y') {
          $('#btn_cangun_send').hide();
          $('#btn_cangun_reviewed').hide();
          $('#btn_cangun_approved').hide();
          $('#btn_cangun_reject').hide();
        } else if ((json.pekerjaan_status == '14' || json.pekerjaan_status == '15') && json.pekerjaan_disposisi_transmital_status == '2') {
          $('#btn_cangun_send').show();
          $('#btn_cangun_reviewed').hide();
          $('#btn_cangun_approved').hide();
          $('#btn_cangun_reject').show();
        } else if ((json.pekerjaan_status == '14' || json.pekerjaan_status == '15') && json.pekerjaan_disposisi_transmital_status == '3' && json.is_proses == 'y') {
          $('#btn_cangun_send').hide();
          $('#btn_cangun_reviewed').hide();
          $('#btn_cangun_approved').hide();
          $('#btn_cangun_reject').hide();
        } else if ((json.pekerjaan_status == '14' || json.pekerjaan_status == '15') && json.pekerjaan_disposisi_transmital_status == '3') {
          $('#btn_cangun_send').hide();
          $('#btn_cangun_reviewed').show();
          $('#btn_cangun_approved').hide();
          $('#btn_cangun_reject').show();
        } else if ((json.pekerjaan_status == '14' || json.pekerjaan_status == '15') && json.pekerjaan_disposisi_transmital_status == '4' && json.is_proses == 'y') {
          $('#btn_cangun_send').hide();
          $('#btn_cangun_reviewed').hide();
          $('#btn_cangun_approved').hide();
          $('#btn_cangun_reject').hide();
        } else if ((json.pekerjaan_status == '14' || json.pekerjaan_status == '15') && json.pekerjaan_disposisi_transmital_status == '4') {
          $('#btn_cangun_send').hide();
          $('#btn_cangun_reviewed').hide();
          $('#btn_cangun_approved').show();
          $('#btn_cangun_reject').show();
        }
      } else if ('<?= $this->input->get('aksi') == 'selesai' ?>') {
        if ((json.pekerjaan_status == '14' || json.pekerjaan_status == '15') && json.pekerjaan_disposisi_transmital_status == '5' && json.is_proses == 'y') {
          $('#btn_kembali_inprogress').show();
        } else if ((json.pekerjaan_status == '14' || json.pekerjaan_status == '15') && json.pekerjaan_disposisi_transmital_status == '5') {
          $('#btn_kembali_inprogress').show();
        }
      } else {
        if (json.pekerjaan_disposisi_transmital_status == '4' && json.is_proses == 'y') {} else if (json.pekerjaan_disposisi_transmital_status == '4') {}
      }

    $('#pekerjaan_status').val(json.pekerjaan_disposisi_transmital_status);
  });

<?php foreach ($dokumen_bagian as $key => $val_bagian) : ?> $('#table_dokumen_<?= $val_bagian['bagian_id'] ?> thead tr').clone(true).addClass('filters_table_dokumen_<?= $val_bagian['bagian_id'] ?>').appendTo('#table_dokumen_<?= $val_bagian['bagian_id'] ?> thead');
$('#table_dokumen_<?= $val_bagian['bagian_id'] ?>').DataTable({
  orderCellsTop: true,
  initComplete: function() {
    var api = this.api();
    api.columns().eq(0).each(function(colIdx) {
      var cell = $('.filters_table_dokumen_<?= $val_bagian['bagian_id'] ?> th').eq(
        $(api.column(colIdx).header()).index()
        );
      var title = $(cell).text();
      $(cell).html('<input type="text" class="form-control" style="width:100%" placeholder="' + title + '" />');

      $('input', $('.filters_table_dokumen_<?= $val_bagian['bagian_id'] ?> th').eq($(api.column(colIdx).header()).index())).off('keyup change').on('keyup change', function(e) {
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
  select: {
    style: 'multi',
  },
  "ajax": {
    "url": "<?= base_url('project/transmital/') ?>getDokumenTransmital?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) ?>&is_transmital=y&bagian_id=<?= $val_bagian['bagian_id'] ?>&is_hps=n",
    "dataSrc": ""
  },
  "columns": [{
    data: null,
    defaultContent: '',
    orderable: false,
    className: 'select-checkbox',
    checkboxes: {
      'selectRow': true
    }
  }, {
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
        var data = 'IFC';
      } else if (full.pekerjaan_dokumen_status == '9') {
        var data = 'IFC';
      } else if (full.pekerjaan_dokumen_status == '10') {
        var data = 'IFC'
      } else if (full.pekerjaan_dokumen_status == '11') {
        var data = 'IFC';
      } else if (full.pekerjaan_dokumen_status == '12') {
        var data = 'Draft - transmital';
      } else if (full.pekerjaan_dokumen_status == '13') {
        var data = 'Upload - transmital';
      } else if (full.pekerjaan_dokumen_status == '14') {
        var data = 'Approve - transmital';
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
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_file + '~' + full.pekerjaan_dokumen_id + '" title="Download" onclick="fun_download_cangun(this.id,this.name)"><i class="fa fa-download"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_nama + '" title="History" onclick="fun_history(this.id,this.name,`' + full.id_pekerjaan_template + '`,`' + full.is_hps + '`,`' + full.id_dokumen_awal + '`,`16`)"><i class="fa fa-history"></i></a></center>';
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
<?php endforeach; ?>

<?php foreach ($dokumen_bagian as $key => $val_bagian) : ?> $('#table_dokumen_kontraktor_<?= $val_bagian['bagian_id'] ?> thead tr').clone(true).addClass('filters_table_dokumen_kontraktor_<?= $val_bagian['bagian_id'] ?>').appendTo('#table_dokumen_kontraktor_<?= $val_bagian['bagian_id'] ?> thead');
$('#table_dokumen_kontraktor_<?= $val_bagian['bagian_id'] ?>').DataTable({
  orderCellsTop: true,
  initComplete: function() {
    $("#table_dokumen_kontraktor_<?= $val_bagian['bagian_id'] ?>").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
    var api = this.api();
    api.columns().eq(0).each(function(colIdx) {
      var cell = $('.filters_table_dokumen_kontraktor_<?= $val_bagian['bagian_id'] ?> th').eq(
        $(api.column(colIdx).header()).index()
        );
      var title = $(cell).text();
      $(cell).html('<input type="text" class="form-control" style="width:100%" placeholder="' + title + '" />');

      $('input', $('.filters_table_dokumen_kontraktor_<?= $val_bagian['bagian_id'] ?> th').eq($(api.column(colIdx).header()).index())).off('keyup change').on('keyup change', function(e) {
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
  select: {
    style: 'multi',
  },
  "ajax": {
    "url": "<?= base_url('project/transmital/') ?>getDokumenKontraktor?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) ?>&id_bagian=<?= $val_bagian['bagian_id'] ?>",
    "dataSrc": ""
  },
  "columns": [{
    data: null,
    defaultContent: '',
    orderable: false,
    className: 'select-checkbox',
    checkboxes: {
      'selectRow': true
    }
  }, {
    render: function(data, type, full, meta) {
      return meta.row + meta.settings._iDisplayStart + 1;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_dokumen_nomor + ' - ' + full.pekerjaan_dokumen_nama;
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
      if (full.pekerjaan_dokumen_status === '0')
        data = 'Revisi';
      else if (full.pekerjaan_dokumen_status === '1')
        data = 'Draft';
      else if (full.pekerjaan_dokumen_status === '2')
        data = 'Menunggu Approve AVP';
      else if (full.pekerjaan_dokumen_status === '3')
        data = 'Approve AVP';
      else if (full.pekerjaan_dokumen_status_doc == 'y')
        data = 'Reviewed Perencana Cangun - <span style="color:green">Without Note</span>';
      else if (full.pekerjaan_dokumen_status_doc == 'yc')
        data = 'Reviewed Perencana Cangun - <span style="color:red">With Note</span>';
      else if (full.pekerjaan_dokumen_status_doc == 'nc')
        data = 'Reviewed Perencana Cangun - <span style="color:red">With Note</span>';
      else if (full.pekerjaan_dokumen_status_doc == 'n')
        data = 'Reviewed Perencana Cangun - <span style="color:red">With Note</span>';
                // else if (full.pekerjaan_dokumen_status === '4' && full.is_change === 'y')
                //   data = 'Reviewed Perencana Cangun - <span style="color:red">With Note</span>';
                // else if (full.pekerjaan_dokumen_status === '4')
                //   data = 'Reviewed Perencana Cangun - <span style="color:green">Without Note</span>';
                // else if (full.pekerjaan_dokumen_status === '5' && full.is_change === 'y')
                //   data = 'Reviewed AVP Cangun - <span style="color:red">With Note</span>';
                // else if (full.pekerjaan_dokumen_status === '5')
                //   data = 'Reviewed AVP Cangun - <span style="color:green">Without Note</span>';
                // else if (full.pekerjaan_dokumen_status === '6' && full.is_change === 'y')
                //   data = 'Approve VP Cangun - <span style="color:red">With Note</span>';
                // else if (full.pekerjaan_dokumen_status === '6')
                //   data = 'Approve VP Cangun - <span style="color:green">Without Note</span>';
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
    // DKkk
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_file + '" name="' + full.pekerjaan_dokumen_id + '" title="Lihat" onclick="fun_lihat(this.id, this.name, 1)"><i class="bx bx-book m-0"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_file + '~' + full.pekerjaan_dokumen_id + '" title="Download" onclick="fun_download_transmital(this.id,this.name)"><i class="fa fa-download"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_nama + '" title="History" onclick="fun_history_transmital(this.id,this.name,`' + full.id_pekerjaan_template + '`,`' + full.is_hps + '`,`' + full.id_dokumen_awal + '`,`16`)"><i class="fa fa-history"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      var aksi = '';
                /* aksi dokumen avp */
      if (full.avp_pic == 'y' && full.pekerjaan_dokumen_status == '2' && full.is_proses == 'wa')
        aksi = '<center><a href="javascript:void(0);" onclick="fun_aksi_baru(`' + full.pekerjaan_dokumen_id + '`,`waspro_avp`)"><i class="fa fa-share"></i></a></center>';
                /* aksi dokumen cangun perencana */
      else if(full.perencana_cangun=='y'){
        if (full.pekerjaan_dokumen_status == '3' && full.is_proses == 'wc' && full.bagian_id == '<?= ($data_bagian['bagian_id']) ?? '' ?>')
          aksi = '<center><a href="javascript:void(0);" onclick="fun_aksi_baru(`' + full.pekerjaan_dokumen_id + '`,`waspro_cangun`)"><i class="fa fa-share"></i></a></center>';
      }
                /* aksi dokumen cangun avp */
      else if (full.pekerjaan_dokumen_status == '4' && full.is_proses == 'wca' && full.bagian_id == '<?= ($data_bagian['bagian_id']) ?? '' ?>' && full.avp_cangun == 'y')
        aksi = '<center><a href="javascript:void(0);" onclick="fun_aksi_baru(`' + full.pekerjaan_dokumen_id + '`,`waspro_cangun_avp`)"><i class="fa fa-share"></i></a></center>';
      else if (full.pekerjaan_dokumen_status == '5' && full.is_proses == 'wcv' && full.vp_cangun == 'y')
        aksi = '<center><a href="javascript:void(0);" onclick="fun_aksi_baru(`' + full.pekerjaan_dokumen_id + '`,`waspro_cangun_vp`)"><i class="fa fa-share"></i></a></center>';
      else
        aksi = '<center>-</center>'

      return aksi;
    }
  },
  ]
});
<?php endforeach; ?>


<?php foreach ($dokumen_bagian as $key => $val_bagian) : ?> $('#table_dokumen_cangun_<?= $val_bagian['bagian_id'] ?> thead tr').clone(true).addClass('filters_table_dokumen_cangun_<?= $val_bagian['bagian_id'] ?>').appendTo('#table_dokumen_cangun_<?= $val_bagian['bagian_id'] ?> thead');
$('#table_dokumen_cangun_<?= $val_bagian['bagian_id'] ?>').DataTable({
  orderCellsTop: true,
  initComplete: function() {
    $("#table_dokumen_cangun_<?= $val_bagian['bagian_id'] ?>").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
    var api = this.api();
    api.columns().eq(0).each(function(colIdx) {
      var cell = $('.filters_table_dokumen_cangun_<?= $val_bagian['bagian_id'] ?> th').eq(
        $(api.column(colIdx).header()).index()
        );
      var title = $(cell).text();
      $(cell).html('<input type="text" class="form-control" style="width:100%" placeholder="' + title + '" />');

      $('input', $('.filters_table_dokumen_cangun_<?= $val_bagian['bagian_id'] ?> th').eq($(api.column(colIdx).header()).index())).off('keyup change').on('keyup change', function(e) {
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
  select: {
    style: 'multi',
  },
  "ajax": {
    "url": "<?= base_url('project/transmital/') ?>getDokumenCangun?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) ?>&id_bagian=<?= $val_bagian['bagian_id'] ?>",
    "dataSrc": ""
  },
  "columns": [{
    data: null,
    defaultContent: '',
    orderable: false,
    className: 'select-checkbox',
    checkboxes: {
      'selectRow': true
    }
  }, {
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
        var data = 'IFC';
      } else if (full.pekerjaan_dokumen_status == '9') {
        var data = 'IFC';
      } else if (full.pekerjaan_dokumen_status == '10') {
        var data = 'IFC'
      } else if (full.pekerjaan_dokumen_status == '11') {
        var data = 'IFC';
      } else if (full.pekerjaan_dokumen_status == '12') {
        var data = 'Draft - transmital';
      } else if (full.pekerjaan_dokumen_status == '13') {
        var data = 'Upload - transmital';
      } else if (full.pekerjaan_dokumen_status == '14') {
        var data = 'Approve - transmital';
      } else if (full.pekerjaan_dokumen_status == '15') {
        var data = 'Review Perencana - Transmital';
      } else if (full.pekerjaan_dokumen_status == '16') {
        var data = 'Review Cangun - Transmital';
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
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_file + '~' + full.pekerjaan_dokumen_id + '" title="Download" onclick="fun_download_cangun(this.id,this.name)"><i class="fa fa-download"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pekerjaan_dokumen_file === null || full.pekerjaan_dokumen_file === '') {
        return '<center>-</center>';
      } else {
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_nama + '" title="History" onclick="fun_history(this.id,this.name,`' + full.id_pekerjaan_template + '`,`' + full.is_hps + '`,`' + full.id_dokumen_awal + '`,`16`)"><i class="fa fa-history"></i></a></center>';
      }
    }
  },
  {
    "render": function(data, type, full, meta) {
      var aksi = '';

      if (full.is_proses == 'dt' && full.pekerjaan_dokumen_status == '13') {
        aksi = '<center><a href="javascript:void(0);" onclick="fun_aksi(`' + full.pekerjaan_dokumen_id + '`)"><i class="fa fa-share"></i></a></center>';
      } else {
        aksi = '<center> - </center>';
      }
      return aksi;
    }
  },
  ]
});
<?php endforeach; ?>

      /*Table History Dokumen*/
$('#table_dokumen_history').DataTable({
  "ajax": {
    "url": "<?= base_url('project/pekerjaan_usulan/') ?>getAsetDocumentHistory?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) ?>&pekerjaan_dokumen_nama=0&id_pekerjaan_template=0&pekerjaan_dokumen_status=0",
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
      } else if (full.pekerjaan_dokumen_status == '12') {
        var data = 'Draft - transmital';
      } else if (full.pekerjaan_dokumen_status == '13') {
        var data = 'Upload - transmital';
      } else if (full.pekerjaan_dokumen_status == '14') {
        var data = 'Approve - transmital';
      } else if (full.pekerjaan_dokumen_status == '15') {
        var data = 'Review Perencana - transmital';
      } else if (full.pekerjaan_dokumen_status == '16') {
        var data = 'Review Cangun - transmital';
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
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_file + '~' + full.pekerjaan_dokumen_id + '" title="Download" onclick="fun_download_cangun(this.id,this.name)"><i class="fa fa-download"></i></a></center>';
      }
    }
  },
  ]
});


$('#table_dokumen_history_transmital').DataTable({
  "ajax": {
    "url": "<?= base_url('project/transmital/') ?>getDokumenHistory?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) ?>&pekerjaan_dokumen_nama=0&id_pekerjaan_template=0&pekerjaan_dokumen_status=0",
    "dataSrc": ""
  },
  "columns": [{
    render: function(data, type, full, meta) {
      return meta.row + meta.settings._iDisplayStart + 1;
    }
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_dokumen_nomor + ' - ' + full.pekerjaan_dokumen_nama;
    }
  },
  {
    render: function(data, type, full, meta) {
      var data = '';
      if (full.pekerjaan_dokumen_status === '0')
        data = 'Revisi';
      else if (full.pekerjaan_dokumen_status === '1')
        data = 'Draft';
      else if (full.pekerjaan_dokumen_status === '2')
        data = 'Menunggu Approve AVP';
      else if (full.pekerjaan_dokumen_status === '3')
        data = 'Approve AVP';
      else if (full.pekerjaan_dokumen_status === '4' && full.is_change === 'y')
        data = 'Reviewed Perencana Cangun - <span style="color:red">With Note</span>';
      else if (full.pekerjaan_dokumen_status === '4')
        data = 'Reviewed Perencana Cangun - <span style="color:green">Without Note</span>';
      else if (full.pekerjaan_dokumen_status === '5' && full.is_change === 'y')
        data = 'Reviewed AVP Cangun - <span style="color:red">With Note</span>';
      else if (full.pekerjaan_dokumen_status === '5')
        data = 'Reviewed AVP Cangun - <span style="color:green">Without Note</span>';
      else if (full.pekerjaan_dokumen_status === '6' && full.is_change === 'y')
        data = 'Approve VP Cangun - <span style="color:red">With Note</span>';
      else if (full.pekerjaan_dokumen_status == '6')
        data = 'Approve VP Cangun - <span style="color:green">Without Note</span>';
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
        return '<center><a href="javascript:;" id="' + full.id_pekerjaan + '" name= "' + full.pekerjaan_dokumen_file + '~' + full.pekerjaan_dokumen_id + '" title="Download" onclick="fun_download_transmital(this.id,this.name)"><i class="fa fa-download"></i></a></center>';
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
    "url": "<?= base_url('project/pekerjaan_usulan/') ?>getHistory?id_pekerjaan=<?php echo preg_replace("/[^0-9^a-z^A-Z]/", "", $this->input->get('pekerjaan_id')) ?>",
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
$('.select2-selection').css({
  height: 'auto',
  margin: '0px -10px 0px -10px'
});
$('.select2').css('width', '100%');
});
    /* SELECT2 */

    /* KLIK */

    /* EASYUI */
    /* DG DOKUMEN KONTRAKTOR */
    /*upload dokumen kontraktor*/
function fun_upload_dokumen_kontraktor(pekerjaan, bagian) {
  $('#bagian_id').val(bagian);
  $('#modal_upload_dokumen_kontraktor').modal('show');

  setTimeout(function() {
    $('#dg_dokumen_kontraktor').edatagrid({
      url: '<?= base_url('project/transmital/getDokumenKontraktor?id_pekerjaan=') ?>' + pekerjaan + '&id_bagian=' + bagian,
      saveUrl: '<?= base_url('project/transmital/aksiDokumenKontraktor?id_pekerjaan=') ?>' + pekerjaan + '&id_bagian=' + bagian + '&opsi=baru',
      updateUrl: '<?= base_url('project/transmital/aksiDokumenKontraktor?id_pekerjaan=') ?>' + pekerjaan + '&id_bagian=' + bagian + '&opsi=edit',
      rowStyler: function(index, row) {
        if (row.pekerjaan_dokumen_status == '0')
          return 'background-color:#FF0000;font-weight:bold;'
        else if (row.is_change == 'y')
          return 'background-color:#FAA300;font-weight:bold;'
      },
      onBeginEdit: function(index, row) {
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
            type: 'datebox',
          },
        },
        {
          field: 'pekerjaan_dokumen_nama',
          title: 'Nama Dokumen',
          width: '20%',
          editor: {
            type: 'textbox',
            options: {
              onchange: function(value) {
                $("#doc_nama_kontraktor").val(value);
              }
            }
          },
        },
        {
          field: 'pekerjaan_dokumen_nomor',
          title: 'No Dokumen',
          width: '20%',
          editor: {
            type: 'textbox',
            options: {
              required: false,
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
              required: false,
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
                var nama = $("#doc_nama_kontraktor").val();
                self.filebox('setText', 'Menyimpan...');
                formData.append('id_pekerjaan', $('#pekerjaan_id').val());
                for (var i = 0; i < files.length; i++) {
                  var file = files[i];
                  formData.append('file', file, file.name);
                }
                $.ajax({
                  url: '<?= base_url('project/transmital/aksiFileDokumenTransmital') ?>',
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
              return '<a href="#" onclick="fun_lihat_file(\'' + row.pekerjaan_dokumen_file + '\')">Lihat File</a>';
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
    /*upload dokumen kontraktor*/

    /* tambah dokumen_kontraktor */
function fun_tambah_dokumen_kontraktor() {
  var isi_awal = $('#dg_dokumen_kontraktor').data('datagrid').data.rows[0];
  if ($('#dg_dokumen_kontraktor').data('datagrid').data.total > 0 && 'isNewRecord' in isi_awal) {
    $.messager.alert('Peringatan', 'Data Dokumen Masih Ada Yang Belum Tersimpan');
  } else {
    var id = '<?= $this->input->get('pekerjaan_id') ?>';
    $('#dg_dokumen_kontraktor').edatagrid('addRow', {
      index: 0,
      row: {
        pekerjaan_id: id,
        is_hps: 'n',
      }
    });
  }
}
    /* tambah dokumen_kontraktor */

    /* simpan dokumen kontraktor */
function fun_simpan_dokumen_kontraktor() {
  $('#dg_dokumen_kontraktor').edatagrid('saveRow');
  setTimeout(() => {
    $('#dg_dokumen_kontraktor').datagrid('reload');
  }, 1000);
}
    /* simpan dokumen kontraktor */

    /* Hapus Dokumen kontraktor */
function fun_hapus_dokumen_kontraktor() {
  var row = $('#dg_dokumen_kontraktor').datagrid('getSelected');
  $.post('<?= base_url('/project/transmital/aksiDokumenKontraktor?opsi=hapus') ?>', {
    pekerjaan_dokumen_id: row.pekerjaan_dokumen_id,
    id_pekerjaan: row.id_pekerjaan
  }, function(data, textStatus, xhr) {
    $('#dg_dokumen_kontraktor').datagrid('reload');
  });
}
    /* Hapus Dokumen kontraktor */

    /* liat dokumen */
function fun_lihat_file(fileName) {
  window.open('<?= base_url('document') ?>/' + fileName, '_blank');
}
    /* liat dokumen */

    /* DG DOKUMEN KONTRAKTOR */
    /* EASYUI */

    /* Klik Aksi Dokumen Pekerjaan */
function fun_aksi(id) {
  window.location.href = '<?= base_url('project/pekerjaan_usulan/dokumenAksi?pekerjaan_dokumen_id=') ?>' + id + '&aksi=transmital';
}

function fun_aksi_cangun(id) {
  window.location.href = '<?= base_url('project/pekerjaan_usulan/dokumenAksi?pekerjaan_dokumen_id=') ?>' + id + '&aksi=transmital_cangun';
}

function fun_aksi_cangun_avp(id) {
  window.location.href = '<?= base_url('project/pekerjaan_usulan/dokumenAksi?pekerjaan_dokumen_id=') ?>' + id + '&aksi=transmital_cangun_avp';
}

function fun_aksi_baru(id, aksi) {
  window.location.href = '<?= base_url('project/pekerjaan_usulan/dokumenAksi?pekerjaan_dokumen_id=') ?>' + id + '&aksi=' + aksi + '&transmital=y';
}
    /* Klik Aksi Dokumen Pekerjaan */


    /* Send IFA */
$('#form_upload_dokumen').on('submit', function(e) {
  e.preventDefault();
  var isi_awal = $('#dg_dokumen_kontraktor').data('datagrid').data.rows[0];
  if ($('#dg_dokumen_kontraktor').data('datagrid').data.total > 0 && 'isNewRecord' in isi_awal) {
    $.messager.alert('Peringatan', 'Data Dokumen Masih Ada Yang Belum Tersimpan');
  } else {
    $('#close_dokumen').click();
  }
});
    /* Send IFA */

    /* CLOSE */

    /*close modal disposisi*/
$('#modal_disposisi').on('hidden.bs.modal', function(e) {
  fun_close_disposisi();
});

function fun_close_disposisi() {
  $('#modal_disposisi').modal('hide');
  $('#form_modal_disposisi')[0].reset();
  $('.pic_bagian').empty();
}
    /*close modal disposisi*/

    /* close upload dokumen kontraktor */
function fun_close_dokumen_kontraktor() {
  $('#modal_upload_dokumen_kontraktor').modal('hide');
  $('#table_dokumen_kontraktor_' + $('#bagian_id').val()).DataTable().ajax.reload();
  $('#bagian_id').val('');
}
    /* close upload dokumen kontraktor */

    /*Close History*/
$('#modal_history').on('hidden.bs.modal', function(e) {
  fun_close_history();
});

$('#modal_history_transmital').on('hidden.bs.modal', function(e) {
  fun_close_history();
});

function fun_close_history() {
  $('#modal_history').modal('hide');
  $('#modal_history_transmital').modal('hide');
}
    /*Close History*/

    /* CLOSE */

    /* Fun Lihat Dokumen Pekerjaan */
function fun_lihat(file, id, is_transmital = 'n') {
  // console.log(file + ' xx ' +id)
  // window.open('<?= base_url('document/') ?>' + file);
  $.getJSON('<?= base_url('project/pekerjaan_usulan/lihatDokumen') ?>', {
    id: id,
    is_transmital: is_transmital
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

function fun_download_cangun(isi, name) {
  window.open('<?= base_url('project/pekerjaan_usulan/downloadDokumen') ?>?pekerjaan_id=' + isi + '&pekerjaan_dokumen_file=' + name);
}

function fun_download_cangun(isi, name) {
  window.open('<?= base_url('project/transmital/downloadDokumenCangun') ?>?pekerjaan_id=' + isi + '&pekerjaan_dokumen_file=' + name);
}

function fun_download_transmital(isi, name) {
  window.open('<?= base_url('project/transmital/downloadDokumen') ?>?pekerjaan_id=' + isi + '&pekerjaan_dokumen_file=' + name);
}



function fun_history(id_pekerjaan, pekerjaan_dokumen_nama, id_pekerjaan_template, is_hps, id_dokumen_awal, status) {
  $('#modal_history').modal('show');
  $('#table_dokumen_history').DataTable().ajax.url('<?= base_url('project/pekerjaan_usulan/getAsetDocumentHistory') ?>?id_pekerjaan=' + id_pekerjaan + '&pekerjaan_dokumen_nama=' + pekerjaan_dokumen_nama + '&id_pekerjaan_template=' + id_pekerjaan_template + '&is_hps=' + is_hps + '&id_dokumen_awal=' + id_dokumen_awal + '&pekerjaan_dokumen_status=' + status).load();
}

function fun_history_transmital(id_pekerjaan, pekerjaan_dokumen_nama, id_pekerjaan_template, is_hps, id_dokumen_awal, status) {
  $('#modal_history_transmital').modal('show');
  $('#table_dokumen_history_transmital').DataTable().ajax.url('<?= base_url('project/transmital/getDokumenHistory') ?>?id_pekerjaan=' + id_pekerjaan + '&id_dokumen_awal=' + id_dokumen_awal).load();
}

function fun_draft_dokumen_kontraktor() {
  var isi_awal = $('#dg_dokumen_kontraktor').data('datagrid').data.rows[0];
  if ($('#dg_dokumen_kontraktor').data('datagrid').data.total > 0 && 'isNewRecord' in isi_awal) {
    $.messager.alert('Peringatan', 'Data Dokumen Masih Ada Yang Belum Tersimpan');
  } else {
    fun_close_dokumen_kontraktor();
  }
}


    /* script waspro */
    /*tombol disposisi*/
function fun_disposisi(id, text) {
  $('#pekerjaan_id_disposisi').val(id);
  $('#modal_disposisi').modal('show');
}
    /*tombol disposisi*/

    /*tombol proses disposisi*/
function fun_proses_disposisi() {
  Swal.fire({
    title: 'Pastikan Data Sudah Terisi Dengan Benar',
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya"
  }).then(function(result) {
    if (result.value) {
      var data = new FormData($('#form_modal_disposisi')[0]);
      data.append('pekerjaan_id', $('#pekerjaan_id_disposisi').val());
      $.ajax({
        url: '<?= base_url('project/transmital/insertDisposisi') ?>',
        type: 'POST',
        dataType: 'HTML',
        data: data,
        contentType: false,
        processData: false,
        cache: false,
        success: function() {
          setTimeout(() => {
            window.location.replace('<?= base_url('project/transmital') ?>');
          }, 1000);
        }
      })
    }
  });
}
    /*tombol proses disposisi*/

    /* tobol pic bagian send avp */
function fun_send(id, text) {
  Swal.fire({
    title: text,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya"
  }).then(function(result) {
    if (result.value) {
      $.post('<?= base_url('project/transmital/insertSend') ?>', {
        pekerjaan_id: id,
        pekerjaan_status: $('#pekerjaan_status').val()
      }, function(json) {});
      setTimeout(() => {
        window.location.replace('<?= base_url('project/transmital') ?>');
      }, 1000);
    }
  });
}
    /* tobol pic bagian send avp */

function fun_send_cangun(id, text) {
  Swal.fire({
    title: text,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya"
  }).then(function(result) {
    if (result.value) {
      $.post('<?= base_url('project/transmital/insertSendCangun') ?>', {
        pekerjaan_id: id,
        pekerjaan_status: $('#pekerjaan_status').val()
      }, function(json) {});
      setTimeout(() => {
        window.location.replace('<?= base_url('project/transmital') ?>');
      }, 1000);
    }
  });
}

function fun_reject(id, text) {
  Swal.fire({
    title: text,
    icon: "warning",
        // html: `<input type="text" id="note_reject" class="swal2-input" placeholder="Note">`,
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya",
        // preConfirm: () => {
        //   const note_reject = Swal.getPopup().querySelector('#note_reject').value
        //   if (!note_reject) {
        //     Swal.showValidationMessage(`Note Tidak Boleh Kosong`)
        //   }
        //   return {
        //     note_reject: note_reject
        //   }
        // }
  }).then(function(result) {
    if (result.value) {
          // var note_reject = result.value.note_reject;
      $.post('<?= base_url('project/transmital/insertReject') ?>', {
        pekerjaan_id: id,
        pekerjaan_status: $('#pekerjaan_status').val(),
            // note_reject: note_reject,
      }, function(json) {});
      setTimeout(() => {
        window.location.replace('<?= base_url('project/transmital') ?>');
      }, 1000);
    }
  });
}

function fun_reviewed(id, text) {
  Swal.fire({
    title: text,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya"
  }).then(function(result) {
    if (result.value) {
      $.post('<?= base_url('project/transmital/insertReviewed') ?>', {
        pekerjaan_id: id,
        pekerjaan_status: $('#pekerjaan_status').val()
      }, function(json) {});
      setTimeout(() => {
        window.location.replace('<?= base_url('project/transmital') ?>');
      }, 1000);
    }
  });
}

function fun_reviewed_cangun(id, text) {
  Swal.fire({
    title: text,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya"
  }).then(function(result) {
    if (result.value) {
      $.post('<?= base_url('project/transmital/insertReviewedCangun') ?>', {
        pekerjaan_id: id,
        pekerjaan_status: $('#pekerjaan_status').val()
      }, function(json) {});
      setTimeout(() => {
        window.location.replace('<?= base_url('project/transmital') ?>');
      }, 1000);
    }
  });
}

function fun_approved_cangun(id, text) {
  Swal.fire({
    title: text,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya"
  }).then(function(result) {
    if (result.value) {
      $.post('<?= base_url('project/transmital/insertApprovedCangun') ?>', {
        pekerjaan_id: id,
        pekerjaan_status: $('#pekerjaan_status').val()
      }, function(json) {});
      setTimeout(() => {
        window.location.replace('<?= base_url('project/transmital') ?>');
      }, 1000);
    }
  });
}

function fun_reject_cangun(id, text) {
  Swal.fire({
    title: text,
    icon: "warning",
    html: `<input type="text" id="note_reject" class="swal2-input" placeholder="Note">`,
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya",
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
      $.post('<?= base_url('project/transmital/insertRejectCangun') ?>', {
        pekerjaan_id: id,
        pekerjaan_status: $('#pekerjaan_status').val(),
        note_reject: note_reject,
      }, function(json) {});
      setTimeout(() => {
        window.location.replace('<?= base_url('project/transmital') ?>');
      }, 1000);
    }
  });
}

function fun_kembali_upload(id, text) {
  Swal.fire({
    title: text,
    html: `<input type="text" id="note_reject" class="swal2-input" placeholder="Note">`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya",
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
      $.post('<?= base_url('project/transmital/insertKembaliUpload') ?>', {
        pekerjaan_id: id,
        pekerjaan_status: $('#pekerjaan_status').val(),
        note_reject: note_reject
      },
      function(json) {});
      setTimeout(() => {
        window.location.replace('<?= base_url('project/transmital') ?>');
      }, 1000);
    }
  });
}

function fun_close_transmital(id, text) {
  Swal.fire({
    title: text,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Iya"
  }).then(function(result) {
    if (result.value) {
      $.post('<?= base_url('project/transmital/insertClose') ?>', {
        pekerjaan_id: id,
        pekerjaan_status: '6'
      }, function(json) {});
      setTimeout(() => {
        window.location.replace('<?= base_url('project/transmital') ?>');
      }, 1000);
    }
  });
}
    /* script waspro */
</script>