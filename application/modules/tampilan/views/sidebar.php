<?php
$sess = $this->session->userdata();
$admin = $this->db->query("SELECT * FROM global.global_admin WHERE admin_nik = '" . $sess['pegawai_nik'] . "'")->row_array();
?>

<div class="vertical-menu">
  <div data-simplebar class="h-100">

    <!--- Sidemenu -->
    <div id="sidebar-menu">
      <!-- Left Menu Start -->
      <ul class="metismenu list-unstyled" id="side-menu">
        <li class="menu-title" key="t-menu">Menu</li>
        <?php
        $user = $this->session->userdata();
        if (!empty($admin) && $user['pegawai_nik'] == $admin['admin_nik']) {
          $main_menu = $this->db->query("SELECT * FROM global.global_menu a WHERE menu_parent = '0' ORDER BY a.menu_urut ASC");
        } else {
          $main_menu = $this->db->query("SELECT * FROM global.global_menu a LEFT JOIN global.global_menu_role b ON a.menu_id = b.id_menu WHERE b.id_role = '" . $pegawai_poscode . "' AND menu_parent = '0' AND menu_id != '17' ORDER BY a.menu_urut ASC");
        }
        ?>
        <?php foreach ($main_menu->result() as $value) : ?>
          <?php if (!empty($admin) && $user['pegawai_nik'] == $admin['admin_nik']) : ?>
            <?php $sub_menu = $this->db->query("SELECT * FROM global.global_menu a WHERE a.menu_parent = '" . $value->menu_id . "' ORDER BY a.menu_urut ASC"); ?>
          <?php else : ?>
            <?php $sub_menu = $this->db->query("SELECT * FROM global.global_menu a LEFT JOIN global.global_menu_role b ON a.menu_id = b.id_menu WHERE b.id_role = '" . $pegawai_poscode . "' AND a.menu_parent = '" . $value->menu_id . "' ORDER BY a.menu_urut ASC"); ?>
          <?php endif ?>
          <?php if ($sub_menu->num_rows() > 0) : ?>
            <li>
              <a href="javascript: void(0);" class="has-arrow waves-effect"><i class="<?= $value->menu_icon ?>"></i><span key="t-ecommerce"><?= $value->menu_nama ?></span><span class="badge bg-danger float-end" id="<?= $value->menu_cust_id ?>"></span></a>
              <ul class="sub-menu" aria-expanded="false">
                <?php foreach ($sub_menu->result() as $val) : ?>
                  <li><a href="<?= base_url() . $val->menu_link ?>" key="t-products"><?= $val->menu_nama ?><span class="badge bg-danger float-end" id="<?= $val->menu_cust_id ?>"></span></a></li>
                <?php endforeach ?>
              </ul>
            </li>
          <?php else : ?>
            <li>
              <a href="<?= base_url() . $value->menu_link ?>" class="waves-effect"><i class="<?= $value->menu_icon ?>"></i><span key="t-chat"><?= $value->menu_nama ?></span><span class="badge bg-danger float-end" id="$value->menu_cust_id"></span></a>
            </li>
          <?php endif ?>
        <?php endforeach ?>
        <li>
          <a href="<?= base_url() . 'task/task' ?>" class="waves-effect"><i class="fas fa-tasks"></i><span key="t-chat">My Task</span><span class="badge bg-danger float-end" id="notif_task_total"></span></a>
        </li>
        <li>
          <a href="<?= base_url() . 'calendar/calendar' ?>" class="waves-effect"><i class="fas fa-calendar"></i><span key="t-chat">Calendar</span><span class="badge bg-danger float-end" id="$value->menu_cust_id"></span></a>
        </li>
        <?php if ($this->session->userdata('pegawai_unit_id') == 'E53000'): ?>
          <li>
            <a href="/manajemen_project" class="waves-effect" target="_blank"><i class="fas fa-line-chart"></i><span key="t-chat">GanttChart</span><span class="badge bg-danger float-end" id="$value->menu_cust_id" ></span></a>
          </li>
        <?php endif ?>
      </ul>
    </div>
    <!-- Sidebar -->
  </div>
</div>
<div class="main-content" id="result">