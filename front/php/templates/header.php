<!-- ---------------------------------------------------------------------------
#  Pi.Alert
#  Open Source Network Guard / WIFI & LAN intrusion detector 
#
#  header.php - Front module. Common header to all the web pages 
#-------------------------------------------------------------------------------
#  Puche 2021        pi.alert.application@gmail.com        GNU GPLv3
#--------------------------------------------------------------------------- -->

<?php
// ###################################
// ## GUI settings processing start
// ###################################
if (file_exists('../db/setting_darkmode')) {
    $ENABLED_DARKMODE = True;
}
if (file_exists('../db/setting_noonlinehistorygraph')) {
    $ENABLED_HISTOY_GRAPH = False;
}
foreach (glob("../db/setting_skin*") as $filename) {
    $pia_skin_selected = str_replace('setting_','',basename($filename));
}
if (strlen($pia_skin_selected) == 0) {$pia_skin_selected = 'skin-blue';}

foreach (glob("../db/setting_language*") as $filename) {
    $pia_lang_selected = str_replace('setting_language_','',basename($filename));
}
if (strlen($pia_lang_selected) == 0) {$pia_lang_selected = 'en_us';}
require 'php/templates/language/'.$pia_lang_selected.'.php';
// ###################################
// ## GUI settings processing end
// ###################################
?>

<!DOCTYPE html> 
<html>

<!-- ----------------------------------------------------------------------- -->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta http-equiv="x-dns-prefetch-control" content="off">
  <meta http-equiv="cache-control" content="max-age=60,private">
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <link rel="manifest" href="img/manifest.json">
  <title>Pi.Alert - <?php echo gethostname();?></title>

  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="lib/AdminLTE/bower_components/bootstrap/dist/css/bootstrap.min.css">
  

  <!-- Font Awesome -->
  <link rel="stylesheet" href="lib/AdminLTE/bower_components/font-awesome/css/font-awesome.min.css">

  <!-- Ionicons -->
  <link rel="stylesheet" href="lib/AdminLTE/bower_components/Ionicons/css/ionicons.min.css">

  <!-- Theme style -->
  <link rel="stylesheet" href="lib/AdminLTE/dist/css/AdminLTE.min.css">

  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect. -->
  <link rel="stylesheet" href="lib/AdminLTE/dist/css/skins/<?php echo $pia_skin_selected;?>.min.css">

  <!-- Pi.Alert CSS -->
  <link rel="stylesheet" href="css/pialert.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic"> -->
  <link rel="stylesheet" href="css/offline-font.css">
  <link rel="icon" type="image/x-icon" href="img/pialertLogoOrange.png">

  <!-- For better UX on Mobile Devices using the Shortcut on the Homescreen -->
  <link rel="manifest" href="img/manifest.json">
  <!-- Dark-Mode Patch -->
<?php
if ($ENABLED_DARKMODE === True) {
   echo '<link rel="stylesheet" href="css/dark-patch.css">';
   $BACKGROUND_IMAGE_PATCH='style="background-image: url(\'img/boxed-bg-dark.png\');"';
} else { $BACKGROUND_IMAGE_PATCH='style="background-image: url(\'img/background.png\');"';}
?>
<!-- Servertime to the right of the hostname -->
<script>
var pia_servertime = new Date(<?php echo date("Y, n, j, G, i, s") ?>);

function show_pia_servertime() {
    if (!document.getElementById) {
        return;
    }
    var pia_hour = pia_servertime.getHours();
    var pia_minute = pia_servertime.getMinutes();
    var pia_second = pia_servertime.getSeconds();
    pia_servertime.setSeconds(pia_second + 1);
    if (pia_hour <= 9) { pia_hour = "0" + pia_hour; }
    if (pia_minute <= 9) { pia_minute = "0" + pia_minute; }
    if (pia_second <= 9) { pia_second = "0" + pia_second; } realtime_pia_servertime = "(" + pia_hour + ":" + pia_minute + ":" + pia_second + ")";
    if (document.getElementById) { document.getElementById("PIA_Servertime_place").innerHTML = realtime_pia_servertime; } setTimeout("show_pia_servertime()", 1000);
}


document.addEventListener("visibilitychange",()=>{
   if(document.visibilityState==="visible"){
       window.location.href = window.location.href.split('#')[0];
   }
})

</script>

</head>

<!-- ----------------------------------------------------------------------- -->
<!-- Layout Boxed Yellow -->
<body class="hold-transition <?php echo $pia_skin_selected;?> layout-boxed sidebar-mini" <?php echo $BACKGROUND_IMAGE_PATCH;?> onLoad="show_pia_servertime();" >
<!-- Site wrapper -->
<div class="wrapper">

  <!-- Main Header -->
  <header class="main-header">

<!-- ----------------------------------------------------------------------- -->
    <!-- Logo -->
    <a href="." class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini">P<b>a</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg">Pi<b>.Alert</b></span>
    </a>

