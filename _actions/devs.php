<?php
if(empty($_SESSION['MM_Username'.$secret_passphrase]) and $_SESSION['MM_Username'.$secret_passphrase] != "okuser"){
exit;
};

$table 	= "cgmonitor__devs";
$device	= get("device");
$rig	= get("rig");

if(isset($_GET['remove'])){
	$sql_del = "DELETE FROM `".$table."` WHERE idrigs = '".mysql_real_escape_string($rig)."' and number='".mysql_real_escape_string(get('remove'))."';";
	result($sql_del);

	$sql_del = "DROP TABLE miner_".mysql_real_escape_string($rig)."_DEVS_".mysql_real_escape_string(get('remove')).";";
	result($sql_del);

	header("Location: .?action=home&rig=".$rig);
	
};

if(isset($_POST['update'])){
	$sql_sel = "SELECT * FROM `".$table."` order by id;";
	$res_sel = result($sql_sel);

	foreach($res_sel as $result_det){
		if(isset($_POST["maxtemp".$result_det["id"]])){
			$fields 	 = returnpostvalue("device",$result_det["id"]) . ",";
			$fields 	.= returnpostvalue("maxtemp",$result_det["id"]) . ",";
			$fields 	.= returnpostvalue("lowtemp",$result_det["id"]) . ",";
			$fields 	.= returnpostvalue("maxfan",$result_det["id"]) . ",";
			$fields 	.= returnpostvalue("lowfan",$result_det["id"]) . ",";
			$fields 	.= returnpostvalue("minint",$result_det["id"]) . ",";
			$fields 	.= returnpostvalue("maxint",$result_det["id"]) . ",";
			$fields 	.= returnpostvalue("autointensity",$result_det["id"]) . "";

			$sql_upd = "UPDATE `".$table."` set ".$fields." where id = '".$result_det["id"]."';";
			result($sql_upd);
		};
	};
};
?>
<form method="post">
<input type="hidden" id="update" name="update" value="update">

<?php
	$sql_sel = "SELECT * FROM cgmonitor__devs where id = '".$device."'";
	$res_sel = result($sql_sel);
	foreach($res_sel as $result_det){

			echo "<b>Device type:</b><br/><select name='device". $result_det["id"] . "' id='device". $result_det["id"] . "' class='selectpicker' data-width='100px'>";

			echo "<option value='GPU' ";
			if($result_det["device"] == "GPU") echo "SELECTED";
			echo ">GPU</option>";

			echo "<option value='GRIDSEED' ";
			if($result_det["device"] == "GRIDSEED") echo "SELECTED";
			echo ">GRIDSEED</option>";


			echo "</select>";

			echo "<br/><br/><b>Auto intensity:</b><br/><select name='autointensity". $result_det["id"] . "' id='autointensity". $result_det["id"] . "'  class='selectpicker' data-width='100px'>";

			echo "<option value='no' ";
			if($result_det["autointensity"] == "no") echo "SELECTED";
			echo ">No</option>";

			echo "<option value='yes' ";
			if($result_det["autointensity"] == "yes") echo "SELECTED";
			echo ">Yes</option>";


			echo "</select>";

			
			echo "<table class='table table-striped'><th><td>Min</td><td>Max</td>";
			echo "<tr><td>Temperature<td><input style='width:30px' maxlength='10' type='text' autocomplete='off' name='lowtemp". $result_det["id"] . "' id='lowtemp". $result_det["id"] . "' value='".urldecode($result_det["lowtemp"])."'>";
			echo "<td><input style='width:30px' maxlength='10' type='text' autocomplete='off' name='maxtemp". $result_det["id"] . "' id='maxtemp". $result_det["id"] . "' value='".urldecode($result_det["maxtemp"])."'>";
			echo "<tr><td>Fan percent<td><input style='width:30px' maxlength='10' type='text' autocomplete='off' name='lowfan". $result_det["id"] . "' id='lowfan". $result_det["id"] . "' value='".urldecode($result_det["lowfan"])."'>";
			echo "<td><input style='width:30px' maxlength='10' type='text' autocomplete='off' name='maxfan". $result_det["id"] . "' id='maxfan". $result_det["id"] . "' value='".urldecode($result_det["maxfan"])."'>";
			echo "<tr><td>Intensity<td><input style='width:30px' maxlength='10' type='text' autocomplete='off' name='minint". $result_det["id"] . "' id='minint". $result_det["id"] . "' value='".urldecode($result_det["minint"])."'>";
			echo "<td><input style='width:30px' maxlength='10' type='text' autocomplete='off' name='maxint". $result_det["id"] . "' id='maxint". $result_det["id"] . "' value='".urldecode($result_det["maxint"])."'>";

		echo "</tr></table>";
	};

?>
<br/>
<button type="submit" id="submit" name="submit" class="tooltipactive btn btn-default " title="Save settings"><span class="glyphicon glyphicon-floppy-disk"></span></button>&nbsp;&nbsp;
<a href='?action=devs&rig=<?php echo $rig;?>&remove=<?php echo $result_det["number"];?>&hidepage=yes'><button type="button" class="tooltipactive btn btn-default " title="Remove your Device"><span class="glyphicon glyphicon-trash"></span></button></a>&nbsp;&nbsp;
<a href='?action=home&rig=<?php echo $rig;?>'><button type="button" class="tooltipactive btn btn-default " title="Back to the Rig dashboard"><span class="glyphicon glyphicon-home"></span></button></a>&nbsp;&nbsp;
</table><br/><br/>

