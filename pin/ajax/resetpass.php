<?php
//require_once("../includes/dbConnect.php");
require '../includes/check_login.php';
//if(isset($_GET['id'])){
if(isset($_POST['userId'])){
	$user_id = $_POST['userId'];
	$adminPass = $_POST['adminPassword'];
	$adminId = $_SESSION['user_id'];
	$data = array();
	$newpass = "";
	$query = $db->prepare("SELECT * FROM users WHERE id = ?");
	$query->bind_param("i", $adminId);
	$query->execute();
	$result = $query->get_result();
	while (($row = $result->fetch_object()) !== NULL) {
		$hash = $row->password;
		if (password_verify($adminPass, $hash)) { 
			//Admin password is verified
			$length = 8;
			$charset="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz0123456789";
			$count = strlen($charset);
			while ($length--) {
				$newpass .= $charset[mt_rand(0, $count-1)];
			}
			$hashed_password = password_hash($newpass, PASSWORD_DEFAULT);
			$query = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
			$query->bind_param("si", $hashed_password, $user_id);
			$query->execute();
			$query = $db->prepare("SELECT * FROM users WHERE id = ?");
			$query->bind_param("i", $user_id);
			$query->execute();
			$result = $query->get_result();
			$row = $result->fetch_assoc();
			$alert = "Success";
			$message = "The new password for ".$row['firstname']." ".$row['lastname'];
			
		}else{
			$alert = "Failure";
			$message = "Admin password is incorrect";
		}
	}
	
	//?userId=5&adminPassword=sbpin
	$data[] = ["alert"=>$alert, "message"=>$message, "new_pass"=>$newpass]; 
	echo json_encode($data);
	
}

?>