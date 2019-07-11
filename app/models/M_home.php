<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class M_home extends CI_Model {
	
	public function __construct(){
		parent::__construct();
		$this->db = $this->load->database('default',TRUE);
		$this->pg = $this->load->database('kiosk',TRUE);
	}
	
	function get_lock_kiosk(){
		$status = "LOCK";
		$SQL = "SELECT * FROM td_status_kiosk";
		$result = $this->db->query($SQL);
		if($result->num_rows() > 0){
			$status = $result->row()->STATUS;
		}
		return $status;
	}
	
	function get_data($act,$type){
		$arrayReturn = array();
		$message = "";
		$success = 0;
		if($act=="customer"){
			$key = trim($this->input->post('key'));
			if($type == "id"){
				$addsql = " AND PROFILE_ID = ".$this->db->escape($key);
			}else{
				$addsql = " AND NPWP = ".$this->db->escape($key);
			}
			$SQL = "SELECT PROFILE_ID, PROFILE_NAME, CONCAT(PROFILE_ADDRESS_1,' ',IFNULL(PROFILE_ADDRESS_2,'')) AS PROFILE_ADDRESS, NPWP 
					FROM tr_profile 
					WHERE 1=1".$addsql."
					ORDER BY ID DESC 
					LIMIT 1";
			$result = $this->db->query($SQL);
			if($result->num_rows() > 0){
				$arrdata = $result->row_array();
				$arr_session['LOGGED'] = true;
				$arr_session['IP'] = $_SERVER['REMOTE_ADDR'];
				$arrayReturn['npwp'] = $arrdata['NPWP'];
				$arrayReturn['cust_id'] = $arrdata['PROFILE_ID'];
				$arrayReturn['cust_name'] = $arrdata['PROFILE_NAME'];
				$arrayReturn['cust_address'] = $arrdata['PROFILE_ADDRESS'];
				$success = 1;
			}
			$arrayReturn['result'] = $success;
			$this->db->close();
			$this->db->initialize();
			echo json_encode($arrayReturn);
		}else if($act == "scanantrian"){
			$id = $this->input->post('scan');
			$this->session->unset_userdata('ID_QUEUE');
			$ip = $_SERVER['REMOTE_ADDR'];
			$status = $this->get_lock_kiosk();
			if($status == "LOCK"){
				$SQL = "SELECT A.idqueue, A.passcode, A.queuecodenumber, TO_CHAR(A.starts1,'DDMMYYYY') AS queuedate, LPAD(CAST(B.channelnumber AS TEXT),2,'0') AS channelnumber
						FROM tqueuedata A
						INNER JOIN tchannel B ON B.queue_assign = A.idqueue
						WHERE A.branch = '1'
						AND TO_CHAR(NOW(),'DDMMYYYY') = TO_CHAR(A.starts1,'DDMMYYYY')
						AND A.s1 < '3'
						AND A.s2 = '0'
						AND A.queuecode IN ('A','C')
						AND B.usestatus = '1'
						AND A.passcode = ".$this->db->escape($id)."
						AND B.ipaddress = ".$this->db->escape($ip);
			}else{
				$SQL = "SELECT A.idqueue, A.passcode, A.queuecodenumber, TO_CHAR(A.starts1,'DDMMYYYY') AS queuedate, LPAD(CAST(B.channelnumber AS TEXT),2,'0') AS channelnumber
						FROM tqueuedata A
						LEFT JOIN tchannel B ON B.queue_assign = A.idqueue
						WHERE A.branch = '1'
						AND A.s1 < '3'
						AND A.s2 = '0'
						AND A.queuecode IN ('A','C')
						AND TO_CHAR(NOW(),'DDMMYYYY') = TO_CHAR(A.starts1,'DDMMYYYY')
						AND A.passcode = ".$this->db->escape($id);
			}
			#echo $SQL; die();
			$result = $this->pg->query($SQL);
			if($result->num_rows() > 0){
				$success = 1;
				$idqueue = $result->row()->idqueue;
				$codenumber = $result->row()->queuecodenumber;
				$passcode = $result->row()->passcode;
				$queuedate = $result->row()->queuedate;
				$channelnumber = $result->row()->channelnumber;
				
				$this->session->set_userdata('ID_QUEUE',$idqueue);
				$this->session->set_userdata('CODE_QUEUE',$codenumber);
				$this->session->set_userdata('PASSCODE',$passcode);
				$this->session->set_userdata('KIOSK_NUMBER',$channelnumber);
				//log_act_hdr($id);
				//log_act_hdr($id,'QUEUE_CODE',$codenumber);
				//UPDATE KIOSK USED
				$this->pg->where(array('ipaddress' => $ip));
				$this->pg->update('tchannel', array('usestatus' => '2', 'panggilannum' => '0'));
				
				#REPORT#
				$SQR = "SELECT reportid
						FROM treport
						WHERE a_passcode = ".$this->db->escape($passcode)."
						AND a_qnumber = ".$this->db->escape($codenumber)."
						AND TO_CHAR(a_tanggal,'DDMMYYYY') = ".$this->db->escape($queuedate);
				$res_r = $this->pg->query($SQR);
				if($res_r->num_rows() > 0){
					$reportid = $res_r->row()->reportid;
					$this->session->set_userdata('ID_REPORT',$reportid);
					$this->session->set_userdata('KIOSK_LOGIN',date('Y-m-d H:i:s'));
					//test
					$this->pg->where(array('reportid' => $reportid));
					$this->pg->update('treport', array('b_kiosklogin' => date('Y-m-d H:i:s')));
				}
				
				$SQD = "SELECT pas, login, logout, queue
						FROM tqueuedatadoc
						WHERE pas = ".$this->db->escape($passcode)."
						AND queue = ".$this->db->escape($codenumber);
				$res_d = $this->pg->query($SQD);
				if($res_d->num_rows() > 0){
					$kiosk_login = $res_d->row()->login;
					$kiosk_logout = $res_d->row()->logout;
					$this->pg->where(array('pas' => $passcode, 'queue' => $codenumber));
					$this->pg->update('tqueuedatadoc', array('login' => 1, 'logout' => 0));
				}else{
					$this->pg->insert('tqueuedatadoc', array('pas' => $passcode, 'queue' => $codenumber, 'login' => 1, 'logout' => 0));
				}
				
				/*
				#REPORT
				$this->pg->where(array('a_passcode' => $passcode, 'a_qnumber' => $codenumber, 'TO_CHAR(a_tanggal,\'DDMMYYYY\')' => date('dmY')));
				$this->pg->update('treport', array('b_kiosklogin' => date('Y-m-d H:i:s')));
				*/
			}else{
				$message = "Passcode tidak ditemukan";
			}
			$arrayReturn['page'] = $this->page_login($id);
			$arrayReturn['success'] = $success;
			$arrayReturn['message'] = $message;
			echo json_encode($arrayReturn);
		}else if($act == "login"){
			$id = $this->input->post('scan');
			$url = "";
			$useraccess = $this->input->post('useraccess');
			$passaccess = $this->input->post('passaccess');
			$kiosk_number = $this->session->userdata('KIOSK_NUMBER');
			$SQL = "SELECT B.ID AS ID_DTL, B.USERNAME, A.COMPANY_NAME
					FROM tr_user_hdr A
					INNER JOIN tr_user_dtl B ON B.ID_USER = A.ID
					WHERE B.ACTIVE = 'Y'
					AND UPPER(B.USERNAME) = ".$this->db->escape(strtoupper($useraccess))."
					AND B.PASSWORD = ".$this->db->escape(base64_encode($passaccess));
			$result = $this->db->query($SQL);
			if($result->num_rows() > 0){
				$success = 1;
				$arrdata = $result->row_array();
				$arr_session['LOGGED_KIOSK'] = true;
				$arr_session['IP'] = $_SERVER['REMOTE_ADDR'];
				$arr_session['COMPANY_KIOSK'] = $arrdata['COMPANY_NAME'];
				$arr_session['USER_KIOSK'] = $arrdata['USERNAME'];
				$arr_session['SCAN'] = $id;
				$this->session->set_userdata($arr_session,true);
				log_act_hdr($id, 'USER_LOGIN', $arrdata['USERNAME']);
				$url = site_url('document');
				//LAST LOGIN
				$this->db->where(array('ID' => $arrdata['ID_DTL']));
				$this->db->update('tr_user_dtl', array('LAST_LOGIN' => date('Y-m-d H:i:s')));
				
				$id_queue = $this->session->userdata('ID_QUEUE');
				$this->pg->where(array('idqueue' => $id_queue));
				$this->pg->update('tqueuedata', array('starts2' => date('Y-m-d H:i:s'), 'keterangan' => 'KIOSK '.$kiosk_number));
			}else{
				$page = "";
				$message = "User tidak valid";
			}
			$arrayReturn['success'] = $success;
			$arrayReturn['message'] = $message;
			$arrayReturn['url'] = $url;
			$this->db->close();
			$this->db->initialize();
			echo json_encode($arrayReturn);
		}
	}
	
	function page_login($id){
		$data['scan'] = strtoupper($id);
		return $this->load->view('content/home/login',$data,true);
	}

	function last_login($ID){
		$data = array('LAST_LOGIN' => date('Y-m-d H:i:s'));
		$this->db->where('ID', $ID);
		$this->db->update('app_user', $data);
	}
	
	function execute($act, $type){
		$arrayReturn = array();
		$message = "";
		$error = 0;
		if($act == "registrasi"){
			$arrdata = $this->input->post();
			$arrpost = array_map('strtoupper', $arrdata);
			$arrhdr['TYPE'] = $arrpost['type_perusahaan'];
			$arrhdr['COMPANY_NAME'] = $arrpost['nama_persh'];
			$arrhdr['NPWP'] = $arrpost['npwp_persh'];
			$arrhdr['ADDRESS'] = $arrpost['alamat_persh'];
			$arrhdr['CITY'] = $arrpost['kota_persh'];
			$arrhdr['ZIP_CODE'] = $arrpost['kode_pos_persh'];
			$arrhdr['TELP'] = $arrpost['telp_persh'];
			$arrhdr['FAX'] = $arrpost['fax_persh'];
			$arrhdr['EMAIL'] = $arrpost['email_persh'];
			$arrdtl['FIRST_NAME'] = $arrpost['nama_pj1'];
			$arrdtl['LAST_NAME'] = $arrpost['nama_pj2'];
			$arrdtl['NO_IDENTITY'] = $arrpost['identity_pj'];
			$arrdtl['NO_HP'] = $arrpost['hp_pj'];
			$arrdtl['EMAIL'] = $arrpost['email_pj'];
			$arrdtl['USERNAME'] = $arrpost['username_pj'];
			$arrdtl['PASSWORD'] = base64_encode($this->input->post('password_pj'));
			$SQL = "SELECT ID 
					FROM tr_user_hdr
					WHERE TRIM(NPWP) = ".$this->db->escape(trim($arrpost['npwp_persh']));
			$result = $this->db->query($SQL);
			if($result->num_rows() > 0){
				$id = $result->row()->ID;
			}else{
				$this->db->insert('tr_user_hdr', $arrhdr);
				$id  = $this->db->insert_id();
			}
			if($id != 0){
				$arrdtl['ID_USER'] = $id;
				$this->db->insert('tr_user_dtl', $arrdtl);
				$result = $this->db->affected_rows();
				if($result > 0){
					$message = "Data berhasil diproses";
				}
			}else{
				$error += 1;
				$message = "Data gagal diproses";
			}
			if($error == 0){
				echo "MSG#OK#".$message;
			}else{
				echo "MSG#ERR#".$message;
			}
		}else if($act == "password"){
			$username_pass = strtoupper($this->input->post('username_pass'));
			$old_pass = $this->input->post('old_pass');
			$new_pass = $this->input->post('new_pass');
			$new_pass_cf = $this->input->post('new_pass_cf');
			$SQL = "SELECT ID 
					FROM tr_user_dtl
					WHERE TRIM(USERNAME) = ".$this->db->escape(trim($username_pass))."
					AND TRIM(PASSWORD) = ".$this->db->escape(base64_encode($old_pass));
			$result = $this->db->query($SQL);
			if($result->num_rows() > 0){
				$ID = $result->row()->ID;
				if($new_pass == $new_pass_cf){
					$this->db->where(array('ID' => $ID));
					$this->db->update('tr_user_dtl', array('PASSWORD' => base64_encode($new_pass)));
					$message = "Success";
				}else{
					$error += 1;
					$message = "Password tidak sama";
				}
			}else{
				$error += 1;
				$message = "Data tidak ditemukan";
			}
			if($error == 0){
				echo "MSG#OK#".$message;
			}else{
				echo "MSG#ERR#".$message;
			}
		}else if($act == "timeout"){
			$queueid	= $this->input->post('queueid');
			$reportid	= $this->input->post('reportid');
			$kioskid 	= $this->session->userdata('KIOSK_NUMBER');
			$remark 	= "K".$kioskid." (NO EDI_TO)";
			$this->pg->where(array('idqueue' => $queueid));
			$this->pg->update('tqueuedata', array('keterangan' => $remark));
			
			$this->pg->where(array('reportid' => $reportid));
			$this->pg->update('treportdetail', array('c_proformaissuancekiosk' => date('Y-m-d H:i:s'), 'c_proformabykiosk' => $remark));
			return true;
		}
	}
	
	function page_registrasi_konfirmasi($arrdata){
		$data['arrdata'] = $arrdata;
		return $this->load->view('content/home/register_konfirmasi',$data,true);
	}
	/*
	function confirm($act){
		if($act=="execute"){
			$arrayReturn = array();
			$id_queue = $this->session->userdata('ID_QUEUE');
			$id = $this->input->post('id');
			if($id == "Y"){
				$this->next_steps();
				$this->session->set_userdata('starts2', date('Y-m-d H:i:s'));
				$url = base_url('index.php/document');
			}else{
				$ip = $_SERVER['REMOTE_ADDR'];
				$this->pg->where(array('ipaddress' => $ip));
				$this->pg->update('tchannel', array('usestatus' => '0', 'queue_assign' => NULL, 'assignedto' => NULL, 'panggilannum' => '0'));
				$this->session->sess_destroy();
				$url = base_url('index.php');
			}
			$arrayReturn['url'] = $url;
			echo json_encode($arrayReturn);
		}
	}
	*/
	function confirm($act){
		if($act=="execute"){
			$arrayReturn = array();
			$id_queue = $this->session->userdata('ID_QUEUE');
			$id = $this->input->post('id');
			
			#START REPORT#
			$reportid = $this->session->userdata('ID_REPORT');
			$this->session->set_userdata('KIOSK_LOGOUT', date('Y-m-d H:i:s'));
			$SQL = "SELECT reportdetailid 
					FROM treportdetail 
					WHERE c_kiosklogout IS NULL
					AND reportid = ".$this->db->escape($reportid);
			$result = $this->pg->query($SQL);
			if($result->num_rows() > 0){
				$arrdata = $result->result_array();
				foreach($arrdata as $data){
					$this->pg->where(array('reportdetailid' => $data['reportdetailid']));
					$this->pg->update('treportdetail', array('c_kiosklogout' => date('Y-m-d H:i:s')));
				}
			}
			#END REPORT#
			if($id == "Y"){
				$this->next_steps($id_queue);
				$this->session->set_userdata('KIOSK_LOGIN', date('Y-m-d H:i:s'));
				$this->session->set_userdata('starts2', date('Y-m-d H:i:s'));
				$url = base_url('index.php/document');
			}else{
				$passcode 	= $this->session->userdata('PASSCODE');
				$codenumber = $this->session->userdata('CODE_QUEUE');
				$this->pg->where(array('pas' => $passcode, 'queue' => $codenumber));
				$this->pg->update('tqueuedatadoc', array('login' => 1, 'logout' => 1));
				
				$ip = $_SERVER['REMOTE_ADDR'];
				$this->pg->where(array('ipaddress' => $ip));
				$this->pg->update('tchannel', array('usestatus' => '0', 'queue_assign' => NULL, 'assignedto' => NULL, 'panggilannum' => '0'));
				$this->session->sess_destroy();
				$url = base_url('index.php');
			}
			$arrayReturn['url'] = $url;
			echo json_encode($arrayReturn);
		}
	}
	
	function signout(){
		$arrayReturn = array();
		$ip = $_SERVER['REMOTE_ADDR'];
		$this->pg->where(array('ipaddress' => $ip));
		$this->pg->update('tchannel', array('usestatus' => '0', 'queue_assign' => NULL, 'assignedto' => NULL, 'panggilannum' => '0'));
		#START REPORT#
		$reportid = $this->session->userdata('ID_REPORT');
		$this->session->set_userdata('KIOSK_LOGOUT', date('Y-m-d H:i:s'));
		$SQL = "SELECT reportdetailid 
				FROM treportdetail 
				WHERE c_kiosklogout IS NULL
				AND reportid = ".$this->db->escape($reportid);
		#echo $SQL; die();
		$result = $this->pg->query($SQL);
		if($result->num_rows() > 0){
			$arrdata = $result->result_array();
			foreach($arrdata as $data){
				$this->pg->where(array('reportdetailid' => $data['reportdetailid']));
				$this->pg->update('treportdetail', array('c_kiosklogout' => $this->session->userdata('KIOSK_LOGOUT')));
			}
		}
		#END REPORT#
		$passcode 	= $this->session->userdata('PASSCODE');
		$codenumber = $this->session->userdata('CODE_QUEUE');
		$this->pg->where(array('pas' => $passcode, 'queue' => $codenumber));
		$this->pg->update('tqueuedatadoc', array('login' => 1, 'logout' => 1));
		$this->session->sess_destroy();
		$arrayReturn['url'] = base_url();
		echo json_encode($arrayReturn);
	}
	
	/*
	function signout(){
		$arrayReturn = array();
		$ip = $_SERVER['REMOTE_ADDR'];
		$this->pg->where(array('ipaddress' => $ip));
		$this->pg->update('tchannel', array('usestatus' => '0', 'queue_assign' => NULL, 'assignedto' => NULL, 'panggilannum' => '0'));
		
		#START REPORT#
		$reportid = $this->session->userdata('ID_REPORT');
		$this->pg->where(array('ipaddress' => $ip));
		$this->pg->update('tchannel', array('usestatus' => '0', 'queue_assign' => NULL, 'assignedto' => NULL, 'panggilannum' => '0'));
		#END REPORT#
		
		$this->session->sess_destroy();
		$arrayReturn['url'] = base_url();
		echo json_encode($arrayReturn);
		
		
	}
	*/
	
	function next_steps($id_queue){
		//$id_queue = $this->session->userdata('ID_QUEUE');
		$kiosk_number 	= $this->session->userdata('KIOSK_NUMBER');
		$starts2 = $this->session->userdata('starts2');
		$SQL = "SELECT queuecode, queuenumber, queuecodenumber, passcode, assigned, s1, s2, s3, s4, s5, keterangan, countertujuancode,
				countertujuannumber, queuetype, panggilannum, branch, starts1, ends1, starts2, ends2, starts3, ends3, starts4, ends4, 
				starts5, ends5, proformanum, entryuser, bookingno, countertujuancodek, countertujuannumberk, starts1k, ends1k
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
			$arrnext['keterangan'] = "K".$kiosk_number;
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
}
?>