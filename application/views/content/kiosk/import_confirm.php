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
					<td class="desc-label">NOMOR DO / BL</td>
					<td>:</td>
					<td class="desc-val"><?php echo $arrhdr['no_do']." / ".$arrhdr['no_bl']; ?></td>
				</tr>
				<tr>
					<td class="desc-label">KAPAL / VOYAGE</td>
					<td>:</td>
					<td class="desc-val"><?php echo (!empty($arrhdr['vessel_name']))?escape($arrhdr['vessel_name'])." - ".escape($arrhdr['voyage_in'])." / ".escape($arrhdr['voyage_out']):""; ?></td> 
				</tr>
				<tr>
					<td class="desc-label">VIA / TRANSIT</td>
					<td>:</td>
					<td class="desc-val"><?php echo escape($arrhdr['spod'])." - ".port_name($arrhdr['spod']); ?></td>
				</tr>
				<tr>
					<td class="desc-label">TUJUAN</td>
					<td>:</td>
					<td class="desc-val"><?php echo escape($arrhdr['pod'])." - ".port_name($arrhdr['pod']); ?></td>
				</tr>
				<tr>
					<td class="desc-label">AGENT</td>
					<td>:</td>
					<td class="desc-val"><?php echo $arrhdr['agent']." - ".$arrhdr['agent_name']; ?></td>
				</tr>
				<tr>
					<td class="desc-label">DOKUMEN BEA CUKAI</td>
					<td>:</td>
					<td class="desc-val"><?php echo $arrhdr['res_dokumen']." / ".$arrhdr['res_nomor']." / ".$arrhdr['res_tanggal']; ?></td>
				</tr>
				<tr>
					<td class="desc-label">TGL. TEMPO / TGL. PEMBAYARAN</td>
					<td>:</td>
					<td class="desc-val"><?php echo $this->input->post('tgl_tempo')." / ".$this->input->post('date_until'); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div>&nbsp;</div>
	<table class="tabelajax">
		<thead>
			<tr>
				<th width="1%">NO</th>
				<th width="10%">KONTAINER</th>
				<th width="5%">ISOCODE</th>
				<th width="5%">UKURAN</th>
				<th width="5%">TIPE</th>
				<th width="1%">F/E</th>
				<th width="1%">DG</th>
				<th width="1%">OOG</th>
				<th width="10%">DISCHARGE</th>
				<th width="10%">PLUG IN</th>
				<th width="10%">PLUG OUT</th>
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
					<td><?php echo ($data['I_CLASS']!="")?"Y":"N"; ?></td>
					<td><?php echo ($data['OR']!="" || $data['OH']!="" || $data['OL']!="")?"Y":"N"; ?></td>
					<td><?php echo substr($data['DIS'],0,16); ?></td>
					<td><?php echo substr($data['RCN'],0,16); ?></td>
					<td><?php echo $data['RDC']; ?></td>
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