<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class M_document extends CI_Model {
	
	public function __construct(){
		parent::__construct();
		$this->db = $this->load->database('default',TRUE);
		$this->pg = $this->load->database('kiosk',TRUE);
	}
	
	function get_data($act,$type){
		$arrayReturn = array();
		$message = "";
		$success = 0;
		if($act=="customer"){
			$key = trim($this->input->post('key'));
			if($type == "cust"){
				$addsql = " AND PROFILE_ID = ".$this->db->escape($key);
			}else{
				$addsql = " AND NPWP = ".$this->db->escape($key);
			}
			$SQL = "SELECT PROFILE_ID, PROFILE_NAME, CONCAT(PROFILE_ADDRESS_1,' ',PROFILE_ADDRESS_2) AS PROFILE_ADDRESS, NPWP 
					FROM tr_profile 
					WHERE 1=1".$addsql."
					ORDER BY ID DESC 
					LIMIT 1";
			$result = $this->db->query($SQL);
			if($result->num_rows() > 0){
				$arrdata = $result->row_array();
				$arr_session['LOGGED'] = true;
				$arr_session['IP'] = $_SERVER['REMOTE_ADDR'];
				$arrayReturn['cust_id'] = $arrdata['PROFILE_ID'];
				$arrayReturn['cust_name'] = $arrdata['PROFILE_NAME'];
				$arrayReturn['cust_address'] = $arrdata['PROFILE_ADDRESS'];
				$success = 1;
			}
			$arrayReturn['result'] = $success;
			$this->db->close();
			$this->db->initialize();
			echo json_encode($arrayReturn);
		}else if($act == "dokumenorder"){
			$url = "";
			$id = $this->session->userdata('SCAN');
			$doc = $this->input->post('document_type');
			$ip = $_SERVER['REMOTE_ADDR'];
			$array_dokumen = array('EXPORT','IMPORT','PERPANJANGAN IMPORT');
			if(in_array($doc,$array_dokumen)){
				$success = 1;
			}else{
				$message = "Pilih dokumen order";
			}
			$arrayReturn['url'] = $url;
			$arrayReturn['success'] = $success;
			$arrayReturn['message'] = $message;
			echo json_encode($arrayReturn);
		}else if($act == "profile"){
			$cust_id = trim($this->input->post('customer_id'));
			$npwp = trim($this->input->post('npwp'));
			$arr_session = array();
			$SQL = "SELECT PROFILE_ID, PROFILE_NAME, CONCAT(PROFILE_ADDRESS_1,' ',PROFILE_ADDRESS_2) AS PROFILE_ADDRESS, NPWP 
					FROM tr_profile 
					WHERE (NPWP = ".$this->db->escape($npwp)." AND PROFILE_ID = ".$this->db->escape($cust_id).")
					ORDER BY ID DESC 
					LIMIT 1";
			$result = $this->db->query($SQL);
			if($result->num_rows() > 0){
				$id = $this->session->userdata('SCAN');
				$arrdata = $result->row_array();
				$arr_session['LOGGED'] = true;
				$arr_session['IP'] = $_SERVER['REMOTE_ADDR'];
				$arr_session['NPWP'] = escape($arrdata['NPWP']);
				$arr_session['CUSTOMER_ID'] = escape($arrdata['PROFILE_ID']);
				$arr_session['CUSTOMER_NAME'] = escape($arrdata['PROFILE_NAME']);
				$arr_session['CUSTOMER_ADDRESS'] = escape($arrdata['PROFILE_ADDRESS']);
				$arr_session['DOCUMENT'] = escape($this->input->post('document_type'));
				$arr_session['QUEUE'] = $id;
				$success = 1;
				$message = "Akses Berhasil";
				$url = base_url('application.php');
			}else{
				$message = "Data tidak ditemukan";
				$url = "";
			}
			$this->session->set_userdata($arr_session,true);
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
	
	function page_document(){
		return $this->load->view('content/home/document','',true);
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
			$arrdtl['PASSWORD'] = md5($this->input->post('password_pj'));
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
					AND TRIM(PASSWORD) = ".$this->db->escape(MD5($old_pass));
			$result = $this->db->query($SQL);
			if($result->num_rows() > 0){
				$ID = $result->row()->ID;
				if($new_pass == $new_pass_cf){
					$this->db->where(array('ID' => $ID));
					$this->db->update('tr_user_dtl', array('PASSWORD' => MD5($new_pass)));
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
		}
	}
	
	function page_registrasi_konfirmasi($arrdata){
		$data['arrdata'] = $arrdata;
		return $this->load->view('content/home/register_konfirmasi',$data,true);
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
		$result = $this->pg->query($SQL);
		if($result->num_rows() > 0){
			$arrdata = $result->result_array();
			foreach($arrdata as $data){
				$this->pg->where(array('reportdetailid' => $data['reportdetailid']));
				$this->pg->update('treportdetail', array('c_kiosklogout' => $this->session->userdata('KIOSK_LOGOUT')));
			}
		}
		#END REPORT#
		$this->session->sess_destroy();
		$arrayReturn['url'] = base_url();
		echo json_encode($arrayReturn);
	}
	
}
?>