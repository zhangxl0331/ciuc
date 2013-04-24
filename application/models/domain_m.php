<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Domain_m extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	function add_domain($domain, $ip) {
		if($domain) {
			return $this->db->insert('domains', array('domain'=>$domain, 'ip'=>$ip));
		}
	}

	function get_total_num() {
		$data = $this->db->count_all_results('domains');
		return $data;
	}

	function get_list($page, $ppp, $totalnum) {
		$start = page_get_start($page, $ppp, $totalnum);
		$data = $this->db->get('domains', $ppp, $start)->result_array();
		return $data;
	}

	function delete_domain($arr) {
		$domainids = $this->base->implode($arr);
		return $this->db->delete('domains', array('id IN'=>$domainids));
	}

	function update_domain($domain, $ip, $id) {
		return $this->db->update('domains', array('domain'=>$domain, 'ip'=>$ip), array('id'=>$id));
	}

}