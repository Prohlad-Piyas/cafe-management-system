<?php
require_once __DIR__ . "/../../controllers/authGuard.php";
requireRole("customer");

require_once __DIR__ . "/../../models/menuModel.php";

$items=getAllMenuItems(true);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menu | Chalk &amp; Bean</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="dash dash-wide">
    <div class="dash-inner">
        <h1>Browse the menu</h1>
        <p class="sub">Pick quantities for what you want, choose how you'll pay, then place the order.</p>

        <?php dashNav("customer", "menu"); ?>

        <?php if(isset($_GET["orderError"])) { ?>
            <p class="notice notice-bad"><?php showGet("orderError"); ?></p>
        <?php } ?>

        <?php if(count($items)==0) { ?>
            <p class="sub">Nothing on the menu right now -- please check back soon.</p>
        <?php } else { ?>

            <form action="../../controllers/orderControls.php" method="post">
                <input type="hidden" name="action" value="place">

                <div class="menu-grid">
                    <?php foreach($items as $item) { ?>
                        <div class="menu-card">
                            <span class="cat"><?php echo e($item["category"]); ?></span>
                            <h3><?php echo e($item["name"]); ?></h3>
                            <p><?php echo e($item["description"]); ?></p>
                            <div class="price">&#2547;<?php echo number_format($item["price"], 2); ?></div>

                            <div class="qty-row">
                                <label for="qty<?php echo (int)$item["itemId"]; ?>">Qty</label>
                                <input class="qty-input" type="number" min="0" max="20" value="0"
                                       id="qty<?php echo (int)$item["itemId"]; ?>"
                                       name="qty[<?php echo (int)$item["itemId"]; ?>]">
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <div class="panel" style="max-width:420px;">
                    <h2>Payment method</h2>

                    <div class="field">
                        <select name="paymentMethod">
                            <option value="cash">Cash</option>
                            <option value="ewallet">E-wallet</option>
                        </select>
                    </div>

                    <button type="submit" class="btn">Place order</button>
                </div>
            </form>

        <?php } ?>

        <p class="switch">
            <a href="customerDashboard.php">Back to my orders</a>
        </p>
    </div>
</body>

</html>
