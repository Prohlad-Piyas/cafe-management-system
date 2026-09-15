<?php
require_once __DIR__ . "/dbConnect.php";







function getAllMenuItems($onlyAvailable = false)
{
    $conn = dbConnection();

    if ($onlyAvailable) {
        $sql = "SELECT itemId, name, description, price, category, available
                FROM menu_items WHERE available = 1 ORDER BY category, name";
    } else {
        $sql = "SELECT itemId, name, description, price, category, available
                FROM menu_items ORDER BY category, name";
    }

    $result = mysqli_query($conn, $sql);
    $items  = mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_close($conn);

    return $items;
}



function getMenuItemById($itemId)
{
    $conn = dbConnection();

    $sql  = "SELECT itemId, name, description, price, category, available
             FROM menu_items WHERE itemId = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $itemId);
    mysqli_stmt_execute($stmt);

    $item = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $item;
}





function addMenuItem($name, $description, $price, $category)
{
    $conn = dbConnection();

    $sql  = "INSERT INTO menu_items (name, description, price, category, available)
             VALUES (?, ?, ?, ?, 1)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssds", $name, $description, $price, $category);

    $ok = mysqli_stmt_execute($stmt);
    $newId = $ok ? mysqli_insert_id($conn) : null;

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $newId;
}



function updateMenuItem($itemId, $name, $description, $price, $category, $available)
{
    $conn = dbConnection();

    $sql  = "UPDATE menu_items
             SET name = ?, description = ?, price = ?, category = ?, available = ?
             WHERE itemId = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssdsii", $name, $description, $price, $category, $available, $itemId);

    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
}



function deleteMenuItem($itemId)
{
    $conn = dbConnection();

    $stmt = mysqli_prepare($conn, "DELETE FROM menu_items WHERE itemId = ?");
    mysqli_stmt_bind_param($stmt, "i", $itemId);
    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
}

?>
