<?php
$auth = 50;
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
$sql = $mysqli->prepare("UPDATE workorders SET status = 1 WHERE id = ?");
$sql->bind_param("i", $_POST["id"]);
$sql->execute();
echo "1";
?>