<?php
// set the basic stuff for each page
session_start();

//error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(E_ERROR);
ini_set('display_errors','on');
date_default_timezone_set("Etc/UTC");

set_include_path(implode(PATH_SEPARATOR, array(realpath(dirname(__FILE__)). "", get_include_path(),)));


// some securtiy settings
if ( (isset($_SESSION['LAST_ACTIVITY'.$secret_passphrase]) && (time() - $_SESSION['LAST_ACTIVITY'.$secret_passphrase] > 1800)) || $_SERVER['REMOTE_ADDR'] !== $_SESSION['PREV_REMOTEADDR'] || $_SERVER['HTTP_USER_AGENT'] !== $_SESSION['PREV_USERAGENT']) {
	// last request was more than 30 minates ago
	session_destroy();  # Nuke it
	unset($_SESSION);   # Delete its contents
	session_start();    # Create a new session
	session_regenerate_id(TRUE);  # Ensure it has a new id
	$token = makeToken();
}

$_SESSION['LAST_ACTIVITY'.$secret_passphrase] = time(); // update last activity time stamp
$_SESSION['PREV_USERAGENT'] = $_SERVER['HTTP_USER_AGENT'];
$_SESSION['PREV_REMOTEADDR'] = $_SERVER['REMOTE_ADDR'];


$url_temp 	= $_SERVER['REQUEST_URI'];
$urlbefore 	= str_ireplace("index.php", "", $_SERVER['PHP_SELF']);
$realpath 	= realpath(dirname(__FILE__));

if($urlbefore == "/"){
	$url_temp = substr($url_temp,1);
}else{
	$url_temp = str_ireplace($urlbefore, "", $url_temp);
};
$url_parts = explode ("/", $url_temp);

$base_url = str_replace("index.php", "", $_SERVER['PHP_SELF']);
	
// connect to db in first palce
$link = connect($sqlserver,$username,$password,$database);
$section = "";

// get some settings

$timezone 		= get_setting("timezone");
$timezone 		= $timezone[1];
if($timezone == "") $timezone = 0;


// all other functions
function connect($Hostname, $Username, $Password,$Database){
		$link = mysql_connect($Hostname, $Username, $Password) or die ("Could not connect");
		mysql_select_db($Database) or die ("Could not select database WHERE");
		return $link;
};

function result($ssql){
		$link 	= $GLOBALS['link'];
		$result = mysql_query($ssql,$link);

		if (is_bool($result) === true) {
			$array_terug = array();
		}else{
			if($result){
				$i = 0;
				while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
					$array_terug[$i] = $row;
					$i++;
				};
			};
		};
		return $array_terug;
};


function prepareArray($array)
{
	foreach ($array as $subKey => $subArray)
	{
		foreach ($subArray as $key => $value)
			$post[$subKey][$key] = $value;
	}
	return($post);
}

function get($name,$standard = ""){
	if ( !isset ( $_GET[$name]) ) {
		if ( !isset ( $_POST[$name]) ) {
			$val = $standard;
		}else{
			$val = $_POST[$name];
		};
	}else{
		$val = $_GET[$name];
	};
	return $val;
};

function updatesql(){
		$currentdir = realpath(dirname(__FILE__));
		$sql	 	= file_get_contents($currentdir . '/update/mysql.sql');
		$sqls 		= explode(";", $sql);
		
		foreach($sqls as $sql){
			$sql2 = $sql . ";";
			result($sql2);
		};
};

function update(){
		$file = "update.zip";
		if (file_exists($file)) { unlink ($file); }
		
		$url  	= "http://www.cg-monitor.com/update/update.zip";
		$ch 	= curl_init();
		
		$agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';
		curl_setopt($curl, CURLOPT_USERAGENT, $agent);			
		curl_setopt($ch,CURLOPT_COOKIEJAR,realpath('.').'/log/cookieupdate.txt');
		curl_setopt($ch,CURLOPT_COOKIEFILE,realpath('.').'/log/cookieupdate.txt');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_REFERER, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$data = curl_exec($ch);
		curl_close($ch);
		
		file_put_contents($file, $data);
		
		$zip = new ZipArchive;
		if ($zip->open($file) === TRUE) {
			$zip->extractTo('.');			
			$zip->close();
			
			updatesql();
			if (file_exists($file)) { unlink ($file); }
			return true;
		} else {
			if (file_exists($file)) { unlink ($file); }
			return false;
		}
		
};

