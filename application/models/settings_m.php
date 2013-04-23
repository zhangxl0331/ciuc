<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings_m extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	function get_settings($keys = '') {
		if($keys) {
			$keys = $this->base->implode($keys);
			$sqladd = "k IN ($keys)";
		} else {
			$sqladd = '1';
		}
		$arr = array();
		$arr = $this->db->fetch_all("SELECT * FROM ".UC_DBTABLEPRE."settings WHERE $sqladd");
		if($arr) {
			foreach($arr as $k => $v) {
				$arr[$v['k']] = $v['v'];
				unset($arr[$k]);
			}
		}
		return $arr;
	}

}