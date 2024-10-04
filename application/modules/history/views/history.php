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
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">`
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
          <h4 class="card-title mb-4">Database Pekerjaan</h4>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title mb-4">Database Pekerjaan</h4>
            <table id="table" class="table table-bordered table-striped" style="width: 100%;">
              <thead class="table-primary">
                <tr>
                  <th>No</th>
                  <th>Nomor Pekerjaan</th>
                  <th>Tgl Awal</th>
                  <th>Tgl Selesai</th>
                  <th>Nama Pekerjaan</th>
                  <th>User</th>
                  <th>Vendor</th>
                  <?php if ($this->session->userdata('pegawai_id_dep') == 'E53000'): ?>
                    <th>HPS</th>
                  <?php endif ?>
                  <th>Nilai Kontrak</th>
                  <th>History</th>
                  <th>Dokumen</th>
                  <th>Detail</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_history">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">History</h4>
      </div>
      <div class="modal-body">
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
      <div class="modal-footer justify-content-between">
        <button type="button" id="close_document" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_document">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Detail Dokumen</h4>
      </div>
      <div class="modal-body">
        <hr>
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title mb-4">Dokumen</h4>
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
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>
        <div class="col-lg-12">
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
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="close_document" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(function () {
    fun_loading();

    $('#table thead tr').clone(true).addClass('filters').appendTo('#table thead');
    $('#table').DataTable({
      orderCellsTop: true,
      initComplete: function() {
        $("#table").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        var api = this.api();
        api.columns().eq(0).each(function(colIdx) {
          var cell = $('.filters th').eq($(api.column(colIdx).header()).index());
          var title = $(cell).text();
          $(cell).html('<input type="text" class="form-control" style="width:100%" placeholder="' + title + '" />');
          $('input', $('.filters th').eq($(api.column(colIdx).header()).index())).off('keyup change').on('keyup change', function(e) {
            e.stopPropagation();
            $(this).attr('title', $(this).val());
            var regexr = '({search})';
            var cursorPosition = this.selectionStart;
            api.column(colIdx).search(
              this.value != '' ?
              regexr.replace('{search}', '(((' + this.value + ')))') :
              '',
              this.value != '',
              this.value == ''
              ).draw();

            $(this).focus()[0].setSelectionRange(cursorPosition, cursorPosition);
          });
        });
      },
      "ajax": {
        "url": "<?= base_url('history/getHistory') ?>",
        "dataSrc": ""
      },
      "columns": [
        {
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
          "render": function(data, type, full, meta) {
            return $.datepicker.formatDate("dd-mm-yy", new Date(full.pekerjaan_waktu));
          }
        },
        {
          "render": function(data, type, full, meta) {
            return $.datepicker.formatDate("dd-mm-yy", new Date(full.pekerjaan_waktu_akhir));
          }
        },
        {"data": "pekerjaan_judul"},
        {"data": "pegawai_nama"},
        {"data": "pekerjaan_vendor"},
        <?php if ($this->session->userdata('pegawai_id_dep') == 'E53000'): ?>
          {
            "data": "pekerjaan_nilai_hps",
            render: $.fn.dataTable.render.number('.', ',', 2, 'Rp. ')
          },
        <?php endif ?>
        {
          "data": "pekerjaan_nilai_kontrak",
          render: $.fn.dataTable.render.number('.', ',', 2, 'Rp. ')
        },
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="javascript:void(0)" id="' + full.pekerjaan_id + '" onclick="fun_history(this.id,`' + full.pekerjaan_status + '`)"><i class="fa fa-history" data-target="#modal_history" data-toggle="modal"></i></a></center>';
          }
        },
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="javascript:void(0)" id="' + full.pekerjaan_id + '" onclick="fun_document(this.id,`' + full.pekerjaan_status + '`)"><i class="fa fa-file" data-target="#modal_document" data-toggle="modal"></i></a></center>';
          }
        },
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="javascript:void(0)" id="' + full.pekerjaan_id + '" onclick="fun_detail(`'+ full.pekerjaan_id +'`,`' + full.pekerjaan_status + '`,`'+full.id_klasifikasi_pekerjaan+'`)"><i class="fa fa-info-circle"></i></a></center>';
          }
        },
      ]
    }).columns.adjust().draw();

    $('#table_history').DataTable({
      "ordering": false,
      "ajax": {
        "url": "<?= base_url('project/pekerjaan_usulan/') ?>getHistory?id_pekerjaan=0",
        "dataSrc": ""
      },
      "columns": [
        {
          render: function(data, type, full, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }
        },
        {"data": "log_when"},
        {"data": "text"},
        {"data": "log_who"},
      ]
    });

    $('#table_dokumen_selesai').DataTable({
      "dom": 'Bfrtip',
      "ajax": {
        "url": "<?= base_url('project/pekerjaan_usulan/') ?>getDokumenSelesai?is_hps=n&id_pekerjaan=0",
        "dataSrc": ""
      },
      "columns": [
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
            } else if (full.pekerjaan_dokumen_status == '4' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
              var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Approve IFA VP';
            } else if (full.pekerjaan_dokumen_status == '4') {
              var data = 'Approve IFA VP';
            } else if (full.pekerjaan_dokumen_status == '5' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
              var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
            } else if (full.pekerjaan_dokumen_status == '5') {
              var data = 'IFA'
            } else if (full.pekerjaan_dokumen_status == '6') {
              var data = 'IFA AVP'
            } else if (full.pekerjaan_dokumen_status == '7') {
              var data = 'IFA VP'
            } else if (full.pekerjaan_dokumen_status == '8') {
              var data = 'Draft IFC';
            } else if (full.pekerjaan_dokumen_status == '9') {
              var data = 'Send IFC';
            } else if (full.pekerjaan_dokumen_status == '10') {
              var data = 'Approved IFC AVP'
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
              return '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_file + '" name="' + full.pekerjaan_dokumen_id + '" title="Lihat" onclick="fun_lihat(this.id, this.name)" ><i class="bx bx-book m-0"></i></a></center>';
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

    $('#table_dokumen_selesai_hps').DataTable({
      "dom": 'Bfrtip',
      "ajax": {
        "url": "<?= base_url('project/pekerjaan_usulan/') ?>getDokumenSelesaiHPS?is_hps=y&id_pekerjaan=0",
        "dataSrc": ""
      },
      "columns": [
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
            } else if (full.pekerjaan_dokumen_status == '4' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
              var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi + ' - Approve IFA VP';
            } else if (full.pekerjaan_dokumen_status == '4') {
              var data = 'Approve IFA VP';
            } else if (full.pekerjaan_dokumen_status == '5' && (full.pekerjaan_dokumen_revisi != null && full.pekerjaan_dokumen_revisi != '')) {
              var data = 'IFA Rev ' + full.pekerjaan_dokumen_revisi;
            } else if (full.pekerjaan_dokumen_status == '5') {
              var data = 'IFA'
            } else if (full.pekerjaan_dokumen_status == '6') {
              var data = 'IFA AVP'
            } else if (full.pekerjaan_dokumen_status == '7') {
              var data = 'IFA VP'
            } else if (full.pekerjaan_dokumen_status == '8') {
              var data = 'Draft IFC';
            } else if (full.pekerjaan_dokumen_status == '9') {
              var data = 'Send IFC';
            } else if (full.pekerjaan_dokumen_status == '10') {
              var data = 'Approved IFC AVP'
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
              return '<center><a href="javascript:;" id="' + full.pekerjaan_dokumen_file + '" name="' + full.pekerjaan_dokumen_id + '" title="Lihat" onclick="fun_lihat(this.id, this.name)" ><i class="bx bx-book m-0"></i></a></center>';
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
  });

  function fun_history(id,status){
    $('#table_history').DataTable().ajax.url('<?= base_url('project/pekerjaan_usulan/') ?>getHistory?id_pekerjaan='+id).load();
  }

  function fun_document(id, status) {
    fun_loading();
    $('#table_dokumen_selesai').DataTable().ajax.url('<?= base_url('project/pekerjaan_usulan/getDokumenSelesai?id_pekerjaan=') ?>' + id + '&pekerjaan_status=' + status + '&is_hps=n').load();
    $('#table_dokumen_selesai_hps').DataTable().ajax.url('<?= base_url('project/pekerjaan_usulan/getDokumenSelesaiHPS?id_pekerjaan=') ?>' + id + '&pekerjaan_status=' + status + '&is_hps=y').load();
    $('#modal_document').modal('show');
  }
  
  function fun_detail(id,status,klasifikasi){
    if(klasifikasi!='1'){
      location.href = "<?=base_url()?>project/pekerjaan_usulan/detailPekerjaan?aksi=selesai&pekerjaan_id="+id+"&status="+status+"&rkap=1"
    }else{
      location.href = "<?=base_url()?>project/pekerjaan_usulan/detailPekerjaan?aksi=selesai&pekerjaan_id="+id+"&status="+status+"&rkap=0"
    }
  }

  function pad(str, max) {
    str = str.toString();
    return str.length < max ? pad("0" + str, max) : str;
  }

  function fun_loading() {
    var simplebar = new Nanobar();
    simplebar.go(100);
  }
</script>