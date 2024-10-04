<!-- Theme style -->
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/jqvmap/jqvmap.min.css">
<!-- overlayScrollbars -->
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
<!-- summernote -->
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/summernote/summernote-bs4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>assets/dist/css/adminlte.min.css">

<div class="page-content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
          <h4 class="card-title mb-2">Dashboard</h4>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <form id="filter">
              <div class="row">
                <div class="form-group col-3">
                  <label>Bulan Pekerjaan</label>
                  <select class="form-control" id="bulan" name="bulan">
                    <option value="">- Semua -</option>
                    <?php foreach ($bulan as $key => $value): ?>
                      <option value="<?= $key ?>"><?= $value ?></option>
                    <?php endforeach ?>
                  </select>
                </div>
                <div class="form-group col-3">
                  <label>Tahun Pekerjaan</label>
                  <select class="form-control" id="tahun" name="tahun">
                    <option value="">- Semua -</option>
                    <?php foreach ($dataTahun as $value) : ?>
                      <option value="<?= $value['tahun'] ?>"><?= $value['tahun'] ?></option>
                    <?php endforeach ?>
                  </select>
                </div>
                <div class="form-group col-md-2">
                  <label>&emsp;</label>
                  <button class="btn btn-primary form-control d-print-none" type="submit" name="cari_filter" id="cari_filter">Cari</button>
                </div>                  
                <div class="form-group col-md-2">
                  <label>&emsp;</label>
                  <button class="btn btn-danger form-control d-print-none" type="button" name="" id="" onclick="printPage();">Cetak</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div  class="row">
      <div class=" <?= ($this->session->userdata('pegawai_unit_id') == 'E53000') ? 'col-lg-3' : 'col-lg-4'; ?>">
        <div class="small-box bg-info">
          <div class="inner">
            <h3 id="pekerjaan_berjalan_total"></h3>
            <p>Pekerjaan Berjalan</p>
          </div>
          <div class="icon">
            <i class="fas fa-edit"></i>
          </div>
        </div>

        <div class="small-box">
          <div class="chart">
            <center><canvas class="m-0 pb-3" id="donutBerjalanChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100% !important;"></canvas></center>
          </div>
        </div>
      </div>

      <div class=" <?= ($this->session->userdata('pegawai_unit_id') == 'E53000') ? 'col-lg-3' : 'col-lg-4' ; ?>">
        <div class="small-box bg-success">
          <div class="inner">
            <h3 id="pekerjaan_selesai_total"></h3>
            <p>Pekerjaan Selesai</p>
          </div>
          <div class="icon">
            <i class="fas fa-thumbs-up"></i>
          </div>
        </div>

        <div class="small-box">
          <div class="chart">
            <center><canvas class="m-0 pb-3" id="donutSelesaiChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100% !important;"></canvas></center>
          </div>
        </div>
      </div>

      <div class=" <?= ($this->session->userdata('pegawai_unit_id') == 'E53000') ? 'col-lg-3' : 'col-lg-4' ; ?>">
        <div class="small-box bg-warning">
          <div class="inner">
            <h3 id="dokumen_total"></h3>
            <p>Dokumen</p>
          </div>
          <div class="icon">
            <i class="fas fa-file"></i>
          </div>
        </div>

        <div class="small-box">
          <div class="chart">
            <center><canvas class="m-0 pb-3" id="donutDokumenChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100% !important;"></canvas></center>
          </div>
        </div>
      </div>

      <?php if ($this->session->userdata('pegawai_unit_id') == 'E53000'): ?>
        <div class="col-lg-3">
          <div class="small-box bg-danger">
            <div class="inner">
              <h3 id="dokumen_transmital_total"></h3>
              <p>Dokumen Transmital</p>
            </div>
            <div class="icon">
              <i class="fas fa-file"></i>
            </div>
          </div>

          <div class="small-box">
            <div class="chart">
              <center><canvas class="m-0 pb-3" id="donutDokumenTransmitalChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100% !important;"></canvas></center>
            </div>
          </div>
        </div>
      <?php endif ?>
    </div>

    <div class="row" style="align-items: self-start;">
      <div class="col-lg-6 <?= ($this->session->userdata('pegawai_unit_id') == 'E53000') ? 'col-lg-6' : 'col-lg-12' ?>">
        <div class="card h-100">
          <div class="card-header">
            <div class="row">
              <div class="col-12" style="height: 37px;">
                <h5 class="card-title">Grafik Total Pekerjaan</h5>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="chart">
              <canvas id="barChart" class="m-0" style="min-height: 420px; height: 420px; max-height: 420px; max-width: 100% !important;"></canvas>
            </div>
          </div>
        </div>
      </div>

      <?php if ($this->session->userdata('pegawai_unit_id') == 'E53000'): ?>
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header">
              <div class="row">
                <div class="col-4">
                  <h5 class="card-title">Rank Pegawai</h5>
                </div>
                <div class="col-8">
                  <select class="form-control" id="employe_filter" name="employe_filter" onchange="fun_filter_rank(this.value)">
                    <option value="1">- Total Pekerjaan -</option>
                    <option value="2">- Total Dokumen Dibuat -</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="card-body table-responsive">
              <table id="employe" class="table table-striped table-valign-middle table-bordered" style="width: 100% !important;">
                <thead>
                  <tr>
                    <th>Rank</th>
                    <th>Nama Pegawai</th>
                    <th>Total Pekerjaan</th>
                    <th>Total Dokumen Dibuat</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      <?php endif ?>
    </div>
  </div>
