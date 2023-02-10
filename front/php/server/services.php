<?php
session_start();
//------------------------------------------------------------------------------
//  Pi.Alert
//  Open Source Network Guard / WIFI & LAN intrusion detector 
//
//  devices.php - Front module. Server side. Manage Devices
//------------------------------------------------------------------------------
//  Puche      2021        pi.alert.application@gmail.com   GNU GPLv3
//  jokob-sk   2022        jokob.sk@gmail.com               GNU GPLv3
//  leiweibau  2023        https://github.com/leiweibau     GNU GPLv3
//------------------------------------------------------------------------------

foreach (glob("../../../db/setting_language*") as $filename) {
    $pia_lang_selected = str_replace('setting_language_','',basename($filename));
}
if (strlen($pia_lang_selected) == 0) {$pia_lang_selected = 'en_us';}

//------------------------------------------------------------------------------
  // External files
  require 'db.php';
  require 'util.php';
  require '../templates/language/'.$pia_lang_selected.'.php';

//------------------------------------------------------------------------------
//  Action selector
//------------------------------------------------------------------------------
  // Set maximum execution time to 1 minute
  ini_set ('max_execution_time','60');
  
  // Open DB
  OpenDB();

  // Action functions
  if (isset ($_REQUEST['action']) && !empty ($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
    switch ($action) {
      case 'getEventsTotals':             getEventsTotals();                  break;
      case 'getEvents':                   getEvents();                        break;
      case 'getEventsTotalsforService':   getEventsTotalsforService();        break;
      case 'setServiceData':              setServiceData();                   break;
      case 'deleteService':               deleteService();                    break;
      case 'insertNewService':            insertNewService();                 break;
    }
  }

//------------------------------------------------------------------------------
//  Query total numbers of Events from Device
//------------------------------------------------------------------------------
function getEventsTotalsforService() {
  global $db;

  // Request Parameters
  $serviceURL = $_REQUEST['url'];

  // SQL 
  $SQL1 = 'SELECT Count(*)
           FROM Services_Events 
           WHERE moneve_URL = "'. $serviceURL.'"';

  // All
  $result = $db->query($SQL1);
  $row = $result -> fetchArray (SQLITE3_NUM);
  $eventsAll = $row[0];

  // 2xx
  $result = $db->query($SQL1. ' AND moneve_StatusCode LIKE "2%" ');
  $row = $result -> fetchArray (SQLITE3_NUM);
  $events2xx = $row[0];

  // Missing
  $result = $db->query($SQL1. ' AND moneve_StatusCode LIKE "3%" ');
  $row = $result -> fetchArray (SQLITE3_NUM);
  $events3xx = $row[0];

  // Voided
  $result = $db->query($SQL1. ' AND moneve_StatusCode LIKE "4%" ');
  $row = $result -> fetchArray (SQLITE3_NUM);
  $events4xx = $row[0];

  // New
  $result = $db->query($SQL1. ' AND moneve_StatusCode LIKE "5%" ');
  $row = $result -> fetchArray (SQLITE3_NUM);
  $events5xx = $row[0];

  // Down
  $result = $db->query($SQL1. ' AND moneve_Latency LIKE "99999%" ');
  $row = $result -> fetchArray (SQLITE3_NUM);
  $eventsDown = $row[0];

  // Return json
  echo (json_encode (array ($eventsAll, $events2xx, $events3xx, $events4xx, $events5xx, $eventsDown)));
}


//------------------------------------------------------------------------------
//  Query total numbers of Events
//------------------------------------------------------------------------------
function getEventsTotals() {
  global $db;

  // Request Parameters
  $periodDate = getDateFromPeriod();

  // SQL 
  $SQL1 = 'SELECT Count(*)
           FROM Services_Events 
           WHERE moneve_DateTime >= '. $periodDate;

  // All
  $result = $db->query($SQL1);
  $row = $result -> fetchArray (SQLITE3_NUM);
  $eventsAll = $row[0];

  // 2xx
  $result = $db->query($SQL1. ' AND moneve_StatusCode LIKE "2%" ');
  $row = $result -> fetchArray (SQLITE3_NUM);
  $events2xx = $row[0];

  // Missing
  $result = $db->query($SQL1. ' AND moneve_StatusCode LIKE "3%" ');
  $row = $result -> fetchArray (SQLITE3_NUM);
  $events3xx = $row[0];

  // Voided
  $result = $db->query($SQL1. ' AND moneve_StatusCode LIKE "4%" ');
  $row = $result -> fetchArray (SQLITE3_NUM);
  $events4xx = $row[0];

  // New
  $result = $db->query($SQL1. ' AND moneve_StatusCode LIKE "5%" ');
  $row = $result -> fetchArray (SQLITE3_NUM);
  $events5xx = $row[0];

  // Down
  $result = $db->query($SQL1. ' AND moneve_Latency LIKE "99999%" ');
  $row = $result -> fetchArray (SQLITE3_NUM);
  $eventsDown = $row[0];

  // Return json
  echo (json_encode (array ($eventsAll, $events2xx, $events3xx, $events4xx, $events5xx, $eventsDown)));
}


//------------------------------------------------------------------------------
//  Query the List of events
//------------------------------------------------------------------------------
function getEvents() {
  global $db;

  // Request Parameters
  $type       = $_REQUEST ['type'];
  $periodDate = getDateFromPeriod();

  // SQL
  $SQL1 = 'SELECT *
           FROM Services_Events 
           WHERE moneve_DateTime >= '. $periodDate;

  // SQL Variations for status
  switch ($type) {
    case 'all':       $SQL = $SQL1;                                           break;
    case '2':         $SQL = $SQL1 .' AND moneve_StatusCode LIKE "2%" ';      break;
    case '3':         $SQL = $SQL1 .' AND moneve_StatusCode LIKE "3%" ';      break;
    case '4':         $SQL = $SQL1 .' AND moneve_StatusCode LIKE "4%" ';      break;
    case '5':         $SQL = $SQL1 .' AND moneve_StatusCode LIKE "5%" ';      break;
    case '99999999':  $SQL = $SQL1 .' AND moneve_Latency LIKE "999999%" ';    break;
    default:          $SQL = $SQL1 .' AND 1==0 ';                             break;
  }

  // Query
  $result = $db->query($SQL);

  $tableData = array();
  while ($row = $result -> fetchArray (SQLITE3_NUM)) {

    $row[1] = formatDate ($row[1]);
    if ($row[3] == "99999999") {$row[3] = "No Response";}

    // IP Order
    // $row[10] = formatIPlong ($row[9]);

    $tableData['data'][] = $row;
  }

  // Control no rows
  if (empty($tableData['data'])) {
    $tableData['data'] = '';
  }

  // Return json
  echo (json_encode ($tableData));
}

//------------------------------------------------------------------------------
//  Set Services Data
//------------------------------------------------------------------------------

function setServiceData() {
  global $db;
  global $pia_lang;
  // sql
  $sql = 'UPDATE Services SET
                 mon_Tags           = "'. quotes($_REQUEST['tags'])         .'",
                 mon_MAC            = "'. quotes($_REQUEST['mac'])          .'",
                 mon_AlertDown      = "'. quotes($_REQUEST['alertdown'])    .'",
                 mon_AlertEvents    = "'. quotes($_REQUEST['alertevents'])  .'"
          WHERE mon_URL="' . $_REQUEST['url'] .'"';
  // update Data
  $result = $db->query($sql);
  // check result
  if ($result == TRUE) {
    echo $pia_lang['BackWebServices_UpdServ'];
  } else {
    echo $pia_lang['BackWebServices_UpdServError']."\n\n$sql \n\n". $db->lastErrorMsg();
    //echo $_REQUEST['tags'];
  }

}

//------------------------------------------------------------------------------
//  Delete Service
//------------------------------------------------------------------------------
function deleteService() {
  global $db;
  global $pia_lang;

  // sql
  $sql = 'DELETE FROM Services WHERE mon_URL="' . $_REQUEST['url'] .'"';
  // execute sql
  $result = $db->query($sql);
  // Remove Events too
  //$sql = 'DELETE FROM Services_Events WHERE moneve_URL="' . $_REQUEST['url'] .'"';
  // execute sql
  //$result = $db->query($sql);
  // check result
  if ($result == TRUE) {
    echo $pia_lang['BackWebServices_DelServ'];
    echo("<meta http-equiv='refresh' content='2; URL=./services.php'>");
  } else {
    echo $pia_lang['BackWebServices_DelServError']."\n\n$sql \n\n". $db->lastErrorMsg();
  }
}

//------------------------------------------------------------------------------
//  Delete Service
//------------------------------------------------------------------------------
function insertNewService() {
  global $db;
  global $pia_lang;

  //echo 'Enter Function';

  $url = $_REQUEST['url'];

  if(!$url || !is_string($url) || ! preg_match('/^http(s)?:\/\/[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i', $url)){
    return false;
  }

  $check_timestamp = date("Y-m-d H:i:s");

  $checkURL = curl_init($url);
  curl_setopt($checkURL, CURLOPT_HEADER,         1);
  curl_setopt($checkURL, CURLOPT_NOBODY,         1);
  curl_setopt($checkURL, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($checkURL, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($checkURL, CURLOPT_TIMEOUT,       10);
  $output = curl_exec($checkURL);
  $httpstats = curl_getinfo($checkURL);
  $http_code = curl_getinfo($checkURL, CURLINFO_HTTP_CODE);
  curl_close($checkURL);

  // sql
  $sql = 'INSERT INTO Services ("mon_URL", "mon_MAC", "mon_LastStatus", "mon_LastLatency", "mon_LastScan", "mon_Tags", "mon_AlertEvents", "mon_AlertDown", "mon_TargetIP") 
                         VALUES("'.$url.'", "'.$_REQUEST['mac'].'", "'.$http_code.'", "'.$httpstats['total_time'].'", "'.$check_timestamp.'", "'.$_REQUEST['tags'].'", "'.$_REQUEST['alertevents'].'", "'.$_REQUEST['alertdown'].'", "'.$httpstats['primary_ip'].'")'; 
  
  // execute sql
  $result = $db->query($sql);
  // check result
  if ($result == TRUE) {
    echo $pia_lang['BackWebServices_InsServ'];
    echo("<meta http-equiv='refresh' content='2; URL=./services.php'>");
  } else {
    echo $pia_lang['BackWebServices_InsServError']."\n\n$sql \n\n". $db->lastErrorMsg();
  }

}


?>
