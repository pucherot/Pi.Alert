<?php
//------------------------------------------------------------------------------
//  Pi.Alert
//  Open Source Network Guard / WIFI & LAN intrusion detector
//
//  maintenance.php - Front module. Server side. Manage Devices
//------------------------------------------------------------------------------
//  Puche      2021        pi.alert.application@gmail.com   GNU GPLv3
//  jokob-sk   2022        jokob.sk@gmail.com               GNU GPLv3
//  leiweibau  2023        https://github.com/leiweibau     GNU GPLv3
//------------------------------------------------------------------------------

session_start();

// Turn off php errors
error_reporting(0);

if ($_SESSION["login"] != 1) {
	header('Location: ./index.php');
	exit;
}

require 'php/templates/header.php';
?>
<!-- Page ------------------------------------------------------------------ -->
<div class="content-wrapper">

<!-- Content header--------------------------------------------------------- -->
    <section class="content-header">
    <?php require 'php/templates/notification.php';?>
      <h1 id="pageTitle">
         <?php echo $pia_lang['Maintenance_Title']; ?>
      </h1>
    </section>

    <!-- Main content ---------------------------------------------------------- -->
    <section class="content">

<?php
// Get API-Key ------------------------------------------------------------------

$CONFIG_FILE_SOURCE = "../config/pialert.conf";
$CONFIG_FILE_KEY_LINE = file($CONFIG_FILE_SOURCE);
$CONFIG_FILE_KEY_VALUE = array_values(preg_grep('/^PIALERT_APIKEY\s.*/', $CONFIG_FILE_KEY_LINE));
if ($CONFIG_FILE_KEY_VALUE != False) {
	$APIKEY_LINE = explode("'", $CONFIG_FILE_KEY_VALUE[0]);
	$APIKEY = trim($APIKEY_LINE[1]);
} else { $APIKEY = $pia_lang['Maintenance_Tool_setapikey_false'];}

// Get Ignore List ------------------------------------------------------------------

$CONFIG_FILE_KEY_VALUE = array_values(preg_grep('/^MAC_IGNORE_LIST\s.*/', $CONFIG_FILE_KEY_LINE));
if ($CONFIG_FILE_KEY_VALUE != False) {
	$MAC_IGNORE_LIST_LINE = substr($CONFIG_FILE_KEY_VALUE[0], (strpos($CONFIG_FILE_KEY_VALUE[0], "=") + 1));
	$MAC_IGNORE_LIST = str_replace("[", "", str_replace("]", "", str_replace("'", "", trim($MAC_IGNORE_LIST_LINE))));
} else { $MAC_IGNORE_LIST = $pia_lang['Maintenance_Tool_ignorelist_false'];}

// Get Notification Settings ------------------------------------------------------------------

$CONFIG_FILE_FILTER_VALUE_ARP = array_values(preg_grep("/(REPORT_MAIL |REPORT_NTFY |REPORT_WEBGUI |REPORT_PUSHSAFER |REPORT_PUSHOVER |REPORT_TELEGRAM )/i", $CONFIG_FILE_KEY_LINE));
$CONFIG_FILE_FILTER_VALUE_WEB = array_values(preg_grep("/(REPORT_MAIL_WEBMON|REPORT_NTFY_WEBMON|REPORT_WEBGUI_WEBMON|REPORT_PUSHSAFER_WEBMON|REPORT_PUSHOVER_WEBMON |REPORT_TELEGRAM_WEBMON)/i", $CONFIG_FILE_KEY_LINE));

function format_notifications($source_array) {
	$format_array_true = array();
	$format_array_false = array();
	$text_reference = array('WEBGUI', 'TELEGRAM', 'MAIL', 'PUSHSAFER', 'PUSHOVER', 'NTFY');
	$text_format = array('WebGUI', 'Telegram', 'Mail', 'Pushsafer', 'Pushover', 'NTFY');
	for ($x = 0; $x < sizeof($source_array); $x++) {
		$temp = explode("=", $source_array[$x]);
		$temp[0] = trim($temp[0]);
		$temp[1] = trim($temp[1]);
		if (strtolower($temp[1]) == "true") {
			$temp[0] = str_replace('REPORT_', '', $temp[0]);
			$temp[0] = str_replace('_WEBMON', '', $temp[0]);
			$key = array_search($temp[0], $text_reference);
			array_push($format_array_true, '<span style="color: green;">' . $text_format[$key] . '</span>');
		}
		if (strtolower($temp[1]) == "false") {
			$temp[0] = str_replace('REPORT_', '', $temp[0]);
			$temp[0] = str_replace('_WEBMON', '', $temp[0]);
			$key = array_search($temp[0], $text_reference);
			array_push($format_array_false, '<span style="color: red;">' . $text_format[$key] . '</span>');
		}
	}
	natsort($format_array_true);
	natsort($format_array_false);
	$output = implode(", ", $format_array_true) . ', ' . implode(", ", $format_array_false);
	echo $output;
}

// Size and last mod of DB ------------------------------------------------------

$DB_SOURCE = str_replace('front', 'db', getcwd()) . '/pialert.db';
$DB_SIZE_DATA = number_format((filesize($DB_SOURCE) / 1000000), 2, ",", ".") . ' MB';
$DB_MOD_DATA = date("d.m.Y, H:i:s", filemtime($DB_SOURCE)) . ' Uhr';

// Count Config Backups -------------------------s------------------------------

$CONFIG_FILE_DIR = str_replace('front', 'config', getcwd()) . '/';
$files = glob($CONFIG_FILE_DIR . "pialert-20*.bak");
if ($files) {
	$CONFIG_FILE_COUNT = count($files);
} else { $CONFIG_FILE_COUNT = 0;}

// Count and Calc DB Backups -------------------------------------------------------

$ARCHIVE_PATH = str_replace('front', 'db', getcwd()) . '/';
$ARCHIVE_COUNT = 0;
$ARCHIVE_DISKUSAGE = 0;
$files = glob($ARCHIVE_PATH . "pialertdb_*.zip");
if ($files) {
	$ARCHIVE_COUNT = count($files);
}
foreach ($files as $result) {
	$ARCHIVE_DISKUSAGE = $ARCHIVE_DISKUSAGE + filesize($result);
}
$ARCHIVE_DISKUSAGE = number_format(($ARCHIVE_DISKUSAGE / 1000000), 2, ",", ".") . ' MB';

// Find latest Backup for restore -----------------------------------------------

$LATEST_FILES = glob($ARCHIVE_PATH . "pialertdb_*.zip");
if (sizeof($LATEST_FILES) == 0) {
	$LATEST_BACKUP_DATE = $pia_lang['Maintenance_Tool_restore_blocked'];
	$block_restore_button = true;
} else {
	natsort($LATEST_FILES);
	$LATEST_FILES = array_reverse($LATEST_FILES, False);
	$LATEST_BACKUP = $LATEST_FILES[0];
	$LATEST_BACKUP_DATE = date("Y-m-d H:i:s", filemtime($LATEST_BACKUP));
}

// Aprscan read Timer -----------------------------------------------------------------

function read_arpscan_timer() {
	//$pia_lang_set_dir = '../db/';
	$file = '../db/setting_stoparpscan';
	if (file_exists($file)) {
		$timer_arpscan = file_get_contents($file, true);
		if ($timer_arpscan == 10 || $timer_arpscan == 15 || $timer_arpscan == 30) {
			$timer_output = ' (' . $timer_arpscan . 'min)';
		}
		if ($timer_arpscan == 60 || $timer_arpscan == 120 || $timer_arpscan == 720 || $timer_arpscan == 1440) {
			$timer_arpscan = $timer_arpscan / 60;
			$timer_output = ' (' . $timer_arpscan . 'h)';
		}
		if ($timer_arpscan == 1051200) {
			$timer_output = ' (very long)';
		}
	}
	$timer_output = '<span style="color:red;">' . $timer_output . '</span>';
	echo $timer_output;
}

// Get Device List Columns -----------------------------------------------------------------

function read_DevListCol() {
	//$pia_lang_set_dir = '../db/';
	$file = '../db/setting_devicelist';
	if (file_exists($file)) {
		$get = file_get_contents($file, true);
		$output_array = json_decode($get, true);
	} else {
		$output_array = array('ConnectionType' => 0, 'Favorites' => 1, 'Group' => 1, 'Owner' => 1, 'Type' => 1, 'FirstSession' => 1, 'LastSession' => 1, 'LastIP' => 1, 'MACType' => 1, 'MACAddress' => 0, 'Location' => 0);
	}
	return $output_array;
}

// Set preset checkboxes for Columnconfig -----------------------------------------------------------------

function set_column_checkboxes($table_config) {
	if ($table_config['ConnectionType'] == 1) {$col_checkbox['ConnectionType'] = "checked";}
	if ($table_config['Favorites'] == 1) {$col_checkbox['Favorites'] = "checked";}
	if ($table_config['Group'] == 1) {$col_checkbox['Group'] = "checked";}
	if ($table_config['Owner'] == 1) {$col_checkbox['Owner'] = "checked";}
	if ($table_config['Type'] == 1) {$col_checkbox['Type'] = "checked";}
	if ($table_config['FirstSession'] == 1) {$col_checkbox['FirstSession'] = "checked";}
	if ($table_config['LastSession'] == 1) {$col_checkbox['LastSession'] = "checked";}
	if ($table_config['LastIP'] == 1) {$col_checkbox['LastIP'] = "checked";}
	if ($table_config['MACType'] == 1) {$col_checkbox['MACType'] = "checked";}
	if ($table_config['MACAddress'] == 1) {$col_checkbox['MACAddress'] = "checked";}
	if ($table_config['Location'] == 1) {$col_checkbox['Location'] = "checked";}
	return $col_checkbox;
}

// Read logfiles -----------------------------------------------------------------

function read_logfile($logfile, $logmessage) {
	$file = file_get_contents('./php/server/' . $logfile, true);
	if ($file == "") {echo $logmessage;}
	if ($logfile == "pialert.webservices.log") {
		$file = str_replace("Start Services Monitoring\n\n", "Start Services Monitoring\n\n<pre style=\"border: solid 1px #666; background-color: transparent;\">", $file);
		$file = str_replace("\nServices Monitoring Changes:", "\n</pre>Services Monitoring Changes:", $file);
	}
	echo str_replace("\n", '<br>', str_replace("    ", '&nbsp;&nbsp;&nbsp;&nbsp;', str_replace("        ", '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $file)));
}

function read_logfile_vendor() {
	global $pia_lang;

	$file = file_get_contents('./php/server/pialert.vendors.log');
	if ($file == "") {echo $pia_lang['Maintenance_Tools_Logviewer_Vendor_empty'];} else {
		$temp_log = explode("\n", $file);
		$x = 0;
		while ($x < sizeof($temp_log)) {
			if (strlen($temp_log[$x]) == 0) {
				$y = $x;
				while ($y < sizeof($temp_log)) {
					echo $temp_log[$y] . '<br>';
					$y++;
				}
				break;
			}
			$x++;
		}
	}
}

function print_logviewer_modal_head($id, $title) {
	echo '<div class="modal fade" id="modal-logviewer-' . $id . '">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Viewer: ' . $title . '</h4>
                </div>
                <div class="modal-body main_logviwer_text_layout">
                    <div class="main_logviwer_log">';
}

function print_logviewer_modal_foot() {
	global $pia_lang;
	echo '                <br></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">' . $pia_lang['Gen_Close'] . '</button>
                </div>
            </div>
        </div>
    </div>';
}
// Set Tab ----------------------------------------------------------------------------

if ($_REQUEST['tab'] == '1') {
	$pia_tab_setting = 'active';
	$pia_tab_tool = '';
	$pia_tab_backup = '';
} elseif ($_REQUEST['tab'] == '2') {
	$pia_tab_setting = '';
	$pia_tab_tool = 'active';
	$pia_tab_backup = '';
} elseif ($_REQUEST['tab'] == '3') {
	$pia_tab_setting = '';
	$pia_tab_tool = '';
	$pia_tab_backup = 'active';
} else {
	$pia_tab_setting = 'active';
	$pia_tab_tool = '';
	$pia_tab_backup = '';}

?>

    <div class="row">
      <div class="col-md-12">

<!-- Status Box ----------------------------------------------------------------- -->

    <div class="box" id="Maintain-Status">
        <div class="box-header with-border">
            <h3 class="box-title">Status</h3>
        </div>
        <div class="box-body" style="padding-bottom: 5px;">
            <div class="db_info_table">
                <div class="db_info_table_row">
                    <div class="db_info_table_cell" style="min-width: 140px"><?php echo $pia_lang['Maintenance_database_path']; ?></div>
                    <div class="db_info_table_cell" style="width: 70%">
                        <?php echo $DB_SOURCE; ?>
                    </div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_info_table_cell"><?php echo $pia_lang['Maintenance_database_size']; ?></div>
                    <div class="db_info_table_cell">
                        <?php echo $DB_SIZE_DATA; ?>
                    </div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_info_table_cell"><?php echo $pia_lang['Maintenance_database_lastmod']; ?></div>
                    <div class="db_info_table_cell">
                        <?php echo $DB_MOD_DATA; ?>
                    </div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_info_table_cell"><?php echo $pia_lang['Maintenance_database_backup']; ?></div>
                    <div class="db_info_table_cell">
                        <?php echo $ARCHIVE_COUNT . ' ' . $pia_lang['Maintenance_database_backup_found'] . ' / ' . $pia_lang['Maintenance_database_backup_total'] . ': ' . $ARCHIVE_DISKUSAGE; ?>
                    </div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_info_table_cell"><?php echo $pia_lang['Maintenance_config_backup']; ?></div>
                    <div class="db_info_table_cell">
                        <?php echo $CONFIG_FILE_COUNT . ' ' . $pia_lang['Maintenance_database_backup_found']; ?>
                    </div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_info_table_cell"><?php echo $pia_lang['Maintenance_arp_status']; ?></div>
                    <div class="db_info_table_cell">
                        <?php echo $_SESSION['arpscan_result'];
read_arpscan_timer(); ?></div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_info_table_cell">Api-Key</div>
                    <div class="db_info_table_cell" style="overflow-wrap: anywhere;">
                        <input readonly value="<?php echo $APIKEY; ?>" style="width:100%; overflow-x: scroll; border: none; background: transparent; margin: 0px; padding: 0px;">
                    </div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_info_table_cell"><?php echo $pia_lang['Maintenance_notification_config']; ?></div>
                    <div class="db_info_table_cell">
                        <?php echo format_notifications($CONFIG_FILE_FILTER_VALUE_ARP); ?>
                    </div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_info_table_cell"><?php echo $pia_lang['Maintenance_notification_config_webmon']; ?></div>
                    <div class="db_info_table_cell">
                        <?php echo format_notifications($CONFIG_FILE_FILTER_VALUE_WEB); ?>
                    </div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_info_table_cell"><?php echo $pia_lang['Maintenance_Tool_ignorelist']; ?></div>
                    <div class="db_info_table_cell">
                        <?php echo $MAC_IGNORE_LIST; ?>
                    </div>
                </div>
            </div>
        </div>
          <!-- /.box-body -->
    </div>

      </div>
    </div>

<!-- Update Check ----------------------------------------------------------------- -->

<!--     <div class="box">
        <div class="box-body" id="updatecheck">
            <button type="button" id="rewwejwejpjo" class="btn btn-primary" onclick="check_github_for_updates()"><?php echo $pia_lang['Maintenance_Tools_Updatecheck']; ?></button>
      </div>
    </div> -->

<!-- Log Viewer ----------------------------------------------------------------- -->

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Log Viewer</h3>
        </div>
        <div class="box-body main_logviwer_buttonbox" id="logviewer">
            <button type="button" id="oisjmofeirfj" class="btn btn-primary main_logviwer_button_m" data-toggle="modal" data-target="#modal-logviewer-scan"><?php echo $pia_lang['Maintenance_Tools_Logviewer_Scan']; ?></button>
            <button type="button" id="wefwfwefewdf" class="btn btn-primary main_logviwer_button_m" data-toggle="modal" data-target="#modal-logviewer-iplog"><?php echo $pia_lang['Maintenance_Tools_Logviewer_IPLog']; ?></button>
            <button type="button" id="tzhrsreawefw" class="btn btn-primary main_logviwer_button_m" data-toggle="modal" data-target="#modal-logviewer-vendor"><?php echo $pia_lang['Maintenance_Tools_Logviewer_Vendor']; ?></button>
            <button type="button" id="arzuozhrsfga" class="btn btn-primary main_logviwer_button_m" data-toggle="modal" data-target="#modal-logviewer-cleanup"><?php echo $pia_lang['Maintenance_Tools_Logviewer_Cleanup']; ?></button>
            <button type="button" id="ufiienfflgze" class="btn btn-primary main_logviwer_button_m" data-toggle="modal" data-target="#modal-logviewer-nmap"><?php echo $pia_lang['Maintenance_Tools_Logviewer_Nmap']; ?></button>
            <button type="button" id="lgnsisnuhzgd" class="btn btn-primary main_logviwer_button_m" data-toggle="modal" data-target="#modal-logviewer-wol"><?php echo $pia_lang['Maintenance_Tools_Logviewer_WOL']; ?></button>
<?php
if ($_SESSION['Scan_WebServices'] == True) {
	echo '<button type="button" id="erftttwrdwqqq" class="btn btn-primary main_logviwer_button_m" data-toggle="modal" data-target="#modal-logviewer-webservices">' . $pia_lang['Maintenance_Tools_Logviewer_WebServices'] . '</button>';
}
?>
      </div>
    </div>

<!-- Log Viewer - Modals Scan ----------------------------------------------------------------- -->
<?php
print_logviewer_modal_head('scan', 'pialert.1.log (File)');
read_logfile('pialert.1.log', $pia_lang['Maintenance_Tools_Logviewer_Scan_empty']);
print_logviewer_modal_foot();
?>

<!-- Log Viewer - Modals IP ----------------------------------------------------------------- -->
<?php
print_logviewer_modal_head('iplog', 'pialert.IP.log (File)');
read_logfile('pialert.IP.log', $pia_lang['Maintenance_Tools_Logviewer_IPLog_empty']);
print_logviewer_modal_foot();
?>

<!-- Log Viewer - Modals Vendor Update ----------------------------------------------------------------- -->
<?php
print_logviewer_modal_head('vendor', 'pialert.vendors.log (File)');
read_logfile_vendor();
print_logviewer_modal_foot();
?>

<!-- Log Viewer - Modals Cleanup ----------------------------------------------------------------- -->
<?php
print_logviewer_modal_head('cleanup', 'pialert.cleanup.log (File)');
read_logfile('pialert.cleanup.log', $pia_lang['Maintenance_Tools_Logviewer_Cleanup_empty']);
print_logviewer_modal_foot();
?>

<!-- Log Viewer - Modals Nmap ----------------------------------------------------------------- -->
<?php
print_logviewer_modal_head('nmap', 'last Nmap Scan (Memory)');
if (!isset($_SESSION['ScanShortMem_NMAP'])) {echo $pia_lang['Maintenance_Tools_Logviewer_Nmap_empty'];} else {echo $_SESSION['ScanShortMem_NMAP'];}
print_logviewer_modal_foot();
?>

<!-- Log Viewer - Modals WebServices ----------------------------------------------------------------- -->
<?php
if ($_SESSION['Scan_WebServices'] == True) {
	print_logviewer_modal_head('webservices', 'pialert.webservices.log (File)');
	read_logfile('pialert.webservices.log', $pia_lang['Maintenance_Tools_Logviewer_WebServices_empty']);
	print_logviewer_modal_foot();
}
?>

<!-- Log Viewer - Modals Wake-on-LAN ----------------------------------------------------------------- -->
<?php
print_logviewer_modal_head('wol', 'last Wake-on-LAN (Memory)');
if (!isset($_SESSION['ScanShortMem_WOL'])) {echo $pia_lang['Maintenance_Tools_Logviewer_WOL_empty'];} else {echo $_SESSION['ScanShortMem_WOL'];}
print_logviewer_modal_foot();
?>

<!-- Tabs ----------------------------------------------------------------- -->

    <div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="<?php echo $pia_tab_setting; ?>"><a href="#tab_Settings" data-toggle="tab" onclick="update_tabURL(window.location.href,'1')"><?php echo $pia_lang['Maintenance_Tools_Tab_Settings']; ?></a></li>
        <li class="<?php echo $pia_tab_tool; ?>"><a href="#tab_DBTools" data-toggle="tab" onclick="update_tabURL(window.location.href,'2')"><?php echo $pia_lang['Maintenance_Tools_Tab_Tools']; ?></a></li>
        <li class="<?php echo $pia_tab_backup; ?>"><a href="#tab_BackupRestore" data-toggle="tab" onclick="update_tabURL(window.location.href,'3')"><?php echo $pia_lang['Maintenance_Tools_Tab_BackupRestore']; ?></a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane <?php echo $pia_tab_setting; ?>" id="tab_Settings">
            <table class="table_settings">
                <tr><td colspan="2"><h4 class="bottom-border-aqua"><?php echo $pia_lang['Maintenance_Tools_Tab_Subheadline_a']; ?></h4></td></tr>
                <tr class="table_settings">
                    <td class="db_info_table_cell" colspan="2" style="text-align: justify;"><?php echo $pia_lang['Maintenance_Tools_Tab_Settings_Intro']; ?></td>
                </tr>
                <tr class="table_settings_row">
                    <td class="db_info_table_cell" colspan="2" style="padding-bottom: 20px;">
                        <div style="display: flex; justify-content: center; flex-wrap: wrap;">
<!-- Language Selection ----------------------------------------------------------------- -->
                            <div class="settings_button_wrapper">
                                <div class="settings_button_box">
                                    <div class="form-group" style="width:160px; margin-bottom:5px;">
                                      <div class="input-group">
                                        <input class="form-control" id="txtLangSelection" type="text" value="<?php echo $pia_lang['Maintenance_lang_selector_empty']; ?>" readonly >
                                        <div class="input-group-btn">
                                          <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id="dropdownButtonLangSelection">
                                            <span class="fa fa-caret-down"></span></button>
                                          <ul id="dropdownLangSelection" class="dropdown-menu dropdown-menu-right">
                                            <li><a href="javascript:void(0)" onclick="setTextValue('txtLangSelection','en_us');"><?php echo $pia_lang['Maintenance_lang_en_us']; ?></a></li>
                                            <li><a href="javascript:void(0)" onclick="setTextValue('txtLangSelection','de_de');"><?php echo $pia_lang['Maintenance_lang_de_de']; ?></a></li>
                                            <li><a href="javascript:void(0)" onclick="setTextValue('txtLangSelection','fr_fr');"><?php echo $pia_lang['Maintenance_lang_fr_fr']; ?></a></li>
                                            <li><a href="javascript:void(0)" onclick="setTextValue('txtLangSelection','es_es');"><?php echo $pia_lang['Maintenance_lang_es_es']; ?></a></li>
                                          </ul>
                                        </div>
                                      </div>
                                    </div>
                                    <button type="button" class="btn btn-default" style="margin-top:0px; width:160px;" id="btnSaveLangSelection" onclick="setPiAlertLanguage()" ><?php echo $pia_lang['Maintenance_lang_selector_apply']; ?> </button>
                                </div>
                            </div>
<!-- Theme Selection ----------------------------------------------------------------- -->
                            <div class="settings_button_wrapper">
                                <div class="settings_button_box">
                                    <div class="form-group" style="width:160px; margin-bottom:5px;">
                                      <div class="input-group">
                                        <input class="form-control" id="txtSkinSelection" type="text" value="<?php echo $pia_lang['Maintenance_themeselector_empty']; ?>" readonly >
                                        <div class="input-group-btn">
                                          <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id="dropdownButtonSkinSelection">
                                            <span class="fa fa-caret-down"></span></button>
                                          <ul id="dropdownSkinSelection" class="dropdown-menu dropdown-menu-right">
                                            <li><a href="javascript:void(0)" onclick="setTextValue('txtSkinSelection','skin-black-light');">Black-light</a></li>
                                            <li><a href="javascript:void(0)" onclick="setTextValue('txtSkinSelection','skin-black');">Black</a></li>
                                            <li><a href="javascript:void(0)" onclick="setTextValue('txtSkinSelection','skin-blue-light');">Blue-light</a></li>
                                            <li><a href="javascript:void(0)" onclick="setTextValue('txtSkinSelection','skin-blue');">Blue</a></li>
                                            <li><a href="javascript:void(0)" onclick="setTextValue('txtSkinSelection','skin-green-light');">Green-light</a></li>
                                            <li><a href="javascript:void(0)" onclick="setTextValue('txtSkinSelection','skin-green');">Green</a></li>
                                            <li><a href="javascript:void(0)" onclick="setTextValue('txtSkinSelection','skin-purple-light');">Purple-light</a></li>
                                            <li><a href="javascript:void(0)" onclick="setTextValue('txtSkinSelection','skin-purple');">Purple</a></li>
                                            <li><a href="javascript:void(0)" onclick="setTextValue('txtSkinSelection','skin-red-light');">Red-light</a></li>
                                            <li><a href="javascript:void(0)" onclick="setTextValue('txtSkinSelection','skin-red');">Red</a></li>
                                            <li><a href="javascript:void(0)" onclick="setTextValue('txtSkinSelection','skin-yellow-light');">Yellow-light</a></li>
                                            <li><a href="javascript:void(0)" onclick="setTextValue('txtSkinSelection','skin-yellow');">Yellow</a></li>
                                          </ul>
                                        </div>
                                      </div>
                                    </div>
                                    <button type="button" class="btn btn-default" style="margin-top:0px; width:160px;" id="btnSaveSkinSelection" onclick="setPiAlertTheme()" ><?php echo $pia_lang['Maintenance_themeselector_apply']; ?> </button>
                                </div>
                            </div>
<!-- Toggle DarkMode ----------------------------------------------------------------- -->
                            <div class="settings_button_wrapper">
                                <div class="settings_button_box">
                                    <button type="button" class="btn btn-default dbtools-button" id="btnPiaEnableDarkmode" onclick="askPiaEnableDarkmode()"><?php echo $pia_lang['Maintenance_Tool_darkmode']; ?></button>
                                </div>
                            </div>
<!-- Toggle History Graph ----------------------------------------------------------------- -->
                            <div class="settings_button_wrapper">
                                <div class="settings_button_box">
                                    <button type="button" class="btn btn-default dbtools-button" id="btnPiaEnableOnlineHistoryGraph" onclick="askPiaEnableOnlineHistoryGraph()"><?php echo $pia_lang['Maintenance_Tool_onlinehistorygraph']; ?></button>
                                </div>
                            </div>
<!-- Toggle Web Service Monitoring ----------------------------------------------------------------- -->
                            <div class="settings_button_wrapper">
                                <div class="settings_button_box">
                                    <button type="button" class="btn btn-default dbtools-button" id="btnPiaEnableWebServiceMon" onclick="askPiaEnableWebServiceMon()"><?php echo $pia_lang['Maintenance_Tool_webservicemon']; ?></button>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr><td colspan="2"><h4 class="bottom-border-aqua"><?php echo $pia_lang['Maintenance_Tools_Tab_Subheadline_b']; ?></h4></td></tr>
                <tr>
                    <td colspan="2" style="text-align: center;">

                        <link rel="stylesheet" href="lib/AdminLTE/plugins/iCheck/all.css">
                        <script src="lib/AdminLTE/plugins/iCheck/icheck.min.js"></script>

                        <?php $col_checkbox = set_column_checkboxes(read_DevListCol());?>

                        <div class="form-group">
                            <div class="table_settings_col_box" style="">
                              <input class="icheckbox_minimal-blue" id="chkConnectionType" type="checkbox" <?php echo $col_checkbox['ConnectionType']; ?> style="position: relative; margin-top:-3px; margin-right: 5px;">
                              <label class="control-label"><?php echo $pia_lang['Device_TableHead_ConnectionType']; ?></label>
                            </div>

                            <div class="table_settings_col_box" style="">
                              <input class="icheckbox_minimal-blue" id="chkOwner" type="checkbox" <?php echo $col_checkbox['Owner']; ?> style="position: relative; margin-top:-3px; margin-right: 5px;">
                              <label class="control-label"><?php echo $pia_lang['Device_TableHead_Owner']; ?></label>
                            </div>

                            <div class="table_settings_col_box" style="">
                              <input class="icheckbox_minimal-blue" id="chkType" type="checkbox" <?php echo $col_checkbox['Type']; ?> style="position: relative; margin-top:-3px; margin-right: 5px;">
                              <label class="control-label"><?php echo $pia_lang['Device_TableHead_Type']; ?></label>
                            </div>

                            <div class="table_settings_col_box" style="">
                              <input class="icheckbox_minimal-blue" id="chkFavorite" type="checkbox" <?php echo $col_checkbox['Favorites']; ?> style="position: relative; margin-top:-3px; margin-right: 5px;">
                              <label class="control-label"><?php echo $pia_lang['Device_TableHead_Favorite']; ?></label>
                            </div>

                            <div class="table_settings_col_box" style="">
                              <input class="icheckbox_minimal-blue" id="chkGroup" type="checkbox" <?php echo $col_checkbox['Group']; ?> style="position: relative; margin-top:-3px; margin-right: 5px;">
                              <label class="control-label"><?php echo $pia_lang['Device_TableHead_Group']; ?></label>
                            </div>

                            <div class="table_settings_col_box" style="">
                              <input class="icheckbox_minimal-blue" id="chkLocation" type="checkbox" <?php echo $col_checkbox['Location']; ?> style="position: relative; margin-top:-3px; margin-right: 5px;">
                              <label class="control-label"><?php echo $pia_lang['Device_TableHead_Location']; ?></label>
                            </div>

                            <div class="table_settings_col_box" style="">
                              <input class="icheckbox_minimal-blue" id="chkfirstSess" type="checkbox" <?php echo $col_checkbox['FirstSession']; ?> style="position: relative; margin-top:-3px; margin-right: 5px;">
                              <label class="control-label"><?php echo $pia_lang['Device_TableHead_FirstSession']; ?></label>
                            </div>

                            <div class="table_settings_col_box" style="">
                              <input class="icheckbox_minimal-blue" id="chklastSess" type="checkbox" <?php echo $col_checkbox['LastSession']; ?> style="position: relative; margin-top:-3px; margin-right: 5px;">
                              <label class="control-label"><?php echo $pia_lang['Device_TableHead_LastSession']; ?></label>
                            </div>

                            <div class="table_settings_col_box" style="">
                              <input class="icheckbox_minimal-blue" id="chklastIP" type="checkbox" <?php echo $col_checkbox['LastIP']; ?> style="position: relative; margin-top:-3px; margin-right: 5px;">
                              <label class="control-label"><?php echo $pia_lang['Device_TableHead_LastIP']; ?></label>
                            </div>

                            <div class="table_settings_col_box" style="">
                              <input class="icheckbox_minimal-blue" id="chkMACtype" type="checkbox" <?php echo $col_checkbox['MACType']; ?> style="position: relative; margin-top:-3px; margin-right: 5px;">
                              <label class="control-label"><?php echo $pia_lang['Device_TableHead_MAC']; ?></label>
                            </div>

                            <div class="table_settings_col_box" style="">
                              <input class="icheckbox_minimal-blue" id="chkMACaddress" type="checkbox" <?php echo $col_checkbox['MACAddress']; ?> style="position: relative; margin-top:-3px; margin-right: 5px;">
                              <label class="control-label"><?php echo $pia_lang['Device_TableHead_MAC']; ?>-Address</label>
                            </div>

                            <br>
                            <button type="button" class="btn btn-default" style="margin-top:10px; width:160px;" id="btnSaveDeviceListCol" onclick="askDeviceListCol()" ><?php echo $pia_lang['Gen_Save']; ?></button>
                        </div>

                    </td>
                </tr>
                <tr><td colspan="2"><h4 class="bottom-border-aqua"><?php echo $pia_lang['Maintenance_Tools_Tab_Subheadline_c']; ?></h4></td></tr>
                <tr class="table_settings_row">
                    <td class="db_info_table_cell db_tools_table_cell_a"><button type="button" class="btn btn-default dbtools-button" id="btnPiaSetAPIKey" onclick="askPiaSetAPIKey()"><?php echo $pia_lang['Maintenance_Tool_setapikey']; ?></button></td>
                    <td class="db_info_table_cell db_tools_table_cell_b"><?php echo $pia_lang['Maintenance_Tool_setapikey_text']; ?></td>
                </tr>
                <tr class="table_settings_row">
                    <td class="db_info_table_cell db_tools_table_cell_a"><button type="button" class="btn btn-default dbtools-button" id="btnTestNotific" onclick="askTestNotificationSystem()"><?php echo $pia_lang['Maintenance_Tool_test_notification']; ?></button></td>
                    <td class="db_info_table_cell db_tools_table_cell_b"><?php echo $pia_lang['Maintenance_Tool_test_notification_text']; ?></td>
                </tr>
                <tr class="table_settings_row">
                    <td class="db_info_table_cell db_tools_table_cell_a">

                        <div style="display: inline-block; text-align: center;">
                              <div class="form-group" style="width:160px; margin-bottom:5px;">
                                <!-- <div class="col-sm-7"> -->
                                  <div class="input-group">
                                    <input class="form-control" id="txtPiaArpTimer" type="text" value="<?php echo $pia_lang['Maintenance_arpscantimer_empty']; ?>" readonly >
                                    <div class="input-group-btn">
                                      <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id="dropdownButtonPiaArpTimer">
                                        <span class="fa fa-caret-down"></span></button>
                                      <ul id="dropdownPiaArpTimer" class="dropdown-menu dropdown-menu-right">
                                        <li><a href="javascript:void(0)" onclick="setTextValue('txtPiaArpTimer','15');">15min</a></li>
                                        <li><a href="javascript:void(0)" onclick="setTextValue('txtPiaArpTimer','30');">30min</a></li>
                                        <li><a href="javascript:void(0)" onclick="setTextValue('txtPiaArpTimer','60');">1h</a></li>
                                        <li><a href="javascript:void(0)" onclick="setTextValue('txtPiaArpTimer','120');">2h</a></li>
                                        <li><a href="javascript:void(0)" onclick="setTextValue('txtPiaArpTimer','720');">12h</a></li>
                                        <li><a href="javascript:void(0)" onclick="setTextValue('txtPiaArpTimer','1440');">24h</a></li>
                                        <li><a href="javascript:void(0)" onclick="setTextValue('txtPiaArpTimer','999999');">Very long</a></li>
                                      </ul>
                                    </div>
                                  </div>
                              </div>
                            </div>
                            <div style="display: block;">
                            <button type="button" class="btn btn-warning" style="margin-top:0px; width:160px; height:36px" id="btnSavePiaArpTimer" onclick="setPiAlertArpTimer()" ><div id="Timeralertspinner" class="loader disablespinner"></div>
                                <div id="TimeralertText" class=""><?php echo $pia_lang['Maintenance_Tool_arpscansw']; ?></div></button>
                            </div>
                        </div>

                    </td>
                    <td class="db_info_table_cell db_tools_table_cell_b"><?php echo $pia_lang['Maintenance_Tool_arpscansw_text']; ?></td>
                </tr>
                <tr class="table_settings_row">

<?php
if (strtolower($_SESSION['WebProtection']) != 'true') {
	echo '          <td class="db_info_table_cell db_tools_table_cell_a"><button type="button" class="btn btn-default dbtools-button" id="btnPiaLoginEnable" onclick="askPiaLoginEnable()">' . $pia_lang['Maintenance_Tool_loginenable'] . '</button></td>
                    <td class="db_info_table_cell db_tools_table_cell_b">' . $pia_lang['Maintenance_Tool_loginenable_text'] . '</td>';} else {
	echo '      <td class="db_info_table_cell db_tools_table_cell_a"><button type="button" class="btn btn-danger dbtools-button" id="btnPiaLoginDisable" onclick="askPiaLoginDisable()">' . $pia_lang['Maintenance_Tool_logindisable'] . '</button></td>
                    <td class="db_info_table_cell db_tools_table_cell_b">' . $pia_lang['Maintenance_Tool_logindisable_text'] . '</td>';}
?>

                </tr>
            </table>

        </div>
        <div class="tab-pane <?php echo $pia_tab_tool; ?>" id="tab_DBTools">
            <div class="db_info_table">
                <div class="db_info_table_row">
                    <div class="db_tools_table_cell_a" style="">
                        <button type="button" class="btn btn-default dbtools-button" id="btnDeleteMAC" onclick="askDeleteDevicesWithEmptyMACs()"><?php echo $pia_lang['Maintenance_Tool_del_empty_macs']; ?></button>
                    </div>
                    <div class="db_tools_table_cell_b"><?php echo $pia_lang['Maintenance_Tool_del_empty_macs_text']; ?></div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_tools_table_cell_a" style="">
                        <button type="button" class="btn btn-default dbtools-button" id="btnDeleteMAC" onclick="askDeleteAllDevices()"><?php echo $pia_lang['Maintenance_Tool_del_alldev']; ?></button>
                    </div>
                    <div class="db_tools_table_cell_b"><?php echo $pia_lang['Maintenance_Tool_del_alldev_text']; ?></div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_tools_table_cell_a" style="">
                        <button type="button" class="btn btn-default dbtools-button" id="btnDeleteUnknown" onclick="askDeleteUnknown()"><?php echo $pia_lang['Maintenance_Tool_del_unknowndev']; ?></button>
                    </div>
                    <div class="db_tools_table_cell_b"><?php echo $pia_lang['Maintenance_Tool_del_unknowndev_text']; ?></div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_tools_table_cell_a" style="">
                        <button type="button" class="btn btn-default dbtools-button" id="btnDeleteEvents" onclick="askDeleteEvents()"><?php echo $pia_lang['Maintenance_Tool_del_allevents']; ?></button>
                    </div>
                    <div class="db_tools_table_cell_b"><?php echo $pia_lang['Maintenance_Tool_del_allevents_text']; ?></div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_tools_table_cell_a" style="">
                        <button type="button" class="btn btn-default dbtools-button" id="btnDeleteActHistory" onclick="askDeleteActHistory()"><?php echo $pia_lang['Maintenance_Tool_del_ActHistory']; ?></button>
                    </div>
                    <div class="db_tools_table_cell_b"><?php echo $pia_lang['Maintenance_Tool_del_ActHistory_text']; ?></div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_tools_table_cell_a" style="">
                        <button type="button" class="btn btn-default dbtools-button" id="btnDeleteInactiveHosts" onclick="askDeleteInactiveHosts()"><?php echo $pia_lang['Maintenance_Tool_del_Inactive_Hosts']; ?></button>
                    </div>
                    <div class="db_tools_table_cell_b"><?php echo $pia_lang['Maintenance_Tool_del_Inactive_Hosts_text']; ?></div>
                </div>
            </div>
        </div>
        <div class="tab-pane <?php echo $pia_tab_backup; ?>" id="tab_BackupRestore">
            <div class="db_info_table">
                <div class="db_info_table_row">
                    <div class="db_tools_table_cell_a" style="">
                        <button type="button" class="btn btn-default dbtools-button" id="btnPiaBackupDBtoArchive" onclick="askPiaBackupDBtoArchive()"><?php echo $pia_lang['Maintenance_Tool_backup']; ?></button>
                    </div>
                    <div class="db_tools_table_cell_b"><?php echo $pia_lang['Maintenance_Tool_backup_text']; ?></div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_tools_table_cell_a" style="">
<?php
if (!$block_restore_button) {
	echo '<button type="button" class="btn btn-default dbtools-button" id="btnPiaRestoreDBfromArchive" onclick="askPiaRestoreDBfromArchive()">' . $pia_lang['Maintenance_Tool_restore'] . '<br>' . $LATEST_BACKUP_DATE . '</button>';
} else {
	echo '<button type="button" class="btn btn-default dbtools-button disabled" id="btnPiaRestoreDBfromArchive">' . $pia_lang['Maintenance_Tool_restore'] . '<br>' . $LATEST_BACKUP_DATE . '</button>';
}
?>
                    </div>

 <?php
if (!$block_restore_button) {
	echo '<div class="db_tools_table_cell_b">' . $pia_lang['Maintenance_Tool_restore_text'] . ' (<a href="./download/database.php">' . $pia_lang['Maintenance_Tool_latestdb_download'] . '</a>)</div>';
} else {
	echo '<div class="db_tools_table_cell_b">' . $pia_lang['Maintenance_Tool_restore_text'] . '</div>';
}
?>
                </div>
                <div class="db_info_table_row">
                    <div class="db_tools_table_cell_a" style="">
                        <button type="button" class="btn btn-default dbtools-button" id="btnPiaPurgeDBBackups" onclick="askPiaPurgeDBBackups()"><?php echo $pia_lang['Maintenance_Tool_purgebackup']; ?></button>
                    </div>
                    <div class="db_tools_table_cell_b"><?php echo $pia_lang['Maintenance_Tool_purgebackup_text']; ?></div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Config Editor ----------------------------------------------------------------- -->

 <div class="box">
        <div class="box-body" id="configeditor">
           <button type="button" id="oisggfjergfeirfj" class="btn btn-danger" data-toggle="modal" data-target="#modal-config-editor"><?php echo $pia_lang['Maintenance_ConfEditor_Start']; ?></button>
      </div>
    </div>

    <div class="box box-solid box-danger collapsed-box" style="margin-top: -15px;">
    <div class="box-header with-border" data-widget="collapse">
           <h3 class="box-title"><?php echo $pia_lang['Maintenance_ConfEditor_Hint']; ?></h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool"><i class="fa fa-plus"></i></button>
          </div>
    </div>
    <div class="box-body">
           <table class="table configeditor_help">
              <tbody>
                <tr>
                  <th scope="row" class="text-nowrap text-danger"><?php echo $pia_lang['Maintenance_ConfEditor_Restore']; ?></th>
                  <td class="db_tools_table_cell_b"><?php echo $pia_lang['Maintenance_ConfEditor_Restore_info']; ?></td>
                </tr>
                <tr>
                  <th scope="row" class="text-nowrap text-danger"><?php echo $pia_lang['Maintenance_ConfEditor_Backup']; ?></th>
                  <td class="db_tools_table_cell_b"><?php echo $pia_lang['Maintenance_ConfEditor_Backup_info']; ?></td>
                </tr>
                <tr>
                  <th scope="row" class="text-nowrap text-danger"><?php echo $pia_lang['Gen_Save']; ?></th>
                  <td class="db_tools_table_cell_b"><?php echo $pia_lang['Maintenance_ConfEditor_Save_info']; ?></td>
                </tr>
              </tbody>
            </table>
    </div>
    <!-- /.box-body -->
</div>

<!-- Config Editor - Modals ----------------------------------------------------------------- -->

    <div class="modal fade" id="modal-config-editor">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <form role="form" accept-charset="utf-8" method="post" action="./index.php">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Config Editor</h4>
                </div>
                <div class="modal-body" style="text-align: left;">
                    <textarea class="form-control" name="txtConfigFileEditor" spellcheck="false" wrap="off" style="resize: none; font-family: monospace; height: 70vh;"><?php echo file_get_contents('../config/pialert.conf'); ?></textarea>
                </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="btnPiaRestoreConfigFile" data-dismiss="modal" style="margin: 5px" onclick="askRestoreConfigFile()"><?php echo $pia_lang['Maintenance_ConfEditor_Restore']; ?></button>
                    <button type="button" class="btn btn-success" id="btnPiaBackupConfigFile" data-dismiss="modal" style="margin: 5px" onclick="BackupConfigFile()"><?php echo $pia_lang['Maintenance_ConfEditor_Backup']; ?></button>
                    <button type="submit" class="btn btn-danger" name="SubmitConfigFileEditor" value="SaveNewConfig" style="margin: 5px"><?php echo $pia_lang['Gen_Save']; ?></button>
                    <button type="button" class="btn btn-default" id="btnPiaEditorClose" data-dismiss="modal" style="margin: 5px"><?php echo $pia_lang['Gen_Close']; ?></button>
                  </div>
              </form>
            </div>
        </div>
    </div>

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
// delete devices with emty macs
function askDeleteDevicesWithEmptyMACs () {
  // Ask
  showModalWarning('<?php echo $pia_lang['Maintenance_Tool_del_empty_macs_noti']; ?>', '<?php echo $pia_lang['Maintenance_Tool_del_empty_macs_noti_text']; ?>',
    '<?php echo $pia_lang['Gen_Cancel']; ?>', '<?php echo $pia_lang['Gen_Delete']; ?>', 'deleteDevicesWithEmptyMACs');
}
function deleteDevicesWithEmptyMACs()
{
  // Delete device
  $.get('php/server/devices.php?action=deleteAllWithEmptyMACs', function(msg) {
    showMessage (msg);
  });
}

// Test Notifications
function askTestNotificationSystem () {
  // Ask
  showModalWarning('<?php echo $pia_lang['Maintenance_Tool_test_notification_noti']; ?>', '<?php echo $pia_lang['Maintenance_Tool_test_notification_noti_text']; ?>',
    '<?php echo $pia_lang['Gen_Cancel']; ?>', '<?php echo $pia_lang['Gen_Run']; ?>', 'TestNotificationSystem');
}
function TestNotificationSystem()
{
  // Delete device
  $.get('php/server/devices.php?action=TestNotificationSystem', function(msg) {
    showMessage (msg);
  });
}

// delete all devices
function askDeleteAllDevices () {
  // Ask
  showModalWarning('<?php echo $pia_lang['Maintenance_Tool_del_alldev_noti']; ?>', '<?php echo $pia_lang['Maintenance_Tool_del_alldev_noti_text']; ?>',
    '<?php echo $pia_lang['Gen_Cancel']; ?>', '<?php echo $pia_lang['Gen_Delete']; ?>', 'deleteAllDevices');
}
function deleteAllDevices()
{
  // Delete device
  $.get('php/server/devices.php?action=deleteAllDevices', function(msg) {
    showMessage (msg);
  });
}

// delete all (unknown) devices
function askDeleteUnknown () {
  // Ask
  showModalWarning('<?php echo $pia_lang['Maintenance_Tool_del_unknowndev_noti']; ?>', '<?php echo $pia_lang['Maintenance_Tool_del_unknowndev_noti_text']; ?>',
    '<?php echo $pia_lang['Gen_Cancel']; ?>', '<?php echo $pia_lang['Gen_Delete']; ?>', 'deleteUnknownDevices');
}
function deleteUnknownDevices()
{
  // Execute
  $.get('php/server/devices.php?action=deleteUnknownDevices', function(msg) {
    showMessage (msg);
  });
}

// delete all Events
function askDeleteEvents () {
  // Ask
  showModalWarning('<?php echo $pia_lang['Maintenance_Tool_del_allevents_noti']; ?>', '<?php echo $pia_lang['Maintenance_Tool_del_allevents_noti_text']; ?>',
    '<?php echo $pia_lang['Gen_Cancel']; ?>', '<?php echo $pia_lang['Gen_Delete']; ?>', 'deleteEvents');
}
function deleteEvents()
{
  // Execute
  $.get('php/server/devices.php?action=deleteEvents', function(msg) {
    showMessage (msg);
  });
}

// delete Hostory
function askDeleteActHistory () {
  // Ask
  showModalWarning('<?php echo $pia_lang['Maintenance_Tool_del_ActHistory_noti']; ?>', '<?php echo $pia_lang['Maintenance_Tool_del_ActHistory_noti_text']; ?>',
    '<?php echo $pia_lang['Gen_Cancel']; ?>', '<?php echo $pia_lang['Gen_Delete']; ?>', 'deleteActHistory');
}
function deleteActHistory()
{
  // Execute
  $.get('php/server/devices.php?action=deleteActHistory', function(msg) {
    showMessage (msg);
  });
}

// Backup DB to Archive
function askPiaBackupDBtoArchive () {
  // Ask
  showModalWarning('<?php echo $pia_lang['Maintenance_Tool_backup_noti']; ?>', '<?php echo $pia_lang['Maintenance_Tool_backup_noti_text']; ?>',
    '<?php echo $pia_lang['Gen_Cancel']; ?>', '<?php echo $pia_lang['Gen_Backup']; ?>', 'PiaBackupDBtoArchive');
}
function PiaBackupDBtoArchive()
{
  // Execute
  $.get('php/server/devices.php?action=PiaBackupDBtoArchive', function(msg) {
    showMessage (msg);
  });
}

// Restore DB from Archive
function askPiaRestoreDBfromArchive () {
  // Ask
  showModalWarning('<?php echo $pia_lang['Maintenance_Tool_restore_noti']; ?>', '<?php echo $pia_lang['Maintenance_Tool_restore_noti_text']; ?>',
    '<?php echo $pia_lang['Gen_Cancel']; ?>', '<?php echo $pia_lang['Gen_Restore']; ?>', 'PiaRestoreDBfromArchive');
}
function PiaRestoreDBfromArchive()
{
  // Execute
  $.get('php/server/devices.php?action=PiaRestoreDBfromArchive', function(msg) {
    showMessage (msg);
  });
}

// Purge Backups
function askPiaPurgeDBBackups() {
  // Ask
  showModalWarning('<?php echo $pia_lang['Maintenance_Tool_purgebackup_noti']; ?>', '<?php echo $pia_lang['Maintenance_Tool_purgebackup_noti_text']; ?>',
    '<?php echo $pia_lang['Gen_Cancel']; ?>', '<?php echo $pia_lang['Gen_Purge']; ?>', 'PiaPurgeDBBackups');
}
function PiaPurgeDBBackups()
{
  // Execute
  $.get('php/server/devices.php?action=PiaPurgeDBBackups', function(msg) {
    showMessage (msg);
  });
}

// Switch Darkmode
function askPiaEnableDarkmode() {
  // Ask
  showModalWarning('<?php echo $pia_lang['Maintenance_Tool_darkmode_noti']; ?>', '<?php echo $pia_lang['Maintenance_Tool_darkmode_noti_text']; ?>',
    '<?php echo $pia_lang['Gen_Cancel']; ?>', '<?php echo $pia_lang['Gen_Switch']; ?>', 'PiaEnableDarkmode');
}
function PiaEnableDarkmode()
{
  // Execute
  $.get('php/server/devices.php?action=PiaEnableDarkmode', function(msg) {
    showMessage (msg);
  });
}

// Switch Web Service Monitor
function askPiaEnableWebServiceMon() {
  // Ask
  showModalWarning('<?php echo $pia_lang['Maintenance_Tool_webservicemon_noti']; ?>', '<?php echo $pia_lang['Maintenance_Tool_webservicemon_noti_text']; ?>',
    '<?php echo $pia_lang['Gen_Cancel']; ?>', '<?php echo $pia_lang['Gen_Switch']; ?>', 'PiaEnableWebServiceMon');
}
function PiaEnableWebServiceMon()
{
  // Execute
  $.get('php/server/devices.php?action=EnableWebServiceMon', function(msg) {
    showMessage (msg);
  });
}

// Toggle Graph
function askPiaEnableOnlineHistoryGraph() {
  // Ask
  showModalWarning('<?php echo $pia_lang['Maintenance_Tool_onlinehistorygraph_noti']; ?>', '<?php echo $pia_lang['Maintenance_Tool_onlinehistorygraph_noti_text']; ?>',
    '<?php echo $pia_lang['Gen_Cancel']; ?>', '<?php echo $pia_lang['Gen_Switch']; ?>', 'PiaEnableOnlineHistoryGraph');
}
function PiaEnableOnlineHistoryGraph()
{
  // Execute
  $.get('php/server/devices.php?action=PiaEnableOnlineHistoryGraph', function(msg) {
    showMessage (msg);
  });
}

// Set API-Key
function askPiaSetAPIKey() {
  // Ask
  showModalWarning('<?php echo $pia_lang['Maintenance_Tool_setapikey_noti']; ?>', '<?php echo $pia_lang['Maintenance_Tool_setapikey_noti_text']; ?>',
    '<?php echo $pia_lang['Gen_Cancel']; ?>', '<?php echo $pia_lang['Gen_Okay']; ?>', 'PiaSetAPIKey');
}
function PiaSetAPIKey()
{
  // Execute
  $.get('php/server/devices.php?action=PiaSetAPIKey', function(msg) {
    showMessage (msg);
  });
}

// Enable Login
function askPiaLoginEnable() {
  // Ask
  showModalWarning('<?php echo $pia_lang['Maintenance_Tool_loginenable_noti']; ?>', '<?php echo $pia_lang['Maintenance_Tool_loginenable_noti_text']; ?>',
    '<?php echo $pia_lang['Gen_Cancel']; ?>', '<?php echo $pia_lang['Gen_Switch']; ?>', 'PiaLoginEnable');
}
function PiaLoginEnable()
{
  // Execute
  $.get('php/server/devices.php?action=PiaLoginEnable', function(msg) {
    showMessage (msg);
  });
}

// Disable Login
function askPiaLoginDisable() {
  // Ask
  showModalWarning('<?php echo $pia_lang['Maintenance_Tool_logindisable_noti']; ?>', '<?php echo $pia_lang['Maintenance_Tool_logindisable_noti_text']; ?>',
    '<?php echo $pia_lang['Gen_Cancel']; ?>', '<?php echo $pia_lang['Gen_Switch']; ?>', 'PiaLoginDisable');
}
function PiaLoginDisable()
{
  // Execute
  $.get('php/server/devices.php?action=PiaLoginDisable', function(msg) {
    showMessage (msg);
  });
}

function setTextValue (textElement, textValue) {
  $('#'+textElement).val (textValue);
}

// Set Theme
function setPiAlertTheme () {
  // update data to server
  $.get('php/server/devices.php?action=setPiAlertTheme&PiaSkinSelection='+ $('#txtSkinSelection').val(), function(msg) {
    showMessage (msg);
  });
}

// Set Language
function setPiAlertLanguage() {
  // update data to server
  $.get('php/server/devices.php?action=setPiAlertLanguage&PiaLangSelection='+ $('#txtLangSelection').val(), function(msg) {
    showMessage (msg);
  });
}

// Set ArpScanTimer
function setPiAlertArpTimer() {
  $.ajax({
        method: "GET",
        url: "./php/server/devices.php?action=setPiAlertArpTimer&PiaArpTimer=" + $('#txtPiaArpTimer').val(),
        data: "",
        beforeSend: function() { $('#Timeralertspinner').removeClass("disablespinner"); $('#TimeralertText').addClass("disablespinner");  },
        complete: function() { $('#Timeralertspinner').addClass("disablespinner"); $('#TimeralertText').removeClass("disablespinner"); },
        success: function(data, textStatus) {
            showMessage (data);
        }
    })
}

// Backup Configfile
function BackupConfigFile()  {
  // Execute
  $.get('php/server/devices.php?action=BackupConfigFile', function(msg) {
    showMessage (msg);
  });
}

// Restore Configfile
function askRestoreConfigFile() {
  // Ask
  showModalWarning('<?php echo $pia_lang['Maintenance_ConfEditor_Restore_noti']; ?>', '<?php echo $pia_lang['Maintenance_ConfEditor_Restore_noti_text']; ?>',
    '<?php echo $pia_lang['Gen_Cancel']; ?>', '<?php echo $pia_lang['Gen_Run']; ?>', 'RestoreConfigFile');
}
function RestoreConfigFile() {
  // Execute
  $.get('php/server/devices.php?action=RestoreConfigFile', function(msg) {
    showMessage (msg);
  });
}

// Set Device List Column
function askDeviceListCol() {
  // Ask
  showModalWarning('<?php echo $pia_lang['Maintenance_Tool_DevListCol_noti']; ?>', '<?php echo $pia_lang['Maintenance_Tool_DevListCol_noti_text']; ?>',
    '<?php echo $pia_lang['Gen_Cancel']; ?>', '<?php echo $pia_lang['Gen_Save']; ?>', 'setDeviceListCol');
}
function setDeviceListCol() {
  // Execute
    $.get('php/server/devices.php?action=setDeviceListCol&'
    + '&connectiontype=' + ($('#chkConnectionType')[0].checked * 1)
    + '&favorite='       + ($('#chkFavorite')[0].checked * 1)
    + '&group='          + ($('#chkGroup')[0].checked * 1)
    + '&type='           + ($('#chkType')[0].checked * 1)
    + '&owner='          + ($('#chkOwner')[0].checked * 1)
    + '&firstsess='      + ($('#chkfirstSess')[0].checked * 1)
    + '&lastsess='       + ($('#chklastSess')[0].checked * 1)
    + '&lastip='         + ($('#chklastIP')[0].checked * 1)
    + '&mactype='        + ($('#chkMACtype')[0].checked * 1)
    + '&macaddress='     + ($('#chkMACaddress')[0].checked * 1)
    + '&location='       + ($('#chkLocation')[0].checked * 1)
    , function(msg) {
    showMessage (msg);
  });
}

// Delete Inactive Hosts
function askDeleteInactiveHosts() {
  // Ask
  showModalWarning('<?php echo $pia_lang['Maintenance_Tool_del_Inactive_Hosts']; ?>', '<?php echo $pia_lang['Maintenance_Tool_del_Inactive_Hosts_text']; ?>',
    '<?php echo $pia_lang['Gen_Cancel']; ?>', '<?php echo $pia_lang['Gen_Delete']; ?>', 'DeleteInactiveHosts');
}
function DeleteInactiveHosts() {
  // Execute
  $.get('php/server/devices.php?action=DeleteInactiveHosts', function(msg) {
    showMessage (msg);
  });
}

// Update Check
function check_github_for_updates() {
    $("#updatecheck").empty();
    $.ajax({
        method: "POST",
        url: "./php/server/updatecheck.php",
        data: "",
        beforeSend: function() { $('#updatecheck').addClass("ajax_scripts_loading"); },
        complete: function() { $('#updatecheck').removeClass("ajax_scripts_loading"); },
        success: function(data, textStatus) {
            $("#updatecheck").html(data);
        }
    })
}

// Update URL when using the tabs
function update_tabURL(url, tab) {
    let stateObj = { id: "100" };

    url = url.replace('?tab=1','');
    url = url.replace('?tab=2','');
    url = url.replace('?tab=3','');
    url = url.replace('#','');
    window.history.pushState(stateObj,
             "Tab"+tab, url + "?tab=" + tab);
}
</script>


