<?php
$ARCHIVE_PATH = str_replace('download', '', str_replace('front', 'db', getcwd()));
$CSVFILE = $ARCHIVE_PATH . "pialertcsv.zip";

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($CSVFILE) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($CSVFILE));
readfile($CSVFILE);
exit;
?>
