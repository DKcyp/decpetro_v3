<link rel="stylesheet" type="text/css" href="<?= base_url('assets_tambahan/') ?>easyui/themes/default/easyui.css">
<link rel="stylesheet" type="text/css" href="<?= base_url('assets_tambahan/') ?>easyui/themes/icon.css">
<script src="<?= base_url() ?>assets/libs/tinymce/tinymce.min.js"></script>

<div class="page-content">

    <?php
    $data_session = $this->session->userdata();
    $dataAtasan = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_poscode = '" . $data_session['pegawai_direct_superior'] . "'")->row_array();
    $dataKlasifikasi = $this->db->query("SELECT * FROM global.global_klasifikasi_pekerjaan ORDER BY klasifikasi_pekerjaan_nama ASC")->result_array();
    ?>

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-4">Pekerjaan</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <button type="button" class="btn btn-primary float-end" data-toggle="modal" data-target="#modal" onclick="fun_tambah()">Tambah</button>

                            <!-- <button type="button" class="btn btn-success" style="float:right;margin-right: 1em;" onclick="fun_refresh()"  style="display:block">Refresh</button> -->


                            <input type="text" name="pegawai_jabatan" id="pegawai_jabatan" value="<?php echo substr($data_session['pegawai_jabatan'], 0, 1) ?>" style="display:none;">
                            <input type="text" name="jabatan_atasan" id="jabatan_atasan" value="<?php echo substr($dataAtasan['pegawai_jabatan'], 0, 1) ?>" style="display:none;">
                            <input type="text" name="nama_atasan" id="nama_atasan" value="<?php echo $dataAtasan['pegawai_nama'] ?>" style="display:none;">
                            <input type="text" name="nik_atasan" id="nik_atasan" value="<?php echo $dataAtasan['pegawai_nik'] ?>" style="display:none;">
                            <input type="text" name="postitle_atasan" id="postitle_atasan" value="<?php echo $dataAtasan['pegawai_postitle'] ?>" style="display:none;">
                            <input type="text" name="direct_superior_atasan" id="direct_superior_atasan" value="<?php echo $dataAtasan['pegawai_direct_superior'] ?>" style="display:none;">
                            <h4 class="card-title mb-4">Pekerjaan Langsung</h4>
                        </div>
                        <table id="table" class="table table-bordered table-striped" width="100%">
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
                </div>
            </div>
        </div>
        <!-- end row -->

    </div>
    <!-- container-fluid -->

    <!-- MODAL -->
    <div class="modal fade" id="modal" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Pekerjaan</h4>
                </div>
                <div class="modal-body">
                    <form id="form_modal" enctype="multipart/form-data">
                        <input type="text" name="pekerjaan_id" id="pekerjaan_id" style="display:none;">
                        <input type="text" name="jabatan_temp" id="jabatan_temp" style="display:none;">
                        <input type="text" name="pekerjaan_status" id="pekerjaan_status" value="0" style="display:none;">
                        <div class="card-body row">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">Tanggal Pengajuan Pekerjaan</label>
                                <div class="input-group col-md-8" id="dt_pekerjaan_waktu">
                                    <input type="text" class="form-control col-md-8" placeholder="dd-mm-yyyy" data-date-format="dd-mm-yyyy" data-date-container='#dt_pekerjaan_waktu' data-provide="datepicker" data-date-autoclose="true" id="pekerjaan_waktu" name="pekerjaan_waktu" value="<?= date('d-m-Y') ?>">
                                    <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                </div>
                                <label style="color:red;display:none" id="pekerjaan_waktu_alert">Waktu Pekerjaan Tidak Boleh Kosong</label>
                            </div>
                        </div>
                        <div class="card-body row">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">Target Selesai Pekerjaan</label>
                                <div class="input-group" id="dt_pekerjaan_waktu_akhir">
                                    <input type="text" class="form-control col-md-8" placeholder="dd-mm-yyyy" data-date-format="dd-mm-yyyy" data-date-container='#dt_pekerjaan_waktu_akhir' data-provide="datepicker" data-date-autoclose="true" id="pekerjaan_waktu_akhir" name="pekerjaan_waktu_akhir" value="<?= date('d-m-Y') ?>">
                                    <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                </div>
                                <label style="color:red;display:none" id="pekerjaan_waktu_akhir_alert">Target Selesai Pekerjaan Tidak Boleh Kosong</label>
                            </div>
                        </div>
                        <div class="card-body row">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">PIC *</label>
                                <select class="form-control select2" id="pic" name="pic" onchange="funChangePIC(this.value)">

                                </select>
                                <label style="color:red;display:none" id="pic_alert">PIC Tidak Boleh Kosong</label>
                            </div>
                        </div>
                        <div class="card-body row" id="div_reviewer">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">Reviewer *</label>
                                <select class="form-control select2" id="reviewer" name="reviewer">
                                </select>
                                <label style="color:red;display:none" id="reviewer_alert">Reviewer Tidak Boleh Kosong</label>
                            </div>
                        </div>
                        <div class="card-body row" id="div_approver">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">Approver</label>
                                <select class="form-control select2" id="approver" name="approver">
                                    <option>Pilih Approver</option>
                                </select>
                                <label style="color:red;display:none" id="approver_alert">Approver Tidak Boleh Kosong</label>
                            </div>
                        </div>
                        <div class="card-body row">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">No Telp *</label>
                                <input type="number" name="pic_no_telp" id="pic_no_telp" class="form-control col-md-8">
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
                                <label class="col-md-4">Klasifikasi Pekerjaan *</label>
                                <select id="id_klasifikasi_pekerjaan" name="id_klasifikasi_pekerjaan" class="form-control">
                                    <option value="">- Pilih Klasifikasi Dokumen -</option>
                                    <?php foreach ($dataKlasifikasi as $value) : ?>
                                        <option value="<?php echo $value['klasifikasi_pekerjaan_id'] ?>"><?php echo $value['klasifikasi_pekerjaan_nama'] ?></option>
                                    <?php endforeach ?>
                                </select>
                                <label style="color:red;display:none" id="pekerjaan_judul_alert">Klasifikasi Pekerjaan Tidak Boleh Kosong</label>
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
                                <textarea name="pekerjaan_deskripsi" id="pekerjaan_deskripsi" class="form-control col-md-8 txtApik pekerjaan_deskripsi"></textarea>
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
                            <button type="button" id="close" class="btn btn-default" data-dismiss="modal" onclick="fun_close()">Close</button>
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

    <div class="modal fade" id="modal_pekerjaan" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Pekerjaan Edit</h4>
                </div>
                <div class="modal-body">
                    <form id="form_modal_edit" enctype="multipart/form-data">
                        <input type="text" name="pekerjaan_id_edit" id="pekerjaan_id_edit" style="display:none;">
                        <div class="card-body row">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">Waktu Pekerjaan</label>
                                <!-- <input type="date" name="pekerjaan_waktu" id="pekerjaan_waktu" class="form-control col-md-8" required value="<?php echo date('Y-m-d') ?>" readonly> -->
                                <div class="input-group col-md-8" id="dt_pekerjaan_waktu_edit">
                                    <input type="text" class="form-control col-md-8" placeholder="dd-mm-yyyy" data-date-format="dd-mm-yyyy" data-date-container='#dt_pekerjaan_waktu_edit' data-provide="datepicker" data-date-autoclose="true" id="pekerjaan_waktu_edit" name="pekerjaan_waktu_edit" value="<?= date('d-m-Y') ?>">
                                    <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                </div>
                                <label style="color:red;display:none" id="pekerjaan_waktu_edit_alert">Waktu Pekerjaan Tidak Boleh Kosong</label>
                            </div>
                        </div>
                        <div class="card-body row">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">Waktu Pekerjaan Akhir</label>
                                <!-- <input type="date" name="pekerjaan_waktu_akhir" id="pekerjaan_waktu_akhir" class="form-control col-md-8" required value="<?php echo date('Y-m-d') ?>"> -->
                                <div class="input-group" id="dt_pekerjaan_waktu_akhir_edit">
                                    <input type="text" class="form-control col-md-8" placeholder="dd-mm-yyyy" data-date-format="dd-mm-yyyy" data-date-container='#dt_pekerjaan_waktu_akhir_edit' data-provide="datepicker" data-date-autoclose="true" id="pekerjaan_waktu_akhir_edit" name="pekerjaan_waktu_akhir_edit" value="<?= date('d-m-Y') ?>">
                                    <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                </div>
                                <label style="color:red;display:none" id="pekerjaan_waktu_akhir_edit_alert">Target Selesai Pekerjaan Tidak Boleh Kosong</label>
                            </div>
                        </div>
                        <div class="card-body row">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">No Telp *</label>
                                <input type="text" name="pic_no_telp_edit" id="pic_no_telp_edit" class="form-control col-md-8">
                                <label style="color:red;display:none" id="pic_no_telp_alert">No Telepon Tidak Boleh Kosong</label>
                            </div>
                        </div>
                        <div class="card-body row">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">Nama Pekerjaan *</label>
                                <input type="text" name="pekerjaan_judul_edit" id="pekerjaan_judul_edit" class="form-control col-md-8">
                                <label style="color:red;display:none" id="pekerjaan_judul_alert">Nama Pekerjaan Tidak Boleh Kosong</label>
                            </div>
                        </div>
                        <div class="card-body row">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">Tahun Pekerjaan *</label>
                                <input type="text" name="pekerjaan_tahun_edit" id="pekerjaan_tahun_edit" class="form-control col-md-8" maxlength="4" onkeypress="return numberOnly(event)" value="<?= date('Y') ?>">
                                <label style="color:red;display:none" id="pekerjaan_tahun_alert">Tahun Pekerjaan Tidak Boleh Kosong</label>
                            </div>
                        </div>
                        <div class="card-body row">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">Klasifikai Pekerjaan *</label>
                                <select class="form-control select2" id="id_klasifikasi_pekerjaan_edit" name="id_klasifikasi_pekerjaan_edit">

                                </select>
                            </div>
                        </div>
                        <div class="card-body row">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">No Pekerjaan</label>
                                <input type="text" name="pekerjaan_nomor_edit" id="pekerjaan_nomor_edit" class="form-control col-md-8">
                                <label style="color:red;display:none" id="pic_no_telp_alert">No Pekerjaan Tidak Boleh Kosong</label>
                            </div>
                        </div>
                        <hr>
                        <div class="card-body row">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">CC HPS</label>
                                <select class="form-control select2" id="cc_hps" name="cc_hps[]" multiple>

                                </select>
                            </div>
                        </div>
                        <div class="card-body row">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">CC NON HPS</label>
                                <select class="form-control select2" id="cc_non_hps" name="cc_non_hps[]" multiple>

                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="card-body row">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">Detail Pekerjaan</label>
                                <textarea name="pekerjaan_deskripsi_edit" id="pekerjaan_deskripsi_edit" class="form-control col-md-8 txtApik pekerjaan_deskripsi_edit"></textarea>
                            </div>
                        </div>

                        <div class="card-body row">
                            <div class="col-12 mb-3">
                                <!-- <label class="col-md-4">Upload Dokumen</label> -->
                                <input type="hidden" name="doc_nama" id="doc_nama_pekerjaan">
                                <table id="dg_document_pekerjaan" title="Document" style="width:100%" toolbar="#toolbar_pekerjaan" pagination="true" idField="id" rownumbers="true" fitColumns="true" singleSelect="true">
                                </table>
                                <div id="toolbar_pekerjaan">
                                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="fun_tambah_document_pekerjaan()">New</a>
                                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="fun_hapus_document_pekerjaan()">Delete</a>
                                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-save" plain="true" onclick="fun_simpan_document_pekerjaan()">Save</a>
                                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-undo" plain="true" onclick="javascript:$('#dg_document_pekerjaan').edatagrid('cancelRow')">Cancel</a>
                                </div>
                            </div>
                            <div class="col-12 mb-3 mt-3">
                                <table id="dg_document_non_hps" title="Document Non HPS" style="width:100%" toolbar="#toolbar_non_hps" pagination="true" idField="id" rownumbers="true" fitColumns="true" singleSelect="true">
                                    <thead>
                                        <tr>
                                            <th field="pekerjaan_dokumen_nama" width="50" editor="{type:'label'}">Nama</th>
                                            <th field="pekerjaan_dokumen_file" width="50" editor="{type:'label'}">File</th>
                                        </tr>
                                    </thead>
                                </table>
                                <div id="toolbar_non_hps">
                                    <!-- <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="fun_tambah_document_non_hps()">New</a> -->
                                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="fun_hapus_document_non_hps()">Delete</a>
                                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-save" plain="true" onclick="fun_simpan_document_non_hps()">Save</a>
                                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-undo" plain="true" onclick="javascript:$('#dg_document_non_hps').edatagrid('cancelRow')">Cancel</a>
                                </div>
                            </div>
                            <div class="col-12 mb-3 mt-3">
                                <table id="dg_document_hps" title="Document HPS" style="width:100%" toolbar="#toolbar_hps" pagination="true" idField="id" rownumbers="true" fitColumns="true" singleSelect="true">
                                    <thead>
                                        <tr>
                                            <th field="pekerjaan_dokumen_nama" width="50" editor="{type:'label'}">Nama</th>
                                            <th field="pekerjaan_dokumen_file" width="50" editor="{type:'label'}">File</th>
                                        </tr>
                                    </thead>
                                </table>
                                <div id="toolbar_hps">
                                    <!-- <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="fun_tambah_document_hps()">New</a> -->
                                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="fun_hapus_document_hps()">Delete</a>
                                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-save" plain="true" onclick="fun_simpan_document_hps()">Save</a>
                                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-undo" plain="true" onclick="javascript:$('#dg_document_hps').edatagrid('cancelRow')">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="modal-footer justify-content-between">
                        <button type="button" id="close_edit" class="btn btn-default" data-dismiss="modal" onclick="fun_close_edit()">Close</button>
                        <input type="button" class="btn btn-primary pull-right" id="edit_pekerjaan" value="Edit">
                        <button class="btn btn-primary" type="button" id="loading_form" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_aksi" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Pekerjaan</h4>
            </div>
            <div class="modal-body">
                <form id="form_modal_aksi" enctype="multipart/form-data">
                    <input type="hidden" name="pekerjaan_id_aksi" id="pekerjaan_id_aksi">
                    <input type="hidden" name="pekerjaan_durasi_hari" id="pekerjaan_durasi_hari">
                    <input type="hidden" name="pekerjaan_durasi_bulan" id="pekerjaan_durasi_bulan">
                    <input type="hidden" name="pekerjaan_durasi_tahun" id="pekerjaan_durasi_tahun">

                    <div class="card-body row">
                        <div class="form-group row col-md-12">
                            <label class="col-md-4">Nomor Pekerjaan</label>
                            <input type="text" name="pekerjaan_nomor_aksi" id="pekerjaan_nomor_aksi" class="form-control col-md-8">
                            <!-- <label style="color:red;" id="pekerjaan_nomor_aksi_alert">Nomor Pekerjaan Tidak Boleh Kosong</label> -->
                        </div>
                    </div>
                    <div class="card-body row">
                        <div class="form-group row col-md-12">
                            <label class="col-md-4">Waktu Pekerjaan</label>
                            <div class="input-group col-md-8" id="dt_pekerjaan_waktu_aksi">
                                <input type="text" class="form-control col-md-8" placeholder="dd-mm-yyyy" data-date-format="dd-mm-yyyy" data-date-container='#dt_pekerjaan_waktu_aksi' data-provide="datepicker" data-date-autoclose="true" id="pekerjaan_waktu_aksi" name="pekerjaan_waktu_aksi">
                                <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                            </div>
                            <!-- <label style="color:red" id="pekerjaan_waktu_aksi_alert">Waktu Pekerjaan Tidak Boleh Kosong</label> -->
                        </div>
                    </div>
                    <div class="card-body row">
                        <div class="form-group row col-md-12">
                            <label class="col-md-4">Waktu Pekerjaan Akhir</label>
                            <div class="input-group col-md-8" id="dt_pekerjaan_waktu_akhir_aksi">
                                <input type="text" class="form-control col-md-8" placeholder="dd-mm-yyyy" data-date-format="dd-mm-yyyy" data-date-container='#dt_pekerjaan_waktu_akhir_aksi' data-provide="datepicker" data-date-autoclose="true" id="pekerjaan_waktu_akhir_aksi" name="pekerjaan_waktu_akhir_aksi">
                                <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                            </div>
                            <!-- <label style="color:red;" id="pekerjaan_waktu_akhir_aksi_alert">Durasi Pekerjaan Tidak Boleh Kosong</label> -->
                        </div>
                    </div>
                    <div class="card-body row">
                        <div class="form-group row col-md-12">
                            <label class="col-md-4">Klasifikasi Pekerjaan</label>
                            <select name="id_klasifikasi_pekerjaan_aksi" id="id_klasifikasi_pekerjaan_aksi" class="form-control col-md-8 select2"></select>
                            <!-- <label style="color:red" id="id_klasifikasi_pekerjaan_aksi_alert">Klasifikasi Pekerjaan Tidak Boleh Kosong</label> -->
                        </div>
                    </div>
                    <div class="card-body row">
                        <div class="form-group row col-md-12">
                            <label class="col-md-4">User PIC</label>
                            <select name="id_user_aksi" id="id_user_aksi" class="form-control col-md-8 select2"></select>
                        </div>
                    </div>
                    <div class="card-body row">
                        <div class="form-group row col-md-12">
                            <label class="col-md-4">Nama Pekerjaan</label>
                            <input type="text" name="pekerjaan_judul_aksi" id="pekerjaan_judul_aksi" class="form-control col-md-8">
                        </div>
                    </div>
                    <div class="card-body row">
                        <div class="form-group row col-md-12">
                            <label for="" class="col-md-4">Nilai HPS</label>
                            <input type="number" name="pekerjaan_nilai_hps_aksi" id="pekerjaan_nilai_hps_aksi" class="form-control col-md-8" onchange="funPercentase()">
                        </div>
                    </div>
                    <div class="card-body row">
                        <div class="form-group row col-md-12">
                            <label for="" class="col-md-4">Nilai Kontrak</label>
                            <input type="number" name="pekerjaan_nilai_kontrak_aksi" id="pekerjaan_nilai_kontrak_aksi" class="form-control col-md-8" onchange="funPercentase()">
                        </div>
                    </div>
                    <div class="card-body row">
                        <div class="form-group row col-md-12">
                            <label class="col-md-4">CC</label>
                            <select name="cc_id[]" id="cc_id" class="form-control select2" multiple></select>
                        </div>
                    </div>
                    <div class="card-body row">
                        <div class="form-group row col-md-12">
                            <label class="col-md-4">CC HPS</label>
                            <select name="cc_hps_id[]" id="cc_hps_id" class="form-control select2" multiple></select>
                        </div>
                    </div>
                    <div class="card-body row">
                        <div class="form-group row col-md-12">
                            <label for="" class="col-md-4">Persentase</label>
                            <input type="number" name="persentase_aksi" id="persentase_aksi" class="form-control col-md-8" disabled>
                        </div>
                    </div>
                    <div class="card-body row">
                        <div class="form-group row col-md-12">
                            <label for="" class="col-md-4">Vendor</label>
                            <input type="text" name="pekerjaan_vendor_aksi" id="pekerjaan_vendor_aksi" class="form-control col-md-8">
                        </div>
                    </div>

                    <div class="card-body row">
                        <div class="form-group row col-md-12">
                            <label class="col-md-4">Detail Pekerjaan</label>
                            <textarea name="pekerjaan_deskripsi_aksi" id="pekerjaan_deskripsi_aksi" class="form-control col-md-8 txtApik pekerjaan_deskripsi_aksi"></textarea>
                        </div>
                    </div>
                    <div class="card-body row" id="dispose" style="display: none;">
                        <div class="form-group row col-md-12">
                            <label class="col-md-4">Disposisi</label>
                            <select name="id_pekerjaan_disposisi_aksi" id="id_pekerjaan_disposisi_aksi" class="form-control col-md-8">
                                <option value="">-</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body row">
                        <div class="form-group row col-md-12">
                            <label class="col-md-4">Upload Dokumen</label>
                            <input type="hidden" name="doc_nama" id="doc_nama_aksi">
                            <table id="dg_document_aksi" title="Document" style="width:100%" toolbar="#toolbar_aksi" pagination="true" idField="id" rownumbers="true" fitColumns="true" singleSelect="true">
                            </table>
                            <div id="toolbar_aksi">
                                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="fun_tambah_document_aksi()">New</a>
                                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="fun_hapus_document_aksi()">Delete</a>
                                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-save" plain="true" onclick="fun_simpan_document_aksi()">Save</a>
                                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-undo" plain="true" onclick="javascript:$('#dg_document_aksi').edatagrid('cancelRow')">Cancel</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-3 mt-3">
                        <table id="dg_document_non_hps_aksi" title="Document Non HPS" style="width:100%" toolbar="#toolbar_non_hps_aksi" pagination="true" idField="id" rownumbers="true" fitColumns="true" singleSelect="true">
                            <thead>
                                <tr>
                                    <th field="pekerjaan_dokumen_nama" width="50" editor="{type:'label'}">Nama</th>
                                    <th field="pekerjaan_dokumen_file" width="50" editor="{type:'label'}">File</th>
                                </tr>
                            </thead>
                        </table>
                        <div id="toolbar_non_hps_aksi">
                            <!-- <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="fun_tambah_document_non_hps_aksi()">New</a> -->
                            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="fun_hapus_document_non_hps_aksi()">Delete</a>
                            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-save" plain="true" onclick="fun_simpan_document_non_hps_aksi()">Save</a>
                            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-undo" plain="true" onclick="javascript:$('#dg_document_non_hps_aksi').edatagrid('cancelRow')">Cancel</a>
                        </div>
                    </div>
                    <div class="col-12 mb-3 mt-3">
                        <table id="dg_document_hps_aksi" title="Document HPS" style="width:100%" toolbar="#toolbar_hps_aksi" pagination="true" idField="id" rownumbers="true" fitColumns="true" singleSelect="true">
                            <thead>
                                <tr>
                                    <th field="pekerjaan_dokumen_nama" width="50" editor="{type:'label'}">Nama</th>
                                    <th field="pekerjaan_dokumen_file" width="50" editor="{type:'label'}">File</th>
                                </tr>
                            </thead>
                        </table>
                        <div id="toolbar_hps_aksi">
                            <!-- <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="fun_tambah_document_hps_aksi()">New</a> -->
                            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="fun_hapus_document_hps_aksi()">Delete</a>
                            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-save" plain="true" onclick="fun_simpan_document_hps_aksi()">Save</a>
                            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-undo" plain="true" onclick="javascript:$('#dg_document_hps_aksi').edatagrid('cancelRow')">Cancel</a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" id="close_aksi" class="btn btn-default" data-dismiss="modal" onclick="fun_close_aksi()">Close</button>
                    <input type="button" class="btn btn-success pull-right" id="simpan_aksi" value="Simpan">
                    <input type="button" class="btn btn-primary pull-right" id="edit_aksi" value="Edit" style="display: none;">
                    <button class="btn btn-primary" type="button" id="loading_form_aksi" disabled style="display: none;">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- MODAL -->

