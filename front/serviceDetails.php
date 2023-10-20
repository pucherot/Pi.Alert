<!-- ---------------------------------------------------------------------------
#  Pi.Alert
#  Open Source Network Guard / WIFI & LAN intrusion detector
#
#  serviceDetails.php - Front module. Service management page
#-------------------------------------------------------------------------------
#  leiweibau 2023                                          GNU GPLv3
#--------------------------------------------------------------------------- -->


<?php
session_start();

if ($_SESSION["login"] != 1) {
	header('Location: ./index.php');
	exit;
}

# Validate URL
$_REQUEST['url'] = filter_var($_REQUEST['url'], FILTER_SANITIZE_URL);

if (filter_var($_REQUEST['url'], FILTER_VALIDATE_URL)) {
	$service_details_title = $_REQUEST['url'];
	$service_details_title_array = explode('://', $_REQUEST['url']);
} else {
	header('Location: /pialert/index.php');
	exit;
}

require 'php/templates/header.php';
require 'php/server/graph.php';
require 'php/server/journal.php';

# Init DB Connection
$db_file = '../db/pialert.db';
$db = new SQLite3($db_file);
$db->exec('PRAGMA journal_mode = wal;');

// -----------------------------------------------------------------------------------
function get_service_details($service_URL) {
	global $db;

	$mon_res = $db->query('SELECT * FROM Services WHERE mon_URL="' . $service_URL . '"');
	$row = $mon_res->fetchArray();
	return $row;
}

// ----------------- Set Filter of fallback to default--------------------------
$http_filter = $_REQUEST['filter'];
if (!isset($http_filter)) {$http_filter = 'all';}

function get_service_events_table($service_URL, $service_filter) {
	global $db;
	global $current_service_IP;

	if ($service_filter == 'all') {
		$filter_sql = "";
	} elseif ($service_filter == 2) {
		$filter_sql = 'AND moneve_StatusCode LIKE "2%"';
	} elseif ($service_filter == 3) {
		$filter_sql = 'AND moneve_StatusCode LIKE "3%"';
	} elseif ($service_filter == 4) {
		$filter_sql = 'AND moneve_StatusCode LIKE "4%"';
	} elseif ($service_filter == 5) {
		$filter_sql = 'AND moneve_StatusCode LIKE "5%"';
	} elseif ($service_filter == "99999999") {
		$filter_sql = 'AND moneve_Latency="99999999"';
	}

	$moneve_res = $db->query('SELECT * FROM Services_Events WHERE moneve_URL="' . $service_URL . '"' . $filter_sql);
	while ($row = $moneve_res->fetchArray()) {
		if ($row['moneve_TargetIP'] == '') {$func_TargetIP = 'n.a.';} else {
			$func_TargetIP = $row['moneve_TargetIP'];
			$current_service_IP = $row['moneve_TargetIP'];}
		echo '<tr>
                  <td>' . $func_TargetIP . '</td>
                  <td>' . $row['moneve_DateTime'] . '</td>
                  <td>' . $row['moneve_StatusCode'] . '</td>
                  <td>' . $row['moneve_Latency'] . '</td>
                  <td>' . $row['moneve_ssl_fc'] . '</td>
              </tr>';
	}
}

// -----------------------------------------------------------------------------------
function set_table_headline($service_filter) {
	global $pia_lang;

	if ($service_filter == 'all') {
		echo '<h3 class="text-aqua" style="display: inline-block;font-size: 18px; margin: 0; line-height: 1;">' . $pia_lang['WebServices_Events_Shortcut_All'] . '</h3>';
	} elseif ($service_filter == 2) {
		echo '<h3 class="text-green" style="display: inline-block;font-size: 18px; margin: 0; line-height: 1;">' . $pia_lang['WebServices_Events_Shortcut_HTTP2xx'] . '</h3>';
	} elseif ($service_filter == 3) {
		echo '<h3 class="text-yellow" style="display: inline-block;font-size: 18px; margin: 0; line-height: 1;">' . $pia_lang['WebServices_Events_Shortcut_HTTP3xx'] . '</h3>';
	} elseif ($service_filter == 4) {
		echo '<h3 class="text-yellow" style="display: inline-block;font-size: 18px; margin: 0; line-height: 1;">' . $pia_lang['WebServices_Events_Shortcut_HTTP4xx'] . '</h3>';
	} elseif ($service_filter == 5) {
		echo '<h3 class="text-yellow" style="display: inline-block;font-size: 18px; margin: 0; line-height: 1;">' . $pia_lang['WebServices_Events_Shortcut_HTTP5xx'] . '</h3>';
	} elseif ($service_filter == "99999999") {
		echo '<h3 class="text-red" style="display: inline-block;font-size: 18px; margin: 0; line-height: 1;">' . $pia_lang['WebServices_Events_Shortcut_Down'] . '</h3>';
	}
}

$servicedetails = get_service_details($service_details_title);

// ----------------- Get Online Graph Arrays -----------------------------------
$graph_arrays = array();
$graph_arrays = prepare_graph_arrays_webservice($service_details_title);
$Pia_Graph_Service_Time = $graph_arrays[0];
$Pia_Graph_Service_Down = $graph_arrays[1];
$Pia_Graph_Service_2xx = $graph_arrays[2];
$Pia_Graph_Service_3xx = $graph_arrays[3];
$Pia_Graph_Service_4xx = $graph_arrays[4];
$Pia_Graph_Service_5xx = $graph_arrays[5];
$http2xx = $graph_arrays[7];
$http3xx = $graph_arrays[8];
$http4xx = $graph_arrays[9];
$http5xx = $graph_arrays[10];
$httpdown = $graph_arrays[6];

// -----------------Geo Location -----------------------------------------------
function init_location_array($HOST_IP) {
	if (file_exists("../db/GeoLite2-Country.mmdb")) {
		$databasePath = '../db/GeoLite2-Country.mmdb';
		$command = "mmdblookup -f {$databasePath} --ip {$HOST_IP}";
		exec($command, $output);
		for ($x = 0; $x < sizeof($output); $x++) {
			$output[$x] = trim($output[$x]);
		}
		$output_str = implode("\n", $output);
		$output_str = str_replace(":\n", ":", $output_str);
		$location_array = explode("\n", $output_str);
		return $location_array;
	} else {
		$nofile = array('######');
		return $nofile;
	}
}

// -----------------Parse Geo Location Array -----------------------------------
function parse_location_array($LOCATION_ARRAY) {
	global $pia_lang_selected;

	$language_code = substr($pia_lang_selected, 0, 2);
	$locations = array();
	if (sizeof($LOCATION_ARRAY) > 1) {
		for ($x = 0; $x < sizeof($LOCATION_ARRAY); $x++) {
			if (stristr($LOCATION_ARRAY[$x], '"' . $language_code . '":')) {
				$temp_location = str_replace('"', '', strip_tags($LOCATION_ARRAY[$x]));
				$temp_location = trim(str_replace("$language_code:", '', $temp_location));
				array_push($locations, $temp_location);
			}
		}
	}
	if (sizeof($locations) < 1) {array_push($locations, "IP not found in DB");}
	return $locations;
}

// ----------------- Get some service stats ------------------------------------
function get_service_statistic($service) {
	global $db;

	// Compensate Timezone
	$stat_query_24h = 24 - (date('Z') / 3600);
	$stat_query_1w = 168 - (date('Z') / 3600);

	$statistic = array();
	$query = "SELECT AVG(moneve_Latency) AS average_latency FROM Services_Events WHERE moneve_Latency != 99999999 AND moneve_Latency IS NOT NULL AND moneve_URL=\"$service\"";
	$result = $db->querySingle($query);
	$statistic['latency_avg'] = round($result, 4) . ' ms';
	$query_max = "SELECT MAX(moneve_Latency) AS max_latency FROM Services_Events WHERE moneve_Latency != 99999999 AND moneve_Latency IS NOT NULL AND moneve_URL=\"$service\"";
	$query_min = "SELECT MIN(moneve_Latency) AS min_latency FROM Services_Events WHERE moneve_Latency != 99999999 AND moneve_Latency IS NOT NULL AND moneve_URL=\"$service\"";
	$result_max = $db->querySingle($query_max);
	$statistic['latency_max'] = '<i class="bi bi-speedometer2 flip-horizontal text-red"></i> ' . round($result_max, 4) . ' ms';
	$result_min = $db->querySingle($query_min);
	$statistic['latency_min'] = '<i class="bi bi-speedometer2 text-green"></i> ' . round($result_min, 4) . ' ms';
	$query = "SELECT COUNT(*) AS row_count FROM Services_Events WHERE moneve_Latency == 99999999 AND moneve_URL=\"$service\"";
	$result = $db->querySingle($query);
	$statistic['offline'] = $result;
	$query = "SELECT COUNT(*) AS row_count FROM Services_Events WHERE moneve_Latency != 99999999 AND moneve_URL=\"$service\"";
	$result = $db->querySingle($query);
	$statistic['online'] = $result;
	$temp100 = $statistic['online'] + $statistic['offline'];
	if ($temp100 > 0 && $statistic['online'] > 0) {
		$statistic['online_percent_all'] = round(($statistic['online'] * 100 / $temp100), 2);
	} else {
		$statistic['online_percent_all'] = 0;
	}
	$statistic['offline_percent_all'] = round((100 - $statistic['online_percent_all']), 2);
	$statistic['online_percent_all'] = $statistic['online_percent_all'] . ' %';
	$statistic['offline_percent_all'] = $statistic['offline_percent_all'] . ' %';

	// 1 Day Stats
	// ---------------------------------------------------
	$query = "SELECT * FROM Services_Events
	  WHERE moneve_URL=\"$service\" AND datetime(moneve_DateTime) >= datetime('now', '-$stat_query_24h hours')
	  ORDER BY datetime(moneve_DateTime) DESC";

	$result = $db->query($query);
	$offline = 0;
	$online = 0;
	$min_service = 99999999;
	$max_service = 0;
	$avg_service = 0;
	while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
		if ($row['moneve_Latency'] != "" && $row['moneve_Latency'] != "99999999") {
			$online++;
			if ($row['moneve_Latency'] > $max_service) {$max_service = $row['moneve_Latency'];}
			if ($row['moneve_Latency'] < $min_service) {$min_service = $row['moneve_Latency'];}
			$avg_service = $avg_service + $row['moneve_Latency'];
		} else { $offline++;}
	}
	if ($min_service == 99999999) {$statistic['latency_min_24h'] = 'n.a.';} else { $statistic['latency_min_24h'] = '<i class="bi bi-speedometer2 text-green"></i> ' . round($min_service, 4) . ' ms';}
	if ($max_service == 0) {$statistic['latency_max_24h'] = 'n.a.';} else { $statistic['latency_max_24h'] = '<i class="bi bi-speedometer2 flip-horizontal text-red"></i> ' . round($max_service, 4) . ' ms';}
	if ($avg_service > 0) {$statistic['latency_avg_24h'] = round(($avg_service / $online), 4) . ' ms';} else { $statistic['latency_avg_24h'] = 'n.a.';}
	$statistic['online_24h'] = $online;
	$statistic['offline_24h'] = $offline;

	$temp24h = $statistic['online_24h'] + $statistic['offline_24h'];
	if ($temp24h > 0 && $statistic['online_24h'] > 0) {
		$statistic['online_percent_24h'] = round(($statistic['online_24h'] * 100 / $temp24h), 2);
	} else {
		$statistic['online_percent_24h'] = 0;
	}
	$statistic['offline_percent_24h'] = round((100 - $statistic['online_percent_24h']), 2);
	$statistic['online_percent_24h'] = $statistic['online_percent_24h'] . ' %';
	$statistic['offline_percent_24h'] = $statistic['offline_percent_24h'] . ' %';

	// 1 Week Stats
	// ---------------------------------------------------
	$query = "SELECT * FROM Services_Events
	  WHERE moneve_URL=\"$service\" AND datetime(moneve_DateTime) >= datetime('now', '-$stat_query_1w hours')
	  ORDER BY datetime(moneve_DateTime) DESC";

	$result = $db->query($query);
	$offline = 0;
	$online = 0;
	$min_service = 99999999;
	$max_service = 0;
	$avg_service = 0;
	while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
		if ($row['moneve_Latency'] != "" && $row['moneve_Latency'] != "99999999") {
			$online++;
			if ($row['moneve_Latency'] > $max_service) {$max_service = $row['moneve_Latency'];}
			if ($row['moneve_Latency'] < $min_service) {$min_service = $row['moneve_Latency'];}
			$avg_service = $avg_service + $row['moneve_Latency'];
		} else { $offline++;}
	}

	if ($min_service == 99999999) {$statistic['latency_min_1w'] = 'n.a.';} else { $statistic['latency_min_1w'] = '<i class="bi bi-speedometer2 text-green"></i> ' . round($min_service, 4) . ' ms';}
	if ($max_service == 0) {$statistic['latency_max_1w'] = 'n.a.';} else { $statistic['latency_max_1w'] = '<i class="bi bi-speedometer2 flip-horizontal text-red"></i> ' . round($max_service, 4) . ' ms';}
	if ($avg_service > 0) {$statistic['latency_avg_1w'] = round(($avg_service / $online), 4) . ' ms';} else { $statistic['latency_avg_1w'] = 'n.a.';}
	$statistic['online_1w'] = $online;
	$statistic['offline_1w'] = $offline;

	$temp24h = $statistic['online_1w'] + $statistic['offline_1w'];
	if ($temp24h > 0 && $statistic['online_1w'] > 0) {
		$statistic['online_percent_1w'] = round(($statistic['online_1w'] * 100 / $temp24h), 2);
	} else {
		$statistic['online_percent_1w'] = 0;
	}
	$statistic['offline_percent_1w'] = round((100 - $statistic['online_percent_1w']), 2);
	$statistic['online_percent_1w'] = $statistic['online_percent_1w'] . ' %';
	$statistic['offline_percent_1w'] = $statistic['offline_percent_1w'] . ' %';

	return $statistic;
}

