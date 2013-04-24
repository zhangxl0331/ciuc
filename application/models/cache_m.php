<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cache_m extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->driver('cache');
		$this->map = array(
			'settings' => array('settings'),
			'badwords' => array('badwords'),
			'plugins' => array('plugins'),
			'apps' => array('apps'),
		);
		
	}
	
	//public
	function updatedata($cachefile = '') {
		$cache = array();
		if($cachefile) {
			foreach((array)$this->map[$cachefile] as $modules) {
				foreach((array)$modules as $m) {
					$method = "_get_$m";
					$cache[$m] = $this->$method();
				}
				$this->cache->save($cachefile, $cache);
			}
		} else {
			foreach((array)$this->map as $file => $modules) {
				foreach($modules as $m) {
					$method = "_get_$m";
					$cache[$m] = $this->$method();
				}
				$this->cache->save($file, $cache);
			}
		}
	}
	
	function getdata($cachefile) {
		if($cache = $this->cache->get($cachefile))
		{
			$this->updatedata($cachefile);
			$cache = $this->cache->get($cachefile);
		}
		return count($cache)==1?current($cache):$cache;
	}

	function updatetpl() {
		$tpl = dir(UC_DATADIR.'view');
		while($entry = $tpl->read()) {
			if(preg_match("/\.php$/", $entry)) {
				@unlink(UC_DATADIR.'view/'.$entry);
			}
		}
		$tpl->close();
	}

	//private
	function _get_badwords() {
		$data = $this->db->get('badwords')->result_array();
		$return = array();
		if(is_array($data)) {
			foreach($data as $k => $v) {
				$return['findpattern'][$k] = $v['findpattern'];
				$return['replace'][$k] = $v['replacement'];
			}
		}
		return $return;
	}

	//private
	function _get_apps() {
		$this->load->model('app_m');
		$apps = $this->app_m->get_apps();
		$apps2 = array();
		if(is_array($apps)) {
			foreach($apps as $v) {
				$apps2[$v['appid']] = $v;
			}
		}
		return $apps2;
	}

	//private
	function _get_settings() {
		$this->load->model('setting_m');
		return $this->setting_m->get_setting();
	}

	//private
	function _get_plugins() {
		$this->load->model('plugin_m');
		return $this->plugin_m->get_plugins();
	}

}