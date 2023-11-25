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
function calc_configfile_hash_top() {
	$Configfile = '../config/pialert.conf';
	return hash_file('md5', $Configfile);
}
// Save Journal
function pialert_logging($LogClass, $Trigger, $LogString, $Hash, $Additional_Info) {
	global $db;

	$journalFile = '../db/temp_journal.sql';

	if (file_exists('../../../config/pialert.conf')) {
		$journalFile = '../../../db/pialert_journal_buffer';
	} else {
		$journalFile = '../db/pialert_journal_buffer';
	}

	if ($Hash == 1) {
		$filehash = calc_configfile_hash();
		if ($filehash == "") {$filehash = calc_configfile_hash_top();}
	} else { $filehash = '';}

	$Journal_DateTime = date('Y-m-d H:i:s');

	// Query Insert
	$sql = 'INSERT INTO "pialert_journal" ("Journal_DateTime", "LogClass", "Trigger", "LogString", "Hash", "Additional_Info") VALUES("' . $Journal_DateTime . '", "' . $LogClass . '", "' . $Trigger . '", "' . $LogString . '", "' . $filehash . '", "' . $Additional_Info . '")';
	$result = $db->exec($sql);

	if ($result === false) {
		if (file_exists($journalFile) && filesize($journalFile) > 0) {
			// Append the error message to the file
			file_put_contents($journalFile, PHP_EOL . $sql, FILE_APPEND);
		} else {
			// Create a file with the error message
			file_put_contents($journalFile, $sql);
		}
	} else {
		if (file_exists($journalFile) && filesize($journalFile) > 0) {
			$queries = file($journalFile, FILE_IGNORE_NEW_LINES);
			foreach ($queries as $query) {
				$result = $db->exec($query);

				// if ($result !== false) {
				// 	echo "Query executed successfully. Rows affected: " . $result . PHP_EOL;
				// } else {
				// 	echo "Error executing query: " . $db->lastErrorMsg() . PHP_EOL;
				// }
			}
			unlink($journalFile);
		}
	}
}
?>