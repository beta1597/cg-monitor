<?php
if(empty($_SESSION['MM_Username'.$secret_passphrase]) and $_SESSION['MM_Username'.$secret_passphrase] != "okuser"){
exit;
};

	$table = "cgmonitor__pools";

	if(isset($_POST['update'])){
		if($_POST["new"]!=""){
			$new 		= returnpostvaluenew("new");
			$sql_new	= "INSERT INTO `".$table."` (`id`, `name`) VALUES ('','".$new."');";
			result($sql_new);
		};
	};
?>
<h1>Pools</h1>
<div style="clear:both"></div>

<form method="post">
<input type="hidden" id="update" name="update" value="update">
	<div class="table-responsive">
		<table class="table table-striped">
		<tr>
			<th><span class="tooltipactive">Name</span></th>
			<th><span class="tooltipactive">Stats page</span></th>
			<th><span class="tooltipactive">Algorithm</span></th>
			<th><span class="tooltipactive">Live BTC/Mhs</span></th>
			<th><span class="tooltipactive">Yesterday</span></th>
			<th><span class="tooltipactive">&nbsp;</span></th>

		</tr>
<?php
	$sql_sel = "SELECT * FROM `".$table."` order by name;";
	$res_sel = result($sql_sel);
	foreach($res_sel as $result_det){
		echo "<tr>";
			echo "<td>".urldecode($result_det["name"])."";
			
			echo "<td>&nbsp;";
			if($result_det["statspage"] != "") echo "<a target='_blank' href='".urldecode($result_det["statspage"])."'>Stats</a>";

			echo "<td>";
			
			$sql_sel 	= "SELECT * FROM `cgmonitor__algorithm` where id > 1 order by number;";
			$algorithms = result($sql_sel);
			foreach($algorithms as $algorithm){
				if($result_det["idalgorithms"] == $algorithm["id"]) echo $algorithm["name"];
			};
			echo "</td>";
			echo "<td><span id='btcmhslive".$result_det["id"]."'></span>";
			echo "<td><span id='btcmhsaverage".$result_det["id"]."'></span>";
			
		echo "</td><td><a href='?action=pool&pool=". $result_det["id"] ."'><button type='button' class='btn btn-default'><span class='glyphicon glyphicon-wrench'></span></button></a></td>";
		echo "</tr>";
		
		echo "<script>$(function() { $('#btcmhsaverage".$result_det["id"]."').load('./json/?action=poolpicker&pool=".$result_det["address"]."');$('#btcmhslive".$result_det["id"]."').load('./json/?action=live_pool&pool=".$result_det["address"]."');});</script>";
	};
	
?>
	</table></div>
	<br/>
	<input style='width:150px' maxlength='400' type='text' autocomplete='off' name='new' id='new' value='' class='form-control' placeholder='New pool' ><br/>
	<button type="submit" id="submit" name="submit" class="tooltipactive btn btn-default " title="Save Pools"><span class="glyphicon glyphicon-floppy-disk"></span></button>&nbsp;&nbsp;

