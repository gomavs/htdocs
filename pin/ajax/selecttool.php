<?php
require_once("../includes/dbConnect.php");
$data = [];
$query = $db->prepare("SELECT * FROM toolstype ORDER BY item ASC");
$query->execute();
$result = $query->get_result();
while (($row = $result->fetch_object()) !== NULL) {

	$data[] = ["id"=>$row->id, "items"=>$row->item];
}
echo json_encode($data);


?>