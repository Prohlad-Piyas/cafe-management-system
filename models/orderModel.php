<?php
require_once __DIR__ . "/dbConnect.php";







function placeOrder($customerId, $cart, $paymentMethod)
{
    $conn = dbConnection();
    $lines = [];
    $total = 0;

    foreach ($cart as $itemId => $qty) {
        $qty = (int) $qty;
        if ($qty <= 0) {
            continue;
        }

        $stmt = mysqli_prepare($conn, "SELECT name, price FROM menu_items WHERE itemId = ? AND available = 1");
        mysqli_stmt_bind_param($stmt, "i", $itemId);
        mysqli_stmt_execute($stmt);
        $item = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if ($item == null) {
            continue;
        }

        $unitPrice = (float) $item["price"];

        $lines[] = ["itemId" => (int) $itemId, "name" => $item["name"], "price" => $unitPrice, "qty" => $qty];
        $total  += $unitPrice * $qty;
    }

    if (count($lines) == 0) {
        mysqli_close($conn);
        return null;
    }

    mysqli_begin_transaction($conn);

    $sql  = "INSERT INTO orders (customerId, status, payment_method, total_amount)
             VALUES (?, 'received', ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssd", $customerId, $paymentMethod, $total);
    $ok = mysqli_stmt_execute($stmt);
    $orderId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    if ($ok) {
        $itemSql  = "INSERT INTO order_items (orderId, itemId, name, unit_price, quantity)
                     VALUES (?, ?, ?, ?, ?)";
        $itemStmt = mysqli_prepare($conn, $itemSql);

        foreach ($lines as $line) {
            mysqli_stmt_bind_param($itemStmt, "iisdi", $orderId, $line["itemId"], $line["name"], $line["price"], $line["qty"]);
            $ok = $ok && mysqli_stmt_execute($itemStmt);
        }

        mysqli_stmt_close($itemStmt);
    }

    if ($ok) {
        mysqli_commit($conn);
    } else {
        mysqli_rollback($conn);
        $orderId = null;
    }

    mysqli_close($conn);

    return $orderId;
}





function getOrdersByCustomer($customerId)
{
    $conn = dbConnection();

    $sql  = "SELECT orderId, status, payment_method, total_amount, created_at, updated_at
             FROM orders WHERE customerId = ? ORDER BY orderId DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $customerId);
    mysqli_stmt_execute($stmt);
    $orders = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);

    foreach ($orders as &$order) {
        $order["items"] = getOrderItems($conn, $order["orderId"]);
    }

    mysqli_close($conn);

    return $orders;
}



function getOrderStatusesForCustomer($customerId)
{
    $conn = dbConnection();

    $sql  = "SELECT orderId, status FROM orders WHERE customerId = ? ORDER BY orderId DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $customerId);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $rows;
}



function getOrderItems($conn, $orderId)
{
    $stmt = mysqli_prepare($conn, "SELECT name, unit_price, quantity FROM order_items WHERE orderId = ?");
    mysqli_stmt_bind_param($stmt, "i", $orderId);
    mysqli_stmt_execute($stmt);
    $items = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);

    return $items;
}





function getIncomingOrders()
{
    $conn = dbConnection();

    $sql = "SELECT o.orderId, o.total_amount, o.payment_method, o.created_at, u.name AS customerName
            FROM orders o
            JOIN users u ON u.userId = o.customerId
            WHERE o.status = 'received'
            ORDER BY o.orderId ASC";
    $result = mysqli_query($conn, $sql);
    $orders = mysqli_fetch_all($result, MYSQLI_ASSOC);

    foreach ($orders as &$order) {
        $order["items"] = getOrderItems($conn, $order["orderId"]);
    }

    mysqli_close($conn);

    return $orders;
}



function getBaristaWorkload()
{
    $conn = dbConnection();

    $sql = "SELECT u.userId, u.name,
                   COUNT(o.orderId) AS activeOrders
            FROM users u
            LEFT JOIN orders o ON o.baristaId = u.userId AND o.status = 'preparing'
            WHERE u.role = 'barista'
            GROUP BY u.userId, u.name
            ORDER BY activeOrders ASC, u.name ASC";
    $result = mysqli_query($conn, $sql);
    $baristas = mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_close($conn);

    return $baristas;
}



