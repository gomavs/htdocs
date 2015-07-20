<?php
require_once("../includes/dbConnect.php");
if(isset($_GET['id']) AND isset($_GET['problem'])){
	$problem = $_GET['problem'];
	$query = $db->prepare("SELECT workrequest.workTypeId, workrequest.itemId FROM workorder LEFT JOIN workrequest ON workorder.workRequestId = workrequest.id WHERE workorder.id = ?");
	$query->bind_param("i", $_GET['id']);
	$query->execute();
	$result = $query->get_result();
	$row = $result->fetch_assoc();
	$workTypeId = intval($row['workTypeId']);
	$itemId = intval($row['itemId']);
	echo $_GET['id']."</br>";
	echo $_GET['problem']."</br>";
	echo $workTypeId."</br>";
	echo $itemId;
	$query = $db->prepare("SELECT id FROM problems WHERE problemType = ? AND problemId = ? AND itemId = ?");
	$query->bind_param("iii", $workTypeId, $_GET['problem'], $itemId);
	$query->execute();
	$result = $query->get_result();
	$row = $result->fetch_assoc();
	$query = $db->prepare("UPDATE workorder SET issue = ? WHERE id = ?");
	$query->bind_param("ii", $row['id'], $_GET['id']);
	$query->execute();	
}

?>