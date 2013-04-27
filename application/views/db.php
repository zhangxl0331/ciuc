<?php $this->load->view('header');?>

<script src="<?php echo $this->config->base_url('js/common.js');?>" type="text/javascript"></script>

<div class="container">
	<?php if($operate == 'list'):?>
		<h3 class="marginbot">
			<a href="<?php echo $this->config->base_url('db/ls?o=export');?>" class="sgbtn"><?php echo $this->lang->line('db_export');?></a>
			<?php echo $this->lang->line('db_list');?>
		</h3>
		<div class="note fixwidthdec">
			<p class="i"><?php echo $this->lang->line('db_list_tips');?></p>
		</div>
		<div class="mainbox">
			<form id="theform">
				<table class="datalist" onmouseover="addMouseEvent(this);">
					<tr>
						<th nowrap="nowrap"><input type="checkbox" name="chkall" id="chkall" onclick="checkall('operate[]')" class="checkbox" /><label for="chkall"><?php echo $this->lang->line('delete');?></label></th>
						<th nowrap="nowrap"><?php echo $this->lang->line('db_backup_dir');?></th>
						<th nowrap="nowrap"><?php echo $this->lang->line('db_backup_date');?></th>
						<th nowrap="nowrap"><?php echo $this->lang->line('db_operation');?></th>
						<th nowrap="nowrap">&nbsp;</th>
						<th nowrap="nowrap">&nbsp;</th>
					</tr>
					<?php foreach($baklist as $bak):?>
						<tr>
							<td width="50"><input type="checkbox" name="operate[]" value="<?php echo $bak['name'];?>" class="checkbox" /></td>
							<td width="200"><a href="<?php echo $this->config->base_url('db/ls?o=view&dir='.$bak['name']);?>"><?php echo $bak['name'];?></a></td>
							<td width="120"><?php echo $bak['date'];?></td>
							<td><a href="<?php echo $this->config->base_url('db/ls?o=view&dir='.$bak['name']);?>"><?php echo $this->lang->line('db_detail');?></a></td>
							<td id="db_operate_<?php echo $bak['name'];?>"></td>
							<td><iframe id="operate_iframe_<?php echo $bak['name'];?>" style="display:none" width="0" height="0"></iframe></td>
						</tr>
					<?php endforeach;?>
					<tr class="nobg">
						<td colspan="6"><input type="button" value="<?php echo $this->lang->line('submit');?>" onclick="db_delete($('theform'))" class="btn" /></td>
					</tr>
				</table>
			</form>
		</div>
	<?php elseif($operate == 'view'):?>
		<h3 class="marginbot">
			<a href="<?php echo $this->config->base_url('db/ls?o=export');?>" class="sgbtn"><?php echo $this->lang->line('db_export');?></a>
			<?php echo $this->lang->line('db_list');?>
		</h3>
		<div class="note fixwidthdec">
			<p class="i"><?php echo $this->lang->line('db_import_tips');?></p>
		</div>
		<div class="mainbox">
			<form id="theform">
			<table class="datalist" onmouseover="addMouseEvent(this);">
				<tr>
					<th nowrap="nowrap"><input type="checkbox" name="chkall" id="chkall" onclick="checkall('operate[]')" class="checkbox" /><label for="chkall"><?php echo $this->lang->line('db_import');?></label></th>
					<th nowrap="nowrap"><?php echo $this->lang->line('app_id');?></th>
					<th nowrap="nowrap"><?php echo $this->lang->line('app_name');?></th>
					<th nowrap="nowrap"><?php echo $this->lang->line('app_url');?></th>
					<th nowrap="nowrap">&nbsp;</th>
					<th nowrap="nowrap">&nbsp;</th>
				</tr>
				<tr>
					<td width="50"><input type="checkbox" name="operate_uc" class="checkbox" /></td>
					<td width="35"></td>
					<td><strong>UCenter</strong></td>
					<td></td>
					<td id="db_operate_0"><img src="images/correct.gif" border="0" class="statimg" /><span class="green"><?php echo $this->lang->line('dumpfile_exists');?></span></td>
					<td><iframe id="operate_iframe_0" style="display:none" width="0" height="0"></iframe></td>
				</tr>
				<?php foreach($applist as $app):?>
					<tr>
						<td width="50"><input type="checkbox" name="operate[]" value="<?php echo $app['appid'];?>" class="checkbox" /></td>
						<td width="35"><?php echo $app['appid'];?></td>
						<td width="160"><a href="<?php echo $this->config->base_url('app/detail?appid='.$app['appid']);?>"><strong><?php echo $app['name'];?></strong></a></td>
						<td><a href="<?php echo $app['url'];?>" target="_blank"><?php echo $app['url'];?></a></td>
						<td id="db_operate_<?php echo $app['appid'];?>"></td>
						<td><iframe id="operate_iframe_<?php echo $app['appid'];?>" src="<?php echo $this->config->base_url('db/ls?o=ping&appid='.$app['appid'].'&dir='.$dir);?>" style="display:none" width="0" height="0"></iframe></td>
					</tr>
				<?php endforeach;?>
				<tr class="nobg">
					<td colspan="6"><input type="button" value="<?php echo $this->lang->line('submit');?>" onclick="db_operate($('theform'), 'import')" class="btn" /></td>
				</tr>
			</table>
			</form>
		</div>
	<?php else:?>
		<h3 class="marginbot">
			<?php echo $this->lang->line('db_export');?>
			<a href="<?php echo $this->config->base_url('db/ls?o=list');?>" class="sgbtn"><?php echo $this->lang->line('db_list');?></a>
		</h3>
		<div class="mainbox">
			<form id="theform">
			<table class="datalist" onmouseover="addMouseEvent(this);">
				<tr>
					<th nowrap="nowrap"><input type="checkbox" name="chkall" id="chkall" checked="checked" onclick="checkall('operate[]')" class="checkbox" /><label for="chkall"><?php echo $this->lang->line('db_export');?></label></th>
					<th nowrap="nowrap"><?php echo $this->lang->line('app_id');?></th>
					<th nowrap="nowrap"><?php echo $this->lang->line('app_name');?></th>
					<th nowrap="nowrap"><?php echo $this->lang->line('app_url');?></th>
					<th nowrap="nowrap">&nbsp;</th>
					<th nowrap="nowrap">&nbsp;</th>
				</tr>
				<tr>
					<td width="50"><input type="checkbox" name="operate_uc" disabled="disabled" checked="checked" class="checkbox" /></td>
					<td width="35"></td>
					<td><strong>UCenter</strong></td>
					<td></td>
					<td id="db_operate_0"></td>
					<td><iframe id="operate_iframe_0" style="display:none" width="0" height="0"></iframe></td>
				</tr>
				<?php foreach($applist as $app):?>
					<tr>
						<td width="50"><input type="checkbox" name="operate[]" value="<?php echo $app['appid'];?>" checked="checked" class="checkbox" /></td>
						<td width="35"><?php echo $app['appid'];?></td>
						<td width="160"><a href="<?php $this->config->base_url('app/detail?appid='.$app['appid']);?>"><strong><?php echo $app['name'];?></strong></a></td>
						<td><a href="<?php echo $app['url'];?>" target="_blank"><?php echo $app['url'];?></a></td>
						<td id="db_operate_<?php echo $app['appid'];?>"></td>
						<td><iframe id="operate_iframe_<?php echo $app['appid'];?>" style="display:none" width="0" height="0"></iframe></td>
					</tr>
				<?php endforeach;?>
				<tr class="nobg">
					<td colspan="6"><input type="button" value="<?php echo $this->lang->line('submit');?>" onclick="db_operate($('theform'), 'export')" class="btn" /></td>
				</tr>
			</table>
			</form>
		</div>
	<?php endif;?>
