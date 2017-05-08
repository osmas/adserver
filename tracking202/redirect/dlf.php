<?php
$startScriptTime=microtime(TRUE);
#only allow numeric t202ids
$t202id = $_GET['t202id']; 
if (!is_numeric($t202id)) die();

# check to see if mysql connection works, if not fail over to cached stored redirect urls
include_once(substr(dirname( __FILE__ ), 0,-21) . '/202-config/connect2.php'); 

$usedCachedRedirect = false;
if (!$db) $usedCachedRedirect = true;

#the mysql server is down, use the cached redirect
if ($usedCachedRedirect==true) { 

		$t202id = $_GET['t202id'];

		//if a cached key is found for this t202id, redirect to that url
		if ($memcacheWorking) {
			$getUrl = $memcache->get(md5('url_'.$t202id.systemHash()));
			if ($getUrl) {

				$new_url = str_replace("[[subid]]", "p202", $getUrl);

				//c1 string replace for cached redirect
				if(isset($_GET['c1']) && $_GET['c1'] != ''){
					$new_url = str_replace("[[c1]]", $db->real_escape_string($_GET['c1']), $new_url);
				}	else {
					$new_url = str_replace("[[c1]]", "p202c1", $new_url);
				}

				//c2 string replace for cached redirect
				if(isset($_GET['c2']) && $_GET['c2'] != ''){
					$new_url = str_replace("[[c2]]", $db->real_escape_string($_GET['c2']), $new_url);
				}	else {
					$new_url = str_replace("[[c2]]", "p202c2", $new_url);
				}
				
				//c3 string replace for cached redirect
				if(isset($_GET['c3']) && $_GET['c3'] != ''){
					$new_url = str_replace("[[c3]]", $db->real_escape_string($_GET['c3']), $new_url);
				}	else {
					$new_url = str_replace("[[c3]]", "p202c3", $new_url);
				}

				//c4 string replace for cached redirect
				if(isset($_GET['c4']) && $_GET['c4'] != ''){
					$new_url = str_replace("[[c4]]", $db->real_escape_string($_GET['c4']), $new_url);
				}	else {
					$new_url = str_replace("[[c4]]", "p202c4", $new_url);
				}

				//gclid string replace for cached redirect
				if(isset($_GET['gclid']) && $_GET['gclid'] != ''){
				    $new_url = str_replace("[[gclid]]", $db->real_escape_string($_GET['gclid']), $new_url);
				}	else {
				    $new_url = str_replace("[[gclid]]", "p202gclid", $new_url);
				}
				
				//utm_source string replace for cached redirect
				if(isset($_GET['utm_source']) && $_GET['utm_source'] != ''){
				    $new_url = str_replace("[[utm_source]]", $db->real_escape_string($_GET['utm_source']), $new_url);
				}	else {
				    $new_url = str_replace("[[utm_source]]", "p202utm_source", $new_url);
				}
				
				//utm_medium string replace for cached redirect
				if(isset($_GET['utm_medium']) && $_GET['utm_medium'] != ''){
				    $new_url = str_replace("[[utm_medium]]", $db->real_escape_string($_GET['utm_medium']), $new_url);
				}	else {
				    $new_url = str_replace("[[utm_medium]]", "p202utm_medium", $new_url);
				}
				
				//utm_campaign string replace for cached redirect
				if(isset($_GET['utm_campaign']) && $_GET['utm_campaign'] != ''){
				    $new_url = str_replace("[[utm_campaign]]", $db->real_escape_string($_GET['utm_campaign']), $new_url);
				}	else {
				    $new_url = str_replace("[[utm_campaign]]", "p202utm_campaign", $new_url);
				}
				
				//utm_term string replace for cached redirect
				if(isset($_GET['utm_term']) && $_GET['utm_term'] != ''){
				    $new_url = str_replace("[[utm_term]]", $db->real_escape_string($_GET['utm_term']), $new_url);
				}	else {
				    $new_url = str_replace("[[utm_term]]", "p202utm_term", $new_url);
				}
				
				//utm_content string replace for cached redirect
				if(isset($_GET['utm_content']) && $_GET['utm_content'] != ''){
				    $new_url = str_replace("[[utm_content]]", $db->real_escape_string($_GET['utm_content']), $new_url);
				}	else {
				    $new_url = str_replace("[[utm_content]]", "p202utm_content", $new_url);
				}
			
				header('location: '. $new_url); 
				die();
			}
		}

	die("<h2>Error establishing a database connection - please contact the webhost</h2>");
}

