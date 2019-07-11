<div class="rms-form-wizard">
   <!--Wizard Step Navigation Start-->
	<div class="rms-step-section" data-step-counter="false" data-step-image="false">
		<ul class="rms-multistep-progressbar"> 
			<li class="rms-step rms-current-step" style="width:25%">
				<span class="step-icon"><i class="fa fa-user" aria-hidden="true"></i></span>
				<span class="step-title">DELIVERY ORDER</span>
				<span class="step-info">Data Delivery Order</span>
			</li>
			<li class="rms-step" style="width:25%">
				<span class="step-icon"><i class="fa fa-credit-card" aria-hidden="true"></i></span>
				<span class="step-title">DOKUMEN BEA CUKAI</span>
				<span class="step-info">Data Dokumen Bea Cukai</span>
			</li>
			<li class="rms-step" style="width:25%">
				<span class="step-icon ml10"><i class="fa fa-credit-card" aria-hidden="true"></i></span>
				<span class="step-title">KONTAINER DETAIL</span>
				<span class="step-info">Data Kontainer Detail</span>
			</li>
			<li class="rms-step" style="width:25%">
				<span class="step-icon"><i class="fa fa-file-text" aria-hidden="true"></i></span>
				<span class="step-title">KONFIRMASI DATA</span>
				<span class="step-info">Konfirmasi Data</span>
			</li>
		</ul>
	</div>
	<!--Wizard Navigation Close-->
	<form name="form-rms-wizard" id="form-rms-wizard" action="<?php echo site_url('kiosk/execute_import'); ?>" autocomplete="off" url="submit" onsubmit="return false;">
	<!--Wizard Content Section Start-->
	<div class="rms-content-section">
		<div class="rms-content-box rms-current-section" id="content_1">
			<div class="rms-content-area">
				<div class="rms-content-title">
					<div class="leftside-title"><b> <i class="fa fa-file-text" aria-hidden="true"></i> DELIVERY</b> ORDER</div>
					<div class="step-label" style="color:#0A79DA"><?php echo $this->session->userdata('DOCUMENT'); ?></div> 
				</div>
				<div class="rms-content-body"> 
					<div class="row">
						<label class="col-md-2 control-label margin-top-10">NOMOR DO</label>
						<div class="col-md-4">
							<div class="inpt-form-group">
								<div class="inpt-group">
									<input type="text" name="no_do" id="no_do" class="inpt-control key-full" placeholder="NOMOR DO" mandatory="yes">
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
						<div class="col-md-3">
							<div class="inpt-form-group">
								<div class="inpt-group">
									<input type="text" name="no_bl" id="no_bl" class="inpt-control key-full" placeholder="CENTANG JIKA BL SAMA DENGAN DO" mandatory="yes">
								</div> 
							</div>
						</div>
						<div class="col-md-1">
							<span class="checkbox-custom checkbox-primary margin-top-5">
								<input name="chk_do" id="chk_do" onclick="chk_value(this.checked);" type="checkbox">
							<label for="chkallform-rms-wizard"></label>
						</span>
						</div>
						<label class="col-md-2 control-label margin-top-10">PEMBAYARAN</label>
						<div class="col-md-4">
							<div class="inpt-form-group">
								<div class="inpt-group">
									<input type="text" name="date_until" id="date_until" class="inpt-control datedefault" placeholder="PEMBAYARAN S/D" mandatory="yes">
								</div> 
							</div>
						</div>
					</div>
					<!---
					<div class="row">
						<label class="col-md-2 control-label margin-top-10">NOMOR BC</label>
						<div class="col-md-4">
							<div class="inpt-form-group">
								<div class="inpt-group">
									<input type="text" name="no_bc" id="no_bc" class="inpt-control key-full" placeholder="NOMOR BC" mandatory="yes">
								</div> 
							</div>
						</div>
					</div>
					-->
				</div>
			</div> 
		</div>
		<div class="rms-content-box" id="content_2">
			 <div class="rms-content-area">
				<div class="rms-content-title">
					<div class="leftside-title"><b> <i class="fa fa-file-text-o" aria-hidden="true"></i> DOKUMEN</b> BEA CUKAI</div>
					<div class="step-label"><?php echo $this->session->userdata('DOCUMENT'); ?></div>
				</div>
				<div class="rms-content-body"> 
					<div class="row">
						<span id="page_dokumenbeacukai"></span>
					</div> 
				</div> 
			</div> 
		</div>
		<div class="rms-content-box" id="content_3">
			 <div class="rms-content-area">
				<div class="rms-content-title">
					<div class="leftside-title"><b> <i class="fa fa-th" aria-hidden="true"></i> KONTAINER</b> DETAIL</div>
					<div class="step-label"><?php echo $this->session->userdata('DOCUMENT'); ?></div>
				</div>
				<div class="rms-content-body"> 
					<div class="row">
						<span id="page_kontainerdetail"></span>
					</div> 
				</div>
			</div> 
		</div>
		<div class="rms-content-box" id="content_5">
			<div class="rms-content-area">
				<div class="rms-content-title">
					<div class="leftside-title"><b> <i class="fa fa-commenting-o" aria-hidden="true"></i> KONFIRMASI</b> DATA</div>
					<div class="step-label"><?php echo $this->session->userdata('DOCUMENT'); ?></div> 
				</div>
				<div class="rms-content-body"> 
					<div class="row">
						<span id="page_konfirmasidata"></span>
					</div> 
				</div> 
			</div> 
		</div>
	</div>
	<!--Wizard Content Section Close-->
	</form>
	<!--Wizard Footer section Start-->
	
	<div class="rms-footer-section">
		<div class="button-section">
			<span class="next">
				<a href="javascript:void(0)" class="btn">
					<div style="margin-top:7px">Next</div>
				</a>
			</span>
			<span class="prev">
				<a href="javascript:void(0)" class="btn">
					<div style="margin-top:7px">Previous</div>
				</a>
			</span>
			<span class="home">
				<a href="<?php echo base_url('index.php/document'); ?>" class="btn" >
					<div style="margin-top:7px">Previous</div>
				</a>
			</span>
			<span class="submit">
				<a href="javascript:void(0)" class="btn" >
					 <div style="margin-top:7px">Next</div>
				</a>
			</span> 
		</div>
	</div>
	<!--Wizard Footer Close-->
</div>
<script>
	function chk_value(chk){
		var do_no = $('#no_do').val();
		if(chk){
			$('#no_bl').val(do_no);
		}else{
			$('#no_bl').val('');
		}
	}
</script>