<?php
require_once("../includes/dbConnect.php");
$data = [];
if(isset($_GET['id'])){
	$problemId = $_GET['id'];
	$query = $db->prepare("SELECT * FROM problems WHERE problemId = ? AND active = 1");
	$query->bind_param("i", $problemId);
	$query->execute();
	$result = $query->get_result();
	while (($row = $result->fetch_object()) !== NULL) {
		$data[] = ["machineId"=>$row->itemId];
	}
}
echo json_encode($data);
?>