<div class="page-content">
  <div class="container-fluid">

    <!-- start page title -->
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
          <h4 class="card-title mb-4">Pekerjaan</h4>

        </div>
      </div>
    </div>
    <!-- end page title -->

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title mb-4">Filter IFA</h4>
            <form id="filter">
              <div class="row">
                <div class="form-group col-md-5">
                  <label>Perencana</label>
                  <select class="form-control select2" id="id_user_cari" name="id_user_cari">

                  </select>
                </div>
                <div class="form-group col-md-2">
                  <label>&emsp;</label>
                  <button class="btn btn-primary form-control" type="button" name="cari" id="cari">Cari</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title mb-4">Dokumen IFA</h4>
            <input type="text" name="user_session" id="user_session" style="display:none">
            <table id="table" class="table table-bordered table-striped nowrap" width="100%">
              <thead class="table-primary">
                <tr>
                  <th style="text-align: center;">No Pekerjaan</th>
                  <th style="text-align: center;">Waktu Pekerjaan</th>
                  <th style="text-align: center;">Batas Waktu Pekerjaan</th>
                  <th style="text-align: center;">Nama Pekerjaan</th>
                  <th style="text-align: center;">Peminta Jasa</th>
                  <th style="text-align: center;">Detail</th>
                  <th style="text-align: center;">Ajuan Extend</th>
                  <th style="text-align: center;">Extend</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->

<!-- MODAL -->
<div class="modal fade" id="modal_ajuan_extend">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Pengajuan Extend Dokumen IFC</h4>
      </div>
      <div class="modal-body">
        <div class="card-body row" id="formDiv">
          <form id="form_modal_ajuan_extend">
            <input type="hidden" name="extend_id_ajuan_extend" id="extend_id_ajuan_extend">
            <input type="hidden" name="id_pekerjaan_ajuan_extend" id="id_pekerjaan_ajuan_extend">
            <input type="hidden" name="pekerjaan_status_ajuan_extend" id="pekerjaan_status_ajuan_extend">
            <div class="card-body row">
              <div class="form-group row col-md-12">
                <label class="col-md-4">Batas Waktu Pekerjaan</label>
                <input type="number" name="pekerjaan_waktu_ajuan_extend" id="pekerjaan_waktu_ajuan_extend" class="form-control col-md-8">
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" id="close_ajuan_extend" class="btn btn-default" data-dismiss="modal" onclick="fun_close()">Close</button>
              <input type="submit" class="btn btn-success pull-right" id="simpan_ajuan_extend" value="Simpan">
              <input type="submit" class="btn btn-primary pull-right" id="edit_ajuan_extend" value="Edit" style="display: none;">
              <button class="btn btn-primary" type="button" id="loading_form_ajuan_extend" disabled style="display: none;">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading..
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- MODAL -->

<!-- MODAL -->
<div class="modal fade" id="modal_extend">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Extend Dokumen IFC</h4>
      </div>
      <div class="modal-body">
        <div class="card-body row" id="formDiv">
          <form id="form_modal_extend">
            <input type="hidden" name="extend_id_extend" id="extend_id_extend">
            <input type="hidden" name="id_pekerjaan_extend" id="id_pekerjaan_extend">
            <input type="hidden" name="pekerjaan_status_extend" id="pekerjaan_status_extend">
            <div class="card-body row" id="dokumen">
              <div class="form-group row col-md-12">
                <label class="col-md-4">Batas Waktu Pekerjaan</label>
                <input type="number" name="pekerjaan_waktu_extend" id="pekerjaan_waktu_extend" class="form-control col-md-8">
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" id="close_extend" class="btn btn-default" data-dismiss="modal" onclick="fun_close()">Close</button>
              <input type="submit" class="btn btn-success pull-right" id="simpan_extend" value="Simpan">
              <input type="submit" class="btn btn-primary pull-right" id="edit_extend" value="Edit" style="display: none;">
              <button class="btn btn-primary" type="button" id="loading_form_extend" disabled style="display: none;">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading..
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- MODAL -->

<script type="text/javascript">
  $(function() {

    // START GET SESSION USER
    $.getJSON('<?= base_url('project/pekerjaan_usulan/getUserSession') ?>', function(json, result) {
      $('#user_session').val(json.pegawai_nik);
    })
    // END GET SESSION USER

    $('#table thead tr').clone(true).addClass('filters').appendTo('#table thead');
    $('#table').DataTable({
      orderCellsTop: true,
      initComplete: function() {
        var api = this.api();
        // For each column
        api
          .columns()
          .eq(0)
          .each(function(colIdx) {
            // Set the header cell to contain the input element
            var cell = $('.filters th').eq(
              $(api.column(colIdx).header()).index()
            );
            var title = $(cell).text();
            $(cell).html('<input type="text" class="form-control" style="width:100%" placeholder="' + title + '" />');

            // On every keypress in this input
            $(
                'input',
                $('.filters th').eq($(api.column(colIdx).header()).index())
              )
              .off('keyup change')
              .on('keyup change', function(e) {
                e.stopPropagation();

                // Get the search value
                $(this).attr('title', $(this).val());
                var regexr = '({search})'; //$(this).parents('th').find('select').val();

                var cursorPosition = this.selectionStart;
                // Search the column for that value
                api
                  .column(colIdx)
                  .search(
                    this.value != '' ?
                    regexr.replace('{search}', '(((' + this.value + ')))') :
                    '',
                    this.value != '',
                    this.value == ''
                  )
                  .draw();

                $(this)
                  .focus()[0]
                  .setSelectionRange(cursorPosition, cursorPosition);
              });
          });
      },
      "scrollX": true,
      "ajax": {
        "url": "<?= base_url('project/') ?>pekerjaan_usulan/getPekerjaanUsulan",
        "dataSrc": ""
      },
      "columns": [{
          render: function(data, type, full, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }
        },
        {
          "data": "tanggal_awal"
        },
        {
          "data": "tanggal_akhir"
        },
        {
          render: function(data, type, full, meta) {
            var nomor = '';
            if (full.milik == 'y' && full.pekerjaan_nomor == null) {
              nomor = '';
            } else if (full.milik == 'y') {
              nomor = '<span class="badge" style="background-color:#c13333 ">' + full.pekerjaan_nomor + '</span>';
            } else {
              nomor = full.pekerjaan_nomor;
            }
            return nomor;
          }
        },
        {
          "data": "pegawai_nama"
        },
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" name="' + full.pekerjaan_status + '" title="Edit" onclick="fun_detail_disposisi(this.id,this.name)"><i class="fas fa-search"></i></a></center>';
          }
        },
        {
          "render": function(data, type, full, meta) {
            console.log(full.pic);
            return (full.pic == $('#user_session').val()) ?
              '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" name="' + full.pekerjaan_status + '" title="Ajuan Extend" onclick="fun_ajuan_extend(this.id,this.name)"><i class="fas fa-share" data-toggle="modal" data-target="#modal_ajuan_extend"></i></a></center>' :
              '<center>-</center>';
          }
        },
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" name="' + full.pekerjaan_status + '" title="Extend" onclick="fun_extend(this.id,this.name)"><i class="fas fa-share" data-toggle="modal" data-target="#modal_extend"></i></a></center>';
          }
        },
      ]
    });
    /* Isi Table */
  });

  /* SELECT 2 */
  $('#id_user_cari').select2({
    // dropdownParent: $('#modal_upload'),
    allowClear: true,
    placeholder: 'Pilih',
    ajax: {
      delay: 250,
      url: '<?= base_url('project/pekerjaan_usulan/getUserStaf') ?>',
      dataType: 'json',
      type: 'GET',
      data: function(params) {
        var queryParameters = {
          pegawai_nama: params.term
        }

        return queryParameters;
      },
    }
  })

  // ('.select2-selection').css({
  //   height: 'auto',
  //   margin: '0px -10px 0px -10px'
  // });
  $('.select2').css('width', '100%');

  /* SELECT 2 */

  /* FROM CARI SUBMIT */
  $('#cari').on('click', function(e) {
    e.preventDefault();
    var data = $('#filter').serialize();
    $('#table').DataTable().ajax.url('<?= base_url('project/pekerjaan_usulan/getPekerjaanUsulan?') ?>' + data).load();
  })
  /* FROM CARI SUBMIT */

  // AJUAN EXTEND
  function fun_ajuan_extend(id, status) {
    $('#modal_ajuan_extend').modal('show');
    $('#id_pekerjaan_ajuan_extend').val(id);
    $('#pekerjaan_status_ajuan_extend').val(status);
    $.getJSON('<?= base_url('project/dokumen_IFA/getExtend') ?>', {
      id_pekerjaan: id,
      pekerjaan_disposisi_status: status,
      // extend_status: 'y'
    }, function(json, result) {
      $('#extend_id_ajuan_extend').val(json.extend_id);
      $('#pekerjaan_waktu_ajuan_extend').val(json.extend_hari);
      if (json.extend_id != null) {
        $('#edit_ajuan_extend').show();
        $('#simpan_ajuan_extend').hide();
      } else {
        $('#edit_ajuan_extend').hide();
        $('#simpan_ajuan_extend').show();
      }
    })

  }

  $('#modal_ajuan_extend').on('submit', function(e) {
    if ($('#extend_id_ajuan_extend').val() != '')
      var url = '<?= base_url('project/dokumen_IFA/updateAjuanExtend') ?>';
    else var url = '<?= base_url('project/dokumen_iFA/insertAjuanExtend') ?>';

    var data = new FormData();

    data.append('id_pekerjaan', $('#id_pekerjaan_ajuan_extend').val());
    data.append('extend_id', $('#extend_id_ajuan_extend').val());
    data.append('pekerjaan_disposisi_status', $('#pekerjaan_status_ajuan_extend').val());
    data.append('extend_hari', $('#pekerjaan_waktu_ajuan_extend').val());
    data.append('extend_status', 'y');

    console.log(data);

    e.preventDefault();
    $.ajax({
      url: url,
      type: 'post',
      data: data,
      dataType: 'HTML',
      processData: false,
      contentType: false,
      beforeSend: function() {
        $('#loading_form').show();
        $('#simpan_ajuan_extend').hide();
        $('#edit_ajuan_extend').hide();
      },
      complete: function() {
        $('#loading_form').hide();
        $('#simpan_ajuan_extend').show();
        $('#edit_ajuan_extend').hide();
      },
      success: function(data) {
        $('#close_extend').click();
      }
    });
  })
  // AJUAN EXTEND

  // EXTEND
  function fun_extend(id, status) {
    $('#modal_extend').modal('show');
    $('#id_pekerjaan_extend').val(id);
    $('#pekerjaan_status_extend').val(status);
    $.getJSON('<?= base_url('project/dokumen_IFA/getExtend') ?>', {
      id_pekerjaan: id,
      pekerjaan_disposisi_status: status,
      // extend_status: '1'
    }, function(json, result) {
      $('#extend_id_extend').val(json.extend_id);
      $('#pekerjaan_waktu_extend').val(json.extend_hari);
      if (json.extend_id != null) {
        $('#edit_extend').show();
        $('#simpan_extend').hide();
      } else {
        $('#edit_extend').hide();
        $('#simpan_extend').show();
      }
    })
  }

  $('#modal_extend').on('submit', function(e) {
    if ($('#extend_id_extend').val() != '')
      var url = '<?= base_url('project/dokumen_IFA/updateAjuanExtend') ?>';
    else var url = '<?= base_url('project/dokumen_iFA/insertAjuanExtend') ?>';

    var data = new FormData();

    data.append('id_pekerjaan', $('#id_pekerjaan_extend').val());
    data.append('extend_id', $('#extend_id_extend').val());
    data.append('pekerjaan_disposisi_status', $('#pekerjaan_status_extend').val());
    data.append('extend_hari', $('#pekerjaan_waktu_extend').val());
    data.append('extend_status', 'n');

    console.log(data);

    e.preventDefault();
    $.ajax({
      url: url,
      type: 'post',
      data: data,
      dataType: 'HTML',
      processData: false,
      contentType: false,
      beforeSend: function() {
        $('#loading_form').show();
        $('#simpan_extend').hide();
        $('#edit_extend').hide();
      },
      complete: function() {
        $('#loading_form').hide();
        $('#simpan_extend').show();
        $('#edit_extend').hide();
      },
      success: function(data) {
        $('#close_extend').click();
      }
    });
  })

  // EXTEND

  function fun_tambah() {
    $('#myModal').modal('show');
  }

  function fun_close() {
    $('#simpan_ajuan_extend').css('display', 'block');
    $('#simpan_extend').css('display', 'block');
    $('#edit_ajuan_extend').css('display', 'none');
    $('#edit_extend').css('display', 'none');
    $('#table_data').css('display', 'none');
    $('#tableDiv').css('display', 'none');
    $('#formDiv').css('display', 'block');
    $('#form_modal_ajuan_extend')[0].reset();
    $('#form_modal_extend')[0].reset();
    $('#modal_ajuan_extend').modal('hide');
    $('#modal_extend').modal('hide');
    $('#table').DataTable().ajax.reload(null, false);
  }

  function fun_edit() {
    $('#myModal').modal('show');
    $('#simpan').css('display', 'none');
    $('#edit').css('display', 'block');
  }

  function fun_detail(id) {
    $('#detailTable').css('display', 'block');
    $('#table_detail').DataTable().ajax.url('<?= base_url('project/') ?>pekerjaan_usulan/getAsetDocument?id_pekerjaan=' + id).load();
    $('html, body').animate({
      scrollTop: $("#detailTable").offset().top
    }, 10);
  }

  function fun_detail_disposisi(id, val) {
    call_ajax_page('project/pekerjaan_usulan/detailPekerjaan?aksi=ifa&pekerjaan_id=' + id + '&status=' + val);
  }


  function fun_verif() {
    $('#myModal').modal('show');
    $('#simpan').css('display', 'none');
    $('#edit').css('display', 'block');
    $('#dokumen').css('display', 'block');
  }

  function fun_lihat(data) {
    $('#document').remove();
    $('#div_document').append('<iframe src="https://docs.google.com/viewer?url=<?= base_url('document/') ?>' + data + '&embedded=true" frameborder="0" id="document" width="100%" height="350px"></iframe>');
    $('#modal_lihat').modal('show');
  }

  function fun_close_lihat() {
    $('#modal_lihat').modal('hide');
  }
</script>