<!-- ---------------------------------------------------------------------------
#  Pi.Alert
#  Open Source Network Guard / WIFI & LAN intrusion detector
#
#  systeminfo.php - Front module. network relationship
#-------------------------------------------------------------------------------
#  leiweibau 2023                                          GNU GPLv3
#--------------------------------------------------------------------------- -->

<?php
session_start();

// Turn off php errors
error_reporting(0);

if ($_SESSION["login"] != 1) {
	header('Location: /pialert/index.php');
	exit;
}

require 'php/templates/header.php';

// https://stackoverflow.com/a/19209082

$os_version = '';
// Raspbian
if ($os_version == '') {$os_version = exec('cat /etc/os-release | grep PRETTY_NAME');}
// Dietpi
if ($os_version == '') {$os_version = exec('uname -o');}
//$os_version_arr = explode("\n", trim($os_version));
$stat['os_version'] = str_replace('"', '', str_replace('PRETTY_NAME=', '', $os_version));

$stat['uptime'] = str_replace('up ', '', shell_exec("uptime -p"));
//cpu stat
$prevVal = shell_exec("cat /proc/cpuinfo | grep processor");
$prevArr = explode("\n", trim($prevVal));
$stat['cpu'] = sizeof($prevArr);
$cpu_result = shell_exec("cat /proc/cpuinfo | grep Model");
$stat['cpu_model'] = strstr($cpu_result, "\n", true);
$stat['cpu_model'] = str_replace("Model    : ", "", $stat['cpu_model']);
if ($stat['cpu_model'] == '') {
	$cpu_result = shell_exec("cat /proc/cpuinfo | grep model\ name");
	$stat['cpu_model'] = strstr($cpu_result, "\n", true);
	$stat['cpu_model'] = str_replace("model name      : ", "", $stat['cpu_model']);
}

//memory stat
$mem_result = shell_exec("cat /proc/meminfo | grep MemTotal");
$stat['mem_total'] = round(preg_replace("#[^0-9]+(?:\.[0-9]*)?#", "", $mem_result) / 1024 / 1024, 3);
$stat['mem_used'] = round(getMemUsage() * 100, 2);
//network stat
$network_result = shell_exec("cat /proc/net/dev | tail -n +3 | awk '{print $1}'");
$net_interfaces = explode("\n", trim($network_result));
$network_result = shell_exec("cat /proc/net/dev | tail -n +3 | awk '{print $2}'");
$net_interfaces_rx = explode("\n", trim($network_result));
$network_result = shell_exec("cat /proc/net/dev | tail -n +3 | awk '{print $10}'");
$net_interfaces_tx = explode("\n", trim($network_result));
//hdd stat
$hdd_result = shell_exec("df | tail | awk '{print $1}'");
$hdd_devices = explode("\n", trim($hdd_result));
$hdd_result = shell_exec("df | tail | awk '{print $2}'");
$hdd_devices_total = explode("\n", trim($hdd_result));
$hdd_result = shell_exec("df | tail | awk '{print $3}'");
$hdd_devices_used = explode("\n", trim($hdd_result));
$hdd_result = shell_exec("df | tail | awk '{print $4}'");
$hdd_devices_free = explode("\n", trim($hdd_result));
$hdd_result = shell_exec("df | tail | awk '{print $5}'");
$hdd_devices_percent = explode("\n", trim($hdd_result));
$hdd_result = shell_exec("df | tail | awk '{print $6}'");
$hdd_devices_mount = explode("\n", trim($hdd_result));
//usb devices
$usb_result = shell_exec("lsusb");
$usb_devices_mount = explode("\n", trim($usb_result));

?>

<!-- Page ------------------------------------------------------------------ -->
<div class="content-wrapper">

<!-- Content header--------------------------------------------------------- -->
    <section class="content-header">
    <?php require 'php/templates/notification.php';?>
      <h1 id="pageTitle">
         System Infomation
      </h1>
    </section>

    <!-- Main content ---------------------------------------------------------- -->
    <section class="content">
<?php

// General ----------------------------------------------------------
echo '<div class="box box-solid">
            <div class="box-header">
              <h3 class="box-title" style="color: #00c0ef">General</h3>
            </div>
            <div class="box-body">
				<div class="row">
				  <div class="col-sm-2" style="font-weight: bold;">Uptime</div>
				  <div class="col-sm-10" style="padding-left: 30px;">' . $stat['uptime'] . '</div>
				</div>
				<div class="row">
				  <div class="col-sm-2" style="font-weight: bold;">OS</div>
				  <div class="col-sm-10" style="padding-left: 30px;">' . $stat['os_version'] . '</div>
				</div>
				<div class="row">
				  <div class="col-sm-2" style="font-weight: bold;">CPU Name:</div>
				  <div class="col-sm-10" style="padding-left: 30px;">' . $stat['cpu_model'] . '</div>
				</div>
				<div class="row">
				  <div class="col-sm-2" style="font-weight: bold;">CPU Cores:</div>
				  <div class="col-sm-10" style="padding-left: 30px;">' . $stat['cpu'] . '</div>
				</div>
				<div class="row">
				  <div class="col-sm-2" style="font-weight: bold;">Memory:</div>
				  <div class="col-sm-10" style="padding-left: 30px;">' . $stat['mem_total'] . ' MB / ' . $stat['mem_used'] . '% is used</div>
				</div>
            </div>
      </div>';

// Storage ----------------------------------------------------------
echo '<div class="box box-solid">
            <div class="box-header">
              <h3 class="box-title" style="color: #00c0ef">Storage</h3>
            </div>
            <div class="box-body">';

for ($x = 0; $x < sizeof($hdd_devices); $x++) {
	if (stristr($hdd_devices[$x], '/dev/')) {
		echo '<div class="row">';
		echo '<div class="col-sm-3" style="font-weight: bold; ">Mount point "' . $hdd_devices_mount[$x] . '"</div>';
		echo '<div class="col-sm-2" style="padding-left: 30px; white-space: nowrap;">Total ' . number_format(round(($hdd_devices_total[$x] / 1024 / 1024), 2), 2, ',', '.') . ' GB</div>';
		echo '<div class="col-sm-2" style="padding-left: 30px; white-space: nowrap;">Used ' . number_format(round(($hdd_devices_used[$x] / 1024 / 1024), 2), 2, ',', '.') . ' GB</div>';
		echo '<div class="col-sm-2" style="padding-left: 30px; white-space: nowrap;">Free ' . number_format(round(($hdd_devices_free[$x] / 1024 / 1024), 2), 2, ',', '.') . ' GB</div>';
		echo '<div class="col-sm-2" style="padding-left: 30px; white-space: nowrap;">Usage ' . number_format($hdd_devices_percent[$x], 1, ',', '.') . '%</div>';
		echo '</div>';
	}
}

echo '      </div>
      </div>';

// Network ----------------------------------------------------------
echo '<div class="box box-solid">
            <div class="box-header">
              <h3 class="box-title" style="color: #00c0ef">Network</h3>
            </div>
            <div class="box-body">';

for ($x = 0; $x < sizeof($net_interfaces); $x++) {
	$interface_name = str_replace(':', '', $net_interfaces[$x]);
	$interface_ip_temp = exec('ip addr show ' . $interface_name . ' | grep inet');
	$interface_ip_arr = explode(' ', trim($interface_ip_temp));

	if (!isset($interface_ip_arr[1])) {$interface_ip_arr[1] = '--';}

	echo '<div class="row">';
	echo '<div class="col-sm-2" style="font-weight: bold; white-space: nowrap;">' . $interface_name . '</div>';
	echo '<div class="col-sm-3" style="padding-left: 30px; white-space: nowrap;">IP  ' . $interface_ip_arr[1] . '</div>';
	echo '<div class="col-sm-3" style="padding-left: 30px; white-space: nowrap;">RX: <div style="display: inline-block; width: 120px; text-align: right;">' . number_format(round(($net_interfaces_rx[$x] / 1024 / 1024), 2), 2, ',', '.') . ' MB</div></div>';
	echo '<div class="col-sm-3" style="padding-left: 30px; white-space: nowrap;">TX: <div style="display: inline-block; width: 120px; text-align: right;">' . number_format(round(($net_interfaces_tx[$x] / 1024 / 1024), 2), 2, ',', '.') . ' MB</div></div>';
	echo '</div>';

}

echo '      </div>
      </div>';

// Services ----------------------------------------------------------
echo '<div class="box box-solid">
            <div class="box-header">
              <h3 class="box-title" style="color: #00c0ef">Services running</h3>
            </div>
            <div class="box-body">';

exec('systemctl --type=service --state=running', $running_services);
echo '<table class="table table-bordered table-hover table-striped dataTable no-footer" style="margin-bottom: 10px;">';
echo '<thead>
		<tr role="row">
			<th style="padding: 8px;">Service Name</th>
			<th style="padding: 8px;">Service Description</th>
		</tr>
	  </thead>';
$table_color = 'odd';
for ($x = 0; $x < sizeof($running_services); $x++) {
	if (stristr($running_services[$x], '.service')) {
		$temp_services_arr = array_values(array_filter(explode(' ', trim($running_services[$x]))));
		$servives_name = $temp_services_arr[0];
		unset($temp_services_arr[0], $temp_services_arr[1], $temp_services_arr[2], $temp_services_arr[3]);
		$servives_description = implode(" ", $temp_services_arr);
		if ($table_color == 'odd') {$table_color = 'even';} else { $table_color = 'odd';}

		echo '<tr class="' . $table_color . '"><td style="padding: 3px; padding-left: 10px;">' . $servives_name . '</td><td style="padding: 3px; padding-left: 10px;">' . $servives_description . '</td></tr>';
	}
}
echo '</table>';

echo '      </div>
      </div>';

// USB ----------------------------------------------------------
echo '<div class="box box-solid">
            <div class="box-header">
               <h3 class="box-title" style="color: #00c0ef">USB Devices</h3>
            </div>
            <div class="box-body">';

sort($usb_devices_mount);
for ($x = 0; $x < sizeof($usb_devices_mount); $x++) {
	echo '' . $usb_devices_mount[$x] . '<br>';
}

echo '      </div>
      </div>';

echo '<br>';

?>
    </section>

    <!-- /.content -->
</div>
  <!-- /.content-wrapper -->

<!-- ----------------------------------------------------------------------- -->
<?php
require 'php/templates/footer.php';
?>