<?php
$current_dir =  str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__ );

include_once(substr(dirname( __FILE__ ), 0,-strlen($current_dir) ) . '/202-config.php');
include_once(substr(dirname( __FILE__ ), 0,-strlen($current_dir) ) . '/202-config/connect2.php');
include_once(substr(dirname( __FILE__ ), 0,-strlen($current_dir) ) . '/api/v1/functions.php');

header('Content-Type: application/json');
$data = array();

if ($_SERVER['REQUEST_METHOD'] == "GET") {
	$data = getStats($db, $_GET);
} else {
	$data = array('msg' => 'Not allowed request method', 'error' => true, 'status' => 405);
}

array_walk_recursive($data, function(&$val) {
    $val = utf8_encode($val);
});


$json = str_replace('\\/', '/', json_encode($data));

print_r(pretty_json($json));
?>