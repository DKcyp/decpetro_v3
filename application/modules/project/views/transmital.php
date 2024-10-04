<link rel="stylesheet" type="text/css" href="<?= base_url('assets_tambahan/') ?>easyui/themes/default/easyui.css">
<link rel="stylesheet" type="text/css" href="<?= base_url('assets_tambahan/') ?>easyui/themes/icon.css">
<style>
  .dataTables_scrollHeadInner,
  .table {
    width: 100% !important;
  }

  .select2-selection__clear {
    float: left !important;
  }
</style>
<div class="page-content">

  <?php
  /*Session*/
  $session = $this->session->userdata();
  /*SQL Atasan*/
  $dataAtasan = $this->db->query("SELECT * FROM global.global_pegawai WHERE pegawai_poscode = '" . $session['pegawai_direct_superior'] . "'")->row_array();
  /*SQL Tahun*/
  $dataTahun = $this->db->query("SELECT pekerjaan_tahun as tahun FROM dec.dec_pekerjaan WHERE pekerjaan_tahun IS NOT NULL GROUP BY pekerjaan_tahun ORDER BY pekerjaan_tahun ASC")->result_array();
  /*SQL Klasifikasi*/
  $dataKlasifikasi = $this->db->query("SELECT * FROM global.global_klasifikasi_pekerjaan WHERE 1=1 AND klasifikasi_pekerjaan_id ='ddf3c67d5c5ff4f4c978eed6da8fe9d9a27e459a' ORDER BY CAST(klasifikasi_pekerjaan_urut AS INT) ASC ")->row_array();
  /*Admin Sistem*/
  $admin_sistem = '';
  $admin_sistem = $this->db->query("SELECT * FROM global.global_admin WHERE admin_nik = '" . $session['pegawai_nik'] . "'")->row_array();
  if (!empty($admin_sistem)) {
    $admin_sistemnya = $admin_sistem['admin_nik'];
  } else {
    $admin_sistemnya = '0';
  }
  /*SQL Jumlah Pekerjaan Belum Selesai Per Departemen*/
  $dataDep = $this->db->query("SELECT pegawai_id_dep FROM global.global_pegawai WHERE pegawai_nik = '" . $session['pegawai_nik'] . "'")->row_array();
  $dataLoadPekerjaan = $this->db->query("SELECT count(pekerjaan_status) FROM dec.dec_pekerjaan b LEFT JOIN global.global_pegawai c ON c.pegawai_nik = b.pic WHERE (pekerjaan_status < '14' OR pekerjaan_status!= '-') AND pic = '" . $session['pegawai_nik'] . "' AND id_klasifikasi_pekerjaan NOT IN('1')")->row_array();
  ?>

  <div class="container-fluid">

    <!-- start page title -->
    <div class="row">
      <div class="col-lg-12">
        <!-- <div class="page-title-box d-sm-flex align-items-center justify-content-between"> -->
          <div class="card"> 
            <div class="card-body">
              <h4 class="card-title mb-2">Approval Waspro</h4>
            </div>
          </div>
          <!-- </div> -->
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
                <div class="form-group col-md-5" style="display: none;" id="div_perencana">
                  <label>Perencana</label>
                  <select class="form-control select2" id="id_user_cari" name="id_user_cari">
                  </select>
                </div>
                <div class="form-group col-md-2">
                  <label>&emsp;</label>
                  <button class="btn btn-primary form-control" type="button" name="cari_waspro" id="cari_waspro">Cari</button>
                  <button class="btn btn-primary form-control" type="button" name="cari_cangun" id="cari_cangun" style="display:none">Cari</button>
                  <button class="btn btn-primary form-control" type="button" name="cari_selesai" id="cari_selesai" style="display:none">Cari</button>
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
          <!--Start Tab -->
          <div class="card-body">
            <ul class="nav nav-tabs">
              <input type="text" id="pekerjaan_status_nama" name="pekerjaan_status_nama" value="usulan" hidden="hidden">
              <input type="text" hidden name="session_direct_superior" id="session_direct_superior" value="<?= $session['pegawai_direct_superior'] ?>">
              <input type="text" hidden name="session_poscode" id="session_poscode" value="<?= $session['pegawai_poscode'] ?>">
              <input type="text" name="session_nik" id="session_nik" value="<?= $session['pegawai_nik'] ?>" hidden>
              <input type="text" name="pegawai_jabatan" id="pegawai_jabatan" value="<?php echo substr($session['pegawai_jabatan'], 0, 1) ?>" style="display:none;">
              <input type="text" name="jabatan_atasan" id="jabatan_atasan" value="<?php echo substr($dataAtasan['pegawai_jabatan'], 0, 1) ?>" style="display:none;">
              <input type="text" name="nama_atasan" id="nama_atasan" value="<?php echo $dataAtasan['pegawai_nama'] ?>" style="display:none;">
              <input type="text" name="nik_atasan" id="nik_atasan" value="<?php echo $dataAtasan['pegawai_nik'] ?>" style="display:none;">
              <input type="text" name="postitle_atasan" id="postitle_atasan" value="<?php echo $dataAtasan['pegawai_postitle'] ?>" style="display:none;">
              <input type="text" name="direct_superior_atasan" id="direct_superior_atasan" value="<?php echo $dataAtasan['pegawai_direct_superior'] ?>" style="display:none;">
              <li class="nav-item">
                <a class="nav-link active bg-secondary bg-gradient" href="#usulan" onclick="div_usulan()" id="link_div_usulan">Usulan <span class="badge bg-primary fload_end" id="notif_usulan"></span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#waspro" onclick="div_waspro()" id="link_div_waspro">Was Pro <span class="badge bg-primary fload_end" id="notif_waspro"></span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#cangun" onclick="div_cangun()" id="link_div_cangun">Cangun <span class="badge bg-secondary fload_end" id="notif_cangun"></span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#selesai" onclick="div_selesai()" id="link_div_selesai">Selesai <span class="badge bg-success fload_end" id="notif_selesai"></a>
                </li>
              </ul>
            </div>
            <!-- End Tab -->
            <!-- start card waspro -->
            <div class="card-body" id="div_usulan">
              <div>
                <!-- <button type="button" class="btn btn-primary float-end" data-toggle="modal" data-target="#myModal" onclick="fun_tambah_waspro()" id="btn_waspro">Tambah</button> -->
                <h4 class="card-title mb-4">Pekerjaan Usulan</h4>
              </div>
              <table id="table_usulan" class="table table-bordered table-striped" style="width:100%">
                <thead class="table-primary">
                  <tr>
                    <th style="text-align: center;">No</th>
                    <th style="text-align: center;">No Pekerjaan</th>
                    <th style="text-align: center;">Waktu Pekerjaan</th>
                    <th style="text-align: center;">Nama Pekerjaan</th>
                    <th style="text-align: center;">Departemen</th>
                    <th style="text-align: center;">PIC</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: center;">Aksi</th>
                    <th style="text-align: center;">Edit</th>
                    <th style="text-align: center;">Delete</th>
                  </tr>
                </thead>
              </table>
            </div>
            <!-- end card waspro -->
            <!-- start card waspro -->
            <div class="card-body" id="div_waspro" style="display: none;">
              <div>
                <h4 class="card-title mb-4">Pekerjaan Waspro</h4>
              </div>
              <table id="table_waspro" class="table table-bordered table-striped" style="width:100%">
                <thead class="table-primary">
                  <tr>
                    <th style="text-align: center;">No</th>
                    <th style="text-align: center;">No Pekerjaan</th>
                    <th style="text-align: center;">Waktu Pekerjaan</th>
                    <th style="text-align: center;">Nama Pekerjaan</th>
                    <th style="text-align: center;">Departemen</th>
                    <th style="text-align: center;">PIC</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: center;">Aksi</th>
                  </tr>
                </thead>
              </table>
            </div>
            <!-- end card waspro -->
            <!-- start card cangun -->
            <div class="card-body" id="div_cangun" style="display:none">
              <div>
                <h4 class="card-title mb-4">Pekerjaan Cangun</h4>
              </div>
              <table id="table_cangun" class="table table-bordered table-striped" style="width:100%">
                <thead class="table-primary">
                  <tr>
                    <th style="text-align: center;">No</th>
                    <th style="text-align: center;">No Pekerjaan</th>
                    <th style="text-align: center;">Waktu Pekerjaan</th>
                    <th style="text-align: center;">Nama Pekerjaan</th>
                    <th style="text-align: center;">Departemen</th>
                    <th style="text-align: center;">PIC</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: center;">Aksi</th>
                  </tr>
                </thead>
              </table>
            </div>
            <!-- end card cangun -->

            <!-- start card selesai -->
            <div class="card-body" id="div_selesai" style="display:none">
              <div>
                <h4 class="card-title mb-4">Pekerjaan Selesai</h4>
              </div>
              <table id="table_selesai" class="table table-bordered table-striped" width="100%">
                <thead class="table-primary">
                  <tr>
                    <th style="text-align: center;">No</th>
                    <th style="text-align: center;">No Pekerjaan</th>
                    <th style="text-align: center;">Tanggal Selesai</th>
                    <th style="text-align: center;">Nama Pekerjaan</th>
                    <th style="text-align: center;">Departemen</th>
                    <th style="text-align: center;">PIC</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: center;">Aksi</th>
                  </tr>
                </thead>
              </table>
            </div>
            <!-- end card selesai -->
          </div>
        </div>
      </div>
      <!-- end row -->
    </div>
    <!-- container-fluid -->
    <!-- MODAL -->
    <!-- start modal usulan -->
    <div class="modal fade" id="modal_waspro" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Pekerjaan Waspro</h4>
          </div>
          <div class="modal-body">
            <form id="form_modal_waspro" enctype="multipart/form-data">
              <input type="text" name="pekerjaan_id" id="pekerjaan_id" style="display:none;">
              <input type="text" name="jabatan_temp" id="jabatan_temp" style="display:none;">
              <input type="text" name="pekerjaan_status" id="pekerjaan_status" value="0" style="display:none;">
              <div class="card-body row">
                <div class="form-group row col-md-12">
                  <label class="col-md-4">User</label>
                  <input type="text" name="user" id="user" class="form-control col-md-8" value="<?= $pegawai_nik ?>" readonly style="display:none">
                  <input type="text" name="user_nama" id="user_nama" class="form-control" value="<?= $pegawai_nama . ' - ' . $pegawai_postitle  ?>" readonly>
                  <label style="color:red;display:none" id="user_alert">PIC Tidak Boleh Kosong</label>
                </div>
              </div>
              <div class="card-body row">
                <div class="form-group row col-md-12">
                  <label class="col-md-4">Nama Pekerjaan</label>
                  <select name="pekerjaan_judul_list" id="pekerjaan_judul_list" class="form-control select2"></select>
                  <label style="color:red;display:none" id="pekerjaan_judul_alert">Nama Pekerjaan Tidak Boleh Kosong</label>
                </div>
              </div>

              <div class="modal-footer justify-content-between">
                <button type="button" id="close" class="btn btn-default" data-dismiss="modal" onclick="fun_close_usulan()">Close</button>
                <input type="button" class="btn btn-success pull-right" id="simpan" value="Simpan">
                <input type="button" class="btn btn-primary pull-right" id="edit" value="Edit" style="display: none;">
                <button class="btn btn-primary" type="button" id="loading_form" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- end modal usulan -->
    <!-- MODAL -->

    <script type="text/javascript" src="<?= base_url('assets_tambahan/') ?>easyui/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?= base_url('assets_tambahan/') ?>easyui/jquery.edatagrid.js"></script>

    <script type="text/javascript">
    /* Klik Tab Usulan */
      function div_usulan() {
        $('#cari_usulan').show();
        $('#cari_waspro').hide();
        $('#cari_cangun').hide();
        $('#cari_selesai').hide();
        $('#div_perencana').hide();
        $('#div_usulan').show();
        $('#div_waspro').hide();
        $('#div_cangun').hide();
        $('#div_selesai').hide();
        $('#link_div_usulan').addClass('active bg-secondary bg-gradient');
        $('#link_div_waspro').removeClass('active bg-secondary bg-gradient');
        $('#link_div_cangun').removeClass('active bg-secondary bg-gradient');
        $('#link_div_selesai').removeClass('active bg-secondary bg-gradient');
        $('#pekerjaan_status_nama').val('usulan');
        $('#table_usulan').DataTable().ajax.reload();
      }
    /* Klik Tab Usulan */
    /* Klik Tab waspro */
      function div_waspro() {
        $('#cari_usulan').hide();
        $('#cari_waspro').show();
        $('#cari_cangun').hide();
        $('#cari_selesai').hide();
        $('#div_perencana').hide();
        $('#div_usulan').hide();
        $('#div_waspro').show();
        $('#div_cangun').hide();
        $('#div_selesai').hide();
        $('#link_div_usulan').removeClass('active bg-secondary bg-gradient');
        $('#link_div_waspro').addClass('active bg-secondary bg-gradient');
        $('#link_div_cangun').removeClass('active bg-secondary bg-gradient');
        $('#link_div_selesai').removeClass('active bg-secondary bg-gradient');
        $('#pekerjaan_status_nama').val('waspro');
        $('#table_waspro').DataTable().ajax.reload();
      }
    /* Klik Tab waspro */
    /* Klik Tab berjalan */
      function div_cangun() {
        $('#cari_usulan').hide();
        $('#cari_waspro').hide();
        $('#cari_cangun').show();
        $('#cari_selesai').hide();
        $('#div_perencana').hide();
        $('#div_usulan').hide();
        $('#div_waspro').hide();
        $('#div_cangun').show();
        $('#div_selesai').hide();
        $('#link_div_usulan').removeClass('active bg-secondary bg-gradient');
        $('#link_div_waspro').removeClass('active bg-secondary bg-gradient');
        $('#link_div_cangun').addClass('active bg-secondary bg-gradient');
        $('#link_div_selesai').removeClass('active bg-secondary bg-gradient');
        $('#pekerjaan_status_nama').val('cangun');
        $('#table_cangun').DataTable().ajax.reload();
      }
    /* Klik Tab berjalan */
    /* Klik Tab IFA */
      function div_selesai() {
        $('#cari_usulan').hide();
        $('#cari_waspro').hide();
        $('#cari_cangun').hide();
        $('#cari_selesai').show();
        $('#div_perencana').hide();
        $('#div_usulan').hide();
        $('#div_waspro').hide();
        $('#div_cangun').hide();
        $('#div_selesai').show();
        $('#link_div_usulan').removeClass('active bg-secondary bg-gradient');
        $('#link_div_waspro').removeClass('active bg-secondary bg-gradient');
        $('#link_div_cangun').removeClass('active bg-secondary bg-gradient');
        $('#link_div_selesai').addClass('active bg-secondary bg-gradient');
        $('#pekerjaan_status_nama').val('selesai');
        $('#table_selesai').DataTable().ajax.reload();
      }
    /* Klik Tab IFA */
    /* TAB */


      $(function() {
      /* get list pekerjaan selesai */
        $('#pekerjaan_judul_list').select2({
          dropdownParent: $('#modal_waspro'),
          placeholder: 'Pilih',
          ajax: {
          // delay: 250,
            url: '<?= base_url('project/transmital/getPekerjaanList?klasifikasi_pekerjaan_id=y&pekerjaan_status=14,15') ?>',
            dataType: 'JSON',
            type: 'GET',
            data: function(params) {
              var queryParameters = {
                q: params.term
              }

              return queryParameters;
            },
          }
        })
      /* get list pekerjaan selesai */

      /*get user list*/
        $('#id_user_cari').select2({
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
      /*get user list*/

        $('.select2-selection').css({
          height: 'auto',
          margin: '0px -10px 0px -10px'
        });
        $('.select2').css('width', '100%');
      });

      fun_loading();
    // START TABLE
    /* Start Isi Table Usulan */
      $('#table_usulan thead tr').clone(true).addClass('filters_usulan').appendTo('#table_usulan thead');
      $('#table_usulan').DataTable({
        orderCellsTop: true,
        initComplete: function() {
          var api = this.api();
          api.columns().eq(0).each(function(colIdx) {
            var cell = $('.filters_usulan th').eq(
              $(api.column(colIdx).header()).index()
              );
            var title = $(cell).text();
            $(cell).html('<input type="text" class="form-control" style="width:100%" placeholder="' + title + '" />');

            $('input', $('.filters_usulan th').eq($(api.column(colIdx).header()).index())).off('keyup change').on('keyup change', function(e) {
              e.stopPropagation();
              $(this).attr('title', $(this).val());
              var regexr = '({search})';
              var cursorPosition = this.selectionStart;

              api.column(colIdx).search(this.value != '' ? regexr.replace('{search}', '(((' + this.value + ')))') : '', this.value != '', this.value == '').draw();

              $(this).focus()[0].setSelectionRange(cursorPosition, cursorPosition);
            });
          });
        },
        "scrollX": true,
        "ajax": {
          "url": "<?= base_url() ?>project/transmital/getPekerjaanWasproUsulan?pekerjaan_status=14,15",
          "dataSrc": ""
        },
        "columns": [{
          render: function(data, type, full, meta) {
            var urut = '';
            urut = meta.row + meta.settings._iDisplayStart + 1;
            return urut;
          }
        },
        {
          render: function(data, type, full, meta) {
            var nomor = ''
            var nomornya = ''
            if (full.pekerjaan_nomor != null) 
              nomor = full.pekerjaan_nomor.split('-'),
            nomor[0] = pad(nomor[0], 3),
            nomornya =  nomor.join('-')
            else 
            nomornya =  full.pekerjaan_nomor

            if (full.milik == 'y') 
              return '<span class="badge" style="background-color:#c13333 ">'+ nomornya+'</span>'
            else
              return nomornya
          }
        },
        {
          "data": "tanggal_awal"
        },
        {
          render: function(data, type, full, meta) {
            return full.pekerjaan_judul;
          }
        },
        {
          "data": "pegawai_nama_dep"
        },
        {
          "data": "usr_name"
        },
        {
          "render": function(data, type, full, meta) {
            var status = '';
            var warna = '';
            if (full.pekerjaan_disposisi_transmital_status == null) {
              status = 'Draft';
              warna = '#A0A0A0';
            } else if (full.pekerjaan_disposisi_transmital_status == 1) {
              warna = '#FFFF00';
              status = 'Sudah Review AVP';
            } else if (full.pekerjaan_disposisi_transmital_status == 1) {
              warna = '#FFFF00';
              status = 'Menunggu Review AVP';
            } else if (full.pekerjaan_disposisi_transmital_status == 2) {
              warna = '#FFFF00';
              status = 'In Progress';
            } else if (full.pekerjaan_disposisi_transmital_status == 3) {
              warna = '#FFFF00';
              status = 'Menunggu Approve';
            } else if (full.pekerjaan_disposisi_transmital_status == '-') {
              warna = '#FF0000';
              status = 'Reject'
            }
            return '<span class="lead"><span class="badge" style="background-color: ' + warna + ';color:black  ">' + status + '</span></span>';
          }
        },
        {
          "render": function(data, type, full, meta) {
            return '<center><a href="<?= base_url('project/transmital/detail?aksi=usulan') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=0"  title="Detail" ><i class="btn btn-info btn-sm" >Detail</i></a></center>';
          }
        },
        {
          "render": function(data, type, full, meta) {
            return ((full.pekerjaan_status == '0' || full.pekerjaan_status == '-') && full.pic == $('#session_nik').val()) ? '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" title="Edit" onclick="fun_edit(this.id)"><i class="btn btn-success btn-sm" >Edit</i></a></center>' : '<center>-</center>';
          }
        },
        {
          "render": function(data, type, full, meta) {
            return ((full.pekerjaan_status == '0' || full.pekerjaan_status == '-') && full.pic == $('#session_nik').val()) ? '<center><a href="javascript:;" id="' + full.pekerjaan_id + '" title="Delete" onclick="fun_delete(this.id)"><i class="btn btn-danger btn-sm">Delete</i></a></center>' : '<center>-</center>';
          }
        },
        ]
      })
    /*End Isi Table Usulan */

    /* Start Isi Table Waspro */
$('#table_waspro thead tr').clone(true).addClass('filters_waspro').appendTo('#table_waspro thead');
$('#table_waspro').DataTable({
  orderCellsTop: true,
  initComplete: function() {
    var api = this.api();
    api.columns().eq(0).each(function(colIdx) {
      var cell = $('.filters_waspro th').eq(
        $(api.column(colIdx).header()).index()
        );
      var title = $(cell).text();
      $(cell).html('<input type="text" class="form-control" style="width:100%" placeholder="' + title + '" />');

      $('input', $('.filters_waspro th').eq($(api.column(colIdx).header()).index())).off('keyup change').on('keyup change', function(e) {
        e.stopPropagation();
        $(this).attr('title', $(this).val());
        var regexr = '({search})';
        var cursorPosition = this.selectionStart;

        api.column(colIdx).search(this.value != '' ? regexr.replace('{search}', '(((' + this.value + ')))') : '', this.value != '', this.value == '').draw();

        $(this).focus()[0].setSelectionRange(cursorPosition, cursorPosition);
      });
    });
  },
  "scrollX": true,
  "ajax": {
    "url": "<?= base_url() ?>project/transmital/getPekerjaanWaspro?pekerjaan_status=14,15&klasifikasi_pekerjaan_id=1&pekerjaan_transmital_status=0,1",
    "dataSrc": ""
  },
  "columns": [{
    render: function(data, type, full, meta) {
      var urut = '';
      urut = meta.row + meta.settings._iDisplayStart + 1;
      return urut;
    }
  },
  {
    render: function(data, type, full, meta) {
      var nomor = ''
      var nomornya = ''
      if (full.pekerjaan_nomor != null) 
        nomor = full.pekerjaan_nomor.split('-'),
      nomor[0] = pad(nomor[0], 3),
      nomornya =  nomor.join('-')
      else 
      nomornya =  full.pekerjaan_nomor

      if (full.milik == 'y') 
        return '<span class="badge" style="background-color:#c13333 ">'+ nomornya+'</span>'
      else
        return nomornya
    }
  },
  {
    "data": "tanggal_awal"
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_judul;
    }
  },
  {
    "data": "pegawai_nama_dep"
  },
  {
    "data": "usr_name"
  },
  {
    "render": function(data, type, full, meta) {
      var status = '';
      var warna = '';
      if (full.pekerjaan_disposisi_transmital_status == '0')
        status = 'Menunggu Send PIC Waspro',
      warna = '#A0A0A0'
      else if (full.pekerjaan_disposisi_transmital_status == '1')
        warna = '#FFFF00',
      status = 'Menunggu Review AVP Waspro'
      else if (full.pekerjaan_disposisi_transmital_status == '2')
        warna = '#FFFF00',
      status = 'In Progress'
      else if (full.pekerjaan_disposisi_transmital_status == '3')
        warna = '#FFFF00',
      status = 'Menunggu Reviewed AVP Cangun'
      else if (full.pekerjaan_disposisi_transmital_status == '4')
        warna = '#FFFF00',
      status = 'Menungggu Approved VP Cangun'
      else if (full.pekerjaan_disposisi_transmital_status == '5')
        warna = 'green',
      status = 'Selesai'
      else if (full.pekerjaan_disposisi_transmital_status == '-')
        warna = '#FF0000',
      status = 'Reject'
      return '<span class="lead"><span class="badge" style="background-color: ' + warna + ';color:black  ">' + status + '</span></span>';
    }
  },
  {
    "render": function(data, type, full, meta) {
      return '<center><a href="<?= base_url('project/transmital/detail?aksi=waspro') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_transmital_status + '"  title="Detail" ><i class="btn btn-info btn-sm" >Detail</i></a></center>';
    }
  },

  ]
})
    /*End Isi Table Waspro */

    /* start table_cangun */
$('#table_cangun thead tr').clone(true).addClass('filters_cangun').appendTo('#table_cangun thead');
$('#table_cangun').DataTable({
  orderCellsTop: true,
  initComplete: function() {
    var api = this.api();
    api.columns().eq(0).each(function(colIdx) {
      var cell = $('.filters_cangun th').eq(
        $(api.column(colIdx).header()).index()
        );
      var title = $(cell).text();
      $(cell).html('<input type="text" class="form-control" style="width:100%" placeholder="' + title + '" />');

      $('input', $('.filters_cangun th').eq($(api.column(colIdx).header()).index())).off('keyup change').on('keyup change', function(e) {
        e.stopPropagation();
        $(this).attr('title', $(this).val());
        var regexr = '({search})';
        var cursorPosition = this.selectionStart;

        api.column(colIdx).search(this.value != '' ? regexr.replace('{search}', '(((' + this.value + ')))') : '', this.value != '', this.value == '').draw();

        $(this).focus()[0].setSelectionRange(cursorPosition, cursorPosition);
      });
    });
  },
  "scrollX": true,
  "ajax": {
    "url": "<?= base_url() ?>project/transmital/getPekerjaanWaspro?pekerjaan_status=14,15&klasifikasi_pekerjaan_id=1&pekerjaan_transmital_status=2,3,4",
    "dataSrc": ""
  },
  "columns": [{
    render: function(data, type, full, meta) {
      urut = meta.row + meta.settings._iDisplayStart + 1
      return urut
    }
  },
  {
    render: function(data, type, full, meta) {
      var nomor = ''
      var nomornya = ''
      if (full.pekerjaan_nomor != null) 
        nomor = full.pekerjaan_nomor.split('-'),
      nomor[0] = pad(nomor[0], 3),
      nomornya =  nomor.join('-')
      else 
      nomornya =  full.pekerjaan_nomor

      if (full.milik == 'y') 
        return '<span class="badge" style="background-color:#c13333 ">'+ nomornya+'</span>'
      else
        return nomornya
    }
  },
  {
    "data": "tanggal_awal"
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_judul;
    }
  },
  {
    "data": "pegawai_nama_dep"
  },
  {
    "data": "usr_name"
  },
  {
    "render": function(data, type, full, meta) {
      var status = '';
      var warna = '';
      if (full.pekerjaan_disposisi_transmital_status == '0')
        status = 'Menunggu Send PIC Waspro',
      warna = '#A0A0A0'
      else if (full.pekerjaan_disposisi_transmital_status == '1')
        warna = '#FFFF00',
      status = 'Menunggu Review AVP Waspro'
      else if (full.pekerjaan_disposisi_transmital_status == '2')
        warna = '#FFFF00',
      status = 'In Progress'
      else if (full.pekerjaan_disposisi_transmital_status == '3')
        warna = '#FFFF00',
      status = 'Menunggu Reviewed AVP Cangun'
      else if (full.pekerjaan_disposisi_transmital_status == '4')
        warna = '#FFFF00',
      status = 'Menungggu Approved VP Cangun'
      else if (full.pekerjaan_disposisi_transmital_status == '5')
        warna = 'green',
      status = 'Selesai'
      else if (full.pekerjaan_disposisi_transmital_status == '-')
        warna = '#FF0000',
      status = 'Reject'
      return '<span class="lead"><span class="badge" style="background-color: ' + warna + ';color:black  ">' + status + '</span></span>';
    }
  },
  {
    "render": function(data, type, full, meta) {
      return '<center><a href="<?= base_url('project/transmital/detail?aksi=cangun') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=' + full.pekerjaan_transmital_status + '&bagian_id=' + full.bagian_id + '"  title="Detail" ><i class="btn btn-info btn-sm" >Detail</i></a></center>';
    }
  },
  ]
})
    /* start table_cangun */

    /* start table_selesai */
$('#table_selesai thead tr').clone(true).addClass('filters_selesai').appendTo('#table_selesai thead');
$('#table_selesai').DataTable({
  orderCellsTop: true,
  initComplete: function() {
    var api = this.api();
    api.columns().eq(0).each(function(colIdx) {
      var cell = $('.filters_selesai th').eq(
        $(api.column(colIdx).header()).index()
        );
      var title = $(cell).text();
      $(cell).html('<input type="text" class="form-control" style="width:100%" placeholder="' + title + '" />');

      $('input', $('.filters_selesai th').eq($(api.column(colIdx).header()).index())).off('keyup change').on('keyup change', function(e) {
        e.stopPropagation();
        $(this).attr('title', $(this).val());
        var regexr = '({search})';
        var cursorPosition = this.selectionStart;

        api.column(colIdx).search(this.value != '' ? regexr.replace('{search}', '(((' + this.value + ')))') : '', this.value != '', this.value == '').draw();

        $(this).focus()[0].setSelectionRange(cursorPosition, cursorPosition);
      });
    });
  },
  "scrollX": true,
  "ajax": {
    "url": "<?= base_url() ?>project/transmital/getPekerjaanWaspro?pekerjaan_status=14,15&klasifikasi_pekerjaan_id=1&pekerjaan_transmital_status=5&pekerjaan_is_selesai=y",
    "dataSrc": ""
  },
  "columns": [{
    render: function(data, type, full, meta) {
      var urut = '';
      urut = meta.row + meta.settings._iDisplayStart + 1;
      return urut;
    }
  },
  {
    render: function(data, type, full, meta) {
      var nomor = ''
      var nomornya = ''
      if (full.pekerjaan_nomor != null) 
        nomor = full.pekerjaan_nomor.split('-'),
      nomor[0] = pad(nomor[0], 3),
      nomornya =  nomor.join('-')
      else 
      nomornya =  full.pekerjaan_nomor

      if (full.milik == 'y') 
        return '<span class="badge" style="background-color:#c13333 ">'+ nomornya+'</span>'
      else
        return nomornya
    }
  },
  {
    "data": "tanggal_awal"
  },
  {
    render: function(data, type, full, meta) {
      return full.pekerjaan_judul;
    }
  },
  {
    "data": "pegawai_nama_dep"
  },
  {
    "data": "usr_name"
  },
  {
    "render": function(data, type, full, meta) {
      var status = '';
      var warna = '';
      if (full.pekerjaan_disposisi_transmital_status == '0')
        status = 'Menunggu Send PIC Waspro',
      warna = '#A0A0A0'
      else if (full.pekerjaan_disposisi_transmital_status == '1')
        warna = '#FFFF00',
      status = 'Menunggu Review AVP Waspro'
      else if (full.pekerjaan_disposisi_transmital_status == '2')
        warna = '#FFFF00',
      status = 'In Progress'
      else if (full.pekerjaan_disposisi_transmital_status == '3')
        warna = '#FFFF00',
      status = 'Menunggu Reviewed AVP Cangun'
      else if (full.pekerjaan_disposisi_transmital_status == '4')
        warna = '#FFFF00',
      status = 'Menungggu Approved VP Cangun'
      else if (full.pekerjaan_disposisi_transmital_status == '5')
        warna = 'green',
      status = 'Selesai'
      else if (full.pekerjaan_disposisi_transmital_status == '-')
        warna = '#FF0000',
      status = 'Reject'
      return '<span class="lead"><span class="badge" style="background-color: ' + warna + ';color:black  ">' + status + '</span></span>';
    }
  },
  {
    "render": function(data, type, full, meta) {
      return '<center><a href="<?= base_url('project/transmital/detail?aksi=selesai') ?>' + '&pekerjaan_id=' + full.pekerjaan_id + '&status=5"  title="Detail" ><i class="btn btn-info btn-sm" >Detail</i></a></center>';
    }
  },
  ]
})
    /* start table_selesai */

