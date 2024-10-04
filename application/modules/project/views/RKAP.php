<link rel="stylesheet" type="text/css" href="<?= base_url('assets_tambahan/') ?>easyui/themes/default/easyui.css">
<link rel="stylesheet" type="text/css" href="<?= base_url('assets_tambahan/') ?>easyui/themes/icon.css">
<style>
  .dataTables_scrollHeadInner,
  .table {
    width: 100% !important;
  }

  .select2-selection__clear {
    float: left !important;
  }
</style>
<div class="page-content">
  <?php
  /*Session*/
  $session = $this->session->userdata();
  /*SQL Atasan*/
  $dataAtasan = $this->db->query("SELECT * FROM global.global_pegawai a LEFT JOIN global.global_pegawai_pgs b ON a.pegawai_nik = b.pegawai_pgs_pemberi_tugas_nik AND NOW() BETWEEN pegawai_pgs_awal_cuti AND pegawai_pgs_akhir_cuti WHERE pegawai_poscode = '" . $session['pegawai_direct_superior'] . "'")->row_array();

  /*SQL Tahun*/
  $dataTahun = $this->db->query("SELECT pekerjaan_tahun as tahun FROM dec.dec_pekerjaan WHERE pekerjaan_tahun IS NOT NULL GROUP BY pekerjaan_tahun ORDER BY pekerjaan_tahun ASC")->result_array();
  $dataKlasifikasi = $this->db->query("SELECT * FROM global.global_klasifikasi_pekerjaan WHERE klasifikasi_pekerjaan_rkap = 'y' ORDER BY CAST(klasifikasi_pekerjaan_urut AS INT) ASC ")->result_array();

  /*Admin Sistem*/
  $admin_sistem = '';
  $admin_sistem = $this->db->query("SELECT * FROM global.global_admin WHERE admin_nik = '" . $session['pegawai_nik'] . "'")->row_array();
  if (!empty($admin_sistem)) {
    $admin_sistemnya = $admin_sistem['admin_nik'];
  } else {
    $admin_sistemnya = '0';
  }
  /*SQL Jumlah Pekerjaan Belum Selesai Per Departemen*/
  $dataDep = $this->db->query("SELECT pegawai_id_dep FROM global.global_pegawai WHERE pegawai_nik = '" . $session['pegawai_nik'] . "'")->row_array();
  $dataLoadPekerjaan = $this->db->query("SELECT count(pekerjaan_status) FROM dec.dec_pekerjaan b LEFT JOIN global.global_pegawai c ON c.pegawai_nik = b.pic WHERE (pekerjaan_status < '14' OR pekerjaan_status!= '-') AND pic = '" . $session['pegawai_nik'] . "' AND id_klasifikasi_pekerjaan NOT IN('1')")->row_array();
  ?>

  <div class="container-fluid">
    <!-- start page title -->
    <div class="row">
      <div class="col-lg-12">
        <!-- <div class="page-title-box d-sm-flex align-items-center justify-content-between"> -->
          <div class="card"> 
            <div class="card-body">
              <h4 class="card-title mb-2">Pekerjaan RKAP</h4>
            </div>
          </div>
          <!-- </div> -->
        </div>
      </div>
      <!-- end page title -->
      <!-- Start Filter Pekerjaan -->
      <div class="row" id="div_filter" style="display:block">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title mb-4">Filter Pekerjaan</h4>
              <form id="filter">
                <div class="row">
                  <div class="form-group col-md-5">
                    <label>Tahun Pekerjaan</label>
                    <select class="form-control" id="tahun" name="tahun">
                      <option value="">- Semua -</option>
                      <?php foreach ($dataTahun as $value) : ?>
                        <option value="<?php echo $value['tahun'] ?>"><?php echo $value['tahun'] ?></option>
                      <?php endforeach ?>
                    </select>
                  </div>
                  <div class="form-group col-md-5" style="display: none;" id="div_perencana">
                    <label>Perencana</label>
                    <select class="form-control select2" id="id_user_cari" name="id_user_cari">
                    </select>
                  </div>
                  <div class="form-group col-md-2">
                    <label>&emsp;</label>
                    <button class="btn btn-primary form-control" type="button" name="cari_filter" id="cari_filter">Cari</button>
                    <button class="btn btn-primary form-control" type="button" name="cari_berjalan" id="cari_berjalan" style="display:none">Cari</button>
                    <button class="btn btn-primary form-control" type="button" name="cari_ifa" id="cari_ifa" style="display:none">Cari</button>
                    <button class="btn btn-primary form-control" type="button" name="cari_ifc" id="cari_ifc" style="display:none">Cari</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- End Filter Pekerjaan -->

      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <!--Start Tab -->
            <div class="card-body">
              <ul class="nav nav-tabs">
                <input type="text" id="pekerjaan_status_nama" name="pekerjaan_status_nama" value="usulan" hidden="hidden">
                <input type="text" hidden name="session_direct_superior" id="session_direct_superior" value="<?= $session['pegawai_direct_superior'] ?>">
                <input type="text" hidden name="session_poscode" id="session_poscode" value="<?= $session['pegawai_poscode'] ?>">
                <input type="text" name="session_nik" id="session_nik" value="<?= $session['pegawai_nik'] ?>" hidden>
                <input type="text" name="pegawai_jabatan" id="pegawai_jabatan" value="<?php echo substr($session['pegawai_jabatan'], 0, 1) ?>" style="display:none;">
                <input type="text" name="jabatan_atasan" id="jabatan_atasan" value="<?php echo substr($dataAtasan['pegawai_jabatan'], 0, 1) ?>" style="display:none;">
                <input type="text" name="nama_atasan" id="nama_atasan" value="<?php echo $dataAtasan['pegawai_nama'] ?>" style="display:none;">
                <input type="text" name="nik_atasan" id="nik_atasan" value="<?php echo $dataAtasan['pegawai_nik'] ?>" style="display:none;">
                <input type="text" name="postitle_atasan" id="postitle_atasan" value="<?php echo $dataAtasan['pegawai_postitle'] ?>" style="display:none;">
                <input type="text" name="direct_superior_atasan" id="direct_superior_atasan" value="<?php echo $dataAtasan['pegawai_direct_superior'] ?>" style="display:none;">
                <li class="nav-item">
                  <a class="nav-link active bg-secondary bg-gradient" href="#usulan" onclick="div_usulan()" id="link_div_usulan">Usulan <input hidden type="text" id="notif_usulan_reject_rkap"><span class="badge bg-primary fload_end" id="notif_usulan_rkap"></span></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#berjalan" onclick="div_berjalan()" id="link_div_berjalan">Berjalan <span class="badge bg-secondary fload_end" id="notif_berjalan_rkap"></span></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#ifi" onclick="div_ifi()" id="link_div_ifi">IFI <span class="badge bg-success float-end" id="notif_ifi_rkap"></span> </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#ifa" onclick="div_ifa()" id="link_div_ifa">IFA <span class="badge bg-success fload_end" id="notif_ifa_rkap"></a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#ifc" onclick="div_ifc()" id="link_div_ifc">IFC <span class="badge bg-warning float-end" id="notif_ifc_rkap"></span> </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#ifc" onclick="div_ift()" id="link_div_ift">IFT <span class="badge bg-warning fload_end" id="notif_ift_rkap"></span></a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#ifr" onclick="div_ifr()" id="link_div_ifr">IFR <span class="badge bg-warning float-end" id="notif_ifr_rkap"></span> </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#selesai" onclick="div_selesai()" id="link_div_selesai">Selesai <span class="badge bg-dark fload_end" id="notif_selesai_rkap"></span></a>
                  </li>
                </ul>
              </div>
              <!-- End Tab -->
              <!-- start card usulan -->
              <div class="card-body" id="div_usulan">
                <div>
                  <button type="button" class="btn btn-primary float-end" data-toggle="modal" data-target="#myModal" onclick="fun_tambah_usulan()" id="btn_rkap_usulan" style="display:none">Tambah</button>
                  <h4 class="card-title mb-4">Pekerjaan Usulan</h4>
                </div>
                <table id="table_usulan" class="table table-bordered table-striped" width="100%">
                  <thead class="table-primary">
                    <tr>
                      <th style="text-align: center;">No</th>
                      <th style="text-align: center;">No Pekerjaan</th>
                      <th style="text-align: center;">Waktu Pekerjaan</th>
                      <th style="text-align: center;">Nama Pekerjaan</th>
                      <th style="text-align: center;">Departemen</th>
                      <th style="text-align: center;">PIC</th>
                      <th style="text-align: center;">Status</th>
                      <th style="text-align: center;">Aksi</th>
                      <th style="text-align: center;">Edit</th>
                      <th style="text-align: center;">Delete</th>
                    </tr>
                  </thead>
                </table>
              </div>
              <!-- end card usulan -->
              <!-- start card berjalan -->
              <div class="card-body" id="div_berjalan" style="display:none">
                <h4 class="card-title mb-4">Pekerjaan Berjalan</h4>
                <table id="table_berjalan" class="table table-bordered table-striped" style="width: 100% !important;">
                  <thead class="table-primary">
                    <tr>
                      <th style="text-align: center;" rowspan="2">No</th>
                      <th style="text-align: center;" rowspan="2">No Pekerjaan</th>
                      <th style="text-align: center;" rowspan="2">Nama Pekerjaan</th>
                      <th style="text-align: center;" rowspan="2">User/PIC</th>
                      <th style="text-align: center;" rowspan="2">Status</th>
                      <th style="text-align: center;" rowspan="2">Progress</th>
                      <th style="text-align: center;" rowspan="2">Detail</th>
                      <th style="text-align: center;" colspan="5">Perencana</th>
                      <th style="text-align: center;" rowspan="2">Man Power</th>
                      <th style="text-align: center;" rowspan="2">Enginering Start</th>
                      <th style="text-align: center;" rowspan="2">Jadwal Engineering Finish</th>
                    </tr>
                    <tr>
                      <th style="text-align: center;">Proses</th>
                      <th style="text-align: center;">Mekanikal</th>
                      <th style="text-align: center;">Listrik</th>
                      <th style="text-align: center;">Instrument</th>
                      <th style="text-align: center;">Sipil</th>
                    </tr>
                  </thead>
                </table>
              </div>
              <!-- end card berjalan -->
              <!-- start card ifi -->
              <div class="card-body" id="div_ifi" style="display:none">
                <h4 class="card-title mb-4">Dokumen IFI</h4>
                <input type="text" name="user_session_ifi" id="user_session_ifi" hidden>
                <table id="table_ifi" class="table table-bordered table-striped" style="width: 100% !important;">
                  <thead class="table-primary">
                    <tr>
                      <th style="text-align: center;">No Pekerjaan</th>
                      <th style="text-align: center;">Waktu Pekerjaan</th>
                      <th style="text-align: center;">Nama Pekerjaan</th>
                      <th style="text-align: center;">Peminta Jasa</th>
                      <th style="text-align: center;">Status</th>
                      <th style="text-align: center;">Detail</th>
                    </tr>
                  </thead>
                </table>
              </div>
              <!-- end card ifi -->
              <!-- start card ifa -->
              <div class="card-body" id="div_ifa" style="display:none">
                <h4 class="card-title mb-4">Dokumen IFA</h4>
                <input type="text" name="user_session" id="user_session" style="display:none">
                <table id="table_ifa" class="table table-bordered table-striped" width="100%">
                  <thead class="table-primary">
                    <tr>
                      <th style="text-align: center;">No Pekerjaan</th>
                      <th style="text-align: center;">Waktu Pekerjaan</th>
                      <th style="text-align: center;">Batas Waktu Pekerjaan</th>
                      <th style="text-align: center;">Nama Pekerjaan</th>
                      <th style="text-align: center;">Peminta Jasa</th>
                      <th style="text-align: center;">Status</th>
                      <th style="text-align: center;">Detail</th>
                      <th style="text-align: center;">Ajuan Extend</th>
                      <th style="text-align: center;">Extend</th>
                    </tr>
                  </thead>
                </table>
              </div>
              <!-- end card ifa -->
              <!-- start card ifc -->
              <div class="card-body" id="div_ifc" style="display:none">
                <h4 class="card-title mb-4">Dokumen IFC</h4>
                <table id="table_ifc" class="table table-bordered table-striped " style="width: 100% !important;">
                  <thead class="table-primary">
                    <tr>
                      <th>No Pekerjaan</th>
                      <th>Waktu Pengajuan</th>
                      <th>Nama Pekerjaan</th>
                      <th>Status</th>
                      <th>Detail</th>
                    </tr>
                  </thead>
                </table>
              </div>
              <!-- end card ifc -->
              <!-- start card ift -->
              <div class="card-body" id="div_ift" style="display:none">
                <h4 class="card-title mb-4">Dokumen IFT</h4>
                <table id="table_ift" class="table table-bordered table-striped" width="100%">
                  <thead class="table-primary">
                    <tr>
                      <th>No Pekerjaan</th>
                      <th>Waktu Pengajuan</th>
                      <th>Nama Pekerjaan</th>
                      <th>Status</th>
                      <th>Detail</th>
                    </tr>
                  </thead>
                </table>
              </div>
              <!-- end card ift -->
              <!-- start card ifr -->
              <div class="card-body" id="div_ifr" style="display:none">
                <h4 class="card-title mb-4">Dokumen IFR</h4>
                <table id="table_ifr" class="table table-bordered table-striped " style="width: 100% !important;">
                  <thead class="table-primary">
                    <tr>
                      <th>No Pekerjaan</th>
                      <th>Waktu Pengajuan</th>
                      <th>Nama Pekerjaan</th>
                      <th>Status</th>
                      <th>Detail</th>
                    </tr>
                  </thead>
                </table>
              </div>
              <!-- end card ifr -->
              <!-- start card selesai -->
              <div class="card-body" id="div_selesai" style="display:none">
                <h4 class="card-title mb-4">Pekerjaan Selesai</h4>
                <table id="table_selesai" class="table table-bordered table-striped" width="100%">
                  <thead class="table-primary">
                    <tr>
                      <th style="text-align: center;">No</th>
                      <th style="text-align: center;">No Pekerjaan</th>
                      <th style="text-align: center;">Tanggal Selesai</th>
                      <th style="text-align: center;">Nama Pekerjaan</th>
                      <th style="text-align: center;">Departemen</th>
                      <th style="text-align: center;">PIC</th>
                      <th style="text-align: center;">Status</th>
                      <th style="text-align: center;">Aksi</th>
                    </tr>
                  </thead>
                </table>
              </div>
              <!-- end card selesai -->
            </div>
          </div>
        </div>
        <!-- end row -->
      </div>
      <!-- container-fluid -->

      <!-- MODAL -->
      <!-- start modal usulan -->
      <div class="modal fade" id="modal_usulan" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Pekerjaan Usulan</h4>
            </div>
            <div class="modal-body">
              <form id="form_modal_usulan" enctype="multipart/form-data">
                <input type="text" name="pekerjaan_id" id="pekerjaan_id" style="display:none;">
                <input type="text" name="jabatan_temp" id="jabatan_temp" style="display:none;">
                <input type="text" name="pekerjaan_status" id="pekerjaan_status" value="0" style="display:none;">
                <div class="card-body row">
                  <div class="form-group row col-md-12">
                    <label class="col-md-4">Tanggal Pengajuan Pekerjaan</label>
                    <div class="input-group col-md-8" id="dt_pekerjaan_waktu">
                      <input type="text" class="form-control col-md-8" placeholder="dd-mm-yyyy" data-date-format="dd-mm-yyyy" data-date-container='#dt_pekerjaan_waktu' data-provide="datepicker" data-date-autoclose="true" id="pekerjaan_waktu" name="pekerjaan_waktu" value="<?= date('d-m-Y') ?>" readonly>
                      <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                    </div>
                    <label style="color:red;display:none" id="pekerjaan_waktu_alert">Waktu Pekerjaan Tidak Boleh Kosong</label>
                  </div>
                </div>
                <div class="card-body row" style="display: none;">
                  <div class="form-group row col-md-12">
                    <label class="col-md-4">Target Selesai Pekerjaan</label>
                    <div class="input-group col-md-8" id="dt_pekerjaan_waktu_akhir">
                      <input type="text" class="form-control col-md-8" placeholder="dd-mm-yyyy" data-date-format="dd-mm-yyyy" data-date-container='#dt_pekerjaan_waktu_akhir' data-provide="datepicker" data-date-autoclose="true" id="pekerjaan_waktu_akhir" name="pekerjaan_waktu_akhir" value="<?= date('d-m-Y') ?>">
                      <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                    </div>
                    <label style="color:red;display:none" id="pekerjaan_waktu_akhir_alert">Target Selesai Pekerjaan Tidak Boleh Kosong</label>
                  </div>
                </div>
                <div class="card-body row">
                  <div class="form-group row col-md-12">
                    <label class="col-md-4">PIC</label>
                    <input type="text" name="pic" id="pic" class="form-control col-md-8" value="<?= $pegawai_nama ?>" readonly style="display:none">
                    <input type="text" name="pic_nama" id="pic_nama" class="form-control" value="<?= $pegawai_nama ?>" readonly>
                    <label style="color:red;display:none" id="pic_alert">PIC Tidak Boleh Kosong</label>
                  </div>
                </div>
                <div class="card-body row" id="div_reviewer">
                  <div class="form-group row col-md-12">
                    <label class="col-md-4">Reviewer</label>
                    <select class="form-control" id="reviewer" name="reviewer">
                      <!-- <option>Pilih Reviewer</option> -->
                    </select>
                    <label style="color:red;display:none" id="pic_alert">PIC Tidak Boleh Kosong</label>
                  </div>
                </div>
                <div class="card-body row" id="div_approver">
                  <div class="form-group row col-md-12">
                    <label class="col-md-4">Approver</label>
                    <select class="form-control" id="approver" name="approver">
                      <!-- <option>Pilih Approver</option> -->
                    </select>
                    <label style="color:red;display:none" id="pic_alert">PIC Tidak Boleh Kosong</label>
                  </div>
                </div>
                <div class="card-body row">
                  <div class="form-group row col-md-12">
                    <label class="col-md-4">Klasifikasi Pekerjaan *</label>
                    <?php $default = 'ddf3c67d5c5ff4f4c978eed6da8fe9d9a27e459a' ?>
                    <select id="id_klasifikasi_pekerjaan" name="id_klasifikasi_pekerjaan" class="form-control">
                      <option value="">- Pilih Klasifikasi Dokumen -</option>
                      <?php foreach ($dataKlasifikasi as $value) : ?>
                        <option <?php if ($value['klasifikasi_pekerjaan_id'] == $default) echo 'selected' ?> value="<?php echo $value['klasifikasi_pekerjaan_id'] ?>"><?php echo $value['klasifikasi_pekerjaan_nama'] ?></option>
                      <?php endforeach ?>
                    </select>
                    <label style="color:red;display:none" id="id_klasifikasi_pekerjaan_alert">Klasifikasi Pekerjaan Tidak Boleh Kosong</label>
                  </div>
                  <div class="card-body row">
                    <div class="form-group row col-md-12">
                      <label class="col-md-4">No Telp *</label>
                      <input type="text" name="pic_no_telp" id="pic_no_telp" class="form-control col-md-8" onkeypress="return numberOnly(event)">
                      <label style="color:red;display:none" id="pic_no_telp_alert">No Telepon Tidak Boleh Kosong</label>
                    </div>
                  </div>
                  <div class="card-body row">
                    <div class="form-group row col-md-12">
                      <label class="col-md-4">Nama Pekerjaan *</label>
                      <input type="text" name="pekerjaan_judul" id="pekerjaan_judul" class="form-control col-md-8">
                      <label style="color:red;display:none" id="pekerjaan_judul_alert">Nama Pekerjaan Tidak Boleh Kosong</label>
                    </div>
                  </div>
                  <div class="card-body row">
                    <div class="form-group row col-md-12">
                      <label class="col-md-4">Tahun Pekerjaan *</label>
                      <!-- <input type="text" name="pekerjaan_tahun" id="pekerjaan_tahun" class="form-control col-md-8" maxlength="4" onkeypress="return numberOnly(event)" value="<?= date('Y') ?>"> -->
                      <select name="pekerjaan_tahun" id="pekerjaan_tahun" class="form-control col-md-8" maxlength="4">
                        <?php for ($year = 2022; $year <= date("Y"); $year++) : ?>
                            <?php $selected = ($year == date("Y")) ? 'selected' : ''; ?>
                            <option value="<?php echo $year; ?>" <?php echo $selected; ?>><?php echo $year; ?></option>
                        <?php endfor; ?>
                      </select>
                      <label style="color:red;display:none" id="pekerjaan_tahun_alert">Tahun Pekerjaan Tidak Boleh Kosong</label>
                    </div>
                  </div>
                  <div class="card-body row" id="div_pekerjaan_note" style="display:none">
                    <div class="form-group row col-md-12">
                      <label class="col-md-4">Alasan Reject</label>
                      <input type="text" name="pekerjaan_note" id="pekerjaan_note" class="form-control col-md-8" readonly>
                    </div>
                  </div>
                  <div class="card-body row">
                    <div class="form-group row col-md-12">
                      <label class="col-md-4">Detail Pekerjaan</label>
                      <textarea name="pekerjaan_deskripsi" id="pekerjaan_deskripsi" class="form-control col-md-8 txtApik"></textarea>
                      <label style="color:red;display:none" id="pekerjaan_deskripsi_alert">Deskripsi Pekerjaan Tidak Boleh Kosong</label>
                    </div>
                  </div>

                  <div class="card-body row">
                    <div class="form-group row col-md-12">
                      <label class="col-md-4">Referensi Unit Kerja Terkait</label>
                      <select name="cc_id[]" id="cc_id" class="form-control select2" multiple></select>
                    </div>
                  </div>

                  <div class="card-body row">
                    <div class="form-group row col-md-12">
                      <label class="col-md-4">Upload Dokumen Pendukung</label>
                      <input type="hidden" name="doc_nama" id="doc_nama">
                      <table id="dg_document" title="Document" style="width:100%" toolbar="#toolbar" pagination="true" idField="id" rownumbers="true" fitColumns="true" singleSelect="true">
                      </table>
                      <div id="toolbar">
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="fun_tambah_document()">New</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="fun_hapus_document()">Delete</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-save" plain="true" onclick="fun_simpan_document()">Save</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-undo" plain="true" onclick="javascript:$('#dg_document').edatagrid('cancelRow')">Cancel</a>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer justify-content-between">
                    <button type="button" id="close" class="btn btn-default" data-dismiss="modal" onclick="fun_close_usulan()">Close</button>
                    <input type="button" class="btn btn-warning pull-right" id="simpan" value="Draft">
                    <input type="button" class="btn btn-success pull-right" id="send" value="Send">
                    <input type="button" class="btn btn-primary pull-right" id="edit" value="Edit" style="display: none;">
                    <button class="btn btn-primary" type="button" id="loading_form" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- end modal usulan -->

        <!-- start modal ajuan extend -->
        <div class="modal fade" id="modal_ajuan_extend">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">Pengajuan Extend Dokumen IFC</h4>
              </div>
              <div class="modal-body">
                <div class="card-body row" id="formDiv">
                  <form id="form_modal_ajuan_extend">
                    <input type="hidden" name="extend_id_ajuan_extend" id="extend_id_ajuan_extend">
                    <input type="hidden" name="id_pekerjaan_ajuan_extend" id="id_pekerjaan_ajuan_extend">
                    <input type="hidden" name="pekerjaan_status_ajuan_extend" id="pekerjaan_status_ajuan_extend">
                    <div class="card-body row">
                      <div class="form-group row col-md-12">
                        <label class="col-md-4">Batas Waktu Pekerjaan</label>
                        <input type="number" name="pekerjaan_waktu_ajuan_extend" id="pekerjaan_waktu_ajuan_extend" class="form-control col-md-8">
                      </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                      <button type="button" id="close_ajuan_extend" class="btn btn-default" data-dismiss="modal" onclick="fun_close_ifa()">Close</button>
                      <input type="submit" class="btn btn-success pull-right" id="simpan_ajuan_extend" value="Simpan">
                      <input type="submit" class="btn btn-primary pull-right" id="edit_ajuan_extend" value="Edit" style="display: none;">
                      <button class="btn btn-primary" type="button" id="loading_form_ajuan_extend" disabled style="display: none;">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading..
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- end modal ajuan extend -->

        <!-- start modal extend -->
        <div class="modal fade" id="modal_extend">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">Extend Dokumen IFC</h4>
              </div>
              <div class="modal-body">
                <div class="card-body row" id="formDiv">
                  <form id="form_modal_extend">
                    <input type="hidden" name="extend_id_extend" id="extend_id_extend">
                    <input type="hidden" name="id_pekerjaan_extend" id="id_pekerjaan_extend">
                    <input type="hidden" name="pekerjaan_status_extend" id="pekerjaan_status_extend">
                    <div class="card-body row" id="dokumen">
                      <div class="form-group row col-md-12">
                        <label class="col-md-4">Batas Waktu Pekerjaan</label>
                        <input type="number" name="pekerjaan_waktu_extend" id="pekerjaan_waktu_extend" class="form-control col-md-8">
                      </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                      <button type="button" id="close_extend" class="btn btn-default" data-dismiss="modal" onclick="fun_close_ifa()">Close</button>
                      <input type="submit" class="btn btn-success pull-right" id="simpan_extend" value="Simpan">
                      <input type="submit" class="btn btn-primary pull-right" id="edit_extend" value="Edit" style="display: none;">
                      <button class="btn btn-primary" type="button" id="loading_form_extend" disabled style="display: none;">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading..
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- end modal extend -->
        <!-- MODAL -->

        <script type="text/javascript" src="<?= base_url('assets_tambahan/') ?>easyui/jquery.easyui.min.js"></script>
        <script type="text/javascript" src="<?= base_url('assets_tambahan/') ?>easyui/jquery.edatagrid.js"></script>

        <script type="text/javascript">

    /* TAB */
    /* Klik Tab usulan */
          function div_usulan() {
            $('#cari_filter').show();
            $('#cari_berjalan').hide();
            $('#cari_ifa').hide();
            $('#cari_ifc').hide();
            $('#div_perencana').hide();
            $('#div_filter').css('display', 'block');
            $('#div_usulan').css('display', 'block');
            $('#div_berjalan').css('display', 'none');
            $('#div_ifa').css('display', 'none');
            $('#div_ift').css('display', 'none');
            $('#div_ifi').css('display', 'none');
            $('#div_ifc').css('display', 'none');
            $('#div_ifr').css('display', 'none');
            $('#div_selesai').css('display', 'none');
            $('#link_div_usulan').addClass('active bg-secondary bg-gradient');
            $('#link_div_berjalan').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifa').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ift').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifi').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifc').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifr').removeClass('active bg-secondary bg-gradient');
            $('#link_div_selesai').removeClass('active bg-secondary bg-gradient');
            $('#pekerjaan_status_nama').val('usulan');
            $('#table_usulan').DataTable().ajax.reload();
          }
    /* Klik Tab usulan */
    /* Klik Tab berjalan */
          function div_berjalan() {
            $('#cari_filter').hide();
            $('#cari_berjalan').show();
            $('#cari_ifa').hide();
            $('#cari_ifc').hide();
            $('#div_perencana').show();
            $('#div_filter').css('display', 'block');
            $('#div_usulan').css('display', 'none');
            $('#div_berjalan').css('display', 'block');
            $('#div_ifa').css('display', 'none');
            $('#div_ift').css('display', 'none');
            $('#div_ifi').css('display', 'none');
            $('#div_ifc').css('display', 'none');
            $('#div_ifr').css('display', 'none');
            $('#div_selesai').css('display', 'none');
            $('#link_div_usulan').removeClass('active bg-secondary bg-gradient');
            $('#link_div_berjalan').addClass('active bg-secondary bg-gradient');
            $('#link_div_ifa').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ift').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifi').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifc').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifr').removeClass('active bg-secondary bg-gradient');
            $('#link_div_selesai').removeClass('active bg-secondary bg-gradient');
            $('#pekerjaan_status_nama').val('berjalan');
            $('#table_berjalan').DataTable().ajax.reload();
          }
    /* Klik Tab berjalan */
    /* Klik Tab IFI */
          function div_ifi() {
            $('#cari_filter').hide();
            $('#cari_berjalan').hide();
            $('#cari_ifa').show();
            $('#cari_ifc').hide();
            $('#div_perencana').show();
            $('#div_filter').css('display', 'block');
            $('#div_usulan').css('display', 'none');
            $('#div_berjalan').css('display', 'none');
            $('#div_berjalan').css('display', 'none');
            $('#div_berjalan').css('display', 'none');
            $('#div_ifi').css('display', 'block');
            $('#div_ifa').css('display', 'none');
            $('#div_ifc').css('display', 'none');
            $('#div_ift').css('display', 'none');
            $('#div_ifr').css('display', 'none');
            $('#div_selesai').css('display', 'none');
            $('#link_div_usulan').removeClass('active bg-secondary bg-gradient');
            $('#link_div_berjalan').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifi').addClass('active bg-secondary bg-gradient');
            $('#link_div_ifa').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifc').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ift').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifr').removeClass('active bg-secondary bg-gradient');
            $('#link_div_selesai').removeClass('active bg-secondary bg-gradient');
            $('#pekerjaan_status_nama').val('ifi');
            $('#table_ifi').DataTable().ajax.reload();

          }
    /* Klik Tab IFI */
    /* Klik Tab IFA */
          function div_ifa() {
            $('#cari_filter').hide();
            $('#cari_berjalan').hide();
            $('#cari_ifa').show();
            $('#cari_ifc').hide();
            $('#div_perencana').show();
            $('#div_filter').css('display', 'block');
            $('#div_usulan').css('display', 'none');
            $('#div_berjalan').css('display', 'none');
            $('#div_berjalan').css('display', 'none');
            $('#div_berjalan').css('display', 'none');
            $('#div_ifi').css('display', 'none');
            $('#div_ifa').css('display', 'block');
            $('#div_ifc').css('display', 'none');
            $('#div_ift').css('display', 'none');
            $('#div_ifr').css('display', 'none');
            $('#div_selesai').css('display', 'none');
            $('#link_div_usulan').removeClass('active bg-secondary bg-gradient');
            $('#link_div_berjalan').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifi').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifa').addClass('active bg-secondary bg-gradient');
            $('#link_div_ifc').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ift').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifr').removeClass('active bg-secondary bg-gradient');
            $('#link_div_selesai').removeClass('active bg-secondary bg-gradient');
            $('#pekerjaan_status_nama').val('ifa');
            $('#table_ifa').DataTable().ajax.reload();
          }
    /* Klik Tab IFA */

    /* Klik Tab IFC */
          function div_ifc() {
            $('#cari_filter').hide();
            $('#cari_berjalan').hide();
            $('#cari_ifa').hide();
            $('#cari_ifc').show();
            $('#div_perencana').show();
            $('#div_filter').css('display', 'block');
            $('#div_usulan').css('display', 'none');
            $('#div_berjalan').css('display', 'none');
            $('#div_ifi').css('display', 'none');
            $('#div_ifa').css('display', 'none');
            $('#div_ifc').css('display', 'block');
            $('#div_ift').css('display', 'none');
            $('#div_ifr').css('display', 'none');
            $('#div_selesai').css('display', 'none');
            $('#link_div_usulan').removeClass('active bg-secondary bg-gradient');
            $('#link_div_berjalan').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifi').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifa').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifc').addClass('active bg-secondary bg-gradient');
            $('#link_div_ift').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifr').removeClass('active bg-secondary bg-gradient');
            $('#link_div_selesai').removeClass('active bg-secondary bg-gradient');
            $('#pekerjaan_status_nama').val('ifc');
            $('#table_ifc').DataTable().ajax.reload();

          }
    /* Klik Tab IFC */

    /* Klik Tab IFt */
          function div_ift() {
            $('#cari_filter').hide();
            $('#cari_berjalan').hide();
            $('#cari_ifa').hide();
            $('#cari_ifc').show();
            $('#div_perencana').show();
            $('#div_filter').css('display', 'block');
            $('#div_usulan').css('display', 'none');
            $('#div_berjalan').css('display', 'none');
            $('#div_ifi').css('display', 'none');
            $('#div_ifa').css('display', 'none');
            $('#div_ifc').css('display', 'none');
            $('#div_ift').css('display', 'block');
            $('#div_ifr').css('display', 'none');
            $('#div_selesai').css('display', 'none');
            $('#link_div_usulan').removeClass('active bg-secondary bg-gradient');
            $('#link_div_berjalan').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifi').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifa').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifc').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ift').addClass('active bg-secondary bg-gradient');
            $('#link_div_ifr').removeClass('active bg-secondary bg-gradient');
            $('#link_div_selesai').removeClass('active bg-secondary bg-gradient');
            $('#pekerjaan_status_nama').val('ift');
            $('#table_ift').DataTable().ajax.reload();
          }
    /* Klik Tab IFt */
    /* Klik Tab IFR */
          function div_ifr() {
            $('#cari_filter').hide();
            $('#cari_berjalan').hide();
            $('#cari_ifa').hide();
            $('#cari_ifc').show();
            $('#div_perencana').show();
            $('#div_filter').css('display', 'block');
            $('#div_usulan').css('display', 'none');
            $('#div_berjalan').css('display', 'none');
            $('#div_ifi').css('display', 'none');
            $('#div_ifa').css('display', 'none');
            $('#div_ifc').css('display', 'none');
            $('#div_ift').css('display', 'none');
            $('#div_ifr').css('display', 'block');
            $('#div_selesai').css('display', 'none');
            $('#link_div_usulan').removeClass('active bg-secondary bg-gradient');
            $('#link_div_berjalan').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifi').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifa').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifc').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ift').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifr').addClass('active bg-secondary bg-gradient');
            $('#link_div_selesai').removeClass('active bg-secondary bg-gradient');
            $('#pekerjaan_status_nama').val('ifr');
            $('#table_ifr').DataTable().ajax.reload();

          }
    /* Klik Tab IFR */

    /* Klik Tab selesai */
          function div_selesai() {
            $('#cari_filter').show();
            $('#cari_berjalan').hide();
            $('#cari_ifa').hide();
            $('#cari_ifc').hide();
            $('#div_perencana').show();
            $('#div_filter').css('display', 'block');
            $('#div_usulan').css('display', 'none');
            $('#div_berjalan').css('display', 'none');
            $('#div_ifa').css('display', 'none');
            $('#div_ift').css('display', 'none');
            $('#div_selesai').css('display', 'block');
            $('#link_div_usulan').removeClass('active bg-secondary bg-gradient');
            $('#link_div_berjalan').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifa').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ift').removeClass('active bg-secondary bg-gradient');
            $('#link_div_ifr').removeClass('active bg-secondary bg-gradient');
            $('#link_div_selesai').addClass('active bg-secondary bg-gradient');
            $('#pekerjaan_status_nama').val('selesai');
            $('#table_selesai').DataTable().ajax.reload();
          }
    /* Klik Tab selesai */
    /* TAB */

    /*select 2*/
      /*list reviewer*/
          $(function() {
            $('#reviewer').select2({
              dropdownParent: $('#modal_usulan'),
              placeholder: 'Pilih',
              ajax: {
                delay: 250,
                url: '<?= base_url('project/pekerjaan_usulan/getListUser') ?>',
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
          /*list reviewer*/

          /*list approver*/
            $('#approver').select2({
              dropdownParent: $('#modal_usulan'),
              placeholder: 'Pilih',
              ajax: {
                delay: 250,
                url: '<?= base_url('project/pekerjaan_usulan/getListUser') ?>',
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
          /*list approver*/

           /*list departemen*/
            $('#dep_id').select2({
              dropdownParent: $('#modal_usulan'),
              placeholder: 'Pilih',
              ajax: {
                delay: 250,
                url: '<?= base_url('project/pekerjaan_usulan/getListDepartemen') ?>',
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
      /*list departemen*/

      /*list cc*/
            $('#cc_id').select2({
              dropdownParent: $('#modal_usulan'),
              placeholder: 'Pilih',
              ajax: {
                delay: 250,
                url: '<?= base_url('project/pekerjaan_usulan/getUserListVPAllDep') ?>',
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
  /*list cc*/

/*list cc hps*/
            $('#cc_hps_id').select2({
              dropdownParent: $('#modal_usulan'),
              placeholder: 'Pilih',
              ajax: {
                delay: 250,
                url: '<?= base_url('project/pekerjaan_langsung/getUserCC') ?>',
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
/*list cc hps*/

            if ($('#pegawai_jabatan').val() == '2') {
              $('#div_reviewer').css('display', 'none');
              $('#div_approver').css('display', 'none');
            } else if ($('#pegawai_jabatan').val() == '3') {
              $('#div_reviewer').css('display', 'none');
              $('#div_approver').css('display', 'block');
        //Approver VP
              var newOption = new Option($('#nama_atasan').val() + ' - ' + $('#postitle_atasan').val(), $('#nik_atasan').val(), true, true);
              $('#approver').append(newOption).trigger('change');
            } else {
              $('#div_reviewer').css('display', 'block');
              $('#div_approver').css('display', 'block');
              if ($('#jabatan_atasan').val() == '2') {
          // Review & Approver direct atasan
                var newOption = new Option($('#nama_atasan').val() + ' - ' + $('#postitle_atasan').val(), $('#nik_atasan').val(), true, true);
                $('#reviewer').append(newOption).trigger('change');
                var newOption2 = new Option($('#nama_atasan').val() + ' - ' + $('#postitle_atasan').val(), $('#nik_atasan').val(), true, true);
                $('#approver').append(newOption2).trigger('change');
              } else {
          // Review direct atasan ; Approve VP
                var newOption = new Option($('#nama_atasan').val() + ' - ' + $('#postitle_atasan').val(), $('#nik_atasan').val(), true, true);
                $('#reviewer').append(newOption).trigger('change');
                $.getJSON('<?= base_url() ?>project/pekerjaan_usulan/getListUserById', {
                  param1: $('#direct_superior_atasan').val()
                }, function(json) {
                  var newOption = new Option(json.text, json.id, true, true);
                  $('#approver').append(newOption).trigger('change');
                });
              }
            }

            fun_loading();
      // START GET SESSION USER
            $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserSession') ?>', function(json, result) {
              $('#user_session').val(json.pegawai_nik);
              if (json.pegawai_id_dep == 'E53000') {
                $('#btn_rkap_usulan').show();
              } else {
                $('#btn_rkap_usulan').hide();
              }
            })
      // END GET SESSION USER
      // START TABLE
      /* Start Isi Table Usulan */
            $('#table_usulan thead tr').clone(true).addClass('filters_usulan').appendTo('#table_usulan thead');
            $('#table_usulan').DataTable({
              orderCellsTop: true,
              initComplete: function() {
                var api = this.api();
                api.columns().eq(0).each(function(colIdx) {
                  var cell = $('.filters_usulan th').eq(
                    $(api.column(colIdx).header()).index()
                    );
                  var title = $(cell).text();
                  $(cell).html('<input type="text" class="form-control" style="width:100%" placeholder="' + title + '" />');

                  $('input', $('.filters_usulan th').eq($(api.column(colIdx).header()).index())).off('keyup change').on('keyup change', function(e) {
                    e.stopPropagation();
                    $(this).attr('title', $(this).val());
                    var regexr = '({search})';
                    var cursorPosition = this.selectionStart;

                    api.column(colIdx).search(this.value != '' ? regexr.replace('{search}', '(((' + this.value + ')))') : '', this.value != '', this.value == '').draw();

                    $(this).focus()[0].setSelectionRange(cursorPosition, cursorPosition);
                  });
                });
              },
              "scrollX": true,
              "ajax": {
                "url": "<?= base_url() ?>project/pekerjaan_usulan/getPekerjaanUsulan?klasifikasi_pekerjaan_id=1&pekerjaan_status=-,0,1,2,3,4",
                "dataSrc": ""
              },
              "columns": [{
                render: function(data, type, full, meta) {
                  var urut = '';
                  if (full.milik == 'y') {
                    urut = '<span class="badge" style="background-color:#c13333 ">' + (
                      meta.row + meta.settings._iDisplayStart + 1) + '</span>'
                  } else {
                    urut = meta.row + meta.settings._iDisplayStart + 1;
                  }
              // return meta.row + meta.settings._iDisplayStart + 1;
                  return urut;
                }
              },
              {
                render: function(data, type, full, meta) {
                  if (full.pekerjaan_nomor != null) {
                    var nomor = full.pekerjaan_nomor.split('-');
                    nomor[0] = pad(nomor[0], 3);
                    return nomor.join('-');
                  } else {
                    return full.pekerjaan_nomor;
                  }
              // var session = '<? echo $this->session->userdata('pegawai_unit_id') ?>';
              // return (session == 'E53000') ? full.pekerjaan_nomor : '-';
                }
              },
              {
                "data": "tanggal_awal"
              },
              {
                render: function(data, type, full, meta) {
                  return full.pekerjaan_judul;
                }
              },
              {
                "data": "pegawai_nama_dep"
              },
              {
                "data": "usr_name"
              },
              {
                "render": function(data, type, full, meta) {
                  var status = '';
                  var warna = '';
                  if (full.pekerjaan_status == 0) {
                    status = 'Draft';
                    warna = '#A0A0A0';
                  } else if (full.pekerjaan_status == 1) {
                    warna = '#FFFF00';
                    status = 'Menunggu Review AVP';
                  } else if (full.pekerjaan_status == 2) {
                    warna = '#FF8000';
                    status = 'Menunggu Approval VP';
                  } else if (full.pekerjaan_status == 3) {
                    warna = '#00FF00';
                    status = 'Menunggu Approve VP Rancang Bangun'
                  } else if (full.pekerjaan_status == 4) {
                    warna = '#0080FF';
                    status = 'Menunggu Disposisi AVP'
                  } else if (full.pekerjaan_status == 5) {
                    warna = '#CC6600';
                    status = 'In Progress'
                  } else if (full.pekerjaan_status == 6) {
                    warna = '#3333FF';
                    status = 'In Progress'
                  } else if (full.pekerjaan_status == 7) {
                    warna = '#3333FF';
                    status = 'Pekerjaan Berjalan'
                  } else if (full.pekerjaan_status == 8) {
                    warna = '#FF8000';
                    status = 'IFA - Send User'
                  } else if (full.pekerjaan_status == 9) {
                    warna = '#FF8000';
                    status = 'IFA – Menunggu Review AVP User'
                  } else if (full.pekerjaan_status == 10) {
                    warna = '#FF8000';
                    status = 'IFA – Menunggu Approve VP User'
                  } else if (full.pekerjaan_status == 11) {
                    warna = '#B266FF';
                    status = 'IFC'
                  } else if (full.pekerjaan_status == 12) {
                    warna = '#B266FF';
                    status = 'IFC'
                  } else if (full.pekerjaan_status == 13) {
                    warna = '#B266FF';
                    status = 'IFC'
                  } else if (full.pekerjaan_status == 14) {
                    warna = '#00FFFF';
                    status = 'Selesai'
                  } else if (full.pekerjaan_status == 15) {
                    warna = '#00FFFF';
                    status = 'Selesai'
                  } else if (full.pekerjaan_status == '-') {
                    warna = '#FF0000';
                    status = 'Reject'
                  }

                  return '<span class="lead"><span class="badge" style="background-color: ' + warna + ';color:black  ">' + status + '</span></span>';
                }
              },
              {
                "render": function(data, type, full, meta) {
                  if (full.pekerjaan_status == '0') return '<center>-</center>';
              // jika staf
              // else if (($('#session_poscode').val() == 'E53110041A' || $('#session_poscode').val() == 'E53210051A' || $('#session_poscode').val() == 'E53310041A' || $('#session_poscode').val() == 'E53410041A' || $('#session_poscode').val() == 'E53410041B' || $('#session_poscode').val() == 'E53410051A' || $('#session_poscode').val() == 'E53500031B' || $('#session_poscode').val() == 'E53600031A' || $('#session_poscode').val() == 'E53600060B')) return '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" name="' + full.pekerjaan_status + '" title="Detail" onclick="fun_detail(this.id,this.name)"><i style="; color:white" class="btn btn-info btn-sm" >Detail</i></a></center>';
              // jika pekerjaan 1 dan avp

              // START BUTTON PEKERJAAN STATUS 1
                  else if (full.pekerjaan_status == '1' && $('#session_direct_superior').val() == 'E31600000')
                    return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=usulan') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=1"  title="Review" ><i  color:white" class="btn btn-primary btn-sm" >Detail</i></a></center>';
                  else if (full.pekerjaan_status == '1' && $('#session_direct_superior').val() == 'E31000000')
                    return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=usulan') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=1"  title="Review" ><i style="background-color:orange; color:white" class="btn btn-warning btn-sm" >Review</i></a></center>';
              // FINISH BUTTON PEKERJAAN STATUS 1

              // START BUTTON PEKERJAAN STATUS 2
                  else if (full.pekerjaan_status == '2' && $('#session_direct_superior').val() != 'E30000000')
                    return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=usulan') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=1"  title="Review" ><i  color:white" class="btn btn-primary btn-sm" >Detail</i></a></center>';
                  else if (full.pekerjaan_status == '2' && $('#session_direct_superior').val() == 'E30000000')
                    return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=usulan') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=1"  title="Review" ><i style="background-color:red; color:white" class="btn btn-danger btn-sm" >Approve</i></a></center>';
              // FINISH BUTTON PEKERJAAN STATUS 2



              // else if (full.pekerjaan_status == '1' && ($('#session_poscode').val() != 'E53110041A' || $('#session_poscode').val() != 'E53210051A' || $('#session_poscode').val() != 'E53310041A' || $('#session_poscode').val() != 'E53410041A' || $('#session_poscode').val() != 'E53410041B' || $('#session_poscode').val() != 'E53410051A' || $('#session_poscode').val() != 'E53500031B' || $('#session_poscode').val() != 'E53600031A' || $('#session_poscode').val() != 'E53600060B')) return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=usulan') ?>'+'&pekerjaan_id='+full.pekerjaan_id+'&status='+full.pekerjaan_status+'&rkap=1"  title="Review" ><i style="background-color:orange; color:white" class="btn btn-warning btn-sm" >Review</i></a></center>';
              // pekerjaan 2
              // else if (full.pekerjaan_status == '2' && ($('#session_poscode').val() == 'E53000000')) return '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" name="' + full.pekerjaan_status + '" title="Approve" onclick="fun_detail(this.id,this.name)"><i style="background-color:orange;color:white" class="btn btn-warning btn-sm" >Approve</i></a></center>';
              // else if (full.pekerjaan_status == '2') return '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" name="' + full.pekerjaan_status + '" title="Approve" onclick="fun_detail(this.id,this.name)"><i class="btn btn-info btn-sm" >Detail</i></a></center>';
              // pekerjaan 3
                  else if (full.pekerjaan_status == '3' && $('#session_direct_superior').val() == 'E53000000') return '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" name="' + full.pekerjaan_status + '" title="Approve" onclick="fun_detail(this.id,this.name)"><i style="background-color:red; color:white" class="btn btn-warning btn-sm" >Approve</i></a></center>';
              // pekerjaan 4

              // START BUTTON PEKERJAAN STATUS 4
                  else if ((full.pekerjaan_status == '4' && full.is_proses == 'y' && $('#session_nik').val() != '2125401'))
                    return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=usulan') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=1"  title="Review" ><i  color:white" class="btn btn-primary btn-sm" >Detail</i></a></center>';
                  else if ((full.pekerjaan_status == '4' && full.is_proses == 'n' && $('#session_poscode').val() == 'E53000000'))
                    return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=usulan') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=1"  title="Review" ><i  color:white" class="btn btn-primary btn-sm" >Detail</i></a></center>';
                  else if ((full.pekerjaan_status == '4' && full.is_proses == 'n' && $('#session_direct_superior').val() == 'E31000000'))
                    return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=usulan') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=1"  title="Review" ><i  color:white" class="btn btn-primary btn-sm" >Detail</i></a></center>';
                  else if ((full.pekerjaan_status == '4' && full.is_proses == 'n' && $('#session_nik').val() != '2125401'))
                    return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=usulan') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=1"  title="Review" ><i style="background-color:orange; color:white" class="btn btn-warning btn-sm" >Review</i></a></center>';
              // FINISH BUTTON PEKERJAAN STATUS 4

              // else if (full.pekerjaan_status == '4' && ($('#session_poscode').val() == 'E53100000' || $('#session_poscode').val() == 'E53200000' || $('#session_poscode').val() == 'E53300000' || $('#session_poscode').val() == 'E53400000')) return '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" name="' + full.pekerjaan_status + '" title="Approve" onclick="fun_detail(this.id,this.name)"><i class="btn btn-warning btn-sm" >Review</i></a></center>';
              // else if (full.pekerjaan_status == '4' && ($('#session_poscode').val() == 'E53000000')) return '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" name="' + full.pekerjaan_status + '" title="Approve" onclick="fun_detail(this.id,this.name)"><i  class="btn btn-info btn-sm" >Detail</i></a></center>';
              // selain
                  else return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=usulan') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=1"  title="Detail" ><i class="btn btn-info btn-sm" >Detail</i></a></center>';
                }
              },
              {
                "render": function(data, type, full, meta) {
                  return ((full.pekerjaan_status == '0' || full.pekerjaan_status == '-') && full.pic == $('#session_nik').val()) ? '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" title="Edit" onclick="fun_edit(this.id)"><i class="btn btn-success btn-sm" >Edit</i></a></center>' : '<center>-</center>';
                }
              },
              {
                "render": function(data, type, full, meta) {
                  return ((full.pekerjaan_status == '0' || full.pekerjaan_status == '-') && full.pic == $('#session_nik').val()) ? '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" title="Delete" onclick="fun_delete(this.id)"><i class="btn btn-danger btn-sm">Delete</i></a></center>' : '<center>-</center>';
                }
              },
              ]
}).columns.adjust();
      /*End Isi Table Usulan */

      // Start Isi Table Berjalan
      // $('#table_berjalan thead tr').clone(true).addClass('filters_berjalan').appendTo('#table_berjalan thead');
$('#table_berjalan').DataTable({
        // "scrollX": true,
        // "fixedHeader": true,
  "initComplete": function(settings, json) {
    $("#table_berjalan").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
  },
  "ajax": {
    "url": "<?= base_url('project/') ?>pekerjaan_usulan/getPekerjaanBerjalan?klasifikasi_pekerjaan_id=1&pekerjaan_status=5,6,7",
    "dataSrc": ""
  },
  "columns": [{
    render: function(data, type, full, meta) {
      return meta.row + meta.settings._iDisplayStart + 1;
    }
  },
  {
    render: function(data, type, full, meta) {
      var nomor_isi = '';
      if (full.pekerjaan_nomor) {
        var nomor = full.pekerjaan_nomor.split('-');
        nomor[0] = pad(nomor[0], 3);
        if (full.milik == 'y' && full.pekerjaan_nomor == null) {
          nomor_isi = '';
        } else if (full.milik == 'y' && full.avp_perencana_sama == 'y' && <?= $session['pegawai_nik'] ?> != <?= $admin_sistemnya ?> && (full.perencana_proses=='0' || full.avp_proses == '0') ) {
          nomor_isi = '<span class="badge" style="background-color:#c13333 ">' + nomor.join('-') + '</span>';
        } else if (full.milik == 'y' && full.avp_perencana_sama == 'y' && <?= $session['pegawai_nik'] ?> != <?= $admin_sistemnya ?> && (full.perencana_proses=='1' || full.avp_proses =='1')) {
          nomor_isi = nomor.join('-');
        } else if (full.milik == 'y' && full.avp_perencana_sama == 'n' && <?= $session['pegawai_nik'] ?> != <?= $admin_sistemnya ?> && (full.perencana_proses=='1' || full.avp_proses == '1')) {
          nomor_isi = nomor.join('-');
        } else if (full.milik == 'y' && full.avp_perencana_sama == 'n' && <?= $session['pegawai_nik'] ?> != <?= $admin_sistemnya ?> && (full.perencana_proses=='0' || full.avp_proses =='0')) {
          nomor_isi = '<span class="badge" style="background-color:#c13333 ">' + nomor.join('-') + '</span>';
        } else {
          nomor_isi = nomor.join('-');
        }
      }
      return nomor_isi;
    }
  },

  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_judul;
    }
  },
  {
    "data": "pegawai_nama"
  },
  {
    "render": function(data, type, full, meta) {
      var status = '';
      var warna = '';
      var font = 'black';
      if (full.pekerjaan_status == 0) {
        status = 'Draft';
        warna = '#A0A0A0';
      } else if (full.pekerjaan_status == 1) {
        warna = '#FFFF00';
        status = 'Menunggu Review AVP';
      } else if (full.pekerjaan_status == 2) {
        warna = '#FF8000';
        status = 'Menunggu Approval VP';
      } else if (full.pekerjaan_status == 3) {
        warna = '#00FF00';
        status = 'Menunggu Approve VP Rancang Bangun'
      } else if (full.pekerjaan_status == 4) {
        warna = '#0080FF';
        status = 'Menunggu Disposisi AVP'
      } else if (full.pekerjaan_status == 5 && full.perencana_proses == '1') {
        warna = '#CC4200';
        status = 'IFA - Send AVP'
      } else if (full.pekerjaan_status == 5 && full.status_avp == '0' && full.revisi_dokumen > '0') {
        warna = '#CC6600';
        status = 'IFA - Revisi'
      } else if (full.pekerjaan_status == 5 && full.status_avp == '0') {
        warna = '#CC6600';
        status = 'In Progress'
      } else if ((full.pekerjaan_status == 6 || full.status_avp == '1') && full.revisi_dokumen > '0') {
        warna = 'blue';
        font = 'white';
        status = 'IFA Revisi'
      } else if (full.pekerjaan_status == 6 || full.status_avp == '1' && full.avp_koor == '1') {
        warna = 'blue';
        font = 'white';
        status = 'IFA - untuk dikirim ke VP oleh AVP ' + full.bagian_nama
      } else if (full.pekerjaan_status == 6 || full.status_avp == '1' && full.avp_koor == '0' && full.avp_terkait_proses == '1') {
        warna = 'blue';
        font = 'white';
        status = 'IFA - Direview AVP Bidang ' + full.bagian_nama
      } else if (full.pekerjaan_status == 6 || full.status_avp == '1' && full.avp_koor == '0' && full.avp_terkait_proses == '0') {
        warna = 'blue';
        font = 'white';
        status = 'IFA - untuk direview AVP Bidang ' + full.bagian_nama
      } else if (full.pekerjaan_status == 7 && full.revisi_dokumen > '0') {
        warna = 'red';
        font = 'white';
        status = 'IFA - Revisi'
      } else if (full.pekerjaan_status == 7) {
        warna = 'red';
        font = 'white';
        status = 'IFA - Send VP'
      } else if (full.pekerjaan_status == 8) {
        warna = '#FF8000';
        status = 'IFA - Send User'
      } else if (full.pekerjaan_status == 9) {
        warna = '#B266FF';
        status = 'IFC'
      } else if (full.pekerjaan_status == 10) {
        warna = '#B266FF';
        status = 'IFC'
      } else if (full.pekerjaan_status == 11) {
        warna = '#B266FF';
        status = 'IFC'
      } else if (full.pekerjaan_status == 12) {
        warna = '#00FFFF';
        status = 'Selesai'
      } else if (full.pekerjaan_status == 15) {
        warna = '#00FFFF';
        status = 'Selesai'
      } else if (full.pekerjaan_status == '-') {
        warna = '#FF0000';
        status = 'Reject'
      }

      return '<span class="lead"><span class="badge" style="background-color: ' + warna + ';color:' + font + '  ">' + status + '</span></span>';
    }
  },
  {
    "data": "pekerjaan_progress"
  }, {
    "render": function(data, type, full, meta) {
      return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=berjalan') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=1"  title="Detail" ><i class="btn btn-info btn-sm" >Detail</i></a></center>';
    }
  }, {
    render: function(data, type, full, meta) {
      return full.pekerjaan_isi_proses;
    }
  }, {
    render: function(data, type, full, meta) {
      return full.pekerjaan_isi_mesin;
    }
  }, {
    render: function(data, type, full, meta) {
      return full.pekerjaan_isi_listrik;
    }
  }, {
    render: function(data, type, full, meta) {
      return full.pekerjaan_isi_instrumen;
    }
  }, {
    render: function(data, type, full, meta) {
      return full.pekerjaan_isi_sipil;
    }
  }, {
    "data": "total"
  }, {
    "data": "tanggal_start"
  }, {
    render: function(data, type, full, meta) {
      return (full.tanggal_akhir < '<?= date('Y-m-d') ?>') ? '<span class="badge" style="background-color:#c13333;font-size:9pt" >' + full.tanggal_akhir + '</span>' : full.tanggal_akhir;
    }
  },

  ]
});
      // End isi Table Berjalan

      /* start isi Table IFI*/
$('#table_ifi thead tr').clone(true).addClass('filters_ifi').appendTo('#table_ifi thead');
var table_ifi = $('#table_ifi').DataTable({
  orderCellsTop: true,
  initComplete: function() {
    $("#table_ifi").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
    var api = this.api();
    api
    .columns()
    .eq(0)
    .each(function(colIdx) {
      var cell = $('.filters_ifi th').eq(
        $(api.column(colIdx).header()).index()
        );
      var title = $(cell).text();
      $(cell).html('<input type="text" class="form-control" style="width:100%" placeholder="' + title + '" />');
      $(
        'input',
        $('.filters_ifi th').eq($(api.column(colIdx).header()).index())
        )
      .off('keyup change')
      .on('keyup change', function(e) {
        e.stopPropagation();

        $(this).attr('title', $(this).val());
        var regexr = '({search})';
        var cursorPosition = this.selectionStart;
        api
        .column(colIdx)
        .search(
          this.value != '' ?
          regexr.replace('{search}', '(((' + this.value + ')))') :
          '',
          this.value != '',
          this.value == ''
          )
        .draw();

        $(this)
        .focus()[0]
        .setSelectionRange(cursorPosition, cursorPosition);
      });
    });
  },
  "ajax": {
    "url": "<?= base_url('project/') ?>pekerjaan_usulan/getPekerjaanUsulan?klasifikasi_pekerjaan_id=1&pekerjaan_status=8,9,10&pekerjaan_jenis=IFI",
    "dataSrc": ""
  },
  "columns": [{
    render: function(data, type, full, meta) {
      var nomor_isi = '';
      if (full.pekerjaan_nomor != null) {
        var nomor = full.pekerjaan_nomor.split('-');
        nomor[0] = pad(nomor[0], 3);
        if (full.milik == 'y' && full.pekerjaan_nomor == null) {
          nomor_isi = '';
        } else if (full.milik == 'y') {
          nomor_isi = '<span class="badge" style="background-color:#c13333 ">' + nomor.join('-') + '</span>';
        } else {
          nomor_isi = nomor.join('-');
        }
      } else {
        nomor_isi = '';
      }
      return nomor_isi
    }
  },
  {
    "data": "tanggal_awal"
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_judul;
    }
  },
  {
    "data": "pegawai_nama"
  },
  {
    "render": function(data, type, full, meta) {
      var status = '';
      var warna = '';
      if (full.pekerjaan_status == 0) {
        status = 'Draft';
        warna = '#A0A0A0';
      } else if (full.pekerjaan_status == 1) {
        warna = '#FFFF00';
        status = 'Menunggu Review AVP';
      } else if (full.pekerjaan_status == 2) {
        warna = '#FF8000';
        status = 'Menunggu Approval VP';
      } else if (full.pekerjaan_status == 3) {
        warna = '#00FF00';
        status = 'Menunggu Approve VP Rancang Bangun'
      } else if (full.pekerjaan_status == 4) {
        warna = '#0080FF';
        status = 'Menunggu Disposisi AVP'
      } else if (full.pekerjaan_status == 5) {
        warna = '#CC6600';
        status = 'In Progress'
      } else if (full.pekerjaan_status == 6) {
        warna = '#3333FF';
        status = 'Pekerjaan Berjalan'
      } else if (full.pekerjaan_status == 7) {
        warna = '#3333FF';
        status = 'Pekerjaan Berjalan'
      } else if (full.pekerjaan_status == 8) {
        warna = '#FF8000';
        status = 'IFA - Send User'
      } else if (full.pekerjaan_status == 9) {
        warna = '#FF8000';
        status = 'IFA – Menunggu Review AVP User';
      } else if (full.pekerjaan_status == 10) {
        warna = '#FF8000';
        status = 'IFA – Menunggu Approve VP User';
      } else if (full.pekerjaan_status == 11) {
        warna = '#B266FF';
        status = 'IFA - Approve User / IFC';
      } else if (full.pekerjaan_status == 12) {
        warna = '#B266FF';
        status = 'IFC - Send AVP';
      } else if (full.pekerjaan_status == 13) {
        warna = '#B266FF';
        status = 'IFC'
      } else if (full.pekerjaan_status == 14) {
        warna = '#00FFFF';
        status = 'Selesai'
      } else if (full.pekerjaan_status == 15) {
        warna = '#00FFFF';
        status = 'Selesai'
      } else if (full.pekerjaan_status == '-') {
        warna = '#FF0000';
        status = 'Reject'
      }

      return '<span class="lead"><span class="badge" style="background-color: ' + warna + ';color:black  ">' + status + '</span></span>';
    }
  },
  {
    "render": function(data, type, full, meta) {
      return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=ifa') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=0"  title="Detail" ><i class="btn btn-info btn-sm">Detail</i></a></center>';
    }
  },
  ]
});
      /*end table ifi*/


      // start isi Table IFA
$('#table_ifa thead tr').clone(true).addClass('filters_ifa').appendTo('#table_ifa thead');
var table_ifa = $('#table_ifa').DataTable({
  orderCellsTop: true,
  initComplete: function() {
    var api = this.api();
          // For each column
    api
    .columns()
    .eq(0)
    .each(function(colIdx) {
              // Set the header cell to contain the input element
      var cell = $('.filters_ifa th').eq(
        $(api.column(colIdx).header()).index()
        );
      var title = $(cell).text();
      $(cell).html('<input type="text" class="form-control" style="width:100%" placeholder="' + title + '" />');

              // On every keypress in this input
      $(
        'input',
        $('.filters_ifa th').eq($(api.column(colIdx).header()).index())
        )
      .off('keyup change')
      .on('keyup change', function(e) {
        e.stopPropagation();

                  // Get the search value
        $(this).attr('title', $(this).val());
                  var regexr = '({search})'; //$(this).parents('th').find('select').val();

                  var cursorPosition = this.selectionStart;
                  // Search the column for that value
                  api
                  .column(colIdx)
                  .search(
                    this.value != '' ?
                    regexr.replace('{search}', '(((' + this.value + ')))') :
                    '',
                    this.value != '',
                    this.value == ''
                    )
                  .draw();

                  $(this)
                  .focus()[0]
                  .setSelectionRange(cursorPosition, cursorPosition);
                });
    });
  },
  "scrollX": true,
  "ajax": {
    "url": "<?= base_url('project/') ?>pekerjaan_usulan/getPekerjaanUsulan?klasifikasi_pekerjaan_id=1&pekerjaan_status=8,9,10&pekerjaan_jenis=IFA",
    "dataSrc": ""
  },
  "columns": [{
    render: function(data, type, full, meta) {
      var nomor = full.pekerjaan_nomor.split('-');
      nomor[0] = pad(nomor[0], 3);
      return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333;font-size:9pt" >' + nomor.join('-') + '</span>' : nomor.join('-');
    }
  },
  {
    "data": "tanggal_awal"
  },
  {
    "data": "tanggal_akhir"
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_judul;
    }
  },
  {
    "data": "pegawai_nama"
  },
  {
    "render": function(data, type, full, meta) {
      var status = '';
      var warna = '';
      if (full.pekerjaan_status == 0) {
        status = 'Draft';
        warna = '#A0A0A0';
      } else if (full.pekerjaan_status == 1) {
        warna = '#FFFF00';
        status = 'Menunggu Review AVP';
      } else if (full.pekerjaan_status == 2) {
        warna = '#FF8000';
        status = 'Menunggu Approval VP';
      } else if (full.pekerjaan_status == 3) {
        warna = '#00FF00';
        status = 'Menunggu Approve VP Rancang Bangun'
      } else if (full.pekerjaan_status == 4) {
        warna = '#0080FF';
        status = 'Menunggu Disposisi AVP'
      } else if (full.pekerjaan_status == 5) {
        warna = '#CC6600';
        status = 'In Progress'
      } else if (full.pekerjaan_status == 6) {
        warna = '#3333FF';
        status = 'Pekerjaan Berjalan'
      } else if (full.pekerjaan_status == 7) {
        warna = '#3333FF';
        status = 'Pekerjaan Berjalan'
                // } else if (full.pekerjaan_status == 8 && full.is_proses == 'r') {
                //     warna = '#FF8000';
                //     status = 'IFA Rev'
      } else if (full.pekerjaan_status == 8) {
        warna = '#FF8000';
        status = 'IFA - Send User'
      } else if (full.pekerjaan_status == 9) {
        warna = '#FF8000';
        status = 'IFA – Menunggu Review AVP User';
      } else if (full.pekerjaan_status == 10) {
        warna = '#FF8000';
        status = 'IFA – Menunggu Approve VP User';
      } else if (full.pekerjaan_status == 11) {
        warna = '#B266FF';
        status = 'IFA - Approve User / IFC';
      } else if (full.pekerjaan_status == 12) {
        warna = '#B266FF';
        status = 'IFC - Send AVP';
      } else if (full.pekerjaan_status == 13) {
        warna = '#B266FF';
        status = 'IFC'
      } else if (full.pekerjaan_status == 14) {
        warna = '#00FFFF';
        status = 'Selesai'
      } else if (full.pekerjaan_status == 15) {
        warna = '#00FFFF';
        status = 'Selesai'
      } else if (full.pekerjaan_status == '-') {
        warna = '#FF0000';
        status = 'Reject'
      }

      return '<span class="lead"><span class="badge" style="background-color: ' + warna + ';color:black  ">' + status + '</span></span>';
    }
  },
  {
    "render": function(data, type, full, meta) {
      return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=ifa') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=1"  title="Detail" ><i class="btn btn-info btn-sm">Detail</i></a></center>';
    }
  },
  {
    "render": function(data, type, full, meta) {
      if (full.pic == $('#user_session').val() && full.extend_ajuan_tanggal == '') return '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" name="' + full.pekerjaan_status + '" title="Ajuan Extend" onclick="fun_ajuan_extend(this.id,this.name)"><i class="fas fa-share" data-toggle="modal" data-target="#modal_ajuan_extend"></i></a></center>';
      else return (full.extend_ajuan_tanggal == '') ? '<center>-</center>' : '<center>' + full.extend_ajuan_tanggal.split("-").reverse().join("-") + '</center>';
    }
  },
  {
    "render": function(data, type, full, meta) {
      if ($('#user_session').val() == <?= $admin_sistemnya ?> && (full.extend_status != '1')) return '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" name="' + full.pekerjaan_status + '" title="Extend" onclick="fun_extend(this.id,this.name)"><i class="fas fa-share" data-toggle="modal" data-target="#modal_extend"></i></a></center>';
      else return (full.extend_status != '1') ? '<center>-</center>' : '<center>' + full.extend_tanggal.split("-").reverse().join("-") + '</center>';
    }
  },
  ]
}).columns.adjust();
      // End isi table ifa

      // start isi table ifc
$('#table_ifc thead tr').clone(true).addClass('filters_ifc').appendTo('#table_ifc thead');
$('#table_ifc').DataTable({
  orderCellsTop: true,
  initComplete: function() {
    var api = this.api();
          // For each column
    api
    .columns()
    .eq(0)
    .each(function(colIdx) {
              // Set the header cell to contain the input element
      var cell = $('.filters_ifc th').eq(
        $(api.column(colIdx).header()).index()
        );
      var title = $(cell).text();
      $(cell).html('<input type="text" class="form-control" style="width:100%" placeholder="' + title + '" />');

              // On every keypress in this input
      $(
        'input',
        $('.filters_ifc th').eq($(api.column(colIdx).header()).index())
        )
      .off('keyup change')
      .on('keyup change', function(e) {
        e.stopPropagation();

                  // Get the search value
        $(this).attr('title', $(this).val());
                  var regexr = '({search})'; //$(this).parents('th').find('select').val();

                  var cursorPosition = this.selectionStart;
                  // Search the column for that value
                  api
                  .column(colIdx)
                  .search(
                    this.value != '' ?
                    regexr.replace('{search}', '(((' + this.value + ')))') :
                    '',
                    this.value != '',
                    this.value == ''
                    )
                  .draw();

                  $(this)
                  .focus()[0]
                  .setSelectionRange(cursorPosition, cursorPosition);
                });
    });
  },
        // "scrollX": true,
  "ajax": {
    "url": "<?= base_url() ?>project/pekerjaan_usulan/getPekerjaanUsulan?klasifikasi_pekerjaan_id=1&pekerjaan_status=11,12,13&pekerjaan_jenis=IFC",
    "dataSrc": ""
  },
  "columns": [{
    render: function(data, type, full, meta) {
      var nomor = full.pekerjaan_nomor.split('-');
      nomor[0] = pad(nomor[0], 3);
      return (full.milik == 'y' && full.is_proses != 'y') ? '<span class="badge" style="background-color:#c13333;font-size:9pt" >' + nomor.join('-') + '</span>' : nomor.join('-');
    }
  },
  {
    "data": "tanggal_awal"
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_judul;
    }
  },
  {
    "render": function(data, type, full, meta) {
      var status = '';
      var warna = '';
      if (full.pekerjaan_status == 0) {
        status = 'Draft';
        warna = '#A0A0A0';
      } else if (full.pekerjaan_status == 1) {
        warna = '#FFFF00';
        status = 'Menunggu Review AVP';
      } else if (full.pekerjaan_status == 2) {
        warna = '#FF8000';
        status = 'Menunggu Approval VP';
      } else if (full.pekerjaan_status == 3) {
        warna = '#00FF00';
        status = 'Menunggu Approve VP Rancang Bangun'
      } else if (full.pekerjaan_status == 4) {
        warna = '#0080FF';
        status = 'Menunggu Disposisi AVP'
      } else if (full.pekerjaan_status == 5) {
        warna = '#CC6600';
        status = 'In Progress'
      } else if (full.pekerjaan_status == 6) {
        warna = '#3333FF';
        status = 'Pekerjaan Berjalan'
      } else if (full.pekerjaan_status == 7) {
        warna = '#3333FF';
        status = 'Pekerjaan Berjalan'
      } else if (full.pekerjaan_status == 8) {
        warna = '#FF8000';
        status = 'IFA - Send User';
      } else if (full.pekerjaan_status == 9) {
        warna = '#FF8000';
        status = 'IFA – Menunggu Review AVP User';
      } else if (full.pekerjaan_status == 10) {
        warna = '#FF8000';
        status = 'IFA – Menunggu Approve VP User';
      } else if (full.pekerjaan_status == 11 && full.proses_perencana_ifc == '1') {
        warna = '#B266FF';
        status = 'IFC - Menunggu Review AVP';
      } else if (full.pekerjaan_status == 11) {
        warna = '#B266FF';
        status = 'IFA - Approve User / IFC';
      } else if (full.pekerjaan_status == 12) {
        warna = '#B266FF';
        status = 'IFC - Send AVP';
      } else if (full.pekerjaan_status == 13) {
        warna = '#B266FF';
        status = 'IFC - Send VP'
      } else if (full.pekerjaan_status == 14) {
        warna = '#00FFFF';
        status = 'Selesai'
      } else if (full.pekerjaan_status == 15) {
        warna = '#00FFFF';
        status = 'Selesai'
      } else if (full.pekerjaan_status == '-') {
        warna = '#FF0000';
        status = 'Reject'
      }

      return '<span class="lead"><span class="badge" style="background-color: ' + warna + ';color:black  ">' + status + '</span></span>';
    }
  },
  {
    "render": function(data, type, full, meta) {
      return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=ifc') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=1"  title="Review" ><i class="btn btn-info btn-sm">Detail</i></a></center>';
    }
  },

  ]
}).columns.adjust();
      // end isi table ifc

      // start isi table ift
$('#table_ift thead tr').clone(true).addClass('filters_ift').appendTo('#table_ift thead');
$('#table_ift').DataTable({
  orderCellsTop: true,
  initComplete: function() {
    var api = this.api();
          // For each column
    api
    .columns()
    .eq(0)
    .each(function(colIdx) {
              // Set the header cell to contain the input element
      var cell = $('.filters_ift th').eq(
        $(api.column(colIdx).header()).index()
        );
      var title = $(cell).text();
      $(cell).html('<input type="text" class="form-control" style="width:100%" placeholder="' + title + '" />');

              // On every keypress in this input
      $(
        'input',
        $('.filters_ift th').eq($(api.column(colIdx).header()).index())
        )
      .off('keyup change')
      .on('keyup change', function(e) {
        e.stopPropagation();

                  // Get the search value
        $(this).attr('title', $(this).val());
                  var regexr = '({search})'; //$(this).parents('th').find('select').val();

                  var cursorPosition = this.selectionStart;
                  // Search the column for that value
                  api
                  .column(colIdx)
                  .search(
                    this.value != '' ?
                    regexr.replace('{search}', '(((' + this.value + ')))') :
                    '',
                    this.value != '',
                    this.value == ''
                    )
                  .draw();

                  $(this)
                  .focus()[0]
                  .setSelectionRange(cursorPosition, cursorPosition);
                });
    });
  },
        // "scrollX": true,
  "ajax": {
    "url": "<?= base_url() ?>project/pekerjaan_usulan/getPekerjaanUsulan?klasifikasi_pekerjaan_id=1&pekerjaan_status=11,12,13&pekerjaan_jenis=IFT",
    "dataSrc": ""
  },
  "columns": [{
    render: function(data, type, full, meta) {
      var nomor = full.pekerjaan_nomor.split('-');
      nomor[0] = pad(nomor[0], 3);
      return (full.milik == 'y' && full.is_proses != 'y') ? '<span class="badge" style="background-color:#c13333;font-size:9pt" >' + nomor.join('-') + '</span>' : nomor.join('-');
    }
  },
  {
    "data": "tanggal_awal"
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_judul;
    }
  },
  {
    "render": function(data, type, full, meta) {
      var status = '';
      var warna = '';
      if (full.pekerjaan_status == 0) {
        status = 'Draft';
        warna = '#A0A0A0';
      } else if (full.pekerjaan_status == 1) {
        warna = '#FFFF00';
        status = 'Menunggu Review AVP';
      } else if (full.pekerjaan_status == 2) {
        warna = '#FF8000';
        status = 'Menunggu Approval VP';
      } else if (full.pekerjaan_status == 3) {
        warna = '#00FF00';
        status = 'Menunggu Approve VP Rancang Bangun'
      } else if (full.pekerjaan_status == 4) {
        warna = '#0080FF';
        status = 'Menunggu Disposisi AVP'
      } else if (full.pekerjaan_status == 5) {
        warna = '#CC6600';
        status = 'In Progress'
      } else if (full.pekerjaan_status == 6) {
        warna = '#3333FF';
        status = 'Pekerjaan Berjalan'
      } else if (full.pekerjaan_status == 7) {
        warna = '#3333FF';
        status = 'Pekerjaan Berjalan'
      } else if (full.pekerjaan_status == 8) {
        warna = '#FF8000';
        status = 'IFA - Send User';
      } else if (full.pekerjaan_status == 9) {
        warna = '#FF8000';
        status = 'IFA – Menunggu Review AVP User';
      } else if (full.pekerjaan_status == 10) {
        warna = '#FF8000';
        status = 'IFA – Menunggu Approve VP User';
      } else if (full.pekerjaan_status == 11 && full.proses_perencana_ifc == '1') {
        warna = '#B266FF';
        status = 'IFC - Menunggu Review AVP';
      } else if (full.pekerjaan_status == 11) {
        warna = '#B266FF';
        status = 'IFA - Approve User / IFC';
      } else if (full.pekerjaan_status == 12) {
        warna = '#B266FF';
        status = 'IFC - Send AVP';
      } else if (full.pekerjaan_status == 13) {
        warna = '#B266FF';
        status = 'IFC - Send VP'
      } else if (full.pekerjaan_status == 14) {
        warna = '#00FFFF';
        status = 'Selesai'
      } else if (full.pekerjaan_status == 15) {
        warna = '#00FFFF';
        status = 'Selesai'
      } else if (full.pekerjaan_status == '-') {
        warna = '#FF0000';
        status = 'Reject'
      }

      return '<span class="lead"><span class="badge" style="background-color: ' + warna + ';color:black  ">' + status + '</span></span>';
    }
  },
  {
    "render": function(data, type, full, meta) {
      return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=ifc') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=1"  title="Review" ><i class="btn btn-info btn-sm">Detail</i></a></center>';
    }
  },

  ]
}).columns.adjust();
      // end isi table ift

      // start isi table ifr
$('#table_ifr thead tr').clone(true).addClass('filters_ifr').appendTo('#table_ifr thead');
$('#table_ifr').DataTable({
  orderCellsTop: true,
  initComplete: function() {
    var api = this.api();
          // For each column
    api
    .columns()
    .eq(0)
    .each(function(colIdx) {
              // Set the header cell to contain the input element
      var cell = $('.filters_ifr th').eq(
        $(api.column(colIdx).header()).index()
        );
      var title = $(cell).text();
      $(cell).html('<input type="text" class="form-control" style="width:100%" placeholder="' + title + '" />');

              // On every keypress in this input
      $(
        'input',
        $('.filters_ifr th').eq($(api.column(colIdx).header()).index())
        )
      .off('keyup change')
      .on('keyup change', function(e) {
        e.stopPropagation();

                  // Get the search value
        $(this).attr('title', $(this).val());
                  var regexr = '({search})'; //$(this).parents('th').find('select').val();

                  var cursorPosition = this.selectionStart;
                  // Search the column for that value
                  api
                  .column(colIdx)
                  .search(
                    this.value != '' ?
                    regexr.replace('{search}', '(((' + this.value + ')))') :
                    '',
                    this.value != '',
                    this.value == ''
                    )
                  .draw();

                  $(this)
                  .focus()[0]
                  .setSelectionRange(cursorPosition, cursorPosition);
                });
    });
  },
        // "scrollX": true,
  "ajax": {
    "url": "<?= base_url() ?>project/pekerjaan_usulan/getPekerjaanUsulan?klasifikasi_pekerjaan_id=1&pekerjaan_status=11,12,13&pekerjaan_jenis=IFR",
    "dataSrc": ""
  },
  "columns": [{
    render: function(data, type, full, meta) {
      var nomor = full.pekerjaan_nomor.split('-');
      nomor[0] = pad(nomor[0], 3);
      return (full.milik == 'y' && full.is_proses!='y') ? '<span class="badge" style="background-color:#c13333;font-size:9pt" >' + nomor.join('-') + '</span>' : nomor.join('-');
    }
  },
  {
    "data": "tanggal_awal"
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_judul;
    }
  },
  {
    "render": function(data, type, full, meta) {
      var status = '';
      var warna = '';
      if (full.pekerjaan_status == 0) {
        status = 'Draft';
        warna = '#A0A0A0';
      } else if (full.pekerjaan_status == 1) {
        warna = '#FFFF00';
        status = 'Menunggu Review AVP';
      } else if (full.pekerjaan_status == 2) {
        warna = '#FF8000';
        status = 'Menunggu Approval VP';
      } else if (full.pekerjaan_status == 3) {
        warna = '#00FF00';
        status = 'Menunggu Approve VP Rancang Bangun'
      } else if (full.pekerjaan_status == 4) {
        warna = '#0080FF';
        status = 'Menunggu Disposisi AVP'
      } else if (full.pekerjaan_status == 5) {
        warna = '#CC6600';
        status = 'In Progress'
      } else if (full.pekerjaan_status == 6) {
        warna = '#3333FF';
        status = 'Pekerjaan Berjalan'
      } else if (full.pekerjaan_status == 7) {
        warna = '#3333FF';
        status = 'Pekerjaan Berjalan'
      } else if (full.pekerjaan_status == 8) {
        warna = '#FF8000';
        status = 'IFA - Send User';
      } else if (full.pekerjaan_status == 9) {
        warna = '#FF8000';
        status = 'IFA – Menunggu Review AVP User';
      } else if (full.pekerjaan_status == 10) {
        warna = '#FF8000';
        status = 'IFA – Menunggu Approve VP User';
      } else if (full.pekerjaan_status == 11 && full.proses_perencana_ifc == '1') {
        warna = '#B266FF';
        status = 'IFC - Menunggu Review AVP';
      } else if (full.pekerjaan_status == 11) {
        warna = '#B266FF';
        status = 'IFA - Approve User / IFC';
      } else if (full.pekerjaan_status == 12) {
        warna = '#B266FF';
        status = 'IFC - Send AVP';
      } else if (full.pekerjaan_status == 13) {
        warna = '#B266FF';
        status = 'IFC - Send VP'
      } else if (full.pekerjaan_status == 14) {
        warna = '#00FFFF';
        status = 'Selesai'
      } else if (full.pekerjaan_status == 15) {
        warna = '#00FFFF';
        status = 'Selesai'
      } else if (full.pekerjaan_status == '-') {
        warna = '#FF0000';
        status = 'Reject'
      }

      return '<span class="lead"><span class="badge" style="background-color: ' + warna + ';color:black  ">' + status + '</span></span>';
    }
  },
  {
    "render": function(data, type, full, meta) {
      return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=ifc') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=1"  title="Review" ><i class="btn btn-info btn-sm">Detail</i></a></center>';
    }
  },

  ]
}).columns.adjust();
      // end isi table ifr

      // start isi table selesai
$('#table_selesai thead tr').clone(true).addClass('filters_selesai').appendTo('#table_selesai thead');
$('#table_selesai').DataTable({
  orderCellsTop: true,
  initComplete: function() {
    var api = this.api();
    api.columns().eq(0).each(function(colIdx) {
      var cell = $('.filters_selesai th').eq(
        $(api.column(colIdx).header()).index()
        );
      var title = $(cell).text();
      $(cell).html('<input type="text" class="form-control" style="width:100%" placeholder="' + title + '" />');

      $('input', $('.filters_selesai th').eq($(api.column(colIdx).header()).index())).off('keyup change').on('keyup change', function(e) {
        e.stopPropagation();
        $(this).attr('title', $(this).val());
        var regexr = '({search})';
        var cursorPosition = this.selectionStart;

        api.column(colIdx).search(this.value != '' ? regexr.replace('{search}', '(((' + this.value + ')))') : '', this.value != '', this.value == '').draw();

        $(this).focus()[0].setSelectionRange(cursorPosition, cursorPosition);
      });
    });
  },
        // "scrollX": true,
  "ajax": {
    "url": "<?= base_url() ?>project/pekerjaan_usulan/getPekerjaanUsulan?klasifikasi_pekerjaan_id=1&pekerjaan_status=14,15,16",
    "dataSrc": ""
  },
  "columns": [{
    render: function(data, type, full, meta) {
      return meta.row + meta.settings._iDisplayStart + 1;
    }
  },
  {
    render: function(data, type, full, meta) {
      var nomor = full.pekerjaan_nomor.split('-');
      nomor[0] = pad(nomor[0], 3);
      return nomor.join('-');
    }
  },
  {
    "data": "tanggal_selesai"
  },
  {
    "data": "pekerjaan_judul"
  },
  {
    "data": "pegawai_nama_dep"
  },
  {
    "data": "usr_name"
  },
  {
    "render": function(data, type, full, meta) {
      var status = '';
      var warna = '';
      if (full.pekerjaan_status == 0) {
        status = 'Draft';
        warna = '#A0A0A0';
      } else if (full.pekerjaan_status == 1) {
        warna = '#FFFF00';
        status = 'Menunggu Review AVP';
      } else if (full.pekerjaan_status == 2) {
        warna = '#FF8000';
        status = 'Menunggu Approval VP';
      } else if (full.pekerjaan_status == 3) {
        warna = '#00FF00';
        status = 'Menunggu Approval VP Rancang Bangun'
      } else if (full.pekerjaan_status == 4) {
        warna = '#0080FF';
        status = 'Approved VP Cangun'
      } else if (full.pekerjaan_status == 5) {
        warna = '#CC6600';
        status = 'In Progress'
      } else if (full.pekerjaan_status == 6) {
        warna = '#3333FF';
        status = 'Pekerjaan Berjalan'
      } else if (full.pekerjaan_status == 7) {
        warna = '#3333FF';
        status = 'Pekerjaan Berjalan'
                // } else if (full.pekerjaan_status == 8 && full.is_proses == 'r') {
                //     warna = '#FF8000';
                //     status = 'IFA Rev'
      } else if (full.pekerjaan_status == 8) {
        warna = '#FF8000';
        status = 'IFA'
      } else if (full.pekerjaan_status == 9) {
        warna = '#FF8000';
        status = 'IFA AVP';
      } else if (full.pekerjaan_status == 10) {
        warna = '#FF8000';
        status = 'IFA VP';
      } else if (full.pekerjaan_status == 11) {
        warna = '#B266FF';
        status = 'IFC';
      } else if (full.pekerjaan_status == 12) {
        warna = '#B266FF';
        status = 'IFC';
      } else if (full.pekerjaan_status == 13) {
        warna = '#00FFFF';
        status = 'IFC'
      } else if (full.pekerjaan_status == 14) {
        warna = '#00FFFF';
        status = 'Selesai'
      } else if (full.pekerjaan_status == 15) {
        warna = '#00FFFF';
        status = 'Selesai'
      } else if (full.pekerjaan_status == 16) {
        warna = '#FF0000';
        status = 'Cancel'
      } else if (full.pekerjaan_status == '-') {
        warna = '#FF0000';
        status = 'Reject'
      }

      return '<span class="lead"><span class="badge" style="background-color: ' + warna + ';color:black  ">' + status + '</span></span>';
    }
  },
  {
    "render": function(data, type, full, meta) {
      return (full.is_allow == 'y') ? '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=selesai') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=1"  title="Review" ><i class="btn btn-info btn-sm">Detail</i></a></center>' : '-';
    }
  },
  ]
}).columns.adjust();
      // tambahan
setTimeout(() => {
  var adjust = $('#table_ifa').DataTable().columns.adjust().draw();
}, 1500);
      // tambahan
      // END TABLE

      // START SELECT2
      // start select2 filter
$('#id_user_cari').select2({
        // dropdownParent: $('#modal_usulan_upload'),
  allowClear: true,
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
      //   end select2 filter

$('.select2-selection').css({
  height: 'auto',
  margin: '0px -10px 0px -10px'
});
$('.select2').css('width', '100%');
      // END SELECT2
      //
});

$('#cari_filter').on('click', function(e) {
  var data = $('#filter').serialize();
  $('#table_usulan').DataTable().ajax.url('<?= base_url('project/pekerjaan_usulan/getPekerjaanUsulan?klasifikasi_pekerjaan_id=1&pekerjaan_status=-,0,1,2,3,4&') ?>' + data).load();
  $('#table_berjalan').DataTable().ajax.url('<?= base_url('project/pekerjaan_usulan/getPekerjaanBerjalan?klasifikasi_pekerjaan_id=1&pekerjaan_status=5,6,7&') ?>' + data).load();
  $('#table_ifa').DataTable().ajax.url('<?= base_url('project/pekerjaan_usulan/getPekerjaanUsulan?klasifikasi_pekerjaan_id=1&pekerjaan_status=8&') ?>' + data).load();
  $('#table_ifc').DataTable().ajax.url('<?= base_url('project/pekerjaan_usulan/getPekerjaanUsulan?klasifikasi_pekerjaan_id=1&pekerjaan_status=9,10,11&') ?>' + data).load();
  $('#table_selesai').DataTable().ajax.url('<?= base_url('project/pekerjaan_usulan/getPekerjaanUsulan?klasifikasi_pekerjaan_id=1&pekerjaan_status=14,15,16&') ?>' + data).load();
})

    /* FROM CARI SUBMIT */
$('#cari_berjalan').on('click', function(e) {
      // alert('tes')
      // e.preventDefault();
  var data = $('#filter').serialize();
  $('#table_berjalan').DataTable().ajax.url('<?= base_url('project/pekerjaan_usulan/getPekerjaanBerjalan?klasifikasi_pekerjaan_id=1&pekerjaan_status=5,6,7') ?>' + data).load();
})
    /* FROM CARI SUBMIT */

    /* FROM CARI SUBMIT */
$('#cari_ifa').on('click', function(e) {
      // alert('tes')
      // e.preventDefault();
  var data = $('#filter').serialize();
  $('#table_ifa').DataTable().ajax.url('<?= base_url('project/pekerjaan_usulan/getPekerjaanUsulan?klasifikasi_pekerjaan_id=1&pekerjaan_status=8,9,10') ?>' + data).load();
})
    /* FROM CARI SUBMIT */

    /* FROM CARI SUBMIT */
$('#cari_ifc').on('click', function(e) {
      // alert('tes')
      // e.preventDefault();
  var data = $('#filter').serialize();
  $('#table_ifc').DataTable().ajax.url('<?= base_url('project/pekerjaan_usulan/getPekerjaanUsulan?klasifikasi_pekerjaan_id=1&pekerjaan_status=11,12,13') ?>' + data).load();
})
    /* FROM CARI SUBMIT */

tinymce.init({
  selector: "textarea#pekerjaan_deskripsi",
  height: 300,
  plugins: [
    "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
    "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
    "save table directionality emoticons template paste "
    ],
  toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons",
  style_formats: [{
    title: 'Bold text',
    inline: 'b'
  },
  {
    title: 'Red text',
    inline: 'span',
    styles: {
      color: '#ff0000'
    }
  },
  {
    title: 'Red header',
    block: 'h1',
    styles: {
      color: '#ff0000'
    }
  },
  {
    title: 'Example 1',
    inline: 'span',
    classes: 'example1'
  },
  {
    title: 'Example 2',
    inline: 'span',
    classes: 'example2'
  },
  {
    title: 'Table styles'
  },
  {
    title: 'Table row 1',
    selector: 'tr',
    classes: 'tablerow1'
  }
  ]
});

function fun_ganti_cc_id(id){
  $('#cc_id').select2({
    dropdownParent: $('#modal_usulan'),
    ajax: {
      delay: 250,
      url: '<?= base_url('project/pekerjaan_usulan/getUserListVPAllDep?pegawai_id_dep=') ?>'+id,
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
  
}

    /* Fun Textarea */
function fun_textarea() {
  tinymce.init({
    selector: "textarea#pekerjaan_deskripsi",
    height: 300,
    plugins: [
      "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
      "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
      "save table contextmenu directionality emoticons template paste textcolor"
      ],
    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons",
    style_formats: [{
      title: 'Bold text',
      inline: 'b'
    },
    {
      title: 'Red text',
      inline: 'span',
      styles: {
        color: '#ff0000'
      }
    },
    {
      title: 'Red header',
      block: 'h1',
      styles: {
        color: '#ff0000'
      }
    },
    {
      title: 'Example 1',
      inline: 'span',
      classes: 'example1'
    },
    {
      title: 'Example 2',
      inline: 'span',
      classes: 'example2'
    },
    {
      title: 'Table styles'
    },
    {
      title: 'Table row 1',
      selector: 'tr',
      classes: 'tablerow1'
    }
    ]
  });
}
    /* Fun Textarea */

    /* Klik Tambah */
function fun_tambah_usulan() {
  $('#pekerjaan_id').val(Date.now());
  $('#jabatan_temp').val('<?= substr($session['pegawai_jabatan'], 0, 1) ?>');
  $('#modal_usulan').modal('show');

      // fun_textarea();

  setTimeout(function() {
    $('#dg_document').edatagrid({
      url: '<?= base_url('project/pekerjaan_usulan/getPekerjaanDokumen?id_pekerjaan=') ?>' + $('#pekerjaan_id').val(),
      saveUrl: '<?= base_url('project/pekerjaan_usulan/insertPekerjaanDokumenUsulan?') ?>',
      updateUrl: '<?= base_url('project/pekerjaan_usulan/updatePekerjaanDokumen?') ?>',
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
          field: 'pekerjaan_dokumen_nama',
          title: 'Judul Dokumen',
          width: '33%',
          editor: {
            type: 'textbox',
            options: {
              required: true,
              onchange: function(value) {
                console.log(value);
                $("#doc_nama").val(value);
              }
            }
          },
        }, {
          field: 'pekerjaan_dokumen_file',
          title: 'File',
          width: '33%',
          formatter: (value, row) => row.fileName || value,
          editor: {
            type: 'filebox',
            options: {
              required: true,
              accept: 'application/pdf',
              buttonText: '...',
              onChange: function() {
                var self = $(this);
                var files = self.filebox('files');
                var formData = new FormData();
                var nama = $("#doc_nama").val();
                self.filebox('setText', 'Menyimpan...');

                formData.append('id_pekerjaan', $('#pekerjaan_id').val());

                  for (var i = 0; i < files.length; i++) {
                    var file = files[i];
                    var extension = file.name.split('.').pop().toLowerCase();
                    
                    if (extension !== 'pdf') {
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
          width: '33%',
          formatter: function(value, row, index) {
            if (row.pekerjaan_dokumen_file) {
              return '<a href="#" onclick="viewFile(\'' + row.pekerjaan_dokumen_file + '\')">Lihat File</a>';
            } else {
              return value;
            }
          },

        },
        ],
        ],
    });
  }, 500);
}
    /* Klik Tambah */

    /* Klik Edit */
function fun_edit(id) {
  $('#modal_usulan').modal('show');
  $('#simpan').css('display', 'none');
  $('#edit').css('display', 'block');
      // $('#div_pekerjaan_note').show();

  fun_textarea();

  $.getJSON('<?= base_url() ?>project/pekerjaan_usulan/getPekerjaanUsulan?pekerjaan_id=' + id, function(json) {
    if (json.pekerjaan_status == '-') {
      $('#div_pekerjaan_note').show();
      $('#pekerjaan_note').val(json.pekerjaan_note);
    } else {
      $('#div_pekerjaan_note').hide();
    }

    if (json.pekerjaan_deskripsi) {
      tinymce.get("pekerjaan_deskripsi").setContent(json.pekerjaan_deskripsi);
    }
    $('#id_klasifikasi_pekerjaan').val(json.klasifikasi_pekerjaan_id);
    $('#klasifikasi_pekerjaan_nama').val(json.klasifikasi_pekerjaan_nama);
    $('#pekerjaan_id').val(json.pekerjaan_id);
    $('#jabatan_temp').val('<?= substr($session['pegawai_jabatan'], 0, 1) ?>');
    $('#pekerjaan_status').val('1');
    $('#pekerjaan_waktu').val(json.tanggal_awal);
    $('#pekerjaan_waktu_akhir').val(json.tanggal_akhir);
    $('#pic').val(json.pic);
    $('#pic_no_telp').val(json.pic_no_telp);
    $('#pekerjaan_judul').val(json.pekerjaan_judul);
    $('#pekerjaan_tahun').val(json.pekerjaan_tahun);

        /*SELECTED SELECT2*/
    $.getJSON('<?= base_url() ?>project/pekerjaan_usulan/getListUserById', {
      pegawai_nik: json.pekerjaan_reviewer
    }, function(jsonReviewer) {
      var newOption = new Option(jsonReviewer.text, jsonReviewer.id, true, true);
      $('#reviewer').append(newOption).trigger('change');
    });

    $.getJSON('<?= base_url() ?>project/pekerjaan_usulan/getListUserById', {
      pegawai_nik: json.pekerjaan_approver
    }, function(jsonApprover) {
      var newOption = new Option(jsonApprover.text, jsonApprover.id, true, true);
      $('#approver').append(newOption).trigger('change');
    });
        /*SELECTED SELECT2*/
    tinymce.get("pekerjaan_deskripsi").setContent(json.pekerjaan_deskripsi);
  });

  setTimeout(function() {
    $('#dg_document').edatagrid({
      url: '<?= base_url('project/pekerjaan_usulan/getPekerjaanDokumen?id_pekerjaan=') ?>' + id,
      saveUrl: '<?= base_url('project/pekerjaan_usulan/insertPekerjaanDokumenUsulan?') ?>',
      updateUrl: '<?= base_url('project/pekerjaan_usulan/updatePekerjaanDokumen?') ?>',
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
          field: 'pekerjaan_dokumen_nama',
          title: 'Judul Dokumen',
          width: '33%',
          editor: {
            type: 'textbox',
            options: {
              required: true,
              onchange: function(value) {
                console.log(value);
                $("#doc_nama").val(value);
              }
            }
          },
        }, {
          field: 'pekerjaan_dokumen_file',
          title: 'File',
          width: '33%',
          formatter: (value, row) => row.fileName || value,
          editor: {
            type: 'filebox',
            options: {
              required: true,
              accept: 'application/pdf',
              buttonText: '...',
              onChange: function() {
                var self = $(this);
                var files = self.filebox('files');
                var formData = new FormData();
                var nama = $("#doc_nama").val();
                self.filebox('setText', 'Menyimpan...');

                formData.append('id_pekerjaan', $('#pekerjaan_id').val());

                  for (var i = 0; i < files.length; i++) {
                    var file = files[i];
                    var extension = file.name.split('.').pop().toLowerCase();
                    
                    if (extension !== 'pdf') {
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
          width: '33%',
          formatter: function(value, row, index) {
            if (row.pekerjaan_dokumen_file) {
              return '<a href="#" onclick="viewFile(\'' + row.pekerjaan_dokumen_file + '\')">Lihat File</a>';
            } else {
              return value;
            }
          },

        },
        ],
        ],
    });
  }, 1500);
}
    /* Klik Edit */

    /* Proses  Draft*/
$('#simpan').on('click', function() {
  var isi_awal = $('#dg_document').data('datagrid').data.rows[0];
  if ($('#dg_document').data('datagrid').data.total > 0 && 'isNewRecord' in isi_awal) {
    $.messager.alert('Peringatan', 'Data Dokumen Masih Ada Yang Belum Tersimpan');
  } else {
    if ($('#pekerjaan_waktu').val() == '') {
      $('#pekerjaan_waktu_alert').show();
    } else {
      $('#pekerjaan_waktu_alert').hide();
    }
    if ($('#pic').val() == '') {
      $('#pic_alert').show();
    } else {
      $('#pic_alert').hide();
    }
    if ($('#pic_no_telp').val() == '') {
      $('#pic_no_telp_alert').show();
    } else {
      $('#pic_no_telp_alert').hide();
    }
    if ($('#pekerjaan_judul').val() == '') {
      $('#pekerjaan_judul_alert').show();
    } else {
      $('#pekerjaan_judul_alert').hide();
    }
        // cek form

    if ($('#pekerjaan_waktu').val() != '' && $('#pic').val() != '' && $('#pic_no_telp').val() != '' && $('#pekerjaan_judul').val() != '') {
      var pekerjaan_deskripsi = tinymce.get('pekerjaan_deskripsi').getContent();
      var data = $('#form_modal_usulan').serialize();
      data += '&pekerjaan_deskripsi=' + escape(pekerjaan_deskripsi);
      fun_simpan_document();
      $.ajax({
        url: '<?= base_url('project/pekerjaan_usulan/insertPekerjaan') ?>',
        data: data,
        type: 'POST',
        dataType: 'html',
        beforeSend: function() {
          $('#loading_form').css('display', 'block');
          $('#simpan').css('display', 'none');
          $('#send').css('display', 'none');
          $('#edit').css('display', 'none');
        },
        complete: function() {
          $('#loading_form').hide();
          $('#simpan').show();
          $('#send').show();
          $('#edit').hide();
        },
        success: function(isi) {
          $('#close').click();
          toastr.success('Berhasil');
        }
      });
    }
  }
});
    /* Proses Draft*/

    /* Proses  Send*/
$('#send').on('click', function() {
  var isi_awal = $('#dg_document').data('datagrid').data.rows[0];
  if ($('#dg_document').data('datagrid').data.total > 0 && 'isNewRecord' in isi_awal) {
    $.messager.alert('Peringatan', 'Data Dokumen Masih Ada Yang Belum Tersimpan');
  } else {
    if ($('#pekerjaan_waktu').val() == '') {
      $('#pekerjaan_waktu_alert').show();
    } else {
      $('#pekerjaan_waktu_alert').hide();
    }
    if ($('#pic').val() == '') {
      $('#pic_alert').show();
    } else {
      $('#pic_alert').hide();
    }
    if ($('#pic_no_telp').val() == '') {
      $('#pic_no_telp_alert').show();
    } else {
      $('#pic_no_telp_alert').hide();
    }
    if ($('#pekerjaan_judul').val() == '') {
      $('#pekerjaan_judul_alert').show();
    } else {
      $('#pekerjaan_judul_alert').hide();
    }
    if ($('#id_klasifikasi_pekerjaan').val() == null) {
      $('#id_klasifikasi_pekerjaan_alert').show();
    } else {
      $('#id_klasifikasi_pekerjaan_alert').hide();
    }
    if (tinymce.get('pekerjaan_deskripsi').getContent() == '') {
      $('#pekerjaan_deskripsi_alert').show();
    } else {
      $('#pekerjaan_deskripsi_alert').hide();
    }

    if ($('#pekerjaan_waktu').val() != '' && $('#pic').val() != '' && $('#pic_no_telp').val() != '' && $('#pekerjaan_judul').val() != '' && $('#id_klasifikasi_pekerjaan') != null && tinymce.get('pekerjaan_deskripsi').getContent() != '') {
      var pekerjaan_deskripsi = tinymce.get('pekerjaan_deskripsi').getContent();
      var data = $('#form_modal_usulan').serialize();
      data += '&pekerjaan_deskripsi=' + escape(pekerjaan_deskripsi);
      fun_simpan_document();
      $.ajax({
        url: '<?= base_url('project/pekerjaan_usulan/insertPekerjaanSend') ?>',
        data: data,
        type: 'POST',
        dataType: 'html',
        beforeSend: function() {
          $('#loading_form').css('display', 'block');
          $('#simpan').css('display', 'none');
          $('#send').css('display', 'none');
          $('#edit').css('display', 'none');
        },
        complete: function() {
          $('#loading_form').hide();
          $('#simpan').show();
          $('#send').show();
          $('#edit').hide();
        },
        success: function(isi) {
          $('#close').click();
          toastr.success('Berhasil');
        }
      });
    }
  }
});
    /* Proses Send*/

    /* Proses Update*/
$('#edit').on('click', function() {
  var isi_awal = $('#dg_document').data('datagrid').data.rows[0];
  if ($('#dg_document').data('datagrid').data.total > 0 && 'isNewRecord' in isi_awal) {
    $.messager.alert('Peringatan', 'Data Dokumen Masih Ada Yang Belum Tersimpan');
  } else {
    if ($('#pekerjaan_waktu').val() == '') {
      $('#pekerjaan_waktu_alert').show();
    } else {
      $('#pekerjaan_waktu_alert').hide();
    }
    if ($('#pic').val() == '') {
      $('#pic_alert').show();
    } else {
      $('#pic_alert').hide();
    }
    if ($('#pic_no_telp').val() == '') {
      $('#pic_no_telp_alert').show();
    } else {
      $('#pic_no_telp_alert').hide();
    }
    if ($('#pekerjaan_judul').val() == '') {
      $('#pekerjaan_judul_alert').show();
    } else {
      $('#pekerjaan_judul_alert').hide();
    }

    if ($('#pekerjaan_waktu').val() != '' && $('#pic').val() != '' && $('#pic_no_telp').val() != '' && $('#pekerjaan_judul').val() != '') {
      var pekerjaan_deskripsi = tinymce.get('pekerjaan_deskripsi').getContent();
      var data = $('#form_modal_usulan').serialize();
      data += '&pekerjaan_deskripsi=' + escape(pekerjaan_deskripsi);
      $.ajax({
        url: '<?= base_url('project/pekerjaan_usulan/updatePekerjaan') ?>',
        data: data,
        type: 'POST',
        dataType: 'html',
        beforeSend: function() {
          $('#loading_form').css('display', 'block');
          $('#simpan').css('display', 'none');
          $('#edit').css('display', 'none');
        },
        complete: function() {
          $('#loading_form').hide();
          $('#simpan').hide();
          $('#edit').show();
        },
        success: function(isi) {
          $('#close').click();
          toastr.success('Berhasil');
        }
      });
    }
  }
});
    /* Proses Update*/

    /* Klik Detail */
function fun_detail(id, val) {
  call_ajax_page('project/pekerjaan_usulan/detailPekerjaan?aksi=' + $('#pekerjaan_status_nama').val() + '&pekerjaan_id=' + id + '&status=' + val + '&rkap=1');
}

function fun_detail_disposisi(id, val) {
  call_ajax_page('project/pekerjaan_usulan/detailPekerjaan?aksi=' + $('#pekerjaan_status_nama').val() + '&pekerjaan_id=' + id + '&status=' + val + '&rkap=1');
}
    /* Klik Detail */

    /* Fun Delete */
function fun_delete(id) {
  Swal.fire({
    title: 'Apakah anda yakin akan menghapusnya?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Hapus!'
  }).then((result) => {
    if (result.isConfirmed) {
      $.get('<?= base_url() ?>project/pekerjaan_usulan/deletePekerjaan', {
        pekerjaan_id: id
      }, function(data) {
        $('#close').click();
        toastr.success('Berhasil');
      });
    }
  })
}
    /* Fun Delete */

    /* EASYUI */
    /* Fun Tambah */
function fun_tambah_document() {
  var isi_awal = $('#dg_document').data('datagrid').data.rows[0];
  if ($('#dg_document').data('datagrid').data.total > 0 && 'isNewRecord' in isi_awal) {
    $.messager.alert('Peringatan', 'Data Dokumen Masih Ada Yang Belum Tersimpan');
  } else {
    $('#dg_document').edatagrid('addRow', {
      index: 0,
      row: {
        pekerjaan_id: $('#pekerjaan_id').val(),
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
    /* EASYUI */

    /* Close */
$('#modal_usulan').on('hidden.bs.modal', function(e) {
  fun_close_usulan();
});

function fun_close_usulan() {
  fun_loading();
  $('#table_usulan').DataTable().ajax.reload(null, false);
  $('#simpan').css('display', 'block');
  $('#edit').css('display', 'none');
  $('#div_pekerjaan_note').hide();
  $('#form_modal_usulan')[0].reset();
  $('#modal_usulan').modal('hide');
  $('#id_klasifikasi_pekerjaan').empty();
      // tinymce.remove('#pekerjaan_deskripsi');
      // alert
  $('#pekerjaan_waktu_alert').hide();
  $('#pic_alert').hide();
  $('#pic_no_telp_alert').hide();
  $('#pekerjaan_judul_alert').hide();
}

function fun_close_ifa() {
  $('#simpan_ajuan_extend').css('display', 'block');
  $('#simpan_extend').css('display', 'block');
  $('#edit_ajuan_extend').css('display', 'none');
  $('#edit_extend').css('display', 'none');
  $('#table_data').css('display', 'none');
  $('#tableDiv').css('display', 'none');
  $('#formDiv').css('display', 'block');
  $('#form_modal_ajuan_extend')[0].reset();
  $('#form_modal_extend')[0].reset();
  $('#modal_ajuan_extend').modal('hide');
  $('#modal_extend').modal('hide');
  $('#table').DataTable().ajax.reload(null, false);
}
    /* Close */

    /* Loading */
function fun_loading() {
  var simplebar = new Nanobar();
  simplebar.go(100);
}
    /* Loading */

    // start IFA (tambah)
    // AJUAN EXTEND
function fun_ajuan_extend(id, status) {
  $('#modal_ajuan_extend').modal('show');
  $('#id_pekerjaan_ajuan_extend').val(id);
  $('#pekerjaan_status_ajuan_extend').val(status);
  $.getJSON('<?= base_url('project/Pekerjaan_usulan/getExtend') ?>', {
    id_pekerjaan: id,
    pekerjaan_disposisi_status: status,
        // extend_status: 'y'
  }, function(json, result) {
    $('#extend_id_ajuan_extend').val(json.extend_id);
    $('#pekerjaan_waktu_ajuan_extend').val(json.extend_hari);
    if (json.extend_id != null) {
      $('#edit_ajuan_extend').show();
      $('#simpan_ajuan_extend').hide();
    } else {
      $('#edit_ajuan_extend').hide();
      $('#simpan_ajuan_extend').show();
    }
  })

}

$('#modal_ajuan_extend').on('submit', function(e) {
  if ($('#extend_id_ajuan_extend').val() != '')
    var url = '<?= base_url('project/Pekerjaan_usulan/updateAjuanExtend') ?>';
  else var url = '<?= base_url('project/Pekerjaan_usulan/insertAjuanExtend') ?>';

  var data = new FormData();

  data.append('id_pekerjaan', $('#id_pekerjaan_ajuan_extend').val());
  data.append('extend_id', $('#extend_id_ajuan_extend').val());
  data.append('pekerjaan_disposisi_status', $('#pekerjaan_status_ajuan_extend').val());
  data.append('extend_hari', $('#pekerjaan_waktu_ajuan_extend').val());
  data.append('extend_status', 'y');

  console.log(data);

  e.preventDefault();
  $.ajax({
    url: url,
    type: 'post',
    data: data,
    dataType: 'HTML',
    processData: false,
    contentType: false,
    beforeSend: function() {
      $('#loading_form').show();
      $('#simpan_ajuan_extend').hide();
      $('#edit_ajuan_extend').hide();
    },
    complete: function() {
      $('#loading_form').hide();
      $('#simpan_ajuan_extend').show();
      $('#edit_ajuan_extend').hide();
    },
    success: function(data) {
      $('#close_extend').click();
    }
  });
})
    // AJUAN EXTEND

    // EXTEND
function fun_extend(id, status) {
  $('#modal_extend').modal('show');
  $('#id_pekerjaan_extend').val(id);
  $('#pekerjaan_status_extend').val(status);
  $.getJSON('<?= base_url('project/Pekerjaan_usulan/getExtend') ?>', {
    id_pekerjaan: id,
    pekerjaan_disposisi_status: status,
        // extend_status: '1'
  }, function(json, result) {
    $('#extend_id_extend').val(json.extend_id);
    $('#pekerjaan_waktu_extend').val(json.extend_hari);
    if (json.extend_id != null) {
      $('#edit_extend').show();
      $('#simpan_extend').hide();
    } else {
      $('#edit_extend').hide();
      $('#simpan_extend').show();
    }
  })
}

$('#modal_extend').on('submit', function(e) {
  if ($('#extend_id_extend').val() != '')
    var url = '<?= base_url('project/Pekerjaan_usulan/updateAjuanExtend') ?>';
  else var url = '<?= base_url('project/Pekerjaan_usulan/insertAjuanExtend') ?>';

  var data = new FormData();

  data.append('id_pekerjaan', $('#id_pekerjaan_extend').val());
  data.append('extend_id', $('#extend_id_extend').val());
  data.append('pekerjaan_disposisi_status', $('#pekerjaan_status_extend').val());
  data.append('extend_hari', $('#pekerjaan_waktu_extend').val());
  data.append('extend_status', 'n');

  console.log(data);

  e.preventDefault();
  $.ajax({
    url: url,
    type: 'post',
    data: data,
    dataType: 'HTML',
    processData: false,
    contentType: false,
    beforeSend: function() {
      $('#loading_form').show();
      $('#simpan_extend').hide();
      $('#edit_extend').hide();
    },
    complete: function() {
      $('#loading_form').hide();
      $('#simpan_extend').show();
      $('#edit_extend').hide();
    },
    success: function(data) {
      $('#close_extend').click();
    }
  });
})
    /* Zero Padding */
function pad(str, max) {
  str = str.toString();
  return str.length < max ? pad("0" + str, max) : str;
}
    /* Zero Padding */

function viewFile(fileName) {
  window.open('<?= base_url('document') ?>/' + fileName, '_blank');
}
</script>