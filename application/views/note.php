<?php $this->load->view('header');?>

<script src="<?php echo $this->config->base_url('js/common.js');?>" type="text/javascript"></script>
<div class="container">
	<h3 class="marginbot">
		<a href="<?php echo $this->config->base_url('feed/ls');?>" class="sgbtn"><?php echo $this->lang->line('feed_list');?></a>
		<?php echo $this->lang->line('note_list');?>
		<?php if($user['isfounder'] || $user['allowadminlog']):?><a href="<?php echo $this->config->base_url('log/ls');?>" class="sgbtn"><?php echo $this->lang->line('menu_log');?></a><?php endif;?>
		<a href="<?php echo $this->config->base_url('mail/ls');?>" class="sgbtn"><?php echo $this->lang->line('mail_queue');?></a>
	</h3>
	<?php if($status == 2):?>
		<div class="correctmsg"><p><?php echo $this->lang->line('note_list_updated');?></p></div>
	<?php endif;?>
	<div class="mainbox">
		<?php if($notelist):?>
			<form action="<?php echo $this->config->base_url('note/ls');?>" method="post">
			<input type="hidden" name="formhash" value="<?php echo formhash();?>">
			<table class="datalist" onmouseover="addMouseEvent(this);" style="table-layout:fixed">
				<tr>
					<th width="60"><input type="checkbox" name="chkall" id="chkall" onclick="checkall('delete[]')" class="checkbox" /><label for="chkall"><?php echo $this->lang->line('note_delete');?></label></th>
					<th width="130"><?php echo $this->lang->line('note_operation');?></th>
					<th width="60"><?php echo $this->lang->line('note_times');?></th>
					<th width="50"><?php echo $this->lang->line('note_param');?></th>
					<th width="140"><?php echo $this->lang->line('note_last_note_time');?></th>
					<?php foreach($applist as $app):?>
						<?php if($app['recvnote']):?>
							<th width="100">$app[name]</th>
						<?php endif;?>
					<?php endforeach;?>
				</tr>
				<?php foreach($notelist as $note):?>
					<?php $debuginfo = htmlspecialchars(str_replace(array("\n", "\r", "'"), array('', '', "\'"), $note['getdata'].$note['postdata2']));?>
					<tr>
						<td><input type="checkbox" name="delete[]" value="<?php echo $note['noteid'];?>" class="checkbox" /></td>
						<td><strong><?php echo $note['operation'];?></strong></td>
						<td><?php echo $note['totalnum'];?></td>
						<td><a href="###" onclick="alert('<?php echo $debuginfo;?>');"><?php echo $this->lang->line('note_view');?></a></td>
						<td><?php echo $note['dateline'];?></td>
						<?php foreach($applist as $appid=>$app):?>
							<?php if($app['recvnote']):?>
								<td><?php echo $note['status'][$appid];?></td>
							<?php endif;?>
						<?php endforeach;?>
					</tr>
				<?php endforeach;?>
				<tr class="nobg">
					<td><input type="submit" value="<?php echo $this->lang->line('submit');?>" class="btn" /></td>
					<td class="tdpage" colspan="<?php echo count($applist) + 4;?>"><?php echo $multipage;?></td>
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

<?php $this->load->view('footer');?>