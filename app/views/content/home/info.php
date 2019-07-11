<?php 
$sess_user = strtoupper($this->session->userdata('USER_KIOSK'));
if($sess_user == "KIOSK"){
	#$order_id = "18240156"; $ref_id = 'INE604DBC9a'; $direct_approve = 'N';
}
?>
<div class="rms-form-wizards">
   <!--Wizard Step Navigation Start-->
	<div class="rms-step-section" data-step-counter="false" data-step-image="false">
		<span class="step-title" style="text-align:center"><center>NOTIFIKASI</center></span>
	</div>
	<!--Wizard Navigation Close-->
	<form name="form-rms-wizards" id="form-rms-wizards" action="<?php echo site_url('home/get_data'); ?>" autocomplete="off" url="profile">
	<!--wizards Content Section Start-->
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
				<?php if($order_id != ""){ ?>
				<div class="rms-content-body">
					<div class="row">
						<div class="col-md-12" id="div_proforma" align="center">
							<img src="<?php echo base_url('assets/images/loading.gif'); ?>" alt="Loading..."><br>Proses Generate Proforma...
						</div>
					</div>
				</div>
				<?php } else { ?>
				<div class="rms-content-body">
					 <div class="row">
						 <div class="col-md-12">
							<center>
								<label><h3>Apakah ada transaksi lain ?</H3></label>
							</center>
						</div>
						<div class="col-md-12">
							<center>
								<button type="button" class="btn btn-primary waves-effect waves-light" onclick="konfirmasi('Y');">
									<i class="fa fa-check" aria-hidden="true"></i>
									<span class="text-uppercase hidden-xs">YA</span>
								</button>
								&nbsp;&nbsp;&nbsp;&nbsp;
								<button type="button" class="btn btn-danger waves-effect waves-light" onclick="konfirmasi('N');">
									<i class="fa fa-close" aria-hidden="true"></i>
									<span class="text-uppercase hidden-xs">TIDAK</span>
								</button>
							</center>
						</div> 
					 </div>
				</div>
				<script>
					function konfirmasi(val){
						var url = site_url+'/home/confirm/execute';
						$.post(url,{id:val},
							function(data){
								close_popup(1);
								window.location.href = data.url;
						}, "json");
					}
				</script>
				<?php } ?>
				
			</div> 
		</div>
	</div>
	<!--wizards Content Section Close-->
	</form>
	<!--
	<div class="rms-footer-section">
		<div class="button-section">
			<span class="prev">
				<a href="<?php //echo base_url('index.php/document'); ?>" class="btn">
					<div style="margin-top:7px">Menu Utama</div>
				</a>
			</span>
		</div>
	</div>
	-->
</div>

