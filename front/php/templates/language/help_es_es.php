<?php
unset($help_lang);

$help_lang['Title'] = 'Ayuda / FAQ';
$help_lang['Cat_General'] = 'General';
$help_lang['Cat_Detail'] = 'Detalles';
$help_lang['Cat_General_100_head'] = 'El reloj en la parte superior derecha y el tiempo de los eventos/presencia no son correctos (diferencia de tiempo).';
$help_lang['Cat_General_100_text_a'] = 'En su PC, la siguiente zona horaria está configurada para el entorno PHP:';
$help_lang['Cat_General_100_text_b'] = 'Si esta no es la zona horaria en la que se encuentra, debe cambiar la zona horaria en el archivo de configuración de PHP. Puedes encontrarlo en este directorio:';
$help_lang['Cat_General_100_text_c'] = 'Busque en este archivo la entrada "date.timezone", elimine el ";" inicial si es necesario e introduzca la zona horaria deseada. Puede encontrar una lista con las zonas horarias compatibles aquí (<a href="https://www.php.net/manual/en/timezones.php" target="blank">Link</a>)';
$help_lang['Cat_General_101_head'] = 'Mi red parece ralentizarse, el streaming se "congela".';
$help_lang['Cat_General_101_text'] = 'Es muy posible que los dispositivos de baja potencia alcancen sus límites de rendimiento con la forma en que Pi.Alert detecta nuevos dispositivos en la red. Esto se amplifica aún más,
									si estos dispositivos se comunican con la red a través de WLAN. Las soluciones aquí serían cambiar a una conexión por cable si es posible o, si el dispositivo sólo se va a utilizar durante un período de tiempo limitado, utilizar el arp scan.
									pausar el arp scan en la página de mantenimiento.';
$help_lang['Cat_General_102_head'] = 'Me aparece el mensaje de que la base de datos es de sólo de lectura.';
$help_lang['Cat_General_102_text'] = 'Es posible que en este momento el backend esté escribiendo cambios en la base de datos. Por favor, inténtalo de nuevo después de una breve espera. Si el comportamiento no cambia, sigue las instrucciones a continuación.<br><br>
									 Compruebe en el directorio Pi.Alert si la carpeta de la base de datos (db) tiene asignados los permisos correctos:<br>
      								 <span class="text-maroon help_faq_code">drwxrwxr-x  2 (nombre de usuario) www-data</span><br>
      								 Si el permiso no es correcto, puede establecerlo de nuevo con los siguientes comandos en la terminal o la consola:<br>
      								 <div class="help_faq_code" style="padding-left: 10px; margin-bottom: 10px;">
      								 sudo chgrp -R www-data ~/pialert/db<br>
      								 sudo chown [Username]:www-data ~/pialert/db/pialert.db<br>
        							 chmod -R 775 ~/pialert/db
      								 </div>
									 Otra opción es restablecer los permisos necesarios en el directorio <span class="text-maroon help_faq_code">~/pialert/back</span> utilizando <span class="text-maroon help_faq_code">pialert-cli</span>. Tienes varias opciones disponibles.<br><br>
									 <span class="text-maroon help_faq_code">./pialert-cli set_permissions</span><br>
									 Este comando solo restablece los permisos del grupo, dejando sin cambios al propietario del archivo.<br><br>
									 <span class="text-maroon help_faq_code">./pialert-cli set_permissions --lxc</span><br>
									 Esta opción adicional se introdujo para su uso en un contenedor LXC. Cambia el grupo según la funcionalidad básica y establece al usuario "root" como propietario. Esta opción no es relevante fuera de un entorno LXC.<br><br>
									 <span class="text-maroon help_faq_code">./pialert-cli set_permissions --homedir</span><br>
									 Esta opción debería ser la preferida. Aquí, el nombre de usuario se determina en función del directorio principal del hogar de la instalación de Pi.Alert. Este nombre de usuario se convierte en el propietario de los archivos. El grupo se establece según la funcionalidad básica.';
$help_lang['Cat_General_103_head'] = 'La página de inicio de sesión no aparece, incluso después de cambiar la contraseña.';
$help_lang['Cat_General_103_text'] = 'Además de la contraseña, el archivo de configuración debe contener <span class="text-maroon help_faq_code">~/pialert/config/pialert.conf</span>
      								 además el parámetro <span class="text-maroon help_faq_code">PIALERT_WEB_PROTECTION</span> debe ajustarse a <span class="text-maroon help_faq_code">True</span>.';
$help_lang['Cat_General_104_head'] = 'Notes on migrating from pucherot to this fork.';
$help_lang['Cat_General_104_text'] = 'The database in this fork has been extended by some fields. To take over the database from the original Pi.Alert (pucherot), an update function is available via the "pialert-cli" in the directory <span class="text-maroon help_faq_code">~/pialert/back</span>.
									 The command is then <span class="text-maroon help_faq_code">./pialert-cli update_db</span>';
$help_lang['Cat_General_105_head'] = 'Explicaciones para "pialert-cli"';
$help_lang['Cat_General_105_text'] = 'The command line tool <span class="text-maroon help_faq_code">pialert-cli</span> is located in the directory <span class="text-maroon help_faq_code">~/pialert/back</span> and offers the possibility to make settings to Pi.Alert
                                     without web page or change to the configuration file. With the command <span class="text-maroon help_faq_code">./pialert-cli help</span> a list with the supported options can be called.
									 <table class="help_table_gen">
									    <tr><td class="help_table_gen_a">set_login</td>
										    <td class="help_table_gen_b">- Establece el parámetro PIALERT_WEB_PROTECTION en el archivo de configuración a TRUE.<br>
										        - Si el parámetro no está presente, se creará. Además, se establecerá la contraseña predeterminada "123456".</td></tr>
										<tr><td class="help_table_gen_a">unset_login</td>
										    <td class="help_table_gen_b">- Establece el parámetro PIALERT_WEB_PROTECTION en el archivo de configuración a FALSE.<br>
										        - Si el parámetro no está presente, se creará. Además, se establecerá la contraseña predeterminada "123456".</td></tr>
										<tr><td class="help_table_gen_a">set_password &lt;password&gt;</td>
										    <td class="help_table_gen_b">- Establece la nueva contraseña como un valor hash.<br>
										        - Si el parámetro PIALERT_WEB_PROTECTION no existe aún, se creará y se establecerá en "TRUE" (inicio de sesión habilitado).</td></tr>
										<tr><td class="help_table_gen_a">set_autopassword</td>
										    <td class="help_table_gen_b">- Establece una nueva contraseña aleatoria como un valor hash y la muestra en texto sin formato en la consola.<br>
										        - Si el parámetro PIALERT_WEB_PROTECTION no existe aún, se creará y se establecerá en "TRUE" (inicio de sesión habilitado).</td></tr>
										<tr><td class="help_table_gen_a">disable_scan &lt;MIN&gt;</td>
										    <td class="help_table_gen_b">- Detiene todas las exploraciones activas.<br>
										        - Impide que se inicien nuevas exploraciones.</td></tr>
										<tr><td class="help_table_gen_a">enable_scan</td>
										    <td class="help_table_gen_b">- Permite el inicio de nuevas exploraciones nuevamente.</td></tr>
										<tr><td class="help_table_gen_a">disable_mainscan</td>
										    <td class="help_table_gen_b">- Desactiva el método principal de escaneo para Pi.Alert (escaneo ARP)</td></tr>
										<tr><td class="help_table_gen_a">enable_mainscan</td>
										    <td class="help_table_gen_b">- Activa el método principal de escaneo para Pi.Alert (escaneo ARP)</td></tr>
										<tr><td class="help_table_gen_a">enable_service_mon</td>
										    <td class="help_table_gen_b">- Habilita la supervisión de servicios web.</td></tr>
										<tr><td class="help_table_gen_a">disable_service_mon</td>
										    <td class="help_table_gen_b">- Deshabilita la supervisión de servicios web.</td></tr>
									    <tr><td class="help_table_gen_a">enable_icmp_mon</td>
									        <td class="help_table_gen_b">- Enable ICMP Monitoring (ping)<br>&nbsp;</td></tr>
									    <tr><td class="help_table_gen_a">disable_icmp_mon</td>
									        <td class="help_table_gen_b">- Disable ICMP Monitoring (ping)<br>&nbsp;</td></tr>
										<tr><td class="help_table_gen_a">update_db</td>
										    <td class="help_table_gen_b">- El script intenta hacer que la base de datos sea compatible con esta bifurcación.</td></tr>
										<tr><td class="help_table_gen_a">set_apikey</td>
										    <td class="help_table_gen_b">- Con la clave API es posible hacer consultas a la base de datos sin utilizar la página web. Si ya existe una clave API, se reemplazará.</td></tr>
										<tr><td class="help_table_gen_a">set_permissions</td>
											<td class="help_table_gen_b">- Repara los permisos del archivo de la base de datos para el grupo. Si también es necesario restablecer los permisos para el usuario, se requiere una opción adicional:<br>
											<span class="text-maroon" style="display:inline-block;width:130px;">--lxc</span> establece "root" como nombre de usuario<br>
											<span class="text-maroon" style="display:inline-block;width:130px;">--custom</span> establece un nombre de usuario personalizado<br>
											<span class="text-maroon" style="display:inline-block;width:130px;">--homedir</span> toma el nombre de usuario del directorio de inicio</td></tr>
										<tr><td class="help_table_gen_a">reporting_test</td>
										    <td class="help_table_gen_b">- Prueba de informes para todos los servicios activados.</td></tr>
									    <tr><td class="help_table_gen_a">set_sudoers</td>
									        <td class="help_table_gen_b">- Create sudoer file for www-data and Pi.Alert user</td></tr>
									    <tr><td class="help_table_gen_a">unset_sudoers</td>
									        <td class="help_table_gen_b">- Delete sudoer file for www-data and Pi.Alert user</td></tr>
									</table>';
$help_lang['Cat_General_106_head'] = '¿Cómo puedo realizar una comprobación de integridad en la base de datos?';
$help_lang['Cat_General_106_text'] = 'Si deseas comprobar la base de datos que está en uso, detén Pi.Alert durante aproximadamente 1 hora para evitar cualquier acceso de escritura a la base de datos durante la comprobación. Además, la interfaz web no debe estar abierta para otras operaciones de escritura durante la comprobación.
									 Ahora, abre la consola en el directorio <span class="text-maroon help_faq_code">~/pialert/db</span> y utiliza el comando <span class="text-maroon help_faq_code">ls</span> para listar el contenido del directorio. Si los archivos
									 <span class="text-maroon help_faq_code">pialert.db-shm</span> y <span class="text-maroon help_faq_code">pialert.db-wal</span> aparecen en la lista (con la misma marca de tiempo que el archivo "pialert.db"), significa que todavía hay transacciones de base de datos abiertas. En este caso, simplemente espera un momento y, para verificar, ejecuta nuevamente el comando <span class="text-maroon help_faq_code">ls</span>.
									 <br><br>
									 Una vez que estos archivos hayan desaparecido, se puede realizar la comprobación. Para hacerlo, ejecuta los siguientes comandos:<br>
									 <div class="help_faq_code" style="padding-left: 10px; margin-bottom: 10px;">
									    sqlite3 pialert.db "PRAGMA integrity_check"<br>
									    sqlite3 pialert.db "PRAGMA foreign_key_check"
									 </div><br>
									 En ambos casos, no debería informarse de errores. Después de la comprobación, puedes reiniciar Pi.Alert.';
$help_lang['Cat_General_107_head'] = 'Explicaciones para el archivo "pialert.conf"';
$help_lang['Cat_General_107_text'] = 'The file <span class="text-maroon help_faq_code">pialert.conf</span> is located in the directory <span class="text-maroon help_faq_code">~/pialert/config</span>.
									 In this configuration file many functions of Pi.Alert can be set according to the personal wishes. Since the possibilities are various, I would like to give a
									 short explanation to the individual points.
									 <table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">General Settings</td></tr>
									    <tr><td class="help_table_gen_a">PIALERT_PATH</td>
										    <td class="help_table_gen_b">Esta variable se establece durante la instalación y no debe modificarse.</td></tr>
										<tr><td class="help_table_gen_a">DB_PATH</td>
										    <td class="help_table_gen_b">Esta variable se establece durante la instalación y no debe modificarse.</td></tr>
										<tr><td class="help_table_gen_a">LOG_PATH</td>
										    <td class="help_table_gen_b">Esta variable se establece durante la instalación y no debe modificarse.</td></tr>
										<tr><td class="help_table_gen_a">PRINT_LOG</td>
										    <td class="help_table_gen_b">Si esta entrada se establece en <span class="text-maroon help_faq_code">True</span>, se agregan marcas de tiempo adicionales para las subfunciones individuales en el registro de escaneo. De forma predeterminada, esta entrada se establece en <span class="text-maroon help_faq_code">False</span>.</td></tr>
										<tr><td class="help_table_gen_a">VENDORS_DB</td>
										    <td class="help_table_gen_b">Esta variable se establece durante la instalación y no debe modificarse.</td></tr>
										<tr><td class="help_table_gen_a">PIALERT_APIKEY</td>
										    <td class="help_table_gen_b">Con la clave de API, es posible realizar consultas a la base de datos sin utilizar la página web. La clave de API es una cadena aleatoria que se puede establecer a través de la configuración o mediante <span class="text-maroon help_faq_code">pialert-cli</span>.</td></tr>
										<tr><td class="help_table_gen_a">PIALERT_WEB_PROTECTION</td>
										    <td class="help_table_gen_b">Activa o desactiva la protección con contraseña de la interfaz web de Pi.Alert.</td></tr>
										<tr><td class="help_table_gen_a">PIALERT_WEB_PASSWORD</td>
										    <td class="help_table_gen_b">Este campo contiene la contraseña cifrada para la interfaz web. La contraseña no se puede ingresar aquí en texto plano, sino que debe establecerse con <span class="text-maroon help_faq_code">pialert-cli</span>.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Other Modules</td></tr>
									    <tr><td class="help_table_gen_a">SCAN_WEBSERVICES</td>
										    <td class="help_table_gen_b">Aquí se puede activar (<span class="text-maroon help_faq_code">True</span>) o desactivar (<span class="text-maroon help_faq_code">False</span>) la función de monitoreo de servicios web.</td></tr>
									    <tr><td class="help_table_gen_a">ICMPSCAN_ACTIVE</td>
									        <td class="help_table_gen_b">ICMP Monitoring on/off</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Special Protocol Scanning</td></tr>
									    <tr><td class="help_table_gen_a">SCAN_ROGUE_DHCP</td>
										    <td class="help_table_gen_b">Activa la búsqueda de servidores DHCP externos, también conocidos como "rogue". Esta función se utiliza para detectar si hay un servidor DHCP externo en la red que podría tomar el control de la gestión de IP.</td></tr>
										<tr><td class="help_table_gen_a">DHCP_SERVER_ADDRESS</td>
										    <td class="help_table_gen_b">Aquí se almacena la dirección IP del servidor DHCP conocido.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Mail-Account Settings</td></tr>
									    <tr><td class="help_table_gen_a">SMTP_SERVER</td>
										    <td class="help_table_gen_b">Dirección del servidor de correo electrónico (por ejemplo, smtp.gmail.com)</td></tr>
										<tr><td class="help_table_gen_a">SMTP_PORT</td>
										    <td class="help_table_gen_b">El puerto del servidor SMTP. El puerto puede variar según la configuración del servidor.</td></tr>
										<tr><td class="help_table_gen_a">SMTP_USER</td>
										    <td class="help_table_gen_b">Nombre de usuario</td></tr>
										<tr><td class="help_table_gen_a">SMTP_PASS</td>
										    <td class="help_table_gen_b">Contraseña</td></tr>
										<tr><td class="help_table_gen_a">SMTP_SKIP_TLS</td>
										    <td class="help_table_gen_b">Si esta entrada se establece en <span class="text-maroon help_faq_code">True</span>, se habilita el cifrado de transporte del correo electrónico. Si el servidor no admite esto, la entrada debe establecerse en <span class="text-maroon help_faq_code">False</span>.</td></tr>
										<tr><td class="help_table_gen_a">SMTP_SKIP_LOGIN</td>
										    <td class="help_table_gen_b">Hay servidores SMTP que no requieren inicio de sesión. En tal caso, este valor se puede establecer en <span class="text-maroon help_faq_code">True</span>.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">WebGUI Reporting</td></tr>
										<tr><td class="help_table_gen_a">REPORT_WEBGUI</td>
										    <td class="help_table_gen_b">Activa/desactiva las notificaciones sobre cambios en la red en la interfaz web.</td></tr>
										<tr><td class="help_table_gen_a">REPORT_WEBGUI_WEBMON</td>
										    <td class="help_table_gen_b">Activa/desactiva las notificaciones sobre cambios en los servicios web monitoreados en la interfaz web.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Mail Reporting</td></tr>
										<tr><td class="help_table_gen_a">REPORT_MAIL</td>
										    <td class="help_table_gen_b">Activa/desactiva las notificaciones sobre cambios en la red mediante correo electrónico.</td></tr>
										<tr><td class="help_table_gen_a">REPORT_MAIL_WEBMON</td>
										    <td class="help_table_gen_b">Activa/desactiva la notificación de cambios en los servicios web monitoreados mediante correo electrónico.</td></tr>
										<tr><td class="help_table_gen_a">REPORT_FROM</td>
										    <td class="help_table_gen_b">Nombre o identificador del remitente.</td></tr>
										<tr><td class="help_table_gen_a">REPORT_TO</td>
										    <td class="help_table_gen_b">Dirección de correo electrónico a la cual se debe enviar la notificación.</td></tr>
										<tr><td class="help_table_gen_a">REPORT_DEVICE_URL</td>
										    <td class="help_table_gen_b">URL de la instalación de Pi.Alert para crear un enlace clickable en el correo electrónico hacia el dispositivo detectado.</td></tr>
										<tr><td class="help_table_gen_a">REPORT_DASHBOARD_URL</td>
										    <td class="help_table_gen_b">URL de la instalación de Pi.Alert, para poder crear un enlace clickable en el correo electrónico.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Pushsafer</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_PUSHSAFER</td>
										    <td class="help_table_gen_b">Permite habilitar/deshabilitar notificaciones sobre cambios en la red a través de Pushsafer.</td></tr>
										<tr><td class="help_table_gen_a">REPORT_PUSHSAFER_WEBMON</td>
										    <td class="help_table_gen_b">Permite habilitar/deshabilitar notificaciones sobre cambios en los servicios web monitoreados a través de Pushsafer.</td></tr>
										<tr><td class="help_table_gen_a">PUSHSAFER_TOKEN</td>
										    <td class="help_table_gen_b">Esta es la clave privada que se puede ver en la página de Pushsafer.</td></tr>
										<tr><td class="help_table_gen_a">PUSHSAFER_DEVICE</td>
										    <td class="help_table_gen_b">El ID del dispositivo al cual se enviará el mensaje. &lsquo;<span class="text-maroon help_faq_code">a</span>&rsquo; significa que el mensaje se enviará a todos los dispositivos configurados y consumirá un número correspondiente de llamadas API.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Pushover</td></tr>
										<tr><td class="help_table_gen_a">REPORT_PUSHOVER</td>
										    <td class="help_table_gen_b">Permite habilitar/deshabilitar notificaciones sobre cambios en la red a través de Pushover.</td></tr>
										<tr><td class="help_table_gen_a">REPORT_PUSHOVER_WEBMON</td>
										    <td class="help_table_gen_b">Permite habilitar/deshabilitar notificaciones sobre cambios en los servicios web monitoreados a través de Pushover.</td></tr>
										<tr><td class="help_table_gen_a">PUSHOVER_TOKEN</td>
										    <td class="help_table_gen_b">También conocido como "APP TOKEN" o "API TOKEN". Este token se puede obtener en la página de Pushover.</td></tr>
										<tr><td class="help_table_gen_a">PUSHOVER_USER</td>
										    <td class="help_table_gen_b">También conocido como "USER KEY". Esta clave se muestra justo después de iniciar sesión en la página de inicio.</td></tr>
									</table>
									<table class="help_table_gen">
			    						<tr><td class="help_table_gen_section" colspan="2">NTFY</td></tr>
										<tr><td class="help_table_gen_a">REPORT_NTFY</td>
										    <td class="help_table_gen_b">Permite habilitar/deshabilitar notificaciones sobre cambios en la red a través de NTFY.</td></tr>
										<tr><td class="help_table_gen_a">REPORT_NTFY_WEBMON</td>
										    <td class="help_table_gen_b">Permite habilitar/deshabilitar notificaciones sobre cambios en los servicios web monitoreados a través de NTFY.</td></tr>
										<tr><td class="help_table_gen_a">NTFY_HOST</td>
										    <td class="help_table_gen_b">El nombre de host o la dirección IP del servidor NTFY.</td></tr>
										<tr><td class="help_table_gen_a">NTFY_TOPIC</td>
										    <td class="help_table_gen_b">El tema o asunto de las notificaciones enviadas a través de NTFY.</td></tr>
										<tr><td class="help_table_gen_a">NTFY_USER</td>
										    <td class="help_table_gen_b">El nombre de usuario utilizado para autenticarse en el servidor NTFY.</td></tr>
										<tr><td class="help_table_gen_a">NTFY_PASSWORD</td>
										    <td class="help_table_gen_b">La contraseña utilizada para autenticarse en el servidor NTFY.</td></tr>
										<tr><td class="help_table_gen_a">NTFY_PRIORITY</td>
										    <td class="help_table_gen_b">La prioridad de las notificaciones enviadas a través de NTFY.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Shoutrrr</td></tr>
										<tr><td class="help_table_gen_a">SHOUTRRR_BINARY</td>
										    <td class="help_table_gen_b">Aquí debes configurar qué archivo binario de Shoutrrr se debe utilizar. Esto depende del hardware en el que se haya instalado Pi.Alert.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Telegram via Shoutrrr</td></tr>
										<tr><td class="help_table_gen_a">REPORT_TELEGRAM</td>
										    <td class="help_table_gen_b">Habilita/deshabilita las notificaciones sobre cambios en la red a través de Telegram.</td></tr>
										<tr><td class="help_table_gen_a">REPORT_TELEGRAM_WEBMON</td>
										    <td class="help_table_gen_b">Habilita/deshabilita las notificaciones sobre cambios en los servicios web monitoreados a través de Telegram.</td></tr>
										<tr><td class="help_table_gen_a">TELEGRAM_BOT_TOKEN_URL</td>
										    <td class="help_table_gen_b">    </td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">DynDNS and IP</td></tr>
									    <tr><td class="help_table_gen_a">QUERY_MYIP_SERVER</td>
									        <td class="help_table_gen_b">    </td></tr>
									    <tr><td class="help_table_gen_a">DDNS_ACTIVE</td>
											<td class="help_table_gen_b">Habilita/deshabilita el servicio DDNS configurado en Pi.Alert. DDNS, también conocido como DynDNS, te permite actualizar un nombre de dominio con una dirección IP que cambia regularmente. Este servicio es ofrecido por varios proveedores de servicios.</td></tr>
									    <tr><td class="help_table_gen_a">DDNS_DOMAIN</td>
									        <td class="help_table_gen_b">    </td></tr>
									    <tr><td class="help_table_gen_a">DDNS_USER</td>
									        <td class="help_table_gen_b">    </td></tr>
									    <tr><td class="help_table_gen_a">DDNS_PASSWORD</td>
									        <td class="help_table_gen_b">    </td></tr>
									    <tr><td class="help_table_gen_a">DDNS_UPDATE_URL</td>
									        <td class="help_table_gen_b">    </td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Automatic Speedtest</td></tr>
									    <tr><td class="help_table_gen_a">SPEEDTEST_TASK_ACTIVE</td>
									        <td class="help_table_gen_b"></td></tr>
									    <tr><td class="help_table_gen_a">SPEEDTEST_TASK_HOUR</td>
									        <td class="help_table_gen_b"></td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Arp-scan Options & Samples</td></tr>
									        <td class="help_table_gen_b">
									            <span class="text-maroon help_faq_code">[&apos;MAC-Address 1&apos;, &apos;MAC-Address 2&apos;]</span><br>
									            Estas direcciones MAC (guardadas con letras minúsculas) se filtran de los resultados del escaneo.</td></tr>
									    <tr><td class="help_table_gen_a">SCAN_SUBNETS</td>
										    <td class="help_table_gen_b">
										        &lsquo;<span class="text-maroon help_faq_code">--localnet</span>&rsquo;<br>
										        Normalmente esta opción ya está configurada correctamente. Esta configuración se selecciona cuando Pi.Alert se instala en un dispositivo con una tarjeta de red y no se configuran otras redes.<br><br>
										        &lsquo;<span class="text-maroon help_faq_code">--localnet --interface=eth0</span>&rsquo;<br>
										        Esta configuración se selecciona si Pi.Alert se instala en un sistema con al menos 2 tarjetas de red y una red configurada. Sin embargo, la designación de la interfaz puede variar y debe adaptarse a las condiciones del sistema.<br><br>
										        <span class="text-maroon help_faq_code">[&apos;192.168.1.0/24 --interface=eth0&apos;,&apos;192.168.2.0/24 --interface=eth1&apos;]</span><br>
										        La última configuración es necesaria si se van a monitorear varias redes. Para cada red que se va a monitorear, se debe configurar una tarjeta de red correspondiente. Esto es necesario porque el "arp-scan" utilizado no está enrutado, es decir, solo funciona dentro de su propio subred. Cada interfaz se ingresa aquí con la red correspondiente. La designación de la interfaz debe adaptarse a las condiciones del sistema.
										    </td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Opciones de Monitoreo ICMP</td></tr>
									    <tr><td class="help_table_gen_a">ICMP_ONLINE_TEST</td>
									        <td class="help_table_gen_b">Número de intentos para determinar si un dispositivo está en línea (Valor predeterminado 1).</td></tr>
									    <tr><td class="help_table_gen_a">ICMP_GET_AVG_RTT</td>
									        <td class="help_table_gen_b">Número de "ping&apos;s" para calcular el tiempo de respuesta promedio (Valor predeterminado 2).</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Pi-hole Configuration</td></tr>
										<tr><td class="help_table_gen_a">PIHOLE_ACTIVE</td>
										    <td class="help_table_gen_b">Esta variable se configura durante la instalación.</td></tr>
										<tr><td class="help_table_gen_a">PIHOLE_DB</td>
										    <td class="help_table_gen_b">Esta variable se configura durante la instalación y no debe modificarse.</td></tr>
										<tr><td class="help_table_gen_a">DHCP_ACTIVE</td>
										    <td class="help_table_gen_b">Esta variable se configura durante la instalación.</td></tr>
										<tr><td class="help_table_gen_a">DHCP_LEASES</td>
										    <td class="help_table_gen_b">Esta variable se configura durante la instalación y no debe modificarse.</td></tr>
									</table>
									<table class="help_table_gen">
			    						<tr><td class="help_table_gen_section" colspan="2">Fritzbox Configuration</td></tr>
										<tr><td class="help_table_gen_a">FRITZBOX_ACTIVE</td>
										    <td class="help_table_gen_b">Si se utiliza un Fritzbox en la red, se puede utilizar como fuente de datos. Esto se puede activar o desactivar en este punto.</td></tr>
										<tr><td class="help_table_gen_a">FRITZBOX_IP</td>
										    <td class="help_table_gen_b">Dirección IP del Fritzbox.</td></tr>
										<tr><td class="help_table_gen_a">FRITZBOX_USER</td>
										    <td class="help_table_gen_b">Nombre de usuario<br>Esto asume que el Fritzbox está configurado para un inicio de sesión con nombre de usuario y contraseña, en lugar de solo contraseña. No se admite el inicio de sesión solo con contraseña.</td></tr>
										<tr><td class="help_table_gen_a">FRITZBOX_PASS</td>
										    <td class="help_table_gen_b">Contraseña</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Configuración de Mikrotik</td></tr>
									    <tr><td class="help_table_gen_a">MIKROTIK_ACTIVE</td>
									        <td class="help_table_gen_b">Si se utiliza un router Mikrotik en la red, se puede utilizar como fuente de datos. Esto se puede habilitar o deshabilitar en este punto.</td></tr>
									    <tr><td class="help_table_gen_a">MIKROTIK_IP</td>
									        <td class="help_table_gen_b">Dirección IP del router Mikrotik.</td></tr>
									    <tr><td class="help_table_gen_a">MIKROTIK_USER</td>
									        <td class="help_table_gen_b">Nombre de usuario</td></tr>
									    <tr><td class="help_table_gen_a">MIKROTIK_PASS</td>
									        <td class="help_table_gen_b">Contraseña</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Configuración de UniFi</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_ACTIVE</td>
									        <td class="help_table_gen_b">Si se utiliza un sistema UniFi en la red, se puede utilizar como fuente de datos. Esto se puede habilitar o deshabilitar en este punto.</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_IP</td>
									        <td class="help_table_gen_b">Dirección IP del sistema Unifi.</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_API</td>
									        <td class="help_table_gen_b">Possible UNIFI APIs are v4, v5, unifiOS, UDMP-unifiOS</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_USER</td>
									        <td class="help_table_gen_b">Nombre de usuario</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_PASS</td>
									        <td class="help_table_gen_b">Contraseña</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Maintenance Tasks Cron</td></tr>
										<tr><td class="help_table_gen_a">DAYS_TO_KEEP_ONLINEHISTORY</td>
										    <td class="help_table_gen_b">Número de días durante los cuales se almacenará el historial en línea (gráfico de actividad) en la base de datos. Un día genera 288 registros de este tipo.</td></tr>
										<tr><td class="help_table_gen_a">DAYS_TO_KEEP_EVENTS</td>
										    <td class="help_table_gen_b">Número de días durante los cuales se almacenarán los eventos de los dispositivos individuales.</td></tr>
									</table>';
$help_lang['Cat_General_108_head'] = 'Hay una actualización disponible. ¿Qué debo hacer si quiero actualizar Pi.Alert?';
$help_lang['Cat_General_108_text'] = '<ol>
										<li>Verifica en la caja de estado en la página de configuración que no se esté ejecutando un escaneo en este momento.</li>
										<li>Más abajo, en la sección de seguridad, detén Pi.Alert durante 15 minutos. Esto evita que la base de datos se modifique durante la actualización.</li>
										<li>Ahora cambia al terminal del dispositivo donde está instalado Pi.Alert.</li>
										<li>Ejecuta el comando:<br>
											<input id="bashupdatecommand" readonly value="bash -c &quot;$(wget -qLO - https://github.com/leiweibau/Pi.Alert/raw/main/install/pialert_update.sh)&quot;" style="width:100%; overflow-x: scroll; border: none; background: transparent; margin: 0px; padding: 0px;"></li>
										<li>Sigue las instrucciones.</li>
										<li>Después de una actualización exitosa, Pi.Alert debería iniciarse automáticamente. Alternativamente, puedes reiniciarlo manualmente en la página de configuración.</li>
									</ol>';
$help_lang['Cat_Device_200_head'] = 'Tengo dispositivos en mi lista que no conozco. Después de borrarlos, siempre vuelven a aparecer.';
$help_lang['Cat_Device_200_text'] = 'Si utiliza Pi-hole, tenga en cuenta que Pi.Alert recupera información de Pi-hole. Ponga en pausa Pi.Alert, vaya a la página de configuración de Pi-hole y
									elimine la concesión DHCP si es necesario. Luego, también en Pi-hole, revise en Herramientas -> Red para ver si puede encontrar los hosts recurrentes allí.
									Si es así, elimínelos también allí. Ahora puede volver a iniciar Pi.Alert. Ahora el dispositivo(s) no debería aparecer más.
									Si dicho dispositivo sigue apareciendo repetidamente, se puede agregar la dirección MAC a la lista de ignorados <span class="text-maroon help_faq_code">MAC_IGNORE_LIST</span> en el archivo <span class="text-maroon help_faq_code">pialert.conf</span>.';
$help_lang['Cat_Detail_300_head'] = '¿Qué significa? ';
$help_lang['Cat_Detail_300_text_a'] = 'significa un dispositivo de red creado a partir de la página de red.';
$help_lang['Cat_Detail_300_text_b'] = 'designa el número de puerto en el que el dispositivo editado actualmente está conectado a este dispositivo de red.';
$help_lang['Cat_Detail_301_head_a'] = '¿Cuándo está escaneando ahora? En ';
$help_lang['Cat_Detail_301_head_b'] = ' dice 1min pero el gráfico muestra intervalos de 5min.';
$help_lang['Cat_Detail_301_text'] = 'El intervalo de tiempo entre los escaneos está definido por el "Cronjob", que está configurado en 5 minutos de forma predeterminada.  La designación "1min" se refiere a la duración esperada del escaneo.
									Dependiendo de la configuración de la red, este tiempo puede variar. Para editar el cronjob, puede utilizar el siguiente comando en la terminal/consola <span class="text-maroon help_faq_code">crontab -e</span>
									y cambiar el intervalo.';
$help_lang['Cat_Detail_302_head_a'] = '¿Qué significa? ';
$help_lang['Cat_Detail_302_head_b'] = '¿y por qué no puedo seleccionarlo?';
$help_lang['Cat_Detail_302_text'] = 'Algunos dispositivos modernos generan direcciones MAC aleatorias por razones de privacidad, que ya no pueden asociarse a ningún fabricante y que vuelven a cambiar con cada nueva conexión.
									Pi.Alert detecta si se trata de una dirección MAC aleatoria y activa este "campo" automáticamente. Para deshabilitar este comportamiento, debe buscar en su dispositivo cómo deshabilitar la
									aleatorización de direcciones MAC.';
$help_lang['Cat_Detail_303_head'] = '¿Qué es Nmap y para qué sirve?';
$help_lang['Cat_Detail_303_text'] = 'Nmap es un escáner de red con múltiples capacidades.<br>
									Cuando aparece un nuevo dispositivo en su lista, tiene la posibilidad de obtener información más detallada sobre el dispositivo a través del escaneo de Nmap.';
$help_lang['Cat_Presence_400_head'] = 'Los dispositivos se muestran con un marcador amarillo y la nota "evento faltante".';
$help_lang['Cat_Presence_400_text'] = 'Si esto sucede, tiene la opción de eliminar los eventos en el dispositivo en cuestión (vista de detalles).  Otra posibilidad sería encender el dispositivo y esperar hasta que Pi.Alert detecte el dispositivo como "online" con el siguiente
										escaneo y luego simplemente apagar el dispositivo nuevamente.  Ahora Pi.Alert debería anotar correctamente el estado del dispositivo en la base de datos con el próximo escaneo.';
$help_lang['Cat_Presence_401_head'] = 'Un dispositivo se muestra como presente aunque esté "Offline".';
$help_lang['Cat_Presence_401_text'] = 'Si esto sucede, tiene la posibilidad de eliminar los eventos del dispositivo en cuestión (vista de detalles).  Otra posibilidad sería encender el dispositivo y esperar hasta que Pi.Alert reconozca el dispositivo como "online" con el siguiente escaneo
										y luego simplemente apagar el dispositivo nuevamente.  Ahora Pi.Alert debería anotar correctamente el estado del dispositivo en la base de datos con el próximo escaneo.';
$help_lang['Cat_Network_600_head'] = '¿Para qué sirve esta sección?';
$help_lang['Cat_Network_600_text'] = 'Esta sección debería ofrecerle la posibilidad de mapear la asignación de sus dispositivos de red.  Para ello, puede crear uno o más conmutadores, WLAN, enrutadores, etc., proporcionarles un número de puerto si es necesario y asignarles dispositivos
										ya detectados. Esta asignación se realiza en la vista detallada del dispositivo a asignar.  Por lo tanto, es posible determinar rápidamente a qué puerto está conectado un host y si está en línea. Es posible asignar un dispositivo a múltiples
										puertos (agrupación de puertos), así como múltiples dispositivos a un puerto (máquinas virtuales).';
$help_lang['Cat_Network_601_head'] = 'How does the network page work?';
$help_lang['Cat_Network_601_text'] = 'On the network side, for example, a switch is created. For this purpose, I already offer corresponding devices in the selection list. You continue to specify the type and the number of ports.<br><br>
									 On the detail view you have now, with each recognized device, the possibility to save this just created switch and the occupied port.<br><br>
									 Now the network page shows you the switch with its ports and the devices connected to it. For each device in the detail view, you have the option of assigning multiple ports to a switch, which you separate with a comma (e.g. for link aggregation). It is also possible to assign several devices to one port (e.g. a server with several virtual machines).<br><br>
									 You can also assign a switch to a router if you have created it on the network side. Normally, this switch will now be displayed on the router tab. What does not happen is that the router is displayed on the switch port. For this it is necessary and possible to save a manual port configuration. To do this, open the "Administration" and select the switch in the editing. After you have entered the type and the number of ports again, you have a selection list of possible devices in the lowest field. After the selection, only the MAC address is visible, followed by a ",". Now simply add the port of the router on the switch and save. It is also possible to enter multiple MAC addresses and ports. It is important to follow the syntax "MAC1,PortA;MAC2,PortB;MAC3,PortC".';
$help_lang['Cat_Network_602_head'] = 'A switch or router is shown to me without ports.';
$help_lang['Cat_Network_602_text'] = 'It is possible that the number of ports was not entered when the device was created on the network page. When editing the device on the network page, it is also necessary to enter an already entered number of ports again.<br>
									 If the number of ports is missing for a device that has already been created, the problem should be solved by editing the device and specifying the ports, the type and, if necessary, the manual port configuration.';
$help_lang['Cat_Service_700_head'] = 'What do the different colors in the colored bar mean?';
$help_lang['Cat_Service_700_text'] = 'There are 5 different color codes in total: <br>
									 <span style="background-color:lightgray;">&nbsp;&nbsp;&nbsp;</span> - no scan available yet<br>
									 <span class="bg-green">&nbsp;&nbsp;&nbsp;</span> - HTTP status code 2xx<br>
									 <span class="bg-yellow">&nbsp;&nbsp;&nbsp;</span> - HTTP status code 3xx-4xx<br>
									 <span class="bg-orange-custom">&nbsp;&nbsp;&nbsp;</span> - HTTP status code 5xx<br>
									 <span class="bg-red">&nbsp;&nbsp;&nbsp;</span> - offline';
$help_lang['Cat_Service_701_head'] = 'What are the HTTP status codes?';
// from json
$help_lang['Cat_Service_702_head'] = 'What changes are reported?';
$help_lang['Cat_Service_702_text'] = 'Detectable events are:<br>
										<ul>
											<li>Changing the HTTP status code</li>
											<li>Change IP</li>
											<li>Response time of the server or the missing of the response.</li>
										</ul>
									 Depending on the choice of notification, either everything is reported, or only the absence of a server response.';
$help_lang['Cat_Service_703_head'] = 'General information about "Web Service Monitoring".';
$help_lang['Cat_Service_703_text'] = 'The monitoring is based exclusively on the responses of HTTP requests sent to the page. Depending on the state of the server, meaningful error patterns can be detected here. If the server does not respond at all, this is considered as "Down/Offline". These web server requests are performed every 10 min as part of the normal scan.';
$help_lang['Cat_ServiceDetails_750_head'] = 'I cannot edit all the fields.';
$help_lang['Cat_ServiceDetails_750_text'] = 'Not every field that is displayed on this page can be edited. Editable fields are:
											<ul>
												<li>' . $pia_lang['WebServices_label_Tags'] . '</li>
												<li>' . $pia_lang['WebServices_label_MAC'] . ' (possibly a device to which this web service is assigned)<br>
													A MAC address is expected here. If something else (e.g. "laptop") is entered here, "' . $pia_lang['WebServices_unknown_Device'] . ' (laptop)" appears in the overview..
													Services without this entry are listed under "' . $pia_lang['WebServices_BoxTitle_General'] . '".</li>
												<li>CheckBox: ' . $pia_lang['WebServices_Events_all'] . '</li>
												<li>CheckBox: ' . $pia_lang['WebServices_Events_down'] . '</li>
											</ul>';

?>
