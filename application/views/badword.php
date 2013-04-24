<?php $this->load->view('header');?>

<script src="<?php $this->config->base_url('js/common.js');?>" type="text/javascript"></script>
<script type="text/javascript">
	function switchbtn(btn) {
		$('srchuserdiv').style.display = btn == 'srch' ? '' : 'none';
		$('srchuserdiv').className = btn == 'srch' ? 'tabcontentcur' : '' ;
		$('srchuserbtn').className = btn == 'srch' ? 'tabcurrent' : '';
		$('adduserdiv').style.display = btn == 'srch' ? 'none' : '';
		$('adduserdiv').className = btn == 'srch' ? '' : 'tabcontentcur';
		$('adduserbtn').className = btn == 'srch' ? '' : 'tabcurrent';
		$('tmenu').style.height = btn == 'srch' ? '80'+'px' : '280'+'px';
	}
</script>
<div class="container">
	<?php if($status):?>
		<div class="correctmsg"><p><?php if($status == 2):?><?php echo $this->lang->line('badword_list_updated');?><?php elseif($status == 1):?><?php echo $this->lang->line('badword_add_succeed');?><?php endif;?></p></div>
	<?php endif;?>
	<div id="tmenu" class="hastabmenu">
		<ul class="tabmenu">
			<li id="srchuserbtn" class="tabcurrent"><a href="#" onclick="switchbtn('srch');"><?php echo $this->lang->line('badword_add');?></a></li>
			<li id="adduserbtn"><a href="#" onclick="switchbtn('add');"><?php echo $this->lang->line('badword_multi_add');?></a></li>
		</ul>
		<div id="adduserdiv" class="tabcontent" style="display:none;">
			<form action="<?php echo $this->config->base_url('badword/ls');?>" method="post">
				<ul class="tiplist">
					<?php echo $this->lang->line('badword_multi_add_comment');?>
				</ul>
				<textarea name="badwords" class="bigarea"></textarea>
				<ul class="optlist">
					<li><input type="radio" name="type" value="2" id="badwordsopt2" class="radio" checked="checked" /><label for="badwordsopt2"><?php echo $this->lang->line('badword_skip');?></label></li>
					<li><input type="radio" name="type" value="1" id="badwordsopt1" class="radio" /><label for="badwordsopt1"><?php echo $this->lang->line('badword_overwrite');?></label></li>
					<li><input type="radio" name="type" value="0" id="badwordsopt0" class="radio" /><label for="badwordsopt0"><?php echo $this->lang->line('badword_truncate');?></label></li>
				</ul>
				<input type="submit" name="multisubmit" value="<?php echo $this->lang->line('submit');?>" class="btn" />
			</form>

		</div>
		<div id="srchuserdiv" class="tabcontentcur">
			<form action="<?php echo $this->config->base_url('badword/ls');?>" method="post">
			<input type="hidden" name="formhash" value="<?php echo formhash();?>">
			<table>
				<tr>
					<td><?php echo $this->lang->line('badword_keyword');?>:</td>
					<td><input type="text" name="findnew" class="txt" /></td>
					<td><?php echo $this->lang->line('badword_replace');?>:</td>
					<td><input type="text" name="replacementnew" class="txt" /></td>
					<td><input type="submit" value="<?php echo $this->lang->line('submit');?>"  class="btn" /></td>
				</tr>
			</table>
			</form>
		</div>
	</div>
	<br />
	<h3><?php echo $this->lang->line('badword_list');?></h3>
	<div class="mainbox">
		<?php if($badwordlist):?>
			<form action="<?php echo $this->config->base_url('badword/ls');?>" method="post">
				<table class="datalist fixwidth">
					<tr>
						<th><input type="checkbox" name="chkall" id="chkall" onclick="checkall('delete[]')" class="checkbox" /><label for="chkall"><?php echo $this->lang->line('badword_delete');?></label></th>
						<th style="text-align:right;padding-right:11px;"><?php echo $this->lang->line('badword_add');?><?php echo $this->lang->line('badword_keyword');?></th>
						<th></th>
						<th><?php echo $this->lang->line('badword_replace');?></th>
						<th><?php echo $this->lang->line('badword_admin');?></th>
					</tr>
					<?php foreach($badwordlist as $badword):?>
						<tr>
							<td class="option"><input type="checkbox" name="delete[]" value="<?php echo $badword['id'];?>" class="checkbox" /></td>
							<td class="tdinput"><input type="text" name="find[<?php echo $badword['id'];?>]" value="<?php echo $badword['find'];?>" title="<?php echo $this->lang->line('shortcut_tips');?>" class="txtnobd" onblur="this.className='txtnobd'" onfocus="this.className='txt'" /></td>
							<td class="tdarrow">&gt;</td>
							<td class="tdinput"><input type="text" name="replacement[<?php echo $badword['id'];?>]" value="<?php echo $badword['replacement'];?>" title="<?php echo $this->lang->line('shortcut_tips');?>" class="txtnobd"  onblur="this.className='txtnobd'" onfocus="this.className='txt'" style="text-align:left;" /></td>
							<td><?php echo $badword['admin'];?></td>
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

<?php $this->load->view('footer');?>