<?php
require_once("../includes/dbConnect.php");
$userId = $_GET['userid'];
$alertId = $_GET['alertid'];
$data = [];
$query = $db->prepare("DELETE FROM messages WHERE id = ? AND msgTo = ?");
$query->bind_param('ii', $alertId, $userId);
$query->execute();
$query = $db->prepare("SELECT * FROM messages WHERE msgTo = ? AND viewed = 0");
$query->bind_param("i", $userId);
$query->execute();
$result = $query->get_result();
$data[] = ["alerts"=>mysqli_num_rows($result)];
echo json_encode($data);
?>