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
    </section>

</div>

<script>
function check_github_for_updates() {
    $("#updatecheck_result").empty();
    $("#rewwejwejpjo").hide();
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