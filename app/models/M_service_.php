<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class M_service extends CI_Model {
	
	public function __construct(){
		parent::__construct();
	}
	
	function set_vessel($mode, $arrxml){
		print_r($arrxml); die();
	}
}