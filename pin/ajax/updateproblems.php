<?php
require_once("../includes/dbConnect.php");
$data = [];
$data1 = [];
$data2 = [];
$data3 = [];
if(isset($_POST['problemId'])){
	$problemId = $_POST['problemId'];
	//Find items already in database and are active then fill array data1
	$query = $db->prepare("SELECT * FROM problems WHERE problemId = ? AND active = 1");
	$query->bind_param("i", $problemId);
	$query->execute();
	$result = $query->get_result();
	while (($row = $result->fetch_object()) !== NULL) {
		$data1[] = $row->machineId;
	}
	//Find the items that have been checked and fill array data2
	if(!empty($_POST['check_list'])){
		foreach($_POST['check_list'] as $value){
			$value = intval($value);
			$data2[] = $value;
		}
	}
	//Find items already in database and are not active then fill array data3
	$query = $db->prepare("SELECT * FROM problems WHERE problemId = ? AND active = 0");
	$query->bind_param("i", $problemId);
	$query->execute();
	$result = $query->get_result();
	while (($row = $result->fetch_object()) !== NULL) {
		$data3[] = $row->machineId;
	}
	$insert_diff = array_diff($data2, $data1);
	$delete_diff = array_diff($data1, $data2);
	//Deactivate all entries for the selected problem
	foreach($delete_diff as $check){
		$check = intval($check);
		$query = $db->prepare("UPDATE problems SET active = 0 WHERE problemId = ? AND machineId = ?");
		$query->bind_param("ii", $problemId, $check);
		$query->execute();
	}
	foreach($insert_diff as $value){
		$value = intval($value);
		if(in_array($value, $data3)){
			$data[] = $value;
			$query = $db->prepare("UPDATE problems SET active = 1 WHERE problemId = ? AND machineId = ?");
			$query->bind_param("ii", $problemId, $value);
			$query->execute();
		}else{
			mysqli_query($db,"INSERT INTO problems (problemId, machineId, active) VALUES ('$problemId', '$value', '1')");
		}
	}
	 $data[] = $problemId;
}
echo json_encode($data);
?>