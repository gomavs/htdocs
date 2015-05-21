<?php
require_once("../includes/dbConnect.php");
if(isset($_GET['id']) && isset($_GET['machine'])){
	$timenow = time();
	$item_id = $_GET["id"];
	$mid = $_GET["machine"];
	
	$query = $db->prepare("DELETE FROM times WHERE item_id = ? AND machine_id = ?");
	$query->bind_param("ii", $item_id, $mid);
	$query->execute();
	
	$data[] = ["start_time"=>$timenow];
	echo json_encode($data);

}

?>