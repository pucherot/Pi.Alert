<?php
unset($help_lang);

$help_lang['Title'] = 'Aiuto / FAQ';
$help_lang['Cat_General'] = 'Generale';
$help_lang['Cat_Detail'] = 'Dettaglio';
$help_lang['Cat_General_100_head'] = 'L&apos;orologio in alto a destra e gli orari degli eventi/presenze non coincidono (fuso orario).';
$help_lang['Cat_General_100_text_a'] = 'Fuso orario attualmente configurato:';
$help_lang['Cat_General_100_text_b'] = '<br>Se questo non è il fuso orario in cui ti trovi, dovresti modificare il fuso orario nel file di configurazione PHP. Lo troverai nella directory:';
$help_lang['Cat_General_100_text_c'] = 'Cerca in questo file l&apos;entrata "<span class="text-maroon help_faq_code">date.timezone</span>", rimuovi eventualmente il ";" iniziale e inserisci il fuso orario desiderato. Puoi trovare un elenco dei fusi orari supportati qui (<a href="https://www.php.net/manual/de/timezones.php" target="blank">Link</a>).';
$help_lang['Cat_General_101_head'] = 'La mia rete sembra rallentare, lo streaming "scatta".';
$help_lang['Cat_General_101_text'] = 'Potrebbe succedere che dispositivi poco potenti raggiungano i propri limiti di prestazione con il modo in cui Pi.Alert rileva i nuovi dispositivi nella rete. Questo problema si accentua ulteriormente se tali dispositivi comunicano tramite WLAN con la rete. Le soluzioni possibili includono il passaggio a una connessione cablata, se possibile, o la sospensione dell&apos;arp-scan nella pagina di manutenzione se si desidera utilizzare il dispositivo solo per un breve periodo.';
$help_lang['Cat_General_102_head'] = 'Ricevo un messaggio che il database è in modalità sola lettura (read-only).';
$help_lang['Cat_General_102_text'] = 'Al momento potrebbero essere in corso modifiche al database da parte del backend. Riprova dopo una breve attesa. Se il comportamento non cambia, segui le istruzioni di seguito.<br><br>
									 Verifica nella directory di Pi.Alert se la cartella del database (db) ha i permessi corretti assegnati:<br>
      								 <span class="text-maroon help_faq_code">drwxrwxr-x  2 (tuo nome utente) www-data</span><br>
      								 Se i permessi non sono corretti, puoi reimpostarli con i seguenti comandi nel terminale o nella console:<br>
      								 <div class="help_faq_code" style="padding-left: 10px; margin-bottom: 10px;">
      								 sudo chgrp -R www-data ~/pialert/db<br>
      								 sudo chown [Username]:www-data ~/pialert/db/pialert.db<br>
        							 chmod -R 775 ~/pialert/db
      								 </div>
      								 Un&apos;altra opzione è ripristinare i permessi necessari nella directory <span class="text-maroon help_faq_code">~/pialert/back</span> utilizzando <span class="text-maroon help_faq_code">pialert-cli</span>. Ci sono diverse opzioni a disposizione.<br><br>
									 <span class="text-maroon help_faq_code">./pialert-cli set_permissions</span><br>
									 Questo comando ripristina solo i permessi del gruppo, lasciando inalterato il proprietario del file.<br><br>
									 <span class="text-maroon help_faq_code">./pialert-cli set_permissions --lxc</span><br>
									 Questa opzione aggiuntiva è stata introdotta per l&apos;uso all&apos;interno di un container LXC. Modifica il gruppo secondo la funzionalità di base e imposta l&apos;utente "root" come proprietario. Questa opzione non è rilevante al di fuori di un ambiente LXC.<br><br>
									 <span class="text-maroon help_faq_code">./pialert-cli set_permissions --homedir</span><br>
									 Questa opzione dovrebbe essere quella preferita. Qui, il nome utente viene determinato in base alla directory principale dell&apos;installazione di Pi.Alert. Questo nome utente diventa il proprietario dei file. Il gruppo è impostato secondo la funzionalità di base.';
$help_lang['Cat_General_103_head'] = 'La pagina di accesso non appare, nemmeno dopo aver cambiato la password.';
$help_lang['Cat_General_103_text'] = 'Oltre alla password, il parametro <span class="text-maroon help_faq_code">PIALERT_WEB_PROTECTION</span> nel file di configurazione <span class="text-maroon help_faq_code">~/pialert/config/pialert.conf</span>
      								 deve essere impostato su <span class="text-maroon help_faq_code">True</span>.';
$help_lang['Cat_General_104_head'] = 'Indicazioni per la migrazione da "pucherot/Pi.Alert" a questo fork.';
$help_lang['Cat_General_104_text'] = 'Il database in questo fork è stato esteso con alcuni campi aggiuntivi. Per migrare il database dall&apos;originale Pi.Alert <b>(pucherot)</b>, puoi utilizzare lo script <span class="text-maroon help_faq_code">pialert-cli</span> nella directory
									 <span class="text-maroon help_faq_code">~/pialert/back</span>. Il comando sarà <span class="text-maroon help_faq_code">./pialert-cli update_db</span>';
