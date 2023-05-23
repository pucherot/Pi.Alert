<?php
#<!-- ---------------------------------------------------------------------------
#  Pi.Alert
#  Open Source Network Guard / WIFI & LAN intrusion detector
#
#  graph.php - Front module. Activity graph
#-------------------------------------------------------------------------------
#  leiweibau 2023                                          GNU GPLv3
#--------------------------------------------------------------------------- -->

// History Graph Online/Offline/Archive Devices
function prepare_graph_arrays_history() {
	global $db;

	$Pia_Graph_Device_Time = array();
	$Pia_Graph_Device_All = array();
	$Pia_Graph_Device_Online = array();
	$Pia_Graph_Device_Down = array();
	$Pia_Graph_Device_Arch = array();
	$results = $db->query('SELECT * FROM Online_History ORDER BY Scan_Date DESC LIMIT 144');
	while ($row = $results->fetchArray()) {
		$time_raw = explode(' ', $row['Scan_Date']);
		$time = explode(':', $time_raw[1]);
		array_push($Pia_Graph_Device_Time, $time[0] . ':' . $time[1]);
		array_push($Pia_Graph_Device_Down, $row['Down_Devices']);
		array_push($Pia_Graph_Device_All, $row['All_Devices']);
		array_push($Pia_Graph_Device_Online, $row['Online_Devices']);
		array_push($Pia_Graph_Device_Arch, $row['Archived_Devices']);
	}
	return array($Pia_Graph_Device_Time, $Pia_Graph_Device_Down, $Pia_Graph_Device_All, $Pia_Graph_Device_Online, $Pia_Graph_Device_Arch);
}

function pia_graph_devices_data($Pia_Graph_Array) {
	$Pia_Graph_Array_rev = array_reverse($Pia_Graph_Array);
	foreach ($Pia_Graph_Array_rev as $result) {
		echo "'" . $result . "'";
		echo ",";
	}
}

// History Graph Online/Offline/Archive Devices
function prepare_graph_arrays_webservice($service_url) {
	global $db;

	$Pia_Graph_Service_Time = array();
	$Pia_Graph_Service_Down = array();
	$Pia_Graph_Service_2xx = array();
	$Pia_Graph_Service_3xx = array();
	$Pia_Graph_Service_4xx = array();
	$Pia_Graph_Service_5xx = array();
	$results = $db->query('SELECT * FROM Services_Events WHERE moneve_URL="' . $service_url . '" ORDER BY moneve_DateTime DESC LIMIT 144');
	$http2xx = 0;
	$http3xx = 0;
	$http4xx = 0;
	$http5xx = 0;
	$httpdown = 0;
	while ($row = $results->fetchArray()) {
		$time_raw = explode(' ', $row['moneve_DateTime']);
		$time = explode(':', $time_raw[1]);
		if ($time[1] < 10) {$time[1] = "00";} else { $time[1] = round($time[1], -1);}
		// Wenn ein Statuscode 2xx ist, müssen die Gruppen der beiden anderen StatusCodes auf null gesetzt werden. der Wert, der "vorhanden" ist wird auf 1 gesetzt
		if ($row['moneve_StatusCode'] >= 200 && $row['moneve_StatusCode'] < 300) {
			array_push($Pia_Graph_Service_Down, "0");
			array_push($Pia_Graph_Service_2xx, "1");
			array_push($Pia_Graph_Service_3xx, "0");
			array_push($Pia_Graph_Service_4xx, "0");
			array_push($Pia_Graph_Service_5xx, "0");
			$http2xx++;
		}
		if ($row['moneve_StatusCode'] >= 300 && $row['moneve_StatusCode'] < 400) {
			array_push($Pia_Graph_Service_Down, "0");
			array_push($Pia_Graph_Service_2xx, "0");
			array_push($Pia_Graph_Service_3xx, "1");
			array_push($Pia_Graph_Service_4xx, "0");
			array_push($Pia_Graph_Service_5xx, "0");
			$http3xx++;
		}
		if ($row['moneve_StatusCode'] >= 400 && $row['moneve_StatusCode'] < 500) {
			array_push($Pia_Graph_Service_Down, "0");
			array_push($Pia_Graph_Service_2xx, "0");
			array_push($Pia_Graph_Service_3xx, "0");
			array_push($Pia_Graph_Service_4xx, "1");
			array_push($Pia_Graph_Service_5xx, "0");
			$http4xx++;
		}
		if ($row['moneve_StatusCode'] >= 500 && $row['moneve_StatusCode'] < 600) {
			array_push($Pia_Graph_Service_Down, "0");
			array_push($Pia_Graph_Service_2xx, "0");
			array_push($Pia_Graph_Service_3xx, "0");
			array_push($Pia_Graph_Service_4xx, "0");
			array_push($Pia_Graph_Service_5xx, "1");
			$http5xx++;
		}
		if ($row['moneve_StatusCode'] == 0) {
			array_push($Pia_Graph_Service_Down, "1");
			array_push($Pia_Graph_Service_2xx, "0");
			array_push($Pia_Graph_Service_3xx, "0");
			array_push($Pia_Graph_Service_4xx, "0");
			array_push($Pia_Graph_Service_5xx, "0");
			$httpdown++;
		}
		array_push($Pia_Graph_Service_Time, $time[0] . ':' . $time[1]);

	}
	return array($Pia_Graph_Service_Time, $Pia_Graph_Service_Down, $Pia_Graph_Service_2xx, $Pia_Graph_Service_3xx, $Pia_Graph_Service_4xx, $Pia_Graph_Service_5xx, $httpdown, $http2xx, $http3xx, $http4xx, $http5xx);
}

?>