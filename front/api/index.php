<?php
// Check API Key
// Print Api-Key for debugging
//echo $_POST['api-key'];
$config_file = "../../config/pialert.conf";
$config_file_lines = file($config_file);
$config_file_lines_bypass = array_values(preg_grep('/^PIALERT_APIKEY\s.*/', $config_file_lines));
if ($config_file_lines_bypass != False) {
    $apikey_line = explode("'", $config_file_lines_bypass[0]);
    $pia_apikey = trim($apikey_line[1]);
} else { echo "No API-Key is set\n"; exit;}

// Exit if API-Key is unequal
if ($_POST['api-key'] != $pia_apikey) { echo "Wrong API-Key\n"; exit;}


// When API is correct
// include db.php
require '../php/server/db.php';
// Overwrite variable from db.php because of current working dir
$DBFILE = '../../db/pialert.db';

// Set maximum execution time to 30 seconds
ini_set ('max_execution_time','30');

// Secure and verify query
$mac_address = str_replace('-', ':', strtolower($_REQUEST['mac']));
if (filter_var($mac_address, FILTER_VALIDATE_MAC) === False) {echo 'Invalid MAC Address.'; exit;}
//echo "\n";

// Open DB
OpenDB();


// Action functions
if (isset ($_REQUEST['get']) && !empty ($_REQUEST['get'])) {
    $action = $_REQUEST['get'];
    switch ($action) {
      case 'mac-status':           getStatusofMAC($mac_address);            break;
      case 'all-online':           getAllOnline();                          break;
      case 'all-offline':          getAllOffline();                         break;
    }
}


//example curl -k -X POST -F 'api-key=key' -F 'get=mac-status' -F 'mac=dc:a6:32:23:06:d3' https://url/pialert/api/
function getStatusofMAC($query_mac) {
	global $db;
    $sql = 'SELECT * FROM Devices WHERE dev_MAC="'. $query_mac .'"';
	$result = $db->query($sql);
	$row = $result -> fetchArray (SQLITE3_ASSOC);
    $json = json_encode($row);
    echo $json;
}

//example curl -k -X POST -F 'api-key=key' -F 'get=all-online' https://url/pialert/api/
function getAllOnline() {
	global $db;
    $sql = 'SELECT * FROM Devices WHERE dev_PresentLastScan="1"';
    $api_online_devices = array();
    $results = $db->query($sql);
    $i = 0;
    while ($row = $results->fetchArray()) {
        $temp_api_online_devices['dev_MAC'] = $row['dev_MAC'];
        $temp_api_online_devices['dev_Name'] = $row['dev_Name'];
        $temp_api_online_devices['dev_Vendor'] = $row['dev_Vendor'];
        $temp_api_online_devices['dev_LastIP'] = $row['dev_LastIP'];
        $temp_api_online_devices['dev_Infrastructure'] = $row['dev_Infrastructure'];
        $temp_api_online_devices['dev_Infrastructure_port'] = $row['dev_Infrastructure_port'];
        $api_online_devices[$i] = $temp_api_online_devices;
        $i++;
    }
    $json = json_encode($api_online_devices);
    echo $json;
}

//example curl -k -X POST -F 'api-key=key' -F 'get=all-offline' https://url/pialert/api/
function getAllOffline() {
    global $db;
    $sql = 'SELECT * FROM Devices WHERE dev_PresentLastScan="0"';
    $api_online_devices = array();
    $results = $db->query($sql);
    $i = 0;
    while ($row = $results->fetchArray()) {
        $temp_api_online_devices['dev_MAC'] = $row['dev_MAC'];
        $temp_api_online_devices['dev_Name'] = $row['dev_Name'];
        $temp_api_online_devices['dev_Vendor'] = $row['dev_Vendor'];
        $temp_api_online_devices['dev_LastIP'] = $row['dev_LastIP'];
        $temp_api_online_devices['dev_Infrastructure'] = $row['dev_Infrastructure'];
        $temp_api_online_devices['dev_Infrastructure_port'] = $row['dev_Infrastructure_port'];
        $api_online_devices[$i] = $temp_api_online_devices;
        $i++;
    }
    $json = json_encode($api_online_devices);
    echo $json;
}

?>