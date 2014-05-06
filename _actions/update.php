<?php

if(empty($_SESSION['MM_Username'.$secret_passphrase]) and $_SESSION['MM_Username'.$secret_passphrase] != "okuser"){
exit;
};

	if(isset($_POST['updatenow'])){

		if(checkupdate(true)){
			echo "Update OK";
		}else{
			echo "Error while updating, contact admin";
		};
	}else{
		$url  	= "http://www.cg-monitor.com/update/version.php";
		$ch 	= curl_init();
		
		$agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';
		curl_setopt($curl, CURLOPT_USERAGENT, $agent);			
		curl_setopt($ch,CURLOPT_COOKIEJAR,realpath('.').'/log/cookiecheckupdate.txt');
		curl_setopt($ch,CURLOPT_COOKIEFILE,realpath('.').'/log/cookiecheckupdate.txt');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_REFERER, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$server = curl_exec($ch);
		curl_close($ch);
		
		$sql_sel 	= "SELECT * FROM `cgmonitor__settings` where name = 'lastupdate';";
		$local 		= result($sql_sel);
		$local 		= $local[0]["value1"];

		$sql_sel 	= "SELECT * FROM `cgmonitor__settings` where name = 'autoupdate';";
		$autoupdate = result($sql_sel);
		$autoupdate = $autoupdate[0]["value1"];
	
		$server_date 	= smalldaterevert($server);
		$local_date 	= smalldaterevert($local);
		
?>
		<h1>Update source</h1>
		<div style="clear:both"></div>

		<table>
		<tr><td>Server version:<td><?php echo $server_date["Y"];?>-<?php echo $server_date["m"];?>-<?php echo $server_date["d"];?> <?php echo $server_date["H"];?>:<?php echo $server_date["i"];?> 
		<tr><td>Your version:<td><?php echo $local_date["Y"];?>-<?php echo $local_date["m"];?>-<?php echo $local_date["d"];?> <?php echo $local_date["H"];?>:<?php echo $local_date["i"];?> 
		</table>
		<br/>
		<form method="post">
		<input type="hidden" id="updatenow" name="updatenow" value="updatenow" />
		<input type="submit" id="submit" name="submit" value="Update now" class='btn btn-default'/>
		</form>
		
		<br/>
		<h3>Automatic update source on login</h3>
		<div style="clear:both"></div>
		<input type="checkbox" id="autoupdate" <?php if($autoupdate == "true") echo "CHECKED";?>>
		
		 <script>
				$(function() {
					$("#autoupdate").bootstrapSwitch();
				});
			   
			   	$('#autoupdate').on('switchChange.bootstrapSwitch', function (event, state) {
					url = "http://<?php echo $_SERVER['SERVER_NAME'] .$base_url;?>json/?action=savesetting&name=autoupdate&value1=" + state;
					$( "#result" ).load( url );
				}); 
		</script>
<?php
	};

?>