//grab tracker data
$mysql['tracker_id_public'] = $db->real_escape_string($t202id);
$tracker_sql = "SELECT
						aff_campaign_url,
                        click_cpc,
						click_cpa,
						click_cloaking,
						aff_campaign_payout,
						aff_campaign_cloaking
						FROM 202_trackers 
                        LEFT JOIN 202_aff_campaigns USING (aff_campaign_id)
				        WHERE tracker_id_public='".$mysql['tracker_id_public']."'"; 
$tracker_row = memcache_mysql_fetch_assoc($db, $tracker_sql);

if ($memcacheWorking) {  
//save a better array with more data
	$url = $tracker_row['aff_campaign_url'];
	$tid = $t202id;

	$getKey = $memcache->get(md5('url_'.$tid.systemHash()));
	if($getKey === false){
		$setUrl = setCache(md5('url_'.$tid.systemHash()), $url);
	}
}

//replace tokens

if (!$tracker_row) { die(); }                                

//get mysql variables 
$mysql['aff_campaign_id'] = $db->real_escape_string($tracker_row['aff_campaign_id']);

// set cpc use dynamic variable if set or the default if not
if (isset ( $_GET ['t202b'] ) && $mysql['user_pref_dynamic_bid'] == '1') {
    $_GET ['t202b']=ltrim($_GET ['t202b'],'$');
    if(is_numeric ( $_GET ['t202b'] )){
        $bid = number_format ( $_GET ['t202b'], 5, '.', '' );
        $mysql ['click_cpc'] = $db->real_escape_string ( $bid );
    }
    else{
        $mysql ['click_cpc'] = $db->real_escape_string ( $tracker_row ['click_cpc'] );
    }
} else
    $mysql ['click_cpc'] = $db->real_escape_string ( $tracker_row ['click_cpc'] );

$mysql['click_cpa'] = $db->real_escape_string($tracker_row['click_cpa']);
$mysql['click_payout'] = $db->real_escape_string($tracker_row['aff_campaign_payout']);
$mysql['click_time'] = time();

/* ok, if $_GET['OVRAW'] that is a yahoo keyword, if on the REFER, there is a $_GET['q], that is a GOOGLE keyword... */
//so this is going to check the REFERER URL, for a ?q=, which is the ACUTAL KEYWORD searched.
$referer_url_parsed = @parse_url($_SERVER['HTTP_REFERER']);
$referer_url_query = $referer_url_parsed['query'];

@parse_str($referer_url_query, $referer_query);

switch ($tracker_row['user_keyword_searched_or_bidded']) { 

	case "bidded":
	      #try to get the bidded keyword first
		if ($_GET['OVKEY']) { //if this is a Y! keyword
			$keyword = $db->real_escape_string($_GET['OVKEY']);   
		}  elseif ($_GET['t202kw']) { 
			$keyword = $db->real_escape_string($_GET['t202kw']);  
		} elseif ($_GET['target_passthrough']) { //if this is a mediatraffic! keyword
			$keyword = $db->real_escape_string($_GET['target_passthrough']);   
		} else { //if this is a zango, or more keyword
			$keyword = $db->real_escape_string($_GET['keyword']);   
		} 
	break;
	case "searched":
		#try to get the searched keyword
		if ($referer_query['q']) { 
			$keyword = $db->real_escape_string($referer_query['q']);
		} elseif ($_GET['OVRAW']) { //if this is a Y! keyword
			$keyword = $db->real_escape_string($_GET['OVRAW']);   
		} elseif ($_GET['target_passthrough']) { //if this is a mediatraffic! keyword
			$keyword = $db->real_escape_string($_GET['target_passthrough']);   
		} elseif ($_GET['keyword']) { //if this is a zango, or more keyword
			$keyword = $db->real_escape_string($_GET['keyword']);   
		} elseif ($_GET['search_word']) { //if this is a eniro, or more keyword
			$keyword = $db->real_escape_string($_GET['search_word']);   
		} elseif ($_GET['query']) { //if this is a naver, or more keyword
			$keyword = $db->real_escape_string($_GET['query']);   
		} elseif ($_GET['encquery']) { //if this is a aol, or more keyword
			$keyword = $db->real_escape_string($_GET['encquery']);   
		} elseif ($_GET['terms']) { //if this is a about.com, or more keyword
			$keyword = $db->real_escape_string($_GET['terms']);   
		} elseif ($_GET['rdata']) { //if this is a viola, or more keyword
			$keyword = $db->real_escape_string($_GET['rdata']);   
		} elseif ($_GET['qs']) { //if this is a virgilio, or more keyword
			$keyword = $db->real_escape_string($_GET['qs']);   
		} elseif ($_GET['wd']) { //if this is a baidu, or more keyword
			$keyword = $db->real_escape_string($_GET['wd']);   
		} elseif ($_GET['text']) { //if this is a yandex, or more keyword
			$keyword = $db->real_escape_string($_GET['text']);   
		} elseif ($_GET['szukaj']) { //if this is a wp.pl, or more keyword
			$keyword = $db->real_escape_string($_GET['szukaj']);   
		} elseif ($_GET['qt']) { //if this is a O*net, or more keyword
			$keyword = $db->real_escape_string($_GET['qt']);   
		} elseif ($_GET['k']) { //if this is a yam, or more keyword
			$keyword = $db->real_escape_string($_GET['k']);   
		} elseif ($_GET['words']) { //if this is a Rambler, or more keyword
			$keyword = $db->real_escape_string($_GET['words']);   
		} else { 
			$keyword = $db->real_escape_string($_GET['t202kw']);
		}
	break;
}

