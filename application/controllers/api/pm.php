<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
define('PMLIMIT1DAY_ERROR', -1);
define('PMFLOODCTRL_ERROR', -2);
define('PMMSGTONOTFRIEND', -3);
define('PMSENDREGDAYS', -4);
class Pm extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('pm_m');
		$this->load->model('user_m');
		$this->load->model('friend_m');
		$this->load->library('uccode');
	}
	
	function check_newpm() {
		$this->user['uid'] = intval($this->input->get_post('uid'));
		$more = $this->input->get_post('more');
		$result = $this->pm_m->check_newpm($this->user['uid'], $more);
		if($more == 3) {
			$this->load->library('uccode');
			
			$result['lastmsg'] = $this->uccode->complie($result['lastmsg']);
		}
		return $result;
	}

	function sendpm() {
		$fromuid = $this->input->get_post('fromuid');
		$msgto = $this->input->get_post('msgto');
		$subject = $this->input->get_post('subject');
		$message = $this->input->get_post('message');
		$replypmid = $this->input->get_post('replypmid');
		$isusername = $this->input->get_post('isusername');
		if($fromuid) {
			$user = $this->user_m->get_user_by_uid($fromuid);
			if(!$user) {
				return 0;
			}
			$this->user['uid'] = $user['uid'];
			$this->user['username'] = $user['username'];
		} else {
			$this->user['uid'] = 0;
			$this->user['username'] = '';
		}
		if($replypmid) {
			$isusername = 1;
			$pms = $this->pm_m->get_pm_by_pmid($this->user['uid'], $replypmid);
			if($pms[0]['msgfromid'] == $this->user['uid']) {
				$user = $this->user_m->get_user_by_uid($pms[0]['msgtoid']);
				$msgto = $user['username'];
			} else {
				$msgto = $pms[0]['msgfrom'];
			}
		}

		$msgto = array_unique(explode(',', $msgto));
		$isusername && $msgto = $this->user_m->name2id($msgto);
		$blackls = $this->pm_m->get_blackls($this->user['uid'], $msgto);

		if($fromuid) {
			if($this->settings['pmsendregdays']) {
				if($user['regdate'] > $this->time - $this->settings['pmsendregdays'] * 86400) {
					return PMSENDREGDAYS;
				}
			}
			if(count($msgto) > 1 && !($is_friend = $this->friend_m->is_friend($fromuid, $msgto, 3))) {
				return PMMSGTONOTFRIEND;
			}
			$pmlimit1day = $this->settings['pmlimit1day'] && $this->pm_m->count_pm_by_fromuid($this->user['uid'], 86400) > $this->settings['pmlimit1day'];
			if($pmlimit1day || ($this->settings['pmfloodctrl'] && $this->pm_m->count_pm_by_fromuid($this->user['uid'], $this->settings['pmfloodctrl']))) {
				if(!$this->friend_m->is_friend($fromuid, $msgto, 3)) {
					if(!$this->pm_m->is_reply_pm($fromuid, $msgto)) {
						if($pmlimit1day) {
							return PMLIMIT1DAY_ERROR;
						} else {
							return PMFLOODCTRL_ERROR;
						}
					}
				}
			}
		}
		$lastpmid = 0;
		foreach($msgto as $uid) {
			if(!$fromuid || !in_array('{ALL}', $blackls[$uid])) {
				$blackls[$uid] = $this->user_m->name2id($blackls[$uid]);
				if(!$fromuid || isset($blackls[$uid]) && !in_array($this->user['uid'], $blackls[$uid])) {
					$lastpmid = $this->pm_m->sendpm($subject, $message, $this->user, $uid, 0, 0, $replypmid);
				}
			}
		}
		return $lastpmid;
	}

	function delete() {
		$this->user['uid'] = intval($this->input->get_post('uid'));
		$id = $this->pm_m->deletepm($this->user['uid'], $this->input('pmids'));
		return $id;
	}

	function deleteuser() {
		$this->user['uid'] = intval($this->input->get_post('uid'));
		$id = $this->pm_m->deleteuidpm($this->user['uid'], $this->input('touids'));
		return $id;
	}
	
	function readstatus() {
		$this->user['uid'] = intval($this->input->get_post('uid'));
		$this->pm_m->set_pm_status($this->user['uid'], $this->input->get_post('uids'), $this->input->get_post('pmids'), $this->input->get_post('status'));
	}

	function ignore() {
		$this->user['uid'] = intval($this->input->get_post('uid'));
		return $this->pm_m->set_ignore($this->user['uid']);
	}

 	function ls() {
 		$pagesize = $this->input->get_post('pagesize');
 		$folder = $this->input->get_post('folder');
 		$filter = $this->input->get_post('filter');
 		$page = $this->input->get_post('page');
 		$folder = in_array($folder, array('newbox', 'inbox', 'searchbox')) ? $folder : 'inbox';
 		if($folder != 'searchbox') {
 			$filter = $filter ? (in_array($filter, array('newpm', 'privatepm', 'systempm', 'announcepm')) ? $filter : '') : '';
 		}
 		$msglen = $this->input->get_post('msglen');
 		$this->user['uid'] = intval($this->input->get_post('uid'));
		if($folder != 'searchbox') {
 			$pmnum = $this->pm_m->get_num($this->user['uid'], $folder, $filter);
 		} else {
 			$pmnum = $pagesize;
 		}
 		if($pagesize > 0) {
	 		$pms = $this->pm_m->get_pm_list($this->user['uid'], $pmnum, $folder, $filter, $page, $pagesize);
	 		if(is_array($pms) && !empty($pms)) {
				foreach($pms as $key => $pm) {
					if($msglen) {
						$pms[$key]['message'] = htmlspecialchars($this->pm_m->removecode($pms[$key]['message'], $msglen));
					} else {
						unset($pms[$key]['message']);
					}
					$pms[$key]['dateline'] = $pms[$key]['dbdateline'];
					unset($pms[$key]['dbdateline'], $pms[$key]['folder']);
				}
			}
			$result['data'] = $pms;
		}
		$result['count'] = $pmnum;
 		return $result;
 	}

 	function viewnode() {
  		$this->user['uid'] = intval($this->input->get_post('uid'));
 		$pmid = $this->pm_m->pmintval($this->input->get_post('pmid'));
 		$type = $this->input->get_post('type');
 		$pm = $this->pm_m->get_pmnode_by_pmid($this->user['uid'], $pmid, $type);
 		if($pm) {
			$this->load->library('uccode');
			$pm['message'] = $this->uccode->complie($pm['message']);
			return $pm;
		}
 	}

 	function view() {
 		$this->user['uid'] = intval($this->input->get_post('uid'));
		$touid = $this->input->get_post('touid');
		$pmid = $this->pm_m->pmintval($this->input->get_post('pmid'));
		$daterange = $this->input->get_post('daterange');
 		if(empty($pmid)) {
	 		$daterange = empty($daterange) ? 1 : $daterange;
	 		$today = $this->time - ($this->time + $this->settings['timeoffset']) % 86400;
	 		if($daterange == 1) {
	 			$starttime = $today;
	 		} elseif($daterange == 2) {
	 			$starttime = $today - 86400;
	 		} elseif($daterange == 3) {
	 			$starttime = $today - 172800;
	 		} elseif($daterange == 4) {
	 			$starttime = $today - 604800;
	 		} elseif($daterange == 5) {
	 			$starttime = 0;
	 		}
	 		$endtime = $this->time;
	 		$pms = $this->pm_m->get_pm_by_touid($this->user['uid'], $touid, $starttime, $endtime);
	 	} else {
	 		$pms = $this->pm_m->get_pm_by_pmid($this->user['uid'], $pmid);
	 	}

 	 	$status = FALSE;
		foreach($pms as $key => $pm) {
			$pms[$key]['message'] = $this->uccode->complie($pms[$key]['message']);
			!$status && $status = $pm['msgtoid'] && $pm['new'];
		}
		$status && $this->pm_m->set_pm_status($this->user['uid'], $touid, $pmid);
		return $pms;
 	}

  	function blackls_get() {
  		$this->user['uid'] = intval($this->input->get_post('uid'));
 		return $this->pm_m->get_blackls($this->user['uid']);
 	}

 	function blackls_set() {
 		$this->user['uid'] = intval($this->input->get_post('uid'));
 		$blackls = $this->input->get_post('blackls');
 		return $this->pm_m->set_blackls($this->user['uid'], $blackls);
 	}

	function blackls_add() {
		$this->user['uid'] = intval($this->input->get_post('uid'));
 		$username = $this->input->get_post('username');
 		return $this->pm_m->update_blackls($this->user['uid'], $username, 1);
 	}

 	function blackls_delete($arr) {
		$this->user['uid'] = intval($this->input->get_post('uid'));
 		$username = $this->input->get_post('username');
 		return $this->pm_m->update_blackls($this->user['uid'], $username, 2);
 	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */