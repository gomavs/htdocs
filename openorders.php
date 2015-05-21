<?php
$title = "Orders in Progress";
$js = "openorders";
$css = "openorders";
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
include("header.php");
?>
<table id="requests" class="table table-striped2">
	<thead>
		<tr>
			<th class="type-int">ID</th>
			<th class="type-string">Item</th>
			<th class="type-string">Problem</th>
			<th class="type-int">Urgency</th>
			<th class="type-string">Requested By</th>
			<th class="type-int">Time</th>
			<th class="type-string">Assigned To</th>
			<th class="type-int">Est. Hours</th>
			<th class="type-int">Due Date</th>
			<th class="type-int">Notes</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<div id="edittimemodal" class="modal hide fade" tabindex="-1">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Edit Time</h3>
	</div>
	<div class="modal-body">
		<form id="edittimeform" action="/ajax/edittime" method="post" class="form-horizontal">
			<div class="control-group">
				<label for="timestart" class="control-label">Start</label>
				<div class="controls">
					<input type="text" id="timestart" name="timestart"/>
				</div>
			</div>
			<div class="control-group">
				<label for="timestop" class="control-label">Stop</label>
				<div class="controls">
					<input type="text" id="timestop" name="timestop"/>
				</div>
			</div>
			<input id="timeid" name="timeid" type="hidden"/>
		</form>
	</div>
	<div class="modal-footer">
		<span id="edittimehelp" class="help-inline" style="color:#B94A48; float:left;"></span>
		<button class="btn" data-dismiss="modal">Cancel</button>
		<button id="edittimebutton" class="btn btn-primary">Save</button>
	</div>
</div>
<div id="editdescmodal" class="modal hide fade" tabindex="-1">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Edit Work Description</h3>
	</div>
	<div class="modal-body">
		<form id="editdescform" action="/ajax/editdesc" method="post" class="form-horizontal">
			<div class="control-group">
				<label for="editsummary" class="control-label">Summary</label>
				<div class="controls">
					<input type="text" id="editsummary" name="editsummary"/>
				</div>
			</div>
			<div class="control-group">
				<label for="editdescription" class="control-label">Description</label>
				<div class="controls">
					<textarea id="editdescription" name="editdescription" rows="6"></textarea>
				</div>
			</div>
			<div class="control-group">
				<label for="editdate" class="control-label">Date</label>
				<div class="controls">
					<input id="editdate" name="editdate" id="editdate" type="text">
				</div>
			</div>
			<input id="workid" name="workid" type="hidden"/>
		</form>
	</div>
	<div class="modal-footer">
		<span id="editdeschelp" class="help-inline" style="color:#B94A48; float:left;"></span>
		<button class="btn" data-dismiss="modal">Cancel</button>
		<button id="editdescbutton" class="btn btn-primary">Save</button>
	</div>
</div>
<div id="editordermodal" class="modal hide fade" tabindex="-1">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Edit Order</h3>
	</div>
	<div class="modal-body">
		<form id="assignwork" action="/ajax/assignwork.php" class="form-horizontal" method="post">
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
						<option>Medium</option>
						<option>Low</option>
					</select>
					<span class="help-inline"></span>
				</div>
			</div>
			<div class="control-group">
				<label for="assignto" class="control-label">Assigned To</label>
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
			<input id="editworkid" name="id" type="hidden"/>
		</form>
	</div>
	<div class="modal-footer">
		<span id="editorderhelp" class="help-inline" style="color:#B94A48; float:left;"></span>
		<button class="btn" data-dismiss="modal">Cancel</button>
		<button id="editorderbutton" class="btn btn-primary">Save</button>
	</div>
</div>
<?php
include("footer.php");
?>