$('#cari_filter').on('click', function(e) {
  var data = $('#filter').serialize();
  $('#table_waspro').DataTable().ajax.url('<?= base_url('project/pekerjaan_usulan/getPekerjaanUsulan?klasifikasi_pekerjaan_id=1&pekerjaan_status=-,0,1,2,3,4&') ?>' + data).load();
  $('#table_selesai').DataTable().ajax.url('<?= base_url('project/pekerjaan_usulan/getPekerjaanUsulan?klasifikasi_pekerjaan_id=1&pekerjaan_status=14,15,16&') ?>' + data).load();
})

    /* FROM CARI SUBMIT */
$('#cari_waspro').on('click', function(e) {
      // alert('tes')
      // e.preventDefault();
  var data = $('#filter').serialize();
  $('#table_berjalan').DataTable().ajax.url('<?= base_url('project/pekerjaan_usulan/getPekerjaanBerjalan?klasifikasi_pekerjaan_id=1&pekerjaan_status=5,6,7') ?>' + data).load();
})
    /* FROM CARI SUBMIT */

    /* FROM CARI SUBMIT */
$('#cari_cangun').on('click', function(e) {
      // alert('tes')
      // e.preventDefault();
  var data = $('#filter').serialize();
  $('#table_ifa').DataTable().ajax.url('<?= base_url('project/pekerjaan_usulan/getPekerjaanUsulan?klasifikasi_pekerjaan_id=1&pekerjaan_status=8,9,10') ?>' + data).load();
})
    /* FROM CARI SUBMIT */

    /* FROM CARI SUBMIT */
