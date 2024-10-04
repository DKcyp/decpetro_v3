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
            <h4 class="card-title mb-4">Filter IFC</h4>
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
            <h4 class="card-title mb-4">Dokumen IFC</h4>
            <table id="table" class="table table-bordered table-striped nowrap" width="100%">
              <thead class="table-primary">
                <tr>
                  <th>No Pekerjaan</th>
                  <th>Waktu Pekerjaan</th>
                  <th>Nama Pekerjaan</th>
                  <th>Detail</th>
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


<script type="text/javascript">
  $(function() {
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
        "url": "<?= base_url() ?>project/pekerjaan_usulan/getPekerjaanUsulan",
        "dataSrc": ""
      },
      "columns": [{
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
          "data": "tanggal_awal"
        },
        {
          "data": "pekerjaan_judul"
        },
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" name="' + full.pekerjaan_status + '" title="Edit" onclick="fun_detail(this.id,this.name)"><i class="fas fa-search"></i></a></center>';
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

  function fun_detail(id, val) {
    call_ajax_page('project/pekerjaan_usulan/detailPekerjaan?aksi=ifc&pekerjaan_id=' + id + '&status=' + val);
  }
</script>