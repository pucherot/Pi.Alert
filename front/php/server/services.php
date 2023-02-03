<?php
//------------------------------------------------------------------------------
//  Pi.Alert
//  Open Source Network Guard / WIFI & LAN intrusion detector 
//
//  events.php - Front module. Server side. Manage Events
//------------------------------------------------------------------------------
//  Puche 2021        pi.alert.application@gmail.com        GNU GPLv3
//------------------------------------------------------------------------------


//------------------------------------------------------------------------------
  // External files
  require 'db.php';
  require 'util.php';


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
      case 'getEventsTotals':    getEventsTotals();                       break;
      case 'getEvents':          getEvents();                             break;
    }
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



?>
