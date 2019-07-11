<script>var index_cont = <?php echo (count($arrdata)>0)?count($arrdata):0; ?></script>
<div class="col-md-12">
	<div class="desc-table">
		<table class="tabelajax-no-border-left" width="100%">
			<tbody>
				<tr>
					<td class="desc-label" width="15%">KAPAL / VOYAGE</td>
					<td width="1%">:</td>
					<td class="desc-val" width="80"><span id="div-vessel"><?php echo ($arrhdr['vessel_name']!="")?strtoupper($arrhdr['vessel_name']." - ".$arrhdr['voyage_in']." / ".$arrhdr['voyage_out']):""; ?></span></td>
				</tr>
				<tr>
					<td class="desc-label">POD / SPOD</td>
					<td>:</td>
					<td class="desc-val"><span id="div-port"><?php echo ($arrhdr['spod']!="")?$arrhdr['spod']." / ".$arrhdr['pod']:$arrhdr['spod']; ?></span></td>
				</tr>
				<tr>
					<td class="desc-label">AGENT</td>
					<td>:</td>
					<td class="desc-val"><span id="div-agent"><?php echo ($arrhdr['agent']['id_cosmos']!="")?$arrhdr['agent']['id_cosmos']." / ".$arrhdr['agent']['name']:""; ?></span></td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" name="call_sign" id="call_sign" value="<?php echo $arrhdr['call_sign']; ?>" readonly="readonly" mandatory="yes"/>
		<input type="hidden" name="vessel_name" id="vessel_name" value="<?php echo $arrhdr['vessel_name']; ?>" readonly="readonly" mandatory="yes"/>
		<input type="hidden" name="voyage_in" id="voyage_in" value="<?php echo $arrhdr['voyage_in']; ?>" readonly="readonly" mandatory="yes"/>
		<input type="hidden" name="voyage_out" id="voyage_out" value="<?php echo $arrhdr['voyage_out']; ?>" readonly="readonly" mandatory="yes"/>
		<input type="hidden" name="spod" id="spod" value="<?php echo $arrhdr['spod']; ?>" readonly="readonly" mandatory="yes"/>
		<input type="hidden" name="pod" id="pod" value="<?php echo $arrhdr['pod']; ?>" readonly="readonly" mandatory="yes"/>
		<input type="hidden" name="agent" id="agent" value="<?php echo $arrhdr['agent']['id_cosmos']; ?>" readonly="readonly" mandatory="yes"/>
		<input type="hidden" name="agent_name" id="agent_name" value="<?php echo $arrhdr['agent']['name']; ?>" readonly="readonly">
	</div>
	<div>&nbsp;</div>
	<!--
	<div class="row">
		<label class="col-md-2 control-label margin-top-10 margin-left-5">NO. KONTAINER</label>
		<div class="col-md-3">
			<div class="inpt-form-group">
				<div class="inpt-group">
					<input type="text" name="cont_no" id="cont_no" class="inpt-control key-full" placeholder="NO. KONTAINER" maxlength="11">
				</div> 
			</div>
		</div>
		<div class="col-md-4">
			<button type="button" onclick="add_cont(); return false;" class="btn btn-primary"><i class="fa fa-plus-circle" aria-hidden="true" ></i> Tambah</button>
		</div>
	</div>
	<div>&nbsp;</div>
	-->
	<div>
		<span class="checkbox-custom checkbox-primary margin-top-5">
			&nbsp;<input id="chkallform-rms-wizard" onclick="tb_chkall('form-rms-wizard',this.checked); chkall_rdc('form-rms-wizard',this.checked);" class="chkall" type="checkbox">
			<label for="chkallform-rms-wizard">PILIH SEMUA DATA</label>
		</span>
	</div>
	<table id="tablecont" class="tabelajax">
		<thead>
			<tr>
				<th width="1%">&nbsp;</th>
				<th width="10%">KONTAINER</th>
				<th width="5%">ISOCODE</th>
				<th width="1%">UKURAN</th>
				<th width="5%">TIPE</th>
				<th width="1%">F/E</th>
				<th width="1%">DG</th>
				<th width="1%">OOG</th>
				<th width="10%">DISCHARGE</th>
				<th width="10%">PLUG IN</th>
				<th width="10%">PLUG OUT</th>
				<th width="10%">RFR. SHIFT</th>
			</tr>
		</thead>
		<tbody>
			<?php if(count($arrdata) > 0): ?>
				<?php $no = 1; foreach($arrdata as $data): $index_cont .= ",".$no; ?>
				<tr id="cont_<?php echo $no; ?>">
					<td>
						<center>
							<span class="checkbox-custom checkbox-primary">
								<input name="chkcontainer[]" id="chkform-rms-wizard" class="chkform-rms-wizard" type="checkbox" value="<?php echo $no; ?>" onclick="tb_chk('form-rms-wizard',this.checked,this.value); check_rdc('form-rms-wizard',this.checked,this.value);"><label for="chkform-rms-wizard"></label>
							</span>
						</center>
					</td>
					<td>
						<?php echo $data['cont_no']; ?>
						<input name="containers[CONT_<?php echo $no; ?>][CONTAINER]" id="CONTAINER_<?php echo $no; ?>" type="hidden" value="<?php echo $data['cont_no']; ?>" maxlength="11" readonly>
					</td>
					<td>
						<?php echo $data['isocode']; ?>
						<input name="containers[CONT_<?php echo $no; ?>][ISOCODE]" id="ISOCODE_<?php echo $no; ?>" type="hidden" value="<?php echo $data['isocode']; ?>" maxlength="4" readonly>
					</td>
					<td>
						<?php echo get_isocode($data['isocode'],'size'); ?>
						<input name="containers[CONT_<?php echo $no; ?>][CONT_SIZE]" id="CONT_SIZE_<?php echo $no; ?>" type="hidden" value="<?php echo get_isocode($data['isocode'],'size'); ?>" maxlength="2" readonly>
					</td>
					<td>
						<?php echo get_isocode($data['isocode'],'type'); ?>
						<input name="containers[CONT_<?php echo $no; ?>][CONT_TYPE]" id="CONT_TYPE_<?php echo $no; ?>" type="hidden" value="<?php echo get_isocode($data['isocode'],'type'); ?>" readonly>
					</td>
					<td>
						<?php echo $data['full_empty']; ?>
						<input name="containers[CONT_<?php echo $no; ?>][FE]" id="FE_<?php echo $no; ?>" type="hidden" value="<?php echo $data['full_empty']; ?>" readonly>
					</td>
					<td>
						<?php echo $data['dg']; ?>
						<input name="containers[CONT_<?php echo $no; ?>][I_CLASS]" id="CLASS_<?php echo $no; ?>" type="hidden" value="<?php echo $data['imo_class']; ?>" readonly>
					</td>
					<td>
						<?php echo ($data['or'] != "" || $data['oh']!="" || $data['ol']!="")?"Y":"N"; ?>
						<input name="containers[CONT_<?php echo $no; ?>][OR]" id="OR_<?php echo $no; ?>" type="hidden" value="<?php echo $data['or']; ?>" readonly>
						<input name="containers[CONT_<?php echo $no; ?>][OH]" id="OH_<?php echo $no; ?>" type="hidden" value="<?php echo $data['oh']; ?>" readonly>
						<input name="containers[CONT_<?php echo $no; ?>][OL]" id="OL_<?php echo $no; ?>" type="hidden" value="<?php echo $data['ol']; ?>" readonly>
					</td>
					<td>
						<?php echo substr(validate($data['in_time'],'DATE-STR'),0,16); ?>
						<input name="containers[CONT_<?php echo $no; ?>][DIS]" id="DIS_<?php echo $no; ?>" type="hidden" value="<?php echo validate($data['in_time'],'DATE-STR'); ?>" readonly>
					</td>
					<td>
						<?php //echo substr(validate($data['rcn_time'],'DATE-STR'),0,16); ?>
						<?php /* ?><input name="containers[CONT_<?php echo $no; ?>][RCN]" id="RCN_<?php echo $no; ?>" type="hidden" value="<?php echo validate($data['rcn_time'],'DATE-STR'); ?>" readonly><?php */ ?>
						<?php echo substr(validate($this->session->userdata('PLUG_IN'),'DATETIME'),0,16); ?>
						<input name="containers[CONT_<?php echo $no; ?>][RCN]" id="RCN_<?php echo $no; ?>" type="hidden" value="<?php echo validate($this->session->userdata('PLUG_IN'),'DATETIME'); ?>" readonly>
					</td>
					<td>
						<?php if($data['reefer']=="Y") : ?>
							<input name="containers[CONT_<?php echo $no; ?>][RDC]" id="RDC_<?php echo $no; ?>" type="<?php echo ($data['reefer']=="Y")?"text":"hidden"; ?>" class="RDC inpt-control <?php echo ($data['reefer']=="Y")?"dateminutes":""; ?>" <?php echo ($data['reefer']=="Y")?"mandatory='no'":""; ?> placeholder="PLUG OUT" maxlength="16" onchange="set_shift(this.value,'<?php echo $no; ?>'); return false;">
						<?php endif; ?>
					</td>
					<td>
						<span id="SHFT_RFR_HTML_<?php echo $no; ?>">&nbsp;</span>
						<input name="containers[CONT_<?php echo $no; ?>][SHFT_RFR]" id="SHFT_RFR_<?php echo $no; ?>" type="hidden" readonly>
					</td>
				</tr>
				<input name="containers[CONT_<?php echo $no; ?>][TEMP]" id="TEMP_<?php echo $no; ?>" type="hidden" value="<?php echo $data['temperature']; ?>" readonly>
				<input name="containers[CONT_<?php echo $no; ?>][I_NO]" id="I_NO_<?php echo $no; ?>" type="hidden" value="<?php echo $data['imo_no']; ?>" readonly>
				<input name="containers[CONT_<?php echo $no; ?>][BRUTO]" id="BRUTO_<?php echo $no; ?>" type="hidden" value="<?php echo $data['bruto']; ?>" readonly>
				<input name="containers[CONT_<?php echo $no; ?>][SEQ]" id="SEQ_<?php echo $no; ?>" type="hidden" value="<?php echo $data['seq']; ?>" readonly>
				<?php $no++; endforeach; ?>
			<?php else: ?>
				<tr id="cont_null">
					<td colspan="13"><center>Data kontainer tidak ditemukan</center></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
	<div>&nbsp;</div>
