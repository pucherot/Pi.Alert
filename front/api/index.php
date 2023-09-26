<?php
// Check API Key
// Print Api-Key for debugging
// echo $_POST['api-key'];
$config_file = "../../config/pialert.conf";
$config_file_lines = file($config_file);
$config_file_lines_bypass = array_values(preg_grep('/^PIALERT_APIKEY\s.*/', $config_file_lines));
if ($config_file_lines_bypass != False) {
	$apikey_line = explode("'", $config_file_lines_bypass[0]);
	$pia_apikey = trim($apikey_line[1]);
} else {echo "No API-Key is set\n";exit;}

// Exit if API-Key is unequal
if ($_POST['api-key'] != $pia_apikey) {echo "Wrong API-Key\n";exit;}

// When API is correct
// include db.php
require '../php/server/db.php';
// Overwrite variable from db.php because of current working dir
$DBFILE = '../../db/pialert.db';

// Set maximum execution time to 30 seconds
ini_set('max_execution_time', '30');

// Secure and verify query
if (isset($_REQUEST['mac'])) {
	$mac_address = str_replace('-', ':', strtolower($_REQUEST['mac']));
	if (filter_var($mac_address, FILTER_VALIDATE_MAC) === False) {echo 'Invalid MAC Address.';exit;}
}

// Open DB
OpenDB();

// Action functions
if (isset($_REQUEST['get']) && !empty($_REQUEST['get'])) {
	$action = $_REQUEST['get'];
	switch ($action) {
	case 'mac-status':getStatusofMAC($mac_address);
		break;
	case 'all-online':getAllOnline();
		break;
	case 'all-offline':getAllOffline();
		break;
	case 'system-status':getSystemStatus();
		break;
	case 'all-online-icmp':getAllOnline_ICMP();
		break;
	case 'all-offline-icmp':getAllOffline_ICMP();
		break;
	}
}

//example curl -k -X POST -F 'api-key=key' -F 'get=system-status' https://url/pialert/api/
function getSystemStatus() {

	# Detect Language
	foreach (glob("../../db/setting_language*") as $filename) {
		$pia_lang_selected = str_replace('setting_language_', '', basename($filename));
	}
	if (strlen($pia_lang_selected) == 0) {$pia_lang_selected = 'en_us';}

	$en_us = array("On", "Off");
	$de_de = array("An", "Aus");
	$es_es = array("En", "Off");

	# Check Scanning Status
	if (file_exists("../../db/setting_stoparpscan")) {$temp_api_online_devices['Scanning'] = $$pia_lang_selected[1];} else { $temp_api_online_devices['Scanning'] = $$pia_lang_selected[0];}

	global $db;
	$results = $db->query('SELECT * FROM Online_History WHERE Data_Source="main_scan" ORDER BY Scan_Date DESC LIMIT 1');
	while ($row = $results->fetchArray()) {
		$time_raw = explode(' ', $row['Scan_Date']);
		$temp_api_online_devices['Last_Scan'] = $time_raw[1];
		$temp_api_online_devices['All_Devices'] = $row['All_Devices'];
		$temp_api_online_devices['Offline_Devices'] = $row['Down_Devices'];
		$temp_api_online_devices['Online_Devices'] = $row['Online_Devices'];
		$temp_api_online_devices['Archived_Devices'] = $row['Archived_Devices'];
	}
	unset($results, $sql);
	$sql = 'SELECT * FROM Devices WHERE dev_NewDevice="1"';
	$results = $db->query($sql);
	$i = 0;
	while ($row = $results->fetchArray()) {
		$i++;
	}
	$temp_api_online_devices['New_Devices'] = $i;
	unset($results, $sql);
	$results = $db->query('SELECT * FROM Online_History WHERE Data_Source="icmp_scan" ORDER BY Scan_Date DESC LIMIT 1');
	while ($row = $results->fetchArray()) {
		$temp_api_online_devices['All_Devices_ICMP'] = $row['All_Devices'];
		$temp_api_online_devices['Offline_Devices_ICMP'] = $row['Down_Devices'];
		$temp_api_online_devices['Online_Devices_ICMP'] = $row['Online_Devices'];
	}
	unset($results, $sql);
	$results = $db->query('SELECT * FROM Online_History WHERE Data_Source="icmp_scan" ORDER BY Scan_Date DESC LIMIT 1');
	while ($row = $results->fetchArray()) {
		$temp_api_online_devices['All_Devices_ICMP'] = $row['All_Devices'];
		$temp_api_online_devices['Offline_Devices_ICMP'] = $row['Down_Devices'];
		$temp_api_online_devices['Online_Devices_ICMP'] = $row['Online_Devices'];
	}
	unset($results, $sql);
	$result = $db->query('SELECT COUNT(*) as count FROM Services');
	$row = $result->fetchArray(SQLITE3_ASSOC);
	if ($row) {
		$temp_api_online_devices['All_Services'] = $row['count'];
	}
	$api_online_devices = $temp_api_online_devices;
	$json = json_encode($api_online_devices);
	echo $json;
	echo "\n";
}

//example curl -k -X POST -F 'api-key=key' -F 'get=mac-status' -F 'mac=dc:a6:32:23:06:d3' https://url/pialert/api/
function getStatusofMAC($query_mac) {
	global $db;
	$sql = 'SELECT * FROM Devices WHERE dev_MAC="' . $query_mac . '"';
	$result = $db->query($sql);
	$row = $result->fetchArray(SQLITE3_ASSOC);
	$json = json_encode($row);
	echo $json;
	echo "\n";
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
	echo "\n";
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
	echo "\n";
}

//example curl -k -X POST -F 'api-key=key' -F 'get=all-online-icmp' https://url/pialert/api/
function getAllOnline_ICMP() {
	global $db;
	$sql = 'SELECT * FROM ICMP_Mon WHERE icmp_PresentLastScan="1"';
	$api_online_devices = array();
	$results = $db->query($sql);
	$i = 0;
	while ($row = $results->fetchArray()) {
		$temp_api_online_devices['icmp_ip'] = $row['icmp_ip'];
		$temp_api_online_devices['icmp_hostname'] = $row['icmp_hostname'];
		$temp_api_online_devices['icmp_avgrtt'] = $row['icmp_avgrtt'];
		$api_online_devices[$i] = $temp_api_online_devices;
		$i++;
	}
	$json = json_encode($api_online_devices);
	echo $json;
	echo "\n";
}

//example curl -k -X POST -F 'api-key=key' -F 'get=all-offline-icmp' https://url/pialert/api/
function getAllOffline_ICMP() {
	global $db;
	$sql = 'SELECT * FROM ICMP_Mon WHERE icmp_PresentLastScan="0"';
	$api_online_devices = array();
	$results = $db->query($sql);
	$i = 0;
	while ($row = $results->fetchArray()) {
		$temp_api_online_devices['icmp_ip'] = $row['icmp_ip'];
		$temp_api_online_devices['icmp_hostname'] = $row['icmp_hostname'];
		$api_online_devices[$i] = $temp_api_online_devices;
		$i++;
	}
	$json = json_encode($api_online_devices);
	echo $json;
	echo "\n";
}

// Close DB
CloseDB();
?>