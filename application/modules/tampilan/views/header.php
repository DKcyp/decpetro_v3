<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8" />
  <title>Dec Petrokimia</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
  <meta content="Themesbrand" name="author" />
  <!-- App favicon -->
  <link rel="shortcut icon" href="<?= base_url() ?>assets/images/logo_dec.jpeg">

  <!-- CSS -->

  <!-- Bootstrap Css -->
  <link href="<?= base_url() ?>assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
  <!-- Icons Css -->
  <link href="<?= base_url() ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
  <!-- App Css-->
  <link href="<?= base_url() ?>assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
  <!-- Custom Css-->
  <link href="<?= base_url() ?>assets/css/custom.css" rel="stylesheet" type="text/css" />

  <!-- DataTables -->
  <link href="<?= base_url() ?>assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="<?= base_url() ?>assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="<?= base_url() ?>assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />

  <!-- toastr  -->
  <link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/libs/toastr/toastr.min.css">

  <!-- jquery-ui -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/libs/jquery-ui/jquery-ui.min.css">
  
  <!-- jquery TE -->
  <link rel="stylesheet" href="<?= base_url() ?>assets_tambahan/jQueryTE/jquery-te-1.4.0.css">
  
  <link href="<?= base_url() ?>assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

  <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css" rel="stylesheet" />

  <link href="<?= base_url() ?>assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

  <link href="<?= base_url() ?>assets_tambahan/orgchart/orgchart.css" rel="stylesheet" type="text/css" />

  <link rel="stylesheet" type="text/css" href="<?= base_url('assets_tambahan') ?>/easyui/themes/default/easyui.css">
  <link rel="stylesheet" type="text/css" href="<?= base_url('assets_tambahan') ?>/easyui/themes/icon.css">

  <link href="<?= base_url() ?>assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">
  <link href="<?= base_url() ?>assets/libs/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="<?= base_url() ?>assets/libs/@chenfengyuan/datepicker/datepicker.min.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prettify/r298/prettify.min.css">

  <link rel="stylesheet" href="<?=base_url()?>assets_tambahan/pdf-annotate/styles.css">
  <link rel="stylesheet" href="<?=base_url()?>assets_tambahan/pdf-annotate/pdfannotate.css">

  <!-- CSS -->


  <!-- JAVASCRIPT -->
  
  <script src="<?= base_url() ?>assets/libs/jquery/jquery-3.6.0.min.js"></script>
  <script src="<?= base_url() ?>assets/libs/jquery-ui/jquery-ui.min.js"></script>
  <script src="<?= base_url() ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?= base_url() ?>assets/libs/metismenu/metisMenu.min.js"></script>
  <script src="<?= base_url() ?>assets/libs/simplebar/simplebar.min.js"></script>
  <script src="<?= base_url() ?>assets/libs/node-waves/waves.min.js"></script>
  <!-- jquery TE -->
  <script src="<?= base_url() ?>assets_tambahan/jQueryTE/jquery-te-1.4.0.min.js"></script>
  <!-- jquery TE -->

  <!-- Required datatable js -->
  <script src="<?= base_url() ?>assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="<?= base_url() ?>assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="<?= base_url() ?>assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
  <script src="<?= base_url() ?>assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
  <script src="<?= base_url() ?>assets/libs/jszip/jszip.min.js"></script>
  <script src="<?= base_url() ?>assets/libs/pdfmake/pdfmake.min.js"></script>
  <script src="<?= base_url() ?>assets/libs/pdfmake/vfs_fonts.js"></script>
  <script src="<?= base_url() ?>assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
  <script src="<?= base_url() ?>assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
  <script src="<?= base_url() ?>assets/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>

  <!-- Responsive examples -->
  <script src="<?= base_url() ?>assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
  <script src="<?= base_url() ?>assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

  <!-- Selected Datatables -->
  <script src="<?= base_url() ?>assets/libs/datatables.net-select/js/dataTables.select.min.js"></script>
  <script src="<?= base_url() ?>assets/libs/datatables.net-select-bs4/js/select.bootstrap4.min.js"></script>

  <!-- Checkbox Datatables -->
  <script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>


  <!-- Datatable init js -->
  <script src="<?= base_url() ?>assets/js/pages/datatables.init.js"></script>

  <!-- Nanobar -->
  <script type="text/javascript" src="<?= base_url() ?>assets_tambahan/nanobar-master/nanobar.js"></script>
  <!-- Nanobar -->

  <script src="<?= base_url() ?>assets/libs/toastr/toastr.min.js"></script>

  <script src="<?= base_url() ?>assets/libs/tinymce/tinymce.min.js"></script>

  <script src="<?= base_url() ?>assets/libs/sweetalert2/sweetalert2.min.js"></script>


  <script src="<?= base_url() ?>assets/libs/select2/js/select2.min.js"></script>

  <script src="<?= base_url() ?>assets_tambahan/orgchart/orgchart.js"></script>
  <script type="text/javascript" src="<?= base_url('assets_tambahan') ?>/easyui/jquery.easyui.min.js"></script>
  <script type="text/javascript" src="<?= base_url('assets_tambahan') ?>/easyui/jquery.edatagrid.js"></script>

  <!-- baru -->
  <!-- date picker -->
  <script src="<?= base_url() ?>assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
  <script src="<?= base_url() ?>assets/libs/@chenfengyuan/datepicker/datepicker.min.js"></script>
  <!-- date picker -->

  <!-- Calendar -->
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js'></script>
  <!-- Calendar -->

  <!-- pdf editor -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.min.js"></script>
  <script>pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.worker.min.js';</script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/4.3.0/fabric.min.js"></script>
  <script src="<?=base_url()?>assets_tambahan/pdf-annotate/arrow.fabric.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.2.0/jspdf.umd.min.js"></script>
  <script src="https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/prettify/r298/prettify.min.js"></script>  
  <script src="<?=base_url()?>assets_tambahan/pdf-annotate/pdfannotate.js"></script>
  
  <!-- pdf editor -->
  <script type="text/javascript">
    function logout() {
      Swal.fire({
        title: "Anda Yakin Logout?",
        text: "Ketika anda logut maka akan keluar dari aplikasi DEC!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#34c38f",
        cancelButtonColor: "#f46a6a",
        confirmButtonText: "Iya"
      }).then(function(result) {
        if (result.value) {
          location.href = "<?= base_url() ?>/login/logout";
        }
      });
    }
  </script>


</head>

<body data-sidebar="dark">

  <!-- Begin page -->
  <div id="layout-wrapper">

    <header id="page-topbar">
      <div class="navbar-header">
        <div class="d-flex">
          <!-- LOGO -->
          <div class="navbar-brand-box">
            <a href="<?= base_url('home') ?>" class="logo logo-dark">
              <span class="logo-sm">
                <img src="<?= base_url() ?>assets/images/logo_dec.jpeg" alt="" height="22">
              </span>
              <span class="logo-lg">
                <img src="<?= base_url() ?>assets/images/logo_dec.jpeg" style="background-color:white;width: 100%;height: auto;" alt="">
              </span>
            </a>

            <a href="<?= base_url('home') ?>" class="logo logo-light">
              <span class="logo-sm">
                <img src="<?= base_url() ?>assets/images/logo-light.svg" alt="" height="22">
              </span>
              <span class="logo-lg">
                <img src="<?= base_url() ?>assets/images/logo_dec.jpeg" style="background-color:white;width: 100%;height: auto;" alt="">
              </span>
            </a>
          </div>

          <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
            <i class="fa fa-fw fa-bars"></i>
          </button>
        </div>

        <div class="d-flex">
          <div class="dropdown d-lg-inline-block ms-1">
            <button type="button" class="btn header-item noti-icon waves-effect">
              <?= $pegawai_nama . ' - ' . $pegawai_postitle ?>
            </button>
          </div>
          <div class="dropdown d-lg-inline-block ms-1">
            <button type="button" class="btn header-item noti-icon waves-effect" onclick="logout()">
              <i class="fa fa-key"></i>
            </button>
          </div>
        </div>
      </div>
    </header>