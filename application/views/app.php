<?php $this->load->view('header');?>

<script src="<?php echo $this->config->base_url('js/common.js');?>" type="text/javascript"></script>
<script type="text/javascript">
var apps = new Array();
var run = 0;
function testlink() {
	if(apps[run]) {
		$('status_' + apps[run]).innerHTML = '<?php echo $this->lang->line('app_link');?>';
		$('link_' + apps[run]).src = $('link_' + apps[run]).getAttribute('testlink') + '&sid=<?php echo $sid;?>';
	}
	run++;
}
window.onload = testlink;
</script>
<div class="container">
	<?php if($method == 'ls'):?>
		<h3 class="marginbot"><?php echo $this->lang->line('app_list');?><a href="<?php echo $this->config->base_url('app/add');?>" class="sgbtn"><?php echo $this->lang->line('app_add');?></a></h3>
		<?php if(!$status):?>
			<div class="note fixwidthdec">
				<p class="i"><?php echo $this->lang->line('app_list_tips');?></p>
			</div>
		<?php elseif($status == '2'):?>
			<div class="correctmsg"><p><?php echo $this->lang->line('app_list_updated');?></p></div>
		<?php endif;?>
		<div class="mainbox">
			<?php if($applist):?>
				<form action="<?php echo $this->config->base_url('app/ls');?>" method="post">
					<input type="hidden" name="formhash" value="<?php echo formhash();?>">
					<table class="datalist fixwidth" onmouseover="addMouseEvent(this);">
						<tr>
							<th nowrap="nowrap"><input type="checkbox" name="chkall" id="chkall" onclick="checkall('delete[]')" class="checkbox" /><label for="chkall"><?php echo $this->lang->line('app_delete');?></label></th>
							<th nowrap="nowrap"><?php echo $this->lang->line('app_id');?></th>
							<th nowrap="nowrap"><?php echo $this->lang->line('app_name');?></th>
							<th nowrap="nowrap"><?php echo $this->lang->line('app_url');?></th>
							<th nowrap="nowrap"><?php echo $this->lang->line('app_linkstatus');?></th>
							<th nowrap="nowrap"><?php echo $this->lang->line('app_detail');?></th>
						</tr>
						<?php $i =0;?>
						<?php foreach($applist as $app):?>
							<tr>
								<td width="50"><input type="checkbox" name="delete[]" value="<?php echo $app['appid'];?>" class="checkbox" /></td>
								<td width="35"><?php echo $app['appid'];?></td>
								<td><a href="<?php echo $this->config->base_url('app/detail?appid='.$app['appid']);?>"><strong><?php echo $app['name'];?></strong></a></td>
								<td><a href="<?php echo $app['url'];?>" target="_blank"><?php echo $app['url'];?></a></td>
								<td width="90"><div id="status_<?php echo $app['appid'];?>"></div><script id="link_<?php echo $app['appid'];?>" testlink="<?php echo $this->config->base_url('app/ping?inajax=1&url='.urlencode($app['url']).'&ip='.urlencode($app['ip']).'&appid='.$app['appid'].'&random='.rand());?>"></script><script>apps[<?php echo $i;?>] = '<?php echo $app['appid'];?>';</script></td>
								<td width="40"><a href="<?php echo $this->config->base_url('app/detail?appid='.$app['appid']);?>"><?php echo $this->lang->line('app_edit');?></a></td>
							</tr>
							<?php $i++;?>
						<?php endforeach;?>
						<tr class="nobg">
							<td colspan="9"><input type="submit" value="<?php echo $this->lang->line('submit');?>" class="btn" /></td>
						</tr>
					</table>
					<div class="margintop"></div>
				</form>
			<?php else:?>
				<div class="note">
					<p class="i"><?php echo $this->lang->line('list_empty');?></p>
				</div>
			<?php endif;?>
		</div>
	<?php elseif($method == 'add'):?>
		<h3 class="marginbot"><?php echo $this->lang->line('app_add');?><a href="<?php echo $this->config->base_url('app/ls');?>" class="sgbtn"><?php echo $this->lang->line('app_list_return');?></a></h3>
		<div class="mainbox">
			<table class="opt">
				<tr>
					<th><?php echo $this->lang->line('app_install_type');?>:</th>
				</tr>
				<tr>
					<td>
						<input type="radio" name="installtype" class="radio" checked="checked" onclick="$('url').style.display='';$('custom').style.display='none';" /><?php echo $this->lang->line('app_install_by_url');?>
						<input type="radio" name="installtype" class="radio" onclick="$('url').style.display='none';$('custom').style.display='';" /><?php echo $this->lang->line('app_install_by_custom');?>
					</td>
				</tr>
			</table>
			<div id="url">
				<form method="post" action="" target="_blank" onsubmit="document.appform.action=document.appform.appurl.value;" name="appform">
					<table class="opt">
						<tr>
							<th><?php echo $this->lang->line('app_install_url');?>:</th>
						</tr>
						<tr>
							<td><input type="text" name="appurl" size="50" value="http://domainname/install/index.php" style="width:300px;" /></td>
						</tr>
					</table>
					<div class="opt">
						<input type="hidden" name="ucapi" value="{UC_API}" />
						<input type="hidden" name="ucfounderpw" value="<?php echo $md5ucfounderpw;?>" />
						<input type="submit" name="installsubmit"  value="<?php echo $this->lang->line('app_install_submit');?>" class="btn" />
					</div>
				</form>
			</div>
			<div id="custom" style="display:none;">
				<form action="<?php echo $this->config->base_url('app/add');?>" method="post">
				<input type="hidden" name="formhash" value="<?php echo formhash();?>">
					<table class="opt">
						<tr>
							<th colspan="2"><?php echo $this->lang->line('app_name');?>:</th>
						</tr>
						<tr>
							<td><input type="text" class="txt" name="name" value="" /></td>
							<td><?php echo $this->lang->line('app_name_comment');?></td>
						</tr>
						<tr>
							<th colspan="2"><?php echo $this->lang->line('app_url');?>:</th>
						</tr>
						<tr>
							<td><input type="text" class="txt" name="url" value="" /></td>
							<td><?php echo $this->lang->line('app_url_comment');?></td>
						</tr>
						<tr>
							<th colspan="2"><?php echo $this->lang->line('app_ip');?>:</th>
						</tr>
						<tr>
							<td><input type="text" class="txt" name="ip" value="" /></td>
							<td><?php echo $this->lang->line('app_ip_comment');?></td>
						</tr>
						<tr>
							<th colspan="2"><?php echo $this->lang->line('app_key');?>:</th>
						</tr>
						<tr>
							<td><input type="text" class="txt" name="authkey" value="" /></td>
							<td><?php echo $this->lang->line('app_key_comment');?></td>
						</tr>
						<tr>
							<th colspan="2"><?php echo $this->lang->line('app_type');?>:</th>
						</tr>
						<tr>
							<td>
							<select name="type">
								<?php foreach($typelist as $typeid=>$typename):?>
									<option value="<?php echo $typeid;?>"> <?php echo $typename;?> </option>
								<?php endforeach;?>
							</select>
							</td>
							<td></td>
						</tr>
						<tr>
							<th colspan="2"><?php echo $this->lang->line('app_path');?>:</th>
						</tr>
						<tr>
							<td>
								<input type="text" class="txt" name="apppath" value="" />
							</td>
							<td><?php echo $this->lang->line('app_path_comment');?></td>
						</tr>
						<tr>
							<th colspan="2"><?php echo $this->lang->line('app_viewpro_url');?>:</th>
						</tr>
						<tr>
							<td>
								<input type="text" class="txt" name="viewprourl" value="" />
							</td>
							<td><?php echo $this->lang->line('app_viewpro_url_comment');?></td>
						</tr>
						<tr>
							<th colspan="2"><?php echo $this->lang->line('app_api_filename');?>:</th>
						</tr>
						<tr>
							<td>
								<input type="text" class="txt" name="apifilename" value="uc.php" />
							</td>
							<td><?php echo $this->lang->line('app_api_filename_comment');?></td>
						</tr>
						<tr>
							<th colspan="2"><?php echo $this->lang->line('app_tagtemplates');?>:</th>
						</tr>
						<tr>
							<td><textarea class="area" name="tagtemplates"></textarea></td>
							<td valign="top"><?php echo $this->lang->line('app_tagtemplates_comment');?></td>
						</tr>
						<tr>
							<th colspan="2"><?php echo $this->lang->line('app_tagfields');?>:</th>
						</tr>
						<tr>
							<td><textarea class="area" name="tagfields"><?php echo $tagtemplates['fields'];?></textarea></td>
							<td valign="top"><?php echo $this->lang->line('app_tagfields_comment');?></td>
						</tr>
						<tr>
							<th colspan="2"><?php echo $this->lang->line('app_login');?>:</th>
						</tr>
						<tr>
							<td>
								<input type="radio" class="radio" id="yes" name="synlogin" value="1" /><label for="yes"><?php echo $this->lang->line('yes');?></label>
								<input type="radio" class="radio" id="no" name="synlogin" value="0" checked="checked" /><label for="no"><?php echo $this->lang->line('no');?></label>
							</td>
							<td><?php echo $this->lang->line('app_login_comment');?></td>
						</tr>
						<tr>
							<th colspan="2"><?php echo $this->lang->line('app_recvnote');?>:</th>
						</tr>
						<tr>
							<td>
								<input type="radio" class="radio" id="yes" name="recvnote" value="1"/><label for="yes"><?php echo $this->lang->line('yes');?></label>
								<input type="radio" class="radio" id="no" name="recvnote" value="0" checked="checked" /><label for="no"><?php echo $this->lang->line('no');?></label>
							</td>
							<td></td>
						</tr>
					</table>
					<div class="opt"><input type="submit" name="submit" value=" <?php echo $this->lang->line('submit');?> " class="btn" tabindex="3" /></div>
				</form>
			</div>
		</div>
	<?php else:?>
		<h3 class="marginbot"><?php echo $this->lang->line('app_setting');?><a href="<?php echo $this->config->base_url('app/ls');?>" class="sgbtn"><?php echo $this->lang->line('app_list_return');?></a></h3>
		<?php if($updated):?>
			<div class="correctmsg"><p><?php echo $this->lang->line('update_succeed');?></p></div>
		<?php elseif($addapp):?>
			<div class="correctmsg"><p><?php echo $this->lang->line('app_add_succeed');?></p></div>
		<?php endif;?>
		<div class="mainbox">
			<form action="<?php echo $this->config->base_url('app/detail?appid='.$appid);?>" method="post">
			<input type="hidden" name="formhash" value="<?php echo formhash();?>">
				<table class="opt">
					<tr>
						<th colspan="2"><?php echo $this->lang->line('app_id');?>: <?php echo $appid;?></th>
					</tr>
					<tr>
						<th colspan="2"><?php echo $this->lang->line('app_name');?>:</th>
					</tr>
					<tr>
						<td><input type="text" class="txt" name="name" value="<?php echo $name;?>" /></td>
						<td><?php echo $this->lang->line('app_name_comment');?></td>
					</tr>
					<tr>
						<th colspan="2"><?php echo $this->lang->line('app_url');?>:</th>
					</tr>
					<tr>
						<td><input type="text" class="txt" name="url" value="<?php echo $url;?>" /></td>
						<td><?php echo $this->lang->line('app_url_comment');?></td>
					</tr>
					<tr>
						<th colspan="2"><?php echo $this->lang->line('app_ip');?>:</th>
					</tr>
					<tr>
						<td><input type="text" class="txt" name="ip" value="<?php echo $ip;?>" /></td>
						<td><?php echo $this->lang->line('app_ip_comment');?></td>
					</tr>
					<tr>
						<th colspan="2"><?php echo $this->lang->line('app_key');?>:</th>
					</tr>
					<tr>
						<td><input type="text" class="txt" name="authkey" value="<?php echo $authkey;?>" /></td>
						<td><?php echo $this->lang->line('app_key_comment');?></td>
					</tr>
					<tr>
						<th colspan="2"><?php echo $this->lang->line('app_type');?>:</th>
					</tr>
					<tr>
						<td>
						<select name="type">
							<?php foreach($typelist as $typeid=>$typename):?>
							<option value="<?php echo $typeid;?>" <?php if($typeid == $type):?>selected="selected"<?php endif;?>> <?php echo $typename;?> </option>
							<?php endforeach;?>
						</select>
						</td>
						<td></td>
					</tr>
					<tr>
						<th colspan="2"><?php echo $this->lang->line('app_path');?>:</th>
					</tr>
					<tr>
						<td>
							<input type="text" class="txt" name="apppath" value="<?php echo $apppath;?>" />
						</td>
						<td><?php echo $this->lang->line('app_path_comment');?></td>
					</tr>
					<tr>
						<th colspan="2"><?php echo $this->lang->line('app_viewpro_url');?>:</th>
					</tr>
					<tr>
						<td>
							<input type="text" class="txt" name="viewprourl" value="<?php echo $viewprourl;?>" />
						</td>
						<td><?php echo $this->lang->line('app_viewpro_url_comment');?></td>
					</tr>
					<tr>
						<th colspan="2"><?php echo $this->lang->line('app_api_filename');?>:</th>
					</tr>
					<tr>
						<td>
							<input type="text" class="txt" name="apifilename" value="<?php echo $apifilename;?>" />
						</td>
						<td><?php echo $this->lang->line('app_api_filename_comment');?></td>
					</tr>
					<tr>
						<th colspan="2"><?php echo $this->lang->line('app_tagtemplates');?>:</th>
					</tr>
					<tr>
						<td><textarea class="area" name="tagtemplates"><?php echo $tagtemplates['template'];?></textarea></td>
						<td valign="top"><?php echo $this->lang->line('app_tagtemplates_comment');?></td>
					</tr>
					<tr>
						<th colspan="2"><?php echo $this->lang->line('app_tagfields');?>:</th>
					</tr>
					<tr>
						<td><textarea class="area" name="tagfields"><?php echo $tagtemplates['fields'];?></textarea></td>
						<td valign="top"><?php echo $this->lang->line('app_tagfields_comment');?></td>
					</tr>
					<tr>
						<th colspan="2"><?php echo $this->lang->line('app_login');?>:</th>
					</tr>
					<tr>
						<td>
							<input type="radio" class="radio" id="yes" name="synlogin" value="1" <?php echo isset($synlogin[1])?$synlogin[1]:'';?> /><label for="yes"><?php echo $this->lang->line('yes');?></label>
							<input type="radio" class="radio" id="no" name="synlogin" value="0" <?php echo isset($synlogin[0])?$synlogin[0]:'';?> /><label for="no"><?php echo $this->lang->line('no');?></label>
						</td>
						<td><?php echo $this->lang->line('app_login_comment');?></td>
					</tr>
					<tr>
						<th colspan="2"><?php echo $this->lang->line('app_recvnote');?>:</th>
					</tr>
					<tr>
						<td>
							<input type="radio" class="radio" id="yes" name="recvnote" value="1" <?php echo isset($recvnotechecked[1])?$recvnotechecked[1]:'';?> /><label for="yes"><?php echo $this->lang->line('yes');?></label>
							<input type="radio" class="radio" id="no" name="recvnote" value="0" <?php echo isset($recvnotechecked[0])?$recvnotechecked[0]:'';?> /><label for="no"><?php echo $this->lang->line('no');?></label>
						</td>
						<td></td>
					</tr>
				</table>
				<div class="opt"><input type="submit" name="submit" value=" <?php echo $this->lang->line('submit');?> " class="btn" tabindex="3" /></div>
				<?php if($isfounder):?>
				<table class="opt">
					<tr>
						<th colspan="2"><?php echo $this->lang->line('app_code');?>:</th>
					</tr>
					<tr>
						<th>
							<textarea class="area" onFocus="this.select()">
							define('UC_CONNECT', 'mysql');
							define('UC_DBHOST', '{UC_DBHOST}');
							define('UC_DBUSER', '{UC_DBUSER}');
							define('UC_DBPW', '{UC_DBPW}');
							define('UC_DBNAME', '{UC_DBNAME}');
							define('UC_DBCHARSET', '{UC_DBCHARSET}');
							define('UC_DBTABLEPRE', '`{UC_DBNAME}`.{UC_DBTABLEPRE}');
							define('UC_DBCONNECT', '0');
							define('UC_KEY', '$authkey');
							define('UC_API', '{UC_API}');
							define('UC_CHARSET', '{UC_CHARSET}');
							define('UC_IP', '');
							define('UC_APPID', '$appid');
							define('UC_PPP', '20');
							</textarea>
						</th>
						<td><?php echo $this->lang->line('app_code_comment');?></td>
					</tr>
				</table>
				<?php endif;?>
			</form>
		</div>
	<?php endif;?>
</div>

<?php $this->load->view('footer');?>