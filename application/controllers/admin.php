<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_m');
		$this->check_priv();
		if(!$this->user['isfounder'] && !$this->user['allowadminbadword']) {
			$this->message('no_permission_for_this_module');
		}
	}
	
	function ls() {

		//include_once UC_ROOT.'view/default/admin.lang.php';
		$status = 0;
		if(!empty($_POST['addname']) && $this->submitcheck()) {
			$addname = getgpc('addname', 'P');
			$data['addname'] = $addname;
			$uid = $this->db->result_first("SELECT uid FROM ".UC_DBTABLEPRE."members WHERE username='$addname'");
			if($uid) {
				$adminuid = $this->db->result_first("SELECT uid FROM ".UC_DBTABLEPRE."admins WHERE username='$addname'");
				if($adminuid) {
					$status = -1;
				} else {
					$allowadminsetting = getgpc('allowadminsetting', 'P');
					$allowadminapp = getgpc('allowadminapp', 'P');
					$allowadminuser = getgpc('allowadminuser', 'P');
					$allowadminbadword = getgpc('allowadminbadword', 'P');
					$allowadmincredits = getgpc('allowadmincredits', 'P');
					$allowadmintag = getgpc('allowadmintag', 'P');
					$allowadminpm = getgpc('allowadminpm', 'P');
					$allowadmindomain = getgpc('allowadmindomain', 'P');
					$allowadmindb = getgpc('allowadmindb', 'P');
					$allowadminnote = getgpc('allowadminnote', 'P');
					$allowadmincache = getgpc('allowadmincache', 'P');
					$allowadminlog = getgpc('allowadminlog', 'P');
					$this->db->query("INSERT INTO ".UC_DBTABLEPRE."admins SET
						uid='$uid',
						username='$addname',
						allowadminsetting='$allowadminsetting',
						allowadminapp='$allowadminapp',
						allowadminuser='$allowadminuser',
						allowadminbadword='$allowadminbadword',
						allowadmincredits='$allowadmincredits',
						allowadmintag='$allowadmintag',
						allowadminpm='$allowadminpm',
						allowadmindomain='$allowadmindomain',
						allowadmindb='$allowadmindb',
						allowadminnote='$allowadminnote',
						allowadmincache='$allowadmincache',
						allowadminlog='$allowadminlog'");
					$insertid = $this->db->insert_id();
					if($insertid) {
						$this->writelog('admin_add', 'username='.htmlspecialchars($addname));
						$status = 1;
					} else {
						$status = -2;
					}
				}
			} else {
				$status = -3;
			}
		}

		if(!empty($_POST['editpwsubmit']) && $this->submitcheck()) {
			$oldpw = getgpc('oldpw', 'P');
			$newpw = getgpc('newpw', 'P');
			$newpw2 = getgpc('newpw2', 'P');
			if(UC_FOUNDERPW == md5(md5($oldpw).UC_FOUNDERSALT)) {
				$configfile = UC_ROOT.'./data/config.inc.php';
				if(!is_writable($configfile)) {
					$status = -4;
				} else {
					if($newpw != $newpw2) {
						$status = -6;
					} else {
						$config = file_get_contents($configfile);
						$salt = substr(uniqid(rand()), 0, 6);
						$md5newpw = md5(md5($newpw).$salt);
						$config = preg_replace("/define\('UC_FOUNDERSALT',\s*'.*?'\);/i", "define('UC_FOUNDERSALT', '$salt');", $config);
						$config = preg_replace("/define\('UC_FOUNDERPW',\s*'.*?'\);/i", "define('UC_FOUNDERPW', '$md5newpw');", $config);
						$fp = @fopen($configfile, 'w');
						@fwrite($fp, $config);
						@fclose($fp);
						$status = 2;
						$this->writelog('admin_pw_edit');
					}
				}
			} else {
				$status = -5;
			}
		}

		$data['status'] = $status;

		if(!empty($_POST['delete'])) {
			$uids = $this->implode(getgpc('delete', 'P'));
			$this->db->query("DELETE FROM ".UC_DBTABLEPRE."admins WHERE uid IN ($uids)");
		}

		$page = max(1, getgpc('page'));
		$ppp  = 15;
		$totalnum = $this->db->get('admins')->num_rows();
		$start = page_get_start($page, $ppp, $totalnum);
		$userlist = $this->db->select('a.*,m.*')->from('admins a')->join('members m', 'a.uid=m.uid', 'LEFT')->get('', $ppp, $start)->result_array();
		$multipage = page($totalnum, $ppp, $page, $this->config->base_url('admin/admin'));
		if($userlist) {
			foreach($userlist as $key => $user) {
				$user['regdate'] = $this->date($user['regdate']);
				$userlist[$key] = $user;
			}
		}

		$a = getgpc('a');
		$data['a'] = $a;
		$data['multipage'] = $multipage;
		$data['userlist'] = $userlist;
		$this->load->view('admin', $data);

	}

	function edit() {
		$uid = getgpc('uid');
		$status = 0;
		if($this->submitcheck()) {
			$allowadminsetting = getgpc('allowadminsetting', 'P');
			$allowadminapp = getgpc('allowadminapp', 'P');
			$allowadminuser = getgpc('allowadminuser', 'P');
			$allowadminbadword = getgpc('allowadminbadword', 'P');
			$allowadmintag = getgpc('allowadmintag', 'P');
			$allowadminpm = getgpc('allowadminpm', 'P');
			$allowadmincredits = getgpc('allowadmincredits', 'P');
			$allowadmindomain = getgpc('allowadmindomain', 'P');
			$allowadmindb = getgpc('allowadmindb', 'P');
			$allowadminnote = getgpc('allowadminnote', 'P');
			$allowadmincache = getgpc('allowadmincache', 'P');
			$allowadminlog = getgpc('allowadminlog', 'P');
			$this->db->query("UPDATE ".UC_DBTABLEPRE."admins SET
				allowadminsetting='$allowadminsetting',
				allowadminapp='$allowadminapp',
				allowadminuser='$allowadminuser',
				allowadminbadword='$allowadminbadword',
				allowadmincredits='$allowadmincredits',
				allowadmintag='$allowadmintag',
				allowadminpm='$allowadminpm',
				allowadmindomain='$allowadmindomain',
				allowadmindb='$allowadmindb',
				allowadminnote='$allowadminnote',
				allowadmincache='$allowadmincache',
				allowadminlog='$allowadminlog'
				WHERE uid='$uid'");
			$status = $this->db->errno() ? -1 : 1;
			$this->writelog('admin_priv_edit', 'username='.htmlspecialchars($admin));
		}
		$admin = $this->db->fetch_first("SELECT * FROM ".UC_DBTABLEPRE."admins WHERE uid='$uid'");
		$data['uid'] = $uid;
		$data['admin'] = $admin;
		$data['status'] = $status;
		$this->load->view('admin_admin', $data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */