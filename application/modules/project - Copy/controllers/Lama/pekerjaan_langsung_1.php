<link rel="stylesheet" type="text/css" href="<?= base_url('assets_tambahan/') ?>easyui/themes/default/easyui.css">
<link rel="stylesheet" type="text/css" href="<?= base_url('assets_tambahan/') ?>easyui/themes/icon.css">
<div class="page-content">

    <?php $data_session = $this->session->userdata(); ?>

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
                            <button type="button" class="btn btn-primary float-end" data-toggle="modal" data-target="#myModal" onclick="fun_tambah()">Tambah</button>
                            <h4 class="card-title mb-4">Pekerjaan Langsung</h4>
                        </div>
                        <table id="table" class="table table-bordered table-striped nowrap" width="100%">
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
    <div class="modal fade" id="modal">
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
                                <label class="col-md-4">Waktu Pekerjaan</label>
                                <input type="date" name="pekerjaan_waktu" id="pekerjaan_waktu" class="form-control col-md-8">
                            </div>
                        </div>
                        <div class="card-body row">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">PIC</label>
                                <select class="form-control select2" id="pic" name="pic">

                                </select>
                                <!-- <input type="text" name="pic" id="pic" class="form-control col-md-8" value="<?= $pegawai_nama ?>" readonly> -->
                            </div>
                        </div>
                        <div class="card-body row">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">No Telp</label>
                                <input type="text" name="pic_no_telp" id="pic_no_telp" class="form-control col-md-8">
                            </div>
                        </div>
                        <div class="card-body row">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">Nama Pekerjaan</label>
                                <input type="text" name="pekerjaan_judul" id="pekerjaan_judul" class="form-control col-md-8">
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
    <div class="modal fade" id="modal_aksi" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
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
                            </div>
                        </div>
                        <div class="card-body row">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">Waktu Pekerjaan</label>
                                <input type="date" name="pekerjaan_waktu_aksi" id="pekerjaan_waktu_aksi" class="form-control col-md-8">
                            </div>
                        </div>
                        <div class="card-body row">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">Durasi Pekerjaan</label>
                                <input type="date" name="pekerjaan_durasi_aksi" id="pekerjaan_durasi_aksi" class="form-control col-md-8">
                            </div>
                        </div>
                        <div class="card-body row">
                            <div class="form-group row col-md-12">
                                <label class="col-md-4">Klasifikasi Pekerjaan</label>
                                <select name="id_klasifikasi_pekerjaan_aksi" id="id_klasifikasi_pekerjaan_aksi" class="form-control col-md-8 select2"></select>
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
                                <textarea name="pekerjaan_deskripsi_aksi" id="pekerjaan_deskripsi_aksi" class="form-control col-md-8 txtApik"></textarea>
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
                        "data": "pekerjaan_nomor"
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
                            else if (full.pekerjaan_status == 1) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Send</span>' : 'Send';
                            else if (full.pekerjaan_status == 2) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Reviewed AVP Customer</span>' : 'Reviewed AVP Customer';
                            else if (full.pekerjaan_status == 3) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Approved VP Customer</span>' : 'Approved VP Customer';
                            else if (full.pekerjaan_status == 4) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Approved VP Cangun</span>' : 'Approved VP Cangun';
                            else if (full.pekerjaan_status == 5) return 'In Progress';
                            else if (full.pekerjaan_status == 6) return 'Pekerjaan Berjalan';
                            else if (full.pekerjaan_status == 7) return 'Pekerjaan Berjalan';
                            else if (full.pekerjaan_status == 8) return 'IFA';
                            else if (full.pekerjaan_status == 9) return 'IFC';
                            else if (full.pekerjaan_status == 10) return 'IFC';
                            else if (full.pekerjaan_status == 11) return 'IFC';
                            else if (full.pekerjaan_status == 12) return 'Selesai';
                            else if (full.pekerjaan_status == 15) return 'Selesai';
                            else if (full.pekerjaan_status == '-') return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Reject</span>' : 'Reject';
                            else return full.pekerjaan_status;
                        }
                    },
                    {
                        "render": function(data, type, full, meta) {
                            if (full.pekerjaan_status == '0') return '<center>-</center>';
                            else if (full.pekerjaan_status == '1') return '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" title="Review" name="' + full.pekerjaan_status + '" onclick="fun_detail(this.id,this.name)"><i class="fa fa-share" data-toggle="modal" data-target="#modal"></i></a></center>';
                            else if (full.pekerjaan_status == '2') return '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" name="' + full.pekerjaan_status + '" title="Approve" onclick="fun_detail(this.id,this.name)"><i class="fa fa-check" data-toggle="modal" data-target="#modal"></i></a></center>';
                            else if (full.pekerjaan_status == '9' || full.pekerjaan_status == '10' || full.pekerjaan_status == '11') return '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" name="' + full.pekerjaan_status + '" title="IFC" onclick="fun_detail_modal(this.id,this.name)"><i class="fa fa-pencil-alt" data-toggle="modal" data-bs-target="#modal"></i></a></center>';
                            else return '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" name="' + full.pekerjaan_status + '" title="Detail" onclick="fun_detail(this.id,this.name)"><i class="fas fa-search" data-toggle="modal" data-target="#modal"></i></a></center>';
                        }
                    },
                    {
                        "render": function(data, type, full, meta) {
                            return ((full.pekerjaan_status == '0' || full.pekerjaan_status == '-') && full.pic == <?= $data_session['pegawai_nik'] ?>) ? '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" title="Edit" onclick="fun_edit(this.id)"><i class="fas fa-edit" data-toggle="modal" data-target="#modal"></i></a></center>' : '<center>-</center>';
                        }
                    },
                    {
                        "render": function(data, type, full, meta) {
                            return ((full.pekerjaan_status == '0' || full.pekerjaan_status == '-') && full.pic == <?= $data_session['pegawai_nik'] ?>) ? '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" title="Delete" onclick="fun_delete(this.id)"><i class="fas fa-trash-alt"></i></a></center>' : '<center>-</center>';
                        }
                    },
                ]
            });
            /* Isi Table */
        });

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

        function fun_textarea_aksi() {
            tinymce.init({
                selector: "textarea#pekerjaan_deskripsi_aksi",
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
        function fun_tambah() {
            $('#pekerjaan_id').val(Date.now());
            $('#jabatan_temp').val('<?= substr($data_session['pegawai_jabatan'], 0, 1) ?>');
            $('#modal').modal('show');

            fun_textarea();

            setTimeout(function() {
                $('#dg_document').edatagrid({
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
                            title: 'Nama',
                            width: '50%',
                            editor: {
                                type: 'textbox',
                                options: {
                                    onchange: function(value) {
                                        console.log(value);
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
            }, 500);
        }
        /* Klik Tambah */

        /* Klik Edit */
        function fun_edit(id) {
            $('#modal').modal('show');
            $('#simpan').css('display', 'none');
            $('#edit').css('display', 'block');
            // $('#div_pekerjaan_note').show();

            fun_textarea();

            $.getJSON('<?= base_url() ?>project/pekerjaan_langsung/getPekerjaanLangsung?pekerjaan_id=' + id, function(json) {
                if (json.pekerjaan_status == '-') {
                    $('#div_pekerjaan_note').show();
                    $('#pekerjaan_note').val(json.pekerjaan_note);
                } else {
                    $('#div_pekerjaan_note').hide();
                }
                $('#pekerjaan_id').val(json.pekerjaan_id);
                $('#jabatan_temp').val('<?= substr($data_session['pegawai_jabatan'], 0, 1) ?>');
                $('#pekerjaan_status').val('1');
                $('#pekerjaan_waktu').val(json.pekerjaan_waktunya);
                $('#pic').append('<option selected value="' + json.id_user + '">' + json.pegawai_nama + '</option>');
                // $('#id_user_vp_avp').select2('data', {id:val.id_user, text:val.pegawai_nama});
                // $('#id_user_vp_avp').trigger('change');
                $('#pic_no_telp').val(json.pic_no_telp);
                $('#pekerjaan_judul').val(json.pekerjaan_judul);
                tinymce.get("pekerjaan_deskripsi").setContent(json.pekerjaan_deskripsi);
            });

            setTimeout(function() {
                $('#dg_document').edatagrid({
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
                            title: 'Nama',
                            width: '50%',
                            editor: {
                                type: 'textbox',
                                options: {
                                    onchange: function(value) {
                                        console.log(value);
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
            }, 500);
        }
        /* Klik Edit */

        /* Proses  Draft*/
        $('#simpan').on('click', function() {
            var pekerjaan_deskripsi = tinymce.get('pekerjaan_deskripsi').getContent();
            var data = $('#form_modal').serialize();
            data += '&pekerjaan_deskripsi=' + pekerjaan_deskripsi;
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
        });
        /* Proses Draft*/

        /* Proses  Send*/
        $('#send').on('click', function() {
            var pekerjaan_deskripsi = tinymce.get('pekerjaan_deskripsi').getContent();
            var data = $('#form_modal').serialize();
            data += '&pekerjaan_deskripsi=' + pekerjaan_deskripsi;
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
        });
        /* Proses Send*/

        /* Proses Update*/
        $('#edit').on('click', function() {
            var pekerjaan_deskripsi = tinymce.get('pekerjaan_deskripsi').getContent();
            var data = $('#form_modal').serialize();
            data += '&pekerjaan_deskripsi=' + pekerjaan_deskripsi;
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
        });
        /* Proses Update*/

        /* Proses Modal Aksi*/
        $('#simpan_aksi').on('click', function(e) {
            const data = $('#form_modal_aksi').serialize();
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
        function fun_detail(id, val) {
            call_ajax_page('project/pekerjaan_langsung/detailPekerjaan?aksi=langsung&pekerjaan_id=' + id + '&status=' + val);
        }

        function fun_detail_modal(id, val) {
            $('#modal_aksi').modal('show');
            // $('#edit_aksi').show();
            // $('#simpan_aksi').hide();

            fun_textarea_aksi();

            $.getJSON('<?= base_url('project/pekerjaan_langsung/getPekerjaanLangsung') ?>', {
                pekerjaan_id: id
            }, function(json, result, xhr) {
                $('#pekerjaan_id_aksi').val(json.pekerjaan_id);
                $('#pekerjaan_nomor_aksi').val(json.pekerjaan_nomor);
                $('#pekerjaan_waktu_aksi').val(json.pekerjaan_waktunya);
                $('#pekerjaan_durasi_aksi').val(json.pekerjaan_durasi);
                $('#pekerjaan_judul_aksi').val(json.pekerjaan_judul);
                $('#pekerjaan_nilai_hps_aksi').val(json.pekerjaan_nilai_hps);
                $('#pekerjaan_nilai_kontrak_aksi').val(json.pekerjaan_nilai_kontrak);
                $('#pekerjaan_vendor_aksi').val(json.pekerjaan_vendor);

                $('#id_klasifikasi_pekerjaan_aksi').append('<option selected value="' + json.id_klasifikasi_pekerjaan + '">' + json.klasifikasi_pekerjaan_nama + '</option>');
                $('#id_klasifikasi_pekerjaan_aksi').trigger('change');
                $('#id_user_aksi').append('<option selected value="' + json.id_user + '">' + json.pegawai_nama + '</option>');
                $('#id_user_aksi').trigger('change');

                tinymce.get('pekerjaan_deskripsi_aksi').setContent(json.pekerjaan_deskripsi);

                funPercentase();
            })

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
            $('#dg_document').edatagrid('addRow', {
                index: 0,
                row: {
                    pekerjaan_id: $('#pekerjaan_id').val(),
                }
            });
        }

        function fun_tambah_document_aksi() {
            $('#dg_document_aksi').edatagrid('addRow', {
                index: 0,
                row: {
                    pekerjaan_id: $('#pekerjaan_id_aksi').val(),
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
        /* Fun Hapus */
        /* EASYUI */

        /* Close */
        $('#modal').on('hidden.bs.modal', function(e) {
            fun_close();
        });

        $('#modal_aksi').on('hidden.bs.modal', function(e) {
            fun_close_aksi();
        })

        function fun_close() {
            fun_loading();
            $('#table').DataTable().ajax.reload(null, false);
            $('#simpan').css('display', 'block');
            $('#edit').css('display', 'none');
            $('#div_pekerjaan_note').hide();
            $('#form_modal')[0].reset();
            $('#modal').modal('hide');
            $("#pic").empty();
            tinymce.remove('#pekerjaan_deskripsi');
        }

        function fun_close_aksi() {
            fun_loading();
            $('#table').DataTable().ajax.reload(null, false);
            // $('#simpan').css('display', 'block');
            // $('#edit').css('display', 'none');
            // $('#div_pekerjaan_note').hide();
            $('#form_modal_aksi')[0].reset();
            $('#modal_aksi').modal('hide');
            // $("#pic").empty();
            tinymce.remove('#pekerjaan_deskripsi_aksi');
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
    </script>