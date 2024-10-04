<style>
	* {
		box-sizing: border-box;
	}

	.row {
		margin-left:-5px;
		margin-right:-5px;
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

th, td {
	text-align: left;
	padding: 16px;
}

tr:nth-child(even) {
	background-color: #f2f2f2;
}
</style>

<form action="" method="GET">
	<input type="text" name="pekerjaan_status" id="pekerjaan_status">
	<input type="submit">
</form>

<div class="row">
	<div class="column">
		<table border="1">
			<tr>
				<th>Nama Pekerjaan</th>
				<th>Status</th>
				<th>Detail</th>
			</tr>
			<?php if(!empty($pekerjaan)): ?>
				<?php foreach($pekerjaan as $val): ?>
					<tr>
						<td <?php if($val['pekerjaan_id']==@$_GET['pekerjaan_id']) echo 'bgcolor="green"' ?> ><?=$val['pekerjaan_nomor']?> - <?=$val['pekerjaan_judul']?></td>
						<td><?=$val['pekerjaan_status']?></td>
						<td><a href="<?=base_url()?>tampilan/reset_pekerjaan?pekerjaan_status=<?php echo @$_GET['pekerjaan_status']?>&pekerjaan_id=<?=$val['pekerjaan_id']?>">Detail</a></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</table>
	</div>
	<div class="column">
		<table border="1">
			<tr>
				<th>Disposisi NIK - Pegawai</th>
				<th>Disposisi Status</th>
				<th>Proses</th>	
				<th>Cek</th>
				<!-- <th>Aksi</th> -->
			</tr>
			<?php if(!empty($disposisi)): ?>
				<?php foreach($disposisi as $val): ?>
					<tr>
						<td><?=$val['id_user']?> - <?=$val['pegawai_nama']?> - <?=$val['is_cc']?></td>
						<td><?=$val['pekerjaan_disposisi_status']?></td>
						<td><?=$val['is_proses']?></td>
						<td><input type="checkbox" name="disposisi_pilih[]"></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</table>
	</div>
</div>