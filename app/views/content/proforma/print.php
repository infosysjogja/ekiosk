<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>PROFORMA</title>
		<style>
			@media print {
				@page{
					margin:0 auto;
					sheet-size:73mm 250mm;
				}
				html{
					direction: rtl;
				}
				html,body{
					margin-right:0;
					padding-right:0;
					font-family:Arial;
					font-size:9px;
				}
				#header {
					width:100%;
					margin: 0 auto;
					/*padding: 10px;*/
					/*border: 2px dotted #000;*/
					text-align: justify;
				}
				#detail{
					width:100%;
					margin: auto;
					/*padding: 10px;*/
					/*border: 2px dotted #000;*/
					text-align: justify;
				}
				.text-center{text-align:center;}
			}
		</style>
	</head>
	<?php 
	$voy_in  = (escape($this->session->userdata('voyage_in')) != "")?$this->session->userdata('voyage_in'):"";
	$voy_out = (escape($this->session->userdata('voyage_out')) != "")?$this->session->userdata('voyage_out'):"";
	if($this->session->userdata('DOCUMENT') == "EXPORT"){
		$voyage = $voy_out;
	}else{
		$voyage = $voy_in;
	}
	?>
	<body>
		<div id="header">
			<table align="center" border="0" width="100%">
				<tr>
					<td style="font-size:11px"><center><h4>PROFORMA <?php echo $this->session->userdata('DOCUMENT'); ?><h4></center></td>
				</tr>
				<tr>
					<td style="font-size:11px"><center><h3><?php echo $arrdata['data']['header']['proforma_no']; ?><h3></center></td>
				</tr>
			</table>
			<table>
				<tr>
					<td>Kapal / Voyage</td>
					<td>:</td>
					<td><?php echo strtoupper($this->session->userdata('vessel_name'))," / ".$voyage; ?></td>
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
						<td><?php echo ($arrcont[0]['PAID_THROUGH'] != "0000000")?substr(validate($arrcont[0]['PAID_THROUGH'],'DATE-STR'),0,10):"-"; ?></td> 
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
			<table width="100%" border="0">
				<thead>
					<tr>
						<td width="45%"><b>Deskripsi</b></td>
						<td width="15%"><center><b>Jumlah</b></center></td>
						<td width="10%"><center><b>UOM</b></center></td>
						<td width="16%"><center><b>Tarif</b></center></td>
						<td width="19%"><center><b>Total</b></center></td>
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
						<td colspan="4">Dasar Pengenaan Pajak (DPP)</td>
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
						<td colspan="3"><h3>JUMLAH PEMBAYARAN</h3></td>
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
				echo "<td style='border-top:1px dotted;border-bottom:1px dotted;border-right:1px dotted;' width='50%'><center>Petikemas<center></td>";
				echo "<td style='border-top:1px dotted;border-bottom:1px dotted' width='50%'><center>Petikemas</center></td>";
				echo "</tr>";
				echo "<tr>";
				foreach ($arrcont as $cont){
					if ($item_count == $max_per_row){
						echo "</tr><tr>";
						$item_count = 0;
					}
					echo "<td style='border-right:1px dotted;border-left:1px dotted;'><center>".$cont['CONTAINER']." / ".$cont['CONT_SIZE']."' / ".$cont['CONT_TYPE']." / ".$cont['FE']."</center></td>";
					$item_count++;
					$no++;
				}
				echo "</tr>";
				echo "</table>";
				date_default_timezone_set("Asia/Jakarta");
			?>
			<div style="font-size:7px">Dicetak pada : <?php echo date('d M Y H:i'); ?></div>
		</div>
	</body>
</html>