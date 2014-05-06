<?php
if(empty($_SESSION['MM_Username'.$secret_passphrase]) and $_SESSION['MM_Username'.$secret_passphrase] != "okuser"){
exit;
};

	$algo 		= mysql_escape_string(get('algo'));
	$result 	= $_REQUEST["table-".$algo];
	$i = 0;

	foreach($result as $value) {
		if($value<> "")	$result2[] = $value;
	};
	$count = count($result2);

	$sql = "SELECT sortorder FROM cgmonitor__groupsvspools WHERE idgroups = '".$algo."' order by sortorder;";
	$result = result($sql);
	
	foreach($result as $sort){
		$old_sort[] = $sort["sortorder"];
	};

	$i = 0;
	foreach($result2 as $value) {
		$query ="UPDATE cgmonitor__groupsvspools SET `sortorder`='".$old_sort[$i]."'  WHERE idpools='".$value."' and idgroups='".$algo."';";
		result($query);
		$i++;

	};
	
	echo "";//json_encode($retun);
