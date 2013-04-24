<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->check_priv();
		if(!$this->user['isfounder'] && !$this->user['allowadminapp']) {
			$this->message('no_permission_for_this_module');
		}
		$this->load->model('app_m');
		$this->load->model('misc_m');
	}
	
	function ls() {
		$status = $affectedrows = 0;
		if(!empty($_POST['delete'])) {
			$affectedrows += $_ENV['app']->delete_apps($_POST['delete']);
			foreach($_POST['delete'] as $k => $appid) {
				$_ENV['app']->alter_app_table($appid, 'REMOVE');
				unset($_POST['name'][$k]);
			}
			$this->load('cache');
			$_ENV['cache']->updatedata();
			$this->writelog('app_delete', 'appid='.implode(',', $_POST['delete']));
			$status = 2;

			$this->_add_note_for_app();
		}

		$a = getgpc('a');
		$applist = $this->app_m->get_apps();
		$data['status'] = $status;
		$data['a'] = $a;
		$data['applist'] = $applist;

		$this->load->view('app', $data);
	}

	function add() {
		if(!submitcheck()) {
			$md5ucfounderpw = md5(UC_FOUNDERPW);
			$data['md5ucfounderpw'] = $md5ucfounderpw;

			$a = getgpc('a');
			$data['a'] = $a;
			$typelist = array('UCHOME'=>'UCenter Home','XSPACE'=>'X-Space','DISCUZ'=>'Discuz!','SUPESITE'=>'SupeSite','SUPEV'=>'SupeV','ECSHOP'=>'ECShop','ECMALL'=>'ECMall','OTHER'=>$this->lang->line('other'));
			$data['typelist'] = $typelist;
			$this->load->view('app', $data);
		} else {
			$type = getgpc('type', 'P');
			$name = getgpc('name', 'P');
			$url = getgpc('url', 'P');
			$ip = getgpc('ip', 'P');
			$viewprourl = getgpc('viewprourl', 'P');
			$authkey = getgpc('authkey', 'P');
			$authkey = $this->authcode($authkey, 'ENCODE', UC_MYKEY);
			$synlogin = getgpc('synlogin', 'P');
			$recvnote = getgpc('recvnote', 'P');
			$apifilename = trim(getgpc('apifilename', 'P'));

			$tagtemplates = array();
			$tagtemplates['template'] = getgpc('tagtemplates', 'P');
			$tagfields = explode("\n", getgpc('tagfields', 'P'));
			foreach($tagfields as $field) {
				$field = trim($field);
				list($k, $v) = explode(',', $field);
				if($k) {
					$tagtemplates['fields'][$k] = $v;
				}
			}
			$tagtemplates = $this->serialize($tagtemplates, 1);

			if(!$_ENV['misc']->check_url($_POST['url'])) {
				$this->message('app_add_url_invalid', 'BACK');
			}
			if(!empty($_POST['ip']) && !$_ENV['misc']->check_ip($_POST['ip'])) {
				$this->message('app_add_ip_invalid', 'BACK');
			}
			$app = $this->db->result_first("SELECT COUNT(*) FROM ".UC_DBTABLEPRE."applications WHERE name='$name'");
			if($app) {
				$this->db->query("UPDATE ".UC_DBTABLEPRE."applications SET name='$name', url='$url', ip='$ip', viewprourl='$viewprourl', apifilename='$apifilename', authkey='$authkey', synlogin='$synlogin', type='$type', tagtemplates='$tagtemplates' WHERE appid='$app[appid]'");
				$appid = $app['appid'];
			} else {
				$extra = serialize(array('apppath'=> getgpc('apppath', 'P')));
				$this->db->query("INSERT INTO ".UC_DBTABLEPRE."applications SET name='$name', url='$url', ip='$ip', viewprourl='$viewprourl', apifilename='$apifilename', authkey='$authkey', synlogin='$synlogin', type='$type', recvnote='$recvnote', extra='$extra', tagtemplates='$tagtemplates'");
				$appid = $this->db->insert_id();
			}

			$this->_add_note_for_app();

			$this->load('cache');
			$_ENV['cache']->updatedata('apps');

			$_ENV['app']->alter_app_table($appid, 'ADD');
			$this->writelog('app_add', "appid=$appid; appname=$_POST[name]");
			header("location: admin.php?m=app&a=detail&appid=$appid&addapp=yes&sid=".$this->view->sid);
		}
	}

	function onping() {
		$ip = getgpc('ip');
		$url = getgpc('url');
		$appid = intval(getgpc('appid'));
		$app = $_ENV['app']->get_app_by_appid($appid);
		$status = '';
		if($app['extra']['apppath'] && @include $app['extra']['apppath'].'./api/'.$app['apifilename']) {
			$uc_note = new uc_note();
			$status = $uc_note->test($note['getdata'], $note['postdata']);
		} else {
			$this->load('note');
			$url = $_ENV['note']->get_url_code('test', '', $appid);
			$status = $_ENV['app']->test_api($url, $ip);
		}
		if($status == '1') {
			echo 'document.getElementById(\'status_'.$appid.'\').innerHTML = "<img src=\'images/correct.gif\' border=\'0\' class=\'statimg\' \/><span class=\'green\'>'.$this->lang['app_connent_ok'].'</span>";testlink();';
		} else {
			echo 'document.getElementById(\'status_'.$appid.'\').innerHTML = "<img src=\'images/error.gif\' border=\'0\' class=\'statimg\' \/><span class=\'red\'>'.$this->lang['app_connent_false'].'</span>";testlink();';
		}

	}

	function ondetail() {
		$appid = getgpc('appid');
		$updated = false;
		$app = $_ENV['app']->get_app_by_appid($appid);
		if($this->submitcheck()) {
			$type = getgpc('type', 'P');
			$name = getgpc('name', 'P');
			$url = getgpc('url', 'P');
			$ip = getgpc('ip', 'P');
			$viewprourl = getgpc('viewprourl', 'P');
			$apifilename = trim(getgpc('apifilename', 'P'));
			$authkey = getgpc('authkey', 'P');
			$authkey = $this->authcode($authkey, 'ENCODE', UC_MYKEY);
			$synlogin = getgpc('synlogin', 'P');
			$recvnote = getgpc('recvnote', 'P');
			if(getgpc('apppath', 'P')) {
				$app['extra']['apppath'] = $this->_realpath(getgpc('apppath', 'P'));
				if($app['extra']['apppath']) {
					$apifile = $app['extra']['apppath'].'./api/uc.php';
					if(!file_exists($apifile)) {
						$this->message('app_apifile_not_exists', 'BACK', 0, array('$apifile' => $apifile));
					}
					$s = file_get_contents($apifile);
					preg_match("/define\(\'UC_CLIENT_VERSION\'\, \'([^\']+?)\'\)/i", $s, $m);
					$uc_client_version = @$m[1];

					//�жϰ汾
					if(!$uc_client_version || $uc_client_version <= '1.0.0') {
						$this->message('app_apifile_too_low', 'BACK', 0, array('$apifile' => $apifile));
					}
				} else {
					$this->message('app_path_not_exists');
				}
			} else {
				$app['extra']['apppath'] = '';
			}

			$tagtemplates = array();
			$tagtemplates['template'] = MAGIC_QUOTES_GPC ? stripslashes(getgpc('tagtemplates', 'P')) : getgpc('tagtemplates', 'P');
			$tagfields = explode("\n", getgpc('tagfields', 'P'));
			foreach($tagfields as $field) {
				$field = trim($field);
				list($k, $v) = explode(',', $field);
				if($k) {
					$tagtemplates['fields'][$k] = $v;
				}
			}
			$tagtemplates = $this->serialize($tagtemplates, 1);

			$extra = addslashes(serialize($app['extra']));
			$this->db->query("UPDATE ".UC_DBTABLEPRE."applications SET appid='$appid', name='$name', url='$url', type='$type', ip='$ip', viewprourl='$viewprourl', apifilename='$apifilename', authkey='$authkey', synlogin='$synlogin', recvnote='$recvnote', extra='$extra', tagtemplates='$tagtemplates' WHERE appid='$appid'");
			$updated = true;
			$this->load('cache');
			$_ENV['cache']->updatedata('apps');
			$this->cache('settings');
			$this->writelog('app_edit', "appid=$appid");

			$this->_add_note_for_app();
			$app = $_ENV['app']->get_app_by_appid($appid);
		}
		$tagtemplates = $this->unserialize($app['tagtemplates']);
		$template = htmlspecialchars($tagtemplates['template']);
		$tmp = '';
		if(is_array($tagtemplates['fields'])) {
			foreach($tagtemplates['fields'] as $field => $memo) {
				$tmp .= $field.','.$memo."\n";
			}
		}
		$tagtemplates['fields'] = $tmp;
		$a = getgpc('a');
		$data['a'] = $a;
		$app = $_ENV['app']->get_app_by_appid($appid);
		$data['isfounder'] = $this->user['isfounder'];
		$data['appid'] = $app['appid'];
		$data['name'] = $app['name'];
		$data['url'] = $app['url'];
		$data['ip'] = $app['ip'];
		$data['viewprourl'] = $app['viewprourl'];
		$data['apifilename'] = $app['apifilename'];
		$data['authkey'] = $app['authkey'];
		$synloginchecked = array($app['synlogin'] => 'checked="checked"');
		$recvnotechecked = array($app['recvnote'] => 'checked="checked"');
		$data['synlogin'] = $synloginchecked;
		$data['charset'] = $app['charset'];
		$data['dbcharset'] = $app['dbcharset'];
		$data['type'] = $app['type'];
		$data['recvnotechecked'] = $recvnotechecked;
		$typelist = array('UCHOME'=>'UCenter Home','XSPACE'=>'X-Space','DISCUZ'=>'Discuz!','SUPESITE'=>'SupeSite','SUPEV'=>'SupeV','ECSHOP'=>'ECShop','ECMALL'=>'ECMall','OTHER'=>$this->lang['other']);
		$data['typelist'] = $typelist;
		$data['updated'] = $updated;
		$addapp = getgpc('addapp');
		$data['addapp'] = $addapp;
		$data['apppath'] = $app['extra']['apppath'];
		$data['tagtemplates'] = $tagtemplates;
		$this->load->view('admin_app', $data);
	}

	function _add_note_for_app() {
		$this->load('note');
		$notedata = $this->db->fetch_all("SELECT appid, type, name, url, ip, viewprourl, apifilename, charset, synlogin, extra, recvnote FROM ".UC_DBTABLEPRE."applications");
		$notedata = $this->_format_notedata($notedata);
		$notedata['UC_API'] = UC_API;
		$_ENV['note']->add('updateapps', '', $this->serialize($notedata, 1));
		$_ENV['note']->send();	
	}

	function _format_notedata($notedata) {
		$arr = array();
		foreach($notedata as $key => $note) {
			$note['extra'] = unserialize($note['extra']);
			$arr[$note['appid']] = $note;
		}
		return $arr;
	}

	function _realpath($path) {
		return realpath($path).'/';
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */