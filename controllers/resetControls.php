<?php
session_start();
require_once __DIR__."/authGuard.php";
require_once __DIR__."/../models/userModel.php";

if($_SERVER["REQUEST_METHOD"]=="POST")
    {
        $action=$_POST["action"]??"";

        

        if($action=="request")
            {
                $email=trim($_POST["email"]);

                $hasError=false;
                $emailError="";

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

                if($hasError)
                    {
                        $url="Location: ../views/forgotPassword.php?emailError=".urlencode($emailError)."&email=".urlencode($email);
                        header($url);
                        exit;
                    }
                else
                    {
                        $user=findUserByEmail($email);
                        $ok="If that email is registered, a reset link has been created.";
                        $url="Location: ../views/forgotPassword.php?requestOk=".urlencode($ok);

                        if($user!=null)
                            {
                                $token=createResetToken($user["userId"]);

                                if($token!=null)
                                    {
                                        $link="resetPassword.php?token=".urlencode($token);
                                        $url=$url."&demoLink=".urlencode($link);
                                    }
                            }

                        header($url);
                        exit;
                    }
            }

        

        elseif($action=="reset")
            {
                $token=$_POST["token"];
                $newPass=$_POST["newPass"];
                $conPass=$_POST["conPass"];

                $userId=findUserIdByToken($token);

                if($userId==null)
                    {
                        $resetError="This reset link is invalid or has expired. Request a new one.";
                        header("Location: ../views/forgotPassword.php?resetError=".urlencode($resetError));
                        exit;
                    }

                $hasError=false;
                $newPassError="";
                $conPassError="";

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

                if($conPass!=$newPass)
                    {
                        $conPassError="both password must match";
                        $hasError=true;
                    }

                if($hasError)
                    {
                        $url="Location: ../views/resetPassword.php?token=".urlencode($token)."&newPassError=".urlencode($newPassError)."&conPassError=".urlencode($conPassError);
                        header($url);
                        exit;
                    }
                else
                    {
                        $success=updatePassword($userId, $newPass);

                        if($success)
                            {
                                consumeResetToken($token);
                                header("Location: ../views/login.php?registerOk=".urlencode("Password changed. Sign in with the new one."));
                                exit;
                            }
                        else
                            {
                                $url="Location: ../views/resetPassword.php?token=".urlencode($token)."&resetError=".urlencode("could not save new password, try again");
                                header($url);
                                exit;
                            }
                    }
            }
        else
            {
                header("Location: ../views/forgotPassword.php");
                exit;
            }
    }
else
    {
        header("Location: ../views/forgotPassword.php");
        exit;
    }

?>
