<?php
if(empty($_SESSION['MM_Username'.$secret_passphrase]) and $_SESSION['MM_Username'.$secret_passphrase] != "okuser"){
exit;
};

	if(isset($_POST['update'])){
		save_setting("user","NOT","NOT",returnpostvaluenew("email"));
		save_setting("timezone",returnpostvaluenew("timezone"));
	};

	$email 			= get_setting("user");
	$email 			= $email[3];

	$timezone 		= get_setting("timezone");
	$timezone 		= $timezone[1];
	
?>
	<h1 class="specialfont">Change User Settings</h1>
	<div style="clear:both"></div>

	<form method="post" name="formusersettings" id="formusersettings">
	<input type="hidden" id="update" name="update" value="update">

	<br/><br/><b>Email for alerts:</b><br/>
	<input style='width:250px'  type="text" placeholder="E-mail" class='form-control' name="email" id="email" value="<?php echo $email;?>"><br/>
	<b>Time zone difference UTC:</b><br/>
	<input style='width:250px'  type="text" placeholder="0" class='form-control' name="timezone" id="timezone" value="<?php echo $timezone;?>"><br/>

	<button type="submit" id="submit" name="submit" class="tooltipactive btn btn-default " title="Save settings"><span class="glyphicon glyphicon-floppy-disk"></span></button>&nbsp;&nbsp;
	<a href='?action=password'><button type="button" class="tooltipactive btn btn-default " title="Change your password"><span class="glyphicon glyphicon-lock"></span></button></a>&nbsp;&nbsp;
	</form>
