<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class M_service extends CI_Model {
	
	public function __construct(){
		parent::__construct();
		$this->db = $this->load->database('default', true);
		//$this->camco  = $this->load->database('camco', true);
	}
	
	function service($username, $password, $type, $mode, $data){
		$user = "KIOSK";
		$pass = "KIOSK@2017";
		if($username==$user && $password==$pass){
			$str_xml = WhiteSpaceXML(trim($data));
			$res     = simplexml_load_string($str_xml);
			$json    = json_encode($res);
			$arrxml  = json_decode($json,TRUE);
			$arrtype = array('set_vessel','set_customer');
			if(in_array($type,$arrtype)){
				if($type=="set_vessel"){
					$result = $this->set_vessel($mode, $arrxml);
				}else if($type=="set_customer"){
					$result = $this->set_customer($mode, $arrxml);
				}else{
					$result = "Access Denied!";
				}
			}else{
				$result = "Access Denied!";
			}
		}else{
			$result = "Access Denied!";
		}
		return $result;
	}
	
	function set_vessel($mode, $data){
		$error = 0;
		$message = "";
		$arrvessel = $data['vessel'];
		if(array_key_exists(0, $arrvessel)){
			$this->db->truncate('tr_vessel_agents');
			$this->db->truncate('tr_vessel_port');
			$this->db->delete('tr_vessel', array('1' => '1'));
			foreach($arrvessel as $index => $data){
				$arrdata['VESSEL_NAME'] 	= escape($data['vessel_name']);
				$arrdata['CALL_SIGN'] 		= escape($data['call_sign']);
				$arrdata['VOY_IN'] 			= escape($data['voyage_in']);
				$arrdata['VOY_OUT'] 		= escape($data['voyage_out']);
				$arrdata['CARRIER_ID']		= escape($data['carrier_id']);
				$arrdata['CARRIER_NAME'] 	= escape($data['carrier_name']);
				$arrdata['SV_IN'] 			= escape($data['service_in']);
				$arrdata['SV_OUT'] 			= escape($data['service_out']);
				$arrdata['ETA'] 			= escape($data['eta']);
				$arrdata['ETD'] 			= escape($data['etd']);
				$arrdata['YOT'] 			= escape($data['yot']);
				$arrdata['YCT'] 			= escape($data['yct']);
				$arrdata['STATUS'] 			= escape($data['status']);
				$arrdata['RECORD_TIME'] 	= date('Y-m-d H:i:s');
				$this->db->insert('tr_vessel', $arrdata);
				$id_hdr = $this->db->insert_id();
				if($id_hdr != 0){
					//AGENT
					if(array_key_exists(0, $arrvessel[$index]['agent'])){
						foreach($arrvessel[$index]['agent'] as $agent){
							$arragent['KD_VESSEL'] 			= $id_hdr;
							$arragent['AGENT_COSMOS_KD'] 	= escape($agent['id_cosmos']);
							$arragent['AGENT_NAME'] 		= escape($agent['name']);
							$exec_agent = $this->db->insert('tr_vessel_agents', $arragent);
							if(!$exec_agent){
								$error += 1;
								$message .= "Insert Agent";
							}
						}
					}else{
						$arragent['KD_VESSEL'] 			= $id_hdr;
						$arragent['AGENT_COSMOS_KD'] 	= escape($arrvessel[$index]['agent']['id_cosmos']);
						$arragent['AGENT_NAME'] 		= escape($arrvessel[$index]['agent']['name']);
						$exec_agent = $this->db->insert('tr_vessel_agents', $arragent);
						if(!$exec_agent){
							$error += 1;
							$message .= "Insert Agent";
						}
					}
					
					//PORT
					if(array_key_exists(0, $arrvessel[$index]['location'])){
						foreach($arrvessel[$index]['location'] as $port){
							$arrport['KD_VESSEL'] 	= $id_hdr;
							$arrport['PORT_KD'] 	= escape($port['id']);
							$arrport['PORT_NAME'] 	= escape($port['name']);
							$exec_port = $this->db->insert('tr_vessel_port', $arrport);
							if(!$exec_port){
								$error += 1;
								$message .= "Insert Port";
							}
						}
					}else{
						$arrport['KD_VESSEL'] 	= $id_hdr;
						$arrport['PORT_KD'] 	= escape($arrvessel[$index]['location']['id']);
						$arrport['PORT_NAME'] 	= escape($arrvessel[$index]['location']['name']);
						$exec_port = $this->db->insert('tr_vessel_port', $arrport);
						if(!$exec_port){
							$error += 1;
							$message .= "Insert Port";
						}
					}
				}else{
					$error += 1;
					$message .= "Insert Vessel";
				}
			}
		}else{
			$arrdata['VESSEL_NAME'] 	= escape($arrvessel['vessel_name']);
			$arrdata['CALL_SIGN'] 		= escape($arrvessel['call_sign']);
			$arrdata['VOY_IN'] 			= escape($arrvessel['voyage_in']);
			$arrdata['VOY_OUT'] 		= escape($arrvessel['voyage_out']);
			$arrdata['CARRIER_ID']		= escape($arrvessel['carrier_id']);
			$arrdata['CARRIER_NAME'] 	= escape($arrvessel['carrier_name']);
			$arrdata['SV_IN'] 			= escape($arrvessel['service_in']);
			$arrdata['SV_OUT'] 			= escape($arrvessel['service_out']);
			$arrdata['ETA'] 			= escape($arrvessel['eta']);
			$arrdata['ETD'] 			= escape($arrvessel['etd']);
			$arrdata['YOT'] 			= escape($arrvessel['yot']);
			$arrdata['YCT'] 			= escape($arrvessel['yct']);
			$arrdata['STATUS'] 			= escape($arrvessel['status']);
			$arrdata['RECORD_TIME'] 	= date('Y-m-d H:i:s');
			$this->db->insert('tr_vessel', $arrdata);
			$id_hdr = $this->db->insert_id();
			if($id_hdr != 0){
				//AGENT
				if(array_key_exists(0, $arrvessel['agent'])){
					foreach($arrvessel['agent'] as $agent){
						$arragent['KD_VESSEL'] 			= $id_hdr;
						$arragent['AGENT_COSMOS_KD'] 	= escape($agent['id_cosmos']);
						$arragent['AGENT_NAME'] 		= escape($agent['name']);
						$exec_agent = $this->db->insert('tr_vessel_agents', $arragent);
						if(!$exec_agent){
							$error += 1;
							$message .= "Insert Agent";
						}
					}
				}else{
					$arragent['KD_VESSEL'] 			= $id_hdr;
					$arragent['AGENT_COSMOS_KD'] 	= escape($arrvessel['agent']['id_cosmos']);
					$arragent['AGENT_NAME'] 		= escape($arrvessel['agent']['name']);
					$exec_agent = $this->db->insert('tr_vessel_agents', $arragent);
					if(!$exec_agent){
						$error += 1;
						$message .= "Insert Agent";
					}
				}
				
				//PORT
				if(array_key_exists(0, $arrvessel['location'])){
					foreach($arrvessel['location'] as $port){
						$arrport['KD_VESSEL'] 	= $id_hdr;
						$arrport['PORT_KD'] 	= escape($port['id']);
						$arrport['PORT_NAME'] 	= escape($port['name']);
						$exec_port = $this->db->insert('tr_vessel_port', $arrport);
						if(!$exec_port){
							$error += 1;
							$message .= "Insert Port";
						}
					}
				}else{
					$arrport['KD_VESSEL'] 	= $id_hdr;
					$arrport['PORT_KD'] 	= escape($arrvessel['location']['id']);
					$arrport['PORT_NAME'] 	= escape($arrvessel['location']['name']);
					$exec_port = $this->db->insert('tr_vessel_port', $arrport);
					if(!$exec_port){
						$error += 1;
						$message .= "Insert Port";
					}
				}
			}else{
				$error += 1;
				$message .= "Insert Vessel";
			}
		}
		$xml  = "<response>";
		if($error == 0){
			$xml .= "<code>00</code>";
			$xml .= "<desc>Successfully</desc>";
		}else{
			$xml .= "<code>01</code>";
			$xml .= "<desc>".$message."</desc>";
		}
		$xml .= "</response>";
		return $xml;
	}
	
	function set_customer($mode, $data){
		$error = 0;
		$message = "";
		if($mode == 1){
			$SQL = "SELECT ID 
					FROM tr_profile
					WHERE TRIM(NPWP) = ".$this->db->escape(trim($data['npwp']));
			$result = $this->db->query($SQL);
			if($result->num_rows() > 0){
				$message = "<response><code>14</code><desc>Record found</desc></response>"; 
			}else{
				$arrdata['PROFILE_ID'] = escape($data['cust_id']);
				$arrdata['PROFILE_ALT_ID'] = escape($data['alt_cust_id']);
				$arrdata['PROFILE_NAME'] = escape($data['cust_name']);
				$arrdata['PROFILE_ADDRESS_1'] = escape($data['address1']);
				$arrdata['PROFILE_ADDRESS_2'] = escape($data['address2']);
				$arrdata['ZIP_CODE'] = escape($data['zip_code']);
				$arrdata['CITY'] = escape($data['city']);
				$arrdata['NPWP'] = escape($data['npwp']);
				$arrdata['PAY_TYPE'] = escape($data['payment_type']);
				$arrdata['PAY_TERM'] = escape($data['payment_term']);
				$arrdata['CREATE_BY'] = escape($data['created_by']);
				$arrdata['CREATE_DATE'] = date('Y-m-d H:i:s');
				$exec = $this->db->insert('tr_profile', $arrdata);
				if($exec){
					$message = "<response><code>00</code><desc>Successfully</desc></response>";
				}
			}
		}else if($mode == 2){
			$SQL = "SELECT ID 
					FROM tr_profile
					WHERE TRIM(NPWP) = ".$this->db->escape(trim($data['npwp']));
			$result = $this->db->query($SQL);
			if($result->num_rows() > 0){
				$ID = $result->row()->ID;
				$arrdata['PROFILE_ID'] = escape($data['cust_id']);
				$arrdata['PROFILE_ALT_ID'] = escape($data['alt_cust_id']);
				$arrdata['PROFILE_NAME'] = escape($data['cust_name']);
				$arrdata['PROFILE_ADDRESS_1'] = escape($data['address1']);
				$arrdata['PROFILE_ADDRESS_2'] = escape($data['address2']);
				$arrdata['ZIP_CODE'] = escape($data['zip_code']);
				$arrdata['CITY'] = escape($data['city']);
				$arrdata['NPWP'] = escape($data['npwp']);
				$arrdata['PAY_TYPE'] = escape($data['payment_type']);
				$arrdata['PAY_TERM'] = escape($data['payment_term']);
				$arrdata['UPDATE_BY'] = escape($data['created_by']);
				$arrdata['UPDATE_DATE'] = date('Y-m-d H:i:s');
				$this->db->where(array('ID' => $ID));
				$exec = $this->db->update('tr_profile', $arrdata);
				if($exec){
					$message = "<response><code>00</code><desc>Successfully</desc></response>"; 
				}
			}else{
				$message = "<response><code>14</code><desc>Record not found</desc></response>";
			}
		}else{
			$message = "<response><code>01</code><desc>Access denied</desc></response>"; 
		}
		return $message;
	}
}