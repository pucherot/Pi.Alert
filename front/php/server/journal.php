<?php
#<!-- ---------------------------------------------------------------------------
#  Pi.Alert
#  Open Source Network Guard / WIFI & LAN intrusion detector
#
#  journal.php - Front module. Application logging
#-------------------------------------------------------------------------------
#  leiweibau 2023                                          GNU GPLv3
#--------------------------------------------------------------------------- -->

// detect file changes
function calc_configfile_hash() {
	$Configfile = '../../../config/pialert.conf';
	return hash_file('md5', $Configfile);
}

# Logging
# Journal_DateTime 	| LogClass 				| Trigger 			| LogString 										| Hash 				| Additional_Info
# 2023-07-31 19:45	| predefined Classes	| IP or pialert-cli	| predefined or custom (Entry removed or Wol log)	| configfile hash 	| Additional_Info

function pialert_logging($LogClass, $Trigger, $LogString, $Hash, $Additional_Info) {
	global $db;

	if ($Hash == 1) {$filehash = calc_configfile_hash();} else { $filehash = '';}

	$Journal_DateTime = date('Y-m-d H:i:s');

	$sql = 'INSERT INTO "pialert_journal" ("Journal_DateTime", "LogClass", "Trigger", "LogString", "Hash", "Additional_Info") VALUES("' . $Journal_DateTime . '", "' . $LogClass . '", "' . $Trigger . '", "' . $LogString . '", "' . $filehash . '", "' . $Additional_Info . '")';
	$result = $db->query($sql);
}
?>