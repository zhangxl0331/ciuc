<?php $this->load->view('header');?>

<?php if($method == 'ls'):?>

	<script src="<?php echo $this->config->base_url('js/common.js');?>" type="text/javascript"></script>
	<script src="<?php echo $this->config->base_url('js/calendar.js');?>" type="text/javascript"></script>
	<script type="text/javascript">
		function switchbtn(btn) {
			$('addadmindiv').className = btn == 'addadmin' ? 'tabcontentcur' : '' ;
			$('editpwdiv').className = btn == 'addadmin' ? '' : 'tabcontentcur';

			$('addadmin').className = btn == 'addadmin' ? 'tabcurrent' : '';
			$('editpw').className = btn == 'addadmin' ? '' : 'tabcurrent';

			$('addadmindiv').style.display = btn == 'addadmin' ? '' : 'none';
			$('editpwdiv').style.display = btn == 'addadmin' ? 'none' : '';
		}
		function chkeditpw(theform) {
			if(theform.oldpw.value == '') {
				alert('<?php echo $this->lang->line('admin_pw_oldpw');?>');
				theform.oldpw.focus();
				return false;
			}
			if(theform.newpw.value == '') {
				alert('<?php echo $this->lang->line('admin_pw_newpw');?>');
				theform.newpw.focus();
				return false;
			}
			if(theform.newpw2.value == '') {
				alert('<?php echo $this->lang->line('admin_pw_newpw2');?>');
				theform.newpw2.focus();
				return false;
			}
			if(theform.newpw.value != theform.newpw2.value) {
				alert('<?php echo $this->lang->line('admin_pw_incorrect');?>');
				theform.newpw2.focus();
				return false;
			}
			if(theform.newpw.value.length < 6 && !confirm('<?php echo $this->lang->line('admin_pw_too_short');?>')) {
				theform.newpw.focus();
				return false;
			}
			return true;
		}
	</script>

	<div class="container">
		<?php if($status):?>
			<div class="<?php if($status > 0):?>correctmsg<?php else:?>errormsg<?php endif;?>">
				<p>
				<?php if($status == 1):?> <?php echo $this->lang->line('admin_add_succeed');?>
				<?php elseif($status == -1):?>{elseif $status == -1} <?php echo $this->lang->line('admin_add_succeed');?>
				<?php elseif($status == -2):?>{elseif $status == -2} <?php echo $this->lang->line('admin_failed');?>
				<?php elseif($status == -3):?>{elseif $status == -3} <?php echo $this->lang->line('admin_user_nonexistance');?>
				<?php elseif($status == -4):?>{elseif $status == -4} <?php echo $this->lang->line('admin_config_unwritable');?>
				<?php elseif($status == -5):?>{elseif $status == -5} <?php echo $this->lang->line('admin_founder_pw_incorrect');?>
				<?php elseif($status == -6):?>{elseif $status == -6} <?php echo $this->lang->line('admin_pw_incorrect');?>
				<?php elseif($status == 2):?>{elseif $status == 2} <?php echo $this->lang->line('admin_founder_pw_modified');?>
				<?php endif;?>
				</p>
			</div>
		<?php endif;?>
		<div class="hastabmenu" style="height:175px;">
			<ul class="tabmenu">
				<li id="addadmin" class="tabcurrent"><a href="#" onclick="switchbtn('addadmin');"><?php echo $this->lang->line('admin_add_admin');?></a></li>
				<?php if($user['isfounder']):?><li id="editpw"><a href="#" onclick="switchbtn('editpw');"><?php echo $this->lang->line('admin_modify_founder_pw');?></a></li><?php endif;?>
			</ul>
			<div id="addadmindiv" class="tabcontentcur">
				<form action="<?php echo $this->config->base_url('admin/ls');?>" method="post">
				<input type="hidden" name="formhash" value="<?php echo formhash();?>">
				<table class="dbtb">
					<tr>
						<td class="tbtitle"><?php echo $this->lang->line('user_name');?>:</td>
						<td><input type="text" name="addname" class="txt" /></td>
					</tr>
					<tr>
						<td valign="top" class="tbtitle"><?php echo $this->lang->line('admin_privilege');?>:</td>
						<td>
							<ul class="dblist">
								<li><input type="checkbox" name="allowadminsetting" value="1" class="checkbox" checked="checked" /><?php echo $this->lang->line('admin_allow_setting');?></li>
								<li><input type="checkbox" name="allowadminapp" value="1" class="checkbox" /><?php echo $this->lang->line('admin_allow_app');?></li>
								<li><input type="checkbox" name="allowadminuser" value="1" class="checkbox" /><?php echo $this->lang->line('admin_allow_user');?></li>
								<li><input type="checkbox" name="allowadminbadword" value="1" class="checkbox" checked="checked" /><?php echo $this->lang->line('admin_allow_badwords');?></li>
								<li><input type="checkbox" name="allowadmintag" value="1" class="checkbox" checked="checked" /><?php echo $this->lang->line('admin_allow_tag');?></li>
								<li><input type="checkbox" name="allowadminpm" value="1" class="checkbox" checked="checked" /><?php echo $this->lang->line('admin_allow_pm');?></li>
								<li><input type="checkbox" name="allowadmincredits" value="1" class="checkbox" checked="checked" /><?php echo $this->lang->line('admin_allow_credits');?></li>
								<li><input type="checkbox" name="allowadmindomain" value="1" class="checkbox" checked="checked" /><?php echo $this->lang->line('admin_allow_hosts');?></li>
								<li><input type="checkbox" name="allowadmindb" value="1" class="checkbox" /><?php echo $this->lang->line('admin_allow_database');?></li>
								<li><input type="checkbox" name="allowadminnote" value="1" class="checkbox" checked="checked" /><?php echo $this->lang->line('admin_allow_note');?></li>
								<li><input type="checkbox" name="allowadmincache" value="1" class="checkbox" checked="checked" /><?php echo $this->lang->line('admin_allow_cache');?></li>
								<li><input type="checkbox" name="allowadminlog" value="1" class="checkbox" checked="checked" /><?php echo $this->lang->line('admin_allow_log');?></li>
							</ul>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<input type="submit" name="addadmin" value="<?php echo $this->lang->line('submit');?>" class="btn" />
						</td>
					</tr>
				</table>
				</form>
			</div>
			<?php if($user['isfounder']):?>
			<div id="editpwdiv" class="tabcontent" style="display:none;">
				<form action="<?php echo $this->config->base_url('admin/ls');?>" onsubmit="return chkeditpw(this)" method="post">
				<input type="hidden" name="formhash" value="<?php echo formhash();?>">
				<table class="dbtb" style="height:123px;">
					<tr>
						<td class="tbtitle"><?php echo $this->lang->line('oldpw');?>:</td>
						<td><input type="password" name="oldpw" class="txt" /></td>
					</tr>
					<tr>
						<td class="tbtitle"><?php echo $this->lang->line('newpw');?>:</td>
						<td><input type="password" name="newpw" class="txt" /></td>
					</tr>
					<tr>
						<td class="tbtitle"><?php echo $this->lang->line('repeatpw');?>:</td>
						<td><input type="password" name="newpw2" class="txt" /></td>
					</tr>
					<tr>
						<td></td>
						<td>
							<input type="submit" name="editpwsubmit" value="<?php echo $this->lang->line('submit');?>" class="btn" />
						</td>
					</tr>
				</table>
				</form>
			</div>
			<?php endif;?>
		</div>
		<h3><?php echo $this->lang->line('admin_list');?></h3>
		<div class="mainbox">
			<?php if($userlist):?>
				<form action="<?php echo $this->config->base_url('admin/ls');?>" onsubmit="return confirm('<?php echo $this->lang->line('confirm_delete');?>');" method="post">
				<input type="hidden" name="formhash" value="{FORMHASH}">
				<table class="datalist fixwidth" onmouseover="addMouseEvent(this);">
					<tr>
						<th><input type="checkbox" name="chkall" id="chkall" onclick="checkall('delete[]')" value="1" class="checkbox" /><label for="chkall"><?php echo $this->lang->line('delete');?></label></th>
						<th><?php echo $this->lang->line('user_name');?></th>
						<th><?php echo $this->lang->line('email');?></th>
						<th><?php echo $this->lang->line('user_regdate');?></th>
						<th><?php echo $this->lang->line('user_regip');?></th>
						<th><?php echo $this->lang->line('profile');?></th>
						<th><?php echo $this->lang->line('privilege');?></th>
					</tr>
					<?php foreach($userlist as $user):?>
						<tr>
							<td class="option"><input type="checkbox" name="delete[]" value="<?php echo $user['uid'];?>" value="1" class="checkbox" /></td>
							<td class="username"><?php echo $user['username'];?></td>
							<td><?php echo $user['email'];?></td>
							<td class="date"><?php echo $user['regdate'];?></td>
							<td class="ip"><?php echo $user['regip'];?></td>
							<td class="ip"><a href="<?php echo $this->config->base_url('user/edit?uid='.$user['uid'].'&fromadmin=yes');?>"><?php echo $this->lang->line('profile');?></a></td>
							<td class="ip"><a href="<?php echo $this->config->base_url('user/edit?uid='.$user['uid']);?>"><?php echo $this->lang->line('privilege');?></a></td>
						</tr>
					<?php endforeach;?>
					<tr class="nobg">
						<td><input type="submit" value="<?php echo $this->lang->line('submit');?>" class="btn" /></td>
						<td class="tdpage" colspan="4"><?php echo $multipage;?></td>
					</tr>
				</table>
				</form>
			<?php else:?>
				<div class="note">
					<p class="i"><?php echo $this->lang->line('list_empty');?></p>
				</div>
			<?php endif;?>
		</div>
	</div>
	<?php if(isset($_POST['editpwsubmit']) && $_POST['editpwsubmit']):?>
		<script type="text/javascript">
		switchbtn('editpw');
		</script>
	<?php else:?>
		<script type="text/javascript">
		switchbtn('addadmin');
		</script>
	<?php endif;?>

<?php else:?>
	<div class="container">
		<h3 class="marginbot"><?php echo $this->lang->line('admin_edit_priv');?><a href="admin.php?m=admin&a=ls" class="sgbtn"><?php echo $this->lang->line('admin_return_admin_ls');?></a></h3>
		<?php if($status == 1):?>
			<div class="correctmsg"><p><?php echo $this->lang->line('admin_priv_modified_successfully');?></p></div>
		<?php elseif($status == -1):?>
			<div class="correctmsg"><p><?php echo $this->lang->line('admin_priv_modified_failed');?></p></div>
		<?php else:?>
			<div class="note"><?php echo $this->lang->line('admin_modification_notice');?></div>
		<?php endif;?>
		<div class="mainbox">
			<form action="<?php echo $this->config->base_url('admin/edit?uid='.$uid);?>" method="post">
			<input type="hidden" name="formhash" value="<?php echo formhash();?>">
				<table class="opt">
					<tr>
						<th><?php echo $this->lang->line('user_name');?><?php echo $this->lang->line('admin_admin');?> <?php echo $admin[username];?>:</th>
					</tr>
					<tr>
						<td>
							<ul>
								<li><input type="checkbox" name="allowadminsetting" value="1" class="checkbox" <?php if($admin['allowadminsetting']):?> checked="checked" <?php endif;?>/><?php echo $this->lang->line('admin_allow_setting');?></li>
								<li><input type="checkbox" name="allowadminapp" value="1" class="checkbox" <?php if($admin['allowadminapp']):?> checked="checked" <?php endif;?>/><?php echo $this->lang->line('admin_allow_app');?></li>
								<li><input type="checkbox" name="allowadminuser" value="1" class="checkbox" <?php if($admin['allowadminuser']):?> checked="checked" <?php endif;?>/><?php echo $this->lang->line('admin_allow_user');?></li>
								<li><input type="checkbox" name="allowadminbadword" value="1" class="checkbox" <?php if($admin['allowadminbadword']):?> checked="checked" <?php endif;?>/><?php echo $this->lang->line('admin_allow_badwords');?></li>
								<li><input type="checkbox" name="allowadmintag" value="1" class="checkbox" <?php if($admin['allowadmintag']):?> checked="checked" <?php endif;?>/><?php echo $this->lang->line('admin_allow_tag');?></li>
								<li><input type="checkbox" name="allowadminpm" value="1" class="checkbox" <?php if($admin['allowadminpm']):?> checked="checked" <?php endif;?>/><?php echo $this->lang->line('admin_allow_pm');?></li>
								<li><input type="checkbox" name="allowadmincredits" value="1" class="checkbox" <?php if($admin['allowadmincredits']):?> checked="checked" <?php endif;?>/><?php echo $this->lang->line('admin_allow_credits');?></li>
								<li><input type="checkbox" name="allowadmindomain" value="1" class="checkbox" <?php if($admin['allowadmindomain']):?> checked="checked" <?php endif;?>/><?php echo $this->lang->line('admin_allow_hosts');?></li>
								<li><input type="checkbox" name="allowadmindb" value="1" class="checkbox" <?php if($admin['allowadmindb']):?> checked="checked" <?php endif;?>/><?php echo $this->lang->line('admin_allow_database');?></li>
								<li><input type="checkbox" name="allowadminnote" value="1" class="checkbox" <?php if($admin['allowadminnote']):?> checked="checked" <?php endif;?>/><?php echo $this->lang->line('admin_allow_note');?></li>
								<li><input type="checkbox" name="allowadmincache" value="1" class="checkbox" <?php if($admin['allowadmincache']):?> checked="checked" <?php endif;?>/><?php echo $this->lang->line('admin_allow_cache');?></li>
								<li><input type="checkbox" name="allowadminlog" value="1" class="checkbox" <?php if($admin['allowadminlog']):?> checked="checked" <?php endif;?>/><?php echo $this->lang->line('admin_allow_log');?></li>
							</ul>
						</td>
					</tr>
				</table>
				<div class="opt"><input type="submit" name="submit" value=" <?php echo $this->lang->line('submit');?> " class="btn" tabindex="3" /></div>
			</form>
		</div>
	</div>

<?php endif;?>

<?php $this->load->view('footer');?>