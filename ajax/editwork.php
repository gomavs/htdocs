<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
$sql = $mysqli->prepare("UPDATE work SET summary = ?, description = ?, `date` = concat(?, time(`date`)) WHERE id = ?");
$editdate = DateTime::createFromFormat("m/d/Y", $_POST["editdate"]);
$editdate = $editdate->format("Y-m-d ");
$sql->bind_param("sssi", $_POST["editsummary"], $_POST["editdescription"], $editdate, $_POST["workid"]);
$sql->execute();
die(json_encode(array("stuff"=>($_POST["editsummary"] . " | " . $_SESSION["first"] . " " . $_SESSION["last"] . " | " . date("g:i A")))));
?>