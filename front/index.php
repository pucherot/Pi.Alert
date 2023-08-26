<!-- ---------------------------------------------------------------------------
#  Pi.Alert
#  Open Source Network Guard / WIFI & LAN intrusion detector
#
#  index.php - Front module. Login and logout, redirect
#-------------------------------------------------------------------------------
#  leiweibau 2023                                          GNU GPLv3
#--------------------------------------------------------------------------- -->

<?php
session_start();

require 'php/server/db.php';
require 'php/server/journal.php';

$DBFILE = '../db/pialert.db';
OpenDB();

// Processing Logout
if ($_REQUEST['action'] == 'logout') {
	// Logging
	pialert_logging('a_001', $_SERVER['REMOTE_ADDR'], 'LogStr_9002', '', '');

	session_destroy();
	setcookie("PiAlert_SaveLogin", "", time() - 3600);
	header('Location: ./index.php');
}

// Login Processing start
$config_file = "../config/pialert.conf";
$config_file_lines = file($config_file);

// Login language settings
foreach (glob("../db/setting_language*") as $filename) {
	$pia_lang_selected = str_replace('setting_language_', '', basename($filename));
}
if (strlen($pia_lang_selected) == 0) {$pia_lang_selected = 'en_us';}
require 'php/templates/language/' . $pia_lang_selected . '.php';

// PIALERT_WEB_PROTECTION FALSE
$config_file_lines_bypass = array_values(preg_grep('/^PIALERT_WEB_PROTECTION\s.*/', $config_file_lines));
$protection_line = explode("=", $config_file_lines_bypass[0]);
$Pia_WebProtection = strtolower(trim($protection_line[1]));

if ($Pia_WebProtection != 'true') {
	header('Location: ./devices.php');
	$_SESSION['login'] = 1;
	$_SESSION['WebProtection'] = $Pia_WebProtection;
	exit;
}

// PIALERT_WEB_PROTECTION TRUE
$config_file_lines = array_values(preg_grep('/^PIALERT_WEB_PASSWORD\s.*/', $config_file_lines));
$password_line = explode("'", $config_file_lines[0]);
$Pia_Password = $password_line[1];

// Password without Cookie check -> pass and set initial cookie
if ($Pia_Password == hash('sha256', $_POST["loginpassword"])) {
	header('Location: ./devices.php');
	$_SESSION["login"] = 1;
	$_SESSION['WebProtection'] = $Pia_WebProtection;
	if (isset($_POST['PWRemember'])) {setcookie("PiAlert_SaveLogin", hash('sha256', $_POST["loginpassword"]), time() + 604800);}
	// Logging
	pialert_logging('a_001', $_SERVER['REMOTE_ADDR'], 'LogStr_9001', '', '');
}

// active Session or valid cookie (cookie not extends)
if (($_SESSION["login"] == 1) || ($Pia_Password == $_COOKIE["PiAlert_SaveLogin"])) {
	if ($_SESSION["login"] != 1) {
		pialert_logging('a_001', $_SERVER['REMOTE_ADDR'], 'LogStr_9004', '', '');
	}
	header('Location: ./devices.php');
	$_SESSION["login"] = 1;
	$_SESSION['WebProtection'] = $Pia_WebProtection;
}

// no active session, cookie not checked
if ($_SESSION["login"] != 1) {
	if ($_SESSION["login"] != 1 && isset($_POST["loginpassword"])) {
		// Logging
		pialert_logging('a_001', $_SERVER['REMOTE_ADDR'], 'LogStr_9003', '', '');
	}
	if (file_exists('../db/setting_darkmode')) {$ENABLED_DARKMODE = True;}
	if ($Pia_Password == '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92') {
		$login_info = 'Defaultpassword "123456" is still active';
		$login_mode = 'danger';
		$login_display_mode = 'display: block;';
		$login_headline = $pia_lang['Login_Toggle_Alert_headline'];
		$login_icon = 'fa-ban';
	} else {
		$login_mode = 'info';
		$login_display_mode = 'display: none;';
		$login_headline = $pia_lang['Login_Toggle_Info_headline'];
		$login_icon = 'fa-info';
	}

	?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Pi-Alert | Log in</title>

  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="lib/AdminLTE/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="lib/AdminLTE/bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="lib/AdminLTE/bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="lib/AdminLTE/dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="lib/AdminLTE/plugins/iCheck/square/blue.css">

  <!-- Dark-Mode Patch -->
<?php
if ($ENABLED_DARKMODE === True) {
		echo '<link rel="stylesheet" href="css/dark-patch.css">';
		$BACKGROUND_IMAGE_PATCH = 'style="background-image: url(\'img/boxed-bg-dark.png\');"';
	} else { $BACKGROUND_IMAGE_PATCH = 'style="background-image: url(\'img/background.png\');"';}
	?>

  <link rel="stylesheet" href="/front/css/offline-font.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="./index.php">Pi.<b>Alert</b></a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg"><?=$pia_lang['Login_Box'];?></p>
      <form action="./index.php" method="post">
      <div class="form-group has-feedback">
        <input type="password" class="form-control" placeholder="<?=$pia_lang['Login_Psw-box'];?>" name="loginpassword">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label for="PWRememberBox">
              <input type="checkbox" name="PWRemember" id="PWRememberBox">
                <div style="margin-left: 10px; display: inline-block; vertical-align: top;">
                  <?=$pia_lang['Login_Remember'];?><br><span style="font-size: smaller"><?=$pia_lang['Login_Remember_small'];?></span>
                </div>
            </label>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4" style="padding-top: 10px;">
          <button type="submit" class="btn btn-primary btn-block btn-flat"><?=$pia_lang['Login_Submit'];?></button>
        </div>
        <!-- /.col -->
      </div>
    </form>

    <div style="padding-top: 10px;">
      <button class="btn btn-xs btn-primary btn-block btn-flat" onclick="Passwordhinfo()"><?=$pia_lang['Login_Toggle_Info'];?></button>
    </div>

  </div>
  <!-- /.login-box-body -->

  <div id="myDIV" class="box-body" style="margin-top: 50px; <?=$login_display_mode;?>">
      <div class="alert alert-<?=$login_mode;?> alert-dismissible">
          <h4><i class="icon fa <?=$login_icon;?>"></i><?=$login_headline;?></h4>
          <p><?=$login_info;?></p>
          <p><?=$pia_lang['Login_Psw_run'];?><br><span style="border: solid 1px yellow; padding: 2px;">./pialert-cli set_password <?=$pia_lang['Login_Psw_new'];?></span><br><?=$pia_lang['Login_Psw_folder'];?></p>
      </div>
  </div>

</div>
<!-- /.login-box -->

<script src="lib/AdminLTE/bower_components/jquery/dist/jquery.min.js"></script>
<script src="lib/AdminLTE/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="lib/AdminLTE/plugins/iCheck/icheck.min.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });

function Passwordhinfo() {
  var x = document.getElementById("myDIV");
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
}

</script>
</body>
</html>

<?php

}
?>