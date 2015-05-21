<?php
set_time_limit(0);
$parts = [];
$handle = fopen("data.csv", "r");
while (($data = fgetcsv($handle, 100)) !== FALSE) {
	$parts[] = $data;
}

