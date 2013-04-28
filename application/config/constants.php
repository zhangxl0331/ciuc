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
define('UC_FOUNDERPW', '2cb6c0859828e7e6f3742f73eb665e33');
define('UC_FOUNDERSALT', '106786');
define('UC_KEY', '2hfE5waxaRdp5B5u5iaT1SfU1Q3Wd980fM4xf76P3waW9c5v4Qczdre44Hdyf52a');
define('UC_SITEID', '2Uft5VaMapdY5K5y5saY1ofg1k3Bdo84fJ4Dfa6T3GaA9a5L4wc3dLeD48dofg2E');
define('UC_MYKEY', '2yf856aLaodr515o5Nai1if31036dC8ZfW4Wfr6j3Rav98564ecVdJeM4idGfp2E');
define('UC_DEBUG', false);
define('UC_PPP', 20);


/* End of file constants.php */
/* Location: ./application/config/constants.php */