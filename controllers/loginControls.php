<?php
session_start();
require_once __DIR__."/authGuard.php";
require_once __DIR__."/../models/userModel.php";

if($_SERVER["REQUEST_METHOD"]=="POST")
    {
        $email=trim($_POST["email"]);
        $pass=$_POST["pass"];

        $hasError=false;
        $emailError="";
        $passError="";
        $loginError="";

        if(empty($email))
            {
                $emailError="email should be provided";
                $hasError=true;
            }
        elseif(!filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                $emailError="Invalid email format";
                $hasError=true;
            }

        if(empty($pass))
            {
                $passError="password should be provided";
                $hasError=true;
            }

        if($hasError)
            {
                $url="Location: ../views/login.php?emailError=".urlencode($emailError)."&passError=".urlencode($passError)."&email=".urlencode($email);
                header($url);
                exit;
            }
        else
            {
                $user=login($email, $pass);

                if($user==null)
                    {
                        $loginError="Email or password is incorrect";
                        $url="Location: ../views/login.php?loginError=".urlencode($loginError)."&email=".urlencode($email);
                        header($url);
                        exit;
                    }
                else
                    {
                        session_regenerate_id(true);

                        $_SESSION["userId"]=$user["userId"];
                        $_SESSION["name"]=$user["name"];
                        $_SESSION["role"]=$user["role"];

                        header("Location: ../views/".dashboardFor($user["role"]));
                        exit;
                    }
            }
    }
else
    {
        header("Location: ../views/login.php");
        exit;
    }

?>
