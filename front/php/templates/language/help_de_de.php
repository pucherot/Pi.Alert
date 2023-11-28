<?php
unset($help_lang);

//////////////////////////////////////////////////////////////////
// Help Page
//////////////////////////////////////////////////////////////////

$help_lang['Title'] = 'Hilfe / FAQ';
$help_lang['Cat_General'] = 'Allgemein';
$help_lang['Cat_Detail'] = 'Detailansicht';
$help_lang['Cat_General_100_head'] = 'Die Uhr oben rechts und die Zeiten der Events/Anwesenheit stimmen nicht (Zeitverschiebung).';
$help_lang['Cat_General_100_text_a'] = 'Aktuell konfigurierte Zeitzone:';
$help_lang['Cat_General_100_text_b'] = '<br>Sollte dies nicht die Zeitzone sein, in der du dich aufhältst, solltest du die Zeitzone in der PHP Konfigurationsdatei anpassen. Diese findest du in dem Verzeichnis:';
$help_lang['Cat_General_100_text_c'] = 'Suche in dieser Datei nach dem Eintrag "<span class="text-maroon help_faq_code">date.timezone</span>", entferne ggf. das führende "<span class="text-maroon help_faq_code">;</span>" und trage die gewünschte Zeitzone ein. Eine Liste mit den unterstützten Zeitzonen findest du hier (<a href="https://www.php.net/manual/de/timezones.php" target="blank">Link</a>).';
$help_lang['Cat_General_101_head'] = 'Mein Netzwerk scheint langsamer zu werden, Streaming "ruckelt".';
$help_lang['Cat_General_101_text'] = 'Es kann durchaus sein, dass leistungsschwache Geräte mit der Art und Weise, wie Pi.Alert neue Geräte im Netzwerk erkennt, an ihre Leistungsgrenzen stoßen. Dies verstärkt sich noch einmal, wenn diese Geräte per WLAN mit dem Netzwerk kommunizieren. Lösungen wären hier, wenn möglich ein Wechsel auf eine Kabelverbindung oder, falls das Geräte nur einen begrenzten Zeitraum genutzt werden soll, den arp-Scan auf der Wartungsseite zu pausieren.';
$help_lang['Cat_General_102_head'] = 'Ich bekomme die Meldung, dass die Datenbank schreibgeschützt (read only) ist.';
$help_lang['Cat_General_102_text'] = 'Möglicherweise werden durch das Backend gerade Änderungen in die Datenbank geschrieben. Probiere es nach einer kurzen Wartezeit einfach noch einmal. Sollte sich das Verhalten nicht ändern, halte dich an die folgenden Hinweise. <br><br>
									 Prüfe im Pi.Alert Verzeichnis, ob der Ordner der Datenbank (db) die richtigen Rechte zugewiesen bekommen hat:<br>
      								 <span class="text-maroon help_faq_code">drwxrwxr-x  2 (dein Username) www-data</span><br>
      								 Sollte die Berechtigung nicht stimmen, kannst du sie mit folgenden Befehlen im Terminal oder der Konsole wieder setzen:<br>
      								 <div class="help_faq_code" style="padding-left: 10px; margin-bottom: 10px;">
      								 sudo chgrp -R www-data ~/pialert/db<br>
      								 sudo chown [Username]:www-data ~/pialert/db/pialert.db<br>
        							 chmod -R 775 ~/pialert/db
      								 </div>
      								 Ein ander Möglichkeit wäre, die notwendigen Rechte mit Hilfe von <span class="text-maroon help_faq_code">pialert-cli</span> im Verzeichnis <span class="text-maroon help_faq_code">~/pialert/back</span> neu zu setze. Hier stehen dir mehrere Optionen zur Verfügung.<br><br>
      								 <span class="text-maroon help_faq_code">./pialert-cli set_permissions</span><br>
      								 Hier werden nur die Gruppenrechte neu gesetzt. Der Eigentümer der Datei bleibt unangetastet.<br><br>
      								 <span class="text-maroon help_faq_code">./pialert-cli set_permissions --lxc</span><br>
      								 Dieses zusätzliche Option wurde für die Nutzung in einem LXC Container eingeführt. Sie ändert die Gruppe, gemäß der Grundfunktion und setzt als Eigentümer den User "root". Außerhalb einer LXC Umgebung ist diese Option nicht relevant.<br><br>
      								 <span class="text-maroon help_faq_code">./pialert-cli set_permissions --homedir</span><br>
      								 Dies sollte die bevorzugte Option sein. Hier wird der Username anhand des übergeordnenten Home Verzeichnisses der Pi.Alert-Installation ermittelt. Dieser Username wird als Eigentümer der Dateien gesetzt. Die Gruppe, wird gemäß der Grundfunktion gesetzt.';
$help_lang['Cat_General_103_head'] = 'Die Login-Seite erscheint nicht, auch nicht nach der Passwortänderung.';
$help_lang['Cat_General_103_text'] = 'Neben dem Passwort, muss in der Konfigurationsdatei <span class="text-maroon help_faq_code">~/pialert/config/pialert.conf</span>
      								 auch der Parameter <span class="text-maroon help_faq_code">PIALERT_WEB_PROTECTION</span> auf <span class="text-maroon help_faq_code">True</span> gesetzt sein.';
$help_lang['Cat_General_104_head'] = 'Hinweise zur Migration von "pucherot/Pi.Alert" zu diesem Fork.';
$help_lang['Cat_General_104_text'] = 'Die Datenbank in diesem Fork wurde um einige Felder erweitert. Um die Datenbank vom originalen Pi.Alert <b>(pucherot)</b> zu übernehmen, steht mit dem Script <span class="text-maroon help_faq_code">pialert-cli</span> im Verzeichnis
									 <span class="text-maroon help_faq_code">~/pialert/back</span> eine Möglichkeit zum Upgrade zur Verfügung. Der Befehl lautet dann <span class="text-maroon help_faq_code">./pialert-cli update_db</span>';
