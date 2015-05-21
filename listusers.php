<?php
$auth = 50;
$title = "List Users";
$js = "listusers";
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
include("header.php");
?>
<table id="users" class="table table-striped">
	<thead>
		<tr>
			<th class="type-int">ID</th>
			<th class="type-string">First Name</th>
			<th class="type-string">Last Name</th>
			<th class="type-int">Role</th>
			<th>Email</th>
			<th>Phone #</th>
			<th>Ext.</th>
            <th class="type-int">Work Center</th>
			<th class="type-string">Username</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<div id="editusermodal" class="modal hide fade" tabindex="-1">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Edit User</h3>
	</div>
	<div class="modal-body">
		<form id="edituser" action="/ajax/edituser" class="form-horizontal" method="post">
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
					<input id="workcenter" name="workcenter" class="input-mini" type="text"/>
				</div>
			</div>
			<div class="control-group">
				<label for="role" class="control-label">Role</label>
				<div class="controls">
					<select id="role" name="role">
						<option value="0">Superadmin</option>
						<option value="30">Facilities Manager</option>
						<option value="50">Maintenance Lead</option>
						<option value="70">Technician</option>
						<option value="90">Supervisor</option>
						<option value="99">User</option>
					</select><span class="help-inline"></span>
				</div>
			</div>
			<input type="hidden" id="id" name="id" value=""/>
		</form>
	</div>
	<div class="modal-footer">
		<span id="edituserhelp" class="help-inline" style="color:#B94A48; float:left;"></span>
		<button class="btn" data-dismiss="modal">Cancel</button>
		<button id="edituserbutton" class="btn btn-primary">Edit User</button>
	</div>
</div>
<?php
include("footer.php");
?>