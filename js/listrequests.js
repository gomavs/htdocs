$(document).ready(function() {
	tablemagic($("#requests"), "/ajax/workorders.php", function(data){return "<tr id=\"" + data[0] + "\"><td>" + data[1] + "</td><td>" + data[15] + "</td><td>" + data[7] + "</td><td>" + data[9] + "</td><td data-order-by='" + (data[10] == "High" ? 1 : data[10] == "Medium" ? 2 : 3) + "'>" + data[10] + "</td><td>" + data[16] + "</td><td data-order-by='" + data[5] + "'>" + dateformat(data[5]) + "</td><td>" + (data[11] === null ? "N/A" : "<span data-content=\"" + data[11] + "\" data-placement=\"right\" data-original-title=\"Notes\" onmouseover=\"$(this).popover('show');\" onmouseout=\"$(this).popover('hide');\">" + data[11].substring(0, 18) + "..</span>") + "</td>" + (role <= 50 ? "<td><a class=\"btn review\">Review</a></td>" : "") + "</tr>";}, ["status=0"]);
	$("#date").datepicker();
	$(".review").live("click", function() {
		tds = $(this).parent().parent().children();
		$("#reviewmodal").modal();
		$("#info").html("Requested by <u>" + tds.eq(5).html() + "</u> on <u>" + tds.eq(6).html() + "</u> for <u>" + tds.eq(1).html() + "</u><br/><br/>");
		$("#issue").find("option:contains('" + tds.eq(3).html() + "')").attr("selected", "selected");
		$("#urgency").val(tds.eq(4).html());
		$("#notes").val(tds.eq(7).children("span").attr("data-content"));
		$("#id").val($(this).parent().parent().attr("id"));
		return false;
	});
	$("#assignworkbutton, #rejectworkbutton").click(function() {
		$.post("/ajax/assignwork.php", $("#assignwork").serialize() + ($(this).attr("id") == "rejectworkbutton" ? "&reject=1" : "&reject=0"), function(data) {
			if (data != 1) {
				$("#assignworkhelp").html(data);
			} else {
				location.reload();
			}
		});
	});
});