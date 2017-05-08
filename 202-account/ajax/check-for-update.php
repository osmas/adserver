<?php 
$current_dir =  str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__ );

include_once(substr(dirname( __FILE__ ), 0,-strlen($current_dir) ) . '/202-config/connect.php');

AUTH::require_user(); 

//check if its the latest verison
if(!isset($_SESSION['next_update_check']) || time() > $_SESSION['next_update_check']) {
    echo "needed";
	$_SESSION['show_update_check'] = true;
	$_SESSION['update_needed'] = update_needed();
} else {
    echo "not needed";
    $_SESSION['show_update_check'] = false;
}
