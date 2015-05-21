<?php
	$db = mysqli_connect('localhost', 'root', '', "timestudy");

	if(!$db){
		die('Could not connect: ' . mysql_error());
	}
?>