<!-- ---------------------------------------------------------------------------
#  Pi.Alert
#  Open Source Network Guard / WIFI & LAN intrusion detector
#
#  maintenance.php - Front module. Server side. Manage Devices
#-------------------------------------------------------------------------------
#  Puche      2021        pi.alert.application@gmail.com   GNU GPLv3
#  jokob-sk   2022        jokob.sk@gmail.com               GNU GPLv3
#  leiweibau  2023        https://github.com/leiweibau     GNU GPLv3
#--------------------------------------------------------------------------- -->

<?php
session_start();
error_reporting(0);

if ($_SESSION["login"] != 1) {
	header('Location: ./index.php');
	exit;
}

require 'php/templates/header.php';
require 'php/server/journal.php';

?>
<!-- Page ------------------------------------------------------------------ -->
<div class="content-wrapper">

<!-- Content header--------------------------------------------------------- -->
    <section class="content-header">
    <?php require 'php/templates/notification.php';?>
      <h1 id="pageTitle">
         <?=$pia_lang['Maintenance_Title'];?>
      </h1>
    </section>

    <!-- Main content ---------------------------------------------------------- -->
    <section class="content">

<?php
// Get API-Key ------------------------------------------------------------------
$APIKEY = get_config_parmeter('PIALERT_APIKEY');
if ($APIKEY == "") {$APIKEY = $pia_lang['Maintenance_Tool_setapikey_false'];}

// Get Ignore List ------------------------------------------------------------------
$MAC_IGNORE_LIST_LINE = get_config_parmeter('MAC_IGNORE_LIST');
if ($MAC_IGNORE_LIST_LINE == "" || $MAC_IGNORE_LIST_LINE == "[]") {$MAC_IGNORE_LIST = $pia_lang['Maintenance_Tool_ignorelist_false'];} else {
	$MAC_IGNORE_LIST = str_replace("[", "", str_replace("]", "", str_replace("'", "", trim($MAC_IGNORE_LIST_LINE))));
	$MAC_IGNORE_LIST = str_replace(",", ", ", trim($MAC_IGNORE_LIST));
}

// Get Notification Settings ------------------------------------------------------------------
$CONFIG_FILE_SOURCE = "../config/pialert.conf";
$CONFIG_FILE_KEY_LINE = file($CONFIG_FILE_SOURCE);
$CONFIG_FILE_FILTER_VALUE_ARP = array_values(preg_grep("/(REPORT_MAIL|REPORT_NTFY|REPORT_WEBGUI|REPORT_PUSHSAFER|REPORT_PUSHOVER|REPORT_TELEGRAM)(?!_)/i", $CONFIG_FILE_KEY_LINE));
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

// Find latest DB Backup for restore and download -----------------------------------
$LATEST_FILES = glob($ARCHIVE_PATH . "pialertdb_*.zip");
if (sizeof($LATEST_FILES) == 0) {
	$LATEST_BACKUP_DATE = $pia_lang['Maintenance_Tool_restore_blocked'];
	$block_restore_button_db = true;
} else {
	natsort($LATEST_FILES);
	$LATEST_FILES = array_reverse($LATEST_FILES, False);
	$LATEST_BACKUP = $LATEST_FILES[0];
	$LATEST_BACKUP_DATE = date("Y-m-d H:i:s", filemtime($LATEST_BACKUP));
}

// Aprscan read Timer -----------------------------------------------------------------
function read_arpscan_timer() {
	$file = '../db/setting_stoppialert';
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

// Read Vendor logfiles -----------------------------------------------------------------
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

// Top Modal Block -----------------------------------------------------------------
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
                    <div class="main_logviwer_log" style="max-height: 70vh;">';
}

// Bottom Modal Block -----------------------------------------------------------------
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
                    <div class="db_info_table_cell" style="min-width: 140px"><?=$pia_lang['Maintenance_database_path'];?></div>
                    <div class="db_info_table_cell" style="width: 70%">
                        <?=$DB_SOURCE;?>
                    </div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_info_table_cell"><?=$pia_lang['Maintenance_database_size'];?></div>
                    <div class="db_info_table_cell">
                        <?=$DB_SIZE_DATA;?>
                    </div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_info_table_cell"><?=$pia_lang['Maintenance_database_lastmod'];?></div>
                    <div class="db_info_table_cell">
                        <?=$DB_MOD_DATA;?>
                    </div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_info_table_cell"><?=$pia_lang['Maintenance_database_backup'];?></div>
                    <div class="db_info_table_cell">
                        <?=$ARCHIVE_COUNT . ' ' . $pia_lang['Maintenance_database_backup_found'] . ' / ' . $pia_lang['Maintenance_database_backup_total'] . ': ' . $ARCHIVE_DISKUSAGE;?>
                    </div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_info_table_cell"><?=$pia_lang['Maintenance_config_backup'];?></div>
                    <div class="db_info_table_cell">
                        <?=$CONFIG_FILE_COUNT . ' ' . $pia_lang['Maintenance_database_backup_found'];?>
                    </div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_info_table_cell"><?=$pia_lang['Maintenance_arp_status'];?></div>
                    <div class="db_info_table_cell">
                        <?=$_SESSION['arpscan_result'];
read_arpscan_timer();?></div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_info_table_cell">Api-Key</div>
                    <div class="db_info_table_cell" style="overflow-wrap: anywhere;">
                        <input readonly value="<?=$APIKEY;?>" style="width:100%; overflow-x: scroll; border: none; background: transparent; margin: 0px; padding: 0px;">
                    </div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_info_table_cell"><?=$pia_lang['Maintenance_notification_config'];?></div>
                    <div class="db_info_table_cell">
                        <?=format_notifications($CONFIG_FILE_FILTER_VALUE_ARP);?>
                    </div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_info_table_cell"><?=$pia_lang['Maintenance_notification_config_webmon'];?></div>
                    <div class="db_info_table_cell">
                        <?=format_notifications($CONFIG_FILE_FILTER_VALUE_WEB);?>
                    </div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_info_table_cell"><?=$pia_lang['Maintenance_Tool_ignorelist'];?></div>
                    <div class="db_info_table_cell">
                        <?=$MAC_IGNORE_LIST;?>
                    </div>
                </div>
            </div>
        </div>
          <!-- /.box-body -->
    </div>

      </div>
    </div>

<!-- Log Viewer ----------------------------------------------------------------- -->

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Log Viewer</h3>
        </div>
        <div class="box-body main_logviwer_buttonbox" id="logviewer">
            <button type="button" id="oisjmofeirfj" class="btn btn-primary main_logviwer_button_m" data-toggle="modal" data-target="#modal-logviewer-scan"><?=$pia_lang['Maintenance_Tools_Logviewer_Scan'];?></button>
            <button type="button" id="wefwfwefewdf" class="btn btn-primary main_logviwer_button_m" data-toggle="modal" data-target="#modal-logviewer-iplog"><?=$pia_lang['Maintenance_Tools_Logviewer_IPLog'];?></button>
            <button type="button" id="tzhrsreawefw" class="btn btn-primary main_logviwer_button_m" data-toggle="modal" data-target="#modal-logviewer-vendor"><?=$pia_lang['Maintenance_Tools_Logviewer_Vendor'];?></button>
            <button type="button" id="arzuozhrsfga" class="btn btn-primary main_logviwer_button_m" data-toggle="modal" data-target="#modal-logviewer-cleanup"><?=$pia_lang['Maintenance_Tools_Logviewer_Cleanup'];?></button>
            <button type="button" id="ufiienfflgze" class="btn btn-primary main_logviwer_button_m" data-toggle="modal" data-target="#modal-logviewer-nmap"><?=$pia_lang['Maintenance_Tools_Logviewer_Nmap'];?></button>

<?php
if ($_SESSION['Scan_WebServices'] == True) {
	echo '<button type="button" id="erftttwrdwqqq" class="btn btn-primary main_logviwer_button_m" data-toggle="modal" data-target="#modal-logviewer-webservices">' . $pia_lang['Maintenance_Tools_Logviewer_WebServices'] . '</button>';
}
?>
      </div>
    </div>

<?php
// Log Viewer - Modals Scan
print_logviewer_modal_head('scan', 'pialert.1.log (File)');
read_logfile('pialert.1.log', $pia_lang['Maintenance_Tools_Logviewer_Scan_empty']);
print_logviewer_modal_foot();

// Log Viewer - Modals IP
print_logviewer_modal_head('iplog', 'pialert.IP.log (File)');
read_logfile('pialert.IP.log', $pia_lang['Maintenance_Tools_Logviewer_IPLog_empty']);
print_logviewer_modal_foot();

// Log Viewer - Modals Vendor Update
print_logviewer_modal_head('vendor', 'pialert.vendors.log (File)');
read_logfile_vendor();
print_logviewer_modal_foot();

// Log Viewer - Modals Cleanup
print_logviewer_modal_head('cleanup', 'pialert.cleanup.log (File)');
read_logfile('pialert.cleanup.log', $pia_lang['Maintenance_Tools_Logviewer_Cleanup_empty']);
print_logviewer_modal_foot();

// Log Viewer - Modals Nmap
print_logviewer_modal_head('nmap', 'last Nmap Scan (Memory)');
if (!isset($_SESSION['ScanShortMem_NMAP'])) {echo $pia_lang['Maintenance_Tools_Logviewer_Nmap_empty'];} else {echo $_SESSION['ScanShortMem_NMAP'];}
print_logviewer_modal_foot();

// Log Viewer - Modals WebServices
if ($_SESSION['Scan_WebServices'] == True) {
	print_logviewer_modal_head('webservices', 'pialert.webservices.log (File)');
	read_logfile('pialert.webservices.log', $pia_lang['Maintenance_Tools_Logviewer_WebServices_empty']);
	print_logviewer_modal_foot();
}
?>

<!-- Tabs ----------------------------------------------------------------- -->
    <div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="<?=$pia_tab_setting?>"><a href="#tab_Settings" data-toggle="tab" onclick="update_tabURL(window.location.href,'1')"><?=$pia_lang['Maintenance_Tools_Tab_Settings']?></a></li>
        <li class="<?=$pia_tab_tool?>"><a href="#tab_DBTools" data-toggle="tab" onclick="update_tabURL(window.location.href,'2')"><?=$pia_lang['Maintenance_Tools_Tab_Tools']?></a></li>
        <li class="<?=$pia_tab_backup?>"><a href="#tab_BackupRestore" data-toggle="tab" onclick="update_tabURL(window.location.href,'3')"><?=$pia_lang['Maintenance_Tools_Tab_BackupRestore']?></a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane <?=$pia_tab_setting;?>" id="tab_Settings">
            <table class="table_settings">
                <tr><td colspan="2"><h4 class="bottom-border-aqua"><?=$pia_lang['Maintenance_Tools_Tab_Subheadline_a'];?></h4></td></tr>
                <tr class="table_settings">
                    <td class="db_info_table_cell" colspan="2" style="text-align: justify;"><?=$pia_lang['Maintenance_Tools_Tab_Settings_Intro'];?></td>
                </tr>
                <tr class="table_settings_row">
                    <td class="db_info_table_cell" colspan="2" style="padding-bottom: 20px;">
                        <div style="display: flex; justify-content: center; flex-wrap: wrap;">
