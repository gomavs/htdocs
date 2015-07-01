<?php
require_once("../includes/dbConnect.php");
//$userId = $_SESSION['user_id'];
$userId = 1;
$data = [];
$query = $db->prepare("SELECT messages.id, messages.priority, messages.date, messages.message, messages.viewed, messages.link, users.firstname, users.lastname FROM messages LEFT JOIN users ON messages.msgFrom = users.id WHERE messages.msgTo = ?");
$query->bind_param("i", $userId);
$query->execute();
$result = $query->get_result();
while (($row = $result->fetch_object()) !== NULL) {
	switch($row->priority){
		case 1: $priority = "Low"; break;
		case 2: $priority = "Medium"; break;
		case 3: $priority = "High"; break;
		default: $priority = "Low";
	}
	$requestDate = date("F j, Y"  ,$row->date);
	$alertTime = "@".$row->date;
	$alertTime = new DateTime($alertTime);
	$alertTime->setTimezone(new DateTimeZone('America/Chicago'));
	$alertTime = $alertTime->format("h:i A");
	$data[] = [
		"Priority"=>$priority, 
		"Date"=>$requestDate, 
		"Time"=>$alertTime,
		"Message"=>$row->message, 
		"From"=>$row->firstname." ".$row->lastname,
		"link"=>$row->link,
		"id"=>$row->id
	];
}

echo json_encode($data);
?>