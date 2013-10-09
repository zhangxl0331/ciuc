<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Note extends MY_Controller {

	var $apps = array();
	var $operations = array();
	
	public function __construct()
	{
		parent::__construct();
		if(!$this->user['isfounder'] && !$this->user['allowadminnote']) {
			$this->message('no_permission_for_this_module');
		}
		$this->load->model('note_m');
		$this->apps = $this->caches['apps'];
		
		$this->operations = array(
				'test'=>array('', 'action=test'),
				'deleteuser'=>array('', 'action=deleteuser'),
				'renameuser'=>array('', 'action=renameuser'),
				'deletefriend'=>array('', 'action=deletefriend'),
				'gettag'=>array('', 'action=gettag', 'tag', 'updatedata'),
				'getcreditsettings'=>array('', 'action=getcreditsettings'),
				'updatecreditsettings'=>array('', 'action=updatecreditsettings'),
				'updateclient'=>array('', 'action=updateclient'),
				'updatepw'=>array('', 'action=updatepw'),
				'updatebadwords'=>array('', 'action=updatebadwords'),
				'updatehosts'=>array('', 'action=updatehosts'),
				'updateapps'=>array('', 'action=updateapps'),
				'updatecredit'=>array('', 'action=updatecredit'),
		);
		$this->check_priv();
	}
	
	function ls() {
		$page = $this->input->get_post('page');
		$delete = $this->input->post('delete');
		$status = 0;
		if(!empty($delete)) {
			$this->note_m->delete_note($delete);
			$status = 2;
			$this->writelog('note_delete', "delete=".implode(',', $delete));
		}
		foreach($this->caches['apps'] as $key => $app) {
			if(empty($app['recvnote'])) {
				unset($this->apps[$key]);
			}
		}
		$num = $this->note_m->get_total_num(1);
		$notelist = $this->note_m->get_list($page, UC_PPP, $num, 1);
		$multipage = page($num, UC_PPP, $page, 'admin.php?m=note&a=ls');

		$data['status'] = $status;
		$data['applist'] = $this->apps;
		$notelist = $this->_format_notlist($notelist);
		$data['notelist'] = $notelist;
		$data['multipage'] = $multipage;

		$this->load->view('note', $data);
	}

	function send() {
		$noteid = intval($this->input->get_post('noteid'));
		$appid = intval($this->input->get_post('appid'));
		$result = $this->note_m->sendone($appid, $noteid);
		if($result) {
			$this->writelog('note_send', "appid=$appid&noteid=$noteid");
			$this->message('note_succeed', $this->input->server('HTTP_REFERER'));
		} else {
			$this->writelog('note_send', 'failed');
			$this->message('note_false', $this->input->server('HTTP_REFERER'));
		}

	}

	function _note_status($status, $appid, $noteid, $args='', $operation='') {
		if($status > 0) {
			return '<font color="green">'.$this->lang->line('note_succeed').'</font>';
		} elseif($status == 0) {
			$url = 'admin.php?m=note&a=send&appid='.$appid.'&noteid='.$noteid;
			return '<a href="'.$url.'" class="red">'.$this->lang->line('note_na').'</a>';
		} elseif($status < 0) {
			$url = 'admin.php?m=note&a=send&appid='.$appid.'&noteid='.$noteid;
			return '<a href="'.$url.'"><font color="red">'.$this->lang->line('note_false').(-$status).$this->lang->line('note_times').'</font></a>';
		}
	}

	function _format_notlist($notelist) {
		if(is_array($notelist)) {
			foreach($notelist AS $key => $note) {
				$notelist[$key]['operation'] = $this->lang->line('note_'.$note['operation']);//$this->operations[$note['operation']][0];
				foreach($this->apps AS $appid => $app) {
					$notelist[$key]['status'][$appid] = $this->_note_status($note['app'.$appid], $appid, $note['noteid'], '', $note['operation']);
				}
			}
		}
		return $notelist;
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */