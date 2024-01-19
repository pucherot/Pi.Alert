<!-- ---------------------------------------------------------------------------
#  Pi.Alert
#  Open Source Network Guard / WIFI & LAN intrusion detector
#
#  network.php - Front module. network relationship
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
require 'php/server/db.php';
require 'php/server/journal.php';

$DBFILE = '../db/pialert.db';
OpenDB();

// Add New Network Devices
if ($_REQUEST['Networkinsert'] == "yes") {
	if (isset($_REQUEST['NetworkDeviceName']) && isset($_REQUEST['NetworkDeviceTyp'])) {
		$sql = 'INSERT INTO "network_infrastructure" ("net_device_name", "net_device_typ", "net_device_port") VALUES("' . $_REQUEST['NetworkDeviceName'] . '", "' . $_REQUEST['NetworkDeviceTyp'] . '", "' . $_REQUEST['NetworkDevicePort'] . '")';
		$result = $db->query($sql);
		// Logging
		pialert_logging('a_040', $_SERVER['REMOTE_ADDR'], 'LogStr_0030', '', '');
	}
}
// Edit Network Devices
if ($_REQUEST['Networkedit'] == "yes") {
	if (($_REQUEST['NewNetworkDeviceName'] != "") && isset($_REQUEST['NewNetworkDeviceTyp'])) {
		$sql = 'UPDATE "network_infrastructure" SET "net_device_name" = "' . $_REQUEST['NewNetworkDeviceName'] . '", "net_device_typ" = "' . $_REQUEST['NewNetworkDeviceTyp'] . '", "net_device_port" = "' . $_REQUEST['NewNetworkDevicePort'] . '", "net_downstream_devices" = "' . $_REQUEST['NetworkDeviceDownlink'] . '" WHERE "device_id"="' . $_REQUEST['NetworkDeviceID'] . '"';
		$result = $db->query($sql);
	}

	if (($_REQUEST['NewNetworkDeviceName'] == "") && isset($_REQUEST['NewNetworkDeviceTyp'])) {
		$sql = 'UPDATE "network_infrastructure" SET "net_device_typ" = "' . $_REQUEST['NewNetworkDeviceTyp'] . '", "net_device_port" = "' . $_REQUEST['NewNetworkDevicePort'] . '", "net_downstream_devices" = "' . $_REQUEST['NetworkDeviceDownlink'] . '" WHERE "device_id "="' . $_REQUEST['NetworkDeviceID'] . '"';
		$result = $db->query($sql);
	}
	// Logging
	pialert_logging('a_040', $_SERVER['REMOTE_ADDR'], 'LogStr_0031', '', '');

}
// remove Network Devices
if ($_REQUEST['Networkdelete'] == "yes") {
	if (isset($_REQUEST['NetworkDeviceID'])) {
		$sql = 'DELETE FROM "network_infrastructure" WHERE "device_id"="' . $_REQUEST['NetworkDeviceID'] . '"';
		$result = $db->query($sql);
	}
	// Logging
	pialert_logging('a_040', $_SERVER['REMOTE_ADDR'], 'LogStr_0032', '', '');
}

// Add New unmanaged Device
if ($_REQUEST['NetworkUnmanagedDevinsert'] == "yes") {
	if (isset($_REQUEST['NetworkUnmanagedDevName']) && isset($_REQUEST['NetworkUnmanagedDevConnect'])) {
		$ip = 'Unmanaged';
		$dumbvar = 'dumb';
		$sql = 'INSERT INTO "network_dumb_dev" ("dev_Name", "dev_MAC", "dev_Infrastructure", "dev_Infrastructure_port", "dev_PresentLastScan", "dev_LastIP") VALUES("' . $_REQUEST['NetworkUnmanagedDevName'] . '", "' . $dumbvar . '", "' . $_REQUEST['NetworkUnmanagedDevConnect'] . '", "' . $_REQUEST['NetworkUnmanagedDevPort'] . '", "' . $dumbvar . '", "' . $ip . '")';
		$result = $db->query($sql);
		// Logging
		pialert_logging('a_040', $_SERVER['REMOTE_ADDR'], 'LogStr_0033', '', '');
	}
}
// Edit  unmanaged Device
if ($_REQUEST['NetworkUnmanagedDevedit'] == "yes") {
	if (($_REQUEST['NewNetworkUnmanagedDevName'] != "") && isset($_REQUEST['NewNetworkUnmanagedDevConnect']) && isset($_REQUEST['NetworkUnmanagedDevID'])) {
		$sql = 'UPDATE "network_dumb_dev" SET "dev_Name" = "' . $_REQUEST['NewNetworkUnmanagedDevName'] . '", "dev_Infrastructure" = "' . $_REQUEST['NewNetworkUnmanagedDevConnect'] . '", "dev_Infrastructure_port" = "' . $_REQUEST['NewNetworkUnmanagedDevPort'] . '" WHERE "id "="' . $_REQUEST['NetworkUnmanagedDevID'] . '"';
		$result = $db->query($sql);
	}

	if (($_REQUEST['NewNetworkUnmanagedDevName'] == "") && isset($_REQUEST['NewNetworkUnmanagedDevConnect']) && isset($_REQUEST['NetworkUnmanagedDevID'])) {
		$sql = 'UPDATE "network_dumb_dev" SET "dev_Infrastructure" = "' . $_REQUEST['NewNetworkUnmanagedDevConnect'] . '", "dev_Infrastructure_port" = "' . $_REQUEST['NewNetworkUnmanagedDevPort'] . '" WHERE "id"="' . $_REQUEST['NetworkUnmanagedDevID'] . '"';
		$result = $db->query($sql);
	}
	// Logging
	pialert_logging('a_040', $_SERVER['REMOTE_ADDR'], 'LogStr_0034', '', '');
}
// remove unmanaged Device
if ($_REQUEST['NetworkUnmanagedDevdelete'] == "yes") {
	if (isset($_REQUEST['NetworkUnmanagedDevID'])) {
		$sql = 'DELETE FROM "network_dumb_dev" WHERE "id"="' . $_REQUEST['NetworkUnmanagedDevID'] . '"';
		$result = $db->query($sql);
		// Logging
		pialert_logging('a_040', $_SERVER['REMOTE_ADDR'], 'LogStr_0035', '', '');
	}
}

?>

<div class="content-wrapper">

    <section class="content-header">
    <?php require 'php/templates/notification.php';?>
      <h1 id="pageTitle">
         <?=$pia_lang['NetworkSettings_Title'];?>
         <a href="./network.php" class="btn btn-success pull-right" role="button" style="position: absolute; display: inline-block; top: 5px; right: 15px;"><?=$pia_lang['Gen_Close'];?></a>
      </h1>
    </section>

    <section class="content">

    <!-- Manage Devices ---------------------------------------------------------- -->
		<div class="box "> <!-- collapsed-box -->
        <div class="box-header">
          <h3 class="box-title" id="netedit"><?=$pia_lang['Network_ManageDevices'];?></h3>
        </div>
        <!-- /.box-header -->

        <div class="box-body" style="">
          <p><?=$pia_lang['Network_ManageDevices_Intro'];?></p>
          <div class="row">
            <!-- Add Device ---------------------------------------------------------- -->
            <div class="col-md-4">
            <h4 class="box-title"><?=$pia_lang['Network_ManageAdd'];?></h4>
            <form role="form" method="post" action="./networkSettings.php">
              <div class="form-group has-success">
                  <label for="NetworkDeviceName"><?=$pia_lang['Network_ManageAdd_Name'];?>:</label>
                  <div class="input-group">
                      <input class="form-control" id="txtNetworkNodeMac" name="NetworkDeviceName" type="text" placeholder="<?=$pia_lang['Network_ManageAdd_Name_text'];?>">
                          <div class="input-group-btn">
                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id="buttonNetworkNodeMac">
                                <span class="fa fa-caret-down"></span>
                            </button>
                            <ul id="dropdownNetworkNodeMac" class="dropdown-menu dropdown-menu-right">
                              <li class="divider"></li>
<?php
network_infrastructurelist();
?>
                            </ul>
                          </div>
                  </div>
              </div>
              <!-- /.form-group -->
              <div class="form-group has-success">
               <label><?=$pia_lang['Network_ManageAdd_Type'];?>:</label>
                  <select class="form-control" name="NetworkDeviceTyp">
                    <option value=""><?=$pia_lang['Network_ManageAdd_Type_text'];?></option>
                    <option value="0_Internet">Internet</option>
                    <option value="1_Router">Router</option>
                    <option value="2_Switch">Switch</option>
                    <option value="3_WLAN">WLAN</option>
                    <option value="4_Powerline">Powerline</option>
                    <option value="5_Hypervisor">Hypervisor</option>
                  </select>
              </div>
              <div class="form-group has-success">
                <label for="NetworkDevicePort"><?=$pia_lang['Network_ManageAdd_Port'];?>:</label>
                <input type="text" class="form-control" id="NetworkDevicePort" name="NetworkDevicePort" placeholder="<?=$pia_lang['Network_ManageAdd_Port_text'];?>">
              </div>
              <div class="form-group">
              <button type="submit" class="btn btn-success" name="Networkinsert" value="yes"><?=$pia_lang['Network_ManageAdd_Submit'];?></button>
          	  </div>
          </form>
              <!-- /.form-group -->
            </div>
            <!-- /.col -->
            <!-- Edit Device ---------------------------------------------------------- -->
            <div class="col-md-4">
              <h4 class="box-title"><?=$pia_lang['Network_ManageEdit'];?></h4>
              <form role="form" method="post" action="./networkSettings.php">
              <div class="form-group has-warning">
              	<label><?=$pia_lang['Network_ManageEdit_ID'];?>:</label>
                  <select class="form-control" name="NetworkDeviceID" onchange="get_networkdev_values(event)">
                    <option value=""><?=$pia_lang['Network_ManageEdit_ID_text'];?></option>
<?php
$sql = 'SELECT "device_id", "net_device_name", "net_device_typ", "net_device_port", "net_downstream_devices" FROM "network_infrastructure" ORDER BY "net_device_typ" ASC';
$result = $db->query($sql); //->fetchArray(SQLITE3_ASSOC);
$netdev_all_ids = array();
while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
	if (!isset($res['device_id'])) {
		continue;
	}
	$temp_name = "netdev_id_" . $res['device_id'];
	echo '<option value="' . $res['device_id'] . '">' . $res['net_device_name'] . ' / ' . substr($res['net_device_typ'], 2) . '</option>';

	$$temp_name = array();
	array_push($netdev_all_ids, $temp_name);
	$$temp_name[0] = $res['device_id'];
	$$temp_name[1] = $res['net_device_name'];
	$$temp_name[2] = $res['net_device_typ'];
	$$temp_name[3] = $res['net_downstream_devices'];
	$$temp_name[4] = $res['net_device_port'];
}
?>
                  </select>
              </div>
<!-- Autofill "Edit" Input fields ---------------------------------------------------------- -->
<script>
function get_networkdev_values(event) {
    var selectElement = event.target;
    var value = 'netdev_id_' + selectElement.value;

<?php
foreach ($netdev_all_ids as $key => $value) {
	echo '    const ' . $value . ' = ["' . $$value[0] . '", "' . $$value[1] . '", "' . $$value[2] . '" , "' . $$value[3] . '", "' . $$value[4] . '"];';
	echo "\n";
}

echo '    var netdev_arrays = {';
echo "\n";
foreach ($netdev_all_ids as $key => $value) {
	echo '        "' . $value . '":' . $value . ',';
	echo "\n";
}
echo '    };';
?>
    var netdev_name = netdev_arrays[value][1];
    $('#NewNetworkDeviceName').val(netdev_name);
    var netdev_type = netdev_arrays[value][2];
    $('#NewNetworkDeviceTyp').val(netdev_type);
    var port_config = netdev_arrays[value][3];
    $('#txtNetworkDeviceDownlinkMac').val(port_config);
    var port_count = netdev_arrays[value][4];
    $('#NewNetworkDevicePort').val(port_count);
};
</script>
              <div class="form-group has-warning">
                <label for="NetworkDeviceName"><?=$pia_lang['Network_ManageEdit_Name'];?>:</label>
                <input type="text" class="form-control" id="NewNetworkDeviceName" name="NewNetworkDeviceName" placeholder="<?=$pia_lang['Network_ManageEdit_Name_text'];?>">
              </div>
              <div class="form-group has-warning">
               <label><?=$pia_lang['Network_ManageEdit_Type'];?>:</label>
                  <select class="form-control" name="NewNetworkDeviceTyp" id="NewNetworkDeviceTyp">
                    <option value=""><?=$pia_lang['Network_ManageEdit_Type_text'];?></option>
                    <option value="0_Internet">Internet</option>
                    <option value="1_Router">Router</option>
                    <option value="2_Switch">Switch</option>
                    <option value="3_WLAN">WLAN</option>
                    <option value="4_Powerline">Powerline</option>
                  </select>
              </div>
              <div class="form-group has-warning">
                <label for="NetworkDevicePort"><?=$pia_lang['Network_ManageEdit_Port'];?>:</label>
                <input type="text" class="form-control" id="NewNetworkDevicePort" name="NewNetworkDevicePort" placeholder="<?=$pia_lang['Network_ManageEdit_Port_text'];?>">
              </div>
              <div class="form-group has-warning">
                  <label for="NetworkDeviceDownlink"><?=$pia_lang['Network_ManageEdit_Downlink'];?>:</label>
                  <div class="input-group">
                      <input class="form-control" id="txtNetworkDeviceDownlinkMac" name="NetworkDeviceDownlink" type="text" placeholder="<?=$pia_lang['Network_ManageEdit_Downlink_text'];?>">
                          <div class="input-group-btn">
                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id="buttonNetworkDeviceDownlinkMac">
                                <span class="fa fa-caret-down"></span>
                            </button>
                            <ul id="dropdownNetworkDeviceDownlinkMac" class="dropdown-menu dropdown-menu-right">
                              <li class="divider"></li>
<?php
//network_infrastructurelist();
network_device_downlink_mac();
?>
                            </ul>
                          </div>
                  </div>
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-warning" name="Networkedit" value="yes"><?=$pia_lang['Network_ManageEdit_Submit'];?></button>
              </div>
         	 </form>
              <!-- /.form-group -->
            </div>
            <!-- /.col -->
            <!-- Del Device ---------------------------------------------------------- -->
           <div class="col-md-4">
            <h4 class="box-title"><?=$pia_lang['Network_ManageDel'];?></h4>
              <form role="form" method="post" action="./networkSettings.php">
              <div class="form-group has-error">
                <label><?=$pia_lang['Network_ManageDel_Name'];?>:</label>
                  <select class="form-control" name="NetworkDeviceID">
                    <option value=""><?=$pia_lang['Network_ManageDel_Name_text'];?></option>
<?php
$sql = 'SELECT "device_id", "net_device_name", "net_device_typ" FROM "network_infrastructure" ORDER BY "net_device_typ" ASC';
$result = $db->query($sql); //->fetchArray(SQLITE3_ASSOC);
while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
	if (!isset($res['device_id'])) {
		continue;
	}
	echo '<option value="' . $res['device_id'] . '">' . $res['net_device_name'] . ' / ' . substr($res['net_device_typ'], 2) . '</option>';
}
?>
                  </select>
              </div>
              <!-- /.form-group -->
              <div class="form-group">
                <button type="submit" class="btn btn-danger" name="Networkdelete" value="yes"><?=$pia_lang['Network_ManageDel_Submit'];?></button>
              </div>
           </form>
              <!-- /.form-group -->
            </div>
          </div>
          <!-- /.row -->
        </div>
        <!-- /.box-body -->
      </div>

    <div class="box ">
        <div class="box-header">
          <h3 class="box-title" id="hostedit"><?=$pia_lang['Network_Unmanaged_Devices'];?></h3>
        </div>
        <!-- /.box-header -->

        <div class="box-body" style="">
          <p><?=$pia_lang['Network_Unmanaged_Devices_Intro'];?></p>
          <div class="row">
            <!-- Add Device ---------------------------------------------------------- -->
            <div class="col-md-4">
            <h4 class="box-title"><?=$pia_lang['Network_ManageAdd'];?></h4>
            <form role="form" method="post" action="./networkSettings.php">
              <!-- /.form-group -->
              <div class="form-group has-success">
                <label for="NetworkUnmanagedDevName"><?=$pia_lang['Network_ManageAdd_Name'];?>:</label>
                <input type="text" class="form-control" id="NetworkUnmanagedDevName" name="NetworkUnmanagedDevName" placeholder="<?=$pia_lang['Network_ManageAdd_Name_text'];?>">
              </div>

              <div class="form-group has-success">
                <label><?=$pia_lang['Network_Unmanaged_Devices_Connected'];?>:</label>
                  <select class="form-control" name="NetworkUnmanagedDevConnect">
                    <option value=""><?=$pia_lang['Network_Unmanaged_Devices_Connected_text'];?></option>
<?php
$sql = 'SELECT "device_id", "net_device_name", "net_device_typ", "net_device_port", "net_downstream_devices" FROM "network_infrastructure" ORDER BY "net_device_typ" ASC';
$result = $db->query($sql); //->fetchArray(SQLITE3_ASSOC);
$netdev_all_ids = array();
while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
	if (!isset($res['device_id'])) {
		continue;
	}
	echo '<option value="' . $res['device_id'] . '">' . $res['net_device_name'] . ' / ' . substr($res['net_device_typ'], 2) . '</option>';
}
?>
                  </select>
              </div>
              <div class="form-group has-success">
                <label for="NetworkUnmanagedDevPort"><?=$pia_lang['Network_Unmanaged_Devices_Port'];?>:</label>
                <input type="text" class="form-control" id="NetworkUnmanagedDevPort" name="NetworkUnmanagedDevPort" placeholder="<?=$pia_lang['Network_Unmanaged_Devices_Port_text'];?>">
              </div>
              <div class="form-group">
              <button type="submit" class="btn btn-success" name="NetworkUnmanagedDevinsert" value="yes"><?=$pia_lang['Network_ManageAdd_Submit'];?></button>
              </div>
          </form>
              <!-- /.form-group -->
            </div>
            <!-- /.col -->
            <!-- Edit Device ---------------------------------------------------------- -->
            <div class="col-md-4">
              <h4 class="box-title"><?=$pia_lang['Network_ManageEdit'];?></h4>
              <form role="form" method="post" action="./networkSettings.php">
              <div class="form-group has-warning">
                <label><?=$pia_lang['Network_ManageEdit_ID'];?>:</label>
                  <select class="form-control" name="NetworkUnmanagedDevID">
                    <option value=""><?=$pia_lang['Network_ManageEdit_ID_text'];?></option>
<?php
$sql = 'SELECT * FROM "network_dumb_dev" ORDER BY "dev_Name" ASC';
$result = $db->query($sql); //->fetchArray(SQLITE3_ASSOC);
$netdev_all_ids = array();
while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
	if (!isset($res['id'])) {
		continue;
	}
	echo '<option value="' . $res['id'] . '">' . $res['dev_Name'] . '</option>';
}
?>
                  </select>
              </div>
              <div class="form-group has-warning">
                <label for="NewNetworkUnmanagedDevName"><?=$pia_lang['Network_ManageEdit_Name'];?>:</label>
                <input type="text" class="form-control" id="NewNetworkUnmanagedDevName" name="NewNetworkUnmanagedDevName" placeholder="<?=$pia_lang['Network_ManageEdit_Name_text'];?>">
              </div>

              <div class="form-group has-warning">
                <label><?=$pia_lang['Network_Unmanaged_Devices_Connected'];?>:</label>
                  <select class="form-control" name="NewNetworkUnmanagedDevConnect">
                    <option value=""><?=$pia_lang['Network_Unmanaged_Devices_Connected_text'];?></option>
