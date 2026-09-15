<?php
require_once __DIR__ . "/../../controllers/authGuard.php";
requireRole("manager");

require_once __DIR__ . "/../../models/orderModel.php";

$incoming=getIncomingOrders();
$allOrders=getAllOrders();
$workload=getBaristaWorkload();

$statusLabel=[
    "received"  => "Received",
    "preparing" => "Preparing",
    "prepared"  => "Ready to serve",
    "served"    => "Served",
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Incoming orders | Chalk &amp; Bean</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="dash dash-wide">
    <div class="dash-inner">
        <h1>Incoming orders</h1>
        <p class="sub">Review new orders and hand each one to the barista with the lightest load.</p>

        <?php dashNav("manager", "orders"); ?>

        <?php if(isset($_GET["assignOk"])) { ?>
            <p class="notice notice-good"><?php showGet("assignOk"); ?></p>
        <?php } ?>

        <?php if(isset($_GET["assignError"])) { ?>
            <p class="notice notice-bad"><?php showGet("assignError"); ?></p>
        <?php } ?>

        <?php if(count($workload)>0) { ?>
            <div class="stat-grid">
                <?php foreach($workload as $b) { ?>
                    <div class="stat-card">
                        <div class="num"><?php echo (int)$b["activeOrders"]; ?></div>
                        <div class="label"><?php echo e($b["name"]); ?> &middot; active orders</div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <h2 style="font-family:Georgia, serif; font-weight:normal;">Waiting for a barista</h2>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($incoming)==0) { ?>
                    <tr class="muted-row"><td colspan="5">Nothing waiting -- all orders have been assigned.</td></tr>
                <?php } ?>

                <?php foreach($incoming as $order) { ?>
                    <tr>
                        <td>#<?php echo (int)$order["orderId"]; ?></td>
                        <td><?php echo e($order["customerName"]); ?></td>
                        <td>
                            <ul class="order-items-list">
                                <?php foreach($order["items"] as $line) { ?>
                                    <li><?php echo (int)$line["quantity"]; ?> &times; <?php echo e($line["name"]); ?></li>
                                <?php } ?>
                            </ul>
                        </td>
                        <td>&#2547;<?php echo number_format($order["total_amount"], 2); ?></td>
                        <td>
                            <form action="../../controllers/orderControls.php" method="post">
                                <input type="hidden" name="action" value="assign">
                                <input type="hidden" name="orderId" value="<?php echo (int)$order["orderId"]; ?>">
                                <button type="submit" class="btn btn-sm" style="width:auto;">Assign to available barista</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <h2 style="font-family:Georgia, serif; font-weight:normal;">All orders</h2>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Barista</th>
                    <th>Waiter</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($allOrders)==0) { ?>
                    <tr class="muted-row"><td colspan="6">No orders placed yet.</td></tr>
                <?php } ?>

                <?php foreach($allOrders as $order) { ?>
                    <tr>
                        <td>#<?php echo (int)$order["orderId"]; ?></td>
                        <td><?php echo e($order["customerName"]); ?></td>
                        <td><?php echo $order["baristaName"] ? e($order["baristaName"]) : "&mdash;"; ?></td>
                        <td><?php echo $order["waiterName"] ? e($order["waiterName"]) : "&mdash;"; ?></td>
                        <td>&#2547;<?php echo number_format($order["total_amount"], 2); ?></td>
                        <td><span class="badge badge-<?php echo e($order["status"]); ?>"><?php echo e($statusLabel[$order["status"]]); ?></span></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>

</html>
