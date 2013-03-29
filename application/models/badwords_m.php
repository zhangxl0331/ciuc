<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Badwords_m extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_badwords() 
	{
		if($result = $this->db->get('badwords')->result_array())
		{
			foreach($result as $k => $v)
			{
				$return['findpattern'][$k] = $v['findpattern'];
				$return['replace'][$k] = $v['replacement'];
			}
			
			return $return;
		}
		
		return FALSE;
	}

}