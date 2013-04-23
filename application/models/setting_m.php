<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setting_m extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	function get_setting($k = array(), $decode = FALSE) {
		$return = array();
		if($k)
		{
			$settings = $this->db->where_in('k', $k)->get('settings')->result_array();
		}
		else 
		{
			$settings = $this->db->get('settings')->result_array();
		}
		if(is_array($settings)) {
			foreach($settings as $arr) {
				$return[$arr['k']] = $decode ? unserialize($arr['v']) : $arr['v'];
			}
		}
		return $return;
	}
	
	function set_setting($k, $v, $encode = FALSE) {
		$v = is_array($v) || $encode ? addslashes(serialize($v)) : $v;
		$this->db->replace('settings', array('k'=>$k, 'v'=>$v));
	}

}