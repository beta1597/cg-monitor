<?php

	$include_dir = "./";
	
	include_once($include_dir."_configfile.php");
	include_once($include_dir."_include/_functions.php");
	include_once($include_dir.'_include/_cryptastic.php');
	
	$action 	= get("action","home");
	$hidepage 	= get("hidepage","no");		

	if(isset($_POST["action"])){
		if($_POST["action"] == "login"){
			include_once($include_dir."_include/_checklogin.php");
		};
	};

	if($hidepage == "no"){
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<title>CG-monitor</title>

	<link  media="screen" rel="stylesheet" type="text/css" href="./css/standard.css?<?php echo rand(11111,99999);?>"/>
	<link  media="screen" rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"/>
	<link  media="screen" rel="stylesheet" type="text/css" href="./dist/css/bootstrap.css"/>
	<link  media="screen" rel="stylesheet" type="text/css" href="./dist/css/bootstrap-select.min.css"/>
	<link  media="screen" rel="stylesheet" type="text/css" href="./dist/css/bootstrap-checkbox.css"/>
	<link  media="screen" rel="stylesheet" type="text/css" href="./dist/css/bootstrap-slider.css"/>
	<link  media="screen" rel="stylesheet" type="text/css" href="./dist/css/bootstrap-switch.css"/>
	<link  media="screen" rel="stylesheet" type="text/css" href="./dist/css/bootstrap-timepicker.css"/>
	<link  media="screen" rel="stylesheet" type="text/css" href="./dist/css/datepicker.css"/>

	<script language="JavaScript" type="text/javascript" src="./js/jquery-1.10.2.min.js"></script>
	<script language="JavaScript" type="text/javascript" src="./js/jquery-ui.js"></script>
	<script language="JavaScript" type="text/javascript" src="./dist/js/bootstrap.js"></script>
	<script language="JavaScript" type="text/javascript" src="./dist/js/bootstrap-select.min.js"></script>
 	<script language="JavaScript" type="text/javascript" src="./dist/js/bootstrap-checkbox.js"></script>
 	<script language="JavaScript" type="text/javascript" src="./dist/js/bootstrap-slider.js"></script>
	<script language="JavaScript" type="text/javascript" src="./dist/js/bootstrap-switch.js"></script>
	<script language="JavaScript" type="text/javascript" src="./dist/js/bootstrap-timepicker.js"></script>
	<script language="JavaScript" type="text/javascript" src="./dist/js/bootstrap-datepicker.js"></script>

	<script language="JavaScript" type="text/javascript" src="./js/jquery.tablednd.js"></script>
	<script language="JavaScript" type="text/javascript" src="./js/standard.php?<?php echo rand(11111,99999);?>"></script>
	<script language="JavaScript" type="text/javascript" src='//code.highcharts.com/stock/highstock.js'></script>
	<script language="JavaScript" type="text/javascript" src='//code.highcharts.com/stock/modules/exporting.js'></script>
</head>
<body>
<?php
if(empty($_SESSION['MM_Username'.$secret_passphrase]) and $_SESSION['MM_Username'.$secret_passphrase] != "okuser"){
	include($include_dir."_actions/login.php");
}else{
?>
<div class="container">
    <div class="navbar navbar-default" role="navigation">
      <div class="container">
        <div class="navbar-header" style="max-width:45px;float:right;">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
			<li><a href='?action=home'>Home</a></li>
			<li><a href='?action=pools'>Pools</a></li>
               <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Algorithm<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                      <li><a href="?action=algorithm&id=1">Scrypt</a></li>
                      <li><a href="?action=algorithm&id=2">N-Scrypt</a></li>
                      <li><a href="?action=algorithm&id=3">Keccak</a></li>
                    </ul>
                </li>
			<li><a href="?action=algorithmmulti">SG Miner 4.1</a></li>
			<li><a href="?action=scheduler">Scheduler</a></li>
			<li><a href='?action=actions'>Actions</a></li>
			<li><a href='?action=settings'>Settings</a></li>
			<li><a href='?action=update'>Update</a></li>
               <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Help<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                      <li><a href="?action=help-linux">Linux</a></li>
                      <li><a href="?action=help-windows">Windows</a></li>
                    </ul>
                </li>
			</ul>
 			<div style="float:right;margin:10px;"><a href="?action=logout&hidepage=yes"><button type="button" class="btn btn-default " title="Logout"><span class="glyphicon glyphicon-log-out"></span></button></a></div>
       </div>
      </div>
    </div>
	<div class="content" id = "content">
		<div class="contenttext" id = "contenttext">
			<span type="text" name="result" id="result"></span>
			&nbsp;<br/>
			<?php include($include_dir."_actions/".$action.".php"); ?>
			&nbsp;<br/>&nbsp;
		</div>
	</div>
</div>
<?php 
}; 
?>
</body>
</html>
<?php 
}else{
	include($include_dir."_actions/".$action.".php");
}; 
?>

