<div class="page-content">
  <div class="container-fluid">

    <!-- start page title -->
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
          <h4 class="card-title mb-4">Rekap</h4>

        </div>
      </div>
    </div>
    <!-- end page title -->

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h4>RKAP</h4>
            <table id="table_rkap" class="table table-bordered table-striped nowrap w-100">
              <thead>
                <tr>
                  <th style="text-align: center;">No</th>
                  <th style="text-align: center;">Nomor Pekerjaan</th>
                  <th style="text-align: center;">Waktu Pekerjaan</th>
                  <th style="text-align: center;">Nama Pekerjaan</th>
                  <th style="text-align: center;">Klasifikasi Pekerjaan</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <h4>Non RKAP</h4>
            <table id="table_non_rkap" class="table table-bordered table-striped nowrap w-100">
              <thead>
                <tr>
                  <th style="text-align: center;">No</th>
                  <th style="text-align: center;">Nomor Pekerjaan</th>
                  <th style="text-align: center;">Waktu Pekerjaan</th>
                  <th style="text-align: center;">Nama Pekerjaan</th>
                  <th style="text-align: center;">Klasifikasi Pekerjaan</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <h4>Jasa Engineering</h4>
            <table id="table_jasa_engineering" class="table table-bordered table-striped nowrap w-100">
              <thead>
                <tr>
                  <th style="text-align: center;">No</th>
                  <th style="text-align: center;">Nomor Pekerjaan</th>
                  <th style="text-align: center;">Waktu Pekerjaan</th>
                  <th style="text-align: center;">Nama Pekerjaan</th>
                  <th style="text-align: center;">Klasifikasi Pekerjaan</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <h4>Approval Dokumen</h4>
            <table id="table_approval_dokumen" class="table table-bordered table-striped nowrap w-100">
              <thead>
                <tr>
                  <th style="text-align: center;">No</th>
                  <th style="text-align: center;">Judul Dokumen</th>
                  <th style="text-align: center;">Nama Pekerjaan</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <h4>Riwayat Disposisi</h4>
            <table id="table_riwayat_disposisi" class="table table-bordered table-striped nowrap w-100">
              <thead>
                <tr>
                  <th style="text-align: center;">No</th>
                  <th style="text-align: center;">Nomor Pekerjaan</th>
                  <th style="text-align: center;">Waktu Pekerjaan</th>
                  <th style="text-align: center;">Nama Pekerjaan</th>
                  <th style="text-align: center;">Klasifikasi Pekerjaan</th>
                  <th style="text-align: center;">Disposisi</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <h4 class="card-title mb-4">Grafik Rencana dan Realisasi Pekerjaan</h4>
            <div id="barChartRencanaDanRealisasiPekerjaan" class="apex-charts" dir="ltr"></div>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <h4 class="card-title mb-4">Grafik Status Pekerjaan</h4>
            <div id="barChartStatusPekerjaan" class="apex-charts" dir="ltr"></div>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <h4 class="card-title mb-4">Grafik Jumlah Status Dokumen</h4>
            <div id="barChartJumlahStatusDokumen" class="apex-charts" dir="ltr"></div>
          </div>
        </div>
      </div>
    </div> <!-- end row -->

  </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->

<!-- apexcharts -->
<script src="<?php echo base_url() ?>assets/libs/apexcharts/apexcharts.min.js"></script>

