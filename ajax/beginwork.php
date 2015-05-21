<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
$workorder = $_POST["workorder"];
$begin = $_POST["begin"];
if ($begin == 1) {
	$sql = $mysqli->prepare("INSERT INTO work (workorder, summary, description, worker, status) VALUES (?, NULL, NULL, ?, 0)");
	$sql->bind_param("is", $workorder, $_SESSION["id"]);
	$sql->execute();
	echo intval($mysqli->insert_id);
} else {
	$sql = $mysqli->prepare("UPDATE WORK SET summary = ?, description = ?, status = 1 WHERE workorder = ? AND worker = ? AND status = 0");
	$sql->bind_param("ssii", $_POST["summary"], $_POST["desc"], $workorder, $_SESSION["id"]);
	$sql->execute();
}
?>