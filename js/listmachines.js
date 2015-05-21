$(document).ready(function() {
	tablemagic($("#machines"), "/ajax/listmachines.php", function(data){return "<tr><td>" + data[0] + "</td><td>" + data[8] + "</td><td>" + data[9] + "</td><td>" + data[3] + "</td><td>" + data[5] + "</td><td>" + data[6] + "</td><td>" + data[4] + "</td><td><a class=\"btn editmachine\">Edit</a></td></tr>";});
	$(".editmachine").live("click", function() {
		$("#editmachinemodal").modal();
		tds = $(this).parent().parent().children();
		$("#make").find("option:contains('" + tds.eq(2).html() + "')").attr("selected", "selected");
		$("#model").val(tds.eq(3).html());
		$("#serial").val(tds.eq(6).html());
		$("#year").val(tds.eq(4).html());
		$("#workcenter").val(tds.eq(5).html());
		$("#nick").val(tds.eq(1).html());
		$("#id").val(tds.eq(0).html());
		return false;
	});
	$("#editmachinebutton").click(function() {
		$.post("/ajax/editmachine.php", $("#editmachine").serialize(), function(data) {
			if (data != 1) {
				$("#editmachinehelp").html(data);
			} else {
				location.reload();
			}
		});
	});
});
