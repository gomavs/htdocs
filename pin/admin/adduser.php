<?php
require '../includes/check_login.php';
if($_SESSION['user_auth_level'] < 10){
	if($_SESSION['user_authWO'] < 10 && $_SESSION['user_authTS'] < 10){
		header('location: ../unauthorized.php');
	}
}
if(isset($_POST['submit'])){
	$mobile = "none";
	$carrier = "0";
	$homePage = "";
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$email = $_POST['email'];
	if(isset($_POST['mobile']) && !empty($_POST['mobile'])){
		$mobile = $_POST['mobile'];
	}
	if(isset($_POST['selectCarrier'])){
		$carrier = $_POST['selectCarrier'];
	}
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
	//set home page
	if(isset($_POST['workCheck'])){
		$workCheck = 1;
		$woAuthLevel = $_POST['woAuthLevel'];
	} else {
		$workCheck = 0;
		$woAuthLevel = 0;
	}
	
	if($partCheck == 1 && $workCheck == 1){
		$homePage = "index.php";
	}elseif($partCheck = 1 && $workCheck == 0){
		$homePage = "timestudy/index.php";
	}elseif($partCheck = 0 && $workCheck == 1){
		$homePage = "workorders/workorders.php";
	}
	
	if(isset($_POST['superCheck'])){
		$authlevel = 10;
	}else{
		$authlevel = 0;
	}
	$allowTexts = 1;
	$permissions = $partCheck.",".$workCheck;
	
	mysqli_query($db,"INSERT INTO users (firstname, lastname, email, password, cell, carrierId, authlevel, permissions, authTS, authWO, department, active, homePage, allowTexts) VALUES ('$firstname', '$lastname','$email', '$hashed_password', '$mobile', '$carrier', '$authlevel', '$permissions', '$tsAuthLevel', '$woAuthLevel', '$department', '$active', '$homePage', '$allowTexts')");
	//echo $firstname." ".$lastname." ".$email." ".$hashed_password." ".$mobile." ".$carrier." ".$authlevel." ".$permissions." ".$tsAuthLevel." ".$woAuthLevel." ".$department." ".$active." ".$homePage." ".$allowTexts;

	/*$query = $db->prepare("INSERT INTO messages (firstname, lastname, email, password, cell, carrierId, authlevel, permissions, authTS, authWO, department, active, homePage, allowTexts) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$query->bind_param('sssssiisiiiisi', $firstname, $lastname, $email, $hashed_password, $mobile, $carrier, $authlevel, $permissions, $tsAuthLevel, $woAuthLevel, $department, $active, $homePage, $allowTexts);
	$query->execute();*/
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
	<li><a href="<?php echo $url_home; ?>">Home</a></li>
	<li><a href="admin.php">Administration</a></li>
	<li><a href="useradmin.php">User Administration</a></li>
	<li class="active">Add User</li>
</ol>
<!--Modal for adding new phone carrier-->
	<div class="modal fade" id="carrierModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<form class="form-horizontal" id="carrier" data-toggle="validator" role="form">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="requestModalLabel">Add Phone Carrier</h4>
					</div>
					
						<div class="modal-body lWellPadding">
							<div class="row">
								<div class="form-group">
									<div class="col-md-3">
										<label for="inputCarrierName" class="control-label">Carrier Name</label>
									</div>
									<div class="col-md-4">
										<input type="text" class="form-control" id="inputCarrierName" name="inputCarrierName" placeholder="Carrier Name" required>
									</div>
									<div class="col-md-5">
										<div class="help-block with-errors"></div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<div class="col-md-3">
										<label for="inputCarrierSuffix" class="control-label">Carrier Suffix</label>
									</div>
									<div class="col-md-4">
										<div class="input-group">
											<span class="input-group-addon" id="basic-addon1">@</span>
											<input type="text" class="form-control" id="inputCarrierSuffix" name="inputCarrierSuffix" placeholder="Carrier Suffix" aria-describedby="basic-addon1" required>
										</div>
									</div>
									<div class="col-md-5">
										<div class="help-block with-errors"></div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<div class="col-md-3">
										<label for="inputCarrierRule class="control-label">Carrier Rule</label>
									</div>
									<div class="col-md-3">
										<div class="input-group">
											<input type="text" class="form-control" id="inputCarrierRule" name="inputCarrierRule" aria-describedby="basic-addon1" value="10" required>
											<span class="input-group-addon" id="basic-addon1">digits</span>
										</div>
									</div>
								</div>
							</div>
							
						</div>
					
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<input class="btn btn-primary" type="submit" value="Add Carrier" id="addCarrier">
					</div>
				</form>
			</div>
		</div>
	</div>
<div class="container-fluid">
	<div class="panel panel-primary">
		<div class="panel-heading">Add User</div>
		<div class="panel-body">
			<div class="row col-md-12">
				<div class ="col-md-1 pull-right"><a href="listusers.php" ><button type="button" class="btn btn-primary btn-sm">List Users</button></a></div>
				<div class ="col-md-1 pull-right"><a href="edituser.php" ><button type="button" class="btn btn-primary btn-sm">Edit Users</button></a></div>
			</div>
			<form method="post" id="addUser">
				<div class="row col-md-12">
					<div class="col-md-4">
						<div class="row spacer">
							<div class="panel panel-info">
								<div class="panel panel-heading">Pesonal Information</div>
								<div class="panel-body">
									<input type="hidden" name="id" value=""/>
									<div class="col-md-12">
										<div class="row">
											<div class="col-md-6 rWellPadding">
												<div class="form-group">
													<div class="row">
														<label for="inputFirstName" class="control-label">First Name</label>
													</div>
													<div class="row">
														<input type="text" class="form-control" id="inputFirstName" name="firstname" placeholder="First Name" minlength="3" tabindex="1">
													</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<div class="row">
														<label for="inputLastName" class="control-label">Last Name</label>
													</div>
													<div class="row">
														<input type="text" class="form-control" id="inputLastName" name="lastname" placeholder="Last Name" minlength="3" tabindex="2">
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6 rWellPadding">
												<div class="form-group">
													<div class="row">
														<label for="email" class="control-label">Email</label>
													</div>
													<div class="row">
														<input type="email" class="form-control" id="email" name="email" placeholder="Email" tabindex="3">
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6 rWellPadding">
												<div class="form-group">
													<div class="row">
														<label for="inputPassword" class="control-label">Password</label>
													</div>
													<div class="row">
														<input type="password" data-minlength="5" class="form-control" id="inputPassword" name="password" placeholder="Password" tabindex="4">
													</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<div class="row">
														<label for="inputPasswordConfirm" class="control-label">Confirm Password</label>
													</div>
													<div class="row">
														<input type="password" class="form-control" id="inputPasswordConfirm" name="confirmPassword" data-match="#inputPassword" data-match-error="Whoops, these don't match" placeholder="Confirm Password" tabindex="5">
													</div>
													
														<!--<di class="help-block with-errors">-->
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6 rWellPadding">
												<div class="form-group">
													<div class="row">
														<label for="inputMobile" class="control-label">Mobile Number</label>
													</div>
													<div class="row">
														<input type="text" class="form-control" id="inputMobile" name="mobile" placeholder="Mobile Number" tabindex="6">
													</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<div class="row">
														<label for="inputLastName" class="control-label">Carrier</label>
													</div>
													<div class="row">
														<div class="col-md-10 leftPull">
														<select class="form-control" id="selectCarrier" name="selectCarrier" placeholder="Last Name" tabindex="7">
															<option value="" disabled selected>--Choose Carrier--</option>
															<?php
															$query = $db->prepare("SELECT * FROM smsaddress ORDER BY carrier ASC");
															$query->execute();
															$result = $query->get_result();
															while (($row = $result->fetch_object()) !== NULL) {
															?>
																<option value="<?php echo $row->id ?>"><?php echo $row->carrier ?></option>
															<?php
															}
															?>
														</select>
														</div>
														<div class="col-md-2 leftPull">
														<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#carrierModal" aria-label="Add">
															<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
														</button>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6 rWellPadding">
												<div class="form-group">
													<div class="row">
														<label for="selectDepartment" class="control-label">Department</label>
													</div>
													<div class="row">
														<select id="selectDepartment" name="selectDepartment" class="form-control" tabindex="8">
															<option value="" disabled selected>--Choose Department--</option>
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
											<div class="col-md-6">
												<div class="form-group">
													<div class="row">
														<label for="active" class="control-label">Active</label>
													</div>
													<div class="row">
														<div class="radio">
															<label class="col-md-4"><input type="radio" name="active" value="1" tabindex="9" checked>Yes</label>
															<label><input type="radio" name="active" value="0" tabindex="8">No</label>
														</div>
													</div>
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
<script src="../js/jquery.validate.js"></script>
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
		//Add User form validation
		$("#addUser").validator({
			
		});

		
	});

	
</script>
</body>
</html>
