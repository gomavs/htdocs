<?php
session_start();
require_once 'includes/dbConnect.php';
$errors = array();
if(isset($_SESSION['logged']) && $_SESSION['logged'] == 1){
	header("location:index.php");
}
if(isset($_POST['submit'])){
	$email = $_POST['email'];
	$password = $_POST['password'];
	$query = $db->prepare("SELECT * FROM users WHERE email = ?");
	$query->bind_param("s", $email);
	$query->execute();
	$result = $query->get_result();
	while (($row = $result->fetch_object()) !== NULL) {
		$hash = $row->password;
		if (password_verify($password, $hash)) {
			//Password is verified
			if($row->active == 1){
				$_SESSION['user_id'] = $row->id;
				$_SESSION['user_first_name'] = $row->firstname;
				$_SESSION['user_last_name'] = $row->lastname;
				$_SESSION['user_email'] = $row->email;
				$_SESSION['user_pass'] = $password;
				$_SESSION['user_auth_level'] = $row->authlevel;
				$_SESSION['user_permissions'] = $row->permissions;
				$_SESSION['user_authTS'] = $row->authTS;
				$_SESSION['user_authWO'] = $row->authWO;
				$_SESSION['logged'] = 1;
				$_SESSION['time'] = time();
				$logged_in = 1;
				header("location:index.php");
			}else{
				$errors[] = "This is not an active account.  Please contact a Time Study administrator.";
			}
		}else{
			$errors[] = "The entered email address or the password is incorrect.";
		}
		
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
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/login.css" rel="stylesheet">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-sm-6 col-md-4 col-md-offset-4">
            <h1 class="text-center login-title">Sign in to continue to PIN Systems</h1>
            <div class="account-wall">
                <img class="profile-img" src="images/icon-user-default.png"
                    alt="">
                <form data-toggle=\"validator\" class="form-signin" method="post">
					<input type="email" class="form-control" id="inputEmail" name="email" placeholder="Email" required autofocus>
					<input type="password" class="form-control" id="inputPassword" name="password" placeholder="Password" required>
					<button class="btn btn-lg btn-primary btn-block" type="submit" name="submit" formmethod="post">
						Sign in</button>
					<label class="checkbox pull-left">
						<input type="checkbox" value="remember-me" disabled>
						Remember me
					</label>
					<a href="#" class="pull-right need-help">Need help? </a><span class="clearfix"></span>
					<div class="login_errors">
						<?php 
							if (count($errors) > 0){
								foreach ($errors AS $Errors){
									echo $Errors."<br>";
								}
							}
						?>
					</div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/validator.js"></script>
<script>
	$(".need-help").click(function() {
		var list = 5;
		var request = $.getJSON("ajax/adminlist.php", {admin : list}, function(data) {
			console.log(data);
			var alertMessage = "If you can't remember your login email or password, please conatct one of the administrators listed below. \n" + " \f";
			$.each(data, function(key, value) {
				alertMessage += value.firstname + " " + value.lastname + "\n";
			});
			alert(alertMessage);
		});
	});
</script>
</body>
</html>

