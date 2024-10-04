<style>
  .tree ul {
    padding-top: 20px; position: relative;
    
    transition: all 0.5s;
    -webkit-transition: all 0.5s;
    -moz-transition: all 0.5s;
  }

  .tree li {
    float: left; text-align: center;
    list-style-type: none;
    position: relative;
    padding: 20px 5px 0 5px;
    
    transition: all 0.5s;
    -webkit-transition: all 0.5s;
    -moz-transition: all 0.5s;
  }

  /*We will use ::before and ::after to draw the connectors*/

  .tree li::before, .tree li::after{
    content: '';
    position: absolute; top: 0; right: 50%;
    border-top: 1px solid #ccc;
    width: 50%; height: 20px;
  }
  .tree li::after{
    right: auto; left: 50%;
    border-left: 1px solid #ccc;
  }

  /*We need to remove left-right connectors from elements without 
  any siblings*/
  .tree li:only-child::after, .tree li:only-child::before {
    display: none;
  }

  /*Remove space from the top of single children*/
  .tree li:only-child{ padding-top: 0;}

  /*Remove left connector from first child and 
  right connector from last child*/
  .tree li:first-child::before, .tree li:last-child::after{
    border: 0 none;
  }
  /*Adding back the vertical connector to the last nodes*/
  .tree li:last-child::before{
    border-right: 1px solid #ccc;
    border-radius: 0 5px 0 0;
    -webkit-border-radius: 0 5px 0 0;
    -moz-border-radius: 0 5px 0 0;
  }
  .tree li:first-child::after{
    border-radius: 5px 0 0 0;
    -webkit-border-radius: 5px 0 0 0;
    -moz-border-radius: 5px 0 0 0;
  }

  /*Time to add downward connectors from parents*/
  .tree ul ul::before{
    content: '';
    position: absolute; top: 0; left: 50%;
    border-left: 1px solid #ccc;
    width: 0; height: 20px;
  }

  .tree li a{
    border: 1px solid #ccc;
    padding: 5px 10px;
    text-decoration: none;
    color: #666;
    font-family: arial, verdana, tahoma;
    font-size: 11px;
    display: inline-block;
    
    border-radius: 5px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    
    transition: all 0.5s;
    -webkit-transition: all 0.5s;
    -moz-transition: all 0.5s;
  }

  /*Time for some hover effects*/
  /*We will apply the hover effect the the lineage of the element also*/
  .tree li a:hover, .tree li a:hover+ul li a {
    background: #c8e4f8; color: #000; border: 1px solid #94a0b4;
  }
  /*Connector styles on hover*/
  .tree li a:hover+ul li::after, 
  .tree li a:hover+ul li::before, 
  .tree li a:hover+ul::before, 
  .tree li a:hover+ul ul::before{
    border-color:  #94a0b4;
  }
</style>

