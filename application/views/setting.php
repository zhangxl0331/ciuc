<?php $this->load->view('header');?>

<script src="<?php echo $this->config->base_url('js/common.js');?>" type="text/javascript"></script>

<div class="container">
	<?php if($updated):?>
		<div class="correctmsg"><p><?php echo $this->lang->line('update_succeed');?></p></div>
	<?php elseif($method == 'register'):?>
		<div class="note fixwidthdec"><p class="i"><?php echo $this->lang->line('setting_register_tips');?></p></div>
	<?php endif;?>
	<?php if($method == 'ls'):?>
		<div class="mainbox nomargin">
			<form action="<?php echo $this->config->base_url('setting/ls');?>" method="post">
				<input type="hidden" name="formhash" value="<?php echo formhash();?>">
				<table class="opt">
					<tr>
						<th colspan="2"><?php echo $this->lang->line('setting_dateformat');?>:</th>
					</tr>
					<tr>
						<td><input type="text" class="txt" name="dateformat" value="<?php echo $dateformat;?>" /></td>
						<td><?php echo $this->lang->line('setting_dateformat_comment');?></td>
					</tr>
					<tr>
						<th colspan="2"><?php echo $this->lang->line('setting_timeformat');?>:</th>
					</tr>
					<td>
						<input type="radio" id="hr24" class="radio" name="timeformat" value="1" <?php echo isset($timeformat[1])?$timeformat[1]:'';?> /><label for="hr24"><?php echo $this->lang->line('setting_timeformat_hr24');?></label>
						<input type="radio" id="hr12" class="radio" name="timeformat" value="0" <?php echo isset($timeformat[0])?$timeformat[0]:'';?> /><label for="hr12"><?php echo $this->lang->line('setting_timeformat_hr12');?></label>
					</td>
					</tr>
					<tr>
						<th colspan="2"><?php echo $this->lang->line('setting_timeoffset');?>:</th>
					</tr>
					<tr>
						<td>
							<select name="timeoffset">
								<option value="-12" <?php echo isset($checkarray['-12'])?$checkarray['-12']:'';?>>(GMT -12:00) Eniwetok, Kwajalein</option>
								<option value="-11" <?php echo isset($checkarray['-11'])?$checkarray['-11']:'';?>>(GMT -11:00) Midway Island, Samoa</option>
								<option value="-10" <?php echo isset($checkarray['-10'])?$checkarray['-10']:'';?>>(GMT -10:00) Hawaii</option>
								<option value="-9" <?php echo isset($checkarray['-9'])?$checkarray['-9']:'';?>>(GMT -09:00) Alaska</option>
								<option value="-8" <?php echo isset($checkarray['-8'])?$checkarray['-8']:'';?>>(GMT -08:00) Pacific Time (US &amp; Canada), Tijuana</option>
								<option value="-7" <?php echo isset($checkarray['-7'])?$checkarray['-7']:'';?>>(GMT -07:00) Mountain Time (US &amp; Canada), Arizona</option>
								<option value="-6" <?php echo isset($checkarray['-6'])?$checkarray['-6']:'';?>>(GMT -06:00) Central Time (US &amp; Canada), Mexico City</option>
								<option value="-5" <?php echo isset($checkarray['-5'])?$checkarray['-5']:'';?>>(GMT -05:00) Eastern Time (US &amp; Canada), Bogota, Lima, Quito</option>
								<option value="-4" <?php echo isset($checkarray['-4'])?$checkarray['-4']:'';?>>(GMT -04:00) Atlantic Time (Canada), Caracas, La Paz</option>
								<option value="-3.5" <?php echo isset($checkarray['-3.5'])?$checkarray['-3.5']:'';?>>(GMT -03:30) Newfoundland</option>
								<option value="-3" <?php echo isset($checkarray['-3'])?$checkarray['-3']:'';?>>(GMT -03:00) Brassila, Buenos Aires, Georgetown, Falkland Is</option>
								<option value="-2" <?php echo isset($checkarray['-2'])?$checkarray['-2']:'';?>>(GMT -02:00) Mid-Atlantic, Ascension Is., St. Helena</option>
								<option value="-1" <?php echo isset($checkarray['-1'])?$checkarray['-1']:'';?>>(GMT -01:00) Azores, Cape Verde Islands</option>
								<option value="0" <?php echo isset($checkarray['0'])?$checkarray['0']:'';?>>(GMT) Casablanca, Dublin, Edinburgh, London, Lisbon, Monrovia</option>
								<option value="1" <?php echo isset($checkarray['1'])?$checkarray['1']:'';?>>(GMT +01:00) Amsterdam, Berlin, Brussels, Madrid, Paris, Rome</option>
								<option value="2" <?php echo isset($checkarray['2'])?$checkarray['2']:'';?>>(GMT +02:00) Cairo, Helsinki, Kaliningrad, South Africa</option>
								<option value="3" <?php echo isset($checkarray['3'])?$checkarray['3']:'';?>>(GMT +03:00) Baghdad, Riyadh, Moscow, Nairobi</option>
								<option value="3.5" <?php echo isset($checkarray['3.5'])?$checkarray['3.5']:'';?>>(GMT +03:30) Tehran</option>
								<option value="4" <?php echo isset($checkarray['4'])?$checkarray['4']:'';?>>(GMT +04:00) Abu Dhabi, Baku, Muscat, Tbilisi</option>
								<option value="4.5" <?php echo isset($checkarray['4.5'])?$checkarray['4.5']:'';?>>(GMT +04:30) Kabul</option>
								<option value="5" <?php echo isset($checkarray['5'])?$checkarray['5']:'';?>>(GMT +05:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
								<option value="5.5" <?php echo isset($checkarray['5.5'])?$checkarray['5.5']:'';?>>(GMT +05:30) Bombay, Calcutta, Madras, New Delhi</option>
								<option value="5.75" <?php echo isset($checkarray['5.75'])?$checkarray['5.75']:'';?>>(GMT +05:45) Katmandu</option>
								<option value="6" <?php echo isset($checkarray['6'])?$checkarray['6']:'';?>>(GMT +06:00) Almaty, Colombo, Dhaka, Novosibirsk</option>
								<option value="6.5" <?php echo isset($checkarray['6.5'])?$checkarray['6.5']:'';?>>(GMT +06:30) Rangoon</option>
								<option value="7" <?php echo isset($checkarray['7'])?$checkarray['7']:'';?>>(GMT +07:00) Bangkok, Hanoi, Jakarta</option>
								<option value="8" <?php echo isset($checkarray['8'])?$checkarray['8']:'';?>>(GMT +08:00) &#x5317;&#x4eac;(Beijing), Hong Kong, Perth, Singapore, Taipei</option>
								<option value="9" <?php echo isset($checkarray['9'])?$checkarray['9']:'';?>>(GMT +09:00) Osaka, Sapporo, Seoul, Tokyo, Yakutsk</option>
								<option value="9.5" <?php echo isset($checkarray['9.5'])?$checkarray['9.5']:'';?>>(GMT +09:30) Adelaide, Darwin</option>
								<option value="10" <?php echo isset($checkarray['10'])?$checkarray['10']:'';?>>(GMT +10:00) Canberra, Guam, Melbourne, Sydney, Vladivostok</option>
								<option value="11" <?php echo isset($checkarray['11'])?$checkarray['11']:'';?>>(GMT +11:00) Magadan, New Caledonia, Solomon Islands</option>
								<option value="12" <?php echo isset($checkarray['12'])?$checkarray['12']:'';?>>(GMT +12:00) Auckland, Wellington, Fiji, Marshall Island</option>
							</select>
						</td>
						<td><?php echo $this->lang->line('setting_timeoffset_comment');?></td>
					</tr>

					<tr>
						<th colspan="2"><?php echo $this->lang->line('setting_pmsendregdays');?>:</th>
					</tr>
					<tr>
						<td><input type="text" class="txt" name="pmsendregdays" value="<?php echo $pmsendregdays;?>" /></td>
						<td><?php echo $this->lang->line('setting_pmsendregdays_comment');?></td>
					</tr>

					<tr>
						<th colspan="2"><?php echo $this->lang->line('setting_pmlimit1day');?>:</th>
					</tr>
					<tr>
						<td><input type="text" class="txt" name="pmlimit1day" value="<?php echo $pmlimit1day;?>" /></td>
						<td><?php echo $this->lang->line('setting_pmlimit1day_comment');?></td>
					</tr>

					<tr>
						<th colspan="2"><?php echo $this->lang->line('setting_pmfloodctrl');?>:</th>
					</tr>
					<tr>
						<td><input type="text" class="txt" name="pmfloodctrl" value="<?php echo $pmfloodctrl;?>" /></td>
						<td><?php echo $this->lang->line('setting_pmfloodctrl_comment');?></td>
					</tr>

					<tr>
						<th colspan="2"><?php echo $this->lang->line('setting_pmcenter');?>:</th>
					</tr>
					<tr>
					<td>
						<input type="radio" id="pmcenteryes" class="radio" name="pmcenter" value="1" <?php echo isset($pmcenter[1])?$pmcenter[1]:'';?> onclick="$('hidden1').style.display=''"  /><label for="pmcenteryes"><?php echo $this->lang->line('yes');?></label>
						<input type="radio" id="pmcenterno" class="radio" name="pmcenter" value="0" <?php echo isset($pmcenter[0])?$pmcenter[0]:'';?> onclick="$('hidden1').style.display='none'" /><label for="pmcenterno"><?php echo $this->lang->line('no');?></label>
					</td>
					<td><?php echo $this->lang->line('setting_pmcenter_comment');?></td>
					</tr>
					<tbody id="hidden1" <?php echo $pmcenter['display'];?>>
					<tr>
						<th colspan="2"><?php echo $this->lang->line('setting_sendpmseccode');?>:</th>
					</tr>
					<tr>
						<td>
							<input type="radio" id="sendpmseccodeyes" class="radio" name="sendpmseccode" value="1" <?php echo isset($sendpmseccode[1])?$sendpmseccode[1]:'';?> /><label for="sendpmseccodeyes"><?php echo $this->lang->line('yes');?></label>
							<input type="radio" id="sendpmseccodeno" class="radio" name="sendpmseccode" value="0" <?php echo isset($sendpmseccode[0])?$sendpmseccode[0]:'';?> /><label for="sendpmseccodeno"><?php echo $this->lang->line('no');?></label>
						</td>
						<td><?php echo $this->lang->line('setting_sendpmseccode_comment');?></td>
					</tr>
					</tbody>
				</table>
				<div class="opt"><input type="submit" name="submit" value=" <?php echo $this->lang->line('submit');?> " class="btn" tabindex="3" /></div>
			</form>
		</div>
	<?php elseif($method == 'register'):?>
		<div class="mainbox nomargin">
			<form action="<?php echo $this->config->base_url('setting/register');?>" method="post">
				<input type="hidden" name="formhash" value="<?php echo formhash();?>">
				<table class="opt">
					<tr>
						<th colspan="2"><?php echo $this->lang->line('setting_register_doublee');?>:</th>
					</tr>
					<tr>
						<td>
							<input type="radio" id="yes" class="radio" name="doublee" value="1" <?php echo isset($doublee[1])?$doublee[1]:''?> /><label for="yes"><?php echo $this->lang->line('yes');?></label>
							<input type="radio" id="no" class="radio" name="doublee" value="0" <?php echo isset($doublee[0])?$doublee[0]:''?> /><label for="no"><?php echo $this->lang->line('no');?></label>
						</td>
					</tr>
					<tr>
						<th colspan="2"><?php echo $this->lang->line('setting_register_accessemail');?>:</th>
					</tr>
					<tr>
						<td><textarea class="area" name="accessemail"><?php echo $accessemail;?></textarea></td>
						<td valign="top"><?php echo $this->lang->line('setting_register_accessemail_comment');?></td>
					</tr>
					<tr>
						<th colspan="2"><?php echo $this->lang->line('setting_register_censoremail');?>:</th>
					</tr>
					<tr>
						<td><textarea class="area" name="censoremail"><?php echo $censoremail;?></textarea></td>
						<td valign="top"><?php echo $this->lang->line('setting_register_censoremail_comment');?></td>
					</tr>
					<tr>
						<th colspan="2"><?php echo $this->lang->line('setting_forbidden_username');?>:</th>
					</tr>
					<tr>
						<td><textarea class="area" name="censorusername"><?php echo $censorusername;?></textarea></td>
						<td valign="top"><?php echo $this->lang->line('setting_ceonsor_comment');?></td>
					</tr>
				</table>
				<div class="opt"><input type="submit" name="submit" value=" <?php echo $this->lang->line('submit');?> " class="btn" tabindex="3" /></div>
			</form>
		</div>
	<?php else:?>
		<div class="mainbox nomargin">
			<form action="<?php echo $this->config->base_url('setting/mail');?>" method="post">
				<input type="hidden" name="formhash" value="<?php echo formhash();?>">
				<table class="opt">
					<tr>
						<th colspan="2"><?php echo $this->lang->line('settings_mail_settings_emailfrom');?>:</th>
					</tr>
					<tr>
						<td><input name="maildefault" value="<?php echo $maildefault;?>" type="text"></td>
						<td><?php echo $this->lang->line('settings_mail_settings_emailfrom_comment');?></td>
					<tr>
						<th colspan="2"><?php echo $this->lang->line('settings_mail_settings_send');?>:</th>
					</tr>
					<tr>
						<td colspan="2">
							<label><input class="radio" name="mailsend" value="1"<?php if($mailsend == 1):?> checked="checked"<?php endif;?> onclick="$('hidden1').style.display = 'none';$('hidden2').style.display = 'none';" type="radio"> <?php echo $this->lang->line('settings_mail_settings_send_1');?></label><br />
							<label><input class="radio" name="mailsend" value="2"<?php if($mailsend == 2):?> checked="checked"<?php endif;?> onclick="$('hidden1').style.display = '';$('hidden2').style.display = '';" type="radio"> <?php echo $this->lang->line('settings_mail_settings_send_2');?></label><br />
							<label><input class="radio" name="mailsend" value="3"<?php if($mailsend == 3):?> checked="checked"<?php endif;?> onclick="$('hidden1').style.display = '';$('hidden2').style.display = 'none';" type="radio"> <?php echo $this->lang->line('settings_mail_settings_send_3');?></label>
						</td>
					</tr>
					<tbody id="hidden1"<?php if($mailsend == 1):?> style="display:none"<?php endif;?>>
					<tr>
						<td colspan="2"><?php echo $this->lang->line('settings_mail_settings_server');?>:</td>
					</tr>
					<tr>
						<td>
							<input name="mailserver" value="<?php echo $mailserver;?>" class="txt" type="text">
						</td>
						<td valign="top"><?php echo $this->lang->line('settings_mail_settings_server_comment');?></td>
					</tr>
					<tr>
						<td colspan="2"><?php echo $this->lang->line('settings_mail_settings_port');?>:</td>
					</tr>
					<tr>
						<td>
							<input name="mailport" value="<?php echo $mailport;?>" type="text">
						</td>
						<td valign="top"><?php echo $this->lang->line('settings_mail_settings_port_comment');?></td>
					</tr>
					</tbody>
					<tbody id="hidden2"<?php if($mailsend == 1 || $mailsend == 3):?> style="display:none"<?php endif;?>>
					<tr>
						<td colspan="2"><?php echo $this->lang->line('settings_mail_settings_auth');?>:</td>
					</tr>
					<tr>
						<td>
							<label><input type="radio" class="radio" name="mailauth"<?php if($mailsend == 1):?> checked="checked"<?php endif;?> value="1" /><?php echo $this->lang->line('yes');?></label>
							<label><input type="radio" class="radio" name="mailauth"<?php if($mailsend == 0):?> checked="checked"<?php endif;?> value="0" /><?php echo $this->lang->line('no');?></label>
						</td>
						<td valign="top"><?php echo $this->lang->line('settings_mail_settings_auth_comment');?></td>
					</tr>
					<tr>
						<td colspan="2"><?php echo $this->lang->line('settings_mail_settings_from');?>:</td>
					</tr>
					<tr>
						<td>
							<input name="mailfrom" value="<?php echo $mailfrom;?>" class="txt" type="text">
						</td>
						<td valign="top"><?php echo $this->lang->line('settings_mail_settings_from_comment');?></td>
					</tr>
					<tr>
						<td colspan="2"><?php echo $this->lang->line('settings_mail_settings_username');?>:</td>
					</tr>
					<tr>
						<td>
							<input name="mailauth_username" value="<?php echo $mailauth_username;?>" type="text">
						</td>
						<td valign="top"></td>
					</tr>
					<tr>
						<td colspan="2"><?php echo $this->lang->line('settings_mail_settings_password');?>:</td>
					</tr>
					<tr>
						<td>
							<input name="mailauth_password" value="<?php echo $mailauth_password;?>" type="text">
						</td>
						<td valign="top"></td>
					</tr>
					</tbody>
					<tr>
						<th colspan="2"><?php echo $this->lang->line('settings_mail_settings_delimiter');?>:</th>
					</tr>
					<tr>
						<td>
							<label><input class="radio" name="maildelimiter"<?php if($maildelimiter == 1):?> checked="checked"<?php endif;?> value="1" type="radio"> <?php echo $this->lang->line('settings_mail_settings_delimiter_crlf');?></label><br />
							<label><input class="radio" name="maildelimiter"<?php if($maildelimiter == 0):?> checked="checked"<?php endif;?> value="0" type="radio"> <?php echo $this->lang->line('settings_mail_settings_delimiter_lf');?></label><br />
							<label><input class="radio" name="maildelimiter"<?php if($maildelimiter == 2):?> checked="checked"<?php endif;?> value="2" type="radio"> <?php echo $this->lang->line('settings_mail_settings_delimiter_cr');?></label>
						</td>
						<td>
							<?php echo $this->lang->line('settings_mail_settings_delimiter_comment');?>
						</td>
					</tr>
					<tr>
						<th colspan="2"><?php echo $this->lang->line('settings_mail_settings_includeuser');?>:</th>
					</tr>
					<tr>
						<td>
							<label><input type="radio" class="radio" name="mailusername"<?php if($mailusername == 1):?> checked="checked"<?php endif;?> value="1" /><?php echo $this->lang->line('yes');?></label>
							<label><input type="radio" class="radio" name="mailusername"<?php if($mailusername == 0):?> checked="checked"<?php endif;?> value="0" /><?php echo $this->lang->line('no');?></label>
						</td>
						<td valign="top"><?php echo $this->lang->line('settings_mail_settings_includeuser_comment');?></td>
					</tr>
					<tr>
						<th colspan="2"><?php echo $this->lang->line('settings_mail_settings_silent');?>:</th>
					</tr>
					<tr>
						<td>
							<label><input type="radio" class="radio" name="mailsilent"<?php if($mailsilent == 1):?> checked="checked"<?php endif;?> value="1" /><?php echo $this->lang->line('yes');?></label>
							<label><input type="radio" class="radio" name="mailsilent"<?php if($mailsilent == 0):?> checked="checked"<?php endif;?> value="0" /><?php echo $this->lang->line('no');?></label>
						</td>
						<td valign="top">&nbsp;</td>
					</tr>
				</table>
				<div class="opt"><input type="submit" name="submit" value=" <?php echo $this->lang->line('submit');?> " class="btn" tabindex="3" /></div>
			</form>
		</div>
	<?php endif;?>
</div>

<?php $this->load->view('footer');?>