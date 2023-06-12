<?php
//------------------------------------------------------------------------------
//  Pi.Alert
//  Open Source Network Guard / WIFI & LAN intrusion detector
//
//  speedtestcli.php - Front module. Server side. Manage Devices
//------------------------------------------------------------------------------
//  leiweibau  2023        https://github.com/leiweibau     GNU GPLv3
//------------------------------------------------------------------------------

exec('../../../back/speedtest-cli --secure --simple', $output);

echo '<h4>Speedtest Results</h4>';
echo '<pre style="border: none;">';
foreach ($output as $line) {
	echo $line . "\n";
}
echo '</pre>';
?>