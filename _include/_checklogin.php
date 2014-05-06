<?php
	// get the token and look if it's the same as your server value

	$token_post				= htmlentities($_POST['token']);
	$token = getToken();
			
	if(!($token == $token_post)){
		header("Location:".$_SERVER['HTTP_REFERER']);
		exit;
	};

	// generate a new Token
	
	$token_new 	= makeToken();

	// check if there is a new user and password request ( request is made by changing the value1 of row user in the settings table by your own secret_passphrase 

	$sql 	= "SELECT value1 FROM `cgmonitor__settings` where name = 'user'";
	$user 	= result($sql);

	
	if($user[0]["value1"] == $GLOBALS['secret_passphrase']){
			
			// if the user wants a new username and password get it, secure the password with an encryption and put in the table

			$passwordEncrypt 		= htmlentities($_POST['passwordEncrypt']);
			$usernameEncrypt 		= htmlentities($_POST['usernameEncrypt']);
			$salt 					= $usernameEncrypt.$GLOBALS['secret_passphrase'];
			$cryptastic 			= new cryptastic;
			$passwordEncrypt 		= base64_encode($cryptastic->pbkdf2($passwordEncrypt, $salt, 1000, 32));

			$sql = "UPDATE `cgmonitor__settings` SET value1 = '".mysql_real_escape_string($usernameEncrypt)."', value2 = '".mysql_real_escape_string($passwordEncrypt)."' WHERE `name`  =  'user';";
			result($sql);

	}else{
	
			// if the user logins check the sha1 username, and encrypt the sha1 password to verrify with the database

			$sql = "SELECT * FROM `cgmonitor__settings` WHERE `name`  =  'user';";
			$result = result($sql);

			$usernameEncrypt 	=  hex2bin($_POST["usernameEncrypt"]);
			$usernameEncrypt 	= string_decrypt($usernameEncrypt, $token_post);
			
			if($usernameEncrypt ==  $result[0]["value1"]){
			
				$passwordEncrypt 		= htmlentities($_POST['passwordEncrypt']);
				$passwordEncrypt 		= hex2bin($passwordEncrypt);
				$passwordEncrypt 		= string_decrypt($passwordEncrypt, $token_post);
				$usernameEncrypt 		= $result[0]["value1"];
				
				$salt 					= $usernameEncrypt.$GLOBALS['secret_passphrase'];
				$cryptastic 			= new cryptastic;
				$passwordEncrypt 		= base64_encode($cryptastic->pbkdf2($passwordEncrypt, $salt, 1000, 32));

				if($result[0]["value2"] == $passwordEncrypt){

					$_SESSION['MM_Username'.$secret_passphrase] = "okuser";
					$_SESSION['username'.$secret_passphrase] = $usernameEncrypt;
					$action = "home";
					
	
					// make a autoupdate if requested, will be removed when opensourced ///

					$sql2 		= "SELECT * FROM `cgmonitor__settings` where name = 'autoupdate';";
					$autoupdate = result($sql2);
					$autoupdate = $autoupdate[0]["value1"];

					if($autoupdate == "true"){
						checkupdate();		
					};
					
					// remove until here ///
					
				};
			};
	};
	
