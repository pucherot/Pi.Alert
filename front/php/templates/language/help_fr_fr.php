<?php
unset($help_lang);

$help_lang['Title'] = 'Aide / FAQ';
$help_lang['Cat_General'] = 'Généralités';
$help_lang['Cat_Detail'] = 'Vue détaillée';
$help_lang['Cat_General_100_head'] = 'L&apos;horloge en haut à droite et les heures des événements/présences ne sont pas correctes (décalage horaire).';
$help_lang['Cat_General_100_text_a'] = 'Fuseau horaire actuellement configuré :';
$help_lang['Cat_General_100_text_b'] = '<br>Si ce n&apos;est pas le fuseau horaire dans lequel tu te trouves, tu dois adapter le fuseau horaire dans le fichier de configuration PHP. Tu le trouveras dans le répertoire :';
$help_lang['Cat_General_100_text_c'] = 'Cherche dans ce fichier l&apos;entrée "date.timezone", supprime le cas échéant le " ;" de tête et saisis le fuseau horaire souhaité. Tu trouveras une liste des fuseaux horaires supportés ici. (<a href="https://www.php.net/manual/fr/timezones.php" target="blank">Link</a>)';
$help_lang['Cat_General_101_head'] = 'Mon réseau semble ralentir, le streaming "saccade".';
$help_lang['Cat_General_101_text'] = 'Il est tout à fait possible que les appareils peu performants atteignent leurs limites de performance avec la manière dont Pi.Alert détecte les nouveaux appareils sur le réseau. Cela est encore plus vrai si ces appareils communiquent avec le réseau par WLAN. La solution serait alors de passer à une connexion câblée si possible ou, si l&apos;appareil ne doit être utilisé que pendant une période limitée, de mettre en pause le scan arp sur la page de maintenance.';
$help_lang['Cat_General_102_head'] = 'Je reçois un message indiquant que la base de données est protégée en écriture (read only).';
$help_lang['Cat_General_102_text'] = 'Des modifications sont peut-être en cours d&apos;écriture dans la base de données par le backend. Veuillez réessayer après une courte attente. Si le comportement ne change pas, suivez les instructions ci-dessous.<br><br>
									 Vérifie dans le répertoire Pi.Alert si le dossier de la base de données (db) s&apos;est vu attribuer les bons droits :<br>
      								 <span class="text-maroon help_faq_code">drwxrwxr-x  2 (nombre de usuario) www-data</span><br>
      								 Si l&apos;autorisation n&apos;est pas correcte, tu peux la rétablir avec les commandes suivantes dans le terminal ou la console :<br>
      								 <div class="help_faq_code" style="padding-left: 10px; margin-bottom: 10px;">
      								 sudo chgrp -R www-data ~/pialert/db<br>
      								 sudo chown [Username]:www-data ~/pialert/db/pialert.db<br>
        							 chmod -R 775 ~/pialert/db
      								 </div>
									 Une autre option consiste à réinitialiser les autorisations nécessaires dans le répertoire <span class="text-maroon help_faq_code">~/pialert/back</span> en utilisant <span class="text-maroon help_faq_code">pialert-cli</span>. Plusieurs options s&apos;offrent à vous.<br><br>
									 <span class="text-maroon help_faq_code">./pialert-cli set_permissions</span><br>
									 Cette commande réinitialise uniquement les autorisations de groupe, laissant le propriétaire du fichier inchangé.<br><br>
									 <span class "text-maroon help_faq_code">./pialert-cli set_permissions --lxc</span><br>
									 Cette option supplémentaire est introduite pour une utilisation dans un conteneur LXC. Elle modifie le groupe conformément à la fonction de base et défini l&apos;utilisateur "root" comme propriétaire. Cette option n&apos;est pas pertinente en dehors d&apos;un environnement LXC.<br><br>
									 <span class="text-maroon help_faq_code">./pialert-cli set_permissions --homedir</span><br>
									 Cette option devrait être privilégiée. Ici, le nom d&apos;utilisateur est déterminé en fonction du répertoire parent du dossier d&apos;installation de Pi.Alert. Ce nom d&apos;utilisateur devient le propriétaire des fichiers. Le groupe est défini conformément à la fonction de base.';
$help_lang['Cat_General_103_head'] = 'La page de connexion n&apos;apparaît pas, même après la modification du mot de passe.';
$help_lang['Cat_General_103_text'] = 'Outre le mot de passe, le paramètre <span class="text-maroon help_faq_code">PIALERT_WEB_PROTECTION = True</span> doit également être défini dans le fichier de configuration <span class="text-maroon help_faq_code">~/pialert/config/pialert.conf</span>.';
$help_lang['Cat_General_104_head'] = 'Notes sur la migration de pucherot vers ce fork.';
$help_lang['Cat_General_104_text'] = 'La base de données de ce fork a été étendue de quelques champs. Pour reprendre la base de données du Pi.Alert original (pucherot), il existe une possibilité de mise à niveau avec le script pialert-cli dans le répertoire ~/pialert/back. La commande est alors ./pialert-cli update_db';
$help_lang['Cat_General_105_head'] = 'Explications pour "pialert-cli"';
$help_lang['Cat_General_105_text'] = 'The command line tool <span class="text-maroon help_faq_code">pialert-cli</span> is located in the directory <span class="text-maroon help_faq_code">~/pialert/back</span> and offers the possibility to make settings to Pi.Alert
                                     without web page or change to the configuration file. With the command <span class="text-maroon help_faq_code">./pialert-cli help</span> a list with the supported options can be called.
									 <table class="help_table_gen">
									    <tr><td class="help_table_gen_a">set_login</td>
									        <td class="help_table_gen_b">- Définit le paramètre PIALERT_WEB_PROTECTION dans le fichier de configuration sur TRUE<br>
									            - Si le paramètre n&apos;est pas présent, il sera créé. De plus, le mot de passe par défaut "123456" est défini.<br>&nbsp;</td></tr>
									    <tr><td class="help_table_gen_a">unset_login</td>
									        <td class="help_table_gen_b">- Définit le paramètre PIALERT_WEB_PROTECTION dans le fichier de configuration sur FALSE<br>
									            - Si le paramètre n&apos;est pas présent, il sera créé. De plus, le mot de passe par défaut "123456" est défini.<br>&nbsp;</td></tr>
									    <tr><td class="help_table_gen_a">set_password &lt;password&gt;</td>
									        <td class="help_table_gen_b">- Définit le nouveau mot de passe sous forme de valeur hachée.<br>
									            - Si le paramètre PIALERT_WEB_PROTECTION n&apos;existe pas encore, il sera créé et défini sur "TRUE" (connexion activée)<br>&nbsp;</td></tr>
									    <tr><td class="help_table_gen_a">set_autopassword</td>
									        <td class="help_table_gen_b">- Définit un nouveau mot de passe aléatoire sous forme de valeur hachée et l&apos;affiche en texte brut dans la console.<br>
									            - Si le paramètre PIALERT_WEB_PROTECTION n&apos;existe pas encore, il sera créé et défini sur "TRUE" (connexion activée)<br>&nbsp;</td></tr>
									    <tr><td class="help_table_gen_a">disable_scan &lt;MIN&gt;</td>
									        <td class="help_table_gen_b">- Arrête toutes les numérisations en cours.<br>
									            - Empêche le démarrage de nouvelles numérisations.<br>&nbsp;</td></tr>
									    <tr><td class="help_table_gen_a">enable_scan</td>
									        <td class="help_table_gen_b">- Permet le démarrage de nouvelles numérisations.<br>&nbsp;</td></tr>
										<tr><td class="help_table_gen_a">disable_mainscan</td>
										    <td class="help_table_gen_b">- Désactive la méthode principale de numérisation pour Pi.Alert (numérisation ARP)</td></tr>
										<tr><td class="help_table_gen_a">enable_mainscan</td>
										    <td class="help_table_gen_b">- Active la méthode principale de numérisation pour Pi.Alert (numérisation ARP)</td></tr>
									    <tr><td class="help_table_gen_a">enable_service_mon</td>
									        <td class="help_table_gen_b">- Active la surveillance des services Web.<br>&nbsp;</td></tr>
									    <tr><td class="help_table_gen_a">disable_service_mon</td>
									        <td class="help_table_gen_b">- Désactive la surveillance des services Web.<br>&nbsp;</td></tr>
									    <tr><td class="help_table_gen_a">enable_icmp_mon</td>
									        <td class="help_table_gen_b">- Enable ICMP Monitoring (ping)<br>&nbsp;</td></tr>
									    <tr><td class="help_table_gen_a">disable_icmp_mon</td>
									        <td class="help_table_gen_b">- Disable ICMP Monitoring (ping)<br>&nbsp;</td></tr>
									    <tr><td class="help_table_gen_a">update_db</td>
									        <td class="help_table_gen_b">- Le script tente de rendre la base de données compatible avec cette version.<br>&nbsp;</td></tr>
									    <tr><td class="help_table_gen_a">set_apikey</td>
									        <td class="help_table_gen_b">- Avec la clé API, il est possible de faire des requêtes à la base de données sans utiliser la page web. Si une clé API existe déjà, elle sera remplacée.<br>&nbsp;</td></tr>
									    <tr><td class="help_table_gen_a">set_permissions</td>
											<td class="help_table_gen_b">- Répare les autorisations de fichier de la base de données pour le groupe. Si les autorisations doivent également être réinitialisées pour l&apos;utilisateur, une option supplémentaire est nécessaire:<br>
											<span class="text-maroon" style="display:inline-block;width:130px;">--lxc</span> définit "root" comme nom d&apos;utilisateur<br>
											<span class="text-maroon" style="display:inline-block;width:130px;">--custom</span> définit un nom d&apos;utilisateur personnalisé<br>
											<span class="text-maroon" style="display:inline-block;width:130px;">--homedir</span> prend le nom d&apos;utilisateur à partir du répertoire de la maison</td></tr>
									    <tr><td class="help_table_gen_a">reporting_test</td>
									        <td class="help_table_gen_b">- Teste les notifications pour tous les services activés.<br>&nbsp;</td></tr>
									    <tr><td class="help_table_gen_a">set_sudoers</td>
									        <td class="help_table_gen_b">- Create sudoer file for www-data and Pi.Alert user</td></tr>
									    <tr><td class="help_table_gen_a">unset_sudoers</td>
									        <td class="help_table_gen_b">- Delete sudoer file for www-data and Pi.Alert user</td></tr>
									</table>';
$help_lang['Cat_General_106_head'] = 'Comment puis-je effectuer une vérification d&apos;intégrité de la base de données ?';
$help_lang['Cat_General_106_text'] = 'Si vous souhaitez vérifier la base de données actuellement en cours d&apos;utilisation, arrêtez Pi.Alert pendant environ 1 heure pour éviter tout accès en écriture à la base de données pendant la vérification. De plus, l&apos;interface Web ne doit pas être ouverte pour d&apos;autres opérations d&apos;écriture pendant la vérification.
									 Maintenant, ouvrez la console dans le répertoire <span class="text-maroon help_faq_code">~/pialert/db</span> et utilisez la commande <span class="text-maroon help_faq_code">ls</span> pour répertorier le contenu du répertoire. Si les fichiers
									 <span class="text-maroon help_faq_code">pialert.db-shm</span> et <span class="text-maroon help_faq_code">pialert.db-wal</span> apparaissent dans la liste (avec la même horodatage que le fichier "pialert.db"), cela signifie qu&apos;il existe encore des transactions de base de données ouvertes. Dans ce cas, attendez simplement un moment, et pour vérifier, exécutez à nouveau la commande <span class="text-maroon help_faq_code">ls</span>.
									 <br><br>
									 Une fois que ces fichiers ont disparu, la vérification peut être effectuée. Pour ce faire, exécutez les commandes suivantes :<br>
									 <div class="help_faq_code" style="padding-left: 10px; margin-bottom: 10px;">
									    sqlite3 pialert.db "PRAGMA integrity_check"<br>
									    sqlite3 pialert.db "PRAGMA foreign_key_check"
									 </div><br>
									 Dans les deux cas, aucun erreur ne devrait être signalée. Après la vérification, vous pouvez redémarrer Pi.Alert.';
$help_lang['Cat_General_107_head'] = 'Explications pour le fichier "pialert.conf"';
$help_lang['Cat_General_107_text'] = 'Le fichier <span class="text-maroon help_faq_code">pialert.conf</span> se trouve dans le répertoire <span class="text-maroon help_faq_code">~/pialert/config</span>.
									 Dans ce fichier de configuration, de nombreuses fonctions de Pi.Alert peuvent être configurées selon les préférences personnelles. Comme les possibilités sont variées, je souhaiterais donner une
									 brève explication des points individuels.
									 <table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">General Settings</td></tr>
									    <tr><td class="help_table_gen_a">PIALERT_PATH</td>
									        <td class="help_table_gen_b">Cette variable est définie lors de l&apos;installation et ne doit pas être modifiée.</td></tr>
									    <tr><td class="help_table_gen_a">DB_PATH</td>
									        <td class="help_table_gen_b">Cette variable est définie lors de l&apos;installation et ne doit pas être modifiée.</td></tr>
									    <tr><td class="help_table_gen_a">LOG_PATH</td>
									        <td class="help_table_gen_b">Cette variable est définie lors de l&apos;installation et ne doit pas être modifiée.</td></tr>
									    <tr><td class="help_table_gen_a">PRINT_LOG</td>
									        <td class="help_table_gen_b">Si cette entrée est définie sur <span class="text-maroon help_faq_code">True</span>, des horodatages supplémentaires pour les sous-fonctions individuelles sont ajoutés au journal de balayage. Par défaut, cette entrée est définie sur <span class="text-maroon help_faq_code">False</span></td></tr>
									    <tr><td class="help_table_gen_a">VENDORS_DB</td>
									        <td class="help_table_gen_b">Cette variable est définie lors de l&apos;installation et ne doit pas être modifiée.</td></tr>
									    <tr><td class="help_table_gen_a">PIALERT_APIKEY</td>
									        <td class="help_table_gen_b">Avec la clé API, il est possible d&apos;effectuer des requêtes à la base de données sans utiliser la page web. La clé API est une chaîne aléatoire qui peut être définie via les paramètres ou via <span class="text-maroon help_faq_code">pialert-cli</span></td></tr>
									    <tr><td class="help_table_gen_a">PIALERT_WEB_PROTECTION</td>
									        <td class="help_table_gen_b">Active ou désactive la protection par mot de passe de l&apos;interface web de Pi.Alert.</td></tr>
									    <tr><td class="help_table_gen_a">PIALERT_WEB_PASSWORD</td>
									        <td class="help_table_gen_b">Ce champ contient le mot de passe haché pour l&apos;interface web. Le mot de passe ne peut pas être saisi ici en texte brut, mais doit être défini avec <span class="text-maroon help_faq_code">pialert-cli</span></td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Other Modules</td></tr>
									    <tr><td class="help_table_gen_a">SCAN_WEBSERVICES</td>
									        <td class="help_table_gen_b">Ici, la fonction de surveillance des services web peut être activée (<span class="text-maroon help_faq_code">True</span>) ou désactivée (<span class="text-maroon help_faq_code">False</span>)</td></tr>
									    <tr><td class="help_table_gen_a">ICMPSCAN_ACTIVE</td>
									        <td class="help_table_gen_b">ICMP Monitoring on/off</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Special Protocol Scanning</td></tr>
									    <tr><td class="help_table_gen_a">SCAN_ROGUE_DHCP</td>
									        <td class="help_table_gen_b">Active la recherche de serveurs DHCP étrangers, également appelés "rogue". Cette fonction est utilisée pour détecter la présence d&apos;un serveur DHCP étranger dans le réseau qui pourrait prendre le contrôle de la gestion des adresses IP.</td></tr>
									    <tr><td class="help_table_gen_a">DHCP_SERVER_ADDRESS</td>
									        <td class="help_table_gen_b">L&apos;adresse IP du serveur DHCP connu est stockée ici.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Mail-Account Settings</td></tr>
									    <tr><td class="help_table_gen_a">SMTP_SERVER</td>
									        <td class="help_table_gen_b">Adresse du serveur de messagerie (par exemple, smtp.gmail.com)</td></tr>
									    <tr><td class="help_table_gen_a">SMTP_PORT</td>
									        <td class="help_table_gen_b">Le port du serveur SMTP. Le port peut varier en fonction de la configuration du serveur.</td></tr>
									    <tr><td class="help_table_gen_a">SMTP_USER</td>
									        <td class="help_table_gen_b">Nom d&apos;utilisateur</td></tr>
									    <tr><td class="help_table_gen_a">SMTP_PASS</td>
									        <td class="help_table_gen_b">Mot de passe</td></tr>
									    <tr><td class="help_table_gen_a">SMTP_SKIP_TLS</td>
									        <td class="help_table_gen_b">Si cette entrée est définie sur <span class="text-maroon help_faq_code">True</span>, le chiffrement de transport de l&apos;e-mail est activé. Si le serveur ne prend pas en charge cela, l&apos;entrée doit être définie sur <span class="text-maroon help_faq_code">False</span>.</td></tr>
									    <tr><td class="help_table_gen_a">SMTP_SKIP_LOGIN</td>
									        <td class="help_table_gen_b">Il existe des serveurs SMTP qui ne nécessitent pas de connexion. Dans ce cas, cette valeur peut être définie sur <span class="text-maroon help_faq_code">True</span>.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">WebGUI Reporting</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_WEBGUI</td>
									        <td class="help_table_gen_b">Active/désactive les notifications concernant les changements dans le réseau dans l&apos;interface web.</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_WEBGUI_WEBMON</td>
									        <td class="help_table_gen_b">Active/désactive les notifications concernant les changements dans les services web surveillés dans l&apos;interface web.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Mail Reporting</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_MAIL</td>
									        <td class="help_table_gen_b">Active/désactive les notifications concernant les changements dans le réseau via e-mail.</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_MAIL_WEBMON</td>
									        <td class="help_table_gen_b">Active/désactive les notifications concernant les changements dans les services web surveillés par e-mail.</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_FROM</td>
									        <td class="help_table_gen_b">Nom ou identifiant de l&apos;expéditeur.</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_TO</td>
									        <td class="help_table_gen_b">Adresse e-mail à laquelle la notification doit être envoyée.</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_DEVICE_URL</td>
									        <td class="help_table_gen_b">URL de l&apos;installation de Pi.Alert pour créer un lien cliquable dans l&apos;e-mail vers le périphérique détecté.</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_DASHBOARD_URL</td>
									        <td class="help_table_gen_b">URL de l&apos;installation de Pi.Alert, pour pouvoir créer un lien cliquable dans l&apos;e-mail.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Pushsafer</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_PUSHSAFER</td>
									        <td class="help_table_gen_b">Active/désactive les notifications concernant les changements dans le réseau via Pushsafer.</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_PUSHSAFER_WEBMON</td>
									        <td class="help_table_gen_b">Active/désactive les notifications concernant les changements dans les services web surveillés via Pushsafer.</td></tr>
									    <tr><td class="help_table_gen_a">PUSHSAFER_TOKEN</td>
									        <td class="help_table_gen_b">Il s&apos;agit de la clé privée qui peut être consultée sur la page Pushsafer.</td></tr>
									    <tr><td class="help_table_gen_a">PUSHSAFER_DEVICE</td>
									        <td class="help_table_gen_b">L&apos;ID du périphérique vers lequel le message sera envoyé. &lsquo;<span class="text-maroon help_faq_code">a</span>&rsquo; signifie que le message sera envoyé à tous les périphériques configurés et consommera un nombre correspondant d&apos;appels API.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Pushover</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_PUSHOVER</td>
									        <td class="help_table_gen_b">Active/désactive les notifications concernant les changements dans le réseau via Pushover.</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_PUSHOVER_WEBMON</td>
									        <td class="help_table_gen_b">Active/désactive les notifications concernant les changements dans les services web surveillés via Pushover.</td></tr>
									    <tr><td class="help_table_gen_a">PUSHOVER_TOKEN</td>
									        <td class="help_table_gen_b">Aussi appelé "APP TOKEN" ou "API TOKEN". Ce jeton peut être consulté sur la page Pushover.</td></tr>
									    <tr><td class="help_table_gen_a">PUSHOVER_USER</td>
									        <td class="help_table_gen_b">Aussi appelé "USER KEY". Cette clé s&apos;affiche immédiatement après la connexion sur la page d&apos;accueil.</td></tr>
									</table>
									<table class="help_table_gen">
			    						<tr><td class="help_table_gen_section" colspan="2">NTFY</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_NTFY</td>
									        <td class="help_table_gen_b">Active/désactive les notifications concernant les changements dans le réseau via NTFY.</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_NTFY_WEBMON</td>
									        <td class="help_table_gen_b">Active/désactive les notifications concernant les changements dans les services web surveillés via NTFY.</td></tr>
									    <tr><td class="help_table_gen_a">NTFY_HOST</td>
									        <td class="help_table_gen_b">    </td></tr>
									    <tr><td class="help_table_gen_a">NTFY_TOPIC</td>
									        <td class="help_table_gen_b">    </td></tr>
									    <tr><td class="help_table_gen_a">NTFY_USER</td>
									        <td class="help_table_gen_b">    </td></tr>
									    <tr><td class="help_table_gen_a">NTFY_PASSWORD</td>
									        <td class="help_table_gen_b">    </td></tr>
									    <tr><td class="help_table_gen_a">NTFY_PRIORITY</td>
									        <td class="help_table_gen_b">    </td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Shoutrrr</td></tr>
									    <tr><td class="help_table_gen_a">SHOUTRRR_BINARY</td>
									        <td class="help_table_gen_b">Ici, vous devez configurer le binaire de shoutrrr à utiliser. Cela dépend du matériel sur lequel Pi.Alert a été installé.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Telegram via Shoutrrr</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_TELEGRAM</td>
									        <td class="help_table_gen_b">Active/désactive les notifications concernant les changements dans le réseau via Telegram</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_TELEGRAM_WEBMON</td>
									        <td class="help_table_gen_b">Active/désactive les notifications concernant les changements dans les services web surveillés via Telegram</td></tr>
									    <tr><td class="help_table_gen_a">TELEGRAM_BOT_TOKEN_URL</td>
									        <td class="help_table_gen_b">    </td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">DynDNS and IP</td></tr>
									    <tr><td class="help_table_gen_a">QUERY_MYIP_SERVER</td>
									        <td class="help_table_gen_b">    </td></tr>
									    <tr><td class="help_table_gen_a">DDNS_ACTIVE</td>
									        <td class="help_table_gen_b">Active/désactive le service DDNS configuré dans Pi.Alert. DDNS, également connu sous le nom de DynDNS, vous permet de mettre à jour un nom de domaine avec une adresse IP qui change régulièrement. Ce service est proposé par plusieurs fournisseurs de services.</td></tr>
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
									            Cette (ces) adresse(s) MAC (à mémoriser avec des petites lettres) sera(ont) filtrée(s) à partir des résultats du scan.</td></tr>
									    <tr><td class="help_table_gen_a">SCAN_SUBNETS</td>
									        <td class="help_table_gen_b">
									        	&lsquo;<span class="text-maroon help_faq_code">--localnet</span>&rsquo;<br>
									        	Normalement, cette option est déjà paramétrée correctement. Ce paramètre est sélectionné lorsque Pi.Alert est installé sur un appareil avec une carte réseau et qu&apos;aucun autre réseau n&apos;est configuré.<br><br>
									        	&apos;<span class="text-maroon help_faq_code">--localnet --interface=eth0</span>&apos;<br>
									        	Cette configuration est sélectionnée si Pi.Alert est installé sur un système avec au moins 2 cartes réseau et un réseau configuré. Cependant, la désignation de l&apos;interface peut varier et doit être adaptée aux conditions du système.<br><br>
									        	<span class="text-maroon help_faq_code">[&apos;192.168.1.0/24 --interface=eth0&apos;,&apos;192.168.2.0/24 --interface=eth1&apos;]</span><br>
									        	La dernière configuration est nécessaire si plusieurs réseaux doivent être surveillés. Pour chaque réseau à surveiller, une carte réseau correspondante doit être configurée. Cela est nécessaire car le "arp-scan" utilisé n&apos;est pas routé, c&apos;est-à-dire qu&apos;il fonctionne uniquement dans son propre sous-réseau. Chaque interface est saisie ici avec le réseau correspondant. La désignation de l&apos;interface doit être adaptée aux conditions du système.
									        </td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Options de Surveillance ICMP</td></tr>
									    <tr><td class="help_table_gen_a">ICMP_ONLINE_TEST</td>
									        <td class="help_table_gen_b">Nombre d&apos;essais pour déterminer si un appareil est en ligne (Par défaut 1).</td></tr>
									    <tr><td class="help_table_gen_a">ICMP_GET_AVG_RTT</td>
									        <td class="help_table_gen_b">Nombre de "ping&apos;s" pour calculer le temps de réponse moyen (Par défaut 2).</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Pi-hole Configuration</td></tr>
									    <tr><td class="help_table_gen_a">PIHOLE_ACTIVE</td>
									        <td class="help_table_gen_b">Cette variable est définie lors de l&apos;installation.</td></tr>
									    <tr><td class="help_table_gen_a">PIHOLE_DB</td>
									        <td class="help_table_gen_b">Cette variable est définie lors de l&apos;installation et ne doit pas être modifiée.</td></tr>
									    <tr><td class="help_table_gen_a">DHCP_ACTIVE</td>
									        <td class="help_table_gen_b">Cette variable est définie lors de l&apos;installation.</td></tr>
									    <tr><td class="help_table_gen_a">DHCP_LEASES</td>
									        <td class="help_table_gen_b">Cette variable est définie lors de l&apos;installation et ne doit pas être modifiée.</td></tr>
									</table>
									<table class="help_table_gen">
			    						<tr><td class="help_table_gen_section" colspan="2">Fritzbox Configuration</td></tr>
									    <tr><td class="help_table_gen_a">FRITZBOX_ACTIVE</td>
									        <td class="help_table_gen_b">Si une Fritzbox est utilisée dans le réseau, elle peut être utilisée comme source de données. Cela peut être activé ou désactivé à ce niveau.</td></tr>
									    <tr><td class="help_table_gen_a">FRITZBOX_IP</td>
									        <td class="help_table_gen_b">Adresse IP de la Fritzbox.</td></tr>
									    <tr><td class="help_table_gen_a">FRITZBOX_USER</td>
									        <td class="help_table_gen_b">Nom d&apos;utilisateur<br>Cela suppose que la Fritzbox est configurée pour une connexion avec nom d&apos;utilisateur et mot de passe, au lieu de mot de passe seul. Une connexion avec mot de passe seul n&apos;est pas prise en charge.</td></tr>
									    <tr><td class="help_table_gen_a">FRITZBOX_PASS</td>
									        <td class="help_table_gen_b">Mot de passe</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Configuration Mikrotik</td></tr>
									    <tr><td class="help_table_gen_a">MIKROTIK_ACTIVE</td>
									        <td class="help_table_gen_b">Si un routeur Mikrotik est utilisé dans le réseau, il peut être utilisé comme source de données. Cela peut être activé ou désactivé à ce stade.</td></tr>
									    <tr><td class="help_table_gen_a">MIKROTIK_IP</td>
									        <td class="help_table_gen_b">Adresse IP du routeur Mikrotik.</td></tr>
									    <tr><td class="help_table_gen_a">MIKROTIK_USER</td>
									        <td class="help_table_gen_b">Nom d&apos;utilisateur</td></tr>
									    <tr><td class="help_table_gen_a">MIKROTIK_PASS</td>
									        <td class="help_table_gen_b">Mot de passe</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Configuration UniFi</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_ACTIVE</td>
									        <td class="help_table_gen_b">Si un système UniFi est utilisé dans le réseau, il peut être utilisé comme source de données. Cela peut être activé ou désactivé à ce stade.</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_IP</td>
									        <td class="help_table_gen_b">Adresse IP du système Unifi.</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_API</td>
									        <td class="help_table_gen_b">Possible UNIFI APIs are v4, v5, unifiOS, UDMP-unifiOS</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_USER</td>
									        <td class="help_table_gen_b">Nom d&apos;utilisateur</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_PASS</td>
									        <td class="help_table_gen_b">Mot de passe</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Maintenance Tasks Cron</td></tr>
									    <tr><td class="help_table_gen_a">DAYS_TO_KEEP_ONLINEHISTORY</td>
									        <td class="help_table_gen_b">Nombre de jours pendant lesquels l&apos;historique en ligne (graphique d&apos;activité) doit être stocké dans la base de données. Un jour génère 288 enregistrements de ce type.</td></tr>
									    <tr><td class="help_table_gen_a">DAYS_TO_KEEP_EVENTS</td>
									        <td class="help_table_gen_b">Nombre de jours pendant lesquels les événements des appareils individuels doivent être stockés.</td></tr>
									</table>';
