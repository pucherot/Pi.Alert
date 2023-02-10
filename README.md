# Pi.Alert
<!--- --------------------------------------------------------------------- --->

WIFI / LAN intruder detector with web service monitoring.

Scan the devices connected to your WIFI / LAN and alert you the connection of
unknown devices. It also warns if a "always connected" device disconnects. In addition, it is 
possible to check web services for availability. For this purpose HTTP status codes and the 
response time of the service are evaluated. If a service fails or the HTTP status code changes, 
a notification can be sent.

![Main screen][main]

## "Under the hood" modifications within this fork
  - Only one scan cycle
  - The Backend has the additional option "cleanup"
  - "[pialert-cli](docs/PIALERTCLI.md)" that helps to configure login, password and DB migration
  - API for basic queries

## Obvious modifications within this fork
  - Maintenance tasks
  - Settings page (optional Login, Darkmode and Theme selection, Multilanguage, Config-Editor)
  - FAQ page
  - Additional notifications
  - Simple network relationship display
  - Web service monitoring

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
  - **Web service monitoring**. This method is optional. An HTTP request is 
        sent and the web server's response is evaluated. If seft signed 
        certificates are used, no validation of the certificate is performed.

## Components
The system consists of three parts:

### Back
In charge of:
  - Scan the network searching connected devices using the scanning methods
    described
  - Store the information in the DB
  - Report the changes detected by e-mail and/or other services (Pushsafer, NTFY, Gotify and Telegram via [shoutrrr](https://github.com/containrrr/shoutrrr/)) and to the Frontend
  - DB cleanup tasks via cron
  - a [pialert-cli](docs/PIALERTCLI.md) that helps to configure login, password and some other things

[pialert-cli - Overview of supported commands](docs/PIALERTCLI.md)

[shoutrrr - Implementation notes](docs/SHOUTRRR.md)

  | ![Report 1][report1] | ![Report 2][report2] |
  | -------------------- | -------------------- |

### Front
There is a configurable login to prevent unauthorized use. The default password is "123456". By default, this is disabled. If you want to use password protection, enable it in the configuration file ~/pialert/config/pialert.conf or via [pialert-cli](docs/PIALERTCLI.md).

A web frontend that allows:
  - Manage the devices inventory and the characteristics
  - Display in a visual way all the information collected by the back *(Sessions, Connected devices, Favorites, Events, Presence, Internet IP address changes, ...)*
  - Manual Nmap scans
  - Speedtest for device "Internet" in the details view
  - Simple network relationship display
  - Maintenance tasks and settings like:
    - Status information *(active scans, database size, backup counter)*
    - Light/Dark-Mode switch and theme selection *(blue, red, green, yellow, black, purple)*
    - Language selection *(english, german, spanish, french)*
    - Enable/Disable network activity graph 
    - Pause arp-scan
    - Set API-key
    - Enable/Disable login
    - DB maintenance tools and a possibility for backup, restore and cleanup the database and the config file
    - Send test notifications
    - Config file editor
  - Web service monitoring
  - Help/FAQ section

  | ![Screen 1][screen1]                            | ![Screen 2][screen2]                         | ![Screen 3][screen3]                                           | 
  | ----------------------------------------------- | -------------------------------------------- | -------------------------------------------------------------- |
  | ![Screen 4][screen4]                            | ![Maintain screen dark][maintain_dark]       | ![HelpFAQ screen dark][helpfaq_dark]                           |
  | ![Notification screen dark][notification_dark]  | ![Webservices screen dark][webservices_dark] | ![Webservices Details screen dark][webservices_details_dark]   |

Based on the original, I have created new icons according to the skins. Since I made the experience that iOS devices do not load homescreen icons from insecure sources (no SSL or selfsigned SSL), you can also link the icons directly from this repository.

```https://raw.githubusercontent.com/leiweibau/Pi.Alert/main/front/img/favicons/glass_black_white.png```

Instead of 'glass_black_white.png' you can use one of the following files.

[List of Favicons/Homescreen icons](docs/ICONS.md)


### API
A possibility to send a request to the Pi.Alert backend via different ways. Currently the API offers the possibility to query 4 things:
  - System status *(Scan on or off, Counts all, online, offline, archived and new devices)*
  - All online devices *(MAC, Name, Vendor, LastIP, Infrastructure, Infrastructure_port)*
  - All offline devices *(MAC, Name, Vendor, LastIP, Infrastructure, Infrastructure_port)*
  - Information about a specific device *(all information, without events and presence)*

With the API (from Pi.Alert 3.7.9+) it is possible to integrate Pi.Alert into Home Assistant. How it works, you can look up in the API documentation.

[Pi.Alert API Usage and Examples / Home Assistant integration](docs/API-USAGE.md)

# Installation
<!--- --------------------------------------------------------------------- --->
Initially designed to run on a Raspberry Pi, probably it can run on many other
Linux distributions. With minor adjustments (FAQ page) I tested Pi.Alert on Dietpi and Ubuntu Server. 
An also active developed Docker version of Pi.Alert can be found here: [jokob-sk/Pi.Alert](https://github.com/jokob-sk/Pi.Alert)

- One-step Automated Install:
  #### `curl -sSL https://github.com/leiweibau/Pi.Alert/raw/main/install/pialert_install.sh | bash`

- [Installation Guide (step by step)](docs/INSTALL.md)

# Update
<!--- --------------------------------------------------------------------- --->
This update script is only recommended for an already existing installation of this fork. If you are using another fork, 
I recommend uninstalling it first. If you backup the database, it may be possible to continue using it with my fork after a patch.

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

### Versions History
  [Versions History](docs/VERSIONS_HISTORY.md)
  
  An archive of older versions can be found at https://leiweibau.net/archive/pialert/

### License
  GPL 3.0
  [Read more here](LICENSE.txt)

  Source of the animated GIF (Loading Animation)
  https://commons.wikimedia.org/wiki/File:Loading_Animation.gif
  
  Source of the selfhosted Fonts
  https://github.com/adobe-fonts/source-sans
  
  Source of the Bootstrap Icons
  https://github.com/twbs/icons

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
[notification_dark]: /docs/img/9_notifications.jpg                "Notification screen dark"
[webservices_dark]: /docs/img/7_webservices.jpg                   "Webservices screen dark"
[webservices_details_dark]: /docs/img/8_webservices_details.jpg   "Webservices Details screen dark"
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
