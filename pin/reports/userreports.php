<?php
require '../includes/check_login.php';

$machine_array = [];

$user_list = "<option value=\"0\">Select a User</option>";
$result = mysqli_query($db,"SELECT * FROM users WHERE active = 1 ORDER BY lastname ASC");
while($row = mysqli_fetch_array($result)) {
	$uid =  $row['id'];
	$firstname = $row['firstname'];
	$lastname = $row['lastname'];
	$user_list .= "<option value=\"".$uid."\">".$firstname." ".$lastname."</option>";
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
	<li class="active">User Reports</li>
</ol>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 main">
			<h2 class="page-header">Time Studies Completed By User</h2>
		</div>

		<div class="col-md-12 main">
			<form data-toggle="validator" role="form" id="graph_machine">
				<div class="row">
					<div class="form-group col-md-2 do_action">
						<select class="form-control" id="user-1" required>
							<?php
							echo $user_list;
							?>
						</select>
					</div>
					<div class="form-group col-md-2 do_action">
						<select class="form-control" id="user-2">
							<?php
							echo $user_list;
							?>
						</select>
					</div>
					<div class="form-group col-md-2 do_action">
						<select class="form-control" id="user-3">
							<?php
							echo $user_list;
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
				<div id="compareusers" style="height: 250px;"></div>
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

	window.selectedUsers = [];
	window.array_toggle = 0;
	$('#user-2').prop('disabled', 'disabled');
	$('#user-3').prop('disabled', 'disabled');
	var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
	var myDate = new Date();
	todaysDate = ( months[(myDate.getMonth())] + '-' + (myDate.getDate()) + '-' + (myDate.getFullYear()));
	var oneWeekAgo = new Date();
	oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
	var start_date = ( months[(oneWeekAgo.getMonth())] + '-' + (oneWeekAgo.getDate()) + '-' + (oneWeekAgo.getFullYear()));

	var request = $.getJSON("../ajax/randomusers.php", function(data) {
		var j = 0;
		$.each(data, function(key, value) {
			selectedUsers[j] = [value.id, value.firstname + " " + value.lastname];
			j++;
		});
		var i;
		var userList = "";
		for (i = 0; i < selectedUsers.length; ++i) {
			if(selectedUsers[i][0] != null && selectedUsers[i][0] > 0){
				userList += selectedUsers[i][0] + "-";

			}
		}
		userList = userList.slice(0,-1);
		graph_plotter(start_date, todaysDate, userList);
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

	$( ".do_action" ).on( "change", "[id=user-1]", function() {
		if($array_toggle = 1){
			selectedUsers[0] = [0, 0];
			selectedUsers[1] = [0, 0];
			selectedUsers[2] = [0, 0];
			$array_toggle = 0;
		}
		var valueUser1 = $('#user-1').val();
		var selectedUser1 = $('#user-1').find(":selected").text();
		if (selectedUsers[0] != null) {
			if(selectedUsers[0][0] != valueUser1 && selectedUsers[0][0] != 0){
				$("#user-2").append('<option value="' + selectedUsers[0][0] + '">' + selectedUsers[0][1] + '</option>');
				$("#user-3").append('<option value="' + selectedUsers[0][0] + '">' + selectedUsers[0][1] + '</option>');
			}
		}
		if(valueUser1 == 0){
			$("#user-1").append('<option value="' + selectedUsers[2][0] + '">' + selectedUsers[2][1] + '</option>');
			$("#user-2").append('<option value="' + selectedUsers[2][0] + '">' + selectedUsers[2][1] + '</option>');
			$("#user-1").append('<option value="' + selectedUsers[1][0] + '">' + selectedUsers[1][1] + '</option>');
			$("#user-3").append('<option value="' + selectedUsers[1][0] + '">' + selectedUsers[1][1] + '</option>');
			selectedUsers[0] = [0, 0];
			selectedUsers[1] = [0, 0];
			selectedUsers[2] = [0, 0];
			$("#user-2").find('option').removeAttr("selected");
			$("#user-3").find('option').removeAttr("selected");
			$('#user-2').prop('disabled', 'disabled');
			$('#user-3').prop('disabled', 'disabled');
		}else{
			selectedUsers[0] = [valueUser1, selectedUser1];
			$("#user-2 option[value=" + valueUser1 + "]").remove();
			$("#user-3 option[value=" + valueUser1 + "]").remove();
			$('#user-2').prop('disabled', false);
		}
	});

	$( ".do_action" ).on( "change", "[id=user-2]", function() {
		var valueUser2 = $('#user-2').val();
		var selectedUser2 = $('#user-2').find(":selected").text();
		if (selectedUsers[1] != null) {
			if(selectedUsers[1][0] != valueUser2 && selectedUsers[1][0] != 0){
				$("#user-1").append('<option value="' + selectedUsers[1][0] + '">' + selectedUsers[1][1] + '</option>');
				$("#user-3").append('<option value="' + selectedUsers[1][0] + '">' + selectedUsers[1][1] + '</option>');
			}
		}
		if(valueUser2 == 0){
			$("#user-1").append('<option value="' + selectedUsers[2][0] + '">' + selectedUsers[2][1] + '</option>');
			$("#user-2").append('<option value="' + selectedUsers[2][0] + '">' + selectedUsers[2][1] + '</option>');
			selectedUsers[1] = [0, 0];
			selectedUsers[2] = [0, 0];
			$("#user-3").find('option').removeAttr("selected");
			$('#user-3').prop('disabled', 'disabled');
		}else{
			selectedUsers[1] = [valueUser2, selectedUser2];
			$("#user-1 option[value=" + valueUser2 + "]").remove();
			$("#user-3 option[value=" + valueUser2 + "]").remove();
			$('#user-3').prop('disabled', false);
		}
	});

	$( ".do_action" ).on( "change", "[id=user-3]", function() {
		var valueUser3 = $('#user-3').val();
		var selectedUser3 = $('#user-3').find(":selected").text();
		if (selectedUsers[2] != null) {
			if(selectedUsers[2][0] != valueUser3 && selectedUsers[2][0] != 0){
				$("#user-1").append('<option value="' + selectedUsers[2][0] + '">' + selectedUsers[2][1] + '</option>');
				$("#user-2").append('<option value="' + selectedUsers[2][0] + '">' + selectedUsers[2][1] + '</option>');
			}
		}
		if(valueUser3 == 0){
			selectedUsers[2] = [0, 0];
		}else{
			selectedUsers[2] = [valueUser3, selectedUser3];
			$("#user-1 option[value=" + valueUser3 + "]").remove();
			$("#user-2 option[value=" + valueUser3 + "]").remove();
		}
	});

	$( ".do_action" ).on( "click", "[id = plotgraph]", function() {
		var userList = "";
		var start_date = $( "#from" ).val();
		var end_date = end_date = $( "#to" ).val();
		start_date=start_date.split(" ");
		var newStartDate=start_date[1]+"-"+start_date[0]+"-"+start_date[2];
		end_date=end_date.split(" ");
		var newEndDate=end_date[1]+"-"+end_date[0]+"-"+end_date[2];
		var i;
		for (i = 0; i < selectedUsers.length; ++i) {
			if(selectedUsers[i][0] != null && selectedUsers[i][0] > 0){
				userList += selectedUsers[i][0] + "-";
			}
		}
		userList = userList.slice(0,-1);
		graph_plotter(newStartDate, newEndDate, userList);
	});

	function graph_plotter(newStartDate, newEndDate, userList){
		var request = $.getJSON("../ajax/graph3.php", {starttime : newStartDate, endtime :  newEndDate, users : userList}, function(dates) {
			$("#compareusers").html(" ");
			var label_list = [];
			var key_list = [];
			for (i = 0; i < selectedUsers.length; ++i) {
				if(selectedUsers[i][0] != null && selectedUsers[i][0] > 0){
					label_list[i] = selectedUsers[i][1];
					switch(i){
						case 0: key_list[i] = "a"; break;
						case 1: key_list[i] = "b"; break;
						case 2: key_list[i] = "c"; break;
					}

				}
			}
			new Morris.Line({
				// ID of the element in which to draw the chart.
				element: 'compareusers',
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