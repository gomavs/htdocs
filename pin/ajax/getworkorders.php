<?php
require_once("../includes/dbConnect.php");
$data = [];
//Pull open work orders
$query = $db->prepare("SELECT * FROM workorder WHERE status = 0 ORDER BY id ASC");
$query->execute();
$result = $query->get_result();
while (($row = $result->fetch_object()) !== NULL) {
	$workOrderId = $row->id;
	$workRequestId = $row->workRequestId;
	$startDate = date("F j, Y" ,$row->startDate);
	$dueDate = date("F j, Y", $row->dueDate);
	$timeEstimate = secondsToTime($row->timeEstimate);
	$notes = $row->notes;
	//Get info from work request
	$query1 = $db->prepare("SELECT * FROM workrequest WHERE id = ?");
	$query1->bind_param("i", $workRequestId);
	$query1->execute();
	$result1 = $query1->get_result();
	$row1 = $result1->fetch_assoc();
	$workTypeId = $row1['workTypeId'];
	$itemId = $row1['itemId'];
	$description = $row1['description'];
	$requestDate = date("F j, Y"  ,$row1['timestamp']);
	$requestTime = "@".$row1['timestamp'];
	$requestTime = new DateTime($requestTime);
	$requestTime->setTimezone(new DateTimeZone('America/Chicago'));
	$requestTime = $requestTime->format("h:i A");
	$requesterId = $row1['requestedBy'];
	$otherType = $row1['other'];
	switch($row1['priority']){
		case 1: $priority = "Low"; break;
		case 2: $priority = "Medium"; break;
		case 3: $priority = "High"; break;
		default: $priority = "Low";
	}
	if($workTypeId == 1){
		$workType = "Machine";
		$query1 = $db->prepare("SELECT name, center FROM workcenter WHERE id = ?");
		$query1->bind_param("i", $itemId);
		$query1->execute();
		$result1 = $query1->get_result();
		$row1 = $result1->fetch_assoc();
		$items = "Center ".$row1['center']."&nbsp;&nbsp; ".$row1['name'];
		
	}elseif($workTypeId == 2){
		$workType = "Facility";
		$query1 = $db->prepare("SELECT item FROM facilitytype WHERE id = ?");
		$query1->bind_param("i", $itemId);
		$query1->execute();
		$result1 = $query1->get_result();
		$row1 = $result1->fetch_assoc();
		$items = $row1['item'];
		
	}elseif($workTypeId == 3){
		$workType = "Safety";
		$query1 = $db->prepare("SELECT item FROM safetytype WHERE id = ?");
		$query1->bind_param("i", $itemId);
		$query1->execute();
		$result1 = $query1->get_result();
		$row1 = $result1->fetch_assoc();
		$items = $row1['item'];
		
	}elseif($workTypeId == 4){
		$workType = "Tools";
		$query1 = $db->prepare("SELECT item FROM toolstype WHERE id = ?");
		$query1->bind_param("i", $itemId);
		$query1->execute();
		$result1 = $query1->get_result();
		$row1 = $result1->fetch_assoc();
		$items = $row1['item'];
		
	}elseif($workTypeId == 5){
		$workType = "Other";
		$items = $otherType;
	}
	$query1 = $db->prepare("SELECT firstname, lastname FROM users WHERE id = ?");
	$query1->bind_param("i", $requesterId);
	$query1->execute();
	$result1 = $query1->get_result();
	$row1 = $result1->fetch_assoc();
	$requestedBy = $row1['firstname']." ".$row1['lastname'];
	$assignedTo = assignment($workOrderId);
	$assignedTo = rtrim($assignedTo, ", ");
	$data[] = [
		"#"=>$workOrderId, 
		"Type"=>$workType, 
		"Item"=>$items, 
		"Description"=>$description, 
		"Request Date"=>$requestDate, 
		"Request Time"=>$requestTime, 
		"Requested By"=>$requestedBy, 
		"Priority"=>$priority,  
		"Assigned"=>$assignedTo, 
		"assignDate"=>$startDate,
		"dueDate"=>$dueDate,
		"estimate"=>$timeEstimate,		
		"notes"=>$notes,
		"id"=>$workOrderId
		];
	
}

function secondsToTime($seconds) {
    $dtF = new DateTime("@0");
    $dtT = new DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes');
}

function assignment($workid){
	global $db;
	$employees = "";
	$query2 = $db->prepare("SELECT assignedTo FROM workdata WHERE workOrderId = ?");
	$query2->bind_param("i", $workid);
	$query2->execute();
	$result2 = $query2->get_result();
	while (($row2 = $result2->fetch_object()) !== NULL) {
		$techId = $row2->assignedTo;
		$query1 = $db->prepare("SELECT firstname, lastname FROM users WHERE id = ?");
		$query1->bind_param("i", $techId);
		$query1->execute();
		$result1 = $query1->get_result();
		$row1 = $result1->fetch_assoc();
		$employees = $employees . $row1['firstname']." ".$row1['lastname'].", ";
	}
	
	return $employees;
}


echo json_encode($data);
?>
