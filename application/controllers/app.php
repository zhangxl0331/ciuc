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
		$this->load->model('cache_m');
	}
	
	function ls() {
		$status = $affectedrows = 0;
		if(!empty($_POST['delete'])) {
			$affectedrows += $this->app_m->delete_apps($_POST['delete']);
			foreach($_POST['delete'] as $k => $appid) {
				$this->app_m->alter_app_table($appid, 'REMOVE');
				unset($_POST['name'][$k]);
			}
			$this->cache_m->updatedata();
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
			$authkey = authcode($authkey, 'ENCODE', UC_MYKEY);
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

			if(!$this->misc_m->check_url($_POST['url'])) {
				$this->message('app_add_url_invalid', 'BACK');
			}
			if(!empty($_POST['ip']) && !$this->misc_m->check_ip($_POST['ip'])) {
				$this->message('app_add_ip_invalid', 'BACK');
			}
			$app = $this->db->where('name', $name)->get('applications')->num_rows();
			if($app) {
				$this->db->update('applications', array('name'=>$name, 'url'=>$url, 'ip'=>$ip, 'viewprourl'=>$viewprourl, 'apifilename'=>$apifilename, 'authkey'=>$authkey, 'synlogin'=>$synlogin, 'type'=>$type, 'tagtemplates'=>$tagtemplates), array('appid'=>$app[appid]));
				$appid = $app['appid'];
			} else {
				$extra = serialize(array('apppath'=> getgpc('apppath', 'P')));
				$this->db->insert('applications', array('name'=>$name, 'url'=>$url, 'ip'=>$ip, 'viewprourl'=>$viewprourl, 'apifilename'=>$apifilename, 'authkey'=>$authkey, 'synlogin'=>$synlogin, 'type'=>$type, 'recvnote'=>$recvnote, 'extra'=>$extra, 'tagtemplates'=>$tagtemplates));
				$appid = $this->db->insert_id();
			}

			$this->_add_note_for_app();

			$this->cache_m->updatedata('apps');

			$this->app_m->alter_app_table($appid, 'ADD');
			$this->writelog('app_add', "appid=$appid; appname=$_POST[name]");
			header("location: admin.php?m=app&a=detail&appid=$appid&addapp=yes&sid=".$this->view->sid);
		}
	}

	function ping() {
		$ip = getgpc('ip');
		$url = getgpc('url');
		$appid = intval(getgpc('appid'));
		$app = $this->app_m->get_app_by_appid($appid);
		$status = '';
		if($app['extra']['apppath'] && @include $app['extra']['apppath'].'./api/'.$app['apifilename']) {
			$uc_note = new uc_note();
			$status = $uc_note->test($note['getdata'], $note['postdata']);
		} else {
			$this->load->model('note_m');
			$url = $this->note_m->get_url_code('test', '', $appid);
			$status = $this->app_m->test_api($url, $ip);
		}
		if($status == '1') {
			echo 'document.getElementById(\'status_'.$appid.'\').innerHTML = "<img src=\'images/correct.gif\' border=\'0\' class=\'statimg\' \/><span class=\'green\'>'.$this->lang->line('app_connent_ok').'</span>";testlink();';
		} else {
			echo 'document.getElementById(\'status_'.$appid.'\').innerHTML = "<img src=\'images/error.gif\' border=\'0\' class=\'statimg\' \/><span class=\'red\'>'.$this->lang->line('app_connent_false').'</span>";testlink();';
		}

	}

	function detail() {
		$appid = getgpc('appid');
		$updated = false;
		$app = $this->app_m->get_app_by_appid($appid);
		if(submitcheck()) {
			$type = getgpc('type', 'P');
			$name = getgpc('name', 'P');
			$url = getgpc('url', 'P');
			$ip = getgpc('ip', 'P');
			$viewprourl = getgpc('viewprourl', 'P');
			$apifilename = trim(getgpc('apifilename', 'P'));
			$authkey = getgpc('authkey', 'P');
			$authkey = authcode($authkey, 'ENCODE', UC_MYKEY);
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
			$this->db->update('applications', array('appid'=>$appid, 'name'=>$name, 'url'=>$url, 'type'=>$type, 'ip'=>$ip, 'viewprourl'=>$viewprourl, 'apifilename'=>$apifilename, 'authkey'=>$authkey, 'synlogin'=>$synlogin, 'recvnote'=>$recvnote, 'extra'=>$extra, 'tagtemplates'=>$tagtemplates), array('appid'=>$appid));
			$updated = true;
			$this->cache_m->updatedata('apps');
			$this->cache_m->updatedata('settings');
			$this->writelog('app_edit', "appid=$appid");

			$this->_add_note_for_app();
			$app = $this->app_m->get_app_by_appid($appid);
		}
		$tagtemplates = unserialize($app['tagtemplates']);
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
		$app = $this->app_m->get_app_by_appid($appid);
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
		$typelist = array('UCHOME'=>'UCenter Home','XSPACE'=>'X-Space','DISCUZ'=>'Discuz!','SUPESITE'=>'SupeSite','SUPEV'=>'SupeV','ECSHOP'=>'ECShop','ECMALL'=>'ECMall','OTHER'=>$this->lang->line('other'));
		$data['typelist'] = $typelist;
		$data['updated'] = $updated;
		$addapp = getgpc('addapp');
		$data['addapp'] = $addapp;
		$data['apppath'] = $app['extra']['apppath'];
		$data['tagtemplates'] = $tagtemplates;
		$this->load->view('app', $data);
	}

	function _add_note_for_app() {
		$this->load('note');
		$notedata = $this->db->select('appid, type, name, url, ip, viewprourl, apifilename, charset, synlogin, extra, recvnote')->get("applications")->result_array();
		$notedata = $this->_format_notedata($notedata);
		$notedata['UC_API'] = UC_API;
		$this->note_m->add('updateapps', '', $this->serialize($notedata, 1));
		$this->note_m->send();	
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