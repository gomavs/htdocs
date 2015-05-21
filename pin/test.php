<?php
require_once 'includes/dbConnect.php';
$return_data = "";
if(isset($_GET["partnumber"])){
	$ul_count = 0;
	$last_level = 0;
	$query = $db->prepare("SELECT * FROM part WHERE partnumber = ?");
	$query2 = $db->prepare("SELECT * FROM part WHERE parentid = ?");
	$query->bind_param("s", $_GET["partnumber"]);
	$query->execute();
	$result = $query->get_result();
	$row = $result->fetch_assoc();
	$query = $db->prepare("SELECT * FROM part WHERE parentid = ?");
	$query->bind_param("i", $row['id']);
	$query->execute();
	$result = $query->get_result();
	$row_cnt = $result->num_rows;
	$return_data = "<ul>";
	if($row_cnt > 0){
		$return_data .= "<li class=\"momma\" id=\"".$row['id']."\">".$row['partnumber']." "."<span>".$row['partdesc']."</span>";
	}else{
		$return_data .= "<li class=\"items\" id=\"".$row['id']."\">".$row['partnumber']." "."<span>".$row['partdesc']."</span></li>";
	}
	display_children($row['id'], 1);
	for($ul_count; $ul_count > 0; $ul_count--){
		$return_data .= "</ul></li>";
	}
	$return_data .= "</ul>";
}
function display_children($category_id, $level){
	global $query;
	global $query2;
	global $return_data;
	global $ul_count;
	global $last_level;
	$query->bind_param("i", $category_id);
	$query->execute();
	$result = $query->get_result();
	// display each child
	while ($row = $result->fetch_array(MYSQLI_ASSOC)){
		$query2->bind_param("i", $row['id']);
		$query2->execute();
		$result2 = $query2->get_result();
		$row_cnt = $result2->num_rows;
		if($level > $last_level){
			if($row_cnt > 0){
				$return_data .= "<ul  class=\"sub\"><li class=\"momma\" id=\"".$row['id']."\">".$row['partnumber']." "."<span>".$row['partdesc']."</span>";
			}else{
				$return_data .= "<ul  class=\"sub\"><li class=\"items\" id=\"".$row['id']."\">".$row['partnumber']." "."<span>".$row['partdesc']."</span></li>";
			}
			$ul_count = $ul_count + 1;
		}elseif($level < $last_level){
			$return_data .= "</ul></li>";
			if($row_cnt >0){
				$return_data .= "<li class=\"momma\" id=\"".$row['id']."\">".$row['partnumber']." "."<span>".$row['partdesc']."</span>";
			}else{
				$return_data .= "<li class=\"items\" id=\"".$row['id']."\">".$row['partnumber']." "."<span>".$row['partdesc']."</span></li>";
			}
			$ul_count = $ul_count - 1;
		}else{
			if($row_cnt >0){
				$return_data .= "<li class=\"momma\" id=\"".$row['id']."\">".$row['partnumber']." "."<span>".$row['partdesc']."</span>";
			}else{
				$return_data .= "<li class=\"items\" id=\"".$row['id']."\">".$row['partnumber']." "."<span>".$row['partdesc']."</span></li>";
			}
		}
		$last_level = $level;
		// call this function again to display this child's children
		display_children($row['id'], $level+1);
	}
}
echo $return_data;
?>