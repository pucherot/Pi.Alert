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

// Turn off php errors
error_reporting(0);

if ($_SESSION["login"] != 1) {
	header('Location: ./index.php');
	exit;
}

require 'php/templates/header.php';
require 'php/server/db.php';
require 'php/server/journal.php';

// ===============================================================================
// Start prepare data
// ===============================================================================

$DBFILE = '../db/pialert.db';
OpenDB();
?>

<!-- Page ------------------------------------------------------------------ -->

<link rel="stylesheet" href="lib/AdminLTE/plugins/iCheck/all.css">

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
                                <input type="text" class="form-control" id="serviceURL" placeholder="Host IP">
                              </div>
                            </div>
                            <div class="form-group col-xs-12">
                              <label class="col-xs-3 control-label"><?php echo $pia_lang['ICMPMonitor_label_Tags']; ?></label>
                              <div class="col-xs-9">
                                <input type="text" class="form-control" id="serviceTag" placeholder="Tag">
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
                            <button type="button" class="btn btn-primary" id="btnInsert" onclick="insertNewService()" ><?php echo $pia_lang['Gen_Save']; ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </section>

    <!-- Main content ---------------------------------------------------------- -->
    <section class="content">



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

<script src="lib/AdminLTE/plugins/iCheck/icheck.min.js"></script>
<link rel="stylesheet" href="lib/AdminLTE/plugins/iCheck/all.css">

<script>
initializeiCheck();

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

</script>
