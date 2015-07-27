<?php
require '../includes/check_login.php';
if(isset($_GET['id'])){
	$requestId = $_GET['id'];
	$query = $db->prepare("SELECT workrequest.workTypeId, workrequest.declinedBy, workrequest.accepted, workrequest.escalate, workrequest.declinedFor, workrequest.declinedBy, users.firstname, users.lastname, declinedreasons.reason FROM workrequest LEFT JOIN declinedreasons ON workrequest.declinedFor = declinedreasons.id LEFT JOIN users ON workrequest.declinedBy = users.id WHERE workrequest.id = ?");
	$query->bind_param("i", $requestId);
	$query->execute();
	$result = $query->get_result();
	$row = $result->fetch_assoc();
	$workTypeId = $row['workTypeId'];
	$itemId = $row['itemId'];
	$requestedBy = $row['requestedBy'];
	$declinedBy = $row['declinedBy'];
	$accepted = $row['accepted'];
	$escalate = $row['escalate'];
	$declinedFor = Row['declinedFor'];
	
	switch($workTypeId){
		case 1:  
			$query1 = $db->prepare("SELECT name, center FROM workcenter WHERE id = ?");
			$query1->bind_param("i", $itemId);
			$query1->execute();
			$result1 = $query1->get_result();
			$row1 = $result1->fetch_assoc();
			$itemDeclined = "Center ".$row1['center']." ".$row1['name'];
			break;
		case 2:
			$query1 = $db->prepare("SELECT item FROM facilitytype WHERE id = ?");
			$query1->bind_param("i", $itemId);
			$query1->execute();
			$result1 = $query1->get_result();
			$row1 = $result1->fetch_assoc();
			$itemDeclined = $row1['item'];
			break;
		case 3:
			$query1 = $db->prepare("SELECT item FROM safetytype WHERE id = ?");
			$query1->bind_param("i", $itemId);
			$query1->execute();
			$result1 = $query1->get_result();
			$row1 = $result1->fetch_assoc();
			$itemDeclined = $row1['item'];
			break;
		case 4:
			$query1 = $db->prepare("SELECT item FROM toolstype WHERE id = ?");
			$query1->bind_param("i", $itemId);
			$query1->execute();
			$result1 = $query1->get_result();
			$row1 = $result1->fetch_assoc();
			$itemDeclined = $row1['item'];
		break;
		case 5:
			$itemDeclined = $row['other'];
			break;
	}
	
	if($accepted < 2){
		$escalate = 1;
		
	}elseif($accepted == 3){
		
	}else{
		
		$escalate = $escalate + 1;
	}
	$date = new DateTime();
	$timestamp = $date->getTimestamp();
	$viewed = 0;
	$type = 1;
	$query = $db->prepare("UPDATE workrequest SET accepted = 2, escalate = 1 WHERE id = ? ");
	$query->bind_param("i", $requestId);
	$query->execute();
	$query = $db->prepare("SELECT users.firstname, users.lastname, escalation.tier FROM users LEFT JOIN escalation ON users.id = escalation.userId WHERE users.id = ?");
	$query->bind_param("i", $deletedBy);
	$query->execute();
	$result = $query->get_result();
	$row = $result->fetch_assoc();
	
	$message = "The work request for <b>".$itemDeclined."</b> has been declined by <b>".$row['firstname']." ".$row['lastname']."</b> and escalated";
	$query = "INSERT INTO messages (msgTo, msgFrom, date, viewed, message) VALUES ('$requestedBy', '$deletedBy', '$timestamp', '$viewed', '$message')";
	$db->query($query);
	
	header('location: openrequests.php');
}