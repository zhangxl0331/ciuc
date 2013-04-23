<?php $this->load->view('header');?>

<script src="<?php echo $this->config->base_url('js/common.js')?>" type="text/javascript"></script>
<div class="container">
	<h3 class="marginbot">
		<?php echo $this->lang->line('feed_list');?>
		<?php if($user['isfounder'] || $user['allowadminnote']):?><a href="<?php echo $this->config->base_url('note/ls');?>admin.php?m=note&a=ls" class="sgbtn"><?php echo $this->lang->line('note_list');?></a><?php endif;?>
		<?php if($user['isfounder'] || $user['allowadminlog']):?><a href="<?php echo $this->config->base_url('log/ls');?>" class="sgbtn"><?php echo $this->lang->line('menu_log');?></a><?php endif;?>
		<a href="<?php echo $this->config->base_url('mail/ls');?>" class="sgbtn"><?php echo $this->lang->line('mail_queue');?></a>
	</h3>
	<div class="mainbox">
		<?php if($feedlist):?>
			<form action="<?php echo $this->config->base_url('note/ls');?>" method="post">
			<input type="hidden" name="formhash" value="<?php echo formhash();?>">
			<table class="datalist" style="table-layout:fixed">
				<tr>
					<th width="100"><?php echo $this->lang->line('dateline');?></th>
					<th>&nbsp;</th>
				</tr>
				<?php foreach($feedlist as $feed):?>
					<tr>
						<td><?php echo $feed['dateline'];?></td>
						<td><?php echo $feed['title_template'];?></td>
					</tr>
				<?php endforeach;?>
				<tr class="nobg">
					<td></td>
					<td class="tdpage"><?php echo $multipage;?></td>
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