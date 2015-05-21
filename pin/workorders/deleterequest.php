<?php
require '../includes/check_login.php';
//require_once '../includes/dbConnect.php';

if(isset($_GET['id'])){
	$requestId = $_GET['id'];
	$query = $db->prepare("SELECT * FROM workrequest WHERE id = ?");
	$query->bind_param("i", $requestId);
	$query->execute();
	$result = $query->get_result();
	$row = $result->fetch_assoc();
	$requestedBy = $row['requestedBy'];
	$deletedBy = $_SESSION['user_id'];
	$date = new DateTime();
	$timestamp = $date->getTimestamp();
	$viewed = 0;
	$type = 1;
	$message = " ";
	$query = $db->prepare("UPDATE workrequest SET accepted = 2 WHERE id = ? ");
	$query->bind_param("i", $requestId);
	$query->execute();
	
	$query = "INSERT INTO messages (msgTo, msgFrom, date, viewed, message, type) VALUES ('$requestedBy', '$deletedBy', '$timestamp', '$viewed', '$message', '$type')";
	$db->query($query);
	
	header('location: openrequests.php');
}