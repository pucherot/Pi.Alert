<!-- ---------------------------------------------------------------------------
#  Pi.Alert
#  Open Source Network Guard / WIFI & LAN intrusion detector
#
#  reports.php - Front module. WebGUI Notification page
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
require 'php/server/journal.php';

function get_notification_class($filename) {
	$headtitle = explode("-", $filename);
	$headeventtype = explode("_", $filename);
	$temp_class[0] = substr($headeventtype[1], 0, -4);
	if ($temp_class[0] == "Events" || $temp_class[0] == "Down" || $temp_class[0] == "New Devices") {
		$temp_class[1] = 'arp';
		$temp_class[2] = substr($headtitle[0], 6, 2) . '.' . substr($headtitle[0], 4, 2) . '.' . substr($headtitle[0], 2, 2) . '/' . substr($headtitle[1], 0, 2) . ':' . substr($headtitle[1], 2, 2);
		return $temp_class;
	}
	if ($temp_class[0] == "Internet") {
		$temp_class[1] = 'internet';
		$temp_class[2] = substr($headtitle[0], 6, 2) . '.' . substr($headtitle[0], 4, 2) . '.' . substr($headtitle[0], 2, 2) . '/' . substr($headtitle[1], 0, 2) . ':' . substr($headtitle[1], 2, 2);
		return $temp_class;
	}
	if ($temp_class[0] == "Services Events" || $temp_class[0] == "Services Down") {
		$temp_class[1] = 'webmon';
		$temp_class[2] = substr($headtitle[0], 6, 2) . '.' . substr($headtitle[0], 4, 2) . '.' . substr($headtitle[0], 2, 2) . '/' . substr($headtitle[1], 0, 2) . ':' . substr($headtitle[1], 2, 2);
		return $temp_class;
	}
	if ($temp_class[0] == "Test") {
		$temp_class[1] = 'test';
		$temp_class[2] = substr($headtitle[0], 6, 2) . '.' . substr($headtitle[0], 4, 2) . '.' . substr($headtitle[0], 2, 2) . '/' . substr($headtitle[1], 0, 2) . ':' . substr($headtitle[1], 2, 2);
		return $temp_class;
	}
	if ($temp_class[0] == "Rogue DHCP Server") {
		$temp_class[1] = 'rogueDHCP';
		$temp_class[2] = substr($headtitle[0], 6, 2) . '.' . substr($headtitle[0], 4, 2) . '.' . substr($headtitle[0], 2, 2) . '/' . substr($headtitle[1], 0, 2) . ':' . substr($headtitle[1], 2, 2);
		return $temp_class;
	}
}

function process_arp_notifications($class_name, $event_time, $filename, $directory) {
	$webgui_report = file_get_contents($directory . $filename);
	$webgui_report = str_replace("\n\n\n", "", $webgui_report);
	return '<div class="box box-solid">
            <div class="box-header">
              <h3 class="box-title" style="color: #D81B60"><i class="fa fa-laptop"></i>&nbsp;&nbsp;' . $event_time . ' - ' . $class_name . '</h3>
                <div class="pull-right">
                  <a href="./download/report.php?report=' . substr($filename, 0, -4) . '" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-fw fa-download"></i></a>
                  <a href="./reports.php?remove_report=' . substr($filename, 0, -4) . '" class="btn btn-sm btn-danger"><i class="fa fa-fw fa-trash"></i></a>
                </div>
            </div>
            <div class="box-body"><pre style="background-color: transparent; border: none;">' . $webgui_report . '</pre></div>
            </div>';
}

function process_internet_notifications($class_name, $event_time, $filename, $directory) {
	$webgui_report = file_get_contents($directory . $filename);
	$webgui_report = str_replace("\n\n\n", "", $webgui_report);
	return '<div class="box box-solid">
            <div class="box-header">
              <h3 class="box-title" style="color: #30bbbb"><i class="fa fa-laptop"></i>&nbsp;&nbsp;' . $event_time . ' - ' . $class_name . '</h3>
                <div class="pull-right">
                  <a href="./download/report.php?report=' . substr($filename, 0, -4) . '" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-fw fa-download"></i></a>
                  <a href="./reports.php?remove_report=' . substr($filename, 0, -4) . '" class="btn btn-sm btn-danger"><i class="fa fa-fw fa-trash"></i></a>
                </div>
            </div>
            <div class="box-body"><pre style="background-color: transparent; border: none;">' . $webgui_report . '</pre></div>
            </div>';
}

function process_webmon_notifications($class_name, $event_time, $filename, $directory) {
	$webgui_report = file_get_contents($directory . $filename);
	$webgui_report = str_replace("\n\n\n", "", $webgui_report);
	return '<div class="box box-solid">
            <div class="box-header">
              <h3 class="box-title" style="color: #00c0ef"><i class="fa fa-globe"></i>&nbsp;&nbsp;' . $event_time . ' - ' . $class_name . '</h3>
                <div class="pull-right">
                  <a href="./download/report.php?report=' . substr($filename, 0, -4) . '" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-fw fa-download"></i></a>
                  <a href="./reports.php?remove_report=' . substr($filename, 0, -4) . '" class="btn btn-sm btn-danger"><i class="fa fa-fw fa-trash"></i></a>
                </div>
            </div>
            <div class="box-body"><pre style="background-color: transparent; border: none;">' . $webgui_report . '</pre></div>
            </div>';
}

function process_test_notifications($class_name, $event_time, $filename, $directory) {
	$webgui_report = file_get_contents($directory . $filename);
	$webgui_report = str_replace("\n\n\n", "", $webgui_report);
	return '<div class="box box-solid">
            <div class="box-header">
              <h3 class="box-title" style="color: #00a65a"><i class="fa fa-envelope-o"></i>&nbsp;&nbsp;' . $event_time . ' - System Message</h3>
                <div class="pull-right">
                  <a href="./download/report.php?report=' . substr($filename, 0, -4) . '" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-fw fa-download"></i></a>
                  <a href="./reports.php?remove_report=' . substr($filename, 0, -4) . '" class="btn btn-sm btn-danger"><i class="fa fa-fw fa-trash"></i></a>
                </div>
            </div>
            <div class="box-body"><pre style="background-color: transparent; border: none;">' . $webgui_report . '</pre></div>
            </div>';
}

function process_rogueDHCP_notifications($class_name, $event_time, $filename, $directory) {
	global $pia_lang;
	$webgui_report = file_get_contents($directory . $filename);
	$webgui_report = str_replace("\n\n\n", "", $webgui_report);
	return '<div class="box box-solid bg-red-active">
            <div class="box-header">
              <h3 class="box-title"><i class="fa fa-warning"></i>&nbsp;&nbsp;' . $event_time . ' - ' . $class_name . '</h3>
                <div class="pull-right">
                  <a href="./download/report.php?report=' . substr($filename, 0, -4) . '" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-fw fa-download"></i></a>
                  <a href="./reports.php?remove_report=' . substr($filename, 0, -4) . '" class="btn btn-sm btn-danger" style=" border: solid 1px #ddd;"><i class="fa fa-fw fa-trash"></i></a>
                </div>
            </div>
            <div class="box-body"><pre style="background-color: transparent; border: none;">' . $webgui_report . '</pre>
            <p style="font-size: 16px; text-align: center;">' . $pia_lang['Reports_Rogue_hint'] . '</p>
            </div>
            </div>';
}

$directory = './reports/';
$scanned_directory = array_diff(scandir($directory), array('..', '.'));
rsort($scanned_directory);

$standard_notification = array();
$special_notification = array();
foreach ($scanned_directory as $file) {
	if (substr($file, -4) == '.txt') {
		$notification_class = get_notification_class($file);
		if ($notification_class[1] == "arp") {
			array_push($standard_notification, process_arp_notifications($notification_class[0], $notification_class[2], $file, $directory));
		} elseif ($notification_class[1] == "internet") {
			array_push($standard_notification, process_internet_notifications($notification_class[0], $notification_class[2], $file, $directory));
		} elseif ($notification_class[1] == "webmon") {
			array_push($standard_notification, process_webmon_notifications($notification_class[0], $notification_class[2], $file, $directory));
		} elseif ($notification_class[1] == "test") {
			array_push($standard_notification, process_test_notifications($notification_class[0], $notification_class[2], $file, $directory));
		} elseif ($notification_class[1] == "rogueDHCP") {
			array_push($special_notification, process_rogueDHCP_notifications($notification_class[0], $notification_class[2], $file, $directory));
		}
	}
}

?>

<!-- Page ------------------------------------------------------------------ -->
<div class="content-wrapper">

<!-- Content header--------------------------------------------------------- -->
    <section class="content-header">
    <?php require 'php/templates/notification.php';?>
      <h1 id="pageTitle">
         <?php echo $pia_lang['Reports_Title']; ?>
      </h1>
    </section>

    <!-- Main content ---------------------------------------------------------- -->
    <section class="content">
      <div class="box">
          <div class="box-body" id="RemoveAllNotifications" style="text-align: center; padding-top: 5px; padding-bottom: 5px; height: 45px;">
              <button type="button" id="rqwejwedewjpjo" class="btn btn-danger" onclick="askdeleteAllNotifications()"><?php echo $pia_lang['Reports_delete_all']; ?></button>
        </div>
      </div>

<?php

for ($x = 0; $x < sizeof($special_notification); $x++) {
	echo $special_notification[$x];
}

for ($x = 0; $x < sizeof($standard_notification); $x++) {
	echo $standard_notification[$x];
}

?>
    <div style="width: 100%; height: 20px;"></div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<!-- ----------------------------------------------------------------------- -->
<?php
require 'php/templates/footer.php';
?>

<script>
function askdeleteAllNotifications () {
  // Ask
  showModalWarning('<?php echo $pia_lang['Reports_delete_all_noti']; ?>', '<?php echo $pia_lang['Reports_delete_all_noti_text']; ?>',
    '<?php echo $pia_lang['Gen_Cancel']; ?>', '<?php echo $pia_lang['Gen_Delete']; ?>', 'deleteAllNotifications');
}
function deleteAllNotifications()
{
  // Delete
  $.get('php/server/devices.php?action=deleteAllNotifications', function(msg) {
    showMessage (msg);
  });
}
</script>