<script type="text/javascript" src="<?= base_url('assets_tambahan/') ?>easyui/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?= base_url('assets_tambahan/') ?>easyui/jquery.edatagrid.js"></script>

<script type="text/javascript">
    $(function() {
        fun_loading();

        /* Klasifikasi Pekerjaan */
        $('#id_klasifikasi_pekerjaan_edit').select2({
            dropdownParent: $('#modal_pekerjaan'),
            placeholder: 'Pilih',
            ajax: {
                delay: 250,
                url: '<?= base_url('history/history/getKlasifikasiPekerjaan') ?>',
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

        $("#cc_hps").select2({
            dropdownParent: $('#modal_pekerjaan'),
            placeholder: 'Pilih',
            ajax: {
                delay: 250,
                url: '<?= base_url('project/pekerjaan_langsung/getUserStaf') ?>',
                dataType: 'json',
                type: 'GET',
                data: function(params) {
                    var queryParameters = {
                        pegawai_nama: params.term
                    }

                    return queryParameters;
                },
                // cache: true
            }
        });

        $("#cc_non_hps").select2({
            dropdownParent: $('#modal_pekerjaan'),
            placeholder: 'Pilih',
            ajax: {
                delay: 250,
                url: '<?= base_url('project/pekerjaan_langsung/getUserStaf') ?>',
                dataType: 'json',
                type: 'GET',
                data: function(params) {
                    var queryParameters = {
                        pegawai_nama: params.term
                    }

                    return queryParameters;
                },
                // cache: true
            }
        });

        $('.select2-selection').css({
            height: 'auto',
            margin: '0px -10px 0px -10px'

        });
        $('.select2').css('width', '100%');

        /* Klasifikasi Pekerjaan */

        /* Isi Table */
        $('#table thead tr').clone(true).addClass('filters').appendTo('#table thead');
        $('#table').DataTable({
            orderCellsTop: true,
            initComplete: function() {
                var api = this.api();
                api.columns().eq(0).each(function(colIdx) {
                    var cell = $('.filters th').eq(
                        $(api.column(colIdx).header()).index()
                        );
                    var title = $(cell).text();
                    $(cell).html('<input type="text" class="form-control" style="width:100%" placeholder="' + title + '" />');

                    $('input', $('.filters th').eq($(api.column(colIdx).header()).index())).off('keyup change').on('keyup change', function(e) {
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
                "url": "<?= base_url() ?>project/pekerjaan_langsung/getPekerjaanLangsung",
                "dataSrc": ""
            },
            "columns": [{
                render: function(data, type, full, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
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

                }
            },
            {
                "data": "tanggal_awal"
            },
            {
                "data": "pekerjaan_judul"
            },
            {
                "data": "pegawai_unit_name"
            },
            {
                "data": "usr_name"
            },
            {
                "render": function(data, type, full, meta) {
                    var session = "<?php echo ($this->session->userdata('pegawai_nik')); ?>";

                        // if (full.pekerjaan_status == 0) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Draft</span>' : 'Draft';
                        // else if (full.pekerjaan_status == 1) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Send</span>' : 'Send';
                        // else if (full.pekerjaan_status == 2) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Reviewed AVP Customer</span>' : 'Reviewed AVP Customer';
                        // else if (full.pekerjaan_status == 3) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Approved VP Customer</span>' : 'Approved VP Customer';
                        // else if (full.pekerjaan_status == 4) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Approved VP Cangun</span>' : 'Approved VP Cangun';
                        // else if (full.pekerjaan_status == 5) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">In Progress</span>' : 'In Progress';
                        // else if (full.pekerjaan_status == '-') return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Reject</span>' : 'Reject';
                        // else return full.pekerjaan_status;
                    if (full.pekerjaan_status == 0) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Draft</span>' : 'Draft';
                    else if (full.pekerjaan_status == 1) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Menunggu Review AVP</span>' : 'Menunggu Review AVP';
                    else if (full.pekerjaan_status == 2) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Menunggu Approval VP</span>' : 'Menunggu Approval VP';
                    else if (full.pekerjaan_status == 3) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Menunggu Approval VP Rancang Bangun</span>' : 'Menunggu Approval VP Rancang Bangun';
                    else if (full.pekerjaan_status == 4) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Approved VP Cangun</span>' : 'Approved VP Cangun';
                    else if (full.pekerjaan_status == 5) return 'In Progress';
                    else if (full.pekerjaan_status == 6) return 'Pekerjaan Berjalan';
                    else if (full.pekerjaan_status == 7) return 'Pekerjaan Berjalan';
                    else if (full.pekerjaan_status == 8) return 'IFA';
                    else if (full.pekerjaan_status == 9) return 'IFA AVP';
                    else if (full.pekerjaan_status == 10) return 'IFA VP';
                    else if (full.pekerjaan_status == 11) return 'IFC';
                    else if (full.pekerjaan_status == 12) return 'IFC';
                    else if (full.pekerjaan_status == 13) return 'IFC';
                    else if (full.pekerjaan_status == 14) return 'Selesai';
                    else if (full.pekerjaan_status == 15) return 'Selesai';
                    else if (full.pekerjaan_status == 16) return 'Cancel';
                    else if (full.pekerjaan_status == '-') return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Reject</span>' : 'Reject';
                    else return full.pekerjaan_status;
                }
            },
            {
                "render": function(data, type, full, meta) {
                    if (full.pekerjaan_status == '0') return '<center>-</center>';
                    else if (full.pekerjaan_status == '1') return '<center><a href="<?= base_url('project/pekerjaan_langsung/detailPekerjaan?aksi=langsung') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=0&id_user=&id_user_cc="  title="Review" ><i class="fa fa-share" ></i></a></center>';
                    else if (full.pekerjaan_status == '2') return '<center><a href="<?= base_url('project/pekerjaan_langsung/detailPekerjaan?aksi=langsung') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=0&id_user=&id_user_cc="  title="Disposisi" ><i class="fa fa-check" ></i></a></center>';
                    else return '<center><a href="<?= base_url('project/pekerjaan_langsung/detailPekerjaan?aksi=langsung') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap=0&id_user=&id_user_cc="  title="Detail" ><i class="fas fa-search" ></i></a></center>';
                }
            },
            {
                "render": function(data, type, full, meta) {
                    if (full.pekerjaan_status == '0' || full.pekerjaan_status == '-') {
                        return '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" title="Edit" onclick="fun_edit(this.id)"><i class="fas fa-edit" data-toggle="modal" data-target="#modal"></i></a></center>';
                    } else if (full.pekerjaan_status == '14') {
                        return '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" name="' + full.pekerjaan_status + '" title="IFC" onclick="fun_detail_modal(this.id,this.name)"><i class="fa fa-thumbs-up" data-toggle="modal" data-bs-target="#modal"></i></a></center>';
                    } else {
                        return '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" title="Edit" onclick="fun_edit_pekerjaan(this.id)"><i class="fa fa-wrench" data-toggle="modal" data-target="#modal_pekerjaan"></i></a></center>'
                    }
                }
            },
            {
                "render": function(data, type, full, meta) {
                    return ((full.pekerjaan_status == '0' || full.pekerjaan_status == '-')) ? '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" title="Delete" onclick="fun_delete(this.id)"><i class="fas fa-trash-alt"></i></a></center>' : '<center>-</center>';
                }
            },
            ]
});
        /* Isi Table */
});

tinymce.init({
    selector: "textarea.pekerjaan_deskripsi",
    height: 300,
    plugins: [
        "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
        "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
        "save table directionality emoticons template paste"
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
    // tinymce.get("pekerjaan_deskripsi").setContent(isi);
    // }
    /* Textarea Tambah */

    /* Textarea Edit */
function fun_textarea_edit(isi = null) {
        // if (isi != null) {
        //     var isi = isi
        // } else {
        //     var isi = '';
        // }
    tinymce.init({
        selector: "textarea.pekerjaan_deskripsi_edit",
        height: 300,
        plugins: [
            "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "save table  directionality emoticons template paste "
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
    tinymce.get("pekerjaan_deskripsi_edit").setContent(isi);
}
    /* Textarea Edit */

    /* Textarea Aksi */
function fun_textarea_aksi(isi = null) {

    tinymce.init({
        selector: "textarea.pekerjaan_deskripsi_aksi",
        height: 300,
        plugins: [
            "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "save table  directionality emoticons template paste"
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
    tinymce.get("pekerjaan_deskripsi_aksi").setContent(isi);
}
    /* Textarea Aksi */


    /* Klik Tambah */
function fun_tambah() {
    $('#pekerjaan_id').val(Date.now());
    $('#jabatan_temp').val('<?= substr($data_session['pegawai_jabatan'], 0, 1) ?>');
    $('#modal').modal('show');
    $('#div_reviewer').css('display','block');
    $('#div_approver').css('display','block');
        // setTimeout(function() {
        // }, 500);
    setTimeout(function() {
        fun_dg_document();
    }, 500);

    fun_textarea();
}
    /* Klik Tambah */

    /* Klik Edit */
function fun_edit(id) {
    $('#modal').modal('show');
    $('#simpan').css('display', 'none');
    $('#edit').css('display', 'block');

    $.getJSON('<?= base_url() ?>project/pekerjaan_langsung/getPekerjaanLangsung?pekerjaan_id=' + id, function(json) {
        fun_textarea(json.pekerjaan_deskripsi);
        if (json.pekerjaan_deskripsi) {
            tinymce.get("pekerjaan_deskripsi").setContent(json.pekerjaan_deskripsi);
        }
        if (json.pekerjaan_status == '-') {
            $('#div_pekerjaan_note').show();
            $('#pekerjaan_note').val(json.pekerjaan_note);
        } else {
            $('#div_pekerjaan_note').hide();
        }


        $('#pekerjaan_id').val(json.pekerjaan_id);
        $('#jabatan_temp').val('<?= substr($data_session['pegawai_jabatan'], 0, 1) ?>');
        $('#pekerjaan_status').val('1');
        $('#pekerjaan_waktu').val(json.tanggal_awal);
        $('#pekerjaan_waktu_akhir').val(json.tanggal_akhir);
        $('#pic').append('<option selected value="' + json.pic + '">' + json.pegawai_nama + '</option>');
        $('#pic_no_telp').val(json.pic_no_telp);
        $('#pekerjaan_judul').val(json.pekerjaan_judul);
        $('#pekerjaan_tahun').val(json.pekerjaan_tahun);
        $('#id_klasifikasi_pekerjaan').append('<option selected value="' + json.id_klasifikasi_pekerjaan + '">' + json.klasifikasi_pekerjaan_nama + '</option>');

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
    });

    setTimeout(function() {
        fun_dg_document();
            // fun_non_hps();
            // fun_hps();
    }, 1500);
}
    /* Klik Edit */

    /* Klik Edit Pekerjaan */
function fun_edit_pekerjaan(id) {

        // fun_textarea_edit();
    $('#modal_pekerjaan').modal('show');


    $.getJSON('<?= base_url() ?>project/pekerjaan_langsung/getPekerjaanLangsung?pekerjaan_id=' + id, function(json) {

        fun_textarea_edit(json.pekerjaan_deskripsi);
        if (json.pekerjaan_deskripsi) {
            tinymce.get("pekerjaan_deskripsi_edit").setContent(json.pekerjaan_deskripsi);
        }
        tinymce.get("pekerjaan_deskripsi_edit").setContent(json.pekerjaan_deskripsi);
        $('#pekerjaan_id_edit').val(json.pekerjaan_id);
        $('#pekerjaan_waktu_edit').val(json.tanggal_awal);
        $('#pekerjaan_waktu_akhir_edit').val(json.tanggal_akhir);
        $('#pic_no_telp_edit').val(json.pic_no_telp);
        $('#pekerjaan_judul_edit').val(json.pekerjaan_judul);
        $('#pekerjaan_tahun_edit').val(json.pekerjaan_tahun);
        $('#pekerjaan_nomor_edit').val(json.pekerjaan_nomor);
        $('#id_klasifikasi_pekerjaan_edit').append('<option selected value="' + json.id_klasifikasi_pekerjaan + '">' + json.klasifikasi_pekerjaan_nama + '</option>');

        $.getJSON('<?= base_url('history/getCC') ?>', {
            is_cc: 'y',
            id_pekerjaan: id
        }, function(json) {
            $.each(json, function(index, val) {
                    // $('#' + index).val(val);
                $('#cc_non_hps').append('<option selected value="' + val.pegawai_nik + '">' + val.pegawai_nama + '-' + val.pegawai_postitle + '</option>');
            });
        });

        $.getJSON('<?= base_url('history/getCC') ?>', {
            is_cc: 'h',
            id_pekerjaan: id
        }, function(json) {
            $.each(json, function(index, val) {
                    // $('#' + index).val(val);
                $('#cc_hps').append('<option selected value="' + val.pegawai_nik + '">' + val.pegawai_nama + '-' + val.pegawai_postitle + '</option>');
            });
        });

    });

    setTimeout(function() {
        fun_dg_document_pekerjaan(id);
        fun_non_hps(id);
        fun_hps(id);
    }, 500);

}
    /* Klik Edit Pekerjaan */

    /* Proses  Draft*/
$('#simpan').on('click', function() {
    var isi_awal = $('#dg_document').data('datagrid').data.rows[0];
    if ($('#dg_document').data('datagrid').data.total > 0 && 'isNewRecord' in isi_awal) {
            /* Masih Belum Tersimpan Semua */
        $.messager.alert('Peringatan', 'Data Dokumen Masih Ada Yang Belum Tersimpan');
            /* Masih Belum Tersimpan Semua */
    } else {
            /* Sudah Tersimpan Semua */
            /* Alert */
        if ($('#pekerjaan_waktu').val() == '') {
            $('#pekerjaan_waktu_alert').show()
        } else {
            $('#pekerjaan_waktu_alert').hide()
        }
        if ($('#pic').val() == null) {
            $('#pic_alert').show()
        } else {
            $('#pic_alert').hide()
        }
        if ($('#pic_no_telp').val() == '') {
            $('#pic_no_telp_alert').show()
        } else {
            $('#pic_no_telp_alert').hide()
        }
        if ($('#pekerjaan_judul').val() == '') {
            $('#pekerjaan_judul_alert').show()
        } else {
            $('#pekerjaan_judul_alert').hide()
        }
            /* Alert */

        if ($('#pekerjaan_waktu').val() != '' && $('#pic').val() != null && $('#pic_no_telp').val() != '' && $('#pekerjaan_judul').val() != '') {
                /* Lolos Alert*/
            var pekerjaan_deskripsi = tinymce.get('pekerjaan_deskripsi').getContent();
            var data = $('#form_modal').serialize();
            data += '&pekerjaan_deskripsi=' + escape(pekerjaan_deskripsi);
            $.ajax({
                url: '<?= base_url('project/pekerjaan_langsung/insertPekerjaan') ?>',
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
                /* Lolos Alert*/
        }
            /* Sudah Tersimpan Semua */
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
        if (tinymce.get('pekerjaan_deskripsi').getContent() == '') {
            $('#pekerjaan_deskripsi_alert').show();
        } else {
            $('#pekerjaan_deskripsi_alert').hide();
        }

        if ($('#pekerjaan_waktu').val() != '' && $('#pic').val() != '' && $('#pic_no_telp').val() != '' && $('#pekerjaan_judul').val() != '' && tinymce.get('pekerjaan_deskripsi').getContent() != '') {
            var pekerjaan_deskripsi = tinymce.get('pekerjaan_deskripsi').getContent();
            var data = $('#form_modal').serialize();
            data += '&pekerjaan_deskripsi=' + escape(pekerjaan_deskripsi);
            $.ajax({
                url: '<?= base_url('project/pekerjaan_langsung/insertPekerjaanSend') ?>',
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
            console.log(pekerjaan_deskripsi);
            var data = $('#form_modal').serialize();
            data += '&pekerjaan_deskripsi=' + escape(pekerjaan_deskripsi);
            $.ajax({
                url: '<?= base_url('project/pekerjaan_langsung/updatePekerjaan') ?>',
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

    /* Proses Update Pekerjaan*/
$('#edit_pekerjaan').on('click', function() {
    if ($('#pekerjaan_waktu_edit').val() == '') {
        $('#pekerjaan_waktu_edit_alert').show();
    } else {
        $('#pekerjaan_waktu_edit_alert').hide();
    }
    if ($('#pic_no_telp_edit').val() == '') {
        $('#pic_no_telp_edit_alert').show();
    } else {
        $('#pic_no_telp_edit_alert').hide();
    }
    if ($('#pekerjaan_judul_edit').val() == '') {
        $('#pekerjaan_judul_edit_alert').show();
    } else {
        $('#pekerjaan_judul_edit_alert').hide();
    }

    if ($('#pekerjaan_waktu_edit').val() != '' && $('#pic_no_telp_edit').val() != '' && $('#pekerjaan_judul_edit').val() != '') {
        var pekerjaan_deskripsi_edit = tinymce.get('pekerjaan_deskripsi_edit').getContent();
        var data = $('#form_modal_edit').serialize();
        data += '&pekerjaan_deskripsi_edit=' + escape(pekerjaan_deskripsi_edit);
        $.ajax({
            url: '<?= base_url('project/pekerjaan_langsung/updatePekerjaanEdit') ?>',
            data: data,
            type: 'POST',
            dataType: 'html',
            success: function(isi) {
                $('#close_edit').click();
                toastr.success('Berhasil');
            }
        });
    }
});
    /* Proses Update Pekerjaan*/

    /* Proses Modal Aksi*/
$('#simpan_aksi').on('click', function(e) {
    var pekerjaan_deskripsi_aksi = tinymce.get('pekerjaan_deskripsi_aksi').getContent();
    var data = $('#form_modal_aksi').serialize();
    data += '&pekerjaan_deskripsi_aksi=' + escape(pekerjaan_deskripsi_aksi);
    $.ajax({
        url: '<?= base_url('project/pekerjaan_langsung/updatePekerjaanLangsungAksi') ?>',
        data: data,
        dataType: 'HTML',
        type: 'POST',
        beforeSend: function() {
            $('#loading_form_aksi').css('display', 'block');
                // $('#simpan_aksi').css('display', 'none');
                // $('#edit_aksi').css('display', 'none');
        },
        complete: function() {
            $('#loading_form_aksi').hide();
                // $('#simpan_aksi').hide();
                // $('#edit_aksi').show();
        },
        success: function(isi) {
            $('#close_aksi').click();
            toastr.success('Berhasil');
        }
    })
})
    /* Proses Modal Aksi*/

function fun_detail(id, val) {
        // call_ajax_page('project/pekerjaan_langsung/detailPekerjaan?aksi=langsung&pekerjaan_id=' + id + '&status=' + val + '&rkap=0&id_user=&id_user_cc=');

    var url = '<?= base_url('project/pekerjaan_langsung/detailPekerjaan?aksi=langsung') ?>' + '&pekerjaan_id=' + id + '&status=' + val + '&rkap=0&id_user=&id_user_cc=';
    window.open(url, '_blank');
}

    /* Fun Textarea */
    /* Textarea Tambah */
    /* Textarea Tambah */
function fun_textarea(isi = null) {
    if (isi == null) {
        var isi = '';
    } else {
        var isi = isi;
    }
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
    /* Textarea Tambah */


    // fun_textarea();
    // fun_textarea_aksi();
    // fun_textarea_edit();
    /* Fun Textarea */

    /* EasyUI Document */
function fun_dg_document() {
    $('#dg_document').edatagrid({
            // width:'100%',
        url: '<?= base_url('project/pekerjaan_langsung/getPekerjaanDokumen?id_pekerjaan=') ?>' + $('#pekerjaan_id').val(),
        saveUrl: '<?= base_url('project/pekerjaan_langsung/insertPekerjaanDokumenUsulan?') ?>',
        updateUrl: '<?= base_url('project/pekerjaan_langsung/updatePekerjaanDokumen?') ?>',
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
                width: '50%',
                editor: {
                    type: 'textbox',
                    options: {
                        onchange: function(value) {
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
                                url: '<?= base_url('project/pekerjaan_langsung/insertFilePekerjaanDokumen') ?>',
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
}
    /* EasyUI Document */

    /* EASYUI DOKUMEN NON HPS */
function fun_non_hps(id) {
        // var id = $('#pekerjaan_id_edit').val();
    $('#dg_document_non_hps').edatagrid({
        url: '<?= base_url("history/getPekerjaanDokumen?is_hps=n&id_pekerjaan=") ?>' + id,
        saveUrl: '<?= base_url("project/pekerjaan_usulan/insertAsetDocument?is_hps=n&id_pekerjaan=") ?>' + id,
        updateUrl: '<?= base_url("project/pekerjaan_usulan/updateAsetDocumentLangsung?is_hps=n&id_pekerjaan=") ?>' + id,
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
                /*urutan*/
            var up = $(this).datagrid('getEditor', {
                index: index,
                field: 'urutan_proyek_nama'
            });
            $(up.target).combobox('setValue', row.urutan_proyek_id);
                /*urutan*/
                /*section area*/
            var sa = $(this).datagrid('getEditor', {
                index: index,
                field: 'section_area_nama'
            });
            $(sa.target).combobox('setValue', row.section_area_id);
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
            [
            {
                field: 'pekerjaan_dokumen_waktu_input',
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
                        required: false,
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
                        required: false,
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
                field: 'bidang_nama',
                title: 'Bidang',
                width: '20%',
                editor: {
                    type: 'combobox',
                    options: {
                        required: false,
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
                        required: false,
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
                        required: false,
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
                    type: 'label',
                },
            },
            {
                field: 'pekerjaan_dokumen_jumlah',
                title: 'Jumlah Halaman',
                width: '20%',
                editor: {
                    type: 'numberbox',
                    options: {
                        required: false,
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
                        required: false,
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
                        required: false,
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
                field: 'pekerjaan_dokumen_status_nama',
                title: 'Status',
                width: '20%',
                editor: {
                    type: 'label',
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

                            formData.append('id_pekerjaan', $('#pekerjaan_id_edit').val());

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
}
    /* EASYUI DOKUMEN NON HPS */

function fun_non_hps_aksi(id) {
        // var id = $('#pekerjaan_id_edit').val();
    $('#dg_document_non_hps_aksi').edatagrid({
        url: '<?= base_url("history/getPekerjaanDokumen?is_hps=n&id_pekerjaan=") ?>' + id,
        saveUrl: '<?= base_url("project/pekerjaan_usulan/insertAsetDocument?is_hps=n&id_pekerjaan=") ?>' + id,
        updateUrl: '<?= base_url("project/pekerjaan_usulan/updateAsetDocumentLangsung?is_hps=n&id_pekerjaan=") ?>' + id,
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
                /*urutan*/
            var up = $(this).datagrid('getEditor', {
                index: index,
                field: 'urutan_proyek_nama'
            });
            $(up.target).combobox('setValue', row.urutan_proyek_id);
                /*urutan*/
                /*section area*/
            var sa = $(this).datagrid('getEditor', {
                index: index,
                field: 'section_area_nama'
            });
            $(sa.target).combobox('setValue', row.section_area_id);
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
            [
            {
                field: 'pekerjaan_dokumen_waktu_input',
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
                        required: false,
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
                        required: false,
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
                field: 'bidang_nama',
                title: 'Bidang',
                width: '20%',
                editor: {
                    type: 'combobox',
                    options: {
                        required: false,
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
                        required: false,
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
                        required: false,
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
                    type: 'label',
                },
            },
            {
                field: 'pekerjaan_dokumen_jumlah',
                title: 'Jumlah Halaman',
                width: '20%',
                editor: {
                    type: 'numberbox',
                    options: {
                        required: false,
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
                        required: false,
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
                        required: false,
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
                field: 'pekerjaan_dokumen_status_nama',
                title: 'Status',
                width: '20%',
                editor: {
                    type: 'label',
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

                            formData.append('id_pekerjaan', $('#pekerjaan_id_aksi').val());

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
}

    /* EASYUI DOKUMEN HPS */
function fun_hps(id) {
        // var id = $('#pekerjaan_id_edit').val();
    $('#dg_document_hps').edatagrid({
        url: '<?= base_url("history/getPekerjaanDokumen?is_hps=y&id_pekerjaan=") ?>' + id,
        saveUrl: '<?= base_url("project/pekerjaan_usulan/insertAsetDocument?is_hps=y&id_pekerjaan=") ?>' + id,
        updateUrl: '<?= base_url("project/pekerjaan_usulan/updateAsetDocumentLangsung?is_hps=y&id_pekerjaan=") ?>' + id,
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
                /*urutan*/
            var up = $(this).datagrid('getEditor', {
                index: index,
                field: 'urutan_proyek_nama'
            });
            $(up.target).combobox('setValue', row.urutan_proyek_id);
                /*urutan*/
                /*section area*/
            var sa = $(this).datagrid('getEditor', {
                index: index,
                field: 'section_area_nama'
            });
            $(sa.target).combobox('setValue', row.section_area_id);
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
            [
            {
                field: 'pekerjaan_dokumen_waktu_input',
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
                        required: false,
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
                        required: false,
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
                field: 'bidang_nama',
                title: 'Bidang',
                width: '20%',
                editor: {
                    type: 'combobox',
                    options: {
                        required: false,
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
                        required: false,
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
                        required: false,
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
                    type: 'label',
                },
            },
            {
                field: 'pekerjaan_dokumen_jumlah',
                title: 'Jumlah Halaman',
                width: '20%',
                editor: {
                    type: 'numberbox',
                    options: {
                        required: false,
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
                        required: false,
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
                        required: false,
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
                field: 'pekerjaan_dokumen_status_nama',
                title: 'Status',
                width: '20%',
                editor: {
                    type: 'label',
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

                            formData.append('id_pekerjaan', $('#pekerjaan_id_edit').val());

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
}
    /* EASYUI DOKUMEN HPS */

function fun_hps_aksi(id) {
        // var id = $('#pekerjaan_id_edit').val();
    $('#dg_document_hps_aksi').edatagrid({
        url: '<?= base_url("history/getPekerjaanDokumen?is_hps=y&id_pekerjaan=") ?>' + id,
        saveUrl: '<?= base_url("project/pekerjaan_usulan/insertAsetDocument?is_hps=y&id_pekerjaan=") ?>' + id,
        updateUrl: '<?= base_url("project/pekerjaan_usulan/updateAsetDocumentLangsung?is_hps=y&id_pekerjaan=") ?>' + id,
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
                /*urutan*/
            var up = $(this).datagrid('getEditor', {
                index: index,
                field: 'urutan_proyek_nama'
            });
            $(up.target).combobox('setValue', row.urutan_proyek_id);
                /*urutan*/
                /*section area*/
            var sa = $(this).datagrid('getEditor', {
                index: index,
                field: 'section_area_nama'
            });
            $(sa.target).combobox('setValue', row.section_area_id);
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
            [
            {
                field: 'pekerjaan_dokumen_waktu_input',
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
                        required: false,
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
                        required: false,
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
                field: 'bidang_nama',
                title: 'Bidang',
                width: '20%',
                editor: {
                    type: 'combobox',
                    options: {
                        required: false,
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
                        required: false,
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
                        required: false,
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
                    type: 'label',
                },
            },
            {
                field: 'pekerjaan_dokumen_jumlah',
                title: 'Jumlah Halaman',
                width: '20%',
                editor: {
                    type: 'numberbox',
                    options: {
                        required: false,
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
                        required: false,
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
                        required: false,
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
                field: 'pekerjaan_dokumen_status_nama',
                title: 'Status',
                width: '20%',
                editor: {
                    type: 'label',
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

                            formData.append('id_pekerjaan', $('#pekerjaan_id_aksi').val());

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
}

    /*Easy UI Document Pekerjaan*/
function fun_dg_document_pekerjaan(id) {
    $('#dg_document_pekerjaan').edatagrid({
        url: '<?= base_url('project/pekerjaan_langsung/getPekerjaanDokumen?id_pekerjaan=') ?>' + id,
            // url: '<?= base_url('project/pekerjaan_langsung/getPekerjaanDokumen?id_pekerjaan=') ?>' + $('#pekerjaan_id_edit').val(),
        saveUrl: '<?= base_url('project/pekerjaan_langsung/insertPekerjaanDokumenUsulan?') ?>',
        updateUrl: '<?= base_url('project/pekerjaan_langsung/updatePekerjaanDokumen?') ?>',
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
                width: '50%',
                editor: {
                    type: 'textbox',
                    options: {
                        onchange: function(value) {
                            $("#doc_nama_pekerjaan").val(value);
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
                        buttonText: '...',
                        onChange: function() {
                            var self = $(this);
                            var files = self.filebox('files');
                            var formData = new FormData();
                            var nama = $("#doc_nama_pekerjaan").val();
                            self.filebox('setText', 'Menyimpan...');

                            formData.append('id_pekerjaan', $('#pekerjaan_id_edit').val());

                            for (var i = 0; i < files.length; i++) {
                                var file = files[i];
                                formData.append('file', file, file.name);
                            }

                            $.ajax({
                                url: '<?= base_url('project/pekerjaan_langsung/insertFilePekerjaanDokumen') ?>',
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
}
    /*Easy UI Document Pekerjaan*/

    /* Get User CC dan CC HPS*/
$('#cc_id').select2({
    dropdownParent: $('#modal_aksi'),
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

$('#cc_hps_id').select2({
    dropdownParent: $('#modal_aksi'),
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
    /* Get User CC dan CC HPS*/

function funChangePIC(val) {
    $.getJSON('<?= base_url() ?>project/RKAP/getUserListRevApp3', {
        pegawai_nik: val
        }, function(json) { // JSON CARI JABATAN PIC
            var pegawai_jabatan = json.pegawai_jabatan;
            console.log(json);
            var q = pegawai_jabatan.substring(0, 1);
            if (q == '2') {
                $('#div_reviewer').css('display', 'none');
                $('#div_approver').css('display', 'none');
                $('#approver').val('');
                $('#reviewer').val('');
                $('#jabatan_temp').val(q);
            } else if (q == '3') {
                $('#div_reviewer').css('display', 'none');
                $('#div_approver').css('display', 'block');
                $('#reviewer').val('');
                $('#jabatan_temp').val(q);
                //Approver VP
                $.getJSON('<?= base_url() ?>project/RKAP/getUserListRevApp2', {
                    param1: json.pegawai_direct_superior
                }, function(jsonq) {
                    var newOption = new Option(jsonq.text, jsonq.id, true, true);
                    $('#approver').append(newOption).trigger('change');
                });
            } else {
                $('#jabatan_temp').val(q);
                $('#div_reviewer').css('display', 'block');
                $('#div_approver').css('display', 'block');
                $.getJSON('<?= base_url() ?>project/RKAP/getUserListRevApp3', {
                    param1: json.pegawai_direct_superior
                }, function(json3) {
                    var pegawai_jabatan = json3.pegawai_jabatan;
                    var q = pegawai_jabatan.substring(0, 1);
                    if (q == '2') {
                        // Review & Approver direct atasan
                        $.getJSON('<?= base_url() ?>project/RKAP/getUserListRevApp2', {
                            param1: json.pegawai_direct_superior
                        }, function(jsonReviewer) {
                            var newOption = new Option(jsonReviewer.text, jsonReviewer.id, true, true);
                            $('#reviewer').append(newOption).trigger('change');
                            var newOption = new Option(jsonReviewer.text, jsonReviewer.id, true, true);
                            $('#approver').append(newOption).trigger('change');
                        });
                    } else {
                        // Review direct atasan ; Approve VP
                        $.getJSON('<?= base_url() ?>project/RKAP/getUserListRevApp2', {
                            param1: json.pegawai_direct_superior
                        }, function(jsonReviewer) {
                            var newOption = new Option(jsonReviewer.text, jsonReviewer.id, true, true);
                            $('#reviewer').append(newOption).trigger('change');
                            $.getJSON('<?= base_url() ?>project/RKAP/getUserListRevApp3', {
                                pegawai_nik: jsonReviewer.id
                            }, function(json2) {
                                $.getJSON('<?= base_url() ?>project/RKAP/getUserListRevApp2', {
                                    param1: json2.pegawai_direct_superior
                                }, function(jsonApprover) {
                                    var newOption = new Option(jsonApprover.text, jsonApprover.id, true, true);
                                    $('#approver').append(newOption).trigger('change');
                                });
                            });
                        });
                    }
                });
            }
        });
}

$('#reviewer').select2({
    dropdownParent: $('#modal'),
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
    dropdownParent: $('#modal'),
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


    /* select2 pic */
$("#pic").select2({
    dropdownParent: $('#modal'),
    placeholder: 'Pilih',
    ajax: {
        delay: 250,
        url: '<?= base_url('project/pekerjaan_langsung/getUserStaf') ?>',
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

$('#id_user_aksi').select2({
    dropdownParent: $('#modal_aksi'),
    placeholder: 'Pilih',
    ajax: {
        delay: 250,
        url: '<?= base_url('history/history/getUser') ?>',
        dataType: 'json',
        type: 'GET',
        data: function(params) {
            var queryParameters = {
                usr_name: params.term
            }

            return queryParameters;
        },
        cache: true
    }
});

$('#id_klasifikasi_pekerjaan_aksi').select2({
    dropdownParent: $('#modal_aksi'),
    placeholder: 'Pilih',
    ajax: {
        delay: 250,
        url: '<?= base_url('history/history/getKlasifikasiPekerjaan') ?>',
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

$('.select2-selection').css({
    height: 'auto',
    margin: '0px -10px 0px -10px'
});
$('.select2').css('width', '100%');
    /* select2 pic */

    /* Klik Detail */


function fun_detail_modal(id, val) {
    $('#modal_aksi').modal('show');
        // $('#edit_aksi').show();
        // $('#simpan_aksi').hide();
    $.getJSON('<?= base_url('project/pekerjaan_langsung/getPekerjaanLangsung') ?>', {
        pekerjaan_id: id
    }, function(json, result, xhr) {
        console.log(json);
        fun_textarea_aksi(json.pekerjaan_deskripsi);
        if (json.pekerjaan_deskripsi) {
            tinymce.get("pekerjaan_deskripsi_aksi").setContent(json.pekerjaan_deskripsi);
        }
        tinymce.get("pekerjaan_deskripsi_aksi").setContent(json.pekerjaan_deskripsi);
        $('#pekerjaan_id_aksi').val(json.pekerjaan_id);
        $('#pekerjaan_nomor_aksi').val(json.pekerjaan_nomor);
        $('#pekerjaan_waktu_aksi').val(json.tanggal_awal);
        $('#pekerjaan_waktu_akhir_aksi').val(json.tanggal_akhir);
        $('#pekerjaan_judul_aksi').val(json.pekerjaan_judul);
        $('#pekerjaan_nilai_hps_aksi').val(json.pekerjaan_nilai_hps);
        $('#pekerjaan_nilai_kontrak_aksi').val(json.pekerjaan_nilai_kontrak);
        $('#pekerjaan_vendor_aksi').val(json.pekerjaan_vendor);

        $('#id_klasifikasi_pekerjaan_aksi').append('<option selected value="' + json.id_klasifikasi_pekerjaan + '">' + json.klasifikasi_pekerjaan_nama + '</option>');
        $('#id_klasifikasi_pekerjaan_aksi').trigger('change');
        $('#id_user_aksi').append('<option selected value="' + json.id_user + '">' + json.pegawai_nama + '</option>');
        $('#id_user_aksi').trigger('change');
        funPercentase();
    });

    $('#cc_id').empty();
    $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserStafVP') ?>', {
        pekerjaan_id: id,
        is_cc: 'y',
    }, function(json) {
        $.each(json, function(index, val) {
            $('#' + index).val(val);
            $('#cc_id').append('<option selected value="' + val.id_user + '">' + val.pegawai_nama + '-' + val.pegawai_postitle + '</option>');
        });
    });

    $('#cc_hps_id').empty();
    $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserStafVP') ?>', {
        pekerjaan_id: id,
        is_cc: 'h',
    }, function(json) {
        $.each(json, function(index, val) {
            $('#' + index).val(val);
            $('#cc_hps_id').append('<option selected value="' + val.id_user + '">' + val.pegawai_nama + '-' + val.pegawai_postitle + '</option>');
        });
    });

    setTimeout(() => {
        fun_non_hps_aksi(id);
        fun_hps_aksi(id);
    }, 500);

    setTimeout(function() {
        $('#dg_document_aksi').edatagrid({
            url: '<?= base_url('project/pekerjaan_langsung/getPekerjaanDokumen?id_pekerjaan=') ?>' + id,
            saveUrl: '<?= base_url('project/pekerjaan_langsung/insertPekerjaanDokumenUsulan?') ?>',
            updateUrl: '<?= base_url('project/pekerjaan_langsung/updatePekerjaanDokumen?') ?>',
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
                    title: 'Nama',
                    width: '50%',
                    editor: {
                        type: 'textbox',
                        options: {
                            onchange: function(value) {
                                console.log(value);
                                $("#doc_nama_aksi").val(value);
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
                            buttonText: '...',
                            onChange: function() {
                                var self = $(this);
                                var files = self.filebox('files');
                                var formData = new FormData();
                                var nama = $("#doc_nama_aksi").val();
                                self.filebox('setText', 'Menyimpan...');

                                formData.append('id_pekerjaan', $('#pekerjaan_id_aksi').val());

                                for (var i = 0; i < files.length; i++) {
                                    var file = files[i];
                                    formData.append('file', file, file.name);
                                }

                                $.ajax({
                                    url: '<?= base_url('project/pekerjaan_langsung/insertFilePekerjaanDokumen') ?>',
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
            $.get('<?= base_url() ?>project/pekerjaan_langsung/deletePekerjaan', {
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

function fun_tambah_document_aksi() {
    $('#dg_document_aksi').edatagrid('addRow', {
        index: 0,
        row: {
            pekerjaan_id: $('#pekerjaan_id_aksi').val(),
        }
    });
}

function fun_tambah_document_pekerjaan() {
    $('#dg_document_pekerjaan').edatagrid('addRow', {
        index: 0,
        row: {
            pekerjaan_id: $('#pekerjaan_id_edit').val(),
        }
    });
}
    /* Fun Tambah */

    /* Fun Simpan */
function fun_simpan_document() {
    $('#dg_document').edatagrid('saveRow');
    setTimeout(() => {
        $('#dg_document').datagrid('reload');
    }, 1000);
}

function fun_simpan_document_aksi() {
    $('#dg_document_aksi').edatagrid('saveRow');
    setTimeout(() => {
        $('#dg_document_aksi').datagrid('reload');
    }, 1000);
}

function fun_simpan_document_pekerjaan() {
    $('#dg_document_pekerjaan').edatagrid('saveRow');
    setTimeout(() => {
        $('#dg_document_pekerjaan').datagrid('reload');
    }, 1000);
}

    /* Fun Simpan Non HPS */
function fun_simpan_document_non_hps() {
    $('#dg_document_non_hps').edatagrid('saveRow');
    setTimeout(() => {
        $('#dg_document_non_hps').datagrid('reload');
    }, 1000);
}
    /* Fun Simpan Non HPS */

    /* Fun Simpan Non HPS */
function fun_simpan_document_non_hps_aksi() {
    $('#dg_document_non_hps_aksi').edatagrid('saveRow');
    setTimeout(() => {
        $('#dg_document_non_hps_aksi').datagrid('reload');
    }, 1000);
}
    /* Fun Simpan Non HPS */

    /* Fun Simpan HPS */
function fun_simpan_document_hps() {
    $('#dg_document_hps').edatagrid('saveRow');
    setTimeout(() => {
        $('#dg_document_hps').datagrid('reload');
    }, 1000);
}
    /* Fun Simpan HPS */

    /* Fun Simpan HPS */
function fun_simpan_document_hps_aksi() {
    $('#dg_document_hps_aksi').edatagrid('saveRow');
    setTimeout(() => {
        $('#dg_document_hps_aksi').datagrid('reload');
    }, 1000);
}
    /* Fun Simpan HPS */
    /* Fun Simpan */

    /* Fun Hapus */
function fun_hapus_document() {
    var row = $('#dg_document').datagrid('getSelected');
    $.post('<?= base_url('/project/pekerjaan_langsung/deletePekerjaanDokumen') ?>', {
        pekerjaan_dokumen_id: row.pekerjaan_dokumen_id
    }, function(data, textStatus, xhr) {
        $('#dg_document').datagrid('reload');
    });
}

function fun_hapus_document_aksi() {
    var row = $('#dg_document_aksi').datagrid('getSelected');
    $.post('<?= base_url('/project/pekerjaan_langsung/deletePekerjaanDokumen') ?>', {
        pekerjaan_dokumen_id: row.pekerjaan_dokumen_id
    }, function(data, textStatus, xhr) {
        $('#dg_document_aksi').datagrid('reload');
    });
}

function fun_hapus_document_pekerjaan() {
    var row = $('#dg_document_pekerjaan').datagrid('getSelected');
    $.post('<?= base_url('/project/pekerjaan_langsung/deletePekerjaanDokumen') ?>', {
        pekerjaan_dokumen_id: row.pekerjaan_dokumen_id
    }, function(data, textStatus, xhr) {
        $('#dg_document_pekerjaan').datagrid('reload');
    });
}

    /* Fun Hapus Non HPS */
function fun_hapus_document_non_hps() {
    var row = $('#dg_document_non_hps').datagrid('getSelected');
    $.post('<?= base_url('/project/pekerjaan_usulan/deletePekerjaanDokumen') ?>', {
        pekerjaan_dokumen_id: row.pekerjaan_dokumen_id
    }, function(data, textStatus, xhr) {
        $('#dg_document_non_hps').datagrid('reload');
    });
}
    /* Fun Hapus Non HPS */
    /* Fun Hapus Non HPS */
function fun_hapus_document_non_hps_aksi() {
    var row = $('#dg_document_non_hps_aksi').datagrid('getSelected');
    $.post('<?= base_url('/project/pekerjaan_usulan/deletePekerjaanDokumen') ?>', {
        pekerjaan_dokumen_id: row.pekerjaan_dokumen_id
    }, function(data, textStatus, xhr) {
        $('#dg_document_non_hps_aksi').datagrid('reload');
    });
}
    /* Fun Hapus Non HPS */

    /* Fun Hapus HPS */
function fun_hapus_document_hps() {
    var row = $('#dg_document_hps').datagrid('getSelected');
    $.post('<?= base_url('/project/pekerjaan_usulan/deletePekerjaanDokumen') ?>', {
        pekerjaan_dokumen_id: row.pekerjaan_dokumen_id
    }, function(data, textStatus, xhr) {
        $('#dg_document_hps').datagrid('reload');
    });
}
    /* Fun Hapus HPS */

    /* Fun Hapus HPS */
function fun_hapus_document_hps_aksi() {
    var row = $('#dg_document_hps_aksi').datagrid('getSelected');
    $.post('<?= base_url('/project/pekerjaan_usulan/deletePekerjaanDokumen') ?>', {
        pekerjaan_dokumen_id: row.pekerjaan_dokumen_id
    }, function(data, textStatus, xhr) {
        $('#dg_document_hps_aksi').datagrid('reload');
    });
}
    /* Fun Hapus HPS */
    /* Fun Hapus */
    /* EASYUI */

    /* Close */
$('#modal').on('hidden.bs.modal', function(e) {
    fun_close();
});

$('#modal_pekerjaan').on('hidden.bs.modal', function(e) {
    fun_close_edit();
});

$('#modal_aksi').on('hidden.bs.modal', function(e) {
    fun_close_aksi();
})

function fun_close() {
    fun_loading();
        // tinymce.remove('#pekerjaan_deskripsi');
    $('#table').DataTable().ajax.reload(null, false);
    $('#simpan').css('display', 'block');
    $('#edit').css('display', 'none');
    $('#div_pekerjaan_note').hide();
    $('#form_modal')[0].reset();
    $('#modal').modal('hide');
    $("#pic").empty();
    $("#approver").empty();
    $("#reviewer").empty();
        // alert
    $('#pekerjaan_waktu_alert').hide();
    $('#pic_alert').hide();
    $('#pic_no_telp_alert').hide();
    $('#pekerjaan_judul_alert').hide();
}

function fun_close_edit() {
    fun_loading();
        // tinymce.remove('#pekerjaan_deskripsi');
    $('#table').DataTable().ajax.reload(null, false);
    $('#form_modal')[0].reset();
    $('#form_modal_edit')[0].reset();
    $('#modal_pekerjaan').modal('hide');
        // alert
    $('#pekerjaan_waktu_edit_alert').hide();
    $('#pic_no_telp_edit_alert').hide();
    $('#pekerjaan_judul_edit_alert').hide();
    $('#cc_hps').empty();
    $('#cc_non_hps').empty();
}

function fun_close_aksi() {
    fun_loading();
        // tinymce.remove('#pekerjaan_deskripsi_aksi');
    $('#table').DataTable().ajax.reload(null, false);
        // $('#simpan').css('display', 'block');
        // $('#edit').css('display', 'none');
        // $('#div_pekerjaan_note').hide();
    $('#form_modal_aksi')[0].reset();
    $('#modal_aksi').modal('hide');
        // $("#pic").empty();
}
    /* Close */

    /* Loading */
function fun_loading() {
    var simplebar = new Nanobar();
    simplebar.go(100);
}
    /* Loading */

function funPercentase() {
    var hps = $('#pekerjaan_nilai_hps_aksi').val() * 1;
    var kontrak = $('#pekerjaan_nilai_kontrak_aksi').val() * 1;

    var sum = hps - kontrak;
    var persentase = sum / hps * 100;

    $('#persentase_aksi').val(persentase);
}



    /* Zero Padding */
function pad(str, max) {
    str = str.toString();
    return str.length < max ? pad("0" + str, max) : str;
}
    /* Zero Padding */

function fun_refresh() {
    $('#table').DataTable().ajax.reload(null, false);

}

window.onfocus = function() {
    setTimeout(function() {
        fun_refresh();
            // code to execute when window gains focus
        }, 1000); // delay of 1000ms (1 second)
};
</script>