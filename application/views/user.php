<?php $this->load->view('header');?>

<script src="<?php echo $this->config->base_url('js/common.js');?>" type="text/javascript"></script>
<script src="<?php echo $this->config->base_url('js/calendar.js');?>" type="text/javascript"></script>

<?php if($method == 'ls'):?>

	<script type="text/javascript">
		function switchbtn(btn) {
			$('srchuserdiv').style.display = btn == 'srch' ? '' : 'none';
			$('srchuserdiv').className = btn == 'srch' ? 'tabcontentcur' : '' ;
			$('srchuserbtn').className = btn == 'srch' ? 'tabcurrent' : '';
			$('adduserdiv').style.display = btn == 'srch' ? 'none' : '';
			$('adduserdiv').className = btn == 'srch' ? '' : 'tabcontentcur';
			$('adduserbtn').className = btn == 'srch' ? '' : 'tabcurrent';
		}
	</script>

	<div class="container">
		<?php if($status):?>
			<div class="<?php if($status > 0):?>correctmsg<?php else:?>errormsg<?php endif;?>"><p><?php if($status < 0):?><em><?php echo $this->lang->line('user_add_failed');?>:</em> <?php endif;?><?php if($status == 2):?><?php echo $this->lang->line('user_delete_succeed');?><?php elseif($status == 1):?><?php echo $this->lang->line('user_add_succeed');?><?php elseif($status == -1):?><?php echo $this->lang->line('user_add_username_ignore');?><?php elseif($status == -2):?><?php echo $this->lang->line('user_add_username_badwords');?><?php elseif($status == -3):?><?php echo $this->lang->line('user_add_username_exists');?><?php elseif($status == -4):?><?php echo $this->lang->line('user_add_email_formatinvalid');?><?php elseif($status == -5):?><?php echo $this->lang->line('user_add_email_ignore');?><?php elseif($status == -6):?><?php echo $this->lang->line('user_add_email_exists');?><?php endif;?></p></div>
		<?php endif;?>
		<div class="hastabmenu">
			<ul class="tabmenu">
				<li id="srchuserbtn" class="tabcurrent"><a href="#" onclick="switchbtn('srch')"><?php echo $this->lang->line('user_search');?></a></li>
				<li id="adduserbtn"><a href="#" onclick="switchbtn('add')"><?php echo $this->lang->line('user_add');?></a></li>
			</ul>
			<div id="adduserdiv" class="tabcontent" style="display:none;">
				<form action="<?php echo $this->config->base_url('user/ls?adduser=yes');?>" method="post">
				<input type="hidden" name="formhash" value="<?php echo formhash();?>">
				<table width="100%">
					<tr>
						<td><?php echo $this->lang->line('user_name');?>:</td>
						<td><input type="text" name="addname" class="txt" /></td>
						<td><?php echo $this->lang->line('user_password');?>:</td>
						<td><input type="text" name="addpassword" class="txt" /></td>
						<td><?php echo $this->lang->line('email');?>:</td>
						<td><input type="text" name="addemail" class="txt" /></td>
						<td><input type="submit" value="<?php echo $this->lang->line('submit');?>" class="btn" /></td>
					</tr>
				</table>
				</form>
			</div>
			<div id="srchuserdiv" class="tabcontentcur">
				<form action="<?php echo $this->config->base_url('user/ls');?>" method="post">
				<input type="hidden" name="formhash" value="<?php echo formhash();?>">
				<table width="100%">
					<tr>
						<td><?php echo $this->lang->line('user_name');?>:</td>
						<td><input type="text" name="srchname" value="<?php echo $srchname;?>" class="txt" /></td>
						<td>UID:</td>
						<td><input type="text" name="srchuid" value="<?php echo $srchuid;?>" class="txt" /></td>
						<td><?php echo $this->lang->line('email');?>:</td>
						<td><input type="text" name="srchemail" value="<?php echo $srchemail;?>" class="txt" /></td>
						<td rowspan="2"><input type="submit" value="<?php echo $this->lang->line('submit');?>" class="btn" /></td>
					</tr>
					<tr>
						<td><?php echo $this->lang->line('user_regdate');?>:</td>
						<td colspan="3"><input type="text" name="srchregdatestart" onclick="showcalendar();" value="<?php echo $srchregdatestart;?>" class="txt" /> <?php echo $this->lang->line('to');?> <input type="text" name="srchregdateend" onclick="showcalendar();" value="<?php echo $srchregdateend;?>" class="txt" /></td>
						<td><?php echo $this->lang->line('user_regip');?>:</td>
						<td><input type="text" name="srchregip" value="<?php echo $srchregip;?>" class="txt" /></td>
					</tr>
				</table>
				</form>
			</div>
		</div>

		<?php if($adduser):?><script type="text/javascript">switchbtn('add');</script><?php endif;?>
