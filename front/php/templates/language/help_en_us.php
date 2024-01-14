<?php
unset($help_lang);

$help_lang['Title'] = 'Help / FAQ';
$help_lang['Cat_General'] = 'General';
$help_lang['Cat_Detail'] = 'Detail View';
$help_lang['Cat_General_100_head'] = 'The clock on the top right and the times of the events/presence are not correct (time difference).';
$help_lang['Cat_General_100_text_a'] = 'On your PC the following time zone is set for the PHP environment:';
$help_lang['Cat_General_100_text_b'] = 'If this is not the timezone you are in, you should change the timezone in the PHP configuration file. You can find it in this directory:';
$help_lang['Cat_General_100_text_c'] = 'Search in this file for the entry "date.timezone", remove the leading ";" if necessary and enter the desired timezone. A list with the supported timezones can be found here (<a href="https://www.php.net/manual/en/timezones.php" target="blank">Link</a>)';
$help_lang['Cat_General_101_head'] = 'My network seems to slow down, streaming "freezes".';
$help_lang['Cat_General_101_text'] = 'It may well be that low-powered devices reach their performance limits with the way Pi.Alert detects new devices on the network. This is amplified even more,
									 if these devices communicate with the network via WLAN. Solutions here would be to switch to a wired connection if possible or, if the device is only to be used for a limited period of time, to use the arp scan.
									 pause the arp scan on the maintenance page.';
$help_lang['Cat_General_102_head'] = 'I get the message that the database is read only.';
$help_lang['Cat_General_102_text'] = 'Changes may currently be written to the database by the backend. Please try again after a short wait. If the behavior does not change, follow the instructions below.<br><br>
									 Check in the Pi.Alert directory if the database folder (db) has been assigned the correct permissions:<br>
      								 <span class="text-maroon help_faq_code">drwxrwxr-x  2 (your username) www-data</span><br>
      								 If the permission is not correct, you can set it again with the following commands in the terminal or the console:<br>
      								 <div class="help_faq_code" style="padding-left: 10px; margin-bottom: 10px;">
      								 sudo chgrp -R www-data ~/pialert/db<br>
      								 sudo chown [Username]:www-data ~/pialert/db/pialert.db<br>
        							 chmod -R 775 ~/pialert/db
      								 </div>
      								 Another option is to reset the necessary permissions in the directory <span class="text-maroon help_faq_code">~/pialert/back</span> using <span class="text-maroon help_faq_code">pialert-cli</span>. There are several options available to you.<br><br>
									 <span class="text-maroon help_faq_code">./pialert-cli set_permissions</span><br>
									 This command only resets the group permissions, leaving the file owner unchanged.<br><br>
									 <span class="text-maroon help_faq_code">./pialert-cli set_permissions --lxc</span><br>
									 This additional option is introduced for use within an LXC container. It changes the group as per the basic functionality and sets the user "root" as the owner. This option is not relevant outside of an LXC environment.<br><br>
									 <span class="text-maroon help_faq_code">./pialert-cli set_permissions --homedir</span><br>
									 This option should be the preferred one. Here, the username is determined based on the parent home directory of the Pi.Alert installation. This username becomes the owner of the files. The group is set according to the basic functionality.';
$help_lang['Cat_General_103_head'] = 'The login page does not appear, even after changing the password.';
$help_lang['Cat_General_103_text'] = 'In addition to the password, the configuration file must contain <span class="text-maroon help_faq_code">~/pialert/config/pialert.conf</span>
              								 also the parameter <span class="text-maroon help_faq_code">PIALERT_WEB_PROTECTION</span> must set to <span class="text-maroon help_faq_code">True</span>.';
$help_lang['Cat_General_104_head'] = 'Notes on migrating from pucherot to this fork.';
$help_lang['Cat_General_104_text'] = 'The database in this fork has been extended by some fields. To take over the database from the original Pi.Alert (pucherot), an update function is available via the "pialert-cli" in the directory <span class="text-maroon help_faq_code">~/pialert/back</span>.
											 The command is then <span class="text-maroon help_faq_code">./pialert-cli update_db</span>';
$help_lang['Cat_General_105_head'] = 'Explanations for "pialert-cli"';
$help_lang['Cat_General_105_text'] = 'The command line tool <span class="text-maroon help_faq_code">pialert-cli</span> is located in the directory <span class="text-maroon help_faq_code">~/pialert/back</span> and offers the possibility to make settings to Pi.Alert
                                     without web page or change to the configuration file. With the command <span class="text-maroon help_faq_code">./pialert-cli help</span> a list with the supported options can be called.
									 <table class="help_table_gen">
									    <tr><td class="help_table_gen_a">set_login</td>
									        <td class="help_table_gen_b">- Sets the parameter PIALERT_WEB_PROTECTION in the config file to TRUE<br>
									            - If the parameter is not present, it will be created. Additionally the default password "123456" is set.<br>&nbsp;</td></tr>
									    <tr><td class="help_table_gen_a">unset_login</td>
									        <td class="help_table_gen_b">- Sets the parameter PIALERT_WEB_PROTECTION in the config file to FALSE<br>
									            - If the parameter is not present, it will be created. Additionally the default password "123456" is set.<br>&nbsp;</td></tr>
									    <tr><td class="help_table_gen_a">set_password &lt;password&gt;</td>
									        <td class="help_table_gen_b">- Sets the new password as a hashed value.<br>
									            - If the PIALERT_WEB_PROTECTION parameter does not exist yet, it will be created and set to "TRUE" (login enabled)<br>&nbsp;</td></tr>
									    <tr><td class="help_table_gen_a">set_autopassword</td>
									        <td class="help_table_gen_b">- Sets a new random password as a hashed value and show it plaintext in the console.<br>
									            - If the PIALERT_WEB_PROTECTION parameter does not exist yet, it will be created and set to "TRUE" (login enabled)<br>&nbsp;</td></tr>
									    <tr><td class="help_table_gen_a">disable_scan &lt;MIN&gt;</td>
									        <td class="help_table_gen_b">- Stops all active scans.<br>
									            - Prevents new scans from starting.<br>
									            - You can set a Timeout in minutes. If no timeout is set, Pi.Alert restarts itself with the next scan after 10min.<br>&nbsp;</td></tr>
									    <tr><td class="help_table_gen_a">enable_scan</td>
									        <td class="help_table_gen_b">- Allows the start of new scans again.<br>&nbsp;</td></tr>
										<tr><td class="help_table_gen_a">disable_mainscan</td>
										    <td class="help_table_gen_b">- Disables the main scanning method for Pi.Alert (ARP scan)</td></tr>
										<tr><td class="help_table_gen_a">enable_mainscan</td>
										    <td class="help_table_gen_b">- Enables the main scanning method for Pi.Alert (ARP scan)</td></tr>
									    <tr><td class="help_table_gen_a">enable_service_mon</td>
									        <td class="help_table_gen_b">- Enable Web Service Monitoring<br>&nbsp;</td></tr>
									    <tr><td class="help_table_gen_a">disable_service_mon</td>
									        <td class="help_table_gen_b">- Disable Web Service Monitoring<br>&nbsp;</td></tr>
									    <tr><td class="help_table_gen_a">enable_icmp_mon</td>
									        <td class="help_table_gen_b">- Enable ICMP Monitoring (ping)<br>&nbsp;</td></tr>
									    <tr><td class="help_table_gen_a">disable_icmp_mon</td>
									        <td class="help_table_gen_b">- Disable ICMP Monitoring (ping)<br>&nbsp;</td></tr>
									    <tr><td class="help_table_gen_a">update_db</td>
									        <td class="help_table_gen_b">- The script tries to make the database compatible for this fork.<br>&nbsp;</td></tr>
									    <tr><td class="help_table_gen_a">set_apikey</td>
									        <td class="help_table_gen_b">- With the API key it is possible to make queries to the database without using the web page. If an API key already exists, it will be replaced.<br>&nbsp;</td></tr>
										<tr><td class="help_table_gen_a">set_permissions</td>
											<td class="help_table_gen_b">- Repairs the file permissions of the database for the group. If permissions need to be reset for the user as well, an additional option is required:<br>
																		<span class="text-maroon" style="display:inline-block;width:130px;">--lxc</span> sets "root" as the username<br>
																		<span class="text-maroon" style="display:inline-block;width:130px;">--custom</span> sets a custom username<br>
																		<span class="text-maroon" style="display:inline-block;width:130px;">--homedir</span> takes the username from the home directory</td></tr>
									    <tr><td class="help_table_gen_a">reporting_test</td>
									        <td class="help_table_gen_b">- Test reporting for all activated services.<br>&nbsp;</td></tr>
									    <tr><td class="help_table_gen_a">set_sudoers</td>
									        <td class="help_table_gen_b">- Create sudoer file for www-data and Pi.Alert user</td></tr>
									    <tr><td class="help_table_gen_a">unset_sudoers</td>
									        <td class="help_table_gen_b">- Delete sudoer file for www-data and Pi.Alert user</td></tr>
									</table>';
