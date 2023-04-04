## pialert-cli

To get an overview of the available commands, you have to enter "./pialert-cli help" in the directory "~/pialert/back".
The current commands are:

| command | explanation |
| ------- | ----------- |
| set_login | <ul><li>Sets the parameter PIALERT_WEB_PROTECTION in the config file to TRUE</li><li>If the parameter is not present, it will be created. Additionally the default password '123456' is set.</li></ul> |
| unset_login | <ul><li>Sets the parameter PIALERT_WEB_PROTECTION in the config file to FALSE</li><li>If the parameter is not present, it will be created. Additionally the default password '123456' is set.</li></ul> |
| set_password &lt;password&gt; | <ul><li>Sets the new password as a hashed value.</li><li>If the PIALERT_WEB_PROTECTION parameter does not exist yet, it will be created and set to TRUE (login enabled).</li></ul> |
| set_autopassword | <ul><li>Sets a new random password as a hashed value and show it plaintext in the console.</li><li>If the PIALERT_WEB_PROTECTION parameter does not exist yet, it will be created and set to TRUE (login enabled).</li></ul> |
| disable_scan &lt;MIN&gt; | <ul><li>Stops all active scans</li><li>Prevents new scans from starting.</li><li>You can set a Timeout in minutes. If no timeout is set, Pi.Alert restarts itself with the next scan after 10min.</li></ul> |
| enable_scan | <ul><li>Allows the start of new scans again.</li></ul> |
| enable_service_mon | <ul><li>Enable Web Service Monitoring.</li><li>If the SCAN_WEBSERVICES parameter does not exist yet, it will be created and set to TRUE.</li></ul> |
| disable_service_mon | <ul><li>Disable Web Service Monitoring.</li><li>If the SCAN_WEBSERVICES parameter does not exist yet, it will be created and set to FALSE.</li></ul> |
| update_db | <ul><li>The script tries to make the database compatible for this fork.</li></ul> |
| set_apikey | <ul><li>With the API key it is possible to make queries to the database without using the web page. If an API key already exists, it will be replaced.</li></ul> |
| reporting_test | <ul><li>Test reporting for all activated services.</li></ul> |
| rewrite_config | <ul><li>A new decluttered configuration file (pialert-rewritten.conf) will be created.</li></ul> |

[Back](https://github.com/leiweibau/Pi.Alert#back)