<br />
		<h3><?php echo $this->lang->line('user_list');?></h3>
		<div class="mainbox">
			<?php if($userlist):?>
				<form action="<?php echo $this->config->base_url('user/ls?srchname='.$srchname.'&srchregdatestart='.$srchregdatestart.'&srchregdateend='.$srchregdateend);?>" onsubmit="return confirm('<?php echo $this->lang->line('user_delete_confirm');?>');" method="post">
				<input type="hidden" name="formhash" value="<?php echo formhash();?>">
				<table class="datalist fixwidth" onmouseover="addMouseEvent(this);">
					<tr>
						<th><input type="checkbox" name="chkall" id="chkall" onclick="checkall('delete[]')" class="checkbox" /><label for="chkall"><?php echo $this->lang->line('delete');?></label></th>
						<th><?php echo $this->lang->line('user_name');?></th>
						<th><?php echo $this->lang->line('email');?></th>
						<th><?php echo $this->lang->line('user_regdate');?></th>
						<th><?php echo $this->lang->line('user_regip');?></th>
						<th><?php echo $this->lang->line('edit');?></th>
					</tr>
					<?php foreach($userlist as $user):?>
						<tr>
							<td class="option"><input type="checkbox" name="delete[]" value="<?php echo $user['uid'];?>" class="checkbox" /></td>
							<td><?php echo $user['smallavatar'];?> <strong><?php echo $user['username'];?></strong></td>
							<td><?php echo $user['email'];?></td>
							<td><?php echo $user['regdate'];?></td>
							<td><?php echo $user['regip'];?></td>
							<td><a href="<?php echo $this->config->base_url('user/edit?uid='.$user['uid']);?>"><?php echo $this->lang->line('edit');?></a></td>
						</tr>
					<?php endforeach;?>
					<tr class="nobg">
						<td><input type="submit" value="<?php echo $this->lang->line('submit');?>" class="btn" /></td>
						<td class="tdpage" colspan="6"><?php echo $multipage;?></td>
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

<?php else:?>

	<div class="container">
		<h3 class="marginbot"><?php echo $this->lang->line('user_edit_profile');?>
			<?php if(getgpc('fromadmin')):?>
				<a href="<?php echo $this->config->base_url('admin/ls');?>" class="sgbtn"><?php echo $this->lang->line('admin_return_admin_ls');?></a>
			<?php else:?>
				<a href="<?php echo $this->config->base_url('user/ls');?>" class="sgbtn"><?php echo $this->lang->line('admin_return_user_ls');?></a>
			<?php endif;?>
		</h3>
		<?php if($status == 1):?>
			<div class="correctmsg"><p><?php echo $this->lang->line('user_edit_profile_sucessfully');?></p></div>
		<?php elseif($status == -1):?>
			<div class="correctmsg"><p><?php echo $this->lang->line('user_edit_profile_failed');?></p></div>
		<?php else:?>
			<div class="note"><p class="i"><?php echo $this->lang->line('user_keep_blank');?></p></div>
		<?php endif;?>
		<div class="mainbox">
			<form action="<?php echo $this->config->base_url('user/edit?uid='.$uid);?>" method="post">
			<input type="hidden" name="formhash" value="<?php echo formhash();?>">
				<table class="opt">
					<tr>
						<th><?php echo $this->lang->line('user_avatar');?>: <input name="delavatar" class="checkbox" type="checkbox" value="1" /> <?php echo $this->lang->line('delete_avatar');?></th>
					</tr>
					<tr>
						<th><?php echo $this->lang->line('user_avatar_virtual');?>:</th>
					</tr>
					<tr>
						<td><?php echo $user['bigavatar'];?></td>
					</tr>
					<tr>
						<th><?php echo $this->lang->line('user_avatar_real');?>:</th>
					</tr>
					<tr>
						<td><?php echo $user['bigavatarreal'];?></td>
					</tr>
					<tr>
						<th><?php echo $this->lang->line('login_username');?>:</th>
					</tr>
					<tr>
						<td>
							<input type="text" name="newusername" value="<?php echo $user['username'];?>" class="txt" />
							<input type="hidden" name="username" value="<?php echo $user['username'];?>" class="txt" />
						</td>
					</tr>
					<tr>
						<th><?php echo $this->lang->line('login_password');?>:</th>
					</tr>
					<tr>
						<td>
							<input type="text" name="password" value="" class="txt" />
						</td>
					</tr>
					<tr>
						<th><?php echo $this->lang->line('login_secques');?>: <input type="checkbox" class="checkbox" name="rmrecques" value="1" /> <?php echo $this->lang->line('login_remove_secques');?></th>
					</tr>
					<tr>
						<th>Email:</th>
					</tr>
					<tr>
						<td>
							<input type="text" name="email" value="<?php echo $user['email'];?>" class="txt" />
						</td>
					</tr>
				</table>
				<div class="opt"><input type="submit" name="submit" value=" <?php echo $this->lang->line('submit');?> " class="btn" tabindex="3" /></div>
			</form>
		</div>
	</div>
<?php endif;?>
<?php $this->load->view('footer');?>