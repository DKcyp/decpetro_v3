<div class="page-content">
  <div class="container-fluid">

    <!-- start page title -->
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
          <h4 class="card-title mb-4">Laporan</h4>

        </div>
      </div>
    </div>
    <!-- end page title -->

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <ul class="nav nav-tabs">
              <li class="nav-item">
                <a class="nav-link active" href="javascript:;" onclick="div_rkap()" id="link_div_rkap">RKAP</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="javascript:;" onclick="div_non_rkap()" id="link_div_non_rkap">Non RKAP</a>
              </li>
            </ul>
          </div>

          <div class="card-body" id="div_rkap">
            <h4>Evaluasi Ajuan Investasi RKAP</h4>
            <table id="table_rkap" class="table table-bordered table-striped nowrap" width="100%">
              <thead class="table-primary">
                <tr>
                  <th style="text-align: center;" rowspan="2">No</th>
                  <th style="text-align: center;" rowspan="2">No Pekerjaan</th>
                  <th style="text-align: center;" rowspan="2">Nama Kegiatan</th>
                  <th style="text-align: center;" rowspan="2">User/PIC</th>
                  <th style="text-align: center;" rowspan="2">Departemen</th>
                  <th style="text-align: center;" rowspan="2">Progress</th>
                  <th style="text-align: center;" colspan="5">Perencana</th>
                  <th style="text-align: center;" rowspan="2">Man Power</th>
                  <th style="text-align: center;" rowspan="2">Enginering Start</th>
                  <th style="text-align: center;" rowspan="2">Jadwal Engineering Finish</th>
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

          <div class="card-body" id="div_non_rkap">
            <h4>Evaluasi Ajuan Investasi Non RKAP</h4>
            <table id="table_non_rkap" class="table table-bordered table-striped nowrap" width="100%">
              <thead class="table-primary">
                <tr>
                  <th style="text-align: center;" rowspan="2">No</th>
                  <th style="text-align: center;" rowspan="2">No Pekerjaan</th>
                  <th style="text-align: center;" rowspan="2">Nama Kegiatan</th>
                  <th style="text-align: center;" rowspan="2">User/PIC</th>
                  <th style="text-align: center;" rowspan="2">Departemen</th>
                  <th style="text-align: center;" rowspan="2">Progress</th>
                  <th style="text-align: center;" colspan="5">Perencana</th>
                  <th style="text-align: center;" rowspan="2">Man Power</th>
                  <th style="text-align: center;" rowspan="2">Enginering Start</th>
                  <th style="text-align: center;" rowspan="2">Jadwal Engineering Finish</th>
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


<!-- end row -->
<script src="<?= base_url() ?>assets/libs/apexcharts/apexcharts.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script type="text/javascript">
  /*Rencana dan Realisasi Pekerjaan*/
  $(function() {
    div_rkap();

    $('#table_rkap').DataTable({
      "initComplete": function (settings, json) {  
        $("#table_rkap").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
      },
      "dom": 'Bfrtip',
      "ajax": {
        "url": "<?= base_url('laporan/') ?>laporan/getPekerjaanBerjalan?klasifikasi_pekerjaan_id=1&pekerjaan_status=5,6,7",
        // "url": "<?= base_url('project/') ?>pekerjaan_usulan/getPekerjaanBerjalan?&pekerjaan_status=5,6,7",
        "dataSrc": ""
      },
      "columns": [{
        render: function(data, type, full, meta) {
          return meta.row + meta.settings._iDisplayStart + 1;
        }
      },
      {
        render: function(data, type, full, meta) {
          var nomor = full.pekerjaan_nomor.split('-');
          nomor[0] = pad(nomor[0], 3);
          return nomor.join('-');
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
        "data": "pegawai_nama_dep"
      },
      {
        "data": "pekerjaan_progress"
      },
      {
        render: function(data, type, full, meta) {
          return full.pekerjaan_isi_proses;
        }
      },
      {
        render: function(data, type, full, meta) {
          return full.pekerjaan_isi_mesin;
        }
      },
      {
        render: function(data, type, full, meta) {
          return full.pekerjaan_isi_listrik;
        }
      },
      {
        render: function(data, type, full, meta) {
          return full.pekerjaan_isi_instrumen;
        }
      },
      {
        render: function(data, type, full, meta) {
          return full.pekerjaan_isi_sipil;
        }
      },
      {
        "data": "total"
      },
      {
        "data": "tanggal_start"
      },
      {
        "data": "tanggal_akhir"
      },
      ]
    });

    $('#table_non_rkap').DataTable({
      "initComplete": function (settings, json) {  
        $("#table_non_rkap").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
      },
      "dom": 'Bfrtip',
      "ajax": {
        "url": "<?= base_url('laporan/') ?>laporan/getPekerjaanBerjalan?klasifikasi_pekerjaan_id_non_rkap=1&pekerjaan_status=5,6,7",
        // "url": "<?= base_url('project/') ?>pekerjaan_usulan/getPekerjaanBerjalan?&pekerjaan_status=5,6,7",
        "dataSrc": ""
      },
      "columns": [{
        render: function(data, type, full, meta) {
          return meta.row + meta.settings._iDisplayStart + 1;
        }
      },
      {
        render: function(data, type, full, meta) {
          var nomor = full.pekerjaan_nomor.split('-');
          nomor[0] = pad(nomor[0], 3);
          return nomor.join('-');
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
        "data": "pegawai_nama_dep"
      },
      {
        "data": "pekerjaan_progress"
      },
      {
        render: function(data, type, full, meta) {
          return full.pekerjaan_isi_proses;
        }
      },
      {
        render: function(data, type, full, meta) {
          return full.pekerjaan_isi_mesin;
        }
      },
      {
        render: function(data, type, full, meta) {
          return full.pekerjaan_isi_listrik;
        }
      },
      {
        render: function(data, type, full, meta) {
          return full.pekerjaan_isi_instrumen;
        }
      },
      {
        render: function(data, type, full, meta) {
          return full.pekerjaan_isi_sipil;
        }
      },
      {
        "data": "total"
      },
      {
        "data": "tanggal_start"
      },
      {
        "data": "tanggal_akhir"
      },
      ]
    });

    var tahun = new Date().getFullYear();
    $.ajax({
      url: "<?= base_url("laporan/getRencanaDanRealisasiPekerjaan"); ?>",
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
$(function() {
  $.ajax({
    url: "<?= base_url("laporan/getStatusPekerjaan"); ?>",
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
$(function() {
  $.ajax({
    url: "<?= base_url("laporan/getStatusDokumen"); ?>",
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

function div_rkap() {
  $('#div_rkap').show();
  $('#link_div_rkap').addClass('active');
  $('#div_non_rkap').hide();
  $('#link_div_non_rkap').removeClass('active');
}

function div_non_rkap() {
  $('#div_rkap').hide();
  $('#link_div_rkap').removeClass('active');
  $('#div_non_rkap').show();
  $('#link_div_non_rkap').addClass('active');
}

  /* Zero Padding */
function pad (str, max) {
  str = str.toString();
  return str.length < max ? pad("0" + str, max) : str;
}
  /* Zero Padding */
</script>