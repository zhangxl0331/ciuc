<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Domain extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		if(!$this->user['isfounder'] && !$this->user['allowadmindomain']) {
			$this->message('no_permission_for_this_module');
		}
		$this->load->model('domain_m');
		$this->load->model('misc_m');
		$this->load->library('xml');
		$this->check_priv();
	}
	
	function ls() {
		$status = 0;
		$domainnew = $this->input->post('domainnew');
		$ipnew = $this->input->post('ipnew');
		$domain = $this->input->post('domain');
		$ip = $this->input->post('ip');
		$delete = $this->input->post('delete');
		if($domainnew) {
			if(!$this->misc_m->check_ip($ipnew)) {
				$this->message('app_add_ip_invalid', 'BACK');
			}
			$this->domain_m->add_domain($domainnew, $ipnew);
			$status = 1;
			$this->writelog('domain_add', 'domainnew='.htmlspecialchars($domainnew).'&ipnew='.htmlspecialchars($ipnew));
		}
		if($domain) {
			foreach($domain as $id => $arr) {
				if(!$this->misc_m->check_ip($ip[$id])) {
					$this->message('app_add_ip_invalid', 'BACK');
				}
				$this->domain_m->update_domain($domain[$id], $ip[$id], $id);
			}
			$status = 2;
		}
		if($delete) {
			$this->domain_m->delete_domain($delete);
			$status = 2;
			$this->writelog('domain_delete', "delete=".implode(',', $delete));
		}
		if($status > 0) {
			$notedata = $this->domain_m->get_list($_GET['page'], 1000000, 1000000);
			$this->load->model('note_m');
			$this->note_m->add('updatehosts', '', $this->xml->serialize($notedata));
			$this->note_m->send();
		}
		$num = $this->domain_m->get_total_num();
		$domainlist = $this->domain_m->get_list($_GET['page'], UC_PPP, $num);
		$multipage = page($num, UC_PPP, $_GET['page'], 'admin.php?m=domain&a=ls');

		$data['status'] = $status;
		$data['domainlist'] = $domainlist;
		$data['multipage'] = $multipage;

		$this->load->view('domain', $data);

	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */