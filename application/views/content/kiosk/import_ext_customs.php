<div class="col-md-7">
	<!--
	<div class="row"> 
		 <div class="col-md-3">
			 <div class="inpt-form-group">
				 <label for="username">CARI DATA BC</label>
				 <div class="inpt-group dropdown-select-icon"> 
					<select name="svc_dokumen" id="svc_dokumen" class="inpt-control select">
						<option value="SPB">SPPB</option>
					</select>
				</div> 
			</div>
		</div>
		<div class="col-md-4">
			 <div class="inpt-form-group">
				 <label for="username">&nbsp;</label>
				 <div class="inpt-group"> 
					<input type="text" name="svc_nomor" id="svc_nomor" class="inpt-control key-full" placeholder="NOMOR">
				</div> 
			</div>
		</div>
		<div class="col-md-3">
			 <div class="inpt-form-group">
				 <label for="username">&nbsp;</label>
				 <div class="inpt-group"> 
					<input type="text" name="svc_tgl" id="svc_tgl" class="inpt-control date" placeholder="TANGGAL">
				</div> 
			</div>
		</div>
		<div class="col-md-2">
			<div class="inpt-form-group">
				 <label for="username">&nbsp;</label>
				 <div class="inpt-group"> 
					<button type="button" onclick="get_customs('spb');" class="btn btn-primary"><i class="fa fa-search"></i> Cari</button>
				</div> 
			</div>
		</div>
	</div>
	<div>&nbsp;</div>
	<div>&nbsp;</div>
	-->
	<div class="row">
		<div class="col-md-5">
			 <div class="inpt-form-group">
				 <label for="username">DOKUMEN PENGAJUAN</label>
				 <div class="inpt-group dropdown-select-icon"> 
					<select name="req_dokumen" id="req_dokumen" class="inpt-control select">
						<option value="">- PILIH DOKUMEN</option>
						<?php if($request_type != ""): ?>
							<option value="<?php echo $request_type; ?>" selected><?php echo doc_name($request_type); ?></option>
							<?php if(count($arr_doc) > 0): ?>
								<?php foreach($arr_doc as $a => $b): ?>
									<option value="<?php echo $a; ?>"><?php echo $b; ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						<?php else: ?>
							<?php if(count($arr_doc) > 0): ?>
								<option value="PIB" selected>PIB</option>
								<?php foreach($arr_doc as $a => $b): ?>
									<option value="<?php echo $a; ?>"><?php echo $b; ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						<?php endif; ?>
					</select>
				</div> 
			</div>
		</div>
		<div class="col-md-4">
			 <div class="inpt-form-group">
				 <label for="username">&nbsp;</label>
				 <div class="inpt-group"> 
					<input type="text" name="req_nomor" id="req_nomor" class="inpt-control key-full" placeholder="NOMOR" value="<?php echo $request_no; ?>" mandatory="yes">
				</div> 
			</div>
		</div>
		<div class="col-md-3">
			 <div class="inpt-form-group">
				 <label for="username">&nbsp;</label>
				 <div class="inpt-group"> 
					<input type="text" name="req_tanggal" id="req_tanggal" class="inpt-control date" placeholder="TANGGAL" value="<?php echo $request_date; ?>" mandatory="yes">
				</div> 
			</div>
		</div>
	</div>
	<div class="row"> 
		 <div class="col-md-5">
			 <div class="inpt-form-group">
				 <label for="username">DOKUMEN RESPONSE</label>
				 <div class="inpt-group dropdown-select-icon"> 
					<select name="res_dokumen" id="res_dokumen" class="inpt-control select" mandatory="yes">
						<option value="">- PILIH DOKUMEN</option>
						<?php if($response_type != ""): ?>
							<option value="<?php echo $response_type; ?>" selected><?php echo doc_name($response_type); ?></option>
							<?php if(count($arr_doc) > 0): ?>
								<?php foreach($arr_doc as $a => $b): ?>
									<option value="<?php echo $a; ?>"><?php echo $b; ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						<?php else: ?>
							<option value="SPB" selected>SPPB</option>
							<?php if(count($arr_doc) > 0): ?>
								<?php foreach($arr_doc as $a => $b): ?>
									<option value="<?php echo $a; ?>"><?php echo $b; ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						<?php endif; ?>
					</select>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			 <div class="inpt-form-group">
				 <label for="username">&nbsp;</label>
				 <div class="inpt-group"> 
					<input type="text" name="res_nomor" id="res_nomor" class="inpt-control key-full" placeholder="NOMOR" value="<?php echo $response_no; ?>" mandatory="yes">
				</div> 
			</div>
		</div>
		<div class="col-md-3">
			 <div class="inpt-form-group">
				 <label for="username">&nbsp;</label>
				 <div class="inpt-group"> 
					<input type="text" name="res_tanggal" id="res_tanggal" class="inpt-control date" placeholder="TANGGAL" value="<?php echo $response_date; ?>" mandatory="yes">
				</div> 
			</div>
		</div>
	</div>
</div>
<div class="col-md-5">
	<div class="row"> 
		 <div class="col-md-12">
			 <div class="inpt-form-group">
				 <label for="username">DATA KONTAINER</label>
				 <div class="inpt-group"> 
					<table class="tabelajax">
						<thead>
							<tr>
								<th width="1%">NO.</th>
								<th width="60%">KONTAINER</th>
								<th width="30%">UKURAN</th>
								<th width="9%">F/E</th>
							</tr>
						</thead>
						<tbody id="div_spb">
							<?php if(count($arrcont) > 0 ): ?>
								<?php $no = 1; foreach($arrcont as $cont): ?>
									<tr>
										<td><?php echo $no++; ?></td>
										<td><?php echo $cont['cont_no']; ?></td>
										<td><?php echo get_isocode($cont['isocode'],'size'); ?></td>
										<td><?php echo $cont['full_empty']; ?></td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="4"><center>Data kontainer tidak ditemukan</center></td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div> 
			</div>
		</div>
	</div>
</div>
<script>
function get_customs(act){
	var svc_dokumen = $('#svc_dokumen').val();
	var svc_nomor = $('#svc_nomor').val();
	var svc_tgl = $('#svc_tgl').val();
	var url = site_url+'/kiosk/get_customs/'+act+'/'+Math.random();
	var index = 0;
	if(act=="spb"){
		document.getElementById("res_dokumen").selectedIndex = 1;
	}
	Loading(true);
	$.post(url,{svc_dokumen:svc_dokumen,svc_nomor:svc_nomor,svc_tgl:svc_tgl},
		function(data){
			$('#req_nomor').val(data.no_req);
			$('#req_tanggal').val(data.tgl_req);
			$('#res_nomor').val(data.no_res);
			$('#res_tanggal').val(data.tgl_res);
			$('#div_'+act).html(data.html);
			Loading(false);
	}, "json");
}
$(function(){
	date('date');
	keyboard('key-full');
});
</script>