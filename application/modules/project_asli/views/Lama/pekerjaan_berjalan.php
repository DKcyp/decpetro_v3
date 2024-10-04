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
            <h4 class="card-title mb-4">Filter Pekerjaan Berjalan</h4>
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
            <h4 class="card-title mb-4">Pekerjaan Berjalan</h4>
            <table id="table" class="table table-bordered table-striped nowrap" width="100%">
              <thead class="table-primary">
                <tr>
                  <th style="text-align: center;" rowspan="2">No</th>
                  <th style="text-align: center;" rowspan="2">No Pekerjaan</th>
                  <th style="text-align: center;" rowspan="2">Nama Kegiatan</th>
                  <th style="text-align: center;" rowspan="2">User/PIC</th>
                  <th style="text-align: center;" rowspan="2">Progress</th>
                  <th style="text-align: center;" colspan="5">Perencana</th>
                  <th style="text-align: center;" rowspan="2">Man Power</th>
                  <th style="text-align: center;" rowspan="2">Jadwal Engineering Finish</th>
                  <th style="text-align: center;" rowspan="2">Detail</th>
                </tr>
                <tr>
                  <th style="text-align: center;">Proses</th>
                  <th style="text-align: center;">Mekanikal</th>
                  <th style="text-align: center;">Listrik</th>
                  <th style="text-align: center;">Instrument</th>
                  <th style="text-align: center;">Sipil</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
    <!-- end row -->

  </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->

<script type="text/javascript">
  $(function() {
    fun_loading();

    /* Isi Table */
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
        "url": "<?= base_url('project/') ?>pekerjaan_usulan/getPekerjaanBerjalan",
        "dataSrc": ""
      },
      "columns": [{
          render: function(data, type, full, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }
        },
        {
          "render": function(data, type, full, meta) {
            //   var session = "<?php echo ($this->session->userdata('pegawai_nik')); ?>";

            //   if (full.pekerjaan_status == 0) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Draft</span>' : 'Draft';
            //   else if (full.pekerjaan_status == 1) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Send</span>' : 'Send';
            //   else if (full.pekerjaan_status == 2) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Reviewed AVP Customer</span>' : 'Reviewed AVP Customer';
            //   else if (full.pekerjaan_status == 3) return (full.milik == 'y') ? '<span class="badge bg-" style="background-color:#c13333 ">Approved VP Customer</span>' : 'Approved VP Customer';
            //   else if (full.pekerjaan_status == 4) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Approved VP Cangun</span>' : 'Approved VP Cangun';
            //   else if (full.pekerjaan_status == 5) return 'In Progress';
            //   else if (full.pekerjaan_status == 6) return 'Pekerjaan Berjalan';
            //   else if (full.pekerjaan_status == 7) return 'Pekerjaan Berjalan';
            //   else if (full.pekerjaan_status == 8) return 'IFA';
            //   else if (full.pekerjaan_status == 9) return 'IFC';
            //   else if (full.pekerjaan_status == 10) return 'IFC';
            //   else if (full.pekerjaan_status == 11) return 'IFC';
            //   else if (full.pekerjaan_status == 12) return 'Selesai';
            //   else if (full.pekerjaan_status == 15) return 'Selesai';
            //   else if (full.pekerjaan_status == '-') return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Reject</span>' : 'Reject';
            //   else return full.pekerjaan_status;
            var status = '';
            var warna = '';
            if (full.pekerjaan_status == 0) {
              status = 'Draft';
              warna = '#FFF000';
            } else if (full.pekerjaan_status == 1) {
              warna = '#FF8000';
              status = 'Send';
            } else if (full.pekerjaan_status == 2) {
              warna = '#009900';
              status = 'Reviewed AVP Customer';
            } else if (full.pekerjaan_status == 3) {
              warna = '#66CC00';
              status = 'Approved VP Customer'
            } else if (full.pekerjaan_status == 4) {
              warna = '#00FF00';
              status = 'Approved VP Cangun'
            } else if (full.pekerjaan_status == 5) {
              warna = '#CC6600';
              status = 'In Progress'
            } else if (full.pekerjaan_status == 6) {
              warna = '#3333FF';
              status = 'Pekerjaan Berjalan'
            } else if (full.pekerjaan_status == 7) {
              warna = '#3333FF';
              status = 'Pekerjaan Berjalan'
            } else if (full.pekerjaan_status == 8) {
              warna = '#FF33FF';
              status = 'IFA'
            } else if (full.pekerjaan_status == 9) {
              warna = '#B266FF';
              status = 'IFC'
            } else if (full.pekerjaan_status == 10) {
              warna = '#B266FF';
              status = 'IFC'
            } else if (full.pekerjaan_status == 11) {
              warna = '#B266FF';
              status = 'IFC'
            } else if (full.pekerjaan_status == 12) {
              warna = '#00FFFF';
              status = 'Selesai'
            } else if (full.pekerjaan_status == 15) {
              warna = '#00FFFF';
              status = 'Selesai'
            } else if (full.pekerjaan_status == '-') {
              warna = '#FF0000';
              status = 'Reject'
            }

            return '<span class="badge" style="background-color: ' + warna + '">' + status + '</span>';
          }
        },
        // {
        //   "render": function(data, type, full, meta) {
        //     var session = "<?php echo ($this->session->userdata('pegawai_nik')); ?>";

        //     if (full.pekerjaan_status == 0) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Draft</span>' : 'Draft';
        //     else if (full.pekerjaan_status == 1) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Send</span>' : 'Send';
        //     else if (full.pekerjaan_status == 2) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Reviewed AVP Customer</span>' : 'Reviewed AVP Customer';
        //     else if (full.pekerjaan_status == 3) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Approved VP Customer</span>' : 'Approved VP Customer';
        //     else if (full.pekerjaan_status == 4) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Approved VP Cangun</span>' : 'Approved VP Cangun';
        //     else if (full.pekerjaan_status == 5) return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">In Progress</span>' : 'In Progress';
        //     else if (full.pekerjaan_status == '-') return (full.milik == 'y') ? '<span class="badge" style="background-color:#c13333 ">Reject</span>' : 'Reject';
        //     else return full.pekerjaan_status;
        //   }
        // },
        {
          "data": "pekerjaan_judul"
        },
        {
          "data": "pegawai_nama"
        },
        {
          "data": "pekerjaan_progress"
        },
        {
          render: function(data, type, full, meta) {
            return full.pekerjaan_proses;
          }
        },
        {
          render: function(data, type, full, meta) {
            return full.pekerjaan_mesin;
          }
        },
        {
          render: function(data, type, full, meta) {
            return full.pekerjaan_listrik;
          }
        },
        {
          render: function(data, type, full, meta) {
            return full.pekerjaan_instrumen;
          }
        },
        {
          render: function(data, type, full, meta) {
            return full.pekerjaan_sipil;
          }
        },
        {
          "data": "total"
        },
        {
          "data": "tanggal_akhir"
        },
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" name="' + full.pekerjaan_status + '" title="Edit" onclick="fun_detail(this.id,this.name)"><i class="fas fa-search" data-toggle="modal" data-target="#modal"></i></a></center>';
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
    $('#table').DataTable().ajax.url('<?= base_url('project/pekerjaan_usulan/getPekerjaanBerjalan?') ?>' + data).load();
  })
  /* FROM CARI SUBMIT */

  function fun_detail(id, val) {
    call_ajax_page('project/pekerjaan_usulan/detailPekerjaan?aksi=berjalan&pekerjaan_id=' + id + '&status=' + val);
  }

  function fun_loading() {
    var simplebar = new Nanobar();
    simplebar.go(100);
  }
</script>