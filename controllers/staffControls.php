<?php
session_start();
require_once __DIR__."/authGuard.php";
require_once __DIR__."/../models/userModel.php";

if(!isLoggedIn() || $_SESSION["role"]!="manager")
    {
        header("Location: ../views/login.php");
        exit;
    }

if($_SERVER["REQUEST_METHOD"]!="POST")
    {
        header("Location: ../views/manager/manageStaff.php");
        exit;
    }

$action=isset($_POST["action"]) ? $_POST["action"] : "";




if($action=="add")
    {
        $name=trim($_POST["name"]);
        $phone=trim($_POST["phone"]);
        $email=trim($_POST["email"]);
        $pass=$_POST["pass"];
        $role=$_POST["role"];

        $hasError=false;
        $nameError="";
        $phoneError="";
        $emailError="";
        $passError="";
        $roleError="";

        if($name=="" || strlen($name)<3 || !preg_match('/^[a-zA-Z. ]+$/', $name))
            {
                $nameError="enter a valid name (letters only, min 3 characters)";
                $hasError=true;
            }

        if(empty($phone) || !preg_match('/^01[3-9][0-9]{8}$/', $phone))
            {
                $phoneError="use 11 digit number starting with 01";
                $hasError=true;
            }

        if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                $emailError="enter a valid email";
                $hasError=true;
            }
        elseif(emailExists($email))
            {
                $emailError="this email is already registered";
                $hasError=true;
            }

        if(empty($pass) || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $pass))
            {
                $passError="min 8 char with one capital, one small and one number";
                $hasError=true;
            }

        if($role!="barista" && $role!="waiter")
            {
                $roleError="choose barista or waiter";
                $hasError=true;
            }

        if($hasError)
            {
                $url="Location: ../views/manager/manageStaff.php?nameError=".urlencode($nameError)."&phoneError=".urlencode($phoneError)."&emailError=".urlencode($emailError)."&passError=".urlencode($passError)."&roleError=".urlencode($roleError);
                header($url);
                exit;
            }

        $newId=registerStaff($name, $phone, $email, $pass, $role);

        if($newId!=null)
            {
                $msg="Account created. ID: ".$newId;
                header("Location: ../views/manager/manageStaff.php?staffOk=".urlencode($msg));
            }
        else
            {
                header("Location: ../views/manager/manageStaff.php?staffError=".urlencode("could not create account, try again"));
            }
        exit;
    }




elseif($action=="delete")
    {
        $userId=$_POST["userId"];
        $ok=deleteStaff($userId);

        $msg=$ok ? "Staff account removed." : "could not remove that account";
        $key=$ok ? "staffOk" : "staffError";
        header("Location: ../views/manager/manageStaff.php?".$key."=".urlencode($msg));
        exit;
    }

else
    {
        header("Location: ../views/manager/manageStaff.php");
        exit;
    }

?>
