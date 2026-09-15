<?php
require_once __DIR__ . "/../../controllers/authGuard.php";
requireRole("manager");

require_once __DIR__ . "/../../models/orderModel.php";

$incoming=getIncomingOrders();
$summary=getSalesSummary();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manager desk | Chalk &amp; Bean</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="dash dash-wide">
    <div class="dash-inner">
        <h1>Manager desk</h1>
        <p class="sub">Signed in as <?php echo e($_SESSION["name"]); ?> (<?php echo e($_SESSION["userId"]); ?>)</p>

        <?php dashNav("manager", "home"); ?>

        <div class="stat-grid">
            <div class="stat-card">
                <div class="num"><?php echo (int)count($incoming); ?></div>
                <div class="label">Orders waiting for a barista</div>
            </div>
            <div class="stat-card">
                <div class="num"><?php echo (int)$summary["openOrders"]; ?></div>
                <div class="label">Orders still open</div>
            </div>
            <div class="stat-card">
                <div class="num">&#2547;<?php echo number_format($summary["totalRevenue"], 2); ?></div>
                <div class="label">Total revenue</div>
            </div>
        </div>

        <p class="sub">
            Use the links above to review <a href="manageOrders.php">incoming orders</a>,
            update the <a href="manageMenu.php">menu</a>,
            manage <a href="manageStaff.php">staff accounts</a>,
            or check the <a href="salesReport.php">sales report</a>.
        </p>
    </div>
</body>

</html>
