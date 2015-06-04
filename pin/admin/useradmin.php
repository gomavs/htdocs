<?php
require '../includes/check_login.php';

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
	<li><a href="admin.php">Administration</a></li>
	<li class="active">User Administration</li>
</ol>
<div class="container administration">
	<div class="row">
		<div class="col-lg-4">
			<img class="img-rounded" src="../images/listusers.png" alt="List Users" style="width: 140px; height: 140px;">
			<h2>List Users</h2>
			<p>Here you can list all the users on PIN Systems.</p>
			<p>
				<a class="btn btn-primary" href="listusers.php" role="button">List Users</a>
			</p>
		</div>
		<div class="col-lg-4">
			<img class="img-rounded" src="../images/addusers.png" alt="Add Users" style="width: 140px; height: 140px;">
			<h2>Add Users</h2>
			<p>Here you can add users on PIN Systems.</p>
			<p>
				<a class="btn btn-primary" href="adduser.php" role="button">Add Users</a>
			</p>
		</div>
		<div class="col-lg-4">
			<img class="img-rounded" src="../images/editusers.png" alt="Edit Users" style="width: 140px; height: 140px;">
			<h2>Edit Users</h2>
			<p>Here you can edit the users on PIN Systems.</p>
			<p>
				<a class="btn btn-primary" href="edituser.php" role="button">Edit Users</a>
			</p>
		</div>
	</div>

</div>
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/jquery-ui.js"></script>


</script>

</body>
</html>
