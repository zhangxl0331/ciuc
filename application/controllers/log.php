<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Log extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->check_priv();
		if(!$this->user['isfounder'] && !$this->user['allowadminlog']) {
			$this->message('no_permission_for_this_module');
		}
		$this->check_priv();
	}
	
	function ls() {
		$logdir = FCPATH.'data/logs/';
		$dir = opendir($logdir);
		$logs = $loglist = array();
		while($entry = readdir($dir)) {
			if(is_file($logdir.$entry) && strpos($entry, '.php') !== FALSE) {
				$logs = array_merge($logs, file($logdir.$entry));
			}
		}
		closedir($dir);

		$logs = array_reverse($logs);
		foreach($logs AS $k => $v) {
			if(count($v = explode("\t", $v)) > 1) {
				$v[3] = $this->date($v[3]);
				$v[4] = $this->lang->line($v[4]);
				$loglist[$k] = $v;
			}
		}

		$page = max(1, intval($_GET['page']));
		$start = ($page - 1) * UC_PPP;

		$num = count($loglist);
		$multipage = page($num, UC_PPP, $page, 'admin.php?m=log&a=ls');
		$loglist = array_slice($loglist, $start, UC_PPP);

		$data['loglist'] = $loglist;
		$data['multipage'] = $multipage;

		$this->load->view('log', $data);

	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */