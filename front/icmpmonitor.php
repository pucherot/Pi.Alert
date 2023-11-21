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
require 'php/server/graph.php';
require 'php/server/journal.php';

$DBFILE = '../db/pialert.db';
OpenDB();

function print_box_top_element($title) {
	echo '<div class="row">
        <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">' . $title . '</h3>
            </div>
            <div class="box-body">
              <div>';
}

function print_box_bottom_element() {
	echo '          </div>
                </div>
                <!-- /.box-body -->
              </div>
            </div>
        </div>';
}

// Get Online Graph Arrays
$graph_arrays = array();
$graph_arrays = prepare_graph_arrays_history("icmpscan");
$Pia_Graph_Device_Time = $graph_arrays[0];
$Pia_Graph_Device_Down = $graph_arrays[1];
$Pia_Graph_Device_All = $graph_arrays[2];
$Pia_Graph_Device_Online = $graph_arrays[3];
?>

<!-- Page ------------------------------------------------------------------ -->

<div class="content-wrapper">

<?php
// ################### Start Bulk-Editor #######################################
if ($_REQUEST['mod'] == 'bulkedit') {
	require 'php/templates/notification.php';

	echo '<section class="content-header">
          <h1 id="pageTitle">' . $pia_lang['ICMPMonitor_Title'] . ' - ' . $pia_lang['Device_bulkEditor_mode'] . '</h1>
          <a href="./icmpmonitor.php" class="btn btn-success pull-right" role="button" style="position: absolute; display: inline-block; top: 5px; right: 15px;">' . $pia_lang['Device_bulkEditor_mode_quit'] . '</a>
        </section>';

	echo '<section class="content">
        <script src="lib/AdminLTE/bower_components/jquery/dist/jquery.min.js"></script>
        <link rel="stylesheet" href="lib/AdminLTE/plugins/iCheck/all.css">';

	if ($_REQUEST['savedata'] == 'yes') {

		$sql_queue = array();

		if ($_REQUEST['en_bulk_owner'] == 'on') {
			$set_bulk_owner = htmlspecialchars($_REQUEST['bulk_owner'], ENT_QUOTES);
			array_push($sql_queue, 'icmp_owner="' . $set_bulk_owner . '"');
		}
		if ($_REQUEST['en_bulk_type'] == 'on') {
			$set_bulk_type = htmlspecialchars($_REQUEST['bulk_type'], ENT_QUOTES);
			array_push($sql_queue, 'icmp_type="' . $set_bulk_type . '"');
		}
		if ($_REQUEST['en_bulk_group'] == 'on') {
			$set_bulk_group = htmlspecialchars($_REQUEST['bulk_group'], ENT_QUOTES);
			array_push($sql_queue, 'icmp_group="' . $set_bulk_group . '"');
		}
		if ($_REQUEST['en_bulk_location'] == 'on') {
			$set_bulk_location = htmlspecialchars($_REQUEST['bulk_location'], ENT_QUOTES);
			array_push($sql_queue, 'icmp_location="' . $set_bulk_location . '"');
		}
		if ($_REQUEST['en_bulk_comments'] == 'on') {
			$set_bulk_comments = htmlspecialchars($_REQUEST['bulk_comments'], ENT_QUOTES);
			array_push($sql_queue, 'icmp_Notes="' . $set_bulk_comments . '"');
		}
		if ($_REQUEST['en_bulk_AlertAllEvents'] == 'on') {
			if ($_REQUEST['bulk_AlertAllEvents'] == 'on') {$set_bulk_AlertAllEvents = 1;} else { $set_bulk_AlertAllEvents = 0;}
			array_push($sql_queue, 'icmp_AlertEvents="' . $set_bulk_AlertAllEvents . '"');
		}
		if ($_REQUEST['en_bulk_AlertDown'] == 'on') {
			if ($_REQUEST['bulk_AlertDown'] == 'on') {$set_bulk_AlertDown = 1;} else { $set_bulk_AlertDown = 0;}
			array_push($sql_queue, 'icmp_AlertDown="' . $set_bulk_AlertDown . '"');
		}

		print_box_top_element($pia_lang['Device_bulkEditor_savebox_title']);
		// Count changed fields
		if (sizeof($sql_queue) < 1) {
			// No fields were selected for modification
			echo '<br>' . $pia_lang['Device_bulkEditor_savebox_noselection'] . '<br>&nbsp;';
		} else {
			// Fields were selected for modification
			echo '<h4>' . $pia_lang['Device_bulkEditor_savebox_mod_devices'] . ':</h4>';
			// Prepare Update Segment start
			$sql_modified_hosts = array();
			$sql = 'SELECT icmp_hostname, icmp_ip FROM ICMP_Mon ORDER BY icmp_hostname COLLATE NOCASE ASC';
			$results = $db->query($sql);
			while ($row = $results->fetchArray()) {
				if (isset($_REQUEST[str_replace(".", "_", $row['icmp_ip'])])) {
					// List modified devices (name)
					$modified_hosts = $modified_hosts . $row['icmp_hostname'] . '; ';
					// Build sql query and update
					$sql_queue_str = implode(', ', $sql_queue);
					// Build Host list
					array_push($sql_modified_hosts, $row['icmp_ip']);
				}
			}
			// output modified hosts
			echo $modified_hosts;
			// List modifications
			echo '<h4>' . $pia_lang['Device_bulkEditor_savebox_mod_fields'] . ':</h4>';
			if (isset($set_bulk_owner)) {echo $pia_lang['DevDetail_MainInfo_Owner'] . ': ' . $set_bulk_owner . '<br>';}
			if (isset($set_bulk_type)) {echo $pia_lang['DevDetail_MainInfo_Type'] . ': ' . $set_bulk_type . '<br>';}
			if (isset($set_bulk_group)) {echo $pia_lang['DevDetail_MainInfo_Group'] . ': ' . $set_bulk_group . '<br>';}
			if (isset($set_bulk_location)) {echo $pia_lang['DevDetail_MainInfo_Location'] . ': ' . $set_bulk_location . '<br>';}
			if (isset($set_bulk_comments)) {echo $pia_lang['DevDetail_MainInfo_Comments'] . ': ' . $set_bulk_comments . '<br>';}
			if (isset($set_bulk_AlertAllEvents)) {echo $pia_lang['DevDetail_EveandAl_AlertAllEvents'] . ': ' . $set_bulk_AlertAllEvents . '<br>';}
			if (isset($set_bulk_AlertDown)) {echo $pia_lang['DevDetail_EveandAl_AlertDown'] . ': ' . $set_bulk_AlertDown . '<br>';}
			// Prepare Update Segment stop

			// Update Segment
			foreach ($sql_modified_hosts as $value) {
				$sql_update = 'UPDATE ICMP_Mon SET ' . $sql_queue_str . ' WHERE icmp_ip="' . $value . '"';
				$results_update = $db->query($sql_update);
			}

			// Logging
			pialert_logging('a_021', $_SERVER['REMOTE_ADDR'], 'LogStr_0002', '', $modified_hosts);
		}

		echo '<a href="./icmpmonitor.php?mod=bulkedit" class="btn btn-default pull-right" role="button" style="margin-bottom: 10px;">' . $pia_lang['Gen_Close'] . '</a>';
		print_box_bottom_element();
	}

	echo '<form method="post" action="./icmpmonitor.php">
          <input type="hidden" id="mod" name="mod" value="bulkedit">
          <input type="hidden" id="savedata" name="savedata" value="yes">';
	print_box_top_element($pia_lang['Device_bulkEditor_hostbox_title']);

	$sql = 'SELECT icmp_hostname, icmp_ip, icmp_PresentLastScan, icmp_AlertEvents, icmp_AlertDown FROM ICMP_Mon ORDER BY icmp_hostname COLLATE NOCASE ASC';
	$results = $db->query($sql);
	while ($row = $results->fetchArray()) {
		if ($row[2] == 1) {$status_border = 'border: 1px solid #00A000; box-shadow: inset 0px 0px 5px 0px #00A000;';} else { $status_border = 'border: 1px solid #888;';}
		if ($row[3] == 1 && $row[4] == 1) {$status_text_color = 'bulked_checkbox_label_alldown';} elseif ($row[3] == 1) {$status_text_color = 'bulked_checkbox_label_all';} elseif ($row[4] == 1) {$status_text_color = 'bulked_checkbox_label_down';} else { $status_text_color = '';}
		//if ($row[4] == 1) {$status_box = 'background-color: #b1720c;';} else { $status_box = 'background-color: transparent;';}
		echo '<div class="table_settings_col_box" style="padding-left: 0px; padding-top: 0px; ' . $status_border . '">
             <div style="display: inline-block; ' . $status_box . ' height: 32px; width: 36px; margin-right: 3px; padding-left: 8px; padding-top: 6px;">
                <input class="icheckbox_flat-blue hostselection" id="' . str_replace(".", "_", $row[1]) . '" name="' . str_replace(".", "_", $row[1]) . '" type="checkbox" style="position: relative; margin-top:-3px; margin-right: 3px;">
             </div>
             <label class="control-label ' . $status_text_color . '" for="' . str_replace(".", "_", $row[1]) . '" style="">' . $row[0] . '</label>
          </div>';
	}

	// Check/Uncheck All Button
	echo '<button type="button" class="btn btn-warning pull-right checkall" style="display: block; margin-top: 20px; margin-bottom: 10px; min-width: 180px;">' . $pia_lang['Device_bulkEditor_selectall'] . '</button>';
	echo '<script>
            var clicked = false;
            $(".checkall").on("click", function() {
              $(".hostselection").prop("checked", !clicked);
              clicked = !clicked;
              this.innerHTML = clicked ? \'' . $pia_lang['Device_bulkEditor_selectnone'] . '\' : \'' . $pia_lang['Device_bulkEditor_selectall'] . '\';
            });
        </script>';
	print_box_bottom_element();

	print_box_top_element($pia_lang['Device_bulkEditor_inputbox_title']);
	// Inputs
	echo '<table style="margin-bottom:30px; width: 100%">
          <tr>
            <td style="padding-left: 10px; height: 70px; width: 80px;"><input class="icheckbox_flat-blue" id="en_bulk_owner" name="en_bulk_owner" type="checkbox"></td>
            <td style="">
                <label for="bulk_owner">' . $pia_lang['DevDetail_MainInfo_Owner'] . ':</label><br>
                <input type="text" class="form-control" id="bulk_owner" name="bulk_owner" style="max-width: 400px;" disabled></td>
          </tr>
          <tr>
            <td style="padding-left: 10px; height: 70px;"><input class="icheckbox_flat-blue" id="en_bulk_type" name="en_bulk_type" type="checkbox"></td>
            <td style="">
                <label for="bulk_type">' . $pia_lang['DevDetail_MainInfo_Type'] . ':</label><br>
                <div class="input-group" style="max-width: 400px;">
                  <input class="form-control" id="bulk_type" name="bulk_type" type="text" disabled>
                  <div class="input-group-btn">
                    <button type="button" id="bulk_type_selector" name="bulk_type_selector" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" disabled>
                      <span class="fa fa-caret-down"></span></button>
                    <ul id="dropdownDeviceType" class="dropdown-menu dropdown-menu-right">
                      <li><a href="javascript:void(0)" onclick="setTextValue(\'bulk_type\',\'Smartphone\')">   Smartphone   </a></li>
                      <li><a href="javascript:void(0)" onclick="setTextValue(\'bulk_type\',\'Laptop\')">       Laptop       </a></li>
                      <li><a href="javascript:void(0)" onclick="setTextValue(\'bulk_type\',\'PC\')">           PC           </a></li>
                      <li><a href="javascript:void(0)" onclick="setTextValue(\'bulk_type\',\'Tablet\')">       Tablet       </a></li>
                      <li class="divider"></li>
                      <li><a href="javascript:void(0)" onclick="setTextValue(\'bulk_type\',\'Router\')">       Router       </a></li>
                      <li><a href="javascript:void(0)" onclick="setTextValue(\'bulk_type\',\'Switch\')">       Switch       </a></li>
                      <li><a href="javascript:void(0)" onclick="setTextValue(\'bulk_type\',\'Access Point\')"> Access Point </a></li>
                      <li class="divider"></li>
                      <li><a href="javascript:void(0)" onclick="setTextValue(\'bulk_type\',\'Others\')">       Others       </a></li>
                    </ul>
                  </div>
                </div>
            </td>
          </tr>
          <tr>
            <td style="padding-left: 10px; height: 70px;"><input class="icheckbox_flat-blue" id="en_bulk_group" name="en_bulk_group" type="checkbox"></td>
            <td style="">
                <label for="bulk_group">' . $pia_lang['DevDetail_MainInfo_Group'] . ':</label><br>
                <div class="input-group" style="max-width: 400px;">
                  <input class="form-control" id="bulk_group" name="bulk_group" type="text" disabled>
                  <div class="input-group-btn">
                    <button type="button" id="bulk_group_selector" name="bulk_group_selector" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" disabled>
                      <span class="fa fa-caret-down"></span></button>
                    <ul id="dropdownGroup" class="dropdown-menu dropdown-menu-right">
                      <li><a href="javascript:void(0)" onclick="setTextValue(\'bulk_group\',\'Always On\')"> Always On </a></li>
                      <li><a href="javascript:void(0)" onclick="setTextValue(\'bulk_group\',\'Friends\')">   Friends   </a></li>
                      <li><a href="javascript:void(0)" onclick="setTextValue(\'bulk_group\',\'Personal\')">  Personal  </a></li>
                      <li class="divider"></li>
                      <li><a href="javascript:void(0)" onclick="setTextValue(\'bulk_group\',\'Others\')">    Others    </a></li>
                    </ul>
                  </div>
                </div>
            </td>
          </tr>
          <tr>
            <td style="padding-left: 10px; height: 70px;"><input class="icheckbox_flat-blue" id="en_bulk_location" name="en_bulk_location" type="checkbox"></td>
            <td style="">
                <label for="bulk_location">' . $pia_lang['DevDetail_MainInfo_Location'] . ':</label><br>
                <div class="input-group" style="max-width: 400px;">
                  <input class="form-control" id="bulk_location" name="bulk_location" type="text" disabled>
                  <div class="input-group-btn">
                    <button type="button" id="bulk_location_selector" name="bulk_location_selector" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" disabled>
                      <span class="fa fa-caret-down"></span></button>
                    <ul id="dropdownLocation" class="dropdown-menu dropdown-menu-right">
                      <li><a href="javascript:void(0)" onclick="setTextValue(\'bulk_location\',\'Bathroom\')">    Bathroom</a></li>
                      <li><a href="javascript:void(0)" onclick="setTextValue(\'bulk_location\',\'Bedroom\')">     Bedroom</a></li>
                      <li><a href="javascript:void(0)" onclick="setTextValue(\'bulk_location\',\'Hall\')">        Hall</a></li>
                      <li><a href="javascript:void(0)" onclick="setTextValue(\'bulk_location\',\'Kitchen\')">     Kitchen</a></li>
                      <li><a href="javascript:void(0)" onclick="setTextValue(\'bulk_location\',\'Living room\')"> Living room</a></li>
                      <li class="divider"></li>
                      <li><a href="javascript:void(0)" onclick="setTextValue(\'bulk_location\',\'Others\')">      Others</a></li>
                    </ul>
                  </div>
                </div>
            </td>
          </tr>
          <tr>
            <td style="padding-left: 10px; height: 70px;"><input class="icheckbox_flat-blue" id="en_bulk_comments" name="en_bulk_comments" type="checkbox"></td>
            <td style="">
                <label for="bulk_comments">' . $pia_lang['DevDetail_MainInfo_Comments'] . ':</label><br>
                <textarea class="form-control" rows="3" id="bulk_comments" name="bulk_comments" spellcheck="false" data-gramm="false" style="max-width: 400px;" disabled></textarea></td>
          </tr>
          <tr>
            <td style="padding-left: 10px; height: 70px;"><input class="icheckbox_flat-blue" id="en_bulk_AlertAllEvents" name="en_bulk_AlertAllEvents" type="checkbox"></td>
            <td style="">
                <label for="bulk_AlertAllEvents" style="width: 200px;">' . $pia_lang['DevDetail_EveandAl_AlertAllEvents'] . ':</label>
                <input class="icheckbox_flat-blue" id="bulk_AlertAllEvents" name="bulk_AlertAllEvents" type="checkbox" disabled></td>
          </tr>
          <tr>
            <td style="padding-left: 10px; height: 70px;"><input class="icheckbox_flat-blue" id="en_bulk_AlertDown" name="en_bulk_AlertDown" type="checkbox"></td>
            <td style="">
                <label for="bulk_AlertDown" style="width: 200px;">' . $pia_lang['DevDetail_EveandAl_AlertDown'] . ':</label>
                <input class="icheckbox_flat-blue" id="bulk_AlertDown" name="bulk_AlertDown" type="checkbox" disabled></td>
          </tr>

        </table>
        <button type="button" class="btn btn-danger" id="btnBulkDeletion" onclick="askBulkDeletion()" style="min-width: 180px;">' . $pia_lang['Device_bulkDel_button'] . '</button>
        <input class="btn btn-warning pull-right" type="submit" value="' . $pia_lang['Gen_Save'] . '" style="margin-bottom: 10px; min-width: 180px;">';

	// JS to enable/disable inputs. Inputs are delete, when disabled
	echo '<script>
            var bulk_owner = true;
            $("#en_bulk_owner").on("click", function() {
              $("#bulk_owner").val(\'\');
              $("#bulk_owner").prop("disabled", !bulk_owner);
              bulk_owner = !bulk_owner;
            });
            var bulk_type = true;
            $("#en_bulk_type").on("click", function() {
              $("#bulk_type").val(\'\');
              $("#bulk_type").prop("disabled", !bulk_type);
              $("#bulk_type_selector").prop("disabled", !bulk_type);
              bulk_type = !bulk_type;
            });
            var bulk_group = true;
            $("#en_bulk_group").on("click", function() {
              $("#bulk_group").val(\'\');
              $("#bulk_group").prop("disabled", !bulk_group);
              $("#bulk_group_selector").prop("disabled", !bulk_group);
              bulk_group = !bulk_group;
            });
            var bulk_location = true;
            $("#en_bulk_location").on("click", function() {
              $("#bulk_location").val(\'\');
              $("#bulk_location").prop("disabled", !bulk_location);
              $("#bulk_location_selector").prop("disabled", !bulk_location);
              bulk_location = !bulk_location;
            });
            var bulk_comments = true;
            $("#en_bulk_comments").on("click", function() {
              $("#bulk_comments").val(\'\');
              $("#bulk_comments").prop("disabled", !bulk_comments);
              bulk_comments = !bulk_comments;
            });
            var bulk_AlertAllEvents = true;
            $("#en_bulk_AlertAllEvents").on("click", function() {
              $("#bulk_AlertAllEvents").prop("checked", false);
              $("#bulk_AlertAllEvents").prop("disabled", !bulk_AlertAllEvents);
              bulk_AlertAllEvents = !bulk_AlertAllEvents;
            });
            var bulk_AlertDown = true;
            $("#en_bulk_AlertDown").on("click", function() {
              $("#bulk_AlertDown").prop("checked", false);
              $("#bulk_AlertDown").prop("disabled", !bulk_AlertDown);
              bulk_AlertDown = !bulk_AlertDown;
            });

            function setTextValue (textElement, textValue) {
              $("#"+textElement).val (textValue);
            }

            function askBulkDeletion() {
              // Ask
              showModalWarning(\'' . $pia_lang['Device_bulkDel_info_head'] . '\', \'' . $pia_lang['Device_bulkDel_info_text'] . '\',
                \'' . $pia_lang['Gen_Cancel'] . '\', \'' . $pia_lang['Gen_Delete'] . '\', \'BulkDeletion\');
            }
            function BulkDeletion()
            {
              const checkboxes = document.querySelectorAll(\'.icheckbox_flat-blue.hostselection:checked\');
              const checkedIds = Array.from(checkboxes).map((checkbox) => checkbox.id);
              const queryParams = new URLSearchParams();
              checkedIds.forEach((id) => queryParams.append(\'hosts[]\', id));
              // Execute
              $.get(\'php/server/icmpmonitor.php?action=BulkDeletion&\' + queryParams.toString(), function(msg) {
                showMessage (msg);
              });
            }

        </script>';

	print_box_bottom_element();

	echo '</form>';

	echo '</section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->';

	require 'php/templates/footer.php';
// ################### End Bulk-Editor #########################################
} else {
// ################### Start ICMP List #######################################
	?>

<!-- Content header--------------------------------------------------------- -->
    <section class="content-header">
    <?php require 'php/templates/notification.php';?>
      <h1 id="pageTitle">
         <?=$pia_lang['ICMPMonitor_Title'];?>
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
                            <h4 class="modal-title"><?=$pia_lang['ICMPMonitor_headline_IP'];?></h4>
                        </div>
                        <div class="modal-body">
                            <div style="height: 230px;">
                            <div class="form-group col-xs-12">
                              <label class="col-xs-3 control-label"><?=$pia_lang['ICMPMonitor_label_IP'];?></label>
                              <div class="col-xs-9">
                                <input type="text" class="form-control" id="icmphost_ip" placeholder="Host IP">
                              </div>
                            </div>
                            <div class="form-group col-xs-12">
                              <label class="col-xs-3 control-label"><?=$pia_lang['ICMPMonitor_label_Hostname'];?></label>
                              <div class="col-xs-9">
                                <input type="text" class="form-control" id="icmphost_name" placeholder="Hostname">
                              </div>
                            </div>
                            <div class="form-group col-xs-12">
                                <label class="col-xs-3 control-label"><?=$pia_lang['Device_TableHead_Favorite'];?></label>
                                <div class="col-xs-9" style="margin-top: 0px;">
                                  <input class="checkbox orange" id="insFavorite" type="checkbox">
                                </div>
                            </div>
                            <div class="form-group col-xs-12">
                                <label class="col-xs-3 control-label"><?=$pia_lang['WebServices_label_AlertEvents'];?></label>
                                <div class="col-xs-9" style="margin-top: 0px;">
                                  <input class="checkbox blue" id="insAlertEvents" type="checkbox">
                                </div>
                            </div>
                            <div class="form-group col-xs-12">
                                <label class="col-xs-3 control-label"><?=$pia_lang['WebServices_label_AlertDown'];?></label>
                                <div class="col-xs-9" style="margin-top: 0px;">
                                  <input class="checkbox red" id="insAlertDown" type="checkbox">
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?=$pia_lang['Gen_Close'];?></button>
                            <button type="button" class="btn btn-primary" id="btnInsert" onclick="insertNewICMPHost()" ><?=$pia_lang['Gen_Save'];?></button>
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
                <p class="infobox_label"><?=$pia_lang['Device_Shortcut_AllDevices'];?></p>
            </div>
            <div class="icon"><i class="fa fa-laptop text-aqua-40"></i></div>
          </div>
        </div>

        <div class="col-lg-3 col-sm-3 col-xs-6">
          <div class="small-box bg-green">
            <div class="inner"><h3 id="devicesConnected"> -- </h3>
                <p class="infobox_label"><?=$pia_lang['Device_Shortcut_Connected'];?></p>
            </div>
            <div class="icon"><i class="fa fa-plug text-green-40"></i></div>
          </div>
        </div>

        <div class="col-lg-3 col-sm-3 col-xs-6">
          <div class="small-box bg-yellow">
            <div class="inner"><h3 id="devicesFavorites"> -- </h3>
                <p class="infobox_label"><?=$pia_lang['Device_Shortcut_Favorites'];?></p>
            </div>
            <div class="icon"><i class="fa fa-star text-yellow-40"></i></div>
          </div>
        </div>

        <div class="col-lg-3 col-sm-3 col-xs-6">
          <div class="small-box bg-red">
            <div class="inner"><h3 id="devicesDown"> -- </h3>
                <p class="infobox_label"><?=$pia_lang['Device_Shortcut_DownAlerts'];?></p>
            </div>
            <div class="icon"><i class="fa fa-warning text-red-40"></i></div>
          </div>
        </div>

      </div>


<!-- Activity Chart ------------------------------------------------------- -->

<?php
If ($ENABLED_HISTOY_GRAPH !== False) {
		?>
      <div class="row">
          <div class="col-md-12">
          <div class="box" id="clients">
              <div class="box-header with-border">
                <h3 class="box-title"><?=$pia_lang['Device_Shortcut_OnlineChart_a'];?><span class="maxlogage-interval">12</span> <?=$pia_lang['Device_Shortcut_OnlineChart_b'];?></h3>
              </div>
              <div class="box-body">
                <div class="chart">
                  <script src="lib/AdminLTE/bower_components/chart.js/Chart.js"></script>
                  <canvas id="OnlineChart" style="width:100%; height: 150px;  margin-bottom: 15px;"></canvas>
                </div>
              </div>
            </div>
          </div>
      </div>

      <script src="js/graph_online_history.js"></script>
      <script>
        var pia_js_online_history_time = [<?php pia_graph_devices_data($Pia_Graph_Device_Time);?>];
        var pia_js_online_history_ondev = [<?php pia_graph_devices_data($Pia_Graph_Device_Online);?>];
        var pia_js_online_history_dodev = [<?php pia_graph_devices_data($Pia_Graph_Device_Down);?>];
        graph_online_history_icmp(pia_js_online_history_time, pia_js_online_history_ondev, pia_js_online_history_dodev);
      </script>
<?php
}
	?>

      <div class="row">
        <div class="col-xs-12">
          <div id="tableDevicesBox" class="box">

            <!-- box-header -->
            <div class="box-header">
              <h3 id="tableDevicesTitle" class="box-title text-aqua"><?=$pia_lang['Device_Title'];?></h3>
              <a href="./icmpmonitor.php?mod=bulkedit" class="btn btn-xs btn-default" role="button" style="display: inline-block; margin-top: -5px; margin-left: 15px;"><i class="fa fa-pencil" style="font-size:1.5rem"></i></a>
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
    'lengthMenu'   : [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, '<?=$pia_lang['Device_Tablelenght_all'];?>']],
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
            $(td).css('min-width', '160px');
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
      "lengthMenu": "<?=$pia_lang['Device_Tablelenght'];?>",
      "search":     "<?=$pia_lang['Device_Searchbox'];?>: ",
      "paginate": {
          "next":       "<?=$pia_lang['Device_Table_nav_next'];?>",
          "previous":   "<?=$pia_lang['Device_Table_nav_prev'];?>"
      },
      "info":           "<?=$pia_lang['Device_Table_info'];?>",
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

<?php
}
// ################### End ICMP List #########################################
?>
