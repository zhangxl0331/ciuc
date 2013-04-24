<?php $this->load->view('header');?>

<script src="<?php echo $this->config->base_url('js/common.js');?>" type="text/javascript"></script>
<div class="container">
	<h3 class="marginbot">
		<a href="<?php echo $this->config->base_url('feed/ls');?>" class="sgbtn"><?php echo $this->lang->line('feed_list');?></a>
		<?php if($user['isfounder'] || $user['allowadminnote']):?><a href="<?php echo $this->config->base_url('note/ls');?>" class="sgbtn"><?php echo $this->lang->line('note_list');?></a><?php endif;?>
		<?php if($user['isfounder'] || $user['allowadminlog']):?><a href="<?php echo $this->config->base_url('log/ls');?>" class="sgbtn"><?php echo $this->lang->line('menu_log');?></a><?php endif;?>
		<?php echo $this->lang->line('mail_queue');?>
	</h3>
	<?php if($status == 2):?>
		<div class="correctmsg"><p><?php echo $this->lang->line('mail_list_updated');?></p></div>
	<?php endif;?>
	<div class="mainbox">
		<?php if($maillist):?>
			<form action="<?php echo $this->config->base_url('mail/ls');?>" method="post">
			<input type="hidden" name="formhash" value="<?php echo formhash();?>">
			<table class="datalist" onmouseover="addMouseEvent(this);" style="table-layout:fixed">
				<tr>
					<th width="60"><input type="checkbox" name="chkall" id="chkall" onclick="checkall('delete[]')" class="checkbox" /><label for="chkall"><?php echo $this->lang->line('mail_delete');?></label></th>
					<th width="130"><?php echo $this->lang->line('mail_subject');?></th>
					<th width="60"><?php echo $this->lang->line('mail_to_username');?></th>
					<th width="80"><?php echo $this->lang->line('mail_add_time');?></th>
					<th width="140"><?php echo $this->lang->line('mail_failures');?></th>
					<th width="100"><?php echo $this->lang->line('mail_from_app');?></th>
					<th width="60"><?php echo $this->lang->line('mail_operate');?></th>
				</tr>
				<?php foreach($maillist as $mail):?>
					<tr>
						<td><input type="checkbox" name="delete[]" value="<?php echo $mail['mailid'];?>" class="checkbox" /></td>
						<td><?php echo $mail['subject'];?></td>
						<td><a href="mailto:<?php echo $mail['email'];?>"><?php if($mail['username']):?><?php echo $mail['username'];?><?php else:?><?php echo $this->lang->line('anonymity');?><?php endif;?></td>
						<td><?php echo $mail['dateline'];?></td>
						<td><?php echo $mail['failures'];?></td>
						<td><?php echo $mail['appname'];?></td>
						<td><a href="<?php echo $this->config->base_url('mail/send?mailid='.$mail['mailid']);?>"><?php echo $this->lang->line('mail_send');?></a></td>
					</tr>
				<?php endforeach;?>
				<tr class="nobg">
					<td><input type="submit" value="<?php echo $this->lang->line('submit');?>" class="btn" /></td>
					<td class="tdpage" colspan="<?php echo count($applist) + 4;?>"><?php echo $multipage;?></td>
				</tr>
			</table>
			</form>
		<?php else:?>
			<div class="mail">
				<p class="i"><?php echo $this->lang->line('list_empty');?></p>
			</div>
		<?php endif;?>
	</div>
</div>

<?php $this->load->view('footer');?>