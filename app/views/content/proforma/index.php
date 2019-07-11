<div class="rms-form-wizard">
   <!--Wizard Step Navigation Start-->
	<div class="rms-step-section" data-step-counter="false" data-step-image="false">
		<span class="step-title" style="text-align:center"><center>INFROMASI</center></span>
	</div>
	<!--Wizard Navigation Close-->
	<form name="form-rms-wizard" id="form-rms-wizard" action="<?php echo site_url('home/get_data'); ?>" autocomplete="off" url="profile">
	<!--Wizard Content Section Start-->
	<div class="rms-content-section">
		<div class="rms-content-box rms-current-section" id="content_1">
			 <div class="rms-content-area">
				<div class="rms-content-title">
					<div class="leftside-title"><b> <i class="fa fa-print" aria-hidden="true"></i> PRINT</b> PROFORMA</div>
					<div class="step-label">&nbsp;</div> 
				</div>
				<div class="rms-content-body">
					 <div class="row">
						 <div class="col-md-12">
							<center>
								<label><?php echo $this->session->flashdata('message_id'); ?></label>
							</center>
						</div> 
					 </div>
				</div>
				<div class="rms-content-body">
					 <div class="row">
						 <div class="col-md-12">
							<center>
								<button type="button" class="btn btn-primary waves-effect waves-light">
									<i class="fa fa-print " aria-hidden="true"></i>
									<br>
									<span class="text-uppercase hidden-xs">PRINT PROFORMA</span>
								</button>
							</center>
						</div> 
					 </div>
				</div>
				<div>&nbsp;</div>
			</div> 
		</div>
	</div>
	<!--Wizard Content Section Close-->
	</form>
</div>