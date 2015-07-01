<?php
require_once("../includes/dbConnect.php");
$email = $_GET['email'];
$data = array();
$query = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();
$row_cnt = mysqli_num_rows($result);

if($row_cnt == 0){
	$x = "true";
}else{
	$x = "false";
}
$data[] = ["valid"=>$x];
echo json_encode($data);
?>