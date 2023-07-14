## Bulk editor

To access the bulk editor, you need to navigate to the devices.php page and click on the button with the pencil next to the table heading

![Open bulkeditor][open_bulkeditor]

A new page will now open showing all detected hosts in alphabetical order with a checkbox. The respective checkbox can be set via the host name as well as via the checkbox itself. Only selected hosts will apply the changes made.

![Bulkeditor list][bulkeditor_list]

The background color of the checkbox informs whether it is an archived or new device (gray or yellow). The color of the frame indicates the online or offline (green or gray) status. Whether notifications are made on all event, on down or on both is indicated by the font color (blue, red, gradient).

To be able to edit a value, the input field must be activated via the respective checkbox (red circle) at the beginning of the line. If the checkbox is deactivated again, the input field is cleared.

![Bulkeditor enable field][Enable_field]

With the Save button, the changes are saved in the database without further questions. A summary of the changed devices with the changed values is displayed. With the "DELETE DEVICES" button all selected devices are deleted from the database.

[Back](https://github.com/leiweibau/Pi.Alert#front)

[open_bulkeditor]:    ./img/bulkeditor_open.png           "Open bulkeditor"
[bulkeditor_list]:    ./img/bulkeditor_list.png           "Bulkeditor list"
[Enable_field]:       ./img/bulkeditor_enable_field.png   "Bulkeditor enable field"