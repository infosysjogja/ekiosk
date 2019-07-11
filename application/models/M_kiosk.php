<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class M_kiosk extends CI_Model {
	
	public function __construct(){
		parent::__construct();
		$this->db = $this->load->database('default',TRUE);
		$this->pg = $this->load->database('kiosk',TRUE);
	}
	
	function execute_export($act,$type){
		$arrayReturn = array();
		$message = "";
		$notify = "";
		$url = "";
		$success = 0;
		$this->session->unset_userdata('LOG_ID');
		$doc = $this->session->userdata('DOCUMENT');
		$queue_id = $this->session->userdata('QUEUE');
		$array_dokumen = array('EXPORT');
		$id_log = log_act_hdr($queue_id);
		$this->session->set_userdata('LOG_ID', $id_log);
		$log_hdr = $this->session->userdata('LOG_ID');
		$booking = strtoupper($this->input->post('booking_order'));
		$ip = $_SERVER['REMOTE_ADDR'];
		if(in_array($doc,$array_dokumen)){
			if($act=="datakapal"){
				$popup = false;
				$this->load->library('Nusoap');
				$WSDL = WEBSERVICE;
				$client = new nusoap_client($WSDL,true);
				$error  = $client->getError();
				if($error){
					echo '<h2>Constructor error</h2>'.$error;
					exit();
				}
				$method = 'service';
				$xml = "<booking_order>";
					$xml .= "<order_type>IN</order_type>";
					$xml .= "<booking_no>".$booking."</booking_no>";
				$xml .= "</booking_order>";
				$param  = array('username'=>'KIOSK', 'password'=>'KIOSK@2017', 'type'=>'check_order', 'mode'=>'2', 'data'=>$xml);
				$response = $client->call($method,$param);
				$str_xml = WhiteSpaceXML(trim($response));
				$res     = simplexml_load_string($str_xml);
				$json    = json_encode($res);
				$arrxml  = json_decode($json,TRUE);
				$arrhdr = array();
				$this->clear_session();
				if($arrxml['code'] == "00"){
					$arrdata = $arrxml['data'];
					$success = 1;
					$this->session->set_userdata($arrdata, true);
				}else{
					$message = "Data booking order tidak ditemukan";
					$notify = "Booking order tidak ditemukan, Apakah anda akan menggunakan nomor booking tersebut ?";
					$popup = true;
					$success = 1;
				}
				$page_vessel = $this->page_vessel($arrdata);
				$arrayReturn['url'] = $url;
				$arrayReturn['success'] = $success;
				$arrayReturn['message'] = $message;
				$arrayReturn['notify'] = $notify;
				$arrayReturn['page'] = $page_vessel;
				$arrayReturn['popup'] = $popup;
				echo json_encode($arrayReturn);
			}else if($act=="datavessel"){
				$id = $this->input->post('id');
				$arrid = explode("~",$id);
				$SQL = "SELECT DATE_FORMAT(ETA,'%d-%m-%Y %H:%i:%s') AS ETA, DATE_FORMAT(ETD,'%d-%m-%Y %H:%i:%s') AS ETD,
						DATE_FORMAT(YOT,'%d-%m-%Y %H:%i:%s') AS YOT, DATE_FORMAT(YCT,'%d-%m-%Y %H:%i:%s') AS YCT
						FROM tr_vessel
						WHERE TRIM(CALL_SIGN) = ".$this->db->escape($arrid[0])."
						AND TRIM(VOY_OUT) = ".$this->db->escape($arrid[1])."
						ORDER BY ID DESC LIMIT 0,1";
				$result = $this->db->query($SQL);
				if($result->num_rows() > 0){
					$arrayReturn['eta'] = $result->row()->ETA;
					$arrayReturn['etd'] = $result->row()->ETD;
					$arrayReturn['yot'] = $result->row()->YOT;
					$arrayReturn['yct'] = $result->row()->YCT;
				}
				echo json_encode($arrayReturn);
			}else if($act=="dokumenbeacukai"){
				$change_header = 0;
				$change_header = $this->input->post('change_header');
				$this->session->set_userdata('change_header',$change_header);
				$vessel = $this->input->post('vessel');
				$arrvessel = explode("~",$vessel);
				$spod = $this->input->post('spod');
				$pod = $this->input->post('pod');
				$agent = $this->input->post('agent');
				$eta = $this->input->post('eta');
				$etd = $this->input->post('etd');
				$arrdata = $this->session->userdata('customs');
				$page_customs = $this->page_customs($arrdata);
				$arrayReturn['success'] = 1;
				$arrayReturn['message'] = "";
				$arrayReturn['page'] = $page_customs;
				echo json_encode($arrayReturn);
			}else if($act=="kontainerdetail"){
				if($type=="add"){
					echo $this->load->view('content/kiosk/export_container_add','',true);
				}else if($type=="edit"){
					$data['arrdata'] = $this->input->post();
					echo $this->load->view('content/kiosk/export_container_edit',$data,true);
				}else{
					$popup 		= false;
					$is_customs = 0;
					$arrconttps = array('TPSONLINE');
					$this->session->set_userdata('arrconttps',$arrconttps);
					$success = 0;
					$msg = "";
					$message = "";
					$error = 0;
					$date_now 	 = strtotime(date('Y-m-d'));
					$response_document = $this->input->post('res_dokumen');
					$response_nomor = $this->input->post('res_nomor');
					$kpbc = $this->input->post('svc_kpbc');
					$npwp = preg_replace('~[\\\\/:*?"<>|.-]~','', $this->session->userdata('NPWP'));
					$req_tanggal = strtotime(validate($this->input->post('req_tanggal'),'DATE'));
					$res_tanggal = strtotime(validate($this->input->post('res_tanggal'),'DATE'));
					$tgl		 = str_replace('-','',$this->input->post('res_tanggal'));
					if($req_tanggal > $date_now){
						$error = 1;
						$msg = "Tanggal dokumen request tidak boleh melebihi tanggal sekarang";
					}else if($res_tanggal > $date_now){
						$error = 1;
						$msg = "Tanggal dokumen response tidak boleh melebihi tanggal sekarang";
					}else{
						$arrcont = $this->session->userdata('containers');
						if($response_document == "NPE"){
							$this->load->library('Nusoap');
							$url	= 'https://tpsonline.beacukai.go.id/tps/service.asmx?wsdl';
							$client = new SoapClient($url,
														array('location' 		=> "https://tpsonline.beacukai.go.id/tps/service.asmx",
															  'uri'      		=> "tps/service.asmx",
															  'style'    		=> SOAP_DOCUMENT,
															  'use'      		=> SOAP_LITERAL
														)
													);
							$param = array(
								array(
									'UserName'	=> 'NCT1',
									'Password'	=> 'NCT1123456',
									'No_PE'		=> substr(trim($response_nomor),0,6),
									'npwp'		=> $npwp,
									'kdKantor'	=> $kpbc
								)
							);
							$response	= $client->__soapCall('GetEkspor_NPE', $param);
							$return 	= $response->GetEkspor_NPEResult;
							$pos = strpos(strtoupper($return), 'NPE');
							if($pos !== false){
								$success = 1;
								$str_xml = WhiteSpaceXML($return);
								$res     = simplexml_load_string($str_xml);
								$json    = json_encode($res);
								$arrxml  = json_decode($json,TRUE);
								$date_now	= date('Y-m-d H:i:s');
								$arrdetail   = $arrxml['NPE']['DETIL'];
								$count_detail = count($arrdetail);
								if($count_detail > 0){
									if(array_key_exists('NPE', $arrxml)){
										if(count($arrcont) > 0){
											if(array_key_exists(0, $arrdetail['CONT'])){
												for($a=0; $a<count($arrdetail['CONT']); $a++){
													$arrcontainers[] = $arrdetail['CONT'][$a]['NO_CONT'];
													$arrcont[$arrdetail['CONT'][$a]['NO_CONT']]['status'] = "OK";
												}
											}else{
												$arrcontainers[] = $arrdetail['CONT']['NO_CONT'];
												$arrcont[$arrdetail['CONT']['NO_CONT']]['status'] = "OK";
											}
										}else{
											if(array_key_exists(0, $arrdetail['CONT'])){
												for($a=0; $a<count($arrdetail['CONT']); $a++){
													$arrcontainers[] = $arrdetail['CONT'][$a]['NO_CONT'];
												}
											}else{
												$arrcontainers[] = $arrdetail['CONT']['NO_CONT'];
											}
										}
										$this->session->set_userdata('arrconttps', $arrcontainers);
										$is_customs = count($arrcontainers);
									}
									$this->session->set_userdata('containers', $arrcont);
								}else{
									$popup	 = true;
									$message = "Dokumen Bea Cukai tidak ditemukan, apakah anda akan menggunakan dokumen tersebut ?";
								}
							}else{
								$popup	 = true;
								$message = "Dokumen Bea Cukai tidak ditemukan, apakah anda akan menggunakan dokumen tersebut ?";
								if(count($arrcont) > 0){
									foreach($arrcont as $a => $b){
										$arrcont[$a]['status'] = "NOK";
									}
								}
								$this->session->set_userdata('containers', $arrcont);
							}
						}else if($response_document == "KBE"){
							$this->load->library('Nusoap');
							$url	= 'https://tpsonline.beacukai.go.id/tps/service.asmx?wsdl';
							$client = new SoapClient($url,
														array('location' 		=> "https://tpsonline.beacukai.go.id/tps/service.asmx",
															  'uri'      		=> "tps/service.asmx",
															  'style'    		=> SOAP_DOCUMENT,
															  'use'      		=> SOAP_LITERAL
														)
													);
							$param = array(
								array(
									'UserName'		=> 'NCT1',
									'Password'		=> 'NCT1123456',
									'No_PKBE'		=> substr(trim($response_nomor),0,6),
									'TGL_PKBE'		=> $tgl,
									'kdKantor'		=> $kpbc
								)
							);
							$response	= $client->__soapCall('GetEkspor_PKBE', $param);
							$return 	= $response->GetEkspor_PKBEResult;
							$pos = strpos(strtoupper($return), 'PKBE');
							if($pos !== false){
								$success = 1;
								$str_xml = WhiteSpaceXML($return);
								$res     = simplexml_load_string($str_xml);
								$json    = json_encode($res);
								$arrxml  = json_decode($json,TRUE);
								$date_now	= date('Y-m-d H:i:s');
								$arrdetail   = $arrxml['PKBE'];
								$count_detail = count($arrdetail);
								if($count_detail > 0){
									if(array_key_exists('PKBE', $arrxml)){
										if(count($arrcont) > 0){
											$arrcontainers[] = $arrdetail['NO_CONT'];
											$arrcont[$arrdetail['NO_CONT']]['status'] = "OK";
										}else{
											$arrcontainers[] = $arrdetail['NO_CONT'];
										}
									}
									$this->session->set_userdata('arrconttps', $arrcontainers);
									$is_customs = count($arrcontainers);
									$this->session->set_userdata('containers', $arrcont);
								}else{
									$popup	 = true;
									$message = "Dokumen Bea Cukai tidak ditemukan, apakah anda akan menggunakan dokumen tersebut ?";
								}
								/*
								$WSDL = WEBSERVICE;
								$kclient = new nusoap_client($WSDL,true);
								$error  = $kclient->getError();
								if($error){
									echo '<h2>Constructor error</h2>'.$error;
									exit();
								}
								$paramcustoms = array('username'=>'KIOSK', 'password'=>'KIOSK@2017', 'type'=>'set_customs', 'mode'=>'B23', 'data'=>$return);
								$kclient->call('service',$paramcustoms);
								*/
							}else{
								$popup	 = true;
								$message = "Dokumen Bea Cukai tidak ditemukan, apakah anda akan menggunakan dokumen tersebut ?";
								if(count($arrcont) > 0){
									foreach($arrcont as $a => $b){
										$arrcont[$a]['status'] = "NOK";
									}
								}
								$this->session->set_userdata('containers', $arrcont);
							}
						}else{
							$popup	 = true;
							$message = "Dokumen Bea Cukai tidak ditemukan, apakah anda akan menggunakan dokumen tersebut ?";
							if(count($arrcont) > 0){
								foreach($arrcont as $a => $b){
									$arrcont[$a]['status'] = "NOK";
								}
							}
							$this->session->set_userdata('containers', $arrcont);
						}
					}
					if($error == 0){
						$arrdata = $this->session->userdata('containers');
						$page_container = $this->page_container($arrdata);
						$success = 1;
					}else{
						$page_container = "";
						$message = $msg;
					}
					#print_r($arrdata); echo "xx"; die();
					$this->session->set_userdata('is_customs', $is_customs);
					$arrayReturn['success'] = $success;
					$arrayReturn['message'] = "";
					$arrayReturn['notify'] = $message;
					$arrayReturn['page'] = $page_container;
					$arrayReturn['popup'] = $popup;
					echo json_encode($arrayReturn);
				}
			}else if($act=="isocode"){
				$key = $this->input->post('key');
				$SQL = "SELECT SIZE, HEIGHT, TYPE
						FROM td_isocode
						WHERE ID = ".$this->db->escape(strtoupper($key));
				$result = $this->db->query($SQL);
				if($result->num_rows() > 0){
					$arrayReturn['size'] = $result->row()->SIZE;
					$arrayReturn['height'] = $result->row()->HEIGHT;
					$arrayReturn['type'] = $result->row()->TYPE;
				}else{
					$arrayReturn['size'] = "";
					$arrayReturn['height'] = "";
					$arrayReturn['type'] = "";
					$arrayReturn['desc'] = "";
				}
				echo json_encode($arrayReturn);
			}else if($act=="konfirmasidata"){
				$error = 0;
				$arrdata = $this->input->post();
				foreach($arrdata['chkcontainer'] as $field => $value){
					$arrchk[] = $value;
				}
				$arrdetail = $arrdata['containers'];
				if(count($arrchk) > 0){
					foreach($arrchk as $a => $b){
						foreach($arrdetail['CONT_'.$b] as $c => $d){
							$arrcont[$a][$c] = strtoupper($d);
						}
					}
					#print_r($arrcont); die();
					if(count($arrcont) > 0){
						foreach($arrcont as $cont){
							$arrtempcont[] = $cont['CONTAINER'];
							if($cont['ISOCODE'] == ""){
								$error = 1;
								$message = "Terdapat data yang harus diisi";
							}
						}
						$container = implode("~",$arrtempcont);
						$this->load->library('Nusoap');
						$WSDL = WEBSERVICE;
						$client = new nusoap_client($WSDL,true);
						$error  = $client->getError();
						if($error){
							echo '<h2>Constructor error</h2>'.$error;
							exit();
						}
						$method = 'service';
						$xml = "<paid>";
							$xml .= "<vessel_id></vessel_id>";
							$xml .= "<booking_order>".strtoupper(trim($arrdata['booking_order']))."</booking_order>";
							$xml .= "<container_no>".$container."</container_no>";
						$xml .= "</paid>";
						$param  = array('username'=>'KIOSK', 'password'=>'KIOSK@2017', 'type'=>'get_paid', 'mode'=>'2', 'data'=>$xml);
						$response = $client->call($method,$param);
						$str_xml = WhiteSpaceXML(trim($response));
						$res     = simplexml_load_string($str_xml);
						$json    = json_encode($res);
						$arrxml  = json_decode($json,TRUE);
						$code 	 = $arrxml['code'];
						$arrpaid = $arrxml['data']['container'];
						if(array_key_exists(0, $arrpaid)){
							foreach($arrpaid as $paid){
								$paid_cont[] = $paid['cotainer_no'];
							}
						}else{
							$paid_cont[] = $arrpaid['cotainer_no'];
						}
						if($code == "08"){
							if(count($arrpaid) > 0){
								$error = 0;
								$message = "Container ".implode(",",$paid_cont)." sudah melakukan pengajuan proforma";
							}
						}else if($code == "09"){
							if(count($arrpaid) > 0){
								$error = 1;
								$message = "Container ".implode(",",$paid_cont)." sudah melakukan pembayaran";
							}
						}
						
						$xmlv = "<request>";
							$xmlv .= "<type>IN</type>";
							$xmlv .= "<booking_order>".strtoupper(trim($arrdata['booking_order']))."</booking_order>";
							$xmlv .= "<container>";
								foreach($arrcont as $cont){
									$xmlv .= "<no_container>".$cont['CONTAINER']."</no_container>";
								}
							$xmlv .= "</container>";
						$xmlv .= "</request>";
						$paramv  	= array('username'=>'KIOSK', 'password'=>'KIOSK@2017', 'type'=>'validate_containers', 'mode'=>'2', 'data'=>$xmlv);
						$responsev	= $client->call($method,$paramv);
						$strxmlv	= WhiteSpaceXML(trim($responsev));
						$resv    	= simplexml_load_string($strxmlv);
						$jsonv   	= json_encode($resv);
						$arrxmlv 	= json_decode($jsonv,TRUE);
						$codev 	 	= $arrxmlv['code'];
						if($codev != "00"){
							$error = 1;
							$message = "Failed, Already boooking order <b>".$arrxmlv['data']['booking_no']."</b>";
						}
					}else{
						$error = 1;
						$message = "Terdapat data yang harus dipilih";
					}
				}else{
					$error = 1;
					$message = "Data kontainer belum dipilih";
				}
				if($error == 0){
					$success = 1;
					$page_confirm = $this->page_confirm($arrdata);
				}
				$arrayReturn['success'] = $success;
				$arrayReturn['notify'] = $message;
				$arrayReturn['page'] = $page_confirm;
				echo json_encode($arrayReturn);
			}else if($act=="submit"){
				$sess_user = strtoupper($this->session->userdata('USER_KIOSK'));
				$arrdata = $this->input->post();
				$npwp = $this->session->userdata('NPWP');
				$customer_id = $this->session->userdata('CUSTOMER_ID');
				$customer_name = $this->session->userdata('CUSTOMER_NAME');
				$customer_name_address = $this->session->userdata('CUSTOMER_ADDRESS');
				$arrdetail = $arrdata['containers'];
				foreach($arrdata['chkcontainer'] as $field => $value){
					$arrchk[] = $value;
				}
				#print_r($arrchk);
				if(count($arrchk) > 0){
					foreach($arrchk as $a => $b){
						foreach($arrdetail['CONT_'.$b] as $c => $d){
							$arrcont[$a][$c] = strtoupper($d);
							$arrcontchk['chkcontainer'][$a][$c] = strtoupper($d);
						}
					}
					$this->session->set_userdata($arrcontchk, true);
				}
				$arraystatus = array();
				$arrheader = array_map('strtoupper', $arrdata);
				$pod = port_name(substr(trim($arrheader['pod']),0,6));
				$spod = port_name(substr(trim($arrheader['spod']),0,6));
				$arrvessel = explode("~",$arrheader['vessel']);
				$this->session->set_userdata('vessel_name', $arrvessel[2]);
				$this->session->set_userdata('voyage_out', $arrvessel[1]);
				$this->session->set_userdata('pod_name', $pod);
				$this->session->set_userdata('spod_name', $spod);
				$this->session->set_userdata('booking_no', $arrheader['booking_order']);
				$xml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
				$xml .= '<booking_order>';
					$xml .= '<force>TRUE</force>';
					$xml .= '<order_type>IN</order_type>';
					$xml .= '<order_status></order_status>';
					$xml .= '<vessel_name>'.$arrvessel[2].'</vessel_name>';
					$xml .= '<call_sign>'.$arrvessel[0].'</call_sign>';
					$xml .= '<voyage_in></voyage_in>';
					$xml .= '<voyage_out>'.$arrvessel[1].'</voyage_out>';
					$xml .= '<spod>'.substr($arrheader['spod'],0,5).'</spod>';
					$xml .= '<pod>'.substr($arrheader['pod'],0,5).'</pod>';
					$xml .= '<booking_no>'.$arrheader['booking_order'].'</booking_no>';
					$xml .= '<customer>'.$customer_name.'</customer>';
					$xml .= '<validity_time></validity_time>';
					$xml .= '<agent>';
						$xml .= '<id_cosmos>'.$arrheader['agent'].'</id_cosmos>';
						$xml .= '<id_kiosk></id_kiosk>';
						$xml .= '<name></name>';
					$xml .= '</agent>';
					$xml .= '<customs>';
						$xml .= '<request_type>'.$arrheader['req_dokumen'].'</request_type>';
						$xml .= '<request_no>'.$arrheader['req_nomor'].'</request_no>';
						$xml .= '<request_date>'.validate($arrheader['req_tanggal'],'DATE-XML-1').'</request_date>';
						$xml .= '<response_type>'.$arrheader['res_dokumen'].'</response_type>';
						$xml .= '<response_no>'.$arrheader['res_nomor'].'</response_no>';
						$xml .= '<response_date>'.validate($arrheader['res_tanggal'],'DATE-XML-1').'</response_date>';
						$xml .= '<kpbc>040300</kpbc>';
					$xml .= '</customs>';
					$xml .= '<customer_id>'.$customer_id.'</customer_id>';
					$xml .= '<containers>';
						foreach($arrcont as $cont){
							$arraystatus[]		= $cont['STATUS'];
							$arraycontstatus[]	= $cont['CONTAINER']."|".$cont['STATUS'];
							$arraycontreq[]		= $cont['CONTAINER'];
							$xml .= '<container>';
								$xml .= '<no_container>'.$cont['CONTAINER'].'</no_container>';
								$xml .= '<isocode>'.$cont['ISOCODE'].'</isocode>';
								$xml .= '<full_empty>'.$cont['FE'].'</full_empty>';
								$xml .= '<bruto></bruto>';
								if(trim($cont['TEMPERATURE']) != ""){
									$xml .= '<reefer>Y</reefer>';
								}else{
									$xml .= '<reefer>N</reefer>';
								}
								$xml .= '<temperature>'.$cont['TEMPERATURE'].'</temperature>';
								$xml .= '<seal_number>'.$cont['SEAL'].'</seal_number>';
								if(trim($cont['I_CLASS']) != ""){
									$xml .= '<dg>Y</dg>';
								}else{
									$xml .= '<dg>N</dg>';
								}
								$xml .= '<imo_class>'.$cont['I_CLASS'].'</imo_class>';
								$xml .= '<imo_no>'.$cont['I_NO'].'</imo_no>';
								$xml .= '<paid_through_date></paid_through_date>';
								$xml .= '<oogs>';
									if(trim($cont['OR']) != ""){
										$xml .= '<oog>';
											$xml .= '<code>OR</code>';
											$xml .= '<value>'.$cont['OR'].'</value>';
										$xml .= '</oog>';
									}
									if(trim($cont['OH']) != ""){
										$xml .= '<oog>';
											$xml .= '<code>OH</code>';
											$xml .= '<value>'.$cont['OH'].'</value>';
										$xml .= '</oog>';
									}
									if(trim($cont['OL']) != ""){
										$xml .= '<oog>';
											$xml .= '<code>OL</code>';
											$xml .= '<value>'.$cont['OL'].'</value>';
										$xml .= '</oog>';
									}
								$xml .= '</oogs>';
								$xml .= '<status>'.$cont['STATUS'].'</status>';
							$xml .= '</container>';
						}
					$xml .= '</containers>';
				$xml .= '</booking_order>';
				#if($this->session->userdata('USER_KIOSK') == "KIOSK"){
					#START REMARK EDO#
					$arrdiffeditps	= array();
					$arrcontorder 	= $this->session->userdata('arrcontorder');
					$arrconttps 	= $this->session->userdata('arrconttps');
					$arrcontreq		= $arraycontreq; 
					$is_edo 		= $this->session->userdata('is_edo');
					$is_customs 	= $this->session->userdata('is_customs');
					$is_request		= count($arraycontstatus);
					$booking_cont	= implode(", ",$arrcontorder);
					$customs_cont	= implode(", ",$arrconttps);
					$request_cont	= implode(", ",$arraycontstatus);
					if(count($arrcontorder) >= count($arrconttps)){
						$arrdiffeditps = array_diff($arrcontorder,$arrconttps);
					}else{
						$arrdiffeditps = array_diff($arrconttps,$arrcontorder);
					}
					$change_header = $this->session->userdata('change_header');
					$remark = "";
					$direct_approve = "N";
					if($is_edo == 0 && $is_customs == 0){
						$remark = "NO EDI";
					}else{
						if($is_edo == 0){
							$remark = "NO e-DO";
						}else if($is_customs == 0){
							$remark = "No Customs";
						}else{
							if(empty($arrdiffeditps)){
								if(!in_array('NOK',$arraystatus)){
									if($change_header == 1){
										$remark = "NO EDI_edit";
									}else{
										$remark = "EDI OK";
										$direct_approve = "Y";
									}
								}else{
									if($is_edo > $is_customs){
										$remark = "NO Cus_Par";
									}else{
										$remark = "NO EDO_Par";
									}
								}
							}else{
								if(!in_array('NOK',$arraystatus)){
									if($change_header == 1){
										$remark = "NO EDI_edit";
									}else{
										$remark = "EDI OK";
										$direct_approve = "Y";
									}
								}else{
									if($is_edo > $is_customs){
										$remark = "NO Cus_Par";
									}else{
										$remark = "NO EDO_Par";
									}
								}
							}
						}
					}
					#echo $remark." - ".$direct_approve."xx"; die();
					#END EMARK EDO#
				#}
				$this->load->library('Nusoap');
				$WSDL = WEBSERVICE;
				$client = new nusoap_client($WSDL,true);
				$error  = $client->getError();
				if($error){
					echo '<h2>Constructor error</h2>'.$error;
					exit();
				}
				$method = 'service';
				$param  = array('username'=>'KIOSK', 'password'=>'KIOSK@2017', 'type'=>'submit_order', 'mode'=>'2', 'data' => $xml);
				$response = $client->call($method, $param);
				$str_xml = WhiteSpaceXML(trim($response));
				$res     = simplexml_load_string($str_xml);
				$json    = json_encode($res);
				$arrxml  = json_decode($json,TRUE);
				$arrhdr  = array();
				$this->session->unset_userdata('pdf');
				
				$BookingCont = $is_edo." : ".$booking_cont;
				$CustomsCont = $is_customs." : ".$customs_cont;
				$RequestCont = $is_request." : ".$request_cont;
				if($arrxml['code'] == "00"){
					$success = 1;
					$message = "Data booking order berhasil diproses";
					$ref_id   = $arrxml['data']['ref_id'];
					$order_id = $arrxml['data']['order_id'];
					$this->session->set_flashdata('message_id',$ref_id."-".$order_id."-".$arrxml['code']."|DATA BERHASIL DIPROSES, SILAHKAN MENUNGGU PROSES SELANJUTNYA|".$direct_approve."|".$remark);
					$r_message = "SUCCESS";
					$remarkProforma = "";
				}else if($arrxml['code'] == "19"){
					$message = "Data booking order gagal dirposes (Timeout)";
					$this->session->set_flashdata('message_id',$ref_id."-".$order_id."-".$arrxml['code']."|DATA GAGAL DIPROSES (TIMEOUT), SILAHKAN MEMBAWA DOKUMEN DAN MENUJU LOKET|".$direct_approve."|".$remark);
					$r_message = "TIMEOUT";
					$remarkProforma = " (NO EDI_TO)";
				}else{
					$message = "Data booking order gagal dirposes";
					$this->session->set_flashdata('message_id',$ref_id."-".$order_id."-".$arrxml['code']."|DATA GAGAL DIPROSES, SILAHKAN MEMBAWA DOKUMEN DAN MENUJU LOKET|".$direct_approve."|".$remark);
					$r_message = "FAILED";
					$remarkProforma = " (NO EDI_TO)";
				}
				$booking_queue  = $this->session->userdata('ID_QUEUE');
				$kiosk_number 	= $this->session->userdata('KIOSK_NUMBER');
				
				$proformaBy 	= "K".$kiosk_number.$remarkProforma;
				$this->pg->where(array('idqueue' => $booking_queue));
				$this->pg->update('tqueuedata', array('keterangan' => $proformaBy, 'bookingno' => $arrheader['booking_order'], 'entryuser' => validate($this->session->userdata('USER_KIOSK')), 'ends2' => date('Y-m-d H:i:s'), 's2' => '2', 'booking_cont' => $BookingCont, 'customs_cont' => $CustomsCont, 'request_cont' => $RequestCont));
				
				#START REPORT#
				$reportid = $this->session->userdata('ID_REPORT');
				$this->pg->where(array('reportid' => $reportid));
				$this->pg->update('treport', array('i_booking_cont' => $BookingCont, 'i_customs_cont' => $CustomsCont, 'i_request_cont' => $RequestCont));
				
				$arrvessel = explode('~',$arrheader['vessel']);
				foreach($arrcont as $cont){
					$SQD = "SELECT reportdetailid
							FROM treportdetail
							WHERE reportid = ".$this->db->escape($reportid)."
							AND f_contnumber = ".$this->db->escape($cont['CONTAINER']);
					$res_d = $this->pg->query($SQD);
					if($res_d->num_rows() > 0){
						$reportdetailid = $res_d->row()->reportdetailid;
						if($cont['CONT_SIZE'] == '20') $teus = 1;
						else $teus = 2;
						$arrupdate = array('reportid' => $reportid,
										   'b_kiosklogin' => validate($this->session->userdata('KIOSK_LOGIN')),
										   'b_userkiosk' => validate($this->session->userdata('USER_KIOSK')),
										   'b_ppjk' => validate($this->session->userdata('COMPANY_KIOSK')),
										   'b_ordersuccess'	  => date('Y-m-d H:i:s'),
										   'b_statusorder'	  => validate($r_message),
										   'f_orderno'	=> validate($arrheader['booking_order']),
										   'f_shipper' => validate($this->session->userdata('CUSTOMER_NAME')),
										   'f_npwp' => validate($this->session->userdata('NPWP')),
										   'f_address' => validate(substr($this->session->userdata('CUSTOMER_ADDRESS'),0,100)),
										   'f_vessel' => validate($arrvessel[2]),
										   'f_voyage' => validate($arrvessel[1]),
										   'f_eta' => validate($arrheader['eta'],'DATETIME'),
										   'f_etd' => validate($arrheader['etd'],'DATETIME'),
										   'f_contnumber' => validate($cont['CONTAINER']),
										   'f_numberofbox' => 1,
										   'f_teus' => validate($teus),
										   'f_size' => validate($cont['CONT_SIZE']),
										   'f_type' => validate($cont['CONT_TYPE']),
										   'f_status' => validate($cont['FE']),
										   'f_pod' => validate(substr($arrheader['pod'],0,5)),
										   'f_spod' => validate(substr($arrheader['spod'],0,5)),
										   'f_document' => validate($this->session->userdata('DOCUMENT')),
										   'g_reqcustdoctype' => validate($arrheader['req_dokumen']),
										   'g_reqcustdocno'	  => validate($arrheader['req_nomor']),
										   'g_reqcustdocdate' => validate($arrheader['req_tanggal'],'DATE'),
										   'g_rescustdoctype' => validate($arrheader['res_dokumen']),
										   'g_rescustdocno'	  => validate($arrheader['res_nomor']),
										   'g_rescustdocdate' => validate($arrheader['res_tanggal'],'DATE'),
										   'i_line' => validate($arrheader['agent']));
						#if($success == 1){
							$arrupdate['c_proformaresponse'] = date('Y-m-d H:i:s');
							#$arrupdate['c_proformabykiosk']  = $proformaBy;
						#}
						$this->pg->where(array('reportdetailid' => $reportdetailid));
						$this->pg->update('treportdetail', $arrupdate);
					}else{
						if($cont['CONT_SIZE'] == '20') $teus = 1;
						else $teus = 2;
						$arrinsert = array('reportid' => $reportid,
										   'b_kiosklogin' => validate($this->session->userdata('KIOSK_LOGIN')),
										   'b_userkiosk' => validate($this->session->userdata('USER_KIOSK')),
										   'b_ppjk' => validate($this->session->userdata('COMPANY_KIOSK')),
										   'b_ordersuccess'	  => date('Y-m-d H:i:s'),
										   'b_statusorder'	  => validate($r_message),
										   'f_orderno'	=> validate($arrheader['booking_order']),
										   'f_shipper' => validate($this->session->userdata('CUSTOMER_NAME')),
										   'f_npwp' => validate($this->session->userdata('NPWP')),
										   'f_address' => validate(substr($this->session->userdata('CUSTOMER_ADDRESS'),0,100)),
										   'f_vessel' => validate($arrvessel[2]),
										   'f_voyage' => validate($arrvessel[1]),
										   'f_eta' => validate($arrheader['eta'],'DATETIME'),
										   'f_etd' => validate($arrheader['etd'],'DATETIME'),
										   'f_contnumber' => validate($cont['CONTAINER']),
										   'f_numberofbox' => 1,
										   'f_teus' => validate($teus),
										   'f_size' => validate($cont['CONT_SIZE']),
										   'f_type' => validate($cont['CONT_TYPE']),
										   'f_status' => validate($cont['FE']),
										   'f_pod' => validate(substr($arrheader['pod'],0,5)),
										   'f_spod' => validate(substr($arrheader['spod'],0,5)),
										   'f_document' => validate($this->session->userdata('DOCUMENT')),
										   'g_reqcustdoctype' => validate($arrheader['req_dokumen']),
										   'g_reqcustdocno'	  => validate($arrheader['req_nomor']),
										   'g_reqcustdocdate' => validate($arrheader['req_tanggal'],'DATE'),
										   'g_rescustdoctype' => validate($arrheader['res_dokumen']),
										   'g_rescustdocno'	  => validate($arrheader['res_nomor']),
										   'g_rescustdocdate' => validate($arrheader['res_tanggal'],'DATE'),
										   'i_line' => validate($arrheader['agent']));
						#if($success == 1){
							$arrinsert['c_proformaresponse'] = date('Y-m-d H:i:s');
							#$arrinsert['c_proformabykiosk']  = $proformaBy;
						#}
						$this->pg->insert('treportdetail', $arrinsert);
					}
				}
				#END REPORT#
				
				$url = base_url('index.php/home/info');
				$this->session->unset_userdata('LOGGED');
				$arrayReturn['success'] = $success;
				$arrayReturn['message'] = $message;
				$arrayReturn['url'] = $url;
				echo json_encode($arrayReturn);
			}
		}else{
			$arrayReturn['success'] = 0;
			$arrayReturn['notify']	= "Akses ditolak, silahkan melakukan pengajuan ulang";
			echo json_encode($arrayReturn);
		}
	}
	
	function page_vessel($arrdata){
		$order_type = escape($arrdata['order_type']);
		$order_status = escape($arrdata['order_status']);
		$vessel_name = escape($arrdata['vessel_name']);
		$call_sign = escape($arrdata['call_sign']);
		$voyage_in = escape($arrdata['voyage_in']);
		$voyage_out = escape($arrdata['voyage_out']);
		$spod = escape($arrdata['spod']);
		$spod_name = escape($arrdata['spod_name']);
		$pod = escape($arrdata['pod']);
		$pod_name = escape($arrdata['pod_name']);
		$booking_no = escape($arrdata['booking_no']);
		$validity_date = escape($arrdata['validity_date']);
		$paid_through_date = escape($arrdata['paid_through_date']);
		$agent_cosmos = escape($arrdata['agent']['id_cosmos']);
		$agent_kiosk = escape($arrdata['agent']['id_kiosk']);
		$agent_name = escape($arrdata['agent']['name']);
		$cust_req_type = escape($arrdata['customs']['request_type']);
		$cust_req_no = escape($arrdata['customs']['request_no']);
		$cust_req_date = escape($arrdata['customs']['request_date']);
		$cust_res_type = escape($arrdata['customs']['response_type']);
		$cust_res_no = escape($arrdata['customs']['response_no']);
		$cust_res_date = escape($arrdata['customs']['response_date']);
		$kpbc = escape($arrdata['customs']['kpbc']);
		$vessel_name_c = $vessel_name." - ".$voyage_out;
		$spod_name_c = $spod." - ".$spod_name;
		$agent_name_c = $agent_cosmos." - ".$agent_name;
		$pod_name_c = ($pod != "")?$pod." - ".$pod_name:"";
		//VESSEL DATA
		$arr_vessel = array();
		$SQL = "SELECT CALL_SIGN, VOY_OUT, ETA, ETD, YOT, YCT
				FROM tr_vessel
				WHERE CALL_SIGN = ".$this->db->escape($call_sign)."
				AND VOY_OUT = ".$this->db->escape($voyage_out)."
				LIMIT 0,1";
		$result = $this->db->query($SQL);
		$arr_vessel = $result->row_array();
		//SPOD
		$arr_spod = array();
		$Q_SPOD = "SELECT DISTINCT B.PORT_KD AS ID, CONCAT(B.PORT_KD,' - ',IFNULL(B.PORT_NAME,'')) AS NAME
				   FROM tr_vessel A
				   INNER JOIN tr_vessel_port B ON B.KD_VESSEL = A.ID
				   WHERE TRIM(A.CALL_SIGN) = ".$this->db->escape($arr_vessel['CALL_SIGN'])."
				   AND TRIM(A.VOY_OUT) = ".$this->db->escape($arr_vessel['VOY_OUT'])."
				   ORDER BY B.PORT_KD ASC";
		$res_spod = $this->db->query($Q_SPOD);
		if($res_spod->num_rows() > 0){
			$arr_spod = $res_spod->result_array();
		}
		//AGENT
		$arr_agent = array();
		$Q_AGENT = "SELECT B.AGENT_COSMOS_KD AS ID, CONCAT(B.AGENT_COSMOS_KD,' - ',IFNULL(B.AGENT_NAME,'')) AS NAME
					FROM tr_vessel A
					INNER JOIN tr_vessel_agents B ON B.KD_VESSEL = A.ID
					WHERE TRIM(A.CALL_SIGN) = ".$this->db->escape($arr_vessel['CALL_SIGN'])."
					AND TRIM(A.VOY_OUT) = ".$this->db->escape($arr_vessel['VOY_OUT'])."
					ORDER BY B.AGENT_COSMOS_KD ASC";
		$res_agent = $this->db->query($Q_AGENT);
		if($res_agent->num_rows() > 0){
			$arr_agent = $res_agent->result_array();
		}
		$data['arr_vessel_c']  = $arr_vessel;
		$data['vessel_c'] 	   = ($call_sign != "")?$call_sign."~".$voyage_out."~".$vessel_name:"";
		$data['vessel_name_c'] = $vessel_name_c;
		$data['arr_spod_c']    = $arr_spod;
		$data['spod_c']   	   = $spod;
		$data['spod_name_c']   = $spod_name_c;
		$data['pod_c']   	   = $pod;
		$data['pod_name_c']    = $pod_name_c;
		$data['arr_agent_c']   = $arr_agent;
		$data['agent_c']  	   = $agent_cosmos;
		$data['agent_name_c']  = $agent_name_c;
		$data['eta_c']  	   = validate($arr_vessel['ETA'],'DATETIME');
		$data['etd_c']  	   = validate($arr_vessel['ETD'],'DATETIME');
		$data['yot_c']  	   = validate($arr_vessel['YOT'],'DATETIME');
		$data['yct_c']  	   = validate($arr_vessel['YCT'],'DATETIME');
		$data['arr_vessel_m']  = $this->get_combobox('vessel');
		return $this->load->view('content/kiosk/export_vessel',$data,true);
	}
	
	function page_customs($arrdata){
		$is_edo = 0;
		$arrcontorder = array('DOONLINE');
		$this->session->set_userdata('arrcontorder', $arrcontorder);
		
		$this->session->unset_userdata('chkcontainer',true);
		$this->session->unset_userdata('v_containers',true);
		$arrcont = $this->session->userdata('containers');
		if(array_key_exists(0, $arrcont['container'])){
			$index = 0;
			foreach($arrcont['container'] as $cont){
				$arrcontainers[] = escape($cont['no_container']);
				$arrconttemp[$cont['no_container']]['cont_no'] = escape($cont['no_container']);
				$arrconttemp[$cont['no_container']]['isocode'] = escape($cont['isocode']);
				$arrconttemp[$cont['no_container']]['full_empty'] = escape($cont['full_empty']);
				$arrconttemp[$cont['no_container']]['bruto'] = escape($cont['bruto']);
				$arrconttemp[$cont['no_container']]['reefer'] = escape($cont['reefer']);
				$arrconttemp[$cont['no_container']]['temperature'] = escape($cont['temperature']);
				$arrconttemp[$cont['no_container']]['dg'] = escape($cont['dg']);
				$arrconttemp[$cont['no_container']]['imo_class'] = escape($cont['imo_class']);
				$arrconttemp[$cont['no_container']]['imo_no'] = escape($cont['imo_no']);
				$arrconttemp[$cont['no_container']]['seal_no'] = escape($cont['seal_no']);
				$arrconttemp[$cont['no_container']]['oogs'] = escape($cont['oogs']);
				$arrconttemp[$cont['no_container']]['in_time'] = escape($cont['in_time']);
				$arrconttemp[$cont['no_container']]['status'] = "NOK";
				$arrconttemp[$cont['no_container']]['billing'] = escape($cont['billing']);
				$index++;
			}
			$this->session->set_userdata('containers', $arrconttemp);
			$this->session->set_userdata('arrcontorder', $arrcontainers);
			$this->session->set_userdata('is_edo', count($arrcontainers));
		}else{
			if(!empty($arrcont['container']['no_container'])){
				$arrcontainers[] = escape($arrcont['container']['no_container']);
				$arrconttemp[$arrcont['container']['no_container']]['cont_no'] = escape($arrcont['container']['no_container']);
				$arrconttemp[$arrcont['container']['no_container']]['isocode'] = escape($arrcont['container']['isocode']);
				$arrconttemp[$arrcont['container']['no_container']]['full_empty'] = escape($arrcont['container']['full_empty']);
				$arrconttemp[$arrcont['container']['no_container']]['bruto'] = escape($arrcont['container']['bruto']);
				$arrconttemp[$arrcont['container']['no_container']]['reefer'] = escape($arrcont['container']['reefer']);
				$arrconttemp[$arrcont['container']['no_container']]['temperature'] = escape($arrcont['container']['temperature']);
				$arrconttemp[$arrcont['container']['no_container']]['dg'] = escape($arrcont['container']['dg']);
				$arrconttemp[$arrcont['container']['no_container']]['imo_class'] = escape($arrcont['container']['imo_class']);
				$arrconttemp[$arrcont['container']['no_container']]['imo_no'] = escape($arrcont['container']['imo_no']);
				$arrconttemp[$arrcont['container']['no_container']]['seal_no'] = escape($arrcont['container']['seal_no']);
				$arrconttemp[$arrcont['container']['no_container']]['oogs'] = escape($arrcont['container']['oogs']);
				$arrconttemp[$arrcont['container']['no_container']]['in_time'] = escape($arrcont['container']['in_time']);
				$arrconttemp[$arrcont['container']['no_container']]['status'] = "NOK";
				$arrconttemp[$arrcont['container']['no_container']]['billing'] = escape($arrcont['container']['billing']);
				$this->session->set_userdata('containers', $arrconttemp);
			}
			$this->session->set_userdata('arrcontorder', $arrcontainers);
			$this->session->set_userdata('is_edo', count($arrcontainers));
		}
		$data['request_type'] = escape($arrdata['request_type']);
		$data['request_no'] = escape($arrdata['request_no']);
		$data['request_date'] = validate(escape($arrdata['request_date']),'DATE-STR');
		$data['response_type'] = escape($arrdata['response_type']);
		$data['response_no'] = escape($arrdata['response_no']);
		$data['response_date'] = validate(escape($arrdata['response_date']),'DATE-STR');
		$data['kpbc'] = escape($arrdata['kpbc']);
		$data['arr_doc'] = $this->get_combobox('doc_customs_exp');
		$data['arrcont'] = $this->session->userdata('containers');
		return $this->load->view('content/kiosk/export_customs',$data,true);
	}
	
	function page_container($arrdata){
		$arrtemp = array();
		if(!empty($arrdata)){
			foreach($arrdata as $index => $cont){
				if(!empty($cont['cont_no'])){
					$arrtemp[$index]['cont_no'] = escape($cont['cont_no']);
					$arrtemp[$index]['isocode'] = escape($cont['isocode']);
					$arrtemp[$index]['full_empty'] = escape($cont['full_empty']);
					$arrtemp[$index]['bruto'] = escape($cont['bruto']);
					$arrtemp[$index]['reefer'] = escape($cont['reefer']);
					$arrtemp[$index]['temperature'] = escape($cont['temperature']);
					$arrtemp[$index]['dg'] = escape($cont['dg']);
					$arrtemp[$index]['imo_class'] = escape($cont['imo_class']);
					$arrtemp[$index]['imo_no'] = escape($cont['imo_no']);
					$arrtemp[$index]['seal_no'] = escape($cont['seal_no']);
					if(escape($cont['oogs']['oog']['code']) == "OR"){
						$arrtemp[$index]['oogs_or'] = escape($cont['oogs']['oog']['value']);
					}else{
						$arrtemp[$index]['oogs_or'] = "";
					}
					if(escape($cont['oogs']['oog']['code']) == "OH"){
						$arrtemp[$index]['oogs_oh'] = escape($cont['oogs']['oog']['value']);
					}else{
						$arrtemp[$index]['oogs_oh'] = "";
					}
					if(escape($cont['oogs']['oog']['code']) == "OL"){
						$arrtemp[$index]['oogs_ol'] = escape($cont['oogs']['oog']['value']);
					}else{
						$arrtemp[$index]['oogs_ol'] = "";
					}
					$arrtemp[$index]['in_time'] = escape($cont['in_time']);
					$arrtemp[$index]['status'] = escape($cont['status']);
					$arrtemp[$index]['billing'] = escape($cont['billing']);
				}
			}
		}
		$data['arrdata'] = $arrtemp;
		return $this->load->view('content/kiosk/export_container',$data,true);
	}
	
	function page_confirm($arrdata){
		$arrsession['npwp'] = $this->session->userdata('NPWP');
		$arrsession['cust_name'] = $this->session->userdata('CUSTOMER_NAME');
		$arrsession['cust_address'] = $this->session->userdata('CUSTOMER_ADDRESS');
		$npwp = $this->session->userdata('NPWP');
		$arrheader = $arrdata;
		$arrdetail = $arrdata['containers'];
		foreach($arrdata['chkcontainer'] as $field => $value){
			$arrchk[] = $value;
		}
		if(count($arrchk) > 0){
			foreach($arrchk as $a => $b){
				foreach($arrdetail['CONT_'.$b] as $c => $d){
					$arrcont[$a][$c] = strtoupper($d);
				}
			}
		}
		$arrheader = array_map('strtoupper', $arrheader);
		$data['arrsess'] = $arrsession;
		$data['arrhdr'] = $arrheader;
		$data['arrcont'] = $arrcont;
		return $this->load->view('content/kiosk/export_confirm',$data,true);
	}
	
	function get_combobox($act){
        $func = get_instance();
        $func->load->model("m_main", "main", true);
        $id = $this->input->post('id');
		$arrayReturn = array();
        if($act == "vessel"){
            $SQL = "SELECT DISTINCT CONCAT(IFNULL(CALL_SIGN,''),'~',IFNULL(VOY_OUT,''),'~',IFNULL(VESSEL_NAME,'')) AS ID,
					CONCAT(IFNULL(VESSEL_NAME,''),' / ', IFNULL(VOY_OUT,'')) AS VESSEL
					FROM tr_vessel
					ORDER BY VESSEL_NAME";
            $arrdata = $func->main->get_combobox($SQL, "ID", "VESSEL", FALSE);
            return $arrdata;
        }else if ($act == "vessel_port"){
			$arrid = explode("~",$id);
            $SQL = "SELECT DISTINCT B.PORT_KD AS ID, CONCAT(B.PORT_KD,' - ',IFNULL(B.PORT_NAME,'')) AS NAME
					FROM tr_vessel A
					INNER JOIN tr_vessel_port B ON B.KD_VESSEL = A.ID
					WHERE TRIM(A.CALL_SIGN) = ".$this->db->escape($arrid[0])."
					AND TRIM(A.VOY_OUT) = ".$this->db->escape($arrid[1])."
					ORDER BY B.PORT_KD ASC";
            $arrdata = $func->main->get_combobox($SQL, "ID", "NAME", "- PILIH PELABUHAN TRANSIT");
			if($id != ""){
				$arrayReturn['result'] = form_dropdown('spod',$arrdata,'','id="spod" class="inpt-control select" mandatory="yes"');
				echo json_encode($arrayReturn);
			}else{
				return $arrdata;
			}
        }else if ($act == "vessel_agent"){
			$arrid = explode("~",$id);
            $SQL = "SELECT B.AGENT_COSMOS_KD AS ID, CONCAT(B.AGENT_COSMOS_KD,' - ',IFNULL(B.AGENT_NAME,'')) AS NAME
					FROM tr_vessel A
					INNER JOIN tr_vessel_agents B ON B.KD_VESSEL = A.ID
					WHERE TRIM(A.CALL_SIGN) = ".$this->db->escape($arrid[0])."
					AND TRIM(A.VOY_OUT) = ".$this->db->escape($arrid[1])."
					ORDER BY B.AGENT_COSMOS_KD ASC";
            $arrdata = $func->main->get_combobox($SQL, "ID", "NAME", "- PILIH AGENT");
			if($id != ""){
				$arrayReturn['result'] = form_dropdown('agent',$arrdata,'','id="agent" class="inpt-control select" mandatory="yes"');
				echo json_encode($arrayReturn);
			}else{
				return $arrdata;
			}
            return $arrdata;
        }else if($act == "doc_customs_exp"){
            $SQL = "SELECT KODE, NAME FROM td_doc_customs WHERE PERMIT IN ('EXP','EXPIMP') AND AKTIF = 'Y'";
            $arrdata = $func->main->get_combobox($SQL, "KODE", "NAME", FALSE);
            return $arrdata;
		 }else if($act == "doc_customs_imp"){
            $SQL = "SELECT KODE, NAME FROM td_doc_customs WHERE PERMIT IN ('IMP','EXPIMP') AND AKTIF = 'Y'";
            $arrdata = $func->main->get_combobox($SQL, "KODE", "NAME", FALSE);
            return $arrdata;
        }else if($act == "mst_port"){
			$post = $this->input->post('term');
            if (!$post) return;
			$SQL = "SELECT CODE, CONCAT(CODE,' - ',NAME) AS NAME
					FROM td_port
					WHERE CODE LIKE '%".$post."%' OR NAME LIKE '%".$post."%'
					LIMIT 5";
			$result = $this->db->query($SQL);
			$banyakData = $result->num_rows();
			$arrayDataTemp = array();
			if($banyakData > 0){
				foreach($result->result() as $row){
					$CODE = strtoupper($row->CODE);
					$NAME = strtoupper($row->NAME);
					$arrayDataTemp[] = array("value"=>$CODE,"label"=>$NAME,"code"=>$CODE,"param"=>$NAME);
				}
			}
			echo json_encode($arrayDataTemp);
        }
    }
	
	function clear_session(){
		$this->session->unset_userdata('order_type');
		$this->session->unset_userdata('order_status');
		$this->session->unset_userdata('vessel_name');
		$this->session->unset_userdata('call_sign');
		$this->session->unset_userdata('voyage_in');
		$this->session->unset_userdata('voyage_out');
		$this->session->unset_userdata('spod');
		$this->session->unset_userdata('spod_name');
		$this->session->unset_userdata('pod');
		$this->session->unset_userdata('pod_name');
		$this->session->unset_userdata('booking_no');
		$this->session->unset_userdata('validity_date');
		$this->session->unset_userdata('paid_through_date');
		$this->session->unset_userdata('agent');
		$this->session->unset_userdata('customs');
		$this->session->unset_userdata('containers');
		$this->session->unset_userdata('arrcontorder');
		$this->session->unset_userdata('arrconttps');
		$this->session->unset_userdata('pdf');
	}
	
	function execute_import($act,$type){
		$arrayReturn = array();
		$message = "";
		$success = 0;
		$notify = "";
		$this->session->unset_userdata('LOG_ID');
		$doc = $this->session->userdata('DOCUMENT');
		$array_dokumen = array('IMPORT');
		
		$queue_id = $this->session->userdata('QUEUE');
		$id_log = log_act_hdr($queue_id);
		$this->session->set_userdata('LOG_ID', $id_log);
		$log_hdr = $this->session->userdata('LOG_ID');
		$booking = strtoupper($this->input->post('no_do'));
		if(in_array($doc,$array_dokumen)){
			if($act=="bookingorder"){
				echo $this->load->view('content/kiosk/import_order','',true);
			}else if($act=="dokumenbeacukai"){
				$popup = false;
				$no_do = strtoupper($this->input->post('no_do'));
				$no_bl = strtoupper($this->input->post('no_bl'));
				$no_bc = strtoupper($this->input->post('no_bc'));
				$tgl_tempo = validate($this->input->post('tgl_tempo'),'DATE');
				$date_until = validate(substr($this->input->post('date_until'),0,10),'DATE');
				$date_now = date('Y-m-d');
				if(strtotime($date_until) > strtotime($tgl_tempo)){
					$notify = "Tanggal DO harus lebih besar dari tanggal pembayaran";
				}else if(strtotime($date_until) < strtotime($date_now)){
					$notify = "Tanggal pembayaran harus lebih besar atau sama dengan tanggal sekarang";
				}else{
					$this->load->library('Nusoap');
					$WSDL = WEBSERVICE;
					$client = new nusoap_client($WSDL,true);
					$error  = $client->getError();
					if($error){
						echo '<h2>Constructor error</h2>'.$error;
						exit();
					}
					$method = 'service';
					$xml = "<booking_order>";
						$xml .= "<order_type>OUT</order_type>";
						$xml .= "<booking_no>".trim($no_do)."~".trim($no_bl)."~".trim($no_bc)."</booking_no>";
					$xml .= "</booking_order>";
					$param  = array('username'=>'KIOSK', 'password'=>'KIOSK@2017', 'type'=>'check_order', 'mode'=>'2', 'data'=>$xml);
					$response = $client->call($method,$param);
					$str_xml = WhiteSpaceXML(trim($response));
					$res     = simplexml_load_string($str_xml);
					$json    = json_encode($res);
					$arrxml  = json_decode($json,TRUE);
					$arrhdr = array();
					$this->clear_session();
					#print_r($arrxml);
					if($arrxml['code'] == "00"){
						$arrdata = $arrxml['data'];
						$expiry_now		= date('Ymd');
						$expiry_date	= escape($arrdata['expiry_date']);
						if($expiry_date != ""){
							if(substr($expiry_date, 0, 8) < $expiry_now){
								#$success = 0;
								#$popup = true;
								#$notify = "Booking order anda sudah expired";
								$success = 1;
								$this->session->set_userdata($arrdata, true);
							}else{
								$success = 1;
								$this->session->set_userdata($arrdata, true);
							}
						}else{
							$success = 1;
							$this->session->set_userdata($arrdata, true);
						}
					}else{
						
						$this->session->set_userdata($arrdata, true);
						$success = 1;
						$popup = true;
						$notify = "Booking order tidak ditemukan, Apakah anda akan menggunakan nomor delivery order tersebut ?";
					}
					$page_customs = $this->pages_customs($arrdata);
				}
				$arrayReturn['success'] = $success;
				$arrayReturn['notify'] = $notify;
				$arrayReturn['popup'] = $popup;
				$arrayReturn['page'] = $page_customs;
				echo json_encode($arrayReturn);
			}else if($act=="kontainerdetail"){
				if($type == "add"){
					$arrdata		  = $this->session->userdata($arrdata);
					$arrcontsess	  = $this->session->userdata('v_containers');
					$arrcontsessstart = $this->session->userdata('containers');
					$cont = $this->input->post('cont');
					$arrconttemp = array();
					if(count($arrcontsessstart) > 0){
						foreach($arrcontsessstart as $a => $b){
							#$arrconttemp[] = $a;
						}
					}
					/*
					if($this->session->userdata('USER_KIOSK') == "KIOSK"){
						print_r($arrconttemp); 
						print_r($_SESSION); 
						echo "xx"; die();
					}
					*/
					if(in_array(strtoupper(trim($cont)),$arrconttemp)){
						$message = "Sudah terdapat kontainer yang sama";
						$arrayReturn['message'] = $message;
						echo json_encode($arrayReturn);
					}else{
						$this->load->library('Nusoap');
						$WSDL = WEBSERVICE;
						$client = new nusoap_client($WSDL,true);
						$error  = $client->getError();
						if($error){
							echo '<h2>Constructor error</h2>'.$error;
							exit();
						}
						$method = 'service';
						$xml = "<validate_container>";
							$xml .= "<type>OUT</type>";
							$xml .= "<booking_no>".strtoupper(trim($arrdata['booking_no']))."</booking_no>";
							$xml .= "<container_no>".strtoupper(trim($cont))."</container_no>";
						$xml .= "</validate_container>";
						$param  = array('username'=>'KIOSK', 'password'=>'KIOSK@2017', 'type'=>'validate_container', 'mode'=>'2', 'data'=>$xml);
						$response = $client->call($method,$param);
						$str_xml = WhiteSpaceXML(trim($response));
						$res     = simplexml_load_string($str_xml);
						$json    = json_encode($res);
						$arrxml  = json_decode($json,TRUE);
						$arrhdr = array();
						if($arrxml['code'] == "00"){
							$arrdata = $arrxml['data'];
							$arrcont = $arrdata['containers']['container'];
							foreach($arrcont as $a => $b){
								if(!empty($b)) $arrtemp[$a] = $b;
								else $arrtemp[$a] = "";
							}
							$arroog = $arrtemp['oogs']['oog'];
							$oog = "N"; $ol = ""; $oh = ""; $or = "";
							if(array_key_exists(0, $arroog)){
								foreach($arroog as $data_oog){
									if(strtoupper($data_oog['code']) == "OR") $or = $data_oog['value'];
									if(strtoupper($data_oog['code']) == "OH") $oh = $data_oog['value'];
									if(strtoupper($data_oog['code']) == "OL") $ol = $data_oog['value'];
								}
								if($or!="" || $oh!="" || $ol!=""){
									$oog = "Y";
								}
							}else{
								if(strtoupper($arroog['code']) == "OR") $or = $arroog['value'];
								if(strtoupper($arroog['code']) == "OH") $oh = $arroog['value'];
								if(strtoupper($arroog['code']) == "OL") $ol = $arroog['value'];
								if($or!="" || $oh!="" || $ol!=""){
									$oog = "Y";
								}
							}
							if($arrtemp['reefer'] == "Y"){
								$fl_reefer = "text";
								$arrayReturn['FL_REEFER~hide~hidden'] = $arrtemp['reefer'];
							}else{
								$fl_reefer = "hidden";
							}
							
							if($arrtemp['billing'] != ""){
								if($arrtemp['billing'] == "FIN"){
									$billing = "INV";
								}else{
									$billing = "PRO";
								}
							}else{
								$billing = "";
							}
							
							$arrcontsess[$arrtemp['no_container']]['seq'] 				= "";
							$arrcontsess[$arrtemp['no_container']]['cont_no'] 			= $arrtemp['no_container'];
							$arrcontsess[$arrtemp['no_container']]['size'] 				= $arrtemp['size'];
							$arrcontsess[$arrtemp['no_container']]['isocode'] 			= $arrtemp['isocode'];
							$arrcontsess[$arrtemp['no_container']]['full_empty'] 		= $arrtemp['full_empty'];
							$arrcontsess[$arrtemp['no_container']]['bruto'] 			= $arrtemp['bruto'];
							$arrcontsess[$arrtemp['no_container']]['reefer'] 			= $arrtemp['reefer'];
							$arrcontsess[$arrtemp['no_container']]['temperature'] 		= $arrtemp['temperature'];
							$arrcontsess[$arrtemp['no_container']]['dg'] 				= $arrtemp['dg'];
							$arrcontsess[$arrtemp['no_container']]['imo_class'] 		= $arrtemp['imo_class'];
							$arrcontsess[$arrtemp['no_container']]['imo_no'] 			= $arrtemp['imo_no'];
							$arrcontsess[$arrtemp['no_container']]['seal_no']			= $arrtemp['seal_no'];
							$arrcontsess[$arrtemp['no_container']]['oogs'] 				= "";
							$arrcontsess[$arrtemp['no_container']]['in_time'] 			= $arrtemp['in_time'];
							$arrcontsess[$arrtemp['no_container']]['rcn_time'] 			= $arrtemp['rcn_time'];
							$arrcontsess[$arrtemp['no_container']]['paid_through_date'] = "";
							$arrcontsess[$arrtemp['no_container']]['status'] 			= "NOK";
							$arrcontsess[$arrtemp['no_container']]['billing'] 			= $billing;
							$this->session->set_userdata('v_containers',$arrcontsess);
							$this->session->set_userdata('containers',$arrcontsess);
							
							$arrayReturn['CONTAINER~show~hidden'] = $arrtemp['no_container'];
							$arrayReturn['ISOCODE~show~hidden'] = $arrtemp['isocode'];
							$arrayReturn['CONT_SIZE~show~hidden'] = get_isocode($arrtemp['isocode'],'size');
							$arrayReturn['CONT_TYPE~show~hidden'] = get_isocode($arrtemp['isocode'],'type');
							$arrayReturn['FE~show~hidden'] = $arrtemp['full_empty'];
							$arrayReturn['DG~show~hidden'] = $arrtemp['dg'];
							$arrayReturn['OOG~show~hidden'] = $oog;
							$arrayReturn['DIS~show~hidden'] = validate($arrtemp['in_time'],'DATE-STR');
							$arrayReturn['RCN~show~hidden'] = ($arrtemp['reefer'] == "Y")?validate($arrtemp['rcn_time'],'DATE-STR'):"";
							$arrayReturn['RDC~show~'.$fl_reefer] = "";
							$arrayReturn['SHFT_RFR~show~hidden'] = "";
							$arrayReturn['TEMP~hide~hidden'] = $arrtemp['temperature'];
							$arrayReturn['I_CLASS~hide~hidden'] = $arrtemp['imo_class'];
							$arrayReturn['I_NO~hide~hidden'] = $arrtemp['imo_no'];
							$arrayReturn['OR~hide~hidden'] = $or;
							$arrayReturn['OH~hide~hidden'] = $oh;
							$arrayReturn['OL~hide~hidden'] = $ol;
							$arrayReturn['BRUTO~hide~hidden'] = $arrtemp['bruto'];
							$arrayReturn['CALL_SIGN'] = $arrdata['call_sign'];
							$arrayReturn['VESSEL'] = $arrdata['vessel_name'];
							$arrayReturn['VOY_IN'] = $arrdata['voyage_in'];
							$arrayReturn['VOY_OUT'] = $arrdata['voyage_out'];
							$arrayReturn['POD'] = $arrdata['pod'];
							$arrayReturn['SPOD'] = $arrdata['spod'];
							$arrayReturn['AGENT'] = $arrdata['agent']['id_cosmos'];
							$arrayReturn['AGENT_NAME'] = $arrdata['agent']['name'];
							$arrayReturn['STATUS~hide~hidden'] = "NOK";
							$arrayReturn['BILLING'] = $billing;
							$arrayReturn['success'] = 1;
							echo json_encode($arrayReturn);
						}else{
							if($arrxml['code'] == "95"){
								$message = "Kontainer ini sudah digunakan dengan nomor delivery order <b>".$arrxml['data']['booking_no']."</b>";
							}else if($arrxml['code'] == "14"){
								$message = "Kontainer tidak ditemukan";
							}
							$arrayReturn['message'] = $message;
							echo json_encode($arrayReturn);
						}
					}
				}else{
					$popup = false;
					$is_customs = 0;
					$arrconttps = array('TPSONLINE');
					$this->session->set_userdata('arrconttps',$arrconttps);
					$this->session->unset_userdata('v_containers');
					$success = 0;
					$msg = "";
					$message = "";
					$error = 0;
					$username  = 'NCT1';
					$password  = 'NCT1123456';
					$npwp 	   = preg_replace('~[\\\\/:*?"<>|.-]~','', $this->session->userdata('NPWP'));
					$nomor 	   = $this->input->post('res_nomor');
					$tgl 	   = str_replace('-','',$this->input->post('res_tanggal'));
					$response_document = $this->input->post('res_dokumen');
					$arrcont = $this->session->userdata('containers');
					$date_now 	 = strtotime(date('Y-m-d'));
					$req_tanggal = strtotime(validate($this->input->post('req_tanggal'),'DATE'));
					$res_tanggal = strtotime(validate($this->input->post('res_tanggal'),'DATE'));
					if($req_tanggal > $date_now){
						$error = 1;
						$msg = "Tanggal dokumen request tidak boleh melebihi tanggal sekarang";
					}else if($res_tanggal > $date_now){
						$error = 1;
						$msg = "Tanggal dokumen response tidak boleh melebihi tanggal sekarang";
					}else{
						if($response_document == "SPB"){
							$this->load->library('Nusoap');
							$url	= 'https://tpsonline.beacukai.go.id/tps/service.asmx?wsdl';
							$client = new SoapClient($url,
														array('location' 		=> "https://tpsonline.beacukai.go.id/tps/service.asmx",
															  'uri'      		=> "tps/service.asmx",
															  'style'    		=> SOAP_DOCUMENT,
															  'use'      		=> SOAP_LITERAL
														)
													);
							$param = array(
								array(
									'UserName'	=> 'NCT1',
									'Password'	=> 'NCT1123456',
									'No_Sppb'	=> strtoupper($nomor),
									'Tgl_Sppb'	=> $tgl,
									'NPWP_Imp'	=> $npwp
								)
							);
							$response	= $client->__soapCall('GetImpor_Sppb', $param);
							$return 	= $response->GetImpor_SppbResult;
							$pos 		= strpos(strtoupper($return), 'SPPB');
							if($pos !== false){
								$success = 1;
								$str_xml = WhiteSpaceXML($return);
								$res     = simplexml_load_string($str_xml);
								$json    = json_encode($res);
								$arrxml  = json_decode($json,TRUE);
								$date_now	= date('Y-m-d H:i:s');
								$arrdetail   = $arrxml['SPPB']['DETIL'];
								$count_detail = count($arrdetail);
								if($count_detail > 0){
									if(array_key_exists('SPPB', $arrxml)){
										if(count($arrcont) > 0){
											if(array_key_exists(0, $arrdetail['CONT'])){
												for($a=0; $a<count($arrdetail['CONT']); $a++){
													$arrcontainers[] = $arrdetail['CONT'][$a]['NO_CONT'];
													$arrcont[$arrdetail['CONT'][$a]['NO_CONT']]['status'] = "OK";
												}
											}else{
												$arrcontainers[] = $arrdetail['CONT']['NO_CONT'];
												$arrcont[$arrdetail['CONT']['NO_CONT']]['status'] = "OK";
											}
										}else{
											if(array_key_exists(0, $arrdetail['CONT'])){
												for($a=0; $a<count($arrdetail['CONT']); $a++){
													$arrcontainers[] = $arrdetail['CONT'][$a]['NO_CONT'];
												}
											}else{
												$arrcontainers[] = $arrdetail['CONT']['NO_CONT'];
											}
										}
										$this->session->set_userdata('arrconttps', $arrcontainers);
										$is_customs = count($arrcontainers);
									}
									$this->session->set_userdata('v_containers', $arrcont);
								}
							}else{
								$this->session->set_userdata('v_containers', $arrcont);
								$popup	 = true;
								$message = "Dokumen Bea Cukai tidak ditemukan, apakah anda akan menggunakan dokumen tersebut ?";
							}
						}else if($response_document == "B23"){
							$this->load->library('Nusoap');
							$url	= 'https://tpsonline.beacukai.go.id/tps/service.asmx?wsdl';
							$client = new SoapClient($url,
														array('location' 		=> "https://tpsonline.beacukai.go.id/tps/service.asmx",
															  'uri'      		=> "tps/service.asmx",
															  'style'    		=> SOAP_DOCUMENT,
															  'use'      		=> SOAP_LITERAL
														)
													);
							$param = array(
								array(
									'UserName'	=> 'NCT1',
									'Password'	=> 'NCT1123456',
									'No_Sppb'	=> strtoupper($nomor),
									'Tgl_Sppb'	=> $tgl,
									'NPWP_Imp'	=> $npwp
								)
							);
							$response	= $client->__soapCall('GetSppb_Bc23', $param);
							$return 	= $response->GetSppb_Bc23Result;
							$pos 		= strpos(strtoupper($return), 'SPPB');
							if($pos !== false){
								$success = 1;
								$str_xml = WhiteSpaceXML($return);
								$res     = simplexml_load_string($str_xml);
								$json    = json_encode($res);
								$arrxml  = json_decode($json,TRUE);
								$date_now	= date('Y-m-d H:i:s');
								$arrdetail   = $arrxml['SPPB']['DETIL'];
								$count_detail = count($arrdetail);
								if($count_detail > 0){
									if(array_key_exists('SPPB', $arrxml)){
										if(count($arrcont) > 0){
											if(array_key_exists(0, $arrdetail['CONT'])){
												for($a=0; $a<count($arrdetail['CONT']); $a++){
													$arrcontainers[] = $arrdetail['CONT'][$a]['NO_CONT'];
													$arrcont[$arrdetail['CONT'][$a]['NO_CONT']]['status'] = "OK";
												}
											}else{
												$arrcontainers[] = $arrdetail['CONT']['NO_CONT'];
												$arrcont[$arrdetail['CONT']['NO_CONT']]['status'] = "OK";
											}
										}else{
											if(array_key_exists(0, $arrdetail['CONT'])){
												for($a=0; $a<count($arrdetail['CONT']); $a++){
													$arrcontainers[] = $arrdetail['CONT'][$a]['NO_CONT'];
												}
											}else{
												$arrcontainers[] = $arrdetail['CONT']['NO_CONT'];
											}
										}
										$is_customs = count($arrcontainers);
										$this->session->set_userdata('arrconttps', $arrcontainers);
									}
									$this->session->set_userdata('v_containers', $arrcont);
								}
							}else{
								$popup	 = true;
								$message = "Dokumen Bea Cukai tidak ditemukan, apakah anda akan menggunakan dokumen tersebut ?";
								$this->session->set_userdata('v_containers', $arrcont);
							}
						}else{
							$popup	 = true;
							$message = "Dokumen Bea Cukai tidak ditemukan, apakah anda akan menggunakan dokumen tersebut ?";
							$this->session->set_userdata('v_containers', $arrcont);
						}
					}
					if($error == 0){
						$arrdata = $this->session->userdata($arrdata);
						$arrcont = $this->session->userdata('v_containers');
						$page_container = $this->pages_container($arrdata,$arrcont);
						$success = 1;
					}else{
						$page_container = "";
						$message = $msg;
					}
					$this->session->set_userdata('is_customs', $is_customs);
					$arrayReturn['success'] = $success;
					$arrayReturn['message'] = "";
					$arrayReturn['notify'] = $message;
					$arrayReturn['page'] = $page_container;
					$arrayReturn['popup'] = $popup;
					echo json_encode($arrayReturn);
				}
			}else if($act=="konfirmasidata"){
				$error = 0;
				$success = 0;
				$npwps = $this->session->userdata('NPWP');
				$arrdata = $this->input->post();
				foreach($arrdata['chkcontainer'] as $field => $value){
					$arrchk[] = $value;
				}
				$arrdetail = $arrdata['containers'];
				if(count($arrchk) > 0){
					foreach($arrchk as $a => $b){
						foreach($arrdetail['CONT_'.$b] as $c => $d){
							$arrcont[$a][$c] = strtoupper($d);
						}
					}
					if(count($arrcont) > 0){
						foreach($arrcont as $cont){
							$arrtempcont[] = $cont['CONTAINER'];
							if($cont['ISOCODE'] == ""){
								$error = 1;
								$message = "Terdapat data yang harus diisi";
							}
							if(!empty($cont['RDC'])){
								$arrtempreefer[] = $cont['RDC'];
							}
							
							if($cont['FL_REEFER'] == "Y"){
								if($cont['RDC'] == ""){
									$error = 1;
									$message = "Terdapat data yang harus diisi";
								}
							}
							
						}
						$userkiosk = $this->session->userdata('USER_KIOSK');
						if(count($arrtempreefer) == -1){
							$error = 1;
							$message = "Data Gagal Diproses";
						}else{
							if($error == 0){
								$container = implode("~",$arrtempcont);
								$this->load->library('Nusoap');
								$WSDL = WEBSERVICE;
								$client = new nusoap_client($WSDL,true);
								$error  = $client->getError();
								if($error){
									echo '<h2>Constructor error</h2>'.$error;
									exit();
								}
								$method = 'service';
								$xml = "<paid>";
									$xml .= "<vessel_id></vessel_id>";
									$xml .= "<booking_order>".strtoupper(trim($arrdata['no_do']))."</booking_order>";
									$xml .= "<container_no>".$container."</container_no>";
								$xml .= "</paid>";
								#echo $xml; die();
								$param  = array('username'=>'KIOSK', 'password'=>'KIOSK@2017', 'type'=>'get_paid', 'mode'=>'2', 'data'=>$xml);
								$response = $client->call($method,$param);
								$str_xml = WhiteSpaceXML(trim($response));
								$res     = simplexml_load_string($str_xml);
								$json    = json_encode($res);
								$arrxml  = json_decode($json,TRUE);
								$code 	 = $arrxml['code'];
								$arrpaid = $arrxml['data']['container'];
								if(array_key_exists(0, $arrpaid)){
									foreach($arrpaid as $paid){
										$paid_cont[] = $paid['cotainer_no'];
									}
								}else{
									$paid_cont[] = $arrpaid['cotainer_no'];
								}
								if($code == "08"){
									if(count($arrpaid) > 0){
										$error = 1;
										$message = "Container ".implode(",",$paid_cont)." sudah melakukan pengajuan proforma";
									}
								}else if($code == "09"){
									if(count($arrpaid) > 0){
										$error = 1;
										$message = "Container ".implode(",",$paid_cont)." sudah melakukan pembayaran";
									}
								}
							}else{
								$error = 1;
								$message = $message;
							}
						}
					}else{
						$error = 1;
						$message = "Terdapat data yang harus dipilih";
					}
				}else{
					$error = 1;
					$message = "Data kontainer belum dipilih";
				}
				if($error == 0){
					$success = 1;
					$page_confirm = $this->pages_confirm($arrdata);
				}
				$arrayReturn['success'] = $success;
				$arrayReturn['notify'] = $message;
				$arrayReturn['page'] = $page_confirm;
				echo json_encode($arrayReturn);
			}else if($act=="submit"){
				$is_request = 0;
				$this->load->library('Nusoap');
				$WSDL = WEBSERVICE;
				$client = new nusoap_client($WSDL,true);
				$error  = $client->getError();
				if($error){
					echo '<h2>Constructor error</h2>'.$error;
					exit();
				}
				$sess_user = strtoupper($this->session->userdata('USER_KIOSK'));
				$arrdata = $this->input->post();
				$npwp = $this->session->userdata('NPWP');
				$customer_id = $this->session->userdata('CUSTOMER_ID');
				$customer_name = $this->session->userdata('CUSTOMER_NAME');
				$customer_name_address = $this->session->userdata('CUSTOMER_ADDRESS');
				$arrdetail = $arrdata['containers'];
				foreach($arrdata['chkcontainer'] as $field => $value){
					$arrchk[] = $value;
				}
				if(count($arrchk) > 0){
					foreach($arrchk as $a => $b){
						foreach($arrdetail['CONT_'.$b] as $c => $d){
							$arrcont[$a][$c] = strtoupper($d);
							$arrcontchk['chkcontainer'][$a][$c] = strtoupper($d);
						}
					}
					$this->session->set_userdata($arrcontchk, true);
				}
				
				$arrayCont 	= array();
				foreach($arrcont as $a => $b){
					$arrayCont[$b['CONTAINER']]['CONTAINER']	= $b['CONTAINER'];
					$arrayCont[$b['CONTAINER']]['ISOCODE']		= $b['ISOCODE'];
					$arrayCont[$b['CONTAINER']]['CONT_SIZE']	= $b['CONT_SIZE'];
					$arrayCont[$b['CONTAINER']]['CONT_TYPE']    = $b['CONT_TYPE'];
					$arrayCont[$b['CONTAINER']]['FE']           = $b['FE'];
					$arrayCont[$b['CONTAINER']]['DG']           = $b['DG'];
					$arrayCont[$b['CONTAINER']]['OOG']          = $b['OOG'];
					$arrayCont[$b['CONTAINER']]['DIS']          = $b['DIS'];
					$arrayCont[$b['CONTAINER']]['RCN']          = $b['RCN'];
					$arrayCont[$b['CONTAINER']]['RDC']          = $b['RDC'];
					$arrayCont[$b['CONTAINER']]['SHFT_RFR']		= $b['SHFT_RFR'];
					$arrayCont[$b['CONTAINER']]['TEMP']			= $b['TEMP'];
					$arrayCont[$b['CONTAINER']]['I_CLASS']		= $b['I_CLASS'];
					$arrayCont[$b['CONTAINER']]['I_NO']			= $b['I_NO'];
					$arrayCont[$b['CONTAINER']]['OR']			= $b['OR'];
					$arrayCont[$b['CONTAINER']]['OH']			= $b['OH'];
					$arrayCont[$b['CONTAINER']]['OL']			= $b['OL'];
					$arrayCont[$b['CONTAINER']]['BRUTO']		= $b['BRUTO'];
					$arrayCont[$b['CONTAINER']]['STATUS']		= $b['STATUS'];
				}
				$xmlc = '<request>';
					$xmlc .= '<cont_type>OUT</cont_type>';
					foreach($arrcont as $cont){
						$xmlc .= '<containers>';
							$xmlc .= '<container_no>'.$cont['CONTAINER'].'</container_no>';
							$xmlc .= '<status>'.$cont['STATUS'].'</status>';
						$xmlc .= '</containers>';
					}
				$xmlc .= '</request>';
				$parameter	= array('username'=>'KIOSK', 'password'=>'KIOSK@2017', 'type'=>'check_customs', 'mode'=>'2', 'data'=>$xmlc);
				$responsec	= $client->call('service',$parameter);
				$strxml 	= WhiteSpaceXML(trim($responsec));
				$resdata    = simplexml_load_string($strxml);
				$jsondata   = json_encode($resdata);
				$arrxmlc 	= json_decode($jsondata,TRUE);
				if($arrxmlc['code'] == "00"){
					$arrayContService = $arrxmlc['data']['containers'];
					if(array_key_exists(0, $arrayContService)){
						foreach($arrayContService as $data){
							$arrayCont[$data['container']]['STATUS'] = $data['status'];
						}
					}else{
						$arrayCont[$arrayContService['container']]['STATUS'] = $arrayContService['status'];
					}
				}
				$arraystatus = array();
				$arrheader = array_map('strtoupper', $arrdata);
				if($arrheader['booking_no'] != ""){
					$arrheader['no_do'] = $arrheader['booking_no'];
				}else{
					$arrheader['no_do'] = $arrheader['no_do'];
				}
				$xml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
				$xml .= '<booking_order>';
					$xml .= '<force>TRUE</force>';
					$xml .= '<order_type>OUT</order_type>';
					$xml .= '<order_status></order_status>';
					$xml .= '<vessel_name>'.$arrheader['vessel_name'].'</vessel_name>';
					$xml .= '<call_sign>'.$arrheader['call_sign'].'</call_sign>';
					$xml .= '<voyage_in>'.$arrheader['voyage_in'].'</voyage_in>';
					$xml .= '<voyage_out>'.$arrheader['voyage_out'].'</voyage_out>';
					$xml .= '<spod>'.substr($arrheader['spod'],0,5).'</spod>';
					$xml .= '<pod>'.substr($arrheader['pod'],0,5).'</pod>';
					$xml .= '<booking_no>'.$arrheader['no_do'].'~'.$arrheader['no_bl'].'~'.$arrheader['res_nomor'].'</booking_no>';
					$xml .= '<customer>'.$customer_name.'</customer>';
					$xml .= '<validity_time>'.validate(substr($arrheader['date_until'],0,10),'DATE-XML-1').'</validity_time>';
					$xml .= '<agent>';
						$xml .= '<id_cosmos>'.$arrheader['agent'].'</id_cosmos>';
						$xml .= '<id_kiosk></id_kiosk>';
						$xml .= '<name>'.$arrheader['agent_name'].'</name>';
					$xml .= '</agent>';
					$xml .= '<customs>';
						$xml .= '<request_type>'.$arrheader['req_dokumen'].'</request_type>';
						$xml .= '<request_no>'.$arrheader['req_nomor'].'</request_no>';
						$xml .= '<request_date>'.validate($arrheader['req_tanggal'],'DATE-XML-1').'</request_date>';
						$xml .= '<response_type>'.$arrheader['res_dokumen'].'</response_type>';
						$xml .= '<response_no>'.$arrheader['res_nomor'].'</response_no>';
						$xml .= '<response_date>'.validate($arrheader['res_tanggal'],'DATE-XML-1').'</response_date>';
						$xml .= '<kpbc>040300</kpbc>';
					$xml .= '</customs>';
					$xml .= '<customer_id>'.$customer_id.'</customer_id>';
					$xml .= '<containers>';
						foreach($arrayCont as $cont){
							$arraystatus[] = $cont['STATUS'];
							$arraycontstatus[] = $cont['CONTAINER']."|".$cont['STATUS'];
							$xml .= '<container>';
								$xml .= '<seq>0</seq>';
								$xml .= '<no_container>'.$cont['CONTAINER'].'</no_container>';
								$xml .= '<isocode>'.$cont['ISOCODE'].'</isocode>';
								$xml .= '<full_empty>'.$cont['FE'].'</full_empty>';
								$xml .= '<bruto>'.$cont['BRUTO'].'</bruto>';
								if(trim($cont['TEMP']) != ""){
									$xml .= '<reefer>Y</reefer>';
								}else{
									$xml .= '<reefer>N</reefer>';
								}
								$xml .= '<temperature>'.$cont['TEMP'].'</temperature>';
								$xml .= '<seal_number></seal_number>';
								if(trim($cont['I_CLASS']) != ""){
									$xml .= '<dg>Y</dg>';
								}else{
									$xml .= '<dg>N</dg>';
								}
								$xml .= '<imo_class>'.$cont['I_CLASS'].'</imo_class>';
								$xml .= '<imo_no>'.$cont['I_NO'].'</imo_no>';
								$xml .= '<oogs>';
									if(trim($cont['OR']) != ""){
										$xml .= '<oog>';
											$xml .= '<code>OR</code>';
											$xml .= '<value>'.$cont['OR'].'</value>';
										$xml .= '</oog>';
									}
									if(trim($cont['OH']) != ""){
										$xml .= '<oog>';
											$xml .= '<code>OH</code>';
											$xml .= '<value>'.$cont['OH'].'</value>';
										$xml .= '</oog>';
									}
									if(trim($cont['OL']) != ""){
										$xml .= '<oog>';
											$xml .= '<code>OL</code>';
											$xml .= '<value>'.$cont['OL'].'</value>';
										$xml .= '</oog>';
									}
								$xml .= '</oogs>';
								if(trim($cont['TEMP']) != ""){
									if($cont['RDC'] != ""){
										$xml .= '<paid_through_date>'.validate($cont['RDC'].':00','DATE-XML-1').'</paid_through_date>';
									}else{
										$xml .= '<paid_through_date>'.validate(substr($arrheader['date_until'],0,10),'DATE-XML-1').'235900</paid_through_date>'; 
									}
								}else{
									$xml .= '<paid_through_date>'.validate(substr($arrheader['date_until'],0,10),'DATE-XML-1').'235900</paid_through_date>';
								}
								$xml .= '<status>'.$cont['STATUS'].'</status>';
							$xml .= '</container>';
						}
					$xml .= '</containers>';
				$xml .= '</booking_order>';
				
				#START REMARK#
				$arrdiffeditps	= array();
				$arrcontorder 	= $this->session->userdata('arrcontorder');
				$arrconttps 	= $this->session->userdata('arrconttps');
				$arrcontreq		= $arraycontreq; 
				$is_edo 		= $this->session->userdata('is_edo');
				$is_customs 	= $this->session->userdata('is_customs');
				$is_request		= count($arraycontstatus);
				$booking_cont	= implode(", ",$arrcontorder);
				$customs_cont	= implode(", ",$arrconttps);
				$request_cont	= implode(", ",$arraycontstatus);
				if(count($arrcontorder) >= count($arrconttps)){
					$arrdiffeditps = array_diff($arrcontorder,$arrconttps);
				}else{
					$arrdiffeditps = array_diff($arrconttps,$arrcontorder);
				}
				$remark = "";
				$BookingCont = $is_edo." : ".$booking_cont;
				$CustomsCont = $is_customs." : ".$customs_cont;
				$RequestCont = $is_request." : ".$request_cont;
				
				$direct_approve = "N";
				if($is_edo == 0 && $is_customs == 0){
					$remark = "NO EDI";
				}else{
					if($is_edo == 0){
						$remark = "NO e-DO";
					}else if($is_customs == 0){
						$remark = "No Customs";
					}else{
						if(empty($arrdiffeditps)){
							if(!in_array('NOK',$arraystatus)){
								$remark = "EDI OK";
								$direct_approve = "Y";
							}else{
								if($is_edo > $is_customs){
									$remark = "NO Cus_Par";
								}else{
									$remark = "NO EDO_Par";
								}
							}
						}else{
							if(!in_array('NOK',$arraystatus)){
								$remark = "EDI OK";
								$direct_approve = "Y";
							}else{
								if($is_edo > $is_customs){
									$remark = "NO Cus_Par";
								}else{
									$remark = "NO EDO_Par";
								}
							}
						}
					}
				}
				#echo $direct_approve." - ".$remark;
				#echo "xx"; die();
				#END REMARK#
				
				$method = 'service';
				$param  = array('username'=>'KIOSK', 'password'=>'KIOSK@2017', 'type'=>'submit_order', 'mode'=>'2', 'data'=>$xml);
				$response = $client->call($method,$param);
				$str_xml = WhiteSpaceXML(trim($response));
				$res     = simplexml_load_string($str_xml);
				$json    = json_encode($res);
				$arrxml  = json_decode($json,TRUE);
				$arrhdr = array();
				$this->session->unset_userdata('pdf');
				$this->session->set_userdata("booking_no", $arrheader['no_do']);
				$this->session->set_userdata('vessel_name', $arrheader['vessel_name']);
				$this->session->set_userdata('voyage_in', $arrheader['voyage_in']);
				
				if($arrxml['code'] == "00"){
					$success = 1;
					$message = "Booking order berhasil diproses";
					$ref_id   = $arrxml['data']['ref_id'];
					$order_id = $arrxml['data']['order_id'];
					$this->session->set_flashdata('message_id',$ref_id."-".$order_id."-".$arrxml['code']."|DATA BERHASIL DIPROSES, SILAHKAN MENUNGGU PROSES SELANJUTNYA|".$direct_approve."|".$remark);
					$r_message = "SUCCESS";
					$remarkProforma = "";
				}else if($arrxml['code'] == "19"){
					$message = "Booking order gagal dirposes (Timeout)";
					$this->session->set_flashdata('message_id',$ref_id."-".$order_id."-".$arrxml['code']."|DATA GAGAL DIPROSES (TIMEOUT), SILAHKAN MEMBAWA DOKUMEN DAN MUNUJU LOKET|".$direct_approve."|".$remark);
					$r_message = "TIMEOUT";
					$remarkProforma = " (NO EDI_TO)";
				}else{
					$message = "Booking order gagal dirposes";
					$this->session->set_flashdata('message_id',$ref_id."-".$order_id."-".$arrxml['code']."|DATA GAGAL DIPROSES, SILAHKAN MEMBAWA DOKUMEN DAN MUNUJU LOKET|".$direct_approve."|".$remark);
					$r_message = "FAILED";
					$remarkProforma = " (NO EDI_TO)";
				}
				
				$kiosk_number 	= $this->session->userdata('KIOSK_NUMBER');
				$proformaBy 	= "K".$kiosk_number.$remarkProforma;
				
				$booking_queue = $this->session->userdata('ID_QUEUE');
				$this->pg->where(array('idqueue' => $booking_queue));
				$this->pg->update('tqueuedata', array('keterangan' => $proformaBy, 'bookingno' => $arrheader['no_do'], 'entryuser' => validate($this->session->userdata('USER_KIOSK')), 'ends2' => date('Y-m-d H:i:s'), 's2' => '2', 'booking_cont' => $BookingCont, 'customs_cont' => $CustomsCont, 'request_cont' => $RequestCont));
			
				#START REPORT#
				$reportid = $this->session->userdata('ID_REPORT');
				$this->pg->where(array('reportid' => $reportid));
				$this->pg->update('treport', array('i_booking_cont' => $BookingCont, 'i_customs_cont' => $CustomsCont, 'i_request_cont' => $RequestCont, 'i_remark' => $remark));
				
				foreach($arrcont as $cont){
					$SQD = "SELECT reportdetailid
							FROM treportdetail
							WHERE reportid = ".$this->db->escape($reportid)."
							AND f_contnumber = ".$this->db->escape($cont['CONTAINER']);
					$res_d = $this->pg->query($SQD);
					if($res_d->num_rows() > 0){
						$reportdetailid = $res_d->row()->reportdetailid;
						if($cont['CONT_SIZE'] == '20') $teus = 1;
						else $teus = 2;
						if(trim($cont['TEMP']) != ""){
							if($cont['RDC'] != ""){
								$paidthr = validate($cont['RDC'].':00','DATETIME');
							}
						}else{
							$paidthr = validate(substr($arrheader['date_until'],0,10)." 23:59:00",'DATETIME');
						}			
						if($cont['SHFT_RFR'] == "") $shift = 0;
						else $shift = $cont['SHFT_RFR'];
						$arrupdate = array('reportid' => $reportid,
										   'b_kiosklogin' => validate($this->session->userdata('KIOSK_LOGIN')),
										   'b_userkiosk' => validate($this->session->userdata('USER_KIOSK')),
										   'b_ppjk' => validate($this->session->userdata('COMPANY_KIOSK')),
										   'b_ordersuccess'	  => date('Y-m-d H:i:s'),
										   'b_statusorder'	  => validate($r_message),
										   'f_orderno'	=> validate($arrheader['no_do']),
										   'f_shipper' => validate($this->session->userdata('CUSTOMER_NAME')),
										   'f_npwp' => validate($this->session->userdata('NPWP')),
										   'f_address' => validate(substr($this->session->userdata('CUSTOMER_ADDRESS'),0,100)),
										   'f_vessel' => validate($arrheader['vessel_name']),
										   'f_voyage' => validate($arrheader['voyage_in']),
										   'f_eta' => NULL,
										   'f_etd' => NULL,
										   'f_contnumber' => validate($cont['CONTAINER']),
										   'f_numberofbox' => 1,
										   'f_teus' => validate($teus),
										   'f_size' => validate($cont['CONT_SIZE']),
										   'f_type' => validate($cont['CONT_TYPE']),
										   'f_status' => validate($cont['FE']),
										   'f_pod' => validate(substr($arrheader['pod'],0,5)),
										   'f_spod' => validate(substr($arrheader['spod'],0,5)),
										   'f_stackingtime' => validate($cont['DIS'],'DATETIME'),
										   'f_paidthru' => validate($paidthr),
										   'f_storagedays' => validate(ceil($cont['SHFT_RFR']/3)),
										   'f_shift' => validate($shift),
										   'f_document' => validate($this->session->userdata('DOCUMENT')),
										   'g_reqcustdoctype' => validate($arrheader['req_dokumen']),
										   'g_reqcustdocno'	  => validate($arrheader['req_nomor']),
										   'g_reqcustdocdate' => validate($arrheader['req_tanggal'],'DATE'),
										   'g_rescustdoctype' => validate($arrheader['res_dokumen']),
										   'g_rescustdocno'	  => validate($arrheader['res_nomor']),
										   'g_rescustdocdate' => validate($arrheader['res_tanggal'],'DATE'),
										   'i_line' => validate($arrheader['agent']));
						#if($success == 1){
							$arrupdate['c_proformaresponse'] = date('Y-m-d H:i:s');
							#$arrupdate['c_proformabykiosk']  = $proformaBy;
						#}
						$this->pg->where(array('reportdetailid' => $reportdetailid));
						$this->pg->update('treportdetail', $arrupdate);
					}else{
						if($cont['CONT_SIZE'] == '20') $teus = 1;
						else $teus = 2;
						if(trim($cont['TEMP']) != ""){
							if($cont['RDC'] != ""){
								$paidthr = validate($cont['RDC'].':00','DATETIME');
							}
						}else{
							$paidthr = validate(substr($arrheader['date_until'],0,10)." 23:59:00",'DATETIME');
						}
						if($cont['SHFT_RFR'] == "") $shift = 0;
						else $shift = $cont['SHFT_RFR'];
						$arrinsert = array('reportid' => $reportid,
										   'b_kiosklogin' => validate($this->session->userdata('KIOSK_LOGIN')),
										   'b_userkiosk' => validate($this->session->userdata('USER_KIOSK')),
										   'b_ppjk' => validate($this->session->userdata('COMPANY_KIOSK')),
										   'b_ordersuccess'	  => date('Y-m-d H:i:s'),
										   'b_statusorder'	  => validate($r_message),
										   'f_orderno'	=> validate($arrheader['no_do']),
										   'f_shipper' => validate($this->session->userdata('CUSTOMER_NAME')),
										   'f_npwp' => validate($this->session->userdata('NPWP')),
										   'f_address' => validate(substr($this->session->userdata('CUSTOMER_ADDRESS'),0,100)),
										   'f_vessel' => validate($arrheader['vessel_name']),
										   'f_voyage' => validate($arrheader['voyage_in']),
										   'f_eta' => NULL,
										   'f_etd' => NULL,
										   'f_contnumber' => validate($cont['CONTAINER']),
										   'f_numberofbox' => 1,
										   'f_teus' => validate($teus),
										   'f_size' => validate($cont['CONT_SIZE']),
										   'f_type' => validate($cont['CONT_TYPE']),
										   'f_status' => validate($cont['FE']),
										   'f_pod' => validate(substr($arrheader['pod'],0,5)),
										   'f_spod' => validate(substr($arrheader['spod'],0,5)),
										   'f_stackingtime' => validate($cont['DIS'],'DATETIME'),
										   'f_paidthru' => validate($paidthr),
										   'f_storagedays' => validate(ceil($cont['SHFT_RFR']/3)),
										   'f_shift' => validate($shift),
										   'f_document' => validate($this->session->userdata('DOCUMENT')),
										   'g_reqcustdoctype' => validate($arrheader['req_dokumen']),
										   'g_reqcustdocno'	  => validate($arrheader['req_nomor']),
										   'g_reqcustdocdate' => validate($arrheader['req_tanggal'],'DATE'),
										   'g_rescustdoctype' => validate($arrheader['res_dokumen']),
										   'g_rescustdocno'	  => validate($arrheader['res_nomor']),
										   'g_rescustdocdate' => validate($arrheader['res_tanggal'],'DATE'),
										   'i_line' => validate($arrheader['agent']));
						#if($success == 1){
							$arrinsert['c_proformaresponse'] = date('Y-m-d H:i:s');
							#$arrinsert['c_proformabykiosk']  = $proformaBy;
						#}
						$this->pg->insert('treportdetail', $arrinsert);
					}
				}
				#END REPORT#
				$arrayReturn['success'] = $success;
				$arrayReturn['message'] = $message;
				$arrayReturn['url'] = base_url('index.php/home/info');
				echo json_encode($arrayReturn);
			}
		}else{
			$arrayReturn['success'] = 0;
			$arrayReturn['notify']	= "Akses ditolak, silahkan melakukan pengajuan ulang";
			echo json_encode($arrayReturn);
		}
	}
	
	function pages_customs($arrdata){
		$is_edo = 0;
		$arrcontorder = array('DOONLINE');
		$this->session->set_userdata('arrcontorder', $arrcontorder);
		$arrcont = $this->session->userdata('containers');
		if(array_key_exists(0, $arrcont['container'])){
			$index = 0;
			foreach($arrcont['container'] as $cont){
				if(!empty($cont['no_container'])){
					$arrcontainers[] = escape($cont['no_container']);
					$arrconttemp[$cont['no_container']]['seq'] = escape($cont['seq']);
					$arrconttemp[$cont['no_container']]['cont_no'] = escape($cont['no_container']);
					$arrconttemp[$cont['no_container']]['size'] = escape($cont['size']);
					$arrconttemp[$cont['no_container']]['isocode'] = escape($cont['isocode']);
					$arrconttemp[$cont['no_container']]['full_empty'] = escape($cont['full_empty']);
					$arrconttemp[$cont['no_container']]['bruto'] = escape($cont['bruto']);
					$arrconttemp[$cont['no_container']]['reefer'] = escape($cont['reefer']);
					$arrconttemp[$cont['no_container']]['temperature'] = escape($cont['temperature']);
					$arrconttemp[$cont['no_container']]['dg'] = escape($cont['dg']);
					$arrconttemp[$cont['no_container']]['imo_class'] = escape($cont['imo_class']);
					$arrconttemp[$cont['no_container']]['imo_no'] = escape($cont['imo_no']);
					$arrconttemp[$cont['no_container']]['seal_no'] = escape($cont['seal_no']);
					$arrconttemp[$cont['no_container']]['oogs'] = escape($cont['oogs']);
					$arrconttemp[$cont['no_container']]['in_time'] = escape($cont['in_time']);
					$arrconttemp[$cont['no_container']]['rcn_time'] = escape($cont['rcn_time']);
					$arrconttemp[$cont['no_container']]['paid_through_date'] = escape($cont['paid_through_date']);
					$arrconttemp[$cont['no_container']]['status'] = "";
					$arrconttemp[$cont['no_container']]['billing'] = escape($cont['billing']);
					$arrconttemp[$cont['no_container']]['precheck'] = escape($cont['precheck']);
					$index++;
				}
			}
			$this->session->set_userdata('arrcontorder', $arrcontainers);
			$this->session->set_userdata('containers', $arrconttemp);
			$this->session->set_userdata('is_edo', count($arrcontainers));
		}else{
			if(!empty($arrcont['container']['no_container'])){
				$arrcontainers[] = escape($arrcont['container']['no_container']);
				$arrconttemp[$arrcont['container']['no_container']]['seq'] = escape($arrcont['container']['seq']);
				$arrconttemp[$arrcont['container']['no_container']]['cont_no'] = escape($arrcont['container']['no_container']);
				$arrconttemp[$arrcont['container']['no_container']]['size'] = escape($arrcont['container']['size']);
				$arrconttemp[$arrcont['container']['no_container']]['isocode'] = escape($arrcont['container']['isocode']);
				$arrconttemp[$arrcont['container']['no_container']]['full_empty'] = escape($arrcont['container']['full_empty']);
				$arrconttemp[$arrcont['container']['no_container']]['bruto'] = escape($arrcont['container']['bruto']);
				$arrconttemp[$arrcont['container']['no_container']]['reefer'] = escape($arrcont['container']['reefer']);
				$arrconttemp[$arrcont['container']['no_container']]['temperature'] = escape($arrcont['container']['temperature']);
				$arrconttemp[$arrcont['container']['no_container']]['dg'] = escape($arrcont['container']['dg']);
				$arrconttemp[$arrcont['container']['no_container']]['imo_class'] = escape($arrcont['container']['imo_class']);
				$arrconttemp[$arrcont['container']['no_container']]['imo_no'] = escape($arrcont['container']['imo_no']);
				$arrconttemp[$arrcont['container']['no_container']]['seal_no'] = escape($arrcont['container']['seal_no']);
				$arrconttemp[$arrcont['container']['no_container']]['oogs'] = escape($arrcont['container']['oogs']);
				$arrconttemp[$arrcont['container']['no_container']]['in_time'] = escape($arrcont['container']['in_time']);
				$arrconttemp[$arrcont['container']['no_container']]['rcn_time'] = escape($arrcont['container']['rcn_time']);
				$arrconttemp[$arrcont['container']['no_container']]['paid_through_date'] = escape($arrcont['container']['paid_through_date']);
				$arrconttemp[$arrcont['container']['no_container']]['status'] = "";
				$arrconttemp[$arrcont['container']['no_container']]['billing'] = escape($arrcont['container']['billing']);
				$arrconttemp[$arrcont['container']['no_container']]['precheck'] = escape($arrcont['container']['precheck']);
				$this->session->set_userdata('arrcontorder', $arrcontainers);
			}
			$this->session->set_userdata('containers', $arrconttemp);
			$this->session->set_userdata('is_edo', count($arrcontainers));
		}
		$data['request_type'] 	= escape($arrdata['customs']['request_type']);
		$data['request_no'] 	= escape($arrdata['customs']['request_no']);
		$data['request_date'] 	= validate(escape($arrdata['customs']['request_date']),'DATE-STR');
		$data['response_type'] 	= escape($arrdata['customs']['response_type']);
		$data['response_no'] 	= escape($arrdata['customs']['response_no']);
		$data['response_date'] 	= validate(escape($arrdata['customs']['response_date']),'DATE-STR');
		$data['kpbc'] 			= escape($arrdata['customs']['kpbc']);
		$data['booking_no'] 	= escape($arrdata['booking_no']);
		$data['arr_doc'] 		= $this->get_combobox('doc_customs_imp');
		$data['arrcont'] 		= $arrconttemp;
		return $this->load->view('content/kiosk/import_customs',$data,true);
	}
	
	function pages_container($arrdata,$arrcont){
		$data['arrpost'] = $this->input->post();
		$data['arrhdr'] = $arrdata;
		$data['arrdata'] = $arrcont;
		return $this->load->view('content/kiosk/import_container',$data,true);
	}
	
	function pages_confirm($arrdata){
		$arrsession['npwp'] = $this->session->userdata('NPWP');
		$arrsession['cust_name'] = $this->session->userdata('CUSTOMER_NAME');
		$arrsession['cust_address'] = $this->session->userdata('CUSTOMER_ADDRESS');
		$npwp = $this->session->userdata('NPWP');
		$arrheader = $arrdata;
		$arrdetail = $arrdata['containers'];
		foreach($arrdata['chkcontainer'] as $field => $value){
			$arrchk[] = $value;
		}
		if(count($arrchk) > 0){
			foreach($arrchk as $a => $b){
				foreach($arrdetail['CONT_'.$b] as $c => $d){
					$arrcont[$a][$c] = strtoupper($d);
				}
			}
		}
		$arrheader = array_map('strtoupper', $arrheader);
		$data['arrsess'] = $arrsession;
		$data['arrhdr'] = $arrheader;
		$data['arrcont'] = $arrcont;
		return $this->load->view('content/kiosk/import_confirm',$data,true);
	}
	
	function get_customs($type, $act){
		$arrayReturn = array();
		$success = 0;
		$no_req = "";
		$tgl_req = "";
		$no_res = "";
		$tgl_res = "";
		if($type=="npe"){
			$this->load->library('Nusoap');
			$WSDL = 'https://tpsonline.beacukai.go.id/tps/service.asmx?wsdl';
			$client = new nusoap_client($WSDL,true);
			$error  = $client->getError();
			if($error){
				echo '<h2>Constructor error</h2>'.$error;
				exit();
			}
			$method    = 'GetEkspor_NPE';
			$username  = 'NCT1';
			$password  = 'NCT1123456';
			$npwp 	   = '010005668092000'; //$this->session->userdata('NPWP');
			$nomor 	   = $this->input->post('svc_nomor');
			$kpbc 	   = $this->input->post('svc_kpbc');
			$param  = array('UserName'=>$username, 'Password'=>$password, 'No_PE'=>$nomor, 'npwp'=>$npwp, 'kdKantor'=>$kpbc);
			$response = $client->call($method,$param);
			$view  = "";
			$this->session->unset_userdata('containers_svc');
			if($response!=""){
				$return = $response[$method.'Result'];
				$pos = strpos(strtoupper($return), 'NPE');
				if($pos !== false){
					$success = 1;
					$str_xml = WhiteSpaceXML($return);
					$res     = simplexml_load_string($str_xml);
					$json    = json_encode($res);
					$arrxml  = json_decode($json,TRUE);
					$date_now	= date('Y-m-d H:i:s');
					$arrheader   = $arrxml['NPE']['HEADER'];
					$arrdetail   = $arrxml['NPE']['DETIL'];
					$count_header = count($arrheader);
					$count_detail = count($arrdetail);
					if($count_detail > 0){
						if(array_key_exists('NPE', $arrxml)){
							$no_req = $arrheader['NO_DAFTAR'];
							$tgl_req = $arrheader['TGL_DAFTAR'];
							$no_res = $arrheader['NONPE'];
							$tgl_res = $arrheader['TGLNPE'];
							if(array_key_exists(0, $arrdetail['CONT'])){
								for($a=0; $a<count($arrdetail['CONT']); $a++){
									$view .= '<tr>';
										$view .= '<td>'.$arrdetail['CONT'][$a]['SERI_CONT'].'</td>';
										$view .= '<td>'.$arrdetail['CONT'][$a]['NO_CONT'].'</td>';
										$view .= '<td>'.$arrdetail['CONT'][$a]['SIZE'].'</td>';
									$view .= '</tr>';
									$arrcont['containers_svc']['container'][$a]['no_container'] = $arrdetail['CONT'][$a]['NO_CONT'];
								}
							}else{
								$view .= '<tr>';
									$view .= '<td>'.$arrdetail['CONT']['SERI_CONT'].'</td>';
									$view .= '<td>'.$arrdetail['CONT']['NO_CONT'].'</td>';
									$view .= '<td>'.$arrdetail['CONT']['SIZE'].'</td>';
								$view .= '</tr>';
								$arrcont['containers_svc']['container'][0]['no_container'] = $arrdetail['CONT']['NO_CONT'];
							}
						}
						$this->session->set_userdata($arrcont);
					}
				}else{
					$view .= '<tr id="cont_null">';
						$view .= '<td colspan="3"><center>Data kontainer tidak ditemukan</center></td>';
					$view .= '</tr>';
				}
			}else{
				$view .= '<tr id="cont_null">';
					$view .= '<td colspan="3"><center>Data kontainer tidak ditemukan</center></td>';
				$view .= '</tr>';
			}
			$arrayReturn['no_req'] = $no_req;
			$arrayReturn['tgl_req'] = validate($tgl_req,'DATE-STR');
			$arrayReturn['no_res'] = $no_res;
			$arrayReturn['tgl_res'] = validate($tgl_res,'DATE-STR');
			$arrayReturn['success'] = $success;
			$arrayReturn['html'] = $view;
			echo json_encode($arrayReturn);
		}else if($type=="spb"){
			$this->load->library('Nusoap');
			$WSDL = 'https://tpsonline.beacukai.go.id/tps/service.asmx?wsdl';
			$client = new nusoap_client($WSDL,true);
			$error  = $client->getError();
			if($error){
				echo '<h2>Constructor error</h2>'.$error;
				exit();
			}
			$method    = 'GetImpor_Sppb';
			$username  = 'NCT1';
			$password  = 'NCT1123456';
			$npwp 	   = '010002301092000'; //$this->session->userdata('NPWP');
			$nomor 	   = $this->input->post('svc_nomor');
			$tgl 	   = str_replace('-','',$this->input->post('svc_tgl'));
			$param  = array('UserName'=>$username, 'Password'=>$password, 'No_Sppb'=>$nomor, 'Tgl_Sppb'=>$tgl, 'NPWP_Imp'=>$npwp);
			$response = $client->call($method,$param);
			$view  = "";
			$this->session->unset_userdata('containers_svc');
			if($response!=""){
				$return = $response[$method.'Result'];
				$pos = strpos(strtoupper($return), 'SPPB');
				if($pos !== false){
					$success = 1;
					$str_xml = WhiteSpaceXML($return);
					$res     = simplexml_load_string($str_xml);
					$json    = json_encode($res);
					$arrxml  = json_decode($json,TRUE);
					$date_now	= date('Y-m-d H:i:s');
					$arrheader   = $arrxml['SPPB']['HEADER'];
					$arrdetail   = $arrxml['SPPB']['DETIL'];
					$count_header = count($arrheader);
					$count_detail = count($arrdetail);
					if($count_detail > 0){
						if(array_key_exists('SPPB', $arrxml)){
							$no_req = $arrheader['NO_PIB'];
							$tgl_req = $arrheader['TGL_PIB'];
							$no_res = $arrheader['NO_SPPB'];
							$tgl_res = $arrheader['TGL_SPPB'];
							if(array_key_exists(0, $arrdetail['CONT'])){
								$no = 1;
								for($a=0; $a<count($arrdetail['CONT']); $a++){
									$view .= '<tr>';
										$view .= '<td>'.$no++.'</td>';
										$view .= '<td>'.$arrdetail['CONT'][$a]['NO_CONT'].'</td>';
										$view .= '<td>'.$arrdetail['CONT'][$a]['SIZE'].'</td>';
										$view .= '<td>'.$arrdetail['CONT'][$a]['JNS_MUAT'].'</td>';
									$view .= '</tr>';
									$arrcont['containers_svc']['container'][$a]['no_container'] = $arrdetail['CONT'][$a]['NO_CONT'];
								}
							}else{
								$view .= '<tr>';
									$view .= '<td>1</td>';
									$view .= '<td>'.$arrdetail['CONT']['NO_CONT'].'</td>';
									$view .= '<td>'.$arrdetail['CONT']['SIZE'].'</td>';
									$view .= '<td>'.$arrdetail['CONT']['JNS_MUAT'].'</td>';
								$view .= '</tr>';
								$arrcont['containers_svc']['container'][0]['no_container'] = $arrdetail['CONT']['NO_CONT'];
							}
						}
						$this->session->set_userdata($arrcont);
					}
				}else{
					$view .= '<tr id="cont_null">';
						$view .= '<td colspan="4"><center>Data kontainer tidak ditemukan</center></td>';
					$view .= '</tr>';
				}
			}else{
				$view .= '<tr id="cont_null">';
					$view .= '<td colspan="4"><center>Data kontainer tidak ditemukan</center></td>';
				$view .= '</tr>';
			}
			$arrayReturn['no_req'] = $no_req;
			$arrayReturn['tgl_req'] = validate($tgl_req,'DATE-SLASH');
			$arrayReturn['no_res'] = $no_res;
			$arrayReturn['tgl_res'] = validate($tgl_res,'DATE-SLASH');
			$arrayReturn['success'] = $success;
			$arrayReturn['html'] = $view;
			echo json_encode($arrayReturn);
		}
	}
	
	function check_cont($cont){
		$arrdata = $this->session->userdata($arrdata);
		$this->load->library('Nusoap');
		$WSDL = WEBSERVICE;
		$client = new nusoap_client($WSDL,true);
		$error  = $client->getError();
		if($error){
			echo '<h2>Constructor error</h2>'.$error;
			exit();
		}
		$method = 'service';
		$xml = "<validate_container>";
			$xml .= "<type>OUT</type>";
			$xml .= "<booking_no>".strtoupper($arrdata['booking_no'])."</booking_no>";
			$xml .= "<container_no>".strtoupper($cont)."</container_no>";
		$xml .= "</validate_container>";
		$param  = array('username'=>'KIOSK', 'password'=>'KIOSK@2017', 'type'=>'validate_container', 'mode'=>'2', 'data'=>$xml);
		$response = $client->call($method,$param);
		$str_xml = WhiteSpaceXML(trim($response));
		$res     = simplexml_load_string($str_xml);
		$json    = json_encode($res);
		$arrxml  = json_decode($json,TRUE);
		$arrhdr = array();
		if($arrxml['code'] == "00"){
			$arrdata = $arrxml['data'];
			$arrcont = $arrdata['containers']['container'];
			foreach($arrcont as $a => $b){
				if(!empty($b)) $arrtemp[$a] = $b;
				else $arrtemp[$a] = "";
			}
			$arroog = $arrtemp['oogs']['oog'];
			$oog = "N"; $ol = ""; $oh = ""; $or = "";
			if(array_key_exists(0, $arroog)){
				foreach($arroog as $data_oog){
					if(strtoupper($data_oog['code']) == "OR") $or = $data_oog['value'];
					if(strtoupper($data_oog['code']) == "OH") $oh = $data_oog['value'];
					if(strtoupper($data_oog['code']) == "OL") $ol = $data_oog['value'];
				}
				if($or!="" || $oh!="" || $ol!=""){
					$oog = "Y";
				}
			}else{
				if(strtoupper($arroog['code']) == "OR") $or = $arroog['value'];
				if(strtoupper($arroog['code']) == "OH") $oh = $arroog['value'];
				if(strtoupper($arroog['code']) == "OL") $ol = $arroog['value'];
				if($or!="" || $oh!="" || $ol!=""){
					$oog = "Y";
				}
			}
			if($arrtemp['reefer'] == "Y") $fl_reefer = "text";
			else $fl_reefer = "hidden";
			$arrayReturn['CONTAINER~show~hidden'] = $arrtemp['no_container'];
			$arrayReturn['ISOCODE~show~hidden'] = $arrtemp['isocode'];
			$arrayReturn['CONT_SIZE~show~hidden'] = get_isocode($arrtemp['isocode'],'size');
			$arrayReturn['CONT_TYPE~show~hidden'] = get_isocode($arrtemp['isocode'],'type');
			$arrayReturn['FE~show~hidden'] = $arrtemp['full_empty'];
			$arrayReturn['DG~show~hidden'] = $arrtemp['dg'];
			$arrayReturn['OOG~show~hidden'] = $oog;
			$arrayReturn['DIS~show~hidden'] = validate($arrtemp['in_time'],'DATE-STR');
			$arrayReturn['RCN~show~hidden'] = ($arrtemp['reefer'] == "Y")?validate($arrtemp['rcn_time'],'DATE-STR'):"";
			$arrayReturn['RDC~show~'.$fl_reefer] = "";
			$arrayReturn['SHFT_RFR~show~hidden'] = "";
			$arrayReturn['TEMP~hide~hidden'] = $arrtemp['temperature'];
			$arrayReturn['I_CLASS~hide~hidden'] = $arrtemp['imo_class'];
			$arrayReturn['I_NO~hide~hidden'] = $arrtemp['imo_no'];
			$arrayReturn['OR~hide~hidden'] = $or;
			$arrayReturn['OH~hide~hidden'] = $oh;
			$arrayReturn['OL~hide~hidden'] = $ol;
			$arrayReturn['BRUTO~hide~hidden'] = $arrtemp['bruto'];
			$arrayReturn['CALL_SIGN'] = $arrdata['call_sign'];
			$arrayReturn['VESSEL'] = $arrdata['vessel_name'];
			$arrayReturn['VOY_IN'] = $arrdata['voyage_in'];
			$arrayReturn['VOY_OUT'] = $arrdata['voyage_out'];
			$arrayReturn['POD'] = $arrdata['pod'];
			$arrayReturn['SPOD'] = $arrdata['spod'];
			$arrayReturn['AGENT'] = $arrdata['agent']['id_cosmos'];
			$arrayReturn['AGENT_NAME'] = $arrdata['agent']['name'];
			$arrayReturn['success'] = 1;
			echo json_encode($arrayReturn);
		}else{
			if($arrxml['code'] == "95"){
				$message = "Kontainer ini sudah digunakan dengan nomor booking <b>".$arrxml['data']['booking_no']."</b>";
			}else if($arrxml['code'] == "14"){
				$message = "Kontainer tidak ditemukan";
			}
			$arrayReturn['message'] = $message;
			echo json_encode($arrayReturn);
		}
	}
	
	function next_steps(){
		$id_queue 		= $this->session->userdata('ID_QUEUE');
		$starts2 		= $this->session->userdata('starts2');
		$kiosk_number 	= $this->session->userdata('KIOSK_NUMBER');
		$SQL = "SELECT queuecode, queuenumber, queuecodenumber, passcode, assigned, s1, s2, s3, s4, s5, countertujuancode,
				countertujuannumber, queuetype, panggilannum, branch, starts1, ends1, starts2, ends2, starts3, ends3, starts4, ends4, 
				starts5, ends5, proformanum, entryuser, bookingno, countertujuancodek, countertujuannumberk, starts1k, ends1k,
				CASE WHEN keterangan = 'e-DO' THEN 'KIOSK' ELSE keterangan END AS keterangan
				FROM tqueuedata
				WHERE idqueue = ".$this->db->escape($id_queue)."
				ORDER BY idqueue DESC
				LIMIT 1";
		$result = $this->pg->query($SQL);
		if($result->num_rows() > 0){
			$arrdata = $result->row_array();
			$arrnext['queuecode'] = $arrdata['queuecode'];
			$arrnext['queuenumber'] = $arrdata['queuenumber'];
			$arrnext['queuecodenumber'] = $arrdata['queuecodenumber'];
			$arrnext['passcode'] = $arrdata['passcode'];
			$arrnext['assigned'] = $arrdata['assigned'];
			$arrnext['s1'] = $arrdata['s1'];
			$arrnext['s2'] = '2';
			$arrnext['s3'] = '0';
			$arrnext['s4'] = '0';
			$arrnext['s5'] = '0';
			$arrnext['keterangan'] = 'K'.$kiosk_number; //$arrdata['keterangan'];
			$arrnext['countertujuancode'] = $arrdata['countertujuancode'];
			$arrnext['countertujuannumber'] = $arrdata['countertujuannumber'];
			$arrnext['queuetype'] = $arrdata['queuetype'];
			$arrnext['panggilannum'] = $arrdata['panggilannum'];
			$arrnext['branch'] = $arrdata['branch'];
			$arrnext['starts1'] = $arrdata['starts1'];
			$arrnext['ends1'] = $arrdata['ends1'];
			$arrnext['starts2'] = $starts2;
			$arrnext['ends2'] = NULL;
			$arrnext['starts3'] = NULL;
			$arrnext['ends3'] = NULL;
			$arrnext['starts4'] = NULL;
			$arrnext['ends4'] = NULL;
			$arrnext['starts5'] = NULL;
			$arrnext['ends5'] = NULL;
			$arrnext['proformanum'] = NULL;
			$arrnext['entryuser'] = NULL;
			$arrnext['bookingno'] = NULL;
			$arrnext['countertujuancodek'] = $arrdata['countertujuancodek'];
			$arrnext['countertujuannumberk'] = $arrdata['countertujuannumberk'];
			$arrnext['starts1k'] = $arrdata['starts1k'];
			$arrnext['ends1k'] = $arrdata['ends1k'];
			$this->pg->insert('tqueuedata', $arrnext);
			$id_queue_new = $this->pg->insert_id();
			$this->session->set_userdata('ID_QUEUE', $id_queue_new);
		}
	}
	
	function get_data($type,$act){
		if($type == "port"){
			$port = "";
			$id = strtoupper(trim($this->input->post('id')));
			$SQL = "SELECT CODE, CONCAT(CODE,' - ',NAME) AS NAME
					FROM td_port 
					WHERE UPPER(CODE) = ".$this->db->escape($id);
			$result = $this->db->query($SQL);
			if($result->num_rows() > 0){
				$port = $result->row()->NAME;
			}
			echo $port;
		}
	}
	
	#PERPANJANGAN IMPORT
	function execute_import_ext($act,$type){
		$arrayReturn = array();
		$message = "";
		$success = 0;
		$notify = "";
		$this->session->unset_userdata('LOG_ID');
		$doc = $this->session->userdata('DOCUMENT');
		$array_dokumen = array('PERPANJANGAN IMPORT');
		$queue_id = $this->session->userdata('QUEUE');
		$id_log = log_act_hdr($queue_id);
		$this->session->set_userdata('LOG_ID', $id_log);
		$log_hdr = $this->session->userdata('LOG_ID');
		$booking = strtoupper($this->input->post('no_do'));
		if(in_array($doc,$array_dokumen)){
			if($act=="bookingorder"){
				echo $this->load->view('content/kiosk/import_ext_order','',true);
			}else if($act=="dokumenbeacukai"){
				$popup = false;
				$no_do = strtoupper($this->input->post('no_do'));
				$no_bl = strtoupper($this->input->post('no_bl'));
				$no_bc = strtoupper($this->input->post('no_bc'));
				$tgl_tempo = validate($this->input->post('tgl_tempo'),'DATE');
				$date_until = validate(substr($this->input->post('date_until'),0,10),'DATE');
				if(strtotime($tgl_tempo) > strtotime($date_until)){
					$notify = "Tanggal perpanjangan harus lebih besar dari tanggal paidthrough";
				}else{
					$this->load->library('Nusoap');
					$WSDL = WEBSERVICE;
					$client = new nusoap_client($WSDL,true);
					$error  = $client->getError();
					if($error){
						echo '<h2>Constructor error</h2>'.$error;
						exit();
					}
					$method = 'service';
					$xml = "<booking_order>";
						$xml .= "<order_type>OUT_EXT</order_type>";
						$xml .= "<booking_no>".trim($no_do)."~".trim($no_bl)."</booking_no>";
					$xml .= "</booking_order>";
					$param  = array('username'=>'KIOSK', 'password'=>'KIOSK@2017', 'type'=>'check_order', 'mode'=>'2', 'data'=>$xml);
					$response = $client->call($method,$param);
					$str_xml = WhiteSpaceXML(trim($response));
					$res     = simplexml_load_string($str_xml);
					$json    = json_encode($res);
					$arrxml  = json_decode($json,TRUE);
					$arrhdr = array();
					$this->clear_session();
					if($arrxml['code'] == "00"){
						$arrdata = $arrxml['data'];
						$fot_date = validate($arrdata['validity_date'],'DATE-S');
						$until_date = validate(substr($this->input->post('date_until'),0,16).":00",'DATETIME');
						if(strtotime($until_date) >= strtotime($fot_date)){
							$this->session->set_userdata('PLUG_IN',$fot_date);
							$this->session->set_userdata($arrdata, true);
							$success = 1;
							$page_customs = $this->pages_ext_customs($arrdata['customs']);
						}else{
							$popup = true;
							$notify = "Tanggal perpanjangan harus lebih besar dari pengajuan sebelumnya yaitu tanggal ".validate($fot_date,'DATETIME');
						}
					}else if($arrxml['code'] == "03"){
						$popup = true;
						$notify = "Booking order belum melakukan pembayaran";
					}else{
						$popup = true;
						$notify = "Booking order tidak ditemukan";
					}
				}
				$arrayReturn['success'] = $success;
				$arrayReturn['notify'] = $notify;
				$arrayReturn['popup'] = $popup;
				$arrayReturn['page'] = $page_customs;
				echo json_encode($arrayReturn);
			}else if($act=="kontainerdetail"){
				if($type == "add"){
					$arrdata = $this->session->userdata($arrdata);
					$cont = $this->input->post('cont');
					$tmp = array();
					$arrconttemp = $arrdata['containers']['container'];
					if(array_key_exists(0, $arrconttemp)){
						foreach($arrconttemp as $a => $b){
							if(!empty($b)) $arrdatatemp[$a] = $b;
							else $arrdatatemp[$a] = "";
						}
						if(count($arrdatatemp) > 0){
							foreach($arrdatatemp as $temp){
								$tmp[] = $temp['no_container'];
							}
						}
					}else{
						$tmp[] = $arrconttemp['no_container'];
					}
					if(in_array(strtoupper(trim($cont)),$tmp)){
						$message = "Sudah terdapat kontainer yang sama";
						$arrayReturn['message'] = $message;
						echo json_encode($arrayReturn);
					}else{
						$this->load->library('Nusoap');
						$WSDL = WEBSERVICE;
						$client = new nusoap_client($WSDL,true);
						$error  = $client->getError();
						if($error){
							echo '<h2>Constructor error</h2>'.$error;
							exit();
						}
						$method = 'service';
						$xml = "<validate_container>";
							$xml .= "<type>OUT</type>";
							$xml .= "<booking_no>".strtoupper($arrdata['booking_no'])."</booking_no>";
							$xml .= "<container_no>".strtoupper($cont)."</container_no>";
						$xml .= "</validate_container>";
						$param  = array('username'=>'KIOSK', 'password'=>'KIOSK@2017', 'type'=>'validate_container', 'mode'=>'2', 'data'=>$xml);
						$response = $client->call($method,$param);
						$str_xml = WhiteSpaceXML(trim($response));
						$res     = simplexml_load_string($str_xml);
						$json    = json_encode($res);
						$arrxml  = json_decode($json,TRUE);
						$arrhdr = array();
						if($arrxml['code'] == "00"){
							$arrdata = $arrxml['data'];
							$arrcont = $arrdata['containers']['container'];
							foreach($arrcont as $a => $b){
								if(!empty($b)) $arrtemp[$a] = $b;
								else $arrtemp[$a] = "";
							}
							$arroog = $arrtemp['oogs']['oog'];
							$oog = "N"; $ol = ""; $oh = ""; $or = "";
							if(array_key_exists(0, $arroog)){
								foreach($arroog as $data_oog){
									if(strtoupper($data_oog['code']) == "OR") $or = $data_oog['value'];
									if(strtoupper($data_oog['code']) == "OH") $oh = $data_oog['value'];
									if(strtoupper($data_oog['code']) == "OL") $ol = $data_oog['value'];
								}
								if($or!="" || $oh!="" || $ol!=""){
									$oog = "Y";
								}
							}else{
								if(strtoupper($arroog['code']) == "OR") $or = $arroog['value'];
								if(strtoupper($arroog['code']) == "OH") $oh = $arroog['value'];
								if(strtoupper($arroog['code']) == "OL") $ol = $arroog['value'];
								if($or!="" || $oh!="" || $ol!=""){
									$oog = "Y";
								}
							}
							if($arrtemp['reefer'] == "Y"){
								$fl_reefer = "text";
								$arrayReturn['FL_REEFER~hide~hidden'] = $arrtemp['reefer'];
							}else{
								$fl_reefer = "hidden";
							}
							$arrayReturn['CONTAINER~show~hidden'] = $arrtemp['no_container'];
							$arrayReturn['ISOCODE~show~hidden'] = $arrtemp['isocode'];
							$arrayReturn['CONT_SIZE~show~hidden'] = get_isocode($arrtemp['isocode'],'size');
							$arrayReturn['CONT_TYPE~show~hidden'] = get_isocode($arrtemp['isocode'],'type');
							$arrayReturn['FE~show~hidden'] = $arrtemp['full_empty'];
							$arrayReturn['DG~show~hidden'] = $arrtemp['dg'];
							$arrayReturn['OOG~show~hidden'] = $oog;
							$arrayReturn['DIS~show~hidden'] = validate($arrtemp['in_time'],'DATE-STR');
							$arrayReturn['RCN~show~hidden'] = ($arrtemp['reefer'] == "Y")?validate($arrtemp['rcn_time'],'DATE-STR'):"";
							$arrayReturn['RDC~show~'.$fl_reefer] = "";
							$arrayReturn['SHFT_RFR~show~hidden'] = "";
							$arrayReturn['TEMP~hide~hidden'] = $arrtemp['temperature'];
							$arrayReturn['I_CLASS~hide~hidden'] = $arrtemp['imo_class'];
							$arrayReturn['I_NO~hide~hidden'] = $arrtemp['imo_no'];
							$arrayReturn['OR~hide~hidden'] = $or;
							$arrayReturn['OH~hide~hidden'] = $oh;
							$arrayReturn['OL~hide~hidden'] = $ol;
							$arrayReturn['BRUTO~hide~hidden'] = $arrtemp['bruto'];
							$arrayReturn['CALL_SIGN'] = $arrdata['call_sign'];
							$arrayReturn['VESSEL'] = $arrdata['vessel_name'];
							$arrayReturn['VOY_IN'] = $arrdata['voyage_in'];
							$arrayReturn['VOY_OUT'] = $arrdata['voyage_out'];
							$arrayReturn['POD'] = $arrdata['pod'];
							$arrayReturn['SPOD'] = $arrdata['spod'];
							$arrayReturn['AGENT'] = $arrdata['agent']['id_cosmos'];
							$arrayReturn['AGENT_NAME'] = $arrdata['agent']['name'];
							$arrayReturn['success'] = 1;
							echo json_encode($arrayReturn);
						}else{
							if($arrxml['code'] == "95"){
								$message = "Kontainer ini sudah digunakan dengan nomor booking <b>".$arrxml['data']['booking_no']."</b>";
							}else if($arrxml['code'] == "14"){
								$message = "Kontainer tidak ditemukan";
							}
							$arrayReturn['message'] = $message;
							echo json_encode($arrayReturn);
						}
					}
				}else{
					$res_dokumen = escape($this->input->post('res_dokumen'));
					$res_nomor = escape($this->input->post('res_nomor'));
					$res_tanggal = escape($this->input->post('res_tanggal'));
					$arrdata = $this->session->userdata($arrdata);
					$page_container = $this->pages_ext_container($arrdata);
					$arrayReturn['success'] = 1;
					$arrayReturn['message'] = "";
					$arrayReturn['page'] = $page_container;
					echo json_encode($arrayReturn);
				}
			}else if($act=="konfirmasidata"){
				$success = 0;
				$arrdata = $this->input->post();
				foreach($arrdata['chkcontainer'] as $field => $value){
					$arrchk[] = $value;
				}
				$arrdetail = $arrdata['containers'];
				if(count($arrchk) > 0){
					foreach($arrchk as $a => $b){
						foreach($arrdetail['CONT_'.$b] as $c => $d){
							$arrcont[$a][$c] = strtoupper($d);
						}
					}
					foreach($arrcont as $cont){
						if(!empty($cont['RDC'])){
							$arrtempreefer[] = $cont['RDC'];
						}
					}
					$userkiosk = $this->session->userdata('USER_KIOSK');
					#if(count($arrtempreefer) > 1 && strtoupper($userkiosk) != "KIOSK"){
					if(count($arrtempreefer) == -1){
						$success = 0;
						//$message = "Container reefer tidak boleh lebih dari satu";
						$message = "Data Gagal Diproses";
					}else{
						$success = 1;
						$page_confirm = $this->pages_ext_confirm($arrdata);
					}
				}else{
					$success = 0;
					$message = "Data kontainer belum dipilih";
				}
				$arrayReturn['success'] = $success;
				$arrayReturn['notify'] = $message;
				$arrayReturn['page'] = $page_confirm;
				echo json_encode($arrayReturn);
			}else if($act=="submit"){
				$arrdata = $this->input->post();
				$npwp = $this->session->userdata('NPWP');
				$customer_id = $this->session->userdata('CUSTOMER_ID');
				$customer_name = $this->session->userdata('CUSTOMER_NAME');
				$customer_name_address = $this->session->userdata('CUSTOMER_ADDRESS');
				$arrdetail = $arrdata['containers'];
				foreach($arrdata['chkcontainer'] as $field => $value){
					$arrchk[] = $value;
				}
				if(count($arrchk) > 0){
					foreach($arrchk as $a => $b){
						foreach($arrdetail['CONT_'.$b] as $c => $d){
							$arrcont[$a][$c] = strtoupper($d);
							$arrcontchk['chkcontainer'][$a][$c] = strtoupper($d);
						}
					}
					$this->session->set_userdata($arrcontchk, true);
				}
				$arrheader = array_map('strtoupper', $arrdata);
				$xml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
				$xml .= '<booking_order>';
					$xml .= '<force>TRUE</force>';
					$xml .= '<order_type>OUT_EXT</order_type>';
					$xml .= '<order_status></order_status>';
					$xml .= '<vessel_name>'.$arrheader['vessel_name'].'</vessel_name>';
					$xml .= '<call_sign>'.$arrheader['call_sign'].'</call_sign>';
					$xml .= '<voyage_in>'.$arrheader['voyage_in'].'</voyage_in>';
					$xml .= '<voyage_out>'.$arrheader['voyage_out'].'</voyage_out>';
					$xml .= '<spod>'.substr($arrheader['spod'],0,5).'</spod>';
					$xml .= '<pod>'.substr($arrheader['pod'],0,5).'</pod>';
					$xml .= '<booking_no>'.$arrheader['no_do'].'~'.$arrheader['no_bl'].'~'.$arrheader['res_nomor'].'</booking_no>';
					$xml .= '<customer>'.$customer_name.'</customer>';
					$xml .= '<validity_time>'.validate(substr($arrheader['date_until'],0,10),'DATE-XML-1').'</validity_time>';
					$xml .= '<agent>';
						$xml .= '<id_cosmos>'.$arrheader['agent'].'</id_cosmos>';
						$xml .= '<id_kiosk></id_kiosk>';
						$xml .= '<name>'.$arrheader['agent_name'].'</name>';
					$xml .= '</agent>';
					$xml .= '<customs>';
						$xml .= '<request_type>'.$arrheader['req_dokumen'].'</request_type>';
						$xml .= '<request_no>'.$arrheader['req_nomor'].'</request_no>';
						$xml .= '<request_date>'.validate($arrheader['req_tanggal'],'DATE-XML-1').'</request_date>';
						$xml .= '<response_type>'.$arrheader['res_dokumen'].'</response_type>';
						$xml .= '<response_no>'.$arrheader['res_nomor'].'</response_no>';
						$xml .= '<response_date>'.validate($arrheader['res_tanggal'],'DATE-XML-1').'</response_date>';
						$xml .= '<kpbc>040300</kpbc>';
					$xml .= '</customs>';
					$xml .= '<customer_id>'.$customer_id.'</customer_id>';
					$xml .= '<containers>';
						foreach($arrcont as $cont){
							$xml .= '<container>';
								$xml .= '<seq>'.$cont['SEQ'].'</seq>';
								$xml .= '<no_container>'.$cont['CONTAINER'].'</no_container>';
								$xml .= '<isocode>'.$cont['ISOCODE'].'</isocode>';
								$xml .= '<full_empty>'.$cont['FE'].'</full_empty>';
								$xml .= '<bruto>'.$cont['BRUTO'].'</bruto>';
								if(trim($cont['TEMP']) != ""){
									$xml .= '<reefer>Y</reefer>';
								}else{
									$xml .= '<reefer>N</reefer>';
								}
								$xml .= '<temperature>'.$cont['TEMP'].'</temperature>';
								$xml .= '<seal_number></seal_number>';
								if(trim($cont['I_CLASS']) != ""){
									$xml .= '<dg>Y</dg>';
								}else{
									$xml .= '<dg>N</dg>';
								}
								$xml .= '<imo_class>'.$cont['I_CLASS'].'</imo_class>';
								$xml .= '<imo_no>'.$cont['I_NO'].'</imo_no>';
								$xml .= '<oogs>';
									if(trim($cont['OR']) != ""){
										$xml .= '<oog>';
											$xml .= '<code>OR</code>';
											$xml .= '<value>'.$cont['OR'].'</value>';
										$xml .= '</oog>';
									}
									if(trim($cont['OH']) != ""){
										$xml .= '<oog>';
											$xml .= '<code>OH</code>';
											$xml .= '<value>'.$cont['OH'].'</value>';
										$xml .= '</oog>';
									}
									if(trim($cont['OL']) != ""){
										$xml .= '<oog>';
											$xml .= '<code>OL</code>';
											$xml .= '<value>'.$cont['OL'].'</value>';
										$xml .= '</oog>';
									}
								$xml .= '</oogs>';
								if(trim($cont['TEMP']) != ""){
									if($cont['RDC'] != ""){
										$xml .= '<paid_through_date>'.validate($cont['RDC'].':00','DATE-XML-1').'</paid_through_date>'; 
									}else{
										$xml .= '<paid_through_date></paid_through_date>'; 
									}
								}else{
									$xml .= '<paid_through_date>'.validate(substr($arrheader['date_until'],0,10),'DATE-XML-1').'235900</paid_through_date>';
								}
							$xml .= '</container>';
						}
					$xml .= '</containers>';
				$xml .= '</booking_order>';
				
				$this->load->library('Nusoap');
				$WSDL = WEBSERVICE;
				$client = new nusoap_client($WSDL,true);
				$error  = $client->getError();
				if($error){
					echo '<h2>Constructor error</h2>'.$error;
					exit();
				}
				$method = 'service';
				$param  = array('username'=>'KIOSK', 'password'=>'KIOSK@2017', 'type'=>'submit_order', 'mode'=>'2', 'data'=>$xml);
				$response = $client->call($method,$param);
				$str_xml = WhiteSpaceXML(trim($response));
				$res     = simplexml_load_string($str_xml);
				$json    = json_encode($res);
				$arrxml  = json_decode($json,TRUE);
				$arrhdr = array();
				#print_r($arrxml); die();
				$remark = "";
				$this->session->unset_userdata('pdf');
				$this->session->set_userdata("booking_no", $arrheader['no_do']);
				$this->session->set_userdata('vessel_name', $arrheader['vessel_name']);
				$this->session->set_userdata('voyage_in', $arrheader['voyage_in']);
				$direct_approve = "N";
				
				if($arrxml['code'] == "00"){
					$success = 1;
					$message = "Data booking order berhasil diproses";
					$ref_id   = $arrxml['data']['ref_id'];
					$order_id = $arrxml['data']['order_id'];
					$this->session->set_flashdata('message_id',$ref_id."-".$order_id."-".$arrxml['code']."|DATA BERHASIL DIPROSES, SILAHKAN MENUNGGU PROSES SELANJUTNYA|".$direct_approve."|".$remark);
					$r_message = "SUCCESS";
					$remarkProforma = "";
				}else if($arrxml['code'] == "19"){
					$message = "Data booking order gagal dirposes (Timeout)";
					$this->session->set_flashdata('message_id',$ref_id."-".$order_id."-".$arrxml['code']."|DATA GAGAL DIPROSES (TIMEOUT), SILAHKAN MEMBAWA DOKUMEN DAN MENUJU LOKET|".$direct_approve."|".$remark);
					$r_message = "TIMEOUT";
					$remarkProforma = " (NO EDI_TO)";
				}else{
					$message = "Data booking order gagal dirposes";
					$this->session->set_flashdata('message_id',$ref_id."-".$order_id."-".$arrxml['code']."|DATA GAGAL DIPROSES, SILAHKAN MEMBAWA DOKUMEN DAN MENUJU LOKET|".$direct_approve."|".$remark);
					$r_message = "FAILED";
					$remarkProforma = " (NO EDI_TO)";
				}
				
				$kiosk_number 	= $this->session->userdata('KIOSK_NUMBER');
				$proformaBy 	= "K".$kiosk_number.$remarkProforma;
				
				$booking_queue = $this->session->userdata('ID_QUEUE');
				$this->pg->where(array('idqueue' => $booking_queue));
				$this->pg->update('tqueuedata', array('keterangan' => $proformaBy, 'bookingno' => $arrheader['no_do'], 'entryuser' => validate($this->session->userdata('USER_KIOSK')), 'ends2' => date('Y-m-d H:i:s'), 's2' => '2'));
				
				#START REPORT#
				$reportid = $this->session->userdata('ID_REPORT');
				foreach($arrcont as $cont){
					$SQD = "SELECT reportdetailid
							FROM treportdetail
							WHERE reportid = ".$this->db->escape($reportid)."
							AND f_contnumber = ".$this->db->escape($cont['CONTAINER']);
					$res_d = $this->pg->query($SQD);
					if($res_d->num_rows() > 0){
						$reportdetailid = $res_d->row()->reportdetailid;
						if($cont['CONT_SIZE'] == '20') $teus = 1;
						else $teus = 2;
						if(trim($cont['TEMP']) != ""){
							if($cont['RDC'] != ""){
								$paidthr = validate($cont['RDC'].':00','DATETIME');
							}
						}else{
							$paidthr = validate(substr($arrheader['date_until'],0,10)." 23:59:00",'DATETIME');
						}
						if($cont['SHFT_RFR'] == "") $shift = 0;
						else $shift = $cont['SHFT_RFR'];
						$arrupdate = array('reportid' => $reportid,
										   'b_kiosklogin' => validate($this->session->userdata('KIOSK_LOGIN')),
										   'b_userkiosk' => validate($this->session->userdata('USER_KIOSK')),
										   'b_ppjk' => validate($this->session->userdata('COMPANY_KIOSK')),
										   'b_ordersuccess'	  => date('Y-m-d H:i:s'),
										   'b_statusorder'	  => validate($r_message),
										   'f_orderno'	=> validate($arrheader['no_do']),
										   'f_shipper' => validate($this->session->userdata('CUSTOMER_NAME')),
										   'f_npwp' => validate($this->session->userdata('NPWP')),
										   'f_address' => validate(substr($this->session->userdata('CUSTOMER_ADDRESS'),0,100)),
										   'f_vessel' => validate($arrheader['vessel_name']),
										   'f_voyage' => validate($arrheader['voyage_in']),
										   'f_eta' => NULL,
										   'f_etd' => NULL,
										   'f_contnumber' => validate($cont['CONTAINER']),
										   'f_numberofbox' => 1,
										   'f_teus' => validate($teus),
										   'f_size' => validate($cont['CONT_SIZE']),
										   'f_type' => validate($cont['CONT_TYPE']),
										   'f_status' => validate($cont['FE']),
										   'f_pod' => validate(substr($arrheader['pod'],0,5)),
										   'f_spod' => validate(substr($arrheader['spod'],0,5)),
										   'f_stackingtime' => validate($cont['DIS'],'DATETIME'),
										   'f_paidthru' => validate($paidthr),
										   'f_storagedays' => validate(ceil($cont['SHFT_RFR']/3)),
										   'f_shift' => validate($shift),
										   'f_document' => validate($this->session->userdata('DOCUMENT')),
										   'g_reqcustdoctype' => validate($arrheader['req_dokumen']),
										   'g_reqcustdocno'	  => validate($arrheader['req_nomor']),
										   'g_reqcustdocdate' => validate($arrheader['req_tanggal'],'DATE'),
										   'g_rescustdoctype' => validate($arrheader['res_dokumen']),
										   'g_rescustdocno'	  => validate($arrheader['res_nomor']),
										   'g_rescustdocdate' => validate($arrheader['res_tanggal'],'DATE'),
										   'i_line' => validate($arrheader['agent']));
						#if($success == 1){
							$arrupdate['c_proformaresponse'] = date('Y-m-d H:i:s');
							$arrupdate['c_proformabykiosk']  = $proformaBy;
						#}
						$this->pg->where(array('reportdetailid' => $reportdetailid));
						$this->pg->update('treportdetail', $arrupdate);
					}else{
						if($cont['CONT_SIZE'] == '20') $teus = 1;
						else $teus = 2;
						if(trim($cont['TEMP']) != ""){
							if($cont['RDC'] != ""){
								$paidthr = validate($cont['RDC'].':00','DATETIME');
							}
						}else{
							$paidthr = validate(substr($arrheader['date_until'],0,10)." 23:59:00",'DATETIME');
						}
						if($cont['SHFT_RFR'] == "") $shift = 0;
						else $shift = $cont['SHFT_RFR'];
						$arrinsert = array('reportid' => $reportid,
										   'b_kiosklogin' => validate($this->session->userdata('KIOSK_LOGIN')),
										   'b_userkiosk' => validate($this->session->userdata('USER_KIOSK')),
										   'b_ppjk' => validate($this->session->userdata('COMPANY_KIOSK')),
										   'b_ordersuccess'	  => date('Y-m-d H:i:s'),
										   'b_statusorder'	  => validate($r_message),
										   'f_orderno'	=> validate($arrheader['no_do']),
										   'f_shipper' => validate($this->session->userdata('CUSTOMER_NAME')),
										   'f_npwp' => validate($this->session->userdata('NPWP')),
										   'f_address' => validate(substr($this->session->userdata('CUSTOMER_ADDRESS'),0,100)),
										   'f_vessel' => validate($arrheader['vessel_name']),
										   'f_voyage' => validate($arrheader['voyage_in']),
										   'f_eta' => NULL,
										   'f_etd' => NULL,
										   'f_contnumber' => validate($cont['CONTAINER']),
										   'f_numberofbox' => 1,
										   'f_teus' => validate($teus),
										   'f_size' => validate($cont['CONT_SIZE']),
										   'f_type' => validate($cont['CONT_TYPE']),
										   'f_status' => validate($cont['FE']),
										   'f_pod' => validate(substr($arrheader['pod'],0,5)),
										   'f_spod' => validate(substr($arrheader['spod'],0,5)),
										   'f_stackingtime' => validate($cont['DIS'],'DATETIME'),
										   'f_paidthru' => validate($paidthr),
										   'f_storagedays' => validate(ceil($cont['SHFT_RFR']/3)),
										   'f_shift' => validate($shift),
										   'f_document' => validate($this->session->userdata('DOCUMENT')),
										   'g_reqcustdoctype' => validate($arrheader['req_dokumen']),
										   'g_reqcustdocno'	  => validate($arrheader['req_nomor']),
										   'g_reqcustdocdate' => validate($arrheader['req_tanggal'],'DATE'),
										   'g_rescustdoctype' => validate($arrheader['res_dokumen']),
										   'g_rescustdocno'	  => validate($arrheader['res_nomor']),
										   'g_rescustdocdate' => validate($arrheader['res_tanggal'],'DATE'),
										   'i_line' => validate($arrheader['agent']));
						#if($success == 1){
							$arrinsert['c_proformaresponse'] = date('Y-m-d H:i:s');
							$arrinsert['c_proformabykiosk']  = $proformaBy;;
						#}
						$this->pg->insert('treportdetail', $arrinsert);
					}
				}
				#END REPORT#
				$arrayReturn['success'] = $success;
				$arrayReturn['message'] = $message;
				$arrayReturn['url'] = base_url('index.php/home/info');
				echo json_encode($arrayReturn);
			}
		}else{
			$arrayReturn['success'] = 0;
			$arrayReturn['notify']	= "Akses ditolak, silahkan melakukan pengajuan ulang";
			echo json_encode($arrayReturn);
		}
	}
	
	function pages_ext_customs($arrdata){
		$arrcont = $this->session->userdata('containers');
		if(array_key_exists(0, $arrcont['container'])){
			$index = 0;
			foreach($arrcont['container'] as $cont){
				$arrconttemp[$index]['cont_no'] = escape($cont['no_container']);
				$arrconttemp[$index]['isocode'] = escape($cont['isocode']);
				$arrconttemp[$index]['full_empty'] = escape($cont['full_empty']);
				$arrconttemp[$index]['bruto'] = escape($cont['bruto']);
				$arrconttemp[$index]['reefer'] = escape($cont['reefer']);
				$arrconttemp[$index]['temperature'] = escape($cont['temperature']);
				$arrconttemp[$index]['dg'] = escape($cont['dg']);
				$arrconttemp[$index]['imo_class'] = escape($cont['imo_class']);
				$arrconttemp[$index]['imo_no'] = escape($cont['imo_no']);
				$arrconttemp[$index]['seal_no'] = escape($cont['seal_no']);
				$arrconttemp[$index]['oogs'] = escape($cont['oogs']);
				$arrconttemp[$index]['in_time'] = escape($cont['in_time']);
				$index++;
			}
		}else{
			if(!empty($arrcont['container']['no_container'])){
				$arrconttemp[0]['cont_no'] = escape($arrcont['container']['no_container']);
				$arrconttemp[0]['isocode'] = escape($arrcont['container']['isocode']);
				$arrconttemp[0]['full_empty'] = escape($arrcont['container']['full_empty']);
				$arrconttemp[0]['bruto'] = escape($arrcont['container']['bruto']);
				$arrconttemp[0]['reefer'] = escape($arrcont['container']['reefer']);
				$arrconttemp[0]['temperature'] = escape($arrcont['container']['temperature']);
				$arrconttemp[0]['dg'] = escape($arrcont['container']['dg']);
				$arrconttemp[0]['imo_class'] = escape($arrcont['container']['imo_class']);
				$arrconttemp[0]['imo_no'] = escape($arrcont['container']['imo_no']);
				$arrconttemp[0]['seal_no'] = escape($arrcont['container']['seal_no']);
				$arrconttemp[0]['oogs'] = escape($arrcont['container']['oogs']);
				$arrconttemp[0]['in_time'] = escape($arrcont['container']['in_time']);
			}
		}
		$data['request_type'] = escape($arrdata['request_type']);
		$data['request_no'] = escape($arrdata['request_no']);
		$data['request_date'] = validate(escape($arrdata['request_date']),'DATE-STR');
		$data['response_type'] = escape($arrdata['response_type']);
		$data['response_no'] = escape($arrdata['response_no']);
		$data['response_date'] = validate(escape($arrdata['response_date']),'DATE-STR');
		$data['kpbc'] = escape($arrdata['kpbc']);
		$data['arr_doc'] = $this->get_combobox('doc_customs_imp');
		$data['arrcont'] = $arrconttemp;
		return $this->load->view('content/kiosk/import_ext_customs',$data,true);
	}
	
	function pages_ext_container($arrdata){
		$arrcont = $arrdata['containers']['container'];
		if(array_key_exists(0, $arrcont)){
			$index = 0;
			foreach($arrcont as $cont){
				$arrtemp[$index]['seq'] = escape($cont['seq']);
				$arrtemp[$index]['cont_no'] = escape($cont['no_container']);
				$arrtemp[$index]['isocode'] = escape($cont['isocode']);
				$arrtemp[$index]['full_empty'] = escape($cont['full_empty']);
				$arrtemp[$index]['bruto'] = escape($cont['bruto']);
				$arrtemp[$index]['reefer'] = escape($cont['reefer']);
				$arrtemp[$index]['temperature'] = escape($cont['temperature']);
				$arrtemp[$index]['dg'] = escape($cont['dg']);
				$arrtemp[$index]['imo_class'] = escape($cont['imo_class']);
				$arrtemp[$index]['imo_no'] = escape($cont['imo_no']);
				$arrtemp[$index]['seal_no'] = escape($cont['seal_no']);
				$arrtemp[$index]['oogs'] = escape($cont['oogs']);
				$arrtemp[$index]['in_time'] = escape($cont['in_time']);
				$arrtemp[$index]['rcn_time'] = escape($cont['rcn_time']);
				$index++;
			}
		}else{
			if(!empty($arrcont['no_container'])){
				$arrtemp[0]['seq'] = escape($arrcont['seq']);
				$arrtemp[0]['cont_no'] = escape($arrcont['no_container']);
				$arrtemp[0]['isocode'] = escape($arrcont['isocode']);
				$arrtemp[0]['full_empty'] = escape($arrcont['full_empty']);
				$arrtemp[0]['bruto'] = escape($arrcont['bruto']);
				$arrtemp[0]['reefer'] = escape($arrcont['reefer']);
				$arrtemp[0]['temperature'] = escape($arrcont['temperature']);
				$arrtemp[0]['dg'] = escape($arrcont['dg']);
				$arrtemp[0]['imo_class'] = escape($arrcont['imo_class']);
				$arrtemp[0]['imo_no'] = escape($arrcont['imo_no']);
				$arrtemp[0]['seal_no'] = escape($arrcont['seal_no']);
				$arrtemp[0]['oogs'] = escape($arrcont['oogs']);
				$arrtemp[0]['in_time'] = escape($arrcont['in_time']);
				$arrtemp[0]['rcn_time'] = escape($arrcont['rcn_time']);
			}
		}
		$data['arrpost'] = $this->input->post();
		$data['arrhdr'] = $arrdata;
		$data['arrdata'] = $arrtemp;
		return $this->load->view('content/kiosk/import_ext_container',$data,true);
	}
	
	function pages_ext_confirm($arrdata){
		$arrsession['npwp'] = $this->session->userdata('NPWP');
		$arrsession['cust_name'] = $this->session->userdata('CUSTOMER_NAME');
		$arrsession['cust_address'] = $this->session->userdata('CUSTOMER_ADDRESS');
		$npwp = $this->session->userdata('NPWP');
		$arrheader = $arrdata;
		$arrdetail = $arrdata['containers'];
		foreach($arrdata['chkcontainer'] as $field => $value){
			$arrchk[] = $value;
		}
		if(count($arrchk) > 0){
			foreach($arrchk as $a => $b){
				foreach($arrdetail['CONT_'.$b] as $c => $d){
					$arrcont[$a][$c] = strtoupper($d);
				}
			}
		}
		$arrheader = array_map('strtoupper', $arrheader);
		$data['arrsess'] = $arrsession;
		$data['arrhdr'] = $arrheader;
		$data['arrcont'] = $arrcont;
		return $this->load->view('content/kiosk/import_ext_confirm',$data,true);
	}
}