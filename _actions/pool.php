<?php
if(empty($_SESSION['MM_Username'.$secret_passphrase]) and $_SESSION['MM_Username'.$secret_passphrase] != "okuser"){
exit;
};

	$table 	= "cgmonitor__pools";
	$pool 	= mysql_real_escape_string(get("pool"));

	if(isset($_GET['remove'])){
		$sql_del = "DELETE FROM `".$table."` WHERE id='".mysql_real_escape_string(get('remove'))."';";
		result($sql_del);
		header("Location: .?action=pools");

	};

	if(isset($_POST['update'])){
		$sql_sel = "SELECT * FROM `".$table."` WHERE id = '".$pool."' order by id;";
		$res_sel	 = result($sql_sel);

		foreach($res_sel as $result_det){
			if(isset($_POST["name".$result_det["id"]])){
				$fields 	  = returnpostvalue("name",$result_det["id"]) . ",";
				$fields 	 .= returnpostvalue("address",$result_det["id"]) . ",";
				$fields 	 .= returnpostvalue("user",$result_det["id"]) . ",";
				$fields 	 .= returnpostvalue("password",$result_det["id"]) . ",";
				$fields 	 .= returnpostvalue("format",$result_det["id"]) . ",";
				$fields 	 .= returnpostvalue("statspage",$result_det["id"]) . ",";
				$fields 	 .= returnpostvalue("idalgorithms",$result_det["id"]) . "";

				$sql_upd = "UPDATE `".$table."` set ".$fields." where id = '".$result_det["id"]."';";
				result($sql_upd);
			};
		};
	};
?>
<h1>Pools</h1>
<div style="clear:both"></div>

<form method="post">
<input type="hidden" id="update" name="update" value="update">
<input type="hidden" id="update" name="update" value="<?php echo $pool;?>">
<?php
	$sql_sel = "SELECT * FROM `".$table."` WHERE id = '".$pool."' order by name;";
	$res_sel = result($sql_sel);
	foreach($res_sel as $result_det){

			echo "<b>Name</b><br/><input style='width:150px' maxlength='400' type='text' class='form-control' autocomplete='off' name='name". $result_det["id"] . "' id='name". $result_det["id"] . "' value='".urldecode($result_det["name"])."'>";
			echo "<br/><b>Address</b><br/><input style='width:610px' maxlength='400' type='text' class='form-control' autocomplete='off' name='address". $result_det["id"] . "' id='address". $result_det["id"] . "' value='".urldecode($result_det["address"])."'>";
			echo "<br/><b>Stats page</b><br/><input style='width:610px' maxlength='400' type='text' class='form-control' autocomplete='off' name='statspage". $result_det["id"] . "' id='statspage". $result_det["id"] . "' value='".urldecode($result_det["statspage"])."'>";
			echo "<br/><b>User</b><br/><input style='width:350px' maxlength='100' type='text' class='form-control' autocomplete='off' name='user". $result_det["id"] . "' id='user". $result_det["id"] . "' value='".urldecode($result_det["user"])."'>";
			echo "<br/><b>Password</b><br/><input style='width:80px' maxlength='100' type='text' class='form-control' autocomplete='off' name='password". $result_det["id"] . "' id='password". $result_det["id"] . "' value='".urldecode($result_det["password"])."'>";
			echo "<br/><b>Format</b><br/>";
			echo "<select name='format". $result_det["id"] . "' id='format". $result_det["id"] . "' class='selectpicker' data-width='120px'>";

			echo "<option value='noworker' ";
			if($result_det["format"] == "noworker") echo "SELECTED";
			echo ">No workers</option>";

			echo "<option value='user.worker' ";
			if($result_det["format"] == "user.worker") echo "SELECTED";
			echo ">User.Worker</option>";

			echo "<option value='user_worker' ";
			if($result_det["format"] == "user_worker") echo "SELECTED";
			echo ">User_Worker</option>";
			
			echo "</select>";

			echo "<br/><br/><b>Algorithm<br/>";
			echo "<select name='idalgorithms". $result_det["id"] . "' id='idalgorithms". $result_det["id"] . "' class='selectpicker' data-width='100px'>";
			
			$sql_sel 	= "SELECT * FROM `cgmonitor__algorithm` where id > 1 order by number;";
			$algorithms = result($sql_sel);
			foreach($algorithms as $algorithm){
				echo "<option value='".$algorithm["id"]."' ";
				if($result_det["idalgorithms"] == $algorithm["id"]) echo "SELECTED";
				echo ">".$algorithm["name"]."</option>";
			};
			echo "</select>";

?>
	<br/><br/>
	<button type="submit" id="submit" name="submit" class="tooltipactive btn btn-default " title="Save Pools"><span class="glyphicon glyphicon-floppy-disk"></span></button>&nbsp;&nbsp;
	<a href='?action=pools'><button type="button" class="tooltipactive btn btn-default " title="Back to the Pools"><span class="glyphicon glyphicon-home"></span></button></a>&nbsp;&nbsp;
	<a href='?action=<?php echo $_GET['action'];?>&pool=<?php echo $pool;?>&remove=<?php echo $result_det["id"];?>&hidepage=yes'><button type='button' class='btn btn-default'><span class='glyphicon glyphicon-trash'></span></button></a>
<?php
	};
	