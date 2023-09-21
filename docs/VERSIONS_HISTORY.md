# Pi.Alert Version History
<!--- --------------------------------------------------------------------- --->

  | Version | Description                                                              |
  | ------- | ------------------------------------------------------------------------ |
  |  v3.8   | Update AdminLTE components, minor improvements and features, cleanup     |
  |  v3.7.x | Bug fixing, minor improvements and features                              |
  |  v3.7   | New features & Usability improvements                                    |
  |  v3.5   | Update AdminLTE, Major set of New features & Enhancements                |
  |  v3.00  | Major set of New features & Enhancements                                 |
  |  v2.70  | New features & Usability improvements in the web prontal                 |
  |  v2.61  | Bug fixing                                                               |
  |  v2.60  | Improved the compability of installation process (Ubuntu)                |
  |  v2.56  | Bug fixing                                                               |
  |  v2.55  | Bug fixing                                                               |
  |  v2.52  | Bug fixing                                                               |
  |  v2.51  | Bug fixing                                                               |
  |  v2.50  | First public release                                                     |

## Pi.Alert v3.8.0
<!--- --------------------------------------------------------------------- --->
New:
 - Config backup counter in the status box on the maintenance ppage
 - Delete all notifications at once on the notification page

Updated:
 - AdminLTE/Bootstrap 3.3.7 to 3.4.1
 - AdminLTE/eve.js 0.5.0 to 0.5.4
 - AdminLTE/Ion.RangeSlider 2.3.0 to 2.3.1
 - AdminLTE/Select2 4.0.8 to 4.0.13
 - Reverse sorting of the notification list. The newest entry is now on top
 - The deletion function of the reports hardened to make manipulations via the frontend more difficult

Changed:
 - Autoreload after sending the test notification
 - Some Code Cleanup
 - Little UI tweaks on the notification page
 - The cleanup of the database backups, except for the latest 3, now also deletes the config file backups, except for the last 3

Removed:
 - Unnecessary files from AdminLTE components

## Pi.Alert v3.7.19
<!--- --------------------------------------------------------------------- --->
New:
 - WebGUI Notification: Displays the notifications now also on the web page. The indication is a small red badge above the Pi.Alert icon, in the upper right corner. Clicking on the icon opens a menu with a button that leads to a page with all notifications. These can now be viewed and deleted individually. The deletion of all notifications will be implemented later. The badge is displayed as long as notifications are present.
This can be disabled in the pialert.conf (REPORT_WEBGUI = False)
 - Page to view all notifications

Updated:
 - Language files
 - FAQ / Help - Network page (en/de)
 - FAQ / Help - General (en/de)

## Pi.Alert v3.7.18
<!--- --------------------------------------------------------------------- --->
New:
 - Possibility to purge Devices not connected since 30 Days

Fixed:
 - Sorting error in the IP column

Updated:
 - Documentation
 - Language files

## Pi.Alert v3.7.17
<!--- --------------------------------------------------------------------- --->
New:
 - Add bootstrap-icons v1.10.3

Fixed:
 - Addressing the issue #67. While it was possible to assign a port to a switch on a router, which was displayed in the router tab, it was not possible to assign multiple switches to a router, so that the router was displayed on all switch tabs. Now it is possible to manually configure ports when configuring a switch on the network page.
 - Issue #68

Updated:
 - Language files
 - Minor visual improvements on the network page
 - Documentation

Note:
 - French Translation is still incomplete

## Pi.Alert v3.7.16
<!--- --------------------------------------------------------------------- --->
New:
 - Configurable columns for device list
 - Optional column "Location"
 - Network page: Indicates the online status by a red or green "light" next to the name. This only works if the node has the same name as in the device list.

Fixed:
 - Install script then existing python version is selected

Updated:
 - Language files
 - nmap_scan hardened
 - Documentation

Changed:
 - header(Location) for login and logout processing, if you use http://pi.alert instead of http://ip/pialert
 - Layout on maintenance page
 - Network page. The mode of operation has not changed. However, with the new update, the first two characters are missing in the display of the "Device Type". This can be corrected by editing the device and selecting the same type (router, switch, ...) again. The background is that I insert numbers at the beginning to be able in the representation of the tabs the devices by type. Previously, the devices were "sorted" according to the order of creation. Now by Router, Switch, WLAN and Powerline

Note:
 - Network page: But you can't map one client or router to multiple nodes. An example to explain. You can assign a router to several switches. However, you can assign only one switch to the router. In the Router tab you can see the connected switches. But you can only see the router on one switch tab.
 - French Translation is still incomplete

## Pi.Alert v3.7.15
<!--- --------------------------------------------------------------------- --->
Fixed:
 - Rollback minifying of the CSS files, due to a page rendering error

Updated:
 - Language files

Changed:
 - The button "Github History" is now called "Version History (leiweibau.net)" and leads to "https://leiweibau.net/archive/pialert/" instead of Github, which allows me to document the versions in a more structured way. It is only a link. No data is transmitted. (60aceff)
 - Disable the DB Restore button if no db backups are available

Note:
 - French Translation is still incomplete

## Pi.Alert v3.7.14
<!--- --------------------------------------------------------------------- --->
New:
 - Add latest Nmap Scan to Log Viewer. This is only save inside the current session. There is no saved log file.
 - Backup and restore of the configuration via the website possible
 - Edit Pi.Alert-Config inside the web interface
 - French language file (incomplete)

Fixed:
 - Missing translation in the notification when language was changed
 - pialert_update.sh (again)
 - Spanish language file on devices page

Updated:
 - All language files
 - Documentation

## Pi.Alert v3.7.13
<!--- --------------------------------------------------------------------- --->
Fixed:
 - Security Fix.
There was the possibility of an RCE (Remote Code Execution) via the nmap scan page. A passing variable was not checked.

Removed:
 - Unnecessary files within the AdminLTE dashboard
 - shoutrrr x86_64 binary (32bit should also work on 64bit systems)

## Pi.Alert v3.7.12
<!--- --------------------------------------------------------------------- --->
New:
 - Add "Cleanup" log to maintenance page

Fixed:
 - Crontab fixed (Installation aborted)
 - pialert_update.sh (Permission issues)

Updated:
 - pialert_install.sh and pialert_update.sh updated to support the new feature
 - Documentation
 - Minor UI improvments (Sidebar behavior)

## Pi.Alert v3.7.11
<!--- --------------------------------------------------------------------- --->
New:
 - API enhancement (new value in "system_status")
 - View logs on frontend (Maintenance page)

Fixed:
 - in some cases speedtest-cli could not be started. The bug has been fixed for new installations. For existing installations, installing an additional package with the command "sudo apt install python-is-python3" helps.

Updated:
 - Documentation
 - Help/FAQ: Handle read only db error.

Changed:
 - UI improvements (Help/FAQ and Maintenance)

Note:
 - Some old code and old comments removed
 - Older versions can be found at "https://leiweibau.net/archive" now

## Pi.Alert v3.7.10
<!--- --------------------------------------------------------------------- --->
New:
  - Add support for multiple interfaces from jokob-sk/Pi.Alert

Fixed:
  - Add php-curl to install script
  - Add dependencies to install script

Updated:
  - Documentation (Readme, API-Usage)
  - pialert_update.sh (extension of the configuration file)

Changed:
  - If the user running Pi.Alert is set up in sudoers, then the database permission can be set to the correct values before the scan (addresses "Read Only Database" errors). To do this, uncomment line 67 in $HOME/pialert/back/pialert.py. This is disabled by default.

## Pi.Alert v3.7.9
<!--- --------------------------------------------------------------------- --->
New:
  - Additional parameter PUSHSAFER_DEVICE in config file. This was done to reduce API-calls. If the parameter is not set or remains set to "a", API calls for all registered devices will be subtracted from the account.

Fixed:
  - Update script (permission problems after update)

Updated:
  - Add new API-Call "system-status". Is shows Last_Scan, number of All_Devices, Offline_Devices, Online_Devices, Archived_Devices and New_Devices
  - Documentation (Readme, API-Usage)

Changed:
  - The tone of the pushsafer notification has been changed

## Pi.Alert v3.7.8
<!--- --------------------------------------------------------------------- --->
New:
  - A rotary wheel has been added to the button to indicate an active function (pausing the arp scan).

Fixed:
  - pialert_install.sh (error while installing python3)

Updated:
  - Update documentation (add links)
  - Update languages

Changed:
  - Conversion of the classic HTML forms to corresponding Javascript versions on the maintenance page in order to be able to use the build-in notification banner.

## Pi.Alert v3.7.7
<!--- --------------------------------------------------------------------- --->
Updated:
  - Update documentation (Github README)
  - Some missing translations

Changed:
  - Handling of "Down" notifications when "Alert All Events" is not checked in device details (1)
  - "Offline" color is now gray instead of red (as on the devices list)
  - Hover effect in tables on network page, for hopefully more overview
  - Some UI changes on "Help/FAQ" page

Note:
  - (1) Previously, "Down" notifications were sent only when "Alert All Events" was active. Now the notifications are sent independently of each other. It may be necessary to check and adjust the configuration of the notification again for individual devices.

## Pi.Alert v3.7.6
<!--- --------------------------------------------------------------------- --->
New:
  - Notification at the start and end of the timer (also when canceling)

Fixed:
  - Optimized the behavior when exiting the timer

Changed:
  - showing the time when the timer was started (Sidebar)

## Pi.Alert v3.7.5
<!--- --------------------------------------------------------------------- --->
New:
  - Timer and automatic activation when pausing Pi.Alert
  - Set pause timer for Pi.Alert with pialert-cli. pialert-cli is now version 0.7
  - Set pause timer for Pi.Alert in the frontend

Fixed:
  - Text formatting in the update notes

Updated:
  - Documentation updated because of timer support
  - Help/FAQ updated because of timer support
  - Languages updated

## Pi.Alert v3.7.4
<!--- --------------------------------------------------------------------- --->
Fixed:
  - shoutrrr/telegram works via pialert-cli and cron but not via frontend

Changed:
  - The display of the update notes has been revised

## Pi.Alert v3.7.3
<!--- --------------------------------------------------------------------- --->
New:
  - Test notification services via pialert-cli, pialert-cli is now Version 0.6
  - Notifications via Telegram based on "shoutrrr"
  - Test notification services via maintenance page
  - Integration of "shoutrrr" to support
  multiple communication platforms

Fixed:
  - Updatescript for pialert

Updated:
  - Update documentation (Github README)

Note:
  - Documentation of telegram setup is
  not includes yet

## Pi.Alert v3.7.2
<!--- --------------------------------------------------------------------- --->
New:
  - Fav/Homescreen icons

Fixed:
  - disable error reporting on maintenance.php and network.php
  - Generating the API key via the frontend does not work after the installation
  - several translation errors and missing entries in language files

Updated:
  - Github README.md and ICONS.md
  - Updatescript (not tested yet)

Note:
  - Work on the update script to make future updates easier

## Pi.Alert v3.7.1
<!--- --------------------------------------------------------------------- --->
New:
  - Add Arp-scan status (Active/Disabled) to sidebar

Fixed:
  - If you have performed a backup / restore, you remain on the current tab after the reload
  - minor fixes in language files and the frontend

Updated:
  - Readme and images

Changed:
  - When switching back to the Pi.Alert tab/window/WebApp, the page reloads

