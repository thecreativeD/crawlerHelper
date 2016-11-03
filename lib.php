<?php

//profiling function
function microtime_float ()
{
    list ($msec, $sec) = explode(' ', microtime());
    $microtime = (float)$msec + (float)$sec;
    return $microtime;
}

$cookiejar = NULL;
$cur_proxy = NULL;


//generic scrape function
function bot_crawl($url,$post='',$referrer='',$host ='',$cookie=''){
	
	global $cookiejar,$cur_proxy;
	
	$url = str_replace('&amp;','&',$url);
	
	$data = '';
	
    $crawl = curl_init();
	
	if(!$cookie){
		curl_setopt($crawl, CURLOPT_COOKIEJAR, $cookiejar );
		curl_setopt($crawl, CURLOPT_COOKIEFILE, $cookiejar);		
	}else{
		curl_setopt($crawl, CURLOPT_COOKIEJAR, $cookie );
		curl_setopt($crawl, CURLOPT_COOKIEFILE, $cookie);
	}
	
	
	if($cur_proxy){
		curl_setopt($crawl, CURLOPT_PROXY, $cur_proxy); 
		
	}	
	
	if($post){
		foreach($post as $key=>$value) { $data .= $key.'='.$value.'&'; }  
		rtrim($data,'&');
		curl_setopt($crawl, CURLOPT_POST, TRUE);
		curl_setopt($crawl, CURLOPT_POSTFIELDS, $data);
	}else{
		curl_setopt($crawl, CURLOPT_HTTPGET ,true);		
	}
	
	if($referrer){
		curl_setopt($crawl, CURLOPT_REFERER,$referrer);		
	}else curl_setopt($crawl, CURLOPT_REFERER, '');
		

    curl_setopt($crawl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1");
    curl_setopt($crawl, CURLOPT_TIMEOUT, 120);
    curl_setopt($crawl, CURLOPT_CONNECTTIMEOUT, 5);	
	curl_setopt($crawl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($crawl, CURLOPT_SSL_VERIFYHOST, false);	
    curl_setopt($crawl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($crawl, CURLOPT_URL, $url);
    curl_setopt($crawl, CURLOPT_FOLLOWLOCATION, TRUE);

	if($host){
		curl_setopt($crawl, CURLOPT_HTTPHEADER, array('Host: '.$host));
	}

    $hit = (curl_exec($crawl)); // execute the curl command

	curl_close ($crawl);
    unset($crawl); 
	
	return $hit;
}



function flush_cookie(){
	global $cookiejar;
	if(file_exists($cookiejar)){
		unlink($cookiejar);
	}
}




function grabImage($img,$fullpath,$referrer='',$cookie=''){

	global $cookiejar,$cur_proxy;
	
	$url = str_replace('&amp;','&',$url);	
	
	$data = '';
	
	$ch = curl_init ($img);
	
	if(!$cookie){
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiejar );
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiejar);		
	}else{
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie );
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
	}
	

	if($referrer){
		curl_setopt($ch, CURLOPT_REFERER,$referrer);		
	}
	
	if($cur_proxy){
		curl_setopt($ch, CURLOPT_PROXY, $cur_proxy); 		
	}

	if($host){
		curl_setopt($crawl, CURLOPT_HTTPHEADER, array('Host: '.$host));
	}

    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.6) Gecko/20100625 Ant.com Toolbar 2.0 Firefox/3.6.6");	
	curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
	$rawdata=curl_exec($ch);
	curl_close ($ch);
	if(file_exists($fullpath)){
		unlink($fullpath);
	}
	$fp = fopen($fullpath,'x');
	fwrite($fp, $rawdata);
	fclose($fp);
}

function cleanse($array){
	foreach( $array as $key=>$value){
		if(is_numeric($key))unset($array[$key]);
	}
	return $array;
}

function getHost($Address) {
   $parseUrl = parse_url(trim($Address));
   return trim($parseUrl[host] ? $parseUrl[host] : array_shift(explode('/', $parseUrl[path], 2)));
} 

function rand_SHA1(){
	$hex=array('0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f');
	$rand='';
	for($i=0; $i < 32; $i++){
		$rand.= $hex[(rand(0,512) % 15)];	
	}
	
	return $rand;
}

function lib_parse_str( $string, &$array ) {
	parse_str( $string, $array );
	if ( get_magic_quotes_gpc() )
		$array = stripslashes_deep( $array );
	$array = apply_filters( 'wp_parse_str', $array );
}

function parse_args( $args, $defaults = '' ) {
	if ( is_object( $args ) )
		$r = get_object_vars( $args );
	elseif ( is_array( $args ) )
		$r =& $args;
	else
		lib_parse_str( $args, $r );

	if ( is_array( $defaults ) )
		return array_merge( $defaults, $r );
	return $r;
}
?>
