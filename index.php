<?php

require_once __DIR__."/controllers/authGuard.php";

if(isLoggedIn())
    {
        header("Location: views/".dashboardFor($_SESSION["role"]));
        exit;
    }
else
    {
        header("Location: views/login.php");
        exit;
    }

?>
