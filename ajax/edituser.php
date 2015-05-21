<?php
$auth = 30;
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
$firstname = ucfirst(trim($_POST["firstname"]));
$lastname = ucfirst(trim($_POST["lastname"]));
$email = trim($_POST["email"]);
$phone = trim($_POST["phone"]);
$extension = trim($_POST["extension"]);
$role = trim($_POST["role"]);
$workcenter = trim($_POST["workcenter"]);
$id = $_POST["id"];
if ($firstname == null) {
	die("Please input a first name.");
}
if ($lastname == null) {
	die("Please input a last name.");
}
if ($role == null) {
	die("Please select a role.");
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
$sql = $mysqli->prepare("UPDATE users SET first = ?, last = ?, email = ?, phone = ?, extension = ?, role = ?, workcenter = ? WHERE id = ?");
$sql->bind_param("ssssiiii", $firstname, $lastname, $email, $phone, $extension, $role, $workcenter, $id);
$sql->execute();
die("1");
?>