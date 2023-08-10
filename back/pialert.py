#!/usr/bin/env python
#
#-------------------------------------------------------------------------------
#  Pi.Alert
#  Open Source Network Guard / WIFI & LAN intrusion detector and Web service monitor
#
#  pialert.py - Back module. Network scanner, Web service monitor
#-------------------------------------------------------------------------------
#  Puche 2021                                              GNU GPLv3
#  leiweibau 2023                                          GNU GPLv3
#-------------------------------------------------------------------------------

#===============================================================================
# IMPORTS
#===============================================================================
from __future__ import print_function
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText
from requests.packages.urllib3.exceptions import InsecureRequestWarning
from fritzconnection.lib.fritzhosts import FritzHosts
from mac_vendor_lookup import MacLookup
from time import sleep, time, strftime
from base64 import b64encode
try:
  from urlparse import urlparse
except ImportError:
  from urllib.parse import urlparse
import sys
import subprocess
import os
import re
import datetime
import sqlite3
import socket
import io
import smtplib
import csv
import requests
import time
import pwd
import glob
import ipaddress

#===============================================================================
# CONFIG CONSTANTS
#===============================================================================
PIALERT_BACK_PATH = os.path.dirname(os.path.abspath(__file__))
PIALERT_PATH = PIALERT_BACK_PATH + "/.."
PIALERT_WEBSERVICES_LOG = PIALERT_PATH + "/log/pialert.webservices.log"
#STOPARPSCAN = PIALERT_PATH + "/db/setting_stop#arpscan"
STOPPIALERT = PIALERT_PATH + "/db/setting_stoppialert"
PIALERT_DB_FILE = PIALERT_PATH + "/db/pialert.db"
REPORTPATH_WEBGUI = PIALERT_PATH + "/front/reports/"

if (sys.version_info > (3,0)):
    exec(open(PIALERT_PATH + "/config/version.conf").read())
    exec(open(PIALERT_PATH + "/config/pialert.conf").read())
else:
    execfile (PIALERT_PATH + "/config/version.conf")
    execfile (PIALERT_PATH + "/config/pialert.conf")

#===============================================================================
# MAIN
#===============================================================================
def main ():
    global startTime
    global cycle
    global log_timestamp
    global sql_connection
    global sql

    # Header
    print ('\nPi.Alert ' + VERSION +' ('+ VERSION_DATE +')')
    print ('---------------------------------------------------------')
    print("Current User: %s \n" % get_username())
    
    # If user is a sudoer, you can uncomment the line below to set the correct db permission every scan
    # set_pia_file_permissions()
    
    # Initialize global variables
    log_timestamp  = datetime.datetime.now()

    # DB
    sql_connection = None
    sql            = None

    # Timestamp
    startTime = datetime.datetime.now()
    startTime = startTime.replace (second=0, microsecond=0)

    # Check parameters
    if len(sys.argv) != 2 :
        print ('usage pialert [scan_cycle] | internet_IP | update_vendors | cleanup | reporting_test' )
        return
    cycle = str(sys.argv[1])

    ## Main Commands
    if cycle == 'internet_IP':
        res = check_internet_IP()
    elif cycle == 'cleanup':
        res = cleanup_database()
    elif cycle == 'reporting_test':
        res = email_reporting_test('Test')
    elif cycle == 'reporting_starttimer':
        res = email_reporting_test('noti_Timerstart')
    elif cycle == 'reporting_stoptimer':
        res = email_reporting_test('noti_Timerstop')
    elif cycle == 'update_vendors':
        res = update_devices_MAC_vendors()
    elif cycle == 'update_vendors_silent':
        res = update_devices_MAC_vendors('-s')
    elif os.path.exists(STOPPIALERT) == True :
        res = start_arpscan_countdown ()
    elif os.path.exists(STOPPIALERT) == False :
        res = scan_network()
    else:
        res = 0
    
    # Check error
    if res != 0 :
        closeDB()
        return res
    
    # Reporting
    if cycle != 'internet_IP' and cycle != 'cleanup' and cycle != 'reporting_test' and cycle != 'reporting_starttimer' and cycle != 'reporting_stoptimer':
        email_reporting()

    # Close SQL
    closeDB()

    # Final menssage
    print ('\nDONE!!!\n\n')
    return 0    

#===============================================================================
# Set Env (Userpermissions DB-file)
#===============================================================================
def get_username():
    return pwd.getpwuid(os.getuid())[0]

# ------------------------------------------------------------------------------
def set_pia_file_permissions():
    os.system("sudo chown " + get_username() + ":www-data " + PIALERT_DB_FILE)

# ------------------------------------------------------------------------------
def set_pia_reports_permissions():
    os.system("sudo chown -R " + get_username() + ":www-data " + REPORTPATH_WEBGUI)
    os.system("sudo chmod -R 775 " + REPORTPATH_WEBGUI)

#===============================================================================
# Countdown
#===============================================================================
def start_arpscan_countdown ():

    if os.path.exists(STOPPIALERT):
        # get timer from file
        with open(STOPPIALERT, 'r') as file:
            data = int(file.read().rstrip())
            # print("Timer in min: %s" % data)

        # date of file creation
        FILETIME = int(os.path.getctime(STOPPIALERT))

        # output start and end
        print("Timer Start: %s" % time.ctime(FILETIME))
        STOPTIME = FILETIME+data*60
        print("Timer Ende : %s" % time.ctime(STOPTIME))
        print ("----------------------------------------")

        ACTUALTIME = int(time.time())

        if ( ACTUALTIME > STOPTIME ):
           print ("File will be deleted")
           os.remove(STOPPIALERT)
           email_reporting_test("noti_Timerstop")
           scan_network()
        else:
           print ("Timer still running")

#===============================================================================
# INTERNET IP CHANGE
#===============================================================================
def check_internet_IP ():
    # Header
    print ('Check Internet IP')
    print ('    Timestamp:', startTime )
    print ('\nRetrieving Internet IP...')
    internet_IP = get_internet_IP()
    # TESTING - Force IP
        # internet_IP = "1.2.3.4"

    # Check result = IP
    if internet_IP == "" :
        print ('    Error retrieving Internet IP')
        print ('    Exiting...\n')
        return 1
    print ('   ', internet_IP)

    # Get previous stored IP
    print ('\nRetrieving previous IP...')
    openDB()
    previous_IP = get_previous_internet_IP ()
    print ('   ', previous_IP)

    # Check IP Change
    if internet_IP != previous_IP :
        print ('    Saving new IP')
        save_new_internet_IP (internet_IP)
        print ('        IP updated')
    else :
        print ('    No changes to perform')
    closeDB()

    # Get Dynamic DNS IP
    if DDNS_ACTIVE :
        print ('\nRetrieving Dynamic DNS IP...')
        dns_IP = get_dynamic_DNS_IP()

        # Check Dynamic DNS IP
        if dns_IP == "" :
            print ('    Error retrieving Dynamic DNS IP')
            print ('    Exiting...\n')
            return 1
        print ('   ', dns_IP)

        # Check DNS Change
        if dns_IP != internet_IP :
            print ('    Updating Dynamic DNS IP...')
            message = set_dynamic_DNS_IP ()
            print ('       ', message)
        else :
            print ('    No changes to perform')
    else :
        print ('\nSkipping Dynamic DNS update...')

    return 0

#-------------------------------------------------------------------------------
def get_internet_IP ():

    # dig_args = ['dig', '+short', '-4', 'myip.opendns.com', '@resolver1.opendns.com']
    # cmd_output = subprocess.check_output (dig_args, universal_newlines=True)
    curl_args = ['curl', '-s', QUERY_MYIP_SERVER]
    cmd_output = subprocess.check_output (curl_args, universal_newlines=True)

    # Check result is an IP
    IP = check_IP_format (cmd_output)
    return IP

#-------------------------------------------------------------------------------
def get_dynamic_DNS_IP ():

    # Using default or OpenDNS DNS server
    dig_args = ['dig', '+short', DDNS_DOMAIN]
    # dig_args = ['dig', '+short', DDNS_DOMAIN, '@resolver1.opendns.com']
    dig_output = subprocess.check_output (dig_args, universal_newlines=True)

    # Check result is an IP
    IP = check_IP_format (dig_output)
    return IP

#-------------------------------------------------------------------------------
def set_dynamic_DNS_IP ():
    # Update Dynamic IP
    curl_output = subprocess.check_output (['curl', '-s',
        DDNS_UPDATE_URL +
        'username='  + DDNS_USER +
        '&password=' + DDNS_PASSWORD +
        '&hostname=' + DDNS_DOMAIN],
        universal_newlines=True)
    return curl_output
    
#-------------------------------------------------------------------------------
def get_previous_internet_IP ():
    # get previos internet IP stored in DB
    sql.execute ("SELECT dev_LastIP FROM Devices WHERE dev_MAC = 'Internet' ")
    previous_IP = sql.fetchone()[0]
    return previous_IP

#-------------------------------------------------------------------------------
def save_new_internet_IP (pNewIP):
    # Log new IP into logfile
    append_line_to_file (LOG_PATH + '/IP_changes.log',
        str(startTime) +'\t'+ pNewIP +'\n')

    # Save event
    sql.execute ("""INSERT INTO Events (eve_MAC, eve_IP, eve_DateTime,
                        eve_EventType, eve_AdditionalInfo,
                        eve_PendingAlertEmail)
                    VALUES ('Internet', ?, ?, 'Internet IP Changed',
                        'Previous Internet IP: '|| ?, 1) """,
                    (pNewIP, startTime, get_previous_internet_IP() ) )

    # Save new IP
    sql.execute ("""UPDATE Devices SET dev_LastIP = ?
                    WHERE dev_MAC = 'Internet' """,
                    (pNewIP,) )

    # commit changes
    sql_connection.commit()
    
#-------------------------------------------------------------------------------
def check_IP_format (pIP):
    # Check IP format
    IPv4SEG  = r'(?:25[0-5]|(?:2[0-4]|1{0,1}[0-9]){0,1}[0-9])'
    IPv4ADDR = r'(?:(?:' + IPv4SEG + r'\.){3,3}' + IPv4SEG + r')'
    IP = re.search(IPv4ADDR, pIP)

    # Return error if not IP
    if IP is None :
        return ""

    return IP.group(0)

#===============================================================================
# Cleanup Tasks
#===============================================================================
def cleanup_database ():
    # Header
    print ('Cleanup Database')
    print ('    Timestamp:', startTime )

    openDB()

    # keep 60 days if not specified how many days to keep
    try:
        strdaystokeepOH = str(DAYS_TO_KEEP_ONLINEHISTORY)
    except NameError: # variable not defined, use a default
        strdaystokeepOH = str(60) # 2 months

    # keep 200 days if not specified how many days to keep
    try:
        strdaystokeepEV = str(DAYS_TO_KEEP_EVENTS)
    except NameError: # variable not defined, use a default
        strdaystokeepEV = str(200) # 200 days

    # Cleanup WebServices Events
    print ('    Services_Events, up to the lastest '+strdaystokeepOH+' days...')
    sql.execute ("DELETE FROM Services_Events WHERE moneve_DateTime <= date('now', '-"+strdaystokeepOH+" day')")
    # Cleanup Online History
    print ('    Online_History, up to the lastest '+strdaystokeepOH+' days...')
    sql.execute ("DELETE FROM Online_History WHERE Scan_Date <= date('now', '-"+strdaystokeepOH+" day')")
    # Cleanup Events
    print ('    Events, up to the lastest '+strdaystokeepEV+' days...')
    sql.execute ("DELETE FROM Events WHERE eve_DateTime <= date('now', '-"+strdaystokeepEV+" day')")
    # Shrink DB
    print ('    Trim Journal to the lastest 1000 entries')
    sql.execute ("DELETE FROM pialert_journal WHERE journal_id NOT IN (SELECT journal_id FROM pialert_journal ORDER BY journal_id DESC LIMIT 1000) AND (SELECT COUNT(*) FROM pialert_journal) > 1000")
    # Shrink DB
    print ('    Shrink Database...')
    sql.execute ("VACUUM;")

    closeDB()
    
    return 0

#===============================================================================
# UPDATE DEVICE MAC VENDORS
#===============================================================================
def update_devices_MAC_vendors (pArg = ''):
    # Header
    print ('Update HW Vendors')
    print ('    Timestamp:', startTime )

    # Update vendors DB (iab oui)
    print ('\nUpdating vendors DB (iab & oui)...')
    update_args = ['sh', PIALERT_BACK_PATH + '/update_vendors.sh', pArg]
    update_output = subprocess.check_output (update_args)

    # Initialize variables
    recordsToUpdate = []
    ignored = 0
    notFound = 0

    # All devices loop
    print ('\nSearching devices vendor', end='')
    openDB()
    # Only the devices for which no vendor has yet been entered are attempted to be updated.
    for device in sql.execute ("SELECT * FROM Devices WHERE dev_Vendor = ''") :
        # Search vendor in HW Vendors DB
        vendor = query_MAC_vendor (device['dev_MAC'])
        if vendor == -1 :
            notFound += 1
        elif vendor == -2 :
            ignored += 1
        else :
            recordsToUpdate.append ([vendor, device['dev_MAC']])
        # progress bar
        print ('.', end='')
        sys.stdout.flush()
            
    # Print log
    print ('')
    print ("    Devices Ignored:  ", ignored)
    print ("    Vendors Not Found:", notFound)
    print ("    Vendors updated:  ", len(recordsToUpdate) )

    # mac-vendor-lookup update
    try:
        print ('\nTry build in mac-vendor-lookup update')
        mac = MacLookup()
        mac.update_vendors()
        print ('    Update successful')
    except:
        print ('\nFallback')
        print ('    Backup old mac-vendors.txt for mac-vendor-lookup')
        p = subprocess.call(["cp ~/.cache/mac-vendors.txt ~/.cache/mac-vendors.bak"], shell=True)
        print ('    Create mac-vendors.txt for mac-vendor-lookup')
        p = subprocess.call(["/usr/bin/sed -e 's/\t/:/g' -e 's/Ã¼/ü/g' -e 's/Ã¶/ö/g' -e 's/Ã¤/ä/g' -e 's/Ã³/ó/g' -e 's/Ã©/é/g' -e 's/â/–/g' -e 's/Â//g' -e '/^#/d' /usr/share/arp-scan/ieee-oui.txt > ~/.cache/mac-vendors.txt"], shell=True)

    # update devices
    sql.executemany ("UPDATE Devices SET dev_Vendor = ? WHERE dev_MAC = ? ",
        recordsToUpdate )

    closeDB()

#-------------------------------------------------------------------------------
def query_MAC_vendor (pMAC):
    try :
        pMACstr = str(pMAC)
        
        # Check MAC parameter
        mac = pMACstr.replace (':','')
        if len(pMACstr) != 17 or len(mac) != 12 :
            return -2

        # Search vendor in HW Vendors DB
        mac = mac[0:6]
        grep_args = ['grep', '-i', mac, VENDORS_DB]
        grep_output = subprocess.check_output (grep_args)

        # Return Vendor
        vendor = grep_output[7:]
        vendor = vendor.rstrip()
        return vendor

    # not Found
    except subprocess.CalledProcessError :
        return -1
            
#===============================================================================
# SCAN NETWORK
#===============================================================================
def scan_network ():
    # Header
    print ('Scan Devices')
    print ('    Timestamp:', startTime )

    # Query ScanCycle properties
    print_log ('Query ScanCycle confinguration...')
    scanCycle_data = query_ScanCycle_Data (True)
    if scanCycle_data is None:
        print ('\n*************** ERROR ***************')
        print ('ScanCycle %s not found' % cycle )
        print ('    Exiting...\n')
        return 1

    # ScanCycle data        
    cycle_interval  = scanCycle_data['cic_EveryXmin']
    arpscan_retries = scanCycle_data['cic_arpscanCycles']
    # arp-scan command
    print ('\nScanning...')
    print ('    arp-scan Method...')
    print_log ('arp-scan starts...')
    arpscan_devices = execute_arpscan ()
    print_log ('arp-scan ends')
    # Pi-hole method
    print ('    Pi-hole Method...')
    openDB()
    print_log ('Pi-hole copy starts...')
    copy_pihole_network()
    # DHCP Leases method
    print ('    DHCP Leases Method...')
    read_DHCP_leases ()
    # Fritzbox method
    print ('    Fritzbox Method...')
    openDB()
    print_log ('Fritzbox copy starts...')
    read_fritzbox_active_hosts()
    # Load current scan data 1/2
    print ('\nProcessing scan results...')
    # Load current scan data 2/2
    print_log ('Save scanned devices')
    save_scanned_devices (arpscan_devices, cycle_interval)
    # Process Ignore list
    print ('    Processing ignore list...')
    remove_entries_from_table()
        # Print stats
    print_log ('Print Stats')
    print_scan_stats()
    print_log ('Stats end')
    # Create Events
    print ('\nUpdating DB Info...')
    print ('    Sessions Events (connect / discconnect) ...')
    insert_events()
    # Create New Devices
    # after create events -> avoid 'connection' event
    print ('    Creating new devices...')
    create_new_devices ()
    # Update devices info
    print ('    Updating Devices Info...')
    update_devices_data_from_scan ()
    # Resolve devices names
    print_log ('   Resolve devices names...')
    update_devices_names()
    # Void false connection - disconnections
    print ('    Voiding false (ghost) disconnections...')
    void_ghost_disconnections ()
    # Pair session events (Connection / Disconnection)
    print ('    Pairing session events (connection / disconnection) ...')
    pair_sessions_events()
    # Sessions snapshot
    print ('    Creating sessions snapshot...')
    create_sessions_snapshot ()
    # Skip repeated notifications
    print ('    Skipping repeated notifications...')
    skip_repeated_notifications ()
    # Calc Activity History
    print ('    Calculate Activity History...')
    calculate_activity_history ()
    # Web Service Monitoring
    try:
        enable_services_monitoring = SCAN_WEBSERVICES
    except NameError:
        enable_services_monitoring = False
    if enable_services_monitoring == True:
        if str(startTime)[15] == "0":
            service_monitoring()
    # Check Rogue DHCP
    try:
        enable_rogue_dhcp_detection = SCAN_ROGUE_DHCP
    except NameError:
        enable_rogue_dhcp_detection = False
    if enable_rogue_dhcp_detection == True:
        print ('\nLooking for Rogue DHCP Servers...')
        rogue_dhcp_detection ()

    # Commit changes
    sql_connection.commit()
    closeDB()

    return 0

#-------------------------------------------------------------------------------
def query_ScanCycle_Data (pOpenCloseDB = False):
    # Check if is necesary open DB
    if pOpenCloseDB :
        openDB()

    # Query Data
    sql.execute ("""SELECT cic_arpscanCycles, cic_EveryXmin
                    FROM ScanCycles
                    WHERE cic_ID = ? """, (cycle,))
    sqlRow = sql.fetchone()

    # Check if is necesary close DB
    if pOpenCloseDB :
        closeDB()

    return sqlRow

#-------------------------------------------------------------------------------
def execute_arpscan ():

    # check if arp-scan is active
    if not ARPSCAN_ACTIVE :
        unique_devices = [] 
        return unique_devices

    # output of possible multiple interfaces
    arpscan_output = ""

    # multiple interfaces
    if type(SCAN_SUBNETS) is list:
        print("    arp-scan: Multiple interfaces")        
        for interface in SCAN_SUBNETS :            
            arpscan_output += execute_arpscan_on_interface (interface)
    # one interface only
    else:
        print("    arp-scan: One interface")
        arpscan_output += execute_arpscan_on_interface (SCAN_SUBNETS)

    # Search IP + MAC + Vendor as regular expresion
    re_ip = r'(?P<ip>((2[0-5]|1[0-9]|[0-9])?[0-9]\.){3}((2[0-5]|1[0-9]|[0-9])?[0-9]))'
    re_mac = r'(?P<mac>([0-9a-fA-F]{2}[:-]){5}([0-9a-fA-F]{2}))'
    re_hw = r'(?P<hw>.*)'
    re_pattern = re.compile (re_ip + '\s+' + re_mac + '\s' + re_hw)

    # Create Userdict of devices
    devices_list = [device.groupdict()
        for device in re.finditer (re_pattern, arpscan_output)]

    # Delete duplicate MAC
    unique_mac = [] 
    unique_devices = [] 

    for device in devices_list :
        if device['mac'] not in unique_mac: 
            unique_mac.append(device['mac'])
            unique_devices.append(device)

    return unique_devices

#-------------------------------------------------------------------------------
def execute_arpscan_on_interface (SCAN_SUBNETS):
    # Prepare command arguments
    subnets = SCAN_SUBNETS.strip().split()
    # Retry is 3 to avoid false offline devices
    arpscan_args = ['sudo', 'arp-scan', '--ignoredups', '--bandwidth=256k', '--retry=6'] + subnets

    # Execute command
    try:
        # try runnning a subprocess
        result = subprocess.check_output (arpscan_args, universal_newlines=True)
    except subprocess.CalledProcessError as e:
        # An error occured, handle it
        print(e.output)
        result = ""

    return result

#-------------------------------------------------------------------------------
def copy_pihole_network ():
    # empty Fritzbox Network table
    sql.execute ("DELETE FROM PiHole_Network")

    # check if Pi-hole is active
    if not PIHOLE_ACTIVE :
        return

    # Open Pi-hole DB
    sql.execute ("ATTACH DATABASE '"+ PIHOLE_DB +"' AS PH")

    # Copy Pi-hole Network table
    sql.execute ("""INSERT INTO PiHole_Network (PH_MAC, PH_Vendor, PH_LastQuery,
                        PH_Name, PH_IP)
                    SELECT hwaddr, macVendor, lastQuery,
                        (SELECT name FROM PH.network_addresses
                         WHERE network_id = id ORDER BY lastseen DESC, ip),
                        (SELECT ip FROM PH.network_addresses
                         WHERE network_id = id ORDER BY lastseen DESC, ip)
                    FROM PH.network
                    WHERE hwaddr NOT LIKE 'ip-%'
                      AND hwaddr <> '00:00:00:00:00:00' """)
    sql.execute ("""UPDATE PiHole_Network SET PH_Name = '(unknown)'
                    WHERE PH_Name IS NULL OR PH_Name = '' """)

    # Close Pi-hole DB
    sql.execute ("DETACH PH")

#-------------------------------------------------------------------------------
def read_fritzbox_active_hosts ():
    # create table if not exists
    sql_create_table = """ CREATE TABLE IF NOT EXISTS Fritzbox_Network(
                                "FB_MAC" STRING(50) NOT NULL COLLATE NOCASE,
                                "FB_IP" STRING(50) COLLATE NOCASE,
                                "FB_Name" STRING(50),
                                "FB_Vendor" STRING(250)
                            ); """
    sql.execute(sql_create_table)
    sql_connection.commit()

    # empty Fritzbox Network table
    sql.execute ("DELETE FROM Fritzbox_Network")

    # check if Pi-hole is active
    if not FRITZBOX_ACTIVE :
        return

    # copy Fritzbox Network list
    fh = FritzHosts(address=FRITZBOX_IP, user=FRITZBOX_USER, password=FRITZBOX_PASS)
    hosts = fh.get_hosts_info()
    for index, host in enumerate(hosts, start=1):
        if host['status'] :
            # status = 'active' if host['status'] else  '-'
            ip = host['ip'] if host['ip'] else 'no IP'
            mac = host['mac'].lower() if host['mac'] else '-'
            hostname = host['name']
            try:
                vendor = MacLookup().lookup(host['mac'])
            except:
                vendor = "Prefix is not registered"
            
            sql.execute ("INSERT INTO Fritzbox_Network (FB_MAC, FB_IP, FB_Name, FB_Vendor) "+
                         "VALUES (?, ?, ?, ?) ", (mac, ip, hostname, vendor) )

#-------------------------------------------------------------------------------
def read_DHCP_leases ():
    # check DHCP Leases is active
    if not DHCP_ACTIVE :
        return
            
    # Read DHCP Leases
    data = []
    with open(DHCP_LEASES, 'r') as f:
        for line in f:
            row = line.rstrip().split()
            if len(row) == 5 :
                data.append (row)

    # Insert into PiAlert table
    sql.execute ("DELETE FROM DHCP_Leases")
    sql.executemany ("""INSERT INTO DHCP_Leases (DHCP_DateTime, DHCP_MAC,
                            DHCP_IP, DHCP_Name, DHCP_MAC2)
                        VALUES (?, ?, ?, ?, ?)
                     """, data)

#-------------------------------------------------------------------------------
def save_scanned_devices (p_arpscan_devices, p_cycle_interval):
    # Delete previous scan data
    sql.execute ("DELETE FROM CurrentScan WHERE cur_ScanCycle = ?",
                (cycle,))

    # Insert new arp-scan devices
    sql.executemany ("INSERT INTO CurrentScan (cur_ScanCycle, cur_MAC, "+
                     "    cur_IP, cur_Vendor, cur_ScanMethod) "+
                     "VALUES ("+ cycle + ", :mac, :ip, :hw, 'arp-scan')",
                     p_arpscan_devices) 

    # Insert Pi-hole devices
    sql.execute ("""INSERT INTO CurrentScan (cur_ScanCycle, cur_MAC, 
                        cur_IP, cur_Vendor, cur_ScanMethod)
                    SELECT ?, PH_MAC, PH_IP, PH_Vendor, 'Pi-hole'
                    FROM PiHole_Network
                    WHERE PH_LastQuery >= ?
                      AND NOT EXISTS (SELECT 'X' FROM CurrentScan
                                      WHERE cur_MAC = PH_MAC
                                        AND cur_ScanCycle = ? )""",
                    (cycle,
                     (int(startTime.strftime('%s')) - 60 * p_cycle_interval),
                     cycle) )

    # Insert Fritzbox devices
    sql.execute ("""INSERT INTO CurrentScan (cur_ScanCycle, cur_MAC, 
                        cur_IP, cur_Vendor, cur_ScanMethod)
                    SELECT ?, FB_MAC, FB_IP, FB_Vendor, 'Fritzbox'
                    FROM Fritzbox_Network
                    WHERE NOT EXISTS (SELECT 'X' FROM CurrentScan
                                      WHERE cur_MAC = FB_MAC )""",
                    (cycle) )

    # Check Internet connectivity
    internet_IP = get_internet_IP()
        # TESTING - Force IP
        # internet_IP = ""
    if internet_IP != "" :
        sql.execute ("""INSERT INTO CurrentScan (cur_ScanCycle, cur_MAC, cur_IP, cur_Vendor, cur_ScanMethod)
                        VALUES (?, 'Internet', ?, Null, 'queryDNS') """, (cycle, internet_IP) )

    local_mac_cmd = ["/sbin/ifconfig `ip -o route get 1 | sed 's/^.*dev \\([^ ]*\\).*$/\\1/;q'` | grep ether | awk '{print $2}'"]
    local_mac = subprocess.Popen (local_mac_cmd, shell=True, stdout=subprocess.PIPE, stderr=subprocess.STDOUT).communicate()[0].decode().strip()
    
    # local_ip_cmd = ["ip route list default | awk {'print $7'}"]
    local_ip_cmd = ["ip -o route get 1 | sed 's/^.*src \\([^ ]*\\).*$/\\1/;q'"]
    local_ip = subprocess.Popen (local_ip_cmd, shell=True, stdout=subprocess.PIPE, stderr=subprocess.STDOUT).communicate()[0].decode().strip()

    # Check if local mac has been detected with other methods
    sql.execute ("SELECT COUNT(*) FROM CurrentScan WHERE cur_ScanCycle = ? AND cur_MAC = ? ", (cycle, local_mac) )
    if sql.fetchone()[0] == 0 :
        sql.execute ("INSERT INTO CurrentScan (cur_ScanCycle, cur_MAC, cur_IP, cur_Vendor, cur_ScanMethod) "+
                     "VALUES ( ?, ?, ?, Null, 'local_MAC') ", (cycle, local_mac, local_ip) )

#-------------------------------------------------------------------------------
def remove_entries_from_table():
    try:
        MAC_IGNORE_LIST
        print ('        Delete ' + str(len(MAC_IGNORE_LIST)) + ' ignored devices from scan on appearance')

        mac_addresses = ','.join(['"{}"'.format(mac) for mac in MAC_IGNORE_LIST])
        query = 'DELETE FROM CurrentScan WHERE cur_MAC IN ({})'.format(mac_addresses)
        sql.execute(query)
        query = 'DELETE FROM PiHole_Network WHERE PH_MAC IN ({})'.format(mac_addresses)
        sql.execute(query)
        query = 'DELETE FROM DHCP_Leases WHERE DHCP_MAC IN ({})'.format(mac_addresses)
        sql.execute(query)
        query = 'DELETE FROM Fritzbox_Network WHERE FB_MAC IN ({})'.format(mac_addresses)
        sql.execute(query)
    except NameError:
        print("        No ignore list defined")

#-------------------------------------------------------------------------------
def print_scan_stats ():
    # Devices Detected
    sql.execute ("""SELECT COUNT(*) FROM CurrentScan
                    WHERE cur_ScanCycle = ? """,
                    (cycle,))
    print ('    Devices Detected.......:', str (sql.fetchone()[0]) )
    # Devices arp-scan
    sql.execute ("""SELECT COUNT(*) FROM CurrentScan
                    WHERE cur_ScanMethod='arp-scan' AND cur_ScanCycle = ? """,
                    (cycle,))
    print ('        arp-scan Method....:', str (sql.fetchone()[0]) )
    # Devices Pi-hole
    sql.execute ("""SELECT COUNT(*) FROM CurrentScan
                    WHERE cur_ScanMethod='Pi-hole' AND cur_ScanCycle = ? """,
                    (cycle,))
    print ('        Pi-hole Method.....: +' + str (sql.fetchone()[0]) )
    # Devices Pi-hole
    sql.execute ("""SELECT COUNT(*) FROM CurrentScan
                    WHERE cur_ScanMethod='Fritzbox' AND cur_ScanCycle = ? """,
                    (cycle,))
    print ('        Fritzbox Method....: +' + str (sql.fetchone()[0]) )
    # New Devices
    sql.execute ("""SELECT COUNT(*) FROM CurrentScan
                    WHERE cur_ScanCycle = ? 
                      AND NOT EXISTS (SELECT 1 FROM Devices
                                      WHERE dev_MAC = cur_MAC) """,
                    (cycle,))
    print ('        New Devices........: ' + str (sql.fetchone()[0]) )
    # Devices in this ScanCycle
    sql.execute ("""SELECT COUNT(*) FROM Devices, CurrentScan
                    WHERE dev_MAC = cur_MAC AND dev_ScanCycle = cur_ScanCycle
                      AND dev_ScanCycle = ? """,
                    (cycle,))
    print ('')
    print ('    Devices in this cycle..: ' + str (sql.fetchone()[0]) )
    # Down Alerts
    sql.execute ("""SELECT COUNT(*) FROM Devices
                    WHERE dev_AlertDeviceDown = 1
                      AND dev_ScanCycle = ?
                      AND NOT EXISTS (SELECT 1 FROM CurrentScan
                                      WHERE dev_MAC = cur_MAC
                                        AND dev_ScanCycle = cur_ScanCycle) """,
                    (cycle,))
    print ('        Down Alerts........: ' + str (sql.fetchone()[0]) )
    # New Down Alerts
    sql.execute ("""SELECT COUNT(*) FROM Devices
                    WHERE dev_AlertDeviceDown = 1
                      AND dev_PresentLastScan = 1
                      AND dev_ScanCycle = ?
                      AND NOT EXISTS (SELECT 1 FROM CurrentScan
                                      WHERE dev_MAC = cur_MAC
                                        AND dev_ScanCycle = cur_ScanCycle) """,
                    (cycle,))
    print ('        New Down Alerts....: ' + str (sql.fetchone()[0]) )
    # New Connections
    sql.execute ("""SELECT COUNT(*) FROM Devices, CurrentScan
                    WHERE dev_MAC = cur_MAC AND dev_ScanCycle = cur_ScanCycle
                      AND dev_PresentLastScan = 0
                      AND dev_ScanCycle = ? """,
                    (cycle,))
    print ('        New Connections....: ' + str ( sql.fetchone()[0]) )
    # Disconnections
    sql.execute ("""SELECT COUNT(*) FROM Devices
                    WHERE dev_PresentLastScan = 1
                      AND dev_ScanCycle = ?
                      AND NOT EXISTS (SELECT 1 FROM CurrentScan
                                      WHERE dev_MAC = cur_MAC
                                        AND dev_ScanCycle = cur_ScanCycle) """,
                    (cycle,))
    print ('        Disconnections.....: ' + str ( sql.fetchone()[0]) )
    # IP Changes
    sql.execute ("""SELECT COUNT(*) FROM Devices, CurrentScan
                    WHERE dev_MAC = cur_MAC AND dev_ScanCycle = cur_ScanCycle
                      AND dev_ScanCycle = ?
                      AND dev_LastIP <> cur_IP """,
                    (cycle,))
    print ('        IP Changes.........: ' + str ( sql.fetchone()[0]) )

#------------------------------------------------------------------------------
def calculate_activity_history ():
    # Add to History
    sql.execute("SELECT * FROM Devices WHERE dev_Archived = 0 AND dev_PresentLastScan = 1")
    Querry_Online_Devices = sql.fetchall()
    History_Online_Devices  = len(Querry_Online_Devices)
    sql.execute("SELECT * FROM Devices WHERE dev_Archived = 0 AND dev_PresentLastScan = 0")
    Querry_Offline_Devices = sql.fetchall()
    History_Offline_Devices  = len(Querry_Offline_Devices)
    sql.execute("SELECT * FROM Devices WHERE dev_Archived = 1")
    Querry_Archived_Devices = sql.fetchall()
    History_Archived_Devices  = len(Querry_Archived_Devices)
    History_ALL_Devices = History_Online_Devices + History_Offline_Devices + History_Archived_Devices
    sql.execute ("INSERT INTO Online_History (Scan_Date, Online_Devices, Down_Devices, All_Devices, Archived_Devices ) "+
                 "VALUES ( ?, ?, ?, ?, ?)", (startTime, History_Online_Devices, History_Offline_Devices, History_ALL_Devices, History_Archived_Devices ) )

#-------------------------------------------------------------------------------
def create_new_devices ():
    # arpscan - Insert events for new devices
    print_log ('New devices - 1 Events')
    sql.execute ("""INSERT INTO Events (eve_MAC, eve_IP, eve_DateTime,
                        eve_EventType, eve_AdditionalInfo,
                        eve_PendingAlertEmail)
                    SELECT cur_MAC, cur_IP, ?, 'New Device', cur_Vendor, 1
                    FROM CurrentScan
                    WHERE cur_ScanCycle = ? 
                      AND NOT EXISTS (SELECT 1 FROM Devices
                                      WHERE dev_MAC = cur_MAC) """,
                    (startTime, cycle) ) 

    # arpscan - Create new devices
    print_log ('New devices - 2 Create devices')
    sql.execute ("""INSERT INTO Devices (dev_MAC, dev_name, dev_Vendor,
                        dev_LastIP, dev_FirstConnection, dev_LastConnection,
                        dev_ScanCycle, dev_AlertEvents, dev_AlertDeviceDown,
                        dev_PresentLastScan)
                    SELECT cur_MAC, '(unknown)', cur_Vendor, cur_IP, ?, ?,
                        1, 1, 0, 1
                    FROM CurrentScan
                    WHERE cur_ScanCycle = ? 
                      AND NOT EXISTS (SELECT 1 FROM Devices
                                      WHERE dev_MAC = cur_MAC) """,
                    (startTime, startTime, cycle) ) 

    # Pi-hole - Insert events for new devices
    print_log ('New devices - 3 Pi-hole Events')
    sql.execute ("""INSERT INTO Events (eve_MAC, eve_IP, eve_DateTime,
                        eve_EventType, eve_AdditionalInfo,
                        eve_PendingAlertEmail)
                    SELECT PH_MAC, IFNULL (PH_IP,'-'), ?, 'New Device',
                        '(Pi-Hole) ' || PH_Vendor, 1
                    FROM PiHole_Network
                    WHERE NOT EXISTS (SELECT 1 FROM Devices
                                      WHERE dev_MAC = PH_MAC) """,
                    (startTime, ) ) 

    # Pi-hole - Create New Devices
    print_log ('New devices - 4 Pi-hole Create devices')
    sql.execute ("""INSERT INTO Devices (dev_MAC, dev_name, dev_Vendor,
                        dev_LastIP, dev_FirstConnection, dev_LastConnection,
                        dev_ScanCycle, dev_AlertEvents, dev_AlertDeviceDown,
                        dev_PresentLastScan)
                    SELECT PH_MAC, PH_Name, PH_Vendor, IFNULL (PH_IP,'-'),
                        ?, ?, 1, 1, 0, 1
                    FROM PiHole_Network
                    WHERE NOT EXISTS (SELECT 1 FROM Devices
                                      WHERE dev_MAC = PH_MAC) """,
                    (startTime, startTime) ) 

    # DHCP Leases - Insert events for new devices
    print_log ('New devices - 5 DHCP Leases Events')
    sql.execute ("""INSERT INTO Events (eve_MAC, eve_IP, eve_DateTime,
                        eve_EventType, eve_AdditionalInfo,
                        eve_PendingAlertEmail)
                    SELECT DHCP_MAC, DHCP_IP, ?, 'New Device', '(DHCP lease)',1
                    FROM DHCP_Leases
                    WHERE NOT EXISTS (SELECT 1 FROM Devices
                                      WHERE dev_MAC = DHCP_MAC) """,
                    (startTime, ) ) 

    # DHCP Leases - Create New Devices
    print_log ('New devices - 6 DHCP Leases Create devices')
    sql.execute ("""INSERT INTO Devices (dev_MAC, dev_name, dev_LastIP, 
                        dev_Vendor, dev_FirstConnection, dev_LastConnection,
                        dev_ScanCycle, dev_AlertEvents, dev_AlertDeviceDown,
                        dev_PresentLastScan)
                    SELECT DISTINCT DHCP_MAC,
                        (SELECT DHCP_Name FROM DHCP_Leases AS D2
                         WHERE D2.DHCP_MAC = D1.DHCP_MAC
                         ORDER BY DHCP_DateTime DESC LIMIT 1),
                        (SELECT DHCP_IP FROM DHCP_Leases AS D2
                         WHERE D2.DHCP_MAC = D1.DHCP_MAC
                         ORDER BY DHCP_DateTime DESC LIMIT 1),
                        '(unknown)', ?, ?, 1, 1, 0, 1
                    FROM DHCP_Leases AS D1
                    WHERE NOT EXISTS (SELECT 1 FROM Devices
                                      WHERE dev_MAC = DHCP_MAC) """,
                    (startTime, startTime) ) 

    print_log ('New Devices end')

#-------------------------------------------------------------------------------
def insert_events ():
    # Check device down
    print_log ('Events 1 - Devices down')
    sql.execute ("""INSERT INTO Events (eve_MAC, eve_IP, eve_DateTime,
                        eve_EventType, eve_AdditionalInfo,
                        eve_PendingAlertEmail)
                    SELECT dev_MAC, dev_LastIP, ?, 'Device Down', '', 1
                    FROM Devices
                    WHERE dev_AlertDeviceDown = 1
                      AND dev_PresentLastScan = 1
                      AND dev_ScanCycle = ?
                      AND NOT EXISTS (SELECT 1 FROM CurrentScan
                                      WHERE dev_MAC = cur_MAC
                                        AND dev_ScanCycle = cur_ScanCycle) """,
                    (startTime, cycle) )

    # Check new connections
    print_log ('Events 2 - New Connections')
    sql.execute ("""INSERT INTO Events (eve_MAC, eve_IP, eve_DateTime,
                        eve_EventType, eve_AdditionalInfo,
                        eve_PendingAlertEmail)
                    SELECT cur_MAC, cur_IP, ?, 'Connected', '', dev_AlertEvents
                    FROM Devices, CurrentScan
                    WHERE dev_MAC = cur_MAC AND dev_ScanCycle = cur_ScanCycle
                      AND dev_PresentLastScan = 0
                      AND dev_ScanCycle = ? """,
                    (startTime, cycle) )

    # Check disconnections
    print_log ('Events 3 - Disconnections')
    sql.execute ("""INSERT INTO Events (eve_MAC, eve_IP, eve_DateTime,
                        eve_EventType, eve_AdditionalInfo,
                        eve_PendingAlertEmail)
                    SELECT dev_MAC, dev_LastIP, ?, 'Disconnected', '',
                        dev_AlertEvents
                    FROM Devices
                    WHERE dev_AlertDeviceDown = 0
                      AND dev_PresentLastScan = 1
                      AND dev_ScanCycle = ?
                      AND NOT EXISTS (SELECT 1 FROM CurrentScan
                                      WHERE dev_MAC = cur_MAC
                                        AND dev_ScanCycle = cur_ScanCycle) """,
                    (startTime, cycle) )

    # Check IP Changed
    print_log ('Events 4 - IP Changes')
    sql.execute ("""INSERT INTO Events (eve_MAC, eve_IP, eve_DateTime,
                        eve_EventType, eve_AdditionalInfo,
                        eve_PendingAlertEmail)
                    SELECT cur_MAC, cur_IP, ?, 'IP Changed',
                        'Previous IP: '|| dev_LastIP, dev_AlertEvents
                    FROM Devices, CurrentScan
                    WHERE dev_MAC = cur_MAC AND dev_ScanCycle = cur_ScanCycle
                      AND dev_ScanCycle = ?
                      AND dev_LastIP <> cur_IP """,
                    (startTime, cycle) )
    print_log ('Events end')

#-------------------------------------------------------------------------------
def update_devices_data_from_scan ():
    # Update Last Connection
    print_log ('Update devices - 1 Last Connection')
    sql.execute ("""UPDATE Devices SET dev_LastConnection = ?,
                        dev_PresentLastScan = 1
                    WHERE dev_ScanCycle = ?
                      AND dev_PresentLastScan = 0
                      AND EXISTS (SELECT 1 FROM CurrentScan 
                                  WHERE dev_MAC = cur_MAC
                                    AND dev_ScanCycle = cur_ScanCycle) """,
                    (startTime, cycle))

    # Clean no active devices
    print_log ('Update devices - 2 Clean no active devices')
    sql.execute ("""UPDATE Devices SET dev_PresentLastScan = 0
                    WHERE dev_ScanCycle = ?
                      AND NOT EXISTS (SELECT 1 FROM CurrentScan 
                                      WHERE dev_MAC = cur_MAC
                                        AND dev_ScanCycle = cur_ScanCycle) """,
                    (cycle,))

    # Update IP & Vendor
    print_log ('Update devices - 3 LastIP & Vendor')
    sql.execute ("""UPDATE Devices
                    SET dev_LastIP = (SELECT cur_IP FROM CurrentScan
                                      WHERE dev_MAC = cur_MAC
                                        AND dev_ScanCycle = cur_ScanCycle),
                        dev_Vendor = (SELECT cur_Vendor FROM CurrentScan
                                      WHERE dev_MAC = cur_MAC
                                        AND dev_ScanCycle = cur_ScanCycle)
                    WHERE dev_ScanCycle = ?
                      AND EXISTS (SELECT 1 FROM CurrentScan
                                  WHERE dev_MAC = cur_MAC
                                    AND dev_ScanCycle = cur_ScanCycle) """,
                    (cycle,)) 

    # Pi-hole Network - Update (unknown) Name
    print_log ('Update devices - 4 Unknown Name')
    sql.execute ("""UPDATE Devices
                    SET dev_NAME = (SELECT PH_Name FROM PiHole_Network
                                    WHERE PH_MAC = dev_MAC)
                    WHERE (dev_Name = "(unknown)"
                           OR dev_Name = ""
                           OR dev_Name IS NULL)
                      AND EXISTS (SELECT 1 FROM PiHole_Network
                                  WHERE PH_MAC = dev_MAC
                                    AND PH_NAME IS NOT NULL
                                    AND PH_NAME <> '') """)

    # DHCP Leases - Update (unknown) Name
    sql.execute ("""UPDATE Devices
                    SET dev_NAME = (SELECT DHCP_Name FROM DHCP_Leases
                                    WHERE DHCP_MAC = dev_MAC)
                    WHERE (dev_Name = "(unknown)"
                           OR dev_Name = ""
                           OR dev_Name IS NULL)
                      AND EXISTS (SELECT 1 FROM DHCP_Leases
                                  WHERE DHCP_MAC = dev_MAC)""")

    # DHCP Leases - Vendor
    print_log ('Update devices - 5 Vendor')

    recordsToUpdate = []
    query = """SELECT * FROM Devices
               WHERE dev_Vendor = '(unknown)' OR dev_Vendor =''
                  OR dev_Vendor IS NULL"""

    for device in sql.execute (query) :
        vendor = query_MAC_vendor (device['dev_MAC'])
        if vendor != -1 and vendor != -2 :
            recordsToUpdate.append ([vendor, device['dev_MAC']])

    sql.executemany ("UPDATE Devices SET dev_Vendor = ? WHERE dev_MAC = ? ",
        recordsToUpdate )

    # New Apple devices -> Cycle 15
    print_log ('Update devices - 6 Cycle for Apple devices')
    sql.execute ("""UPDATE Devices SET dev_ScanCycle = 1
                    WHERE dev_FirstConnection = ?
                      AND UPPER(dev_Vendor) LIKE '%APPLE%' """,
                (startTime,) )

    print_log ('Update devices end')

#-------------------------------------------------------------------------------
def update_devices_names ():
    # Initialize variables
    recordsToUpdate = []
    ignored = 0
    notFound = 0

    # Devices without name
    print ('        Trying to resolve devices without name...', end='')
    for device in sql.execute ("SELECT * FROM Devices WHERE dev_Name IN ('(unknown)','') AND dev_LastIP <> '-'") :
        # Resolve device name
        newName = resolve_device_name (device['dev_MAC'], device['dev_LastIP'])
       
        if newName == -1 :
            notFound += 1
        elif newName == -2 :
            ignored += 1
        else :
            recordsToUpdate.append ([newName, device['dev_MAC']])
        # progress bar
        print ('.', end='')
        sys.stdout.flush()
            
    # Print log
    print ('')
    print ("        Names updated:  ", len(recordsToUpdate) )

    # update devices
    sql.executemany ("UPDATE Devices SET dev_Name = ? WHERE dev_MAC = ? ", recordsToUpdate )

#-------------------------------------------------------------------------------
def resolve_device_name (pMAC, pIP):
    try :
        pMACstr = str(pMAC)
        
        # Check MAC parameter
        mac = pMACstr.replace (':','')
        if len(pMACstr) != 17 or len(mac) != 12 :
            return -2

        # Resolve name with DIG
        dig_args = ['dig', '+short', '-x', pIP]
        newName = subprocess.check_output (dig_args, universal_newlines=True)

        # Check returns
        newName = newName.strip()
        if len(newName) == 0 :
            return -2
            
        # Eliminate local domain
        if newName.endswith('.') :
            newName = newName[:-1]
        if newName.endswith('.lan') :
            newName = newName[:-4]
        if newName.endswith('.local') :
            newName = newName[:-6]
        if newName.endswith('.home') :
            newName = newName[:-5]

        # Return newName
        return newName

    # not Found
    except subprocess.CalledProcessError :
        return -1            

#-------------------------------------------------------------------------------
def void_ghost_disconnections ():
    # Void connect ghost events (disconnect event exists in last X min.) 
    print_log ('Void - 1 Connect ghost events')
    sql.execute ("""UPDATE Events SET eve_PairEventRowid = Null,
                        eve_EventType ='VOIDED - ' || eve_EventType
                    WHERE eve_MAC != 'Internet'
                      AND eve_EventType = 'Connected'
                      AND eve_DateTime = ?
                      AND eve_MAC IN (
                          SELECT Events.eve_MAC
                          FROM CurrentScan, Devices, ScanCycles, Events 
                          WHERE cur_ScanCycle = ?
                            AND dev_MAC = cur_MAC
                            AND dev_ScanCycle = cic_ID
                            AND cic_ID = cur_ScanCycle
                            AND eve_MAC = cur_MAC
                            AND eve_EventType = 'Disconnected'
                            AND eve_DateTime >=
                                DATETIME (?, '-' || cic_EveryXmin ||' minutes')
                          ) """,
                    (startTime, cycle, startTime)   )

    # Void connect paired events
    print_log ('Void - 2 Paired events')
    sql.execute ("""UPDATE Events SET eve_PairEventRowid = Null 
                    WHERE eve_MAC != 'Internet'
                      AND eve_PairEventRowid IN (
                          SELECT Events.RowID
                          FROM CurrentScan, Devices, ScanCycles, Events 
                          WHERE cur_ScanCycle = ?
                            AND dev_MAC = cur_MAC
                            AND dev_ScanCycle = cic_ID
                            AND cic_ID = cur_ScanCycle
                            AND eve_MAC = cur_MAC
                            AND eve_EventType = 'Disconnected'
                            AND eve_DateTime >=
                                DATETIME (?, '-' || cic_EveryXmin ||' minutes')
                          ) """,
                    (cycle, startTime)   )

    # Void disconnect ghost events 
    print_log ('Void - 3 Disconnect ghost events')
    sql.execute ("""UPDATE Events SET eve_PairEventRowid = Null, 
                        eve_EventType = 'VOIDED - '|| eve_EventType
                    WHERE eve_MAC != 'Internet'
                      AND ROWID IN (
                          SELECT Events.RowID
                          FROM CurrentScan, Devices, ScanCycles, Events 
                          WHERE cur_ScanCycle = ?
                            AND dev_MAC = cur_MAC
                            AND dev_ScanCycle = cic_ID
                            AND cic_ID = cur_ScanCycle
                            AND eve_MAC = cur_MAC
                            AND eve_EventType = 'Disconnected'
                            AND eve_DateTime >=
                                DATETIME (?, '-' || cic_EveryXmin ||' minutes')
                          ) """,
                    (cycle, startTime)   )
    print_log ('Void end')

#-------------------------------------------------------------------------------
def pair_sessions_events ():
    # Pair Connection / New Device events
    print_log ('Pair session - 1 Connections / New Devices')
    sql.execute ("""UPDATE Events
                    SET eve_PairEventRowid =
                       (SELECT ROWID
                        FROM Events AS EVE2
                        WHERE EVE2.eve_EventType IN ('New Device', 'Connected',
                            'Device Down', 'Disconnected')
                           AND EVE2.eve_MAC = Events.eve_MAC
                           AND EVE2.eve_Datetime > Events.eve_DateTime
                        ORDER BY EVE2.eve_DateTime ASC LIMIT 1)
                    WHERE eve_EventType IN ('New Device', 'Connected')
                    AND eve_PairEventRowid IS NULL
                 """ )

    # Pair Disconnection / Device Down
    print_log ('Pair session - 2 Disconnections')
    sql.execute ("""UPDATE Events
                    SET eve_PairEventRowid =
                        (SELECT ROWID
                         FROM Events AS EVE2
                         WHERE EVE2.eve_PairEventRowid = Events.ROWID)
                    WHERE eve_EventType IN ('Device Down', 'Disconnected')
                      AND eve_PairEventRowid IS NULL
                 """ )
    print_log ('Pair session end')

#-------------------------------------------------------------------------------
def create_sessions_snapshot ():
    # Clean sessions snapshot
    print_log ('Sessions Snapshot - 1 Clean')
    sql.execute ("DELETE FROM SESSIONS" )

    # Insert sessions
    print_log ('Sessions Snapshot - 2 Insert')
    sql.execute ("""INSERT INTO Sessions
                    SELECT * FROM Convert_Events_to_Sessions""" )
    print_log ('Sessions end')

#-------------------------------------------------------------------------------
def skip_repeated_notifications ():
    # Skip repeated notifications
    # due strfime : Overflow --> use  "strftime / 60"
    print_log ('Skip Repeated')
    sql.execute ("""UPDATE Events SET eve_PendingAlertEmail = 0
                    WHERE eve_PendingAlertEmail = 1 AND eve_MAC IN
                        (
                        SELECT dev_MAC FROM Devices
                        WHERE dev_LastNotification IS NOT NULL
                          AND dev_LastNotification <>""
                          AND (strftime("%s", dev_LastNotification)/60 +
                                dev_SkipRepeated * 60) >
                              (strftime('%s','now','localtime')/60 )
                        )
                 """ )
    print_log ('Skip Repeated end')

#===============================================================================
# nmap Scan - DHCP Detection
#===============================================================================
def validate_dhcp_address(ip_string):
   try:
       ip_object = ipaddress.ip_address(ip_string)
       return True
   except ValueError:
       return False

# -----------------------------------------------------------------------------------
def rogue_dhcp_detection ():
    print_log ('Looking for Rogue DHCP Servers')
    # Create Table is not exist
    sql_create_table = """ CREATE TABLE IF NOT EXISTS Nmap_DHCP_Server(
                                scan_num INTEGER NOT NULL,
                                dhcp_server TEXT NOT NULL
                            ); """
    sql.execute(sql_create_table)
    sql_connection.commit()

    # Flush Table
    sql.execute("DELETE FROM Nmap_DHCP_Server")
    sql_connection.commit()

    # Execute 10 probes and insert in list
    dhcp_probes = 15
    dhcp_server_list = []
    dhcp_server_list.append(strftime("%Y-%m-%d %H:%M:%S"))
    for _ in range(dhcp_probes):
        stream = os.popen('sudo nmap --script broadcast-dhcp-discover 2>/dev/null | grep "Server Identifier" | awk \'{ print $4 }\'')
        output = stream.read()
        dhcp_server_list.append(output.replace("\n", ""))

    for i in range(len(dhcp_server_list)):
        # Insert list in database
        sqlite_insert = """INSERT INTO Nmap_DHCP_Server
                         (scan_num, dhcp_server) 
                         VALUES (?, ?);"""

        table_data = (i, dhcp_server_list[i])
        sql.execute(sqlite_insert, table_data)
    
    sql_connection.commit()

    rogue_dhcp_notification ()

# -----------------------------------------------------------------------------------
def rogue_dhcp_notification ():
    sql.execute("SELECT DISTINCT dhcp_server FROM Nmap_DHCP_Server")
    rows = sql.fetchall()

    rogue_dhcp_server_list = []

    if len(rows) == 1:
        print ('    No DHCP Server detected.')

    if len(rows) == 2:
        if validate_dhcp_address(rows[1][0]):
            if rows[1][0] == DHCP_SERVER_ADDRESS :
                print ('    One DHCP Server detected......: ' + rows[1][0] + ' (valid)')
            else:
                print ('    One DHCP Server detected......: ' + rows[1][0] + ' (invalid)')
                rogue_dhcp_server_list.append(rows[1][0])
        else:
            print ('    Detection Error')

    if len(rows) > 2:
        print ('    Multiple DHCP Servers detected:')
        for i in range(1,len(rows),1):
            if validate_dhcp_address(rows[i][0]):
                if rows[i][0] == DHCP_SERVER_ADDRESS :
                    print ('        ' + rows[i][0] + ' (valid)' )
                else:
                    print ('        ' + rows[i][0] + ' (rogue)' )
                    rogue_dhcp_server_list.append(rows[i][0])
            else:
                print ('    Detection Error')

    rogue_dhcp_reports = glob.glob(REPORTPATH_WEBGUI + "*Rogue DHCP Server*.txt")    

    if rogue_dhcp_server_list and not rogue_dhcp_reports:
        rogue_dhcp_server_string = "Report Date: " + rows[0][0] + "\nServer: " + socket.gethostname() + "\n\nRogue DHCP Server\nDetected Server(s): "
        rogue_dhcp_server_string += ', '.join(rogue_dhcp_server_list)

        # Send Mail
        if REPORT_MAIL or REPORT_MAIL_WEBMON:
            print ('    Sending report by email...')
            send_email (rogue_dhcp_server_string, rogue_dhcp_server_string)
        else :
            print ('    Skip mail...')
        if REPORT_PUSHSAFER or REPORT_PUSHSAFER_WEBMON:
            print ('    Sending report by PUSHSAFER...')
            send_pushsafer (rogue_dhcp_server_string)
        else :
            print ('    Skip PUSHSAFER...')
        if REPORT_PUSHOVER or REPORT_PUSHOVER_WEBMON:
            print ('    Sending report by PUSHOVER...')
            send_pushover (rogue_dhcp_server_string)
        else :
            print ('    Skip PUSHOVER...')
        if REPORT_NTFY or REPORT_NTFY_WEBMON:
            print ('    Sending report by NTFY...')
            send_ntfy (rogue_dhcp_server_string)
        else :
            print ('    Skip NTFY...')
        if REPORT_TELEGRAM or REPORT_TELEGRAM_WEBMON:
            print ('    Sending report by Telegram...')
            send_telegram (rogue_dhcp_server_string)
        if REPORT_WEBGUI or REPORT_WEBGUI_WEBMON:
            print ('    Save report to file...')
            send_webgui (rogue_dhcp_server_string)
        else :
            print ('    Skip Telegram...')

#===============================================================================
# nmap Scan of a single device (inactive)
# Maybe outsource to an extra script because of longer runtime
#===============================================================================
def prepare_nmap_env ():
    # create table in db
    sql_create_table = """ CREATE TABLE IF NOT EXISTS nmap_scan_cur(
                                mac TEXT NOT NULL,
                                scan_time TEXT NOT NULL,
                                port_protocol TEXT NOT NULL,
                                port_status TEXT NOT NULL,
                                port_description TEXT NOT NULL,
                            ); """
    sql.execute(sql_create_table)

    sql_create_table = """ CREATE TABLE IF NOT EXISTS nmap_scan_prev(
                                mac TEXT NOT NULL,
                                scan_time TEXT NOT NULL,
                                port_protocol TEXT NOT NULL,
                                port_status TEXT NOT NULL,
                                port_description TEXT NOT NULL,
                            ); """
    sql.execute(sql_create_table)

#-------------------------------------------------------------------------------
def use_nmap_regex(_nmap_raw_result):
    # Filter for relevant lines from output
    pattern = re.compile(r"^.*\d\d/.*$", re.IGNORECASE)
    return pattern.match(_nmap_raw_result)

#-------------------------------------------------------------------------------
def execute_nmap_scan(_IP):
    # nmap scan
    stream = os.popen('nmap -n -p -10000 ' + _IP)
    output = stream.read()
    nmap_scan = output.split("\n")
    # get MAC of current ip. Both should be insert in the Database for a unique host
    process_nmap_scan(nmap_scan)

#-------------------------------------------------------------------------------
def process_nmap_scan(_nmap_result):
    # apply filter on output
    for x in range(len(_nmap_result)):
        if use_nmap_regex(_nmap_result[x]):
            # split filtered lines and remove empty elements from list
            # not tested with python 2
            temp = _nmap_result[x].split(" ")
            temp = list(filter(None, temp))

            # processing results (maybe write to db or to temp file)
            # compare to prev scan or not?
            # maybe INSERT INTO old_scan SELECT * FROM current_scan;
            # Do not write intermediate results to the DB immediately, but to a temporary variable/list first, to have only one big DB operation at the end and not many small ones during runtime.
            #print(temp[0] + " - " + temp[2])
            
            # ============================
            # Actual Output for IP (DEMO)|
            # ----------------------------
            # 21/tcp - ftp               |
            # 53/tcp - domain            |
            # 80/tcp - http              |
            # 443/tcp - https            |
            # 5060/tcp - sip             |
            # 8181/tcp - intermapper     |
            # ============================

# DEBUG
#execute_nmap_scan(IP)

#===============================================================================
# Services Monitoring
#===============================================================================
def set_service_update(_mon_URL, _mon_lastScan, _mon_lastStatus, _mon_lastLatence, _mon_TargetIP, _mon_Redirect):

    if _mon_Redirect != 200 and _mon_lastStatus == 200:
        _mon_Redirect_Text = "Redirected by " + str(_mon_Redirect)
    else:
        _mon_Redirect_Text = ""

    sqlite_insert = """UPDATE Services SET mon_LastScan=?, mon_LastStatus=?, mon_LastLatency=?, mon_TargetIP=?, mon_Notes=? WHERE mon_URL=?;"""

    table_data = (_mon_lastScan, _mon_lastStatus, _mon_lastLatence, _mon_TargetIP, _mon_Redirect_Text, _mon_URL)
    sql.execute(sqlite_insert, table_data)
    sql_connection.commit()

# -----------------------------------------------------------------------------
def set_services_events(_moneve_URL, _moneve_DateTime, _moneve_StatusCode, _moneve_Latency, _moneve_TargetIP):

    sqlite_insert = """INSERT INTO Services_Events
                     (moneve_URL, moneve_DateTime, moneve_StatusCode, moneve_Latency, moneve_TargetIP) 
                     VALUES (?, ?, ?, ?, ?);"""

    table_data = (_moneve_URL, _moneve_DateTime, _moneve_StatusCode, _moneve_Latency, _moneve_TargetIP)
    sql.execute(sqlite_insert, table_data)
    sql_connection.commit()

# -----------------------------------------------------------------------------
def set_services_current_scan(_cur_URL, _cur_DateTime, _cur_StatusCode, _cur_Latency, _cur_TargetIP):

    sql.execute("SELECT * FROM Services WHERE mon_URL = ?", [_cur_URL])
    rows = sql.fetchall()
    for row in rows:
        _mon_AlertEvents = row[6]
        _mon_AlertDown = row[7]
        _mon_StatusCode = row[2]
        _mon_Latency = row[3]
        _mon_TargetIP = row[8]

    if _mon_TargetIP != _cur_TargetIP:
        _cur_StatusChanged = 1
    elif _mon_StatusCode != _cur_StatusCode:
        _cur_StatusChanged = 1
    else:
        _cur_StatusChanged = 0

    if _mon_Latency == "99999999" and _mon_Latency != _cur_Latency:
        _cur_LatencyChanged = 0
        _cur_StatusChanged = 1
    elif _cur_Latency == "99999999" and _mon_Latency != _cur_Latency:
        _cur_LatencyChanged = 1
    else:
        _cur_LatencyChanged = 0 

    sqlite_insert = """INSERT INTO Services_CurrentScan
                     (cur_URL, cur_DateTime, cur_StatusCode, cur_Latency, cur_AlertEvents, cur_AlertDown, cur_StatusChanged, cur_LatencyChanged, cur_TargetIP, cur_StatusCode_prev, cur_TargetIP_prev) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);"""

    table_data = (_cur_URL, _cur_DateTime, _cur_StatusCode, _cur_Latency, _mon_AlertEvents, _mon_AlertDown, _cur_StatusChanged, _cur_LatencyChanged, _cur_TargetIP, _mon_StatusCode, _mon_TargetIP)
    sql.execute(sqlite_insert, table_data)
    sql_connection.commit()

# -----------------------------------------------------------------------------
def service_monitoring_log(site, status, latency):
    status_str = str(status)

    # Log status message to log file
    with open(PIALERT_WEBSERVICES_LOG, 'a') as monitor_logfile:
        monitor_logfile.write("{} |        {} |     {} | {}\n".format(strftime("%Y-%m-%d %H:%M:%S"),
                                                status_str.zfill(3),
                                                latency,
                                                site
                                                )
                             )

# -----------------------------------------------------------------------------
def check_services_health(site):
    # Enable self signed SSL
    requests.packages.urllib3.disable_warnings(InsecureRequestWarning)
    try:
        resp = requests.get(site, verify=False, timeout=10)
        latency = resp.elapsed
        latency_str = str(latency)
        latency_str_seconds = latency_str.split(":")
        format_latency_str = latency_str_seconds[2]
        if format_latency_str[0] == "0" and format_latency_str[1] != "." :
            format_latency_str = format_latency_str[1:]
        return resp.status_code, format_latency_str
    except requests.exceptions.SSLError:
        pass
    except:
        # Latency for offline services
        latency = "99999999"
        # HTTP Status Code for offline services
        return 0, latency

# -----------------------------------------------------------------------------
def check_services_redirect(site):
    # Enable self signed SSL
    requests.packages.urllib3.disable_warnings(InsecureRequestWarning)
    try:
        resp = requests.get(site, verify=False, timeout=10, allow_redirects=False)
        return resp.status_code
    except requests.exceptions.SSLError:
        pass
    except:
        # HTTP Status Code for offline services
        return 0

# -----------------------------------------------------------------------------
def get_services_list():

    with open(PIALERT_WEBSERVICES_LOG, 'a') as monitor_logfile:
        monitor_logfile.write("    Get Services List\n")
        monitor_logfile.close()

    sql.execute("SELECT mon_URL FROM Services")
    rows = sql.fetchall()

    sites = []
    for row in rows:
        sites.append(row[0])

    return sites

# -----------------------------------------------------------------------------
def flush_services_current_scan():

    with open(PIALERT_WEBSERVICES_LOG, 'a') as monitor_logfile:
        monitor_logfile.write("    Flush previous scan results\n")
        monitor_logfile.close()

    sql.execute("DELETE FROM Services_CurrentScan")
    sql_connection.commit()

# -----------------------------------------------------------------------------
def print_service_monitoring_changes():

    print("    Services Monitoring Changes...")
    changedStatusCode = sql.execute("SELECT COUNT() FROM Services_CurrentScan WHERE cur_StatusChanged = 1").fetchone()[0]
    print("        Changed StatusCodes.....:", str(changedStatusCode))
    changedLatency = sql.execute("SELECT COUNT() FROM Services_CurrentScan WHERE cur_LatencyChanged = 1").fetchone()[0]
    print("        Changed Reachability....:", str(changedLatency))
    with open(PIALERT_WEBSERVICES_LOG, 'a') as monitor_logfile:
        monitor_logfile.write("\nServices Monitoring Changes:\n")
        monitor_logfile.write("    Changed StatusCodes.....: " + str(changedStatusCode))
        monitor_logfile.write("\n    Changed Reachability....: " + str(changedLatency))
        monitor_logfile.write("\n")
        monitor_logfile.close()

# -----------------------------------------------------------------------------
def service_monitoring_notification():
    global mail_text_webservice
    global mail_html_webservice
    
    # Reporting section
    print ('\nReporting (Web Services) ...')

    # Open text Template
    template_file = open(PIALERT_BACK_PATH + '/report_template_webservice.txt', 'r') 
    mail_text_webservice = template_file.read() 
    template_file.close() 

    # Open html Template
    template_file = open(PIALERT_BACK_PATH + '/report_template_webservice.html', 'r') 
    mail_html_webservice = template_file.read() 
    template_file.close() 

    # Report Header & footer
    timeFormated = startTime.strftime ('%Y-%m-%d %H:%M')
    mail_text_webservice = mail_text_webservice.replace ('<REPORT_DATE>', timeFormated)
    mail_html_webservice = mail_html_webservice.replace ('<REPORT_DATE>', timeFormated)

    mail_text_webservice = mail_text_webservice.replace ('<SERVER_NAME>', socket.gethostname() )
    mail_html_webservice = mail_html_webservice.replace ('<SERVER_NAME>', socket.gethostname() )

    # Compose Devices Down Section
    mail_section_services_down = False
    mail_text_services_down = ''
    mail_html_services_down = ''
    text_line_template = '{}{}\n\t{}\t\t\t{}\n\t{}\t{}\n\t{}\t{}\n\n'
    html_line_template     = '<tr>\n'+ \
        '  <td> {} </td>\n  <td> {} </td>\n'+ \
        '  <td> {} </td>\n  <td> {} </td>\n</tr>\n'

    sql.execute ("""SELECT * FROM Services_CurrentScan
                    WHERE cur_AlertDown = 1 AND cur_LatencyChanged = 1
                    ORDER BY cur_DateTime""")

    for eventAlert in sql :
        if eventAlert['cur_TargetIP'] == '':
            _func_cur_TargetIP = 'n.a.'
        else:
            _func_cur_TargetIP = eventAlert['cur_TargetIP']
        if eventAlert['cur_TargetIP_prev'] == '':
            _func_cur_TargetIP_prev = 'n.a.'
        else:
            _func_cur_TargetIP_prev = eventAlert['cur_TargetIP_prev']

        mail_section_services_down = True
        mail_text_services_down += text_line_template.format (
            'Service: ', eventAlert['cur_URL'], 
            'Time: ', eventAlert['cur_DateTime'], 
            'Destination IP: ', _func_cur_TargetIP,
            'prev. Destination IP: ', _func_cur_TargetIP_prev)
        mail_html_services_down += html_line_template.format (
            eventAlert['cur_URL'], eventAlert['cur_DateTime'], _func_cur_TargetIP, _func_cur_TargetIP_prev)

    format_report_section_services (mail_section_services_down, 'SECTION_DEVICES_DOWN',
        'TABLE_DEVICES_DOWN', mail_text_services_down, mail_html_services_down)

    # Compose Events Section (includes Down as an Event)
    mail_section_events = False
    mail_text_events   = ''
    mail_html_events   = ''
    text_line_template = '{}{}\n\t{}\t\t\t{}\n\t{}\t{}\n\t{}\t{}\n\t{}\t{}\n\t{}{}\n\n'
    html_line_template = '<tr>\n  <td>'+ \
            '  {} </td>\n  <td> {} </td>\n'+ \
            '  <td> {} </td>\n  <td> {} </td>\n  <td> {} </td>\n  <td> {} </td>\n'+ \
            '  <td> {} </td>\n</tr>\n'

    sql.execute ("""SELECT * FROM Services_CurrentScan
                    WHERE cur_AlertEvents = 1 AND cur_StatusChanged = 1
                    ORDER BY cur_DateTime""")

    for eventAlert in sql :
        if eventAlert['cur_TargetIP'] == '':
            _func_cur_TargetIP = 'n.a.'
        else:
            _func_cur_TargetIP = eventAlert['cur_TargetIP']
        if eventAlert['cur_TargetIP_prev'] == '':
            _func_cur_TargetIP_prev = 'n.a.'
        else:
            _func_cur_TargetIP_prev = eventAlert['cur_TargetIP_prev']

        mail_section_events = True
        mail_text_events += text_line_template.format (
            'Service: ', eventAlert['cur_URL'], 
            'Time: ', eventAlert['cur_DateTime'], 
            'Destination IP: ', _func_cur_TargetIP,
            'prev. Destination IP: ', _func_cur_TargetIP_prev, 
            'HTTP Status Code: ', eventAlert['cur_StatusCode'], 
            'prev. HTTP Status Code: ', eventAlert['cur_StatusCode_prev'])
        mail_html_events += html_line_template.format (
            eventAlert['cur_URL'], eventAlert['cur_Latency'], _func_cur_TargetIP,
            _func_cur_TargetIP_prev, eventAlert['cur_DateTime'], eventAlert['cur_StatusCode'],
            eventAlert['cur_StatusCode_prev'])

    format_report_section_services (mail_section_events, 'SECTION_EVENTS',
        'TABLE_EVENTS', mail_text_events, mail_html_events)

    # # Send Mail
    if mail_section_services_down == True or mail_section_events == True :
        if REPORT_MAIL_WEBMON :
            print ('    Sending report by email...')
            send_email (mail_text_webservice, mail_html_webservice)
        else :
            print ('    Skip mail...')
        if REPORT_PUSHSAFER_WEBMON :
            print ('    Sending report by PUSHSAFER...')
            send_pushsafer (mail_text_webservice)
        else :
            print ('    Skip PUSHSAFER...')
        if REPORT_PUSHOVER :
            print ('    Sending report by PUSHOVER...')
            send_pushover (mail_text_webservice)
        else :
            print ('    Skip PUSHOVER...')
        if REPORT_TELEGRAM_WEBMON :
            print ('    Sending report by Telegram...')
            send_telegram (mail_text_webservice)
        else :
            print ('    Skip Telegram...')
        if REPORT_NTFY_WEBMON :
            print ('    Sending report by NTFY...')
            send_ntfy (mail_text_webservice)
        else :
            print ('    Skip NTFY...')
        if REPORT_WEBGUI_WEBMON :
            print ('    Save report to file...')
            send_webgui (mail_text_webservice)
        else :
            print ('    Skip WebUI...')
    else :
        print ('    No changes to report...')

    # Commit changes
    sql_connection.commit()

# -----------------------------------------------------------------------------
def service_monitoring():
    global VERSION
    global VERSION_DATE

    # Empty Log and write new header
    print("\nStart Services Monitoring...")
    print("    Prepare Logfile...")
    with open(PIALERT_WEBSERVICES_LOG, 'w') as monitor_logfile:
        monitor_logfile.write("\nPi.Alert " + VERSION + " (" + VERSION_DATE + "):\n---------------------------------------------------------\n")
        monitor_logfile.write("Current User: %s \n\n" % get_username())
        monitor_logfile.write("Monitor Web-Services\n")
        monitor_logfile.write("    Timestamp: " + strftime("%Y-%m-%d %H:%M:%S") + "\n")
        monitor_logfile.close()

    print("    Get Services List...")
    sites = get_services_list()

    print("    Flush previous scan results...")
    flush_services_current_scan()

    print("    Check Services...")
    with open(PIALERT_WEBSERVICES_LOG, 'a') as monitor_logfile:
        monitor_logfile.write("\nStart Services Monitoring\n\n Timestamp          | StatusCode | ResponseTime | URL \n-----------------------------------------------------------------\n") 
        monitor_logfile.close()

    while sites:
        for site in sites:
            status,latency = check_services_health(site)
            site_retry = ''
            if latency == "99999999" :
                # 2nd Retry if the first attempt fails
                status,latency = check_services_health(site)
                site_retry = '*'
                if latency == "99999999" :
                    # 3rd Retry if the second attempt fails
                    status,latency = check_services_health(site)
                    site_retry = '**'

            scantime = strftime("%Y-%m-%d %H:%M:%S")

            #Get IP from Domain
            if latency != "99999999":
                redirect_state = check_services_redirect(site)
                domain = urlparse(site).netloc
                domain = domain.split(":")[0]
                #print(domain)
                domain_ip = socket.gethostbyname(domain)
            else:
                domain_ip = ""
                redirect_state = ""
            # Write Logfile
            service_monitoring_log(site + ' ' + site_retry, status, latency)
            # Insert Services_Events with moneve_URL, moneve_DateTime, moneve_StatusCode and moneve_Latency
            set_services_events(site, scantime, status, latency, domain_ip)
            # Insert Services_CurrentScan with moneve_URL, moneve_DateTime, moneve_StatusCode and moneve_Latency
            set_services_current_scan(site, scantime, status, latency, domain_ip)

            sys.stdout.flush()

            # Update Service with lastLatence, lastScan and lastStatus after compare with services_current_scan
            set_service_update(site, scantime, status, latency, domain_ip, redirect_state)
        break

    else:
        print("    No site(s) to monitor!")
        with open(PIALERT_WEBSERVICES_LOG, 'a') as monitor_logfile:
            monitor_logfile.write("\n**************** No site(s) to monitor!! ****************\n")
            monitor_logfile.close()

    # Print to log file
    print_service_monitoring_changes()

#===============================================================================
# REPORTING
#===============================================================================
def email_reporting ():
    global mail_text
    global mail_html
    
    # Reporting section
    print ('\nReporting...')
    openDB()

    # Disable reporting on events for devices where reporting is disabled based on the MAC address
    sql.execute ("""UPDATE Events SET eve_PendingAlertEmail = 0
                    WHERE eve_PendingAlertEmail = 1 AND eve_EventType != 'Device Down' AND eve_MAC IN
                        (
                            SELECT dev_MAC FROM Devices WHERE dev_AlertEvents = 0 
                        )""")
    sql.execute ("""UPDATE Events SET eve_PendingAlertEmail = 0
                    WHERE eve_PendingAlertEmail = 1 AND eve_EventType = 'Device Down' AND eve_MAC IN
                        (
                            SELECT dev_MAC FROM Devices WHERE dev_AlertDeviceDown = 0 
                        )""")

    # Open text Template
    template_file = open(PIALERT_BACK_PATH + '/report_template.txt', 'r') 
    mail_text = template_file.read() 
    template_file.close() 

    # Open html Template
    template_file = open(PIALERT_BACK_PATH + '/report_template.html', 'r') 
    mail_html = template_file.read() 
    template_file.close() 

    # Report Header & footer
    timeFormated = startTime.strftime ('%Y-%m-%d %H:%M')
    mail_text = mail_text.replace ('<REPORT_DATE>', timeFormated)
    mail_html = mail_html.replace ('<REPORT_DATE>', timeFormated)

    mail_text = mail_text.replace ('<SERVER_NAME>', socket.gethostname() )
    mail_html = mail_html.replace ('<SERVER_NAME>', socket.gethostname() )

    # Compose Internet Section
    print ('    Formating report...')
    mail_section_Internet = False
    mail_text_Internet = ''
    mail_html_Internet = ''
    text_line_template = '{} \t{}\t{}\t{}\n'
    html_line_template = '<tr>\n'+ \
        '  <td> <a href="{}{}"> {} </a> </td>\n  <td> {} </td>\n'+ \
        '  <td style="font-size: 24px; color:#D02020"> {} </td>\n'+ \
        '  <td> {} </td>\n</tr>\n'

    sql.execute ("""SELECT * FROM Events
                    WHERE eve_PendingAlertEmail = 1 AND eve_MAC = 'Internet'
                    ORDER BY eve_DateTime""")

    for eventAlert in sql :
        mail_section_Internet = True
        mail_text_Internet += text_line_template.format (
            eventAlert['eve_EventType'], eventAlert['eve_DateTime'],
            eventAlert['eve_IP'], eventAlert['eve_AdditionalInfo'])
        mail_html_Internet += html_line_template.format (
            REPORT_DEVICE_URL, eventAlert['eve_MAC'],
            eventAlert['eve_EventType'], eventAlert['eve_DateTime'],
            eventAlert['eve_IP'], eventAlert['eve_AdditionalInfo'])

    format_report_section (mail_section_Internet, 'SECTION_INTERNET',
        'TABLE_INTERNET', mail_text_Internet, mail_html_Internet)

    # Compose New Devices Section
    mail_section_new_devices = False
    mail_text_new_devices = ''
    mail_html_new_devices = ''
    text_line_template = '{}\t{}\n\t{}\t\t{}\n\t{}\t\t{}\n\t{}\t\t{}\n\t{}\t{}\n\n'
    html_line_template    = '<tr>\n'+ \
        '  <td> <a href="{}{}"> {} </a> </td>\n  <td> {} </td>\n'+\
        '  <td> {} </td>\n  <td> {} </td>\n  <td> {} </td>\n</tr>\n'
    
    sql.execute ("""SELECT * FROM Events_Devices
                    WHERE eve_PendingAlertEmail = 1
                      AND eve_EventType = 'New Device'
                    ORDER BY eve_DateTime""")

    for eventAlert in sql :
        mail_section_new_devices = True
        mail_text_new_devices += text_line_template.format (
            'Name: ', eventAlert['dev_Name'], 'MAC: ', eventAlert['eve_MAC'], 'IP: ', eventAlert['eve_IP'],
            'Time: ', eventAlert['eve_DateTime'], 'More Info: ', eventAlert['eve_AdditionalInfo'])
        mail_html_new_devices += html_line_template.format (
            REPORT_DEVICE_URL, eventAlert['eve_MAC'], eventAlert['eve_MAC'],
            eventAlert['eve_DateTime'], eventAlert['eve_IP'],
            eventAlert['dev_Name'], eventAlert['eve_AdditionalInfo'])

    format_report_section (mail_section_new_devices, 'SECTION_NEW_DEVICES',
        'TABLE_NEW_DEVICES', mail_text_new_devices, mail_html_new_devices)

    # Compose Devices Down Section
    mail_section_devices_down = False
    mail_text_devices_down = ''
    mail_html_devices_down = ''
    text_line_template = '{}\t{}\n\t{}\t{}\n\t{}\t{}\n\t{}\t{}\n\n'
    html_line_template     = '<tr>\n'+ \
        '  <td> <a href="{}{}"> {} </a>  </td>\n  <td> {} </td>\n'+ \
        '  <td> {} </td>\n  <td> {} </td>\n</tr>\n'

    sql.execute ("""SELECT * FROM Events_Devices
                    WHERE eve_PendingAlertEmail = 1
                      AND eve_EventType = 'Device Down'
                    ORDER BY eve_DateTime""")

    for eventAlert in sql :
        mail_section_devices_down = True
        mail_text_devices_down += text_line_template.format (
            'Name: ', eventAlert['dev_Name'], 'MAC: ', eventAlert['eve_MAC'],
            'Time: ', eventAlert['eve_DateTime'],'IP: ', eventAlert['eve_IP'])
        mail_html_devices_down += html_line_template.format (
            REPORT_DEVICE_URL, eventAlert['eve_MAC'], eventAlert['eve_MAC'],
            eventAlert['eve_DateTime'], eventAlert['eve_IP'],
            eventAlert['dev_Name'])

    format_report_section (mail_section_devices_down, 'SECTION_DEVICES_DOWN',
        'TABLE_DEVICES_DOWN', mail_text_devices_down, mail_html_devices_down)

    # Compose Events Section
    mail_section_events = False
    mail_text_events   = ''
    mail_html_events   = ''
    text_line_template = '{}\t{}\n\t{}\t\t{}\n\t{}\t\t{}\n\t{}\t\t{}\n\t{}\t\t{}\n\t{}\t{}\n\n'
    html_line_template = '<tr>\n  <td>'+ \
            ' <a href="{}{}"> {} </a> </td>\n  <td> {} </td>\n'+ \
            '  <td> {} </td>\n  <td> {} </td>\n  <td> {} </td>\n'+ \
            '  <td> {} </td>\n</tr>\n'

    sql.execute ("""SELECT * FROM Events_Devices
                    WHERE eve_PendingAlertEmail = 1
                      AND eve_EventType IN ('Connected','Disconnected',
                          'IP Changed')
                    ORDER BY eve_DateTime""")

    for eventAlert in sql :
        mail_section_events = True
        mail_text_events += text_line_template.format (
            'Name: ', eventAlert['dev_Name'], 'MAC: ', eventAlert['eve_MAC'], 
            'IP: ', eventAlert['eve_IP'],'Time: ', eventAlert['eve_DateTime'],
            'Event: ', eventAlert['eve_EventType'],'More Info: ', eventAlert['eve_AdditionalInfo'])
        mail_html_events += html_line_template.format (
            REPORT_DEVICE_URL, eventAlert['eve_MAC'], eventAlert['eve_MAC'],
            eventAlert['eve_DateTime'], eventAlert['eve_IP'],
            eventAlert['eve_EventType'], eventAlert['dev_Name'],
            eventAlert['eve_AdditionalInfo'])

    format_report_section (mail_section_events, 'SECTION_EVENTS',
        'TABLE_EVENTS', mail_text_events, mail_html_events)

    # Send Mail
    if mail_section_Internet == True or mail_section_new_devices == True \
    or mail_section_devices_down == True or mail_section_events == True :
        # Send Mail
        if REPORT_MAIL :
            print ('    Sending report by email...')
            send_email (mail_text, mail_html)
        else :
            print ('    Skip mail...')
        # Send Pushsafer
        if REPORT_PUSHSAFER :
            print ('    Sending report by PUSHSAFER...')
            send_pushsafer (mail_text)
        else :
            print ('    Skip PUSHSAFER...')
        # Send Pushover
        if REPORT_PUSHOVER :
            print ('    Sending report by PUSHOVER...')
            send_pushover (mail_text)
        else :
            print ('    Skip PUSHOVER...')
        # Send Telegram
        if REPORT_TELEGRAM :
            print ('    Sending report by Telegram...')
            send_telegram (mail_text)
        else :
            print ('    Skip Telegram...')
        # Send NTFY
        if REPORT_NTFY :
            print ('    Sending report by NTFY...')
            send_ntfy (mail_text)
        else :
            print ('    Skip NTFY...')
        # Send WebGUI
        if REPORT_WEBGUI :
            print ('    Save report to file...')
            send_webgui (mail_text)
        else :
            print ('    Skip WebGUI...')
    else :
        print ('    No changes to report...')

    # Clean Pending Alert Events
    sql.execute ("""UPDATE Devices SET dev_LastNotification = ?
                    WHERE dev_MAC IN (SELECT eve_MAC FROM Events
                                      WHERE eve_PendingAlertEmail = 1)
                 """, (datetime.datetime.now(),) )
    sql.execute ("""UPDATE Events SET eve_PendingAlertEmail = 0
                    WHERE eve_PendingAlertEmail = 1""")

    print ('    Notifications:', sql.rowcount)

    # Commit changes
    sql_connection.commit()

    try:
        enable_services_monitoring = SCAN_WEBSERVICES
    except NameError:
        enable_services_monitoring = False

    if enable_services_monitoring == True:
        if str(startTime)[15] == "0":
            service_monitoring_notification()

    closeDB()

#-------------------------------------------------------------------------------
def send_pushsafer (_Text):
    try:
        notification_target = PUSHSAFER_DEVICE
    except NameError:
        notification_target = "a"

    # Remove one linebrake between "Server" and the headline of the event type
    _pushsafer_Text = _Text.replace('\n\n\n', '\n\n')
    # extract event type headline to use it in the notification headline
    findsubheadline = _pushsafer_Text.split('\n')
    subheadline = findsubheadline[3]
    url = 'https://www.pushsafer.com/api'
    post_fields = {
        "t" : 'Pi.Alert Message - '+subheadline,
        "m" : _pushsafer_Text,
        "s" : 22,
        "v" : 3,
        "i" : 148,
        "c" : '#ef7f7f',
        "d" : notification_target,
        "u" : REPORT_DASHBOARD_URL,
        "ut" : 'Open Pi.Alert',
        "k" : PUSHSAFER_TOKEN,
        }
    requests.post(url, data=post_fields)

#-------------------------------------------------------------------------------
def send_pushover (_Text):
    # Remove one linebrake between "Server" and the headline of the event type
    _pushover_Text = _Text.replace('\n\n\n', '\n\n')
    # Text-layout tweak
    _pushover_Text = _pushover_Text.replace('IP: \t\t', 'IP: \t\t\t')
    # extract event type headline to use it in the notification headline
    findsubheadline = _pushover_Text.split('\n')
    subheadline = findsubheadline[3]
    url = 'https://api.pushover.net/1/messages.json'
    post_fields = {
        "token": PUSHOVER_TOKEN,
        "user": PUSHOVER_USER,
        "title" : 'Pi.Alert Message - '+subheadline,
        "message" : _pushover_Text,
        }
    requests.post(url, data=post_fields)

#-------------------------------------------------------------------------------
def send_ntfy (_Text):
    # Prepare header
    headers = {
        "Title": "Pi.Alert Notification",
        "Click": REPORT_DASHBOARD_URL,
        "Priority": NTFY_PRIORITY,
        "Tags": "warning"
    }
    # if username and password are set generate hash and update header
    if NTFY_USER != "" and NTFY_PASSWORD != "":
    # Generate hash for basic auth
        usernamepassword = "{}:{}".format(NTFY_USER,NTFY_PASSWORD)
        basichash = b64encode(bytes(NTFY_USER + ':' + NTFY_PASSWORD, "utf-8")).decode("ascii")

    # add authorization header with hash
        headers["Authorization"] = "Basic {}".format(basichash)

    requests.post("{}/{}".format( NTFY_HOST, NTFY_TOPIC),
    data=_Text,
    headers=headers)

#-------------------------------------------------------------------------------
def send_telegram (_Text):
    # Remove one linebrake between "Server" and the headline of the event type
    _telegram_Text = _Text.replace('\n\n\n', '\n\n')
    # extract event type headline to use it in the notification headline
    findsubheadline = _telegram_Text.split('\n')
    subheadline = findsubheadline[3]
    runningpath = os.path.abspath(os.path.dirname(__file__))
    stream = os.popen(runningpath+'/shoutrrr/'+SHOUTRRR_BINARY+'/shoutrrr send --url "'+TELEGRAM_BOT_TOKEN_URL+'" --message "'+_telegram_Text+'" --title "Pi.Alert - '+subheadline+'"')

#-------------------------------------------------------------------------------
def send_webgui (_Text):
    # Remove one linebrake between "Server" and the headline of the event type
    _webgui_Text = _Text.replace('\n\n\n', '\n\n')
    # extract event type headline to use it in the notification headline
    findsubheadline = _webgui_Text.split('\n')
    subheadline = findsubheadline[3]
    _webgui_filename = time.strftime("%Y%m%d-%H%M%S") + "_" + subheadline + ".txt"
    if (os.path.exists(REPORTPATH_WEBGUI + _webgui_filename) == False):
        f = open(REPORTPATH_WEBGUI + _webgui_filename, "w")
        f.write(_webgui_Text)
        f.close()
    set_pia_reports_permissions()

#===============================================================================
# Test REPORTING
#===============================================================================
def email_reporting_test (_Mode):
    if _Mode == 'Test' :
        notiMessage = "Test-Notification"
    elif _Mode == 'noti_Timerstart' :
        notiMessage = "Pi.Alert is paused"
    elif _Mode == 'noti_Timerstop' :
        notiMessage = "Pi.Alert reactivated"

    # Reporting section
    print ('\nTest Reporting...')
    # Open text Template

    # Send Mail
    if REPORT_MAIL or REPORT_MAIL_WEBMON:
        print ('    Sending report by email...')
        send_email (notiMessage, notiMessage)
    else :
        print ('    Skip mail...')
    # Send Pushsafer
    if REPORT_PUSHSAFER or REPORT_PUSHSAFER_WEBMON:
        print ('    Sending report by PUSHSAFER...')
        send_pushsafer_test (notiMessage)
    else :
        print ('    Skip PUSHSAFER...')
    # Send Pushover
    if REPORT_PUSHOVER or REPORT_PUSHOVER_WEBMON:
        print ('    Sending report by PUSHOVER...')
        send_pushover_test (notiMessage)
    else :
        print ('    Skip PUSHOVER...')
    # Send Telegram
    if REPORT_TELEGRAM or REPORT_TELEGRAM_WEBMON:
        print ('    Sending report by Telegram...')
        send_telegram_test (notiMessage)
    else :
        print ('    Skip Telegram...')
    # Send NTFY
    if REPORT_NTFY or REPORT_NTFY_WEBMON:
        print ('    Sending report by NTFY...')
        send_ntfy_test (notiMessage)
    else :
        print ('    Skip NTFY...')
    # Send WebGUI
    if REPORT_WEBGUI or REPORT_WEBGUI_WEBMON:
        print ('    Save report to file...')
        send_webgui_test (notiMessage)
    else :
        print ('    Skip WebGUI...')        
    return 0

#-------------------------------------------------------------------------------
def send_ntfy_test (_notiMessage):
    headers = {
        "Title": "Pi.Alert Notification",
        "Click": REPORT_DASHBOARD_URL,
        "Priority": NTFY_PRIORITY,
        "Tags": "warning"
    }
    # if username and password are set generate hash and update header
    if NTFY_USER != "" and NTFY_PASSWORD != "":
    # Generate hash for basic auth
        usernamepassword = "{}:{}".format(NTFY_USER,NTFY_PASSWORD)
        basichash = b64encode(bytes(NTFY_USER + ':' + NTFY_PASSWORD, "utf-8")).decode("ascii")

    # add authorization header with hash
        headers["Authorization"] = "Basic {}".format(basichash)

    requests.post("{}/{}".format( NTFY_HOST, NTFY_TOPIC),
    data=_notiMessage,
    headers=headers)

#-------------------------------------------------------------------------------
def send_pushsafer_test (_notiMessage):
    try:
        notification_target = PUSHSAFER_DEVICE
    except NameError:
        notification_target = "a"

    url = 'https://www.pushsafer.com/api'
    post_fields = {
        "t" : 'Pi.Alert Message',
        "m" : _notiMessage,
        "s" : 22,
        "v" : 3,
        "i" : 148,
        "c" : '#ef7f7f',
        "d" : notification_target,
        "u" : REPORT_DASHBOARD_URL,
        "ut" : 'Open Pi.Alert',
        "k" : PUSHSAFER_TOKEN,
        }
    requests.post(url, data=post_fields)

#-------------------------------------------------------------------------------
def send_pushover_test (_notiMessage):
    url = 'https://api.pushover.net/1/messages.json'
    post_fields = {
        "token": PUSHOVER_TOKEN,
        "user": PUSHOVER_USER,
        "title" : 'Pi.Alert Message',
        "message" : _notiMessage,
        }
    requests.post(url, data=post_fields)

#-------------------------------------------------------------------------------
def send_telegram_test (_notiMessage):
    runningpath = os.path.abspath(os.path.dirname(__file__))
    stream = os.popen(runningpath+'/shoutrrr/'+SHOUTRRR_BINARY+'/shoutrrr send --url "'+TELEGRAM_BOT_TOKEN_URL+'" --message "'+_notiMessage+'" --title "Pi.Alert"')

#-------------------------------------------------------------------------------
def send_webgui_test (_notiMessage):
    # Remove one linebrake between "Server" and the headline of the event type
    # extract event type headline to use it in the notification headline
    _webgui_filename = time.strftime("%Y%m%d-%H%M%S") + "_Test.txt"
    if (os.path.exists(REPORTPATH_WEBGUI + _webgui_filename) == False):
        f = open(REPORTPATH_WEBGUI + _webgui_filename, "w")
        f.write(_notiMessage)
        f.close()
    set_pia_reports_permissions()

#-------------------------------------------------------------------------------
def format_report_section (pActive, pSection, pTable, pText, pHTML):
    global mail_text
    global mail_html

    # Replace section text
    if pActive :
        mail_text = mail_text.replace ('<'+ pTable +'>', pText)
        mail_html = mail_html.replace ('<'+ pTable +'>', pHTML)       

        mail_text = remove_tag (mail_text, pSection)       
        mail_html = remove_tag (mail_html, pSection)
    else:
        mail_text = remove_section (mail_text, pSection)
        mail_html = remove_section (mail_html, pSection)

#-------------------------------------------------------------------------------
def format_report_section_services (pActive, pSection, pTable, pText, pHTML):
    global mail_text_webservice
    global mail_html_webservice

    # Replace section text
    if pActive :
        mail_text_webservice = mail_text_webservice.replace ('<'+ pTable +'>', pText)
        mail_html_webservice = mail_html_webservice.replace ('<'+ pTable +'>', pHTML)       

        mail_text_webservice = remove_tag (mail_text_webservice, pSection)       
        mail_html_webservice = remove_tag (mail_html_webservice, pSection)
    else:
        mail_text_webservice = remove_section (mail_text_webservice, pSection)
        mail_html_webservice = remove_section (mail_html_webservice, pSection)

#-------------------------------------------------------------------------------
def remove_section (pText, pSection):
    # Search section into the text
    if pText.find ('<'+ pSection +'>') >=0 \
    and pText.find ('</'+ pSection +'>') >=0 : 
        # return text without the section
        return pText[:pText.find ('<'+ pSection+'>')] + \
               pText[pText.find ('</'+ pSection +'>') + len (pSection) +3:]
    else :
        # return all text
        return pText

#-------------------------------------------------------------------------------
def remove_tag (pText, pTag):
    # return text without the tag
    return pText.replace ('<'+ pTag +'>','').replace ('</'+ pTag +'>','')

#-------------------------------------------------------------------------------
def write_file (pPath, pText):
    # Write the text depending using the correct python version
    if sys.version_info < (3, 0):
        file = io.open (pPath , mode='w', encoding='utf-8')
        file.write ( pText.decode('unicode_escape') ) 
        file.close() 
    else:
        file = open (pPath, 'w', encoding='utf-8') 
        file.write (pText) 
        file.close() 

#-------------------------------------------------------------------------------
def append_line_to_file (pPath, pText):
    # append the line depending using the correct python version
    if sys.version_info < (3, 0):
        file = io.open (pPath , mode='a', encoding='utf-8')
        file.write ( pText.decode('unicode_escape') ) 
        file.close() 
    else:
        file = open (pPath, 'a', encoding='utf-8') 
        file.write (pText) 
        file.close() 

#-------------------------------------------------------------------------------
def send_email (pText, pHTML):
    # Compose email
    msg = MIMEMultipart('alternative')
    msg['Subject'] = 'Pi.Alert Report'
    msg['From'] = REPORT_FROM
    msg['To'] = REPORT_TO
    msg.attach (MIMEText (pText, 'plain'))
    msg.attach (MIMEText (pHTML, 'html'))

    # Send mail
    smtp_connection = smtplib.SMTP (SMTP_SERVER, SMTP_PORT)
    smtp_connection.ehlo()
    if not SafeParseGlobalBool("SMTP_SKIP_TLS"):
        smtp_connection.starttls()
        smtp_connection.ehlo()
    if not SafeParseGlobalBool("SMTP_SKIP_LOGIN"):
        smtp_connection.login (SMTP_USER, SMTP_PASS)
    smtp_connection.sendmail (REPORT_FROM, REPORT_TO, msg.as_string())
    smtp_connection.quit()

#-------------------------------------------------------------------------------
def SafeParseGlobalBool(boolVariable):
    if boolVariable in globals():
        return eval(boolVariable)
    return False

#===============================================================================
# DB
#===============================================================================
def openDB ():
    global sql_connection
    global sql

    # Check if DB is open
    if sql_connection != None :
        return

    # Log    
    print_log ('Opening DB...')

    # Open DB and Cursor
    sql_connection = sqlite3.connect (DB_PATH, isolation_level=None)
    sql_connection.execute('pragma journal_mode=wal') #
    sql_connection.text_factory = str
    sql_connection.row_factory = sqlite3.Row
    sql = sql_connection.cursor()

#-------------------------------------------------------------------------------
def closeDB ():
    global sql_connection
    global sql

    # Check if DB is open
    if sql_connection == None :
        return

    # Log    
    print_log ('Closing DB...')

    # Close DB
    sql_connection.commit()
    sql_connection.close()
    sql_connection = None    

#===============================================================================
# UTIL
#===============================================================================
def print_log (pText):
    global log_timestamp

    # Check LOG actived
    if not PRINT_LOG :
        return

    # Current Time    
    log_timestamp2 = datetime.datetime.now()

    # Print line + time + elapsed time + text
    print ('--------------------> ',
        log_timestamp2, ' ',
        log_timestamp2 - log_timestamp, ' ',
        pText)

    # Save current time to calculate elapsed time until next log
    log_timestamp = log_timestamp2

#===============================================================================
# BEGIN
#===============================================================================
if __name__ == '__main__':
    sys.exit(main())       