?>

<!-- Page ------------------------------------------------------------------ -->
  <div class="content-wrapper">

<!-- Content header--------------------------------------------------------- -->
    <section class="content-header">
      <?php require 'php/templates/notification.php';?>

      <h1 id="pageTitle">
        <?='[' . strtoupper($service_details_title_array[0]) . '] ' . $service_details_title_array[1];?>
      </h1>
    </section>

<!-- Main content ---------------------------------------------------------- -->
    <section class="content">

<!-- top small boxes -->
      <div class="row">

        <div class="col-lg-2 col-sm-4 col-xs-6">
          <a href="./serviceDetails.php?url=<?=$service_details_title?>&filter=all" onclick="javascript: getEventsTotalsforService('all');">
            <div class="small-box bg-aqua">
              <div class="inner"> <h3 id="eventsAll"> -- </h3>
                <p class="infobox_label"><?=$pia_lang['WebServices_Events_Shortcut_All'];?></p>
              </div>
              <div class="icon"> <i class="fa fa-bolt text-aqua-40"></i> </div>
            </div>
          </a>
        </div>

        <div class="col-lg-2 col-sm-4 col-xs-6">
          <a href="./serviceDetails.php?url=<?=$service_details_title?>&filter=2" onclick="javascript: getEventsTotalsforService('2');">
            <div class="small-box bg-green">
              <div class="inner"> <h3 id="events2xx"> -- </h3>
                <p class="infobox_label"><?=$pia_lang['WebServices_Events_Shortcut_HTTP2xx'];?></p>
              </div>
              <div class="icon"> <i class="bi bi-check2-square text-green-40"></i> </div>
            </div>
          </a>
        </div>

        <div class="col-lg-2 col-sm-4 col-xs-6">
          <a href="./serviceDetails.php?url=<?=$service_details_title?>&filter=3" onclick="javascript: getEventsTotalsforService('3');">
            <div  class="small-box bg-yellow">
              <div class="inner"> <h3 id="events3xx"> -- </h3>
                <p class="infobox_label"><?=$pia_lang['WebServices_Events_Shortcut_HTTP3xx'];?></p>
              </div>
              <div class="icon"> <i class="bi bi-sign-turn-right text-yellow-40"></i> </div>
            </div>
          </a>
        </div>

        <div class="col-lg-2 col-sm-4 col-xs-6">
          <a href="./serviceDetails.php?url=<?=$service_details_title?>&filter=4" onclick="javascript: getEventsTotalsforService('4');">
            <div  class="small-box bg-yellow">
              <div class="inner"> <h3 id="events4xx"> -- </h3>
                <p class="infobox_label"><?=$pia_lang['WebServices_Events_Shortcut_HTTP4xx'];?></p>
              </div>
              <div class="icon"> <i class="bi bi-exclamation-square text-yellow-40"></i> </div>
            </div>
          </a>
        </div>

        <div class="col-lg-2 col-sm-4 col-xs-6">
          <a href="./serviceDetails.php?url=<?=$service_details_title?>&filter=5" onclick="javascript: getEventsTotalsforService('5');">
            <div  class="small-box bg-yellow">
              <div class="inner"> <h3 id="events5xx"> -- </h3>
                <p class="infobox_label"><?=$pia_lang['WebServices_Events_Shortcut_HTTP5xx'];?></p>
              </div>
              <div class="icon"> <i class="bi bi-database-x text-yellow-40"></i> </div>
            </div>
          </a>
        </div>

        <div class="col-lg-2 col-sm-4 col-xs-6">
          <a href="./serviceDetails.php?url=<?=$service_details_title?>&filter=99999999" onclick="javascript: getEventsTotalsforService('99999999');">
            <div  class="small-box bg-red">
              <div class="inner"> <h3 id="eventsDown"> -- </h3>
                <p class="infobox_label"><?=$pia_lang['WebServices_Events_Shortcut_Down'];?></p>
              </div>
              <div class="icon"> <i class="bi bi-exclamation-diamond-fill text-red-40"></i> </div>
            </div>
          </a>
        </div>

      </div>
      <!-- /.row -->

<!-- tab control------------------------------------------------------------ -->
      <div class="row">
        <div class="col-lg-12 col-sm-12 col-xs-12">
          <div id="navDevice" class="nav-tabs-custom">
            <ul class="nav nav-tabs" style="fon t-size:16px;">
              <li class=""> <a id="tabDetails"  href="#panDetails"  data-toggle="tab"> <?=$pia_lang['DevDetail_Tab_Details'];?>  </a></li>
              <li class=""> <a id="tabEvents"   href="#panEvents"   data-toggle="tab"> <?=$pia_lang['DevDetail_Tab_Events'];?>   </a></li>
              <li class=""> <a id="tabGraph"   href="#panGraph"   data-toggle="tab"> <?=$pia_lang['WebServices_Tab_Graph'];?>   </a></li>
            </ul>
            <div class="tab-content" style="min-height: 430px;">

<!-- tab page 1 ------------------------------------------------------------ -->

              <div class="tab-pane" id="panDetails">
                <div class="row">
    <!-- column 1 -->
                  <div class="col-sm-6 col-xs-12">
                    <h4 class="bottom-border-aqua"><?=$pia_lang['DevDetail_MainInfo_Title'];?></h4>
                    <div class="box-body form-horizontal">

                      <!-- URL -->
                      <div class="form-group">
                        <label class="col-sm-3 control-label"><?=$pia_lang['WebServices_label_URL'];?></label>
                        <div class="col-sm-9">
                          <input class="form-control" id="txtURL" type="text" readonly value="<?=$servicedetails['mon_URL']?>">
                        </div>
                      </div>

                      <!-- Tags -->
                      <div class="form-group">
                        <label class="col-sm-3 control-label"><?=$pia_lang['WebServices_label_Tags'];?></label>
                        <div class="col-sm-9">
                          <input class="form-control" id="txtTags" type="text" value="<?=$servicedetails['mon_Tags']?>">
                        </div>
                      </div>

                      <!-- Mac address -->
                      <div class="form-group">
                        <label class="col-sm-3 control-label"><?=$pia_lang['WebServices_label_MAC'];?></label>
                        <div class="col-sm-9">
                          <div class="input-group">
                            <div class="input-group-btn">
                              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><?=$pia_lang['WebServices_label_MAC_Select'];?>
                                <span class="fa fa-caret-down"></span></button>
                              <ul class="dropdown-menu">
<?php
if ($servicedetails['mon_MAC'] != "") {
	echo '<li><a href="javascript:void(0)" onclick="setTextValue(\'txtMAC\',\'' . $servicedetails['mon_MAC'] . '\')">' . $servicedetails['mon_MAC'] . '</a></li>';
}
echo '<li> -----  </li>';

$dev_res = $db->query('SELECT dev_MAC, dev_Name FROM Devices ORDER BY dev_Name ASC');
$code_array = array();
while ($row = $dev_res->fetchArray()) {
	echo '<li><a href="javascript:void(0)" onclick="setTextValue(\'txtMAC\',\'' . $row['dev_MAC'] . '\')">' . $row['dev_Name'] . '</a></li>';
}
?>
                              </ul>
                            </div>
                            <!-- /btn-group -->
                            <input type="text" id="txtMAC" class="form-control" data-enpassusermodified="yes" value="<?=$servicedetails['mon_MAC'];?>">
                          </div>
                        </div>
                      </div>

                      <!-- Notes -->
                      <div class="form-group">
                        <label class="col-sm-3 control-label"><?=$pia_lang['WebServices_label_Notes'];?></label>
                        <div class="col-sm-9">
                          <input class="form-control" id="txtNotes" type="text" readonly value="<?=$servicedetails['mon_Notes']?>">
                        </div>
                      </div>

                    </div>
                  </div>

    <!-- column 2 -->
                  <div class="col-sm-6 col-xs-12">
                    <h4 class="bottom-border-aqua"><?=$pia_lang['DevDetail_EveandAl_Title'];?></h4>
                    <div class="box-body form-horizontal">

                      <!-- Last HTTP Status -->
                      <div class="form-group">
                        <label class="col-sm-4 control-label"><?=$pia_lang['WebServices_label_StatusCode'];?></label>
                        <div class="col-sm-8">
                          <input class="form-control" id="txtLastStatus" type="text" readonly value="<?=$servicedetails['mon_LastStatus']?>">
                        </div>
                      </div>

                      <!-- Last HTTP Status -->
                      <div class="form-group">
                        <label class="col-sm-4 control-label">SSL Status</label>
                        <div class="col-sm-8">
                          <input class="form-control" id="txtLastStatus" type="text" readonly value="<?=$servicedetails['mon_ssl_fc']?>">
                        </div>
                      </div>

                      <!-- Last IP -->
                      <div class="form-group">
                        <label class="col-sm-4 control-label"><?=$pia_lang['WebServices_label_TargetIP'];?></label>
                        <div class="col-sm-8">
                          <input class="form-control" id="txtLastIP" type="text" readonly value="<?=$servicedetails['mon_TargetIP']?>">
                        </div>
                      </div>

                      <!-- Last Scan -->
                      <div class="form-group">
                        <label class="col-sm-4 control-label"><?=$pia_lang['WebServices_label_ScanTime'];?></label>
                        <div class="col-sm-8">
                          <input class="form-control" id="txtLastScan" type="text" readonly value="<?=$servicedetails['mon_LastScan']?>">
                        </div>
                      </div>

                      <!-- Last Latency -->
                      <div class="form-group">
                        <label class="col-sm-4 control-label"><?=$pia_lang['WebServices_label_Response_Time'];?></label>
                        <div class="col-sm-8">
                          <input class="form-control" id="txtLastLatency" type="text" readonly value="<?=$servicedetails['mon_LastLatency']?>">
                        </div>
                      </div>

                      <!-- Alert events -->
                      <div class="form-group">
                        <label class="col-xs-4 control-label"><?=$pia_lang['WebServices_label_AlertEvents'];?></label>
                        <div class="col-xs-4" style="padding-top:6px;">
                          <input class="checkbox blue" id="chkAlertEvents" <?php if ($servicedetails['mon_AlertEvents'] == 1) {echo 'checked';}?> type="checkbox">
                        </div>
                      </div>

                      <!-- Alert Down -->
                      <div class="form-group">
                        <label class="col-xs-4 control-label"><?=$pia_lang['WebServices_label_AlertDown'];?></label>
                        <div class="col-xs-4" style="padding-top:6px;">
                          <input class="checkbox red" id="chkAlertDown" <?php if ($servicedetails['mon_AlertDown'] == 1) {echo 'checked';}?> type="checkbox">
                        </div>
                      </div>

                    </div>
                  </div>
								</div>

								<div class="row" style="margin-bottom: 20px;">
                  	<div class="col-xs-12">
                    <h4 class="bottom-border-aqua">SSL Certificate Info</h4>
                    <div class="box-body form-horizontal">
                        <label class="col-sm-2 control-label">Subject</label>
                        <div class="col-sm-10">
                          <input class="form-control" id="txtLastStatus" type="text" readonly value="<?=str_replace("<Name(", "", str_replace(")>", "", $servicedetails['mon_ssl_subject']))?>">
                        </div>
                        <label class="col-sm-2 control-label">Issuer</label>
                        <div class="col-sm-10">
                          <input class="form-control" id="txtLastStatus" type="text" readonly value="<?=str_replace("<Name(", "", str_replace(")>", "", $servicedetails['mon_ssl_issuer']))?>">
                        </div>
                        <label class="col-sm-2 control-label">Valid from</label>
                        <div class="col-sm-10">
                          <input class="form-control" id="txtLastStatus" type="text" readonly value="<?=$servicedetails['mon_ssl_valid_from']?>">
                        </div>
                        <label class="col-sm-2 control-label">Valid to</label>
                        <div class="col-sm-10">
                          <input class="form-control" id="txtLastStatus" type="text" readonly value="<?=$servicedetails['mon_ssl_valid_to']?>">
                        </div>
                      </div>
                  </div>
                </div>


								<div class="row">
                  <!-- Buttons -->
                  <div class="col-xs-12">
                    <div class="pull-right">
                        <button type="button" class="btn btn-danger servicedet_button_space"  id="btnDelete"   onclick="askDeleteService()"> <?=$pia_lang['Gen_Delete'];?> </button>
                        <button type="button" class="btn btn-default servicedet_button_space" id="btnRestore"  onclick="location.reload()">  <?=$pia_lang['Gen_Cancel'];?> </button>
                        <button type="button" class="btn btn-primary servicedet_button_space" id="btnSave"     onclick="setServiceData()" >  <?=$pia_lang['Gen_Save'];?> </button>
                    </div>
                  </div>
                </div>

              </div>

<!-- Events ------------------------------------------------------------ -->
              <div class="tab-pane fade table-responsive" id="panEvents">
<?php
# Create Event table headline
set_table_headline($http_filter);
?>
                <!-- Datatable Events -->
                <table id="tableEvents" class="table table-bordered table-hover table-striped ">
                  <thead>
                    <tr>
                      <!-- <th>Service URL</th> -->
                      <th><?=$pia_lang['WebServices_tablehead_TargetIP'];?></th>
                      <th><?=$pia_lang['WebServices_tablehead_ScanTime'];?></th>
                      <th><?=$pia_lang['WebServices_tablehead_Status_Code'];?></th>
                      <th><?=$pia_lang['WebServices_tablehead_Response_Time'];?></th>
                      <th>SSL Status</th>
                    </tr>
                  </thead>
                  <tbody>
<?php
# Create Event table
get_service_events_table($service_details_title, $http_filter);
?>
                  </tbody>
                </table>
              </div>

<!-- Graph ------------------------------------------------------------ -->
              <div class="tab-pane fade table-responsive" id="panGraph">
                <h4 class="text-aqua" style="font-size: 18px;margin: 0;line-height: 1; margin-bottom: 20px;"><?=$pia_lang['WebServices_Chart_a'];?> <span class="maxlogage-interval">24</span> <?=$pia_lang['WebServices_Chart_b'];?></h4>
                <div class="col-md-12">
                  <div class="chart" style="height: 150px;">
                    <script src="lib/AdminLTE/bower_components/chart.js/Chart.js"></script>
                    <canvas id="ServiceChart"></canvas>
                  </div>
                </div>
                <script src="js/graph_online_history.js"></script>
                <script>
                  var pia_js_online_history_time = [<?php pia_graph_devices_data($Pia_Graph_Service_Time);?>];
                  var pia_js_online_history_down = [<?php pia_graph_devices_data($Pia_Graph_Service_Down);?>];
                  var pia_js_online_history_2xx = [<?php pia_graph_devices_data($Pia_Graph_Service_2xx);?>];
                  var pia_js_online_history_3xx = [<?php pia_graph_devices_data($Pia_Graph_Service_3xx);?>];
                  var pia_js_online_history_4xx = [<?php pia_graph_devices_data($Pia_Graph_Service_4xx);?>];
                  var pia_js_online_history_5xx = [<?php pia_graph_devices_data($Pia_Graph_Service_5xx);?>];
                  graph_services_history(pia_js_online_history_time, pia_js_online_history_down, pia_js_online_history_2xx, pia_js_online_history_3xx, pia_js_online_history_4xx, pia_js_online_history_5xx);
                </script>

                <div class="col-md-12 bottom-border-aqua" style="margin-top: 30px; opacity: 0.7"></div>

                <div class="col-md-12">
                  <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-2" style="font-weight: 600;"><?=$pia_lang['WebServices_Stats_Code'];?>:</div>
                    <div class="col-sm-2"><i class="fa fa-w fa-circle text-green"></i> HTTP-Code 2xx (<?=$http2xx;?>)</div>
                    <div class="col-sm-2"><i class="fa fa-w fa-circle text-yellow"></i> HTTP-Code 3xx (<?=$http3xx;?>)</div>
                    <div class="col-sm-2"><i class="fa fa-w fa-circle text-yellow"></i> HTTP-Code 4xx (<?=$http4xx;?>)</div>
                    <div class="col-sm-2"><i class="fa fa-w fa-circle text-orange-custom"></i> HTTP-Code 5xx (<?=$http5xx;?>)</div>
                    <div class="col-sm-2"><i class="fa fa-w fa-circle text-red"></i> <?=$pia_lang['WebServices_Page_down'];?> (<?=$httpdown;?>)</div>
                  </div>
                </div>

                <div class="col-md-12 bottom-border-aqua" style="margin-top: 10px; opacity: 0.7"></div>
<?php
# Get Statistic
$statistic = get_service_statistic($service_details_title);
?>
                <div class="col-md-12">
                  <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-12" style="font-weight: 600;"><?=$pia_lang['WebServices_Stats_Time'];?></div>
                  </div>
                  <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-2" style="font-weight: 600;">24h</div>
                    <div class="col-sm-2"><span class="text-aqua">&Oslash;</span> <?=$statistic['latency_avg_24h'];?></div>
                    <div class="col-sm-2"><?=$statistic['latency_min_24h'];?></div>
                    <div class="col-sm-2"><?=$statistic['latency_max_24h'];?></div>
                  </div>
                  <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-2" style="font-weight: 600;">7d</div>
                    <div class="col-sm-2"><span class="text-aqua">&Oslash;</span> <?=$statistic['latency_avg_1w'];?></div>
                    <div class="col-sm-2"><?=$statistic['latency_min_1w'];?></div>
                    <div class="col-sm-2"><?=$statistic['latency_max_1w'];?></div>
                  </div>
                  <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-2" style="font-weight: 600;">All</div>
                    <div class="col-sm-2"><span class="text-aqua">&Oslash;</span> <?=$statistic['latency_avg'];?></div>
                    <div class="col-sm-2"><?=$statistic['latency_min'];?></div>
                    <div class="col-sm-2"><?=$statistic['latency_max'];?></div>
                  </div>
                </div>

                <div class="col-md-12 bottom-border-aqua" style="margin-top: 10px; opacity: 0.7"></div>

                <div class="col-md-12">
                  <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-12" style="font-weight: 600;"><?=$pia_lang['ICMPMonitor_Availability'];?></div>
                  </div>
                  <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-2" style="font-weight: 600;">24h</div>
                    <div class="col-sm-2"><span class="text-green"><?=$pia_lang['ICMPMonitor_Shortcut_Online'];?></span> <?=$statistic['online_percent_24h'];?></div>
                    <div class="col-sm-2"><span class="text-red"><?=$pia_lang['ICMPMonitor_Shortcut_Offline'];?></span> <?=$statistic['offline_percent_24h'];?></div>
                  </div>
                  <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-2" style="font-weight: 600;">7d</div>
                    <div class="col-sm-2"><span class="text-green"><?=$pia_lang['ICMPMonitor_Shortcut_Online'];?></span> <?=$statistic['online_percent_1w'];?></div>
                    <div class="col-sm-2"><span class="text-red"><?=$pia_lang['ICMPMonitor_Shortcut_Offline'];?></span> <?=$statistic['offline_percent_1w'];?></div>
                  </div>
                  <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-2" style="font-weight: 600;">All</div>
                    <div class="col-sm-2"><span class="text-green"><?=$pia_lang['ICMPMonitor_Shortcut_Online'];?></span> <?=$statistic['online_percent_all'];?></div>
                    <div class="col-sm-2"><span class="text-red"><?=$pia_lang['ICMPMonitor_Shortcut_Offline'];?></span> <?=$statistic['offline_percent_all'];?></div>
                  </div>
                </div>

                <div class="col-md-12 bottom-border-aqua" style="margin-top: 10px; opacity: 0.7"></div>
