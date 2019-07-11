<div class="col-md-12">
	<div class="row">
		<label class="col-md-2 control-label margin-top-10">USERNAME</label>
		<div class="col-md-10">
			<div class="inpt-form-group">
				<div class="inpt-group">
					<input type="text" name="useraccess" id="useraccess" class="inpt-control keyboard" placeholder="USERNAME" mandatory="yes" value="">
				</div> 
			</div>
		</div>
	</div>
	<div class="row">
		<label class="col-md-2 control-label margin-top-10">PASSWORD</label>
		<div class="col-md-10">
			<div class="inpt-form-group">
				<div class="inpt-group">
					<input type="password" name="passaccess" id="passaccess" class="inpt-control keyboard" placeholder="PASSWORD" mandatory="yes" value="">
				</div> 
			</div>
		</div>
	</div>
	<div class="row">
		<label class="col-md-2 control-label margin-top-10">&nbsp;</label>
		<div class="col-md-10">
			<div class="inpt-form-group">
				<div class="inpt-group">
					<?php if($scan == "919191") : ?>
					<button type="button" onclick="popup('home/register'); return false;" class="btn btn-primary"><i class="fa fa-user-plus" aria-hidden="true" ></i> Registrasi</button>
					<?php endif; ?>
					<button type="button" onclick="popup('home/password','','','350'); return false;" class="btn btn-primary"><i class="fa fa-question-circle" aria-hidden="true" ></i> Ganti Password</button>
				</div> 
			</div>
		</div>
	</div>
</div>
<script>
	$('.keyboard').keypad({
		keypadOnly: false, 
		layout: $.keypad.qwertyLayout
	});
</script>