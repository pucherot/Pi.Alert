<!-- ---------------------------------------------------------------------------
#  Pi.Alert
#  Open Source Network Guard / WIFI & LAN intrusion detector
#
#  services.php - Front module. Server side. Manage Devices
#-------------------------------------------------------------------------------
#  leiweibau 2023                                          GNU GPLv3
#--------------------------------------------------------------------------- -->

<?php
session_start();
error_reporting(0);

if ($_SESSION["login"] != 1) {
	header('Location: ./index.php');
	exit;
}

require 'php/templates/header.php';
require 'php/server/db.php';
require 'php/server/journal.php';

// -----------------------------------------------------------------------------
$DBFILE = '../db/pialert.db';
OpenDB();

// -----------------------------------------------------------------------------
function open_http_status_code_json() {
	$jsonfile = file_get_contents("./lib/http-status-code/index.json");
	$array = json_decode($jsonfile, true);
	return $array;
}

// -----------------------------------------------------------------------------
function getDeviceMacs() {
	global $db;
	$dev_res = $db->query('SELECT dev_MAC, dev_Name FROM Devices ORDER BY dev_Name ASC');
	$code_array = array();
	while ($row = $dev_res->fetchArray()) {
		echo '<li><a href="javascript:void(0)" onclick="setTextValue(\'serviceMAC\',\'' . $row['dev_MAC'] . '\')">' . $row['dev_Name'] . '</a></li>';
	}
}

// -----------------------------------------------------------------------------
// Get the latest 15 StatusCodes from a specific URL in order latest -> older
function get_latest_latency_from_url($service_URL) {
	global $db;
	unset($code_array, $i, $moneve_res);
	$moneve_res = $db->query('SELECT * FROM Services_Events ORDER BY moneve_DateTime DESC');
	$i = 0;
	$code_array = array();
	while ($row = $moneve_res->fetchArray()) {
		if ($row['moneve_URL'] == $service_URL) {
			$code_array[17 - $i] = $row['moneve_Latency'];
			$i++;
		}
		if ($i == 18) {break;}
	}
	return $code_array;
}

// -----------------------------------------------------------------------------
// Get the latest 15 StatusCodes from a specific URL in order latest -> older
function get_latest_statuscodes_from_url($service_URL) {
	global $db;
	unset($code_array, $i, $moneve_res);
	$moneve_res = $db->query('SELECT * FROM Services_Events ORDER BY moneve_DateTime DESC');
	$i = 0;
	$code_array = array();
	while ($row = $moneve_res->fetchArray()) {
		if ($row['moneve_URL'] == $service_URL) {
			$code_array[17 - $i] = $row['moneve_StatusCode'];
			$i++;
		}
		if ($i == 18) {break;}
	}
	return $code_array;
}

// -----------------------------------------------------------------------------
// Get the latest 15 StatusCodes from a specific URL in order latest -> older
function get_latest_scans_from_url($service_URL) {
	global $db;
	unset($code_array, $i, $moneve_res);
	$moneve_res = $db->query('SELECT * FROM Services_Events ORDER BY moneve_DateTime DESC');
	$i = 0;
	$code_array = array();
	while ($row = $moneve_res->fetchArray()) {
		if ($row['moneve_URL'] == $service_URL) {
			$code_array[17 - $i] = $row['moneve_DateTime'];
			$i++;
		}
		if ($i == 18) {break;}
	}
	return $code_array;
}

// -----------------------------------------------------------------------------
// Get Name from Devices
function get_device_name($service_MAC) {
	global $db;
	$dev_res = $db->query('SELECT * FROM Devices');
	while ($row = $dev_res->fetchArray()) {
		if ($row['dev_MAC'] == $service_MAC) {
			return $row['dev_Name'];
		}
	}
}

// -----------------------------------------------------------------------------
// Print a list of all monitored URLs
function list_all_services() {
	global $db;
	$mon_res = $db->query('SELECT * FROM Services');
	while ($row = $mon_res->fetchArray()) {
		echo $row['mon_URL'] . ' - ' . $row['mon_MAC'] . ' - ' . $row['mon_TargetIP'] . '<br>';
	}
}

// -----------------------------------------------------------------------------
// get Count of all standalone services
function get_count_standalone_services() {
	global $db;
	$mon_res = $db->query('SELECT * FROM Services');
	$func_count = 0;
	while ($row = $mon_res->fetchArray()) {
		if ($row['mon_MAC'] == "") {$func_count++;}
	}
	return $func_count;
}

// -----------------------------------------------------------------------------
// get String with the selected notifications
function get_notifications($alertDown, $alertEvent) {
	global $pia_lang;
	if ($alertEvent == "1" && $alertDown == "1") {$notification_type = '<i class="fa fa-fw fa-bell-o"></i> ' . $pia_lang['WebServices_Events_all'] . ", " . $pia_lang['WebServices_Events_down'];} elseif ($alertEvent == "0" && $alertDown == "1") {$notification_type = '<i class="fa fa-fw fa-bell-o"></i> ' . $pia_lang['WebServices_Events_down'];} elseif ($alertEvent == "1" && $alertDown == "0") {$notification_type = '<i class="fa fa-fw fa-bell-o"></i> ' . $pia_lang['WebServices_Events_all'];} else { $notification_type = '<i class="fa fa-fw fa-bell-slash-o"></i>';}
	return $notification_type;
}

// -----------------------------------------------------------------------------
// get color from status code
function get_icon_color($statuscode) {
	if (substr($statuscode, 0, 1) == "2") {$code_icon_color = "bg-green";}
	if (substr($statuscode, 0, 1) == "3") {$code_icon_color = "bg-yellow";}
	if (substr($statuscode, 0, 1) == "4") {$code_icon_color = "bg-yellow";}
	if (substr($statuscode, 0, 1) == "5") {$code_icon_color = "bg-orange-custom";}
	if ($statuscode == "0") {$code_icon_color = "bg-red";}
	return $code_icon_color;
}

// -----------------------------------------------------------------------------
// Print a list of all monitored URLs without a MAC Adresse
function list_standalone_services() {
	global $pia_lang;
	global $http_status_code;
	global $db;

	$mon_res = $db->query('SELECT * FROM Services ORDER BY mon_Tags COLLATE NOCASE ASC');
	// General Box for all Services without MAC
	echo '<div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">' . $pia_lang['WebServices_BoxTitle_General'] . '</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">';

	// Print Services Loop
	while ($row = $mon_res->fetchArray()) {
		if ($row['mon_MAC'] == "") {
			if (substr($row['mon_LastStatus'], 0, 1) == "2") {$code_icon_color = "bg-green";}

			$notification_type = get_notifications($row['mon_AlertDown'], $row['mon_AlertEvents']);
			$code_icon_color = get_icon_color($row['mon_LastStatus']);
			$url_array = explode('://', $row['mon_URL']);

			if ($http_status_code[$row['mon_LastStatus']] != "") {
				$status_description = $http_status_code[$row['mon_LastStatus']]['description'];
			} else {
				$status_description = 'No status code was received from the server. The server may be offline or the network may have a problem.';
			}

			echo '<div class="servicelist_entry">
                    <div class="' . $code_icon_color . ' servicebox_httpstat_hover" data-toggle="tooltip" data-placement="top" title="' . $status_description . '">
                        <div class="servicebox_box">
                            <div style="display: block; margin-top:5px;"><span class="servicebox_box_prot">' . strtoupper($url_array[0]) . '</span></div>
                            <div style="display: block;"><span class="servicebox_box_code">' . $row['mon_LastStatus'] . '</span></div>
                            <i class="fa fa-globe" style="position: relative; top: -58px; left: 0px; font-size: 58px; opacity: 0.2;"></i>
                        </div>
                    </div>
                    <div class="servicebox_text">
                        <div class="servicebox_text_m">
                           <table height="20px" width="100%"><tr><td><a href="serviceDetails.php?url=' . $row['mon_URL'] . '"><span class="">' . $url_array[1] . '</span></a></td><td align="right"><span class="servicebox_text_tag">&nbsp;' . $row['mon_Tags'] . '</span></td></tr></table>';
			// Render Progressbar
			echo '          <div class="progress-segment">';

			// Get Tooltip values
			$func_scans = get_latest_scans_from_url($row['mon_URL']);
			$func_httpcodes = get_latest_statuscodes_from_url($row['mon_URL']);
			$func_latency = get_latest_latency_from_url($row['mon_URL']);

			for ($x = 0; $x < 18; $x++) {
				unset($codecolor);
				$for_httpcode = $func_httpcodes[$x];
				if ($for_httpcode >= 200 && $for_httpcode < 300) {$codecolor = "bg-green";}
				if ($for_httpcode >= 300 && $for_httpcode < 500) {$codecolor = "bg-yellow";}
				if ($for_httpcode >= 500 && $for_httpcode < 600) {$codecolor = "bg-orange-custom";}
				if ($for_httpcode == "0") {$codecolor = "bg-red";}
				if ($func_latency[$x] == '99999999') {$loop_latency = 'offline';} else { $loop_latency = $func_latency[$x] . 's';}

				echo '       <div class="single_scan ' . $codecolor . '" title="' . $func_scans[$x] . ' / HTTP: ' . $for_httpcode . ' / Latency: ' . $loop_latency . '"></div>';

			}

			echo '         </div>';
			echo '         <table height="20px" width="100%"><tr><td><span class="progress-description">IP: ' . $row['mon_TargetIP'] . '</span></td><td align="right">' . $notification_type . '</td></tr></table>
                        </div>
                    </div>
                  </div>';
		}
	}

	echo '  <!-- /.box-body -->
            </div>
          </div>';
}

// -----------------------------------------------------------------------------
// Get a array of unique devices with monitored URLs
function get_devices_from_services() {
	global $db;
	$mon_res = $db->query('SELECT * FROM Services');
	$func_unique_devices = array();
	while ($row = $mon_res->fetchArray()) {
		array_push($func_unique_devices, $row['mon_MAC']);
	}
	$func_unique_devices = array_values(array_unique(array_filter($func_unique_devices)));
	return $func_unique_devices;
}

// -----------------------------------------------------------------------------
// Print a list of all monitored URLs of an unique device
function get_service_from_unique_device($func_unique_device) {
	global $pia_lang;
	global $http_status_code;
	global $db;

	$mon_res = $db->query('SELECT * FROM Services ORDER BY mon_Tags ASC');
	// Print Services Loop
	while ($row = $mon_res->fetchArray()) {
		if ($row['mon_MAC'] == $func_unique_device) {
			unset($func_httpcodes);

			$notification_type = get_notifications($row['mon_AlertDown'], $row['mon_AlertEvents']);
			$code_icon_color = get_icon_color($row['mon_LastStatus']);
			$url_array = explode('://', $row['mon_URL']);

			if ($http_status_code[$row['mon_LastStatus']] != "") {
				$status_description = $http_status_code[$row['mon_LastStatus']]['description'];
			} else {
				$status_description = 'No status code was received from the server. The server may be offline or the network could have a problem.';
			}

			echo '<div class="servicelist_entry">
                    <div class="' . $code_icon_color . ' servicebox_httpstat_hover" data-toggle="tooltip" data-placement="top" title="' . $status_description . '">
                        <div class="servicebox_box">
                            <div style="display: block; margin-top:5px;"><span class="servicebox_box_prot">' . strtoupper($url_array[0]) . '</span></div>
                            <div style="display: block;"><span class="servicebox_box_code">' . $row['mon_LastStatus'] . '</span></div>
                            <i class="fa fa-globe" style="position: relative; top: -58px; left: 0px; font-size: 58px; opacity: 0.2;"></i>
                        </div>
                    </div>
                    <div class="servicebox_text">
                        <div class="servicebox_text_m">
                             <table height="20px" width="100%"><tr><td><a href="serviceDetails.php?url=' . $row['mon_URL'] . '"><span class="">' . $url_array[1] . '</span></a></td><td align="right"><span class="servicebox_text_tag">&nbsp;' . $row['mon_Tags'] . '</span></td></tr></table>';
			// Render Progressbar
			echo '                <div class="progress-segment">';

			// Get Tooltip values
			$func_scans = get_latest_scans_from_url($row['mon_URL']);
			$func_httpcodes = get_latest_statuscodes_from_url($row['mon_URL']);
			$func_latency = get_latest_latency_from_url($row['mon_URL']);

			for ($x = 0; $x < 18; $x++) {
				unset($codecolor);
				$for_httpcode = $func_httpcodes[$x];
				if ($for_httpcode >= 200 && $for_httpcode < 300) {$codecolor = "bg-green";}
				if ($for_httpcode >= 300 && $for_httpcode < 500) {$codecolor = "bg-yellow";}
				if ($for_httpcode >= 500 && $for_httpcode < 600) {$codecolor = "bg-orange-custom";}
				if ($for_httpcode == "0") {$codecolor = "bg-red";}

				if ($func_latency[$x] == '99999999') {$loop_latency = 'offline';} else { $loop_latency = $func_latency[$x] . 's';}

				echo '       <div class="single_scan ' . $codecolor . '" title="' . $func_scans[$x] . ' / HTTP: ' . $for_httpcode . ' / Latency: ' . $loop_latency . '"></div>';

			}

			echo '        </div>';
			echo '              <table height="20px" width="100%"><tr><td><span class="progress-description">IP: ' . $row['mon_TargetIP'] . '</span></td><td align="right">' . $notification_type . '</td></tr></table>
                        </div>
                    </div>
                  </div>';
		}
	}
}

?>
<!-- Page ------------------------------------------------------------------ -->

<link rel="stylesheet" href="lib/AdminLTE/plugins/iCheck/all.css">
<div class="content-wrapper">

<!-- Content header--------------------------------------------------------- -->
    <section class="content-header">
    <?php require 'php/templates/notification.php';?>
      <h1 id="pageTitle">
         <?=$pia_lang['WebServices_Title'];?>
      <button type="button" class="btn btn-xs btn-success servicelist_add_serv" data-toggle="modal" data-target="#modal-add-monitoringURL"><i class="bi bi-plus-lg" style="font-size:1.5rem"></i></button>
      </h1>

<!-- Modals New URL ----------------------------------------------------------------- -->
        <form role="form">
            <div class="modal fade" id="modal-add-monitoringURL">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                            <h4 class="modal-title"><?=$pia_lang['WebServices_headline_NewService'];?></h4>
                        </div>
                        <div class="modal-body">
                            <div style="height: 230px;">
                            <div class="form-group col-xs-12">
                              <label class="col-xs-3 control-label"><?=$pia_lang['WebServices_label_URL'];?></label>
                              <div class="col-xs-9">
                                <input type="text" class="form-control" id="serviceURL" placeholder="Service URL">
                              </div>
                            </div>
                            <div class="form-group col-xs-12">
                              <label class="col-xs-3 control-label"><?=$pia_lang['WebServices_label_Tags'];?></label>
                              <div class="col-xs-9">
                                <input type="text" class="form-control" id="serviceTag" placeholder="Tag">
                              </div>
                            </div>
                              <div class="form-group col-xs-12">
                                <label class="col-xs-3 control-label"><?=$pia_lang['WebServices_label_MAC'];?></label>
                                <div class="col-xs-9">
                                  <div class="input-group">
                                    <div class="input-group-btn">
                                      <button type="button" class="btn btn-default dropdown-toggle black-tooltip" data-toggle="dropdown" aria-expanded="false"><?=$pia_lang['WebServices_label_MAC_Select'];?>
                                        <span class="fa fa-caret-down"></span></button>
                                      <ul class="dropdown-menu">
                                        <?php getDeviceMacs();?>
                                      </ul>
                                    </div>
                                    <!-- /btn-group -->
                                    <input type="text" id="serviceMAC" class="form-control" data-enpassusermodified="yes">
                                  </div>
                                </div>
                              </div>
                            <div class="form-group col-xs-12">
                                <label class="col-xs-3 control-label"><?=$pia_lang['WebServices_label_AlertEvents'];?></label>
                                <div class="col-xs-9" style="margin-top: 0px;">
                                  <input class="checkbox blue" id="insAlertEvents" type="checkbox">
                                </div>
                            </div>
                            <div class="form-group col-xs-12">
                                <label class="col-xs-3 control-label"><?=$pia_lang['WebServices_label_AlertDown'];?></label>
                                <div class="col-xs-9" style="margin-top: 0px;">
                                  <input class="checkbox red" id="insAlertDown" type="checkbox">
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?=$pia_lang['Gen_Close'];?></button>
                            <button type="button" class="btn btn-primary" id="btnInsert" onclick="insertNewService()" ><?=$pia_lang['Gen_Save'];?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </section>

    <!-- Main content ---------------------------------------------------------- -->
    <section class="content">
