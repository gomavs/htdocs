<?php
require '../includes/check_login.php';
$build_table= "";
$search_value = "";
$data = [];
if(isset($_POST["partnumber"])){
	$last_level = 0;
	$search_value = $_POST["partnumber"];
	
	//////////////////////// Get info for entered part number ///////////////////////////////////
	$query = $db->prepare("SELECT * FROM part WHERE partnumber = ?");
	$query->bind_param("s", $_POST["partnumber"]);
	$query->execute();
	$result = $query->get_result();
	$row = $result->fetch_assoc();
	//////////////////////// Is this part a parent or a child? ///////////////////////////////////
	$query2 = $db->prepare("SELECT * FROM part WHERE parentid = ?");
	$query2->bind_param("i", $row['id']);
	$query2->execute();
	$result2 = $query2->get_result();
	$row2 = $result2->fetch_assoc();
	$row_cnt = $result2->num_rows;
	if($row_cnt > 0){
	//////////////////////// This is a parent ///////////////////////////////////
		echo "test";
	}else{
	//////////////////////// This is a child ///////////////////////////////////
		$query = $db->prepare("SELECT * FROM part WHERE id = ?");
		$query->bind_param("i", $row['parentid']);
		$query->execute();
		$result = $query->get_result();
		$row4 = $result->fetch_assoc();
		$completed = 1;
		$query3 = $db->prepare("SELECT times.*, workcenter.* FROM times LEFT JOIN workcenter ON times.machine_id = workcenter.id WHERE times.item_id = ? AND times.completed = ? ORDER BY times.end_time ASC");
		$query3->bind_param("ii", $row['id'], $completed);
		$query3->execute();
		$result3 = $query3->get_result();
		while (($row3 = $result3->fetch_object()) !== NULL) {
			$data[] = ["Part Number"=>$row['partnumber'], "Part Description"=>$row['partdesc'], "Parent Number"=>$row4['partnumber'], "Work Center"=>$row3->center, "Machine"=>$row3->name, "Date"=>date("M d, Y", $row3->end_time), "Time"=>secondsToWords($row3->end_time - $row3->start_time)];
		}
		json_encode($data);
	}
	//display_children($row['id'], 1);
$build_table= "<thead>";
$build_table.= "<tr><th>Part Number</th><th>Part Description</th><th>Parent Number</th><th>Work Center</th><th>Machine</th><th>Date</th><th>Time</th></tr>";					
$build_table.= "</thead><tbody><tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tbody>";	

}
/*
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
}*/

function secondsToWords($seconds){
    /*** return value ***/
    $ret = "";
    /*** get the hours ***/
    $hours = intval(intval($seconds) / 3600);
    if($hours > 0){
        $ret .= $hours."hr ";
    }
    /*** get the minutes ***/
    $minutes = bcmod((intval($seconds) / 60),60);
    if($hours > 0 || $minutes > 0){
        $ret .= $minutes."m ";
    }
    /*** get the seconds ***/
    $seconds = bcmod(intval($seconds),60);
    $ret .= $seconds."s";
    return $ret;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>PIN Time Study</title>

<!-- Bootstrap -->
<link href="../css/bootstrap.min.css" rel="stylesheet">
<link href="../css/main.css" rel="stylesheet">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body>
<?php
include '../includes/navbar.php';
?>
<ol class="breadcrumb">
	<li><a href="..\index.php">Home</a></li>
	<li><a href="index.php">Reports</a></li>
	<li class="active">Part Times</li>
</ol>
<div class="container-fluid">
	<!-- Stack the columns on mobile by making one full-width and the other half-width -->
	<div class="row">
		<div class="col-md-12 main">
			<h2 class="page-header">Part Time Studies</h2>
		</div>	
	</div>
	<div class="row">
		<div class="col-md-12">
			<form method = "POST">
			<div class="col-md-2"><label>Part Number:</label></div>
			<div class="col-md-3"><input type="text" class="form-control" name="partnumber" id="autocomplete" autofocus placeholder="Enter part number"><input type="submit" style="position: absolute; left: -9999px; width: 1px; height: 1px;"/></div>
			</form>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<table id="table_id" class="display">
				<?php echo $build_table; ?>
			</table>
		</div>
	</div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript" src="../js/jquery.autocomplete.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/jquery-ui.js"></script>
<script type="text/javascript" charset="utf8" src="../js/jquery.dataTables.js"></script>
<script>
var data = "";
$(function(){
	$('#autocomplete').autocomplete({
		serviceUrl:"../ajax/search.php",
		onSelect: function(suggestion) {
		}
	});
});
	

$(document).ready(function() {
	$('#table_id').DataTable( {
		//"processing": true,
		//"bProcessing": true,
		//"sAjaxDataProp":"",
		//"bServerSide": true,
		//"ajax": "../ajax/updatepartsreport.php?starttime=" + startDate + "&endtime=" + endDate,
		data: data,
		"columns": [
			{ "data": "Part Number" },
			{ "data": "Part Description" },
			{ "data": "Parent Number" },
			{ "data": "Work Center" },
			{ "data": "Machine" },
			{ "data": "Date" },
			{ "data": "Time" }
		]
	});
});

</script>
</body>
</html>