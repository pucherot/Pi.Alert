#!/bin/sh

# Input file
input_file="../config/pialert_test.conf"

# Get Value from config file
# General
REWRITE_PIALERT_PATH=$(grep "^PIALERT_PATH" $input_file)
REWRITE_DB_PATH=$(grep "^DB_PATH" $input_file)
REWRITE_LOG_PATH=$(grep "^LOG_PATH" $input_file)
REWRITE_PRINT_LOG=$(grep "^PRINT_LOG" $input_file)
REWRITE_VENDORS_DB=$(grep "^VENDORS_DB" $input_file)
REWRITE_PIALERT_WEB_PROTECTION=$(grep "^PIALERT_WEB_PROTECTION" $input_file)
REWRITE_PIALERT_WEB_PASSWORD=$(grep "^PIALERT_WEB_PASSWORD" $input_file)
REWRITE_SCAN_WEBSERVICES=$(grep "^SCAN_WEBSERVICES" $input_file)
# Special Protocol Scanning
REWRITE_SCAN_ROGUE_DHCP=$(grep "^SCAN_ROGUE_DHCP" $input_file)
REWRITE_DHCP_SERVER_ADDRESS=$(grep "^DHCP_SERVER_ADDRESS" $input_file)
# Mail-Account Settings
REWRITE_SMTP_SERVER=$(grep "^SMTP_SERVER" $input_file)
REWRITE_SMTP_PORT=$(grep "^SMTP_PORT" $input_file)
REWRITE_SMTP_USER=$(grep "^SMTP_USER" $input_file)
REWRITE_SMTP_PASS=$(grep "^SMTP_PASS" $input_file)
REWRITE_SMTP_SKIP_TLS=$(grep "^SMTP_SKIP_TLS" $input_file)
REWRITE_SMTP_SKIP_LOGIN=$(grep "^SMTP_SKIP_LOGIN" $input_file)
# WebGUI Reporting
REWRITE_REPORT_WEBGUI=$(grep "^REPORT_WEBGUI" $input_file)
REWRITE_REPORT_WEBGUI_WEBMON=$(grep "^REPORT_WEBGUI_WEBMON" $input_file)

# Set defaults if empty
if [ -z "$REWRITE_SCAN_WEBSERVICES" ]
then
    REWRITE_SCAN_WEBSERVICES="SCAN_WEBSERVICES       = True"
fi

if [ -z "$REWRITE_SCAN_ROGUE_DHCP" ]
then
    REWRITE_SCAN_ROGUE_DHCP="SCAN_ROGUE_DHCP        = False"
fi

if [ -z "$REWRITE_DHCP_SERVER_ADDRESS" ]
then
    REWRITE_DHCP_SERVER_ADDRESS="DHCP_SERVER_ADDRESS    = '0.0.0.0'"
fi

if [ -z "$REWRITE_REPORT_WEBGUI" ]
then
    REWRITE_REPORT_WEBGUI="REPORT_WEBGUI         = True"
fi

if [ -z "$REWRITE_REPORT_WEBGUI_WEBMON" ]
then
    REWRITE_REPORT_WEBGUI_WEBMON="REPORT_WEBGUI_WEBMON  = True"
fi

# Write clean Config
cat >../config/pialert_new.conf <<EOL
# General Settings
# ----------------------
${REWRITE_PIALERT_PATH}
${REWRITE_DB_PATH}
${REWRITE_LOG_PATH}
${REWRITE_PRINT_LOG}
${REWRITE_VENDORS_DB}
${REWRITE_PIALERT_WEB_PROTECTION}
${REWRITE_PIALERT_WEB_PASSWORD}
${REWRITE_SCAN_WEBSERVICES}

# Special Protocol Scanning
# ----------------------
${REWRITE_SCAN_ROGUE_DHCP}
${REWRITE_DHCP_SERVER_ADDRESS}

# Mail-Account Settings
# ----------------------
${REWRITE_SMTP_SERVER}
${REWRITE_SMTP_PORT}
${REWRITE_SMTP_USER}
${REWRITE_SMTP_PASS}
${REWRITE_SMTP_SKIP_TLS}
${REWRITE_SMTP_SKIP_LOGIN}

# WebGUI Reporting
# ----------------------
${REWRITE_REPORT_WEBGUI}
${REWRITE_REPORT_WEBGUI_WEBMON}

# Mail Reporting
# ----------------------
REPORT_MAIL          = False
REPORT_MAIL_WEBMON   = False
REPORT_FROM          = 'Pi.Alert <' + SMTP_USER +'>'
REPORT_TO            = 'user@gmail.com'
REPORT_DEVICE_URL    = 'http://pi.alert/deviceDetails.php?mac='
REPORT_DASHBOARD_URL = 'http://pi.alert/'

# Pushsafer
# ----------------------
REPORT_PUSHSAFER         = False
REPORT_PUSHSAFER_WEBMON  = False
PUSHSAFER_TOKEN          = 'ApiKey'
PUSHSAFER_DEVICE         = 'a'

# ntfy
# ----------------------
REPORT_NTFY         = False
REPORT_NTFY_WEBMON  = False
NTFY_HOST           = 'https://ntfy.sh'
NTFY_TOPIC          = 'replace_my_secure_topicname_91h889f28'
NTFY_USER           = 'user'
NTFY_PASSWORD	    = 'password'
NTFY_PRIORITY 	    = 'default'

# Shoutrrr
# ----------------------
SHOUTRRR_BINARY    = 'armhf'
# SHOUTRRR_BINARY    = 'arm64'
# SHOUTRRR_BINARY    = 'x86'

# Telegram via Shoutrrr
# ----------------------
REPORT_TELEGRAM         = False
REPORT_TELEGRAM_WEBMON  = False
TELEGRAM_BOT_TOKEN_URL  = '<Your generated servive URL for telegram - use ~/pialert/back/shoutrrr/<your Systemtyp>/shoutrrr generate telegram>'

# DynDNS
# ----------------------
QUERY_MYIP_SERVER = 'https://myipv4.p1.opendns.com/get_my_ip'
DDNS_ACTIVE       = False
DDNS_DOMAIN       = 'your_domain.freeddns.org'
DDNS_USER         = 'dynu_user'
DDNS_PASSWORD     = 'A0000000B0000000C0000000D0000000'
DDNS_UPDATE_URL   = 'https://api.dynu.com/nic/update?'

# Pi-hole Configuration
# ----------------------
PIHOLE_ACTIVE     = False
PIHOLE_DB         = '/etc/pihole/pihole-FTL.db'
DHCP_ACTIVE       = False
DHCP_LEASES       = '/etc/pihole/dhcp.leases'

# Maintenance Tasks Cron
# ----------------------
DAYS_TO_KEEP_ONLINEHISTORY = 30
DAYS_TO_KEEP_EVENTS = 90

# Arp-scan Options & Samples
# ----------------------
# Scan local network (default)
SCAN_SUBNETS    = '--localnet'
#
# Scan using interface eth0
# SCAN_SUBNETS    = '--localnet --interface=eth0'
#
# Scan multiple interfaces (eth1 and eth0):
# SCAN_SUBNETS    = [ '192.168.1.0/24 --interface=eth0', '192.168.2.0/24 --interface=eth1' ]

EOL

