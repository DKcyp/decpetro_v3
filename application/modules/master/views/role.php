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
              <h4 class="card-title mb-4">Role</h4>
            </div>
            <table id="table" class="table table-bordered table-striped nowrap" width="100%">
              <thead class="table-primary">
                <tr>
                  <th style="text-align: center;">No</th>
                  <th style="text-align: center;">Nama Role</th>
                  <th style="text-align: center;">Menu</th>
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
  <!-- MODAL 1 -->
    <div class="modal fade" id="myModal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Role</h4>
          </div>
          <div class="modal-body">
            <form id="form_modal">
              <div class="card-body row">
                <div class="form-group row col-md-12">
                  <label class="col-md-4">Nama Role</label>
                  <input type="hidden" name="rol_id" id="rol_id" class="form-control col-md-8">
                  <input type="text" name="rol_name" id="rol_name" class="form-control col-md-8">
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
  <!-- MODAL 1 -->

  <!-- MODAL 2 -->
    <div class="modal fade" id="myModalMenu">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Menu</h4>
          </div>
          <div class="modal-body">
            <form id="form_modal_menu">
              <table border="1" width="100%" class="table table-bordered table-striped nowrap w-100">
                <input type="text" id="role_id_temp" name="role_id_temp" value="" style="display: none;">
                <thead class="table-success">
                  <tr>
                    <th><center>Menu</center></th>
                    <th><center>Aktif</center></th>
                  </tr>
                </thead>
                <tbody>
                  <?php $list_menu = $this->db->query("SELECT * FROM global.global_menu ORDER BY menu_urut ASC"); ?>
                  <?php foreach ($list_menu->result() as $value): ?>
                    <tr>
                      <td><?= $value->menu_nama ?></td>
                      <td align="center"><input type="checkbox" id="<?= $value->menu_id ?>" name="menu[]" value="<?= $value->menu_id ?>"></td>
                    </tr>
                  <?php endforeach ?>
                </tbody>
              </table>
              <div class="modal-footer justify-content-between">
                <button type="button" id="close_menu" class="btn btn-secondary" data-dismiss="modal" onclick="fun_close2()">Close</button>
                <button type="submit"class="btn btn-success pull-right" id="simpan_menu">Simpan</button>
                <button type="submit"class="btn btn-primary pull-right" id="edit_menu" style="display: none;">Edit</button>
                <button class="btn btn-primary" type="button" id="loading_form_menu" disabled style="display: none;">
                  <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                  Loading...
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  <!-- MODAL 2 -->
<!-- MODAL -->

<script type="text/javascript">
  $(function () {
    fun_loading();

    /* Isi Table */ 
      $('#table').DataTable({
        "scrollX": true,
        "ajax": {
            "url": "<?= base_url() ?>/master/role/getRole",
            "dataSrc": ""
          },
          "columns": [
            {render: function ( data, type, full, meta ) {
                return meta.row + meta.settings._iDisplayStart + 1;
              }
            },
            {"data": "rol_name"},
            {"render": function ( data, type, full, meta ) {
              return '<center><a href="javascript:;" id="'+full.rol_id+'" title="Menu" onclick="fun_menu(this.id)"><i class="fa fa-bars" data-toggle="modal" data-target="#MyModalMenu"></i></a></center>';
              }
            },
            {"render": function ( data, type, full, meta ) {
              return '<center><a href="javascript:;" id="'+full.rol_id+'" title="Edit" onclick="fun_edit(this.id)"><i class="fa fa-edit" data-toggle="modal" data-target="#myModal"></i></a></center>';
              }
            },
            {"render": function ( data, type, full, meta ) {
              return '<center><a href="javascript:;" id="'+full.rol_id+'" title="Edit" onclick="fun_delete(this.id)"><i class="fa fa-trash"></i></a></center>';
              }
            },
          ]
      });
    /* Isi Table */
  });

  /* Fun Menu */
    function fun_menu(id) {
      $('#myModalMenu').modal('show');
      $.getJSON('<?= base_url('master/role/getMenuRole') ?>', {rol_id: id}, function(json) {
        $.each(json, function(index, val) {
          $('#'+val.menu_id).prop('checked', true);
        });
        $('#role_id_temp').val(id);
      });
    }
  /* Fun Menu */

  /* Proses */
    $("#form_modal").on("submit", function (e) {
      if ($('#rol_id').val() != '') var url = '<?= base_url('master/role/updateRole') ?>';
      else var url = '<?= base_url('master/role/insertRole') ?>';

      e.preventDefault();
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
  /* Proses */

  /* Proses Menu */
    $("#form_modal_menu").on("submit", function (e) {
      url = '<?= base_url('master/role/insertMenuRole') ?>';

      e.preventDefault();
      $.ajax({
        url:url,
        data:$('#form_modal_menu').serialize(),
        type:'POST',
        dataType: 'html',
        beforeSend:function () {
          $('#loading_form_menu').css('display', 'block');
          $('#simpan_menu').css('display', 'none');
          $('#edit_menu').css('display', 'none');
        },success:function(isi) {
          $('#close_menu').click();
          toastr.success('Berhasil');
        }
      });
    });
  /* Proses Menu */

  /* Fun Delete */
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
          $.get('<?= base_url() ?>master/role/deleteRole', {rol_id: id}, function(data) {
            $('#close').click();
            toastr.success('Berhasil');
          });
        }
      });
    }
  /* Fun Delete */

  function fun_tambah(){
    $('#myModal').modal('show');
  }

  /* Fun Close */
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
  /* Fun Close */

  /* View Update */
    function fun_edit(id) {
      $('#myModal').modal('show');
      $('#simpan').css('display', 'none');
      $('#edit').css('display', 'block');
      $.getJSON('<?= base_url('master/role/getRole') ?>', {rol_id: id}, function(json) {
        $('#rol_name').val(json.rol_name);
        $('#rol_id').val(json.rol_id);
      });
    }
  /* View Update */

  /* Fun Close */
    function fun_close2() {
      fun_loading();
      $('#table').DataTable().ajax.reload();
      $('#simpan_menu').css('display', 'block');
      $('#edit_menu').css('display', 'none');
      $('#table_data').css('display', 'none');
      $('#form_modal_menu')[0].reset();
      $('#myModalMenu').modal('hide');
      $('#loading_form_menu').css('display', 'none');
    }
  /* Fun Close */

  $('#myModal').on('hidden.bs.modal', function (e) {
    fun_close();
  });

  $('#myModalMenu').on('hidden.bs.modal', function (e) {
    fun_close2();
  });

  function fun_loading() {
    var simplebar = new Nanobar();
    simplebar.go(100);
  }
</script>