if (substr($keyword, 0, 8) == 't202var_') {
	$t202var = substr($keyword, strpos($keyword, "_") + 1);

	if (isset($_GET[$t202var])) {
		$keyword = $_GET[$t202var];
	}
}

$keyword = str_replace('%20',' ',$keyword);      

$mysql['keyword'] = $db->real_escape_string($keyword);		  

$_lGET = array_change_key_case($_GET, CASE_LOWER); //make lowercase copy of get 
//Get C1-C4 IDs
for ($i=1;$i<=4;$i++){
    $custom= "c".$i; //create dynamic variable
    $custom_val=$db->real_escape_string($_lGET[$custom]); // get the value
    if(isset($custom_val) && $custom_val !=''){ //if there's a value get an id
        $custom_val = str_replace('%20',' ',$custom_val);
        $mysql[$custom]=$db->real_escape_string($custom_val); //save it
    }
}

$mysql['gclid']= $db->real_escape_string($_GET['gclid']);

//utm_source
$utm_source = $db->real_escape_string($_GET['utm_source']);
if(isset($utm_source) && $utm_source != '')
{
$utm_source = str_replace('%20',' ',$utm_source);
}
$mysql['utm_source']=$db->real_escape_string($utm_source);

//utm_medium
$utm_medium = $db->real_escape_string($_GET['utm_medium']);
if(isset($utm_medium) && $utm_medium != '')
{
    $utm_medium = str_replace('%20',' ',$utm_medium);
}
$mysql['utm_medium']=$db->real_escape_string($utm_medium);

//utm_campaign
$utm_campaign = $db->real_escape_string($_GET['utm_campaign']);
if(isset($utm_campaign) && $utm_campaign != '')
{
    $utm_campaign = str_replace('%20',' ',$utm_campaign);
}
$mysql['utm_campaign']=$db->real_escape_string($utm_campaign);

//utm_term
$utm_term = $db->real_escape_string($_GET['utm_term']);
if(isset($utm_term) && $utm_term != '')
{
    $utm_term = str_replace('%20',' ',$utm_term);
}
$mysql['utm_term']=$db->real_escape_string($utm_term);

//utm_content
$utm_content = $db->real_escape_string($_GET['utm_content']);
if(isset($utm_content) && $utm_content != '')
{
    $utm_content = str_replace('%20',' ',$utm_content);
}
$mysql['utm_content']=$db->real_escape_string($utm_content);
     
$mysql['click_in'] = 1;
$mysql['click_out'] = 1; 

$user_id = $tracker_row['user_id'];

//ok we have the main data, now insert this row
$click_sql = "INSERT INTO  202_clicks_counter SET click_id=DEFAULT";
$click_result = $db->query($click_sql) or record_mysql_error($db, $click_sql);   

//now gather the info for the advance click insert
$click_id = $db->insert_id;
$mysql['click_id'] = $db->real_escape_string($click_id);                            


//now gather variables for the clicks record db
//lets determine if cloaking is on
if (($tracker_row['click_cloaking'] == 1) or //if tracker has overrided cloaking on                                                             
	(($tracker_row['click_cloaking'] == -1) and ($tracker_row['aff_campaign_cloaking'] == 1)) or
	((!isset($tracker_row['click_cloaking'])) and ($tracker_row['aff_campaign_cloaking'] == 1)) //if no tracker but but by default campaign has cloaking on
) {
	$cloaking_on = true;
	$mysql['click_cloaking'] = 1;
	//if cloaking is on, add in a click_id_public, because we will be forwarding them to a cloaked /cl/xxxx link
	$click_id_public = rand(1,9) . $click_id . rand(1,9);
	$mysql['click_id_public'] = $db->real_escape_string($click_id_public); 
} else { 
	$mysql['click_cloaking'] = 0;
	$mysql['click_id_public'] = 0; 
}


if ($cloaking_on == true) {
	$cloaking_site_url = 'http://'.$_SERVER['SERVER_NAME'] . '/tracking202/redirect/cl.php?pci=' . $click_id_public;      
}


//replace tokens
$redirect_site_url = replaceTrackerPlaceholders($db, $redirect_site_url,$click_id,$mysql);

//set the cookie
setClickIdCookie($mysql['click_id'],$mysql['aff_campaign_id']);
echo "thecookie=".$mysql['click_id'];

//get and prep extra stuff for pre-pop or data passing
$urlvars = getPrePopVars($_GET);
unset($mysql);
$endScriptTime=microtime(TRUE);
$totalScriptTime=$endScriptTime-$startScriptTime;

//$fp = pfsockopen('localhost', 80);

$vars = [];

//$vars['t202id']=$t202id;
$vars=$_SERVER;
print_r($vars);
echo "<br><br><br><br>";
$content = ($vars);

function curl_post_async($url, $params)
{
    foreach ($params as $key => &$val) {
      if (is_array($val)) $val = implode(',', $val);
        $post_params[] = $key.'='.urlencode($val);
    }
    $post_params[] = 't202subidcookie='.$mysql['click_id'];
    $post_string = implode('&', $post_params);

//    $post_string = ((serialize($params)));
    $parts=parse_url($url);
echo "sending:".$post_string;
    $fp = pfsockopen($parts['host'], 
        isset($parts['port'])?$parts['port']:80, 
        $errno, $errstr, 30);

    //pete_assert(($fp!=0), "Couldn't open a socket to ".$url." (".$errstr.")");
/* if($fp!=0){
    echo "<br><br><br><br>failed trying again";
sleep(1);    
    $fp = pfsockopen($parts['host'],
        isset($parts['port'])?$parts['port']:80,
        $errno, $errstr, 30);
}
if($fp!=0){
    echo "<br><br><br><br>failed trying again";

    $fp = pfsockopen($parts['host'],
        isset($parts['port'])?$parts['port']:80,
        $errno, $errstr, 30);
} */

$out = "POST ".$parts['path']." HTTP/1.1\r\n";
    $out.= "Host: ".$parts['host']."\r\n";
    $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
    $out.= "Content-Length: ".strlen($post_string)."\r\n";
    $out.= "Connection: Close\r\n\r\n";
    if (isset($post_string)) $out.= $post_string;

    fwrite($fp, $out);
 // remove this when live
   echo  fread($fp,2048);
 //******   fclose($fp);
}

curl_post_async('http://localhost/tracking202/redirect/dlp.php?c='.$mysql['click_id'], $content);
/* 
fwrite($fp, "POST /tracking202/redirect/dlp.php HTTP/1.1\r\n");
fwrite($fp, "Host: localhost\r\n");
fwrite($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
fwrite($fp, "Content-Length: ".strlen($content)."\r\n");
fwrite($fp, "Connection: close\r\n");
fwrite($fp, "\r\n");

fwrite($fp, $content);
fread($fp, 1); */
//fclose($fp);
//header('Content-type: text/plain');
//while (!feof($fp)) {
  //  echo fgets($fp, 1024);
//} 

die('Load time: '.number_format($totalScriptTime, 4).' seconds');

//now we've recorded, now lets redirect them
if ($cloaking_on == true) {
	//if cloaked, redirect them to the cloaked site. 
	header('location: '.setPrePopVars($urlvars,$cloaking_site_url,true));
} else {
	header('location: '.setPrePopVars($urlvars,$redirect_site_url,false));
} 

die();