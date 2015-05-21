$(document).ready(function() {
	tablemagic($("#requests"), "/ajax/workorders.php", function(data){return "<tr id=\"" + data[0] + "\"><td>" + data[2] + "</td><td>" + data[15] + "</td><td>" + data[9] + "</td><td data-order-by='" + (data[10] == "High" ? 1 : data[10] == "Medium" ? 2 : 3) + "'>" + data[10] + "</td><td>" + data[16] + "</td><td data-order-by='" + data[5] + "'>" + dateformat(data[5]) + "</td><td>" + data[17] + "</td><td>" + data[13] + "</td><td data-order-by='" + data[6] + "'>" + dateformat(data[6], false) + "</td><td>" + (data[11] === null ? "N/A" : "<span data-content=\"" + data[11] + "\" data-placement=\"right\" data-original-title=\"Notes\" onmouseover=\"$(this).popover('show');\" onmouseout=\"$(this).popover('hide');\">" + data[11].substring(0, 18) + "..</span>") + "</td><td><a class=\"btn details\" data-toggle=\"button\">Details</a></td></tr><tr style=\"display:none; border-top:none;\"><td style=\"border-top:none;\"colspan=\"11\">" + data[18] + "</td></tr>";}, ["status=1"]);
	$("#editdate").datepicker();
	$(".details").live("click", function() {
		$(this).parent().parent().next().toggle();
		return false;
	});
	$(".spoiler .title").live("click", function() {
		$(this).next().slideToggle();
	});
	$(".btn-beginwork").live("click", function() {
		btn = $(this);
		$.post("/ajax/beginwork.php", {workorder:$(this).parent().parent().prev().children().eq(0).html(),begin:1}, function(data) {
			btn.hide().nextAll(".well.spoiler:first").attr("id", data).show().children().eq(1).slideDown().find("tbody").children("tr:not(:last)").remove();
		});
	});
	$(".btn-endwork").live("click", function() {
		$.post("/ajax/beginwork.php", {workorder:$(this).parents("tr").prev().children().eq(0).html(),begin:0,summary:$("#worksummary").val(),desc:$("#workdesc").val(),work:$(this).parents(".well.spoiler").attr("id")});
		//now = new Date();
		//sp = $(this).parents(".well.spoiler").hide();
		//$("<div id=\"" + sp.attr("id") + "\" class=\"well spoiler\"><div class=\"title\">" + $("#worksummary").val() + " - " + ((now.getMonth() + 1) + "/" + now.getDate() + "/" + now.getFullYear().toString().substring(2)) + "</div><div class=\"more-content\">" + $("#workdesc").val() + "<br/></div></div>").insertBefore(sp.next());
		location.reload();
	});
	$(".edit-time").live("click", function() {
		$("#edittimemodal").modal();
		$("#timestart").val($(this).parent().next().html());
		$("#timestop").val($(this).parent().next().next().html());
		$("#timeid").val($(this).attr("data-time-id"));
	});
	$(".edit-desc").live("click", function() {
		$("#editdescmodal").modal();
		$("#editsummary").val($(this).attr("data-summary"));
		$("#editdescription").val($(this).prev().html().trim());
		$("#editdate").val($(this).attr("data-date"))
		$("#workid").val($(this).parent().parent().attr("id"));
	});
	$("")
	$("#edittimebutton").click(function() {
		$.post("/ajax/edittime.php", $("#edittimeform").serialize(), function(data) {
			if (!data.error) {
				$("#edittimehelp").html("");
				$("button[data-time-id=\"" + $("#timeid").val() + "\"]").parent().next().html($("#timestart").val()).next().html($("#timestop").val()).next().html(data.elapsed);
				$("#edittimemodal").modal("hide");
			} else {
				$("#edittimehelp").html(data.error);
			}
		}, "json");
	});
	$("#editdescbutton").click(function() {
		$.post("/ajax/editwork.php", $("#editdescform").serialize(), function(data) {
			if (!data.error) {
				location.reload();
			} else {
				$("#editdeschelp").html(data.error);
			}
		}, "json");
	});
	$(".btn-time").live("click", function() {
		now = new Date();
		nows = (now.getHours() % 12 === 0 ? "12" : now.getHours() % 12) + ":" + (now.getMinutes() < 10 ? "0" : "") + now.getMinutes() + " " + (now.getHours() < 12 ? "AM" : "PM");
		btn = $(this);
		if (btn.hasClass("btn-success")) {
			$.post("/ajax/time.php", {start:1, id:btn.parents(".well.spoiler").attr("id")}, function(data) {
				btn.addClass("btn-danger");
				btn.removeClass("btn-success");
				btn.html("Stop <i class=\"icon-time icon-white\">");
				btn.parent().next().html(nows);
			});
		} else {
			$.post("/ajax/time.php", {start:0, id:btn.parents(".well.spoiler").attr("id")}, function(data) {
				btn.addClass("btn-success");
				btn.removeClass("btn-danger");
				btn.html("Start <i class=\"icon-time icon-white\">");
				clone = btn.parent().next().next().html(nows).next().html(data).parent().clone();
				clone.children().eq(1).html("").next().html("").next().html("");
				clone.appendTo(btn.parent().parent().parent());
				btn.remove();
			});
		}
	});
	$(".btn-close").live("click", function() {
		$.post("/ajax/closeorder.php", {workorder:$(this).parent().parent().prev().children().eq(0).html()}, function() {
			window.location.href = "/closedorders";
		});
	});
	$(".btn-editorder").live("click", function() {
		tds = $(this).parent().parent().prev().children();
		$("#editordermodal").modal();
		$("#issue").val(tds.eq(2).html());
		$("#urgency").val(tds.eq(3).html());
		$("#assignto").find("option:contains(" + tds.eq(6).html() + ")").attr("selected", "selected");
		$("#date").val(tds.eq(8).html());
		$("#esthours").val(tds.eq(7).html());
		$("#notes").val(tds.eq(9).children("span").attr("data-content"));
		$("#editworkid").val($(this).parent().parent().prev().attr("id"));
	});
	$("#editorderbutton").click(function() {
		$.post("/ajax/assignwork.php", $("#assignwork").serialize() + "&reject=0", function(data) {
			if (data != 1) {
				$("#editorderhelp").html(data);
			} else {
				location.reload();
			}
		});
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