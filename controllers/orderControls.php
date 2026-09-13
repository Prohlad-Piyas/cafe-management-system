<?php
session_start();
require_once __DIR__."/authGuard.php";
require_once __DIR__."/../models/orderModel.php";

if(!isLoggedIn())
    {
        header("Location: ../views/login.php");
        exit;
    }

if($_SERVER["REQUEST_METHOD"]!="POST")
    {
        header("Location: ../views/".dashboardFor($_SESSION["role"]));
        exit;
    }

$action=isset($_POST["action"]) ? $_POST["action"] : "";




if($action=="place")
    {
        if($_SESSION["role"]!="customer")
            {
                header("Location: ../views/login.php");
                exit;
            }

        $qty=isset($_POST["qty"]) ? $_POST["qty"] : [];
        $paymentMethod=isset($_POST["paymentMethod"]) ? $_POST["paymentMethod"] : "cash";

        if($paymentMethod!="cash" && $paymentMethod!="ewallet")
            {
                $paymentMethod="cash";
            }

        $cart=[];
        foreach($qty as $itemId => $q)
            {
                if((int)$q>0)
                    {
                        $cart[(int)$itemId]=(int)$q;
                    }
            }

        if(count($cart)==0)
            {
                header("Location: ../views/customer/menu.php?orderError=".urlencode("pick at least one item first"));
                exit;
            }

        $orderId=placeOrder($_SESSION["userId"], $cart, $paymentMethod);

        if($orderId!=null)
            {
                $msg="Order #".$orderId." placed. Track it from your dashboard.";
                header("Location: ../views/customer/customerDashboard.php?orderOk=".urlencode($msg));
            }
        else
            {
                header("Location: ../views/customer/menu.php?orderError=".urlencode("could not place order, try again"));
            }
        exit;
    }




elseif($action=="assign")
    {
        if($_SESSION["role"]!="manager")
            {
                header("Location: ../views/login.php");
                exit;
            }

        $orderId=(int)$_POST["orderId"];
        $baristaName=assignOrderToAvailableBarista($orderId);

        if($baristaName!=null)
            {
                $msg="Order #".$orderId." assigned to ".$baristaName.".";
                header("Location: ../views/manager/manageOrders.php?assignOk=".urlencode($msg));
            }
        else
            {
                header("Location: ../views/manager/manageOrders.php?assignError=".urlencode("could not assign, no barista account exists yet"));
            }
        exit;
    }




elseif($action=="prepared")
    {
        if($_SESSION["role"]!="barista")
            {
                header("Location: ../views/login.php");
                exit;
            }

        $orderId=(int)$_POST["orderId"];
        $ok=markOrderPrepared($orderId, $_SESSION["userId"]);

        $msg=$ok ? "Order #".$orderId." marked prepared and sent to the floor." : "could not update that order";
        $key=$ok ? "queueOk" : "queueError";
        header("Location: ../views/barista/baristaDashboard.php?".$key."=".urlencode($msg));
        exit;
    }




elseif($action=="served")
    {
        if($_SESSION["role"]!="waiter")
            {
                header("Location: ../views/login.php");
                exit;
            }

        $orderId=(int)$_POST["orderId"];
        $ok=markOrderServed($orderId, $_SESSION["userId"]);

        $msg=$ok ? "Order #".$orderId." served and closed." : "could not update that order";
        $key=$ok ? "floorOk" : "floorError";
        header("Location: ../views/waiter/waiterDashboard.php?".$key."=".urlencode($msg));
        exit;
    }

else
    {
        header("Location: ../views/".dashboardFor($_SESSION["role"]));
        exit;
    }

?>
