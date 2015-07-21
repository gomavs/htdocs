<?php
require 'includes/check_login.php';
$errors = array();


if(isset($_POST['changeName'])){
	$new_first_name = $_POST['firstname'];
	$new_last_name = $_POST['lastname'];
	$query = $db->prepare("UPDATE users SET firstname = ?, lastname = ? WHERE id = ? ");
	$query->bind_param("ssi", $new_first_name, $new_last_name, $_SESSION['user_id']);
	$query->execute();
	$_SESSION['user_first_name'] = $new_first_name;
	$_SESSION['user_last_name'] = $new_last_name;
}

if(isset($_POST['changeEmail'])){
	$new_email = $_POST['email'];
	$query = $db->prepare("SELECT id FROM users WHERE email = ?");
	$query->bind_param("s", $new_email);
	$query->execute();
	$result = $query->get_result();
	$row = $result->fetch_assoc();
	if ($row['id'] == NULL){
		$query = $db->prepare("UPDATE users SET email = ? WHERE id = ? ");
		$query->bind_param("si", $new_email, $_SESSION['user_id']);
		$query->execute();
		$_SESSION['user_email'] = $new_email;
	}else{
		$errors[] = "That email address is already in use.";
	}
}
if(isset($_POST['changePhone'])){
	
	if(isset($_POST['cell']) AND !isset($_POST['selectCarrier']) AND isset($_POST['allowText'])){
		$errors[] = "You must select a carrier in order to receive texts";
	}
	$phoneNumber = $_POST['cell'];
	if(isset($_POST['selectCarrier'])){
		$carrier = $_POST['selectCarrier'];
	}else{
		$carrier = 0;
		$phoneNumber = "";
	}
	if(isset($_POST['allowText'])){
		$allowTexts = 1;
	}else{
		$allowTexts = 0;
	}
	$query = $db->prepare("UPDATE users SET cell = ?, carrierId = ?, allowTexts = ? WHERE id = ? ");
	$query->bind_param("siii", $phoneNumber, $carrier, $allowTexts, $_SESSION['user_id']);
	$query->execute();
}
if(isset($_POST['changePass'])){
	$old_password = $_POST['oldpassword'];
	$user_email = $_SESSION['user_email'];
	$query = $db->prepare("SELECT password FROM users WHERE email = ?");
	$query->bind_param("s", $user_email);
	$query->execute();
	$result = $query->get_result();
	while (($row = $result->fetch_object()) !== NULL){
		$hash = $row->password;
		if(password_verify($old_password, $hash)){
			$hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
			$query = $db->prepare("UPDATE users SET password = ? WHERE email = ? ");
			$query->bind_param("ss", $hashed_password, $_SESSION['user_email']);
			$query->execute();
			$_SESSION['user_pass'] = $hashed_password;
		}else{
			$errors[] = "The entered password is incorrect.";
		}
	}
}
$query = $db->prepare("SELECT cell, carrierId, allowTexts FROM users WHERE id = ?");
$query->bind_param("i", $_SESSION['user_id']);
$query->execute();
$result = $query->get_result();
$row = $result->fetch_assoc();
$phone = $row['cell'];
$carrier = $row['carrierId'];
if($carrier == 0){
	$selected = "selected";
}else{
	$selected = "";
}
if($row['allowTexts'] == 1){
	$checked = "checked";
}else{
	$checked = "";
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
	<li><a href="<?php echo $url_home; ?>">Home</a></li>
	<li class="active">Settings</li>
</ol>
<div class="container">
	<div class="row">
		<div class="col-lg-3"></div>
		<div class="col-lg-6">
			<div class="panel-group" id="accordion">
				<div class="panel panel-primary" id="panel1">
					<div class="panel-heading">
						<div class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
								Name
							</a>
						</div>
					</div>
					<div id="collapseOne" class="panel-collapse collapse in">
						<div class="panel-body">
							<form data-toggle="validator" role="form" method="post" id="changeName">
								<div class="form-group col-md-6">
									<label for="inputFirstName" class="control-label">First Name</label>
									<input type="text" class="form-control" id="inputFirstName" name="firstname" placeholder="First Name" value="<?php echo $_SESSION['user_first_name']; ?>" required>
								</div>
								<div class="form-group col-md-6">
									<label for="inputLastName" class="control-label">Last Name</label>
									<input type="text" class="form-control" id="inputLastName" name="lastname" placeholder="Last Name" value="<?php echo $_SESSION['user_last_name']; ?>" required>
								</div>
								<div class="form-group col-md-8">
									<button type="submit" name="changeName" class="btn btn-primary" formmethod="post">Change Name</button>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="panel panel-primary" id="panel2">
					<div class="panel-heading">
						<div class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
								Email
							</a>
						</div>
					</div>
					<div id="collapseTwo" class="panel-collapse collapse">
						<div class="panel-body">
							<form data-toggle="validator" role="form" method="post" id="changeEmail">
								<div class="form-group">
									<label for="inputEmail" class="control-label col-md-12" >Email</label>
									<div class="form-group col-md-6">
										<input type="email" class="form-control col-md-6" id="inputEmail" name="email" placeholder="Email" data-error="That email address is invalid" value="<?php echo $_SESSION['user_email']; ?>" required>
										<div class="help-block with-errors"></div>
									</div>
								</div>
								<div class="form-group col-md-6">
									<input type="email" class="form-control" id="confirmEmail" name="confirmemail" placeholder="Confirm Email" data-match="#inputEmail" data-error="The email addresses do not match." required>
									<div class="help-block with-errors"></div>
								</div>
								<div class="form-group col-md-8">
									<button type="submit" name="changeEmail" class="btn btn-primary" formmethod="post">Change Email</button>
								</div>								
							</form>
						</div>
					</div>
				</div>
				<div class="panel panel-primary" id="panel3">
					<div class="panel-heading">
						<div class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
								Phone
							</a>
						</div>
					</div>
					<div id="collapseThree" class="panel-collapse collapse">
						<div class="panel-body">
							<form data-toggle="validator" role="form" method="post" id="changePhone">
								<div class="form-group col-md-6">
									<label for="cell" class="control-label">Phone Number</label>
									<input type="text" class="form-control" id="cell" name="cell" placeholder="Phone Number" value="<?php echo $phone; ?>">
								</div>
								<div class="form-group col-md-6">
									<label for="selectCarrier" class="control-label">Carrier</label>									
									<select class="form-control" id="selectCarrier" name="selectCarrier">
										<option value="" disabled <?php echo $selected; ?>>--Choose Carrier--</option>
										<?php
										$query = $db->prepare("SELECT * FROM smsaddress ORDER BY carrier ASC");
										$query->execute();
										$result = $query->get_result();
										while (($row = $result->fetch_object()) !== NULL) {
											if($row->id == $carrier){
										?>
												<option value="<?php echo $row->id ?>" selected ><?php echo $row->carrier ?></option>
										<?php		
											}else{
										?>
												<option value="<?php echo $row->id ?>"><?php echo $row->carrier ?></option>
										<?php
											}
										}
										?>
										<option value="0">--- No Phone ---</option>
									</select>
								</div>
								<div class="form-group col-md-12">
									<div class="row">
										<label for="allowText" class="control-label">Allow Texts</label>
									</div>
									<div class="row">
										<input type="checkbox" id="allowText" name="allowText" <?php echo $checked; ?>> Receive Work Alerts On My Phone
									</div>
								</div>
								<div class="form-group col-md-8">
									<button type="submit" name="changePhone" class="btn btn-primary" formmethod="post">Change Phone</button>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="panel panel-primary" id="panel4">
					<div class="panel-heading">
						<div class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
								Password
							</a>
						</div>
					</div>
					<div id="collapseFour" class="panel-collapse collapse">
						<div class="panel-body">
							<form data-toggle="validator" role="form" method="post" id="changePassword">
								<div class="form-group">
									<label for="inputPassword" class="control-label col-md-12">Current Password</label>
									<div class="form-group col-md-6">
										<input type="password" class="form-control pull-left" id="oldPassword" name="oldpassword" placeholder="Current Password" required>
									</div>
									<label for="inputPassword" class="control-label col-md-12">New Password</label>
									<div class="form-group col-md-6">
										<input type="password" data-minlength="5" class="form-control pull-left" id="inputPassword" name="password" placeholder="New Password" required>
										<span class="help-block">Minimum of 5 characters</span>
									</div>
									<div class="form-group col-md-6">
										<input type="password" class="form-control" id="inputPasswordConfirm" name="confirmPassword" data-match="#inputPassword" data-match-error="Whoops, these don't match" placeholder="Confirm New Password" required>
										<div class="help-block with-errors"></div>
									</div>
								</div>
								<div class="form-group col-md-8">
									<button type="submit" name="changePass" class="btn btn-primary" formmethod="post">Change Password</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div> 
			<div class="error-block">
				<?php
					if (count($errors) > 0){
						foreach ($errors AS $Error){
							echo "<b>".$Error."</b><br>";
						}
					}
				?>
			</div>
		</div>
		<div class="col-lg-3">
	
		</div>
	</div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/validator.js"></script>
<script src="js/timestudy.js"></script>

</body>
</html>