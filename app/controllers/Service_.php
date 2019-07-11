<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Service extends CI_Controller {
	public function __construct() {
        parent::__construct();
    }
	
	function kiosk(){
		$this->load->library('nusoap');
		include_once APPPATH.'models/m_server.php';
		$server = $this->nusoap;
		$server->configureWSDL('Server', 'urn:Server');		
		$server->register('service',
			array('username'    => 'xsd:string',
				  'password'    => 'xsd:string',
				  'type'    	=> 'xsd:string',
				  'mode'    	=> 'xsd:integer',
				  'data'    	=> 'xsd:string'),
			array('return' => 'xsd:string'),
				  'urn:service',
				  'urn:service#service',
				  'rpc',
				  'encoded',
				  'Service Production Kiosk'
		);
		if(ob_get_length() > 0){
			ob_end_clean();
		}
		$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : file_get_contents('php://input');
		$server->service($HTTP_RAW_POST_DATA, $this);
	}
}