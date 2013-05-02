<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Friend extends MY_Controller {

	var $settings = array();
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('setting_m');
		$this->load->model('friend_m');
		$this->settings = $this->cache_m->getdata('settings');
	}
	
	function delete() {
		$uid = intval($this->input->get_post('uid'));
		$friendids = $this->input->get_post('friendids');
		$id = $this->friend_m->delete($uid, $friendids);
		return $id;
	}

	function add() {
		$uid = intval($this->input->get_post('uid'));
		$friendid = $this->input->get_post('friendid');
		$comment = $this->input->get_post('comment');
		$id = $this->friend_m->add($uid, $friendid, $comment);
		return $id;
	}

	function totalnum() {
		$uid = intval($this->input('uid'));
		$direction = intval($this->input('direction'));
		$totalnum = $this->friend_m->get_totalnum_by_uid($uid, $direction);
		return $totalnum;
	}

	function ls() {
		$uid = intval($this->input('uid'));
		$page = intval($this->input('page'));
		$pagesize = intval($this->input('pagesize'));
		$totalnum = intval($this->input('totalnum'));
		$direction = intval($this->input('direction'));
		$pagesize = $pagesize ? $pagesize : UC_PPP;
		$totalnum = $totalnum ? $totalnum : $this->friend_m->get_totalnum_by_uid($uid);
		$data = $this->friend_m->get_list($uid, $page, $pagesize, $totalnum, $direction);
		return $data;
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */