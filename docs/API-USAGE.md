# Pi.Alert API Usage
<!--- --------------------------------------------------------------------- --->
This is my first attempt at building an API, so if I've done basic things wrong, I'm happy to see improvements.

For the API, I limited myself to basic things. There are only 4 queries possible at the moment (system-status, mac-status, all-online, 
all-offline). For a query we need the API key, which can be created via the frontend (maintenance page) or 
via the pialer-cli in the "/back" directory.
The API key must be transmitted with "post", at least that's how it's written on my part at the moment.

## Examples
[Query with PHP (system-status)](https://github.com/leiweibau/Pi.Alert/blob/main/docs/API-USAGE.md#example-of-a-query-with-php-system-status)<br>
[Query with PHP (mac-status)](https://github.com/leiweibau/Pi.Alert/blob/main/docs/API-USAGE.md#example-of-a-query-with-php-mac-status)<br>
[Query with PHP (all-online or all-offline)](https://github.com/leiweibau/Pi.Alert/blob/main/docs/API-USAGE.md#example-of-a-query-with-php-all-online-or-all-offline)<br><br>
[Query with curl (system-status)](https://github.com/leiweibau/Pi.Alert/blob/main/docs/API-USAGE.md#example-of-a-query-with-the-commandline-tool-curl-system-status)<br>
[Query with curl (mac-status)](https://github.com/leiweibau/Pi.Alert/blob/main/docs/API-USAGE.md#example-of-a-query-with-the-commandline-tool-curl-mac-status)<br>
[Query with curl (all-online or all-offline)](https://github.com/leiweibau/Pi.Alert/blob/main/docs/API-USAGE.md#example-of-a-query-with-the-commandline-tool-curl-all-online-or-all-offline)<br><br>
[Use API-Call for Home Assistant](https://github.com/leiweibau/Pi.Alert/blob/main/docs/API-USAGE.md#use-api-call-for-home-assistant)
<br><hr><br>

### Example of a query with PHP (system-status)

Prepare post fields
```
$api_url = 'https://[URL]/pialert/api/'; //Pi.Alert URL
$api_key = 'YourApi-Key'; //api-key
$api_action = 'system-status';
```

Set post fields
```
$post = ['api-key' => $api_key, 'get' => $api_action];
```

Init PHP curl
```
$apicall = curl_init($api_url);
curl_setopt($apicall, CURLOPT_RETURNTRANSFER, true);
curl_setopt($apicall, CURLOPT_POSTFIELDS, $post);
curl_setopt($apicall, CURLOPT_SSL_VERIFYPEER, false);
```

Execute PHP curl
```
$response = curl_exec($apicall);
```

Close the PHP curl connection
```
curl_close($apicall);
```

Demo output
```
print_r(json_decode($response));
```

### Example of a query with PHP (mac-status)

Prepare post fields
```
$api_url = 'https://[URL]/pialert/api/'; //Pi.Alert URL
$api_key = 'YourApi-Key'; //api-key
$api_action = 'mac-status';
$api_macquery = '00:0d:93:89:15:90'; // single mac address
```

Set post fields
```
$post = ['api-key' => $api_key, 'get' => $api_action,  'mac' => $api_macquery];
```

Init PHP curl
```
$apicall = curl_init($api_url);
curl_setopt($apicall, CURLOPT_RETURNTRANSFER, true);
curl_setopt($apicall, CURLOPT_POSTFIELDS, $post);
curl_setopt($apicall, CURLOPT_SSL_VERIFYPEER, false);
```

Execute PHP curl
```
$response = curl_exec($apicall);
```

Close the PHP curl connection
```
curl_close($apicall);
```

Demo output
```
print_r(json_decode($response));
```

### Example of a query with PHP (all-online or all-offline)

Prepare post fields
```
$api_url = 'https://[URL]/pialert/api/'; //Pi.Alert URL
$api_key = 'YourApi-Key'; //api-key
$api_action = 'all-online'; //all-online, all-offline
```

Set post fields
```
$post = ['api-key' => $api_key, 'get' => $api_action];
```

Init PHP curl
```
$apicall = curl_init($api_url);
curl_setopt($apicall, CURLOPT_RETURNTRANSFER, true);
curl_setopt($apicall, CURLOPT_POSTFIELDS, $post);
curl_setopt($apicall, CURLOPT_SSL_VERIFYPEER, false);
```

Execute PHP curl
```
$response = curl_exec($apicall);
```

Close the PHP curl connection
```
curl_close($apicall);
```

Demo output
```
print_r(json_decode($response));
```
<hr>

### Example of a query with the commandline tool curl (system-status)
```
curl -k -X POST -F 'api-key=yourApi-Key' -F 'get=system-status' https://[URL]/pialert/api/
```

### Example of a query with the commandline tool curl (mac-status)
```
curl -k -X POST -F 'api-key=yourApi-Key' -F 'get=mac-status' -F 'mac=00:11:22:aa:bb:cc' https://[URL]/pialert/api/
```

### Example of a query with the commandline tool curl (all-online or all-offline)

```
curl -k -X POST -F 'api-key=yourApi-Key' -F 'get=all-offline' https://[URL]/pialert/api/
```
<hr>

### Use API-Call for Home Assistant

For possibly better integrations in Home Assistant a pull request is welcome. First, the sensors must be added manually to the "configuration.yaml" file. If you don't use HTTPS, you have to replace it with HTTP in the following code.
```
sensor:
  - platform: command_line
    name: "PiAlert - Last Scan"
    command: curl -k -X POST -F 'api-key=yourApi-Key' -F 'get=system-status' https://[URL]/pialert/api/
    scan_interval: 200
    unique_id: pialert.status.lastscan
    value_template: '{{ value_json.Last_Scan }}'

  - platform: command_line
    name: "PiAlert - All Devices"
    command: curl -k -X POST -F 'api-key=yourApi-Key' -F 'get=system-status' https://[URL]/pialert/api/
    scan_interval: 200
    unique_id: pialert.status.alldevices
    unit_of_measurement: ""
    value_template: '{{ value_json.All_Devices }}'

  - platform: command_line
    name: "PiAlert - Online Devices"
    command: curl -k -X POST -F 'api-key=yourApi-Key' -F 'get=system-status' https://[URL]/pialert/api/
    scan_interval: 200
    unique_id: pialert.status.onlinedevices
    unit_of_measurement: ""
    value_template: '{{ value_json.Online_Devices }}'

  - platform: command_line
    name: "PiAlert - Offline Devices"
    command: curl -k -X POST -F 'api-key=yourApi-Key' -F 'get=system-status' https://[URL]/pialert/api/
    scan_interval: 200
    unique_id: pialert.status.offlinedevices
    unit_of_measurement: ""
    value_template: '{{ value_json.Offline_Devices }}'

  - platform: command_line
    name: "PiAlert - Archived Devices"
    command: curl -k -X POST -F 'api-key=yourApi-Key' -F 'get=system-status' https://[URL]/pialert/api/
    scan_interval: 200
    unique_id: pialert.status.archiveddevices
    unit_of_measurement: ""
    value_template: '{{ value_json.Archived_Devices }}'

  - platform: command_line
    name: "PiAlert - New Devices"
    command: curl -k -X POST -F 'api-key=yourApi-Key' -F 'get=system-status' https://[URL]/pialert/api/
    scan_interval: 200
    unique_id: pialert.status.newdevices
    unit_of_measurement: ""
    value_template: '{{ value_json.New_Devices }}'

  - platform: command_line
    name: "PiAlert - Scanning"
    command: curl -k -X POST -F 'api-key=yourApi-Key' -F 'get=system-status' https://[URL]/pialert/api/
    scan_interval: 120
    unique_id: pialert.status.scanning
    unit_of_measurement: ""
    value_template: '{{ value_json.Scanning }}'
```
Restart Home Assistant after the change. Then open the developer tools in Home Assistant and switch to the States tab. Here you should now find the PiAlert sensors. Now you can create a new card on the dashboard and add the individual sensors as you wish. For illustration here is a picture of my Pi.Alert Card (It is configured in german for me, but it should be enough for understanding)

![pialert_card.png][pialert_card] 


[Back](https://github.com/leiweibau/Pi.Alert#api)

[pialert_card]:    /docs/img/pialert_card.png       "pialert_card.png"
