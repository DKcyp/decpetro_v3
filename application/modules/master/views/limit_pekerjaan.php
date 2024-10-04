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
              <h4 class="card-title mb-4">Limit Pekerjaan</h4>
            </div>
            <table id="table" class="table table-bordered table-striped nowrap" width="100%">
              <thead class="table-primary">
                <tr>
                  <th style="text-align: center;">Kode Bagian</th>
                  <th style="text-align: center;">Nama Bagian</th>
                  <th style="text-align: center;">Jumlah Limit</th>
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
<div class="modal fade" id="modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Limit Pekerjaan</h4>
      </div>
      <div class="modal-body">
        <form id="form_modal">
          <input type="text" name="limit_pekerjaan_id" id="limit_pekerjaan_id" class="form-control col-md-8" style="display:none">
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <label class="col-md-4">Kode Bagian</label>
              <input type="text" name="bagian_kode" id="bagian_kode" class="form-control">
            </div>
            <div class="form-group row col-md-12">
              <label class="col-md-4">Nama Bagian</label>
              <input type="text" name="bagian_nama" id="bagian_nama" class="form-control">
            </div>
            <div class="form-group row col-md-12">
              <label class="col-md-4">Limit Pekerjaan</label>
              <input type="number" name="limit_pekerjaan_total" id="limit_pekerjaan_total" class="form-control">
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
        "url": "<?= base_url() ?>master/limit_pekerjaan/getLimitPekerjaan",
        "dataSrc": ""
      },
      "columns": [
        {"data": "bagian_kode"}, 
        {"data": "bagian_nama"}, 
        {"data": "limit_pekerjaan_total"}, 
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="javascript:;" id="' + full.limit_pekerjaan_id + '" title="Edit" onclick="fun_edit(this.id)"><i class="fa fa-edit" data-toggle="modal" data-target="#modal"></i></a></center>';
          }
        }, 
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="javascript:;" id="' + full.limit_pekerjaan_id + '" title="Delete" onclick="fun_delete(this.id)"><i class="fa fa-trash"></i></a></center>';
          }
        }
        ]
    });
  });
    /* Isi Table */

  function fun_tambah() {
    $('#modal').modal('show');
  }

  function fun_edit(id) {
    $('#modal').modal('show');
    $('#simpan').css('display', 'none');
    $('#edit').css('display', 'block');

    $.getJSON('<?= base_url() ?>master/limit_pekerjaan/getLimitPekerjaan?limit_pekerjaan_id=' + id, function(json) {
      $('#limit_pekerjaan_id').val(json.limit_pekerjaan_id);
      $('#bagian_kode').val(json.bagian_kode);
      $('#bagian_nama').val(json.bagian_nama);
      $('#limit_pekerjaan_total').val(json.limit_pekerjaan_total);
    });
  }

  $("#form_modal").on("submit", function(e) {
    e.preventDefault();
    var url = ($('#limit_pekerjaan_id').val() != '') ? '<?= base_url('master/limit_pekerjaan/updateLimitPekerjaan') ?>' : '<?= base_url('master/limit_pekerjaan/insertLimitPekerjaan') ?>';

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
        $.get('<?= base_url() ?>master/limit_pekerjaan/deleteLimitPekerjaan', {
          limit_pekerjaan_id: id
        }, function(data) {
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
    $('#table_data').css('display', 'none');
    $('#form_modal')[0].reset();
    $('#modal').modal('hide');
    $('#loading_form').css('display', 'none');
  }

  $('#modal').on('hidden.bs.modal', function(e) {
    fun_close();
  });

  function fun_loading() {
    var simplebar = new Nanobar();
    simplebar.go(100);
  }
</script>