$('#cari_selesai').on('click', function(e) {
      // alert('tes')
      // e.preventDefault();
  var data = $('#filter').serialize();
  $('#table_ifc').DataTable().ajax.url('<?= base_url('project/pekerjaan_usulan/getPekerjaanUsulan?klasifikasi_pekerjaan_id=1&pekerjaan_status=11,12,13') ?>' + data).load();
})
    /* FROM CARI SUBMIT */

tinymce.init({
  selector: "textarea#pekerjaan_deskripsi",
  height: 300,
  plugins: [
    "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
    "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
    "save table directionality emoticons template paste "
    ],
  toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons",
  style_formats: [{
    title: 'Bold text',
    inline: 'b'
  },
  {
    title: 'Red text',
    inline: 'span',
    styles: {
      color: '#ff0000'
    }
  },
  {
    title: 'Red header',
    block: 'h1',
    styles: {
      color: '#ff0000'
    }
  },
  {
    title: 'Example 1',
    inline: 'span',
    classes: 'example1'
  },
  {
    title: 'Example 2',
    inline: 'span',
    classes: 'example2'
  },
  {
    title: 'Table styles'
  },
  {
    title: 'Table row 1',
    selector: 'tr',
    classes: 'tablerow1'
  }
  ]
});

    /* Fun Textarea */
function fun_textarea() {
  tinymce.init({
    selector: "textarea#pekerjaan_deskripsi",
    height: 300,
    plugins: [
      "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
      "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
      "save table contextmenu directionality emoticons template paste textcolor"
      ],
    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons",
    style_formats: [{
      title: 'Bold text',
      inline: 'b'
    },
    {
      title: 'Red text',
      inline: 'span',
      styles: {
        color: '#ff0000'
      }
    },
    {
      title: 'Red header',
      block: 'h1',
      styles: {
        color: '#ff0000'
      }
    },
    {
      title: 'Example 1',
      inline: 'span',
      classes: 'example1'
    },
    {
      title: 'Example 2',
      inline: 'span',
      classes: 'example2'
    },
    {
      title: 'Table styles'
    },
    {
      title: 'Table row 1',
      selector: 'tr',
      classes: 'tablerow1'
    }
    ]
  });
}
    /* Fun Textarea */

    /* Klik Tambah */
function fun_tambah_waspro() {
      // $('#pekerjaan_id').val(Date.now());
  $('#jabatan_temp').val('<?= substr($session['pegawai_jabatan'], 0, 1) ?>');
  $('#modal_waspro').modal('show');

      // fun_textarea();

  setTimeout(function() {
    $('#dg_document').edatagrid({
      url: '<?= base_url('project/pekerjaan_usulan/getPekerjaanDokumen?id_pekerjaan=') ?>' + $('#pekerjaan_id').val(),
      saveUrl: '<?= base_url('project/pekerjaan_usulan/insertPekerjaanDokumenUsulan?') ?>',
      updateUrl: '<?= base_url('project/pekerjaan_usulan/updatePekerjaanDokumen?') ?>',
      onEndEdit: function(index, row) {
        var e = $(this).datagrid('getEditor', {
          index: index,
          field: 'pekerjaan_dokumen_file'
        });
        var files = $(e.target).filebox('files');
        if (files.length) row.savedFileName = e.target.filebox('getText');
      },
      columns: [
        [{
          field: 'pekerjaan_dokumen_nama',
          title: 'Judul Dokumen',
          width: '50%',
          editor: {
            type: 'textbox',
            options: {
              required: true,
              onchange: function(value) {
                console.log(value);
                $("#doc_nama").val(value);
              }
            }
          },
        }, {
          field: 'pekerjaan_dokumen_file',
          title: 'File',
          width: '50%',
          formatter: (value, row) => row.fileName || value,
          editor: {
            type: 'filebox',
            options: {
              required: true,
              accept: 'application/pdf',
              buttonText: '...',
              onChange: function() {
                var self = $(this);
                var files = self.filebox('files');
                var formData = new FormData();
                var nama = $("#doc_nama").val();
                self.filebox('setText', 'Menyimpan...');

                formData.append('id_pekerjaan', $('#pekerjaan_id').val());

                for (var i = 0; i < files.length; i++) {
                  var file = files[i];
                  formData.append('file', file, file.name);
                }

                $.ajax({
                  url: '<?= base_url('project/pekerjaan_usulan/insertFilePekerjaanDokumen') ?>',
                  type: 'post',
                  data: formData,
                  contentType: false,
                  processData: false,
                  beforeSend: function() {
                    $.messager.progress({
                      title: 'Uploading',
                      msg: 'Uploading file...',
                      interval: 1000
                    });
                  },
                  complete: function() {
                    $.messager.progress('close');
                  },
                  success: function(data) {
                    self.filebox('setText', data);
                  }
                })
              }
            },
          },
        },
        {
          field: 'pekerjaan_dokumen_id',
          title: 'Lihat',
          width: '33%',
          formatter: function(value, row, index) {
            if (row.pekerjaan_dokumen_file) {
              return '<a href="#" onclick="viewFile(\'' + row.pekerjaan_dokumen_file + '\')">Lihat File</a>';
            } else {
              return value;
            }
          },

        },
        ],
        ],
    });
  }, 500);
}
    /* Klik Tambah */

    /* Klik Edit */
function fun_edit(id) {
  $('#modal_waspro').modal('show');
  $('#simpan').hide();
  $('#edit').show();
      // $('#div_pekerjaan_note').show();

  fun_textarea();

  $.getJSON('<?= base_url() ?>project/pekerjaan_usulan/getPekerjaanUsulan?pekerjaan_id=' + id, function(json) {
    if (json.pekerjaan_status == '-') {
      $('#div_pekerjaan_note').show();
      $('#pekerjaan_note').val(json.pekerjaan_note);
    } else {
      $('#div_pekerjaan_note').hide();
    }

    if (json.pekerjaan_deskripsi) {
      tinymce.get("pekerjaan_deskripsi").setContent(json.pekerjaan_deskripsi);
    }
    $('#id_klasifikasi_pekerjaan').val(json.klasifikasi_pekerjaan_id);
    $('#klasifikasi_pekerjaan_nama').val(json.klasifikasi_pekerjaan_nama);
    $('#pekerjaan_id').val(json.pekerjaan_id);
    $('#jabatan_temp').val('<?= substr($session['pegawai_jabatan'], 0, 1) ?>');
    $('#pekerjaan_status').val('1');
    $('#pekerjaan_waktu').val(json.tanggal_awal);
    $('#pekerjaan_waktu_akhir').val(json.tanggal_akhir);
    $('#pic').val(json.pic);
    $('#pic_no_telp').val(json.pic_no_telp);
    $('#pekerjaan_judul').val(json.pekerjaan_judul);
    $('#pekerjaan_tahun').val(json.pekerjaan_tahun);

        /*SELECTED SELECT2*/
    $.getJSON('<?= base_url() ?>project/RKAP/getUserListRevApp2', {
      pegawai_nik: json.pekerjaan_reviewer
    }, function(jsonReviewer) {
      var newOption = new Option(jsonReviewer.text, jsonReviewer.id, true, true);
      $('#reviewer').append(newOption).trigger('change');
    });

    $.getJSON('<?= base_url() ?>project/RKAP/getUserListRevApp2', {
      pegawai_nik: json.pekerjaan_approver
    }, function(jsonApprover) {
      var newOption = new Option(jsonApprover.text, jsonApprover.id, true, true);
      $('#approver').append(newOption).trigger('change');
    });
        /*SELECTED SELECT2*/
    tinymce.get("pekerjaan_deskripsi").setContent(json.pekerjaan_deskripsi);
  });

  setTimeout(function() {
    $('#dg_document').edatagrid({
      url: '<?= base_url('project/pekerjaan_usulan/getPekerjaanDokumen?id_pekerjaan=') ?>' + id,
      saveUrl: '<?= base_url('project/pekerjaan_usulan/insertPekerjaanDokumenUsulan?') ?>',
      updateUrl: '<?= base_url('project/pekerjaan_usulan/updatePekerjaanDokumen?') ?>',
      onEndEdit: function(index, row) {
        var e = $(this).datagrid('getEditor', {
          index: index,
          field: 'pekerjaan_dokumen_file'
        });
        var files = $(e.target).filebox('files');
        if (files.length) row.savedFileName = e.target.filebox('getText');
      },
      columns: [
        [{
          field: 'pekerjaan_dokumen_nama',
          title: 'Judul Dokumen',
          width: '50%',
          editor: {
            type: 'textbox',
            options: {
              required: true,
              onchange: function(value) {
                console.log(value);
                $("#doc_nama").val(value);
              }
            }
          },
        }, {
          field: 'pekerjaan_dokumen_file',
          title: 'File',
          width: '50%',
          formatter: (value, row) => row.fileName || value,
          editor: {
            type: 'filebox',
            options: {
              required: true,
              accept: 'application/pdf',
              buttonText: '...',
              onChange: function() {
                var self = $(this);
                var files = self.filebox('files');
                var formData = new FormData();
                var nama = $("#doc_nama").val();
                self.filebox('setText', 'Menyimpan...');

                formData.append('id_pekerjaan', $('#pekerjaan_id').val());

                for (var i = 0; i < files.length; i++) {
                  var file = files[i];
                  formData.append('file', file, file.name);
                }

                $.ajax({
                  url: '<?= base_url('project/pekerjaan_usulan/insertFilePekerjaanDokumen') ?>',
                  type: 'post',
                  data: formData,
                  contentType: false,
                  processData: false,
                  beforeSend: function() {
                    $.messager.progress({
                      title: 'Uploading',
                      msg: 'Uploading file...',
                      interval: 1000
                    });
                  },
                  complete: function() {
                    $.messager.progress('close');
                  },
                  success: function(data) {
                    self.filebox('setText', data);
                  }
                })
              }
            },
          },
        }, ],
        ],
    });
  }, 1500);
}
    /* Klik Edit */

    /* Proses  Simpan*/