$help_lang['Cat_General_106_head'] = 'How can I perform an integrity check on the database?';
$help_lang['Cat_General_106_text'] = 'If you want to check the database currently in use, stop Pi.Alert for about 1 hour to prevent any writing access to the database during the check. Also, the web interface should not be open for any other write operations during the check.
									 Now, open the console in the directory <span class="text-maroon help_faq_code">~/pialert/db</span> and use the command <span class="text-maroon help_faq_code">ls</span> to list the contents of the directory. If the files
									 <span class="text-maroon help_faq_code">pialert.db-shm</span> and <span class="text-maroon help_faq_code">pialert.db-wal</span> appear in the list (with the same timestamp as the "pialert.db" file), it means that there are still database transactions open. In this case, just wait a moment, and to check, run the <span class="text-maroon help_faq_code">ls</span> command again.
									 <br><br>
									 Once these files have disappeared, the check can be performed. To do this, execute the following commands:<br>
									 <div class="help_faq_code" style="padding-left: 10px; margin-bottom: 10px;">
									    sqlite3 pialert.db "PRAGMA integrity_check"<br>
									    sqlite3 pialert.db "PRAGMA foreign_key_check"
									 </div><br>
									 In both cases, no errors should be reported. After the check, you can restart Pi.Alert.';
