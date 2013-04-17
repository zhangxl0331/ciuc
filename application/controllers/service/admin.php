<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	function seccode() {
		$authkey = md5(UC_KEY.$_SERVER['HTTP_USER_AGENT'].$this->onlineip);

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

}

?>