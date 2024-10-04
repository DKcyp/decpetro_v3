<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
  <meta content="Themesbrand" name="author" />
  <!-- App favicon -->
  <link rel="shortcut icon" href="assets/images//logo_dec.jpeg">
  <!-- owl.carousel css -->
  <link rel="stylesheet" href="<?= base_url('/') ?>assets/libs/owl.carousel/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="<?= base_url('/') ?>assets/libs/owl.carousel/assets/owl.theme.default.min.css">
  <!-- Bootstrap Css -->
  <link href="<?= base_url('/') ?>assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
  <!-- Icons Css -->
  <link href="<?= base_url('/') ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
  <!-- App Css-->
  <link href="<?= base_url('/') ?>assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
</head>

<body class="auth-body-bg">
  <div>
    <div class="container-fluid p-0">
      <div class="row g-0">
        <div class="col-xl-9" style="background-image: url(<?= base_url('assets/') ?>/images/Background-Login-Website-JDIH5.png);background-repeat: no-repeat;background-size: 100%">
          <div class="auth-full-bg pt-lg-5 p-4">
            <div class="w-100">
              <div></div>
              <div class="d-flex h-100 flex-column">
                <div class="p-4 mt-auto">
                  <div class="row justify-content-center">
                    <div class="col-lg-7">
                      <div class="text-center">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- end col -->
        <div class="col-xl-3">
          <div class="auth-full-page-content p-md-5 p-4">
            <div class="w-100">
              <div class="d-flex flex-column h-100">
                <div class="mb-4 mb-md-5">
                  <span class="logo-lg">
                    <img src="<?= base_url() ?>assets/images/logo-baru.png" style="background-color:white;width: 100%;height: auto;" alt="">
                  </span>
                  <p class="login-box-msg" style="color: #f00;">
                    <?php if ($this->session->flashdata('pesan')) { ?>
                      <div class="alert alert-danger"><?= $this->session->flashdata('pesan') ?></div>
                    <?php } ?>
                  </p>
                </div>
                <div class="my-auto">
                  <div class="mt-4">
                    <?php
                      if (isset($_GET['pekerjaan_id'])) {
                        $url = base_url() . 'login/auth?aksi=' . $this->input->get('aksi') . '&pekerjaan_id=' . $this->input->get('pekerjaan_id') . '&status=' . $this->input->get('status') . '&rkap=' . $this->input->get('rkap') . '&id_user=' . $this->input->get('id_user') . '&red=' . $this->input->get('red');
                      } else {
                        $url = base_url('login/auth?red=' . $this->input->get('red'));
                      }
                    ?>
                    <form action="<?php echo $url ?>" method="post">
                      <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" placeholder="Enter username" name="username">
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group auth-pass-inputgroup">
                          <input type="password" class="form-control" placeholder="Enter password" aria-label="Password" aria-describedby="password-addon" name="password">
                          <button class="btn btn-light " type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                        </div>
                        <div class="float-end">
                          <a href="auth-recoverpw-2.html" class="text-muted">Forgot
                            password?</a>
                        </div>
                      </div>
                      &nbsp;
                      <div class="mt-3 d-grid">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- end col -->
      </div>
      <!-- end row -->
    </div>
    <!-- end container-fluid -->
  </div>
  <!-- JAVASCRIPT -->
  <script src="<?= base_url('/') ?>assets/libs/jquery/jquery.min.js"></script>
  <script src="<?= base_url('/') ?>assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?= base_url('/') ?>assets/libs/metismenu/metisMenu.min.js"></script>
  <script src="<?= base_url('/') ?>assets/libs/simplebar/simplebar.min.js"></script>
  <script src="<?= base_url('/') ?>assets/libs/node-waves/waves.min.js"></script>
  <!-- owl.carousel js -->
  <script src="<?= base_url('/') ?>assets/libs/owl.carousel/owl.carousel.min.js"></script>
  <!-- auth-2-carousel init -->
  <script src="<?= base_url('/') ?>assets/js/pages/auth-2-carousel.init.js"></script>
  <!-- App js -->
  <script src="<?= base_url('/') ?>assets/js/app.js"></script>
</body>

</html>