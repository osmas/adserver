<?php

function is_prefetch(){
    

if (isset($_SERVER["HTTP_X_PURPOSE"]) || isset($_SERVER['HTTP_X_MOZ'])){
    if($_SERVER['HTTP_PURPOSE'] == "prefetch" || $_SERVER['HTTP_PURPOSE'] == "preview"){
        return true;
    }
    
    if($_SERVER['HTTP_PURPOSE'] == "prefetch"){
        return true;
    }
}
else{
    return false;
}

}

$prefetch = is_prefetch();

#only allow numeric t202ids
$t202id = $_GET['t202id']; 
if (!is_numeric($t202id)) die();

# check to see if mysql connection works, if not fail over to cached stored redirect urls
include_once(substr(dirname( __FILE__ ), 0,-21) . '/202-config/connect2.php'); 
include_once(substr(dirname( __FILE__ ), 0,-21) . '/202-config/class-dataengine-slim.php');

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

				//show url or redirect to url
				if(isset($_GET['202rdu']) && $_GET['202rdu'] != ''){
				    echo $new_url;
				}
                else{
                    header('location: '. $new_url);
				 }
				 
				 die();

			}
		}
die();
}

//grab tracker data
$mysql['tracker_id_public'] = $db->real_escape_string($t202id);
$tracker_sql = "SELECT 202_trackers.user_id,
						202_trackers.aff_campaign_id,
						text_ad_id,
						ppc_account_id,
						click_cpc,
						click_cpa,
						click_cloaking,
						aff_campaign_rotate,
						aff_campaign_url,
						aff_campaign_url_2,
						aff_campaign_url_3,
						aff_campaign_url_4,
						aff_campaign_url_5,
						aff_campaign_payout,
						aff_campaign_cloaking,
						2cv.ppc_variable_ids,
						2cv.parameters,
                        user_timezone, 
		                user_keyword_searched_or_bidded,
                        user_pref_referer_data,
                        user_pref_dynamic_bid,
		                maxmind_isp 
                        FROM 202_trackers 
                        LEFT JOIN 202_users_pref USING (user_id) 
            LEFT JOIN 202_users USING (user_id) 
    			LEFT JOIN 202_aff_campaigns USING (aff_campaign_id)
				LEFT JOIN 202_ppc_accounts USING (ppc_account_id)
				LEFT JOIN (SELECT ppc_network_id, GROUP_CONCAT(ppc_variable_id) AS ppc_variable_ids, GROUP_CONCAT(parameter) AS parameters FROM 202_ppc_network_variables GROUP BY ppc_network_id) AS 2cv USING (ppc_network_id)					                 
				WHERE tracker_id_public='".$mysql['tracker_id_public']."'"; 

$tracker_row = memcache_mysql_fetch_assoc($dbro, $tracker_sql);

if($prefetch){
    //if this is  a prefetch request, send to the offer as quickly as possible
    $redirect_site_url = rotateTrackerUrl($db, $tracker_row);
    $redirect_site_url = replaceTrackerPlaceholders($db, $redirect_site_url,$click_id,$mysql);
    header('location: '.$redirect_site_url);
    die();    
}

if ($memcacheWorking) {  

	$url = $tracker_row['aff_campaign_url'];
	$tid = $t202id;

	$getKey = $memcache->get(md5('url_'.$tid.systemHash()));
	if($getKey === false){
		$setUrl = setCache(md5('url_'.$tid.systemHash()), $url);
	}
}
 




if (!$tracker_row) { die(); }                                
//set the user id
$mysql['user_id'] = $db->real_escape_string($tracker_row['user_id']);

//now this sets timezone
date_default_timezone_set($tracker_row['user_timezone']);

//get mysql variables 
$mysql['aff_campaign_id'] = $db->real_escape_string($tracker_row['aff_campaign_id']);
$mysql['ppc_account_id'] = $db->real_escape_string($tracker_row['ppc_account_id']);
$mysql['user_pref_dynamic_bid'] = $db->real_escape_string($tracker_row['user_pref_dynamic_bid']);
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
$mysql['text_ad_id'] = $db->real_escape_string($tracker_row['text_ad_id']);


$keyword = get_keyword();

$keyword_id = INDEXES::get_keyword_id($db, $keyword); 
$mysql['keyword_id'] = $db->real_escape_string($keyword_id); 
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

$custom_var_ids = array();

$ppc_variable_ids = explode(',', $tracker_row['ppc_variable_ids']);
$parameters = explode(',', $tracker_row['parameters']);

foreach ($parameters as $key => $value) {
	$variable = $db->real_escape_string($_GET[$value]);

	if (isset($variable) && $variable != '') {
		$variable = str_replace('%20',' ',$variable);
		$variable_id = INDEXES::get_variable_id($db, $variable, $ppc_variable_ids[$key]);
		$custom_var_ids[] = $variable_id;
	} 
}

//utm_source
$utm_source = $db->real_escape_string($_GET['utm_source']);
if(isset($utm_source) && $utm_source != '')
{
$utm_source = str_replace('%20',' ',$utm_source);
$mysql['utm_source']=$db->real_escape_string($utm_source);
}


//utm_medium
$utm_medium = $db->real_escape_string($_GET['utm_medium']);
if(isset($utm_medium) && $utm_medium != '')
{
    $utm_medium = str_replace('%20',' ',$utm_medium);
    $mysql['utm_medium']=$db->real_escape_string($utm_medium);
}

//utm_campaign
$utm_campaign = $db->real_escape_string($_GET['utm_campaign']);
if(isset($utm_campaign) && $utm_campaign != '')
{
    $utm_campaign = str_replace('%20',' ',$utm_campaign);
    $mysql['utm_campaign']=$db->real_escape_string($utm_campaign);
}

//utm_term
$utm_term = $db->real_escape_string($_GET['utm_term']);
if(isset($utm_term) && $utm_term != '')
{
    $utm_term = str_replace('%20',' ',$utm_term);
    $mysql['utm_term']=$db->real_escape_string($utm_term);
}

//utm_content
$utm_content = $db->real_escape_string($_GET['utm_content']);
if(isset($utm_content) && $utm_content != '')
{
    $utm_content = str_replace('%20',' ',$utm_content);
    $mysql['utm_content']=$db->real_escape_string($utm_content);
}
     
$mysql['click_in'] = 1;
$mysql['click_out'] = 1; 

//because this is a simple landing page, set click_alp (which stands for click advanced landing page, equal to 0)
$mysql['click_alp'] = 0;


//before we finish filter this click
$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
$user_id = $tracker_row['user_id'];

//GEO Lookup
$GeoData = getGeoData($ip_address);

$mysql['country'] = $db->real_escape_string($GeoData['country']);
$mysql['region'] = $db->real_escape_string($GeoData['city']);
$mysql['city'] = $db->real_escape_string($GeoData['city']);

if ($tracker_row['maxmind_isp'] == '1') {
	$IspData = getIspData($ip_address);
}



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

// if user wants to use t202ref from url variable use that first if it's not set try and get it from the ref url
if ($tracker_row['user_pref_referer_data'] == 't202ref') {
    if (isset($_GET['t202ref']) && $_GET['t202ref'] != '') { //check for t202ref value
        $mysql['t202ref']= $db->real_escape_string($_GET['t202ref']);
    }
} else { //user wants the real referer first

    // now lets get variables for clicks site
    // so this is going to check the REFERER URL, for a ?url=, which is the ACUTAL URL, instead of the google content, pagead2.google....
    if ($referer_query['url']) {
       $mysql['t202ref']= $referer_query['url'];
    } else {
        $mysql['t202ref']= $_SERVER['HTTP_REFERER'];
    }
}


$outbound_site_url = 'http://'.$_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

if ($cloaking_on == true) {
	$cloaking_site_url = 'http://'.$_SERVER['SERVER_NAME'] .dirname($_SERVER['PHP_SELF']). '/cl.php?pci=' . $click_id_public;      
}

//rotate the urls
$redirect_site_url = rotateTrackerUrl($db, $tracker_row);
$redirect_site_url = replaceTrackerPlaceholders($db, $redirect_site_url,$click_id,$mysql);

 
//set the cookie
setClickIdCookie($mysql['click_id'],$mysql['aff_campaign_id']);


//get and prep extra stuff for pre-pop or data passing
$urlvars = getPrePopVars($_GET);

//now we've recorded, now lets redirect them
if ($cloaking_on == true) {
	//if cloaked, redirect them to the cloaked site. 
	header('location: '.setPrePopVars($urlvars,$cloaking_site_url,true));
} else {
	header('location: '.setPrePopVars($urlvars,$redirect_site_url,false));
} 

die();

function get_keyword()
{
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
    
    return $keyword;
}
