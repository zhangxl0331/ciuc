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

define('UC_NAME', 'UCenter');
define('UC_VERSION', '1.5.0');
define('UC_RELEASE', '20090121');
define('UC_API', strtolower((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']));



define('UC_CHARSET', 'utf-8');
define('DBCHARSET', 'utf8');


define('UC_FOUNDERPW', '11a70e6b057110b82f2bb2d8c0431c51');
define('UC_FOUNDERSALT', '134180');
define('UC_KEY', '4dcL8G6s36dI7Z6TfsaqfJcW0T7O005Ddi9A3w8Ub3cwc823610kcI5N1q1s8I1t');
define('UC_SITEID', '4Zck8N6a3pdG7t6LfKaMf1cL0z790I5PdE9X378Cblcucm2y680HcN5p1R11871R');
define('UC_MYKEY', '4gcF8y693qdr7L61fha4fYcV057W095Ad9983G8Qb7cLcY2S6z0McH5v1R1v8S1l');
define('UC_DEBUG', false);
define('UC_PPP', 20);


/* End of file constants.php */
/* Location: ./application/config/constants.php */