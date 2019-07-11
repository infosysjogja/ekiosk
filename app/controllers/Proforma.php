<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Proforma extends CI_Controller {
	public $content;
	public function __construct() {
        parent::__construct();
		$this->connect = $this->load->database('kiosk', TRUE);
		$this->pg = $this->load->database('kiosk',TRUE);
		$this->tcpay = $this->load->database('tcpay',TRUE);
    }
	
	public function index(){
		$headers  = '<link href="'.base_url().'assets/css/bootstrap.min.css" rel="stylesheet">';
		$headers .= '<link href="'.base_url().'assets/css/bootstrap-extend.min.css" rel="stylesheet">';
		$headers .= '<link href="'.base_url().'assets/css/site.min.css" rel="stylesheet">';
		$headers .= '<link href="'.base_url().'assets/vendor/jquery-ui/jquery-ui.min.css" rel="stylesheet">';
		$headers .= '<link href="'.base_url().'assets/css/font-awesome.min.css" rel="stylesheet">';
		$headers .= '<link href="'.base_url().'assets/css/bootstrap-grid.css" rel="stylesheet">';
		$headers .= '<link href="'.base_url().'assets/css/multistep.home.min.css" rel="stylesheet">';
		$headers .= '<link href="'.base_url().'assets/css/animate.css" rel="stylesheet">';
		$headers .= '<link href="'.base_url().'assets/css/newtable.css" rel="stylesheet">';
		$headers .= '<link href="'.base_url().'assets/vendor/sweetalert/sweetalert.css" rel="stylesheet">';
		$headers .= '<link href="'.base_url().'assets/vendor/themes/facebook.css" rel="stylesheet" >';
		$headers .= '<link href="'.base_url().'assets/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css" rel="stylesheet">';
		$headers .= '<link rel="stylesheet" href="'.base_url().'assets/vendor/jquery-wizard/jquery-wizard.min.css?v2.1.0">';
		$footers  = '<script type="text/javascript" src="'.base_url().'assets/js/jquery.min.js"></script>';
		$footers .= '<script type="text/javascript" src="'.base_url().'assets/vendor/jquery-ui/jquery-ui.min.js"></script>';
		$footers .= '<script type="text/javascript" src="'.base_url().'assets/js/multistep.home.min.js"></script>';
		$footers .= '<script type="text/javascript" src="'.base_url().'assets/js/alerts.js"></script>';
		$footers .= '<script src="'.base_url().'assets/vendor/sweetalert/sweetalert.min.js"></script>';
		$footers .= '<script src="'.base_url().'assets/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.js"></script>';
		$footers .= '<script type="text/javascript" src="'.base_url().'assets/js/script.js"></script>';
		$footers .= '<script src="'.base_url().'assets/vendor/jquery-wizard/jquery-wizard.min.js"></script>';
		$id = $this->session->flashdata('message_id');
		if($id == ""){
			//redirect(base_url());
		}
		$data = array('_title_' 	  => 'KIOSK',
					  '_headers_' 	  => $headers,
					  '_header_' 	  => $this->load->view('content/header_document','',true),
					  '_content_' 	  => $this->load->view('content/proforma/index','',true),
					  '_footers_' 	  => $footers);
		$this->parser->parse('index', $data);
	}
	
	function generate_proforma($act=""){
		error_reporting(0);
		if(strtolower($_SERVER['REQUEST_METHOD']) != "post") {
			echo 'access is forbidden'; exit();
		}else{
			$arrayReturn = array();
			$order_id 		= $this->input->post('id');
			$ref_id 		= $this->input->post('ref_id');
			$direct_approve = $this->input->post('direct_approve');
			$remark 		= $this->input->post('remark');
			$chk_cont 		= $this->session->userdata('chkcontainer');
			$kiosk_number 	= $this->session->userdata('KIOSK_NUMBER');
			$xml = "<proforma>";
				$xml .= "<ref_id>".$ref_id."</ref_id>";
				$xml .= "<order_id>".$order_id."</order_id>";
				$xml .= "<container>";
					foreach($chk_cont as $cont){
						$xml .= "<no_container>".$cont['CONTAINER']."</no_container>";
					}
				$xml .= "</container>";
			$xml .= "</proforma>";
			
			$this->load->library('Nusoap');
			$WSDL = WEBSERVICE;
			$client = new nusoap_client($WSDL,true);
			$error  = $client->getError();
			if($error){
				echo '<h2>Constructor error</h2>'.$error;
				exit();
			}
			$method = 'service';
			$param    = array('username'=>'KIOSK', 'password'=>'KIOSK@2017', 'type'=>'generate_proforma', 'mode'=>'2', 'data'=>$xml);
			$response = $client->call($method,$param);
			$str_xml  = WhiteSpaceXML(trim($response));
			$res      = simplexml_load_string($str_xml);
			$json     = json_encode($res);
			$arrxml   = json_decode($json,TRUE);
			$arrcont  = $arrxml['data']['detail_cont'];
			$arrcustomsempty  = array();
			$arrcustomsdouble = array();
			if(array_key_exists(0, $arrcont['loop'])){
				$index = 0;
				foreach($arrcont['loop'] as $cont){
					$arrconttemp[$index]['CONTAINER'] = escape($cont['container_no']);
					$arrconttemp[$index]['CONT_SIZE'] = escape($cont['container_size']);
					$arrconttemp[$index]['CONT_TYPE'] = escape($cont['container_type']);
					$arrconttemp[$index]['FE'] = escape($cont['container_status']);
					$arrconttemp[$index]['PAID_THROUGH'] = escape($cont['paid_through']);
					if(escape($cont['jml_doc']) == 0){
						$arrcustomsempty[] = escape($cont['jml_doc']);
					}
					if(escape($cont['jml_doc']) > 1){
						$arrcustomsdouble[] = escape($cont['jml_doc']);
					}
					$index++;
				}
			}else{
				if(!empty($arrcont['loop']['container_no'])){
					$arrconttemp[0]['CONTAINER'] = escape($arrcont['loop']['container_no']);
					$arrconttemp[0]['CONT_SIZE'] = escape($arrcont['loop']['container_size']);
					$arrconttemp[0]['CONT_TYPE'] = escape($arrcont['loop']['container_type']);
					$arrconttemp[0]['FE'] = escape($arrcont['loop']['container_status']);
					$arrconttemp[0]['PAID_THROUGH'] = escape($arrcont['loop']['paid_through']);
					if(escape($arrcont['loop']['jml_doc']) == 0){
						$arrcustomsempty[] = escape($arrcont['loop']['jml_doc']);
					}
					if(escape($arrcont['loop']['jml_doc']) > 1){
						$arrcustomsdouble[] = escape($arrcont['loop']['jml_doc']);
					}
				}
			}
			if($arrxml['code'] == "00"){
				if(!empty($arrcustomsempty)){
					$direct_approve = "N";
					$proformaBy = "K".$kiosk_number." (NO Customs)";
				}else{
					if(!empty($arrcustomsdouble)){
						$direct_approve = "N";
						$proformaBy = "K".$kiosk_number." (NO EDI_CSDB)";
					}else{
						if($remark != ""){
							$proformaBy = "K".$kiosk_number." (".$remark.")";
						}else{
							$proformaBy = "K".$kiosk_number;
						}
					}
				}
				#START REPORT#
				$arrcontainer = array();
				$reportid = $this->session->userdata('ID_REPORT');
				foreach($arrconttemp as $cont){
					$arrcontainer[] = trim($cont['CONTAINER']);
				}
				$arrupdate = array('c_proformaissuancekiosk' => date('Y-m-d H:i:s'),
								   'c_proformabykiosk'		 => $proformaBy,
								   'h_proformano' => substr(str_replace('/','',$arrxml['data']['header']['proforma_no']),4));
				$this->pg->where(array('reportid' => $reportid));
				$this->pg->where_in('f_contnumber', $arrcontainer);
				$this->pg->update('treportdetail', $arrupdate);
				#END REPORT#
				$booking_queue = $this->session->userdata('ID_QUEUE');
				
				if($direct_approve == "Y"){
					#UPDATE KIOSK
					$proforma_no = substr(str_replace('/','',$arrxml['data']['header']['proforma_no']),4);
					$APR = "SELECT idproformaapr, proformanum
							FROM tproformaapr
							WHERE proformanum = ".$this->pg->escape($proforma_no);
					$resultapr = $this->pg->query($APR);
					if($resultapr->num_rows() > 0){
						$arrdata = $resultapr->row_array();
						$arrproforma = array('modifby'	=> 'KIOSK', 'modifdate'	=> date('Y-m-d H:i:s'));
						$this->pg->where(array('idproformaapr' => $arrdata['idproformaapr']));
						$this->pg->update('tproformaapr', $arrproforma);
					}else{
						$arrproforma = array('noantrian' 	=> $this->session->userdata('CODE_QUEUE'),
											 'proformanum'	=> $proforma_no, 
											 'createby'		=> 'KIOSK',
											 'createdate'	=> date('Y-m-d H:i:s'));
						$this->pg->insert('tproformaapr', $arrproforma);
					}
					
					#UPDATE TCPAY
					$PAY = "SELECT performa_no
							FROM tcpay_payapprove
							WHERE performa_no = ".$this->pg->escape($proforma_no);
					$resultpay = $this->tcpay->query($PAY);
					if($resultpay->num_rows() > 0){
						$arrdata = $resultpay->row_array();
						$arrproformapay = array('modi_on'	=> date('Y-m-d H:i:s'));
						$this->tcpay->where(array('performa_no' => $proforma_no));
						$this->tcpay->update('tcpay_payapprove', $arrproformapay);
					}else{
						$arrproformapay = array('creat_on' 		=> date('Y-m-d H:i:s'),
												'modi_on'		=> date('Y-m-d H:i:s'), 
												'approve'		=> '1',
												'performa_no'	=> $proforma_no);
						$this->tcpay->insert('tcpay_payapprove', $arrproformapay);
					}
					
					$this->pg->where(array('idqueue' => $booking_queue));
					$this->pg->update('tqueuedata', array('keterangan' => $proformaBy, 'proformanum' => substr(str_replace('/','',$arrxml['data']['header']['proforma_no']),4), 's3' => '2', 'starts3' => date('Y-m-d H:i:s'), 'ends3' => date('Y-m-d H:i:s')));
				}else{
					$this->pg->where(array('idqueue' => $booking_queue));
					$this->pg->update('tqueuedata', array('keterangan' => $proformaBy, 'proformanum' => substr(str_replace('/','',$arrxml['data']['header']['proforma_no']),4), 's3' => '2', 'starts3' => date('Y-m-d H:i:s'), 'ends3' => date('Y-m-d H:i:s')));
				}
				$this->session->set_userdata('pdf', $arrxml);
			}
			$data['arrdata'] = $arrxml;
			$data['arrcont'] = $arrconttemp;
			$data['order_id'] = $order_id;
			$data['directApprove'] = $direct_approve;
			$arrayReturn['returnCode'] = $arrxml['code'];
			$arrayReturn['returnView'] = $this->load->view('content/proforma/view',$data,true);
			echo json_encode($arrayReturn);
		}
	}
	
	function print_data(){
		$arrprint = $this->session->userdata('pdf');
		$arrcont = $arrprint['data']['detail_cont'];
		if(array_key_exists(0, $arrcont['loop'])){
			$index = 0;
			foreach($arrcont['loop'] as $cont){
				$arrconttemp[$index]['CONTAINER'] = escape($cont['container_no']);
				$arrconttemp[$index]['CONT_SIZE'] = escape($cont['container_size']);
				$arrconttemp[$index]['CONT_TYPE'] = escape($cont['container_type']);
				$arrconttemp[$index]['FE'] = escape($cont['container_status']);
				$arrconttemp[$index]['PAID_THROUGH'] = escape($cont['paid_through']);
				$index++;
			}
		}else{
			if(!empty($arrcont['loop']['container_no'])){
				$arrconttemp[0]['CONTAINER'] = escape($arrcont['loop']['container_no']);
				$arrconttemp[0]['CONT_SIZE'] = escape($arrcont['loop']['container_size']);
				$arrconttemp[0]['CONT_TYPE'] = escape($arrcont['loop']['container_type']);
				$arrconttemp[0]['FE'] = escape($arrcont['loop']['container_status']);
				$arrconttemp[0]['PAID_THROUGH'] = escape($arrcont['loop']['paid_through']);
			}
		}
	
		if($arrprint['code'] == "00"){
			ini_set('memory_limit', '256M');
			$this->load->library('mpdf');
			$mpdf = new mPDF('c'); 
			$mpdf->SetJS('this.print(true);');
			$mpdf->use_kwt = true;
			$mpdf->charset_in = 'UTF-8';
			$data['arrdata'] = $arrprint;
			$data['arrcont'] = $arrconttemp;
			
			if($arrprint['data']['header']['proforma_no'] == "NPCT/20180125/00346"){
				//print_r($_SESSION);
			}
			
			$html = $this->load->view('content/proforma/print',$data,true);
			$mpdf->WriteHTML($html);
			$mpdf->Output();
		}else{
			$arrayReturn['code'] = $arrprint['code'];
		}
		echo json_encode($arrayReturn);
	}
	
	function print_data_2(){
		$arrprint = $this->session->userdata('pdf');
		$arrcont = $arrprint['data']['detail_cont'];
		if(array_key_exists(0, $arrcont['loop'])){
			$index = 0;
			foreach($arrcont['loop'] as $cont){
				$arrconttemp[$index]['CONTAINER'] = escape($cont['container_no']);
				$arrconttemp[$index]['CONT_SIZE'] = escape($cont['container_size']);
				$arrconttemp[$index]['CONT_TYPE'] = escape($cont['container_type']);
				$arrconttemp[$index]['FE'] = escape($cont['container_status']);
				$arrconttemp[$index]['PAID_THROUGH'] = escape($cont['paid_through']);
				$index++;
			}
		}else{
			if(!empty($arrcont['loop']['container_no'])){
				$arrconttemp[0]['CONTAINER'] = escape($arrcont['loop']['container_no']);
				$arrconttemp[0]['CONT_SIZE'] = escape($arrcont['loop']['container_size']);
				$arrconttemp[0]['CONT_TYPE'] = escape($arrcont['loop']['container_type']);
				$arrconttemp[0]['FE'] = escape($arrcont['loop']['container_status']);
				$arrconttemp[0]['PAID_THROUGH'] = escape($arrcont['loop']['paid_through']);
			}
		}
	
		if($arrprint['code']=="00"){
			ini_set('memory_limit', '256M');
			$this->load->library('mpdf');
			$mpdf = new mPDF('c'); 
			$mpdf->SetJS('this.print(true);');
			$mpdf->use_kwt = true;
			$mpdf->charset_in = 'UTF-8';
			$data['arrdata'] = $arrprint;
			$data['arrcont'] = $arrconttemp;
			$html = $this->load->view('content/proforma/print',$data,true);
			$mpdf->WriteHTML($html);
			$mpdf->Output();
			exit;
		}else{
			$arrayReturn['code'] = $arrprint['code'];
		}
		echo json_encode($arrayReturn);
	}
	
	function view_data(){
		$this->load->library('Nusoap');
		$WSDL = 'https://ebookingkiosk.npct1.co.id/booking/index.php/service/kiosk_production?wsdl';
		$client = new nusoap_client($WSDL,true);
		$error  = $client->getError();
		if($error){
			echo '<h2>Constructor error</h2>'.$error;
			exit();
		}
		$method = 'service';
		$xml = "<proforma>";
			$xml .= "<ref_id>123</ref_id>";
		$xml .= "</proforma>";
		$param  = array('username'=>'KIOSK', 'password'=>'KIOSK@2017', 'type'=>'generate_proforma', 'mode'=>'2', 'data'=>$xml);
		$response = $client->call($method,$param);
		$str_xml = WhiteSpaceXML(trim($response));
		$res     = simplexml_load_string($str_xml);
		$json    = json_encode($res);
		$arrxml  = json_decode($json,TRUE);
		$arrhdr = array();
		$data['status']	 = $arrxml['code'];
		$data['arrdata'] = $arrxml['data'];
		echo $this->load->view('content/proforma/view',$data,true);
	}
	
	function test_print(){
		ini_set('memory_limit', '256M');
		$this->load->library('mpdf');
		$mpdf = new mPDF('c'); 
		$mpdf->SetJS('this.print(true);');
		$mpdf->use_kwt = true;
		$mpdf->charset_in = 'UTF-8';
		$html = $this->load->view('content/proforma/test_print','',true);
		$mpdf->WriteHTML($html);
		$mpdf->Output();
	}
}