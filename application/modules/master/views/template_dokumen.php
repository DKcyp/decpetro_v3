<?php
  echo "<pre>";
  print_r ($data_admin);
  echo "</pre>";
?>
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
              <?php if ($data_admin > 0) : ?>
                <button type="button" class="btn btn-primary float-end" data-toggle="modal" data-target="#modal_bagian" onclick="fun_tambah()">Tambah</button>
              <?php endif; ?>
              <h4 class="card-title mb-4">Template</h4>
            </div>
            <table id="table" class="table table-bordered table-striped nowrap" width="100%">
              <thead class="table-primary">
                <tr>
                  <th style="text-align: center;">Nama</th>
                  <th style="text-align: center;">Download</th>
                  <?php if ($data_admin > 0) : ?>
                    <th style="text-align: center;">Edit</th>
                    <th style="text-align: center;">Delete</th>
                  <?php endif; ?>
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
<div class="modal fade" id="modal_bagian">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Template</h4>
      </div>
      <div class="modal-body">
        <form id="form_modal">
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <input type="text" name="id_template_dokumen" id="id_template_dokumen" style="display:none">
              <input type="text" name="bagian_id" id="bagian_id" class="form-control col-md-8" style="display:none">
              <label for="" class=" col-md-4">Nama</label>
              <input type="text" name="template_nama" id="template_nama" class="form-control col-md-8">
            </div>
            <div class="form-group row col-md-12">
              <input type="text" name="bagian_id" id="bagian_id" class="form-control col-md-8" style="display:none">
              <label for="" class=" col-md-4">Dokumen</label>
              <input type="file" name="template_dokumen_file" id="template_dokumen_file" class="form-control col-md-8">
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" id="close_modal" class="btn btn-secondary" data-dismiss="modal" onclick="fun_close()">Close</button>
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
    /* Isi Table */
    $('#table').DataTable({
      "scrollX": true,
      "ajax": {
        "url": "<?= base_url() ?>master/template_dokumen/get_template_data",
        "dataSrc": ""
      },
      "columns": [
        {"data": "template_nama"},
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="template_dokumen/down_template?template_dokumen_file=' + full.template_dokumen_file + '" id="' + full.template_dokumen_file + '" title="Edit"><i class="fa fa-download"></i></a></center>';
          }
        },
        <?php if ($data_admin > 0) : ?> 
          {
            "render": function(data, type, full, meta) {
              return '<center><a href="javascript:;" id="' + full.id_template_dokumen + '" title="Edit" onclick="fun_edit(this.id)"><i class="fa fa-edit"></i></a></center>';
            }
          },
          {
            "render": function(data, type, full, meta) {
              return '<center><a href="javascript:;" id="' + full.id_template_dokumen + '" title="Edit" onclick="fun_delete(this.id)"><i class="fa fa-trash"></i></a></center>';
            }
          },
        <?php endif; ?>
      ]
    });
    /* Isi Table */
  });

  function fun_edit(id) {
    $('#modal_bagian').modal('show');
    $('#simpan').css('display', 'none');
    $('#edit').css('display', 'block');

    $.getJSON('<?= base_url() ?>master/template_dokumen/get_template_data?id_template_dokumen=' + id, function(json) {
      $('#id_template_dokumen').val(json.id_template_dokumen);
      $('#template_nama').val(json.template_nama);
    });
  }

  $("#form_modal").on("submit", function(e) {
    e.preventDefault();
    var data = new FormData($('#form_modal')[0]);

    $.ajax({
      url: '<?= base_url('master/template_dokumen/store_template') ?>',
      data: data,
      type: 'POST',
      dataType: 'html',
      processData: false,
      cache: false,
      contentType: false,
      beforeSend: function() {
        $('#loading_form').css('display', 'block');
        $('#simpan').css('display', 'none');
        $('#edit').css('display', 'none');
      },
      success: function(isi) {
        $('#close_modal').click();
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
        $.get('<?= base_url() ?>master/template_dokumen/del_template', {id_template_dokumen: id}, function(data) {
          fun_close();
          toastr.success('Berhasil');
        });
      }
    });
  }

  function fun_close() {
    $('#table').DataTable().ajax.reload();
    $('#loading_form').css('display', 'none');
    $('#simpan').css('display', 'block');
    $('#edit').css('display', 'none');
    $('#form_modal')[0].reset();
  }
</script>