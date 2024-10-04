<?php
  $dataAdmin = $this->db->get_where('global.global_admin', ['admin_nik' => $this->session->userdata('pegawai_nik')])->row_array();
  $dataAdminBagian = $this->db->get_where('global.global_admin_bagian', ['admin_bagian_nik' => $this->session->userdata('pegawai_nik')])->row_array();

  if ($dataAdmin) $this->db->where('1 = 1', null);
  elseif ($dataAdminBagian) $this->db->where('id_bagian', $dataAdminBagian['id_bagian']);
  else $this->db->where('pegawai_nik', $this->session->userdata('pegawai_nik'));
  $this->db->where('pegawai_id_dep', 'E53000');
  $this->db->order_by('pegawai_jabatan', 'asc');
  $this->db->join('global.global_bagian_detail b', 'a.pegawai_nik = b.id_pegawai', 'left');
  $dataPegawai = $this->db->get('global.global_pegawai a')->result_array();
?>
<style>
    .red-bg {
        background-color: #ff7369 !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets_tambahan/') ?>easyui/themes/default/easyui.css">
<link rel="stylesheet" type="text/css" href="<?= base_url('assets_tambahan/') ?>easyui/themes/icon.css">
<div class="page-content">
  <div class="container-fluid">
    <!-- start page title -->
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
          <h4 class="card-title mb-2">MY TASK</h4>
        </div>
      </div>
    </div>
    <!-- end page title -->
    <!-- Start Filter Pekerjaan -->
    <div class="row" id="div_filter" style="display:block">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title mb-4">Filter Pekerjaan</h4>
            <form id="filter">
              <div class="row">
                <div class="form-group col-md-5">
                  <label>Tahun Pekerjaan</label>
                  <select class="form-control" id="tahun" name="tahun">
                    <option value="">- Semua -</option>
                    <?php foreach ($dataTahun as $value) : ?>
                      <option value="<?php echo $value['tahun'] ?>"><?php echo $value['tahun'] ?></option>
                    <?php endforeach ?>
                  </select>
                </div>
                <div class="form-group col-md-5" id="div_perencana">
                  <label>Perencana</label>
                  <select class="form-control select2" id="id_user_cari" name="id_user_cari">
                    <?php foreach ($dataPegawai as $value): ?>
                      <option value="<?= $value['pegawai_nik'] ?>" <?= ($value['pegawai_nik'] == $this->session->userdata('pegawai_nik') ? 'selected' : '') ?>><?= $value['pegawai_nama'] ?></option>
                    <?php endforeach ?>
                  </select>
                </div>
                <div class="form-group col-md-2">
                  <label>&emsp;</label>
                  <button class="btn btn-primary form-control" type="button" name="cari_filter" id="cari_filter">Cari</button>
                  <button class="btn btn-primary form-control" type="button" name="cari_berjalan" id="cari_berjalan" style="display:none">Cari</button>
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
            <div>
              <h4 class="card-title mb-4">My Task</h4>
            </div>
            <table id="table" class="table table-bordered nowrap" width="100%">
              <thead class="table-primary">
                <tr>
                  <th style="text-align: center;">No</th>
                  <th style="text-align: center;">Waktu</th>
                  <th style="text-align: center;">Nama Pekerjaan</th>
                  <th style="text-align: center;">Keterangan</th>
                  <th style="text-align: center;">Status</th>
                  <!-- <th style="text-align: center;">Status</th> -->
                  <th style="text-align: center;">Aksi</th>
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

  <script type="text/javascript" src="<?= base_url('assets_tambahan/') ?>easyui/jquery.easyui.min.js"></script>
  <script type="text/javascript" src="<?= base_url('assets_tambahan/') ?>easyui/jquery.edatagrid.js"></script>

  <script>
    /*table*/
    $('#table thead tr').clone(true).addClass('filters').appendTo('#table thead');
    $('#table').DataTable({
      paging: true,
      scrollX:false,
      orderCellsTop: true,
      columnDefs: [
      {
        targets: 0,
        type: "html-numeric"
      }
      ],
      initComplete: function() {
        $("#table").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        var api = this.api();
        api.columns().eq(0).each(function(colIdx) {
          var cell = $('.filters th').eq(
            $(api.column(colIdx).header()).index()
            );
          var title = $(cell).text();
          $(cell).html('<input type="text" class="form-control" style="width:100%" placeholder="' + title + '" />');

          $('input', $('.filters th').eq($(api.column(colIdx).header()).index())).off('keyup change').on('keyup change', function(e) {
            e.stopPropagation();
            $(this).attr('title', $(this).val());
            var regexr = '({search})';
            var cursorPosition = this.selectionStart;

            api.column(colIdx).search(this.value != '' ? regexr.replace('{search}', '(((' + this.value + ')))') : '', this.value != '', this.value == '').draw();

            $(this).focus()[0].setSelectionRange(cursorPosition, cursorPosition);
          });
        });
      },
      "ajax": {
        "url": "<?= base_url('task/task/getTask') ?>",
        "dataSrc": ""
      },
      "rowCallback": function(row, data, index) {
            if (data.is_proses != 'n') {$(row).addClass('red-bg');}
        },
      "columns": [{
        render: function(data, type, full, meta) {
          // var urut = '';
          // if (full.is_proses != 'y') {
          //   urut = '<span class="badge" style="background-color:#c13333 ">' + (parseInt(meta.row + 1)) + '</span>';
          // } else {
            urut = parseInt(meta.row + 1);
          // }
          return urut;
        },
        type: "html-numeric"
      },
      {
        "data": "task_date"
      },
      {
        "data": "pekerjaan_judul"
      },
      {
        // render: function(data, type, full, meta) {
        //   var nama = '';
        //   if (full.is_proses != 'y') {
        //     nama = '<span class="badge" style="background-color:#c13333 ">' + (full.task_name) + '</span>';
        //   } else {
        //     nama = full.task_name;
        //   }
        //   return nama;
        // },
        "data": "task_name"
      },
        {
    "render": function(data, type, full, meta) {
      var status = '';
      var warna = '';
      if (full.pekerjaan_status == 0) {
        status = 'Draft';
        warna = '#A0A0A0';
      } else if (full.pekerjaan_status == 1) {
        warna = '#FFFF00';
        status = 'Menunggu Review AVP';
      } else if (full.pekerjaan_status == 2) {
        warna = '#FF8000';
        status = 'Menunggu Approval VP';
      } else if (full.pekerjaan_status == 3) {
        warna = '#00FF00';
        status = 'Menunggu Approve VP Rancang Bangun'
      } else if (full.pekerjaan_status == 4) {
        warna = '#0080FF';
        status = 'Menunggu Disposisi AVP'
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
        warna = '#FF8000';
        status = 'IFA - Send User'
      } else if (full.pekerjaan_status == 9) {
        warna = '#FF8000';
        status = 'IFA – Menunggu Review AVP User';
      } else if (full.pekerjaan_status == 10) {
        warna = '#FF8000';
        status = 'IFA – Menunggu Approve VP User';
      } else if (full.pekerjaan_status == 11) {
        warna = '#B266FF';
        status = 'IFA - Approve User / IFC';
      } else if (full.pekerjaan_status == 12) {
        warna = '#B266FF';
        status = 'IFC - Send AVP';
      } else if (full.pekerjaan_status == 13) {
        warna = '#B266FF';
        status = 'IFC'
      } else if (full.pekerjaan_status == 14) {
        warna = '#00FFFF';
        status = 'Selesai'
      } else if (full.pekerjaan_status == 15) {
        warna = '#00FFFF';
        status = 'Selesai'
      } else if (full.pekerjaan_status == '-') {
        warna = '#FF0000';
        status = 'Reject'
      }

      return '<span class="lead"><span class="badge" style="background-color: ' + warna + ';color:black  ">' + status + '</span></span>';
    }
  },
      {
        "render": function(data, type, full, meta) {
          var aksi = ''
          var rkap = ''
          if(full.pekerjaan_status >=0 && full.pekerjaan_status <=4){
            aksi = 'usulan';
          }else if(full.pekerjaan_status >=5 && full.pekerjaan_status<=7){
            aksi = 'berjalan';
          }else if(full.pekerjaan_status >=8 && full.pekerjaan_status<=10){
            aksi = 'ifa';
          }else if(full.pekerjaan_status >=11 && full.pekerjaan_status <=13){
            aksi = 'ifc';
          }else if(full.pekerjaan_status >=14){
            aksi = 'selesai'
          }
          if(full.id_klasifikasi_pekerjaan!='1'){
            rkap = '0';
          }else{
            rkap = '1';
          }
          if (full.is_proses != 'n') {
            return ((full.is_proses != 'n' || full.user_action == 'n') &&  full.pekerjaan_status != '-') ? '<center><a href="<?= base_url('project/pekerjaan_usulan/detailPekerjaan?aksi=') ?>' + aksi + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_status + '&rkap='+rkap+'" id="'+full.task_id+'" title="Detail" onclick="fun_update(this.id)"><i class="btn btn-info btn-sm">Detail</i></a></center>' : '<center>-</center>';
          } else {
            return '<center><i class="fa fa-check"></i></center>'
          }
        }
      },
      ]
});
/*table*/

/*zero pad*/
function pad(str, max) {
  str = str.toString();
  return str.length < max ? pad("0" + str, max) : str;
}
/*zero pad*/

function fun_update(id) {
  $.ajax({
    url: '<?= base_url() ?>/task/updateTask?id='+id,
    method: 'GET',
    success: function (data) {
    },
    error: function () {
    }
  });
}

$('#cari_filter').on('click', function(e) {
  var data = $('#filter').serialize();
  $('#table').DataTable().ajax.url('<?= base_url('task/task/getTask') ?>?' + data).load();
})
</script>