<?php
if(empty($_SESSION['MM_Username'.$secret_passphrase]) and $_SESSION['MM_Username'.$secret_passphrase] != "okuser"){
exit;
};
	
	$timenow 		= date("YmdHis");
	$timelast 		= mysql_escape_string(get("time"));
	$timeoffline 	= mktime (date("H"), date("i") - 20 ,date("s"), date("m") , date("d") ,date("Y"));
	$timeoffline	= date("YmdHis",$timeoffline);
	$force 			= mysql_escape_string(get("force"));

	if($force == "yes"){
		$force = true;
	}else{
		$force = false;
	};

				$sql_alerts = "SELECT * FROM `cgmonitor__alerts` where idrigs = '0';";
				$res_alerts = result($sql_alerts);

				$sql_pools 		= "SELECT * FROM `cgmonitor__pools` order by name;";
				$result_pools	= result($sql_pools);

				$pools = array();
				foreach($result_pools as $pool){
					$pools[$pool["address"]]["name"] = urldecode($pool["name"]);
					$pools[$pool["address"]]["statspage"] = urldecode($pool["statspage"]);
				};
				
				$sql_rigs 		= "SELECT * FROM `cgmonitor__rigs` order by name;";
				$result_rigs	= result($sql_rigs);

				$status_total_n =	$uptime_total_n =	$mhs1_total_n = $mhsa_total_n =	$WUs_total_n = $hashwu_total_n = $Accepted_total_n = $Rejected_total_n = $rejp_total_n = $url_total_n = "";
				$status_total_l =	$uptime_total_l =	$mhs1_total_l = $mhsa_total_l =	$WUs_total_l = $hashwu_total_l = $Accepted_total_l = $Rejected_total_l = $rejp_total_l = $url_total_l = "";
		
				$total_active_rigs_n = 0;
				$total_active_rigs_l = 0;

				$total_rigs = 0;
				
				foreach($result_rigs as $rig){
						$last_rig = $status_rig_n =	$uptime_rig_n =	$mhs1_rig_n = $mhsa_rig_n =	$WUs_rig_n = $hashwu_rig_n = $Accepted_rig_n = $Rejected_rig_n = $rejp_rig_n = $url_rig_n = "";
						$status_rig_l =	$uptime_rig_l =	$mhs1_rig_l = $mhsa_rig_l =	$WUs_rig_l = $hashwu_rig_l = $Accepted_rig_l = $Rejected_rig_l = $rejp_rig_l = $url_rig_l = "";

							
						$sql_devices = "SELECT * FROM `cgmonitor__devs` WHERE `idrigs` = '".$rig['id']."' order by number;";
						$devices = result($sql_devices);

						$total_active_devc_n = 0;
						$total_active_devc_l = 0;

						foreach($devices as $device){		
								$wu_last = 0;
								$hash_last = 0;

								$sql_dev_time = "SELECT * FROM `miner_".$device['idrigs']."_DEVS_".$device['number']."` where time < '".$timelast."' and Time > '".$timeoffline."'  order by Time desc Limit 0,1;";
								$dev_timea = result($sql_dev_time);

								
								foreach($dev_timea as $dev_time){		

										// all settings to calc or get rig data
										$gpuacti	= $dev_time["GPU Activity"];
										$temp		= $dev_time["Temperature"];
										$fanspeed	= $dev_time["Fan Speed"];
										$fanpercent	= $dev_time["Fan Percent"];
										$mhs1 		= round($dev_time["MHS 1s"] * 1000);
										$mhsa 		= round($dev_time["MHS av"] * 1000);
										$WUs 		= round($dev_time["WUs"] * 1000);
										$hashwu 	= ($WUs / $mhsa) * 100;
										$hashwu 	= number_format($hashwu , 0, '.', '') ;
										$GPUCl 		= $dev_time["GPU Clock"];
										$GPUMe 		= $dev_time["Memory Clock"];
										$GPUVo 		= $dev_time["GPU Voltage"];
										$Accepted 	= round($dev_time["Accepted"]);
										$Rejected 	= round($dev_time["Rejected"]);
										$rejp   	= $dev_time["Device Rejected%"];
										$rejp	 	= number_format($rejp , 2, '.', '') ;
										$int	   	= $dev_time["Intensity"];

										// all settings to calc or get rig data
										if($status_rig_l == "") 	$status_rig_l = $dev_time["Status"];
										if($status_rig_l != "Dead") $status_rig_l = $dev_time["Status"];

										if($res_alerts[0]["tempactiveemail"] == "yes"){
											if($temp < $res_alerts[0]["tempmin"]){
												$status_rig_l = "TempLow";
											};
											if($temp > $res_alerts[0]["tempmax"]){
												$status_rig_l = "TempHigh";
											};
										};
										if($res_alerts[0]["hashactiveemail"] == "yes"){
											if(($mhs1 *1000) < $res_alerts[0]["hashmin"]){
												$status_rig_l = "HashLow";
											};
										};
										
										$c1 = $status_rig_l;
										$c2 = $gpuacti."%";
										$c3 = $temp." ".$fanspeed." (".$fanpercent."%)";
										$c4 = $mhs1." KHs (".$mhsa." KHs)";
										$c5 = $WUs . " (" . $hashwu. " %)";
										$c6 = $Accepted." / ".$Rejected." (".$rejp."%)";
										$c7 = $GPUCl." / ".$GPUMe." MHz @ ".$GPUVo." V";
										$c8 = $int;
										
										$last_value[$rig["id"]."-".$device['number']."-c1"] =  $c1;
										$last_value[$rig["id"]."-".$device['number']."-c2"] =  $c2;
										$last_value[$rig["id"]."-".$device['number']."-c3"] =  $c3;
										$last_value[$rig["id"]."-".$device['number']."-c4"] =  $c4;
										$last_value[$rig["id"]."-".$device['number']."-c5"] =  $c5;
										$last_value[$rig["id"]."-".$device['number']."-c6"] =  $c6;
										$last_value[$rig["id"]."-".$device['number']."-c7"] =  $c7;
										$last_value[$rig["id"]."-".$device['number']."-c8"] =  $c8;
										
										$last_rig = $rig["id"];
									

										
										
										$url_rig_l					= $dev_time["Last Share Pool"];
										if(isset($pools[urlencode($url_rig_l)])){
											$url_rig_l_t	= $url_rig_l;
											$url_rig_l 		= $pools[urlencode($url_rig_l_t)]["name"];

											if($pools[urlencode($url_rig_l_t)]["statspage"] != ""){
												$url_rig_l = "<a target='_blank' href='".$pools[urlencode($url_rig_l_t)]["statspage"]."'>".$url_rig_l."</a>";
											};	
										};
										
										$time_rig_l					= $dev_time["Time"];

										$hours 						= floor($dev_time["Elapsed"] / 3600);
										$mins 						= floor(($dev_time["Elapsed"] - ($hours*3600)) / 60);
										$secs 						= floor($dev_time["Elapsed"] % 60);
										$uptime_rig_l				= $hours . ":". $mins.":".$secs;
										
										$uptime_rig_l				= secondsToTime($dev_time["Elapsed"]);		
										
										$mhsa_rig_l				 	= $mhsa_rig_l 		+ $mhsa * 1;
										$mhs1_rig_l				 	= $mhs1_rig_l 		+ $mhs1 * 1;
										$WUs_rig_l 					= $WUs_rig_l	 	+ $WUs * 1;
										$hashwu_rig_l 				= $hashwu_rig_l 	+ $hashwu * 1;
										$Accepted_rig_l 			= $Accepted_rig_l 	+ $Accepted;
										$Rejected_rig_l				= $Rejected_rig_l 	+ $Rejected;
										$rejp_rig_l					= $rejp_rig_l 		+ $rejp;
										
								};

								
								$sql_dev_time = "SELECT * FROM `miner_".$device['idrigs']."_DEVS_".$device['number']."` where time < '".$timenow."' and Time > '".$timeoffline."'  order by Time desc Limit 0,1;";
								$dev_timea = result($sql_dev_time);
								
								foreach($dev_timea as $dev_time){		
										// all settings to calc or get rig data
										$gpuacti	= $dev_time["GPU Activity"];
										$temp		= $dev_time["Temperature"];
										$fanspeed	= $dev_time["Fan Speed"];
										$fanpercent	= $dev_time["Fan Percent"];
										$mhs1 		= round($dev_time["MHS 1s"] * 1000);
										$mhsa 		= round($dev_time["MHS av"] * 1000);
										$WUs 		= round($dev_time["WUs"] * 1000);
										$hashwu 	= ($WUs / $mhsa) * 100;
										$hashwu 	= number_format($hashwu , 0, '.', '') ;
										$GPUCl 		= $dev_time["GPU Clock"];
										$GPUMe 		= $dev_time["Memory Clock"];
										$GPUVo 		= $dev_time["GPU Voltage"];
										$Accepted 	= round($dev_time["Accepted"]);
										$Rejected 	= round($dev_time["Rejected"]);
										$rejp   	= $dev_time["Device Rejected%"];
										$rejp	 	= number_format($rejp , 2, '.', '') ;
										$int	   	= $dev_time["Intensity"];

										// all settings to calc or get rig data
										if($status_rig_n == "") 	$status_rig_n = $dev_time["Status"];
										if($status_rig_n != "Dead") $status_rig_n = $dev_time["Status"];

										if($res_alerts[0]["tempactiveemail"] == "yes"){
											if($temp < $res_alerts[0]["tempmin"]){
												$status_rig_n = "TempLow";
											};
											if($temp > $res_alerts[0]["tempmax"]){
												$status_rig_n = "TempHigh";
											};
										};
										if($res_alerts[0]["hashactiveemail"] == "yes"){
											if(($mhs1 *1000) < $res_alerts[0]["hashmin"]){
												$status_rig_n = "HashLow";
											};
										};

										$c1 = $status_rig_n;
										$c2 = $gpuacti."%";
										$c3 = $temp." ".$fanspeed." (".$fanpercent."%)";
										$c4 = $mhs1." KHs (".$mhsa." KHs)";
										$c5 = $WUs . " (" . $hashwu. " %)";
										$c6 = $Accepted." / ".$Rejected." (".$rejp."%)";
										$c7 = $GPUCl." / ".$GPUMe." MHz @ ".$GPUVo." V";
										$c8 = $int;

										if(!($last_value[$rig["id"]."-".$device['number']."-c1"] ==  $c1) or $force) $json_array[] =  array("id" => $rig["id"]."-".$device['number']."-c1" ,"val"  => $c1);
										if(!($last_value[$rig["id"]."-".$device['number']."-c2"] ==  $c2) or $force) $json_array[] =  array("id" => $rig["id"]."-".$device['number']."-c2" ,"val"  => $c2);
										if(!($last_value[$rig["id"]."-".$device['number']."-c3"] ==  $c3) or $force) $json_array[] =  array("id" => $rig["id"]."-".$device['number']."-c3" ,"val"  => $c3);
										if(!($last_value[$rig["id"]."-".$device['number']."-c4"] ==  $c4) or $force) $json_array[] =  array("id" => $rig["id"]."-".$device['number']."-c4" ,"val"  => $c4);
										if(!($last_value[$rig["id"]."-".$device['number']."-c5"] ==  $c5) or $force) $json_array[] =  array("id" => $rig["id"]."-".$device['number']."-c5" ,"val"  => $c5);
										if(!($last_value[$rig["id"]."-".$device['number']."-c6"] ==  $c6) or $force) $json_array[] =  array("id" => $rig["id"]."-".$device['number']."-c6" ,"val"  => $c6);
										if(!($last_value[$rig["id"]."-".$device['number']."-c7"] ==  $c7) or $force) $json_array[] =  array("id" => $rig["id"]."-".$device['number']."-c7" ,"val"  => $c7);
										if(!($last_value[$rig["id"]."-".$device['number']."-c8"] ==  $c8) or $force) $json_array[] =  array("id" => $rig["id"]."-".$device['number']."-c8" ,"val"  => $c8);
																		


										
										$url_rig_n					= $dev_time["Last Share Pool"];
										if(isset($pools[urlencode($url_rig_n)])){
											$url_rig_n_t	= $url_rig_n;
											$url_rig_n 		= $pools[urlencode($url_rig_n_t)]["name"];

											if($pools[urlencode($url_rig_n_t)]["statspage"] != ""){
												$url_rig_n = "<a target='_blank' href='".$pools[urlencode($url_rig_n_t)]["statspage"]."'>".$url_rig_n."</a>";
											};	
										}
										$time_rig_n					= $dev_time["Time"];

										
										$hours 						= floor($dev_time["Elapsed"] / 3600);
										$mins 						= floor(($dev_time["Elapsed"] - ($hours*3600)) / 60);
										$secs 						= floor($dev_time["Elapsed"] % 60);
										$uptime_rig_n				= $hours . ":". $mins.":".$secs;
										$uptime_rig_n				= secondsToTime($dev_time["Elapsed"]);		

										$mhsa_rig_n				 	= $mhsa_rig_n 		+ $mhsa * 1;
										$mhs1_rig_n				 	= $mhs1_rig_n 		+ $mhs1 * 1;
										$WUs_rig_n 					= $WUs_rig_n		+ $WUs * 1;
										$hashwu_rig_n 				= $hashwu_rig_n 	+ $hashwu * 1;
										$Accepted_rig_n 			= $Accepted_rig_n 	+ $Accepted;
										$Rejected_rig_n				= $Rejected_rig_n 	+ $Rejected;
										$rejp_rig_n					= $rejp_rig_n 		+ $rejp;
										
										
								};

								$last_rig = $rig["id"];
								if( (!(isset($last_value[$rig["id"]."-".$device['number']."-c1"])))){
									if($force){
										$json_array[] =  array("id" => $rig["id"]."-".$device['number']."-c1" ,"val"  => "NoContact");
									};
									$status_rig_l = "NoContact";
									$status_rig_n = "NoContact";
								}else{
									$total_active_devc_l++;
									$total_active_devc_n++;
								};

							};

							if($total_active_devc_n > 0) $total_active_rigs_n++;
							if($total_active_devc_l > 0) $total_active_rigs_l++;

							$total_rigs++;

							$hashwu_rig_n 	= $hashwu_rig_n /$total_active_devc_n;
							$hashwu_rig_l 	= $hashwu_rig_l /$total_active_devc_l;

							$rejp_rig_n 	= $rejp_rig_n /$total_active_devc_n;
							$rejp_rig_l 	= $rejp_rig_l /$total_active_devc_l;
							
							$c2_n = $status_rig_n;
							$c3_n = $uptime_rig_n;
							$c4_n = $mhs1_rig_n." KHs (".$mhsa_rig_n." KHs)";
							$c5_n = $WUs_rig_n . " (" . number_format($hashwu_rig_n, 0, '.', ''). " %)";
							$c6_n = $Accepted_rig_n." / ".$Rejected_rig_n." (".number_format($rejp_rig_n, 2, '.', '')."%)";		
							$c7_n = $url_rig_n;
							
							$c2_l = $status_rig_l;
							$c3_l = $uptime_rig_l;
							$c4_l = $mhs1_rig_l." KHs (".$mhsa_rig_l." KHs)";
							$c5_l = $WUs_rig_l . " (" . number_format($hashwu_rig_l, 0, '.', ''). " %)";
							$c6_l = $Accepted_rig_l." / ".$Rejected_rig_l." (".number_format($rejp_rig_l, 2, '.', '')."%)";		
							$c7_l = $url_rig_l;
								
							if(!($c2_n==$c2_l) or $force) 	$json_array[] =  array("id" => $last_rig."-c1" ,"val"  => $c2_n);
							if(!($c2_n==$c2_l) or $force) 	$json_array[] =  array("id" => $last_rig."-c2" ,"val"  => $c2_n);
							if(!($c3_n==$c3_l) or $force)  	$json_array[] =  array("id" => $last_rig."-c3" ,"val"  => $c3_n);
							if(!($c4_n==$c4_l) or $force)  	$json_array[] =  array("id" => $last_rig."-c4" ,"val"  => $c4_n);
							if(!($c5_n==$c5_l) or $force)  	$json_array[] =  array("id" => $last_rig."-c5" ,"val"  => $c5_n);
							if(!($c6_n==$c6_l) or $force)  	$json_array[] =  array("id" => $last_rig."-c6" ,"val"  => $c6_n);
							if(!($c7_n==$c7_l) or $force)  	$json_array[] =  array("id" => $last_rig."-c7" ,"val"  => $c7_n);
	
							$status_total_n 	= $status_total_n;
							$mhs1_total_n 		= $mhs1_total_n 	+ $mhs1_rig_n;
							$mhsa_total_n 		= $mhsa_total_n 	+ $mhsa_rig_n;
							$WUs_total_n 		= $WUs_total_n 		+ $WUs_rig_n;
							$hashwu_total_n 	= $hashwu_total_n 	+ $hashwu_rig_n;
							$Accepted_total_n 	= $Accepted_total_n + $Accepted_rig_n;
							$Rejected_total_n 	= $Rejected_total_n + $Rejected_rig_n;
							$rejp_total_n 		= $rejp_total_n 	+ $rejp_rig_n;

							$status_total_l 	= $status_total_l;
							$mhs1_total_l 		= $mhs1_total_l 	+ $mhs1_rig_l;
							$mhsa_total_l 		= $mhsa_total_l 	+ $mhsa_rig_l;
							$WUs_total_l 		= $WUs_total_l 		+ $WUs_rig_l;
							$hashwu_total_l 	= $hashwu_total_l 	+ $hashwu_rig_l;
							$Accepted_total_l 	= $Accepted_total_l + $Accepted_rig_l;
							$Rejected_total_l 	= $Rejected_total_l + $Rejected_rig_l;
							$rejp_total_l 		= $rejp_total_l 	+ $rejp_rig_l;
							
							
						};

						$hashwu_total_n 	= number_format($hashwu_total_n /$total_active_rigs_n, 2, '.', '');
						$hashwu_total_l 	= number_format($hashwu_total_l /$total_active_rigs_l, 2, '.', '');

						$rejp_total_n 		= number_format($rejp_total_n /$total_active_rigs_n, 2, '.', '');
						$rejp_total_l 		= number_format($rejp_total_l /$total_active_rigs_l, 2, '.', '');
						
						
						$total_dective_rigs_n = $total_rigs - $total_active_rigs_n;
						$total_dective_rigs_l = $total_rigs - $total_active_rigs_l;
					
						
						$t_c2_n = "ONLINE: ".$total_active_rigs_n . " - OFFLINE: ".$total_dective_rigs_n;
						$t_c4_n = $mhs1_total_n." KHs (".$mhsa_total_n." KHs)";
						$t_c5_n = $WUs_total_n . " (" . $hashwu_total_n. " %)";
						$t_c6_n = $Accepted_total_n." / ".$Rejected_total_n." (".$rejp_total_n."%)";		
							
						$t_c2_l = "ONLINE: ".$total_active_rigs_l . " - OFFLINE: ".$total_dective_rigs_l;
						$t_c4_l = $mhs1_total_l." KHs (".$mhsa_total_l." KHs)";
						$t_c5_l = $WUs_total_l . " (" . $hashwu_total_l. " %)";
						$t_c6_l = $Accepted_total_l." / ".$Rejected_total_l." (".$rejp_total_l."%)";		

						if(!($t_c2_n==$t_c2_l) or $force) 	$json_array[] =  array("id" => "t-c2" ,"val"  => $t_c2_n);
						if(!($t_c4_n==$t_c4_l) or $force)  	$json_array[] =  array("id" => "t-c4" ,"val"  => $t_c4_n);
						if(!($t_c5_n==$t_c5_l) or $force)  	$json_array[] =  array("id" => "t-c5" ,"val"  => $t_c5_n);
						if(!($t_c6_n==$t_c6_l) or $force)  	$json_array[] =  array("id" => "t-c6" ,"val"  => $t_c6_n);

						$t_l1_n = "ONLINE: ".$total_active_rigs_n . " - OFFLINE: ".$total_dective_rigs_n;
						$t_l2_n = $mhs1_total_n." KHs (".$mhsa_total_n." KHs)";
						$t_l3_n = $WUs_total_n . " (" . $hashwu_total_n. " %) WU";
						$t_l4_n = "A: ". $Accepted_total_n." / ". " R: ". $Rejected_total_n." (".$rejp_total_n."%)";		
							
						$t_l1_l = "ONLINE: ".$total_active_rigs_n . " - OFFLINE: ".$total_dective_rigs_n;
						$t_l2_l = $mhs1_total_l." KHs (".$mhsa_total_l." KHs)";
						$t_l3_l = $WUs_total_l . " (" . $hashwu_total_l. " %) WU";
						$t_l4_l = "A: ". $Accepted_total_l." / ". " R: ". $Rejected_total_l." (".$rejp_total_l."%)";		

						if(!($t_l1_n==$t_l1_l) or $force) 	$json_array[] =  array("id" => "t-l1" ,"val"  => $t_l1_n);
						if(!($t_l2_n==$t_l2_l) or $force)  	$json_array[] =  array("id" => "t-l2" ,"val"  => $t_l2_n);
						if(!($t_l3_n==$t_l3_l) or $force)  	$json_array[] =  array("id" => "t-l3" ,"val"  => $t_l3_n);
						if(!($t_l4_n==$t_l4_l) or $force)  	$json_array[] =  array("id" => "t-l4" ,"val"  => $t_l4_n);


	$json = json_encode($json_array);

	echo get('callback').'({"stats":' .$json . ',"time":"'.$timenow.'","token":"'.$token.'"})';
?>