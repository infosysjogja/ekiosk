<div class="col-md-12">
	<div class="desc-table">
		<table class="tabelajax-no-border-left" width="100%">
			<tbody>
				<tr>
					<td class="desc-label" width="19%">NPWP</td>
					<td width="1%">:</td>
					<td class="desc-val" width="80"><?php echo $arrsess['npwp']; ?></td>
				</tr>
				<tr>
					<td class="desc-label">NAMA PELANGGAN</td>
					<td>:</td>
					<td class="desc-val"><?php echo $arrsess['cust_name']; ?></td>
				</tr>
				<tr>
					<td class="desc-label">ALAMAT</td>
					<td>:</td>
					<td class="desc-val"><?php echo $arrsess['cust_address']; ?></td>
				</tr>
				<tr>
					<td class="desc-label">NOMOR BOOKING</td>
					<td>:</td>
					<td class="desc-val"><?php echo $arrhdr['booking_order']; ?></td>
				</tr>
				<tr>
					<td class="desc-label">KAPAL / VOYAGE</td>
					<td>:</td>
					<td class="desc-val"><?php $arrvessel = explode("~",$arrhdr['vessel']); echo $arrvessel[2]." / ".$arrvessel[1]; ?></td>
				</tr>
				<tr>
					<td class="desc-label">VIA / TRANSIT</td>
					<td>:</td>
					<td class="desc-val"><?php echo substr(trim($arrhdr['spod']),0,6)." - ".port_name(substr(trim($arrhdr['spod']),0,6)); ?></td>
				</tr>
				<tr>
					<td class="desc-label">TUJUAN</td>
					<td>:</td>
					<td class="desc-val"><?php echo substr(trim($arrhdr['pod']),0,6)." - ".port_name(substr(trim($arrhdr['pod']),0,6)); ?></td>
				</tr>
				<tr>
					<td class="desc-label">AGENT</td>
					<td>:</td>
					<td class="desc-val"><?php echo $arrhdr['agent']." - ".agent_name($arrhdr['agent']); ?></td>
				</tr>
				<tr>
					<td class="desc-label">DOKUMEN BEA CUKAI</td>
					<td>:</td>
					<td class="desc-val"><?php echo $arrhdr['res_dokumen']." / ".$arrhdr['res_nomor']." / ".$arrhdr['res_tanggal']; ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div>&nbsp;</div>
	<table class="tabelajax">
		<thead>
			<tr>
				<th width="1%">NO.</th>
				<th width="10%">KONTAINER</th>
				<th width="5%">ISOCODE</th>
				<th width="1%">UKURAN</th>
				<th width="5%">TIPE</th>
				<th width="1%">F/E</th>
				<th width="8%">IMO CLASS</th>
				<th width="8%">NO. UN</th>
				<th width="5%">SUHU</th>
				<th width="1%">OR</th>
				<th width="1%">OH</th>
				<th width="1%">OL</th>
				<th width="10%">RFR. SHIFT</th>
			</tr>
		</thead>
		<tbody>
			<?php if(count($arrcont) > 0): ?>
				<?php $no = 1; foreach($arrcont as $data): $index_cont .= ",".$no; ?>
				<tr>
					<td><center><?php echo $no; ?></center></td>
					<td><?php echo $data['CONTAINER']; ?></td>
					<td><?php echo $data['ISOCODE']; ?></td>
					<td><?php echo get_isocode($data['ISOCODE'],'size'); ?></td>
					<td><?php echo get_isocode($data['ISOCODE'],'type'); ?></td>
					<td><?php echo $data['FE']; ?></td>
					<td><?php echo $data['I_CLASS']; ?></td>
					<td><?php echo $data['I_NO']; ?></td>
					<td><?php echo $data['TEMPERATURE']; ?></td>
					<td><?php echo $data['OR']; ?></td>
					<td><?php echo $data['OH']; ?></td>
					<td><?php echo $data['OL']; ?></td>
					<td><?php echo $data['SHFT_RFR']; ?></td>
				</tr>
				<?php $no++; endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan="13"><center>Data kontainer tidak ditemukan</center></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>
<div>&nbsp;</div>