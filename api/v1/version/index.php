<?php
$current_dir =  str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__ );

include_once(substr(dirname( __FILE__ ), 0,-strlen($current_dir) ) . '/202-config.php');
include_once(substr(dirname( __FILE__ ), 0,-strlen($current_dir) ) . '/202-config/connect2.php');
header('Content-Type: application/json');

echo json_encode(array('version' => $version));

?>