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
	<li class="active">Tools</li>
</ol>
<div class="container-fluid">
	<div class="panel panel-primary">
		<div class="panel-heading">Add Problem Types</div>
			<div class="panel-body">
				<div class="row col-md-12">
					<div id="exTab2">	
						<ul class="nav nav-tabs" id="assignment">
							<li class="active"><a href="#tab1" data-toggle="tab">Machines</a></li>
							<li><a href="#tab2" data-toggle="tab">Facility</a></li>
							<li><a href="#tab3" data-toggle="tab">Safety</a></li>
							<li><a href="#tab4" data-toggle="tab">Tools</a></li>
						</ul>
						<div class="tab-content">
							<!-- Machines Pane -->
							<div class="tab-pane active" id="tab1" >
								<div class="row col-md-12 spacer">
									<div class="row LbtnMargin">
										<div class="col-md-2">
											<form role="form" method="post" id="machineproblems">
												<div class="form-group">
													<div class="row ">
														<label for="newProblem" class="control-label">Add Problem</label>
													</div>
													<div class="row RbtnMargin">
														<div class="input-group">
															<input type="text" class="form-control" id="newProblem" name="newProblem" placeholder="New Machine Problem">
															<span class="input-group-btn">
																<button class="btn btn-default" type="button" id="addProblem">
																	<span class=" glyphicon glyphicon-arrow-right"></span>
																</button>
															</span>
														</div>										
													</div>
												</div>
											</form>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<div class="row">
													<label for="problemList" class="control-label">Problem List</label>
												</div>
												<div class="row RbtnMargin">
													<div class="panel panel-primary">
														<div class="panel-body panel-max">
															<table id="clickableRow">
																<tbody id="remove">
																<?php
																$query = $db->prepare("SELECT * FROM machineproblems WHERE active = 1 ORDER BY problem ASC");
																$query->execute();
																$result = $query->get_result();
																while (($row = $result->fetch_object()) !== NULL) {
																?>	
																	<tr id="<?php echo $row->id; ?>"><td><?php echo $row->problem; ?></td></tr>
																<?php
																}
																?>
																</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-8">
											<div class="form-group">
												<div class="row leftPush">
													<label for="check_list[]" class="control-label">Machines</label>
												</div>
												<div class="panel panel-primary" id="machineList">
													<div class="panel-body panel-fixed">
														<form class="machines" name="machines">
															<input type="hidden" id="problemId" name="problemId" value="">
															<?php
															$i = 1;
															$j = 0;
															$query = $db->prepare("SELECT * FROM workcenter ORDER BY center ASC");
															$query->execute();
															$result = $query->get_result();
															$row_cnt = mysqli_num_rows($result);
															while (($row = $result->fetch_object()) !== NULL) {
																$j++;
																if($i <= 10){
																	if($i == 1){
																	?>
																		<div class="col-md-3">
																	<?php
																	}
															?>
																<div class="row">
																	<div class="checkbox"><label><input type="checkbox" name="check_list[]" value="<?php echo $row->id ?>">Center <?php echo $row->center; ?>&nbsp;&nbsp;<?php echo $row->name; ?></label></div>
																</div>
															<?php
																	if($i == 10){
																		$i = 1;
																	?>	
																		</div>
																	<?php
																	}else{
																		$i++;
																	}
																}
																if($j == $row_cnt){
																	?>
																	</div>
																	<?php
																}
															}
															?>
														</form>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row LbtnMargin">
										<div class="form-group">
											<div class="col-md-2 col-md-offset-4">
												<div class="checkbox"><label><input type="checkbox" id="checkAll" name="checkAll" value="1">Check all</label></div>
											</div>
											<button class="btn btn-primary" type="submit" id="saveChanges">Save Changes</button>
										</div>
									</div>
								</div>
							</div>
							<!-- Facility Pane -->
							<div class="tab-pane" id="tab2" >
								<div class="row col-md-12 spacer">
									<div class="row LbtnMargin">
										<div class="col-md-2">
											<form role="form" method="post" id="facilityproblems">
												<div class="form-group">
													<div class="row ">
														<label for="newProblem" class="control-label">Add Problem</label>
													</div>
													<div class="row RbtnMargin">
														<div class="input-group">
															<input type="text" class="form-control" id="newFacilityProblem" name="newFacilityProblem" placeholder="New Facility Problem">
															<span class="input-group-btn">
																<button class="btn btn-default" type="button" id="addFacilityProblem">
																	<span class=" glyphicon glyphicon-arrow-right"></span>
																</button>
															</span>
														</div>
													</div>										
												</div>
											</form>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<div class="row">
													<label class="control-label">Problem List</label>
												</div>
												<div class="row RbtnMargin">
													<div class="panel panel-primary">
														<div class="panel-body panel-max">
															<table id="clickableRowFacility">
																<tbody id="removeFacility">
																<?php
																$query = $db->prepare("SELECT * FROM facilitytype WHERE active = 1 ORDER BY item ASC");
																$query->execute();
																$result = $query->get_result();
																while (($row = $result->fetch_object()) !== NULL) {
																?>	
																	<tr id="<?php echo $row->id; ?>"><td><?php echo $row->item; ?></td></tr>
																<?php
																}
																?>
																</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- Safety Pane -->
							<div class="tab-pane" id="tab3" >
								<div class="row col-md-12 spacer">
									<div class="row LbtnMargin">
										<div class="col-md-2">
											<form role="form" method="post" id="safetyproblems">
												<div class="form-group">
													<div class="row ">
														<label for="newSafetyProblem" class="control-label">Add Problem</label>
													</div>
													<div class="row RbtnMargin">
														<div class="input-group">
															<input type="text" class="form-control" id="newSafetyProblem" name="newSafetyProblem" placeholder="New Safety Problem">
															<span class="input-group-btn">
																<button class="btn btn-default" type="button" id="addSafetyProblem">
																	<span class=" glyphicon glyphicon-arrow-right"></span>
																</button>
															</span>
														</div>
													</div>										
												</div>
											</form>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<div class="row">
													<label class="control-label">Problem List</label>
												</div>
												<div class="row RbtnMargin">
													<div class="panel panel-primary">
														<div class="panel-body panel-max">
															<table id="clickableRowSafety">
																<tbody id="removeSafety">
																<?php
																$query = $db->prepare("SELECT * FROM safetytype WHERE active = 1 ORDER BY item ASC");
																$query->execute();
																$result = $query->get_result();
																while (($row = $result->fetch_object()) !== NULL) {
																?>	
																	<tr id="<?php echo $row->id; ?>"><td><?php echo $row->item; ?></td></tr>
																<?php
																}
																?>
																</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- Tools Pane -->
							<div class="tab-pane" id="tab4" >
								<div class="row col-md-12 spacer">
									<div class="row LbtnMargin">
										<div class="col-md-2">
											<form role="form" method="post" id="toolsproblems">
												<div class="form-group">
													<div class="row ">
														<label for="newToolsProblem" class="control-label">Add Problem</label>
													</div>
													<div class="row RbtnMargin">
														<div class="input-group">
															<input type="text" class="form-control" id="newToolsProblem" name="newToolsProblem" placeholder="New Tools Problem">
															<span class="input-group-btn">
																<button class="btn btn-default" type="button" id="addToolsProblem">
																	<span class=" glyphicon glyphicon-arrow-right"></span>
																</button>
															</span>
														</div>
													</div>										
												</div>
											</form>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<div class="row">
													<label class="control-label">Problem List</label>
												</div>
												<div class="row RbtnMargin">
													<div class="panel panel-primary">
														<div class="panel-body panel-max">
															<table id="clickableRowTools">
																<tbody id="removeTools">
																<?php
																$query = $db->prepare("SELECT * FROM toolstype WHERE active = 1 ORDER BY item ASC");
																$query->execute();
																$result = $query->get_result();
																while (($row = $result->fetch_object()) !== NULL) {
																?>	
																	<tr id="<?php echo $row->id; ?>"><td><?php echo $row->item; ?></td></tr>
																<?php
																}
																?>
																</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							
							
							<!-- End Panes -->
						</div>
					</div>
				</div>
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
<script>

