<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Domain extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('domain_m');
		$this->load->model('misc_m');
		$this->load->library('xml');
		$this->check_priv();
	}
	
	function ls() {
		return $this->domain_m->get_list(1, 9999, 9999);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */