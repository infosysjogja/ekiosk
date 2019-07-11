<div class="rms-form-wizard">
   <!--Wizard Step Navigation Start-->
	<div class="rms-step-section" data-step-counter="false" data-step-image="false">
		<ul class="rms-multistep-progressbar"> 
			<li class="rms-step rms-current-step">
				<span class="step-icon"><i class="fa fa-user" aria-hidden="true"></i></span>
				<span class="step-title">BOOKING ORDER</span>
				<span class="step-info">Data Booking Order</span>
			</li>
			<li class="rms-step">
				<span class="step-icon"><i class="fa fa-credit-card" aria-hidden="true"></i></span>
				<span class="step-title">DATA KAPAL</span>
				<span class="step-info">Data Kapal</span>
			</li>
			<li class="rms-step">
				<span class="step-icon"><i class="fa fa-credit-card" aria-hidden="true"></i></span>
				<span class="step-title">DOKUMEN BEA CUKAI</span>
				<span class="step-info">Data Dokumen Bea Cukai</span>
			</li>
			<li class="rms-step">
				<span class="step-icon ml10"><i class="fa fa-credit-card" aria-hidden="true"></i></span>
				<span class="step-title">KONTAINER DETAIL</span>
				<span class="step-info">Data Kontainer Detail</span>
			</li>
			<li class="rms-step">
				<span class="step-icon"><i class="fa fa-file-text" aria-hidden="true"></i></span>
				<span class="step-title">KONFIRMASI DATA</span>
				<span class="step-info">Konfirmasi Data</span>
			</li>
		</ul>
	</div>
	<!--Wizard Navigation Close-->
	<form name="form-rms-wizard" id="form-rms-wizard" action="<?php echo site_url('kiosk/execute_export'); ?>" autocomplete="off" url="submit" onsubmit="return false;">
	<!--Wizard Content Section Start-->
	<div class="rms-content-section">
		<div class="rms-content-box rms-current-section" id="content_1">
			 <div class="rms-content-area">
				<div class="rms-content-title">
					<div class="leftside-title"><b> <i class="fa fa-file-text" aria-hidden="true"></i> NO. BOOKING</b> ORDER</div>
					<div class="step-label" style="color:#0A79DA"><?php echo $this->session->userdata('DOCUMENT'); ?></div> 
				</div>
				<div class="rms-content-body"> 
					 <div class="row">
						 <div class="col-md-12">
							<div class="row">
							   <div class="col-md-12">
								   <div class="inpt-form-group">
									   <div class="inpt-group">
										   <input type="text" name="booking_order" id="booking_order" class="inpt-control key-full" placeholder="NO. BOOKING ORDER" mandatory="yes">
										</div>
									</div>
								</div>
							</div>
						</div> 
					 </div> 
				</div>
				<div>&nbsp;</div>
				<div>&nbsp;</div>
				<div>&nbsp;</div>
				<div>&nbsp;</div>
			</div> 
		</div>
		<div class="rms-content-box" id="content_2">
			 <div class="rms-content-area">
				<div class="rms-content-title">
					<div class="leftside-title"><b> <i class="fa fa-ship" aria-hidden="true"></i> DATA</b> KAPAL</div>
					<div class="step-label"><?php echo $this->session->userdata('DOCUMENT'); ?></div>
				</div>
				<div class="rms-content-body"> 
					<div class="row">
						<span id="page_datakapal"></span>
					</div> 
				</div> 
			</div> 
		</div>
		<div class="rms-content-box" id="content_3">
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
		<div class="rms-content-box" id="content_4">
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
				<a href="<?php echo base_url('index.php/document'); ?>" class="btn">
					<div style="margin-top:7px">Previous</div>
				</a>
			</span>
			<span class="submit">
				<a href="javascript:void(0)" class="btn">
					 <div style="margin-top:7px">Submit</div>
				</a>
			</span> 
		</div>
	</div>
	<!--Wizard Footer Close-->
</div>