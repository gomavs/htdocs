<?php
require_once("../includes/dbConnect.php");
$email = $_GET['email'];

$query = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
$query->bind_param("s", $q);
$query->execute();
$result = $query->get_result()->fetch_object();
if($result == 0){
	$data = "true";
}elso{
	$data = "false";
}
echo json_encode(array('valid' => $data,));
?>