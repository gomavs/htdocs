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
	

}

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

<link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="../css/dataTables.tableTools.min.css">
<link rel="stylesheet" type="text/css" href="../css/dataTables.colVis.css">


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
	<div class="row col-md-12">
		<form method = "POST">
			<div class="col-md-1"><label>Part Number:</label></div>
			<div class="col-md-2"><input type="text" class="form-control" name="partnumber" id="autocomplete" autofocus placeholder="Enter part number"><input type="submit" style="position: absolute; left: -9999px; width: 1px; height: 1px;"/></div>
		</form>
	</div>
	<div class="row col-md-12 spacer hidden" id="partTable">
			<table id="table_id" width=100%>
				<thead>
					<tr>
						<th>Part Number</th>
						<th>Part Description</th>
						<th>Parent Number</th>
						<th>Work Center</th>
						<th>Machine</th>
						<th>Date</th>
						<th>Average Time</th>
						<th>Parts per Hour</th>
						<th>Cycles</th>
						<th>id</th>
					</tr>					
				</thead>
				<tbody>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</tbody>
			</table>
		
	</div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="../js/jquery.autocomplete.min.js" type="text/javascript" ></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/jquery.dataTables.js" type="text/javascript" charset="utf8" ></script>
<script src="../js/dataTables.tableTools.min.js"></script>
<script src="../js/dataTables.colVis.js"></script>
<script>
	$(document).ready(function() {
		var data = "";
		var itemId = "";
		var table;
		$(function(){
			$('#autocomplete').autocomplete({
				serviceUrl:"../ajax/search.php",
				onSelect: function(suggestion) {
					itemId = $(this).val();
					//alert(itemId);
					if(table){
						table.destroy();
					}
					table_fill(itemId);
				}
			});
		});

		$('#autocomplete').keypress(function (e) {
			var key = e.which;
			// the enter key code
			if(key == 13){
				itemId = $(this).val();
				//alert(itemId);
				if(table){
					table.destroy();
				}
				table_fill(itemId);
				return false;  
			}
		});
		function table_fill(item_Id){
			$("#partTable").removeClass("hidden");
			table = $('#table_id').DataTable( {
				"aLengthMenu": [[15, 25, 50, 100, -1], [15, 25, 50, 100, "All"]],
				"iDisplayLength": 15,
				dom: 'C&T<"clear">lfrtip',
				"oColVis": { "aiExclude": [ 8 ]},
				tableTools: {
					"sRowSelect": "os",
					"aButtons": [ "select_all", "select_none", "copy", "print", "csv" ]
				},
				"bProcessing": true,
				"sAjaxDataProp":"",
				"ajax": "../ajax/getpartsreport.php?partId=" + itemId,
				data: data,
				"columns": [
					{ "data": "Part Number" },
					{ "data": "Part Description" },
					{ "data": "Parent Number" },
					{ "data": "Work Center" },
					{ "data": "Machine" },
					{ "data": "Date" },
					{ "data": "Average Time" },
					{ "data": "Parts per Hour" },
					{ "data": "Cycles" },
					{ "data": "id",
						  "visible": false,
						  "searchable": false
					}
				],
				"fnRowCallback": function( nRow, data, iDisplayIndex ) {
					try{
						if(data.status == 1){
							//$(nRow).addClass("bg-mama");
							$(nRow).css({"background-color":"#d9edf7"});
						}						
					} catch(ex){
						alert("fnRowCallback exception:");
					}
					return nRow
				}
			});
		};
	});

</script>
</body>
</html>