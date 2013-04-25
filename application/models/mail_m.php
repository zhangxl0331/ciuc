<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mail_m extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	function get_total_num() {
		$data = $this->db->count_all_results('mailqueue');
		return $data;
	}

	function get_list($page, $ppp, $totalnum) {
		$start = page_get_start($page, $ppp, $totalnum);
		$data = $this->db->select('m.*, u.username, u.email')->from('mailqueue m')->join('members u', 'm.touid=u.uid', 'LEFT')->order_by('dateline DESC')->get('', $ppp, $start)->result_array();
		foreach((array)$data as $k => $v) {
			$data[$k]['subject'] = htmlspecialchars($v['subject']);
			$data[$k]['tomail'] = empty($v['tomail']) ? $v['email'] : $v['tomail'];
			$data[$k]['dateline'] = $v['dateline'] ? $this->base->date($data[$k]['dateline']) : '';
			$data[$k]['appname'] = $this->base->cache['apps'][$v['appid']]['name'];
		}
		return $data;
	}

	function delete_mail($ids) {
		$ids = $this->base->implode($ids);
		return $this->db->delete('mailqueue', array('mailid IN'=>$ids));
	}

	function add($mail) {
		if($mail['level']) {
			$sql = "INSERT INTO ".UC_DBTABLEPRE." () VALUES ";
			$values_arr = array();
			foreach($mail['uids'] as $uid) {
				if(empty($uid)) continue;
				$email = '';
			}
			foreach($mail['emails'] as $email) {
				if(empty($email)) continue;
				$uid = '';
				$mail['message'] = '';
			}
			$insert_id = $this->db->insert('mailqueue',
				array(
					'touid' => $uid, 
					'tomail' => $email, 
					'subject' => $mail['subject'], 
					'message' => $mail['message'], 
					'frommail' => $mail['frommail'], 
					'charset' => $mail['charset'], 
					'htmlon' => $mail['htmlon'], 
					'level' => $mail['level'], 
					'dateline' => $mail['dateline'], 
					'failures' => 0, 
					'appid' => $mail['appid']
				)
			);
			$insert_id && $this->db->replace('vars', array('name'=>'mailexists', 'value'=>'1'));
			return $insert_id;
		} else {
			$mail['email_to'] = array();
			$uids = 0;
			foreach($mail['uids'] as $uid) {
				if(empty($uid)) continue;
				$uids .= ','.$uid;
			}
			$users = $this->db->select('uid, username, email')->where('uid IN', $uids)->get('members')->result_array();
			foreach($users as $v) {
				$mail['email_to'][] = $v['username'].'<'.$v['email'].'>';
			}
			foreach($mail['emails'] as $email) {
				if(empty($email)) continue;
				$mail['email_to'][] = $email;
			}
			$mail['message'] = str_replace('\"', '"', $mail['message']);
			$mail['email_to'] = implode(',', $mail['email_to']);
			return $this->send_one_mail($mail);
		}
	}

	function send() {
		register_shutdown_function(array($this, '_send'));
	}

	function _send() {

		$mail = $this->_get_mail();
		if(empty($mail)) {
			$this->db->replace('vars', array('name'=>'mailexists', 'value'=>'0'));
			return NULL;
		} else {
			$mail['email_to'] = $mail['tomail'] ? $mail['tomail'] : $mail['username'].'<'.$mail['email'].'>';
			if($this->send_one_mail($mail)) {
				$this->_delete_one_mail($mail['mailid']);
				return true;
			} else {
				$this->_update_failures($mail['mailid']);
				return false;
			}
		}

	}

	function send_by_id($mailid) {
		if ($this->send_one_mail($this->_get_mail_by_id($mailid))) {
			$this->_delete_one_mail($mailid);
			return true;
		}
	}

	function send_one_mail($mail) {
		if(empty($mail)) return;
		$mail['email_to'] = $mail['email_to'] ? $mail['email_to'] : $mail['username'].'<'.$mail['email'].'>';
		$mail_setting = $this->base->settings;
		return include UC_ROOT.'lib/sendmail.inc.php';
	}

	function _get_mail() {
		$data = $this->db->select('m.*, u.username, u.email')->from('mailqueue m')->join('members u', 'm.touid=u.uid', 'LEFT')->where('failures<', UC_MAIL_REPEAT)->order_by('level DESC, mailid ASC')->get('', 1)->first_row();
		return $data;
	}

	function _get_mail_by_id($mailid) {
		$data = $this->db->select('m.*, u.username, u.email')->from('mailqueue m')->join('members u', 'm.touid=u.uid', 'LEFT')->where('mailid', $mailid)->get()->first_row();
		return $data;
	}

	function _delete_one_mail($mailid) {
		$mailid = intval($mailid);
		return $this->db->delete('mailqueue', array('mailid'=>$mailid));
	}

	function _update_failures($mailid) {
		$mailid = intval($mailid);
		return $this->db->set('failures', 'failures+1', FALSE)->update('mailqueue', array(), array('mailid'=>$mailid'));
	}

}