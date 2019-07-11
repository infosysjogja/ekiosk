<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Service extends CI_Controller {
	public function __construct() {
        parent::__construct();
		$this->load->library("Nusoap");
        $this->nusoap_server = $this->nusoap;
        $this->nusoap_server->configureWSDL('Server', 'urn:Server');
        $this->nusoap_server->register('service',
			array('username'    => 'xsd:string',
				  'password'    => 'xsd:string',
				  'type'    	=> 'xsd:string',
				  'mode'    	=> 'xsd:integer',
				  'data'    	=> 'xsd:string'),
			array('return' => 'xsd:string'),
				  'urn:Server',
				  'urn:Server#service',
				  'rpc',
				  'encoded',
				  'Service Production Kiosk'
		);
    }
	
	function index(){
		foreach($_GET as $a => $b){
			if($a == "wsdl"){
				$_SERVER['QUERY_STRING'] = "wsdl";
			}else{
				 $_SERVER['QUERY_STRING'] = "";
			}
		}
		
        function service($username, $password, $type, $mode, $data){
			global $objci;
			$objci->load->model('m_service');
			return $objci->m_service->service($username, $password, $type, $mode, $data);
        }
		
		if(ob_get_length() > 0){
			ob_end_clean();
		}
		$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : file_get_contents('php://input');
		$this->nusoap_server->service($HTTP_RAW_POST_DATA, $this);
    }
}