<script type="text/javascript">
  $(function () { 
    fun_loading();
    /*Table RKAP*/
      $('#table_rkap').DataTable({
        "ajax": {
            "url": "<?= base_url() ?>rekap/getRKAP",
            "dataSrc": ""
          },
          "columns": [
            {render: function ( data, type, full, meta ) {
                return meta.row + meta.settings._iDisplayStart + 1;
              }
            },
            {"data": "pekerjaan_nomor"},
            {"data": "pekerjaan_waktu"},
            {"data": "pekerjaan_judul"},
            {"data": "klasifikasi_pekerjaan_nama"},
          ]
      });
    /*Table RKAP*/

    /*Table Non RKAP*/
      $('#table_non_rkap').DataTable({
        "ajax": {
            "url": "<?= base_url() ?>rekap/getNonRKAP",
            "dataSrc": ""
          },
          "columns": [
            {render: function ( data, type, full, meta ) {
                return meta.row + meta.settings._iDisplayStart + 1;
              }
            },
            {"data": "pekerjaan_nomor"},
            {"data": "pekerjaan_waktu"},
            {"data": "pekerjaan_judul"},
            {"data": "klasifikasi_pekerjaan_nama"},
          ]
      });
    /*Table Non RKAP*/

    /*Table Ajuan Investasi*/
      $('#table_jasa_engineering').DataTable({
        "ajax": {
            "url": "<?= base_url() ?>rekap/getJasaEngineering",
            "dataSrc": ""
          },
          "columns": [
            {render: function ( data, type, full, meta ) {
                return meta.row + meta.settings._iDisplayStart + 1;
              }
            },
            {"data": "pekerjaan_nomor"},
            {"data": "pekerjaan_waktu"},
            {"data": "pekerjaan_judul"},
            {"data": "klasifikasi_pekerjaan_nama"},
          ]
      });
    /*Table Ajuan Investasi*/

    /*Table Approval Dokumen*/
      $('#table_approval_dokumen').DataTable({
        "ajax": {
            "url": "<?= base_url() ?>rekap/getApprovalDokumen",
            "dataSrc": ""
          },
          "columns": [
            {render: function ( data, type, full, meta ) {
                return meta.row + meta.settings._iDisplayStart + 1;
              }
            },
            {"data": "pekerjaan_dokumen_file"},
            {"data": "pekerjaan_judul"},
          ]
      });
    /*Table Approval Dokumen*/

    /*Table Riwayat Disposisi*/
      $('#table_riwayat_disposisi').DataTable({
        "ajax": {
            "url": "<?= base_url() ?>rekap/getRiwayatDisposisi",
            "dataSrc": ""
          },
          "columns": [
            {render: function ( data, type, full, meta ) {
                return meta.row + meta.settings._iDisplayStart + 1;
              }
            },
            {"data": "pekerjaan_nomor"},
            {"data": "pekerjaan_waktu"},
            {"data": "pekerjaan_judul"},
            {"data": "klasifikasi_pekerjaan_nama"},
            {"data": "pekerjaan_disposisi_status"},
          ]
      });
    /*Table Riwayat Disposisi*/
  });

  /*Rencana dan Realisasi Pekerjaan*/
    $(function () { 
      var tahun = new Date().getFullYear();
      $.ajax({
        url: "<?= base_url("rekap/getRencanaDanRealisasiPekerjaan"); ?>",
        method: "GET",
        data: {
          tahun: tahun,
        },
        success: function(json) {
          var label = [];
          var isi_rencanaRKAP = [];
          var isi_realisasiRKAP = [];
          var isi_rencanaNonRKAP = [];
          var isi_realisasiNonRKAP = [];
          var isi_rencanaJasaEngineering = [];
          var isi_realisasiJasaEngineering = [];

          $.each(JSON.parse(json), function(index, val) {
            label.push(val.bulan);
            isi_rencanaRKAP.push(val.rencanaRKAP);
            isi_realisasiRKAP.push(val.realisasiRKAP);
            isi_rencanaNonRKAP.push(val.rencanaNonRKAP);
            isi_realisasiNonRKAP.push(val.realisasiNonRKAP);
            isi_rencanaJasaEngineering.push(val.rencanaJasaEngineering);
            isi_realisasiJasaEngineering.push(val.realisasiJasaEngineering);
          });

          var options = {
            chart: {
              height: 350,
              type: 'bar',
              toolbar: {
                show: false,
              }
            },
            plotOptions: {
              bar: {
                horizontal: false,
                columnWidth: '45%',
                endingShape: 'rounded'
              },
            },
            dataLabels: {
              enabled: false
            },
            stroke: {
              show: true,
              width: 2,
              colors: ['transparent']
            },
            series: [{
              name: 'Rencana Pekerjaan RKAP',
              data: isi_rencanaRKAP
            }, {
              name: 'Realisasi Pekerjaan RKAP',
              data: isi_realisasiRKAP
            }, {
              name: 'Rencana Pekerjaan Non RKAP',
              data: isi_rencanaNonRKAP
            }, {
              name: 'Realisasi Pekerjaan Non RKAP',
              data: isi_realisasiNonRKAP
            }, {
              name: 'Rencana Pekerjaan Jasa Engineering',
              data: isi_rencanaJasaEngineering
            }, {
              name: 'Realisasi Pekerjaan Jasa Engineering',
              data: isi_realisasiJasaEngineering
            }],
            colors: ['rgb(252, 182, 193)', 'rgb(251, 160, 122)', 'rgb(176, 196, 222)', 'rgb(119, 136, 153)', 'rgb(106, 90, 205)', 'rgb(63, 255, 128)'],
            xaxis: {
              categories: label,
            },
            yaxis: {
              title: {
                style: {
                  fontWeight: '500',
                },
              }
            },
            grid: {
              borderColor: '#f1f1f1',
            },
            fill: {
              opacity: 1

            },
            tooltip: {
              y: {
                formatter: function(val) {
                  return val
                }
              }
            }
          }

          var chart = new ApexCharts(
            document.querySelector("#barChartRencanaDanRealisasiPekerjaan"),
            options
          );

          chart.render();

        }
      });
    });
  /*Rencana dan Realisasi Pekerjaan*/

  /*Status Pekerjaan*/
    $(function () { 
      $.ajax({
        url: "<?= base_url("rekap/getStatusPekerjaan"); ?>",
        method: "GET",
        success: function(json) {
          var isi_baru = [];
          var isi_proses = [];
          var isi_selesai = [];

          $.each(JSON.parse(json), function(index, val) {
            isi_baru.push(val.baru);
            isi_proses.push(val.proses);
            isi_selesai.push(val.selesai);
          });

          var options = {
            chart: {
              height: 350,
              type: 'bar',
              toolbar: {
                show: false,
              }
            },
            plotOptions: {
              bar: {
                horizontal: false,
                columnWidth: '45%',
                endingShape: 'rounded'
              },
            },
            dataLabels: {
              enabled: false
            },
            stroke: {
              show: true,
              width: 2,
              colors: ['transparent']
            },
            series: [{
              name: 'Baru',
              data: isi_baru
            }, {
              name: 'Proses',
              data: isi_proses
            }, {
              name: 'Selesai',
              data: isi_selesai
            }],
            colors: ['rgb(252, 182, 193)', 'rgb(251, 160, 122)', 'rgb(176, 196, 222)'],
            xaxis: {
              categories: [''],
            },
            yaxis: {
              title: {
                style: {
                  fontWeight: '500',
                },
              }
            },
            grid: {
              borderColor: '#f1f1f1',
            },
            fill: {
              opacity: 1

            },
            tooltip: {
              y: {
                formatter: function(val) {
                  return val
                }
              }
            }
          }

          var chart = new ApexCharts(
            document.querySelector("#barChartStatusPekerjaan"),
            options
          );

          chart.render();
        }
      });
    });
  /*Status Pekerjaan*/

  /*Grafik Batang Dokumen*/
    $(function () { 
      $.ajax({
        url: "<?= base_url("rekap/getStatusDokumen"); ?>",
        method: "GET",
        success: function(json) {
          var isi_ifa = [];
          var isi_ifc = [];

          $.each(JSON.parse(json), function(index, val) {
            isi_ifa.push(val.ifa);
            isi_ifc.push(val.ifc);
          });

          var options = {
            chart: {
              height: 350,
              type: 'bar',
              toolbar: {
                show: false,
              }
            },
            plotOptions: {
              bar: {
                horizontal: false,
                columnWidth: '45%',
                endingShape: 'rounded'
              },
            },
            dataLabels: {
              enabled: false
            },
            stroke: {
              show: true,
              width: 2,
              colors: ['transparent']
            },
            series: [{
              name: 'IFA',
              data: isi_ifa
            }, {
              name: 'IFC',
              data: isi_ifc
            }, ],
            colors: ['rgb(252, 182, 193)', 'rgb(251, 160, 122)'],
            xaxis: {
              categories: [''],
            },
            yaxis: {
              title: {
                style: {
                  fontWeight: '500',
                },
              }
            },
            grid: {
              borderColor: '#f1f1f1',
            },
            fill: {
              opacity: 1

            },
            tooltip: {
              y: {
                formatter: function(val) {
                  return val
                }
              }
            }
          }

          var chart = new ApexCharts(
            document.querySelector("#barChartJumlahStatusDokumen"),
            options
          );

          chart.render();
        }
      });
    });
  /*Grafik Batang Dokumen*/

  function fun_loading() {
    var simplebar = new Nanobar();
    simplebar.go(100);
  }
</script>