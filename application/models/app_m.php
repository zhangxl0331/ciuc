<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App_m extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('xml');
	}
	
	function get_apps($col = '*', $where = '') {
		if($where)
		{
			$arr = $this->db->select($col)->where($where)->get('applications')->result_array();
		}
		else
		{
			$arr = $this->db->select($col)->get('applications')->result_array();
		}
		$return = array();
		foreach($arr as $k => $v) {
			isset($v['extra']) && !empty($v['extra']) && $v['extra'] = $this->xml->unserialize($v['extra']);
			if($tmp = authcode($v['authkey'], 'DECODE', UC_MYKEY)) {
				$v['authkey'] = $tmp;
			}
			$return[$v['appid']] = $v;
		}
		return $return;
	}

	function get_app_by_appid($appid, $includecert = FALSE) {
		$appid = intval($appid);
		$arr = $this->db->where('appid', $appid)->get('applications')->first_row('array');
		$arr['extra'] = $this->xml->unserialize($arr['extra']);
		if($tmp = authcode($arr['authkey'], 'DECODE', UC_MYKEY)) {
			$arr['authkey'] = $tmp;
		}
		if($includecert) {
			$this->load->model('plugin_m');
			$certfile = $this->plugin_m->cert_get_file();
			$appdata = $this->plugin_m->cert_dump_decode($certfile);
			if(is_array($appdata[$appid])) {
				$arr += $appdata[$appid];
			}
		}
		return $arr;
	}

	function delete_apps($appids) {
		return $this->db->where_in('appid', $appids)->delete('applications');
	}

/*	function update_app($appid, $name, $url, $authkey, $charset, $dbcharset) {
		if($name && $appid) {
			$this->db->query("UPDATE ".UC_DBTABLEPRE."applications SET name='$name', url='$url', authkey='$authkey', ip='$ip', synlogin='$synlogin', charset='$charset', dbcharset='$dbcharset' WHERE appid='$appid'");
			return $this->db->affected_rows();
		}
		return 0;
	}*/

	//private
	function alter_app_table($appid, $operation = 'ADD') {
		if($operation == 'ADD') {
			$this->db->query("ALTER TABLE ".$this->db->dbprefix."notelist ADD COLUMN app$appid tinyint NOT NULL", 'SILENT');
		} else {
			$this->db->query("ALTER TABLE ".$this->db->dbprefix."notelist DROP COLUMN app$appid", 'SILENT');
		}
	}

	function test_api($url, $ip = '') {
		$this->load->model('misc_m');
		if(!$ip) {
			$ip = $this->misc_m->get_host_by_url($url);
		}

		if($ip < 0) {
			return FALSE;
		}
		return $this->misc_m->dfopen($url, 0, '', '', 1, $ip);
	}

}