<?php
$sql = 'SELECT "device_id", "net_device_name", "net_device_typ", "net_device_port", "net_downstream_devices" FROM "network_infrastructure" ORDER BY "net_device_typ" ASC';
$result = $db->query($sql); //->fetchArray(SQLITE3_ASSOC);
$netdev_all_ids = array();
while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
	if (!isset($res['device_id'])) {
		continue;
	}
	echo '<option value="' . $res['device_id'] . '">' . $res['net_device_name'] . ' / ' . substr($res['net_device_typ'], 2) . '</option>';
}
?>
                  </select>
              </div>
              <div class="form-group has-warning">
                <label for="NetworkDevicePort"><?=$pia_lang['Network_Unmanaged_Devices_Port'];?>:</label>
                <input type="text" class="form-control" id="NewNetworkUnmanagedDevPort" name="NewNetworkUnmanagedDevPort" placeholder="<?=$pia_lang['Network_Unmanaged_Devices_Port_text'];?>">
              </div>

              <div class="form-group">
                <button type="submit" class="btn btn-warning" name="NetworkUnmanagedDevedit" value="yes"><?=$pia_lang['Network_ManageEdit_Submit'];?></button>
              </div>
           </form>
              <!-- /.form-group -->
            </div>
            <!-- /.col -->
            <!-- Del Device ---------------------------------------------------------- -->
           <div class="col-md-4">
            <h4 class="box-title"><?=$pia_lang['Network_ManageDel'];?></h4>
              <form role="form" method="post" action="./networkSettings.php">
              <div class="form-group has-error">
                <label><?=$pia_lang['Network_ManageDel_Name'];?>:</label>
                  <select class="form-control" name="NetworkUnmanagedDevID">
                    <option value=""><?=$pia_lang['Network_ManageDel_Name_text'];?></option>
<?php
$sql = 'SELECT "id", "dev_Name" FROM "network_dumb_dev" ORDER BY "dev_Name" ASC';
$result = $db->query($sql); //->fetchArray(SQLITE3_ASSOC);
while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
	if (!isset($res['id'])) {
		continue;
	}

	echo '<option value="' . $res['id'] . '">' . $res['dev_Name'] . '</option>';
}
?>
                  </select>
              </div>
              <!-- /.form-group -->
              <div class="form-group">
                <button type="submit" class="btn btn-danger" name="NetworkUnmanagedDevdelete" value="yes"><?=$pia_lang['Network_ManageDel_Submit'];?></button>
              </div>
           </form>
              <!-- /.form-group -->
            </div>
          </div>
          <!-- /.row -->
        </div>
        <!-- /.box-body -->
      </div>

<script>
function setTextValue (textElement, textValue) {
  $('#'+textElement).val(textValue);
}
function appendTextValue(textElement, textValue) {
  var existingText = $('#' + textElement).val();
        $('#' + textElement).val(existingText + textValue);
    }
</script>

<?php
// #####################################
// ## Start Function Setup
// #####################################
function network_infrastructurelist() {
	global $db;
	$func_sql = 'SELECT * FROM "Devices" WHERE "dev_DeviceType" IN ("Router", "Switch", "AP", "Access Point", "Hypervisor") OR "dev_MAC" = "Internet"';

	$func_result = $db->query($func_sql); //->fetchArray(SQLITE3_ASSOC);
	while ($func_res = $func_result->fetchArray(SQLITE3_ASSOC)) {
		echo '<li><a href="javascript:void(0)" onclick="setTextValue(\'txtNetworkNodeMac\',\'' . $func_res['dev_Name'] . '\')">' . $func_res['dev_Name'] . '/' . $func_res['dev_DeviceType'] . '</a></li>';
	}
}

function network_device_downlink_mac() {
	global $db;
	$func_sql = 'SELECT * FROM "Devices" WHERE "dev_DeviceType" IN ("Router", "Switch", "AP", "Access Point") OR "dev_MAC" = "Internet"';
	$func_result = $db->query($func_sql); //->fetchArray(SQLITE3_ASSOC);
	while ($func_res = $func_result->fetchArray(SQLITE3_ASSOC)) {
		echo '<li><a href="javascript:void(0)" onclick="appendTextValue(\'txtNetworkDeviceDownlinkMac\',\'' . $func_res['dev_MAC'] . ',\')">' . $func_res['dev_Name'] . '</a></li>';
	}
}
// #####################################
// ## End Function Setup
// #####################################
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