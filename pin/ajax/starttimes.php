<?php
require_once("../includes/dbConnect.php");
if(isset($_GET['id']) && isset($_GET['machine'])){
	$timenow = time();
	$item_id = $_GET["id"];
	$mid = $_GET["machine"];
	mysqli_query($db,"INSERT INTO times (item_id, machine_id, start_time, completed)
	VALUES ('$item_id', '$mid', '$timenow', 0)");
	$data[] = ["start_time"=>$timenow];
echo json_encode($data);
}
?>