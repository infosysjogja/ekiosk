<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Document extends CI_Controller {
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
		if($this->session->userdata('LOGGED_KIOSK')){
			$data = array('_title_' 	  => 'KIOSK',
						  '_headers_' 	  => $headers,
						  '_header_' 	  => $this->load->view('content/header_document','',true),
						  '_content_' 	  => $this->load->view('content/home/index_2','',true),
						  '_footers_' 	  => $footers);
			$this->parser->parse('index', $data);
		}else{
			redirect(base_url('index.php'));
		}
	}
	
	function get_data($act="",$type=""){
		if(strtolower($_SERVER['REQUEST_METHOD']) != "post") {
			echo 'access is forbidden'; exit();
		}else{
			$this->load->model("m_document");
			$this->m_document->get_data($act,$type);
		}
	}
	
	function signout(){
		$this->load->model("m_document");
		$this->m_document->signout();
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
		$headers .= '<script type="text/javascript" src="'.base_url().'assets/js/jquery-1.7.2.min.js"></script>';
		$footers  = '<script type="text/javascript" src="'.base_url().'assets/js/jquery.min.js"></script>';
		$footers .= '<script type="text/javascript" src="'.base_url().'assets/vendor/jquery-ui/jquery-ui.min.js"></script>';
		$id = $this->session->flashdata('message_id');
		if($id == ""){
			redirect(base_url());
		}else{
			$arrmessage = explode("|",$id);
			$refid = explode("-",$arrmessage[0]);
			$data['message'] = $arrmessage[1];
			$data['ref_id']  = $refid[0];
		}
		$data = array('_title_' 	  => 'KIOSK',
					  '_headers_' 	  => $headers,
					  '_header_' 	  => $this->load->view('content/header','',true),
					  '_content_' 	  => $this->load->view('content/home/info',$data,true),
					  '_footers_' 	  => $footers);
		$this->parser->parse('index', $data);
	}
}
