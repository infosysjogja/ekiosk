<div class="panel">
  <div class="ribbon ribbon-bookmark">
  	<span class="ribbon-inner">
		<i class="fa fa-server" aria-hidden="true"></i> GANTI PASSWORD
    </span>
  </div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div class="panel-body container-fluid">
    <div class="row">
      <div class="col-sm-12">
        <form name="form_password" id="form_password" action="<?php echo site_url('home/execute/password'); ?>" class="form-horizontal" role="form" method="post" autocomplete="off" popup="1">
          <div class="panel-body container-fluid" id="f-cont">
            <div class="row">
			  <div class="form-group">
                <label class="col-sm-4 control-label">USERNAME</label>
                <div class="col-sm-8">
				  <input name="username_pass" id="username_pass" mandatory="yes" class="form-control" placeholder="USERNAME" type="text" mandatory="yes">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">PASSWORD LAMA</label>
                <div class="col-sm-8">
				  <input name="old_pass" id="old_pass" mandatory="yes" class="form-control" placeholder="PASSWORD LAMA" type="password" mandatory="yes">
                </div>
              </div>
			  <div class="form-group">
                <label class="col-sm-4 control-label">PASSWORD BARU</label>
                <div class="col-sm-8">
				  <input name="new_pass" id="new_pass" mandatory="yes" class="form-control" placeholder="PASSWORD BARU" type="password" mandatory="yes">
                </div>
              </div>
			  <div class="form-group">
                <label class="col-sm-4 control-label">KONFIRMASI PASSWORD BARU</label>
                <div class="col-sm-8">
				  <input name="new_pass_cf" id="new_pass_cf" mandatory="yes" class="form-control" placeholder="KONFIRMASI PASSWORD BARU" type="password" mandatory="yes">
                </div>
              </div>
			  <div class="form-group">
                <label class="col-sm-4 control-label">&nbsp;</label>
				<div class="col-sm-8">
                  <button type="button" onclick="save_data('form_password','div_password');" class="btn btn-sm btn-primary">
						<i class="fa fa-floppy-o" aria-hidden="true"></i> Ganti Password
				  </button>
				  <button type="reset" class="btn btn-sm btn-danger">
						<i class="fa fa-refresh" aria-hidden="true"></i> Reset
				  </button>
				  <div id="div_password">&nbsp;</div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div>&nbsp;</div>
</div>