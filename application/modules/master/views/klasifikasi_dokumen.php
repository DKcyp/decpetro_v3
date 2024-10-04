<div class="page-content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
          <h4 class="card-title mb-4">Master Data</h4>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div>
              <button type="button" class="btn btn-primary float-end" data-toggle="modal" data-target="#modal_kd" onclick="fun_tambah()">Tambah</button>
              <h4 class="card-title mb-4">Inisial Pegawai</h4>
            </div>
            <table id="table" class="table table-bordered table-striped nowrap" width="100%">
              <thead class="table-primary">
                <tr>
                  <th style="text-align: center;">Nama Personil</th>
                  <th style="text-align: center;">Jabatan</th>
                  <th style="text-align: center;">Inisial</th>
                  <th style="text-align: center;">Digital Signature</th>
                  <th style="text-align: center;">Edit</th>
                  <th style="text-align: center;">Delete</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- MODAL -->
<div class="modal fade" id="modal_kd">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Inisial Pegawai</h4>
      </div>
      <div class="modal-body">
        <form id="form_modal">
          <div class="card-body row">
            <input type="text" name="klasifikasi_dokumen_id" id="klasifikasi_dokumen_id" class="form-control col-md-8" style="display:none">
            <div class="form-group row col-md-12">
              <label class="col-md-4">Nama Personil</label>
              <select class="form-control col-md-8 select2" id="pegawai_nik" name="pegawai_nik" style="width:100%"></select>
            </div>
            <div class="form-group row col-md-12">
              <label for="" class=" col-md-4">Inisial</label>
              <input type="text" name="klasifikasi_dokumen_inisial" id="klasifikasi_dokumen_inisial" class="form-control col-md-8">
            </div>
            <div class="form-group row col-md-12">
              <label for="" class=" col-md-4">Digital Signature</label>
              <input type="file" name="signature_pegawai" id="signature_pegawai" class="form-control col-md-8">
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" id="close" class="btn btn-secondary" data-dismiss="modal" onclick="fun_close()">Close</button>
            <button type="submit" class="btn btn-success pull-right" id="simpan">Simpan</button>
            <button type="submit" class="btn btn-primary pull-right" id="edit" style="display: none;">Edit</button>
            <button class="btn btn-primary" type="button" id="loading_form" disabled style="display: none;">
              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              Loading...
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- MODAL -->


<script type="text/javascript">
  $(function() {
    fun_loading();

    /* Isi Table */
    $('#table').DataTable({
      "scrollX": true,
      "ajax": {
        "url": "<?= base_url() ?>master/klasifikasi_dokumen/getKlasifikasiDokumen",
        "dataSrc": ""
      },
      "columns": [
        {"data": "pegawai_nama"},
        {"data": "pegawai_postitle"},
        {"data": "klasifikasi_dokumen_inisial"},
        {
          "render": function(data, type, full, meta) {
            return (full.signature_pegawai) ? '<center><a href="<?= base_url('document/signature/') ?>'+full.signature_pegawai+'" target="_blank" title="Lihat Digital Signature">Lihat Digital Signature !</a></center>' : '<center><p class="text-danger">Belum Ada Digital Signature!</p></center>';
          }
        },
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="javascript:;" id="' + full.klasifikasi_dokumen_id + '" title="Edit" onclick="fun_edit(this.id)"><i class="fa fa-edit" data-toggle="modal" data-target="#modal"></i></a></center>';
          }
        },
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="javascript:;" id="' + full.klasifikasi_dokumen_id + '" title="Delete" onclick="fun_delete(this.id)"><i class="fa fa fa-trash"></i></a></center>';
          }
        },
      ]
    });
    /* Isi Table */

    /* SELECT 2 */
    $('#pegawai_nik').select2({
      dropdownParent: $('#modal_kd'),
      placeholder: 'Pilih',
      ajax: {
        delay: 250,
        url: '<?= base_url('master/klasifikasi_dokumen/getUserListRB') ?>',
        dataType: 'json',
        type: 'GET',
        data: function(params) {
          var queryParameters = {pegawai_nama: params.term}

          return queryParameters;
        },
      }
    });
    /* SELECT 2 */

    $('.select2-selection').css({
      height: 'auto',
      margin: '0px -10px 0px -10px'
    });
    $('.select2').css('width', '100%');
  });

  /* Fun Tambah */
  function fun_tambah() {
    $('#modal_kd').modal('show');
  }
  /* Fun Tambah */

  /* Fun View Edit */
  function fun_edit(id) {
    $('#modal_kd').modal('show');
    $('#simpan').css('display', 'none');
    $('#edit').css('display', 'block');

    $.getJSON('<?= base_url() ?>master/klasifikasi_dokumen/getKlasifikasiDokumen?klasifikasi_dokumen_id=' + id, function(json) {
      $('#klasifikasi_dokumen_id').val(json.klasifikasi_dokumen_id);
      $('#klasifikasi_dokumen_inisial').val(json.klasifikasi_dokumen_inisial);
      $('#pegawai_nik').append('<option selected value="' + json.pegawai_nik + '">' + json.pegawai_nama + ' - ' + json['pegawai_postitle'] + '</option>');
      $('#pegawai_nik').select2('data', {
        id: json.pegawai_nik,
        text: json.pegawai_nama
      });
    });
  }
  /* Fun View Edit */

  /* Proses */
  $("#form_modal").on("submit", function(e) {
    e.preventDefault();
    
    var url = ($('#klasifikasi_dokumen_id').val() != '') ? '<?= base_url('master/klasifikasi_dokumen/updateKlasifikasiDokumen') ?>' : '<?= base_url('master/klasifikasi_dokumen/insertKlasifikasiDokumen') ?>';
    var data = new FormData($('#form_modal')[0]);
    $.ajax({
      url: url,
      data: data,
      type: 'POST',
      dataType: 'html',
      processData:false,
      cache:false,
      contentType:false,
      beforeSend: function() {
        $('#loading_form').css('display', 'block');
        $('#simpan').css('display', 'none');
        $('#edit').css('display', 'none');
      },
      success: function(isi) {
        $('#close').click();
        toastr.success('Berhasil');
      }
    });
  });
  /* Proses */

  /* Fun Delete */
  function fun_delete(id) {
    Swal.fire({
      title: "Apakah anda yakin akan menghapusnya?",
      icon: "Danger",
      showCancelButton: true,
      confirmButtonColor: "#34c38f",
      cancelButtonColor: "#f46a6a",
      confirmButtonText: "Iya"
    }).then(function(result) {
      if (result.value) {
        $.get('<?= base_url() ?>master/klasifikasi_dokumen/deleteKlasifikasiDokumen', {
          klasifikasi_dokumen_id: id
        }, function(data) {
          $('#close').click();
          toastr.success('Berhasil');
        });
      }
    });
  }
  /* Fun Delete */

  /* Fun Close */
  function fun_close() {
    fun_loading();
    $('#table').DataTable().ajax.reload();
    $('#simpan').css('display', 'block');
    $('#edit').css('display', 'none');
    $('#table_data').css('display', 'none');
    $('#form_modal')[0].reset();
    $('#modal_kd').modal('hide');
    $('#loading_form').css('display', 'none');
    $('#pegawai_nik').empty();
  }
  /* Fun Close */

  $('#modal_kd').on('hidden.bs.modal', function(e) {
    fun_close();
  });

  function fun_loading() {
    var simplebar = new Nanobar();
    simplebar.go(100);
  }
</script>