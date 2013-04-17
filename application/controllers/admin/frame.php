<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Frame extends MY_Controller {

	var $members;
	var $apps;
	var $friends;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	function onindex() {
		$this->view->assign('sid', $this->view->sid);
		$mainurl = getgpc('mainurl');
		$mainurl = !empty($mainurl) && preg_match("/^admin\.php\?(&*\w+=\w+)*$/i", $mainurl) ? $mainurl : 'admin.php?m=frame&a=main&sid='.$this->view->sid;
		$this->view->assign('mainurl', $mainurl);
		$this->view->display('admin_frame_index');
	}

	function main() {
		$ucinfo = '<sc'.'ript language="Jav'.'aScript" src="ht'.'tp:/'.'/cus'.'tome'.'r.disc'.'uz.n'.'et/ucn'.'ews'.'.p'.'hp?'.$this->_get_uc_info().'"></s'.'cri'.'pt>';
		$data['ucinfo'] = $ucinfo;

		$members = $this->_get_uc_members();
		$applist = $this->_get_uc_apps();
		$notes = $this->_get_uc_notes();
		$errornotes = $this->_get_uc_errornotes($applist);
		$pms = $this->_get_uc_pms();
		$apps = count($applist);
		$friends = $this->_get_uc_friends();
		$data['members'] = $members;
		$data['applist'] = $applist;
		$data['apps'] = $apps;
		$data['friends'] = $friends;
		$data['notes'] = $notes;
		$data['errornotes'] = $errornotes;
		$data['pms'] = $pms;
		$data['iframe'] = getgpc('iframe', 'G');

		$serverinfo = PHP_OS.' / PHP v'.PHP_VERSION;
		$serverinfo .= @ini_get('safe_mode') ? ' Safe Mode' : NULL;
		$dbversion = $this->db->query("SELECT VERSION()")->row();
		$fileupload = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : '<font color="red">'.$lang['no'].'</font>';
		$dbsize = 0;
		$tablepre = $this->db->dbprefix;
		$query = $tables = $this->db->query("SHOW TABLE STATUS LIKE '$tablepre%'")->result();
		foreach($tables as $table) {
			$dbsize += $table->Data_length + $table->Index_length;
		}
		$dbsize = $dbsize ? $this->_sizecount($dbsize) : $lang['unknown'];
		$dbversion = $this->db->version();
		$magic_quote_gpc = get_magic_quotes_gpc() ? 'On' : 'Off';
		$allow_url_fopen = ini_get('allow_url_fopen') ? 'On' : 'Off';
		$data['serverinfo'] = $serverinfo;
		$data['fileupload'] = $fileupload;
		$data['dbsize'] = $dbsize;
		$data['dbversion'] = $dbversion;
		$data['magic_quote_gpc'] = $magic_quote_gpc;
		$data['allow_url_fopen'] = $allow_url_fopen;

		$this->load->view('admin/frame_main', $data);
	}

	function onmenu() {
		$this->view->display('admin_frame_menu');
	}

	function onheader() {
		$this->load('app');
		$applist = $_ENV['app']->get_apps();
		$cparray = array(
			'UCHOME' => 'admincp.php',
			'DISCUZ' => 'admincp.php',
			'SUPESITE' => 'admincp.php',
			'XSPACE' => 'admincp.php',
			'SUPEV' => 'admincp.php',
			'ECSHOP' => 'admin/index.php',
			'ECMALL' => 'admin.php'
		);
		$admincp = '';
		if(is_array($applist)) {
			foreach($applist AS $k => $app) {
				if(isset($cparray[$app['type']])) {
					$admincp .= '<li><a href="'.(substr($app['url'], -1) == '/' ? $app['url'] : $app['url'].'/').$cparray[$app['type']].'" target="_blank">'.$app['name'].'</a></li>';
				}
			}
		}
		$this->view->assign('admincp', $admincp);
		$this->view->assign('username', $this->user['username']);
		$this->view->display('admin_frame_header');
	}

	function _get_uc_members() {
		if(!$this->members) {
			$this->members = $this->db->count_all_results('members');
		}
		return $this->members;
	}

	function _get_uc_friends() {
		$friends = $this->db->count_all_results('friends');
		return $friends;
	}

	function _get_uc_apps() {
		if(!$this->apps) {
			$this->apps = $this->db->get('applications')->result_array();
		}
		return $this->apps;
	}
	function _get_uc_pms() {
		$pms = $this->db->count_all_results('pms');
		return $pms;
	}

	function _get_uc_notes() {
		$notes = $this->db->where('closed', 0)->get('notelist')->num_rows();
		return $notes;
	}
	
	function _get_uc_errornotes($applist) {
		$notelist = $this->db->order_by('dateline', 'DESC')->get('notelist', 20)->result();
		$error = array();
		foreach($notelist as $note) {
			foreach($applist as $k => $app) {
				if($note->{'app'.$app['appid']} < 0) {
					$error[$k]++;
				}
			}
		}
		return $error;
	}

	function _sizecount($filesize) {
		if($filesize >= 1073741824) {
			$filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
		} elseif($filesize >= 1048576) {
			$filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
		} elseif($filesize >= 1024) {
			$filesize = round($filesize / 1024 * 100) / 100 . ' KB';
		} else {
			$filesize = $filesize . ' Bytes';
		}
		return $filesize;
	}

	function _get_uc_info() {
		$update = array('uniqueid' => UC_SITEID, 'version' => UC_SERVER_VERSION, 'release' => UC_SERVER_RELEASE, 'php' => PHP_VERSION, 'mysql' => $this->db->version(), 'charset' => CHARSET);
		$updatetime = @filemtime(UC_ROOT.'./data/updatetime.lock');
		if(empty($updatetime) || ($this->time - $updatetime > 3600 * 4)) {
			@touch(UC_ROOT.'./data/updatetime.lock');
			$update['members'] = $this->_get_uc_members();
			$update['friends'] = $this->_get_uc_friends();
			$apps = $this->_get_uc_apps();
			if($apps) {
				foreach($apps as $app) {
					$update['app_'.$app['appid']] = $app['name']."\t".$app['url']."\t".$app['type'];
				}
			}
		}

		$data = '';
		foreach($update as $key => $value) {
			$data .= $key.'='.rawurlencode($value).'&';
		}

		return 'update='.rawurlencode(base64_encode($data)).'&md5hash='.substr(md5($_SERVER['HTTP_USER_AGENT'].implode('', $update).$this->time), 8, 8).'&timestamp='.$this->time;
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */