<?php
require '../includes/check_login.php';
//require_once '../includes/dbConnect.php';
if(isset($_GET['id'])){
	$workOrderId = $_GET['id'];
	if(strlen($workOrderId)< 6){
		$k = strlen($workOrderId);
		$workOrderNum = "";
		for($j=0; $j < 6 - $k; $j++){
			$workOrderNum = $workOrderNum."0";
		}
		$workOrderNum = $workOrderNum.$workOrderId;
	}else{
		$workOrderNum = $workOrderId;
	}
	$query = $db->prepare("SELECT * FROM workorder WHERE id = ?");
	$query->bind_param("i", $workOrderId);
	$query->execute();
	$result = $query->get_result();
	$row = $result->fetch_assoc();
	$workRequestId = $row['workRequestId'];
	$startDate = $row['startDate'];
	$dueDate = date("N/j/y"  ,$row['dueDate']);
	$dueDateLong = date("M d, Y"  ,$row['dueDate']);
	$endDate = $row['endDate'];
	$timeEstimate = secondsToTime($row['timeEstimate']);
	list($days, $hrs, $mins) = explode(",", $timeEstimate);
	$notes = $row['notes'];
	$status = $row['status'];
	$query = $db->prepare("SELECT * FROM workrequest WHERE id = ?");
	$query->bind_param("i", $workRequestId);
	$query->execute();
	$result = $query->get_result();
	$row = $result->fetch_assoc();
	$workTypeId = $row['workTypeId'];
	$itemId = $row['itemId'];
	$priority = $row['priority'];
	$description = $row['description'];
	$requestDate = date("N/j/y"  ,$row['timestamp']);
	$requestedBy = $row['requestedBy'];
	$other = $row['other'];
	$approvedBy = $row['approvedBy'];
	$addRow = "";
	$item_list = "";
	$workItem = "Item:";
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
		$workType = "Machine";
		$workItem = "Work Center:";
		$query = $db->prepare("SELECT * FROM workcenter ORDER BY center ASC");
		$query->execute();
		$result = $query->get_result();
		while (($row = $result->fetch_object()) !== NULL) {
			if($row->id == $itemId){
				$workCenter = $row->center;
				$addRow = "<div class=\"col-md-3\"><label>Machine:</label></div><div class=\"col-md-3\"><small>". $row->name ."</small></div><div class=\"col-md-3\"><label>Serial #:</label></div><div class=\"col-md-3\"><small>". $row->serial ."</small></div>";
				$item_list = $item_list."<option value=\"".$row->id."\" selected>Center ".$row->center."&nbsp;&nbsp;".$row->name."</option>";
			}else{
				$item_list = $item_list."<option value=\"".$row->id."\">Center".$row->center."&nbsp;&nbsp;".$row->name."</option>";
			}	
		}
	}elseif($workTypeId == 2){
		$workType = "Facility";
		$workItem = "Item:";
		$query = $db->prepare("SELECT * FROM facilitytype ORDER BY id ASC");
		$query->execute();
		$result = $query->get_result();
		while (($row = $result->fetch_object()) !== NULL) {
			if($row->id == $itemId){
				$workCenter = $row->item;
				$item_list = $item_list."<option value=\"".$row->id."\" selected>".$row->item."</option>";
			}else{
				$item_list = $item_list."<option value=\"".$row->id."\">".$row->item."</option>";
			}	
		}
	}elseif($workTypeId == 3){
		$workType = "Safety";
		$query = $db->prepare("SELECT * FROM safetytype ORDER BY id ASC");
		$query->execute();
		$result = $query->get_result();
		while (($row = $result->fetch_object()) !== NULL) {
			if($row->id == $itemId){
				$workCenter = $row->item;
				$item_list = $item_list."<option value=\"".$row->id."\" selected>".$row->item."</option>";
			}else{
				$item_list = $item_list."<option value=\"".$row->id."\">".$row->item."</option>";
			}	
		}
	}elseif($workTypeId == 4){
		$workType = "Tools";
		$item_list = "";
		$query = $db->prepare("SELECT * FROM toolstype ORDER BY id ASC");
		$query->execute();
		$result = $query->get_result();
		while (($row = $result->fetch_object()) !== NULL) {
			if($row->id == $itemId){
				$workCenter = $row->item;
				$item_list = $item_list."<option value=\"".$row->id."\" selected>".$row->item."</option>";
			}else{
				$item_list = $item_list."<option value=\"".$row->id."\">".$row->item."</option>";
			}	
		}
	}elseif($workTypeId == 5){
		$workType = "Other";
		$workCenter = $other;
	}else{
		
	}
	// Requested By
	$query = $db->prepare("SELECT firstname, lastname FROM users WHERE id = ?");
	$query->bind_param("i", $requestedBy);
	$query->execute();
	$result = $query->get_result();
	$row = $result->fetch_assoc();
	$requesterName = $row['firstname']." ".$row['lastname'];
	// Approved By
	$query = $db->prepare("SELECT firstname, lastname FROM users WHERE id = ?");
	$query->bind_param("i", $approvedBy);
	$query->execute();
	$result = $query->get_result();
	$row = $result->fetch_assoc();
	$approverName = $row['firstname']." ".$row['lastname'];
	//Selected Priority
	$priorityLevel = "";
	For($i=1; $i<4; $i++){
		switch ($i){
			case 1: $x = "Low"; break;
			case 2: $x = "Medium"; break;
			case 3: $x = "High"; break;
			default: $x = "--Choose Priority--";
		}
		if($priority == $i){
			$priorityLabel = $x;
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
	// Assigned Technicians
	$query = $db->prepare("SELECT * FROM workdata WHERE workOrderId = ?");
	$query->bind_param("i", $workOrderId);
	$query->execute();
	$result = $query->get_result();
	$assignedJob = "";
	$assignmentTab = "";
	$assignedTechs = array();
	while (($row = $result->fetch_object()) !== NULL) {
		$query1 = $db->prepare("SELECT firstname, lastname FROM users WHERE id = ?");
		$query1->bind_param("i", $row->assignedTo);
		$query1->execute();
		$result1 = $query1->get_result();
		$row1 = $result1->fetch_assoc();
		$assignedJob = $assignedJob."<div class=\"row\"><div class=\"col-md-5\">".$row1['firstname']." ".$row1['lastname']."</div></div>";
		$i = 1;
		if($_SESSION['user_id'] == $row->assignedTo){
			$assignmentTab = $assignmentTab. "<li class=\"active\"><a href=\"#1\" data-toggle=\"tab\" id=\"worker-".$row->assignedTo."\">".$row1['firstname']." ".$row1['lastname']."</a></li>";
		}else{
			$assignmentTab = $assignmentTab."<li><a href=\"#1\" data-toggle=\"tab\" id=\"worker-".$row->assignedTo."\">".$row1['firstname']." ".$row1['lastname']."</a></li>";
		}
		$assignedTechs[] = $row->assignedTo;
	}
	
	// checked Technicians
	$query = $db->prepare("SELECT * FROM users WHERE department = 600 ORDER BY firstname ASC");
	$query->execute();
	$result = $query->get_result();
	$checkbox_list = "";
	$availableTechs = array();
	while (($row = $result->fetch_object()) !== NULL) {
		if(in_array($row->id, $assignedTechs)){
			$checkbox_list = $checkbox_list."<div class=\"checkbox\"><label><input type=\"checkbox\" name=\"check_list[]\" value=\"".$row->id."\" checked>".$row->firstname." ".$row->lastname."</label></div>";
		}else{
			$checkbox_list = $checkbox_list."<div class=\"checkbox\"><label><input type=\"checkbox\" name=\"check_list[]\" value=\"".$row->id."\">".$row->firstname." ".$row->lastname."</label></div>";
		}
		$availableTechs[] = $row->id;
	}
	// checked other
	$checkedOther = 0;
	$otherCheckbox = "<div class=\"checkbox\"><label><input id=\"checkboxOther\" type=\"checkbox\" name=\"checkboxOther\">Other</label></div>";
	$assignUser = "";
	$query = $db->prepare("SELECT id, firstname, lastname, permissions FROM users WHERE department != 600 ORDER BY firstname ASC");
	$query->execute();
	$result = $query->get_result();
	while (($row = $result->fetch_object()) !== NULL) {
		$permission = explode(",", $row->permissions);
		if($permission[1] == 1){
			if(in_array($row->id, $assignedTechs)){
				$assignUser = $assignUser."<option value=\"".$row->id."\" selected>".$row->firstname." ".$row->lastname."</option>";
				$otherCheckbox = "<div class=\"checkbox\"><label><input id=\"checkboxOther\" type=\"checkbox\" name=\"checkboxOther\" checked>Other</label></div>";
				$checkedOther = 1;
			}else{
				$assignUser = $assignUser."<option value=\"".$row->id."\">".$row->firstname." ".$row->lastname."</option>";
			}
		}
	}
		

}

function secondsToTime($seconds) {
    $dtF = new DateTime("@0");
    $dtT = new DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%a,%h,%i');
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
	<link href="../css/bootstrap-horizon.css" rel="stylesheet">
	<!-- Custom styles for this template -->
	<link href="../css/jquery-ui.css" rel="stylesheet">
	<link href="../css/main.css" rel="stylesheet">
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
	<li><a href="workorders.php">Work Orders</a></li>
	<li class="active">Work Order</li>
</ol>
<div class="container-fluid">
	<div class="panel panel-primary">
		<div class="panel-heading">Work Order</div>
			<div class="panel-body">
				<div class="row col-md-12">
					<div class="col-md-1 pull-right"><b><p class="text-danger"><?php echo $workOrderNum; ?></p></b></div>
					<div class="col-md-2 pull-right"><label class="control-label">Work Order Number</label></div>
				</div>
				<div class="row col-md-12">
					<div class="col-md-4 form-group">
						<div class="panel panel-default">
							<div class="panel-body">
								<div class="row">
									<div class="pull-right RbtnMargin">
										<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#requestModal" aria-label="Edit">
											<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
										</button>
									</div>
								</div>
								<div class="row">
									<div class="col-md-3"><label>Type:</label></div>
									<div class="col-md-7"><small><?php echo $workType; ?></small></div>
								</div>
								<div class="row">
									<div class="col-md-3"><label><?php echo $workItem; ?></label></div>
									<div class="col-md-2"><small><?php echo $workCenter; ?></small></div>
								</div>
								<div class="row">
									<?php echo $addRow; ?>
								</div>
								<div class="row">
									<div class="col-md-3"><label>Priority:</label></div>
									<div class="col-md-2"><small><?php echo $priorityLabel; ?></small></div>
								</div>
								<div class="row">
									<div class="col-md-3"><label>Problem:</label></div>
									<div class="col-md-9"><textarea class="form-control" name="textDescription" id="textDescription" rows="2" disabled><?php echo $description; ?></textarea></div>
								</div>
								<div class="row">
									<div class="col-md-3"><label>Request By:</label></div>
									<div class="col-md-3"><small><?php echo $requesterName; ?></small></div>
									<div class="col-md-3"><label>Date:</label></div>
									<div class="col-md-3"><small><?php echo $requestDate ?></small></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-5 form-group">
						<div class="panel panel-default">
							<div class="panel-body">
								<div class="row">
									<div class="pull-right RbtnMargin">
										<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#approveModal" aria-label="Edit">
											<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
										</button>
									</div>
								</div>
								<div class="row">
									<div class="col-md-3"><label>Due Date:</label></div>
									<div class="col-md-3"><small><?php echo $dueDate; ?></small></div>
									<div class="col-md-3"><label>Esitmated Time:</label></div>
									<div class="col-md-3"><small><?php echo $days." Days ".$hrs. " Hrs ".$mins." Mins"; ?></small></div>
								</div>
								<div class="row">
									<div class="col-md-3"><label>Assigned to:</label></div>
									<div class="col-md-9">
										<small>
											<?php echo $assignedJob; ?>
										</small>
									</div>
								</div>
								<div class="row">
									<div class="col-md-3"><label>Notes:</label></div>
									<div class="col-md-9"><textarea class="form-control" name="textDescription" id="textDescription" rows="2" disabled><?php echo $notes; ?></textarea></div>
								</div>
								<div class="row">
									<div class="col-md-3"><label>Approved By:</label></div>
									<div class="col-md-3"><small><?php echo $approverName; ?></small></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-3 form-group">
						<div class="panel panel-default">
							<div class="panel-body">
								<div class="row">
									<div class="pull-right RbtnMargin">
										<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#dataModal" aria-label="Edit">
											<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
										</button>
									</div>
								</div>
								<div class="row">
									<div class="col-md-7"><label>Completed:</label></div>
									<div class="col-md-3"><small>No</small></div>
								</div>
								<div class="row">
									<div class="col-md-7"><label>Total Hours:</label></div>
									<div class="col-md-5"><small>30.5</small></div>
								</div>
								<div class="row">
									<div class="col-md-7"><label>Parts Required:</label></div>
									<div class="col-md-5"><small>Yes</small></div>
								</div>
								<div class="row">
									<div class="col-md-7"><label>Parts Cost:</label></div>
									<div class="col-md-5"><small>$12,762.00</small></div>
								</div>
							</div>
						</div>								
					</div>
				</div>
				<div class="row col-md-12">
					<div id="exTab2">	
						<ul class="nav nav-tabs" id="assignment">
							<?php echo $assignmentTab; ?>
						</ul>
						<div class="tab-content ">
							<div class="tab-pane active" id="1">
								<div class="row col-md-6 spacer">
									<div class="well well-sm">
									<div class="row">
										<div class="col-md-3"><label>Work Done:</label></div>
										<div class="col-md-8"><textarea class="form-control" name="textDescription" id="textDescription" rows="8"></textarea></div>
										<div class="col-md-1">
											<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#descriptionModal" aria-label="Edit">
												<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
											</button>
										</div>
									</div>
									</div>
								</div>
								<div class="row col-md-6 spacer LbtnMargin" >
									<div class="well well-sm">
									
										<div class="row">
											<div class="col-md-1 col-md-offset-11">
												<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#hoursModal" aria-label="Edit">
													<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
												</button>
											</div>
										</div>
										<div class="row row-horizon LbtnMargin RbtnMargin">
											<div class="col-md-3">
												<div class="row"><b>Date</b></div>
												<div class="row"><b>Start Time</b></div>
												<div class="row"><b>Stop Time</b></div>
												<div class="row"><b>Total Time</b></div>
											</div>
											<div class="col-md-2">
												<div class="row">5/21/15</div>
												<div class="row" id="startTime">&nbsp;</div>
												<div class="row" id="stopTime">&nbsp;</div>
												<div class="row" id="runner"></div>
											</div>
											<div class="col-md-2">
												<div class="row">5/15/15</div>
												<div class="row">10:45</div>
												<div class="row">11:50</div>
												<div class="row">1:05</div>
											</div>
										</div>
									</div>
									<div class="col-md-2" id="do_action">
										<button id="startTimer" type="button" class="btn btn-success btn-xs">Start</button>
									</div>
								</div>
							</div>
							<div class="tab-pane" id="2">
								<h3>Notice the gap between the content and tab after applying a background color</h3>
							</div>
							<div class="tab-pane" id="3">
								<h3>add clearfix to tab-content (see the css)</h3>
							</div>
						</div>
					</div>
				</div>
				<!-- Request Modal -->
				<div class="modal fade" id="requestModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title" id="requestModalLabel">Edit Request Information</h4>
							</div>
							<div class="modal-body form-group">
								<div class="row form-group">
									<div class="col-md-3"><label for="selectRequestType" class="control-label">Request Type</label></div>
									<div class="col-md-4"><select id="selectRequestType" name="selectRequestType" class="form-control" required><option value="0">-- Choose Type --</option><?php echo $type_list; ?></select></div>
								</div>
								<div class="row form-group" id="machines">
									<div class="col-md-3"><label for="selectMachine" class="control-label" id="itemType"><?php echo $workType; ?></label></div>
									<div class="col-md-5" id="dropdown">
										<select id="selectItem" name="selectItem" class="form-control" required>
											<?php echo $item_list; ?>
										</Select>
									</div>
									<div class="col-md-4 hidden" id="textOther">
										<input type="text" class="form-control" id="inputOther" name="inputOther" value="<?php echo $other; ?>">
									</div>
								</div>
								<div class="row form-group" id="priority">
									<div class="col-md-3"><label for="selectPriority" class="control-label">Priority</label></div>
									<div class="col-md-4"><select id="selectPriority" name="selectPriority" class="form-control">
										<?php
											echo $priorityLevel;
										?>
									</Select></div>
								</div>
								<div class="row form-group" id="description">
									<div class="col-md-3"><label for="textDescription" class="control-label">Description</label></div>
									<div class="col-md-9"><textarea class="form-control" name="textDescription" id="textDescription" rows="2" required><?php echo $description; ?></textarea></div>
								</div>
								<div class="row form-group" id="requestBy">
									<div class="col-md-3"><label for="selectRequestBy" class="control-label">Request By</label></div>
									<div class="col-md-4">
										<select id="selectRequestBy" name="selectRequestBy" class="form-control" required>
											<?php
												echo $selectUser;
											?>
										</Select>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								<button type="button" class="btn btn-primary">Save changes</button>
							</div>
						</div>
					</div>
				</div>
				<!-- Approve Modal -->
				<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title" id="requestModalLabel">Edit Approval Information</h4>
							</div>
							<div class="modal-body">
								<div class="row form-group" id="completeBy">
									<div class="col-md-3"><label for="inputcompleteBy">Complete By</label></div>
									<div class="col-md-4"><input class="form-control" type="text" id="from" name="from" value="<?php echo $dueDateLong; ?>"></div>
								</div>
								<div class="row form-group" id="estimatedTime">
									<div class="col-md-3"><label for="inputEstimatedTime">Estimated time</label></div>
									<div class="col-xs-2" id="inputDays"><input class="form-control" type="text" id="inputEstimatedTimeDays" name="inputEstimatedTimeDays" value="<?php echo $days; ?>">Days</div>
									<div class="col-xs-2" id="inputHours"><input class="form-control" type="text" id="inputEstimatedTimeHrs" name="inputEstimatedTimeHrs" value="<?php echo $hrs; ?>">Hrs</div>
									<div class="col-xs-2" id="inputMinutes"><input class="form-control" type="text" min="0" id="inputEstimatedTimeMin" name="inputEstimatedTimeMin" value="<?php echo $mins; ?>">Mins</div>
								</div>
								<div class="row form-group" id="assignTo">
									<div class="col-md-3"><label for="Checkbox">Assigned to</label></div>
									<div class="col-md-4">
										<?php echo $checkbox_list; echo $otherCheckbox;?>
										
									</div>
									<div class="col-md-4" id="assignOther">
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
									<div class="col-md-9"><textarea class="form-control" name="textNotes" id="textNotes" rows="3"><?php echo $notes; ?></textarea></div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								<button type="button" class="btn btn-primary">Save changes</button>
							</div>
						</div>
					</div>
				</div>
				<!-- Approve Modal -->
				<div class="modal fade" id="dataModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title" id="requestModalLabel">Edit Data Information</h4>
							</div>
							<div class="modal-body">
								...
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								<button type="button" class="btn btn-primary">Save changes</button>
							</div>
						</div>
					</div>
				</div>
				<!-- Approve Modal -->
				<div class="modal fade" id="descriptionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title" id="requestModalLabel">Edit Work Summary</h4>
							</div>
							<div class="modal-body">
								...
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								<button type="button" class="btn btn-primary">Save changes</button>
							</div>
						</div>
					</div>
				</div>
				<!-- Approve Modal -->
				<div class="modal fade" id="hoursModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title" id="requestModalLabel">Edit Work Hours</h4>
							</div>
							<div class="modal-body">
								...
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								<button type="button" class="btn btn-primary">Save changes</button>
							</div>
						</div>
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
<script src="../js/jquery.runner-min.js" type="text/javascript"></script>
<script>
	window.workOrderId = <?php echo $workOrderId; ?>;
	window.user_id = <?php echo $_SESSION['user_id']; ?>;
	$(document).ready(function(){
		var itemType = <?php echo $workTypeId; ?>;
		var selectOtherChecked = <?php echo $checkedOther; ?>;
		if(itemType == 5){
			$('#selectItem').removeAttr("required");
			$("#dropdown").addClass("hidden");
			$("#textOther").removeClass("hidden");
		}
		$("#assignOther").addClass("hidden");
		if(selectOtherChecked == 1){
			$("#assignOther").removeClass("hidden");
		}		
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
		$('#runner').runner({
			milliseconds: false
		});
		$( "#do_action" ).on( "click", "[id=startTimer]", function() {
			//var buttonId = this.id;
			$('#runner').runner('start');
			var action_button = "<button id=\"stopTimer\" type=\"button\" class=\"btn btn-danger btn-xs\">Stop</button>";
			$("#do_action").html(action_button);
			var d = new Date(); // for now
			var hours = d.getHours(); // => 9
			var mins = d.getMinutes(); // =>  30
			var currentTime = hours + ":" + mins;
			$("#startTime").html(currentTime);
			/*var request = $.getJSON("ajax/starttimes.php", {id : partId, machine : buttonId}, function(data) {
				console.log(data);
				$.each(data, function(key, value) {
					var a = format_date(value.start_time);
					$("#machine-" + buttonId + " td.study_date").html(a);
				});
			});*/
		});
		
		$( "#do_action" ).on( "click", "[id=stopTimer]", function() {
			//var buttonId = this.id;
			$("#runner").runner('stop');
			var action_button = "<button id=\"resetTimer\" type=\"button\" class=\"btn btn-warning btn-xs\">Reset</button>  <button id=\"doneTimer\"type=\"button\" class=\"btn btn-primary btn-xs\">Done</button>";
			$("#do_action").html(action_button);
			var d = new Date(); // for now
			var hours = d.getHours(); // => 9
			var mins = d.getMinutes(); // =>  30
			var currentTime = hours + ":" + mins;
			$("#stopTime").html(currentTime);
			/*
			var request = $.getJSON("ajax/updatetimes.php", {id : partId, machine : buttonId}, function(data) {
				console.log(data);
				
			});*/
		});
		$("#assignment").on("click", "[id]", function(event) {
			var workerId = this.id;
			
		});
	});
</script>
</body>
</html>
