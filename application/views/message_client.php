<?php $this->load->view('header_client');?>
<br />
<table cellpadding="0" cellspacing="0" class="msg" style="width: 65%" align="center">
	<thead>
		<tr>
			<th>{lang message_title}</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>$message<br />
			<!--{if $redirect == 'BACK'}-->
				<a href="###" onclick="history.back();"> {lang message_back} </a>
			<!--{elseif $redirect}-->
				<a href="$redirect"> {lang message_redirect} </a>
				<script type="text/javascript">
				function redirect(url, time) {
					setTimeout("window.location='" + url + "'", time * 1000);
				}
				redirect('$redirect', 3);
				</script>
			<!--{/if}-->
			</td>
		</tr>
	</tbody>
</table>
</div>

<?php $this->load->view('footer_client');?>