<link rel="stylesheet" type="text/css" href="<?= base_url('assets_tambahan/') ?>easyui/themes/default/easyui.css">
<link rel="stylesheet" type="text/css" href="<?= base_url('assets_tambahan/') ?>easyui/themes/icon.css">
<style>
    .dataTables_scrollHeadInner,
    .table {
        width: 100% !important;
    }
</style>
<div class="page-content">

    <?php
    $data_session = $this->session->userdata();
    $dataAtasan = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_poscode = '" . $data_session['pegawai_direct_superior'] . "'")->row_array();
    ?>

    <?php $data_session = $this->session->userdata(); ?>
    <!-- SQL Klasifikasi -->
    <?php $klasifikasi = $this->db->query("SELECT * FROM global.global_klasifikasi_pekerjaan a WHERE klasifikasi_pekerjaan_id = '2' ")->row_array(); ?>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-2">Pekerjaan NON RKAP</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <!-- Start Filter Pekerjaan -->
        <div class="row" id="div_filter" style="display:none">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Filter Pekerjaan</h4>
                        <form id="filter">
                            <div class="row">
                                <div class="form-group col-md-5">
                                    <label>Perencana</label>
                                    <select class="form-control select2" id="id_user_cari" name="id_user_cari">

                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>&emsp;</label>
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
                            <input hidden type="text" id="pekerjaan_status_nama" name="pekerjaan_status_nama" value="usulan">
                            <input hidden type="text" name="session_direct_superior" id="session_direct_superior" value="<?= $data_session['pegawai_direct_superior'] ?>">
                            <input hidden type="text" name="session_poscode" id="session_poscode" value="<?= $data_session['pegawai_poscode'] ?>">
                            <input hidden type="text" name="session_nik" id="session_nik" value="<?= $data_session['pegawai_nik'] ?>">
                            <input type="text" name="pegawai_jabatan" id="pegawai_jabatan" value="<?= substr($data_session['pegawai_jabatan'], 0, 1) ?>" style="display:none;">
                            <input type="text" name="jabatan_atasan" id="jabatan_atasan" value="<?= (!isset($dataAtasan)) ? '' : substr($dataAtasan['pegawai_jabatan'], 0, 1) ?>" style="display:none;">
                            <input type="text" name="nama_atasan" id="nama_atasan" value="<?= (!isset($dataAtasan)) ? '' : $dataAtasan['pegawai_nama'] ?>" style="display:none;">
                            <input type="text" name="nik_atasan" id="nik_atasan" value="<?= (!isset($dataAtasan)) ? '' : $dataAtasan['pegawai_nik'] ?>" style="display:none;">
                            <input type="text" name="postitle_atasan" id="postitle_atasan" value="<?= (!isset($dataAtasan)) ? '' : $dataAtasan['pegawai_postitle'] ?>" style="display:none;">
                            <input type="text" name="direct_superior_atasan" id="direct_superior_atasan" value="<?= (!isset($dataAtasan)) ? '' : $dataAtasan['pegawai_direct_superior'] ?>" style="display:none;">
                            <li class="nav-item">
                                <a class="nav-link active" href="#usulan" onclick="div_usulan()" id="link_div_usulan">Usulan <input type="text" id="notif_usulan_reject_non_rkap" hidden><span class="badge bg-primary float-end" id="notif_usulan_non_rkap"></span> </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#berjalan" onclick="div_berjalan()" id="link_div_berjalan">Berjalan <span class="badge bg-secondary float-end" id="notif_berjalan_non_rkap"></span> </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#ifa" onclick="div_ifa()" id="link_div_ifa">IFA <span class="badge bg-success float-end" id="notif_ifa_non_rkap"></span> </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#ifc" onclick="div_ifc()" id="link_div_ifc">IFC <span class="badge bg-warning float-end" id="notif_ifc_non_rkap"></span> </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#selesai" onclick="div_selesai()" id="link_div_selesai">Selesai <span class="badge bg-dark float-end" id="notif_selesai_non_rkap"></span> </a>
                            </li>
                        </ul>
                    </div>
                    <!-- End Tab -->
                    <!-- start card usulan -->
                    <div class="card-body" id="div_usulan">
                        <div>
                            <button type="button" class="btn btn-primary float-end" data-toggle="modal" data-target="#myModal" onclick="fun_tambah_usulan()">Tambah</button>
                            <h4 class="card-title mb-4">Pekerjaan Usulan</h4>
                        </div>
                        <table id="table_usulan" class="table table-bordered table-striped " width="100%">
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
                                    <th style="text-align: center;" rowspan="2">Nama Kegiatan</th>
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
                    <!-- start card ifa -->
                    <div class="card-body" id="div_ifa" style="display:none">
                        <h4 class="card-title mb-4">Dokumen IFA</h4>
                        <input type="text" name="user_session" id="user_session" hidden>
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
                        <table id="table_ifc" class="table table-bordered table-striped " width="100%">
                            <thead class="table-primary">
                                <tr>
                                    <th>No Pekerjaan</th>
                                    <th>Waktu Pekerjaan</th>
                                    <th>Nama Pekerjaan</th>
                                    <th>Status</th>
                                    <th>Detail</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <!-- end card ifc -->
                    <!-- start card selesai -->
                    <div class="card-body" id="div_selesai" style="display:none">
                        <table id="table_selesai" class="table table-bordered table-striped" width="100%">
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
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <!-- end card selesai -->
                </div>
            </div>
        </div>
        <!-- end row -->

    </div> <!-- container-fluid -->

    <!-- MODAL -->
    <!-- start modal usulan -->
    <div class="modal fade" id="modal_usulan" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg  modal-dialog-scrollable">
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
                                <input type="date" name="pekerjaan_waktu" id="pekerjaan_waktu" class="form-control col-md-8" required value="<?php echo date('Y-m-d') ?>" readonly>
                                <label style="color:red;display:none" id="pekerjaan_waktu_alert">Waktu Pekerjaan Tidak Boleh Kosong</label>
                            </div>
                        </div>
                        <div class="card-body row">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">Target Selesai Pekerjaan</label>
                                <input type="date" name="pekerjaan_waktu_akhir" id="pekerjaan_waktu_akhir" class="form-control col-md-8" required value="<?php echo date('Y-m-d') ?>">
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
                                    <option>Pilih Reviewer</option>
                                </select>
                                <label style="color:red;display:none" id="pic_alert">PIC Tidak Boleh Kosong</label>
                            </div>
                        </div>
                        <div class="card-body row" id="div_approver">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">Approver</label>
                                <select class="form-control" id="approver" name="approver">
                                    <option>Pilih Approver</option>
                                </select>
                                <label style="color:red;display:none" id="pic_alert">PIC Tidak Boleh Kosong</label>
                            </div>
                        </div>
                        <div class="card-body row">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">Klasifikasi Pekerjaan *</label>
                                <input type="text" name="id_klasifikasi_pekerjaan" id="id_klasifikasi_pekerjaan" class="form-control" value="<?= $klasifikasi['klasifikasi_pekerjaan_id'] ?>" hidden="hidden">
                                <input type=" text" name="klasifikasi_pekerjaan_nama" id="klasifikasi_pekerjaan_nama" class="form-control" value="<?= $klasifikasi['klasifikasi_pekerjaan_nama'] ?>" readonly>
                                <label style="color:red;display:none" id="id_klasifikasi_pekerjaan_alert">Klasifikasi Pekerjaan Tidak Boleh Kosong</label>
                            </div>
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
                                <input type="text" name="pekerjaan_tahun" id="pekerjaan_tahun" class="form-control col-md-8" maxlength="4" onkeypress="return numberOnly(event)" value="<?= date('Y') ?>">
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
                                <label class="col-md-4">Upload Dokumen</label>
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
                            <input type="hidden" name="id_pekerjaan_ajuan_extend" id="id_pekerjaan_ajuan_extend">
                            <div class="card-body row">
                                <div class="form-group row col-md-12">
                                    <label class="col-md-4">Ajuan Extend</label>
                                    <input type="number" name="pekerjaan_waktu_ajuan_extend" id="pekerjaan_waktu_ajuan_extend" class="form-control col-md-8">
                                </div>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" id="close_ajuan_extend" class="btn btn-default" data-dismiss="modal" onclick="fun_close_ifa()">Close</button>
                                <input type="submit" class="btn btn-success pull-right" id="simpan_ajuan_extend" value="Simpan">
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
                                    <label class="col-md-4">Waktu Extend</label>
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
            $('#div_filter').css('display', 'none');
            $('#div_usulan').css('display', 'block');
            $('#div_berjalan').css('display', 'none');
            $('#div_ifa').css('display', 'none');
            $('#div_ifc').css('display', 'none');
            $('#div_selesai').css('display', 'none');
            $('#link_div_usulan').addClass('active');
            $('#link_div_berjalan').removeClass('active');
            $('#link_div_ifa').removeClass('active');
            $('#link_div_ifc').removeClass('active');
            $('#link_div_selesai').removeClass('active');
            $('#pekerjaan_status_nama').val('usulan');
        }
        /* Klik Tab usulan */
        /* Klik Tab berjalan */
        function div_berjalan() {
            $('#div_filter').css('display', 'block');
            $('#cari_berjalan').show();
            $('#cari_ifa').hide();
            $('#cari_ifc').hide();
            $('#div_usulan').css('display', 'none');
            $('#div_berjalan').css('display', 'block');
            $('#div_ifa').css('display', 'none');
            $('#div_ifc').css('display', 'none');
            $('#div_selesai').css('display', 'none');
            $('#link_div_usulan').removeClass('active');
            $('#link_div_berjalan').addClass('active');
            $('#link_div_ifa').removeClass('active');
            $('#link_div_ifc').removeClass('active');
            $('#link_div_selesai').removeClass('active');
            $('#pekerjaan_status_nama').val('berjalan');
        }
        /* Klik Tab berjalan */
        /* Klik Tab IFA */
        function div_ifa() {
            $('#div_filter').css('display', 'block');
            $('#cari_berjalan').hide();
            $('#cari_ifa').show();
            $('#cari_ifc').hide();
            $('#div_usulan').css('display', 'none');
            $('#div_berjalan').css('display', 'none');
            $('#div_berjalan').css('display', 'none');
            $('#div_berjalan').css('display', 'none');
            $('#div_ifa').css('display', 'block');
            $('#div_ifc').css('display', 'none');
            $('#div_selesai').css('display', 'none');
            $('#link_div_usulan').removeClass('active');
            $('#link_div_berjalan').removeClass('active');
            $('#link_div_ifa').addClass('active');
            $('#link_div_ifc').removeClass('active');
            $('#link_div_selesai').removeClass('active');
            $('#pekerjaan_status_nama').val('ifa');
        }
        /* Klik Tab IFA */
        /* Klik Tab IFC */
        function div_ifc() {
            $('#div_filter').css('display', 'block');
            $('#cari_berjalan').hide();
            $('#cari_ifa').hide();
            $('#cari_ifc').show();
            $('#div_usulan').css('display', 'none');
            $('#div_berjalan').css('display', 'none');
            $('#div_ifa').css('display', 'none');
            $('#div_ifc').css('display', 'block');
            $('#div_selesai').css('display', 'none');
            $('#link_div_usulan').removeClass('active');
            $('#link_div_berjalan').removeClass('active');
            $('#link_div_ifa').removeClass('active');
            $('#link_div_ifc').addClass('active');
            $('#link_div_selesai').removeClass('active');
            $('#pekerjaan_status_nama').val('ifc');
        }
        /* Klik Tab IFC */
        /* Klik Tab selesai */
        function div_selesai() {
            $('#div_usulan').css('display', 'none');
            $('#div_berjalan').css('display', 'none');
            $('#div_ifa').css('display', 'none');
            $('#div_ifc').css('display', 'none');
            $('#div_selesai').css('display', 'block');
            $('#link_div_usulan').removeClass('active');
            $('#link_div_berjalan').removeClass('active');
            $('#link_div_ifa').removeClass('active');
            $('#link_div_ifc').removeClass('active');
            $('#link_div_selesai').addClass('active');
            $('#pekerjaan_status_nama').val('selesai');
        }
        /* Klik Tab selesai */
        /* TAB */


        $(function() {
            $('#reviewer').select2({
                dropdownParent: $('#modal_usulan'),
                placeholder: 'Pilih',
                ajax: {
                    delay: 250,
                    url: '<?= base_url('project/RKAP/getUserListRevApp') ?>',
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


            $('#approver').select2({
                dropdownParent: $('#modal_usulan'),
                placeholder: 'Pilih',
                ajax: {
                    delay: 250,
                    url: '<?= base_url('project/RKAP/getUserListRevApp') ?>',
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
                    $.getJSON('<?= base_url() ?>project/RKAP/getUserListRevApp2', {
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
                    "url": "<?= base_url() ?>project/pekerjaan_usulan/getPekerjaanUsulan?klasifikasi_pekerjaan_id_non_rkap=1&pekerjaan_status=-,0,1,2,3,4",
                    "dataSrc": ""
                },
                "columns": [{
                        render: function(data, type, full, meta) {
                            var urut = '';
                            if (full.milik == 'y') {
                                return '<span class="badge" style="background-color:#c13333 ">' + (meta.row + meta.settings._iDisplayStart + 1) + '</span>'
                            } else {
                                return '<span class="badge" style="background-color:none; color: black">' + (meta.row + meta.settings._iDisplayStart + 1) + '</span>'
                                // return meta.row + meta.settings._iDisplayStart + 1;
                            }
                            // return meta.row + meta.settings._iDisplayStart + 1;
                            // return urut;
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
                            // var nomor = '';
                            // if (full.milik == 'y' && full.pekerjaan_nomor == null) {
                            // nomor = '';
                            // } else if (full.milik == 'y') {
                            // nomor = '<span class="badge" style="background-color:#c13333 ">' + full.pekerjaan_nomor + '</span>';
                            // } else {
                            // nomor = full.pekerjaan_nomor;
                            // }
                            // return nomor;
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
                                status = 'Menunggu Approval VP Rancang Bangun'
                            } else if (full.pekerjaan_status == 4) {
                                warna = '#0080FF';
                                status = 'Approved by VP Cangun'
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
                                status = 'IFA'
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

                            return '<span class="lead"><span class="badge" style="background-color: ' + warna + ';color:black  ">' + status + '</span></span>';
                        }
                    },
                    {
                        "render": function(data, type, full, meta) {
                            // console.log(full);
                            if (full.pekerjaan_status == '0') return '<center>-</center>';
                            // jika staf
                            // else if (($('#session_poscode').val() == 'E53110041A' || $('#session_poscode').val() == 'E53210051A' || $('#session_poscode').val() == 'E53310041A' || $('#session_poscode').val() == 'E53410041A' || $('#session_poscode').val() == 'E53410041B' || $('#session_poscode').val() == 'E53410051A' || $('#session_poscode').val() == 'E53500031B' || $('#session_poscode').val() == 'E53600031A' || $('#session_poscode').val() == 'E53600060B')) return '<center><a href="javascript:void(0);" id="' + full.pekerjaan_id + '" name="' + full.pekerjaan_status + '" title="Detail" onclick="fun_detail(this.id,this.name)"><i style="; color:white" class="btn btn-info btn-sm" >Detail</i></a></center>';
                            // jika pekerjaan 1 dan avp

                            // START BUTTON PEKERJAAN STATUS 1
                            // <?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=') ?>"+$('#pekerjaan_status_nama').val()+'&pekerjaan_id='+id+'&status='+val+'&rkap=0
                            else if (full.pekerjaan_status == '1' && $('#session_direct_superior').val() == 'E31600000')
                                return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=usulan') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=0"  title="Review" ><i  color:white" class="btn btn-primary btn-sm" >Detail</i></a></center>';
                            else if (full.pekerjaan_status == '1' && $('#session_direct_superior').val() == 'E31000000')
                                return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=usulan') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=0" title="Review" ><i style="background-color:orange; color:white" class="btn btn-warning btn-sm" >Review</i></a></center>';
                            // FINISH BUTTON PEKERJAAN STATUS 1

                            // START BUTTON PEKERJAAN STATUS 2
                            else if (full.pekerjaan_status == '2' && $('#session_direct_superior').val() != 'E30000000')
                                return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=usulan') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=0"  title="Review" ><i  color:white" class="btn btn-primary btn-sm" >Detail</i></a></center>';
                            else if (full.pekerjaan_status == '2' && $('#session_direct_superior').val() == 'E30000000')
                                return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=usulan') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=0"  title="Review" ><i style="background-color:red; color:white" class="btn btn-danger btn-sm" >Approve</i></a></center>';
                            // FINISH BUTTON PEKERJAAN STATUS 2

                            // START BUTTON PEKERJAAN STATUS 3
                            else if (full.pekerjaan_status == '3' && $('#session_poscode').val() == 'E53000000') return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=usulan') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=0"  title="Approve" ><i style="background-color:red; color:white" class="btn btn-warning btn-sm" >Approve</i></a></center>';
                            else if (full.pekerjaan_status == '3' && $('#session_poscode').val() != 'E53000000') return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=usulan') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=0"  title="Approve" ><i class="btn btn-info btn-sm" >Detail</i></a></center>';
                            // FINISH BUTTON PEKERJAAN STATUS 3

                            // START BUTTON PEKERJAAN STATUS 4
                            else if ((full.pekerjaan_status == '4' && full.is_proses == 'y' && $('#session_nik').val() != '2125401'))
                                return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=usulan') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=0"  title="Review" ><i  color:white" class="btn btn-primary btn-sm" >Detail</i></a></center>';
                            else if (full.is_disposisi_aktif == 'n' && full.is_proses == 'n' && full.pekerjaan_status == '4')
                                return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=usulan') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=0"  title="Detail" ><i class="btn btn-info btn-sm" >Detail</i></a></center>';
                            else if ((full.pekerjaan_status == '4' && full.is_proses == 'n' && $('#session_poscode').val() == 'E53000000'))
                                return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=usulan') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=0"  title="Review" ><i  color:white" class="btn btn-primary btn-sm" >Detail</i></a></center>';
                            else if ((full.pekerjaan_status == '4' && full.is_proses == 'n' && $('#session_direct_superior').val() == 'E31000000'))
                                return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=usulan') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=0"  title="Review" ><i  color:white" class="btn btn-primary btn-sm" >Detail</i></a></center>';
                            else if ((full.pekerjaan_status == '4' && full.is_proses == 'n' && $('#session_nik').val() != '2125401'))
                                return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=usulan') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=0"  title="Review" ><i style="background-color:orange; color:white" class="btn btn-warning btn-sm" >Review</i></a></center>';
                            // FINISH BUTTON PEKERJAAN STATUS 4

                            // START JIKA SELAIN STATUS DIATAS
                            else return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=usulan') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=0"  title="Detail" ><i class="btn btn-info btn-sm" >Detail</i></a></center>';
                            // FINISH JIKA SELAIN STATUS DIATAS

                        }
                    },
                    {
                        "render": function(data, type, full, meta) {
                            return ((full.pekerjaan_status == '0' || full.pekerjaan_status == '-') && full.pic == $('#session_nik').val()) ? '<center><a href="javascript:void(0);" id="' + full.pekerjaan_id + '" title="Edit" onclick="fun_edit(this.id)"><i class="btn btn-success btn-sm" >Edit</i></a></center>' : '<center>-</center>';
                        }
                    },
                    {
                        "render": function(data, type, full, meta) {
                            return ((full.pekerjaan_status == '0' || full.pekerjaan_status == '-') && full.pic == $('#session_nik').val()) ? '<center><a href="javascript:void(0);" id="' + full.pekerjaan_id + '" title="Delete" onclick="fun_delete(this.id)"><i class="btn btn-danger btn-sm">Delete</i></a></center>' : '<center>-</center>';
                        }
                    },
                ]
            }).columns.adjust();
            /*End Isi Table Usulan */

            // Start Isi Table Berjalan
            $('#table_berjalan').DataTable({
                // "scrollX": true,
                // "fixedHeader": true,
                "initComplete": function(settings, json) {
                    $("#table_berjalan").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
                },
                "ajax": {
                    "url": "<?= base_url('project/') ?>pekerjaan_usulan/getPekerjaanBerjalan?klasifikasi_pekerjaan_id_non_rkap=1&pekerjaan_status=5,6,7",
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
                            var nomor = full.pekerjaan_nomor.split('-');
                            nomor[0] = pad(nomor[0], 3);
                            if (full.milik == 'y' && full.pekerjaan_nomor == null) {
                                nomor_isi = '';
                            } else if (full.milik == 'y' && <?= $data_session['pegawai_nik'] ?> != '2190626') {
                                nomor_isi = '<span class="badge" style="background-color:#c13333 ">' + nomor.join('-') + '</span>';
                            } else {
                                nomor_isi = nomor.join('-');
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
                    <?php if ($this->session->userdata('pegawai_id_dep') != 'E53000') : ?> {
                            "render": function(data, type, full, meta) {
                                var status = '';
                                var warna = '';
                                var font = 'black';

                                warna = '#CC6600';
                                status = 'In Progress'

                                return '<span class="lead"><span class="badge" style="background-color: ' + warna + ';color:' + font + '  ">' + status + '</span></span>';
                            }
                        },
                    <?php else : ?> {
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
                                    status = 'Men unggu Approval VP';
                                } else if (full.pekerjaan_status == 3) {
                                    warna = '#00FF00';
                                    status = 'Menunggu Approval VP Rancang Bangun'
                                } else if (full.pekerjaan_status == 4) {
                                    warna = '#0080FF';
                                    status = 'Approved VP Cangun'
                                } else if (full.pekerjaan_status == 5 && full.status_avp == '0') {
                                    warna = '#CC6600';
                                    status = 'In Progress'
                                } else if (full.pekerjaan_status == 6 || full.status_avp == '1') {
                                    warna = 'blue';
                                    font = 'white';
                                    status = 'Send IFA AVP'
                                } else if (full.pekerjaan_status == 7) {
                                    warna = 'red';
                                    font = 'white';
                                    status = 'Send IFA VP'
                                } else if (full.pekerjaan_status == 8) {
                                    warna = '#FF8000';
                                    status = 'IFA'
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
                    <?php endif; ?> {
                        "data": "pekerjaan_progress"
                    },
                    {
                        "render": function(data, type, full, meta) {
                            return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=berjalan') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=0" title="Detail" ><i class="btn btn-info btn-sm" >Detail</i></a></center>';
                        }
                    },
                    {
                        render: function(data, type, full, meta) {
                            return full.pekerjaan_isi_proses;
                        }
                    },
                    {
                        render: function(data, type, full, meta) {
                            return full.pekerjaan_isi_mesin;
                        }
                    },
                    {
                        render: function(data, type, full, meta) {
                            return full.pekerjaan_isi_listrik;
                        }
                    },
                    {
                        render: function(data, type, full, meta) {
                            return full.pekerjaan_isi_instrumen;
                        }
                    },
                    {
                        render: function(data, type, full, meta) {
                            return full.pekerjaan_isi_sipil;
                        }
                    },
                    {
                        "data": "total"
                    },
                    {
                        "data": "tanggal_start"
                    },
                    {
                        render: function(data, type, full, meta) {
                            return (full.tanggal_akhir < '<?= date('Y-m-d') ?>') ? '<span class="badge" style="background-color:#c13333;font-size:9pt" >' + full.tanggal_akhir + '</span>' : full.tanggal_akhir;
                        }
                    },

                ]
            });
            // End isi Table Berjalan

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
                    "url": "<?= base_url('project/') ?>pekerjaan_usulan/getPekerjaanUsulan?klasifikasi_pekerjaan_id_non_rkap=1&pekerjaan_status=8",
                    "dataSrc": ""
                },
                "columns": [{
                        render: function(data, type, full, meta) {
                            var nomor_isi = '';
                            var nomor = full.pekerjaan_nomor.split('-');
                            nomor[0] = pad(nomor[0], 3);
                            if (full.milik == 'y' && full.pekerjaan_nomor == null) {
                                nomor_isi = '';
                            } else if (full.milik == 'y') {
                                nomor_isi = '<span class="badge" style="background-color:#c13333 ">' + nomor.join('-') + '</span>';
                            } else {
                                nomor_isi = nomor.join('-');
                            }
                            return nomor_isi;
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
                                warna = '#B266FF';
                                status = 'IFC';
                            } else if (full.pekerjaan_status == 10) {
                                warna = '#B266FF';
                                status = 'IFC';
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
                    {
                        "render": function(data, type, full, meta) {
                            if (full.pic == $('#user_session').val() && full.extend_ajuan_tanggal == '') return '<center><a href="javascript:void(0);" id="' + full.pekerjaan_id + '" name="' + full.pekerjaan_status + '" title="Ajuan Extend" onclick="fun_ajuan_extend(this.id,this.name)"><i class="fas fa-share" data-toggle="modal" data-target="#modal_ajuan_extend"></i></a></center>';
                            else return (full.extend_ajuan_tanggal == '') ? '<center>-</center>' : '<center>' + full.extend_ajuan_tanggal.split("-").reverse().join("-") + '</center>';
                        }
                    },
                    {
                        "render": function(data, type, full, meta) {
                            if ($('#user_session').val() == '2190626' && (full.extend_status != '1')) return '<center><a href="javascript:void(0);" id="' + full.pekerjaan_id + '" name="' + full.pekerjaan_status + '" title="Extend" onclick="fun_extend(this.id,this.name)"><i class="fas fa-share" data-toggle="modal" data-target="#modal_extend"></i></a></center>';
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
                    "url": "<?= base_url() ?>project/pekerjaan_usulan/getPekerjaanUsulan?klasifikasi_pekerjaan_id_non_rkap=1&pekerjaan_status=9,10,11",
                    "dataSrc": ""
                },
                "columns": [{
                        render: function(data, type, full, meta) {
                            var nomor_isi = '';
                            var nomor = full.pekerjaan_nomor.split('-');
                            nomor[0] = pad(nomor[0], 3);
                            if (full.milik == 'y' && full.pekerjaan_nomor == null) {
                                nomor_isi = '';
                            } else if (full.milik == 'y') {
                                nomor_isi = '<span class="badge" style="background-color:#c13333 ">' + nomor.join('-') + '</span>';
                            } else {
                                nomor_isi = nomor.join('-');
                            }
                            return nomor_isi;
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
                            } else if (full.pekerjaan_status == 8) {
                                warna = '#FF8000';
                                status = 'IFA';
                            } else if (full.pekerjaan_status == 9) {
                                warna = '#B266FF';
                                status = 'IFA';
                            } else if (full.pekerjaan_status == 10) {
                                warna = '#B266FF';
                                status = 'IFA';
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
                            } else if (full.pekerjaan_status == '-') {
                                warna = '#FF0000';
                                status = 'Reject'
                            }

                            return '<span class="lead"><span class="badge" style="background-color: ' + warna + ';color:black  ">' + status + '</span></span>';
                        }
                    },
                    {
                        "render": function(data, type, full, meta) {
                            return '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=ifc') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=0" title="Detail" ><i class="btn btn-info btn-sm">Detail</i></a></center>';
                        }
                    },

                ]
            }).columns.adjust();
            // end isi table ifc

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
                    "url": "<?= base_url() ?>project/pekerjaan_usulan/getPekerjaanUsulan?klasifikasi_pekerjaan_id_non_rkap=1&pekerjaan_status=12,15,16",
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
                            var nomor = full.pekerjaan_nomor.split('-');
                            nomor[0] = pad(nomor[0], 3);
                            if (full.milik == 'y' && full.pekerjaan_nomor == null) {
                                nomor_isi = '';
                            } else if (full.milik == 'y') {
                                nomor_isi = '<span class="badge" style="background-color:#c13333 ">' + nomor.join('-') + '</span>';
                            } else {
                                nomor_isi = nomor.join('-');
                            }
                            return nomor_isi;
                        }
                    },
                    {
                        "data": "tanggal_awal"
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
                                // warna = '#CC6600';
                                status = 'In Progress'
                            } else if (full.pekerjaan_status == 6) {
                                // warna = '#3333FF';
                                status = 'Pekerjaan Berjalan'
                            } else if (full.pekerjaan_status == 7) {
                                warna = '#3333FF';
                                status = 'Pekerjaan Berjalan'
                            } else if (full.pekerjaan_status == 8) {
                                warna = '#FF8000';
                                status = 'IFA';
                            } else if (full.pekerjaan_status == 9) {
                                warna = '#B266FF';
                                status = 'IFA';
                            } else if (full.pekerjaan_status == 10) {
                                warna = '#B266FF';
                                status = 'IFA';
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
                                warna = 'red';
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
                            return (full.is_allow == 'y') ? '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=selesai') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=0"  title="Detail" ><i class="btn btn-info btn-sm">Detail</i></a></center>' : '<center>-</center>';
                        }
                    },
                ]
            }).columns.adjust();
            // end isi table selesai
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

        /* FROM CARI SUBMIT */
        $('#cari_berjalan').on('click', function(e) {
            // alert('tes')
            // e.preventDefault();
            var data = $('#filter').serialize();
            $('#table_berjalan').DataTable().ajax.url('<?= base_url('project/pekerjaan_usulan/getPekerjaanBerjalan?klasifikasi_pekerjaan_id_non_rkap=1&pekerjaan_status=5,6,7&') ?>' + data).load();
        })
        /* FROM CARI SUBMIT */

        /* FROM CARI SUBMIT */
        $('#cari_ifa').on('click', function(e) {
            // alert('tes')
            // e.preventDefault();
            var data = $('#filter').serialize();
            $('#table_ifa').DataTable().ajax.url('<?= base_url('project/pekerjaan_usulan/getPekerjaanUsulan?klasifikasi_pekerjaan_id_non_rkap=1&pekerjaan_status=8&') ?>' + data).load();
        })
        /* FROM CARI SUBMIT */

        /* FROM CARI SUBMIT */
        $('#cari_ifc').on('click', function(e) {
            // alert('tes')
            // e.preventDefault();
            var data = $('#filter').serialize();
            $('#table_ifc').DataTable().ajax.url('<?= base_url('project/pekerjaan_usulan/getPekerjaanUsulan?klasifikasi_pekerjaan_id_non_rkap=1&pekerjaan_status=9,10,11&') ?>' + data).load();
        })
        /* FROM CARI SUBMIT */

        /* Fun Textarea */
        function fun_textarea(isi = null) {
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
            tinymce.get("pekerjaan_deskripsi").setContent(isi);
        }
        /* Fun Textarea */

        /* Klik Tambah */
        function fun_tambah_usulan() {
            $('#pekerjaan_id').val(Date.now());
            $('#jabatan_temp').val('<?= substr($data_session['pegawai_jabatan'], 0, 1) ?>');
            $('#modal_usulan').modal('show');

            fun_textarea();

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
                        console.log(e);
                        var files = $(e.target).filebox('files');
                        if (files.length) row.savedFileName = e.target.filebox('getText');
                    },
                    columns: [
                        [{
                            field: 'pekerjaan_dokumen_nama',
                            title: 'Judul Dokumen',
                            width: '50%',
                            editor: {
                                type: 'textbox',
                                options: {
                                    required: true,
                                    onchange: function(value) {
                                        // console.log(value);
                                        $("#doc_nama").val(value);
                                    }
                                }
                            },
                        }, {
                            field: 'pekerjaan_dokumen_file',
                            title: 'File',
                            width: '50%',
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
                        }, ],
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


            $.getJSON('<?= base_url() ?>project/pekerjaan_usulan/getPekerjaanUsulan?pekerjaan_id=' + id, function(json) {
                fun_textarea(json.pekerjaan_deskripsi);

                if (json.pekerjaan_status == '-') {
                    $('#div_pekerjaan_note').show();
                    $('#pekerjaan_note').val(json.pekerjaan_note);
                } else {
                    $('#div_pekerjaan_note').hide();
                }
                $('#id_klasifikasi_pekerjaan').val(json.klasifikasi_pekerjaan_id);
                $('#klasifikasi_pekerjaan_nama').val(json.klasifikasi_pekerjaan_nama);
                $('#pekerjaan_id').val(json.pekerjaan_id);
                $('#jabatan_temp').val('<?= substr($data_session['pegawai_jabatan'], 0, 1) ?>');
                $('#pekerjaan_status').val('1');
                $('#pekerjaan_waktu').val(json.pekerjaan_waktunya);
                $('#pic').val(json.pic);
                $('#pic_no_telp').val(json.pic_no_telp);
                $('#pekerjaan_judul').val(json.pekerjaan_judul);
                $('#pekerjaan_tahun').val(json.pekerjaan_tahun);
                /*SELECTED SELECT2*/
                $.getJSON('<?= base_url() ?>project/RKAP/getUserListRevApp2', {
                    pegawai_nik: json.pekerjaan_reviewer
                }, function(jsonReviewer) {
                    var newOption = new Option(jsonReviewer.text, jsonReviewer.id, true, true);
                    $('#reviewer').append(newOption).trigger('change');
                });

                $.getJSON('<?= base_url() ?>project/RKAP/getUserListRevApp2', {
                    pegawai_nik: json.pekerjaan_approver
                }, function(jsonApprover) {
                    var newOption = new Option(jsonApprover.text, jsonApprover.id, true, true);
                    $('#approver').append(newOption).trigger('change');
                });
                /*SELECTED SELECT2*/
                // tinymce.get("pekerjaan_deskripsi").setContent(json.pekerjaan_deskripsi);
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
                            title: 'Judul DOkumen',
                            width: '50%',
                            editor: {
                                type: 'textbox',
                                options: {
                                    required: true,
                                    onchange: function(value) {
                                        // console.log(value);
                                        $("#doc_nama").val(value);
                                    }
                                }
                            },
                        }, {
                            field: 'pekerjaan_dokumen_file',
                            title: 'File',
                            width: '50%',
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
                        }, ],
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
            // call_ajax_page('project/pekerjaan_usulan/detailPekerjaan?aksi=' + $('#pekerjaan_status_nama').val() + '&pekerjaan_id=' + id + '&status=' + val + '&rkap=0');
            var url = "<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=') ?>" + $('#pekerjaan_status_nama').val() + '&pekerjaan_id=' + id + '&status=' + val + '&rkap=0';

            window.location.replace(url);

        }

        function fun_detail_disposisi(id, val) {
            call_ajax_page('project/pekerjaan_usulan/detailPekerjaan?aksi=' + $('#pekerjaan_status_nama').val() + '&pekerjaan_id=' + id + '&status=' + val + '&rkap=0');
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
            console.log(row);
            $.post('<?= base_url('/project/pekerjaan_usulan/deletePekerjaanDokumen') ?>', {
                pekerjaan_dokumen_id: row.pekerjaan_dokumen_id,
                pekerjaan_id: row.id_pekerjaan,
                pekerjaan_dokumen_nama: row.pekerjaan_dokumen_nama,
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
            tinymce.remove('#pekerjaan_deskripsi');
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
            $('#table_ifa').DataTable().ajax.reload(null, false);
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
        }

        $('#form_modal_ajuan_extend').on('submit', function(e) {
            var data = new FormData();

            data.append('id_pekerjaan', $('#id_pekerjaan_ajuan_extend').val());
            data.append('extend_hari', $('#pekerjaan_waktu_ajuan_extend').val());
            data.append('extend_status', '0');

            e.preventDefault();
            $.ajax({
                url: '<?= base_url('project/Dokumen_IFA/insertAjuanExtend') ?>',
                type: 'post',
                data: data,
                dataType: 'HTML',
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#loading_form').show();
                    $('#simpan_ajuan_extend').hide();
                },
                complete: function() {
                    $('#loading_form').hide();
                    $('#simpan_ajuan_extend').show();
                },
                success: function(data) {
                    $('#close_ajuan_extend').click();
                }
            });
        })
        // AJUAN EXTEND

        // EXTEND
        function fun_extend(id, status) {
            $('#modal_extend').modal('show');
            $('#id_pekerjaan_extend').val(id);
            $('#pekerjaan_status_extend').val(status);
            $.getJSON('<?= base_url('project/dokumen_IFA/getExtend') ?>', {
                id_pekerjaan: id,
                // pekerjaan_disposisi_status: status,
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
                var url = '<?= base_url('project/dokumen_IFA/updateAjuanExtend') ?>';
            else var url = '<?= base_url('project/dokumen_IFA/insertAjuanExtend') ?>';

            var data = new FormData();

            data.append('id_pekerjaan', $('#id_pekerjaan_extend').val());
            data.append('extend_id', $('#extend_id_extend').val());
            data.append('pekerjaan_disposisi_status', $('#pekerjaan_status_extend').val());
            data.append('extend_hari', $('#pekerjaan_waktu_extend').val());
            data.append('extend_status', '1');

            // console.log(data);

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

        // EXTEND
        // end IFA (tambah)

        /* Zero Padding */
        function pad(str, max) {
            str = str.toString();
            return str.length < max ? pad("0" + str, max) : str;
        }
        /* Zero Padding */
    </script>