<!-- ----------------------------------------------------------------------- -->
    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">

          <!-- Server Name -->
          <li><a style="pointer-events:none;"><?php echo gethostname();?> <span id="PIA_Servertime_place"></span></a></li>

          <!-- Header right info -->
          <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <!-- The user image in the navbar-->
              <img src="img/pialertLogoWhite.png" class="user-image" style="border-radius: initial" alt="Pi.Alert Logo">
              <!-- hidden-xs hides the username on small devices so only the image appears. -->
              <span class="hidden-xs">Pi.Alert</span>
            </a>
            <ul class="dropdown-menu">
              <!-- The user image in the menu -->
              <li class="user-header" style=" height: 100px;">
                <img src="img/pialertLogoWhite.png" class="img-circle" alt="Pi.Alert Logo" style="border-color:transparent;  height: 50px; width: 50px; margin-top:15px;">
                <p style="float: right; width: 200px">
                <?php echo $pia_lang['About_Title'];?>
                  <small><?php echo $pia_lang['About_Design'];?> Raspberry Pi</small>
                </p>
              </li>

              <!-- Menu Body -->

              <li class="user-footer">
                <div class="pull-right">
                  <a href="/pialert/index.php?action=logout" class="btn btn-danger"><?php echo $pia_lang['About_Exit'];?></a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>

<!-- ----------------------------------------------------------------------- -->
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <!-- Sidebar user panel (optional) -->
      <div class="user-panel">

<!--         <a href="." class="logo">
          <img src="img/pialertLogoGray80.png" class="img-responsive" alt="Pi.Alert Logo"/>
        </a> -->

                <div class="logo" style="width:50%; margin:auto;">
                   <img src="img/pialertLogoGray80.png" class="img-responsive" alt="Pi.Alert Logo"/>
                </div>
                <div class="systemstatusbox" style="font-size: smaller; margin-top:10px;">
                    <?php

                    // Pause Arp Scan Section ---------------------------------------------------------------

                    if (!file_exists('../db/setting_stoparpscan')) {
                      $execstring = 'ps -f -u root | grep "sudo arp-scan" 2>&1';
                      $pia_arpscans = "";
                      exec($execstring, $pia_arpscans);
                      $pia_arpscans_result = sizeof($pia_arpscans).' '.$pia_lang['Maintenance_arp_status_on'];
                      $pia_arpscans_sidebarstate = 'Active';
                      $pia_arpscans_sidebarstate_light = 'green-light';
                    } else {
                      $pia_arpscans_result = '<span style="color:red;">arp-Scan '.$pia_lang['Maintenance_arp_status_off'] .'</span>';
                      $pia_arpscans_sidebarstate = 'Disabled';
                      $pia_arpscans_sidebarstate_light = 'red';
                    }

                    // Pause Arp Scan Section ---------------------------------------------------------------

                    echo '<span id="status">
                            <i class="fa fa-w fa-circle text-'.$pia_arpscans_sidebarstate_light.'"></i> '.$pia_arpscans_sidebarstate.'&nbsp;&nbsp;
                          </span><br>';


                    // (may be less than the number of online processors)
                    $nproc = shell_exec('nproc');
                    if (!is_numeric($nproc)) {
                        $cpuinfo = file_get_contents('/proc/cpuinfo');
                        preg_match_all('/^processor/m', $cpuinfo, $matches);
                        $nproc = count($matches[0]);
                    }

                    $loaddata = sys_getloadavg();
                    echo '<span title="Detected '.$nproc.' cores"><i class="fa fa-w fa-circle ';
                    if ($loaddata[0] > $nproc) {
                        echo 'text-red';
                    } else {
                        echo 'text-green-light';
                    }
                    echo '"></i> Load:&nbsp;&nbsp;'.$loaddata[0].'&nbsp;&nbsp;'.$loaddata[1].'&nbsp;&nbsp;'.$loaddata[2].'</span>';
                    ?>
                    <br/>
                    <?php

                    function getMemUsage()
                    {
                        $data = explode("\n", file_get_contents('/proc/meminfo'));
                        $meminfo = array();
                        if (count($data) > 0) {
                            foreach ($data as $line) {
                                $expl = explode(':', $line);
                                if (count($expl) == 2) {
                                    // remove " kB" from the end of the string and make it an integer
                                    $meminfo[$expl[0]] = intval(trim(substr($expl[1], 0, -3)));
                                }
                            }
                            $memused = $meminfo['MemTotal'] - $meminfo['MemFree'] - $meminfo['Buffers'] - $meminfo['Cached'];
                            $memusage = $memused / $meminfo['MemTotal'];
                        } else {
                            $memusage = -1;
                        }

                        return $memusage;
                    }

                    $memory_usage = getMemUsage();
                    echo '<span><i class="fa fa-w fa-circle ';
                    if ($memory_usage > 0.75 || $memory_usage < 0.0) {
                        echo 'text-red';
                    } else {
                        echo 'text-green-light';
                    }
                    if ($memory_usage > 0.0) {
                        echo '"></i> Memory usage:&nbsp;&nbsp;'.sprintf('%.1f', 100.0 * $memory_usage).'&thinsp;%</span>';
                    } else {
                        echo '"></i> Memory usage:&nbsp;&nbsp; N/A</span>';
                    }
                    ?>
                    <br/>
                    <?php


                    function getTemperature()
                    {
                        if (file_exists('/sys/class/thermal/thermal_zone0/temp')) {
                            $output = rtrim(file_get_contents('/sys/class/thermal/thermal_zone0/temp'));
                        } elseif (file_exists('/sys/class/hwmon/hwmon0/temp1_input')) {
                            $output = rtrim(file_get_contents('/sys/class/hwmon/hwmon0/temp1_input'));
                        } else {
                            $output = '';
                        }

                        // Test if we succeeded in getting the temperature
                        if (is_numeric($output)) {
                            // $output could be either 4-5 digits or 2-3, and we only divide by 1000 if it's 4-5
                            // ex. 39007 vs 39
                            $celsius = intval($output);
                            // If celsius is greater than 1 degree and is in the 4-5 digit format
                            if ($celsius > 1000) {
                                // Use multiplication to get around the division-by-zero error
                                $celsius *= 1e-3;
                            }
                            $limit = 60;
                            
                        } else {
                            // Nothing can be colder than -273.15 degree Celsius (= 0 Kelvin)
                            // This is the minimum temperature possible (AKA absolute zero)
                            $celsius = -273.16;
                            // Set templimit to null if no tempsensor was found
                            $limit = null;
                        }
                        return array($celsius, $limit);
                    }

                    list($celsius, $temperaturelimit) = getTemperature();

                    if ($celsius >= -273.15) {
                        // Only show temp info if any data is available -->
                        $tempcolor = 'text-vivid-blue';
                        if (isset($temperaturelimit) && $celsius > $temperaturelimit) {
                            $tempcolor = 'text-red';
                        }
                        echo '<span id="temperature"><i class="fa fa-w fa-fire '.$tempcolor.'" style="width: 1em !important"></i> ';
                        echo 'Temp:&nbsp;<span id="rawtemp" hidden>'.$celsius.'</span>';
                        echo '<span id="tempdisplay"></span></span>';
                    }
                    ?>
                </div>





      </div>

      <!-- search form (Optional) -->
        <!-- DELETED -->

      <!-- Sidebar Menu -->
      <ul class="sidebar-menu" data-widget="tree">
