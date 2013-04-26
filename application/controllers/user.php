<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('UC_USER_CHECK_USERNAME_FAILED', -1);
define('UC_USER_USERNAME_BADWORD', -2);
define('UC_USER_USERNAME_EXISTS', -3);
define('UC_USER_EMAIL_FORMAT_ILLEGAL', -4);
define('UC_USER_EMAIL_ACCESS_ILLEGAL', -5);
define('UC_USER_EMAIL_EXISTS', -6);

define('UC_LOGIN_SUCCEED', 0);
define('UC_LOGIN_ERROR_FOUNDER_PW', -1);
define('UC_LOGIN_ERROR_ADMIN_PW', -2);
define('UC_LOGIN_ERROR_ADMIN_NOT_EXISTS', -3);
define('UC_LOGIN_ERROR_SECCODE', -4);
define('UC_LOGIN_ERROR_FAILEDLOGIN', -5);

class User extends MY_Controller {

	public function __construct()
	{
		parent::__construct();

		if($this->router->fetch_method() != 'login' && $this->router->fetch_method() != 'logout') {
			$this->check_priv();
			if(!$this->user['isfounder'] && !$this->user['allowadminuser']) {
				$this->message('no_permission_for_this_module');
			}
		}
		$this->load->model('user_m');
	}
	
	function login() {
		$authkey = md5(UC_KEY.$_SERVER['HTTP_USER_AGENT'].$this->onlineip);
	
		$username = getgpc('username', 'P');
		$password = getgpc('password', 'P');
		$iframe	  = getgpc('iframe');
	
		$isfounder = intval(getgpc('isfounder', 'P'));
		/*
			echo $sid = $this->sid_encode('admin');
		echo $this->sid_decode($sid);
		*/
		$rand = rand(100000, 999999);
		$seccodeinit = rawurlencode(authcode($rand, 'ENCODE', $authkey, 180));
		$errorcode = 0;
		if(submitcheck()) 
		{
			$failedlogin = $this->db->where('ip', $this->onlineip)->get('failedlogins')->first_row();
			if($failedlogin->count > 4) 
			{
				if($this->time - $failedlogin->lastupdate < 15 * 60) 
				{
					$errorcode = UC_LOGIN_ERROR_FAILEDLOGIN;
				} 
				else 
				{
					$expiration = $this->time - 15 * 60;
					$this->db->delete('failedlogins', array('lastupdate<'=>$expiration));
				}
			} 
			else 
			{	
				$seccodehidden = urldecode(getgpc('seccodehidden', 'P'));
				$seccode = strtoupper(getgpc('seccode', 'P'));
				$seccodehidden = authcode($seccodehidden, 'DECODE', $authkey);
				$this->load->library('seccode');
				$this->seccode->seccodeconvert($seccodehidden);
				if(empty($seccodehidden) || $seccodehidden != $seccode) 
				{
					$errorcode = UC_LOGIN_ERROR_SECCODE;
				} 
				else 
				{
					$errorcode = UC_LOGIN_SUCCEED;
					$this->user['username'] = $username;
					if($isfounder == 1) 
					{
						$this->user['username'] = 'UCenterAdministrator';
						$md5password =  md5(md5($password).UC_FOUNDERSALT);
						if($md5password == UC_FOUNDERPW) 
						{
							$username = $this->user['username'];
							$this->view->sid = $this->sid_encode($this->user['username']);
						} 
						else 
						{
							$errorcode = UC_LOGIN_ERROR_FOUNDER_PW;
						}
					} 
					else 
					{
						$admin = $this->db->select('a.uid,m.username,m.salt,m.password')->from('admins a')->join('members m', 'a.uid=m.uid', 'LEFT')->where('a.username', $username)->get()->first_row();
						if(!empty($admin)) 
						{
							$md5password =  md5(md5($password).$admin->salt);
							if($admin->password == $md5password) 
							{
								$this->view->sid = $this->sid_encode($admin->username);
							} 
							else 
							{
								$errorcode = UC_LOGIN_ERROR_ADMIN_PW;
							}
						} else {
							$errorcode = UC_LOGIN_ERROR_ADMIN_NOT_EXISTS;
						}
					}
	
					$pwlen = strlen($password);
					if($errorcode == 0) {
						$this->setcookie('sid', $this->view->sid, 86400);
						
						$this->user['admin'] = 1;
						$this->writelog('login', 'succeed');
						if($iframe) {
							$url = $this->config->base_url('index/main?iframe=1'.($this->cookie_status ? '' : '&sid='.$this->sid));
							header('location: '.$url);
							exit;
						} else {
							$url = $this->config->base_url(($this->cookie_status ? '' : '?sid='.$this->sid));
							header('location: '.$url);
							exit;
						}
					} else {
						$this->writelog('login', 'error: user='.$this->user['username'].'; password='.($pwlen > 2 ? preg_replace("/^(.{".round($pwlen / 4)."})(.+?)(.{".round($pwlen / 6)."})$/s", "\\1***\\3", $password) : $password));
						if(empty($failedlogin)) {
							$expiration = $this->time - 15 * 60;
							$this->db->delete('failedlogins', array('lastupdate <'=>$expiration));
							$this->db->insert('failedlogins', array('ip'=>$this->onlineip, 'count'=>1, 'lastupdate'=>$this->time));
						} else {
							$this->db->set('count', 'count+1', FALSE)->update('failedlogins', array('lastupdate'=>$this->time), array('ip'=>$this->onlineip));
						}
					}
				}
			}
		}
		$username = htmlspecialchars($username);
		$password = htmlspecialchars($password);
		$data['seccodeinit'] = $seccodeinit;
		$data['username'] = $username;
		$data['password'] = $password;
		$data['isfounder'] = $isfounder;
		$data['errorcode'] = $errorcode;
		$data['iframe'] = $iframe;

		$this->load->view('login', $data);
	}
	
