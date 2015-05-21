<?php
require '../includes/check_login.php';

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
	<li class="active">Reports</li>
</ol>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 main">
			<h2 class="page-header">Total Completed Time Studies</h2>
		</div>
		<div class="col-md-12 main">
			<div class="row">
				<div class="col-md-5">
					<label for="from">From</label>
					<input type="text" id="from" name="from">
					<label for="to">to</label>
					<input type="text" id="to" name="to">
				</div>
				<div class="col-md-3 do_action">
					<button id="plotgraph" type="button" class="btn btn-primary btn-xs">Plot Graph</button>
				</div>
			</div>
			<div id="myfirstchart" style="height: 250px;"></div>
		</div>
	</div>
</div>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/jquery-ui.js"></script>
<!--<script src="../assets/js/docs.min.js"></script>-->

<script>
	var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
	var myDate = new Date();
	todaysDate = ( months[(myDate.getMonth())] + '-' + (myDate.getDate()) + '-' + (myDate.getFullYear()));
	var oneWeekAgo = new Date();
	oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
	var start_date = ( months[(oneWeekAgo.getMonth())] + '-' + (oneWeekAgo.getDate()) + '-' + (oneWeekAgo.getFullYear()));
	graph_plotter(start_date, todaysDate);
	$(function() {
		$( "#from" ).datepicker({
			defaultDate: "+1w",
			dateFormat:"dd M yy",
			changeMonth: true,
			numberOfMonths: 3,
			onClose: function( selectedDate ) {
				$( "#to" ).datepicker( "option", "minDate", selectedDate );
			}
		});
		$( "#to" ).datepicker({
			defaultDate: "+1w",
			dateFormat:"dd M yy",
			changeMonth: true,
			numberOfMonths: 3,
			onClose: function( selectedDate ) {
				$( "#from" ).datepicker( "option", "maxDate", selectedDate );
			}
		});
	});

	$( ".do_action" ).on( "click", "[id = plotgraph]", function() {
		var start_date = $( "#from" ).val();
		var end_date = end_date = $( "#to" ).val();
		start_date=start_date.split(" ");
		var newStartDate=start_date[1]+"-"+start_date[0]+"-"+start_date[2];
		end_date=end_date.split(" ");
		var newEndDate=end_date[1]+"-"+end_date[0]+"-"+end_date[2];
		graph_plotter(newStartDate, newEndDate);
	});

	function graph_plotter(newStartDate, newEndDate){
		var request = $.getJSON("../ajax/graph1.php", {starttime : newStartDate, endtime :  newEndDate}, function(dates) {
			$("#myfirstchart").html(" ");
			new Morris.Line({
				// ID of the element in which to draw the chart.
				element: 'myfirstchart',
				// Chart data records -- each entry in this array corresponds to a point on
				// the chart.
				data: dates,
				// The name of the data record attribute that contains x-values.
				xkey: 'day',
				// A list of names of data record attributes that contain y-values.
				ykeys: ['value'],
				// Labels for the ykeys -- will be displayed when you hover over the
				// chart.
				labels: ['value'],
				xLabels:"day",
				resize: "true"
			});
		});
	}
	
</script>
</body>
</html>
