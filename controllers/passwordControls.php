<?php
session_start();
require_once __DIR__."/authGuard.php";
require_once __DIR__."/../models/userModel.php";

if($_SERVER["REQUEST_METHOD"]=="POST")
    {
        if(!isLoggedIn())
            {
                header("Location: ../views/login.php");
                exit;
            }

        $currentPass=$_POST["currentPass"];
        $newPass=$_POST["newPass"];
        $conPass=$_POST["conPass"];

        $userId=$_SESSION["userId"];

        $hasError=false;
        $currentPassError="";
        $newPassError="";
        $conPassError="";

        if(empty($currentPass))
            {
                $currentPassError="current password should be provided";
                $hasError=true;
            }
        elseif(!checkCurrentPassword($userId, $currentPass))
            {
                $currentPassError="current password is incorrect";
                $hasError=true;
            }

        if(empty($newPass))
            {
                $newPassError="new password should be provided";
                $hasError=true;
            }
        elseif(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $newPass))
            {
                $newPassError="min 8 char with one capital, one small and one number";
                $hasError=true;
            }
        elseif($newPass==$currentPass)
            {
                $newPassError="new password must be different from the old one";
                $hasError=true;
            }

        if($conPass!=$newPass)
            {
                $conPassError="both password must match";
                $hasError=true;
            }

        if($hasError)
            {
                $url="Location: ../views/changePassword.php?currentPassError=".urlencode($currentPassError)."&newPassError=".urlencode($newPassError)."&conPassError=".urlencode($conPassError);
                header($url);
                exit;
            }
        else
            {
                $success=updatePassword($userId, $newPass);

                if($success)
                    {
                        session_regenerate_id(true);
                        header("Location: ../views/changePassword.php?changeOk=".urlencode("Password updated."));
                        exit;
                    }
                else
                    {
                        header("Location: ../views/changePassword.php?changeError=".urlencode("could not save new password, try again"));
                        exit;
                    }
            }
    }
else
    {
        header("Location: ../views/changePassword.php");
        exit;
    }

?>
