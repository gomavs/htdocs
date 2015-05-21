<?php
require '../includes/check_login.php';

if (isset( $_POST[ 'submit' ] ) ) {
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$email = $_POST['email'];
	$authlevel = $_POST['authlevel'];
	$active = $_POST['active'];
	$id = $_POST["id"];
	$hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
	if (!empty($id)) {
		$query = $db->prepare("UPDATE users SET firstname = ?, lastname = ?, email = ?, authlevel = ?, active = ? WHERE id = ?");
		$query->bind_param("sssiii", $firstname, $lastname, $email, $authlevel, $active, $id);
		$query->execute();
	} else {
		mysqli_query($db,"INSERT INTO users (firstname, lastname, email, password, authlevel, active)
	VALUES ('$firstname', '$lastname','$email', '$hashed_password', '$authlevel', '$active')");
	
	}
}

if(isset($_POST['updateuser'])){
	$user_id = $_POST['update_user'];
	$user_first_name = $_POST['firstname'];
	$user_last_name = $_POST['lastname'];
	$user_email = $_POST['email'];
	$user_auth_level = $_POST['authlevel'];
	$user_active = $_POST['active'];
	$query = $db->prepare("UPDATE users SET firstname = ?, lastname = ?, email = ?, authlevel = ?, active = ? WHERE id = ? ");
	$query->bind_param("sssiii", $user_first_name, $user_last_name, $user_email, $user_auth_level, $user_active, $user_id);
	$query->execute();
}

$table_data = "";
$result = mysqli_query($db,"SELECT * FROM users ORDER BY lastname ASC");
while($row = mysqli_fetch_array($result)) {
	$id =  $row['id'];
	$firstname = $row['firstname'];
	$lastname = $row['lastname'];
	$email = $row['email'];
	$authlevel = $row['authlevel'];
	$active = $row['active'];
	switch($authlevel){
		case 1: $authorized = "Time Keeper"; $authlist = "<option value=\"1\" selected>Time Keeper</option><option value=\"3\">Manager</option><option value=\"5\">Administrator</option>"; break;
		case 3: $authorized = "Manager"; $authlist = "<option value=\"1\">Time Keeper</option><option value=\"3\" selected>Manager</option><option value=\"5\">Administrator</option>"; break;
		case 5: $authorized = "Administrator"; $authlist = "<option value=\"1\">Time Keeper</option><option value=\"3\">Manager</option><option value=\"5\" selected>Administrator</option>"; break;
		default: $authorized = "None"; $authlist = "<option value=\"1\">Time Keeper</option><option value=\"3\">Manager</option><option value=\"5\">Administrator</option>"; break;
	}
	if ($active == 0){
		$user_active = "No";
		$radio_active = "<label class=\"col-md-4\"><input type=\"radio\" name=\"active\" value=\"1\" required>Yes</label><label><input type=\"radio\" name=\"active\" value=\"0\" required checked>No</label></div>";
	}else{
		$user_active = "Yes";
		$radio_active = "<label class=\"col-md-4\"><input type=\"radio\" name=\"active\" value=\"1\" required checked>Yes</label><label><input type=\"radio\" name=\"active\" value=\"0\" required>No</label></div>";
	}

	$table_data .= "<tr id=\"$id\"><td>".$row['id']."</td><td>". $row['firstname']."</td><td>".$row['lastname']."</td><td>".$row['email']."</td><td>".$authorized."</td><td>".$user_active."</td></tr>";
	$table_data .= "<tr class=\"test\"><td colspan=\"6\">";
	$table_data .= "<form id=\"user-". $row['id']."\"><input type=\"hidden\" name=\"update_user\" value=\"".$row['id']."\"/>";
	$table_data .= "<div class=\"row\"><div class=\"form-group col-md-3\">";
	$table_data .= "<label for=\"inputFirstName\" class=\"control-label\">First Name</label>";
	$table_data .= "<input type=\"text\" class=\"form-control\" id=\"inputFirstName\" name=\"firstname\" value=\"".$row['firstname']."\" required></div>";
	$table_data .= "<div class=\"form-group col-md-3\">";
	$table_data .= "<label for=\"inputLastName\" class=\"control-label\">Last Name</label>";
	$table_data .= "<input type=\"text\" class=\"form-control\" id=\"inputLastName\" name=\"lastname\" value=\"".$row['lastname']."\" required></div>";
	$table_data .= "<div class=\"form-group col-md-3\">";
	$table_data .= "<label for=\"active\" class=\"control-label\">Authorization Level</label>";
	$table_data .= "<select class=\"form-control\" name=\"authlevel\">.$authlist.</select></div>";
	$table_data .= "<div class=\"form-group col-md-3\">";
	$table_data .= "<label for=\"newpass\" class=\"control-label newpasslabel-".$row['id']."\">&nbsp</label>";
	$table_data .= "<h4 for=\"newpass\" class=\"control-label newpass-".$row['id']."\" >&nbsp</h4></div></div>";
	$table_data .= "<div class=\"row\"><div class=\"form-group col-md-3\">";
	$table_data .= "<label for=\"inputEmail\" class=\"control-label\">Email</label>";
	$table_data .= "<input type=\"email\" class=\"form-control\" id=\"inputEmail\" name=\"email\" value=\"".$row['email']."\" data-error=\"That email address is invalid\" required>";
	$table_data .= "<div class=\"help-block with-errors\"></div></div>";
	$table_data .= "<div class=\"form-group col-md-3\">";
	$table_data .= "<label for=\"active\" class=\"control-label\">Active</label>";
	$table_data .= "<div class=\"radio\">".$radio_active."</div>";
	$table_data .= "<div class=\"form-group col-md-6 do_action\">";
	$table_data .= "<label for=\"button\" class=\"control-label col-md-12\">&nbsp;</label>";
	$table_data .= "<button type=\"submit\" name=\"updateuser\" class=\"btn btn-primary btn-sm\" formmethod=\"post\">Update User</button>&nbsp;&nbsp;<button type=\"button\" name=\"resetpass\" id=\"resetPass-".$row['id']."\" class=\"btn btn-warning btn-sm\">Reset Password</button></div>";
	$table_data .= "<div class=\"form-group col-md-3\">";
	$table_data .= "</div>";
	$table_data .= "</form></td></tr>";
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
	<li><a href="..">Home</a></li>
	<li><a href="admin.php">Administration</a></li>
	<li class="active">Users</li>
</ol>
<div class="container-fluid">
	<!-- Stack the columns on mobile by making one full-width and the other half-width -->
	<div class="row">
		<div class="col-md-5">
			<form data-toggle="validator" role="form" method="post" id="add">
				<input type="hidden" name="id" value=""/>
				<div class="form-group col-md-6">
					<label for="inputFirstName" class="control-label">First Name</label>
					<input type="text" class="form-control" id="inputFirstName" name="firstname" placeholder="First Name" required>
				</div>
				<div class="form-group col-md-6">
					<label for="inputLastName" class="control-label">Last Name</label>
					<input type="text" class="form-control" id="inputLastName" name="lastname" placeholder="Last Name" required>
				</div>
				<div class="form-group">
					<label for="inputPassword" class="control-label col-md-8">Password</label>
					<div class="form-group col-md-6">
						<input type="password" data-minlength="5" class="form-control pull-left" id="inputPassword" name="password" placeholder="Password" required>
						<span class="help-block">Minimum of 5 characters</span>
					</div>
					<div class="form-group col-md-6">
						<input type="password" class="form-control" id="inputPasswordConfirm" name="confirmPassword" data-match="#inputPassword" data-match-error="Whoops, these don't match" placeholder="Confirm Password" required>
						<div class="help-block with-errors"></div>
					</div>
				</div>
				<div class="form-group col-md-8">
					<label for="inputEmail" class="control-label">Email</label>
					<input type="email" class="form-control" id="inputEmail" name="email" placeholder="Email" data-error="That email address is invalid" required>
					<div class="help-block with-errors"></div>
				</div>
				
				<div class="form-group col-md-8">
					<label for="active" class="control-label">Authorization Level</label>
					<select class="form-control" name="authlevel">
						<option value="1">Time Keeper</option>
						<option value="3">Manager</option>
						<option value="5">Administrator</option>
					</select>
				</div>
				<div class="form-group col-md-8">
					<label for="active" class="control-label">Active</label>
					<div class="radio">
						<label class="col-md-2"><input type="radio" name="active" value="1" required checked>Yes</label><label><input type="radio" name="active" value="0" required>No</label>
					</div>
				</div>

				<div class="form-group col-md-8">
					<button type="submit" name="submit" class="btn btn-primary" formmethod="post">Submit</button>
				</div>
			</form>
		</div>
		<div class="col-md-7">
			<div class="row">
				<table class="table table-hover" id="users">
					<tr>
						<th>id</th>
						<th>First Name</th>
						<th>Last Name</th>
						<th>Email</th>
						<th>Level</th>
						<th>Active</th>
					</tr>
					<?php
						echo $table_data;
					?>
				</table>
			</div>
			
		</div>
	</div>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/validator.js"></script>
<script src="../js/jExpand.js"></script>
<script>
	function populate(form, data) {
		$.each(data, function(key, value) {
			var $field = $("[name=" + key + "]", form);
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

	//This is to make a table row clickable
/*
		$(".clickableRow").click(function() {
		var rowId = this.id;
		var request = $.getJSON("../ajax/updateusers.php", {id : rowId}, function(data) {
			populate($("#add"), data);
			$("[name=confirmPassword]").val(data.password);
			$("[name=submit]", $("#add")).html("Update");
		});
	});
*/

	$("#users").jExpand();
	
	$( ".do_action" ).on( "click", "[id^=resetPass-]", function() {
		var buttonId = this.id;
		var arr = buttonId.split('-');
		userId = arr[1];
		var request = $.get("../ajax/resetpass.php", {id : userId}, function(data) {
			console.log(data);
			var passlabel = "New Password";
			//alert(data);
			$("label.newpasslabel-"+userId).html(passlabel);
			$("h4.newpass-"+userId).html(data);
			$("h4.newpass-"+userId).css( "color", "blue" );
		});
	});
	
</script>
</body>
</html>
