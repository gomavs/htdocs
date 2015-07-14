<?php
require_once("../includes/dbConnect.php");
$userId = $_GET['userid'];
$data = [];
$query = $db->prepare("SELECT messages.id, messages.priority, messages.date, messages.message, messages.viewed, messages.link, users.firstname, users.lastname FROM messages LEFT JOIN users ON messages.msgFrom = users.id WHERE messages.msgTo = ? ORDER BY messages.viewed ASC, messages.priority DESC, messages.date DESC");
$query->bind_param("i", $userId);
$query->execute();
$result = $query->get_result();
$row_cnt = mysqli_num_rows($result);
while (($row = $result->fetch_object()) !== NULL) {
	$symbol = "";
	switch($row->priority){
		case 1: $priority = "Low"; break;
		case 2: $priority = "Medium";
				$symbol = "<b>!</b>";
				break;
		case 3:	$priority = "High";
				$symbol = "<b>!</b>";
				break;
		default: $priority = "Low";
	}
	$requestDate = date("F j, Y"  ,$row->date);
	$alertTime = "@".$row->date;
	$alertTime = new DateTime($alertTime);
	$alertTime->setTimezone(new DateTimeZone('America/Chicago'));
	$alertTime = $alertTime->format("h:i A");
	$data[] = [
		"mark"=>$symbol,
		"Priority"=>$priority,
		"Date"=>$requestDate, 
		"Time"=>$alertTime,
		"Message"=>$row->message, 
		"From"=>$row->firstname." ".$row->lastname,
		"link"=>$row->link,
		"id"=>$row->id,
		"viewed"=>$row->viewed,
		"status"=>$row->priority
	];
}
if($row_cnt == 0){
	$message = "You have no alerts";
	$data[] = [
		"mark"=>"",
		"Priority"=>"",
		"Date"=>"", 
		"Time"=>"",
		"Message"=>$message, 
		"From"=>"",
		"link"=>"",
		"id"=>0,
		"viewed"=>1,
		"status"=>""
	];
}

echo json_encode($data);
?>