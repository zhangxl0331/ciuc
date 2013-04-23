<?php $this->load->view('header');?>
{if $iframe}
<script type="text/javascript">
	var uc_menu_data = new Array();
	o = document.getElementById('header_menu_menu');
	elems = o.getElementsByTagName('A');
	for(i = 0; i<elems.length; i++) {
		uc_menu_data.push(elems[i].innerHTML);
		uc_menu_data.push(elems[i].href);
	}
	try {
		parent.uc_left_menu(uc_menu_data);
		parent.uc_modify_sid('{$sid}');
	} catch(e) {}
</script>
{/if}
<div class="container">
	<h3>{lang home_stats}</h3>
	<ul class="memlist fixwidth">
		<li><em><!--{if $user['allowadminapp'] || $user['isfounder']}--><a href="admin.php?m=app&a=ls">{lang home_app_count}</a><!--{else}-->{lang home_app_count}<!--{/if}-->:</em>$apps</li>
		<li><em><!--{if $user['allowadminuser'] || $user['isfounder']}--><a href="admin.php?m=user&a=ls">{lang home_member_count}</a><!--{else}-->{lang home_member_count}<!--{/if}-->:</em>$members</li>
		<li><em><!--{if $user['allowadminpm'] || $user['isfounder']}--><a href="admin.php?m=pm&a=ls">{lang home_pm_count}</a><!--{else}-->{lang home_pm_count}<!--{/if}-->:</em>$pms</li>
		<li><em>{lang home_friend_count}:</em>$friends</li>
	</ul>
	
	<h3>{lang note_status}</h3>
	<ul class="memlist fixwidth">
		<li><em><!--{if $user['allowadminnote'] || $user['isfounder']}--><a href="admin.php?m=note&a=ls">{lang home_note_count}</a><!--{else}-->{lang home_note_count}<!--{/if}-->:</em>$notes</li>
		<!--{if $errornotes}-->
			<li><em><!--{if $user['allowadminnote'] || $user['isfounder']}--><a href="admin.php?m=note&a=ls">{lang note_fail_apps}</a><!--{else}-->{lang note_fail_apps}<!--{/if}-->:</em>		
			<!--{loop $errornotes $appid $error}-->
				$applist[$appid][name]&nbsp;
			<!--{/loop}-->
		<!--{/if}-->
	</ul>
	
	<h3>{lang home_env}</h3>
	<ul class="memlist fixwidth">
		<li><em>{lang home_version}:</em>UCenter {UC_SERVER_VERSION} Release {UC_SERVER_RELEASE} <a href="http://www.discuz.net/forumdisplay.php?fid=151" target="_blank">{lang view_new_version}</a> 
		<li><em>{lang home_environment}:</em>$serverinfo</li>
		<li><em>{lang home_server_software}:</em>$_SERVER[SERVER_SOFTWARE]</li>
		<li><em>{lang home_database}:</em>$dbversion</li>
		<li><em>{lang home_upload_perm}:</em>$fileupload</li>
		<li><em>{lang home_database_size}:</em>$dbsize</li>		
		<li><em>{lang home_server_ip}:</em>$_SERVER[SERVER_NAME] ($_SERVER[SERVER_ADDR]:$_SERVER[SERVER_PORT])</li>
		<li><em>magic_quote_gpc:</em>$magic_quote_gpc</li>
		<li><em>allow_url_fopen:</em>$allow_url_fopen</li>		
	</ul>
	<h3>{lang home_team}</h3>
	<ul class="memlist fixwidth">
		<li>
			<em>{lang home_dev_copyright}:</em>
			<em class="memcont"><a href="http://www.comsenz.com" target="_blank">&#x5eb7;&#x76db;&#x521b;&#x60f3;(&#x5317;&#x4eac;)&#x79d1;&#x6280;&#x6709;&#x9650;&#x516c;&#x53f8; (Comsenz Inc.)</a></em>
		</li>
		<li>
			<em>{lang home_dev_manager}:</em>
			<em class="memcont"><a href="http://www.discuz.net/space.php?uid=1" target="_blank">&#x6234;&#x5FD7;&#x5EB7; (Kevin 'Crossday' Day)</a></em>
		</li>
		<li>
			<em>{lang home_dev_team}:</em>
			<em class="memcont">
				<a href="http://www.discuz.net/space.php?uid=859" target="_blank">Hypo 'cnteacher' Wang</a>,
				<a href="http://www.discuz.net/space.php?uid=16678" target="_blank">Yang 'Dokho' Song</a>,
				<a href="http://www.discuz.net/space.php?uid=10407" target="_blank">Qiang Liu</a>,
				<a href="http://www.discuz.net/space.php?uid=80629" target="_blank">Ning 'Monkey' Hou</a>,				
				<a href="http://www.discuz.net/space.php?uid=15104" target="_blank">Xiongfei 'Redstone' Zhao</a>
			</em>
		</li>
		<li>
			<em>{lang home_safe_team}:</em>
			<em class="memcont">
				<a href="http://www.discuz.net/space.php?uid=859" target="_blank">Hypo 'cnteacher' Wang</a>,
				<a href="http://www.discuz.net/space.php?uid=210272" target="_blank">XiaoDun 'Kenshine' Fang</a>,
				<a href="http://www.discuz.net/space.php?uid=492114" target="_blank">Liang 'Metthew' Xu</a>,
				<a href="http://www.discuz.net/space.php?uid=285706" target="_blank">Wei (Sniffer) Yu</a>
			</em>
		</li>
		<li>
			<em>{lang home_supported_team}:</em>
			<em class="memcont">
				<a href="http://www.discuz.net/space.php?uid=2691" target="_blank">Liang 'Readme' Chen</a>,
				<a href="http://www.discuz.net/space.php?uid=1519" target="_blank">Yang 'Summer' Xia</a>,
				<a href="http://www.discuz.net/space.php?uid=1904" target="_blank">Tao 'FengXue' Cheng</a>
			</em>
		</li>
		<li>
			<em>{lang home_supported_ui}:</em>
			<em class="memcont">
				<a href="http://www.discuz.net/space.php?uid=294092" target="_blank">Fangming 'Lushnis' Li</a>,
				<a href="http://www.discuz.net/space.php?uid=717854" target="_blank">Ruitao 'Pony.M' Ma</a>
			</em>
		</li>
		<li>
			<em>{lang home_supported_thanks}:</em>
			<em class="memcont">
				<a href="http://www.discuz.net/space.php?uid=122246" target="_blank">Heyond</a>
			</em>
		</li>
		<li>
			<em>{lang home_dev_enterprise_site}:</em>
			<em class="memcont"><a href="http://www.comsenz.com" target="_blank">http://www.Comsenz.com</a></em>
		</li>
		<li>
			<em>{lang home_dev_project_site}:</em>
			<em class="memcont"><a href="http://www.discuz.com" target="_blank">http://www.Discuz.com</a></em>
		</li>
		<li>
			<em>{lang home_dev_community}:</em>
			<em class="memcont"><a href="http://www.discuz.net" target="_blank">http://www.Discuz.net</a></em>
		</li>
	</ul>
</div>

$ucinfo

<?php $this->load->view('footer');?>