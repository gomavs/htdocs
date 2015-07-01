<?php
require '../includes/check_login.php';
$type_list = "<option value=\"\" disabled selected>-- Choose Type --</option>";
$query = $db->prepare("SELECT * FROM worktypes ORDER BY id ASC");
$query->execute();
$result = $query->get_result();
while (($row = $result->fetch_object()) !== NULL) {
	$type_id = $row->id;
	$worktype = $row->type;
	$type_list = $type_list."<option value=\"".$type_id."\">".$worktype."</option>";
		
}
if (isset( $_POST[ 'submit' ] ) ) {
	$requestType = $_POST['requestType'];
	$selectPriority = $_POST['selectPriority'];
	$textDescription = $_POST['textDescription'];
	$message = "";
	switch($requestType){
		case 1:
			$itemId = $_POST['selectMachine'];
			$query1 = $db->prepare("SELECT name, center FROM workcenter WHERE id = ?");
			$query1->bind_param("i", $itemId);
			$query1->execute();
			$result1 = $query1->get_result();
			$row1 = $result1->fetch_assoc();
			$message = "A new work request has been issued for <b>Center ".$row1['center']." ".$row1['name']."</b>";
			
			break;
		case 2:
			$itemId = $_POST['selectFacility'];
			break;
		case 3:
			$itemId = $_POST['selectSafety'];
			break;
		case 4:
			$itemId = $_POST['selectTool'];
			break;
		case 5: $itemId = 0;
			break; 
		default : $itemId = 0;
	}
	if(isset($_POST['inputOther'])){
		$inputOther = $_POST['inputOther'];
		//$message = $inputOther;
	}else{
		$inputOther = "";
	}
	$date = new DateTime();
	$timestamp = $date->getTimestamp();
	$requestedBy = $_SESSION['user_id'];
	$accpeted = 0;
	$query = $db->prepare("INSERT INTO workrequest (workTypeId, itemId, priority, description, timestamp, requestedBy, accepted, other) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $query->bind_param('iiisiiis', $requestType, $itemId, $selectPriority, $textDescription, $timestamp, $requestedBy, $accpeted, $inputOther);
    $query->execute();
	$viewed = 0;
	$requestId = $db->insert_id;
	$link = "workorders/approverequest.php?id=".$requestId;
	$query = $db->prepare("SELECT id FROM users WHERE authWO >= 4 AND department = 600");
	$query->execute();
	$result = $query->get_result();
	while (($row = $result->fetch_object()) !== NULL) {
		$query1 = $db->prepare("INSERT INTO messages (msgTo, msgFrom, priority, date, viewed, message, link) VALUES (?, ?, ?, ?, ?, ?, ?)");
		$query1->bind_param('iiiiiss', $row->id, $requestedBy, $selectPriority, $timestamp, $viewed, $message, $link);
		$query1->execute();
	}
	header('location: openrequests.php');
}
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>PIN Systems</title>

	<!-- Bootstrap -->
	<link href="../css/bootstrap.min.css" rel="stylesheet">

	<!-- Custom styles for this template -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	<link href="../css/jquery-ui.css" rel="stylesheet">
	<link href="../css/main.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.css">
	<link href="../css/jquery-ui.theme.css" rel="stylesheet">
	<!--<link rel="styesheet" href="../css/folder/popup.css">-->
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
	<li><a href="workorders.php">Work Orders</a></li>
	<li class="active">Requst work</li>
</ol>
<div class="container-fluid">
	<div class="panel panel-primary">
		<div class="panel-heading">Request Work</div>
		<div class="panel-body">
			<div class="row">
			<!--<div class="col-md-12">
					<div class ="col-md-1 pull-right"><a href="openrequests.php" ><button type="button" class="btn btn-primary btn-sm btn-block">Open Requests</button></a></div>
				</div>-->
			</div>
			<form role="form" data-toggle="validator" method="post" id="requestWork" >
				<div class="row col-md-12 spacer">
					<div class="col-md-3 form-group">
						<div class="col-md-5"><label for="selectRequestType" class="control-label">Request Type</label></div>
						<div class="col-md-7"><select id="selectRequestType" class="form-control" name="requestType" required><?php echo $type_list; ?></select></div>
					</div>
					<div class="col-md-3 form-group hidden" id="machines">
						<div class="col-md-3"><label for="selectMachine" class="control-label">Machine</label></div>
						<div class="col-md-7"><select id="selectMachine" name="selectMachine" class="form-control"><option value="" disabled selected>-- Choose Type --</option></Select></div>
					</div>
					<div class="col-md-3 form-group hidden" id="facility">
						<div class="col-md-3"><label for="selectFacility" class="control-label">Facility</label></div>
						<div class="col-md-7"><select id="selectFacility" name="selectFacility" class="form-control"><option value="" disabled selected>-- Choose Type --</option></Select></div>
					</div>
					<div class="col-md-3 form-group hidden" id="safety">
						<div class="col-md-3"><label for="selectSafety" class="control-label">Safety</label></div>
						<div class="col-md-7"><select id="selectSafety" name="selectSafety" class="form-control"><option value="" disabled selected>-- Choose Type --</option></Select></div>
					</div>
					<div class="col-md-3 form-group hidden" id="tools">
						<div class="col-md-3"><label for="selectTool" class="control-label">Tool</label></div>
						<div class="col-md-7"><select id="selectTool" name="selectTool" class="form-control"><option value="" disabled selected>-- Choose Type --</option></Select></div>
					</div>
					<div class="col-md-3 form-group hidden" id="other">
						<div class="col-md-3"><label for="inputOther" class="control-label">Other</label></div>
						<div class="col-md-7"><input type="text" class="form-control" id="inputOther" name="inputOther" placeholder="Required"></div>
					</div>
					<div class="col-md-3 form-group hidden" id="priority">
						<div class="col-md-3"><label for="selectPriority" class="control-label">Priority</label></div>
						<div class="col-md-7"><select id="selectPriority" name="selectPriority" class="form-control">
							<option value="" disabled selected>--Choose Priority--</option>
							<option value="1">Low</option>
							<option value="2">Medium</option>
							<option value="3">High</option>
						</Select></div>
					</div>
					<div class="col-md-3 form-group hidden" id="description">
						<div class="col-md-4"><label for="textDescription" class="control-label">Description</label></div>
						<div class="col-md-8"><textarea class="form-control" name="textDescription" id="textDescription" rows="3" required></textarea></div>
					</div>
				</div>
				<div class="row col-md-12 spacer">
					<div class="form-group col-md-2">
						<input name="submit" type="submit" class="btn btn-primary" value="Request Work" />
						<div class="help-block with-errors"></div>
					</div>
				</div>
			</form>
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
<script src="../js/jquery.popup.min.js"></script>
<script>
	$(document).ready(function(){
		$("#selectRequestType").change(function(){
			$("#machines").addClass("hidden");
			$("#facility").addClass("hidden");
			$("#safety").addClass("hidden");
			$("#tools").addClass("hidden");
			$("#other").addClass("hidden");
			$("#priority").addClass("hidden");
			$("#description").addClass("hidden");
			$("#inputOther").val(" ");
			$("#selectPriority").val("0");
			$("#textDescription").val(" ");
			var optionValue = $( "#selectRequestType" ).val();
			if (optionValue > 0){
				if(optionValue == 1){
					$("#machines").removeClass("hidden");
					var request = $.get("../ajax/selectrequesttype.php", {id : optionValue}, function(data) {
						console.log(data);
						data = JSON.parse(data);
						$.each(data, function(i,item) {
							$('#selectMachine').append( '<option value="'
								 + item.id
								 + '">center &nbsp;'
								 + item.center
								 + '&nbsp;&nbsp;'
								 + item.name
								 + '</option>' ); 
						});
					});
				};
				if(optionValue == 2){
					$("#facility").removeClass("hidden");
					var request = $.get("../ajax/selectfacility.php", {id : optionValue}, function(data) {
						console.log(data);
						data = JSON.parse(data);
						$.each(data, function(i,item) {
							$('#selectFacility').append( '<option value="'
								 + item.id
								 + '">'
								 + item.items
								 + '</option>' ); 
						});
						
					});
				};
				if(optionValue == 3){
					$("#safety").removeClass("hidden");
					var request = $.get("../ajax/selectsafety.php", {id : optionValue}, function(data) {
						console.log(data);
						data = JSON.parse(data);
						$.each(data, function(i,item) {
							$('#selectSafety').append( '<option value="'
								 + item.id
								 + '">'
								 + item.items
								 + '</option>' ); 
						});
						
					});
				};
				if(optionValue == 4){
					$("#tools").removeClass("hidden");
					var request = $.get("../ajax/selecttool.php", {id : optionValue}, function(data) {
						console.log(data);
						data = JSON.parse(data);
						$.each(data, function(i,item) {
							$('#selectTool').append( '<option value="'
								 + item.id
								 + '">'
								 + item.items
								 + '</option>' ); 
						});
						
					});
				};
				if(optionValue == 5){
					$("#other").removeClass("hidden");
				};
			}
		});
		$("#selectMachine").change(function(){
			var optionValue2 = $( "#selectMachine").val();
			if (optionValue2 > 0){
				$("#priority").removeClass("hidden");
			}else{
				$("#priority").addClass("hidden");
				$("#description").addClass("hidden");
			}
		});
		$("#selectFacility").change(function(){
			var optionValue2 = $( "#selectFacility").val();
			if (optionValue2 > 0){
				$("#priority").removeClass("hidden");
			}else{
				$("#priority").addClass("hidden");
				$("#description").addClass("hidden");
			}
		});
		$("#selectSafety").change(function(){
			var optionValue2 = $( "#selectSafety").val();
			if (optionValue2 > 0){
				$("#priority").removeClass("hidden");
			}else{
				$("#priority").addClass("hidden");
				$("#description").addClass("hidden");
			}
		});
		$("#selectTool").change(function(){
			var optionValue2 = $( "#selectTool").val();
			if (optionValue2 > 0){
				$("#priority").removeClass("hidden");
			}else{
				$("#priority").addClass("hidden");
				$("#description").addClass("hidden");
			}
		});	
		$('#inputOther').on('input', function(){
			var optionValue2 = $( "#inputOther").val().length;
			if (optionValue2 >= 3){
				$("#priority").removeClass("hidden");
			}else{
				$("#priority").addClass("hidden");
				$("#description").addClass("hidden");
			}
		});
		
		$("#selectPriority").change(function(){
			var optionValue2 = $( "#selectPriority" ).val();
			if (optionValue2 > 0){
				$("#description").removeClass("hidden");
				
			}else{
				$("#description").addClass("hidden");
			}
		});
	});
</script>
</body>
</html>
