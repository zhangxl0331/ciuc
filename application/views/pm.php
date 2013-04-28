<?php $this->load->view('header');?>

<script src="<?php echo $this->config->base_url('js/common.js');?>" type="text/javascript"></script>
<?php if($a == 'ls'):?>
<div class="container">
	<?php if($status):?>
		<div class="correctmsg"><p><?php if($status == 1):?><?php echo $this->lang->line('announcepm_deleted');?><?php endif;?></p></div>
	<?php endif;?>
	<h3 class="marginbot">
		<?php echo $this->lang->line('announcepm');?>
		<a href="<?php echo $this->config->base_url('pm/send');?>" class="sgbtn"><?php echo $this->lang->line('pm_send_announce');?></a>
		<a href="<?php echo $this->config->base_url('pm/clear');?>" class="sgbtn"><?php echo $this->lang->line('clear_pm');?></a>
	</h3>
	<div class="mainbox">
	<?php if($pmlist):?>
		<form action="<?php echo $this->config->base_url('pm/ls');?>" method="post">
			<input type="hidden" name="formhash" value="<?php echo formhash();?>">
			<table class="datalist fixwidth" onmouseover="addMouseEvent(this);">
				<tr>
					<th><input type="checkbox" name="chkall" id="chkall" onclick="checkall('delete[]')" class="checkbox" /><label for="chkall"><?php echo $this->lang->line('badword_delete');?></label></th>
					<th><?php echo $this->lang->line('title');?></th>
					<th><?php echo $this->lang->line('pm_from');?></th>
					<th><?php echo $this->lang->line('dateline');?></th>
				</tr>
				<?php foreach($pmlist as $pm):?>
					<tr>
						<td class="option"><input type="checkbox" name="delete[]" value="<?php echo $pm['pmid'];?>" class="checkbox" /></td>
						<td><a href="<?php echo $this->config->base_url('pm/view?pmid='.$pm['pmid'].'&$extra');?>"><?php if($pm['subject']):?><?php echo $pm['subject'];?><?php else:?><?php echo $this->lang->line('pm_notitle');?><?php endif;?></a></td>
						<td><?php echo $pm['msgfrom'];?></td>
						<td><?php echo $pm['dateline'];?></td>
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
<?php elseif($a == 'view'):?>
<div class="container">
	<h3 class="marginbot"><?php echo $this->lang->line('announcepm');?><a href="<?php echo $this->config->base_url('pm/ls?'.$extra);?>" class="sgbtn"><?php echo $this->lang->line('return');?></a></h3>
	<div class="mainbox">
	<?php if($pms):?>
		<table class="datalist fixwidth">
			<tr><th><?php echo $this->lang->line('pm_from');?></th><td><?php echo $pms['msgfrom'];?></td></tr>
			<tr><th><?php echo $this->lang->line('dateline');?></th><td><?php echo $pms['dateline'];?></td></tr>
			<tr><th><?php echo $this->lang->line('title');?></th><td><?php if($pms['subject']):?><?php echo $pms['subject'];?><?php else:?><?php echo $this->lang->line('pm_notitle');?><?php endif;?></td></tr>
		<tr class="nobg"><td colspan="2"><?php echo $pms['message'];?></td></tr>
		</table>
	<?php else:?>
		<div class="note">
			<p class="i"><?php echo $this->lang->line('list_empty');?></p>
		</div>
	<?php endif;?>
	</div>
</div>
<?php endif;?>

<?php $this->load->view('footer');?>