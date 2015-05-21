<?php
require_once("../includes/dbConnect.php");

$data = [];
$query = $db->prepare("SELECT * FROM workcenter ORDER BY center ASC");
$query->execute();
$result = $query->get_result();
while (($row = $result->fetch_object()) !== NULL) {
	switch($row->inservice){
		case 0: $inservice = "No"; break;
		case 1: $inservice = "Yes"; break;
		default : $inserice = "No"; 
	}
	switch($row->type){
		case 1: $type = "Machining"; break;
		case 2: $type = "Edgebanding"; break;
		case 3: $type = "BAZ"; break;
		case 4: $type = "Router"; break;
		case 5: $type = "Saw"; break;
		case 6: $type = "Conveyor"; break;
		case 7: $type = "Doweling"; break;
		case 8: $type = "Clamp"; break;
		case 20: $type = "Other"; break;
		default : $type = "Unknown";
	}
	$data[] = ["Work Center"=>$row->center, "Name"=>$row->name, "Make"=>$row->make, "Model"=>$row->model, "Serial"=>$row->serial, "Year"=>$row->year, "Type"=>$type, "In Service"=>$inservice, "ID"=>$row->id];
}
echo json_encode($data);


?>