$help_lang['Cat_General_105_head'] = 'Erklärungen zu "pialert-cli"';
$help_lang['Cat_General_105_text'] = 'Das Kommandozeilen-Tool <span class="text-maroon help_faq_code">pialert-cli</span> befindet sich im Verzeichnis <span class="text-maroon help_faq_code">~/pialert/back</span> und bietet die Möglichkeit, Einstellungen an Pi.Alert ohne Webseite, oder manuelle Veränderungen an der
                                     Konfigurationsdatei vornehmen zu können. Mit dem Kommando <span class="text-maroon help_faq_code">./pialert-cli help</span> kann eine Liste mit den unterstützen Funktionen aufgerufen werden.
									 <table class="help_table_gen">
									    <tr><td class="help_table_gen_a">set_login</td>
									        <td class="help_table_gen_b">- Setzt den Parameter PIALERT_WEB_PROTECTION in der Konfigurationsdatei auf TRUE<br>
									            - Wenn der Parameter nicht vorhanden ist, wird er erstellt. Zusätzlich wird dann das Standard-Passwort "123456" festgelegt</td></tr>
									    <tr><td class="help_table_gen_a">unset_login</td>
									        <td class="help_table_gen_b">- Setzt den Parameter PIALERT_WEB_PROTECTION in der Konfigurationsdatei auf FALSE<br>
									            - Wenn der Parameter nicht vorhanden ist, wird er erstellt. Zusätzlich wird dann das Standard-Passwort "123456" festgelegt</td></tr>
									    <tr><td class="help_table_gen_a">set_password &lt;password&gt;</td>
									        <td class="help_table_gen_b">- Legt das neue Passwort als Hash-Wert fest.<br>
									            - Wenn der Parameter PIALERT_WEB_PROTECTION noch nicht vorhanden ist, wird er erstellt und auf "TRUE" gesetzt (Anmeldung aktiviert)</td></tr>
									    <tr><td class="help_table_gen_a">set_autopassword</td>
									        <td class="help_table_gen_b">- Legt ein neues Zufallspasswort als Hashwert fest und zeigt es im Klartext in der der Konsole an.<br>
									            - Wenn der Parameter PIALERT_WEB_PROTECTION noch nicht vorhanden ist, wird er erstellt und auf "TRUE" gesetzt (Anmeldung aktiviert)</td></tr>
									    <tr><td class="help_table_gen_a">disable_scan &lt;MIN&gt;</td>
									        <td class="help_table_gen_b">- Stoppt alle aktiven Scans.<br>
									            - Verhindert den Start neuer Scans.<br>- Sie können eine Zeitspanne in Minuten festlegen. Wenn keine Zeitspanne festgelegt wird, startet Pi.Alert mit dem nächsten Scan nach 10 Minuten neu</td></tr>
									    <tr><td class="help_table_gen_a">enable_scan</td>
									        <td class="help_table_gen_b">- Aktiviert den Start neuer Scans wieder</td></tr>
									    <tr><td class="help_table_gen_a">disable_mainscan</td>
									        <td class="help_table_gen_b">- Deaktiviert die Haupt Scan Methode für Pi.Alert (Arp-scan)</td></tr>
									    <tr><td class="help_table_gen_a">enable_mainscan</td>
									        <td class="help_table_gen_b">- Aktiviert die Haupt Scan Methode für Pi.Alert (Arp-scan)</td></tr>
									    <tr><td class="help_table_gen_a">disable_service_mon</td>
									        <td class="help_table_gen_b">- Deaktiviert die Web Service Überwachung</td></tr>
									    <tr><td class="help_table_gen_a">enable_service_mon</td>
									        <td class="help_table_gen_b">- Aktiviert die Web Service Überwachung</td></tr>
									    <tr><td class="help_table_gen_a">disable_icmp_mon</td>
									        <td class="help_table_gen_b">- Deaktiviert das ICMP Monitoring (ping)</td></tr>
									    <tr><td class="help_table_gen_a">enable_icmp_mon</td>
									        <td class="help_table_gen_b">- Aktiviert das ICMP Monitoring (ping)</td></tr>
									    <tr><td class="help_table_gen_a">update_db</td>
									        <td class="help_table_gen_b">- Erstellt die benötigten Datenbankfelder, welche für diesen Fork benötigt werden</td></tr>
									    <tr><td class="help_table_gen_a">set_apikey</td>
									        <td class="help_table_gen_b">- Mit dem API-Schlüssel ist es möglich, Abfragen an die Datenbank zu stellen, ohne die Webseite zu benutzen. Wenn bereits ein API-Schlüssel existiert, wird er ersetzt</td></tr>
									    <tr><td class="help_table_gen_a">set_permissions</td>
									        <td class="help_table_gen_b">- Repariert die Dateiberechtigung der Datenbank für die Gruppe. Wenn die Berechtigungen für den User ebenfalls neu gesetzt werden sollen, ist eine zusäzliche Option notwendig:<br>
									        							    <span class="text-maroon" style="display:inline-block;width:130px;">--lxc</span>        setzt "root" as Usernamen<br>
																			<span class="text-maroon" style="display:inline-block;width:130px;">--custom</span>     setzt einen individuellen Usernamen<br>
																			<span class="text-maroon" style="display:inline-block;width:130px;">--homedir</span>    übernimmt den Username vom Home-Verzeichnis</td></tr>
									    <tr><td class="help_table_gen_a">reporting_test</td>
									        <td class="help_table_gen_b">- Testet alle aktiven Benachrichtigungsdienste</td></tr>
									    <tr><td class="help_table_gen_a">set_sudoers</td>
									        <td class="help_table_gen_b">- Erstellt sudoers Dateien für den User www-data und den User, unter dem Pi.Alert installiert ist</td></tr>
									    <tr><td class="help_table_gen_a">unset_sudoers</td>
									        <td class="help_table_gen_b">- Löscht die sudoers Dateien für den User www-data und den User, unter dem Pi.Alert installiert ist</td></tr>
									</table>';
