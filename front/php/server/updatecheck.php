<?php
foreach (glob("../../../db/setting_language*") as $filename) {
    $pia_lang_selected = str_replace('setting_language_','',basename($filename));
}
if (strlen($pia_lang_selected) == 0) {$pia_lang_selected = 'en_us';}
require '../templates/language/'.$pia_lang_selected.'.php';

?>
<h5 class="text-aqua" style="font-size: 16px;"><?php echo $pia_lang['Maintenance_Github_package_a'];?>
<?php

$curl_handle=curl_init();
curl_setopt($curl_handle, CURLOPT_URL,'https://api.github.com/repos/leiweibau/Pi.Alert/commits?path=tar%2Fpialert_latest.tar&page=1&per_page=1');
curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl_handle, CURLOPT_USERAGENT, 'PHP');
$query = curl_exec($curl_handle);
curl_close($curl_handle);

$data = json_decode($query, true);

$utc_ts = strtotime($data['0']['commit']['author']['date']);
$offset = date("Z");
$local_ts = $utc_ts + $offset;
$local_time = date("d.m.Y, H:i", $utc_ts);
echo $local_time;
?>       
    <?php echo $pia_lang['Maintenance_Github_package_b'];?>
<a href="#" data-toggle="modal" data-target="#modal-update">&nbsp;<i class="fa fa-fw fa-info-circle"></i></a>
</h5>


<div class="modal fade" id="modal-update">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Update notes from Github</h4>
            </div>
            <div class="modal-body" style="text-align: left;">
                <pre style="border: none"><?php echo $data['0']['commit']['message'];?></pre>
            </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
              </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
