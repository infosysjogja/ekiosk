<div class="rms-form-wizard">
   <!--Wizard Step Navigation Start-->
	<div class="rms-step-section" data-step-counter="false" data-step-image="false">
		<ul class="rms-multistep-progressbar">
			<li class="rms-step rms-current-step" style="width:50%">
				<span class="step-icon"><i class="fa fa-qrcode" aria-hidden="true"></i></span>
				<span class="step-title">SCAN ANTRIAN</span>
				<span class="step-info">Scan Antrian</span>
			</li>
			<li class="rms-step" style="width:50%">
				<span class="step-icon"><i class="fa fa-sign-in" aria-hidden="true"></i></span>
				<span class="step-title">LOGIN</span>
				<span class="step-info">LOGIN</span>
			</li>
		</ul>
	</div>
	<!--Wizard Navigation Close-->
	<form name="form-rms-wizard" id="form-rms-wizard" action="<?php echo site_url('home/get_data'); ?>" autocomplete="off" url="login" onsubmit="return false;">
	<!--Wizard Content Section Start-->
	<div class="rms-content-section">
		<div class="rms-content-box rms-current-section" id="content_1">
			 <div class="rms-content-area">
				<div class="rms-content-title">
					<div class="leftside-title"><b> <i class="fa fa-qrcode" aria-hidden="true"></i> SCAN</b> ANTRIAN</div>
					<div class="step-label">&nbsp;</div> 
				</div>
				<div class="rms-content-body">
					 <div class="row">
						 <div class="col-md-12">
							<div class="row">
							   <div class="col-md-12">
								   <div class="inpt-form-group">
									   <div class="inpt-group">
										   <input type="text" name="scan" id="scan" class="inpt-control key-num" placeholder="SCAN ANTRIAN" mandatory="yes" maxlength="6">
										</div>
									</div>
								</div>
							</div>
						</div> 
					 </div>
				</div>
				<div>&nbsp;</div>
			</div> 
		</div>
		<div class="rms-content-box" id="content_2">
			 <div class="rms-content-area">
				<div class="rms-content-title">
					<div class="leftside-title"><b> <i class="fa fa-sign-in" aria-hidden="true"></i> LOGIN</b></div>
					<div class="step-label"><span class="document_type"></span></div> 
				</div>
				<div class="rms-content-body">
					<div class="rms-content-body"> 
						<div class="row">
							<span id="page_login">&nbsp;</span>
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
					<!--<div>&nbsp;</div>-->
				</a>
			</span>
			<!--
			<span class="prev">
				<a href="javascript:void(0)" class="btn" >Previous
					 <small>Your information</small>
				</a>
			</span>
			-->
			<span class="submit">
				<a href="javascript:void(0)" class="btn">
					<div style="margin-top:7px">Next</div>
				</a>
			</span> 
		</div>
	</div>
	<!--Wizard Footer Close-->
</div>
<script>
function _replace(string, target, replacement){
	var i = 0, length = string.length;
	for (i; i < length; i++){
		string = string.replace(target, replacement);
	}
	return string;
}

function check_scan(id){
	if(id.length >= 5){
		var _0xad3bx6 = '';
			_0xad3bx7 = 'rms-wizard';
			_0xad3bx2 = $('#' + _0xad3bx7 + ' .rms-content-box.rms-current-section')['index'](),
			_0xad3bx3 = $('#' + _0xad3bx7 + ' .rms-multistep-progressbar li.rms-step'),
			_idData = _0xad3bx3['eq'](_0xad3bx2)['find']('.step-title')['text'](),
			_form = 'form-'+_0xad3bx7,
			_action = $('[name="'+_form+'"]').attr('action'),
			_id = _replace(_idData,' ','').toLowerCase(),
			_nextPage = _0xad3bx3['eq'](_0xad3bx2 + 1)['find']('.step-title')['text'](),
			_nextId = _replace(_nextPage,' ','').toLowerCase(),
			_div = $('.rms-current-section').attr('id');
			if(validasi(_form,_div)){
				$.ajax({
					type: 'POST',
					dataType : 'JSON',
					url: _action+'/'+_id,
					data: $('[name="'+_form+'"]').serialize(),
					beforeSend: function(){Loading(true)},
					complete:function(){Loading(false)},
					success: function(data){
						$('#div-'+_id).html('<span class="errors animated">'+data.message+'</span>');
						if(data.success == 1){
							_0xad3bx3['eq'](_0xad3bx2 + 1)['addClass']('rms-current-step');
							var _0xad3bx4 = _0xad3bx2 + 1,
								_0xad3bx5 = $('#' + _0xad3bx7 + ' .rms-multistep-progressbar li.rms-step:lt(' + _0xad3bx4 + ')');
								_0xad3bx5['removeClass']('rms-current-step')['addClass']('completed-step'),
								//$['goToSection'](_0xad3bx4),
								//_0xad3bx6['btnInformationText'](_0xad3bx4, _0xad3bx2);
								//_0xad3bx6['handleStepBtn']();
							$('#page_'+_nextId).html(data.page);
						}else{
							if(data.url != undefined){
								swal({title:'KIOSK',
									  text:data.message,
									  type:'info',
									  showConfirmButton:true,
									  html: true
								},function(){
									window.location.href = data.url;
								});
							}else{
								swalert('error',data.message);
							}
						}
						return false;
					}
				});
			}
		}
}
</script>
