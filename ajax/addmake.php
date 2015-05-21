<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
$name = htmlspecialchars($_POST["addmakename"]);
if ($name == null) {
	die("0");
}
$sql = $mysqli->prepare("INSERT INTO makes (name) VALUES (?)");
$sql->bind_param("s", $name);
$sql->execute();
die(strval($mysqli->insert_id));
?>