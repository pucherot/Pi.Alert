<?php
require 'db.php';
require 'journal.php';

OpenDB();

foreach (glob("../../../db/setting_language*") as $filename) {
	$pia_lang_selected = str_replace('setting_language_', '', basename($filename));
}
if (strlen($pia_lang_selected) == 0) {$pia_lang_selected = 'en_us';}
require '../templates/language/' . $pia_lang_selected . '.php';

// Get Version from version.conf
$conf_file = '../../../config/version.conf';
$conf_data = parse_ini_file($conf_file);

// Get Pi.Alert Release
$curl_handle = curl_init();
curl_setopt($curl_handle, CURLOPT_URL, 'https://api.github.com/repos/leiweibau/Pi.Alert/commits?path=tar%2Fpialert_latest.tar&page=1&per_page=1');
curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl_handle, CURLOPT_USERAGENT, 'PHP');
$query = curl_exec($curl_handle);
curl_close($curl_handle);
// Generate JSON (Pi.Alert)
$pialert_update = json_decode($query, true);

// Get MaxMind DB Release
$curl_handle = curl_init();
curl_setopt($curl_handle, CURLOPT_URL, 'https://api.github.com/repos/P3TERX/GeoLite.mmdb/releases/latest');
curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl_handle, CURLOPT_USERAGENT, 'PHP');
$query = curl_exec($curl_handle);
curl_close($curl_handle);
// Generate JSON (GeoIP)
$geolite_update = json_decode($query, true);

// Get GeoIP Version from Tag Name
$geolite_new_version = $geolite_update['name'];
// GeoIP Version from file system
$geoliteDB_file = '../../../db/GeoLite2-Country.mmdb';
if (file_exists($geoliteDB_file)) {
	$geolite_cur_version = date("Y.m.d", filemtime($geoliteDB_file));
} else { $geolite_cur_version = "###";}
// DEBUG
// $geolite_cur_version = '2023-05-28';

// Get Pi.Alert Version fro Github timestamp
$utc_ts = strtotime($pialert_update['0']['commit']['author']['date']);
$offset = date("Z");
$local_ts = $utc_ts + $offset;
$local_time = date("d.m.Y, H:i", $utc_ts);
// Pi.Alert Version from config file
$pialert_cur_version = $conf_data['VERSION_DATE'];

// Get latest Release notes from Github
$updatenotes_array = explode("\n", $pialert_update['0']['commit']['message']);
$updatenotes_array = array_filter($updatenotes_array);

// DEBUG
// $pialert_cur_version = '2023-05-28';
$pialert_new_version = substr($updatenotes_array[0], -10);
// prepare dates for comparison
$temp_geolite_cur_version = str_replace(".", "-", $geolite_cur_version);
$temp_geolite_new_version = str_replace(".", "-", $geolite_new_version);

// Print Update Box for GeoIP
if (($temp_geolite_new_version > $temp_geolite_cur_version) && ($geolite_cur_version != "###")) {
// DB present and github is newer as local
	echo '<div class="box">
    		<div class="box-body">
				<h4 class="text-aqua" style="text-align: center;">' . $pia_lang['GeoLiteDB_Title'] . '</h4>
				<p style="font-size: 16px; font-weight: bold;">
				' . $pia_lang['GeoLiteDB_cur'] . ': 	<span class="text-green">	' . $geolite_cur_version . '</span><br>
				' . $pia_lang['GeoLiteDB_new'] . ': 	<span class="text-red">		' . $geolite_new_version . '</span>
				</p>



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
                <button class="btn btn-default" id="updateDB-button">' . $pia_lang['GeoLiteDB_button_upd'] . '</button>
              </div>
            </div>
          </div>


			</div>
		  </div>';
	// Logging
	pialert_logging('a_060', $_SERVER['REMOTE_ADDR'], 'LogStr_0063', '', '');
} elseif ($geolite_cur_version == "###") {
// No DB present
	echo '<div class="box">
    		<div class="box-body">
				<h4 class="text-aqua" style="text-align: center;">' . $pia_lang['GeoLiteDB_Title'] . '</h4>
				<p class="text-yellow" style="font-size: 16px; font-weight: bold;">' . $pia_lang['GeoLiteDB_absent'] . '</p>
				<p>' . $pia_lang['GeoLiteDB_Installnotes'] . '</p>
			</div>
		  </div>';
	// Logging
	pialert_logging('a_060', $_SERVER['REMOTE_ADDR'], 'LogStr_0065', '', '');
} else {
// DB present an newer as github version
	echo '<div class="box">
    		<div class="box-body">
				<h4 class="text-aqua" style="text-align: center;">' . $pia_lang['GeoLiteDB_Title'] . '</h4>
				<p class="text-green" style="font-size: 16px; font-weight: bold;">' . $pia_lang['Updatecheck_U2D'] . '</p>
			</div>
		  </div>';
	// Logging
	pialert_logging('a_060', $_SERVER['REMOTE_ADDR'], 'LogStr_0064', '', '');
}

// Print Update Box for Pi.Alert
if ($pialert_cur_version != $pialert_new_version) {
// github version is not equal to local version (only a local dev version could be newer than github version)
	echo '<div class="box">
    		<div class="box-body">
				<h4 class="text-aqua" style="text-align: center;">' . $pia_lang['Maintenance_Github_package_a'] . ' ' . $local_time . ' ' . $pia_lang['Maintenance_Github_package_b'] . '</h4>
				<p style="font-size: 16px; font-weight: bold;">
				' . $pia_lang['Updatecheck_cur'] . ': 	<span class="text-green">	' . $pialert_cur_version . '</span><br>
				' . $pia_lang['Updatecheck_new'] . ': 	<span class="text-red">		' . $pialert_new_version . '</span>
				</p>
			</div>
		  </div>';
}

// Print Update Box for Pi.Alert
if ($pialert_cur_version != $pialert_new_version) {
	echo '<div class="box">
    <div class="box-body">
		<h4 class="text-aqua" style="text-align: center;">' . $pia_lang['Updatecheck_RN'] . '</h4><div>';
// Transform release notes
	foreach ($updatenotes_array as $row) {
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
// Logging
	pialert_logging('a_060', $_SERVER['REMOTE_ADDR'], 'LogStr_0061', '', '');

}

if ($pialert_cur_version == $pialert_new_version) {
// github version is equal to local version
	echo '<div class="box">
    		<div class="box-body">
				<h4 class="text-aqua" style="text-align: center;">' . $pia_lang['Updatecheck_RN2'] . '</h4>
				<p class="text-green" style="font-size: 16px; font-weight: bold;">' . $pia_lang['Updatecheck_U2D'] . '</p>
			</div>
		  </div>';
// Logging
	pialert_logging('a_060', $_SERVER['REMOTE_ADDR'], 'LogStr_0062', '', '');
}

echo '</div>';
echo '</div>';

echo '
<script>
$("#updateDB-button").on(\'click\', function() {
    var loader = $("#downloader");
    var downloadButton = $(this);
    // Hide the download button
    downloadButton.hide();
    // Display the loading animation
    loader.show();
    // Send an AJAX request to initiate the file download
    $.ajax({
        url: \'./php/server/services.php?action=updateGeoDB\',
        method: \'GET\',
        success: function(response) {
            console.log(\'Download complete!\');
        },
        // error: function() {
        //     console.error(\'Download error!\');
        // },
        complete: function() {
            // Show the download button again
            setTimeout(function () {
              location.reload(true);
            }, 1000);
        }
    });
});
</script>';

?>


