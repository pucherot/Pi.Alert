# Pi.Alert Version History
<!--- --------------------------------------------------------------------- --->

  | Version | Description                                                     |
  | ------- | --------------------------------------------------------------- |
  |  v3.7.x | Bug fixing, minor improvements and features                     |
  |  v3.7   | New features & Usability improvements                           |
  |  v3.5   | Update AdminLTE, Major set of New features & Enhancements       |
  |  v3.00  | Major set of New features & Enhancements                        |
  |  v2.70  | New features & Usability improvements in the web prontal        |
  |  v2.61  | Bug fixing                                                      |
  |  v2.60  | Improved the compability of installation process (Ubuntu)       |
  |  v2.56  | Bug fixing                                                      |
  |  v2.55  | Bug fixing                                                      |
  |  v2.52  | Bug fixing                                                      |
  |  v2.51  | Bug fixing                                                      |
  |  v2.50  | First public release                                            |


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
  - If the user running Pi.Alert is set up in sudoers, then the database permission can be set to the correct values before the scan (addresses "Read Only Database" errors). To do this, uncomment line 67 in ~/pialert/back/pialert.py. This is disabled by default.

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

### Contact
  pi.alert.application@gmail.com
