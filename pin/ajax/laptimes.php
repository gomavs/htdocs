<?php
require_once("../includes/dbConnect.php");
if(isset($_GET['id']) && isset($_GET['machine'])){
	$timenow = time();
	$item_id = $_GET["id"];
	$mid = $_GET["machine"];
	$query = $db->prepare("UPDATE times SET end_time = ?, lap = 1 WHERE item_id = ? AND machine_id = ? AND lap = 0");
	$query->bind_param("iii", $timenow, $item_id, $mid);
	$query->execute();
	
	$query = $db->prepare("INSERT INTO times (item_id, machine_id, start_time, completed) VALUES (?, ?, ?, 0)");
	$query->bind_param("iii", $item_id, $mid, $timenow);
	$query->execute();
	//$data[] = ["start_time"=>$timenow];
}
?>