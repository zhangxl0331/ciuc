<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings_m extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_settings() 
	{
		if($result = $this->db->get('settings')->result_array())
		{
			foreach($result as $v)
			{
				$return[$v['k']] = $v['v'];
			}
			
			return $return;
		}
		
		return FALSE;
	}

}