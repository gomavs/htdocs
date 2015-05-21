$(document).ready(function() {
	$("#updatebutton").click(function() {
		$.post("/ajax/myaccount.php", $("#myaccount").serialize(), function(data) {
			$(".control-group").removeClass("error");
			$(".help-inline").html("");
			if (data == 1) {
				location.reload();
			} else {
				$.each(data, function() {
					$("#" + this["field"]).parent().parent().addClass("error");
					$("#" + this["field"] + "~.help-inline").html(this["error"]);
				});
			}
		}, "json");
		return false;
	});
});
