<?php
$current_dir =  str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__ );

include_once(substr(dirname( __FILE__ ), 0,-strlen($current_dir) ) . '/202-config/connect.php');    

//AUTH::require_user();
if (isset($_POST['apikey'])) {
	echo api_key_validate($_POST['apikey']);
}
/* if($isvalid)
{
    $msg = array('error' => false, 'msg' => 'Valid');
}    
else{
    $msg = array('error' => true, 'msg' => "Invalid API Key");
}
echo json_encode($msg, true); */
?>