</div>
<input type="hidden" name="do_no" id="do_no" value="<?php echo $arrpost['no_do']; ?>" readonly="readonly" mandatory="yes"/>
<input type="hidden" name="index_cont" id="index_cont" value="<?php echo $index_cont; ?>" readonly="readonly" mandatory="yes"/>
<input type="hidden" name="tmpchkform-rms-wizard" id="tmpchkform-rms-wizard" value="" readonly="readonly"/>
<input type="hidden" name="PLUG_IN" id="PLUG_IN" value="<?php echo $this->session->userdata('PLUG_IN'); ?>" readonly="readonly"/>
<script>
dateminutes('dateminutes');
keyboard('key-full');

function set_shift(val1, index){
	var paid_through = new Date(formatDatetime($('#date_until').val().substr(0, 10)));
	var paid_rdc = new Date(formatDatetime(val1.substr(0,10)));
	if (paid_rdc > paid_through){
		//alert(123);
		$('#SHFT_RFR_'+index).val("");
		$('#SHFT_RFR_HTML_'+index).html("");
		$('#RDC_'+index).val("");
		swalert('info',"Tanggal plugout harus lebih kecil atau sama dengan tanggal perpanjangan");
		return false;
	}else if (paid_rdc < paid_through){
		//alert(123);
		$('#SHFT_RFR_'+index).val("");
		$('#SHFT_RFR_HTML_'+index).html("");
		$('#RDC_'+index).val("");
		swalert('info',"Tanggal plugout harus lebih besar atau sama dengan tanggal perpanjangan");
		return false;
	}else{
		//var start_date = $('#RCN_'+index).val();
		var start_date = $('#PLUG_IN').val();
		var end_date   = val1;
		var url = site_url+'/kiosk/set_shift/'+Math.random();
		$.post(url,{start_date:start_date,end_date:end_date},
			function(data){
				$('#SHFT_RFR_'+index).val(data.shift);
				$('#SHFT_RFR_HTML_'+index).html(data.shift);
		},'json');
	}
}

