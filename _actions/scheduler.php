<?php
if(empty($_SESSION['MM_Username'.$secret_passphrase]) and $_SESSION['MM_Username'.$secret_passphrase] != "okuser"){
exit;
};

if(isset($_POST['update'])){

		$action_f 	= get("action_f");
		$pool 		= get("pool");
		$rig 		= get("rig");
		$time 		= get("time");
		$date_f 	= get("date_f");
		$repeat 	= get("repeat");
		$device		= get("device");
		$int 		= get("int");

		$rig 				= mysql_escape_string($rig);
		$sql_rig			= "SELECT * FROM  `cgmonitor__rigs` where id = '".$rig."';";
		$result_rig			= result($sql_rig);
		$privatekey 		= $result_rig["0"]["privatekey"];
		$rig 				= $result_rig["0"]["id"];

		$time 				= explode(":", $time);
		$date_f 			= explode("-", $date_f);

		$timenow 		= mktime ($time[0] - $timezone, $time[1] ,"0", $date_f[1] , $date_f[2] ,$date_f[0]);

		$time		 	= date("YmdHis",$timenow);
		

		if($action_f == "switch"){
			$sql_pool = "SELECT name FROM `cgmonitor__pools` where id ='".$pool."';";
			$res_pool = result($sql_pool);
			
			$command_read = "Scheduled Switch to pool ".$res_pool[0]["name"];
		}else if($action_f == "reboot"){
			$command_read = "Scheduled reboot";
		}else if($action_f == "resetstats"){
			$command_read = "Scheduled Reset stats";
		}else if($action_f == "intensity"){
			$device 			= explode("|", $device);
			$rig				= $device[0];
			$device				= $device[1];
			$command_read = "Scheduled Intensity set : DEV:".$device." : INT ".$int;
		};

		$command 	= $action_f . ";" . $pool . ";" . $device . ";" . $int . ";".$repeat;
				
		$command_enc	= encryptCommand($command,$privatekey);

		$sql_ins		= "INSERT INTO  `cgmonitor__actions` (`id` ,`idrigs` ,`name` ,`command` ,`time` ,`timeclosed` ,`status`) VALUES (NULL ,  '".$rig."', '".$command_read."' , '".$command_enc."',  '".$time ."',  '',  'scheduled');";
		result($sql_ins);
		
		echo "Schedule saved<br/>";
		
		exit;
};
?>
<h1>Scheduler</h1>
<div style="clear:both"></div>
<p class="label label-info">Still in early development, check always in the actions tab.</p><br/><br/>

<form method="post">
<input type="hidden" id="update" name="update" value="update">
	<div>
	<select name='action_f' id='action_f' class='selectpicker' data-live-search='true' >
		<option value='switch'>Switch</option>
		<option value='reboot'>Reboot</option>
		<option value='resetstats'>Resetstats</option>
		<option value='intensity'>Intensity</option>
	</select>
	<br/>
	<select name='pool' id='pool' class='selectpicker' data-live-search='true' >

<?php
	$timenow 		= mktime (date("H") + $timezone, date("i") ,"0", date("m") , date("d") ,date("Y"));

	$sql_pools 		= "SELECT cgmonitor__pools.id as id,cgmonitor__pools.name as poolname, cgmonitor__algorithm.name as algoname FROM cgmonitor__pools JOIN cgmonitor__algorithm ON cgmonitor__algorithm.id =cgmonitor__pools.idalgorithms ORDER by cgmonitor__algorithm.number, cgmonitor__pools.name";
	$result_pools	= result($sql_pools);

	$algo = "";
	foreach($result_pools as $pool){
		if($algo != $pool["algoname"]){
			if($algo != "") echo "</optgroup>";
			echo "<optgroup label='".urldecode($pool["algoname"])."'>";
		};
		echo "<option value='".$pool["id"]."'>".urldecode($pool["poolname"])."</option>";
		$algo = $pool["algoname"];
	};
	echo "</optgroup>";
?>
	</select>
	<br/>
	<select name='rig' id='rig' class='selectpicker' data-live-search='true' >

<?php
	$sql_rigs 	= "SELECT * FROM `cgmonitor__rigs` order by name;";

	$res_rigs 	= result($sql_rigs);
	
	foreach($res_rigs as $rig){
		echo "<option value='".$rig["id"]."'>".urldecode($rig["name"])."</option>";
	};
?>
	</select><br/>
	<select name='device' id='device' class='selectpicker' data-live-search='true' >

<?php
	$sql_device	= "SELECT cgmonitor__devs.*,cgmonitor__rigs.name  FROM `cgmonitor__devs` join cgmonitor__rigs on cgmonitor__rigs.id = cgmonitor__devs.idrigs order by cgmonitor__rigs.name,number;";

	$res_device = result($sql_device);
	
	$rig = "";
	foreach($res_device as $device){
		if($rig != $device["idrigs"]){
			if($rig != "") echo "</optgroup>";
			echo "<optgroup label='".urldecode($device["name"])."'>";
		};
		echo "<option value='".$device["idrigs"]."|".$device["number"]."'>".urldecode($device["number"])."</option>";
		$rig = $device["idrigs"];
	};
?>
	</select><br/>
<?php	
	echo "<select name='int' id='int' class='selectpicker' data-width='60px'>";
	for($i = 0; $i <= 30; $i++){
		echo "<option value='".$i."'>".$i."</option>";
	};
	echo "</select>";
?>
	<input style='width:150px' id="time" name="time" type="text" class='form-control' value="<?php echo date("H:i",$timenow);?>"><br/>
	<input style='width:150px' id="date_f" name="date_f" type="text" value="<?php echo date("Y-m-d",$timenow);?>" data-date-format="yyyy-mm-dd"  class='form-control' ><br/>
	
	<select name='repeat' id='repeat' class='selectpicker' data-live-search='true' >
		<option value='0'>No Repeat</option>
		<option value='30'>Every 30 min</option>
		<option value='60'>Every hour</option>
		<option value='120'>Every 2 hours</option>
		<option value='360'>Every 6 hours</option>
		<option value='720'>Every 12 hours</option>
		<option value='1440'>Every day</option>
		<option value='2880'>Every 2 days</option>
		<option value='10080'>Every 7 days</option>
	</select>
	
	
</table></div>
<br/>
<button type="submit" id="submit" name="submit" class="tooltipactive btn btn-default " title="Save settings"><span class="glyphicon glyphicon-floppy-disk"></span></button>&nbsp;&nbsp;

<script>
        var nowTemp = new Date();
        var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

$('#time').timepicker({showMeridian:false,minuteStep: 10});
$('#date_f').datepicker({
          onRender: function(date) {
            return date.valueOf() < now.valueOf() ? 'disabled' : '';
          }
        });
$(function() {
	$('#device').selectpicker('hide');
	$('#int').selectpicker('hide');
});


$("#action_f").change(function(){
    value = $(this).children(":selected").html();

		if(value == "Switch"){
			$('#pool').selectpicker('show');
			$('#rig').selectpicker('show');
			$('#device').selectpicker('hide');
			$('#int').selectpicker('hide');
		}else if(value == "Intensity"){
			$('#pool').selectpicker('hide');
			$('#rig').selectpicker('hide');
			$('#device').selectpicker('show');
			$('#int').selectpicker('show');
		}else{
			$('#pool').selectpicker('hide');
			$('#rig').selectpicker('show');
			$('#device').selectpicker('hide');
			$('#int').selectpicker('hide');
		};
});
		
</script>