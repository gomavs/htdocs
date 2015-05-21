<?php
require_once("../includes/dbConnect.php");

$start_time = strtotime($_GET['starttime']);
$end_time = strtotime($_GET['endtime']);
if($_GET['users']){
	$user_list = explode("-", $_GET['users']);
}
$total_users = count($user_list);
$time_diff = $end_time - $start_time;
if($time_diff < 86400*15){//less than 16 days = days
	$multiplier = 86400;
	$skip_by = "P1D";
}elseif($time_diff >= 86400*15 && $time_diff < 86400*30){// between 15 days and 1 month = weeks
	$multiplier = 604800 ;
	$skip_by = "P1W";
}elseif($time_diff >= 86400*30 && $time_diff < 86400*365){//between 1 month and 1 year = months
	$multiplier = 2592000;
	$skip_by = "P1M";
}else{//greater than a year = years
	$multiplier = 31536000;
	$skip_by = "P1Y";
}
$row_cnt = 0;
$data = [];
$startfrom = date( "Y-m-d", $start_time );
$goto = date("Y-m-d", $end_time);
$begin = new DateTime( $startfrom );
$end = new DateTime( $goto );
$end = $end->modify( '+1 day' );
$interval = new DateInterval($skip_by);
$daterange = new DatePeriod($begin, $interval ,$end);
$query = $db->prepare("SELECT * FROM times WHERE start_time >= ? AND end_time < ? AND completedby = ? ORDER BY end_time ASC");
foreach($daterange as $date){
	$count_data = [];
	$i = 0;
	$start = $date->format("Ymd");
	$start =  strtotime($start);
	$end = $start + $multiplier;
	foreach($user_list as $uid){
		$query->bind_param("iii", $start, $end, $uid);
		$query->execute();
		$result = $query->get_result();
		if(is_null($result->num_rows)){
			$row_cnt = 0;
		}else{
			$row_cnt = $result->num_rows;
		}
		$count_data[$i] = $row_cnt;
		$i++;
	}
	$i--;
	$theday = date( "Y-m-d", $start );
	switch($i){
		case 0: $data[] = (object)array("day"=>$theday, "a"=>$count_data[0]); break;
		case 1: $data[] = (object)array("day"=>$theday, "a"=>$count_data[0], "b"=>$count_data[1]); break;
		case 2: $data[] = (object)array("day"=>$theday, "a"=>$count_data[0], "b"=>$count_data[1], "c"=>$count_data[2]); break;
	}
}

echo json_encode($data);

?>