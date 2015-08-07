<?php
require_once("../includes/dbConnect.php");
$data = [];
$i = 0;
//$query = $db->prepare("SELECT * FROM workrequest WHERE accepted = 2 AND escalate = 1 ORDER BY id ASC");
$query = $db->prepare("SELECT workrequest.*, users.firstname AS uRF, users.lastname AS uRL, decline_user.firstname AS uDF, decline_user.lastname AS uDL, declinedreasons.reason FROM workrequest LEFT JOIN users ON workrequest.requestedBy = users.id LEFT JOIN users AS decline_user ON workrequest.declinedBy = decline_user.id LEFT JOIN declinedreasons ON workrequest.declinedFor = declinedreasons.id WHERE workrequest.accepted = 2 AND workrequest.escalate = 1 ORDER BY workrequest.id ASC");
$query->execute();
$result = $query->get_result();
while (($row = $result->fetch_object()) !== NULL) {
	$i++;
	$workTypeId = $row->workTypeId;
	$itemId = $row->itemId;
	$description = $row->description;
	$requestDate = date("F j, Y"  ,$row->timestamp);
	$requestTime = "@".$row->timestamp;
	$requestTime = new DateTime($requestTime);
	$requestTime->setTimezone(new DateTimeZone('America/Chicago'));
	$requestTime = $requestTime->format("h:i A");
	$otherType = $row->other;
	$id = $row->id;
	$requestedBy = $row->uRF." ".$row->uRL;
	$declinedBy = $row->uDF." ".$row->uDL;
	$declinedFor = $row->reason;
	switch($row->priority){
		case 1: $priority = 1; $mark = ""; break;
		case 2: $priority = 2; $mark = "!"; break;
		case 3: $priority = 3; $mark = "!"; break;
		default: $priority = 1; $mark = "";
	}
	if($workTypeId == 1){
		$workType = "Machine";
		$query = $db->prepare("SELECT name, center FROM workcenter WHERE id = ?");
		$query->bind_param("i", $itemId);
		$query->execute();
		$result3 = $query->get_result();
		$row3 = $result3->fetch_assoc();
		$items = "Center ".$row3['center']."&nbsp;&nbsp; ".$row3['name'];
		
	}elseif($workTypeId == 2){
		$workType = "Facility";
		$query = $db->prepare("SELECT item FROM facilitytype WHERE id = ?");
		$query->bind_param("i", $itemId);
		$query->execute();
		$result3 = $query->get_result();
		$row3 = $result3->fetch_assoc();
		$items = $row3['item'];
		
	}elseif($workTypeId == 3){
		$workType = "Safety";
		$query = $db->prepare("SELECT item FROM safetytype WHERE id = ?");
		$query->bind_param("i", $itemId);
		$query->execute();
		$result3 = $query->get_result();
		$row3 = $result3->fetch_assoc();
		$items = $row3['item'];
		
	}elseif($workTypeId == 4){
		$workType = "Tools";
		$query = $db->prepare("SELECT item FROM toolstype WHERE id = ?");
		$query->bind_param("i", $itemId);
		$query->execute();
		$result3 = $query->get_result();
		$row3 = $result3->fetch_assoc();
		$items = $row3['item'];
		
	}elseif($workTypeId == 5){
		$workType = "Other";
		$items = $otherType;
	}
	$data[] = ["#"=>$i, "Mark"=>$mark, "Type"=>$workType, "Item"=>$items, "Description"=>$description, "Request Date"=>$requestDate, "Requested By"=>$requestedBy, "Declined By"=>$declinedBy, "Declined Reason"=>$declinedFor, "ID"=>$id, "status"=>$priority];
	//$data[] = ["#"=>$i, "Type"=>$workType, "Item"=>$items, "Description"=>$description, "Request Date"=>$requestDate, "Request Time"=>$requestTime, "Requested By"=>$requestedBy, "Priority"=>$priority, "ID"=>$id];
}
echo json_encode($data);
?>
