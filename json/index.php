<?php

	$include_dir = "../";
	
	include_once($include_dir."_configfile.php");
	include_once($include_dir."_include/_functions.php");
	include_once($include_dir.'_include/_cryptastic.php');

	$action = mysql_escape_string(get("action","home"));
	$action = $action . ".php";

	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 01 Jul 1980 05:00:00 GMT');
	header('Access-Control-Allow-Origin: *'); 
	header('Content-type: application/json');

	$file = "_actions/" . $action;

	if(file_exists($file)){
		include $file;
	}else{
		header("HTTP/1.0 404 Not Found");
	};