function checkupdate($force = false){
		$url  	= "http://www.cg-monitor.com/update/version.php";
		$ch 	= curl_init();
		
		$agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';
		curl_setopt($curl, CURLOPT_USERAGENT, $agent);			
		curl_setopt($ch,CURLOPT_COOKIEJAR,realpath('.').'/log/cookiecheckupdate.txt');
		curl_setopt($ch,CURLOPT_COOKIEFILE,realpath('.').'/log/cookiecheckupdate.txt');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_REFERER, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$server = curl_exec($ch);
		curl_close($ch);
		
		$sql2 = "SELECT * FROM `cgmonitor__settings` where name = 'lastupdate';";
		$local = result($sql2);
		$local = $local[0]["value1"];
		
		if($server > $local or $force == true){
			if(update()){
				$sql2 = "update `cgmonitor__settings` set value1 = '".$server."' where name = 'lastupdate';";
				result($sql2);			
				return true;
			}else{
				return false;
			};
		};
};

function generatesalt($length = 20) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
}

function returnpostvalue($name,$id){
	$value   = get($name . $id);
	$value   = str_replace("'", "`",trim($value));
	$value   = mysql_real_escape_string(urlencode($value));
	$value   = "`".$name."` = '".$value."'";
	return $value;
};

function returnpostvaluenew($name){
	$value   = get($name);
	$value   = str_replace("'", "`",trim($value));
	$value   = mysql_real_escape_string(urlencode($value));
	return $value;
};

function smalldaterevert($date,$timezone=0){
		$array_data		= array();
		
		$array_data["Y"]	= substr($date, 0, 4);
		$array_data["m"]	= substr($date, 4, 2);
		$array_data["d"]	= substr($date, 6, 2);
		$array_data["H"]	= substr($date, 8, 2) + $timezone;
		$array_data["i"]	= substr($date, 10, 2);

		$timepayoutz  		= mktime ($array_data["H"], $array_data["i"] ,"0", $array_data["m"] , $array_data["d"] ,$array_data["Y"]);

		$array_data			= array();
		$array_data["Y"]	= date("Y",$timepayoutz);
		$array_data["m"]	= date("m",$timepayoutz);
		$array_data["d"]	= date("d",$timepayoutz);
		$array_data["H"]	= date("H",$timepayoutz);
		$array_data["i"]	= date("i",$timepayoutz);
		
		return $array_data;
};

function makeToken(){
	$secret_passphrase		= $GLOBALS['secret_passphrase'];
	$token 				= md5(uniqid(rand(), true));
	$_SESSION['token'.$secret_passphrase] = $token;
	return $token;
};

function getToken(){
	$secret_passphrase		= $GLOBALS['secret_passphrase'];
	$tokens 			= $_SESSION[$form.'token'.$secret_passphrase];
	$_SESSION[$form.'token'.$secret_passphrase] = "";
	unset($_SESSION[$form.'token'.$secret_passphrase]);
	return $tokens;

};

function encryptCommand($command,$privatekey_rig){
	$cryptastic 		= new cryptastic;
	$secret_passphrase	= $GLOBALS['secret_passphrase'];
	$key		 		= $cryptastic->pbkdf2($privatekey_rig, $secret_passphrase, 1000, 32);
	$command_encrpt		= $cryptastic->encrypt($command,$key,true);

	return $command_encrpt;
};

function decryptCommand($command,$privatekey_rig){
	$cryptastic_cmd		= new cryptastic;
	$secret_passphrase	= $GLOBALS['secret_passphrase'];
	$key		 		= $cryptastic_cmd->pbkdf2($privatekey_rig, $secret_passphrase, 1000, 32);
	$command_encrpt		= $cryptastic_cmd->decrypt($command,$key,true);

	return $command_encrpt;
};

function save_Action($array_command){
	$include_dir 		= $GLOBALS["include_dir"];
	$back 				= include($include_dir."_include/save_action.php");
	return $back;
};

function secondsToTime($seconds) {
/*
	$hours 		= floor($seconds / 3600);
	$mins 		= floor(($seconds - ($hours*3600)) / 60);
	$secs 		= floor($seconds % 60);
	$ret		= $hours . ":". $mins.":".$secs;
	
	return $ret;
	exit;
*/
    $ret = '';
    $divs = array(86400, 3600, 60, 1);

    for ($d = 0; $d < 4; $d++)
    {
        $q = (int)($seconds / $divs[$d]);
        $r = $seconds % $divs[$d];
        $ret .= sprintf("%d%s", $q, substr('d::', $d, 1));
        $seconds = $r;
    }

    return $ret;
	
};

function file_get_contents_curl($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_REFERER, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$html = curl_exec($ch);
	curl_close($ch);
	return $html;
};

function xml_to_array($root) {
	$result = array();

    if ($root->hasAttributes()) {
        $attrs = $root->attributes;
        foreach ($attrs as $attr) {
            $result['@attributes'][$attr->name] = $attr->value;
        }
    }

    if ($root->hasChildNodes()) {
        $children = $root->childNodes;
        if ($children->length == 1) {
            $child = $children->item(0);
            if ($child->nodeType == XML_TEXT_NODE) {
                $result['_value'] = $child->nodeValue;
                return count($result) == 1
                    ? $result['_value']
                    : $result;
            }
        }
        $groups = array();
        foreach ($children as $child) {
            if (!isset($result[$child->nodeName])) {
                $result[$child->nodeName] = xml_to_array($child);
            } else {
                if (!isset($groups[$child->nodeName])) {
                    $result[$child->nodeName] = array($result[$child->nodeName]);
                    $groups[$child->nodeName] = 1;
                }
                $result[$child->nodeName][] = xml_to_array($child);
            }
        }
    }
	
    return $result;
};

function save_setting($name,$value1="NOT",$value2="NOT",$value3="NOT"){
	$sql_sett = "SELECT * FROM `cgmonitor__settings` WHERE `name` = '".$name."';";
	$settings = result($sql_sett);
	
	if(!(isset($settings[0]))){
		$sql_insert = "INSERT INTO `cgmonitor__settings` (`id`, `name`, `value1`, `value2`, `value3`) VALUES (NULL, '".$name."', '".$value1."', '".$value2."', '".$value3."');";
		result($sql_insert);
	};
	
	$update_value = "";
	if($value1 != "NOT") $update_value = "value1 = '".$value1."',";
	if($value2 != "NOT") $update_value = "value2 = '".$value2."',";
	if($value3 != "NOT") $update_value = "value3 = '".$value3."',";
	$update_value 	= substr($update_value, 0, -1);
	
	$sql_update = "UPDATE cgmonitor__settings set  ".$update_value." where name = '".$name."'";
	result($sql_update);
};

function get_setting($name,$value="all"){
	$array 			= array();

	$sql_user 		= "SELECT * FROM `cgmonitor__settings` where name = '".$name."';";
	$res_user 		= result($sql_user);
	$array[1]		= urldecode($res_user[0]["value1"]);
	$array[2]		= urldecode($res_user[0]["value2"]);
	$array[3]		= urldecode($res_user[0]["value3"]);
	
	return $array;
};

function string_encrypt($string, $key) {
    $crypted_text = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $string, MCRYPT_MODE_ECB);
    return $crypted_text;
}

function string_decrypt($encrypted_string, $key) {
    $decrypted_text = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $encrypted_string, MCRYPT_MODE_ECB);
    return trim($decrypted_text);
}
