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

By clicking on the blue URL, you will get to the details page of the service, where you can change the tag, the assigned device and the desired alert. You also have the option to delete the service or view more details. Information about the colors used and a list of the different HTTP status codes can be found on the Help/FAQ page in Pi.Alert.

In addition, this page also shows you the current information from the SSL certificate of the web server. If these change, this is also recognized as an event and a notification can be sent. The notification does not contain the changed data, but a code that indicates the changed field. The code is composed as follows:

| Code | Field |
| ---- | ------|
| 8 | Subject |
| 4 | Issuer |
| 2 | Valid from |
| 1 | Valid to |

A code "12" therefore means changes in the fields "Subject" and "Issuer", a code "11" means changes in the fields "Subject", "Valid from" and "Valid to". After the change is detected, the fields are updated accordingly and the code "0" appears again in the following scan. There is no notification for the change back to code "0".

[Back](https://github.com/leiweibau/Pi.Alert#front)


[enable_wsm]:         ./img/wsm_enable.png             "Enable WSM"
[wsm_addURL_1]:       ./img/wsm_addURL_1.png   		   "WSM add URL"
[wsm_addURL_2]:       ./img/wsm_addURL_2.png   		   "WSM add URL Details"
[wsm_newURL_1]:       ./img/wsm_newURL_1.png   		   "WSM new URL"
[wsm_newURL_2]:       ./img/wsm_newURL_2.png   		   "WSM new URL Scans"
[wsm_statuscode]:     ./img/wsm_statuscode.png   	   "WSM statuscode"
