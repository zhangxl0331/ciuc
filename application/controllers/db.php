<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Db extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->check_priv();
		if(!$this->user['isfounder'] && !$this->user['allowadmindb']) {
			$this->message('no_permission_for_this_module');
		}
		$this->check_priv();
		$this->load->model('misc_m');
	}
	
	function ls() {
		$status = 0;
		$operate = $this->input->get_post('o');
		if($operate == 'list') {
			if($delete = $this->input->post('delete')) {
				if(is_array($delete)) {
					foreach($delete AS $filename) {
						@unlink('./data/backup/'.str_replace(array('/', '\\'), '', $filename));
					}
				}
				$status = 2;
				$this->writelog('db_delete', "delete=".implode(',', $delete));
			}
	
			$baklist = array();
			if(is_dir(FCPATH.'data/backup/')) {
				$dir = dir(FCPATH.'data/backup/');
				while($entry = $dir->read()) {
					$file = FCPATH.'data/backup/'.$entry;
					if(is_dir($file) && preg_match("/backup_(\d+)_\w+/i", $file, $match)) {
						$baklist[] = array('name' => $match[0], 'date' => $match[1]);
					}
				}
				$dir->close();
			} else {
				cpmsg('db_export_dest_invalid');
			}
			$data['baklist'] = $baklist;
		} elseif($operate == 'view') {
			$dir = $this->input->get_post('dir');
			$this->load->model('app_m');
			$applist = $this->app_m->get_apps();
			$data['applist'] = $applist;
			$data['dir'] = $dir;
		} elseif($operate == 'ping') {
			$appid = intval($this->input->get_post('appid'));
			$app = $this->cache['apps'][$appid];
			$dir = trim($this->input->get_post('dir'));
			$url = $app['url'].'/api/dbbak.php?apptype='.$app['type'];
			$code = authcode('&method=ping&dir='.$dir.'&time='.time(), 'ENCODE', $app['authkey']);
			$url .= '&code='.urlencode($code);
			$this->load->model('misc_m');
			$res = $this->misc_m->dfopen2($url, 0, '', '', 1, $app['ip'], 20, TRUE);
			if($res == '1') {
				$this->message($this->_parent_js($appid, '<img src="images/correct.gif" border="0" class="statimg" /><span class="green">'.$this->lang['dumpfile_exists'].'</span>').'<script>parent.import_status['.$appid.']=true;</script>');
			} else {
				$this->message($this->_parent_js($appid, '<img src="images/error.gif" border="0" class="statimg" /><span class="red">'.$this->lang['dumpfile_not_exists'].'</span>').'<script>parent.import_status['.$appid.']=false;</script>');
			}
			exit;
		} else {
			$this->load->model('app_m');
			$applist = $this->app_m->get_apps();
			$data['applist'] = $applist;
			$data['dir'] = 'backup_'.date('ymd', time()).'_'.$this->random(6);
		}
		$data['operate'] = $operate;
		$this->load->view('db', $data);
	}

	function operate() {
		$this->load->library('xml');
		$nexturl = $this->input->get_post('nexturl');
		$appid = intval($this->input->get_post('appid'));
		$type = $this->input->get_post('t') == 'import' ? 'import' : 'export';
		$backupdir = $this->input->get_post('backupdir');
		$app = isset($this->caches['apps'][$appid]) && $this->caches['apps'][$appid];
		if($nexturl) {
			$url = $nexturl;
		} else {
			if($appid) {
				if(!isset($this->caches['apps'][$appid])) {
					$this->message($this->_parent_js($appid, 'appid_invalid'));
				}
				$url = $app['url'].'/api/dbbak.php?apptype='.$app['type'];
				$code = $this->authcode('&method='.$type.'&sqlpath='.$backupdir.'&time='.time(), 'ENCODE', $app['authkey']);
			} else {
				$url = 'http://'.$this->input->server('HTTP_HOST').str_replace('admin.php', 'api/dbbak.php', $this->input->server('PHP_SELF')).'?apptype=UCENTER';
				$code = authcode('&method='.$type.'&sqlpath='.$backupdir.'&time='.time(), 'ENCODE', UC_KEY);
			}
			$url .= '&code='.urlencode($code);
		}
		if(empty($appid)) {
			$app['ip'] = defined('UC_IP') ? UC_IP : '';
		}
		$this->load->model('misc_m');
		$res = $this->misc_m->dfopen2($url, 0, '', '', 1, $app['ip'], 20, TRUE);
		if(empty($res)) {
			$this->message($this->_parent_js($appid, 'db_back_api_url_invalid'));
		}
		$arr = $this->_xml2array($res);

		if(empty($arr['fileinfo'])) {
			$this->message($this->_parent_js($appid, 'undefine_error'));
		} elseif($arr['error']['errorcode']) {
			$this->message($this->_parent_js($appid, 'dbback_error_code_'.$arr['error']['errorcode']));
		} elseif($arr['nexturl']) {
			$this->message($this->_parent_js($appid, 'db_'.$type.'_multivol_redirect', array('$volume' => $arr['fileinfo']['file_num'])), 'admin.php?m=db&a=operate&t='.$type.'&appid='.$appid.'&nexturl='.urlencode($arr['nexturl']));
		} elseif(empty($arr['nexturl'])) {
			$this->message($this->_parent_js($appid, 'db_'.$type.'_multivol_succeed'));
		} else {
			$this->message($this->_parent_js($appid, 'undefine_error'));
		}
		exit;
	}

	function delete() {
		require_once UC_ROOT.'lib/xml.class.php';
		$appid = intval($this->input->get_post('appid'));
		$backupdir = $this->input->get_post('backupdir');
		$app = $this->cache['apps'][$appid];
		if(empty($appid)) {
			$app['ip'] = defined('UC_IP') ? UC_IP : '';
			$url = 'http://'.$this->input->server('HTTP_HOST').str_replace('admin.php', 'api/dbbak.php', $this->input->server('PHP_SELF')).'?apptype=UCENTER';
			$code = $this->authcode('&method=delete&sqlpath='.$backupdir.'&time='.time(), 'ENCODE', UC_KEY);
			$appname = 'UCenter';
		} else {
			if(!isset($this->cache['apps'][$appid])) {
				$this->message($this->_parent_js($appid, 'appid_invalid'));
			}	
			$url = $app['url'].'/api/dbbak.php?apptype='.$app['type'];
			$code = $this->authcode('&method=delete&sqlpath='.$backupdir.'&time='.time(), 'ENCODE', $app['authkey']);
			$appname = $app['name'];
		}
		$url .= '&code='.urlencode($code);
		$this->load->model('misc_m');
		$res = $this->misc_m->dfopen2($url, 0, '', '', 1, $app['ip'], 20, TRUE);
		$next_appid = $this->_next_appid($appid);
		if($next_appid != $this->_next_appid($next_appid)) {
			$this->message($this->_parent_js($backupdir, 'delete_dumpfile_redirect', array('$appname' => $appname)), 'admin.php?m=db&a=delete&appid='.$next_appid.'&backupdir='.$backupdir);
		} else {
			$this->message($this->_parent_js($backupdir, 'delete_dumpfile_success'));
		}
	}

	function _next_appid($appid) {
		$last_appid = 0;
		foreach($this->cache['apps'] as $key => $val) {
			if($appid == $last_appid) {
				return $key;
			}
			$last_appid = $key;
		}
		return $last_appid;
	}

	function _parent_js($extid, $message, $vars = array()) {
		$this->load->language('messages');
 		if($this->lang->line($message)) {
 			$message = $this->lang->line($message) ? str_replace(array_keys($vars), array_values($vars), $this->lang->line($message)) : $message;
 		}
 		return '<script type="text/javascript">parent.show_status(\''.$extid.'\', \''.$message.'\');</script>';
	}

	function _xml2array($xml) {
		$arr = xml_unserialize($xml, 1);
		preg_match('/<error errorCode="(\d+)" errorMessage="([^\/]+)" \/>/', $xml, $match);
		$arr['error'] = array('errorcode' => $match[1], 'errormessage' => $match[2]);
		return $arr;
	}

	function random($length, $numeric = 0) {
		PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
		if($numeric) {
			$hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
		} else {
			$hash = '';
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
			$max = strlen($chars) - 1;
			for($i = 0; $i < $length; $i++) {
				$hash .= $chars[mt_rand(0, $max)];
			}
		}
		return $hash;
	}

	function sqldumptable($table, $startfrom = 0, $currsize = 0) {
		$offset = 300;
		$tabledump = '';
		$usehex = TRUE;
		$tablefields = array();

		$query = $this->db->query("SHOW FULL COLUMNS FROM $table", 'SILENT');
		if(!$query && $this->db->errno() == 1146) {
			return;
		} elseif(!$query) {
			$usehex = FALSE;
		} else {
			while($fieldrow = $this->db->fetch_array($query)) {
				$tablefields[] = $fieldrow;
			}
		}
		if(!$startfrom) {
			$createtable = $this->db->query("SHOW CREATE TABLE $table", 'SILENT');
			if(!$this->db->error()) {
				$tabledump = "DROP TABLE IF EXISTS $table;\n";
			} else {
				return '';
			}
			$create = $this->db->fetch_row($createtable);
			$tabledump .= $create[1];

			$tablestatus = $this->db->fetch_first("SHOW TABLE STATUS LIKE '$table'");
			$tabledump .= ($tablestatus['Auto_increment'] && strpos($create[1], 'AUTO_INCREMENT') === FALSE ? " AUTO_INCREMENT=$tablestatus[Auto_increment]" : '').";\n\n";
		}

		$tabledumped = 0;
		$numrows = $offset;
		$firstfield = $tablefields[0];

		while($currsize + strlen($tabledump) + 500 < $this->sizelimit * 1000 && $numrows == $offset) {
			if($firstfield['Extra'] == 'auto_increment') {
				$selectsql = "SELECT * FROM $table WHERE $firstfield[Field] > $startfrom LIMIT $offset";
			} else {
				$selectsql = "SELECT * FROM $table LIMIT $startfrom, $offset";
			}
			$tabledumped = 1;
			$rows = $this->db->query($selectsql);
			$numfields = $this->db->num_fields($rows);

			$numrows = $this->db->num_rows($rows);
			while($row = $this->db->fetch_row($rows)) {
				$comma = $t = '';
				for($i = 0; $i < $numfields; $i++) {
					$t .= $comma.($usehex && !empty($row[$i]) && (strpos($tablefields[$i]['Type'], 'char') !== FALSE || strpos($tablefields[$i]['Type'], 'text') !== FALSE) ? '0x'.bin2hex($row[$i]) : '\''.mysql_escape_string($row[$i]).'\'');
					$comma = ',';
				}
				if(strlen($t) + $currsize + strlen($tabledump) + 500 < $this->sizelimit * 1000) {
					if($firstfield['Extra'] == 'auto_increment') {
						$startfrom = $row[0];
					} else {
						$startfrom++;
					}
					$tabledump .= "INSERT INTO $table VALUES ($t);\n";
				} else {
					$this->complete = FALSE;
					break 2;
				}
			}
		}

		$this->startrow = $startfrom;
		$tabledump .= "\n";

		return $tabledump;
	}

	function splitsql($sql) {
		$sql = str_replace("\r", "\n", $sql);
		$ret = array();
		$num = 0;
		$queriesarray = explode(";\n", trim($sql));
		unset($sql);
		foreach($queriesarray as $query) {
			$queries = explode("\n", trim($query));
			foreach($queries as $query) {
				$ret[$num] .= $query[0] == "#" ? NULL : $query;
			}
			$num++;
		}
		return($ret);
	}

	function syntablestruct($sql, $version, $dbcharset) {

		if(strpos(trim(substr($sql, 0, 18)), 'CREATE TABLE') === FALSE) {
			return $sql;
		}

		$sqlversion = strpos($sql, 'ENGINE=') === FALSE ? FALSE : TRUE;

		if($sqlversion === $version) {

			return $sqlversion && $dbcharset ? preg_replace(array('/ character set \w+/i', '/ collate \w+/i', "/DEFAULT CHARSET=\w+/is"), array('', '', "DEFAULT CHARSET=$dbcharset"), $sql) : $sql;
		}

		if($version) {
			return preg_replace(array('/TYPE=HEAP/i', '/TYPE=(\w+)/is'), array("ENGINE=MEMORY DEFAULT CHARSET=$dbcharset", "ENGINE=\\1 DEFAULT CHARSET=$dbcharset"), $sql);

		} else {
			return preg_replace(array('/character set \w+/i', '/collate \w+/i', '/ENGINE=MEMORY/i', '/\s*DEFAULT CHARSET=\w+/is', '/\s*COLLATE=\w+/is', '/ENGINE=(\w+)(.*)/is'), array('', '', 'ENGINE=HEAP', '', '', 'TYPE=\\1\\2'), $sql);
		}
	}

	function sizecount($filesize) {
		if($filesize >= 1073741824) {
			$filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
		} elseif($filesize >= 1048576) {
			$filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
		} elseif($filesize >= 1024) {
			$filesize = round($filesize / 1024 * 100) / 100 . ' KB';
		} else {
			$filesize = $filesize . ' Bytes';
		}
		return $filesize;
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */