<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
define('UC_SERVER_VERSION', '1.5.0');
define('UC_SERVER_RELEASE', '20090121');
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
	var $badwords = array();
	var $applications = array();
	var $input = array();
	
	var $cookie_status = 1;

	function __construct() 
	{
		parent::__construct();
		$this->load->helper(array('install', 'global'));
		//init var
		$this->time = time();
		$this->onlineip = $this->input->ip_address();
		$_GET['page'] =  max(1, intval($this->input->get_post('page')));
		
		$this->load->language(array('main', 'templates'));
		
		//init cache
		$this->load->driver('cache');
		
		$cache_keys = array('settings', 'badwords', 'applications');
		foreach($cache_keys as $v)
		{
			if( ! $$v = $this->cache->get($v))
			{
				$model = "{$v}_m";
				$method = "get_{$v}";
				$this->load->model($model);
				if($$v = $this->$model->$method())
				{
					$this->cache->save($v, $$v);
				}
			}
				
			$this->$v = $$v;
		}
		
		//init app
		$appid = intval(getgpc('appid'));
		
		//init user
		if(isset($_COOKIE['uc_auth']))
		{
			@list($uid, $username, $agent) = explode('|', authcode($_COOKIE['uc_auth'], 'DECODE', ($this->input ? $this->app['appauthkey'] : UC_KEY)));
			if($agent != md5($_SERVER['HTTP_USER_AGENT']))
			{
				$this->setcookie('uc_auth', '');
			}
			else
			{
				@$this->user['uid'] = $uid;
				@$this->user['username'] = $username;
			}
		}
		
		//init note
// 		if($this->var_exists('noteexists') && !getgpc('inajax')) {
// 			$this->load->library('notelist_l');
// 			$this->notelist_l->send();
// 		}
		
		//init mail
// 		if($this->var_exists('mailexists') && !getgpc('inajax')) {
// 			$this->load->library('mailqueue_l');
// 			$this->mailqueue_l->send();
// 		}
		
		//		$this->cron();
		
		$sid = $this->cookie_status ? getgpc('sid', 'C') : rawurlencode(getgpc('sid', 'R'));
// 		if($this->router->fetch_class() !='user' && $this->router->fetch_method() != 'login' && $this->router->fetch_method() != 'logout') {
// 			$this->check_priv();
// 		}
	}
	
	function check_priv() {
		$sid = $this->cookie_status ? getgpc('sid', 'C') : rawurlencode(getgpc('sid', 'R'));
		$username = $this->sid_decode($sid);
		if(empty($username)) {
			header('Location: '.$this->config->base_url().'admin/user/login?iframe='.getgpc('iframe', 'G').($this->cookie_status ? '' : '&sid='.$sid));
			exit;
		} else {
			$this->user['isfounder'] = $username == 'UCenterAdministrator' ? 1 : 0;
			if(!$this->user['isfounder']) {
				$admin = $this->db->fetch_first("SELECT a.*, m.* FROM ".UC_DBTABLEPRE."admins a LEFT JOIN ".UC_DBTABLEPRE."members m USING(uid) WHERE a.username='$username'");
				if(empty($admin)) {
					header('Location: '.$this->config->base_url().'admin/user/login?iframe='.getgpc('iframe', 'G').($this->cookie_status ? '' : '&sid='.$sid));
					exit;
				} else {
					$this->user = $admin;
					$this->user['username'] = $username;
					$this->user['admin'] = 1;
					$this->view->sid = $this->sid_encode($username);
					$this->setcookie('sid', $this->view->sid, 86400);
				}
			} else {
				$this->user['username'] = 'UCenterAdministrator';
				$this->user['admin'] = 1;
			}
			$this->view->assign('user', $this->user);
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
		$agent = $_SERVER['HTTP_USER_AGENT'];
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
		$agent = $_SERVER['HTTP_USER_AGENT'];
		$authkey = md5($ip.$agent.UC_KEY);
		$check = substr(md5($ip.$agent), 0, 8);
		return rawurlencode(authcode("$username\t$check", 'ENCODE', $authkey, 1800));
	}

	function message($message, $redirect = '', $type = 0, $vars = array()) {
		$this->load->language('message');
		if(isset($lang[$message])) {
			$message = $lang[$message] ? str_replace(array_keys($vars), array_values($vars), $lang[$message]) : $message;
		}
		$this->view->assign('message', $message);
		$this->view->assign('redirect', $redirect);
		if($type == 0) {
			$this->view->display('message');
		} elseif($type == 1) {
			$this->view->display('message_client');
		}
		exit;
	}

	function date($time, $type = 3) {
		$format[] = $type & 2 ? (!empty($this->settings['dateformat']) ? $this->settings['dateformat'] : 'Y-n-j') : '';
		$format[] = $type & 1 ? (!empty($this->settings['timeformat']) ? $this->settings['timeformat'] : 'H:i') : '';
		return gmdate(implode(' ', $format), $time + $this->settings['timeoffset']);
	}

	function implode($arr) {
		return "'".implode("','", (array)$arr)."'";
	}

	function set_home($uid, $dir = '.') {
		$uid = sprintf("%09d", $uid);
		$dir1 = substr($uid, 0, 3);
		$dir2 = substr($uid, 3, 2);
		$dir3 = substr($uid, 5, 2);
		!is_dir($dir.'/'.$dir1) && mkdir($dir.'/'.$dir1, 0777);
		!is_dir($dir.'/'.$dir1.'/'.$dir2) && mkdir($dir.'/'.$dir1.'/'.$dir2, 0777);
		!is_dir($dir.'/'.$dir1.'/'.$dir2.'/'.$dir3) && mkdir($dir.'/'.$dir1.'/'.$dir2.'/'.$dir3, 0777);
	}

	function get_home($uid) {
		$uid = sprintf("%09d", $uid);
		$dir1 = substr($uid, 0, 3);
		$dir2 = substr($uid, 3, 2);
		$dir3 = substr($uid, 5, 2);
		return $dir1.'/'.$dir2.'/'.$dir3;
	}

	function get_avatar($uid, $size = 'big', $type = '') {
		$size = in_array($size, array('big', 'middle', 'small')) ? $size : 'big';
		$uid = abs(intval($uid));
		$uid = sprintf("%09d", $uid);
		$dir1 = substr($uid, 0, 3);
		$dir2 = substr($uid, 3, 2);
		$dir3 = substr($uid, 5, 2);
		$typeadd = $type == 'real' ? '_real' : '';
		return  $dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).$typeadd."_avatar_$size.jpg";
	}

	function input($k) {
		return isset($this->input[$k]) ? (is_array($this->input[$k]) ? $this->input[$k] : trim($this->input[$k])) : NULL;
	}

	function serialize($s, $htmlon = 0) {
		if(file_exists(FCPATH.RELEASE_ROOT.'./lib/xml.class.php')) {
			include_once FCPATH.RELEASE_ROOT.'./lib/xml.class.php';
		} else {
			include_once FCPATH.'./lib/xml.class.php';
		}

		return xml_serialize($s, $htmlon);
	}

	function unserialize($s) {
		if(file_exists(FCPATH.RELEASE_ROOT.'./lib/xml.class.php')) {
			include_once FCPATH.RELEASE_ROOT.'./lib/xml.class.php';
		} else {
			include_once FCPATH.'./lib/xml.class.php';
		}

		return xml_unserialize($s);
	}

	function cutstr($string, $length, $dot = ' ...') {
		if(strlen($string) <= $length) {
			return $string;
		}

		$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);

		$strcut = '';
		if(strtolower(UC_CHARSET) == 'utf-8') {

			$n = $tn = $noc = 0;
			while($n < strlen($string)) {

				$t = ord($string[$n]);
				if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
					$tn = 1; $n++; $noc++;
				} elseif(194 <= $t && $t <= 223) {
					$tn = 2; $n += 2; $noc += 2;
				} elseif(224 <= $t && $t < 239) {
					$tn = 3; $n += 3; $noc += 2;
				} elseif(240 <= $t && $t <= 247) {
					$tn = 4; $n += 4; $noc += 2;
				} elseif(248 <= $t && $t <= 251) {
					$tn = 5; $n += 5; $noc += 2;
				} elseif($t == 252 || $t == 253) {
					$tn = 6; $n += 6; $noc += 2;
				} else {
					$n++;
				}

				if($noc >= $length) {
					break;
				}

			}
			if($noc > $length) {
				$n -= $tn;
			}

			$strcut = substr($string, 0, $n);

		} else {
			for($i = 0; $i < $length; $i++) {
				$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
			}
		}

		$strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

		return $strcut.$dot;
	}

	function setcookie($key, $value, $life = 0, $httponly = false) {
		(!defined('UC_COOKIEPATH')) && define('UC_COOKIEPATH', '/');
		(!defined('UC_COOKIEDOMAIN')) && define('UC_COOKIEDOMAIN', '');

		if($value == '' || $life < 0) {
			$value = '';
			$life = -1;
		}
		
		$life = $life > 0 ? $this->time + $life : ($life < 0 ? $this->time - 31536000 : 0);
		$path = $httponly && PHP_VERSION < '5.2.0' ? UC_COOKIEPATH."; HttpOnly" : UC_COOKIEPATH;
		$secure = $_SERVER['SERVER_PORT'] == 443 ? 1 : 0;
		if(PHP_VERSION < '5.2.0') {
			setcookie($key, $value, $life, $path, UC_COOKIEDOMAIN, $secure);
		} else {
			setcookie($key, $value, $life, $path, UC_COOKIEDOMAIN, $secure, $httponly);
		}
	}

	function var_exists($var) 
	{
		$this->load->model('vars_m');
		return $this->vars_m->var_exists($var);
	}

	function dstripslashes($string) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = $this->dstripslashes($val);
			}
		} else {
			$string = stripslashes($string);
		}
		return $string;
	}
}

/* End of file install.php */
/* Location: application/controllers/install.php */