</div>

<script type="text/javascript">
var import_status = new Array();
function db_delete(theform) {
	var lang_tips = '<?php echo $this->lang->line('db_start_delete_dumpfile');?>';
	if(!confirm('<?php echo $this->lang->line('db_delete_dumpfile_confirm');?>')) {
		return;
	}
	for(i = 0; theform[i] != null; i++) {
		ele = theform[i];
		if(/^operate\[/.test(ele.name) && ele.type == "checkbox" && ele.checked) {
			show_status(ele.value, lang_tips);
			$('operate_iframe_'+ele.value).src = '<?php echo $this->config->base_url('db/delete?backupdir=');?>'+ele.value;
		}
	}
}

function db_operate(theform, operate) {
	operate = operate == 'import' ? 'import' : 'export';
	if(operate == 'export') {
		var lang_tips = '<?php echo $this->lang->line('db_start_export_dumpfile');?>';
	} else {
		if(!confirm('<?php echo $this->lang->line('db_import_dumpfile_confirm');?>')) {
			return;
		}
		if(theform.operate_uc.checked && !confirm('<?php echo $this->lang->line('db_import_uc_dumpfile_confirm');?>')) {
			return;
		}
		var lang_tips = '<?php echo $this->lang->line('db_start_import_dumpfile');?>';
	}

	if(theform.operate_uc.checked) {
		show_status(0, lang_tips);
		$('operate_iframe_0').src = '<?php echo $this->config->base_url('db/operate?t=');?>'+operate+'&appid=0&backupdir=<?php echo $dir;?>';
	}
	for(i = 0; theform[i] != null; i++) {
		ele = theform[i];
		if(/^operate\[\]$/.test(ele.name) && ele.type == "checkbox" && ele.checked) {
			if(operate != 'import' || import_status[ele.value] != false) {
				show_status(ele.value, lang_tips);
				$('operate_iframe_'+ele.value).src = '<?php echo $this->config->base_url('db/operate?t=');?>'+operate+'&appid='+ele.value+'&backupdir=<?php echo $dir;?>';
			}
		}
	}
}

function show_status(extid, msg) {
	var o = $('db_operate_'+extid);
	o.innerHTML = msg;
}
</script>

<?php $this->load->view('footer');?>