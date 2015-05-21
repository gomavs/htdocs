$(document).ready(function(){
	$("form#login").submit(function() {
	var username = $("#username").attr("value");
	var password = $("#password").attr("value");
	var remember = $("#remember").get(0).checked;
		$.post("/ajax/login.php", "username="+username+"&password="+password+"&remember="+remember, function(data) {
			$error = $(".alert");
			if (data != 1) {
				$error.fadeIn(1000).html("<strong>Error: </strong>" + data);
			} else {
				window.location.reload(); 
			}
		});
		return false;
	});
});