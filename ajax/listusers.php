<?php
$auth = 50;
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
$start = 0;
$length = 10;
if (isset($_GET["start"])) {
	$start = $_GET["start"];
}
if (isset($_GET["length"])) {
	$length = $_GET["length"];
}
$res = $mysqli->query("SELECT id, username, first, last, email, phone, extension, role, workcenter FROM users LIMIT $start, $length");
echo json_encode($res->fetch_all());
?>