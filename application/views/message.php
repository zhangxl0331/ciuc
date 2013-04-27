<?php $this->load->view('header');?>
<?php if(empty($_REQUEST['inajax'])):?>
	<div class="container">
		<div class="ajax rtninfo">
			<div class="ajaxbg">
				<h4><?php echo $this->lang->line('message_title');?>:</h4>
				<p>$message</p>
				<?php if($redirect == 'BACK'):?>
					<p><a href="###" onclick="history.back();"><?php echo $this->lang->line('message_back');?></a></p>
				<?php elseif($redirect):?>
					<p><a href="<?php echo $redirect;?>"><?php echo $this->lang->line('message_redirect');?></a></p>
					<script type="text/javascript">
					function redirect(url, time) {
						setTimeout("window.location='" + url + "'", time * 1000);
					}
					redirect('$redirect', 3);
					</script>
				<?php endif;?>
			</div>
		</div>
	</div>
<?php else:?>
	<?php echo $message;?>
<?php endif;?>
<?php $this->load->view('footer');?>