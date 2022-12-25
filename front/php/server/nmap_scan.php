<?php
session_start();

$PIA_HOST_IP = $_REQUEST['scan'];
$PIA_SCAN_MODE = $_REQUEST['mode'];

if (filter_var($PIA_HOST_IP, FILTER_VALIDATE_IP)) {
	if ($PIA_SCAN_MODE == 'fast') {
    		exec('nmap -F '.$PIA_HOST_IP, $output);
	} elseif ($PIA_SCAN_MODE == 'normal') {
	    exec('nmap '.$PIA_HOST_IP, $output);
	} elseif ($PIA_SCAN_MODE == 'detail') {
	    exec('nmap -A '.$PIA_HOST_IP, $output);
	}
} else {exit;}
echo '<h4>Scan ('.$PIA_SCAN_MODE.') Results of: '.$PIA_HOST_IP.'</h4>';
echo '<pre style="border: none;">';

// Prepare short term memory
$PIA_SCAN_TIME = date('Y-m-d H:i:s');

unset($_SESSION['ScanShortMem']);
$_SESSION['ScanShortMem'] = 'Last Nmap Scan<br><br><span style="display:inline-block; width: 100px;">Scan Target:</span> '.$PIA_HOST_IP.'<br><span style="display:inline-block; width: 100px;">Scan Mode:</span> '.$PIA_SCAN_MODE.'<br><span style="display:inline-block; width: 100px;">Scan Time:</span> '.$PIA_SCAN_TIME.'<br><br>Result:<br>';

foreach($output as $line){
    echo $line . "\n";
    // Safe last Scan result in Session (Short term memory)
    $_SESSION['ScanShortMem'] = $_SESSION['ScanShortMem'].$line.'<br>';
}
echo '</pre>';


?>