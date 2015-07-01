<?php
require_once("../includes/dbConnect.php");
if(isset($_POST['requestId'])){
	$requestId = $_POST["requestId"];
	$requestType = $_POST['selectRequestType'];
	if(isset($_POST['selectItem'])){
		$itemType = $_POST['selectItem'];
	}else{
		$itemType = "";
	}
	
	if(isset($_POST['inputOther'])){
		$inputOther = $_POST['inputOther'];
	}else{
		$inputOther = "";
	}
	$selectPriority = $_POST['selectPriority'];
	$textDescription = $_POST['textDescription'];
	$requestBy = $_POST['selectRequestBy'];
	$query = $db->prepare("UPDATE workrequest SET workTypeId = ?, itemId = ?, priority = ?, description = ?, requestedBy = ?, other = ? WHERE id = ?");
	$query->bind_param("iiisisi", $requestType, $itemType, $selectPriority, $textDescription, $requestBy, $inputOther, $requestId);
	$query->execute();
	$workItem = "Item";
	$workCenter = "";
	$serial = "";
	$item_id = "";
	$item_name = "";
	//Item Select Dropdown
	if($requestType == 1){
		$workType = "Machine";
		$workItem = "Work Center:";
		$query = $db->prepare("SELECT * FROM workcenter WHERE id = ?");
		$query->bind_param("i", $itemType);
		$query->execute();
		$result = $query->get_result();
		$row = $result->fetch_assoc();
		$workCenter = $row['center'];
		$serial = $row['serial'];
		$item_id = $row['id'];
		$item_name = $row['name'];
	}elseif($requestType == 2){
		$workType = "Facility";
		$query = $db->prepare("SELECT * FROM facilitytype WHERE id = ?");
		$query->bind_param("i", $itemType);
		$query->execute();
		$result = $query->get_result();
		$row = $result->fetch_assoc();
		$item_id = $row['id'];
		$workCenter = $row['item'];
	}elseif($requestType == 3){
		$workType = "Safety";
		$query = $db->prepare("SELECT * FROM safetytype WHERE id = ?");
		$query->bind_param("i", $itemType);
		$query->execute();
		$result = $query->get_result();
		$row = $result->fetch_assoc();
		$item_id = $row['id'];
		$workCenter = $row['item'];
	}elseif($requestType == 4){
		$workType = "Tools";
		$query = $db->prepare("SELECT * FROM toolstype WHERE id = ?");
		$query->bind_param("i", $itemType);
		$query->execute();
		$result = $query->get_result();
		$row = $result->fetch_assoc();
		$item_id = $row['id'];
		$workCenter = $row['item'];
	}elseif($requestType == 5){
		$workType = "Other";
		$workCenter = $inputOther;
	}
	switch ($selectPriority){
		case 1: $priority = "Low"; break;
		case 2: $priority = "Medium"; break;
		case 3: $priority = "High"; break;
		default: $priority = "Low";
	}
	$query = $db->prepare("SELECT firstname, lastname FROM users WHERE id = ?");
	$query->bind_param("i", $requestBy);
	$query->execute();
	$result = $query->get_result();
	$row = $result->fetch_assoc();
	$requesterName = $row['firstname']." ".$row['lastname'];
	
	
	$data[] = ["work_type"=>$workType, "work_item"=>$workItem, "work_center"=>$workCenter, "serial"=>$serial, "item_id"=>$item_id, "item_name"=>$item_name, "priority"=>$priority, "work_done"=>$textDescription, "request_by"=>$requesterName]; 
	echo json_encode($data);
}
?>