<div class="container-fluid">
  <br><br><br>

  <div class="row" id="div_hirarki">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <div class="tree">
            <?php
              $sql_jumlah = $this->db->query("SELECT pekerjaan_disposisi_status FROM dec.dec_pekerjaan_disposisi WHERE is_aktif = 'y' AND id_pekerjaan = '1641867172656' AND pekerjaan_disposisi_status <= '6' GROUP BY pekerjaan_disposisi_status ORDER BY pekerjaan_disposisi_status ASC");
              $dataJumlah = $sql_jumlah->result_array();

              $sql_pekerjaan = $this->db->query("SELECT * FROM dec.dec_pekerjaan a LEFT JOIN global.global_pegawai b ON a.pic = b.pegawai_nik WHERE a.pekerjaan_id = '1641867172656'");
              $dataPekerjaan = $sql_pekerjaan->row_array();
            ?>
            <ul>
              <?php if(isset($dataJumlah)) : ?>
                <li style="width: 100%">
                  <a href="javascript:0;"><?= $dataPekerjaan['pegawai_nama'] ?></a>
                  <?php if (isset($dataJumlah[0]['pekerjaan_disposisi_status'])): ?>
                    <?php
                      $sql_1 = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik WHERE is_aktif = 'y' AND id_pekerjaan = '1641867172656' AND pekerjaan_disposisi_status = '".$dataJumlah[0]['pekerjaan_disposisi_status']."'");
                      $data1 = $sql_1->result_array();

                      $total1 = (COUNT($data1) != NULL) ? 100/COUNT($data1) : 0;
                    ?>
                    <?php if (isset($data1)): ?>
                      <ul>
                        <?php foreach ($data1 as $value1): ?>
                          <li style="width: <?= $total1; ?>%">
                            <a href="javascript:0;"><?= $value1['pegawai_nama'] ?></a>
                            <?php if (isset($dataJumlah[1]['pekerjaan_disposisi_status'])): ?>
                              <?php
                                $sql_2 = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik WHERE is_aktif = 'y' AND id_pekerjaan = '1641867172656' AND pekerjaan_disposisi_status = '".$dataJumlah[1]['pekerjaan_disposisi_status']."'");
                                $data2 = $sql_2->result_array();

                                $total2 = (COUNT($data2) != NULL) ? 100/COUNT($data2) : 0;
                              ?>
                              <?php if (isset($data2)): ?>
                                <ul>
                                  <?php foreach ($data2 as $value2): ?>
                                    <li style="width: <?= $total2; ?>%">
                                      <a href="javascript:0;"><?= $value2['pegawai_nama'] ?></a>
                                      <?php if (isset($dataJumlah[2]['pekerjaan_disposisi_status'])): ?>
                                        <?php
                                          $sql_3 = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik WHERE is_aktif = 'y' AND id_pekerjaan = '1641867172656' AND pekerjaan_disposisi_status = '".$dataJumlah[2]['pekerjaan_disposisi_status']."'");
                                          $data3 = $sql_3->result_array();

                                          $total3 = (COUNT($data3) != NULL) ? 100/COUNT($data3) : 0;
                                        ?>
                                        <?php if (isset($data3)): ?>
                                          <ul>
                                            <?php foreach ($data3 as $value3): ?>
                                              <li style="width: <?= $total3; ?>%">
                                                <a href="javascript:0;"><?= $value3['pegawai_nama'] ?></a>
                                                <?php if (isset($dataJumlah[3]['pekerjaan_disposisi_status'])): ?>
                                                  <?php
                                                    $sql_4 = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik WHERE is_aktif = 'y' AND id_pekerjaan = '1641867172656' AND pekerjaan_disposisi_status = '".$dataJumlah[3]['pekerjaan_disposisi_status']."'");
                                                    $data4 = $sql_4->result_array();

                                                    $total4 = (COUNT($data4) != NULL) ? 100/COUNT($data4) : 0;
                                                  ?>
                                                  <?php if (isset($data4)): ?>
                                                    <ul>
                                                      <?php foreach ($data4 as $value4): ?>
                                                        <li style="width: <?= $total4; ?>%">
                                                          <a href="javascript:0;"><?= $value4['pegawai_nama'] ?></a>
                                                          <?php if (isset($dataJumlah[4]['pekerjaan_disposisi_status'])): ?>
                                                            <?php
                                                              $sql_5 = $this->db->query("SELECT * FROM dec.dec_pekerjaan_disposisi a LEFT JOIN global.global_pegawai b ON a.id_user = b.pegawai_nik WHERE is_aktif = 'y' AND id_pekerjaan = '1641867172656' AND pekerjaan_disposisi_status = '".$dataJumlah[4]['pekerjaan_disposisi_status']."'");
                                                              $data5 = $sql_5->result_array();

                                                              $total5 = (COUNT($data5) != NULL) ? 100/COUNT($data5) : 0;
                                                            ?>
                                                            <?php if (isset($data5)): ?>
                                                              <ul>
                                                                <?php foreach ($data5 as $value5): ?>
                                                                  <li style="width: <?= $total5; ?>%">
                                                                    <a href="javascript:0;"><?= $value5['pegawai_nama'] ?></a>
                                                                  </li>
                                                                <?php endforeach ?>
                                                              </ul>
                                                            <?php endif ?>
                                                          <?php endif ?>
                                                        </li>
                                                      <?php endforeach ?>
                                                    </ul>
                                                  <?php endif ?>
                                                <?php endif ?>
                                              </li>
                                            <?php endforeach ?>
                                          </ul>
                                        <?php endif ?>
                                      <?php endif ?>
                                    </li>
                                  <?php endforeach ?>
                                </ul>
                              <?php endif ?>
                            <?php endif ?>
                          </li>
                        <?php endforeach ?>
                      </ul>
                    <?php endif ?>
                  <?php endif ?>
                </li>
              <?php endif; ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


