<?php
session_start();

require 'db.php';
require 'journal.php';

// Open DB
OpenDB();

function crosscheckIP($query_ip) {
	global $db;
	$sql = 'SELECT * FROM Devices WHERE dev_LastIP="' . $query_ip . '"';
	$result = $db->query($sql);
	$row = $result->fetchArray(SQLITE3_ASSOC);
	$neededIP = $row['dev_LastIP'];
	if ($neededIP == "") {
		$sql = 'SELECT * FROM ICMP_Mon WHERE icmp_ip="' . $query_ip . '"';
		$result = $db->query($sql);
		$row = $result->fetchArray(SQLITE3_ASSOC);
		$neededIP = $row['icmp_ip'];
	}
	return $neededIP;
}

$DBFILE = '../../../db/pialert.db';

$PIA_HOST_IP = $_REQUEST['scan'];
$PIA_SCAN_MODE = $_REQUEST['mode'];

// Check if IP is valid
if (filter_var($PIA_HOST_IP, FILTER_VALIDATE_IP)) {

	// Check if IP is already known and in DB
	$db_crosscheck = crosscheckIP($PIA_HOST_IP);
	if (isset($db_crosscheck)) {
		if ($PIA_SCAN_MODE == 'fast') {
			exec('nmap -F ' . $PIA_HOST_IP, $output);
		} elseif ($PIA_SCAN_MODE == 'normal') {
			exec('nmap ' . $PIA_HOST_IP, $output);
		} elseif ($PIA_SCAN_MODE == 'detail') {
			exec('nmap -A -p -10000 ' . $PIA_HOST_IP, $output);
		}
		// Logging
		pialert_logging('a_002', $_SERVER['REMOTE_ADDR'], 'LogStr_0210', '', $PIA_SCAN_MODE . ' Scan: ' . $PIA_HOST_IP);
	} else {echo "Unknown IP";exit;}
} else {echo "Wrong parameter";exit;}
echo '<h4>Scan (' . $PIA_SCAN_MODE . ') Results of: ' . $PIA_HOST_IP . '</h4>';
echo '<pre style="border: none;">';

// Prepare short term memory
$PIA_SCAN_TIME = date('Y-m-d H:i:s');

unset($_SESSION['ScanShortMem_NMAP']);
$_SESSION['ScanShortMem_NMAP'] = 'Last Nmap Scan<br><br><span style="display:inline-block; width: 100px;">Scan Target:</span> ' . $PIA_HOST_IP . '<br><span style="display:inline-block; width: 100px;">Scan Mode:</span> ' . $PIA_SCAN_MODE . '<br><span style="display:inline-block; width: 100px;">Scan Time:</span> ' . $PIA_SCAN_TIME . '<br><br>Result:<br>';

foreach ($output as $line) {
	echo $line . "\n";
	// Safe last Scan result in Session (Short term memory)
	$_SESSION['ScanShortMem_NMAP'] = $_SESSION['ScanShortMem_NMAP'] . $line . '<br>';
}
echo '</pre>';

?>