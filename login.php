<?php
$nologin = 1;
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8"/>
	<title>Sign In</title>
	<link rel="stylesheet" type="text/css" href="/css/main.css"/>
	<link rel="stylesheet" type="text/css" href="/css/login.css"/>
	<script type="text/javascript" src="/js/main.js"></script>
	<script type="text/javascript" src="/js/login.js"></script>
</head>
<body>
<div id="container">
	<form id="login" action="/ajax/login.php" method="post" class="form-horizontal">
		<legend>Sign In</legend>
		<div class="control-group">
			<div class="controls">
				<input type="text" id="username" name="username" placeholder="Username">
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<input type="password" id="password" name="password" placeholder="Password">
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<label class="checkbox">
					<input type="checkbox" id="remember" name="remember"> Remember Me
				</label>
				<button type="submit" class="btn btn-primary">Sign in</button>
			</div>
		</div>
	</form>
</div>
<div class="alert alert-error">
	<strong>Error: </strong> This is a fake error to test.
</div>
<div id="footer">Copyright © 2012 Jacob Brunson</div>
</body>
</html>