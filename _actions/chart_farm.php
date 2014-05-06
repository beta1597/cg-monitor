<?php
	include_once("_configfile.php");
	include_once("_functions.php");
	include_once('_cryptastic.php');
	$width 	= get("width");

	if($width == "0" or $width == "100"){
		$width = "80%";
	}else{
		$width = $width - 30;
		if($width == "-30") $width= 900;
		$width = $width . "px";
	};

if(empty($_SESSION['MM_Username'.$secret_passphrase]) and $_SESSION['MM_Username'.$secret_passphrase] != "okuser"){
exit;
};

	$gpu 		= get("gpu");
	$rig 		= get("rig");
	$value	 	= get("value");

	if($value == ""){
		$values		= array("WUs");
	}else if($value == "MHS av"){
		$values		= array("MHS av","MHS 1s");
	}else{
		$values		= array($value);
	};

	
	$test 		= array();
	$timenow = mktime (date("H"), date("i") ,0, date("m") , date("d"),date("Y") );
	$timeyesterday = mktime (date("H"), date("i") ,0, date("m") , date("d")-2 ,date("Y") );
	
	$sql_devs = "SELECT * FROM `cgmonitor__rigs` order by name;";
	$rigs = result($sql_devs);
	
		foreach($rigs as $rig){
			$sql3 = "SELECT * FROM `miner_".$rig["id"]."` where Time > '".$timeyesterday."' order by Time";
			$result3 = result($sql3);
								
			foreach($result3 as $result_det){
					$i++;
					$stamp 	= substr($result_det['Time'], 0,12);

					$hour 	= substr($result_det['Time'], 8,2);
					$min 	= substr($result_det['Time'], 10,2);

					foreach($values as $value){
						$hashwu 	= number_format(($result_det["WUs"] / $result_det["MHS av"]) * 100, 2, '.', '');
						
						$temp = 1000 * $result_det[$value];
						$temp_array[$stamp][$rig["id"]]["value"][$value] 		= $temp;
						$temp_array[$stamp][$rig["id"]]["value"]["hashwu"] 	= $hashwu;
					};
				};
		};


	$retun = array();

	for ($i = $timeyesterday; $i <= $timenow; $i += 60) {
		$timecheck 	= date("YmdHi", $i );
		$timecheck2 = date("YmdHi", $i - 60 );
		$timecheck3 = date("YmdHi", $i - 120 );
		$year 		= substr($timecheck, 0,4);
		$month 		= substr($timecheck, 4,2);
		$month 		= $month -1;
		$day 		= substr($timecheck, 6,2);
		$hour 		= substr($timecheck, 8,2);
		$min 		= substr($timecheck, 10,2);
	
		$time_ck 	= date("H:i", $i );
		$percent 	= '';
		
		foreach($values as $value){

			$count_rigs = 0;
			$totalWUs = 0;
			
			foreach($rigs as $rig){
				if(isset($temp_array[$timecheck][$rig["id"]])){
					$temp = $temp_array[$timecheck][$rig["id"]]["value"][$value];
					$WUs  = $temp_array[$timecheck][$rig["id"]]["value"]["hashwu"];

				}else if(isset($temp_array[$timecheck2][$rig["id"]])){
					$temp = $temp_array[$timecheck2][$rig["id"]]["value"][$value];
					$WUs  = $temp_array[$timecheck2][$rig["id"]]["value"]["hashwu"];

				}else if(isset($temp_array[$timecheck3][$rig["id"]])){
					$temp = $temp_array[$timecheck3][$rig["id"]]["value"][$value];
					$WUs  = $temp_array[$timecheck3][$rig["id"]]["value"]["hashwu"];

				}else{
					$temp = 0;
					$WUs = 0;
				};
				if($WUs != 0){
					$count_rigs++;
					$totalWUs = $totalWUs + $WUs;
				};
				
				$profit[$rig["id"]][$value] 	.= $temp .",";
				$percent 						.= ",perc".$rig["id"].":'".$WUs."'";
			};
		};
		$totalWUs = number_format(($totalWUs / $count_rigs), 2, '.', '');
		$percent_data	 	.= $totalWUs . ",";
		$profit["categ"] 	.= "{x:'".$hour.":".$min."',perc:'".$totalWUs."'".$percent."},";
	};
	foreach($values as $value){

?>

<div id='container<?php echo str_replace(" ", "", $value);?>' style='height: 300px;width:<?php echo $width;?>'></div>
<script type='text/javascript'>
$(function () {
        $('#container<?php echo str_replace(" ", "", $value);?>').highcharts({
            chart: {
                type: 'area'
            },
            title: {
                text: ''
            },
            xAxis: {
				type: 'text',
                categories: [<?php echo $profit["categ"];?>],
			    lineWidth: 0,
			    minorGridLineWidth: 0,
			    lineColor: 'transparent',
                title: {
                    text: null
                },				 
			    labels: {
				   enabled: false
			    },
			    tickInterval: 3600 * 1000
            },
            yAxis: [{
                title: {
                    text: '<?php echo $value;?>'
                },
				opposite: true
            }
			
<?php
if($value == "WUs"){
?>
			,{
                title: {
                    text: 'Total Percent'
                }
            }
<?php
};
?>		
			],
            tooltip: {
				formatter: function() {
					var s = '<b>'+ this.x.x +'</b>',
					sum = 0;

<?php
if($value == "WUs"){
?>
					
					$.each(this.points, function(i, point) {
						var str = point.series.name;
						var res = str.split("|");

						if(res[1] != undefined){
						s += '<br/>'+ res[1] + ': '+ Math.round(point.y) +' KH/s (' + this.x['perc' + res[0]] + '%)';
						sum += point.y;
						};
					});
					
					s += '<br/><b>Total: '+ Math.round(sum) + ' KH/s (' + this.x['perc'] + '%)</b>';
<?php
}else{
?>					
					$.each(this.points, function(i, point) {
						var str = point.series.name;
						var res = str.split("|");

						s += '<br/>'+ res[1] + ': '+
							Math.round(point.y) +' KH/s';
						sum += point.y;
					});
					
					s += '<br/><b>Total: '+ Math.round(sum) + ' KH/s</b>';

<?php
};
?>
					return s;
				},
                shared: true,
                valueSuffix: ' KH/s'
            },
            plotOptions: {
                area: {
                    stacking: 'normal',
                    lineColor: '#666666',
                    lineWidth: 1,
                    marker: {
                        lineWidth: 0,
                        lineColor: '#666666'
                    }
                }
            },
            series: [
<?php
			foreach($rigs as $rig){
?>		
			{
                name: '<?php echo $rig["id"] . "|" . $rig["name"];?>',
                data: [<?php echo $profit[$rig["id"]][$value];?>],
				marker: {
                    enabled: false
                }
            },
<?php
};
if($value == "WUs"){
?>
			{
                name: 'WU Total percent',
				type: 'spline',
                data: [<?php echo $percent_data;?>],
				yAxis: 1,
				marker: {
                    enabled: false
                }
            },
<?php
};
?>
			]
        });
    });
    
</script>

<?php
};
?>

