<?php
require_once("../includes/dbConnect.php");
$q = "{$_GET["id"]}%";
$machineArray = array();
$data = [];
$machine_id = "";
$query = $db->prepare("SELECT * FROM times WHERE item_id = ?");
$query->bind_param("s", $q);
$query->execute();
$result = $query->get_result();
while (($row = $result->fetch_object()) !== NULL) {
	if(!in_array($row->machine_id, $machineArray)){
		$machineArray[] = $row->machine_id;
		$query = $db->prepare("SELECT * FROM times WHERE item_id = ? AND machine_id = ?");
		$query1->bind_param("s", $q, $row->machine_id);
		$query1->execute();
		$result1 = $query1->get_result();
		$count = mysqli_num_rows($result1);
		
	}
	
}

$data[] = ["id"=>$row->id, "machine_id"=>$row->machine_id, "start_time"=>$row->start_time, "end_time"=>$row->end_time, "completed"=>$row->completed];

echo json_encode($data);
?>