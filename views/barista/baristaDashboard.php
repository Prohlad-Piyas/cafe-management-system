<?php
require_once __DIR__ . "/../../controllers/authGuard.php";
requireRole("barista");

require_once __DIR__ . "/../../models/orderModel.php";

$queue=getBaristaQueue($_SESSION["userId"]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order queue | Chalk &amp; Bean</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="dash dash-wide">
    <div class="dash-inner">
        <h1>Your order queue</h1>
        <p class="sub">Signed in as <?php echo e($_SESSION["name"]); ?> (<?php echo e($_SESSION["userId"]); ?>)</p>

        <?php dashNav("barista", "home"); ?>

        <?php if(isset($_GET["queueOk"])) { ?>
            <p class="notice notice-good"><?php showGet("queueOk"); ?></p>
        <?php } ?>

        <?php if(isset($_GET["queueError"])) { ?>
            <p class="notice notice-bad"><?php showGet("queueError"); ?></p>
        <?php } ?>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($queue)==0) { ?>
                    <tr class="muted-row"><td colspan="4">Nothing assigned to you right now.</td></tr>
                <?php } ?>

                <?php foreach($queue as $order) { ?>
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
                        <td>
                            <form action="../../controllers/orderControls.php" method="post">
                                <input type="hidden" name="action" value="prepared">
                                <input type="hidden" name="orderId" value="<?php echo (int)$order["orderId"]; ?>">
                                <button type="submit" class="btn btn-sm" style="width:auto;">Mark prepared</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>

</html>
