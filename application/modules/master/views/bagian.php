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
              <button type="button" class="btn btn-primary float-end" data-toggle="modal" data-target="#modal_bagian" onclick="fun_tambah()">Tambah</button>
              <h4 class="card-title mb-4">Bagian</h4>
            </div>
            <table id="table" class="table table-bordered table-striped nowrap" width="100%">
              <thead class="table-primary">
                <tr>
                  <th style="text-align: center;">Nama Bagian</th>
                  <th class="text-center">Admin</th>
                  <th style="text-align: center;">Pegawai</th>
                  <th style="text-align: center;">Edit</th>
                  <th style="text-align: center;">Delete</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div id="div_bagian_detail" style="display: none">
      <div class=" row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <div>
                <input type="text" id="id_bagian" name="id_bagian" style="display:none">
                <button type="button" class="btn btn-primary float-end" data-toggle="modal" data-target="#modal_bagian_pegawai" onclick="fun_tambah_pegawai()">Tambah</button>
                <h4 class="card-title mb-4">Detail Bagian</h4>
              </div>
              <table id="table_bagian_pegawai" class="table table-bordered table-striped nowrap" width="100%">
                <thead class="table-primary">
                  <tr>
                    <th style="text-align: center;">Nama Pegawai</th>
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
</div>


<!-- MODAL -->
<div class="modal fade" id="modal_bagian">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Bagian</h4>
      </div>
      <div class="modal-body">
        <form id="form_modal">
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <input type="text" name="bagian_id" id="bagian_id" class="form-control col-md-8" style="display:none">
              <label for="" class=" col-md-4">Bagian</label>
              <input type="text" name="bagian_nama" id="bagian_nama" class="form-control col-md-8">
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

<!-- MODAL -->
<div class="modal fade" id="modal_bagian_pegawai">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Detail Bagian</h4>
      </div>
      <div class="modal-body">
        <form id="form_modal_bagian_pegawai">
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <input type="text" name="temp_id_bagian" id="temp_id_bagian" style="display:none">
              <input type="text" name="bagian_detail_id" id="bagian_detail_id" class="form-control col-md-8" style="display:none">
              <label for="" class=" col-md-4">Pegawai</label>
              <select name="pegawai_nik" id="pegawai_nik" class="form-control col-md-8" width="100%"></select>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" id="close_bagian_pegawai" class="btn btn-secondary" data-dismiss="modal" onclick="fun_close_bagian_pegawai()">Close</button>
            <button type="submit" class="btn btn-success pull-right" id="simpan_bagian_pegawai">Simpan</button>
            <button type="submit" class="btn btn-primary pull-right" id="edit_bagian_pegawai" style="display: none;">Edit</button>
            <button class="btn btn-primary" type="button" id="loading_form_bagian_pegawai" disabled style="display: none;">
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

