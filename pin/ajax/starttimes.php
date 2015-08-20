<?php
require_once("../includes/dbConnect.php");
if(isset($_GET['id']) && isset($_GET['machine'])){
	$timenow = time();
	$item_id = $_GET["id"];
	$mid = $_GET["machine"];
	$query = $db->prepare("INSERT INTO times (item_id, machine_id, start_time, completed) VALUES (?, ?, ?, 0)");
	$query->bind_param("iii", $item_id, $mid, $timenow);
	$query->execute();
	$data[] = ["start_time"=>$timenow];
	echo json_encode($data);
}
?>