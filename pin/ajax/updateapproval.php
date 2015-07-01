<?php
require_once("../includes/dbConnect.php");
if(isset($_POST['workOrderId'])){
	$workOrderId = $_POST['workOrderId'];
	$dueDate = strtotime($_POST['from']);
	$estimatedTime = ($_POST['inputEstimatedTimeDays'] * 86400)+($_POST['inputEstimatedTimeHrs'] * 3600)+($_POST['inputEstimatedTimeMin'] * 60);
	$approvalNotes = $_POST['textNotes'];
	$query = $db->prepare("UPDATE workorder SET dueDate = ?, timeEstimate = ?, notes = ? WHERE id = ?");
	$query->bind_param("iisi", $dueDate, $estimatedTime, $approvalNotes, $workOrderId);
	$query->execute();
	$dueDate =  date("n/j/y", $dueDate);;
	
	$timeEstimate = secondsToTime($estimatedTime);
	list($days, $hrs, $mins) = explode(",", $timeEstimate);
	$estimated_time = $days." Days ".$hrs. " Hrs ".$mins." Mins";
	
	$checked_users = array();
	if(!empty($_POST['check_list'] )){
		foreach($_POST['check_list'] as $check){
			$checked_users[] = intval($check);
		}
	}
	if(isset($_POST['checkboxOther'])){
		$checked_users[] = intval($_POST['selectOther']);
	}
	//*****************Check if Worker was removed from checkboxes*****************************
	$removed_user_list = array();
	$query = $db->prepare("SELECT * FROM workdata WHERE workOrderID = ?");
	$query->bind_param("i", $workOrderId);
	$query->execute();
	$result = $query->get_result();
	while (($row = $result->fetch_object()) !== NULL) {
		$removed_user = $row->assignedTo;
		$row_cnt = "";
		if(!in_array($removed_user, $checked_users)){
			//Checks if the removed user has any work on this work order
			$query1 = $db->prepare("SELECT * FROM worktimes WHERE userId = ? AND workOrderID = ?");
			$query1->bind_param("ii", $removed_user, $workOrderId);
			$query1->execute();
			$result1 = $query1->get_result();
			$row_cnt = mysqli_num_rows($result1);
			if($row_cnt == 0){
				//If there is no work started, then remove the user from the work order
				$query2 = $db->prepare("DELETE FROM workdata WHERE workOrderID = ? AND assignedTo = ?");
				$query2->bind_param("ii", $workOrderId, $removed_user);
				$query2->execute();
				$removed_user_list[] = intval($removed_user);// fills array with a list of removed users
			}else{
				//The user had work started so the user can't be removed from the work order.
				$checked_users[] = intval($removed_user);// adds the removed user back into the array
			}
		}
	}
	
	//add the newly selected user to the workdata table (assign job) and get user data
	$user_list = array();
	$added_users = array();
	$hideButtons = "";
	foreach($checked_users as $tech){
		$query = $db->prepare("SELECT * FROM workdata WHERE workOrderID = ? AND assignedTo = ?");
		$query->bind_param("ii", $workOrderId, $tech);
		$query->execute();
		$result = $query->get_result();
		$row_cnt = mysqli_num_rows($result);
		$row = $result->fetch_assoc();
		$workStatus = $row['status'];
		if($row_cnt === 0){
			mysqli_query($db,"INSERT INTO workdata (workOrderId, assignedTo) VALUES ('$workOrderId', '$tech')");
			$query1 = $db->prepare("SELECT firstname, lastname FROM users WHERE id = ?");
			$query1->bind_param("i", $tech);
			$query1->execute();
			$result1 = $query1->get_result();
			$row1 = $result1->fetch_assoc();			
			$added_users[] = ["id"=>$tech, "firstname"=>$row1['firstname'], "lastname"=>$row1['lastname']];
		}
		$query1 = $db->prepare("SELECT id, firstname, lastname FROM users WHERE id = ?");
		$query1->bind_param("i", $tech);
		$query1->execute();
		$result1 = $query1->get_result();
		while (($row1 = $result1->fetch_object()) !== NULL) {
			$user_list[] = ["id"=>$row1->id, "firstname"=>$row1->firstname, "lastname"=>$row1->lastname];
		}
	}
	
	
	$data[] = ["due_date"=>$dueDate, "estimated_time"=>$estimated_time, "approval_notes"=>$approvalNotes, "selected_users"=>$user_list, "removed_users"=>$removed_user_list, "added_users"=>$added_users]; 
	echo json_encode($data);
}
function secondsToTime($seconds) {
	$dtF = new DateTime("@0");
	$dtT = new DateTime("@$seconds");
	return $dtF->diff($dtT)->format('%a,%h,%i');
}
?>