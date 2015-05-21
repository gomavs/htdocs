<?php
require_once("../includes/dbConnect.php");
//if(isset($_GET['id'])){
	$user_id = $_GET['id'];
	$length = 8;
	$charset="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz0123456789";
	$newpass = '';
    $count = strlen($charset);
    while ($length--) {
		$newpass .= $charset[mt_rand(0, $count-1)];
    }
	$hashed_password = password_hash($newpass, PASSWORD_DEFAULT);
	$query = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
	$query->bind_param("si", $hashed_password, $user_id);
	$query->execute();
	
	echo $newpass;
//}
?>