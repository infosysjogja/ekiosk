<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Popup extends CI_Controller {
	public function __construct() {
        parent::__construct();
    }
	
	function popup_search($type="",$id="",$popup="",$ajax=""){
		$this->load->model("m_popup");
		$arrdata = $this->m_popup->popup_search($type,$id,$popup,$ajax);
		if($this->input->post("ajax")){
			echo $arrdata;
		}else{
			echo $this->load->view("content/newtable", $arrdata, true);	
		}
	}
	
	function pilih($id="",$ajax=""){
		$this->load->model("m_popup");
		$this->m_popup->pilih($id,$ajax);
	}
	
	function autocomplete($type="",$act=""){
		if (!$this->session->userdata('LOGGED')) {
			$this->index();
			return;
		}else{
			if (strtolower($_SERVER['REQUEST_METHOD']) != "post") {
				echo 'access is forbidden'; exit();
			}else{
				$this->load->model("m_popup");
				$this->m_popup->execute($type,$act);
			}
		}
	}
	
	public function notification($act=""){
		if (!$this->session->userdata('LOGGED')){
			$this->index();
			return;
		}
		$id = ($id!="")?$id:$this->input->post('id');
		if($act=="schduller"){
			$json_return = array();
			$this->load->model('m_popup');
			$json_return['jml'] = $this->m_popup->execute('notification');
			echo json_encode($json_return);
		}else{
			$this->load->model("m_popup");
			$arrdata = $this->m_popup->notification($act, $id);
			$data = $this->load->view('content/newtable', $arrdata, true);
			if($this->input->post("ajax")||$act=="post"){
				echo $arrdata;
			}else{
				$this->content = $data;
				$this->index();
			}
		}
	}
}