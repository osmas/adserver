<?php 
$current_dir =  str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__ );

include_once(substr(dirname( __FILE__ ), 0,-strlen($current_dir) ) . '/202-config/connect.php'); 

AUTH::require_user();

template_top('Prosper202 ClickServer App Store');

//Initiate curl
$result = getData('https://my.tracking202.com/api/feeds/resources?us='.$_SESSION['user_hash']);

if ($result) {

    echo $result;

} else {
    echo "Sorry TV202 Feed Not Found: Please try again later";
} 

template_bottom(); 