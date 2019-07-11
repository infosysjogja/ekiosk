<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Execute extends CI_Controller {
	public function __construct() {
        parent::__construct();
    }
	
	function process($type="",$act="", $id=""){
		if (!$this->session->userdata('LOGGED')) {
			$this->index();
			return;
		}else{
			if (strtolower($_SERVER['REQUEST_METHOD']) != "post") {
				echo 'access is forbidden'; exit();
			}else{
				$this->load->model("m_execute");
				$this->m_execute->process($type,$act,$id);
			}
		}
	}
	
	function download($act,$id){
		if($act=="excel"){
			$data = file_get_contents(base_url()."assets/files/CoarriCodecoKontainer.xls");
			$name = 'CoarriCodecoKontainer.xls';
			force_download($name, $data); 	
		}
	}
}