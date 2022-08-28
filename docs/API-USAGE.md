# Pi.Alert API Usage
<!--- --------------------------------------------------------------------- --->
This is my first attempt at building an API, so if I've done basic things wrong, I'm happy to see improvements.

For the API, I limited myself to basic things. There are only 3 queries possible at the moment (mac-status, all-online, 
all-offline). For each query we need a special API key, which can be created via the frontend (maintenance page) or 
via the pialer-cli in the "/back" directory.

The API key must be transmitted with "post", at least that's how it's written on my part at the moment.

## Example of a query with PHP (mac-status)

#### Prepare post fields
```
$api_url = 'https://[URL]/pialert/api/'; //Pi.Alert URL
$api_key = 'YourApi-Key'; //api-key
$api_action = 'mac-status';
$api_macquery = '00:0d:93:89:15:90'; // single mac address
```

#### Set post fields
```
$post = ['api-key' => $api_key, 'get' => $api_action,  'mac' => $api_macquery];
```

#### Init PHP curl
```
$apicall = curl_init($api_url);
curl_setopt($apicall, CURLOPT_RETURNTRANSFER, true);
curl_setopt($apicall, CURLOPT_POSTFIELDS, $post);
curl_setopt($apicall, CURLOPT_SSL_VERIFYPEER, false);
```

#### Execute PHP curl
```
$response = curl_exec($apicall);
```

#### Close the PHP curl connection
```
curl_close($apicall);
```

#### Demo output
```
print_r(json_decode($response));
```

## Example of a query with PHP (all-online or all-offline)

#### Prepare post fields
```
$api_url = 'https://[URL]/pialert/api/'; //Pi.Alert URL
$api_key = 'YourApi-Key'; //api-key
$api_action = 'all-online'; //all-online, all-offline
```

#### Set post fields
```
$post = ['api-key' => $api_key, 'get' => $api_action];
```

#### Init PHP curl
```
$apicall = curl_init($api_url);
curl_setopt($apicall, CURLOPT_RETURNTRANSFER, true);
curl_setopt($apicall, CURLOPT_POSTFIELDS, $post);
curl_setopt($apicall, CURLOPT_SSL_VERIFYPEER, false);
```

#### Execute PHP curl
```
$response = curl_exec($apicall);
```

#### Close the PHP curl connection
```
curl_close($apicall);
```

#### Demo output
```
print_r(json_decode($response));
```

## Example of a query with the commandline tool curl (mac-status)
```
curl -k -X POST -F 'api-key=yourApi-Key' -F 'get=mac-status' -F 'mac=00:11:22:aa:bb:cc' https://url/pialert/api/
```

## Example of a query with the commandline tool curl (all-online or all-offline)

```
curl -k -X POST -F 'api-key=yourApi-Key' -F 'get=all-offline' https://url/pialert/api/
```

