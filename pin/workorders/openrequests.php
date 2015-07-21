<?php
require '../includes/check_login.php';
//require_once '../includes/dbConnect.php';

$type_list = "<select id=\"selectRequestType\" class=\"form-control\" name=\"requestType\" required><option value=\"0\">-- Choose Type --</option>";
$query = $db->prepare("SELECT * FROM worktypes ORDER BY id ASC");
$query->execute();
$result = $query->get_result();
while (($row = $result->fetch_object()) !== NULL) {
	$type_id = $row->id;
	$worktype = $row->type;
	$type_list = $type_list."<option value=\"".$type_id."\">".$worktype."</option>";
		
}
$type_list = $type_list."</select>";

if (isset( $_POST[ 'submit' ] ) ) {
	$requestType = $_POST['requestType'];
	$selectPriority = $_POST['selectPriority'];
	$textDescription = $_POST['textDescription'];
	switch($requestType){
		case 1: $itemId = $_POST['selectMachine']; break;
		case 2: $itemId = $_POST['selectFacility']; break;
		case 3: $itemId = $_POST['selectSafety']; break;
		case 4: $itemId = $_POST['selectTool']; break;
		case 5: $itemId = 0; break; 
		default : $itemId = 0;
	}
	$inputOther = $_POST['inputOther'];
	$date = new DateTime();
	$timestamp = $date->getTimestamp();
	$requestedBy = $_SESSION['user_id'];
	$accpeted = 0;
	mysqli_query($db,"INSERT INTO workrequest (workTypeId, itemId, priority, description, timestamp, requestedBy, accepted, other) 
	VALUES ('$requestType', '$itemId', '$selectPriority', '$textDescription', '$timestamp', '$requestedBy', '$accpeted', '$inputOther')");
	$message = 'Your  work request has been submitted.';
	echo "<SCRIPT>alert('$message');</SCRIPT>";
}
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>PIN Systems</title>

	<!-- Bootstrap -->
	<link href="../css/bootstrap.min.css" rel="stylesheet">

	<!-- Custom styles for this template -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	<link href="../css/jquery-ui.css" rel="stylesheet">
	<link href="../css/main.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.css">
	<link href="../css/jquery-ui.theme.css" rel="stylesheet">
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
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
	<li><a href="<?php echo $url_home; ?>">Home</a></li>
	<li><a href="workorders.php">Work Orders</a></li>
	<li class="active">Open Work Requests</li>
</ol>
<div class="container-fluid">
	<div class="panel panel-primary">
		<div class="panel-heading">Open Work Requests</div>
		<div class="panel-body">
			<div class="row col-md-12 bottom_fix">
			<!--	<div class ="col-md-1 pull-right"><a href="#" ><button type="button" class="btn btn-primary btn-sm btn-block">Complete WOs</button></a></div>
				<div class ="col-md-1 pull-right"><a href="workprogress.php" ><button type="button" class="btn btn-primary btn-sm btn-block">Open WOs</button></a></div>
				<div class ="col-md-1 pull-right"><a href="requestwork.php" ><button type="button" class="btn btn-primary btn-sm btn-block">Request Work</button></a></div>-->
			</div>
			<table id="table_id" class="display">
				<thead>
					<tr>
						<th>#</th>
						<th>Type</th>
						<th>Item</th>
						<th>Description</th>
						<th>Request Date</th>
						<th>Request Time</th>
						<th>Requested By</th>
						<th>Priority</th>
						<th>Command</th>
						<th>ID</th>
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
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/jquery-ui.js"></script>
<script type="text/javascript" charset="utf8" src="../js/jquery.dataTables.js"></script>
<script src="../js/dataTables.tableTools.min.js"></script>
<script src="../js/dataTables.colVis.js"></script>
<script>
	$(document).ready(function() {
		var authWO = <?php echo $_SESSION['user_authWO'] ?>;
		table = $('#table_id').DataTable( {
			
			"bProcessing": true,
			"sAjaxDataProp":"",
			"ajax": "../ajax/getworkrequests.php",
			"aoColumns": [
				{ "data": "#", "sWidth": "5%" },
				{ "data": "Type", "sWidth": "7%" },
				{ "data": "Item", "sWidth": "11%" },
				{ "data": "Description", "sWidth": "25%" },
				{ "data": "Request Date", "sWidth": "10%" },
				{ "data": "Request Time", "sWidth": "10%" },
				{ "data": "Requested By", "sWidth": "10%" },
				{ "data": "Priority", "sWidth": "10%"},
				{ "data": null, "sWidth": "10%", "bSortable": false, "mRender": function(data, type, full){
					if( authWO >= 3){
						return '<a class="btn btn-info btn-sm" href=approverequest.php?id=' + data.ID + '>' + 'Approve' + '</a>&nbsp;<a class="btn btn-danger btn-sm" href=deleterequest.php?id=' + data.ID + '>' + 'Delete' + '</a>';
					}else{
						return '<a class="btn btn-info btn-sm disabled" href=approverequest.php?id=' + data.ID + '>' + 'Approve' + '</a>&nbsp;<a class="btn btn-danger btn-sm disabled" href=deleterequest.php?id=' + data.ID + '>' + 'Delete' + '</a>';
					}
				}},
				
				{ "data": "ID",
				  "visible": false,
				  "searchable": false },
			]
		} );
	} );
</script>
</body>
</html>
