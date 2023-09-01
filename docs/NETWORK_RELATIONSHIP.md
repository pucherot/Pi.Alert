## Network Relationship

* [Requirements](#requirements)
* [First start](#first-start)
* [Editing mode](#editing-mode)
  * [Manage Devices](#manage-devices)
  * [Manage Non-Scannable Devices](#manage-non-scannable-devices)
* [Manage Devices](#manage-devices-1)
  * [Create a device Internet](#create-a-device-internet)
  * [Create a device Router](#create-a-device-router)
  * [Create a device Switch](#create-a-device-switch)
  * [Create a device WLAN](#create-a-device-wlan)
  * [Delete a device](#delete-a-device)
* [Assign Devices](#assign-devices)
* [Manage Non-Scannable Devices](#manage-non-scannable-devices-1)
  * [Create a device](#create-a-device)
  * [Edit a device](#edit-a-device)
  * [Delete a device](#delete-a-device-1)

### Requirements

In this description, I assume that devices have already been detected in the Device list and devices have already been assigned types such as switch or router.

### First start

When you open the page for the 1st time it is almost empty. Under a narrow empty box there 
is another one, which has the title "Unassigned devices" and can be expanded. This box lists all devices that are not yet assigned to a network component.

![Manage unassigned Devices][Manage_unassigned_Devices]

On the top left, next to the page heading "Network Overview", there is a green "+" button with which you can switch to edit mode. Editing the components on this page 
does not affect already detected devices in the devices list. **We now switch to the edit mode.**

### Editing mode

The edit mode also consists of 2 large areas, which in turn are divided into 3 sub-areas each. For differentiation, these sub-areas are highlighted in different colors.

#### Manage Devices

![Manage Net Devices][Manage_Net_Devices]

#### Manage Non-Scannable Devices

![Manage Non Scanable Net Devices][Manage_NonScanable_Net_Devices]

### Manage Devices

This section is used for creating transparent/IP-less (unmanaged) devices such as hubs or switches, as well as active devices like routers, managed switches, or access points that influence the network structure. Devices already detected by Pi.Alert must be recreated here for display. Editing or deleting devices already detected by Pi.Alert is independent of the "Devices" list and only has significance for the "Network" page.

#### <ins>Create a device Internet:</ins>

For this, it is necessary to have a device with the MAC address "Internet" in the devices list.
```
Device Name: Internet
Device Type: Internet
```

![Manage Net Devices - add 1][Manage_Net_Devices_add_1]

Now a new box appears with a tab named "Internet

![Management Tab 1][Management_tab_1]

This new created network component can now be selected in the details view of a device like a router (in this example the router is named "Virtual Box Interface 1"). 
After the selection, the name no longer appears in the input field, but only the ID of the device. Since no port number was entered for the "Internet" device, it is 
not necessary to enter one here. As "Connection Type" I enter "Ethernet". The "Connection Type" is only additional information, but it is not relevant for the display.

![Management Device Add Internet][Management_Device_Add_Internet]

If you now open the "Network" page again, you will see the router you just edited in the "Internet" tab. No port numbers are displayed for the network components 
Internet, WLAN and Powerline.

![Management Tab 2][Management_tab_2]

#### <ins>Create a device Router:</ins>

To create a router on the network page, I select a device of type "Router" from the list, specify the type again, followed by the number of ports.

![Manage Net Devices - add 2][Manage_Net_Devices_add_2]

There is now a 2nd tab to be seen with the name of the router. This tab already contains a table with the numbering of the ports. However, the already configured 
device "Internet" does not appear in the table yet. For this, you can now go to the details view of the device Internet and configure the router there.

![Management Tab 3][Management_tab_3]

To assign the "Internet" device to the router port, proceed as described above. Go to the detail view of the "Internet" device and select the router as "Uplink Target" 
and enter the corresponding port.

| ![Management Device Add Router 1][Management_Device_Add_Router_1] | ![Management Tab 4][Management_tab_4] |
| ------------------------------------------------------------------|---------------------------------------|

#### <ins>Create a device Switch:</ins>

If now a switch is connected to the router and this is to be mapped, it is necessary to create another device of the type "Switch". If the switch is a manageable 
switch, it can be selected from the drop-down list, just like the router before. If this is not the case, you can also enter any name in the "Device Name" field.

| ![Manage Net Devices - add 3][Manage_Net_Devices_add_3] | ![Management Tab 5][Management_tab_5] |
| --------------------------------------------------------|---------------------------------------|

If you now open the detail view of the switch, you can now set the router as "Uplink Target" with the corresponding port (e.g. port number 4 on the router).

| ![Management Device Add Router 2][Management_Device_Add_Router_2] | ![Management Tab 6][Management_tab_6] |
| ------------------------------------------------------------------|---------------------------------------|

Since only one network connection can be configured per device on the details view, there is now the problem that the switch is visible at the router tab, but 
the router is not visible in the switch tab.
To overcome this problem, it is possible to make a "manual port configuration".

To do this, open the "Manage Devices" box and select the switch (the device that should display the other device) in the "Update Device" section. At this point, 
it is important that you enter the correct type in "New Device Type" and the correct port count in "New Port Count". If you don't want to change the name, you can 
leave the field "New Device Name" empty.
The lowest field in the edit is the "manual port configuration". If you want to assign only one device, you can simply select it from the drop-down list. In this 
example I configure the router "Virtual Box Interface 1" on port 1 of the switch "Virtual Box Interface 2". After selecting the device name, the MAC address of 
the device appears instead of the name. The reason for this is that the MAC address is unique in contrast to the name. 
When you select a device from the drop-down list, a comma is immediately appended to the MAC address. After this comma, you enter the port that the device 
occupies on this switch. In this example, I use port "1".


| ![Management Device Update Switch 1][Management_Device_Update_Switch_1] | ![Management Device Update Switch 2][Management_Device_Update_Switch_2] |
| ------------------------------------------------------------------------|-------------------------------------------------------------------------|

After saving, you can now see the router in the switch's tab. If you want to edit the switch again, you also have to enter the "manual port configuration" again. 
You can also assign multiple devices in manual. The syntax for this is then "MAC address 1,port;MAC address 2,port;MAC address 3,port;". However, only the 1st 
Mac address can be selected from the drop-down list. Further MAC addresses must be entered manually.

***Attention:***<br>
***Switches (e. g. unmanaged switches) or Routers which do not appear in the devices list cannot be assigned to any other network component, not even via the "manual port configuration".***


#### <ins>Create a device WLAN:</ins>

To create a WLAN, it is not necessary to select an already detected device on the drop-down menu. The idea here is to use the SSID, for example. As "Device Type" "WLAN" 
is selected, while the "Port - Count" is left empty. After adding the device, a new tab appears with the assigned name.

| ![Manage Net Devices - add 4][Manage_Net_Devices_add_4] | ![Management Tab 7][Management_tab_7] |
| --------------------------------------------------------|---------------------------------------|

Now it is possible to assign a device from the devices list to this WLAN. It is not necessary to specify a port for the WLAN type. At the moment it is not possible to 
assign a device to multiple WLANs. Possibly this will change in the future.

| ![Management Device Add Host][Management_Device_Add_Host] | ![Management Tab 8][Management_tab_8] |
| ----------------------------------------------------------|---------------------------------------|

#### <ins>Delete a device:</ins>

To delete a network component from this display, select the corresponding device in the "Manage Devices" box in the "Delete Device" area and continue with "Delete". 
Only devices that were created via "Manage Devices" can be deleted. If already recognized devices were used as a template, these already recognized devices remain 
unaffected. Only the entry on the "Network" page is deleted. The assignments that were made for the individual devices remain in the database and can be updated on 
occasion.

### Assign Devices

Simple devices such as PCs, consoles, TVs, etc. can be easily assigned to devices via their details page. It should be noted that there is no check whether a port, 
e.g. on a switch, has already been configured on another device or not. This means that a TV and a PC can be configured on the same port of a switch. So you have 
to check this yourself.

However, this is not an error, but quite intentional. The background is that there are servers or PCs that run virtual machines, so there are several devices on 
one port. Another special feature is that you can specify multiple ports, separated by a comma, in the detail view of each device. The reason for this is that 
there are, for example, servers that are configured with link aggregation, which consequently occupy multiple ports on a switch.

### Manage Non-Scannable Devices

At this location, devices that occupy ports or connections on switches or routers but do not have active network functionality themselves, can be created. 
Devices that cannot be detected but should be displayed for overview, e.g. lighting with PoE, can also be considered here. The devices created here only appear on the "Network" page.

#### <ins>Create a device</ins>

To create such a device, enter a descriptive name in the green section (this can also contain spaces), select one of the already created network components in the next line where this 
device should be displayed and specify the port that will be occupied by this device.

![Manage Net Devices - add 5][Manage_Net_Devices_add_5]

In the example shown, I have assigned the "Lighting Basement (PoE)" to the "VirtualBox Interface 2" device on port 5. If you now look at the tab of "VirtualBox Interface 2", you can now see the new device. You will notice that a yellow label with the name "UM" is used here. However, this is only the case when it is assigned to a router or switch. 
For all other assignments, the devices are provided with the label "Offline".

![Management Tab 9][Management_tab_9]

#### <ins>Edit a device</ins>

Clicking on an "Unmanaged Device" opens the "Settings - Network Overview" page. In the "Manage Non-Scannable Devices" section, select the device to be edited in the yellow area and enter the desired changes.

| ![Manage Net UM Devices - update][Manage_Net_UM_Devices_update] | ![Management Tab 10][Management_tab_10] |
| --------------------------------------------------------|---------------------------------------|

#### <ins>Delete a device</ins>

To remove a manually created device from the network overview, simply select it in the "Manage Non-Scannable Devices" section, in the red area and click the "Delete Device" button. 

[Back](https://github.com/leiweibau/Pi.Alert#front)

[Manage_Net_Devices]:                 ./img/netrel_management_1.png                "Manage Net Devices"
[Manage_NonScanable_Net_Devices]:     ./img/netrel_management_2.png                "Manage Non Scanable Net Devices"
[Manage_Net_Devices_add_1]:           ./img/netrel_management_add_1.jpg            "Manage Net Devices - add 1"
[Manage_Net_Devices_add_2]:           ./img/netrel_management_add_2.jpg            "Manage Net Devices - add 2"
[Manage_Net_Devices_add_3]:           ./img/netrel_management_add_3.jpg            "Manage Net Devices - add 3"
[Manage_Net_Devices_add_4]:           ./img/netrel_management_add_4.jpg            "Manage Net Devices - add 4"
[Manage_Net_Devices_add_5]:           ./img/netrel_management_add_5.png            "Manage Net Devices - add 5"

[Management_tab_1]:      		      ./img/netrel_management_tab_1.jpg            "Management Tab 1"
[Management_tab_2]:      		      ./img/netrel_management_tab_2.jpg            "Management Tab 2"
[Management_tab_3]:      		      ./img/netrel_management_tab_3.jpg            "Management Tab 3"
[Management_tab_4]:      		      ./img/netrel_management_tab_4.jpg            "Management Tab 4"
[Management_tab_5]:      		      ./img/netrel_management_tab_5.jpg            "Management Tab 5"
[Management_tab_6]:      		      ./img/netrel_management_tab_6.jpg            "Management Tab 6"
[Management_tab_7]:      		      ./img/netrel_management_tab_7.jpg            "Management Tab 7"
[Management_tab_8]:      		      ./img/netrel_management_tab_8.jpg            "Management Tab 8"

[Management_tab_9]:      		      ./img/netrel_management_tab_9.png            "Management Tab 9"
[Management_tab_10]:      		      ./img/netrel_management_tab_10.png            "Management Tab 10"

[Management_Device_Add_Internet]:     ./img/netrel_management_assign_internet.jpg  "Management Device Add Internet"
[Management_Device_Add_Router_1]:     ./img/netrel_management_assign_router_1.jpg  "Management Device Add Router 1"
[Management_Device_Add_Router_2]:     ./img/netrel_management_assign_router_2.jpg  "Management Device Add Router 2"
[Management_Device_Add_Host]:         ./img/netrel_management_assign_host.jpg      "Management Device Add Host"

[Management_Device_Update_Switch_1]:  ./img/netrel_management_update_switch_1.jpg  "Management Device Update Switch 1"
[Management_Device_Update_Switch_2]:  ./img/netrel_management_update_switch_2.jpg  "Management Device Update Switch 2"

[Manage_Net_Devices_update]:          ./img/netrel_management_update.jpg           "Manage Net Devices - update"

[Manage_Net_UM_Devices_update]:          ./img/netrel_UM_management_update.png           "Manage Net UM Devices - update"

[Manage_Net_Devices_delete]:          ./img/netrel_management_delete.jpg           "Manage Net Devices - delete"
[Manage_unassigned_Devices]:          ./img/netrel_unassigned.jpg                  "Manage unassigned Devices"