</div>

<script src="<?= base_url() ?>assets/libs/apexcharts/apexcharts.min.js"></script>
<script type="text/javascript" src="<?= base_url() ?>assets_tambahan/chart-js/chart.js"></script> 
<script type="text/javascript" src="<?= base_url() ?>assets_tambahan/chart-js/chartjs-plugin-datalabels.js"></script>
<!-- Sparkline -->
<script src="<?= base_url() ?>assets/plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="<?= base_url() ?>assets/plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="<?= base_url() ?>assets/plugins/moment/moment.min.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?= base_url() ?>assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="<?= base_url() ?>assets/plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="<?= base_url() ?>assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url() ?>assets/dist/js/adminlte.js"></script>

<script type="text/javascript">
  var chartInstancesPkerjaanPerBulan = {};
  var chartInstancesBerjalan = {};
  var chartInstancesSelesai = {};
  var chartInstancesDokumen = {};
  var chartInstancesDokumenTransmital = {};

  $(function () {
    totalpekerjaan();
    dokumenTotal();
    dokumenTransmitalTotal();

    $('#employe').DataTable({
      "ordering": false,
      "pageLength": 5,
      "ajax": {
        "url": "<?= base_url() ?>home/getEmploye?bulan=&tahun=",
        "dataSrc": ""
      },
      "columns": [
        {
          render: function(data, type, full, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }
        },
        {
          render:function(data,type,full,meta){
            var warna = '';
            var logo = '';
            var weight = '';
            if((meta.row+1)=='1'){
              warna = 'aqua';
              logo = 'fa fa-star';
              weight = 'bold'
            }
            return '<span class="" style="background-color: ' + warna + ';color:black;font-weight:'+weight+' ">' + full.pegawai_nama + ' <i class="'+logo+'"></i></span>';
          }
        },
        {"data": "totalpekerjaan"},
        {"data": "totaldokumen"},
      ]
    }).columns.adjust().draw();
  });

  $('#filter').on('submit',function(e){
    e.preventDefault();
    var bulan =  $('#bulan').val();
    var tahun =  $('#tahun').val();
    var rank =  $('#employe_filter').val();

    dokumenTotal(bulan, tahun);
    totalpekerjaan(bulan, tahun);
    dokumenTransmitalTotal(bulan, tahun);
    $('#employe').DataTable().ajax.url('/decpetro_v3/home/getEmploye?filter='+rank+'&bulan='+bulan+'&tahun='+tahun).load();
  });

  function printPage() {
    var printStyles = document.createElement('style');
    printStyles.innerHTML = `
      @media print {
        body {font-size: 12pt;}
        @page {size: A4;}
      }
    `;

    document.head.appendChild(printStyles);
    document.head.removeChild(printStyles);
    window.print();
  }

  function totalpekerjaan(bulan = null, tahun = null) {
    /* Box Pekerjaan Berjalan & Selesai */
      $.getJSON('<?= base_url('home/getTotalPekerjaan')?>', {bulan: bulan, tahun: tahun}, function(json) {
        $('#pekerjaan_berjalan_total').html(json.dataBerjalanTotal.total);
        $('#pekerjaan_selesai_total').html(json.dataselesaiTotal.total);
      });
    /* Box Pekerjaan Berjalan & Selesai */

    $.ajax({
      url: "<?= base_url() ?>home/getTotalPekerjaan",
      method: "GET",
      data: {bulan: bulan, tahun: tahun},
      async: true,
      dataType: 'json',
      success: function(isi) {
        /* Grafik Pekerjaan Berjalan */
          var label = [];
          var total = [];
          var bgcolor = [];
          var backgroundColor = ['#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'];

          $.each(isi.berjalanPerStatus, function(index, val) {
            label.push(val.nama);
            total.push(val.total);
            bgcolor.push(backgroundColor[index]);
          });

          var donutBerjalanData = {
            labels  : label,
            datasets: [
              {
                data:total,
                backgroundColor:bgcolor
              }
            ]
          }

          var donutBerjalanCanvas = $('#donutBerjalanChart').get(0).getContext('2d');
          var chartID = donutBerjalanCanvas.id;

          if (chartInstancesBerjalan[chartID]) {chartInstancesBerjalan[chartID].destroy();}

          var donutBerjalanOptions = {
            responsive: true,
            plugins: {
              datalabels: {
                color: 'white',
                formatter: function (value, context) {
                  return context.chart.data.labels[context.chart.data.labels];
                },
              },
              title: {display: true,},
              legend: {display: true}
            },
          }

          var newChart = new Chart(donutBerjalanCanvas, {
            plugins: [ChartDataLabels],
            type: 'doughnut',
            data: donutBerjalanData,
            options: donutBerjalanOptions
          })

          chartInstancesBerjalan[chartID] = newChart;
        /* Grafik Pekerjaan Berjalan */

        /* Grafik Pekerjaan Selesai */
          var label = [];
          var total = [];
          var bgcolor = [];
          var backgroundColor = ['#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'];

          $.each(isi.selesaiPerStatus, function(index, val) {
            label.push(val.nama);
            total.push(val.total);
            bgcolor.push(backgroundColor[index]);
          });

          var donutBerjalanData = {
            labels  : label,
            datasets: [
              {
                data:total,
                backgroundColor:bgcolor
              }
            ]
          }

          var donutSelesaiCanvas = $('#donutSelesaiChart').get(0).getContext('2d');
          var chartID = donutSelesaiChart.id;
            
          if (chartInstancesSelesai[chartID]) {chartInstancesSelesai[chartID].destroy();}

          var donutBerjalanOptions = {
            responsive: true,
            plugins: {
              datalabels: {
                color: 'white',
                formatter: function (value, context) {
                  return context.chart.data.labels[context.chart.data.labels];
                },
              },
              title: {display: true,},
              legend: {display: true}
            },
          }

          var newChart = new Chart(donutSelesaiCanvas, {
            plugins: [ChartDataLabels],
            type: 'doughnut',
            data: donutBerjalanData,
            options: donutBerjalanOptions
          });

          chartInstancesSelesai[chartID] = newChart;
        /* Grafik Pekerjaan Selesai */

        /* Grafik Total Pekerjaan */
          var namabulan = ['Jan','Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct','Nov','Dec'];
          var totalBerjalanPerBulan = ['0','0', '0', '0', '0', '0', '0', '0', '0', '0','0','0'];
          var totalSelesaiPerBulan = ['0','0', '0', '0', '0', '0', '0', '0', '0', '0','0','0'];

          $.each(isi.berjalanPerBulan, function(index, val) {
            var varblnberjalan = parseInt(val.bln) - 1;
            totalBerjalanPerBulan[varblnberjalan] = val.total;
          });

          $.each(isi.selesaiPerBulan, function(index, val) {
            var varblnselesai = parseInt(val.bln) - 1;
            totalSelesaiPerBulan[varblnselesai] = val.total;
          });

          var areaChartData = {
            labels  : namabulan,
            datasets: [
              {
                label               : 'Selesai',
                backgroundColor     : 'rgba(60,141,188,0.9)',
                borderColor         : 'rgba(60,141,188,0.8)',
                pointRadius          : false,
                pointColor          : '#3b8bba',
                pointStrokeColor    : 'rgba(60,141,188,1)',
                pointHighlightFill  : '#fff',
                pointHighlightStroke: 'rgba(60,141,188,1)',
                data                : totalBerjalanPerBulan
              },
              {
                label               : 'Berjalan',
                backgroundColor     : '#b3b3b3',
                borderColor         : '#b3b3b3',
                pointRadius         : false,
                pointColor          : '#b3b3b3',
                pointStrokeColor    : '#c1c7d1',
                pointHighlightFill  : '#fff',
                pointHighlightStroke: 'rgba(220,220,220,1)',
                data                : totalSelesaiPerBulan
              },
            ]
          }

          var barChartCanvas = $('#barChart').get(0).getContext('2d')
          var chartID = barChartCanvas.id;

          if (chartInstancesPkerjaanPerBulan[chartID]) {chartInstancesPkerjaanPerBulan[chartID].destroy();}

          var barChartData = $.extend(true, {}, areaChartData)
          var temp0 = areaChartData.datasets[0]
          var temp1 = areaChartData.datasets[1]
          barChartData.datasets[0] = temp1
          barChartData.datasets[1] = temp0

          var barChartOptions = {
            responsive              : true,
            maintainAspectRatio     : false,
          }

          var newChart = new Chart(barChartCanvas, {
            type: 'bar',
            data: barChartData,
            options: barChartOptions
          })
          chartInstancesPkerjaanPerBulan[chartID] = newChart;
        /* Grafik Total Pekerjaan */
      }
    });
  }

  function dokumenTotal(bulan = null, tahun = null) {
    $.getJSON('<?= base_url('home/dokumenTotal')?>', {bulan: bulan, tahun: tahun}, function(json) {
      $('#dokumen_total').html(json.dokumenTotal.total);
    });

    $.ajax({
      url: "<?= base_url() ?>home/dokumenTotal",
      method: "GET",
      data: {bulan: bulan, tahun: tahun},
      async: true,
      dataType: 'json',
      success: function(isi) {
        var label = [];
        var total = [];
        var bgcolor = [];
        var backgroundColor = ['#fc5375', '#0cc9b0', '#327bfa', '#3c8dbc', '#d2d6de'];

        $.each(isi.dokumenPerstatus, function(index, val) {
          label.push(val.dokumen_status);
          total.push(val.count);
          bgcolor.push(backgroundColor[index]);
        });

        var donutDokumenData = {
          labels  : label,
          datasets: [
            {
              data:total,
              backgroundColor:bgcolor
            }
          ]
        }

        var donutDokumenCanvas = $('#donutDokumenChart').get(0).getContext('2d');
        var chartID = donutDokumenCanvas.id;

        if (chartInstancesDokumen[chartID]) {chartInstancesDokumen[chartID].destroy();}

        var donutDokumenOptions = {
          responsive: true,
          plugins: {
            datalabels: {
              color: 'white',
              formatter: function (value, context) {
                return context.chart.data.labels[context.chart.data.labels];
              },
            },
            title: {display: true,},
            legend: {display: true}
          },
        }

        var newChart = new Chart(donutDokumenCanvas, {
          plugins: [ChartDataLabels],
          type: 'doughnut',
          data: donutDokumenData,
          options: donutDokumenOptions
        });

        chartInstancesDokumen[chartID] = newChart;
      }
    });
  }

  function dokumenTransmitalTotal(bulan = null, tahun = null) {
    $.getJSON('<?= base_url('home/dokumenTransmitalTotal')?>', {bulan: bulan, tahun: tahun}, function(json) {
      $('#dokumen_transmital_total').html(json.dokumenTotal.total);
    });

    $.ajax({
      url: "<?= base_url() ?>home/dokumenTransmitalTotal",
      method: "GET",
      data: {bulan: bulan, tahun: tahun},
      async: true,
      dataType: 'json',
      success: function(isi) {
        var label = [];
        var total = [];
        var bgcolor = [];
        var backgroundColor = ['#fc5375', '#0cc9b0', '#327bfa', '#3c8dbc', '#d2d6de'];

        $.each(isi.dokumenPerstatus, function(index, val) {
          label.push(val.dokumen_status);
          total.push(val.count);
          bgcolor.push(backgroundColor[index]);
        });

        var donutDokumenData = {
          labels  : label,
          datasets: [
            {
              data:total,
              backgroundColor:bgcolor
            }
          ]
        }

        var donutDokumenTransmitalCanvas = $('#donutDokumenTransmitalChart').get(0).getContext('2d');
        var chartID = donutDokumenTransmitalCanvas.id;

        if (chartInstancesDokumenTransmital[chartID]) {chartInstancesDokumenTransmital[chartID].destroy();}

        var donutDokumenOptions = {
          responsive: true,
          plugins: {
            datalabels: {
              color: 'white',
              formatter: function (value, context) {
                return context.chart.data.labels[context.chart.data.labels];
              },
            },
            title: {display: true,},
            legend: {display: true}
          },
        }

        var newChart = new Chart(donutDokumenTransmitalCanvas, {
          plugins: [ChartDataLabels],
          type: 'doughnut',
          data: donutDokumenData,
          options: donutDokumenOptions
        });

        chartInstancesDokumenTransmital[chartID] = newChart;
      }
    });
  }

  function fun_filter_rank(isi) {
    $('#employe').DataTable().ajax.url('<?= base_url() ?>/home/getEmploye?filter='+isi+'&bulan='+$('#bulan').val()+'&tahun='+$('#tahun').val()).load();
  }
</script>