<?php
require_once __DIR__ . "/../../controllers/authGuard.php";
requireRole("customer");

require_once __DIR__ . "/../../models/orderModel.php";

$orders=getOrdersByCustomer($_SESSION["userId"]);

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
    <title>Your orders | Chalk &amp; Bean</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="dash dash-wide">
    <div class="dash-inner">
        <h1>Your orders</h1>
        <p class="sub">Signed in as <?php echo e($_SESSION["name"]); ?> (<?php echo e($_SESSION["userId"]); ?>)</p>

        <?php dashNav("customer", "home"); ?>

        <?php if(isset($_GET["orderOk"])) { ?>
            <p class="notice notice-good"><?php showGet("orderOk"); ?></p>
        <?php } ?>

        <?php if(count($orders)==0) { ?>
            <p class="sub">
                You haven't placed an order yet.
                <a href="menu.php">Browse the menu</a> to get started.
            </p>
        <?php } else { ?>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($orders as $order) { ?>
                        <tr id="order-row-<?php echo (int)$order["orderId"]; ?>">
                            <td>#<?php echo (int)$order["orderId"]; ?></td>
                            <td>
                                <ul class="order-items-list">
                                    <?php foreach($order["items"] as $line) { ?>
                                        <li><?php echo (int)$line["quantity"]; ?> &times; <?php echo e($line["name"]); ?></li>
                                    <?php } ?>
                                </ul>
                            </td>
                            <td>&#2547;<?php echo number_format($order["total_amount"], 2); ?></td>
                            <td><?php echo e(ucfirst($order["payment_method"])); ?></td>
                            <td>
                                <span class="badge badge-<?php echo e($order["status"]); ?>" data-order-status="<?php echo (int)$order["orderId"]; ?>">
                                    <?php echo e($statusLabel[$order["status"]]); ?>
                                </span>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <p class="sub" style="font-size:13px;">Status updates on its own every few seconds -- no need to refresh.</p>

        <?php } ?>
    </div>

    <script>
        const statusLabel = {
            received:  "Received",
            preparing: "Preparing",
            prepared:  "Ready to serve",
            served:    "Served"
        };

        function refreshOrderStatuses() {
            fetch("../../controllers/orderStatus.php")
                .then(function (res) { return res.json(); })
                .then(function (rows) {
                    if (!Array.isArray(rows)) {
                        return;
                    }

                    rows.forEach(function (row) {
                        const badge = document.querySelector('[data-order-status="' + row.orderId + '"]');
                        if (!badge) {
                            return;
                        }

                        badge.className = "badge badge-" + row.status;
                        badge.textContent = statusLabel[row.status] || row.status;
                    });
                })
                .catch(function () {
                });
        }

        setInterval(refreshOrderStatuses, 4000);
    </script>
</body>

</html>
