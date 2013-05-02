<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Credit extends MY_Controller {

	var $settings = array();
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('setting_m');
		$this->load->model('cache_m');
		$this->settings = $this->cache_m->getdata('settings');
	}
	
	function onrequest() {
		$uid = intval($this->input('uid'));
		$from = intval($this->input('from'));
		$to = intval($this->input('to'));
		$toappid = intval($this->input('toappid'));
		$amount = intval($this->input('amount'));
		$status = 0;
		if(isset($this->settings['creditexchange'][$this->app['appid'].'_'.$from.'_'.$toappid.'_'.$to])) {
			$toapp = $app = $this->cache['apps'][$toappid];
			$apifilename = isset($toapp['apifilename']) && $toapp['apifilename'] ? $toapp['apifilename'] : 'uc.php';
			if($toapp['extra']['apppath'] && @include $toapp['extra']['apppath'].'./api/'.$apifilename) {
				$uc_note = new uc_note();
				$status = $uc_note->updatecredit(array('uid' => $uid, 'credit' => $to, 'amount' => $amount), '');
			} else {
				$url = $_ENV['note']->get_url_code('updatecredit', "uid=$uid&credit=$to&amount=$amount", $toappid);
				$status = trim($_ENV['misc']->dfopen($url, 0, '', '', 1, $toapp['ip'], UC_NOTE_TIMEOUT));
			}
		}
		echo $status ? 1 : 0;
		exit;
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */