<?php
require_once("../includes/dbConnect.php");
$query = $db->prepare("SELECT * FROM workcenter WHERE id = ?");
$query->bind_param("i", $_GET["id"]);
$query->execute();
$result = $query->get_result()->fetch_object();
echo json_encode($result);
?>