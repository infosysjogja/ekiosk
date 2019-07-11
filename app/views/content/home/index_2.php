<style>
.radio-custom input[type="radio"]{
	width: 150px;
	height: 150px;
	opacity: 0;
	z-index: 1;
}
.radio-custom label::before {
	width: 150px;
	height: 150px;
	border: 3px solid #818F93;
}
.radio-custom label::after {
	width: 120px;
	height: 120px;
	left: 15px;
	top: 15px;
	background-color: #FCB348;
}
</style>
<div class="rms-form-wizard">
   <!--Wizard Step Navigation Start-->
	<div class="rms-step-section" data-step-counter="false" data-step-image="false">
		<ul class="rms-multistep-progressbar">
			<li class="rms-step rms-current-step" style="width:50%">
				<span class="step-icon"><i class="fa fa-user" aria-hidden="true"></i></span>
				<span class="step-title">DOKUMEN ORDER</span>
				<span class="step-info">Data Dokumen Order</span>
			</li>
			<li class="rms-step" style="width:50%">
				<span class="step-icon"><i class="fa fa-credit-card" aria-hidden="true"></i></span>
				<span class="step-title">PROFILE PERUSAHAAN</span>
				<span class="step-info">Data Profile Perusahaan</span>
			</li>
		</ul>
	</div>
	<!--Wizard Navigation Close-->
	<form name="form-rms-wizard" id="form-rms-wizard" action="<?php echo site_url('document/get_data'); ?>" autocomplete="off" url="profile">
	<!--Wizard Content Section Start-->
	<div class="rms-content-section">
		<div class="rms-content-box rms-current-section" id="content_1">
			 <div class="rms-content-area">
				<div class="rms-content-title">
					<div class="leftside-title"><b> <i class="fa fa-file-text" aria-hidden="true"></i> DOKUMEN</b> ORDER</div>
					<div class="step-label">&nbsp;</div> 
				</div>
				<div class="rms-content-body" style="height:370px">
					<div class="col-md-6">
						<div class="rms-content-body" style="height:175px;">
							<div class="col-md-12">
								<div class="row">
									<div class="col-md-4">
										<div class="radio-custom radio-primary margin-top-5">
										  <input id="export" name="document_type" value="EXPORT" type="radio" onclick="check(this.value,'document_order');">
										  <label for="export">&nbsp;</label>
										</div>
									</div>
									<div class="col-md-8">
										<label for="export" style="font-size:40px;margin-top:55px">EXPORT</label>
									</div>
								</div>
							</div>
							<div>&nbsp;</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="rms-content-body" style="height:175px;">
							<div class="col-md-12">
								<div class="row">
									<div class="col-md-4">
										<div class="radio-custom radio-primary margin-top-5">
										  <input id="export" name="document_type" value="IMPORT" type="radio" onclick="check(this.value,'document_order');">
										  <label for="import">&nbsp;</label>
										</div>
									</div>
									<div class="col-md-8">
										<label for="import" style="font-size:40px;margin-top:55px">IMPORT</label>
									</div>
								</div>
							</div>
							<div>&nbsp;</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="rms-content-body" style="height:175px;">
							<div class="col-md-12">
								<div class="row">
									&nbsp;
								</div>
							</div>
							<div>&nbsp;</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="rms-content-body" style="height:175px;">
							<div class="col-md-12">
								<div class="row">
									<div class="col-md-4">
										<div class="radio-custom radio-primary margin-top-5">
										  <input id="import_p" name="document_type" value="PERPANJANGAN IMPORT" type="radio" onclick="check(this.value,'document_order');">
										  <label for="import_p">&nbsp;</label>
										</div>
									</div>
									<div class="col-md-8">
										<label for="import_p" style="font-size:40px;margin-top:25px">PERPANJANGAN IMPORT</label>
									</div>
								</div>
							</div>
							<div>&nbsp;</div>
						</div>
					</div>
					<input type="hidden" name="document_order" id="document_order" mandatory="yes" readonly>
				</div>
			</div>
		</div>
		<div class="rms-content-box" id="content_2">
			 <div class="rms-content-area">
				<div class="rms-content-title">
					<div class="leftside-title"><b> <i class="fa fa-user" aria-hidden="true"></i> PROFILE</b> PERUSAHAAN</div>
					<div class="step-label">&nbsp;</div> 
				</div>
				<div class="rms-content-body"> 
					<div class="row">
						<div class="col-md-12">
							<div class="row"> 
								 <div class="col-md-12">
									<div class="inpt-form-group"> 
										<div class="inpt-group">
											<input type="text" name="npwp" id="npwp" class="inpt-control key-full" placeholder="NPWP /  PASSPORT" onkeyUp="get_data(this.value,'home/get_data/customer/npwp','profile'); enter_data(this.value,'v_npwp');" mandatory="yes" onblur="get_data(this.value,'home/get_data/customer/npwp','profile');" value="<?php echo $this->session->userdata('NPWP'); ?>">
										</div> 
									</div>
								</div>
							</div>
							<div class="row"> 
								 <div class="col-md-12">
									<div class="inpt-form-group"> 
										<div class="inpt-group">
											<input type="text" name="customer_id" id="customer_id" class="inpt-control key-full" placeholder="ID PELANGGAN" onkeyUp="get_data(this.value,'home/get_data/customer/id','profile'); enter_data(this.value,'v_customer_id');" mandatory="yes" onblur="get_data(this.value,'home/get_data/customer/id','profile');" value="<?php echo $this->session->userdata('CUSTOMER_ID'); ?>">
										</div> 
									</div>
								</div>
							</div> 
							<div class="row"> 
								 <div class="col-md-12">
									<div class="inpt-form-group"> 
										<div class="inpt-group">
											<input type="text" name="customer_name" id="customer_name" class="inpt-control key-full" placeholder="NAMA PELANGGAN" onkeyUp="enter_data(this.value,'v_customer_name');" mandatory="yes" value="<?php echo $this->session->userdata('CUSTOMER_NAME'); ?>">
										</div> 
									</div>
								</div>
							</div> 
							<div class="row"> 
								 <div class="col-md-12">
									<div class="inpt-form-group"> 
										<div class="inpt-group">
											<textarea rows="3" name="customer_address" id="customer_address" class="inpt-control key-full" placeholder="ALAMAT PELANGGAN" onkeyUp="enter_data(this.value,'v_customer_address');" mandatory="yes"><?php echo $this->session->userdata('CUSTOMER_ADDRESS'); ?></textarea>
										</div> 
									</div>
								</div>
							</div>
						</div> 
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
			<span class="submit">
				<a href="javascript:void(0)" class="btn">
					 <div style="margin-top:7px">Next</div>
				</a>
			</span> 
		</div>
	</div>
	<!--Wizard Footer Close-->
</div>