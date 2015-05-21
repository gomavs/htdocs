<?php
require_once("../includes/dbConnect.php");
$data = array();
$auth_level = 5;
$query = $db->prepare("SELECT * FROM users WHERE authlevel = ?");
$query->bind_param("i", $auth_level);
$query->execute();
$result = $query->get_result();
while (($row = $result->fetch_object()) !== NULL) {

	$data[] = ["firstname"=>$row->firstname, "lastname"=>$row->lastname];
}
echo json_encode($data);
?>