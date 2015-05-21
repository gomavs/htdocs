<?php
require_once("../includes/dbConnect.php");
if(isset($_GET['id']) && isset($_GET['machine'])){
	$timenow = time();
	$item_id = $_GET["id"];
	$mid = $_GET["machine"];
	$query = $db->prepare("UPDATE times SET end_time = ? WHERE item_id = ? AND machine_id = ?");
	$query->bind_param("iii", $timenow, $item_id, $mid);
	$query->execute();

}
?>