$help_lang['Cat_General_107_head'] = 'Explanations for the file "pialert.conf"';
$help_lang['Cat_General_107_text'] = 'The file <span class="text-maroon help_faq_code">pialert.conf</span> is located in the directory <span class="text-maroon help_faq_code">~/pialert/config</span>.
									 In this configuration file many functions of Pi.Alert can be set according to the personal wishes. Since the possibilities are various, I would like to give a
									 short explanation to the individual points.
									 <table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">General Settings</td></tr>
									    <tr><td class="help_table_gen_a">PIALERT_PATH</td>
									        <td class="help_table_gen_b">This variable is set during installation and should not be changed.</td></tr>
									    <tr><td class="help_table_gen_a">DB_PATH</td>
									        <td class="help_table_gen_b">This variable is set during installation and should not be changed.</td></tr>
									    <tr><td class="help_table_gen_a">LOG_PATH</td>
									        <td class="help_table_gen_b">This variable is set during installation and should not be changed.</td></tr>
									    <tr><td class="help_table_gen_a">PRINT_LOG</td>
									        <td class="help_table_gen_b">If this entry is set to <span class="text-maroon help_faq_code">True</span>, additional timestamps for the individual sub-functions are added to the scan log. By default this entry is set to <span class="text-maroon help_faq_code">False</span></td></tr>
									    <tr><td class="help_table_gen_a">VENDORS_DB</td>
									        <td class="help_table_gen_b">This variable is set during installation and should not be changed.</td></tr>
									    <tr><td class="help_table_gen_a">PIALERT_APIKEY</td>
									        <td class="help_table_gen_b">With the API key it is possible to make queries to the database without using the web page. The API key is a random string that can be set via the settings or via <span class="text-maroon help_faq_code">pialert-cli</span></td></tr>
									    <tr><td class="help_table_gen_a">PIALERT_WEB_PROTECTION</td>
									        <td class="help_table_gen_b">Enables or disables the password protection of the Pi.Alert web interface.</td></tr>
									    <tr><td class="help_table_gen_a">PIALERT_WEB_PASSWORD</td>
									        <td class="help_table_gen_b">This field contains the hashed password for the web interface. The password cannot be entered here in plain text, but must be set with <span class="text-maroon help_faq_code">pialert-cli</span></td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Other Modules</td></tr>
									    <tr><td class="help_table_gen_a">SCAN_WEBSERVICES</td>
									        <td class="help_table_gen_b">Here the function for monitoring web services can be switched on (<span class="text-maroon help_faq_code">True</span>) or off (<span class="text-maroon help_faq_code">False</span>)</td></tr>
									    <tr><td class="help_table_gen_a">ICMPSCAN_ACTIVE</td>
									        <td class="help_table_gen_b">ICMP Monitoring on/off</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Special Protocol Scanning</td></tr>
									    <tr><td class="help_table_gen_a">SCAN_ROGUE_DHCP</td>
									        <td class="help_table_gen_b">Activates the search for foreign, also called "rogue", DHCP servers. This function is used to detect whether there is a foreign DHCP server in the network that could take control of IP management.</td></tr>
									    <tr><td class="help_table_gen_a">DHCP_SERVER_ADDRESS</td>
									        <td class="help_table_gen_b">The IP of the known DHCP server is stored here.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Mail-Account Settings</td></tr>
									    <tr><td class="help_table_gen_a">SMTP_SERVER</td>
									        <td class="help_table_gen_b">Address of the e-mail server (e.g. smtp.gmail.com)</td></tr>
									    <tr><td class="help_table_gen_a">SMTP_PORT</td>
									        <td class="help_table_gen_b">The port of the SMTP server. The port may vary depending on the server configuration.</td></tr>
									    <tr><td class="help_table_gen_a">SMTP_USER</td>
									        <td class="help_table_gen_b">User name</td></tr>
									    <tr><td class="help_table_gen_a">SMTP_PASS</td>
									        <td class="help_table_gen_b">Password</td></tr>
									    <tr><td class="help_table_gen_a">SMTP_SKIP_TLS</td>
									        <td class="help_table_gen_b">If this entry is set to <span class="text-maroon help_faq_code">True</span>, transport encryption of the e-mail is enabled. If the server does not support this, the entry must be set to <span class="text-maroon help_faq_code">False</span>.</td></tr>
									    <tr><td class="help_table_gen_a">SMTP_SKIP_LOGIN</td>
									        <td class="help_table_gen_b">There are SMTP servers which do not require a login. In such a case, this value can be set to <span class="text-maroon help_faq_code">True</span>.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">WebGUI Reporting</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_WEBGUI</td>
									        <td class="help_table_gen_b">Enables/disables the notifications about changes in the network in the web interface.</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_WEBGUI_WEBMON</td>
									        <td class="help_table_gen_b">Enables/disables the notifications about changes in the monitored web services in the web interface.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr>
									        <td class="help_table_gen_section" colspan="2">Mail Reporting</td>
									    </tr>
									    <tr><td class="help_table_gen_a">REPORT_MAIL</td>
									        <td class="help_table_gen_b">Enables/disables the notifications about changes in the network via e-mail.</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_MAIL_WEBMON</td>
									        <td class="help_table_gen_b">Enables/disables the notification of changes in the monitored web services by e-mail.</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_FROM</td>
									        <td class="help_table_gen_b">Name or identifier of the sender.</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_TO</td>
									        <td class="help_table_gen_b">E-mail address to which the notification should be sent.</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_DEVICE_URL</td>
									        <td class="help_table_gen_b">URL of the Pi.Alert installation to create a clickable link in the e-mail to the detected device.</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_DASHBOARD_URL</td>
									        <td class="help_table_gen_b">URL of the Pi.Alert installation, to be able to create a clickable link in the e-mail.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr>
									        <td class="help_table_gen_section" colspan="2">Pushsafer</td>
									    </tr>
									    <tr><td class="help_table_gen_a">REPORT_PUSHSAFER</td>
									        <td class="help_table_gen_b">Enables/disables notifications about changes in the network via Pushsafer.</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_PUSHSAFER_WEBMON</td>
									        <td class="help_table_gen_b">Enables/disables notifications about changes in the monitored web services via Pushsafer.</td></tr>
									    <tr><td class="help_table_gen_a">PUSHSAFER_TOKEN</td>
									        <td class="help_table_gen_b">This is the private key that can be viewed on the pushsafer page.</td></tr>
									    <tr><td class="help_table_gen_a">PUSHSAFER_DEVICE</td>
									        <td class="help_table_gen_b">The device ID to which the message will be sent. &lsquo;<span class="text-maroon help_faq_code">a</span>&rsquo; means the message will be sent to all configuring devices and will consume a corresponding number of API calls.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Pushover</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_PUSHOVER</td>
									        <td class="help_table_gen_b">Enables/disables notifications about changes in the network via Pushover.</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_PUSHOVER_WEBMON</td>
									        <td class="help_table_gen_b">Enables/disables the notifications about changes in the monitored web services via Pushover.</td></tr>
									    <tr><td class="help_table_gen_a">PUSHOVER_TOKEN</td>
									        <td class="help_table_gen_b">Also called "APP TOKEN" or "API TOKEN". This token can be queried on the pushover page.</td></tr>
									    <tr><td class="help_table_gen_a">PUSHOVER_USER</td>
									        <td class="help_table_gen_b">Also called "USER KEY". This key is displayed right after login on the start page.</td></tr>
									</table>
									<table class="help_table_gen">
			    						<tr><td class="help_table_gen_section" colspan="2">NTFY</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_NTFY</td>
									        <td class="help_table_gen_b">Enables/disables the notifications about changes in the network via NTFY.</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_NTFY_WEBMON</td>
									        <td class="help_table_gen_b">Enables/disables the notifications about changes in the monitored web services via NTFY.</td></tr>
									    <tr><td class="help_table_gen_a">NTFY_HOST</td>
									        <td class="help_table_gen_b">    </td></tr>
									    <tr><td class="help_table_gen_a">NTFY_TOPIC</td>
									        <td class="help_table_gen_b">    </td></tr>
									    <tr><td class="help_table_gen_a">NTFY_USER</td>
									        <td class="help_table_gen_b">    </td></tr>
									    <tr><td class="help_table_gen_a">NTFY_PASSWORD</td>
									        <td class="help_table_gen_b">    </td></tr>
									    <tr><td class="help_table_gen_a">NTFY_PRIORITY</td>
									        <td class="help_table_gen_b">    </td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Shoutrrr</td></tr>
									    <tr><td class="help_table_gen_a">SHOUTRRR_BINARY</td>
									        <td class="help_table_gen_b">Here you have to configure which binary of shoutrrr has to be used. This depends on the hardware Pi.Alert was installed on.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Telegram via Shoutrrr</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_TELEGRAM</td>
									        <td class="help_table_gen_b">Enables/disables the notifications about changes in the network via Telegram</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_TELEGRAM_WEBMON</td>
									        <td class="help_table_gen_b">Enables/disables the notifications about changes in the monitored web services via Telegram</td></tr>
									    <tr><td class="help_table_gen_a">TELEGRAM_BOT_TOKEN_URL</td>
									        <td class="help_table_gen_b">    </td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">DynDNS and IP</td></tr>
									    <tr><td class="help_table_gen_a">QUERY_MYIP_SERVER</td>
									        <td class="help_table_gen_b">    </td></tr>
									    <tr><td class="help_table_gen_a">DDNS_ACTIVE</td>
									        <td class="help_table_gen_b">Enables/disables the configured DDNS service in Pi.Alert. DDNS, also known as DynDNS, allows you to update a domain name with a regularly changing IP address. This service is offered by several service providers.</td></tr>
									    <tr><td class="help_table_gen_a">DDNS_DOMAIN</td>
									        <td class="help_table_gen_b">    </td></tr>
									    <tr><td class="help_table_gen_a">DDNS_USER</td>
									        <td class="help_table_gen_b">    </td></tr>
									    <tr><td class="help_table_gen_a">DDNS_PASSWORD</td>
									        <td class="help_table_gen_b">    </td></tr>
									    <tr><td class="help_table_gen_a">DDNS_UPDATE_URL</td>
									        <td class="help_table_gen_b">    </td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Automatic Speedtest</td></tr>
									    <tr><td class="help_table_gen_a">SPEEDTEST_TASK_ACTIVE</td>
									        <td class="help_table_gen_b">Activate/deactivate the automatic speed test. This requires the installation of the Ookla speed test in the "Tools" tab of the "Internet" device. Follow the instructions during installation.</td></tr>
									    <tr><td class="help_table_gen_a">SPEEDTEST_TASK_HOUR</td>
									        <td class="help_table_gen_b">Full hour, or comma-separated hours, at which the speed test is to be started.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Arp-scan Options & Samples</td></tr>
									    <tr><td class="help_table_gen_a">MAC_IGNORE_LIST</td>
									        <td class="help_table_gen_b">
									            <span class="text-maroon help_faq_code">[&apos;MAC-Address 1&apos;, &apos;MAC-Address 2&apos;]</span><br>
									            This MAC address(es) (save with small letters) will be filtered out from the scan results.</td></tr>
									    <tr><td class="help_table_gen_a">SCAN_SUBNETS</td>
									        <td class="help_table_gen_b">
									        	&lsquo;<span class="text-maroon help_faq_code">--localnet</span>&rsquo;<br>
									        	Normally this option is already the correct settings. This setting is selected when Pi.Alert is installed on a device with a network card and no other networks are configured.<br><br>
									        	&lsquo;<span class="text-maroon help_faq_code">--localnet --interface=eth0</span>&rsquo;<br>
									        	This configuration is selected if Pi.Alert is installed on a system with at least 2 network cards and a configured network. However, the interface designation may differ and must be adapted to the conditions of the system.<br><br>
									        	<span class="text-maroon help_faq_code">[&apos;192.168.1.0/24 --interface=eth0&apos;,&apos;192.168.2.0/24 --interface=eth1&apos;]</span><br>
									        	The last configuration is necessary if several networks are to be monitored. For each network to be monitored, a corresponding network card must be configured. This is necessary because the "arp-scan" used is not routed, i.e. it only works within its own subnet. Each interface is entered here with the corresponding network. The interface designation must be adapted to the conditions of the system.
									        </td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">ICMP Monitoring Options</td></tr>
									    <tr><td class="help_table_gen_a">ICMP_ONLINE_TEST</td>
									        <td class="help_table_gen_b">Number of attempts to determine if a device is online (Default 1).</td></tr>
									    <tr><td class="help_table_gen_a">ICMP_GET_AVG_RTT</td>
									        <td class="help_table_gen_b">Number of "ping&apos;s" to calculate the average response time (Default 2).</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Pi-hole Configuration</td></tr>
									    <tr><td class="help_table_gen_a">PIHOLE_ACTIVE</td>
									        <td class="help_table_gen_b">This variable is set during installation.</td></tr>
									    <tr><td class="help_table_gen_a">PIHOLE_DB</td>
									        <td class="help_table_gen_b">This variable is set during installation and should not be changed.</td></tr>
									    <tr><td class="help_table_gen_a">DHCP_ACTIVE</td>
									        <td class="help_table_gen_b">This variable is set during installation.</td></tr>
									    <tr><td class="help_table_gen_a">DHCP_LEASES</td>
									        <td class="help_table_gen_b">This variable is set during installation and should not be changed.</td></tr>
									</table>
									<table class="help_table_gen">
			    						<tr><td class="help_table_gen_section" colspan="2">Fritzbox Configuration</td></tr>
									    <tr><td class="help_table_gen_a">FRITZBOX_ACTIVE</td>
									        <td class="help_table_gen_b">If a Fritzbox is used in the network, it can be used as a data source. This can be activated or deactivated at this point.</td></tr>
									    <tr><td class="help_table_gen_a">FRITZBOX_IP</td>
									        <td class="help_table_gen_b">IP address of the Fritzbox.</td></tr>
									    <tr><td class="help_table_gen_a">FRITZBOX_USER</td>
									        <td class="help_table_gen_b">User name<br>This assumes that the Fritzbox is configured for a login with username and password, instead of password only. A login with password only is not supported.</td></tr>
									    <tr><td class="help_table_gen_a">FRITZBOX_PASS</td>
									        <td class="help_table_gen_b">Password</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Mikrotik Configuration</td></tr>
									    <tr><td class="help_table_gen_a">MIKROTIK_ACTIVE</td>
									        <td class="help_table_gen_b">If a Mikrotik router is used in the network, it can be used as a data source. This can be enabled or disabled at this point.</td></tr>
									    <tr><td class="help_table_gen_a">MIKROTIK_IP</td>
									        <td class="help_table_gen_b">IP address of the Mikrotik router.</td></tr>
									    <tr><td class="help_table_gen_a">MIKROTIK_USER</td>
									        <td class="help_table_gen_b">Username</td></tr>
									    <tr><td class="help_table_gen_a">MIKROTIK_PASS</td>
									        <td class="help_table_gen_b">Password</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">UniFi Configuration</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_ACTIVE</td>
									        <td class="help_table_gen_b">If a UniFi system is used in the network, it can be used as a data source. This can be enabled or disabled at this point.</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_IP</td>
									        <td class="help_table_gen_b">IP address of the Unifi system.</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_API</td>
									        <td class="help_table_gen_b">Possible UNIFI APIs are v4, v5, unifiOS, UDMP-unifiOS</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_USER</td>
									        <td class="help_table_gen_b">Username</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_PASS</td>
									        <td class="help_table_gen_b">Password</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Maintenance Tasks Cron</td></tr>
									    <tr><td class="help_table_gen_a">DAYS_TO_KEEP_ONLINEHISTORY</td>
									        <td class="help_table_gen_b">Number of days for which the online history (activity graph) is to be stored in the database. One day generates 288 such records.</td></tr>
									    <tr><td class="help_table_gen_a">DAYS_TO_KEEP_EVENTS</td>
									        <td class="help_table_gen_b">Number of days for which the events of the individual devices are to be stored.</td></tr>
									</table>';
$help_lang['Cat_General_108_head'] = 'There is an update available. What should I do if I want to update Pi.Alert?';
$help_lang['Cat_General_108_text'] = '<ol>
										<li>Check in the status box on the settings page that no scan is currently running.</li>
										<li>Further down, in the security section, stop Pi.Alert for 15 minutes. This prevents the database from being modified during the update.</li>
										<li>Now switch to the terminal of the device where Pi.Alert is installed.</li>
										<li>Execute the command:<br>
											<input id="bashupdatecommand" readonly value="bash -c &quot;$(wget -qLO - https://github.com/leiweibau/Pi.Alert/raw/main/install/pialert_update.sh)&quot;" style="width:100%; overflow-x: scroll; border: none; background: transparent; margin: 0px; padding: 0px;"></li>
										<li>Follow the instructions.</li>
										<li>After a successful update, Pi.Alert should start automatically. Alternatively, you can manually restart it on the settings page.</li>
									</ol>';
$help_lang['Cat_Device_200_head'] = 'I have devices in my list that I do not know about. After deleting them, they always reappear.';
$help_lang['Cat_Device_200_text'] = 'If you use Pi-hole, please note that Pi.Alert retrieves information from Pi-hole. Pause Pi.Alert, go to the settings page in Pi-hole and
 									delete the DHCP lease if necessary. Then, also in Pi-hole, look under Tools -> Network to see if you can find the recurring hosts there.
 									If yes, delete them there as well. Now you can start Pi.Alert again. Now the device(s) should not show up anymore. Restarting the <span class="text-maroon help_faq_code">pihole-FTL</span> service may also fix the problem.
 									If such a device continues to appear repeatedly, the MAC address can be added to the ignore list <span class="text-maroon help_faq_code">MAC_IGNORE_LIST</span> in the <span class="text-maroon help_faq_code">pialert.conf</span>.';
$help_lang['Cat_Detail_300_head'] = 'What means ';
$help_lang['Cat_Detail_300_text_a'] = 'means a network device created from the network page.';
$help_lang['Cat_Detail_300_text_b'] = 'designates the port number where the currently edited device is connected to this network device.';
$help_lang['Cat_Detail_301_head_a'] = 'When is scanning now? At ';
$help_lang['Cat_Detail_301_head_b'] = ' says 1min but the graph shows 5min intervals.';
$help_lang['Cat_Detail_301_text'] = 'The time interval between the scans is defined by the "Cronjob", which is set to 5min by default. The designation "1min" refers to the expected duration of the scan.
									Depending on the network configuration, this time may vary. To edit the cronjob, you can use the following command in the terminal/console <span class="text-maroon help_faq_code">crontab -e</span>
									and change the interval.';
$help_lang['Cat_Detail_302_head_a'] = 'What means ';
$help_lang['Cat_Detail_302_head_b'] = 'and why can\'t I select that?';
$help_lang['Cat_Detail_302_text'] = 'Some modern devices generate random MAC addresses for privacy reasons, which can no longer be associated with any manufacturer and which change again with each new connection.
									Pi.Alert detects if it is such a random MAC address and activates this "field" automatically. To disable this behavior you have to look in your device how to disable
									MAC address randomization.';
$help_lang['Cat_Detail_303_head'] = 'What is Nmap and what is it for?';
$help_lang['Cat_Detail_303_text'] = 'Nmap is a network scanner with multiple capabilities.<br>
									When a new device appears in your list, you have the possibility to get more detailed information about the device via the Nmap scan.';
$help_lang['Cat_Presence_400_head'] = 'Devices are displayed with a yellow marker and the note "missing event".';
$help_lang['Cat_Presence_400_text'] = 'If this happens, you have the option to delete the events on the device in question (details view). Another possibility would be to switch on the device and wait until Pi.Alert detects the device as "online" with the next
									  scan and then simply turn the device off again. Now Pi.Alert should properly note the state of the device in the database with the next scan.';
$help_lang['Cat_Presence_401_head'] = 'A device is displayed as present although it is "Offline".';
$help_lang['Cat_Presence_401_text'] = 'If this happens, you have the possibility to delete the events for the device in question (details view). Another possibility would be to switch on the device and wait until Pi.Alert recognizes the device as "online" with the next scan
									  and then simply switch the device off again. Now Pi.Alert should properly note the state of the device in the database with the next scan.';
$help_lang['Cat_Network_600_head'] = 'What is this page for?';
$help_lang['Cat_Network_600_text'] = 'This page should offer you the possibility to map the assignment of your network devices. For this purpose, you can create one or more switches, WLANs, routers, etc., provide them with a port number if necessary and assign already detected
									 devices to them. This assignment is done in the detailed view of the device to be assigned. So it is possible for you to quickly determine to which port a host is connected and if it is online. It is possible to assign a device to multiple
									 ports (port bundling), as well as multiple devices to one port (virtual machines).';
$help_lang['Cat_Network_601_head'] = 'How does the network page work?';
$help_lang['Cat_Network_601_text'] = 'On the network side, for example, a switch is created. For this purpose, I already offer corresponding devices in the selection list. You continue to specify the type and the number of ports.<br><br>
									 On the detail view you have now, with each recognized device, the possibility to save this just created switch and the occupied port.<br><br>
									 Now the network page shows you the switch with its ports and the devices connected to it. For each device in the detail view, you have the option of assigning multiple ports to a switch, which you separate with a comma (e.g. for link aggregation). It is also possible to assign several devices to one port (e.g. a server with several virtual machines).<br><br>
									 You can also assign a switch to a router if you have created it on the network side. Normally, this switch will now be displayed on the router tab. What does not happen is that the router is displayed on the switch port. For this it is necessary and possible to save a manual port configuration. To do this, open the "Administration" and select the switch in the editing. After you have entered the type and the number of ports again, you have a selection list of possible devices in the lowest field. After the selection, only the MAC address is visible, followed by a ",". Now simply add the port of the router on the switch and save. It is also possible to enter multiple MAC addresses and ports. It is important to follow the syntax "MAC1,PortA;MAC2,PortB;MAC3,PortC".';
$help_lang['Cat_Network_602_head'] = 'A switch or router is shown to me without ports.';
$help_lang['Cat_Network_602_text'] = 'It is possible that the number of ports was not entered when the device was created on the network page. When editing the device on the network page, it is also necessary to enter an already entered number of ports again.<br>
									 If the number of ports is missing for a device that has already been created, the problem should be solved by editing the device and specifying the ports, the type and, if necessary, the manual port configuration.';
$help_lang['Cat_Service_700_head'] = 'What do the different colors in the colored bar mean?';
$help_lang['Cat_Service_700_text'] = 'There are 5 different color codes in total: <br>
									 <span style="background-color:lightgray;">&nbsp;&nbsp;&nbsp;</span> - no scan available yet<br>
									 <span class="bg-green">&nbsp;&nbsp;&nbsp;</span> - HTTP status code 2xx<br>
									 <span class="bg-yellow">&nbsp;&nbsp;&nbsp;</span> - HTTP status code 3xx-4xx<br>
									 <span class="bg-orange-custom">&nbsp;&nbsp;&nbsp;</span> - HTTP status code 5xx<br>
									 <span class="bg-red">&nbsp;&nbsp;&nbsp;</span> - offline';
$help_lang['Cat_Service_701_head'] = 'What are the HTTP status codes?';
// from json
$help_lang['Cat_Service_702_head'] = 'What changes are reported?';
$help_lang['Cat_Service_702_text'] = 'Detectable events include:<br>
  									<ul>
  										<li>Change in HTTP status code</li>
  										<li>Change in IP address</li>
  										<li>Server response time or lack of response</li>
  										<li>Changes to the SSL certificate</li>
  									</ul>
  								 Depending on the notification choice, either everything is reported, or only the absence of a server response. For changes to the certificate, a code is used, which is calculated as follows.
  								 In this case, the values 8 = Subject, 4 = Issuer, 2 = Valid from, and 1 = Valid to are assigned to the individual fields. So, a code of 13 means that there were changes in the Subject, Issuer, and Valid to fields.';
$help_lang['Cat_Service_703_head'] = 'General information about "Web Service Monitoring".';
$help_lang['Cat_Service_703_text'] = 'The monitoring is based exclusively on the responses of HTTP requests sent to the page. Depending on the state of the server, meaningful error patterns can be detected here. If the server does not respond at all, this is considered as "Down/Offline". These web server requests are performed every 10 min as part of the normal scan.';
$help_lang['Cat_ServiceDetails_750_head'] = 'I cannot edit all the fields.';
$help_lang['Cat_ServiceDetails_750_text'] = 'Not every field that is displayed on this page can be edited. Editable fields are:
											<ul>
												<li>' . $pia_lang['WebServices_label_Tags'] . '</li>
												<li>' . $pia_lang['WebServices_label_MAC'] . ' (possibly a device to which this web service is assigned)<br>
													A MAC address is expected here. If something else (e.g. "laptop") is entered here, "' . $pia_lang['WebServices_unknown_Device'] . ' (laptop)" appears in the overview..
													Services without this entry are listed under "' . $pia_lang['WebServices_BoxTitle_General'] . '".</li>
												<li>CheckBox: ' . $pia_lang['WebServices_Events_all'] . '</li>
												<li>CheckBox: ' . $pia_lang['WebServices_Events_down'] . '</li>
											</ul>';

?>