	function logout() {
		$this->writelog('logout');
		$this->setcookie('sid', '');
		header('location: '.$this->config->base_url());
	}
	
	function add() {
		$this->check_priv();
		if(!submitcheck('submit')) {
			exit;
		}
		$username = getgpc('addname', 'P');
		$password =  getgpc('addpassword', 'P');
		$email = getgpc('addemail', 'P');
	
		if(($status = $this->_check_username($username)) < 0) {
			if($status == UC_USER_CHECK_USERNAME_FAILED) {
				$this->message('user_add_username_ignore', 'BACK');
			} elseif($status == UC_USER_USERNAME_BADWORD) {
				$this->message('user_add_username_badwords', 'BACK');
			} elseif($status == UC_USER_USERNAME_EXISTS) {
				$this->message('user_add_username_exists', 'BACK');
			}
		}
		if(($status = $this->_check_email($email)) < 0) {
			if($status == UC_USER_EMAIL_FORMAT_ILLEGAL) {
				$this->message('user_add_email_formatinvalid', 'BACK');
			} elseif($status == UC_USER_EMAIL_ACCESS_ILLEGAL) {
				$this->message('user_add_email_ignore', 'BACK');
			} elseif($status == UC_USER_EMAIL_EXISTS) {
				$this->message('user_add_email_exists', 'BACK');
			}
		}
		$uid = $this->user_m->add_user($username, $password, $email);
		$this->message('user_add_succeed', 'admin.php?m=user&a=ls');
	}
	
	function ls() {
		$this->check_priv();
	
		$this->load->language('admin');
	
		$status = 0;
		if(!empty($_POST['addname']) && submitcheck()) {
			$this->check_priv();
			$username = getgpc('addname', 'P');
			$password =  getgpc('addpassword', 'P');
			$email = getgpc('addemail', 'P');
	
			if(($status = $this->_check_username($username)) >= 0) {
				if(($status = $this->_check_email($email)) >= 0) {
					$this->user_m->add_user($username, $password, $email);
					$status = 1;
					$this->writelog('user_add', "username=$username");
				}
			}
		}
		$data['status'] = $status;
	
		if(!empty($_POST['delete'])) {
			$this->user_m->delete_user($_POST['delete']);
			$status = 2;
			$this->writelog('user_delete', "uid=".implode(',', $_POST['delete']));
		}
		$srchname = getgpc('srchname', 'R');
		$srchregdatestart = getgpc('srchregdatestart', 'R');
		$srchregdateend = getgpc('srchregdateend', 'R');
		$srchuid = intval(getgpc('srchuid', 'R'));
		$srchregip = trim(getgpc('srchregip', 'R'));
		$srchemail = trim(getgpc('srchemail', 'R'));
	
		$data['srchname'] = $srchname;
		$data['srchuid'] = $srchuid;
		$data['srchemail'] = $srchemail;
		$data['srchregdatestart'] = $srchregdatestart;
		$data['srchregdateend'] = $srchregdateend;
		$data['srchregip'] = $srchregip;
		
		$sqladd = '';
		if($srchname) {
			$sqladd .= " AND username LIKE '$srchname%'";			
		}
		if($srchuid) {
			$sqladd .= " AND uid='$srchuid'";			
		}
		if($srchemail) {
			$sqladd .= " AND email='$srchemail'";			
		}
		if($srchregdatestart) {
			$sqladd .= " AND regdate>'".strtotime($srchregdatestart)."'";			
		}
		if($srchregdateend) {
			$sqladd .= " AND regdate<'".strtotime($srchregdateend)."'";			
		}
		if($srchregip) {
			$sqladd .= " AND regip='$srchregip'";			
		}
		$sqladd = $sqladd ? "$sqladd" : '';
	
		$num = $this->user_m->get_total_num($sqladd);
		$userlist = $this->user_m->get_list($_GET['page'], UC_PPP, $num, $sqladd);
		foreach($userlist as $key => $user) {
			$user['smallavatar'] = '<img src="avatar.php?uid='.$user['uid'].'&size=small">';
			$userlist[$key] = $user;
		}
		$multipage = page($num, UC_PPP, $_GET['page'], $this->config->base_url('user/ls?srchname='.$srchname.'&srchregdatestart='.$srchregdatestart.'&srchregdateend='.$srchregdateend));
	
		$this->_format_userlist($userlist);
		$data['userlist'] = $userlist;
		//$data['apps', $this->cache['apps']);
		$adduser = getgpc('adduser');
		$a = getgpc('a');
		$data['multipage'] = $multipage;
		$data['adduser'] = $adduser;
		$data['a'] = $a;
		$this->load->view('user', $data);
	
	}
	
