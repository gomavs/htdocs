<?php
require '../includes/check_login.php';
if($_SESSION['user_auth_level'] < 10){
	if($_SESSION['user_authWO'] < 10 && $_SESSION['user_authTS'] < 10){
		header('location: ../unauthorized.php');
	}
}
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>PIN Time Study</title>

	<!-- Bootstrap -->
	<link href="../css/bootstrap.min.css" rel="stylesheet">

	<!-- Custom styles for this template -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	<link href="../css/jquery-ui.css" rel="stylesheet">
	<link href="../css/main.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.css">
	<link rel="stylesheet" type="text/css" href="../css/dataTables.tableTools.min.css">
	<link rel="stylesheet" type="text/css" href="../css/dataTables.colVis.css">
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
	<li><a href="admin.php">Administration</a></li>
	<li><a href="useradmin.php">User Administration</a></li>
	<li class="active">User List</li>
</ol>
<div class="container-fluid">
	<div class="panel panel-primary">
		<div class="panel-heading">User List</div>
		<div class="panel-body">
			<div class="row col-md-12">
				<div class ="col-md-1 pull-right"><a href="adduser.php" ><button type="button" class="btn btn-primary btn-sm">Add Users</button></a></div>
				<div class ="col-md-1 pull-right"><a href="edituser.php" ><button type="button" class="btn btn-primary btn-sm">Edit Users</button></a></div>
			</div>
			<div class="row col-md-12 spacer">
				<table id="table_id" class="display">
					<thead>
						<tr>
							<th>First Name</th>
							<th>Last Name</th>
							<th>Email Address</th>
							<th>Mobil Number</th>
							<th>Permissions</th>
							<th>Active</th>
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
						</tr>
					</tbody>
				</table>	
			</div>
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
		var table = $('#table_id').DataTable( {
			dom: 'C&T<"clear">lfrtip',
			"oColVis": { "aiExclude": [ 6 ]},
			tableTools: {
				"sRowSelect": "os",
				"aButtons": [ "select_all", "select_none", "copy", "print" ],
			},
			"bProcessing": true,
			"sAjaxDataProp":"",
			"ajax": "../ajax/users.php",
			"columns": [
				{ "data": "First Name" },
				{ "data": "Last Name" },
				{ "data": "Email Address" },
				{ "data": "Mobil Number"},
				{ "data": "Permissions" },
				{ "data": "Active" },
				{ "data": "ID",
				  "visible": false,
				  "searchable": false }
			]
		} );
	} );
</script>

</body>
</html>