$(document).ready(function() {
	$("#machineList :input").attr("disabled", true);
	$("#checkAll").attr("disabled", true);
	$('#clickableRow').on('click', 'tr',function(){
		$('input:checkbox').prop('checked', false);
		var rowId = this.id;
		var selected = $(this).hasClass("highlight");
		$("#clickableRow tr").removeClass("highlight");
		$("#machineList :input").attr("disabled", true);
		$("#checkAll").attr("disabled", true);
		$("#problemId").val("");
		//$('input:checkbox').prop('checked', false);
		//$('input:checkbox').not(this).prop('checked', this.checked);
		if(!selected){
			$(this).addClass("highlight");
			$("#machineList :input").attr("disabled", false);
			$("#checkAll").attr("disabled", false);
			$("#problemId").val(rowId);
			var request = $.getJSON("../ajax/retrievechecks.php", {id : rowId}, function(data) {
				console.log(data);
				//data = JSON.parse(data);
				$.each(data, function(i,item) {
					$("input:checkbox[value=" + item.machineId + "]").prop('checked', true);
					
				});
			});
		}
	});
	//Check all the check boxes 
	$("#checkAll").click(function () {
		$('input:checkbox').not(this).prop('checked', this.checked);
	});
	//submit new problem
	$("#addProblem").click(function() {
		var problem = $("#newProblem").val();
		var probLength = problem.length;
		if(probLength >= 5){
			var request = $.getJSON("../ajax/machineproblem.php", {type : problem}, function(data) {
				console.log(data);
				$("#newProblem").val("");
				$("#remove").remove();
				$('#clickableRow').append("<tbody id=\"remove\"></tbody>");
				$.each(data, function(i,item) {
					if(item.problem == problem){
						$('#remove').append( "<tr id=" + item.id + " class=\"highlight\"><td>" + item.problem + "</td></tr>");
						$("#machineList :input").attr("disabled", false);
						$("#checkAll").attr("disabled", false);
					}else{
						$('#remove').append( "<tr id=" + item.id + "><td>" + item.problem + "</td></tr>");
					}
				});
				
			});
		}
	});
	//Submit checked items
	$("button#saveChanges").click(function(){
		$.ajax({
			type: "POST",
			url: "../ajax/updateproblems.php",
			data: $('form.machines').serialize(),
			success: function(data){
				console.log(data);
				alert("Changes Saved");
				//data = jQuery.parseJSON(data);
				/*$.each(data, function(key, value) {
					$("#due_date").html("<small>" + value.due_date + "</small>");
					$("#estimated_time").html("<small>" + value.estimated_time + "</small>");
					$("#approval_notes").html(value.approval_notes);
					$(".assignment").remove();
					$("#assigned_job").append("<div class=\"assignment\"></div>");
					$.each(value.selected_users, function(j, id){
						//updated displayed users on work order
						var a = "<div class=\"row\"><div class=\"col-md-5\"><small>" + id.firstname + " " + id.lastname + "</small></div></div>";
						$(".assignment").append(a);
					});
					
					
					
				});*/
			},
			error: function(){
				alert("failure");
			}
		});
	});
	//Submit new facility problem
	$("#addFacilityProblem").click(function() {
		var facilityProblem = $("#newFacilityProblem").val();
		var facilityProbLength = facilityProblem.length;
		if(facilityProbLength >= 5){
			var request = $.getJSON("../ajax/facilityproblem.php", {type : facilityProblem}, function(data) {
				console.log(data);
				$("#newFacilityProblem").val("");
				$("#removeFacility").remove();
				$('#clickableRowFacility').append("<tbody id=\"removeFacility\"></tbody>");
				$.each(data, function(i,item) {
					$('#removeFacility').append( "<tr id=" + item.id + "><td>" + item.problem + "</td></tr>");
					/*if(item.problem == facilityProblem){
						$('#removeFacility').append( "<tr id=" + item.id + " class=\"highlight\"><td>" + item.problem + "</td></tr>");
						$("#machineList :input").attr("disabled", false);
						$("#checkAll").attr("disabled", false);
					}else{
						$('#removeFacility').append( "<tr id=" + item.id + "><td>" + item.problem + "</td></tr>");
					}*/
				});
				
			});
		}
	});
	//Submit new safety problem
	$("#addSafetyProblem").click(function() {
		var safetyProblem = $("#newSafetyProblem").val();
		var safetyProbLength = safetyProblem.length;
		if(safetyProbLength >= 5){
			var request = $.getJSON("../ajax/safetyproblem.php", {type : safetyProblem}, function(data) {
				console.log(data);
				$("#newSafetyProblem").val("");
				$("#removeSafety").remove();
				$('#clickableRowSafety').append("<tbody id=\"removeSafety\"></tbody>");
				$.each(data, function(i,item) {
					$('#removeSafety').append( "<tr id=" + item.id + "><td>" + item.problem + "</td></tr>");
					/*if(item.problem == safetyProblem){
						$('#removeSafety').append( "<tr id=" + item.id + " class=\"highlight\"><td>" + item.problem + "</td></tr>");
						$("#machineList :input").attr("disabled", false);
						$("#checkAll").attr("disabled", false);
					}else{
						$('#removeSafety').append( "<tr id=" + item.id + "><td>" + item.problem + "</td></tr>");
					}*/
				});
				
			});
		}
	});
	//Submit new tools problem
	$("#addToolsProblem").click(function() {
		var toolsProblem = $("#newToolsProblem").val();
		var toolsProbLength = toolsProblem.length;
		if(toolsProbLength >= 5){
			var request = $.getJSON("../ajax/toolsproblem.php", {type : toolsProblem}, function(data) {
				console.log(data);
				$("#newToolsProblem").val("");
				$("#removeTools").remove();
				$('#clickableRowTools').append("<tbody id=\"removeTools\"></tbody>");
				$.each(data, function(i,item) {
					$('#removeTools').append( "<tr id=" + item.id + "><td>" + item.problem + "</td></tr>");
					/*if(item.problem == toolsProblem){
						$('#removeTools').append( "<tr id=" + item.id + " class=\"highlight\"><td>" + item.problem + "</td></tr>");
						$("#machineList :input").attr("disabled", false);
						$("#checkAll").attr("disabled", false);
					}else{
						$('#removeTools').append( "<tr id=" + item.id + "><td>" + item.problem + "</td></tr>");
					}*/
				});
				
			});
		}
	});
});
</script>

</body>
</html>