	function edit() {
		$uid = getgpc('uid');
		$status = 0;
		if(!$this->user['isfounder']) {
			$isprotected = $this->db->where('uid', $uid)->get('protectedmembers')->num_rows();
			if($isprotected) {
				$this->message('user_edit_noperm');
			}
		}
	
		if(submitcheck()) {
			$username = getgpc('username', 'P');
			$newusername = getgpc('newusername', 'P');
			$password = getgpc('password', 'P');
			$email = getgpc('email', 'P');
			$delavatar = getgpc('delavatar', 'P');
			$rmrecques = getgpc('rmrecques', 'P');
			$sqladd = array();
			$this->load->model('note_m');
			if($username != $newusername) {
				if($this->user_m->get_user_by_username($newusername)) {
					$this->message('admin_user_exists');
				}
				$sqladd['username'] = $newusername;
				
				$this->note_m->add('renameuser', 'uid='.$uid.'&oldusername='.urlencode($username).'&newusername='.urlencode($newusername));
			}
			if($password) {
				$salt = substr(uniqid(rand()), 0, 6);
				$orgpassword = $password;
				$password = md5(md5($password).$salt);
				$sqladd['password'] = $password;
				$sqladd['salt'] = $salt;
				$this->note_m->add('updatepw', 'username='.urlencode($username).'&password=');
			}
			if($rmrecques) {
				$sqladd['secques'] = '';
			}
			if(!empty($delavatar)) {
				$this->user_m->delete_useravatar($uid);
			}
			$sqladd['email'] = $email;
			$update = $this->db->update('members', $sqladd, array('uid'=>$uid));
			$status = !$update ? -1 : 1;
		}
		$user = $this->db->where('uid', $uid)->get('members')->first_row('array');
		$user['bigavatar'] = '<img src="avatar.php?uid='.$uid.'&size=big">';
		$user['bigavatarreal'] = '<img src="avatar.php?uid='.$uid.'&size=big&type=real">';
		$data['uid'] = $uid;
		$data['user'] = $user;
		$data['status'] = $status;
		$this->load->view('user', $data);
	}
	
	
	function _check_username($username) {
		$username = addslashes(trim(stripslashes($username)));
		if(!$this->user_m->check_username($username)) {
			return UC_USER_CHECK_USERNAME_FAILED;
			/*		} elseif($username != $_ENV['user']->replace_badwords($username)) {
				return UC_USER_USERNAME_BADWORD;*/
		} elseif($this->user_m->check_usernameexists($username)) {
			return UC_USER_USERNAME_EXISTS;
		}
		return 1;
	}
	
	function _check_email($email) {
		if(!$this->user_m->check_emailformat($email)) {
			return UC_USER_EMAIL_FORMAT_ILLEGAL;
		} elseif(!$this->user_m->check_emailaccess($email)) {
			return UC_USER_EMAIL_ACCESS_ILLEGAL;
		} elseif($this->settings['doublee'] && $this->user_m->check_emailexists($email)) {
			return UC_USER_EMAIL_EXISTS;
		} else {
			return 1;
		}
	}
	
	function _format_userlist(&$userlist) {
		if(is_array($userlist)) {
			foreach($userlist AS $key => $user) {
				$userlist[$key]['regdate'] = $this->date($user['regdate']);
			}
		}
	}
	
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */