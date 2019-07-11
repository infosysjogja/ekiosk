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
	<body>
		<div id="header">
			<table align="center" border="0" width="100%">
				<tr>
					<td style="font-size:11px"><center><h4>PROFORMA TEST<h4></center></td>
				</tr>
				<tr>
					<td style="font-size:11px"><center><h3>123456767890<h3></center></td>
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
						<td width="16%"><center><b>Tarif</b></center></td>
						<td width="19%"><center><b>Total</b></center></td>
					</tr>
				</thead>
			</table>
			<br>
			<div style="font-size:7px">Dicetak pada : <?php echo date('d M Y H:i'); ?></div>
		</div>
	</body>
</html>