<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Badword extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		if(!$this->user['isfounder'] && !$this->user['allowadminbadword']) {
			$this->message('no_permission_for_this_module');
		}
		$this->load->model('badword_m');
		$this->load->library('xml');
	}
	
	function ls() {
		$page = $this->input->get_post('page');
		$find = $this->input->post('find');
		$replacement = $this->input->post('replacement');
		$replacementnew = $this->input->post('replacementnew');
		$findnew = $this->input->post('findnew');
		$delete = $this->input->post('delete');
		if($find) {
			foreach($find as $id => $arr) {
				$this->badword_m->update_badword($find[$id], $replacement[$id], $id);
			}
		}
		$status = 0;
		if($findnew) {
			$this->badword_m->add_badword($findnew, $replacementnew, $this->user['username']);
			$status = 1;
			$this->writelog('badword_add', 'findnew='.htmlspecialchars($findnew).'&replacementnew='.htmlspecialchars($replacementnew));
		}
		if(@$delete) {

			$this->badword_m->delete_badword($delete);
			$status = 2;
			$this->writelog('badword_delete', "delete=".implode(',', $delete));
		}
		if($this->input->post('multisubmit')) {
			$badwords = $this->input->post('badwords');
			$type = $this->input->post('type');
			if($type == 0) {
				$this->badword_m->truncate_badword();
				$type = 1;
			}
			$arr = explode("\n", str_replace(array("\r", "\n\n"), array("\r", "\n"), $badwords));
			foreach($arr as $k => $v) {
				$arr2 = explode("=", $v);
				$this->badword_m->add_badword($arr2[0], $arr2[1], $this->user['username'], $type);
			}
		}
		if($status > 0) {
			$notedata = $this->badword_m->get_list($page, 1000000, 1000000);
			$this->load->model('note_m');
			$this->note_m->add('updatebadwords', '', $this->xml->serialize($notedata, 1));
			$this->note_m->send();

			$this->load->model('cache_m');
			$this->cache_m->updatedata('badwords');
		}
		$num = $this->badword_m->get_total_num();
		$badwordlist = $this->badword_m->get_list($page, UC_PPP, $num);
		$multipage = page($num, UC_PPP, $page, 'admin.php?m=badword&a=ls');

		$data['status'] = $status;
		$data['badwordlist'] = $badwordlist;
		$data['multipage'] = $multipage;

		$this->load->view('badword', $data);

	}

	function export() {
		$data = $this->badword_m->get_list(1, 1000000, 1000000);
		$s = '';
		if($data) {
			foreach($data as $v) {
				$s .= $v['find'].'='.$v['replacement']."\r\n";
			}
		}
		@header('Content-Disposition: inline; filename=CensorWords.txt');
		@header("Content-Type: text/plain");
		echo $s;

	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */