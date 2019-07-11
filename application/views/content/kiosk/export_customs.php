<div class="col-md-7">
	<div class="row"> 
		<div class="col-md-3">
			 <div class="inpt-form-group">
				 <label for="svc_kpbc">KODE KPBC</label>
				 <div class="inpt-group"> 
					<input type="text" name="svc_kpbc" id="svc_kpbc" class="inpt-control key-full" placeholder="KPBC" value="040300">
				</div> 
			</div>
		</div>
	</div>
	<div>&nbsp;</div>
	<div>&nbsp;</div>
	<div class="row">
		<div class="col-md-5">
			 <div class="inpt-form-group">
				 <label for="req_dokumen">DOKUMEN PENGAJUAN</label>
				 <div class="inpt-group dropdown-select-icon">
					<select name="req_dokumen" id="req_dokumen" class="inpt-control select">
						<option value="">- PILIH DOKUMEN</option>
						<?php if($request_type != ""): ?>
							<option value="<?php echo $request_type; ?>" selected><?php echo $request_type; ?></option>
						<?php else: ?>
							<option value="PEB" selected>PEB</option>
						<?php endif; ?>
						<?php if(count($arr_doc) > 0): ?>
							<?php foreach($arr_doc as $a => $b): ?>
								<option value="<?php echo $a; ?>"><?php echo $b; ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div> 
			</div>
		</div>
		<div class="col-md-4">
			 <div class="inpt-form-group">
				 <label for="req_nomor">&nbsp;</label>
				 <div class="inpt-group"> 
					<input type="text" name="req_nomor" id="req_nomor" class="inpt-control key-full" placeholder="NOMOR" value="<?php echo $request_no; ?>" mandatory="yes">
				</div> 
			</div>
		</div>
		<div class="col-md-3">
			 <div class="inpt-form-group">
				 <label for="req_tanggal">&nbsp;</label>
				 <div class="inpt-group"> 
					<input type="text" name="req_tanggal" id="req_tanggal" class="inpt-control date" placeholder="TANGGAL" value="<?php echo $request_date; ?>" mandatory="yes">
				</div> 
			</div>
		</div>
	</div>
	<div class="row"> 
		 <div class="col-md-5">
			 <div class="inpt-form-group">
				 <label for="res_dokumen">DOKUMEN RESPONSE</label>
				 <div class="inpt-group dropdown-select-icon"> 
					<select name="res_dokumen" id="res_dokumen" class="inpt-control select" mandatory="yes">
						<option value="">- PILIH DOKUMEN</option>
						<?php if($response_type != ""): ?>
							<option value="<?php echo $response_type; ?>" selected><?php echo $response_type; ?></option>
						<?php else: ?>
							<option value="NPE" selected>NPE</option>
						<?php endif; ?>
						<?php if(count($arr_doc) > 0): ?>
							<?php foreach($arr_doc as $a => $b): ?>
								<option value="<?php echo $a; ?>"><?php echo $b; ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			 <div class="inpt-form-group">
				 <label for="res_nomor">&nbsp;</label>
				 <div class="inpt-group"> 
					<input type="text" name="res_nomor" id="res_nomor" class="inpt-control key-full" placeholder="NOMOR" value="<?php echo $response_no; ?>" mandatory="yes">
				</div> 
			</div>
		</div>
		<div class="col-md-3">
			 <div class="inpt-form-group">
				 <label for="res_tanggal">&nbsp;</label>
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
				 <label>DATA KONTAINER</label>
				 <div class="inpt-group"> 
					<table class="tabelajax">
						<thead>
							<tr>
								<th width="1%">NO.</th>
								<th width="60%">KONTAINER</th>
								<th width="39%">UKURAN</th>
							</tr>
						</thead>
						<tbody id="div_npe">
							<?php if(count($arrcont) > 0 ): ?>
								<?php $no = 1; foreach($arrcont as $cont): ?>
									<?php if($cont['cont_no'] != ""): ?>
										<tr>
											<td><?php echo $no++; ?></td>
											<td><?php echo $cont['cont_no']; ?></td>
											<td><?php echo get_isocode($cont['isocode'],'size'); ?></td>
										</tr>
									<?php endif; ?>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="3"><center>Data kontainer tidak ditemukan</center></td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div> 
			</div>
		</div>
	</div>
	<div>&nbsp;</div>
</div>
<script>
function get_customs(act){
	var svc_dokumen = $('#svc_dokumen').val();
	var svc_nomor = $('#svc_nomor').val();
	var svc_kpbc = $('#svc_kpbc').val();
	var url = site_url+'/kiosk/get_customs/'+act+'/'+Math.random();
	var index = 0;
	if(act=="npe") index = 1;
	document.getElementById("res_dokumen").selectedIndex = index;
	Loading(true);
	$.post(url,{svc_dokumen:svc_dokumen,svc_nomor:svc_nomor,svc_kpbc:svc_kpbc},
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