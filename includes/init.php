<?php
error_reporting(E_ALL);
if (isset($_COOKIE["remember"])) {
	ini_set('session.cookie_lifetime', 1209600);
}
session_start();
if (!isset($_SESSION['id'])) {
	if (!isset($nologin)) {
		header("Location:/login.php");
	}
} else {
	if (isset($nologin)) {
		header("Location:/");
	}
	$logged_in = 1;
}
set_include_path($_SERVER["DOCUMENT_ROOT"] . "/includes/");
date_default_timezone_set("America/Chicago");
if (isset($auth) && $_SESSION['role'] > $auth) {
	header("Location: /");
}
$mysqli = new mysqli('localhost', 'root', '', 'pin', '3306') or die("Failed to connect to the database. Error log: <br/>" . mysqli_error());
?>
