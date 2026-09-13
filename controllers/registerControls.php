<?php
session_start();
require_once __DIR__."/authGuard.php";
require_once __DIR__."/../models/userModel.php";

if($_SERVER["REQUEST_METHOD"]=="POST")
    {
        $name=trim($_POST["name"]);
        $phone=trim($_POST["phone"]);
        $email=trim($_POST["email"]);
        $pass=$_POST["pass"];
        $conPass=$_POST["conPass"];

        $hasError=false;
        $nameError="";
        $phoneError="";
        $emailError="";
        $passError="";
        $conPassError="";

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
        elseif(emailExists($email))
            {
                $emailError="this email is already registered";
                $hasError=true;
            }

        if(empty($pass))
            {
                $passError="password should be provided";
                $hasError=true;
            }
        elseif(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $pass))
            {
                $passError="min 8 char with one capital, one small and one number";
                $hasError=true;
            }

        if($conPass!=$pass)
            {
                $conPassError="both password must match";
                $hasError=true;
            }

        if($hasError)
            {
                $url="Location: ../views/register.php?nameError=".urlencode($nameError)."&phoneError=".urlencode($phoneError)."&emailError=".urlencode($emailError)."&passError=".urlencode($passError)."&conPassError=".urlencode($conPassError)."&name=".urlencode($name)."&phone=".urlencode($phone)."&email=".urlencode($email);
                header($url);
                exit;
            }
        else
            {
                $userId=registerCustomer($name, $phone, $email, $pass);

                if($userId==null)
                    {
                        $url="Location: ../views/register.php?registerError=".urlencode("could not create account, try again");
                        header($url);
                        exit;
                    }
                else
                    {
                        $ok="Account created. Your customer ID is ".$userId.". Sign in below.";
                        header("Location: ../views/login.php?registerOk=".urlencode($ok));
                        exit;
                    }
            }
    }
else
    {
        header("Location: ../views/register.php");
        exit;
    }

?>