$('#simpan').on('click', function() {

  var data = new FormData($('#form_modal_waspro')[0]);
  $.ajax({
    url: '<?= base_url('project/transmital/insertPekerjaan') ?>',
    data: data,
    type: 'POST',
    dataType: 'html',
    cache: false,
    contentType: false,
    processData: false,
    beforeSend: function() {
      $('#loading_form').show();
      $('#simpan').hide();
      $('#edit').hide();
    },
    complete: function() {
      $('#loading_form').hide();
      $('#simpan').show();
      $('#simpan').show();
      $('#edit').hide();
    },
    success: function(isi) {
      $('#close').click();
      toastr.success('Berhasil');
    }
  });
});
    /* Proses Send*/

    /* Proses Update*/
$('#edit').on('click', function() {
  var isi_awal = $('#dg_document').data('datagrid').data.rows[0];
  if ($('#dg_document').data('datagrid').data.total > 0 && 'isNewRecord' in isi_awal) {
    $.messager.alert('Peringatan', 'Data Dokumen Masih Ada Yang Belum Tersimpan');
  } else {
    if ($('#pekerjaan_waktu').val() == '') {
      $('#pekerjaan_waktu_alert').show();
    } else {
      $('#pekerjaan_waktu_alert').hide();
    }
    if ($('#pic').val() == '') {
      $('#pic_alert').show();
    } else {
      $('#pic_alert').hide();
    }
    if ($('#pic_no_telp').val() == '') {
      $('#pic_no_telp_alert').show();
    } else {
      $('#pic_no_telp_alert').hide();
    }
    if ($('#pekerjaan_judul').val() == '') {
      $('#pekerjaan_judul_alert').show();
    } else {
      $('#pekerjaan_judul_alert').hide();
    }

    if ($('#pekerjaan_waktu').val() != '' && $('#pic').val() != '' && $('#pic_no_telp').val() != '' && $('#pekerjaan_judul').val() != '') {
      var pekerjaan_deskripsi = tinymce.get('pekerjaan_deskripsi').getContent();
      var data = $('#form_modal_waspro').serialize();
      data += '&pekerjaan_deskripsi=' + escape(pekerjaan_deskripsi);
      $.ajax({
        url: '<?= base_url('project/pekerjaan_usulan/updatePekerjaan') ?>',
        data: data,
        type: 'POST',
        dataType: 'html',
        beforeSend: function() {
          $('#loading_form').show();
          $('#simpan').hide();
          $('#edit').hide();
        },
        complete: function() {
          $('#loading_form').hide();
          $('#simpan').hide();
          $('#edit').show();
        },
        success: function(isi) {
          $('#close').click();
          toastr.success('Berhasil');
        }
      });
    }
  }
});
    /* Proses Update*/

    /* Klik Detail */
