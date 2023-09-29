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
$supported_arch = array('i386', 'x86_64', 'armel', 'armhf', 'aarch64');
$mod = $_REQUEST['mod'];

# Checking the web page to determine the current version
# ------------------------------------------------------------------------------
function get_speedtest_link($architecture) {
	global $supported_arch;

	$url = 'https://www.speedtest.net/apps/cli';
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$html = curl_exec($ch);
	if (curl_errno($ch)) {
		echo 'cURL error: ' . curl_error($ch);
		exit;
	}
	curl_close($ch);

	$dom = new DOMDocument();
	@$dom->loadHTML($html);
	$xpath = new DOMXPath($dom);
	$ulBlock = $xpath->query('//ul[@id="linux-flyout"]')->item(0);
	if ($ulBlock) {
		$ulHTML = $dom->saveHTML($ulBlock);
		$strippedText = $ulHTML;
		$pattern = '/https:\/\/[^\s"]+/';
		preg_match_all($pattern, $strippedText, $matches);
		$urls = $matches[0][0];
		return str_replace($supported_arch, $architecture, $urls);
	}
}

# Download the current version
# ------------------------------------------------------------------------------
function download_speedtest($link) {
	$savePath = '../../../back/speedtest/speedtest.tgz';

// Disable caching
	header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');

	file_put_contents($savePath, fopen($link, 'r'));

	echo 'File downloaded successfully!';
}

# Extract archive
# ------------------------------------------------------------------------------
function extract_speedtest() {
	$archivePath = '../../../back/speedtest/';
	$archiveName = 'speedtest.tgz';
	$speedtestbinary = 'speedtest';

	if (file_exists($archivePath . $archiveName)) {
		exec('/usr/bin/tar zxvf ' . $archivePath . $archiveName . ' -C ' . $archivePath, $output);
	}
	if (file_exists($archivePath . $speedtestbinary)) {
		echo 'File extract successfully!';
	} else {echo 'File extraction failed!';}
}

# Delete archive
# ------------------------------------------------------------------------------
function delete_speedtest_archive() {
	$achivePath = '../../../back/speedtest/speedtest.tgz';

	if (file_exists($achivePath)) {
		unlink($achivePath);
	}
	if (!file_exists($achivePath)) {
		echo 'Archive deleted successfully!';
	}
}

# Start the Test if speedtest exists
# ------------------------------------------------------------------------------
if (file_exists($speedtest_binary) && $mod == "test") {
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

# Try Downloading
# ------------------------------------------------------------------------------
} elseif ($mod == "get") {
	echo '<h4>Speedtest (Ookla) Results</h4>';
	echo '<pre style="border: none;">';
	echo 'Try to install the speedtest. Only i386, x86_64, armel, armhf and aarch64 are currently supported.';

	echo "\n";

	$supported_arch = array('i386', 'x86_64', 'armel', 'armhf', 'aarch64');
	$kernel_arch = exec('dpkg --print-architecture');

	if (in_array($kernel_arch, $supported_arch)) {
		# System matches one of the clients
		echo "Detected System Architecture: " . $kernel_arch . "\n";
		$downloadlink = get_speedtest_link($kernel_arch);
		echo "Selected Downloadlink: " . $downloadlink . "\n";
		download_speedtest($downloadlink);
		echo "\n";
		extract_speedtest();
		echo "\n";
		delete_speedtest_archive();
		echo "\n";

		//echo 'Page will be reloaded';
		//echo ("<meta http-equiv='refresh' content='4; URL=./deviceDetails.php?mac=Internet'>");

	} elseif ($kernel_arch == "amd64") {
		# Compatible client possible
		echo "Detected System Architecture: " . $kernel_arch . "\n";
		$downloadlink = get_speedtest_link('x86_64');
		echo "Selected Downloadlink: " . $downloadlink . "\n";
		download_speedtest($downloadlink);
		echo "\n";
		extract_speedtest();
		echo "\n";
		delete_speedtest_archive();
		echo "\n";
		//echo 'Page will be reloaded';
		//echo ("<meta http-equiv='refresh' content='4; URL=./deviceDetails.php?mac=Internet'>");
	}

	echo '</pre>';

	echo '<span class="text-red" style="font-size: 18px;">Before you can to use the speedtest client from Ookla, you have to execute the command "sudo ./speedtest" once in the directory "$HOME/pialert/back/speedtest/".</span>';

# Speedtest not installed
# ------------------------------------------------------------------------------
} else {
	echo '<h4>Speedtest (Ookla) Results</h4>';
	echo '<pre style="border: none;">';
	echo 'Speedtest not installed';
	echo '</pre>';
}

?>