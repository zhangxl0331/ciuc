<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tag_m extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	function get_tag_by_name($tagname) {
		$arr = $this->db->where('tagname', $tagname)->get('tags')->result_array();
		return $arr;
	}

	function get_template($appid) {
		$result = $this->db->select('tagtemplates')->where('appid', $appid)->get('applications')->first_row();
		return $result;
	}

	function updatedata($appid, $data) {
		$appid = intval($appid);
		include_once UC_ROOT.'lib/xml.class.php';
		$data = xml_unserialize($data);
		$this->load->model('app_m');
		$data[0] = addslashes($data[0]);
		$datanew = array();
		if(is_array($data[1])) {
			$this->load->model('misc_m');
			foreach($data[1] as $r) {
				$datanew[] = $this->misc_m->array2string($r);
			}
		}
		$tmp = $this->app_m->get_apps('type', "appid='$appid'");
		$datanew = addslashes($tmp[0]['type']."\t".implode("\t", $datanew));
		if(!empty($data[0])) {
			$return = $this->db->where(array('tagname'=>$data[0], 'appid'=>$appid))->get('tags')->num_rows();
			if($return) {
				$this->db->update('tags', array('data'=>$datanew, 'expiration'=>$this->base->time), array('tagname'=>$data[0], 'appid'=>$appid));
			} else {
				$this->db->insert('tags', array('tagname'=>$data[0], 'appid'=>$appid, 'data'=>$datanew, 'expiration'=>$this->base->time));
			}
		}
	}

	function formatcache($appid, $tagname) {
		$return = $this->db->where(array('tagname'=>$tagname, 'appid'=>$appid))->get('tags')->num_rows();
		if($return) {
			$this->db->update('tags', array('expiration'=>'0'), array('tagname'=>$tagname, 'appid'=>$appid));
		} else {
			$this->db->insert('tags', array('tagname'=>$tagname, 'appid'=>$appid, 'expiration'=>0));
		}
	}

}