<?php
require '../includes/check_login.php';
//require_once '../includes/dbConnect.php';

$machine_list = "<select id=\"chooseMachine\" class=\"form-control\" name=\"work_center\"><option value=\"0\">-- Choose Machine --</option>";
$query = $db->prepare("SELECT * FROM workcenter ORDER BY center ASC");
$query->execute();
$result = $query->get_result();
while (($row = $result->fetch_object()) !== NULL) {
	$workcenter_id = $row->id;
	$workcenter = $row->center;
	$name = $row->name;
	$machine_list = $machine_list."<option value=\"".$workcenter_id."\">".$workcenter."&nbsp&nbsp&nbsp&nbsp".$name."</option>";
		
}
$machine_list = $machine_list."</select>";

if (isset( $_POST[ 'submit' ] ) ) {
	$id = $_POST['id'];
	$workCenter = $_POST['center'];
	$machineName = $_POST['name'];
	$make = $_POST['make'];
	$model = $_POST['model'];
	$year = $_POST['year'];
	$serial = $_POST['serial'];
	$type = $_POST['type'];
	$active = $_POST['inservice'];
	if (!empty($id)) {
		$query = $db->prepare("UPDATE workcenter SET inservice = ?, name = ?, type = ?, center = ?, make = ?, model = ?, serial = ?, year = ? WHERE id = ?");
		$query->bind_param("isiissssi", $active, $machineName, $type, $workCenter, $make, $model, $serial, $year, $id);
		$query->execute();
	}
	//header("location:machines.php");
}
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>PIN Time Study</title>

	<!-- Bootstrap -->
	<link href="../css/bootstrap.min.css" rel="stylesheet">

	<!-- Custom styles for this template -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	<link href="../css/jquery-ui.css" rel="stylesheet">
	<link href="../css/main.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.css">
	<link href="../css/jquery-ui.theme.css" rel="stylesheet">
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
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
	<li><a href="admin.php">Administration</a></li>
	<li><a href="machineadmin.php">Machine Administration</a></li>
	<li class="active">Edit Machine</li>
</ol>
<div class="container-fluid">
	<div class="panel panel-primary">
		<div class="panel-heading">Edit Machine</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-12">
					<div class ="col-md-1 pull-right"><a href="machines.php" ><button type="button" class="btn btn-primary btn-sm">List Machines</button></a></div>
					<div class ="col-md-1 pull-right"><a href="addmachine.php" ><button type="button" class="btn btn-primary btn-sm">Add Machines</button></a></div>
				</div>
				<div class="col-md-12">
					<div class="row spacer" name="choose">
						<div class="col-md-1"><h4>Machine:</h4></div>
						<div class="col-md-2"><?php echo $machine_list; ?></div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<form class="hidden" action="" method="post" id="editMachine">
						<input type="hidden" name="id" value=""/>
						<div class="row">
							<div class="col-md-2"><h4>Work Center:</h4></div>
							<div class="col-md-2"><h4>Machine Name:</h4></div>
							<div class="col-md-2"><h4>Make:</h4></div>
							<div class="col-md-2"><h4>Model:</h4></div>
							<div class="col-md-2"><h4>Serial Number:</h4></div>
							<div class="col-md-2"><h4>Year:</h4></div>
						</div>
						<div class="row">
							<div class="col-md-2"><input type="text" class="form-control" name="center" placeholder="Required"></div>
							<div class="col-md-2"><input type="text" class="form-control" name="name" placeholder="Required"></div>
							<div class="col-md-2"><input type="text" class="form-control" name="make" placeholder="Required"></div>
							<div class="col-md-2"><input type="text" class="form-control" name="model" placeholder="Required"></div>
							<div class="col-md-2"><input type="text" class="form-control" name="serial" placeholder="Required"></div>
							<div class="col-md-2"><input type="text" class="form-control" name="year" placeholder="Required"></div>
						</div>
						<div class="row">
							<div class="col-md-2"><h4>Machine Type:</h4></div>
							<div class="col-md-2"><h4>Machine Active:</h4></div>
						</div>
						<div class="row">
							<div class="col-md-2">
								<select class="form-control" name="type">
									<option value="1">Machine Center</option>
									<option value="2">Edgebander</option>
									<option value="3">BAZ</option>
									<option value="4">Router</option>
									<option value="5">Saw</option>
									<option value="6">Conveyor</option>
									<option value="7">Doweling</option>
									<option value="8">Clamp</option>
									<option value="20">Other</option>
								</select>
							</div>
							<div class="col-md-2">
								<input type="radio" name="inservice" value="1" checked>Yes &nbsp&nbsp
								<input type="radio" name="inservice" value="0">NO
							</div>
						</div>
						<div class="row spacer">
							<div class="col-md-2">
								<input name="submit" type="submit" value="Update" />
								 <div class="help-block with-errors"></div>
							</div>	
						</div>				
					</form>
				</div>
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
<script>
	$(document).ready(function(){
		$("#chooseMachine").change(function(){
			var optionValue = $( "#chooseMachine" ).val();
			if (optionValue > 0){
				$("form").removeClass("hidden");
					
				var request = $.get("../ajax/updatemachines.php", {id : optionValue}, function(data) {
					console.log(data);
					populate($("#editMachine"), $.parseJSON(data));
				});
			} else {
				$("form").addClass("hidden");
			}
		})
	});
	
	function populate(form, data) {
		$.each(data, function(key, value) {
			var $field = $("[name=" + key + "]", form);
			switch ($field.attr("type")) {
				case "radio":
				case "checkbox":
					$field.each(function(index, element) {
						element.checked = $(element).val() == value
					});
					break;
				default:
					$field.val(value);
			}
		});
	}
</script>
</body>
</html>
