<?php
$title = "Open Requests";
$js = "listrequests";
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
include("header.php");
?>
<table id="requests" class="table table-striped">
	<thead>
		<tr>
			<th class="type-int">ID</th>
			<th class="type-string">Item</th>
			<th class="type-string">Type</th>
			<th class="type-string">Problem</th>
			<th class="type-int">Urgency</th>
			<th class="type-string">Requested By</th>
			<th class="type-int">Time</th>
			<th class="type-int">Notes</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<div id="reviewmodal" class="modal hide fade" tabindex="-1">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Review Request</h3>
	</div>
	<div class="modal-body">
		<form id="assignwork" action="/ajax/assignwork" class="form-horizontal" method="post">
			<div id="info"></div>
			<div class="control-group">
				<label for="issue" class="control-label">Problem</label>
				<div class="controls">
					<select id="issue" name="issue">
						<option selected="selected" disabled="disabled"></option>
						<?php
						$sql = $mysqli->query("SELECT name FROM problems ORDER BY name");
						$res = $sql->fetch_all();
						foreach ($res as $row) {
							echo "<option>" . $row[0] . "</option>";
						}
						?>
					</select>
					<span class="help-inline"></span>
				</div>
			</div>
			<div class="control-group">
				<label for="urgency" class="control-label">Urgency</label>
				<div class="controls">
					<select id="urgency" class="input-small" name="urgency">
						<option>High</option>
						<option selected="selected">Medium</option>
						<option>Low</option>
					</select>
					<span class="help-inline"></span>
				</div>
			</div>
			<div class="control-group">
				<label for="assignto" class="control-label">Assign To</label>
				<div class="controls">
					<select id="assignto" name="assignto">
						<option selected="selected" disabled="disabled"></option>
						<?php
						$res = $mysqli->query("SELECT id, first, last FROM users WHERE workcenter = 600 ORDER BY last");
						while ($row = $res->fetch_assoc()) {
							echo "<option value=\"" . $row["id"] . "\">" . $row["first"] . " " . $row["last"]  . "</option>";
						}
						?>
					</select>
					<span class="help-inline"></span>
				</div>
			</div>
			<div class="control-group">
				<label for="date" class="control-label">Due Date</label>
				<div class="controls">
					<input id="date" name="date" type="text"/>
					<span class="help-inline"></span>
				</div>
			</div>
			<div class="control-group">
				<label for="esthours" class="control-label">Estimated Hours</label>
				<div class="controls">
					<input id="esthours" name="esthours" type="number" class="input-mini"/>
					<span class="help-inline"></span>
				</div>
			</div>
			<div class="control-group">
				<label for="notes" class="control-label">Notes</label>
				<div class="controls">
					<textarea id="notes" name="notes" rows="3"></textarea>
				</div>
			</div>
			<input id="id" name="id" type="hidden"/>
		</form>
	</div>
	<div class="modal-footer">
		<span id="assignworkhelp" class="help-inline" style="color:#B94A48; float:left;"></span>
		<button class="btn" data-dismiss="modal">Cancel</button>
		<button id="rejectworkbutton" class="btn btn-primary">Reject</button>
		<button id="assignworkbutton" class="btn btn-primary">Assign</button>
	</div>
</div>
<?php
include("footer.php");
?>