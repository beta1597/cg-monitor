<?php
if(empty($_SESSION['MM_Username'.$secret_passphrase]) and $_SESSION['MM_Username'.$secret_passphrase] != "okuser"){
exit;
};

if(isset($_POST['update'])){
	include_once("_cryptastic.php");

	$sql 	= "SELECT * FROM `cgmonitor__settings` WHERE `name`  =  'user';";
	$user 	= result($sql);
	
	$passwordEncrypt 		= htmlentities($_POST['passwordEncrypt']);
	$usernameEncrypt 		= $user[0]["value1"];
	$salt 					= $usernameEncrypt.$GLOBALS['secret_passphrase'];
	$cryptastic 			= new cryptastic;
	$passwordEncrypt 		= base64_encode($cryptastic->pbkdf2($passwordEncrypt, $salt, 1000, 32));

	$sql = "UPDATE `cgmonitor__settings` SET `value2` = '".$passwordEncrypt."' WHERE `name`  =  'user';";
	result($sql);
	echo "Password saved<br/><br/>";
	exit;
};

?>
	<script src="./js/sha3.js"></script>
	
	<h1 class="specialfont">Change Password</h1>
	<div style="clear:both"></div>
	<br/><br/>
	
	Your password needs to be more than 6 characters<br/><br/>
	<form method="post" name="formusersettings" id="formusersettings">
	<input type="hidden" id="update" name="update" value="update">
	<input type="hidden" name="passwordEncrypt" id="passwordEncrypt" value="">

    <input style='width:150px'  type="password" placeholder="New Password" class='form-control' name="password" id="password"><br/>
    <input style='width:150px'  type="password" placeholder="Repeat Password" class='form-control' name="password2" id="password2"><br/>
	<button type="submit" id="submit" name="submit" class="tooltipactive btn btn-default " title="Save password" disabled ><span class="glyphicon glyphicon-floppy-disk"></span></button>&nbsp;&nbsp;
	<script>
		$( "#password" ).keyup(function() { checkpassword(); });
		$( "#password2" ).keyup(function() { checkpassword(); });

		function checkpassword(){
			if($('#password').val() == $('#password2').val() && $('#password').val().length >= 6 ){
				$('#submit').removeAttr("disabled");
			}else{
				$('#submit').attr("disabled", "disabled");
			};
		};
		
		$(document).ready(function() {
				$('#formusersettings').submit(function() {
					if($('#password').val() != "" && $('#password').val().length >= 6){
							passwordEncrypt = CryptoJS.SHA3($('#password').val());
							passwordEncrypt = passwordEncrypt.toString(CryptoJS.enc.Base64);
							
							$('#passwordEncrypt').val(passwordEncrypt);
							$('#password').val("");
							$('#password2').val("");
							return true;
					};
			});
		});

	
	</script>