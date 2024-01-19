<!-- ---------------------------------------------------------------------------
#  Pi.Alert
#  Open Source Network Guard / WIFI & LAN intrusion detector
#
#  help_faq.php - Front module. Help and FAQ
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
require 'php/templates/language/help_' . $pia_lang_selected . '.php';
# Code Snippets
# -----------------------------------------------------------
$help_faq_toptemp = '
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">';

$trip_div_close = '
            </div>
          </div>
        </div>';

?>
<!-- Page ------------------------------------------------------------------ -->
<div class="content-wrapper">

<!-- Content header--------------------------------------------------------- -->
    <section class="content-header">
    <?php require 'php/templates/notification.php';?>
      <h1 id="pageTitle">
         <?=$help_lang['Title'];?>
      </h1>
    </section>

    <!-- Main content ---------------------------------------------------------- -->
    <section class="content">
      <h4><?=$help_lang['Cat_General'];?></h4>
        <div class="panel-group" id="accordion_gen">

        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_gen" href="#collapse100">
              <?=$help_lang['Cat_General_100_head'];?></a>
            </h4>
          </div>
          <div id="collapse100" class="panel-collapse collapse help_question">
            <div class="panel-body"><?=$help_lang['Cat_General_100_text_a'];?>
              <span class="text-danger help_faq_code"><?=date_default_timezone_get();?></span><br>
              <?=$help_lang['Cat_General_100_text_b'];?>
              <span class="text-danger help_faq_code"><?=php_ini_loaded_file();?></span><br>
              <?=$help_lang['Cat_General_100_text_c'];?></div>
          </div>
        </div>

        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_gen" href="#collapse101">
              <?=$help_lang['Cat_General_101_head'];?></a>
            </h4>
          </div>
          <div id="collapse101" class="panel-collapse collapse help_question">
            <div class="panel-body">
              <?=$help_lang['Cat_General_101_text'];?>
        <?=$trip_div_close;?>

        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_gen" href="#collapse102">
              <?=$help_lang['Cat_General_102_head'];?></a>
            </h4>
          </div>
          <div id="collapse102" class="panel-collapse collapse help_question">
            <div class="panel-body">
              <?=$help_lang['Cat_General_102_text'];?>
        <?=$trip_div_close;?>

        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_gen" href="#collapse103">
              <?=$help_lang['Cat_General_103_head'];?></a>
            </h4>
          </div>
          <div id="collapse103" class="panel-collapse collapse help_question">
            <div class="panel-body">
              <?=$help_lang['Cat_General_103_text'];?>
        <?=$trip_div_close;?>

        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_gen" href="#collapse104">
              <?=$help_lang['Cat_General_104_head'];?></a>
            </h4>
          </div>
          <div id="collapse104" class="panel-collapse collapse help_question">
            <div class="panel-body">
              <?=$help_lang['Cat_General_104_text'];?>
        <?=$trip_div_close;?>

        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_gen" href="#collapse105">
              <?=$help_lang['Cat_General_105_head'];?></a>
            </h4>
          </div>
          <div id="collapse105" class="panel-collapse collapse help_question">
            <div class="panel-body">
              <?=$help_lang['Cat_General_105_text'];?>
        <?=$trip_div_close;?>

        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_gen" href="#collapse107">
              <?=$help_lang['Cat_General_107_head'];?></a>
            </h4>
          </div>
          <div id="collapse107" class="panel-collapse collapse help_question">
            <div class="panel-body">
              <?=$help_lang['Cat_General_107_text'];?>
        <?=$trip_div_close;?>

        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_gen" href="#collapse106">
              <?=$help_lang['Cat_General_106_head'];?></a>
            </h4>
          </div>
          <div id="collapse106" class="panel-collapse collapse help_question">
            <div class="panel-body">
              <?=$help_lang['Cat_General_106_text'];?>
        <?=$trip_div_close;?>

        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_gen" href="#collapse108">
              <?=$help_lang['Cat_General_108_head'];?></a>
            </h4>
          </div>
          <div id="collapse108" class="panel-collapse collapse help_question">
            <div class="panel-body">
              <?=$help_lang['Cat_General_108_text'];?>
        <?=$trip_div_close;?>

        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_gen" href="#collapse109">
              <?=$help_lang['Cat_General_109_head'];?></a>
            </h4>
          </div>
          <div id="collapse109" class="panel-collapse collapse help_question">
            <div class="panel-body">
              <?=$help_lang['Cat_General_109_text'];?>
        <?=$trip_div_close;?>

      </div>

<!-- Devices -->

      <h4><?=$pia_lang['Navigation_Devices'];?></h4>
        <div class="panel-group" id="accordion_dev">
        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_dev" href="#collapse200">
              <?=$help_lang['Cat_Device_200_head'];?></a>
            </h4>
          </div>
          <div id="collapse200" class="panel-collapse collapse help_question">
            <div class="panel-body">
               <?=$help_lang['Cat_Device_200_text'];?>
        <?=$trip_div_close;?>

        </div>

<!-- Devices Details -->

      <h4><?=$pia_lang['Navigation_Devices'];?> - <?=$help_lang['Cat_Detail'];?></h4>
        <div class="panel-group" id="accordion_det">
        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_det" href="#collapse300">
              <?=$help_lang['Cat_Detail_300_head'];?> "<?=$pia_lang['DevDetail_MainInfo_Network'];?>" / "<?=$pia_lang['DevDetail_MainInfo_Network_Port'];?>"?</a>
            </h4>
          </div>
          <div id="collapse300" class="panel-collapse collapse help_question">
            <div class="panel-body">
              "<?=$pia_lang['DevDetail_MainInfo_Network'];?>" <?=$help_lang['Cat_Detail_300_text_a'];?><br>
              "<?=$pia_lang['DevDetail_MainInfo_Network_Port'];?>" <?=$help_lang['Cat_Detail_300_text_b'];?>
        <?=$trip_div_close;?>

        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_det" href="#collapse301">
              <?=$help_lang['Cat_Detail_301_head_a'];?> "<?=$pia_lang['DevDetail_EveandAl_ScanCycle'];?>" <?=$help_lang['Cat_Detail_301_head_b'];?></a>
            </h4>
          </div>
          <div id="collapse301" class="panel-collapse collapse help_question">
            <div class="panel-body">
              <?=$help_lang['Cat_Detail_301_text'];?>
        <?=$trip_div_close;?>

        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_det" href="#collapse302">
              <?=$help_lang['Cat_Detail_302_head_a'];?> "<?=$pia_lang['DevDetail_EveandAl_RandomMAC'];?>" <?=$help_lang['Cat_Detail_302_head_b'];?></a>
            </h4>
          </div>
          <div id="collapse302" class="panel-collapse collapse help_question">
            <div class="panel-body">
              <?=$help_lang['Cat_Detail_302_text'];?>
        <?=$trip_div_close;?>

        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_det" href="#collapse303">
              <?=$help_lang['Cat_Detail_303_head'];?></a>
            </h4>
          </div>
          <div id="collapse303" class="panel-collapse collapse help_question">
            <div class="panel-body">
              <?=$help_lang['Cat_Detail_303_text'];?>
        <?=$trip_div_close;?>

        </div>

<!-- Network -->

      <h4><?=$pia_lang['Navigation_Network'];?></h4>
        <div class="panel-group" id="accordion_net">
        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_net" href="#collapse600">
              <?=$help_lang['Cat_Network_600_head'];?></a>
            </h4>
          </div>
          <div id="collapse600" class="panel-collapse collapse help_question">
            <div class="panel-body">
              <?=$help_lang['Cat_Network_600_text'];?>
        <?=$trip_div_close;?>

        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_net" href="#collapse601">
              <?=$help_lang['Cat_Network_601_head'];?></a>
            </h4>
          </div>
          <div id="collapse601" class="panel-collapse collapse help_question">
            <div class="panel-body">
              <?=$help_lang['Cat_Network_601_text'];?>
        <?=$trip_div_close;?>

        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_net" href="#collapse602">
              <?=$help_lang['Cat_Network_602_head'];?></a>
            </h4>
          </div>
          <div id="collapse602" class="panel-collapse collapse help_question">
            <div class="panel-body">
              <?=$help_lang['Cat_Network_602_text'];?>
        <?=$trip_div_close;?>

        </div>

<!-- Web Services -->

      <h4><?=$pia_lang['Navigation_Services'];?></h4>
        <div class="panel-group" id="accordion_pre">
        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_pre" href="#collapse703">
              <?=$help_lang['Cat_Service_703_head'];?></a>
            </h4>
          </div>
          <div id="collapse703" class="panel-collapse collapse help_question">
            <div class="panel-body">
              <?=$help_lang['Cat_Service_703_text'];?>
        <?=$trip_div_close;?>

        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_pre" href="#collapse700">
              <?=$help_lang['Cat_Service_700_head'];?></a>
            </h4>
          </div>
          <div id="collapse700" class="panel-collapse collapse help_question">
            <div class="panel-body">
              <?=$help_lang['Cat_Service_700_text'];?>
        <?=$trip_div_close;?>

        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_pre" href="#collapse701">
              <?=$help_lang['Cat_Service_701_head'];?></a>
            </h4>
          </div>
          <div id="collapse701" class="panel-collapse collapse help_question">
            <div class="panel-body">
              <table class="table table-bordered table-hover table-striped"><tr><th class="col-xs-1" style="text-align: center">Code</th><th class="col-xs-11">Description</th></tr>
        <?php

$jsonfile = file_get_contents("./lib/http-status-code/index.json");
$code_array = json_decode($jsonfile, true);
asort($code_array);
foreach ($code_array as $key => $jsons) {
	echo '<tr><td class="text-danger httpstatus_table_code">' . $jsons['code'] . '</td><td class="httpstatus_table_text">' . $jsons['description'] . '</td></tr>';
}

;?>
            </table>
        <?=$trip_div_close;?>

        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_pre" href="#collapse702">
              <?=$help_lang['Cat_Service_702_head'];?></a>
            </h4>
          </div>
          <div id="collapse702" class="panel-collapse collapse help_question">
            <div class="panel-body">
              <?=$help_lang['Cat_Service_702_text'];?>
        <?=$trip_div_close;?>

        </div>

<!-- Web Services Details -->

      <h4><?=$pia_lang['Navigation_Services'];?> - <?=$help_lang['Cat_Detail'];?></h4>
        <div class="panel-group" id="accordion_pre">
        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_pre" href="#collapse750">
              <?=$help_lang['Cat_ServiceDetails_750_head'];?></a>
            </h4>
          </div>
          <div id="collapse750" class="panel-collapse collapse help_question">
            <div class="panel-body">
              <?=$help_lang['Cat_ServiceDetails_750_text'];?>
        <?=$trip_div_close;?>

        </div>

<!-- Presence -->

      <h4><?=$pia_lang['Navigation_Presence'];?></h4>
        <div class="panel-group" id="accordion_pre">
        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_pre" href="#collapse400">
              <?=$help_lang['Cat_Presence_400_head'];?></a>
            </h4>
          </div>
          <div id="collapse400" class="panel-collapse collapse help_question">
            <div class="panel-body">
              <?=$help_lang['Cat_Presence_400_text'];?>
        <?=$trip_div_close;?>

        <?=$help_faq_toptemp;?>
              <a data-toggle="collapse" data-parent="#accordion_pre" href="#collapse401">
              <?=$help_lang['Cat_Presence_401_head'];?></a>
            </h4>
          </div>
          <div id="collapse401" class="panel-collapse collapse help_question">
            <div class="panel-body">
              <?=$help_lang['Cat_Presence_401_text'];?>
        <?=$trip_div_close;?>

        </div>

  <div style="width: 100%; height: 20px;"></div>
    </section>
    <!-- /.content -->
</div>
  <!-- /.content-wrapper -->

<!-- ----------------------------------------------------------------------- -->
<?php
require 'php/templates/footer.php';
?>
