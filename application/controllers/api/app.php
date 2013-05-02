<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('app_m');
		$this->load->model('misc_m');
		$this->load->model('cache_m');
		$this->load->library('xml');
	}
	
	function ls() {
		$this->init_input();
		$applist = $this->app_m->get_apps('appid, type, name, url, tagtemplates, viewprourl, synlogin');
		$applist2 = array();
		foreach($applist as $key => $app) {
			$app['tagtemplates'] = $this->xml->unserialize($app['tagtemplates']);
			$applist2[$app['appid']] = $app;
		}
		return $applist2;
	}

	function add() {
		$ucfounderpw = $this->input->post('ucfounderpw');
		$apptype = $this->input->post('apptype');
		$apptype = $this->input->post('apptype');
		$appname = $this->input->post('appname');
		$appurl = $this->input->post('appurl');
		$appip = $this->input->post('appip');
		$viewprourl = $this->input->post('viewprourl');
		$appcharset = $this->input->post('appcharset');
		$appdbcharset = $this->input->post('appdbcharset');
		$apptagtemplates = $this->input->post('apptagtemplates');

		if(md5(md5($ucfounderpw).UC_FOUNDERSALT) == UC_FOUNDERPW || (strlen($ucfounderpw) == 32 && $ucfounderpw == md5(UC_FOUNDERPW))) {
			@ob_start();
			$return  = '';

			$app = $this->db->where(array('url'=>$appurl, 'type'=>$apptype))->get('applications')->first_row('array');

			if(empty($app)) {
				$authkey = $this->_generate_key();
				$apptagtemplates = $this->xml->serialize($apptagtemplates, 1);
				$this->db->insert('applications', array('name'=>$appname, 'url'=>$appurl, 'ip'=>$appip, 'authkey'=>$authkey, 'viewprourl'=>$viewprourl, 'synlogin'=>1, 'charset'=>$appcharset, 'dbcharset'=>$appdbcharset, 'type'=>$apptype, 'recvnote'=>1, 'tagtemplates'=>$apptagtemplates));
				$appid = $this->db->insert_id();

				$this->app_m->alter_app_table($appid, 'ADD');
				//$return = "UC_STATUS_OK|$authkey|$appid|".UC_DBHOST.'|'.UC_DBNAME.'|'.UC_DBUSER.'|'.UC_DBPW.'|'.UC_DBCHARSET.'|'.UC_DBTABLEPRE.'|'.UC_CHARSET;
				$return = "$authkey|$appid|".UC_DBHOST.'|'.UC_DBNAME.'|'.UC_DBUSER.'|'.UC_DBPW.'|'.UC_DBCHARSET.'|'.UC_DBTABLEPRE.'|'.UC_CHARSET;
				$this->load->model('cache_m');
				$this->cache_m->updatedata('apps');

				$this->load->model('note_m');
				$notedata = $this->db->select('appid, type, name, url, ip, charset, synlogin, extra')->get('applications')->result_array();
				$notedata = $this->_format_notedata($notedata);
				$notedata['UC_API'] = UC_API;
				$this->note_m->add('updateapps', '', $this->xml->serialize($notedata, 1));
				$this->note_m->send();
			} else {
				//$return = "UC_STATUS_OK|$app[authkey]|$app[appid]|".UC_DBHOST.'|'.UC_DBNAME.'|'.UC_DBUSER.'|'.UC_DBPW.'|'.UC_DBCHARSET.'|'.UC_DBTABLEPRE.'|'.UC_CHARSET;
				$return = "$app[authkey]|$app[appid]|".UC_DBHOST.'|'.UC_DBNAME.'|'.UC_DBUSER.'|'.UC_DBPW.'|'.UC_DBCHARSET.'|'.UC_DBTABLEPRE.'|'.UC_CHARSET;
			}
			@ob_end_clean();
			exit($return);
		} else {
			exit('-1');
		}
	}

	function ucinfo() {
		$arrapptypes = $this->db->distinct('type')->get('applications')->result_array();
		$apptypes = $tab = '';
		foreach($arrapptypes as $apptype) {
			$apptypes .= $tab.$apptype['type'];
			$tab = "\t";
		}
		exit("UC_STATUS_OK|".UC_SERVER_VERSION."|".UC_SERVER_RELEASE."|".UC_CHARSET."|".UC_DBCHARSET."|".$apptypes);
	}

	function _random($length, $numeric = 0) {
		PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
		if($numeric) {
			$hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
		} else {
			$hash = '';
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
			$max = strlen($chars) - 1;
			for($i = 0; $i < $length; $i++) {
				$hash .= $chars[mt_rand(0, $max)];
			}
		}
		return $hash;
	}

	function _generate_key() {
		$random = $this->_random(32);
		$info = md5($_SERVER['SERVER_SOFTWARE'].$_SERVER['SERVER_NAME'].$_SERVER['SERVER_ADDR'].$_SERVER['SERVER_PORT'].$_SERVER['HTTP_USER_AGENT'].time());
		$return = array();
		for($i=0; $i<32; $i++) {
			$return[$i] = $random[$i].$info[$i];
		}
		return implode('', $return);
	}

	function _format_notedata($notedata) {
		$arr = array();
		foreach($notedata as $key => $note) {
			$arr[$note['appid']] = $note;
		}
		return $arr;
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */