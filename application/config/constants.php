<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/*
|--------------------------------------------------------------------------
| UCenter Constants And Variables
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('SOFT_NAME', 'UCenter');
define('SOFT_VERSION', '1.5.0');
define('SOFT_RELEASE', '20090121');
define('INSTALL_LANG', 'SC_UTF8');

define('CONSTANTS', APPPATH.'config/constants.php');
define('DATABASE', APPPATH.'config/database.php');
define('SQLFILE', FCPATH.'data/uc.sql');
define('LOCK_INSTALL', FCPATH.'data/install.lock');
define('LOCK_UPGRADE', FCPATH.'data/upgrade.lock');

define('CHARSET', 'utf-8');
define('DBCHARSET', 'utf8');

define('ORIG_TABLEPRE', 'uc_');

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

define('UC_FOUNDERPW', '14ea5d01d47b28d9f972dd1babc2deaf');
define('UC_FOUNDERSALT', '117460');
define('UC_KEY', '4dcL8G6s36dI7Z6TfsaqfJcW0T7O005Ddi9A3w8Ub3cwc823610kcI5N1q1s8I1t');
define('UC_SITEID', '4Zck8N6a3pdG7t6LfKaMf1cL0z790I5PdE9X378Cblcucm2y680HcN5p1R11871R');
define('UC_MYKEY', '4gcF8y693qdr7L61fha4fYcV057W095Ad9983G8Qb7cLcY2S6z0McH5v1R1v8S1l');
define('UC_DEBUG', false);
define('UC_PPP', 20);


/* End of file constants.php */
/* Location: ./application/config/constants.php */