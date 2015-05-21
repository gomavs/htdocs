$(document).ready(function() {
	$("#worktype").change(function() {
		thing = $("#depend-" + this.id);
		thing.children().hide();
		thing.find(".option-" + this.value).show();
	});
	$("#requestwork").submit(function() {
		$.post("/ajax/requestwork.php", $("#requestwork").serialize(), function(data) {
			$(".control-group").removeClass("error");
			$(".help-inline").html("");
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
});