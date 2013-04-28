<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Code here is run before ALL controllers
 * 
 * @package PyroCMS\Core\Controllers 
 */
class MY_Controller extends CI_Controller
{
	var $time;
	var $onlineip;
	var $user = array();
	var $settings = array();
	var $caches = array();
	var $app = array();
	var $input = array();
	
	var $cookie_status = 1;
	
	var $sid;

	function __construct() 
	{
		parent::__construct();
		
		$this->init_var();
// 		$this->init_db();
		$this->init_cache();
		$this->init_app();
		$this->init_user();
		$this->init_template();
		$this->init_note();
		$this->init_mail();
		//		$this->cron();
		
		$this->load->helper(array('global'));
		
		$this->sid = $this->cookie_status ? $this->input->cookie(config_item('cookie_prefix').'sid') : rawurlencode($this->input->get_post('sid'));
		$this->load->vars('sid', $this->sid);
		$this->load->vars('iframe', @$_REQUEST['iframe']);
		$this->load->vars('m', $this->router->fetch_class());
		$this->load->vars('a', $this->router->fetch_method());
	}
	
	function init_var() {
		$this->time = time();
		$this->onlineip = $this->input->ip_address();
		$_GET['page'] =  max(1, intval($this->input->get_post('page')));
		
		$this->load->language('main');
	}
	
	function init_cache() {
		$this->settings = $this->cache('settings');
		$this->caches['apps'] = $this->cache('apps');
		if(PHP_VERSION > '5.1') {
			$timeoffset = intval($this->settings['timeoffset'] / 3600);
			@date_default_timezone_set('Etc/GMT'.($timeoffset > 0 ? '-' : '+').(abs($timeoffset)));
		}
	}
	
	function init_input($getagent = '') {
		$input = @$_REQUEST['input'];
		if($input) {
			$input = authcode($input, 'DECODE', $this->app['authkey']);
			parse_str($input, $this->input);
			$this->input = daddslashes($this->input, 1, TRUE);
			$agent = $getagent ? $getagent : $this->input['agent'];
	
			if(($getagent && $getagent != $this->input['agent']) || (!$getagent && md5($_SERVER['HTTP_USER_AGENT']) != $agent)) {
				exit('Access denied for agent changed');
			} elseif($this->time - $this->input('time') > 3600) {
				exit('Authorization has expired');
			}
		}
		if(empty($this->input)) {
			exit('Invalid input');
		}
	}
	
	function init_app() {
		$appid = isset($_REQUEST['appid'])?intval($_REQUEST['appid']):NULL;
		$appid && $this->app = $this->caches['apps'][$appid];
	}
	
	function init_user() {
		if($auth = $this->input->cookie('auth')) {
			@list($uid, $username, $agent) = explode('|', $this->authcode($auth, 'DECODE', ($this->input ? $this->app['appauthkey'] : UC_KEY)));
			if($agent != md5($_SERVER['HTTP_USER_AGENT'])) {
				$this->input->set_cookie('auth', '');
			} else {
				@$this->user['uid'] = $uid;
				@$this->user['username'] = $username;
			}
		}
	}
	
	function init_template() {

		$this->load->language('templates');

		$this->load->vars('dbhistories', $this->db->queries);
		$this->load->vars('charset', UC_CHARSET);
		$this->load->vars('dbquerynum', $this->db->query_count);
		$this->load->vars('user', $this->user);
	}
	
	function init_note() {
		if($this->note_exists() && !@$_REQUEST['inajax']) {
			$this->load->model('note_m');
			$this->note_m->send();
		}
	}
	
	function init_mail() {
		if($this->mail_exists() && !@$_REQUEST['inajax']) {
			$this->load->model('mail_m');
			$this->mail_m->send();
		}
	}
	
	function message($message, $redirect = '', $type = 0, $vars = array()) {
		$this->load->language('messages');
		if($this->lang->line($message)) {
			$message = $this->lang->line($message) ? str_replace(array_keys($vars), array_values($vars), $this->lang->line($message)) : $message;
		}
		$this->load->vars('message', $message);
		$this->load->vars('redirect', $redirect);
		if($type == 0) {
			$this->load->view('message');
		} elseif($type == 1) {
			$this->load->view('message_client');
		}
		exit;
	}
	
	
	
	
	
	function date($time, $type = 3) {
		$format[] = $type & 2 ? (!empty($this->settings['dateformat']) ? $this->settings['dateformat'] : 'Y-n-j') : '';
		$format[] = $type & 1 ? (!empty($this->settings['timeformat']) ? $this->settings['timeformat'] : 'H:i') : '';
		return gmdate(implode(' ', $format), $time + $this->settings['timeoffset']);
	}
	
	
	
	function &cache($cachefile) {
		$this->load->model('cache_m');
		static $_CACHE = array();
		if(!isset($_CACHE[$cachefile])) {
			$cachepath = config_item('cache_path').$cachefile;
			if(!file_exists($cachepath)) {
				
				$this->cache_m->updatedata($cachefile);
			} 
		}
		$_CACHE[$cachefile] = $this->cache_m->getdata($cachefile);
		return $_CACHE[$cachefile];
	}
	
	function input($k) {
		return isset($this->input[$k]) ? (is_array($this->input[$k]) ? $this->input[$k] : trim($this->input[$k])) : NULL;
	}
	
	function note_exists() {
		$noteexists = $this->db->where('name', 'noteexists')->get('vars')->first_row();
		if(empty($noteexists)) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	function mail_exists() {
		$mailexists = $this->db->where('name', 'mailexists')->get('vars')->first_row();
		if(empty($mailexists)) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	
	
	function check_priv() {
		$username = $this->sid_decode($this->sid);
		if(empty($username)) {
			header('Location: '.$this->config->base_url().'user/login?iframe='.$this->input->get('iframe').($this->cookie_status ? '' : '&sid='.$this->sid));
			exit;
		} else {
			$this->user['isfounder'] = $username == 'UCenterAdministrator' ? 1 : 0;
			if(!$this->user['isfounder']) {
				$admin = $this->db->select('a.*, m.*')->from('admins a')->join('members m', 'a.uid=m.uid', 'LEFT')->where('a.username', $username)->get()->row_array();
				
				if(empty($admin)) {
					header('Location: '.$this->config->base_url().'user/login?iframe='.$this->input->get('iframe').($this->cookie_status ? '' : '&sid='.$this->sid));
					exit;
				} else {
					$this->user += $admin;
					$this->user['username'] = $username;
					$this->user['admin'] = 1;
					$this->sid = $this->sid_encode($username);
					$this->load->vars('sid', $this->sid);
					$this->input->set_cookie('sid', $this->sid, 86400);
				}
			} else {
				$this->user['username'] = 'UCenterAdministrator';
				$this->user['admin'] = 1;
				$this->user['uid'] = 1;
			}
			$this->load->vars('user', $this->user);
		}
	}
	
	function is_founder($username) {
		return $this->user['isfounder'];
	}
	
	function writelog($action, $extra = '') {
		$log = htmlspecialchars($this->user['username']."\t".$this->onlineip."\t".$this->time."\t$action\t$extra");
		$logfile = FCPATH.'data/logs/'.gmdate('Ym', $this->time).'.php';
		if(@filesize($logfile) > 2048000) {
			PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
			$hash = '';
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
			for($i = 0; $i < 4; $i++) {
				$hash .= $chars[mt_rand(0, 61)];
			}
			@rename($logfile, FCPATH.'data/logs/'.gmdate('Ym', $this->time).'_'.$hash.'.php');
		}
		if($fp = @fopen($logfile, 'a')) {
			@flock($fp, 2);
			@fwrite($fp, "<?PHP exit;?>\t".str_replace(array('<?', '?>'), '', $log)."\n");
			@fclose($fp);
		}
	}
	
	function fetch_plugins() {
		$plugindir = FCPATH.'plugin';
		$d = opendir($plugindir);
		while($f = readdir($d)) {
			if($f != '.' && $f != '..' && is_dir($plugindir.'/'.$f)) {
				$pluginxml = $plugindir.$f.'/plugin.xml';
				$plugins[] = xml_unserialize($pluginxml);
			}
		}
	}
	
	function sid_decode($sid) {
		$ip = $this->input->ip_address();
		$agent = $this->input->user_agent();
		$authkey = md5($ip.$agent.UC_KEY);
		$s = authcode(rawurldecode($sid), 'DECODE', $authkey, 1800);
		if(empty($s)) {
			return FALSE;
		}
		@list($username, $check) = explode("\t", $s);
		if($check == substr(md5($ip.$agent), 0, 8)) {
			return $username;
		} else {
			return FALSE;
		}
	}
	
	function sid_encode($username) {
		$ip = $this->input->ip_address();
		$agent = $this->input->user_agent();
		$authkey = md5($ip.$agent.UC_KEY);
		$check = substr(md5($ip.$agent), 0, 8);
		return rawurlencode(authcode("$username\t$check", 'ENCODE', $authkey, 1800));
	}
}

/* End of file install.php */
/* Location: application/controllers/install.php */