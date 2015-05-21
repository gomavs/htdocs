<?php
$title = "My Account";
$js = "myaccount";
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
include("header.php");
?>
<form id="myaccount" action="/ajax/myaccount" class="form-horizontal" method="post">
	<div class="control-group">
		<label for="password" class="control-label">Current Password</label>
		<div class="controls">
			<input id="password" name="password" type="password"/><span class="help-inline"></span>
		</div>
	</div>
	<div class="control-group">
		<label for="newpass1" class="control-label">New Password</label>
		<div class="controls">
			<input id="newpass1" name="newpass1" type="password"/><span class="help-inline"></span>
		</div>
	</div>
    <div class="control-group">
		<label for="newpass2" class="control-label">New Password</label>
		<div class="controls">
			<input id="newpass2" name="newpass2" type="password"/><span class="help-inline"></span>
		</div>
	</div>
	<div class="control-group">
		<label for="email" class="control-label">Email</label>
		<div class="controls">
			<input id="email" name="email" type="text" value="<?php echo $_SESSION["email"]; ?>"/><span class="help-inline"></span>
		</div>
	</div>
	<div class="control-group">
		<label for="phone" class="control-label">Phone Number</label>
		<div class="controls">
			<input id="phone" name="phone" type="text" value="<?php echo $_SESSION["phone"]; ?>"/>
		</div>
	</div>
	<div class="control-group">
		<label for="extension" class="control-label">Extension</label>
		<div class="controls">
			<input id="extension" name="extension" class="input-mini" type="text" value="<?php echo $_SESSION["extension"]; ?>"/>
		</div>
	</div>
	<div class="form-actions"><input id="updatebutton" class="btn btn-primary" type="submit" value="Update My account"/></div>
</form>
<?php
include("footer.php");
?>