<!-- Language Selection ----------------------------------------------------------------- -->
                            <div class="settings_button_wrapper">
                                <div class="settings_button_box">
                                    <div class="form-group" style="width:160px; margin-bottom:5px;">
                                      <div class="input-group">
                                        <input class="form-control" id="txtLangSelection" type="text" value="<?=$pia_lang['Maintenance_lang_selector_empty'];?>" readonly >
                                        <div class="input-group-btn">
                                          <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id="dropdownButtonLangSelection">
                                            <span class="fa fa-caret-down"></span></button>
                                          <ul id="dropdownLangSelection" class="dropdown-menu dropdown-menu-right">
                                            <li><a href="javascript:void(0)" onclick="setTextValue('txtLangSelection','en_us');"><?=$pia_lang['Maintenance_lang_en_us'];?></a></li>
                                            <li><a href="javascript:void(0)" onclick="setTextValue('txtLangSelection','de_de');"><?=$pia_lang['Maintenance_lang_de_de'];?></a></li>
                                            <li><a href="javascript:void(0)" onclick="setTextValue('txtLangSelection','fr_fr');"><?=$pia_lang['Maintenance_lang_fr_fr'];?></a></li>
                                            <li><a href="javascript:void(0)" onclick="setTextValue('txtLangSelection','es_es');"><?=$pia_lang['Maintenance_lang_es_es'];?></a></li>
                                          </ul>
                                        </div>
                                      </div>
                                    </div>
                                    <button type="button" class="btn btn-default" style="margin-top:0px; width:160px;" id="btnSaveLangSelection" onclick="setPiAlertLanguage()" ><?=$pia_lang['Maintenance_lang_selector_apply'];?> </button>
                                </div>
                            </div>
<!-- Theme Selection ----------------------------------------------------------------- -->
                            <div class="settings_button_wrapper">
                                <div class="settings_button_box">
                                    <div class="form-group" style="width:160px; margin-bottom:5px;">
                                      <div class="input-group">
                                        <input class="form-control" id="txtSkinSelection" type="text" value="<?=$pia_lang['Maintenance_themeselector_empty'];?>" readonly >
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
                                    <button type="button" class="btn btn-default" style="margin-top:0px; width:160px;" id="btnSaveSkinSelection" onclick="setPiAlertTheme()" ><?=$pia_lang['Maintenance_themeselector_apply'];?> </button>
                                </div>
                            </div>
<!-- Toggle DarkMode ----------------------------------------------------------------- -->
                            <div class="settings_button_wrapper">
                                <div class="settings_button_box">
                                	<?php $state = convert_state($ENABLED_DARKMODE, 1);?>
                                    <button type="button" class="btn btn-default dbtools-button" id="btnPiaEnableDarkmode" onclick="askPiaEnableDarkmode()"><?=$pia_lang['Maintenance_Tool_darkmode'] . '<br>' . $state;?></button>
                                </div>
                            </div>
<!-- Toggle History Graph ----------------------------------------------------------------- -->
                            <div class="settings_button_wrapper">
                                <div class="settings_button_box">
                                	<?php $state = convert_state($ENABLED_HISTOY_GRAPH, 1);?>
                                    <button type="button" class="btn btn-default dbtools-button" id="btnPiaEnableOnlineHistoryGraph" onclick="askPiaEnableOnlineHistoryGraph()"><?=$pia_lang['Maintenance_Tool_onlinehistorygraph'] . '<br>' . $state;?></button>
                                </div>
                            </div>
<!-- Toggle Web Service Monitoring ----------------------------------------------------------------- -->
                            <div class="settings_button_wrapper">
                                <div class="settings_button_box">
                                	<?php $state = convert_state($_SESSION['Scan_WebServices'], 1);?>
                                    <button type="button" class="btn btn-default dbtools-button" id="btnPiaEnableWebServiceMon" onclick="askPiaEnableWebServiceMon()"><?=$pia_lang['Maintenance_Tool_webservicemon'] . '<br>' . $state;?></button>
                                </div>
                            </div>
<!-- Toggle ICMP Monitoring ----------------------------------------------------------------- -->
                            <div class="settings_button_wrapper">
                                <div class="settings_button_box">
                                	<?php $state = convert_state($_SESSION['ICMPScan'], 1);?>
                                    <button type="button" class="btn btn-default dbtools-button" id="btnPiaEnableICMPMon" onclick="askPiaEnableICMPMon()"><?=$pia_lang['Maintenance_Tool_icmpmon'] . '<br>' . $state;?></button>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr><td colspan="2"><h4 class="bottom-border-aqua"><?=$pia_lang['Maintenance_Tools_Tab_Subheadline_b'];?></h4></td></tr>
                <tr>
                    <td colspan="2" style="text-align: center;">
                        <?php $col_checkbox = set_column_checkboxes(read_DevListCol());?>
                        <div class="form-group">
                            <div class="table_settings_col_box" style="">
                              <input class="checkbox blue" id="chkConnectionType" type="checkbox" <?=$col_checkbox['ConnectionType'];?>>
                              <label class="control-label" style="margin-left: 5px"><?=$pia_lang['Device_TableHead_ConnectionType'];?></label>
                            </div>
                            <div class="table_settings_col_box" style="">
                              <input class="checkbox blue" id="chkOwner" type="checkbox" <?=$col_checkbox['Owner'];?>>
                              <label class="control-label" style="margin-left: 5px"><?=$pia_lang['Device_TableHead_Owner'];?></label>
                            </div>
                            <div class="table_settings_col_box" style="">
                              <input class="checkbox blue" id="chkType" type="checkbox" <?=$col_checkbox['Type'];?>>
                              <label class="control-label" style="margin-left: 5px"><?=$pia_lang['Device_TableHead_Type'];?></label>
                            </div>
                            <div class="table_settings_col_box" style="">
                              <input class="checkbox blue" id="chkFavorite" type="checkbox" <?=$col_checkbox['Favorites'];?>>
                              <label class="control-label" style="margin-left: 5px"><?=$pia_lang['Device_TableHead_Favorite'];?></label>
                            </div>
                            <div class="table_settings_col_box" style="">
                              <input class="checkbox blue" id="chkGroup" type="checkbox" <?=$col_checkbox['Group'];?>>
                              <label class="control-label" style="margin-left: 5px"><?=$pia_lang['Device_TableHead_Group'];?></label>
                            </div>
                            <div class="table_settings_col_box" style="">
                              <input class="checkbox blue" id="chkLocation" type="checkbox" <?=$col_checkbox['Location'];?>>
                              <label class="control-label" style="margin-left: 5px"><?=$pia_lang['Device_TableHead_Location'];?></label>
                            </div>
                            <div class="table_settings_col_box" style="">
                              <input class="checkbox blue" id="chkfirstSess" type="checkbox" <?=$col_checkbox['FirstSession'];?>>
                              <label class="control-label" style="margin-left: 5px"><?=$pia_lang['Device_TableHead_FirstSession'];?></label>
                            </div>
                            <div class="table_settings_col_box" style="">
                              <input class="checkbox blue" id="chklastSess" type="checkbox" <?=$col_checkbox['LastSession'];?>>
                              <label class="control-label" style="margin-left: 5px"><?=$pia_lang['Device_TableHead_LastSession'];?></label>
                            </div>
                            <div class="table_settings_col_box" style="">
                              <input class="checkbox blue" id="chklastIP" type="checkbox" <?=$col_checkbox['LastIP'];?>>
                              <label class="control-label" style="margin-left: 5px"><?=$pia_lang['Device_TableHead_LastIP'];?></label>
                            </div>
                            <div class="table_settings_col_box" style="">
                              <input class="checkbox blue" id="chkMACtype" type="checkbox" <?=$col_checkbox['MACType'];?>>
                              <label class="control-label" style="margin-left: 5px"><?=$pia_lang['Device_TableHead_MAC'];?></label>
                            </div>
                            <div class="table_settings_col_box" style="">
                              <input class="checkbox blue" id="chkMACaddress" type="checkbox" <?=$col_checkbox['MACAddress'];?>>
                              <label class="control-label" style="margin-left: 5px"><?=$pia_lang['Device_TableHead_MAC'];?>-Address</label>
                            </div>
                            <br>
                            <button type="button" class="btn btn-default" style="margin-top:10px; width:160px;" id="btnSaveDeviceListCol" onclick="askDeviceListCol()" ><?=$pia_lang['Gen_Save'];?></button>
                        </div>
                    </td>
                </tr>
                <tr><td colspan="2"><h4 class="bottom-border-aqua"><?=$pia_lang['Maintenance_Tools_Tab_Subheadline_c'];?></h4></td></tr>
                <tr class="table_settings_row">
                    <td class="db_info_table_cell db_tools_table_cell_a"><button type="button" class="btn btn-default dbtools-button" id="btnPiaSetAPIKey" onclick="askPiaSetAPIKey()"><?=$pia_lang['Maintenance_Tool_setapikey'];?></button></td>
                    <td class="db_info_table_cell db_tools_table_cell_b"><?=$pia_lang['Maintenance_Tool_setapikey_text'];?></td>
                </tr>
                <tr class="table_settings_row">
                    <td class="db_info_table_cell db_tools_table_cell_a"><button type="button" class="btn btn-default dbtools-button" id="btnTestNotific" onclick="askTestNotificationSystem()"><?=$pia_lang['Maintenance_Tool_test_notification'];?></button></td>
                    <td class="db_info_table_cell db_tools_table_cell_b"><?=$pia_lang['Maintenance_Tool_test_notification_text'];?></td>
                </tr>
                <tr class="table_settings_row">
                    <td class="db_info_table_cell db_tools_table_cell_a">

                        <div style="display: inline-block; text-align: center;">
                              <div class="form-group" style="width:160px; margin-bottom:5px;">
                                <!-- <div class="col-sm-7"> -->
                                  <div class="input-group">
                                    <input class="form-control" id="txtPiaArpTimer" type="text" value="<?=$pia_lang['Maintenance_arpscantimer_empty'];?>" readonly >
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
                                <div id="TimeralertText" class=""><?=$pia_lang['Maintenance_Tool_arpscansw'];?></div></button>
                            </div>
                        </div>

                    </td>
                    <td class="db_info_table_cell db_tools_table_cell_b"><?=$pia_lang['Maintenance_Tool_arpscansw_text'];?></td>
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
        <div class="tab-pane <?=$pia_tab_tool;?>" id="tab_DBTools">
            <div class="db_info_table">
                <div class="db_info_table_row">
                    <div class="db_tools_table_cell_a" style="">
                        <button type="button" class="btn btn-default dbtools-button" id="btnDeleteMAC" onclick="askDeleteAllDevices()"><?=$pia_lang['Maintenance_Tool_del_alldev'];?></button>
                    </div>
                    <div class="db_tools_table_cell_b"><?=$pia_lang['Maintenance_Tool_del_alldev_text'];?></div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_tools_table_cell_a" style="">
                        <button type="button" class="btn btn-default dbtools-button" id="btnDeleteUnknown" onclick="askDeleteUnknown()"><?=$pia_lang['Maintenance_Tool_del_unknowndev'];?></button>
                    </div>
                    <div class="db_tools_table_cell_b"><?=$pia_lang['Maintenance_Tool_del_unknowndev_text'];?></div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_tools_table_cell_a" style="">
                        <button type="button" class="btn btn-default dbtools-button" id="btnDeleteEvents" onclick="askDeleteEvents()"><?=$pia_lang['Maintenance_Tool_del_allevents'];?></button>
                    </div>
                    <div class="db_tools_table_cell_b"><?=$pia_lang['Maintenance_Tool_del_allevents_text'];?></div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_tools_table_cell_a" style="">
                        <button type="button" class="btn btn-default dbtools-button" id="btnDeleteActHistory" onclick="askDeleteActHistory()"><?=$pia_lang['Maintenance_Tool_del_ActHistory'];?></button>
                    </div>
                    <div class="db_tools_table_cell_b"><?=$pia_lang['Maintenance_Tool_del_ActHistory_text'];?></div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_tools_table_cell_a" style="">
                        <button type="button" class="btn btn-default dbtools-button" id="btnDeleteInactiveHosts" onclick="askDeleteInactiveHosts()"><?=$pia_lang['Maintenance_Tool_del_Inactive_Hosts'];?></button>
                    </div>
                    <div class="db_tools_table_cell_b"><?=$pia_lang['Maintenance_Tool_del_Inactive_Hosts_text'];?></div>
                </div>
            </div>
        </div>
        <div class="tab-pane <?=$pia_tab_backup;?>" id="tab_BackupRestore">
            <div class="db_info_table">
				<div class="db_info_table_row">
                    <div class="db_tools_table_cell_a" style="">
                        <button type="button" class="btn btn-default dbtools-button" id="btnPiaBackupConfigFile" onclick="BackupConfigFile('yes')"><?=$pia_lang['Maintenance_Tool_ConfBackup'];?></button>
                    </div>
                    <div class="db_tools_table_cell_b"><?=$pia_lang['Maintenance_Tool_ConfBackup_text'];?></div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_tools_table_cell_a" style="">
                        <button type="button" class="btn btn-default dbtools-button" id="btnPiaBackupDBtoArchive" onclick="askPiaBackupDBtoArchive()"><?=$pia_lang['Maintenance_Tool_backup'];?></button>
                    </div>
                    <div class="db_tools_table_cell_b"><?=$pia_lang['Maintenance_Tool_backup_text'];?></div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_tools_table_cell_a" style="">
