</div>
<footer class="footer">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6">
        <script>
          document.write(new Date().getFullYear())
        </script> © Petrokimia Gresik..
      </div>
      <div class="col-sm-6">
        <div class="text-sm-end d-none d-sm-block">
          Design & Develop for Petrokimia Gresik
        </div>
      </div>
    </div>
  </div>
</footer>

<!-- App js -->

<script src="<?= base_url() ?>assets/js/app.js"></script>
</body>

</html>
<script>
  $(function() {

    // setInterval(() => {
    //   getNotifRKAP();
    // }, 2000)
    // setInterval(() => {
    //   getNotifNonRKAP();
    // }, 2000)
  })
  /*notifikasi rkap*/
  function getNotifRKAP() {
    $.getJSON('<?= base_url('tampilan/tampilan/get_notif') ?>', {
      is_rkap: 'y'
    }, function(json, textStatus) {
      if (json.notif_reject == 0) {
        $('#notif_usulan_reject_rkap').hide()
      } else {
        $('#notif_usulan_reject_rkap').show();
      }
      if (json.notif_usulan == 0) {
        $('#notif_usulan_rkap').hide()
      } else {
        $('#notif_usulan_rkap').show();
      }
      if (json.notif_berjalan == 0) {
        $('#notif_berjalan_rkap').hide()
      } else {
        $('#notif_berjalan_rkap').show();
      }
      if (json.notif_ifa == 0) {
        $('#notif_ifa_rkap').hide()
      } else {
        $('#notif_ifa_rkap').show();
      }
      if (json.notif_ifc == 0) {
        $('#notif_ifc_rkap').hide()
      } else {
        $('#notif_ifc_rkap').show();
      }
      if (json.notif_selesai == 0) {
        $('#notif_selesai_rkap').hide()
      } else {
        $('#notif_selesai_rkap').show();
      }
      $('#notif_usulan_reject_rkap').html(json.notif_reject);
      $('#notif_usulan_rkap').html(parseInt(json.notif_reject) + parseInt(json.notif_usulan));
      $('#notif_berjalan_rkap').html(parseInt(json.notif_berjalan));
      $('#notif_ifa_rkap').html(parseInt(json.notif_ifa));
      $('#notif_ifc_rkap').html(parseInt(json.notif_ifc));
      $('#notif_selesai_rkap').html(parseInt(json.notif_selesai));
      /*optional stuff to do after success */
    });
  }
  /*notifikasi rkap*/

  /*notifikasi rkap*/
  function getNotifNonRKAP() {
    $.getJSON('<?= base_url('tampilan/tampilan/get_notif') ?>', {
      is_rkap: 'n'
    }, function(json, textStatus) {
      if (json.notif_reject == 0) {
        $('#notif_usulan_reject_non_rkap').hide()
      } else {
        $('#notif_usulan_reject_non_rkap').show();
      }
      if (json.notif_usulan == 0) {
        $('#notif_usulan_non_rkap').hide()
      } else {
        $('#notif_usulan_non_rkap').show();
      }
      if (json.notif_berjalan == 0) {
        $('#notif_berjalan_non_rkap').hide()
      } else {
        $('#notif_berjalan_non_rkap').show();
      }
      if (json.notif_ifa == 0) {
        $('#notif_ifa_non_rkap').hide()
      } else {
        $('#notif_ifa_non_rkap').show();
      }
      if (json.notif_ifc == 0) {
        $('#notif_ifc_non_rkap').hide()
      } else {
        $('#notif_ifc_non_rkap').show();
      }
      if (json.notif_selesai == 0) {
        $('#notif_selesai_non_rkap').hide()
      } else {
        $('#notif_selesai_non_rkap').show();
      }
      $('#notif_usulan_reject_non_rkap').html(json.notif_reject);
      $('#notif_usulan_non_rkap').html(parseInt(json.notif_reject) + parseInt(json.notif_usulan));
      $('#notif_berjalan_non_rkap').html(parseInt(json.notif_berjalan));
      $('#notif_ifa_non_rkap').html(parseInt(json.notif_ifa));
      $('#notif_ifc_non_rkap').html(parseInt(json.notif_ifc));
      $('#notif_selesai_non_rkap').html(parseInt(json.notif_selesai));
      /*optional stuff to do after success */
    });
  }
  /*notifikasi rkap*/
</script>
<script type="text/javascript">
  /*notif baru*/
  /*norif rkap*/
  /*notif usulan*/
  function notif_usulan_reject_rkap(view = '') {
    $.getJSON('<?= base_url('tampilan/Tampilan/notif_baru?status=-,0') ?>' + view, {
      is_rkap: 'y',
    }, function(data) {
      if (data.total > 0) {
        $('#notif_usulan_reject_rkap').val(data.total);
      } else if (data.total == 0) {
        $('#notif_usulan_reject_rkap').val(0);
      }
    })
  }
  // setInterval(() => {
  //   notif_usulan_reject_rkap();
  // }, 2000);
  /*notif usulan*/

  /*notif usulan*/
  function notif_usulan_rkap(view = '') {
    $.getJSON('<?= base_url('tampilan/Tampilan/notif_baru?status=1,2,3,4') ?>' + view, {
      is_rkap: 'y',
    }, function(data) {
      if ((parseInt(data.total) + parseInt($('#notif_usulan_reject_rkap').val())) > 0) {
        $('#notif_usulan_rkap').html((parseInt(data.total) + parseInt($('#notif_usulan_reject_rkap').val())));
      } else if (data.total == 0) {
        $('#notif_usulan_rkap').hide();
        $('#notif_usulan_rkap').html(0);
      }
    })
  }
  // setInterval(() => {
  //   notif_usulan_rkap();
  // }, 2000);
  /*notif usulan*/

  /*notif berjalan*/
  function notif_berjalan_rkap(view = '') {
    $.getJSON('<?= base_url('tampilan/Tampilan/notif_baru?status=5,6,7') ?>' + view, {
      is_rkap: 'y',
    }, function(data) {
      if (data.total > 0) {
        $('#notif_berjalan_rkap').html(data.total);
      } else if (data.total == 0) {
        $('#notif_berjalan_rkap').hide();
        $('#notif_berjalan_rkap').html(0);
      }
    })
  }
  // setInterval(() => {
  //   notif_berjalan_rkap();
  // }, 2000);
  /*notif berjalan*/

  /*notif ifa*/
  function notif_ifa_rkap(view = '') {
    $.getJSON('<?= base_url('tampilan/Tampilan/notif_baru?status=8,9,10') ?>' + view, {
      is_rkap: 'y',
    }, function(data) {
      if (data.total > 0) {
        $('#notif_ifa_rkap').html(data.total);
      } else if (data.total == 0) {
        $('#notif_ifa_rkap').hide();
        $('#notif_ifa_rkap').html(0);
      }
    })
  }
  // setInterval(() => {
  //   notif_ifa_rkap();
  // }, 2000);
  /*notif ifa*/

  /*notif ifc*/
  function notif_ifc_rkap(view = '') {
    $.getJSON('<?= base_url('tampilan/Tampilan/notif_baru?status=11,12,13') ?>' + view, {
      is_rkap: 'y',
    }, function(data) {
      if (data.total > 0) {
        $('#notif_ifc_rkap').html(data.total);
      } else if (data.total == 0) {
        $('#notif_ifc_rkap').hide();
        $('#notif_ifc_rkap').html(0);
      }
    })
  }
  // setInterval(() => {
  //   notif_ifc_rkap();
  // }, 2000);
  /*notif ifc*/

  /*notif selesai*/
  function notif_selesai_rkap(view = '') {
    $.getJSON('<?= base_url('tampilan/Tampilan/notif_baru?status=14,15') ?>' + view, {
      is_rkap: 'y',
    }, function(data) {
      if (data.total > 0) {
        $('#notif_selesai_rkap').html(data.total);
      } else if (data.total == 0) {
        $('#notif_selesai_rkap').hide();
        $('#notif_selesai_rkap').html(0);
      }
    })
  }
  // setInterval(() => {
  //   notif_selesai_rkap();
  // }, 2000);
  /*notif ifc*/

  function notif_rkap_total(view = '') {
    $.getJSON('<?= base_url('tampilan/Tampilan/notif_baru?status=-,0') ?>' + view, {
      is_rkap: 'y',
    }, function(data_reject) {
      $.getJSON('<?= base_url('tampilan/Tampilan/notif_baru') ?>' + view, {
        is_rkap: 'y',
      }, function(data) {
        if (parseInt(data.total) + parseInt(data_reject.total) > 0) {
          $('#notif_rkap_total').html(parseInt(data.total) + parseInt(data_reject.total));
        } else if (data.total == 0) {
          $('#notif_rkap_total').hide();
          $('#notif_rkap_total').html(0);
        }
      })
    })
  }
  // setInterval(() => {
  //   notif_rkap_total();
  // }, 2000);
  /*notif rkap total*/
  /*notif_rkap*/

  /*notif non rkap*/
  /*notif usulan*/
  function notif_usulan_reject_non_rkap(view = '') {
    $.getJSON('<?= base_url('tampilan/Tampilan/notif_baru?status=-,0') ?>' + view, {
      is_rkap: 'n',
    }, function(data) {
      if (data.total > 0) {
        $('#notif_usulan_reject_non_rkap').val(data.total);
      } else if (data.total == 0) {
        $('#notif_usulan_reject_non_rkap').val(0);
      }
    })
  }
  // setInterval(() => {
  //   notif_usulan_reject_non_rkap();
  // }, 2000);
  /*notif usulan*/

  /*notif usulan*/
  function notif_usulan_non_rkap(view = '') {
    $.getJSON('<?= base_url('tampilan/Tampilan/notif_baru?status=1,2,3,4') ?>' + view, {
      is_rkap: 'n',
    }, function(data) {
      var cek = (parseInt(data.total) + parseInt($('#notif_usulan_reject_non_rkap').val()));
      if (parseInt(data.total) + parseInt($('#notif_usulan_reject_non_rkap').val()) > 0) {
        $('#notif_usulan_non_rkap').html(parseInt(data.total) + parseInt($('#notif_usulan_reject_non_rkap').val()));
      } else if (data.total == 0) {
        $('#notif_usulan_non_rkap').hide();
        $('#notif_usulan_non_rkap').html(0);
      }
    })
  }
  // setInterval(() => {
  //   notif_usulan_non_rkap();
  // }, 2000);
  /*notif usulan*/

  /*notif berjalan*/
  function notif_berjalan_non_rkap(view = '') {
    $.getJSON('<?= base_url('tampilan/Tampilan/notif_baru?status=5,6,7') ?>' + view, {
      is_rkap: 'n',
    }, function(data) {
      if (data.total > 0) {
        $('#notif_berjalan_non_rkap').html(data.total);
      } else if (data.total == 0) {
        $('#notif_berjalan_non_rkap').hide();
        $('#notif_berjalan_non_rkap').html(0);
      }
    })
  }
  // setInterval(() => {
  //   notif_berjalan_non_rkap();
  // }, 2000);
  /*notif berjalan*/

  /*notif ifa*/
  function notif_ifa_non_rkap(view = '') {
    $.getJSON('<?= base_url('tampilan/Tampilan/notif_baru?status=8,9,10') ?>' + view, {
      is_rkap: 'n',
    }, function(data) {
      if (data.total > 0) {
        $('#notif_ifa_non_rkap').html(data.total);
      } else if (data.total == 0) {
        $('#notif_ifa_non_rkap').hide();
        $('#notif_ifa_non_rkap').html(0);
      }
    })
  }
  // setInterval(() => {
  //   notif_ifa_non_rkap();
  // }, 2000);
  /*notif ifa*/

  /*notif ifc*/
  function notif_ifc_non_rkap(view = '') {
    $.getJSON('<?= base_url('tampilan/Tampilan/notif_baru?status=11,12,13') ?>' + view, {
      is_rkap: 'n',
    }, function(data) {
      if (data.total > 0) {
        $('#notif_ifc_non_rkap').html(data.total);
      } else if (data.total == 0) {
        $('#notif_ifc_non_rkap').hide();
        $('#notif_ifc_non_rkap').html(0);
      }
    })
  }
  // setInterval(() => {
  //   notif_ifc_non_rkap();
  // }, 2000);
  /*notif ifc*/

  /*notif selesai*/
  function notif_selesai_non_rkap(view = '') {
    $.getJSON('<?= base_url('tampilan/Tampilan/notif_baru?status=14,15') ?>' + view, {
      is_rkap: 'n',
    }, function(data) {
      if (data.total > 0) {
        $('#notif_selesai_non_rkap').html(data.total);
      } else if (data.total == 0) {
        $('#notif_selesai_non_rkap').hide();
        $('#notif_selesai_non_rkap').html(0);
      }
    })
  }
  // setInterval(() => {
  //   notif_selesai_non_rkap();
  // }, 2000);
  /*notif ifc*/


  function notif_non_rkap_total(view = '') {
    $.getJSON('<?= base_url('tampilan/Tampilan/notif_baru?status=-,0') ?>' + view, {
      is_rkap: 'n',
    }, function(data_reject) {
      $.getJSON('<?= base_url('tampilan/Tampilan/notif_baru') ?>' + view, {
        is_rkap: 'n',

      }, function(data) {
        if (parseInt(data.total) + parseInt(data_reject.total) > 0) {
          $('#notif_non_rkap_total').show();
          $('#notif_non_rkap_total').html(parseInt(data.total) + parseInt(data_reject.total));
        } else if (data.total == 0) {
          $('#notif_non_rkap_total').hide();
          $('#notif_non_rkap_total').html(0);
        }
      })
    })
  }
  // setInterval(() => {
  //   notif_non_rkap_total();
  // }, 2000);
  /*notif non rkap total*/
  /*notif non rkap*/

  /*notid baru*/

  /*notif total*/
  function notif_pekerjaan_total(view = '') {
    var rkap = ($('#notif_rkap_total').html() > 0) ? $('#notif_rkap_total').html() : 0;
    var non_rkap = ($('#notif_non_rkap_total').html() > 0) ? $('#notif_non_rkap_total').html() : 0;
    var total = (rkap * 1) + (non_rkap * 1);
    if (total > 0) {
      $('#notif_pekerjaan_total').show();
      $('#notif_pekerjaan_total').html(total);
    } else if (total == 0) {
      $('#notif_pekerjaan_total').hide();
    }
  }
  // setInterval(() => {
  //   notif_pekerjaan_total();
  // }, 2000);
  /*notif total*/

  function reminder() {
    $.getJSON('<?= base_url('project/pekerjaan_usulan/reminder') ?>', function(data) {
      console.log(data);
    });
  }

  // setTimeout(() => {
  //   reminder();
  // }, 2000);

  function autoUpdateIFA() {
    $.getJSON('<?= base_url('project/pekerjaan_usulan/autoUpdateIFA') ?>', function(data) {
      console.log(data);
    });
  }

  // setTimeout(() => {
  //   autoUpdateIFA();
  // }, 2000);

  function notif_task(view = '') {
    $.getJSON('<?= base_url('task/task/getTaskTotal') ?>', function(data) {
      if (parseInt(data) > 0) {
        $('#notif_task_total').html(parseInt(data));
      } else if (data == 0) {
        $('#notif_task_total').hide();
        $('#notif_task_total').html(0);
      }
    })
  }

  // setInterval(() => {
  //   notif_task();
  // }, 2000);

  function numberOnly(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))

      return false;
    return true;
  }
</script>