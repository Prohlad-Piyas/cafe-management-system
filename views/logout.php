<?php
require_once __DIR__."/../controllers/authGuard.php";

$_SESSION=[];
session_unset();
session_destroy();

header("Location: login.php");
exit;

?>
