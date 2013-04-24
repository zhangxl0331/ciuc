<?php $this->load->view('header');?>

<script src="<?php $this->config->base_url('js/common.js');?>" type="text/javascript"></script>

<div class="container">
	<?php if($status):?>
		<div class="correctmsg"><p><?php if($status == 1):?><?php echo $this->lang->line('announcepm_send_succeed');?><?php endif;?></p></div>
	<?php endif;?>
	<h3 class="marginbot">
		<a href="<?php echo $this->config->base_url('pm/ls');?>" class="sgbtn"><?php echo $this->lang->line('announcepm');?></a>
		<?php echo $this->lang->line('pm_send_announce');?>
		<a href="<?php echo $this->config->base_url('pm/clear');?>" class="sgbtn"><?php echo $this->lang->line('clear_pm');?></a>
	</h3>
	<div class="mainbox nomargin">
			<form action="<?php echo $this->config->base_url('pm/send');?>" method="post">
				<input type="hidden" name="formhash" value="<?php echo formhash();?>">
				<table class="opt">
					<tr>
						<th colspan="2"><?php echo $this->lang->line('pm_subject');?>:</th>
					</tr>
					<tr>
						<td><input type="text" class="txt" style="width: 500px" name="subject" /></td>
						<td></td>
					</tr>
					<tr>
						<th colspan="2"><?php echo $this->lang->line('pm_message');?>:</th>
					</tr>
					<tr>
						<td><textarea class="area" style="width: 600px;height: 100px" name="message"></textarea></td>
						<td valign="top"></td>
					</tr>
				</table>
				<div class="opt"><input type="submit" name="submit" value=" <?php echo $this->lang->line('submit');?> " class="btn" tabindex="3" /></div>
			</form>
		</div>
</div>

<?php $this->load->view('footer');?>