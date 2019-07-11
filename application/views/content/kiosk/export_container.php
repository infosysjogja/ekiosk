<script>var index_cont = <?php echo (count($arrdata)>0)?count($arrdata):0; ?></script>
<div class="col-md-12">
	<span class="menu-content">
		<button type="button" onclick="popup('kiosk/execute_export/kontainerdetail/add');" class="btn btn-primary"><i class="fa fa-plus-circle" aria-hidden="true" ></i> Tambah</button>
	</span>
	<div>&nbsp;</div>
	<div>
		<span class="checkbox-custom checkbox-primary margin-top-5">
			<input id="chkallform-rms-wizard" onclick="tb_chkall('form-rms-wizard',this.checked)" class="chkall" type="checkbox">
			<label for="chkallform-rms-wizard">PILIH SEMUA DATA</label>
		</span>
	</div>
	<table id="tablecont" class="tabelajax">
		<thead>
			<tr>
				<th width="1%">&nbsp;</th>
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
			<?php if(count($arrdata) > 0): ?>
				<?php $no = 1; foreach($arrdata as $data): $index_cont .= ",".$no; ?>
				<tr id="cont_<?php echo $no; ?>">
					<td>
						<?php if($data['billing'] != ""): ?>
							<span style="color:red"><center><?php echo $data['billing']; ?></center></span>
						<?php else: ?>
							<center>
								<span class="checkbox-custom checkbox-primary">
									<input <?php echo ($data['status'] == "OK")?"checked":""; ?> name="chkcontainer[]" id="chkform-rms-wizard" class="chkform-rms-wizard" type="checkbox" value="<?php echo $no; ?>" onclick="tb_chk('form-rms-wizard',this.checked,this.value);"><label for="chkform-rms-wizard"></label>
								</span>
							</center>
						<?php endif; ?>
					</td>
					<td>
						<?php echo $data['cont_no']; ?>
						<input name="containers[CONT_<?php echo $no; ?>][CONTAINER]" id="CONTAINER_<?php echo $no; ?>" type="hidden" value="<?php echo $data['cont_no']; ?>" maxlength="11" readonly>
					</td>
					<td>
						<?php echo $data['isocode']; ?>
						<input name="containers[CONT_<?php echo $no; ?>][ISOCODE]" id="ISOCODE_<?php echo $no; ?>" type="hidden" value="<?php echo $data['isocode']; ?>" maxlength="4" readonly>
					</td>
					<td>
						<?php echo get_isocode($data['isocode'],'size'); ?>
						<input name="containers[CONT_<?php echo $no; ?>][CONT_SIZE]" id="CONT_SIZE_<?php echo $no; ?>" type="hidden" value="<?php echo get_isocode($data['isocode'],'size'); ?>" maxlength="2" readonly>
					</td>
					<td>
						<?php echo get_isocode($data['isocode'],'type'); ?>
						<input name="containers[CONT_<?php echo $no; ?>][CONT_TYPE]" id="CONT_TYPE_<?php echo $no; ?>" type="hidden" value="<?php echo get_isocode($data['isocode'],'type'); ?>" readonly>
					</td>
					<td>
						<?php echo $data['full_empty']; ?>
						<input name="containers[CONT_<?php echo $no; ?>][FE]" id="FE_<?php echo $no; ?>" type="hidden" value="<?php echo $data['full_empty']; ?>" readonly>
					</td>
					<td>
						<?php echo $data['imo_class']; ?>
						<input name="containers[CONT_<?php echo $no; ?>][I_CLASS]" id="CLASS_<?php echo $no; ?>" type="hidden" value="<?php echo $data['imo_class']; ?>" readonly>
					</td>
					<td>
						<?php echo $data['imo_no']; ?>
						<input name="containers[CONT_<?php echo $no; ?>][I_NO]" id="I_NO_<?php echo $no; ?>" type="hidden" value="<?php echo $data['imo_no']; ?>" readonly>
					</td>
					<td>
						<?php echo $data['temperature']; ?>
						<input name="containers[CONT_<?php echo $no; ?>][TEMPERATURE]" id="TEMPERATURE_<?php echo $no; ?>" type="hidden" value="<?php echo $data['temperature']; ?>" readonly>
					</td>
					<td>
						<?php echo $data['oogs_or']; ?>
						<input name="containers[CONT_<?php echo $no; ?>][OR]" id="OR_<?php echo $no; ?>" type="hidden" value="<?php echo $data['oogs_or']; ?>" readonly>
					</td>
					<td>
						<?php echo $data['oogs_oh']; ?>
						<input name="containers[CONT_<?php echo $no; ?>][OH]" id="OH_<?php echo $no; ?>" type="hidden" value="<?php echo $data['oogs_oh']; ?>" readonly>
					</td>
					<td>
						<?php echo $data['oogs_ol']; ?>
						<input name="containers[CONT_<?php echo $no; ?>][OL]" id="OL_<?php echo $no; ?>" type="hidden" value="<?php echo $data['oogs_ol']; ?>" readonly>
					</td>
					<td>
						<?php echo $data['reefer_shift']; ?>
						<input name="containers[CONT_<?php echo $no; ?>][SHFT_RFR]" id="SHFT_RFR_<?php echo $no; ?>" type="hidden" value="<?php echo $data['reefer_shift']; ?>" readonly>
						<input name="containers[CONT_<?php echo $no; ?>][SEAL]" id="SEAL_<?php echo $no; ?>" type="hidden" value="" readonly>
						<input name="containers[CONT_<?php echo $no; ?>][STATUS]" id="STATUS_<?php echo $no; ?>" type="hidden" value="<?php echo $data['status']; ?>" readonly>
					</td>
				</tr>
				<?php $no++; endforeach; ?>
			<?php else: ?>
				<tr id="cont_null">
					<td colspan="13"><center>Data kontainer tidak ditemukan</center></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
	<div>&nbsp;</div>
</div>
<input type="hidden" name="index_cont" id="index_cont" value="<?php echo $index_cont; ?>" readonly="readonly" mandatory="yes"/>
<input type="hidden" name="tmpchkform-rms-wizard" id="tmpchkform-rms-wizard" value="" readonly="readonly"/>