$help_lang['Cat_General_106_head'] = 'Wie kannn ich eine Integritätsprüfungdie der Datenbnak durchführen?';
$help_lang['Cat_General_106_text'] = 'Wenn du die Datenbank, welche gerade in Benutzung ist, überprüfen willst, stoppe Pi.Alert für vielleicht eine 1h, damit nicht zwischendurch schreibende Zugriffe auf die Datenbank erfolgen.
									 Auch die Weboberfläche sollte während der Prüfung nicht anderweitig geöffnet sein um auch hier keine Schreibvorgänge zu ermöglichen. Öffnet nun in der Konsole das Verzeichen <span class="text-maroon help_faq_code">~/pialert/db</span>
									 und lasse dir mit dem Befehl <span class="text-maroon help_faq_code">ls</span> den Inhalt des Verzeichnisses anzeigen. Wenn die Dateien <span class="text-maroon help_faq_code">pialert.db-shm</span> und <span class="text-maroon help_faq_code">pialert.db-wal</span>
									 in der Liste (mit dem gleichen Zeitstempel wie die Datei "pialert.db") auftauchen, dann sind noch Datenbanktransaktionen offen. In diesem Fall, einfach noch einen Moment warten und zur Kontrolle einfach noch einmal den Befehl <span class="text-maroon help_faq_code">ls</span> ausführen.
									 <br><br>
									 Wenn diese Dateien verschunden sind, kann die Prüfung durchgeführt werden. Dazu werden folgende Befehle ausgeführt:<br>
									 <div class="help_faq_code" style="padding-left: 10px; margin-bottom: 10px;">
									 	sqlite3 pialert.db "PRAGMA integrity_check"<br>
										sqlite3 pialert.db "PRAGMA foreign_key_check"
									 </div><br>
									 In beiden Fällen sollten keine Fehler gemeldet werden. Nach der Prüfung kannst du Pi.Alert wieder starten.';
$help_lang['Cat_General_107_head'] = 'Erklärungen zur Datei "pialert.conf"';
$help_lang['Cat_General_107_text'] = 'Die Datei <span class="text-maroon help_faq_code">pialert.conf</span> befindet sich im Verzeichnis <span class="text-maroon help_faq_code">~/pialert/config</span>.
									 In dieser Konfigurationsdatei können viele Funktionen von Pi.Alert ensprechend der persönlichen Wünsche eingestellt werden.
									 Da die Möglichkeiten vielfältig sind, möchte ich eine kurze Erklärung zu den einzelnen Punkten geben.
									 <table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">General Settings</td></tr>
									    <tr><td class="help_table_gen_a">PIALERT_PATH</td>
									        <td class="help_table_gen_b">Diese Variable wird während der Installation gesetzt und sollte nicht mehr verändert werden.</td></tr>
									    <tr><td class="help_table_gen_a">DB_PATH</td>
									        <td class="help_table_gen_b">Diese Variable wird während der Installation gesetzt und sollte nicht mehr verändert werden.</td></tr>
									    <tr><td class="help_table_gen_a">LOG_PATH</td>
									        <td class="help_table_gen_b">Diese Variable wird während der Installation gesetzt und sollte nicht mehr verändert werden.</td></tr>
									    <tr><td class="help_table_gen_a">PRINT_LOG</td>
									        <td class="help_table_gen_b">Wenn dieser Eintrag auf <span class="text-maroon help_faq_code">True</span> steht, werden dem Scan-Log zusätzlich Zeitstempel für die einzelnen Unterfunktionen hinzugefügt. Standardmäßig steht dieser Eintrag auf <span class="text-maroon help_faq_code">False</span></td></tr>
									    <tr><td class="help_table_gen_a">VENDORS_DB</td>
									        <td class="help_table_gen_b">Diese Variable wird während der Installation gesetzt und sollte nicht mehr verändert werden.</td></tr>
									    <tr><td class="help_table_gen_a">PIALERT_APIKEY</td>
									        <td class="help_table_gen_b">Mit dem API-Schlüssel ist es möglich, Abfragen an die Datenbank zu stellen, ohne die Webseite zu benutzen. Der API-Schlüssel ist eine zufällige Zeichenfolge, die über die Einstellungen oder über <span class="text-maroon help_faq_code">pialert-cli</span> gesetzt werden kann</td></tr>
									    <tr><td class="help_table_gen_a">PIALERT_WEB_PROTECTION</td>
									        <td class="help_table_gen_b">Aktiviert bzw. deaktiviert den Passwortschutz der Weboberfläche von Pi.Alert</td></tr>
									    <tr><td class="help_table_gen_a">PIALERT_WEB_PASSWORD</td>
									        <td class="help_table_gen_b">Dieses Feld beinhaltet das "gehashte" Passwort für die Weboberfläche. Das Passwort kann nicht im Klartext hier eingetragen, sondern muss mit <span class="text-maroon help_faq_code">pialert-cli</span> gesetzt werden</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Other Modules</td></tr>
									    <tr><td class="help_table_gen_a">SCAN_WEBSERVICES</td>
									        <td class="help_table_gen_b">Hier kann die Funktion zur Überwachung von Webservices an- (<span class="text-maroon help_faq_code">True</span>) bzw. ausgeschaltet (<span class="text-maroon help_faq_code">False</span>) werden</td></tr>
									    <tr><td class="help_table_gen_a">ICMPSCAN_ACTIVE</td>
									        <td class="help_table_gen_b">Aktivierung oder Deaktivierung des ICMP Monitoring</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Special Protocol Scanning</td></tr>
									    <tr><td class="help_table_gen_a">SCAN_ROGUE_DHCP</td>
									        <td class="help_table_gen_b">Aktiviert die Suche nach fremden, auch "rogue" genannt, DHCP Servern. Diese Funktion dient dazu, zu erkennen, ob sich ein fremder DHCP Server im Netzwerk befindet, welcher die Kontrolle über die IP Verwaltung übernehmen könnte.</td></tr>
									    <tr><td class="help_table_gen_a">DHCP_SERVER_ADDRESS</td>
									        <td class="help_table_gen_b">Hier wird die IP des bekannten DHCP Servers hinterlegt.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Mail-Account Settings</td></tr>
									    <tr><td class="help_table_gen_a">SMTP_SERVER</td>
									        <td class="help_table_gen_b">Addresse des eMail Servers. (z.B. smtp.gmail.com)</td></tr>
									    <tr><td class="help_table_gen_a">SMTP_PORT</td>
									        <td class="help_table_gen_b">Der Port des SMTP-Servers. Der Port kann je nach Serverkonfiguration variieren.</td></tr>
									    <tr><td class="help_table_gen_a">SMTP_USER</td>
									        <td class="help_table_gen_b">Benutzername</td></tr>
									    <tr><td class="help_table_gen_a">SMTP_PASS</td>
									        <td class="help_table_gen_b">Passwort</td></tr>
									    <tr><td class="help_table_gen_a">SMTP_SKIP_TLS</td>
									        <td class="help_table_gen_b">Wenn dieser Eintrag auf <span class="text-maroon help_faq_code">True</span> steht, ist die Transportverschlüsselung der eMail aktiviert. Wenn der Server dies nicht unterstützt, muss der Eintrag auf <span class="text-maroon help_faq_code">False</span> gesetzt werden.</td></tr>
									    <tr><td class="help_table_gen_a">SMTP_SKIP_LOGIN</td>
									        <td class="help_table_gen_b">Es gibt SMTP Server, die keine Anmeldung benötigen. In einem solchen Fall, muss dieser Wert auf <span class="text-maroon help_faq_code">True</span> gesetzt werden</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">WebGUI Reporting</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_WEBGUI</td>
									        <td class="help_table_gen_b">Aktiviert/Deaktiviert die Benachrichtigungen über Änderungen im Netzwerk in der Weboberfläche.</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_WEBGUI_WEBMON</td>
									        <td class="help_table_gen_b">Aktiviert/Deaktiviert die Benachrichtigungen über Änderungen der überwachten Webservices in der Weboberfläche.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr>
									        <td class="help_table_gen_section" colspan="2">Mail Reporting</td>
									    </tr>
									    <tr><td class="help_table_gen_a">REPORT_MAIL</td>
									        <td class="help_table_gen_b">Aktiviert/Deaktiviert die Benachrichtigungen über Änderungen im Netzwerk per eMail.</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_MAIL_WEBMON</td>
									        <td class="help_table_gen_b">Aktiviert/Deaktiviert die Benachrichtigungen über Änderungen der überwachten Webservices per eMail.</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_FROM</td>
									        <td class="help_table_gen_b">Name oder Bezeichnung des Absenders.</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_TO</td>
									        <td class="help_table_gen_b">eMail-Adresse, an die die Benachrichtigung gesendet werden soll.</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_DEVICE_URL</td>
									        <td class="help_table_gen_b">URL der Pi.Alert Installation, um einen klickbaren Link in der eMail, zum erkannten Gerät, erzeugen zu können.</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_DASHBOARD_URL</td>
									        <td class="help_table_gen_b">URL der Pi.Alert Installation, um einen klickbaren Link in der eMail erzeugen zu können.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr>
									        <td class="help_table_gen_section" colspan="2">Pushsafer</td>
									    </tr>
									    <tr><td class="help_table_gen_a">REPORT_PUSHSAFER</td>
									        <td class="help_table_gen_b">Aktiviert/Deaktiviert die Benachrichtigungen über Änderungen im Netzwerk via Pushsafer</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_PUSHSAFER_WEBMON</td>
									        <td class="help_table_gen_b">Aktiviert/Deaktiviert die Benachrichtigungen über Änderungen der überwachten Webservices via Pushsafer</td></tr>
									    <tr><td class="help_table_gen_a">PUSHSAFER_TOKEN</td>
									        <td class="help_table_gen_b">Hierbei handelt es sich um den privaten Schlüssel, den man auf der pushsafer-Seite einsehen kann.</td></tr>
									    <tr><td class="help_table_gen_a">PUSHSAFER_DEVICE</td>
									        <td class="help_table_gen_b">Die Device-ID, an die die Nachricht gesendet wird. &lsquo;<span class="text-maroon help_faq_code">a</span>&rsquo; bedeutet, die Nachricht wird an alle konfigurieren Geräte gesendet und verbraucht entsprechend viele API-Calls</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Pushover</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_PUSHOVER</td>
									        <td class="help_table_gen_b">Aktiviert/Deaktiviert die Benachrichtigungen über Änderungen im Netzwerk via Pushover</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_PUSHOVER_WEBMON</td>
									        <td class="help_table_gen_b">Aktiviert/Deaktiviert die Benachrichtigungen über Änderungen der überwachten Webservices via Pushover</td></tr>
									    <tr><td class="help_table_gen_a">PUSHOVER_TOKEN</td>
									        <td class="help_table_gen_b">Auch "APP TOKEN" oder "API TOKEN" genannt. Dieser Token kann auf der pushover-Seite abgefragt werden</td></tr>
									    <tr><td class="help_table_gen_a">PUSHOVER_USER</td>
									        <td class="help_table_gen_b">Oder auch "USER KEY". Dieser Key wird, gleich nach dem Login, auf der pushover-Startseite angezeigt.</td></tr>
									</table>
									<table class="help_table_gen">
			    						<tr><td class="help_table_gen_section" colspan="2">NTFY</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_NTFY</td>
									        <td class="help_table_gen_b">Aktiviert/Deaktiviert die Benachrichtigungen über Änderungen im Netzwerk über NTFY</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_NTFY_WEBMON</td>
									        <td class="help_table_gen_b">Aktiviert/Deaktiviert die Benachrichtigungen über Änderungen der überwachten Webservices über NTFY</td></tr>
									    <tr><td class="help_table_gen_a">NTFY_HOST</td>
									        <td class="help_table_gen_b">Der Hostname oder die IP-Adresse des NTFY-Servers.</td></tr>
									    <tr><td class="help_table_gen_a">NTFY_TOPIC</td>
									        <td class="help_table_gen_b">Der Betreff von Benachrichtigungen, die über NTFY gesendet werden.</td></tr>
									    <tr><td class="help_table_gen_a">NTFY_USER</td>
									        <td class="help_table_gen_b">Der Benutzername, der für die Authentifizierung beim NTFY-Server verwendet wird.</td></tr>
									    <tr><td class="help_table_gen_a">NTFY_PASSWORD</td>
									        <td class="help_table_gen_b">Das Passwort, das für die Authentifizierung beim NTFY-Server verwendet wird.</td></tr>
									    <tr><td class="help_table_gen_a">NTFY_PRIORITY</td>
									        <td class="help_table_gen_b">Priorisierung der über NTFY gesendeten Benachrichtigungen</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Shoutrrr</td></tr>
									    <tr><td class="help_table_gen_a">SHOUTRRR_BINARY</td>
									        <td class="help_table_gen_b">Hier muss konfiguriert werden, welches Binary von shoutrrr zum einsatz gebracht werden muss. Dies richtet sich danach, auf welcher Hardware Pi.Alert installiert wurde.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Telegram via Shoutrrr</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_TELEGRAM</td>
									        <td class="help_table_gen_b">Aktiviert/Deaktiviert die Benachrichtigungen über Änderungen im Netzwerk via Telegram</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_TELEGRAM_WEBMON</td>
									        <td class="help_table_gen_b">Aktiviert/Deaktiviert die Benachrichtigungen über Änderungen der überwachten Webservices via Telegram</td></tr>
									    <tr><td class="help_table_gen_a">TELEGRAM_BOT_TOKEN_URL</td>
									        <td class="help_table_gen_b">    </td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">DynDNS and IP</td></tr>
									    <tr><td class="help_table_gen_a">QUERY_MYIP_SERVER</td>
									        <td class="help_table_gen_b">Server URL, welche die aktuelle öffentliche IP ermittelt und zurückgibt.</td></tr>
									    <tr><td class="help_table_gen_a">DDNS_ACTIVE</td>
									        <td class="help_table_gen_b">Aktiviert/Deaktiviert den konfigurierten DDNS Service in Pi.Alert. DDNS, auch als DynDNS bezeichnet, ermöglicht es, einen Domainnamen mit einer, sich regelmäßig ändernden, IP-Adresse zu aktualisieren. Diesen Service bieten verschiedene Dienstleister an.</td></tr>
									    <tr><td class="help_table_gen_a">DDNS_DOMAIN</td>
									        <td class="help_table_gen_b">    </td></tr>
									    <tr><td class="help_table_gen_a">DDNS_USER</td>
									        <td class="help_table_gen_b">Benutzername</td></tr>
									    <tr><td class="help_table_gen_a">DDNS_PASSWORD</td>
									        <td class="help_table_gen_b">Passwort</td></tr>
									    <tr><td class="help_table_gen_a">DDNS_UPDATE_URL</td>
									        <td class="help_table_gen_b">    </td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Automatic Speedtest</td></tr>
									    <tr><td class="help_table_gen_a">SPEEDTEST_TASK_ACTIVE</td>
									        <td class="help_table_gen_b">Automatischen Speedtest aktivieren/deaktivieren</td></tr>
									    <tr><td class="help_table_gen_a">SPEEDTEST_TASK_HOUR</td>
									        <td class="help_table_gen_b">Volle Stunde(n) zu denen der Speedtest gestartet werden soll.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Arp-scan Options & Samples</td></tr>
									    <tr><td class="help_table_gen_a">MAC_IGNORE_LIST</td>
									        <td class="help_table_gen_b">
									            <span class="text-maroon help_faq_code">[&apos;MAC-Adresse 1&apos;, &apos;MAC-Adresse 2&apos;]</span><br>
									            Diese MAC-Adresse(n) (mit kleinen Buchstaben abspeichern) werden aus den Scan-Ergebnissen herausgefiltert.</td></tr>
									    <tr><td class="help_table_gen_a">SCAN_SUBNETS</td>
									        <td class="help_table_gen_b">
									        	&lsquo;<span class="text-maroon help_faq_code">--localnet</span>&rsquo;<br>
									        	Im Normalfall ist diese Option bereits die korrekte Einstellungen. Diese Einstellung wird gewählt, wenn Pi.Alert auf einem Gerät mit einer Netzwerkkarte installiert ist und keine weiteren Netzwerke konfiguriert sind.<br><br>
									        	&lsquo;<span class="text-maroon help_faq_code">--localnet --interface=eth0</span>&rsquo;<br>
									        	Diese Konfiguration wird gewählt, wenn Pi.Alert auf einem System, mit mindestens 2 Netzwerkkarten und einem konfigurierten Netzwerk, installiert ist. Die Interface-Bezeichnung kann jedoch abweichen und muss den Gegebenheiten des Systems angepasst werden.<br><br>
									        	<span class="text-maroon help_faq_code">[&apos;192.168.1.0/24 --interface=eth0&apos;,&apos;192.168.2.0/24 --interface=eth1&apos;]</span><br>
									        	Die letzte Konfiguration ist dann notwendig, wenn mehrere Netzwerke überwacht werden sollen. Für jedes, zu überwachenden Netzwerk, muss eine entsprechende Netzwerkkarte konfiguriert sein. Dies ist deshalb notwendig, da der verwendete "arp-scan" nicht geroutet wird, also nur innerhalb des eigenen Subnetzes funktioniert. Jede Schnittstelle wird hier mit dem dazugehörigen Netzwerk eingetragen. Die Interface-Bezeichnung muss den Gegebenheiten des Systems angepasst werden.
									        </td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">ICMP Monitoring Options</td></tr>
									    <tr><td class="help_table_gen_a">ICMP_ONLINE_TEST</td>
									        <td class="help_table_gen_b">Anzahl der Versuche um festzustellen, ob ein Gerät online ist (Default 1).</td></tr>
									    <tr><td class="help_table_gen_a">ICMP_GET_AVG_RTT</td>
									        <td class="help_table_gen_b">Anzahl der "ping&apos;s" zur Berechnung der durchschnittlichen Antwortzeit (Default 2).</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Pi-hole Configuration</td></tr>
									    <tr><td class="help_table_gen_a">PIHOLE_ACTIVE</td>
									        <td class="help_table_gen_b">Diese Variable wird während der Installation gesetzt.</td></tr>
									    <tr><td class="help_table_gen_a">PIHOLE_DB</td>
									        <td class="help_table_gen_b">Diese Variable wird während der Installation gesetzt und sollte nicht mehr verändert werden.</td></tr>
									    <tr><td class="help_table_gen_a">DHCP_ACTIVE</td>
									        <td class="help_table_gen_b">Diese Variable wird während der Installation gesetzt.</td></tr>
									    <tr><td class="help_table_gen_a">DHCP_LEASES</td>
									        <td class="help_table_gen_b">Diese Variable wird während der Installation gesetzt und sollte nicht mehr verändert werden.</td></tr>
									</table>
									<table class="help_table_gen">
			    						<tr><td class="help_table_gen_section" colspan="2">Fritzbox Configuration</td></tr>
									    <tr><td class="help_table_gen_a">FRITZBOX_ACTIVE</td>
									        <td class="help_table_gen_b">Wenn eine Fritzbox im Netzwerk zum Einsatz kommt, kann diese als Datenquelle genutzt werden. An dieser Stelle kann dies aktiviert oder deaktiviert werden.</td></tr>
									    <tr><td class="help_table_gen_a">FRITZBOX_IP</td>
									        <td class="help_table_gen_b">IP-Adresse der Fritzbox.</td></tr>
									    <tr><td class="help_table_gen_a">FRITZBOX_USER</td>
									        <td class="help_table_gen_b">Benutzername<br>Dies setzt voraus, dass die Fritzbox für eine Anmeldung mit Benutzername und Passwort, anstatt nur mit Passwort, konfiguiert ist. Eine Anmeldung, nur mit Passwort, wird nicht unterstützt.</td></tr>
									    <tr><td class="help_table_gen_a">FRITZBOX_PASS</td>
									        <td class="help_table_gen_b">Passwort</td></tr>
									</table>
									<table class="help_table_gen">
			    						<tr><td class="help_table_gen_section" colspan="2">Mikrotik Configuration</td></tr>
									    <tr><td class="help_table_gen_a">MIKROTIK_ACTIVE</td>
									        <td class="help_table_gen_b">Wenn ein Mikrotik Router im Netzwerk zum Einsatz kommt, kann dieser als Datenquelle genutzt werden. An dieser Stelle kann dies aktiviert oder deaktiviert werden.</td></tr>
									    <tr><td class="help_table_gen_a">MIKROTIK_IP</td>
									        <td class="help_table_gen_b">IP-Adresse des Mikrotik Router.</td></tr>
									    <tr><td class="help_table_gen_a">MIKROTIK_USER</td>
									        <td class="help_table_gen_b">Benutzername</td></tr>
									    <tr><td class="help_table_gen_a">MIKROTIK_PASS</td>
									        <td class="help_table_gen_b">Passwort</td></tr>
									</table>
									<table class="help_table_gen">
			    						<tr><td class="help_table_gen_section" colspan="2">UniFi Configuration</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_ACTIVE</td>
									        <td class="help_table_gen_b">Wenn ein UniFi System im Netzwerk zum Einsatz kommt, kann dieses als Datenquelle genutzt werden. An dieser Stelle kann dies aktiviert oder deaktiviert werden.</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_IP</td>
									        <td class="help_table_gen_b">IP-Adresse des Unifi Systems.</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_API</td>
									        <td class="help_table_gen_b">Mögliche UNIFI APIs sind v4, v5, unifiOS, UDMP-unifiOS</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_USER</td>
									        <td class="help_table_gen_b">Benutzername</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_PASS</td>
									        <td class="help_table_gen_b">Passwort</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Maintenance Tasks Cron</td></tr>
									    <tr><td class="help_table_gen_a">DAYS_TO_KEEP_ONLINEHISTORY</td>
									        <td class="help_table_gen_b">Anzahl der Tage, für welche die Online-Historie (Aktivitäts-Diagramm) in der Datenbank gespeichert werden soll. Ein Tag generiert 288 solcher Datensätze.</td></tr>
									    <tr><td class="help_table_gen_a">DAYS_TO_KEEP_EVENTS</td>
									        <td class="help_table_gen_b">Anzahl der Tage, für welche die Events der einzelnen Geräte gespeichert werden sollen.</td></tr>
									</table>';
