<?php
require_once("../includes/dbConnect.php");
if(isset($_GET['id']) && isset($_GET['user'])){
	$timenow = time();
	$workOrderId = $_GET["id"];
	$userId = $_GET["user"];
	mysqli_query($db,"INSERT INTO worktimes (userId, workOrderId, startTime)VALUES ('$userId', '$workOrderId', '$timenow')");
	$data[] = ["start_time"=>$timenow];
echo json_encode($data);
}
?>