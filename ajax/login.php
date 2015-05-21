<?php
$nologin = 1;
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
require "PasswordHash.php";
$username = trim($_POST["username"]);
$password = trim($_POST["password"]);
if ($username == null) {
	die("Please input a username.");
} elseif ($password == null) {
	die("Please input a password.");
}
$sql = $mysqli->prepare("SELECT id, username, first, last, email, phone, extension, role, password FROM users WHERE username = ?");
$sql->bind_param("s", $username);
$sql->bind_result($id, $dbuser, $first, $last, $email, $phone, $extension, $role, $dbpass);
$sql->execute();
$sql->store_result();
$sql->fetch();
if ($sql->num_rows == 1) {
	$sql->free_result();
	$hasher = new PasswordHash(8, false);
	if ($hasher->CheckPassword($password, $dbpass)) {
		if ($_POST["remember"] == "true") {
			setcookie("remember", "1", time()+31536000, "/");
			$_SESSION["remember"] = 1;
		}
		$_SESSION["id"] = $id;
		$_SESSION["username"] = $username;
		$_SESSION["first"] = $first;
		$_SESSION["last"] = $last;
		$_SESSION["email"] = $email;
		$_SESSION["phone"] = $phone;
		$_SESSION["extension"] = $extension;
		$_SESSION["role"] = $role;
		session_regenerate_id(true);
		die("1");
	} else {
		die("The password you provided is incorrect.");
	}
} else {
	die("The username you provided is invalid.");
}
?>