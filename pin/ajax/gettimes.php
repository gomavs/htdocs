<?php
require_once("../includes/dbConnect.php");
$q = "{$_GET["id"]}%";
$data = [];
$query = $db->prepare("SELECT id, item_id, machine_id, MAX(completed) as complete, MIN(start_time) as first_start, COUNT(*) as num_rows, AVG(IF(end_time = 0, UNIX_TIMESTAMP(), end_time)-start_time) as avg_time, MAX(start_time) as last_start, IF(MIN(lap) = 0, 0, MAX(end_time)) as last_end FROM times WHERE item_id = ? GROUP BY machine_id");
$query->bind_param("i", $q);
$query->execute();
$result = $query->get_result();
$count = mysqli_num_rows($result);
while (($row = $result->fetch_object()) !== NULL) {
	$data[] = ["machine_id"=>$row->machine_id, "first_start"=>$row->first_start, "rows"=>$row->num_rows, "avg_time"=>$row->avg_time, "last_start"=>$row->last_start, "last_end"=>$row->last_end, "completed"=>$row->complete];
}
echo json_encode($data);
?>