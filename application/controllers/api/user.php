<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('UC_USER_CHECK_USERNAME_FAILED', -1);
define('UC_USER_USERNAME_BADWORD', -2);
define('UC_USER_USERNAME_EXISTS', -3);
define('UC_USER_EMAIL_FORMAT_ILLEGAL', -4);
define('UC_USER_EMAIL_ACCESS_ILLEGAL', -5);
define('UC_USER_EMAIL_EXISTS', -6);

class User extends MY_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('user_m');
		$this->load->model('note_m');
		$this->load->model('misc_m');
	}
	
	function synlogin() {
		$uid = $this->input->get_post->get_post('uid');
		if($this->app['synlogin']) {
			if($this->user = $this->user_m->get_user_by_uid($uid)) {
				$synstr = '';
				foreach($this->caches['apps'] as $appid => $app) {
					if($app['synlogin'] && $app['appid'] != $this->app['appid']) {
						$synstr .= '<script type="text/javascript" src="'.$app['url'].'/api/uc.php?time='.$this->time.'&code='.urlencode($this->authcode('action=synlogin&username='.$this->user['username'].'&uid='.$this->user['uid'].'&password='.$this->user['password']."&time=".$this->time, 'ENCODE', $app['authkey'])).'" reload="1"></script>';
					}
				}
				return $synstr;
			}
		}
		return '';
	}

	function synlogout() {
		
		if($this->app['synlogin']) {
			$synstr = '';
			foreach($this->caches['apps'] as $appid => $app) {
				if($app['synlogin'] && $app['appid'] != $this->app['appid']) {
					$synstr .= '<script type="text/javascript" src="'.$app['url'].'/api/uc.php?time='.$this->time.'&code='.urlencode($this->authcode('action=synlogout&time='.$this->time, 'ENCODE', $app['authkey'])).'" reload="1"></script>';
				}
			}
			return $synstr;
		}
		return '';
	}

	function register() {
		
		$username = $this->input->get_post('username');
		$password =  $this->input->get_post('password');
		$email = $this->input->get_post('email');
		$questionid = $this->input->get_post('questionid');
		$answer = $this->input->get_post('answer');

		if(($status = $this->_check_username($username)) < 0) {
			return $status;
		}
		if(($status = $this->_check_email($email)) < 0) {
			return $status;
		}

		$uid = $this->user_m->add_user($username, $password, $email, 0, $questionid, $answer);
		return $uid;
	}

	function edit() {
		
		$username = $this->input->get_post('username');
		$oldpw = $this->input->get_post('oldpw');
		$newpw = $this->input->get_post('newpw');
		$email = $this->input->get_post('email');
		$ignoreoldpw = $this->input->get_post('ignoreoldpw');
		$questionid = $this->input->get_post('questionid');
		$answer = $this->input->get_post('answer');

		if(!$ignoreoldpw && $email && ($status = $this->_check_email($email, $username)) < 0) {
			return $status;
		}
		$status = $this->user_m->edit_user($username, $oldpw, $newpw, $email, $ignoreoldpw, $questionid, $answer);

		if($newpw && $status > 0) {
			$this->load->model('note_m');
			$this->note_m->add('updatepw', 'username='.urlencode($username).'&password=');
			$this->note_m->send();
		}
		return $status;
	}

	function login() {
		
		$isuid = $this->input->get_post('isuid');
		$username = $this->input->get_post('username');
		$password = $this->input->get_post('password');
		$checkques = $this->input->get_post('checkques');
		$questionid = $this->input->get_post('questionid');
		$answer = $this->input->get_post('answer');
		if($isuid) {
			$user = $this->user_m->get_user_by_uid($username);
		} else {
			$user = $this->user_m->get_user_by_username($username);
		}

		$passwordmd5 = preg_match('/^\w{32}$/', $password) ? $password : md5($password);
		if(empty($user)) {
			$status = -1;
		} elseif($user['password'] != md5($passwordmd5.$user['salt'])) {
			$status = -2;
		} elseif($checkques && $user['secques'] != '' && $user['secques'] != $this->user_m->quescrypt($questionid, $answer)) {
			$status = -3;
		} else {
			$status = $user['uid'];
		}
		$merge = $status != -1 && !$isuid && $this->user_m->check_mergeuser($username) ? 1 : 0;
		return array($status, $user['username'], $password, $user['email'], $merge);
	}

	function check_email() {
		
		$email = $this->input->get_post('email');
		return $this->_check_email($email);
	}

	function check_username() {
		
		$username = $this->input->get_post('username');
		if(($status = $this->_check_username($username)) < 0) {
			return $status;
		} else {
			return 1;
		}
	}

	function get_user() {
		
		$username = $this->input->get_post('username');
		if(!$this->input->get_post('isuid')) {
			$status = $this->user_m->get_user_by_username($username);
		} else {
			$status = $this->user_m->get_user_by_uid($username);
		}
		if($status) {
			return array($status['uid'],$status['username'],$status['email']);
		} else {
			return 0;
		}
	}


	function getprotected() {
		$protectedmembers = $this->db->select('uid,username')->group_by('username')->get('protectedmembers')->result_array();
		return $protectedmembers;
	}

	function delete() {
		
		$uid = $this->input->get_post('uid');
		return $this->user_m->delete_user($uid);
	}

	function deleteavatar() {
		
		$uid = $this->input->get_post('uid');
		$this->user_m->delete_useravatar($uid);
	}

	function addprotected() {
		
		$username = $this->input->get_post('username');
		$admin = $this->input->get_post('admin');
		$appid = $this->app['appid'];
		$usernames = (array)$username;
		foreach($usernames as $username) {
			$user = $this->user_m->get_user_by_username($username);
			$uid = $user['uid'];
			$this->db->replace('protectedmembers', array('uid'=>$uid, 'username'=>$username, 'appid'=>$appid, 'dateline'=>time(), 'admin'=>$admin));
		}
		return $this->db->errno() ? -1 : 1;
	}

	function deleteprotected() {
		
		$username = $this->input->get_post('username');
		$appid = $this->app['appid'];
		$usernames = (array)$username;
		foreach($usernames as $username) {
			$this->db->delete('protectedmembers', array('username'=>$username, 'appid'=>$appid));
		}
		return $this->db->errno() ? -1 : 1;
	}

	function merge() {
		
		$oldusername = $this->input->get_post('oldusername');
		$newusername = $this->input->get_post('newusername');
		$uid = $this->input->get_post('uid');
		$password = $this->input->get_post('password');
		$email = $this->input->get_post('email');
		if(($status = $this->_check_username($newusername)) < 0) {
			return $status;
		}
		$uid = $this->user_m->add_user($newusername, $password, $email, $uid);
		$this->db->update('pms', array('msgfrom'=>$newusername), array('msgfromid'=>$uid, 'msgfrom'=>$oldusername));
		$this->db->delete('mergemembers', array('appid'=>$this->app['appid'], 'username'=>$oldusername));
		return $uid;
	}

	function merge_remove() {
		
		$username = $this->input->get_post('username');
		$this->db->delete('mergemembers', array('appid'=>$this->app['appid'], 'username'=>$username));
		return NULL;
	}

	function _check_username($username) {
		$username = addslashes(trim(stripslashes($username)));
		if(!$this->user_m->check_username($username)) {
			return UC_USER_CHECK_USERNAME_FAILED;
		} elseif(!$this->user_m->check_usernamecensor($username)) {
			return UC_USER_USERNAME_BADWORD;
		} elseif($this->user_m->check_usernameexists($username)) {
			return UC_USER_USERNAME_EXISTS;
		}
		return 1;
	}

	function _check_email($email, $username = '') {
		if(!$this->user_m->check_emailformat($email)) {
			return UC_USER_EMAIL_FORMAT_ILLEGAL;
		} elseif(!$this->user_m->check_emailaccess($email)) {
			return UC_USER_EMAIL_ACCESS_ILLEGAL;
		} elseif(!$this->settings['doublee'] && $this->user_m->check_emailexists($email, $username)) {
			return UC_USER_EMAIL_EXISTS;
		} else {
			return 1;
		}
	}

	function getcredit($arr) {
		
		$appid = $this->input->get_post('appid');
		$uid = $this->input->get_post('uid');
		$credit = $this->input->get_post('credit');
		$app = $this->caches['apps'][$appid];
		$apifilename = isset($app['apifilename']) && $app['apifilename'] ? $app['apifilename'] : 'uc.php';
		if($app['extra']['apppath'] && @include $app['extra']['apppath'].'./api/'.$apifilename) {
			$uc_note = new uc_note();
			return $uc_note->getcredit(array('uid' => $uid, 'credit' => $credit), '');
		} else {
			$url = $this->note_m->get_url_code('getcredit', "uid=$uid&credit=$credit", $appid);
			return $this->misc_m->dfopen($url, 0, '', '', 1, $app['ip'], UC_NOTE_TIMEOUT);
		}
	}


	function uploadavatar() {
		@header("Expires: 0");
		@header("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
		@header("Pragma: no-cache");
		//header("Content-type: application/xml; charset=utf-8");
		$this->init_input(getgpc('agent', 'G'));

		$uid = $this->input->get_post('uid');
		if(empty($uid)) {
			return -1;
		}
		if(empty($_FILES['Filedata'])) {
			return -3;
		}

		$file = @$_FILES['Filedata']['tmp_name'];
		$filetype = '.jpg';
		$tmpavatar = UC_DATADIR.'./tmp/upload'.$uid.$filetype;
		file_exists($tmpavatar) && @unlink($tmpavatar);
		if(@copy($_FILES['Filedata']['tmp_name'], $tmpavatar) || @move_uploaded_file($_FILES['Filedata']['tmp_name'], $tmpavatar)) {
			@unlink($_FILES['Filedata']['tmp_name']);
			list($width, $height, $type, $attr) = getimagesize($tmpavatar);
			if($width < 10 || $height < 10 || $width > 3000 || $height > 3000 || $type == 4) {
				@unlink($tmpavatar);
				return -2;
			}
		} else {
			@unlink($_FILES['Filedata']['tmp_name']);
			return -4;
		}
		$avatarurl = UC_DATAURL.'/tmp/upload'.$uid.$filetype;
		return $avatarurl;
	}

	function rectavatar() {
		@header("Expires: 0");
		@header("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
		@header("Pragma: no-cache");
		header("Content-type: application/xml; charset=utf-8");
		$this->init_input(getgpc('agent'));
		$uid = $this->input->get_post('uid');
		if(empty($uid)) {
			return '<root><message type="error" value="-1" /></root>';
		}
		$home = $this->get_home($uid);
		if(!is_dir(UC_DATADIR.'./avatar/'.$home)) {
			$this->set_home($uid, UC_DATADIR.'./avatar/');
		}
		$avatartype = getgpc('avatartype', 'G') == 'real' ? 'real' : 'virtual';
		$bigavatarfile = UC_DATADIR.'./avatar/'.$this->get_avatar($uid, 'big', $avatartype);
		$middleavatarfile = UC_DATADIR.'./avatar/'.$this->get_avatar($uid, 'middle', $avatartype);
		$smallavatarfile = UC_DATADIR.'./avatar/'.$this->get_avatar($uid, 'small', $avatartype);
		$bigavatar = $this->flashdata_decode(getgpc('avatar1', 'P'));
		$middleavatar = $this->flashdata_decode(getgpc('avatar2', 'P'));
		$smallavatar = $this->flashdata_decode(getgpc('avatar3', 'P'));
		if(!$bigavatar || !$middleavatar || !$smallavatar) {
			return '<root><message type="error" value="-2" /></root>';
		}

		$success = 1;
		$fp = @fopen($bigavatarfile, 'wb');
		@fwrite($fp, $bigavatar);
		@fclose($fp);

		$fp = @fopen($middleavatarfile, 'wb');
		@fwrite($fp, $middleavatar);
		@fclose($fp);

		$fp = @fopen($smallavatarfile, 'wb');
		@fwrite($fp, $smallavatar);
		@fclose($fp);

		$biginfo = @getimagesize($bigavatarfile);
		$middleinfo = @getimagesize($middleavatarfile);
		$smallinfo = @getimagesize($smallavatarfile);
		if(!$biginfo || !$middleinfo || !$smallinfo || $biginfo[2] == 4 || $middleinfo[2] == 4 || $smallinfo[2] == 4) {
			file_exists($bigavatarfile) && unlink($bigavatarfile);
			file_exists($middleavatarfile) && unlink($middleavatarfile);
			file_exists($smallavatarfile) && unlink($smallavatarfile);
			$success = 0;
		}

		$filetype = '.jpg';
		@unlink(UC_DATADIR.'./tmp/upload'.$uid.$filetype);

		if($success) {
			return '<?xml version="1.0" ?><root><face success="1"/></root>';
		} else {
			return '<?xml version="1.0" ?><root><face success="0"/></root>';
		}
	}


	function flashdata_decode($s) {
		$r = '';
		$l = strlen($s);
		for($i=0; $i<$l; $i=$i+2) {
			$k1 = ord($s[$i]) - 48;
			$k1 -= $k1 > 9 ? 7 : 0;
			$k2 = ord($s[$i+1]) - 48;
			$k2 -= $k2 > 9 ? 7 : 0;
			$r .= chr($k1 << 4 | $k2);
		}
		return $r;
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */