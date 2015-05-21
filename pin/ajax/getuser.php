<?php
require_once("../includes/dbConnect.php");
/*
$q = "{$_GET["id"]}%";
$data = [];
$query = $db->prepare("SELECT * FROM users WHERE id = ?");
$query->bind_param("s", $q);
$query->execute();
$result = $query->get_result();
while (($row = $result->fetch_object()) !== NULL) {
	
	$data[] = ["id"=>$row->id, "firstname"=>$row->firstname, "lastname"=>$row->lastname, "email"=>$row->email, "mobile"=>$row->cell];
}
echo json_encode($data);
*/

$query = $db->prepare("SELECT * FROM users WHERE id = ?");
$query->bind_param("i", $_GET["id"]);
$query->execute();
$result = $query->get_result()->fetch_object();
echo json_encode($result);
?>