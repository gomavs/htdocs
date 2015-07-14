<?php
require_once("../includes/dbConnect.php");
include '../includes/systems.php';
if(isset($_GET['id']) && isset($_GET['user'])){
	$workOrderId = $_GET["id"];
	$userId = $_GET["user"];
	$timenow = time();
	$query = $db->prepare("SELECT * FROM worktimes WHERE userId = ? AND workOrderId = ? AND stopTime = 0");
	$query->bind_param("ii", $userId, $workOrderId);
	$query->execute();
	$result = $query->get_result();
	while (($row = $result->fetch_object()) !== NULL) {
		$rowId = $row->id;
		$query = $db->prepare("UPDATE worktimes SET stopTime = ? WHERE id = ?");
		$query->bind_param("ii", $timenow, $rowId);
		$query->execute();
	}
	$query = $db->prepare("UPDATE workdata SET status = 1 WHERE workOrderId = ? AND assignedTo = ?");
	$query->bind_param("ii", $workOrderId, $userId);
	$query->execute();
	$jobComplete = "1";
	$query = $db->prepare("SELECT * FROM workdata WHERE workOrderId = ?");
	$query->bind_param("i", $workOrderId);
	$query->execute();
	$result = $query->get_result();
	while (($row = $result->fetch_object()) !== NULL) {
		if($row->status == 0){
			$jobComplete = 0;
		}
	}
	if($jobComplete == 1){
		$query = $db->prepare("UPDATE workorder SET endDate = ?, status = 1 WHERE id = ?");
		$query->bind_param("ii", $timenow, $workOrderId);
		$query->execute();
		$message = "Work Order <b>".$workOrderId."</b> has been completed.";	
		$link = "workorders/workorder.php?id=".$workOrderId;
		$viewed = 0;
		$selectPriority = "3";
		$query = $db->prepare("SELECT id FROM users WHERE authWO >= 4 AND department = 600");
		$query->execute();
		$result = $query->get_result();
		while (($row = $result->fetch_object()) !== NULL) {
			$query1 = $db->prepare("INSERT INTO messages (msgTo, msgFrom, priority, date, viewed, message, link) VALUES (?, ?, ?, ?, ?, ?, ?)");
			$query1->bind_param('iiiiiss', $row->id, $userId, $selectPriority, $timenow, $viewed, $message, $link);
			$query1->execute();
			sendAlert($db->insert_id);
		}
	}

	$data[] = ["stop_time"=>$timenow];
	
echo json_encode($data);

}
?>