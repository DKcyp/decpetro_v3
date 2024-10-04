<link rel="stylesheet" type="text/css" href="<?= base_url('assets_tambahan/') ?>easyui/themes/default/easyui.css">
<link rel="stylesheet" type="text/css" href="<?= base_url('assets_tambahan/') ?>easyui/themes/icon.css">
<div class="page-content">
  <div class="container-fluid">
    <!-- start page title -->
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
          <h4 class="card-title mb-2">CALENDAR</h4>
        </div>
      </div>
    </div>
    <!-- end page title -->
    <!-- Start Filter Pekerjaan -->
    <div class="row" id="div_filter" style="display:block">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title mb-4">Filter User</h4>
            <form id="submit_cari">
              <div class="row">
                <div class="form-group col-md-5">
                  <label>User</label>
                  <select class="form-control select2" id="id_user_cari" name="id_user_cari" required>
                  </select>
                </div>
                <div class="form-group col-md-2">
                  <label>&emsp;</label>
                  <button class="btn btn-primary form-control" type="submit" name="tombol_cari" id="tombol_cari">Cari</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- End Filter Pekerjaan -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div id='calendar'></div>
          </div>
        </div>
      </div>
    </div>

    <!-- end page title -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div id="table_kegiatan_judul">
            </div>
            <table id="table" class="table table-bordered table-striped nowrap" width="100%">
              <thead class="table-primary">
                <tr>
                  <th style="text-align: center;">Judul Pekerjaan</th>
                  <th style="text-align: center;">Waktu awal</th>
                  <th style="text-align: center;">Waktu akhir</th>
                  <th style="text-align: center;">Nama</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- end row -->
  </div>
  <!-- container-fluid -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.10/index.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script>
    $(function () {
      $('#id_user_cari').select2({
        placeholder: 'Pilih',
        allowClear:'true',
        ajax: {
          delay: 250,
          url: '<?= base_url('calendar/get_user_list') ?>',
          dataType: 'json',
          type: 'GET',
          data: function(params) {
            var queryParameters = {
              pegawai_nama: params.term
            }

            return queryParameters;
          },
        }
      });
      $('.select2-selection').css({
        height: 'auto',
        margin: '0px -10px 0px -10px'
      });
      $('.select2').css('width', '100%');
      render_calender(user='');
    });

    $('#submit_cari').on('submit',function(e){
      e.preventDefault();
      var user = $('#id_user_cari').val();
      render_calender(user);
    })

    const date = new Date();

    function render_calender(user='') {
      var events = '<?= base_url() ?>/calendar/show_awal?user='+user;
      var calendarEl = document.getElementById('calendar');
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: events,
        datesSet: function(info) {
              // Get the currently displayed date
          var currentDate = info.view.currentStart;

              // Displayed month information with month name
          var monthnum = currentDate.toLocaleDateString(undefined, { month: '2-digit' });
          var yearnum = currentDate.toLocaleDateString(undefined, { year: 'numeric' });
          var displayedMonth = new Intl.DateTimeFormat('en', { month: 'long', year: 'numeric' }).format(currentDate);

              // Log the displayed month
          $('#table_kegiatan_judul').html('<h4>'+displayedMonth+'</h4>');

          $('#table').DataTable().ajax.url('<?=base_url()?>calendar/show_table?bln='+monthnum+'&thn='+yearnum+'&user='+user).load();
          console.log('Currently displayed month:', displayedMonth, monthnum, yearnum);
        }
      });
      calendar.render();
    }

    $('#table').DataTable({
      "scrollX": true,
      "ajax": {
        "url": "<?= base_url() ?>calendar/show_table?bln="+parseInt(date.getMonth()+1)+"&thn="+date.getFullYear()+"&user=0",
        "dataSrc": ""
      },
      "columns": [
        {"data": "pekerjaan_judul"},
        {"data": "pekerjaan_disposisi_waktu"},
        {"data": "pekerjaan_waktu_akhir"},
        {"data": "pegawai_nama"},
        ],    
      "createdRow": function (row, data, dataIndex) {
        if (data.pekerjaan_prioritas == '2') {
          $(row).addClass('bg-warning');
        }
      }
    });
  </script>