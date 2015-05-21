<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
$name = htmlspecialchars($_POST["problemname"]);
if ($name == null) {
	die("0");
}
if ($_POST["problemtype"] == "Machine") {
	$type = 0;
} else {
	$type = 1;
}
$sql = $mysqli->prepare("INSERT INTO problems (name, type) VALUES (?, ?)");
$sql->bind_param("si", $name, $type);
$sql->execute();
die("1");
?>