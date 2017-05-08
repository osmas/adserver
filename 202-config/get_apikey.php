<?php
//include mysql settings
include_once(dirname( __FILE__ ) . '/connect.php');

//check to see if this is already installed, if so dob't do anything
if (is_installed() == true) {
if(isset($_GET['customers_api_key']) && $_GET['customers_api_key'] != ''){
    header("Location: /202-login.php?customers_api_key=".$_GET['customers_api_key']);
}else{
    _die("<h6>Already Installed</h6>
    <small>You appear to have already installed Prosper202. To reinstall please clear your old database tables first. <a href='/202-login.php'>Login Now</a></small>");    
}
    
}

$html['user_api'] = htmlentities($_GET['customers_api_key'], ENT_QUOTES, 'UTF-8');
 
info_top(); ?>
	<div class="main col-xs-7 install">
	<center><img src="<?php echo get_absolute_url();?>202-img/prosper202.png"></center>
	<h6>Welcome</h6>
	<small>Welcome to the five minute Prosper202 installation process! You may want to browse the <a href="http://support.tracking202.com/" target="_blank">ReadMe documentation</a> at your leisure. Otherwise, just fill in the information below and you'll be on your way to using the most powerful internet marketing applications in the world.</small>
	
	<h6>Create your account</h6>
	<small>Please provide the following information. Don't worry, you can always change these settings later.</small>
	<br><br>
		<form method=post action="" id="getapikey" class="form-horizontal" role="form">
            <div class="form-group <?php if ($error['user_email']) echo "has-error";?>">
			    <label for="user_api" class="col-xs-4 control-label"><strong>Prosper202 API Key:</strong><br> <small><a href="https://my.tracking202.com/api/customers/login?redirect=get-api">Click Here For Your API Key</a></small></label>
			    <div class="col-xs-8">
			      <input type="text" class="form-control input-sm" id="user_api" name="user_api" value="<?php echo $html['user_api']; ?>" readonly>
			    </div>
			</div>
		  

			<button class="btn btn-lg btn-p202 btn-block" type="submit">Save API Key & Install Prosper202<span class="fui-check-inverted pull-right"></span></button>

		</form>
		</div>
<script type="text/javascript">
$(document).ready(function() {

	$( "#getapikey").submit(function( event ) {
	
		    var apikey = {apikey: $("#user_api").val()};
	
		$.post("<?php echo get_absolute_url();?>202-account/ajax/validate-apikey.php", apikey).done(function(response) {
			var json = $.parseJSON(response);
			console.log(json);
				if (json.msg === 'Key valid') {
					document.cookie ="user_api="+$("#user_api").val();
					document.location.href="install.php"
				}
				else{
					if (confirm('Your Api Key Is Invalid. Would You Like Help Finding Your Key?')) {
						document.location.href="https://my.tracking202.com/api/customers/login?redirect=get-api"
					} 
				}
	  	});
		  event.preventDefault();
	});
});

</script>	

<?php
if ( isset( $_SERVER["HTTPS"] ) && strtolower( $_SERVER["HTTPS"] ) == "on" ) {
    $strProtocol = 'https://';
} else {
    $strProtocol = 'http://';
}

?>
<img src="https://my.tracking202.com/api/v2/dni/deeplink/cookie/set/<?php echo base64_encode($strProtocol .  $_SERVER['SERVER_NAME'] . get_absolute_url());?>">	
	<?php info_bottom(); 
	
	