<!--<div id="dialog" style="display:none">&nbsp;</div>-->
<script type="text/javascript">
	<?php if($order_id != ""): ?>
		var x = 0;
		var timeout = 13;
		var trigger = setInterval(function(){
			var url = site_url+'/proforma/generate_proforma';
			$.post(url,{id:'<?php echo $order_id; ?>',ref_id:'<?php echo $ref_id; ?>',direct_approve:'<?php echo $direct_approve; ?>', remark:'<?php echo $remark; ?>'},
				function(data){
					if(data.returnCode != ""){
						$("#div_proforma").html(data.returnView);
						clearInterval(trigger);
					}
			}, "json");
			if (++x === timeout){
				var html  = "<h3>PROFORMA GAGAL DIPROSES, SILAHKAN MEMBAWA DOKUMEN DAN MENUJU LOKET</h3>";
					html += '<div class="rms-content-body">';
					html += ' <div class="row">';
					html += '	 <div class="col-md-12">';
					html += '		<center>';
					html += '			<label><h3>Apakah ada transaksi lain ?</H3></label>';
					html += '		</center>';
					html += '	</div>';
					html += '	<div class="col-md-12">';
					html += '		<center>';
					html += '			<button type="button" class="btn btn-primary waves-effect waves-light" onclick="confirm(\'Y\'); return false;">';
					html += '				<i class="fa fa-check" aria-hidden="true"></i>';
					html += '				<span class="text-uppercase hidden-xs">YA</span>';
					html += '			</button>';
					html += '			&nbsp;&nbsp;&nbsp;&nbsp;';
					html += '			<button type="button" class="btn btn-danger waves-effect waves-light" onclick="confirm(\'N\'); return false;">';
					html += '				<i class="fa fa-close" aria-hidden="true"></i>';
					html += '				<span class="text-uppercase hidden-xs">TIDAK</span>';
					html += '			</button>';
					html += '		</center>';
					html += '	</div>';
					html += ' </div>';
					html += '</div>';
				$("#div_proforma").html(html);
				set_timeout('<?php echo $queueid; ?>','<?php echo $reportid; ?>');
				clearInterval(trigger);
			}
		},5000);
	<?php endif; ?>
	
	function confirm(val){
		var url = site_url+'/home/confirm/execute';
		$.post(url,{id:val},
			function(data){
				close_popup(1);
				window.location.href = data.url;
		}, "json");
	}
	
	function set_timeout(queueid, reportid){
		var url = site_url+'/home/execute/timeout';
		$.post(url,{queueid:queueid, reportid:reportid},
			function(data){
				console.log();
		}, "json");
	}
	
	function print_(id){
		var url = site_url+"/proforma/print_data/"+id;
		$.post(url,{id:id},
			function(data){
				if(data.code=="00"){
					var object = "<object data=\"{FileName}\" type=\"application/pdf\" width=\"700px\" height=\"600px\">Failed load PDF</object>";
					$("#dialog").html(object);
					//popup('home/confirm','');
				}
		},'html');
	}
	
	function print_div(divid){
		Loading(true);
		var object = "<object data=\"{FileName}\" type=\"application/pdf\" width=\"700px\" height=\"600px\">Failed to load PDF</object>";
			object = object.replace(/{FileName}/g, site_url+"/proforma/print_data");
		var frame1 = document.createElement('iframe');
		frame1.name = "frame1";
		frame1.style.src  = site_url+"/proforma/print_data";
		frame1.style.width  = '15px';
		frame1.style.height  = '15px';
		document.body.appendChild(frame1);
		var frameDoc = frame1.contentWindow?frame1.contentWindow:frame1.contentDocument.document?frame1.contentDocument.document:frame1.contentDocument;
		frameDoc.document.open();
		frameDoc.document.write(object);
		frameDoc.document.close();
		setTimeout(function(){
			window.frames["frame1"].focus();
			//window.frames["frame1"].print();
			document.body.removeChild(frame1);
		},5000);
		
		var x = 0;
		var timeout = 1;
		var trigger = setInterval(function(){
			var object = "<object data=\"{FileName}\" type=\"application/pdf\" width=\"700px\" height=\"600px\">Failed to load PDF</object>";
				object = object.replace(/{FileName}/g, site_url+"/proforma/print_data");
			var frame1 = document.createElement('iframe');
			frame1.name = "frame1";
			frame1.style.src  = site_url+"/proforma/print_data";
			frame1.style.width  = '15px';
			frame1.style.height  = '15px';
			document.body.appendChild(frame1);
			var frameDoc = frame1.contentWindow?frame1.contentWindow:frame1.contentDocument.document?frame1.contentDocument.document:frame1.contentDocument;
			frameDoc.document.open();
			frameDoc.document.write(object);
			frameDoc.document.close();
			setTimeout(function(){
				window.frames["frame1"].focus();
				//window.frames["frame1"].print();
				document.body.removeChild(frame1);
			},5000);
			if (++x === timeout){
			  clearInterval(trigger);
			}
		},5000);
		
		setInterval(function(){
			Loading(false);
		},11000);
	}
</script>