## Pi.Alert v3.7.0
<!--- --------------------------------------------------------------------- --->
New:
  - Basic API Support
  - API usage documented on Github/README
  - System information as in the sidebar of pihole
  - Add not assigned Devices to network page

Fixed:
  - Darkmode improvements for the different skins
  - Lightmode improvements for the different skins
  - Minor bugfixes

Updated:
  - Language files
  - All fields necessary for this fork were added to the initial database
  - FAQ/Help page
  - Github README

Changed:
  - UI Tweaks
  - HTTP POST Variable Check
  - Wording german language file

## Pi.Alert v3.5.0+
<!--- --------------------------------------------------------------------- --->
  - Network Activity Graph is now optional (can be disabled on the maintenance page)
  - Rolling back the Archived Devices display in the Network Activity Graph (there is a logical error)
  - minor UI tweaks
  - Fixed a bug where tabs on the maintenance page do not work when the English language is active.
  - Add pialert-cli to Help/FAQ
  - Correct wrong description in pialert-cli
  - Pushsafer: The notification type (Events, Devices Down) was added to the notification title
  - Minor fixes in the interface (css, wordings)
  - Fix modal-headline in dark mode
  - Updating the information on the login screen for setting the password (due to the switch to pialert-cli)
  - Increase pialert-cli version to 0.3
  - Add "pialert-cli update_db" function to simplify and unify the migration process
  - Update FAQ (Migration notes)
  - Remove pialert/install/pialert_patch_DB.sh
  - Darkmode
  - Multilanguage UI
  - Update AdminLTE

## Start Fork with v3.5.0
<!--- --------------------------------------------------------------------- --->

## Pi.Alert v3.02
<!--- --------------------------------------------------------------------- --->
 **PENDING UPDATE DOC**
  - Fixed: UNIQUE constraint failed with Local MAC #114


## Pi.Alert v3.01
<!--- --------------------------------------------------------------------- --->
 **PENDING UPDATE DOC**
  - Fixed: Problem with local MAC & IP (raspberry) #106
 

## Pi.Alert v3.00
<!--- --------------------------------------------------------------------- --->
 **PENDING UPDATE DOC**
  - `arp-scan` config options: interface, several subnets. #101 #15
  - Next/previos button while editing devices #66 #37
  - Internet presence/sessions monitoring #63
  - Logical delete / archive / hide Device #93
  - Flag to mark device with random MAC's #87
  - New Device Types predefined in combobox #92
  - Ask before leave the page with unsaved changes #104
  - Option to don't mark devices as new during installation #94
  - Uninstall script #62
  - Fixed: Error updating name of devices w/o IP #97
  - Fixed: Deleted devices reappear #84
  - Fixed: Device running Pi.Alert must be marked as "on-line" #76
  - Fixed: Incorrect calculation of presence hours #102
  - Fixed: Problem redirect to homepage clicking in logo #103


## Pi.Alert v2.70
<!--- --------------------------------------------------------------------- --->
  - Added Client names resolution #43
  - Added Check to mark devices as "known" #16
  - Remember "Show XXX entries" dropdown value #16 #26
  - Remember "sorting" in devices #16
  - Remember "Device panel " in device detail #16
  - Added "All" option to "Show x Entries" option #16
  - Added optional Location field (Door, Basement, etc.) to devices #16
  - "Device updated successfully" message now is not modal #16
  - Now is possible to delete Devices #16
  - Added Device Type Singleboard Computer (SBC) #16
  - Allowed to use " in device name #42


## Pi.Alert v2.60
<!--- --------------------------------------------------------------------- --->
  - `pialert.conf` moved from `back` to `config` folder
  - `pialert.conf` splitted in two files: `pialert.conf` and `version.conf`
  - Added compatibility with Python 3 (default version installed with Ubuntu)
  - Added compatibility in the Installation guide with Ubuntu server
  - Eliminated some unnecessary packages from the installation


### License
  GPL 3.0
  [Read more here](../LICENSE.txt)

