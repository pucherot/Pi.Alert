<!-- ---------------------------------------------------------------------------
#  Pi.Alert
#  Open Source Network Guard / WIFI & LAN intrusion detector
#
#  serviceEvents.php - Front module. Service events page
#-------------------------------------------------------------------------------
#  leiweibau 2023                                          GNU GPLv3
#--------------------------------------------------------------------------- -->

<?php
session_start();

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
      <h1 id="pageTitle">
         <?=$pia_lang['WebServices_Events_Title'];?>
      </h1>

      <!-- period selector -->
      <span class="breadcrumb" style="top: 0px;">
        <select class="form-control" id="period" onchange="javascript: periodChanged();">
          <option value="1 day"><?=$pia_lang['Events_Periodselect_today'];?></option>
          <option value="7 days"><?=$pia_lang['Events_Periodselect_LastWeek'];?></option>
          <option value="1 month" selected><?=$pia_lang['Events_Periodselect_LastMonth'];?></option>
          <option value="1 year"><?=$pia_lang['Events_Periodselect_LastYear'];?></option>
          <option value="100 years"><?=$pia_lang['Events_Periodselect_All'];?></option>
        </select>
      </span>
    </section>

<!-- Main content ---------------------------------------------------------- -->
    <section class="content">

<!-- top small box --------------------------------------------------------- -->
      <div class="row">

        <div class="col-lg-2 col-sm-4 col-xs-6">
          <a href="#" onclick="javascript: getEvents('all');">
            <div class="small-box bg-aqua">
              <div class="inner"> <h3 id="eventsAll"> -- </h3>
                <p class="infobox_label"><?=$pia_lang['WebServices_Events_Shortcut_All'];?></p>
              </div>
              <div class="icon"> <i class="fa fa-bolt text-aqua-40"></i> </div>
            </div>
          </a>
        </div>
<!-- top small box --------------------------------------------------------- -->
        <div class="col-lg-2 col-sm-4 col-xs-6">
          <a href="#" onclick="javascript: getEvents('2');">
            <div class="small-box bg-green">
              <div class="inner"> <h3 id="events2xx"> -- </h3>
                <p class="infobox_label"><?=$pia_lang['WebServices_Events_Shortcut_HTTP2xx'];?></p>
              </div>
              <div class="icon"> <i class="bi bi-check2-square text-green-40"></i> </div>
            </div>
          </a>
        </div>
<!-- top small box --------------------------------------------------------- -->
        <div class="col-lg-2 col-sm-4 col-xs-6">
          <a href="#" onclick="javascript: getEvents('3');">
            <div  class="small-box bg-yellow">
              <div class="inner"> <h3 id="events3xx"> -- </h3>
                <p class="infobox_label"><?=$pia_lang['WebServices_Events_Shortcut_HTTP3xx'];?></p>
              </div>
              <div class="icon"> <i class="bi bi-sign-turn-right text-yellow-40"></i> </div>
            </div>
          </a>
        </div>
<!-- top small box --------------------------------------------------------- -->
        <div class="col-lg-2 col-sm-4 col-xs-6">
          <a href="#" onclick="javascript: getEvents('4');">
            <div  class="small-box bg-yellow">
              <div class="inner"> <h3 id="events4xx"> -- </h3>
                <p class="infobox_label"><?=$pia_lang['WebServices_Events_Shortcut_HTTP4xx'];?></p>
              </div>
              <div class="icon"> <i class="bi bi-exclamation-square text-yellow-40"></i> </div>
            </div>
          </a>
        </div>
<!-- top small box --------------------------------------------------------- -->
        <div class="col-lg-2 col-sm-4 col-xs-6">
          <a href="#" onclick="javascript: getEvents('5');">
            <div  class="small-box bg-yellow">
              <div class="inner"> <h3 id="events5xx"> -- </h3>
                <p class="infobox_label"><?=$pia_lang['WebServices_Events_Shortcut_HTTP5xx'];?></p>
              </div>
              <div class="icon"> <i class="bi bi-database-x text-yellow-40"></i> </div>
            </div>
          </a>
        </div>
<!-- top small box --------------------------------------------------------- -->
        <div class="col-lg-2 col-sm-4 col-xs-6">
          <a href="#" onclick="javascript: getEvents('99999999');">
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

<!-- datatable ------------------------------------------------------------- -->
      <div class="row">
        <div class="col-xs-12">
          <div id="tableEventsBox" class="box">
            <div class="box-header">
              <h3 id="tableEventsTitle" class="box-title text-gray">Events</h3>
            </div>
            <div class="box-body table-responsive">
              <table id="tableEvents" class="table table-bordered table-hover table-striped ">
                <thead>
                  <tr>
                    <th><?=$pia_lang['WebServices_Events_TableHead_URL'];?></th>
                    <th><?=$pia_lang['WebServices_Events_TableHead_TargetIP'];?></th>
                    <th><?=$pia_lang['WebServices_Events_TableHead_ScanTime'];?></th>
                    <th><?=$pia_lang['WebServices_Events_TableHead_StatusCode'];?></th>
                    <th><?=$pia_lang['WebServices_Events_TableHead_ResponsTime'];?></th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>
      </div>

<!-- ----------------------------------------------------------------------- -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<!-- ----------------------------------------------------------------------- -->
<?php
require 'php/templates/footer.php';
?>

<!-- Datatable -->
<link rel="stylesheet" href="lib/AdminLTE/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
<script src="lib/AdminLTE/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="lib/AdminLTE/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<!-- page script ----------------------------------------------------------- -->
<script>
  var parPeriod       = 'Front_ServicesEvents_Period';
  var parTableRows    = 'Front_ServicesEvents_Rows';

  var eventsType      = 'all';
  var period          = '';
  var tableRows       = 50;

  // Read parameters & Initialize components
  main();

// -----------------------------------------------------------------------------
function main () {
  // get parameter value
  $.get('php/server/parameters.php?action=get&parameter='+ parPeriod, function(data) {
    var result = JSON.parse(data);
    if (result) {
      period = result;
      $('#period').val(period);
    }

    // get parameter value
    $.get('php/server/parameters.php?action=get&parameter='+ parTableRows, function(data) {
      var result = JSON.parse(data);
      if (Number.isInteger (result) ) {
          tableRows = result;
      }

      // Initialize components
      initializeDatatable();

      // query data
      getEventsTotals();
      getEvents(eventsType);
    });
  });
}

// -----------------------------------------------------------------------------
function initializeDatatable () {
  $('#tableEvents').DataTable({
    'paging'       : true,
    'lengthChange' : true,
    'lengthMenu'   : [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, 'All']],
    'searching'    : true,
    'ordering'     : true,
    'info'         : true,
    'autoWidth'    : false,
    'columns': [
        { "data": 0 },
        { "data": 4 },
        { "data": 1 },
        { "data": 2 },
        { "data": 3 }
      ],

    // Parameters
    'pageLength'   : tableRows,
    'columnDefs'  : [
      {className: 'text-center', targets: [1,3] },
      {className: 'text-right',  targets: [] },
      {width:     '220px',       targets: [0] },
      {width:     '120px',       targets: [1] },
      {width:     '80px',        targets: [3] },

      {targets: [0],
       "createdCell": function (td, cellData, rowData, row, col) {
         $(td).html ('<b><a href="serviceDetails.php?url='+ rowData[0] +'" class="">'+ cellData +'</a></b>');
      } },
    ],

    // Processing
    'processing'  : true,
    'language'    : {
      processing: '<table><td width="130px" align="middle">Loading...</td><td><i class="ion ion-ios-sync fa-spin fa-2x fa-fw"></td></table>',
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

  // Save Parameter rows when changed
  $('#tableEvents').on( 'length.dt', function ( e, settings, len ) {
    setParameter (parTableRows, len);
  } );
};

// -----------------------------------------------------------------------------
function periodChanged () {
  // Save Parameter Period
  period = $('#period').val();
  setParameter (parPeriod, period);

  // Requery totals and events
  getEventsTotals();
  getEvents(eventsType);

}

// -----------------------------------------------------------------------------
function getEventsTotals() {
  // stop timer
  // stopTimerRefreshData();

  // get totals and put in boxes
  $.get('php/server/services.php?action=getEventsTotals&period='+ period, function(data) {
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
function getEvents (p_eventsType) {
  // Save status selected
  eventsType = p_eventsType;

  // Define color & title for the status selected
  switch (eventsType) {
    case 'all':       tableTitle = '<?=$pia_lang['WebServices_Events_Shortcut_All'];?>';      color = 'aqua';    break;
    case '2':         tableTitle = '<?=$pia_lang['WebServices_Events_Shortcut_HTTP2xx'];?>';  color = 'green';   break;
    case '3':         tableTitle = '<?=$pia_lang['WebServices_Events_Shortcut_HTTP3xx'];?>';  color = 'yellow';  break;
    case '4':         tableTitle = '<?=$pia_lang['WebServices_Events_Shortcut_HTTP4xx'];?>';  color = 'yellow';  break;
    case '5':         tableTitle = '<?=$pia_lang['WebServices_Events_Shortcut_HTTP5xx'];?>';  color = 'yellow';  break;
    case '99999999':  tableTitle = '<?=$pia_lang['WebServices_Events_Shortcut_Down'];?>';     color = 'red';     break;
    default:          tableTitle = '<?=$pia_lang['WebServices_Events_Shortcut_All'];?>';      boxClass = '';     break;
  }

  // Set title and color
  $('#tableEventsTitle')[0].className = 'box-title text-' + color;
  $('#tableEventsBox')[0].className = 'box box-' + color;
  $('#tableEventsTitle').html (tableTitle);

  // Define new datasource URL and reload
  $('#tableEvents').DataTable().clear();
  $('#tableEvents').DataTable().draw();
  $('#tableEvents').DataTable().order ([2,"desc"]);
  $('#tableEvents').DataTable().ajax.url('php/server/services.php?action=getEvents&type=' + eventsType +'&period='+ period ).load();
};

</script>
