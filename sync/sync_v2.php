<?php

	$include_dir = "../";
	
	include_once($include_dir."_configfile.php");
	include_once($include_dir."_include/_functions.php");
	include_once($include_dir.'_include/_cryptastic.php');

	$realpath 	= realpath(dirname(__FILE__));
	
	$uniqueid	 		= $url_parts[0];
	if((substr($uniqueid,0,1)) == "?") $uniqueid = get("uniqueid");

	$sql 				= "SELECT id, privatekey FROM `cgmonitor__rigs` WHERE uniqueid = '".mysql_escape_string($uniqueid)."';";
	$privatekey_result 	= result($sql);
	$privatekey 		= $privatekey_result[0]["privatekey"];
	$idrig		 		= $privatekey_result[0]["id"];
	$timenow 			= date("YmdHis");

	if($privatekey == ""){
		echo "Error minerhash";
		exit;
	};
	
	$key_post = $_POST["key"];
	if($key_post <> ""){
	
		$base_url 		= str_replace("sync_v2.php", "", $base_url);
		$base_url 		= str_replace("sync.php", "", $base_url);
		$url			= 'http://'. $_SERVER['SERVER_NAME'] .$base_url. '../_cron/cron.php';
		$result_http	= file_get_contents($url);

		// ontvang key en zet hem in de tabel
		$cryptastic 		= new cryptastic;
		$key_dec 			= $cryptastic->pbkdf2($privatekey, $uniqueid, 1000, 32);
		$data_key_receive	= $cryptastic->decrypt($key_post,$key_dec,true);
		
		$key_receive 		= $data_key_receive["key"];
		$timeserver 		= $data_key_receive["time"];
		
		$sql = "UPDATE `cgmonitor__rigs` SET `lastkey` = '".$key_receive."' WHERE uniqueid = '".mysql_escape_string($uniqueid)."';";
		result($sql);

		// get the scheduledactions
				
		$sql_sch 			= "SELECT * FROM `cgmonitor__actions` WHERE status = 'scheduled' and time < '".$timenow."' order by time;";
		$actions_sch		= result($sql_sch);
	
		foreach($actions_sch as $action_sch){
			$command_dec_sch	= decryptCommand($action_sch["command"],$privatekey);
			$commands_sch 		= explode(";", $command_dec_sch);

			$gpu_sch 			= $commands_sch[2];
			$int_sch 			= $commands_sch[3];
			$pool_sch 			= $commands_sch[1];
			$command_sch		= $commands_sch[0];
			$repeat_sch			= $commands_sch[4];

			if($repeat_sch == 0){
				$sql_del_sch	= "DELETE FROM `cgmonitor__actions` WHERE id = '".$action_sch["id"]."';";
				result($sql_del_sch);
			}else{
				$year 	= substr($action_sch["time"],  0, 4);
				$month 	= substr($action_sch["time"],  4, 2);
				$day 	= substr($action_sch["time"],  6, 2);
				$hour 	= substr($action_sch["time"],  8, 2);
				$min 	= substr($action_sch["time"], 10, 2);
				$sec 	= substr($action_sch["time"], 12, 2);
				
				$timenew_sch	= date("YmdHis",mktime ($hour, $min + $repeat_sch ,$sec, $month , $day , $year ));

				$sql_upd	= "UPDATE `cgmonitor__actions` SET time = '".$timenew_sch."' WHERE id = '".$action_sch["id"]."';";
				result($sql_upd);

			};
			
			save_action(array("automanual"=>"Timed","command"=>$command_sch,"rig"=>$idrig,"pool"=>$pool_sch,"gpu"=>$gpu_sch,"int"=>$int_sch));
		};
		
		// get the actions
		
		$result_key = array();
		$sql 				= "SELECT * FROM `cgmonitor__actions` WHERE status = 'open' and idrigs = '".$idrig."' order by time,id;";
		$actions		 	= result($sql);
	
		foreach($actions as $action){
			$result_key["commands"][$action["id"]]["id"] 		= $action["id"];
			$result_key["commands"][$action["id"]]["command"] 	= $action["command"];
			if($action["command"] == "reboot"){
				$sql = "UPDATE `cgmonitor__actions` set status = 'closed', timeclosed = '".$timeserver."' WHERE id = '". $action["id"]."';";
				result($sql);
				break;
			}
		};
		
		$result_key["result"] 	= "key_good";
		$result_key["api"]	 	= array('devs', 'devdetails', 'summary', 'pools');

		$result_key 	= json_encode($result_key);
		$key_sent	 	= $cryptastic->pbkdf2($privatekey, $key_receive, 1000, 32);
		$result_key	 	= $cryptastic->encrypt($result_key,$key_sent,true);

		echo $result_key;
		exit;
	};

	$command_post = $_POST["command"];
	if($command_post <> ""){
		// ontvang key en zet hem in de tabel
		$sql 			= "SELECT * FROM `cgmonitor__rigs` WHERE uniqueid = '".mysql_escape_string($uniqueid)."';";
		$rigs	 		= result($sql);
		
		$salt 			= $rigs[0]['lastkey'];
		$idrig	 		= $rigs[0]['id'];
	
		$cryptastic = new cryptastic;
		$key 	= $cryptastic->pbkdf2($privatekey, $salt, 1000, 32);
		$data 	= $cryptastic->decrypt($command_post,$key,true);
		
		$data = json_decode($data,true);

		$commands 	= $data["command"];
		$timeserver = $data["time"];
		
		foreach($commands as $command){
			if($command["status"] == "ok_command"){
				$sql = "UPDATE `cgmonitor__actions` set status = 'closed', timeclosed = '".$timeserver."' WHERE id = '". $command["id"]."';";
				result($sql);
			};
		};
		
		$result_key["result"] 	= "key_good";

		$result_key = json_encode($result_key);
		$result_key	 = $cryptastic->encrypt($result_key,$key,true);

		echo $result_key;
		exit;
	};
	
	$data = $_POST["data"];
	if($data <> ""){
		
		$timenow 		= date("YmdHis");
		$datesave 		= date("Ymd");
		$timeremove 	= date("YmdHis",mktime (date("H"), date("i") ,date("s"), date("m") , date("d")-4 ,date("Y") ));
		$min    = date("i",mktime (0, date("i") ,0, 0 , 0 ,0 ));

		$sql = "SELECT * FROM `cgmonitor__rigs` WHERE uniqueid = '".mysql_escape_string($uniqueid)."';";
		$lastkey = result($sql);
		
		$salt 			= $lastkey[0]['lastkey'];
		$idrig	 		= $lastkey[0]['id'];
		$lastsyncpoolid	= $lastkey[0]['lastsyncpoolid'];
		$autosync		= $lastkey[0]['autosync'];
		$lasthashpools	= $lastkey[0]['lasthashpools'];
	
		$cryptastic = new cryptastic;
		$key = $cryptastic->pbkdf2($privatekey, $salt, 1000, 32);
		$data = $cryptastic->decrypt($data,$key,true);

		
		$data = json_decode($data,true);
		
		$devs_fields 	= array('Enabled','Status','Temperature','Fan Speed','Fan Percent','GPU Clock','Memory Clock','GPU Voltage','GPU Activity','Powertune','MHS av','MHS 1s','Accepted','Rejected','Hardware Errors','Utility','Intensity','Total MH','Diff1 Work','Difficulty Accepted','Difficulty Rejected','Last Share Difficulty','Last Valid Work','Device Hardware%','Device Rejected%','WUs','Elapsed','Last Share Pool');
		$pool_fields 	= array('POOL','URL','Status','Priority','User');
			
		$DEVS_UN	= $data["devs"];
		$POOLS_UN	= $data["pools"];
		$timeserver = $data["time"];
		
		if(isset($data["devs"])){
			$poolcount	= str_replace(" Pool(s)", "", $POOLS_UN["STATUS"]["Msg"]);
			
			$i = 0;
			foreach($DEVS_UN as $result_det){
				if(isset($result_det["GPU"])){
					$DEVS[$i]["DEVS"] = $result_det["GPU"];
				}else{
					$DEVS[$i]["DEVS"] = $result_det["ID"];
				};
				if(isset($result_det["Enabled"])){
					foreach($devs_fields  as $fields){
						if($fields == "MHS 1s"){
							if(isset($result_det["MHS 1s"])) $value = $result_det["MHS 1s"];
							if(isset($result_det["MHS 5s"])) $value = $result_det["MHS 5s"];
						
							$DEVS[$i][$fields] = $value;					
						}else{
							$DEVS[$i][$fields] = $result_det[$fields];
						};
					};
					$i++;
				};
			};		

			$i = 0;
			$pooladdress = "";

			foreach($POOLS_UN as $result_det){
				if(isset($result_det["POOL"])){
					$pooladdress 			.= $result_det["URL"];
					foreach($pool_fields  as $fields){
						$POOLS[$i][$fields] = $result_det[$fields];
						
					};
					$i++;
				};
			};
			
			$result_key = array();			

			$MHSav_total 	= 0;
			$MHS1s_total 	= 0;
			$DRej_total 	= 0;
			$WUs_total 		= 0;
			$LSP_total 		= 0;
			$devices_count	= 0;
			foreach($DEVS as $DEV){
				$devices_count++;
				if(mysql_num_rows(mysql_query("SHOW TABLES LIKE 'miner_".$idrig."_DEVS_".$DEV["DEVS"]."'"))==1){ 
					//echo "Table exists";
				}else{ 
				
					$sql 				= "INSERT INTO  `cgmonitor__devs` ( `id` ,`idrigs` ,`number`,`device`)VALUES (NULL ,  '".$idrig."',  '".$DEV["DEVS"]."','GPU');";
					$result 			= result($sql);

					$sql_make 			= "CREATE TABLE IF NOT EXISTS `miner_".$idrig."_DEVS_".$DEV["DEVS"]."` (`Time` varchar(200) DEFAULT NULL,`DEVS` varchar(100) DEFAULT NULL, `Enabled` varchar(5) DEFAULT NULL,`Status` varchar(100) DEFAULT NULL,`Temperature` varchar(100) DEFAULT NULL,`Fan Speed` varchar(100) DEFAULT NULL,`Fan Percent` varchar(100) DEFAULT NULL,`GPU Clock` varchar(100) DEFAULT NULL,`Memory Clock` varchar(100) DEFAULT NULL,`GPU Voltage` varchar(100) DEFAULT NULL,`GPU Activity` varchar(100) DEFAULT NULL,`Powertune` varchar(100) DEFAULT NULL,`MHS av` varchar(100) DEFAULT NULL,`MHS 1s` varchar(100) DEFAULT NULL,`Accepted` varchar(100) DEFAULT NULL,`Rejected` varchar(100) DEFAULT NULL,`Hardware Errors` varchar(100) DEFAULT NULL,`Utility` varchar(100) DEFAULT NULL,`Intensity` varchar(100) DEFAULT NULL,`Total MH` varchar(100) DEFAULT NULL,`Diff1 Work` varchar(100) DEFAULT NULL,`Difficulty Accepted` varchar(100) DEFAULT NULL,`Difficulty Rejected` varchar(100) DEFAULT NULL,`Last Share Difficulty` varchar(100) DEFAULT NULL,`Last Valid Work` varchar(100) DEFAULT NULL,`Device Hardware%` varchar(100) DEFAULT NULL,`Device Rejected%` varchar(100) DEFAULT NULL,`WUs` varchar(100) DEFAULT NULL,`Last Share Pool` varchar(100) DEFAULT NULL,`Last Share Time` varchar(100) DEFAULT NULL,`Elapsed` varchar(100) DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
					$result_make 		= result($sql_make);
				};

				$sql = "SELECT * FROM `cgmonitor__devs` WHERE number = '".$DEV["DEVS"]."' and idrigs = '".$idrig."'";
				$devices = result($sql);
					
				$sql = "DELETE FROM miner_".$idrig."_DEVS_".$DEV["DEVS"]." WHERE `Time` < '".$timeremove."';";
				result($sql);

				$fields  = "`DEVS`,";
				$value   = "'".$DEV["DEVS"]."',";
				$fields .= "`Time`,";
				$value  .= "'".$timenow."',";
				foreach($devs_fields as $val){
					$fields .= "`".$val."`,";
					if($val == "Elapsed"){
						$value  .= "'".$data["summary"]["SUMMARY"]["Elapsed"]."',";
					}else if($val == "Last Share Pool"){
						$value  		.= "'".$POOLS[$DEV["Last Share Pool"]]["URL"]."',";
						$poollasturl 	= $POOLS[$DEV["Last Share Pool"]]["URL"];
						$poollast	 	= $DEV["Last Share Pool"];
					}else if($val == "WUs"){
						if($devices[0]['device']=="GPU"){
							$WUs	 	= 1 * $DEV["Diff1 Work"];
							$time 		= 1 * $data["summary"]["SUMMARY"]["Elapsed"] / 60;
							if($WUs <> 0){
								$WUs 	= round($WUs / $time) / 1000;
							};
						}else if($devices[0]['device']=="GRIDSEED"){
							$WUs 		= round($DEV["MHS 1s"] * 1000 * 0.9) / 1000;
							$WUs 		= round($DEV["Utility"] * $DEV["Last Share Difficulty"]) / 1000;
						};
						
						$value  .= "'". $WUs ."',";
					}else if($val == "Device Rejected%"){
						if($devices[0]['device']=="GPU"){
							$rej 		= $DEV[$val];
						}else if($devices[0]['device']=="GRIDSEED"){
							$rej   	= ( $DEV["Rejected"] / $DEV["Accepted"] ) * 100;
						};				
						$value  .= "'". $rej  ."',";
					}else{
						$value  .= "'". $DEV[$val] ."',";
					};
					
				};
				$fields = substr($fields, 0, -1);
				$value  = substr($value , 0, -1);
				
				$sql = "INSERT INTO miner_".$idrig."_DEVS_".$DEV["DEVS"]." (".$fields.") VALUES (".$value.");";
				result($sql);

				$sql = "UPDATE cgmonitor__rigs set `poollasturl` = '".$poollasturl."',`poolcount` = '".$poolcount."',`poollast` = '".$poollast."' WHERE `id` = '".$idrig."'";
				result($sql);

				$MHSav_total 	= $MHSav_total 	+ $DEV['MHS av'];
				$MHS1s_total 	= $MHS1s_total 	+ $DEV['MHS 1s'];
				$WUs_total 		= $WUs_total 	+ $WUs;
				$DRej_total 	= $DRej_total 	+ $rej;
				$LSP_total 		= $POOLS[$DEV["Last Share Pool"]]["URL"];
				
			
				if($devices[0]["autointensity"] == "yes"){
					/*
					
					//
					//  Temp check for the last 15 min.
					//
					
					*/
					
					
					$devs  = $DEV["DEVS"];
					$temp  = $DEV["Temperature"];
					$fanp  = $DEV["Fan Percent"];
					$int   = $DEV["Intensity"];

					$tempfile = $realpath . "/temp/averagetemp-".$idrig."-".$DEV["DEVS"].".save";
					
					$avetemp = file_get_contents($tempfile);
					$avetemp = json_decode($avetemp, true);
					
					$temp_array 	= array();
					$teller_temp 	= 1;
					$temp_average = 0;
					foreach($avetemp as $tempave){
						if($teller_temp < 10){
							$temp_array[$teller_temp] = $tempave;
							$temp_average 		 = $temp_average + $tempave;
							$teller_temp++;
						};
					};
					$temp_array[0] 	= $temp;
					$temp_average 	= ($temp_average + $temp) / $teller_temp;
					ksort($temp_array);
					file_put_contents($tempfile, json_encode($temp_array));

					$fanpfile = $realpath . "/temp/averagefan-".$idrig."-".$DEV["DEVS"].".save";
					$avetemp = file_get_contents($fanpfile);
					$avetemp = json_decode($avetemp, true);
					
					$temp_array = array();
					$teller_fanp = 1;
					$fanp_average = 0;
					foreach($avetemp as $tempave){
						if($teller_fanp < 8){
							$temp_array[$teller_fanp] = $tempave;
							$fanp_average 		 = $fanp_average + $tempave;
							$teller_fanp++;
						};
					};
					
					$temp_array[0] 	= $fanp;
					$fanp_average 	= ($fanp_average + $temp) / $teller_fanp;
					ksort($temp_array);
					file_put_contents($fanpfile, json_encode($temp_array));

					
					$updateintensity = true;
					if($teller_fanp < 4 or $teller_temp < 4) $updateintensity = false;
					
					if($updateintensity){
						
						$subject = "";
						$updated = false;
						
						if($fanp_average > 	$devices[0]["maxfan"]){
							$intset = $int - 1;
							if($intset >= $devices[0]["minint"]){
								save_action(array("automanual"=>"Automatic","command"=>"intensity","rig"=>$idrig,"gpu"=>$DEV["DEVS"],"int"=>$intset));
								if(file_exists($tempfile)) unlink($tempfile);
								if(file_exists($fanpfile)) unlink($fanpfile);
								$updated = true;
							};
						}
						if($temp_average > $devices[0]["maxtemp"]){
							$intset = $int - 1;
							if($intset >= $devices[0]["minint"] and $updated != true){
								save_action(array("automanual"=>"Automatic","command"=>"intensity","rig"=>$idrig,"gpu"=>$DEV["DEVS"],"int"=>$intset));
								if(file_exists($tempfile)) unlink($tempfile);
								if(file_exists($fanpfile)) unlink($fanpfile);
								$updated = true;
							};
						}
						if($fanp_average < 	$devices[0]["lowfan"]){
							$intset = $int + 1;
							if($intset <= $devices[0]["maxint"] and $updated != true){
								save_action(array("automanual"=>"Automatic","command"=>"intensity","rig"=>$idrig,"gpu"=>$DEV["DEVS"],"int"=>$intset));
								if(file_exists($tempfile)) unlink($tempfile);
								if(file_exists($fanpfile)) unlink($fanpfile);
								$updated = true;
							};
						}
						if($temp_average < $devices[0]["lowtemp"]){
							$intset = $int + 1;
							if($intset <= $devices[0]["maxint"] and $updated != true){
								save_action(array("automanual"=>"Automatic","command"=>"intensity","rig"=>$idrig,"gpu"=>$DEV["DEVS"],"int"=>$intset));
								if(file_exists($tempfile)) unlink($tempfile);
								if(file_exists($fanpfile)) unlink($fanpfile);
								$updated = true;
							};
						}
					};
					//end temp check last 15 min
				};
			};

			$sql = "DELETE FROM miner_".$idrig." WHERE `Time` < '".$timeremove."';";
			result($sql);
			
			$DRej_total = $DRej_total / $devices_count;
			$sql_insert = "INSERT INTO `miner_".$idrig."` (`Time`, `MHS av`, `MHS 1s`, `Device Rejected%`, `WUs`, `Last Share Pool`) VALUES ('".$timenow."','".$MHSav_total."','".$MHS1s_total."','".$DRej_total."','".$WUs_total."','".$LSP_total."');";
			result($sql_insert);
			
			if($pooladdress != ""){
				$pooladdress  	= md5($pooladdress);
				
				if($autosync == "yes" and $pooladdress != $lasthashpools){
					if($lastsyncpoolid != ""){
						save_action(array("automanual"=>"Automatic","command"=>"switch","rig"=>$idrig,"pool"=>$lastsyncpoolid));
					};
				};
			};
			
			$sql 				= "SELECT * FROM `cgmonitor__actions` WHERE status = 'open' and idrigs = '".$idrig."' order by time;";
			$actions		 	= result($sql);
		
			foreach($actions as $action){
				$result_key["commands"][$action["id"]]["id"] 		= $action["id"];
				$result_key["commands"][$action["id"]]["command"] 	= $action["command"];
				if($action["command"] == "reboot"){
					$sql = "UPDATE `cgmonitor__actions` set status = 'closed', timeclosed = '".$timeserver."' WHERE id = '". $action["id"]."';";
					result($sql);
					break;
				}
			};
		};			
		$result_key["result"] 	= "key_good";

		
		$result_key = json_encode($result_key);
		$result_key	 = $cryptastic->encrypt($result_key,$key,true);
		
		echo $result_key;
		exit;
	};

	echo "Error";
	exit;

?>
