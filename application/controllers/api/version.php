<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Version extends MY_Controller {

	var $settings = array();
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('version_m');
	}
	
	function check() {
		$db_version = $this->version_m->check();
		$return = array('file' => UC_SERVER_VERSION, 'db' => $db_version);
		return $return;
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */