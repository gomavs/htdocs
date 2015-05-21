<?php
require_once("../includes/dbConnect.php");

$data = [];
$test = 1;
$query = $db->prepare("SELECT * FROM users WHERE active = ? ORDER BY rand() LIMIT 3");
$query->bind_param("i", $test);
$query->execute();
$result = $query->get_result();
while (($row = $result->fetch_object()) !== NULL) {
	$data[] = ["id"=>$row->id, "firstname"=>$row->firstname, "lastname"=>$row->lastname];
}

echo json_encode($data);
?>