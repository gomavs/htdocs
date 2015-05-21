<?php
require_once("../includes/dbConnect.php");

$data = [];
$query = $db->prepare("SELECT * FROM users ORDER BY lastname ASC");
$query->execute();
$result = $query->get_result();
while (($row = $result->fetch_object()) !== NULL) {
	switch($row->active){
		case 0: $active = "No"; break;
		case 1: $active = "Yes"; break;
		default : $active = "No"; 
	}

	$data[] = ["First Name"=>$row->firstname, "Last Name"=>$row->lastname, "Email Address"=>$row->email, "Mobil Number"=>$row->cell, "Permissions"=>$row->permissions, "Active"=>$active, "ID"=>$row->id];
}
echo json_encode($data);


?>