$help_lang['Cat_General_108_head'] = 'Es liegt ein Update vor. Wie gehe ich vor, wenn ich Pi.Alert aktualisieren möchte?';
$help_lang['Cat_General_108_text'] = '<ol>
										<li>Überprüfe in der Statusbox auf der Einsellungsseite, dass gerade kein Scan ausgeführt wird</li>
										<li>Weiter unten, im Bereich Sicherheit, stoppst du nun Pi.Alert für 15min. Hiermit verhinderst du, dass die Datenbank beim Update nicht noch bearbeitet wird.</li>
										<li>Wechsel nun in das Terminal des Gerätes, auf dem Pi.Alert installiert ist.</li>
										<li>Führe den Befehl aus:<br>
											<input id="bashupdatecommand" readonly value="bash -c &quot;$(wget -qLO - https://github.com/leiweibau/Pi.Alert/raw/main/install/pialert_update.sh)&quot;" style="width:100%; overflow-x: scroll; border: none; background: transparent; margin: 0px; padding: 0px;"></li>
										<li>Folge nun den Anweisungen</li>
										<li>Nach dem erfolgreichen Update, sollte Pi.Alert wieder von allein starten. Alternativ kann du auf der Einsellungsseite auch manuell wieder starten.</li>
									</ol>';

