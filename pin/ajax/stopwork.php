<?php
require_once("../includes/dbConnect.php");
if(isset($_GET['id']) && isset($_GET['user'])){
	$timenow = time();
	$workOrderId = $_GET["id"];
	$userId = $_GET["user"];
	$query = $db->prepare("UPDATE worktimes SET stopTime = ? WHERE workOrderId = ? AND userId = ? AND stopTime = 0");
	$query->bind_param("iii", $timenow, $workOrderId, $userId);
	$query->execute();

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
	$data[] = ["stop_time"=>$timenow, "total_time"=>$totalTime]; 
echo json_encode($data);
}
?>