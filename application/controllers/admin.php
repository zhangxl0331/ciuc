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

		$this->load->language('admin');
		$status = 0;
		if($this->input->post('addname') && submitcheck()) {
			$addname = $this->input->post('addname');
			$data['addname'] = $addname;
			$uid = $this->db->select('uid')->where('username', $addname)->get('members')->first_row()->uid;
			if($uid) {
				$adminuid = $this->db->select('uid')->where('username', $addname)->get('admins')->first_row('array');
				if($adminuid) {
					$status = -1;
				} else {
					$allowadminsetting = $this->input->post('allowadminsetting');
					$allowadminapp = $this->input->post('allowadminapp');
					$allowadminuser = $this->input->post('allowadminuser');
					$allowadminbadword = $this->input->post('allowadminbadword');
					$allowadmincredits = $this->input->post('allowadmincredits');
					$allowadmintag = $this->input->post('allowadmintag');
					$allowadminpm = $this->input->post('allowadminpm');
					$allowadmindomain = $this->input->post('allowadmindomain');
					$allowadmindb = $this->input->post('allowadmindb');
					$allowadminnote = $this->input->post('allowadminnote');
					$allowadmincache = $this->input->post('allowadmincache');
					$allowadminlog = $this->input->post('allowadminlog');
					$insertid = $this->db->insert('admins', array(
						'uid'=>$uid,
						'username'=>$addname,
						'allowadminsetting'=>$allowadminsetting,
						'allowadminapp'=>$allowadminapp,
						'allowadminuser'=>$allowadminuser,
						'allowadminbadword'=>$allowadminbadword,
						'allowadmincredits'=>$allowadmincredits,
						'allowadmintag'=>$allowadmintag,
						'allowadminpm'=>$allowadminpm,
						'allowadmindomain'=>$allowadmindomain,
						'allowadmindb'=>$allowadmindb,
						'allowadminnote'=>$allowadminnote,
						'allowadmincache'=>$allowadmincache,
						'allowadminlog'=>$allowadminlog
						)
					);
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

		if($this->input->post('editpwsubmit') && submitcheck()) {
			$oldpw = $this->input->post('oldpw');
			$newpw = $this->input->post('newpw');
			$newpw2 = $this->input->post('newpw2');
			if(UC_FOUNDERPW == md5(md5($oldpw).UC_FOUNDERSALT)) {
				$configfile = APPPATH.'config/constants.php';
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

		if($this->input->post('delete')) {
			$uids = $this->input->post('delete');
			$this->db->where_in('uid', $uids)->delete('admins');
		}

		$page = max(1, $this->input->get_post('page'));
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

		$data['multipage'] = $multipage;
		$data['userlist'] = $userlist;
		$this->load->view('admin', $data);

	}

	function edit() {
		$uid = $this->input->get_post('uid');
		$status = 0;
		$admin = $this->db->where('uid', $uid)->get('admins')->first_row('array');
		if(submitcheck()) {
			$allowadminsetting = $this->input->post('allowadminsetting');
			$allowadminapp = $this->input->post('allowadminapp');
			$allowadminuser = $this->input->post('allowadminuser');
			$allowadminbadword = $this->input->post('allowadminbadword');
			$allowadmintag = $this->input->post('allowadmintag');
			$allowadminpm = $this->input->post('allowadminpm');
			$allowadmincredits = $this->input->post('allowadmincredits');
			$allowadmindomain = $this->input->post('allowadmindomain');
			$allowadmindb = $this->input->post('allowadmindb');
			$allowadminnote = $this->input->post('allowadminnote');
			$allowadmincache = $this->input->post('allowadmincache');
			$allowadminlog = $this->input->post('allowadminlog');
			$update = $this->db->update('admins', array(
				'allowadminsetting'=>$allowadminsetting,
				'allowadminapp'=>$allowadminapp,
				'allowadminuser'=>$allowadminuser,
				'allowadminbadword'=>$allowadminbadword,
				'allowadmincredits'=>$allowadmincredits,
				'allowadmintag'=>$allowadmintag,
				'allowadminpm'=>$allowadminpm,
				'allowadmindomain'=>$allowadmindomain,
				'allowadmindb'=>$allowadmindb,
				'allowadminnote'=>$allowadminnote,
				'allowadmincache'=>$allowadmincache,
				'allowadminlog'=>$allowadminlog),
				array('uid'=>$uid));
			$status = !$update ? -1 : 1;
			$this->writelog('admin_priv_edit', 'username='.htmlspecialchars($admin['username']));
		}
		
		$data['uid'] = $uid;
		$data['admin'] = $admin;
		$data['status'] = $status;
		$this->load->view('admin', $data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */