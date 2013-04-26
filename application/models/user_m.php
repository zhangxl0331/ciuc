<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_m extends CI_Model
{
	var $settings = array();
	public function __construct()
	{
		parent::__construct();
		$this->load->model('cache_m');
		$this->settings = $this->cache_m->getdata('settings');
	}

	function get_user_by_uid($uid) {
		$arr = $this->db->where('uid', $uid)->get('members')->first_row('array');
		return $arr;
	}

	function get_user_by_username($username) {
		$arr = $this->db->where('username', $username)->get('members')->first_row('array');
		return $arr;
	}

	function check_username($username) {
		$guestexp = '\xA1\xA1|\xAC\xA3|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8';
		$len = strlen($username);
		if($len > 15 || $len < 3 || preg_match("/\s+|^c:\\con\\con|[%,\*\"\s\<\>\&]|$guestexp/is", $username)) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function check_mergeuser($username) {
		$data = $this->db->where(array('appid'=>$this->base->app['appid'], 'username'=>$username))->get('mergemembers')->num_rows();
		return $data;
	}

	function check_usernamecensor($username) {
		$_CACHE['badwords'] = $this->base->cache('badwords');
		$censorusername = $this->base->get_setting('censorusername');
		$censorusername = $censorusername['censorusername'];
		$censorexp = '/^('.str_replace(array('\\*', "\r\n", ' '), array('.*', '|', ''), preg_quote(($censorusername = trim($censorusername)), '/')).')$/i';
		$usernamereplaced = isset($_CACHE['badwords']['findpattern']) && !empty($_CACHE['badwords']['findpattern']) ? @preg_replace($_CACHE['badwords']['findpattern'], $_CACHE['badwords']['replace'], $username) : $username;
		if(($usernamereplaced != $username) || ($censorusername && preg_match($censorexp, $username))) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function check_usernameexists($username) {
		$data = $this->db->select('username')->where('username', $username)->get('members')->first_row('array');
		return $data;
	}

	function check_emailformat($email) {
		return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
	}

	function check_emailaccess($email) {
		$setting = $this->settings;
		$accessemail = $setting['accessemail'];
		$censoremail = $setting['censoremail'];
		$accessexp = '/('.str_replace("\r\n", '|', preg_quote(trim($accessemail), '/')).')$/i';
		$censorexp = '/('.str_replace("\r\n", '|', preg_quote(trim($censoremail), '/')).')$/i';
		if($accessemail || $censoremail) {
			if(($accessemail && !preg_match($accessexp, $email)) || ($censoremail && preg_match($censorexp, $email))) {
				return FALSE;
			} else {
				return TRUE;
			}
		} else {
			return TRUE;
		}
	}

	function check_emailexists($email, $username = '') {
		$sqladd = $username !== '' ? "AND username<>'$username'" : '';
		if($username !== '')
		{
			$email = $this->db->select('email')->where(array('email'=>$email, 'username'=>$username))->get('members')->first_row();
		}
		else 
		{
			$email = $this->db->select('email')->where(array('email'=>$email))->get('members')->first_row();
		}
		
		return $email;
	}

	function check_login($username, $password, &$user) {
		$user = $this->get_user_by_username($username);
		if(empty($user['username'])) {
			return -1;
		} elseif($user['password'] != md5(md5($password).$user['salt'])) {
			return -2;
		}
		return $user['uid'];
	}

	function add_user($username, $password, $email, $uid = 0, $questionid = '', $answer = '') {
		$salt = substr(uniqid(rand()), -6);
		$password = md5(md5($password).$salt);
		$update = array();
		$uid && $update['uid'] = intval($uid);
		$update['secques'] = $questionid > 0 ? $this->quescrypt($questionid, $answer) : '';
		$update['username'] = $username;
		$update['password'] = $password;
		$update['email'] = $email;
		$update['regip'] = $this->input->ip_address();
		$update['regdate'] = time();
		$update['salt'] = $salt;
		$this->db->insert('members', $update);
		$uid = $this->db->insert_id();
		$this->db->insert('memberfields', array('uid'=>$uid));
		return $uid;
	}

	function edit_user($username, $oldpw, $newpw, $email, $ignoreoldpw = 0, $questionid = '', $answer = '') {
		$data = $this->db->select('username, uid, password, salt')->where('username', $username)->get('members')->first_row('array');

		if($ignoreoldpw) {
			$isprotected = $this->db->where('uid', $data[uid])->get('protectedmembers')->num_rows();
			if($isprotected) {
				return -8;
			}
		}

		if(!$ignoreoldpw && $data['password'] != md5(md5($oldpw).$data['salt'])) {
			return -1;
		}

		$update = array();
		$update['password'] = $newpw ? md5(md5($newpw).$data['salt']) : '';
		$update['email'] = $email ? $email : '';
		if($questionid !== '') {
			if($questionid > 0) {
				$update['secques'] = $this->quescrypt($questionid, $answer);
			} else {
				$update['secques'] = '';
			}
		}
		if($update || $emailadd) {
			return $this->db->update('members', $update, array('username'=>$username));
		} else {
			return -7;
		}
	}

	function delete_user($uidsarr) {
		$uidsarr = (array)$uidsarr;
		$arr = $this->db->select('uid')->where_in('uid', $uidsarr)->get('protectedmembers')->result_array();
		$puids = array();
		foreach((array)$arr as $member) {
			$puids[] = $member['uid'];
		}
		$uids = $this->base->implode(array_diff($uidsarr, $puids));
		if($uids) {
			$this->db->delete('members', array('uid IN'=>$uids));
			$this->db->query('memberfields', array('uid IN'=>$uids));
			$this->delete_useravatar($uidsarr);
			$this->load->model('note_m');
			return $this->note_m->add('deleteuser', "ids=$uids");
		} else {
			return 0;
		}
	}

	function delete_useravatar($uidsarr) {
		$uidsarr = (array)$uidsarr;
		foreach((array)$uidsarr as $uid) {
			file_exists($avatar_file = FCPATH.'data/avatar/'.get_avatar($uid, 'big', 'real')) && unlink($avatar_file);
			file_exists($avatar_file = FCPATH.'data/avatar/'.get_avatar($uid, 'middle', 'real')) && unlink($avatar_file);
			file_exists($avatar_file = FCPATH.'data/avatar/'.get_avatar($uid, 'small', 'real')) && unlink($avatar_file);
			file_exists($avatar_file = FCPATH.'data/avatar/'.get_avatar($uid, 'big')) && unlink($avatar_file);
			file_exists($avatar_file = FCPATH.'data/avatar/'.get_avatar($uid, 'middle')) && unlink($avatar_file);
			file_exists($avatar_file = FCPATH.'data/avatar/'.get_avatar($uid, 'small')) && unlink($avatar_file);
		}		
	}
	
	function get_total_num($sqladd = '', $like=array()) {
		if($sqladd)
		{
			if($like)
			{
				$data = $this->db->like($like)->where($sqladd)->get('members')->num_rows();
			}
			else 
			{
				$data = $this->db->where($sqladd)->get('members')->num_rows();
			}
		}
		else 
		{
			if($like)
			{
				$data = $this->db->like($like)->get('members')->num_rows();
			}
			else
			{
				$data = $this->db->get('members')->num_rows();
			}
			
		}
		
		return $data;
	}

	function get_list($page, $ppp, $totalnum, $sqladd, $like=array()) {
		$start = page_get_start($page, $ppp, $totalnum);
		if($sqladd)
		{
			if($like)
			{
				$data = $this->db->like($like)->where($sqladd)->get('members', $ppp, $start)->result_array();
			}
			else
			{
				$data = $this->db->where($sqladd)->get('members', $ppp, $start)->result_array();
			}			
		}
		else 
		{
			if($like)
			{
				$data = $this->db->like($like)->get('members', $ppp, $start)->result_array();
			}
			else
			{
				$data = $this->db->get('members', $ppp, $start)->result_array();
			}
			
		}
		
		return $data;
	}

	function name2id($usernamesarr) {
		$usernamesarr = daddslashes($usernamesarr, 1, TRUE);
		$users = $this->db->select('uid')->where_in('username', $usernamesarr)->get('members')->result_array();
		$arr = array();
		foreach($users as $user) {
			$arr[] = $user['uid'];
		}
		return $arr;
	}

	function quescrypt($questionid, $answer) {
		return $questionid > 0 && $answer != '' ? substr(md5($answer.md5($questionid)), 16, 8) : '';
	}

}