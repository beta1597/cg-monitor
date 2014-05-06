<?php
if(empty($_SESSION['MM_Username'.$secret_passphrase]) and $_SESSION['MM_Username'.$secret_passphrase] != "okuser"){
exit;
};

	$name 		= mysql_escape_string(get('name'));
	$value1 	= mysql_escape_string(get('value1'));
	$value2		= mysql_escape_string(get('value2'));
	$value3 	= mysql_escape_string(get('value3'));

	save_setting($name,$value1,$value2,$value3);

	
	$return = "";
	echo "";//json_encode($retun);
