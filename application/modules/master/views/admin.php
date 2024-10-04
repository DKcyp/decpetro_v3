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
              <button type="button" class="btn btn-primary float-end" data-toggle="modal" data-target="#modal" onclick="fun_tambah()">Tambah</button>
              <h4 class="card-title mb-4">Admin Sistem</h4>
            </div>
            <table id="table" class="table table-bordered table-striped nowrap" width="100%">
              <thead class="table-primary">
                <tr>
                  <th style="text-align: center;">No</th>
                  <th style="text-align: center;">NIK</th>
                  <th style="text-align: center;">Nama</th>
                  <th style="text-align: center;">Jabatan</th>
                  <th class="text-center">Hapus</th>
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
<div class="modal fade" id="modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Admin Sistem</h4>
      </div>
      <div class="modal-body">
        <form id="form_modal">
          <input type="text" name="admin_id" id="admin_id" class="form-control col-md-8" style="display:none">
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <label for="" class=" col-md-4">NIK</label>
              <select type="text" name="admin_nik" id="admin_nik" class="form-control col-md-8 select2"></select>
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
      "ajax": {
        "url": "<?= base_url() ?>master/admin/getAdmin",
        "dataSrc": ""
      },
      "columns": [
        {
          render: function(data, type, full, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }
        },
        {"data": "pegawai_nik"},
        {"data": "pegawai_nama"},
        {"data": "pegawai_postitle"},
        {
          render: function(data, type, full, meta) {
            return '<center><a href="javascript:void(0)" id="' + full.admin_id + '" onclick="fun_delete(this.id)"><i class="fa fa-trash"></i></a></center>';
          }
        },
      ]
    });
    /* Isi Table */

    /* Select2 */
    $('#admin_nik').select2({
      dropdownParent: $('#modal'),
      placeholder: 'Pilih',
      ajax: {
        delay: 250,
        url: '<?= base_url('master/admin/getUser') ?>',
        dataType: 'json',
        type: 'GET',
        data: function(params) {
          var queryParameters = {pegawai_nama: params.term}
          return queryParameters;
        }
      }
    })

    $('.select2-selection').css({
      height: 'auto',
      margin: '0px -10px 0px -10px'
    });
    $('.select2').css('width', '100%');
    /* Select2 */
  });

  function fun_tambah() {
    $('#modal').modal('show');
  }

  $("#form_modal").on("submit", function(e) {
    e.preventDefault();
    $.ajax({
      url: '<?= base_url('master/admin/insertAdmin') ?>',
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

  function fun_delete(id) {
    Swal.fire({
      title: "Apakah anda yakin akan menghapusnya?",
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#34c38f",
      cancelButtonColor: "#f46a6a",
      confirmButtonText: "Iya"
    }).then(function(result) {
      if (result.value) {
        $.get('<?= base_url() ?>master/admin/deleteAdmin', {admin_id: id}, function(data) {
          $('#close').click();
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
    $('#form_modal')[0].reset();
    $('#modal').modal('hide');
    $('#admin_nik').empty();
    $('#loading_form').css('display', 'none');
  }

  function fun_loading() {
    var simplebar = new Nanobar();
    simplebar.go(100);
  }
</script>