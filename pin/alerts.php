<?php
require 'includes/check_login.php';
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
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<!-- Custom styles for this template -->
	<link href="css/jquery-ui.css" rel="stylesheet">
	<link href="css/main.css" rel="stylesheet">
	<link href="css/jquery.dataTables.css" rel="stylesheet" type="text/css">
	<link href="css/jquery-ui.theme.css" rel="stylesheet">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>

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
	<li><a href="..">Home</a></li>
	<li Class="active">Alerts</a></li>
</ol>
<div class="container-fluid">
	<div class="row col-md-12 spacer">
		<div class="panel panel-primary">
			<div class="panel-heading">Alerts</div>
			<div class="panel-body">
				<table id="table_id" class="display table">
					<thead>
						<tr>
							<th></th>
							<th>Priority</th>
							<th>Date</th>
							<th>Time</th>
							<th>Message</th>
							<th>From</th>
							<th>Command</th>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery-ui.js"></script>
<script src="js/jquery.dataTables.js" type="text/javascript" charset="utf8"></script>
<script src="js/dataTables.tableTools.min.js"></script>
<script src="js/dataTables.colVis.js"></script>
<script>

$(document).ready(function() {
	var authWO = <?php echo $_SESSION['user_authWO']; ?>;
	var user_id = <?php echo $_SESSION['user_id']; ?>;
	table = $('#table_id').DataTable( {
		"bProcessing": true,
		"sAjaxDataProp":"",
		"ajax": "ajax/getalerts.php?userid="+user_id,
		"aaSorting": [],
		"aoColumns": [
			{	"data": "mark",
				"sWidth": "2%",
				"orderable": false
			},
			{ "data": "Priority", "sWidth": "5%" },
			{ "data": "Date", "sWidth": "10%" },
			{ "data": "Time", "sWidth": "10%" },
			{ "data": "Message", "sWidth": "53%" },
			{ "data": "From", "sWidth": "10%" },
			{ "data": null, "sWidth": "10%", "bSortable": false, "mRender": function(data, type, full){
				if(data.id > 0){
					return '<a class="btn btn-info btn-sm" href=' + data.link + '&alert=' + data.id + '>View</a>&nbsp;&nbsp;<button id=delete-' + data.id + ' type="button" class="btn btn-danger btn-sm" >Delete</button>';
				}else{
					return '';
				}
			}},
		],
		"fnRowCallback": function( nRow, data, iDisplayIndex ) {
			try{
				if(data.viewed == 0){
					$(nRow).addClass("info");
				}
			} catch(ex){
				alert("fnRowCallback exception:");
			}
			return nRow
		},
		"createdRow": function ( row, data, index ) {
			if(data.status == 3){
				$('td', row).eq(0).addClass('text-danger high-importance');
			}else if (data.status == 2){
				$('td', row).eq(0).addClass('text-primary med-importance');
			}
        }
	});	
	//Delete alert
	$( "#table_id" ).on( "click", "[id^=delete-]", function() {
		var buttonId = this.id;
		var arr = buttonId.split('-');
		buttonId = arr[1];
		//table.row('.selected').remove().draw( false );
		table.row($(this).parents('tr')).remove().draw(false);
		var request = $.getJSON("ajax/deletealert.php", {alertid : buttonId, userid : user_id}, function(data) {
			console.log(data);
			
			$.each(data, function(key, value) {
				//alert (value.alerts);
				$('.badge').text(value.alerts);
			});
		});
		
	});
	
});

</script>
</body>
</html>