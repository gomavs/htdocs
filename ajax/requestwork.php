<?php
$auth = 90;
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
$worktype = $_POST["worktype"];
$machine = $_POST["machine"];
$appliance = $_POST["appliance"];
$urgency = $_POST["urgency"];
$notes = htmlspecialchars($_POST["notes"]);
$item = null;

$errors = array();
if ($worktype == "Machine") {
	if ($machine == null) {
		$errors[] = array("field"=>"machine", "error"=>"Please select a machine.");
	} else {
		$item = $machine;
		$issue = $_POST["issuem"];
	}
} elseif ($worktype == "Appliance") {
	if ($appliance == null) {
		$errors[] = array("field"=>"appliance", "error"=>"Please select an appliance.");
	} else {
		$item = $appliance;
		$issue = $_POST["issuea"];
	}
	
} else {
	$errors[] = array("field"=>"worktype", "error"=>"Please select a work type.");
}
if ($urgency == null) {
	$errors[] = array("field"=>"urgency", "error"=>"Please select the urgency of this problem.");
}
if (count($errors) != 0) {
	die(json_encode($errors));
}
if ($notes == null) {
	$notes = null;
}
$res = $mysqli->query("SELECT MAX(requestid) FROM workorders")->fetch_row();
$requestid = $res[0] + 1;
$sql = $mysqli->prepare("INSERT INTO workorders (requestid, requester, worktype, item, issue, urgency, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
$sql->bind_param("iisisss", $requestid, $_SESSION["id"], $worktype, $item, $issue, $urgency, $notes);
$sql->execute();
die("{\"success\":1}");
?>