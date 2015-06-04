<?php
require '../includes/check_login.php';

if(isset($_POST['submit'])){
	//echo "test";
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$email = $_POST['email'];
	$mobile = $_POST['mobile'];
	$hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
	$department = $_POST['selectDepartment'];
	$active = $_POST['active'];
	if(isset($_POST['partCheck'])){
		$partCheck = 1;
		$tsAuthLevel = $_POST['tsAuthLevel'];
	} else {
		$partCheck = 0;
		$tsAuthLevel = 0;
	}
	if(isset($_POST['workCheck'])){
		$workCheck = 1;
		$woAuthLevel = $_POST['woAuthLevel'];
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
	echo $firstname." ".$lastname."<br>";
	echo $email." ".$mobile."<br>";
	echo $_POST['password']." ".$active." ".$permissions." ".$tsAuthLevel." ".$woAuthLevel." ".$authlevel;
	
	mysqli_query($db,"INSERT INTO users (firstname, lastname, email, password, cell, authlevel, permissions, authTS, authWO, department, active) VALUES ('$firstname', '$lastname','$email', '$hashed_password', '$mobile', '$authlevel', '$permissions', '$tsAuthLevel', '$woAuthLevel', '$department', '$active')");
	//header("location:listusers.php");
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
	<li class="active">Add User</li>
</ol>
<div class="container-fluid">
	<div class="panel panel-primary">
		<div class="panel-heading">Add User</div>
		<div class="panel-body">
			<div class="row col-md-12">
				<div class ="col-md-1 pull-right"><a href="listusers.php" ><button type="button" class="btn btn-primary btn-sm">List Users</button></a></div>
				<div class ="col-md-1 pull-right"><a href="edituser.php" ><button type="button" class="btn btn-primary btn-sm">Edit Users</button></a></div>
			</div>
			<form role="form" method="post" id="add">
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
												<div class="col-md-12"><label for="inputPassword" class="control-label">Password</label></div>
												<div class="col-md-12"><input type="password" data-minlength="5" class="form-control" id="inputPassword" name="password" placeholder="Password" tabindex="5" required></div>
											</div>
										</div>
										<div class="form-group">
											<div class="row ">
												<div class="col-md-12"><label for="selectDepartment" class="control-label">Department</label></div>
												<div class="col-md-12">
													<select id="selectDepartment" name="selectDepartment" class="form-control">
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
												<div class="col-md-12"><input type="text" class="form-control" id="inputMobile" name="mobile" placeholder="Mobile Number" tabindex="4"></div>
											</div>
										</div>
										<div class="form-group">
											<div class="row ">
												<div class="col-md-12"><label for="inputPasswordConfirm" class="control-label">Confirm Password</label></div>
												<div class="col-md-12"><input type="password" class="form-control" id="inputPasswordConfirm" name="confirmPassword" data-match="#inputPassword" data-match-error="Whoops, these don't match" placeholder="Confirm Password" tabindex="6" required></div>
												<div class="col-md-12"><div class="help-block with-errors"></div></div>
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
											<select class="form-control" name="tsAuthLevel" id="partDrop" tabindex="11" disabled>
												<option value="1">Time Keeper</option>
												<option value="2">Supervisor</option>
												<option value="4">Pin Manager</option>
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
											<select class="form-control" name="woAuthLevel" id="workDrop" tabindex="13" disabled>
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
					<div class="col-md-12">
						<div class="row spacer">
							<div class="form-group col-md-12">
								<button type="submit" name="submit" class="btn btn-primary" formmethod="post" tabindex="15">Submit</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/validator.js"></script>
<script src="../js/jquery-ui.js"></script>
<script>
	var superAdmin = <?php echo $_SESSION['user_auth_level']; ?>;
	$(document).ready(function() {
		$("#partCheck").click(function() {
			if ($(this).is(":checked")) {
				$("#partDrop").prop("disabled", false);
			} else {
				$("#partDrop").prop("disabled", true);  
			}
		});
		$("#workCheck").click(function() {
			if ($(this).is(":checked")) {
				$("#workDrop").prop("disabled", false);
			} else {
				$("#workDrop").prop("disabled", true);  
			}
		});
		if(superAdmin == 10){
			$("#superCheck").prop("disabled", false);
		} else {
			$("#superCheck").prop("disabled", true);
		};
		$('#add').validator({
			rules: {
				email:{required: true, email: true,	remote: "../ajax/check-email.php"}
			},
			messages: {
				email:{required: "that email address is already in use."}
			}
		});
	});

	
</script>
</body>
</html>
