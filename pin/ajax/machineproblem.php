<?php
require '../includes/check_login.php';
if(isset($_GET['type'])){
	$problemType = $_GET['type'];
	mysqli_query($db,"INSERT INTO machineproblems (problem, active) VALUES ('$problemType', 1)");
	$data = [];
	//retrieve new list of problems
	$query = $db->prepare("SELECT * FROM machineproblems WHERE active = 1 ORDER BY problem ASC");
	$query->execute();
	$result = $query->get_result();
	while (($row = $result->fetch_object()) !== NULL) {
		$data[] = ["id"=>$row->id, "problem"=>$row->problem];	
	}
	echo json_encode($data);
}
?>