<?php
if(empty($_SESSION['MM_Username'.$secret_passphrase]) and $_SESSION['MM_Username'.$secret_passphrase] != "okuser"){
	exit;
};

$table 	= "cgmonitor__actions";
$page_s	= get('page',0);

$page 	= $page_s * 25;

$page_n	= $page_s + 1;
$page_l	= $page_s - 1;
if($page_l < 0) $page_l = 0;

$sql_numbers = "SELECT count(id) as number FROM `cgmonitor__actions`;";
$res_numbers = result($sql_numbers);
$number = round($res_numbers[0]["number"] / 25);
if($page_n > $number) $page_n = $number;
	
if(isset($_GET['remove'])){
	$sql2 = "DELETE FROM `".$table."` WHERE id='".mysql_escape_string(($_GET['remove']))."';";
	result($sql2);
};

?>
<a href='?action=actions&page=<?php echo $page_l;?>'><button type="button" class="tooltipactive btn btn-default " title="Prev page"><span class="glyphicon glyphicon-chevron-left"></span></button></a>
<a href='?action=actions&page=<?php echo $page_n;?>'><button type="button" class="tooltipactive btn btn-default " title="Next page"><span class="glyphicon glyphicon-chevron-right"></span></button></a>
<br/><br/>
<div class="table-responsive">
<table class="table table-striped">
<form method="post">
<input type="hidden" id="update" name="update" value="update">
	<tr><th>Time</th><th>Rig</th><th>Command</th><th>Time Closed</th><th>Status<th>Remove</th></tr>
<?php
	$sql2 = "SELECT cgmonitor__actions.*,cgmonitor__rigs.name as rigname FROM `cgmonitor__actions` JOIN cgmonitor__rigs ON cgmonitor__actions.idrigs =cgmonitor__rigs.id order by time desc limit ".$page.",25;";

	$result2 = result($sql2);

	foreach($result2 as $result_det){
		$time 		= smalldaterevert($result_det["time"],$timezone);
		$timeclosed = smalldaterevert($result_det["timeclosed"],$timezone);
		echo "<tr>";
			echo "<td>".$time["Y"]."-".$time["m"]."-".$time["d"]." ".$time["H"].":".$time["i"];
			echo "<td>".urldecode($result_det["rigname"]);
			echo "<td>".urldecode($result_det["name"]);
			if($result_det["timeclosed"] == ""){
				echo "<td>&nbsp;";
			}else{
				echo "<td>".$timeclosed["Y"]."-".$timeclosed["m"]."-".$timeclosed["d"]." ".$timeclosed["H"].":".$timeclosed["i"];
			}
			echo "<td>".urldecode($result_det["status"]);
			echo '</td><td>&nbsp;&nbsp;&nbsp;<a href="?action='.$_GET['action'].'&remove='. $result_det["id"] .'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-trash"></span></button></a></td>';
		echo "</tr>";
	};
	
?>
	</table></div>
	<br/>
