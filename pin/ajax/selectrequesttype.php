<?php
require_once("../includes/dbConnect.php");


$data = [];
$query = $db->prepare("SELECT * FROM workcenter WHERE inservice = 1 ORDER BY center ASC");
$query->execute();
$result = $query->get_result();
while (($row = $result->fetch_object()) !== NULL) {

	$data[] = ["id"=>$row->id, "center"=>$row->center, "name"=>$row->name];
}
echo json_encode($data);


?>