<!--
        <li class="header">MAIN MENU</li>
-->
        <li class="header text-uppercase" style="font-size: 0; padding: 1px;">MAIN MENU</li>

        <li class=" <?php if (in_array (basename($_SERVER['SCRIPT_NAME']), array('devices.php', 'deviceDetails.php') ) ){ echo 'active'; } ?>">
          <a href="devices.php"><i class="fa fa-laptop"></i> <span><?php echo $pia_lang['Navigation_Devices'];?></span></a>
        </li>

<!--
         <li><a href="devices.php?status=favorites"><i class="fa fa-star"></i> <span>Favorites Devices</span></a></li>
-->

        <li class=" <?php if (in_array (basename($_SERVER['SCRIPT_NAME']), array('presence.php') ) ){ echo 'active'; } ?>">
          <a href="presence.php"><i class="fa fa-calendar"></i> <span><?php echo $pia_lang['Navigation_Presence'];?></span></a>
        </li>

        <li class=" <?php if (in_array (basename($_SERVER['SCRIPT_NAME']), array('events.php') ) ){ echo 'active'; } ?>">
          <a href="events.php"><i class="fa fa-bolt"></i> <span><?php echo $pia_lang['Navigation_Events'];?></span></a>
        </li>

        <li class="header text-uppercase" style="font-size: 0; padding: 1px;">Maintain and Settings</li>


        <li class=" <?php if (in_array (basename($_SERVER['SCRIPT_NAME']), array('network.php') ) ){ echo 'active'; } ?>">
          <a href="network.php"><i class="fa fa-server"></i> <span><?php echo $pia_lang['Navigation_Network'];?></span></a>
        </li>

        <li class=" <?php if (in_array (basename($_SERVER['SCRIPT_NAME']), array('maintenance.php') ) ){ echo 'active'; } ?>">
          <a href="maintenance.php"><i class="fa fa-cog"></i> <span><?php echo $pia_lang['Navigation_Maintenance'];?></span></a>
        </li>

        <li class="header text-uppercase" style="font-size: 0; padding: 1px;">Help</li>

        <li class=" <?php if (in_array (basename($_SERVER['SCRIPT_NAME']), array('help_faq.php') ) ){ echo 'active'; } ?>">
          <a href="help_faq.php"><i class="fa fa-question"></i> <span><?php echo $pia_lang['Navigation_HelpFAQ'];?></span></a>
        </li>

<!--
        <li class="treeview">
          <a href="#"><i class="fa fa-link"></i> <span>Config</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
          </a>

          <ul class="treeview-menu">
            <li class=" <?php if (in_array (basename($_SERVER['SCRIPT_NAME']), array('scancycles.php', 'scancyclesDetails.php') ) ){ echo 'active'; } ?>">
              <a href="scancycles.php"><i class="fa fa-link"></i> <span>Scan Cycles</span></a>
            </li>
            <li><a href="#">Cron Status</a></li>
            <li><a href="#">Current IP</a></li>
          </ul>
        </li>
-->
      </ul>

      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>
