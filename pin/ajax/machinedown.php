<?php
require_once("../includes/dbConnect.php");
if(isset($_GET['id']) AND isset($_GET['down'])){
$query = $db->prepare("UPDATE workorder SET down = ? WHERE id = ?");
$query->bind_param("ii", $_GET['down'], $_GET['id']);
$query->execute();	
}

?>