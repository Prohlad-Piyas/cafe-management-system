<?php
require_once __DIR__ . "/../../controllers/authGuard.php";
requireRole("manager");

require_once __DIR__ . "/../../models/orderModel.php";

$summary=getSalesSummary();
$byDay=getSalesByDay();
$bestSellers=getBestSellingItems(5);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sales report | Chalk &amp; Bean</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="dash dash-wide">
    <div class="dash-inner">
        <h1>Sales report</h1>
        <p class="sub">A quick read on how the caf&eacute; is doing.</p>

        <?php dashNav("manager", "reports"); ?>

        <div class="stat-grid">
            <div class="stat-card">
                <div class="num"><?php echo (int)$summary["totalOrders"]; ?></div>
                <div class="label">Total orders</div>
            </div>
            <div class="stat-card">
                <div class="num">&#2547;<?php echo number_format($summary["totalRevenue"], 2); ?></div>
                <div class="label">Total revenue</div>
            </div>
            <div class="stat-card">
                <div class="num"><?php echo (int)$summary["servedOrders"]; ?></div>
                <div class="label">Orders served</div>
            </div>
            <div class="stat-card">
                <div class="num"><?php echo (int)$summary["openOrders"]; ?></div>
                <div class="label">Orders still open</div>
            </div>
        </div>

        <h2 style="font-family:Georgia, serif; font-weight:normal;">Last 7 days</h2>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Orders</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($byDay)==0) { ?>
                    <tr class="muted-row"><td colspan="3">No orders in the last 7 days.</td></tr>
                <?php } ?>

                <?php foreach($byDay as $row) { ?>
                    <tr>
                        <td><?php echo e(date("d M Y", strtotime($row["orderDate"]))); ?></td>
                        <td><?php echo (int)$row["orderCount"]; ?></td>
                        <td>&#2547;<?php echo number_format($row["revenue"], 2); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <h2 style="font-family:Georgia, serif; font-weight:normal;">Best-selling items</h2>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Units sold</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($bestSellers)==0) { ?>
                    <tr class="muted-row"><td colspan="3">No items sold yet.</td></tr>
                <?php } ?>

                <?php foreach($bestSellers as $row) { ?>
                    <tr>
                        <td><?php echo e($row["name"]); ?></td>
                        <td><?php echo (int)$row["unitsSold"]; ?></td>
                        <td>&#2547;<?php echo number_format($row["revenue"], 2); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>

</html>
