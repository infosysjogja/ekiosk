<div class="col-md-12">
	<div class="row">
		<label class="col-md-3 control-label margin-top-10">KAPAL / VOYAGE</label>
		 <div class="col-md-9">
			<div class="inpt-form-group">
				<div class="inpt-group dropdown-select-icon">
					<select name="vessel" id="vessel" class="inpt-control select" onchange="get_change(this.value,'kiosk/get_combobox/vessel_port','spod'); get_change(this.value,'kiosk/get_combobox/vessel_agent','agent'); set_data(this.value,'eta|etd|yot|yct','kiosk/execute_export/datavessel'); get_change_header('change_header','1'); return false;" mandatory="yes">
						<option value="">PILIH KAPAL / VOYAGE</option>
						<?php if($vessel_c != ""): ?>
							<option value="<?php echo $vessel_c; ?>" selected><?php echo $vessel_name_c; ?></option>
						<?php endif; ?>
						<?php if(count($arr_vessel_m) > 0): ?>
							<?php foreach($arr_vessel_m as $id => $value): ?>
								<option value="<?php echo $id; ?>"><?php echo $value; ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div> 
			</div>
		</div>
	</div>
	<div class="row">
		<label class="col-md-3 control-label margin-top-10">PELABUHAN TRANSIT</label>
		<div class="col-md-9">
			<div class="inpt-form-group">
				<div class="inpt-group dropdown-select-icon">
					<span id="div_spod">
						<select name="spod" id="spod" class="inpt-control select" mandatory="yes" onchange="get_change_header('change_header','1');">
							<option value="">PILIH PELABUHAN TRANSIT</option>
							<?php if($spod_c != ""): ?>
								<option value="<?php echo $spod_c; ?>" selected><?php echo $spod_name_c; ?></option>
							<?php endif; ?>
							<?php if(count($arr_spod_c) > 0): ?>
								<?php foreach($arr_spod_c as $spod): ?>
									<option value="<?php echo $spod['ID']; ?>"><?php echo $spod['NAME']; ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</span>
				</div> 
			</div>
		</div>
	</div> 
	<div class="row">
		<label class="col-md-3 control-label margin-top-10">PELABUHAN TUJUAN</label>
		<div class="col-md-4">
			<div class="inpt-form-group">
				<div class="inpt-group dropdown-select-icon">
					<input type="text" name="pod" id="pod" class="inpt-control keyboard" placeholder="PELABUHAN TUJUAN KODE" value="<?php echo trim(substr($pod_name_c,0,6)); ?>" mandatory="yes" onblur="get_port(this.value,'pod_text'); get_change_header('change_header','1');">
				</div> 
			</div>
		</div>
		<div class="col-md-5">
			<div class="inpt-form-group">
				<div class="inpt-group dropdown-select-icon">
					<input type="text" name="pod_text" id="pod_text" class="inpt-control" placeholder="PELABUHAN TUJUAN NAMA" value="<?php echo $pod_name_c; ?>" mandatory="yes" readonly>
				</div> 
			</div>
		</div>
	</div>
	<div class="row"> 
		<label class="col-md-3 control-label margin-top-10">AGENT</label>
		<div class="col-md-9">
			<div class="inpt-form-group">
				<div class="inpt-group dropdown-select-icon">
					<span id="div_agent">
						<select name="agent" id="agent" class="inpt-control select" mandatory="yes" onchange="get_change_header('change_header','1');">
							<option value="">PILIH AGENT</option>
							<?php if($agent_c != ""): ?>
								<option value="<?php echo $agent_c; ?>" selected><?php echo $agent_name_c; ?></option>
							<?php endif; ?>
							<?php if(count($arr_agent_c) > 0): ?>
								<?php foreach($arr_agent_c as $agent): ?>
									<option value="<?php echo $agent['ID']; ?>"><?php echo $agent['NAME']; ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</span>
				</div> 
			</div>
		</div>
	</div>
	<div class="row">
		<label class="col-md-3 control-label margin-top-10">ETA</label>
		<div class="col-md-4">
			<div class="inpt-form-group">
				<div class="inpt-group">
					<input type="text" name="eta" id="eta" class="inpt-control" placeholder="ETA" value="<?php echo $eta_c; ?>" readonly mandatory="yes">
				</div> 
			</div>
		</div>
		<label class="col-md-1 control-label margin-top-10">ETD</label>
		<div class="col-md-4">
			<div class="inpt-form-group"> 
				<div class="inpt-group">
					<input type="text" name="etd" id="etd" class="inpt-control" placeholder="ETD" value="<?php echo $etd_c; ?>" readonly mandatory="yes">
				</div> 
			</div>
		 </div>
	</div>
	<div class="row">
		<label class="col-md-3 control-label margin-top-10">YOT</label>
		<div class="col-md-4">
			<div class="inpt-form-group">
				<div class="inpt-group">
					<input type="text" name="yot" id="yot" class="inpt-control" placeholder="YOT" value="<?php echo $yot_c; ?>" readonly mandatory="yes">
				</div> 
			</div>
		</div>
		<label class="col-md-1 control-label margin-top-10">YCT</label>
		<div class="col-md-4">
			<div class="inpt-form-group"> 
				<div class="inpt-group">
					<input type="text" name="yct" id="yct" class="inpt-control" placeholder="YCT" value="<?php echo $yct_c; ?>" readonly mandatory="yes">
				</div> 
			</div>
		 </div>
	</div>
</div> 
<input type="hidden" name="change_header" id="change_header" class="inpt-control" value="0" readonly mandatory="yes">
<script>
function get_port(id,obj){
	$.post(site_url+'/kiosk/get_data/port',{id:id},
		function(data){
			$('#'+obj).val(data);
	}, "html");
}
$(function(){
	$('.keyboard').keypad({
		keypadOnly: false, 
		layout: $.keypad.qwertyLayout
	});
	autocomplete('pod','/kiosk/get_combobox/mst_port',function(event, ui){
		$('#pod').val(ui.item.code);
		$('#pod_text').val(ui.item.param);
	});
})
</script>