function check_rdc(formid,status,id){
	if(status==true){
		$('#RDC_'+id).attr('mandatory','yes');
	}else{
		$('#RDC_'+id).attr('mandatory','no');
	}
}

function add_cont(){
	var cont = $('#cont_no').val();
	if(cont == ""){
		$("#cont_no").css({
			'background-size':'100% 2px, 100% 1px',
			'border':'1px solid red'
		});
		return false;
	}
	var url = site_url+'/kiosk/execute_import/kontainerdetail/add/'+Math.random();
	Loading(true);
	$.post(url,{cont:$('#cont_no').val(),call_sign:$('#call_sign').val(),voyage:$('#voyage_in').val(),no_do:$('#do_no').val()},
		function(data){
			Loading(false);
			if(data.success == 1){
				$('#call_sign').val(data.CALL_SIGN);
				$('#vessel_name').val(data.VESSEL);
				$('#voyage_in').val(data.VOY_IN);
				$('#voyage_out').val(data.VOY_OUT);
				$('#pod').val(data.POD);
				$('#spod').val(data.SPOD);
				$('#agent').val(data.AGENT);
				$('#agent_name').val(data.AGENT_NAME);
				$('#div-vessel').html(data.VESSEL+' / '+data.VOY_IN+' - '+data.VOY_OUT);
				$('#div-port').html(data.POD+' / '+data.SPOD);
				$('#div-agent').html(data.AGENT+' - '+data.AGENT_NAME);
				if (strpos($('#index_cont').val(),",") === false){
					$('#tablecont tbody tr').remove();
				}
				index_cont++;
				var html  = '<tr id="cont_'+index_cont+'">';
					html += "<td>";
					html += "<span class='checkbox-custom checkbox-primary'><center>";
					html += "<input name=\"chkcontainer[]\" id=\"chkform-rms-wizard\" class=\"chkform-rms-wizard\" type=\"checkbox\" value=\""+index_cont+"\" onclick=\"tb_chk('form-rms-wizard',this.checked,this.value);\"><label for=\"chkform-rms-wizard\"></label>";
					html += "</center></span>";
					html += "</td>";
					$.each(data, function(id, value){
						var arrid = id.split("~");
						if(arrid[1] == "show"){
							html += "<td>";
								var addclass = "";
								var mandatory = "";
								var placeholder = "";
								var onchange = "";
								if(arrid[0] == "RCN"){
									var rcn = value;
								}
								if(arrid[0] == "FL_REEFER"){
									addclass = "dateminutes";
									mandatory = "mandatory='no'";
									placeholder = "placeholder='PLUG OUT'";
									onchange = "onchange='set_shift(this.value,\""+index_cont+"\"); return false;'";
								}
								html += "<span id=\""+arrid[0]+"_HTML_"+index_cont+"\">"+value+"</span>";
								html +=	"<input type=\""+arrid[2]+"\" name=\"containers[CONT_"+index_cont+"]["+arrid[0]+"]\" id=\""+arrid[0]+"_"+index_cont+"\" value=\""+value+"\" class=\"inpt-control "+addclass+"\" "+mandatory+" "+placeholder+" "+onchange+"/>";
							html += "</td>";
						}else if(arrid[1] == "hide"){
							html +=	"<input type=\""+arrid[2]+"\" name=\"containers[CONT_"+index_cont+"]["+arrid[0]+"]\" id=\""+arrid[0]+"_"+index_cont+"\" value=\""+value+"\" class=\"inpt-control\"/>";
						}
					});
					html += '</tr>';
					$('#tablecont tbody').append(html);
					$('#index_cont').val($('#index_cont').val()+','+index_cont);
					dateminutes('dateminutes');
					$('#cont_no').val('');
		}else{
			swalert('info',data.message);
		}
	},'json');
}

function chkall_rdc(formid,status){
	if(status == true){
		$('input[id^="chk'+formid+'"]').each(function(i){
			$('.RDC').attr('mandatory','yes');
		});
	}else{
		$('input[id^="chk'+formid+'"]').each(function(i){
		 	$('.RDC').attr('mandatory','no');
		});
	}
}
</script>