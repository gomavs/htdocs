<?php
require_once 'includes/dbConnect.php';

$default = ini_get('max_execution_time');
set_time_limit(1800);

$result = mysqli_query($db,"SELECT * FROM leftovers ORDER BY childnumber ASC");
$i = 0;
$j = 0;
while($row = mysqli_fetch_array($result)) {
	$parentnumber =  $row['parentnumber'];
	$childnumber = $row['childnumber'];
	$childdescription = $row['childdescription'];
	
	$result3 = mysqli_query($db,"SELECT id FROM part WHERE partnumber = '$childnumber'");
	$row3 = mysqli_fetch_assoc($result3);
	$rowid = $row3['id'];
	
	if(empty($rowid)){
		$result2 = mysqli_query($db,"SELECT id FROM part WHERE partnumber = '$parentnumber'");
		$row2 = mysqli_fetch_assoc($result2);
		$id = $row2['id'];
		//echo $partnumber." ".$cutnumber." id=".$id."<br>";
		
		if(!empty ($id)){
			mysqli_query($db,"INSERT INTO part (partnumber, partdesc, parentid) VALUES ('$childnumber', '$childdescription', '$id')");
			$i++;
		}else{
			mysqli_query($db,"INSERT INTO noparent (parentnumber, childnumber, childdescription) VALUES ('$parentnumber', '$childnumber', '$childdescription')");
			$j++;
		}
	}

}

echo "There were ".$i." records with parents.<br>";
echo "There were ".$j." records without parents.<br>";
set_time_limit($default);
?>