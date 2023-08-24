<?php
//------------------------------------------------------------------------------
//  Pi.Alert
//  Open Source Network Guard / WIFI & LAN intrusion detector
//
//  icmpmonitor.php - Front module. Ping polling
//------------------------------------------------------------------------------
//  leiweibau  2023        https://github.com/leiweibau     GNU GPLv3
//------------------------------------------------------------------------------
session_start();
error_reporting(0);

if ($_SESSION["login"] != 1) {
	header('Location: ./index.php');
	exit;
}

require 'php/templates/header.php';
require 'php/server/db.php';
require 'php/server/journal.php';

$DBFILE = '../db/pialert.db';
OpenDB();
?>

<!-- Page ------------------------------------------------------------------ -->

<div class="content-wrapper">

<!-- Content header--------------------------------------------------------- -->
    <section class="content-header">
    <?php require 'php/templates/notification.php';?>
      <h1 id="pageTitle">
         <?php echo $pia_lang['ICMPMonitor_Title']; ?>
      <button type="button" class="btn btn-xs btn-success icmplist_add_ip" data-toggle="modal" data-target="#modal-add-monitoringIP"><i class="bi bi-plus-lg" style="font-size:1.5rem;"></i></button>
      </h1>

<!-- Modals New URL ----------------------------------------------------------------- -->

       <form role="form">
            <div class="modal fade" id="modal-add-monitoringIP">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                            <h4 class="modal-title"><?php echo $pia_lang['ICMPMonitor_headline_IP']; ?></h4>
                        </div>
                        <div class="modal-body">
                            <div style="height: 230px;">
                            <div class="form-group col-xs-12">
                              <label class="col-xs-3 control-label"><?php echo $pia_lang['ICMPMonitor_label_IP']; ?></label>
                              <div class="col-xs-9">
                                <input type="text" class="form-control" id="icmphost_ip" placeholder="Host IP">
                              </div>
                            </div>
                            <div class="form-group col-xs-12">
                              <label class="col-xs-3 control-label"><?php echo $pia_lang['ICMPMonitor_label_Hostname']; ?></label>
                              <div class="col-xs-9">
                                <input type="text" class="form-control" id="icmphost_name" placeholder="Hostname">
                              </div>
                            </div>
                            <div class="form-group col-xs-12">
                                <label class="col-xs-3 control-label"><?php echo $pia_lang['Device_TableHead_Favorite']; ?></label>
                                <div class="col-xs-9" style="margin-top: 0px;">
                                  <input class="checkbox orange" id="insFavorite" type="checkbox">
                                </div>
                            </div>
                            <div class="form-group col-xs-12">
                                <label class="col-xs-3 control-label"><?php echo $pia_lang['WebServices_label_AlertEvents']; ?></label>
                                <div class="col-xs-9" style="margin-top: 0px;">
                                  <input class="checkbox blue" id="insAlertEvents" type="checkbox">
                                </div>
                            </div>
                            <div class="form-group col-xs-12">
                                <label class="col-xs-3 control-label"><?php echo $pia_lang['WebServices_label_AlertDown']; ?></label>
                                <div class="col-xs-9" style="margin-top: 0px;">
                                  <input class="checkbox red" id="insAlertDown" type="checkbox">
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php echo $pia_lang['Gen_Close']; ?></button>
                            <button type="button" class="btn btn-primary" id="btnInsert" onclick="insertNewICMPHost()" ><?php echo $pia_lang['Gen_Save']; ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </section>

    <!-- Main content ---------------------------------------------------------- -->
    <section class="content">

      <div class="row">
        <div class="col-lg-3 col-sm-3 col-xs-6">
          <div class="small-box bg-aqua">
            <div class="inner"><h3 id="devicesAll"> -- </h3>
                <p class="infobox_label"><?php echo $pia_lang['Device_Shortcut_AllDevices']; ?></p>
            </div>
            <div class="icon"><i class="fa fa-laptop text-aqua-40"></i></div>
          </div>
        </div>

        <div class="col-lg-3 col-sm-3 col-xs-6">
          <div class="small-box bg-green">
            <div class="inner"><h3 id="devicesConnected"> -- </h3>
                <p class="infobox_label"><?php echo $pia_lang['Device_Shortcut_Connected']; ?></p>
            </div>
            <div class="icon"><i class="fa fa-plug text-green-40"></i></div>
          </div>
        </div>

        <div class="col-lg-3 col-sm-3 col-xs-6">
          <div class="small-box bg-yellow">
            <div class="inner"><h3 id="devicesFavorites"> -- </h3>
                <p class="infobox_label"><?php echo $pia_lang['Device_Shortcut_Favorites']; ?></p>
            </div>
            <div class="icon"><i class="fa fa-star text-yellow-40"></i></div>
          </div>
        </div>

        <div class="col-lg-3 col-sm-3 col-xs-6">
          <div class="small-box bg-red">
            <div class="inner"><h3 id="devicesDown"> -- </h3>
                <p class="infobox_label"><?php echo $pia_lang['Device_Shortcut_DownAlerts']; ?></p>
            </div>
            <div class="icon"><i class="fa fa-warning text-red-40"></i></div>
          </div>
        </div>

      </div>

      <div class="row">
        <div class="col-xs-12">
          <div id="tableDevicesBox" class="box">

            <!-- box-header -->
            <div class="box-header">
              <h3 id="tableDevicesTitle" class="box-title text-aqua"><?php echo $pia_lang['Device_Title']; ?></h3>
            </div>

            <div class="box-body table-responsive">
              <table id="tableDevices" class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                  <th><?=$pia_lang['Device_TableHead_Name']?></th>
                  <th>IP</th>
                  <th><?=$pia_lang['Device_TableHead_Favorite']?></th>
                  <th><?=$pia_lang['WebServices_Events_TableHead_ResponsTime']?></th>
                  <th style="white-space: nowrap;"><?=$pia_lang['WebServices_tablehead_ScanTime']?></th>
                  <th><?=$pia_lang['Device_TableHead_Status']?></th>
                  <th>Present</th>
                  <th>RowID</th>
                </tr>
                </thead>
              </table>
            </div>
            <!-- /.box-body -->

          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    <div style="width: 100%; height: 20px;"></div>
    <!-- ----------------------------------------------------------------------- -->

    </section>

  </div>
  <!-- /.content-wrapper -->

<!-- ----------------------------------------------------------------------- -->

<?php
require 'php/templates/footer.php';
?>

<script src="lib/AdminLTE/plugins/iCheck/icheck.min.js"></script>
<link rel="stylesheet" href="lib/AdminLTE/plugins/iCheck/all.css">
<link rel="stylesheet" href="lib/AdminLTE/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
<script src="lib/AdminLTE/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="lib/AdminLTE/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<script>
main();

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
function main () {
    initializeiCheck();
    initializeDatatable();
    getDevicesList();
    getICMPHostTotals();
}

// -----------------------------------------------------------------------------
function initializeDatatable () {
  var table=
  $('#tableDevices').DataTable({
    'paging'       : true,
    'lengthChange' : true,
    'lengthMenu'   : [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, '<?php echo $pia_lang['Device_Tablelenght_all']; ?>']],
    'searching'    : true,
    'ordering'     : true,
    'info'         : true,
    'autoWidth'    : false,
    'order'       : [[0,'asc']],

    'columnDefs'   : [
      {visible:   false,         targets: [6,7] },
      {className: 'text-center', targets: [1,2,3,4,5] },
      {className: 'text-left',   targets: [0] },
      {width:     '150px',       targets: [4] },
      {width:     '80px',        targets: [2,5] },
      {width:     '110px',       targets: [3] },

      {targets: [0],
        'createdCell': function (td, cellData, rowData, row, col) {
            $(td).html ('<b><a href="icmpmonitorDetails.php?hostip='+ rowData[1] +'" class="">'+ cellData +'</a></b>');
      } },
      {targets: [2],
        'createdCell': function (td, cellData, rowData, row, col) {
          if (cellData == 1){
            $(td).html ('<i class="fa fa-star text-yellow" style="font-size:16px"></i>');
          } else {
            $(td).html ('');
          }
      } },
      {targets: [3],
        'createdCell': function (td, cellData, rowData, row, col) {
          if (cellData == 99999){
            $(td).html ('TimeOut');
          } else {
            $(td).html (cellData + ' ms');
          }
      } },
      {targets: [5],
        'createdCell': function (td, cellData, rowData, row, col) {
          if (cellData == 1){
            $(td).html ('<a href="icmpmonitorDetails.php?hostip='+ rowData[1] +'" class="badge bg-green">Online</a>');
          } else if (cellData == 0 && rowData[6] == 1) {
            $(td).html ('<a href="icmpmonitorDetails.php?hostip='+ rowData[1] +'" class="badge bg-red">Down</a>');
          } else {
            $(td).html ('<a href="icmpmonitorDetails.php?hostip='+ rowData[1] +'" class="badge bg-gray text-white">Offline</a>');
          }
      } },

    ],

    // Processing
    'processing'  : true,
    'language'    : {
      processing: '<table> <td width="130px" align="middle">Loading...</td><td><i class="ion ion-ios-loop-strong fa-spin fa-2x fa-fw"></td> </table>',
      emptyTable: 'No data',
      "lengthMenu": "<?php echo $pia_lang['Device_Tablelenght']; ?>",
      "search":     "<?php echo $pia_lang['Device_Searchbox']; ?>: ",
      "paginate": {
          "next":       "<?php echo $pia_lang['Device_Table_nav_next']; ?>",
          "previous":   "<?php echo $pia_lang['Device_Table_nav_prev']; ?>"
      },
      "info":           "<?php echo $pia_lang['Device_Table_info']; ?>",
    }
  });
};

// -----------------------------------------------------------------------------
function getDevicesList () {
  // Define new datasource URL and reload
  $('#tableDevices').DataTable().ajax.url(
    'php/server/icmpmonitor.php?action=getDevicesList').load();
};

// -----------------------------------------------------------------------------
function getICMPHostTotals () {
  $.get('php/server/icmpmonitor.php?action=getICMPHostTotals', function(data) {
    var totalsDevices = JSON.parse(data);

    $('#devicesAll').html        (totalsDevices[0].toLocaleString());
    $('#devicesConnected').html  (totalsDevices[2].toLocaleString());
    $('#devicesFavorites').html  (totalsDevices[3].toLocaleString());
    $('#devicesDown').html       (totalsDevices[1].toLocaleString());
} );
};

// -----------------------------------------------------------------------------
function insertNewICMPHost(refreshCallback='') {
  // Check URL
  if ($('#icmp_ip').val() == '') {
    return;
  }

  // update data to server
  $.get('php/server/icmpmonitor.php?action=insertNewICMPHost'
    + '&icmp_ip='         + $('#icmphost_ip').val()
    + '&icmp_hostname='   + $('#icmphost_name').val()
    + '&icmp_fav='        + ($('#insFavorite')[0].checked * 1)
    + '&alertdown='       + ($('#insAlertEvents')[0].checked * 1)
    + '&alertevents='     + ($('#insAlertDown')[0].checked * 1)
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

</script>
