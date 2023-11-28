<?php
$ARCHIVE_PATH = str_replace('download', '', str_replace('front', 'config', getcwd()));
$DOWN_CONF_FILE = $ARCHIVE_PATH . "pialert.conf";

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($DOWN_CONF_FILE) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($DOWN_CONF_FILE));
readfile($DOWN_CONF_FILE);
exit;
?>