function fun_detail(id, val) {
  call_ajax_page('project/pekerjaan_usulan/detailPekerjaan?aksi=' + $('#pekerjaan_status_nama').val() + '&pekerjaan_id=' + id + '&status=' + val + '&rkap=1');
}

function fun_detail_disposisi(id, val) {
  call_ajax_page('project/pekerjaan_usulan/detailPekerjaan?aksi=' + $('#pekerjaan_status_nama').val() + '&pekerjaan_id=' + id + '&status=' + val + '&rkap=1');
}
    /* Klik Detail */

    /* Fun Delete */
function fun_delete(id) {
  Swal.fire({
    title: 'Apakah anda yakin akan menghapusnya?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Hapus!'
  }).then((result) => {
    if (result.isConfirmed) {
      $.get('<?= base_url() ?>project/pekerjaan_usulan/deletePekerjaan', {
        pekerjaan_id: id
      }, function(data) {
        $('#close').click();
        toastr.success('Berhasil');
      });
    }
  })
}
    /* Fun Delete */

    /* EASYUI */
    /* Fun Tambah */
function fun_tambah_document() {
  var isi_awal = $('#dg_document').data('datagrid').data.rows[0];
  if ($('#dg_document').data('datagrid').data.total > 0 && 'isNewRecord' in isi_awal) {
    $.messager.alert('Peringatan', 'Data Dokumen Masih Ada Yang Belum Tersimpan');
  } else {
    $('#dg_document').edatagrid('addRow', {
      index: 0,
      row: {
        pekerjaan_id: $('#pekerjaan_id').val(),
      }
    });
  }
}
    /* Fun Tambah */

    /* Fun Simpan */
function fun_simpan_document() {
  $('#dg_document').edatagrid('saveRow');
  setTimeout(() => {
    $('#dg_document').datagrid('reload');
  }, 1000);
}
    /* Fun Simpan */

    /* Fun Hapus */
function fun_hapus_document() {
  var row = $('#dg_document').datagrid('getSelected');
  $.post('<?= base_url('/project/pekerjaan_usulan/deletePekerjaanDokumen') ?>', {
    pekerjaan_dokumen_id: row.pekerjaan_dokumen_id
  }, function(data, textStatus, xhr) {
    $('#dg_document').datagrid('reload');
  });
}
    /* Fun Hapus */
    /* EASYUI */

    /* Close */
$('#modal_waspro').on('hidden.bs.modal', function(e) {
  fun_close_usulan();
});

function fun_close_usulan() {
  fun_loading();
  $('#table_usulan').DataTable().ajax.reload(null, false);
  $('#simpan').show();
  $('#edit').hide();
  $('#div_pekerjaan_note').hide();
  $('#form_modal_waspro')[0].reset();
  $('#modal_waspro').modal('hide');
  $('#id_klasifikasi_pekerjaan').empty();
      // tinymce.remove('#pekerjaan_deskripsi');
      // alert
  $('#pekerjaan_waktu_alert').hide();
  $('#pic_alert').hide();
  $('#pic_no_telp_alert').hide();
  $('#pekerjaan_judul_alert').hide();
}

function fun_close_ifa() {
  $('#simpan_ajuan_extend').show();
  $('#simpan_extend').show();
  $('#edit_ajuan_extend').hide();
  $('#edit_extend').hide();
  $('#table_data').hide();
  $('#tableDiv').hide();
  $('#formDiv').show();
  $('#form_modal_ajuan_extend')[0].reset();
  $('#form_modal_extend')[0].reset();
  $('#modal_ajuan_extend').modal('hide');
  $('#modal_extend').modal('hide');
  $('#table').DataTable().ajax.reload(null, false);
}
    /* Close */

    /* Loading */
function fun_loading() {
  var simplebar = new Nanobar();
  simplebar.go(100);
}
    /* Loading */

    // start IFA (tambah)
    // AJUAN EXTEND
function fun_ajuan_extend(id, status) {
  $('#modal_ajuan_extend').modal('show');
  $('#id_pekerjaan_ajuan_extend').val(id);
  $('#pekerjaan_status_ajuan_extend').val(status);
  $.getJSON('<?= base_url('project/Pekerjaan_usulan/getExtend') ?>', {
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
    var url = '<?= base_url('project/Pekerjaan_usulan/updateAjuanExtend') ?>';
  else var url = '<?= base_url('project/Pekerjaan_usulan/insertAjuanExtend') ?>';

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
  $.getJSON('<?= base_url('project/Pekerjaan_usulan/getExtend') ?>', {
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
    var url = '<?= base_url('project/Pekerjaan_usulan/updateAjuanExtend') ?>';
  else var url = '<?= base_url('project/Pekerjaan_usulan/insertAjuanExtend') ?>';

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
    // end IFA (tambah)

    /* Zero Padding */
function pad(str, max) {
  str = str.toString();
  return str.length < max ? pad("0" + str, max) : str;
}
    /* Zero Padding */
</script>