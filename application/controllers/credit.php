<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Credit extends MY_Controller {

	var $settings = array();
	public function __construct()
	{
		parent::__construct();
		$this->check_priv();
		if(!$this->user['isfounder'] && !$this->user['allowadmincredits']) {
			$this->message('no_permission_for_this_module');
		}
		$this->load->model('setting_m');
		$this->load->model('cache_m');
		$this->settings = $this->cache_m->getdata('settings');
	}
	
	function ls() {
		$appsrc = $this->input->post('appsrc');
		$creditsrc = $this->input->post('creditsrc');
		$appdesc = $this->input->post('appdesc');
		$creditdesc = $this->input->post('creditdesc');
		$ratiosrc = $this->input->post('ratiosrc');
		$ratiodesc = $this->input->post('ratiodesc');
		$delete = $this->input->post('delete');
		$addexchange = $this->input->get('addexchange');
		$delexchange = $this->input->get('delexchange');
		$settings = $this->setting_m->get_setting(array('creditexchange'), TRUE);
		$creditexchange = isset($settings['creditexchange']) && is_array($settings['creditexchange']) ? $settings['creditexchange'] : array();
		$appsrc = @intval($appsrc);
		$creditsrc = @intval($creditsrc);
		$appdesc = @intval($appdesc);
		$creditdesc = @intval($creditdesc);
		$ratiosrc = ($ratiosrc = @intval($ratiosrc)) > 0 ? $ratiosrc : 1;
		$ratiodesc = ($ratiodesc = @intval($ratiodesc)) > 0 ? $ratiodesc : 1;
		$status = 0;
		$creditselect = array();
		if(!empty($addexchange) && submitcheck()) {
			if($appsrc != $appdesc) {
				$key = $appsrc.'_'.$creditsrc.'_'.$appdesc.'_'.$creditdesc;
				$creditexchange[$key] = $ratiosrc."\t".$ratiodesc;
				$this->set_setting('creditexchange', $creditexchange, TRUE);
				$this->cache_m->updatedata('settings');
				$status = 1;
				$this->writelog('credit_addexchange', $appsrc.'_'.$creditsrc.' : '.$appdesc.'_'.$creditdesc.'='.$ratiosrc.' : '.$ratiodesc);
			} else {
				$status = -1;
			}
			$settings = $this->get_setting(array('creditexchange'), TRUE);
			$creditexchange = is_array($settings['creditexchange']) ? $settings['creditexchange'] : array();
		} elseif(!empty($delexchange) && $this->submitcheck()) {
			if(is_array($delete)) {
				foreach($delete as $key) {
					unset($creditexchange[$key]);
				}
				$this->set_setting('creditexchange', $creditexchange, TRUE);
				$this->cache_m->updatedata('settings');
				$status = 1;
				$this->writelog('credit_deleteexchange', "delete=".implode(',', $delete));
			}
			$settings = $this->get_setting(array('creditexchange'), TRUE);
			$creditexchange = is_array($settings['creditexchange']) ? $settings['creditexchange'] : array();
		}

		$apps = isset($this->settings['credits'])&&unserialize($this->settings['credits']);
		if(is_array($creditexchange)) {
			foreach($creditexchange as $set => $ratio) {
				$tmp = array();
				list($tmp['appsrc'], $tmp['creditsrc'], $tmp['appdesc'], $tmp['creditdesc']) = explode('_', $set);
				list($tmp['ratiosrc'], $tmp['ratiodesc']) = explode("\t", $ratio);
				$tmp['creditsrc'] = $apps[$tmp['appsrc']][$tmp['creditsrc']][0];
				$tmp['creditdesc'] = $apps[$tmp['appdesc']][$tmp['creditdesc']][0];
				$tmp['appsrc'] = $this->cache['apps'][$tmp['appsrc']]['name'];
				$tmp['appdesc'] = $this->cache['apps'][$tmp['appdesc']]['name'];
				$creditexchange[$set] = $tmp;
			}
		}

		$appselect = '';
		if(is_array($apps)) {
			foreach($apps as $appid => $credits) {
				$appselect .= '<option value="'.$appid.'">'.$this->cache['apps'][$appid]['name'].'</option>';
				$tmp = array();
				if(is_array($credits)) {
					foreach($credits as $id => $credit) {
						$tmp[] = '['.$id.', \''.str_replace('\'', '\\\'', $credit[0]).'\']';
					}
				}
				$creditselect[$appid] = 'credit['.$appid.'] = ['.implode(',', $tmp).'];';
			}
		}

		$data['status'] = $status;
		$data['appsrc'] = $appsrc;
		$data['creditsrc'] = $creditsrc;
		$data['appdesc'] = $appdesc;
		$data['creditdesc'] = $creditdesc;
		$data['ratiosrc'] = $ratiosrc;
		$data['ratiodesc'] = $ratiodesc;
		$data['appselect'] = $appselect;
		$data['creditselect'] = $creditselect;
		$data['creditexchange'] = $creditexchange;

		$this->load->view('credit', $data);
	}

	function sync() {
		$this->load('note');
		$this->load('misc');
		$this->load('cache');
		$step = intval($this->input->get('step'));
		if(!$step && is_array($this->cache['apps'])) {
			$credits = array();
			$stepapp = intval($this->input->get('stepapp'));
			$testrelease = intval($this->input->get('testrelease'));
			$appids = array_keys($this->cache['apps']);
			$appid = $appids[$stepapp];
			if(!$stepapp) {
				$_CACHE['credits'] = array();
			} else {
				include UC_DATADIR.'cache/credits.php';
			}
			if($app = $this->cache['apps'][$appid]) {
				$apifilename = isset($app['apifilename']) && $app['apifilename'] ? $app['apifilename'] : 'uc.php';
				if($app['extra']['apppath'] && @include $app['extra']['apppath'].'./api/'.$apifilename) {
					$uc_note = new uc_note();
					$data = trim($uc_note->getcreditsettings('', ''));
				} else {
					$url = $_ENV['note']->get_url_code('getcreditsettings', '', $appid);
					$data = trim($_ENV['misc']->dfopen($url, 0, '', '', 1));
				}
				if(!$testrelease) {
					if(!($data = $this->sync_unserialize($data, ''))) {
						header('location: '.UC_API.'/admin.php?m=credit&a=sync&step=0&stepapp='.$stepapp.'&testrelease=1&sid='.$this->view->sid);
						exit();
					} else {
						$stepapp++;
					}
				} else {
					$data = $this->sync_unserialize($data, 'release/20080429/');
					$stepapp++;
				}

				if($data) {
					$_CACHE['credits'][$appid] = $data;
					$s = "<?php\r\n";
					$s .= '$_CACHE[\'credits\'] = '.var_export($_CACHE['credits'], TRUE).";\r\n";
					$s .= "\r\n?>";
					$fp = @fopen(UC_DATADIR.'cache/credits.php', 'w');
					@fwrite($fp, $s);
					@fclose($fp);
				}
				header('location: '.UC_API.'/admin.php?m=credit&a=sync&step=0&stepapp='.$stepapp.'&sid='.$this->view->sid);
			} else {
				header('location: '.UC_API.'/admin.php?m=credit&a=sync&step=1&sid='.$this->view->sid);
			}
			exit();
		}

		include_once UC_DATADIR.'cache/credits.php';
		$credits = $_CACHE['credits'];
		$this->set_setting('credits', $credits, TRUE);
		$this->cache_m->updatedata('settings');
		$this->writelog('credit_sync', 'succeed');

		$settings = $this->get_setting(array('creditexchange'), TRUE);
		$creditexchange = is_array($settings['creditexchange']) ? $settings['creditexchange'] : array();
		$updaterequest = array();
		$i = 0;
		foreach($creditexchange as $set => $ratio) {
			$tmp = array();
			list($tmp['appsrc'], $tmp['creditsrc'], $tmp['appdesc'], $tmp['creditdesc']) = explode('_', $set);
			list($tmp['ratiosrc'], $tmp['ratiodesc']) = explode("\t", $ratio);
			$updaterequest[$tmp['appsrc']][] =
				'&credit['.$tmp['appsrc'].']['.$i.'][creditsrc]='.intval($tmp['creditsrc']).
				'&credit['.$tmp['appsrc'].']['.$i.'][appiddesc]='.urlencode($tmp['appdesc']).
				'&credit['.$tmp['appsrc'].']['.$i.'][creditdesc]='.intval($tmp['creditdesc']).
				'&credit['.$tmp['appsrc'].']['.$i.'][title]='.urlencode($this->cache['apps'][$tmp['appdesc']]['name'].' '.$credits[$tmp['appdesc']][$tmp['creditdesc']][0]).
				'&credit['.$tmp['appsrc'].']['.$i.'][unit]='.urlencode($credits[$tmp['appdesc']][$tmp['creditdesc']][1]).
				'&credit['.$tmp['appsrc'].']['.$i.'][ratiosrc]='.$tmp['ratiosrc'].
				'&credit['.$tmp['appsrc'].']['.$i.'][ratiodesc]='.$tmp['ratiodesc'].
				'&credit['.$tmp['appsrc'].']['.$i.'][ratio]='.($tmp['ratiosrc'] / $tmp['ratiodesc']);
			$i++;
		}
		$data = array();
		foreach($updaterequest as $appid => $value) {
			$data[] = implode('', $updaterequest[$appid]);
		}
		$this->note_m->add('updatecreditsettings', implode('', $data));
		$this->note_m->send();

		$this->message('syncappcredits_updated','admin.php?m=credit&a=ls');
	}

	function sync_unserialize($s, $release_root) {
		if(!function_exists('xml_unserialize')) {
			if($release_root && file_exists(UC_ROOT.$release_root.'./lib/xml.class.php')) {
				include UC_ROOT.$release_root.'./lib/xml.class.php';
			} else {
				include UC_ROOT.'./lib/xml.class.php';
			}
		}

		return xml_unserialize($s);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */