<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('UC_INSTALL_LANG', 'SC_UTF8');
define('CONSTANTS', APPPATH.'config/constants.php');
define('DATABASE', APPPATH.'config/database.php');
define('UC_INSTALL_SQL', FCPATH.'data/uc.sql');
define('UC_INSTALL_LOCK', FCPATH.'data/install.lock');
define('LOCK_UPGRADE', FCPATH.'data/upgrade.lock');

define('METHOD_UNDEFINED', 255);
define('ENV_CHECK_RIGHT', 0);
define('ERROR_CONFIG_VARS', 1);
define('SHORT_OPEN_TAG_INVALID', 2);
define('INSTALL_LOCKED', 3);
define('DATABASE_NONEXISTENCE', 4);
define('PHP_VERSION_TOO_LOW', 5);
define('MYSQL_VERSION_TOO_LOW', 6);
define('UC_URL_INVALID', 7);
define('UC_DNS_ERROR', 8);
define('UC_URL_UNREACHABLE', 9);
define('UC_VERSION_INCORRECT', 10);
define('UC_DBCHARSET_INCORRECT', 11);
define('UC_API_ADD_APP_ERROR', 12);
define('UC_ADMIN_INVALID', 13);
define('UC_DATA_INVALID', 14);
define('DBNAME_INVALID', 15);
define('DATABASE_ERRNO_2003', 16);
define('DATABASE_ERRNO_1044', 17);
define('DATABASE_ERRNO_1045', 18);
define('DATABASE_CONNECT_ERROR', 19);
define('TABLEPRE_INVALID', 20);
define('CONFIG_UNWRITEABLE', 21);
define('ADMIN_USERNAME_INVALID', 22);
define('ADMIN_EMAIL_INVALID', 25);
define('ADMIN_EXIST_PASSWORD_ERROR', 26);
define('ADMININFO_INVALID', 27);
define('LOCKFILE_NO_EXISTS', 28);
define('TABLEPRE_EXISTS', 29);
define('ERROR_UNKNOW_TYPE', 30);
define('ENV_CHECK_ERROR', 31);
define('UNDEFINE_FUNC', 32);
define('MISSING_PARAMETER', 33);
define('LOCK_FILE_NOT_TOUCH', 34);

$func_items = array('mysql_connect', 'fsockopen', 'gethostbyname', 'file_get_contents', 'xml_parser_create');

$env_items = array
(
		'os' => array('c' => 'PHP_OS', 'r' => 'notset', 'b' => 'unix'),
		'php' => array('c' => 'PHP_VERSION', 'r' => '4.0', 'b' => '5.0'),
		'attachmentupload' => array('r' => 'notset', 'b' => '2M'),
		'gdversion' => array('r' => '1.0', 'b' => '2.0'),
		'diskspace' => array('r' => '10M', 'b' => 'notset'),
);

$dirfile_items = array
(
		'constants' => array('type' => 'file', 'path' => FCPATH.APPPATH.'config/constants.php'),
		'database' => array('type' => 'file', 'path' => FCPATH.APPPATH.'config/database.php'),
		'data' => array('type' => 'dir', 'path' => FCPATH.'data'),
		'cache' => array('type' => 'dir', 'path' => FCPATH.'data/cache'),
		'view' => array('type' => 'dir', 'path' => FCPATH.'data/view'),
		'avatar' => array('type' => 'dir', 'path' => FCPATH.'data/avatar'),
		'logs' => array('type' => 'dir', 'path' => FCPATH.'data/logs'),
		'backup' => array('type' => 'dir', 'path' => FCPATH.'data/backup'),
		'tmp' => array('type' => 'dir', 'path' => FCPATH.'data/tmp')
);

$form_db_init_items = array
(
		'dbinfo' => array
		(
				'dbhost' => array('type' => 'text', 'required' => 1, 'reg' => '/^.*$/', 'value' => array('type' => 'string', 'var' => 'localhost')),
				'dbname' => array('type' => 'text', 'required' => 1, 'reg' => '/^.*$/', 'value' => array('type' => 'string', 'var' => 'ucenter')),
				'dbuser' => array('type' => 'text', 'required' => 0, 'reg' => '/^.*$/', 'value' => array('type' => 'string', 'var' => 'root')),
				'dbpw' => array('type' => 'password', 'required' => 0, 'reg' => '/^.*$/', 'value' => array('type' => 'string', 'var' => '')),
				'dbcharset' => array('type' => 'text', 'required' => 0, 'reg' => '/^.*$/', 'value' => array('type' => 'string', 'var' => 'utf8')),
				'tablepre' => array('type' => 'text', 'required' => 0, 'reg' => '/^.*$/', 'value' => array('type' => 'string', 'var' => 'uc_')),
		),
		'admininfo' => array
		(
				'ucfounderpw' => array('type' => 'password', 'required' => 1, 'reg' => '/^.*$/'),
				'ucfounderpw2' => array('type' => 'password', 'required' => 1, 'reg' => '/^.*$/'),
		)
);

$allow_method = array('show_license', 'env_check', 'db_init', 'ext_info', 'install_check', 'tablepre_check');

