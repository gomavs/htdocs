<?php
require '../includes/check_login.php';
if(isset($_GET['type'])){
	$problemType = $_GET['type'];
	mysqli_query($db,"INSERT INTO facilitytype (item, active) VALUES ('$problemType', 1)");
	$data = [];
	//retrieve new list of problems
	$query = $db->prepare("SELECT * FROM facilitytype ORDER BY item ASC");
	$query->execute();
	$result = $query->get_result();
	while (($row = $result->fetch_object()) !== NULL) {
		$data[] = ["id"=>$row->id, "problem"=>$row->item];	
	}
	echo json_encode($data);
}
?>