# Pi.Alert API Usage
<!--- --------------------------------------------------------------------- --->
This is my first attempt at building an API, so if I've done basic things wrong, I'm happy to see improvements.

Depending on the system configuration, it may be necessary to specify the path "/pialert" (e.g. `http://192.168.0.10` or `http://192.168.0.10/pialert/`) in the URL in addition to the IP or host name.

For the API, I limited myself to basic things. There are only 4 queries possible at the moment (system-status, mac-status, all-online, 
all-offline). For a query we need the API key, which can be created via the frontend (maintenance page) or 
via the pialer-cli in the "/back" directory.
The API key must be transmitted with "post", at least that's how it's written on my part at the moment.

The following fields are returned with the API call "system-status".

```
"Scanning":"<String>",
"Last_Scan":"<String>",
"All_Devices":<Integer>,
"Offline_Devices":<Integer>,
"Online_Devices":<Integer>,
"Archived_Devices":<Integer>,
"New_Devices":<Integer>,
"All_Devices_ICMP":<Integer>,
"Offline_Devices_ICMP":<Integer>,
"Online_Devices_ICMP":<Integer>,
"All_Services":<Integer>
```

[Query with PHP](https://github.com/leiweibau/Pi.Alert/blob/main/docs/API-USAGE.md#example-of-a-query-with-php-system-status), 
[Query with curl](https://github.com/leiweibau/Pi.Alert/blob/main/docs/API-USAGE.md#example-of-a-query-with-the-commandline-tool-curl-system-status)

The following fields are returned with the API call "mac-status".

```
"dev_MAC":"<String>",
"dev_Name":"<String>",
"dev_Owner":"<String>",
"dev_DeviceType":"<String>",
"dev_Vendor":"<String>",
"dev_Favorite":<Integer>,
"dev_Group":"<String>",
"dev_Comments":"<String>",
"dev_FirstConnection":"<String>",
"dev_LastConnection":"<String>",
"dev_LastIP":"<String>",
"dev_StaticIP":<Integer>,
"dev_ScanCycle":<Integer>,
"dev_LogEvents":<Integer>,
"dev_AlertEvents":<Integer>,
"dev_AlertDeviceDown":<Integer>,
"dev_SkipRepeated":<Integer>,
"dev_LastNotification":"<String>",
"dev_PresentLastScan":<Integer>,
"dev_NewDevice":<Integer>,
"dev_Location":"<String>",
"dev_Archived":<Integer>,
"dev_Infrastructure":<Integer>,
"dev_Infrastructure_port":<Integer>,
"dev_Model":"<String>",
"dev_Serialnumber":"<String>",
"dev_ConnectionType":"<String>"
```
[Query with PHP](https://github.com/leiweibau/Pi.Alert/blob/main/docs/API-USAGE.md#example-of-a-query-with-php-mac-status), 
[Query with curl](https://github.com/leiweibau/Pi.Alert/blob/main/docs/API-USAGE.md#example-of-a-query-with-the-commandline-tool-curl-mac-status)

The following fields are returned with the API call "all-online" or "all-offline" for each device.

```
"dev_MAC":"<String>",
"dev_Name":"<String>",
"dev_Vendor":"<String>",
"dev_LastIP":"<String>",
"dev_Infrastructure":<Integer>,
"dev_Infrastructure_port":<Integer>
```

[Query with PHP](https://github.com/leiweibau/Pi.Alert/blob/main/docs/API-USAGE.md#example-of-a-query-with-php-all-online-or-all-offline), 
[Query with curl](https://github.com/leiweibau/Pi.Alert/blob/main/docs/API-USAGE.md#example-of-a-query-with-the-commandline-tool-curl-all-online-or-all-offline)

The following fields are returned with the API call "all-online-icmp" for each device.

```
"icmp_ip":"<String>",
"icmp_hostname":"<String>",
"icmp_avgrtt":<Float>
```

[Query with PHP](https://github.com/leiweibau/Pi.Alert/blob/main/docs/API-USAGE.md#example-of-a-query-with-php-all-online-or-all-offline), 
[Query with curl](https://github.com/leiweibau/Pi.Alert/blob/main/docs/API-USAGE.md#example-of-a-query-with-the-commandline-tool-curl-all-online-or-all-offline)

The following fields are returned with the API call "all-offline-icmp" for each device.

```
"icmp_ip":"<String>",
"icmp_hostname":"<String>"
```

[Query with PHP](https://github.com/leiweibau/Pi.Alert/blob/main/docs/API-USAGE.md#example-of-a-query-with-php-all-online-or-all-offline), 
[Query with curl](https://github.com/leiweibau/Pi.Alert/blob/main/docs/API-USAGE.md#example-of-a-query-with-the-commandline-tool-curl-all-online-or-all-offline)


## Home Assistant Integration

The API can also be used to make information available in Home Assistant.

[Use API-Call for Home Assistant](https://github.com/leiweibau/Pi.Alert/blob/main/docs/API-USAGE.md#use-api-call-for-home-assistant)

<hr>

### Example of a query with PHP (system-status)

Prepare post fields
```php
$api_url = 'https://[URL]/api/'; //Pi.Alert URL
$api_key = 'YourApi-Key'; //api-key
$api_action = 'system-status';
```

Set post fields
```php
$post = ['api-key' => $api_key, 'get' => $api_action];
```

Init PHP curl
```php
$apicall = curl_init($api_url);
curl_setopt($apicall, CURLOPT_RETURNTRANSFER, true);
curl_setopt($apicall, CURLOPT_POSTFIELDS, $post);
curl_setopt($apicall, CURLOPT_SSL_VERIFYPEER, false);
```

Execute PHP curl
```php
$response = curl_exec($apicall);
```

Close the PHP curl connection
```php
curl_close($apicall);
```

Demo output
```php
print_r(json_decode($response));
```

### Example of a query with PHP (mac-status)

Prepare post fields
```php
$api_url = 'https://[URL]/api/'; //Pi.Alert URL
$api_key = 'YourApi-Key'; //api-key
$api_action = 'mac-status';
$api_macquery = '00:0d:93:89:15:90'; // single mac address
```

Set post fields
```php
$post = ['api-key' => $api_key, 'get' => $api_action,  'mac' => $api_macquery];
```

Init PHP curl
```php
$apicall = curl_init($api_url);
curl_setopt($apicall, CURLOPT_RETURNTRANSFER, true);
curl_setopt($apicall, CURLOPT_POSTFIELDS, $post);
curl_setopt($apicall, CURLOPT_SSL_VERIFYPEER, false);
```

Execute PHP curl
```php
$response = curl_exec($apicall);
```

Close the PHP curl connection
```php
curl_close($apicall);
```

Demo output
```php
print_r(json_decode($response));
```

### Query with PHP (all-online, all-offline, all-online-icmp, all-offline-icmp)

Prepare post fields
```php
$api_url = 'https://[URL]/api/'; //Pi.Alert URL
$api_key = 'YourApi-Key'; //api-key
$api_action = 'all-online'; //all-online, all-offline
```

Set post fields
```php
$post = ['api-key' => $api_key, 'get' => $api_action];
```

Init PHP curl
```php
$apicall = curl_init($api_url);
curl_setopt($apicall, CURLOPT_RETURNTRANSFER, true);
curl_setopt($apicall, CURLOPT_POSTFIELDS, $post);
curl_setopt($apicall, CURLOPT_SSL_VERIFYPEER, false);
```

Execute PHP curl
```php
$response = curl_exec($apicall);
```

Close the PHP curl connection
```php
curl_close($apicall);
```

Demo output
```php
print_r(json_decode($response));
```
<hr>

### Example of a query with the commandline tool curl (system-status)
```bash
curl -k -X POST -F 'api-key=yourApi-Key' -F 'get=system-status' https://[URL]/api/
```

### Example of a query with the commandline tool curl (mac-status)
```bash
curl -k -X POST -F 'api-key=yourApi-Key' -F 'get=mac-status' -F 'mac=00:11:22:aa:bb:cc' https://[URL]/api/
```

### Example of a query with the commandline tool curl (all-online or all-offline)

```bash
curl -k -X POST -F 'api-key=yourApi-Key' -F 'get=all-offline' https://[URL]/api/
```
<hr>

### Use API-Call for Home Assistant

For possibly better integrations in Home Assistant a pull request is welcome. First, the sensors must be added manually to the "configuration.yaml" file. If you don't use HTTPS, you have to replace it with HTTP in the following code.

For actual versions of Home Assistant
```yaml
command_line:
  - sensor:
      name: "PiAlert - Last Scan"
      command: curl -k -X POST -F 'api-key=[APIKEY]' -F 'get=system-status' https://[URL]/pialert/api/
      scan_interval: 200
      unique_id: pialert.status.lastscan
      value_template: "{{ value_json.Last_Scan }}"
  - sensor:
      name: "PiAlert - All Devices"
      command: curl -k -X POST -F 'api-key=[APIKEY]' -F 'get=system-status' https://[URL]/pialert/api/
      scan_interval: 200
      unique_id: pialert.status.alldevices
      unit_of_measurement: ""
      value_template: "{{ value_json.All_Devices }}"
  - sensor:
      name: "PiAlert - Online Devices"
      command: curl -k -X POST -F 'api-key=[APIKEY]' -F 'get=system-status' https://[URL]/pialert/api/
      scan_interval: 200
      unique_id: pialert.status.onlinedevices
      unit_of_measurement: ""
      value_template: "{{ value_json.Online_Devices }}"
  - sensor:
      name: "PiAlert - Offline Devices"
      command: curl -k -X POST -F 'api-key=[APIKEY]' -F 'get=system-status' https://[URL]/pialert/api/
      scan_interval: 200
      unique_id: pialert.status.offlinedevices
      unit_of_measurement: ""
      value_template: "{{ value_json.Offline_Devices }}"
  - sensor:
      name: "PiAlert - Archived Devices"
      command: curl -k -X POST -F 'api-key=[APIKEY]' -F 'get=system-status' https://[URL]/pialert/api/
      scan_interval: 200
      unique_id: pialert.status.archiveddevices
      unit_of_measurement: ""
      value_template: "{{ value_json.Archived_Devices }}"
  - sensor:
      name: "PiAlert - New Devices"
      command: curl -k -X POST -F 'api-key=[APIKEY]' -F 'get=system-status' https://[URL]/pialert/api/
      scan_interval: 200
      unique_id: pialert.status.newdevices
      unit_of_measurement: ""
      value_template: "{{ value_json.New_Devices }}"
  - sensor:
      name: "PiAlert - Scanning"
      command: curl -k -X POST -F 'api-key=[APIKEY]' -F 'get=system-status' https://[URL]/pialert/api/
      scan_interval: 120
      unique_id: pialert.status.scanning
      value_template: "{{ value_json.Scanning }}"
```
For older versions of Home Assistant
```yaml
sensor:
  - platform: command_line
    name: "PiAlert - Last Scan"
    command: curl -k -X POST -F 'api-key=yourApi-Key' -F 'get=system-status' https://[URL]/api/
    scan_interval: 200
    unique_id: pialert.status.lastscan
    value_template: '{{ value_json.Last_Scan }}'

  - platform: command_line
    name: "PiAlert - All Devices"
    command: curl -k -X POST -F 'api-key=yourApi-Key' -F 'get=system-status' https://[URL]/api/
    scan_interval: 200
    unique_id: pialert.status.alldevices
    unit_of_measurement: ""
    value_template: '{{ value_json.All_Devices }}'

  - platform: command_line
    name: "PiAlert - Online Devices"
    command: curl -k -X POST -F 'api-key=yourApi-Key' -F 'get=system-status' https://[URL]/api/
    scan_interval: 200
    unique_id: pialert.status.onlinedevices
    unit_of_measurement: ""
    value_template: '{{ value_json.Online_Devices }}'

  - platform: command_line
    name: "PiAlert - Offline Devices"
    command: curl -k -X POST -F 'api-key=yourApi-Key' -F 'get=system-status' https://[URL]/api/
    scan_interval: 200
    unique_id: pialert.status.offlinedevices
    unit_of_measurement: ""
    value_template: '{{ value_json.Offline_Devices }}'

  - platform: command_line
    name: "PiAlert - Archived Devices"
    command: curl -k -X POST -F 'api-key=yourApi-Key' -F 'get=system-status' https://[URL]/api/
    scan_interval: 200
    unique_id: pialert.status.archiveddevices
    unit_of_measurement: ""
    value_template: '{{ value_json.Archived_Devices }}'

  - platform: command_line
    name: "PiAlert - New Devices"
    command: curl -k -X POST -F 'api-key=yourApi-Key' -F 'get=system-status' https://[URL]/api/
    scan_interval: 200
    unique_id: pialert.status.newdevices
    unit_of_measurement: ""
    value_template: '{{ value_json.New_Devices }}'

  - platform: command_line
    name: "PiAlert - Scanning"
    command: curl -k -X POST -F 'api-key=yourApi-Key' -F 'get=system-status' https://[URL]/api/
    scan_interval: 120
    unique_id: pialert.status.scanning
    value_template: '{{ value_json.Scanning }}'
```
Restart Home Assistant after the change. Then open the developer tools in Home Assistant and switch to the States tab. Here you should now find the PiAlert sensors. Now you can create a new card on the dashboard and add the individual sensors as you wish. For illustration here is a picture of my Pi.Alert Card (It is configured in german for me, but it should be enough for understanding)

![pialert_card.png][pialert_card] 


[Back](https://github.com/leiweibau/Pi.Alert#api)

[pialert_card]:    /docs/img/pialert_card.png       "pialert_card.png"
