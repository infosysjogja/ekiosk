<div class="rms-wizard-header">
	<table width="100%" height="150px" border="0">
		<tr style="height:75px">
			<td style="padding:5px;width:10%" rowspan="3"><img src="<?php echo base_url('assets/images/logo_npct1.png'); ?>" width="150px"></td>
			<td style="width:1%;background-color:#FCB347" rowspan="3">&nbsp;</td>
			<td style="width:84%;background-color:#124387" colspan="3"><h2 class="title" style="color:#FFFFFF;font-size:30px">KIOSK NPCT1</h2></td>
			<td style="width:5%;background-color:#FFFFFF;color:red;cursor:pointer;font-size:30px;text-align:center" onclick="signout();"><img src="<?php echo base_url('assets/images/sign-out.png'); ?>"></td>
		</tr>
		<tr style="background-color:#FEDF83">
			<td style="width:15%;padding-left:10px;color:#0074d9;font-size:16px"><span><b>CONSIGNEE / SHIPPER</b></span></td>
			<td style="width:1%;text-align:center;font-size:16px">:</td>
			<td style="padding-left:10px;color:#0074d9;font-size:16px"><b><?php echo $this->session->userdata('COMPANY_KIOSK'); ?></b></td>
			<td rowspan="2" style="width:5%;color:red;cursor:pointer;font-size:30px;text-align:center"><?php echo $this->session->userdata('CODE_QUEUE'); ?></td>
		</tr>
		<tr style="background-color:#FEDF83">
			<td style="padding-left:10px;color:#0074d9;font-size:16px"><b>USERNAME KIOSK</b></td>
			<td style="text-align:center;font-size:16px">:</td>
			<td style="padding-left:10px;color:#0074d9;font-size:16px"><b><?php echo $this->session->userdata('USER_KIOSK'); ?></b></td>
		</tr>
	</table>
</div>