## pialert-cli

To get an overview of the available commands, you have to enter "./pialert-cli help" in the directory "$HOME/pialert/back".
The current commands are:

| command | explanation |
| ------- | ----------- |
| disable_icmp_mon | <ul><li>Disable ICMP Monitoring.</li><li>If the ICMPSCAN_ACTIVE parameter does not exist yet, it will be created and set to FALSE.</li></ul> |
| disable_mainscan | <ul><li>Disable Main Scan (arp-scan).</li><li>If the ARPSCAN_ACTIVE parameter does not exist yet, it will be created and set to FALSE.</li></ul> |
| disable_scan &lt;MIN&gt; | <ul><li>Configured Pi.Alert scans are disabled</li><li>Prevents new scans from starting.</li><li>You can set a Timeout in minutes. If no timeout is set, Pi.Alert restarts itself with the next scan after 10min.</li></ul> |
| disable_service_mon | <ul><li>Disable Web Service Monitoring.</li><li>If the SCAN_WEBSERVICES parameter does not exist yet, it will be created and set to FALSE.</li></ul> |
| enable_icmp_mon | <ul><li>Enable ICMP Monitoring.</li><li>If the ICMPSCAN_ACTIVE parameter does not exist yet, it will be created and set to TRUE.</li></ul> |
| disable_mainscan | <ul><li>Enable Main Scan (arp-scan).</li><li>If the ARPSCAN_ACTIVE parameter does not exist yet, it will be created and set to TRUE.</li></ul> |
| enable_scan | <ul><li>Configured Pi.Alert scans are enabled</li></ul> |
| enable_service_mon | <ul><li>Enable Web Service Monitoring.</li><li>If the SCAN_WEBSERVICES parameter does not exist yet, it will be created and set to TRUE.</li></ul> |
| reporting_test | <ul><li>Test reporting for all activated services.</li></ul> |
| set_apikey | <ul><li>With the API key it is possible to make queries to the database without using the web page. If an API key already exists, it will be replaced.</li></ul> |
| set_autopassword | <ul><li>Sets a new random password as a hashed value and show it plaintext in the console.</li><li>If the PIALERT_WEB_PROTECTION parameter does not exist yet, it will be created and set to TRUE (login enabled).</li></ul> |
| set_login | <ul><li>Sets the parameter PIALERT_WEB_PROTECTION in the config file to TRUE</li><li>If the parameter is not present, it will be created. Additionally the default password '123456' is set.</li></ul> |
| set_password &lt;password&gt; | <ul><li>Sets the new password as a hashed value.</li><li>If the PIALERT_WEB_PROTECTION parameter does not exist yet, it will be created and set to TRUE (login enabled).</li></ul> |
| set_permissions | <ul><li>Repair file group permissions. Additional options to set user permissions are: <br>--lxc:        set "root" as user name <br>--custom:     set individual user name <br>--homedir:    get user name from homedir</li></ul> |
| set_sudoers | <ul><li>Create sudoer file for www-data and Pi.Alert user.</li></ul> |
| unset_login | <ul><li>Sets the parameter PIALERT_WEB_PROTECTION in the config file to FALSE</li><li>If the parameter is not present, it will be created. Additionally the default password '123456' is set.</li></ul> |
| unset_sudoers | <ul><li>Delete sudoer file for www-data and Pi.Alert user.</li></ul> |
| update_db | <ul><li>The script tries to make the database compatible for this fork.</li></ul> |

[Back](https://github.com/leiweibau/Pi.Alert#back)
