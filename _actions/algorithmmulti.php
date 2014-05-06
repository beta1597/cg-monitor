<?php
if(empty($_SESSION['MM_Username'.$secret_passphrase]) and $_SESSION['MM_Username'.$secret_passphrase] != "okuser"){
exit;
};

$table = "cgmonitor__algorithm";

if(isset($_GET['removepool'])){
	$sql_del = "DELETE FROM `cgmonitor__groupsvspools` WHERE idgroups = '1' and idpools='".mysql_real_escape_string(get('removepool'))."';";
	result($sql_del);
};
if(isset($_POST['update'])){
	$sql_sel = "SELECT * FROM `".$table."` order by id;";
	$result = result($sql_sel);

	foreach($result as $result_det){
		if(isset($_POST["rentpool".$result_det["id"]])){
			$fields 	 .= returnpostvalue("rentpool",$result_det["id"]) . "";
			$sql_upd = "UPDATE `".$table."` set ".$fields." where id = '".$result_det["id"]."';";
			result($sql_upd);
		};
		if(isset($_POST["addalgo".$result_det["id"]]) and $_POST["addalgo".$result_det["id"]] != ''){
			$max_sql = "SELECT max(sortorder) as max FROM `cgmonitor__groupsvspools`;";
			$max_res = result($max_sql);
			$sql_ins = "INSERT INTO `cgmonitor__groupsvspools` (`id`, `idgroups`, `idpools`,`sortorder`) VALUES ('','".$result_det["id"]."','".mysql_real_escape_string(get("addalgo".$result_det["id"]))."','".++$max_res[0]["max"]."');";
			result($sql_ins);
		};
	};
};

?>
<form method="post">
<input type="hidden" id="update" name="update" value="update">

	
<?php
	$sql_sel = "SELECT * FROM `".$table."` where id = '1';";
	$res_sel = result($sql_sel);
	foreach($res_sel as $result_det){

?>
	<h1>Multi Algorithm Miner (sgminer 4.1)</h1>
	<div style="clear:both"></div>
		<h3>Backup Pools</h3>
			<table id="table-<?php echo $result_det["id"];?>" class="tablesmall">
<?php
	$sql_sel 	= "SELECT cgmonitor__pools.id as poolid,cgmonitor__pools.name as poolname,cgmonitor__groupsvspools.sortorder as sortorder FROM cgmonitor__groupsvspools JOIN cgmonitor__algorithm ON cgmonitor__groupsvspools.idgroups =cgmonitor__algorithm.id JOIN cgmonitor__pools ON cgmonitor__groupsvspools.idpools =cgmonitor__pools.id where cgmonitor__groupsvspools.idgroups = '".$result_det["id"]."' order by cgmonitor__groupsvspools.idgroups, cgmonitor__groupsvspools.sortorder";
	$res_pools 	= result($sql_sel);
	$alreadyinarray = array();
	
	foreach($res_pools as $pool){
		echo "<tr id='".$pool["poolid"]."'>";
		echo '<td width="1%" class="dragHandle">&nbsp;</td>';
		echo "<td>&nbsp;&nbsp;&nbsp;".urldecode($pool["poolname"])."</td>";
		echo "<td>&nbsp;&nbsp;&nbsp;<a href='?action=".$_GET['action']."&removepool=". $pool["poolid"] ."'><button type='button' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-trash'></span></button></a></td>";
		echo "</tr>";
		$alreadyinarray[] = $pool["poolid"];
	};
	echo "    </table><br/><br/><div ><select name='addalgo".$result_det["id"]."' id='addalgo".$result_det["id"]."' class='selectpicker' ><option value=''></option>";

	$sql_pools 	= "SELECT cgmonitor__pools.id as id,cgmonitor__pools.name as poolname, cgmonitor__algorithm.name as algoname
						FROM cgmonitor__pools
						JOIN cgmonitor__algorithm
						ON cgmonitor__algorithm.id =cgmonitor__pools.idalgorithms 
						ORDER by cgmonitor__algorithm.name, cgmonitor__pools.name";


	
	$result_pools	= result($sql_pools);

	$algo = "";
	
	$poolsallow = array();
	foreach($result_pools as $pool){
		if(in_array($pool["id"], $alreadyinarray, true)){
		}else{
			$poolsallow[] = $pool;
		}
	};

	foreach($poolsallow as $pool){
		if($algo != $pool["algoname"]){
			if($algo != "") echo "</optgroup>";
			echo "<optgroup label='".urldecode($pool["algoname"])."'>";
		};
		
		echo "<option value='".$pool["id"]."'>".urldecode($pool["poolname"])."</option>";

		$algo = $pool["algoname"];
	};
?>
	</optgroup>
	</select></div>
	<button type="submit" id="submit" name="submit" class="tooltipactive btn btn-default " title="Add backup pool"><span class="glyphicon glyphicon glyphicon-plus"></span></button>
	<h3>Use first pool as renting Pool</h3>
<?php
			echo "<select name='rentpool". $result_det["id"] . "' id='rentpool". $result_det["id"] . "' class='selectpicker' data-width='120px'>";

			echo "<option value='no' ";
			if($result_det["rentpool"] == "no") echo "SELECTED";
			echo ">No</option>";

			echo "<option value='yes' ";
			if($result_det["rentpool"] == "yes") echo "SELECTED";
			echo ">Yes</option>";

			echo "</select>";	
?>
<br/>
<button type="submit" id="submit" name="submit" class="tooltipactive btn btn-default " title="Save Algorithm"><span class="glyphicon glyphicon-floppy-disk"></span></button>


<?php
	};
?>

<script>
$(document).ready(function() {
<?php
	foreach($res_sel as $result_det){
?>
 				$('#table-<?php echo $result_det["id"];?>').tableDnD({
					onDrop: function(table, row) {
						$('#result').load("./json/?action=sortalgo&algo=<?php echo $result_det["id"];?>&"+$.tableDnD.serialize());
					},
					dragHandle: "dragHandle"
				});
<?php
	}
?>
});

</script>