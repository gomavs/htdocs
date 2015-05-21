
<div class="navbar navbar-default" navbar-fixed-top" role="navigation">
	<div class="container-fluid">
	<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#">Pin Time Study</a>
		</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Part Timing <span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="#">Cutting</a></li>
						<li><a href="#">Edgebanding</a></li>
						<li><a href="machining.php">Machining</a></li>
						<li class="divider"></li>
						<li><a href="#">Assembly</a></li>
						<li class="divider"></li>
						<li><a href="#">Packaging</a></li>
					</ul>
				</li>
				<?php
					if($_SESSION['user_auth_level'] >= 3){
						echo "<li class=\"dropdown\">";
						echo "<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">Reports <span class=\"caret\"></span></a>";
						echo "<ul class=\"dropdown-menu\" role=\"menu\">";
						echo "<li><a href=\"reports/reports.php\">Machining Times</a></li>";
						echo "<li><a href=\"#\">Weekly Times</a></li>";
						echo "<li><a href=\"#\">Monthly Times</a></li>";
						echo "</ul>";
						echo "</li>";
					}
					?>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Account<span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="settings.php">Settings</a></li>
						<?php
						if($_SESSION['user_auth_level'] == 5){
						 echo "<li class=\"divider\"></li><li><a href=\"./admin/admin.php\">Administration</a></li>";}
						?>
					</ul>
				</li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<a href="logout.php" class="btn btn-default navbar-btn btn-xs">Sign Out</a>
			</ul>
			<p class="navbar-text navbar-right">Signed in as <a class="navbar-link" href="settings.php"><b><?php echo $_SESSION['user_first_name']." ".$_SESSION['user_last_name'] ?>&nbsp;</b></a></p>
 
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</div>




<div class="col-sm-3 col-md-2 sidebar">
	<ul class="nav nav-sidebar">
		<li class="active"><a href="#">Overview</a></li>
		<li><a href="#">Reports</a></li>
		<li><a href="#">Analytics</a></li>
		<li><a href="#">Export</a></li>
	</ul>
	<ul class="nav nav-sidebar">
		<li><a href="">Nav item</a></li>
		<li><a href="">Nav item again</a></li>
		<li><a href="">One more nav</a></li>
		<li><a href="">Another nav item</a></li>
		<li><a href="">More navigation</a></li>
	</ul>
	<ul class="nav nav-sidebar">
		<li><a href="">Nav item again</a></li>
		<li><a href="">One more nav</a></li>
		<li><a href="">Another nav item</a></li>
	</ul>
</div>
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	test
</div>