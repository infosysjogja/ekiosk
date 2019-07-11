<div class="panel">
  <div class="ribbon ribbon-bookmark">
  	<span class="ribbon-inner">
		<i class="fa fa-server" aria-hidden="true"></i> Registrasi Customer
    </span>
  </div>
	<div>&nbsp;</div>
	<div>&nbsp;</div>
	<div>&nbsp;</div>
	<div id="tabs">
	  <form name="form_reg" id="form_reg" action="<?php echo site_url('home/execute/registrasi'); ?>" class="form-horizontal" role="form" popup="1" method="post" autocomplete="off">
	  <ul>
		<li><a href="#tabs-1">PERUSAHAAN PPJK</a></li>
		<li><a href="#tabs-2">PENANGGUNG JAWAB</a></li>
	  </ul>
	  <div id="tabs-1">
		<div class="panel-body container-fluid">
			<div class="row">
			  <div class="form-group">
				<label class="col-sm-3 control-label">TIPE</label>
				<div class="col-sm-4">
					<select name="type_perusahaan" id="type_perusahaan" class="form-control" mandatory="yes" onchange="change(this.value,'type_perusahaan_txt');">
						<option value="PT">PT</option>
						<option value="CV">CV</option>
						<option value="-">LAINNYA</option>
					</select>
				</div>
				<div class="col-sm-4">
					  <input type="text" name="type_perusahaan_txt" id="type_perusahaan_txt" class="form-control" placeholder="TIPE PERUSAHAAN" value="" disabled>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-3 control-label">NAMA</label>
				<div class="col-sm-8">
				  <input type="text" name="nama_persh" id="nama_persh" class="form-control" placeholder="NAMA PERUSAHAAN" value="" mandatory="yes">
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-3 control-label">NPWP</label>
				<div class="col-sm-8">
				  <input type="text" name="npwp_persh" id="npwp_persh" class="form-control" placeholder="NPWP PERUSAHAAN" value="" mandatory="yes">
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-3 control-label">ALAMAT</label>
				<div class="col-sm-8">
					<textarea name="alamat_persh" id="alamat_persh" class="form-control" placeholder="ALAMAT PERUSAHAAN" mandatory="yes"></textarea>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-3 control-label">KOTA</label>
				<div class="col-sm-8">
				  <input type="text" name="kota_persh" id="kota_persh" class="form-control" placeholder="KOTA" value="" mandatory="yes">
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-3 control-label">KODE POS</label>
				<div class="col-sm-8">
				  <input type="text" name="kode_pos_persh" id="kode_pos_persh" class="form-control" placeholder="KODE POS" value="" mandatory="yes">
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-3 control-label">TELEPON / FAX</label>
				<div class="col-sm-4">
				  <input type="text" name="telp_persh" id="telp_persh" class="form-control" placeholder="TELEPON" value="" mandatory="yes">
				</div>
				<div class="col-sm-4">
				  <input type="text" name="fax_persh" id="fax_persh" class="form-control" placeholder="FAX" value="" mandatory="yes">
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-3 control-label">EMAIL</label>
				<div class="col-sm-8">
				  <input type="text" name="email_persh" id="email_persh" class="form-control" placeholder="EMAIL" value="" mandatory="yes">
				</div>
			  </div>
			</div>
		 </div>
	  </div>
	  <div id="tabs-2">
		<div class="panel-body container-fluid">
			<div class="row">
			  <div class="form-group">
				<label class="col-sm-3 control-label">NAMA</label>
				<div class="col-sm-4">
				  <input type="text" name="nama_pj1" id="nama_pj1" class="form-control" placeholder="NAMA DEPAN" value="" mandatory="yes">
				</div>
				<div class="col-sm-4">
				  <input type="text" name="nama_pj2" id="nama_pj2" class="form-control" placeholder="NAMA BELAKANG" value="" mandatory="yes">
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-3 control-label">KTP / SIM</label>
				<div class="col-sm-8">
				  <input type="text" name="identity_pj" id="identity_pj" class="form-control" placeholder="KTP / SIM" value="" mandatory="yes">
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-3 control-label">NO. HANDPHONE</label>
				<div class="col-sm-8">
				  <input type="text" name="hp_pj" id="hp_pj" class="form-control" placeholder="NO. HANDPHONE" value="" mandatory="yes">
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-3 control-label">EMAIL</label>
				<div class="col-sm-8">
				  <input type="text" name="email_pj" id="email_pj" class="form-control" placeholder="EMAIL" value="" mandatory="yes">
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-3 control-label">USERNAME</label>
				<div class="col-sm-8">
				  <input type="text" name="username_pj" id="username_pj" class="form-control" placeholder="USERNAME" value="" mandatory="yes">
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-3 control-label">PASSWORD</label>
				<div class="col-sm-4">
				  <input type="password" name="password_pj" id="password_pj" class="form-control" placeholder="PASSWORD" value="" mandatory="yes">
				</div>
				<div class="col-sm-4">
				  <input type="password" name="cf_password_pj" id="cf_password_pj" class="form-control" placeholder="KONFIRMASI PASSWORD" value="" mandatory="yes">
				</div>
			  </div>
			</div>
		 </div>
	  </div>
	  <div class="form-group">
		<label class="col-sm-3 control-label">&nbsp;</label>
		<div class="col-sm-4">
		  <button type="button" onclick="save_post('form_reg');" class="btn btn-sm btn-primary">
				<i class="fa fa-floppy-o" aria-hidden="true"></i> Registrasi
		  </button>
		  <button type="reset" class="btn btn-sm btn-danger">
				<i class="fa fa-refresh" aria-hidden="true"></i> Reset
		  </button>
		</div>
	  </div>
	</div>
	</form>
</div>
<script>
$( function() {
	$("#tabs").tabs();
});

function change(val,object){
	var arrobject = object.split("|");
	if(val == "-"){
		$.each(arrobject, function(a, b){
			$('#'+arrobject[a]).removeAttr('disabled');
			$('#'+arrobject[a]).attr('mandatory','yes');
		});
	}else{
		$.each(arrobject, function(a, b){
			$('#'+arrobject[a]).attr('disabled','disabled');
			$('#'+arrobject[a]).removeAttr('mandatory');
			$('#'+arrobject[a]).val('');
		})
	}
}
</script>