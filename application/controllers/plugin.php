<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Plugin extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->check_priv();
		if(!$this->user['isfounder']) {
			$this->message('no_permission_for_this_module');
		}
		$a = $this->input->get_post('a');
		$this->load->model('plugin_m');
		$this->plugin = $this->plugin_m->get_plugin($a);
		$this->plugins = $this->plugin_m->get_plugins();
		if(empty($this->plugin)) {
			$this->message('read_plugin_invalid');
		}
		$this->load->vars('plugin', $this->plugin);
		$this->load->vars('plugins', $this->plugins);
		$this->view->languages = $this->plugin['lang'];
		$this->view->tpldir = UC_ROOT.'./plugin/'.$a;
		$this->view->objdir = UC_DATADIR.'./view';
	}
	
	function _call($a, $arg) {
		$do = $this->input->get_post('do');
		$do = empty($do) ? 'onindex' : 'on'.$do;
		if(method_exists($this, $do) && $do{0} != '_') {
			$this->$do();
		} else {
			exit('Plugin module not found');
		}
	}
}
$a = $this->input->get_post('a');
$do = $this->input->get_post('do');
if(!preg_match("/^[\w]{1,64}$/", $a)) {
	exit('Argument Invalid');
}
if(!@require_once UC_ROOT."./plugin/$a/plugin.php") {
	exit('Plugin not found');
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */