<?php
//echo dirname($_SERVER['PHP_SELF']);
$folders = explode ('/', $_SERVER['PHP_SELF']);
//echo $folders[2];
$result = count($folders);
for($i=1;$i<$result;$i++){
echo $folders[$i]."<br>";
}
?>
<nav class="navbar navbar-default" role="navigation">
	<div class="container-fluid">
	<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="../index.php">Pin Time Study</a>
		</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Part Timing <span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="#">Cutting</a></li>
						<li><a href="#">Edgebanding</a></li>
						<li><a href="../machining.php">Machining</a></li>
						<li class="divider"></li>
						<li><a href="#">Assembly</a></li>
						<li class="divider"></li>
						<li><a href="#">Packaging</a></li>
					</ul>
				</li>
				<?php
					if($_SESSION['user_auth_level'] >= 3){
						echo "<li><a href=\"../reports/index.php\">Reports</a></li>";
					}
				?>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Account<span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="../settings.php">Settings</a></li>
						<?php
						if($_SESSION['user_auth_level'] == 5){
						 echo "<li class=\"divider\"></li><li><a href=\"admin.php\">Administration</a></li>";}
						?>
					</ul>
				</li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<a href="../logout.php" class="btn btn-primary navbar-btn btn-xs">Sign Out</a>
			</ul>
			<p class="navbar-text navbar-right">Signed in as <a class="navbar-link" href="../settings.php"><b><?php echo $_SESSION['user_first_name']." ".$_SESSION['user_last_name'] ?>&nbsp;</b></a></p>
 
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>