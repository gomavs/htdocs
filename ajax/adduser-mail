<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
/*if ($logged_in == 0 && $_SESSION["auth"] <= 1)
	return;*/
if (isset($_POST["send"]) && isset($_POST["type"]) && isset($_POST["firstname"]) && isset($_POST["lastname"]) && isset($_POST["username"]) && isset($_POST["pass"])) {
	$name = $_POST["firstname"]." ".$_POST["lastname"];
	$username = $_POST["username"];
	$pass = $_POST["pass"];
	$url = $_SERVER["HTTP_HOST"];
	$headers = "From: noreply@pin.dimensionsmachine.com" . "\r\n" .
    "Reply-To: noreply@pin.dimensionsmachine.com" . "\r\n" .
    "X-Mailer: PHP/" . phpversion();
	if ($_POST["type"] == "me") {
		$subject = "Pin control panel login information for $name";
		$message = "These are the control panel account credidentials for $name:\n\nUsername: $username \nPassword: $pass";
	} else {
		$subject = "Your Pin control panel login information";
		$message = "These are your control panel account credidentials:\n\nUsername: $username \nPassword: $pass\n\nYou may log in at this location: $url";
	}
	//mail($_POST["send"], $subject, $message, $headers);
}
?>