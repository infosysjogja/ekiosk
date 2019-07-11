<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>PROFORMA</title>
		<style>
			@media print {
				@page {
					margin: 0 auto;
					sheet-size:79mm 250mm;
				}
				html {
					direction: rtl;
				}
				html,body{
					margin:0;
					padding:0;
					font-family:Arial;
					font-size:11px;
				}
				#header {
					width: 95mm;
					margin: auto;
					/*padding: 10px;*/
					/*border: 2px dotted #000;*/
					text-align: justify;
				}
				#detail{
					width: 98mm;
					margin: auto;
					/*padding: 10px;*/
					/*border: 2px dotted #000;*/
					text-align: justify;
				}
				/*span {
					display: inline-block;
					min-width: 350px;
					white-space: nowrap;
					background: red;
				}*/
				.text-center{text-align: center;}
			}
		</style>
	</head>
	<body onload="window.print();">
		<div id="header">
			<table align="center" border="0" width="100%">
				<tr>
					<td><center><h4>PROFORMA <?php echo $this->session->userdata('DOCUMENT'); ?><h4></center></td>
				</tr>
				<tr>
					<td><center><h3><?php echo $arrdata['data']['header']['proforma_no']; ?><h3></center></td>
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
				 <tr>
					<td>Paid Through</td>
					<td>:</td>
					<td>&nbsp;</td> 
				</tr>
				 <tr>
					<td>Order No.</td>
					<td>:</td>
					<td><?php echo $this->session->userdata('booking_no'); ?></td>
				</tr>
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
						<td width="15%"><center><b>Tarif</b></center></td>
						<td width="20%"><center><b>Total</b></center></td>
					</tr>
				</thead>
				<tbody>
					<?php 
					foreach($arrdata['data']['detail_tarif']['loop'] as $inv){
						$arrinvoice[$inv['code']][] = $inv;
					}
					$html = "";
					foreach($arrinvoice as $field => $detail){
						$arr_tot = array();
						if(!in_array($field,array('STM'))){
							foreach($detail as $dtl){
								$arrsum[] = $dtl['total'];
							}
						}
						if(!in_array($field,array('ADM','STM'))){
							if(array_key_exists(0, $detail)){
								foreach($detail as $a => $b){
									$arr_tot[] = $b['total'];
								}
								$html .= '<tr>';
								$html .= '	<td>'.$detail[0]['desc'].' //.</td>';
								$html .= '	<td><center>'.$detail[0]['jumlah'].'</center></td>';
								$html .= '	<td><center>'.$detail[0]['uom'].'</center></td>';
								$html .= '	<td><center>'.number_format($detail[0]['tarif'],0,',','.').'</center></td>';
								$html .= '	<td style="text-align:right;">'.number_format(array_sum($arr_tot),0,',','.').'</td>';
								$html .= '</tr>';
								foreach($detail as $a => $b){
									if(!in_array($field,array('#CR','GTP','LLI','PT'))){
										$html .= '<tr>';
										$html .= '	<td>&nbsp;&nbsp;<i>'.$b['line_item'].'</i></td>';
										$html .= '	<td><center>&nbsp;</center></td>';
										$html .= '	<td><center>&nbsp;</center></td>';
										$html .= '	<td><center>&nbsp;</center></td>';
										$html .= '	<td><center>&nbsp;</center></td>';
										$html .= '</tr>';
									}
								}
							}else{
								$html .= '<tr>';
								$html .= '	<td>'.$detail['desc'].' //.</td>';
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
					</tr>';
					<tr>
						<td colspan="5" style="border-bottom:1px dotted">&nbsp;</td>
					</tr>
					<tr>
						<td>Biaya Administrasi</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td style="text-align:right;"><?php echo number_format($arrinvoice['ADM'][0]['total'],0,',','.'); ?></td>
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
						<td style="text-align:right;"><?php echo number_format(($arrinvoice['ADM'][0]['total_pay']-$arrinvoice['STM'][0]['total']-array_sum($arrsum)),0,',','.'); ?></td>
					</tr>
					<tr>
						<td>BEA MATERAI LUNAS</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td style="text-align:right;"><?php echo number_format($arrinvoice['STM'][0]['total'],0,',','.'); ?></td>
					</tr>
					<tr>
						<td><h3>JUMLAH PEMBAYARAN</h3></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td colspan="2" style="text-align:right;"><h3><?php echo "Rp. ".number_format($arrinvoice['ADM'][0]['total_pay'],0,',','.'); ?></h3></td>
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
			?>
		</div>
	</body>
</html>