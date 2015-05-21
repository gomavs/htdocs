<?php
$auth = 50;
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
$type = $_POST["type"];
$make = $_POST["make"];
$model = $_POST["model"];
$serial = $_POST["serial"];
$year = $_POST["year"];
$workcenter = $_POST["workcenter"];
$nick = $_POST["nick"];
$appliancetype = $_POST["appliancetype"];
$errors = array();
if ($type == "Machine") {
	if ($make == null) {
		$errors[] = array("field"=>"make", "error"=>"Please select a make.");
	}
	if ($model == null) {
		$errors[] = array("field"=>"model", "error"=>"Please input a model.");
	}
	if ($serial == null) {
		$errors[] = array("field"=>"serial", "error"=>"Please input a serial number.");
	}
	if ($year == null) {
		$errors[] = array("field"=>"year", "error"=>"Please select a year.");
	}
	if ($workcenter == null) {
		$errors[] = array("field"=>"workcenter", "error"=>"Please input a work center.");
	}
	$appliancetype = null;
} elseif ($type == "Appliance") {
	if ($appliancetype == null) {
		$errors[] = array("field"=>"workcenter", "error"=>"Please select an appliance type.");
	}
	$make = null;
	$model = null;
	$serial = null;
	$year = null;
	$workcenter = null;
} else {
	$errors[] = array("field"=>"workcenter", "error"=>"Please input a type.");
}
if ($nick == null) {
	$errors[] = array("field"=>"nick", "error"=>"Please input a nick name.");
}
if (count($errors) != 0) {
	die(json_encode($errors));
}
$sql = $mysqli->prepare("INSERT INTO machines (type, make, model, serial, year, workcenter, appliancetype, nick) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$sql->bind_param("sissiiss", $type, $make, $model, $serial, $year, $workcenter, $appliancetype, $nick);
$sql->execute();
die("{\"success\":1}");
?>