<!-- ---------------------------------------------------------------------------
#  Pi.Alert
#  Open Source Network Guard / WIFI & LAN intrusion detector
#
#  header.php - Front module. Common header to all the web pages
#-------------------------------------------------------------------------------
#  Puche 2021        pi.alert.application@gmail.com        GNU GPLv3
#  leiweibau 2023                                          GNU GPLv3
#--------------------------------------------------------------------------- -->

<?php
error_reporting(0);

$conf_file = '../config/version.conf';
$conf_data = parse_ini_file($conf_file);

require 'header_func.php';
require 'php/templates/language/' . $pia_lang_selected . '.php';

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
  <title>Pi.Alert - <?php echo gethostname(); ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.4.1 -->
  <link rel="stylesheet" href="lib/AdminLTE/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Bootstrap Icons v1.10.3 -->
  <link href="lib/AdminLTE/bower_components/bootstrap-icons/font/bootstrap-icons.css" media="all" rel="stylesheet" type="text/css" />
  <!-- Font Awesome 6.40 -->
  <link rel="stylesheet" href="lib/AdminLTE/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="lib/AdminLTE/bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="lib/AdminLTE/dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. -->
  <link rel="stylesheet" href="lib/AdminLTE/dist/css/skins/<?=$pia_skin_selected;?>.min.css">
  <!-- Pi.Alert CSS -->
  <link rel="stylesheet" href="css/pialert.css?<?=$conf_data['VERSION_DATE'];?>">
  <!-- Offline Font -->
  <link rel="stylesheet" href="css/offline-font.css">
  <!-- Fav / Homescreen Icon -->
  <link rel="icon" type="image/x-icon" href="<?=$FRONTEND_FAVICON?>">
  <link rel="apple-touch-icon" href="<?=$FRONTEND_FAVICON?>">
  <!-- For better UX on Mobile Devices using the Shortcut on the Homescreen -->
  <link rel="manifest" href="img/manifest.json">

  <!-- Dark-Mode Patch -->
<?php
if ($ENABLED_DARKMODE === True) {
	echo '<link rel="stylesheet" href="css/dark-patch.css?' . $conf_data['VERSION_DATE'] . '">';
	$BACKGROUND_IMAGE_PATCH = 'style="background-image: url(\'img/boxed-bg-dark.png\');"';
} else {
	$BACKGROUND_IMAGE_PATCH = 'style="background-image: url(\'img/background.png\');"';
}
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

    if (window.navigator.standalone || document.referrer.includes("android-app://") ||  window.matchMedia("(display-mode: standalone)").matches) {
      document.addEventListener("visibilitychange", () => {
        if (document.visibilityState === "visible") {
          window.location.href = window.location.href.split('#')[0];
        }
      });
    }
  </script>
</head>

<!-- Layout Boxed Yellow -->
<body class="hold-transition <?=$pia_skin_selected;?> sidebar-mini" <?=$BACKGROUND_IMAGE_PATCH;?> onLoad="show_pia_servertime();" >
<!-- Site wrapper -->
<div class="wrapper">

  <!-- Main Header -->
  <header class="main-header">
    <!-- Logo -->
    <a href="." class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini">P<b>a</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg">Pi<b>.Alert</b></span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button" onclick="toggle_systeminfobox()">
        <span class="sr-only">Toggle navigation</span>
      </a>
<?php
insert_back_button();
$PIALERTLOGO_LINK = set_iconcolor_for_skin($pia_skin_selected);
?>
      <a id="navbar-reload-button" href="" role="button" onclick="window.location.href=window.location.href" style="">
        <i class="fa fa-repeat"></i>
      </a>
      <script>
          function toggle_systeminfobox() {
            $("#sidebar_systeminfobox").toggleClass("collapse");
          }
      </script>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">

          <!-- Server Name -->
          <li><a style="pointer-events:none; display: inline-block; height: 50px; padding-top: 15px"><?php echo gethostname(); ?> <span id="PIA_Servertime_place"></span></a></li>

          <!-- Header right info -->
          <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="height: 50px; padding-top: 15px">
              <!-- The user image in the navbar-->
              <img src="img/<?=$PIALERTLOGO_LINK;?>.png" class="user-image" style="border-radius: initial" alt="Pi.Alert Logo">
              <!-- hidden-xs hides the username on small devices so only the image appears. -->
              <!-- <span class="hidden-xs">Pi.Alert</span> -->
              <span class="label label-danger" id="Menu_Report_Counter_Badge"></span>
            </a>
            <ul class="dropdown-menu" style="width: 240px;">
              <!-- The user image in the menu -->
              <li class="user-header" style=" height: 70px; padding-top: 0px;">
                <img src="img/pialertLogoWhite.png" class="img-circle" alt="Pi.Alert Logo" style="border-color:transparent; height: 50px; width: 50px; margin-top:10px;">
                <p style="float: right; width: 150px">
                <?=$pia_lang['About_Title'];?>
                </p>
              </li>
              <!-- Menu Body -->
              <li class="user-footer" style="padding-top: 15px; padding-bottom: 0px;">
                <div class="" style="text-align: center;">
                  <label>
                    <input type="checkbox" id="autoReloadCheckbox" style="margin-right: 10px;"> Auto Page Reload (2min)
                  </label>
                </div>
              </li>
              <li class="user-footer">
                <div class="" style="text-align: center;">
                  <a href="./deviceDetails.php?mac=Internet" id="custom-menu-default-button" class="btn btn-default"><i class="fa-solid fa-globe custom-menu-button-icon"></i><div class="custom-menu-button-text">Internet</div></a>
                </div>
              </li>
              <li class="user-footer">
                <div class="" style="text-align: center;">
                  <a href="./reports.php" id="custom-menu-report-button" class="btn btn-warning"><i class="fa-regular fa-envelope-open custom-menu-button-icon" id="Menu_Report_Envelope_Icon"></i><div class="custom-menu-button-text"><?=$pia_lang['About_Reports'];?></div></a>
                </div>
              </li>
              <li class="user-footer">
                <div class="" style="text-align: center;">
                  <a href="./index.php?action=logout" id="custom-menu-logout-button" class="btn btn-danger"><i class="fa-solid fa-arrow-right-from-bracket custom-menu-button-icon"></i><div class="custom-menu-button-text"><?=$pia_lang['About_Exit'];?></div></a>
                </div>
              </li>
              <li class="user-footer">
                <div class="custom-menu-icon-links">
                  <a href="https://github.com/leiweibau/Pi.Alert" class="btn btn-default" target="blank"><i class="fa-brands fa-github"></i></a>
                </div>
                <div class="custom-menu-icon-links">
                  <a href="https://github.com/sponsors/leiweibau" class="btn btn-default" target="blank"><i class="fa-regular fa-heart text-maroon"></i></a>
                </div>
                <div class="custom-menu-icon-links">
                  <a href="https://leiweibau.net/archive/pialert/" class="btn btn-default" target="blank"><i class="fa-solid fa-house"></i></a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>

  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <!-- Sidebar user panel (optional) -->
      <div class="user-panel" id="sidebar_systeminfobox">

        <div class="logo" style="width:58px; display: inline-block;">
           <a href="./"><img src="img/pialertLogoGray80.png" class="img-responsive" alt="Pi.Alert Logo"/></a>
        </div>
        <div style="width:142px; display: inline-block; padding-left: 8px;">
          <a href="systeminfo.php">
            <div class="systemstatusbox" style="font-size: smaller; margin-top:10px;">
<?php
arpscanstatus();

echo '<span id="status"><i class="fa fa-w fa-circle text-' . $_SESSION['arpscan_sidebarstate_light'] . '"></i> ' . $_SESSION['arpscan_sidebarstate'] . '&nbsp;&nbsp;</span><br>';

format_sysloadavg(sys_getloadavg());

echo '<br/>';
format_MemUsage(getMemUsage());

echo '<br/>';

list($celsius, $temperaturelimit) = getTemperature();
format_temperature($celsius, $temperaturelimit);
?>
            </div>
          </a>
        </div>
      </div>
      <!-- Sidebar Menu -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header text-uppercase" style="font-size: 10; padding: 1px;"><?=$pia_lang['Navigation_Section_A'];?></li>
        <li class=" <?php if (in_array(basename($_SERVER['SCRIPT_NAME']), array('devices.php', 'deviceDetails.php'))) {echo 'active';}?>">
          <a href="devices.php">
            <i class="fa fa-laptop"></i>
            <span><?=$pia_lang['Navigation_Devices'];?></span>
            <span class="pull-right-container">
              <small class="label pull-right bg-yellow" id="header_dev_count_new"></small>
              <small class="label pull-right bg-red" id="header_dev_count_down"></small>
              <small class="label pull-right bg-green" id="header_dev_count_on"></small>
            </span>
          </a>
        </li>
        <li class=" <?php if (in_array(basename($_SERVER['SCRIPT_NAME']), array('network.php', 'networkSettings.php'))) {echo 'active';}?>">
          <a href="network.php"><i class="fa fa-server"></i> <span><?=$pia_lang['Navigation_Network'];?></span></a>
        </li>
        <?php toggle_webservices_menu('Main');?>
        <?php toggle_icmpscan_menu('Main');?>

        <li class="header text-uppercase" style="font-size: 10; padding: 1px;"><?=$pia_lang['Navigation_Section_B'];?></li>
        <li class=" <?php if (in_array(basename($_SERVER['SCRIPT_NAME']), array('devicesEvents.php'))) {echo 'active';}?>">
          <a href="devicesEvents.php"><i class="fa fa-laptop"></i> <span><?=$pia_lang['Navigation_Events_Dev'];?></span></a>
        </li>
        <li class=" <?php if (in_array(basename($_SERVER['SCRIPT_NAME']), array('presence.php'))) {echo 'active';}?>">
          <a href="presence.php"><i class="fa fa-calendar"></i> <span><?=$pia_lang['Navigation_Presence'];?></span></a>
        </li>
        <li class=" <?php if (in_array(basename($_SERVER['SCRIPT_NAME']), array('journal.php'))) {echo 'active';}?>">
          <a href="journal.php"><i class="fa fa-list"></i> <span><?=$pia_lang['Navigation_Journal'];?></span></a>
        </li>

        <li class="header text-uppercase" style="font-size: 10; padding: 1px;"><?=$pia_lang['Navigation_Section_C'];?></li>
        <li class=" <?php if (in_array(basename($_SERVER['SCRIPT_NAME']), array('maintenance.php'))) {echo 'active';}?>">
          <a href="maintenance.php"><i class="fa fa-cog"></i> <span><?=$pia_lang['Navigation_Maintenance'];?></span></a>
        </li>
        <li class=" <?php if (in_array(basename($_SERVER['SCRIPT_NAME']), array('help_faq.php'))) {echo 'active';}?>">
          <a href="help_faq.php"><i class="fa fa-question"></i> <span><?=$pia_lang['Navigation_HelpFAQ'];?></span></a>
        </li>
        <li class=" <?php if (in_array(basename($_SERVER['SCRIPT_NAME']), array('updatecheck.php'))) {echo 'active';}?>">
          <a href="updatecheck.php"><i class="fa fa-rotate-right"></i> <span> <?=$pia_lang['Navigation_UpdateCheck'];?></span></a>
        </li>

      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>
