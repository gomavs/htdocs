<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/init.php");
$start = 0;
$length = 10;
if (isset($_GET["start"])) {
	$start = $_GET["start"];
}
if (isset($_GET["length"])) {
	$length = $_GET["length"];
}
$res = $mysqli->query("SELECT workorders.*, machines.nick, CONCAT(requester.first, \" \", requester.last), CONCAT(assigned.first, \" \", assigned.last), '', times.* FROM workorders LEFT JOIN users as requester ON requester.id = workorders.requester LEFT JOIN users as assigned ON assigned.id = workorders.assigned LEFT JOIN machines ON machines.id = workorders.item LEFT JOIN times ON times.work = workorders.id WHERE workorders.status = " . $_GET["status"] . " ORDER BY time DESC LIMIT $start, $length");
$out = $res->fetch_all();
$res->close();
$ids = array();
for ($i = 0; $i < count($out); $i++) {
	$out[$i][5] = strtotime($out[$i][5]);
	$out[$i][6] = strtotime($out[$i][6]);
	$out[$i][14] = strtotime($out[$i][14]);
	$out[$i][18] = "<div style=\"height:200px;overflow:auto;\"><div style=\"width:75%;margin-right:5%;float:left;\">" . $out[$i][11] . "</div><!--<dl style=\"margin-left:80%;\"><dt>Parts Required</dt><dd>Pancakes</dd><dd>Sausage</dd><dd>Bacon</dd><dt>Parts Recieved</dt><dd>Chocolate Milk</dd></dl>--></div>";
	$ids[] = $out[$i][0];
}
function timetable($id, $times, $wart, $worker) {
	$str = "<table>
				<thead>
					<tr>" . ($wart != 0 ? "<th></th>" : "") . "<th>Start</th><th>Stop</th><th>Elapsed Time</th></tr>
				</thead>
				<tbody>";
	$times->bind_param("i", $id);
	$times->execute();
	$times->bind_result($tid, $tstart, $tend, $telapsed);
	$pro = false;
	while ($row = $times->fetch()) {
		$str .= "<tr>" . ($tend == null || $wart != 0 ? "<td>" : "");
		if ($tend == null) {
			$str .= "<button class=\"btn btn-danger btn-time\">Stop <i class=\"icon-time icon-white\"></i></button>";
			$pro = true;
		} elseif ($wart == 2 && $_SESSION["role"] <= 0 || $_SESSION["id"] == $worker) {
			$str .= "<button class=\"btn edit-time\" data-time-id=\"$tid\">Edit <i class=\"icon-time\"></i></button>";
		}
		$str .= ($tend == null || $wart ? "</td>" : "") . "<td>" . date("g:i A", $tstart) . "</td><td>" . (!$pro ? date("g:i A", $tend) : "") . "</td><td>" . (!$pro ? floor($telapsed / 60) . "hrs, " . ($telapsed % 60) . "mins" : "") . "</td></tr>";
	}
	if (!$pro && $wart == 1) {
		$str .=	"<tr><td><button class=\"btn btn-success btn-time\">Start <i class=\"icon-time icon-white\"></i></button></td><td></td><td></td><td></td></tr>";
	}
	$str .=	"</tbody>
			</table>";
	return $str;
}
function logwork($today, $id, $times, $worker) {
	$str = ($_GET["status"] == 1 ? "<button class=\"btn btn-primary btn-beginwork\" style=\"" . ($today ? "display:none;" : "") ."float:left; margin:15px 10px;\">Begin Work</button>" : ($_SESSION["role"] <= 50 ? "<button class=\"btn btn-primary btn-reopen\" style=\"" . ($today ? "display:none;" : "") ."float:left; margin:15px 10px;\">Reopen Order</button>" : "")) . ($_GET["status"] != 2 ? "<button class=\"btn btn-editorder\" style=\"margin:15px 10px;\">Edit Order</button>" : "") . ($_GET["status"] == 1 ? "<button class=\"btn btn-close\" style=\"margin:15px 10px;\">Close Order</button>" : "<button class=\"btn btn-pdf\" style=\"margin:15px 10px; display:none;\">View PDF</button>") .
			"<div id=\"$id\" class=\"well spoiler\"" . (!$today ? " style=\"display:none;\"" : "") . "><div class=\"title\">Log Work Time <i class=\"icon-pencil\"></i></div>
			<div class=\"more-content\"" . ($today ? " style=\"display:block;\"" : "") . ">
			<div style=\"width:49%; float:left;\">" . timetable($id, $times, 1, $worker) . "
			</div>
			<div style=\"width:49%; float:left;\">
			<form>
			<input id=\"worksummary\" name=\"worksummary\" type=\"text\" style=\"width:50%;\" placeholder=\"Summary\"/><strong> " . date("n/j/y", time()) . "</strong><br/>
			<textarea id=\"workdesc\" rows=\"6\" style=\"width:80%;\" placeholder=\"Detailed description\"></textarea><br/>
			<div class=\"form-actions\"><input class=\"btn btn-primary btn-endwork\" type=\"submit\" value=\"End Work\"/></div>
			</form>
			</div>
			</div>
			</div>";
	return $str;
}
$times = $mysqli->prepare("SELECT id, UNIX_TIMESTAMP(start),  UNIX_TIMESTAMP(end), TIMESTAMPDIFF(MINUTE, start, end) + 1 FROM times WHERE work = ? ORDER BY start");
if ($_GET["status"] == 1 || $_GET["status"] == 2) {
	$sql = $mysqli->prepare("SELECT work.*, CONCAT(users.first, \" \", users.last) FROM work LEFT JOIN users ON users.id = work.worker WHERE workorder = ? ORDER BY date DESC");
	for ($i = 0; $i < count($out); $i++) {
		$sql->bind_param("i", $out[$i][2]);
		$sql->execute();
		$sql->store_result();
		$sql->bind_result($wid, $wworkorder, $wsummary, $wdescription, $wworker, $wdate, $wstatus, $wworkername);
		if ($sql->num_rows == 0) {
			$out[$i][18] .= logwork(false, 1337, $times, 0);
		}
		$donetoday = false;
		while ($row = $sql->fetch()) {
			$today = false;
			if ($wworker == $_SESSION["id"] && date("Ymd", strtotime($wdate)) == date("Ymd") && $wstatus == 0) {
				$today = true;
			}
			if (!$donetoday) {
				$out[$i][18] .= logwork($today, $wid, $times, $wworker);
				$donetoday = true;
			}
			if (!$today) {
				$wdate = DateTime::createFromFormat("Y-m-d", explode(" ", $wdate)[0]);
				$wdate = $wdate->format("m/d/Y");
				$out[$i][18] .= "<div id=\"$wid\" class=\"well spoiler\"><div class=\"title\">" . $wsummary . " | $wworkername | " . date("n/j/y", strtotime($wdate)) . "</div>
				<div class=\"more-content\"><div style=\"width:49%; float:left;\">" . timetable($wid, $times, 2, $wworker) . "</div><div class=\"desc\" style=\"width:49%; float:left;\">$wdescription
				</div>" . ($_SESSION["id"] == $wworker || $_SESSION["role"] <= 0 ? "<button class=\"btn edit-desc\" style=\"margin-top:20px;\" data-summary=\"$wsummary\" data-date=\"$wdate\">Edit</button>" : "") . "</div></div>";
			}
		}
	}
}
if ($_GET["status"] == 2) {
	$sql = $mysqli->prepare("SELECT SUM(TIMESTAMPDIFF(MINUTE, times.start, times.end) + 1) FROM times LEFT JOIN work ON work.id = times.work WHERE work.workorder = ?");
	for ($i = 0; $i < count($out); $i++) {
		$sql->bind_param("i", $out[$i][0]);
		$sql->execute();
		$sql->bind_result($totes);
		$sql->fetch();
		$out[$i][19] = $totes;
	}
}
/*
$foundtoday = false;
if ($_GET["status"] == 1) {
	$ids2 = join(",", $ids);
	$res = $mysqli->query("SELECT * FROM work WHERE workorder IN ($ids2) ORDER BY date DESC");
	while ($row = $res->fetch_row()) {
		for ($i = 0; $i < count($out); $i++) {
			if ($out[$i][0] == $row[1]) {
				$today = false;
				if (!$foundtoday && $row[4] == $_SESSION["id"] && date("Ymd", strtotime($row[5])) == date("Ymd")) {
					$today = true;
					$foundtoday = true;
				}
				die(var_dump($today));
				if (!$today && !$foundtoday) {
					$out[$i][17] .= "<button class=\"btn btn-primary btn-beginwork\" style=\"float:left; margin-bottom:20px;\">Begin Work for the Day</button>";
				}
				$out[$i][17] .= "<div class=\"well spoiler\"" . (!$today ? " style=\"display:none;\"" : "") . ">
			<div class=\"title\">Log Work Time <i class=\"icon-pencil\"></i></div>
			<div class=\"more-content\"" . ($today ? " style=\"display:block;\"" : "") . ">
			<div style=\"width:49%; float:left;\">
			<form>
			<input id=\"worksummary\" name=\"worksummary\" type=\"text\" style=\"width:50%;\" placeholder=\"Summary\"/><strong> " . date("n/j/y", time()) . "</strong><br/>
			<textarea rows=\"6\" style=\"width:80%;\" placeholder=\"Detailed description\"></textarea><br/>
			<div class=\"form-actions\"><input class=\"btn btn-primary\" type=\"submit\" value=\"End Work for the Day\"/></div>
			</form>
			</div>
			<div style=\"width:49%; float:left;\">
			<table>
				<thead>
					<tr><th></th><th>Start</th><th>Stop</th><th>Elapsed Time</th></tr>
				</thead>
				<tbody>";
			
				$out[$i][17] .= "<tr><td><button class=\"btn btn-success btn-time\">Start <i class=\"icon-time icon-white\"></i></button></td><td></td><td></td><td></td></tr>
				</tbody>
			</table>
			</div>
			</div>
			</div>";
				$out[$i][17] .= 
"<div class=\"well spoiler\"><div class=\"title\">" . $row[2] . " - " . date("n/j/y", strtotime($row[5])) . "</div>
<div class=\"more-content\">" . $row[3] ."<br/>
</div></div>";
			}
		}
	}
}*/
echo json_encode($out);
?>