$help_lang['Cat_General_105_head'] = 'Spiegazioni per "pialert-cli"';
$help_lang['Cat_General_105_text'] = 'Lo strumento da riga di comando <span class="text-maroon help_faq_code">pialert-cli</span> si trova nella directory <span class="text-maroon help_faq_code">~/pialert/back</span> e offre la possibilità di modificare le impostazioni di Pi.Alert senza utilizzare il sito web o apportare modifiche manuali al file di configurazione.
                                     Puoi ottenere un elenco delle funzionalità supportate con il comando <span class="text-maroon help_faq_code">./pialert-cli help</span>.
									 <table class="help_table_gen">
									    <tr><td class="help_table_gen_a">set_login</td>
									        <td class="help_table_gen_b">- Imposta il parametro PIALERT_WEB_PROTECTION nel file di configurazione su TRUE<br>
									            - Se il parametro non esiste, verrà creato e verrà impostata la password predefinita "123456"</td></tr>
									    <tr><td class="help_table_gen_a">unset_login</td>
									        <td class="help_table_gen_b">- Imposta il parametro PIALERT_WEB_PROTECTION nel file di configurazione su FALSE<br>
									            - Se il parametro non esiste, verrà creato e verrà impostata la password predefinita "123456"</td></tr>
									    <tr><td class="help_table_gen_a">set_password &lt;password&gt;</td>
									        <td class="help_table_gen_b">- Imposta la nuova password come valore hash.<br>
									            - Se il parametro PIALERT_WEB_PROTECTION non esiste, verrà creato e verrà impostata la password predefinita "123456"</td></tr>
									    <tr><td class="help_table_gen_a">set_autopassword</td>
									        <td class="help_table_gen_b">- Imposta una nuova password casuale come valore hash e la visualizza in chiaro nella console.<br>
									            - Se il parametro PIALERT_WEB_PROTECTION non esiste, verrà creato e verrà impostata la password predefinita "123456"</td></tr>
									    <tr><td class="help_table_gen_a">disable_scan &lt;MIN&gt;</td>
									        <td class="help_table_gen_b">- Interrompe tutte le scansioni attive.<br>
									            - Impedisce l&apos;avvio di nuove scansioni.<br>- Puoi specificare un periodo di tempo in minuti. Se non specificato, Pi.Alert riprenderà la scansione dopo 10 minuti</td></tr>
									    <tr><td class="help_table_gen_a">enable_scan</td>
									        <td class="help_table_gen_b">- Abilita nuovamente l&apos;avvio di nuove scansioni</td></tr>
									    <tr><td class="help_table_gen_a">disable_mainscan</td>
									        <td class="help_table_gen_b">- Disabilita il metodo di scansione principale di Pi.Alert (arp-scan)</td></tr>
									    <tr><td class="help_table_gen_a">enable_mainscan</td>
									        <td class="help_table_gen_b">- Abilita il metodo di scansione principale di Pi.Alert (arp-scan)</td></tr>
									    <tr><td class="help_table_gen_a">disable_service_mon</td>
									        <td class="help_table_gen_b">- Disabilita il monitoraggio del servizio web</td></tr>
									    <tr><td class="help_table_gen_a">enable_service_mon</td>
									        <td class="help_table_gen_b">- Abilita il monitoraggio del servizio web</td></tr>
									    <tr><td class="help_table_gen_a">disable_icmp_mon</td>
									        <td class="help_table_gen_b">- Disabilita il monitoraggio ICMP (ping)</td></tr>
									    <tr><td class="help_table_gen_a">enable_icmp_mon</td>
									        <td class="help_table_gen_b">- Abilita il monitoraggio ICMP (ping)</td></tr>
									    <tr><td class="help_table_gen_a">update_db</td>
									        <td class="help_table_gen_b">- Crea i campi del database necessari per questo fork</td></tr>
									    <tr><td class="help_table_gen_a">set_apikey</td>
									        <td class="help_table_gen_b">- Consente di effettuare query al database senza utilizzare il sito web, utilizzando una chiave API. Se una chiave API esiste già, verrà sovrascritta</td></tr>
									    <tr><td class="help_table_gen_a">set_permissions</td>
											<td class="help_table_gen_b">- Ripara i permessi del file del database per il gruppo. Se è necessario ripristinare anche i permessi per l&apos;utente, è necessaria un&apos;opzione aggiuntiva:<br>
											<span class="text-maroon" style="display:inline-block;width:130px;">--lxc</span> imposta "root" come nome utente<br>
											<span class="text-maroon" style="display:inline-block;width:130px;">--custom</span> imposta un nome utente personalizzato<br>
											<span class="text-maroon" style="display:inline-block;width:130px;">--homedir</span> prende il nome utente dalla directory home</td></tr>
									    <tr><td class="help_table_gen_a">reporting_test</td>
									        <td class="help_table_gen_b">- Testa tutti i servizi di notifica attivi</td></tr>
									    <tr><td class="help_table_gen_a">set_sudoers</td>
									        <td class="help_table_gen_b">- Crea file sudoers per l&apos;utente www-data e l&apos;utente sotto cui è installato Pi.Alert</td></tr>
									    <tr><td class="help_table_gen_a">unset_sudoers</td>
									        <td class="help_table_gen_b">- Elimina i file sudoers per l&apos;utente www-data e l&apos;utente sotto cui è installato Pi.Alert</td></tr>
									</table>';
$help_lang['Cat_General_106_head'] = 'Come posso eseguire una verifica di integrità del database?';
$help_lang['Cat_General_106_text'] = 'Se desideri verificare il database attualmente in uso, arresta Pi.Alert per circa 1 ora per evitare qualsiasi accesso in scrittura al database durante la verifica. Inoltre, l&apos;interfaccia web non dovrebbe essere aperta per altre operazioni di scrittura durante la verifica.
									 Ora apri la console nella directory <span class="text-maroon help_faq_code">~/pialert/back</span> e utilizza il comando <span class="text-maroon help_faq_code">ls</span> per elencare il contenuto della directory. Se i file
									 <span class="text-maroon help_faq_code">pialert.db-shm</span> e <span class="text-maroon help_faq_code">pialert.db-wal</span> compaiono nell&apos;elenco (con lo stesso timestamp del file "pialert.db"), significa che ci sono ancora transazioni di database aperte. In questo caso, attendi semplicemente un momento e, per verificare, esegui nuovamente il comando <span class="text-maroon help_faq_code">ls</span>.
									 <br><br>
									 Una volta che questi file sono scomparsi, è possibile eseguire la verifica. Per farlo, esegui i seguenti comandi:<br>
									 <div class="help_faq_code" style="padding-left: 10px; margin-bottom: 10px;">
									    sqlite3 pialert.db "PRAGMA integrity_check"<br>
									    sqlite3 pialert.db "PRAGMA foreign_key_check"
									 </div><br>
									 In entrambi i casi, non dovrebbero essere segnalati errori. Dopo la verifica, puoi riavviare Pi.Alert.';
$help_lang['Cat_General_107_head'] = 'Spiegazioni per il file "pialert.conf"';
$help_lang['Cat_General_107_text'] = 'Il file <span class="text-maroon help_faq_code">pialert.conf</span> si trova nella directory <span class="text-maroon help_faq_code">~/pialert/config</span>. In questo file di configurazione è possibile personalizzare molte funzionalità di Pi.Alert in base alle proprie preferenze. Poiché le opzioni sono varie, fornirò una breve spiegazione di ciascun punto.
                                    <table class="help_table_gen">
                                        <tr><td class="help_table_gen_section" colspan="2">Impostazioni generali</td></tr>
                                        <tr><td class="help_table_gen_a">PIALERT_PATH</td>
                                            <td class="help_table_gen_b">Questa variabile viene impostata durante l&apos;installazione e non dovrebbe essere più modificata.</td></tr>
                                        <tr><td class="help_table_gen_a">DB_PATH</td>
                                            <td class="help_table_gen_b">Questa variabile viene impostata durante l&apos;installazione e non dovrebbe essere più modificata.</td></tr>
                                        <tr><td class="help_table_gen_a">LOG_PATH</td>
                                            <td class="help_table_gen_b">Questa variabile viene impostata durante l&apos;installazione e non dovrebbe essere più modificata.</td></tr>
                                        <tr><td class="help_table_gen_a">PRINT_LOG</td>
                                            <td class="help_table_gen_b">Se questa voce è impostata su <span class="text-maroon help_faq_code">True</span>, verranno aggiunti timestamp alle voci di registro della scansione per le singole sottosezioni. Per impostazione predefinita, questa voce è impostata su <span class="text-maroon help_faq_code">False</span></td></tr>
                                        <tr><td class="help_table_gen_a">VENDORS_DB</td>
                                            <td class="help_table_gen_b">Questa variabile viene impostata durante l&apos;installazione e non dovrebbe essere più modificata.</td></tr>
                                        <tr><td class="help_table_gen_a">PIALERT_APIKEY</td>
                                            <td class="help_table_gen_b">Con la chiave API è possibile effettuare query al database senza utilizzare il sito web. La chiave API è una stringa casuale che può essere impostata tramite le impostazioni o tramite <span class="text-maroon help_faq_code">pialert-cli</span>.</td></tr>
                                        <tr><td class="help_table_gen_a">PIALERT_WEB_PROTECTION</td>
                                            <td class="help_table_gen_b">Attiva o disattiva la protezione con password dell&apos;interfaccia web di Pi.Alert.</td></tr>
                                        <tr><td class="help_table_gen_a">PIALERT_WEB_PASSWORD</td>
                                            <td class="help_table_gen_b">Questo campo contiene la password "hashata" per l&apos;interfaccia web. La password non può essere inserita in chiaro qui ma deve essere impostata tramite <span class="text-maroon help_faq_code">pialert-cli</span>.</td></tr>
                                    </table>
                                    <table class="help_table_gen">
                                        <tr><td class="help_table_gen_section" colspan="2">Altri moduli</td></tr>
                                        <tr><td class="help_table_gen_a">SCAN_WEBSERVICES</td>
                                            <td class="help_table_gen_b">Qui è possibile attivare (<span class="text-maroon help_faq_code">True</span>) o disattivare (<span class="text-maroon help_faq_code">False</span>) la funzione di monitoraggio dei servizi web.</td></tr>
                                        <tr><td class="help_table_gen_a">ICMPSCAN_ACTIVE</td>
                                            <td class="help_table_gen_b">Attiva o disattiva il monitoraggio ICMP.</td></tr>
                                    </table>
                                    <table class="help_table_gen">
                                        <tr><td class="help_table_gen_section" colspan="2">Scansione di protocolli speciali</td></tr>
                                        <tr><td class="help_table_gen_a">SCAN_ROGUE_DHCP</td>
                                            <td class="help_table_gen_b">Attiva la ricerca di server DHCP "rogue" (sconosciuti). Questa funzione serve a rilevare la presenza di un server DHCP sconosciuto nella rete, che potrebbe assumere il controllo della gestione degli indirizzi IP.</td></tr>
                                        <tr><td class="help_table_gen_a">DHCP_SERVER_ADDRESS</td>
                                            <td class="help_table_gen_b">Qui viene inserito l&apos;indirizzo IP del server DHCP noto.</td></tr>
                                    </table>
                                    <table class="help_table_gen">
                                        <tr><td class="help_table_gen_section" colspan="2">Impostazioni dell&apos;account e-mail</td></tr>
                                        <tr><td class="help_table_gen_a">SMTP_SERVER</td>
                                            <td class="help_table_gen_b">Indirizzo del server di posta elettronica (ad esempio, smtp.gmail.com).</td></tr>
                                        <tr><td class="help_table_gen_a">SMTP_PORT</td>
                                            <td class="help_table_gen_b">La porta del server SMTP. La porta può variare a seconda della configurazione del server.</td></tr>
                                        <tr><td class="help_table_gen_a">SMTP_USER</td>
                                            <td class="help_table_gen_b">Nome utente.</td></tr>
                                        <tr><td class="help_table_gen_a">SMTP_PASS</td>
                                            <td class="help_table_gen_b">Password.</td></tr>
                                        <tr><td class="help_table_gen_a">SMTP_SKIP_TLS</td>
                                            <td class="help_table_gen_b">Se questa voce è impostata su <span class="text-maroon help_faq_code">True</span>, verrà utilizzata la crittografia di trasporto per le e-mail. Se il server non lo supporta, questa voce deve essere impostata su <span class="text-maroon help_faq_code">False</span>.</td></tr>
                                        <tr><td class="help_table_gen_a">SMTP_SKIP_LOGIN</td>
                                            <td class="help_table_gen_b">Alcuni server SMTP non richiedono l&apos;autenticazione. In tal caso, questo valore deve essere impostato su <span class="text-maroon help_faq_code">True</span>.</td></tr>
                                    </table>
                                    <table class="help_table_gen">
                                        <tr><td class="help_table_gen_section" colspan="2">Segnalazioni tramite WebGUI</td></tr>
                                        <tr><td class="help_table_gen_a">REPORT_WEBGUI</td>
                                            <td class="help_table_gen_b">Attiva o disattiva le notifiche sulle modifiche di rete nell&apos;interfaccia web.</td></tr>
                                        <tr><td class="help_table_gen_a">REPORT_WEBGUI_WEBMON</td>
                                            <td class="help_table_gen_b">Attiva o disattiva le notifiche sulle modifiche dei servizi web monitorati nell&apos;interfaccia web.</td></tr>
                                    </table>
                                    <table class="help_table_gen">
                                        <tr>
                                            <td class="help_table_gen_section" colspan="2">Segnalazioni tramite e-mail</td>
                                        </tr>
                                        <tr><td class="help_table_gen_a">REPORT_MAIL</td>
                                            <td class="help_table_gen_b">Attiva o disattiva le notifiche sulle modifiche di rete tramite e-mail.</td></tr>
                                        <tr><td class="help_table_gen_a">REPORT_MAIL_WEBMON</td>
                                            <td class="help_table_gen_b">Attiva o disattiva le notifiche sulle modifiche dei servizi web monitorati tramite e-mail.</td></tr>
                                        <tr><td class="help_table_gen_a">REPORT_FROM</td>
                                            <td class="help_table_gen_b">Nome o denominazione del mittente.</td></tr>
                                        <tr><td class="help_table_gen_a">REPORT_TO</td>
                                            <td class="help_table_gen_b">Indirizzo e-mail a cui inviare la notifica.</td></tr>
                                        <tr><td class="help_table_gen_a">REPORT_DEVICE_URL</td>
                                            <td class="help_table_gen_b">URL dell&apos;installazione di Pi.Alert per creare un collegamento cliccabile nell&apos;e-mail relativo al dispositivo rilevato.</td></tr>
                                        <tr><td class="help_table_gen_a">REPORT_DASHBOARD_URL</td>
                                            <td class="help_table_gen_b">URL dell&apos;installazione di Pi.Alert per creare un collegamento cliccabile nell&apos;e-mail.</td></tr>
                                    </table>
                                    <table class="help_table_gen">
                                        <tr>
                                            <td class="help_table_gen_section" colspan="2">Pushsafer</td>
                                        </tr>
                                        <tr><td class="help_table_gen_a">REPORT_PUSHSAFER</td>
                                            <td class="help_table_gen_b">Attiva o disattiva le notifiche sulle modifiche di rete tramite Pushsafer.</td></tr>
                                        <tr><td class="help_table_gen_a">REPORT_PUSHSAFER_WEBMON</td>
                                            <td class="help_table_gen_b">Attiva o disattiva le notifiche sulle modifiche dei servizi web monitorati tramite Pushsafer.</td></tr>
                                        <tr><td class="help_table_gen_a">PUSHSAFER_TOKEN</td>
                                            <td class="help_table_gen_b">Questo è la chiave privata visualizzabile sulla pagina Pushsafer.</td></tr>
                                        <tr><td class="help_table_gen_a">PUSHSAFER_DEVICE</td>
                                            <td class="help_table_gen_b">L&apos;ID del dispositivo a cui inviare il messaggio. &apos;<span class="text-maroon help_faq_code">a</span>&apos; significa che il messaggio sarà inviato a tutti i dispositivi configurati e utilizzerà quindi molti API call.</td></tr>
                                    </table>
                                    <table class="help_table_gen">
                                        <tr><td class="help_table_gen_section" colspan="2">Pushover</td></tr>
                                        <tr><td class="help_table_gen_a">REPORT_PUSHOVER</td>
                                            <td class="help_table_gen_b">Attiva o disattiva le notifiche sulle modifiche di rete tramite Pushover.</td></tr>
                                        <tr><td class="help_table_gen_a">REPORT_PUSHOVER_WEBMON</td>
                                            <td class="help_table_gen_b">Attiva o disattiva le notifiche sulle modifiche dei servizi web monitorati tramite Pushover.</td></tr>
                                        <tr><td class="help_table_gen_a">PUSHOVER_TOKEN</td>
                                            <td class="help_table_gen_b">Noti anche come "APP TOKEN" o "API TOKEN". Questo token può essere ottenuto dalla pagina Pushover.</td></tr>
                                        <tr><td class="help_table_gen_a">PUSHOVER_USER</td>
                                            <td class="help_table_gen_b">O "USER KEY". Questo codice viene mostrato sulla homepage di Pushover subito dopo il login.</td></tr>
                                    </table>
                                    <table class="help_table_gen">
                                        <tr><td class="help_table_gen_section" colspan="2">NTFY</td></tr>
                                        <tr><td class="help_table_gen_a">REPORT_NTFY</td>
                                            <td class="help_table_gen_b">Attiva o disattiva le notifiche sulle modifiche di rete tramite NTFY.</td></tr>
                                        <tr><td class="help_table_gen_a">REPORT_NTFY_WEBMON</td>
                                            <td class="help_table_gen_b">Attiva o disattiva le notifiche sulle modifiche dei servizi web monitorati tramite NTFY.</td></tr>
                                        <tr><td class="help_table_gen_a">NTFY_HOST</td>
                                            <td class="help_table_gen_b">L&apos;hostname o l&apos;indirizzo IP del server NTFY.</td></tr>
                                        <tr><td class="help_table_gen_a">NTFY_TOPIC</td>
                                            <td class="help_table_gen_b">Il soggetto delle notifiche inviate tramite NTFY.</td></tr>
                                        <tr><td class="help_table_gen_a">NTFY_USER</td>
                                            <td class="help_table_gen_b">Il nome utente utilizzato per l&apos;autenticazione presso il server NTFY.</td></tr>
                                        <tr><td class="help_table_gen_a">NTFY_PASSWORD</td>
                                            <td class="help_table_gen_b">La password utilizzata per l&apos;autenticazione presso il server NTFY.</td></tr>
                                        <tr><td class="help_table_gen_a">NTFY_PRIORITY</td>
                                            <td class="help_table_gen_b">La priorità delle notifiche inviate tramite NTFY.</td></tr>
                                    </table>
                                    <table class="help_table_gen">
                                        <tr><td class="help_table_gen_section" colspan="2">Shoutrrr</td></tr>
                                        <tr><td class="help_table_gen_a">SHOUTRRR_BINARY</td>
                                            <td class="help_table_gen_b">Qui è necessario configurare quale binario di Shoutrrr utilizzare, in base all&apos;hardware su cui è installato Pi.Alert.</td></tr>
                                    </table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Telegram tramite Shoutrrr</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_TELEGRAM</td>
									        <td class="help_table_gen_b">Attiva/Disattiva le notifiche sulle modifiche di rete tramite Telegram</td></tr>
									    <tr><td class="help_table_gen_a">REPORT_TELEGRAM_WEBMON</td>
									        <td class="help_table_gen_b">Attiva/Disattiva le notifiche sulle modifiche nei servizi web monitorati tramite Telegram</td></tr>
									    <tr><td class="help_table_gen_a">TELEGRAM_BOT_TOKEN_URL</td>
									        <td class="help_table_gen_b">URL del token del bot Telegram</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">DynDNS e IP</td></tr>
									    <tr><td class="help_table_gen_a">QUERY_MYIP_SERVER</td>
									        <td class="help_table_gen_b">URL del server che recupera e restituisce l&apos;indirizzo IP pubblico corrente</td></tr>
									    <tr><td class="help_table_gen_a">DDNS_ACTIVE</td>
									        <td class="help_table_gen_b">Attiva/Disattiva il servizio DDNS configurato in Pi.Alert. Il DDNS, noto anche come DynDNS, consente di aggiornare un nome di dominio con un indirizzo IP che cambia dinamicamente. Diversi fornitori di servizi offrono questo servizio.</td></tr>
									    <tr><td class="help_table_gen_a">DDNS_DOMAIN</td>
									        <td class="help_table_gen_b">Dominio DDNS</td></tr>
									    <tr><td class="help_table_gen_a">DDNS_USER</td>
									        <td class="help_table_gen_b">Nome utente</td></tr>
									    <tr><td class="help_table_gen_a">DDNS_PASSWORD</td>
									        <td class="help_table_gen_b">Password</td></tr>
									    <tr><td class="help_table_gen_a">DDNS_UPDATE_URL</td>
									        <td class="help_table_gen_b">URL di aggiornamento DDNS</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Automatic Speedtest</td></tr>
									    <tr><td class="help_table_gen_a">SPEEDTEST_TASK_ACTIVE</td>
									        <td class="help_table_gen_b"></td></tr>
									    <tr><td class="help_table_gen_a">SPEEDTEST_TASK_HOUR</td>
									        <td class="help_table_gen_b"></td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Opzioni e campioni di arp-scan</td></tr>
									    <tr><td class="help_table_gen_a">MAC_IGNORE_LIST</td>
									        <td class="help_table_gen_b">
									            <span class="text-maroon help_faq_code">[&apos;Indirizzo MAC 1&apos;, &apos;Indirizzo MAC 2&apos;]</span><br>
									            Questi indirizzi MAC (in minuscolo) verranno filtrati dai risultati della scansione.</td></tr>
									    <tr><td class="help_table_gen_a">SCAN_SUBNETS</td>
									        <td class="help_table_gen_b">
									            &lsquo;<span class="text-maroon help_faq_code">--localnet</span>&rsquo;<br>
									            Questa opzione è di solito l&apos;impostazione corretta. Utilizzala quando Pi.Alert è installato su un dispositivo con una scheda di rete e non sono configurate reti aggiuntive.<br><br>
									            &lsquo;<span class="text-maroon help_faq_code">--localnet --interface=eth0</span>&rsquo;<br>
									            Questa configurazione viene scelta quando Pi.Alert è installato su un sistema con almeno 2 schede di rete e una rete configurata. Il nome dell&apos;interfaccia potrebbe variare e dovrebbe essere adattato alla configurazione del sistema.<br><br>
									            <span class="text-maroon help_faq_code">[&apos;192.168.1.0/24 --interface=eth0&apos;, &apos;192.168.2.0/24 --interface=eth1&apos;]</span><br>
									            L&apos;ultima configurazione è necessaria quando si monitorano più reti. Dovrebbe essere configurata una scheda di rete dedicata per ogni rete da monitorare. Questo è necessario perché il comando "arp-scan" utilizzato non effettua il routing ed è in grado di funzionare solo all&apos;interno del proprio subnet. Ogni interfaccia è associata alla rispettiva rete e il nome dell&apos;interfaccia dovrebbe essere adattato alla configurazione del sistema.
									        </td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Opzioni di monitoraggio ICMP</td></tr>
									    <tr><td class="help_table_gen_a">ICMP_ONLINE_TEST</td>
									        <td class="help_table_gen_b">Numero di tentativi per determinare se un dispositivo è online (impostazione predefinita 1).</td></tr>
									    <tr><td class="help_table_gen_a">ICMP_GET_AVG_RTT</td>
									        <td class="help_table_gen_b">Numero di richieste "ping" per calcolare il tempo medio di risposta (impostazione predefinita 2).</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Configurazione di Pi-hole</td></tr>
									    <tr><td class="help_table_gen_a">PIHOLE_ACTIVE</td>
									        <td class="help_table_gen_b">Questa variabile viene impostata durante l&apos;installazione.</td></tr>
									    <tr><td class="help_table_gen_a">PIHOLE_DB</td>
									        <td class="help_table_gen_b">Questa variabile viene impostata durante l&apos;installazione e non dovrebbe essere più modificata.</td></tr>
									    <tr><td class="help_table_gen_a">DHCP_ACTIVE</td>
									        <td class="help_table_gen_b">Questa variabile viene impostata durante l&apos;installazione.</td></tr>
									    <tr><td class="help_table_gen_a">DHCP_LEASES</td>
									        <td class="help_table_gen_b">Questa variabile viene impostata durante l&apos;installazione e non dovrebbe essere più modificata.</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Configurazione di Fritzbox</td></tr>
									    <tr><td class="help_table_gen_a">FRITZBOX_ACTIVE</td>
									        <td class="help_table_gen_b">Se viene utilizzato un router Fritzbox nella rete, questo può essere utilizzato come fonte dati. In questo punto è possibile attivarlo o disattivarlo.</td></tr>
									    <tr><td class="help_table_gen_a">FRITZBOX_IP</td>
									        <td class="help_table_gen_b">Indirizzo IP del Fritzbox.</td></tr>
									    <tr><td class="help_table_gen_a">FRITZBOX_USER</td>
									        <td class="help_table_gen_b">Nome utente<br>Questo presuppone che il Fritzbox sia configurato per l&apos;accesso con nome utente e password anziché solo con la password. L&apos;accesso solo con password non è supportato.</td></tr>
									    <tr><td class="help_table_gen_a">FRITZBOX_PASS</td>
									        <td class="help_table_gen_b">Password</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Configurazione di Mikrotik</td></tr>
									    <tr><td class="help_table_gen_a">MIKROTIK_ACTIVE</td>
									        <td class="help_table_gen_b">Se viene utilizzato un router Mikrotik nella rete, questo può essere utilizzato come fonte dati. In questo punto è possibile attivarlo o disattivarlo.</td></tr>
									    <tr><td class="help_table_gen_a">MIKROTIK_IP</td>
									        <td class="help_table_gen_b">Indirizzo IP del router Mikrotik.</td></tr>
									    <tr><td class="help_table_gen_a">MIKROTIK_USER</td>
									        <td class="help_table_gen_b">Nome utente</td></tr>
									    <tr><td class="help_table_gen_a">MIKROTIK_PASS</td>
									        <td class="help_table_gen_b">Password</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Configurazione di UniFi</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_ACTIVE</td>
									        <td class="help_table_gen_b">Se un sistema UniFi è utilizzato nella rete, questo può essere utilizzato come fonte dati. In questo punto è possibile attivarlo o disattivarlo.</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_IP</td>
									        <td class="help_table_gen_b">Indirizzo IP del sistema UniFi.</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_API</td>
									        <td class="help_table_gen_b">Possible UNIFI APIs are v4, v5, unifiOS, UDMP-unifiOS</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_USER</td>
									        <td class="help_table_gen_b">Nome utente</td></tr>
									    <tr><td class="help_table_gen_a">UNIFI_PASS</td>
									        <td class="help_table_gen_b">Password</td></tr>
									</table>
									<table class="help_table_gen">
									    <tr><td class="help_table_gen_section" colspan="2">Compiti di manutenzione Cron</td></tr>
									    <tr><td class="help_table_gen_a">DAYS_TO_KEEP_ONLINEHISTORY</td>
									        <td class="help_table_gen_b">Numero di giorni per cui la cronologia online (grafico di attività) deve essere conservata nel database. Un giorno genera 288 di tali record.</td></tr>
									    <tr><td class="help_table_gen_a">DAYS_TO_KEEP_EVENTS</td>
									        <td class="help_table_gen_b">Numero di giorni per cui gli eventi dei singoli dispositivi devono essere conservati.</td></tr>
									</table>';
$help_lang['Cat_General_108_head'] = 'È disponibile un aggiornamento. Cosa devo fare se voglio aggiornare Pi.Alert?';
$help_lang['Cat_General_108_text'] = '<ol>
										<li>Verifica nella casella di stato nella pagina delle impostazioni che al momento non sia in esecuzione alcuna scansione.</li>
										<li>Maggiormente in basso, nella sezione sicurezza, ferma Pi.Alert per 15 minuti. Ciò impedisce che il database venga modificato durante l&apos;aggiornamento.</li>
										<li>Ora passa al terminale del dispositivo su cui è installato Pi.Alert.</li>
										<li>Esegui il comando:<br>
											<input id="bashupdatecommand" readonly value="bash -c &quot;$(wget -qLO - https://github.com/leiweibau/Pi.Alert/raw/main/install/pialert_update.sh)&quot;" style="width:100%; overflow-x: scroll; border: none; background: transparent; margin: 0px; padding: 0px;"></li>
										<li>Segui le istruzioni.</li>
										<li>Dopo un aggiornamento riuscito, Pi.Alert dovrebbe riavviarsi automaticamente. In alternativa, puoi riavviarlo manualmente nella pagina delle impostazioni.</li>
									</ol>';
$help_lang['Cat_Device_200_head'] = 'Ho dispositivi nella mia lista che sono sconosciuti o che non uso più. Dopo averli cancellati, ricompaiono sempre.';
$help_lang['Cat_Device_200_text'] = 'Se stai utilizzando Pi-hole, tieni presente che Pi.Alert recupera informazioni da Pi-hole. Sospendi Pi.Alert, vai alla pagina Impostazioni di Pi-hole e cancella eventuali lease DHCP
									relativi ai dispositivi che desideri rimuovere. Successivamente, controlla anche nella sezione Strumenti -> Rete di Pi-hole se trovi i dispositivi che riappaiono. Se li trovi, cancellali
									anche lì. Se questi dispositivi continuano a riapparire anche dopo la cancellazione in Pi-hole, riavvia il servizio `pihole-FTL`. Ora puoi riavviare Pi.Alert. Ora i dispositivi non dovrebbero
									più riapparire. In caso di dubbi, un riavvio generale potrebbe aiutare. Se un dispositivo continua a riapparire, puoi aggiungere il suo indirizzo MAC a una lista di ignorati (`MAC_IGNORE_LIST`)
									nel file `pialert.conf`.';
$help_lang['Cat_Detail_300_head'] = 'Cosa significa ';
$help_lang['Cat_Detail_300_text_a'] = 'significa un dispositivo di rete creato tramite la pagina di rete.';
$help_lang['Cat_Detail_300_text_b'] = 'indica il numero di porta a cui il dispositivo attualmente in fase di modifica è collegato a questo dispositivo di rete.';
$help_lang['Cat_Detail_301_head_a'] = 'Quando viene eseguita la scansione? Nella configurazione è scritto "1min", ma il grafico mostra intervalli di 5 minuti.';
$help_lang['Cat_Detail_301_head_b'] = ' è impostato su 1 minuto ma il grafico mostra intervalli di 5 minuti.';
$help_lang['Cat_Detail_301_text'] = 'L&apos;intervallo di tempo tra le scansioni è determinato dal "Cronjob", che di default è impostato su 5 minuti. La denominazione "1min" si riferisce alla durata prevista della scansione.
									Tuttavia, a seconda della configurazione di rete, questo intervallo potrebbe variare. Per modificare l&apos;intervallo di esecuzione del "Cronjob", puoi utilizzare il comando `crontab -e` nel
									terminale o nella console.';
$help_lang['Cat_Detail_302_head_a'] = 'Cosa significa ';
$help_lang['Cat_Detail_302_head_b'] = ' e perché non posso selezionarlo?';
$help_lang['Cat_Detail_302_text'] = 'Alcuni dispositivi moderni generano indirizzi MAC casuali per motivi di privacy, che non possono essere associati a un produttore specifico e che cambiano ogni volta che il dispositivo si
									connette alla rete. Pi.Alert riconosce automaticamente se si tratta di un indirizzo MAC casuale e attiva automaticamente questo campo. Per disattivare questa funzionalità, è necessario verificare
									le impostazioni del dispositivo per vedere come disattivare la generazione di indirizzi MAC casuali.';
$help_lang['Cat_Detail_303_head'] = 'Cos&apos;è Nmap e a cosa serve?';
$help_lang['Cat_Detail_303_text'] = 'Nmap è uno scanner di rete con molte funzionalità. Quando un nuovo dispositivo appare nella tua lista, puoi utilizzare la scansione Nmap per ottenere informazioni dettagliate sul dispositivo.';
$help_lang['Cat_Presence_400_head'] = 'I dispositivi vengono visualizzati con una barra gialla e la scritta "missing Event".';
$help_lang['Cat_Presence_400_text'] = 'Se ciò accade, hai la possibilità di eliminare gli eventi per il dispositivo in questione (nella vista dettagli). Un&apos;altra opzione potrebbe essere quella di accendere il dispositivo e
									  attendere che Pi.Alert lo riconosca come "Online" con la successiva scansione, quindi spegnere nuovamente il dispositivo. Ora Pi.Alert dovrebbe registrare correttamente lo stato del dispositivo nella prossima scansione.';
$help_lang['Cat_Presence_401_head'] = 'Un dispositivo viene visualizzato come "Presente" anche se è "Offline".';
$help_lang['Cat_Presence_401_text'] = 'Se ciò accade, hai la possibilità di eliminare gli eventi per il dispositivo in questione (nella vista dettagli). Un&apos;altra opzione potrebbe essere quella di accendere il dispositivo e
									  attendere che Pi.Alert lo riconosca come "Online" con la successiva scansione, quindi spegnere nuovamente il dispositivo. Ora Pi.Alert dovrebbe registrare correttamente lo stato del dispositivo nella prossima scansione.';
$help_lang['Cat_Network_600_head'] = 'A cosa serve questa pagina?';
$help_lang['Cat_Network_600_text'] = 'Questa pagina ti consente di rappresentare la disposizione dei dispositivi nella tua rete. Puoi creare uno o più switch, reti Wi-Fi, router, ecc., assegnare loro un numero di porta (se necessario)
									 e associare dispositivi rilevati a queste porte. Questa associazione avviene nella vista dettagli del dispositivo che desideri associare.';
$help_lang['Cat_Network_601_head'] = 'Come funziona la pagina di rete?';
$help_lang['Cat_Network_601_text'] = 'La pagina di rete è composta da due componenti: la vista di rappresentazione e la pagina di gestione, raggiungibile facendo clic su "+" accanto all&apos;intestazione. Qualsiasi modifica nella
									 pagina di gestione influisce solo sulla vista di rappresentazione e non sulla lista dei dispositivi stessa.
									<br><br>
									Nella pagina di gestione, puoi creare uno switch, ad esempio, e vedere i dispositivi già rilevati nell&apos;elenco a discesa. Devi anche specificare il tipo e, se necessario, il numero di porta.
									<br><br>
									Nella vista dettagli di ciascun dispositivo rilevato, hai la possibilità di associare questo switch appena creato e la porta utilizzata.
									<br><br>
									Ora la vista di rappresentazione della rete ti mostrerà lo switch con le relative porte e i dispositivi ad esso collegati. Inoltre, nella vista dettagli di ciascun dispositivo, puoi assegnare più porte a uno switch, separandole con una virgola (ad esempio, per il bonding delle porte) o associare più dispositivi a una porta (ad esempio, un server con più macchine virtuali).';
$help_lang['Cat_Network_602_head'] = 'Uno switch o un router viene visualizzato senza porte.';
$help_lang['Cat_Network_602_text'] = 'Potrebbe essere che al momento della creazione del dispositivo nella pagina di rete non sia stato specificato il numero di porte. Inoltre, quando si modifica il dispositivo nella pagina di rete, è necessario inserire nuovamente il numero di porte se il campo non viene riempito automaticamente.';
$help_lang['Cat_Service_700_head'] = 'Cosa significano i diversi colori delle barre?';
$help_lang['Cat_Service_700_text'] = 'Ci sono in totale 5 codici di colore differenti:<br>
									<span style="background-color:lightgray;">&nbsp;&nbsp;&nbsp;</span> - Nessuna scansione disponibile<br>
									<span class="bg-green">&nbsp;&nbsp;&nbsp;</span> - Codice di stato HTTP 2xx<br>
									<span class="bg-yellow">&nbsp;&nbsp;&nbsp;</span> - Codice di stato HTTP 3xx-4xx<br>
									<span class="bg-orange-custom">&nbsp;&nbsp;&nbsp;</span> - Codice di stato HTTP 5xx<br>
									<span class="bg-red">&nbsp;&nbsp;&nbsp;</span> - Offline';
$help_lang['Cat_Service_701_head'] = 'Quali sono i codici di stato HTTP? (inglese)';
// da json
$help_lang['Cat_Service_702_head'] = 'Quali modifiche vengono segnalate?';
$help_lang['Cat_Service_702_text'] = 'Gli eventi rilevabili includono:<br>
									<ul>
										<li>Cambio del codice di stato HTTP</li>
										<li>Cambio dell&apos;indirizzo IP</li>
										<li>Tempo di risposta del server o mancanza di risposta</li>
									</ul>
									A seconda delle impostazioni di notifica, verranno segnalati tutti o solo la mancanza di risposta del server.';
$help_lang['Cat_Service_703_head'] = 'Informazioni generali sul "Monitoraggio dei servizi web".';
$help_lang['Cat_Service_703_text'] = 'Il monitoraggio si basa esclusivamente sulle risposte alle richieste HTTP inviate al server web di destinazione. A seconda dello stato del server, possono essere rilevati errori significativi. Se il server non risponde affatto, ciò viene considerato come "Non disponibile/Offline". Queste richieste ai server web vengono eseguite ogni 10 minuti come parte della scansione regolare.';
$help_lang['Cat_ServiceDetails_750_head'] = 'Non posso modificare tutti i campi.';
$help_lang['Cat_ServiceDetails_750_text'] = 'Non tutti i campi visualizzati in questa pagina possono essere modificati. I campi modificabili sono:
											<ul>
												<li>' . $pia_lang['WebServices_label_Tags'] . '</li>
												<li>' . $pia_lang['WebServices_label_MAC'] . ' (eventualmente il dispositivo a cui è associato questo servizio web)<br>
													Qui viene richiesto un indirizzo MAC. Se inserisci qualcos&apos;altro (ad esempio, "Laptop"), verrà visualizzato come "' . $pia_lang['WebServices_unknown_Device'] . ' (Laptop)" nella panoramica.
													I servizi senza questa voce saranno elencati sotto "' . $pia_lang['WebServices_BoxTitle_General'] . '".</li>
												<li>CheckBox: ' . $pia_lang['WebServices_Events_all'] . '</li>
												<li>CheckBox: ' . $pia_lang['WebServices_Events_down'] . '</li>
											</ul>';

?>