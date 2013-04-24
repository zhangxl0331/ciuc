<?php $this->load->view('header');?>

<script src="<?php $this->config->base_url('js/common.js');?>" type="text/javascript"></script>
<div class="container">
	<?php if($status):?>
		<div class="<?php if($status > 0):?>correctmsg<?php else:?>errormsg<?php endif;?>"><p><?php if($status == 2):?><?php echo $this->lang->line('domain_list_updated');?><?php elseif($status == 1):?><?php echo $this->lang->line('domain_add_succeed');?><?php endif;?></p></div>
	<?php endif;?>
	<div class="hastabmenu">
		<ul class="tabmenu">
			<li class="tabcurrent"><a href="#" class="tabcurrent"><?php echo $this->lang->line('domain_add');?></a></li>
		</ul>
		<div class="tabcontentcur">
			<form action="<?php echo $this->config->base_url('domain/ls');?>" method="post">
			<input type="hidden" name="formhash" value="<?php echo formhash();?>">
			<table>
				<tr>
					<td><?php echo $this->lang->line('domain');?>:</td>
					<td><input type="text" name="domainnew" class="txt" /></td>
					<td><?php echo $this->lang->line('ip');?>:</td>
					<td><input type="text" name="ipnew" class="txt" /></td>
					<td><input type="submit" value="<?php echo $this->lang->line('submit');?>"  class="btn" /></td>
				</tr>
			</table>
			</form>
		</div>
	</div>
	<h3><?php echo $this->lang->line('domain_list');?></h3>
	<div class="mainbox">
		<?php if($domainlist):?>
			<form action="<?php echo $this->config->base_url('domain/ls');?>" method="post">
				<input type="hidden" name="formhash" value="<?php echo formhash();?>">
				<table class="datalist fixwidth">
					<tr>
						<th width="10%"><input type="checkbox" name="chkall" id="chkall" onclick="checkall('delete[]')" class="checkbox" /><label for="chkall"><?php echo $this->lang->line('delete');?></label></th>
						<th width="60%"><?php echo $this->lang->line('domain');?></th>
						<th width="30%"><?php echo $this->lang->line('ip');?></th>
					</tr>
					<?php foreach($domainlist as $domain):?>
					<tr>
						<td><input type="checkbox" name="delete[]" value="<?php echo $domain['id'];?>" class="checkbox" /></td>
						<td><input type="text" name="domain[<?php echo $domain['id'];?>]" value="$domain[domain]" title="<?php echo $this->lang->line('shortcut_tips');?>" class="txtnobd" onblur="this.className='txtnobd'" onfocus="this.className='txt'" style="text-align:left;" /></td>
						<td><input type="text" name="ip[<?php echo $domain['ip'];?>]" value="<?php echo $domain['ip'];?>" title="<?php echo $this->lang->line('shortcut_tips');?>" class="txtnobd" onblur="this.className='txtnobd'" onfocus="this.className='txt'" style="text-align:left;" /></td>
					</tr>
					<?php endforeach;?>
					<tr class="nobg">
						<td><input type="submit" value="<?php echo $this->lang->line('submit');?>" class="btn" /></td>
						<td class="tdpage" colspan="2"><?php echo $multipage;?></td>
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