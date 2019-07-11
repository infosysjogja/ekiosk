<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Kiosk extends CI_Controller {
	public $content;
	public function __construct() {
        parent::__construct();
    }
	
	public function index(){
		#Stylesheets
		$dok  = $this->session->userdata('DOCUMENT');
		$headers  = '<link href="'.base_url().'assets/css/bootstrap.min.css" rel="stylesheet">';
		$headers .= '<link href="'.base_url().'assets/css/bootstrap-extend.min.css" rel="stylesheet">';
		$headers .= '<link href="'.base_url().'assets/css/site.min.css" rel="stylesheet">';
		$headers .= '<link href="'.base_url().'assets/vendor/jquery-ui/jquery-ui.min.css" rel="stylesheet">';
		$headers .= '<link href="'.base_url().'assets/css/font-awesome.min.css" rel="stylesheet">';
		$headers .= '<link href="'.base_url().'assets/css/bootstrap-grid.css" rel="stylesheet">';
		if($dok == "EXPORT"){
			$headers .= '<link href="'.base_url().'assets/css/multistep.exp.min.css" rel="stylesheet">';
		}else{
			$headers .= '<link href="'.base_url().'assets/css/multistep.min.css" rel="stylesheet">';
		}
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
		//$footers .= '<script src="'.base_url().'assets/vendor/bootstrap-datetimepicker/moment-datetimepicker.js"></script>';
		$footers .= '<script src="'.base_url().'assets/vendor/bootstrap-datetimepicker/jquery.datetimepicker.full.js"></script>';
		$footers .= '<script type="text/javascript" src="'.base_url().'assets/js/multistep.min.js"></script>';
		$footers .= '<script type="text/javascript" src="'.base_url().'assets/js/alerts.js"></script>';
		$footers .= '<script src="'.base_url().'assets/vendor/sweetalert/sweetalert.min.js"></script>';
		$footers .= '<script type="text/javascript" src="'.base_url().'assets/js/script.js"></script>';
	
		if($this->session->userdata('LOGGED')){
			if($dok == "EXPORT"){
				$content = $this->load->view('content/kiosk/export','',true);
			}else if($dok == "IMPORT"){
				$content = $this->load->view('content/kiosk/import','',true);
			}else if($dok == "PERPANJANGAN IMPORT"){
				$content = $this->load->view('content/kiosk/import_ext','',true);
			}else{
				redirect(base_url('index.php'));
			}
			$data = array('_title_' 	  => 'KIOSK',
						  '_headers_' 	  => $headers,
						  '_header_' 	  => $this->load->view('content/header','',true),
						  '_content_' 	  => $content,
						  '_footers_' 	  => $footers);
			$this->parser->parse('index', $data);
		}else{
			redirect(base_url('index.php'));
		}
	}
	
	function execute_export($act="",$type=""){
		if(strtolower($_SERVER['REQUEST_METHOD']) != "post") {
			echo 'access is forbidden'; exit();
		}else{
			$this->load->model("m_kiosk");
			$this->m_kiosk->execute_export($act,$type);
		}
	}
	
	function execute_import($act="",$type=""){
		if(strtolower($_SERVER['REQUEST_METHOD']) != "post") {
			echo 'access is forbidden'; exit();
		}else{
			$this->load->model("m_kiosk");
			$this->m_kiosk->execute_import($act,$type);
		}
	}
	
	function execute_import_ext($act="",$type=""){
		if(strtolower($_SERVER['REQUEST_METHOD']) != "post") {
			echo 'access is forbidden'; exit();
		}else{
			$this->load->model("m_kiosk");
			$this->m_kiosk->execute_import_ext($act,$type);
		}
	}
	
	function get_combobox($act=""){
		if(strtolower($_SERVER['REQUEST_METHOD']) != "post") {
			echo 'access is forbidden'; exit();
		}else{
			$this->load->model("m_kiosk");
			$this->m_kiosk->get_combobox($act);
		}
	}
	
	function get_customs($type="",$act=""){
		if(strtolower($_SERVER['REQUEST_METHOD']) != "post") {
			echo 'access is forbidden'; exit();
		}else{
			$this->load->model("m_kiosk");
			$this->m_kiosk->get_customs($type,$act);
		}
	}
	
	function get_data($type="",$act=""){
		if(strtolower($_SERVER['REQUEST_METHOD']) != "post") {
			echo 'access is forbidden'; exit();
		}else{
			$this->load->model("m_kiosk");
			$this->m_kiosk->get_data($type,$act);
		}
	}
	
	function set_shift(){
		if(strtolower($_SERVER['REQUEST_METHOD']) != "post") {
			echo 'access is forbidden'; exit();
		}else{
			$arrayReturn = array();
			$shift = 1;
			$start = validate($this->input->post('start_date'),'DATETIME');
			$arrStart = explode(" ",$start);
			$arrStartDay = explode("-",$arrStart[0]);
			$arrStartTime = explode(":",$arrStart[1]);
			$end   = validate(substr($this->input->post('end_date'),0,16).":".$arrStartTime[2],'DATETIME');
			$selisih = strtotime($end) - strtotime($start);
			$minutes = $selisih/60;
			$hours = $minutes/60;
			$shift = ceil($hours/8);
			if($shift <= 0) $shift = 1;
			else $shift = $shift;
			$arrayReturn['shift'] = $shift;
			echo json_encode($arrayReturn);
		}
	}
}
