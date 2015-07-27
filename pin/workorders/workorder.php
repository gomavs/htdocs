<?php
require '../includes/check_login.php';
if(isset($_GET['alert'])){
	$query = $db->prepare("UPDATE messages SET viewed = 1 WHERE id = ? ");
	$query->bind_param("i",$_GET['alert']);
	$query->execute();
}
if(isset($_GET['id'])){
	$workOrderId = $_GET['id'];
	if(isset($_POST['workDone'])){
		$userId = $_SESSION['user_id'];
		$workDone = $_POST['userWork'];
		$workOrderId = $_GET["id"];
		$query = $db->prepare("UPDATE workdata SET workDone = ? WHERE workOrderId = ? AND assignedTo = ?");
		$query->bind_param("sii", $workDone, $workOrderId, $userId);
		$query->execute();
	}

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
	if($_SESSION['user_authWO'] >= 3 || $_SESSION['user_auth_level'] == 10){
		$disable1 = "";
	}else{
		$disable1 = "disabled";
	}
	if($_SESSION['user_authWO'] >= 5 || $_SESSION['user_auth_level'] == 10){
		$disable2 = "";
	}else{
		$disable2 = "disabled";
	}
	$query = $db->prepare("SELECT * FROM workorder WHERE id = ?");
	$query->bind_param("i", $workOrderId);
	$query->execute();
	$result = $query->get_result();
	$row = $result->fetch_assoc();
	$workRequestId = $row['workRequestId'];
	$startDate = $row['startDate'];
	$dueDate = date("n/j/y", $row['dueDate']);
	$dueDateLong = date("M d, Y", $row['dueDate']);
	$endDate = $row['endDate'];
	$timeEstimate = secondsToTime($row['timeEstimate']);
	list($days, $hrs, $mins) = explode(",", $timeEstimate);
	$issue = $row['issue'];
	$notes = $row['notes'];
	$check_down = "";
	if($row['down'] == 1){
		$check_down = "checked";
	}
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
	$hidden = "hidden";
	$machine_name = "";
	$serial = "";
	$item_list = "";
	$issue_list = "";
	$workItem = "Item:";
	$workClosed = "";
	$showDown = "hidden";
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
		$showDown = "";
		$query = $db->prepare("SELECT * FROM workcenter ORDER BY center ASC");
		$query->execute();
		$result = $query->get_result();
		while (($row = $result->fetch_object()) !== NULL) {
			if($row->id == $itemId){
				$workCenter = $row->center;
				$hidden = "";
				$machine_name = $row->name;
				$serial = $row->serial;
				$item_list = $item_list."<option value=\"".$row->id."\" selected>Center ".$row->center."&nbsp;&nbsp;".$row->name."</option>";
			}else{
				$item_list = $item_list."<option value=\"".$row->id."\">Center".$row->center."&nbsp;&nbsp;".$row->name."</option>";
			}	
		}
		
	}elseif($workTypeId == 2){
		$workType = "Facility";
		$query = $db->prepare("SELECT * FROM facilitytype WHERE active = 1 ORDER BY id ASC");
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
		$query = $db->prepare("SELECT * FROM safetytype WHERE active = 1 ORDER BY id ASC");
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
		$query = $db->prepare("SELECT * FROM toolstype WHERE active = 1 ORDER BY id ASC");
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
	}
	//Find issues for selected workType
	$query = $db->prepare("SELECT problems.id as table1id, problems.problemId, problemlist.id as table2id, problemlist.problem FROM problems LEFT JOIN problemlist ON problems.problemId = problemlist.id WHERE problems.problemType = ? AND problems.itemId = ? AND problems.active = 1 ORDER BY problemlist.problem ASC");
	$query->bind_param("ii", $workTypeId, $itemId);
	$query->execute();
	$result = $query->get_result();
	while (($row = $result->fetch_object()) !== NULL) {
		if($row->table1id == $issue){
			$problem = $row->problem;
			//echo "test";
			$issue_list = $issue_list."<option value=\"".$row->table2id."\" selected>".$row->problem."</option>";
		}else{
			//echo "test2";
			$issue_list = $issue_list."<option value=\"".$row->table2id."\">".$row->problem."</option>";
		}	
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
	$userActive = 0;
	$query = $db->prepare("SELECT * FROM workdata WHERE workOrderId = ?");
	$query->bind_param("i", $workOrderId);
	$query->execute();
	$result = $query->get_result();
	while (($row = $result->fetch_object()) !== NULL) {
		if($_SESSION['user_id'] == $row->assignedTo){
			$userActive = 1;
		}
	}
	$assignedJob = "";
	$workCompleted = "Yes";
	$assignedTechs = array();
	$assignmentTab = "";
	$workDone = "";
	$workTimes = "";
	$i = 1;
	$activeTab = "active";
	$topPane = "";
	$elapsedTime = 0;
	$startRunner = [];
	$openWorkTimeId = "";
	$completePane = "";
	$query = $db->prepare("SELECT * FROM workdata WHERE workOrderId = ?");
	$query->bind_param("i", $workOrderId);
	$query->execute();
	$result = $query->get_result();
	while (($row = $result->fetch_object()) !== NULL) {
		$userId = $row->assignedTo;
		$workStatus = $row->status;
		$lastTime = "";
		$newColumn = "";
		$actionButton = "";
		$myWork = "disabled";
		$workButton = "";
		$myForm = "";
		$myFormClose = "";
		$workName = "";
		$finishButton = "";
		$hideButtons = "";
		$workDone = $row->workDone;
		$query1 = $db->prepare("SELECT firstname, lastname FROM users WHERE id = ?");
		$query1->bind_param("i", $row->assignedTo);
		$query1->execute();
		$result1 = $query1->get_result();
		$row1 = $result1->fetch_assoc();
		$assignedJob = $assignedJob."<div class=\"row\"><div class=\"col-md-5\"><small>".$row1['firstname']." ".$row1['lastname']."</small></div></div>";
		$assignedTechs[] = $row->assignedTo;
		if($row->status == 0){
			$workCompleted = "No";
		}
		if($workStatus == 1){
			$hideButtons = "hidden";
		}
		$paneActive = "";
		if($userActive == 1){
			if($_SESSION['user_id'] == $userId){
				$assignmentTab = $assignmentTab. "<li class=\"assignmentTab tab-".$userId." active\"><a href=\"#".$userId."\" data-toggle=\"tab\" id=\"worker-".$userId."\">".$row1['firstname']." ".$row1['lastname']."</a></li>";
				$startButton = "<button id=\"startTimer-".$userId."\" type=\"button\" class=\"btn btn-success btn-xs\">Start</button>";
				$finishButton = "<button id=\"workCompleted\" type=\"button\" class=\"btn btn-primary btn-sm\">Work Completed</button>";
				$paneActive = "active";
				$newColumn = "newCol";
				$actionButton = "do_action";
				$myWork = "";
				$myForm = "<form role=\"form\" method=\"post\" id=\"work\">";
				$myFormClose = "</form>";
				$workName = " name=\"userWork\"";
				$workButton = "<button type=\"submit\" name=\"workDone\" formmethod=\"post\" class=\"btn btn-primary btn-sm\">Post Work</button>";
				
			}else{
				$assignmentTab = $assignmentTab. "<li class=\"assignmentTab tab-".$userId."\"><a href=\"#".$userId."\" data-toggle=\"tab\" id=\"worker-".$userId."\">".$row1['firstname']." ".$row1['lastname']."</a></li>";
				$startButton = "<button id=\"startTimer-".$userId."\" type=\"button\" class=\"btn btn-success btn-xs\" disabled>Start</button>";
			}
		}else{
			if($i == 1){
				$assignmentTab = $assignmentTab. "<li class=\"assignmentTab tab-".$userId." active\"><a href=\"#".$userId."\" data-toggle=\"tab\" id=\"worker-".$userId."\">".$row1['firstname']." ".$row1['lastname']."</a></li>";
				$startButton = "<button id=\"startTimer-".$userId."\" type=\"button\" class=\"btn btn-success btn-xs\" disabled>Start</button>";
				$paneActive = "active";
			}else{
				$assignmentTab = $assignmentTab. "<li class=\"assignmentTab tab-".$userId."\"><a href=\"#".$userId."\" data-toggle=\"tab\" id=\"worker-".$userId."\">".$row1['firstname']." ".$row1['lastname']."</a></li>";
				$startButton = "<button id=\"startTimer-".$userId."\" type=\"button\" class=\"btn btn-success btn-xs\" disabled>Start</button>";
			}
		}
		if($status == 1){
			$workClosed = "hidden";
			$workDisabled = "disabled";
			$myWork = "disabled";
		}
		$topPane = "<div class=\"tab-pane pane-".$userId." ".$paneActive."\" id=\"".$userId."\"><div class=\"row col-md-12 spacer\">".$myForm."<div class=\"col-md-6 rWellPadding\">";
		$topPane .= "<div class=\"row\"><div class=\"well well-sm\">";
		
		//$topPane .= "<div class=\"row\"><div class=\"col-md-2\"><label>Issue:</label></div><div class=\"col-md-3\">";
		//$topPane .= "<select id=\"selectIssue\" name=\"selectIssue\" class=\"form-control\"><option value=\"0\">-- Choose Issue --</option>".$issue_list."</select></div>";
		//$topPane .="<div class=\"col-md-3 checkbox\"><label><input type=\"checkbox\" name=\"machineDown\" value=\"1\">Machine Down</label></div></div>";
		
		$topPane .= "<div class=\"row spacer\"><div class=\"col-md-2\"><label>Work Done:</label></div><div class=\"col-md-8\">";
		$topPane .= "<textarea class=\"form-control\"".$workName." id=\"workDone\" rows=\"8\" ".$myWork.">".$workDone."</textarea></div>";
		$topPane .= "<div class=\"col-md-2 ".$workClosed."\">".$workButton."</div></div></div></div></div>".$myFormClose."<div class=\"col-md-6 lWellPadding\" ><div class=\"row\">";
		$topPane .= "<div class=\"well well-sm\"><div class=\"row\"><div class=\"col-md-1 col-md-offset-11\">";
		$topPane .= "<button type=\"button\" class=\"btn btn-default btn-sm\" data-toggle=\"modal\" data-target=\"#hoursModal\" aria-label=\"Edit\">";
		$topPane .= "<span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span></button></div></div><div class=\"row row-horizon LbtnMargin RbtnMargin spacer\">";
		$topPane .= "<div class=\"col-md-3 ". $newColumn ."\"><div class=\"row\"><b>Date</b></div><div class=\"row\"><b>Start Time</b></div>";
		$topPane .= "<div class=\"row\"><b>Stop Time</b></div><div class=\"row\"><b>Total Time</b></div></div>";
		$query2 = $db->prepare("SELECT * FROM worktimes WHERE userId = ? AND workOrderId = ? ORDER BY startTime DESC");
		$query2->bind_param("ii", $userId, $workOrderId);
		$query2->execute();
		$result2 = $query2->get_result();
		while (($row2 = $result2->fetch_object()) !== NULL) {
			$lastDate = date("m/j/y", $row2->startTime);
			$lastStart = "@".$row2->startTime;
			$lastStart = new DateTime($lastStart);
			$lastStart->setTimezone(new DateTimeZone('America/Chicago'));
			$lastStart = $lastStart->format("H:i");
			$lastStop = "@".$row2->stopTime;
			$lastStop = new DateTime($lastStop);
			$lastStop->setTimezone(new DateTimeZone('America/Chicago'));
			$lastStop = $lastStop->format("H:i");
			if($row2->stopTime == 0){
				$openWorkTimeId = $row->id;
				$elapsedTime = time() - $row2->startTime;
				$startRunner[] = ["user"=>$userId, "elapsed_time"=>$elapsedTime];
				$lastTime = "<div class=\"col-md-2\"><div class=\"row\">".$lastDate."</div><div class=\"row\">".$lastStart."</div><div class=\"row\" id=\"stopTime-".$userId."\">&nbsp;</div><div class=\"row\" id=\"runner-".$userId."\">".$elapsedTime."</div></div>";
				if($_SESSION['user_id'] == $userId){
					$startButton = "<button id=\"stopTimer-".$userId."\" type=\"button\" class=\"btn btn-danger btn-xs\">Stop</button>";
				}else{
					$startButton = "<button type=\"button\" class=\"btn btn-danger btn-xs\" disabled>Stop</button>";
				}
			}else{
				$elapsedTime = $row2->stopTime - $row2->startTime + 60;
				$elapsedTime = "@".$elapsedTime;
				$elapsedTime = new DateTime($elapsedTime);
				$elapsedTime = $elapsedTime->format("H:i");
				$lastTime = $lastTime."<div class=\"col-md-2\"><div class=\"row\">".$lastDate."</div><div class=\"row\">".$lastStart."</div><div class=\"row\">".$lastStop."</div><div class=\"row\">".$elapsedTime."</div></div>";
			}
		}
		$completePane = $completePane.$topPane.$lastTime."</div></div></div><div class=\"row complete_work ".$hideButtons."\"><div class=\"col-md-2 ".$actionButton."\">". $startButton ."</div><div class=\"col-md-2 col-md-offset-8\">". $finishButton ."</div></div></div></div>";
		$completePane .= "<div class=\"row col-md-12 spacer\"><div class=\"col-md-6 rWellPadding\"><div class=\"row\"><div class=\"well well-sm\">For Future Use</div></div></div></div></div>";
		 
		$i++;
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
	//Total Hours worked on job	
	$query = $db->prepare("SELECT * FROM worktimes WHERE workOrderId = ? AND stopTime > 0");
	$query->bind_param("i", $workOrderId);
	$query->execute();
	$result = $query->get_result();
	$totalHours = 0;
	while (($row = $result->fetch_object()) !== NULL) {
		$totalHours = $totalHours + ($row->stopTime - $row->startTime);
	}
	$totalTime = "@".$totalHours;
	$totalTime = new DateTime($totalTime);
	$totalTime = $totalTime->format("H:i");
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
	<li><a href="<?php echo $url_home; ?>">Home</a></li>
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
										<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#requestModal" aria-label="Edit" <?php echo $disable1; ?>>
											<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
										</button>
									</div>
								</div>
								<div class="row">
									<div class="col-md-3"><label>Type:</label></div>
									<div class="col-md-7" id="workType"><small><?php echo $workType; ?></small></div>
								</div>
								<div class="row">
									<div class="col-md-3" id="item_type"><label><?php echo $workItem; ?></label></div>
									<div class="col-md-7" id="item_name"><small><?php echo $workCenter; ?></small></div>
								</div>
								<div class="row <?php echo $hidden; ?>" id="hidden_row">
									<div class="col-md-3"><label>Machine:</label></div><div class="col-md-3" id="machine_name"><small><?php echo $machine_name; ?></small></div><div class="col-md-3"><label>Serial #:</label></div><div class="col-md-3" id="serial_number"><small><?php echo $serial; ?></small></div>
								</div>
								<div class="row">
									<div class="col-md-3"><label>Priority:</label></div>
									<div class="col-md-2" id="priority_level"><small><?php echo $priorityLabel; ?></small></div>
								</div>
								<div class="row">
									<div class="col-md-3"><label>Problem:</label></div>
									<div class="col-md-9"><textarea class="form-control" name="textDescription" id="textDescription" rows="2" disabled><?php echo $description; ?></textarea></div>
								</div>
								<div class="row">
									<div class="col-md-3"><label>Request By:</label></div>
									<div class="col-md-3" id="request_by"><small><?php echo $requesterName; ?></small></div>
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
										<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#approveModal" aria-label="Edit" <?php echo $disable2; ?>>
											<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
										</button>
									</div>
								</div>
								<div class="row">
									<div class="col-md-3"><label>Due Date:</label></div>
									<div class="col-md-3" id="due_date"><small><?php echo $dueDate; ?></small></div>
									<div class="col-md-3"><label>Esitmated Time:</label></div>
									<div class="col-md-3" id="estimated_time"><small><?php echo $days." Days ".$hrs. " Hrs ".$mins." Mins"; ?></small></div>
								</div>
								<div class="row">
									<div class="col-md-3"><label>Assigned to:</label></div>
									<div class="col-md-9" id="assigned_job"><div class="assignment"><?php echo $assignedJob; ?></div></div>
								</div>
								<div class="row">
									<div class="col-md-3"><label>Notes:</label></div>
									<div class="col-md-9"><textarea class="form-control" name="approval_notes" id="approval_notes" rows="2" disabled><?php echo $notes; ?></textarea></div>
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
										<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#dataModal" aria-label="Edit" <?php echo $disable2; ?>>
											<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
										</button>
									</div>
								</div>
								<div class="row">
									<div class="col-md-5"><label>Completed:</label></div>
									<div class="col-md-7"><small><?php echo $workCompleted; ?></small></div>
								</div>
								<div class="row">
									<div class="col-md-5"><label>Total Hours:</label></div>
									<div class="col-md-7" id="totalHours"><small><?php echo $totalTime; ?></small></div>
								</div>
								<div class="row">
									<div class="col-md-5"><label>Parts Required:</label></div>
									<div class="col-md-7"><small>N/A</small></div>
								</div>
								<div class="row">
									<div class="col-md-5"><label>Parts Cost:</label></div>
									<div class="col-md-7"><small>N/A</small></div>
								</div>
								<div class="row <?php echo $showDown; ?>">
									<div class="col-md-5"><label>Machine Down:</label></div>
									<div class="col-md-7"><input type="checkbox" name="machineDown" value="1" <?php echo $check_down; ?>></div>
								</div>
								<div class="row">
									<div class="col-md-5"><label>Issue:</label></div>
									<div class="col-md-7"><select id="selectIssue" name="selectIssue" class="form-control"><option value="0">-- Choose Type --</option><?php echo $issue_list; ?></select></div>
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
							<!--Start Pane generation -->
							<?php echo $completePane; ?>
							<!-- end Pane generation -->
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
								<form class="requestInfo" name="requestInfo">
									<input type="hidden" name="requestId" value="<?php echo $workRequestId; ?>">
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
								</form>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								<input class="btn btn-primary" type="submit" value="Save Changes" id="request">
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
								<h4 class="modal-title" id="approveModalLabel">Edit Approval Information</h4>
							</div>
							<div class="modal-body form-group">
								<form class="approveInfo" name="approveInfo">
									<input type="hidden" name="workOrderId" value="<?php echo $workOrderId; ?>">
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
											<?php
											
												$query = $db->prepare("SELECT * FROM users WHERE department = 600 ORDER BY firstname ASC");
												$query->execute();
												$result = $query->get_result();
												//$availableTechs = array();
													$i = 1;
												while (($row = $result->fetch_object()) !== NULL) {
													if(in_array($row->id, $assignedTechs)){
														
											?>
														<div class="checkbox"><label><input type="checkbox" name="check_list[]" value="<?php echo $row->id; ?>" checked><?php echo $row->firstname." ".$row->lastname; ?></label></div>
												<?php
													}else{
														
												?>		
														<div class="checkbox"><label><input type="checkbox" name="check_list[]" value="<?php echo $row->id; ?>"><?php echo $row->firstname." ".$row->lastname; ?></label></div>
												<?php		
													}
												}
											?>
												<?php //echo $checkbox_list; 
												echo $otherCheckbox;?>
												
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
								</form>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								<input type="submit" class="btn btn-primary"value="Save Changes" id="approve">
							</div>
						</div>
					</div>
				</div>
				<!-- Information Modal -->
				<div class="modal fade" id="dataModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title" id="infoModalLabel">Edit Data Information</h4>
							</div>
							<form>
								<div class="modal-body">
									...
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
									<button type="button" class="btn btn-primary">Save changes</button>
								</div>
							</form>
						</div>
					</div>
				</div>
				<!-- Work Summary Modal -->
				<div class="modal fade" id="descriptionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title" id="worktModalLabel">Edit Work Summary</h4>
							</div>
							<form>
								<div class="modal-body">
									<div class="row form-group" id="work_done">
										<div class="col-md-3"><label for="workDone">Work Done:</label></div>
										<div class="col-md-8"><textarea class="form-control" name="workDone" id="workDone" rows="9"><?php echo $workDone; ?></textarea></div>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
									<button type="button" class="btn btn-primary">Save changes</button>
								</div>
							</form>
						</div>
					</div>
				</div>
				<!-- Work Hours Modal -->
				<div class="modal fade" id="hoursModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title" id="hourstModalLabel">Edit Work Hours</h4>
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
</body>
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/jquery-ui.js"></script>
<script src="../js/jquery.runner-min.js" type="text/javascript"></script>
<script src="../js/jquery-idleTimeout.js"></script>
<script>
	window.workOrderId = <?php echo $workOrderId; ?>;
	window.user_id = <?php echo $_SESSION['user_id']; ?>;
	$(document).ready(function(){
		/*$(document).idleTimeout({ 
			inactivity: 10000, noconfirm: 5000, sessionAlive: 5000 
		});*/
		
		var itemType = <?php echo $workTypeId; ?>;
		var selectOtherChecked = <?php echo $checkedOther; ?>;
		//Start all the runners for work in progress when the page loads
		var startRunner = <?php echo json_encode($startRunner); ?>;
		$.each(startRunner, function(i,runner) {
			$("#runner-" + runner.user).runner({
				startAt: runner.elapsed_time*1000, 
				milliseconds: false
			});
			$("#runner-" + runner.user).runner('start');
		});
		
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
			//$('#selectItem').addAttr("required");
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
		
		$( ".do_action" ).on( "click", "[id^=startTimer-]", function() {
			var buttonId = this.id;
			var arr = buttonId.split('-');
			buttonId = arr[1];
			var d = new Date(); // for now
			var year = ('0' + d.getFullYear()).slice(-2);
			var month = d.getMonth() + 1;
			var day = d.getDay();
			var hours = d.getHours();
			var mins = ('0' + d.getMinutes()).slice(-2);
			var currentTime = hours + ":" + mins;
			
			var newCol = "<div class=\"col-md-2\"><div class=\"row\">" + month + "/" + day + "/" + year + "</div><div class=\"row\" id=\"startTime-" + buttonId + "\">" + currentTime + "</div><div class=\"row\" id=\"stopTime-" + buttonId + "\">&nbsp;</div><div class=\"row\" id=\"runner-" + buttonId + "\"></div></div>";
			$(".newCol").after($(newCol));
			$("#startTime-" + buttonId).html(currentTime);
			$("#runner-" + buttonId).runner({
				milliseconds: false
			});
			$("#runner-" + buttonId).runner('start');
			var action_button = "<button id=\"stopTimer-"+ buttonId +"\"type=\"button\" class=\"btn btn-danger btn-xs\">Stop</button>";
			$(".do_action").html(action_button);
			var d = new Date(); // for now
			var hours = d.getHours(); // => 9
			var mins = ('0' + d.getMinutes()).slice(-2); // =>  30
			var currentTime = hours + ":" + mins;
			$("#startTime-" + buttonId).html(currentTime);
			var request = $.getJSON("../ajax/startwork.php", {id : workOrderId, user : user_id}, function(data) {
				console.log(data);
				
				
			});
		});
		
		$( ".do_action" ).on( "click", "[id^=stopTimer-]", function() {
			var buttonId = this.id;
			var buttonId = this.id;
			var arr = buttonId.split('-');
			buttonId = arr[1];
			$("#runner-" + buttonId).runner('stop');
			var action_button = "<button id=\"startTimer-"+ buttonId +"\"type=\"button\" class=\"btn btn-success btn-xs\">Start</button>";
			$(".do_action").html(action_button);
			var d = new Date(); // for now
			var hours = d.getHours();
			var mins = ('0' + d.getMinutes()).slice(-2);
			var currentTime = hours + ":" + mins;
			$("#stopTime-" + buttonId).html(currentTime);
			var request = $.getJSON("../ajax/stopwork.php", {id : workOrderId, user : user_id}, function(data) {
				console.log(data);
				//alert(data[0].total_time);
				var a = "<small>" + data[0].total_time + "</small>";
				$("#totalHours").html(a);
				
			});
		});
		//Submit modal request form
		$("input#request").click(function(){
			$.ajax({
				type: "POST",
				url: "../ajax/updaterequest.php",
				data: $('form.requestInfo').serialize(),
				success: function(data){
					console.log(data);
					$("#requestModal").modal('hide'); //hide popup
					
					data = jQuery.parseJSON(data);
					$.each(data, function(key, value) {
						$("#workType").html("<small>" + value.work_type + "</small>");
						$("#item_type").html("<label>" + value.work_item + "</label>");
						$("#item_name").html("<small>" + value.work_center + "</small>");
						$("#priority_level").html("<small>" + value.priority + "</small>");
						$("#machine_name").html("<small>" + value.item_name + "</small>");
						$("#serial_number").html("<small>" + value.serial + "</small>");
						$("#textDescription").html(value.work_done);
						$("#request_by").html("<small>" + value.request_by + "</small>");
						if(value.work_type == "Machine"){
							$("#hidden_row").removeClass("hidden");
						}else{
							$("#hidden_row").addClass("hidden");
						}
						
					});
				},
				error: function(){
					alert("failure");
				}
			});
		});
		//Submit modal approval form
		$("input#approve").click(function(){
			$.ajax({
				type: "POST",
				url: "../ajax/updateapproval.php",
				data: $('form.approveInfo').serialize(),
				success: function(data){
					console.log(data);
					$("#approveModal").modal('hide'); //hide popup
					data = jQuery.parseJSON(data);
					$.each(data, function(key, value) {
						$("#due_date").html("<small>" + value.due_date + "</small>");
						$("#estimated_time").html("<small>" + value.estimated_time + "</small>");
						$("#approval_notes").html(value.approval_notes);
						$(".assignment").remove();
						$("#assigned_job").append("<div class=\"assignment\"></div>");
						$.each(value.selected_users, function(j, id){
							//updated displayed users on work order
							var a = "<div class=\"row\"><div class=\"col-md-5\"><small>" + id.firstname + " " + id.lastname + "</small></div></div>";
							$(".assignment").append(a);
						});
						$.each(value.removed_users, function(j, id){
							//removed tab and tab pane for removed users
							$(".tab-"+ id).remove();
							$(".pane-"+ id).remove();
						});
						$.each(value.added_users, function(j, added){
							//adds tab and tab pane for added users
							var workButton = "";
							var startButton = "";
							var finishButton = "";
							var newColumn = "";
							var disabled = "disabled";
							var doAction = "";
							var b = "<li class=\"assignmentTab tab-" + added.id + "\"><a href=\"#" + added.id + "\" data-toggle=\"tab\" id=\"worker-" + added.id + "\">" + added.firstname + " " + added.lastname + "</a></li>";
							$(".nav-tabs").append(b);
							if(user_id == added.id){
								workButton = "<button type=\"submit\" name=\"workDone\" formmethod=\"post\" class=\"btn btn-primary btn-sm\">Post Work</button>";
								startButton = "<button id=\"startTimer-" + added.id + "\" type=\"button\" class=\"btn btn-success btn-xs\">Start</button>";
								finishButton = "<button id=\"workCompleted\" type=\"button\" class=\"btn btn-primary btn-sm\">Work Completed</button>";
								newColumn = "newCol";
								doAction = "do_action";
								disabled = "";
							}else{
								startButton = "<button type=\"button\" class=\"btn btn-success btn-xs\" disabled>Start</button>";
							}
							var c = "<div class=\"tab-pane pane-" + added.id + "\" id=\"" + added.id + "\"><div class=\"row col-md-12 spacer\">" +
								"<form role=\"form\" method=\"post\" id=\"work\"><div class=\"col-md-6 rWellPadding\"><div class=\"row\">" +
								"<div class=\"well well-sm\"><div class=\"row\"><div class=\"col-md-2\"><label>Work Done:</label></div>" +
								"<div class=\"col-md-8\"><textarea class=\"form-control\" id=\"workDone\" rows=\"8\" " + disabled + "></textarea></div>" +
								"<div class=\"col-md-2\">" + workButton + "</div></div></div></div></div></form>" +
								"<div class=\"col-md-6 lWellPadding\" ><div class=\"row\"><div class=\"well well-sm\"><div class=\"row\">" +
								"<div class=\"col-md-1 col-md-offset-11\">" +
								"<button type=\"button\" class=\"btn btn-default btn-sm\" data-toggle=\"modal\" data-target=\"#hoursModal\" aria-label=\"Edit\">" +
								"<span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span></button></div></div>" +
								"<div class=\"row row-horizon LbtnMargin RbtnMargin spacer\"><div class=\"col-md-3 " + newColumn + "\">" +
								"<div class=\"row\"><b>Date</b></div><div class=\"row\"><b>Start Time</b></div><div class=\"row\"><b>Stop Time</b></div>" +
								"<div class=\"row\"><b>Total Time</b></div></div></div></div></div><div class=\"row complete_work\">" +
								"<div class=\"col-md-2 " + doAction + "\">" + startButton + "</div><div class=\"col-md-2 col-md-offset-8\">" + finishButton + "</div></div></div></div>" +
								"<div class=\"row col-md-12 spacer\"><div class=\"col-md-6 rWellPadding\"><div class=\"row\">" +
								"<div class=\"well well-sm\">For Future Use</div></div></div></div></div>";
							
							$(".tab-content").append(c);
						});
						
					});
				},
				error: function(){
					alert("failure");
				}
			});
		});
		//Complete work
		$( ".complete_work" ).on( "click", "[id=workCompleted]", function() {
			$(".complete_work").addClass("hidden");
			var request = $.getJSON("../ajax/completework.php", {id : workOrderId, user : user_id}, function(data) {
				console.log(data);
				//alert("Message sent");
			});
		});
		//Is machine down
		$("input[name=machineDown]").click(function(){
			var machineDown = 0;
			if(this.checked){
				machineDown = 1;
			}else{
				machineDown = 0;
			}
			var request = $.getJSON("../ajax/machinedown.php", {id : workOrderId, down : machineDown}, function(data) {
			console.log(data);
			});
		});
		$("#selectIssue").on('change', function(){
			//alert(this.value);
			var issue = this.value;
			var request = $.getJSON("../ajax/issue.php", {id : workOrderId, problem : issue}, function(data) {
				console.log(data);
			});
		});
		
	});
</script>
</body>
</html>
