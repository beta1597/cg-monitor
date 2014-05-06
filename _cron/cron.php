<?php
	include_once("../_configfile.php");
	include_once("../_include/_functions.php");
	include_once('../_include/_cryptastic.php');

	$sql_cron 		= "SELECT value1 FROM `cgmonitor__settings` where name = 'lastcron';";
	$res_cron 		= result($sql_cron);
	
	$timelastcron 	= date("YmdHis",mktime (date("H"), date("i") -10 ,date("s"), date("m") , date("d") ,date("Y") ));
	$timetimeout 	= date("YmdHis",mktime (date("H"), date("i") -30 ,date("s"), date("m") , date("d") ,date("Y") ));
	$timeremoveact 	= date("YmdHis",mktime (date("H"), date("i") ,date("s"), date("m") , date("d") - 7 ,date("Y") ));
	$timenow 		= date("YmdHis");
	
	if($res_cron[0]["value1"] < $timelastcron){
		$sql_del		= "DELETE FROM `cgmonitor__actions` WHERE `time` < '".$timeremoveact."';";
		result($sql_del);

		$sql_cron 		= "UPDATE  `cgmonitor__settings` set value1 = '".$timenow."' where name = 'lastcron';";
		result($sql_cron);
		
		$sql_actions	= "UPDATE `cgmonitor__actions` SET status = 'timeout', timeclosed = '".$timenow."'  WHERE status = 'open' and time < '".$timetimeout."' order by time;";
		result($sql_actions);

		$sql_alerts = "SELECT * FROM `cgmonitor__alerts` where idrigs = '0';";
		$res_alerts = result($sql_alerts);
		$sql_rigs = "SELECT * FROM `cgmonitor__rigs` order by name;";
		$rigs = result($sql_rigs);
		
		$email_total_sub 	= "Alert from CG-MONITOR";
		$email_total_tex 	= "";
		$error_total 				= false;
		
		foreach($rigs as $rig){
			$sql_devs 	= "SELECT * FROM `cgmonitor__devs` WHERE `idrigs` = '".$rig['id']."';";
			$devices	= result($sql_devs);
			foreach($devices as $device){
				$error = false;
				
				$title_device 	= "<br/><br/><b>RIG: ".urldecode($rig['name'])." : DEVICE " . $device['number'] . "</b><br/><br/>";
				$email			= "";
				
				$sql_devs_stats	= "SELECT * FROM `miner_".$rig['id']."_DEVS_".$device['number']."` ORDER BY `Time` DESC Limit 0,1;";
				$devices_stats	= result($sql_devs_stats);
				//print_r($devices_stats);
				if($devices_stats[0]["Time"] < $timelastcron){
					if($res_alerts[0]["deadactiveemail"] == "yes"){
						$email .= "Not up to date (no internet connection, etc ...)<br/>";
						$error = true;
					};				
				}else{
					if($res_alerts[0]["tempactiveemail"] == "yes"){
						if($devices_stats[0]["Temperature"] < $res_alerts[0]["tempmin"]){
							$email .= "Temperature is to low<br/>";
							$error = true;
						};
						if($devices_stats[0]["Temperature"] > $res_alerts[0]["tempmax"]){
							$email .= "Temperature is to high<br/>";
							$error = true;
						};
					};
					if($res_alerts[0]["hashactiveemail"] == "yes"){
						if(($devices_stats[0]["MHS 1s"] *1000) < $res_alerts[0]["hashmin"]){
							$email .= "Hash is to low<br/>";
							$error = true;
						};
					};
					if($res_alerts[0]["deadactiveemail"] == "yes"){
						if($devices_stats[0]["Status"] != "Alive"){
							$email .= "Device is not Alive<br/>";
							$error = true;
							
							if($res_alerts[0]["deadactivereboot"] == "yes"){
								$email 		.= "We try to reboot for you<br/>";
								save_action(array("automanual"=>"Automatic","command"=>"reboot","rig"=>$rig['id']));
							}else{
								$email .= "<b>YOU NEED TO REBOOT</b><br/>";
							};
						};
					};

				};
				if($device['lastemailsend'] == '' and $error){
					$error_total 		= true;
					$email_total_tex 	.= $title_device . $email;
					$sql_dev_ok 		= "UPDATE  `cgmonitor__devs` set lastemailsend = '".$timenow."' where id = '".$device['id']."' and idrigs = '".$rig['id']."';";
					$res_dev_ok			= result($sql_dev_ok);
				};
				if($error == false){
					$sql_dev_ok 	= "UPDATE  `cgmonitor__devs` set lastemailsend = '' where id = '".$device['id']."' and idrigs = '".$rig['id']."';";
					$res_dev_ok		= result($sql_dev_ok);
				};
			};
		};
		
		if($error_total == true){
			$sql_user 		= "SELECT value3 FROM `cgmonitor__settings` where name = 'user';";
			$res_user 		= result($sql_user);
			$email 			= urldecode($res_user[0]["value3"]);
			
			if($email <> ""){
				include_once "../_include/class.phpmailer.php";
				$mail = new PHPMailer(true); //defaults to using php "mail()"; the true param means it will throw exceptions on errors, which we need to catch
				try {
				
				  $mail->SetFrom($email);
				  $mail->AddReplyTo($email);
				  $mail->AddAddress($email);
				  $mail->Subject = $email_total_sub;
				  $mail->MsgHTML($email_total_tex);
				  $mail->Send();
				} catch (phpmailerException $e) {
					echo $e->errorMessage(); //Pretty error messages from PHPMailer
				} catch (Exception $e) {
					echo $e->getMessage(); //Boring error messages from anything else!
				}
			};
		};
	};
	
	echo "CRON";