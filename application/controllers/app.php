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
		$this->load->library('xml');
	}
	
	function ls() {
		$status = $affectedrows = 0;
		if($delete = $this->input->post('delete')) {
			$affectedrows += $this->app_m->delete_apps($delete);
			foreach($this->input->post('delete') as $k => $appid) {
				$this->app_m->alter_app_table($appid, 'REMOVE');
				unset($_POST['name'][$k]);
			}
			$this->cache_m->updatedata();
			$this->writelog('app_delete', 'appid='.implode(',', $delete));
			$status = 2;

			$this->_add_note_for_app();
		}


		$applist = $this->app_m->get_apps();
		$data['status'] = $status;
		$data['applist'] = $applist;

		$this->load->view('app', $data);
	}

	function add() {
		if(!submitcheck()) {
			$md5ucfounderpw = md5(UC_FOUNDERPW);
			$data['md5ucfounderpw'] = $md5ucfounderpw;


			$typelist = array('UCHOME'=>'UCenter Home','XSPACE'=>'X-Space','DISCUZ'=>'Discuz!','SUPESITE'=>'SupeSite','SUPEV'=>'SupeV','ECSHOP'=>'ECShop','ECMALL'=>'ECMall','OTHER'=>$this->lang->line('other'));
			$data['typelist'] = $typelist;
			$this->load->view('app', $data);
		} else {
			$type = $this->input->post('type');
			$name = $this->input->post('name');
			$url = $this->input->post('url');
			$ip = $this->input->post('ip');
			$viewprourl = $this->input->post('viewprourl');
			$authkey = $this->input->post('authkey');
			$authkey = authcode($authkey, 'ENCODE', UC_MYKEY);
			$synlogin = $this->input->post('synlogin');
			$recvnote = $this->input->post('recvnote');
			$apifilename = trim($this->input->post('apifilename'));

			$tagtemplates = array();
			$tagtemplates['template'] = $this->input->post('tagtemplates');
			$tagfields = explode("\n", $this->input->post('tagfields'));
			foreach($tagfields as $field) {
				$field = trim($field);
				list($k, $v) = explode(',', $field);
				if($k) {
					$tagtemplates['fields'][$k] = $v;
				}
			}
			$tagtemplates = $this->xml->serialize($tagtemplates, 1);

			if(!$this->misc_m->check_url($this->input->post('url'))) {
				$this->message('app_add_url_invalid', 'BACK');
			}
			if($this->input->post('ip') && !$this->misc_m->check_ip($this->input->post('ip'))) {
				$this->message('app_add_ip_invalid', 'BACK');
			}
			$app = $this->db->where('name', $name)->get('applications')->num_rows();
			if($app) {
				$this->db->update('applications', array('name'=>$name, 'url'=>$url, 'ip'=>$ip, 'viewprourl'=>$viewprourl, 'apifilename'=>$apifilename, 'authkey'=>$authkey, 'synlogin'=>$synlogin, 'type'=>$type, 'tagtemplates'=>$tagtemplates), array('appid'=>$app[appid]));
				$appid = $app['appid'];
			} else {
				$extra = $this->xml->serialize(array('apppath'=> $this->input->post('apppath')));
				$this->db->insert('applications', array('name'=>$name, 'url'=>$url, 'ip'=>$ip, 'viewprourl'=>$viewprourl, 'apifilename'=>$apifilename, 'authkey'=>$authkey, 'synlogin'=>$synlogin, 'type'=>$type, 'recvnote'=>$recvnote, 'extra'=>$extra, 'tagtemplates'=>$tagtemplates));
				$appid = $this->db->insert_id();
			}

			$this->_add_note_for_app();

			$this->cache_m->updatedata('apps');

			$this->app_m->alter_app_table($appid, 'ADD');
			$this->writelog('app_add', "appid=$appid; appname={$this->input->post('name')}");
			header("location: admin.php?m=app&a=detail&appid=$appid&addapp=yes&sid=".$this->view->sid);
		}
	}

	function ping() {
		$ip = $this->input->get_post('ip');
		$url = $this->input->get_post('url');
		$appid = intval($this->input->get_post('appid'));
		$app = $this->app_m->get_app_by_appid($appid);
		$status = '';
		if(isset($app['extra']['apppath']) && $app['extra']['apppath'] && @include $app['extra']['apppath'].'./api/'.$app['apifilename']) {
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
		$appid = $this->input->get_post('appid');
		$updated = false;
		$app = $this->app_m->get_app_by_appid($appid);
		if(submitcheck()) {
			$type = $this->input->post('type');
			$name = $this->input->post('name');
			$url = $this->input->post('url');
			$ip = $this->input->post('ip');
			$viewprourl = $this->input->post('viewprourl');
			$apifilename = trim($this->input->post('apifilename'));
			$authkey = $this->input->post('authkey');
			$authkey = authcode($authkey, 'ENCODE', UC_MYKEY);
			$synlogin = $this->input->post('synlogin');
			$recvnote = $this->input->post('recvnote');
			if($this->input->post('apppath')) {
				$app['extra']['apppath'] = $this->_realpath($this->input->post('apppath'));
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
			$tagtemplates['template'] = $this->input->post('tagtemplates');
			$tagfields = explode("\n", $this->input->post('tagfields'));
			foreach($tagfields as $field) {
				$field = trim($field);
				list($k, $v) = explode(',', $field);
				if($k) {
					$tagtemplates['fields'][$k] = $v;
				}
			}
			$tagtemplates = $this->xml->serialize($tagtemplates, 1);

			$extra = addslashes($this->xml->serialize($app['extra']));
			$this->db->update('applications', array('appid'=>$appid, 'name'=>$name, 'url'=>$url, 'type'=>$type, 'ip'=>$ip, 'viewprourl'=>$viewprourl, 'apifilename'=>$apifilename, 'authkey'=>$authkey, 'synlogin'=>$synlogin, 'recvnote'=>$recvnote, 'extra'=>$extra, 'tagtemplates'=>$tagtemplates), array('appid'=>$appid));
			$updated = true;
			$this->cache_m->updatedata('apps');
			$this->cache_m->updatedata('settings');
			$this->writelog('app_edit', "appid=$appid");

			$this->_add_note_for_app();
			$app = $this->app_m->get_app_by_appid($appid);
		}
		$tagtemplates = $this->xml->unserialize($app['tagtemplates']);
		$template = htmlspecialchars($tagtemplates['template']);
		$tmp = '';
		if(is_array($tagtemplates['fields'])) {
			foreach($tagtemplates['fields'] as $field => $memo) {
				$tmp .= $field.','.$memo."\n";
			}
		}
		$tagtemplates['fields'] = $tmp;

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
		$addapp = $this->input->get_post('addapp');
		$data['addapp'] = $addapp;
		$data['apppath'] = isset($app['extra']['apppath'])?$app['extra']['apppath']:'';
		$data['tagtemplates'] = $tagtemplates;
		$this->load->view('app', $data);
	}

	function _add_note_for_app() {
		$this->load->model('note_m');
		$notedata = $this->db->select('appid, type, name, url, ip, viewprourl, apifilename, charset, synlogin, extra, recvnote')->get("applications")->result_array();
		$notedata = $this->_format_notedata($notedata);
		$notedata['UC_API'] = UC_API;
		$this->note_m->add('updateapps', '', $this->xml->serialize($notedata, 1));
		$this->note_m->send();	
	}

	function _format_notedata($notedata) {
		$arr = array();
		foreach($notedata as $key => $note) {
			$note['extra'] = $this->xml->unserialize($note['extra']);
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