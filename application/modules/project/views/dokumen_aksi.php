<div class="page-content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="toolbar">
              <div class="tool">
                <label>Brush size</label>
                <input type="number" class="form-control text-right" value="1" id="brush-size" max="50">
              </div>
              <div class="tool">
                <label for="">Font size</label>
                <select id="font-size" class="form-control">
                  <option value="10">10</option>
                  <option value="12">12</option>
                  <option value="16" selected>16</option>
                  <option value="18">18</option>
                  <option value="24">24</option>
                  <option value="32">32</option>
                  <option value="48">48</option>
                  <option value="64">64</option>
                  <option value="72">72</option>
                  <option value="108">108</option>
                </select>
              </div>
              <div class="tool">
                <button class="color-tool active" style="background-color: #212121;"></button>
                <button class="color-tool" style="background-color: red;"></button>
                <button class="color-tool" style="background-color: blue;"></button>
                <button class="color-tool" style="background-color: green;"></button>
                <button class="color-tool" style="background-color: yellow;"></button>
              </div>
              <div class="tool">
                <button type="button" class="tool-button active"><i class="fa fa-hand-paper-o" title="Free Hand" onclick="enableSelector(event)"></i></button>
              </div>
              <div class="tool">
                <button type="button" class="tool-button"><i class="fa fa-pencil" title="Pencil" onclick="enablePencil(event)"></i></button>
              </div>
              <div class="tool">
                <button type="button" class="tool-button"><i class="fa fa-font" title="Add Text" onclick="enableAddText(event)"></i></button>
              </div>
              <div class="tool">
                <button type="button" class="tool-button"><i class="fa fa-long-arrow-right" title="Add Arrow" onclick="enableAddArrow(event)"></i></button>
              </div>
              <div class="tool">
                <button type="button" class="tool-button"><i class="fa fa-square-o" title="Add rectangle" onclick="enableRectangle(event)"></i></button>
              </div>
              <div class="tool">
                <button type="button" class="tool-button"><i class="fa fa-picture-o" title="Add an Image" onclick="addImage(event)"></i></button>
              </div>
              <div class="tool">
                <button type="button" class="btn btn-danger btn-sm" onclick="deleteSelectedObject(event)"><i class="fa fa-trash"></i></button>
              </div>
              <div class="tool">
                <button type="button" class="btn btn-danger btn-sm" onclick="clearPage()">Clear Page</button>
              </div>
              <div class="tool" style="display: none;">
                <button type="button" class="btn btn-info btn-sm" onclick="showPdfData()">{}</button>
              </div>
              <div class="tool">
                <button type="button" class="btn btn-light btn-sm" onclick="savePDFToServer('<?= $dokumen['pekerjaan_dokumen_file'] ?>','<?= $dokumen['pekerjaan_dokumen_id'] ?>')">Save</button>
              </div>
              <div class="tool">
                <button type="button" class="btn btn-light btn-sm" onclick="downloadPDF()">Download</button>
              </div>
              <div class="tool">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="loadingsimpanPDF" style="display: none">
              </div>
            </div>
            <div class="row">
              <div class="form-group row col-md-12" style="background-color: rgb(82, 86, 89);">
                <div id="aksi_dokumen" class="pdf-container"></div>
              </div>
            </div>
            <form id="form_dokumen">
              <div class="card-body row">
                <input type="text" style="display: none;" name="is_change" id="is_change" value="n">
                <input type="text" style="display: none;" name="aksi" id="aksi" value="<?php echo $this->input->get('aksi') ?>">
                <input type="text" style="display: none;" name="pekerjaan_dokumen_status_ifa" id="pekerjaan_dokumen_status_ifa">
                <input type="text" style="display: none;" name="pekerjaan_dokumen_status" id="pekerjaan_dokumen_status" value="<?= $dokumen['pekerjaan_dokumen_status'] ?>">
                <input type="text" style="display: none;" name="pekerjaan_dokumen_id_temp" id="pekerjaan_dokumen_id_temp" value="<?= $dokumen['pekerjaan_dokumen_id'] ?>">
                <div class="form-group row col-md-12">
                  <label class="col-md-4">Nama File</label>
                  <input type="text" name="pekerjaan_dokumen_nama" id="pekerjaan_dokumen_nama" class="form-control" readonly value="<?= $dokumen['pekerjaan_dokumen_nama'] ?>">
                </div>
                <?php if (
                  $this->input->get('aksi') == 'usulan' || $this->input->get('aksi') == 'ifa' || $this->input->get('aksi') == 'ifc' || $this->input->get('aksi') == 'cc'
                ) : ?>
                  <div class="form-group row col-md-12">
                    <label class="col-md-4">Status</label>
                    <select name="pekerjaan_dokumen_status_aksi" id="pekerjaan_dokumen_status_aksi" class="form-control select2" style="width: 100%" onchange="cekStatus(this.value);">
                      <option value="y">APPROVE</option>
                      <option value="n">REVISI</option>
                    </select>
                  </div>
                <?php else : ?>
                  <div class="form-group row col-md-12">
                    <label class="col-md-4">Tanggal</label>
                    <input type="date" name="pekerjaan_dokumen_waktu" id="pekerjaan_dokumen_waktu" class="form-control" value="<?= date("Y-m-d", strtotime($dokumen['pekerjaan_dokumen_waktu'])) ?>">
                  </div>
                  <div class="form-group row col-md-12">
                    <label class="col-md-4">Status</label>
                    <select name="pekerjaan_dokumen_status_aksi" id="pekerjaan_dokumen_status_aksi" class="form-control select2" style="width: 100%" onchange="cekStatus(this.value);">
                      <option value="y">APPROVED</option>
                      <option value="yc">APPROVED WITH MINOR COMMENTS</option>
                      <option value="nc">TO BE REVISED AS COMMENTS AND RESUBMIT</option>
                      <option value="n">REJECTED</option>
                    </select>
                  </div>
                <?php endif; ?>
                <div class="form-group row col-md-12">
                  <label class="col-md-4">File</label>
                  <input type="file" name="pekerjaan_dokumen_file" id="pekerjaan_dokumen_file" class="form-control">
                </div>
                <div class="form-group row col-md-12" id="div_keterangan" style="display:none">
                  <label class="col-md-4">Keterangan</label>
                  <input type="text" name="pekerjaan_dokumen_keterangan" id="pekerjaan_dokumen_keterangan" class="form-control">
                </div>
              </div>

              <div class="card-footer">
                <button type="button" id="close_aksi" class="btn btn-default" data-dismiss="modal" onclick="fun_close_aksi()">Close</button>
                <input type="submit" class="btn btn-success pull-right" id="simpan_aksi_dokumen" value="Simpan">
                <button class="btn btn-primary" type="button" id="loading_form_aksi" disabled style="display: none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  /*pdf editor options*/
  var pdf = new PDFAnnotate("aksi_dokumen", "<?= base_url('document/' . $dokumen['pekerjaan_dokumen_file']) ?>", {
    scale: 1.25,
    pageImageCompression: "LOW",
  });


  function changeActiveTool(event) {
    var element = $(event.target).hasClass("tool-button") ?
      $(event.target) :
      $(event.target).parents(".tool-button").first();
    $(".tool-button.active").removeClass("active");
    $(element).addClass("active");
    $("#simpan_aksi_dokumen").hide();
  }

  function enableSelector(event) {
    event.preventDefault();
    changeActiveTool(event);
    pdf.enableSelector();
  }

  function enablePencil(event) {
    event.preventDefault();
    changeActiveTool(event);
    pdf.enablePencil();
  }

  function enableAddText(event) {
    event.preventDefault();
    changeActiveTool(event);
    pdf.enableAddText();
  }

  function enableAddArrow(event) {
    event.preventDefault();
    changeActiveTool(event);
    pdf.enableAddArrow();
  }

  function addImage(event) {
    event.preventDefault();
    pdf.addImageToCanvas()
  }

  function enableRectangle(event) {
    event.preventDefault();
    changeActiveTool(event);
    pdf.setColor('rgba(255, 0, 0, 0.3)');
    pdf.setBorderColor('blue');
    pdf.enableRectangle();
  }

  function deleteSelectedObject(event) {
    event.preventDefault();
    pdf.deleteSelectedObject();
  }

  function savePDFToServer(filename, dokumen_id) {
    $('#is_change').val('y');
    if ('<?= $this->input->get('transmital') == 'y' ?>')
      pdf.saveToServer('<?= base_url('project/transmital/uploadDokumen') ?>', filename, dokumen_id);
    else
      pdf.saveToServer('<?= base_url('project/pekerjaan_usulan/uploadDokumen') ?>', filename, dokumen_id);

    // $("#simpan_aksi_dokumen").removeClass("d-none");
  }

  function downloadPDF() {
    pdf.savePdf('<?= $dokumen['pekerjaan_dokumen_nama'] . '-edit' ?>')
  }

  function clearPage() {
    pdf.clearActivePage();
  }

  function showPdfData() {
    var string = pdf.serializePdf();
    $('#dataModal .modal-body pre').first().text(string);
    PR.prettyPrint();
    $('#dataModal').modal('show');
  }

  $(function() {
    $('.color-tool').click(function() {
      $('.color-tool.active').removeClass('active');
      $(this).addClass('active');
      color = $(this).get(0).style.backgroundColor;
      pdf.setColor(color);
    });

    $('#brush-size').change(function() {
      var width = $(this).val();
      pdf.setBrushSize(width);
    });

    $('#font-size').change(function() {
      var font_size = $(this).val();
      pdf.setFontSize(font_size);
    });
  });
  /*pdf editor options*/

  function cekStatus(data) {
    if (data == 'n') {
      $("#div_keterangan").show();
      // $('#is_change').val('y');
    } else {
      $('#div_keterangan').hide();
      // $('#is_change').val('');
    }
  }

  /*form submit*/
  $('#form_dokumen').on('submit', function(e) {
    var url = '';
    switch ('<?= $this->input->get('aksi') ?>') {
      case 'usulan':
        url = '<?= base_url('project/pekerjaan_usulan/simpanAksi') ?>';
        break;
      case 'cc':
        url = '<?= base_url('project/pekerjaan_usulan/simpanAksiCC') ?>';
        break;
      case 'ifa':
        url = '<?= base_url('project/pekerjaan_usulan/simpanAksiIFA') ?>'
        break;
      case 'ifc':
        url = '<?= base_url('project/pekerjaan_usulan/simpanAksiIFC') ?>'
        break;
      case 'waspro_avp':
        url = '<?= base_url('project/transmital/simpanAksiDokumenKontraktor?opsi=' . $this->input->get('aksi')) ?>'
        break;
      case 'waspro_cangun':
        url = '<?= base_url('project/transmital/simpanAksiDokumenKontraktor?opsi=' . $this->input->get('aksi')) ?>'
        break;
      case 'waspro_cangun_avp':
        url = '<?= base_url('project/transmital/simpanAksiDokumenKontraktor?opsi=' . $this->input->get('aksi')) ?>'
        break;
      case 'waspro_cangun_vp':
        url = '<?= base_url('project/transmital/simpanAksiDokumenKontraktor?opsi=' . $this->input->get('aksi')) ?>'
        break;
      default:
    }

    var aksi_file = $('#pekerjaan_dokumen_file').prop('files')[0];
    var data = new FormData();
    data.append('pekerjaan_dokumen_file', aksi_file);
    data.append('pekerjaan_status', $('#pekerjaan_dokumen_status').val());
    data.append('pekerjaan_dokumen_id', $('#pekerjaan_dokumen_id_temp').val());
    data.append('pekerjaan_dokumen_waktu', $('#pekerjaan_dokumen_waktu').val());
    data.append('pekerjaan_dokumen_status', $('#pekerjaan_dokumen_status_aksi').val());
    data.append('pekerjaan_dokumen_keterangan', $('#pekerjaan_dokumen_keterangan').val());
    data.append('pekerjaan_dokumen_status_nomor', $('#pekerjaan_dokumen_status').val());
    data.append('is_change', $('#is_change').val());
    e.preventDefault();
    $.ajax({
      url: url,
      data: data,
      type: 'POST',
      dataType: 'html',
      processData: false,
      contentType: false,
      beforeSend: function() {
        $('#loading_form_aksi').css('display', 'block');
        $('#simpan_aksi_dokumen').css('display', 'none');
      },
      complete: function() {
        $('#loading_form_aksi').hide();
        $('#simpan_aksi_dokumen').show();
        $("#simpan_aksi_dokumen").removeClass("d-none");
      },
      success: function(isi) {
        history.back(-1);
      }
    });
  })
  /*form submit*/
</script>
