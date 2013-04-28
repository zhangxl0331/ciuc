<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cache extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->check_priv();
		if(!$this->user['isfounder'] && !$this->user['allowadmincache']) {
			$this->message('no_permission_for_this_module');
		}
		$this->load->model('cache_m');
	}
	
	function update() {
		$updated = false;
		if(submitcheck('submit')) {
			$type = $this->input->post('type');
			if(!is_array($type) || in_array('data', $type)) {
				$this->cache_m->updatedata();
			}
			if(!is_array($type) || in_array('tpl', $type)) {
				$this->cache_m->updatetpl();
			}
			$updated = true;
		}
		$data['updated'] = $updated;
		$this->load->view('cache', $data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */