<?php
//------------------------------------------------------------------------------
//  Pi.Alert
//  Open Source Network Guard / WIFI & LAN intrusion detector 
//
//  services.php - Front module. Server side. Manage Devices
//------------------------------------------------------------------------------
//  leiweibau  2023        https://github.com/leiweibau     GNU GPLv3
//------------------------------------------------------------------------------

session_start();

// Turn off php errors
error_reporting(0);

if ($_SESSION["login"] != 1)
  {
      header('Location: /pialert/index.php');
      exit;
  }

require 'php/templates/header.php';
?>
<!-- Page ------------------------------------------------------------------ -->
<div class="content-wrapper">

<!-- Content header--------------------------------------------------------- -->
    <section class="content-header">
    <?php require 'php/templates/notification.php'; ?>
      <h1 id="pageTitle">
         <?php echo $pia_lang['WebServices_Title'];?>
      </h1>
    </section>

    <!-- Main content ---------------------------------------------------------- -->
    <section class="content">
<?php

$db_file = '../db/pialert.db';
$db = new SQLite3($db_file);
$mon_res = $db->query('SELECT * FROM Services');
$dev_res = $db->query('SELECT * FROM Devices');

// Get Name from Devices
function get_device_name($db_resource, $service_MAC) {
    while ($row = $db_resource->fetchArray()) {
        if ($row['dev_MAC'] == $service_MAC) {
            return $row['dev_Name'];
        }
    }
}

// Print a list of all monitored URLs
function list_all_services($db_resource) {
    while ($row = $db_resource->fetchArray()) {
        echo $row['mon_URL'].' - '.$row['mon_MAC'].' - '.$row['mon_TargetIP'].'<br>';
    }
}

// Print a list of all monitored URLs without a MAC Adresse
function list_standalone_services($db_resource) {
    echo '<div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">General</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">';

    while ($row = $db_resource->fetchArray()) {
        if ($row['mon_MAC'] == "") {
            echo $row['mon_URL'].' - '.$row['mon_TargetIP'].'<br>';
        }
    }

    echo '  <!-- /.box-body -->
            </div>
          </div>';
}

// Get a array of unique devices with monitored URLs
function get_devices_from_services($db_resource) {
    $func_unique_devices = array();
    while ($row = $db_resource->fetchArray()) {
        array_push($func_unique_devices, $row['mon_MAC']);
    }
    $func_unique_devices = array_unique(array_values(array_filter($func_unique_devices)));
    return $func_unique_devices;
}

// Print a list of all monitored URLs of an unique device
function get_service_from_unique_device($db_resource, $func_unique_device) {
    $func_unique_devices = array();
    while ($row = $db_resource->fetchArray()) {
        if ($row['mon_MAC'] == $func_unique_device) {
            echo $func_unique_device.' - '.$row['mon_URL'].'<br>';
        }
    }
}

// Get a array of device with monitored URLs
$unique_devices = get_devices_from_services($mon_res);

// print_r($unique_devices);
$i = 0;
while($i < count($unique_devices))
{
    $device_name = get_device_name($dev_res, $unique_devices[$i]);
    if ($device_name == "") {$device_name = 'Unbekanntes Gerät';}
    echo '<div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">'.$device_name.'</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">';

    get_service_from_unique_device($mon_res, $unique_devices[$i]);

    echo '  <!-- /.box-body -->
            </div>
          </div>';

    echo '<br>';
    $i++;
}

list_standalone_services($mon_res);

//echo sizeof($unique_devices);

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

<script>

</script>


