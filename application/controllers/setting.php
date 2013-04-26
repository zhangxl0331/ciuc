<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setting extends MY_Controller {

	var $_setting_items = array('doublee', 'accessemail', 'censoremail', 'censorusername', 'dateformat', 'timeoffset', 'timeformat', 'extra', 'maildefault', 'mailsend', 'mailserver', 'mailport', 'mailauth', 'mailfrom', 'mailauth_username', 'mailauth_password', 'maildelimiter', 'mailusername', 'mailsilent', 'pmcenter', 'pmlimit1day', 'pmfloodctrl', 'sendpmseccode', 'pmsendregdays');
	
	public function __construct()
	{
		parent::__construct();
		$this->check_priv();
		if(!$this->user['isfounder'] && !$this->user['allowadminsetting']) {
			$this->message('no_permission_for_this_module');
		}
	}
	
	function ls() {
		$this->load->model('user_m');
		$this->load->model('setting_m');
		$updated = false;
		if(submitcheck()) {
			$timeformat = $this->input->post('timeformat');
			$dateformat = $this->input->post('dateformat');
			$timeoffset = $this->input->post('timeoffset');
			$pmlimit1day = $this->input->post('pmlimit1day');
			$pmfloodctrl = $this->input->post('pmfloodctrl');
			$pmsendregdays = $this->input->post('pmsendregdays');
			$pmcenter = $this->input->post('pmcenter');
			$sendpmseccode = $this->input->post('sendpmseccode');
			$dateformat = str_replace(array('yyyy', 'mm', 'dd'), array('y', 'n', 'j'), strtolower($dateformat));
			$timeformat = $timeformat == 1 ? 'H:i' : 'h:i A';
			$timeoffset = in_array($timeoffset, array('-12', '-11', '-10', '-9', '-8', '-7', '-6', '-5', '-4', '-3.5', '-3', '-2', '-1', '0', '1', '2', '3', '3.5', '4', '4.5', '5', '5.5', '5.75', '6', '6.5', '7', '8', '9', '9.5', '10', '11', '12')) ? $timeoffset : 8;

			$this->setting_m->set_setting('dateformat', $dateformat);
			$this->setting_m->set_setting('timeformat', $timeformat);
			$timeoffset = $timeoffset * 3600;
			$this->setting_m->set_setting('timeoffset', $timeoffset);
			$this->setting_m->set_setting('pmlimit1day', intval($pmlimit1day));
			$this->setting_m->set_setting('pmfloodctrl', intval($pmfloodctrl));
			$this->setting_m->set_setting('pmsendregdays', intval($pmsendregdays));
			$this->setting_m->set_setting('pmcenter', $pmcenter);
			$this->setting_m->set_setting('sendpmseccode', $sendpmseccode ? 1 : 0);
			$updated = true;

			$this->updatecache();
		}

		$settings = $this->setting_m->get_setting($this->_setting_items);
		if($updated) {
			$this->_add_note_for_setting($settings);
		}
		$settings['dateformat'] = str_replace(array('y', 'n', 'j'), array('yyyy', 'mm', 'dd'), $settings['dateformat']);
		$settings['timeformat'] = $settings['timeformat'] == 'H:i' ? 1 : 0;
		$settings['pmcenter'] = $settings['pmcenter'] ? 1 : 0;
		$a = getgpc('a');
		$data['a'] = $a;

		$data['dateformat'] = $settings['dateformat'];
		$timeformatchecked = array($settings['timeformat'] => 'checked="checked"');
		$data['timeformat'] = $timeformatchecked;
		$data['pmlimit1day'] = $settings['pmlimit1day'];
		$data['pmsendregdays'] = $settings['pmsendregdays'];
		$data['pmfloodctrl'] = $settings['pmfloodctrl'];
		$pmcenterchecked = array($settings['pmcenter'] => 'checked="checked"');
		$pmcenterchecked['display'] = $settings['pmcenter'] ? '' : 'style="display:none"';
		$data['pmcenter'] = $pmcenterchecked;
		$sendpmseccodechecked = array($settings['sendpmseccode'] => 'checked="checked"');
		$data['sendpmseccode'] = $sendpmseccodechecked;
		$timeoffset = intval($settings['timeoffset'] / 3600);
		$checkarray = array($timeoffset => 'selected="selected"');
		$data['checkarray'] = $checkarray;
		$data['updated'] = $updated;
		$this->load->view('setting', $data);
	}

	function updatecache() {
		$this->load->model('cache_m');
		$this->cache_m->updatedata('settings');
	}

	function register() {
		$updated = false;
		if(submitcheck()) {
			$this->setting_m->set_setting('doublee', getgpc('doublee', 'P'));
			$this->setting_m->set_setting('accessemail', getgpc('accessemail', 'P'));
			$this->setting_m->set_setting('censoremail', getgpc('censoremail', 'P'));
			$this->setting_m->set_setting('censorusername', getgpc('censorusername', 'P'));
			$updated = true;
			$this->writelog('setting_register_update');
			$this->updatecache();
		}

		$settings = $this->setting_m->get_setting($this->_setting_items);
		if($updated) {
			$this->_add_note_for_setting($settings);
		}

		$doubleechecked = array($settings['doublee'] => 'checked="checked"');
		$data['doublee'] = $doubleechecked;
		$data['accessemail'] = $settings['accessemail'];
		$data['censoremail'] = $settings['censoremail'];
		$data['censorusername'] = $settings['censorusername'];
		$data['updated'] = $updated;
		$this->load->view('setting', $data);
	}

	function mail() {
		$updated = false;
		$items = array('maildefault', 'mailsend', 'mailserver', 'mailport', 'mailauth', 'mailfrom', 'mailauth_username', 'mailauth_password', 'maildelimiter', 'mailusername', 'mailsilent');
		if(submitcheck()) {
			foreach($items as $item) {
				$value = $this->input->post($item);
				$this->setting_m->set_setting($item, $value);
			}
			$updated = true;
			$this->writelog('setting_mail_update');
			$this->updatecache();
		}

		$settings = $this->setting_m->get_setting($this->_setting_items);
		if($updated) {
			$this->_add_note_for_setting($settings);
		}
		foreach($items as $item) {
			$data[$item] = htmlspecialchars($settings[$item]);
		}

		$data['updated'] = $updated;
		$this->load->view('setting', $data);
	}

	function _add_note_for_setting($settings) {
		$this->load->model('note_m');
		$this->note_m->add('updateclient', '', $this->serialize($settings, 1));
		$this->note_m->send();
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */