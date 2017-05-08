<?php
$current_dir =  str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__ );

include_once(substr(dirname( __FILE__ ), 0,-strlen($current_dir) ) . '/202-config/connect.php');

AUTH::require_user();

template_top('API Key Required');  ?> 


<div class="big-alert">

	The application you are trying to use requires a valid Stats202 App Key. <br/>
	You may enter in your Application Key by visiting the <a href="<?php echo get_absolute_url();?>202-account/account.php">My Account</a> tab in Prosper202.

</div>

        
<?php template_bottom();