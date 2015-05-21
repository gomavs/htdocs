<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
require "PasswordHash.php";
$email = trim($_POST["email"]);
$phone = trim($_POST["phone"]);
$extension = trim($_POST["extension"]);
$password = trim($_POST["password"]);
$new = trim($_POST["newpass1"]);
$confirmnew = trim($_POST["newpass2"]);
$hasher = new PasswordHash(8, false);
$errors = array();
$sql = $mysqli->prepare("SELECT password FROM users WHERE id = ?");
$sql->bind_param("i", $_SESSION["id"]);
$sql->bind_result($dbpassword);
$sql->execute();
$sql->store_result();
$sql->fetch();
if (!$hasher->CheckPassword($password, $dbpassword)) {
	$errors[] = array("field"=>"password", "error"=>"Your password is incorrect.");
}
if ($new != null) {
	if (strlen($new) > 50 || strlen($new) < 3) {
		$errors[] = array("field"=>"newpass1", "error"=>"Passwords must be between 3 and 50 characters in length.");
	} else {
		$newpass = $hasher->HashPassword($new);	
	}
	if ($new != $confirmnew) {
		$errors[] = array("field"=>"newpass2", "error"=>"Passwords do not match.");
	}
} else {
	$newpass = $dbpassword;
}
if ($email == null) {
	$errors[] = array("field"=>"email", "error"=>"Please input an email address.");
}
if (count($errors) != 0) {
	die(json_encode($errors));
}
if ($extension == null) {
	$extension = null;
}
if ($phone == null) {
	$phone = null;
} else {
	$phone = str_pad(substr(preg_replace("/\D/", "", $phone), 0, 10), 10, "0");
}
$sql->close();
$sql = $mysqli->prepare("UPDATE users SET email = ?, phone = ?, extension = ?, password = ? WHERE id = ?");
$sql->bind_param("ssisi", $email, $phone, $extension, $newpass, $_SESSION["id"]);
$sql->execute();
$_SESSION["email"] = $email;
$_SESSION["phone"] = $phone;
$_SESSION["extension"] = $extension;
die("1");
?>