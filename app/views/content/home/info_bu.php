<div class="rms-form-wizard">
   <!--Wizard Step Navigation Start-->
	<div class="rms-step-section" data-step-counter="false" data-step-image="false">
		<span class="step-title" style="text-align:center"><center>NOTIFIKASI</center></span>
	</div>
	<!--Wizard Navigation Close-->
	<form name="form-rms-wizard" id="form-rms-wizard" action="<?php echo site_url('home/get_data'); ?>" autocomplete="off" url="profile">
	<!--Wizard Content Section Start-->
	<div class="rms-content-section">
		<div class="rms-content-box rms-current-section" id="content_1">
			 <div class="rms-content-area">
				<div class="rms-content-title">
					<div class="leftside-title"><b> <i class="fa fa-info-circle " aria-hidden="true"></i> NOTIFIKASI</b></div>
					<div class="step-label">&nbsp;</div> 
				</div>
				<div class="rms-content-body">
					 <div class="row">
						 <div class="col-md-12">
							<center>
								<label><h3><?php echo $message; ?></H3></label>
							</center>
						</div> 
					 </div>
				</div>
				<?php if($ref_id != ""): ?>
				<div class="rms-content-body">
					 <div class="row">
						 <div class="col-md-12">
							<center>
								<button type="button" class="btn btn-primary waves-effect waves-light" onclick="print('123'); popup('home/confirm','');">
									<i class="fa fa-print " aria-hidden="true"></i>
									<br>
									<span class="text-uppercase hidden-xs">PRINT PROFORMA</span>
								</button>
							</center>
						</div> 
					 </div>
				</div>
				<?php endif; ?>
			</div> 
		</div>
	</div>
	<!--Wizard Content Section Close-->
	</form>
	<div class="rms-footer-section">
		<div class="button-section">
			<span class="prev">
				<a href="<?php echo base_url('index.php/document'); ?>" class="btn" >Menu Utama
					 <small>Dokumen Order</small>
				</a>
			</span>
		</div>
	</div>
</div>
<div id="dialog" style="display: none">&nbsp;</div>
<script type="text/javascript">
	function print(id){
		$("#dialog").dialog({
			modal: true,
			title: 'PROFORMA',
			width: 740,
			height: 650,
			buttons: {
				Close: function () {
					$(this).dialog('close');
				}
			},
			open: function(){
				var object = "<object data=\"{FileName}\" type=\"application/pdf\" width=\"700px\" height=\"600px\">";
				object += "If you are unable to view file, you can download from <a href = \"{FileName}\">here</a>";
				object += " or download <a target = \"_blank\" href = \"http://get.adobe.com/reader/\">Adobe PDF Reader</a> to view the file.";
				object += "</object>";
				object = object.replace(/{FileName}/g, site_url+"/proforma/print_data/"+id);
				$("#dialog").html(object);
			}
		});
	}
</script>
