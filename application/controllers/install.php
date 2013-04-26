<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
@set_time_limit(1000);
set_magic_quotes_runtime(0);

define('INSTALL_LANG', 'SC_UTF8');
define('CONSTANTS', APPPATH.'config/constants.php');
define('DATABASE', APPPATH.'config/database.php');
define('SQLFILE', FCPATH.'data/uc.sql');
define('LOCK_INSTALL', FCPATH.'data/install.lock');
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
	}
	
	public function index()
	{		
		global $func_items, $env_items, $dirfile_items, $form_db_init_items, $allow_method;
		
		$this->load->helper('install');
		
		$view_off = getgpc('view_off') ? TRUE : FALSE;
		
		$step = intval(getgpc('step', 'R')) ? intval(getgpc('step', 'R')) : 0;
		$method = getgpc('method');
		
		if(empty($method) || !in_array($method, $allow_method))
		{
			$method = isset($allow_method[$step]) ? $allow_method[$step] : '';
		}		
		
		if(empty($method)) 
		{
			show_msg('method_undefined', $method, 0);
		}	
		
		if(file_exists(LOCK_INSTALL))
		{
			show_msg('install_locked', '', 0);
		}
					
		if($method == 'show_license') 
		{		
			show_license();		
		} 
		elseif($method == 'env_check') 
		{			
			$view_off && function_check($func_items);
		
			env_check($env_items);
		
			dirfile_check($dirfile_items);
		
			show_env_result($env_items, $dirfile_items, $func_items);
		
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
					$$key = getgpc($key, 'p');
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
		
			if(!$view_off && $_SERVER['REQUEST_METHOD'] == 'POST') 
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
					$dbname_not_exists = check_db($dbhost, $dbuser, $dbpw, $dbname, $tablepre);
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
					show_msg('dbname_invalid', $dbname, 0);
				} 
				else 
				{
					if(!@mysql_connect($dbhost, $dbuser, $dbpw)) 
					{
						$errno = mysql_errno();
						$error = mysql_error();
						if($errno == 1045) 
						{
							show_msg('database_errno_1045', $error, 0);
						} 
						elseif($errno == 2003) 
						{
							show_msg('database_errno_2003', $error, 0);
						} 
						else 
						{
							show_msg('database_connect_error', $error, 0);
						}
					}
					if(mysql_get_server_info() > '4.1') 
					{
						mysql_query("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET ".DBCHARSET);
					} 
					else 
					{
						mysql_query("CREATE DATABASE IF NOT EXISTS `$dbname`");
					}
		
					if(mysql_errno()) 
					{
						show_msg('database_errno_1044', mysql_error(), 0);
					}
					mysql_close();
				}
		
				if(strpos($tablepre, '.') !== false || intval($tablepre{0})) 
				{
					show_msg('tablepre_invalid', $tablepre, 0);
				}
				
				config_edit();
		
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
				$params['char_set'] = DBCHARSET;
				$params['dbcollat'] = 'utf8_general_ci';
				$params['swap_pre'] = '';
				$params['autoinit'] = TRUE;
				$params['stricton'] = FALSE;
				$this->load->database($params);
		
				$sql = file_get_contents(SQLFILE);
				$sql = str_replace("\r\n", "\n", $sql);
		
				if(!$view_off) 
				{
					show_header();
					show_install();
				}
		
				runquery($sql);
		
				$view_off && show_msg('initdbresult_succ');
		
				if(!$view_off) 
				{
					echo '<script type="text/javascript">document.getElementById("laststep").disabled=false;document.getElementById("laststep").value = \''.lang('install_succeed').'\';</script>'."\r\n";
					show_footer();
				}
		
			}
			if($view_off) 
			{
				show_msg('missing_parameter', '', 0);		
			} 
			else 
			{				
				show_form($form_db_init_items, $error_msg);		
			}
		
		} 
		elseif($method == 'ext_info') 
		{		
			@touch(LOCK_INSTALL);
			@touch(FCPATH.'data/install.lock');
			if($view_off) 
			{
				show_msg('ext_info_succ');
			} 
			else 
			{		
				include CONFIG;
				$md5password =  UC_FOUNDERPW;
				setcookie('uc_founderauth', authcode("|$md5password|".md5($_SERVER['HTTP_USER_AGENT'])."|1", 'ENCODE', UC_KEY), time() + 3600, '/');
				header("Location:.admin.php?m=frame&a=index&mainurl=".urlencode('admin.php?m=app&a=add'));		
			}
		
		} 
		elseif($method == 'install_check') 
		{		
			if(file_exists(LOCK_INSTALL)) 
			{
				@touch(LOCK_UPGRADE);
				show_msg('installstate_succ');
			} 
			else 
			{
				show_msg('lock_file_not_touch', LOCK_INSTALL, 0);
			}
		
		} 
		elseif($method == 'tablepre_check') 
		{
		
			$dbinfo = getgpc('dbinfo');
			extract($dbinfo);
			if(check_db($dbhost, $dbuser, $dbpw, $dbname, $tablepre)) 
			{
				show_msg('tablepre_not_exists', 0);
			} 
			else 
			{
				show_msg('tablepre_exists', $tablepre, 0);
			}
		}
	}
}

/* End of file install.php */
/* Location: application/controllers/install.php */