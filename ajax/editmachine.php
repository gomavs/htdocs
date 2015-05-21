<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
$make = $_POST["make"];
$model = $_POST["model"];
$serial = $_POST["serial"];
$year = $_POST["year"];
$workcenter = $_POST["workcenter"];
$nick = $_POST["nick"];
$id = $_POST["id"];
if ($make == null) {
	die("Please select a make.");
}
if ($model == null) {
	die("Please input a model.");
}
if ($serial == null) {
	die("Please input a serial number.");
}
if ($year == null) {
	die("Please select a year.");
}
if ($workcenter == null) {
	die("Please input a work center.");
}
if ($nick == null) {
	die("Please input a nick name.");
}
$sql = $mysqli->prepare("UPDATE machines SET make = ?, model = ?, serial = ?, year = ?, workcenter = ?, nick = ? WHERE id = ?");
$sql->bind_param("issiisi", $make, $model, $serial, $year, $workcenter, $nick, $id);
$sql->execute();
die("1");
?>