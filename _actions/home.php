<?php
if(empty($_SESSION['MM_Username'.$secret_passphrase]) and $_SESSION['MM_Username'.$secret_passphrase] != "okuser"){
exit;
};

	$rig			= get("rig","%");
	
	$timenow 		= date("YmdHis");
	$timeoffline 	= mktime (date("H"), date("i") - 20 ,date("s"), date("m") , date("d") ,date("Y"));
	$timeoffline	= date("YmdHis",$timeoffline);

?>
	<div class='totalfloat' style=''>
	<span id='t-l1'></span><br/>
	<span id='t-l2'></span><br/>
	<span id='t-l3'></span><br/>
	<span id='t-l4'></span><br/>
	</div>

	<div>
	<select name='poolswitch' id='poolswitch' class='selectpicker' data-live-search='true' >

<?php
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
	</div>
	
	<div >
		<button id='switch' title='Switch from pool' class='tooltipactive btn btn-default' >Switch</button>
	</div>
	<input type="hidden" name="time" id="time" value="<?php echo $timenow;?>" />

<?php
				if($rig == "%"){
?>
				<h1 class="specialfont">Rig Overview</h1>
				&nbsp;&nbsp;&nbsp;<button type="button" class="tooltipactive btn btn-default  btn-sm titlehome" title="Add a new Rig" data-toggle="modal" data-target="#modalnewrig"><span class=" glyphicon glyphicon-plus-sign"></span></button>
				&nbsp;&nbsp;<a href="?action=alerts"><button type="button" class="tooltipactive btn btn-default  btn-sm titlehome" title="Alerts"><span class="glyphicon glyphicon-warning-sign"></span></button></a>
<?php
						}else{
?>
				<h1 class="specialfont">Rig Detail</h1>
				
				&nbsp;&nbsp;&nbsp;<a href='?action=rigs&rig=<?php echo $rig;?>'><button type="button" class="tooltipactive btn btn-default  btn-sm titlehome" title="Change Rig Settings"><span class="glyphicon glyphicon-wrench"></span></button></a>
				&nbsp;&nbsp;<button type="button" class="tooltipactive btn btn-default btn-sm reboot titlehome" rig="<?php echo $rig;?>" title="Reboot your Rig"><span class="glyphicon glyphicon-flash"></span></button>
				&nbsp;&nbsp;<button type="button" class="tooltipactive btn btn-default btn-sm resetstats titlehome" rig="<?php echo $rig;?>"  title="Reset stats"><span class="glyphicon glyphicon-floppy-remove"></span></button>
				&nbsp;&nbsp;<a href='?action=home'><button type="button" class="tooltipactive btn btn-default  btn-sm titlehome" title="Back to the Rig dashboard"><span class="glyphicon glyphicon-home"></span></button></a>&nbsp;&nbsp;
<?php
						};
?>
					<br/>
					<br/>
					<div class="table-responsive">
					<img class="loading" src="./images/loading.gif"/>
					<table class="table" style="visibility:hidden;">
					<tr>
						<th style="text-align:left">Status</th>
						<th style="text-align:left">Miner</th>
						<th style="text-align:left">Status</th>
						<th style="text-align:left">Uptime</th>
						<th style="text-align:left">MH/s</th>
						<th style="text-align:left">WU (%)</th>
						<th style="text-align:left">A / R</th>
						<th style="text-align:left">Pool</th>
						<th style="text-align:left"><input class="checkbox2" type="checkbox" id="selectall"></th>
					</tr>
<?php
				$sql = "SELECT * FROM `cgmonitor__rigs` where id Like '".mysql_real_escape_string($rig)."' order by name;";
				$result = result($sql);
				foreach($result as $row){
?>

						<tr id='tr-<?php echo $row["id"];?>-c1'>
						<td align='center' ><span id='icon-<?php echo $row["id"];?>-c1'></span>
						<td ><a href='?action=home&rig=<?php echo $row["id"];?>' class='tooltipactive' title='Detail of this rig'><?php echo urldecode($row["name"]);?></a></td>
						<td ><span id='<?php echo $row["id"];?>-c2'></span></td>
						<td ><span id='<?php echo $row["id"];?>-c3'></span></td>
						<td ><a href='http://<?php echo $_SERVER['SERVER_NAME'] .$base_url;?>?action=chart_rig&hidepage=yes&rig=<?php echo $row["id"];?>&value=<?php echo urlencode("MHS av");?>' data-target='#modalgraph'><span id='<?php echo $row["id"];?>-c4'></span><a/></td>
						<td ><a href='http://<?php echo $_SERVER['SERVER_NAME'] .$base_url;?>?action=chart_rig&hidepage=yes&rig=<?php echo $row["id"];?>&value=<?php echo urlencode("WUs");?>' data-target='#modalgraph'><span id='<?php echo $row["id"];?>-c5'></span><a/></td>
						<td ><span id='<?php echo $row["id"];?>-c6'></span></td>
						<td ><span id='<?php echo $row["id"];?>-c7'></span></td>
						<td ><input class='checkbox' type='checkbox' id='<?php echo $row["id"];?>-switch' rig='<?php echo $row["id"];?>' name='<?php echo $row["name"];?>'></td>
<?php
						if($rig != "%"){
						
							$contents .= "<span id='".$row["id"]."-url'></span>";
							$contents .= "<table class='table'  style='visibility:hidden;'>";
							$contents .= "<tr><th align='center'>Status</th><th align='center'>Dev</th><th align='center'>Status</th><th align='center'>Load</th><th align='center'>Temp / Fan</th><th align='center'>Hashrate (Avg)</th><th align='center'>WU</th><th align='center'>A / R</th><th align='center'>Engine / Mem @V</th><th align='center'>Inten</th><th>&nbsp;</th><th>&nbsp;</th></tr>";
							
							$sql_devs 	= "SELECT * FROM `cgmonitor__devs` WHERE `idrigs` = '".$row['id']."';";
							$devices	= result($sql_devs);
							
							foreach($devices as $device){
											$contents .= "
											<tr id='tr-".$row["id"]."-".$device['number']."-c1'>
											<td align='center' ><span id='icon-".$row["id"]."-".$device['number']."-c1'></span></td>
											<td >".$device['number']."</td>
											<td id='".$row["id"]."-".$device['number']."-c1'></td>
											<td id='".$row["id"]."-".$device['number']."-c2'></td>
											<td ><a href='http://".$_SERVER['SERVER_NAME'] .$base_url."?action=chart_device&hidepage=yes&value=".urlencode("Temperature")."&rig=".$row['id']."&device=".$device['number']."' data-target='#modalgraph'><span id='".$row["id"]."-".$device['number']."-c3'></span></a></td>
											<td ><a href='http://".$_SERVER['SERVER_NAME'] .$base_url."?action=chart_device&hidepage=yes&value=".urlencode("MHS 1s")."&rig=".$row['id']."&device=".$device['number']."' data-target='#modalgraph'><span id='".$row["id"]."-".$device['number']."-c4'></span></a></td>
											<td ><a href='http://".$_SERVER['SERVER_NAME'] .$base_url."?action=chart_device&hidepage=yes&value=".urlencode("WUs")."&rig=".$row['id']."&device=".$device['number']."' data-target='#modalgraph'><span id='".$row["id"]."-".$device['number']."-c5'></span></a></td>
											<td ><a href='http://".$_SERVER['SERVER_NAME'] .$base_url."?action=chart_device&hidepage=yes&value=".urlencode("Device Rejected%")."&rig=".$row['id']."&device=".$device['number']."' data-target='#modalgraph'><span id='".$row["id"]."-".$device['number']."-c6'></span></a></td>
											<td id='".$row["id"]."-".$device['number']."-c7'></td>";
									
											$contents .= "<td><input type='hidden' id='hid-".$row["id"]."-".$device['number']."-c8'/><select name='poolswitch' device='".$device['number']."' id='".$row["id"]."-".$device['number']."-c8' class='selectpicker intensitychange' data-width='60px'>";
											for($i = 0; $i <= 30; $i++){
												$contents .= "<option value='".$i."'>".$i."</option>";
											};

											$contents .= "</select><script>$('#".$row["id"]."-".$device['number']."-c8').change(function() {
											if($('#hid-".$row["id"]."-".$device['number']."-c8').val() != $(this).val() && $(this).val() != null){
													rig = '".$row['id']."';
											  		device = $( this ).attr( 'device' );
													int = $(this).val();
													action = 'intensity';
													event.preventDefault(); // cancel default behavior
													
													$('#modalyesno #myModalLabel').html('Set intensity');
													$('#modalyesno .modal-text').html('<br/>Are you sure you set int of device '+ device +' to '+ int +'?.');
													$('#modalyesno').modal('show');
											  };
											});</script>";
											
											$contents .= '</td><td align="center" ><a href="http://'.$_SERVER['SERVER_NAME'] .$base_url.'?action=chart_device&hidepage=yes&rig='.$row['id'].'&device='.$device['number'].'" data-target="#modalgraph"><button type="button" class="tooltipactive btn btn-default btn-sm " title="Show Graph"><span class="glyphicon glyphicon-stats"></span></button></a></td>';
											$contents .= '</td><td align="center" ><a href="?action=devs&rig='.$row['id'].'&device='.$device['id'].'"><button type="button" class="tooltipactive btn btn-default  btn-sm " title="Change Device settings"><span class="glyphicon glyphicon-wrench"></span></button></a>&nbsp;&nbsp;</a></td>

											</tr>';
							
							};
						};

				};

				if($rig == "%"){
					$top .= "<tr ><td class='tdtotal'>Total</td>";
					$top .= "<td class='tdtotal' colspan='2'><span id='t-c2'></span></td>";
					$top .= "<td class='tdtotal'>&nbsp;</td>";
					$top .= "<td class='tdtotal'><a href='http://".$_SERVER['SERVER_NAME'] .$base_url."?action=chart_farm&hidepage=yes&value=".urlencode("MHS av")."' data-target='#modalgraph'><span id='t-c4'></span></a></td>";
					$top .= "<td class='tdtotal'><a href='http://".$_SERVER['SERVER_NAME'] .$base_url."?action=chart_farm&hidepage=yes&value=".urlencode("WUs")."' data-target='#modalgraph'><span id='t-c5'></span></a></td>";
					$top .= "<td class='tdtotal'><span id='t-c6'></span></td>";
					$top .= "<td class='tdtotal'><span id=''></span></td>";
				};
$contents .= '
	<div>&nbsp;</div>
	</div>

	<script>

	function loaddata(force)
		{		
			 $.ajax({
			 url: "http://'. $_SERVER['SERVER_NAME'] .$base_url. 'json/?action=getstats&force="+force,
			 dataType: "jsonp",
			  data: {
				featureClass: "P",
				time: $("#time").val()
			 },
			  success: function( data ) {
				//console.log(data);
				$("#time").val(data.time);
				$("#token").val(data.token);
				
				if(data.stats != null){
					jQuery.each(data.stats, function(i, val) {
						str = val.id;
						if(str.substring(str.length-2,str.length) == "c8"){
							$("#hid-" + val.id).val(val.val);
							
							$("#" + val.id).selectpicker("val", val.val);
							if(val.val == 0 || val.val == null){
								$("#" + val.id).prop("disabled",true);
								$("#" + val.id).selectpicker("refresh");
							}else{
								$("#" + val.id).prop("disabled",false);
								$("#" + val.id).selectpicker("refresh");							
							}
						}else if(str.substring(str.length-2,str.length) == "c1"){
							$("#" + val.id).html(val.val);
							if(val.val == "Alive"){
								$("#tr-" + val.id).removeClass( "success danger warning" ).addClass( "success" );
								$("#icon-" + val.id).removeClass( "glyphicon glyphicon-ok glyphicon-warning-sign glyphicon-sort-by-attributes-alt glyphicon-fire glyphicon-eye-close" ).addClass( "glyphicon glyphicon-ok" );
							}else if(val.val == "NoContact"){
								$("#tr-" + val.id).removeClass( "success danger warning" ).addClass( "warning" );
								$("#icon-" + val.id).removeClass( "glyphicon glyphicon-ok glyphicon-warning-sign glyphicon-sort-by-attributes-alt glyphicon-fire glyphicon-eye-close" ).addClass( "glyphicon glyphicon-eye-close" );
							}else if(val.val == "tempmin" || val.val == "tempmax"){
								$("#tr-" + val.id).removeClass( "success danger warning" ).addClass( "warning" );
								$("#icon-" + val.id).removeClass( "glyphicon glyphicon-ok glyphicon-warning-sign glyphicon-sort-by-attributes-alt glyphicon-fire glyphicon-eye-close" ).addClass( "glyphicon glyphicon-fire" );
							}else if(val.val == "tempmin" || val.val == "hashmin"){
								$("#tr-" + val.id).removeClass( "success danger warning" ).addClass( "warning" );
								$("#icon-" + val.id).removeClass( "glyphicon glyphicon-ok glyphicon-warning-sign glyphicon-sort-by-attributes-alt glyphicon-fire glyphicon-eye-close" ).addClass( "glyphicon glyphicon-sort-by-attributes-alt" );
							}else{
								$("#tr-" + val.id).removeClass( "success danger warning" ).addClass( "danger" );
								$("#icon-" + val.id).removeClass( "glyphicon glyphicon-ok glyphicon-warning-sign glyphicon-sort-by-attributes-alt glyphicon-fire glyphicon-eye-close" ).addClass( "glyphicon glyphicon-warning-sign" );
							};

						}else{
							$("#" + val.id).html(val.val);
						};
					  if(force != "yes"){
						$("#" + val.id).css("color", "#33CC33");
						window.setInterval(setTimeout(function() {   setback("#" + val.id);}, 5000));
					  }else{
						$(".table").css("visibility","visible");
						$(".loading").hide();
					  };
					});
				};
			  },
			  error: function (error) {
					 console.log(error);
			  }
			});
		};
	
	loaddata("yes");
	setInterval( "loaddata();", 10000 );
	$(".checkbox2").checkbox();

	</script>
	';
						
$top .= "</tr></table><br/><br/>";

echo $top;
echo $contents;			

echo "</table></div>";
?>

<!-- Modal modalgraph -->
<div class="modal fade" id="modalgraph" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                 <h4 class="modal-title">Graph</h4>
            </div>
            <div class="modal-body">
			<img class="modal-loading" src="./images/loading.gif"/>
			<div class="modal-span">
			</div>
			</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal new rig-->

<div class="modal fade" id="modalnewrig" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Add new Rig</h4>
      </div>
      <div class="modal-body">
		<div class="form-group">
			<label for="inputEmail3" class="col-sm-3 control-label">Rig name</label>
			<div class="col-sm-8">
			  <input type="text" class="form-control" autocomplete='off' name='newrigname' id='newrigname' value='' placeholder="New Rig">
			</div>
		  </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="savenewrig">Save Rig</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal modalchangepool-->

<div class="modal fade" id="modalyesno" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel"></h4>
      </div>
      <div class="modal-body">
		<span class="modal-text"></span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-primary" id="buttonyesaction">Yes</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal modalerror-->

<div class="modal fade" id="modalerror" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel"></h4>
      </div>
      <div class="modal-body">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<style>
.datepicker {z-index: 1151 !important;}
.bootstrap-timepicker {}
</style>
