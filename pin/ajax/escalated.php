<?php
require_once("../includes/dbConnect.php");
$data = [];
if(isset($_GET['request'])){
	$query = $db->prepare("SELECT declinedFor FROM workrequest WHERE id = ?");
	$query->bind_param("i", $_GET['request']);
	$query->execute();
	$result = $query->get_result();
	$row = $result->fetch_assoc();
	$data[] = ["reason"=>$row['declinedFor']];
}
echo json_encode($data);
?>