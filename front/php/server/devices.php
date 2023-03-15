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
  // Set maximum execution time to 15 seconds
  ini_set ('max_execution_time','30');
  
  // Open DB
  OpenDB();

  // Action functions
  if (isset ($_REQUEST['action']) && !empty ($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
    switch ($action) {
      case 'getDeviceData':                getDeviceData();                         break;
      case 'setDeviceData':                setDeviceData();                         break;
      case 'deleteDevice':                 deleteDevice();                          break;
      case 'getNetworkNodes':              getNetworkNodes();                       break;
      case 'deleteAllWithEmptyMACs':       deleteAllWithEmptyMACs();                break;
      case 'deleteAllDevices':             deleteAllDevices();                      break;
      case 'runScan15min':                 runScan15min();                          break;
      case 'runScan1min':                  runScan1min();                           break;
      case 'deleteUnknownDevices':         deleteUnknownDevices();                  break;
      case 'TestNotificationSystem':       TestNotificationSystem();                break;
      case 'deleteEvents':                 deleteEvents();                          break;
      case 'deleteActHistory':             deleteActHistory();                      break;
      case 'deleteDeviceEvents':           deleteDeviceEvents();                    break;
      case 'PiaBackupDBtoArchive':         PiaBackupDBtoArchive();                  break;
      case 'PiaRestoreDBfromArchive':      PiaRestoreDBfromArchive();               break;
      case 'PiaPurgeDBBackups':            PiaPurgeDBBackups();                     break;
      case 'PiaEnableDarkmode':            PiaEnableDarkmode();                     break;
      case 'EnableWebServiceMon':          EnableWebServiceMon();                   break;
      case 'PiaEnableOnlineHistoryGraph':  PiaEnableOnlineHistoryGraph();           break;
      case 'PiaSetAPIKey':                 PiaSetAPIKey();                          break;
      case 'PiaLoginEnable':               PiaLoginEnable();                        break;
      case 'PiaLoginDisable':              PiaLoginDisable();                       break;
      case 'DeleteInactiveHosts':          DeleteInactiveHosts();                   break;
      case 'deleteAllNotifications':       deleteAllNotifications();                break;
      case 'setPiAlertTheme':              setPiAlertTheme();                       break;
      case 'setPiAlertLanguage':           setPiAlertLanguage();                    break;
      case 'setPiAlertArpTimer':           setPiAlertArpTimer();                    break;
      case 'setDeviceListCol':             setDeviceListCol();                      break;
      case 'wakeonlan':                    wakeonlan();                             break;

      case 'getDevicesTotals':             getDevicesTotals();                      break;
      case 'getDevicesList':               getDevicesList();                        break;
      case 'getDevicesListCalendar':       getDevicesListCalendar();                break;
      case 'getOwners':                    getOwners();                             break;
      case 'getDeviceTypes':               getDeviceTypes();                        break;
      case 'getGroups':                    getGroups();                             break;
      case 'getLocations':                 getLocations();                          break;

      case 'saveNewConfigFile':            saveNewConfigFile();                    break;
      case 'RestoreConfigFile':            RestoreConfigFile();                    break;
      case 'BackupConfigFile':             BackupConfigFile();                     break;
     
      default:                             logServerConsole ('Action: '. $action);  break;
    }
  }


//------------------------------------------------------------------------------
//  Query Device Data
//------------------------------------------------------------------------------
function getDeviceData() {
  global $db;

  // Request Parameters
  $periodDate = getDateFromPeriod();
  $mac = $_REQUEST['mac'];
  // Device Data
  $sql = 'SELECT rowid, *,
            CASE WHEN dev_AlertDeviceDown=1 AND dev_PresentLastScan=0 THEN "Down"
                 WHEN dev_PresentLastScan=1 THEN "On-line"
                 ELSE "Off-line" END as dev_Status
          FROM Devices
          WHERE dev_MAC="'. $mac .'" or cast(rowid as text)="'. $mac. '"';
  $result = $db->query($sql);
  $row = $result -> fetchArray (SQLITE3_ASSOC);
  $deviceData = $row;
  $mac = $deviceData['dev_MAC'];
  $deviceData['dev_Network_Node_MAC'] = $row['dev_Infrastructure'];
  $deviceData['dev_Network_Node_port'] = $row['dev_Infrastructure_port'];
  $deviceData['dev_FirstConnection'] = formatDate ($row['dev_FirstConnection']); // Date formated
  $deviceData['dev_LastConnection'] =  formatDate ($row['dev_LastConnection']);  // Date formated
  $deviceData['dev_RandomMAC'] = ( in_array($mac[1], array("2","6","A","E","a","e")) ? 1 : 0);
  // Count Totals
  $condition = ' WHERE eve_MAC="'. $mac .'" AND eve_DateTime >= '. $periodDate;
  // Connections
  $sql = 'SELECT COUNT(*) FROM Sessions
          WHERE ses_MAC="'. $mac .'"
          AND (   ses_DateTimeConnection    >= '. $periodDate .'
               OR ses_DateTimeDisconnection >= '. $periodDate .'
               OR ses_StillConnected = 1 )';
  $result = $db->query($sql);
  $row = $result -> fetchArray (SQLITE3_NUM);
  $deviceData['dev_Sessions'] = $row[0];
  // Events
  $sql = 'SELECT COUNT(*) FROM Events '. $condition .' AND eve_EventType <> "Connected" AND eve_EventType <> "Disconnected" ';
  $result = $db->query($sql);
  $row = $result -> fetchArray (SQLITE3_NUM);
  $deviceData['dev_Events'] = $row[0];
  // Down Alerts
  $sql = 'SELECT COUNT(*) FROM Events '. $condition .' AND eve_EventType = "Device Down"';
  $result = $db->query($sql);
  $row = $result -> fetchArray (SQLITE3_NUM);
  $deviceData['dev_DownAlerts'] = $row[0];
  // Presence hours
  $sql = 'SELECT CAST(( MAX (0, SUM (julianday (IFNULL (ses_DateTimeDisconnection, DATETIME("now","localtime")))
                                     - julianday (CASE WHEN ses_DateTimeConnection < '. $periodDate .' THEN '. $periodDate .'
                                                       ELSE ses_DateTimeConnection END)) *24 )) AS INT)
          FROM Sessions
          WHERE ses_MAC="'. $mac .'"
            AND ses_DateTimeConnection IS NOT NULL
            AND (ses_DateTimeDisconnection IS NOT NULL OR ses_StillConnected = 1 )
            AND (   ses_DateTimeConnection    >= '. $periodDate .'
                 OR ses_DateTimeDisconnection >= '. $periodDate .'
                 OR ses_StillConnected = 1 )';
  $result = $db->query($sql);
  $row = $result -> fetchArray (SQLITE3_NUM);
  $deviceData['dev_PresenceHours'] = round ($row[0]);
  // Return json
  echo (json_encode ($deviceData));
}


//------------------------------------------------------------------------------
//  Update Device Data
//------------------------------------------------------------------------------
function setDeviceData() {
  global $db;
  global $pia_lang;

  // sql
  $sql = 'UPDATE Devices SET
                 dev_Name                 = "'. quotes($_REQUEST['name'])           .'",
                 dev_Owner                = "'. quotes($_REQUEST['owner'])          .'",
                 dev_DeviceType           = "'. quotes($_REQUEST['type'])           .'",
                 dev_Vendor               = "'. quotes($_REQUEST['vendor'])         .'",
                 dev_Model                = "'. quotes($_REQUEST['model'])          .'",
                 dev_Serialnumber         = "'. quotes($_REQUEST['serialnumber'])   .'",
                 dev_Favorite             = "'. quotes($_REQUEST['favorite'])       .'",
                 dev_Group                = "'. quotes($_REQUEST['group'])          .'",
                 dev_Location             = "'. quotes($_REQUEST['location'])       .'",
                 dev_Comments             = "'. quotes($_REQUEST['comments'])       .'",
                 dev_Infrastructure       = "'. quotes($_REQUEST['networknode'])    .'",
                 dev_Infrastructure_port  = "'. quotes($_REQUEST['networknodeport']).'",
                 dev_ConnectionType       = "'. quotes($_REQUEST['connectiontype']) .'",                 
                 dev_StaticIP             = "'. quotes($_REQUEST['staticIP'])       .'",
                 dev_ScanCycle            = "'. quotes($_REQUEST['scancycle'])      .'",
                 dev_AlertEvents          = "'. quotes($_REQUEST['alertevents'])    .'",
                 dev_AlertDeviceDown      = "'. quotes($_REQUEST['alertdown'])      .'",
                 dev_SkipRepeated         = "'. quotes($_REQUEST['skiprepeated'])   .'",
                 dev_NewDevice            = "'. quotes($_REQUEST['newdevice'])      .'",
                 dev_Archived             = "'. quotes($_REQUEST['archived'])       .'"
          WHERE dev_MAC="' . $_REQUEST['mac'] .'"';
  // update Data
  $result = $db->query($sql);
  // check result
  if ($result == TRUE) {
    echo $pia_lang['BackDevices_DBTools_UpdDev'];
  } else {
    echo $pia_lang['BackDevices_DBTools_UpdDevError']."\n\n$sql \n\n". $db->lastErrorMsg();
  }
}


//------------------------------------------------------------------------------
//  Delete Device
//------------------------------------------------------------------------------
function deleteDevice() {
  global $db;
  global $pia_lang;

  // sql
  $sql = 'DELETE FROM Devices WHERE dev_MAC="' . $_REQUEST['mac'] .'"';
  // execute sql
  $result = $db->query($sql);
  // check result
  if ($result == TRUE) {
    echo $pia_lang['BackDevices_DBTools_DelDev_a'];
  } else {
    echo $pia_lang['BackDevices_DBTools_DelDevError_a']."\n\n$sql \n\n". $db->lastErrorMsg();
  }
}

//------------------------------------------------------------------------------
//  Delete all devices with empty MAC addresses
//------------------------------------------------------------------------------
function deleteAllWithEmptyMACs() {
  global $db;
  global $pia_lang;

  // sql
  $sql = 'DELETE FROM Devices WHERE dev_MAC=""';
  // execute sql
  $result = $db->query($sql);
  // check result
  if ($result == TRUE) {
    echo $pia_lang['BackDevices_DBTools_DelDev_b'];
  } else {
    echo $pia_lang['BackDevices_DBTools_DelDevError_b']."\n\n$sql \n\n". $db->lastErrorMsg();
  }
}

//------------------------------------------------------------------------------
//  Delete all devices with empty MAC addresses
//------------------------------------------------------------------------------
function deleteUnknownDevices() {
  global $db;
  global $pia_lang;

  // sql
  $sql = 'DELETE FROM Devices WHERE dev_Name="(unknown)"';
  // execute sql
  $result = $db->query($sql);
  // check result
  if ($result == TRUE) {
    echo $pia_lang['BackDevices_DBTools_DelDev_b'];
  } else {
    echo $pia_lang['BackDevices_DBTools_DelDevError_b']."\n\n$sql \n\n". $db->lastErrorMsg();
  }
}

//------------------------------------------------------------------------------
//  Delete Device Events
//------------------------------------------------------------------------------
function deleteDeviceEvents() {
  global $db;
  global $pia_lang;

  // sql
  $sql = 'DELETE FROM Events WHERE eve_MAC="' . $_REQUEST['mac'] .'"';
  // execute sql
  $result = $db->query($sql);
  // check result
  if ($result == TRUE) {
    echo $pia_lang['BackDevices_DBTools_DelEvents'];
  } else {
    echo $pia_lang['BackDevices_DBTools_DelEventsError']."\n\n$sql \n\n". $db->lastErrorMsg();
  }
}

//------------------------------------------------------------------------------
//  Delete all devices 
//------------------------------------------------------------------------------
function deleteAllDevices() {
  global $db;
  global $pia_lang;

  // sql
  $sql = 'DELETE FROM Devices';
  // execute sql
  $result = $db->query($sql);
  // check result
  if ($result == TRUE) {
    echo $pia_lang['BackDevices_DBTools_DelDev_b'];
  } else {
    echo $pia_lang['BackDevices_DBTools_DelDevError_b']."\n\n$sql \n\n". $db->lastErrorMsg();
  }
}

//------------------------------------------------------------------------------
//  Delete all Events 
//------------------------------------------------------------------------------
function deleteEvents() {
  global $db;
  global $pia_lang;

  // sql
  $sql = 'DELETE FROM Events';
  // execute sql
  $result = $db->query($sql);
  // check result
  if ($result == TRUE) {
    echo $pia_lang['BackDevices_DBTools_DelEvents'];
  } else {
    echo $pia_lang['BackDevices_DBTools_DelEventsError']."\n\n$sql \n\n". $db->lastErrorMsg();
  }
}

//------------------------------------------------------------------------------
//  Delete History
//------------------------------------------------------------------------------
function deleteActHistory() {
  global $db;
  global $pia_lang;

  // sql
  $sql = 'DELETE FROM Online_History';
  // execute sql
  $result = $db->query($sql);
  // check result
  if ($result == TRUE) {
    echo $pia_lang['BackDevices_DBTools_DelActHistory'];
  } else {
    echo $pia_lang['BackDevices_DBTools_DelActHistoryError']."\n\n$sql \n\n". $db->lastErrorMsg();
  }
}

//------------------------------------------------------------------------------
//  Backup DB to Archiv
//------------------------------------------------------------------------------
function PiaBackupDBtoArchive() {
  // prepare fast Backup
  $file = '../../../db/pialert.db';
  $newfile = '../../../db/pialert.db.latestbackup';
  global $pia_lang;

  // copy files as a fast Backup
  if (!copy($file, $newfile)) {
      echo $pia_lang['BackDevices_Backup_CopError'];
  } else {
    // Create archive with actual date
    $Pia_Archive_Name = 'pialertdb_'.date("Ymd_His").'.zip';
    $Pia_Archive_Path = '../../../db/';
    exec('zip -j '.$Pia_Archive_Path.$Pia_Archive_Name.' ../../../db/pialert.db', $output);
    // chheck if archive exists
    if (file_exists($Pia_Archive_Path.$Pia_Archive_Name) && filesize($Pia_Archive_Path.$Pia_Archive_Name) > 0) {
      echo $pia_lang['BackDevices_Backup_okay'].': ('.$Pia_Archive_Name.')';
      unlink($newfile);
      echo("<meta http-equiv='refresh' content='2; URL=./maintenance.php?tab=3'>");
    } else {
      echo $pia_lang['BackDevices_Backup_Failed'].' (pialert.db.latestbackup)';
    }
  }
}

//------------------------------------------------------------------------------
//  Restore DB from Archiv
//------------------------------------------------------------------------------
function PiaRestoreDBfromArchive() {
  // prepare fast Backup
  $file = '../../../db/pialert.db';
  $oldfile = '../../../db/pialert.db.prerestore';
  global $pia_lang;

  // copy files as a fast Backup
  if (!copy($file, $oldfile)) {
      echo $pia_lang['BackDevices_Restore_CopError'];
  } else {
    // extract latest archive and overwrite the actual pialert.db
    $Pia_Archive_Path = '../../../db/';
    exec('/bin/ls -Art '.$Pia_Archive_Path.'*.zip | /bin/tail -n 1 | /usr/bin/xargs -n1 /bin/unzip -o -d ../../../db/', $output);
    // check if the pialert.db exists
    if (file_exists($file)) {
       echo $pia_lang['BackDevices_Restore_okay'];
       unlink($oldfile);
       echo("<meta http-equiv='refresh' content='2; URL=./maintenance.php?tab=3'>");
     } else {
       echo $pia_lang['BackDevices_Restore_Failed'];
     }
  }
}

//------------------------------------------------------------------------------
//  Purge Backups
//------------------------------------------------------------------------------
function PiaPurgeDBBackups() {
  global $pia_lang;

  // Clean DB Backups
  $Pia_Archive_Path = '../../../db';
  $Pia_Backupfiles = array();
  $files = array_diff(scandir($Pia_Archive_Path, SCANDIR_SORT_DESCENDING), array('.', '..', 'pialert.db', 'pialertdb-reset.zip'));
  foreach ($files as &$item) 
    {
      $item = $Pia_Archive_Path.'/'.$item;
      if (stristr($item, 'setting_') == '') {array_push($Pia_Backupfiles, $item);}
    }
  if (sizeof($Pia_Backupfiles) > 3) 
    {
      rsort($Pia_Backupfiles);
      unset($Pia_Backupfiles[0], $Pia_Backupfiles[1], $Pia_Backupfiles[2]);
      $Pia_Backupfiles_Purge = array_values($Pia_Backupfiles);
      for ($i = 0; $i < sizeof($Pia_Backupfiles_Purge); $i++) 
        {
          unlink($Pia_Backupfiles_Purge[$i]);
        }
    }
  // Clean Config Backups
  unset($Pia_Backupfiles);
  $Pia_Archive_Path = '../../../config';
  $Pia_Backupfiles = array();
  $files = array_diff(scandir($Pia_Archive_Path, SCANDIR_SORT_DESCENDING), array('.', '..', 'pialert.conf', 'version.conf', 'pialert-prev.bak'));
  foreach ($files as &$item) 
    {
      $item = $Pia_Archive_Path.'/'.$item;
      array_push($Pia_Backupfiles, $item);
    }
  if (sizeof($Pia_Backupfiles) > 3) 
    {
      rsort($Pia_Backupfiles);
      unset($Pia_Backupfiles[0], $Pia_Backupfiles[1], $Pia_Backupfiles[2]);
      $Pia_Backupfiles_Purge = array_values($Pia_Backupfiles);
      for ($i = 0; $i < sizeof($Pia_Backupfiles_Purge); $i++) 
        {
          unlink($Pia_Backupfiles_Purge[$i]);
        }
    }
  echo $pia_lang['BackDevices_DBTools_Purge'];
  echo("<meta http-equiv='refresh' content='2; URL=./maintenance.php?tab=3'>");
}

//------------------------------------------------------------------------------
//  Toggle Dark/Light Themes
//------------------------------------------------------------------------------
function PiaEnableDarkmode() {
  $file = '../../../db/setting_darkmode';
  global $pia_lang;

  if (file_exists($file)) {
      echo $pia_lang['BackDevices_darkmode_disabled'];
      unlink($file);
      echo("<meta http-equiv='refresh' content='2; URL=./maintenance.php?tab=1'>");
     } else {
      echo $pia_lang['BackDevices_darkmode_enabled'];
      $darkmode = fopen($file, 'w');
      echo("<meta http-equiv='refresh' content='2; URL=./maintenance.php?tab=1'>");
     }
  }

//------------------------------------------------------------------------------
//  Toggle Web Service Monitoring
//------------------------------------------------------------------------------
function EnableWebServiceMon() {
  global $pia_lang;

  if ($_SESSION['Scan_WebServices'] == True) {
      exec('../../../back/pialert-cli disable_service_mon', $output);
      echo $pia_lang['BackDevices_webservicemon_disabled'];
      echo("<meta http-equiv='refresh' content='2; URL=./maintenance.php?tab=1'>");
  } else {
      exec('../../../back/pialert-cli enable_service_mon', $output);
      echo $pia_lang['BackDevices_webservicemon_enabled'];
      echo("<meta http-equiv='refresh' content='2; URL=./maintenance.php?tab=1'>");
  }
}

//------------------------------------------------------------------------------
//  Toggle History Graph Themes
//------------------------------------------------------------------------------
function PiaEnableOnlineHistoryGraph() {
  $file = '../../../db/setting_noonlinehistorygraph';
  global $pia_lang;

  if (file_exists($file)) {
      echo $pia_lang['BackDevices_onlinehistorygraph_enabled'];
      unlink($file);
      echo("<meta http-equiv='refresh' content='2; URL=./maintenance.php?tab=1'>");
     } else {
      echo $pia_lang['BackDevices_onlinehistorygraph_disabled'];
      $darkmode = fopen($file, 'w');
      echo("<meta http-equiv='refresh'content='2; URL=./maintenance.php?tab=1'>");
     }
  }


//------------------------------------------------------------------------------
//  Set API-Key
//------------------------------------------------------------------------------
function PiaSetAPIKey() {
  //$file = '../../../db/setting_noonlinehistorygraph';
  global $pia_lang;

    exec('../../../back/pialert-cli set_apikey', $output);
    echo $pia_lang['BackDevices_setapikey'];
    echo("<meta http-equiv='refresh' content='2; URL=./maintenance.php?tab=1'>");
  }


//------------------------------------------------------------------------------
//  Test Notification
//------------------------------------------------------------------------------
function TestNotificationSystem() {
  //$file = '../../../db/setting_noonlinehistorygraph';
  global $pia_lang;

    exec('../../../back/pialert-cli reporting_test', $output);
    echo $pia_lang['BackDevices_test_notification'];
    echo("<meta http-equiv='refresh' content='2; URL=./maintenance.php?tab=1'>");
  }

//------------------------------------------------------------------------------
//  Enable Login
//------------------------------------------------------------------------------
function PiaLoginEnable() {
  global $pia_lang;

  session_destroy();
  exec('../../../back/pialert-cli set_login', $output);
  echo $pia_lang['BackDevices_Login_enabled'];
  echo("<meta http-equiv='refresh' content='1; ./index.php?action=logout'>");
  }

//------------------------------------------------------------------------------
//  Disable Login
//------------------------------------------------------------------------------
function PiaLoginDisable() {
  global $pia_lang;

  session_destroy();
  setcookie("PiAlert_SaveLogin", "", time() - 3600);
  exec('../../../back/pialert-cli unset_login', $output);
  echo $pia_lang['BackDevices_Login_disabled'];
  echo("<meta http-equiv='refresh' content='1; ./index.php?action=logout'>");
  }

//------------------------------------------------------------------------------
//  Query total numbers of Devices by status
//------------------------------------------------------------------------------
function getDevicesTotals() {
  global $db;

  // combined query
  $result = $db->query(
        'SELECT 
        (SELECT COUNT(*) FROM Devices '. getDeviceCondition ('all').') as devices, 
        (SELECT COUNT(*) FROM Devices '. getDeviceCondition ('connected').') as connected, 
        (SELECT COUNT(*) FROM Devices '. getDeviceCondition ('favorites').') as favorites, 
        (SELECT COUNT(*) FROM Devices '. getDeviceCondition ('new').') as new, 
        (SELECT COUNT(*) FROM Devices '. getDeviceCondition ('down').') as down, 
        (SELECT COUNT(*) FROM Devices '. getDeviceCondition ('archived').') as archived
   ');
  $row = $result -> fetchArray (SQLITE3_NUM);   
  echo (json_encode (array ($row[0], $row[1], $row[2], $row[3], $row[4], $row[5])));
}


//------------------------------------------------------------------------------
//  Query the List of devices in a determined Status
//------------------------------------------------------------------------------
function getDevicesList() {
  global $db;

  // SQL
  $condition = getDeviceCondition ($_REQUEST['status']);
  $sql = 'SELECT rowid, *, CASE
            WHEN dev_AlertDeviceDown=1 AND dev_PresentLastScan=0 THEN "Down"
            WHEN dev_NewDevice=1 THEN "New"
            WHEN dev_PresentLastScan=1 THEN "On-line"
            ELSE "Off-line"
          END AS dev_Status
          FROM Devices '. $condition;
  $result = $db->query($sql);
  // arrays of rows
  $tableData = array();
  while ($row = $result -> fetchArray (SQLITE3_ASSOC)) {
    $tableData['data'][] = array ($row['dev_Name'],
                                  $row['dev_ConnectionType'],
                                  $row['dev_Owner'],
                                  $row['dev_DeviceType'],
                                  $row['dev_Favorite'],
                                  $row['dev_Group'],
                                  $row['dev_Location'],
                                  formatDate ($row['dev_FirstConnection']),
                                  formatDate ($row['dev_LastConnection']),
                                  $row['dev_LastIP'],
                                  ( in_array($row['dev_MAC'][1], array("2","6","A","E","a","e")) ? 1 : 0),
                                  $row['dev_MAC'], // MAC (hidden)
                                  $row['dev_Status'],
                                  formatIPlong ($row['dev_LastIP']), // IP orderable
                                  $row['rowid'] // Rowid (hidden)
                                 );
  }
  // Control no rows
  if (empty($tableData['data'])) {
    $tableData['data'] = '';
  }
  // Return json
  echo (json_encode ($tableData));
}


//------------------------------------------------------------------------------
//  Query the List of devices for calendar
//------------------------------------------------------------------------------
function getDevicesListCalendar() {
  global $db;

  // SQL
  $condition = getDeviceCondition ($_REQUEST['status']);
  $result = $db->query('SELECT * FROM Devices ' . $condition);

  // arrays of rows
  $tableData = array();
  while ($row = $result -> fetchArray (SQLITE3_ASSOC)) {
    if ($row['dev_Favorite'] == 1) {
      $row['dev_Name'] = '<span class="text-yellow">&#9733</span>&nbsp'. $row['dev_Name'];
    }

    $tableData[] = array ('id'       => $row['dev_MAC'],
                          'title'    => $row['dev_Name'],
                          'favorite' => $row['dev_Favorite']);
  }
  // Return json
  echo (json_encode ($tableData));
}


//------------------------------------------------------------------------------
//  Query the List of Owners
//------------------------------------------------------------------------------
function getOwners() {
  global $db;

  // SQL
  $sql = 'SELECT DISTINCT 1 as dev_Order, dev_Owner
          FROM Devices
          WHERE dev_Owner <> "(unknown)" AND dev_Owner <> ""
            AND dev_Favorite = 1
        UNION
          SELECT DISTINCT 2 as dev_Order, dev_Owner
          FROM Devices
          WHERE dev_Owner <> "(unknown)" AND dev_Owner <> ""
            AND dev_Favorite = 0
            AND dev_Owner NOT IN
               (SELECT dev_Owner FROM Devices WHERE dev_Favorite = 1)
        ORDER BY 1,2 ';
  $result = $db->query($sql);

  // arrays of rows
  $tableData = array();
  while ($row = $result -> fetchArray (SQLITE3_ASSOC)) {
    $tableData[] = array ('order' => $row['dev_Order'],
                          'name'  => $row['dev_Owner']);
  }
  // Return json
  echo (json_encode ($tableData));
}


//------------------------------------------------------------------------------
//  Query the List of types
//------------------------------------------------------------------------------
function getDeviceTypes() {
  global $db;

  // SQL
  $sql = 'SELECT DISTINCT 9 as dev_Order, dev_DeviceType
          FROM Devices
          WHERE dev_DeviceType NOT IN ("",
                 "Smartphone", "Tablet",
                 "Laptop", "Mini PC", "PC", "Printer", "Server", "Singleboard Computer (SBC)",
                 "Game Console", "SmartTV", "TV Decoder", "Virtual Assistance",
                 "Clock", "House Appliance", "Phone", "Radio",
                 "AP", "NAS", "PLC", "Router")

          UNION SELECT 1 as dev_Order, "Smartphone"
          UNION SELECT 1 as dev_Order, "Tablet"

          UNION SELECT 2 as dev_Order, "Laptop"
          UNION SELECT 2 as dev_Order, "Mini PC"
          UNION SELECT 2 as dev_Order, "PC"
          UNION SELECT 2 as dev_Order, "Printer"
          UNION SELECT 2 as dev_Order, "Server"
          UNION SELECT 2 as dev_Order, "Singleboard Computer (SBC)"

          UNION SELECT 3 as dev_Order, "Domotic"
          UNION SELECT 3 as dev_Order, "Game Console"
          UNION SELECT 3 as dev_Order, "SmartTV"
          UNION SELECT 3 as dev_Order, "TV Decoder"
          UNION SELECT 3 as dev_Order, "Virtual Assistance"

          UNION SELECT 4 as dev_Order, "Clock"
          UNION SELECT 4 as dev_Order, "House Appliance"
          UNION SELECT 4 as dev_Order, "Phone"
          UNION SELECT 4 as dev_Order, "Radio"

          UNION SELECT 5 as dev_Order, "AP"
          UNION SELECT 5 as dev_Order, "NAS"
          UNION SELECT 5 as dev_Order, "PLC"
          UNION SELECT 5 as dev_Order, "Router"
          UNION SELECT 5 as dev_Order, "USB LAN Adapter"
          UNION SELECT 5 as dev_Order, "USB WIFI Adapter"

          UNION SELECT 10 as dev_Order, "Other"

          ORDER BY 1,2';
  $result = $db->query($sql);

  // arrays of rows
  $tableData = array();
  while ($row = $result -> fetchArray (SQLITE3_ASSOC)) {
    $tableData[] = array ('order' => $row['dev_Order'],
                          'name'  => $row['dev_DeviceType']);
  }
  // Return json
  echo (json_encode ($tableData));
}


//------------------------------------------------------------------------------
//  Query the List of groups
//------------------------------------------------------------------------------
function getGroups() {
  global $db;

  // SQL
  $sql = 'SELECT DISTINCT 1 as dev_Order, dev_Group
          FROM Devices
          WHERE dev_Group NOT IN ("(unknown)", "Others") AND dev_Group <> ""
          UNION SELECT 1 as dev_Order, "Always on"
          UNION SELECT 1 as dev_Order, "Friends"
          UNION SELECT 1 as dev_Order, "Personal"
          UNION SELECT 2 as dev_Order, "Others"
          ORDER BY 1,2 ';
  $result = $db->query($sql);

  // arrays of rows
  $tableData = array();
  while ($row = $result -> fetchArray (SQLITE3_ASSOC)) {
    $tableData[] = array ('order' => $row['dev_Order'],
                          'name'  => $row['dev_Group']);
  }

  // Return json
  echo (json_encode ($tableData));
}


//------------------------------------------------------------------------------
//  Query the List of locations
//------------------------------------------------------------------------------
function getLocations() {
  global $db;

  // SQL
  $sql = 'SELECT DISTINCT 9 as dev_Order, dev_Location
          FROM Devices
          WHERE dev_Location <> ""
            AND dev_Location NOT IN (
                "Bathroom", "Bedroom", "Dining room", "Hallway",
                "Kitchen", "Laundry", "Living room", "Study", 
                "Attic", "Basement", "Garage", 
                "Back yard", "Garden", "Terrace",
                "Other")

          UNION SELECT 1 as dev_Order, "Bathroom"
          UNION SELECT 1 as dev_Order, "Bedroom"
          UNION SELECT 1 as dev_Order, "Dining room"
          UNION SELECT 1 as dev_Order, "Hall"  
          UNION SELECT 1 as dev_Order, "Kitchen"
          UNION SELECT 1 as dev_Order, "Laundry"
          UNION SELECT 1 as dev_Order, "Living room"
          UNION SELECT 1 as dev_Order, "Study" 

          UNION SELECT 2 as dev_Order, "Attic"
          UNION SELECT 2 as dev_Order, "Basement" 
          UNION SELECT 2 as dev_Order, "Garage" 

          UNION SELECT 3 as dev_Order, "Back yard"
          UNION SELECT 3 as dev_Order, "Garden" 
          UNION SELECT 3 as dev_Order, "Terrace"

          UNION SELECT 10 as dev_Order, "Other"
          ORDER BY 1,2 ';

  $result = $db->query($sql);
  // arrays of rows
  $tableData = array();
  while ($row = $result -> fetchArray (SQLITE3_ASSOC)) {
    $tableData[] = array ('order' => $row['dev_Order'],
                          'name'  => $row['dev_Location']);
  }
  // Return json
  echo (json_encode ($tableData));
}


//------------------------------------------------------------------------------
//  Query Device Data
//------------------------------------------------------------------------------
function getNetworkNodes() {
  global $db;

  // Device Data
  $sql = 'SELECT * FROM network_infrastructure';
  $result = $db->query($sql);
  // arrays of rows
  $tableData = array();
  while ($row = $result -> fetchArray (SQLITE3_ASSOC)) {   
    // Push row data
    $tableData[] = array('id'    => $row['device_id'], 
                         'name'  => $row['net_device_name'].'/'.substr($row['net_device_typ'], 2) );                        
  }
  // Control no rows
  if (empty($tableData)) {
    $tableData = [];
  }
    // Return json
  echo (json_encode ($tableData));
}


//------------------------------------------------------------------------------
//  Status Where conditions
//------------------------------------------------------------------------------
function getDeviceCondition ($deviceStatus) {
  switch ($deviceStatus) {
    case 'all':        return 'WHERE dev_Archived=0';                                                      break;
    case 'connected':  return 'WHERE dev_Archived=0 AND dev_PresentLastScan=1';                            break;
    case 'favorites':  return 'WHERE dev_Archived=0 AND dev_Favorite=1';                                   break;
    case 'new':        return 'WHERE dev_Archived=0 AND dev_NewDevice=1';                                  break;
    case 'down':       return 'WHERE dev_Archived=0 AND dev_AlertDeviceDown=1 AND dev_PresentLastScan=0';  break;
    case 'archived':   return 'WHERE dev_Archived=1';                                                      break;
    default:           return 'WHERE 1=0';                                                                 break;
  }
}


//------------------------------------------------------------------------------
//  Set Theme
//------------------------------------------------------------------------------
function setPiAlertTheme() {
  global $pia_lang;

  $pia_installed_skins = array('skin-black-light', 
                               'skin-black', 
                               'skin-blue-light', 
                               'skin-blue', 
                               'skin-green-light', 
                               'skin-green', 
                               'skin-purple-light', 
                               'skin-purple', 
                               'skin-red-light', 
                               'skin-red', 
                               'skin-yellow-light', 
                               'skin-yellow');

  if (isset($_REQUEST['PiaSkinSelection'])) {
    $pia_skin_set_dir = '../../../db/';
    // echo "Enter Level 1";
    $pia_skin_selector = htmlspecialchars($_REQUEST['PiaSkinSelection']);
    if (in_array($pia_skin_selector, $pia_installed_skins)) {
      foreach ($pia_installed_skins as $file) {
        unlink ($pia_skin_set_dir.'/setting_'.$file);
      }
      foreach ($pia_installed_skins as $file) {
        if (file_exists($pia_skin_set_dir.'/setting_'.$file)) {
            $pia_skin_error = True;
            break;
        } else {
            $pia_skin_error = False;
        }
      }
      if ($pia_skin_error == False) {
        $testskin = fopen($pia_skin_set_dir.'setting_'.$pia_skin_selector, 'w');
        echo $pia_lang['BackDevices_Theme_set'].': '.$_REQUEST['PiaSkinSelection'];
        echo("<meta http-equiv='refresh' content='2; URL=./maintenance.php'>");
      } else {
        echo $pia_lang['BackDevices_Theme_notset'];
        echo("<meta http-equiv='refresh' content='2; URL=./maintenance.php'>");
      }    
    } else {echo $pia_lang['BackDevices_Theme_invalid'];}
  }
}

//------------------------------------------------------------------------------
//  Set Language
//------------------------------------------------------------------------------
function setPiAlertLanguage() {
  global $pia_lang;

  $pia_installed_langs = array('en_us', 
                               'de_de',
                               'es_es',
                               'fr_fr');

  if (isset($_REQUEST['PiaLangSelection'])) {
    $pia_lang_set_dir = '../../../db/';
    $pia_lang_selector = htmlspecialchars($_REQUEST['PiaLangSelection']);
    if (in_array($pia_lang_selector, $pia_installed_langs)) {
      foreach ($pia_installed_langs as $file) {
        unlink ($pia_lang_set_dir.'/setting_language_'.$file);
      }
      foreach ($pia_installed_langs as $file) {
        if (file_exists($pia_lang_set_dir.'/setting_language_'.$file)) {
            $pia_lang_error = True;
            break;
        } else {
            $pia_lang_error = False;
        }
      }
      if ($pia_lang_error == False) {
        $testlang = fopen($pia_lang_set_dir.'setting_language_'.$pia_lang_selector, 'w');
        echo $pia_lang['BackDevices_Language_set'].': '.$_REQUEST['PiaLangSelection'];
        echo("<meta http-equiv='refresh' content='2; URL=./maintenance.php'>");
      } else {
        echo $pia_lang['BackDevices_Language_notset'];
        echo("<meta http-equiv='refresh' content='2; URL=./maintenance.php'>");
      }    
    } else {echo $pia_lang['BackDevices_Language_invalid'];}
  }

}

//------------------------------------------------------------------------------
//  Set Timer
//------------------------------------------------------------------------------
function setPiAlertArpTimer() {
  global $pia_lang;

  if (isset($_REQUEST['PiaArpTimer'])) {
    $pia_lang_set_dir = '../../../db/';
    $file = '../../../db/setting_stoparpscan';
    if (file_exists($file)) {
        echo $pia_lang['BackDevices_Arpscan_enabled'];
        exec('../../../back/pialert-cli enable_scan', $output);
        echo("<meta http-equiv='refresh' content='2; URL=./maintenance.php'>");
       } else {
          if (is_numeric($_REQUEST['PiaArpTimer'])) {
            exec('../../../back/pialert-cli disable_scan '.$_REQUEST['PiaArpTimer'], $output);
          } else { 
            exec('../../../back/pialert-cli disable_scan', $output);
          }
        echo $pia_lang['BackDevices_Arpscan_disabled'];
        echo("<meta http-equiv='refresh' content='2; URL=./maintenance.php'>");
       }
  }
}

//------------------------------------------------------------------------------
//  Restore Config File
//------------------------------------------------------------------------------
function RestoreConfigFile() {
  global $pia_lang;
  // prepare fast Backup
  //$file = '../../../config/pialert.conf';
  // start temp var
  $file = '../../../config/pialert.conf';
  // end temp var
  //$newfile = '../../../config/pialert-'.date("Ymd_His").'.bak';
  $laststate = '../../../config/pialert-prev.bak';
  // Restore fast Backup
  if (!copy($laststate, $file)) {
      echo $pia_lang['BackDevices_ConfEditor_RestoreError'];
  } else {
    echo $pia_lang['BackDevices_ConfEditor_RestoreOkay'];
  }
  copy($file, $laststate);
  echo("<meta http-equiv='refresh' content='2; URL=./maintenance.php'>");
}

//------------------------------------------------------------------------------
//  Save Config File
//------------------------------------------------------------------------------
function BackupConfigFile() {
  global $pia_lang;

  // prepare fast Backup
  $file = '../../../config/pialert.conf';
  $newfile = '../../../config/pialert-'.date("Ymd_His").'.bak';
  $laststate = '../../../config/pialert-prev.bak';
  if (!copy($file, $newfile)) {
      echo $pia_lang['BackDevices_ConfEditor_CopError'];
  } else {
    echo $pia_lang['BackDevices_ConfEditor_CopOkay'];
  }
  // copy files as a fast Backup
  copy($file, $laststate);
}

//------------------------------------------------------------------------------
//  Set Device List Columns
//------------------------------------------------------------------------------
function setDeviceListCol() {
  global $pia_lang;

  if (($_REQUEST['connectiontype'] == 0) || ($_REQUEST['connectiontype'] == 1)) {$Set_ConnectionType = $_REQUEST['connectiontype'];} else {echo "Error. Wrong variable value!"; exit;}
  if (($_REQUEST['favorite'] == 0) || ($_REQUEST['favorite'] == 1))             {$Set_Favorites = $_REQUEST['favorite'];} else {echo "Error. Wrong variable value!"; exit;}
  if (($_REQUEST['group'] == 0) || ($_REQUEST['group'] == 1))                   {$Set_Group = $_REQUEST['group'];} else {echo "Error. Wrong variable value!"; exit;}
  if (($_REQUEST['owner'] == 0) || ($_REQUEST['owner'] == 1))                   {$Set_Owner = $_REQUEST['owner'];} else {echo "Error. Wrong variable value!"; exit;}
  if (($_REQUEST['type'] == 0) || ($_REQUEST['type'] == 1))                     {$Set_Type = $_REQUEST['type'];} else {echo "Error. Wrong variable value!"; exit;}
  if (($_REQUEST['firstsess'] == 0) || ($_REQUEST['firstsess'] == 1))           {$Set_First_Session = $_REQUEST['firstsess'];} else {echo "Error. Wrong variable value!"; exit;}
  if (($_REQUEST['lastsess'] == 0) || ($_REQUEST['lastsess'] == 1))             {$Set_Last_Session = $_REQUEST['lastsess'];} else {echo "Error. Wrong variable value!"; exit;}
  if (($_REQUEST['lastip'] == 0) || ($_REQUEST['lastip'] == 1))                 {$Set_LastIP = $_REQUEST['lastip'];} else {echo "Error. Wrong variable value!"; exit;}
  if (($_REQUEST['mactype'] == 0) || ($_REQUEST['mactype'] == 1))               {$Set_MACType = $_REQUEST['mactype'];} else {echo "Error. Wrong variable value!"; exit;}
  if (($_REQUEST['macaddress'] == 0) || ($_REQUEST['macaddress'] == 1))         {$Set_MACAddress = $_REQUEST['macaddress'];} else {echo "Error. Wrong variable value!"; exit;}
  if (($_REQUEST['location'] == 0) || ($_REQUEST['location'] == 1))             {$Set_Location = $_REQUEST['location'];} else {echo "Error. Wrong variable value!"; exit;}
  echo $pia_lang['BackDevices_DevListCol_noti_text'];
  $config_array = array('ConnectionType' => $Set_ConnectionType, 'Favorites' => $Set_Favorites, 'Group' => $Set_Group, 'Owner' => $Set_Owner, 'Type' => $Set_Type, 'FirstSession' => $Set_First_Session, 'LastSession' => $Set_Last_Session, 'LastIP' => $Set_LastIP, 'MACType' => $Set_MACType, 'MACAddress' => $Set_MACAddress, 'Location' => $Set_Location);
  $DevListCol_file = '../../../db/setting_devicelist';
  $DevListCol_new = fopen($DevListCol_file,'w');
  fwrite($DevListCol_new, json_encode($config_array));
  fclose($DevListCol_new);
  echo("<meta http-equiv='refresh' content='2; URL=./maintenance.php'>");
}

//------------------------------------------------------------------------------
//  Delete Inactive Hosts
//------------------------------------------------------------------------------
function DeleteInactiveHosts() {
  global $pia_lang;
  global $db;

  // sql
  $sql = 'SELECT * FROM Devices WHERE dev_PresentLastScan = 0 AND dev_LastConnection <= date("now", "-30 day")';
  // execute sql
  $result = $db->query($sql);
  while($res = $result->fetchArray(SQLITE3_ASSOC)){
      // sql
      $sql_dev = 'DELETE FROM Devices WHERE dev_MAC="' . $res['dev_MAC'] .'"';
      // execute sql
      $result_dev = $db->query($sql_dev);
      // sql
      $sql_evt = 'DELETE FROM Events WHERE eve_MAC="' . $res['dev_MAC'] .'"';
      // execute sql
      $result_evt = $db->query($sql_evt);
  } 
  //check result
  if ($result_dev == TRUE && $result_evt == TRUE) {
    echo $pia_lang['BackDevices_DBTools_DelInactHosts'];
  } else {
    echo $pia_lang['BackDevices_DBTools_DelInactHostsError'].'<br>'."\n\n$sql_loop \n\n". $db->lastErrorMsg();
  }
}

//------------------------------------------------------------------------------
//  Delete All Notification in WebGUI
//------------------------------------------------------------------------------
function deleteAllNotifications() {
  global $pia_lang;

  $regex = '/[0-9]+-[0-9]+_.*\\.txt/i';
  $reports_path = '../../reports/';
  $files = array_diff(scandir($reports_path, SCANDIR_SORT_DESCENDING), array('.', '..'));
  $count_all_reports =  sizeof($files);
  foreach ($files as &$item) 
    {
      if (preg_match($regex, $item) == True) {
        unlink($reports_path.$item);
      }
    }
  echo $count_all_reports.' '.$pia_lang['BackDevices_Report_Delete'] ;
  echo("<meta http-equiv='refresh' content='2; URL=./reports.php'>");
}

//------------------------------------------------------------------------------
//  Wake-on-LAN
//------------------------------------------------------------------------------
function crosscheckMAC($query_mac) {
  global $db;
    $sql = 'SELECT * FROM Devices WHERE dev_MAC="'. $query_mac .'"';
  $result = $db->query($sql);
  $row = $result -> fetchArray (SQLITE3_ASSOC);
  return $row['dev_MAC'];
}

function wakeonlan() {
  global $pia_lang;

  $WOL_HOST_IP = $_REQUEST['ip'];
  $WOL_HOST_MAC = $_REQUEST['mac'];

  if (!filter_var($WOL_HOST_IP, FILTER_VALIDATE_IP)) {
      echo "Invalid IP! ".$pia_lang['BackDevDetail_Tools_WOL_error']; exit;
  } 
  elseif (!filter_var($WOL_HOST_MAC, FILTER_VALIDATE_MAC)) {
      echo "Invalid MAC! ".$pia_lang['BackDevDetail_Tools_WOL_error']; exit;
  } 
  elseif (crosscheckMAC($WOL_HOST_MAC) == "") {
      echo "Unknown MAC! ".$pia_lang['BackDevDetail_Tools_WOL_error']; exit;
  }

  exec('wakeonlan '.$WOL_HOST_MAC , $output);

  echo $pia_lang['BackDevDetail_Tools_WOL_okay'];

  // Prepare short term memory
  $PIA_TIME = date('Y-m-d H:i:s');

  unset($_SESSION['ScanShortMem_WOL']);
  $_SESSION['ScanShortMem_WOL'] = 'Last Wake-on-LAN Command<br><br><span style="display:inline-block; width: 100px;">IP:</span> '.$WOL_HOST_IP.'<br><span style="display:inline-block; width: 100px;">MAC:</span> '.$WOL_HOST_MAC.'<br><span style="display:inline-block; width: 100px;">Scan Time:</span> '.$PIA_TIME.'<br><br>Output:<br>';

  foreach($output as $line){
      //echo $line . "\n";
      // Safe last Scan result in Session (Short term memory)
      $_SESSION['ScanShortMem_WOL'] = $_SESSION['ScanShortMem_WOL'].$line.'<br>';
  }

}


//------------------------------------------------------------------------------
//  End
//------------------------------------------------------------------------------
?>
