<?php
require_once("../includes/dbConnect.php");
$q = "{$_GET["query"]}%";
$data = [];
$query = $db->prepare("SELECT * FROM part WHERE partnumber LIKE ? LIMIT 100");
$query->bind_param("s", $q);
$query->execute();
$result = $query->get_result();
while (($row = $result->fetch_object()) !== NULL) {
	$data[] = ["data"=>$row->id, "value"=>$row->partnumber];
}
echo json_encode(["suggestions"=>$data]);
?>