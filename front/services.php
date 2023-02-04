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

<style type="text/css">

.progress-segment {
  display: flex;
  margin-bottom: 5px;
  margin-top: 5px;
}

.item {
  width: 100%;
  background-color: lightgray;
  margin-right: 2px;
  height: 12px;

  &:first-child {
    border-top-left-radius: 3px;
    border-bottom-left-radius: 3px;
  }

  &:last-child {
    border-top-right-radius: 3px;
    border-bottom-right-radius: 3px;
  }
}

.orange-common {
    background-color: #f04500 !important;
  }

.item:hover:after {
    position: absolute;
    display: flex;
    content: attr(title);
    left: 0px;
    top: 0px;
    padding: 5px;
    background-color: #222;
    font-size: 16px;
    color: white;
    width: 100%;
    height: 38px;
}

</style>

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

// ===============================================================================
// Start prepare data
// ===============================================================================

$db_file = '../db/pialert.db';
// $db = new SQLite3($db_file);
// $mon_res = $db->query('SELECT * FROM Services');
// $dev_res = $db->query('SELECT * FROM Devices');
// $moneve_res = $db->query('SELECT * FROM Services_Events ORDER BY moneve_DateTime DESC');

// ===============================================================================
// End prepare data
// ===============================================================================

// -----------------------------------------------------------------------------------------------

// Get the latest 15 StatusCodes from a specific URL in order latest -> older
function get_latest_latency_from_url($service_URL) {
    global $db_file;
    unset($code_array, $i, $moneve_res);
    $db = new SQLite3($db_file);
    $moneve_res = $db->query('SELECT * FROM Services_Events ORDER BY moneve_DateTime DESC');
    $i = 0;
    $code_array = array();
    while ($row = $moneve_res->fetchArray()) {
        if ($row['moneve_URL'] == $service_URL) {
            //echo $row['moneve_StatusCode'];
            $code_array[17-$i] = $row['moneve_Latency'];
            //echo $i;
            $i++;
        }
        if ($i == 18) {break;}

    }
    $db->close();
    return $code_array;
}

// -----------------------------------------------------------------------------------------------

// Get the latest 15 StatusCodes from a specific URL in order latest -> older
function get_latest_statuscodes_from_url($service_URL) {
    global $db_file;
    unset($code_array, $i, $moneve_res);
    $db = new SQLite3($db_file);
    $moneve_res = $db->query('SELECT * FROM Services_Events ORDER BY moneve_DateTime DESC');
    $i = 0;
    $code_array = array();
    while ($row = $moneve_res->fetchArray()) {
        if ($row['moneve_URL'] == $service_URL) {
            //echo $row['moneve_StatusCode'];
            $code_array[17-$i] = $row['moneve_StatusCode'];
            //echo $i;
            $i++;
        }
        if ($i == 18) {break;}

    }
    $db->close();
    return $code_array;
}

// -----------------------------------------------------------------------------------------------

// Get the latest 15 StatusCodes from a specific URL in order latest -> older
function get_latest_scans_from_url($service_URL) {
    global $db_file;
    unset($code_array, $i, $moneve_res);
    $db = new SQLite3($db_file);
    $moneve_res = $db->query('SELECT * FROM Services_Events ORDER BY moneve_DateTime DESC');
    $i = 0;
    $code_array = array();
    while ($row = $moneve_res->fetchArray()) {
        if ($row['moneve_URL'] == $service_URL) {
            //echo $row['moneve_StatusCode'];
            $code_array[17-$i] = $row['moneve_DateTime'];
            //echo $i;
            $i++;
        }
        if ($i == 18) {break;}
    }
    $db->close();
    return $code_array;
}


// -----------------------------------------------------------------------------------------------

// Get Name from Devices
function get_device_name($service_MAC) {
    global $db_file;
    $db = new SQLite3($db_file);
    $dev_res = $db->query('SELECT * FROM Devices');
    while ($row = $dev_res->fetchArray()) {
        if ($row['dev_MAC'] == $service_MAC) {
            return $row['dev_Name'];
        }
    }
    $db->close();
}

// -----------------------------------------------------------------------------------------------

// Print a list of all monitored URLs
function list_all_services() {
    global $db_file;
    $db = new SQLite3($db_file);
    $mon_res = $db->query('SELECT * FROM Services');
    while ($row = $mon_res->fetchArray()) {
        echo $row['mon_URL'].' - '.$row['mon_MAC'].' - '.$row['mon_TargetIP'].'<br>';
    }
    $db->close();
}

// -----------------------------------------------------------------------------------------------

// get Count of all standalone services
function get_count_standalone_services() {
    global $db_file;
    $db = new SQLite3($db_file);
    $mon_res = $db->query('SELECT * FROM Services');
    $func_count = 0;
    while ($row = $mon_res->fetchArray()) {
        if ($row['mon_MAC'] == "") {$func_count++;}
    }
    $db->close();
    return $func_count;
}

// -----------------------------------------------------------------------------------------------

// Print a list of all monitored URLs without a MAC Adresse
function list_standalone_services() {
    global $db_file;
    $db = new SQLite3($db_file);
    $mon_res = $db->query('SELECT * FROM Services');
    // General Box for all Services without MAC
    echo '<div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">General</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">';

    // Print Services Loop
    while ($row = $mon_res->fetchArray()) {
        if ($row['mon_MAC'] == "") {
            if (substr($row['mon_LastStatus'],0,1) == "2") {$code_icon_color = "bg-green";}

            if ($row['mon_AlertEvents'] == "1") {$notification_type = "Alle Events";}
            elseif ($row['mon_AlertDown'] == "1") {$notification_type = "Down";}
            else {$notification_type = "keine";}

            if (substr($row['mon_LastStatus'],0,1) == "2") {$code_icon_color = "bg-green";}
            if (substr($row['mon_LastStatus'],0,1) == "3") {$code_icon_color = "bg-yellow";}
            if (substr($row['mon_LastStatus'],0,1) == "4") {$code_icon_color = "bg-yellow";}
            if (substr($row['mon_LastStatus'],0,1) == "5") {$code_icon_color = "orange-common";}
            if ($row['mon_LastLanency'] == "99999999") {$code_icon_color = "bg-red";}
            $url_array = explode('://', $row['mon_URL']);
            echo '<div style="display: flex; width: 100%; margin-bottom: 5px; margin-top: 5px;">
                    <div class="'.$code_icon_color.'" style="display: flex; width: 70px; height: 70px;">
                        <div style="width: 70px; height: 70px; text-align: center; padding-top: 5px;">
                            <span style="font-size:18px;">'.strtoupper($url_array[0]).'</span><br>
                            <span style="font-size:24px;">'.$row['mon_LastStatus'].'</span>
                        </div>
                    </div>
                    <div style="display: inline-block; width: 100%;">
                        <div style="margin: 0px 15px;">
                           <table height="20px" width="100%"><tr><td><span class="">'.$url_array[1].'</span></td><td align="right"><span style="font-weight: bolder; font-size:16px;">'.$row['mon_Tags'].'</span></td></tr></table>';
            echo'          <div class="progress-segment">';

            // Get Tooltip values
            $func_scans = get_latest_scans_from_url($row['mon_URL']);
            $func_httpcodes = get_latest_statuscodes_from_url($row['mon_URL']);
            $func_latency = get_latest_latency_from_url($row['mon_URL']);

                    for($x = 0; $x < 18; $x++) {
                        unset($codecolor);
                        $for_httpcode = $func_httpcodes[$x];
                        if ($for_httpcode >= 200 && $for_httpcode < 300) {$codecolor = "bg-green";}
                        if ($for_httpcode >= 300 && $for_httpcode < 400) {$codecolor = "bg-yellow";}
                        if ($for_httpcode >= 400 && $for_httpcode < 500) {$codecolor = "bg-yellow";}
                        if ($for_httpcode >= 500 && $for_httpcode < 500) {$codecolor = "orange-common";}
                        if ($for_httpcode >= 500 && $for_httpcode < 600) {$codecolor = "bg-red";}
                        echo '<div class="item '.$codecolor.'" title="'.$func_scans[$x].' / HTTP: '.$for_httpcode.' / Latency: '.$func_latency[$x].'s"></div>';

                    }

            echo '         </div>';
            echo '         <table height="20px" width="100%"><tr><td><span class="progress-description">IP: '.$row['mon_TargetIP'].'</span></td><td align="right">Meldung bei: '.$notification_type.'</td></tr></table>
                        </div>
                    </div>
                  </div>';
            // ###### Debugging
            // ##########################################
            // print_r($func_httpcodes);
            // echo '<br>';
            // print_r($func_scans);

        }
    }

    echo '  <!-- /.box-body -->
            </div>
          </div>';
    $db->close();
}

// -----------------------------------------------------------------------------------------------

// Get a array of unique devices with monitored URLs
function get_devices_from_services() {
    global $db_file;
    $db = new SQLite3($db_file);
    $mon_res = $db->query('SELECT * FROM Services');
    $func_unique_devices = array();
    while ($row = $mon_res->fetchArray()) {
        array_push($func_unique_devices, $row['mon_MAC']);
    }
    $func_unique_devices = array_values(array_unique(array_filter($func_unique_devices)));
    $db->close();
    return $func_unique_devices;
}
// ###### Debugging
// ##########################################
//print_r(get_devices_from_services($mon_res));

