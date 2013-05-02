<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Feed extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('feed_m');
		$this->load->model('misc_m');
		$this->apps = $this->caches['apps'];		
		
	}
	
	function onadd() {
		$appid = intval($this->input->get_post('appid'));
		$icon = $this->input->get_post('icon');
		$uid = intval($this->input->get_post('uid'));
		$username = $this->input->get_post('username');
		$body_data = $this->misc_m->array2string($this->input->get_post('body_data'));
		$title_data = $this->misc_m->array2string($this->input->get_post('title_data'));

		$title_template = $this->_parsetemplate($this->input->get_post('title_template'));
		$body_template = $this->_parsetemplate($this->input->get_post('body_template'));
		$body_general = $this->input->get_post('body_general');
		$target_ids = $this->input->get_post('target_ids');
		$image_1 = $this->input->get_post('image_1');
		$image_1_link = $this->input->get_post('image_1_link');
		$image_2 = $this->input->get_post('image_2');
		$image_2_link = $this->input->get_post('image_2_link');
		$image_3 = $this->input->get_post('image_3');
		$image_3_link = $this->input->get_post('image_3_link');
		$image_4 = $this->input->get_post('image_4');
		$image_4_link = $this->input->get_post('image_4_link');

		$hash_template = md5($title_template.$body_template);
		$hash_data = md5($title_template.$title_data.$body_template.$body_data);
		$dateline = $this->time;
		$this->db->insert('feeds', array('appid'=>$appid, 'icon'=>$icon, 'uid'=>$uid, 'username'=>$username,
			'title_template'=>$title_template, 'title_data'=>$title_data, 'body_template'=>$body_template, 'body_data'=>$body_data, 'body_general'=>$body_general,
			'image_1'=>$image_1, 'image_1_link'=>$image_1_link, 'image_2'=>$image_2, 'image_2_link'=>$image_2_link,
			'image_3'=>$image_3, 'image_3_link'=>$image_3_link, 'image_4'=>$image_4, 'image_4_link'=>$image_4_link,
			'hash_template'=>$hash_template, 'hash_data'=>$hash_data, 'target_ids'=>$target_ids, 'dateline'=>$dateline));
		return $this->db->insert_id();
	}

	function delete() {
		$start = $this->input->get_post('start');
		$limit = $this->input->get_post('limit');
		$end = $start + $limit;
		$this->db->delete('feeds', array('feedid>'=>$start, 'feedid<'=>$end));
	}

	function get() {
		$limit = intval($this->input->get_post('limit'));
		$delete = $this->input->get_post('delete');
		$feedlist = $this->db->order_by('feedid DESC')->get('feeds', $limit);
		if($feedlist) {
			$maxfeedid = $feedlist[0]['feedid'];
			foreach($feedlist as $key => $feed) {
				$feed['body_data'] = $this->misc_m->string2array($feed['body_data']);
				$feed['title_data'] = $this->misc_m->string2array($feed['title_data']);
				$feedlist[$key] = $feed;
			}
		}
		if(!empty($feedlist)) {
			if(!isset($delete) || $delete) {
				$this->_delete(0, $maxfeedid);
			}
		}
		return $feedlist;
	}

	function _delete($start, $end) {
		$this->db->delete('feeds',  array('feedid>='=>$start, 'feedid<='=>$end));
	}

	function _parsetemplate($template) {
		$template = str_replace(array("\r", "\n"), '', $template);
		$template = str_replace(array('<br>', '<br />', '<BR>', '<BR />'), "\n", $template);
		$template = str_replace(array('<b>', '<B>'), '[B]', $template);
		$template = str_replace(array('<i>', '<I>'), '[I]', $template);
		$template = str_replace(array('<u>', '<U>'), '[U]', $template);
		$template = str_replace(array('</b>', '</B>'), '[/B]', $template);
		$template = str_replace(array('</i>', '</I>'), '[/I]', $template);
		$template = str_replace(array('</u>', '</U>'), '[/U]', $template);
		$template = htmlspecialchars($template);
		$template = nl2br($template);
		$template = str_replace(array('[B]', '[I]', '[U]', '[/B]', '[/I]', '[/U]'), array('<b>', '<i>', '<u>', '</b>', '</i>', '</u>'), $template);
		return $template;
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */