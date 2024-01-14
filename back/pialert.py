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
#  piapiacz, hspindel
#-------------------------------------------------------------------------------

#===============================================================================
# IMPORTS
#===============================================================================
from __future__ import print_function
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText
from requests.packages.urllib3.exceptions import InsecureRequestWarning
from mac_vendor_lookup import MacLookup
from time import sleep, time, strftime
from base64 import b64encode
from urllib.parse import urlparse
from cryptography import x509
from cryptography.hazmat.backends import default_backend
import sys, subprocess, os, re, datetime, sqlite3, socket, io, smtplib, csv, requests, time, pwd, glob, ipaddress, ssl, json

#===============================================================================
# CONFIG CONSTANTS
#===============================================================================
PIALERT_BACK_PATH = os.path.dirname(os.path.abspath(__file__))
PIALERT_PATH = f"{PIALERT_BACK_PATH}/.."
PIALERT_WEBSERVICES_LOG = f"{PIALERT_PATH}/log/pialert.webservices.log"
STOPPIALERT = f"{PIALERT_PATH}/db/setting_stoppialert"
PIALERT_DB_FILE = f"{PIALERT_PATH}/db/pialert.db"
REPORTPATH_WEBGUI = f"{PIALERT_PATH}/front/reports/"

if (sys.version_info > (3,0)):
    exec(open(f"{PIALERT_PATH}/config/version.conf").read())
    exec(open(f"{PIALERT_PATH}/config/pialert.conf").read())
else:
    execfile(f"{PIALERT_PATH}/config/version.conf")
    execfile(f"{PIALERT_PATH}/config/pialert.conf")

#===============================================================================
# MAIN
#===============================================================================
def main():
    global startTime
    global cycle
    global log_timestamp
    global sql_connection
    global sql

    # Header
    print ('\nPi.Alert ' + VERSION +' ('+ VERSION_DATE +')')
    print ('---------------------------------------------------------')
    print("Current User: %s \n" % get_username())

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
        print ('usage pialert [scan_cycle] | internet_IP | update_vendors | cleanup' )
        return
    cycle = str(sys.argv[1])

    ## Main Commands
    if cycle == 'internet_IP':
        res = check_internet_IP()
    elif cycle == 'cleanup':
        res = cleanup_database()
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
    if cycle not in ['internet_IP', 'cleanup']:
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
def set_pia_reports_permissions():
    os.system(f"sudo chown -R {get_username()}:www-data {REPORTPATH_WEBGUI}")
    os.system(f"sudo chmod -R 775 {REPORTPATH_WEBGUI}")

#===============================================================================
# Countdown
#===============================================================================
def start_arpscan_countdown():

    openDB()
    if os.path.exists(STOPPIALERT):
        # get timer from file
        with open(STOPPIALERT, 'r') as file:
            data = int(file.read().rstrip())
            # print("Timer in min: %s" % data)

        FILETIME = int(os.path.getctime(STOPPIALERT))

        print(f"Timer Start: {time.ctime(FILETIME)}")
        STOPTIME = FILETIME+data*60
        print(f"Timer Ende : {time.ctime(STOPTIME)}")
        print ("----------------------------------------")

        ACTUALTIME = int(time.time())

        if ( ACTUALTIME > STOPTIME ):
           print ("File will be deleted")
           os.remove(STOPPIALERT)
           os.system('/usr/bin/python3 ' + PIALERT_BACK_PATH + '/pialert_reporting_test.py reporting_stoptimer')

           sql.execute ("""INSERT INTO pialert_journal (Journal_DateTime, LogClass, Trigger, LogString, Hash, Additional_Info)
                           VALUES (?, 'c_002', 'cronjob', 'LogStr_0513', '', '') """, (startTime,))

           sql_connection.commit()
           scan_network()
        else:
           print ("Timer still running")

    closeDB()

#===============================================================================
# INTERNET IP CHANGE
#===============================================================================
def check_internet_IP():
    # Header
    print ('Check Internet IP')
    print ('    Timestamp:', startTime )
    print ('\nRetrieving Internet IP...')
    internet_IP = get_internet_IP()
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

    # Run automated Speedtest
    if SPEEDTEST_TASK_ACTIVE:
        # Check if Speedtest is installed
        speedtest_binary = f'{PIALERT_BACK_PATH}/speedtest/speedtest'
        if os.path.exists(speedtest_binary):
            print ('\nRun daily Speedtest...')
            run_speedtest_task()
        else:
            print('\nSkipping Speedtest... Not installed!')
    else:
        print ('\nSkipping Speedtest...')
    return 0

#-------------------------------------------------------------------------------
def run_speedtest_task ():
    # Define the command and arguments
    command = ["sudo", PIALERT_BACK_PATH + "/speedtest/speedtest", "-p", "no", "-f", "json"]
    if len(SPEEDTEST_TASK_HOUR) != 0:
        openDB()
        speedtest_actual_hour = startTime.hour
        speedtest_actual_min = startTime.minute
        for value in SPEEDTEST_TASK_HOUR:
            if value == speedtest_actual_hour and speedtest_actual_min == 0:
                try:
                    output = subprocess.check_output(command, text=True)
                    # Parse the JSON output
                    result = json.loads(output)
                    # Access the speed test results
                    speedtest_isp = result['isp']
                    speedtest_server = result['server']['name'] + ' (' + result['server']['location'] + ') (' + result['server']['host'] + ')'
                    speedtest_ping = result['ping']['latency']
                    speedtest_down = round(result['download']['bandwidth'] / 125000, 2)
                    speedtest_up = round(result['upload']['bandwidth'] / 125000, 2)
                    # Build output
                    speedtest_output = ""
                    speedtest_output += f"    ISP:            {speedtest_isp}\n"
                    speedtest_output += f"    Server:         {speedtest_server}\n\n"
                    speedtest_output += f"    Ping:           {speedtest_ping} ms\n"
                    speedtest_output += f"    Download Speed: {speedtest_down} Mbps\n"
                    speedtest_output += f"    Upload Speed:   {speedtest_up} Mbps\n"
                    print(speedtest_output)
                    # Prepare db string
                    speedtest_db_output = speedtest_output.replace("\n", "<br>")
                    # Insert in db
                    sql.execute ("""INSERT INTO Tools_Speedtest_History (speed_date, speed_isp, speed_server, speed_ping, speed_down, speed_up)
                                    VALUES (?, ?, ?, ?, ?, ?) """, (startTime, speedtest_isp, speedtest_server, speedtest_ping, speedtest_down, speedtest_up))
                    # Logging
                    sql.execute ("""INSERT INTO pialert_journal (Journal_DateTime, LogClass, Trigger, LogString, Hash, Additional_Info)
                                    VALUES (?, 'c_002', 'cronjob', 'LogStr_0255', '', ?) """, (startTime, speedtest_db_output))
                    sql_connection.commit()
                except subprocess.CalledProcessError as e:
                    print(f"Error running 'speedtest': {e}")
                except json.JSONDecodeError as e:
                    print(f"Error parsing JSON output: {e}")
            else :
                print (f"    Planned time ({value}:00) not reached yet")
        closeDB()
    else:
        print("    The Parameter SPEEDTEST_TASK_HOUR is not set.")
    return 0

#-------------------------------------------------------------------------------
def get_internet_IP():
    # dig_args = ['dig', '+short', '-4', 'myip.opendns.com', '@resolver1.opendns.com']
    # cmd_output = subprocess.check_output (dig_args, universal_newlines=True)
    curl_args = ['curl', '-s', QUERY_MYIP_SERVER]
    cmd_output = subprocess.check_output (curl_args, universal_newlines=True)
    return check_IP_format (cmd_output)

#-------------------------------------------------------------------------------
def get_dynamic_DNS_IP():
    # Using default or OpenDNS DNS server
    dig_args = ['dig', '+short', DDNS_DOMAIN]
    # dig_args = ['dig', '+short', DDNS_DOMAIN, '@resolver1.opendns.com']
    dig_output = subprocess.check_output (dig_args, universal_newlines=True)
    return check_IP_format (dig_output)

#-------------------------------------------------------------------------------
def set_dynamic_DNS_IP():
    return subprocess.check_output(
        [
            'curl',
            '-s',
            f'{DDNS_UPDATE_URL}username={DDNS_USER}&password={DDNS_PASSWORD}&hostname={DDNS_DOMAIN}',
        ],
        universal_newlines=True,
    )
    
#-------------------------------------------------------------------------------
def get_previous_internet_IP():
    # get previos internet IP stored in DB
    sql.execute ("SELECT dev_LastIP FROM Devices WHERE dev_MAC = 'Internet' ")
    return sql.fetchone()[0]

#-------------------------------------------------------------------------------
def save_new_internet_IP(pNewIP):
    # Log new IP into logfile
    append_line_to_file(
        f'{LOG_PATH}/IP_changes.log', str(startTime) + '\t' + pNewIP + '\n'
    )
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
    sql_connection.commit()
    
#-------------------------------------------------------------------------------
def check_IP_format(pIP):
    # Check IP format
    IPv4SEG  = r'(?:25[0-5]|(?:2[0-4]|1{0,1}[0-9]){0,1}[0-9])'
    IPv4ADDR = f'(?:(?:{IPv4SEG}' + r'\.){3,3}' + IPv4SEG + r')'
    # IPv4ADDR = r'(?:(?:' + IPv4SEG + r'\.){3,3}' + IPv4SEG + r')'
    IP = re.search(IPv4ADDR, pIP)
    # Return error if not IP
    #return "" if IP is None else IP.group(0)
    return "" if IP is None else IP[0]

#===============================================================================
# Cleanup Tasks
#===============================================================================
def cleanup_database():
    print ('Cleanup Database')
    print ('    Timestamp:', startTime )
    openDB()
    try:
        strdaystokeepOH = str(DAYS_TO_KEEP_ONLINEHISTORY)
    except NameError: # variable not defined, use a default
        strdaystokeepOH = str(30) # 1 month
    try:
        strdaystokeepEV = str(DAYS_TO_KEEP_EVENTS)
    except NameError: # variable not defined, use a default
        strdaystokeepEV = str(90) # 90 days
    print(f'    Online_History, up to the lastest {strdaystokeepOH} days...')
    sql.execute(
        f"DELETE FROM Online_History WHERE Scan_Date <= date('now', '-{strdaystokeepOH} day')"
    )
    print(f'    Events, up to the lastest {strdaystokeepEV} days...')
    sql.execute(
        f"DELETE FROM Events WHERE eve_DateTime <= date('now', '-{strdaystokeepEV} day')"
    )
    print(f'    Services_Events, up to the lastest {strdaystokeepOH} days...')
    sql.execute(
        f"DELETE FROM Services_Events WHERE moneve_DateTime <= date('now', '-{strdaystokeepOH} day')"
    )
    print(f'    ICMP_Mon_Events, up to the lastest {strdaystokeepOH} days...')
    sql.execute(
        f"DELETE FROM ICMP_Mon_Events WHERE icmpeve_DateTime <= date('now', '-{strdaystokeepOH} day')"
    )
    print ('    Trim Journal to the lastest 1000 entries')
    sql.execute ("DELETE FROM pialert_journal WHERE journal_id NOT IN (SELECT journal_id FROM pialert_journal ORDER BY journal_id DESC LIMIT 1000) AND (SELECT COUNT(*) FROM pialert_journal) > 1000")
    print(f'    Speedtest_History, up to the lastest {strdaystokeepOH} days...')
    sql.execute(
        f"DELETE FROM Tools_Speedtest_History WHERE speed_date <= date('now', '-{strdaystokeepOH} day')"
    )
    print ('    Shrink Database...')
    sql.execute ("VACUUM;")
    sql.execute ("""INSERT INTO pialert_journal (Journal_DateTime, LogClass, Trigger, LogString, Hash, Additional_Info)
                    VALUES (?, 'c_010', 'cronjob', 'LogStr_0101', '', '') """, (startTime,))
    closeDB()
    return 0

#===============================================================================
# UPDATE DEVICE MAC VENDORS
#===============================================================================
def update_devices_MAC_vendors(pArg = ''):
    print ('Update HW Vendors')
    print ('    Timestamp:', startTime )

    # Update vendors DB (oui)
    print ('\nUpdating vendors DB...')
    update_args = ['sh', f'{PIALERT_BACK_PATH}/update_vendors.sh', pArg]
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
    except Exception:
        print ('\nFallback')
        print ('    Backup old mac-vendors.txt for mac-vendor-lookup')
        p = subprocess.call(["cp $HOME/.cache/mac-vendors.txt $HOME/.cache/mac-vendors.bak"], shell=True)
        print ('    Create mac-vendors.txt for mac-vendor-lookup')
        p = subprocess.call(["/usr/bin/sed -e 's/\t/:/g' -e 's/Ã¼/ü/g' -e 's/Ã¶/ö/g' -e 's/Ã¤/ä/g' -e 's/Ã³/ó/g' -e 's/Ã©/é/g' -e 's/â/–/g' -e 's/Â//g' -e '/^#/d' /usr/share/arp-scan/ieee-oui.txt > $HOME/.cache/mac-vendors.txt"], shell=True)

    # update devices
    sql.executemany ("UPDATE Devices SET dev_Vendor = ? WHERE dev_MAC = ? ",
        recordsToUpdate )

    closeDB()

#-------------------------------------------------------------------------------
def query_MAC_vendor(pMAC):
    try:
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
        return vendor.rstrip()
    except subprocess.CalledProcessError :
        return -1
            
#===============================================================================
# SCAN NETWORK
#===============================================================================
def scan_network():
    # Header
    print ('Scan Devices')
    print ('    Timestamp:', startTime )

    # Query ScanCycle properties
    print_log ('Query ScanCycle confinguration...')
    scanCycle_data = query_ScanCycle_Data (True)
    if scanCycle_data is None:
        print ('\n*************** ERROR ***************')
        print(f'ScanCycle {cycle} not found')
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
    # Pi-hole
    init_import_methode('    Pi-hole Method...', 'Pi-hole copy starts...')
    copy_pihole_network()
    # DHCP Leases
    print ('    DHCP Leases Method...')
    read_DHCP_leases ()
    # Fritzbox
    init_import_methode('    Fritzbox Method...', 'Fritzbox copy starts...')
    read_fritzbox_active_hosts()
    # Mikrotik
    init_import_methode('    Mikrotik Method...', 'Mikrotik copy starts...')
    read_mikrotik_leases()
    # Unifi
    init_import_methode('    UniFi Method...', 'UniFi copy starts...')
    read_unifi_clients()
    # Load current scan data 1/2
    print ('\nProcessing scan results...')
    # Load current scan data 2/2
    print_log ('Save scanned devices')
    save_scanned_devices (arpscan_devices, cycle_interval)
    # Process Ignore list
    print ('    Processing ignore list...')
    remove_entries_from_table()
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
    calc_activity_history_main_scan ()
    # Web Service Monitoring
    try:
        enable_services_monitoring = SCAN_WEBSERVICES
    except NameError:
        enable_services_monitoring = False
    if enable_services_monitoring and str(startTime)[15] == "0":
        service_monitoring()
    # ICMP Monitoring
    try:
        enable_icmp_monitoring = ICMPSCAN_ACTIVE
    except NameError:
        enable_icmp_monitoring = False
    if enable_icmp_monitoring:
        icmp_monitoring()
    # Check Rogue DHCP
    try:
        enable_rogue_dhcp_detection = SCAN_ROGUE_DHCP
    except NameError:
        enable_rogue_dhcp_detection = False
    if enable_rogue_dhcp_detection:
        print ('\nLooking for Rogue DHCP Servers...')
        rogue_dhcp_detection ()

    sql_connection.commit()
    closeDB()

    return 0


def init_import_methode(arg0, arg1):
    print(arg0)
    openDB()
    print_log(arg1)

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
def execute_arpscan():

    # check if arp-scan is active
    try:
        module_arpscan_status = ARPSCAN_ACTIVE
    except NameError:
        module_arpscan_status = True
    if not module_arpscan_status:
        print('        ...Skipped')
        return []
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
def copy_pihole_network():
    # empty Fritzbox Network table
    sql.execute ("DELETE FROM PiHole_Network")

    # check if Pi-hole is active
    if not PIHOLE_ACTIVE :
        print('        ...Skipped')
        return

    # Open Pi-hole DB
    sql.execute(f"ATTACH DATABASE '{PIHOLE_DB}' AS PH")

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
        print('        ...Skipped')
        return

    from fritzconnection.lib.fritzhosts import FritzHosts

    # copy Fritzbox Network list
    fh = FritzHosts(address=FRITZBOX_IP, user=FRITZBOX_USER, password=FRITZBOX_PASS)
    hosts = fh.get_hosts_info()
    for index, host in enumerate(hosts, start=1):
        if host['status'] :
            # status = 'active' if host['status'] else  '-'
            ip = host['ip'] or 'no IP'
            mac = host['mac'].lower() if host['mac'] else '-'
            hostname = host['name']
            try:
                vendor = MacLookup().lookup(host['mac'])
            except:
                vendor = "Prefix is not registered"
            
            sql.execute ("INSERT INTO Fritzbox_Network (FB_MAC, FB_IP, FB_Name, FB_Vendor) "+
                         "VALUES (?, ?, ?, ?) ", (mac, ip, hostname, vendor) )

#-------------------------------------------------------------------------------
def read_mikrotik_leases ():

    sql_create_table = """ CREATE TABLE IF NOT EXISTS Mikrotik_Network(
                                "MT_MAC" STRING(50) NOT NULL COLLATE NOCASE,
                                "MT_IP" STRING(50) COLLATE NOCASE,
                                "MT_Name" STRING(50),
                                "MT_Vendor" STRING(250)
                            ); """
    sql.execute(sql_create_table)
    sql_connection.commit()

    sql.execute ("DELETE FROM Mikrotik_Network")

    if not MIKROTIK_ACTIVE:
        print('        ...Skipped')
        return

    #installed using pip3 install routeros_api
    import routeros_api

    data = []
    conn = routeros_api.RouterOsApiPool(MIKROTIK_IP, MIKROTIK_USER, MIKROTIK_PASS, plaintext_login=True)
    api = conn.get_api()
    ret = api.get_resource('/ip/dhcp-server/lease').get()
    conn.disconnect()
    for row in ret:
        if 'active-mac-address' in row:
            mac = row['active-mac-address'].lower()
            ip = row['active-address']
            hostname = row.get('host-name','')
            try:
                vendor = MacLookup().lookup(mac)
            except:
                vendor = "Prefix is not registered"

            sql.execute ("INSERT INTO Mikrotik_Network (MT_MAC, MT_IP, MT_Name, MT_Vendor) "+
                         "VALUES (?, ?, ?, ?) ", (mac, ip, hostname, vendor) )

#-------------------------------------------------------------------------------
def read_unifi_clients ():

    sql_create_table = """ CREATE TABLE IF NOT EXISTS Unifi_Network(
                                "UF_MAC" STRING(50) NOT NULL COLLATE NOCASE,
                                "UF_IP" STRING(50) COLLATE NOCASE,
                                "UF_Name" STRING(50),
                                "UF_Vendor" STRING(250)
                            ); """
    sql.execute(sql_create_table)
    sql_connection.commit()

    sql.execute ("DELETE FROM Unifi_Network")

    if not UNIFI_ACTIVE:
        print('        ...Skipped')
        return

    from pyunifi.controller import Controller

    # Enable self signed SSL / no warnings
    requests.packages.urllib3.disable_warnings(InsecureRequestWarning)

    try:
        UNIFI_API_VERSION = UNIFI_API
    except NameError: # variable not defined, use a default
        UNIFI_API_VERSION = 'v5'

    try:
        data = []
        c = Controller(UNIFI_IP,UNIFI_USER,UNIFI_PASS,8443,UNIFI_API_VERSION,'default',ssl_verify=False)
        clients = c.get_clients()
        for row in clients:
            mac = row['mac'].lower()
            ip = row.get('ip','no IP')
            hostname = row.get('hostname',row.get('name',''))
            vendor = row.get('oui',None)
            if not vendor:
                try:
                    vendor = MacLookup().lookup(mac)
                except:
                    vendor = "Prefix is not registered"

            sql.execute ("INSERT INTO Unifi_Network (UF_MAC, UF_IP, UF_Name, UF_Vendor) "+
                         "VALUES (?, ?, ?, ?) ", (mac, ip, hostname, vendor) )

    except Exception as e:
        print('        Could not connect to UniFi Controller')

#-------------------------------------------------------------------------------
def read_DHCP_leases ():
    # check DHCP Leases is active
    if not DHCP_ACTIVE :
        print('        ...Skipped')
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

    # Insert Mikrotik devices
    sql.execute ("""INSERT INTO CurrentScan (cur_ScanCycle, cur_MAC, 
                        cur_IP, cur_Vendor, cur_ScanMethod)
                    SELECT ?, MT_MAC, MT_IP, MT_Vendor, 'Mikrotik'
                    FROM Mikrotik_Network
                    WHERE NOT EXISTS (SELECT 'X' FROM CurrentScan
                                      WHERE cur_MAC = MT_MAC )""",
                    (cycle) )

    # Insert UniFi devices
    sql.execute ("""INSERT INTO CurrentScan (cur_ScanCycle, cur_MAC, 
                        cur_IP, cur_Vendor, cur_ScanMethod)
                    SELECT ?, UF_MAC, UF_IP, UF_Vendor, 'UniFi'
                    FROM Unifi_Network
                    WHERE NOT EXISTS (SELECT 'X' FROM CurrentScan
                                      WHERE cur_MAC = UF_MAC )""",
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
        print(
            f'        Delete {len(MAC_IGNORE_LIST)} ignored devices from scan on appearance'
        )

        mac_addresses = ','.join([f'"{mac}"' for mac in MAC_IGNORE_LIST])
        query = f'DELETE FROM CurrentScan WHERE cur_MAC IN ({mac_addresses})'
        sql.execute(query)
        query = f'DELETE FROM PiHole_Network WHERE PH_MAC IN ({mac_addresses})'
        sql.execute(query)
        query = f'DELETE FROM DHCP_Leases WHERE DHCP_MAC IN ({mac_addresses})'
        sql.execute(query)
        query = f'DELETE FROM Fritzbox_Network WHERE FB_MAC IN ({mac_addresses})'
        sql.execute(query)
        query = f'DELETE FROM Mikrotik_Network WHERE MT_MAC IN ({mac_addresses})'
        sql.execute(query)
        query = f'DELETE FROM Unifi_Network WHERE UF_MAC IN ({mac_addresses})'
        sql.execute(query)
    except NameError:
        print("        No ignore list defined")

#-------------------------------------------------------------------------------
def print_scan_stats():
    # Devices Detected
    sql.execute ("""SELECT COUNT(*) FROM CurrentScan
                    WHERE cur_ScanCycle = ? """,
                    (cycle,))
    print('    Devices Detected.......:', sql.fetchone()[0])
    # Devices arp-scan
    sql.execute ("""SELECT COUNT(*) FROM CurrentScan
                    WHERE cur_ScanMethod='arp-scan' AND cur_ScanCycle = ? """,
                    (cycle,))
    print('        arp-scan Method....:', sql.fetchone()[0])
    # Devices Pi-hole
    sql.execute ("""SELECT COUNT(*) FROM CurrentScan
                    WHERE cur_ScanMethod='Pi-hole' AND cur_ScanCycle = ? """,
                    (cycle,))
    print(f'        Pi-hole Method.....: +{str(sql.fetchone()[0])}')
    # Devices Fritzbox
    sql.execute ("""SELECT COUNT(*) FROM CurrentScan
                    WHERE cur_ScanMethod='Fritzbox' AND cur_ScanCycle = ? """,
                    (cycle,))
    print(f'        Fritzbox Method....: +{str(sql.fetchone()[0])}')
    # Devices Mikrotik
    sql.execute ("""SELECT COUNT(*) FROM CurrentScan
                    WHERE cur_ScanMethod='Mikrotik' AND cur_ScanCycle = ? """,
                    (cycle,))
    print(f'        Mikrotik Method....: +{str(sql.fetchone()[0])}')
    # Devices UniFi
    sql.execute ("""SELECT COUNT(*) FROM CurrentScan
                    WHERE cur_ScanMethod='UniFi' AND cur_ScanCycle = ? """,
                    (cycle,))
    print(f'        UniFi Method.......: +{str(sql.fetchone()[0])}')
    # New Devices
    sql.execute ("""SELECT COUNT(*) FROM CurrentScan
                    WHERE cur_ScanCycle = ? 
                      AND NOT EXISTS (SELECT 1 FROM Devices
                                      WHERE dev_MAC = cur_MAC) """,
                    (cycle,))
    print(f'        New Devices........: {str(sql.fetchone()[0])}')
    # Devices in this ScanCycle
    sql.execute ("""SELECT COUNT(*) FROM Devices, CurrentScan
                    WHERE dev_MAC = cur_MAC AND dev_ScanCycle = cur_ScanCycle
                      AND dev_ScanCycle = ? """,
                    (cycle,))
    print ('')
    print(f'    Devices in this scan...: {str(sql.fetchone()[0])}')
    # Down Alerts
    sql.execute ("""SELECT COUNT(*) FROM Devices
                    WHERE dev_AlertDeviceDown = 1
                      AND dev_ScanCycle = ?
                      AND NOT EXISTS (SELECT 1 FROM CurrentScan
                                      WHERE dev_MAC = cur_MAC
                                        AND dev_ScanCycle = cur_ScanCycle) """,
                    (cycle,))
    print(f'        Down Alerts........: {str(sql.fetchone()[0])}')
    # New Down Alerts
    sql.execute ("""SELECT COUNT(*) FROM Devices
                    WHERE dev_AlertDeviceDown = 1
                      AND dev_PresentLastScan = 1
                      AND dev_ScanCycle = ?
                      AND NOT EXISTS (SELECT 1 FROM CurrentScan
                                      WHERE dev_MAC = cur_MAC
                                        AND dev_ScanCycle = cur_ScanCycle) """,
                    (cycle,))
    print(f'        New Down Alerts....: {str(sql.fetchone()[0])}')
    # New Connections
    sql.execute ("""SELECT COUNT(*) FROM Devices, CurrentScan
                    WHERE dev_MAC = cur_MAC AND dev_ScanCycle = cur_ScanCycle
                      AND dev_PresentLastScan = 0
                      AND dev_ScanCycle = ? """,
                    (cycle,))
    print(f'        New Connections....: {str(sql.fetchone()[0])}')
    # Disconnections
    sql.execute ("""SELECT COUNT(*) FROM Devices
                    WHERE dev_PresentLastScan = 1
                      AND dev_ScanCycle = ?
                      AND NOT EXISTS (SELECT 1 FROM CurrentScan
                                      WHERE dev_MAC = cur_MAC
                                        AND dev_ScanCycle = cur_ScanCycle) """,
                    (cycle,))
    print(f'        Disconnections.....: {str(sql.fetchone()[0])}')
    # IP Changes
    sql.execute ("""SELECT COUNT(*) FROM Devices, CurrentScan
                    WHERE dev_MAC = cur_MAC AND dev_ScanCycle = cur_ScanCycle
                      AND dev_ScanCycle = ?
                      AND dev_LastIP <> cur_IP """,
                    (cycle,))
    print(f'        IP Changes.........: {str(sql.fetchone()[0])}')

#------------------------------------------------------------------------------
def calc_activity_history_main_scan ():
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
    sql.execute ("INSERT INTO Online_History (Scan_Date, Online_Devices, Down_Devices, All_Devices, Archived_Devices, Data_Source) "+
                 "VALUES ( ?, ?, ?, ?, ?, ?)", (startTime, History_Online_Devices, History_Offline_Devices, History_ALL_Devices, History_Archived_Devices, 'main_scan') )

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
def update_devices_data_from_scan():
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

    # Mikrotik Leases - Update (unknown) Name
    sql.execute ("""UPDATE Devices
                    SET dev_Name = (SELECT MT_Name FROM Mikrotik_Network
                                    WHERE MT_MAC = dev_MAC)
                    WHERE (dev_Name = "(unknown)"
                           OR dev_Name = ""
                           OR dev_Name IS NULL)
                      AND EXISTS (SELECT 1 FROM Mikrotik_Network
                                  WHERE MT_MAC = dev_MAC
                                    AND MT_NAME IS NOT NULL
                                    AND MT_NAME <> '') """)

    # Unifi Leases - Update (unknown) Name
    sql.execute ("""UPDATE Devices
                    SET dev_Name = (SELECT UF_Name FROM Unifi_Network
                                    WHERE UF_MAC = dev_MAC)
                    WHERE (dev_Name = "(unknown)"
                           OR dev_Name = ""
                           OR dev_Name IS NULL)
                      AND EXISTS (SELECT 1 FROM Unifi_Network
                                  WHERE UF_MAC = dev_MAC
                                    AND UF_Name IS NOT NULL
                                    AND UF_Name <> '') """)

    # DHCP Leases - Vendor
    print_log ('Update devices - 5 Vendor')

    recordsToUpdate = []
    query = """SELECT * FROM Devices
               WHERE dev_Vendor = '(unknown)' OR dev_Vendor =''
                  OR dev_Vendor IS NULL"""

    for device in sql.execute (query):
        vendor = query_MAC_vendor (device['dev_MAC'])
        if vendor not in [-1, -2]:
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
def rogue_dhcp_detection():
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
    dhcp_server_list = [strftime("%Y-%m-%d %H:%M:%S")]
    for _ in range(dhcp_probes):
        stream = os.popen('sudo nmap --script broadcast-dhcp-discover 2>/dev/null | grep "Server Identifier" | awk \'{ print $4 }\'')
        output = stream.read()
        dhcp_server_list.append(output.replace("\n", ""))

    # Insert list in database
    sqlite_insert = """INSERT INTO Nmap_DHCP_Server
                         (scan_num, dhcp_server) 
                         VALUES (?, ?);"""

    for i in range(len(dhcp_server_list)):
        table_data = (i, dhcp_server_list[i])
        sql.execute(sqlite_insert, table_data)

    sql_connection.commit()

    rogue_dhcp_notification ()

# -----------------------------------------------------------------------------------
def rogue_dhcp_notification():
    sql.execute("SELECT DISTINCT dhcp_server FROM Nmap_DHCP_Server")
    rows = sql.fetchall()

    rogue_dhcp_server_list = []

    if len(rows) == 1:
        print ('    No DHCP Server detected.')

    if len(rows) == 2:
        if validate_dhcp_address(rows[1][0]):
            if rows[1][0] == DHCP_SERVER_ADDRESS:
                print(f'    One DHCP Server detected......: {rows[1][0]} (valid)')
            else:
                print(f'    One DHCP Server detected......: {rows[1][0]} (invalid)')
                rogue_dhcp_server_list.append(rows[1][0])
        else:
            print ('    Detection Error')

    if len(rows) > 2:
        print ('    Multiple DHCP Servers detected:')
        for i in range(1, len(rows)):
            if validate_dhcp_address(rows[i][0]):
                if rows[i][0] == DHCP_SERVER_ADDRESS:
                    print(f'        {rows[i][0]} (valid)')
                else:
                    print(f'        {rows[i][0]} (rogue)')
                    rogue_dhcp_server_list.append(rows[i][0])
            else:
                print ('    Detection Error')

    rogue_dhcp_reports = glob.glob(f"{REPORTPATH_WEBGUI}*Rogue DHCP Server*.txt")    

    if rogue_dhcp_server_list and not rogue_dhcp_reports:
        rogue_dhcp_server_string = "Report Date: " + rows[0][0] + "\nServer: " + socket.gethostname() + "\n\nRogue DHCP Server\nDetected Server(s): "
        rogue_dhcp_server_string += ', '.join(rogue_dhcp_server_list)

        # Send Mail
        sending_notifications ('rogue_dhcp', rogue_dhcp_server_string, rogue_dhcp_server_string)

#===============================================================================
# Services Monitoring
#===============================================================================
def set_service_update(_mon_URL, _mon_lastScan, _mon_lastStatus, _mon_lastLatence, _mon_TargetIP, _mon_Redirect, _mon_ssl_info, _mon_ssl_fc):

    # SSL Info change
    if len(_mon_ssl_info) == 4 :
        _mon_ssl_subject = _mon_ssl_info['Subject']
        _mon_ssl_issuer = _mon_ssl_info['Issuer']
        _mon_ssl_valid_from = _mon_ssl_info['Valid_from']
        _mon_ssl_valid_to = _mon_ssl_info['Valid_to']
    else :
        _mon_ssl_subject = ""
        _mon_ssl_issuer = ""
        _mon_ssl_valid_from = ""
        _mon_ssl_valid_to = ""

    ssl_fc = str(_mon_ssl_fc)

    if _mon_Redirect != 200 and _mon_lastStatus == 200:
        _mon_Redirect_Text = f"Redirected by {str(_mon_Redirect)}"
    else:
        _mon_Redirect_Text = ""

    sqlite_insert = """UPDATE Services SET mon_LastScan=?, mon_LastStatus=?, mon_LastLatency=?, mon_TargetIP=?, mon_Notes=?, mon_ssl_subject=?, mon_ssl_issuer=?, mon_ssl_valid_from=?, mon_ssl_valid_to=?, mon_ssl_fc=? WHERE mon_URL=?;"""

    table_data = (_mon_lastScan, _mon_lastStatus, _mon_lastLatence, _mon_TargetIP, _mon_Redirect_Text, _mon_ssl_subject, _mon_ssl_issuer, _mon_ssl_valid_from, _mon_ssl_valid_to, ssl_fc, _mon_URL)
    sql.execute(sqlite_insert, table_data)
    sql_connection.commit()

# -----------------------------------------------------------------------------
def set_services_events(_moneve_URL, _moneve_DateTime, _moneve_StatusCode, _moneve_Latency, _moneve_TargetIP, _moneve_ssl_fc):

    sqlite_insert = """INSERT INTO Services_Events
                     (moneve_URL, moneve_DateTime, moneve_StatusCode, moneve_Latency, moneve_TargetIP, moneve_ssl_fc) 
                     VALUES (?, ?, ?, ?, ?, ?);"""

    table_data = (_moneve_URL, _moneve_DateTime, _moneve_StatusCode, _moneve_Latency, _moneve_TargetIP, _moneve_ssl_fc)
    sql.execute(sqlite_insert, table_data)
    sql_connection.commit()

# -----------------------------------------------------------------------------
def set_services_current_scan(_cur_URL, _cur_DateTime, _cur_StatusCode, _cur_Latency, _cur_TargetIP, _cur_ssl_info):

    _cur_StatusChanged = 0

    sql.execute("SELECT * FROM Services WHERE mon_URL = ?", [_cur_URL])
    rows = sql.fetchall()
    for row in rows:
        _mon_AlertEvents = row[6]
        _mon_AlertDown = row[7]
        _mon_StatusCode = row[2]
        _mon_Latency = row[3]
        _mon_TargetIP = row[8]
        _mon_ssl_subject = row[10] # FC value 8
        _mon_ssl_issuer = row[11] # FC value 4
        _mon_ssl_valid_from = row[12] # FC value 2
        _mon_ssl_valid_to = row[13] # FC value 1
        _mon_ssl_fc = row[14] # FC value between 0 and 15

    _cur_ssl_fc = 0
    # SSL Info change - Calc FC
    if len(_cur_ssl_info) == 4:
        if _cur_ssl_info['Subject'] != _mon_ssl_subject:
            _cur_ssl_fc += 8
        _cur_ssl_subject = _cur_ssl_info['Subject']
        if _cur_ssl_info['Issuer'] != _mon_ssl_issuer:
            _cur_ssl_fc += 4
        _cur_ssl_issuer = _cur_ssl_info['Issuer']
        if _cur_ssl_info['Valid_from'] != _mon_ssl_valid_from:
            _cur_ssl_fc += 2
        _cur_ssl_valid_from = _cur_ssl_info['Valid_from']
        if _cur_ssl_info['Valid_to'] != _mon_ssl_valid_to:
            _cur_ssl_fc += 1
        _cur_ssl_valid_to = _cur_ssl_info['Valid_to']
    else:
        _cur_ssl_subject = ""
        _cur_ssl_issuer = ""
        _cur_ssl_valid_from = ""
        _cur_ssl_valid_to = ""

    # SSL Info change - Compare FC
    if _cur_ssl_fc > 0:
        _cur_StatusChanged += 1

    # IP change
    if _mon_TargetIP != _cur_TargetIP or _mon_StatusCode != _cur_StatusCode:
        _cur_StatusChanged += 1
    # Down or Online
    if _mon_Latency == "99999999" and _mon_Latency != _cur_Latency:
        _cur_LatencyChanged = 0
        _cur_StatusChanged += 1
    elif _cur_Latency == "99999999" and _mon_Latency != _cur_Latency:
        _cur_LatencyChanged = 1
    else:
        _cur_LatencyChanged = 0 

    # Merge Changes from all Events to 1 or 0
    StatusChanged = 1 if _cur_StatusChanged > 0 else 0
    sqlite_insert = """INSERT INTO Services_CurrentScan
                     (cur_URL, cur_DateTime, cur_StatusCode, cur_Latency, cur_AlertEvents, cur_AlertDown, cur_StatusChanged, cur_LatencyChanged, cur_TargetIP, cur_StatusCode_prev, cur_TargetIP_prev, cur_ssl_subject, cur_ssl_issuer, cur_ssl_valid_from, cur_ssl_valid_to, cur_ssl_fc) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);"""

    table_data = (_cur_URL, _cur_DateTime, _cur_StatusCode, _cur_Latency, _mon_AlertEvents, _mon_AlertDown, StatusChanged, _cur_LatencyChanged, _cur_TargetIP, _mon_StatusCode, _mon_TargetIP, _cur_ssl_subject, _cur_ssl_issuer, _cur_ssl_valid_from, _cur_ssl_valid_to, _cur_ssl_fc)
    sql.execute(sqlite_insert, table_data)
    sql_connection.commit()

    return _cur_ssl_fc

# -----------------------------------------------------------------------------
def service_monitoring_log(site, status, latency):
    status_str = str(status)

    # Log status message to log file
    with open(PIALERT_WEBSERVICES_LOG, 'a') as monitor_logfile:
        monitor_logfile.write(
            f'{strftime("%Y-%m-%d %H:%M:%S")} |        {status_str.zfill(3)} |     {latency} | {site}\n'
        )

# -----------------------------------------------------------------------------
def check_services_health(site):
    # Enable self signed SSL / no warning
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
def get_ssl_cert_info(url, timeout=10):
    
    try:
        parsed_url = urlparse(url)
        hostname = parsed_url.hostname
        port = parsed_url.port or 443

        socket.setdefaulttimeout(timeout)

        #with socket.create_connection((hostname, 443)) as sock:
        with socket.create_connection((hostname, port)) as sock:
            context = ssl.SSLContext(ssl.PROTOCOL_TLS_CLIENT)
            context.check_hostname = False
            context.verify_mode = ssl.CERT_NONE  # Disable certificate verification
            with context.wrap_socket(sock, server_hostname=hostname, do_handshake_on_connect=False) as ssock:
                ssock.do_handshake()  # Perform the SSL handshake

                cert_data = ssock.getpeercert(binary_form=True)
                cert = x509.load_der_x509_certificate(cert_data, default_backend())

                return {
                    'Subject': f"""{cert.subject}""",
                    'Issuer': f"""{cert.issuer}""",
                    'Valid_from': f"""{cert.not_valid_before}""",
                    'Valid_to': f"""{cert.not_valid_after}""",
                }
    except socket.timeout:
        return "SSL certificate could not be found (Timeout)"

    except socket.gaierror:
        return "SSL certificate could not be found (Host down or does not exists)"
        # return 0

    except ConnectionRefusedError:
        return "SSL certificate could not be found (Connection Refused)"
        # return 0

    except Exception as e:
        return "SSL certificate could not be found (General Error)"
        # print(e)

# -----------------------------------------------------------------------------
def get_services_list():

    with open(PIALERT_WEBSERVICES_LOG, 'a') as monitor_logfile:
        monitor_logfile.write("    Get Services List\n")
        monitor_logfile.close()

    sql.execute("SELECT mon_URL FROM Services")
    rows = sql.fetchall()

    return [row[0] for row in rows]

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
        monitor_logfile.write(
            f"    Changed StatusCodes.....: {str(changedStatusCode)}"
        )
        monitor_logfile.write("\n    Changed Reachability....: " + str(changedLatency))
        monitor_logfile.write("\n")
        monitor_logfile.close()

# -----------------------------------------------------------------------------
def service_monitoring_notification():
    global mail_text_webservice
    global mail_html_webservice

    # Reporting section
    print ('\nReporting (Web Services) ...')

    with open(f'{PIALERT_BACK_PATH}/report_template_webservice.txt', 'r') as template_file:
        mail_text_webservice = template_file.read()
    with open(f'{PIALERT_BACK_PATH}/report_template_webservice.html', 'r') as template_file:
        mail_html_webservice = template_file.read()
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
    text_line_template = '{}{}\n\t{}\t\t\t{}\n\t{}\t{}\n\t{}\t{}\n\t{}\t{}\n\t{}{}\n\t{}\t\t{}\n\n'
    html_line_template = '<tr>\n  <td>'+ \
            '  {} </td>\n  <td> {} </td>\n'+ \
            '  <td> {} </td>\n <td> {} </td>\n  <td> {} </td>\n  <td> {} </td>\n'+ \
            '  <td> {} </td>\n <td> {} </td>\n</tr>\n'

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
            'prev. HTTP Status Code: ', eventAlert['cur_StatusCode_prev'],
            'SSL Status: ', eventAlert['cur_ssl_fc'])
        mail_html_events += html_line_template.format (
            eventAlert['cur_URL'], eventAlert['cur_Latency'], _func_cur_TargetIP,
            _func_cur_TargetIP_prev, eventAlert['cur_DateTime'], eventAlert['cur_StatusCode'],
            eventAlert['cur_StatusCode_prev'], eventAlert['cur_ssl_fc'])

    format_report_section_services (mail_section_events, 'SECTION_EVENTS',
        'TABLE_EVENTS', mail_text_events, mail_html_events)

    # # Send Mail
    if mail_section_services_down == True or mail_section_events == True :
        sending_notifications ('webservice', mail_html_webservice, mail_text_webservice)
    else :
        print ('    No changes to report...')

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

    scantime = startTime.strftime("%Y-%m-%d %H:%M")

    while sites:
        for site in sites:
            status,latency = check_services_health(site)
            site_retry = ''
            if latency == "99999999":
                # 2nd Retry if the first attempt fails
                status,latency = check_services_health(site)
                site_retry = '*'
            if latency == "99999999" :
                # 3rd Retry if the second attempt fails
                status,latency = check_services_health(site)
                site_retry = '**'

            #Get IP from Domain
            if latency != "99999999":
                redirect_state = check_services_redirect(site)
                domain = urlparse(site).netloc
                domain = domain.split(":")[0]
                domain_ip = socket.gethostbyname(domain)
                # get SSL info
                ssl_info = get_ssl_cert_info(site)
                #print(ssl_info)
            else:
                domain_ip = ""
                redirect_state = ""
                ssl_info = ""

            service_monitoring_log(f'{site} {site_retry}', status, latency)
            ssl_fc = set_services_current_scan(site, scantime, status, latency, domain_ip, ssl_info)
            set_services_events(site, scantime, status, latency, domain_ip, ssl_fc)
#            set_services_current_scan(site, scantime, status, latency, domain_ip, ssl_info)
            sys.stdout.flush()
            set_service_update(site, scantime, status, latency, domain_ip, redirect_state, ssl_info, ssl_fc)
        break

    else:
        print("    No site(s) to monitor!")
        with open(PIALERT_WEBSERVICES_LOG, 'a') as monitor_logfile:
            monitor_logfile.write("\n**************** No site(s) to monitor!! ****************\n")
            monitor_logfile.close()

    # Print to log file
    print_service_monitoring_changes()

#===============================================================================
# ICMP Monitoring
#===============================================================================
def icmp_monitoring():

    print("\nStart ICMP Monitoring...")
    print("    Get Host/Domain List...")
    icmphosts = get_icmphost_list()
    icmphostscount = len(icmphosts)
    print(f"        List contains {icmphostscount} entries")
    print("    Flush previous ping results...")
    flush_icmphost_current_scan()
    print("    Ping Hosts...")

    closeDB()
    scantime = startTime.strftime("%Y-%m-%d %H:%M")

    icmphosts_all = len(icmphosts)
    try:
        ping_retries = ICMP_ONLINE_TEST
    except NameError: # variable not defined, use a default
        ping_retries = 1 # 1

    if icmphosts_all > 0:
        _extracted_from_icmp_monitoring_15(
            icmphosts_all, icmphosts, ping_retries, scantime
        )
    else:
        openDB()
        print("    No Hosts(s) to monitor!")


# TODO Rename this here and in `icmp_monitoring`
def _extracted_from_icmp_monitoring_15(icmphosts_all, icmphosts, ping_retries, scantime):
    icmp_scan_results = {}

    icmphosts_online = 0
    icmphosts_offline = 0

    for icmphosts_index in range(icmphosts_all):
        host_ip = icmphosts[icmphosts_index]
        for _ in range(ping_retries):
            # print("Host %s retry %s" % (host_ip, str(i+1)))
            icmp_status = ping(host_ip)
            if icmp_status == "1":
                break;

        if icmp_status == "1":
            icmp_rtt = ping_avg(host_ip)
            # print("Host %s RTT %s" % (host_ip, str(icmp_rtt)))
            icmphosts_online+=1
        else:
            icmp_rtt = "99999"
            icmphosts_offline+=1

        current_data = {
            "host_ip": host_ip,
            "scantime": scantime,
            "icmp_status": icmp_status,
            "icmp_rtt": icmp_rtt
        }

        icmp_scan_results[host_ip] = current_data
        sys.stdout.flush()

    print(f"        Online Host(s)  : {str(icmphosts_online)}")
    print(f"        Offline Host(s) : {str(icmphosts_offline)}")

    openDB()
    # Save Scan Results
    icmp_save_scandata(icmp_scan_results)

    print("    Calculate Activity History...")
    calc_activity_history_icmp(icmphosts_online, icmphosts_offline)

# -----------------------------------------------------------------------------
def icmp_save_scandata(data):
    print("    Save scan results...")
    for host_ip, scan_data in data.items():
        #print(f"Host IP: {host_ip}")
        #print(f"ICMP Status: {scan_data['icmp_status']}")
        set_icmphost_events(host_ip, scan_data['scantime'], scan_data['icmp_status'], scan_data['icmp_rtt'])
        set_icmphost_current_scan(host_ip, scan_data['scantime'], scan_data['icmp_status'], scan_data['icmp_rtt'])
        set_icmphost_update(host_ip, scan_data['scantime'], scan_data['icmp_status'], scan_data['icmp_rtt'])

# -----------------------------------------------------------------------------
def get_icmphost_list():

    sql.execute("SELECT icmp_ip FROM ICMP_Mon")
    rows = sql.fetchall()

    return [row[0] for row in rows]

# -----------------------------------------------------------------------------
def ping(host):

    command = ['sudo', 'ping', '-c' , '1', host]
    result = subprocess.run(command, stdout=subprocess.PIPE, stderr=subprocess.DEVNULL)
    output = result.stdout.decode('utf8')
    if "Request timed out." in output or "100% packet loss" in output:
        return "0"
    return "1"

# -----------------------------------------------------------------------------
def ping_avg(host):

    try:
        ping_count = str(ICMP_GET_AVG_RTT)
    except NameError: # variable not defined, use a default
        ping_count = str(2) # 1

    command = ['sudo', 'ping', '-c', ping_count, host]
    ping_process = subprocess.Popen(command, stdout=subprocess.PIPE)
    tail_process = subprocess.Popen(['tail', '-1'], stdin=ping_process.stdout, stdout=subprocess.PIPE)
    awk_process = subprocess.Popen(['awk', '-F/', '{print $5}'], stdin=tail_process.stdout, stdout=subprocess.PIPE)
    output, error = awk_process.communicate()
    return output.decode('utf-8').strip()

# -----------------------------------------------------------------------------
def set_icmphost_events(_icmpeve_ip, _icmpeve_DateTime, _icmpeve_Present, _icmpeve_avgrtt):

    #print(_icmpeve_ip, _icmpeve_DateTime, _icmpeve_Present, _icmpeve_avgrtt)
    sqlite_insert = """INSERT INTO ICMP_Mon_Events
                     (icmpeve_ip, icmpeve_DateTime, icmpeve_Present, icmpeve_avgrtt) 
                     VALUES (?, ?, ?, ?);"""

    table_data = (_icmpeve_ip, _icmpeve_DateTime, _icmpeve_Present, _icmpeve_avgrtt)
    sql.execute(sqlite_insert, table_data)
    sql_connection.commit()

# -----------------------------------------------------------------------------
def set_icmphost_current_scan(_cur_ip, _cur_DateTime, _cur_Present, _cur_avgrrt):

    sql.execute("SELECT * FROM ICMP_Mon WHERE icmp_ip = ?", [_cur_ip])
    rows = sql.fetchall()
    for row in rows:
        _icmp_PresentLastScan = row[3]
        _icmp_AlertEvents = row[5]
        _icmp_AlertDown = row[6]

    if str(_icmp_PresentLastScan) != str(_cur_Present):
        _cur_PresentChanged = 1
    else:
        _cur_PresentChanged = 0 

    sqlite_insert = """INSERT INTO ICMP_Mon_CurrentScan
                     (cur_ip, cur_LastScan, cur_Present, cur_PresentChanged, cur_avgrrt, cur_AlertEvents, cur_AlertDown) 
                     VALUES (?, ?, ?, ?, ?, ?, ?);"""

    table_data = (_cur_ip, _cur_DateTime, _cur_Present, _cur_PresentChanged, _cur_avgrrt, _icmp_AlertEvents, _icmp_AlertDown)
    sql.execute(sqlite_insert, table_data)
    sql_connection.commit()

# -----------------------------------------------------------------------------
def set_icmphost_update(_icmp_ip, _icmp_LastScan, _icmp_PresentLastScan, _icmp_avgrtt):

    sqlite_insert = """UPDATE ICMP_Mon SET icmp_LastScan=?, icmp_PresentLastScan=?, icmp_avgrtt=? WHERE icmp_ip=?;"""
    table_data = (_icmp_LastScan, _icmp_PresentLastScan, _icmp_avgrtt, _icmp_ip)
    sql.execute(sqlite_insert, table_data)
    sql_connection.commit()

# -----------------------------------------------------------------------------
def flush_icmphost_current_scan():

    sql.execute("DELETE FROM ICMP_Mon_CurrentScan")
    sql_connection.commit()

# -----------------------------------------------------------------------------
def get_icmphost_name(_icmp_ip):
    query = "SELECT icmp_hostname FROM ICMP_Mon WHERE icmp_ip = ?"
    sql.execute(query, (_icmp_ip,))
    result_hostname = sql.fetchone()

    if result_hostname:
        hostname = result_hostname[0]
    else:
        hostname = 'No Hostname set'

    return hostname

# -----------------------------------------------------------------------------
def calc_activity_history_icmp(History_Online_Devices, History_Offline_Devices):

    History_ALL_Devices = History_Online_Devices + History_Offline_Devices
    sql.execute ("INSERT INTO Online_History (Scan_Date, Online_Devices, Down_Devices, All_Devices, Data_Source) "+
                 "VALUES ( ?, ?, ?, ?, ?)", (startTime, History_Online_Devices, History_Offline_Devices, History_ALL_Devices, 'icmp_scan') )
    sql_connection.commit()

# -----------------------------------------------------------------------------
def icmphost_monitoring_notification():
    global mail_text_icmphost
    global mail_html_icmphost

    # Reporting section
    print ('\nReporting (ICMP Monitoring) ...')

    with open(f'{PIALERT_BACK_PATH}/report_template_icmpmon.txt', 'r') as template_file:
        mail_text_icmphost = template_file.read()
    with open(f'{PIALERT_BACK_PATH}/report_template_icmpmon.html', 'r') as template_file:
        mail_html_icmphost = template_file.read()
    # Report Header & footer
    timeFormated = startTime.strftime ('%Y-%m-%d %H:%M')
    mail_text_icmphost = mail_text_icmphost.replace ('<REPORT_DATE>', timeFormated)
    mail_html_icmphost = mail_html_icmphost.replace ('<REPORT_DATE>', timeFormated)

    mail_text_icmphost = mail_text_icmphost.replace ('<SERVER_NAME>', socket.gethostname() )
    mail_html_icmphost = mail_html_icmphost.replace ('<SERVER_NAME>', socket.gethostname() )

    # Compose Devices Down Section
    mail_section_icmphost_down = False
    mail_text_icmphost_down = ''
    mail_html_icmphost_down = ''
    text_line_template = '{}{}\n\t{}\t{}\n\t{}\t\t{}\n\t{}\t{}\n\n'
    html_line_template     = '<tr>\n'+ \
        '  <td> {} </td>\n  <td> {} </td> <td> {} </td>\n'+ \
        '  <td> {} </td>\n</tr>\n'

    sql.execute ("""SELECT * FROM ICMP_Mon_CurrentScan
                    WHERE cur_AlertDown = 1 AND cur_Present = 0 AND cur_PresentChanged = 1
                    ORDER BY cur_LastScan""")

    for eventAlert in sql :

        hostname = get_icmphost_name(eventAlert['cur_ip'])
        # print(hostname)

        mail_section_icmphost_down = True
        mail_text_icmphost_down += text_line_template.format (
            'IP: ', eventAlert['cur_ip'],
            'Hostname: ', hostname,
            'Time: ', eventAlert['cur_LastScan'], 
            'Status: ', 'Down')
        mail_html_icmphost_down += html_line_template.format (
            eventAlert['cur_ip'], hostname, eventAlert['cur_LastScan'], 'Down')

    format_report_section_icmp (mail_section_icmphost_down, 'SECTION_DEVICES_DOWN',
        'TABLE_DEVICES_DOWN', mail_text_icmphost_down, mail_html_icmphost_down)

    # Compose Events Section (includes Down as an Event)
    mail_section_events = False
    mail_text_events   = ''
    mail_html_events   = ''
    text_line_template = '{}{}\n\t{}\t{}\n\t{}\t\t{}\n\t{}\t\t{} ms\n\t{}\t{}\n\n'
    html_line_template = '<tr>\n  <td>'+ \
            '  {} </td>\n  <td> {} </td> <td> {} </td>\n'+ \
            '  <td> {} </td>\n <td> {} </td>\n'+ \
            '  </tr>\n'

    sql.execute ("""SELECT * FROM ICMP_Mon_CurrentScan
                    WHERE cur_AlertEvents = 1 AND cur_PresentChanged = 1
                    ORDER BY cur_LastScan""")

    for eventAlert in sql:
        mail_section_events = True

        hostname = get_icmphost_name(eventAlert['cur_ip'])
        # print(hostname)

        icmp_online_status = 'Up' if eventAlert['cur_Present'] == 1 else 'Down'
        mail_text_events += text_line_template.format (
            'IP: ', eventAlert['cur_ip'],
            'Hostname:', hostname,
            'Time: ', eventAlert['cur_LastScan'], 
            'RTT: ', eventAlert['cur_avgrrt'], 
            'Status: ', icmp_online_status)
        mail_html_events += html_line_template.format (
            eventAlert['cur_ip'], hostname, eventAlert['cur_LastScan'], eventAlert['cur_avgrrt'], icmp_online_status)

    format_report_section_icmp (mail_section_events, 'SECTION_EVENTS',
        'TABLE_EVENTS', mail_text_events, mail_html_events)

    # # Send Mail
    if mail_section_icmphost_down == True or mail_section_events == True :
        sending_notifications ('icmp_mon', mail_html_icmphost, mail_text_icmphost)
    else :
        print ('    No changes to report...')

    sql_connection.commit()

#===============================================================================
# REPORTING
#===============================================================================
def email_reporting():
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

    with open(f'{PIALERT_BACK_PATH}/report_template.txt', 'r') as template_file:
        mail_text = template_file.read()
    with open(f'{PIALERT_BACK_PATH}/report_template.html', 'r') as template_file:
        mail_html = template_file.read()
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
        sending_notifications ('pialert', mail_html, mail_text)
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

    sql_connection.commit()

    try:
        enable_services_monitoring = SCAN_WEBSERVICES
    except NameError:
        enable_services_monitoring = False
    if enable_services_monitoring and str(startTime)[15] == "0":
        service_monitoring_notification()

    try:
        enable_icmp_monitoring = ICMPSCAN_ACTIVE
    except NameError:
        enable_icmp_monitoring = False
    if enable_icmp_monitoring:
        icmphost_monitoring_notification()

    closeDB()

#-------------------------------------------------------------------------------
def send_pushsafer(_Text):
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
        "t": f'Pi.Alert Message - {subheadline}',
        "m": _pushsafer_Text,
        "s": 22,
        "v": 3,
        "i": 148,
        "c": '#ef7f7f',
        "d": notification_target,
        "u": REPORT_DASHBOARD_URL,
        "ut": 'Open Pi.Alert',
        "k": PUSHSAFER_TOKEN,
    }
    requests.post(url, data=post_fields)

#-------------------------------------------------------------------------------
def send_pushover(_Text):
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
        "title": f'Pi.Alert Message - {subheadline}',
        "message": _pushover_Text,
    }
    requests.post(url, data=post_fields)

#-------------------------------------------------------------------------------
def send_ntfy(_Text):
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
        usernamepassword = f"{NTFY_USER}:{NTFY_PASSWORD}"
        basichash = b64encode(
            bytes(f'{NTFY_USER}:{NTFY_PASSWORD}', "utf-8")
        ).decode("ascii")

    # add authorization header with hash
        headers["Authorization"] = f"Basic {basichash}"

    requests.post(f"{NTFY_HOST}/{NTFY_TOPIC}", data=_Text, headers=headers)

#-------------------------------------------------------------------------------
def send_telegram(_Text):
    # Remove one linebrake between "Server" and the headline of the event type
    _telegram_Text = _Text.replace('\n\n\n', '\n\n')
    # extract event type headline to use it in the notification headline
    findsubheadline = _telegram_Text.split('\n')
    subheadline = findsubheadline[3]
    runningpath = os.path.abspath(os.path.dirname(__file__))
    stream = os.popen(
        f'{runningpath}/shoutrrr/{SHOUTRRR_BINARY}/shoutrrr send --url "{TELEGRAM_BOT_TOKEN_URL}" --message "{_telegram_Text}" --title "Pi.Alert - {subheadline}"'
    )

#-------------------------------------------------------------------------------
def send_webgui(_Text):
    # Remove one linebrake between "Server" and the headline of the event type
    _webgui_Text = _Text.replace('\n\n\n', '\n\n')
    # extract event type headline to use it in the notification headline
    findsubheadline = _webgui_Text.split('\n')
    subheadline = findsubheadline[3]
    _webgui_filename = time.strftime("%Y%m%d-%H%M%S") + "_" + subheadline + ".txt"
    if (os.path.exists(REPORTPATH_WEBGUI + _webgui_filename) == False):
        with open(REPORTPATH_WEBGUI + _webgui_filename, "w") as f:
            f.write(_webgui_Text)
    set_pia_reports_permissions()

#===============================================================================
# Sending Notofications
#===============================================================================
def sending_notifications(_type, _html_text, _txt_text):

    if _type in ['webservice', 'rogue_dhcp']:
        if REPORT_MAIL_WEBMON :
            print ('    Sending report by email...')
            send_email (_txt_text, _html_text)
        else :
            print ('    Skip mail...')
        if REPORT_PUSHSAFER_WEBMON :
            print ('    Sending report by PUSHSAFER...')
            send_pushsafer (_txt_text)
        else :
            print ('    Skip PUSHSAFER...')
        if REPORT_PUSHOVER_WEBMON :
            print ('    Sending report by PUSHOVER...')
            send_pushover (_txt_text)
        else :
            print ('    Skip PUSHOVER...')
        if REPORT_TELEGRAM_WEBMON :
            print ('    Sending report by Telegram...')
            send_telegram (_txt_text)
        else :
            print ('    Skip Telegram...')
        if REPORT_NTFY_WEBMON :
            print ('    Sending report by NTFY...')
            send_ntfy (_txt_text)
        else :
            print ('    Skip NTFY...')
        if REPORT_WEBGUI_WEBMON :
            print ('    Save report to file...')
            send_webgui (_txt_text)
        else :
            print ('    Skip WebUI...')
    elif _type in ['pialert', 'icmp_mon']:
        if REPORT_MAIL :
            print ('    Sending report by email...')
            send_email (_txt_text, _html_text)
        else :
            print ('    Skip mail...')
        if REPORT_PUSHSAFER :
            print ('    Sending report by PUSHSAFER...')
            send_pushsafer (_txt_text)
        else :
            print ('    Skip PUSHSAFER...')
        if REPORT_PUSHOVER :
            print ('    Sending report by PUSHOVER...')
            send_pushover (_txt_text)
        else :
            print ('    Skip PUSHOVER...')
        if REPORT_TELEGRAM :
            print ('    Sending report by Telegram...')
            send_telegram (_txt_text)
        else :
            print ('    Skip Telegram...')
        if REPORT_NTFY :
            print ('    Sending report by NTFY...')
            send_ntfy (_txt_text)
        else :
            print ('    Skip NTFY...')
        if REPORT_WEBGUI :
            print ('    Save report to file...')
            send_webgui (_txt_text)
        else :
            print ('    Skip WebUI...')

#-------------------------------------------------------------------------------
def format_report_section(pActive, pSection, pTable, pText, pHTML):
    global mail_text
    global mail_html

    # Replace section text
    if pActive:
        mail_text = mail_text.replace(f'<{pTable}>', pText)
        mail_html = mail_html.replace(f'<{pTable}>', pHTML)       

        mail_text = remove_tag (mail_text, pSection)
        mail_html = remove_tag (mail_html, pSection)
    else:
        mail_text = remove_section (mail_text, pSection)
        mail_html = remove_section (mail_html, pSection)

#-------------------------------------------------------------------------------
def format_report_section_services(pActive, pSection, pTable, pText, pHTML):
    global mail_text_webservice
    global mail_html_webservice

    # Replace section text
    if pActive:
        mail_text_webservice = mail_text_webservice.replace(f'<{pTable}>', pText)
        mail_html_webservice = mail_html_webservice.replace(f'<{pTable}>', pHTML)       

        mail_text_webservice = remove_tag (mail_text_webservice, pSection)
        mail_html_webservice = remove_tag (mail_html_webservice, pSection)
    else:
        mail_text_webservice = remove_section (mail_text_webservice, pSection)
        mail_html_webservice = remove_section (mail_html_webservice, pSection)

#-------------------------------------------------------------------------------
def format_report_section_icmp(pActive, pSection, pTable, pText, pHTML):
    global mail_html_icmphost
    global mail_text_icmphost

    # Replace section text
    if pActive:
        mail_text_icmphost = mail_text_icmphost.replace(f'<{pTable}>', pText)
        mail_html_icmphost = mail_html_icmphost.replace(f'<{pTable}>', pHTML)       

        mail_text_icmphost = remove_tag (mail_text_icmphost, pSection)
        mail_html_icmphost = remove_tag (mail_html_icmphost, pSection)
    else:
        mail_text_icmphost = remove_section (mail_text_icmphost, pSection)
        mail_html_icmphost = remove_section (mail_html_icmphost, pSection)

#-------------------------------------------------------------------------------
def remove_section(pText, pSection):
    # Search section into the text
    if pText.find(f'<{pSection}>') >= 0 and pText.find(f'</{pSection}>') >= 0: 
        # return text without the section
        return (
            pText[: pText.find(f'<{pSection}>')]
            + pText[pText.find(f'</{pSection}>') + len(pSection) + 3 :]
        )
    else:
        # return all text
        return pText

#-------------------------------------------------------------------------------
def remove_tag(pText, pTag):
    # return text without the tag
    return pText.replace(f'<{pTag}>', '').replace(f'</{pTag}>', '')

#-------------------------------------------------------------------------------
def write_file(pPath, pText):
    # Write the text depending using the correct python version
    if sys.version_info < (3, 0):
        file = io.open (pPath , mode='w', encoding='utf-8')
        file.write ( pText.decode('unicode_escape') )
    else:
        file = open (pPath, 'w', encoding='utf-8')
        file.write (pText) 

    file.close() 

#-------------------------------------------------------------------------------
def append_line_to_file(pPath, pText):
    # append the line depending using the correct python version
    if sys.version_info < (3, 0):
        file = io.open (pPath , mode='a', encoding='utf-8')
        file.write ( pText.decode('unicode_escape') )
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
        escaped_password = repr(SMTP_PASS)[1:-1]
        smtp_connection.login (SMTP_USER, escaped_password)
    smtp_connection.sendmail (REPORT_FROM, REPORT_TO, msg.as_string())
    smtp_connection.quit()

#-------------------------------------------------------------------------------
def SafeParseGlobalBool(boolVariable):
    return eval(boolVariable) if boolVariable in globals() else False

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
def closeDB():
    global sql_connection
    global sql

    # Check if DB is open
    if sql_connection is None:
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
