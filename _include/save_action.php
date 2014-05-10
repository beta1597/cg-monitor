<?php

	$command_ex		= $array_command["command"];
	$automanual		= $array_command["automanual"];
	$rig			= $array_command["rig"];
	$gpu 			= $array_command["gpu"];
	$int 			= $array_command["int"];
	$pool 			= $array_command["pool"];
	$algo 			= $array_command["algo"];
	
	$rig 			= mysql_escape_string($rig);
	$gpu 			= mysql_escape_string($gpu);
	$int 			= mysql_escape_string($int);
	
	$sql_rig			= "SELECT * FROM  `cgmonitor__rigs` where id = '".$rig."';";
	$result_rig			= result($sql_rig);
	$privatekey 		= $result_rig["0"]["privatekey"];
	$rig 				= $result_rig["0"]["id"];
	$worker		 		= $result_rig["0"]["worker"];
	$os		 			= $result_rig["0"]["os"];
	$poollasturl 		= $result_rig["0"]["poollasturl"];
	$poolcount 			= $result_rig["0"]["poolcount"];
	$poollast 			= $result_rig["0"]["poollast"];
	$changealgorithm 	= $result_rig["0"]["changealgorithm"];
	$multialgominer 	= $result_rig["0"]["multialgominer"];
	$lastkey 			= $result_rig["0"]["lastkey"];
		
	$timenow 		= date("YmdHis");
	$command_exec   = "";
	
	if($command_ex == "intensity"){
		$command_exec	= "gpuintensity|".$gpu.",".$int;
		$command_read	= $automanual . " intenstity: DEV:".$gpu." : INT ".$int;
		$command_enc	= encryptCommand($command_exec,$privatekey);
	}else if($command_ex == "algochange"){
			$sql_algo			= "SELECT name,number FROM  `cgmonitor__algorithm` where id = '".$algo."';";
			$res_algo			= result($sql_algo);

			if($os=="windows"){
				$command_exec = "quit§sleep|3000000§algochange|windows|".$res_algo[0]["number"]."§sleep|10000000§";
			}else if($os=="linux2"){
				$command_exec = "quit§sleep|3000000§algochange|linux|".$res_algo[0]["number"]."§sleep|10000000§";
			}else{
				$command_exec = "algochange|linux|".$res_algo[0]["number"]."§sleep|10000000§";
			};
			
		$command_read	= $automanual . " Algochange to " . $res_algo[0]["name"];
		$command_enc	= encryptCommand($command_exec,$privatekey);
				
	}else if($command_ex == "reboot"){
		$command_exec	= "reboot|specialaction";
		$command_read	= $automanual . " reboot";
		$command_enc	= encryptCommand($command_exec,$privatekey);
	}else if($command_ex == "resetstats"){
		$command_exec	= "zero|all,false";
		$command_read	= $automanual . " reset stats";
		$command_enc	= encryptCommand($command_exec,$privatekey);
	}else if($command_ex == "switch"){

		$sql2 				= "SELECT cgmonitor__pools.name as name,cgmonitor__algorithm.name as algorithm,cgmonitor__algorithm.id as number FROM `cgmonitor__pools` JOIN cgmonitor__algorithm ON cgmonitor__pools.idalgorithms =cgmonitor__algorithm.id where cgmonitor__pools.address = '".urlencode($poollasturl)."';";
		$poolslast			= result($sql2);
		$numberlast 		= $poolslast[0]["number"];

		$sql2 				= "SELECT cgmonitor__pools.*,cgmonitor__pools.name as name,cgmonitor__algorithm.id as number,cgmonitor__algorithm.name as algoname,cgmonitor__algorithm.multiname as algomultiname,cgmonitor__algorithm.rentpool as rentpool FROM `cgmonitor__pools` JOIN cgmonitor__algorithm ON cgmonitor__pools.idalgorithms =cgmonitor__algorithm.id where cgmonitor__pools.id = '".$pool."';";
		$poolsnew 			= result($sql2);
		$numbernew 			= $poolsnew[0]["number"];
		$rentpool 			= $poolsnew[0]["rentpool"];
		
		if($multialgominer == "yes"){
			$sql2 				= "SELECT cgmonitor__algorithm.id as number,cgmonitor__algorithm.name as algoname,cgmonitor__algorithm.multiname as algomultiname,cgmonitor__algorithm.rentpool as rentpool FROM cgmonitor__algorithm where cgmonitor__algorithm.id = '1';";
			$poolsmulti			= result($sql2);
			$numbernew 			= $poolsmulti[0]["number"];
			$rentpool 			= $poolsmulti[0]["rentpool"];
		};

		$sql2 				= "SELECT cgmonitor__pools.*,cgmonitor__pools.name as name,cgmonitor__algorithm.name as algoname,cgmonitor__algorithm.multiname as algomultiname FROM `cgmonitor__pools` JOIN cgmonitor__groupsvspools ON cgmonitor__pools.id =cgmonitor__groupsvspools.idpools JOIN cgmonitor__algorithm ON cgmonitor__pools.idalgorithms =cgmonitor__algorithm.id where cgmonitor__groupsvspools.idgroups = '".$numbernew."' order by cgmonitor__groupsvspools.sortorder;";
		$poolsbackup		= result($sql2);

	
		$newalgo = false;
		if($numberlast != $numbernew and $changealgorithm == "yes" and $multialgominer != "yes"){
		
			save_action(array("automanual"=>$automanual,"command"=>"algochange","rig"=>$rig,"algo"=>$numbernew));
			$newalgo = true;
		
		};

		
		$command = "removepool|0§sleep|100000§removepool|0§sleep|100000§removepool|0§sleep|100000§removepool|0§sleep|100000§removepool|0§sleep|100000§removepool|0§switchpool|0§sleep|100000§removepool|1§sleep|100000§removepool|1§sleep|100000§removepool|1§sleep|100000§removepool|1§sleep|100000§removepool|1§sleep|100000§removepool|1§sleep|100000§removepool|1§sleep|100000§removepool|1§sleep|100000§removepool|1§sleep|100000§removepool|1§sleep|100000§switchpool|0§sleep|100000§removepool|1§sleep|100000§removepool|1§sleep|100000§removepool|1§sleep|100000§removepool|1§sleep|100000§removepool|1§sleep|100000§removepool|1§sleep|100000§removepool|1§sleep|100000§removepool|1§sleep|100000§removepool|1§sleep|100000§removepool|1§sleep|100000§";

		$i = 0;
		foreach($poolsnew as $result_det){

			if($result_det["format"] == "noworker"){
				$worker_set = urldecode($result_det["user"]);
			}else if($result_det["format"] == "user.worker"){
				$worker_set = urldecode($result_det["user"]).".".$worker;
			}else if($result_det["format"] == "user_worker"){
				$worker_set = urldecode($result_det["user"])."_".$worker;
			}else if($result_det["format"] == "user-worker"){
				$worker_set = urldecode($result_det["user"])."-".$worker;
			};
			
			$pools[$i]["address"] 	= urldecode($result_det["address"]);
			if($multialgominer != "yes"){
				$pools[$i]["command"] 	= "addpool|".urldecode($result_det["address"]).",".$worker_set.",".urldecode($result_det["password"])."§sleep|1000000§";
			}else{
				$pools[$i]["command"] 	= "addpool|".urldecode($result_det["address"]).",".$worker_set.",".urldecode($result_det["password"]).",".urldecode($result_det["name"]).",".urldecode($result_det["name"]).",".strtolower(urldecode($result_det["algomultiname"]))."§sleep|1000000§";
			};
			$i++;
		};

	
		$i = 1;
		foreach($poolsbackup as $result_det){
			if($poolsnew[0]["id"] != $result_det["id"]){
				if($result_det["format"] == "noworker"){
					$worker_set = urldecode($result_det["user"]);
				}else if($result_det["format"] == "user.worker"){
					$worker_set = urldecode($result_det["user"]).".".$worker;
				}else if($result_det["format"] == "user_worker"){
					$worker_set = urldecode($result_det["user"])."_".$worker;
				}else if($result_det["format"] == "user-worker"){
					$worker_set = urldecode($result_det["user"])."-".$worker;
				};
				
				$pools[$i]["address"] 		= urldecode($result_det["address"]);
				if($multialgominer != "yes"){
					$pools[$i]["command"] 		= "addpool|".urldecode($result_det["address"]).",".$worker_set.",".urldecode($result_det["password"])."§sleep|1000000§";
				}else{
					$pools[$i]["command"] 		= "addpool|".urldecode($result_det["address"]).",".$worker_set.",".urldecode($result_det["password"]).",".urldecode($result_det["name"]).",".urldecode($result_det["name"]).",".strtolower(urldecode($result_det["algomultiname"]))."§sleep|1000000§";
				};
				$i++;
			};
		};
	
		$start = 1;
		if($rentpool == "yes") $start = 2;

		$address_backup = "";
		$command_backup = "";
		for($i2 = $start; $i2 < $i; $i2++){
			$address_backup .= $pools[$i2]["address"];
			$command_backup .= $pools[$i2]["command"];
		};
	
		$address_first	= $pools[0]["address"];
		$command_first 	= $pools[0]["command"];
		$address_rent	= "";
		$command_rent 	= "";
		
		if($rentpool == "yes"){
			$address_rent	= $pools[1]["address"];
			$command_rent 	= $pools[1]["command"];
		}
	
		if($rentpool == "yes"){
			 $pooladdress 	 = $address_rent . $address_first . $address_backup;
			 $command 		.= $command_first . $command_backup . $command_rent;
			 $command 		.= "switchpool|2§sleep|1000000§switchpool|1§sleep|1000000§removepool|0§sleep|1000000§";
		}else{
			 $pooladdress 	 =  $address_first . $address_backup;
			 $command 		.=  $command_backup . $command_first;
			 $command 		.= "switchpool|2§sleep|1000000§switchpool|1§sleep|1000000§switchpool|".$i."§sleep|1000000§removepool|0§sleep|1000000§";
		};
	
		$pooladdress 		= md5($pooladdress);
		$sql2 				= "UPDATE `cgmonitor__rigs` set lastsyncpoolid = '".$pool."',lasthashpools = '".$pooladdress."' where id = '".$rig."';";
		$rigs	 			= result($sql2);

		$command			= $command . "poolpriority|";
		for($i2 = 0; $i2 < $i; $i2++){
			 $command 		.= $i2 .",";
		};
		$command_exec 		= substr($command, 0, -1); 
		 
		$command_read		= $automanual . " : Switch to pool " . $poolsnew[0]["name"];
		$command_enc		= encryptCommand($command_exec,$privatekey);

	};

	
	if($command_exec != ""){
		$sql 				= "SELECT * FROM `cgmonitor__actions` WHERE `name` = '".$command_read."' and status = 'open' and idrigs = '".$rig."' order by time;";
		$actions		 	= result($sql);
	
		if(!(isset($actions[0]))){
			$sql2 = "INSERT INTO  `cgmonitor__actions` (`id` ,`idrigs` ,`name` ,`command` ,`time` ,`timeclosed` ,`status`) VALUES (NULL ,  '".$rig."', '".$command_read."' , '".$command_enc."',  '".$timenow."',  '',  'open');";
			result($sql2);
		};
	};
