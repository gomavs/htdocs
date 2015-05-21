<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
$sql = $mysqli->prepare("UPDATE workorders SET status = 2, end = CURRENT_TIMESTAMP WHERE id = ?");
$sql->bind_param("i", $_POST["workorder"]);
$sql->execute();
?>