class Install extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->language('install');
	}
	
	public function index()
	{		
		global $func_items, $env_items, $dirfile_items, $form_db_init_items, $allow_method;
		
		$view_off = $this->input->get_post('view_off') ? TRUE : FALSE;
		
		$step = isset($_REQUEST['step']) ? intval($_REQUEST['step']) : 0;
		$method = $this->input->get_post('method');
		
		if(empty($method) || !in_array($method, $allow_method))
		{
			$method = isset($allow_method[$step]) ? $allow_method[$step] : '';
		}		
		
		if(empty($method)) 
		{
			$this->show_msg('method_undefined', $method, 0);
		}	
		
		if(file_exists(UC_INSTALL_LOCK))
		{
			$this->show_msg('install_locked', '', 0);
		}
					
		if($method == 'show_license') 
		{		
			$this->show_license();		
		} 
		elseif($method == 'env_check') 
		{			
			$view_off && $this->function_check($func_items);
		
			$this->env_check($env_items);
		
			$this->dirfile_check($dirfile_items);
		
			$this->show_env_result($env_items, $dirfile_items, $func_items);
		
		} 
		elseif($method == 'db_init') 
		{			
			@include CONFIG;
			$submit = true;
			$error_msg = array();
			if(isset($form_db_init_items) && is_array($form_db_init_items)) 
			{
				foreach($form_db_init_items as $key => $items) 
				{
					$$key = $this->input->post($key);
					if(!isset($$key) || !is_array($$key)) 
					{
						$submit = false;
						break;
					}
					foreach($items as $k => $v) 
					{
						$tmp = $$key;
						$$k = $tmp[$k];
						if(empty($$k) || !preg_match($v['reg'], $$k)) 
						{
							if(empty($$k) && !$v['required']) 
							{
								continue;
							}
							$submit = false;
							$view_off or $error_msg[$key][$k] = 1;
						}
					}
				}
			} 
			else 
			{
				$submit = false;
			}
		
			if(!$view_off && $this->input->server('REQUEST_METHOD') == 'POST') 
			{
				if($ucfounderpw != $ucfounderpw2) 
				{
					$error_msg['admininfo']['ucfounderpw2'] = 1;
					$submit = false;
				}
		
				$forceinstall = isset($_POST['dbinfo']['forceinstall']) ? $_POST['dbinfo']['forceinstall'] : '';
				$dbname_not_exists = true;
				if(!empty($dbhost) && empty($forceinstall)) 
				{
					$dbname_not_exists = $this->check_db($dbhost, $dbuser, $dbpw, $dbname, $tablepre);
					if(!$dbname_not_exists) 
					{
						$form_db_init_items['dbinfo']['forceinstall'] = array('type' => 'checkbox', 'required' => 0, 'reg' => '/^.*+/');
						$error_msg['dbinfo']['forceinstall'] = 1;
						$submit = false;
						$dbname_not_exists = false;
					}
				}
			}
		
			if($submit) 
			{
		
				$step = $step + 1;
				if(empty($dbname)) 
				{
					$this->show_msg('dbname_invalid', $dbname, 0);
				} 
				else 
				{
					if(!@mysql_connect($dbhost, $dbuser, $dbpw)) 
					{
						$errno = mysql_errno();
						$error = mysql_error();
						if($errno == 1045) 
						{
							$this->show_msg('database_errno_1045', $error, 0);
						} 
						elseif($errno == 2003) 
						{
							$this->show_msg('database_errno_2003', $error, 0);
						} 
						else 
						{
							$this->show_msg('database_connect_error', $error, 0);
						}
					}
					if(mysql_get_server_info() > '4.1') 
					{
						mysql_query("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET ".$dbcharset);
					} 
					else 
					{
						mysql_query("CREATE DATABASE IF NOT EXISTS `$dbname`");
					}
		
					if(mysql_errno()) 
					{
						$this->show_msg('database_errno_1044', mysql_error(), 0);
					}
					mysql_close();
				}
		
				if(strpos($tablepre, '.') !== false || intval($tablepre{0})) 
				{
					$this->show_msg('tablepre_invalid', $tablepre, 0);
				}
				
				$this->config_edit();
		
				$params['hostname'] = $dbhost;
				$params['username'] = $dbuser;
				$params['password'] = $dbpw;
				$params['database'] = $dbname;
				$params['dbdriver'] = 'mysql';
				$params['dbprefix'] = $tablepre;
				$params['pconnect'] = TRUE;
				$params['db_debug'] = TRUE;
				$params['cache_on'] = FALSE;
				$params['cachedir'] = '';
				$params['char_set'] = $dbcharset;
				$params['dbcollat'] = 'utf8_general_ci';
				$params['swap_pre'] = '';
				$params['autoinit'] = TRUE;
				$params['stricton'] = FALSE;
				$this->load->database($params);
		
				$sql = file_get_contents(UC_INSTALL_SQL);
				$sql = str_replace("\r\n", "\n", $sql);
		
				if(!$view_off) 
				{
					$this->show_header();
					$this->show_install();
				}
		
				$this->runquery($sql);
		
				$view_off && $this->show_msg('initdbresult_succ');
		
				if(!$view_off) 
				{
					echo '<script type="text/javascript">document.getElementById("laststep").disabled=false;document.getElementById("laststep").value = \''.$this->lang->line('install_succeed').'\';</script>'."\r\n";
					$this->show_footer();
				}
		
			}
			if($view_off) 
			{
				$this->show_msg('missing_parameter', '', 0);		
			} 
			else 
			{				
				$this->show_form($form_db_init_items, $error_msg);		
			}
		
		} 
		elseif($method == 'ext_info') 
		{		
			@touch(UC_INSTALL_LOCK);
			@touch(FCPATH.'data/install.lock');
			if($view_off) 
			{
				$this->show_msg('ext_info_succ');
			} 
			else 
			{		
				include CONFIG;
				$md5password =  UC_FOUNDERPW;
				setcookie('uc_founderauth', authcode("|$md5password|".md5($this->input->user_agent())."|1", 'ENCODE', UC_KEY), time() + 3600, '/');
				header('Location:'.$this->config->base_url('index/index?mainurl='.urlencode('app/add')));		
			}
		
		} 
		elseif($method == 'install_check') 
		{		
			if(file_exists(UC_INSTALL_LOCK)) 
			{
				@touch(LOCK_UPGRADE);
				$this->show_msg('installstate_succ');
			} 
			else 
			{
				$this->show_msg('lock_file_not_touch', UC_INSTALL_LOCK, 0);
			}
		
		} 
		elseif($method == 'tablepre_check') 
		{
		
			$dbinfo = $this->input->get_post('dbinfo');
			extract($dbinfo);
			if(check_db($dbhost, $dbuser, $dbpw, $dbname, $tablepre)) 
			{
				$this->show_msg('tablepre_not_exists', 0);
			} 
			else 
			{
				$this->show_msg('tablepre_exists', $tablepre, 0);
			}
		}

	}
	
	function show_msg($error_no, $error_msg = 'ok', $success = 1, $quit = TRUE) {
		$view_off = $this->input->get_post('view_off') ? TRUE : FALSE;
		$step = intval($this->input->get_post('step')) ? intval($this->input->get_post('step')) : 0;
		if($view_off) {
			$error_code = $success ? 0 : constant(strtoupper($error_no));
			$error_msg = empty($error_msg) ? $error_no : $error_msg;
			$error_msg = str_replace('"', '\"', $error_msg);
			$str = "<root>\n";
			$str .= "\t<error errorCode=\"$error_code\" errorMessage=\"$error_msg\" />\n";
			$str .= "</root>";
			echo $str;
			exit;
		} else {
			$this->show_header();
	
			$title = $this->lang->line($error_no);
			$comment = $this->lang->line($error_no.'_comment', false);
			$errormsg = '';
	
			if($error_msg) {
				if(!empty($error_msg)) {
					foreach ((array)$error_msg as $k => $v) {
						if(is_numeric($k)) {
							$comment .= "<li><em class=\"red\">".$this->lang->line($v)."</em></li>";
						}
					}
				}
			}
	
			if($step > 0) {
				echo "<div class=\"desc\"><b>$title</b><ul>$comment</ul>";
			} else {
				echo "</div><div class=\"main\" style=\"margin-top: -123px;\"><b>$title</b><ul style=\"line-height: 200%; margin-left: 30px;\">$comment</ul>";
			}
	
			if($quit) {
				echo '<br /><span class="red">'.$this->lang->line('error_quit_msg').'</span><br /><br /><br />';
			}
	
			echo '<input type="button" onclick="history.back()" value="'.$this->lang->line('click_to_back').'" /><br /><br /><br />';
	
			echo '</div>';
	
			$quit && $this->show_footer();
		}
	}
	
	function check_db($dbhost, $dbuser, $dbpw, $dbname, $tablepre) {
		if(!function_exists('mysql_connect')) {
			$this->show_msg('undefine_func', 'mysql_connect', 0);
		}
		if(!@mysql_connect($dbhost, $dbuser, $dbpw)) {
			$errno = mysql_errno();
			$error = mysql_error();
			if($errno == 1045) {
				$this->show_msg('database_errno_1045', $error, 0);
			} elseif($errno == 2003) {
				$this->show_msg('database_errno_2003', $error, 0);
			} else {
				$this->show_msg('database_connect_error', $error, 0);
			}
		} else {
			if($query = mysql_query("SHOW TABLES FROM $dbname")) {
				while($row = mysql_fetch_row($query)) {
					if(preg_match("/^$tablepre/", $row[0])) {
						return false;
					}
				}
			}
		}
		return true;
	}
	
	function dirfile_check(&$dirfile_items) {
		foreach($dirfile_items as $key => $item) {
			$item_path = $item['path'];
			if($item['type'] == 'dir') {
				if(!$this->dir_writeable($item_path)) {
					if(is_dir($item_path)) {
						$dirfile_items[$key]['status'] = 0;
						$dirfile_items[$key]['current'] = '+r';
					} else {
						$dirfile_items[$key]['status'] = -1;
						$dirfile_items[$key]['current'] = 'nodir';
					}
				} else {
					$dirfile_items[$key]['status'] = 1;
					$dirfile_items[$key]['current'] = '+r+w';
				}
			} else {
				if(file_exists($item_path)) {
					if(is_writable($item_path)) {
						$dirfile_items[$key]['status'] = 1;
						$dirfile_items[$key]['current'] = '+r+w';
					} else {
						$dirfile_items[$key]['status'] = 0;
						$dirfile_items[$key]['current'] = '+r';
					}
				} else {
					if(dir_writeable(dirname($item_path))) {
						$dirfile_items[$key]['status'] = 1;
						$dirfile_items[$key]['current'] = '+r+w';
					} else {
						$dirfile_items[$key]['status'] = -1;
						$dirfile_items[$key]['current'] = 'nofile';
					}
				}
			}
		}
	}
	
	function env_check(&$env_items) {
		foreach($env_items as $key => $item) {
			if($key == 'php') {
				$env_items[$key]['current'] = PHP_VERSION;
			} elseif($key == 'attachmentupload') {
				$env_items[$key]['current'] = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknow';
			} elseif($key == 'gdversion') {
				$tmp = function_exists('gd_info') ? gd_info() : array();
				$env_items[$key]['current'] = empty($tmp['GD Version']) ? 'noext' : $tmp['GD Version'];
				unset($tmp);
			} elseif($key == 'diskspace') {
				if(function_exists('disk_free_space')) {
					$env_items[$key]['current'] = floor(disk_free_space(APPPATH) / (1024*1024)).'M';
				} else {
					$env_items[$key]['current'] = 'unknow';
				}
			} elseif(isset($item['c'])) {
				$env_items[$key]['current'] = constant($item['c']);
			}
	
			$env_items[$key]['status'] = 1;
			if($item['r'] != 'notset' && strcmp($env_items[$key]['current'], $item['r']) < 0) {
				$env_items[$key]['status'] = 0;
			}
		}
	}
	
	function function_check(&$func_items) {
		foreach($func_items as $item) {
			function_exists($item) or $this->show_msg('undefine_func', $item, 0);
		}
	}
	
	function show_env_result(&$env_items, &$dirfile_items, &$func_items) {
	
		$env_str = $file_str = $dir_str = $func_str = '';
		$error_code = 0;
	
		foreach($env_items as $key => $item) {
			if($key == 'php' && strcmp($item['current'], $item['r']) < 0) {
				$this->show_msg('php_version_too_low', $item['current'], 0);
			}
			$status = 1;
			if($item['r'] != 'notset') {
				if(intval($item['current']) && intval($item['r'])) {
					if(intval($item['current']) < intval($item['r'])) {
						$status = 0;
						$error_code = ENV_CHECK_ERROR;
					}
				} else {
					if(strcmp($item['current'], $item['r']) < 0) {
						$status = 0;
						$error_code = ENV_CHECK_ERROR;
					}
				}
			}
			$view_off = $this->input->get_post('view_off') ? TRUE : FALSE;
			if($view_off) {
				$env_str .= "\t\t<runCondition name=\"$key\" status=\"$status\" Require=\"$item[r]\" Best=\"$item[b]\" Current=\"$item[current]\"/>\n";
			} else {
				$env_str .= "<tr>\n";
				$env_str .= "<td>".$this->lang->line($key)."</td>\n";
				$env_str .= "<td class=\"padleft\">".$this->lang->line($item['r'])."</td>\n";
				$env_str .= "<td class=\"padleft\">".$this->lang->line($item['b'])."</td>\n";
				$env_str .= ($status ? "<td class=\"w pdleft1\">" : "<td class=\"nw pdleft1\">").$item['current']."</td>\n";
				$env_str .= "</tr>\n";
			}
		}
	
		foreach($dirfile_items as $key => $item) {
			$tagname = $item['type'] == 'file' ? 'File' : 'Dir';
			$variable = $item['type'].'_str';
	
			if($view_off) {
				if($item['status'] == 0) {
					$error_code = ENV_CHECK_ERROR;
				}
				$$variable .= "\t\t\t<File name=\"$item[path]\" status=\"$item[status]\" requirePermisson=\"+r+w\" currentPermisson=\"$item[current]\" />\n";
			} else {
				$$variable .= "<tr>\n";
				$$variable .= "<td>$item[path]</td><td class=\"w pdleft1\">".$this->lang->line('writeable')."</td>\n";
				if($item['status'] == 1) {
					$$variable .= "<td class=\"w pdleft1\">".$this->lang->line('writeable')."</td>\n";
				} elseif($item['status'] == -1) {
					$error_code = ENV_CHECK_ERROR;
					$$variable .= "<td class=\"nw pdleft1\">".$this->lang->line('nodir')."</td>\n";
				} else {
					$error_code = ENV_CHECK_ERROR;
					$$variable .= "<td class=\"nw pdleft1\">".$this->lang->line('unwriteable')."</td>\n";
				}
				$$variable .= "</tr>\n";
			}
		}
	
		if($view_off) {
	
			$str = "<root>\n";
			$str .= "\t<runConditions>\n";
			$str .= $env_str;
			$str .= "\t</runConditions>\n";
			$str .= "\t<FileDirs>\n";
			$str .= "\t\t<Dirs>\n";
			$str .= $dir_str;
			$str .= "\t\t</Dirs>\n";
			$str .= "\t\t<Files>\n";
			$str .= $file_str;
			$str .= "\t\t</Files>\n";
			$str .= "\t</FileDirs>\n";
			$str .= "\t<error errorCode=\"$error_code\" errorMessage=\"\" />\n";
			$str .= "</root>";
			echo $str;
			exit;
	
		} else {
	
			$this->show_header();
	
			echo "<h2 class=\"title\">".$this->lang->line('env_check')."</h2>\n";
			echo "<table class=\"tb\" style=\"margin:20px 0 20px 55px;\">\n";
			echo "<tr>\n";
			echo "\t<th>".$this->lang->line('project')."</th>\n";
			echo "\t<th class=\"padleft\">".$this->lang->line('ucenter_required')."</th>\n";
			echo "\t<th class=\"padleft\">".$this->lang->line('ucenter_best')."</th>\n";
			echo "\t<th class=\"padleft\">".$this->lang->line('curr_server')."</th>\n";
			echo "</tr>\n";
			echo $env_str;
			echo "</table>\n";
	
			echo "<h2 class=\"title\">".$this->lang->line('priv_check')."</h2>\n";
			echo "<table class=\"tb\" style=\"margin:20px 0 20px 55px;width:90%;\">\n";
			echo "\t<tr>\n";
			echo "\t<th>".$this->lang->line('step1_file')."</th>\n";
			echo "\t<th class=\"padleft\">".$this->lang->line('step1_need_status')."</th>\n";
			echo "\t<th class=\"padleft\">".$this->lang->line('step1_status')."</th>\n";
			echo "</tr>\n";
			echo $file_str;
			echo $dir_str;
			echo "</table>\n";
	
			foreach($func_items as $item) {
				$status = function_exists($item);
				$func_str .= "<tr>\n";
				$func_str .= "<td>$item()</td>\n";
				if($status) {
					$func_str .= "<td class=\"w pdleft1\">".$this->lang->line('supportted')."</td>\n";
					$func_str .= "<td class=\"padleft\">".$this->lang->line('none')."</td>\n";
				} else {
					$error_code = ENV_CHECK_ERROR;
					$func_str .= "<td class=\"nw pdleft1\">".$this->lang->line('unsupportted')."</td>\n";
					$func_str .= "<td><font color=\"red\">".$this->lang->line('advice_'.$item)."</font></td>\n";
				}
			}
			echo "<h2 class=\"title\">".$this->lang->line('func_depend')."</h2>\n";
			echo "<table class=\"tb\" style=\"margin:20px 0 20px 55px;width:90%;\">\n";
			echo "<tr>\n";
			echo "\t<th>".$this->lang->line('func_name')."</th>\n";
			echo "\t<th class=\"padleft\">".$this->lang->line('check_result')."</th>\n";
			echo "\t<th class=\"padleft\">".$this->lang->line('suggestion')."</th>\n";
			echo "</tr>\n";
			echo $func_str;
			echo "</table>\n";
	
			$this->show_next_step(2, $error_code);
	
			$this->show_footer();
	
		}
	
	}
	
	function show_next_step($step, $error_code) {
		echo "<form action=\"install\" method=\"get\">\n";
		echo "<input type=\"hidden\" name=\"step\" value=\"$step\" />";
		if(isset($GLOBALS['hidden'])) {
			echo $GLOBALS['hidden'];
		}
		if($error_code == 0) {
			$nextstep = "<input type=\"button\" onclick=\"history.back();\" value=\"".$this->lang->line('old_step')."\"><input type=\"submit\" value=\"".$this->lang->line('new_step')."\">\n";
		} else {
			$nextstep = "<input type=\"button\" disabled=\"disabled\" value=\"".$this->lang->line('not_continue')."\">\n";
		}
		echo "<div class=\"btnbox marginbot\">".$nextstep."</div>\n";
		echo "</form>\n";
	}
	
	function show_form(&$form_items, $error_msg) {
	
		$step = intval($_REQUEST['step']) ? intval($_REQUEST['step']) : 0;
	
		if(empty($form_items) || !is_array($form_items)) {
			return;
		}
	
		$this->show_header();
		$this->show_setting('start');
		$this->show_setting('hidden', 'step', $step);
		$is_first = 1;
		foreach($form_items as $key => $items) {
			global ${'error_'.$key};
			if($is_first == 0) {
				echo '</table>';
			}
	
			if(!${'error_'.$key}) {
				$this->show_tips('tips_'.$key);
			} else {
				$this->show_error('tips_admin_config', ${'error_'.$key});
			}
	
			if($is_first == 0) {
				echo '<table class="tb2">';
			}
	
			foreach($items as $k => $v) {
				if(!empty($error_msg)) {
					$value = isset($_POST[$key][$k]) ? $_POST[$key][$k] : '';
				} else {
					if(isset($v['value']) && is_array($v['value'])) {
						if($v['value']['type'] == 'constant') {
							$value = defined($v['value']['var']) ? constant($v['value']['var']) : '';
						} elseif($v['value']['type'] == 'var') {
							$value = $GLOBALS[$v['value']['var']];
						} elseif($v['value']['type'] == 'string') {
							$value = $v['value']['var'];
						}
					} else {
						$value = '';
					}
				}
				if($v['type'] == 'checkbox') {
					$value = '1';
				}
				$this->show_setting($k, $key.'['.$k.']', $value, $v['type'], isset($error_msg[$key][$k]) ? $key.'_'.$k.'_invalid' : '');
			}
	
			if($is_first) {
				$is_first = 0;
			}
		}
		$this->show_setting('', 'submitname', 'new_step', 'submit');
		$this->show_setting('end');
		$this->show_footer();
	}
	
	function show_license() {
		$view_off = $this->input->get_post('view_off') ? TRUE : FALSE;
		$self = $this->input->get_post('self');
		$uchidden = $this->input->get_post('uchidden');
		$step = intval($_REQUEST['step']) ? intval($_REQUEST['step']) : 0;
		$next = $step + 1;
		if($view_off)
		{
			$this->show_msg('license_contents', $this->lang->line('license'), 1);
		}
		else
		{
			$this->show_header();
	
			$license = str_replace('  ', '&nbsp; ', $this->lang->line('license'));
			$lang_agreement_yes = $this->lang->line('agreement_yes');
			$lang_agreement_no = $this->lang->line('agreement_no');
			echo <<<EOT
</div>
<div class="main" style="margin-top:-123px;">
	<div class="licenseblock">$license</div>
	<div class="btnbox marginbot">
		<form method="get" action="install">
		<input type="hidden" name="step" value="$next">
		$uchidden
		<input type="submit" name="submit" value="{$lang_agreement_yes}" style="padding: 2px">&nbsp;
		<input type="button" name="exit" value="{$lang_agreement_no}" style="padding: 2px" onclick="javascript: window.close(); return false;">
		</form>
	</div>
EOT;
	
			$this->show_footer();
	
		}
	}
	
	function createtable($sql) {
		$type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
		$type = in_array($type, array('MYISAM', 'HEAP')) ? $type : 'MYISAM';
		return preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql).
		(mysql_get_server_info() > '4.1' ? " ENGINE=$type DEFAULT CHARSET=".$this->db->char_set : " TYPE=$type");
	}
	
	function dir_writeable($dir) {
		$writeable = 0;
		if(!is_dir($dir)) {
			@mkdir($dir, 0777);
		}
		if(is_dir($dir)) {
			if($fp = @fopen("$dir/test.txt", 'w')) {
				@fclose($fp);
				@unlink("$dir/test.txt");
				$writeable = 1;
			} else {
				$writeable = 0;
			}
		}
		return $writeable;
	}
	
	function dir_clear($dir) {
		global $lang;
		showjsmessage($lang['clear_dir'].' '.str_replace(APPPATH, '', $dir));
		$directory = dir($dir);
		while($entry = $directory->read()) {
			$filename = $dir.'/'.$entry;
			if(is_file($filename)) {
				@unlink($filename);
			}
		}
		$directory->close();
		@touch($dir.'/index.htm');
	}
	
	function show_header() {
		$step = isset($_REQUEST['step']) ? intval($_REQUEST['step']) : 0;
		$version = UC_VERSION;
		$release = UC_RELEASE;
		$charset = UC_CHARSET;
		echo <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=$charset" />
<title>{$this->lang->line('title_install')}</title>
<link rel="stylesheet" href="{$this->config->base_url('css/install.css')}" type="text/css" media="all" />
<script type="text/javascript">
	function $(id) {
		return document.getElementById(id);
	}
	
	function showmessage(message) {
		$('notice').value += message + "\\r\\n";
	}
</script>
<meta content="Comsenz Inc." name="Copyright" />
</head>
<div class="container">
	<div class="header">
		<h1>{$this->lang->line('title_install')}</h1>
		<span>V$version {$this->lang->line(UC_INSTALL_LANG)} $release</span>
EOT;
	
		$step > 0 && $this->show_step($step);
	}
	
	function show_footer($quit = true) {
	
		echo <<<EOT
		<div class="footer">&copy;2001 - 2008 <a href="http://www.comsenz.com/">Comsenz</a> Inc.</div>
	</div>
</div>
</body>
</html>
EOT;
		$quit && exit();
	}
	
	function loginit($logfile) {
		global $lang;
		showjsmessage($lang['init_log'].' '.$logfile);
		if($fp = @fopen('./forumdata/logs/'.$logfile.'.php', 'w')) {
			fwrite($fp, '<'.'?PHP exit(); ?'.">\n");
			fclose($fp);
		}
	}
	
	function showjsmessage($message) {
		$view_off = $this->input->get_post('view_off') ? TRUE : FALSE;
		if($view_off) return;
		echo '<script type="text/javascript">showmessage(\''.addslashes($message).' \');</script>'."\r\n";
		flush();
		ob_flush();
	}
	
	function random($length) {
		$hash = '';
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
		$max = strlen($chars) - 1;
		PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
		for($i = 0; $i < $length; $i++) {
			$hash .= $chars[mt_rand(0, $max)];
		}
		return $hash;
	}
	
	function redirect($url) {
	
		echo "<script>".
				"function redirect() {window.location.replace('$url');}\n".
				"setTimeout('redirect();', 0);\n".
				"</script>";
		exit();
	
	}
	
	
	function config_edit() {
		global $form_db_init_items;
		if(isset($form_db_init_items) && is_array($form_db_init_items))
		{
			foreach($form_db_init_items as $key => $items)
			{
				$$key = $this->input->post($key);
				foreach($items as $k => $v)
				{
					$tmp = $$key;
					$$k = $tmp[$k];
				}
			}
		}
	
		$ucsalt = substr(uniqid(rand()), 0, 6);
		$ucfounderpw= md5(md5($ucfounderpw).$ucsalt);
		$regdate = time();
	
		$ucauthkey = $this->generate_key();
		$ucsiteid = $this->generate_key();
		$ucmykey = $this->generate_key();
	
		$constants = file_get_contents(CONSTANTS);
		$pattern = array(
				"/define\('UC_FOUNDERPW', '.*'\);/",
				"/define\('UC_FOUNDERSALT', '.*'\);/",
				"/define\('UC_KEY', '.*'\);/",
				"/define\('UC_SITEID', '.*'\);/",
				"/define\('UC_MYKEY', '.*'\);/",
				"/define\('UC_DEBUG', '.*'\);/",
				"/define\('UC_PPP', '.*'\);/",
		);
		$replacement = array(
				"define('UC_FOUNDERPW', '$ucfounderpw');",
				"define('UC_FOUNDERSALT', '$ucsalt');",
				"define('UC_KEY', '$ucauthkey');",
				"define('UC_SITEID', '$ucsiteid');",
				"define('UC_MYKEY', '$ucmykey');",
				"define('UC_DEBUG', false);",
				"define('UC_PPP', 20);",
		);
		$constants = preg_replace($pattern, $replacement, $constants);
		$fp = @fopen(CONSTANTS, 'w');
		@fwrite($fp, $constants);
		@fclose($fp);
	
		$database = file_get_contents(DATABASE);
		$pattern = array(
				"/\\\$db\['default'\]\['hostname'\] \= '.*';/",
				"/\\\$db\['default'\]\['username'\] \= '.*';/",
				"/\\\$db\['default'\]\['password'\] \= '.*';/",
				"/\\\$db\['default'\]\['database'\] \= '.*';/",
				"/\\\$db\['default'\]\['dbprefix'\] \= '.*';/",
		);
		$replacement = array(
				"\$db['default']['hostname'] = '$dbhost';",
				"\$db['default']['username'] = '$dbuser';",
				"\$db['default']['password'] = '$dbpw';",
				"\$db['default']['database'] = '$dbname';",
				"\$db['default']['dbprefix'] = '$tablepre';",
		);
		$database = preg_replace($pattern, $replacement, $database);
		$fp = @fopen(DATABASE, 'w');
		@fwrite($fp, $database);
		@fclose($fp);
	}
	
	function generate_key() {
		$random = $this->random(32);
		$info = md5($_SERVER['SERVER_SOFTWARE'].$_SERVER['SERVER_NAME'].$_SERVER['SERVER_ADDR'].$_SERVER['SERVER_PORT'].$_SERVER['HTTP_USER_AGENT'].time());
		$return = '';
		for($i=0; $i<64; $i++) {
			$p = intval($i/2);
			$return[$i] = $i % 2 ? $random[$p] : $info[$p];
		}
		return implode('', $return);
	}
	
	function show_install() {
		$view_off = $this->input->get_post('view_off') ? TRUE : FALSE;
		if($view_off) return;
		$step = intval($_REQUEST['step']) ? intval($_REQUEST['step']) : 0;
		echo <<<EOT
<script type="text/javascript">
function showmessage(message) {
	document.getElementById('notice').value += message + '\\n';
}
function initinput() {
	window.location='{$this->config->base_url('install?step='.($step+1))}';
}
</script>
	<div class="main">
		<div class="btnbox"><textarea name="notice" style="width: 80%;"  readonly="readonly" id="notice"></textarea></div>
		<div class="btnbox marginbot">
	<input type="button" name="submit" value="{$this->lang->line('install_in_processed')}" disabled style="height: 25" id="laststep" onclick="initinput()">
	</div>
EOT;
	}
	
	function runquery($sql) {
		global $lang, $tablepre;
	
		if(!isset($sql) || empty($sql)) return;
	
		$sql = str_replace("\r", "\n", str_replace(' uc_', ' '.$this->db->dbprefix, $sql));
		$ret = array();
		$num = 0;
		foreach(explode(";\n", trim($sql)) as $query) {
			$ret[$num] = '';
			$queries = explode("\n", trim($query));
			foreach($queries as $query) {
				$ret[$num] .= (isset($query[0]) && $query[0] == '#') || (isset($query[1]) && isset($query[1]) && $query[0].$query[1] == '--') ? '' : $query;
			}
			$num++;
		}
		unset($sql);
	
		foreach($ret as $query) {
			$query = trim($query);
			if($query) {
	
				if(substr($query, 0, 12) == 'CREATE TABLE') {
					$name = preg_replace("/CREATE TABLE ([a-z0-9_]+) .*/is", "\\1", $query);
					$this->showjsmessage($this->lang->line('create_table').' '.$name.' ... '.$this->lang->line('succeed'));
					$this->db->query($this->createtable($query));
				} else {
					$this->db->query($query);
				}
	
			}
		}
	
	}
	
	function charcovert($string) {
		if(!get_magic_quotes_gpc()) {
			$string = str_replace('\'', '\\\'', $string);
		} else {
			$string = str_replace('\"', '"', $string);
		}
		return $string;
	}
	
	function insertconfig($s, $find, $replace) {
		if(preg_match($find, $s)) {
			$s = preg_replace($find, $replace, $s);
		} else {
			// ���뵽���һ��
			$s .= "\r\n".$replace;
		}
		return $s;
	}
	
	
	
	function var_to_hidden($k, $v) {
		return "<input type=\"hidden\" name=\"$k\" value=\"$v\" />\n";
	}
	
	function dfopen($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE) {
		$return = '';
		$matches = parse_url($url);
		$host = $matches['host'];
		$path = $matches['path'] ? $matches['path'].(isset($matches['query']) && $matches['query'] ? '?'.$matches['query'] : '') : '/';
		$port = !empty($matches['port']) ? $matches['port'] : 80;
	
		if($post) {
			$out = "POST $path HTTP/1.0\r\n";
			$out .= "Accept: */*\r\n";
			//$out .= "Referer: $boardurl\r\n";
			$out .= "Accept-Language: zh-cn\r\n";
			$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$out .= "User-Agent: {$this->input->user_agent()}\r\n";
			$out .= "Host: $host\r\n";
			$out .= 'Content-Length: '.strlen($post)."\r\n";
			$out .= "Connection: Close\r\n";
			$out .= "Cache-Control: no-cache\r\n";
			$out .= "Cookie: $cookie\r\n\r\n";
			$out .= $post;
		} else {
			$out = "GET $path HTTP/1.0\r\n";
			$out .= "Accept: */*\r\n";
			//$out .= "Referer: $boardurl\r\n";
			$out .= "Accept-Language: zh-cn\r\n";
			$out .= "User-Agent: {$this->input->user_agent()}\r\n";
			$out .= "Host: $host\r\n";
			$out .= "Connection: Close\r\n";
			$out .= "Cookie: $cookie\r\n\r\n";
		}
		$fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
		if(!$fp) {
			return '';
		} else {
			stream_set_blocking($fp, $block);
			stream_set_timeout($fp, $timeout);
			@fwrite($fp, $out);
			$status = stream_get_meta_data($fp);
			if(!$status['timed_out']) {
				while (!feof($fp)) {
					if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
						break;
					}
				}
	
				$stop = false;
				while(!feof($fp) && !$stop) {
					$data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
					$return .= $data;
					if($limit) {
						$limit -= strlen($data);
						$stop = $limit <= 0;
					}
				}
			}
			@fclose($fp);
			return $return;
		}
	}
	
	function check_env() {
	
		global $lang, $attachdir;
	
		$errors = array('quit' => false);
		$quit = false;
	
		if(!function_exists('mysql_connect')) {
			$errors[] = 'mysql_unsupport';
			$quit = true;
		}
	
		if(PHP_VERSION < '4.3') {
			$errors[] = 'php_version_430';
			$quit = true;
		}
	
		if(!file_exists(DISCUZ_ROOT.'./config.inc.php')) {
			$errors[] = 'config_nonexistence';
			$quit = true;
		} elseif(!is_writeable(DISCUZ_ROOT.'./config.inc.php')) {
			$errors[] = 'config_unwriteable';
			$quit = true;
		}
	
		$checkdirarray = array(
				'attach' => $attachdir,
				'forumdata' => './forumdata',
				'cache' => './forumdata/cache',
				'ftemplates' => './forumdata/templates',
				'threadcache' => './forumdata/threadcaches',
				'log' => './forumdata/logs',
				'uccache' => './uc_client/data/cache'
		);
	
		foreach($checkdirarray as $key => $dir) {
			if(!dir_writeable(DISCUZ_ROOT.$dir)) {
				$langkey = $key.'_unwriteable';
				$errors[] = $key.'_unwriteable';
				if(!in_array($key, array('ftemplate'))) {
					$quit = TRUE;
				}
			}
		}
	
		$errors['quit'] = $quit;
		return $errors;
	
	}
	
	function show_error1($type, $errors = '', $quit = false) {
	
		global $lang, $step;
	
		$title = $this->lang->line($type);
		$comment = $this->lang->line($type.'_comment', false);
		$errormsg = '';
		if($errors) {
			if(!empty($errors)) {
				foreach ((array)$errors as $k => $v) {
					if(is_numeric($k)) {
						$comment .= "<li><em class=\"red\">".$this->lang->line($v)."</em></li>";
					}
				}
			}
		}
	
		if($step > 0) {
			echo "<div class=\"desc\"><b>$title</b><ul>$comment</ul>";
		} else {
			echo "</div><div class=\"main\" style=\"margin-top: -123px;\"><b>$title</b><ul style=\"line-height: 200%; margin-left: 30px;\">$comment</ul>";
		}
	
		if($quit) {
			echo '<br /><span class="red">'.$lang['error_quit_msg'].'</span><br /><br /><br /><br /><br /><br />';
		}
	
		echo '</div>';
	
		$quit && show_footer();
	}
	
	function show_tips($tip, $title = '', $comment = '', $style = 1) {
	
		$title = empty($title) ? $this->lang->line($tip) : $title;
		$comment = empty($comment) ? $this->lang->line($tip.'_comment', FALSE) : $comment;
		if($style) {
			echo "<div class=\"desc\"><b>$title</b>";
		} else {
			echo "</div><div class=\"main\" style=\"margin-top: -123px;\">$title<div class=\"desc1 marginbot\"><ul>";
		}
		$comment && print('<br>'.$comment);
		echo "</div>";
	}
	
	function show_setting($setname, $varname = '', $value = '', $type = 'text|password|checkbox', $error = '') {
		if($setname == 'start') {
			echo "<form method=\"post\" action=\"install\">\n<table class=\"tb2\">\n";
			return;
		} elseif($setname == 'end') {
			echo "\n</table>\n</form>\n";
			return;
		} elseif($setname == 'hidden') {
			echo "<input type=\"hidden\" name=\"$varname\" value=\"$value\">\n";
			return;
		}
	
		echo "\n".'<tr><th class="tbopt'.($error ? ' red' : '').'">&nbsp;'.(empty($setname) ? '' : $this->lang->line($setname).':')."</th>\n<td>";
		if($type == 'text' || $type == 'password') {
			$value = htmlspecialchars($value);
			echo "<input type=\"$type\" name=\"$varname\" value=\"$value\" size=\"35\" class=\"txt\">";
		} elseif($type == 'submit') {
			$value = empty($value) ? 'next_step' : $value;
			echo "<input type=\"submit\" name=\"$varname\" value=\"".$this->lang->line($value)."\" class=\"btn\">\n";
		} elseif($type == 'checkbox') {
			if(!is_array($varname) && !is_array($value)) {
				echo'<label><input type="checkbox" name="'.$varname.'" value="'.$value."\" style=\"border: 0\">".$this->lang->line($setname.'_check_label')."</label>\n";
			}
		} else {
			echo $value;
		}
	
		echo "</td>\n<td>&nbsp;";
		if($error) {
			$comment = '<span class="red">'.(is_string($error) ? $this->lang->line($error) : $this->lang->line($setname.'_error')).'</span>';
		} else {
			$comment = $this->lang->line($setname.'_comment', false);
		}
		echo "$comment</td>\n</tr>\n";
		return true;
	}
	
	function show_step($step) {
	
		$allow_method = array('show_license', 'env_check', 'db_init', 'ext_info', 'install_check', 'tablepre_check');
		$method = $this->input->get_post('method');
		if(empty($method) || !in_array($method, $allow_method)) {
			$method = isset($allow_method[$step]) ? $allow_method[$step] : '';
		}
	
		if(empty($method)) {
			$this->show_msg('method_undefined', $method, 0);
		}
	
		$laststep = 4;
		$title = $this->lang->line('step_'.$method.'_title');
		$comment = $this->lang->line('step_'.$method.'_desc');
	
		$stepclass = array();
		for($i = 1; $i <= $laststep; $i++) {
			$stepclass[$i] = $i == $step ? 'current' : ($i < $step ? '' : 'unactivated');
		}
		$stepclass[$laststep] .= ' last';
	
		echo <<<EOT
	<div class="setup step{$step}">
		<h2>$title</h2>
		<p>$comment</p>
	</div>
	<div class="stepstat">
		<ul>
			<li class="$stepclass[1]">1</li>
			<li class="$stepclass[2]">2</li>
			<li class="$stepclass[3]">3</li>
			<li class="$stepclass[4]">4</li>
		</ul>
		<div class="stepstatbg stepstat1"></div>
	</div>
</div>
<div class="main">
EOT;
	
	}
	
