<?php
require_once("../includes/dbConnect.php");
$data = [];
$i = 0;
$query = $db->prepare("SELECT * FROM workrequest WHERE accepted = 0 ORDER BY id ASC");
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
	$requesterId = $row->requestedBy;
	$otherType = $row->other;
	$id = $row->id;
	$query = $db->prepare("SELECT firstname, lastname FROM users WHERE id = ?");
	$query->bind_param("i", $requesterId);
	$query->execute();
	$result2 = $query->get_result();
	$row2 = $result2->fetch_assoc();
	$requestedBy = $row2['firstname']." ".$row2['lastname'];
	switch($row->priority){
		case 1: $priority = "Low"; break;
		case 2: $priority = "Medium"; break;
		case 3: $priority = "High"; break;
		default: $priority = "Low";
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
	$data[] = ["#"=>$i, "Type"=>$workType, "Item"=>$items, "Description"=>$description, "Request Date"=>$requestDate, "Request Time"=>$requestTime, "Requested By"=>$requestedBy, "Priority"=>$priority, "ID"=>$id];
}
echo json_encode($data);
?>
