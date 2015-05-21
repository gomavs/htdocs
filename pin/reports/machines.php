<?php
require '../includes/check_login.php';

$machine_array = [];

$machine_list = "<option value=\"0\">Select a Machine</option>";
$result = mysqli_query($db,"SELECT * FROM workcenter WHERE type = 1 OR type = 3 AND inservice = 1 ORDER BY center ASC");
while($row = mysqli_fetch_array($result)) {
	$mid =  $row['id'];
	$wc = $row['center'];
	$mName = $row['name'];
	$machine_list .= "<option value=\"".$mid."\">WC ".$wc."&nbsp; &nbsp;".$mName."</option>";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" href="../../favicon.ico">

	<title>Pin Time Study</title>

	<!-- Bootstrap core CSS -->
	<link href="../css/bootstrap.min.css" rel="stylesheet">

	<!-- Custom styles for this template -->
	<link href="../css/main.css" rel="stylesheet">
	<link rel="stylesheet" href="http://cdn.oesmith.co.uk/morris-0.5.1.css">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
	<script src="http://cdn.oesmith.co.uk/morris-0.5.1.min.js"></script>
	<link href="../font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link href="../css/jquery-ui.css" rel="stylesheet">
	<link href="../css/main.css" rel="stylesheet">
	<!--<link href="../css/jquery-ui.structure.css" rel="stylesheet">-->
	<link href="../css/jquery-ui.theme.css" rel="stylesheet">
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>

<body>
<?php
include '../includes/navbar.php';
?>
<ol class="breadcrumb">
	<li><a href="..">Home</a></li>
	<li><a href="index.php">Reports</a></li>
	<li class="active">Machine Reports</li>
</ol>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 main">
			<h2 class="page-header">Time Studies Completed By Machine</h2>
		</div>
		<div class="col-md-12 main">
			<form data-toggle="validator" role="form" id="graph_machine">
				<div class="row">
					<div class="form-group col-md-2 do_action">
						<select class="form-control" id="machine-1" required>
							<?php
								echo $machine_list;
							?>
						</select>
					</div>
					<div class="form-group col-md-2 do_action">
						<select class="form-control" id="machine-2">
							<?php
								echo $machine_list;
							?>
						</select>
					</div>
					<div class="form-group col-md-2 do_action">
						<select class="form-control" id="machine-3">
							<?php
								echo $machine_list;
							?>>
						</select>
					</div>
				</div>
				<div class="row">
					<div class="col-md-5">
						<label for="from">From</label>
						<input type="text" id="from" name="from" required>
						<label for="to">to</label>
						<input type="text" id="to" name="to" required>
					</div>
					<div class="row do_action">
						<button id="plotgraph" type="button" class="btn btn-primary btn-xs">Plot Graph</button>
					</div>
				</div>
			</form>
			<div class="row">
				<div id="comparemachines" style="height: 250px;"></div>
			</div>
		</div>

	</div>
</div>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/jquery-ui.js"></script>
<script src="../js/validator.js"></script>
<!--<script src="../assets/js/docs.min.js"></script>-->

<script>
	
	window.selectedMachines = [];
	window.array_toggle = 0;
	$('#machine-2').prop('disabled', 'disabled');
	$('#machine-3').prop('disabled', 'disabled');
	var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
	var myDate = new Date();
	todaysDate = ( months[(myDate.getMonth())] + '-' + (myDate.getDate()) + '-' + (myDate.getFullYear()));
	var oneWeekAgo = new Date();
	oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
	var start_date = ( months[(oneWeekAgo.getMonth())] + '-' + (oneWeekAgo.getDate()) + '-' + (oneWeekAgo.getFullYear()));
	
	var request = $.getJSON("../ajax/randommachines.php", function(data) {
		var j = 0;
		$.each(data, function(key, value) {
			selectedMachines[j] = [value.id, "WC " + value.center + "&nbsp; &nbsp;" + value.name];
			j++;
		});
		var i;
		var machineList = "";
		for (i = 0; i < selectedMachines.length; ++i) {
			if(selectedMachines[i][0] != null && selectedMachines[i][0] > 0){
				machineList += selectedMachines[i][0] + "-";

			}
		}
		machineList = machineList.slice(0,-1);
		graph_plotter(start_date, todaysDate, machineList);
		array_toggle = 1;
	});

	$(function() {
		$( "#from" ).datepicker({
			defaultDate: "+1w",
			dateFormat:"dd M yy",
			changeMonth: true,
			numberOfMonths: 2,
			onClose: function( selectedDate ) {
				$( "#to" ).datepicker( "option", "minDate", selectedDate );
			}
		});
		$( "#to" ).datepicker({
			defaultDate: "+1w",
			dateFormat:"dd M yy",
			changeMonth: true,
			numberOfMonths: 2,
			onClose: function( selectedDate ) {
				$( "#from" ).datepicker( "option", "maxDate", selectedDate );
			}
		});
	});

	$( ".do_action" ).on( "change", "[id=machine-1]", function() {
		if($array_toggle = 1){
			selectedMachines[0] = [0, 0];
			selectedMachines[1] = [0, 0];
			selectedMachines[2] = [0, 0];
			$array_toggle = 0;
		}
		var valueMachine1 = $('#machine-1').val();
		var selectedMachine1 = $('#machine-1').find(":selected").text();
		if (selectedMachines[0] != null) {
			if(selectedMachines[0][0] != valueMachine1 && selectedMachines[0][0] != 0){
				$("#machine-2").append('<option value="' + selectedMachines[0][0] + '">' + selectedMachines[0][1] + '</option>');
				$("#machine-3").append('<option value="' + selectedMachines[0][0] + '">' + selectedMachines[0][1] + '</option>');
			}
		}
		if(valueMachine1 == 0){
			$("#machine-1").append('<option value="' + selectedMachines[2][0] + '">' + selectedMachines[2][1] + '</option>');
			$("#machine-2").append('<option value="' + selectedMachines[2][0] + '">' + selectedMachines[2][1] + '</option>');
			$("#machine-1").append('<option value="' + selectedMachines[1][0] + '">' + selectedMachines[1][1] + '</option>');
			$("#machine-3").append('<option value="' + selectedMachines[1][0] + '">' + selectedMachines[1][1] + '</option>');
			selectedMachines[0] = [0, 0];
			selectedMachines[1] = [0, 0];
			selectedMachines[2] = [0, 0];
			$("#machine-2").find('option').removeAttr("selected");
			$("#machine-3").find('option').removeAttr("selected");
			$('#machine-2').prop('disabled', 'disabled');
			$('#machine-3').prop('disabled', 'disabled');
		}else{
			selectedMachines[0] = [valueMachine1, selectedMachine1];
			$("#machine-2 option[value=" + valueMachine1 + "]").remove();
			$("#machine-3 option[value=" + valueMachine1 + "]").remove();
			$('#machine-2').prop('disabled', false);
		}
	});
	
	$( ".do_action" ).on( "change", "[id=machine-2]", function() {
		var valueMachine2 = $('#machine-2').val();
		var selectedMachine2 = $('#machine-2').find(":selected").text();
		if (selectedMachines[1] != null) {
			if(selectedMachines[1][0] != valueMachine2 && selectedMachines[1][0] != 0){
				$("#machine-1").append('<option value="' + selectedMachines[1][0] + '">' + selectedMachines[1][1] + '</option>');
				$("#machine-3").append('<option value="' + selectedMachines[1][0] + '">' + selectedMachines[1][1] + '</option>');
			}
		}
		if(valueMachine2 == 0){
			$("#machine-1").append('<option value="' + selectedMachines[2][0] + '">' + selectedMachines[2][1] + '</option>');
			$("#machine-2").append('<option value="' + selectedMachines[2][0] + '">' + selectedMachines[2][1] + '</option>');
			selectedMachines[1] = [0, 0];
			selectedMachines[2] = [0, 0];
			$("#machine-3").find('option').removeAttr("selected");
			$('#machine-3').prop('disabled', 'disabled');
		}else{
			selectedMachines[1] = [valueMachine2, selectedMachine2];
			$("#machine-1 option[value=" + valueMachine2 + "]").remove();
			$("#machine-3 option[value=" + valueMachine2 + "]").remove();
			$('#machine-3').prop('disabled', false);
		}
	});
	
	$( ".do_action" ).on( "change", "[id=machine-3]", function() {
		var valueMachine3 = $('#machine-3').val();
		var selectedMachine3 = $('#machine-3').find(":selected").text();
		if (selectedMachines[2] != null) {
			if(selectedMachines[2][0] != valueMachine3 && selectedMachines[2][0] != 0){
				$("#machine-1").append('<option value="' + selectedMachines[2][0] + '">' + selectedMachines[2][1] + '</option>');
				$("#machine-2").append('<option value="' + selectedMachines[2][0] + '">' + selectedMachines[2][1] + '</option>');
			}
		}	
		if(valueMachine3 == 0){
			selectedMachines[2] = [0, 0];
		}else{
			selectedMachines[2] = [valueMachine3, selectedMachine3];
			$("#machine-1 option[value=" + valueMachine3 + "]").remove();
			$("#machine-2 option[value=" + valueMachine3 + "]").remove();
		}
	});
	
	$( ".do_action" ).on( "click", "[id = plotgraph]", function() {
		var machineList = "";
		var start_date = $( "#from" ).val();
		var end_date = end_date = $( "#to" ).val();
		start_date=start_date.split(" ");
		var newStartDate=start_date[1]+"-"+start_date[0]+"-"+start_date[2];
		end_date=end_date.split(" ");
		var newEndDate=end_date[1]+"-"+end_date[0]+"-"+end_date[2];
		var i;
		for (i = 0; i < selectedMachines.length; ++i) {
			if(selectedMachines[i][0] != null && selectedMachines[i][0] > 0){
				machineList += selectedMachines[i][0] + "-";
			}
		}
		machineList = machineList.slice(0,-1);
		graph_plotter(newStartDate, newEndDate, machineList);
	});

	function graph_plotter(newStartDate, newEndDate, machineList){
		var request = $.getJSON("../ajax/graph2.php", {starttime : newStartDate, endtime :  newEndDate, machines : machineList}, function(dates) {
			$("#comparemachines").html(" ");
			var label_list = [];
			var key_list = [];
			for (i = 0; i < selectedMachines.length; ++i) {
				if(selectedMachines[i][0] != null && selectedMachines[i][0] > 0){
					label_list[i] = selectedMachines[i][1];
					switch(i){
						case 0: key_list[i] = "a"; break;
						case 1: key_list[i] = "b"; break;
						case 2: key_list[i] = "c"; break;
					}

				}
			}
			new Morris.Line({
				// ID of the element in which to draw the chart.
				element: 'comparemachines',
				// Chart data records -- each entry in this array corresponds to a point on
				// the chart.
				data: dates,
				// The name of the data record attribute that contains x-values.
				xkey: 'day',
				// A list of names of data record attributes that contain y-values.
				ykeys: key_list, //['a', 'b', 'c'],
				// Labels for the ykeys -- will be displayed when you hover over the
				// chart.
				labels: label_list,
				lineColors: ["blue", "green", "red"],
				xLabels:"day",
				resize: "true"
			});
		});
	}
	
</script>
</body>
</html>
