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
              <button type="button" class="btn btn-primary float-end" data-toggle="modal" data-target="#myModal" onclick="fun_tambah()">Tambah</button>
              <h4 class="card-title mb-4">Klasifikasi Pekerjaan</h4>
            </div>
            <table id="table" class="table table-bordered table-striped nowrap" width="100%">
              <thead class="table-primary">
                <tr>
                  <th style="text-align: center;">No</th>
                  <th style="text-align: center;">Nama Klasifikasi</th>
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
<div class="modal fade" id="myModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Klasifikasi Pekerjaan</h4>
      </div>
      <div class="modal-body">
        <form id="form_modal">
          <div class="card-body row">
            <div class="form-group row col-md-12">
              <label class="col-md-4">Nama Kualifikasi Pekerjaan</label>
              <input type="hidden" name="klasifikasi_pekerjaan_id" id="klasifikasi_pekerjaan_id" class="form-control col-md-8">
              <input type="text" name="klasifikasi_pekerjaan_nama" id="klasifikasi_pekerjaan_nama" class="form-control col-md-8">
            </div>
            <div class="form-group row col-md-12">
              <label class="col-md-4">RKAP / Non RKAP</label>
              <select name="klasifikasi_pekerjaan_rkap" id="klasifikasi_pekerjaan_rkap" class="form-control col-md-8">
                <option value="y">RKAP</option>
                <option value="n">Non RKAP</option>
              </select>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" id="close" class="btn btn-secondary" data-dismiss="modal" onclick="fun_close()">Close</button>
            <button type="submit"class="btn btn-success pull-right" id="simpan">Simpan</button>
            <button type="submit"class="btn btn-primary pull-right" id="edit" style="display: none;">Edit</button>
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
      "ajax": {
        "url": "<?= base_url() ?>master/klasifikasi_pekerjaan/getKlasifikasiPekerjaan",
        "dataSrc": ""
      },
      "columns": [
        {
          render: function(data, type, full, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }
        },
        {
          "render": function ( data, type, full, meta ) {
            var tipe = (full.klasifikasi_pekerjaan_rkap == 'y') ? 'RKAP' : 'Non RKAP';
            return tipe+' - '+full.klasifikasi_pekerjaan_nama;
          }
        },
        {
          "render": function ( data, type, full, meta ) {
            return '<center><a href="javascript:;" id="'+full.klasifikasi_pekerjaan_id+'" title="Edit" onclick="fun_edit(this.id)"><i class="fa fa-edit" data-toggle="modal" data-target="#modal"></i></a></center>';
          }
        },
        {
          "render": function ( data, type, full, meta ) {
            return '<center><a href="javascript:;" id="'+full.klasifikasi_pekerjaan_id+'" title="Delete" onclick="fun_delete(this.id)"><i class="fa fa-trash"></i></a></center>';
          }
        },
      ]
    });
    /* Isi Table */
  });

  function fun_tambah(){
    $('#myModal').modal('show');
  }

  function fun_edit(id){
    $('#myModal').modal('show');
    $('#simpan').css('display', 'none');
    $('#edit').css('display', 'block');

    $.getJSON('<?= base_url() ?>master/klasifikasi_pekerjaan/getKlasifikasiPekerjaan?klasifikasi_pekerjaan_id='+id, function(json) {
      $('#klasifikasi_pekerjaan_id').val(json.klasifikasi_pekerjaan_id);
      $('#klasifikasi_pekerjaan_nama').val(json.klasifikasi_pekerjaan_nama);
      $('#klasifikasi_pekerjaan_rkap').val(json.klasifikasi_pekerjaan_rkap);
    });
  }

  $("#form_modal").on("submit", function (e) {
    e.preventDefault();
    var url = ($('#klasifikasi_pekerjaan_id').val() != '') ? '<?= base_url('master/klasifikasi_pekerjaan/updateKlasifikasiPekerjaan') ?>' : '<?= base_url('master/klasifikasi_pekerjaan/insertKlasifikasiPekerjaan') ?>';

    $.ajax({
      url:url,
      data:$('#form_modal').serialize(),
      type:'POST',
      dataType: 'html',
      beforeSend:function () {
        $('#loading_form').css('display', 'block');
        $('#simpan').css('display', 'none');
        $('#edit').css('display', 'none');
      }, success:function(isi) {
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
    }).then(function (result) {
      if (result.value) {
        $.get('<?= base_url() ?>master/klasifikasi_pekerjaan/deleteKlasifikasiPekerjaan', {klasifikasi_pekerjaan_id: id}, function(data) {
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
    $('#myModal').modal('hide');
    $('#loading_form').css('display', 'none');
  }

  $('#myModal').on('hidden.bs.modal', function (e) {
    fun_close();
  });

  function fun_loading() {
    var simplebar = new Nanobar();
    simplebar.go(100);
  }
</script>