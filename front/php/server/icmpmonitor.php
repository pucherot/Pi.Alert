<?php
//------------------------------------------------------------------------------
//  Pi.Alert
//  Open Source Network Guard / WIFI & LAN intrusion detector
//
//  icmpmonitor.php - Front module. Server side. Manage Devices
//------------------------------------------------------------------------------
//  leiweibau  2023        https://github.com/leiweibau     GNU GPLv3
//------------------------------------------------------------------------------

session_start();

if ($_SESSION["login"] != 1) {
	header('Location: ../../index.php');
	exit;
}

foreach (glob("../../../db/setting_language*") as $filename) {
	$pia_lang_selected = str_replace('setting_language_', '', basename($filename));
}
if (strlen($pia_lang_selected) == 0) {$pia_lang_selected = 'en_us';}

require 'db.php';
require 'util.php';
require 'journal.php';
require '../templates/language/' . $pia_lang_selected . '.php';

// Action selector
// Set maximum execution time to 1 minute
ini_set('max_execution_time', '60');

// Open DB
OpenDB();

// Action functions
if (isset($_REQUEST['action']) && !empty($_REQUEST['action'])) {
	$action = $_REQUEST['action'];
	switch ($action) {
	case 'setICMPHostData':setICMPHostData();
		break;
	case 'deleteICMPHost':deleteICMPHost();
		break;
	case 'insertNewICMPHost':insertNewICMPHost();
		break;
	case 'EnableICMPMon':EnableICMPMon();
		break;
	case 'getDevicesList':getDevicesList();
		break;
	case 'getICMPHostTotals':getICMPHostTotals();
		break;
	case 'getEventsTotalsforICMP':getEventsTotalsforICMP();
		break;
	case 'BulkDeletion':BulkDeletion();
		break;
	}
}

//  Get List Totals
function getICMPHostTotals() {
	global $db;

	$query = "SELECT COUNT(*) AS rowCount FROM ICMP_Mon WHERE icmp_PresentLastScan=0 AND icmp_AlertDown=1";
	$alertDown_Count = $db->querySingle($query);
	$query = "SELECT COUNT(*) AS rowCount FROM ICMP_Mon WHERE icmp_PresentLastScan=1";
	$online_Count = $db->querySingle($query);
	$query = "SELECT COUNT(*) AS rowCount FROM ICMP_Mon WHERE icmp_Favorite=1";
	$favorite_Count = $db->querySingle($query);
	$query = "SELECT COUNT(*) AS rowCount FROM ICMP_Mon";
	$all_Count = $db->querySingle($query);

	$totals = array($all_Count, $alertDown_Count, $online_Count, $favorite_Count);
	echo (json_encode($totals));
}

//  Get List
function getDevicesList() {
	global $db;

	$sql = 'SELECT rowid,* FROM ICMP_Mon';
	$result = $db->query($sql);
	// arrays of rows
	$tableData = array();
	while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
		if ($row['icmp_hostname'] == '') {$row['icmp_hostname'] = $row['icmp_ip'];}
		$tableData['data'][] = array(
			$row['icmp_hostname'],
			$row['icmp_ip'],
			$row['icmp_Favorite'],
			$row['icmp_avgrtt'],
			$row['icmp_LastScan'],
			$row['icmp_PresentLastScan'],
			$row['icmp_AlertDown'],
			$row['rowid'], // Rowid (hidden)
		);
	}
	// Control no rows
	if (empty($tableData['data'])) {
		$tableData['data'] = '';
	}
	// Return json
	echo (json_encode($tableData));
}

