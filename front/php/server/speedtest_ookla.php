<?php
//------------------------------------------------------------------------------
//  Pi.Alert
//  Open Source Network Guard / WIFI & LAN intrusion detector
//
//  speedtest_ookla.php - Front module. Server side. Manage Devices
//------------------------------------------------------------------------------
//  leiweibau  2023        https://github.com/leiweibau     GNU GPLv3
//------------------------------------------------------------------------------
session_start();

require 'db.php';
require 'journal.php';
$DBFILE = '../../../db/pialert.db';

// Open DB
OpenDB();

$speedtest_binary = '../../../back/speedtest/speedtest';
$speedtest_option = ' -p no -f json';

if (file_exists($speedtest_binary)) {
	exec('sudo ' . $speedtest_binary . $speedtest_option, $output);

	echo '<h4>Speedtest (Ookla) Results</h4>';
	echo '<pre style="border: none;">';

	$output_json = json_decode($output[0], true);

	$isp = $output_json['isp'];
	$server = $output_json['server']['name'] . ' (' . $output_json['server']['location'] . ') (' . $output_json['server']['host'] . ')';
	$ping = $output_json['ping']['latency'];
	$download_mbps = round($output_json['download']['bandwidth'] / 125000, 2);
	$upload_mbps = round($output_json['upload']['bandwidth'] / 125000, 2);

	$cli_output = "ISP: " . $isp . "\nServer: " . $server . "\n\nPing:     " . $ping . " ms\nDownload: " . $download_mbps . " Mbps\nUpload:   " . $upload_mbps . " Mbps";
	echo $cli_output;

	echo '</pre>';

	$cli_output = str_replace("\n", "<br>", $cli_output);
// Logging
	pialert_logging('a_002', $_SERVER['REMOTE_ADDR'], 'LogStr_0255', '', $cli_output);

} else {
	echo '<h4>Speedtest (Ookla) Results</h4>';
	echo '<pre style="border: none;">';
	echo 'Speedtest not installed';
	echo '</pre>';
}

?>