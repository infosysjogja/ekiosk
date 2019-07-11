<?php 
	$voy_in  = (escape($this->session->userdata('voyage_in')) != "")?$this->session->userdata('voyage_in'):"";
	$voy_out = (escape($this->session->userdata('voyage_out')) != "")?$this->session->userdata('voyage_out'):"";
	if($this->session->userdata('DOCUMENT') == "EXPORT"){
		$voyage = $voy_out;
	}else{
		$voyage = $voy_in;
	}
 ?>
<style>
table tr td{ 
    padding:1px;
	border-bottom:1px #DDD solid;
}
</style>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>PROFORMA</title>
	</head>
	<body>
		<?php if(!empty($arrdata['data']['header']['proforma_no'])): ?>
			<div id="proforma">
				<?php 
				if($directApprove == "Y"){
					echo "<h2><code>Silahkan langsung melakukan pembayaran</code></h2>";
				}	
				?>
				<table align="center" border="0" width="50%" style="background:white" cellpadding="5" cellspacing="5">
					<tr>
						<td>
							<div id="header">
								<div><center><h3>PROFORMA <?php echo $this->session->userdata('DOCUMENT'); ?><h3></center></div>
								<div><center><h3><?php echo $arrdata['data']['header']['proforma_no']; ?><h3></center></div>
								<table border="0" width="100%">
									<tr>
										<td width="25%">Kapal / Voyage</td>
										<td width="1%">:</td>
										<td width="74%"><?php echo strtoupper($this->session->userdata('vessel_name'))," / ".$voyage; ?></td>
									</tr>
									 <tr>
										<td>Pelanggan</td>
										<td>:</td>
										<td><?php echo $arrdata['data']['header']['customer_name']; ?></td>
									</tr>
									 <tr>
										<td>NPWP</td>
										<td>:</td>
										<td><?php echo $arrdata['data']['header']['npwp']; ?></td>
									</tr>
									<?php if($this->session->userdata('DOCUMENT') == "EXPORT"): ?>
										<tr>
											<td>POD / Transit</td>
											<td>:</td>
											<td><?php echo $this->session->userdata('pod_name')." - ".$this->session->userdata('spod_name'); ?></td> 
										</tr>
										<tr>
											<td>Order No.</td>
											<td>:</td>
											<td><?php echo $this->session->userdata('booking_no'); ?></td>
										</tr>
									<?php else : ?>
										<tr>
											<td>Paid Through</td>
											<td>:</td>
											<td><?php echo ($arrcont[0]['PAID_THROUGH'] != "0000000")?validate($arrcont[0]['PAID_THROUGH'],'DATE-STR'):"-"; ?></td> 
										</tr>
										<tr>
											<td>Delivery Order No.</td>
											<td>:</td>
											<td><?php echo $this->session->userdata('booking_no'); ?></td>
										</tr>
									<?php endif; ?>
								</table>
							</div>
							<div>&nbsp;</div>
							<div id="detail">
								<table width="100%">
									<thead>
										<tr>
											<td width="45%"><b>Deskripsi</b></td>
											<td width="15%"><center><b>Jumlah</b></center></td>
											<td width="10%"><center><b>UOM</b></center></td>
											<td width="15%"><center><b>Tarif</b></center></td>
											<td width="20%"><center><b>Total</b></center></td>
										</tr>
									</thead>
									<tbody>
										<?php
										$arrinv = array();
										foreach($arrdata['data']['detail_tarif']['loop'] as $inv) {
											$arrinvoice[$inv['code']][$inv['seq']] = $inv;
										}
										if(!empty($arrinvoice)){
											foreach($arrinvoice as $a => $b){
												foreach($b as $c => $d){
													$arrinv[$a][] = $d; 
												}
											}
										}
										
										$sess_user = strtoupper($this->session->userdata('USER_KIOSK'));
										if($sess_user == "KIOSK"){
											#print_r($arrinv); 
										}
										
										$html = "";
										foreach($arrinv as $field => $detail){
											if(!in_array($field,array('STM'))){
												foreach($detail as $dtl){
													$arrsum[] = $dtl['total'];
												}
											}
											if(!in_array($field,array('ADM','STM'))){
												if(array_key_exists(0, $detail)){
													$html .= '<tr>';
													$html .= '	<td>'.$detail[0]['desc'].'</td>';
													$html .= '	<td><center>&nbsp;</center></td>';
													$html .= '	<td><center>&nbsp;</center></td>';
													$html .= '	<td><center>&nbsp;</center></td>';
													$html .= '	<td style="text-align:right;">&nbsp;</td>';
													$html .= '</tr>';
													foreach($detail as $a => $b){
														$arr_tot[] = $b['total'];
														$html .= '<tr>';
														$html .= '	<td>&nbsp;&nbsp;<i>'.$b['line_item'].'</i></td>';
														$html .= '	<td><center>'.str_replace('.000','',$b['jumlah']).'</center></td>';
														$html .= '	<td><center>'.$b['uom'].'</center></td>';
														$html .= '	<td><center>'.number_format($b['tarif'],0,',','.').'</center></td>';
														$html .= '	<td><center>'.number_format($b['total'],0,',','.').'</center></td>';
														$html .= '</tr>';
													}
												}else{
													$html .= '<tr>';
													$html .= '	<td>'.$detail['desc'].'</td>';
													$html .= '	<td><center>'.$detail['jumlah'].'</center></td>';
													$html .= '	<td><center>'.$detail['uom'].'</center></td>';
													$html .= '	<td><center>'.number_format($detail['tarif'],0,',','.').'</center></td>';
													$html .= '	<td style="text-align:right;">'.number_format($detail['total'],0,',','.').'</td>';
													$html .= '</tr>';
												}
											}
										}
										echo $html;
										?>
										<tr>
											<td colspan="5" style="border-bottom:1px dotted">&nbsp;</td>
										</tr>
										<tr>
											<td>Biaya Administrasi</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td style="text-align:right;"><?php echo number_format($arrinv['ADM'][0]['total'],0,',','.'); ?></td>
										</tr>
										<tr>
											<td>Dasar Pengenaan Pajak (DPP)</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td style="text-align:right;"><?php echo number_format(array_sum($arrsum),0,',','.'); ?></td>
										</tr>
										<tr>
											<td>Jumlah PPN</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td style="text-align:right;"><?php echo number_format(($arrinv['ADM'][0]['total_pay']-$arrinv['STM'][0]['total']-array_sum($arrsum)),0,',','.'); ?></td>
										</tr>
										<tr>
											<td>BEA MATERAI LUNAS</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td style="text-align:right;"><?php echo number_format($arrinv['STM'][0]['total'],0,',','.'); ?></td>
										</tr>
										<tr>
											<td><h3>JUMLAH PEMBAYARAN</h3></td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td colspan="2" style="text-align:right;"><h3><?php echo "Rp. ".number_format($arrinv['ADM'][0]['total_pay'],0,',','.'); ?></h3></td>
										</tr>
									</tbody>
								</table>
								<br>
								<?php
									$max_per_row = 2;
									$item_count = 0;
									$no = 1;
									echo "<table width='100%' align='center' border='0'>";
									echo "<tr>";
									echo "<td style='border:1px #000 dotted;' width='50%'><center>Petikemas<center></td>";
									echo "<td style='border:1px #000 dotted;' width='50%'><center>Petikemas</center></td>";
									echo "</tr>";
									echo "<tr>";
									foreach ($arrcont as $cont){
										if($item_count == $max_per_row){
											echo "</tr><tr>";
											$item_count = 0;
										}
										echo "<td style='border:1px #000 dotted;'><center>".$cont['CONTAINER']." / ".substr($cont['CONT_SIZE'],0,2)."' / ".$cont['CONT_TYPE']." / ".$cont['FE']."</center></td>";
										$item_count++;
										$no++;
									}
									echo "</tr>";
									echo "</table>";
								?>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<br>
			<center>
				<button type="button" class="btn btn-primary waves-effect waves-light" onclick="print_div('proforma-data');">
					<i class="fa fa-print" aria-hidden="true"></i>
					<br>
					<span class="text-uppercase hidden-xs">PRINT PROFORMA</span>
				</button>
			</center>
			<!-- PRINT PROFORMA -->
			<div>&nbsp;</div>
			<div>&nbsp;</div>
			<div>&nbsp;</div>
		<?php else: ?>
			<h3>PROFORMA GAGAL DIPROSES, SILAHKAN MEMBAWA DOKUMEN DAN MENUJU LOKET</h3>
		<?php endif; ?>
		<div class="rms-content-body">
			 <div class="row">
				 <div class="col-md-12">
					<center>
						<label><h3>Apakah ada transaksi lain ?</H3></label>
					</center>
				</div>
				<div class="col-md-12">
					<center>
						<button type="button" class="btn btn-primary waves-effect waves-light" onclick="confirm('Y'); return false;">
							<i class="fa fa-check" aria-hidden="true"></i>
							<span class="text-uppercase hidden-xs">YA</span>
						</button>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<button type="button" class="btn btn-danger waves-effect waves-light" onclick="confirm('N'); return false;">
							<i class="fa fa-close" aria-hidden="true"></i>
							<span class="text-uppercase hidden-xs">TIDAK</span>
						</button>
					</center>
				</div> 
			 </div>
		</div>
		<div id="proforma-data" style="display:none">&nbsp;</div>
	</body>
</html>