<?php
require '../includes/check_login.php';
//require_once '../includes/dbConnect.php';

$user_list = "<select id=\"chooseUser\" class=\"form-control\" name=\"chooseUser\"><option value=\"0\">-- Choose User --</option>";
$query = $db->prepare("SELECT * FROM users ORDER BY lastname ASC");
$query->execute();
$result = $query->get_result();
while (($row = $result->fetch_object()) !== NULL) {
	$user_id = $row->id;
	$firstname = $row->firstname;
	$lastname = $row->lastname;
	if($_SESSION['user_auth_level'] >= $row->authlevel){
		$user_list = $user_list."<option value=\"".$user_id."\">".$firstname."&nbsp&nbsp".$lastname."</option>";
	}	
}
$user_list = $user_list."</select>";
if(isset($_POST['submit'])){
	
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$email = $_POST['email'];
	$mobile = $_POST['cell'];
	$department = $_POST['department'];
	$active = $_POST['active'];
	if(isset($_POST['partCheck'])){
		$partCheck = 1;
		$tsAuthLevel = $_POST['authTS'];
	} else {
		$partCheck = 0;
		$tsAuthLevel = 0;
	}
	if(isset($_POST['workCheck'])){
		$workCheck = 1;
		$woAuthLevel = $_POST['authWO'];
	} else {
		$workCheck = 0;
		$woAuthLevel = 0;
	}
	if(isset($_POST['superCheck'])){
		$authlevel = 10;
	}else{
		$authlevel = 0;
	}
	$permissions = $partCheck.",".$workCheck;
	if(isset($_POST['id'])){
		$query = $db->prepare("UPDATE users SET firstname = ?, lastname = ?, email = ?, cell = ?, authlevel = ?, permissions = ?, authTS = ?, authWO = ?, department = ?, active =? WHERE id = ?");
		$query->bind_param("ssssisiiiii", $firstname, $lastname, $email, $mobile, $authlevel, $permissions, $tsAuthLevel, $woAuthLevel, $department, $active, $_POST['id']);
		$query->execute();
	}
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
	<li><a href="..">Home</a></li>
	<li><a href="admin.php">Administration</a></li>
	<li><a href="useradmin.php">User Administration</a></li>
	<li class="active">Edit user</li>
</ol>
<div class="container-fluid">
	<div class="panel panel-primary">
		<div class="panel-heading">Edit User</div>
		<div class="panel-body">
			<div class="row col-md-12">
				<div class ="col-md-1 pull-right"><a href="listusers.php" ><button type="button" class="btn btn-primary btn-sm">List Users</button></a></div>
				<div class ="col-md-1 pull-right"><a href="edituser.php" ><button type="button" class="btn btn-primary btn-sm">Edit Users</button></a></div>
			</div>
			<div class="row col-md-12">
				<div class="col-md-2"><label for="selectUser" class="control-label">Select User</label></div>
				<div class="col-md-2">
					<?php echo $user_list; ?>
				</div>
			</div>
			<form class="hidden" role="form" method="post" id="editUser">
				<div class="row col-md-12">
					<div class="col-md-4">
						<div class="row spacer">
							<div class="panel panel-info">
								<div class="panel panel-heading">Pesonal Information</div>
								<div class="panel-body">
									<input type="hidden" name="id" value=""/>
									<div class="col-md-6">
										<div class="form-group">
											<div class="row ">
												<div class="col-md-12"><label for="inputFirstName" class="control-label">First Name</label></div>
												<div class="col-md-12"><input type="text" class="form-control" id="inputFirstName" name="firstname" placeholder="First Name" tabindex="1" required></div>
											</div>
										</div>
										<div class="form-group">
											<div class="row ">
												<div class="col-md-12"><label for="email" class="control-label">Email</label></div>
												<div class="col-md-12"><input type="email" class="form-control" id="email" name="email" placeholder="Email" tabindex="3" data-error="That email address is invalid" required></div>
												
											</div>
										</div>
										<div class="form-group">
											<div class="row ">
												<div class="col-md-12"><label for="selectDepartment" class="control-label">Department</label></div>
												<div class="col-md-12">
													<select id="selectDepartment" name="department" class="form-control">
														<option value="0">--Choose Department--</option>
														<option value="100">Mill</option>
														<option value="200">QC-Boxing</option>
														<option value="300">Laminate</option>
														<option value="400">Assembly</option>
														<option value="450">Prototypes/Projects</option>
														<option value="500">Shipping</option>
														<option value="550">Receiving</option>
														<option value="575">Warehouse</option>
														<option value="600">Maintenance</option>
														<option value="700">Engineering Admin</option>
														<option value="701">Engineering</option>
														<option value="702">Design</option>
														<option value="703">HR</option>
														<option value="704">Accounting</option>
														<option value="705">IT</option>
														<option value="706">Purchasing</option>
														<option value="707">Sales</option>
														<option value="708">Customer Service/General</option>
														<option value="710">Executive</option>
														<option value="712">Shop Supervisors</option>
													</Select>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<div class="row ">
												<div class="col-md-12"><label for="inputLastName" class="control-label">Last Name</label></div>
												<div class="col-md-12"><input type="text" class="form-control" id="inputLastName" name="lastname" placeholder="Last Name" tabindex="2" required></div>
											</div>
										</div>
										<div class="form-group">
											<div class="row ">
												<div class="col-md-12"><label for="inputMobile" class="control-label">Mobile Number</label></div>
												<div class="col-md-12"><input type="text" class="form-control" id="inputMobile" name="cell" placeholder="Mobile Number" tabindex="4"></div>
											</div>
										</div>
										<div class="form-group">
											<div class="row ">
												<div class="col-md-12"><label for="active" class="control-label">Active</label></div>
												<div class="radio">
													<label class="col-md-4"><input type="radio" name="active" value="1" tabindex="7" required checked>Yes</label>
													<label><input type="radio" name="active" value="0" tabindex="8" required>No</label>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-1">
					
					</div>
					<div class="col-md-4">
						<div class="row spacer">
							<div class="panel panel-info">
								<div class="panel panel-heading">Permissions</div>
								<div class="panel-body">
									<div class="row col-md-12">
										<div class="row col-md-5">
											<div class="checkbox">
												<label><input type="checkbox" id="partCheck" name="partCheck" tabindex="10">Part Timing System</label>
											</div>
										</div>
										<div class="row col-md-7">
											<select class="form-control" name="authTS" id="authTS" tabindex="11" disabled>
												<option value="0"disabled selected>--Choose Option--</option>
												<option value="1">Time Keeper</option>
												<option value="2">Supervisor</option>
												<option value="5">Pin Manager</option>
												<option value="10">Administrator</option>
											</select>
										</div>
									</div>
									<div class="row col-md-12 spacer">
										<div class="row col-md-5">
											<div class="checkbox">
												<label><input type="checkbox" id="workCheck" name="workCheck" tabindex="12">Work Order System</label>
											</div>
										</div>
										<div class="row col-md-7">
											<select class="form-control" name="authWO" id="authWO" tabindex="13" disabled>
												<option value="0" disabled selected>--Choose Option--</option>
												<option value="1">Operator</option>
												<option value="2">Supervisor</option>
												<option value="3">Maintenance Tech</option>
												<option value="4">Senior Maintenance Tech</option>
												<option value="5">Pin Manager</option>
												<option value="10">Administrator</option>
											</select>
										</div>
									</div>
									<div class="row col-md-12 spacer">
										<div class="row col-md-6">
											<div class="checkbox">
												<label><input type="checkbox" id="superCheck" name="superCheck" tabindex="14" disabled>Super Administrator</label>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-1">
					
					</div>
					<div class="col-md-2">
						<div class="row spacer">
							<div class="panel panel-info">
								<div class="panel panel-heading">Reset Password</div>
								<div class="panel-body">
									<div class="row col-md-12 text-center">
										<button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#resetPass">Reset Password</button>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="row spacer">
							<div class="form-group col-md-12">
								<button type="submit" name="submit" class="btn btn" formmethod="post" tabindex="15">Update</button>
							</div>
						</div>
					</div>
				</div>
			</form>
			
			<!-- Reset Pass Modal -->
			<div class="modal fade" id="resetPass" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="hourstModalLabel">Reset Password</h4>
						</div>
						<form class="resetPassord" id="resetPassword">
							<input type="hidden" id="userId" name="userId" value=""/>
							<div class="modal-body">
							
								<div class="row form-group" id="password">
									<div class="col-md-3"><label for="inputPassword">Admin Password</label></div>
									<div class="col-md-4"><input data-minlength="5" type="password" class="form-control" id="adminPassword" name="adminPassword" placeholder="Password" required></div>
								</div>
								<div class="well hidden" id="reset_alert">
									<div class="row text-center" id="alert">
									</div>
									<div class="row text-center" id="message">
										
									</div>
									<div class="row text-center" id="newpassword">
										
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								<button type="button" class="btn btn-warning" id="reset_pass">Reset Password</button>
							<!--	<input class="btn btn-warning" type="submit" value="Reset Password" id="resetPass">-->
							</div>
						</form>
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
<script src="../js/validator.js"></script>
<script>
	window.superUser = <?php echo $_SESSION['user_auth_level']; ?>;
	

	$(document).ready(function(){
		$("#chooseUser").change(function(){
			$("#reset_alert").addClass("hidden");
			$("#reset_pass").removeClass("hidden");
			$("#reset_alert").removeClass("well-success");
			$("#reset_alert").removeClass("well-error");
			var optionValue = $( "#chooseUser" ).val();
			if (optionValue > 0){
				$("form").removeClass("hidden");	
				var request = $.get("../ajax/getuser.php", {id : optionValue}, function(data) {
					console.log(data);
					populate($('#editUser'), $.parseJSON(data));
					
				});
			} else {
				$("form").addClass("hidden");
			}
		})
		$("#partCheck").click(function() {
			if ($(this).is(":checked")) {
				$("#authTS").prop("disabled", false);
			} else {
				$("#authTS").prop("disabled", true);  
			}
		});
		$("#workCheck").click(function() {
			if ($(this).is(":checked")) {
				$("#authWO").prop("disabled", false);
			} else {
				$("#authWO").prop("disabled", true);  
			}
		});
		if(superUser == 10){
			$("#superCheck").prop("disabled", false);
		} else {
			$("#superCheck").prop("disabled", true);
		};
	});
	
	function populate(form, data) {
		console.log(data);
		$.each(data, function(key, value) {
			var $field = $("[name=" + key + "]", form);
			if(key == "id"){
				$("#userId").val(value);
			}
			if(key == "authTS"){
				if(value > 0){
					$("#partCheck").prop("checked", true);
					$("#authTS").prop("disabled", false);
				}else{
					$("#partCheck").prop("checked", false);
					$("#authTS").prop("disabled", true);
				}
			}
			if(key == "authWO"){
				if(value > 0){
					$("#workCheck").prop("checked", true);
					$("#authWO").prop("disabled", false);
				}else{
					$("#workCheck").prop("checked", false);
					$("#authWO").prop("disabled", true);
				}
			}
			if(key == "authlevel"){
				if(value == 10){
					$("#superCheck").prop("checked", true);
				}else{
					$("#superCheck").prop("checked", false);
				}
			}
			
			switch ($field.attr("type")) {
				case "radio":
				case "checkbox":
					$field.each(function(index, element) {
						element.checked = $(element).val() == value
					});
					break;
				default:
					$field.val(value);
			}
			
		});
	}
	$("#reset_pass").click(function(){
		$.ajax({
			type: "POST",
			url: "../ajax/resetpass.php",
			data: $('#resetPassword').serialize(),
			success: function(data){
				console.log(data);
				//$("#requestModal").modal('hide'); //hide popup
				//$("#adminPassword").html("");
				data = jQuery.parseJSON(data);
				$("#reset_alert").removeClass("hidden");
				$.each(data, function(key, value) {
					if(value.alert == "Success"){
						$("#reset_alert").addClass("well-success");
						$("#reset_pass").addClass("hidden");
						$("#newpassword").html("<strong><h3>" + value.new_pass + "</h3></strong>");
						$("#newpassword").addClass("text-primary");
					}else{
						$("#reset_alert").addClass("well-error");
						$("#newpassword").html("");
					}
					$("#alert").html("<h4>" + value.alert + "</h4>");
					$("#message").html(value.message);
					
				});
				
			},
			error: function(){
				alert("failure");
			}
		});
		
	});

</script>
</body>
</html>