<!-- ---------------------------------------------------------------------------
#  Pi.Alert
#  Open Source Network Guard / WIFI & LAN intrusion detector
#
#  systeminfo.php - Front module. SystemInfo page
#-------------------------------------------------------------------------------
#  leiweibau 2023                                          GNU GPLv3
#--------------------------------------------------------------------------- -->

<?php
session_start();

// Turn off php errors
error_reporting(0);

if ($_SESSION["login"] != 1) {
	header('Location: ./index.php');
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
$stat['cpu_model'] = str_replace(":", "", trim(str_replace("Model", "", $stat['cpu_model'])));
if ($stat['cpu_model'] == '') {
	$cpu_result = shell_exec("cat /proc/cpuinfo | grep model\ name");
	$stat['cpu_model'] = strstr($cpu_result, "\n", true);
	$stat['cpu_model'] = str_replace(":", "", trim(str_replace("model name", "", $stat['cpu_model'])));
}
if (file_exists('/sys/devices/system/cpu/cpu0/cpufreq/scaling_max_freq')) {
	// RaspbianOS
	$stat['cpu_frequ'] = exec('cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_max_freq') / 1000;
} elseif (is_numeric(str_replace(',', '.', exec('lscpu | grep "MHz" | awk \'{print $3}\'')))) {
	// Ubuntu Server, DietPi event. others
	$stat['cpu_frequ'] = round(exec('lscpu | grep "MHz" | awk \'{print $3}\''), 0);
} elseif (is_numeric(str_replace(',', '.', exec('lscpu | grep "max MHz" | awk \'{print $4}\'')))) {
	// RaspbianOS and event. others
	$stat['cpu_frequ'] = round(str_replace(',', '.', exec('lscpu | grep "max MHz" | awk \'{print $4}\'')), 0);
} else {
	// Fallback
	$stat['cpu_frequ'] = "unknown";
}
$kernel_arch = exec('dpkg --print-architecture');
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
$hdd_result = shell_exec("sudo df | awk '{print $1}'");
$hdd_devices = explode("\n", trim($hdd_result));
$hdd_result = shell_exec("sudo df | awk '{print $2}'");
$hdd_devices_total = explode("\n", trim($hdd_result));
$hdd_result = shell_exec("sudo df | awk '{print $3}'");
$hdd_devices_used = explode("\n", trim($hdd_result));
$hdd_result = shell_exec("sudo df | awk '{print $4}'");
$hdd_devices_free = explode("\n", trim($hdd_result));
$hdd_result = shell_exec("sudo df | awk '{print $5}'");
$hdd_devices_percent = explode("\n", trim($hdd_result));
$hdd_result = shell_exec("sudo df | awk '{print $6}'");
$hdd_devices_mount = explode("\n", trim($hdd_result));
//usb devices
$usb_result = shell_exec("lsusb");
$usb_devices_mount = explode("\n", trim($usb_result));
// users
$temp_usercount = shell_exec("w -h | awk '{print $1 \"##\" $2 \"##\" $3 \"//\"}'");
$search_usercount = array("##:0", "##-");
$str_usercount = str_replace($search_usercount, '', substr($temp_usercount, 0, -3));
$arr_usercount = explode("//", $str_usercount);
foreach ($arr_usercount as $user) {
	$arr_user = explode("##", $user);
	$stat['user_count'] = $stat['user_count'] . '<span class="text-aqua">' . $arr_user[0] . '</span> (' . $arr_user[1] . ') <span class="text-red">' . $arr_user[2] . '</span> /';
}
$stat['user_count'] = substr($stat['user_count'], 0, -2);
// count processes
$stat['process_count'] = shell_exec("ps -e --no-headers | wc -l");
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
// Client ----------------------------------------------------------
echo '<div class="box box-solid">
            <div class="box-header">
              <h3 class="box-title sysinfo_headline"><i class="bi bi-globe"></i> This Client</h3>
            </div>
            <div class="box-body">
				<div class="row">
				  <div class="col-sm-3 sysinfo_gerneral_a">User Agent</div>
				  <div class="col-sm-9 sysinfo_gerneral_b">' . $_SERVER['HTTP_USER_AGENT'] . '</div>
				</div>
				<div class="row">
				  <div class="col-sm-3 sysinfo_gerneral_a">Browser Resolution:</div>
				  <div class="col-sm-9 sysinfo_gerneral_b" id="resolution"></div>
				</div>
            </div>
      </div>';

// General ----------------------------------------------------------
echo '<div class="box box-solid">
            <div class="box-header">
              <h3 class="box-title sysinfo_headline"><i class="bi bi-info-circle"></i> General</h3>
            </div>
            <div class="box-body">
				<div class="row">
				  <div class="col-sm-3 sysinfo_gerneral_a">Uptime</div>
				  <div class="col-sm-9 sysinfo_gerneral_b">' . $stat['uptime'] . '</div>
				</div>
				<div class="row">
				  <div class="col-sm-3 sysinfo_gerneral_a">Operating System</div>
				  <div class="col-sm-9 sysinfo_gerneral_b">' . $stat['os_version'] . '</div>
				</div>
				<div class="row">
				  <div class="col-sm-3 sysinfo_gerneral_a">Kernel Architecture:</div>
				  <div class="col-sm-9 sysinfo_gerneral_b">' . $kernel_arch . '</div>
				</div>
				<div class="row">
				  <div class="col-sm-3 sysinfo_gerneral_a">CPU Name:</div>
				  <div class="col-sm-9 sysinfo_gerneral_b">' . $stat['cpu_model'] . '</div>
				</div>
				<div class="row">
				  <div class="col-sm-3 sysinfo_gerneral_a">CPU Cores:</div>
				  <div class="col-sm-9 sysinfo_gerneral_b">' . $stat['cpu'] . ' @ ' . $stat['cpu_frequ'] . ' MHz</div>
				</div>
				<div class="row">
				  <div class="col-sm-3 sysinfo_gerneral_a">Memory:</div>
				  <div class="col-sm-9 sysinfo_gerneral_b">' . $stat['mem_total'] . ' MB / ' . $stat['mem_used'] . '% is used</div>
				</div>
				<div class="row">
				  <div class="col-sm-3 sysinfo_gerneral_a">Running Processes:</div>
				  <div class="col-sm-9 sysinfo_gerneral_b">' . $stat['process_count'] . '</div>
				</div>
				<div class="row">
				  <div class="col-sm-3 sysinfo_gerneral_a">Logged in Users:</div>
				  <div class="col-sm-9 sysinfo_gerneral_b">' . $stat['user_count'] . '</div>
				</div>
            </div>
      </div>';

echo '<script>
	var ratio = window.devicePixelRatio || 1;
	var w = window.innerWidth;
	var h = window.innerHeight;
	var rw = window.innerWidth * ratio;
	var rh = window.innerHeight * ratio;

	var resolutionDiv = document.getElementById("resolution");
	resolutionDiv.innerHTML = "Width: " + w + "px / Height: " + h + "px<br> " + "Width: " + rw + "px / Height: " + rh + "px (native)";
</script>';

// Storage ----------------------------------------------------------
echo '<div class="box box-solid">
            <div class="box-header">
              <h3 class="box-title sysinfo_headline"><i class="bi bi-hdd"></i> Storage</h3>
            </div>
            <div class="box-body">';

$storage_lsblk = shell_exec("lsblk -io NAME,SIZE,TYPE,MOUNTPOINT,MODEL --list | tail -n +2 | awk '{print $1\"#\"$2\"#\"$3\"#\"$4\"#\"$5}'");
$storage_lsblk_line = explode("\n", $storage_lsblk);
$storage_lsblk_line = array_filter($storage_lsblk_line);

for ($x = 0; $x < sizeof($storage_lsblk_line); $x++) {
	$temp = array();
	$temp = explode("#", $storage_lsblk_line[$x]);
	$storage_lsblk_line[$x] = $temp;
}

for ($x = 0; $x < sizeof($storage_lsblk_line); $x++) {
	echo '<div class="row">';
	if (preg_match('~[0-9]+~', $storage_lsblk_line[$x][0])) {
		echo '<div class="col-sm-4 sysinfo_gerneral_a">Mount point "' . $storage_lsblk_line[$x][3] . '"</div>';
	} else {
		echo '<div class="col-sm-4 sysinfo_gerneral_a">"' . str_replace('_', ' ', $storage_lsblk_line[$x][3]) . '"</div>';
	}
	echo '<div class="col-sm-3 sysinfo_gerneral_b">Device: /dev/' . $storage_lsblk_line[$x][0] . '</div>';
	echo '<div class="col-sm-2 sysinfo_gerneral_b">Size: ' . $storage_lsblk_line[$x][1] . '</div>';
	echo '<div class="col-sm-2 sysinfo_gerneral_b">Type: ' . $storage_lsblk_line[$x][2] . '</div>';
	echo '</div>';
}
echo '      </div>
      </div>';

// Storage usage ----------------------------------------------------------
echo '<div class="box box-solid">
            <div class="box-header">
              <h3 class="box-title sysinfo_headline"><i class="bi bi-hdd"></i> Storage usage</h3>
            </div>
            <div class="box-body">';
for ($x = 0; $x < sizeof($hdd_devices); $x++) {
	if (stristr($hdd_devices[$x], '/dev/')) {
		if (!stristr($hdd_devices[$x], '/loop')) {
			if ($hdd_devices_total[$x] == 0 || $hdd_devices_total[$x] == '') {$temp_total = 0;} else { $temp_total = number_format(round(($hdd_devices_total[$x] / 1024 / 1024), 2), 2, ',', '.');}
			if ($hdd_devices_used[$x] == 0 || $hdd_devices_used[$x] == '') {$temp_used = 0;} else { $temp_used = number_format(round(($hdd_devices_used[$x] / 1024 / 1024), 2), 2, ',', '.');}
			if ($hdd_devices_free[$x] == 0 || $hdd_devices_free[$x] == '') {$temp_free = 0;} else { $temp_free = number_format(round(($hdd_devices_free[$x] / 1024 / 1024), 2), 2, ',', '.');}
			echo '<div class="row">';
			echo '<div class="col-sm-4 sysinfo_gerneral_a">Mount point "' . $hdd_devices_mount[$x] . '"</div>';
			echo '<div class="col-sm-2 sysinfo_gerneral_b">Total: ' . $temp_total . ' GB</div>';
			echo '<div class="col-sm-3 sysinfo_gerneral_b">Used: ' . $temp_used . ' GB (' . $hdd_devices_percent[$x] . ')</div>';
			echo '<div class="col-sm-2 sysinfo_gerneral_b">Free: ' . $temp_free . ' GB</div>';
			echo '</div>';
		}
	}
}
//echo '<br>' . $pia_lang['SysInfo_storage_note'];
echo '      </div>
      </div>';

// Network ----------------------------------------------------------
echo '<div class="box box-solid">
            <div class="box-header">
              <h3 class="box-title sysinfo_headline"><i class="bi bi-hdd-network"></i> Network</h3>
            </div>
            <div class="box-body">';

for ($x = 0; $x < sizeof($net_interfaces); $x++) {
	$interface_name = str_replace(':', '', $net_interfaces[$x]);
	$interface_ip_temp = exec('ip addr show ' . $interface_name . ' | grep "inet "');
	$interface_ip_arr = explode(' ', trim($interface_ip_temp));

	if (!isset($interface_ip_arr[1])) {$interface_ip_arr[1] = '--';}

	if ($net_interfaces_rx[$x] == 0) {$temp_rx = 0;} else { $temp_rx = number_format(round(($net_interfaces_rx[$x] / 1024 / 1024), 2), 2, ',', '.');}
	if ($net_interfaces_tx[$x] == 0) {$temp_tx = 0;} else { $temp_tx = number_format(round(($net_interfaces_tx[$x] / 1024 / 1024), 2), 2, ',', '.');}
	echo '<div class="row">';
	echo '<div class="col-sm-2 sysinfo_network_a">' . $interface_name . '</div>';
	echo '<div class="col-sm-2 sysinfo_network_b">' . $interface_ip_arr[1] . '</div>';
	echo '<div class="col-sm-3 sysinfo_network_b">RX: <div class="sysinfo_network_value">' . $temp_rx . ' MB</div></div>';
	echo '<div class="col-sm-3 sysinfo_network_b">TX: <div class="sysinfo_network_value">' . $temp_tx . ' MB</div></div>';
	echo '</div>';

}
echo '      </div>
      </div>';

// Services ----------------------------------------------------------
echo '<div class="box box-solid">
            <div class="box-header">
              <h3 class="box-title sysinfo_headline"><i class="bi bi-database-gear"></i> Services (running)</h3>
            </div>
            <div class="box-body">';
echo '<div style="height: 300px; overflow: scroll;">';
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

		echo '<tr class="' . $table_color . '"><td style="padding: 3px; padding-left: 10px;">' . substr($servives_name, 0, -8) . '</td><td style="padding: 3px; padding-left: 10px;">' . $servives_description . '</td></tr>';
	}
}
echo '</table>';
echo '</div>';
echo '      </div>
      </div>';

// USB ----------------------------------------------------------
echo '<div class="box box-solid">
            <div class="box-header">
               <h3 class="box-title sysinfo_headline"><i class="bi bi-usb-symbol"></i> USB Devices</h3>
            </div>
            <div class="box-body">';
echo '         <table class="table table-bordered table-hover table-striped dataTable no-footer" style="margin-bottom: 10px;">';

$table_color = 'odd';
sort($usb_devices_mount);
for ($x = 0; $x < sizeof($usb_devices_mount); $x++) {
	$cut_pos = strpos($usb_devices_mount[$x], ':');
	$usb_bus = substr($usb_devices_mount[$x], 0, $cut_pos);
	$usb_dev = substr($usb_devices_mount[$x], $cut_pos + 1);

	if ($table_color == 'odd') {$table_color = 'even';} else { $table_color = 'odd';}
	echo '<tr class="' . $table_color . '"><td style="padding: 3px; padding-left: 10px; width: 150px;"><b>' . str_replace('Device', 'Dev.', $usb_bus) . '</b></td><td style="padding: 3px; padding-left: 10px;">' . $usb_dev . '</td></tr>';
}
echo '         </table>';
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