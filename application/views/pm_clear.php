<?php $this->load->view('header');?>

<div class="container">
	<?php if($status):?>
		<div class="correctmsg"><p><?php echo $this->lang->line('clearpm_deleted');?>: <?php echo $delnum;?></p></div>
	<?php endif;?>
	<h3 class="marginbot">
		<a href="admin.php?m=pm&a=ls" class="sgbtn"><?php echo $this->lang->line('announcepm');?></a>
		<a href="admin.php?m=pm&a=send" class="sgbtn"><?php echo $this->lang->line('pm_send_announce');?></a>
		<?php echo $this->lang->line('clear_pm');?>
	</h3>
	<div class="note fixwidthdec"><p class="i"><?php echo $this->lang->line('clearpm_totalnum');?>: <?php echo $pmnum;?></p></div>
	<div class="mainbox nomargin">
		<form action="<?php echo $this->config->base_url('pm/clear');?>" method="post">
			<input type="hidden" name="formhash" value="<?php echo formhash();?>">
			<table class="opt">
				<tr>
					<th colspan="2"><?php echo $this->lang->line('clearpm_delunread');?>:</th>
				</tr>
				<tr>
					<td>
						<input type="radio" id="yes" checked="checked" class="radio" name="unread" value="1" /><label for="yes"><?php echo $this->lang->line('yes');?></label>
						<input type="radio" id="no" class="radio" name="unread" value="0" /><label for="no"><?php echo $this->lang->line('no');?></label>
					</td>
				</tr>
				<tr>
					<th colspan="2"><?php echo $this->lang->line('clearpm_cleardays');?>:</th>
				</tr>
				<tr>
					<td><input type="text" class="txt" name="cleardays"></td>
					<td valign="top"><?php echo $this->lang->line('clearpm_cleardays_comment');?></td>
				</tr>
				<tr>
					<th colspan="2"><?php echo $this->lang->line('clearpm_usernames');?>:</th>
				</tr>
				<tr>
					<td><input type="text" class="txt" name="usernames"></td>
					<td valign="top"><?php echo $this->lang->line('clearpm_usernames_comment');?></td>
				</tr>
			</table>
			<div class="opt"><input type="submit" name="submit" value=" <?php echo $this->lang->line('submit');?> " class="btn" tabindex="3" /></div>
		</form>
	</div>
</div>

<?php $this->load->view('footer');?>