<?php
session_start();
require_once __DIR__."/authGuard.php";
require_once __DIR__."/../models/menuModel.php";

if(!isLoggedIn() || $_SESSION["role"]!="manager")
    {
        header("Location: ../views/login.php");
        exit;
    }

if($_SERVER["REQUEST_METHOD"]!="POST")
    {
        header("Location: ../views/manager/manageMenu.php");
        exit;
    }

$action=isset($_POST["action"]) ? $_POST["action"] : "";




if($action=="add" || $action=="update")
    {
        $name=trim($_POST["name"]);
        $description=trim($_POST["description"]);
        $price=trim($_POST["price"]);
        $category=trim($_POST["category"]);
        $available=isset($_POST["available"]) ? 1 : 0;

        $hasError=false;
        $nameError="";
        $priceError="";
        $categoryError="";

        if($name=="" || strlen($name)<2)
            {
                $nameError="item name should be at least 2 characters";
                $hasError=true;
            }

        if($price=="" || !is_numeric($price) || $price<=0)
            {
                $priceError="price must be a number greater than 0";
                $hasError=true;
            }

        if($category=="")
            {
                $categoryError="category should be provided";
                $hasError=true;
            }

        if($hasError)
            {
                $url="Location: ../views/manager/manageMenu.php?nameError=".urlencode($nameError)."&priceError=".urlencode($priceError)."&categoryError=".urlencode($categoryError);
                header($url);
                exit;
            }

        if($action=="add")
            {
                $newId=addMenuItem($name, $description, (float)$price, $category);
                $msg=($newId!=null) ? "Menu item added." : "could not add item, try again";
            }
        else
            {
                $itemId=(int)$_POST["itemId"];
                $ok=updateMenuItem($itemId, $name, $description, (float)$price, $category, $available);
                $msg=$ok ? "Menu item updated." : "could not save changes, try again";
            }

        $key=($action=="add" && $newId!=null) ? "menuOk" : (($action=="update" && $ok) ? "menuOk" : "menuError");
        header("Location: ../views/manager/manageMenu.php?".$key."=".urlencode($msg));
        exit;
    }




elseif($action=="delete")
    {
        $itemId=(int)$_POST["itemId"];
        $ok=deleteMenuItem($itemId);

        $msg=$ok ? "Menu item removed." : "could not remove item, try again";
        $key=$ok ? "menuOk" : "menuError";
        header("Location: ../views/manager/manageMenu.php?".$key."=".urlencode($msg));
        exit;
    }

else
    {
        header("Location: ../views/manager/manageMenu.php");
        exit;
    }

?>
