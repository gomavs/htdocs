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
	<li><a href="<?php echo $url_home; ?>">Home</a></li>
	<li><a href="workorders.php">Work Orders</a></li>
	<li class="active">Open Work Requests</li>
</ol>
<!-- Decline modal -->
<div class="modal fade" id="declineModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="declineModalLabel">Decline Reason</h4>
			</div>
			<form>
				<div class="modal-body ">
					<div class="row form-group" id="declineReason">
						<div class="col-md-2"><label for="declined" class="control-label">Reason</label></div>
						<div class="col-md-6">
							<select id="declined" name="declined" class="form-control">
								<option value="0">---- Choose Reason -----</option>
								<?php	
									$query = $db->prepare("SELECT * FROM declinedreasons");
									$query->execute();
									$result = $query->get_result();
										$i = 1;
									while (($row = $result->fetch_object()) !== NULL) {	
								?>
										<option value="<?php echo $row->id; ?>"><?php echo $row->reason ?></option>
								<?php
									}
								?>
							</Select>
						</div>
					</div>
				</div>
				<input type="hidden" id="requestId", name="requestId" value="">
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button type="button" id="declineRequest" class="btn btn-primary">Decline</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="container-fluid">
	<div class="panel panel-primary">
		<div class="panel-heading">Open Work Requests</div>
		<div class="panel-body">
			
			<table id="table_id" class="display testing">
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
		var user_id = <?php echo $_SESSION['user_id']; ?>;
		var authWO = <?php echo $_SESSION['user_authWO']; ?>;
		var rowIdx;
		var table;
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
					if( authWO >= 4){
						return '<a class="btn btn-info btn-sm" href=approverequest.php?id=' + data.ID + '>' + 'Approve' + '</a>&nbsp;<button type="button" id="decline-' + data.ID + '" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#declineModal" aria-label="Decline">Decline</button>';
						//return '<a class="btn btn-info btn-sm" href=approverequest.php?id=' + data.ID + '>' + 'Approve' + '</a>&nbsp;<a class="btn btn-danger btn-sm"  href=deleterequest.php?id=' + data.ID + '>' + 'Decline' + '</a>';
					}else{
						return '<a class="btn btn-info btn-sm disabled" href=approverequest.php?id=' + data.ID + '>' + 'Approve' + '</a>&nbsp;<a class="btn btn-danger btn-sm disabled" href=deleterequest.php?id=' + data.ID + '>' + 'Decline' + '</a>';
					}
				}},
				{ "data": "ID",
				  "visible": false,
				  "searchable": false },
			]
		});
		
		$( ".testing" ).on( "click", "[id^=decline-]", function() {
			var buttonId = this.id;
			var arr = buttonId.split('-');
			buttonId = arr[1];
			$('#requestId').val(buttonId);
			
		});
		
		$('#table_id tbody').on( 'click', 'tr', function () {
			rowIdx = table.row(this).index();
			//alert(rowIdx);
		} );
		
		$("button#declineRequest").click(function(){
			var requestId = $('#requestId').val();
			var declinedReason = $('#declined').val();
			var request = $.getJSON("../ajax/deleterequest.php", {id : requestId, reason : declinedReason, userId : user_id}, function(data) {
				console.log(data);
				$("#declineModal").modal('hide'); //hide modal
				table.row(rowIdx).remove().draw(false);  
				
			});
		});
		
	});
</script>
</body>
</html>
