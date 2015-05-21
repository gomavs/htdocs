<?php
$auth = 50;
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
$issue = $_POST["issue"];
$urgency = $_POST["urgency"];
$assignto = $_POST["assignto"];
$date = strtotime($_POST["date"]);
$esthours = $_POST["esthours"];
$notes = htmlspecialchars($_POST["notes"]);
$id = $_POST["id"];
if ($issue == null) {
	die("Please select an issue type.");
}
if ($urgency == null) {
	die("Please select the urgency of this issue.");
}
if ($assignto == null) {
	die("Please assign somebody to this order.");
}
if ($notes == null) {
	$notes = null;
}
if ($_POST["reject"] == 0) {
	if ($id == null) {
		//create a new request
	} else {
		$res = $mysqli->query("SELECT MAX(orderid) FROM workorders")->fetch_row();
		$orderid = $res[0] + 1;
		$sql = $mysqli->prepare("UPDATE workorders SET orderid = ?, assigned = ?, due = FROM_UNIXTIME(?), issue = ?, urgency = ?, notes = ?, status = 1, estimate = ? WHERE id = ?");
		$sql->bind_param("iissssii", $orderid, $assignto, $date, $issue, $urgency, $notes, $esthours, $id);
		$sql->execute();
	}
} else {
	if ($id == null) {
		die("Please input an ID.");
	}
	$sql = $mysqli->prepare("UPDATE workorders SET issue = ?, urgency = ?, notes = ?, status = 2 WHERE id = ?");
	$sql->bind_param("sssi", $issue, $urgency, $notes, $id);
	$sql->execute();
}
die("1");
?>