$help_lang['Cat_Device_200_head'] = 'Ich habe Geräte in meiner Liste, die mir unbekannt sind, oder die ich nicht mehr verwende. Nach dem Löschen tauchen diese immer wieder auf.';
$help_lang['Cat_Device_200_text'] = 'Wenn du Pi-hole verwendest, beachte bitte, dass Pi.Alert Informationen von Pi-hole abruft. Pausiere Pi.Alert, gehe in Pi-hole auf die Settings-Seite und
 									lösche ggf. die betreffende DHCP-Lease. Anschließend schaue, ebenfalls in Pi-hole, unter Tools -> Network, ob sich dort die immer wiederkehrenden Hosts finden lassen.
 									Wenn ja, lösche diese dort ebenfalls. Sollten diese Geräte auch nach dem Löschen in Pi-hole immer wieder auftauchen, starte den Dienst <span class="text-maroon help_faq_code">pihole-FTL</span> neu.
 									Nun kannst du Pi.Alert wieder starten. Jetzt sollte das Gerät/die Geräte nicht mehr auftauchen. Im Zweifel kann auch ein Neustart nicht schaden.
 									Wenn ein solches Gerät auch weiterhin immer wieder auftaucht, kann die MAC-Adresse einer Ignorierliste <span class="text-maroon help_faq_code">MAC_IGNORE_LIST</span> in der <span class="text-maroon help_faq_code">pialert.conf</span> hinzugefügt werden.';
$help_lang['Cat_Detail_300_head'] = 'Was bedeutet ';
$help_lang['Cat_Detail_300_text_a'] = 'meint ein Netzwerkgerät, welches über die Netzwerk-Seite erstellt wurde.';
$help_lang['Cat_Detail_300_text_b'] = 'bezeichnet die Anschlussnummer/Portnummer, an der das gerade bearbeitete Gerät mit diesem Netzwerkgerät verbunden ist.';
$help_lang['Cat_Detail_301_head_a'] = 'Wann wird nun gescannt? Bei ';
$help_lang['Cat_Detail_301_head_b'] = ' steht 1min aber der Graph zeigt 5min - Abstände an.';
$help_lang['Cat_Detail_301_text'] = 'Den zeitlichen Abstand zwischen den Scans legt der "Cronjob" fest, welcher standardmäßig auf 5min eingestellt ist. Die Benennung "1min" bezieht sich auf die zu erwartende Dauer des Scans.
									Abhängig vor der Netzwerkkonfiguration kann diese Zeitangabe variieren. Um den Cronjob zu bearbeiten, kannst du im Terminal/der Konsole <span class="text-maroon help_faq_code">crontab -e</span>
									eingeben und den Intervall ändern.';
$help_lang['Cat_Detail_302_head_a'] = 'Was bedeutet ';
$help_lang['Cat_Detail_302_head_b'] = ' und warum kann ich das nicht auswählen?';
$help_lang['Cat_Detail_302_text'] = 'Einige moderne Geräte generieren aus Datenschutzgründen zufällige MAC-Adressen, die keinem Hersteller mehr zugeordnet werden können und welche sich mit jeder neuen Verbindung wieder ändern.
									Pi.Alert erkennt, ob es sich um eine solche zufällige MAC-Adresse handelt und aktiviert diese "Feld" automatisch. Um das Verhalten abzustellen, muss du in deinem Endgerät schauen, wie du die
									MAC-Adressen-Generierung deaktivierst.';
$help_lang['Cat_Detail_303_head'] = 'Was ist Nmap und wozu dient es?';
$help_lang['Cat_Detail_303_text'] = 'Nmap ist ein Netzwerkscanner mit vielfältigen Möglichkeiten.<br>
									Wenn ein neues Gerät in deiner Liste auftaucht, hast du die Möglichkeit über den Nmap-Scan genauere Informationen über das Gerät zu erhalten.';
$help_lang['Cat_Presence_400_head'] = 'Geräte werden mit einer gelben Markierung und dem Hinweis "missing Event" angezeigt.';
$help_lang['Cat_Presence_400_text'] = 'Wenn dies geschieht hast du die Möglickeit, bei dem betreffenden Gerät (Detailsansicht) die Events zu löschen. Eine andere Möglichkeit wäre, das Gerät einzuschalten und zu warten, bis Pi.Alert mit dem nächsten
									  Scan das Gerät als "Online" erkennt und anschließend das Gerät einfach wieder ausschalten. Nun sollte Pi.Alert mit dem nächsten Scan den Zustand des Gerätes ordentlich in der Datenbank vermerken.';
$help_lang['Cat_Presence_401_head'] = 'Ein Gerät wird als Anwesend angezeigt, obwohl es "Offline" ist.';
$help_lang['Cat_Presence_401_text'] = 'Wenn dies geschieht hast du die Möglickeit, bei dem betreffenden Gerät (Detailsansicht) die Events zu löschen. Eine andere Möglichkeit wäre, das Gerät einzuschalten und zu warten, bis Pi.Alert mit dem nächsten
									  Scan das Gerät als "Online" erkennt und anschließend das Gerät einfach wieder ausschalten. Nun sollte Pi.Alert mit dem nächsten Scan den Zustand des Gerätes ordentlich in der Datenbank vermerken.';
$help_lang['Cat_Network_600_head'] = 'Was bringt mir diese Seite?';
$help_lang['Cat_Network_600_text'] = 'Diese Seite soll dir die Möglichkeit bieten, die Belegung deiner Netzwerkgeräte abzubilden. Dazu kannst du einen oder mehrere Switches, WLANs, Router, etc. erstellen,
									 sie ggf. mit einer Portanzahl versehen und bereits erkannte Geräte diesen zuordnen. Diese Zuordnung erfolgt in der Detailansicht, des zuzuordnenden Gerätes. So ist es dir möglich, schnell festzustellen
									 an welchem Port ein Host angeschlossen und ob er online ist. Es ist möglich ein Gerät mehreren Ports (bei Portbündelung), als auch mehrere Geräte einem Port (virtuelle Maschinen), zuzuordnen.';
