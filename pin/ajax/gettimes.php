<?php
require_once("../includes/dbConnect.php");
$q = "{$_GET["id"]}%";
$data = [];
$query = $db->prepare("SELECT * FROM times WHERE item_id = ?");
$query->bind_param("s", $q);
$query->execute();
$result = $query->get_result();
$count = mysqli_num_rows($result);
while (($row = $result->fetch_object()) !== NULL) {

	$data[] = ["id"=>$row->id, "machine_id"=>$row->machine_id, "start_time"=>$row->start_time, "end_time"=>$row->end_time, "completed"=>$row->completed];
}
echo json_encode($data);
?>