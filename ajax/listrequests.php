<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
$start = 0;
if (isset($_GET["start"])) {
	$start = $_GET["start"];
}
$res = $mysqli->query("SELECT workorders.id, CONCAT(users.first, \" \", users.last), workorders.time, workorders.worktype, machines.nick, workorders.issue, workorders.urgency FROM workorders LEFT JOIN users ON users.id = workorders.requester LEFT JOIN machines ON workorders.item = machines.id LIMIT $start, 15");
echo json_encode($res->fetch_all());
?>