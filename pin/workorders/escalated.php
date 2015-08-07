<?php
require '../includes/check_login.php';
$hidden2 = "hidden";
$hidden3 = "hidden";
$query = $db->prepare("SELECT tier FROM escalation WHERE userId = ?");
$query->bind_param("i", $_SESSION['user_id']);
$query->execute();
$result = $query->get_result();
$row = $result->fetch_assoc();
$tier = $row['tier'];
if($tier == 2){
	$hidden2 = "";
}elseif($tier == 3){
	$hidden2 = "";
	$hidden3 = "";
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
	<li class="active">Escalated Requests</li>
</ol>
<!-- Approve modal -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="approveModalLabel">Approval Override</h4>
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
				<input type="hidden" id="requestIdApprove", name="requestIdApprove" value="">
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-primary">Approve</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- Decline modal 1 -->
<div class="modal fade" id="declineModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
							<select id="declined1" name="declined1" class="form-control">
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
				<input type="hidden" id="requestIdDecline1", name="requestIdDecline1" value="">
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-primary" id="declineRequest1">Decline</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- Decline modal 2 -->
<div class="modal fade" id="declineModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
							<select id="declined2" name="declined2" class="form-control">
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
				<input type="hidden" id="requestIdDecline2", name="requestIdDecline2" value="">
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-primary" id="declineRequest2">Decline</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="container-fluid">
	<div class="panel panel-primary <?php echo $hidden2; ?>">
		<div class="panel-heading">Requests Escalated To Level 2</div>
		<div class="panel-body">
			
			<table id="table_1" class="display do_action1">
				<thead>
					<tr>
						<th>#</th>
						<th></th>
						<th>Type</th>
						<th>Item</th>
						<th>Description</th>
						<th>Request Date</th>
						<th>Requested By</th>
						<th>Declined By</th>
						<th>Declined Reason</th>
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
						<td></td>
						<td></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="panel panel-primary <?php echo $hidden3; ?>">
		<div class="panel-heading">Requests Escalated To Level 3</div>
		<div class="panel-body">
			<table id="table_2" class="display do_action2">
				<thead>
					<tr>
						<th>#</th>
						<th></th>
						<th>Type</th>
						<th>Item</th>
						<th>Description</th>
						<th>Request Date</th>
						<th>Requested By</th>
						<th>Declined By</th>
						<th>Declined Reason</th>
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
		var authWO = <?php echo $_SESSION['user_authWO'] ?>;
		var rowIdx;
		var table1;
		var table2;
		var 
		table1 = $('#table_1').DataTable( {
			"bProcessing": true,
			"sAjaxDataProp":"",
			"ajax": "../ajax/getescalated.php",
			"aoColumns": [
				{ "data": "#", "sWidth": "3%" },
				{ "data": "Mark", "sWidth": "2%", "orderable": false},
				{ "data": "Type", "sWidth": "7%" },
				{ "data": "Item", "sWidth": "11%" },
				{ "data": "Description", "sWidth": "23%" },
				{ "data": "Request Date", "sWidth": "10%" },
				{ "data": "Requested By", "sWidth": "10%" },
				{ "data": "Declined By", "sWidth": "10%"},
				{ "data": "Declined Reason", "sWidth": "14%"},
				{ "data": null, "sWidth": "10%", "bSortable": false, "mRender": function(data, type, full){
					if( authWO >= 4){
						return '<button type="button" id="approve-' + data.ID + '" class="btn btn-info btn-sm" data-toggle="modal" data-target="#approveModal" aria-label="Approve">Approve</button>&nbsp;<button type="button" id="decline-' + data.ID + '" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#declineModal1" aria-label="Decline">Decline</button>';
					}else{
						return '<button type="button" id="approve-' + data.ID + '" class="btn btn-info btn-sm" data-toggle="modal" data-target="#approveModal" aria-label="Approve" disabled >Approve</button>&nbsp;<button type="button" id="decline-' + data.ID + '" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#declineModal1" aria-label="Decline" disabaled >Decline</button>';
					}
				}},
				{ "data": "ID", "visible": false, "searchable": false },
				
			],
			"createdRow": function ( row, data, index ) {
				if(data.status == 3){
					$('td', row).eq(1).addClass('text-danger high-importance');
				}else if (data.status == 2){
					$('td', row).eq(1).addClass('text-primary med-importance');
				}
			}
		});
		table2 = $('#table_2').DataTable( {
			"bProcessing": true,
			"sAjaxDataProp":"",
			"ajax": "../ajax/getescalated2.php",
			"aoColumns": [
				{ "data": "#", "sWidth": "3%" },
				{ "data": "Mark", "sWidth": "2%", "orderable": false},
				{ "data": "Type", "sWidth": "7%" },
				{ "data": "Item", "sWidth": "11%" },
				{ "data": "Description", "sWidth": "23%" },
				{ "data": "Request Date", "sWidth": "10%" },
				{ "data": "Requested By", "sWidth": "10%" },
				{ "data": "Declined By", "sWidth": "10%"},
				{ "data": "Declined Reason", "sWidth": "14%"},
				{ "data": null, "sWidth": "10%", "bSortable": false, "mRender": function(data, type, full){
					if( authWO >= 4){
						return '<button type="button" id="approve-' + data.ID + '" class="btn btn-info btn-sm" data-toggle="modal" data-target="#approveModal" aria-label="Approve">Approve</button>&nbsp;<button type="button" id="decline-' + data.ID + '" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#declineModal2" aria-label="Decline">Decline</button>';
					}else{
						return '<button type="button" id="approve-' + data.ID + '" class="btn btn-info btn-sm" data-toggle="modal" data-target="#approveModal" aria-label="Approve" disabled >Approve</button>&nbsp;<button type="button" id="decline-' + data.ID + '" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#declineModal2" aria-label="Decline" disabaled >Decline</button>';
					}
				}},
				{ "data": "ID", "visible": false, "searchable": false }
			],
			"createdRow": function ( row, data, index ) {
				if(data.status == 3){
					$('td', row).eq(1).addClass('text-danger high-importance');
				}else if (data.status == 2){
					$('td', row).eq(1).addClass('text-primary med-importance');
				}
			}
		});
		$( ".do_action1" ).on( "click", "[id^=decline-]", function() {
			var buttonId = this.id;
			var arr = buttonId.split('-');
			buttonId = arr[1];
			$('#requestIdDecline1').val(buttonId);
			var request = $.getJSON("../ajax/escalated.php", {request : buttonId}, function(data) {
				console.log(data);
				$('#declined1').val(data[0].reason); 
			});
			
			
		});
		$( ".do_action2" ).on( "click", "[id^=decline-]", function() {
			var buttonId = this.id;
			var arr = buttonId.split('-');
			buttonId = arr[1];
			$('#requestIdDecline2').val(buttonId);
			var request = $.getJSON("../ajax/escalated.php", {request : buttonId}, function(data) {
				console.log(data);
				$('#declined2').val(data[0].reason); 
			});
		});
		$( ".do_action1" ).on( "click", "[id^=approve-]", function() {
			var buttonId = this.id;
			var arr = buttonId.split('-');
			buttonId = arr[1];
			$('#requestIdApprove').val(buttonId);
			
		});

		$('#table_1 tbody').on( 'click', 'tr', function () {
			rowIdx = table1.row(this).index();
		} );
		
		$('#table_2 tbody').on( 'click', 'tr', function () {
			rowIdx = table2.row(this).index();
		} );
		
		$("button#declineRequest1").click(function(){
			var requestId = $('#requestIdDecline1').val();
			var declinedReason = $('#declined1').val();
			var request = $.getJSON("../ajax/deleterequest.php", {id : requestId, reason : declinedReason, userId : user_id}, function(data) {
				console.log(data);
				$("#declineModal1").modal('hide'); //hide modal
				table1.row(rowIdx).remove().draw(false);  
				
			});
		});
		$("button#declineRequest2").click(function(){
			var requestId = $('#requestIdDecline2').val();
			var declinedReason = $('#declined2').val();
			var request = $.getJSON("../ajax/deleterequest.php", {id : requestId, reason : declinedReason, userId : user_id}, function(data) {
				console.log(data);
				$("#declineModal2").modal('hide'); //hide modal
				table2.row(rowIdx).remove().draw(false);  
				
			});
		});
		
	});
</script>
</body>
</html>
