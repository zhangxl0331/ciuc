<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tag extends MY_Controller {

	var $settings = array();
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('note_m');
		$this->load->model('tag_m');
		$this->load->model('misc_m');
		$this->settings = $this->cache_m->getdata('settings');
	}
	
	function gettag() {
		$appid = $this->input->get_post('appid');
		$tagname = $this->input->get_post('tagname');
		$nums = $this->input->get_post('nums');
		if(empty($tagname)) {
			return NULL;
		}
		$return = $apparray = $appadd = array();

		if($nums && is_array($nums)) {
			foreach($nums as $k => $num) {
				$apparray[$k] = $k;
			}
		}

		$data = $this->tag_m->get_tag_by_name($tagname);
		if($data) {
			$apparraynew = array();
			foreach($data as $tagdata) {
				$row = $r = array();
				$tmp = explode("\t", $tagdata['data']);
				$type = $tmp[0];
				array_shift($tmp);
				foreach($tmp as $tmp1) {
					$tmp1 != '' && $r[] = $this->misc_m->string2array($tmp1);
				}
				if(in_array($tagdata['appid'], $apparray)) {
					if($tagdata['expiration'] > 0 && $this->time - $tagdata['expiration'] > 3600) {
						$appadd[] = $tagdata['appid'];
						$this->tag_m->formatcache($tagdata['appid'], $tagname);
					} else {
						$apparraynew[] = $tagdata['appid'];
					}
					$datakey = array();
					$count = 0;
					foreach($r as $data) {
						$return[$tagdata['appid']]['data'][] = $data;
						$return[$tagdata['appid']]['type'] = $type;
						$count++;
						if($count >= $nums[$tagdata['appid']]) {
							break;
						}
					}
				}
			}
			$apparray = array_diff($apparray, $apparraynew);
		} else {
			foreach($apparray as $appid) {
				$this->tag_m->formatcache($appid, $tagname);
			}
		}
		if($apparray) {
			$this->load('note');
			$this->note_m->add('gettag', "id=$tagname", '', $appadd, -1);
		}
		return $return;
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */