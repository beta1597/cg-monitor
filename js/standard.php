<?php
header("Content-type: text/javascript; charset=iso-8859-1");
$base_url = str_replace("standard.php", "", $_SERVER['PHP_SELF']);

?>

$(function() {
	$(".tooltipactive").tooltip();
	$('.selectpicker').selectpicker();
	$('.checkbox').checkbox();
				
	$("#selectall").click(function (event){
		if(this.checked){
			$(".checkbox").checkbox({checked: true});
		}else{
			$(".checkbox").checkbox({checked: false});	
		};
	});

	$("#switch").click(function (event) {
			madeswitch = 0;
			pool = $( "#poolswitch" ).val();
			if(pool != ""){
				name = "";
				$(".checkbox").each(function () {
					if(this.checked){
							action 	= "switch";
							url = "http://<?php echo $_SERVER['SERVER_NAME'] .$base_url;?>../?action=save_action&command=" + action + "&hidepage=yes&rig=" + $( this ).attr( "rig" ) + "&pool=" + pool;
							console.log(url);
							name = name + $( this ).attr( "name" ) + "<br/>";
							$( "#result" ).load( url );
							madeswitch = 1;
					};
				});
				if(madeswitch == 1){
					$("#modalerror #myModalLabel").html("OK: switch pool");
					$("#modalerror .modal-body").html("<br/>Switch has been made:<br/>" + name);
					$('#modalerror').modal('show');
				}else{
					$("#modalerror #myModalLabel").html("Error: select rig");
					$("#modalerror .modal-body").html("<br/>Please select a rig.");
					$('#modalerror').modal('show');
				};
			}else{
				$("#modalerror #myModalLabel").html("Error: select pool");
				$("#modalerror .modal-body").html("<br/>Please select a pool.");
				$('#modalerror').modal('show');						
			};
	});
	
	$('#savenewrig').click(function (event) {
		$('#modalnewrig').modal('hide');
		url = "./?action=rigs&new=" + encodeURIComponent($( "#newrigname" ).val());
		window.location = url;
		
	});
	$(".reboot").click(function(event){
		device 	= '';
		int 	= '';
		rig 	= $( this ).attr( "rig" );
		action 	= "reboot";
		event.preventDefault(); 
		$("#modalyesno #myModalLabel").html("Reboot rig");
		$("#modalyesno .modal-text").html("<br/>Are you sure you want to reboot your rig?");
		$('#modalyesno').modal('show');
	});
	$(".resetstats").click(function(event){
		device 	= '';
		int 	= '';
		rig 	= $( this ).attr( "rig" );
		action 	= "resetstats";
		event.preventDefault(); 
		$("#modalyesno #myModalLabel").html("Reset Stats rig");
		$("#modalyesno .modal-text").html("<br/>Are you sure you want to reset your rigs stats?");
		$('#modalyesno').modal('show');	
	});  

	$("#buttonyesaction").click(function (event) {
	    url = "http://<?php echo $_SERVER['SERVER_NAME'] .$base_url;?>../?action=save_action&command=" + action + "&hidepage=yes&rig="+ rig +"&gpu=" + device + "&int=" + int;
		console.log(url);
		$( "#result" ).load( url );
		$('#modalyesno').modal('hide');
    });	
	
	$("a[data-target=#modalgraph]").click(function(ev) {
		ev.preventDefault();
		var target = $(this).attr("href") + "&width=" + $(".modal-lg").width();
		$("#modalgraph").modal("show"); 
		$(".modal-loading").show();
		$("#modalgraph .modal-span").html('');
		$("#modalgraph .modal-span").load(target, function() { 
			$(".modal-loading").hide();
		});
	});
  
	var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

	$('#time_f').timepicker({showMeridian:false,minuteStep: 10});
	$('#date').datepicker({
		onRender: function(date) {
        return date.valueOf() < now.valueOf() ? 'disabled' : '';
        }
	});
});

	function setback(val){
		$(val).css("color", "#333333");
	};
    function toHex(str) {
        var hex = '';
        for(var i=0;i<str.length;i++) {
            var val = ''+str.charCodeAt(i).toString(16);
            if(val.length == 1)
                hex += '0'+val;
            else
                hex += val;
        }
        return hex;
    };
    function hexToString (hex) {
        var str = '';
        for (var i=0; i<hex.length; i+=2) {
            str += ''+String.fromCharCode(parseInt(hex.charAt(i)+hex.charAt(i+1), 16));
        }
        return str;
    };
	