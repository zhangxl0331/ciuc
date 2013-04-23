<?php $this->load->view('header');?>

<div class="container">
	<h3 class="marginbot">		
		<a href="<?php echo $this->config->base_url('feed/ls');?>" class="sgbtn"><?php echo $this->lang->line('feed_list');?></a>
		<?php if($user['isfounder'] || $user['allowadminnote']):?><a href="<?php echo $this->config->base_url('note/ls');?>" class="sgbtn"><?php echo $this->lang->line('note_list');?></a><?php endif;?>
		<?php echo $this->lang->line('menu_log');?>
		<a href="<?php echo $this->config->base_url('mail/ls');?>" class="sgbtn"><?php echo $this->lang->line('mail_queue');?></a>
	</h3>
	<div class="mainbox">
		<?php if($loglist):?>
			<table class="datalist">
				<tr>
					<th><?php echo $this->lang->line('log_operator');?></th>
					<th><?php echo $this->lang->line('log_ip');?></th>
					<th><?php echo $this->lang->line('log_time');?></th>
					<th><?php echo $this->lang->line('log_operation');?></th>
					<th><?php echo $this->lang->line('log_extra');?></th>
				</tr>
				<?php foreach($loglist as $log):?>
					<tr>
						<td><strong><?php echo $log[1];?></strong></td>
						<td><?php echo $log[2];?></td>
						<td><?php echo $log[3];?></td>
						<td><?php echo $log[4];?></td>
						<td><?php echo $log[5];?></td>
					</tr>
				<?php endforeach;?>
				<tr class="nobg">
					<td class="tdpage" colspan="5"><?php echo $multipage;?></td>
				</tr>
			</table>
		<?php else:?>
			<div class="note">
				<p class="i"><?php echo $this->lang->line('list_empty');?></p>
			</div>
		<?php endif;?>
	</div>
</div>

<?php $this->load->view('footer');?>