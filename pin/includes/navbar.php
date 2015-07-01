<?php
$folders = explode ('/', $_SERVER['PHP_SELF']);
$folder_count = count($folders);
if($folder_count > 3){
	$relative = "../";
}else{
	$relative = "";
}
$permission = explode(",", $_SESSION['user_permissions']);
//Find if user has open work orders
$query = $db->prepare("SELECT * FROM workdata WHERE assignedTo = ? AND status = 0");
$query->bind_param("i", $_SESSION['user_id']);
$query->execute();
$result = $query->get_result();
$row_cnt = mysqli_num_rows($result);
$alerts = "";
$query = $db->prepare("SELECT * FROM messages WHERE msgTo = ? AND viewed = 0");
$query->bind_param("i", $_SESSION['user_id']);
$query->execute();
$result = $query->get_result();
///Find if user has unviewed alerts
if(mysqli_num_rows($result) > 0){
	$alerts = mysqli_num_rows($result);
}
////////////////URL's////////////////////
$url_home = $relative."index.php";
$url_logout = $relative."logout.php";
$url_machining = $relative."machining.php";
$url_settings = $relative."settings.php";
$url_admin = $relative."admin/admin.php";
$url_overview = $relative."reports/index.php";
$url_machine_reports = $relative."reports/machines.php";
$url_user_reports = $relative."reports/userreports.php";
$url_part_reports = $relative."reports/partreports.php";
$url_item_reports = $relative."reports/itemreport.php";
$url_request_work = $relative."workorders/requestwork.php";
$url_open_work = $relative."workorders/openrequests.php";
$url_work_progress = $relative."workorders/workprogress.php";
$url_closed_work = $relative."workorders/closedwork.php";
$url_my_work_orders = $relative."workorders/myworkorders.php";
$url_my_alerts = $relative."alerts.php";
?>
<nav class="navbar navbar-default" role="navigation">
	<div class="container-fluid">
	<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<a class="navbar-brand" href="#">PIN systems</a>
		</div>
		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
				<?php
					if($permission[0] == 1){
						echo "<li class=\"dropdown\">";
						echo "<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">Part Timing <span class=\"caret\"></span></a>";
						echo "<ul class=\"dropdown-menu\" role=\"menu\">";
						echo "<li role=\"presentation\" class=\"disabled\"><a role=\"menuitem\" href=\"#\">Cutting</a></li>";
						echo "<li role=\"presentation\" class=\"disabled\"><a role=\"menuitem\" href=\"#\">Edgebanding</a></li>";
						echo "<li role=\"presentation\"><a role=\"menuitem\" href=\" $url_machining \">Machining</a></li>";
						echo "<li class=\"divider\"></li>";
						echo "<li role=\"presentation\" class=\"disabled\"><a role=\"menuitem\" href=\"#\">Assembly</a></li>";
						echo "<li class=\"divider\"></li>";
						echo "<li role=\"presentation\" class=\"disabled\"><a role=\"menuitem\" href=\"#\">Packaging</a></li>";
						echo "</ul>";
						echo "</li>";
					}
				?>
				<?php
					if($_SESSION['user_auth_level'] >= 3 && $permission[0] == 1){
						echo "<li class=\"dropdown\">";
						echo "<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">Timing Reports<span class=\"caret\"></span></a>";
						//echo "<li><a href=\"reports/index.php\">Reports</a></li>";
						echo "<ul class=\"dropdown-menu\" role=\"menu\">";
						echo "<li role=\"presentation\"><a role=\"menuitem\" href=\"".$url_overview."\">Overview</a></li>";
						echo "<li role=\"presentation\"><a role=\"menuitem\" href=\"".$url_machine_reports."\">Machine Reports</a></li>";
						echo "<li role=\"presentation\"><a role=\"menuitem\" href=\"".$url_user_reports."\">User Reports</a></li>";
						echo "<li role=\"presentation\"><a role=\"menuitem\" href=\"".$url_part_reports."\">Performed Studies</a></li>";
						echo "<li role=\"presentation\"><a role=\"menuitem\" href=\"".$url_item_reports."\">Part Times</a></li>";
						echo "</ul></li>";
					}
				?>
				<?php
					if($permission[1] == 1){
						echo "<li class=\"dropdown\">";
						echo "<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">Work Orders<span class=\"caret\"></span></a>";
						echo "<ul class=\"dropdown-menu\" role=\"menu\">";
						if($row_cnt > 0){
							echo "<li role=\"presentation\"><a role=\"menuitem\" href=\"".$url_my_work_orders."\">My Work Orders</a></li>";
						}
						echo "<li role=\"presentation\"><a role=\"menuitem\" href=\"".$url_request_work."\">Request Work</a></li>";
						echo "<li role=\"presentation\"><a role=\"menuitem\" href=\"".$url_open_work."\">Open Work Requests</a></li>";
						echo "<li role=\"presentation\"><a role=\"menuitem\" href=\"".$url_work_progress."\">Work in Progress</a></li>";
						echo "<li role=\"presentation\"><a role=\"menuitem\" href=\"".$url_closed_work."\">Closed Work</a></li>";
						echo "</ul></li>";
					}
				?>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Account<span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><a role="menuitem" href="<?php echo $url_settings; ?>">Settings</a></li>
						<?php
						if($_SESSION['user_auth_level'] >= 5){
						 echo "<li class=\"divider\"></li><li><a role=\"menuitem\" href=\"".$url_admin."\">Administration</a></li>";}
						?>
					</ul>
				</li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<a href="<?php echo $url_logout ?>" class="btn btn-primary navbar-btn btn-xs">Sign Out</a>
			</ul>
			<p class="nav navbar-text navbar-right">Signed in as <a class="navbar-link" href="<?php echo $url_settings; ?>"><b><?php echo $_SESSION['user_first_name']." ".$_SESSION['user_last_name'] ?>&nbsp;</b></a></p>
			<?php if($permission[1] == 1) //echo"test";
			echo "<p class=\"nav navbar-text navbar-right\"><a class=\"navbar-link\" href=\"".$url_my_alerts."\"><b>Alerts</b><span class=\"badge\">".$alerts."</span></a></p>&nbsp;";
			?>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>