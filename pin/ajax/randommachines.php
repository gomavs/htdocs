<?php
require_once("../includes/dbConnect.php");

$data = [];
$test = 1;
$query = $db->prepare("SELECT * FROM workcenter WHERE type = ? AND inservice = ? ORDER BY rand() LIMIT 3");
$query->bind_param("ii", $test, $test);
$query->execute();
$result = $query->get_result();
while (($row = $result->fetch_object()) !== NULL) {
	$data[] = ["id"=>$row->id, "center"=>$row->center, "name"=>$row->name];
	//echo $row->id."<br>";
}

echo json_encode($data);
?>