// -----------------------------------------------------------------------------------------------

// Print a list of all monitored URLs of an unique device
function get_service_from_unique_device($func_unique_device) {
    global $db_file;
    $db = new SQLite3($db_file);
    $mon_res = $db->query('SELECT * FROM Services');
    // Print Services Loop
    while ($row = $mon_res->fetchArray()) {
        if ($row['mon_MAC'] == $func_unique_device) {
            unset($func_httpcodes);
            if ($row['mon_AlertEvents'] == "1") {$notification_type = "Alle Events";}
            elseif ($row['mon_AlertDown'] == "1") {$notification_type = "Down";}
            else {$notification_type = "keine";}

            if (substr($row['mon_LastStatus'],0,1) == "2") {$code_icon_color = "bg-green";}
            if (substr($row['mon_LastStatus'],0,1) == "3") {$code_icon_color = "bg-yellow";}
            if (substr($row['mon_LastStatus'],0,1) == "4") {$code_icon_color = "bg-yellow";}
            if (substr($row['mon_LastStatus'],0,1) == "5") {$code_icon_color = "orange-common";}
            if ($row['mon_LastLanency'] == "99999999") {$code_icon_color = "bg-red";}
            $url_array = explode('://', $row['mon_URL']);
            echo '<div style="display: flex; width: 100%; margin-bottom: 5px; margin-top: 5px;">
                    <div class="'.$code_icon_color.'" style="display: flex; width: 70px; height: 70px;">
                        <div style="width: 70px; height: 70px; text-align: center; padding-top: 5px;">
                            <span style="font-size:18px;">'.strtoupper($url_array[0]).'</span><br>
                            <span style="font-size:24px;">'.$row['mon_LastStatus'].'</span>
                        </div>
                    </div>
                    <div style="display: inline-block; width: 100%;">
                        <div style="margin: 0px 15px;">
                                <table height="20px" width="100%"><tr><td><span class="">'.$url_array[1].'</span></td><td align="right"><span style="font-weight: bolder; font-size:16px;">'.$row['mon_Tags'].'</span></td></tr></table>';
            // Render Progressbar
                    echo'         <div class="progress-segment">';

            // Get Tooltip values
            $func_scans = get_latest_scans_from_url($row['mon_URL']);
            $func_httpcodes = get_latest_statuscodes_from_url($row['mon_URL']);
            $func_latency = get_latest_latency_from_url($row['mon_URL']);

                    for($x = 0; $x < 18; $x++) {
                        unset($codecolor);
                        $for_httpcode = $func_httpcodes[$x];
                        if ($for_httpcode >= 200 && $for_httpcode < 300) {$codecolor = "bg-green";}
                        if ($for_httpcode >= 300 && $for_httpcode < 400) {$codecolor = "bg-yellow";}
                        if ($for_httpcode >= 400 && $for_httpcode < 500) {$codecolor = "bg-yellow";}
                        if ($for_httpcode >= 500 && $for_httpcode < 500) {$codecolor = "orange-common";}
                        if ($for_httpcode >= 500 && $for_httpcode < 600) {$codecolor = "bg-red";}
                        echo '<div class="item '.$codecolor.'" title="'.$func_scans[$x].' / HTTP: '.$for_httpcode.' / Latency: '.$func_latency[$x].'s"></div>';

                    }

                    echo '        </div>';
                    // ###### Debugging
                    // ##########################################
                    // print_r($func_httpcodes);
                    // echo '<br>';
                    // print_r($func_scans);
                                        
            echo '              <table height="20px" width="100%"><tr><td><span class="progress-description">IP: '.$row['mon_TargetIP'].'</span></td><td align="right">Meldung bei: '.$notification_type.'</td></tr></table>
                        </div>
                    </div>
                  </div>';
        }
    }
    $db->close();
}

// ===============================================================================
// Start rendering page data
// ===============================================================================

// Get a array of device with monitored URLs
$unique_devices = get_devices_from_services();


// ###### Debugging
// ##########################################
//print_r($unique_devices);

// #######################################################
// ###### Main Function (Unique Devices)
// #######################################################
// Print a Box for every unique Device (MAC Address)
$i = 0;
while($i < count($unique_devices))
{
    $device_name = get_device_name($unique_devices[$i]);
    if ($device_name == "") {$device_name = $pia_lang['WebServices_unknown_Device'].' ('.$unique_devices[$i].')';}
    echo '<div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">'.$device_name.'</h3>
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
// ###### Main Function (Standalone)
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

<script>

</script>


