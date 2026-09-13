<?php
session_start();
require_once __DIR__."/authGuard.php";
require_once __DIR__."/../models/userModel.php";

if(!isLoggedIn())
    {
        header("Location: ../views/login.php");
        exit;
    }

$userId=$_SESSION["userId"];
$backLink="../views/".dashboardFor($_SESSION["role"]);

if($_SERVER["REQUEST_METHOD"]!="POST")
    {
        header("Location: ../views/profile.php");
        exit;
    }

$action=isset($_POST["action"]) ? $_POST["action"] : "";




if($action=="update")
    {
        $name=trim($_POST["name"]);
        $phone=trim($_POST["phone"]);

        $hasError=false;
        $nameError="";
        $phoneError="";

        if($name=="")
            {
                $nameError="name should be provided";
                $hasError=true;
            }
        elseif(strlen($name)<3)
            {
                $nameError="name must be at least 3 characters";
                $hasError=true;
            }
        elseif(!preg_match('/^[a-zA-Z. ]+$/', $name))
            {
                $nameError="name cannot have numbers or special char";
                $hasError=true;
            }

        if(empty($phone))
            {
                $phoneError="phone should be provided";
                $hasError=true;
            }
        elseif(!preg_match('/^01[3-9][0-9]{8}$/', $phone))
            {
                $phoneError="use 11 digit number starting with 01";
                $hasError=true;
            }

        if($hasError)
            {
                $url="Location: ../views/profile.php?nameError=".urlencode($nameError)."&phoneError=".urlencode($phoneError);
                header($url);
                exit;
            }
        else
            {
                $ok=updateProfile($userId, $name, $phone);
                if($ok)
                    {
                        $_SESSION["name"]=$name;
                        header("Location: ../views/profile.php?profileOk=".urlencode("Profile updated."));
                        exit;
                    }
                else
                    {
                        header("Location: ../views/profile.php?profileError=".urlencode("could not save changes, try again"));
                        exit;
                    }
            }
    }




elseif($action=="delete")
    {
        $currentPass=$_POST["currentPass"];

        if(empty($currentPass) || !checkCurrentPassword($userId, $currentPass))
            {
                $err="type your current password correctly to confirm deletion";
                header("Location: ../views/profile.php?deleteError=".urlencode($err));
                exit;
            }

        deleteAccount($userId);
        $_SESSION=[];
        session_destroy();

        header("Location: ../views/login.php?registerOk=".urlencode("Your account has been deleted."));
        exit;
    }

else
    {
        header("Location: ../views/profile.php");
        exit;
    }

?>
