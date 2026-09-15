<?php
session_start();
require_once __DIR__."/authGuard.php";
require_once __DIR__."/../models/orderModel.php";



header("Content-Type: application/json");

if(!isLoggedIn() || $_SESSION["role"]!="customer")
    {
        http_response_code(403);
        echo json_encode(["error" => "not signed in"]);
        exit;
    }

$rows=getOrderStatusesForCustomer($_SESSION["userId"]);

echo json_encode($rows);

?>
