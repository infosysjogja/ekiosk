<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
	public $content;
	public function __construct() {
        parent::__construct();
		$this->connect = $this->load->database('kiosk', TRUE);
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
		$headers .= '<link href="'.base_url().'assets/vendor/bootstrap-datetimepicker/jquery.datetimepicker.min.css" rel="stylesheet">';
		$headers .= '<link href="'.base_url().'assets/vendor/keyboard/jquery.keypad.css" rel="stylesheet">';
		$footers  = '<script type="text/javascript" src="'.base_url().'assets/js/jquery.min.js"></script>';
		$footers .= '<script type="text/javascript" src="'.base_url().'assets/vendor/jquery-ui/jquery-ui.min.js"></script>';
		$footers .= '<script type="text/javascript" src="'.base_url().'assets/vendor/keyboard/jquery.plugin.js"></script>';
		$footers .= '<script type="text/javascript" src="'.base_url().'assets/vendor/keyboard/jquery.keypad.js"></script>';
		$footers .= '<script type="text/javascript" src="'.base_url().'assets/vendor/bootstrap-datetimepicker/jquery.datetimepicker.full.js"></script>';
		$footers .= '<script type="text/javascript" src="'.base_url().'assets/js/multistep.home.min.js"></script>';
		$footers .= '<script type="text/javascript" src="'.base_url().'assets/js/alerts.js"></script>';
		$footers .= '<script type="text/javascript" src="'.base_url().'assets/vendor/sweetalert/sweetalert.min.js"></script>';
		$footers .= '<script type="text/javascript" src="'.base_url().'assets/js/script.js"></script>';
		$data = array('_title_' 	  => 'KIOSK',
					  '_headers_' 	  => $headers,
					  '_header_' 	  => $this->load->view('content/header_home','',true),
					  '_content_' 	  => $this->load->view('content/home/index_1','',true),
					  '_footers_' 	  => $footers);
		$this->parser->parse('index', $data);
	}
	
	function get_data($act="",$type=""){
		if(strtolower($_SERVER['REQUEST_METHOD']) != "post") {
			echo 'access is forbidden'; exit();
		}else{
			$this->load->model("m_home");
			$this->m_home->get_data($act,$type);
		}
	}
	
	function signout(){
		$this->load->model("m_home");
		$this->m_home->signout();
	}
	
	function register(){
		echo $this->load->view('content/home/register','',true);
	}
	
	function password(){
		$data['title'] = "Ganti Password";
		echo $this->load->view('content/home/password',$data,true);
	}
	
	function execute($act="",$type=""){
		if(strtolower($_SERVER['REQUEST_METHOD']) != "post") {
			echo 'access is forbidden'; exit();
		}else{
			$this->load->model("m_home");
			$this->m_home->execute($act,$type);
		}
	}
	
	function info(){
		error_reporting(0);
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
		$headers .= '<link href="'.base_url().'assets/vendor/bootstrap-datetimepicker/jquery.datetimepicker.min.css" rel="stylesheet">';
		$headers .= '<link href="'.base_url().'assets/vendor/keyboard/jquery.keypad.css" rel="stylesheet">';
		$footers  = '<script type="text/javascript" src="'.base_url().'assets/js/jquery.min.js"></script>';
		$footers .= '<script type="text/javascript" src="'.base_url().'assets/vendor/jquery-ui/jquery-ui.min.js"></script>';
		$footers .= '<script type="text/javascript" src="'.base_url().'assets/vendor/keyboard/jquery.plugin.js"></script>';
		$footers .= '<script type="text/javascript" src="'.base_url().'assets/vendor/keyboard/jquery.keypad.js"></script>';
		$footers .= '<script type="text/javascript" src="'.base_url().'assets/vendor/bootstrap-datetimepicker/jquery.datetimepicker.full.js"></script>';
		$footers .= '<script type="text/javascript" src="'.base_url().'assets/js/multistep.home.min.js"></script>';
		$footers .= '<script type="text/javascript" src="'.base_url().'assets/js/alerts.js"></script>';
		$footers .= '<script type="text/javascript" src="'.base_url().'assets/vendor/sweetalert/sweetalert.min.js"></script>';
		$footers .= '<script type="text/javascript" src="'.base_url().'assets/js/script.js"></script>';
		
		$id			= $this->session->flashdata('message_id');
		$queueid	= $this->session->userdata('ID_QUEUE');
		$reportid	= $this->session->userdata('ID_REPORT');
		$kioskid 	= $this->session->userdata('KIOSK_NUMBER');
		if($id == ""){
			$arrdata['message']	  		= "Failed to load page";
			$arrdata['ref_id']	  		= 0;
			$arrdata['order_id']  		= 0;
			$arrdata['code']  	  		= 14;
			$arrdata['direct_approve']  = 'N';
			$arrdata['remark']  		= '';
			$arrdata['queueid']  		= $queueid;
			$arrdata['reportid']  		= $reportid;
			$arrdata['kioskid']  		= $kioskid;
		}else{
			$arrmessage = explode("|",$id);
			$refid 						= explode("-", $arrmessage[0]);
			$arrdata['message']	  		= $arrmessage[1];
			$arrdata['ref_id']	  		= $refid[0];
			$arrdata['order_id']  		= $refid[1];
			$arrdata['code']  	  		= $refid[2];
			$arrdata['direct_approve']  = $arrmessage[2];
			$arrdata['remark']  		= $arrmessage[3];
			$arrdata['queueid']  		= $queueid;
			$arrdata['reportid']  		= $reportid;
			$arrdata['kioskid']  		= $kioskid;
		}
		$data = array('_title_' 	  => 'KIOSK',
					  '_headers_' 	  => $headers,
					  '_header_' 	  => $this->load->view('content/header_document','',true),
					  '_content_' 	  => $this->load->view('content/home/info',$arrdata,true),
					  '_footers_' 	  => $footers);
		$this->parser->parse('index', $data);
	}
	
	function confirm($act=""){
		if($act=="execute"){
			if(strtolower($_SERVER['REQUEST_METHOD']) != "post") {
				echo 'access is forbidden'; exit();
			}else{
				$this->load->model("m_home");
				$this->m_home->confirm($act);
			}
		}else{
			$data['title'] = "KONFIRMASI";
			echo $this->load->view('content/home/confirm',$data,true);
		}
	}
}
