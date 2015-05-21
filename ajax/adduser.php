<?php
$auth = 30;
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
require "PasswordHash.php";
$firstname = ucfirst(trim($_POST["firstname"]));
$lastname = ucfirst(trim($_POST["lastname"]));
$email = trim($_POST["email"]);
$phone = trim($_POST["phone"]);
$extension = trim($_POST["extension"]);
$role = trim($_POST["role"]);
$workcenter = trim($_POST["workcenter"]);
$errors = array();
if ($firstname == null) {
	$errors[] = array("field"=>"firstname", "error"=>"Please input a first name.");
}
if ($lastname == null) {
	$errors[] = array("field"=>"lastname", "error"=>"Please input a last name.");
}
if ($role == null) {
	$errors[] = array("field"=>"role", "error"=>"Please select a role.");
}
if (count($errors) != 0) {
	die(json_encode($errors));
}
$sql = $mysqli->prepare("SELECT id FROM users WHERE first = ? AND last = ?");
$sql->bind_param("ss", $firstname, $lastname);
$sql->execute();
$sql->store_result();
$dupes = $sql->num_rows;
$username = $firstname . $lastname;
if ($dupes != 0) {
	$username .= $dupes + 1;
}
if ($extension == null) {
	$extension = null;
}
if ($phone == null) {
	$phone = null;
} else {
	$phone = str_pad(substr(preg_replace("/\D/", "", $phone), 0, 10), 10, "0");
}
if ($workcenter == null) {
	$workcenter = null;
}
$hasher = new PasswordHash(8, false);
$password = substr(md5(uniqid(rand(), true)), 0, 8);
$hashed = $hasher->HashPassword($password);
$sql = $mysqli->prepare("INSERT INTO users (username, first, last, email, phone, extension, role, workcenter, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$sql->bind_param("sssssisis", $username, $firstname, $lastname, $email, $phone, $extension, $role, $workcenter, $hashed);
$sql->execute();
die("{\"success\":1,\"username\":\"$username\",\"password\":\"$password\"}");
?>