<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
$start = strtotime($_POST["timestart"]);
$end = strtotime($_POST["timestop"]);
$elapsed = floor(($end - $start) / 60);
$hours = floor($elapsed / 60);
$minutes = $elapsed % 60 + 1;
if (!$start) {
	die(json_encode(array("error"=>"Invalid start time.")));
} elseif (!$end) {
	die(json_encode(array("error"=>"Invalid stop time.")));
}
$sql = $mysqli->prepare("UPDATE times SET start = FROM_UNIXTIME(?), end = FROM_UNIXTIME(?) WHERE id = ?");
$sql->bind_param("iii", $start, $end, $_POST["timeid"]);
$sql->execute();
die(json_encode(array("elapsed"=>"$hours hrs, $minutes mins")));
?>