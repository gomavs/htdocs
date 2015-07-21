<?php
session_start();
require_once 'dbConnect.php';
$folders = explode ('/', $_SERVER['PHP_SELF']);
$foler_count = count($folders);
if($foler_count > 3){
	$location = "location: ../login.php";
}else{
	$location = "location: login.php";
}
$errors = array();
if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_pass'])) {
	header($location);
	
} else {
    if(!get_magic_quotes_gpc()) {
        $_SESSION['user_email'] = addslashes($_SESSION['user_email']);
    }
	$query = $db->prepare("SELECT * FROM users WHERE email = ?");
	$query->bind_param("s", $_SESSION['user_email']);
	$query->execute();
	$result = $query->get_result();
	$row = $result->fetch_assoc();
	$num_rows = $result->num_rows;
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
	}	
	
	if($num_rows != 1) {
        unset($_SESSION['user_email']);
        unset($_SESSION['user_pass']);
        // kill incorrect session variables.
    }
	$user_password = stripslashes($row['password']);
	$active = $row['active'];
	if ($active == 1) {
		$hash = $row['password'];
		if (time() - $_SESSION['time'] < 1800 && password_verify($_SESSION['user_pass'], $hash)) {
			$_SESSION['user_id'] = $row['id'];
			$_SESSION['user_first_name'] = $row['firstname'];
			$_SESSION['user_last_name'] = $row['lastname'];
			$_SESSION['user_email'] = $row['email'];
			$_SESSION['user_auth_level'] = $row['authlevel'];
			$_SESSION['user_permissions'] = $row['permissions'];
			$_SESSION['user_authTS'] = $row['authTS'];
			$_SESSION['user_authWO'] = $row['authWO'];
			$_SESSION['logged'] = 1;
			$_SESSION['home_page'] = $row['homePage'];
			$_SESSION['time'] = time();
		}else{
			session_destroy();
			header($location);
		}
	}
	unset($row['password']);
}
?>