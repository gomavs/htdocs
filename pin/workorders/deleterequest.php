<?php
//require '../includes/check_login.php';
require_once("../includes/dbConnect.php");
if(isset($_GET['id'])){
	
	$requestId = $_GET['id'];
	$query = $db->prepare("SELECT workrequest.workTypeId, workrequest.itemId, workrequest.requestedBy, workrequest.escalate, workrequest.declinedFor, workrequest.declinedBy, users.firstname, users.lastname, declinedreasons.reason FROM workrequest LEFT JOIN declinedreasons ON workrequest.declinedFor = declinedreasons.id LEFT JOIN users ON workrequest.declinedBy = users.id WHERE workrequest.id = ?");
	$query->bind_param("i", $requestId);
	$query->execute();
	$result = $query->get_result();
	$row = $result->fetch_assoc();
	$workTypeId = $row['workTypeId'];
	$itemId = $row['itemId'];
	$requestedBy = $row['requestedBy'];
	$escalate = $row['escalate'];
	$declinedFor = $row['declinedFor'];
	$declinedBy= $row['declinedBy'];
	$declinedReason = $row['reason'];
	$firstname = $row['firstname'];
	$lastname = $row['lastname'];
	if(isset($_GET['reason'])){
		$declinedFor = $_GET['reason'];
		$query1 = $db->prepare("SELECT reason FROM declinedreasons WHERE id = ?");
		$query1->bind_param("i", $declinedReason);
		$query1->execute();
		$result1 = $query1->get_result();
		$row1 = $result1->fetch_assoc();
		$declinedReason = $row1['reason'];
	}
	if(isset($_GET['userId'])){
		$declinedBy = $_GET['userId'];
		$query1 = $db->prepare("SELECT firstname, lastname FROM users WHERE id = ?");
		$query1->bind_param("i", $declinedBy);
		$query1->execute();
		$result1 = $query1->get_result();
		$row1 = $result1->fetch_assoc();
		$decliner = $row1['firstname']." ".$row1['lastname'];
	}
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
	// Group the tiers
	$authorized = 0;
	$data = array();
	$query = $db->prepare("SELECT userId, tier FROM escalation ORDER BY tier ASC");
	$query->execute();
	$result = $query->get_result();
	while (($row = $result->fetch_object()) !== NULL) {
		if($row->userId == $declinedBy && $row->tier > $escalate){
			$authorized = 1;
			$escalateTo = $row->tier;
			$key = $row->tier - 1;
		}
		$data[$row->tier - 1][] = $row->userId;
	}
	//print_r($data);
	
	if($authorized == 1){
		$date = new DateTime();
		$timestamp = $date->getTimestamp();
		$viewed = 0;
		$accepted = 2;
		$messageEnd = "and escalated";
		if($key == 2){
			$messageEnd = "";
		}
		//message to the requester
		$message = "The work request for <b>".$itemDeclined."</b> has been declined by <b>".$decliner."</b> ".$messageEnd;
		$query = $db->prepare("INSERT INTO messages (msgTo, msgFrom, date, viewed, message) VALUES (?, ?, ?, ?, ?)");
		$query->bind_param("iiiis", $requestedBy, $declinedBy, $timestamp, $viewed, $message);
		$query->execute();
		//message to the next tier
		$message = "A work request for <b>".$itemDeclined."</b> has been declined by <b>".$decliner."</b> ";
		foreach($data[$key] as $value){
			$query = $db->prepare("INSERT INTO messages (msgTo, msgFrom, date, viewed, message) VALUES (?, ?, ?, ?, ?)");
			$query->bind_param("iiiis", $value, $declinedBy, $timestamp, $viewed, $message);
			$query->execute();
		}
		$query = $db->prepare("UPDATE workrequest SET accepted = 2, escalate = ?, declinedFor = ?, declinedBy = ? WHERE id = ? ");
		$query->bind_param("iiii", $escalateTo, $declinedFor, $declinedBy, $requestId);
		$query->execute();
	}
}