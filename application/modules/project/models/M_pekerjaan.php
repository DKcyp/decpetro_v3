<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_pekerjaan extends CI_Model
{
  /* GET */
  /* Get Pekerjaan */
  public function getPekerjaan($data = null)
  {
    $this->db->select("a.*, b.*, c.*, d.*, to_char(pekerjaan_waktu_akhir, 'DD-MM-YYYY') as tanggal_akhir, to_char(pekerjaan_waktu, 'DD-MM-YYYY') as tanggal_awal, to_char(pekerjaan_waktu, 'yyyy-MM-dd') AS pekerjaan_waktunya, to_char(pekerjaan_waktu_akhir,'yyyy-MM-dd') AS pekerjaan_akhirnya, to_char(pekerjaan_waktu_selesai,'DD-MM-YYYY') as tanggal_selesai, to_char(pekerjaan_waktu_selesai,'yyyy-MM-dd') as tanggal_selesainya, pekerjaan_referensi_unit_kerja,(SELECT SUM(CAST(pekerjaan_nilai_hps_jumlah AS DECIMAL(10, 0))) FROM dec.dec_pekerjaan_nilai_hps WHERE id_pekerjaan = a.pekerjaan_id) AS pekerjaan_nilai_hps_jumlah");
    $this->db->distinct();
    $this->db->from('dec.dec_pekerjaan a');
    $this->db->join('global.global_auth_user b', 'b.usr_id = a.pic', 'left');
    $this->db->join('global.global_pegawai c', 'c.pegawai_nik = b.usr_id', 'left');
    $this->db->join('global.global_klasifikasi_pekerjaan d', 'a.id_klasifikasi_pekerjaan=d.klasifikasi_pekerjaan_id', 'left');
    $this->db->join('dec.dec_pekerjaan_disposisi e', 'e.id_pekerjaan = a.pekerjaan_id', 'left');
    $this->db->join('global.global_bagian_detail f', 'f.id_pegawai = e.id_user', 'left');
    $this->db->join('global.global_bagian g', 'g.bagian_id = f.id_bagian', 'left');
    $this->db->join('dec.dec_pekerjaan_nilai_hps h', 'h.id_pekerjaan = a.pekerjaan_id', 'left');

    if (isset($data['pekerjaan_judul'])) $this->db->like('upper(pekerjaan_judul)', strtoupper($data['pekerjaan_judul']));
    if (isset($data['pekerjaan_id'])) $this->db->where("pekerjaan_id = '" . ($data['pekerjaan_id']) . "'");
    // if (isset($data['klasifikasi_pekerjaan_id'])) $this->db->where("(d.klasifikasi_pekerjaan_id =  '" . $data['klasifikasi_pekerjaan_id'] . "' OR klasifikasi_pekerjaan_rkap = 'y')");
    if (isset($data['klasifikasi_pekerjaan_id'])) $this->db->where("klasifikasi_pekerjaan_rkap = 'y'");
    if (isset($data['klasifikasi_pekerjaan_id_non_rkap'])) $this->db->where("klasifikasi_pekerjaan_rkap = 'n'");
    // if (isset($data['klasifikasi_pekerjaan_id_non_rkap'])) $this->db->where("(d.klasifikasi_pekerjaan_id != '" . $data['klasifikasi_pekerjaan_id_non_rkap'] . "' OR d.klasifikasi_pekerjaan_id IS NULL)");
    if (isset($data['pekerjaan_status'])) $this->db->where_in('pekerjaan_status', $data['pekerjaan_status']);

    if (isset($data['pekerjaan_jenis'])) :
      $this->db->where('klasifikasi_pekerjaan_kode', $data['pekerjaan_jenis']);
    elseif (isset($data['pekerjaan_jenis_ifa'])) :
      $this->db->where("klasifikasi_pekerjaan_kode !='ifi'");
    endif;
    if (isset($data['pekerjaan_status_not_inpro'])) $this->db->where("pekerjaan_status != '" . $data['pekerjaan_status_not_inpro'] . "' ");
    if (isset($data['id_bagian'])) $this->db->where("(g.bagian_id = '" . $data['id_bagian'] . "' OR a.pic = '" . $data['pic_bagian'] . "')");
    if (isset($data['user_disposisi'])) $this->db->where("(e.id_user = '" . $data['user_disposisi'] . "' OR a.pic = '" . $data['user_disposisi'] . "')");
    if (isset($data['user_pic'])) $this->db->where("(e.id_user = '" . $data['user_pic'] . "' OR a.pic = '" . $data['user_pic'] . "')");
    if (!empty($data['tahun'])) $this->db->where('pekerjaan_tahun', $data['tahun']);

    if (isset($data['is_transmital'])) {
      $this->db->where('pekerjaan_disposisi_status', '8');
      $this->db->where("is_cc = 'y'");
    }

    $this->db->order_by('pekerjaan_id', 'DESC');
    $this->db->order_by('pekerjaan_waktu', 'DESC');

    $sql = $this->db->get();

    return (isset($data['pekerjaan_id'])) ? $sql->row_array() : $sql->result_array();
  }
  /* Get Pekerjaan */


  /* Get Pekerjaan dengan filter*/

  public function getPekerjaanDispo($data = null)
  {
    $this->db->select("a.*,b.*,c.*,d.*, to_char(pekerjaan_waktu_akhir, 'DD-MM-YYYY') as tanggal_akhir, to_char(pekerjaan_waktu, 'DD-MM-YYYY') as tanggal_awal, to_char(pekerjaan_waktu, 'yyyy-MM-dd') AS pekerjaan_waktunya,to_char(pekerjaan_waktu_akhir,'yyyy-MM-dd') AS pekerjaan_akhirnya");
    $this->db->from('dec.dec_pekerjaan a');
    $this->db->join('global.global_auth_user b', 'b.usr_id = a.pic', 'left');
    $this->db->join('global.global_pegawai c', 'c.pegawai_nik = b.usr_id');
    $this->db->join('global.global_klasifikasi_pekerjaan d', 'a.id_klasifikasi_pekerjaan=d.klasifikasi_pekerjaan_id', 'left');
    $this->db->join('dec.dec_pekerjaan_disposisi e', 'e.id_pekerjaan = a.pekerjaan_id', 'left');
    if (isset($data['pekerjaan_id'])) $this->db->where('pekerjaan_id', $data['pekerjaan_id']);
    if (isset($data['pic'])) $this->db->where('a.pic', $data['pic']);
    if (isset($data['id_user'])) $this->db->where('e.id_user', $data['id_user']);
    if (isset($data['klasifikasi_pekerjaan_id'])) $this->db->where('d.klasifikasi_pekerjaan_id', $data['klasifikasi_pekerjaan_id']);
    if (isset($data['klasifikasi_pekerjaan_id_non_rkap'])) $this->db->where('d.klasifikasi_pekerjaan_id !=', $data['klasifikasi_pekerjaan_id_non_rkap']);
    if (isset($data['pekerjaan_status'])) $this->db->where_in('pekerjaan_status', $data['pekerjaan_status']);
    if (isset($data['pekerjaan_status_not_inpro'])) $this->db->where("pekerjaan_status != '" . $data['pekerjaan_status_not_inpro'] . "' ");
    if (!empty($data['tahun'])) $this->db->where('pekerjaan_tahun', $data['tahun']);

    $this->db->group_by('pekerjaan_id ,usr_name,usr_loginname,usr_password,id_rol,usr_status,usr_when_create,usr_app_def,companycode,usr_foto,is_sync,usr_id,pegawai_nik,klasifikasi_pekerjaan_id');

    $this->db->order_by('pekerjaan_id', 'DESC');
    $this->db->order_by('pekerjaan_waktu', 'DESC');

    $sql = $this->db->get();

    return (isset($data['pekerjaan_id'])) ? $sql->row_array() : $sql->result_array();
  }

  /* Get Pekerjaan filter*/

  /* Get Pekerjaan Langsung Aksi */
  public function getPekerjaanLangsungAksi($data = null)
  {
    $this->db->select("a.*, b.*, c.*, to_char(pekerjaan_waktu, 'yyyy-MM-dd') AS pekerjaan_waktunya, date_part('month', pekerjaan_waktu) as month,to_char(pekerjaan_durasi,'yyyy-MM-dd') AS pekerjaan_durasinya ");
    $this->db->from('dec.dec_pekerjaan a');
    $this->db->join('global.global_auth_user b', 'a.id_user=b.usr_id', 'left');
    $this->db->join('global.global_klasifikasi_pekerjaan c', 'a.id_klasifikasi_pekerjaan=c.klasifikasi_pekerjaan_id', 'left');
    if (isset($data['pekerjaan_id'])) $this->db->where('pekerjaan_id', $data['pekerjaan_id']);
    $this->db->order_by('pekerjaan_waktu', "desc");
    $sql = $this->db->get();

    return (isset($data['pekerjaan_id'])) ? $sql->row_array() : $sql->result_array();
  }
  /* Get Pekerjaan Langsung Aksi */

  /* get pekerjaan transmital */
  public function getPekerjaanTransmitalUsulan($data = null)
  {
    $this->db->select("pekerjaan_nomor,pekerjaan_judul,pegawai_nama_dep,usr_name,pekerjaan_id,pekerjaan_status,pic,pekerjaan_waktu, to_char(pekerjaan_waktu_akhir, 'DD-MM-YYYY') as tanggal_akhir, to_char(pekerjaan_waktu, 'DD-MM-YYYY') as tanggal_awal, to_char(pekerjaan_waktu, 'yyyy-MM-dd') AS pekerjaan_waktunya,to_char(pekerjaan_waktu_akhir,'yyyy-MM-dd') AS pekerjaan_akhirnya, to_char(pekerjaan_waktu_selesai,'DD-MM-YYYY') as tanggal_selesai, to_char(pekerjaan_waktu_selesai,'yyyy-MM-dd') as tanggal_selesainya,pekerjaan_referensi_unit_kerja,id_koor_baru");
    // $this->db->select("a.*,b.*,c.*,d.*,e.*, to_char(pekerjaan_waktu_akhir, 'DD-MM-YYYY') as tanggal_akhir, to_char(pekerjaan_waktu, 'DD-MM-YYYY') as tanggal_awal, to_char(pekerjaan_waktu, 'yyyy-MM-dd') AS pekerjaan_waktunya,to_char(pekerjaan_waktu_akhir,'yyyy-MM-dd') AS pekerjaan_akhirnya, to_char(pekerjaan_waktu_selesai,'DD-MM-YYYY') as tanggal_selesai, to_char(pekerjaan_waktu_selesai,'yyyy-MM-dd') as tanggal_selesainya,pekerjaan_referensi_unit_kerja,id_koor_baru");
    $this->db->distinct();
    $this->db->from('dec.dec_pekerjaan a');
    $this->db->join('dec.dec_pekerjaan_disposisi b', 'b.id_pekerjaan = a.pekerjaan_id', 'left');
    $this->db->join('global.global_klasifikasi_pekerjaan c', 'c.klasifikasi_pekerjaan_id  = a.id_klasifikasi_pekerjaan', 'left');
    $this->db->join('global.global_auth_user d', 'd.usr_id = b.id_user', 'left');
    $this->db->join('global.global_pegawai e', 'e.pegawai_nik = d.usr_id');
    $this->db->join('global.global_bagian_detail f', 'f.id_pegawai = b.id_user', 'left');
    $this->db->join('global.global_bagian g', 'g.bagian_id = f.id_bagian', 'left');

    if (isset($data['pekerjaan_status'])) $this->db->where_in('pekerjaan_status', $data['pekerjaan_status']);
    $this->db->where('klasifikasi_pekerjaan_rkap', 'y');
    if (isset($data['id_user'])) $this->db->where('b.id_user', $data['id_user']);
    $this->db->where('pekerjaan_disposisi_status', '8');
    $this->db->where("is_cc IS NOT NULL");

    $this->db->where("a.pekerjaan_id NOT IN (SELECT id_pekerjaan FROM dec.dec_pekerjaan_disposisi_transmital)");


    $this->db->order_by('pekerjaan_id', 'DESC');
    $this->db->order_by('pekerjaan_waktu', 'DESC');

    $sql = $this->db->get();

    return (isset($data['pekerjaan_id'])) ? $sql->row_array() : $sql->result_array();
  }
  /* get pekerjaan transmital */

  /* get pekerjaan transmital */
  public function getPekerjaanTransmital($data = null)
  {
    $this->db->select("a.*,b.*,c.*,d.*, to_char(pekerjaan_waktu_akhir, 'DD-MM-YYYY') as tanggal_akhir, to_char(pekerjaan_waktu, 'DD-MM-YYYY') as tanggal_awal, to_char(pekerjaan_waktu, 'yyyy-MM-dd') AS pekerjaan_waktunya,to_char(pekerjaan_waktu_akhir,'yyyy-MM-dd') AS pekerjaan_akhirnya, to_char(pekerjaan_waktu_selesai,'DD-MM-YYYY') as tanggal_selesai, to_char(pekerjaan_waktu_selesai,'yyyy-MM-dd') as tanggal_selesainya,pekerjaan_referensi_unit_kerja");
    $this->db->distinct();
    $this->db->from('dec.dec_pekerjaan a');
    $this->db->join('global.global_auth_user b', 'b.usr_id = a.pic', 'left');
    $this->db->join('global.global_pegawai c', 'c.pegawai_nik = b.usr_id');
    $this->db->join('global.global_klasifikasi_pekerjaan d', 'a.id_klasifikasi_pekerjaan=d.klasifikasi_pekerjaan_id', 'left');
    $this->db->join('dec.dec_pekerjaan_disposisi_transmital e', 'e.id_pekerjaan = a.pekerjaan_id', 'left');
    $this->db->join('global.global_bagian f', 'f.bagian_id = e.id_bagian', 'left');


    if (isset($data['pekerjaan_judul'])) $this->db->like('upper(pekerjaan_judul)', strtoupper($data['pekerjaan_judul']));
    if (isset($data['pekerjaan_id'])) $this->db->where("pekerjaan_id = '" . ($data['pekerjaan_id']) . "'");
    if (isset($data['klasifikasi_pekerjaan_id'])) $this->db->where("klasifikasi_pekerjaan_rkap = 'y'");
    if (isset($data['klasifikasi_pekerjaan_id_non_rkap'])) $this->db->where("klasifikasi_pekerjaan_rkap = 'n'");
    if (isset($data['pekerjaan_is_selesai'])) $this->db->where("pekerjaan_is_selesai", $data['pekerjaan_is_selesai']);
    if (isset($data['pekerjaan_disposisi_transmital_status_cangun'])) $this->db->where_in('pekerjaan_disposisi_transmital_status', $data['pekerjaan_disposisi_transmital_status_cangun']);
    // if (isset($data['user_disposisi'])) $this->db->where("e.id_user = '" . $data['user_disposisi'] . "' AND pekerjaan_disposisi_transmital_status IN(" . $data['pekerjaan_disposisi_transmital_status'] . ")");
    // if (isset($data['user_disposisi'])) $this->db->where("e.id_user = '" . $data['user_disposisi'] . "' AND pekerjaan_status_transmital IN(" . $data['pekerjaan_disposisi_transmital_status'] . ")");
    if (isset($data['user_disposisi'])) $this->db->where("e.id_user = '" . $data['user_disposisi'] . "' AND pekerjaan_disposisi_transmital_status IN(" . $data['pekerjaan_disposisi_transmital_status'] . ")");
    if (!empty($data['tahun'])) $this->db->where('pekerjaan_tahun', $data['tahun']);
    $this->db->order_by('pekerjaan_id', 'DESC');
    $this->db->order_by('pekerjaan_waktu', 'DESC');

    $sql = $this->db->get();

    return (isset($data['pekerjaan_id'])) ? $sql->row_array() : $sql->result_array();
  }
  /* get pekerjaan transmital */
  /* Get Pekerjaan Dokumen */
  public function getPekerjaanDokumen($data = null)
  {
    $this->db->select("*");
    $this->db->from('dec.dec_pekerjaan_dokumen a');
    if (isset($data['id_pekerjaan'])) $this->db->where('id_pekerjaan', $data['id_pekerjaan']);
    if (isset($data['pekerjaan_dokumen_awal'])) $this->db->where('pekerjaan_dokumen_awal', $data['pekerjaan_dokumen_awal']);
    $sql = $this->db->get();

    return (isset($data['pekerjaan_dokumen_id'])) ? $sql->row_array() : $sql->result_array();
  }
  /* Get Pekerjaan Dokumen */

  /* Get History */
  public function getHistory($data = null)
  {
    if (isset($data['id_pekerjaan'])) $this->db->where('pekerjaan_id', $data['id_pekerjaan']);

    $this->db->select("*");
    $this->db->from('global.global_dblog a');
    $this->db->order_by('log_when', 'DESC');
    $sql = $this->db->get();

    return $sql->result_array();
  }
  /* Get History */

  /* Get Template */
  public function getTemplatePekerjaan($data = null)
  {
    $this->db->select("*");
    $this->db->from('dec.dec_pekerjaan_template');
    if (isset($data['nama'])) $this->db->where("upper(pekerjaan_template_nama) LIKE '%" . strtoupper($data['nama']) . "%'");
    if (isset($data['pekerjaan_template_id'])) $this->db->where("pekerjaan_template_id", $data['pekerjaan_template_id']);
    $this->db->order_by('pekerjaan_template_nama', 'ASC');
    $sql = $this->db->get();

    return $sql->result_array();
  }
  /* Get Template */

  /* Get Bidang */
  public function getBidang($data = null)
  {
    $this->db->select("*");
    $this->db->from('global.global_bidang');
    if (isset($data['nama'])) $this->db->where("upper(bidang_nama) LIKE '%" . strtoupper($data['nama']) . "%'");
    $this->db->order_by('bidang_nama', 'ASC');
    $sql = $this->db->get();

    return $sql->result_array();
  }
  /* Get Bidang */

  /* Get Urutan Proyek */
  public function getUrutanProyek($data = null)
  {
    $this->db->select("*");
    $this->db->from('global.global_urutan_proyek');
    if (isset($data['nama'])) $this->db->where("upper(urutan_proyek_nama) LIKE '%" . strtoupper($data['nama']) . "%'");
    $this->db->order_by('urutan_proyek_nama', 'ASC');
    $sql = $this->db->get();

    return $sql->result_array();
  }
  /* Get Urutan Proyek */

  /* Get Section Area */
  public function getSectionArea($data = null)
  {
    $this->db->select("*");
    $this->db->from('global.global_section_area');
    if (isset($data['nama'])) $this->db->where("upper(section_area_nama) LIKE '%" . strtoupper($data['nama']) . "%'");
    if (isset($data['id_urutan_proyek'])) $this->db->where('id_urutan_proyek', $data['id_urutan_proyek']);
    $this->db->order_by('section_area_nama', 'ASC');
    $sql = $this->db->get();

    return $sql->result_array();
  }
  /* Get Section Area */

  /* Get Revisi Nomor */
  public function getRevisiNomor($data = null)
  {
    $this->db->select('max(pekerjaan_dokumen_revisi) as max');
    $this->db->from('dec.dec_pekerjaan_dokumen');
    $this->db->where('pekerjaan_dokumen_id', $data['pekerjaan_dokumen_id_temp']);
    $sql = $this->db->get();
    return $sql->row_array();
  }
  /* Get Revisi Nomor */
  /* GET */

  /* INSERT */
  /* Insert Pekerjaan */
  public function insertPekerjaan($data)
  {
    $this->db->insert('dec.dec_pekerjaan', $data);
    return $this->db->affected_rows();
  }
  /* Insert Pekerjaan */

  /* Insert Pekerjaan Dokumen */
  public function insertPekerjaanDokumen($data)
  {
    $this->db->insert('dec.dec_pekerjaan_dokumen', $data);
    return $this->db->affected_rows();
  }
  /* Insert Pekerjaan Dokumen */

  /* insert dokumen transmital */
  public function insertDokumenTransmital($data)
  {
    $this->db->insert('dec.dec_pekerjaan_dokumen_transmital', $data);
    return $this->db->affected_rows();
  }
  /* insert dokumen transmital */

  /* Insert Pekerjaan Disposisi */
  public function insertPekerjaanDisposisi($data)
  {
    $this->db->insert('dec.dec_pekerjaan_disposisi', $data);
    return $this->db->affected_rows();
  }
  /* Insert Pekerjaan Disposisi */

  public function updatePekerjaanDisposisiDisposisi($id, $data = null)
  {
    $this->db->where('pekerjaan_disposisi_id', $id);
    $this->db->update('dec.dec_pekerjaan_disposisi', $data);
    return $this->db->affected_rows();
  }

  /* Insert Aksi Dokumen */
  public function simpanAksi($data)
  {
    $this->db->query("INSERT INTO dec.dec_pekerjaan_dokumen SELECT '" . $data['pekerjaan_dokumen_id'] . "', id_pekerjaan, '" . $data['pekerjaan_dokumen_file'] . "', id_pekerjaan_disposisi, pekerjaan_dokumen_nama, pekerjaan_dokumen_awal, '" . $data['pekerjaan_dokumen_status'] . "', '" . $data['pekerjaan_dokumen_keterangan'] . "', who_create, is_lama, id_pekerjaan_template ,'" . $data['pekerjaan_dokumen_revisi'] . "',pekerjaan_dokumen_status_review,'" . $data['id_create'] . "' , is_hps, '" . $data['is_proses'] . "', id_create_awal,pekerjaan_dokumen_nomor,pekerjaan_dokumen_cc,is_review, pekerjaan_dokumen_jumlah,pekerjaan_dokumen_jenis,id_bidang,id_urutan_proyek,id_section_area,pekerjaan_dokumen_kertas,pekerjaan_dokumen_orientasi,pekerjaan_dokumen_waktu,is_reject,is_update_ifa,pekerjaan_dokumen_qrcode,pekerjaan_dokumen_waktu_input,id_dokumen_awal,revisi_ifc,'" . $data['pekerjaan_dokumen_waktu_update'] . "' FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id_temp'] . "'");

    return $this->db->affected_rows();
  }
  /* Insert Aksi Dokumen */

  /* Insert Aksi Dokumen IFA*/
  public function simpanAksiIFA($data)
  {
    $this->db->query("INSERT INTO dec.dec_pekerjaan_dokumen SELECT '" . $data['pekerjaan_dokumen_id'] . "', id_pekerjaan, '" . $data['pekerjaan_dokumen_file'] . "', id_pekerjaan_disposisi, pekerjaan_dokumen_nama, pekerjaan_dokumen_awal, '" . $data['pekerjaan_dokumen_status'] . "', '" . $data['pekerjaan_dokumen_keterangan'] . "', who_create, is_lama, id_pekerjaan_template ,'" . $data['pekerjaan_dokumen_revisi'] . "',pekerjaan_dokumen_status_review,'" . $data['id_create'] . "' , is_hps, '" . $data['is_proses'] . "', id_create_awal,pekerjaan_dokumen_nomor,pekerjaan_dokumen_cc,is_review, pekerjaan_dokumen_jumlah,pekerjaan_dokumen_jenis,id_bidang,id_urutan_proyek,id_section_area,pekerjaan_dokumen_kertas,pekerjaan_dokumen_orientasi,'" . $data['pekerjaan_dokumen_waktu'] . "',is_reject,is_update_ifa,pekerjaan_dokumen_qrcode,pekerjaan_dokumen_waktu_input,id_dokumen_awal,revisi_ifc,'" . $data['pekerjaan_dokumen_waktu_update'] . "' FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id_temp'] . "'");

    return $this->db->affected_rows();
  }
  /* Insert Aksi Dokumen IFA */

  // insert dokumen ifc dari ifa
  public function simpanAksiIFC($data, $where)
  {
    $this->db->query("INSERT INTO dec.dec_pekerjaan_dokumen SELECT '" . $data['pekerjaan_dokumen_id'] . "','" . $data['id_pekerjaan'] . "','" . $data['pekerjaan_dokumen_file'] . "',id_pekerjaan_disposisi,'" . $data['pekerjaan_dokumen_nama'] . "',pekerjaan_dokumen_awal,'" . $data['pekerjaan_dokumen_status'] . "',pekerjaan_dokumen_keterangan,'" . $data['who_create'] . "',is_lama,'" . $data['id_pekerjaan_template'] . "',pekerjaan_dokumen_revisi,pekerjaan_dokumen_status_review,'" . $data['id_create'] . "',is_hps,is_proses,id_create_awal,'" . $data['pekerjaan_dokumen_nomor'] . "',pekerjaan_dokumen_cc,is_review,'" . $data['pekerjaan_dokumen_jumlah'] . "','" . $data['pekerjaan_dokumen_jenis'] . "','" . $data['id_bidang'] . "','" . $data['id_urutan_proyek'] . "','" . $data['id_section_area'] . "','" . $data['pekerjaan_dokumen_kertas'] . "','" . $data['pekerjaan_dokumen_orientasi'] . "',pekerjaan_dokumen_waktu,is_reject,is_update_ifa,pekerjaan_dokumen_qrcode,pekerjaan_dokumen_waktu_input,id_dokumen_awal,revisi_ifc,'" . $data['pekerjaan_dokumen_waktu_update'] . "' FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $where['pekerjaan_dokumen_id'] . "'");

    return $this->db->affected_rows();
  }
  // insert dokumen ifc dari ifa

  /* Insert Aksi Dokumen Revisi*/
  public function simpanAksiRevisi($data)
  {
    $this->db->query("INSERT INTO dec.dec_pekerjaan_dokumen SELECT '" . $data['pekerjaan_dokumen_id'] . "', id_pekerjaan, '" . $data['pekerjaan_dokumen_file'] . "', id_pekerjaan_disposisi, '" . $data['pekerjaan_dokumen_nama'] . "', pekerjaan_dokumen_awal, '" . $data['pekerjaan_dokumen_status'] . "', '" . $data['pekerjaan_dokumen_keterangan'] . "', who_create, is_lama, id_pekerjaan_template ,'" . $data['pekerjaan_dokumen_revisi'] . "',pekerjaan_dokumen_status_review,'" . $data['id_create'] . "' , is_hps, '" . $data['is_proses'] . "', id_create_awal,pekerjaan_dokumen_nomor,pekerjaan_dokumen_cc,is_review, pekerjaan_dokumen_jumlah,pekerjaan_dokumen_jenis,id_bidang,id_urutan_proyek,id_section_area,pekerjaan_dokumen_kertas,pekerjaan_dokumen_orientasi,pekerjaan_dokumen_waktu,is_reject,is_update_ifa,pekerjaan_dokumen_qrcode,'" . $data['pekerjaan_dokumen_waktu_update'] . "',id_dokumen_awal,revisi_ifc,'" . $data['pekerjaan_dokumen_waktu_update'] . "' FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id_temp'] . "'");

    return $this->db->affected_rows();
  }
  /* Insert Aksi Dokumen Revisi */

  /* Insert Aksi Dokumen CC*/
  public function simpanAksiCC($data)
  {
    $this->db->query("INSERT INTO dec.dec_pekerjaan_dokumen SELECT '" . $data['pekerjaan_dokumen_id'] . "', id_pekerjaan, '" . $data['pekerjaan_dokumen_file'] . "', id_pekerjaan_disposisi, pekerjaan_dokumen_nama, pekerjaan_dokumen_awal, pekerjaan_dokumen_status, '" . $data['pekerjaan_dokumen_keterangan'] . "', who_create, is_lama, id_pekerjaan_template ,pekerjaan_dokumen_revisi,'" . $data['pekerjaan_dokumen_status_review'] . "','" . $data['id_create'] . "' , is_hps,is_proses, id_create_awal,pekerjaan_dokumen_nomor,pekerjaan_dokumen_cc ,'" . $data['is_review'] . "', pekerjaan_dokumen_jumlah,pekerjaan_dokumen_jenis,id_bidang,id_urutan_proyek,id_section_area,pekerjaan_dokumen_kertas,pekerjaan_dokumen_orientasi,pekerjaan_dokumen_waktu,is_reject,is_update_ifa,pekerjaan_dokumen_qrcode,pekerjaan_dokumen_waktu_input,id_dokumen_awal,revisi_ifc FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id_temp'] . "'");

    return $this->db->affected_rows();
  }
  /* Insert Aksi Dokumen CC*/

  /* Insert Aksi Dokumen */
  public function simpanAksiSama($data)
  {
    $this->db->query("INSERT INTO dec.dec_pekerjaan_dokumen SELECT '" . $data['pekerjaan_dokumen_id'] . "', id_pekerjaan, pekerjaan_dokumen_file, id_pekerjaan_disposisi, pekerjaan_dokumen_nama, pekerjaan_dokumen_awal, '" . $data['pekerjaan_dokumen_status'] . "', '" . $data['pekerjaan_dokumen_keterangan'] . "', who_create, is_lama, id_pekerjaan_template,'" . $data['pekerjaan_dokumen_revisi'] . "',pekerjaan_dokumen_status_review,'" . $data['id_create'] . "' , is_hps, '" . $data['is_proses'] . "', id_create_awal,pekerjaan_dokumen_nomor,pekerjaan_dokumen_cc,is_review, pekerjaan_dokumen_jumlah,pekerjaan_dokumen_jenis,id_bidang,id_urutan_proyek,id_section_area,pekerjaan_dokumen_kertas,pekerjaan_dokumen_orientasi,pekerjaan_dokumen_waktu,is_reject,is_update_ifa,pekerjaan_dokumen_qrcode,pekerjaan_dokumen_waktu_input,id_dokumen_awal,revisi_ifc,'" . $data['pekerjaan_dokumen_waktu_update'] . "',is_transmital FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id_temp'] . "'");

    return $this->db->affected_rows();
  }
  /* Insert Aksi Dokumen */

  /* Insert Aksi Dokumen IFA*/
  public function simpanAksiIFASama($data)
  {
    $this->db->query("INSERT INTO dec.dec_pekerjaan_dokumen SELECT '" . $data['pekerjaan_dokumen_id'] . "', id_pekerjaan, pekerjaan_dokumen_file, id_pekerjaan_disposisi, pekerjaan_dokumen_nama, pekerjaan_dokumen_awal, '" . $data['pekerjaan_dokumen_status'] . "', '" . $data['pekerjaan_dokumen_keterangan'] . "', who_create, is_lama, id_pekerjaan_template,'" . $data['pekerjaan_dokumen_revisi'] . "',pekerjaan_dokumen_status_review,'" . $data['id_create'] . "' , is_hps, '" . $data['is_proses'] . "', id_create_awal,pekerjaan_dokumen_nomor,pekerjaan_dokumen_cc,is_review, pekerjaan_dokumen_jumlah,pekerjaan_dokumen_jenis,id_bidang,id_urutan_proyek,id_section_area,pekerjaan_dokumen_kertas,pekerjaan_dokumen_orientasi,'" . $data['pekerjaan_dokumen_waktu'] . "',is_reject,is_update_ifa,pekerjaan_dokumen_qrcode,pekerjaan_dokumen_waktu_input,id_dokumen_awal,revisi_ifc,'" . $data['pekerjaan_dokumen_waktu_update'] . "' FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id_temp'] . "'");

    return $this->db->affected_rows();
  }
  /* Insert Aksi Dokumen IFA*/

  /* Insert Aksi Dokumen */
  public function simpanAksiSamaRevisi($data)
  {
    $this->db->query("INSERT INTO dec.dec_pekerjaan_dokumen SELECT '" . $data['pekerjaan_dokumen_id'] . "', id_pekerjaan, pekerjaan_dokumen_file, id_pekerjaan_disposisi, '" . $data['pekerjaan_dokumen_nama'] . "', pekerjaan_dokumen_awal, '" . $data['pekerjaan_dokumen_status'] . "', '" . $data['pekerjaan_dokumen_keterangan'] . "', who_create, is_lama, id_pekerjaan_template,'" . $data['pekerjaan_dokumen_revisi'] . "',pekerjaan_dokumen_status_review,'" . $data['id_create'] . "' , is_hps, '" . $data['is_proses'] . "', id_create_awal,pekerjaan_dokumen_nomor,pekerjaan_dokumen_cc,is_review, pekerjaan_dokumen_jumlah,pekerjaan_dokumen_jenis,id_bidang,id_urutan_proyek,id_section_area,pekerjaan_dokumen_kertas,pekerjaan_dokumen_orientasi,pekerjaan_dokumen_waktu,is_reject,is_update_ifa,pekerjaan_dokumen_qrcode,'" . $data['pekerjaan_dokumen_waktu_update'] . "',id_dokumen_awal,revisi_ifc,'" . $data['pekerjaan_dokumen_waktu_update'] . "' FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id_temp'] . "'");

    return $this->db->affected_rows();
  }
  /* Insert Aksi Dokumen */

  /* Insert Aksi Dokumen */
  public function simpanAksiSamaCC($data)
  {
    $this->db->query("INSERT INTO dec.dec_pekerjaan_dokumen SELECT '" . $data['pekerjaan_dokumen_id'] . "', id_pekerjaan, pekerjaan_dokumen_file, id_pekerjaan_disposisi, pekerjaan_dokumen_nama, pekerjaan_dokumen_awal, pekerjaan_dokumen_status, '" . $data['pekerjaan_dokumen_keterangan'] . "', who_create, is_lama, id_pekerjaan_template,null, '" . $data['pekerjaan_dokumen_status_review'] . "','" . $data['id_create'] . "' , is_hps, is_proses, id_create_awal,pekerjaan_dokumen_nomor,pekerjaan_dokumen_cc,'" . $data['is_review'] . "', pekerjaan_dokumen_jumlah,pekerjaan_dokumen_jenis,id_bidang,id_urutan_proyek,id_section_area,pekerjaan_dokumen_kertas,pekerjaan_dokumen_orientasi,pekerjaan_dokumen_waktu,is_reject,is_update_ifa,pekerjaan_dokumen_qrcode,pekerjaan_dokumen_waktu_input,id_dokumen_awal,revisi_ifc FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id_temp'] . "'");

    return $this->db->affected_rows();
  }
  /* Insert Aksi Dokumen */

  /* aksi dokumen kontraktor */
  public function simpanAksiDokumenKontraktorSama($data = null)
  {
    $this->db->query("INSERT INTO dec.dec_pekerjaan_dokumen_transmital SELECT '" . $data['pekerjaan_dokumen_id'] . "',id_pekerjaan,pekerjaan_dokumen_file,pekerjaan_dokumen_nama,pekerjaan_dokumen_awal,'" . $data['pekerjaan_dokumen_status'] . "','" . $data['pekerjaan_dokumen_keterangan'] . "',who_create,is_lama,'" . $data['pekerjaan_dokumen_revisi'] . "','" . $data['id_create'] . "','" . $data['is_proses'] . "',id_create_awal,pekerjaan_dokumen_nomor,pekerjaan_dokumen_jumlah,pekerjaan_dokumen_waktu,pekerjaan_dokumen_waktu_input,id_dokumen_awal,'" . $data['pekerjaan_dokumen_waktu_update'] . "',id_bagian,is_change, pekerjaan_dokumen_qrcode, pekerjaan_dokumen_jenis, '" . $data['pekerjaan_dokumen_status_doc'] . "', pekerjaan_dokumen_kertas, pekerjaan_dokumen_orientasi FROM dec.dec_pekerjaan_dokumen_transmital WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id_temp'] . "' ");
  }

  public function simpanAksiDokumenKontraktor($data = null)
  {
    $this->db->query("INSERT INTO dec.dec_pekerjaan_dokumen_transmital SELECT '" . $data['pekerjaan_dokumen_id'] . "',id_pekerjaan,'" . $data['pekerjaan_dokumen_file'] . "',pekerjaan_dokumen_nama,pekerjaan_dokumen_awal,'" . $data['pekerjaan_dokumen_status'] . "','" . $data['pekerjaan_dokumen_keterangan'] . "',who_create,is_lama,'" . $data['pekerjaan_dokumen_revisi'] . "','" . $data['id_create'] . "','" . $data['is_proses'] . "',id_create_awal,pekerjaan_dokumen_nomor,pekerjaan_dokumen_jumlah,pekerjaan_dokumen_waktu,pekerjaan_dokumen_waktu_input,id_dokumen_awal,'" . $data['pekerjaan_dokumen_waktu_update'] . "',id_bagian,is_change, pekerjaan_dokumen_qrcode, pekerjaan_dokumen_jenis, '" . $data['pekerjaan_dokumen_status_doc'] . "', pekerjaan_dokumen_kertas, pekerjaan_dokumen_orientasi FROM dec.dec_pekerjaan_dokumen_transmital WHERE pekerjaan_dokumen_id = '" . $data['pekerjaan_dokumen_id_temp'] . "' ");
  }
  /* aksi dokumen kontraktor */



  /* Insert Email */
  public function insertEmail($data)
  {
    $this->db->insert('dec.dec_pekerjaan_email', $data);
    return $this->db->affected_rows();
  }
  /* Insert Email */
  /* INSERT */

  /* UPDATE */
  /* Update Pekerjaan */
  public function updatePekerjaan($data, $id)
  {
    $this->db->set($data);
    $this->db->where('pekerjaan_id', $id);
    $this->db->update('dec.dec_pekerjaan');

    return $this->db->affected_rows();
  }
  /* Update Pekerjaan */

  /* Update Pekerjaan Dokumen */
  public function updatePekerjaanDokumen($data, $id)
  {
    $this->db->set($data);
    $this->db->where('pekerjaan_dokumen_id', $id);
    $this->db->update('dec.dec_pekerjaan_dokumen');

    return $this->db->affected_rows();
  }
  /* Update Pekerjaan Dokumen */

  /* update dokumen transmital */
  public function updateDokumenTransmital($id, $data = null)
  {
    $this->db->where('pekerjaan_dokumen_id', $id);
    $this->db->update('dec.dec_pekerjaan_dokumen_transmital', $data);

    return $this->db->affected_rows();
  }
  /* update dokumen transmital */

  /*update nomor revisi */
  public function updateRevisiNomor($data, $id)
  {
    $this->db->where('pekerjaan_dokumen_id', $id);
    $this->db->update('dec.dec_pekerjaan_dokumen', $data);

    return $this->db->affected_rows();
  }
  /*update nomor revisi */
  /* UPDATE */

  /* DELETE */
  /* Delete Pekerjaan */
  public function deletePekerjaan($pekerjaan_id)
  {
    $this->db->where('pekerjaan_id', $pekerjaan_id);
    $this->db->delete('dec.dec_pekerjaan');

    return $this->db->affected_rows();
  }
  /* Delete Pekerjaan */

  /* Delete Pekerjaan Dokumen */
  public function deletePekerjaanDokumen($id)
  {
    $this->db->where('pekerjaan_dokumen_id', $id);
    $this->db->delete('dec.dec_pekerjaan_dokumen');

    return $this->db->affected_rows();
  }
  /* Delete Pekerjaan Dokumen */

  public function deleteDokumenTransmital($id)
  {
    $this->db->where('pekerjaan_dokumen_id', $id);
    $this->db->delete('dec.dec_pekerjaan_dokumen_transmital');

    return $this->db->affected_rows();
  }

  /* Delete Pekerjaan Disposisi */

  public function deleteDisposisi($where = null)
  {
    if (isset($where['id_pekerjaan'])) $this->db->where('id_pekerjaan', $where['id_pekerjaan']);
    if (isset($where['pekerjaan_disposisi_status'])) $this->db->where('pekerjaan_disposisi_status', $where['pekerjaan_disposisi_status']);
    if (isset($where['is_cc'])) {
      if ($where['is_cc'] == '-') {
        $this->db->where("is_cc is null");
      } else {
        $this->db->where('is_cc', $where['is_cc']);
      }
    }
    if (isset($where['id_user'])) $this->db->where('id_user', $where['id_user']);
    if (isset($where['id_penanggung_jawab'])) $this->db->where('id_penanggung_jawab', $where['id_penanggung_jawab']);
    // if(isset($where['is_proses'])) $this->db->where('is_proses', $where['is_proses']);
    // $this->db->where("is_proses is NULL");
    $this->db->delete('dec.dec_pekerjaan_disposisi');
    return $this->db->affected_rows();
  }

  public function deletePekerjaanDisposisi($id, $nik = null, $id_tanggung_jawab = null, $status = null, $is_cc = null)
  {
    $this->db->where('id_pekerjaan', $id);
    if ($is_cc != null) $this->db->where('is_cc', $is_cc);
    if ($nik != null) $this->db->where('id_user', $nik);
    if ($status != null) $this->db->where('pekerjaan_disposisi_status', $status);
    if ($id_tanggung_jawab != null) $this->db->where('id_penanggung_jawab', $id_tanggung_jawab);
    $this->db->delete('dec.dec_pekerjaan_disposisi');

    return $this->db->affected_rows();
  }

  public function deletePekerjaanDisposisiDisposisi($id, $nik = null, $id_tanggung_jawab = null, $status = null)
  {
    $this->db->where('id_pekerjaan', $id);
    if ($nik != null) $this->db->where('id_user', $nik);
    if ($status != null) $this->db->where('pekerjaan_disposisi_status', $status);
    if ($id_tanggung_jawab != null) $this->db->where('id_penanggung_jawab', $id_tanggung_jawab);
    $this->db->where("is_proses is null");
    $this->db->delete('dec.dec_pekerjaan_disposisi');

    return $this->db->affected_rows();
  }

  public function deletePekerjaanDisposisiReject($pekerjaan_id, $disposisi_status, $user_id)
  {
    if (isset($pekerjaan_id)) $this->db->where('id_pekerjaan', $pekerjaan_id);
    if (isset($disposisi_status)) $this->db->where('pekerjaan_disposisi_status', $disposisi_status);
    if (isset($user_id)) $this->db->where('id_user', $user_id);
    $this->db->delete('dec.dec_pekerjaan_disposisi');
    return $this->db->affected_rows();
  }
  /* DELETE */











  public function editAksi($data, $id)
  {
    $this->db->set($data);
    $this->db->where('pekerjaan_dokumen_id', $id);
    $this->db->update('dec.dec_pekerjaan_dokumen');

    return $this->db->affected_rows();
  }


  /* GET */



  public function getPekerjaanDisposisi($data = null)
  {
    $this->db->select("count(pekerjaan_disposisi_id) as jumlah");
    $this->db->from('dec.dec_pekerjaan_disposisi');
    if (isset($data['pekerjaan_id'])) $this->db->where('id_pekerjaan', $data['pekerjaan_id']);
    if (isset($data['is_aktif'])) $this->db->where('is_aktif', $data['is_aktif']);
    if (isset($data['id_penanggung_jawab'])) $this->db->where('id_penanggung_jawab', $data['id_penanggung_jawab']);

    $sql = $this->db->get();

    return $sql->row_array();
  }

  public function getPekerjaanDetail($data = null)
  {
    $this->db->select('*');
    $this->db->from('dec.dec_pekerjaan a');
    $this->db->join('dec.dec_pekerjaan_disposisi b', 'a.pekerjaan_id = b.id_pekerjaan AND a.pekerjaan_status = b.pekerjaan_disposisi_status', 'left');
    if (isset($data['pekerjaan_id'])) $this->db->where('b.id_pekerjaan', $data['pekerjaan_id']);
    if (isset($data['pegawai_nik'])) $this->db->where('b.id_user', $data['pegawai_nik']);
    $this->db->where('b.is_aktif', 'y');
    $sql = $this->db->get();

    return (isset($data['pekerjaan_id']) && isset($data['pegawai_nik'])) ? $sql->row_array() : $sql->result_array();

    // return $sql->row_array();
  }

  public function getPekerjaanDetailBerjalan($data = null)
  {
    $this->db->select('*');
    $this->db->from('dec.dec_pekerjaan a');
    $this->db->join('dec.dec_pekerjaan_disposisi b', 'a.pekerjaan_id = b.id_pekerjaan');
    if (isset($data['pekerjaan_status'])) $this->db->where('pekerjaan_status  ', $data['pekerjaan_status']);
    if (isset($data['pekerjaan_disposisi_status']))    $this->db->where('pekerjaan_disposisi_status  ', $data['pekerjaan_disposisi_status']);
    if (isset($data['pekerjaan_id'])) $this->db->where('b.id_pekerjaan', $data['pekerjaan_id']);
    if (isset($data['pegawai_nik'])) $this->db->where('b.id_user', $data['pegawai_nik']);
    $this->db->where('b.is_aktif', 'y');
    $this->db->order_by('pekerjaan_disposisi_status', 'desc');

    $sql = $this->db->get();

    return (isset($data['pekerjaan_id']) && isset($data['pegawai_nik'])) ? $sql->row_array() : $sql->result_array();

    // return $sql->row_array();
  }

  public function getPekerjaanDetailIFARev($data = null)
  {
    $this->db->select('*');
    $this->db->from('dec.dec_pekerjaan a');
    $this->db->join('dec.dec_pekerjaan_disposisi b', 'a.pekerjaan_id = b.id_pekerjaan');
    if (isset($data['pekerjaan_status'])) $this->db->where('pekerjaan_status  ', $data['pekerjaan_status']);
    if (isset($data['pekerjaan_disposisi_status_rev_ifa']))    $this->db->where('pekerjaan_disposisi_status  ', $data['pekerjaan_disposisi_status_rev_ifa']);
    if (isset($data['pekerjaan_disposisi_status_rev_ifa_avp']))    $this->db->where('pekerjaan_disposisi_status  ', $data['pekerjaan_disposisi_status_rev_ifa_avp']);
    if (isset($data['pekerjaan_id'])) $this->db->where('b.id_pekerjaan', $data['pekerjaan_id']);
    if (isset($data['pegawai_nik'])) $this->db->where('b.id_user', $data['pegawai_nik']);
    $this->db->where('b.is_aktif', 'y');
    $this->db->order_by('pekerjaan_disposisi_status', 'desc');

    $sql = $this->db->get();

    return (isset($data['pekerjaan_id']) && isset($data['pegawai_nik'])) ? $sql->row_array() : $sql->result_array();

    // return $sql->row_array();
  }

  public function getPekerjaanDetailLangsung($data = null)
  {
    $this->db->select('*');
    $this->db->from('dec.dec_pekerjaan_disposisi a');
    if (isset($data['pekerjaan_disposisi_status'])) $this->db->where('pekerjaan_disposisi_status', $data['pekerjaan_disposisi_status']);
    if (isset($data['pekerjaan_id'])) $this->db->where('id_pekerjaan', $data['pekerjaan_id']);
    if (isset($data['pegawai_nik'])) $this->db->where('id_user', $data['pegawai_nik']);
    $this->db->where('is_aktif', 'y');
    $this->db->order_by('pekerjaan_disposisi_status', 'ASC');


    $query = $this->db->get();

    return (isset($data['pekerjaan_id']) && isset($data['pegawai_nik'])) ? $query->row_array() : $query->result_array();
  }

  public function getUserList($data = null)
  {
    if (isset($data['pegawai_poscode'])) $this->db->where('pegawai_direct_superior', $data['pegawai_poscode']);
    if (isset($data['pegawai_id_bag'])) $this->db->where('pegawai_id_bag', $data['pegawai_id_bag']);
    if (isset($data['pegawai_unitkerja'])) $this->db->where('pegawai_unitkerja', $data['pegawai_unitkerja']);
    if (isset($data['pegawai_nama'])) $this->db->where("upper(pegawai_nama) LIKE '%" . strtoupper($data['pegawai_nama']) . "%'");

    $this->db->select('*');
    $this->db->from('global.global_pegawai');
    $this->db->order_by('pegawai_postitle', 'asc');
    $this->db->order_by('pegawai_nama', 'asc');

    $sql = $this->db->get();

    return $sql->result_array();
  }

  public function getUserCangunList($data = '')
  {
    if (isset($data['pegawai_nama'])) $this->db->where("upper(pegawai_nama) LIKE '%" . strtoupper($data['pegawai_nama']) . "%'");

    $this->db->select('*');
    $this->db->from('global.global_pegawai a');
    $this->db->join('global.global_bagian_detail b', 'b.id_pegawai = a.pegawai_nik', 'left');
    $this->db->where('pegawai_id_dep', 'E53000');
    $this->db->order_by('id_bagian', 'desc');
    $this->db->order_by('pegawai_jabatan', 'asc');

    $sql = $this->db->get();
    return $sql->result_array();
  }

  /* user list vp all dep */
  public function getUserListVPAllDep($data = null)
  {
    $this->db->select('*');
    $this->db->from('global.global_pegawai');
    if (isset($data['pegawai_id_dep'])) $this->db->where('pegawai_id_dep', $data['pegawai_id_dep']);
    if (isset($data['pegawai_nama'])) $this->db->where("upper(pegawai_nama) LIKE '%" . strtoupper($data['pegawai_nama']) . "%'");
    $this->db->like('pegawai_jabatan', '20F', 'escape');
    $this->db->order_by('pegawai_nama', 'asc');
    $this->db->order_by('pegawai_postitle', 'asc');

    $sql = $this->db->get();

    return $sql->result_array();
  }
  /* user list vp all dep */

  /* user list VP */
  public function getUserListVP($data = null)
  {
    $this->db->select('*');
    $this->db->from('global.global_pegawai');
    $this->db->where('pegawai_direct_superior', 'E53000000');
    if (isset($data['pegawai_nama'])) $this->db->where("upper(pegawai_nama) LIKE '%" . strtoupper($data['pegawai_nama']) . "%'");
    // $this->db->where("UPPER(pegawai_jabatan) LIKE '%".(strtoupper('3'))."%'");
    $this->db->where("LEFT(pegawai_jabatan,1) = '3'");
    $this->db->order_by('pegawai_postitle', 'asc');
    $this->db->order_by('pegawai_nama', 'asc');

    $sql = $this->db->get();

    return $sql->result_array();
  }
  /* user list VP */

  /* user list AVP */
  public function getUserListAVP($data = null)
  {
    $this->db->select('*');
    $this->db->from('global.global_pegawai a');
    $this->db->join('global.global_bagian_detail b', 'b.id_pegawai = a.pegawai_nik', 'left');
    $this->db->join('global.global_bagian c', 'c.bagian_id = b.id_bagian', 'left');
    if (isset($data['bagian_id'])) $this->db->where("c.bagian_id = '$data[bagian_id]'");
    if (isset($data['pegawai_nama'])) $this->db->where("upper(pegawai_nama) LIKE '%" . strtoupper($data['pegawai_nama']) . "%'");
    if (isset($data['bagian_nama'])) $this->db->like('UPPER(bagian_nama)', str_replace('BAG ', '', strtoupper($data['bagian_nama'])));
    if (isset($data['pegawai_poscode'])) {
      $this->db->where("(LEFT(pegawai_jabatan,1) != '3' OR pegawai_poscode = '$data[pegawai_poscode]') ");
    }
    $this->db->order_by('pegawai_postitle', 'asc');
    $this->db->order_by('pegawai_nama', 'asc');

    $sql = $this->db->get();

    return $sql->result_array();
  }
  /* user list AVP */

  /* user list AVP */
  public function getUserListAVPKhusus($data = null)
  {
    $this->db->select('*');
    $this->db->from('global.global_pegawai a');
    $this->db->join('global.global_bagian_detail b', 'b.id_pegawai = a.pegawai_nik', 'left');
    $this->db->join('global.global_bagian c', 'c.bagian_id = b.id_bagian', 'left');
    if (isset($data['bagian_id'])) $this->db->where("c.bagian_id = '$data[bagian_id]'");
    if (isset($data['pegawai_nama'])) $this->db->where("upper(pegawai_nama) LIKE '%" . strtoupper($data['pegawai_nama']) . "%'");
    if (isset($data['pegawai_poscode'])) {
      $this->db->where("pegawai_poscode = '$data[pegawai_poscode]' ");
    }
    $this->db->order_by('pegawai_postitle', 'asc');
    $this->db->order_by('pegawai_nama', 'asc');

    $sql = $this->db->get();

    return $sql->result_array();
  }
  /* user list AVP */

  /* user list staf */
  public function getUserStaf($data = null)
  {
    $this->db->select('*');
    $this->db->from('global.global_pegawai');
    // if (isset($data['pekerjaan_id'])) $this->db->where('b.id_pekerjaan', $data['pekerjaan_id']);
    if (isset($data['pegawai_nik'])) $this->db->where('b.id_user', $data['pegawai_nik']);
    if (isset($data['pegawai_unit_id'])) $this->db->where('pegawai_unit_id', $data['pegawai_unit_id']);

    if (isset($data['pegawai_nama'])) $this->db->where("upper(pegawai_nama) LIKE '%" . strtoupper($data['pegawai_nama']) . "%' OR pegawai_postitle iLIKE '%" . $data['pegawai_nama'] . "%' ");
    $this->db->limit('100');
    // $this->db->group_by('pegawai_nama');
    $this->db->order_by('pegawai_nama', 'asc');

    $sql = $this->db->get();

    return $sql->result_array();
  }


  /* user list staf */

  /*user koor pengganti*/
  public function getUserKoorPengganti($data = null)
  {
    $this->db->select('*');
    $this->db->from('global.global_pegawai a');
    $this->db->join('dec.dec_pekerjaan_disposisi b', 'b.id_user = a.pegawai_nik', 'left');
    // $this->db->where('pegawai_direct_superior', 'E53000000');
    if (isset($data['pegawai_nama'])) $this->db->where("upper(pegawai_nama) LIKE '%" . strtoupper($data['pegawai_nama']) . "%'");
    if (isset($data['id_pekerjaan'])) $this->db->where('id_pekerjaan', $data['id_pekerjaan']);
    if (isset($data['id_pekerjaan'])) $this->db->where('pekerjaan_disposisi_status', '4');
    // $this->db->where("LEFT(pegawai_jabatan,1) = '3'");
    $this->db->order_by('pegawai_postitle', 'asc');
    $this->db->order_by('pegawai_nama', 'asc');

    $sql = $this->db->get();

    return $sql->result_array();
  }
  /*user koor pengganti*/

  public function getDataKoor($data = null)
  {
    if (isset($data['id_pekerjaan'])) $this->db->where('id_pekerjaan', $data['id_pekerjaan']);
    if (isset($data['id_bagian'])) $this->db->where('id_bagian', $data['id_bagian']);
    if (isset($data['id_penanggung_jawab'])) $this->db->where('id_penanggung_jawab', $data['id_penanggung_jawab']);

    $this->db->select('*');
    $this->db->from('dec.dec_pekerjaan_disposisi a');
    $this->db->join('global.global_bagian_detail b', 'b.id_pegawai = a.id_user', 'left');

    $sql = $this->db->get();

    return $sql->result_array();
  }

  public function getDataKoorAVP($data = null)
  {
    if (isset($data['id_pekerjaan'])) $this->db->where('id_pekerjaan', $data['id_pekerjaan']);
    if (isset($data['id_bagian'])) $this->db->where('id_bagian', $data['id_bagian']);
    if (isset($data['id_penanggung_jawab'])) $this->db->where('id_penanggung_jawab', $data['id_penanggung_jawab']);
    $this->db->where('pekerjaan_disposisi_status', '4');

    $this->db->select('*');
    $this->db->from('dec.dec_pekerjaan_disposisi a');
    $this->db->join('global.global_bagian_detail b', 'b.id_pegawai = a.id_user', 'left');

    $sql = $this->db->get();

    return $sql->result_array();
  }


  public function getUser($data = null)
  {
    $this->db->select('usr_id, usr_name');
    $this->db->from('global.global_auth_user');
    if (isset($data['usr_name'])) $this->db->where("upper(usr_name) LIKE '%" . strtoupper($data['usr_name']) . "%'");
    $this->db->order_by('usr_name', 'asc');
    // if (!empty($where)) $this->db->where($where);

    $sql = $this->db->get();

    return $sql->result_array();
  }

  public function getKlasifikasiPekerjaan($data = null)
  {
    $this->db->select("*");
    $this->db->from('global.global_klasifikasi_pekerjaan');
    $this->db->order_by('klasifikasi_pekerjaan_nama', 'asc');

    $sql = $this->db->get();

    return $sql->result_array();
  }



  public function getDisposisi($data = null)
  {
    $this->db->select("*");
    $this->db->from('dec.dec_pekerjaan_disposisi a');
    $this->db->join('global.global_auth_user b', 'b.usr_id = a.id_user', 'left');
    if (isset($data['id_pekerjaan'])) $this->db->where('id_pekerjaan', $data['id_pekerjaan']);
    $this->db->order_by('pekerjaan_disposisi_waktu', 'asc');

    $sql = $this->db->get();

    return (isset($data['pekerjaan_id'])) ? $sql->row_array() : $sql->result_array();
  }

  public function getUnitDepartemen()
  {
    $this->db->select('pegawai_unit_id,pegawai_unit_name');
    $this->db->from('global.global_pegawai');

    $sql = $this->db->get();

    return $sql->result_array();
  }

  public function getDokumenAksi($data = null)
  {
    $this->db->select('*');
    $this->db->from('dec.dec_pekerjaan_dokumen');
    $this->db->where('pekerjaan_dokumen_id', $data['pekerjaan_dokumen_id']);

    $sql = $this->db->get();

    return $sql->row_array();
  }

  public function getDokumenAksiTransmital($data = null)
  {
    $this->db->select('*');
    $this->db->from('dec.dec_pekerjaan_dokumen_transmital');
    $this->db->where('pekerjaan_dokumen_id', $data['pekerjaan_dokumen_id']);

    $sql = $this->db->get();

    return $sql->row_array();
  }


  public function getDokumenTransmital($data = null)
  {
    if (isset($data['id_pekerjaan'])) $this->db->where('id_pekerjaan', $data['id_pekerjaan']);
    if (isset($data['id_bagian'])) $this->db->where('id_bagian', $data['id_bagian']);
    if (isset($data['id_dokumen_awal'])) {
      $this->db->where('id_dokumen_awal', $data['id_dokumen_awal']);
    } else {
      $this->db->where('is_lama', 'n');
    }
    if (isset($data['pekerjaan_dokumen_id'])) $this->db->where('pekerjaan_dokumen_id', $data['pekerjaan_dokumen_id']);
    $this->db->select("*,to_char(pekerjaan_dokumen_waktu_input, 'DD-MM-YYYY HH24:MI:SS') as tanggal_dokumen_input");
    $this->db->from('dec.dec_pekerjaan_dokumen_transmital a');
    $this->db->join('dec.dec_pekerjaan b', 'b.pekerjaan_id = a.id_pekerjaan', 'left');
    $this->db->join('global.global_bagian c', 'c.bagian_id = a.id_bagian', 'left');
    $this->db->join('global.global_pegawai d', 'd.pegawai_nik = a.id_create', 'left');
    $this->db->join('global.global_bagian e', 'e.bagian_id = a.id_bagian', 'left');

    $this->db->order_by('pekerjaan_dokumen_nama', 'asc');
    $this->db->order_by('pekerjaan_dokumen_waktu_update', 'desc');
    $this->db->order_by('CAST(pekerjaan_dokumen_status as INT)', 'desc');

    $sql = $this->db->get();

    if ($sql) {
      return (isset($data['pekerjaan_dokumen_id'])) ? $sql->row_array() : $sql->result_array();
    }
  }

  public function getAsetDocument($data = null)
  {
    if (isset($data['is_cc_hps'])) {
      $this->db->select("a.*,b.*,c.*,d.*,e.*,f.*,h.*,i.*");
    } else {
      $this->db->select("a.*,b.*,c.*,d.*,e.*,f.*,h.*,x.*,k.*,l.* ");
    }
    $this->db->from('dec.dec_pekerjaan_dokumen a');
    $this->db->join('dec.dec_pekerjaan_template b', 'a.id_pekerjaan_template = b.pekerjaan_template_id', 'left');
    $this->db->join('dec.dec_pekerjaan c', 'c.pekerjaan_id = a.id_pekerjaan', 'left');
    $this->db->join('global.global_bagian_detail d ', 'd.id_pegawai = a.id_create_awal', 'left');
    $this->db->join('global.global_bagian e', 'e.bagian_id=id_bagian', 'left');
    $this->db->join('global.global_pegawai f', 'f.pegawai_nik = d.id_pegawai', 'left');
    $this->db->join('global.global_pegawai h', 'a.pekerjaan_dokumen_cc = h.pegawai_nama', 'left');
    if (isset($data['is_cc_hps'])) {
      $this->db->join('dec.dec_pekerjaan_disposisi i', 'i.id_pekerjaan = c.pekerjaan_id', 'left');
      $this->db->where("i.is_cc = 'h'");
      if ($data['id_dep'] != 'E53000') $this->db->where('i.id_user', $data['id_user']);
    }
    $this->db->join('global.global_bidang j', 'j.bidang_id = a.id_bidang', 'left');
    $this->db->join('dec.dec_pekerjaan_dokumen_penomoran x', 'x.id_pekerjaan = a.id_pekerjaan', 'left');
    $this->db->join('global.global_urutan_proyek k', 'x.urutan_proyek_default = k.urutan_proyek_id', 'left');
    $this->db->join('global.global_section_area l', 'x.section_area_default = l.section_area_id', 'left');

    if (isset($data['id_pekerjaan'])) $this->db->where('a.id_pekerjaan', $data['id_pekerjaan']);
    $this->db->where('pekerjaan_dokumen_awal', 'n');
    $this->db->where("(is_lama != 'y' or is_lama is null)");
    if (isset($data['pekerjaan_disposisi_status'])) $this->db->where('pekerjaan_status', $data['pekerjaan_disposisi_status']);
    if (isset($data['id_create'])) $this->db->where('id_create', $data['id_create']);
    if (isset($data['is_hps'])) $this->db->where('is_hps', $data['is_hps']);
    if (isset($data['is_hps_cc'])) $this->db->where('is_hps', $data['is_hps_cc']);
    if (isset($data['pekerjaan_dokumen_cc'])) $this->db->where('pekerjaan_dokumen_cc', $data['pekerjaan_dokumen_cc']);
    if (isset($data['pekerjaan_dokumen_status_min'])) $this->db->where("pekerjaan_dokumen_status >= '" . $data['pekerjaan_dokumen_status_min'] . "'");
    if (isset($data['pekerjaan_dokumen_status_max'])) $this->db->where("pekerjaan_dokumen_status <= '" . $data['pekerjaan_dokumen_status_max'] . "'");



    $sql = $this->db->get();
    // echo $this->db->last_query();
    return (isset($data['pekerjaan_dokumen_id'])) ? $sql->row_array() : $sql->result_array();
  }

  public function getAsetDocumentUpload($data = null)
  {
    $this->db->select("*,to_char(pekerjaan_dokumen_waktu_input, 'DD-MM-YYYY HH24:MI:SS') as tanggal_dokumen_input");
    $this->db->from('dec.dec_pekerjaan_dokumen a');
    $this->db->join('dec.dec_pekerjaan_template b', 'a.id_pekerjaan_template = b.pekerjaan_template_id', 'left');
    $this->db->join('dec.dec_pekerjaan c', 'c.pekerjaan_id = a.id_pekerjaan', 'left');
    $this->db->join('global.global_bagian_detail d ', 'd.id_pegawai = a.id_create_awal', 'left');
    $this->db->join('global.global_bagian e', 'e.bagian_id=id_bagian', 'left');
    $this->db->join('global.global_pegawai f', 'f.pegawai_nik = d.id_pegawai', 'left');
    $this->db->join('global.global_bidang j', 'a.id_bidang = j.bidang_id', 'left');
    $this->db->join('global.global_urutan_proyek k', 'a.id_urutan_proyek = k.urutan_proyek_id', 'left');
    $this->db->join('global.global_section_area l', 'a.id_section_area = l.section_area_id', 'left');

    if (isset($data['id_pekerjaan'])) $this->db->where('a.id_pekerjaan', $data['id_pekerjaan']);
    $this->db->where('pekerjaan_dokumen_awal', 'n');
    $this->db->where("(is_lama != 'y' or is_lama is null)");
    if (isset($data['pekerjaan_disposisi_status'])) $this->db->where('pekerjaan_status', $data['pekerjaan_disposisi_status']);
    if (isset($data['id_create_awal'])) $this->db->where('id_create_awal', $data['id_create_awal']);
    if (isset($data['is_hps'])) $this->db->where('is_hps', $data['is_hps']);
    if (isset($data['is_hps_cc'])) $this->db->where('is_hps', $data['is_hps_cc']);
    $this->db->where("(is_update_ifa !='y' OR is_update_ifa IS NULL)");
    if (isset($data['pekerjaan_dokumen_status_min'])) $this->db->where("pekerjaan_dokumen_status >= '" . $data['pekerjaan_dokumen_status_min'] . "'");
    if (isset($data['pekerjaan_dokumen_status_max'])) $this->db->where("pekerjaan_dokumen_status <= '" . $data['pekerjaan_dokumen_status_max'] . "'");

    $this->db->order_by('pekerjaan_dokumen_waktu_input', 'desc');


    $sql = $this->db->get();

    return (isset($data['pekerjaan_dokumen_id'])) ? $sql->row_array() : $sql->result_array();
  }

  public function getAsetDocumentUploadIFC($data = null)
  {
    $this->db->select("*,to_char(pekerjaan_dokumen_waktu_input, 'DD-MM-YYYY HH24:MI:SS') as tanggal_dokumen_input");
    $this->db->from('dec.dec_pekerjaan_dokumen a');
    $this->db->join('dec.dec_pekerjaan_template b', 'a.id_pekerjaan_template = b.pekerjaan_template_id', 'left');
    $this->db->join('dec.dec_pekerjaan c', 'c.pekerjaan_id = a.id_pekerjaan', 'left');
    $this->db->join('global.global_bagian_detail d ', 'd.id_pegawai = a.id_create_awal', 'left');
    $this->db->join('global.global_bagian e', 'e.bagian_id=id_bagian', 'left');
    $this->db->join('global.global_pegawai f', 'f.pegawai_nik = d.id_pegawai', 'left');
    $this->db->join('global.global_bidang j', 'a.id_bidang = j.bidang_id', 'left');
    $this->db->join('global.global_urutan_proyek k', 'a.id_urutan_proyek = k.urutan_proyek_id', 'left');
    $this->db->join('global.global_section_area l', 'a.id_section_area = l.section_area_id', 'left');

    $this->db->where('pekerjaan_dokumen_awal', 'n');
    $this->db->where("(is_update_ifa !='y' OR is_update_ifa IS NULL)");
    // $this->db->where("is_lama ='n'");
    $this->db->where("(is_lama != 'y' or is_lama is null)");
    if (isset($data['id_pekerjaan'])) $this->db->where('a.id_pekerjaan', $data['id_pekerjaan']);
    if (isset($data['is_hps'])) $this->db->where('is_hps', $data['is_hps']);
    if (isset($data['pekerjaan_dokumen_status_min'])) $this->db->where("pekerjaan_dokumen_status >= '" . $data['pekerjaan_dokumen_status_min'] . "'");
    if (isset($data['pekerjaan_dokumen_status_max'])) $this->db->where("pekerjaan_dokumen_status <= '" . $data['pekerjaan_dokumen_status_max'] . "'");
    if (isset($data['id_create_awal'])) $this->db->where('id_create_awal', $data['id_create_awal']);
    $sql = $this->db->get();

    return (isset($data['pekerjaan_dokumen_id'])) ? $sql->row_array() : $sql->result_array();



    $sql = $this->db->get();

    return (isset($data['pekerjaan_dokumen_id'])) ? $sql->row_array() : $sql->result_array();
  }

  public function getDocumentUploadTransmital($data = null)
  {
    $this->db->select("*,to_char(pekerjaan_dokumen_waktu_input, 'DD-MM-YYYY HH24:MI:SS') as tanggal_dokumen_input");
    $this->db->from('dec.dec_pekerjaan_dokumen a');
    $this->db->join('dec.dec_pekerjaan_template b', 'a.id_pekerjaan_template = b.pekerjaan_template_id', 'left');
    $this->db->join('dec.dec_pekerjaan c', 'c.pekerjaan_id = a.id_pekerjaan', 'left');
    $this->db->join('global.global_bagian_detail d ', 'd.id_pegawai = a.id_create_awal', 'left');
    $this->db->join('global.global_bagian e', 'e.bagian_id=id_bagian', 'left');
    $this->db->join('global.global_pegawai f', 'f.pegawai_nik = d.id_pegawai', 'left');
    $this->db->join('global.global_bidang j', 'a.id_bidang = j.bidang_id', 'left');
    $this->db->join('global.global_urutan_proyek k', 'a.id_urutan_proyek = k.urutan_proyek_id', 'left');
    $this->db->join('global.global_section_area l', 'a.id_section_area = l.section_area_id', 'left');

    if (isset($data['id_pekerjaan'])) $this->db->where('a.id_pekerjaan', $data['id_pekerjaan']);
    if (isset($data['id_bagian'])) $this->db->where('e.bagian_id', $data['id_bagian']);
    $this->db->where('pekerjaan_dokumen_awal', 'n');
    $this->db->where("(is_lama != 'y' or is_lama is null)");
    if (isset($data['pekerjaan_disposisi_status'])) $this->db->where('pekerjaan_status', $data['pekerjaan_disposisi_status']);
    if (isset($data['id_create_awal'])) $this->db->where('id_create_awal', $data['id_create_awal']);
    // if (isset($data['is_hps'])) $this->db->where('is_hps', $data['is_hps']);
    $this->db->where("(is_hps = '" . $data['is_hps'] . "' OR is_transmital = '" . $data['is_transmital'] . "')");
    if (isset($data['is_hps_cc'])) $this->db->where('is_hps', $data['is_hps_cc']);
    $this->db->where("(is_update_ifa IS NULL)");
    if (isset($data['pekerjaan_dokumen_status_min'])) $this->db->where("CAST(pekerjaan_dokumen_status AS INT) >= '" . $data['pekerjaan_dokumen_status_min'] . "'");
    if (isset($data['pekerjaan_dokumen_status_max'])) $this->db->where("CAST(pekerjaan_dokumen_status AS INT) <= '" . $data['pekerjaan_dokumen_status_max'] . "'");

    $this->db->order_by('pekerjaan_dokumen_waktu_input', 'desc');


    $sql = $this->db->get();

    return (isset($data['pekerjaan_dokumen_id'])) ? $sql->row_array() : $sql->result_array();
  }

  public function getAsetDocumentTransmital($data = null)
  {
    $this->db->select("*,to_char(pekerjaan_dokumen_waktu_input, 'DD-MM-YYYY HH24:MI:SS') as tanggal_dokumen_input");
    $this->db->from('dec.dec_pekerjaan_dokumen a');
    $this->db->join('dec.dec_pekerjaan_template b', 'a.id_pekerjaan_template = b.pekerjaan_template_id', 'left');
    $this->db->join('dec.dec_pekerjaan c', 'c.pekerjaan_id = a.id_pekerjaan', 'left');
    $this->db->join('global.global_bagian_detail d ', 'd.id_pegawai = a.id_create_awal', 'left');
    $this->db->join('global.global_bagian e', 'e.bagian_id=id_bagian', 'left');
    $this->db->join('global.global_pegawai f', 'f.pegawai_nik = d.id_pegawai', 'left');
    $this->db->join('global.global_bidang j', 'a.id_bidang = j.bidang_id', 'left');
    $this->db->join('global.global_urutan_proyek k', 'a.id_urutan_proyek = k.urutan_proyek_id', 'left');
    $this->db->join('global.global_section_area l', 'a.id_section_area = l.section_area_id', 'left');

    if (isset($data['id_pekerjaan'])) $this->db->where('a.id_pekerjaan', $data['id_pekerjaan']);
    $this->db->where('pekerjaan_dokumen_awal', 'n');
    $this->db->where("(is_lama != 'y' or is_lama is null)");
    if (isset($data['pekerjaan_disposisi_status'])) $this->db->where('pekerjaan_status', $data['pekerjaan_disposisi_status']);
    if (isset($data['id_create_awal'])) $this->db->where('id_create_awal', $data['id_create_awal']);
    if (isset($data['is_hps'])) $this->db->where('is_hps', $data['is_hps']);
    if (isset($data['is_hps_cc'])) $this->db->where('is_hps', $data['is_hps_cc']);
    $this->db->where("(is_update_ifa IS NULL)");
    if (isset($data['pekerjaan_dokumen_status_min'])) $this->db->where("CAST(pekerjaan_dokumen_status AS INT) >= '" . $data['pekerjaan_dokumen_status_min'] . "'");

    $this->db->order_by('pekerjaan_dokumen_waktu_input', 'desc');


    $sql = $this->db->get();

    return (isset($data['pekerjaan_dokumen_id'])) ? $sql->row_array() : $sql->result_array();
  }



  public function getAsetDocumentIFC($data = null)
  {
    // if (isset($data['id_pekerjaan'])) {

    $this->db->select("*,case
    when pekerjaan_dokumen_status = 'b' then 'Pengajuan Baru'
    when pekerjaan_dokumen_status = 'y' then 'Approval'
    when pekerjaan_dokumen_status = 'c' then 'Approval'
    when pekerjaan_dokumen_status = 'n' then 'Reject'
    end as pekerjaan_dokumen_status_nama");
    $this->db->from('dec.dec_pekerjaan_dokumen a');
    $this->db->join('dec.dec_pekerjaan_template b', 'a.id_pekerjaan_template = b.pekerjaan_template_id', 'left');
    $this->db->join('dec.dec_pekerjaan c', 'c.pekerjaan_id = a.id_pekerjaan', 'left');
    $this->db->join('global.global_bagian_detail d ', 'd.id_pegawai = a.id_create_awal', 'left');
    $this->db->join('global.global_bagian e', 'e.bagian_id=id_bagian', 'left');
    $this->db->join('global.global_pegawai f', 'f.pegawai_nik = d.id_pegawai', 'left');


    // $this->db->join('dec.dec_pekerjaan_disposisi d', 'd.id_pekerjaan = c.pekerjaan_id', 'left');

    // $this->db->where('id_pekerjaan', $data['id_pekerjaan']);
    if (isset($data['id_pekerjaan'])) $this->db->where('a.id_pekerjaan', $data['id_pekerjaan']);
    // if(isset($data['is_lama'])) $this->db->where('is_lama',$data['is_lama']);
    // if(isset($data['pekerjaan_dokumen_status'])) $this->db->where('pekerjaan_dokumen_status',$data['pekerjaan_dokumen_status']);
    $this->db->where('pekerjaan_dokumen_awal', 'n');
    // $this->db->where("(pekerjaan_dokumen_status >= '5')");
    // $this->db->where("(is_lama != 'y' or is_lama is null)");
    if (isset($data['id_pekerjaan'])) $this->db->where('pekerjaan_status', $data['pekerjaan_disposisi_status']);
    if (isset($data['id_create'])) $this->db->where('id_create', $data['id_create']);
    if (isset($data['is_hps'])) {
      $this->db->where('is_hps', $data['is_hps']);
      // } else {
      //   $this->db->where('is_hps', $data['is_hps']);
    }

    $sql = $this->db->get();

    return (isset($data['pekerjaan_dokumen_id'])) ? $sql->row_array() : $sql->result_array();
    // } else {
    // return null;
    // }
  }

  public function getAsetDocumentUsulanBaru($data = null)
  {
    // if (isset($data['id_pekerjaan'])) {

    $this->db->select("a.*,case
    when pekerjaan_dokumen_status = 'b' then 'Pengajuan Baru'
    when pekerjaan_dokumen_status = 'y' then 'Approval'
    when pekerjaan_dokumen_status = 'c' then 'Approval VP'
    when pekerjaan_dokumen_status = 'n' then 'Reject'
    end as pekerjaan_dokumen_status_nama");
    $this->db->from('dec.dec_pekerjaan_dokumen a');
    // $this->db->where('id_pekerjaan', $data['id_pekerjaan']);
    if (isset($data['id_pekerjaan'])) $this->db->where('id_pekerjaan', $data['id_pekerjaan']);
    // if(isset($data['is_lama'])) $this->db->where('is_lama',$data['is_lama']);
    // if(isset($data['pekerjaan_dokumen_status'])) $this->db->where('pekerjaan_dokumen_status',$data['pekerjaan_dokumen_status']);
    $this->db->where('pekerjaan_dokumen_awal', 'n');
    $this->db->where("(is_lama != 'y' or is_lama is null)");
    // $this->db->where('pekerjaan_dokumen_status',$data['pekerjaan_dokumen_status']);

    $sql = $this->db->get();

    return (isset($data['pekerjaan_dokumen_id'])) ? $sql->row_array() : $sql->result_array();
    // } else {
    // return null;
    // }
  }




  public function getAsetDocumentApproveAVP($data = null)
  {
    $this->db->select("b.*,a.*,
    case
    when b.pekerjaan_dokumen_status = 'b' then 'Pengajuan Baru'
    when b.pekerjaan_dokumen_status = 'y' then 'Approval AVP'
    when b.pekerjaan_dokumen_status = 'n' then 'Reject'
    end as pekerjaan_dokumen_status_nama ");
    // $this->db->select('*');
    $this->db->from('dec.dec_pekerjaan a');
    $this->db->join('dec.dec_pekerjaan_dokumen b', 'b.id_pekerjaan = a.pekerjaan_id', 'left');
    // $this->db->join('dec.dec_pekerjaan_disposisi c','c.id_pekerjaan = a.pekerjaan_id','left');
    // $this->db->join('global.global_pegawai d','d.pegawai_nik = c.id_user','left');
    $this->db->where("(is_lama != 'y' or is_lama is null)");
    // $this->db->or_where("a.is_lama",null);

    if ($data['id_pekerjaan']) $this->db->where('a.pekerjaan_id', $data['id_pekerjaan']);
    // if(isset($data['pekerjaan_disposisi_status'])) $this->db->where('pekerjaan_disposisi_status',$data['pekerjaan_disposisi_status']);

    $sql = $this->db->get();

    return $sql->result_array();
  }

  public function getAsetDocumentApproveVP($data = null)
  {
    $this->db->select("b.*,a.*,
    case
    when pekerjaan_dokumen_status = 'b' then 'Pengajuan Baru'
    when pekerjaan_dokumen_status = 'y' then 'Approval AVP'
    when pekerjaan_dokumen_status = 'n' then 'Reject'
    when pekerjaan_dokumen_status = 'c' then 'Approval VP'
    end as pekerjaan_dokumen_status_nama ");
    $this->db->from('dec.dec_pekerjaan_dokumen a');
    $this->db->join('dec.dec_pekerjaan b', 'a.id_pekerjaan = b.pekerjaan_id', 'left');
    // $this->db->join('dec.dec_pekerjaan_disposisi c','a.id_pekerjaan = c.id_pekerjaan','left');
    // $this->db->join('global.global_pegawai d','d.pegawai_nik = c.id_user','left');
    $this->db->where("(is_lama != 'y' or is_lama is null)");
    $this->db->where("pekerjaan_dokumen_status != 'b'");
    // $this->db->or_where("a.is_lama",null);
    // $

    if ($data['id_pekerjaan']) $this->db->where('a.id_pekerjaan', $data['id_pekerjaan']);

    $sql = $this->db->get();

    return $sql->result_array();
  }

  public function getApproveVP($data = null)
  {
    $this->db->select('*');
    $this->db->from('dec.dec_pekerjaan_dokumen');

    // if(isset($data['pekerjaan_dokumen_id'])) $this->db->where()

    $sql = $this->db->get();

    return (isset($data['pekerjaan_dokumen_id'])) ? $sql->row_array() : $sql->result_array();
  }

  public function getVP($data = null)
  {
    $this->db->select('*');
    $this->db->from('dec.dec_pekerjaan a');
    $this->db->join('dec.dec_pekerjaan_disposisi b', 'a.pekerjaan_id = b.id_pekerjaan', 'left');
    $this->db->join('global.global_pegawai c', 'c.pegawai_nik = b.id_user', 'left');
    if (isset($data['pekerjaan_id'])) $this->db->where('b.id_pekerjaan', $data['pekerjaan_id']);
    if (isset($data['id_tanggung_jawab'])) $this->db->where('id_penanggung_jawab', $data['id_tanggung_jawab']);
    $this->db->where('pekerjaan_disposisi_status', '4');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function getVPAVP($data = null)
  {
    $this->db->select('*');
    $this->db->from('dec.dec_pekerjaan a');
    $this->db->join('dec.dec_pekerjaan_disposisi b', 'a.pekerjaan_id = b.id_pekerjaan AND a.pekerjaan_status = b.pekerjaan_disposisi_status', 'left');
    $this->db->join('global.global_pegawai c', 'c.pegawai_nik = b.id_user', 'left');
    $this->db->join('global.global_klasifikasi_pekerjaan d', 'd.klasifikasi_pekerjaan_id = a.id_klasifikasi_pekerjaan', 'left');


    if (isset($data['pekerjaan_id'])) $this->db->where('b.id_pekerjaan', $data['pekerjaan_id']);
    if (isset($data['pegawai_nik'])) $this->db->where('b.id_user', $data['pegawai_nik']);
    if (isset($data['id_tanggung_jawab'])) $this->db->where('b.id_penanggung_jawab', $data['id_tanggung_jawab']);


    $this->db->where('b.is_aktif', 'y');
    $sql = $this->db->get();

    return $sql->result_array();
  }

  /* user list AVP */
  public function getAVP($data = null)
  {
    $this->db->select('*');
    $this->db->from('global.global_pegawai a');
    $this->db->join('global.global_bagian_detail b', 'b.id_pegawai = a.pegawai_nik', 'left');
    $this->db->join('global.global_bagian c', 'c.bagian_id = b.id_bagian', 'left');
    $this->db->join('dec.dec_pekerjaan_disposisi d', 'd.id_user = a.pegawai_nik', 'left');

    if (isset($data['pegawai_poscode'])) {
      $this->db->where("(pegawai_direct_superior = '$data[pegawai_poscode]' OR pegawai_poscode = '$data[pegawai_poscode]' OR pegawai_direct_superior = 'E53000000')");
    }

    if (isset($data['pekerjaan_id'])) $this->db->where('d.id_pekerjaan', $data['pekerjaan_id']);
    if (isset($data['pekerjaan_disposisi_status'])) $this->db->where('d.pekerjaan_disposisi_status', $data['pekerjaan_disposisi_status']);

    if (isset($data['bagian_id'])) $this->db->where("c.bagian_id = '$data[bagian_id]'");
    if (isset($data['pegawai_nama'])) $this->db->where("upper(pegawai_nama) LIKE '%" . strtoupper($data['pegawai_nama']) . "%'");
    if (isset($data['bagian_nama'])) $this->db->like('bagian_nama', $data['bagian_nama'], 'both');
    // $this->db->where("UPPER(pegawai_jabatan) LIKE '%".(strtoupper('3'))."%'");

    if (isset($data['pegawai_poscode'])) {
      $this->db->where("(LEFT(pegawai_jabatan,1) != '3' OR pegawai_poscode = '$data[pegawai_poscode]') ");
    }
    $this->db->order_by('pegawai_postitle', 'asc');
    $this->db->order_by('pegawai_nama', 'asc');

    $sql = $this->db->get();

    return $sql->result_array();
  }
  /* user list AVP */

  public function getVPAVPLangsung($data = null)
  {
    $this->db->select('*');
    $this->db->from('dec.dec_pekerjaan a');
    $this->db->join('dec.dec_pekerjaan_disposisi b', 'a.pekerjaan_id = b.id_pekerjaan', 'left');
    $this->db->join('global.global_pegawai c', 'c.pegawai_nik = b.id_user', 'left');
    $this->db->join('global.global_klasifikasi_pekerjaan d', 'd.klasifikasi_pekerjaan_id = a.id_klasifikasi_pekerjaan', 'left');


    if (isset($data['pekerjaan_id'])) $this->db->where('b.id_pekerjaan', $data['pekerjaan_id']);
    if (isset($data['pegawai_nik'])) $this->db->where('b.id_user', $data['pegawai_nik']);
    if (isset($data['id_tanggung_jawab'])) $this->db->where('b.id_penanggung_jawab', $data['id_tanggung_jawab']);
    if (isset($data['pekerjaan_disposisi_status'])) $this->db->where('pekerjaan_disposisi_status', $data['pekerjaan_disposisi_status']);
    $this->db->where('b.is_aktif', 'y');
    $sql = $this->db->get();

    return $sql->result_array();
  }

  public function getUserStafVP($data = null)
  {
    $this->db->select('b.id_user,pegawai_nama,pekerjaan_id,pegawai_postitle,pegawai_nama_dep,b.cc_awal');
    $this->db->from('dec.dec_pekerjaan a');
    $this->db->join('dec.dec_pekerjaan_disposisi b', 'a.pekerjaan_id = b.id_pekerjaan', 'left');
    $this->db->join('global.global_pegawai c', 'c.pegawai_nik = b.id_user', 'left');
    if (isset($data['pekerjaan_id'])) $this->db->where('a.pekerjaan_id', $data['pekerjaan_id']);
    if (isset($data['pegawai_nik'])) $this->db->where('b.id_user', $data['pegawai_nik']);
    if (isset($data['pegawai_unit_id'])) $this->db->where('c.pegawai_unit_id', $data['pegawai_unit_id']);
    $this->db->where('pekerjaan_disposisi_status', '8');

    if (isset($data['is_cc'])) $this->db->where('b.is_cc', $data['is_cc']);
    // if (isset($data['id_tanggung_jawab'])) $this->db->where('b.id_penanggung_jawab', $data['id_tanggung_jawab']);
    $this->db->where('b.is_aktif', 'y');
    $this->db->group_by('pegawai_nama,a.pekerjaan_id,c.pegawai_nik,b.id_user,cc_awal');
    $sql = $this->db->get();

    return $sql->result_array();
  }

  public function getUserKoor($data = null)
  {
    $this->db->select('*');
    $this->db->from('dec.dec_pekerjaan_disposisi a');
    $this->db->join('global.global_bagian_detail b', 'a.id_user = b.id_pegawai', 'left');
    $this->db->join('global.global_bagian c', 'c.bagian_id = b.id_bagian', 'left');


    if (isset($data['pekerjaan_id'])) $this->db->where('id_pekerjaan', $data['pekerjaan_id']);
    if (isset($data['pekerjaan_disposisi_status'])) $this->db->where('pekerjaan_disposisi_status', $data['pekerjaan_disposisi_status']);
    if (isset($data['id_user'])) $this->db->where('id_user', $data['id_user']);
    if (isset($data['bagian_id'])) $this->db->where('bagian_id', $data['bagian_id']);

    $query = $this->db->get();

    return $query->row_array();
  }

  public function getUserKoorKhusus($data = null)
  {
    $this->db->select('*');
    $this->db->from('dec.dec_pekerjaan_disposisi a');

    if (isset($data['pekerjaan_id'])) $this->db->where('id_pekerjaan', $data['pekerjaan_id']);
    if (isset($data['pekerjaan_disposisi_status'])) $this->db->where('pekerjaan_disposisi_status', $data['pekerjaan_disposisi_status']);
    if (isset($data['id_user'])) $this->db->where('id_user', $data['id_user']);
    // if (isset($data['bagian_id'])) $this->db->where('bagian_id', $data['bagian_id']);

    $query = $this->db->get();

    return $query->row_array();
  }

  public function getBagianSession($data = null)
  {
    $this->db->select('*');
    $this->db->from('global.global_bagian a');
    $this->db->join('global.global_bagian_detail b', 'a.bagian_id=b.id_bagian', 'left');
    $this->db->join('global.global_pegawai c', 'c.pegawai_nik=b.id_pegawai', 'left');

    // if (isset($data['pegawai_direct_superior'])) $this->db->where('pegawai_poscode', $data['pegawai_direct_superior']);
    if (isset($data['id_user'])) $this->db->where('id_pegawai', $data['id_user']);

    $sql = $this->db->get();

    return $sql->row_array();
  }

  function getUserListRevApp($data = null)
  {
    $this->db->select('*');
    $this->db->from('global.global_pegawai');
    if (isset($data['pegawai_nama'])) $this->db->where("upper(pegawai_nama) LIKE '%" . strtoupper($data['pegawai_nama']) . "%'");
    $this->db->order_by('pegawai_nama', 'asc');

    $sql = $this->db->get();

    return $sql->result_array();
  }

  function getUserListRevApp2($data = null)
  {
    $this->db->select('*');
    $this->db->from('global.global_pegawai a');
    $this->db->join('global.global_klasifikasi_dokumen b', 'b.id_pegawai = a.pegawai_nik', 'left');
    if (isset($data['pegawai_poscode'])) $this->db->where("pegawai_poscode", $data['pegawai_poscode']);
    if (isset($data['pegawai_nik'])) $this->db->where("pegawai_nik", $data['pegawai_nik']);
    $this->db->order_by('pegawai_nama', 'asc');

    $sql = $this->db->get();

    return $sql->row_array();
  }

  /* GET */

  /* INSERT */





  /* INSERT */

  /* UPDATE */

  public function updateStatus($where = null, $param = null)
  {
    if (isset($where['id_user'])) $this->db->where('id_user', $where['id_user']);
    if (isset($where['id_pekerjaan'])) $this->db->where('id_pekerjaan', $where['id_pekerjaan']);
    if (isset($where['disposisi_status'])) $this->db->where('pekerjaan_disposisi_status', $where['disposisi_status']);
    $this->db->update('dec.dec_pekerjaan_disposisi', $param);
    return $this->db->affected_rows();
  }

  public function updateStatusProses($where_id_user = null, $where_id_pekerjaan = null, $where_disposisi_status = null, $data = null)
  {
    $this->db->set($data);
    if (isset($where_id_user)) $this->db->where('id_user', $where_id_user);
    if (isset($where_id_pekerjaan)) $this->db->where('id_pekerjaan', $where_id_pekerjaan);
    if (isset($where_disposisi_status)) $this->db->where('pekerjaan_disposisi_status', $where_disposisi_status);
    $this->db->update('dec.dec_pekerjaan_disposisi');

    return $this->db->affected_rows();
  }

  public function updateStatusTransmital($where = '', $param = '')
  {
    if (isset($where['id_user'])) $this->db->where('id_user', $where['id_user']);
    if (isset($where['id_pekerjaan'])) $this->db->where('id_pekerjaan', $where['id_pekerjaan']);
    if (isset($where['disposisi_status'])) $this->db->where('pekerjaan_disposisi_transmital_status', $where['disposisi_status']);
    $this->db->update('dec.dec_pekerjaan_disposisi_transmital', $param);
    return $this->db->affected_rows();
  }

  public function updatePekerjaanDisposisi($data, $id, $nik = null, $id_tanggung_jawab = null, $pekerjaan_disposisi_status)
  {
    $this->db->set($data);
    $this->db->where('id_pekerjaan', $id);
    if ($nik != null) $this->db->where('id_user', $nik);
    if ($id_tanggung_jawab != null) $this->db->where('id_penanggung_jawab', $id_tanggung_jawab);
    if ($pekerjaan_disposisi_status != null) $this->db->where('pekerjaan_disposisi_status', $pekerjaan_disposisi_status);
    $this->db->update('dec.dec_pekerjaan_disposisi');

    return $this->db->affected_rows();
  }

  public function updatePekerjaanDisposisiReject($data_disposisi = null, $pekerjaan_id = null, $user_id = null, $tanggung_jawab = null, $disposisi_status = null)
  {
    if (isset($disposisi_status)) $this->db->where('pekerjaan_disposisi_status', $disposisi_status);
    if (isset($tanggung_jawab)) $this->db->where('id_penanggung_jawab', $tanggung_jawab);
    if (isset($pekerjaan_id)) $this->db->where('id_pekerjaan', $pekerjaan_id);
    if (isset($user_id)) $this->db->where('id_user', $user_id);
    $this->db->update('dec.dec_pekerjaan_disposisi', $data_disposisi);
    return $this->db->affected_rows();
  }



  public function updateAsetDocument2($data, $id)
  {
    $this->db->set($data);
    $this->db->where('id_pekerjaan', $id);
    $this->db->update('dec.dec_pekerjaan_dokumen');

    return $this->db->affected_rows();
  }

  public function updateExtend($data, $id)
  {
    $this->db->set($data);
    $this->db->where('pekerjaan_id', $id);
    $this->db->update('dec.dec_pekerjaan');

    return $this->db->affected_rows();
  }
  /* UPDATE */

  /* DELETE */




  public function deleteAsetDocument2($id)
  {
    $this->db->where('id_pekerjaan', $id);
    $this->db->delete('dec.dec_pekerjaan_dokumen');

    return $this->db->affected_rows();
  }
  /* DELETE */

  /* GET DOWNLOAD */
  public function getAsetDownload($data = null)
  {
    $this->db->select('*');
    $this->db->from('dec.dec_pekerjaan_dokumen a');
    if (isset($data['id_pekerjaan'])) $this->db->where('id_pekerjaan', $data['id_pekerjaan']);
    $sql = $this->db->get();

    return $sql->result_array();
  }
  /* GET DOWNLOAD */

  /* LAIN */
  public function cekRevisi($data = null)
  {
    $this->db->select('COUNT(pekerjaan_dokumen_id) AS jumlah_revisi, pic');
    $this->db->from('dec.dec_pekerjaan_dokumen a');
    $this->db->join('dec.dec_pekerjaan b', 'b.pekerjaan_id = a.id_pekerjaan', 'left');
    $this->db->where('is_lama', 'n');
    $this->db->where('pekerjaan_dokumen_status', '0');
    if (isset($data['pekerjaan_id'])) $this->db->where('id_pekerjaan', $data['pekerjaan_id']);
    if (isset($data['pic'])) $this->db->where('pic', $data['pic']);
    $this->db->group_by('pic');
    $sql = $this->db->get();
    return $sql->row_array();
  }

  // public function cekReviewUrut($data = null)
  // {
  //   $this->db->select('pekerjaan_dokumen_status_review');
  //   $this->db->from('dec.dec_pekerjaan_dokumen');
  //   $this->db->where('pekerjaan_dokumen_id', $data);

  //   $sql = $this->db->get();
  //   return $sql->row_array();
  // }

  // public function updateDokumenNomor($data = null, $id)
  // {
  //   $this->db->where('id_pekerjaan', $id);
  //   $this->db->update('dec.dec_pekerjaan_dokumen', $data);
  //   return $this->db->affected_rows();
  // }
  /* LAIN */

  public function getProgressPekerjaan($data = null)
  {
    $this->db->select('*');
    $this->db->from('dec.dec_pekerjaan_progress a');
    if (isset($data['id_pekerjaan'])) $this->db->where('id_pekerjaan', $data['id_pekerjaan']);
    if (isset($data['id_user'])) $this->db->where('id_user', $data['id_user']);
    $sql = $this->db->get();
    return (isset($data['id_pekerjaan']) && isset($data['id_user'])) ? $sql->row_array() : $sql->result_array();
  }

  public function insertProgress($data = null)
  {
    $this->db->insert('dec.dec_pekerjaan_progress', $data);
    return $this->db->affected_rows();
  }

  public function updateProgress($id, $data = null)
  {
    $this->db->where('progress_id', $id);
    $this->db->update('dec.dec_pekerjaan_progress', $data);
    return $this->db->affected_rows();
  }

  // EXTEND

  public function getIdDisposisi($data = null)
  {
    $this->db->select('*');
    $this->db->from('dec.dec_pekerjaan_disposisi a');
    if (isset($data['pekerjaan_status'])) $this->db->where('pekerjaan_disposisi_status', $data['pekerjaan_status']);
    if (isset($data['id_user'])) $this->db->where('id_user', $data['id_user']);
    if (isset($data['id_pekerjaan'])) $this->db->where('id_pekerjaan', $data['id_pekerjaan']);

    $sql = $this->db->get();

    if (isset($data['pekerjaan_status']) && isset($data['id_user']) && isset($data['id_pekerjaan'])) {
      return $sql->row_array();
    } else {
      return null;
    }
  }


  public function getExtend($data = null)
  {
    $this->db->select('a.*,b.*');
    $this->db->from('dec.dec_pekerjaan_extend a');
    // $this->db->join('dec.dec_pekerjaan_disposisi b', 'b.pekerjaan_disposisi_id = a.id_pekerjaan_disposisi AND b.id_pekerjaan = a.id_pekerjaan', 'left');
    $this->db->join('dec.dec_pekerjaan b', 'b.pekerjaan_id = a.id_pekerjaan', 'left');
    if (isset($data['pekerjaan_id'])) $this->db->where('b.pekerjaan_id', $data['pekerjaan_id']);
    if (isset($data['pekerjaan_status'])) $this->db->where('pekerjaan_status', $data['pekerjaan_status']);
    if (isset($data['extend_status'])) $this->db->where('extend_status', $data['extend_status']);
    if (isset($data['id_user'])) $this->db->where('a.id_user', $data['id_user']);

    $sql = $this->db->get();

    if (isset($data['pekerjaan_id']) && isset($data['pekerjaan_status'])) {
      return $sql->row_array();
    } else {
      return $sql->result_array();
    }
  }

  public function insertExtend($data = null)
  {
    $this->db->insert('dec.dec_pekerjaan_extend', $data);
    return $this->db->affected_rows();
  }

  public function updateExtendBaru($id, $data = null)
  {
    $this->db->where('extend_id', $id);
    $this->db->update('dec.dec_pekerjaan_extend', $data);
    return $this->db->affected_rows();
  }


  // public function updateWaktuDisposisi($id = null, $data = null)
  // {
  //   $this->db->where('pekerjaan_disposisi_id', $id);
  //   $this->db->update('dec.dec_pekerjaan_disposisi', $data);
  //   return $this->db->affected_rows();
  // }

  // EXTEND

  // START REMINDER
  public function getReminder($data = null)
  {
    $this->db->select('*');
    $this->db->from('dec.dec_pekerjaan_extend a');
    $this->db->join('dec.dec_pekerjaan b', 'b.pekerjaan_id = a.id_pekerjaan', 'left');
    if (isset($data['id_user'])) $this->db->where('a.id_user', $data['id_user']);
    $this->db->where('pekerjaan_status', '8');

    $sql = $this->db->get();

    return $sql->result_array();
  }

  public function updateStatusDokumen($where_id_pekerjaan_dokumen, $where_id_user_dokumen, $where_status_dokumen, $param_user_dokumen = null)
  {
    if (!empty($where_id_pekerjaan_dokumen)) $this->db->where('id_pekerjaan', $where_id_pekerjaan_dokumen);
    if (!empty($where_id_user_dokumen)) $this->db->where('id_create', $where_id_user_dokumen);
    if (!empty($where_status_dokumen)) $this->db->where('pekerjaan_dokumen_status', $where_status_dokumen);
    $this->db->update('dec.dec_pekerjaan_dokumen', $param_user_dokumen);
    return $this->db->affected_rows();
  }

  public function updateStatusDokumenIFC($where, $param)
  {
    if (!empty($where['id_pekerjaan'])) $this->db->where('id_pekerjaan', $where['id_pekerjaan']);
    if (!empty($where['pekerjaan_dokumen_id'])) $this->db->where('pekerjaan_dokumen_id', $where['pekerjaan_dokumen_id']);
    $this->db->update('dec.dec_pekerjaan_dokumen', $param);

    return $this->db->affected_rows();
  }

  // public function updateStatusDokumenIFC($where_id_pekerjaan_dokumen = null, $where_id_user_dokumen = null, $where_status_dokumen = null, $param_user_dokumen = null)
  // {
  //   if (!empty($where_id_pekerjaan_dokumen)) $this->db->where('id_pekerjaan', $where_id_pekerjaan_dokumen);
  //   // if (!empty($where_id_user_dokumen)) $this->db->where('id_create', $where_id_user_dokumen);
  //   if (!empty($where_id_user_dokumen)) $this->db->where("id_create = '" . $where_id_user_dokumen . "' OR id_create_awal = '" . $where_id_user_dokumen . "'");
  //   $this->db->where('is_lama', 'n');
  //   if (!empty($where_status_dokumen)) $this->db->where('pekerjaan_dokumen_status', $where_status_dokumen);
  //   $this->db->update('dec.dec_pekerjaan_dokumen', $param_user_dokumen);
  //   return $this->db->affected_rows();
  // }

  public function updateStatusDokumenIFCAll($where, $param)
  {
    $this->db->query("INSERT INTO dec.dec_pekerjaan_dokumen SELECT '" . $param['pekerjaan_dokumen_id'] . "',id_pekerjaan,pekerjaan_dokumen_file,id_pekerjaan_disposisi,pekerjaan_dokumen_nama,pekerjaan_dokumen_awal,'" . $param['pekerjaan_dokumen_status'] . "',pekerjaan_dokumen_keterangan,who_create,is_lama,id_pekerjaan_template,pekerjaan_dokumen_revisi,pekerjaan_dokumen_status_review,'" . $param['id_create'] . "','" . $param['is_hps'] . "',null,id_create_awal,pekerjaan_dokumen_nomor,pekerjaan_dokumen_cc,is_review
    ,pekerjaan_dokumen_jumlah,pekerjaan_dokumen_jenis,id_bidang,id_urutan_proyek,id_section_area,pekerjaan_dokumen_kertas,pekerjaan_dokumen_orientasi,pekerjaan_dokumen_waktu,is_reject,is_update_ifa,pekerjaan_dokumen_qrcode,pekerjaan_dokumen_waktu_input,id_dokumen_awal,revisi_ifc,'" . $param['pekerjaan_dokumen_waktu_update'] . "' FROM dec.dec_pekerjaan_dokumen WHERE pekerjaan_dokumen_id = '" . $where['pekerjaan_dokumen_id'] . "'");

    return $this->db->affected_rows();
  }

  public function updateProgressIFA($where_id_pekerjaan_progress, $where_id_user_progress, $param_user_progress = null)
  {
    if (!empty($where_id_pekerjaan_progress)) $this->db->where('id_pekerjaan', $where_id_pekerjaan_progress);
    if (!empty($where_id_user_progress)) $this->db->where('id_user', $where_id_user_progress);
    $this->db->update('dec.dec_pekerjaan_progress', $param_user_progress);
    return $this->db->affected_rows();
  }

  public function insertProgressIFA($data = null)
  {
    $this->db->insert('dec.dec_pekerjaan_progress', $data);
    return $this->db->affected_rows();
  }
  // STOP REMINDER

  public function getPenomoranDokumen($data = '')
  {
    if (isset($data['id_pekerjaan'])) $this->db->where('id_pekerjaan', $data['id_pekerjaan']);
    $this->db->select('*');
    $this->db->from('dec.dec_pekerjaan_dokumen_penomoran a');
    $this->db->join('global.global_urutan_proyek b', 'b.urutan_proyek_id = a.urutan_proyek_default', 'left');
    $this->db->join('global.global_section_area c', 'c.section_area_id = a.section_area_default', 'left');
    $sql = $this->db->get();
    return (isset($data['single'])) ? $sql->row_array() : $sql->result_array();
  }

  public function insertPenomoranDokumen($data = '')
  {
    $this->db->insert('dec.dec_pekerjaan_dokumen_penomoran', $data);
    return $this->db->affected_rows();
  }

  public function updatePenomoranDokumen($id, $data = '')
  {
    $this->db->where('pekerjaan_dokumen_penomoran_id', $id);
    $this->db->update('dec.dec_pekerjaan_dokumen_penomoran', $data);
    return $this->db->affected_rows();
  }

  /* Transmital */
  public function getPekerjaanTransmitalWaspro($param = '')
  {
    if (isset($param['pekerjaan_id'])) $this->db->where('pekerjaan_id', $param['pekerjaan_id']);
    if (isset($param['pekerjaan_status'])) $this->db->where('pekerjaan_disposisi_transmital_status', $param['pekerjaan_status']);
    if (isset($param['pegawai_nik'])) $this->db->where('b.id_user', $param['pegawai_nik']);

    $this->db->where("pekerjaan_status IN('14','15')");
    $this->db->select('*');
    $this->db->from('dec.dec_pekerjaan a');
    $this->db->join('dec.dec_pekerjaan_disposisi_transmital b', 'a.pekerjaan_id = b.id_pekerjaan', 'left');

    $get = $this->db->get();

    if ($get) {
      return ((isset($param['pekerjaan_id']) && isset($param['pegawai_nik']))) ? $get->row_array() : $get->result_array();
    } else {
      return false;
    }
  }

  public function insertSendTransmital($data = '')
  {
    $this->db->insert('dec.dec_pekerjaan_disposisi_transmital', $data);
    return $this->db->affected_rows();
  }

  public function updateDisposisiTransmital($param = '', $data = '')
  {
    if (isset($param['pekerjaan_disposisi_transmital_status'])) $this->db->where('pekerjaan_disposisi_transmital_status', $param['pekerjaan_disposisi_transmital_status']);
    if (isset($param['id_pekerjaan'])) $this->db->where('id_pekerjaan', $param['id_pekerjaan']);
    if (isset($param['id_user'])) $this->db->where('id_user', $param['id_user']);

    $this->db->update('dec.dec_pekerjaan_disposisi_transmital', $data);
    return $this->db->affected_rows();
  }
  /* Transmital */
}
