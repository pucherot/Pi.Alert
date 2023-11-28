<?php
unset($pia_lang);
//////////////////////////////////////////////////////////////////
// About - Update by @TeroRERO 07ago2022
//////////////////////////////////////////////////////////////////
$pia_lang['About_Title'] = 'Open Source Network Guard';
$pia_lang['About_Exit'] = 'Sign out';
$pia_lang['About_Reports'] = 'Show notifications';

//////////////////////////////////////////////////////////////////
// General
//////////////////////////////////////////////////////////////////
$pia_lang['Gen_Delete'] = 'Delete';
$pia_lang['Gen_Cancel'] = 'Cancel';
$pia_lang['Gen_Okay'] = 'Ok';
$pia_lang['Gen_Purge'] = 'Purge';
$pia_lang['Gen_Backup'] = 'Run Backup';
$pia_lang['Gen_Restore'] = 'Run Restore';
$pia_lang['Gen_Switch'] = 'Switch';
$pia_lang['Gen_Run'] = 'Run';
$pia_lang['Gen_Save'] = 'Save';
$pia_lang['Gen_Close'] = 'Close';
$pia_lang['Gen_on'] = 'On';
$pia_lang['Gen_off'] = 'Off';

//////////////////////////////////////////////////////////////////
// Login Page
//////////////////////////////////////////////////////////////////

$pia_lang['Login_Box'] = 'Enter your password';
$pia_lang['Login_Remember'] = 'Remember';
$pia_lang['Login_Remember_small'] = '(valid for 7 days)';
$pia_lang['Login_Submit'] = 'Log in';
$pia_lang['Login_Psw_run'] = 'To change password run:';
$pia_lang['Login_Psw_new'] = 'new_password';
$pia_lang['Login_Psw_folder'] = 'in the "~/pialert/back" folder.';
$pia_lang['Login_Psw_alert'] = 'Password Alert!';
$pia_lang['Login_Psw-box'] = 'Password';
$pia_lang['Login_Toggle_Info'] = 'Password Information';
$pia_lang['Login_Toggle_Info_headline'] = 'Password Information';
$pia_lang['Login_Toggle_Alert_headline'] = 'Password Alert!';

//////////////////////////////////////////////////////////////////
// Sidebar
//////////////////////////////////////////////////////////////////

$pia_lang['Navigation_Devices'] = 'Devices';
$pia_lang['Navigation_Services'] = 'Web Services';
$pia_lang['Navigation_Presence'] = 'Presence';
$pia_lang['Navigation_Events'] = 'Events';
$pia_lang['Navigation_Journal'] = 'Pi.Alert Journal';
$pia_lang['Navigation_Events_Dev'] = 'Devices';
$pia_lang['Navigation_Events_Serv'] = 'Web Services';
$pia_lang['Navigation_ICMPScan'] = 'ICMP Monitoring';
$pia_lang['Navigation_Maintenance'] = 'Settings';
$pia_lang['Navigation_Network'] = 'Network';
$pia_lang['Navigation_HelpFAQ'] = 'Help / FAQ';
$pia_lang['Navigation_UpdateCheck'] = 'Update Check';
$pia_lang['Navigation_Section_A'] = 'MAIN MENU';
$pia_lang['Navigation_Section_B'] = 'EVENTS & JOURNAL';
$pia_lang['Navigation_Section_C'] = 'SETTINGS & HELP';

//////////////////////////////////////////////////////////////////
// Device Page
//////////////////////////////////////////////////////////////////

$pia_lang['Device_Title'] = 'Devices';
$pia_lang['Device_Shortcut_AllDevices'] = 'All Devices';
$pia_lang['Device_Shortcut_Connected'] = 'Connected';
$pia_lang['Device_Shortcut_Favorites'] = 'Favorites';
$pia_lang['Device_Shortcut_NewDevices'] = 'New Devices';
$pia_lang['Device_Shortcut_DownAlerts'] = 'Down Alerts';
$pia_lang['Device_Shortcut_Archived'] = 'Archived';
$pia_lang['Device_Shortcut_Devices'] = 'Devices';
$pia_lang['Device_Shortcut_OnlineChart_a'] = 'Device presence over time (';
$pia_lang['Device_Shortcut_OnlineChart_b'] = 'h)';
$pia_lang['Device_TableHead_Name'] = 'Name';
$pia_lang['Device_TableHead_Owner'] = 'Owner';
$pia_lang['Device_TableHead_Type'] = 'Type';
$pia_lang['Device_TableHead_Favorite'] = 'Favorite';
$pia_lang['Device_TableHead_Group'] = 'Group';
$pia_lang['Device_TableHead_Location'] = 'Location';
$pia_lang['Device_TableHead_FirstSession'] = 'First Session';
$pia_lang['Device_TableHead_LastSession'] = 'Last Session';
$pia_lang['Device_TableHead_LastIP'] = 'Last IP';
$pia_lang['Device_TableHead_MAC'] = 'MAC';
$pia_lang['Device_TableHead_MACaddress'] = 'MAC-Address';
$pia_lang['Device_TableHead_LastIPOrder'] = 'Last IP Order';
$pia_lang['Device_TableHead_Rowid'] = 'Rowid';
$pia_lang['Device_TableHead_Status'] = 'Status';
$pia_lang['Device_TableHead_ConnectionType'] = 'Connection Type';
$pia_lang['Device_Searchbox'] = 'Search';
$pia_lang['Device_Tablelenght'] = 'Show _MENU_ entries';
$pia_lang['Device_Tablelenght_all'] = 'All';
$pia_lang['Device_Table_info'] = 'Showing _START_ to _END_ of _TOTAL_ entries';
$pia_lang['Device_Table_nav_next'] = 'Next';
$pia_lang['Device_Table_nav_prev'] = 'Previous';
$pia_lang['Device_bulkEditor_mode'] = 'Bulk Editor';
$pia_lang['Device_bulkEditor_mode_quit'] = 'Quit Editor';
$pia_lang['Device_bulkEditor_selectall'] = 'Select all Host&apos;s';
$pia_lang['Device_bulkEditor_selectnone'] = 'Deselect all Host&apos;s';
$pia_lang['Device_bulkEditor_savebox_title'] = 'Fields were saved';
$pia_lang['Device_bulkEditor_savebox_noselection'] = 'No fields were selected for modification';
$pia_lang['Device_bulkEditor_savebox_mod_devices'] = 'Modified devices';
$pia_lang['Device_bulkEditor_savebox_mod_fields'] = 'Changed values';
$pia_lang['Device_bulkEditor_hostbox_title'] = 'Select the devices to be edited';
$pia_lang['Device_bulkEditor_inputbox_title'] = 'Edit fields';
$pia_lang['Device_bulkDel_button'] = 'DELETE DEVICES';
$pia_lang['Device_bulkDel_info_head'] = 'Delete multiple devices';
$pia_lang['Device_bulkDel_info_text'] = 'With this function, you delete one or more devices from the database. If the devices are still in the network, they may be added back to the database, but without the individual settings.';
$pia_lang['Device_bulkDel_back_before'] = 'Number of devices before';
$pia_lang['Device_bulkDel_back_after'] = 'Current number of devices';
$pia_lang['Device_bulkDel_back_hosts'] = 'MAC addresses to be deleted';

//////////////////////////////////////////////////////////////////
// Presence Page
//////////////////////////////////////////////////////////////////

$pia_lang['Presence_Title'] = 'Presence by Device';
$pia_lang['Presence_Shortcut_AllDevices'] = 'All Devices';
$pia_lang['Presence_Shortcut_Connected'] = 'Connected';
$pia_lang['Presence_Shortcut_Favorites'] = 'Favorites';
$pia_lang['Presence_Shortcut_NewDevices'] = 'New Devices';
$pia_lang['Presence_Shortcut_DownAlerts'] = 'Down Alerts';
$pia_lang['Presence_Shortcut_Archived'] = 'Archived';
$pia_lang['Presence_Shortcut_Devices'] = 'Devices';

// Localizationfiles under pialert/front/lib/AdminLTE/bower_components/fullcalendar/dist/locale
$pia_lang['Presence_CallHead_Devices'] = 'Devices';
$pia_lang['Presence_CalHead_lang'] = 'en-us';
$pia_lang['Presence_CalHead_year'] = 'year';
$pia_lang['Presence_CalHead_quarter'] = 'quarter';
$pia_lang['Presence_CalHead_month'] = 'month';
$pia_lang['Presence_CalHead_week'] = 'week';
$pia_lang['Presence_CalHead_day'] = 'day';

//////////////////////////////////////////////////////////////////
// Events Page
//////////////////////////////////////////////////////////////////

$pia_lang['Events_Title'] = 'Events';
$pia_lang['Events_Periodselect_today'] = 'Today';
$pia_lang['Events_Periodselect_LastWeek'] = 'Last Week';
$pia_lang['Events_Periodselect_LastMonth'] = 'Last Month';
$pia_lang['Events_Periodselect_LastYear'] = 'Last Year';
$pia_lang['Events_Periodselect_All'] = 'All Info';
$pia_lang['Events_Shortcut_AllEvents'] = 'All Events';
$pia_lang['Events_Shortcut_Sessions'] = 'Sessions';
$pia_lang['Events_Shortcut_MissSessions'] = 'Missing Sessions';
$pia_lang['Events_Shortcut_VoidSessions'] = 'Voided Sessions';
$pia_lang['Events_Shortcut_NewDevices'] = 'New Devices';
$pia_lang['Events_Shortcut_DownAlerts'] = 'Down Alerts';
$pia_lang['Events_Shortcut_Events'] = 'Events';
$pia_lang['Events_TableHead_Order'] = 'Order';
$pia_lang['Events_TableHead_Device'] = 'Device';
$pia_lang['Events_TableHead_Owner'] = 'Owner';
$pia_lang['Events_TableHead_Date'] = 'Date';
$pia_lang['Events_TableHead_EventType'] = 'Event Type';
$pia_lang['Events_TableHead_Connection'] = 'Connection';
$pia_lang['Events_TableHead_Disconnection'] = 'Disconnection';
$pia_lang['Events_TableHead_Duration'] = 'Duration';
$pia_lang['Events_TableHead_DurationOrder'] = 'Duration Order';
$pia_lang['Events_TableHead_IP'] = 'IP';
$pia_lang['Events_TableHead_IPOrder'] = 'IP Order';
$pia_lang['Events_TableHead_AdditionalInfo'] = 'Additional Info';
$pia_lang['Events_Searchbox'] = 'Search';
$pia_lang['Events_Tablelenght'] = 'Show _MENU_ entries';
$pia_lang['Events_Tablelenght_all'] = 'All';
$pia_lang['Events_Table_info'] = 'Showing _START_ to _END_ of _TOTAL_ entries';
$pia_lang['Events_Table_nav_next'] = 'Next';
$pia_lang['Events_Table_nav_prev'] = 'Previous';

//////////////////////////////////////////////////////////////////
// WebServices Events Page
//////////////////////////////////////////////////////////////////

$pia_lang['WebServices_Events_Title'] = 'Web Services - Events';
$pia_lang['WebServices_Events_Shortcut_All'] = 'All Events';
$pia_lang['WebServices_Events_Shortcut_HTTP2xx'] = 'HTTP Code 2xx';
$pia_lang['WebServices_Events_Shortcut_HTTP3xx'] = 'HTTP Code 3xx';
$pia_lang['WebServices_Events_Shortcut_HTTP4xx'] = 'HTTP Code 4xx';
$pia_lang['WebServices_Events_Shortcut_HTTP5xx'] = 'HTTP Code 5xx';
$pia_lang['WebServices_Events_Shortcut_Down'] = 'Down';
$pia_lang['WebServices_Events_TableHead_URL'] = 'URL';
$pia_lang['WebServices_Events_TableHead_TargetIP'] = 'Dest. IP';
$pia_lang['WebServices_Events_TableHead_ScanTime'] = 'Scan Time';
$pia_lang['WebServices_Events_TableHead_StatusCode'] = 'Status Code';
$pia_lang['WebServices_Events_TableHead_ResponsTime'] = 'Response Time';

//////////////////////////////////////////////////////////////////
// Device Details Page
//////////////////////////////////////////////////////////////////

$pia_lang['DevDetail_Periodselect_today'] = 'Today';
$pia_lang['DevDetail_Periodselect_LastWeek'] = 'Last Week';
$pia_lang['DevDetail_Periodselect_LastMonth'] = 'Last Month';
$pia_lang['DevDetail_Periodselect_LastYear'] = 'Last Year';
$pia_lang['DevDetail_Periodselect_All'] = 'All Info';
$pia_lang['DevDetail_Shortcut_CurrentStatus'] = 'Current Status';
$pia_lang['DevDetail_Shortcut_Sessions'] = 'Sessions';
$pia_lang['DevDetail_Shortcut_Presence'] = 'Presence';
$pia_lang['DevDetail_Shortcut_DownAlerts'] = 'Down Alerts';
$pia_lang['DevDetail_Tab_Details'] = 'Details';
$pia_lang['DevDetail_Tab_Nmap'] = 'Tools';
$pia_lang['DevDetail_Tab_Sessions'] = 'Sessions';
$pia_lang['DevDetail_Tab_Presence'] = 'Presence';
$pia_lang['DevDetail_Tab_Events'] = 'Events';
$pia_lang['DevDetail_MainInfo_Title'] = 'Main Info';
$pia_lang['DevDetail_MainInfo_mac'] = 'MAC';
$pia_lang['DevDetail_MainInfo_Name'] = 'Name';
$pia_lang['DevDetail_MainInfo_Owner'] = 'Owner';
$pia_lang['DevDetail_MainInfo_Type'] = 'Type';
$pia_lang['DevDetail_MainInfo_Vendor'] = 'Vendor';
$pia_lang['DevDetail_MainInfo_Model'] = 'Model';
$pia_lang['DevDetail_MainInfo_Serialnumber'] = 'Serial';
$pia_lang['DevDetail_MainInfo_Favorite'] = 'Favorite';
$pia_lang['DevDetail_MainInfo_Group'] = 'Group';
$pia_lang['DevDetail_MainInfo_Location'] = 'Location';
$pia_lang['DevDetail_MainInfo_Comments'] = 'Comments';
$pia_lang['DevDetail_MainInfo_Network'] = 'Uplink Target';
$pia_lang['DevDetail_MainInfo_Network_Port'] = 'Target Port Number';
$pia_lang['DevDetail_MainInfo_Network_ConnectType'] = 'Connection Type';
$pia_lang['DevDetail_SessionInfo_Title'] = 'Session Info';
$pia_lang['DevDetail_SessionInfo_Status'] = 'Status';
$pia_lang['DevDetail_SessionInfo_FirstSession'] = 'First Session';
$pia_lang['DevDetail_SessionInfo_LastSession'] = 'Last Session';
$pia_lang['DevDetail_SessionInfo_LastIP'] = 'Last IP';
$pia_lang['DevDetail_SessionInfo_StaticIP'] = 'Static IP';
$pia_lang['DevDetail_Network_Titel'] = 'Network';
$pia_lang['DevDetail_EveandAl_Title'] = 'Events & Alerts config';
$pia_lang['DevDetail_EveandAl_ScanCycle'] = 'Scan Cycle';
$pia_lang['DevDetail_EveandAl_AlertAllEvents'] = 'Alert All Events';
$pia_lang['DevDetail_EveandAl_AlertDown'] = 'Alert Down';
$pia_lang['DevDetail_EveandAl_Skip'] = 'Skip repeated notifications during';
$pia_lang['DevDetail_EveandAl_NewDevice'] = 'New Device';
$pia_lang['DevDetail_EveandAl_Archived'] = 'Archived';
$pia_lang['DevDetail_EveandAl_RandomMAC'] = 'Random MAC';
$pia_lang['DevDetail_EveandAl_ScanCycle_a'] = 'Scan Device (1 min)';
$pia_lang['DevDetail_EveandAl_ScanCycle_z'] = 'Don&#39;t Scan Device (0 min)';
$pia_lang['DevDetail_button_Delete'] = 'Delete Device';
$pia_lang['DevDetail_button_Delete_Warning'] = 'Are you sure you want to delete this device?<br>(maybe you prefer to archive it)';
$pia_lang['DevDetail_button_DeleteEvents'] = 'Delete Events';
$pia_lang['DevDetail_button_DeleteEvents_Warning'] = 'Are you sure you want to delete all Events of this device?<br><br>This will clear the <b>Events history</b> and the <b>Sessions</b> and might help with constant (persistent) notifications';
$pia_lang['DevDetail_button_Reset'] = 'Reset Changes';
$pia_lang['DevDetail_button_Save'] = 'Save';
$pia_lang['DevDetail_SessionTable_Order'] = 'Order';
$pia_lang['DevDetail_SessionTable_Connection'] = 'Connection';
$pia_lang['DevDetail_SessionTable_Disconnection'] = 'Disconnection';
$pia_lang['DevDetail_SessionTable_Duration'] = 'Duration';
$pia_lang['DevDetail_SessionTable_IP'] = 'IP';
$pia_lang['DevDetail_SessionTable_Additionalinfo'] = 'Additional info';
$pia_lang['DevDetail_Events_CheckBox'] = 'Hide Connection Events';
$pia_lang['DevDetail_Tools_nmap_buttonFast'] = 'Fast Scan';
$pia_lang['DevDetail_Tools_nmap_buttonDefault'] = 'Default Scan';
$pia_lang['DevDetail_Tools_nmap_buttonDetail'] = 'Detailed Scan';
$pia_lang['DevDetail_Tools_nmap_buttonFast_text'] = 'Fast Scan: Scan fewer ports (100) than the default scan (a few seconds)';
$pia_lang['DevDetail_Tools_nmap_buttonDefault_text'] = 'Default Scan: Nmap scans the top 1,000 ports for each scan protocol requested. This catches roughly 93% of the TCP ports and 49% of the UDP ports. (about 5 seconds)';
$pia_lang['DevDetail_Tools_nmap_buttonDetail_text'] = 'Detailed Scan: Default scan with enabled OS detection, version detection, script scanning and traceroute (up to 30 seconds or more)';
$pia_lang['DevDetail_Tools_WOL'] = 'Send Wol command to ';
$pia_lang['DevDetail_Tools_WOL_noti'] = 'Wake-on-LAN';
$pia_lang['DevDetail_Tools_WOL_noti_text'] = 'The Wake-on-LAN command is sent to the broadcast address. If the target is not in the subnet/vlan of Pi.Alert, the target device will not respond.';
$pia_lang['BackDevDetail_Tools_WOL_okay'] = 'The command was executed';
$pia_lang['BackDevDetail_Tools_WOL_error'] = 'The command was not executed';

//////////////////////////////////////////////////////////////////
// WebServices Details Page
//////////////////////////////////////////////////////////////////

$pia_lang['WebServices_Title'] = 'Web Services';
$pia_lang['WebServices_unknown_Device'] = 'Unknown Device';
$pia_lang['WebServices_tablehead_TargetIP'] = 'Dest. IP';
$pia_lang['WebServices_tablehead_ScanTime'] = 'Scan Time';
$pia_lang['WebServices_tablehead_Status_Code'] = 'HTTP Status';
$pia_lang['WebServices_tablehead_Response_Time'] = 'Response Time';
$pia_lang['WebServices_Tab_Graph'] = 'Stats';
$pia_lang['WebServices_label_URL'] = 'URL';
$pia_lang['WebServices_label_Tags'] = 'Tag';
$pia_lang['WebServices_label_MAC'] = 'Device';
$pia_lang['WebServices_label_MAC_Select'] = 'Select';
$pia_lang['WebServices_label_Notes'] = 'Note';
$pia_lang['WebServices_label_TargetIP'] = 'Dest. IP';
$pia_lang['WebServices_label_StatusCode'] = 'HTTP Status';
$pia_lang['WebServices_label_ScanTime'] = 'Scan Time';
$pia_lang['WebServices_label_Response_Time'] = 'Response Time';
$pia_lang['WebServices_label_AlertEvents'] = 'All Events';
$pia_lang['WebServices_label_AlertDown'] = 'Down Events';
$pia_lang['WebServices_label_Notification'] = 'Notify';
$pia_lang['WebServices_Chart_a'] = 'Service Status over the last';
$pia_lang['WebServices_Chart_b'] = 'hours';
$pia_lang['WebServices_Page_down'] = 'Offline';
$pia_lang['WebServices_button_Delete_label'] = 'Delete Service';
$pia_lang['WebServices_button_Delete_Warning'] = 'Are you sure you want to delete this web service?';
$pia_lang['WebServices_headline_NewService'] = 'New Web Service';
$pia_lang['WebServices_Events_all'] = 'All Events';
$pia_lang['WebServices_Events_down'] = 'Down';
$pia_lang['WebServices_Events_none'] = 'none';
$pia_lang['WebServices_BoxTitle_General'] = 'General';
$pia_lang['WebServices_Stats_Code'] = 'Page Status';
$pia_lang['WebServices_Stats_Time'] = 'Response Time';
$pia_lang['WebServices_Stats_Time_min'] = 'Fastest';
$pia_lang['WebServices_Stats_Time_max'] = 'Slowest';
$pia_lang['WebServices_Stats_Location'] = 'Location';
$pia_lang['WebServices_Stats_IP'] = 'Current IP';
$pia_lang['WebServices_Stats_IPLocation'] = 'Country Mapping';
$pia_lang['WebServices_Stats_comment_a'] = '(Calculated from all scans of this service.)';

//////////////////////////////////////////////////////////////////
// WebServices Details Page - Backend
//////////////////////////////////////////////////////////////////

$pia_lang['BackWebServices_UpdServ'] = 'The web service was updated';
$pia_lang['BackWebServices_UpdServError'] = 'The web service was not updated';
$pia_lang['BackWebServices_InsServ'] = 'The web service was saved';
$pia_lang['BackWebServices_InsServError'] = 'The web service was not saved';
$pia_lang['BackWebServices_DelServ'] = 'The web service was deleted from the database';
$pia_lang['BackWebServices_DelServError'] = 'The web service could not be deleted, or could not be deleted completely';

//////////////////////////////////////////////////////////////////
// ICMP Monitoring Page
//////////////////////////////////////////////////////////////////

$pia_lang['ICMPMonitor_Title'] = 'ICMP Monitor';
$pia_lang['ICMPMonitor_headline_IP'] = 'Monitor New IP Address';
$pia_lang['ICMPMonitor_label_IP'] = 'Host IP';
$pia_lang['ICMPMonitor_label_Hostname'] = 'Hostname';
$pia_lang['ICMPMonitor_label_RTT'] = 'avg. RTT';
$pia_lang['BackICMP_mon_disabled'] = 'ICMP monitoring is disabled';
$pia_lang['BackICMP_mon_enabled'] = 'ICMP monitoring is enabled';
$pia_lang['BackICMP_mon_InsICMP'] = 'The ICMP host has been saved';
$pia_lang['BackICMP_mon_InsICMPError'] = 'The ICMP host was not saved';
$pia_lang['BackICMP_mon_DelICMP'] = 'The ICMP host has been deleted from the database';
$pia_lang['BackICMP_mon_DelICMPError'] = 'The ICMP host could not be deleted, or not fully deleted';
$pia_lang['BackICMP_mon_UpdICMP'] = 'The ICMP host has been updated';
$pia_lang['BackICMP_mon_UpdICMPError'] = 'The ICMP host could not be updated';
$pia_lang['ICMPMonitor_Shortcut_Online'] = 'Online';
$pia_lang['ICMPMonitor_Shortcut_Offline'] = 'Offline/Down';
$pia_lang['ICMPMonitor_Availability'] = 'Availability';

//////////////////////////////////////////////////////////////////
// Maintenance Page
//////////////////////////////////////////////////////////////////

$pia_lang['Maintenance_Title'] = 'Settings and Maintenance';
$pia_lang['Maintenance_database_path'] = 'Database-Path';
$pia_lang['Maintenance_database_size'] = 'Database-Size';
$pia_lang['Maintenance_database_lastmod'] = 'Last Update (DB)';
$pia_lang['Maintenance_database_backup'] = 'DB Backups';
$pia_lang['Maintenance_config_backup'] = 'Config Backups';
$pia_lang['Maintenance_database_backup_found'] = 'backups were found';
$pia_lang['Maintenance_database_backup_total'] = 'total disk usage';
$pia_lang['Maintenance_arp_status'] = 'Scan Status';
$pia_lang['Maintenance_arp_status_off'] = 'is currently disabled';
$pia_lang['Maintenance_arp_status_on'] = 'scan(s) currently running';
$pia_lang['Maintenance_notification_config'] = 'Notifications (Devices, ICMP)';
$pia_lang['Maintenance_notification_config_webmon'] = 'Notifications (Service Mon.)';
$pia_lang['Maintenance_Tools_Logviewer_Scan'] = 'Scan';
$pia_lang['Maintenance_Tools_Logviewer_Scan_empty'] = 'It is probably currently running scan';
$pia_lang['Maintenance_Tools_Logviewer_IPLog'] = 'Internet IP';
$pia_lang['Maintenance_Tools_Logviewer_IPLog_empty'] = 'No log available';
$pia_lang['Maintenance_Tools_Logviewer_Vendor'] = 'Vendor Update';
$pia_lang['Maintenance_Tools_Logviewer_Vendor_empty'] = 'No log available';
$pia_lang['Maintenance_Tools_Logviewer_Cleanup'] = 'Cleanup';
$pia_lang['Maintenance_Tools_Logviewer_Cleanup_empty'] = 'No log available';
$pia_lang['Maintenance_Tools_Logviewer_Nmap'] = 'Nmap (Session)';
$pia_lang['Maintenance_Tools_Logviewer_Nmap_empty'] = 'No log available';
$pia_lang['Maintenance_Tools_Logviewer_WebServices'] = 'Web Services';
$pia_lang['Maintenance_Tools_Logviewer_WebServices_empty'] = 'No log available';
$pia_lang['Maintenance_Tools_Logviewer_WOL'] = 'Wake-on-LAN (Session)';
$pia_lang['Maintenance_Tools_Logviewer_WOL_empty'] = 'No log available';
$pia_lang['Maintenance_themeselector_lable'] = 'Select Skin';
$pia_lang['Maintenance_themeselector_empty'] = 'Choose a Skin';
$pia_lang['Maintenance_themeselector_apply'] = 'Apply';
$pia_lang['Maintenance_lang_selector_lable'] = 'Select Language';
$pia_lang['Maintenance_lang_selector_empty'] = 'Choose Language';
$pia_lang['Maintenance_lang_en_us'] = 'English (US)';
$pia_lang['Maintenance_lang_de_de'] = 'German (DE)';
$pia_lang['Maintenance_lang_es_es'] = 'Spanish (ES)';
$pia_lang['Maintenance_lang_fr_fr'] = 'French (FR)';
$pia_lang['Maintenance_lang_it_it'] = 'Italian (IT)';
$pia_lang['Maintenance_lang_selector_apply'] = 'Apply';
$pia_lang['Maintenance_Tools_Tab_Settings'] = 'Settings';
$pia_lang['Maintenance_Tools_Tab_Settings_Intro'] = 'The options listed here cause server-side configuration changes. These changes affect all devices that access this page.';
$pia_lang['Maintenance_Tools_Tab_GUI'] = 'GUI';
$pia_lang['Maintenance_Tools_Tab_Tools'] = 'Maintenance';
$pia_lang['Maintenance_Tools_Tab_BackupRestore'] = 'Data backup';
$pia_lang['Maintenance_Tools_Tab_Subheadline_a'] = 'General';
$pia_lang['Maintenance_Tools_Tab_Subheadline_b'] = 'Configure device overview';
$pia_lang['Maintenance_Tools_Tab_Subheadline_c'] = 'Security';
$pia_lang['Maintenance_Tools_Tab_Subheadline_d'] = 'Scans';
$pia_lang['Maintenance_Tools_Tab_Subheadline_e'] = 'FavIcon';
$pia_lang['Maintenance_Tools_Tab_Subheadline_e_Intro'] = 'You can enter your desired FavIcon URL in the field, or choose between local or remote (https://github.com/leiweibau/Pi.Alert) FavIcons. Depending on the configuration, local FavIcons may not work as a HomeScreen icon.';
$pia_lang['Maintenance_Tool_darkmode'] = 'Dark Mode';
$pia_lang['Maintenance_Tool_darkmode_noti'] = 'Toggle Modes';
$pia_lang['Maintenance_Tool_darkmode_noti_text'] = 'After the theme switch, the page tries to reload itself to activate the change. If necessary, the cache must be cleared.';
$pia_lang['Maintenance_Tool_onlinehistorygraph'] = 'History Graph';
$pia_lang['Maintenance_Tool_onlinehistorygraph_noti'] = 'Graph display';
$pia_lang['Maintenance_Tool_onlinehistorygraph_noti_text'] = 'Enables/disables the graph to display the online/offline history over the last 12h.';
$pia_lang['Maintenance_Tool_webservicemon'] = 'Web Service Mon.';
$pia_lang['Maintenance_Tool_webservicemon_noti'] = 'Web Service Monitoring';
$pia_lang['Maintenance_Tool_webservicemon_noti_text'] = 'Enables or disables the web service monitoring for Pi.Alert. With activation, additional elements are displayed in the interface. With deactivation, they disappear again.';
$pia_lang['Maintenance_Tool_icmpmon'] = 'ICMP Monitoring';
$pia_lang['Maintenance_Tool_icmpmon_noti'] = 'ICMP Monitoring';
$pia_lang['Maintenance_Tool_icmpmon_noti_text'] = 'Enables or disables ICMP monitoring for Pi.Alert. Enabling this will display additional elements in the interface. Disabling it will make these elements disappear.';
$pia_lang['Maintenance_Tool_mainscan'] = 'Main Scan (arp)';
$pia_lang['Maintenance_Tool_mainscan_noti'] = 'Main Scan';
$pia_lang['Maintenance_Tool_mainscan_noti_text'] = 'Enables or disables the main scanning method for Pi.Alert (ARP scan). The collection of data from configured external sources remains active.';
$pia_lang['Maintenance_Tool_DevListCol_noti'] = 'Configure device overview';
$pia_lang['Maintenance_Tool_DevListCol_noti_text'] = 'Do you want to save the changes you have made? This change will affect all devices you use to access this page.';
$pia_lang['Maintenance_Tool_setapikey_false'] = 'No API-Key set';
$pia_lang['Maintenance_Tool_setapikey'] = 'Set API-Key';
$pia_lang['Maintenance_Tool_setapikey_text'] = 'With the API key it is possible to make queries to the database without using the web page. This can be the case if another service should access the data of this database. If an API key already exists, it will be replaced.';
$pia_lang['Maintenance_Tool_setapikey_noti'] = 'Setting API-Key';
$pia_lang['Maintenance_Tool_setapikey_noti_text'] = 'Do you want to replace an existing API key with a new key? Existing keys are then no longer usable.';
$pia_lang['Maintenance_Tool_ignorelist'] = 'Ignore list';
$pia_lang['Maintenance_Tool_ignorelist_false'] = 'No ignore list configured';
$pia_lang['Maintenance_arpscantimer_empty'] = 'Set period';
$pia_lang['Maintenance_Tool_arpscansw'] = 'Toggle Scans (on/off)';
$pia_lang['Maintenance_Tool_arpscansw_text'] = 'Switches all scans of Pi.Alert on or off. If no time period has been defined for timer activation, Pi.Alert switches on again after a pause of 10 min with the next pending scan. Scans that are already running will not be terminated.';
$pia_lang['Maintenance_Tool_arpscansw_noti'] = 'Toggle arp-Scan on or off';
$pia_lang['Maintenance_Tool_arpscansw_noti_text'] = 'When the scan has been switched off it remains off until it is activated again.';
$pia_lang['Maintenance_Tool_test_notification'] = 'Test Notifications';
$pia_lang['Maintenance_Tool_test_notification_text'] = 'Test reporting for all activated services.';
$pia_lang['Maintenance_Tool_test_notification_noti'] = 'Notifications test';
$pia_lang['Maintenance_Tool_test_notification_noti_text'] = 'Should a test notification be performed across all configured and enabled notification services?';
$pia_lang['Maintenance_Tool_del_alldev'] = 'Delete all Devices';
$pia_lang['Maintenance_Tool_del_alldev_text'] = 'All devices will be deleted from the database.';
$pia_lang['Maintenance_Tool_del_alldev_noti'] = 'Delete Devices';
$pia_lang['Maintenance_Tool_del_alldev_noti_text'] = 'Are you sure you want to delete all devices?';
$pia_lang['Maintenance_Tool_del_unknowndev'] = 'Delete (unknown) Devices';
$pia_lang['Maintenance_Tool_del_unknowndev_text'] = 'All devices named (unknown) will be deleted from the database.';
$pia_lang['Maintenance_Tool_del_unknowndev_noti'] = 'Delete (unknown) Devices';
$pia_lang['Maintenance_Tool_del_unknowndev_noti_text'] = 'Are you sure you want to delete all (unknown) devices?';
$pia_lang['Maintenance_Tool_del_allevents'] = 'Delete Events (Reset Presence)';
$pia_lang['Maintenance_Tool_del_allevents_text'] = 'All events in the database will be deleted. At that moment the presence of all devices will be reset. This can lead to invalid sessions. This means that devices are displayed as "present" although they are offline. A scan while the device in question is online solves the problem.';
$pia_lang['Maintenance_Tool_del_allevents_noti'] = 'Delete Events';
$pia_lang['Maintenance_Tool_del_allevents_noti_text'] = 'Are you sure you want to delete all Events? This resets Presence of all Devices.';
$pia_lang['Maintenance_Tool_del_Inactive_Hosts'] = 'Delete inactive devices';
$pia_lang['Maintenance_Tool_del_Inactive_Hosts_text'] = 'All devices that have been inactive for 30 days will be deleted. The events of these devices are also removed from the database.';
$pia_lang['Maintenance_Tool_backup'] = 'DB Backup';
$pia_lang['Maintenance_Tool_backup_text'] = 'The database backups are located in the database directory as a zip-archive, named with the creation date. There is no maximum number of backups.';
$pia_lang['Maintenance_Tool_backup_noti'] = 'DB Backup';
$pia_lang['Maintenance_Tool_backup_noti_text'] = 'Are you sure you want to execute the the DB Backup? Be sure that no scan is currently running.';
$pia_lang['Maintenance_Tool_backupcsv'] = 'Backup Devices/Services to<br>CSV File';
$pia_lang['Maintenance_Tool_backupcsv_text'] = 'Devices, Web Services, and devices from ICMP monitoring will be exported to CSV files and saved as zip archives. This serves as an emergency solution in case everything needs to be re-entered due to a defective database. Importing is not possible at the moment.';
$pia_lang['Maintenance_Tool_backupcsv_noti'] = 'Backup Devices/Services to CSV File';
$pia_lang['Maintenance_Tool_backupcsv_noti_text'] = 'Are you sure you want to export the devices and web services lists as CSV files now?';
$pia_lang['Maintenance_Tool_restore'] = 'DB Restore';
$pia_lang['Maintenance_Tool_restore_blocked'] = 'not available';
$pia_lang['Maintenance_Tool_restore_text'] = 'The latest backup can be restored via the button, but older backups can only be restored manually. After the restore, make an integrity check on the database for safety, in case the db was currently in write access when the backup was created.';
$pia_lang['Maintenance_Tool_restore_noti'] = 'DB Restore';
$pia_lang['Maintenance_Tool_restore_noti_text'] = 'Are you sure you want to execute the the DB Restore? Be sure that no scan is currently running.';
$pia_lang['Maintenance_Tool_latestdb_download'] = 'Download latest DB Backup';
$pia_lang['Maintenance_Tool_latestconf_download'] = 'Download current pialert.conf';
$pia_lang['Maintenance_Tool_CSVExport_download'] = 'Download CSV Export';
$pia_lang['Maintenance_Tool_purgebackup'] = 'Purge Backups';
$pia_lang['Maintenance_Tool_purgebackup_text'] = 'All other backups will be deleted except for the last 3 backups.';
$pia_lang['Maintenance_Tool_purgebackup_noti'] = 'Purge Backups';
$pia_lang['Maintenance_Tool_purgebackup_noti_text'] = 'Are you sure you want to delete all backups except the last 3?';
$pia_lang['Maintenance_Tool_del_ActHistory'] = 'Deleting the network activity';
$pia_lang['Maintenance_Tool_del_ActHistory_text'] = 'The network activity graph is reset. This does not affect the events.';
$pia_lang['Maintenance_Tool_del_ActHistory_noti'] = 'Delete network activity';
$pia_lang['Maintenance_Tool_del_ActHistory_noti_text'] = 'Are you sure you want to reset the network activity?';
$pia_lang['Maintenance_Tool_loginenable'] = 'Enable Login';
$pia_lang['Maintenance_Tool_loginenable_text'] = 'After activation, you will automatically be taken to the login page. If you have not set a password before, "123456" is the default password.';
$pia_lang['Maintenance_Tool_loginenable_noti'] = 'Enable Login';
$pia_lang['Maintenance_Tool_loginenable_noti_text'] = 'Are you sure you want to activate the login?';
$pia_lang['Maintenance_Tool_logindisable'] = 'Disable Login';
$pia_lang['Maintenance_Tool_logindisable_text'] = 'The login will be deactivated. Every user now has the possibility to access the Pi.Alert page again.';
$pia_lang['Maintenance_Tool_logindisable_noti'] = 'Disable Login';
$pia_lang['Maintenance_Tool_logindisable_noti_text'] = 'Are you sure you want to deactivate the login?';
$pia_lang['Maintenance_ConfEditor_Start'] = 'Edit Pi.Alert Config';
$pia_lang['Maintenance_ConfEditor_Hint'] = 'Notes to the editor';
$pia_lang['Maintenance_ConfEditor_Backup'] = 'Create Backup';
$pia_lang['Maintenance_ConfEditor_Restore'] = 'Prev. Version';
$pia_lang['Maintenance_ConfEditor_Restore_noti'] = 'To previous version';
$pia_lang['Maintenance_ConfEditor_Restore_noti_text'] = 'The last version of the configuration file is restored. The "previous version" is either that of the last backup, or the version before the last save. Depending on which is more current.';
$pia_lang['Maintenance_ConfEditor_Backup_info'] = 'In addition to the "pialert-prev.bak" file, another backup is created with the current date incl. time. This backup must be restored manually if necessary (rename to "pialert.conf"). An existing "pialert-prev.bak" file is always overwritten.';
$pia_lang['Maintenance_ConfEditor_Restore_info'] = 'This function restores the configuration file from the "pialert-prev.bak" file. The file "pialert-prev.bak" is created when saving and creating a backup.';
$pia_lang['Maintenance_ConfEditor_Save_info'] = 'The content of the text field with any changes is saved to the "pialert.conf" file. The previous configuration is saved as "pialert-prev.bak". With each save the file "pialert-prev.bak" is overwritten again and again with the previous configuration file.';
$pia_lang['Maintenance_Tool_ConfBackup'] = 'Create Config Backup';
$pia_lang['Maintenance_Tool_ConfBackup_text'] = 'In addition to the file "pialert-prev.bak," another backup is created with the current date and time. This backup must be manually restored if needed (rename it to "pialert.conf").';
$pia_lang['FavIcon_color_white'] = 'white';
$pia_lang['FavIcon_color_black'] = 'black';
$pia_lang['FavIcon_color_red'] = 'red';
$pia_lang['FavIcon_color_blue'] = 'blue';
$pia_lang['FavIcon_color_green'] = 'green';
$pia_lang['FavIcon_color_yellow'] = 'yellow';
$pia_lang['FavIcon_color_purple'] = 'purple';
$pia_lang['FavIcon_logo_white'] = 'white Logo';
$pia_lang['FavIcon_logo_black'] = 'black Logo';
$pia_lang['FavIcon_mode_glass'] = 'glass';
$pia_lang['FavIcon_mode_flat'] = 'flat';
$pia_lang['FavIcon_local'] = 'local';
$pia_lang['FavIcon_remote'] = 'remote';

//////////////////////////////////////////////////////////////////
// Maintenance Page (Backend)
//////////////////////////////////////////////////////////////////

$pia_lang['BackDevices_Arpscan_disabled'] = 'Arp-Scan Disabled';
$pia_lang['BackDevices_Arpscan_enabled'] = 'Arp-Scan Enabled';
$pia_lang['BackDevices_setapikey'] = 'API-Key set';
$pia_lang['BackDevices_test_notification'] = 'Notification sent';
$pia_lang['BackDevices_darkmode_disabled'] = 'Darkmode Disabled';
$pia_lang['BackDevices_darkmode_enabled'] = 'Darkmode Enabled';
$pia_lang['BackDevices_webservicemon_disabled'] = 'Web Service Monitoring Disabled';
$pia_lang['BackDevices_webservicemon_enabled'] = 'Web Service Monitoring Enabled';
$pia_lang['BackDevices_MainScan_disabled'] = 'Main Scan is disabled';
$pia_lang['BackDevices_MainScan_enabled'] = 'Main Scan is enabled';
$pia_lang['BackDevices_onlinehistorygraph_disabled'] = 'Graph disabled.';
$pia_lang['BackDevices_onlinehistorygraph_enabled'] = 'Graph enabled.';
$pia_lang['BackDevices_Restore_CopError'] = 'The original database could not be saved.';
$pia_lang['BackDevices_Restore_okay'] = 'Restore executed successfully.';
$pia_lang['BackDevices_Restore_Failed'] = 'Restore Failed. Please restore the backup manually.';
$pia_lang['BackDevices_Backup_WALError'] = 'The original database was not copied because transactions may be open.';
$pia_lang['BackDevices_Backup_IntegrityError'] = 'The copied database has not passed the integrity check.';
$pia_lang['BackDevices_Backup_CopError'] = 'The original database could not be saved.';
$pia_lang['BackDevices_Backup_okay'] = 'The backup executed successfully with the new archive';
$pia_lang['BackDevices_Backup_Failed'] = 'The backup executed partially successfully. The archive could not be created or is empty.';
$pia_lang['BackDevices_BackupCSV_FailedExport'] = 'One or more CSV files could not be created.';
$pia_lang['BackDevices_BackupCSV_FailedZip'] = 'The archive with the CSV files could not be created.';
$pia_lang['BackDevices_BackupCSV_okay'] = 'The archive with the CSV files was successfully created.';
$pia_lang['BackDevices_DBTools_DelDev_a'] = 'Device deleted successfully';
$pia_lang['BackDevices_DBTools_DelDev_b'] = 'Devices deleted successfully';
$pia_lang['BackDevices_DBTools_DelEvents'] = 'Events deleted successfully';
$pia_lang['BackDevices_DBTools_DelEventsError'] = 'Error deleting Events';
$pia_lang['BackDevices_DBTools_DelDevError_a'] = 'Error deleting Device';
$pia_lang['BackDevices_DBTools_DelDevError_b'] = 'Error deleting Devices';
$pia_lang['BackDevices_DBTools_UpdDev'] = 'Device updated successfully';
$pia_lang['BackDevices_DBTools_UpdDevError'] = 'Error updating device';
$pia_lang['BackDevices_DBTools_Purge'] = 'The oldest backups were deleted';
$pia_lang['BackDevices_DBTools_DelActHistory'] = 'Network aktivity reset successful';
$pia_lang['BackDevices_DBTools_DelActHistoryError'] = 'Network aktivity reset not successful';
$pia_lang['BackDevices_DBTools_DelInactHosts'] = 'Inactive hosts have been deleted.';
$pia_lang['BackDevices_DBTools_DelInactHostsError'] = 'An error occurred when trying to delete inactive hosts.';
$pia_lang['BackDevices_Login_disabled'] = 'Login disabled.';
$pia_lang['BackDevices_Login_enabled'] = 'Login enabled.';
$pia_lang['BackDevices_Theme_set'] = 'Theme applied';
$pia_lang['BackDevices_Theme_notset'] = 'Theme not applied';
$pia_lang['BackDevices_Theme_invalid'] = 'No theme selected';
$pia_lang['BackDevices_Language_set'] = 'Language applied';
$pia_lang['BackDevices_Language_notset'] = 'Language not applied';
$pia_lang['BackDevices_Language_invalid'] = 'No language selected';
$pia_lang['BackDevices_DevListCol_noti_text'] = 'Table setting is saved.';
$pia_lang['BackDevices_ConfEditor_CopError'] = 'It is not possible to save the configuration.';
$pia_lang['BackDevices_ConfEditor_CopOkay'] = 'The configuration backup is completed.';
$pia_lang['BackDevices_ConfEditor_RestoreError'] = 'The last version of the configuration could not be restored.';
$pia_lang['BackDevices_ConfEditor_RestoreOkay'] = 'The last version of the configuration has been restored.';
$pia_lang['BackDevices_Report_Delete'] = ' Notification(s) has been deleted.';
$pia_lang['BackFiles_FavIcon_invalidURL'] = 'An invalid URL has been entered.';
$pia_lang['BackFiles_FavIcon_ErrorURL'] = 'A manually entered FavIcon must be a URL. Path references for a local file will not be saved.';
$pia_lang['BackFiles_FavIcon_okay'] = 'The new FavIcon has been saved.';

//////////////////////////////////////////////////////////////////
// Network Page
//////////////////////////////////////////////////////////////////

$pia_lang['Network_Title'] = 'Network Overview';
$pia_lang['Network_ManageDevices'] = 'Manage Devices';
$pia_lang['Network_ManageAdd_Name'] = 'Device Name';
$pia_lang['Network_ManageAdd_Name_text'] = 'Name without special characters';
$pia_lang['Network_ManageAdd_Type'] = 'Device Type';
$pia_lang['Network_ManageAdd_Type_text'] = '-- Select Type --';
$pia_lang['Network_ManageAdd_Port'] = 'Port Count';
$pia_lang['Network_ManageAdd_Port_text'] = 'leave blank for wifi and powerline';
$pia_lang['Network_ManageAdd_Submit'] = 'Add Device';
$pia_lang['Network_ManageEdit_ID'] = 'Device to update';
$pia_lang['Network_ManageEdit_ID_text'] = '-- Select Device for editing --';
$pia_lang['Network_ManageEdit_Name'] = 'New Device Name';
$pia_lang['Network_ManageEdit_Name_text'] = 'Name without special characters';
$pia_lang['Network_ManageEdit_Type'] = 'New Device Type';
$pia_lang['Network_ManageEdit_Type_text'] = '-- Select Type --';
$pia_lang['Network_ManageEdit_Port'] = ' New Port Count';
$pia_lang['Network_ManageEdit_Port_text'] = 'leave blank for wifi and powerline';
$pia_lang['Network_ManageEdit_Downlink'] = 'manual Port-Configuration (Target-MAC,locale Port)';
$pia_lang['Network_ManageEdit_Downlink_text'] = '0a:1b:3c:4d:5e:6f,16;Target-MAC 2,Port 2;';
$pia_lang['Network_ManageEdit_Submit'] = 'Save Changes';
$pia_lang['Network_ManageDel_Name'] = 'Device to delete';
$pia_lang['Network_ManageDel_Name_text'] = '-- Select Device --';
$pia_lang['Network_ManageDel_Submit'] = 'Delete Device';
$pia_lang['Network_Table_State'] = 'State';
$pia_lang['Network_Table_Hostname'] = 'Hostname';
$pia_lang['Network_Table_IP'] = 'IP';
$pia_lang['Network_UnassignedDevices'] = 'Unassigned devices';
$pia_lang['NetworkSettings_Title'] = 'Settings - Network Overview';
$pia_lang['Network_ManageDevices_Intro'] = 'This section is used for creating transparent/IP-less (unmanaged) devices such as hubs or switches, as well as active devices like routers, managed switches, or access points that influence the network structure. Devices already detected by Pi.Alert must be recreated here for display. Editing or deleting devices already detected by Pi.Alert is independent of the "<span class="text-maroon help_faq_code">' . $pia_lang['Navigation_Devices'] . '</span>" list and only has significance for the "<span class="text-maroon help_faq_code">' . $pia_lang['Navigation_Network'] . '</span>" page.';
$pia_lang['Network_Unmanaged_Devices'] = 'Manage Non-Scannable Devices';
$pia_lang['Network_Unmanaged_Devices_Intro'] = 'At this location, end devices that occupy ports or connections on switches or routers but do not have network functionality themselves can be created. Devices that cannot be detected but should be displayed for an overview can also be taken into account here. The devices created here only appear on the "<span class="text-maroon help_faq_code">' . $pia_lang['Navigation_Network'] . '</span>" page.';
$pia_lang['Network_Unmanaged_Devices_Connected'] = 'Connected to';
$pia_lang['Network_Unmanaged_Devices_Connected_text'] = 'Select Device';
$pia_lang['Network_Unmanaged_Devices_Port'] = 'On Port';
$pia_lang['Network_Unmanaged_Devices_Port_text'] = 'On Port number on the selected device';

//////////////////////////////////////////////////////////////////
// Reports Page
//////////////////////////////////////////////////////////////////

$pia_lang['Reports_Title'] = 'Notifications';
$pia_lang['Reports_delete_all'] = 'Delete all notifications';
$pia_lang['Reports_delete_all_noti'] = 'Delete all notifications';
$pia_lang['Reports_delete_all_noti_text'] = 'All notifications are deleted. The counter is being reset with this.';
$pia_lang['Reports_Rogue_hint'] = 'A new message about an unknown DHCP server will only be displayed after this message has been deleted.';

//////////////////////////////////////////////////////////////////
// UpdateCheck
//////////////////////////////////////////////////////////////////

$pia_lang['Maintenance_Tools_Updatecheck'] = 'Check for Updates';
$pia_lang['Maintenance_Github_package_a'] = 'Latest Version: ';
$pia_lang['Maintenance_Github_package_b'] = 'o\'clock';
$pia_lang['Updatecheck_Title'] = 'Update Check';
$pia_lang['Updatecheck_cur'] = 'Currently used Pi.Alert Version';
$pia_lang['Updatecheck_new'] = 'Latest Pi.Alert Version on Github';
$pia_lang['GeoLiteDB_Title'] = 'GeoLite2 DB Check';
$pia_lang['GeoLiteDB_cur'] = 'GeoLite2 DB loaded on';
$pia_lang['GeoLiteDB_new'] = 'Latest GeoLite2 DB on Github';
$pia_lang['Updatecheck_RN'] = 'Pi.Alert Update Notes';
$pia_lang['Updatecheck_RN2'] = 'Pi.Alert Update Check';
$pia_lang['Updatecheck_U2D'] = 'You are already using the latest version';
$pia_lang['GeoLiteDB_button_del'] = 'Delete GeoLite2 Database';
$pia_lang['GeoLiteDB_button_ins'] = 'Install GeoLite2 Database';
$pia_lang['GeoLiteDB_button_upd'] = 'Update GeoLite2 Database';
$pia_lang['GeoLiteDB_credits'] = 'The database is downloaded from <a href="https://github.com/P3TERX/GeoLite.mmdb" target="_blank">github.com/P3TERX/GeoLite.mmdb</a>. The GeoLite2 database is a product of <a href="https://dev.maxmind.com/geoip/geolite2-free-geolocation-data" target="_blank">MaxMind</a>.';
$pia_lang['GeoLiteDB_Installnotes'] = 'You can install the database via the ' . $pia_lang['WebServices_Tab_Graph'] . ' tab on the details page of any web service.';
$pia_lang['GeoLiteDB_absent'] = 'DB not installed';

//////////////////////////////////////////////////////////////////
// System Info Page
//////////////////////////////////////////////////////////////////

$pia_lang['SysInfo_storage_note'] = 'It is possible that the memory usage cannot be determined for all drives listed above. This depends on the user under which the drives are mounted.';

//////////////////////////////////////////////////////////////////
// Speedtest
//////////////////////////////////////////////////////////////////

$pia_lang['ookla_postinstall_note'] = 'Before you can use the Ookla Speedtest client, you need to execute the command "sudo ./speedtest" once in the directory "$HOME/pialert/back/speedtest/." The Speedtest button will be enabled after reloading the page, but it will only work after accepting the Ookla license.';
$pia_lang['ookla_devdetails_tab_title'] = 'Speedtest History';
$pia_lang['ookla_devdetails_required'] = 'The history of Speedtest results is currently supported only with the official Speedtest by Ookla (<a href="https://www.speedtest.net/apps/cli" target="blank">speedtest.net</a>).';
$pia_lang['ookla_devdetails_tab_headline'] = 'Speedtest History';
$pia_lang['ookla_devdetails_table_time'] = 'Date';
$pia_lang['ookla_devdetails_table_isp'] = 'ISP';
$pia_lang['ookla_devdetails_table_server'] = 'Server';
$pia_lang['ookla_devdetails_table_ping'] = 'Ping';
$pia_lang['ookla_devdetails_table_down'] = 'Download';
$pia_lang['ookla_devdetails_table_up'] = 'Upload';

// =============================================================================================================

$pia_journ_lang['Journal_TableHead_Class'] = 'Method';
$pia_journ_lang['Journal_TableHead_Trigger'] = 'Trigger';

$pia_journ_lang['Title'] = 'Application Journal';
$pia_journ_lang['File_hash'] = 'pialert.conf - Hash';
// Frontend
$pia_journ_lang['a_000'] = 'Configuration File';
$pia_journ_lang['a_001'] = 'GUI Access';
$pia_journ_lang['a_002'] = 'Pi.Alert Scans';
$pia_journ_lang['a_005'] = 'GUI Configuration';
$pia_journ_lang['a_010'] = 'DB Maintenance';
$pia_journ_lang['a_020'] = 'Devices';
$pia_journ_lang['a_021'] = 'Multi-Editor';
$pia_journ_lang['a_025'] = 'Tools';
$pia_journ_lang['a_030'] = 'Web Service Monitoring';
$pia_journ_lang['a_031'] = 'ICMP Monitoring';
$pia_journ_lang['a_032'] = 'Main Scan';
$pia_journ_lang['a_040'] = 'Network Overview';
$pia_journ_lang['a_050'] = 'Reports';
$pia_journ_lang['a_060'] = 'Update Check';
$pia_journ_lang['a_070'] = 'API';
// pialert-cli
$pia_journ_lang['b_002'] = $pia_journ_lang['a_002'];
$pia_journ_lang['b_010'] = 'DB Maintenance';
$pia_journ_lang['b_030'] = $pia_journ_lang['a_030'];
$pia_journ_lang['b_031'] = $pia_journ_lang['a_031'];
$pia_journ_lang['b_032'] = $pia_journ_lang['a_032'];
// cronjob
$pia_journ_lang['c_002'] = $pia_journ_lang['a_002'];
$pia_journ_lang['c_010'] = $pia_journ_lang['a_010'];
// Log Strings
$pia_journ_lang['LogStr_0001'] = 'Entry added';
$pia_journ_lang['LogStr_0002'] = 'Entry edited';
$pia_journ_lang['LogStr_0003'] = 'Entry deleted';
$pia_journ_lang['LogStr_0004'] = 'Entry edited (with error)';
$pia_journ_lang['LogStr_0005'] = 'Entry deleted (with error)';
$pia_journ_lang['LogStr_0006'] = 'Last configuration file change reverted';
$pia_journ_lang['LogStr_0007'] = 'Configuration backup created';
$pia_journ_lang['LogStr_0008'] = 'GeoLite2 database installed';
$pia_journ_lang['LogStr_0009'] = 'GeoLite2 database deleted';
$pia_journ_lang['LogStr_0010'] = 'Database updated';
$pia_journ_lang['LogStr_0011'] = 'Database backup created';
$pia_journ_lang['LogStr_0012'] = 'Database backup restored';
$pia_journ_lang['LogStr_0013'] = 'Cleaned up backups (configuration file and database)';
$pia_journ_lang['LogStr_0014'] = 'Delete inactive hosts (with error)';
$pia_journ_lang['LogStr_0015'] = 'Delete inactive hosts';
$pia_journ_lang['LogStr_0016'] = 'Delete all hosts without MAC address';
$pia_journ_lang['LogStr_0017'] = 'Delete all hosts without MAC address (with error)';
$pia_journ_lang['LogStr_0018'] = 'Delete all (unknown) hosts';
$pia_journ_lang['LogStr_0019'] = 'Delete all (unknown) hosts (with error)';
$pia_journ_lang['LogStr_0020'] = 'Delete all events of a device';
$pia_journ_lang['LogStr_0021'] = 'Delete all events of a device (with error)';
$pia_journ_lang['LogStr_0022'] = 'Delete all hosts from the device list';
$pia_journ_lang['LogStr_0023'] = 'Delete all hosts from the device list (with error)';
$pia_journ_lang['LogStr_0024'] = 'Delete all events';
$pia_journ_lang['LogStr_0025'] = 'Delete all events (with error)';
$pia_journ_lang['LogStr_0026'] = 'Reset network activity';
$pia_journ_lang['LogStr_0027'] = 'Reset network activity (with error)';
$pia_journ_lang['LogStr_0030'] = 'Added active network component (Switch, Router, etc.)';
$pia_journ_lang['LogStr_0031'] = 'Edited active network component (Switch, Router, etc.)';
$pia_journ_lang['LogStr_0032'] = 'Deleted active network component (Switch, Router, etc.)';
$pia_journ_lang['LogStr_0033'] = 'Added non-scannable device (Hubs, PoE devices, Docker, etc.)';
$pia_journ_lang['LogStr_0034'] = 'Edited non-scannable device (Hubs, PoE devices, Docker, etc.)';
$pia_journ_lang['LogStr_0035'] = 'Deleted non-scannable device (Hubs, PoE devices, Docker, etc.)';
$pia_journ_lang['LogStr_0036'] = 'Devices, Web Services, and ICMP Hosts exported in CSV format.';
$pia_journ_lang['LogStr_0050'] = 'Enabled Pi.Alert password protection';
$pia_journ_lang['LogStr_0051'] = 'Disabled Pi.Alert password protection';
$pia_journ_lang['LogStr_0052'] = 'Changed device list column configuration';
$pia_journ_lang['LogStr_0053'] = 'Changed theme';
$pia_journ_lang['LogStr_0054'] = 'Changed language';
$pia_journ_lang['LogStr_0055'] = $pia_lang['BackDevices_darkmode_disabled'];
$pia_journ_lang['LogStr_0056'] = $pia_lang['BackDevices_darkmode_enabled'];
$pia_journ_lang['LogStr_0057'] = $pia_lang['BackDevices_onlinehistorygraph_disabled'];
$pia_journ_lang['LogStr_0058'] = $pia_lang['BackDevices_onlinehistorygraph_enabled'];
$pia_journ_lang['LogStr_0059'] = 'FavIcon changed';
$pia_journ_lang['LogStr_0061'] = 'Pi.Alert update available';
$pia_journ_lang['LogStr_0062'] = 'No Pi.Alert update available';
$pia_journ_lang['LogStr_0063'] = 'GeoLite2 DB update available';
$pia_journ_lang['LogStr_0064'] = 'No GeoLite2 DB update available';
$pia_journ_lang['LogStr_0065'] = 'GeoLite2 DB not installed';
$pia_journ_lang['LogStr_0101'] = 'Database cleaned and optimized';
$pia_journ_lang['LogStr_0210'] = 'Performed individual nmap scan';
$pia_journ_lang['LogStr_0251'] = 'Wake-on-lan executed';
$pia_journ_lang['LogStr_0255'] = 'Online Speedtest executed';
$pia_journ_lang['LogStr_0301'] = $pia_lang['BackDevices_webservicemon_enabled'];
$pia_journ_lang['LogStr_0302'] = $pia_lang['BackDevices_webservicemon_disabled'];
$pia_journ_lang['LogStr_0500'] = 'Test notification(s) sent';
$pia_journ_lang['LogStr_0503'] = 'Report deleted';
$pia_journ_lang['LogStr_0504'] = 'Delete all reports';
$pia_journ_lang['LogStr_0510'] = 'Pi.Alert reactivated';
$pia_journ_lang['LogStr_0511'] = 'Pi.Alert deactivated (with time specification)';
$pia_journ_lang['LogStr_0512'] = 'Pi.Alert deactivated (for 10 minutes)';
$pia_journ_lang['LogStr_0700'] = 'Set API key';
$pia_journ_lang['LogStr_9001'] = 'Access to the web interface started';
$pia_journ_lang['LogStr_9002'] = 'Logout performed';
$pia_journ_lang['LogStr_9003'] = 'Login error (incorrect password!)';
$pia_journ_lang['LogStr_9004'] = 'Access to the web interface started (with cookie)';
$pia_journ_lang['LogStr_9991'] = $pia_lang['BackDevices_MainScan_enabled'];
$pia_journ_lang['LogStr_9992'] = $pia_lang['BackDevices_MainScan_disabled'];
$pia_journ_lang['LogStr_9999'] = 'Configuration file edited';

?>
