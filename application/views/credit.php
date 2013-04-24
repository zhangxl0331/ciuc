<?php $this->load->view('header');?>

<script src="<?php $this->config->base_url('js/common.js');?>" type="text/javascript"></script>
<div class="container">

	<div class="note">
		<p class="i"><?php echo $this->lang->line('creditexchange_tips');?></p>
	</div>

	<?php if($status):?>
		<div class="<?php if($status > 0):?>correctmsg<?php else:?>errormsg<?php endif;?>"><p><?php if($status == 1):?><?php echo $this->lang->line('creditexchange_updated');?><?php elseif($status == -1):?><?php echo $this->lang->line('creditexchange_invalid');?><?php endif;?></p></div>
	<?php endif;?>
	<div class="hastabmenu">
		<ul class="tabmenu">
			<li class="tabcurrent"><a href="#" class="tabcurrent"><?php echo $this->lang->line('creditexchange_update');?></a></li>
		</ul>
		<div class="tabcontentcur">
			<form id="creditform" action="<?php echo $this->config->base_url('credit/ls?addexchange=yes');?>" method="post">
			<input type="hidden" name="formhash" value="<?php echo formhash();?>">
			<table class="dbtb">
				<tr>
					<td class="tbtitle"><?php echo $this->lang->line('creditexchange_fromto');?>:</td>
					<td>
						<select onchange="switchcredit('src', this.value)" name="appsrc">
							<option><?php echo $this->lang->line('creditexchange_select');?></option><?php echo $appselect;?>
						</select><span id="src"></span>
						&nbsp;&gt;&nbsp;
						<select onchange="switchcredit('desc', this.value)" name="appdesc">
							<option><?php echo $this->lang->line('creditexchange_select');?></option><?php echo $appselect;?>
						</select><span id="desc"></span>
					</td>
				</tr>
				<tr>
					<td class="tbtitle"><?php echo $this->lang->line('creditexchange_ratio');?>:</td>
					<td>
						<input name="ratiosrc" size="3" value="<?php echo $ratiosrc;?>" class="txt" style="margin-right:0" />
						&nbsp;:&nbsp;
						<input name="ratiodesc" size="3" value="<?php echo $ratiodesc;?>" class="txt" />
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type="submit" value="<?php echo $this->lang->line('submit');?>" class="btn" /> &nbsp;
						<input type="button" value="<?php echo $this->lang->line('creditexchange_syncappcredits');?>" class="btn" onclick="location.href='<?php echo $this->config->base_url('creadit/sync?sid='.$sid);?>'" />
					</td>
				</tr>
			</table>
			<div style="display: none">
			<script type="text/javascript">
			var credit = new Array();
			<?php foreach($creditselect as $select):?><?php echo $select;?><?php endforeach;?>
			<?php if($appsrc):?>
				setselect($('creditform').appsrc, <?php echo $appsrc;?>);
				switchcredit('src', <?php echo $appsrc;?>);
			<?php endif;?>
			<?php if($appdesc):?>
				setselect($('creditform').appdesc, <?php echo $appdesc;?>);
				switchcredit('desc', <?php echo $appdesc;?>);
			<?php endif;?>
			<?php if($creditsrc):?>
				setselect($('creditform').creditsrc, <?php echo $creditsrc;?>);
			<?php endif;?>
			<?php if($creditdesc):?>
				setselect($('creditform').creditdesc, <?php echo $creditsrc;?>);
			<?php endif;?>
			</script>
			</div>
			</form>
		</div>
	</div>
	<br />
	<h3><?php echo $this->lang->line('creditexchange');?></h3>
	<div class="mainbox">
		<?php if($creditexchange):?>
			<form action="<?php echo $this->config->base_url('credit/ls?delexchange=yes');?>" method="post">
			<input type="hidden" name="formhash" value="<?php echo formhash();?>">
			<table class="datalist fixwidth" onmouseover="addMouseEvent(this);">
				<tr>
					<th><input type="checkbox" name="chkall" id="chkall" onclick="checkall('delete[]')" class="checkbox" /><label for="chkall"><?php echo $this->lang->line('badword_delete');?></label></th>
					<th style="padding-right: 11px; text-align: right"><?php echo $this->lang->line('creditexchange_fromto');?></th>
					<th></th>
					<th style="text-align: center"><?php echo $this->lang->line('creditexchange_ratio');?></th>
				</tr>
				<?php foreach($creditexchange as $key=>$exchange):?>
					<tr>
						<td class="option"><input type="checkbox" name="delete[]" value="<?php echo $key;?>" class="checkbox" /></td>
						<td align="right"><?php echo $exchange['appsrc'];?> <?php echo $exchange['creditsrc'];?></td>
						<td>&nbsp;&gt;&nbsp;<?php echo $exchange['appdesc'];?> <?php echo $exchange['creditdesc'];?></td>
						<td align="center"><?php echo $exchange['ratiosrc'];?> : <?php echo $exchange['ratiodesc'];?></td>
					</tr>
				<?php endforeach;?>
				<tr class="nobg">
					<td><input type="submit" value="<?php echo $this->lang->line('submit');?>" class="btn" /></td>
				</tr>
			</table>
			</form>
		<?php else:?>
			<div class="note">
				<p class="i"><?php echo $this->lang->line('list_empty');?></p>
			</div>
		<?php endif;?>
</div>

<?php $this->load->view('footer');?>