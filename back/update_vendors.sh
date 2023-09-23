#!/bin/sh
# ------------------------------------------------------------------------------
#  Pi.Alert
#  Open Source Network Guard / WIFI & LAN intrusion detector 
#
#  update_vendors.sh - Back module. IEEE Vendors db update
# ------------------------------------------------------------------------------
#  Puche 2021        pi.alert.application@gmail.com        GNU GPLv3
# ------------------------------------------------------------------------------

# ----------------------------------------------------------------------
#  Main directories to update:
#    /usr/share/arp-scan
#    /usr/share/ieee-data
#    /var/lib/ieee-data
# ----------------------------------------------------------------------

# ----------------------------------------------------------------------
echo "Updating... /usr/share/ieee-data/"
cd /usr/share/ieee-data/

sudo aria2c --dir=/usr/share/ieee-data --max-connection-per-server=3 --allow-overwrite=true --quiet=true "https://standards-oui.ieee.org/oui/oui.csv"
sudo aria2c --dir=/usr/share/ieee-data --max-connection-per-server=3 --allow-overwrite=true --quiet=true "https://standards-oui.ieee.org/oui/oui.txt"
sudo aria2c --dir=/usr/share/ieee-data --max-connection-per-server=3 --allow-overwrite=true --quiet=true "https://standards-oui.ieee.org/oui36/oui36.csv"
sudo aria2c --dir=/usr/share/ieee-data --max-connection-per-server=3 --allow-overwrite=true --quiet=true "https://standards-oui.ieee.org/oui36/oui36.txt"
sudo aria2c --dir=/usr/share/ieee-data --max-connection-per-server=3 --allow-overwrite=true --quiet=true "https://standards-oui.ieee.org/oui28/mam.csv"
sudo aria2c --dir=/usr/share/ieee-data --max-connection-per-server=3 --allow-overwrite=true --quiet=true "https://standards-oui.ieee.org/oui28/mam.txt"
sudo aria2c --dir=/usr/share/ieee-data --max-connection-per-server=3 --allow-overwrite=true --quiet=true "https://standards-oui.ieee.org/iab/iab.csv"
sudo aria2c --dir=/usr/share/ieee-data --max-connection-per-server=3 --allow-overwrite=true --quiet=true "https://standards-oui.ieee.org/iab/iab.txt"

# ----------------------------------------------------------------------
echo ""
echo "Updating... /usr/share/arp-scan/"
cd /usr/share/arp-scan

# Update from /var/lib/ieee-data
sudo get-oui -v

# Update from ieee website
# sudo get-iab -v -u https://standards-oui.ieee.org/iab/iab.txt
# sudo get-oui -v -u https://standards-oui.ieee.org/oui/oui.txt

# Update from Sanitized oui (linuxnet.ca)
# sudo get-oui -v -u https://linuxnet.ca/ieee/oui.txt

