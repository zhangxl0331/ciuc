<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Version_m extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('xml');
	}
	
	function check() {
		$data = $this->db->select('v')->get('settings')->where('k', 'version')->first_row('array');
		return $data;
	}

}