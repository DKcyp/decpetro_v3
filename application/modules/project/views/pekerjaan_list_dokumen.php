<table align="center" style="width: 80%;border: 1px solid black;border-collapse: collapse;" border="1">
	<thead>
		<tr>
			<th>No</th>
			<th>Nama File</th>
			<?php if ($this->input->get('status') != 'usulan') : ?>
				<th>Bagian</th>
				<th>Status</th>
				<th>Diupload Oleh</th>
				<th>Keterangan</th>
			<?php endif; ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($dokumen as $key => $value) : ?>
			<tr>
				<td><?= $key + 1 ?></td>
				<td><?= $value['pekerjaan_template_nama'] . ' - ' . $value['pekerjaan_dokumen_nama'] ?></td>

				<?php if ($this->input->get('status') != 'usulan') : ?>
					<td><?= $value['bagian_nama'] ?></td>
					<td>
						<?php
						$status = '';
						if ($value['pekerjaan_dokumen_status'] == '0' && $value['revisi_ifc'] == 'y' && ($value['pekerjaan_dokumen_revisi'] != null && $value['pekerjaan_dokumen_revisi'] != '')) {
							$status = 'IFA Rev ' + $value['pekerjaan_dokumen_revisi'] + ' - Revisi';
						} else if ($value['pekerjaan_dokumen_status'] == '0' && ($value['revisi_ifc'] != 'y' && $value['revisi_ifc'] == null) && ($value['pekerjaan_dokumen_revisi'] != null && $value['pekerjaan_dokumen_revisi'] != '')) {
							$status = 'IFA Rev ' + $value['pekerjaan_dokumen_revisi'] + ' - Revisi';
						} else if ($value['pekerjaan_dokumen_status'] == '0') {
							$status = 'Revisi';
						} else if ($value['pekerjaan_dokumen_status'] == '1' && ($value['pekerjaan_dokumen_revisi'] != null && $value['pekerjaan_dokumen_revisi'] != '')) {
							$status = 'IFA Rev ' + $value['pekerjaan_dokumen_revisi'] + ' - Draft';
						} else if ($value['pekerjaan_dokumen_status'] == '1') {
							$status = 'Draft';
						} else if ($value['pekerjaan_dokumen_status'] == '2' && ($value['pekerjaan_dokumen_revisi'] != null && $value['pekerjaan_dokumen_revisi'] != '')) {
							$status = 'IFA Rev ' + $value['pekerjaan_dokumen_revisi'] + ' - IFA – Menunggu Review AVP';
						} else if ($value['pekerjaan_dokumen_status'] == '2') {
							$status = 'IFA – Menunggu Review AVP';
						} else if ($value['pekerjaan_dokumen_status'] == '3' && ($value['pekerjaan_dokumen_revisi'] != null && $value['pekerjaan_dokumen_revisi'] != '')) {
							$status = 'IFA Rev ' + $value['pekerjaan_dokumen_revisi'] + ' - Menunggu Approve VP';
						} else if ($value['pekerjaan_dokumen_status'] == '3') {
							$status = 'IFA - Menunggu Approve VP';
						} else if ($value['pekerjaan_dokumen_status'] == '4' && ($value['pic'] == 'y' || $value['picavp'] == 'y' || $value['picvp'] == 'y') && ($value['pekerjaan_dokumen_revisi'] != null && $value['pekerjaan_dokumen_revisi'] != '')) {
							$status = 'IFA Rev ' + $value['pekerjaan_dokumen_revisi'];
						} else if ($value['pekerjaan_dokumen_status'] == '4' && ($value['pekerjaan_dokumen_revisi'] != null && $value['pekerjaan_dokumen_revisi'] != '')) {
							$status = 'IFA Rev ' + $value['pekerjaan_dokumen_revisi'] + ' - Send User';
						} else if ($value['pekerjaan_dokumen_status'] == '4') {
							$status = 'IFA - Send User';
						} else if ($value['pekerjaan_dokumen_status'] == '5' && ($value['pekerjaan_dokumen_revisi'] != null && $value['pekerjaan_dokumen_revisi'] != '')) {
							$status = 'IFA Rev ' + $value['pekerjaan_dokumen_revisi'];
						} else if ($value['pekerjaan_dokumen_status'] == '5') {
							$status = 'IFA – Menunggu Review AVP User';
						} else if ($value['pekerjaan_dokumen_status'] == '6' && ($value['pekerjaan_dokumen_revisi'] != null && $value['pekerjaan_dokumen_revisi'] != '')) {
							$status = 'IFA Rev ' + $value['pekerjaan_dokumen_revisi'] + ' - Menunggu Approve VP User';
						} else if ($value['pekerjaan_dokumen_status'] == '6') {
							$status = 'IFA - Menunggu Approve VP User';
						} else if ($value['pekerjaan_dokumen_status'] == '7' && ($value['pekerjaan_dokumen_revisi'] != null && $value['pekerjaan_dokumen_revisi'] != '')) {
							$status = 'IFA Rev ' + $value['pekerjaan_dokumen_revisi'] + ' - Approve VP User';
						} else if ($value['pekerjaan_dokumen_status'] == '7') {
							$status = 'IFA - Approve VP User';
						} else if ($value['pekerjaan_dokumen_status'] == '8') {
							$status = 'Draft IFC';
						} else if ($value['pekerjaan_dokumen_status'] == '9') {
							$status = 'IFC – Menunggu Review AVP';
						} else if ($value['pekerjaan_dokumen_status'] == '10') {
							$status = 'IFC – Menunggu Approve VP';
						} else if ($value['pekerjaan_dokumen_status'] == '11') {
							$status = 'IFC - Approved VP';
						} else if ($value['pekerjaan_dokumen_status_review'] == '2') {
							$status = 'Review CC';
						} else {
							$status = '';
						}
						echo $status
						?>
					</td>
					<td><?= $value['who_create'] ?></td>
					<td><?= $value['pekerjaan_dokumen_keterangan'] ?></td>
				<?php endif; ?>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>