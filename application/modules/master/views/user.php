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
              <h4 class="card-title mb-4">User</h4>
            </div>
            <table id="table" class="table table-bordered table-striped nowrap" width="100%">
              <thead class="table-primary">
                <tr>
                  <th style="text-align: center;">No</th>
                  <th style="text-align: center;">Nama User</th>
                  <th style="text-align: center;">Username</th>
                  <th style="text-align: center;">Password</th>
                  <th style="text-align: center;">NIK</th>
                  <th style="text-align: center;">Role</th>
                  <th style="text-align: center;">Sync</th>
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
          <h4 class="modal-title">User</h4>
      </div>
      <div class="modal-body">
        <form id="form_modal">
          <input type="text" name="usr_id" id="usr_id" class="form-control col-md-8" style="display:none">
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <label for="" class=" col-md-4">NIK</label>
              <input type="text" name="pegawai_nik" id="pegawai_nik" class="form-control col-md-8">
            </div>
            <div class="form-group row col-md-12">
              <label for="" class=" col-md-4">Nama Lengkap</label>
              <input type="text" name="pegawai_nama" id="pegawai_nama" class="form-control col-md-8">
            </div>
            <div class="form-group row col-md-12">
              <label for="" class=" col-md-4">Username</label>
              <input type="text" name="usr_loginname" id="usr_loginname" class="form-control col-md-8">
            </div>
            <div class="form-group row col-md-12">
              <label for="" class=" col-md-4">Password</label>
              <input type="password" name="usr_password" id="usr_password" class="form-control col-md-8">
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
  $(function () { 
    fun_loading();

    /* Isi Table */
      $('#table').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
          "url": "<?= base_url() ?>master/user/getUserTable",
          "type":'POST',
        }
      });
    /* Isi Table */
  });

  /* Fun Tambah */
    function fun_tambah() {
      $('#modal_kd').modal('show');
    }
  /* Fun Tambah */

  /* Fun Syncone */
    function fun_sync(nik) {
      $.ajax({
        url: '<?= base_url('master/user/syncMenu') ?>',
        dataType: 'html',
        data: {nik: nik},
      })
      .done(function() {
        toastr.success('Berhasil');
      })
      .fail(function() {
        toastr.error('Gagal');
      });
      
    }
  /* Fun Syncone */

  /* Fun Close */
    function fun_close() {
      fun_loading();
      $('#table').DataTable().ajax.reload();
      $('#simpan').css('display', 'block');
      $('#edit').css('display', 'none');
      $('#form_modal')[0].reset();
      $('#modal_kd').modal('hide');
      $('#loading_form').css('display', 'none');
    }
  /* Fun Close */

  /* Proses */
    $("#form_modal").on("submit", function(e) {
      e.preventDefault();
      $.ajax({
        url: '<?= base_url('master/user/insertUser') ?>',
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
  /* Proses */

  function fun_loading() {
    var simplebar = new Nanobar();
    simplebar.go(100);
  }
</script>