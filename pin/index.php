<?php
require 'includes/check_login.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>PIN Time Study</title>

<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/main.css" rel="stylesheet">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body>
<?php
include 'includes/navbar.php';
?>
<ol class="breadcrumb">
	<li class="active">Home</li>
</ol>
<div class="container administration">
	<div class="row">
		<?php
			if($permission[1] == 1){
		?>
		<div class="col-lg-4">
			<img class="img-rounded" src="images/work-order.png" alt="Work Orders" style="width: 140px; height: 140px;">
			<h2>Work Order System</h2>
			<p>All things related to maintenance work orders.</p>
			<p>
				<a class="btn btn-primary" href="workorders/workorders.php" role="button">Work Orders</a>
			</p>
		</div>
		<?php
			}
			if($permission[0] == 1){
		?>
		<div class="col-lg-4">
			<img class="img-rounded" src="images/stop-watch.png" alt="Time Study" style="width: 140px; height: 140px;">
			<h2>Time Study</h2>
			<p>This area is for the time studies.</p>
			<p>
				<a class="btn btn-primary" href="timestudy/index.php" role="button">Time Studies</a>
			</p>
		</div>
		<?php
			}
		?>
	<!--<div class="col-lg-4">
			<img class="img-rounded" src="images/machinestime.png" alt="Machining Times" style="width: 140px; height: 140px;">
			<h2>Machining Times</h2>
			<p>This area is for timing the machinging of parts.  BAZ operations are included in this area.</p>
			<p>
				<a class="btn btn-primary" href="machining.php" role="button">Machining Times</a>
			</p>
		</div>-->
	</div>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/validator.js"></script>
<script src="js/timestudy.js"></script>

</body>
</html>