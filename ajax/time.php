<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
$id = $_POST["id"];
if ($_POST["start"] == 1) {
	$sql = $mysqli->prepare("INSERT INTO times (work) VALUES (?)");
	$sql->bind_param("i", $id);
	$sql->execute();
	echo "1";
} else {
	$sql = $mysqli->prepare("SELECT TIMESTAMPDIFF(MINUTE, start, NOW()) + 1 FROM times WHERE work = ? AND end IS NULL");
	$sql->bind_param("i", $id);
	$sql->execute();
	$sql->bind_result($elapsed);
	$sql->fetch();
	$sql->close();
	$sql = $mysqli->prepare("UPDATE times SET end = NOW() WHERE work = ? AND end IS NULL");
	$sql->bind_param("i", $id);
	$sql->execute();
	$hours = floor($elapsed / 60);
	$minutes = $elapsed % 60;
	echo "$hours hrs, $minutes mins";
}
?>