$help_lang['Cat_General_108_head'] = 'Une mise à jour est disponible. Que dois-je faire si je veux mettre à jour Pi.Alert ?';
$help_lang['Cat_General_108_text'] = '<ol>
										<li>Vérifiez dans la boîte d&apos;état sur la page des paramètres qu&apos;aucune analyse n&apos;est en cours.</li>
										<li>Plus bas, dans la section sécurité, arrêtez Pi.Alert pendant 15 minutes. Cela empêche la modification de la base de données pendant la mise à jour.</li>
										<li>Passez maintenant au terminal de l&apos;appareil où Pi.Alert est installé.</li>
										<li>Exécutez la commande :<br>
											<input id="bashupdatecommand" readonly value="bash -c &quot;$(wget -qLO - https://github.com/leiweibau/Pi.Alert/raw/main/install/pialert_update.sh)&quot;" style="width:100%; overflow-x: scroll; border: none; background: transparent; margin: 0px; padding: 0px;"></li>
										<li>Suivez les instructions.</li>
										<li>Après une mise à jour réussie, Pi.Alert devrait redémarrer automatiquement. Vous pouvez également le redémarrer manuellement sur la page des paramètres.</li>
									</ol>';
$help_lang['Cat_Device_200_head'] = 'I have devices in my list that I do not know about. After deleting them, they always reappear.';
$help_lang['Cat_Device_200_text'] = 'If you use Pi-hole, please note that Pi.Alert retrieves information from Pi-hole. Pause Pi.Alert, go to the settings page in Pi-hole and
 									delete the DHCP lease if necessary. Then, also in Pi-hole, look under Tools -> Network to see if you can find the recurring hosts there.
 									If yes, delete them there as well. Now you can start Pi.Alert again. Now the device(s) should not show up anymore. Restarting the <span class="text-maroon help_faq_code">pihole-FTL</span> service may also fix the problem.
 									Si un tel appareil continue à apparaître de manière répétée, vous pouvez ajouter l&apos;adresse MAC à la liste d&apos;ignorés <span class="text-maroon help_faq_code">MAC_IGNORE_LIST</span> dans le fichier <span class="text-maroon help_faq_code">pialert.conf</span>.';
$help_lang['Cat_Detail_300_head'] = 'Que signifie ';
$help_lang['Cat_Detail_300_text_a'] = 'signifie un appareil réseau créé à partir de la page réseau.';
$help_lang['Cat_Detail_300_text_b'] = 'désigne le numéro de port auquel l&apos;appareil actuellement modifié est connecté à cet appareil réseau.';
$help_lang['Cat_Detail_301_head_a'] = 'Quand se fait la numérisation maintenant ? À ';
$help_lang['Cat_Detail_301_head_b'] = ' indique 1 minute mais le graphique montre des intervalles de 5 minutes.';
$help_lang['Cat_Detail_301_text'] = 'L&apos;intervalle de temps entre les numérisations est défini par le "Cronjob", qui est réglé par défaut sur 5 minutes. La mention "1 minute" fait référence à la durée prévue de la numérisation.
									En fonction de la configuration du réseau, cette durée peut varier. Pour modifier le cronjob, vous pouvez utiliser la commande suivante dans le terminal/console <span class="text-maroon help_faq_code">crontab -e</span>
									et modifier l&apos;intervalle.';
$help_lang['Cat_Detail_302_head_a'] = 'What means ';
$help_lang['Cat_Detail_302_head_b'] = 'and why can&apos;t I select that?';
$help_lang['Cat_Detail_302_text'] = 'Some modern devices generate random MAC addresses for privacy reasons, which can no longer be associated with any manufacturer and which change again with each new connection.
									Pi.Alert detects if it is such a random MAC address and activates this "field" automatically. To disable this behavior you have to look in your device how to disable
									MAC address randomization.';
$help_lang['Cat_Detail_303_head'] = 'What is Nmap and what is it for?';
$help_lang['Cat_Detail_303_text'] = 'Nmap is a network scanner with multiple capabilities.<br>
									When a new device appears in your list, you have the possibility to get more detailed information about the device via the Nmap scan.';
$help_lang['Cat_Presence_400_head'] = 'Devices are displayed with a yellow marker and the note "missing event".';
$help_lang['Cat_Presence_400_text'] = 'If this happens, you have the option to delete the events on the device in question (details view). Another possibility would be to switch on the device and wait until Pi.Alert detects the device as "online" with the next
									  scan and then simply turn the device off again. Now Pi.Alert should properly note the state of the device in the database with the next scan.';
$help_lang['Cat_Presence_401_head'] = 'A device is displayed as present although it is "Offline".';
$help_lang['Cat_Presence_401_text'] = 'If this happens, you have the possibility to delete the events for the device in question (details view). Another possibility would be to switch on the device and wait until Pi.Alert recognizes the device as "online" with the next scan
									  and then simply switch the device off again. Now Pi.Alert should properly note the state of the device in the database with the next scan.';
$help_lang['Cat_Network_600_head'] = 'What is this page for?';
$help_lang['Cat_Network_600_text'] = 'This page should offer you the possibility to map the assignment of your network devices. For this purpose, you can create one or more switches, WLANs, routers, etc., provide them with a port number if necessary and assign already detected
									 devices to them. This assignment is done in the detailed view of the device to be assigned. So it is possible for you to quickly determine to which port a host is connected and if it is online. It is possible to assign a device to multiple
									 ports (port bundling), as well as multiple devices to one port (virtual machines).';
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
