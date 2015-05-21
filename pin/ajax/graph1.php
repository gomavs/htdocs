<?php
require_once("../includes/dbConnect.php");

$start_time = strtotime($_GET['starttime']);
$end_time = strtotime($_GET['endtime']);
$time_diff = $end_time - $start_time;
$multiplier = 86400;
$skip_by = "P1D";
/*
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
}*/
$row_cnt = 0;
$data = [];
$startfrom = date( "Y-m-d", $start_time );
$goto = date("Y-m-d", $end_time);
$begin = new DateTime( $startfrom );
$end = new DateTime( $goto );
$end = $end->modify( '+1 day' ); 

$interval = new DateInterval($skip_by);
$daterange = new DatePeriod($begin, $interval ,$end);
foreach($daterange as $date){
    $start = $date->format("Ymd");
	//echo $start;
	$start =  strtotime($start);
	$end = $start + $multiplier;
	$completed = 1;
	$query = $db->prepare("SELECT * FROM times WHERE end_time >= ? AND end_time < ? AND completed = ? ORDER BY end_time ASC");
	$query->bind_param("iii", $start, $end, $completed);
	$query->execute();
	$result = $query->get_result();
	$row_cnt = $result->num_rows;
	
	switch($skip_by){
		case "PT1H" : $theday = $start; break;
		default : $theday = date( "Y-m-d", $start );
	}
	
	$data[] = (object)array("day"=>$theday, "value"=>$row_cnt);
}

echo json_encode($data);

?>