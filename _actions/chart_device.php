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

	$device_get	= get("device");
	$rig 		= get("rig");
	$value 		= get("value");

	if($value == ""){
		$values		= array("WUs","Device Rejected%","Temperature","Fan Percent","Intensity");
	}else if($value == "Temperature"){
		$values		= array("Temperature","Fan Percent","Intensity");
	}else{
		$values		= array($value);
	};
	$temp_array 	= array();

	foreach($values as $value){
?>	

	<div id='container<?php echo md5($value);?>' style='height: 300px;width:<?php echo $width;?>'></div>
<?php
	};
	
	echo $value;
	
	$sql_devs = "SELECT * FROM `cgmonitor__devs` WHERE `idrigs` = '".mysql_real_escape_string($rig)."' and `number` = '".mysql_real_escape_string($device_get)."';";
	$devices = result($sql_devs);
	
		foreach($devices as $device){
			$sql3 = "SELECT * FROM `miner_".$rig."_DEVS_".$device_get."` order by Time";
			$result3 = result($sql3);
								
			foreach($result3 as $result_det){
					$i++;
					$stamp 	= substr($result_det['Time'], 0,12);

					$hour 	= substr($result_det['Time'], 8,2);
					$min 	= substr($result_det['Time'], 10,2);

					foreach($values as $value){
						$temp = 1 * $result_det[$value];
						
						if($value == "WUs"){
							$temp = 1000 * $result_det[$value];
						};
						if($value == "MHS 1s"){
							$temp = 1000 * $result_det[$value];
						};						
			
						$temp_array[$stamp][$value] = $temp;
					};
				};
		};


	$timenow = mktime (date("H"), date("i") ,0, date("m") , date("d"),date("Y") );
	$timeyesterday = mktime (date("H"), date("i") ,0, date("m") , date("d")-2 ,date("Y") );

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

		foreach($values as $value){

			if(isset($temp_array[$timecheck])){
				$temp = $temp_array[$timecheck][$value];
			}else if(isset($temp_array[$timecheck2])){
				$temp = $temp_array[$timecheck2][$value];
			}else if(isset($temp_array[$timecheck3])){
				$temp = $temp_array[$timecheck3][$value];
			}else{
				$temp = 0;
			};
			
			$profit[$value] .= "[Date.UTC(".$year.", ".$month.", ".$day.", ".$hour.", ".$min."), ". $temp ." ],";
		};
	};

?>
		
<script type='text/javascript'>
$(function () {
<?php
	foreach($values as $value){
?>
	$('#container<?php echo md5($value);?>').highcharts('StockChart', {
			chart: {
				   zoomType: 'x'
			},

		    rangeSelector: {
		        selected: 0,
				buttons: [{
					type: 'day',
					count: 1,
					text: '1d'
				}, {
					type: 'day',
					count: 2,
					text: '2d'
				}
				]
		    },
	    title:{
		text:''
	    },
            xAxis: {
                type: 'datetime',
                title: {
                    text: null
                }
            },
            yAxis: [{
			title: {
				text: ''
			},
			opposite: true
		},{
			title: {
				text: '<?php echo $value;?>'
			},
			min: 0,
			labels: {
				format: '{value}',
				style: {
					color: '#4572A7'
				}
			}
            }],
            tooltip: {
                shared: true
            },
            legend: {
		align: 'center',
		verticalAlign: 'top',
		floating: true,
		x: 0,
		y: 10
	    },
           
            series: [{
                type: 'line',
				yAxis: 1,
                name: '<?php echo $value;?>',
				marker: {
                    enabled: false
                },
                data: [
                    <?php echo $profit[$value];?>
                ],
		        tooltip: {
		        	valueDecimals: 2
		        }
            }
	    ]
        });
<?php
	};
?>
		
});

</script>



