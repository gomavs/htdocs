<?php
$auth = 30;
$title = "Add User";
$js = "adduser";
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
include("header.php");
?>
<form id="adduser" action="/ajax/adduser" class="form-horizontal" method="post">
	<div class="control-group">
		<label for="firstname" class="control-label">First Name</label>
		<div class="controls">
			<input id="firstname" name="firstname" type="text"/><span class="help-inline"></span>
		</div>
	</div>
	<div class="control-group">
		<label for="lastname" class="control-label">Last Name</label>
		<div class="controls">
			<input id="lastname" name="lastname" type="text"/><span class="help-inline"></span>
		</div>
	</div>
	<div class="control-group">
		<label for="email" class="control-label">Email</label>
		<div class="controls">
			<input id="email" name="email" type="text"/><span class="help-inline"></span>
		</div>
	</div>
	<div class="control-group">
		<label for="phone" class="control-label">Phone Number</label>
		<div class="controls">
			<input id="phone" name="phone" type="text"/>
		</div>
	</div>
	<div class="control-group">
		<label for="extension" class="control-label">Extension</label>
		<div class="controls">
			<input id="extension" name="extension" class="input-mini" type="text"/>
		</div>
	</div>
	<div class="control-group">
				<label for="workcenter" class="control-label">Department</label>
				<div class="controls">
					<select id="workcenter" name="workcenter" class="input input-small">
						<option value="100">Mill</option>
						<option value="200">Assembly</option>
						<option value="300">Laminate</option>
						<option value="500">Shipping</option>
						<option value="600">Maintenance</option>
					</select>
				</div>
			</div>
	<div class="control-group">
		<label for="role" class="control-label">Role</label>
		<div class="controls">
			<select id="role" name="role">
				<option selected="selected" disabled="disabled"></option>
				<option value="0">Superadmin</option>
				<option value="30">Facilities Manager</option>
				<option value="50">Maintenance Lead</option>
				<option value="70">Technician</option>
				<option value="90">Supervisor</option>
				<option value="99">User</option>
			</select><span class="help-inline"></span>
		</div>
	</div>
	<div class="form-actions"><input class="btn btn-primary" type="submit" value="Create User"/></div>
</form>
<div id="success" class="modal hide fade" tabindex="-1">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Success!</h3>
	</div>
	<div class="modal-body">
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal">Close</button>
		<button id="btn-name" class="btn btn-primary"></button>
		<button id="btn-me" class="btn btn-primary">Send to Me</button>
		<button id="btn-newuser" class="btn btn-primary" data-dismiss="modal">Add Another User</button>
	</div>
</div>
<?php
include("footer.php");
?>