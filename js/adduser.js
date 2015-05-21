$(document).ready(function() {
	var username;
	var password;
	$("#adduser").submit(function() {
		$.post("/ajax/adduser.php", $("#adduser").serialize(), function(data) {
			$(".control-group").removeClass("error");
			$(".help-inline").html("");
			if (data["success"] == 1) {
				username = data["username"];
				password = data["password"];
				$("#adduser :input").attr("disabled", "disabled");
				$("#success > .modal-body").html("<p>" + $("#firstname").val() + " " + $("#lastname").val() + "'s acocunt has been created!</p><p><strong>Username: </strong> " + username + "<br/><strong>Password: </strong> " + password + "</p>");
				$("#btn-name").html("Send to " + $("#firstname").val());
				$("#success").modal();

			} else {
				$.each(data, function() {
					$("#" + this["field"]).parent().parent().addClass("error");
					$("#" + this["field"] + "~.help-inline").html(this["error"]);
				});
			}
		}, "json");
		return false;
	});
	$("#btn-newuser").click(function() {
		$("#adduser :input").removeAttr("disabled");
		$("#adduser :input:not(:submit)").val("");
	});
	$("#btn-me, #btn-name").click(function() {
		email(this, username, password);
	});
});
function email(button, username, password) {
	stuff = "firstname=" +  $("#firstname").val() + "&lastname=" +  $("#firstname").val() + "&username=" + username + "&pass=" + password;
	if (button.name == "btn-me") {
		stuff = "type=me&send=<?php echo $_SESSION['email']; ?>&" + stuff;
	} else if (button.name == "btn-name") {
		stuff = "type=them&send="+window.creds.email + "&" + stuff;
	}
	button.disabled = true;
	button.innerHTML = "Sent!";
	$.post("/ajax/adduser-mail.php", stuff);
}