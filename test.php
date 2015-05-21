<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
require "PasswordHash.php";
$hasher = new PasswordHash(8, false);
echo($hasher->HashPassword("password"));
?>