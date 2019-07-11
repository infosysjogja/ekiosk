<div class="panel">
  <div class="ribbon ribbon-bookmark">
  	<span class="ribbon-inner">
		<i class="fa fa-server" aria-hidden="true"></i> Konfirmasi
    </span>
  </div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div style="text-align:center"><small class="help-block" id="div-message">&nbsp;</small></div>
  <div class="panel-body container-fluid">
    <div class="row">
      <div class="col-sm-12">
        <form name="form_cont" id="form_cont" class="form-horizontal" role="form" method="post" autocomplete="off" popup="1">
          <div class="panel-body container-fluid" id="f-cont">
            <div class="row">
              <div class="form-group">
                <label class="col-sm-12 control-label"><center><h2>Apakah ada transaksi lain ?</h2></center></label>
              </div>
			  <div class="form-group">
			   <div class="col-sm-12">
				<center>
				  <button type="button" onclick="confirm('Y');" class="btn btn-xl btn-primary">
						<i class="fa fa-check" aria-hidden="true"></i> YA
				  </button>
				   <button type="button" onclick="confirm('N');" class="btn btn-xl btn-danger">
						<i class="fa fa-times" aria-hidden="true"></i> TIDAK
				  </button>
				</center>
                </div>
			  </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
function confirm(val){
	var url = site_url+'/home/confirm/execute';
	$.post(url,{id:val},
		function(data){
			close_popup(1);
			window.location.href = data.url;
	}, "json");
}
</script>