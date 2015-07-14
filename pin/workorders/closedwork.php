<?php
require '../includes/check_login.php';

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
	<li><a href="..">Home</a></li>
	<li><a href="workorders.php">Work Orders</a></li>
	<li class="active">Closed Work Orders</li>
</ol>
<div class="container-fluid">
	<div class="panel panel-primary">
		<div class="panel-heading">Closed Work Orders</div>
		<div class="panel-body">
			<div class="row col-md-12 bottom_fix">
			<!--	<div class ="col-md-1 pull-right"><a href="#" ><button type="button" class="btn btn-primary btn-sm btn-block">Open Work Orders</button></a></div>
				<div class ="col-md-1 pull-right"><a href="openrequests.php" ><button type="button" class="btn btn-primary btn-sm btn-block">Open Requests</button></a></div>
				<div class ="col-md-1 pull-right"><a href="requestwork.php" ><button type="button" class="btn btn-primary btn-sm btn-block">Request Work</button></a></div>-->
			</div>
			<table id="table_id" class="display table">
				<thead>
					<tr>
						<th></th>
						<th>#</th>
						<th>Type</th>
						<th>Item</th>
						<th>Description</th>
						<th>Request Date</th>
						<th>End Date</th>
						<th>Requested By</th>
						<th>Priority</th>
						<th>Command</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th></th>
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
			"ajax": "../ajax/getclosedworkorders.php",
			"aoColumns": [
				{
					"className":      'details-control',
					"orderable":      false,
					"data":           null,
					"defaultContent": ''
				},
				{ "data": "#", "sWidth": "5%" },
				{ "data": "Type", "sWidth": "7%" },
				{ "data": "Item", "sWidth": "10%" },
				{ "data": "Description", "sWidth": "25%" },
				{ "data": "Request Date", "sWidth": "10%" },
				{ "data": "End Date", "sWidth": "10%" },
				{ "data": "Requested By", "sWidth": "10%" },
				{ "data": "Priority", "sWidth": "10%"},
				{ "data": null, "sWidth": "10%", "bSortable": false, "mRender": function(data, type, full){
					if( authWO >= 3){
						return '<a class="btn btn-info btn-sm" href=workorder.php?id=' + data.id + '>' + 'View' + '</a>';
					}else{
						return '<a class="btn btn-info btn-sm disabled" href=workorder.php?id=' + data.id + '>' + 'View' + '</a>';
					}
				}},
			],
			"fnRowCallback": function( nRow, data, iDisplayIndex ) {
				try{
					if(data.Priority == 'High'){
						$(nRow).addClass("danger");
					} else if (data.Priority == 'Medium'){
						$(nRow).addClass("warning");
					} else {
						//$(nRow).addClass("info");
					}
					
				} catch(ex){
					alert("fnRowCallback exception:");
				}
				return nRow
			}
		} );
		
		/* Formatting function for row details - modify as you need */
		function format ( d ) {
			// `d` is the original data object for the row
			return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
				'<tr>'+
					'<td><b>Assigned To:</b></td>'+
					'<td colspan="5">'+d.Assigned+'</td>'+
				'</tr>'+
				'<tr>'+
					'<td><b>Assigned Date:</b></td>'+
					'<td>'+d.assignDate+'</td>'+
					'<td><b>Due Date:</b></td>'+
					'<td>'+d.dueDate+'</td>'+
					'<td><b>Estimated Time:</b></td>'+
					'<td>'+d.estimate+'</td>'+
				'</tr>'+
				'<tr>'+
					'<td><b>Notes:</b></td>'+
					'<td colspan="5">'+d.notes+'</td>'+
				'</tr>'+
			'</table>';
		}
		
		// Add event listener for opening and closing details
		$('#table_id tbody').on('click', 'td.details-control', function () {
			var tr = $(this).closest('tr');
			var row = table.row( tr );
	 
			if ( row.child.isShown() ) {
				// This row is already open - close it
				row.child.hide();
				tr.removeClass('shown');
			}
			else {
				// Open this row
				row.child( format(row.data()) ).show();
				tr.addClass('shown');
			}
		} );
		
		
	} );
</script>
</body>
</html>