<?php
if (!$block_restore_button_db) {
	echo '<button type="button" class="btn btn-default dbtools-button" id="btnPiaRestoreDBfromArchive" onclick="askPiaRestoreDBfromArchive()">' . $pia_lang['Maintenance_Tool_restore'] . '<br>' . $LATEST_BACKUP_DATE . '</button>';
} else {
	echo '<button type="button" class="btn btn-default dbtools-button disabled" id="btnPiaRestoreDBfromArchive">' . $pia_lang['Maintenance_Tool_restore'] . '<br>' . $LATEST_BACKUP_DATE . '</button>';
}
?>
                    </div>
                    <div class="db_tools_table_cell_b"><?=$pia_lang['Maintenance_Tool_restore_text'];?></div>
                </div>
                <div class="db_info_table_row">
                    <div class="db_tools_table_cell_a" style="">
                        <button type="button" class="btn btn-default dbtools-button" id="btnPiaPurgeDBBackups" onclick="askPiaPurgeDBBackups()"><?=$pia_lang['Maintenance_Tool_purgebackup'];?></button>
                    </div>
                    <div class="db_tools_table_cell_b"><?=$pia_lang['Maintenance_Tool_purgebackup_text'];?></div>
                </div>
            </div>
 <?php
echo '<div class="row">';
if (!$block_restore_button_db) {
	echo '<div class="col-sm-6" style="text-align: center;">
			<a class="btn btn-default" href="./download/database.php" role="button" style="margin-top: 20px; margin-bottom: 20px;">' . $pia_lang['Maintenance_Tool_latestdb_download'] . '</a>
			</div>';}
echo '<div class="col-sm-6" style="text-align: center;">
			<a class="btn btn-default" href="./download/config.php" role="button" style="margin-top: 20px; margin-bottom: 20px;">' . $pia_lang['Maintenance_Tool_latestconf_download'] . '</a>
			</div>';
echo '</div>';
?>
        </div>
    </div>
</div>

<!-- Config Editor ----------------------------------------------------------------- -->
 <div class="box">
        <div class="box-body" id="configeditor">
           <button type="button" id="oisggfjergfeirfj" class="btn btn-danger" data-toggle="modal" data-target="#modal-config-editor"><?=$pia_lang['Maintenance_ConfEditor_Start'];?></button>
      </div>
    </div>

    <div class="box box-solid box-danger collapsed-box" style="margin-top: -15px;">
    <div class="box-header with-border" data-widget="collapse">
           <h3 class="box-title"><?=$pia_lang['Maintenance_ConfEditor_Hint'];?></h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool"><i class="fa fa-plus"></i></button>
          </div>
    </div>
    <div class="box-body">
           <table class="table configeditor_help">
              <tbody>
                <tr>
                  <th scope="row" class="text-nowrap text-danger"><?=$pia_lang['Maintenance_ConfEditor_Restore'];?></th>
                  <td class="db_tools_table_cell_b"><?=$pia_lang['Maintenance_ConfEditor_Restore_info'];?></td>
                </tr>
                <tr>
                  <th scope="row" class="text-nowrap text-danger"><?=$pia_lang['Maintenance_ConfEditor_Backup'];?></th>
                  <td class="db_tools_table_cell_b"><?=$pia_lang['Maintenance_ConfEditor_Backup_info'];?></td>
                </tr>
                <tr>
                  <th scope="row" class="text-nowrap text-danger"><?=$pia_lang['Gen_Save'];?></th>
                  <td class="db_tools_table_cell_b"><?=$pia_lang['Maintenance_ConfEditor_Save_info'];?></td>
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
              <form role="form" accept-charset="utf-8">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Config Editor</h4>
                </div>
                <div class="modal-body" style="text-align: left;">
                    <textarea class="form-control" name="txtConfigFileEditor" id="ConfigFileEditor" spellcheck="false" wrap="off" style="resize: none; font-family: monospace; height: 70vh;"><?=file_get_contents('../config/pialert.conf');?></textarea>
                </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="btnPiaRestoreConfigFile" data-dismiss="modal" style="margin: 5px" onclick="askRestoreConfigFile()"><?=$pia_lang['Maintenance_ConfEditor_Restore'];?></button>
                    <button type="button" class="btn btn-success" id="btnPiaBackupConfigFile" style="margin: 5px" onclick="BackupConfigFile('no')"><?=$pia_lang['Maintenance_ConfEditor_Backup'];?></button>
                    <button type="button" class="btn btn-danger" id="btnConfigFileEditor" style="margin: 5px" onclick="SaveConfigFile()"><?=$pia_lang['Gen_Save'];?></button>
                    <button type="button" class="btn btn-default" id="btnPiaEditorClose" data-dismiss="modal" style="margin: 5px"><?=$pia_lang['Gen_Close'];?></button>
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
<link rel="stylesheet" href="lib/AdminLTE/plugins/iCheck/all.css">
<script src="lib/AdminLTE/plugins/iCheck/icheck.min.js"></script>

<!-- Autoscroll-fix for Modals -->
<script>
$(document).ready(function () {
    $('#modal-config-editor').on('show.bs.modal', function () {
        // Save the current scroll position and apply styles to the body and modal
        var scrollPosition = $(window).scrollTop();
        $('body').css({
            position: 'fixed',
            width: '100%',
            top: -scrollPosition
        });
        $('#modal-config-editor').css('overflow-y', 'scroll');
    });

    $('#modal-config-editor').on('hidden.bs.modal', function () {
        // Reset styles when modal is hidden
        var scrollPosition = Math.abs(parseInt($('body').css('top')));
        $('body').css({
            position: '',
            width: '',
            top: ''
        });
        $(window).scrollTop(scrollPosition);
        $('#modal-config-editor').css('overflow-y', 'hidden');
    });
});
</script>

<script>
initializeiCheck();
// delete devices with emty macs
function askDeleteDevicesWithEmptyMACs () {
  showModalWarning('<?=$pia_lang['Maintenance_Tool_del_empty_macs_noti'];?>', '<?=$pia_lang['Maintenance_Tool_del_empty_macs_noti_text'];?>',
    '<?=$pia_lang['Gen_Cancel'];?>', '<?=$pia_lang['Gen_Delete'];?>', 'deleteDevicesWithEmptyMACs');
}
function deleteDevicesWithEmptyMACs()
{
  $.get('php/server/devices.php?action=deleteAllWithEmptyMACs', function(msg) {
    showMessage (msg);
  });
}

// Test Notifications
function askTestNotificationSystem () {
  showModalWarning('<?=$pia_lang['Maintenance_Tool_test_notification_noti'];?>', '<?=$pia_lang['Maintenance_Tool_test_notification_noti_text'];?>',
    '<?=$pia_lang['Gen_Cancel'];?>', '<?=$pia_lang['Gen_Run'];?>', 'TestNotificationSystem');
}
function TestNotificationSystem()
{
  $.get('php/server/devices.php?action=TestNotificationSystem', function(msg) {
    showMessage (msg);
  });
}

// delete all devices
function askDeleteAllDevices () {
  showModalWarning('<?=$pia_lang['Maintenance_Tool_del_alldev_noti'];?>', '<?=$pia_lang['Maintenance_Tool_del_alldev_noti_text'];?>',
    '<?=$pia_lang['Gen_Cancel'];?>', '<?=$pia_lang['Gen_Delete'];?>', 'deleteAllDevices');
}
function deleteAllDevices()
{
  $.get('php/server/devices.php?action=deleteAllDevices', function(msg) {
    showMessage (msg);
  });
}

// delete all (unknown) devices
function askDeleteUnknown () {
  showModalWarning('<?=$pia_lang['Maintenance_Tool_del_unknowndev_noti'];?>', '<?=$pia_lang['Maintenance_Tool_del_unknowndev_noti_text'];?>',
    '<?=$pia_lang['Gen_Cancel'];?>', '<?=$pia_lang['Gen_Delete'];?>', 'deleteUnknownDevices');
}
function deleteUnknownDevices()
{
  $.get('php/server/devices.php?action=deleteUnknownDevices', function(msg) {
    showMessage (msg);
  });
}

// delete all Events
function askDeleteEvents () {
  showModalWarning('<?=$pia_lang['Maintenance_Tool_del_allevents_noti'];?>', '<?=$pia_lang['Maintenance_Tool_del_allevents_noti_text'];?>',
    '<?=$pia_lang['Gen_Cancel'];?>', '<?=$pia_lang['Gen_Delete'];?>', 'deleteEvents');
}
function deleteEvents()
{
  $.get('php/server/devices.php?action=deleteEvents', function(msg) {
    showMessage (msg);
  });
}

// delete Hostory
function askDeleteActHistory () {
  showModalWarning('<?=$pia_lang['Maintenance_Tool_del_ActHistory_noti'];?>', '<?=$pia_lang['Maintenance_Tool_del_ActHistory_noti_text'];?>',
    '<?=$pia_lang['Gen_Cancel'];?>', '<?=$pia_lang['Gen_Delete'];?>', 'deleteActHistory');
}
function deleteActHistory()
{
  $.get('php/server/devices.php?action=deleteActHistory', function(msg) {
    showMessage (msg);
  });
}

// Backup DB to Archive
function askPiaBackupDBtoArchive () {
  showModalWarning('<?=$pia_lang['Maintenance_Tool_backup_noti'];?>', '<?=$pia_lang['Maintenance_Tool_backup_noti_text'];?>',
    '<?=$pia_lang['Gen_Cancel'];?>', '<?=$pia_lang['Gen_Backup'];?>', 'PiaBackupDBtoArchive');
}
function PiaBackupDBtoArchive()
{
  $.get('php/server/files.php?action=BackupDBtoArchive', function(msg) {
    showMessage (msg);
  });
}

// Restore DB from Archive
function askPiaRestoreDBfromArchive () {
  showModalWarning('<?=$pia_lang['Maintenance_Tool_restore_noti'];?>', '<?=$pia_lang['Maintenance_Tool_restore_noti_text'];?>',
    '<?=$pia_lang['Gen_Cancel'];?>', '<?=$pia_lang['Gen_Restore'];?>', 'PiaRestoreDBfromArchive');
}
function PiaRestoreDBfromArchive()
{
  $.get('php/server/files.php?action=RestoreDBfromArchive', function(msg) {
    showMessage (msg);
  });
}

// Purge Backups
function askPiaPurgeDBBackups() {
  showModalWarning('<?=$pia_lang['Maintenance_Tool_purgebackup_noti'];?>', '<?=$pia_lang['Maintenance_Tool_purgebackup_noti_text'];?>',
    '<?=$pia_lang['Gen_Cancel'];?>', '<?=$pia_lang['Gen_Purge'];?>', 'PiaPurgeDBBackups');
}
function PiaPurgeDBBackups()
{
  $.get('php/server/files.php?action=PurgeDBBackups', function(msg) {
    showMessage (msg);
  });
}

// Switch Darkmode
function askPiaEnableDarkmode() {
  showModalWarning('<?=$pia_lang['Maintenance_Tool_darkmode_noti'];?>', '<?=$pia_lang['Maintenance_Tool_darkmode_noti_text'];?>',
    '<?=$pia_lang['Gen_Cancel'];?>', '<?=$pia_lang['Gen_Switch'];?>', 'PiaEnableDarkmode');
}
function PiaEnableDarkmode()
{
  $.get('php/server/files.php?action=EnableDarkmode', function(msg) {
    showMessage (msg);
  });
}

// Switch Web Service Monitor
function askPiaEnableWebServiceMon() {
  showModalWarning('<?=$pia_lang['Maintenance_Tool_webservicemon_noti'];?>', '<?=$pia_lang['Maintenance_Tool_webservicemon_noti_text'];?>',
    '<?=$pia_lang['Gen_Cancel'];?>', '<?=$pia_lang['Gen_Switch'];?>', 'PiaEnableWebServiceMon');
}
function PiaEnableWebServiceMon()
{
  $.get('php/server/services.php?action=EnableWebServiceMon', function(msg) {
    showMessage (msg);
  });
}

// Switch ICMP Monitor
function askPiaEnableICMPMon() {
  showModalWarning('<?=$pia_lang['Maintenance_Tool_icmpmon_noti'];?>', '<?=$pia_lang['Maintenance_Tool_icmpmon_noti_text'];?>',
    '<?=$pia_lang['Gen_Cancel'];?>', '<?=$pia_lang['Gen_Switch'];?>', 'PiaEnableICMPMon');
}
function PiaEnableICMPMon()
{
  $.get('php/server/icmpmonitor.php?action=EnableICMPMon', function(msg) {
    showMessage (msg);
  });
}

// Toggle Graph
function askPiaEnableOnlineHistoryGraph() {
  showModalWarning('<?=$pia_lang['Maintenance_Tool_onlinehistorygraph_noti'];?>', '<?=$pia_lang['Maintenance_Tool_onlinehistorygraph_noti_text'];?>',
    '<?=$pia_lang['Gen_Cancel'];?>', '<?=$pia_lang['Gen_Switch'];?>', 'PiaEnableOnlineHistoryGraph');
}
function PiaEnableOnlineHistoryGraph()
{
  $.get('php/server/files.php?action=EnableOnlineHistoryGraph', function(msg) {
    showMessage (msg);
  });
}

// Set API-Key
function askPiaSetAPIKey() {
  showModalWarning('<?=$pia_lang['Maintenance_Tool_setapikey_noti'];?>', '<?=$pia_lang['Maintenance_Tool_setapikey_noti_text'];?>',
    '<?=$pia_lang['Gen_Cancel'];?>', '<?=$pia_lang['Gen_Okay'];?>', 'PiaSetAPIKey');
}
function PiaSetAPIKey()
{
  $.get('php/server/files.php?action=SetAPIKey', function(msg) {
    showMessage (msg);
  });
}

// Enable Login
function askPiaLoginEnable() {
  showModalWarning('<?=$pia_lang['Maintenance_Tool_loginenable_noti'];?>', '<?=$pia_lang['Maintenance_Tool_loginenable_noti_text'];?>',
    '<?=$pia_lang['Gen_Cancel'];?>', '<?=$pia_lang['Gen_Switch'];?>', 'PiaLoginEnable');
}
function PiaLoginEnable()
{
  $.get('php/server/files.php?action=LoginEnable', function(msg) {
    showMessage (msg);
  });
}

// Disable Login
function askPiaLoginDisable() {
  showModalWarning('<?=$pia_lang['Maintenance_Tool_logindisable_noti'];?>', '<?=$pia_lang['Maintenance_Tool_logindisable_noti_text'];?>',
    '<?=$pia_lang['Gen_Cancel'];?>', '<?=$pia_lang['Gen_Switch'];?>', 'PiaLoginDisable');
}
function PiaLoginDisable()
{
  $.get('php/server/files.php?action=LoginDisable', function(msg) {
    showMessage (msg);
  });
}

function setTextValue (textElement, textValue) {
  $('#'+textElement).val (textValue);
}

// Set Theme
function setPiAlertTheme () {
  $.get('php/server/files.php?action=setTheme&SkinSelection='+ $('#txtSkinSelection').val(), function(msg) {
    showMessage (msg);
  });
}

// Set Language
function setPiAlertLanguage() {
  $.get('php/server/files.php?action=setLanguage&LangSelection='+ $('#txtLangSelection').val(), function(msg) {
    showMessage (msg);
  });
}

// Set ArpScanTimer
function setPiAlertArpTimer() {
  $.ajax({
        method: "GET",
        url: "./php/server/files.php?action=setArpTimer&ArpTimer=" + $('#txtPiaArpTimer').val(),
        data: "",
        beforeSend: function() { $('#Timeralertspinner').removeClass("disablespinner"); $('#TimeralertText').addClass("disablespinner");  },
        complete: function() { $('#Timeralertspinner').addClass("disablespinner"); $('#TimeralertText').removeClass("disablespinner"); },
        success: function(data, textStatus) {
            showMessage (data);
        }
    })
}

// Backup Configfile
function BackupConfigFile(reload)  {
	if (reload == 'yes') {
		$.get('php/server/files.php?action=BackupConfigFile&reload=yes', function(msg) {
		    showMessage (msg);
		  });
	} else {
		$.get('php/server/files.php?action=BackupConfigFile&reload=no', function(msg) {
		    showMessage (msg);
		  });
	}
}

// Restore Configfile
function askRestoreConfigFile() {
  showModalWarning('<?=$pia_lang['Maintenance_ConfEditor_Restore_noti'];?>', '<?=$pia_lang['Maintenance_ConfEditor_Restore_noti_text'];?>',
    '<?=$pia_lang['Gen_Cancel'];?>', '<?=$pia_lang['Gen_Run'];?>', 'RestoreConfigFile');
}
function RestoreConfigFile() {
  $.get('php/server/files.php?action=RestoreConfigFile', function(msg) {
    showMessage (msg);
  });
}

function SaveConfigFile() {
  var postData = {
    action: 'SaveConfigFile',
    configfile: $('#ConfigFileEditor').val()
  };
  $.post('php/server/files.php', postData, function(msg) {
    showMessage(msg);
  });
}

// Set Device List Column
function askDeviceListCol() {
  showModalWarning('<?=$pia_lang['Maintenance_Tool_DevListCol_noti'];?>', '<?=$pia_lang['Maintenance_Tool_DevListCol_noti_text'];?>',
    '<?=$pia_lang['Gen_Cancel'];?>', '<?=$pia_lang['Gen_Save'];?>', 'setDeviceListCol');
}
function setDeviceListCol() {
    $.get('php/server/files.php?action=setDeviceListCol&'
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
  showModalWarning('<?=$pia_lang['Maintenance_Tool_del_Inactive_Hosts'];?>', '<?=$pia_lang['Maintenance_Tool_del_Inactive_Hosts_text'];?>',
    '<?=$pia_lang['Gen_Cancel'];?>', '<?=$pia_lang['Gen_Delete'];?>', 'DeleteInactiveHosts');
}
function DeleteInactiveHosts() {
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

function initializeiCheck () {
   // Blue
   $('input[type="checkbox"].blue').iCheck({
     checkboxClass: 'icheckbox_flat-blue',
     radioClass:    'iradio_flat-blue',
     increaseArea:  '20%'
   });

}
</script>