<?php
$output = init_location_array($servicedetails['mon_TargetIP']);
if ($output[0] != "######") {
	$locations = parse_location_array($output);
	echo '<div class="col-md-12">
          <div class="row" style="margin-top: 10px;">
            <div class="col-sm-12" style="font-weight: 600;">' . $pia_lang['WebServices_Stats_Location'] . ': </div>
          </div>
          <div class="row">
            <div class="col-sm-12" style="padding-left: 40px;"><div style="display: inline-block; width: 130px;">' . $pia_lang['WebServices_Stats_IP'] . ':</div> ' . $servicedetails['mon_TargetIP'] . '</div>
          </div>
          <div class="row">
            <div class="col-sm-12" style="padding-left: 40px;"><div style="display: inline-block; width: 130px;">' . $pia_lang['WebServices_Stats_IPLocation'] . ':</div> ' . $locations[1] . ' (' . $locations[0] . ')</div>
          </div>
          <div class="row">
            <div class="col-sm-12" style="margin-top: 30px;">
              <button class="btn btn-default" id="deleteDB-button">' . $pia_lang['GeoLiteDB_button_del'] . '</button>
              <p style="margin-top: 20px;">' . $pia_lang['GeoLiteDB_credits'] . '</p>
            </div>
          </div>
        </div>';
} else {
	echo '<div class="col-md-12">
          <div class="row" style="margin-top: 30px;">
            <style>
                .downloader {
                    border: 6px solid #f3f3f3; /* Light gray */
                    border-top: 6px solid #3498db; /* Blue */
                    border-radius: 50%;
                    width: 32px;
                    height: 32px;
                    animation: spin 2s linear infinite;
                    margin-left: 50px;
                }

                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>
            <div class="col-sm-12" style="">
              <div style="height: 60px;">
                <div class="downloader" id="downloader" style="display: none;"></div>
                <button class="btn btn-default" id="downloadDB-button">' . $pia_lang['GeoLiteDB_button_ins'] . '</button>
              </div>
              <p style="margin-top: 20px;">' . $pia_lang['GeoLiteDB_credits'] . '</p>
            </div>
          </div>
        </div>';
}
?>
                <!-- Closing  <div class="col-md-12">   -->

              </div>

            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->

          <!-- </div> -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->


<!-- ----------------------------------------------------------------------- -->
<?php
require 'php/templates/footer.php';
?>

<!-- iCkeck -->
<link rel="stylesheet" href="lib/AdminLTE/plugins/iCheck/all.css">
<script src="lib/AdminLTE/plugins/iCheck/icheck.min.js"></script>

<!-- Datatable -->
<link rel="stylesheet" href="lib/AdminLTE/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
<script src="lib/AdminLTE/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="lib/AdminLTE/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<!-- fullCalendar -->
<link rel="stylesheet" href="lib/AdminLTE/bower_components/fullcalendar/dist/fullcalendar.min.css">
<link rel="stylesheet" href="lib/AdminLTE/bower_components/fullcalendar/dist/fullcalendar.print.min.css" media="print">
<script src="lib/AdminLTE/bower_components/moment/moment.js"></script>
<script src="lib/AdminLTE/bower_components/fullcalendar/dist/fullcalendar.min.js"></script>
<script src="lib/AdminLTE/bower_components/fullcalendar/dist/locale-all.js"></script>

<!-- Dark-Mode Patch -->
<?php
if ($ENABLED_DARKMODE === True) {
	echo '<link rel="stylesheet" href="css/dark-patch-cal.css">';
}
?>

<!-- page script ----------------------------------------------------------- -->
<script>

  var url                 = '';
  var devicesList         = [];
  var pos                 = -1;
  var parPeriod           = 'Front_ServiceDetails_Period';
  var parTab              = 'Front_ServiceDetails_Tab';
  var parEventsRows       = 'Front_ServiceDetails_Events_Rows';
  var period              = '1 month';
  var tab                 = 'tabDetails'
  //var eventsRows          = 25;

  // Read parameters & Initialize components
  main();

// -----------------------------------------------------------------------------
function main () {
  url = '<?=$service_details_title;?>'
  initializeTabs();
  initializeiCheck();
  getEventsTotalsforService();
  initializeDatatable();

<?php
if (isset($_REQUEST['filter'])) {
	echo "$('.nav-tabs a[id=tabEvents]').tab('show');";
}
?>

}

// -----------------------------------------------------------------------------
function initializeTabs () {
  // Activate panel
  var activeTab = getCookie("serviceTab");

  // If there is an active tab in the cookie, activate it
  if (activeTab != "") {
    $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
  } else {
    activeTab = "#panDetails";
    $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
  }

  // Save the selected tab in a cookie
  $('.nav-tabs a').on('shown.bs.tab', function(event) {
    var selectedTab = $(event.target).attr("href");
    setCookie("serviceTab", selectedTab, 30);
  });
  //$('.nav-tabs a[id='+ tab +']').tab('show');
}

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
function getEventsTotalsforService() {
  // stop timer
  // stopTimerRefreshData();

  // get totals and put in boxes
  $.get('php/server/services.php?action=getEventsTotalsforService&url=<?=$servicedetails['mon_URL']?>', function(data) {
    var totalsEvents = JSON.parse(data);

    $('#eventsAll').html      (totalsEvents[0].toLocaleString());
    $('#events2xx').html      (totalsEvents[1].toLocaleString());
    $('#events3xx').html      (totalsEvents[2].toLocaleString());
    $('#events4xx').html      (totalsEvents[3].toLocaleString());
    $('#events5xx').html      (totalsEvents[4].toLocaleString());
    $('#eventsDown').html     (totalsEvents[5].toLocaleString());
  });
    // Timer for refresh data
    //newTimerRefreshData(getEventsTotals);
}

// -----------------------------------------------------------------------------
function initializeDatatable () {
  $('#tableEvents').DataTable({
    'paging'       : true,
    'lengthChange' : true,
    'lengthMenu'   : [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, 'All']],
    //'bLengthChange': false,
    'searching'    : true,
    'ordering'     : true,
    'info'         : true,
    'autoWidth'    : false,
    'pageLength'   : 10,
    'order'        : [[1, 'desc']],
    'columns': [
        { "data": 0 },
        { "data": 1 },
        { "data": 2 },
        { "data": 3 },
        { "data": 4 }
      ],

    'columnDefs'  : [
      {className: 'text-center', targets: [1,2,3,4] },

      //Device Name
      {targets: [0],
       "createdCell": function (td, cellData, rowData, row, col) {
         $(td).html ('<b>'+ cellData +'</b>');
      } },

    ],

    // Processing
    'processing'  : true,
    'language'    : {
      processing: '<table><td width="130px" align="middle">Loading...</td><td><i class="ion ion-ios-loop-strong fa-spin fa-2x fa-fw"></td></table>',
      emptyTable: 'No data',
      "lengthMenu": "<?=$pia_lang['Events_Tablelenght'];?>",
      "search":     "<?=$pia_lang['Events_Searchbox'];?>: ",
      "paginate": {
          "next":       "<?=$pia_lang['Events_Table_nav_next'];?>",
          "previous":   "<?=$pia_lang['Events_Table_nav_prev'];?>"
      },
      "info":           "<?=$pia_lang['Events_Table_info'];?>",
    },
  });
};

// -----------------------------------------------------------------------------
function setServiceData(refreshCallback='') {
  // Check MAC
  if (url == '') {
    return;
  }

  // update data to server
  $.get('php/server/services.php?action=setServiceData'
    + '&url='             + $('#txtURL').val()
    + '&tags='            + $('#txtTags').val()
    + '&mac='             + $('#txtMAC').val()
    + '&alertdown='       + ($('#chkAlertDown')[0].checked * 1)
    + '&alertevents='     + ($('#chkAlertEvents')[0].checked * 1)
    , function(msg) {

    // deactivate button
    // deactivateSaveRestoreData ();
    showMessage (msg);
    // Callback fuction
    if (typeof refreshCallback == 'function') {
      refreshCallback();
    }
  });
}

// -----------------------------------------------------------------------------
function askDeleteService () {
  // Check MAC
  if (url == '') {
    return;
  }

  // Ask delete device
  showModalWarning ('<?=$pia_lang['WebServices_button_Delete_label'];?>', '<?=$pia_lang['WebServices_button_Delete_Warning'];?>',
    '<?=$pia_lang['Gen_Cancel'];?>', '<?=$pia_lang['Gen_Delete'];?>', 'deleteService');
}

// -----------------------------------------------------------------------------
function deleteService () {
  if (url == '') {
    return;
  }
  $.get('php/server/services.php?action=deleteService&url='+ url, function(msg) {
    showMessage (msg);
  });
  // Deactivate controls
  $('#panDetails :input').attr('disabled', true);
}

// -----------------------------------------------------------------------------
function setTextValue (textElement, textValue) {
  $('#'+textElement).val (textValue);
}

// Get Cookie (Tab state)
function getCookie(cookieName) {
  var name = cookieName + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var cookieArray = decodedCookie.split(';');

  for (var i = 0; i < cookieArray.length; i++) {
    var cookie = cookieArray[i];

    while (cookie.charAt(0) == ' ') {
      cookie = cookie.substring(1);
    }

    if (cookie.indexOf(name) == 0) {
      return cookie.substring(name.length, cookie.length);
    }
  }

  return "";
}

// -----------------------------------------------------------------------------
// Set Cookie (Tab state)
function setCookie(cookieName, cookieValue, expirationDays) {
  var date = new Date();
  date.setTime(date.getTime() + (expirationDays * 24 * 60 * 60 * 1000));
  var expires = "expires=" + date.toUTCString();
  document.cookie = cookieName + "=" + cookieValue + ";" + expires + ";path=/";
}

// -----------------------------------------------------------------------------
// Download GeoIP DB
$('#downloadDB-button').on('click', function() {
    var loader = $("#downloader");
    var downloadButton = $(this);
    // Hide the download button
    downloadButton.hide();
    // Display the loading animation
    loader.show();
    // Send an AJAX request to initiate the file download
    $.ajax({
        url: './php/server/services.php?action=downloadGeoDB',
        method: 'GET',
        success: function(response) {
            console.log('Download complete!');
        },
        // error: function() {
        //     console.error('Download error!');
        // },
        complete: function() {
            // Show the download button again
            setTimeout(function () {
              location.reload(true);
            }, 1000);
        }
    });
});

// -----------------------------------------------------------------------------
// Delete GeoIP DB
$('#deleteDB-button').on('click', function() {
    // Send an AJAX request to initiate the file download
    $.ajax({
        url: './php/server/services.php?action=deleteGeoDB',
        method: 'GET',
        success: function(response) {
            console.log('Delete complete!');
        },
        // error: function() {
        //     console.error('Delete error!');
        // },
        complete: function() {
            // Show the download button again
            setTimeout(function () {
              location.reload(true);
            }, 1000);
        }
    });
});
</script>
