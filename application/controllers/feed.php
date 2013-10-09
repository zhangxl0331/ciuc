<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Feed extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		if(!$this->user['isfounder'] && !$this->user['allowadminnote']) {
			$this->message('no_permission_for_this_module');
		}
		$this->load->model('feed_m');
		$this->load->model('misc_m');
		$this->apps = $this->caches['apps'];		
		
	}
	
	function ls() {
		$page = $this->input->get_post('page');
		$delete = $this->input->post('delete');
		$num = $this->feed_m->get_total_num();
		$feedlist = $this->feed_m->get_list($page, UC_PPP, $num);
		$multipage = page($num, UC_PPP, $page, 'admin.php?m=feed&a=ls');

		$data['feedlist'] = $feedlist;
		$data['multipage'] = $multipage;

		$this->load->view('feed', $data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */