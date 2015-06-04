<?php
require_once("../includes/dbConnect.php");
if(isset($_GET['userid']) && isset($_GET['wo'])){
	$timenow = time();
	$workOrderId = $_GET["wo"];
	$userId = $_GET["userid"];
	$query = $db->prepare("SELECT * FROM workdata WHERE workOrderId = ? AND assignedTo = ?");
	$query->bind_param("ii", $workOrderId, $userId);
	$query->execute();
	$result = $query->get_result();
	$row = $result->fetch_assoc();
	$data[] = ["work_done"=>$row['workDone']];
	$startRunner = "";
	$lastStop = "";
	$elapsedTime = "";
	$query = $db->prepare("SELECT * FROM worktimes WHERE userId = ? AND workOrderId = ? ORDER BY startTime DESC");
	$query->bind_param("ii", $userId, $workOrderId);
	$query->execute();
	$result = $query->get_result();
	while (($row = $result->fetch_object()) !== NULL) {
		
		$lastDate = date("m/j/y", $row->startTime);
		$lastStart = "@".$row->startTime;
		$lastStart = new DateTime($lastStart);
		$lastStart->setTimezone(new DateTimeZone('America/Chicago'));
		$lastStart = $lastStart->format("H:i");
		if($row->stopTime > 0){
			$lastStop = "@".$row->stopTime;
			$lastStop = new DateTime($lastStop);
			$lastStop->setTimezone(new DateTimeZone('America/Chicago'));
			$lastStop = $lastStop->format("H:i");
			$startRunner = $row->stopTime - $row->startTime;
			$elapsedTime = $startRunner + 60;
			$elapsedTime = "@".$elapsedTime;
			$elapsedTime = new DateTime($elapsedTime);
			$elapsedTime = $elapsedTime->format("H:i");
		}else{
			
		}

		$data[] = ["date"=>$lastDate, "start_time"=>$lastStart, "stop_time"=>$lastStop, "work_time"=>$elapsedTime, "elapsed_time"=>$startRunner];
		
	}

	
echo json_encode($data);
}
?>