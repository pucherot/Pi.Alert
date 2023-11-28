<?php
$ARCHIVE_PATH = str_replace('download', '', str_replace('front', 'db', getcwd()));
$LATEST_FILES = glob($ARCHIVE_PATH . "pialertdb_*.zip");
if (sizeof($LATEST_FILES) == 0) {
	exit;
} else {
	natsort($LATEST_FILES);
	$LATEST_FILES = array_reverse($LATEST_FILES, False);
	$LATEST_BACKUP = $LATEST_FILES[0];
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($LATEST_BACKUP) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($LATEST_BACKUP));
readfile($LATEST_BACKUP);
exit;
?>
