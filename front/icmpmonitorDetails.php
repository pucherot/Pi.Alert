<!-- ---------------------------------------------------------------------------
#  Pi.Alert
#  Open Source Network Guard / WIFI & LAN intrusion detector
#
#  icmpmonitorDetails.php - Front module. Service management page
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

if (filter_var($_REQUEST['hostip'], FILTER_FLAG_IPV4) || filter_var($_REQUEST['hostip'], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
	$hostip = $_REQUEST['hostip'];
} else {
	header('Location: /pialert/index.php');
	exit;
}

require 'php/templates/header.php';
//require 'php/server/db.php';
require 'php/server/graph.php';
require 'php/server/journal.php';

# Init DB Connection
$db_file = '../db/pialert.db';
$db = new SQLite3($db_file);
$db->exec('PRAGMA journal_mode = wal;');

// -----------------------------------------------------------------------------------
function get_hostip_details($hostip) {
	global $db;

	$mon_res = $db->query('SELECT * FROM ICMP_Mon WHERE icmp_ip="' . $hostip . '"');
	$row = $mon_res->fetchArray();
	return $row;
}

// -----------------------------------------------------------------------------------
# Set Filter of fallback to default
$icmpfilter = $_REQUEST['icmpfilter'];
if (!isset($icmpfilter)) {$icmpfilter = 'all';}

function get_icmphost_events_table($icmp_ip, $icmpfilter) {
	global $db;
	//global $current_service_IP;

	if ($icmpfilter == 'all') {
		$filter_sql = "";
	} elseif ($icmpfilter == 'Online') {
		$filter_sql = 'AND icmpeve_Present=1';
	} elseif ($icmpfilter == 'Offline') {
		$filter_sql = 'AND icmpeve_Present=0';
	}
	$icmp_res = $db->query('SELECT * FROM ICMP_Mon WHERE icmp_ip="' . $icmp_ip . '"');
	while ($rowa = $icmp_res->fetchArray(SQLITE3_ASSOC)) {
		$icmp_hostname = $rowa['icmp_hostname'];
	}

	$icmpeve_res = $db->query('SELECT * FROM ICMP_Mon_Events WHERE icmpeve_ip="' . $icmp_ip . '"' . $filter_sql);
	while ($row = $icmpeve_res->fetchArray()) {
		if ($icmp_hostname != "" && strlen($icmp_hostname) > 0) {$icmpeve_ip = $icmp_hostname;} else { $icmpeve_ip = $row['icmpeve_ip'];}
		echo '<tr>
              <td>' . $icmpeve_ip . '</td>
              <td>' . $row['icmpeve_DateTime'] . '</td>
              <td>' . $row['icmpeve_Present'] . '</td>
              <td>' . $row['icmpeve_avgrtt'] . '</td>
          </tr>';
	}
}

// -----------------------------------------------------------------------------------
function set_table_headline($icmpfilter) {
	global $pia_lang;

	if ($icmpfilter == 'all') {
		echo '<h3 class="text-aqua" style="display: inline-block;font-size: 18px; margin: 0; line-height: 1;">' . $pia_lang['WebServices_Events_Shortcut_All'] . '</h3>';
	} elseif ($icmpfilter == 'Online') {
		echo '<h3 class="text-green" style="display: inline-block;font-size: 18px; margin: 0; line-height: 1;">' . $pia_lang['ICMPMonitor_Shortcut_Online'] . '</h3>';
	} elseif ($icmpfilter == 'Offline') {
		echo '<h3 class="text-red" style="display: inline-block;font-size: 18px; margin: 0; line-height: 1;">' . $pia_lang['ICMPMonitor_Shortcut_Offline'] . '</h3>';
	}
}

$icmpmonitorDetails = get_hostip_details($hostip);

// -----------------------------------------------------------------------------------
// Get Online Graph Arrays
$graph_arrays = array();
$graph_arrays = prepare_graph_arrays_ICMPHost($hostip);
//print_r($graph_arrays);
$Pia_Graph_ICMPHost_Time = $graph_arrays[0];
$Pia_Graph_ICMPHost_Up = $graph_arrays[1];
$Pia_Graph_ICMPHost_Down = $graph_arrays[2];

?>

<!-- Page ------------------------------------------------------------------ -->
  <div class="content-wrapper">

<!-- Content header--------------------------------------------------------- -->
    <section class="content-header">
      <?php require 'php/templates/notification.php';?>

      <h1 id="pageTitle">
        <?php echo $hostip; ?>
      </h1>
    </section>

<!-- Main content ---------------------------------------------------------- -->
    <section class="content">

<!-- top small box --------------------------------------------------------- -->
      <div class="row">

        <div class="col-lg-4 col-sm-4 col-xs-6">
          <a href="./icmpmonitorDetails.php?hostip=<?php echo $hostip ?>&icmpfilter=all">
            <div class="small-box bg-aqua">
              <div class="inner"> <h3 id="eventsAll"> -- </h3>
                <p class="infobox_label"><?php echo $pia_lang['WebServices_Events_Shortcut_All']; ?></p>
              </div>
              <div class="icon"> <i class="fa fa-bolt text-aqua-40"></i> </div>
            </div>
          </a>
        </div>

<!-- top small box --------------------------------------------------------- -->
        <div class="col-lg-4 col-sm-4 col-xs-6">
          <a href="./icmpmonitorDetails.php?hostip=<?php echo $hostip ?>&icmpfilter=Online">
            <div class="small-box bg-green">
              <div class="inner"> <h3 id="eventsOnline"> -- </h3>
                <p class="infobox_label"><?php echo $pia_lang['ICMPMonitor_Shortcut_Online']; ?></p>
              </div>
              <div class="icon"> <i class="bi bi-check2-square text-green-40"></i> </div>
            </div>
          </a>
        </div>

<!-- top small box --------------------------------------------------------- -->
        <div class="col-lg-4 col-sm-4 col-xs-6">
          <a href="./icmpmonitorDetails.php?hostip=<?php echo $hostip ?>&icmpfilter=Offline">
            <div  class="small-box bg-red">
              <div class="inner"> <h3 id="eventsOffline"> -- </h3>
                <p class="infobox_label"><?php echo $pia_lang['ICMPMonitor_Shortcut_Offline']; ?></p>
              </div>
              <div class="icon"> <i class="bi bi-sign-turn-right text-red-40"></i> </div>
            </div>
          </a>
        </div>

      </div>
      <!-- /.row -->

<!-- tab control------------------------------------------------------------ -->
      <div class="row">
        <div class="col-lg-12 col-sm-12 col-xs-12">
        <!-- <div class="box-transparent"> -->

          <div id="navDevice" class="nav-tabs-custom">
            <ul class="nav nav-tabs" style="fon t-size:16px;">
              <li class=""> <a id="tabDetails" href="#panDetails" data-toggle="tab"> <?php echo $pia_lang['DevDetail_Tab_Details']; ?></a></li>
              <li class=""> <a id="tabEvents" href="#panEvents" data-toggle="tab"> <?php echo $pia_lang['DevDetail_Tab_Events']; ?></a></li>
              <li class=""> <a id="tabGraph" href="#panGraph" data-toggle="tab"> <?php echo $pia_lang['WebServices_Tab_Graph']; ?></a></li>
            </ul>

            <div class="tab-content" style="min-height: 430px;">

<!-- tab page 1 ------------------------------------------------------------ -->
<!--
              <div class="tab-pane fade in active" id="panDetails">
-->
              <div class="tab-pane" id="panDetails">

                <div class="row">
    <!-- column 1 -->
                  <div class="col-sm-6 col-xs-12">
                    <h4 class="bottom-border-aqua"><?php echo $pia_lang['DevDetail_MainInfo_Title']; ?></h4>
                    <div class="box-body form-horizontal">

                      <!-- URL -->
                      <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo $pia_lang['ICMPMonitor_label_IP']; ?></label>
                        <div class="col-sm-9">
                          <input class="form-control" id="txtIP" type="text" readonly value="<?php echo $icmpmonitorDetails['icmp_ip'] ?>">
                        </div>
                      </div>

                      <!-- Tags -->
                      <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo $pia_lang['ICMPMonitor_label_Hostname']; ?></label>
                        <div class="col-sm-9">
                          <input class="form-control" id="txtHostname" type="text" value="<?php echo $icmpmonitorDetails['icmp_hostname'] ?>">
                        </div>
                      </div>

                      <!-- Owner -->
                      <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo $pia_lang['DevDetail_MainInfo_Owner']; ?></label>
                        <div class="col-sm-9">
                          <div class="input-group">
                            <input class="form-control" id="txtOwner" type="text" value="<?php echo $icmpmonitorDetails['icmp_owner'] ?>">
                            <div class="input-group-btn">
                              <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="fa fa-caret-down"></span></button>
                              <ul id="dropdownOwner" class="dropdown-menu dropdown-menu-right">
                              </ul>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Type -->
                      <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo $pia_lang['DevDetail_MainInfo_Type']; ?></label>
                        <div class="col-sm-9">
                          <div class="input-group">
                            <input class="form-control" id="txtDeviceType" type="text" value="<?php echo $icmpmonitorDetails['icmp_type'] ?>">
                            <div class="input-group-btn">
                              <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" >
                                <span class="fa fa-caret-down"></span></button>
                              <ul id="dropdownDeviceType" class="dropdown-menu dropdown-menu-right">
                                <li><a href="javascript:void(0)" onclick="setTextValue('txtDeviceType','Smartphone')"> Smartphone </a></li>
                                <li><a href="javascript:void(0)" onclick="setTextValue('txtDeviceType','Laptop')">     Laptop     </a></li>
                                <li><a href="javascript:void(0)" onclick="setTextValue('txtDeviceType','PC')">         PC         </a></li>
                                <li><a href="javascript:void(0)" onclick="setTextValue('txtDeviceType','Others')">     Others     </a></li>
                              </ul>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Group -->
                      <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo $pia_lang['DevDetail_MainInfo_Group']; ?></label>
                        <div class="col-sm-9">
                          <div class="input-group">
                            <input class="form-control" id="txtGroup" type="text" value="<?php echo $icmpmonitorDetails['icmp_group'] ?>">
                            <div class="input-group-btn">
                              <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="fa fa-caret-down"></span></button>
                              <ul id="dropdownGroup" class="dropdown-menu dropdown-menu-right">
                                <li><a href="javascript:void(0)" onclick="setTextValue('txtGroup','Always On')"> Always On </a></li>
                                <li><a href="javascript:void(0)" onclick="setTextValue('txtGroup','Friends')">   Friends   </a></li>
                                <li><a href="javascript:void(0)" onclick="setTextValue('txtGroup','Personal')">  Personal  </a></li>
                                <li class="divider"></li>
                                <li><a href="javascript:void(0)" onclick="setTextValue('txtGroup','Others')">    Others    </a></li>
                              </ul>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Location -->
                      <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo $pia_lang['DevDetail_MainInfo_Location']; ?></label>
                        <div class="col-sm-9">
                          <div class="input-group">
                            <input class="form-control" id="txtLocation" type="text" value="<?php echo $icmpmonitorDetails['icmp_location'] ?>">
                            <div class="input-group-btn">
                              <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="fa fa-caret-down"></span></button>
                              <ul id="dropdownLocation" class="dropdown-menu dropdown-menu-right">
                                <li><a href="javascript:void(0)" onclick="setTextValue('txtLocation','Bathroom')">    Bathroom</a></li>
                                <li><a href="javascript:void(0)" onclick="setTextValue('txtLocation','Bedroom')">     Bedroom</a></li>
                                <li><a href="javascript:void(0)" onclick="setTextValue('txtLocation','Hall')">        Hall</a></li>
                                <li><a href="javascript:void(0)" onclick="setTextValue('txtLocation','Kitchen')">     Kitchen</a></li>
                                <li><a href="javascript:void(0)" onclick="setTextValue('txtLocation','Living room')"> Living room</a></li>
                                <li class="divider"></li>
                                <li><a href="javascript:void(0)" onclick="setTextValue('txtLocation','Others')">      Others</a></li>
                              </ul>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Notes -->
                      <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo $pia_lang['WebServices_label_Notes']; ?></label>
                        <div class="col-sm-9">
                          <input class="form-control" id="txtNotes" type="text" readonly value="<?php echo $icmpmonitorDetails['icmp_Notes'] ?>">
                        </div>
                      </div>

                    </div>
                  </div>

    <!-- column 2 -->
                  <div class="col-sm-6 col-xs-12" style="margin-bottom: 50px;">
                    <h4 class="bottom-border-aqua"><?php echo $pia_lang['DevDetail_EveandAl_Title']; ?></h4>
                    <div class="box-body form-horizontal">

                      <!-- Last Scan -->
                      <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $pia_lang['WebServices_label_ScanTime']; ?></label>
                        <div class="col-sm-8">
                          <input class="form-control" id="txtLastScan" type="text" readonly value="<?php echo $icmpmonitorDetails['icmp_LastScan'] ?>">
                        </div>
                      </div>

                      <!-- Last HTTP Status -->
                      <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $pia_lang['ICMPMonitor_label_RTT']; ?></label>
                        <div class="col-sm-8">
                          <input class="form-control" id="txtavgrtt" type="text" readonly value="<?php echo $icmpmonitorDetails['icmp_avgrtt'] ?>">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="col-xs-4 control-label"><?php echo $pia_lang['Device_TableHead_Favorite']; ?></label>
                        <div class="col-xs-4" style="padding-top:6px;">
                          <input class="checkbox orange" id="chkFavorit" <?php if ($icmpmonitorDetails['icmp_Favorite'] == 1) {echo 'checked';}?> type="checkbox">
                        </div>
                      </div>

                      <!-- Alert events -->
                      <div class="form-group">
                        <label class="col-xs-4 control-label"><?php echo $pia_lang['WebServices_label_AlertEvents']; ?></label>
                        <div class="col-xs-4" style="padding-top:6px;">
                          <input class="checkbox blue" id="chkAlertEvents" <?php if ($icmpmonitorDetails['icmp_AlertEvents'] == 1) {echo 'checked';}?> type="checkbox">
                        </div>
                      </div>

                      <!-- Alert Down -->
                      <div class="form-group">
                        <label class="col-xs-4 control-label"><?php echo $pia_lang['WebServices_label_AlertDown']; ?></label>
                        <div class="col-xs-4" style="padding-top:6px;">
                          <input class="checkbox red" id="chkAlertDown" <?php if ($icmpmonitorDetails['icmp_AlertDown'] == 1) {echo 'checked';}?> type="checkbox">
                        </div>
                      </div>

                    </div>
                  </div>

                  <!-- Buttons -->
                  <div class="col-xs-12">
                    <div class="pull-right">
                        <button type="button" class="btn btn-danger servicedet_button_space"  id="btnDelete"   onclick="deleteICMPHost()"> <?php echo $pia_lang['Gen_Delete']; ?> </button>
                        <button type="button" class="btn btn-default servicedet_button_space" id="btnRestore"  onclick="location.reload()">  <?php echo $pia_lang['Gen_Cancel']; ?> </button>
                        <button type="button" class="btn btn-primary servicedet_button_space" id="btnSave"     onclick="setICMPHostData()" >  <?php echo $pia_lang['Gen_Save']; ?> </button>
                    </div>
                  </div>

                </div>
              </div>

<!-- Events ------------------------------------------------------------ -->
              <div class="tab-pane fade table-responsive" id="panEvents">
<?php
# Create Event table headline
set_table_headline($icmpfilter);
?>
                <!-- Datatable Events -->
                <table id="tableEvents" class="table table-bordered table-hover table-striped ">
                  <thead>
                    <tr>
                      <!-- <th>Service URL</th> -->
                      <th><?php echo $pia_lang['WebServices_tablehead_TargetIP']; ?></th>
                      <th><?php echo $pia_lang['WebServices_tablehead_ScanTime']; ?></th>
                      <th><?php echo $pia_lang['WebServices_tablehead_Response_Time']; ?></th>
                      <th><?php echo $pia_lang['Device_TableHead_Status']; ?></th>
                    </tr>
                  </thead>
                  <tbody>
<?php
# Create Event table
get_icmphost_events_table($hostip, $icmpfilter);
?>
                  </tbody>
                </table>
              </div>

<!-- Graph ------------------------------------------------------------ -->
              <div class="tab-pane fade table-responsive" id="panGraph">
                <h4 class="text-aqua" style="font-size: 18px;margin: 0;line-height: 1; margin-bottom: 20px;"><?php echo $pia_lang['WebServices_Chart_a']; ?> <span class="maxlogage-interval">24</span> <?php echo $pia_lang['WebServices_Chart_b']; ?></h4>
                <div class="col-md-12">
                  <div class="chart" style="height: 150px;">
                    <script src="lib/AdminLTE/bower_components/chart.js/Chart.js"></script>
                    <canvas id="ServiceChart"></canvas>
                  </div>
                </div>
                <script src="js/graph_online_history.js"></script>
                <script>
                  var pia_js_online_history_time = [<?php pia_graph_devices_data($Pia_Graph_ICMPHost_Time);?>];
                  var pia_js_online_history_online = [<?php pia_graph_devices_data($Pia_Graph_ICMPHost_Up);?>];
                  var pia_js_online_history_offline = [<?php pia_graph_devices_data($Pia_Graph_ICMPHost_Down);?>];
                  pia_draw_graph_icmphost_history(pia_js_online_history_time, pia_js_online_history_offline, pia_js_online_history_online);
                </script>

                <div class="col-md-12 bottom-border-aqua" style="margin-top: 30px; opacity: 0.7"></div>

                <div class="col-md-12">
                  <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-2" style="font-weight: 600;"><?php echo $pia_lang['WebServices_Stats_Code']; ?>:</div>
                    <div class="col-sm-2"><i class="fa fa-w fa-circle text-green"></i> Online (<?php echo $http2xx; ?>)</div>
                    <div class="col-sm-2"><i class="fa fa-w fa-circle text-red"></i> <?php echo $pia_lang['WebServices_Page_down']; ?> (<?php echo $httpdown; ?>)</div>
                  </div>
                </div>

                <div class="col-md-12 bottom-border-aqua" style="margin-top: 10px; opacity: 0.7"></div>

                <div class="col-md-12">
                  <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-2" style="font-weight: 600;"><?php echo $pia_lang['WebServices_Stats_Time']; ?>:</div>
                    <div class="col-sm-2">&Oslash; <?php echo $latency_average; ?> ms</div>
                    <div class="col-sm-2"><?php echo $pia_lang['WebServices_Stats_Time_min']; ?> <?php echo $latency_min; ?> ms</div>
                    <div class="col-sm-2"><?php echo $pia_lang['WebServices_Stats_Time_max']; ?> <?php echo $latency_max; ?> ms</div>
                    <div class="col-sm-4"><span style="opacity: 0.6"><?php echo $pia_lang['WebServices_Stats_comment_a']; ?></span></div>
                  </div>

                </div>


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


<!-- ----------------------------------------------------------------------- -->
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
  var parPeriod           = 'Front_icmpmonitorDetails_Period';
  var parTab              = 'Front_icmpmonitorDetails_Tab';
  var parEventsRows       = 'Front_icmpmonitorDetails_Events_Rows';
  var period              = '1 month';
  var tab                 = 'tabDetails'
  //var eventsRows          = 25;

  // Read parameters & Initialize components
  main();

// -----------------------------------------------------------------------------

function main () {
  hostip = '<?php echo $hostip; ?>'
  initializeTabs();
  initializeiCheck();
  getEventsTotalsforICMPHost();
  initializeDatatable();
  initializeCombos();

<?php
if (isset($_REQUEST['icmpfilter'])) {
	echo "$('.nav-tabs a[id=tabEvents]').tab('show');";
}
?>

}

function initializeTabs () {
  // Activate panel
  var activeTab = getCookie("icmpTab");

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
    setCookie("icmpTab", selectedTab, 30);
  });
  //$('.nav-tabs a[id='+ tab +']').tab('show');
}

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

function getEventsTotalsforICMPHost() {
  // stop timer
  // stopTimerRefreshData();

  // get totals and put in boxes
  $.get('php/server/icmpmonitor.php?action=getEventsTotalsforICMP&hostip=<?php echo $icmpmonitorDetails['icmp_ip'] ?>', function(data) {
    var totalsEvents = JSON.parse(data);

    $('#eventsAll').html      (totalsEvents[0].toLocaleString());
    $('#eventsOnline').html   (totalsEvents[1].toLocaleString());
    $('#eventsOffline').html  (totalsEvents[2].toLocaleString());
  });
    // Timer for refresh data
    //newTimerRefreshData(getEventsTotals);
}


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
        { "data": 3 },
        { "data": 2 }
      ],

    'columnDefs'  : [
      {className: 'text-center', targets: [1,2,3] },
      {targets: [2],
        'createdCell': function (td, cellData, rowData, row, col) {
          if (cellData == 99999){
            $(td).html ('TimeOut');
          } else {
            $(td).html (cellData);
          }
      } },
      {targets: [3],
        'createdCell': function (td, cellData, rowData, row, col) {
          if (cellData == 1){
            $(td).html ('<span class="badge bg-green">Online</span>');
          } else {
            $(td).html ('<span class="badge bg-gray text-white">Down/Offline</span>');
          }
      } },
    ],

    // Processing
    'processing'  : true,
    'language'    : {
      processing: '<table><td width="130px" align="middle">Loading...</td><td><i class="ion ion-ios-loop-strong fa-spin fa-2x fa-fw"></td></table>',
      emptyTable: 'No data',
      "lengthMenu": "<?php echo $pia_lang['Events_Tablelenght']; ?>",
      "search":     "<?php echo $pia_lang['Events_Searchbox']; ?>: ",
      "paginate": {
          "next":       "<?php echo $pia_lang['Events_Table_nav_next']; ?>",
          "previous":   "<?php echo $pia_lang['Events_Table_nav_prev']; ?>"
      },
      "info":           "<?php echo $pia_lang['Events_Table_info']; ?>",
    },
  });
};

// -----------------------------------------------------------------------------
function setICMPHostData(refreshCallback='') {
  if (hostip == '') {
    return;
  }

  // update data to server
  $.get('php/server/icmpmonitor.php?action=setICMPHostData'
    + '&icmp_ip='         + $('#txtIP').val()
    + '&icmp_hostname='   + $('#txtHostname').val()
    + '&icmp_type='       + $('#txtDeviceType').val()
    + '&icmp_group='      + $('#txtGroup').val()
    + '&icmp_location='   + $('#txtLocation').val()
    + '&icmp_owner='      + $('#txtOwner').val()
    + '&icmp_notes='      + $('#txtNotes').val()
    + '&favorit='         + ($('#chkFavorit')[0].checked * 1)
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
function askdeleteICMPHost () {
  if (hostip == '') {
    return;
  }

  // Ask delete device
  showModalWarning ('<?php echo $pia_lang['WebServices_button_Delete_label']; ?>', '<?php echo $pia_lang['WebServices_button_Delete_Warning']; ?>',
    '<?php echo $pia_lang['Gen_Cancel']; ?>', '<?php echo $pia_lang['Gen_Delete']; ?>', 'deleteICMPHost');
}

// -----------------------------------------------------------------------------
function deleteICMPHost () {
  if (hostip == '') {
    return;
  }

  // Delete device
  $.get('php/server/icmpmonitor.php?action=deleteICMPHost&icmp_ip='+ hostip, function(msg) {
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
function initializeCombos () {
  // Initialize combos with queries
  initializeCombo ( $('#dropdownOwner')[0],                      'getOwners',       'txtOwner');
  initializeCombo ( $('#dropdownDeviceType')[0],                 'getDeviceTypes',  'txtDeviceType');
  initializeCombo ( $('#dropdownGroup')[0],                      'getGroups',       'txtGroup');
  initializeCombo ( $('#dropdownLocation')[0],                   'getLocations',    'txtLocation');

  // Initialize static combos
  //initializeComboSkipRepeated ();
}

function initializeComboSkipRepeated () {
  // find dropdown menu element
  HTMLelement = $('#dropdownSkipRepeated')[0];
  HTMLelement.innerHTML = ''

  // for each item
  skipRepeatedItems.forEach(function (item, index) {
    // add dropdown item
    HTMLelement.innerHTML += ' <li><a href="javascript:void(0)" ' +
      'onclick="setTextValue(\'txtSkipRepeated\',\'' + item + '\');">'+
      item +'</a></li>';
  });
}

function initializeCombo (HTMLelement, queryAction, txtDataField) {
  // get data from server
  $.get('php/server/devices.php?action='+queryAction, function(data) {
    var listData = JSON.parse(data);
    var order = 1;

    HTMLelement.innerHTML = ''
    // for each item
    listData.forEach(function (item, index) {
      // insert line divisor
      if (order != item['order']) {
        HTMLelement.innerHTML += '<li class="divider"></li>';
        order = item['order'];
      }

      id = item['name'];
      // use explicitly specified id (value) if avaliable
      if(item['id'])
      {
        id = item['id'];
      }

      // add dropdown item
      HTMLelement.innerHTML +=
        '<li><a href="javascript:void(0)" onclick="setTextValue(\''+
        txtDataField +'\',\''+ id +'\')">'+ item['name'] + '</a></li>'
    });
  });
}
</script>
