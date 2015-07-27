<?php
require_once("../includes/dbConnect.php");

$query = $db->prepare("SELECT users.*, escalation.tier FROM users LEFT JOIN escalation ON users.id = escalation.userID WHERE users.id = ?");
$query->bind_param("i", $_GET["id"]);
$query->execute();
$result = $query->get_result()->fetch_object();
echo json_encode($result);
?>