// 	function lang($lang_key, $force = true) {
// 		global $CI;
// 		$CI->load->language('install');
// 		return $CI->lang->line($lang_key) ? $CI->lang->line($lang_key) : ($force ? $lang_key : '');
// 	}
	
	function check_adminuser($username, $password, $email) {
	
		@include APPPATH.'./config.inc.php';
		include APPPATH.'./uc_client/client.php';
		$error = '';
		$uid = uc_user_register($username, $password, $email);
		/*
		 -1 : �û���Ϸ�
		-2 : ������ע��Ĵ���
		-3 : �û����Ѿ�����
		-4 : email ��ʽ����
		-5 : email ������ע��
		-6 : �� email �Ѿ���ע��
		>1 : ��ʾ�ɹ�����ֵΪ UID
		*/
		if($uid == -1 || $uid == -2) {
			$error = 'admin_username_invalid';
		} elseif($uid == -4 || $uid == -5 || $uid == -6) {
			$error = 'admin_email_invalid';
		} elseif($uid == -3) {
			$ucresult = uc_user_login($username, $password);
			list($tmp['uid'], $tmp['username'], $tmp['password'], $tmp['email']) = uc_addslashes($ucresult);
			$ucresult = $tmp;
			if($ucresult['uid'] <= 0) {
				$error = 'admin_exist_password_error';
			} else {
				$uid = $ucresult['uid'];
				$email = $ucresult['email'];
				$password = $ucresult['password'];
			}
		}
	
		if(!$error && $uid > 0) {
			$password = md5($password);
			uc_user_addprotected($username, '');
		} else {
			$uid = 0;
			$error = empty($error) ? 'error_unknow_type' : $error;
		}
		return array('uid' => $uid, 'username' => $username, 'password' => $password, 'email' => $email, 'error' => $error);
	}
	
	function save_uc_config($config, $file) {
	
		$success = false;
	
		list($appauthkey, $appid, $ucdbhost, $ucdbname, $ucdbuser, $ucdbpw, $ucdbcharset, $uctablepre, $uccharset, $ucapi, $ucip) = explode('|', $config);
	
		if($content = file_get_contents($file)) {
			$content = trim($content);
			$content = substr($content, -2) == '?>' ? substr($content, 0, -2) : $content;
			$link = mysql_connect($ucdbhost, $ucdbuser, $ucdbpw, 1);
			$uc_connnect = $link && mysql_select_db($ucdbname, $link) ? 'mysql' : '';
			$content = insertconfig($content, "/define\('UC_CONNECT',\s*'.*?'\);/i", "define('UC_CONNECT', '$uc_connnect');");
			$content = insertconfig($content, "/define\('UC_DBHOST',\s*'.*?'\);/i", "define('UC_DBHOST', '$ucdbhost');");
			$content = insertconfig($content, "/define\('UC_DBUSER',\s*'.*?'\);/i", "define('UC_DBUSER', '$ucdbuser');");
			$content = insertconfig($content, "/define\('UC_DBPW',\s*'.*?'\);/i", "define('UC_DBPW', '$ucdbpw');");
			$content = insertconfig($content, "/define\('UC_DBNAME',\s*'.*?'\);/i", "define('UC_DBNAME', '$ucdbname');");
			$content = insertconfig($content, "/define\('UC_DBCHARSET',\s*'.*?'\);/i", "define('UC_DBCHARSET', '$ucdbcharset');");
			$content = insertconfig($content, "/define\('UC_DBTABLEPRE',\s*'.*?'\);/i", "define('UC_DBTABLEPRE', '`$ucdbname`.$uctablepre');");
			$content = insertconfig($content, "/define\('UC_DBCONNECT',\s*'.*?'\);/i", "define('UC_DBCONNECT', '0');");
			$content = insertconfig($content, "/define\('UC_KEY',\s*'.*?'\);/i", "define('UC_KEY', '$appauthkey');");
			$content = insertconfig($content, "/define\('UC_API',\s*'.*?'\);/i", "define('UC_API', '$ucapi');");
			$content = insertconfig($content, "/define\('UC_CHARSET',\s*'.*?'\);/i", "define('UC_CHARSET', '$uccharset');");
			$content = insertconfig($content, "/define\('UC_IP',\s*'.*?'\);/i", "define('UC_IP', '$ucip');");
			$content = insertconfig($content, "/define\('UC_APPID',\s*'?.*?'?\);/i", "define('UC_APPID', '$appid');");
			$content = insertconfig($content, "/define\('UC_PPP',\s*'?.*?'?\);/i", "define('UC_PPP', '20');");
	
			$fp = @fopen($file, 'w');
			$success = (boolean)@fwrite($fp, $content);
			@fclose($fp);
		}
	
		return $success;
	}
	
}

/* End of file install.php */
/* Location: application/controllers/install.php */