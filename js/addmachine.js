$(document).ready(function() {
	$("#addmachine").submit(function() {
		$.post("/ajax/addmachine.php", $("#addmachine").serialize(), function(data) {
			if (data.success == 1) {
				alert("success");
			} else {
				$.each(data, function() {
					$("#" + this.field).parent().parent().addClass("error");
					$("#" + this.field + "~.help-inline").html(this.error);
				});
			}
		}, "json");
		return false;
	});
	$("#type").change(function() {
		thing = $("#depend-" + this.id);
		thing.children().hide();
		thing.find("#option-" + this.value).show();
	});
	$("#addmakebutton").click(function() {
		$.post("/ajax/addmake.php", "addmakename=" + $("#addmakename").val(), function(data) {
			if (data != "0") {
				$("#make").prepend("<option value=\"" + data + "\">" + $("#addmakename").val() + "</option>").val($("#addmakename").val());
				$("#addmakename").val("");
				$("#addmakemodal").modal("hide");
			} else {
				return false;
			}
		});
	});
});