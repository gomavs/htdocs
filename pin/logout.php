<?php
session_start();
$_SESSION = array(); // reset session array
session_destroy();
$folders = explode ('/', $_SERVER['PHP_SELF']);
$foler_count = count($folders);
if($foler_count > 3){
	$relative = "../";
}else{
	$relative = "";
}
$location = "location: ".$relative."login.php";
header($location);

?>