<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Badword_m extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
function add_badword($find, $replacement, $admin, $type = 1) {
		if($find) {
			$find = trim($find);
			$replacement = trim($replacement);
			$findpattern = $this->pattern_find($find);
			if($type == 1) {
				$this->db->query("REPLACE INTO ".UC_DBTABLEPRE."badwords SET find='$find', replacement='$replacement', admin='$admin', findpattern='$findpattern'");
			} elseif($type == 2) {
				$this->db->query("INSERT INTO ".UC_DBTABLEPRE."badwords SET find='$find', replacement='$replacement', admin='$admin', findpattern='$findpattern'", 'SILENT');
			}
		}
		return $this->db->insert_id();
	}

	function get_total_num() {
		$data = $this->db->count_all_results('badwords');
		return $data;
	}

	function get_list($page, $ppp, $totalnum) {
		$start = page_get_start($page, $ppp, $totalnum);
		$data = $this->db->get('badwords', $ppp, $start)->result_array();
		return $data;
	}

	function delete_badword($arr) {
		$badwordids = $this->base->implode($arr);
		$this->db->query("DELETE FROM ".UC_DBTABLEPRE."badwords WHERE id IN ($badwordids)");
		return $this->db->affected_rows();
	}

	function truncate_badword() {
		$this->db->query("TRUNCATE ".UC_DBTABLEPRE."badwords");
	}

	function update_badword($find, $replacement, $id) {
		$findpattern = $this->pattern_find($find);
		$this->db->query("UPDATE ".UC_DBTABLEPRE."badwords SET find='$find', replacement='$replacement', findpattern='$findpattern' WHERE id='$id'");
		return $this->db->affected_rows();
	}

	function pattern_find($find) {
		$find = preg_quote($find, "/'");
		$find = str_replace("\\", "\\\\", $find);
		$find = str_replace("'", "\\'", $find);
		return '/'.preg_replace("/\\\{(\d+)\\\}/", ".{0,\\1}", $find).'/is';
	}

}