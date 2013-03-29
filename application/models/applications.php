<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Applications_m extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_badwords() 
	{
		if($result = $this->db->get('applications')->result_array())
		{
			foreach($result as $k => $v)
			{
				$return[$v['appid']] = $v;
			}
			
			return $return;
		}
		
		return FALSE;
	}

}