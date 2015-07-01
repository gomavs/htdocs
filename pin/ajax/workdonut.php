<?php_egg_logo_guid
require_once("../includes/dbConnect.php");
$data = [];
$lastWeek = strtotime("-1 week");
$query = $db->prepare("SELECT workcenter.id, workcenter.center, workcenter.name, COUNT(workorder.id) as count FROM workorder LEFT JOIN workcenter ON workorder.workcenterId = workcenter.id WHERE workorder.startDate >= ? AND workorder.status=1 GROUP BY workcenter.id ORDER BY count DESC LIMIT 5 ");
$query->bind_param("i", $lastWeek);
$query->execute();
$result = $query->get_result();
while (($row = $result->fetch_object()) !== NULL) {
	$data[] = [
		"id"=>$row->id, 
		"center"=>$row->center, 
		"name"=>$row->name
		];
}

echo json_encode($data);
?>