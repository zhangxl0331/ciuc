<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mail extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->check_priv();
		$this->load->model('mail_m');
		$this->check_priv();
	}
	
	function ls() {
		$page = getgpc('page');
		$delete = getgpc('delete', 'P');
		$status = 0;
		if(!empty($delete)) {
			$this->mail_m->delete_mail($delete);
			$status = 2;
			$this->writelog('mail_delete', "delete=".implode(',', $delete));
		}

		$num = $this->mail_m->get_total_num();
		$maillist = $this->mail_m->get_list($page, UC_PPP, $num);
		$multipage = page($num, UC_PPP, $page, 'admin.php?m=mail&a=ls');

		$data['status'] = $status;
		$data['maillist'] = $maillist;
		$data['multipage'] = $multipage;

		$this->load->view('mail', $data);
	}

	function send() {
		$mailid = intval(getgpc('mailid'));
		$result = $this->mail_m->send_by_id($mailid);
		if($result) {
			$this->writelog('mail_send', "appid=$appid&noteid=$noteid");
			$this->message('mail_succeed', $_SERVER['HTTP_REFERER']);
		} else {
			$this->writelog('mail_send', 'failed');
			$this->message('mail_false', $_SERVER['HTTP_REFERER']);
		}

	}

	function _note_status($status, $appid, $noteid, $args, $operation) {
		if($status > 0) {
			return '<font color="green">'.$this->lang['note_succeed'].'</font>';
		} elseif($status == 0) {
			$url = 'admin.php?m=note&a=send&appid='.$appid.'&noteid='.$noteid;
			return '<a href="'.$url.'" class="red">'.$this->lang['note_na'].'</a>';
		} elseif($status < 0) {
			$url = 'admin.php?m=note&a=send&appid='.$appid.'&noteid='.$noteid;
			return '<a href="'.$url.'"><font color="red">'.$this->lang['note_false'].(-$status).$this->lang['note_times'].'</font></a>';
		}
	}

	function _format_maillist(&$maillist) {
		if(is_array($maillist)) {
			foreach($maillist AS $key => $note) {
				$maillist[$key]['operation'] = $this->lang['note_'.$note['operation']];//$this->operations[$note['operation']][0];
				foreach($this->apps AS $appid => $app) {
					$maillist[$key]['status'][$appid] = $this->_note_status($note['app'.$appid], $appid, $note['noteid'], $note['args'], $note['operation']);
				}
			}
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */