<?php

// prepare post fields
$api_url = 'https://url/pialert/api/'; //Pi.Alert URL
$api_key = 'yourApi-Key'; //api-key
$api_action = 'all-online'; //mac-status, all-online, all-offline
$api_macquery = '00:11:22:aa:bb:cc'; // single mac address

// set post fields
if ($api_action == 'mac-status') {
    $post = ['api-key' => $api_key, 'get' => $api_action,  'mac' => $api_macquery];
} else {
    $post = ['api-key' => $api_key, 'get' => $api_action];
}

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// execute!
$response = curl_exec($ch);

// close the connection, release resources used
curl_close($ch);

// do anything you want with your response
//var_dump($response);

// json conversion
print_r(json_decode($response));

?>