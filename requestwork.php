<?php
$auth = 90;
$title = "Request Work";
$js = "requestwork";
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
include("header.php");
?>
<form id="requestwork" action="/ajax/requestwork" class="form-horizontal" method="post">
	<div class="control-group">
		<label for="worktype" class="control-label">Work Type</label>
		<div class="controls">
			<select id="worktype" name="worktype">
				<option>Machine</option>
				<option>Appliance</option>
			</select>
			<span class="help-inline"></span>
		</div>
	</div>
	<div id="depend-worktype">
		<div class="option-Machine control-group">
			<label for="machine" class="control-label">Machine</label>
			<div class="controls">
				<select id="machine" name="machine">
					<option selected="selected" disabled="disabled"></option>
					<?php
					$res = $mysqli->query("SELECT id, nick FROM machines WHERE type='Machine' ORDER BY nick");
					while ($row = $res->fetch_assoc()) {
						echo "<option value=\"" . $row["id"] . "\">" . $row["nick"] . "</option>";
					}
					?>
				</select>
				<span class="help-inline"></span>
			</div>
		</div>
		<div class="option-Machine control-group">
			<label for="issue" class="control-label">Problem</label>
			<div class="controls">
				<select id="issuem" name="issuem">
					<option selected="selected" disabled="disabled"></option>
					<?php
					$sql = $mysqli->query("SELECT name FROM problems WHERE type = 0 ORDER BY name");
					$res = $sql->fetch_all();
					foreach ($res as $row) {
						echo "<option>" . $row[0] . "</option>";
					}
					?>
				</select>
				<span class="help-inline"></span>
			</div>
		</div>
		<div class="option-Appliance control-group" style="display:none;">
			<label for="appliance" class="control-label">Appliance</label>
			<div class="controls">
				<select id="appliance" name="appliance">
					<option selected="selected" disabled="disabled"></option>
					<?php
					$res = $mysqli->query("SELECT id, nick FROM machines WHERE type='Appliance' ORDER BY nick");
					while ($row = $res->fetch_assoc()) {
						echo "<option value=\"" . $row["id"] . "\">" . $row["nick"] . "</option>";
					}
					?>
				</select>
				<span class="help-inline"></span>
			</div>
		</div>
		<div class="option-Appliance control-group" style="display:none;">
			<label for="issue" class="control-label">Problem</label>
			<div class="controls">
				<select id="issuea" name="issuea">
					<option selected="selected" disabled="disabled"></option>
					<?php
					$sql = $mysqli->query("SELECT name FROM problems WHERE type = 1 ORDER BY name");
					$res = $sql->fetch_all();
					foreach ($res as $row) {
						echo "<option>" . $row[0] . "</option>";
					}
					?>
				</select>
				<span class="help-inline"></span>
			</div>
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
		<label for="notes" class="control-label">Notes</label>
		<div class="controls">
			<textarea id="notes" name="notes" rows="3"></textarea>
		</div>
	</div>
	<div class="form-actions"><input class="btn btn-primary" type="submit" value="File Request"/></div>
</form>
<?php
include("footer.php");
?>