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
              <h4 class="card-title mb-4">Bidang</h4>
            </div>
            <table id="table" class="table table-bordered table-striped nowrap" width="100%">
              <thead class="table-primary">
                <tr>
                  <th style="text-align: center;">Bidang Nama</th>
                  <th style="text-align: center;">Bidang Kode</th>
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
        <h4 class="modal-title">Bidang</h4>
      </div>
      <div class="modal-body">
        <form id="form_modal">
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <input type="text" name="bidang_id" id="bidang_id" class="form-control col-md-8" style="display:none">
              <label class="col-md-4">Template Nama</label>
              <input type="text" name="bidang_nama" id="bidang_nama" class="form-control col-md-8">
              <label for="" class=" col-md-4">Template Kode</label>
              <input type="text" name="bidang_kode" id="bidang_kode" class="form-control col-md-8">
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
        "url": "<?= base_url() ?>master/bidang/getBidang",
        "dataSrc": ""
      },
      "columns": [
        {"data": "bidang_nama"}, 
        {"data": "bidang_kode"}, 
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="javascript:;" id="' + full.bidang_id + '" title="Edit" onclick="fun_edit(this.id)"><i class="fa fa-edit" data-toggle="modal" data-target="#modal"></i></a></center>';
          }
        }, 
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="javascript:;" id="' + full.bidang_id + '" title="Delete" onclick="fun_delete(this.id)"><i class="fa fa-trash"></i></a></center>';
          }
        }
      ]
    });
    /* Isi Table */
  });

  function fun_tambah() {
    $('#modal').modal('show');
  }

  function fun_edit(id) {
    $('#modal').modal('show');
    $('#simpan').css('display', 'none');
    $('#edit').css('display', 'block');

    $.getJSON('<?= base_url() ?>master/bidang/getBidang?bidang_id=' + id, function(json) {
      $('#bidang_id').val(json.bidang_id);
      $('#bidang_nama').val(json.bidang_nama);
      $('#bidang_kode').val(json.bidang_kode);
    });
  }

  $("#form_modal").on("submit", function(e) {
    e.preventDefault();
    var url = ($('#bidang_id').val() != '') ? '<?= base_url('master/bidang/updateBidang') ?>' : '<?= base_url('master/bidang/insertBidang') ?>';

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
      icon: "Danger",
      showCancelButton: true,
      confirmButtonColor: "#34c38f",
      cancelButtonColor: "#f46a6a",
      confirmButtonText: "Iya"
    }).then(function(result) {
      if (result.value) {
        $.get('<?= base_url() ?>master/bidang/deleteBidang', {bidang_id: id}, function(data) {
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