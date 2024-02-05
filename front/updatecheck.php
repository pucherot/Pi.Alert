<!-- ---------------------------------------------------------------------------
#  Pi.Alert
#  Open Source Network Guard / WIFI & LAN intrusion detector
#
#  updatecheck.php - Front module. network relationship
#-------------------------------------------------------------------------------
#  leiweibau 2023                                          GNU GPLv3
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

<div class="content-wrapper">

    <section class="content-header">
    <?php require 'php/templates/notification.php';?>
      <h1 id="pageTitle">
         <?=$pia_lang['Updatecheck_Title'];?>
      </h1>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-body" id="updatecheck">
                <button type="button" id="rewwejwejpjo" class="btn btn-primary" onclick="check_github_for_updates()"><?=$pia_lang['Maintenance_Tools_Updatecheck'];?></button>
          	</div>
        </div>

        <div id="updatecheck_result"></div>
    	<div style="width: 100%; height: 20px;"></div>

<?php
if (file_exists("auto_Update.info")) {
    $content = file_get_contents("auto_Update.info");
    $content_array = explode("\n", $content);
    $content_array = array_filter($content_array);
    echo '<div class="box" id="auto_update_releasenotes">
        <div class="box-body">
            <h4 class="text-aqua" style="text-align: center;">' . $pia_lang['Auto_Updatecheck_RN'] . '</h4><div>';
    // Transform release notes
        foreach ($content_array as $row) {
            $row = str_replace("BREAKING CHANGES", "<span class=\"text-red\">BREAKING CHANGES</span>", $row);
            if (stristr($row, "Update Notes: ")) {
                echo '<span style="font-size: 16px; font-weight: bold; text-decoration: underline;">' . $row . '</span><br>';
            } elseif (stristr($row, "New:")) {
                echo '<br><span style="font-size: 16px; font-weight: bold;">' . $row . '</span><br>';
            } elseif (stristr($row, "Fixed:")) {
                echo '<br><span style="font-size: 16px; font-weight: bold;">' . $row . '</span><br>';
            } elseif (stristr($row, "Updated:")) {
                echo '<br><span style="font-size: 16px; font-weight: bold;">' . $row . '</span><br>';
            } elseif (stristr($row, "Changed:")) {
                echo '<br><span style="font-size: 16px; font-weight: bold;">' . $row . '</span><br>';
            } elseif (stristr($row, "Note:")) {
                echo '<br><span style="font-size: 16px; font-weight: bold;">' . $row . '</span><br>';
            } elseif (stristr($row, "Removed:")) {
                echo '<br><span style="font-size: 16px; font-weight: bold;">' . $row . '</span><br>';
            } else {
                echo '<div style="display: list-item; margin-left : 2em;">' . str_replace('* ', '', $row) . '</div>';
            }
        }
        echo '<br><br>
                <lable for="bashupdatecommand" class="text-red"><i>Update command:</i></lable>
                <input id="bashupdatecommand" readonly value="bash -c &quot;$(wget -qLO - https://github.com/leiweibau/Pi.Alert/raw/main/install/pialert_update.sh)&quot;" style="width:100%; overflow-x: scroll; border: none; background: transparent; margin: 0px; padding: 0px;">
              <br><br>
            </div>
        <div class="box-footer">
            <a class="btn btn-default pull-left" href="https://leiweibau.net/archive/pialert/" target="_blank">Version History (leiweibau.net)</a>
        </div>
    </div>';
}


?>


    </section>

</div>

<script>
function check_github_for_updates() {
    $("#updatecheck_result").empty();
    $("#rewwejwejpjo").hide();
    $("#auto_update_releasenotes").hide();
    $.ajaxSetup({ cache: false });
    $.ajax({
        method: "POST",
        url: "./php/server/updatecheck_v2.php",
        data: "",
        beforeSend: function() { $('#updatecheck').addClass("ajax_scripts_loading"); },
        complete: function() { $('#updatecheck').removeClass("ajax_scripts_loading"); },
        success: function(data, textStatus) {
            $("#updatecheck_result").html(data);
            $("#rewwejwejpjo").show();
        }
    })
}

</script>

<?php
require 'php/templates/footer.php';
?>