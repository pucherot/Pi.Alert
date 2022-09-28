## pialert-cli

To get an overview of the available commands, you have to enter "./pialert-cli help" in the directory "~/pialert/back".
The current commands are:

| command | explanation |
| ------- | ----------- |
| set_login | - Sets the parameter PIALERT_WEB_PROTECTION in the config file to TRUE <br> - If the parameter is not present, it will be created. Additionally the default password '123456' is set. |
| unset_login | - Sets the parameter PIALERT_WEB_PROTECTION in the config file to FALSE <br> - If the parameter is not present, it will be created. Additionally the default password '123456' is set. |
| set_password <password> | - Sets the new password as a hashed value. <br> - If the PIALERT_WEB_PROTECTION parameter does not exist yet, it will be created and set to 'True' (login enabled). |
| set_autopassword | - Sets a new random password as a hashed value and show it plaintext in the console. <br> - If the PIALERT_WEB_PROTECTION parameter does not exist yet, it will be created and set to 'True' (login enabled). |
| disable_scan | - Stops all active scans. <br> - Prevents new scans from starting.|
| enable_scan | - Allows the start of new scans again. |
| update_db | - The script tries to make the database compatible for this fork. |
| set_apikey | - With the API key it is possible to make queries to the database without using the web page. If an API key already exists, it will be replaced. |
| reporting_test | - Test reporting for all activated services. |