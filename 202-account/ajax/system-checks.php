<?php
$current_dir =  str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__ );

include_once(substr(dirname( __FILE__ ), 0,-strlen($current_dir) ) . '/202-config/connect.php');

AUTH::require_user(); 

//check to see if this user has stats202 enabled
$_SESSION['stats202_enabled'] = AUTH::is_valid_app_key('stats202', $_SESSION['user_api_key'], $_SESSION['user_stats202_app_key']);