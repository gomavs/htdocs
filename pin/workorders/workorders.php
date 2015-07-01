<?php
require '../includes/check_login.php';
$data = [];
$data2 = [];
$lastWeek = strtotime("-1 week");
//echo $lastWeek;

$query = $db->prepare("SELECT workcenter.id, workcenter.center, workcenter.name, COUNT(workorder.id) as c FROM workorder LEFT JOIN workcenter ON workorder.workcenterId = workcenter.id WHERE workorder.startDate >= ? AND workorder.status=1 GROUP BY workcenter.id ORDER BY c DESC LIMIT 5 ");
$query->bind_param("i", $lastWeek);
$query->execute();
$result = $query->get_result();
while (($row = $result->fetch_object()) !== NULL) {
	$data[] = (object)array("label"=>$row->name, "value"=>$row->c);
}
//Work request bar graph data
$query = $db->prepare("SELECT worktypes.type, COUNT(workrequest.workTypeId) as c FROM workrequest LEFT JOIN worktypes ON workrequest.workTypeId = worktypes.id WHERE workrequest.timestamp >= ? GROUP BY workrequest.workTypeId ORDER BY c DESC LIMIT 5 ");
$query->bind_param("i", $lastWeek);
$query->execute();
$result = $query->get_result();
while (($row = $result->fetch_object()) !== NULL) {

	$data2[] = (object)array("type"=>$row->type, "a"=>$row->c);
}

//echo json_encode($data2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>PIN Systems</title>

<!-- Bootstrap -->
	<link href="../css/bootstrap.min.css" rel="stylesheet">
	<!-- Custom styles for this template -->
	<link href="../css/jquery-ui.css" rel="stylesheet">
	<link href="../css/main.css" rel="stylesheet">
	<link href="../css/jquery-ui.theme.css" rel="stylesheet">
	<link rel="stylesheet" href="http://cdn.oesmith.co.uk/morris-0.5.1.css">
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
	
	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>

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
	<li><a href="..">Home</a></li>
	<li Class="active">Work Orders</a></li>
</ol>
<div class="container-fluid">
	<div class="row col-md-12 spacer">
		<div class="col-md-3">
			<div class="panel panel-primary">
				<div class="panel-heading">Top 5 Machines Worked Over The Last 7 Days</div>
				<div class="panel-body">
					<div class="row">
						<div id="comparemachines" style="height: 300px;"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-5">
			<div class="panel panel-primary">
				<div class="panel-heading">Top 5 Work Request Over the Last 7 Days</div>
				<div class="panel-body">
					<div class="row">
						<div id="workrequests" style="height: 300px;"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-1">
			<div class="row <?php if($row_cnt == 0){ ?> hidden <?php } ?> ">
				<a href="myworkorders.php" class="btn btn-info btn-block">My Work Orders</a>
			</div>
			<div class="row <?php if($row_cnt > 0){ ?> spacer <?php } ?>">
				<a href="requestwork.php" class="btn btn-info btn-block">Request Work</a>
			</div>
			<div class="row spacer">
				<a href="openrequests.php" class="btn btn-info btn-block">Open Requests</a>
			</div>
			<div class="row spacer">
				<a href="workprogress.php" class="btn btn-info btn-block">Work In Progress</a>
			</div>
			<div class="row spacer">
				<a href="closedwork.php" class="btn btn-info btn-block">Closed Work</a>
			</div>
		</div>
	</div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>

<script>

$(document).ready(function() {
	window.data = <?php echo json_encode($data); ?>;
	window.data2 = <?php echo json_encode($data2) ?>;
	console.log(data2);
	Morris.Donut({
		// ID of the element in which to draw the chart.
		element: 'comparemachines',
		// Data for the Chart
		data: data
	});
	Morris.Bar({
		element: 'workrequests',
		data: data2,
		/*data: [
			{"type":"Machinery","a":5, "b":3},
			{"type":"Facility","a":6, "b":7}
		],*/
		xkey: 'type',
		ykeys: ['a'],
		labels: ['Qty']
	});
	
});

</script>
</body>
</html>