<?php
require '../includes/check_login.php';
if($_SESSION['user_auth_level'] < 10){
	if($_SESSION['user_authWO'] < 10 && $_SESSION['user_authTS'] < 10){
		header('location: ../unauthorized.php');
	}
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
	<li><a href="<?php echo $url_home; ?>">Home</a></li>
	<li class="active">Administration</li>
</ol>
<div class="container administration">
	<div class="row">
		<div class="col-lg-4">
			<img class="img-rounded" src="../images/users.png" alt="Users" style="width: 140px; height: 140px;">
			<h2>Users</h2>
			<p>Here you can add, edit and deactivate users.</p>
			<p>
				<a class="btn btn-primary" href="useradmin.php" role="button">Users</a>
			</p>
		</div>
		<div class="col-lg-4">
			<img class="img-rounded" src="../images/machines.png" alt="Users" style="width: 140px; height: 140px;">
			<h2>Machines</h2>
			<p>Here you can add, edit and deactivate machinery.</p>
			<p>
				<a class="btn btn-primary" href="machineadmin.php" role="button">Machines</a>
			</p>
		</div>
		<div class="col-lg-4">
			<img class="img-rounded" src="../images/parts.png" alt="Users" style="width: 140px; height: 140px;">
			<h2>Parts</h2>
			<p>Here you can add, edit, and delete parts.</p>
			<p>
				<a class="btn btn-primary" href="parts.php" role="button">Parts</a>
			</p>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-4">
			<img class="img-rounded" src="../images/tools.png" alt="Tools" style="width: 140px; height: 140px;">
			<h2>Tools</h2>
			<p>Here you can add, edit and remove trouble types.</p>
			<p>
				<a class="btn btn-primary" href="tools.php" role="button">Tools</a>
			</p>
		</div>
		
	</div>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/timeStudy.js"></script>
<script>
	$(".tree li:has(ul)").addClass("parent").click(function(event) {
		$(this).toggleClass("open");
		event.stopPropagation();
	});
</script>
</body>
</html>
