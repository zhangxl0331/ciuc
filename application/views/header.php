<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo UC_CHARSET;?>" />
<title>UCenter Administrator's Control Panel</title>
<link rel="stylesheet" href="<?php echo $this->config->base_url('css/admincp.css');?>" type="text/css" media="all" />
<meta content="Comsenz Inc." name="Copyright" />
</head>
<body><div id="append"></div>
<?php if(!empty($iframe) && !empty($user)):?>
	<a class="othersoff" style="float:right;text-align:center" id="header_menu" onclick="headermenu(this)"><?php echo $this->lang->line('menu')?></a>
	<ul id="header_menu_menu" style="display: none">
		<li><a href="<?php echo $this->config->base_url('index/main?iframe=1');?>" target="main" class="tabon"><?php echo $this->lang->line('menu_index')?></a></li>
		<?php if($user['isfounder'] || $user['allowadminsetting']):?><li><a href="<?php echo $this->config->base_url('setting/ls?iframe=1');?>" target="main"><?php echo $this->lang->line('menu_basic_setting')?></a></li><?php endif;?>
		<?php if($user['isfounder'] || $user['allowadminsetting']):?><li><a href="<?php echo $this->config->base_url('setting/register?iframe=1');?>" target="main"><?php echo $this->lang->line('menu_register_setting')?></a></li><?php endif;?>
		<?php if($user['isfounder'] || $user['allowadminsetting']):?><li><a href="<?php echo $this->config->base_url('setting/mail?iframe=1');?>" target="main"><?php echo $this->lang->line('menu_mail_setting')?></a></li><?php endif;?>
		<?php if($user['isfounder'] || $user['allowadminapp']):?><li><a href="<?php echo $this->config->base_url('app/ls?iframe=1');?>" target="main"><?php echo $this->lang->line('menu_application')?></a></li><?php endif;?>
		<?php if($user['isfounder'] || $user['allowadminuser']):?><li><a href="<?php echo $this->config->base_url('user/ls?iframe=1');?>" target="main"><?php echo $this->lang->line('menu_manager_user')?></a></li><?php endif;?>
		<?php if($user['isfounder']):?><li><a href="<?php echo $this->config->base_url('admin/ls?iframe=1');?>" target="main"><?php echo $this->lang->line('menu_admin_user')?></a></li><?php endif;?>
		<?php if($user['isfounder'] || $user['allowadminpm']):?><li><a href="<?php echo $this->config->base_url('pm/ls?iframe=1');?>" target="main"><?php echo $this->lang->line('menu_pm')?></a></li><?php endif;?>
		<?php if($user['isfounder'] || $user['allowadmincredits']):?><li><a href="<?php echo $this->config->base_url('credit/ls?iframe=1');?>" target="main"><?php echo $this->lang->line('menu_credit_exchange')?></a></li><?php endif;?>
		<?php if($user['isfounder'] || $user['allowadminbadword']):?><li><a href="<?php echo $this->config->base_url('badword/ls?iframe=1');?>" target="main"><?php echo $this->lang->line('menu_censor_word')?></a></li><?php endif;?>
		<?php if($user['isfounder'] || $user['allowadmindomain']):?><li><a href="<?php echo $this->config->base_url('domain/ls?iframe=1');?>" target="main"><?php echo $this->lang->line('menu_domain_list')?></a></li><?php endif;?>
		<?php if($user['isfounder'] || $user['allowadmindb']):?><li><a href="<?php echo $this->config->base_url('db/ls?iframe=1');?>" target="main"><?php echo $this->lang->line('menu_db')?></a></li><?php endif;?>
		<?php if($user['isfounder']):?><li><a href="<?php echo $this->config->base_url('feed/ls?iframe=1');?>" target="main"><?php echo $this->lang->line('menu_data_list')?></a></li><?php endif;?>
		<?php if($user['isfounder'] || $user['allowadmincache']):?><li><a href="<?php echo $this->config->base_url('cache/update?iframe=1');?>" target="main"><?php echo $this->lang->line('menu_update_cache')?></a></li><?php endif;?>
		<?php if($user['isfounder']):?><li><a href="<?php echo $this->config->base_url('plugin/filecheck?iframe=1');?>" target="main"><?php echo $this->lang->line('plugin')?></a></li><?php endif;?>
		<a href="<?php echo $this->config->base_url('user/logout');?>" target="main"><?php echo $this->lang->line('menu_logout')?></a>
	</ul>
<?php endif;?>
<script type="text/javascript">
	function headermenu(ctrl) {
		ctrl.className = ctrl.className == 'otherson' ? 'othersoff' : 'otherson';
		var menu = document.getElementById('header_menu_body');
		if(!menu) {
			menu = document.createElement('div');
			menu.id = 'header_menu_body';
			menu.innerHTML = '<ul>' + document.getElementById('header_menu_menu').innerHTML + '</ul>';
			var obj = ctrl;
			var x = ctrl.offsetLeft;
			var y = ctrl.offsetTop;
			while((obj = obj.offsetParent) != null) {
				x += obj.offsetLeft;
				y += obj.offsetTop;
			}
			menu.style.left = x + 'px';
			menu.style.top = y + ctrl.offsetHeight + 'px';
			menu.className = 'togglemenu';
			menu.style.display = '';
			document.body.appendChild(menu);
		} else {
			menu.style.display = menu.style.display == 'none' ? '' : 'none';
		}
	}
</script>
