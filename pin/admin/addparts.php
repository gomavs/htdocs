<?php
require '../includes/check_login.php';
if($_SESSION['user_auth_level'] < 10){
	if($_SESSION['user_authWO'] < 10 && $_SESSION['user_authTS'] < 10){
		header('location: ../unauthorized.php');
	}
}
$return_data = "";
if(isset($_POST["partnumber"])){
	$ul_count = 0;
	$last_level = 0;
	$query = $db->prepare("SELECT * FROM part WHERE partnumber = ?");
	$query2 = $db->prepare("SELECT * FROM part WHERE parentid = ?");
	$query->bind_param("s", $_POST["partnumber"]);
	$query->execute();
	$result = $query->get_result();
	$row = $result->fetch_assoc();
	$query = $db->prepare("SELECT * FROM part WHERE parentid = ?");
	$query->bind_param("i", $row['id']);
	$query->execute();
	$result = $query->get_result();
	$row_cnt = $result->num_rows;
	if($row_cnt > 0){
		$return_data .= "<li class=\"momma\" id=\"".$row['id']."\">".$row['partnumber']." "."<span>".$row['partdesc']."</span>";
	}else{
		$return_data .= "<li class=\"items\" id=\"".$row['id']."\">".$row['partnumber']." "."<span>".$row['partdesc']."</span></li>";
	}
	display_children($row['id'], 1);
	for($ul_count; $ul_count > 0; $ul_count--){
		$return_data .= "</ul></li>";
	}
}
function display_children($category_id, $level){
	global $query;
	global $query2;
	global $return_data;
	global $ul_count;
	global $last_level;
	$query->bind_param("i", $category_id);
	$query->execute();
	$result = $query->get_result();
	// display each child
	while ($row = $result->fetch_array(MYSQLI_ASSOC)){
		$query2->bind_param("i", $row['id']);
		$query2->execute();
		$result2 = $query2->get_result();
		$row_cnt = $result2->num_rows;
		if($level > $last_level){
			if($row_cnt > 0){
				$return_data .= "<ul  class=\"sub\"><li class=\"momma\" id=\"".$row['id']."\">".$row['partnumber']." "."<span>".$row['partdesc']."</span>";
			}else{
				$return_data .= "<ul  class=\"sub\"><li class=\"items\" id=\"".$row['id']."\">".$row['partnumber']." "."<span>".$row['partdesc']."</span></li>";
			}
			$ul_count = $ul_count + 1;
		}elseif($level < $last_level){
			$return_data .= "</ul></li>";
			if($row_cnt >0){
				$return_data .= "<li class=\"momma\" id=\"".$row['id']."\">".$row['partnumber']." "."<span>".$row['partdesc']."</span>";
			}else{
				$return_data .= "<li class=\"items\" id=\"".$row['id']."\">".$row['partnumber']." "."<span>".$row['partdesc']."</span></li>";
			}
			$ul_count = $ul_count - 1;
		}else{
			if($row_cnt >0){
				$return_data .= "<li class=\"momma\" id=\"".$row['id']."\">".$row['partnumber']." "."<span>".$row['partdesc']."</span>";
			}else{
				$return_data .= "<li class=\"items\" id=\"".$row['id']."\">".$row['partnumber']." "."<span>".$row['partdesc']."</span></li>";
			}
		}
		$last_level = $level;
		// call this function again to display this child's children
		display_children($row['id'], $level+1);
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
	<li><a href="admin.php">Administration</a></li>
	<li class="active">Parts</li>
</ol>
<div class="container-fluid">
	<!-- Stack the columns on mobile by making one full-width and the other half-width -->
	<div class="row">
		<div class="col-md-6">
			<div class="row">
				<form method = "POST">
				<div class="col-md-4"><label>Part Number:</label></div>
				<div class="col-md-5"><input type="text" class="form-control" name="partnumber" id="autocomplete" autofocus placeholder="Enter part number"><input type="submit" style="position: absolute; left: -9999px; width: 1px; height: 1px;"/></div>
				</form>
			</div>
			<ul class="tree">
			<?php echo $return_data; ?>
			</ul>
		</div>
		<div class="col-md-6">
			<div class="row">
				<div class="col-md-5"><label>Part Number:</label></div>
				<div class="col-md-5"><label id="partNumber">Testing</label></div>
			</div>
			<div class="row">
				<table class="table table-hover tabletimes">
					<tr>
						<tr class="xxs">
						<th width=15%>Work Center</th>
						<th width=15%>Machine</th>
						<th width=15%>Date</th>
						<th width=15%>Time</th>
						<th width=15%>Lap</th>
						<th width=7%>Count</th>
						<th width=18%>Action</th>
					</tr>
					</tr>
					<?php
					$result = mysqli_query($db,"SELECT * FROM timestudy.workcenter WHERE type = 1 OR type = 3 AND inservice = 1 ORDER BY center ASC");
					$machine_list = [];
					while($row = mysqli_fetch_array($result)) {
						$mid =  $row['id'];
						$wc = $row['center'];
						$mName = $row['name'];
						$machine_list[] = [$mid];
						echo "<tr class=\"xxs\" id=\"machine-$mid\"><td>".$wc."</td><td >".$mName."</td><td class =\"study_date\"></td><td class=\"elapsed_time\" id=\"runner-$mid\"></td><td class=\"lap_time\" id=\"runnerLap-$mid\"></td><td class=\"counter\" id=\"counter-$mid\"></td><td class =\"do_action\" id=\"$mid\"></td></tr>";
					}
					?>
				</table>
			</div>
		
		</div>
	</div>
	
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript" src="../js/jquery.autocomplete.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/timeStudy.js"></script>
<script src="../js/jquery.runner-min.js" type="text/javascript"></script>
<script>
	window.machine_list = <?php echo json_encode($machine_list); ?>;
	window.partId = "";
	$(".tree li:has(ul)").addClass("parent").click(function(event) {
		$(this).toggleClass("open");
		event.stopPropagation();
	});
	
	$(function() {
		$('.tree ul.sub').hide();
		$(".tree li:has(.sub)").click(function() {
			$("ul", this).toggle('slow');
		});
		$(".tree li").click(function(event) {
			event.stopPropagation();
		});
	});

	$(".tree li").mouseover(function(event) {
		$(this).addClass("hover");
		event.stopPropagation();
	});
	$(".tree li").mouseout(function(event) {
		$(this).removeClass("hover");
		event.stopPropagation();
	});
	
	$(function(){
		$('#autocomplete').autocomplete({
			serviceUrl:"../ajax/search.php",
			onSelect: function(suggestion) {
				console.log(suggestion);
			}
		});
	});
	$(".items").click(function() {
		partId = this.id;
		var rowId = this.id;
		var partNumber = $(this).text();
		var arr = partNumber.split(' ');
		partNumber = arr[0];
		$(".boom").removeClass("boom");
		$(this).addClass("boom");
		var request = $.getJSON("../ajax/gettimes.php", {id : rowId}, function(data) {
			console.log(data);
			$("#partNumber").html(partNumber);
			$.each(machine_list, function(k, v){
				$("#runner-" + v).runner('stop');
				$("#runner-" + v).runner('reset');
				$("#runnerLap-" + v).runner('stop');
				$("#runner-Lap" + v).runner('reset');
				var start_button = "<button id=\"startTimer-"+ v +"\" type=\"button\" class=\"btn btn-success btn-xs \">Start</button>";
				$("#machine-" + v + " td.study_date").html(" ");
				$("#machine-" + v + " td.elapsed_time").html(" ");
				$("#machine-" + v + " td.lap_time").html(" ");
				$("#machine-" + v + " td.counter").html(" ");
				$("#machine-" + v + " td.do_action").html(start_button);
				$("#runner-" + v).runner({autostart: false, milliseconds: false});//Overall timer initialize
				$("#runnerLap-" + v).runner({autostart: false, milliseconds: false});//Lap timer initialize
			});
			$.each(data, function(key, value) {
				var a = new Date(value.first_start * 1000);
				var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
				var year = a.getFullYear();
				var month = months[a.getMonth() - 1];
				var date = a.getDate();
				var timeDiff = value.avg_time;
				var seconds = Math.round(timeDiff % 60);
				timeDiff = Math.floor(timeDiff / 60);
				var minutes = Math.round(timeDiff % 60);
				timeDiff = Math.floor(timeDiff / 60);
				var hours = Math.round(timeDiff % 24);
				timeDiff = Math.floor(timeDiff / 24);
				var days = timeDiff;
				//var elapsed_time = days + " days, " + hours + ":" + minutes + ":" + seconds;
				if(!value.end_time){
					var elapsed_time = " ";
				}else{
					var elapsed_time = hours + "hr " + minutes + "m " + seconds + "s";
				}
				if(!value.last_end){
					var elapsed_time = "";
					var time_now = new Date().getTime();
					var start_time = value.first_start * 1000;
					startRunner = time_now - start_time;
					$("#runner-" + value.machine_id).runner({
						startAt: startRunner, 
						milliseconds: false
					});
					$("#runner-" + value.machine_id).runner('start');//Start overall runner
					var start_time = value.last_start * 1000;
					startRunner = time_now - start_time;
					$("#runnerLap-" + value.machine_id).runner({
						startAt: startRunner, 
						milliseconds: false
					});
					$("#runnerLap-" + value.machine_id).runner('start');//Start Lap runner
					var action_button = "<button id=\"lapTimer-"+ value.machine_id +"\"type=\"button\" class=\"btn btn-info btn-xs\">Lap</button>   <button id=\"stopTimer-"+ value.machine_id +"\" type=\"button\" class=\"btn btn-danger btn-xs\">Stop</button>";
				}else{
					var elapsed_time = hours + "hr " + minutes + "m " + seconds + "s";
					$("#machine-" + value.machine_id + " td.elapsed_time").html(elapsed_time);
					if(value.completed == 1){
						var action_button = "<button id=\"resetTimer-"+ value.machine_id +"\" type=\"button\" class=\"btn btn-warning btn-xs\">Reset</button>";
					}else{
						var action_button = "<button id=\"resetTimer-"+ value.machine_id +"\" type=\"button\" class=\"btn btn-warning btn-xs\">Reset</button>  <button id=\"doneTimer-"+ value.machine_id +"\" type=\"button\" class=\"btn btn-primary btn-xs\">Done</button>";
					}
				}
				
				$("#machine-" + value.machine_id + " td.study_date").html(month + " " + date + ", " + year);
				$("#machine-" + value.machine_id + " td.elapsed_time").html(elapsed_time);
				$("#machine-" + value.machine_id + " td.do_action").html(action_button);
			});
			
		});
	});
	$(".momma").click(function() {
		$.each(machine_list, function(k, v){
			$("#partNumber").html(" ");
			$("#runner-" + v).runner('stop');
			$("#runner-" + v).runner('reset');
			$("#machine-" + v + " td.study_date").html(" ");
			$("#machine-" + v + " td.elapsed_time").html(" ");
			$("#machine-" + v + " td.do_action").html(" ");
		});
	});
	//Start Timer button
	$( ".do_action" ).on( "click", "[id^=startTimer-]", function() {
		var buttonId = this.id;
		var arr = buttonId.split('-');
		buttonId = arr[1];
		$("#runner-" + buttonId).runner('start');
		$("#runnerLap-" + buttonId).runner('start');
		var action_button = "<button id=\"lapTimer-"+ buttonId +"\"type=\"button\" class=\"btn btn-info btn-xs\">Lap</button>   <button id=\"stopTimer-"+ buttonId +"\"type=\"button\" class=\"btn btn-danger btn-xs\">Stop</button>";
		$("#machine-" + buttonId + " td.do_action").html(action_button);
		var request = $.getJSON("../ajax/starttimes.php", {id : partId, machine : buttonId}, function(data) {
			console.log(data);
			$.each(data, function(key, value) {
				var a = format_date(value.start_time);
				$("#machine-" + buttonId + " td.study_date").html(a);
				$("#counter-" + buttonId).text(1);
			});
		});
	});
	//Lap timer button
	$( ".do_action" ).on( "click", "[id^=lapTimer-]", function() {
		var buttonId = this.id;
		var arr = buttonId.split('-');
		buttonId = arr[1];
		$("#runnerLap-" + buttonId).runner('stop');
		$("#runnerLap-" + buttonId).runner('reset', true);
		$("#runnerLap-" + buttonId).runner('start');
		var count = parseInt($("#counter-" + buttonId).html());
		count = count + 1;
		$("#counter-" + buttonId).text(count);
		var request = $.getJSON("../ajax/laptimes.php", {id : partId, machine : buttonId}, function(data) {
			console.log(data);
			
		});
	});
	//Stop Timer button
	$( ".do_action" ).on( "click", "[id^=stopTimer-]", function() {
		var buttonId = this.id;
		var arr = buttonId.split('-');
		buttonId = arr[1];
		$("#runner-" + buttonId).runner('stop');
		$("#runnerLap-" + buttonId).runner('stop');
		var action_button = "<button id=\"resetTimer-"+ buttonId +"\"type=\"button\" class=\"btn btn-warning btn-xs\">Reset</button>  <button id=\"doneTimer-"+ buttonId +"\"type=\"button\" class=\"btn btn-primary btn-xs\">Done</button>";
		$("#machine-" + buttonId + " td.do_action").html(action_button);
		var request = $.getJSON("../ajax/updatetimes.php", {id : partId, machine : buttonId}, function(data) {
			console.log(data);
			
		});
	});
	
	$( ".do_action" ).on( "click", "[id^=resetTimer-]", function() {
		var buttonId = this.id;
		var arr = buttonId.split('-');
		buttonId = arr[1];
		$("#runner-" + buttonId).runner('reset');
		var action_button = "<button id=\"startTimer-"+ buttonId +"\"type=\"button\" class=\"btn btn-success btn-xs\">Start</button>";
		$("#machine-" + buttonId + " td.do_action").html(action_button);
		$("#machine-" + buttonId + " td.study_date").html(" ");
		$("#counter-" + buttonId).text("");
		$("#runnerLap-" + buttonId).runner('stop');
		$("#runnerLap-" + buttonId).runner('reset');
		var request = $.getJSON("../ajax/removetimes.php", {id : partId, machine : buttonId}, function(data) {
			console.log(data);
			$("#machine-" + buttonId + " td.study_date").html(" ");
			
		});
	});
	
	$( ".do_action" ).on( "click", "[id^=doneTimer-]", function() {
		var buttonId = this.id;
		var arr = buttonId.split('-');
		buttonId = arr[1];
		$("#runner-" + buttonId).runner('stop');
		$("#runnerLap-" + buttonId).runner('stop');
		var action_button = "<button id=\"resetTimer-"+ buttonId +"\"type=\"button\" class=\"btn btn-warning btn-xs\">Reset</button>";
		$("#machine-" + buttonId + " td.do_action").html(action_button);
		var request = $.getJSON("../ajax/finishtimes.php", {id : partId, machine : buttonId, userid : user_id}, function(data) {
			console.log(data);
			
		});
	});
	
</script>
</body>
</html>