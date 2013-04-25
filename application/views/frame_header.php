<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo UC_CHARSET;?>" />
<title>UCenter Administrator's Control Panel</title>
<link rel="stylesheet" href="<?php echo $this->config->base_url('css/admincp.css');?>" type="text/css" media="all" />
<meta content="Comsenz Inc." name="Copyright" />
</head>
<body>
<div class="mainhd">
	<div class="logo">UCenter Administrator's Control Panel</div>
	<div class="uinfo">
		<p><?php echo $this->lang->line('welcome');?>, <em><?php echo $username;?></em> [ <a href="<?php echo $this->config->base_url('user/logout');?>" target="_top"><?php echo $this->lang->line('menu_logout');?></a> ]</p>
		<?php if($admincp):?>
			<p id="others"><a href="#" class="othersoff" onclick="showmenu(this);"><?php echo $this->lang->line('menu_otheradmincp');?></a></p>
			<script type="text/javascript">
				function showmenu(ctrl) {
					ctrl.className = ctrl.className == 'otherson' ? 'othersoff' : 'otherson';
					var menu = parent.document.getElementById('toggle');
					if(!menu) {
						menu = parent.document.createElement('div');
						menu.id = 'toggle';
						menu.innerHTML = '<ul><?php echo $admincp;?></ul>';
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
						parent.document.body.appendChild(menu);
					} else {
						menu.style.display = menu.style.display == 'none' ? '' : 'none';
					}
				}
			</script>
		<?php endif;?>
	</div>
</div>
</body>
</html>