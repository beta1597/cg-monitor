<?php
	if(empty($_SESSION['MM_Username'.$secret_passphrase]) and $_SESSION['MM_Username'.$secret_passphrase] != "okuser"){
	exit;
	};

	$rig			= get('rig');
	$gpu 			= get('gpu');
	$int 			= get('int');
	$pool 			= get("pool");
	$command		= get('command');
		
	save_action(array("automanual"=>"Manual","command"=>$command,"rig"=>$rig,"pool"=>$pool,"gpu"=>$gpu,"int"=>$int));