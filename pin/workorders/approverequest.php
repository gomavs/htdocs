<?php
require '../includes/check_login.php';
//require_once '../includes/dbConnect.php';

if(isset($_GET['id'])){
	$requestId = $_GET['id'];
	$query = $db->prepare("SELECT * FROM workrequest WHERE id = ?");
	$query->bind_param("i", $requestId);
	$query->execute();
	$result = $query->get_result();
	$row = $result->fetch_assoc();
	$requestedBy = $row['requestedBy'];
	$workTypeId = $row['workTypeId'];
	$itemId = $row['itemId'];
	$priority = $row['priority'];
	$description = $row['description'];
	$accepted = $row['accepted'];
	//$timestamp = $row['timestamp'];
	$requestDate = date("F j, Y"  ,$row['timestamp']);
	$requestTime = "@".$row['timestamp'];
	$requestTime = new DateTime($requestTime);
	$requestTime->setTimezone(new DateTimeZone('America/Chicago'));
	$requestTime = $requestTime->format("h:i A");
	$other = $row['other'];
	$priorityLevel = "";
	//Priority Select dropdown
	For($i=1; $i<4; $i++){
		switch ($i){
			case 1: $x = "Low"; break;
			case 2: $x = "Medium"; break;
			case 3: $x = "High"; break;
			default: $x = "--Choose Priority--";
		}
		if($priority == $i){
			$priorityLevel = $priorityLevel."<option value=\"".$i."\" selected>".$x."</option>";
		}else{
			$priorityLevel = $priorityLevel."<option value=\"".$i."\">".$x."</option>";
		}	
	}
	//User Select dropdown
	$selectUser = "";
	$query = $db->prepare("SELECT id, firstname, lastname FROM users ORDER BY firstname ASC");
	$query->execute();
	$result = $query->get_result();
	while (($row = $result->fetch_object()) !== NULL) {
		if($row->id == $requestedBy){
			$selectUser = $selectUser."<option value=\"".$row->id."\" selected>".$row->firstname." ".$row->lastname."</option>";
		}else{
			$selectUser = $selectUser."<option value=\"".$row->id."\">".$row->firstname." ".$row->lastname."</option>";
		}
	}
	//Request type Select Dropdown
	$type_list = "";
	$query = $db->prepare("SELECT * FROM worktypes ORDER BY id ASC");
	$query->execute();
	$result = $query->get_result();
	while (($row = $result->fetch_object()) !== NULL) {
		if($row->id == $workTypeId){
			$type_list = $type_list."<option value=\"".$row->id."\" selected>".$row->type."</option>";
		}else{
			$type_list = $type_list."<option value=\"".$row->id."\">".$row->type."</option>";
		}	
	}
	//Item Select Dropdown
	if($workTypeId == 1){
		$item_name = "Machine";
		$item_list = "";
		$query = $db->prepare("SELECT id, center, name FROM workcenter ORDER BY center ASC");
		$query->execute();
		$result = $query->get_result();
		while (($row = $result->fetch_object()) !== NULL) {
			if($row->id == $itemId){
				$item_list = $item_list."<option value=\"".$row->id."\" selected>Center ".$row->center."&nbsp;&nbsp;".$row->name."</option>";
			}else{
				$item_list = $item_list."<option value=\"".$row->id."\">Center".$row->center."&nbsp;&nbsp;".$row->name."</option>";
			}	
		}
	}elseif($workTypeId == 2){
		$item_name = "Facility";
		$item_list = "";
		$query = $db->prepare("SELECT * FROM facilitytype ORDER BY id ASC");
		$query->execute();
		$result = $query->get_result();
		while (($row = $result->fetch_object()) !== NULL) {
			if($row->id == $itemId){
				$item_list = $item_list."<option value=\"".$row->id."\" selected>".$row->item."</option>";
			}else{
				$item_list = $item_list."<option value=\"".$row->id."\">".$row->item."</option>";
			}	
		}
	}elseif($workTypeId == 3){
		$item_name = "Safety";
		$item_list = "";
		$query = $db->prepare("SELECT * FROM safetytype ORDER BY id ASC");
		$query->execute();
		$result = $query->get_result();
		while (($row = $result->fetch_object()) !== NULL) {
			if($row->id == $itemId){
				$item_list = $item_list."<option value=\"".$row->id."\" selected>".$row->item."</option>";
			}else{
				$item_list = $item_list."<option value=\"".$row->id."\">".$row->item."</option>";
			}	
		}
	}elseif($workTypeId == 4){
		$item_name = "Tools";
		$item_list = "";
		$query = $db->prepare("SELECT * FROM toolstype ORDER BY id ASC");
		$query->execute();
		$result = $query->get_result();
		while (($row = $result->fetch_object()) !== NULL) {
			if($row->id == $itemId){
				$item_list = $item_list."<option value=\"".$row->id."\" selected>".$row->item."</option>";
			}else{
				$item_list = $item_list."<option value=\"".$row->id."\">".$row->item."</option>";
			}	
		}
	}elseif($workTypeId == 5){
		$item_name = "Other";
		$item_list = "";
	}else{
		
	}
	$query = $db->prepare("SELECT * FROM users WHERE department = 600 ORDER BY firstname ASC");
	$query->execute();
	$result = $query->get_result();
	$checkbox_list = "";
	$availableTechs = array();
	while (($row = $result->fetch_object()) !== NULL) {
		$checkbox_list = $checkbox_list."<div class=\"checkbox\"><label><input type=\"checkbox\" name=\"check_list[]\" value=\"".$row->id."\">".$row->firstname." ".$row->lastname."</label></div>";
		$availableTechs[] = $row->id;
	}
	$assignUser = "";
	$query = $db->prepare("SELECT id, firstname, lastname, permissions FROM users WHERE department != 600 ORDER BY firstname ASC");
	$query->execute();
	$result = $query->get_result();
	while (($row = $result->fetch_object()) !== NULL) {
		$permission = explode(",", $row->permissions);
		if($permission[1] == 1){
			$assignUser = $assignUser."<option value=\"".$row->id."\">".$row->firstname." ".$row->lastname."</option>";
		}
	}
}
if (isset( $_POST[ 'submit' ] ) ) {
	$requestType = $_POST['requestType'];
	$inputOther = "";
	$workCenterId = "";
	if($requestType == 5){
		$selectItem = 0;
		$inputOther = $_POST['inputOther'];
	}else{
		$selectItem = $_POST['selectItem'];
	}
	if($requestType == 1){
		$workCenterId = $_POST['selectItem'];
	}
	$selectPriority = $_POST['selectPriority'];
	$textDescription = $_POST['textDescription'];
	$selectRequestBy = $_POST['selectRequestBy'];
	$completeBy = strtotime($_POST['from']);
	$estimatedTime = ($_POST['inputEstimatedTimeDays'] * 86400)+($_POST['inputEstimatedTimeHrs'] * 3600)+($_POST['inputEstimatedTimeMin'] * 60);
	$textNotes = $db->real_escape_string($_POST['textNotes']);
	
	$date = new DateTime();
	$timestamp = $date->getTimestamp();
	$approvedBy = $_SESSION['user_id'];
	$accepted = 1;
	$status = 0;
	//Update the work request with correct information and approve request
	$query = $db->prepare("UPDATE workrequest SET workTypeId = ?, itemId = ?, priority = ?, description = ?, requestedBy = ?, accepted = ?, other = ?, approvedBy = ? WHERE id = ? ");
	$query->bind_param("iiisiisii", $requestType, $selectItem, $selectPriority, $textDescription, $selectRequestBy, $accepted, $inputOther, $approvedBy, $requestId);
	$query->execute();
	//Start a new work order
	$query = "INSERT INTO workorder (workRequestId, workCenterId, startDate, dueDate, timeEstimate, notes, status) VALUES ('$requestId', '$workCenterId', '$timestamp', '$completeBy', '$estimatedTime', '$textNotes', '$status')";
	$db->query($query);
	$workOrderId =  $db->insert_id;
	//echo $workOrderId;
	//Assign techs to the workorder
	if(!empty($_POST['check_list'] )){
		foreach($_POST['check_list'] as $check){
			mysqli_query($db,"INSERT INTO workdata (workOrderId, assignedTo) VALUES ('$workOrderId', '$check')");
		}
	}
	if(isset($_POST['checkboxOther'])){
		$assignOther = intval($_POST['selectOther']);
		mysqli_query($db,"INSERT INTO workdata (workOrderId, assignedTo) VALUES ('$workOrderId', '$assignOther')");
	}
	header('location: workprogress.php');
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
	
	<style>
    input {
        max-width: 100%;
    } 
	</style>
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
	<li class="active">Approve Request</li>
</ol>
<div class="container-fluid">
	<div class="panel panel-primary">
		<div class="panel-heading">Approve Request</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-12">
					<div class ="col-md-1 pull-right"><a href="openrequests.php" ><button type="button" class="btn btn-primary btn-sm btn-block">Open WOs</button></a></div>
					<div class ="col-md-1 pull-right"><a href="#" ><button type="button" class="btn btn-primary btn-sm btn-block">Completed WOs</button></a></div>
				</div>
			</div>
			<form role="form" data-toggle="validator" method="post" id="requestWork" >
				<div class="row col-md-12 spacer">
					<div class="col-md-6">
						<div class="panel panel-info">
							<div class="panel-heading">Work Request Information</div>
							<div class="panel-body">
								<div class="row form-group">
									<div class="col-md-2"><label for="selectRequestType" class="control-label">Request Type</label></div>
									<div class="col-md-3">
										<select id="selectRequestType" class="form-control" name="requestType" required>
											<?php
												echo $type_list;
											?>
										</select>
									</div>
									<div class="col-md-3 col-md-offset-2"><label class="control-label pull-right ">Request Number</label></div>
									<div class="col-md-2"><label class="control-label pull-right text-danger"><?php echo $requestId; ?></label></div>
								</div>
								<div class="row form-group" id="machines">
									<div class="col-md-2"><label for="selectMachine" class="control-label" id="itemType"><?php echo $item_name; ?></label></div>
									<div class="col-md-4" id="dropdown">
										<select id="selectItem" name="selectItem" class="form-control" required>
											<?php echo $item_list; ?>
										</Select>
									</div>
									<div class="col-md-4 hidden" id="textOther">
										<input type="text" class="form-control" id="inputOther" name="inputOther" value="<?php echo $other; ?>">
									</div>
								</div>
								<div class="row form-group" id="priority">
									<div class="col-md-2"><label for="selectPriority" class="control-label">Priority</label></div>
									<div class="col-md-3"><select id="selectPriority" name="selectPriority" class="form-control">
										<?php
											echo $priorityLevel;
										?>
									</Select></div>
								</div>
								<div class="row form-group" id="description">
									<div class="col-md-2"><label for="textDescription" class="control-label">Description</label></div>
									<div class="col-md-10"><textarea class="form-control" name="textDescription" id="textDescription" rows="2" required><?php echo $description; ?></textarea></div>
								</div>
								<div class="row form-group" id="requestBy">
									<div class="col-md-2"><label for="selectRequestBy" class="control-label">Request By</label></div>
									<div class="col-md-3">
										<select id="selectRequestBy" name="selectRequestBy" class="form-control" required>
											<?php
												echo $selectUser;
											?>
										</Select>
									</div>
								</div>
								<div class="row form-group" id="requestDate">
									<div class="col-md-2"><label for="inputtRequestDate" class="control-label">Request Date</label></div>
									<div class="col-md-3"><input type="text" id="inputtRequestDate" name="inputtRequestDate" value="<?php echo $requestDate; ?>" disabled></div>
									<div class="col-md-2"><label for="inputtRequestTime" class="control-label">Request Time</label></div>
									<div class="col-md-3"><input type="text" id="inputtRequestTime" name="inputtRequestTime" value="<?php echo $requestTime; ?>" disabled></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="panel panel-warning">
						<div class="panel-heading">Assign Work</div>
							<div class="panel-body">
								<div class="row form-group" id="completeBy">
									<div class="col-md-3"><label for="inputcompleteBy">Complete By</label></div>
									<div class="col-md-3"><input class="form-control" type="text" id="from" name="from" required></div>
								</div>
								<div class="row" id="estimatedTime">
									<div class="col-md-3"><label for="inputEstimatedTime">Estimated time</label></div>
									<div class="form-group col-xs-2" id="inputDays"><input class="form-control" type="text" id="inputEstimatedTimeDays" name="inputEstimatedTimeDays">Days</div>
									<div class="form-group col-xs-2" id="inputHours"><input class="form-control" type="text" id="inputEstimatedTimeHrs" name="inputEstimatedTimeHrs">Hrs</div>
									<div class="form-group col-xs-2" id="inputMinutes"><input class="form-control" type="text" min="0" id="inputEstimatedTimeMin" name="inputEstimatedTimeMin">Mins</div>
								</div>
								<div class="row form-group" id="assignTo">
									<div class="col-md-3"><label for="Checkbox">Assigned to</label></div>
									<div class="col-md-3">
										<?php echo $checkbox_list; ?>
										<div class="checkbox"><label><input id="checkboxOther" type="checkbox" name="checkboxOther">Other</label></div>
									</div>
									<div class="col-md-3" id="assignOther">
										<select id="selectOther" name="selectOther" class="form-control ">
											<option value="0">-- Choose User --</option>
											<?php
												echo $assignUser;
											?>
										</Select>
									</div>
								</div>
								<div class="row form-group" id="notes">
									<div class="col-md-3"><label for="textNotes">Notes</label></div>
									<div class="col-md-9"><textarea class="form-control" name="textNotes" id="textNotes" rows="3"></textarea></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="row col-md-12 spacer">
					<div class="col-md-2">
						<input name="submit" type="submit" class="btn btn-primary" value="Approve Request" />
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
<script src="../js/validator.js"></script>
<script src="../js/jquery-ui.js"></script>
<script src="../js/jquery.popup.min.js"></script>
<script>
	$(document).ready(function(){
		var itemType = <?php echo $workTypeId; ?>;
		if(itemType == 5){
			$('#selectItem').removeAttr("required");
			$("#dropdown").addClass("hidden");
			$("#textOther").removeClass("hidden");
		}
		$("#assignOther").addClass("hidden");
		$("#checkboxOther").change(function(){
			if($('#checkboxOther').is(":checked"))
				$("#assignOther").removeClass("hidden");
			else
				$("#assignOther").addClass("hidden");
		});
		$('.pull-down').each(function() {
			$(this).css('margin-top', $(this).parent().height()-$(this).height())
		});
		$("#selectRequestType").change(function(){
			$('#selectItem').empty();
			$("#dropdown").removeClass("hidden");
			$("#textOther").addClass("hidden");
			$('#selectItem').addAttr("required");
			var optionValue = $( "#selectRequestType" ).val();
			if (optionValue > 0){
				if(optionValue == 1){
					$('#itemType').text("Machine");
					var request = $.get("../ajax/selectrequesttype.php", {id : optionValue}, function(data) {
						console.log(data);
						data = JSON.parse(data);
						$.each(data, function(i,item) {
							$('#selectItem').append( '<option value="'
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
					$('#itemType').text("Facility");
					var request = $.get("../ajax/selectfacility.php", {id : optionValue}, function(data) {
						console.log(data);
						data = JSON.parse(data);
						$.each(data, function(i,item) {
							$('#selectItem').append( '<option value="'
								 + item.id
								 + '">'
								 + item.items
								 + '</option>' ); 
						});
					});
				};
				if(optionValue == 3){
					$('#itemType').text("Safety");
					var request = $.get("../ajax/selectsafety.php", {id : optionValue}, function(data) {
						console.log(data);
						data = JSON.parse(data);
						$.each(data, function(i,item) {
							$('#selectItem').append( '<option value="'
								 + item.id
								 + '">'
								 + item.items
								 + '</option>' ); 
						});
					});
				};
				if(optionValue == 4){
					$('#itemType').text("Tool");
					var request = $.get("../ajax/selecttool.php", {id : optionValue}, function(data) {
						console.log(data);
						data = JSON.parse(data);
						$.each(data, function(i,item) {
							$('#selectItem').append( '<option value="'
								 + item.id
								 + '">'
								 + item.items
								 + '</option>' ); 
						});
					});
				};
				if(optionValue == 5){
					$('#itemType').text("Other");
					$("#dropdown").addClass("hidden");
					$("#textOther").removeClass("hidden");
					$('#selectItem').removeAttr("required");
				};
			}
		});
		$(function() {
			$( "#from" ).datepicker({
				defaultDate: "+1w",
				dateFormat:"M dd, yy",
				changeMonth: true,
				numberOfMonths: 3,
				onClose: function( selectedDate ) {
					$( "#to" ).datepicker( "option", "minDate", selectedDate );
				}
			});
		});
		$("#inputEstimatedTimeMin").keydown(function (e) {
			// Allow: backspace, delete, tab, escape, enter and .
			if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
				 // Allow: Ctrl+A, Command+A
				(e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) || 
				 // Allow: home, end, left, right, down, up
				(e.keyCode >= 35 && e.keyCode <= 40)) {
					 // let it happen, don't do anything
					 return;
			}
			// Ensure that it is a number and stop the keypress
			if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
				e.preventDefault();
			}
		});
		$("#inputEstimatedTimeMin").keyup(function (e) {
			var minuteValue = ($(this).val());
			if (minuteValue < 0 || minuteValue > 59){
				$("#inputMinutes").addClass("has-error");
			} else{
				$("#inputMinutes").removeClass("has-error");
			}
		});
		$("#inputEstimatedTimeDays").keydown(function (e) {
			// Allow: backspace, delete, tab, escape, enter and .
			if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
				 // Allow: Ctrl+A, Command+A
				(e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) || 
				 // Allow: home, end, left, right, down, up
				(e.keyCode >= 35 && e.keyCode <= 40)) {
					 // let it happen, don't do anything
					 return;
			}
			// Ensure that it is a number and stop the keypress
			if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
				e.preventDefault();
			}
		});
		$("#inputEstimatedTimeDays").keyup(function (e) {
			var minuteValue = ($(this).val());
			if (minuteValue < 0 || minuteValue > 99){
				$("#inputDays").addClass("has-error");
			} else{
				$("#inputDays").removeClass("has-error");
			}
		});
		$("#inputEstimatedTimeHrs").keydown(function (e) {
			// Allow: backspace, delete, tab, escape, enter and .
			if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
				 // Allow: Ctrl+A, Command+A
				(e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) || 
				 // Allow: home, end, left, right, down, up
				(e.keyCode >= 35 && e.keyCode <= 40)) {
					 // let it happen, don't do anything
					 return;
			}
			// Ensure that it is a number and stop the keypress
			if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
				e.preventDefault();
			}
		});
		$("#inputEstimatedTimeHrs").keyup(function (e) {
			var minuteValue = ($(this).val());
			if (minuteValue < 0 || minuteValue > 23){
				$("#inputHours").addClass("has-error");
			} else{
				$("#inputHours").removeClass("has-error");
			}
		});
		
	});
	
</script>
</body>
</html>
