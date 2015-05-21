<?php
$auth = 50;
$title = "Add Machine/Appliance";
$js = "addmachine";
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
include("header.php");
?>
<form id="addmachine" action="/ajax/addmachine" class="form-horizontal" method="post">
	<div class="control-group">
		<label for="type" class="control-label">Type</label>
		<div class="controls">
			<select id="type" name="type">
				<option>Machine</option>
				<option>Appliance</option>
			</select>
			<span class="help-inline"></span>
		</div>
	</div>
	<div id="depend-type">
		<div id="option-Machine">
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
					<a class="btn btn-small" href="#addmakemodal" data-toggle="modal"><i class="icon-plus"></i></a>
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
		</div>
		<div id="option-Appliance" style="display:none;">
			<div class="control-group">
				<label for="appliancetype" class="control-label">Appliance Type</label>
				<div class="controls">
					<select id="appliancetype" name="appliancetype">
						<option>Conveyers</option>
						<option>Electrical</option>
						<option>Lighting</option>
						<option>Plumbing</option>
					</select>
					<span class="help-inline"></span>
				</div>
			</div>
		</div>
	</div>
	<div class="control-group">
		<label for="nick" class="control-label">Nick Name</label>
		<div class="controls">
			<input id="nick" name="nick" type="text"/><span class="help-inline"></span>
		</div>
	</div>
	<div class="form-actions"><input class="btn btn-primary" type="submit" value="Add Machine/Appliance"/></div>
</form>
<div id="addmakemodal" class="modal hide fade" tabindex="-1">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Add a New Make</h3>
	</div>
	<div class="modal-body">
		<form class="form-inline">
			<span style="margin-right:10px;">Make Name</span><input id="addmakename" name="addmakename" type="text"/>
		</form>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal">Cancel</button>
		<button id="addmakebutton" class="btn btn-primary">Add Make</button>
	</div>
</div>
<?php
include("footer.php");
?>