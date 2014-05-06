<?php
if(empty($_SESSION['MM_Username'.$secret_passphrase]) and $_SESSION['MM_Username'.$secret_passphrase] != "okuser"){
exit;
};

$table 	= "cgmonitor__rigs";
$rig 	= get("rig");

if(isset($_GET['remove'])){
	$remove = get('remove');
	
	$sql_del = "DELETE FROM `".$table."` WHERE id='".mysql_real_escape_string($remove)."';";
	result($sql_del);
	
	$sql_del = "DROP TABLE miner_".$remove.";";
	result($sql_del);
	$sql_del = "DROP TABLE miner_".$remove."_DEVS_1;";
	result($sql_del);
	$sql_del = "DROP TABLE miner_".$remove."_DEVS_2;";
	result($sql_del);
	$sql_del = "DROP TABLE miner_".$remove."_DEVS_3;";
	result($sql_del);
	$sql_del = "DROP TABLE miner_".$remove."_DEVS_4;";
	result($sql_del);
	$sql_del = "DROP TABLE miner_".$remove."_DEVS_5;";
	result($sql_del);
	$sql_del = "DROP TABLE miner_".$remove."_DEVS_6;";
	result($sql_del);
	
	header("Location: .?");
	
};
if(isset($_POST['update'])){
	$sql_sel = "SELECT * FROM `".$table."` order by id;";
	$res_sel = result($sql_sel);

	foreach($res_sel as $result_det){
		if(isset($_POST["name".$result_det["id"]])){
			$fields 	  = returnpostvalue("name",$result_det["id"]) . ",";
			$fields 	 .= returnpostvalue("privatekey",$result_det["id"]) . ",";
			$fields 	 .= returnpostvalue("uniqueid",$result_det["id"]) . ",";
			$fields 	 .= returnpostvalue("worker",$result_det["id"]) . ",";
			$fields 	 .= returnpostvalue("changealgorithm",$result_det["id"]) . ",";
			$fields 	 .= returnpostvalue("autosync",$result_det["id"]) . ",";
			$fields 	 .= returnpostvalue("multialgominer",$result_det["id"]) . ",";
			$fields 	 .= returnpostvalue("os",$result_det["id"]) . "";

			$sql_upd = "UPDATE `".$table."` set ".$fields." where id = '".$result_det["id"]."';";
			result($sql_upd);
		};
	};

};
if(get("new")!=""){
	$new 		= returnpostvaluenew("new");
	$sql_ins 	= "INSERT INTO `".$table."` (`id`, `name`,`privatekey`,`uniqueid`,`multialgominer`) VALUES ('','".$new."','".generatesalt()."','".generatesalt()."','no');";
	result($sql_ins);
		
	$max_sql 	= "SELECT max(id) as max FROM `".$table."`";
	$max 		= result($max_sql);

	$sql_crea 	= "CREATE TABLE IF NOT EXISTS `miner_".$max[0]["max"]."` ( `Time` varchar(200) DEFAULT NULL,`MHS av` varchar(100) DEFAULT NULL,`MHS 1s` varchar(100) DEFAULT NULL,`Device Rejected%` varchar(100) DEFAULT NULL, `WUs` varchar(100) NOT NULL, `Last Share Pool` varchar(100) DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
	result($sql_crea);
	
	$rig = $max[0]["max"];
	
};
?>

<form method="post">
<input type="hidden" id="update" name="update" value="update">


<?php
	$sql_sel = "SELECT * FROM `".$table."` where id = '".$rig."' order by name;";
	$res_sel = result($sql_sel);
	foreach($res_sel as $result_det){
?>
  <h3><?php echo urldecode($result_det["name"]);?></h3>

<?php

			echo "<div class='table-responsive'><table style=''><tr><td style='vertical-align: top;'>";
			echo "<b>Name:</b><input style='width:100px' maxlength='400' class='form-control' type='text' autocomplete='off' name='name". $result_det["id"] . "' id='name". $result_det["id"] . "' value='".urldecode($result_det["name"])."'>";

			echo "<br/><td style='vertical-align: top;padding-left:40px;'><b>Operating System:</b><br/><select name='os". $result_det["id"] . "' id='os". $result_det["id"] . "' class='selectpicker' data-width='150px'>";

			echo "<option value='linux1' ";
			if($result_det["os"] == "linux1") echo "SELECTED";
			echo ">linux (bamt)</option>";

			echo "<option value='linux2' ";
			if($result_det["os"] == "linux2") echo "SELECTED";
			echo ">linux (regular)</option>";

			echo "<option value='windows' ";
			if($result_det["os"] == "windows") echo "SELECTED";
			echo ">windows</option>";

			echo "</select>";

			echo "<tr><td><b>Unique id:</b><input style='width:200px' maxlength='400' class='form-control' type='text' autocomplete='off' name='uniqueid". $result_det["id"] . "' id='uniqueid". $result_det["id"] . "' value='".urldecode($result_det["uniqueid"])."'>";
			echo "<br/><td style='vertical-align: top;padding-left:40px;'><b>Automatic change Algorithm:</b><br/><select name='changealgorithm". $result_det["id"] . "' id='changealgorithm". $result_det["id"] . "' class='selectpicker' data-width='100px'>";

			echo "<option value='no' ";
			if($result_det["changealgorithm"] == "no") echo "SELECTED";
			echo ">No</option>";

			echo "<option value='yes' ";
			if($result_det["changealgorithm"] == "yes") echo "SELECTED";
			echo ">Yes</option>";

			echo "</select>";
			echo "<tr><td><b>Private key:</b><input style='width:200px' maxlength='400' class='form-control' type='text' autocomplete='off' name='privatekey". $result_det["id"] . "' id='privatekey". $result_det["id"] . "' value='".urldecode($result_det["privatekey"])."'>";

			echo "<br/><td style='vertical-align: top;padding-left:40px;'><b>Automatic keep pools synced:</b><br/><select name='autosync". $result_det["id"] . "' id='autosync". $result_det["id"] . "' class='selectpicker' data-width='100px'>";

			echo "<option value='no' ";
			if($result_det["autosync"] == "no") echo "SELECTED";
			echo ">No</option>";

			echo "<option value='yes' ";
			if($result_det["autosync"] == "yes") echo "SELECTED";
			echo ">Yes</option>";

			echo "</select>";

			echo "<tr><td><b>Worker name:</b><input style='width:100px' maxlength='400' class='form-control' type='text' autocomplete='off' name='worker". $result_det["id"] . "' id='worker". $result_det["id"] . "' value='".urldecode($result_det["worker"])."'>";

			echo "<td style='vertical-align: top;padding-left:40px;'><b>Multi algo miner active (sgminer 4.1.271 and up):</b><br/><select name='multialgominer". $result_det["id"] . "' id='multialgominer". $result_det["id"] . "' class='selectpicker' data-width='100px'>";

			echo "<option value='no' ";
			if($result_det["multialgominer"] == "no") echo "SELECTED";
			echo ">No</option>";

			echo "<option value='yes' ";
			if($result_det["multialgominer"] == "yes") echo "SELECTED";
			echo ">Yes</option>";

			echo "</select>";			
?>
			</td>

<?php	
	};
?>
</table></div>
<br/>
<button type="submit" id="submit" name="submit" class="tooltipactive btn btn-default " title="Save settings"><span class="glyphicon glyphicon-floppy-disk"></span></button>&nbsp;&nbsp;
<a href='?action=rigs&remove=<?php echo $rig;?>&hidepage=yes'><button type="button" class="tooltipactive btn btn-default " title="Remove your Rig"><span class="glyphicon glyphicon-trash"></span></button></a>&nbsp;&nbsp;
<a href='?action=home&rig=<?php echo $rig;?>'><button type="button" class="tooltipactive btn btn-default " title="Back to the Rig dashboard"><span class="glyphicon glyphicon-home"></span></button></a>&nbsp;&nbsp;