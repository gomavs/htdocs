<?php
require '../includes/check_login.php';
if(isset($_GET['type'])){
	$problemType = $_GET['type'];
	$workTypeId = $_GET['workTypeId'];
	//echo $problemType;
	$query = $db->prepare("INSERT INTO problemlist (problem, workTypeId, active) VALUES (?, ?, 1)");
	$query->bind_param('si', $problemType, $workTypeId);
	$query->execute();
	$data = [];
	//retrieve new list of problems
	$query = $db->prepare("SELECT * FROM problemlist WHERE workTypeId = ? AND active = 1 ORDER BY problem ASC");
	$query->bind_param("i", $workTypeId);
	$query->execute();
	$result = $query->get_result();
	while (($row = $result->fetch_object()) !== NULL) {
		$data[] = ["id"=>$row->id, "problem"=>$row->problem];	
	}
	echo json_encode($data);
}
?>