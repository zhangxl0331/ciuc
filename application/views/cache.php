<?php $this->load->view('header');?>

<script src="<?php echo $this->config->base_url('js/common.js')?>" type="text/javascript"></script>

<div class="container">
	<h3><?php echo $this->lang->line('cache_update');?></h3>
	<?php if($updated):?>
		<div class="correctmsg"><p><?php echo $this->lang->line('update_succeed');?></p></div>
	<?php endif;?>
	<div class="mainbox">
		<form action="<?php echo $this->config->base_url('cache/update');?>" method="post">
			<input type="hidden" name="formhash" value="<?php echo formhash();?>">
			<table class="datalist fixwidth" onmouseover="addMouseEvent(this);">
				<tr>
					<td class="option"><input type="checkbox" name="type[]" value="data" class="checkbox" checked="checked" /></td>
					<td><?php echo $this->lang->line('cache_update_data');?></td>
				</tr>
				<tr>
					<td class="option"><input type="checkbox" name="type[]" value="tpl" class="checkbox" /></td>
					<td><?php echo $this->lang->line('cache_update_tpl');?></td>
				</tr>
				<tr class="nobg">
					<td colspan="2"><input type="submit" value="<?php echo $this->lang->line('submit');?>" class="btn" /></td>
				</tr>
			</table>
		</form>
	</div>
</div>

<?php $this->load->view('footer');?>