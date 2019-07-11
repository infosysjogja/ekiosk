<div class="rms-content-area">
	<div class="rms-content-title">
		<div class="leftside-title"><b> <i class="fa fa-file-text" aria-hidden="true"></i> BOOKING</b> ORDER</div>
		<div class="step-label" style="color:#0A79DA"><?php echo $this->session->userdata('DOKUMEN'); ?></div> 
	</div>
	<div class="rms-content-body"> 
		<div class="row">
			<label class="col-md-2 control-label margin-top-10">NOMOR DO</label>
			<div class="col-md-4">
				<div class="inpt-form-group">
					<div class="inpt-group">
						<input type="text" name="no_do" id="no_do" class="inpt-control" placeholder="NOMOR DO" mandatory="yes">
					</div> 
				</div>
			</div>
			<label class="col-md-2 control-label margin-top-10">JATUH TEMPO DO</label>
			<div class="col-md-4">
				<div class="inpt-form-group">
					<div class="inpt-group">
						<input type="text" name="tgl_tempo" id="tgl_tempo" class="inpt-control date" placeholder="JATUH TEMPO DO" mandatory="yes">
					</div> 
				</div>
			</div>
		</div>
		<div class="row">
			<label class="col-md-2 control-label margin-top-10">NOMOR BL</label>
			<div class="col-md-4">
				<div class="inpt-form-group">
					<div class="inpt-group">
						<input type="text" name="no_bl" id="no_bl" class="inpt-control" placeholder="NOMOR BL" mandatory="yes">
					</div> 
				</div>
			</div>
			<label class="col-md-2 control-label margin-top-10">PEMBAYATAN S/D</label>
			<div class="col-md-4">
				<div class="inpt-form-group">
					<div class="inpt-group">
						<input type="text" name="date_until" id="date_until" class="inpt-control date" placeholder="PEMBAYARAN S/D" mandatory="yes">
					</div> 
				</div>
			</div>
		</div>
		<div class="row">
			<label class="col-md-2 control-label margin-top-10">NOMOR BC</label>
			<div class="col-md-4">
				<div class="inpt-form-group">
					<div class="inpt-group">
						<input type="text" name="no_bc" id="no_bc" class="inpt-control" placeholder="NOMOR BC" mandatory="yes">
					</div> 
				</div>
			</div>
		</div>
	</div>
</div> 