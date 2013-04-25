<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Friend_m extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	function add($uid, $friendid, $comment='') {
		$direction = $this->db->result_first("SELECT direction FROM ".UC_DBTABLEPRE."friends WHERE uid='$friendid' AND friendid='$uid' LIMIT 1");
		if($direction == 1) {
			$this->db->insert('friends', array('uid'=>$uid, 'friendid'=>$friendid, 'comment'=>$comment, 'direction'=>'3'));
			$this->db->update('friends', array('direction'=>'3'), array('uid'=>$friendid, 'friendid'=>$uid));
			return 1;
		} elseif($direction == 2) {
			return 1;
		} elseif($direction == 3) {
			return -1;
		} else {
			return $this->db->insert('friends', array('uid'=>$uid, 'friendid'=>$friendid, 'comment'=>$comment, 'direction'=>'1'));
		}
	}

	function delete($uid, $friendids) {
		$friendids = $this->base->implode($friendids);
		if($this->db->delete('friends', array('uid'=>$uid, 'friendid IN'=>$friendids))) {
			return $this->db->update('friends', array('direction'=>1), array('uid IN'=> $friendids, 'friendid'=>$uid, 'direction'=>'3'));
		}
		return FALSE;
	}

	function get_totalnum_by_uid($uid, $direction = 0) {
		$sqladd = '';
		if($direction == 0) {
			$sqladd = "uid='$uid'";
		} elseif($direction == 1) {
			$sqladd = "uid='$uid' AND direction='1'";
		} elseif($direction == 2) {
			$sqladd = "friendid='$uid' AND direction='1'";
		} elseif($direction == 3) {
			$sqladd = "uid='$uid' AND direction='3'";
		}
		$totalnum = $this->db->where($sqladd)->get('friends')->num_rows();
		return $totalnum;
	}

	function get_list($uid, $page, $pagesize, $totalnum, $direction = 0) {
		$start = page_get_start($page, $pagesize, $totalnum);
		$sqladd = '';
		if($direction == 0) {
			$sqladd = "f.uid='$uid'";
		} elseif($direction == 1) {
			$sqladd = "f.uid='$uid' AND f.direction='1'";
		} elseif($direction == 2) {
			$sqladd = "f.friendid='$uid' AND f.direction='1'";
		} elseif($direction == 3) {
			$sqladd = "f.uid='$uid' AND f.direction='3'";
		}
		if($sqladd) {
			$data = $this->db->select('f.*, m.username')->from('friends f')->join('members m', 'f.friendid=m.uid', 'LEFT')->where($sqladd)->get('', $pagesize, $start)->result_array();
			return $data;
		} else {
			return array();
		}
	}

	function is_friend($uid, $friendids, $direction = 0) {
		$friendid_str = implode("', '", $friendids);
		$sqladd = '';
		if($direction == 0) {
			$sqladd = "uid='$uid'";
		} elseif($direction == 1) {
			$sqladd = "uid='$uid' AND friendid IN ('$friendid_str') AND direction='1'";
		} elseif($direction == 2) {
			$sqladd = "friendid='$uid' AND uid IN ('$friendid_str') AND direction='1'";
		} elseif($direction == 3) {
			$sqladd = "uid='$uid' AND friendid IN ('$friendid_str') AND direction='3'";
		}
		if($this->db->where($sqladd)->get('friends')->num_rows() == count($friendids)) {
			return true;
		} else {
			return false;
		}
	}

}