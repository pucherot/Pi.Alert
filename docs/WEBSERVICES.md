## Web service monitoring

To use the web service monitoring it is necessary that the corresponding entry in the configuration file is set to "True".

![Enable WSM][enable_wsm]

Web service monitoring is used to check web pages or web services for availability every 10 min. For this purpose, an HTTP request is sent to the stored URL and the server response is stored in the database in the form of the 
HTTP status code. If the web service / website does not respond within 10 seconds, a response time of "99999999" is stored in the database. This value stands for "No Response".

To add a web service, use the green button next to the heading.

![WSM add URL][wsm_addURL_1]

In the dialog that opens, you can enter the web service/web page including the protocol (http:// or https://).

![WSM add URL Details][wsm_addURL_2]

| Input&nbsp;field | Description |
|-------------|-------------|
| URL         | The complete URL incl. http::// or https:// as well as a possibly configured port number. The standard port numbers 80 and 443 do not have to be specified separately. |
| Tag         | The tag serves as a short description of the URL in order to better assign the service (e.g. media server, homepage, etc.). |
| Device      | Here you can select a device from the device list, which provides the web service / web page. All services assigned to a device are displayed in a separate box. |
| All Events  | Allert changes IP / response or no response / HTTP status code |
| Down Events | Allert response or no response |

URLs that are saved without a device are displayed by tag, sorted alphabetically, in a separate box named "General".

When the service is saved, a check is already performed, which is why a status code can be seen even before the actual scan. A gray segment in the bar indicates that there are no scan results yet.

![WSM new URL][wsm_newURL_1]

Hovering the mouse over one of the segments in the bar, you will get information about the corresponding scan.

![WSM new URL Scans][wsm_newURL_2]

If you hover over the box with the status code, you will get a short explanation about the current status code.

![WSM statuscode][wsm_statuscode]

By clicking on the blue URL, you will get to the details page of the service, where you can change the tag, the assigned device and the desired alert. You also have the option to delete the service or view more details.

[Back](https://github.com/leiweibau/Pi.Alert#front)


[enable_wsm]:         ./img/wsm_enable.png             "Enable WSM"
[wsm_addURL_1]:       ./img/wsm_addURL_1.png   		   "WSM add URL"
[wsm_addURL_2]:       ./img/wsm_addURL_2.png   		   "WSM add URL Details"
[wsm_newURL_1]:       ./img/wsm_newURL_1.png   		   "WSM new URL"
[wsm_newURL_2]:       ./img/wsm_newURL_2.png   		   "WSM new URL Scans"
[wsm_statuscode]:     ./img/wsm_statuscode.png   	   "WSM statuscode"