<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Domain_m extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	function add_domain($domain, $ip) {
		if($domain) {
			$this->db->query("INSERT INTO ".UC_DBTABLEPRE."domains SET domain='$domain', ip='$ip'");
		}
		return $this->db->insert_id();
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
		$this->db->query("DELETE FROM ".UC_DBTABLEPRE."domains WHERE id IN ($domainids)");
		return $this->db->affected_rows();
	}

	function update_domain($domain, $ip, $id) {
		$this->db->query("UPDATE ".UC_DBTABLEPRE."domains SET domain='$domain', ip='$ip' WHERE id='$id'");
		return $this->db->affected_rows();
	}

}