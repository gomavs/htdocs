<?php
require_once("../includes/dbConnect.php");
$q = $_GET["partId"];
$level = 0;
$data = [];
$parttime = 0;
//get part number info
$query = $db->prepare("SELECT * FROM part WHERE partnumber = ?");
$query->bind_param("s", $q);
$query->execute();
$result = $query->get_result();
$row = $result->fetch_assoc();
$findThis = $row['id'];
$desc = $row['partdesc'];
//get parent part number
$query = $db->prepare("SELECT * FROM part WHERE id = ?");
$query->bind_param("i", $row['parentid']);
$query->execute();
$result = $query->get_result();
$row_cnt = $result->num_rows;
$row = $result->fetch_assoc();
if($row_cnt > 0){
	$parent = $row['partnumber'];
}else{
	$parent = "";
}
//Determine if this part number is a parent
$query= $db->prepare("SELECT * FROM part WHERE parentid = ?");
$query->bind_param("i", $findThis);
$query->execute();
$result = $query->get_result();
$row_cnt = $result->num_rows;
if($row_cnt == 0){
	$query2 = $db->prepare("SELECT times.id, times.machine_id, MIN(times.start_time) as first_start, COUNT(*) as num_rows, AVG(IF(times.end_time = 0, UNIX_TIMESTAMP(), times.end_time)-times.start_time) as avg_time, workcenter.center, workcenter.name FROM times LEFT JOIN workcenter ON times.machine_id = workcenter.id WHERE times.item_id = ? GROUP BY times.machine_id ORDER BY avg_time ASC LIMIT 1");
	$query2->bind_param("i", $findThis);
	$query2->execute();
	$result2 = $query2->get_result();
	$row_cnt2 = $result2->num_rows;
	while(($row2 = $result2->fetch_object()) !== NULL){
		$parttime = $row2->avg_time;	
		$data[] = ["Part Number"=>$q, "Part Description"=>$desc, "Parent Number"=>$parent, "Work Center"=>$row2->center, "Machine"=>$row2->name, "Date"=>"" , "Average Time"=>round($row2->avg_time)." sec" , "Parts per Hour"=>round(3600/$row2->avg_time,2), "Cycles"=>$row2->num_rows, "id"=>$findThis, "status"=>0 ];
	}
}else{
	$data[] = ["Part Number"=>$q, "Part Description"=>$desc, "Parent Number"=>$parent, "Work Center"=>"", "Machine"=>"", "Date"=>"", "Average Time"=>"", "Parts per Hour"=>"", "Cycles"=>"", "id"=>"", "status"=>1 ];
}
//echo $q." ".$desc. " ".$parent." ".$parttime." seconds </br>";
children($findThis);
function children($partNumber){
	global $db;
	global $data;
	$childData = [];
	$query= $db->prepare("SELECT * FROM part WHERE parentid = ?");
	$query->bind_param("s", $partNumber);
	$query->execute();
	$result = $query->get_result();
	while (($row = $result->fetch_object()) !== NULL) {
		$thisChildId = $row->id;
		//echo $row->partnumber." ".$row->partdesc." ".$row->parentid." ";
		$part_number = $row->partnumber;
		$part_desc = $row->partdesc;
		$parent_id = $row->parentid;
		$query1 = $db->prepare("SELECT * FROM part WHERE id = ?");
		$query1->bind_param("i", $parent_id);
		$query1->execute();
		$result1 = $query1->get_result();
		$row1 = $result1->fetch_assoc();
		$parent_number = $row1['partnumber'];
		$query1 = $db->prepare("SELECT * FROM part WHERE parentid = ?");
		$query1->bind_param("i", $thisChildId);
		$query1->execute();
		$result1 = $query1->get_result();
		$row_cnt1 = $result1->num_rows;
		If($row_cnt1 > 0){
			//echo "mama </br>";
			$data[] = ["Part Number"=>$part_number, "Part Description"=>$part_desc, "Parent Number"=>$parent_number, "Work Center"=>"", "Machine"=>"", "Date"=>"", "Average Time"=>"", "Parts per Hour"=>"", "Cycles"=>"", "id"=>$thisChildId, "status"=>1 ];
		}else{
			//echo "child ";
			$workCenter = "";
			$machine = "";
			$averageTime = "";
			$startDate = "";
			$cycles = "";
			$partsHour = "";
			$query2 = $db->prepare("SELECT times.id, times.machine_id, MIN(times.start_time) as first_start, COUNT(*) as num_rows, AVG(IF(times.end_time = 0, UNIX_TIMESTAMP(), times.end_time)-times.start_time) as avg_time, workcenter.center, workcenter.name FROM times LEFT JOIN workcenter ON times.machine_id = workcenter.id WHERE times.item_id = ? GROUP BY times.machine_id ORDER BY avg_time ASC LIMIT 1");
			$query2->bind_param("i", $row->id);
			$query2->execute();
			$result2 = $query2->get_result();
			$row_cnt2 = $result2->num_rows;
			while(($row2 = $result2->fetch_object()) !== NULL){
				//echo $row2->first_start." </br>";
				$workCenter = $row2->center;
				$machine = $row2->name;
				$averageTime = round($row2->avg_time)." sec";
				$cycles = $row2->num_rows;
				$end_time =$row2->first_start;
				$startDate = date("M d, Y",$end_time);
				$partsHour = round(3600/$averageTime, 2);
			}
			$data[] = ["Part Number"=>$part_number, "Part Description"=>$part_desc, "Parent Number"=>$parent_number, "Work Center"=>$workCenter, "Machine"=>$machine, "Date"=>$startDate , "Average Time"=>$averageTime, "Parts per Hour"=>$partsHour, "Cycles"=>$cycles, "id"=>$thisChildId, "status"=>0];
			if($row_cnt2 == 0){
				//echo "</br>";
			}
		}
		children($thisChildId);
	}
}
echo json_encode($data);
?>