<?php
if(empty($_SESSION['MM_Username'.$secret_passphrase]) and $_SESSION['MM_Username'.$secret_passphrase] != "okuser"){
exit;
};

$table = "cgmonitor__alerts";

if(isset($_POST['update'])){
	$sql_sel = "SELECT * FROM `".$table."` order by id;";
	$res_sel = result($sql_sel);

	foreach($res_sel as $result_det){
		if(isset($_POST["tempactiveemail".$result_det["id"]])){
			$fields 	 = returnpostvalue("tempactiveemail",$result_det["id"]) . ",";
			$fields 	.= returnpostvalue("tempmin",$result_det["id"]) . ",";
			$fields 	.= returnpostvalue("tempmax",$result_det["id"]) . ",";
			$fields 	.= returnpostvalue("hashactiveemail",$result_det["id"]) . ",";
			$fields 	.= returnpostvalue("hashmin",$result_det["id"]) . ",";
			$fields 	.= returnpostvalue("deadactiveemail",$result_det["id"]) . ",";
			$fields 	.= returnpostvalue("deadactivereboot",$result_det["id"]) . "";

			$sql_upd = "UPDATE `".$table."` set ".$fields." where id = '".$result_det["id"]."';";
			result($sql_upd);
		};
	};
};
?>
<form method="post">
<input type="hidden" id="update" name="update" value="update">

<?php
	$sql_sel = "SELECT * FROM `".$table."` where idrigs = '0';";
	$res_sel = result($sql_sel);
	foreach($res_sel as $result_det){
?>
  <h3>General Alert Settings</h3>

<?php
			echo "<div class='table-responsive'><table class='table table-striped'><th><td>Email Alert</td><td>Min</td><td>Max</td>";

			
			echo "<tr><td>Temperature";
			echo "<td><select name='tempactiveemail". $result_det["id"] . "' id='tempactiveemail". $result_det["id"] . "'  class='selectpicker' data-width='100px'>";

			echo "<option value='no' ";
			if($result_det["tempactiveemail"] == "no") echo "SELECTED";
			echo ">No</option>";

			echo "<option value='yes' ";
			if($result_det["tempactiveemail"] == "yes") echo "SELECTED";
			echo ">Yes</option>";

			echo "</select>";

			echo "<td><input style='width:40px' maxlength='10' type='text' autocomplete='off' name='tempmin". $result_det["id"] . "' id='tempmin". $result_det["id"] . "' value='".urldecode($result_det["tempmin"])."'>";
			echo "<td><input style='width:40px' maxlength='10' type='text' autocomplete='off' name='tempmax". $result_det["id"] . "' id='tempmax". $result_det["id"] . "' value='".urldecode($result_det["tempmax"])."'>";
			echo "<tr><td>Hash / GPU";

			echo "<td><select name='hashactiveemail". $result_det["id"] . "' id='hashactiveemail". $result_det["id"] . "'  class='selectpicker' data-width='100px'>";
			echo "<option value='no' ";
			if($result_det["hashactiveemail"] == "no") echo "SELECTED";
			echo ">No</option>";
			echo "<option value='yes' ";
			if($result_det["hashactiveemail"] == "yes") echo "SELECTED";
			echo ">Yes</option>";
			echo "</select>";			

			echo "<td><input style='width:80px' maxlength='10' type='text' autocomplete='off' name='hashmin". $result_det["id"] . "' id='hashmin". $result_det["id"] . "' value='".urldecode($result_det["hashmin"])."'>";
			echo "<td>&nbsp;";
			echo "<tr><td>Dead GPU";

			echo "<td><select name='deadactiveemail". $result_det["id"] . "' id='deadactiveemail". $result_det["id"] . "'  class='selectpicker' data-width='100px'>";
			echo "<option value='no' ";
			if($result_det["deadactiveemail"] == "no") echo "SELECTED";
			echo ">No</option>";
			echo "<option value='yes' ";
			if($result_det["deadactiveemail"] == "yes") echo "SELECTED";
			echo ">Yes</option>";
			echo "</select>";
			
			echo "<td colspan='2'><select name='deadactivereboot". $result_det["id"] . "' id='deadactivereboot". $result_det["id"] . "'  class='selectpicker' data-width='100px'>";
			echo "<option value='no' ";
			if($result_det["deadactivereboot"] == "no") echo "SELECTED";
			echo ">No</option>";
			echo "<option value='yes' ";
			if($result_det["deadactivereboot"] == "yes") echo "SELECTED";
			echo ">Yes</option>";
			echo "</select> (auto restart)";
			
			
		echo "</tr></table></div>";
		?>

<?php
	};
?>
<br/>
<input type="submit" id="submit" name="submit" value="Save" class='btn btn-default' >
