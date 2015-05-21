$(document).ready(function() {
	$(".btn-addproblem").click(function() {
		$("#addproblemmodal").modal();
	});
	$("#addproblembutton").click(function() {
		$.post("/ajax/addproblem.php", $("#addproblemform").serialize(), function(data) {
			if (data == 1) {
				location.reload();
			}
		});
	});
});