<!-- MODAL -->
<div class="modal fade" id="modal_admin_bagian">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Admin Bagian</h4>
      </div>
      <div class="modal-body">
        <form id="form_modal_admin_bagian">
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <input type="hidden" name="admin_bagian_id" id="admin_bagian_id" class="form-control col-md-8">
              <input style="display: none;" type="text" name="admin_bagian_nik" id="admin_bagian_nik" class="form-control col-md-8">
              <label for="" class=" col-md-4">Admin</label>
              <select name="admin_nik" id="admin_nik" class="form-control select2"></select>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" id="close_admin_bagian" class="btn btn-secondary" data-dismiss="modal" onclick="fun_close_admin_bagian()">Close</button>
            <button type="submit" class="btn btn-success pull-right" id="simpan_admin_bagian">Simpan</button>
            <button type="submit" class="btn btn-primary pull-right" id="edit_admin_bagian" style="display: none;">Edit</button>
            <button class="btn btn-primary" type="button" id="loading_form_admin_bagian" disabled style="display: none;">
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
      "ajax": {
        "url": "<?= base_url() ?>master/bagian/getBagian",
        "dataSrc": ""
      },
      "columns": [
        {"data": "bagian_nama"},
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="javascript:;" id="' + full.bagian_id + '" name="' + full.bagian_nama + '" title="Edit" onclick="fun_admin_bagian(this.id,this.name)"><i class="fa fa-user" ></i></a></center>';
          }
        },
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="javascript:;" id="' + full.bagian_id + '" name="' + full.bagian_nama + '" title="Edit" onclick="fun_bagian_pegawai(this.id,this.name)"><i class="fa fa-list" ></i></a></center>';
          }
        },
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="javascript:;" id="' + full.bagian_id + '" title="Edit" onclick="fun_edit(this.id)"><i class="fa fa-edit" data-toggle="modal" data-target="#modal"></i></a></center>';
          }
        },
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="javascript:;" id="' + full.bagian_id + '" title="Delete" onclick="fun_delete(this.id)"><i class="fa fa-trash"></i></a></center>';
          }
        },
      ]
    });

    $('#table_bagian_pegawai').DataTable({
      "ajax": {
        "url": "<?= base_url() ?>master/bagian/getBagianPegawai",
        "dataSrc": ""
      },
      "columns": [
        {"data": "pegawai_nama"},
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="javascript:;" id="' + full.bagian_detail_id + '" title="Delete" onclick="fun_delete_bagian_pegawai(this.id)"><i class="fa fa-trash"></i></a></center>';
          }
        },
      ]
    });
    /* Isi Table */

    /* SELECT 2 */
    $('#admin_nik').select2({
      dropdownParent: $('#modal_admin_bagian'),
      placeholder: 'Pilih',
      ajax: {
        delay: 250,
        url: '<?= base_url('master/bagian/getBagianUser') ?>',
        dataType: 'json',
        type: 'GET',
        data: function(params) {
          var queryParameters = {
            pegawai_nama: params.term,
            bagian_id: $('#admin_bagian_id').val()
          }
          return queryParameters;
        }
      }
    })

    $('#pegawai_nik').select2({
      dropdownParent: $('#modal_bagian_pegawai'),
      placeholder: 'Pilih',
      ajax: {
        delay: 250,
        url: '<?= base_url('master/bagian/getUserStaf') ?>',
        dataType: 'json',
        type: 'GET',
        data: function(params) {
          var queryParameters = {pegawai_nama: params.term}
          return queryParameters;
        },
      }
    });

    $('.select2-selection').css({
      height: 'auto',
      margin: '0px -10px 0px -10px'
    });
    $('.select2').css('width', '100%');
    /* SELECT 2 */
  });

  function fun_tambah() {
    $('#modal_bagian').modal('show');
  }

  function fun_admin_bagian(id, name) {
    $('#modal_admin_bagian').modal('show');
    $('#admin_bagian_id').val(id);
    $.getJSON("<?= base_url() ?>master/bagian/getBagianAdmin", {bagian_id: id}, function(data) {
      $.each(data, function(index, val) {
        $('#' + index).val(val);
        $('#admin_bagian_nik').val(val.pegawai_nik);
        $('#admin_nik').append('<option selected value="' + val.pegawai_nik + '">' + val.pegawai_nik + '-' + val.pegawai_nama + ' - ' + val.pegawai_postitle + '</option>');
      })
    });
  }

  function fun_edit(id) {
    $('#modal_bagian').modal('show');
    $('#simpan').css('display', 'none');
    $('#edit').css('display', 'block');

    $.getJSON('<?= base_url() ?>master/bagian/getBagian?bagian_id=' + id, function(json) {
      $('#bagian_id').val(json.bagian_id);
      $('#bagian_nama').val(json.bagian_nama);
    });
  }

  $("#form_modal").on("submit", function(e) {
    e.preventDefault();
    var url = ($('#bagian_id').val() != '') ? '<?= base_url('master/bagian/updateBagian') ?>' : '<?= base_url('master/bagian/insertBagian') ?>';

    $.ajax({
      url: url,
      data: $('#form_modal').serialize(),
      type: 'POST',
      dataType: 'html',
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

  $('#form_modal_admin_bagian').on('submit', function(e) {
    e.preventDefault();
    var url = ($('#admin_bagian_nik').val() != '') ? '<?= base_url('master/bagian/updateBagianAdmin') ?>' : '<?= base_url('master/bagian/insertBagianAdmin') ?>';

    $.ajax({
      type: "POST",
      url: url,
      data: $('#form_modal_admin_bagian').serialize(),
      dataType: "HTML",
      beforeSend: function() {
        $('#loading_form_admin_bagian').css('display', 'block');
        $('#simpan_admin_bagian').css('display', 'none');
        $('#edit_admin_bagian').css('display', 'none');
      },
      success: function(isi) {
        $('#close_admin_bagian').click();
        toastr.success('Berhasil');
      }
    });
  })

  function fun_bagian_pegawai(id, name) {
    $('#div_bagian_detail').show();
    $('#id_bagian').val(id);
    $('#table_bagian_pegawai').DataTable().ajax.url('<?= base_url('master/bagian/getBagianPegawai?id_bagian=') ?>' + id).load();
    $('html, body').animate({
      scrollTop: $("#div_bagian_detail").offset().top
    }, 10);
  }

  function fun_delete(id) {
    Swal.fire({
      title: "Apakah anda yakin akan menghapusnya?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#34c38f",
      cancelButtonColor: "#f46a6a",
      confirmButtonText: "Iya"
    }).then(function(result) {
      if (result.value) {
        $.get('<?= base_url() ?>master/bagian/deleteBagian', {bagian_id: id}, function(data) {
          $('#close').click();
          toastr.success('Berhasil');
        });
      }
    });
  }

  function fun_tambah_pegawai() {
    $('#temp_id_bagian').val($('#id_bagian').val());
    $('#modal_bagian_pegawai').modal('show');
  }

  $("#form_modal_bagian_pegawai").on("submit", function(e) {
    e.preventDefault();
    $.ajax({
      url: '<?= base_url('master/bagian/insertBagianPegawai') ?>',
      data: $('#form_modal_bagian_pegawai').serialize(),
      type: 'POST',
      dataType: 'html',
      beforeSend: function() {
        $('#loading_form_bagian_pegawai').css('display', 'block');
        $('#simpan_bagian_pegawai').css('display', 'none');
        $('#edit_bagian_pegawai').css('display', 'none');
      },
      success: function(isi) {
        $('#close_bagian_pegawai').click();
        toastr.success('Berhasil');
      }
    });
  });

  function fun_delete_bagian_pegawai(id) {
    Swal.fire({
      title: "Apakah anda yakin akan menghapusnya?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#34c38f",
      cancelButtonColor: "#f46a6a",
      confirmButtonText: "Iya"
    }).then(function(result) {
      if (result.value) {
        $.get('<?= base_url() ?>master/bagian/deleteBagianPegawai', {bagian_detail_id: id}, function(data) {
          $('#close_bagian_pegawai').click();
          toastr.success('Berhasil');
        });
      }
    });
  }

  function fun_close() {
    fun_loading();
    $('#table').DataTable().ajax.reload();
    $('#simpan').css('display', 'block');
    $('#edit').css('display', 'none');
    $('#table_data').css('display', 'none');
    $('#form_modal')[0].reset();
    $('#modal_bagian').modal('hide');
    $('#loading_form').css('display', 'none');
    $('#pegawai_nik').empty();
    $('#div_bagian_detail').hide();
  }

  $('#modal_bagian').on('hidden.bs.modal', function(e) {
    fun_close();
  });

  function fun_close_admin_bagian() {
    fun_loading();
    $('#table').DataTable().ajax.reload();
    $('#form_modal_admin_bagian')[0].reset();
    $('#admin_nik').empty();
    $('#simpan_admin_bagian').show();
    $('#loading_form_admin_bagian').hide();
    $('#modal_admin_bagian').modal('hide');
  }

  $('#modal_admin_bagian').on('hidden.bs.modal', function(e) {
    fun_close_admin_bagian();
  });

  function fun_close_bagian_pegawai() {
    fun_loading();
    $('#table_bagian_pegawai').DataTable().ajax.reload();
    $('#simpan_bagian_pegawai').css('display', 'block');
    $('#edit_bagian_pegawai').css('display', 'none');
    $('#table_data').css('display', 'none');
    $('#form_modal_bagian_pegawai')[0].reset();
    $('#modal_bagian_pegawai').modal('hide');
    $('#loading_form_bagian_pegawai').css('display', 'none');
    $('#pegawai_nik').empty();
  }

  $('#modal_bagian_pegawai').on('hidden.bs.modal', function(e) {
    fun_close_bagian_pegawai();
  });

  function fun_loading() {
    var simplebar = new Nanobar();
    simplebar.go(100);
  }
</script>