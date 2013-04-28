<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pm extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->check_priv();
		if(!$this->user['isfounder'] && !$this->user['allowadminpm']) {
			$this->message('no_permission_for_this_module');
		}
		$this->load->model('pm_m');
		
	}
	
	function ls() {
		$folder = 'inbox';
		$filter = 'announcepm';
		$status = 0;
		$delete = $this->input->post('delete');
		if(submitcheck()) {
			$delnum = $this->pm_m->deletepm($this->user['uid'], $delete);
			$status = 1;
			$this->writelog('pm_delete', "delete=".implode(',', $delete));
		}
		$pmnum = $this->db->where(array('msgtoid'=>'0', 'folder'=>'inbox'))->get('pms')->num_rows();
		$pmlist = $this->pm_m->get_pm_list($this->user['uid'], $pmnum, $folder, $filter, $_GET['page']);
		$multipage = page($pmnum, 10, $_GET['page'], 'admin.php?m=pm&a=ls');
		$extra = 'extra='.rawurlencode($this->input->get('extra'));

		$data['status'] = $status;
		$data['pmlist'] = $pmlist;
		$data['extra'] = $extra;
		$data['multipage'] = $multipage;

		$this->load->view('pm', $data);
	}

	function view() {
		$pmid = @is_numeric($this->input->get('pmid')) ? $this->input->get('pmid') : 0;
		$pms = $this->pm_m->get_pm_by_pmid($this->user['uid'], $pmid);

		if($pms[0]) {
			$pms = $pms[0];
			require_once UC_ROOT.'lib/uccode.class.php';
			$this->uccode = new uccode();
			$this->uccode->lang = &$this->lang;
			$pms['message'] = $this->uccode->complie($pms['message']);
			$pms['dateline'] = $this->date($pms['dateline']);
		}

		$extra = 'extra='.rawurlencode($this->input->get('extra'));

		$data['pms'] = $pms;
		$data['extra'] = $extra;

		$this->load->view('pm', $data);
	}

	function send() {
		$status = 0;
		$subject = $this->input->post('subject');
		$message = $this->input->post('message');
		if(submitcheck()) {
			$lastpmid = $this->pm_m->sendpm($subject, $message, $this->user['isfounder'] ? '' : $this->user, 0);
			$status = 1;
			$this->writelog('pm_send', "subject=".htmlspecialchars($subject));
		}
		$data['status'] = $status;
		$this->load->view('pm_send', $data);
	}

	function clear() {
		$delnum = 0;
		$status = 0;
		if(submitcheck()) {
			$cleardays = intval($this->input->post('cleardays'));
			$unread = $this->input->get_post('unread') ? 1 : 0;
			$usernames = trim($this->input->post('usernames'));
			$sqladd = '';
			if($cleardays > 0) {
				$sqladd .= ' AND dateline < '.($this->time - $cleardays * 86400);
			}
			if($unread) {
				$sqladd .= " AND new='0'";
			}
			if($usernames) {
				$uids = 0;
				$usernames = "'".implode("', '", explode(',', $usernames))."'";
				$rows = $this->db->select('uid')->where_in('username', $usernames)->get('members')->result_array();
				foreach($rows as $res) {
					$uids .= ','.$res['uid'];
				}
				if($uids) {
					$sqladd .= " AND (msgfromid IN ($uids) OR msgtoid IN ($uids))";
				}
			}
			
			if($sqladd) {
				$this->db->query("DELETE FROM ".UC_DBTABLEPRE."pms WHERE 1 $sqladd", 'UNBUFFERED');
				$delnum = $this->db->affected_rows();
				$status = 1;
				$this->writelog('pm_clear', "cleardays=$cleardays&unread=$unread");
			}
		}

		$pmnum = $this->db->count_all_results('pms');
		$data['pmnum'] = $pmnum;
		$data['delnum'] = $delnum;
		$data['status'] = $status;
		$this->load->view('pm_clear', $data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */