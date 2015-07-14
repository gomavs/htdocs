<?php

function sendAlert($alertId){
	//require_once("dbConnect.php");
	global $db;
	//$alertId = 68;
	$query = $db->prepare("SELECT messages.id, messages.msgTo, messages.msgFrom, messages.message, users.cell, users.carrierId, users.allowTexts, smsaddress.suffix, smsaddress.rules FROM messages LEFT JOIN users ON users.id=messages.msgTo LEFT JOIN smsaddress ON smsaddress.id=users.carrierId WHERE messages.id=?");
	$query->bind_param("i", $alertId);
	$query->execute();
	$result = $query->get_result();
	$row = $result->fetch_assoc();
	if($row['allowTexts'] == 1){
		if(!empty($row['cell']) and $row['carrierId'] > 0){
			if($row['msgTo'] != $row['msgFrom']){
				$to      = str_replace("-", "", $row['cell'])."@".$row['suffix'];
				//$to = "sbrunson@pin.com";
				$subject = 'New Alert';
				$message = $row['message'];
				$message = str_replace("<b>", "", $message);
				$message = str_replace("</b>", "", $message);
				$headers = 'From: noreply@pin.com' . "\r\n" . 'X-Mailer: PHP/' . phpversion();
				//echo $to."</br>";
				//echo $subject."</br>";
				//echo $row['message']."</br>";
				if(mail($to, $subject, $message, $headers)){
					//echo "Success";
				}else{
					//echo "Failure";
				}
			}
		}else{
			//echo "There is not sufficient phone data to send a text";
		}
	}else{
		//echo "This user does not want to receive texts";
	}
}
?>