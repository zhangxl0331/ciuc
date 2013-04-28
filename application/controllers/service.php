<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Service extends MY_Controller {

	public function __construct()
	{
		parent::__construct();

	}
	
	function seccode() {
		$authkey = md5(UC_KEY.$this->input->user_agent().$this->input->ip_address());

		$this->time = time();
		$seccodeauth = $this->input->get_post('seccodeauth');
		$seccode = authcode($seccodeauth, 'DECODE', $authkey);
		
		//$seccode = rand(100000, 999999);
		//$this->setcookie('uc_secc', $this->authcode($seccode."\t".$this->time, 'ENCODE'));

		@header("Expires: -1");
		@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
		@header("Pragma: no-cache");

		$this->load->library('seccode');
		$this->seccode->code = $seccode;
		$this->seccode->type = 0;
		$this->seccode->width = 70;
		$this->seccode->height = 21;
		$this->seccode->background = 0;
		$this->seccode->adulterate = 1;
		$this->seccode->ttf = 1;
		$this->seccode->angle = 0;
		$this->seccode->color = 1;
		$this->seccode->size = 0;
		$this->seccode->shadow = 1;
		$this->seccode->animator = 0;
		$this->seccode->fontpath = FCPATH.'images/fonts/';
		$this->seccode->datapath = FCPATH.'images/';
		$this->seccode->includepath = '';
		$this->seccode->display();
	}
	
	function avatar()
	{
		$uid = intval($this->input->get('uid'));
		$size = $this->input->get('size');
		$random = $this->input->get('random');
		$type = $this->input->get('type');
		$check =$this->input->get('check_file_exists');
		
		$avatar = 'data/avatar/'.get_avatar($uid, $size, $type);
		if(file_exists(FCPATH.$avatar)) {
			if($check) {
				echo 1;
				exit;
			}
			$random = !empty($random) ? rand(1000, 9999) : '';
			$avatar_url = empty($random) ? $avatar : $avatar.'?random='.$random;
		} else {
			if($check) {
				echo 0;
				exit;
			}
			$size = in_array($size, array('big', 'middle', 'small')) ? $size : 'middle';
			$avatar_url = 'images/noavatar_'.$size.'.gif';
		}
		
		if(empty($random)) {
			header("HTTP/1.1 301 Moved Permanently");
			header("Last-Modified:".date('r'));
			header("Expires: ".date('r', time() + 86400));
		}
		
		header('Location: '.$this->config->base_url($avatar_url));
		exit;
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */