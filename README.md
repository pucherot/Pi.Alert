# Pi.Alert
<!--- --------------------------------------------------------------------- --->

WIFI / LAN intruder detector.

Scan the devices connected to your WIFI / LAN and alert you the connection of
unknown devices. It also warns if a "always connected" device disconnects.

![Main screen][main]
*(Apologies for my English and my limited knowledge of Python, php and
JavaScript)*

## Modifications within this Fork
  - Only one scan cycle
  - Modified scanmethod. If you want to go back to the original method comment line 546 and uncomment line 549 in ~/pialert/back/pialert.py
  - Because of the modified scan, the extended scan parameters in the configuration file do not work. For this reason they were removed. 
  - The Backend has the additional option "cleanup"
  - "pialert-cli" that helps to configure login, password and DB migration

## How it works
The system continuously scans the network for:
  - New devices
  - New connections (re-connections)
  - Disconnections
  - "Always Connected" devices down
  - Devices IP changes
  - Internet IP address changes

## Scan Methods
Up to three scanning methods are used:
  - **Method 1: arp-scan**. The arp-scan system utility is used to search
        for devices on the network using arp frames.
  - **Method 2: Pi-hole**. This method is optional and complementary to
        method 1. If the Pi-hole DNS server is active, Pi.Alert examines its
        activity looking for active devices using DNS that have not been
        detected by method 1.
  - **Method 3. dnsmasq**. This method is optional and complementary to the
        previous methods. If the DHCP server dnsmasq is active, Pi.Alert
        examines the DHCP leases (addresses assigned) to find active devices
        that were not discovered by the other methods.

## Components
The system consists of three parts:

### Back
In charge of:
  - Scan the network searching connected devices using the scanning methods
    described
  - Store the information in the DB
  - Report the changes detected by e-mail and/or other services (Pushsafer, NTFY, Gotify and Telegram via [shoutrrr](https://github.com/containrrr/shoutrrr/))
  - DB cleanup tasks via cron
  - a pialert-cli that helps to configure login, password and some other things

[pialert-cli - Overview of supported commands](docs/PIALERTCLI.md)

[shoutrrr - Implementation notes](docs/SHOUTRRR.md)

  | ![Report 1][report1] | ![Report 2][report2] |
  | -------------------- | -------------------- |

### Front
There is a configurable login to prevent unauthorized use. The default password is "123456". By default, this is disabled. If you want to use password protection, enable it in the configuration file ~/pialert/config/pialert.conf or via pialert-cli.

A web frontend that allows:
  - Manage the devices inventory and the characteristics
  - Display in a visual way all the information collected by the back
    - Sessions
    - Connected devices
    - Favorites
    - Events
    - Presence
    - Concurrent devices
    - Down alerts
    - IP's
    - ...
  - Manual Nmap scans
  - Speedtest for device "Internet" in the details view
  - Simple network relationship display
  - Maintenance tasks and settings like:
    - Status information (active scans, database size, backup counter)
    - Theme selection (blue, red, green, yellow, black, purple)
    - Language selection (english, german, spanish)
    - Light/Dark-Mode switch
    - Enable/Disable network activity graph 
    - Pause arp-scan
    - Set API-key
    - Enable/Disable login
    - DB maintenance tools
    - DB backup, restore and cleanup
    - Send test notifications
  - Help/FAQ section 

  | ![Screen 1][screen1]                   | ![Screen 2][screen2]                 |
  | -------------------------------------- | ------------------------------------ |
  | ![Screen 3][screen3]                   | ![Screen 4][screen4]                 |
  | ![Maintain screen dark][maintain_dark] | ![HelpFAQ screen dark][helpfaq_dark] |

Based on the original, I have created new icons according to the skins. Since I made the experience that iOS devices do not load homescreen icons from insecure sources (no SSL or selfsigned SSL), you can also link the icons directly from this repository.

```https://raw.githubusercontent.com/leiweibau/Pi.Alert/main/front/img/favicons/glass_black_white.png```

Instead of 'glass_black_white.png' you can use one of the following files.

[List of Favicons/Homescreen icons](docs/ICONS.md)


### API
A possibility to send a request to the Pi.Alert backend via different ways. Currently the API offers the possibility to query 4 things:
  - System status (Counts all, online, offline, archived and new devices).
  - All online devices (MAC, Name, Vendor, LastIP, Infrastructure, Infrastructure_port).
  - All offline devices (MAC, Name, Vendor, LastIP, Infrastructure, Infrastructure_port)
  - Information about a specific device (all information, without events and presence)

With the API (from Pi.Alert 3.7.9+) it is possible to integrate Pi.Alert into Home Assistant. How it works, you can look up in the API documentation.

[Pi.Alert API Usage and Examples / Home Assistant integration](docs/API-USAGE.md)

# Installation
<!--- --------------------------------------------------------------------- --->
Initially designed to run on a Raspberry Pi, probably it can run on many other
Linux distributions.

- One-step Automated Install:
  #### `curl -sSL https://github.com/leiweibau/Pi.Alert/raw/main/install/pialert_install.sh | bash`

- [Installation Guide (step by step)](docs/INSTALL.md)

# Update
<!--- --------------------------------------------------------------------- --->
This update script is only recommended for an already existing installation of this fork. The script is still being tested.

- One-step Automated Update:
  #### `curl -sSL https://github.com/leiweibau/Pi.Alert/raw/main/install/pialert_update.sh | bash`

# Uninstall process
<!--- --------------------------------------------------------------------- --->
  - [Unistall process](docs/UNINSTALL.md)


# Device Management
<!--- --------------------------------------------------------------------- --->
  - [Device Management instructions](docs/DEVICE_MANAGEMENT.md)


## Other useful info
<!--- --------------------------------------------------------------------- --->

### [Versions History](docs/VERSIONS_HISTORY.md)

### Powered by:
  | Product       | Objetive                                                |
  | ------------- | ------------------------------------------------------- |
  | Python        | Programming language for the Back                       |
  | PHP           | Programming language for the Front-end                  |
  | JavaScript    | Programming language for the Front-end                  |
  | Bootstrap     | Front-end framework                                     |
  | Admin.LTE     | Bootstrap template                                      |
  | FullCalendar  | Calendar component                                      |
  | Sqlite        | DB engine                                               |
  | Lighttpd      | Webserver                                               |
  | arp-scan      | Scan network using arp commands                         |
  | Pi.hole       | DNS Server with Ad-block                                |
  | dnsmasq       | DHCP Server                                             |
  | nmap          | Network Scanner                                         |
  | zip           | Filecompression Tool                                    |
  | speedtest-cli | Python SpeedTest https://github.com/sivel/speedtest-cli |
  | shoutrrr      | Notification library https://github.com/containrrr/shoutrrr/ |
  | deepl.com     | translation                                             |

### License
  GPL 3.0
  [Read more here](LICENSE.txt)

  Source of the animated GIF (Loading Animation)
  https://commons.wikimedia.org/wiki/File:Loading_Animation.gif
  
  Source of the selfhosted Fonts
  https://github.com/adobe-fonts/source-sans

### Contact
  pi.alert.application@gmail.com
  
  ***Suggestions and comments are welcome***

### Special thanks 🥇

  This code is a collaborative body of work, with special thanks to: 

[Macleykun](https://github.com/Macleykun), [Final-Hawk](https://github.com/Final-Hawk), [TeroRERO](https://github.com/terorero), [jokob-sk](https://github.com/jokob-sk/Pi.Alert) and many more

<!--- --------------------------------------------------------------------- --->
[main]:    ./docs/img/1_devices.jpg           "Main screen"
[screen1]: ./docs/img/2_1_device_details.jpg  "Screen 1"
[screen2]: ./docs/img/2_2_device_sessions.jpg "Screen 2"
[screen3]: ./docs/img/2_3_device_presence.jpg "Screen 3"
[screen4]: ./docs/img/3_presence.jpg          "Screen 4"
[report1]: ./docs/img/4_report_1.jpg          "Report sample 1"
[report2]: ./docs/img/4_report_2.jpg          "Report sample 2"
[maintain_dark]: /docs/img/5_maintain.jpg     "Maintain screen dark"
[helpfaq_dark]: /docs/img/6_helpfaq.jpg       "HelpFAQ screen dark"
[glass_black_white]: /favicons/glass_black_white.png       "glass_black_white.png"
[glass_blue_white]: /favicons/glass_blue_white.png       "glass_blue_white.png"
[glass_green_white]: /favicons/glass_green_white.png       "glass_green_white.png"
[glass_red_white]: /favicons/glass_red_white.png       "glass_red_white.png"
[glass_purple_white]: /favicons/glass_purple_white.png       "glass_purple_white.png"
[glass_yellow_white]: /favicons/glass_yellow_white.png       "glass_yellow_white.png"
[glass_blue_black]: /favicons/glass_blue_black.png       "glass_blue_black.png"
[glass_green_black]: /favicons/glass_green_black.png       "glass_green_black.png"
[glass_red_black]: /favicons/glass_red_black.png       "glass_red_black.png"
[glass_purple_black]: /favicons/glass_purple_black.png       "glass_purple_black.png"
[glass_yellow_black]: /favicons/glass_yellow_black.png       "glass_yellow_black.png"