<?php

// ===============================================================================
// Start rendering page data
// ===============================================================================

// Load http status code in array
$http_status_code = open_http_status_code_json();
// Get a array of device with monitored URLs
$unique_devices = get_devices_from_services();

// #######################################################
// Main Function (Unique Devices)
// #######################################################
// Print a Box for every unique Device (MAC Address)
$i = 0;
while ($i < count($unique_devices)) {
	$device_name = get_device_name($unique_devices[$i]);
	if ($device_name == "") {$device_name = $pia_lang['WebServices_unknown_Device'] . ' (' . $unique_devices[$i] . ')';}
	echo '<div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">' . $device_name . '</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">';

	get_service_from_unique_device($unique_devices[$i]);

	echo '  <!-- /.box-body -->
            </div>
          </div>';

	echo '<br>';
	$i++;
}

// #######################################################
// Main Function (Standalone)
// #######################################################

// Get counter of standalone services
$count_standalone = get_count_standalone_services();

// Print a Box for all Device without MAC Address
if ($count_standalone > 0) {
	list_standalone_services();
}

// ===============================================================================
// End rendering page data
// ===============================================================================
?>

    <div style="width: 100%; height: 20px;"></div>
    <!-- ----------------------------------------------------------------------- -->

    </section>

    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<!-- ----------------------------------------------------------------------- -->
<?php
require 'php/templates/footer.php';
?>

<script src="lib/AdminLTE/plugins/iCheck/icheck.min.js"></script>
<link rel="stylesheet" href="lib/AdminLTE/plugins/iCheck/all.css">
<script>

$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

initializeiCheck();

// -----------------------------------------------------------------------------
function initializeiCheck () {
   // Blue
   $('input[type="checkbox"].blue').iCheck({
     checkboxClass: 'icheckbox_flat-blue',
     radioClass:    'iradio_flat-blue',
     increaseArea:  '20%'
   });
  // Orange
  $('input[type="checkbox"].orange').iCheck({
    checkboxClass: 'icheckbox_flat-orange',
    radioClass:    'iradio_flat-orange',
    increaseArea:  '20%'
  });
  // Red
  $('input[type="checkbox"].red').iCheck({
    checkboxClass: 'icheckbox_flat-red',
    radioClass:    'iradio_flat-red',
    increaseArea:  '20%'
  });
}

// -----------------------------------------------------------------------------
function insertNewService(refreshCallback='') {
  // Check URL
  if ($('#serviceURL').val() == '') {
    return;
  }

  // update data to server
  $.get('php/server/services.php?action=insertNewService'
    + '&url='             + $('#serviceURL').val()
    + '&tags='            + $('#serviceTag').val()
    + '&mac='             + $('#serviceMAC').val()
    + '&alertdown='       + ($('#insAlertEvents')[0].checked * 1)
    + '&alertevents='     + ($('#insAlertDown')[0].checked * 1)
    , function(msg) {
    showMessage (msg);
    // Callback fuction
    if (typeof refreshCallback == 'function') {
      refreshCallback();
    }
  });
}

// -----------------------------------------------------------------------------
function setTextValue (textElement, textValue) {
  $('#'+textElement).val (textValue);
}

</script>
