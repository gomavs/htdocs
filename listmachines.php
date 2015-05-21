<?php
$auth = 50;
$title = "List Machines";
$js = "listmachines";
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
include("header.php");
?>
<table id="machines" class="table table-striped">
	<thead>
		<tr>
			<th class="type-int">ID</th>
			<th class="type-string">Nick</th>
			<th class="type-string">Make</th>
			<th class="type-string">Model</th>
			<th class="type-int">Year</th>
			<th class="type-int">Workcenter</th>
			<th>Serial</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<div id="editmachinemodal" class="modal hide fade" tabindex="-1">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Edit Machine</h3>
	</div>
	<div class="modal-body">
		<form id="editmachine" action="/ajax/editmachine" class="form-horizontal" method="post">
			<div class="control-group">
				<label for="make" class="control-label">Make</label>
				<div class="controls">
					<select id="make" name="make">
						<option selected="selected" disabled="disabled"></option>
						<?php
						$res = $mysqli->query("SELECT * FROM makes ORDER BY name");
						while ($row = $res->fetch_assoc()) {
							echo "<option value=\"" . $row["id"] . "\">" . $row["name"] . "</option>";
						}
						?>
					</select>
					<span class="help-inline"></span>
				</div>
			</div>
			<div class="control-group">
				<label for="model" class="control-label">Model</label>
				<div class="controls">
					<input id="model" name="model" type="text"/><span class="help-inline"></span>
				</div>
			</div>
			<div class="control-group">
				<label for="serial" class="control-label">Serial Number</label>
				<div class="controls">
					<input id="serial" name="serial" type="text"/><span class="help-inline"></span>
				</div>
			</div>
			<div class="control-group">
				<label for="year" class="control-label">Year</label>
				<div class="controls">
					<select id="year" name="year" class="input-small">
						<option selected="selected" disabled="disabled"></option>
						<?php
						for ($i = intval(date("Y")); $i >= 1995; $i--) {
							echo "<option>$i</option>";
						}
						?>
					</select>
					<span class="help-inline"></span>
				</div>
			</div>
			<div class="control-group">
				<label for="workcenter" class="control-label">Work Center</label>
				<div class="controls">
					<input id="workcenter" name="workcenter" class="input-mini" type="text"/><span class="help-inline"></span>
				</div>
			</div>
			<div class="control-group">
				<label for="nick" class="control-label">Nick Name</label>
				<div class="controls">
					<input id="nick" name="nick" type="text"/><span class="help-inline"></span>
				</div>
			</div>
			<input type="hidden" id="id" name="id" value=""/>
		</form>
	</div>
	<div class="modal-footer">
		<span id="editmachinehelp" class="help-inline" style="color:#B94A48; float:left;"></span>
		<button class="btn" data-dismiss="modal">Cancel</button>
		<button id="editmachinebutton" class="btn btn-primary" data-dismiss="modal">Edit Machine</button>
	</div>
</div>
<?php
include("footer.php");
?>