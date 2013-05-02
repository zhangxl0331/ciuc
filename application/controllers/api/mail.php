<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mail extends MY_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('mail_m');
	}
	
	function add() {
		$mail = array();
		$mail['appid']		= $this->app['appid'];
		$mail['uids']		= explode(',', $this->input->get_post('uids'));
		$mail['emails']		= explode(',', $this->input->get_post('emails'));
		$mail['subject']	= $this->input->get_post('subject');
		$mail['message']	= $this->input->get_post('message');
		$mail['charset']	= $this->input->get_post('charset');
		$mail['htmlon']		= intval($this->input->get_post('htmlon'));
		$mail['level']		= abs(intval($this->input->get_post('level')));
		$mail['frommail']	= $this->input->get_post('frommail');
		$mail['dateline']	= time();
		return $this->mail_m->add($mail);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */