<?php
$auth = 50;
$title = "Problems";
$js = "problems";
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
include("header.php");
?>
<h4>Machine</h4>
<ul>
<?php
$sql = $mysqli->query("SELECT name FROM problems WHERE type = 0 ORDER BY name");
$res = $sql->fetch_all();
foreach ($res as $row) {
	echo "<li>" . $row[0] . "</li>";
}
?>
</ul>
<h4>Facility</h4>
<ul>
<?php
$sql = $mysqli->query("SELECT name FROM problems WHERE type = 1 ORDER BY name");
$res = $sql->fetch_all();
foreach ($res as $row) {
	echo "<li>" . $row[0] . "</li>";
}
?>
</ul>
<button class="btn btn-primary btn-addproblem">Add Problem</button>
<div id="addproblemmodal" class="modal hide fade" tabindex="-1">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Add Problem</h3>
	</div>
	<div class="modal-body">
		<form id="addproblemform" class="form-horizontal">
			<div class="control-group">
				<label for="notes" class="control-label">Problem Type</label>
				<div class="controls">
					<select id="problemtype" name="problemtype">
						<option>Machine</option>
						<option>Facility</option>
					</select>
				</div>
			</div>
			<div class="control-group">
				<label for="notes" class="control-label">Problem Name</label>
				<div class="controls">
					<input id="problemname" name="problemname" type="text"/>
				</div>
			</div>
		</form>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal">Cancel</button>
		<button id="addproblembutton" class="btn btn-primary">Add Problem</button>
	</div>
</div>
<?php
include("footer.php");
?>