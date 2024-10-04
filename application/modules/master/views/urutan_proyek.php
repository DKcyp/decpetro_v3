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
              <h4 class="card-title mb-4">Urutan Proyek</h4>
            </div>
            <table id="table" class="table table-bordered table-striped nowrap" width="100%">
              <thead class="table-primary">
                <tr>
                  <th style="text-align: center;">Nama Urutan Proyek</th>
                  <th style="text-align: center;">Kode Urutan Proyek</th>
                  <th style="text-align: center;">Detail</th>
                  <th style="text-align: center;">Edit</th>
                  <th style="text-align: center;">Delete</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div id="div_detail" style="display: none">
      <div class=" row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <div>
                <input type="text" id="id_urutan_proyek" name="id_urutan_proyek" style="display:none">
                <button type="button" class="btn btn-primary float-end" data-toggle="modal" data-target="#modal_detail" onclick="fun_tambah_pegawai()">Tambah</button>
                <h4 class="card-title mb-4">Section Area</h4>
              </div>
              <table id="table_detail" class="table table-bordered table-striped nowrap" width="100%">
                <thead class="table-primary">
                  <tr>
                    <th style="text-align: center;">Nama Section Area</th>
                    <th style="text-align: center;">Kode Section Area</th>
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
</div>

<!-- MODAL -->
<div class="modal fade" id="modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Urutan Proyek</h4>
      </div>
      <div class="modal-body">
        <form id="form_modal">
          <input type="text" name="urutan_proyek_id" id="urutan_proyek_id" class="form-control col-md-8" style="display:none">
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <label for="" class=" col-md-4">Nama Urutan Proyek</label>
              <input type="text" name="urutan_proyek_nama" id="urutan_proyek_nama" class="form-control col-md-8">
            </div>
            <div class="form-group row col-md-12">
              <label for="" class=" col-md-4">Kode Urutan Proyek</label>
              <input type="text" name="urutan_proyek_kode" id="urutan_proyek_kode" class="form-control col-md-8">
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" id="close" class="btn btn-secondary" data-dismiss="modal" onclick="fun_close()">Close</button>
            <button type="submit" class="btn btn-success pull-right" id="simpan">Simpan</button>
            <button type="submit" class="btn btn-primary pull-right" id="edit" style="display: none;">Edit</button>
            <button class="btn btn-primary" type="button" id="loading_form" disabled style="display: none;">
              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_detail">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Section Area</h4>
      </div>
      <div class="modal-body">
        <form id="form_modal_detail">
          <input type="text" name="temp_id_urutan_proyek" id="temp_id_urutan_proyek" style="display:none">
          <input type="text" name="section_area_id" id="section_area_id" class="form-control col-md-8" style="display:none">
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <label for="" class=" col-md-4">Nama Section Area</label>
              <input type="text" name="section_area_nama" id="section_area_nama" class="form-control col-md-8">
            </div>
            <div class="form-group row col-md-12">
              <label for="" class=" col-md-4">Kode Section Area</label>
              <input type="text" name="section_area_kode" id="section_area_kode" class="form-control col-md-8">
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" id="close_detail" class="btn btn-secondary" data-dismiss="modal" onclick="fun_close_detail()">Close</button>
            <button type="submit" class="btn btn-success pull-right" id="simpan_detail">Simpan</button>
            <button type="submit" class="btn btn-primary pull-right" id="edit_detail" style="display: none;">Edit</button>
            <button class="btn btn-primary" type="button" id="loading_form_detail" disabled style="display: none;">
              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
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
        "url": "<?= base_url() ?>master/urutan_proyek/getUrutanProyek",
        "dataSrc": ""
      },
      "columns": [
        {"data": "urutan_proyek_nama"}, 
        {"data": "urutan_proyek_kode"}, 
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="javascript:;" id="' + full.urutan_proyek_id + '" name="' + full.urutan_proyek_nama + '" title="Edit" onclick="fun_detail(this.id,this.name)"><i class="fa fa-search" ></i></a></center>';
          }
        }, 
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="javascript:;" id="' + full.urutan_proyek_id + '" title="Edit" onclick="fun_edit(this.id)"><i class="fa fa-edit" data-toggle="modal" data-target="#modal"></i></a></center>';
          }
        }, 
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="javascript:;" id="' + full.urutan_proyek_id + '" title="Delete" onclick="fun_delete(this.id)"><i class="fa fa-trash"></i></a></center>';
          }
        }
      ]
    });

    $('#table_detail').DataTable({
      "ajax": {
        "url": "<?= base_url() ?>master/urutan_proyek/getSectionArea?section_area_id=0",
        "dataSrc": ""
      },
      "columns": [
        {"data": "section_area_nama"}, 
        {"data": "section_area_kode"}, 
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="javascript:;" id="' + full.section_area_id + '" title="Edit" onclick="fun_edit_detail(this.id)"><i class="fa fa-edit" data-toggle="modal" data-target="#modal_detail"></i></a></center>';
          }
        }, 
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="javascript:;" id="' + full.section_area_id + '" title="Delete" onclick="fun_delete_detail(this.id)"><i class="fa fa-trash"></i></a></center>';
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

    $.getJSON('<?= base_url() ?>master/urutan_proyek/getUrutanProyek?urutan_proyek_id=' + id, function(json) {
      $('#urutan_proyek_id').val(json.urutan_proyek_id);
      $('#urutan_proyek_nama').val(json.urutan_proyek_nama);
      $('#urutan_proyek_kode').val(json.urutan_proyek_kode);
    });
  }

  $("#form_modal").on("submit", function(e) {
    e.preventDefault();
    var url = ($('#urutan_proyek_id').val() != '') ? '<?= base_url('master/urutan_proyek/updateUrutanProyek') ?>' : '<?= base_url('master/urutan_proyek/insertUrutanProyek') ?>';

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
        $.get('<?= base_url() ?>master/urutan_proyek/deleteUrutanProyek', {urutan_proyek_id: id}, function(data) {
          $('#close').click();
          toastr.success('Berhasil');
        });
      }
    });
  }

  function fun_detail(id, name) {
    $('#div_detail').show();
    $('#id_urutan_proyek').val(id);
    $('#table_detail').DataTable().ajax.url('<?= base_url('master/urutan_proyek/getSectionArea?id_urutan_proyek=') ?>' + id).load();
    $('html, body').animate({
      scrollTop: $("#div_detail").offset().top
    }, 10);
  }

  function fun_tambah_pegawai() {
    $('#temp_id_urutan_proyek').val($('#id_urutan_proyek').val());
    $('#modal_detail').modal('show');
  }

  function fun_edit_detail(id) {
    $('#modal_detail').modal('show');
    $('#simpan_detail').css('display', 'none');
    $('#edit_detail').css('display', 'block');

    $.getJSON('<?= base_url() ?>master/urutan_proyek/getSectionArea?section_area_id=' + id, function(json) {
      $('#temp_id_urutan_proyek').val(json.urutan_proyek_id);
      $('#section_area_id').val(json.section_area_id);
      $('#section_area_nama').val(json.section_area_nama);
      $('#section_area_kode').val(json.section_area_kode);
    });
  }

  $("#form_modal_detail").on("submit", function(e) {
    e.preventDefault();
    var url = ($('#section_area_id').val() != '') ? '<?= base_url('master/urutan_proyek/updateSectionArea') ?>' : '<?= base_url('master/urutan_proyek/insertSectionArea') ?>';

    $.ajax({
      url: url,
      data: $('#form_modal_detail').serialize(),
      type: 'POST',
      dataType: 'html',
      beforeSend: function() {
        $('#loading_form_detail').css('display', 'block');
        $('#simpan_detail').css('display', 'none');
        $('#edit_detail').css('display', 'none');
      },
      success: function(isi) {
        $('#close_detail').click();
        toastr.success('Berhasil');
      }
    });
  });

  function fun_delete_detail(id) {
    Swal.fire({
      title: "Apakah anda yakin akan menghapusnya?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#34c38f",
      cancelButtonColor: "#f46a6a",
      confirmButtonText: "Iya"
    }).then(function(result) {
      if (result.value) {
        $.get('<?= base_url() ?>master/urutan_proyek/deleteSectionArea', {section_area_id: id}, function(data) {
          $('#close_detail').click();
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
    $('#loading_form').css('display', 'none');
    $('#div_detail').hide();
  }
  
  $('#modal').on('hidden.bs.modal', function(e) {
    fun_close();
  });

  function fun_close_detail() {
    fun_loading();
    $('#table_detail').DataTable().ajax.reload();
    $('#simpan_detail').css('display', 'block');
    $('#edit_detail').css('display', 'none');
    $('#form_modal_detail')[0].reset();
    $('#modal_detail').modal('hide');
    $('#loading_form_detail').css('display', 'none');
  }

  $('#modal_detail').on('hidden.bs.modal', function(e) {
    fun_close_detail();
  });

  function fun_loading() {
    var simplebar = new Nanobar();
    simplebar.go(100);
  }
</script>