<?php
if(empty($_SESSION['MM_Username'.$secret_passphrase]) and $_SESSION['MM_Username'.$secret_passphrase] != "okuser"){
exit;
};

$pool 		 = get("pool");
$array_pools = array();

$number = 1; // Middlecoin
$array_pools[$number]["id"] 		= $number;
$array_pools[$number]["url"][]		= "stratum+tcp://middlecoin.com:3333";
$array_pools[$number]["url"][]		= "stratum+tcp://uswest.middlecoin.com:3333";
$array_pools[$number]["url"][]		= "stratum+tcp://useast.middlecoin.com:3333";
$array_pools[$number]["url"][]		= "stratum+tcp://eu.middlecoin.com:3333";
$array_pools[$number]["url"][]		= "stratum+tcp://amsterdam.middlecoin.com:3333";
$array_pools[$number]["url"][]		= "stratum+tcp://asia.middlecoin.com:3333";

$number = 2; // Clever
$array_pools[$number]["id"] 		= $number;
$array_pools[$number]["url"][]		= "stratum+tcp://us.clevermining.com:3333";
$array_pools[$number]["url"][]		= "stratum+tcp://eu.clevermining.com:3333";
$array_pools[$number]["url"][]		= "stratum+tcp://sf.clevermining.com:3333";

$number = 3; // WafflePool
$array_pools[$number]["id"] 		= $number;
$array_pools[$number]["url"][]		= "stratum+tcp://useast.wafflepool.com:3333";
$array_pools[$number]["url"][]		= "stratum+tcp://uswest.wafflepool.com:3333";
$array_pools[$number]["url"][]		= "stratum+tcp://eu.wafflepool.com:3333";
$array_pools[$number]["url"][]		= "stratum+tcp://sea.wafflepool.com:3333";

$number = 4; // HashCows
$array_pools[$number]["id"] 		= $number;
$array_pools[$number]["url"][]		= "stratum+tcp://stratum01.hashco.ws:8888";

$number = 5; // Coinshift
$array_pools[$number]["id"] 		= $number;
$array_pools[$number]["url"][]		= "stratum+tcp://mine.coinshift.com:3333";
$array_pools[$number]["url"][]		= "stratum+tcp://us-west.coinshift.com:3333";

$number = 7; // WeMineALL
$array_pools[$number]["id"] 		= $number;
$array_pools[$number]["url"][]		= "stratum+tcp://multi1.wemineall.com:5555";
$array_pools[$number]["url"][]		= "stratum+tcp://multi1.wemineall.com:80";
$array_pools[$number]["url"][]		= "stratum+tcp://multi2.wemineall.com:5555";
$array_pools[$number]["url"][]		= "stratum+tcp://multi2.wemineall.com:80";

$number = 8; // CoinFu
$array_pools[$number]["id"] 		= $number;
$array_pools[$number]["url"][]		= "stratum+tcp://pool.coinfu.io:3333";

$number = 9; // Multipool
$array_pools[$number]["id"] 		= $number;
$array_pools[$number]["url"][]		= "stratum+tcp://us-east.multipool.us:7777";
$array_pools[$number]["url"][]		= "stratum+tcp://us-east2.multipool.us:7777";
$array_pools[$number]["url"][]		= "stratum+tcp://us-west.multipool.us:7777";
$array_pools[$number]["url"][]		= "stratum+tcp://us-west2.multipool.us:7777";
$array_pools[$number]["url"][]		= "stratum+tcp://eu.multipool.us:7777";
$array_pools[$number]["url"][]		= "stratum+tcp://eu2.multipool.us:7777";

$number = 10; // BlackCoin 
$array_pools[$number]["id"] 		= $number;
$array_pools[$number]["url"][]		= "stratum+tcp://useast.blackcoinpool.com:3333";

$number = 11; // NiceHash
$array_pools[$number]["id"] 		= $number;
$array_pools[$number]["url"][]		= "stratum+tcp://stratum.nicehash.com:3333";

$pool_number = 9999;

foreach($array_pools as $pools){
	foreach($pools["url"] as $url){
		if(($url) == ($pool)) $pool_number = $pools["id"];
	};
};

$filename = '../log/poolpicker.tmp';
if (file_exists($filename)) {
	$datum = date ("YmdHi", filemtime($filename));
}
$datumnu = date ("YmdHi");
	
$tijd = $datumnu - $datum;
if($tijd > 30){
	$html 		= file_get_contents_curl("http://poolpicker.eu/api");
	$myFile 	= '../log/poolpicker.tmp';
	$fh 		= fopen($myFile, 'w') or die("can't open file");
	fwrite($fh, $html);
	fclose($fh);
}else{
	$myFiler 	= '../log/poolpicker.tmp';
	$fhr 		= fopen($myFiler, 'r');
	$html 		= fread($fhr, filesize($myFiler));
	fclose($fhr);
};

$data = json_decode($html,TRUE);
$value = $data["average"][0]["pool".$pool_number];
if($value == "" or $value == "-"){
	echo $value;
}else{
	echo number_format($value, 8, '.', '');
};