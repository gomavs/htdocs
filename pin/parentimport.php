<?php
require_once 'includes/dbConnect.php';

$default = ini_get('max_execution_time');
set_time_limit(300);

$result = mysqli_query($db,"SELECT * FROM tempparts ORDER BY partnumber ASC");
$i = 0;
$j = 0;
while($row = mysqli_fetch_array($result)) {
	$partnumber =  $row['partnumber'];
	$partdesc = $row['partdesc'];
	$numberlength = strlen($partnumber);
	if ($numberlength > 5) {

/*
		$query = $db->prepare("INSERT INTO part SET partnumber = ?, partdesc = ?");
		$query->bind_param("ss", $partnumber, $partdesc);
		$query->execute();
		
		if ($query->affected_rows == 0){
			echo $partnumber." was not inserted<br>";
			$i++;
		}
*/

		$cutnumber = mb_strcut($partnumber,0,5);
		$result2 = mysqli_query($db,"SELECT id FROM part WHERE partnumber = '$cutnumber'");
		$row2 = mysqli_fetch_assoc($result2);
		$id = $row2['id'];
		//echo $partnumber." ".$cutnumber." id=".$id."<br>";
		
		if(!empty ($id)){
			mysqli_query($db,"INSERT INTO part (partnumber, partdesc, parentid) VALUES ('$partnumber', '$partdesc', '$id')");
			$i++;
		}else{
			mysqli_query($db,"INSERT INTO part (partnumber, partdesc) VALUES ('$partnumber', '$partdesc')");
			$j++;
		}

	}
}
//echo $i." was not inserted into database.<br>";
echo "There were ".$i." records with parents.<br>";
echo "There were ".$j." records without parents.<br>";
set_time_limit($default);
?>