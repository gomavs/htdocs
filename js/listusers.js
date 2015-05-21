$(document).ready(function() {
	tablemagic($("#users"), "/ajax/listusers.php", function(data){return "<tr><td>" + data[0] + "</td><td>" + data[2] + "</td><td>" + data[3] + "</td><td data-order-by=\"" + data[7] +"\">" + roletostring(data[7]) + "</td><td>" + data[4] + "</td><td>" + (data[5] ? data[5].replace(/(\d{3})(\d{3})(\d{4})/, "$1-$2-$3") : "N/A") + "</td><td>" + (data[6] ? data[6] : "N/A") + "</td><td>" + data[8] + "</td><td>" + data[1] + "</td>" + (role <= 30 ? "<td><a class=\"btn edituser\">Edit</a></td>" : "") + "</tr>";});
	$(".edituser").live("click", function() {
		$("#editusermodal").modal();
		tds = $(this).parent().parent().children();
		$("#firstname").val(tds.eq(1).html());
		$("#lastname").val(tds.eq(2).html());
		$("#email").val(tds.eq(4).html());
		phone = tds.eq(5).html();
		$("#phone").val(phone == "N/A" ? "" : phone);
		ext = tds.eq(6).html();
		$("#extension").val(ext == "N/A" ? "" : ext);
		$("#workcenter").val(tds.eq(7).html());
		$("#role").find("option:contains('" + tds.eq(3).html() + "')").attr("selected", "selected");
		$("#id").val(tds.eq(0).html());
		return false;
	});
	$("#edituserbutton").click(function() {
		$.post("/ajax/edituser.php", $("#edituser").serialize(), function(data) {
			if (data != 1) {
				$("#edituserhelp").html(data);
			} else {
				location.reload();
			}
		});
	});
});
