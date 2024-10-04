<script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
<style>
	* {
		box-sizing: border-box;
	}

	.row {
		margin-left: -5px;
		margin-right: -5px;
	}

	.column {
		float: left;
		width: 50%;
		padding: 5px;
	}

	/* Clearfix (clear floats) */
	.row::after {
		content: "";
		clear: both;
		display: table;
	}

	table {
		border-collapse: collapse;
		border-spacing: 0;
		width: 100%;
		border: 1px solid #ddd;
	}

	th,
	td {
		text-align: left;
		padding: 16px;
	}

	tr:nth-child(even) {
		background-color: #f2f2f2;
	}
</style>

<a href="<?= base_url() ?>tools_bantuan">Utama</a>
<hr>
<form action="" method="GET">
	<label for="">Status</label>
	<select name="pekerjaan_status" id="pekerjaan_status">
		<option value="">Semua</option>
		<option value="0,1,2,3,4">Usulan</option>
		<option value="5,6,7">Berjalan</option>
		<option value="8">IFA</option>
		<option value="9,10,11">IFC</option>
		<option value="12,13,14,15">Selesai</option>
	</select>
	<label for="">Jenis</label>
	<select name="pekerjaan_jenis" id="pekerjaan_jenis">
		<option <?php if (@$_GET['pekerjaan_jenis'] == '1') echo 'selected' ?> value="1">RKAP</option>
		<option <?php if (@$_GET['pekerjaan_jenis'] == '0') echo 'selected' ?> value="0">Non RKAP</option>
	</select>
	<label for="">Nomor</label>
	<input type="text" name="pekerjaan_nomor" id="pekerjaan_nomor" value="<?= @$_GET['pekerjaan_nomor'] ?>">
	<label for="">Judul</label>
	<input type="text" name="pekerjaan_judul" id="pekerjaan_judul" value="<?= @$_GET['pekerjaan_judul'] ?>">
	<input type="submit">
</form>

<div class="row">
	<div class="column">
		<table border="1">
			<tr>
				<th>PIC</th>
				<th>Nama Pekerjaan</th>
				<th>Status</th>
				<th>Detail</th>
			</tr>
			<?php if (!empty($pekerjaan)) : ?>
				<?php foreach ($pekerjaan as $val) : ?>
					<tr>
						<td><?= $val['pic'] ?></td>
						<td <?php if ($val['pekerjaan_id'] == @$_GET['pekerjaan_id']) echo 'bgcolor="green"' ?>><?= $val['pekerjaan_nomor'] ?> - <?= $val['pekerjaan_judul'] ?></td>
						<td><?= $val['pekerjaan_status'] ?></td>
						<td><a href="<?= base_url() ?>tools_bantuan/tools_pekerjaan?pekerjaan_status=<?= @$_GET['pekerjaan_status'] ?>&pekerjaan_jenis=<?= @$_GET['pekerjaan_jenis'] ?>&pekerjaan_nomor=<?= @$_GET['pekerjaan_nomor'] ?>&pekerjaan_judul=<?= @$_GET['pekerjaan_judul'] ?>&is_lama=n&is_hps=n&pekerjaan_id=<?= $val['pekerjaan_id'] ?>">Detail</a></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</table>
	</div>
	<?php //if($_GET[''])
	?>
	<div class="column">
		<table border="1">
			<tr>
				<th>Disposisi NIK - Pegawai - CC- PJ</th>
				<th>Disposisi Status</th>
				<th>Proses</th>
				<th>Cek</th>
				<th>Hapus</th>
				<!-- <th>Aksi</th> -->
			</tr>
			<?php if (!empty($disposisi)) : ?>
				<?php foreach ($disposisi as $val) : ?>
					<tr>
						<td><?= $val['id_user'] ?> - <?= $val['pegawai_nama'] ?> - <?= $val['is_cc'] ?> - <?= $val['id_penanggung_jawab'] ?></td>
						<td><?= $val['pekerjaan_disposisi_status'] ?></td>
						<td><?= $val['is_proses'] ?></td>
						<td><input type="checkbox" name="disposisi_pilih[]"></td>
						<td><a href="javascript:void(0);" onclick="delete_disposisi('<?= $val['pekerjaan_disposisi_id'] ?>')">Hapus</a></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</table>
	</div>
</div>
<script>
	function delete_disposisi(id) {
		jQuery.get('<?= base_url() ?>tools_bantuan/delete_disposisi', {
			pekerjaan_disposisi_id: id
		}, function(data, textStatus, xhr) {
			location.reload();
		});

	}
</script>