function assignOrderToAvailableBarista($orderId)
{
    $baristas = getBaristaWorkload();

    if (count($baristas) == 0) {
        return null;
    }

    $chosen = $baristas[0]["userId"];

    $conn = dbConnection();
    $stmt = mysqli_prepare($conn, "UPDATE orders SET baristaId = ?, status = 'preparing'
                                    WHERE orderId = ? AND status = 'received'");
    mysqli_stmt_bind_param($stmt, "si", $chosen, $orderId);
    $ok = mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return ($ok && $rows > 0) ? $baristas[0]["name"] : null;
}



function getAllOrders()
{
    $conn = dbConnection();

    $sql = "SELECT o.orderId, o.status, o.payment_method, o.total_amount, o.created_at,
                   c.name AS customerName, b.name AS baristaName, w.name AS waiterName
            FROM orders o
            JOIN users c ON c.userId = o.customerId
            LEFT JOIN users b ON b.userId = o.baristaId
            LEFT JOIN users w ON w.userId = o.waiterId
            ORDER BY o.orderId DESC";
    $result = mysqli_query($conn, $sql);
    $orders = mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_close($conn);

    return $orders;
}





function getBaristaQueue($baristaId)
{
    $conn = dbConnection();

    $sql = "SELECT o.orderId, o.status, o.created_at, u.name AS customerName
            FROM orders o
            JOIN users u ON u.userId = o.customerId
            WHERE o.baristaId = ? AND o.status = 'preparing'
            ORDER BY o.orderId ASC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $baristaId);
    mysqli_stmt_execute($stmt);
    $orders = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);

    foreach ($orders as &$order) {
        $order["items"] = getOrderItems($conn, $order["orderId"]);
    }

    mysqli_close($conn);

    return $orders;
}



function markOrderPrepared($orderId, $baristaId)
{
    $conn = dbConnection();

    $stmt = mysqli_prepare($conn, "UPDATE orders SET status = 'prepared'
                                    WHERE orderId = ? AND baristaId = ? AND status = 'preparing'");
    mysqli_stmt_bind_param($stmt, "is", $orderId, $baristaId);
    $ok = mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_affected_rows($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok && $rows > 0;
}





function getOrdersReadyToServe()
{
    $conn = dbConnection();

    $sql = "SELECT o.orderId, o.updated_at, u.name AS customerName
            FROM orders o
            JOIN users u ON u.userId = o.customerId
            WHERE o.status = 'prepared'
            ORDER BY o.orderId ASC";
    $result = mysqli_query($conn, $sql);
    $orders = mysqli_fetch_all($result, MYSQLI_ASSOC);

    foreach ($orders as &$order) {
        $order["items"] = getOrderItems($conn, $order["orderId"]);
    }

    mysqli_close($conn);

    return $orders;
}



function markOrderServed($orderId, $waiterId)
{
    $conn = dbConnection();

    $stmt = mysqli_prepare($conn, "UPDATE orders SET status = 'served', waiterId = ?
                                    WHERE orderId = ? AND status = 'prepared'");
    mysqli_stmt_bind_param($stmt, "si", $waiterId, $orderId);
    $ok = mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_affected_rows($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok && $rows > 0;
}





function getSalesSummary()
{
    $conn = dbConnection();

    $sql = "SELECT
                COUNT(*)                                            AS totalOrders,
                COALESCE(SUM(total_amount), 0)                      AS totalRevenue,
                COALESCE(SUM(CASE WHEN status = 'served' THEN 1 ELSE 0 END), 0)   AS servedOrders,
                COALESCE(SUM(CASE WHEN status != 'served' THEN 1 ELSE 0 END), 0)  AS openOrders
            FROM orders";
    $result = mysqli_query($conn, $sql);
    $summary = mysqli_fetch_assoc($result);

    mysqli_close($conn);

    return $summary;
}



function getSalesByDay()
{
    $conn = dbConnection();

    $sql = "SELECT DATE(created_at) AS orderDate,
                   COUNT(*) AS orderCount,
                   COALESCE(SUM(total_amount), 0) AS revenue
            FROM orders
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
            GROUP BY DATE(created_at)
            ORDER BY orderDate ASC";
    $result = mysqli_query($conn, $sql);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_close($conn);

    return $rows;
}



function getBestSellingItems($limit = 5)
{
    $conn = dbConnection();

    $limit = (int) $limit;

    $sql = "SELECT name,
                   SUM(quantity) AS unitsSold,
                   SUM(quantity * unit_price) AS revenue
            FROM order_items
            GROUP BY name
            ORDER BY unitsSold DESC
            LIMIT $limit";
    $result = mysqli_query($conn, $sql);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_close($conn);

    return $rows;
}

?>
