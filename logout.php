<?php
session_start();
unset($_SESSION["id"]);
unset($_SESSION["username"]);
unset($_SESSION["first"]);
unset($_SESSION["last"]);
unset($_SESSION["email"]);
unset($_SESSION["phone"]);
unset($_SESSION["extension"]);
unset($_SESSION["role"]);
setcookie("remember", "", -3600);
session_regenerate_id(true);
header("Location: /login");
?>