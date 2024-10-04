<form action="<?=base_url('tools_bantuan/import/import_dokumen_upload')?>" method="POST" enctype="multipart/form-data">
	<select name="jenis" id="">
		<option value="Gambar">Gambar</option>
		<option value="Dokumen">Dokumen</option>
	</select>
	<input type="file" name="file" id="file" required>
	<input type="submit" value="Upload">
</form>

<?php if(isset($_GET['import_kode'])){ ?>
	<a href="<?=base_url('tools_bantuan/import/import_dokumen_insert?import_kode='.$_GET['import_kode'])?>">Insert</a>
	<a href="<?=base_url('tools_bantuan/import/import_dokumen_delete?import_kode='.$_GET['import_kode'])?>">Delete</a>
	<table border="1" style="border-collapse: collapse;border: 1px solid black;" width="100%">
		<tr>
			<th>No</th>
			<th>Nama Dokumen</th>
			<th>Bidang</th>
			<th>Template</th>
			<th>UP</th>
			<th>SA</th>
			<th>UD</th>
			<th>Nomor Doc</th>
			<th>Waktu</th>
		</tr>
		<?php
		$dokumen_import = $this->db->get_where('import.import_nomor_dokumen',array('import_kode'=>$_GET['import_kode']))->result_array();
		foreach ($dokumen_import as $key=> $value) { ?>
			
			<tr>
				<td >
					<?=$key+1?>
				</td>
				<td>
					<?=$value['pekerjaan_dokumen_nama']?>
						
				</td>
				<td>
					<?=$value['id_bidang']?>
					
				</td>
				<td>
					<?=$value['id_pekerjaan_template']?>
					
				</td>
				<td>
					<?=$value['id_urutan_proyek']?>
					
				</td>
				<td>
					<?=$value['id_section_area']?>
					
				</td>
				<td>
					<?=$value['id_urutan_dokumen']?>
					
				</td>
				<td>
					<?=$value['nomor_dokumen']?>
					
				</td>
				<td>
					<?=$value['pekerjaan_dokumen_waktu']?>
					
				</td>
			</tr>
		<?php } ?>
	</table>
	<?php } ?>