//  Set ICMP Host Data
function setICMPHostData() {
	global $db;
	global $pia_lang;

	if ($_REQUEST['icmp_group'] == '--') {unset($_REQUEST['icmp_group']);}
	if ($_REQUEST['icmp_type'] == '--') {unset($_REQUEST['icmp_type']);}
	if ($_REQUEST['icmp_location'] == '--') {unset($_REQUEST['icmp_location']);}

	$sql = 'UPDATE ICMP_Mon SET
				icmp_hostname    = "' . quotes($_REQUEST['icmp_hostname']) . '",
                icmp_type        = "' . quotes($_REQUEST['icmp_type']) . '",
                icmp_group       = "' . quotes($_REQUEST['icmp_group']) . '",
                icmp_location    = "' . quotes($_REQUEST['icmp_location']) . '",
                icmp_owner       = "' . quotes($_REQUEST['icmp_owner']) . '",
                icmp_notes       = "' . quotes($_REQUEST['icmp_notes']) . '",
                icmp_AlertEvents = "' . quotes($_REQUEST['alertevents']) . '",
                icmp_AlertDown   = "' . quotes($_REQUEST['alertdown']) . '",
                icmp_Favorite    = "' . quotes($_REQUEST['favorit']) . '"
          WHERE icmp_ip="' . $_REQUEST['icmp_ip'] . '"';

	$result = $db->query($sql);

	if ($result == TRUE) {
		// Logging
		pialert_logging('a_031', $_SERVER['REMOTE_ADDR'], 'LogStr_0002', '', $_REQUEST['icmp_ip']);
		echo $pia_lang['BackICMP_mon_UpdICMP'];
		echo ("<meta http-equiv='refresh' content='2; URL=./icmpmonitor.php'>");
	} else {
		// Logging
		pialert_logging('a_031', $_SERVER['REMOTE_ADDR'], 'LogStr_0004', '', $_REQUEST['icmp_ip']);
		echo $pia_lang['BackICMP_mon_UpdICMPError'] . "\n\n$sql \n\n" . $db->lastErrorMsg();
	}
}

//  Delete Host
function deleteICMPHost() {
	global $db;
	global $pia_lang;

	$hostip = $_REQUEST['icmp_ip'];
	if (!filter_var($hostip, FILTER_FLAG_IPV4) && !filter_var($hostip, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
		echo $pia_lang['BackICMP_mon_DelICMPError'];
		return false;
	}
	$sql = 'DELETE FROM ICMP_Mon WHERE icmp_ip="' . $hostip . '"';
	$result = $db->query($sql);
	$sql = 'DELETE FROM ICMP_Mon_Events WHERE icmpeve_ip="' . $hostip . '"';
	$result = $db->query($sql);

	if ($result == TRUE) {
		// Logging
		pialert_logging('a_031', $_SERVER['REMOTE_ADDR'], 'LogStr_0003', '', $url);
		echo $pia_lang['BackICMP_mon_DelICMP'];
		echo ("<meta http-equiv='refresh' content='2; URL=./icmpmonitor.php'>");
	} else {
		// Logging
		pialert_logging('a_031', $_SERVER['REMOTE_ADDR'], 'LogStr_0005', '', $url);
		echo $pia_lang['BackICMP_mon_DelICMPError'] . "\n\n$sql \n\n" . $db->lastErrorMsg();
	}
}

//  Insert Service
function insertNewICMPHost() {
	global $db;
	global $pia_lang;

	$hostip = $_REQUEST['icmp_ip'];
	if ($_REQUEST['icmp_hostname'] == "") {$_REQUEST['icmp_hostname'] = $_REQUEST['icmp_ip'];}
	$check_timestamp = date("Y-m-d H:i:s");

	if (!filter_var($hostip, FILTER_FLAG_IPV4) && !filter_var($hostip, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
		echo $pia_lang['BackICMP_mon_InsICMPError'];
		return false;
	}

	$sql = 'INSERT INTO ICMP_Mon ("icmp_ip", "icmp_hostname", "icmp_LastScan", "icmp_PresentLastScan", "icmp_avgrtt", "icmp_AlertEvents", "icmp_AlertDown", "icmp_Favorite")
                         VALUES("' . $hostip . '", "' . $_REQUEST['icmp_hostname'] . '", "' . $check_timestamp . '", "0", "99999", "' . $_REQUEST['alertevents'] . '", "' . $_REQUEST['alertdown'] . '", "' . $_REQUEST['icmp_fav'] . '")';
	$result = $db->query($sql);

	if ($result == TRUE) {
		// Logging
		pialert_logging('a_031', $_SERVER['REMOTE_ADDR'], 'LogStr_0001', '', $hostip);
		echo $pia_lang['BackICMP_mon_InsICMP'];
		echo ("<meta http-equiv='refresh' content='2; URL=./icmpmonitor.php'>");
	} else {
		// Logging
		pialert_logging('a_031', $_SERVER['REMOTE_ADDR'], 'LogStr_0001', '', $hostip);
		echo $pia_lang['BackICMP_mon_InsICMPError'] . "\n\n$sql \n\n" . $db->lastErrorMsg();
	}
}

//  Toggle Web Service Monitoring
function EnableICMPMon() {
	global $pia_lang;

	if ($_SESSION['ICMPScan'] == True) {
		exec('../../../back/pialert-cli disable_icmp_mon', $output);
		echo $pia_lang['BackICMP_mon_disabled'];
		// Logging
		pialert_logging('a_031', $_SERVER['REMOTE_ADDR'], 'LogStr_0304', '', '');
		echo ("<meta http-equiv='refresh' content='2; URL=./maintenance.php?tab=1'>");
	} else {
		exec('../../../back/pialert-cli enable_icmp_mon', $output);
		echo $pia_lang['BackICMP_mon_enabled'];
		// Logging
		pialert_logging('a_031', $_SERVER['REMOTE_ADDR'], 'LogStr_0303', '', '');
		echo ("<meta http-equiv='refresh' content='2; URL=./maintenance.php?tab=1'>");
	}
}

//  Details
function getEventsTotalsforICMP() {
	global $db;

	// Request Parameters
	$hostip = $_REQUEST['hostip'];
	// SQL
	$SQL1 = 'SELECT Count(*)
           FROM ICMP_Mon_Events
           WHERE icmpeve_ip = "' . $hostip . '"';
	// All
	$result = $db->query($SQL1);
	$row = $result->fetchArray(SQLITE3_NUM);
	$eventsAll = $row[0];
	// Online
	$result = $db->query($SQL1 . ' AND icmpeve_Present = "1" ');
	$row = $result->fetchArray(SQLITE3_NUM);
	$eventsonline = $row[0];
	// Offline
	$result = $db->query($SQL1 . ' AND icmpeve_Present = "0" ');
	$row = $result->fetchArray(SQLITE3_NUM);
	$eventsoffline = $row[0];
	// Return json
	echo (json_encode(array($eventsAll, $eventsonline, $eventsoffline)));
}

//  Bulk Deletion
function BulkDeletion() {
	global $db;
	global $pia_lang;

	$hosts = str_replace("_", ".", '"' . implode('","', $_REQUEST['hosts']) . '"');
	$journal_hosts = str_replace("_", ".", implode(',', $_REQUEST['hosts']));
	echo $pia_lang['Device_bulkDel_back_hosts'] . ': ' . str_replace(",", ", ", $hosts) . '<br><br>';

	$sql = "SELECT COUNT(*) AS row_count FROM ICMP_Mon";
	$result = $db->query($sql);

	$row = $result->fetchArray();
	$rowCount_before = $row['row_count'];

	$sql = "DELETE FROM ICMP_Mon WHERE icmp_ip IN ($hosts)";
	$result = $db->query($sql);

	$sql = "SELECT COUNT(*) AS row_count FROM ICMP_Mon";
	$result = $db->query($sql);

	$row = $result->fetchArray();
	$rowCount_after = $row['row_count'];

	echo $pia_lang['Device_bulkDel_back_before'] . ': ' . $rowCount_before . '<br>' . $pia_lang['Device_bulkDel_back_after'] . ': ' . $rowCount_after;
	echo ("<meta http-equiv='refresh' content='2; URL=./icmpmonitor.php?mod=bulkedit'>");

	// Logging
	pialert_logging('a_021', $_SERVER['REMOTE_ADDR'], 'LogStr_0003', '', $journal_hosts);

}

?>
