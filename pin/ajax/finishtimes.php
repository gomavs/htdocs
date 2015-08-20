<?php
require '../includes/check_login.php';
if(isset($_GET['id']) && isset($_GET['machine']) && isset($_GET['userid'])){
	$item_id = $_GET["id"];
	$mid = $_GET["machine"];
	$userid = $_GET['userid'];
	$completed = 1;
	$query = $db->prepare("UPDATE times SET completed = ?, completedby = ? WHERE item_id = ? AND machine_id = ?");
	$query->bind_param("iiii", $completed, $userid, $item_id, $mid);
	$query->execute();
}
?>