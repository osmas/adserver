<?php 
$current_dir =  str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__ );

include_once(substr(dirname( __FILE__ ), 0,-strlen($current_dir) ) . '/202-config/connect.php'); 

AUTH::require_user(); 

$html['new_offers'] = htmlentities($_SESSION['new_offers']);

if ($html['new_offers'])
	echo " ({$html['new_offers']})";