$help_lang['Cat_Network_601_head'] = 'Wie funktioniert die Netzwerk Seite?';
$help_lang['Cat_Network_601_text'] = 'Die Seite besteht aus 2 Komponenten: Der Darstellunsseite und der Verwaltungsseite, zu der man über das "+" Zeichen neben der Überschrift gelangt. Jegliche Bearbeitung auf der
									 Verwaltungsseite hat auschließlich Auswirkungen auf die Darstellungsseite, jedoch nicht auf die Geräte Liste selbst. <br>
									 <br>
									 Auf der Verwaltungsseite wird z.B. ein Switch erstellt. Bereits erkannte Geräte werden in der Auswahlliste angezeigt. Du gibt\'s zusätzlich den Typ und die Portanzahl an.<br><br>
										 In der Detailansicht eines jeden erkannten Gerätes, hast du nun die Möglichkeit, diesen gerade erstellten Switch und den belegten Port zu speichern.<br><br>
										 Jetzt zeigt dir die Netzwerk Darstellunsseite den Switch mit seinen Ports und den daran angeschlossenen Geräten an. Du hast bei jedem Gerät in der Detailansicht die Möglichkeit,
									 mehrere Ports an einem Switch, die du mit einem Komma trennst, zu belegen (z.B. bei Link-Aggregation). Auch ist es möglich, mehrere Geräte einem Port zuzuordnen (z.B. ein Server
									 mit mehreren virtuellen Maschinen).<br>
									 <br>
										 Einen Switch kannst du analog dazu auch einem Router zuweisen, wenn du diesen zuvor auf der Verwaltungsseite erstellt hast. Im Normalfall wird dieser Switch nun auf dem Router-Tab
									 angezeigt. Was aber nicht geschieht ist, dass der Router auf dem Switchport angezeigt wird. Hierfür ist es nötig und möglich, eine manuelle Port-Konfiguration zu speichern. Dazu
									 rufst du die Verwaltungsseite auf und wählst den Switch in der Bearbeitung aus. Nachdem du den Typ und die Portanzahl erneut eingegeben hast (falls das automatische Ausfüllen nicht
									 funktioniert), hast du im untersten Feld eine Auswahlliste an möglichen Geräte. Nach der Auswahl ist nur noch die MAC-Adresse, gefolgt von einem "," zu sehen. Füge hier nun einfach
									 den Port des Routers an dem Switch ein und speichere. Auch hier ist es möglich mehrere MAC-Adressen und Ports anzugeben. Wichtig ist die Einhaltung des Syntax "MAC1,PortA;MAC2,PortB;MAC3,PortC"';
$help_lang['Cat_Network_602_head'] = 'Ein Switch oder Router wird mir ohne Ports angezeigt.';
$help_lang['Cat_Network_602_text'] = 'Möglicherweise wurde beim Anlegen des Gerätes auf der Netzwerkseite versäumt, die Portanzahl einzugeben. Auch bei der Bearbeitung des Gerätes auf der Netzwerkseite, ist es notwendig, eine bereits eingegebene Portanzahl, erneut einzugeben, falls das automatische Ausfüllen nicht funktioniert.<br>
									 Sollte also bei einem bereits angelegten Gerät die Portanzzahl fehlen, wird eine Bearbeitung des Gerätes unter Angabe der <span class="text-maroon help_faq_code">Ports</span>, des <span class="text-maroon help_faq_code">Types</span> und ggf. der <span class="text-maroon help_faq_code">manuelle Port-Konfiguration</span> das Problem beseitigen.';
$help_lang['Cat_Service_700_head'] = 'Was bedeuten die unterschiedlichen Farben der einzelnen Balken?';
$help_lang['Cat_Service_700_text'] = 'Es gibt insgesamt 5 verschiedene Farbcodes: <br>
									 <span style="background-color:lightgray;">&nbsp;&nbsp;&nbsp;</span> - noch kein Scan verfügbar<br>
									 <span class="bg-green">&nbsp;&nbsp;&nbsp;</span> - HTTP Status Code 2xx<br>
									 <span class="bg-yellow">&nbsp;&nbsp;&nbsp;</span> - HTTP Status Code 3xx-4xx<br>
									 <span class="bg-orange-custom">&nbsp;&nbsp;&nbsp;</span> - HTTP Status Code 5xx<br>
									 <span class="bg-red">&nbsp;&nbsp;&nbsp;</span> - Offline';
$help_lang['Cat_Service_701_head'] = 'Welche HTTP Status Codes gibt es? (englisch)';
// von json
$help_lang['Cat_Service_702_head'] = 'Welche Änderungen werden gemeldet?';
$help_lang['Cat_Service_702_text'] = 'Feststellbare Events sind:<br>
										<ul>
											<li>Änderung des HTTP Status Codes</li>
											<li>Änderung der IP</li>
											<li>Antwortzeit des Servers bzw. das Ausbleiben der Antwort</li>
										</ul>
									 Je nach Wahl der Benachrichtigung, wird entweder alles gemeldet, oder nur das Ausbleiben einer Serverantwort.';
$help_lang['Cat_Service_703_head'] = 'Allgemeines zum "Web Service Monitoring".';
$help_lang['Cat_Service_703_text'] = 'Das Monitoring basiert ausschließlich auf den Antworten von  HTTP-Requests, welche an die Seite gesendet werden. Je nach Zustand des Servers können hier sinnvolle Fehlerbilder erkannt werden. Falls der Server überhaupt nicht reagiert, wird dies als "Down/Offline" gewertet. Diese Webserver-Anfragen werden alle 10min im Rahmen der Rahmen des normalen Scans durchgeführt.';
$help_lang['Cat_ServiceDetails_750_head'] = 'Ich kann nicht alle Felder bearbeiten.';
$help_lang['Cat_ServiceDetails_750_text'] = 'Nicht jedes Feld, was auf dieser Seite angezeigt wird, kann auch bearbeitet werden. Bearbeitbare Felder sind:
											<ul>
												<li>' . $pia_lang['WebServices_label_Tags'] . '</li>
												<li>' . $pia_lang['WebServices_label_MAC'] . ' (eventuell ein Gerät, welchem dieser Web Service zugeordnet ist)<br>
													Hier wird eine MAC-Adresse erwartet. Wenn hier etwas anderes (z.B. "Laptop") eingetragen wird, erscheint in der Übersicht "' . $pia_lang['WebServices_unknown_Device'] . ' (Laptop)".
													Services ohne diesen Eintrag werden unter "' . $pia_lang['WebServices_BoxTitle_General'] . '" aufgelistet.</li>
												<li>CheckBox: ' . $pia_lang['WebServices_Events_all'] . '</li>
												<li>CheckBox: ' . $pia_lang['WebServices_Events_down'] . '</li>
											</ul>';

?>