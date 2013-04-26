<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pm_m extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	function pmintval($pmid) {
		return @is_numeric($pmid) ? $pmid : 0;
	}

	function get_pm_by_pmid($uid, $pmid) {
		$arr = $this->db->where('pmid', $pmid)->where_in('msgtoid', $uid)->or_where('msgfromid', $uid)->get('pms')->result_array();
		return $arr;
	}

	function get_pm_by_touid($uid, $touid, $starttime, $endtime) {
		$arr1 = $this->db->where("msgfromid='$uid' AND msgtoid='$touid' AND dateline>='$starttime' AND dateline<'$endtime' AND related>'0' AND delstatus IN (0,2)")->order_by('dateline')->get('pms')->result_array();
		$arr2 = $this->db->where("msgfromid='$touid' AND msgtoid='$uid' AND dateline>='$starttime' AND dateline<'$endtime' AND related>'0' AND delstatus IN (0,1)")->order_by('dateline')->get('pms')->result_array();
		$arr = array_merge($arr1, $arr2);
		uasort($arr, 'pm_datelinesort');
		return $arr;
	}

	function get_pmnode_by_pmid($uid, $pmid, $type = 0) {
		$arr = array();
		if($type == 1) {
			$arr = $this->db->where(array('msgfromid'=>$uid, 'folder'=>'inbox'))->order_by('dateline DESC')->get('pms', 1)->first_row();
		} elseif($type == 2) {
			$arr = $this->db->where(array('msgtoid'=>$uid, 'folder'=>'inbox'))->order_by('dateline DESC')->get('pms', 1)->first_row();
		} else {
			$arr = $this->db->where('pmid', $pmid)->get('pms')->first_row();
		}
		return $arr;
	}

	function set_pm_status($uid, $touid, $pmid = 0, $status = 0) {
		if(!$status) {
			$oldstatus = 1;
			$newstatus = 0;
		} else {
			$oldstatus = 0;
			$newstatus = 1;
		}
		if($touid) {
			$ids = is_array($touid) ? $this->base->implode($touid) : $touid;
			$this->db->update('pms', array('new'=>$newstatus), array('msgfromid IN'=>$ids, 'msgtoid'=>$uid, 'new'=>$oldstatus));
		}
		if($pmid) {
			$ids = is_array($pmid) ? $this->base->implode($pmid) : $pmid;
			$this->db->update('pms', array('new'=>$newstatus), array('pmid IN'=>$ids, 'msgtoid'=>$uid, 'new'=>$oldstatus));
		}
	}

	function get_pm_num($uid, $folder, $filter, $a) {
		$folder = $folder;
		$get_pm_num = 0;
		$pm_num = isset($_COOKIE['uc_pmnum']) && ($pm_num = explode('|', $_COOKIE['uc_pmnum'])) && $pm_num[0] == $uid ? $pm_num : array(0,0,0,0);
		switch($folder) {
			case 'newbox':
				$get_pm_num = $this->get_num($uid, 'newbox');
				break;
			case 'inbox':
				if(!$filter && $a != 'view') {
					$get_pm_num = $this->get_num($uid, 'inbox');
				} else {
					$get_pm_num = $pm_num[1];
				}
				break;
		}
		if($a == 'ls') {
			$get_announcepm_num = $this->get_num($uid, 'inbox', 'announcepm');
			$get_systempm_num = $this->get_num($uid, 'inbox', 'systempm');
			$get_newinbox_num = $this->get_num($uid, 'inbox', 'newpm');
		} else {
			list(, $get_newinbox_num, $get_systempm_num, $get_announcepm_num) = $pm_num;
		}
		if($pm_num[2] != $get_newinbox_num || $pm_num[3] != $get_systempm_num || $pm_num[4] != $get_announcepm_num) {
			header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
			$this->base->setcookie('uc_pmnum', $uid.'|'.$get_pm_num.'|'.$get_systempm_num.'|'.$get_announcepm_num, 3600);
		}
		return array($get_pm_num, $get_newinbox_num, 0, $get_systempm_num, $get_announcepm_num);
	}

	function get_num($uid, $folder, $filter = '') {
		switch($folder) {
			case 'newbox':
				$num = $this->db->where(array('msgtoid'=>$uid, 'related'=>0, 'msgfromid>='=>0, 'folder'=>'inbox', 'new'=>'1'))->get('pms')->num_rows();
				return $num;
			case 'inbox':
				if($filter == 'newpm') {
					$num = $this->db->where(array('msgtoid'=>$uid, 'related'=>0, 'msgfromid>='=>0, 'folder'=>'inbox', 'new'=>'1'))->get('pms')->num_rows();
				} elseif($filter == 'systempm') {
					$num = $this->db->where(array('msgtoid'=>$uid, 'msgfromid'=>0, 'folder'=>'inbox'))->get('pms')->num_rows();
				} elseif($filter == 'privatepm') {
					$num = $this->db->where(array('msgtoid'=>$uid, 'related'=>0, 'msgfromid>'=>0, 'folder'=>'inbox'))->get('pms')->num_rows();
				} elseif($filter == 'announcepm') {
					$num = $this->db->where(array('msgtoid'=>0, 'folder'=>'inbox'))->get('pms')->num_rows();
				} else {
					$num = $this->db->where(array('msgtoid'=>$uid, 'related'=>0, 'folder'=>'inbox'))->get('pms')->num_rows();
				}
				break;
			case 'savebox':
				$num = $this->db->where(array('msgfromid'=>$uid, 'related'=>0, 'folder'=>'outbox'))->get('pms')->num_rows();
				break;
		}
		return $num;
	}

	function get_pm_list($uid, $pmnum, $folder, $filter, $page, $ppp = 10, $new = 0) {
		$ppp = $ppp ? $ppp : 10;
		if($folder != 'searchbox') {
 			$start_limit = page_get_start($page, $ppp, $pmnum);
 		} else {
 			$start_limit = ($page - 1) * $ppp;
 		}
		switch($folder) {
			case 'newbox':
				$folder = 'inbox';
				$filter = 'newpm';
			case 'inbox':
				if($filter == 'newpm') {
					$filteradd = array('pm.msgtoid'=>$uid, 'pm.related'=>'0', 'pm.msgfromid>='=>'0', 'pm.folder'=>'inbox', 'pm.new'=>'1');
				} elseif($filter == 'systempm') {
					$filteradd = array('pm.msgtoid'=>$uid, 'pm.msgfromid'=>'0', 'pm.folder'=>'inbox');
				} elseif($filter == 'privatepm') {
					$filteradd = array('pm.msgtoid'=>$uid, 'pm.related'=>'0', 'pm.msgfromid>'=>'0', 'pm.folder'=>'inbox');
				} elseif($filter == 'announcepm') {
					$filteradd = array('pm.msgtoid'=>'0', 'pm.folder'=>'inbox');
				} else {
					$filteradd = array('pm.msgtoid'=>$uid, 'pm.related'=>'0', 'pm.folder'=>'inbox');
				}

				$rows = $this->db->select('pm.*,m.username as msgfrom')->from('pms pm')->join('members m', 'pm.msgfromid = m.uid', 'LEFT')->where($filteradd)->order_by('pm.dateline DESC')->get('', $ppp, $start_limit)->result_array();
				break;
			case 'searchbox':
				$filteradd = array('msgtoid'=>$uid, 'folder'=>'inbox', 'message LIKE'=>'%'.(str_replace('_', '\_', addcslashes($filter, '%_'))).'%');
				$rows = $this->db->where($filteradd)->order_by('dateline DESC')->get('pms', $ppp, $start_limit)->result_array();
				break;
			case 'savebox':
				$rows = $this->db->select('p.*, m.username AS msgto')->from('pms p')->join('members m', 'm.uid=p.msgtoid')->where("p.related='0' AND p.msgfromid='$uid' AND p.folder='outbox'")->order_by('p.dateline DESC')->get('', $ppp, $start_limit)->result_array();
				break;
		}
		$array = array();
		$today = time() - (time() + $this->settings['timeoffset']) % 86400;
		foreach($rows as $data) {
			$daterange = 5;
			if($data['dateline'] >= $today) {
				$daterange = 1;
			} elseif($data['dateline'] >= $today - 86400) {
				$daterange = 2;
			} elseif($data['dateline'] >= $today - 172800) {
				$daterange = 3;
			} elseif($data['dateline'] >= $today - 604800) {
				$daterange = 4;
			}
			$data['daterange'] = $daterange;
			$data['daterangetext'] = !empty($this->lang['pm_daterange_'.$daterange]) ? $this->lang['pm_daterange_'.$daterange] : $daterange;
			$data['dbdateline'] = $data['dateline'];
			$data['datelinetime'] = $this->base->date($data['dateline'], 1);
			$data['dateline'] = $this->base->date($data['dateline']);
			$data['subject'] = $data['subject'] != '' ? htmlspecialchars($data['subject']) : $this->lang['pm_notitle'];
			$data['newstatus'] = $data['new'];
			$data['touid'] = $data['avataruid'] = $uid == $data['msgfromid'] ? $data['msgtoid'] : $data['msgfromid'];
			$data['message'] = $this->removecode($data['message'], 80);
			$array[] = $data;
		}
		if(in_array($folder, array('inbox', 'outbox'))) {
			$this->db->delete('newpm', array('uid'=>$uid));
		}
		return $array;
	}

	function sendpm($subject, $message, $msgfrom, $msgto, $pmid = 0, $savebox = 0, $related = 0) {
		$_CACHE['badwords'] = $this->base->cache('badwords');
		if($_CACHE['badwords']['findpattern']) {
			$subject = @preg_replace($_CACHE['badwords']['findpattern'], $_CACHE['badwords']['replace'], $subject);
			$message = @preg_replace($_CACHE['badwords']['findpattern'], $_CACHE['badwords']['replace'], $message);
		}

		if($savebox && $pmid) {
			$this->db->update('pms', array('msgtoid'=>$msgto, 'subject'=>$subject, 'dateline'=>$this->base->time, 'related'=>$related, 'message'=>$message),
				array('pmid'=>$pmid, 'folder'=>'outbox', 'msgfromid'=>$msgfrom['uid']));
		} else {
			if($msgfrom['uid'] && $msgfrom['uid'] == $msgto) {
				return 0;
			}
			$box = $savebox ? 'outbox' : 'inbox';
			$subject = trim($subject);
			if($subject == '' && !$related) {
				$subject = $this->removecode(trim($message), 75);
			} else {
				$subject = $this->base->cutstr(trim($subject), 75, ' ');
			}

			if($msgfrom['uid']) {
				if($msgto) {
					$sessionexist = $this->db->where(array('msgfromid'=>$msgfrom[uid], 'msgtoid'=>$msgto, 'folder'=>'inbox', 'related'=>'0'))->get('pms')->num_rows();
				} else {
					$sessionexist = 0;
				}
				if(!$sessionexist || $sessionexist > 1) {
					if($sessionexist > 1) {
						$this->db->insert('pms', array('msgfromid'=>$msgfrom[uid], 'msgtoid'=>$msgto, 'folder'=>'inbox', 'related'=>'0'));
					}
					$this->db->insert('pms',
							 array('msgfrom'=>$msgfrom['username'],
							 		'msgfromid'=>$msgfrom['uid'],
							 		'msgtoid'=>$msgto,
							 		'folder'=>$box,
							 		'new'=>1,
							 		'subject'=>$subject,
							 		'dateline'=>$this->base->time,
							 		'related'=>0,
							 		'message'=>$message,
							 		'fromappid'=>$this->base->app['appid']
							 )
					);
					$lastpmid = $this->db->insert_id();
				} else {
					$this->db->update('pms', array('subject'=>$subject, 'message'=>$message, 'dateline'=>$this->base->time, 'new'=>'1', 'fromappid'=>$this->base->app['appid']),
						array('msgfromid'=>$msgfrom[uid], 'msgtoid'=>$msgto, 'folder'=>'inbox', 'related'=>'0'));
				}
				if($msgto && !$savebox) {
					$sessionexist = $this->db->where(array('msgfromid'=>$msgto, 'msgtoid'=>$msgfrom[uid], 'folder'=>'inbox', 'related'=>'0'))->get('pms')->num_rows();
					if($msgfrom['uid'] && !$sessionexist) {
						$this->db->insert('pms',
								 array('msgfrom'=>$msgfrom['username'],
								 		'msgfromid'=>$msgto,
								 		'msgtoid'=>$msgfrom['uid'],
								 		'folder'=>$box,
								 		'new'=>0,
								 		'subject'=>$subject,
								 		'dateline'=>$this->base->time,
								 		'related'=>0,
								 		'message'=>$message,
								 		'fromappid'=>o
								 )
						);
					}
					$this->db->insert('pms', 
							array(
									'msgfrom'=>$msgfrom['username'],
									'msgfromid'=>$msgfrom['uid'],
									'msgtoid'=>$msgto,
									'folder'=>$box,
									'new'=>1,
									'subject'=>$subject,
									'dateline'=>$this->base->time,
									'related'=>$msgfrom['uid'] ? 1 : 0,
									'message'=>$message,
									'fromappid'=>$this->base->app['appid']
									));
					$lastpmid = $this->db->insert_id();
				}
				if($msgto) {
					$this->db->replace('newpm', array('uid'=>$msgto));
				}
			} else {
				$this->db->insert('pms', 
						array(
								'msgfrom'=>$msgfrom['username'],
								'msgfromid'=>$msgfrom['uid'],
								'msgtoid'=>$msgto,
								'folder'=>$box,
								'new'=>1,
								'subject'=>$subject,
								'dateline'=>$this->base->time,
								'related'=>0,
								'message'=>$message,
								'fromappid'=>$this->base->app['appid']
								));
				$lastpmid = $this->db->insert_id();
			}
		}
		return $lastpmid;
	}

	function set_ignore($uid) {
		$this->db->delete('newpm', array('uid'=>$uid));
	}

	function check_newpm($uid, $more) {
		if($more < 2) {
			$newpm = $this->db->where('uid', $uid)->get('newpm')->num_rows();
			if($newpm) {
				$newpm = $this->db->where(array('related'=>'0', 'msgfromid>='=>'0', 'msgtoid'=>$uid, 'folder'=>'inbox', 'new'=>'1'))->get('pms')->num_rows();
				if($more) {
					$newprvpm = $this->db->where(array('related'=>'0', 'msgfromid>'=>'0', 'msgtoid'=>$uid, 'folder'=>'inbox', 'new'=>'1'))->get('pms')->num_rows();
					return array('newpm' => $newpm, 'newprivatepm' => $newprvpm);
				} else {
					return $newpm;
				}
			}
		} else {
			$newpm = $this->db->where(array('related'=>'0', 'msgfromid>='=>'0', 'msgtoid'=>$uid, 'folder'=>'inbox', 'new'=>'1'))->get('pms')->num_rows();
			$newprvpm = $this->db->where(array('related'=>'0', 'msgfromid>'=>'0', 'msgtoid'=>$uid, 'folder'=>'inbox', 'new'=>'1'))->get('pms')->num_rows();
			if($more == 2 || $more == 3) {
				$annpm = $this->db->where(array('related'=>'0', 'msgtoid'=>'0', 'folder'=>'inbox'))->get('pms')->num_rows();
				$syspm = $this->db->where(array('related'=>'0', 'msgtoid'=>$uid, 'folder'=>'inbox', 'msgfromid'=>'0'))->get('pms')->num_rows();
			}
			if($more == 2) {
				return array('newpm' => $newpm, 'newprivatepm' => $newprvpm, 'announcepm' => $annpm, 'systempm' => $syspm);
			} if($more == 4) {
				return array('newpm' => $newpm, 'newprivatepm' => $newprvpm);
			} else {
				$pm = $this->db->select('pm.dateline,pm.msgfromid,m.username as msgfrom,pm.message')->from('pms pm')->join('members m', 'pm.msgfromid = m.uid', 'LEFT')->where(array('pm.related'=>'0', 'pm.msgtoid'=>$uid, 'pm.folder'=>'inbox'))->or_where('pm.msgfromid', '0')->get('')->first_row("( ORDER BY pm.dateline DESC LIMIT 1");
				return array('newpm' => $newpm, 'newprivatepm' => $newprvpm, 'announcepm' => $annpm, 'systempm' => $syspm, 'lastdate' => $pm['dateline'], 'lastmsgfromid' => $pm['msgfromid'], 'lastmsgfrom' => $pm['msgfrom'], 'lastmsg' => $pm['message']);
			}
		}
	}

	function deletepm($uid, $pmids) {
		$this->db->delete('pms', array('msgtoid'=>$uid, 'pmid IN'=>$pmids));
		$delnum = $this->db->affected_rows();
		return $delnum;
	}

	function deleteuidpm($uid, $ids, $folder = 'inbox', $filter = '') {
		$delnum = 0;
		if($folder == 'inbox' || $folder == 'newbox') {
			if($filter == 'announcepm' && $this->base->user['admin']) {
				$this->db->delete('pms', array('folder'=>'inbox', 'msgtoid'=>'0', 'pmid IN'=>$ids));
			} elseif($ids) {
				$delnum = 1;
				$this->db->delete('pms',
					array('msgfromid IN'=>$ids, 'msgtoid'=>$uid, 'folder'=>'inbox', 'related'=>'0'));
				$this->db->update('pms', array('delstatus'=>2),
					array('msgfromid IN'=>$ids, 'msgtoid'=>$uid, 'folder'=>'inbox', 'delstatus'=>0));
				$this->db->update('pms', array('delstatus'=>1),
					array('msgtoid IN'=>$ids, 'msgfromid'=>$uid, 'folder'=>'inbox', 'delstatus'=>0));
				$this->db->delete('pms',
					array('msgfromid IN'=>$ids, 'msgtoid'=>$uid, 'delstatus'=>1, 'folder'=>'inbox'));
				$this->db->delete('pms',
					array('msgtoid IN'=>$ids, 'msgfromid'=>$uid, 'delstatus'=>2, 'folder'=>'inbox'));
			}
		} elseif($folder == 'savebox') {
			$this->db->delete('pms',
				array('pmid IN'=>$ids, 'folder'=>'outbox', 'msgfromid'=>$uid));
			$delnum = 1;
		}
		return $delnum;
	}

	function get_blackls($uid, $uids = array()) {
		if(!$uids) {
			$blackls = $this->db->select('blacklist')->where('uid', $uid)->get('memberfields')->first_row();
		} else {
			$uids = $this->base->implode($uids);
			$blackls = array();
			$rows = $this->db->select('uid, blacklist')->where_in('uid',$uids)->get('memberfields')->result_array();
			foreach($rows as $data) {
				$blackls[$data['uid']] = explode(',', $data['blacklist']);
			}
		}
		return $blackls;
	}

	function set_blackls($uid, $blackls) {
		return $this->db->update('memberfields', array('blacklist'=>$blackls), array('uid'=>$uid));
	}

	function update_blackls($uid, $username, $action = 1) {
		$username = !is_array($username) ? array($username) : $username;
		if($action == 1) {
			if(!in_array('{ALL}', $username)) {
				$usernames = $this->base->implode($username);
				$rows = $this->db->select('username')->where_in('username', $username)->get('members')->result_array();
				$usernames = array();
				foreach($rows as $data) {
					$usernames[addslashes($data['username'])] = addslashes($data['username']);
				}
				if(!$usernames) {
					return 0;
				}
				$blackls = addslashes($this->db->select('blacklist')->where('uid', $uid)->get('memberfields')->first_row('array'));
				if($blackls) {
					$list = explode(',', $blackls);
					foreach($list as $k => $v) {
						if(in_array($v, $usernames)) {
							unset($usernames[$v]);
						}
					}
				}
				if(!$usernames) {
					return 1;
				}
				$listnew = implode(',', $usernames);
				$blackls .= $blackls !== '' ? ','.$listnew : $listnew;
			} else {
				$blackls = addslashes($this->db->select('blacklist')->where('uid', $uid)->get('memberfields')->first_row('array'));
				$blackls .= ',{ALL}';
			}
		} else {
			$blackls = addslashes($this->db->select('blacklist')->where('uid', $uid)->get('memberfields')->first_row('array'));
			$list = $blackls = explode(',', $blackls);
			foreach($list as $k => $v) {
				if(in_array($v, $username)) {
					unset($blackls[$k]);
				}
			}
			$blackls = implode(',', $blackls);
		}
		$this->db->update('memberfields', array('blacklist'=>$blackls), array('uid'=>$uid));
		return 1;
	}

	function removecode($str, $length) {
		return trim($this->base->cutstr(preg_replace(array(
				"/\[(email|code|quote|img)=?.*\].*?\[\/(email|code|quote|img)\]/siU",
				"/\[\/?(b|i|url|u|color|size|font|align|list|indent|float)=?.*\]/siU",
				"/\r\n/",
			), '', $str), $length));
	}

	function count_pm_by_fromuid($uid, $timeoffset = 86400) {
		$dateline = $this->base->time - intval($timeoffset);
		return $this->db->where(array('msgfromid'=>$uid, 'dateline>'=>$dateline))->get('pms')->first_row('array');
	}

	function is_reply_pm($uid, $touids) {
		$touid_str = implode("', '", $touids);
		$pm_reply = $this->db->select('msgfromid, msgtoid')->where(array('msgfromid IN'=>$touid_str, 'msgtoid'=>$uid, 'related'=>1))->get('pms')->result_array();
		foreach($pm_reply as $reply){
			foreach($touids as $val) {
				if(!isset($pm_reply[$reply['msgfromid']])) {
					return false;
				}
			}
		}
		return true;
	}
	
	function pm_datelinesort($a, $b) {
		if ($a['dateline'] == $b['dateline']) {
			return 0;
		}
		return ($a['dateline'] < $b['dateline']) ? -1 : 1;
	}
}